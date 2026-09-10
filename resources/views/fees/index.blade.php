@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Fee Management, Receipts & Dues Tracker</h3>
        <p class="text-muted mb-0">Manage course fee structures, collect fee installments, and generate tax receipts.</p>
    </div>
    @if(Auth::user()->isManagement())
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="modal" data-bs-target="#feeStructureModal">
            <i class="fa-solid fa-sliders me-1"></i> Course Fee Structure
        </button>
        <button class="btn btn-success shadow-sm" data-bs-toggle="modal" data-bs-target="#collectPaymentModal">
            <i class="fa-solid fa-cash-register me-1"></i> Collect Fee Payment
        </button>
    </div>
    @endif
</div>

<div class="row g-4 mb-4">
    <!-- Fee Structure Breakdown -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-file-invoice-dollar text-primary me-2"></i> Course Fee Structures</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Course</th>
                                <th>Year</th>
                                <th>Tuition / Exam / Lib</th>
                                <th>Total Fee</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($structures as $struct)
                            <tr>
                                <td class="fw-bold">{{ optional($struct->course)->name }}</td>
                                <td><span class="badge bg-light text-dark border">{{ $struct->academic_year }}</span></td>
                                <td class="fs-7 text-muted">₹{{ number_format($struct->tuition_fee) }} / ₹{{ number_format($struct->exam_fee) }} / ₹{{ number_format($struct->library_fee) }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($struct->total_amount, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-4">No fee structures configured.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Dues Summary -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-calculator text-danger me-2"></i> Student Fee Dues Ledger</h5>
            </div>
            <div class="card-body p-3 pb-0">
                <form action="{{ route('fees.index') }}" method="GET" class="row g-2 align-items-end mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small text-muted">Stream</label>
                        <select name="dues_course_id" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Streams</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (string) $duesCourseId === (string) $course->id ? 'selected' : '' }}>{{ $course->code }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small text-muted">Semester</label>
                        <select name="dues_semester" class="form-select form-select-sm" onchange="this.form.submit()">
                            <option value="">All Semesters</option>
                            @for($s = 1; $s <= 8; $s++)
                                <option value="{{ $s }}" {{ (string) $duesSemester === (string) $s ? 'selected' : '' }}>Sem {{ $s }}</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-2">
                        @if($duesCourseId || $duesSemester)
                            <a href="{{ route('fees.index') }}" class="btn btn-outline-danger btn-sm w-100"><i class="fa-solid fa-rotate-left"></i></a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Enrollment No</th>
                                <th>Student</th>
                                <th>Paid</th>
                                <th>Pending Dues</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($students as $st)
                            @php
                                $paid = $st->calculateTotalPaidFee();
                                $dues = $st->calculatePendingDues();
                            @endphp
                            <tr>
                                <td class="font-monospace fw-bold fs-7">{{ $st->enrollment_no }}</td>
                                <td>{{ optional($st->user)->name }}</td>
                                <td class="fw-bold text-success">₹{{ number_format($paid, 2) }}</td>
                                <td class="fw-bold text-danger">₹{{ number_format($dues, 2) }}</td>
                                <td>
                                    @if($dues == 0)
                                        <span class="badge bg-success">Full Paid</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Partial Dues</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">No student fee ledgers found.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($students->hasPages())
                <div class="p-3 border-top d-flex justify-content-between align-items-center">
                    <span class="text-muted fs-7">{{ $students->firstItem() }}-{{ $students->lastItem() }} of {{ $students->total() }}</span>
                    {{ $students->links() }}
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions Log -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-clock-rotate-left text-dark me-2"></i> Payment Transactions & Receipts</h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Receipt No</th>
                        <th>Student Name</th>
                        <th>Amount Paid</th>
                        <th>Payment Mode</th>
                        <th>Date</th>
                        <th>Print Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $pay)
                    <tr>
                        <td class="font-monospace fw-bold text-primary">{{ $pay->receipt_no }}</td>
                        <td>{{ optional($pay->student->user)->name }}</td>
                        <td class="fw-bold text-success">₹{{ number_format($pay->amount_paid, 2) }}</td>
                        <td><span class="badge bg-light text-dark border">{{ $pay->payment_mode }}</span></td>
                        <td>{{ $pay->payment_date }}</td>
                        <td>
                            <a href="{{ route('fees.receipt', $pay->id) }}" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fa-solid fa-print me-1"></i> Print Tax Receipt
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">No fee payment receipts recorded yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($payments->hasPages())
        <div class="p-3 border-top d-flex justify-content-between align-items-center">
            <span class="text-muted fs-7">{{ $payments->firstItem() }}-{{ $payments->lastItem() }} of {{ $payments->total() }}</span>
            {{ $payments->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Modal: Fee Structure -->
<div class="modal fade" id="feeStructureModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('fees.structure') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Configure Course Fee Structure</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Course</label>
                        <select name="course_id" class="form-select" required>
                            @foreach($courses as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Academic Year</label>
                        <input type="text" name="academic_year" class="form-control" value="2026-2027" required>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Tuition Fee (₹)</label>
                            <input type="number" name="tuition_fee" class="form-control" value="60000" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Exam Fee (₹)</label>
                            <input type="number" name="exam_fee" class="form-control" value="5000" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Library Fee (₹)</label>
                            <input type="number" name="library_fee" class="form-control" value="3000" required>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label fw-bold">Other Fee (₹)</label>
                            <input type="number" name="other_fee" class="form-control" value="2000" required>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Fee Structure</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Collect Payment -->
<div class="modal fade" id="collectPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('fees.pay') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="fa-solid fa-cash-register text-success me-2"></i> Collect Fee Installment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">1. Stream (Optional)</label>
                            <select id="payFilterCourse" class="form-select">
                                <option value="">All Streams</option>
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->name }} ({{ $course->code }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">2. Semester (Optional)</label>
                            <select id="payFilterSemester" class="form-select">
                                <option value="">All Semesters</option>
                                @for($s = 1; $s <= 8; $s++)
                                    <option value="{{ $s }}">Semester {{ $s }}</option>
                                @endfor
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-muted">3. Class / Section (Optional)</label>
                            <select id="payFilterSection" class="form-select">
                                <option value="">All Sections</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">4. Select Student <span class="text-danger">*</span></label>
                        <select name="student_id" id="payStudentSelect" class="form-select" required>
                            <option value="">Search or select student...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Amount Paid (₹) <span class="text-danger">*</span></label>
                        <input type="number" name="amount_paid" class="form-control" placeholder="45000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Mode</label>
                        <select name="payment_mode" class="form-select">
                            <option value="Online UPI">Online UPI / Net Banking</option>
                            <option value="Cash">Cash</option>
                            <option value="Bank Cheque">Bank Cheque / DD</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Payment Date</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Remarks / Transaction Ref</label>
                        <input type="text" name="remarks" class="form-control" placeholder="e.g. UPI Ref: 98765432100">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success fw-bold">Process & Generate Receipt</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const cmsFeeStudents = @json($modalStudents);
    const cmsFeeSections = @json($modalSections);
    let cmsPayChoices = null;

    const collectPaymentModalEl = document.getElementById('collectPaymentModal');
    if (collectPaymentModalEl) {
        collectPaymentModalEl.addEventListener('shown.bs.modal', function () {
            if (!cmsPayChoices) {
                cmsPayChoices = new Choices('#payStudentSelect', {
                    removeItemButton: false,
                    searchEnabled: true,
                    shouldSort: false,
                    placeholderValue: 'Search or select student...',
                    noResultsText: 'No matching students found',
                    noChoicesText: 'No students found',
                });
            }
            cmsRenderPayStudents();
        });

        document.getElementById('payFilterCourse').addEventListener('change', cmsUpdatePayFilters);
        document.getElementById('payFilterSemester').addEventListener('change', cmsUpdatePayFilters);
        document.getElementById('payFilterSection').addEventListener('change', cmsRenderPayStudents);
    }

    function cmsUpdatePayFilters() {
        const courseId = document.getElementById('payFilterCourse').value;
        const semester = document.getElementById('payFilterSemester').value;
        const sectionSel = document.getElementById('payFilterSection');

        if (!courseId && !semester) {
            sectionSel.innerHTML = '<option value="">All Sections</option>';
        } else {
            const matchingSections = cmsFeeSections.filter(sec => {
                if (courseId && String(sec.course_id) !== String(courseId)) return false;
                if (semester && String(sec.semester) !== String(semester)) return false;
                return true;
            });
            sectionSel.innerHTML = '<option value="">All Sections</option><option value="unassigned">Unassigned</option>' +
                matchingSections.map(sec => `<option value="${sec.id}">Section ${sec.name}</option>`).join('');
        }

        cmsRenderPayStudents();
    }

    function cmsRenderPayStudents() {
        const courseId = document.getElementById('payFilterCourse').value;
        const semester = document.getElementById('payFilterSemester').value;
        const sectionVal = document.getElementById('payFilterSection').value;

        let list = cmsFeeStudents;
        if (courseId) list = list.filter(s => String(s.course_id) === String(courseId));
        if (semester) list = list.filter(s => String(s.semester) === String(semester));
        if (sectionVal === 'unassigned') list = list.filter(s => !s.section_id);
        else if (sectionVal) list = list.filter(s => String(s.section_id) === String(sectionVal));

        if (!cmsPayChoices) return;

        cmsPayChoices.clearStore();

        if (list.length === 0) {
            cmsPayChoices.setChoices([{ value: '', label: 'No students found for this filter', selected: true, disabled: true }], 'value', 'label', true);
            return;
        }

        const options = [
            { value: '', label: 'Search or select student...', selected: true, disabled: true },
            ...list.map(s => ({ value: String(s.id), label: `${s.name} (${s.enrollment_no})` }))
        ];
        cmsPayChoices.setChoices(options, 'value', 'label', true);
    }
</script>
@endsection
