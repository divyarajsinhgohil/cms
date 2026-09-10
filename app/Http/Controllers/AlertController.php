<?php

namespace App\Http\Controllers;

use App\Models\CmsAlert;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AlertController extends Controller
{
    public function index()
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('cms_alerts')) {
            return view('alerts.index', ['alerts' => new \Illuminate\Pagination\LengthAwarePaginator([], 0, 20)]);
        }

        $alerts = CmsAlert::where('user_id', Auth::id())
            ->whereIn('status', ['sent', 'read'])
            ->latest('sent_at')
            ->paginate(20);

        return view('alerts.index', compact('alerts'));
    }

    public function markRead(CmsAlert $alert)
    {
        abort_unless($alert->user_id === Auth::id(), 403);
        $alert->markRead();

        return redirect()->back()->with('success', 'Notification marked as read.');
    }

    public function markAllRead()
    {
        CmsAlert::where('user_id', Auth::id())
            ->where('status', 'sent')
            ->update(['status' => 'read', 'read_at' => now()]);

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }
}
