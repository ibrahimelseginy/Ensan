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
        Schema::table('web_volunteer_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('web_volunteer_requests', 'id_card_path')) {
                $table->string('id_card_path')->nullable()->after('cv_path');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('web_volunteer_requests', function (Blueprint $table) {
            $table->dropColumn('id_card_path');
        });
    }
};
