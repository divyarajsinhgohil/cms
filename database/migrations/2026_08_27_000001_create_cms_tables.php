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
        // 1. Departments
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique(); // e.g. MCA, CSE, ECE, MBA
            $table->string('head_of_department')->nullable();
            $table->timestamps();
        });

        // 2. Courses
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('code')->unique(); // e.g. MCA-2Y, BTECH-CSE
            $table->integer('duration_years')->default(2);
            $table->timestamps();
        });

        // 3. Admission Enquiries
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->nullable();
            $table->string('phone');
            $table->foreignId('course_id')->nullable()->constrained()->onDelete('set null');
            $table->string('source')->default('Website'); // Website, Walk-in, Social Media, Referral
            $table->string('status')->default('New'); // New, In Progress, Converted, Dropped
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();
        });

        // 4. Marketing Templates
        Schema::create('marketing_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->default('whatsapp'); // whatsapp, sms
            $table->text('content'); // e.g. "Hello {name}, admissions for {course} are open!"
            $table->timestamps();
        });

        // 5. Marketing Campaigns
        Schema::create('marketing_campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('template_id')->constrained('marketing_templates')->onDelete('cascade');
            $table->integer('recipients_count')->default(0);
            $table->string('status')->default('Completed');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });

        // 6. Marketing Logs
        Schema::create('marketing_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->nullable()->constrained('marketing_campaigns')->onDelete('cascade');
            $table->string('recipient_phone');
            $table->string('channel')->default('WhatsApp');
            $table->text('message');
            $table->string('status')->default('Delivered');
            $table->timestamps();
        });

        // 7. Students Profile
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('enrollment_no')->unique(); // e.g. CMS-2026-MCA-00101
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('batch_year')->default(2026);
            $table->integer('current_semester')->default(1);
            $table->string('gender')->nullable();
            $table->date('dob')->nullable();
            $table->text('address')->nullable();
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone')->nullable();
            $table->timestamps();
        });

        // 8. Staff Profiles
        Schema::create('staff_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id')->constrained()->onDelete('cascade');
            $table->string('employee_id')->unique();
            $table->string('designation')->default('Assistant Professor');
            $table->string('qualification')->nullable();
            $table->text('address')->nullable();
            $table->string('work_field')->nullable(); // e.g. Commerce, Computer Science, Management, Finance
            $table->decimal('salary', 10, 2)->nullable();
            $table->date('joining_date')->nullable();
            $table->timestamps();
        });

        // 9. Subjects
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->default(1);
            $table->string('name');
            $table->string('code')->unique(); // e.g. MCA101
            $table->integer('credits')->default(4);
            $table->timestamps();
        });

        // 10. Daily Attendance
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('present'); // present, absent, late
            $table->foreignId('marked_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });

        // 11. Fee Structures
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->string('academic_year')->default('2026-2027');
            $table->decimal('tuition_fee', 10, 2)->default(0);
            $table->decimal('exam_fee', 10, 2)->default(0);
            $table->decimal('library_fee', 10, 2)->default(0);
            $table->decimal('other_fee', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->timestamps();
        });

        // 12. Fee Payments
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('receipt_no')->unique();
            $table->decimal('amount_paid', 10, 2);
            $table->string('payment_mode')->default('Cash'); // Cash, Online, Cheque, UPI
            $table->date('payment_date');
            $table->text('remarks')->nullable();
            $table->timestamps();
        });

        // 13. Semester Exam Marks & Results
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->default(1);
            $table->decimal('internal_marks', 5, 2)->default(0);
            $table->decimal('external_marks', 5, 2)->default(0);
            $table->decimal('total_marks', 5, 2)->default(0);
            $table->string('grade')->default('F');
            $table->decimal('grade_point', 3, 2)->default(0.00);
            $table->timestamps();
        });

        // 14. Timetables (Clash-Free Schedule)
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->onDelete('cascade');
            $table->integer('semester')->default(1);
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('staff_id')->constrained('users')->onDelete('cascade');
            $table->string('classroom'); // e.g. Room 101, Lab 3
            $table->string('day_of_week'); // Monday, Tuesday, etc.
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
        });

        // 15. Study Materials & Assignments
        Schema::create('study_materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained()->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->default('notes'); // notes, assignment, syllabus
            $table->string('file_path')->nullable();
            $table->date('due_date')->nullable(); // for assignments
            $table->timestamps();
        });

        // 16. Assignment Submissions
        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('study_material_id')->constrained()->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('file_path')->nullable();
            $table->text('comments')->nullable();
            $table->string('status')->default('submitted'); // submitted, graded
            $table->decimal('marks_obtained', 5, 2)->nullable();
            $table->timestamps();
        });

        // 17. Notice Board
        Schema::create('notices', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->string('target_audience')->default('all'); // all, students, staff
            $table->string('priority')->default('normal'); // normal, urgent
            $table->string('attachment_path')->nullable();
            $table->foreignId('posted_by')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notices');
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('study_materials');
        Schema::dropIfExists('timetables');
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('subjects');
        Schema::dropIfExists('staff_profiles');
        Schema::dropIfExists('students');
        Schema::dropIfExists('marketing_logs');
        Schema::dropIfExists('marketing_campaigns');
        Schema::dropIfExists('marketing_templates');
        Schema::dropIfExists('enquiries');
        Schema::dropIfExists('courses');
        Schema::dropIfExists('departments');
    }
};
