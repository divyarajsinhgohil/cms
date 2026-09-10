@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Result Hard-Copy Collection Register</h3>
        <p class="text-muted mb-0">Track which students have physically collected their printed result, and remind the ones who haven't.</p>
    </div>
    <a href="{{ route('exams.index') }}" class="btn btn-outline-primary shadow-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Exam Marks
    </a>
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('exams.collection.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Select Course</label>
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ $selectedCourseId == $course->id ? 'selected' : '' }}>
                            {{ $course->name }}
                        </option>
                    @endforeach
                </select>
                @if($streamScoped)
                    <div class="form-text fs-7"><i class="fa-solid fa-lock me-1"></i> Scoped to your assigned stream(s).</div>
                @endif
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold">Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    @for($s = 1; $s <= 6; $s++)
                        <option value="{{ $s }}" {{ $semester == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Class Section</label>
                <select name="section_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Sections</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ (string) $selectedSectionId === (string) $section->id ? 'selected' : '' }}>Section {{ $section->name }}</option>
                    @endforeach
                    <option value="unassigned" {{ $selectedSectionId === 'unassigned' ? 'selected' : '' }}>Unassigned Students</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Search by Enrollment No. or Name</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control" placeholder="e.g. CMS-2026-MCA-00101 or student name" value="{{ $search }}">
                    <button type="submit" class="btn btn-primary">Search</button>
                </div>
                <div class="form-text fs-7">Enrollment number is unique — type it for a direct match, or just search by name across the class.</div>
            </div>
        </form>
    </div>
</div>

<!-- Summary -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="fa-solid fa-users"></i></div>
                <div><div class="fs-7 text-muted">Total Students</div><div class="fs-4 fw-bold">{{ $totalCount }}</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="fa-solid fa-circle-check"></i></div>
                <div><div class="fs-7 text-muted">Result Collected</div><div class="fs-4 fw-bold">{{ $collectedCount }}</div></div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-body d-flex align-items-center gap-3">
                <div class="rounded-circle bg-danger-subtle text-danger d-flex align-items-center justify-content-center" style="width:48px;height:48px;"><i class="fa-solid fa-triangle-exclamation"></i></div>
                <div><div class="fs-7 text-muted">Not Yet Collected</div><div class="fs-4 fw-bold">{{ $totalCount - $collectedCount }}</div></div>
            </div>
        </div>
    </div>
</div>

<!-- Mark Collection -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-clipboard-check text-primary me-2"></i> Mark Who Has Collected Their Result</h5>
        <span class="badge bg-primary fs-7">Semester {{ $semester }}</span>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('exams.collection.mark') }}" method="POST">
            @csrf
            <input type="hidden" name="course_id" value="{{ $selectedCourseId }}">
            <input type="hidden" name="semester" value="{{ $semester }}">
            <input type="hidden" name="section_id" value="{{ $selectedSectionId }}">
            <input type="hidden" name="search" value="{{ $search }}">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th></th>
                            <th>Roll No</th>
                            <th>Enrollment No</th>
                            <th>Student</th>
                            <th class="text-center">Collected?</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roster as $row)
                        @php $student = $row['student']; $collection = $row['collection']; @endphp
                        <tr>
                            <td>
                                <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="rounded-circle" width="36" height="36" style="object-fit: cover;">
                            </td>
                            <td class="fw-bold">{{ $student->roll_no ?? '—' }}</td>
                            <td class="font-monospace fs-7">{{ $student->enrollment_no }}</td>
                            <td class="fw-bold">{{ optional($student->user)->name }}</td>
                            <td class="text-center">
                                <div class="form-check form-switch d-flex justify-content-center">
                                    <input class="form-check-input" type="checkbox" role="switch" name="collected[{{ $student->id }}]" value="1" {{ optional($collection)->collected ? 'checked' : '' }}>
                                </div>
                                @if(optional($collection)->collected && $collection->collected_at)
                                    <div class="fs-8 text-muted">{{ $collection->collected_at->format('d M, h:i A') }}</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">No students found for this stream, semester & filter.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($roster->isNotEmpty())
            <div class="p-3 bg-light text-end rounded-bottom-4">
                <button type="submit" class="btn btn-primary fw-bold px-4">
                    <i class="fa-solid fa-floppy-disk me-1"></i> Save Collection Status
                </button>
            </div>
            @endif
        </form>
    </div>
</div>

<!-- Not Collected — Send Reminder -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-warning-subtle py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-bell text-warning me-2"></i> Not Collected — Select Students to Remind</h5>
        <span class="badge bg-danger fs-7">{{ $pendingList->count() }} Pending</span>
    </div>
    <div class="card-body p-0">
        @if($pendingList->isEmpty())
            <div class="text-center text-success py-4">
                <i class="fa-solid fa-circle-check display-6 mb-2"></i>
                <p class="mb-0 fw-semibold">Everyone in this list has collected their result!</p>
            </div>
        @else
        <form action="{{ route('exams.collection.remind') }}" method="POST">
            @csrf
            <input type="hidden" name="semester" value="{{ $semester }}">

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th><input type="checkbox" class="form-check-input" onclick="document.querySelectorAll('.remind-check').forEach(c => c.checked = this.checked)"></th>
                            <th></th>
                            <th>Enrollment No</th>
                            <th>Student</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($pendingList as $row)
                        @php $student = $row['student']; @endphp
                        <tr>
                            <td><input type="checkbox" class="form-check-input remind-check" name="student_ids[]" value="{{ $student->id }}"></td>
                            <td>
                                <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="rounded-circle" width="36" height="36" style="object-fit: cover;">
                            </td>
                            <td class="font-monospace fs-7">{{ $student->enrollment_no }}</td>
                            <td class="fw-bold">{{ optional($student->user)->name }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-3 bg-light text-end rounded-bottom-4">
                <div class="alert alert-warning py-2 fs-7 mb-3 text-start" role="status">
                    <i class="fa-solid fa-circle-info me-1"></i> Sends an in-app notification now (visible under Notifications). SMS delivery isn't wired up yet — it can be added to this same reminder later.
                </div>
                <button type="submit" class="btn btn-warning fw-bold px-4 text-dark">
                    <i class="fa-solid fa-paper-plane me-1"></i> Send Reminder to Selected
                </button>
            </div>
        </form>
        @endif
    </div>
</div>
@endsection
