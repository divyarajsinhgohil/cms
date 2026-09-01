@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">{{ $ownOnly ? 'My Certificates' : 'Certificates & ID Cards' }}</h3>
        <p class="text-muted mb-0">
            @if($ownOnly)
                Print or download your own Student ID Card and Bonafide Certificate.
            @else
                Generate printable or downloadable Student ID Cards and Bonafide Certificates.
            @endif
        </p>
    </div>
</div>

@unless($ownOnly)
<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('certificates.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">Stream / Course</label>
                <select name="course_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ $streamScoped ? 'All My Assigned Streams' : 'All Streams & Courses' }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
                @if($streamScoped)
                    <div class="form-text fs-7"><i class="fa-solid fa-lock me-1"></i> Scoped to your assigned stream(s).</div>
                @endif
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Search Name / Enrollment</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>
            <div class="col-md-2">
                @if(request()->hasAny(['course_id', 'semester', 'search']))
                    <a href="{{ route('certificates.index') }}" class="btn btn-outline-danger w-100"><i class="fa-solid fa-rotate-left me-1"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>
@endunless

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Enrollment No</th>
                        <th>Student Name</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th>Certificates</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle px-2 py-1 fs-7">
                                {{ $student->enrollment_no }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="rounded-circle" width="36" height="36">
                                <div>
                                    <div class="fw-bold text-dark">{{ optional($student->user)->name }}</div>
                                    <div class="text-muted fs-7">{{ optional($student->user)->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-primary fs-7">{{ optional($student->course)->code }}</span>
                            <div class="fs-7 text-muted">{{ optional($student->course)->name }}</div>
                        </td>
                        <td><span class="badge bg-light text-dark border">{{ optional($student->department)->name }}</span></td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning-subtle">Sem {{ $student->current_semester }}</span></td>
                        <td>
                            <div class="d-flex flex-wrap gap-1">
                                <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-dark" title="Print Student ID Card">
                                    <i class="fa-solid fa-id-card"></i> ID Card
                                </a>
                                <a href="{{ route('certificates.id_card.pdf', $student->id) }}" class="btn btn-sm btn-dark" title="Download ID Card PDF">
                                    <i class="fa-solid fa-file-arrow-down"></i> PDF
                                </a>
                                <a href="{{ route('certificates.bonafide', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Print Bonafide Certificate">
                                    <i class="fa-solid fa-certificate"></i> Bonafide
                                </a>
                                <a href="{{ route('certificates.bonafide.pdf', $student->id) }}" class="btn btn-sm btn-info text-white" title="Download Bonafide PDF">
                                    <i class="fa-solid fa-file-arrow-down"></i> PDF
                                </a>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-user-slash display-5 mb-2 text-secondary"></i>
                            <p class="mb-0 fw-semibold">No students found yet.</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($students->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted fs-7">Showing {{ $students->firstItem() }}-{{ $students->lastItem() }} of {{ $students->total() }} students</span>
            {{ $students->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
