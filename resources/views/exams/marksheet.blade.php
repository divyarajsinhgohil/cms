<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Official Marksheet - {{ $student->enrollment_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 2rem; }
        .marksheet-card { background: white; border: 3px double #cbd5e1; border-radius: 1rem; padding: 3rem; max-width: 900px; margin: 0 auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header-title { color: #1e1b4b; }
        @media print {
            body { background: white; padding: 0; }
            .marksheet-card { border: none; box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
            <i class="fa-solid fa-print me-2"></i> Print Marksheet
        </button>
        <a href="{{ route('exams.marksheet.pdf', $student->id) }}" class="btn btn-success btn-lg shadow ms-2">
            <i class="fa-solid fa-file-arrow-down me-2"></i> Download PDF
        </a>
        <a href="{{ route('exams.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Back to Exams</a>
    </div>

    <div class="marksheet-card">
        <!-- University Banner Header -->
        <div class="text-center border-bottom pb-4 mb-4">
            <i class="fa-solid fa-graduation-cap text-primary display-4 mb-2"></i>
            <h2 class="fw-bold header-title mb-0">{{ strtoupper($siteSettings->site_name ?? 'CMS College of Technology') }}</h2>
            <div class="text-muted fw-semibold">{{ strtoupper($siteSettings->affiliation ?? 'Affiliated to State Technological University') }}</div>
            <div class="badge bg-primary text-uppercase px-3 py-2 mt-2">SEMESTER EXAMINATION STATEMENT OF MARKS</div>
        </div>

        <!-- Student Profile Box -->
        <div class="row g-3 bg-light p-3 rounded-3 mb-4 border">
            <div class="col-md-6">
                <div><strong>Student Name:</strong> {{ optional($student->user)->name }}</div>
                <div><strong>Enrollment Number:</strong> <span class="font-monospace text-primary fw-bold">{{ $student->enrollment_no }}</span></div>
                <div><strong>Degree Program:</strong> {{ optional($student->course)->name }}</div>
            </div>
            <div class="col-md-6 text-md-end">
                <div><strong>Department:</strong> {{ optional($student->department)->name }}</div>
                <div><strong>Current Semester:</strong> Semester {{ $student->current_semester }}</div>
                <div><strong>Academic Batch:</strong> {{ $student->batch_year }}</div>
            </div>
        </div>

        <!-- Marks Table -->
        <table class="table table-bordered align-middle mb-4">
            <thead class="table-dark text-center">
                <tr>
                    <th>Code</th>
                    <th>Subject Title</th>
                    <th>Internal (40)</th>
                    <th>External (60)</th>
                    <th>Total (100)</th>
                    <th>Grade</th>
                    <th>Grade Point</th>
                </tr>
            </thead>
            <tbody>
                @forelse($marks as $m)
                <tr>
                    <td class="font-monospace fw-bold text-center">{{ optional($m->subject)->code }}</td>
                    <td class="fw-bold">{{ optional($m->subject)->name }}</td>
                    <td class="text-center">{{ $m->internal_marks }}</td>
                    <td class="text-center">{{ $m->external_marks }}</td>
                    <td class="text-center fw-bold">{{ $m->total_marks }}</td>
                    <td class="text-center"><span class="badge bg-primary fs-7">{{ $m->grade }}</span></td>
                    <td class="text-center font-monospace fw-bold">{{ number_format($m->grade_point, 2) }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">No examination results recorded for this student.</td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Summary Statistics -->
        <div class="row bg-light p-3 rounded-3 mb-5 border align-items-center">
            <div class="col-6">
                <div class="text-uppercase text-muted fs-7 fw-bold">Overall Performance Result</div>
                <div class="fs-4 fw-bold text-success"><i class="fa-solid fa-circle-check me-2"></i> PASSED WITH DISTINCTION</div>
            </div>
            <div class="col-6 text-end">
                <div class="text-uppercase text-muted fs-7 fw-bold">Cumulative Grade Point Average (CGPA)</div>
                <div class="fs-2 fw-bold text-primary font-monospace">{{ number_format($cgpa, 2) }} / 4.00</div>
            </div>
        </div>

        <!-- Official Signatures Footer -->
        <div class="row pt-5 border-top text-center">
            <div class="col-4">
                <div class="border-top border-dark pt-2 fw-bold">Prepared By</div>
                <div class="text-muted fs-7">Academic Coordinator</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark pt-2 fw-bold">Verified By</div>
                <div class="text-muted fs-7">Head of Department</div>
            </div>
            <div class="col-4">
                <div class="border-top border-dark pt-2 fw-bold">Controller of Examinations</div>
                <div class="text-muted fs-7">CMS University Seal</div>
            </div>
        </div>
    </div>

</body>
</html>
