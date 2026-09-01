<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LeaveRequest;

class LeaveController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isManagement()) {
            $leaveRequests = LeaveRequest::with('user')->latest()->get();
        } else {
            $leaveRequests = LeaveRequest::where('user_id', $user->id)->latest()->get();
        }

        return view('facilities.leaves', compact('leaveRequests'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string',
        ]);

        LeaveRequest::create([
            'user_id' => Auth::id(),
            'leave_type' => $request->leave_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'reason' => $request->reason,
            'status' => 'Pending',
        ]);

        return redirect()->back()->with('success', 'Leave application submitted successfully!');
    }

    public function updateStatus(Request $request, LeaveRequest $leave)
    {
        $request->validate(['status' => 'required|string']);
        $leave->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Leave application #{$leave->id} set to {$request->status}!");
    }
}
