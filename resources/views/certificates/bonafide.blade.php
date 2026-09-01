<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bonafide Certificate - {{ optional($student->user)->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f4f6f9; font-family: 'Georgia', serif; }
        .cert-container { width: 800px; background: #fff; padding: 50px; border: 10px solid #1e3c72; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        @media print {
            .no-print { display: none !important; }
            body { background: transparent; }
            .cert-container { box-shadow: none; width: 100%; border: 5px solid #1e3c72; }
        }
    </style>
</head>
<body class="d-flex flex-column align-items-center justify-content-center min-vh-100 py-5">

    <div class="mb-4 no-print d-flex gap-2">
        <button onclick="window.print()" class="btn btn-primary shadow-sm"><i class="fa-solid fa-print me-1"></i> Print Official Bonafide Certificate</button>
        <a href="{{ route('certificates.bonafide.pdf', $student->id) }}" class="btn btn-success shadow-sm"><i class="fa-solid fa-file-arrow-down me-1"></i> Download PDF</a>
        <button onclick="window.close()" class="btn btn-secondary shadow-sm">Close</button>
    </div>

    <!-- Official Bonafide Certificate -->
    <div class="cert-container text-center">
        <div class="border-bottom pb-4 mb-4">
            <h2 class="fw-bold text-uppercase text-primary mb-1">{{ $siteSettings->site_name ?? 'CMS College Management System' }}</h2>
            <div class="fs-6 text-muted font-sans-serif">{{ $siteSettings->affiliation ?? 'Affiliated to State University' }}</div>
            <div class="fs-7 text-muted font-sans-serif">{{ $siteSettings->address ?? 'Campus Road, Institutional Area, Pin: 380001' }}</div>
        </div>

        <div class="d-flex justify-content-between align-items-start">
            <h3 class="fw-bold text-uppercase my-4 tracking-wider text-dark border-bottom border-top py-2 d-inline-block px-4">
                BONAFIDE CERTIFICATE
            </h3>
            <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}" class="rounded border mt-3" width="90" height="90" style="object-fit: cover;">
        </div>

        <div class="text-start fs-5 leading-relaxed my-4 text-dark font-sans-serif" style="line-height: 2;">
            <p>
                This is to certify that <strong>{{ optional($student->user)->name }}</strong>, 
                holding Enrollment Number <strong class="font-monospace text-primary">{{ $student->enrollment_no }}</strong>, 
                is a bonafide student of this institution pursuing the 
                <strong>{{ optional($student->course)->name }} ({{ optional($student->course)->code }})</strong> program 
                in the <strong>{{ optional($student->department)->name }}</strong>.
            </p>
            <p>
                He / She is currently studying in <strong>Semester {{ $student->current_semester }}</strong> 
                for the Academic Batch Year <strong>{{ $student->batch_year }}</strong>. 
                His / Her conduct and character during the period of study have been found to be <strong>Exemplary</strong>.
            </p>
        </div>

        <div class="d-flex justify-content-between align-items-end mt-5 pt-5 text-dark font-sans-serif">
            <div class="text-start">
                <div class="fw-bold">Date of Issue: {{ date('F d, Y') }}</div>
                <div class="fs-7 text-muted">Certificate Ref: CMS/BON/2026/{{ sprintf('%04d', $student->id) }}</div>
            </div>
            <div class="text-center">
                <div class="border-bottom border-dark pb-2 mb-1 fw-bold px-4">Dr. Robert Vance</div>
                <div class="fw-bold">Principal / Executive Director</div>
                <div class="fs-7 text-muted">{{ $siteSettings->site_name ?? 'CMS College Management' }}</div>
            </div>
        </div>
    </div>

</body>
</html>
