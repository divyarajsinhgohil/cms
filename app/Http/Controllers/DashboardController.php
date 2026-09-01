<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Student;
use App\Models\User;
use App\Models\AdmissionApplication;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Models\Attendance;
use App\Models\Notice;
use App\Models\Timetable;
use App\Models\StudyMaterial;
use App\Models\ExamMark;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->must_change_password) {
            return redirect()->route('password.change.show')->with('info', 'Please set and confirm your new password to access your dashboard.');
        }

        if ($user->isManagement()) {
            $totalStudents = Student::where('status', 'Active')->count();
            $totalStaff = User::where('role', 'staff')->count();
            $pendingApplications = AdmissionApplication::where('status', 'Pending')->count();
            $totalFeesCollected = FeePayment::sum('amount_paid');

            // Calculate total expected fee
            $totalExpectedFee = Student::join('courses', 'students.course_id', '=', 'courses.id')
                ->leftJoin('fee_structures', 'courses.id', '=', 'fee_structures.course_id')
                ->sum('fee_structures.total_amount');
            $pendingDues = max(0, $totalExpectedFee - $totalFeesCollected);

            $recentApplications = AdmissionApplication::latest()->take(5)->get();
            $recentNotices = Notice::latest()->take(5)->get();

            return view('dashboards.management', compact(
                'totalStudents', 'totalStaff', 'pendingApplications',
                'totalFeesCollected', 'pendingDues', 'recentApplications', 'recentNotices'
            ));
        }

        if ($user->isStaff()) {
            $staffProfile = $user->staffProfile;
            $recentNotices = Notice::whereIn('target_audience', ['all', 'staff'])->latest()->take(5)->get();
            $materialsCount = StudyMaterial::where('uploaded_by', $user->id)->count();
            $todayTimetable = Timetable::where('staff_id', $user->id)->with(['course', 'subject'])->get();
            $totalStudentsInDept = Student::where('department_id', optional($staffProfile)->department_id)->count();

            return view('dashboards.staff', compact(
                'staffProfile', 'recentNotices', 'materialsCount', 'todayTimetable', 'totalStudentsInDept'
            ));
        }

        if ($user->isStudent()) {
            $student = $user->studentProfile;

            $attendancePct = $student ? $student->calculateAttendancePercentage() : 100;
            $cgpa = $student ? $student->calculateCGPA() : 0.0;
            $paidFee = $student ? $student->calculateTotalPaidFee() : 0;
            $pendingDues = $student ? $student->calculatePendingDues() : 0;

            // Same all/students + course/semester targeting as the Notice Board itself
            $notices = Notice::whereIn('target_audience', ['all', 'students'])
                ->where(function ($q) use ($student) {
                    $q->whereNull('course_id')->orWhere('course_id', optional($student)->course_id);
                })
                ->where(function ($q) use ($student) {
                    $q->whereNull('semester')->orWhere('semester', optional($student)->current_semester);
                })
                ->latest()->take(5)->get();

            $timetable = $student ? Timetable::where('course_id', $student->course_id)->where('semester', $student->current_semester)->whereNull('specific_date')->with(['subject', 'staff'])->get() : collect();
            $todayOverride = $student ? Timetable::where('course_id', $student->course_id)->where('semester', $student->current_semester)->whereDate('specific_date', today())->with(['subject', 'staff'])->orderBy('start_time')->get() : collect();
            $examMarks = $student ? ExamMark::where('student_id', $student->id)->with('subject')->get() : collect();

            // Daily attendance trend for the last 14 days, for the dashboard graph
            $attendanceTrend = [];
            if ($student) {
                $trendStart = now()->subDays(13)->toDateString();
                $recordsByDate = Attendance::where('student_id', $student->id)
                    ->where('date', '>=', $trendStart)
                    ->get()
                    ->groupBy('date');

                for ($i = 13; $i >= 0; $i--) {
                    $date = now()->subDays($i)->toDateString();
                    $dayRecords = $recordsByDate->get($date, collect());
                    $total = $dayRecords->count();
                    $present = $dayRecords->whereIn('status', ['present', 'late'])->count();

                    $attendanceTrend[] = [
                        'label' => \Illuminate\Support\Carbon::parse($date)->format('M d'),
                        'pct' => $total > 0 ? round(($present / $total) * 100, 1) : null,
                    ];
                }
            }

            return view('dashboards.student', compact(
                'student', 'attendancePct', 'cgpa', 'paidFee', 'pendingDues', 'notices', 'timetable', 'todayOverride', 'examMarks', 'attendanceTrend'
            ));
        }

        return redirect()->route('login');
    }
}
