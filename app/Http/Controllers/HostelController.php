<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\HostelRoom;
use App\Models\RoomAllotment;
use App\Models\Outpass;
use App\Models\Student;

class HostelController extends Controller
{
    public function index()
    {
        $rooms = HostelRoom::with('allotments.student.user')->get();
        $allotments = RoomAllotment::with(['student.user', 'student.course', 'room'])->latest()->get();
        $outpasses = Outpass::with('student.user')->latest()->get();
        $students = Student::with('user')->get();

        return view('facilities.hostel', compact('rooms', 'allotments', 'outpasses', 'students'));
    }

    public function storeRoom(Request $request)
    {
        $request->validate([
            'building_name' => 'required|string',
            'room_no' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'annual_fee' => 'required|numeric|min:0',
        ]);

        HostelRoom::create($request->only(['building_name', 'room_no', 'capacity', 'annual_fee']));
        return redirect()->back()->with('success', 'Hostel room added successfully!');
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
            return redirect()->back()->with('error', "Room {$room->room_no} is already full!");
        }

        RoomAllotment::create([
            'student_id' => $request->student_id,
            'hostel_room_id' => $request->hostel_room_id,
            'allotment_date' => $request->allotment_date,
        ]);

        $room->increment('current_occupancy');
        return redirect()->back()->with('success', 'Hostel room allotted successfully!');
    }

    public function storeOutpass(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'reason' => 'required|string',
            'out_time' => 'required|date',
            'in_time' => 'required|date|after:out_time',
        ]);

        Outpass::create([
            'student_id' => $request->student_id,
            'reason' => $request->reason,
            'out_time' => $request->out_time,
            'in_time' => $request->in_time,
            'status' => 'Approved',
        ]);

        return redirect()->back()->with('success', 'Outpass gate pass issued successfully!');
    }
}
