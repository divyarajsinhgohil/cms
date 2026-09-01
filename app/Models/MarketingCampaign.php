<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingCampaign extends Model
{
    protected $fillable = ['name', 'template_id', 'recipients_count', 'status', 'sent_at'];

    public function template()
    {
        return $this->belongsTo(MarketingTemplate::class, 'template_id');
    }

    public function logs()
    {
        return $this->hasMany(MarketingLog::class, 'campaign_id');
    }
}
