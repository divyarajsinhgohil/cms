<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;
use App\Models\Student;
use App\Models\User;
use App\Models\WhatsAppLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WhatsAppController extends Controller
{
    /**
     * Dedicated WhatsApp Hub Dashboard
     */
    public function index(WhatsAppService $whatsapp)
    {
        abort_unless(in_array(Auth::user()->role, ['management', 'staff']), 403);

        $settings = SiteSetting::current();
        $connectionStatus = $whatsapp->checkConnection();

        // Students & Staff with phone numbers
        $students = Student::with('user')->whereHas('user', function ($q) {
            $q->whereNotNull('phone')->where('phone', '!=', '');
        })->get();

        $staffUsers = User::whereIn('role', ['management', 'staff'])
            ->whereNotNull('phone')
            ->where('phone', '!=', '')
            ->get();

        $logs = WhatsAppLog::latest()->take(25)->get();

        $todayCount = WhatsAppLog::whereDate('created_at', now()->today())->count();
        $deliveredCount = WhatsAppLog::where('status', 'Delivered')->count();
        $failedCount = WhatsAppLog::where('status', 'Failed')->count();

        return view('whatsapp.dashboard', compact(
            'settings',
            'connectionStatus',
            'students',
            'staffUsers',
            'logs',
            'todayCount',
            'deliveredCount',
            'failedCount'
        ));
    }

    /**
     * Send direct instant WhatsApp message
     */
    public function sendDirect(Request $request, WhatsAppService $whatsapp)
    {
        abort_unless(in_array(Auth::user()->role, ['management', 'staff']), 403);

        $validated = $request->validate([
            'phone' => 'required|string',
            'recipient_name' => 'nullable|string|max:255',
            'message' => 'required|string',
        ]);

        $result = $whatsapp->sendDirectMessage(
            $validated['phone'],
            $validated['message'],
            $validated['recipient_name'] ?? 'Recipient',
            'direct'
        );

        if ($result['success']) {
            $notice = isset($result['simulated'])
                ? 'Message recorded (Simulated dispatch mode - Meta API token unset).'
                : 'WhatsApp message sent successfully!';

            return redirect()->back()->with('success', $notice);
        }

        return redirect()->back()->with('error', 'Failed to send WhatsApp message: '.$result['error']);
    }

    /**
     * Update Automated WhatsApp Trigger preferences
     */
    public function updateTriggers(Request $request)
    {
        abort_unless(Auth::user()->isManagement(), 403);

        $settings = SiteSetting::current();
        $settings->update([
            'whatsapp_auto_fee_receipt' => $request->has('whatsapp_auto_fee_receipt'),
            'whatsapp_auto_attendance_alert' => $request->has('whatsapp_auto_attendance_alert'),
            'whatsapp_auto_exam_result' => $request->has('whatsapp_auto_exam_result'),
            'whatsapp_auto_admission_approval' => $request->has('whatsapp_auto_admission_approval'),
        ]);

        return redirect()->back()->with('success', 'Automated WhatsApp trigger preferences updated successfully!');
    }

    /**
     * View detailed WhatsApp delivery logs
     */
    public function logs(Request $request)
    {
        abort_unless(in_array(Auth::user()->role, ['management', 'staff']), 403);

        $query = WhatsAppLog::query();

        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('recipient_phone', 'like', "%{$search}%")
                  ->orWhere('recipient_name', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        if ($type = $request->get('type')) {
            $query->where('type', $type);
        }

        $logs = $query->latest()->paginate(25);

        return view('whatsapp.logs', compact('logs'));
    }

    /**
     * Test connection to Meta Cloud API via AJAX
     */
    public function testConnection(WhatsAppService $whatsapp)
    {
        $status = $whatsapp->checkConnection();

        return response()->json($status);
    }
}
