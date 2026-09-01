<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = ['course_id', 'semester', 'name', 'code', 'credits'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function examMarks()
    {
        return $this->hasMany(ExamMark::class);
    }

    public function studyMaterials()
    {
        return $this->hasMany(StudyMaterial::class);
    }
}
