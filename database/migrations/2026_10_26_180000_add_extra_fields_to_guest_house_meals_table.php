<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('guest_house_meals', function (Blueprint $table) {
            if (!Schema::hasColumn('guest_house_meals', 'quantity_deducted')) {
                $table->decimal('quantity_deducted', 12, 2)->nullable()->after('inventory_item_id');
            }
            if (!Schema::hasColumn('guest_house_meals', 'expense_id')) {
                $table->foreignId('expense_id')->nullable()->after('expense_amount')->constrained('expenses')->nullOnDelete();
            }
            if (!Schema::hasColumn('guest_house_meals', 'expense_recorded')) {
                $table->boolean('expense_recorded')->default(false)->after('expense_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('guest_house_meals', function (Blueprint $table) {
            if (Schema::hasColumn('guest_house_meals', 'expense_id')) {
                $table->dropForeign(['expense_id']);
                $table->dropColumn('expense_id');
            }
            if (Schema::hasColumn('guest_house_meals', 'quantity_deducted')) {
                $table->dropColumn('quantity_deducted');
            }
            if (Schema::hasColumn('guest_house_meals', 'expense_recorded')) {
                $table->dropColumn('expense_recorded');
            }
        });
    }
};
