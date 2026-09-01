<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = [
        'title', 'content', 'target_audience', 'priority', 'attachment_path', 'posted_by', 'course_id', 'semester'
    ];

    public function author()
    {
        return $this->belongsTo(User::class, 'posted_by');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
