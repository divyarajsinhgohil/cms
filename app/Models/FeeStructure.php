<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeStructure extends Model
{
    protected $fillable = [
        'course_id', 'academic_year', 'tuition_fee', 'exam_fee', 'library_fee', 'other_fee', 'total_amount'
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
