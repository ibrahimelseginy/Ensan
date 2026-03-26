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
        Schema::table('web_room_bookings', function (Blueprint $table) {
            if (!Schema::hasColumn('web_room_bookings', 'national_id')) {
                $table->string('national_id')->nullable()->after('email');
            }
            if (!Schema::hasColumn('web_room_bookings', 'address')) {
                $table->string('address')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('web_room_bookings', 'companion_name')) {
                $table->string('companion_name')->nullable()->after('address');
            }
            if (!Schema::hasColumn('web_room_bookings', 'companion_phone')) {
                $table->string('companion_phone')->nullable()->after('companion_name');
            }
            if (!Schema::hasColumn('web_room_bookings', 'arrival_date')) {
                $table->date('arrival_date')->nullable()->after('room_type');
            }
            if (!Schema::hasColumn('web_room_bookings', 'expected_duration')) {
                $table->string('expected_duration')->nullable()->after('arrival_date');
            }
            if (!Schema::hasColumn('web_room_bookings', 'medical_center')) {
                $table->string('medical_center')->nullable()->after('expected_duration');
            }

            // Files paths
            if (!Schema::hasColumn('web_room_bookings', 'patient_id_path')) {
                $table->string('patient_id_path')->nullable();
            }
            if (!Schema::hasColumn('web_room_bookings', 'companion_id_path')) {
                $table->string('companion_id_path')->nullable();
            }
            if (!Schema::hasColumn('web_room_bookings', 'medical_transfer_path')) {
                $table->string('medical_transfer_path')->nullable();
            }
            if (!Schema::hasColumn('web_room_bookings', 'followup_card_path')) {
                $table->string('followup_card_path')->nullable();
            }
            if (!Schema::hasColumn('web_room_bookings', 'medical_report_path')) {
                $table->string('medical_report_path')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_room_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'address',
                'companion_name',
                'companion_phone',
                'arrival_date',
                'expected_duration',
                'medical_center',
                'patient_id_path',
                'companion_id_path',
                'medical_transfer_path',
                'followup_card_path',
                'medical_report_path'
            ]);
        });
    }
};
