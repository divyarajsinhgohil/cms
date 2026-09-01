<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingLog extends Model
{
    protected $fillable = ['campaign_id', 'recipient_phone', 'channel', 'message', 'status'];

    public function campaign()
    {
        return $this->belongsTo(MarketingCampaign::class, 'campaign_id');
    }
}
