<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('campaigns') && !Schema::hasColumn('campaigns', 'target_families')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->unsignedInteger('target_families')->nullable()->after('beneficiaries_count')->comment('عدد الأسر المستهدفة (1 لعملية جراحية أو عدة أسر)');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('campaigns') && Schema::hasColumn('campaigns', 'target_families')) {
            Schema::table('campaigns', function (Blueprint $table) {
                $table->dropColumn('target_families');
            });
        }
    }
};
