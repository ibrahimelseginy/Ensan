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
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('notes');
            $table->text('evaluation_notes')->nullable()->after('rating');
        });

        Schema::table('volunteer_attendances', function (Blueprint $table) {
            $table->tinyInteger('rating')->nullable()->after('notes');
            $table->text('evaluation_notes')->nullable()->after('rating');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('employee_attendances', function (Blueprint $table) {
            $table->dropColumn(['rating', 'evaluation_notes']);
        });

        Schema::table('volunteer_attendances', function (Blueprint $table) {
            $table->dropColumn(['rating', 'evaluation_notes']);
        });
    }
};
