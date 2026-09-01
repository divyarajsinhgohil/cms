<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\StaffProfile;
use App\Models\Department;
use App\Models\Course;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffProfile::with(['user', 'department', 'courses']);

        // Filter by Department
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        // Filter by Work Field / Stream (Commerce, Computer Science, etc.)
        if ($request->filled('work_field')) {
            $query->where('work_field', $request->work_field);
        }

        // Search by Name, Email, Employee ID
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_id', 'LIKE', "%{$search}%")
                  ->orWhere('designation', 'LIKE', "%{$search}%")
                  ->orWhere('qualification', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        $staffMembers = $query->latest()->paginate(15)->withQueryString();
        $departments = Department::all();
        $courses = Course::with('department')->orderBy('name')->get();

        return view('academics.staff', compact('staffMembers', 'departments', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string',
            'work_field' => 'nullable|string',
            'qualification' => 'nullable|string',
            'address' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'joining_date' => 'nullable|date',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        // 1. Create Staff User Account. First-time default password is the
        // teacher's own name (lowercase, no spaces) so it's easy to tell them
        // over the phone/in person — they're forced to set their own password
        // on first login via must_change_password.
        $avatarPath = $request->hasFile('photo')
            ? Storage::url($request->file('photo')->store('avatars/staff', 'public'))
            : 'https://ui-avatars.com/api/?name=' . urlencode($request->name);

        $defaultPassword = strtolower(str_replace(' ', '', $request->name));

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($defaultPassword),
            'role' => 'staff',
            'phone' => $request->phone,
            'avatar' => $avatarPath,
            'must_change_password' => true,
        ]);

        // 2. Generate Employee ID: EMP-{DEPT}-{SEQ}
        $dept = Department::findOrFail($request->department_id);
        $count = StaffProfile::where('department_id', $dept->id)->count() + 1;
        $employeeId = sprintf("EMP-%s-%03d", strtoupper($dept->code), $count);

        // 3. Create Staff Profile
        $staff = StaffProfile::create([
            'user_id' => $user->id,
            'department_id' => $request->department_id,
            'employee_id' => $employeeId,
            'designation' => $request->designation,
            'work_field' => $request->work_field,
            'qualification' => $request->qualification,
            'address' => $request->address,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date ?? date('Y-m-d'),
        ]);

        // 4. Assign Streams (Courses) this faculty member is responsible for
        $staff->courses()->sync($request->input('course_ids', []));

        return redirect()->back()->with('success', "Faculty member '{$request->name}' added successfully! Employee ID: {$employeeId}. First-time login password: \"{$defaultPassword}\" (they'll be asked to set their own on first login).");
    }

    public function update(Request $request, StaffProfile $staff)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
            'department_id' => 'required|exists:departments,id',
            'designation' => 'required|string',
            'work_field' => 'nullable|string',
            'qualification' => 'nullable|string',
            'address' => 'nullable|string',
            'salary' => 'nullable|numeric|min:0',
            'photo' => 'nullable|image|max:2048',
            'joining_date' => 'nullable|date',
            'course_ids' => 'nullable|array',
            'course_ids.*' => 'exists:courses,id',
        ]);

        // Update User info
        $staff->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->hasFile('photo')) {
            $avatarPath = Storage::url($request->file('photo')->store('avatars/staff', 'public'));
            $staff->user->update(['avatar' => $avatarPath]);
        }

        // Update Staff Profile info
        $staff->update([
            'department_id' => $request->department_id,
            'designation' => $request->designation,
            'work_field' => $request->work_field,
            'qualification' => $request->qualification,
            'address' => $request->address,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
        ]);

        $staff->courses()->sync($request->input('course_ids', []));

        return redirect()->back()->with('success', "Faculty member '{$request->name}' updated successfully!");
    }

    public function destroy(StaffProfile $staff)
    {
        $name = optional($staff->user)->name;
        if ($staff->user) {
            $staff->user->delete(); // Cascades staff profile
        } else {
            $staff->delete();
        }

        return redirect()->back()->with('success', "Faculty profile for '{$name}' deleted successfully!");
    }
}
