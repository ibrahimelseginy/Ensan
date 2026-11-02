<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('donations')) {
            Schema::table('donations', function (Blueprint $table) {
                if (!Schema::hasColumn('donations', 'split_allocations')) {
                    $table->json('split_allocations')->nullable()->after('beneficiary_allocations');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('donations')) {
            Schema::table('donations', function (Blueprint $table) {
                if (Schema::hasColumn('donations', 'split_allocations')) {
                    $table->dropColumn('split_allocations');
                }
            });
        }
    }
};
