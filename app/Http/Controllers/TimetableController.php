<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;
use App\Services\TimetableClashService;
use Illuminate\Support\Carbon;

class TimetableController extends Controller
{
    public function __construct(private TimetableClashService $clashService) {}

    public function index(Request $request)
    {
        $user = Auth::user();
        $ownOnly = $user->isStudent();
        $viewMode = $request->get('view', 'grid');

        $courses = Course::all();

        if ($ownOnly) {
            $student = $user->studentProfile;
            $selectedCourseId = optional($student)->course_id;
            $semester = optional($student)->current_semester ?? 1;
        } else {
            $selectedCourseId = $request->get('course_id', optional($courses->first())->id);
            $semester = $request->get('semester', 1);
        }

        $subjects = $selectedCourseId ? Subject::where('course_id', $selectedCourseId)->where('semester', $semester)->get() : collect();
        $staffMembers = User::where('role', 'staff')->get();

        $timetables = Timetable::where('course_id', $selectedCourseId)
            ->where('semester', $semester)
            ->whereNull('specific_date')
            ->with(['subject', 'staff'])
            ->orderBy('start_time')
            ->get();

        $todayOverride = Timetable::where('course_id', $selectedCourseId)
            ->where('semester', $semester)
            ->whereDate('specific_date', today())
            ->with(['subject', 'staff'])
            ->orderBy('start_time')
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        $calendarMonth = Carbon::parse($request->get('month', now()->format('Y-m')))->startOfMonth();
        $calendarEvents = ($selectedCourseId && $viewMode === 'calendar')
            ? $this->clashService->buildCalendarEvents($selectedCourseId, (int) $semester, $calendarMonth)
            : [];

        return view('timetables.index', compact(
            'courses', 'subjects', 'staffMembers', 'selectedCourseId', 'semester',
            'timetables', 'todayOverride', 'days', 'ownOnly', 'viewMode',
            'calendarMonth', 'calendarEvents'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer',
            'type' => 'required|in:Lecture,Lab,Break',
            'subject_id' => 'required_unless:type,Break|nullable|exists:subjects,id',
            'staff_id' => 'required_unless:type,Break|nullable|exists:users,id',
            'classroom' => 'nullable|string|max:100',
            'label' => 'required_if:type,Break|nullable|string|max:100',
            'schedule_mode' => 'required|in:recurring,specific',
            'day_of_week' => 'required_if:schedule_mode,recurring|nullable|string',
            'specific_date' => 'required_if:schedule_mode,specific|nullable|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $isSpecificDate = $validated['schedule_mode'] === 'specific';
        $dayOfWeek = $isSpecificDate
            ? Carbon::parse($validated['specific_date'])->format('l')
            : $validated['day_of_week'];

        if ($validated['type'] !== 'Break') {
            $specificDate = $isSpecificDate ? $validated['specific_date'] : null;
            $conflicts = $this->clashService->detectConflicts(
                $validated['staff_id'] ?? null,
                $validated['classroom'] ?? null,
                $dayOfWeek,
                $validated['start_time'],
                $validated['end_time'],
                $specificDate
            );

            $message = $this->clashService->formatClashMessage($conflicts['staff'], $conflicts['room']);
            if ($message) {
                return redirect()->back()->with('error', $message);
            }
        }

        Timetable::create([
            'course_id' => $validated['course_id'],
            'semester' => $validated['semester'],
            'type' => $validated['type'],
            'subject_id' => $validated['type'] === 'Break' ? null : $validated['subject_id'],
            'staff_id' => $validated['type'] === 'Break' ? null : $validated['staff_id'],
            'classroom' => $validated['type'] === 'Break' ? null : ($validated['classroom'] ?? null),
            'label' => $validated['type'] === 'Break' ? $validated['label'] : null,
            'day_of_week' => $dayOfWeek,
            'specific_date' => $isSpecificDate ? $validated['specific_date'] : null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        $message = $isSpecificDate
            ? "One-time schedule override created for {$validated['specific_date']}."
            : 'Recurring schedule slot created with no conflicts detected.';

        return redirect()->back()->with('success', $message);
    }

    public function autoGenerate(Request $request)
    {
        $courseId = $request->input('course_id');
        $semester = (int) $request->input('semester', 1);

        $subjects = Subject::where('course_id', $courseId)->where('semester', $semester)->get();
        $staffMembers = User::where('role', 'staff')->get();

        if ($subjects->isEmpty() || $staffMembers->isEmpty()) {
            return redirect()->back()->with('error', 'Cannot auto-generate timetable: No subjects or staff available.');
        }

        Timetable::where('course_id', $courseId)->where('semester', $semester)->whereNull('specific_date')->delete();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['09:30:00', '10:30:00', 'Room 101'],
            ['10:30:00', '11:30:00', 'Room 102'],
            ['11:30:00', '12:30:00', 'Lab 201'],
            ['13:30:00', '14:30:00', 'Room 103'],
        ];

        $subjectIndex = 0;
        $skipped = 0;

        foreach ($days as $day) {
            foreach ($slots as $slot) {
                $subject = $subjects[$subjectIndex % $subjects->count()];
                $staff = $staffMembers[$subjectIndex % $staffMembers->count()];

                $conflicts = $this->clashService->detectConflicts(
                    $staff->id,
                    $slot[2],
                    $day,
                    $slot[0],
                    $slot[1]
                );

                if ($conflicts['staff'] || $conflicts['room']) {
                    $skipped++;
                    $subjectIndex++;

                    continue;
                }

                Timetable::create([
                    'course_id' => $courseId,
                    'semester' => $semester,
                    'type' => 'Lecture',
                    'subject_id' => $subject->id,
                    'staff_id' => $staff->id,
                    'classroom' => $slot[2],
                    'day_of_week' => $day,
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                ]);

                $subjectIndex++;
            }
        }

        $msg = 'Automated timetable generated with clash detection.';
        if ($skipped > 0) {
            $msg .= " {$skipped} slot(s) skipped due to conflicts.";
        }

        return redirect()->back()->with('success', $msg);
    }
}
