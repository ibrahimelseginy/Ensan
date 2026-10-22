<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guest_house_stays')) {
            Schema::table('guest_house_stays', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_stays', 'is_temporary_out')) {
                    $table->boolean('is_temporary_out')->default(false)->after('status');
                }
                if (!Schema::hasColumn('guest_house_stays', 'temporary_out_at')) {
                    $table->timestamp('temporary_out_at')->nullable()->after('is_temporary_out');
                }
            });
        }

        if (Schema::hasTable('guest_house_patient_profiles')) {
            Schema::table('guest_house_patient_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_patient_profiles', 'gender')) {
                    $table->string('gender', 20)->nullable()->after('sessions_count');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('guest_house_stays')) {
            Schema::table('guest_house_stays', function (Blueprint $table) {
                if (Schema::hasColumn('guest_house_stays', 'temporary_out_at')) {
                    $table->dropColumn('temporary_out_at');
                }
                if (Schema::hasColumn('guest_house_stays', 'is_temporary_out')) {
                    $table->dropColumn('is_temporary_out');
                }
            });
        }

        if (Schema::hasTable('guest_house_patient_profiles')) {
            Schema::table('guest_house_patient_profiles', function (Blueprint $table) {
                if (Schema::hasColumn('guest_house_patient_profiles', 'gender')) {
                    $table->dropColumn('gender');
                }
            });
        }
    }
};
