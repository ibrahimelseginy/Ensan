<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('attendance_devices') && !Schema::hasColumn('attendance_devices', 'user_id')) {
            Schema::table('attendance_devices', function (Blueprint $table) {
                $table->unsignedBigInteger('user_id')->nullable()->after('guest_house_id')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('attendance_devices') && Schema::hasColumn('attendance_devices', 'user_id')) {
            Schema::table('attendance_devices', function (Blueprint $table) {
                $table->dropColumn('user_id');
            });
        }
    }
};
