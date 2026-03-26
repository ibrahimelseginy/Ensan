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
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'original_price')) {
                $table->decimal('original_price', 10, 2)->nullable()->after('unit');
            }
            if (!Schema::hasColumn('items', 'discount_percentage')) {
                $table->decimal('discount_percentage', 5, 2)->default(0)->after('original_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn(['original_price', 'discount_percentage']);
        });
    }
};
