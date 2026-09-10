<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->string('target_role')->default('all'); // all, management, staff, student
            $table->string('type')->default('info'); // info, success, warning, danger
            $table->string('display_format')->default('toast'); // toast, banner, modal
            $table->boolean('is_active')->default(true);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });

        Schema::create('user_broadcast_reads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('broadcast_message_id')->constrained('broadcast_messages')->onDelete('cascade');
            $table->timestamp('read_at')->useCurrent();
            $table->timestamps();

            $table->unique(['user_id', 'broadcast_message_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_broadcast_reads');
        Schema::dropIfExists('broadcast_messages');
    }
};
