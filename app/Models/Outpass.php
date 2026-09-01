<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Outpass extends Model
{
    protected $fillable = ['student_id', 'reason', 'out_time', 'in_time', 'status'];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
