<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;

class CertificateController extends Controller
{
    public function index()
    {
        return response()->json(Student::with(['user', 'department', 'course'])->latest()->get());
    }

    public function idCard(Student $student)
    {
        return response()->json($student->load(['user', 'department', 'course']));
    }

    public function idCardPdf(Student $student)
    {
        $student->load(['user', 'department', 'course']);
        $pdf = Pdf::loadView('certificates.id_card_pdf', compact('student'));

        return $pdf->download("ID-Card-{$student->enrollment_no}.pdf");
    }

    public function bonafide(Student $student)
    {
        return response()->json($student->load(['user', 'department', 'course']));
    }

    public function bonafidePdf(Student $student)
    {
        $student->load(['user', 'department', 'course']);
        $pdf = Pdf::loadView('certificates.bonafide_pdf', compact('student'));

        return $pdf->download("Bonafide-Certificate-{$student->enrollment_no}.pdf");
    }
}
