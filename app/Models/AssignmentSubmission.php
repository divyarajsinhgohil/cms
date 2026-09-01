<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssignmentSubmission extends Model
{
    protected $fillable = [
        'study_material_id', 'student_id', 'file_path', 'comments', 'status', 'marks_obtained'
    ];

    public function studyMaterial()
    {
        return $this->belongsTo(StudyMaterial::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
