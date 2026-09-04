<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('inventory_transactions', 'notes')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->text('notes')->nullable()->after('reference');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('inventory_transactions', 'notes')) {
            Schema::table('inventory_transactions', fn (Blueprint $table) => $table->dropColumn('notes'));
        }
    }
};
