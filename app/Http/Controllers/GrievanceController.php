<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Complaint;

class GrievanceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        if ($user->isManagement()) {
            $complaints = Complaint::with('user')->latest()->get();
        } else {
            $complaints = Complaint::where('user_id', $user->id)->latest()->get();
        }

        return view('facilities.complaints', compact('complaints'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Complaint::create([
            'user_id' => Auth::id(),
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'Open',
        ]);

        return redirect()->back()->with('success', 'Grievance ticket submitted successfully!');
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate(['status' => 'required|in:Open,In Progress,Resolved']);
        $complaint->update(['status' => $request->status]);

        return redirect()->back()->with('success', "Ticket #{$complaint->id} status updated to {$request->status}!");
    }
}
