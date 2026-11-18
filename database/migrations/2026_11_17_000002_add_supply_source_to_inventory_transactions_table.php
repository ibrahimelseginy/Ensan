<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (Schema::hasTable('inventory_transactions')
            && ! Schema::hasColumn('inventory_transactions', 'supply_source')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->string('supply_source', 20)->nullable()->index();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_transactions')
            && Schema::hasColumn('inventory_transactions', 'supply_source')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropColumn('supply_source');
            });
        }
    }
};
