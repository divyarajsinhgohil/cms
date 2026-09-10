<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BroadcastMessage extends Model
{
    protected $fillable = [
        'user_id',
        'title',
        'message',
        'target_role',
        'type',
        'display_format',
        'action_url',
        'is_active',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'expires_at' => 'datetime',
        ];
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function reads()
    {
        return $this->hasMany(UserBroadcastRead::class, 'broadcast_message_id');
    }

    /**
     * Helper to dispatch system-wide real-time broadcasts for any website event.
     */
    public static function dispatchSystemMessage(
        string $title,
        string $message,
        string $targetRole = 'all',
        string $type = 'info',
        string $displayFormat = 'toast',
        ?string $actionUrl = null,
        int $expiresInHours = 24
    ): self {
        $senderId = Auth::id() ?? User::where('role', 'management')->value('id') ?? 1;

        return static::create([
            'user_id' => $senderId,
            'title' => $title,
            'message' => $message,
            'target_role' => $targetRole,
            'type' => $type,
            'display_format' => $displayFormat,
            'action_url' => $actionUrl,
            'is_active' => true,
            'expires_at' => now()->addHours($expiresInHours),
        ]);
    }
}
