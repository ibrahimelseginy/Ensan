<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('leaves')) {
            if (DB::getDriverName() === 'mysql') {
                DB::statement("ALTER TABLE leaves MODIFY COLUMN type ENUM('annual','sick','unpaid','emergency','permission','other') NOT NULL DEFAULT 'annual'");
            }

            Schema::table('leaves', function (Blueprint $table) {
                if (!Schema::hasColumn('leaves', 'from_time')) {
                    $table->time('from_time')->nullable()->after('end_date');
                }
                if (!Schema::hasColumn('leaves', 'to_time')) {
                    $table->time('to_time')->nullable()->after('from_time');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('leaves')) {
            Schema::table('leaves', function (Blueprint $table) {
                $drops = array_values(array_filter(['from_time', 'to_time'], fn ($c) => Schema::hasColumn('leaves', $c)));
                if (!empty($drops)) {
                    $table->dropColumn($drops);
                }
            });
        }
    }
};
