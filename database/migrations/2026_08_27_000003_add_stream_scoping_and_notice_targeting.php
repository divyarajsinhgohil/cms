<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Assigns one or more streams (courses) to a faculty/staff profile.
        Schema::create('staff_courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_profile_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            $table->unique(['staff_profile_id', 'course_id']);
        });

        // Lets a notice optionally target one specific stream + semester.
        Schema::table('notices', function (Blueprint $table) {
            $table->foreignId('course_id')->nullable()->after('target_audience')->constrained()->nullOnDelete();
            $table->unsignedTinyInteger('semester')->nullable()->after('course_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notices', function (Blueprint $table) {
            $table->dropConstrainedForeignId('course_id');
            $table->dropColumn('semester');
        });

        Schema::dropIfExists('staff_courses');
    }
};
