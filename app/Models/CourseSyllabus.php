<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseSyllabus extends Model
{
    protected $table = 'course_syllabi';

    protected $fillable = [
        'course_id',
        'semester',
        'title',
        'description',
        'syllabus_pdf_path',
        'uploaded_by',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
