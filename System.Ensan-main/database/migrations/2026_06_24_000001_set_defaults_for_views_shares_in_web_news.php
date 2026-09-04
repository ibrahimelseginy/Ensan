<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Web News
        if (Schema::hasTable('web_news')) {
            Schema::table('web_news', function (Blueprint $table) {
                if (Schema::hasColumn('web_news', 'views_count')) {
                    DB::statement('ALTER TABLE web_news MODIFY views_count INT UNSIGNED DEFAULT 0 NOT NULL');
                }
                if (Schema::hasColumn('web_news', 'shares_count')) {
                    DB::statement('ALTER TABLE web_news MODIFY shares_count INT UNSIGNED DEFAULT 0 NOT NULL');
                }
            });
        }

        // Web Events
        if (Schema::hasTable('web_events')) {
            Schema::table('web_events', function (Blueprint $table) {
                if (Schema::hasColumn('web_events', 'views_count')) {
                    DB::statement('ALTER TABLE web_events MODIFY views_count INT UNSIGNED DEFAULT 0 NOT NULL');
                }
                if (Schema::hasColumn('web_events', 'shares_count')) {
                    DB::statement('ALTER TABLE web_events MODIFY shares_count INT UNSIGNED DEFAULT 0 NOT NULL');
                }
            });
        }
    }

    public function down(): void
    {
    }
};
