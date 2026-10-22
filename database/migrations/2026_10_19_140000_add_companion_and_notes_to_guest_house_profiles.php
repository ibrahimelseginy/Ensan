<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('guest_house_patient_profiles')) {
            Schema::table('guest_house_patient_profiles', function (Blueprint $table) {
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_name')) {
                    $table->string('companion_name')->nullable()->after('medical_notes');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_relation')) {
                    $table->string('companion_relation')->nullable()->after('companion_name');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_national_id')) {
                    $table->string('companion_national_id', 20)->nullable()->after('companion_relation');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_phone')) {
                    $table->string('companion_phone', 25)->nullable()->after('companion_national_id');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_address')) {
                    $table->string('companion_address')->nullable()->after('companion_phone');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_id_front_path')) {
                    $table->string('companion_id_front_path')->nullable()->after('companion_address');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'companion_id_back_path')) {
                    $table->string('companion_id_back_path')->nullable()->after('companion_id_front_path');
                }
                if (!Schema::hasColumn('guest_house_patient_profiles', 'manager_notes')) {
                    $table->text('manager_notes')->nullable()->after('companion_id_back_path')->comment('ملاحظات مدير الدار (مشاكل أو سلبيات)');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('guest_house_patient_profiles')) {
            Schema::table('guest_house_patient_profiles', function (Blueprint $table) {
                $table->dropColumn([
                    'companion_name',
                    'companion_relation',
                    'companion_national_id',
                    'companion_phone',
                    'companion_address',
                    'companion_id_front_path',
                    'companion_id_back_path',
                    'manager_notes',
                ]);
            });
        }
    }
};
