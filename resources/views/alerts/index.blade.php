@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h1 class="h3 fw-bold mb-1">Notifications</h1>
        <p class="text-muted mb-0">Queued alerts for attendance, fees, results, and notices.</p>
    </div>
    @if($alerts->where('status', 'sent')->count())
    <form action="{{ route('alerts.read_all') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-primary btn-sm">Mark all as read</button>
    </form>
    @endif
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="list-group list-group-flush">
        @forelse($alerts as $alert)
            <article class="list-group-item py-3 {{ $alert->status === 'sent' ? 'bg-primary-subtle' : '' }}">
                <div class="d-flex justify-content-between align-items-start gap-3">
                    <div>
                        <span class="badge bg-secondary text-uppercase mb-1">{{ $alert->type }}</span>
                        <h2 class="h6 fw-bold mb-1">{{ $alert->title }}</h2>
                        <p class="text-muted mb-1">{{ $alert->body }}</p>
                        <time class="small text-muted" datetime="{{ ($alert->sent_at ?? $alert->created_at)->toIso8601String() }}">
                            {{ ($alert->sent_at ?? $alert->created_at)->diffForHumans() }}
                        </time>
                    </div>
                    @if($alert->status === 'sent')
                    <form action="{{ route('alerts.read', $alert) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-light">Mark read</button>
                    </form>
                    @endif
                </div>
            </article>
        @empty
            <div class="list-group-item text-muted py-5 text-center">No notifications yet.</div>
        @endforelse
    </div>
    @if($alerts->hasPages())
        <div class="card-footer bg-white">{{ $alerts->links() }}</div>
    @endif
</div>
@endsection
