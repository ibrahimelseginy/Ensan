<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('field_visits') && ! Schema::hasColumn('field_visits', 'location_url')) {
            Schema::table('field_visits', function (Blueprint $table) {
                $table->string('location_url', 500)->nullable()->after('address');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('field_visits') && Schema::hasColumn('field_visits', 'location_url')) {
            Schema::table('field_visits', function (Blueprint $table) {
                $table->dropColumn('location_url');
            });
        }
    }
};

