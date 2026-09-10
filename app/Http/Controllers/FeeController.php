<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FeeStructure;
use App\Models\FeePayment;
use App\Models\Student;
use App\Models\Course;
use App\Models\Section;
use App\Support\UniqueCode;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\AlertDispatchService;

class FeeController extends Controller
{
    public function __construct(private AlertDispatchService $alerts) {}
    public function index(Request $request)
    {
        // Fees & Dues is a management-only module (see role:management on the route group)
        $courses = Course::all();

        $structures = FeeStructure::with('course')->get();

        $duesCourseId = $request->get('dues_course_id');
        $duesSemester = $request->get('dues_semester');

        // Student Fee Dues Ledger — filterable by stream/semester, paginated
        $duesQuery = Student::with(['user', 'course']);
        if ($duesCourseId) {
            $duesQuery->where('course_id', $duesCourseId);
        }
        if ($duesSemester) {
            $duesQuery->where('current_semester', $duesSemester);
        }
        $students = $duesQuery->latest()->paginate(15, ['*'], 'students_page')->withQueryString();

        // Payment Transactions Log — same course scoping, independently paginated
        $paymentsQuery = FeePayment::with(['student.user', 'student.course']);
        if ($duesCourseId) {
            $paymentsQuery->whereHas('student', fn ($q) => $q->where('course_id', $duesCourseId));
        }
        $payments = $paymentsQuery->latest()->paginate(15, ['*'], 'payments_page')->withQueryString();

        // Collect Payment modal: cascading Stream -> Semester -> Section ->
        // searchable student picker. Only Active students can receive a new
        // payment, and only the lightweight fields the picker needs are sent
        // to the browser (not full model/timestamps) to keep the page light
        // even with thousands of students.
        $modalStudents = Student::where('status', 'Active')
            ->with('user')
            ->get()
            ->map(fn ($s) => [
                'id' => $s->id,
                'name' => optional($s->user)->name,
                'enrollment_no' => $s->enrollment_no,
                'course_id' => $s->course_id,
                'semester' => $s->current_semester,
                'section_id' => $s->section_id,
            ])
            ->values();

        $modalSections = Section::orderBy('course_id')->orderBy('semester')->orderBy('name')->get(['id', 'course_id', 'semester', 'name']);

        return view('fees.index', compact(
            'structures', 'payments', 'students', 'courses',
            'duesCourseId', 'duesSemester', 'modalStudents', 'modalSections'
        ));
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

        FeeStructure::updateOrCreate(['course_id' => $request->course_id], $validated);

        return redirect()->back()->with('success', 'Fee structure updated successfully!');
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
            $receiptNo = sprintf("REC-%s-%05d", $year, UniqueCode::nextSequence($lastReceipt));

            return FeePayment::create([
                'student_id' => $request->student_id,
                'receipt_no' => $receiptNo,
                'amount_paid' => $request->amount_paid,
                'payment_mode' => $request->payment_mode,
                'payment_date' => $request->payment_date,
                'remarks' => $request->remarks,
            ]);
        });

        $payment->load('student.user');
        if ($payment->student) {
            $this->alerts->notifyFeePayment($payment->student, (float) $payment->amount_paid, $payment->receipt_no);

            // Automated WhatsApp Fee Receipt
            $settings = \App\Models\SiteSetting::current();
            if ($settings->whatsapp_auto_fee_receipt && optional($payment->student->user)->phone) {
                app(\App\Services\WhatsAppService::class)->sendDirectMessage(
                    $payment->student->user->phone,
                    "Dear {$payment->student->user->name}, fee payment of ₹".number_format($payment->amount_paid, 2)." received successfully! Receipt No: {$payment->receipt_no}. Thank you!",
                    $payment->student->user->name,
                    'fee_receipt'
                );
            }
            // Real-Time Broadcast to Student
            \App\Models\BroadcastMessage::dispatchSystemMessage(
                "💳 Fee Payment Receipt Issued: {$payment->receipt_no}",
                "Payment of ₹".number_format($payment->amount_paid, 2)." recorded successfully. Click to view fee receipt.",
                'student',
                'success',
                'toast',
                route('fees.receipt', $payment->id)
            );
        }

        return redirect()->route('fees.receipt', $payment->id)->with('success', "Payment recorded & broadcasted live! Receipt Generated: {$payment->receipt_no}");
    }

    public function viewReceipt(FeePayment $payment)
    {
        $payment->load(['student.user', 'student.course', 'student.department']);
        return view('fees.receipt', compact('payment'));
    }

    public function downloadReceiptPdf(FeePayment $payment)
    {
        $payment->load(['student.user', 'student.course', 'student.department']);
        $pdf = Pdf::loadView('fees.receipt_pdf', compact('payment'));

        return $pdf->download("Fee-Receipt-{$payment->receipt_no}.pdf");
    }
}
