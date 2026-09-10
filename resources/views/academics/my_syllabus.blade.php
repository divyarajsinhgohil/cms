@extends('layouts.app')

@section('content')
<div class="container-fluid px-0">
    <!-- Header Banner -->
    <div class="bg-white p-4 rounded-4 shadow-sm border mb-4 d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary text-uppercase px-3 py-1 fs-7">{{ $selectedCourse->code ?? 'Course' }}</span>
                <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-3 py-1 fs-7">Semester {{ $selectedSemester }}</span>
            </div>
            <h3 class="fw-bold text-dark mb-1">{{ $selectedCourse->name ?? 'Academic Program' }}</h3>
            <div class="text-secondary small">
                <i class="fa-solid fa-building-columns me-1"></i> Department of {{ $selectedCourse->department->name ?? 'Academics' }}
            </div>
        </div>

        @if(!Auth::user()->isStudent())
        <!-- Stream & Semester Selector for Faculty/Management -->
        <form action="{{ route('academics.my_syllabus') }}" method="GET" class="d-flex align-items-center gap-2">
            <select name="course_id" class="form-select rounded-3 fs-7" onchange="this.form.submit()">
                @foreach($courses as $c)
                    <option value="{{ $c->id }}" {{ $selectedCourseId == $c->id ? 'selected' : '' }}>
                        {{ $c->name }} ({{ $c->code }})
                    </option>
                @endforeach
            </select>
            <select name="semester" class="form-select rounded-3 fs-7" onchange="this.form.submit()" style="width: 130px;">
                @for($s = 1; $s <= 8; $s++)
                    <option value="{{ $s }}" {{ $selectedSemester == $s ? 'selected' : '' }}>Sem {{ $s }}</option>
                @endfor
            </select>
        </form>
        @else
        <div class="text-md-end">
            <div class="fw-bold text-dark fs-6">{{ Auth::user()->name }}</div>
            <div class="text-muted fs-7">Enrollment: {{ optional(Auth::user()->studentProfile)->enrollment_no }}</div>
        </div>
        @endif
    </div>

    <!-- Official Default Semester Syllabus PDF Card -->
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, #6d3fa0 0%, #2e5fa3 100%);">
        <div class="card-body p-4 text-white">
            <div class="d-flex flex-column flex-md-row align-items-md-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary d-flex align-items-center justify-content-center shadow-sm" style="width: 56px; height: 56px; font-size: 1.8rem;">
                        <i class="fa-solid fa-file-pdf text-danger"></i>
                    </div>
                    <div>
                        <div class="text-white-50 text-uppercase fw-bold fs-7 tracking-wider">Official Curriculum Document</div>
                        <h4 class="fw-bold mb-1">{{ $syllabus->title ?? ($selectedCourse->code . ' Semester ' . $selectedSemester . ' Default Syllabus PDF') }}</h4>
                        <div class="text-white-50 fs-7">
                            @if($syllabus)
                                Uploaded {{ $syllabus->created_at->diffForHumans() }} &bull; Updated for Academic Year {{ date('Y') }}
                            @else
                                Official semester syllabus PDF document for {{ $selectedCourse->code ?? 'this course' }}
                            @endif
                        </div>
                    </div>
                </div>

                <div>
                    @if($syllabus && $syllabus->syllabus_pdf_path)
                        <a href="{{ Storage::url($syllabus->syllabus_pdf_path) }}" target="_blank" class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm fs-6">
                            <i class="fa-solid fa-file-arrow-down me-1"></i> Download Official Syllabus PDF
                        </a>
                    @else
                        @if(Auth::user()->isManagement() || Auth::user()->isStaff())
                            <a href="{{ route('academics.syllabi', ['course_id' => $selectedCourseId, 'semester' => $selectedSemester]) }}" class="btn btn-light fw-bold px-4 py-2 rounded-pill shadow-sm fs-7">
                                <i class="fa-solid fa-cloud-arrow-up me-1"></i> Upload Syllabus PDF
                            </a>
                        @else
                            <button class="btn btn-light text-muted fw-semibold px-4 py-2 rounded-pill fs-7" disabled>
                                <i class="fa-solid fa-clock me-1"></i> Syllabus PDF Pending Upload
                            </button>
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Section Title -->
    <div class="d-flex align-items-center justify-content-between mb-3">
        <h4 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-chalkboard-user text-primary me-2"></i>Semester {{ $selectedSemester }} Subjects & Faculty Teachers</h4>
        <span class="badge bg-primary rounded-pill px-3 py-2 fs-7">{{ $subjects->count() }} Subjects Offered</span>
    </div>

    <!-- Subjects & Faculty Grid -->
    <div class="row g-4 mb-4">
        @forelse($subjects as $subject)
        <div class="col-lg-6 col-xl-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
                <div class="card-header bg-white p-3 border-bottom d-flex align-items-center justify-content-between">
                    <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle px-3 py-1 fs-7">
                        {{ $subject->code }}
                    </span>
                    <span class="badge bg-light text-secondary border fs-7">
                        <i class="fa-solid fa-star me-1 text-warning"></i> {{ $subject->credits }} Credits
                    </span>
                </div>

                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">{{ $subject->name }}</h5>

                    <!-- Teaching Faculty Card -->
                    <div class="p-3 bg-light rounded-3 border mb-3">
                        <div class="text-uppercase text-muted fs-8 fw-bold mb-2">Teaching Faculty / Instructor</div>
                        @if($subject->teacher)
                            <div class="d-flex align-items-center gap-3">
                                <img src="{{ $subject->teacher->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode($subject->teacher->name) }}" class="rounded-circle border border-2 border-primary" width="48" height="48" style="object-fit: cover;">
                                <div>
                                    <div class="fw-bold text-dark fs-6">{{ $subject->teacher->name }}</div>
                                    <div class="text-primary fs-7 fw-semibold">
                                        {{ optional($subject->teacher->staffProfile)->designation ?? 'Faculty Teacher' }}
                                    </div>
                                    <div class="text-muted fs-8">
                                        <i class="fa-solid fa-envelope me-1"></i> {{ $subject->teacher->email }}
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="text-muted small">
                                <i class="fa-solid fa-user-clock me-1"></i> Faculty Assignment Pending
                            </div>
                        @endif
                    </div>

                    <!-- Subject Resources Count -->
                    <div class="d-flex align-items-center justify-content-between text-muted fs-7 pt-2 border-top">
                        <span><i class="fa-solid fa-folder-open text-primary me-1"></i> {{ $subject->studyMaterials->count() }} Study Materials</span>
                        <a href="{{ route('materials.index') }}" class="text-primary text-decoration-none fw-bold fs-7">View Notes &raquo;</a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                <i class="fa-solid fa-book-open fs-1 text-black-50 mb-3"></i>
                <h5 class="fw-bold">No Subjects Created for Semester {{ $selectedSemester }} Yet</h5>
                <p class="small mb-0">Subjects added by management for this stream and semester will appear here automatically.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
