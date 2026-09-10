<?php

namespace App\Jobs;

use App\Models\CmsAlert;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class ProcessCmsAlertJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $alertId) {}

    public function handle(): void
    {
        $alert = CmsAlert::find($this->alertId);
        if (! $alert || $alert->status !== 'pending') {
            return;
        }

        try {
            // Delivery hook: extend here for email, SMS, or push channels.
            Log::info('CMS alert delivered', [
                'alert_id' => $alert->id,
                'user_id' => $alert->user_id,
                'type' => $alert->type,
                'title' => $alert->title,
            ]);

            $alert->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $alert->update(['status' => 'failed']);
            throw $e;
        }
    }
}
