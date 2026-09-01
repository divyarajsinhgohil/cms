<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeePayment extends Model
{
    protected $fillable = [
        'student_id', 'receipt_no', 'amount_paid', 'payment_mode', 'payment_date', 'remarks'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
