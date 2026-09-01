<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Notice;
use Illuminate\Http\Request;

class NoticeController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isManagement()) {
            $notices = Notice::with('author')->latest()->get();
        } elseif ($user->isStaff()) {
            $notices = Notice::whereIn('target_audience', ['all', 'staff'])->with('author')->latest()->get();
        } else {
            $notices = Notice::whereIn('target_audience', ['all', 'students'])->with('author')->latest()->get();
        }

        return response()->json($notices);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all,students,staff',
            'priority' => 'required|in:normal,urgent',
        ]);

        $notice = Notice::create([
            'title' => $request->title,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'priority' => $request->priority,
            'posted_by' => $request->user()->id,
        ]);

        return response()->json(['message' => 'Notice posted to the board successfully!', 'data' => $notice], 201);
    }
}
