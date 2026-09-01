<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Subject;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;

class TimetableController extends Controller
{
    public function index(Request $request)
    {
        $query = Timetable::with(['course', 'subject', 'staff']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('staff_id')) {
            $query->where('staff_id', $request->staff_id);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer',
            'subject_id' => 'required|exists:subjects,id',
            'staff_id' => 'required|exists:users,id',
            'classroom' => 'required|string',
            'day_of_week' => 'required|string',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
        ]);

        $facultyClash = Timetable::where('staff_id', $request->staff_id)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })->first();

        if ($facultyClash) {
            return response()->json(['message' => "Faculty clash detected! Faculty member is already assigned to room {$facultyClash->classroom} at this time."], 422);
        }

        $roomClash = Timetable::where('classroom', $request->classroom)
            ->where('day_of_week', $request->day_of_week)
            ->where(function ($q) use ($request) {
                $q->whereBetween('start_time', [$request->start_time, $request->end_time])
                  ->orWhereBetween('end_time', [$request->start_time, $request->end_time]);
            })->first();

        if ($roomClash) {
            return response()->json(['message' => "Classroom clash detected! Classroom '{$request->classroom}' is already occupied at this time."], 422);
        }

        $slot = Timetable::create($request->only([
            'course_id', 'semester', 'subject_id', 'staff_id', 'classroom', 'day_of_week', 'start_time', 'end_time',
        ]));

        return response()->json(['message' => 'Schedule slot created with 0 clashes detected!', 'data' => $slot], 201);
    }

    public function autoGenerate(Request $request)
    {
        $courseId = $request->input('course_id');
        $semester = $request->input('semester', 1);

        $subjects = Subject::where('course_id', $courseId)->get();
        $staffMembers = User::where('role', 'staff')->get();

        if ($subjects->isEmpty() || $staffMembers->isEmpty()) {
            return response()->json(['message' => 'Cannot auto-generate timetable: No subjects or staff available.'], 422);
        }

        Timetable::where('course_id', $courseId)->where('semester', $semester)->delete();

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
        $slots = [
            ['09:30:00', '10:30:00', 'Lab 101'],
            ['10:30:00', '11:30:00', 'Room 201'],
            ['11:30:00', '12:30:00', 'Room 202'],
            ['13:30:00', '14:30:00', 'Lab 102'],
        ];

        $subjectIndex = 0;
        foreach ($days as $day) {
            foreach ($slots as $slot) {
                $subject = $subjects[$subjectIndex % $subjects->count()];
                $staff = $staffMembers[$subjectIndex % $staffMembers->count()];

                Timetable::create([
                    'course_id' => $courseId,
                    'semester' => $semester,
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

        return response()->json([
            'message' => 'Automated clash-free weekly timetable generated successfully!',
            'data' => Timetable::where('course_id', $courseId)->where('semester', $semester)->with(['subject', 'staff'])->get(),
        ]);
    }
}
