@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">{{ $ownOnly ? 'My Certificates & ID Cards' : 'Certificates & ID Cards' }}</h3>
        <p class="text-muted mb-0">
            @if($ownOnly)
                Print or download your official Student ID Card and Bonafide / Benefit Claim Certificate.
            @else
                Generate official Student ID Cards and Bonafide / Benefit Claim Certificates.
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
                        <th class="ps-4">Enrollment No</th>
                        <th>Student Details</th>
                        <th>Course</th>
                        <th>Department</th>
                        <th>Semester</th>
                        <th class="pe-4">Generate Certificates</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td class="ps-4">
                            <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle px-2 py-1 fs-7">
                                {{ $student->enrollment_no }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="rounded-circle border" width="40" height="40" style="object-fit: cover;">
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
                        <td class="pe-4">
                            <div class="d-flex flex-wrap gap-2">
                                <!-- Student ID Card Actions -->
                                <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-dark rounded-pill px-3" title="Print Student ID Card">
                                    <i class="fa-solid fa-id-card me-1"></i> ID Card
                                </a>

                                <!-- Bonafide & Benefit Claim Action Modal Trigger -->
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 fw-bold" onclick="cmsOpenBonafideModal({{ $student->id }}, '{{ addslashes(optional($student->user)->name) }}', '{{ $student->enrollment_no }}')">
                                    <i class="fa-solid fa-certificate me-1"></i> Bonafide & Benefit Claim
                                </button>
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

<!-- Bonafide Purpose & Benefit Claim Reason Modal -->
<div class="modal fade" id="cmsBonafideReasonModal" tabindex="-1" aria-labelledby="cmsBonafideReasonModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header text-white p-4" style="background: linear-gradient(135deg, #6d3fa0 0%, #2e5fa3 100%);">
                <h5 class="modal-title fw-bold d-flex align-items-center gap-2" id="cmsBonafideReasonModalLabel">
                    <i class="fa-solid fa-certificate text-warning fs-4"></i> Bonafide Benefit Claim Generator
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-info border-0 shadow-sm rounded-3 mb-3 fs-7">
                    <i class="fa-solid fa-circle-info me-1"></i> Student photo and official college receipt logo will be automatically positioned on the certificate. Select or write the claim reason below.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Student Name & Enrollment</label>
                    <input type="text" id="modalStudentInfo" class="form-control bg-light fw-bold text-primary" readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Select Purpose / Benefit Claim Reason</label>
                    <select id="modalReasonPreset" class="form-select rounded-3 mb-2" onchange="cmsSelectPresetReason(this)">
                        <option value="Bus Pass Concession Claim">🚌 Bus Pass Concession Claim</option>
                        <option value="State Govt / Merit Scholarship Claim">🎓 State Govt / Merit Scholarship Claim</option>
                        <option value="Bank Account Opening & KYC">🏦 Bank Account Opening & KYC Purpose</option>
                        <option value="Passport & Visa Application Verification">🛂 Passport & Visa Application Verification</option>
                        <option value="Education Loan & Financial Assistance Application">💳 Education Loan Application</option>
                        <option value="Internship & Industrial Training Approval">💼 Internship & Industrial Training</option>
                        <option value="custom">✏️ Custom Purpose / Enter Specific Reason...</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-dark">Certificate Claim Reason Text <span class="text-danger">*</span></label>
                    <textarea id="modalReasonText" class="form-control rounded-3" rows="3" placeholder="Specify reason..." required>Bus Pass Concession Claim</textarea>
                </div>
            </div>
            <div class="modal-footer bg-light border-0 p-3 justify-content-between">
                <button type="button" class="btn btn-light px-3" data-bs-dismiss="modal">Cancel</button>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-primary fw-bold rounded-pill px-3" onclick="cmsGenerateBonafide('print')">
                        <i class="fa-solid fa-print me-1"></i> Print View
                    </button>
                    <button type="button" class="btn btn-success fw-bold rounded-pill px-4" onclick="cmsGenerateBonafide('pdf')">
                        <i class="fa-solid fa-file-arrow-down me-1"></i> Download PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let activeStudentId = null;

    function cmsOpenBonafideModal(studentId, studentName, enrollmentNo) {
        activeStudentId = studentId;
        document.getElementById('modalStudentInfo').value = `${studentName} (${enrollmentNo})`;
        document.getElementById('modalReasonPreset').value = "Bus Pass Concession Claim";
        document.getElementById('modalReasonText').value = "Bus Pass Concession Claim";

        const modal = new bootstrap.Modal(document.getElementById('cmsBonafideReasonModal'));
        modal.show();
    }

    function cmsSelectPresetReason(selectEl) {
        if (selectEl.value !== 'custom') {
            document.getElementById('modalReasonText').value = selectEl.value;
        } else {
            document.getElementById('modalReasonText').value = '';
            document.getElementById('modalReasonText').focus();
        }
    }

    function cmsGenerateBonafide(type) {
        if (!activeStudentId) return;
        const reason = encodeURIComponent(document.getElementById('modalReasonText').value || 'General Documentation & Benefit Claim');

        if (type === 'print') {
            const url = `/certificates/bonafide/${activeStudentId}?reason=${reason}`;
            window.open(url, '_blank');
        } else {
            const url = `/certificates/bonafide/${activeStudentId}/pdf?reason=${reason}`;
            window.location.href = url;
        }
    }
</script>
@endsection
