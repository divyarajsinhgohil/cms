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

    @if($ownOnly)
    <div class="mb-3">
        <div class="btn-group" role="group" aria-label="Timetable view">
            <a href="{{ route('timetables.index', ['view' => 'grid']) }}" class="btn btn-sm {{ ($viewMode ?? 'grid') === 'grid' ? 'btn-primary' : 'btn-outline-primary' }}">Weekly Grid</a>
            <a href="{{ route('timetables.index', ['view' => 'calendar', 'month' => ($calendarMonth ?? now())->format('Y-m')]) }}" class="btn btn-sm {{ ($viewMode ?? 'grid') === 'calendar' ? 'btn-primary' : 'btn-outline-primary' }}">Calendar</a>
        </div>
    </div>
    @endif
@unless($ownOnly)
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('timetables.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold" for="course_id">Select Course</label>
                <select name="course_id" id="course_id" class="form-select" onchange="this.form.submit()">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold" for="semester">Select Semester</label>
                <select name="semester" id="semester" class="form-select" onchange="this.form.submit()">
                    @for($s = 1; $s <= 6; $s++)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold" for="view">View</label>
                <select name="view" id="view" class="form-select" onchange="this.form.submit()">
                    <option value="grid" {{ ($viewMode ?? 'grid') === 'grid' ? 'selected' : '' }}>Weekly Grid</option>
                    <option value="calendar" {{ ($viewMode ?? 'grid') === 'calendar' ? 'selected' : '' }}>Calendar View</option>
                </select>
            </div>
            @if(($viewMode ?? 'grid') === 'calendar')
            <div class="col-md-2">
                <label class="form-label fw-bold" for="month">Month</label>
                <input type="month" name="month" id="month" class="form-control" value="{{ $calendarMonth->format('Y-m') }}" onchange="this.form.submit()">
            </div>
            @endif
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
                    @if($slot->classroom)
                        <div class="text-muted fs-7"><i class="fa-solid fa-door-open me-1"></i> {{ $slot->classroom }}</div>
                    @endif
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@if(($viewMode ?? 'grid') === 'calendar')
<!-- Calendar View -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h2 class="h5 fw-bold mb-0"><i class="fa-solid fa-calendar text-primary me-2" aria-hidden="true"></i> {{ $calendarMonth->format('F Y') }} Calendar</h2>
    </div>
    <div class="card-body">
        <div class="cms-calendar-grid" role="grid" aria-label="Monthly timetable calendar">
            @php
                $calStart = $calendarMonth->copy()->startOfMonth()->startOfWeek(\Illuminate\Support\Carbon::MONDAY);
                $calEnd = $calendarMonth->copy()->endOfMonth()->endOfWeek(\Illuminate\Support\Carbon::SUNDAY);
            @endphp
            @for($day = $calStart->copy(); $day->lte($calEnd); $day->addDay())
                @php $key = $day->toDateString(); $slots = $calendarEvents[$key] ?? collect(); @endphp
                <div class="cms-calendar-day {{ $day->isToday() ? 'is-today' : '' }} {{ !$day->isSameMonth($calendarMonth) ? 'opacity-50' : '' }}" role="gridcell">
                    <div class="fw-bold small mb-1">{{ $day->format('D j') }}</div>
                    @foreach($slots as $slot)
                        <div class="cms-calendar-slot" title="{{ optional($slot->subject)->name }}">
                            {{ \Illuminate\Support\Str::limit($slot->start_time, 5, '') }}
                            {{ $slot->type === 'Break' ? ($slot->label ?: 'Break') : optional($slot->subject)->code }}
                            @if($slot->classroom)<span class="text-muted"> · {{ $slot->classroom }}</span>@endif
                        </div>
                    @endforeach
                </div>
            @endfor
        </div>
    </div>
</div>
@else
<!-- Timetable Grid -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-calendar-days text-primary me-2"></i> Regular Weekly Schedule</h5>
    </div>
    <div class="card-body p-0">
        @php
            // Distinct time periods across the week, sorted by start time — these become the columns.
            $periods = $timetables
                ->map(fn ($t) => ['start' => $t->start_time, 'end' => $t->end_time])
                ->unique(fn ($p) => $p['start'].'|'.$p['end'])
                ->sortBy('start')
                ->values();
        @endphp
        <div class="table-responsive">
            <table class="table table-bordered align-middle mb-0 text-center">
                <thead class="table-dark">
                    <tr>
                        <th style="min-width: 110px;">Time</th>
                        @foreach($days as $day)
                            <th style="min-width: 150px;">{{ $day }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($periods as $p)
                    <tr>
                        <td class="fw-bold bg-light font-monospace fs-7">
                            {{ \Illuminate\Support\Str::limit($p['start'], 5, '') }} - {{ \Illuminate\Support\Str::limit($p['end'], 5, '') }}
                        </td>
                        @foreach($days as $day)
                            @php
                                $slot = $timetables->first(fn ($t) =>
                                    $t->day_of_week === $day
                                    && $t->start_time == $p['start']
                                    && $t->end_time == $p['end']
                                );
                            @endphp
                            <td class="{{ $slot ? ($slot->type === 'Break' ? 'bg-light' : '') : '' }}">
                                @if($slot)
                                    @if($slot->type === 'Break')
                                        <div class="fw-semibold text-muted"><i class="fa-solid fa-mug-hot me-1"></i> {{ $slot->label ?: 'Break' }}</div>
                                    @else
                                        <div class="fw-bold text-dark">{{ optional($slot->subject)->name }}</div>
                                        <span class="badge {{ $slot->type === 'Lab' ? 'bg-info' : 'bg-primary' }} fs-7">{{ $slot->type }}</span>
                                        @if($slot->classroom)
                                            <div class="text-muted fs-7 mt-1"><i class="fa-solid fa-door-open me-1"></i> {{ $slot->classroom }}</div>
                                        @endif
                                    @endif
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                    @empty
                    <tr>
                        <td colspan="{{ count($days) + 1 }}" class="text-muted small">No slots scheduled.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endif

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
                            <label class="form-label fw-bold" for="staff_id">Faculty Member</label>
                            <select name="staff_id" id="staff_id" class="form-select">
                                @foreach($staffMembers as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold" for="classroom">Classroom / Room</label>
                            <input type="text" name="classroom" id="classroom" class="form-control" placeholder="e.g. Room 101, Lab 3">
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
                    <div class="alert alert-warning py-2 fs-7 mb-0" role="status">
                        <i class="fa-solid fa-shield-halved me-1" aria-hidden="true"></i> <strong>Clash Prevention:</strong> Faculty and room conflicts are blocked before saving.
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
