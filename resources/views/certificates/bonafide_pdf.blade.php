<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bonafide Certificate - {{ optional($student->user)->name }}</title>
    <style>
        @page { margin: 25px; }
        body { font-family: 'DejaVu Sans', sans-serif; color: #1e293b; font-size: 13px; line-height: 1.6; }
        .cert-border { border: 3px double #6d3fa0; padding: 30px; border-radius: 8px; }
        .header-table { width: 100%; border-bottom: 2px solid #6d3fa0; padding-bottom: 15px; margin-bottom: 20px; }
        .header-logo { color: #6d3fa0; font-size: 22px; font-weight: bold; }
        .title-box { text-align: center; margin: 15px 0; }
        .title-text { font-size: 16px; font-weight: bold; text-transform: uppercase; border-top: 1px solid #1e293b; border-bottom: 1px solid #1e293b; padding: 6px 15px; display: inline-block; }
        .student-photo { width: 100px; height: 120px; border: 2px solid #6d3fa0; border-radius: 6px; }
        .reason-box { background-color: #f1f5f9; border-left: 4px solid #6d3fa0; padding: 10px 15px; margin: 15px 0; border-radius: 4px; }
        .footer-table { width: 100%; margin-top: 40px; border-top: 1px solid #cbd5e1; padding-top: 15px; }
        .text-muted { color: #64748b; font-size: 11px; }
        .text-primary { color: #6d3fa0; font-weight: bold; }
    </style>
</head>
<body>

    <div class="cert-border">
        <!-- Header Row -->
        <table class="header-table">
            <tr>
                <td style="width: 70%;">
                    <div class="header-logo">{{ $siteSettings->site_name ?? 'CMS College of Technology' }}</div>
                    <div class="text-muted">{{ $siteSettings->affiliation ?? 'Affiliated to State University' }}</div>
                    <div class="text-muted">{{ $siteSettings->address ?? 'Campus Road, Institutional Area' }}</div>
                    <div class="text-muted">Email: {{ $siteSettings->contact_email ?? 'info@cms.edu' }} | Phone: {{ $siteSettings->contact_phone ?? '+91 98765 43210' }}</div>
                </td>
                <td style="width: 30%; text-align: right; vertical-align: top;">
                    <div style="background: #6d3fa0; color: #ffffff; padding: 4px 8px; font-size: 11px; font-weight: bold; display: inline-block; border-radius: 4px;">OFFICIAL BONAFIDE</div>
                    <div style="font-family: monospace; color: #6d3fa0; font-weight: bold; margin-top: 5px;">CMS/BON/2026/{{ sprintf('%04d', $student->id) }}</div>
                    <div class="text-muted">Date: {{ date('F d, Y') }}</div>
                </td>
            </tr>
        </table>

        <!-- Title & Student Photo Row -->
        <table style="width: 100%; margin-bottom: 15px;">
            <tr>
                <td style="vertical-align: middle;">
                    <div class="title-text">BONAFIDE & BENEFIT CLAIM CERTIFICATE</div>
                    <div class="text-muted" style="margin-top: 5px;">Academic Year: {{ $siteSettings->academic_year ?? date('Y').'-'.(date('Y')+1) }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    @if($photoBase64)
                        <img src="{{ $photoBase64 }}" class="student-photo" alt="Student Photo">
                    @else
                        <div style="width: 90px; height: 110px; border: 2px dashed #94a3b8; border-radius: 6px; text-align: center; line-height: 110px; color: #94a3b8; font-size: 10px;">[Photo]</div>
                    @endif
                </td>
            </tr>
        </table>

        <!-- Certificate Main Body -->
        <div style="font-size: 14px; line-height: 2; margin: 20px 0;">
            <p>
                This is to certify that <strong>{{ optional($student->user)->name }}</strong>, 
                holding Enrollment Number <span class="text-primary">{{ $student->enrollment_no }}</span>, 
                is a bonafide student of this institution pursuing the 
                <strong>{{ optional($student->course)->name }} ({{ optional($student->course)->code }})</strong> program 
                in the Department of <strong>{{ optional($student->department)->name }}</strong>.
            </p>
            <p>
                He / She is currently studying in <strong>Semester {{ $student->current_semester }}</strong> 
                for the Academic Batch Year <strong>{{ $student->batch_year }}</strong>. 
                His / Her conduct, academic standing, and character during the period of study have been found to be <strong>Exemplary</strong>.
            </p>
        </div>

        <!-- Purpose / Benefit Claim Reason -->
        <div class="reason-box">
            <div style="font-weight: bold; color: #6d3fa0; font-size: 11px; text-transform: uppercase;">Purpose / Benefit Claim Reason:</div>
            <div style="font-size: 14px; font-weight: bold; margin-top: 3px;">{{ $reason }}</div>
            <div class="text-muted" style="margin-top: 2px;">This certificate is issued upon student's request for official submission towards the purpose stated above.</div>
        </div>

        <!-- Footer Signatures -->
        <table class="footer-table">
            <tr>
                <td style="vertical-align: top;">
                    <div style="font-weight: bold;">Date of Issue: {{ date('F d, Y') }}</div>
                    <div class="text-muted">Ref: CMS/BON/2026/{{ sprintf('%04d', $student->id) }}</div>
                    <div class="text-muted" style="font-size: 9px; margin-top: 4px;">Hash: {{ md5($student->enrollment_no . $reason . date('Y-m-d')) }}</div>
                </td>
                <td style="text-align: right; vertical-align: top;">
                    <div style="border-bottom: 1px solid #1e293b; padding-bottom: 4px; font-weight: bold; display: inline-block;">Dr. Robert Vance</div>
                    <div style="font-weight: bold; margin-top: 4px;">Principal / Executive Director</div>
                    <div class="text-muted">{{ $siteSettings->site_name ?? 'CMS College Management' }}</div>
                </td>
            </tr>
        </table>
    </div>

</body>
</html>
