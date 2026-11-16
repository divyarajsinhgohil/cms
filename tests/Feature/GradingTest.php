<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\ExamMark;
use App\Models\Student;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GradingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_staff_entering_marks_stores_ten_point_grade(): void
    {
        $student = Student::firstOrFail();
        $subject = Subject::firstOrFail();

        $this->actingAs(User::where('role', 'staff')->firstOrFail())
            ->post('/exams/store', [
                'student_id' => $student->id,
                'subject_id' => $subject->id,
                'semester' => 1,
                'internal_marks' => 38,
                'external_marks' => 57,
            ]);

        $mark = ExamMark::where('student_id', $student->id)->where('subject_id', $subject->id)->where('semester', 1)->firstOrFail();
        $this->assertSame('O', $mark->grade);
        $this->assertEquals(10, (float) $mark->grade_point);
    }

    public function test_cgpa_is_weighted_by_subject_credits(): void
    {
        $student = Student::firstOrFail();
        $student->examMarks()->delete();
        $course = Course::firstOrFail();

        $heavy = Subject::create(['course_id' => $course->id, 'semester' => 1, 'name' => 'Heavy', 'code' => 'HV1', 'credits' => 4]);
        $light = Subject::create(['course_id' => $course->id, 'semester' => 1, 'name' => 'Light', 'code' => 'LT1', 'credits' => 2]);

        foreach ([[$heavy, 95], [$light, 55]] as [$subject, $total]) {
            [$grade, $point] = ExamMark::gradeFor($total);
            ExamMark::create([
                'student_id' => $student->id, 'subject_id' => $subject->id, 'semester' => 1,
                'internal_marks' => 0, 'external_marks' => $total, 'total_marks' => $total,
                'grade' => $grade, 'grade_point' => $point,
            ]);
        }

        // (10 x 4 + 6 x 2) / 6 = 8.67 (a plain average would wrongly give 8.00)
        $this->assertEquals(8.67, $student->fresh()->calculateCGPA());
    }

    public function test_marksheet_shows_ten_point_cgpa(): void
    {
        $student = Student::whereHas('examMarks')->firstOrFail();

        $this->actingAs(User::where('role', 'management')->firstOrFail())
            ->get("/exams/marksheet/{$student->id}")
            ->assertOk()
            ->assertSee('/ 10.00')
            ->assertDontSee('/ 4.00');
    }
}
