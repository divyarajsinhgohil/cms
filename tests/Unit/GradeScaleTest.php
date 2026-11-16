<?php

namespace Tests\Unit;

use App\Models\ExamMark;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class GradeScaleTest extends TestCase
{
    public static function bands(): array
    {
        return [
            'top mark' => [100, 'O', 10],
            'O boundary' => [90, 'O', 10],
            'just below O' => [89.5, 'A+', 9],
            'A+ boundary' => [80, 'A+', 9],
            'A boundary' => [70, 'A', 8],
            'B+ boundary' => [60, 'B+', 7],
            'B boundary' => [50, 'B', 6],
            'pass boundary' => [40, 'C', 5],
            'just failed' => [39.5, 'F', 0],
            'zero' => [0, 'F', 0],
        ];
    }

    #[DataProvider('bands')]
    public function test_grade_for_total(float $total, string $grade, int $point): void
    {
        $this->assertSame([$grade, $point], ExamMark::gradeFor($total));
    }
}
