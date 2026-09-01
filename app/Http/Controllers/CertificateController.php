<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Course;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $courses = Course::all();
        $streamScoped = false;
        $ownOnly = false;

        $query = Student::with(['user', 'department', 'course']);

        // Students can only view/print their own certificates — not browse anyone else's
        if ($user->isStudent()) {
            $ownOnly = true;
            if ($user->studentProfile) {
                $query->where('id', $user->studentProfile->id);
            } else {
                $query->whereRaw('1 = 0');
            }
        } elseif ($user->isStaff() && $user->staffProfile) {
            // Faculty assigned to specific streams only see/print certificates for their own stream(s)
            $assignedCourseIds = $user->staffProfile->courses->pluck('id')->all();
            if (count($assignedCourseIds) > 0) {
                $streamScoped = true;
                $courses = Course::whereIn('id', $assignedCourseIds)->get();
                $query->whereIn('course_id', $assignedCourseIds);

                if ($request->filled('course_id') && in_array((int) $request->course_id, $assignedCourseIds, true)) {
                    $query->where('course_id', $request->course_id);
                }
            }
        }

        if (! $ownOnly) {
            if (! $streamScoped && $request->filled('course_id')) {
                $query->where('course_id', $request->course_id);
            }

            if ($request->filled('semester')) {
                $query->where('current_semester', $request->semester);
            }

            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    $q->where('enrollment_no', 'LIKE', "%{$search}%")
                      ->orWhereHas('user', fn ($u) => $u->where('name', 'LIKE', "%{$search}%"));
                });
            }
        }

        $students = $query->latest()->paginate(20)->withQueryString();

        return view('certificates.index', compact('students', 'courses', 'streamScoped', 'ownOnly'));
    }

    public function idCard(Student $student)
    {
        $this->authorizeStudentSelf($student);
        $student->load(['user', 'department', 'course']);
        return view('certificates.id_card', compact('student'));
    }

    public function idCardPdf(Student $student)
    {
        $this->authorizeStudentSelf($student);
        $student->load(['user', 'department', 'course']);
        $pdf = Pdf::loadView('certificates.id_card_pdf', compact('student'));

        return $pdf->download("ID-Card-{$student->enrollment_no}.pdf");
    }

    public function bonafide(Student $student)
    {
        $this->authorizeStudentSelf($student);
        $student->load(['user', 'department', 'course']);
        return view('certificates.bonafide', compact('student'));
    }

    public function bonafidePdf(Student $student)
    {
        $this->authorizeStudentSelf($student);
        $student->load(['user', 'department', 'course']);
        $pdf = Pdf::loadView('certificates.bonafide_pdf', compact('student'));

        return $pdf->download("Bonafide-Certificate-{$student->enrollment_no}.pdf");
    }

    /**
     * A student may only ever pull up their own certificate, even if they guess another
     * student's URL directly — the sidebar link alone is not a real access control.
     */
    private function authorizeStudentSelf(Student $student): void
    {
        $user = Auth::user();

        if ($user->isStudent() && optional($user->studentProfile)->id !== $student->id) {
            abort(403, 'You can only access your own certificates.');
        }
    }
}
