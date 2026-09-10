<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultCollection extends Model
{
    protected $fillable = [
        'student_id', 'semester', 'collected', 'collected_at', 'collected_by',
    ];

    protected function casts(): array
    {
        return [
            'collected' => 'boolean',
            'collected_at' => 'datetime',
        ];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function collector()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
