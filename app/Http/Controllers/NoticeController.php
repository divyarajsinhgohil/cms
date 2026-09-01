<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notice;
use App\Models\Course;

class NoticeController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isManagement()) {
            $query = Notice::with(['author', 'course']);
        } elseif ($user->isStaff()) {
            $query = Notice::whereIn('target_audience', ['all', 'staff'])->with(['author', 'course']);
        } else {
            $student = $user->studentProfile;

            $query = Notice::whereIn('target_audience', ['all', 'students'])
                ->where(function ($q) use ($student) {
                    $q->whereNull('course_id')
                      ->orWhere('course_id', optional($student)->course_id);
                })
                ->where(function ($q) use ($student) {
                    $q->whereNull('semester')
                      ->orWhere('semester', optional($student)->current_semester);
                })
                ->with(['author', 'course']);
        }

        $notices = $query->latest()->paginate(9)->withQueryString();
        $courses = Course::all();

        return view('notices.index', compact('notices', 'courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'target_audience' => 'required|in:all,students,staff',
            'priority' => 'required|in:normal,urgent',
            'course_id' => 'nullable|exists:courses,id',
            'semester' => 'nullable|integer|min:1|max:8',
        ]);

        Notice::create([
            'title' => $request->title,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'priority' => $request->priority,
            'posted_by' => Auth::id(),
            // Stream/semester targeting only makes sense for a student audience
            'course_id' => $request->target_audience === 'students' ? $request->course_id : null,
            'semester' => $request->target_audience === 'students' ? $request->semester : null,
        ]);

        return redirect()->back()->with('success', 'Notice posted to the board successfully!');
    }
}
