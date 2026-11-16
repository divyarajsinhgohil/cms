<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * The WhatsApp token is now stored encrypted (see SiteSetting casts).
 * Encrypted text is longer than 255 characters, so the column becomes TEXT,
 * and any token that is still plain text is encrypted in place.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->text('whatsapp_access_token')->nullable()->change();
        });

        DB::table('site_settings')->whereNotNull('whatsapp_access_token')->get()->each(function ($row) {
            try {
                Crypt::decryptString($row->whatsapp_access_token);

                return; // already encrypted
            } catch (DecryptException) {
                DB::table('site_settings')->where('id', $row->id)->update([
                    'whatsapp_access_token' => Crypt::encryptString($row->whatsapp_access_token),
                ]);
            }
        });
    }

    public function down(): void
    {
        DB::table('site_settings')->whereNotNull('whatsapp_access_token')->get()->each(function ($row) {
            try {
                DB::table('site_settings')->where('id', $row->id)->update([
                    'whatsapp_access_token' => Crypt::decryptString($row->whatsapp_access_token),
                ]);
            } catch (DecryptException) {
                // already plain text
            }
        });

        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('whatsapp_access_token')->nullable()->change();
        });
    }
};
