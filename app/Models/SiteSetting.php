<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name', 'affiliation', 'logo_url', 'contact_email', 'contact_phone', 'address', 'academic_year',
        'whatsapp_access_token', 'whatsapp_phone_number_id', 'whatsapp_business_account_id',
    ];

    public function hasWhatsAppConfigured(): bool
    {
        return filled($this->whatsapp_access_token) && filled($this->whatsapp_phone_number_id);
    }

    public static function current(): self
    {
        return static::first() ?? static::create([]);
    }
}
