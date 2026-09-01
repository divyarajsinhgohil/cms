@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Online Admission Applications</h3>
        <p class="text-muted mb-0">Review applications submitted through the public admission form and approve or reject them.</p>
    </div>
    <a href="{{ route('admission-applications.create') }}" target="_blank" class="btn btn-outline-primary shadow-sm">
        <i class="fa-solid fa-up-right-from-square me-1"></i> View Public Form
    </a>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('admission-applications.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">All (Pending First)</option>
                    <option value="Pending" {{ request('status') === 'Pending' ? 'selected' : '' }}>Pending</option>
                    <option value="Approved" {{ request('status') === 'Approved' ? 'selected' : '' }}>Approved</option>
                    <option value="Rejected" {{ request('status') === 'Rejected' ? 'selected' : '' }}>Rejected</option>
                </select>
            </div>
            <div class="col-md-2">
                @if(request('status'))
                    <a href="{{ route('admission-applications.index') }}" class="btn btn-outline-danger w-100"><i class="fa-solid fa-rotate-left me-1"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Applicant</th>
                        <th>Stream Applied</th>
                        <th>Category</th>
                        <th>Contact</th>
                        <th>Hostel</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($applications as $app)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ asset('storage/' . $app->photo_path) }}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark">{{ $app->fullName() }}</div>
                                    <div class="text-muted fs-7">Aadhar: {{ $app->aadhar_number }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary fs-7">{{ optional($app->course)->code }}</span>
                            <div class="fs-7 text-muted">{{ optional($app->course)->name }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ $app->category }}</span></td>
                        <td>
                            <div class="fs-7 font-monospace">{{ $app->mobile }}</div>
                            <div class="fs-7 text-muted">{{ $app->email }}</div>
                        </td>
                        <td>
                            @if($app->wants_hostel)
                                <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $app->hostel_preference ?: 'Required' }}</span>
                            @else
                                <span class="text-muted fs-7">Not required</span>
                            @endif
                        </td>
                        <td>
                            @if($app->status === 'Pending')
                                <span class="badge bg-warning text-dark">Pending</span>
                            @elseif($app->status === 'Approved')
                                <span class="badge bg-success">Approved</span>
                                @if($app->student)
                                    <div class="fs-7 text-muted font-monospace">{{ $app->student->enrollment_no }}</div>
                                @endif
                            @else
                                <span class="badge bg-danger">Rejected</span>
                            @endif
                        </td>
                        <td class="text-end">
                            @if($app->status === 'Pending')
                            <div class="d-flex gap-1 justify-content-end">
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewAppModal{{ $app->id }}">
                                    <i class="fa-solid fa-eye"></i> View
                                </button>
                                <form action="{{ route('admission-applications.approve', $app->id) }}" method="POST" data-confirm="Approve {{ $app->fullName() }} and enroll them into {{ optional($app->course)->name }}?">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success"><i class="fa-solid fa-check"></i> Approve</button>
                                </form>
                                <button class="btn btn-sm btn-outline-danger" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $app->id }}">
                                    <i class="fa-solid fa-xmark"></i> Reject
                                </button>
                            </div>
                            @else
                                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#viewAppModal{{ $app->id }}">
                                    <i class="fa-solid fa-eye"></i> View
                                </button>
                            @endif
                        </td>
                    </tr>

                    <!-- View Application Modal -->
                    <div class="modal fade" id="viewAppModal{{ $app->id }}" tabindex="-1">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title fw-bold">Application: {{ $app->fullName() }}</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row g-3">
                                        <div class="col-md-3 text-center">
                                            <img src="{{ asset('storage/' . $app->photo_path) }}" class="rounded border w-100" style="aspect-ratio: 1; object-fit: cover;">
                                        </div>
                                        <div class="col-md-9">
                                            <div class="row g-2 fs-7">
                                                <div class="col-6"><strong>Father's Name:</strong> {{ $app->father_name }}</div>
                                                <div class="col-6"><strong>Mother's Name:</strong> {{ $app->mother_name }}</div>
                                                <div class="col-6"><strong>Gender:</strong> {{ $app->gender }}</div>
                                                <div class="col-6"><strong>Category:</strong> {{ $app->category }}</div>
                                                <div class="col-6"><strong>Mobile:</strong> {{ $app->mobile }}</div>
                                                <div class="col-6"><strong>Email:</strong> {{ $app->email }}</div>
                                                <div class="col-6"><strong>Father's Mobile:</strong> {{ $app->father_mobile ?: 'N/A' }}</div>
                                                <div class="col-6"><strong>Mother's Mobile:</strong> {{ $app->mother_mobile ?: 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="col-12"><hr></div>
                                        <div class="col-md-6 fs-7"><strong>Home Address:</strong><br>{{ $app->home_address }}</div>
                                        <div class="col-md-6 fs-7"><strong>Permanent Address:</strong><br>{{ $app->permanent_address }}</div>
                                        <div class="col-12"><hr></div>
                                        <div class="col-12 d-flex gap-2 flex-wrap">
                                            <a href="{{ asset('storage/' . $app->aadhar_photo_path) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-file-arrow-down me-1"></i> Aadhar Card</a>
                                            @if($app->caste_certificate_path)
                                                <a href="{{ asset('storage/' . $app->caste_certificate_path) }}" target="_blank" class="btn btn-sm btn-outline-dark"><i class="fa-solid fa-file-arrow-down me-1"></i> Caste Certificate</a>
                                            @endif
                                        </div>
                                        @if($app->status === 'Rejected' && $app->rejection_reason)
                                        <div class="col-12">
                                            <div class="alert alert-danger fs-7 mb-0"><strong>Rejection Reason:</strong> {{ $app->rejection_reason }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Reject Modal -->
                    @if($app->status === 'Pending')
                    <div class="modal fade" id="rejectModal{{ $app->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form action="{{ route('admission-applications.reject', $app->id) }}" method="POST">
                                    @csrf
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Reject Application: {{ $app->fullName() }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <label class="form-label fw-bold">Reason for Rejection</label>
                                        <textarea name="rejection_reason" class="form-control" rows="3" required placeholder="e.g. Incomplete documents, seats full..."></textarea>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-danger fw-bold">Reject Application</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endif
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox display-5 mb-2 text-secondary"></i>
                            <p class="mb-0 fw-semibold">No admission applications yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted fs-7">Showing {{ $applications->firstItem() }}-{{ $applications->lastItem() }} of {{ $applications->total() }} applications</span>
            {{ $applications->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
