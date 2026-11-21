<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_attendances', 'check_in_photo')) {
                $table->string('check_in_photo')->nullable()->after('check_in_at');
            }
            if (!Schema::hasColumn('employee_attendances', 'check_out_photo')) {
                $table->string('check_out_photo')->nullable()->after('check_out_at');
            }
            if (!Schema::hasColumn('employee_attendances', 'check_in_lat')) {
                $table->decimal('check_in_lat', 10, 7)->nullable()->after('check_in_photo');
            }
            if (!Schema::hasColumn('employee_attendances', 'check_in_lng')) {
                $table->decimal('check_in_lng', 10, 7)->nullable()->after('check_in_lat');
            }
            if (!Schema::hasColumn('employee_attendances', 'check_out_lat')) {
                $table->decimal('check_out_lat', 10, 7)->nullable()->after('check_out_photo');
            }
            if (!Schema::hasColumn('employee_attendances', 'check_out_lng')) {
                $table->decimal('check_out_lng', 10, 7)->nullable()->after('check_out_lat');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn([
                'check_in_photo',
                'check_out_photo',
                'check_in_lat',
                'check_in_lng',
                'check_out_lat',
                'check_out_lng',
            ]);
        });
    }
};
