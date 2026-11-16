<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamMark extends Model
{
    protected $fillable = [
        'student_id', 'subject_id', 'semester', 'internal_marks', 'external_marks',
        'total_marks', 'grade', 'grade_point'
    ];

    /**
     * 10-point grading scale (UGC CBCS style), out of 100 total marks.
     * Each row: minimum total => [grade letter, grade point].
     */
    public const GRADE_SCALE = [
        90 => ['O', 10],
        80 => ['A+', 9],
        70 => ['A', 8],
        60 => ['B+', 7],
        50 => ['B', 6],
        40 => ['C', 5],
        0 => ['F', 0],
    ];

    /**
     * Grade letter and grade point for a total out of 100.
     *
     * @return array{0: string, 1: int}
     */
    public static function gradeFor(float $total): array
    {
        foreach (self::GRADE_SCALE as $minimum => $grade) {
            if ($total >= $minimum) {
                return $grade;
            }
        }

        return ['F', 0];
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
