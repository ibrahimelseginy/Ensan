<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (! Schema::hasTable('guest_house_meal_inventory_items')) {
            Schema::create('guest_house_meal_inventory_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('guest_house_meal_id')->constrained()->cascadeOnDelete();
                $table->foreignId('warehouse_id')->constrained()->restrictOnDelete();
                $table->foreignId('item_id')->constrained()->restrictOnDelete();
                $table->decimal('quantity', 12, 2);
                $table->timestamps();

                $table->unique(['guest_house_meal_id', 'warehouse_id', 'item_id'], 'gh_meal_inventory_item_unique');
            });
        }

        if (Schema::hasTable('inventory_transactions')) {
            if (! Schema::hasColumn('inventory_transactions', 'guest_house_meal_inventory_item_id')) {
                Schema::table('inventory_transactions', function (Blueprint $table) {
                    $table->unsignedBigInteger('guest_house_meal_inventory_item_id')->nullable();
                });
            }

            try {
                Schema::table('inventory_transactions', function (Blueprint $table) {
                    $table->foreign('guest_house_meal_inventory_item_id', 'inv_tx_gh_meal_item_fk')
                        ->references('id')
                        ->on('guest_house_meal_inventory_items')
                        ->nullOnDelete();
                });
            } catch (\Throwable $e) {
                // Foreign key already exists
            }
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('inventory_transactions')
            && Schema::hasColumn('inventory_transactions', 'guest_house_meal_inventory_item_id')) {
            Schema::table('inventory_transactions', function (Blueprint $table) {
                $table->dropConstrainedForeignId('guest_house_meal_inventory_item_id');
            });
        }

        Schema::dropIfExists('guest_house_meal_inventory_items');
    }
};
