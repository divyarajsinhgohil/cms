<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Subject;
use Illuminate\Http\Request;

class AcademicController extends Controller
{
    // ==================== DEPARTMENTS ====================
    public function departmentsIndex(Request $request)
    {
        $query = Department::withCount(['courses', 'staff']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%")
                  ->orWhere('head_of_department', 'LIKE', "%{$search}%");
            });
        }

        return response()->json($query->latest()->get());
    }

    public function storeDepartment(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:departments,code',
            'head_of_department' => 'nullable|string|max:255',
        ]);

        $department = Department::create([
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'head_of_department' => $request->head_of_department,
        ]);

        return response()->json(['message' => "Department '{$department->name}' added successfully!", 'data' => $department], 201);
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

        return response()->json(['message' => "Department '{$department->name}' updated successfully!", 'data' => $department]);
    }

    public function destroyDepartment(Department $department)
    {
        $name = $department->name;
        $department->delete();

        return response()->json(['message' => "Department '{$name}' deleted successfully!"]);
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        return response()->json($query->latest()->get());
    }

    public function storeCourse(Request $request)
    {
        $request->validate([
            'department_id' => 'required|exists:departments,id',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:courses,code',
            'duration_years' => 'required|integer|min:1|max:5',
        ]);

        $course = Course::create([
            'department_id' => $request->department_id,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'duration_years' => $request->duration_years,
        ]);

        return response()->json(['message' => "Course program '{$course->name}' added successfully!", 'data' => $course], 201);
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

        return response()->json(['message' => "Course program '{$course->name}' updated successfully!", 'data' => $course]);
    }

    public function destroyCourse(Course $course)
    {
        $name = $course->name;
        $course->delete();

        return response()->json(['message' => "Course program '{$name}' deleted successfully!"]);
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
            $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('code', 'LIKE', "%{$search}%");
            });
        }

        return response()->json($query->latest()->get());
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

        $subject = Subject::create([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'credits' => $request->credits,
        ]);

        return response()->json(['message' => "Subject '{$subject->name}' added successfully!", 'data' => $subject], 201);
    }

    public function updateSubject(Request $request, Subject $subject)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:10',
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:20|unique:subjects,code,'.$subject->id,
            'credits' => 'required|integer|min:1|max:10',
        ]);

        $subject->update([
            'course_id' => $request->course_id,
            'semester' => $request->semester,
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'credits' => $request->credits,
        ]);

        return response()->json(['message' => "Subject '{$subject->name}' updated successfully!", 'data' => $subject]);
    }

    public function destroySubject(Subject $subject)
    {
        $name = $subject->name;
        $subject->delete();

        return response()->json(['message' => "Subject '{$name}' deleted successfully!"]);
    }
}
