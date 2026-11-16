<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExamMark;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ExamController extends Controller
{
    public function index(Request $request)
    {
        $query = ExamMark::with(['student.user', 'subject']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        return response()->json($query->get());
    }

    public function storeMarks(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'subject_id' => 'required|exists:subjects,id',
            'semester' => 'required|integer',
            'internal_marks' => 'required|numeric|min:0|max:40',
            'external_marks' => 'required|numeric|min:0|max:60',
        ]);

        $total = $request->internal_marks + $request->external_marks;

        [$grade, $gp] = ExamMark::gradeFor($total);

        $mark = ExamMark::updateOrCreate(
            [
                'student_id' => $request->student_id,
                'subject_id' => $request->subject_id,
                'semester' => $request->semester,
            ],
            [
                'internal_marks' => $request->internal_marks,
                'external_marks' => $request->external_marks,
                'total_marks' => $total,
                'grade' => $grade,
                'grade_point' => $gp,
            ]
        );

        return response()->json([
            'message' => "Marks saved & CGPA updated! Total: {$total}/100, Grade: {$grade}",
            'data' => $mark,
        ]);
    }

    public function marksheet(Student $student)
    {
        $student->load(['user', 'course.department']);
        $marks = ExamMark::where('student_id', $student->id)->with('subject')->get();
        $cgpa = $student->calculateCGPA();

        return response()->json(compact('student', 'marks', 'cgpa'));
    }

    public function downloadMarksheetPdf(Student $student)
    {
        $student->load(['user', 'course.department']);
        $marks = ExamMark::where('student_id', $student->id)->with('subject')->get();
        $cgpa = $student->calculateCGPA();

        $pdf = Pdf::loadView('exams.marksheet_pdf', compact('student', 'marks', 'cgpa'));

        return $pdf->download("Marksheet-{$student->enrollment_no}.pdf");
    }

    public function myMarks(Request $request)
    {
        $student = $request->user()->studentProfile;

        if (! $student) {
            return response()->json(['message' => 'No student profile linked to this account.'], 404);
        }

        return response()->json([
            'cgpa' => $student->calculateCGPA(),
            'marks' => ExamMark::where('student_id', $student->id)->with('subject')->get(),
        ]);
    }
}
