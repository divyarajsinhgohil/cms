@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Faculty / Staff Portal</h3>
        <p class="text-muted mb-0">Welcome back, {{ Auth::user()->name }}! Manage classes, attendance, marks, and study materials.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('attendance.index') }}" class="btn btn-primary"><i class="fa-solid fa-clipboard-user me-1"></i> Mark Attendance</a>
        <a href="{{ route('materials.index') }}" class="btn btn-outline-primary"><i class="fa-solid fa-upload me-1"></i> Upload Material</a>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Assigned Department</div>
                    <div class="fs-4 fw-bold text-dark mt-1">{{ optional($staffProfile->department)->name ?? 'MCA Dept' }}</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-building-columns"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">Designation: {{ optional($staffProfile)->designation ?? 'Associate Professor' }}</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Department Students</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $totalStudentsInDept }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-2 text-success small"><i class="fa-solid fa-check me-1"></i> Under Academic Guidance</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Materials Published</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $materialsCount }}</div>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-file-arrow-up"></i>
                </div>
            </div>
            <div class="mt-2 text-info small">Notes & Assignment Tasks</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Today's Class Schedule -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock text-primary me-2"></i> Today's Class Timetable</h5>
                <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-link text-decoration-none">Full Timetable Grid &rarr;</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Time & Day</th>
                                <th>Course</th>
                                <th>Subject</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayTimetable as $slot)
                            <tr>
                                <td>
                                    <span class="badge bg-primary">{{ $slot->day_of_week }}</span>
                                    <div class="fw-bold fs-7 mt-1">{{ $slot->start_time }} - {{ $slot->end_time }}</div>
                                </td>
                                <td>{{ optional($slot->course)->code }} (Sem {{ $slot->semester }})</td>
                                <td class="fw-bold">{{ optional($slot->subject)->name }}</td>
                                <td><span class="badge {{ $slot->type === 'Lab' ? 'bg-info' : 'bg-primary' }}">{{ $slot->type }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No teaching slots scheduled for today.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Notice Board -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i> Faculty Notices</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($recentNotices as $notice)
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary text-uppercase fs-7">{{ $notice->priority }}</span>
                            <span class="text-muted fs-7">{{ $notice->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="fw-bold text-dark">{{ $notice->title }}</div>
                        <p class="text-secondary small mb-0">{{ Str::limit($notice->content, 80) }}</p>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No staff notices posted.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
