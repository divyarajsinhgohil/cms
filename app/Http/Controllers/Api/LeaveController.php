<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isManagement()) {
            $leaveRequests = LeaveRequest::with('user')->latest()->get();
        } else {
            $leaveRequests = LeaveRequest::where('user_id', $user->id)->latest()->get();
        }

        return response()->json($leaveRequests);
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        $leave = LeaveRequest::create([
            'user_id' => $request->user()->id,
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return response()->json(['message' => 'Leave application submitted successfully!', 'data' => $leave], 201);
    }

    public function updateStatus(Request $request, LeaveRequest $leave)
    {
        $request->validate(['status' => 'required|string']);
        $leave->update(['status' => $request->status]);

        return response()->json(['message' => "Leave application #{$leave->id} set to {$request->status}!", 'data' => $leave]);
    }
}
