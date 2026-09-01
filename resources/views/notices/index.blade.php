@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Central Notice Board & Announcements</h3>
        <p class="text-muted mb-0">Official announcements broadcasted to Students, Staff, or Management.</p>
    </div>
    @if(Auth::user()->isManagement())
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newNoticeModal">
        <i class="fa-solid fa-bullhorn me-1"></i> Post New Notice
    </button>
    @endif
</div>

<div class="row g-4">
    @forelse($notices as $notice)
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 border-start border-5 {{ $notice->priority === 'urgent' ? 'border-danger' : 'border-primary' }}">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="d-flex gap-2">
                        <span class="badge {{ $notice->priority === 'urgent' ? 'bg-danger' : 'bg-primary' }} text-uppercase fs-7">
                            <i class="fa-solid {{ $notice->priority === 'urgent' ? 'fa-triangle-exclamation' : 'fa-info-circle' }} me-1"></i> {{ $notice->priority }}
                        </span>
                        <span class="badge bg-light text-dark border text-uppercase fs-7">
                            Target: {{ $notice->target_audience }}
                        </span>
                        @if($notice->course)
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle fs-7">
                            {{ $notice->course->code }}{{ $notice->semester ? ' · Sem '.$notice->semester : '' }}
                        </span>
                        @elseif($notice->semester)
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle fs-7">
                            Sem {{ $notice->semester }}
                        </span>
                        @endif
                    </div>
                    <span class="text-muted fs-7"><i class="fa-regular fa-clock me-1"></i> {{ $notice->created_at->format('M d, Y H:i') }}</span>
                </div>

                <h4 class="fw-bold text-dark mb-2">{{ $notice->title }}</h4>
                <p class="text-secondary leading-relaxed mb-3">{!! nl2br(e($notice->content)) !!}</p>

                <div class="border-top pt-3 d-flex justify-content-between align-items-center text-muted fs-7">
                    <span><i class="fa-solid fa-user-shield me-1"></i> Posted by: <strong>{{ optional($notice->author)->name ?? 'College Administration' }}</strong></span>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center text-muted py-5">
        <i class="fa-solid fa-bullhorn display-4 mb-2 text-warning"></i>
        <p class="mb-0 fs-5 fw-semibold">No notices published on the notice board.</p>
    </div>
    @endforelse
</div>

@if($notices->hasPages())
<div class="d-flex justify-content-between align-items-center mt-4">
    <span class="text-muted fs-7">Showing {{ $notices->firstItem() }}-{{ $notices->lastItem() }} of {{ $notices->total() }} notices</span>
    {{ $notices->links() }}
</div>
@endif

<!-- Modal: New Notice -->
<div class="modal fade" id="newNoticeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('notices.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-bullhorn text-primary me-2"></i> Post Notice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notice Headline / Title</label>
                        <input type="text" name="title" class="form-control" placeholder="e.g. Mid-Semester Exam Schedule 2026" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Target Audience</label>
                        <select name="target_audience" id="noticeAudience" class="form-select" onchange="document.getElementById('noticeStreamTargeting').classList.toggle('d-none', this.value !== 'students')">
                            <option value="all">Everyone (Students & Staff)</option>
                            <option value="students">Students Only</option>
                            <option value="staff">Faculty & Staff Only</option>
                        </select>
                    </div>
                    <div id="noticeStreamTargeting" class="row g-2 mb-3 d-none">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Limit to Stream (optional)</label>
                            <select name="course_id" class="form-select">
                                <option value="">All Streams</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-muted">Limit to Semester (optional)</label>
                            <select name="semester" class="form-select">
                                <option value="">All Semesters</option>
                                @for($s = 1; $s <= 8; $s++)
                                    <option value="{{ $s }}">Semester {{ $s }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="form-text fs-7">Leave both as "All" to notify every student. Only used when audience is Students Only.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Priority Badge</label>
                        <select name="priority" class="form-select">
                            <option value="normal">Normal Announcement</option>
                            <option value="urgent">Urgent / Important Alert</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Notice Announcement Body</label>
                        <textarea name="content" class="form-control" rows="4" placeholder="Write announcement details..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Broadcast Notice</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
