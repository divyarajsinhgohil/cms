<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bonafide & Benefit Claim Certificate - {{ optional($student->user)->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Georgia', serif; padding: 2rem; }
        .cert-card { background: white; border: 3px double #6d3fa0; border-radius: 1rem; padding: 3.5rem; max-width: 850px; margin: 0 auto; box-shadow: 0 15px 35px rgba(0,0,0,0.08); position: relative; }
        .header-logo { color: #6d3fa0; }
        .student-photo-frame {
            width: 110px;
            height: 130px;
            border: 3px solid #6d3fa0;
            border-radius: 8px;
            object-fit: cover;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .benefit-reason-box {
            background-color: #f1f5f9;
            border-left: 5px solid #6d3fa0;
            padding: 1rem 1.25rem;
            border-radius: 0.5rem;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        @media print {
            body { background: white; padding: 0; }
            .cert-card { border: 2px solid #6d3fa0; box-shadow: none; padding: 2rem; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow-sm">
            <i class="fa-solid fa-print me-2"></i> Print Official Certificate
        </button>
        <a href="{{ route('certificates.bonafide.pdf', ['student' => $student->id, 'reason' => $reason]) }}" class="btn btn-success btn-lg shadow-sm ms-2">
            <i class="fa-solid fa-file-arrow-down me-2"></i> Download PDF
        </a>
        <a href="{{ route('certificates.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Back to Certificates</a>
    </div>

    <div class="cert-card">
        <!-- Header — Same Logo & Style as the official fee receipt -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
            <div class="d-flex align-items-center gap-3">
                @if(!empty($siteSettings?->logo_url))
                    <img src="{{ asset($siteSettings->logo_url) }}" alt="Logo" height="60" class="rounded">
                @else
                    <i class="fa-solid fa-graduation-cap header-logo display-4"></i>
                @endif
                <div>
                    <h2 class="fw-bold header-logo mb-1">{{ $siteSettings->site_name ?? 'CMS College of Technology' }}</h2>
                    <div class="text-muted small font-sans-serif">{{ $siteSettings->affiliation ?? 'Affiliated to State University' }}{{ $siteSettings->address ? ' | '.$siteSettings->address : '' }}</div>
                    <div class="text-muted small font-sans-serif">Email: {{ $siteSettings->contact_email ?? 'info@cms.edu' }} | Phone: {{ $siteSettings->contact_phone ?? '+91 98765 43210' }}</div>
                </div>
            </div>
            <div class="text-end font-sans-serif">
                <span class="badge bg-primary fs-6 text-uppercase px-3 py-2">Official Bonafide</span>
                <div class="fw-bold font-monospace mt-2 text-primary">CMS/BON/2026/{{ sprintf('%04d', $student->id) }}</div>
                <div class="text-muted small">Date: {{ date('F d, Y') }}</div>
            </div>
        </div>

        <!-- Student Photo & Header Title Row -->
        <div class="d-flex justify-content-between align-items-start my-3">
            <div>
                <h3 class="fw-bold text-uppercase text-dark border-bottom border-top py-2 d-inline-block px-3 font-sans-serif mb-1" style="letter-spacing: 1px;">
                    BONAFIDE & BENEFIT CLAIM CERTIFICATE
                </h3>
                <div class="text-muted fs-7 font-sans-serif">Academic Year: {{ $siteSettings->academic_year ?? date('Y').'-'.(date('Y')+1) }}</div>
            </div>

            <!-- Automatic Student Photo Placement -->
            <div class="text-center font-sans-serif">
                @if(optional($student->user)->avatar)
                    <img src="{{ asset($student->user->avatar) }}" alt="Student Photo" class="student-photo-frame">
                @else
                    <img src="https://ui-avatars.com/api/?name={{ urlencode(optional($student->user)->name) }}&size=150" alt="Student Avatar" class="student-photo-frame">
                @endif
                <div class="fs-8 text-muted mt-1 fw-bold">Student Photo</div>
            </div>
        </div>

        <!-- Certificate Main Body -->
        <div class="text-start fs-5 leading-relaxed my-4 text-dark font-sans-serif" style="line-height: 2;">
            <p>
                This is to certify that <strong>{{ optional($student->user)->name }}</strong>, 
                holding Enrollment Number <strong class="font-monospace text-primary">{{ $student->enrollment_no }}</strong>, 
                is a bonafide student of this institution pursuing the 
                <strong>{{ optional($student->course)->name }} ({{ optional($student->course)->code }})</strong> degree program 
                in the Department of <strong>{{ optional($student->department)->name }}</strong>.
            </p>
            <p>
                He / She is currently studying in <strong>Semester {{ $student->current_semester }}</strong> 
                for the Academic Batch Year <strong>{{ $student->batch_year }}</strong>. 
                His / Her conduct, academic standing, and character during the period of study have been found to be <strong>Exemplary</strong>.
            </p>

            <!-- Purpose / Benefit Claim Reason Highlight -->
            <div class="benefit-reason-box my-4">
                <div class="fw-bold text-uppercase text-primary fs-7 mb-1"><i class="fa-solid fa-file-circle-check me-1"></i> Purpose / Benefit Claim Reason:</div>
                <div class="fs-5 fw-semibold text-dark">{{ $reason }}</div>
                <div class="text-muted fs-7 mt-1">This certificate is issued upon the student's request for official submission towards the purpose stated above.</div>
            </div>
        </div>

        <!-- Signatures & Computer Verification -->
        <div class="row pt-5 mt-4 border-top font-sans-serif">
            <div class="col-6 text-start">
                <div class="fw-bold text-dark">Date of Issue: {{ date('F d, Y') }}</div>
                <div class="text-muted fs-7">Certificate Ref: CMS/BON/2026/{{ sprintf('%04d', $student->id) }}</div>
                <div class="text-muted fs-8 mt-1">Digital Verification Hash: {{ md5($student->enrollment_no . $reason . date('Y-m-d')) }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="border-bottom border-dark pb-2 mb-1 fw-bold d-inline-block px-4">Dr. Robert Vance</div>
                <div class="fw-bold text-dark">Principal / Executive Director</div>
                <div class="text-muted fs-7">{{ $siteSettings->site_name ?? 'CMS College Management System' }}</div>
            </div>
        </div>
    </div>

</body>
</html>
