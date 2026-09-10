@extends('layouts.app')

@section('content')
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fade-down">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <h1 class="campus-page-heading mb-0">Campus overview</h1>
            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><span class="status-dot status-dot-pulse bg-primary me-1"></span> Live Data</span>
        </div>
        <p class="text-muted mb-0">A clear view of your students, admissions and campus operations.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admission-applications.index') }}" class="btn btn-gradient-primary shadow-sm"><i class="fa-solid fa-file-signature me-1"></i> Admission Applications</a>
        <a href="{{ route('marketing.index') }}" class="btn btn-outline-success shadow-sm"><i class="fa-brands fa-whatsapp me-1"></i> Launch Outreach</a>
    </div>
</div>

<section class="campus-welcome" aria-label="Welcome">
    <div class="campus-eyebrow text-primary">Every detail. One workspace.</div>
    <h2>Welcome back, {{ Auth::user()->name }}.</h2>
    <p>Keep your campus moving forward. Review applications, support your students, and stay on top of the day.</p>
</section>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-primary animate-fade-up delay-50">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Students</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $totalStudents }}</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <div class="mt-2 text-success small fw-semibold d-flex align-items-center gap-1">
                <span class="status-dot status-dot-pulse bg-success"></span>
                <i class="fa-solid fa-arrow-up me-1"></i> Active Enrollments
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-warning animate-fade-up delay-100">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Pending Applications</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $pendingApplications }}</div>
                </div>
                <div class="stat-icon bg-info-subtle text-info">
                    <i class="fa-solid fa-file-signature"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">
                <i class="fa-regular fa-clock me-1"></i> Awaiting review
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-success animate-fade-up delay-150">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Fees Collected</div>
                    <div class="fs-2 fw-bold text-success mt-1">₹{{ number_format($totalFeesCollected, 2) }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="mt-2 text-success small fw-semibold">
                <i class="fa-solid fa-check-double me-1"></i> Realized Receipts
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card stat-card-danger animate-fade-up delay-200">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Pending Dues</div>
                    <div class="fs-2 fw-bold text-danger mt-1">₹{{ number_format($pendingDues, 2) }}</div>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="mt-2 text-danger small fw-semibold">
                <i class="fa-solid fa-clock me-1"></i> Outstanding Balance
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Admission Applications -->
    <div class="col-lg-8">
        <div class="card card-hover-lift h-100 animate-fade-up delay-250">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-file-signature text-primary me-2"></i> Recent Admission Applications</h5>
                <a href="{{ route('admission-applications.index') }}" class="btn btn-sm btn-outline-primary text-decoration-none">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Applicant</th>
                            <th>Stream</th>
                            <th>Contact</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentApplications as $app)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $app->fullName() }}</div>
                                <div class="text-muted fs-7">{{ $app->category }}</div>
                            </td>
                            <td><span class="badge bg-light text-dark border">{{ optional($app->course)->name ?? 'N/A' }}</span></td>
                            <td><span class="text-muted small"><i class="fa-solid fa-phone me-1 fs-8"></i>{{ $app->mobile }}</span></td>
                            <td>
                                @if($app->status === 'Pending')
                                    <span class="badge bg-warning text-dark"><span class="status-dot bg-dark me-1"></span> Pending</span>
                                @elseif($app->status === 'Approved')
                                    <span class="badge bg-success"><span class="status-dot bg-white me-1"></span> Approved</span>
                                @else
                                    <span class="badge bg-danger"><span class="status-dot bg-white me-1"></span> Rejected</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admission-applications.index') }}" class="btn btn-sm btn-outline-primary shadow-xs">Review</a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No admission applications yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Active College Notices -->
    <div class="col-lg-4">
        <div class="card card-hover-lift h-100 animate-fade-up delay-300">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i> Notice Board</h5>
                <a href="{{ route('notices.index') }}" class="btn btn-sm btn-outline-primary text-decoration-none">Post Notice &rarr;</a>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($recentNotices as $notice)
                    <div class="p-3 bg-light rounded-3 border-start border-4 {{ $notice->priority === 'urgent' ? 'border-danger' : 'border-primary' }} transition-all">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge {{ $notice->priority === 'urgent' ? 'bg-danger' : 'bg-primary' }} text-uppercase fs-7">{{ $notice->priority }}</span>
                            <span class="text-muted fs-7"><i class="fa-regular fa-clock me-1"></i>{{ $notice->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="fw-bold text-dark mb-1">{{ $notice->title }}</div>
                        <p class="text-secondary small mb-0">{{ Str::limit($notice->content, 90) }}</p>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4 mb-0">No active notices.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
