<?php

namespace App\Http\Controllers;

use App\Models\AdmissionApplication;
use App\Models\Course;
use App\Models\CourseSyllabus;
use App\Models\Department;
use App\Models\StaffProfile;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AcademicController extends Controller
{
    // ==================== DEPARTMENTS ====================
    public function departmentsIndex(Request $request)
    {
        $query = Department::withCount(['courses', 'staff', 'students']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('head_of_department', 'LIKE', "%{$search}%");
            });
        }

        $departments = $query->latest()->get();

        $stats = [
            'totalDepartments' => Department::count(),
            'totalCourses' => Course::count(),
            'totalStudents' => Student::count(),
            'totalStaff' => StaffProfile::count(),
        ];

        return view('academics.departments', compact('departments', 'stats'));
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code',
            'head_of_department' => 'nullable|string|max:255',
        ]);

        Department::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'head_of_department' => $request->head_of_department,
        ]);

        return redirect()->back()->with('success', "Department '{$request->name}' added successfully!");
    }

    public function updateDepartment(Request $request, Department $department)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code,'.$department->id,
            'head_of_department' => 'nullable|string|max:255',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'head_of_department' => $request->head_of_department,
        ]);

        return redirect()->back()->with('success', "Department '{$department->name}' updated successfully!");
    }

    public function destroyDepartment(Request $request, Department $department)
    {
        $request->validate([
            'confirm_name' => 'required|string',
        ]);

        if ($request->confirm_name !== $department->name) {
            return redirect()->back()->with('error', 'Department name did not match — nothing was deleted.');
        }

        $name = $department->name;
        $this->cascadeDeleteDepartment($department);

        return redirect()->back()->with('success', "Department '{$name}' and all its courses, faculty, and students were permanently deleted.");
    }

    public function destroyDepartmentsBulk(Request $request)
    {
        $request->validate([
            'department_ids' => 'required|array|min:1',
            'department_ids.*' => 'exists:departments,id',
            'confirm_word' => 'required|string',
        ]);

        if (strtoupper($request->confirm_word) !== 'DELETE') {
            return redirect()->back()->with('error', 'Confirmation word did not match — nothing was deleted.');
        }

        $departments = Department::whereIn('id', $request->department_ids)->get();
        $count = $departments->count();

        foreach ($departments as $department) {
            $this->cascadeDeleteDepartment($department);
        }

        return redirect()->back()->with('success', "{$count} department(s) and all their courses, faculty, and students were permanently deleted.");
    }

    private function cascadeDeleteDepartment(Department $department): void
    {
        DB::transaction(function () use ($department) {
            $courseIds = Course::where('department_id', $department->id)->pluck('id');
            AdmissionApplication::whereIn('course_id', $courseIds)->delete();

            $orphanedUserIds = Student::where('department_id', $department->id)->pluck('user_id')
                ->merge(StaffProfile::where('department_id', $department->id)->pluck('user_id'));

            $department->delete();

            User::whereIn('id', $orphanedUserIds)->delete();
        });
    }

    // ==================== COURSES / STREAMS ====================
    public function coursesIndex(Request $request)
    {
        $query = Course::with(['department', 'subjects']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $courses = $query->latest()->get();
        $departments = Department::all();

        $stats = [
            'totalCourses' => Course::count(),
            'totalDepartments' => Department::count(),
            'totalSubjects' => Subject::count(),
            'totalStudents' => Student::count(),
        ];

        return view('academics.courses', compact('courses', 'departments', 'stats'));
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code',
            'duration_years' => 'required|integer|min:1|max:5',
        ]);

        Course::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'duration_years' => $request->duration_years,
        ]);

        return redirect()->back()->with('success', "Course program '{$request->name}' added successfully!");
    }

    public function updateCourse(Request $request, Course $course)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code,'.$course->id,
            'duration_years' => 'required|integer|min:1|max:5',
        ]);

        $course->update([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'duration_years' => $request->duration_years,
        ]);

        return redirect()->back()->with('success', "Course program '{$course->name}' updated successfully!");
    }

    public function destroyCourse(Course $course)
    {
        $studentsCount = $course->students()->count();

        if ($studentsCount > 0) {
            return redirect()->back()->with('error',
                "Cannot delete '{$course->name}': {$studentsCount} student(s) are enrolled in this course. Transfer or remove them first."
            );
        }

        $name = $course->name;
        $course->delete();

        return redirect()->back()->with('success', "Course program '{$name}' deleted successfully!");
    }

    // ==================== SUBJECTS & TEACHER MAPPING ====================
    public function subjectsIndex(Request $request)
    {
        $query = Subject::with(['course.department', 'teacher']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $subjects = $query->latest()->get();
        $courses = Course::all();
        $facultyUsers = User::whereIn('role', ['management', 'staff'])->orderBy('name')->get();

        return view('academics.subjects', compact('subjects', 'courses', 'facultyUsers'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code',
            'credits' => 'required|integer|min:1|max:10',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        Subject::create([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'credits' => $request->credits,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->back()->with('success', "Subject '{$request->name}' added & faculty assigned successfully!");
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,'.$subject->id,
            'credits' => 'required|integer|min:1|max:10',
            'teacher_id' => 'nullable|exists:users,id',
        ]);

        $subject->update([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'credits' => $request->credits,
            'teacher_id' => $request->teacher_id,
        ]);

        return redirect()->back()->with('success', "Subject '{$subject->name}' updated successfully!");
    }

    public function destroySubject(Subject $subject)
    {
        $recordsCount = $subject->attendances()->count() + $subject->examMarks()->count();

        if ($recordsCount > 0) {
            return redirect()->back()->with('error',
                "Cannot delete '{$subject->name}': it already has {$recordsCount} attendance/exam record(s) tied to it."
            );
        }

        $name = $subject->name;
        $subject->delete();

        return redirect()->back()->with('success', "Subject '{$name}' deleted successfully!");
    }

    // ==================== SEMESTER SYLLABUS MANAGEMENT ====================
    public function syllabiIndex(Request $request)
    {
        $query = CourseSyllabus::with(['course', 'uploader']);

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        $syllabi = $query->latest()->get();
        $courses = Course::all();

        return view('academics.syllabi', compact('syllabi', 'courses'));
    }

    public function storeSyllabus(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf|max:15360', // max 15MB PDF
        ]);

        $course = Course::findOrFail($request->course_id);
        $pdfPath = $request->file('file')->store('syllabi', 'public');

        $syllabus = CourseSyllabus::updateOrCreate(
            [
                'course_id' => $request->course_id,
                'semester' => $request->semester,
            ],
            [
                'title' => $request->title ?: "{$course->code} Semester {$request->semester} Official Syllabus",
                'description' => $request->description,
                'syllabus_pdf_path' => $pdfPath,
                'uploaded_by' => Auth::id(),
            ]
        );

        // Dispatch website-wide Real-Time Broadcast
        \App\Models\BroadcastMessage::dispatchSystemMessage(
            "📄 Official Syllabus Uploaded: {$course->code} Semester {$request->semester}",
            "The official semester syllabus PDF for {$course->name} (Semester {$request->semester}) is now available for download.",
            'student',
            'warning',
            'toast',
            route('academics.my_syllabus')
        );

        return redirect()->back()->with('success', "Official Syllabus PDF for {$course->code} Semester {$request->semester} uploaded & broadcasted live!");
    }

    // ==================== STUDENT PORTAL: MY SEMESTER SYLLABUS & TEACHERS ====================
    public function mySyllabus(Request $request)
    {
        $user = Auth::user();
        $courses = Course::all();

        if ($user->isStudent()) {
            $student = $user->studentProfile;
            if (! $student) {
                return view('academics.my_syllabus', ['error' => 'No active student profile found on your account.']);
            }

            $selectedCourseId = $student->course_id;
            $selectedSemester = $student->current_semester;
            $selectedCourse = Course::with('department')->find($selectedCourseId);
        } else {
            // Management or Faculty selecting course & sem
            $selectedCourseId = $request->get('course_id', optional($courses->first())->id);
            $selectedSemester = (int) $request->get('semester', 1);
            $selectedCourse = Course::with('department')->find($selectedCourseId);
        }

        $syllabus = CourseSyllabus::where('course_id', $selectedCourseId)
            ->where('semester', $selectedSemester)
            ->first();

        $subjects = Subject::where('course_id', $selectedCourseId)
            ->where('semester', $selectedSemester)
            ->with(['teacher.staffProfile.department', 'studyMaterials'])
            ->get();

        return view('academics.my_syllabus', compact(
            'courses',
            'selectedCourse',
            'selectedCourseId',
            'selectedSemester',
            'syllabus',
            'subjects'
        ));
    }
}
