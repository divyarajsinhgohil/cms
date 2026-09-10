<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Daily Attendance Report</title>
    <style>
        @page { margin: 28px 34px; }
        body { font-family: DejaVu Sans, sans-serif; color: #172033; font-size: 10px; }
        h1 { margin: 0; color: #173b70; font-size: 21px; text-align: center; }
        .subtitle { margin: 4px 0 18px; color: #667085; text-align: center; }
        .meta, .summary { width: 100%; border-collapse: collapse; margin-bottom: 14px; }
        .meta td { width: 50%; padding: 4px 7px; border: 1px solid #d7deea; }
        .summary td { padding: 7px; border: 1px solid #d7deea; text-align: center; font-weight: bold; }
        .present { color: #087443; } .absent { color: #b42318; } .late { color: #a15c00; }
        .report { width: 100%; border-collapse: collapse; }
        .report th { padding: 7px 5px; color: #fff; background: #173b70; border: 1px solid #173b70; text-align: left; }
        .report td { padding: 6px 5px; border: 1px solid #d7deea; }
        .report tr:nth-child(even) td { background: #f7f9fc; }
        .center { text-align: center !important; }
        .footer { margin-top: 35px; width: 100%; }
        .footer td { width: 50%; padding-top: 25px; text-align: center; }
        .line { border-top: 1px solid #667085; padding-top: 5px; margin: 0 35px; }
        .generated { margin-top: 18px; color: #8a94a6; font-size: 8px; text-align: right; }
    </style>
</head>
<body>
    <h1>Daily Attendance Report</h1>
    <div class="subtitle">Official class attendance sheet</div>

    <table class="meta">
        <tr><td><strong>Course:</strong> {{ $course->name }} ({{ $course->code }})</td><td><strong>Semester:</strong> {{ $subject->semester }}</td></tr>
        <tr><td><strong>Subject:</strong> {{ $subject->name }} ({{ $subject->code }})</td><td><strong>Section:</strong> {{ $section ? $section->name : ($sectionId === 'unassigned' ? 'Unassigned' : 'All Sections') }}</td></tr>
        <tr><td><strong>Date:</strong> {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</td><td><strong>Total students:</strong> {{ $students->count() }}</td></tr>
    </table>

    <table class="summary">
        <tr>
            <td class="present">Present: {{ $summary['present'] }}</td>
            <td class="absent">Absent: {{ $summary['absent'] }}</td>
            <td class="late">Late: {{ $summary['late'] }}</td>
            <td>Unmarked: {{ $summary['unmarked'] }}</td>
        </tr>
    </table>

    <table class="report">
        <thead><tr><th class="center">Roll</th><th>Enrollment No.</th><th>Student Name</th><th class="center">Overall %</th><th class="center">Status</th></tr></thead>
        <tbody>
        @forelse($students as $student)
            @php
                $status = $statuses[$student->id] ?? 'unmarked';
                $percentage = $student->attendance_total > 0 ? round(($student->attendance_present / $student->attendance_total) * 100, 1) : 100;
            @endphp
            <tr>
                <td class="center">{{ $student->roll_no ?? '-' }}</td>
                <td>{{ $student->enrollment_no }}</td>
                <td>{{ optional($student->user)->name }}</td>
                <td class="center">{{ $percentage }}%</td>
                <td class="center {{ $status }}">{{ ucfirst($status) }}</td>
            </tr>
        @empty
            <tr><td colspan="5" class="center">No active students found for the selected filters.</td></tr>
        @endforelse
        </tbody>
    </table>

    <table class="footer"><tr><td><div class="line">Faculty Signature</div></td><td><div class="line">Head of Department</div></td></tr></table>
    <div class="generated">Generated on {{ now()->format('d M Y, h:i A') }}</div>
</body>
</html>
