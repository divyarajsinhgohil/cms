<?php

namespace App\Http\Controllers\Api;

use App\Jobs\SendMarketingCampaignJob;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\MarketingCampaign;
use App\Models\MarketingLog;
use App\Models\MarketingTemplate;
use Illuminate\Http\Request;

class MarketingController extends Controller
{
    public function templates()
    {
        return response()->json(MarketingTemplate::latest()->get());
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string',
            'type' => 'required|in:whatsapp,sms',
            'content' => 'required|string',
        ]);

        $template = MarketingTemplate::create($validated);

        return response()->json(['message' => 'Marketing template saved successfully!', 'data' => $template], 201);
    }

    public function campaigns()
    {
        return response()->json(MarketingCampaign::with('template')->latest()->get());
    }

    public function logs()
    {
        return response()->json(MarketingLog::latest()->take(50)->get());
    }

    public function launchCampaign(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'template_id' => 'required|exists:marketing_templates,id',
            'channel' => 'required|in:WhatsApp,SMS',
        ]);

        $recipients = Student::where('status', 'Active')->with('user')->get()
            ->filter(fn ($s) => filled(optional($s->user)->phone))
            ->count();

        $campaign = MarketingCampaign::create([
            'name' => $request->name,
            'template_id' => $request->template_id,
            'recipients_count' => $recipients,
            'status' => 'Queued',
        ]);

        SendMarketingCampaignJob::dispatch($campaign->id, $request->channel);

        return response()->json([
            'message' => "Campaign '{$campaign->name}' queued for {$recipients} students.",
            'data' => $campaign,
        ], 202);
    }
}
