<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\FeePayment;
use App\Models\FeeStructure;
use App\Support\UniqueCode;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    public function structures()
    {
        return response()->json(FeeStructure::with('course')->get());
    }

    public function storeStructure(Request $request)
    {
        $validated = $request->validate([
            'course_id' => 'required|exists:courses,id',
            'academic_year' => 'required|string',
            'tuition_fee' => 'required|numeric|min:0',
            'exam_fee' => 'required|numeric|min:0',
            'library_fee' => 'required|numeric|min:0',
            'other_fee' => 'required|numeric|min:0',
        ]);

        $validated['total_amount'] = $request->tuition_fee + $request->exam_fee + $request->library_fee + $request->other_fee;

        $structure = FeeStructure::updateOrCreate(['course_id' => $request->course_id], $validated);

        return response()->json(['message' => 'Fee structure updated successfully!', 'data' => $structure]);
    }

    public function payments(Request $request)
    {
        $query = FeePayment::with(['student.user', 'student.course']);

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        return response()->json($query->latest()->get());
    }

    public function collectPayment(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount_paid' => 'required|numeric|min:1',
            'payment_mode' => 'required|string',
            'payment_date' => 'required|date',
            'remarks' => 'nullable|string',
        ]);

        $payment = UniqueCode::create(function () use ($request) {
            $year = date('Y');
            $lastReceipt = FeePayment::where('receipt_no', 'like', "REC-{$year}-%")
                ->orderByDesc('id')->value('receipt_no');
            $receiptNo = sprintf('REC-%s-%05d', $year, UniqueCode::nextSequence($lastReceipt));

            return FeePayment::create([
                'student_id' => $request->student_id,
                'receipt_no' => $receiptNo,
                'amount_paid' => $request->amount_paid,
                'payment_mode' => $request->payment_mode,
                'payment_date' => $request->payment_date,
                'remarks' => $request->remarks,
            ]);
        });

        return response()->json([
            'message' => "Payment recorded! Receipt Generated: {$payment->receipt_no}",
            'data' => $payment,
        ], 201);
    }

    public function showReceipt(FeePayment $payment)
    {
        return response()->json($payment->load(['student.user', 'student.course', 'student.department']));
    }

    public function downloadReceiptPdf(FeePayment $payment)
    {
        $payment->load(['student.user', 'student.course', 'student.department']);
        $pdf = Pdf::loadView('fees.receipt_pdf', compact('payment'));

        return $pdf->download("Fee-Receipt-{$payment->receipt_no}.pdf");
    }

    public function myFees(Request $request)
    {
        $student = $request->user()->studentProfile;

        if (! $student) {
            return response()->json(['message' => 'No student profile linked to this account.'], 404);
        }

        return response()->json([
            'paid_fee' => $student->calculateTotalPaidFee(),
            'pending_dues' => $student->calculatePendingDues(),
            'payments' => FeePayment::where('student_id', $student->id)->latest()->get(),
        ]);
    }
}
