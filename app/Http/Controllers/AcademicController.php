<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Department;
use App\Models\Course;
use App\Models\Subject;
use App\Models\Student;
use App\Models\StaffProfile;

class AcademicController extends Controller
{
    // ==================== DEPARTMENTS ====================
    public function departmentsIndex(Request $request)
    {
        $query = Department::withCount(['courses', 'staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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
            'code' => 'required|string|max:20|unique:departments,code,' . $department->id,
            'head_of_department' => 'nullable|string|max:255',
        ]);

        $department->update([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'head_of_department' => $request->head_of_department,
        ]);

        return redirect()->back()->with('success', "Department '{$department->name}' updated successfully!");
    }

    public function destroyDepartment(Department $department)
    {
        $coursesCount = $department->courses()->count();
        $studentsCount = $department->students()->count();
        $staffCount = $department->staff()->count();

        if ($coursesCount > 0 || $studentsCount > 0 || $staffCount > 0) {
            return redirect()->back()->with('error',
                "Cannot delete '{$department->name}': it still has {$coursesCount} course(s), {$studentsCount} student(s), and {$staffCount} faculty member(s) linked to it. Reassign or remove them first."
            );
        }

        $name = $department->name;
        $department->delete();
        return redirect()->back()->with('success', "Department '{$name}' deleted successfully!");
    }

    // ==================== COURSES ====================
    public function coursesIndex(Request $request)
    {
        $query = Course::with(['department', 'subjects']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
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
            'code' => 'required|string|max:20|unique:courses,code,' . $course->id,
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

    // ==================== SUBJECTS ====================
    public function subjectsIndex(Request $request)
    {
        $query = Subject::with('course.department');

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        $subjects = $query->latest()->get();
        $courses = Course::all();

        return view('academics.subjects', compact('subjects', 'courses'));
    }

    public function storeSubject(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code',
            'credits' => 'required|integer|min:1|max:10',
        ]);

        Subject::create([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'credits' => $request->credits,
        ]);

        return redirect()->back()->with('success', "Subject '{$request->name}' added successfully!");
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,' . $subject->id,
            'credits' => 'required|integer|min:1|max:10',
        ]);

        $subject->update([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'credits' => $request->credits,
        ]);

        return redirect()->back()->with('success', "Subject '{$subject->name}' updated successfully!");
    }

    public function destroySubject(Subject $subject)
    {
        $recordsCount = $subject->attendances()->count() + $subject->examMarks()->count();

        if ($recordsCount > 0) {
            return redirect()->back()->with('error',
                "Cannot delete '{$subject->name}': it already has {$recordsCount} attendance/exam record(s) tied to it. Removing it would erase that history."
            );
        }

        $name = $subject->name;
        $subject->delete();
        return redirect()->back()->with('success', "Subject '{$name}' deleted successfully!");
    }
}
