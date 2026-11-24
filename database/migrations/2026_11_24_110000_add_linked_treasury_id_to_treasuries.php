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
        Schema::table('treasuries', function (Blueprint $table) {
            if (!Schema::hasColumn('treasuries', 'linked_treasury_id')) {
                $table->foreignId('linked_treasury_id')
                    ->nullable()
                    ->after('branch_id')
                    ->constrained('treasuries')
                    ->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treasuries', function (Blueprint $table) {
            if (Schema::hasColumn('treasuries', 'linked_treasury_id')) {
                $table->dropConstrainedForeignId('linked_treasury_id');
            }
        });
    }
};
