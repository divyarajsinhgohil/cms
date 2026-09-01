@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Executive Management Dashboard</h3>
        <p class="text-muted mb-0">Real-time college operations, admissions, and financial analytics.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admission-applications.index') }}" class="btn btn-primary"><i class="fa-solid fa-file-signature me-1"></i> Admission Applications</a>
        <a href="{{ route('marketing.index') }}" class="btn btn-outline-success"><i class="fa-brands fa-whatsapp me-1"></i> Launch Outreach</a>
    </div>
</div>

<!-- Key Stat Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Total Students</div>
                    <div class="fs-2 fw-bold text-dark mt-1">{{ $totalStudents }}</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
            </div>
            <div class="mt-2 text-success small fw-semibold">
                <i class="fa-solid fa-arrow-up me-1"></i> Active Enrollments
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
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
                Awaiting your review
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Fees Collected</div>
                    <div class="fs-2 fw-bold text-success mt-1">₹{{ number_format($totalFeesCollected, 2) }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="mt-2 text-success small">
                <i class="fa-solid fa-check-double me-1"></i> Realized Receipts
            </div>
        </div>
    </div>

    <div class="col-md-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Pending Dues</div>
                    <div class="fs-2 fw-bold text-danger mt-1">₹{{ number_format($pendingDues, 2) }}</div>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fa-solid fa-hand-holding-dollar"></i>
                </div>
            </div>
            <div class="mt-2 text-danger small">
                <i class="fa-solid fa-clock me-1"></i> Outstanding Balance
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Admission Applications -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-file-signature text-primary me-2"></i> Recent Admission Applications</h5>
                <a href="{{ route('admission-applications.index') }}" class="btn btn-sm btn-link text-decoration-none">View All &rarr;</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
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
                                <div class="fw-bold">{{ $app->fullName() }}</div>
                                <div class="text-muted fs-7">{{ $app->category }}</div>
                            </td>
                            <td>{{ optional($app->course)->name ?? 'N/A' }}</td>
                            <td>{{ $app->mobile }}</td>
                            <td>
                                @if($app->status === 'Pending')
                                    <span class="badge bg-warning text-dark">Pending</span>
                                @elseif($app->status === 'Approved')
                                    <span class="badge bg-success">Approved</span>
                                @else
                                    <span class="badge bg-danger">Rejected</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admission-applications.index') }}" class="btn btn-sm btn-outline-primary">Review</a>
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
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i> Notice Board</h5>
                <a href="{{ route('notices.index') }}" class="btn btn-sm btn-link text-decoration-none">Post Notice &rarr;</a>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($recentNotices as $notice)
                    <div class="p-3 bg-light rounded-3 border-start border-4 {{ $notice->priority === 'urgent' ? 'border-danger' : 'border-primary' }}">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge {{ $notice->priority === 'urgent' ? 'bg-danger' : 'bg-primary' }} text-uppercase fs-7">{{ $notice->priority }}</span>
                            <span class="text-muted fs-7">{{ $notice->created_at->diffForHumans() }}</span>
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
