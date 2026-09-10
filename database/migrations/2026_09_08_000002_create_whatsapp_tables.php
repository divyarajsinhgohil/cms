<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add automated trigger settings to site_settings table if missing
        Schema::table('site_settings', function (Blueprint $table) {
            if (! Schema::hasColumn('site_settings', 'whatsapp_auto_fee_receipt')) {
                $table->boolean('whatsapp_auto_fee_receipt')->default(true);
            }
            if (! Schema::hasColumn('site_settings', 'whatsapp_auto_attendance_alert')) {
                $table->boolean('whatsapp_auto_attendance_alert')->default(true);
            }
            if (! Schema::hasColumn('site_settings', 'whatsapp_auto_exam_result')) {
                $table->boolean('whatsapp_auto_exam_result')->default(true);
            }
            if (! Schema::hasColumn('site_settings', 'whatsapp_auto_admission_approval')) {
                $table->boolean('whatsapp_auto_admission_approval')->default(true);
            }
        });

        // Create whatsapp_logs table if not exists
        if (! Schema::hasTable('whatsapp_logs')) {
            Schema::create('whatsapp_logs', function (Blueprint $table) {
                $table->id();
                $table->string('recipient_phone');
                $table->string('recipient_name')->nullable();
                $table->string('type')->default('direct'); // direct, fee_receipt, attendance_alert, exam_result, admission_approval, campaign
                $table->text('message');
                $table->string('status')->default('Sent'); // Delivered, Sent, Failed
                $table->string('message_id')->nullable();
                $table->text('error_details')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('whatsapp_logs');

        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'whatsapp_auto_fee_receipt',
                'whatsapp_auto_attendance_alert',
                'whatsapp_auto_exam_result',
                'whatsapp_auto_admission_approval',
            ]);
        });
    }
};
