<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CmsAlert extends Model
{
    protected $fillable = [
        'user_id', 'type', 'title', 'body', 'metadata',
        'status', 'queued_at', 'sent_at', 'read_at',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
            'queued_at' => 'datetime',
            'sent_at' => 'datetime',
            'read_at' => 'datetime',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function markRead(): void
    {
        $this->update([
            'status' => 'read',
            'read_at' => now(),
        ]);
    }
}
