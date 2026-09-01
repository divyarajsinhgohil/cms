<?php

namespace App\Http\Controllers\Api;

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
        $validated = $request->validate([
            'name' => 'required|string',
            'template_id' => 'required|exists:marketing_templates,id',
            'channel' => 'required|in:WhatsApp,SMS',
        ]);

        $template = MarketingTemplate::findOrFail($request->template_id);
        $recipients = Student::where('status', 'Active')->with(['user', 'course'])->get()
            ->filter(fn ($s) => filled(optional($s->user)->phone))
            ->values();

        $campaign = MarketingCampaign::create([
            'name' => $request->name,
            'template_id' => $template->id,
            'recipients_count' => $recipients->count(),
            'status' => 'Completed',
            'sent_at' => now(),
        ]);

        // Simulated dispatch — live WhatsApp/SMS gateway integration is coming soon.
        foreach ($recipients as $student) {
            $personalizedMessage = str_replace(
                ['{name}', '{course}'],
                [optional($student->user)->name, optional($student->course)->name ?? 'our Programs'],
                $template->content
            );

            MarketingLog::create([
                'campaign_id' => $campaign->id,
                'recipient_phone' => optional($student->user)->phone,
                'channel' => $request->channel,
                'message' => $personalizedMessage,
                'status' => rand(0, 10) > 1 ? 'Delivered' : 'Sent',
            ]);
        }

        return response()->json([
            'message' => "Campaign '{$campaign->name}' launched successfully! Sent {$recipients->count()} messages (simulated).",
            'data' => $campaign,
        ], 201);
    }
}
