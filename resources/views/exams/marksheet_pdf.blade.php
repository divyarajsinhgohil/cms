<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Official Marksheet - {{ $student->enrollment_no }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #1e293b; font-size: 13px; }
        .banner { text-align: center; border-bottom: 3px double #1e1b4b; padding-bottom: 14px; margin-bottom: 16px; }
        .banner h2 { color: #1e1b4b; margin: 4px 0; }
        .muted { color: #64748b; font-size: 11px; }
        .badge { display: inline-block; background: #4f46e5; color: #fff; padding: 4px 12px; font-size: 11px; font-weight: bold; text-transform: uppercase; margin-top: 6px; }
        .profile-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 10px; margin-bottom: 16px; width: 100%; }
        .profile-box td { vertical-align: top; padding: 2px 0; }
        .marks-table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        .marks-table th, .marks-table td { border: 1px solid #94a3b8; padding: 6px; text-align: center; }
        .marks-table th { background: #1e293b; color: #fff; }
        .marks-table td.subject { text-align: left; font-weight: bold; }
        .summary-box { background: #f8fafc; border: 1px solid #cbd5e1; padding: 12px; margin-bottom: 30px; width: 100%; }
        .summary-box td { vertical-align: middle; }
        .result-pass { color: #16a34a; font-weight: bold; font-size: 15px; }
        .cgpa { color: #4f46e5; font-weight: bold; font-size: 18px; }
        .text-end { text-align: right; }
        .sig-table { width: 100%; border-top: 1px solid #1e293b; padding-top: 10px; text-align: center; }
        .sig-table td { border-top: 1px solid #1e293b; padding-top: 6px; font-weight: bold; font-size: 12px; }
    </style>
</head>
<body>

    <div class="banner">
        <h2>{{ strtoupper($siteSettings->site_name ?? 'CMS College of Technology') }}</h2>
        <div class="muted">{{ strtoupper($siteSettings->affiliation ?? 'Affiliated to State Technological University') }}</div>
        <span class="badge">Semester Examination Statement of Marks</span>
    </div>

    <table class="profile-box">
        <tr>
            <td width="50%">
                <strong>Student Name:</strong> {{ optional($student->user)->name }}<br>
                <strong>Enrollment Number:</strong> {{ $student->enrollment_no }}<br>
                <strong>Degree Program:</strong> {{ optional($student->course)->name }}
            </td>
            <td width="50%" class="text-end">
                <strong>Department:</strong> {{ optional($student->department)->name }}<br>
                <strong>Current Semester:</strong> Semester {{ $student->current_semester }}<br>
                <strong>Academic Batch:</strong> {{ $student->batch_year }}
            </td>
        </tr>
    </table>

    <table class="marks-table">
        <thead>
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
                <td>{{ optional($m->subject)->code }}</td>
                <td class="subject">{{ optional($m->subject)->name }}</td>
                <td>{{ $m->internal_marks }}</td>
                <td>{{ $m->external_marks }}</td>
                <td>{{ $m->total_marks }}</td>
                <td>{{ $m->grade }}</td>
                <td>{{ number_format($m->grade_point, 2) }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">No examination results recorded for this student.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="summary-box">
        <tr>
            <td width="50%">
                <div class="muted">OVERALL PERFORMANCE RESULT</div>
                <span class="result-pass">PASSED WITH DISTINCTION</span>
            </td>
            <td width="50%" class="text-end">
                <div class="muted">CUMULATIVE GRADE POINT AVERAGE (CGPA)</div>
                <span class="cgpa">{{ number_format($cgpa, 2) }} / 10.00</span>
            </td>
        </tr>
    </table>

    <table class="sig-table">
        <tr>
            <td width="33%">Prepared By<br><span class="muted" style="font-weight: normal;">Academic Coordinator</span></td>
            <td width="33%">Verified By<br><span class="muted" style="font-weight: normal;">Head of Department</span></td>
            <td width="33%">Controller of Examinations<br><span class="muted" style="font-weight: normal;">CMS University Seal</span></td>
        </tr>
    </table>

</body>
</html>
