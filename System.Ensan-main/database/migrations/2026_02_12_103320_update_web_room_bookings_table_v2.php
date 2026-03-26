<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{
    public function up(): void
    {
        Schema::table('web_room_bookings', function (Blueprint $table) {
            $table->string('national_id')->nullable();
            $table->string('companion_name')->nullable();
            $table->string('companion_phone')->nullable();
            $table->date('arrival_date')->nullable();
            $table->string('expected_duration')->nullable();
            $table->string('medical_center')->nullable();
            $table->string('patient_id_path')->nullable();
            $table->string('companion_id_path')->nullable();
            $table->string('medical_transfer_path')->nullable();
            $table->string('followup_card_path')->nullable();
            $table->string('medical_report_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('web_room_bookings', function (Blueprint $table) {
            $table->dropColumn([
                'national_id', 'companion_name', 'companion_phone', 'arrival_date',
                'expected_duration', 'medical_center', 'patient_id_path',
                'companion_id_path', 'medical_transfer_path', 'followup_card_path',
                'medical_report_path'
            ]);
        });
    }
};
