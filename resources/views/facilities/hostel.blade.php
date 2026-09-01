@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Hostel & Room Allotment Master</h3>
        <p class="text-muted mb-0">Manage hostel buildings, room capacity, student room allotments, and home leave outpasses.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#addRoomModal">
            <i class="fa-solid fa-hotel me-1"></i> Add Hostel Room
        </button>
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#allotRoomModal">
            <i class="fa-solid fa-key me-1"></i> Allot Room
        </button>
        <button class="btn btn-warning text-dark shadow-sm" data-bs-toggle="modal" data-bs-target="#outpassModal">
            <i class="fa-solid fa-door-open me-1"></i> Issue Outpass
        </button>
    </div>
</div>

<!-- Room Allotments Table -->
<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 fw-bold fs-5">
                <i class="fa-solid fa-bed text-primary me-2"></i> Current Student Room Allotments
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Student</th>
                                <th>Building & Room</th>
                                <th>Course Program</th>
                                <th>Allotment Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($allotments as $allot)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ optional($allot->student->user)->name }}</div>
                                    <div class="text-muted fs-7">{{ optional($allot->student)->enrollment_no }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle fs-7">
                                        {{ $allot->room->building_name }} - Room {{ $allot->room->room_no }}
                                    </span>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ optional($allot->student->course)->name }}</span></td>
                                <td>{{ $allot->allotment_date }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No student room allotments found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Hostel Buildings Status -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 fw-bold fs-5">
                <i class="fa-solid fa-building text-success me-2"></i> Hostel Buildings & Occupancy
            </div>
            <div class="card-body">
                @forelse($rooms as $room)
                <div class="p-3 bg-light rounded-3 mb-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <div class="fw-bold text-dark">{{ $room->building_name }} - Room {{ $room->room_no }}</div>
                        <span class="badge bg-dark">₹{{ number_format($room->annual_fee) }}/yr</span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center fs-7 text-muted">
                        <span>Occupancy: {{ $room->current_occupancy }} / {{ $room->capacity }} Beds</span>
                        <span class="badge {{ $room->current_occupancy >= $room->capacity ? 'bg-danger' : 'bg-success' }}">
                            {{ $room->current_occupancy >= $room->capacity ? 'FULL' : 'AVAILABLE' }}
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center text-muted py-4">No hostel rooms registered.</div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Room -->
<div class="modal fade" id="addRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hostels.room.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-hotel text-primary me-2"></i> Add Hostel Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Hostel Building</label>
                        <input type="text" name="building_name" class="form-control" placeholder="e.g. Boys Hostel Block A" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Room Number</label>
                        <input type="text" name="room_no" class="form-control" placeholder="e.g. Room 101" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Bed Capacity</label>
                        <input type="number" name="capacity" class="form-control" value="3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Annual Hostel Fee (₹)</label>
                        <input type="number" name="annual_fee" class="form-control" value="35000" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Add Room</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Allot Room -->
<div class="modal fade" id="allotRoomModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('hostels.allot') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-key text-primary me-2"></i> Allot Hostel Room</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Student</label>
                        <select name="student_id" class="form-select" required>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ optional($st->user)->name }} ({{ $st->enrollment_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Hostel Room</label>
                        <select name="hostel_room_id" class="form-select" required>
                            @foreach($rooms as $rm)
                                <option value="{{ $rm->id }}">{{ $rm->building_name }} - Room {{ $rm->room_no }} (Available: {{ $rm->capacity - $rm->current_occupancy }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Allotment Date</label>
                        <input type="date" name="allotment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Confirm Allotment</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
