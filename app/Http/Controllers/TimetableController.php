<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Timetable;
use App\Models\Course;
use App\Models\Subject;
use App\Models\User;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $ownOnly = $user->isStudent();

        $courses = Course::all();

        if ($ownOnly) {
            // A student only ever sees their own stream's current-semester timetable
            $student = $user->studentProfile;
            $selectedCourseId = optional($student)->course_id;
            $semester = optional($student)->current_semester ?? 1;
        } else {
            $selectedCourseId = $request->get('course_id', optional($courses->first())->id);
            $semester = $request->get('semester', 1);
        }

        $subjects = $selectedCourseId ? Subject::where('course_id', $selectedCourseId)->where('semester', $semester)->get() : collect();
        $staffMembers = User::where('role', 'staff')->get();

        // The regular, recurring weekly schedule
        $timetables = Timetable::where('course_id', $selectedCourseId)
            ->where('semester', $semester)
            ->whereNull('specific_date')
            ->with(['subject', 'staff'])
            ->orderBy('start_time')
            ->get();

        // A one-off override for today, if the admin created one — shown as a highlight
        $todayOverride = Timetable::where('course_id', $selectedCourseId)
            ->where('semester', $semester)
            ->whereDate('specific_date', today())
            ->with(['subject', 'staff'])
            ->orderBy('start_time')
            ->get();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];

        return view('timetables.index', compact(
            'courses', 'subjects', 'staffMembers', 'selectedCourseId', 'semester',
            'timetables', 'todayOverride', 'days', 'ownOnly'
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
            'label' => 'required_if:type,Break|nullable|string|max:100',
            'schedule_mode' => 'required|in:recurring,specific',
            'day_of_week' => 'required_if:schedule_mode,recurring|nullable|string',
            'specific_date' => 'required_if:schedule_mode,specific|nullable|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $isSpecificDate = $validated['schedule_mode'] === 'specific';
        $dayOfWeek = $isSpecificDate
            ? \Illuminate\Support\Carbon::parse($validated['specific_date'])->format('l')
            : $validated['day_of_week'];

        // Faculty clash check — scoped to the same context this slot will actually run in:
        // a specific-date override only clashes with other overrides on that exact date,
        // a recurring slot only clashes with other recurring slots on that weekday.
        if ($validated['type'] !== 'Break' && ! empty($validated['staff_id'])) {
            $clashQuery = Timetable::where('staff_id', $validated['staff_id'])
                ->where('day_of_week', $dayOfWeek)
                ->where(function ($q) use ($validated) {
                    $q->whereBetween('start_time', [$validated['start_time'], $validated['end_time']])
                      ->orWhereBetween('end_time', [$validated['start_time'], $validated['end_time']]);
                });

            $clashQuery = $isSpecificDate
                ? $clashQuery->whereDate('specific_date', $validated['specific_date'])
                : $clashQuery->whereNull('specific_date');

            $facultyClash = $clashQuery->first();

            if ($facultyClash) {
                return redirect()->back()->with('error', 'FACULTY CLASH DETECTED! This faculty member already has a class at this time.');
            }
        }

        Timetable::create([
            'course_id' => $validated['course_id'],
            'semester' => $validated['semester'],
            'type' => $validated['type'],
            'subject_id' => $validated['type'] === 'Break' ? null : $validated['subject_id'],
            'staff_id' => $validated['type'] === 'Break' ? null : $validated['staff_id'],
            'label' => $validated['type'] === 'Break' ? $validated['label'] : null,
            'day_of_week' => $dayOfWeek,
            'specific_date' => $isSpecificDate ? $validated['specific_date'] : null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
        ]);

        $message = $isSpecificDate
            ? "One-time schedule override created for {$validated['specific_date']}. Students will see it highlighted on that day."
            : 'Recurring schedule slot created with 0 clashes detected!';

        return redirect()->back()->with('success', $message);
    }

    public function autoGenerate(Request $request)
    {
        $courseId = $request->input('course_id');
        $semester = $request->input('semester', 1);

        $subjects = Subject::where('course_id', $courseId)->get();
        $staffMembers = User::where('role', 'staff')->get();

        if ($subjects->isEmpty() || $staffMembers->isEmpty()) {
            return redirect()->back()->with('error', 'Cannot auto-generate timetable: No subjects or staff available.');
        }

        // Clear existing recurring slots for this course/semester (one-off date overrides are left untouched)
        Timetable::where('course_id', $courseId)->where('semester', $semester)->whereNull('specific_date')->delete();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['09:30:00', '10:30:00'],
            ['10:30:00', '11:30:00'],
            ['11:30:00', '12:30:00'],
            ['13:30:00', '14:30:00'],
        ];

        $subjectIndex = 0;
        foreach ($days as $day) {
            foreach ($slots as $slot) {
                $subject = $subjects[$subjectIndex % $subjects->count()];
                $staff = $staffMembers[$subjectIndex % $staffMembers->count()];

                Timetable::create([
                    'course_id' => $courseId,
                    'semester' => $semester,
                    'type' => 'Lecture',
                    'subject_id' => $subject->id,
                    'staff_id' => $staff->id,
                    'day_of_week' => $day,
                    'start_time' => $slot[0],
                    'end_time' => $slot[1],
                ]);

                $subjectIndex++;
            }
        }

        return redirect()->back()->with('success', 'Automated clash-free weekly timetable generated successfully!');
    }
}
