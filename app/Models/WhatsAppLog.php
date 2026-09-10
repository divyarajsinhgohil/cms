<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppLog extends Model
{
    protected $table = 'whatsapp_logs';

    protected $fillable = [
        'recipient_phone',
        'recipient_name',
        'type',
        'message',
        'status',
        'message_id',
        'error_details',
    ];
}
