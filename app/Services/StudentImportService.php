<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Department;
use App\Models\Student;
use App\Models\User;
use App\Support\UniqueCode;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class StudentImportService
{
    public const HEADERS = [
        'name', 'email', 'phone', 'department_code', 'course_code',
        'batch_year', 'semester', 'gender', 'dob', 'guardian_name',
        'guardian_phone', 'address',
    ];

    public const REQUIRED = [
        'name', 'email', 'phone', 'department_code', 'course_code',
        'batch_year', 'semester', 'gender',
    ];

    /** @return array{headers: array, rows: array<int, array>, errors: array<int, array<string>>} */
    public function parseFile(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        if (! in_array($extension, ['csv', 'txt', 'xlsx', 'xls'], true)) {
            return [
                'headers' => [],
                'rows' => [],
                'errors' => [0 => ['Supported formats: CSV, TXT, Excel CSV. Save Excel spreadsheet as CSV (UTF-8) before uploading.']],
            ];
        }

        $filePath = $file->getRealPath();
        $content = @file_get_contents($filePath);
        if ($content === false) {
            return ['headers' => [], 'rows' => [], 'errors' => [0 => ['Unable to read the uploaded file.']]];
        }

        // Strip UTF-8 BOM if present (common in Excel CSV exports)
        $content = preg_replace('/\x{EF}\x{BB}\x{BF}/', '', $content);

        // Auto-detect delimiter (comma, semicolon, tab)
        $firstLine = strtok($content, "\r\n");
        $delimiter = ',';
        if ($firstLine !== false) {
            if (substr_count($firstLine, ';') > substr_count($firstLine, ',')) {
                $delimiter = ';';
            } elseif (substr_count($firstLine, "\t") > substr_count($firstLine, ',')) {
                $delimiter = "\t";
            }
        }

        $lines = explode("\n", str_replace("\r", '', $content));
        if (empty($lines) || trim($lines[0]) === '') {
            return ['headers' => [], 'rows' => [], 'errors' => [0 => ['The file is empty.']]];
        }

        $rawHeaders = str_getcsv($lines[0], $delimiter);
        if (! $rawHeaders) {
            return ['headers' => [], 'rows' => [], 'errors' => [0 => ['Unable to parse column headers.']]];
        }

        $headers = array_map(fn ($h) => $this->normalizeHeader(trim((string) $h)), $rawHeaders);
        $rows = [];

        for ($i = 1; $i < count($lines); $i++) {
            $lineNum = $i + 1;
            $lineStr = trim($lines[$i]);
            if ($lineStr === '') {
                continue;
            }
            $data = str_getcsv($lineStr, $delimiter);
            if ($this->rowIsEmpty($data)) {
                continue;
            }
            $row = [];
            foreach ($headers as $idx => $header) {
                $row[$header] = isset($data[$idx]) ? trim((string) $data[$idx]) : '';
            }
            $rows[$lineNum] = $row;
        }

        return ['headers' => $headers, 'rows' => $rows, 'errors' => $this->validateHeaders($headers)];
    }

    private function normalizeHeader(string $headerName): string
    {
        $clean = strtolower(trim($headerName));

        return match ($clean) {
            'name', 'full name', 'student name', 'user name' => 'name',
            'email', 'email address', 'email id' => 'email',
            'phone', 'mobile', 'mobile no', 'phone number', 'contact' => 'phone',
            'department_code', 'dept_code', 'department code', 'dept', 'department' => 'department_code',
            'course_code', 'stream_code', 'course code', 'stream', 'course', 'degree' => 'course_code',
            'batch_year', 'batch', 'year', 'batch year' => 'batch_year',
            'semester', 'sem', 'current_semester' => 'semester',
            'gender', 'sex' => 'gender',
            'dob', 'date of birth', 'birth date' => 'dob',
            'guardian_name', 'guardian', 'father name', 'parent name' => 'guardian_name',
            'guardian_phone', 'guardian mobile', 'parent phone' => 'guardian_phone',
            'address', 'city', 'location' => 'address',
            default => Str::snake(trim($headerName)),
        };
    }

    /**
     * @param  array<int, array<string, string>>  $rows
     * @return array{valid: array<int, array>, invalid: array<int, array<string>>, summary: array}
     */
    public function validateRows(array $rows, bool $dryRun = true): array
    {
        $valid = [];
        $invalid = [];
        $departments = Department::pluck('id', 'code')->mapWithKeys(fn ($id, $code) => [strtoupper($code) => $id]);
        $courses = Course::all()->keyBy(fn ($c) => strtoupper($c->code));
        $emailsInFile = [];
        $summary = ['total' => count($rows), 'valid' => 0, 'invalid' => 0];

        foreach ($rows as $line => $row) {
            $errors = $this->validateRow($row, $departments, $courses, $emailsInFile);
            if ($errors) {
                $invalid[$line] = $errors;
                $summary['invalid']++;
            } else {
                $valid[$line] = $row;
                $emailsInFile[] = strtolower($row['email']);
                $summary['valid']++;
            }
        }

        return compact('valid', 'invalid', 'summary') + ['dry_run' => $dryRun];
    }

    /**
     * @param  array<int, array<string, string>>  $validRows
     * @return array{imported: int, failed: array<int, string>}
     */
    public function importRows(array $validRows): array
    {
        $imported = 0;
        $failed = [];
        $departments = Department::all()->keyBy(fn ($d) => strtoupper($d->code));
        $courses = Course::all()->keyBy(fn ($c) => strtoupper($c->code));

        foreach ($validRows as $line => $row) {
            try {
                $dept = $departments->get(strtoupper($row['department_code']));
                $course = $courses->get(strtoupper($row['course_code']));

                $user = User::create([
                    'name' => $row['name'],
                    'email' => $row['email'],
                    'password' => Hash::make('password'),
                    'role' => 'student',
                    'phone' => $row['phone'],
                    'must_change_password' => true,
                ]);

                UniqueCode::create(function () use ($row, $dept, $course, $user) {
                    Student::create([
                        'user_id' => $user->id,
                        'enrollment_no' => sprintf(
                            'CMS-%d-%s-%05d',
                            (int) $row['batch_year'],
                            strtoupper($dept->code),
                            UniqueCode::nextSequence(
                                Student::where('department_id', $dept->id)->orderByDesc('id')->value('enrollment_no')
                            )
                        ),
                        'department_id' => $dept->id,
                        'course_id' => $course->id,
                        'batch_year' => (int) $row['batch_year'],
                        'current_semester' => (int) $row['semester'],
                        'gender' => $row['gender'],
                        'dob' => $row['dob'] ?: null,
                        'guardian_name' => $row['guardian_name'] ?: null,
                        'guardian_phone' => $row['guardian_phone'] ?: null,
                        'address' => $row['address'] ?: null,
                        'status' => 'Active',
                    ]);
                });

                $imported++;
            } catch (\Throwable $e) {
                $failed[$line] = $e->getMessage();
            }
        }

        // Regenerate roll numbers per affected course/semester
        $touched = collect($validRows)->map(fn ($r) => [
            'course_id' => $courses->get(strtoupper($r['course_code']))?->id,
            'semester' => (int) $r['semester'],
        ])->filter(fn ($t) => $t['course_id'])->unique(fn ($t) => $t['course_id'].'-'.$t['semester']);

        foreach ($touched as $t) {
            Student::regenerateRollNumbers($t['course_id'], $t['semester']);
        }

        return compact('imported', 'failed');
    }

    public function sampleCsvContent(): string
    {
        $headers = implode(',', self::HEADERS);
        $sample = 'Rahul Sharma,rahul@example.com,9876543210,CS,BCA,2026,1,Male,2005-01-15,Mr Sharma,9876543211,Ahmedabad';

        return $headers."\n".$sample."\n";
    }

    /** @return array<string> */
    private function validateHeaders(array $headers): array
    {
        $missing = array_diff(self::REQUIRED, $headers);
        if ($missing) {
            return ['Missing required columns: '.implode(', ', $missing)];
        }

        return [];
    }

    /** @return array<string> */
    private function validateRow(array $row, $departments, $courses, array $emailsInFile): array
    {
        $errors = [];

        foreach (self::REQUIRED as $field) {
            if (empty($row[$field] ?? '')) {
                $errors[] = "Missing required field: {$field}";
            }
        }

        if (! empty($row['email'])) {
            if (! filter_var($row['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Invalid email format.';
            } elseif (User::where('email', $row['email'])->exists()) {
                $errors[] = 'Email already exists in the system.';
            } elseif (in_array(strtolower($row['email']), $emailsInFile, true)) {
                $errors[] = 'Duplicate email within this file.';
            }
        }

        $deptCode = strtoupper($row['department_code'] ?? '');
        if ($deptCode && ! $departments->has($deptCode)) {
            $errors[] = "Unknown department code: {$row['department_code']}";
        }

        $courseCode = strtoupper($row['course_code'] ?? '');
        if ($courseCode && ! $courses->has($courseCode)) {
            $errors[] = "Unknown course code: {$row['course_code']}";
        }

        if (! empty($row['semester']) && ((int) $row['semester'] < 1 || (int) $row['semester'] > 8)) {
            $errors[] = 'Semester must be between 1 and 8.';
        }

        if (! empty($row['batch_year']) && ((int) $row['batch_year'] < 2000 || (int) $row['batch_year'] > 2100)) {
            $errors[] = 'Batch year looks invalid.';
        }

        if (! empty($row['dob']) && ! strtotime($row['dob'])) {
            $errors[] = 'Date of birth must be a valid date (YYYY-MM-DD).';
        }

        return $errors;
    }

    private function rowIsEmpty(array $data): bool
    {
        return count(array_filter($data, fn ($v) => trim((string) $v) !== '')) === 0;
    }
}
