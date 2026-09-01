<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('semester');
            $table->string('name'); // e.g. A, B, C
            $table->integer('capacity');
            $table->timestamps();
            $table->unique(['course_id', 'semester', 'name']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->string('status')->default('Active')->after('current_semester'); // Active, Left
            $table->integer('roll_no')->nullable()->after('status');
            $table->foreignId('section_id')->nullable()->after('roll_no')->constrained()->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
            $table->dropColumn(['status', 'roll_no']);
        });

        Schema::dropIfExists('sections');
    }
};
