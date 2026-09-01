<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HostelRoom extends Model
{
    protected $fillable = ['building_name', 'room_no', 'capacity', 'current_occupancy', 'annual_fee'];

    public function allotments()
    {
        return $this->hasMany(RoomAllotment::class);
    }
}
