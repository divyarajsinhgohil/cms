<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Student ID Card - {{ optional($student->user)->name }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #1e293b; font-size: 13px; }
        .id-card { width: 320px; margin: 0 auto; border: 2px solid #1e3c72; border-radius: 6px; overflow: hidden; }
        .id-card-header { background: #1e3c72; color: #fff; padding: 14px; text-align: center; }
        .id-card-header h3 { margin: 0; font-size: 15px; text-transform: uppercase; }
        .id-card-header div { font-size: 10px; color: #cbd5e1; }
        .id-card-body { padding: 16px; text-align: center; }
        .name { font-weight: bold; font-size: 15px; margin-bottom: 6px; }
        .enroll-badge { display: inline-block; border: 1px solid #1e3c72; color: #1e3c72; padding: 3px 10px; font-size: 11px; margin-bottom: 10px; }
        .details { text-align: left; background: #f4f6f9; padding: 10px; font-size: 11px; line-height: 1.6; }
        .footer { margin-top: 14px; padding-top: 8px; border-top: 1px solid #cbd5e1; font-size: 10px; color: #64748b; text-align: center; }
    </style>
</head>
<body>

    <div class="id-card">
        <div class="id-card-header">
            <h3>{{ $siteSettings->site_name ?? 'CMS College Master' }}</h3>
            <div>Official Student Identity Card</div>
        </div>
        <div class="id-card-body">
            <div class="name">{{ optional($student->user)->name }}</div>
            <div class="enroll-badge">{{ $student->enrollment_no }}</div>

            <div class="details">
                <div><strong>Course:</strong> {{ optional($student->course)->name }} (Semester {{ $student->current_semester }})</div>
                <div><strong>Department:</strong> {{ optional($student->department)->name }}</div>
                <div><strong>Batch Year:</strong> {{ $student->batch_year }}</div>
                <div><strong>Phone:</strong> {{ optional($student->user)->phone }}</div>
                <div><strong>Guardian:</strong> {{ $student->guardian_name }} ({{ $student->guardian_phone }})</div>
            </div>

            <div class="footer">Authorized Seal &amp; Signature</div>
        </div>
    </div>

</body>
</html>
