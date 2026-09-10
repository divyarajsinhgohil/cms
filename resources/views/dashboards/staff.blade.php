@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fade-down">
    <div>
        <h1 class="campus-page-heading mb-1">Faculty workspace</h1>
        <p class="text-muted mb-0">Your teaching day, thoughtfully organised.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('attendance.index') }}" class="btn btn-gradient-primary shadow-sm"><i class="fa-solid fa-clipboard-user me-1"></i> Mark Attendance</a>
        <a href="{{ route('materials.index') }}" class="btn btn-outline-primary shadow-sm"><i class="fa-solid fa-upload me-1"></i> Upload Material</a>
    </div>
</div>

<section class="campus-welcome" aria-label="Faculty welcome">
    <div class="campus-eyebrow text-primary">Inspiring minds. Shaping futures.</div>
    <h2>Welcome back, {{ Auth::user()->name }}.</h2>
    <p>Your classes, learning resources and campus updates are ready. Make room for what matters most: your students.</p>
</section>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card stat-card-primary animate-fade-up delay-50">
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
        <div class="stat-card stat-card-success animate-fade-up delay-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Department Students</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $totalStudentsInDept }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <div class="mt-2 text-success small fw-semibold"><i class="fa-solid fa-check me-1"></i> Under Academic Guidance</div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="stat-card stat-card-warning animate-fade-up delay-150">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Materials Published</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $materialsCount }}</div>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-file-arrow-up"></i>
                </div>
            </div>
            <div class="mt-2 text-info small fw-semibold">Notes & Assignment Tasks</div>
        </div>
    </div>
</div>

@include('components.campus-shortcuts', ['label' => 'Faculty quick access', 'links' => [
    ['route' => 'timetables.index', 'icon' => 'fa-calendar-days', 'title' => 'Your schedule', 'description' => 'Plan the teaching week'],
    ['route' => 'academics.my_syllabus', 'icon' => 'fa-book-open', 'title' => 'Syllabus & faculty', 'description' => 'Explore your curriculum'],
    ['route' => 'exams.index', 'icon' => 'fa-award', 'title' => 'Exams & results', 'description' => 'Manage academic progress'],
]])

<div class="row g-4">
    <!-- Today's Class Schedule -->
    <div class="col-lg-7">
        <div class="card card-hover-lift h-100 animate-fade-up delay-200">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock text-primary me-2"></i> Today's Class Timetable</h5>
                <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-outline-primary text-decoration-none">Full Timetable Grid &rarr;</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead>
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
        <div class="card card-hover-lift h-100 animate-fade-up delay-250">
            <div class="card-header py-3">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i> Faculty Notices</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($recentNotices as $notice)
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary text-uppercase fs-7">{{ $notice->priority }}</span>
                            <span class="text-muted fs-7"><i class="fa-regular fa-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="fw-bold text-dark mb-1">{{ $notice->title }}</div>
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
