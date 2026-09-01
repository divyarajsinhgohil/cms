@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Staff & Student Leave Management</h3>
        <p class="text-muted mb-0">Apply for leave, track approval workflow, and manage leave balances.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#applyLeaveModal">
        <i class="fa-solid fa-calendar-plus me-1"></i> Apply for Leave
    </button>
</div>

<!-- Leave Requests Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Applicant</th>
                        <th>Leave Type</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Reason</th>
                        <th>Status</th>
                        @if(Auth::user()->isManagement())
                        <th class="text-end">Approve / Reject</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($leaveRequests as $req)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ optional($req->user)->name }}</div>
                            <div class="text-muted fs-7">{{ ucfirst(optional($req->user)->role) }}</div>
                        </td>
                        <td><span class="badge bg-primary-subtle text-primary border border-primary-subtle">{{ $req->leave_type }}</span></td>
                        <td>{{ $req->start_date }}</td>
                        <td>{{ $req->end_date }}</td>
                        <td>{{ Str::limit($req->reason, 40) }}</td>
                        <td>
                            @if($req->status === 'Approved')
                                <span class="badge bg-success"><i class="fa-solid fa-check me-1"></i> Approved</span>
                            @elseif($req->status === 'Rejected')
                                <span class="badge bg-danger"><i class="fa-solid fa-xmark me-1"></i> Rejected</span>
                            @else
                                <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i> Pending</span>
                            @endif
                        </td>
                        @if(Auth::user()->isManagement())
                        <td class="text-end">
                            <form action="{{ route('leaves.status', $req->id) }}" method="POST" class="d-inline-flex gap-1">
                                @csrf
                                @method('PUT')
                                <button type="submit" name="status" value="Approved" class="btn btn-sm btn-success">Approve</button>
                                <button type="submit" name="status" value="Rejected" class="btn btn-sm btn-outline-danger">Reject</button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No leave applications submitted.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: Apply Leave -->
<div class="modal fade" id="applyLeaveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('leaves.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-calendar-plus text-primary me-2"></i> Leave Application Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Leave Type</label>
                        <select name="leave_type" class="form-select" required>
                            <option value="Casual Leave">Casual Leave (CL)</option>
                            <option value="Sick Leave">Sick / Medical Leave (SL)</option>
                            <option value="Academic Leave">Academic / Duty Leave</option>
                        </select>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Start Date</label>
                            <input type="date" name="start_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">End Date</label>
                            <input type="date" name="end_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Reason for Leave</label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="State reason for leave..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Submit Application</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
