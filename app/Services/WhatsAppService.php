<?php

namespace App\Services;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\Http;

/**
 * Thin wrapper around the official Meta WhatsApp Business Cloud API.
 *
 * Meta only allows a business to *initiate* a conversation (as opposed to replying
 * within an open 24-hour window) using a message template that has already been
 * submitted and approved in Meta Business Manager → WhatsApp Manager → Message
 * Templates. Free-form text cannot be used for outbound campaigns like an
 * admission-open announcement — this class only supports the template send path.
 */
class WhatsAppService
{
    /**
     * Send an approved template to one recipient.
     *
     * @param  array<int, string>  $bodyParams  Ordered values for the template's {{1}}, {{2}}, ... placeholders
     * @return array{success: bool, message_id: ?string, error: ?string}
     */
    public function sendTemplate(string $to, string $templateName, string $language, array $bodyParams = []): array
    {
        $settings = SiteSetting::current();

        if (! $settings->hasWhatsAppConfigured()) {
            return ['success' => false, 'message_id' => null, 'error' => 'WhatsApp Business API is not configured in Website Settings.'];
        }

        $payload = [
            'messaging_product' => 'whatsapp',
            'recipient_type' => 'individual',
            'to' => preg_replace('/[^\d+]/', '', $to),
            'type' => 'template',
            'template' => [
                'name' => $templateName,
                'language' => ['code' => $language],
            ],
        ];

        if (! empty($bodyParams)) {
            $payload['template']['components'] = [[
                'type' => 'body',
                'parameters' => array_map(fn ($v) => ['type' => 'text', 'text' => (string) $v], $bodyParams),
            ]];
        }

        try {
            $response = Http::withToken($settings->whatsapp_access_token)
                ->timeout(30)
                ->post("https://graph.facebook.com/v20.0/{$settings->whatsapp_phone_number_id}/messages", $payload);
        } catch (\Throwable $e) {
            return ['success' => false, 'message_id' => null, 'error' => 'Could not reach Meta: '.$e->getMessage()];
        }

        $data = $response->json() ?? [];

        if ($response->successful() && isset($data['messages'][0]['id'])) {
            return ['success' => true, 'message_id' => $data['messages'][0]['id'], 'error' => null];
        }

        return [
            'success' => false,
            'message_id' => null,
            'error' => $data['error']['message'] ?? ('HTTP '.$response->status()),
        ];
    }

    /**
     * Ask Meta whether the saved credentials actually work, so a bad token surfaces
     * on the Settings page instead of as a wall of failures during a real campaign.
     */
    public function checkConnection(): array
    {
        $settings = SiteSetting::current();

        if (! $settings->hasWhatsAppConfigured()) {
            return ['state' => 'unset', 'message' => 'No WhatsApp credentials saved yet.'];
        }

        try {
            $response = Http::withToken($settings->whatsapp_access_token)
                ->timeout(10)
                ->get("https://graph.facebook.com/v20.0/{$settings->whatsapp_phone_number_id}", [
                    'fields' => 'display_phone_number,verified_name,quality_rating',
                ]);
        } catch (\Throwable $e) {
            return ['state' => 'unknown', 'message' => 'Could not reach Meta: '.$e->getMessage()];
        }

        $data = $response->json() ?? [];

        if ($response->successful() && isset($data['display_phone_number'])) {
            return [
                'state' => 'ok',
                'message' => 'Connected to Meta.',
                'number' => $data['display_phone_number'],
                'name' => $data['verified_name'] ?? '',
            ];
        }

        $code = $data['error']['code'] ?? null;
        $hint = match ($code) {
            190 => 'The token is expired, revoked, or incomplete. Generate a new permanent token in Meta Business Settings → System Users.',
            100 => 'The Phone Number ID does not match this token. Check both in Meta → WhatsApp → API Setup.',
            200, 10 => 'The token lacks permission. It needs whatsapp_business_messaging and whatsapp_business_management.',
            default => 'Check the credentials in Meta → WhatsApp → API Setup.',
        };

        return [
            'state' => 'error',
            'message' => $data['error']['message'] ?? 'Rejected by Meta.',
            'hint' => $hint,
        ];
    }
}
