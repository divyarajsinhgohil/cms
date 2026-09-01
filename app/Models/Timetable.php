<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Timetable extends Model
{
    protected $fillable = [
        'course_id', 'semester', 'subject_id', 'staff_id', 'classroom', 'day_of_week',
        'specific_date', 'type', 'label', 'start_time', 'end_time',
    ];

    protected function casts(): array
    {
        return [
            'specific_date' => 'date',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }
}
