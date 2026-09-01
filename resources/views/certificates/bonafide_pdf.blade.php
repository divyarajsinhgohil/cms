<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bonafide Certificate - {{ optional($student->user)->name }}</title>
    <style>
        body { font-family: 'Times New Roman', 'Georgia', serif; color: #1e293b; font-size: 14px; }
        .cert-container { border: 8px solid #1e3c72; padding: 40px; }
        .header { text-align: center; border-bottom: 1px solid #1e3c72; padding-bottom: 16px; margin-bottom: 16px; }
        .header h2 { color: #1e3c72; text-transform: uppercase; margin: 0 0 6px 0; }
        .muted { color: #64748b; font-size: 11px; font-family: Helvetica, Arial, sans-serif; }
        .title { text-align: center; text-transform: uppercase; letter-spacing: 2px; border-top: 1px solid #1e293b; border-bottom: 1px solid #1e293b; padding: 8px 0; margin: 24px 0; }
        .body-text { line-height: 2; margin: 20px 0; text-align: justify; }
        .footer-table { width: 100%; margin-top: 60px; }
        .footer-table td { vertical-align: bottom; font-family: Helvetica, Arial, sans-serif; font-size: 11px; }
        .sig { border-top: 1px solid #1e293b; padding-top: 6px; font-weight: bold; text-align: center; }
        .photo-box { text-align: center; }
        .photo-box img { width: 90px; height: 90px; object-fit: cover; border: 1px solid #1e3c72; }
    </style>
</head>
<body>

    <div class="cert-container">
        <table style="width: 100%;">
            <tr>
                <td style="width: 90px;" class="photo-box">
                    <img src="{{ optional($student->user)->avatar ?? 'https://ui-avatars.com/api/?name='.urlencode(optional($student->user)->name) }}">
                </td>
                <td class="header" style="border-bottom: none; padding-left: 16px;">
                    <h2>{{ $siteSettings->site_name ?? 'CMS College Management System' }}</h2>
                    <div class="muted">{{ $siteSettings->affiliation ?? 'Affiliated to State University' }}</div>
                    <div class="muted">{{ $siteSettings->address ?? 'Campus Road, Institutional Area, Pin: 380001' }}</div>
                </td>
            </tr>
        </table>
        <div style="border-bottom: 1px solid #1e3c72; margin-bottom: 16px;"></div>

        <h3 class="title">Bonafide Certificate</h3>

        <div class="body-text">
            <p>
                This is to certify that <strong>{{ optional($student->user)->name }}</strong>,
                holding Enrollment Number <strong>{{ $student->enrollment_no }}</strong>,
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

        <table class="footer-table">
            <tr>
                <td width="50%">
                    <strong>Date of Issue:</strong> {{ date('F d, Y') }}<br>
                    <span class="muted">Certificate Ref: CMS/BON/2026/{{ sprintf('%04d', $student->id) }}</span>
                </td>
                <td width="50%">
                    <div class="sig">Dr. Robert Vance</div>
                    <div style="text-align:center;">Principal / Executive Director</div>
                    <div class="muted" style="text-align:center;">{{ $siteSettings->site_name ?? 'CMS College Management' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
