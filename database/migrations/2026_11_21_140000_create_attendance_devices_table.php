<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('attendance_devices')) {
            Schema::create('attendance_devices', function (Blueprint $table) {
                $table->id();
                $table->string('device_token', 64)->unique()->index();
                $table->string('device_name');
                $table->string('location_name');
                $table->unsignedBigInteger('guest_house_id')->nullable()->index();
                $table->unsignedBigInteger('authorized_by_user_id')->nullable()->index();
                $table->string('ip_address', 45)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('last_used_at')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('attendance_devices');
    }
};
