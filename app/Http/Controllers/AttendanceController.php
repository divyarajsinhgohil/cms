<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Attendance;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Course;
use App\Models\Section;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $courses = Course::all();
        $streamScoped = false;

        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourses = $user->staffProfile->courses;
            if ($assignedCourses->isNotEmpty()) {
                $streamScoped = true;
                $courses = $assignedCourses;
            }
        }

        $selectedCourseId = $request->get('course_id', optional($courses->first())->id);

        // A staff member cannot select a course outside their assigned streams
        if ($streamScoped && ! $courses->contains('id', (int) $selectedCourseId)) {
            $selectedCourseId = optional($courses->first())->id;
        }

        $selectedSubjectId = $request->get('subject_id');
        $date = $request->get('date', date('Y-m-d'));

        $subjects = $selectedCourseId ? Subject::where('course_id', $selectedCourseId)->get() : collect();
        if (!$selectedSubjectId && $subjects->isNotEmpty()) {
            $selectedSubjectId = $subjects->first()->id;
        }

        // A subject belongs to a specific semester, so the class list is scoped
        // to that semester (not every student in the whole stream) — and can be
        // narrowed further to one class section.
        $selectedSubject = $selectedSubjectId ? $subjects->firstWhere('id', (int) $selectedSubjectId) : null;
        $selectedSemester = optional($selectedSubject)->semester;
        $selectedSectionId = $request->get('section_id');

        $sections = ($selectedCourseId && $selectedSemester)
            ? Section::where('course_id', $selectedCourseId)->where('semester', $selectedSemester)->orderBy('name')->get()
            : collect();

        $studentsQuery = ($selectedCourseId && $selectedSemester)
            ? Student::where('course_id', $selectedCourseId)->where('current_semester', $selectedSemester)->where('status', 'Active')
            : Student::query()->whereRaw('0 = 1');

        if ($selectedSectionId === 'unassigned') {
            $studentsQuery->whereNull('section_id');
        } elseif ($selectedSectionId) {
            $studentsQuery->where('section_id', $selectedSectionId);
        }

        $students = $studentsQuery->with('user')->orderByRaw('roll_no IS NULL')->orderBy('roll_no')->get();

        // Existing attendance for the selected date & subject
        $existingAttendance = Attendance::where('subject_id', $selectedSubjectId)
            ->where('date', $date)
            ->pluck('status', 'student_id')
            ->toArray();

        // Low attendance report (< 75%), scoped to the faculty's assigned stream(s) if applicable.
        // Computed as one aggregate query instead of iterating every student in PHP, so this stays
        // fast even with thousands of students.
        $lowAttendanceQuery = DB::table('attendances')
            ->join('students', 'students.id', '=', 'attendances.student_id')
            ->groupBy('attendances.student_id')
            ->selectRaw("attendances.student_id, COUNT(*) as total, SUM(CASE WHEN attendances.status IN ('present','late') THEN 1 ELSE 0 END) as present_count")
            ->havingRaw("(SUM(CASE WHEN attendances.status IN ('present','late') THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) < 75");

        if ($streamScoped) {
            $lowAttendanceQuery->whereIn('students.course_id', $courses->pluck('id'));
        }

        $lowAttendanceRows = $lowAttendanceQuery
            ->orderByRaw("(SUM(CASE WHEN attendances.status IN ('present','late') THEN 1 ELSE 0 END) * 100.0 / COUNT(*)) ASC")
            ->get();

        $lowAttendanceTotal = $lowAttendanceRows->count();
        $lowAttendanceStudentIds = $lowAttendanceRows->take(10)->pluck('student_id')->all();

        $lowAttendanceList = Student::whereIn('id', $lowAttendanceStudentIds)
            ->with(['user', 'course'])
            ->get()
            ->sortBy(fn ($st) => array_search($st->id, $lowAttendanceStudentIds))
            ->values();

        return view('attendance.index', compact(
            'courses', 'subjects', 'students', 'selectedCourseId', 'streamScoped',
            'selectedSubjectId', 'date', 'existingAttendance', 'lowAttendanceList', 'lowAttendanceTotal',
            'sections', 'selectedSectionId', 'selectedSemester'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendance' => 'required|array',
        ]);

        $user = Auth::user();

        // Guard: a stream-scoped faculty member can only mark attendance for their assigned stream(s)
        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourseIds = $user->staffProfile->courses->pluck('id');
            if ($assignedCourseIds->isNotEmpty()) {
                $subjectCourseId = Subject::where('id', $request->subject_id)->value('course_id');
                if (! $assignedCourseIds->contains($subjectCourseId)) {
                    return redirect()->back()->with('error', 'You are not assigned to this stream and cannot mark attendance for it.');
                }
            }
        }

        $markedBy = $user->id;

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

        return redirect()->back()->with('success', 'Daily attendance marked and percentages recalculated successfully!');
    }
}
