<?php

namespace App\Jobs;

use App\Models\MarketingCampaign;
use App\Models\MarketingLog;
use App\Models\SiteSetting;
use App\Models\Student;
use App\Services\WhatsAppService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

/**
 * Sends a marketing campaign to every active student with a phone number.
 *
 * This runs on the queue (php artisan queue:work) instead of inside the web
 * request, so a campaign to thousands of students cannot time out the page.
 */
class SendMarketingCampaignJob implements ShouldQueue
{
    use Queueable;

    /** A campaign can take a while: one HTTP call to Meta per recipient. */
    public int $timeout = 1800;

    /** Do not retry automatically - a retry would message everyone twice. */
    public int $tries = 1;

    public function __construct(public int $campaignId, public string $channel) {}

    public function handle(WhatsAppService $whatsapp): void
    {
        $campaign = MarketingCampaign::with('template')->find($this->campaignId);
        if (! $campaign || ! $campaign->template) {
            return;
        }

        $template = $campaign->template;
        $campaign->update(['status' => 'Sending']);

        // Real sending only works for WhatsApp with account credentials AND an
        // approved Meta template name. Everything else is logged as "Simulated"
        // so the logs never claim a message was delivered when it was not.
        $canSendReal = $this->channel === 'WhatsApp'
            && SiteSetting::current()->hasWhatsAppConfigured()
            && filled($template->meta_template_name);

        $failed = 0;

        Student::where('status', 'Active')
            ->with(['user', 'course'])
            ->chunkById(200, function ($students) use ($campaign, $template, $canSendReal, $whatsapp, &$failed) {
                foreach ($students as $student) {
                    $name = optional($student->user)->name;
                    $phone = optional($student->user)->phone;
                    if (blank($phone)) {
                        continue;
                    }

                    $courseName = optional($student->course)->name ?? 'our Programs';
                    $message = str_replace(['{name}', '{course}'], [$name, $courseName], $template->content);

                    if ($canSendReal) {
                        $result = $whatsapp->sendTemplate($phone, $template->meta_template_name, $template->meta_language ?: 'en_US', [$name, $courseName]);
                        $status = $result['success'] ? 'Delivered' : 'Failed: '.substr((string) $result['error'], 0, 200);
                        $result['success'] || $failed++;
                    } else {
                        $status = 'Simulated';
                    }

                    MarketingLog::create([
                        'campaign_id' => $campaign->id,
                        'recipient_phone' => $phone,
                        'channel' => $this->channel,
                        'message' => $message,
                        'status' => $status,
                    ]);
                }
            });

        $campaign->update([
            'status' => $failed > 0 ? 'Completed with errors' : 'Completed',
            'sent_at' => now(),
        ]);
    }

    public function failed(?\Throwable $e): void
    {
        MarketingCampaign::whereKey($this->campaignId)->update(['status' => 'Failed']);
    }
}
