@extends('layouts.app')

@section('content')
@php
    $paid = $student->calculateTotalPaidFee();
    $dues = $student->calculatePendingDues();
    $attendancePct = $student->calculateAttendancePercentage();
    $cgpa = $student->calculateCGPA();
@endphp

<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="{{ route('admissions.index') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Directory
    </a>
    <div class="d-flex gap-2">
        <a href="{{ route('certificates.id_card', $student->id) }}" target="_blank" class="btn btn-outline-dark"><i class="fa-solid fa-id-card me-1"></i> ID Card</a>
        <a href="{{ route('certificates.bonafide', $student->id) }}" target="_blank" class="btn btn-outline-info"><i class="fa-solid fa-certificate me-1"></i> Bonafide</a>
        <a href="{{ route('exams.marksheet', $student->id) }}" class="btn btn-outline-primary"><i class="fa-solid fa-file-invoice me-1"></i> Marksheet</a>
        @if(Auth::user()->isManagement() && $student->status === 'Active')
        <form action="{{ route('admissions.mark_left', $student->id) }}" method="POST" data-confirm="Mark {{ optional($student->user)->name }} as Left college? Their history is kept, but they will be hidden from active rosters and roll numbers for their class will be regenerated.">
            @csrf
            <button type="submit" class="btn btn-outline-danger"><i class="fa-solid fa-user-slash me-1"></i> Mark as Left</button>
        </form>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Profile Card -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center">
            <div class="card-body p-4">
                <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?size=128&name='.urlencode(optional($student->user)->name) }}" class="rounded-circle border mb-3" width="110" height="110" style="object-fit: cover;">
                <h4 class="fw-bold mb-0">{{ optional($student->user)->name }}</h4>
                <p class="text-muted mb-2">{{ optional($student->user)->email }}</p>
                <div class="d-flex justify-content-center gap-2 mb-3">
                    @if($student->status === 'Active')
                        <span class="badge bg-success">Active</span>
                    @else
                        <span class="badge bg-secondary">Left</span>
                    @endif
                    @if($student->section)
                        <span class="badge bg-info-subtle text-info border border-info-subtle">Section {{ $student->section->name }}</span>
                    @endif
                    @if($student->roll_no)
                        <span class="badge bg-warning-subtle text-dark border border-warning-subtle">Roll No {{ $student->roll_no }}</span>
                    @endif
                </div>
                <div class="text-start fs-7">
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Enrollment No</span><span class="fw-bold font-monospace">{{ $student->enrollment_no }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Stream</span><span class="fw-bold">{{ optional($student->course)->name }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Department</span><span class="fw-bold">{{ optional($student->department)->name }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Semester</span><span class="fw-bold">Sem {{ $student->current_semester }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Batch Year</span><span class="fw-bold">{{ $student->batch_year }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Gender</span><span class="fw-bold">{{ $student->gender }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Date of Birth</span><span class="fw-bold">{{ $student->dob ?? '—' }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Mobile</span><span class="fw-bold font-monospace">{{ optional($student->user)->phone }}</span></div>
                    <div class="d-flex justify-content-between border-bottom py-2"><span class="text-muted">Guardian</span><span class="fw-bold">{{ $student->guardian_name ?? '—' }}</span></div>
                    <div class="d-flex justify-content-between py-2"><span class="text-muted">Guardian Phone</span><span class="fw-bold font-monospace">{{ $student->guardian_phone ?? '—' }}</span></div>
                </div>
                @if($student->address)
                <div class="mt-2 pt-2 border-top text-start">
                    <div class="text-muted fs-7 fw-bold text-uppercase mb-1">Address</div>
                    <p class="mb-0 fs-7">{{ $student->address }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Stats + Records -->
    <div class="col-lg-8">
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-muted small fw-bold text-uppercase">Attendance</div>
                    <div class="fs-2 fw-bold {{ $attendancePct < 75 ? 'text-danger' : 'text-success' }} mt-1">{{ $attendancePct }}%</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-muted small fw-bold text-uppercase">CGPA</div>
                    <div class="fs-2 fw-bold text-primary mt-1">{{ $cgpa }}</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card">
                    <div class="text-muted small fw-bold text-uppercase">Pending Dues</div>
                    <div class="fs-2 fw-bold {{ $dues > 0 ? 'text-danger' : 'text-success' }} mt-1">₹{{ number_format($dues, 2) }}</div>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice-dollar text-success me-2"></i> Fee Summary</h6>
            </div>
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Total Paid</span>
                    <span class="fw-bold text-success">₹{{ number_format($paid, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mt-2">
                    <span class="text-muted">Pending Dues</span>
                    <span class="fw-bold text-danger">₹{{ number_format($dues, 2) }}</span>
                </div>
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white py-3 border-0">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-award text-warning me-2"></i> Exam Marks</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Semester</th>
                                <th>Subject</th>
                                <th>Internal</th>
                                <th>External</th>
                                <th>Total</th>
                                <th>Grade</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($examMarks as $mark)
                            <tr>
                                <td>Sem {{ $mark->semester }}</td>
                                <td>{{ optional($mark->subject)->name }}</td>
                                <td>{{ $mark->internal_marks }}</td>
                                <td>{{ $mark->external_marks }}</td>
                                <td class="fw-bold">{{ $mark->total_marks }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $mark->grade }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">No exam marks recorded yet.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
