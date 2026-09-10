@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Student Master Directory</h3>
        <p class="text-muted mb-0">Filter student records stream-wise, semester-wise, batch-wise, or by department.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('admissions.export', request()->all()) }}" class="btn btn-outline-success shadow-sm" title="Export filtered students as Excel/CSV">
            <i class="fa-solid fa-file-excel me-1" aria-hidden="true"></i> Export CSV
        </a>
        @if(Auth::user()->isManagement() || Auth::user()->isStaff())
        <a href="{{ route('admissions.import') }}" class="btn btn-outline-primary shadow-sm">
            <i class="fa-solid fa-file-import me-1" aria-hidden="true"></i> Bulk Import
        </a>
        @endif
        @if(Auth::user()->isManagement())
        <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#newAdmissionModal">
            <i class="fa-solid fa-user-plus me-1"></i> New Student Admission
        </button>
        @endif
    </div>
</div>

<!-- Interactive Stream & Semester Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h6 class="fw-bold mb-0"><i class="fa-solid fa-filter text-primary me-2"></i> Stream & Semester Filter Section</h6>
    </div>
    <div class="card-body">
        <form action="{{ route('admissions.index') }}" method="GET" class="row g-3">
            <!-- Filter 1: Stream / Course -->
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Stream / Degree Program</label>
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

            <!-- Filter 2: Semester -->
            <div class="col-md-2">
                <label class="form-label fw-bold small text-muted">Semester</label>
                <select name="semester" class="form-select" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ request('semester') == $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>

            <!-- Filter 3: Status -->
            <div class="col-md-2">
                <label class="form-label fw-bold small text-muted">Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="Active" {{ $status === 'Active' ? 'selected' : '' }}>Active Only</option>
                    <option value="Left" {{ $status === 'Left' ? 'selected' : '' }}>Left Only</option>
                    <option value="All" {{ $status === 'All' ? 'selected' : '' }}>All</option>
                </select>
            </div>

            <!-- Filter 4: Department -->
            <div class="col-md-2">
                <label class="form-label fw-bold small text-muted">Department</label>
                <select name="department_id" class="form-select" onchange="this.form.submit()">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }} ({{ $dept->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Filter 5: Search -->
            <div class="col-md-2">
                <label class="form-label fw-bold small text-muted">Search Name / Enrollment</label>
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search..." value="{{ request('search') }}">
                    <button type="submit" class="btn btn-primary"><i class="fa-solid fa-magnifying-glass"></i></button>
                </div>
            </div>

            <!-- Filter Actions -->
            <div class="col-md-1 d-flex align-items-end">
                @if(request()->hasAny(['course_id', 'semester', 'department_id', 'search', 'batch_year', 'status']))
                    <a href="{{ route('admissions.index') }}" class="btn btn-outline-danger w-100" title="Reset Filters">
                        <i class="fa-solid fa-rotate-left me-1"></i> Reset
                    </a>
                @endif
            </div>
        </form>

        @if(Auth::user()->isManagement() && request()->filled('course_id') && request()->filled('semester'))
        <form action="{{ route('admissions.promote_semester') }}" method="POST" class="mt-3" data-confirm="Promote every active student in this stream & semester to the next semester? Their class section assignment will be cleared and will need to be re-divided.">
            @csrf
            <input type="hidden" name="course_id" value="{{ request('course_id') }}">
            <input type="hidden" name="semester" value="{{ request('semester') }}">
            <button type="submit" class="btn btn-outline-success btn-sm">
                <i class="fa-solid fa-arrow-up-right-from-square me-1"></i> Promote This Semester's Students to Next Semester
            </button>
        </form>
        @endif
    </div>
</div>

<!-- Results Header Summary -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <div class="fw-bold text-dark">
        Showing <span class="badge bg-primary fs-6">{{ $students->total() }}</span> Students Listed
        @if(request('course_id'))
            <span class="badge bg-info-subtle text-info me-1">Stream: {{ optional($courses->firstWhere('id', request('course_id')))->code }}</span>
        @endif
        @if(request('semester'))
            <span class="badge bg-warning-subtle text-warning text-dark me-1">Semester: {{ request('semester') }}</span>
        @endif
    </div>
</div>

<!-- Student Directory Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Enrollment No</th>
                        <th>Student Name</th>
                        <th>Stream / Course</th>
                        <th>Section</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th>Contact</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr class="{{ $student->status === 'Left' ? 'table-secondary' : '' }}">
                        <td class="fw-bold text-center">{{ $student->roll_no ?? '—' }}</td>
                        <td>
                            <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle px-2 py-1 fs-7">
                                {{ $student->enrollment_no }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('admissions.show', $student->id) }}" class="d-flex align-items-center gap-2 text-decoration-none">
                                <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="rounded-circle" width="36" height="36">
                                <div>
                                    <div class="fw-bold text-dark">{{ optional($student->user)->name }}</div>
                                    <div class="text-muted fs-7">{{ optional($student->user)->email }}</div>
                                </div>
                            </a>
                        </td>
                        <td>
                            <span class="badge bg-primary fs-7">{{ optional($student->course)->code }}</span>
                            <div class="fs-7 text-muted">{{ optional($student->department)->name }}</div>
                        </td>
                        <td>
                            @if($student->section)
                                <span class="badge bg-info-subtle text-info border border-info-subtle">{{ $student->section->name }}</span>
                            @else
                                <span class="text-muted fs-7">Unassigned</span>
                            @endif
                        </td>
                        <td><span class="badge bg-warning-subtle text-dark border border-warning-subtle">Sem {{ $student->current_semester }}</span></td>
                        <td>
                            @if($student->status === 'Active')
                                <span class="badge bg-success">Active</span>
                            @else
                                <span class="badge bg-secondary">Left</span>
                            @endif
                        </td>
                        <td>
                            <div class="fs-7 font-monospace fw-bold">{{ optional($student->user)->phone }}</div>
                        </td>
                        <td>
                            <div class="d-flex gap-1 flex-wrap">
                                <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Print Student ID Card">
                                    <i class="fa-solid fa-id-card"></i>
                                </a>
                                <a href="{{ route('certificates.bonafide', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-info" title="Print Bonafide Certificate">
                                    <i class="fa-solid fa-certificate"></i>
                                </a>
                                <a href="{{ route('exams.marksheet', $student->id) }}" class="btn btn-sm btn-outline-primary" title="View Marksheet">
                                    <i class="fa-solid fa-file-invoice"></i>
                                </a>
                                @if(Auth::user()->isManagement() && $student->status === 'Active')
                                <form action="{{ route('admissions.mark_left', $student->id) }}" method="POST" data-confirm="Mark {{ optional($student->user)->name }} as Left college? Their history is kept, but they will be hidden from active rosters and roll numbers for their class will be regenerated.">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Mark as Left">
                                        <i class="fa-solid fa-user-slash"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-5">
                            <i class="fa-solid fa-user-slash display-5 mb-2 text-secondary"></i>
                            <p class="mb-0 fw-semibold">No students match your selected stream / semester filter.</p>
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

@if(Auth::user()->isManagement())
<!-- Modal: New Admission -->
<div class="modal fade" id="newAdmissionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="{{ route('admissions.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-graduation-cap text-primary me-2"></i> Student Admission Form</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 fs-7 mb-3">
                        <i class="fa-solid fa-wand-magic-sparkles me-1"></i> <strong>Auto Enrollment Generator:</strong> Enrollment number will automatically format as <code>CMS-{YEAR}-{DEPT_CODE}-{00101}</code> upon form submission.
                    </div>

                    <!-- Photo Upload Section -->
                    <div class="d-flex align-items-center gap-3 mb-4 p-3 bg-light rounded-3 border">
                        <img id="studentPhotoPreview" src="https://ui-avatars.com/api/?name=Student&background=e2e8f0&color=64748b" class="rounded-circle border" width="72" height="72" style="object-fit: cover;">
                        <div class="flex-grow-1">
                            <label class="form-label fw-bold mb-1">Student Photo</label>
                            <input type="file" name="photo" accept="image/*" class="form-control" onchange="cmsPreviewPhoto(this, 'studentPhotoPreview')">
                            <div class="form-text fs-7">JPG or PNG, max 2MB. Leave empty to use an auto-generated avatar.</div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Full Student Name</label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Alex Morgan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Student Email</label>
                            <input type="email" name="email" class="form-control" placeholder="student@cms.edu" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Mobile Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+91 9876543212" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Gender</label>
                            <select name="gender" class="form-select" required>
                                <option value="Female">Female</option>
                                <option value="Male">Male</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Department</label>
                            <select name="department_id" class="form-select" required>
                                @foreach($departments as $dept)
                                    <option value="{{ $dept->id }}">{{ $dept->name }} ({{ $dept->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Stream / Degree Program</label>
                            <select name="course_id" id="newAdmCourse" class="form-select" onchange="cmsFilterSections()" required>
                                <option value="">Select stream...</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Current Semester</label>
                            <select name="current_semester" id="newAdmSemester" class="form-select" onchange="cmsFilterSections()" required>
                                <option value="">Select semester...</option>
                                @for($sem=1; $sem<=8; $sem++)
                                    <option value="{{ $sem }}">Semester {{ $sem }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Class Section <span class="text-muted fw-normal">(optional)</span></label>
                            <select name="section_id" id="newAdmSection" class="form-select">
                                <option value="">Choose stream & semester first...</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Batch Admission Year</label>
                            <input type="number" name="batch_year" class="form-control" value="2026" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Date of Birth</label>
                            <input type="date" name="dob" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">Guardian / Parent Name</label>
                            <input type="text" name="guardian_name" class="form-control" placeholder="Parent / Guardian Name">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold">Residential Address</label>
                            <textarea name="address" class="form-control" rows="2" placeholder="Full permanent address..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Confirm Admission & Generate ID</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const cmsAllSections = @json($allSections);

    function cmsFilterSections() {
        const courseId = document.getElementById('newAdmCourse').value;
        const semester = document.getElementById('newAdmSemester').value;
        const target = document.getElementById('newAdmSection');
        target.innerHTML = '';

        if (!courseId || !semester) {
            target.innerHTML = '<option value="">Choose stream & semester first...</option>';
            return;
        }

        const matches = cmsAllSections.filter(s => String(s.course_id) === String(courseId) && String(s.semester) === String(semester));
        if (matches.length === 0) {
            target.innerHTML = '<option value="">No sections created yet for this stream & semester</option>';
            return;
        }

        target.innerHTML = '<option value="">Leave unassigned</option>' +
            matches.map(s => `<option value="${s.id}">Section ${s.name} (capacity ${s.capacity})</option>`).join('');
    }
</script>
@endif
@endsection
