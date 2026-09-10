<?php

namespace App\Http\Controllers;

use App\Models\BroadcastMessage;
use App\Models\UserBroadcastRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BroadcastController extends Controller
{
    /**
     * Admin/Management dashboard list of broadcasts
     */
    public function index()
    {
        abort_unless(in_array(Auth::user()->role, ['management']), 403);

        $broadcasts = BroadcastMessage::with('sender')
            ->latest()
            ->paginate(15);

        return view('broadcasts.index', compact('broadcasts'));
    }

    /**
     * Store new real-time broadcast message (Admin/Management only)
     */
    public function store(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['management']), 403);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'target_role' => 'required|in:all,management,staff,student',
            'type' => 'required|in:info,success,warning,danger',
            'display_format' => 'required|in:toast,banner,modal',
            'expires_in_hours' => 'nullable|integer|min:1|max:168',
        ]);

        $expiresAt = isset($validated['expires_in_hours']) && $validated['expires_in_hours']
            ? now()->addHours((int) $validated['expires_in_hours'])
            : null;

        $broadcast = BroadcastMessage::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'message' => $validated['message'],
            'target_role' => $validated['target_role'],
            'type' => $validated['type'],
            'display_format' => $validated['display_format'],
            'is_active' => true,
            'expires_at' => $expiresAt,
        ]);

        if ($request->wantsJson()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Real-time message broadcasted successfully!',
                'broadcast' => $broadcast,
            ]);
        }

        return redirect()->back()->with('success', 'Real-time message broadcasted successfully to online users!');
    }

    /**
     * Check for new real-time messages (Called via AJAX polling by all active users)
     */
    public function checkRealtime()
    {
        if (! Auth::check()) {
            return response()->json(['messages' => []]);
        }

        $user = Auth::user();
        $userRole = $user->role;

        $messages = BroadcastMessage::with('sender')
            ->where('is_active', true)
            ->where(function ($q) use ($userRole) {
                $q->where('target_role', 'all')
                  ->orWhere('target_role', $userRole);
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            })
            ->whereDoesntHave('reads', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'title' => e($msg->title),
                    'message' => e($msg->message),
                    'type' => $msg->type,
                    'display_format' => $msg->display_format,
                    'action_url' => $msg->action_url,
                    'sender' => e($msg->sender ? $msg->sender->name : 'Administrator'),
                    'created_at' => $msg->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'messages' => $messages,
        ]);
    }

    /**
     * Mark a broadcast message as read/dismissed by current user
     */
    public function dismiss(Request $request, BroadcastMessage $broadcast)
    {
        if (! Auth::check()) {
            return response()->json(['status' => 'unauthenticated'], 401);
        }

        UserBroadcastRead::firstOrCreate([
            'user_id' => Auth::id(),
            'broadcast_message_id' => $broadcast->id,
        ], [
            'read_at' => now(),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification dismissed.',
        ]);
    }

    /**
     * Toggle active state or delete a broadcast message
     */
    public function toggle(BroadcastMessage $broadcast)
    {
        abort_unless(in_array(Auth::user()->role, ['management']), 403);

        $broadcast->update(['is_active' => ! $broadcast->is_active]);

        return redirect()->back()->with('success', 'Broadcast status updated.');
    }

    public function destroy(BroadcastMessage $broadcast)
    {
        abort_unless(in_array(Auth::user()->role, ['management']), 403);

        $broadcast->delete();

        return redirect()->back()->with('success', 'Broadcast deleted successfully.');
    }
}
