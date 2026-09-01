@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Grievance & Complaint Redressal Portal</h3>
        <p class="text-muted mb-0">Submit academic, hostel, or facility complaints and track resolution progress live.</p>
    </div>
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newTicketModal">
        <i class="fa-solid fa-circle-plus me-1"></i> Submit New Grievance
    </button>
</div>

<!-- Complaints List -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Ticket ID</th>
                        <th>User / Student</th>
                        <th>Category</th>
                        <th>Complaint Title & Description</th>
                        <th>Date Submitted</th>
                        <th>Status</th>
                        @if(Auth::user()->isManagement())
                        <th class="text-end">Update Status</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                    <tr>
                        <td><span class="badge bg-dark font-monospace fs-7">#TCK-{{ sprintf('%04d', $c->id) }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ optional($c->user)->name }}</div>
                            <div class="text-muted fs-7">{{ optional($c->user)->email }}</div>
                        </td>
                        <td><span class="badge bg-info-subtle text-info border border-info-subtle">{{ $c->category }}</span></td>
                        <td>
                            <div class="fw-bold text-dark">{{ $c->title }}</div>
                            <div class="text-muted fs-7">{{ Str::limit($c->description, 60) }}</div>
                        </td>
                        <td>{{ $c->created_at->format('M d, Y') }}</td>
                        <td>
                            @if($c->status === 'Resolved')
                                <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-circle-check me-1"></i> Resolved</span>
                            @elseif($c->status === 'In Progress')
                                <span class="badge bg-warning-subtle text-dark border border-warning-subtle"><i class="fa-solid fa-spinner me-1"></i> In Progress</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fa-solid fa-clock me-1"></i> Open</span>
                            @endif
                        </td>
                        @if(Auth::user()->isManagement())
                        <td class="text-end">
                            <form action="{{ route('complaints.status', $c->id) }}" method="POST" class="d-inline-flex gap-1">
                                @csrf
                                @method('PUT')
                                <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="Open" {{ $c->status === 'Open' ? 'selected' : '' }}>Open</option>
                                    <option value="In Progress" {{ $c->status === 'In Progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="Resolved" {{ $c->status === 'Resolved' ? 'selected' : '' }}>Resolved</option>
                                </select>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">No grievances or complaints submitted yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal: New Ticket -->
<div class="modal fade" id="newTicketModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('complaints.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-headset text-primary me-2"></i> Submit Grievance Ticket</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Grievance Category</label>
                        <select name="category" class="form-select" required>
                            <option value="Academic">Academic & Exams</option>
                            <option value="Hostel">Hostel & Accommodation</option>
                            <option value="Mess">Mess & Food Quality</option>
                            <option value="IT & Infrastructure">IT & Infrastructure Support</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Complaint Title</label>
                        <input type="text" name="title" class="form-control" placeholder="Brief summary of the issue..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Detailed Description</label>
                        <textarea name="description" class="form-control" rows="4" placeholder="Explain the grievance details..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Submit Ticket</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
