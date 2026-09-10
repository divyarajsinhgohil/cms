<?php

namespace App\Services;

use App\Models\Timetable;
use Illuminate\Support\Carbon;

class TimetableClashService
{
    /**
     * @return array{staff: ?Timetable, room: ?Timetable}
     */
    public function detectConflicts(
        ?int $staffId,
        ?string $classroom,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $specificDate = null,
        ?int $excludeId = null
    ): array {
        return [
            'staff' => $staffId ? $this->findStaffClash($staffId, $dayOfWeek, $startTime, $endTime, $specificDate, $excludeId) : null,
            'room' => $classroom ? $this->findRoomClash($classroom, $dayOfWeek, $startTime, $endTime, $specificDate, $excludeId) : null,
        ];
    }

    public function findStaffClash(
        int $staffId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $specificDate = null,
        ?int $excludeId = null
    ): ?Timetable {
        return $this->baseClashQuery($dayOfWeek, $startTime, $endTime, $specificDate, $excludeId)
            ->where('staff_id', $staffId)
            ->where('type', '!=', 'Break')
            ->with(['subject', 'staff', 'course'])
            ->first();
    }

    public function findRoomClash(
        string $classroom,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $specificDate = null,
        ?int $excludeId = null
    ): ?Timetable {
        return $this->baseClashQuery($dayOfWeek, $startTime, $endTime, $specificDate, $excludeId)
            ->where('classroom', $classroom)
            ->where('type', '!=', 'Break')
            ->with(['subject', 'staff', 'course'])
            ->first();
    }

    public function staffIsFree(
        int $staffId,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $specificDate = null
    ): bool {
        return $this->findStaffClash($staffId, $dayOfWeek, $startTime, $endTime, $specificDate) === null;
    }

    public function roomIsFree(
        string $classroom,
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $specificDate = null
    ): bool {
        return $this->findRoomClash($classroom, $dayOfWeek, $startTime, $endTime, $specificDate) === null;
    }

    public function formatClashMessage(?Timetable $staffClash, ?Timetable $roomClash): ?string
    {
        if ($staffClash) {
            $subject = optional($staffClash->subject)->name ?? 'another class';
            $faculty = optional($staffClash->staff)->name ?? 'Faculty';

            return "Faculty clash: {$faculty} is already assigned to {$subject} ({$staffClash->start_time}–{$staffClash->end_time} on {$staffClash->day_of_week}).";
        }

        if ($roomClash) {
            $subject = optional($roomClash->subject)->name ?? 'another class';

            return "Room clash: {$roomClash->classroom} is already booked for {$subject} ({$roomClash->start_time}–{$roomClash->end_time} on {$roomClash->day_of_week}).";
        }

        return null;
    }

    /** Build calendar events for a month view. */
    public function buildCalendarEvents(int $courseId, int $semester, Carbon $month): array
    {
        $start = $month->copy()->startOfMonth()->startOfWeek(Carbon::MONDAY);
        $end = $month->copy()->endOfMonth()->endOfWeek(Carbon::SUNDAY);

        $recurring = Timetable::where('course_id', $courseId)
            ->where('semester', $semester)
            ->whereNull('specific_date')
            ->with(['subject', 'staff'])
            ->get();

        $overridesByDate = Timetable::where('course_id', $courseId)
            ->where('semester', $semester)
            ->whereNotNull('specific_date')
            ->whereBetween('specific_date', [$start->toDateString(), $end->toDateString()])
            ->with(['subject', 'staff'])
            ->get()
            ->groupBy(fn ($t) => $t->specific_date->toDateString());

        $events = [];
        for ($day = $start->copy(); $day->lte($end); $day->addDay()) {
            $dateKey = $day->toDateString();
            $slots = $overridesByDate->has($dateKey)
                ? $overridesByDate->get($dateKey)->sortBy('start_time')
                : $recurring->where('day_of_week', $day->format('l'))->sortBy('start_time');

            if ($slots->isNotEmpty()) {
                $events[$dateKey] = $slots->values();
            }
        }

        return $events;
    }

    private function baseClashQuery(
        string $dayOfWeek,
        string $startTime,
        string $endTime,
        ?string $specificDate,
        ?int $excludeId
    ) {
        $query = Timetable::query()
            ->where('day_of_week', $dayOfWeek)
            ->where('start_time', '<', $endTime)
            ->where('end_time', '>', $startTime);

        if ($specificDate) {
            $query->whereDate('specific_date', $specificDate);
        } else {
            $query->whereNull('specific_date');
        }

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query;
    }
}
