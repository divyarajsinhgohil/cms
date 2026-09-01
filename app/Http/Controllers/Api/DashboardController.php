<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\ExamMark;
use App\Models\FeePayment;
use App\Models\Notice;
use App\Models\Student;
use App\Models\StudyMaterial;
use App\Models\Timetable;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isManagement()) {
            $totalStudents = Student::count();
            $totalStaff = User::where('role', 'staff')->count();
            $totalFeesCollected = FeePayment::sum('amount_paid');

            $totalExpectedFee = Student::join('courses', 'students.course_id', '=', 'courses.id')
                ->leftJoin('fee_structures', 'courses.id', '=', 'fee_structures.course_id')
                ->sum('fee_structures.total_amount');
            $pendingDues = max(0, $totalExpectedFee - $totalFeesCollected);

            return response()->json([
                'role' => 'management',
                'total_students' => $totalStudents,
                'total_staff' => $totalStaff,
                'total_fees_collected' => $totalFeesCollected,
                'pending_dues' => $pendingDues,
                'recent_notices' => Notice::latest()->take(5)->get(),
            ]);
        }

        if ($user->isStaff()) {
            $staffProfile = $user->staffProfile;

            return response()->json([
                'role' => 'staff',
                'staff_profile' => $staffProfile,
                'recent_notices' => Notice::whereIn('target_audience', ['all', 'staff'])->latest()->take(5)->get(),
                'materials_count' => StudyMaterial::where('uploaded_by', $user->id)->count(),
                'today_timetable' => Timetable::where('staff_id', $user->id)->with(['course', 'subject'])->get(),
                'total_students_in_dept' => Student::where('department_id', optional($staffProfile)->department_id)->count(),
            ]);
        }

        if ($user->isStudent()) {
            $student = $user->studentProfile;

            if (! $student) {
                return response()->json(['message' => 'No student profile linked to this account.'], 404);
            }

            return response()->json([
                'role' => 'student',
                'student' => $student->load(['user', 'course', 'department']),
                'attendance_percentage' => $student->calculateAttendancePercentage(),
                'cgpa' => $student->calculateCGPA(),
                'paid_fee' => $student->calculateTotalPaidFee(),
                'pending_dues' => $student->calculatePendingDues(),
                'notices' => Notice::whereIn('target_audience', ['all', 'students'])->latest()->take(5)->get(),
                'timetable' => Timetable::where('course_id', $student->course_id)->with(['subject', 'staff'])->get(),
                'exam_marks' => ExamMark::where('student_id', $student->id)->with('subject')->get(),
            ]);
        }

        return response()->json(['message' => 'Unrecognized role.'], 400);
    }
}
