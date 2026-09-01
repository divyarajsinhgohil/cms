<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyMaterial extends Model
{
    protected $fillable = [
        'subject_id', 'uploaded_by', 'title', 'description', 'type', 'file_path', 'due_date'
    ];

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }
}
