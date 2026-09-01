@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="fw-bold mb-1">Student Portal</h3>
        <p class="text-muted mb-0">
            Welcome back, {{ Auth::user()->name }}!
            @if($student)
                Enrollment No: <span class="badge bg-primary font-monospace fs-6">{{ $student->enrollment_no }}</span>
            @else
                <span class="badge bg-warning text-dark">No student profile linked to this account yet</span>
            @endif
        </p>
    </div>
    @if($student)
    <a href="{{ route('exams.marksheet', $student->id) }}" class="btn btn-outline-primary shadow-sm"><i class="fa-solid fa-download me-1"></i> Download Marksheet PDF</a>
    @endif
</div>

@if(isset($todayOverride) && $todayOverride->isNotEmpty())
<div class="card border-0 shadow-lg rounded-4 mb-4" style="border-left: 6px solid #f5841f !important;">
    <div class="card-header bg-warning-subtle py-3 border-0 d-flex align-items-center gap-2">
        <i class="fa-solid fa-triangle-exclamation text-warning fs-5"></i>
        <h5 class="fw-bold mb-0 text-dark">Heads up — today's timetable is different!</h5>
    </div>
    <div class="card-body">
        <div class="d-flex flex-wrap gap-3">
            @foreach($todayOverride as $slot)
            <div class="p-3 bg-warning-subtle rounded-3 border border-warning shadow-sm flex-fill" style="min-width: 200px;">
                <span class="badge bg-dark font-monospace mb-1">{{ $slot->start_time }} - {{ $slot->end_time }}</span>
                @if($slot->type === 'Break')
                    <div class="fw-bold text-dark"><i class="fa-solid fa-mug-hot me-1"></i> {{ $slot->label ?: 'Break' }}</div>
                @else
                    <div class="fw-bold text-dark">{{ optional($slot->subject)->name }} <span class="badge {{ $slot->type === 'Lab' ? 'bg-info' : 'bg-primary' }} fs-7">{{ $slot->type }}</span></div>
                    <div class="text-muted fs-7"><i class="fa-solid fa-user-tie me-1"></i> {{ optional($slot->staff)->name }}</div>
                @endif
            </div>
            @endforeach
        </div>
        <a href="{{ route('timetables.index') }}" class="btn btn-sm btn-outline-dark mt-3">View Full Timetable</a>
    </div>
</div>
@endif

<!-- Student Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Cumulative CGPA</div>
                    <div class="fs-2 fw-bold text-primary mt-1">{{ number_format($cgpa, 2) }} / 4.00</div>
                </div>
                <div class="stat-icon bg-primary-subtle text-primary">
                    <i class="fa-solid fa-award"></i>
                </div>
            </div>
            <div class="mt-2 text-success small"><i class="fa-solid fa-star me-1"></i> Academic Standing: Excellent</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Attendance %</div>
                    <div class="fs-2 fw-bold {{ $attendancePct < 75 ? 'text-danger' : 'text-success' }} mt-1">{{ $attendancePct }}%</div>
                </div>
                <div class="stat-icon {{ $attendancePct < 75 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                    <i class="fa-solid fa-clipboard-user"></i>
                </div>
            </div>
            <div class="mt-2 small {{ $attendancePct < 75 ? 'text-danger fw-bold' : 'text-muted' }}">
                @if($attendancePct < 75)
                    <i class="fa-solid fa-triangle-exclamation me-1"></i> Below 75% Threshold!
                @else
                    <i class="fa-solid fa-circle-check me-1"></i> Good Standing (>75%)
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Fee Paid</div>
                    <div class="fs-2 fw-bold text-success mt-1">₹{{ number_format($paidFee, 2) }}</div>
                </div>
                <div class="stat-icon bg-success-subtle text-success">
                    <i class="fa-solid fa-circle-dollar-to-slot"></i>
                </div>
            </div>
            <div class="mt-2 text-muted small">Cleared Installments</div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <div class="text-muted small fw-bold text-uppercase">Pending Dues</div>
                    <div class="fs-2 fw-bold text-danger mt-1">₹{{ number_format($pendingDues, 2) }}</div>
                </div>
                <div class="stat-icon bg-danger-subtle text-danger">
                    <i class="fa-solid fa-receipt"></i>
                </div>
            </div>
            <div class="mt-2 text-danger small">Next Installment Balance</div>
        </div>
    </div>
</div>

<!-- Daily Attendance Trend -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white py-3 border-0">
        <h5 class="fw-bold mb-0"><i class="fa-solid fa-chart-line text-primary me-2"></i> My Daily Attendance (Last 14 Days)</h5>
    </div>
    <div class="card-body">
        @if($student)
            <canvas id="attendanceTrendChart" height="80"></canvas>
        @else
            <p class="text-muted text-center py-4 mb-0">No student profile linked — attendance trend unavailable.</p>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Academic Results -->
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-book-open text-primary me-2"></i> Semester Academic Marks</h5>
                @if($student)
                <a href="{{ route('exams.marksheet', $student->id) }}" class="btn btn-sm btn-link text-decoration-none">Full Marksheet PDF &rarr;</a>
                @endif
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
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
                            <td class="fw-bold">{{ optional($mark->subject)->name }}</td>
                            <td>{{ $mark->internal_marks }}/40</td>
                            <td>{{ $mark->external_marks }}/60</td>
                            <td class="fw-bold">{{ $mark->total_marks }}/100</td>
                            <td><span class="badge bg-primary fs-7">{{ $mark->grade }}</span></td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No exam marks published yet for current semester.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Student Notices -->
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white py-3 border-0">
                <h5 class="fw-bold mb-0"><i class="fa-solid fa-bullhorn text-warning me-2"></i> Student Announcements</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-column gap-3">
                    @forelse($notices as $notice)
                    <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="badge bg-primary text-uppercase fs-7">{{ $notice->priority }}</span>
                            <span class="text-muted fs-7">{{ $notice->created_at->diffForHumans() }}</span>
                        </div>
                        <div class="fw-bold text-dark">{{ $notice->title }}</div>
                        <p class="text-secondary small mb-0">{{ Str::limit($notice->content, 80) }}</p>
                    </div>
                    @empty
                    <p class="text-muted text-center py-4">No student notices posted.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@if($student)
@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const attendanceTrend = @json($attendanceTrend);
    new Chart(document.getElementById('attendanceTrendChart'), {
        type: 'line',
        data: {
            labels: attendanceTrend.map(d => d.label),
            datasets: [{
                label: 'Attendance %',
                data: attendanceTrend.map(d => d.pct),
                spanGaps: false,
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true,
                pointRadius: 4,
                pointBackgroundColor: attendanceTrend.map(d => (d.pct !== null && d.pct < 75) ? '#dc3545' : '#4f46e5'),
            }]
        },
        options: {
            scales: {
                y: { min: 0, max: 100, ticks: { callback: (v) => v + '%' } }
            },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (ctx) => ctx.raw === null ? 'No class marked' : ctx.raw + '% present'
                    }
                }
            }
        }
    });
</script>
@endsection
@endif
@endsection
