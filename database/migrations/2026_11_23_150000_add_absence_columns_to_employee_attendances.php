<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            if (!Schema::hasColumn('employee_attendances', 'is_absence')) {
                $table->boolean('is_absence')->default(false)->after('notes');
            }
            if (!Schema::hasColumn('employee_attendances', 'deducted_from_leave')) {
                $table->boolean('deducted_from_leave')->default(false)->after('is_absence');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn(['is_absence', 'deducted_from_leave']);
        });
    }
};

