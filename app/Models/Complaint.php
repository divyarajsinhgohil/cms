<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = ['user_id', 'category', 'title', 'description', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
