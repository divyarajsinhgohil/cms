<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('whatsapp_access_token')->nullable();
            $table->string('whatsapp_phone_number_id')->nullable();
            $table->string('whatsapp_business_account_id')->nullable();
        });

        Schema::table('marketing_templates', function (Blueprint $table) {
            // The exact template name approved in Meta Business Manager — required for
            // real WhatsApp sends, since Meta only allows business-initiated messages
            // through a pre-approved template, never arbitrary free text.
            $table->string('meta_template_name')->nullable()->after('content');
            $table->string('meta_language')->default('en_US')->after('meta_template_name');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn(['whatsapp_access_token', 'whatsapp_phone_number_id', 'whatsapp_business_account_id']);
        });

        Schema::table('marketing_templates', function (Blueprint $table) {
            $table->dropColumn(['meta_template_name', 'meta_language']);
        });
    }
};
