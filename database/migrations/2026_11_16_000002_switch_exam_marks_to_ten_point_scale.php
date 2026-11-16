<?php

use App\Models\ExamMark;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Grading moves from a 4-point to a 10-point scale.
 * decimal(3,2) can only hold up to 9.99, so the column is widened to
 * decimal(4,2), then every saved mark gets its grade recalculated from
 * its total marks.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exam_marks', function (Blueprint $table) {
            $table->decimal('grade_point', 4, 2)->default(0)->change();
        });

        DB::table('exam_marks')->orderBy('id')->each(function ($mark) {
            [$grade, $point] = ExamMark::gradeFor((float) $mark->total_marks);

            DB::table('exam_marks')->where('id', $mark->id)->update([
                'grade' => $grade,
                'grade_point' => $point,
            ]);
        });
    }

    public function down(): void
    {
        // Back to the old 4-point scale.
        $old = [90 => ['O', 4.00], 80 => ['A+', 3.80], 70 => ['A', 3.50], 60 => ['B+', 3.00], 50 => ['B', 2.50], 40 => ['C', 2.00], 0 => ['F', 0.00]];

        DB::table('exam_marks')->orderBy('id')->each(function ($mark) use ($old) {
            foreach ($old as $minimum => [$grade, $point]) {
                if ((float) $mark->total_marks >= $minimum) {
                    DB::table('exam_marks')->where('id', $mark->id)->update(['grade' => $grade, 'grade_point' => $point]);
                    break;
                }
            }
        });

        Schema::table('exam_marks', function (Blueprint $table) {
            $table->decimal('grade_point', 3, 2)->default(0)->change();
        });
    }
};
