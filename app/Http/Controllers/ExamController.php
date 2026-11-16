<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Course;
use Illuminate\Support\Facades\Auth;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AlertDispatchService;

class ExamController extends Controller
{
    public function __construct(private AlertDispatchService $alerts) {}
    public function index(Request $request)
    {
        $user = Auth::user();
        $courses = Course::all();
        $streamScoped = false;
        $ownOnly = false;

        if ($user->isStudent()) {
            $ownOnly = true;
        } elseif ($user->isStaff() && $user->staffProfile) {
            $assignedCourses = $user->staffProfile->courses;
            if ($assignedCourses->isNotEmpty()) {
                $streamScoped = true;
                $courses = $assignedCourses;
            }
        }

        $selectedCourseId = $request->get('course_id', optional($courses->first())->id);
        if ($streamScoped && ! $courses->contains('id', (int) $selectedCourseId)) {
            $selectedCourseId = optional($courses->first())->id;
        }
        $semester = $request->get('semester', 1);

        $subjects = $selectedCourseId ? Subject::where('course_id', $selectedCourseId)->where('semester', $semester)->get() : collect();
        $students = $selectedCourseId ? Student::where('course_id', $selectedCourseId)->with('user')->get() : collect();

        $ledgerSemester = $request->get('ledger_semester');
        $ledgerCourseId = $request->get('ledger_course_id');

        if ($ownOnly) {
            // A student only ever sees their own marks, across every subject and semester
            $studentId = optional($user->studentProfile)->id;
            $marksQuery = ExamMark::with(['student.user', 'subject'])
                ->where('student_id', $studentId ?? 0);

            if ($ledgerSemester) {
                $marksQuery->where('semester', $ledgerSemester);
            }
        } else {
            // Exam Marks Ledger — independently filterable by stream/semester ("All" allowed), scoped for restricted faculty
            $marksQuery = ExamMark::with(['student.user', 'subject'])
                ->whereHas('student', function ($q) use ($ledgerCourseId, $streamScoped, $courses) {
                    if ($streamScoped) {
                        $q->whereIn('course_id', $courses->pluck('id'));
                    }
                    if ($ledgerCourseId) {
                        $q->where('course_id', $ledgerCourseId);
                    }
                });

            if ($ledgerSemester) {
                $marksQuery->where('semester', $ledgerSemester);
            }
        }

        $examMarks = $marksQuery->latest()->paginate(20)->withQueryString();

        return view('exams.index', compact(
            'courses', 'subjects', 'students', 'selectedCourseId', 'semester', 'examMarks',
            'streamScoped', 'ownOnly', 'ledgerCourseId', 'ledgerSemester'
        ));
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

        // Grade & grade point on the 10-point scale (see ExamMark::GRADE_SCALE)
        [$grade, $gp] = ExamMark::gradeFor($total);

        ExamMark::updateOrCreate(
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

        $student = Student::with('user')->find($request->student_id);
        $subject = Subject::find($request->subject_id);
        if ($student) {
            $this->alerts->notifyExamResult($student, optional($subject)->name ?? 'Subject', (int) $total, $grade);

            // Automated WhatsApp Exam Result Alert
            $settings = \App\Models\SiteSetting::current();
            if ($settings->whatsapp_auto_exam_result && optional($student->user)->phone) {
                $subjectName = optional($subject)->name ?? 'Subject';
                app(\App\Services\WhatsAppService::class)->sendDirectMessage(
                    $student->user->phone,
                    "Exam Result Update: {$student->user->name} scored {$total}/100 (Grade: {$grade}) in {$subjectName} for Semester {$request->semester}.",
                    $student->user->name,
                    'exam_result'
                );
            }
        }

        // Real-Time Broadcast to Students
        $subjectName = optional($subject)->name ?? 'Subject';
        \App\Models\BroadcastMessage::dispatchSystemMessage(
            "🎓 Exam Result Published: {$subjectName}",
            "Exam marks for {$subjectName} (Semester {$request->semester}) have been published.",
            'student',
            'success',
            'toast',
            route('exams.index')
        );

        return redirect()->back()->with('success', "Marks saved & broadcasted live! Total: {$total}/100, Grade: {$grade}");
    }

    public function viewMarksheet(Student $student)
    {
        $this->authorizeStudentSelf($student);
        $student->load(['user', 'course.department']);
        $marks = ExamMark::where('student_id', $student->id)->with('subject')->get();
        $cgpa = $student->calculateCGPA();

        return view('exams.marksheet', compact('student', 'marks', 'cgpa'));
    }

    public function downloadMarksheetPdf(Student $student)
    {
        $this->authorizeStudentSelf($student);
        $student->load(['user', 'course.department']);
        $marks = ExamMark::where('student_id', $student->id)->with('subject')->get();
        $cgpa = $student->calculateCGPA();

        $pdf = Pdf::loadView('exams.marksheet_pdf', compact('student', 'marks', 'cgpa'));

        return $pdf->download("Marksheet-{$student->enrollment_no}.pdf");
    }

    /**
     * A student may only ever pull up their own marksheet, even if they guess another
     * student's URL directly.
     */
    private function authorizeStudentSelf(Student $student): void
    {
        $user = Auth::user();

        if ($user->isStudent() && optional($user->studentProfile)->id !== $student->id) {
            abort(403, 'You can only access your own marksheet.');
        }
    }
}
