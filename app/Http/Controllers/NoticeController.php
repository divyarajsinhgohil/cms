<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Notice;
use App\Models\Course;
use App\Services\AlertDispatchService;

class NoticeController extends Controller
{
    public function __construct(private AlertDispatchService $alerts) {}
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

        $notice = Notice::create([
            'title' => $request->title,
            'content' => $request->content,
            'target_audience' => $request->target_audience,
            'priority' => $request->priority,
            'posted_by' => Auth::id(),
            'course_id' => $request->target_audience === 'students' ? $request->course_id : null,
            'semester' => $request->target_audience === 'students' ? $request->semester : null,
        ]);

        $queued = $this->alerts->notifyNoticePosted($notice);

        // Dispatch website-wide Real-Time Broadcast
        $targetRole = match ($notice->target_audience) {
            'students' => 'student',
            'staff' => 'staff',
            default => 'all',
        };
        \App\Models\BroadcastMessage::dispatchSystemMessage(
            "📢 Notice Board Update: {$notice->title}",
            "A new notice has been published by ".Auth::user()->name.". Click to view details.",
            $targetRole,
            $notice->priority === 'urgent' ? 'danger' : 'info',
            $notice->priority === 'urgent' ? 'modal' : 'toast',
            route('notices.index')
        );

        return redirect()->back()->with('success', "Notice posted & broadcasted live! {$queued} notification(s) queued for delivery.");
    }
}
