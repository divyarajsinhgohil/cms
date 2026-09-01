<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student ID Card - {{ optional($student->user)->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Segoe UI', sans-serif; }
        .id-card { width: 350px; background: #fff; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .id-card-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px; text-align: center; }
        .id-card-body { padding: 20px; text-align: center; }
        .student-avatar { width: 90px; height: 90px; border-radius: 50%; border: 4px solid #fff; box-shadow: 0 4px 10px rgba(0,0,0,0.15); margin-top: -50px; }
        @media print {
            .no-print { display: none !important; }
            body { background: transparent; }
            .id-card { box-shadow: none; border: 1px solid #ccc; }
        }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center min-vh-100 py-5">

    <div class="mb-4 no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary shadow-sm"><i class="fa-solid fa-print me-1"></i> Print Student ID Card</button>
        <a href="{{ route('certificates.id_card.pdf', $student->id) }}" class="btn btn-success shadow-sm"><i class="fa-solid fa-file-arrow-down me-1"></i> Download PDF</a>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">Close</button>
    </div>

    <!-- Student ID Card -->
    <div class="id-card">
        <div class="id-card-header">
            <h5 class="fw-bold mb-0 text-uppercase tracking-wider">{{ $siteSettings->site_name ?? 'CMS College Master' }}</h5>
            <div class="fs-7 text-white-50">Official Student Identity Card</div>
        </div>
        <div class="id-card-body">
            <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="student-avatar mb-2">
            <h5 class="fw-bold text-dark mb-0">{{ optional($student->user)->name }}</h5>
            <div class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-3 py-1 fs-7 my-2">
                {{ $student->enrollment_no }}
            </div>
            
            <div class="text-start bg-light p-3 rounded-3 mt-2 fs-7">
                <div class="mb-1"><strong>Course:</strong> {{ optional($student->course)->name }} (Semester {{ $student->current_semester }})</div>
                <div class="mb-1"><strong>Department:</strong> {{ optional($student->department)->name }}</div>
                <div class="mb-1"><strong>Batch Year:</strong> {{ $student->batch_year }}</div>
                <div class="mb-1"><strong>Phone:</strong> {{ optional($student->user)->phone }}</div>
                <div><strong>Guardian:</strong> {{ $student->guardian_name }} ({{ $student->guardian_phone }})</div>
            </div>

            <div class="mt-3 pt-2 border-top text-muted fs-7 font-monospace">
                ||||||||||||||||||||||||||||||||||||||||||||
                <div class="fs-7 text-muted mt-1">Authorized Seal & Signature</div>
            </div>
        </div>
    </div>

</body>
</html>
