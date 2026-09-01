<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'user_id', 'enrollment_no', 'department_id', 'course_id', 'batch_year',
        'current_semester', 'gender', 'dob', 'address', 'guardian_name', 'guardian_phone',
        'status', 'roll_no', 'section_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function isActive(): bool
    {
        return $this->status === 'Active';
    }

    /**
     * Recompute roll numbers 1..N alphabetically by student name, restarting
     * at 1 within each class section (students with no section yet share one
     * "unassigned" pool) — called whenever a stream+semester roster changes
     * (a student leaves, a new one is admitted, a semester promotion happens,
     * or sections are re-divided).
     */
    public static function regenerateRollNumbers(int $courseId, int $semester): void
    {
        static::where('course_id', $courseId)
            ->where('current_semester', $semester)
            ->where('status', 'Active')
            ->with('user')
            ->get()
            ->groupBy('section_id')
            ->each(function ($sectionStudents) {
                $sectionStudents
                    ->sortBy(fn ($s) => strtolower(optional($s->user)->name ?? ''))
                    ->values()
                    ->each(fn ($student, $index) => $student->update(['roll_no' => $index + 1]));
            });
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function feePayments()
    {
        return $this->hasMany(FeePayment::class);
    }

    public function examMarks()
    {
        return $this->hasMany(ExamMark::class);
    }

    public function calculateAttendancePercentage()
    {
        $total = $this->attendances()->count();
        if ($total === 0) return 100;
        $present = $this->attendances()->whereIn('status', ['present', 'late'])->count();
        return round(($present / $total) * 100, 1);
    }

    public function calculateTotalPaidFee()
    {
        return $this->feePayments()->sum('amount_paid');
    }

    public function calculatePendingDues()
    {
        $totalFee = optional($this->course->feeStructure)->total_amount ?? 0;
        return max(0, $totalFee - $this->calculateTotalPaidFee());
    }

    public function calculateCGPA()
    {
        $marks = $this->examMarks;
        if ($marks->isEmpty()) return 0.00;
        return round($marks->avg('grade_point'), 2);
    }
}
