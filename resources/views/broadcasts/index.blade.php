@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h3 class="fw-bold mb-1"><i class="fa-solid fa-tower-broadcast text-warning me-2"></i>Real-Time Live Broadcasts</h3>
            <p class="text-secondary mb-0">Broadcast instant live popup messages to all active users without page reloads.</p>
        </div>
        <button type="button" class="btn btn-warning text-dark fw-bold px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#cmsAdminBroadcastModal">
            <i class="fa-solid fa-plus me-1"></i> Send Live Broadcast
        </button>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white py-3 border-0 d-flex align-items-center justify-content-between">
            <h5 class="mb-0 fw-bold"><i class="fa-solid fa-clock-rotate-left me-2 text-primary"></i>Broadcast History</h5>
            <span class="badge bg-primary rounded-pill px-3 py-2 fs-7">{{ $broadcasts->total() }} Total Messages</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4">Title & Details</th>
                        <th>Target Audience</th>
                        <th>Alert Format</th>
                        <th>Sender</th>
                        <th>Status</th>
                        <th>Sent At</th>
                        <th class="text-end pe-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($broadcasts as $broadcast)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark fs-6">{{ $broadcast->title }}</div>
                            <div class="text-muted small text-truncate" style="max-width: 320px;">{{ $broadcast->message }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary text-capitalize px-2 py-1 fs-7">
                                <i class="fa-solid fa-users me-1"></i> {{ $broadcast->target_role }}
                            </span>
                        </td>
                        <td>
                            @php
                                $typeBadge = [
                                    'info' => 'bg-info text-dark',
                                    'success' => 'bg-success',
                                    'warning' => 'bg-warning text-dark',
                                    'danger' => 'bg-danger',
                                ][$broadcast->type] ?? 'bg-primary';
                            @endphp
                            <span class="badge {{ $typeBadge }} text-uppercase px-2 py-1 fs-7">
                                {{ $broadcast->display_format }} ({{ $broadcast->type }})
                            </span>
                        </td>
                        <td>
                            <div class="fw-semibold small"><i class="fa-solid fa-user-shield me-1 text-primary"></i>{{ $broadcast->sender->name ?? 'Admin' }}</div>
                        </td>
                        <td>
                            @if($broadcast->is_active && (!$broadcast->expires_at || $broadcast->expires_at->isFuture()))
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1">
                                    <i class="fa-solid fa-signal me-1"></i> Active
                                </span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill px-3 py-1">
                                    Inactive / Expired
                                </span>
                            @endif
                        </td>
                        <td class="text-secondary fs-7">
                            {{ $broadcast->created_at->format('M d, Y H:i A') }}
                            <div class="fs-8 text-muted">{{ $broadcast->created_at->diffForHumans() }}</div>
                        </td>
                        <td class="text-end pe-4">
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <form action="{{ route('broadcasts.toggle', $broadcast) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $broadcast->is_active ? 'btn-outline-secondary' : 'btn-outline-success' }}" title="Toggle Status">
                                        <i class="fa-solid {{ $broadcast->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                    </button>
                                </form>

                                <form action="{{ route('broadcasts.destroy', $broadcast) }}" method="POST" data-confirm="Are you sure you want to delete this broadcast message?">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Broadcast">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="fa-solid fa-tower-broadcast fs-1 text-black-50 mb-3 d-block"></i>
                            <h5>No Real-Time Broadcasts Sent Yet</h5>
                            <p class="small mb-3">Click the "Send Live Broadcast" button to send an instant message to active users.</p>
                            <button type="button" class="btn btn-warning text-dark fw-bold px-4 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#cmsAdminBroadcastModal">
                                Send First Live Broadcast
                            </button>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($broadcasts->hasPages())
        <div class="card-footer bg-white py-3 border-0">
            {{ $broadcasts->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
