@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">{{ $ownOnly ? 'My Weekly Timetable' : 'Auto Timetable & Clash Detection Engine' }}</h3>
        <p class="text-muted mb-0">
            @if($ownOnly)
                Your current Semester {{ $semester }} class schedule.
            @else
                Generate conflict-free weekly schedules with faculty clash detection.
            @endif
        </p>
    </div>
    @if(Auth::user()->isManagement())
    <div class="d-flex gap-2">
        <form action="{{ route('timetables.auto') }}" method="POST" data-confirm="Auto-generate clash-free schedule for this course? This replaces the current recurring schedule (one-time date overrides are kept).">
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
            <input type="hidden" name="semester" value="{{ $semester }}">
            <button type="submit" class="btn btn-success shadow-sm">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Auto-Generate Schedule
            </button>
        </form>

        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newSlotModal">
            <i class="fa-solid fa-plus me-1"></i> Add Timetable Slot
        </button>
    </div>
    @endif
</div>

@unless($ownOnly)
<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('timetables.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold">Select Course</label>
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Select Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    @for($s = 1; $s <= 6; $s++)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
        </form>
    </div>
</div>
@endunless

@if($todayOverride->isNotEmpty())
<!-- Highlighted: today's schedule differs from the regular recurring one -->
<div class="card border-0 shadow-lg rounded-4 mb-4" style="border-left: 6px solid #f5841f !important;">
    <div class="card-header bg-warning-subtle py-3 border-0 d-flex align-items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-warning fs-5"></i>
        <h5 class="fw-bold mb-0 text-dark">Today's Schedule Is Different! ({{ today()->format('l, M d') }})</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-3">
            @foreach($todayOverride as $slot)
            <div class="p-3 bg-warning-subtle rounded-3 border border-warning shadow-sm flex-fill" style="min-width: 220px;">
                <span class="badge bg-dark font-monospace mb-1">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                @if($slot->type === 'Break')
                    <div class="fw-bold text-dark mb-1"><i class="fa-solid fa-mug-hot me-1"></i> {{ $slot->label ?: 'Break' }}</div>
                @else
                    <div class="fw-bold text-dark mb-1">
                        {{ optional($slot->subject)->name }}
                        <span class="badge {{ $slot->type === 'Lab' ? 'bg-info' : 'bg-primary' }} fs-7 ms-1">{{ $slot->type }}</span>
                    </div>
                    <div class="text-muted fs-7"><i class="fa-solid fa-user-tie me-1"></i> {{ optional($slot->staff)->name }}</div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- Timetable Grid -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Regular Weekly Schedule</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0">
                <thead class="table-dark text-center">
                    <tr>
                        <th width="15%">Day</th>
                        <th>Scheduled Slots</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($days as $day)
                    @php
                        $daySlots = $timetables->where('day_of_week', $day);
                    @endphp
                    <tr>
                        <td class="fw-bold text-center bg-light">{{ $day }}</td>
                        <td>
                            <div class="d-flex flex-wrap gap-3">
                                @forelse($daySlots as $slot)
                                <div class="p-3 bg-light rounded-3 border-start border-4 {{ $slot->type === 'Break' ? 'border-secondary' : ($slot->type === 'Lab' ? 'border-info' : 'border-primary') }} shadow-sm flex-fill" style="min-width: 220px;">
                                    <div class="mb-1">
                                        <span class="badge bg-dark font-monospace">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                                    </div>
                                    @if($slot->type === 'Break')
                                        <div class="fw-bold text-muted"><i class="fa-solid fa-mug-hot me-1"></i> {{ $slot->label ?: 'Break' }}</div>
                                    @else
                                        <div class="fw-bold text-dark mb-1">
                                            {{ optional($slot->subject)->name }}
                                            <span class="badge {{ $slot->type === 'Lab' ? 'bg-info' : 'bg-primary' }} fs-7 ms-1">{{ $slot->type }}</span>
                                        </div>
                                        <div class="text-muted fs-7"><i class="fa-solid fa-user-tie me-1"></i> {{ optional($slot->staff)->name }}</div>
                                    @endif
                                </div>
                                @empty
                                <span class="text-muted small py-2">No slots scheduled.</span>
                                @endforelse
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Slot -->
<div class="modal fade" id="newSlotModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('timetables.store') }}" method="POST">
                @csrf
                <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
                <input type="hidden" name="semester" value="{{ $semester }}">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-plus text-primary me-2"></i> Add Timetable Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Slot Type</label>
                        <div class="btn-group w-100" role="group">
                            <input type="radio" class="btn-check" name="type" id="typeLecture" value="Lecture" checked onchange="cmsToggleSlotType()">
                            <label class="btn btn-outline-primary" for="typeLecture"><i class="fa-solid fa-chalkboard me-1"></i> Lecture</label>

                            <input type="radio" class="btn-check" name="type" id="typeLab" value="Lab" onchange="cmsToggleSlotType()">
                            <label class="btn btn-outline-info" for="typeLab"><i class="fa-solid fa-flask me-1"></i> Lab</label>

                            <input type="radio" class="btn-check" name="type" id="typeBreak" value="Break" onchange="cmsToggleSlotType()">
                            <label class="btn btn-outline-secondary" for="typeBreak"><i class="fa-solid fa-mug-hot me-1"></i> Break</label>
                        </div>
                    </div>

                    <div id="slotSubjectFields">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Select Subject</label>
                            <select name="subject_id" class="form-select">
                                @foreach($subjects as $sub)
                                    <option value="{{ $sub->id }}">{{ $sub->name }} ({{ $sub->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Faculty Member</label>
                            <select name="staff_id" class="form-select">
                                @foreach($staffMembers as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div id="slotBreakField" class="d-none mb-3">
                        <label class="form-label fw-bold">Break Label</label>
                        <input type="text" name="label" class="form-control" placeholder="e.g. Lunch Break, Short Break" value="Lunch Break">
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold d-block">Applies To</label>
                        <div class="btn-group w-100 mb-2" role="group">
                            <input type="radio" class="btn-check" name="schedule_mode" id="modeRecurring" value="recurring" checked onchange="cmsToggleScheduleMode()">
                            <label class="btn btn-outline-dark" for="modeRecurring"><i class="fa-solid fa-repeat me-1"></i> Every Week</label>

                            <input type="radio" class="btn-check" name="schedule_mode" id="modeSpecific" value="specific" onchange="cmsToggleScheduleMode()">
                            <label class="btn btn-outline-dark" for="modeSpecific"><i class="fa-solid fa-calendar-day me-1"></i> One Specific Date</label>
                        </div>

                        <div id="recurringField">
                            <select name="day_of_week" class="form-select">
                                @foreach($days as $d)
                                    <option value="{{ $d }}">{{ $d }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div id="specificField" class="d-none">
                            <input type="date" name="specific_date" class="form-control" value="{{ date('Y-m-d') }}">
                            <div class="form-text fs-7">A one-time override for just this date — students will see it highlighted, and it won't change the regular weekly schedule.</div>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Start Time</label>
                            <input type="time" name="start_time" class="form-control" value="09:30" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">End Time</label>
                            <input type="time" name="end_time" class="form-control" value="10:30" required>
                        </div>
                    </div>
                    <div class="alert alert-warning py-2 fs-7 mb-0">
                        <i class="fa-solid fa-shield-halved me-1"></i> <strong>Clash Prevention:</strong> The system checks this faculty member isn't already booked at this time.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Validate & Add Slot</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function cmsToggleSlotType() {
        const isBreak = document.getElementById('typeBreak').checked;
        document.getElementById('slotSubjectFields').classList.toggle('d-none', isBreak);
        document.getElementById('slotBreakField').classList.toggle('d-none', !isBreak);
        document.querySelector('[name="subject_id"]').required = !isBreak;
        document.querySelector('[name="staff_id"]').required = !isBreak;
    }
    function cmsToggleScheduleMode() {
        const specific = document.getElementById('modeSpecific').checked;
        document.getElementById('recurringField').classList.toggle('d-none', specific);
        document.getElementById('specificField').classList.toggle('d-none', !specific);
    }
</script>
@endsection
