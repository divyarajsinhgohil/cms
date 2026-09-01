<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt - {{ $payment->receipt_no }}</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; color: #1e293b; font-size: 13px; }
        .header { border-bottom: 2px solid #4f46e5; padding-bottom: 12px; margin-bottom: 16px; }
        .header table { width: 100%; }
        .college-name { color: #4f46e5; font-size: 20px; font-weight: bold; margin: 0; }
        .muted { color: #64748b; font-size: 11px; }
        .badge { display: inline-block; background: #16a34a; color: #fff; padding: 4px 10px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .info-table { width: 100%; margin-bottom: 16px; }
        .info-table td { vertical-align: top; padding: 2px 0; }
        .amount-table { width: 100%; border-collapse: collapse; margin-bottom: 24px; }
        .amount-table th, .amount-table td { border: 1px solid #cbd5e1; padding: 8px; }
        .amount-table th { background: #f1f5f9; text-align: left; }
        .text-end { text-align: right; }
        .total-row td { font-weight: bold; background: #f8fafc; }
        .footer-table { width: 100%; margin-top: 40px; }
        .footer-table td { vertical-align: bottom; font-size: 11px; }
        .sig { border-top: 1px solid #1e293b; padding-top: 4px; font-weight: bold; }
    </style>
</head>
<body>

    <div class="header">
        <table>
            <tr>
                <td>
                    <p class="college-name">{{ $siteSettings->site_name ?? 'CMS College of Technology' }}</p>
                    <div class="muted">{{ $siteSettings->affiliation ?? 'Affiliated to State University' }}{{ $siteSettings->address ? ' | '.$siteSettings->address : '' }}</div>
                    <div class="muted">Email: {{ $siteSettings->contact_email ?? 'accounts@cms.edu' }} | Phone: {{ $siteSettings->contact_phone ?? '+91 98765 43210' }}</div>
                </td>
                <td class="text-end">
                    <span class="badge">Official Tax Receipt</span><br><br>
                    <strong>{{ $payment->receipt_no }}</strong><br>
                    <span class="muted">Date: {{ $payment->payment_date }}</span>
                </td>
            </tr>
        </table>
    </div>

    <table class="info-table">
        <tr>
            <td width="50%">
                <div class="muted">STUDENT DETAILS</div>
                <strong>{{ optional($payment->student->user)->name }}</strong><br>
                Enrollment No: {{ $payment->student->enrollment_no }}<br>
                Course: {{ optional($payment->student->course)->name }}<br>
                Department: {{ optional($payment->student->department)->name }}
            </td>
            <td width="50%" class="text-end">
                <div class="muted">PAYMENT DETAILS</div>
                Payment Mode: {{ $payment->payment_mode }}<br>
                Academic Session: 2026-2027<br>
                Remarks: {{ $payment->remarks ?? 'N/A' }}
            </td>
        </tr>
    </table>

    <table class="amount-table">
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-end">Amount Paid (INR)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Semester Tuition &amp; Academic Fee Payment</td>
                <td class="text-end">Rs. {{ number_format($payment->amount_paid, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td class="text-end">Total Payment Received:</td>
                <td class="text-end">Rs. {{ number_format($payment->amount_paid, 2) }}</td>
            </tr>
        </tbody>
    </table>

    <table class="footer-table">
        <tr>
            <td width="50%">
                Computer-generated official receipt.<br>
                <span class="muted">Verification Hash: {{ md5($payment->receipt_no . $payment->created_at) }}</span>
            </td>
            <td width="50%" class="text-end">
                <div class="sig" style="display:inline-block; padding: 4px 20px;">Authorized Accounts Signatory</div><br>
                <span class="muted">Finance Officer, CMS College</span>
            </td>
        </tr>
    </table>

</body>
</html>
