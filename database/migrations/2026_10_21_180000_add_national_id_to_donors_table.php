<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('donors') && !Schema::hasColumn('donors', 'national_id')) {
            Schema::table('donors', function (Blueprint $table) {
                $table->string('national_id', 20)->nullable()->after('phone')->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('donors') && Schema::hasColumn('donors', 'national_id')) {
            Schema::table('donors', function (Blueprint $table) {
                $table->dropColumn('national_id');
            });
        }
    }
};
