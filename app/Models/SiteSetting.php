<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'affiliation', 'logo_url', 'contact_email', 'contact_phone', 'address', 'academic_year',
        'whatsapp_access_token', 'whatsapp_phone_number_id', 'whatsapp_business_account_id',
        'whatsapp_auto_fee_receipt', 'whatsapp_auto_attendance_alert', 'whatsapp_auto_exam_result', 'whatsapp_auto_admission_approval',
    ];

    protected function casts(): array
    {
        return [
            'whatsapp_auto_fee_receipt' => 'boolean',
            'whatsapp_auto_attendance_alert' => 'boolean',
            'whatsapp_auto_exam_result' => 'boolean',
            'whatsapp_auto_admission_approval' => 'boolean',
        ];
    }

    public function hasWhatsAppConfigured(): bool
    {
        return filled($this->whatsapp_access_token) && filled($this->whatsapp_phone_number_id);
    }

    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }
}
