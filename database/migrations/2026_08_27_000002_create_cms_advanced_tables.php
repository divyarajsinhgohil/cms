<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Hostel Rooms
        Schema::create('hostel_rooms', function (Blueprint $table) {
            $table->id();
            $table->string('building_name'); // e.g. Boys Hostel Block A, Girls Hostel Block B
            $table->string('room_no');
            $table->integer('capacity')->default(3);
            $table->integer('current_occupancy')->default(0);
            $table->decimal('annual_fee', 10, 2)->default(35000.00);
            $table->timestamps();
        });

        // 2. Room Allotments
        Schema::create('room_allotments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->foreignId('hostel_room_id')->constrained()->onDelete('cascade');
            $table->date('allotment_date');
            $table->timestamps();
        });

        // 3. Student Outpass & Leave Gate Passes
        Schema::create('outpasses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->onDelete('cascade');
            $table->string('reason');
            $table->dateTime('out_time');
            $table->dateTime('in_time');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->timestamps();
        });

        // 4. Complaints & Grievance Redressal
        Schema::create('complaints', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('category')->default('Academic'); // Academic, Hostel, Mess, IT & Facilities
            $table->string('title');
            $table->text('description');
            $table->string('status')->default('Open'); // Open, In Progress, Resolved
            $table->timestamps();
        });

        // 5. Leave Requests (Staff & Students)
        Schema::create('leave_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('leave_type')->default('Casual Leave');
            $table->date('start_date');
            $table->date('end_date');
            $table->text('reason');
            $table->string('status')->default('Pending'); // Pending, Approved, Rejected
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leave_requests');
        Schema::dropIfExists('complaints');
        Schema::dropIfExists('outpasses');
        Schema::dropIfExists('room_allotments');
        Schema::dropIfExists('hostel_rooms');
    }
};
