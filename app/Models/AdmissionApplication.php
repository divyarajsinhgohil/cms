<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionApplication extends Model
{
    protected $fillable = [
        'surname', 'first_name', 'last_name', 'father_name', 'mother_name', 'gender',
        'aadhar_number', 'photo_path', 'aadhar_photo_path',
        'category', 'caste_certificate_path',
        'mobile', 'father_mobile', 'mother_mobile', 'email',
        'home_address', 'permanent_address',
        'wants_hostel', 'hostel_preference',
        'course_id', 'status', 'reviewed_by', 'reviewed_at', 'rejection_reason', 'student_id',
    ];

    protected function casts(): array
    {
        return [
            'wants_hostel' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function fullName(): string
    {
        return trim("{$this->first_name} {$this->last_name} {$this->surname}");
    }
}
