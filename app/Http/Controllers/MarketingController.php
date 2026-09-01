<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MarketingTemplate;
use App\Models\MarketingCampaign;
use App\Models\MarketingLog;
use App\Models\Student;
use App\Models\SiteSetting;
use App\Services\WhatsAppService;

class MarketingController extends Controller
{
    public function index()
    {
        $templates = MarketingTemplate::latest()->get();
        $campaigns = MarketingCampaign::with('template')->latest()->get();
        $logs = MarketingLog::latest()->take(30)->get();
        $recipients = $this->activeStudentsWithPhone();
        $whatsappConfigured = SiteSetting::current()->hasWhatsAppConfigured();

        return view('marketing.index', compact('templates', 'campaigns', 'logs', 'recipients', 'whatsappConfigured'));
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:whatsapp,sms',
            'content' => 'required|string',
            'meta_template_name' => 'nullable|string|max:255',
            'meta_language' => 'nullable|string|max:10',
        ]);

        // A Meta template name is only meaningful for the WhatsApp channel
        if ($validated['type'] !== 'whatsapp') {
            $validated['meta_template_name'] = null;
        }
        $validated['meta_language'] = $validated['meta_language'] ?: 'en_US';

        MarketingTemplate::create($validated);

        return redirect()->back()->with('success', 'Marketing template saved successfully!');
    }

    public function launchCampaign(Request $request, WhatsAppService $whatsapp)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'template_id' => 'required|exists:marketing_templates,id',
            'channel' => 'required|in:WhatsApp,SMS',
        ]);

        $template = MarketingTemplate::findOrFail($request->template_id);
        $recipients = $this->activeStudentsWithPhone();

        $campaign = MarketingCampaign::create([
            'name' => $request->name,
            'template_id' => $template->id,
            'recipients_count' => $recipients->count(),
            'status' => 'Completed',
            'sent_at' => now(),
        ]);

        // Real sending is only wired up for WhatsApp, and only when both the
        // account credentials AND an approved Meta template name are set. Every
        // other combination (SMS, or WhatsApp without those) falls back to the
        // existing simulated log so nothing breaks for an unconfigured account.
        $canSendReal = $request->channel === 'WhatsApp'
            && SiteSetting::current()->hasWhatsAppConfigured()
            && filled($template->meta_template_name);

        $sentReal = 0;
        $failedReal = 0;

        foreach ($recipients as $student) {
            $name = optional($student->user)->name;
            $phone = optional($student->user)->phone;
            $courseName = optional($student->course)->name ?? 'our Programs';

            $personalizedMessage = str_replace(['{name}', '{course}'], [$name, $courseName], $template->content);

            if ($canSendReal) {
                $result = $whatsapp->sendTemplate(
                    $phone,
                    $template->meta_template_name,
                    $template->meta_language ?: 'en_US',
                    [$name, $courseName]
                );

                $result['success'] ? $sentReal++ : $failedReal++;

                MarketingLog::create([
                    'campaign_id' => $campaign->id,
                    'recipient_phone' => $phone,
                    'channel' => $request->channel,
                    'message' => $personalizedMessage,
                    'status' => $result['success'] ? 'Delivered' : 'Failed: '.substr((string) $result['error'], 0, 200),
                ]);
            } else {
                // Simulated dispatch — no real gateway configured for this channel/template
                MarketingLog::create([
                    'campaign_id' => $campaign->id,
                    'recipient_phone' => $phone,
                    'channel' => $request->channel,
                    'message' => $personalizedMessage,
                    'status' => rand(0, 10) > 1 ? 'Delivered' : 'Sent',
                ]);
            }
        }

        if ($canSendReal) {
            $summary = "Campaign '{$campaign->name}' sent via WhatsApp! {$sentReal} delivered";
            $summary .= $failedReal > 0 ? ", {$failedReal} failed (see Gateway Delivery Logs)." : '.';

            return redirect()->back()->with($failedReal > 0 && $sentReal === 0 ? 'error' : 'success', $summary);
        }

        return redirect()->back()->with('success', "Campaign '{$campaign->name}' launched successfully! Sent (simulated) ".$recipients->count().' messages.');
    }

    /**
     * Currently enrolled students with a phone number on file — the marketing
     * audience now that the pre-admission Enquiry pipeline has been removed.
     */
    private function activeStudentsWithPhone()
    {
        return Student::where('status', 'Active')
            ->with(['user', 'course'])
            ->get()
            ->filter(fn ($s) => filled(optional($s->user)->phone))
            ->values();
    }
}
