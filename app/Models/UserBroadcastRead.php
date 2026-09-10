<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBroadcastRead extends Model
{
    protected $fillable = [
        'user_id',
        'broadcast_message_id',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function broadcastMessage()
    {
        return $this->belongsTo(BroadcastMessage::class);
    }
}
