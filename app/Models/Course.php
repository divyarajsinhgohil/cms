<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['department_id', 'name', 'code', 'duration_years'];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function feeStructure()
    {
        return $this->hasOne(FeeStructure::class);
    }

    public function assignedStaff()
    {
        return $this->belongsToMany(StaffProfile::class, 'staff_courses');
    }

    public function sections()
    {
        return $this->hasMany(Section::class);
    }
}
