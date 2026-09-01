<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketingTemplate extends Model
{
    protected $fillable = ['title', 'type', 'content', 'meta_template_name', 'meta_language'];

    public function campaigns()
    {
        return $this->hasMany(MarketingCampaign::class, 'template_id');
    }
}
