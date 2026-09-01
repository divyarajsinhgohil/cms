<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * staff_profiles was created without these columns even though the application
     * code (StaffController, staff.blade.php) has always read/written them — this
     * backfills the live schema to match.
     */
    public function up(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            if (! Schema::hasColumn('staff_profiles', 'work_field')) {
                $table->string('work_field')->nullable()->after('designation');
            }
            if (! Schema::hasColumn('staff_profiles', 'qualification')) {
                $table->string('qualification')->nullable()->after('work_field');
            }
            if (! Schema::hasColumn('staff_profiles', 'address')) {
                $table->text('address')->nullable()->after('qualification');
            }
            if (! Schema::hasColumn('staff_profiles', 'salary')) {
                $table->decimal('salary', 10, 2)->nullable()->after('address');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('staff_profiles', function (Blueprint $table) {
            $table->dropColumn(['work_field', 'address', 'salary']);
        });
    }
};
