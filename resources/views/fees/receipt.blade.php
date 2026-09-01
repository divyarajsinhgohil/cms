<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt - {{ $payment->receipt_no }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body { background: #f8fafc; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 2rem; }
        .receipt-card { background: white; border: 2px solid #e2e8f0; border-radius: 1rem; padding: 3rem; max-width: 800px; margin: 0 auto; box-shadow: 0 10px 25px rgba(0,0,0,0.05); }
        .header-logo { color: #4f46e5; }
        @media print {
            body { background: white; padding: 0; }
            .receipt-card { border: none; box-shadow: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

    <div class="no-print text-center mb-4">
        <button onclick="window.print()" class="btn btn-primary btn-lg shadow">
            <i class="fa-solid fa-print me-2"></i> Print Tax Receipt
        </button>
        <a href="{{ route('fees.receipt.pdf', $payment->id) }}" class="btn btn-success btn-lg shadow ms-2">
            <i class="fa-solid fa-file-arrow-down me-2"></i> Download PDF
        </a>
        <a href="{{ route('fees.index') }}" class="btn btn-outline-secondary btn-lg ms-2">Back to Fees</a>
    </div>

    <div class="receipt-card">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center border-bottom pb-4 mb-4">
            <div>
                <h2 class="fw-bold header-logo mb-1"><i class="fa-solid fa-graduation-cap me-2"></i> {{ $siteSettings->site_name ?? 'CMS College of Technology' }}</h2>
                <div class="text-muted small">{{ $siteSettings->affiliation ?? 'Affiliated to State University' }}{{ $siteSettings->address ? ' | '.$siteSettings->address : '' }}</div>
                <div class="text-muted small">Email: {{ $siteSettings->contact_email ?? 'accounts@cms.edu' }} | Phone: {{ $siteSettings->contact_phone ?? '+91 98765 43210' }}</div>
            </div>
            <div class="text-end">
                <span class="badge bg-success fs-6 text-uppercase px-3 py-2">Official Tax Receipt</span>
                <div class="fw-bold font-monospace mt-2 text-primary fs-5">{{ $payment->receipt_no }}</div>
                <div class="text-muted small">Date: {{ $payment->payment_date }}</div>
            </div>
        </div>

        <!-- Student Information -->
        <div class="row mb-4">
            <div class="col-6">
                <div class="text-uppercase text-muted fs-7 fw-bold mb-1">Student Details</div>
                <div class="fw-bold fs-5 text-dark">{{ optional($payment->student->user)->name }}</div>
                <div><strong>Enrollment No:</strong> <span class="font-monospace text-primary">{{ $payment->student->enrollment_no }}</span></div>
                <div><strong>Course:</strong> {{ optional($payment->student->course)->name }}</div>
                <div><strong>Department:</strong> {{ optional($payment->student->department)->name }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="text-uppercase text-muted fs-7 fw-bold mb-1">Payment Details</div>
                <div><strong>Payment Mode:</strong> {{ $payment->payment_mode }}</div>
                <div><strong>Academic Session:</strong> 2026-2027</div>
                <div><strong>Remarks:</strong> {{ $payment->remarks ?? 'N/A' }}</div>
            </div>
        </div>

        <!-- Amount Table -->
        <table class="table table-bordered mb-4">
            <thead class="table-light">
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount Paid (INR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Semester Tuition & Academic Fee Payment</td>
                    <td class="text-end font-monospace fw-bold">₹{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
                <tr class="table-light">
                    <td class="fw-bold text-end">Total Payment Received:</td>
                    <td class="text-end font-monospace fw-bold text-success fs-5">₹{{ number_format($payment->amount_paid, 2) }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Signatures -->
        <div class="row pt-5 mt-5 border-top">
            <div class="col-6">
                <div class="text-muted small">Computer-generated official receipt.</div>
                <div class="text-muted fs-7">Verification Hash: {{ md5($payment->receipt_no . $payment->created_at) }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="fw-bold text-dark mb-4">Authorized Accounts Signatory</div>
                <div class="text-muted fs-7">Finance Officer, CMS College</div>
            </div>
        </div>
    </div>

</body>
</html>
