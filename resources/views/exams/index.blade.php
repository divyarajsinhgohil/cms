@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">{{ $ownOnly ? 'My Exam Results & CGPA' : 'Semester Exam Marks Entry & CGPA Engine' }}</h3>
        <p class="text-muted mb-0">
            @if($ownOnly)
                Your marks across every subject and semester recorded so far.
            @else
                Record internal & external marks, calculate SGPA/CGPA, and issue official semester marksheets.
            @endif
        </p>
    </div>
    @if(Auth::user()->isStaff() || Auth::user()->isManagement())
    <button class="btn btn-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#marksEntryModal">
        <i class="fa-solid fa-pen-to-square me-1"></i> Enter Exam Marks
    </button>
    @endif
</div>

<!-- Filter Bar -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body">
        <form action="{{ route('exams.index') }}" method="GET" class="row g-3 align-items-end">
            @unless($ownOnly)
            <div class="col-md-4">
                <label class="form-label fw-bold small text-muted">Stream / Course</label>
                <select name="ledger_course_id" class="form-select" onchange="this.form.submit()">
                    <option value="">{{ $streamScoped ? 'All My Assigned Streams' : 'All Streams' }}</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}" {{ (string) $ledgerCourseId === (string) $course->id ? 'selected' : '' }}>
                            {{ $course->name }} ({{ $course->code }})
                        </option>
                    @endforeach
                </select>
                @if($streamScoped)
                    <div class="form-text fs-7"><i class="fa-solid fa-lock me-1"></i> Scoped to your assigned stream(s).</div>
                @endif
            </div>
            @endunless
            <div class="col-md-3">
                <label class="form-label fw-bold small text-muted">Semester</label>
                <select name="ledger_semester" class="form-select" onchange="this.form.submit()">
                    <option value="">All Semesters</option>
                    @for($s = 1; $s <= 8; $s++)
                        <option value="{{ $s }}" {{ (string) $ledgerSemester === (string) $s ? 'selected' : '' }}>Semester {{ $s }}</option>
                    @endfor
                </select>
            </div>
            <div class="col-md-2">
                @if($ledgerCourseId || $ledgerSemester)
                    <a href="{{ route('exams.index') }}" class="btn btn-outline-danger w-100"><i class="fa-solid fa-rotate-left me-1"></i> Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

<!-- Exam Marks Master Table -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-award text-primary me-2"></i> {{ $ownOnly ? 'My Marks' : 'Examination Marks Ledger' }}</h5>
        <span class="badge bg-primary fs-7">{{ $examMarks->total() }} Records</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Student Name & Enrollment</th>
                        <th>Subject Name</th>
                        <th>Semester</th>
                        <th>Internal (40)</th>
                        <th>External (60)</th>
                        <th>Total (100)</th>
                        <th>Grade</th>
                        <th>Grade Point</th>
                        <th>Marksheet</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($examMarks as $mark)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ optional($mark->student->user)->name }}</div>
                            <span class="badge bg-light text-primary border font-monospace fs-7">{{ optional($mark->student)->enrollment_no }}</span>
                        </td>
                        <td class="fw-semibold">{{ optional($mark->subject)->name }}</td>
                        <td><span class="badge bg-info-subtle text-info">Sem {{ $mark->semester }}</span></td>
                        <td>{{ $mark->internal_marks }}</td>
                        <td>{{ $mark->external_marks }}</td>
                        <td class="fw-bold text-dark">{{ $mark->total_marks }}</td>
                        <td><span class="badge bg-primary fs-7 px-2 py-1">{{ $mark->grade }}</span></td>
                        <td class="fw-bold font-monospace">{{ number_format($mark->grade_point, 2) }}</td>
                        <td>
                            <a href="{{ route('exams.marksheet', $mark->student_id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-file-pdf me-1"></i> Download PDF
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted py-4">No exam marks entered yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($examMarks->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted fs-7">Showing {{ $examMarks->firstItem() }}-{{ $examMarks->lastItem() }} of {{ $examMarks->total() }} records</span>
            {{ $examMarks->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Enter Marks -->
<div class="modal fade" id="marksEntryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('exams.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-pen-to-square text-primary me-2"></i> Semester Marks Entry</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Student</label>
                        <select name="student_id" class="form-select" required>
                            @foreach($students as $st)
                                <option value="{{ $st->id }}">{{ optional($st->user)->name }} ({{ $st->enrollment_no }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Select Subject</label>
                        <select name="subject_id" class="form-select" required>
                            @foreach($subjects as $sub)
                                <option value="{{ $sub->id }}">{{ $sub->name }} (Sem {{ $sub->semester }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Semester</label>
                        <input type="number" name="semester" class="form-control" value="3" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Internal Marks (Max 40)</label>
                            <input type="number" name="internal_marks" class="form-control" min="0" max="40" placeholder="28" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">External Marks (Max 60)</label>
                            <input type="number" name="external_marks" class="form-control" min="0" max="60" placeholder="58" required>
                        </div>
                    </div>
                    <div class="alert alert-info fs-7 py-2 mb-0">
                        <i class="fa-solid fa-calculator me-1"></i> Grade & CGPA points will be auto-calculated upon saving.
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary fw-bold">Calculate & Save Marks</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
