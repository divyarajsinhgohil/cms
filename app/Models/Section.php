<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $fillable = ['course_id', 'semester', 'name', 'capacity', 'class_teacher_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function classTeacher()
    {
        return $this->belongsTo(StaffProfile::class, 'class_teacher_id');
    }
}
