@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between mb-4 gap-3">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-brands fa-whatsapp text-success me-2"></i>WhatsApp Transmission Logs</h3>
            <p class="text-secondary mb-0">Detailed history of all sent, automated, and failed WhatsApp messages.</p>
        </div>
        <a href="{{ route('whatsapp.index') }}" class="btn btn-outline-primary rounded-pill px-4 fw-semibold">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to WhatsApp Hub
        </a>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('whatsapp.logs') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-5">
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0" placeholder="Search phone number, recipient name, or message..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-4">
                    <select name="type" class="form-select">
                        <option value="">-- All Message Types --</option>
                        <option value="direct" {{ request('type') == 'direct' ? 'selected' : '' }}>Direct Instant Message</option>
                        <option value="fee_receipt" {{ request('type') == 'fee_receipt' ? 'selected' : '' }}>Fee Receipt</option>
                        <option value="attendance_alert" {{ request('type') == 'attendance_alert' ? 'selected' : '' }}>Attendance Alert</option>
                        <option value="exam_result" {{ request('type') == 'exam_result' ? 'selected' : '' }}>Exam Result Alert</option>
                        <option value="admission_approval" {{ request('type') == 'admission_approval' ? 'selected' : '' }}>Admission Approval</option>
                        <option value="campaign" {{ request('type') == 'campaign' ? 'selected' : '' }}>Bulk Campaign</option>
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold rounded-pill w-100">Filter</button>
                    @if(request()->hasAny(['search', 'type']))
                        <a href="{{ route('whatsapp.logs') }}" class="btn btn-light rounded-pill">Reset</a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Recipient</th>
                        <th>Phone Number</th>
                        <th>Type</th>
                        <th>Message Preview</th>
                        <th>Status</th>
                        <th>Dispatched At</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                    <tr>
                        <td class="ps-4 fw-bold text-dark">
                            <i class="fa-solid fa-user text-secondary me-1"></i> {{ $log->recipient_name ?? 'N/A' }}
                        </td>
                        <td class="fw-semibold text-primary">
                            {{ $log->recipient_phone }}
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize px-2 py-1 fs-7">
                                {{ str_replace('_', ' ', $log->type) }}
                            </span>
                        </td>
                        <td>
                            <div class="text-dark small text-truncate" style="max-width: 380px;" title="{{ $log->message }}">
                                {{ $log->message }}
                            </div>
                            @if($log->error_details)
                                <div class="text-danger fs-8 text-truncate" style="max-width: 380px;">
                                    <i class="fa-solid fa-circle-exclamation me-1"></i> {{ $log->error_details }}
                                </div>
                            @endif
                        </td>
                        <td>
                            @if($log->status === 'Delivered')
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                    <i class="fa-solid fa-circle-check me-1"></i> Delivered
                                </span>
                            @else
                                <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill px-3 py-1">
                                    <i class="fa-solid fa-circle-xmark me-1"></i> Failed
                                </span>
                            @endif
                        </td>
                        <td class="text-secondary fs-7">
                            {{ $log->created_at->format('M d, Y H:i A') }}
                            <div class="fs-8 text-muted">{{ $log->created_at->diffForHumans() }}</div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="fa-brands fa-whatsapp fs-1 text-black-50 mb-2"></i>
                            <h5 class="fw-semibold">No WhatsApp logs found</h5>
                            <p class="small mb-0">Try clearing your search query or filters.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($logs->hasPages())
        <div class="card-footer bg-white py-3 border-0">
            {{ $logs->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
