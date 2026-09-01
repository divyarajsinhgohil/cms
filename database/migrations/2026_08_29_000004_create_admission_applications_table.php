<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admission_applications', function (Blueprint $table) {
            $table->id();

            // Applicant identity
            $table->string('surname');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('mother_name');
            $table->string('gender');
            $table->string('aadhar_number', 20);
            $table->string('photo_path')->nullable();
            $table->string('aadhar_photo_path')->nullable();

            // Category & reservation proof
            $table->string('category')->default('General'); // General, SC, ST, OBC
            $table->string('caste_certificate_path')->nullable();

            // Contact
            $table->string('mobile');
            $table->string('father_mobile')->nullable();
            $table->string('mother_mobile')->nullable();
            $table->string('email');

            // Address
            $table->text('home_address');
            $table->text('permanent_address');

            // Hostel
            $table->boolean('wants_hostel')->default(false);
            $table->string('hostel_preference')->nullable();

            // Admission target
            $table->foreignId('course_id')->constrained();

            // Review workflow
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->foreignId('reviewed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('reviewed_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->foreignId('student_id')->nullable()->constrained()->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admission_applications');
    }
};
