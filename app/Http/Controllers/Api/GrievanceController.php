<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;

class GrievanceController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isManagement()) {
            $complaints = Complaint::with('user')->latest()->get();
        } else {
            $complaints = Complaint::where('user_id', $user->id)->latest()->get();
        }

        return response()->json($complaints);
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        $complaint = Complaint::create([
            'user_id' => $request->user()->id,
            'category' => $request->category,
            'title' => $request->title,
            'description' => $request->description,
            'status' => 'Open',
        ]);

        return response()->json(['message' => 'Grievance ticket submitted successfully!', 'data' => $complaint], 201);
    }

    public function updateStatus(Request $request, Complaint $complaint)
    {
        $request->validate(['status' => 'required|string']);
        $complaint->update(['status' => $request->status]);

        return response()->json(['message' => "Ticket #{$complaint->id} status updated to {$request->status}!", 'data' => $complaint]);
    }
}
