@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Daily Attendance & Attendance Percentage Report</h3>
        <p class="text-muted mb-0">Mark daily student attendance and track low attendance warning indicators.</p>
    </div>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('attendance.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold">Select Course</label>
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                @if($streamScoped)
                    <div class="form-text fs-7"><i class="fa-solid fa-lock me-1"></i> Scoped to your assigned stream(s).</div>
                @endif
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Select Subject</label>
                <select name="subject_id" class="form-select" onchange="this.form.submit()">
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}" {{ $selectedSubjectId == $subject->id ? 'selected' : '' }}>
                            {{ $subject->name }} ({{ $subject->code }}) &mdash; Sem {{ $subject->semester }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Class Section</label>
                <select name="section_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Sections{{ $selectedSemester ? ' — Sem '.$selectedSemester : '' }}</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ (string) $selectedSectionId === (string) $section->id ? 'selected' : '' }}>Section {{ $section->name }}</option>
                    @endforeach
                    <option value="unassigned" {{ $selectedSectionId === 'unassigned' ? 'selected' : '' }}>Unassigned Students</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Attendance Date</label>
                <input type="date" name="date" class="form-control" value="{{ $date }}" onchange="this.form.submit()">
            </div>
        </form>
    </div>
</div>

<div class="row g-4">
    <!-- Attendance Entry Form -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-clipboard-check text-primary me-2"></i> Attendance Sheet: {{ $date }}</h5>
                <span class="badge bg-primary fs-7">{{ $students->count() }} Students Listed</span>
            </div>
            <div class="card-body p-0">
                <form action="{{ route('attendance.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="subject_id" value="{{ $selectedSubjectId }}">
                    <input type="hidden" name="date" value="{{ $date }}">

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Roll No</th>
                                    <th>Enrollment No</th>
                                    <th>Student Name</th>
                                    <th>Current %</th>
                                    <th class="text-center">Status (Present / Absent / Late)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                @php
                                    $pct = $student->calculateAttendancePercentage();
                                    $status = $existingAttendance[$student->id] ?? 'present';
                                @endphp
                                <tr>
                                    <td class="fw-bold text-center">{{ $student->roll_no ?? '—' }}</td>
                                    <td class="font-monospace fw-bold fs-7">{{ $student->enrollment_no }}</td>
                                    <td>
                                        <div class="fw-bold">{{ optional($student->user)->name }}</div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $pct < 75 ? 'bg-danger' : 'bg-success' }} fs-7">
                                            {{ $pct }}%
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="pres_{{ $student->id }}" value="present" {{ $status === 'present' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-success btn-sm" for="pres_{{ $student->id }}"><i class="fa-solid fa-check me-1"></i> Present</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="abs_{{ $student->id }}" value="absent" {{ $status === 'absent' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-danger btn-sm" for="abs_{{ $student->id }}"><i class="fa-solid fa-xmark me-1"></i> Absent</label>

                                            <input type="radio" class="btn-check" name="attendance[{{ $student->id }}]" id="late_{{ $student->id }}" value="late" {{ $status === 'late' ? 'checked' : '' }}>
                                            <label class="btn btn-outline-warning btn-sm" for="late_{{ $student->id }}"><i class="fa-solid fa-clock me-1"></i> Late</label>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">No students found for this stream, semester & section.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($students->isNotEmpty())
                    <div class="p-3 bg-light text-end rounded-bottom-4">
                        <button type="submit" class="btn btn-primary fw-bold px-4">
                            <i class="fa-solid fa-floppy-disk me-1"></i> Submit & Save Daily Attendance
                        </button>
                    </div>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Low Attendance Alert Widget (< 75%) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-danger-subtle text-danger py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-triangle-exclamation me-2"></i> Low Attendance Report (&lt; 75%)</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($lowAttendanceList as $lowSt)
                    @php $lowPct = $lowSt->calculateAttendancePercentage(); @endphp
                    <div class="p-3 bg-light rounded-3 border border-danger-subtle">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="fw-bold text-dark">{{ optional($lowSt->user)->name }}</span>
                            <span class="badge bg-danger fs-7">{{ $lowPct }}%</span>
                        </div>
                        <div class="fs-7 text-muted">Enrollment: {{ $lowSt->enrollment_no }}</div>
                        <div class="fs-7 text-muted">Course: {{ optional($lowSt->course)->code }}</div>
                    </div>
                    @empty
                    <div class="text-center text-success py-4">
                        <i class="fa-solid fa-circle-check display-5 mb-2"></i>
                        <p class="mb-0 fw-semibold">All students are above the 75% attendance threshold!</p>
                    </div>
                    @endforelse
                </div>
                @if($lowAttendanceTotal > $lowAttendanceList->count())
                <div class="text-center text-muted fs-7 pt-3 mt-2 border-top">
                    Showing top {{ $lowAttendanceList->count() }} of {{ $lowAttendanceTotal }} students below threshold.
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
