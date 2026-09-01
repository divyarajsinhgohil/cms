<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Student;
use App\Models\User;
use App\Models\Department;
use App\Models\Course;
use App\Models\Section;
use App\Support\UniqueCode;

class AdmissionController extends Controller
{
    public function index(Request $request)
    {
        $query = Student::with(['user', 'department', 'course', 'section']);

        $user = Auth::user();
        $streamScoped = false;
        $courses = Course::all();
        $sections = collect();

        $status = $request->get('status', 'Active');
        if ($status !== 'All') {
            $query->where('status', $status);
        }

        // Faculty assigned to specific streams only see students within their assigned stream(s)
        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourseIds = $user->staffProfile->courses->pluck('id')->all();
            if (count($assignedCourseIds) > 0) {
                $streamScoped = true;
                $courses = Course::whereIn('id', $assignedCourseIds)->get();
                $query->whereIn('course_id', $assignedCourseIds);

                // Narrow further to one of their assigned streams, ignore attempts to pick outside it
                if ($request->filled('course_id') && in_array((int) $request->course_id, $assignedCourseIds, true)) {
                    $query->where('course_id', $request->course_id);
                }
            }
        }

        // Filter by Stream / Course (management, or unrestricted staff)
        if (! $streamScoped && $request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by Semester
        if ($request->filled('semester')) {
            $query->where('current_semester', $request->semester);
        }

        // Filter by Batch Year
        if ($request->filled('batch_year')) {
            $query->where('batch_year', $request->batch_year);
        }

        // Search by Name or Enrollment Number
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('enrollment_no', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        if ($request->filled('course_id') && $request->filled('semester')) {
            $sections = Section::where('course_id', $request->course_id)
                ->where('semester', $request->semester)
                ->orderBy('name')
                ->get();
        }

        $students = $query->orderByRaw('roll_no IS NULL')
            ->orderBy('roll_no')
            ->latest()
            ->paginate(20)
            ->withQueryString();
        $departments = Department::all();
        $allSections = Section::orderBy('course_id')->orderBy('semester')->orderBy('name')->get()
            ->map(fn ($s) => ['id' => $s->id, 'name' => $s->name, 'course_id' => $s->course_id, 'semester' => $s->semester, 'capacity' => $s->capacity])
            ->values();

        return view('admissions.index', compact('students', 'departments', 'courses', 'streamScoped', 'status', 'sections', 'allSections'));
    }

    /**
     * A single-page report combining a student's academic, attendance,
     * fee, and exam record — the "click a student, see everything" view
     * linked to from the Student Directory and Class Section rosters.
     */
    public function show(Student $student)
    {
        $user = Auth::user();

        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourseIds = $user->staffProfile->courses->pluck('id')->all();
            if (count($assignedCourseIds) > 0 && ! in_array($student->course_id, $assignedCourseIds, true)) {
                abort(403, 'This student is outside your assigned stream(s).');
            }
        }

        $student->load(['user', 'department', 'course', 'section']);
        $examMarks = $student->examMarks()->with('subject')->orderBy('semester')->get();

        return view('admissions.show', compact('student', 'examMarks'));
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
            'photo' => 'nullable|image|max:2048',
            'section_id' => 'nullable|exists:sections,id',
        ]);

        // 1. Create User Account
        $avatarPath = null;
        if ($request->hasFile('photo')) {
            $avatarPath = Storage::url($request->file('photo')->store('avatars/students', 'public'));
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'student',
            'phone' => $request->phone,
            'must_change_password' => true,
            'avatar' => $avatarPath,
        ]);

        // 2. Enrollment Number (CMS-{YEAR}-{DEPT}-{SEQ}) is generated per
        //    department in step 4, inside a retry so a concurrent admission
        //    can't hand out the same number.
        $dept = Department::findOrFail($request->department_id);

        // 3. Resolve optional class section — only honor it if it actually
        // belongs to the chosen stream + semester (defends against a stale
        // section list if the form was left open while data changed).
        $sectionId = null;
        if ($request->filled('section_id')) {
            $section = Section::find($request->section_id);
            if ($section && $section->course_id == $request->course_id && $section->semester == $request->current_semester) {
                $sectionId = $section->id;
            }
        }

        // 4. Create Student Profile
        $student = UniqueCode::create(fn () => Student::create([
            'user_id' => $user->id,
            'enrollment_no' => sprintf(
                "CMS-%d-%s-%05d",
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
            'section_id' => $sectionId,
            'status' => 'Active',
        ]));

        // 5. Fold the new student into the alphabetical roll number sequence
        // for their stream + semester.
        Student::regenerateRollNumbers($student->course_id, $student->current_semester);

        return redirect()->back()->with('success', "Student admitted successfully! Generated Enrollment No: {$enrollmentNo}");
    }

    /**
     * Mark a student as having left college. History (attendance, fees,
     * exam marks) is preserved — only status changes, and their old class's
     * roll numbers are recomputed alphabetically for those who remain.
     */
    public function markLeft(Student $student)
    {
        $courseId = $student->course_id;
        $semester = $student->current_semester;
        $name = optional($student->user)->name ?? 'Student';

        $student->update([
            'status' => 'Left',
            'section_id' => null,
            'roll_no' => null,
        ]);

        Student::regenerateRollNumbers($courseId, $semester);

        return redirect()->back()->with('success', "{$name} has been marked as Left. Roll numbers for the remaining students in that class have been regenerated.");
    }

    /**
     * Bulk-promote every Active student in a stream + semester to the next
     * semester. Section assignment doesn't carry over since sections are
     * defined per stream+semester — the class needs to be re-divided.
     */
    public function promoteSemester(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'semester' => 'required|integer|min:1|max:8',
        ]);

        $course = Course::findOrFail($validated['course_id']);
        $fromSemester = (int) $validated['semester'];
        $maxSemester = $course->duration_years * 2;

        if ($fromSemester >= $maxSemester) {
            return redirect()->back()->with('error', "Semester {$fromSemester} is already the final semester for {$course->name} — there is no next semester to promote to.");
        }

        $students = Student::where('course_id', $course->id)
            ->where('current_semester', $fromSemester)
            ->where('status', 'Active')
            ->get();

        if ($students->isEmpty()) {
            return redirect()->back()->with('error', "No active students found in {$course->name} — Semester {$fromSemester}.");
        }

        foreach ($students as $student) {
            $student->update([
                'current_semester' => $fromSemester + 1,
                'section_id' => null,
                'roll_no' => null,
            ]);
        }

        Student::regenerateRollNumbers($course->id, $fromSemester + 1);

        return redirect()->back()->with('success', "{$students->count()} students promoted from Semester {$fromSemester} to Semester ".($fromSemester + 1)." in {$course->name}. Please re-divide them into class sections.");
    }
}
