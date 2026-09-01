<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\HostelRoom;
use App\Models\Outpass;
use App\Models\RoomAllotment;
use Illuminate\Http\Request;

class HostelController extends Controller
{
    public function rooms()
    {
        return response()->json(HostelRoom::with('allotments.student.user')->get());
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'building_name' => 'required|string',
            'room_no' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'annual_fee' => 'required|numeric|min:0',
        ]);

        $room = HostelRoom::create($request->only(['building_name', 'room_no', 'capacity', 'annual_fee']));

        return response()->json(['message' => 'Hostel room added successfully!', 'data' => $room], 201);
    }

    public function allotments()
    {
        return response()->json(RoomAllotment::with(['student.user', 'student.course', 'room'])->latest()->get());
    }

    public function allotRoom(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'hostel_room_id' => 'required|exists:hostel_rooms,id',
            'allotment_date' => 'required|date',
        ]);

        $room = HostelRoom::findOrFail($request->hostel_room_id);
        if ($room->current_occupancy >= $room->capacity) {
            return response()->json(['message' => "Room {$room->room_no} is already full!"], 422);
        }

        $allotment = RoomAllotment::create([
            'student_id' => $request->student_id,
            'hostel_room_id' => $request->hostel_room_id,
            'allotment_date' => $request->allotment_date,
        ]);

        $room->increment('current_occupancy');

        return response()->json(['message' => 'Hostel room allotted successfully!', 'data' => $allotment], 201);
    }

    public function outpasses(Request $request)
    {
        $query = Outpass::with('student.user');

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        return response()->json($query->latest()->get());
    }

    public function storeOutpass(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string',
            'out_time' => 'required|date',
            'in_time' => 'required|date|after:out_time',
        ]);

        $outpass = Outpass::create([
            'student_id' => $request->student_id,
            'reason' => $request->reason,
            'out_time' => $request->out_time,
            'in_time' => $request->in_time,
            'status' => 'Approved',
        ]);

        return response()->json(['message' => 'Outpass gate pass issued successfully!', 'data' => $outpass], 201);
    }
}
