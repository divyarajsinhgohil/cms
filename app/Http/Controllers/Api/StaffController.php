<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\StaffProfile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class StaffController extends Controller
{
    public function index(Request $request)
    {
        $query = StaffProfile::with(['user', 'department']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('work_field')) {
            $query->where('work_field', $request->work_field);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('employee_id', 'LIKE', "%{$search}%")
                  ->orWhere('designation', 'LIKE', "%{$search}%")
                  ->orWhere('qualification', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function ($u) use ($search) {
                      $u->where('name', 'LIKE', "%{$search}%")
                        ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        return response()->json($query->latest()->get());
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
            'avatar' => 'nullable|string',
            'joining_date' => 'nullable|date',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make('password'),
            'role' => 'staff',
            'phone' => $request->phone,
            'avatar' => $request->avatar ?: 'https://ui-avatars.com/api/?name='.urlencode($request->name),
        ]);

        $dept = Department::findOrFail($request->department_id);
        $count = StaffProfile::where('department_id', $dept->id)->count() + 1;
        $employeeId = sprintf('EMP-%s-%03d', strtoupper($dept->code), $count);

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

        return response()->json([
            'message' => "Faculty member '{$request->name}' added successfully! Employee ID: {$employeeId}",
            'data' => $staff->load(['user', 'department']),
        ], 201);
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
            'joining_date' => 'nullable|date',
        ]);

        $staff->user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->filled('avatar')) {
            $staff->user->update(['avatar' => $request->avatar]);
        }

        $staff->update([
            'department_id' => $request->department_id,
            'designation' => $request->designation,
            'work_field' => $request->work_field,
            'qualification' => $request->qualification,
            'address' => $request->address,
            'salary' => $request->salary,
            'joining_date' => $request->joining_date,
        ]);

        return response()->json(['message' => "Faculty member '{$request->name}' updated successfully!", 'data' => $staff->load(['user', 'department'])]);
    }

    public function destroy(StaffProfile $staff)
    {
        $name = optional($staff->user)->name;
        if ($staff->user) {
            $staff->user->delete();
        } else {
            $staff->delete();
        }

        return response()->json(['message' => "Faculty profile for '{$name}' deleted successfully!"]);
    }
}
