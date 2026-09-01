<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Support\UniqueCode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'department', 'course', 'section']);

        $status = $request->get('status', 'Active');
        if ($status !== 'All') {
            $query->where('status', $status);
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('semester')) {
            $query->where('current_semester', $request->semester);
        }

        if ($request->filled('batch_year')) {
            $query->where('batch_year', $request->batch_year);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('enrollment_no', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        return response()->json($query->latest()->get());
    }

    public function show(Student $student)
    {
        return response()->json($student->load(['user', 'department', 'course']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'course_id' => 'required|exists:courses,id',
            'batch_year' => 'required|integer',
            'current_semester' => 'required|integer|min:1|max:8',
            'gender' => 'required|string',
            'dob' => 'nullable|date',
            'guardian_name' => 'nullable|string',
            'guardian_phone' => 'nullable|string',
            'address' => 'nullable|string',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => $request->phone,
            'must_change_password' => true,
        ]);

        $dept = Department::findOrFail($request->department_id);

        $student = UniqueCode::create(fn () => Student::create([
            'user_id' => $user->id,
            'enrollment_no' => sprintf(
                'CMS-%d-%s-%05d',
                $request->batch_year,
                strtoupper($dept->code),
                UniqueCode::nextSequence(
                    Student::where('department_id', $dept->id)->orderByDesc('id')->value('enrollment_no')
                )
            ),
            'department_id' => $request->department_id,
            'course_id' => $request->course_id,
            'batch_year' => $request->batch_year,
            'current_semester' => $request->current_semester,
            'gender' => $request->gender,
            'dob' => $request->dob,
            'guardian_name' => $request->guardian_name,
            'guardian_phone' => $request->guardian_phone,
            'address' => $request->address,
            'status' => 'Active',
        ]));

        Student::regenerateRollNumbers($student->course_id, $student->current_semester);

        return response()->json([
            'message' => "Student admitted successfully! Generated Enrollment No: {$student->enrollment_no}",
            'data' => $student->load(['user', 'department', 'course']),
        ], 201);
    }
}
