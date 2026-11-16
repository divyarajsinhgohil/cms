<?php

namespace Tests\Feature;

use App\Jobs\SendMarketingCampaignJob;
use App\Models\MarketingCampaign;
use App\Models\MarketingTemplate;
use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class SettingsAndCampaignTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    private function admin(): User
    {
        return User::where('role', 'management')->firstOrFail();
    }

    private function settingsPayload(array $extra = []): array
    {
        return array_merge(['site_name' => 'Test College', 'academic_year' => '2026-27'], $extra);
    }

    public function test_whatsapp_token_is_encrypted_in_the_database(): void
    {
        $this->actingAs($this->admin())
            ->put('/settings', $this->settingsPayload(['whatsapp_access_token' => 'EAAG-secret-token']));

        $raw = DB::table('site_settings')->value('whatsapp_access_token');

        $this->assertNotSame('EAAG-secret-token', $raw);
        $this->assertStringNotContainsString('EAAG-secret-token', $raw);
        $this->assertSame('EAAG-secret-token', SiteSetting::current()->whatsapp_access_token);
    }

    public function test_blank_token_field_keeps_the_saved_token(): void
    {
        $this->actingAs($this->admin())
            ->put('/settings', $this->settingsPayload(['whatsapp_access_token' => 'EAAG-secret-token']));
        $this->actingAs($this->admin())
            ->put('/settings', $this->settingsPayload(['whatsapp_access_token' => '']));

        $this->assertSame('EAAG-secret-token', SiteSetting::current()->fresh()->whatsapp_access_token);
    }

    public function test_settings_page_never_shows_the_token(): void
    {
        SiteSetting::current()->update(['whatsapp_access_token' => 'EAAG-secret-token']);

        $this->actingAs($this->admin())->get('/settings')
            ->assertOk()
            ->assertDontSee('EAAG-secret-token');
    }

    public function test_campaign_is_queued_instead_of_sent_during_the_request(): void
    {
        Queue::fake();
        $template = MarketingTemplate::firstOrFail();

        $this->actingAs($this->admin())
            ->post('/marketing/campaign', ['name' => 'Admissions 2027', 'template_id' => $template->id, 'channel' => 'SMS'])
            ->assertSessionHas('success');

        $campaign = MarketingCampaign::where('name', 'Admissions 2027')->firstOrFail();
        $this->assertSame('Queued', $campaign->status);
        Queue::assertPushed(SendMarketingCampaignJob::class, fn ($job) => $job->campaignId === $campaign->id);
    }

    public function test_campaign_job_logs_every_recipient_as_simulated_without_gateway(): void
    {
        $template = MarketingTemplate::firstOrFail();
        $campaign = MarketingCampaign::create([
            'name' => 'Job test', 'template_id' => $template->id, 'recipients_count' => 0, 'status' => 'Queued',
        ]);

        SendMarketingCampaignJob::dispatchSync($campaign->id, 'SMS');

        $campaign->refresh();
        $this->assertSame('Completed', $campaign->status);
        $this->assertGreaterThan(0, $campaign->logs()->count());
        $this->assertSame(0, $campaign->logs()->where('status', '!=', 'Simulated')->count());
    }
}
