<?php

namespace App\Http\Controllers;

use App\Jobs\SendMarketingCampaignJob;

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

    public function launchCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'required|exists:marketing_templates,id',
            'channel' => 'required|in:WhatsApp,SMS',
        ]);

        $campaign = MarketingCampaign::create([
            'name' => $request->name,
            'template_id' => $request->template_id,
            'recipients_count' => $this->activeStudentsWithPhone()->count(),
            'status' => 'Queued',
        ]);

        // Sending happens in the background (queue worker), so large lists cannot time out.
        SendMarketingCampaignJob::dispatch($campaign->id, $request->channel);

        return redirect()->back()->with('success', "Campaign '{$campaign->name}' queued for {$campaign->recipients_count} students. Delivery results will appear in the logs below.");
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
