<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with(['student.user', 'subject']);

        if ($request->filled('course_id')) {
            $query->whereHas('student', fn ($q) => $q->where('course_id', $request->course_id));
        }

        if ($request->filled('subject_id')) {
            $query->where('subject_id', $request->subject_id);
        }

        if ($request->filled('date')) {
            $query->where('date', $request->date);
        }

        return response()->json($query->latest()->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $markedBy = $request->user()->id;

        foreach ($request->attendance as $studentId => $status) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $studentId,
                    'subject_id' => $request->subject_id,
                    'date' => $request->date,
                ],
                [
                    'status' => $status,
                    'marked_by' => $markedBy,
                ]
            );
        }

        return response()->json(['message' => 'Daily attendance marked and percentages recalculated successfully!']);
    }

    public function lowAttendance()
    {
        $students = Student::with(['user', 'course'])->get()
            ->filter(fn ($st) => $st->calculateAttendancePercentage() < 75)
            ->values();

        return response()->json($students);
    }

    public function myAttendance(Request $request)
    {
        $student = $request->user()->studentProfile;

        if (! $student) {
            return response()->json(['message' => 'No student profile linked to this account.'], 404);
        }

        return response()->json([
            'attendance_percentage' => $student->calculateAttendancePercentage(),
            'records' => Attendance::where('student_id', $student->id)->with('subject')->latest('date')->get(),
        ]);
    }
}
