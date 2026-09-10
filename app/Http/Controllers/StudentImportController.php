<?php

namespace App\Http\Controllers;

use App\Services\StudentImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StudentImportController extends Controller
{
    public function __construct(private StudentImportService $importService) {}

    public function index()
    {
        return view('admissions.import');
    }

    public function template()
    {
        $filename = 'student-import-template.csv';

        return response($this->importService->sampleCsvContent(), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function preview(Request $request)
    {
        $request->validate(['import_file' => 'required|file|max:5120']);

        $parsed = $this->importService->parseFile($request->file('import_file'));
        if (! empty($parsed['errors']) && empty($parsed['rows'])) {
            $firstError = is_array($parsed['errors'][0] ?? null)
                ? implode(' ', $parsed['errors'][0])
                : implode(' ', $parsed['errors']);

            return redirect()->back()->with('error', $firstError);
        }

        $result = $this->importService->validateRows($parsed['rows'], dryRun: true);
        $storedPath = $request->file('import_file')->store('imports');

        return view('admissions.import', [
            'preview' => true,
            'headers' => $parsed['headers'],
            'validRows' => $result['valid'],
            'invalidRows' => $result['invalid'],
            'summary' => $result['summary'],
            'storedPath' => $storedPath,
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'stored_path' => 'required|string',
            'confirm' => 'required|accepted',
        ]);

        if (! Storage::exists($request->stored_path)) {
            return redirect()->route('admissions.import')->with('error', 'Import session expired. Please upload the file again.');
        }

        $fullPath = Storage::path($request->stored_path);
        $dummyFile = new \Illuminate\Http\UploadedFile($fullPath, basename($fullPath), null, null, true);
        $parsed = $this->importService->parseFile($dummyFile);

        $validated = $this->importService->validateRows($parsed['rows'], dryRun: false);
        if (! empty($validated['invalid'])) {
            return redirect()->route('admissions.import')->with('error', 'Import aborted: validation errors remain in the file.');
        }

        $result = $this->importService->importRows($validated['valid']);
        Storage::delete($request->stored_path);

        $message = "{$result['imported']} students imported successfully.";
        if (! empty($result['failed'])) {
            $message .= ' '.count($result['failed']).' row(s) failed.';
        }

        return redirect()->route('admissions.import')->with('success', $message)->with('import_failed', $result['failed'] ?? []);
    }

    public function export(Request $request)
    {
        $query = \App\Models\Student::with(['user', 'department', 'course', 'section']);
        $user = auth()->user();

        // Faculty stream scoping
        if ($user->isStaff() && $user->staffProfile) {
            $assignedCourseIds = $user->staffProfile->courses->pluck('id')->all();
            if (count($assignedCourseIds) > 0) {
                $query->whereIn('course_id', $assignedCourseIds);
            }
        }

        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }
        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }
        if ($request->filled('semester')) {
            $query->where('current_semester', $request->semester);
        }
        if ($request->filled('batch_year')) {
            $query->where('batch_year', $request->batch_year);
        }
        if ($request->filled('status') && $request->status !== 'All') {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('enrollment_no', 'LIKE', "%{$search}%")
                    ->orWhereHas('user', function ($u) use ($search) {
                        $u->where('name', 'LIKE', "%{$search}%")
                            ->orWhere('email', 'LIKE', "%{$search}%");
                    });
            });
        }

        $students = $query->orderBy('enrollment_no')->get();

        $filename = 'student_directory_'.date('Y-m-d_His').'.csv';
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Microsoft Excel compatibility
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($file, [
                'Enrollment No', 'Name', 'Email', 'Phone', 'Department Code',
                'Course Code', 'Stream Name', 'Batch Year', 'Semester',
                'Roll No', 'Section', 'Gender', 'Status',
            ]);

            foreach ($students as $s) {
                fputcsv($file, [
                    $s->enrollment_no,
                    $s->user->name ?? '',
                    $s->user->email ?? '',
                    $s->user->phone ?? '',
                    $s->department->code ?? '',
                    $s->course->code ?? '',
                    $s->course->name ?? '',
                    $s->batch_year,
                    $s->current_semester,
                    $s->roll_no ?? '',
                    $s->section->name ?? '',
                    $s->gender,
                    $s->status,
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
