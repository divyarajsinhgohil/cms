<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            // Break slots have no subject/faculty; classroom is no longer tracked at all.
            $table->foreignId('subject_id')->nullable()->change();
            $table->foreignId('staff_id')->nullable()->change();
            $table->string('classroom')->nullable()->change();

            $table->string('type')->default('Lecture')->after('staff_id'); // Lecture, Lab, Break
            $table->string('label')->nullable()->after('type'); // free text used only for Break slots
            $table->date('specific_date')->nullable()->after('day_of_week'); // one-off override for a single date
        });
    }

    public function down(): void
    {
        Schema::table('timetables', function (Blueprint $table) {
            $table->dropColumn(['type', 'label', 'specific_date']);
        });
    }
};
