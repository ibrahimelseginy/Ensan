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
        Schema::table('inventory_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('inventory_transactions', 'unit_cost')) {
                $table->decimal('unit_cost', 10, 2)->default(0)->after('quantity');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'total_cost')) {
                $table->decimal('total_cost', 10, 2)->default(0)->after('unit_cost');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'expense_id')) {
                $table->unsignedBigInteger('expense_id')->nullable()->after('campaign_id');
                $table->foreign('expense_id')->references('id')->on('expenses')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'delegate_id')) {
                $table->unsignedBigInteger('delegate_id')->nullable()->after('expense_id');
                $table->foreign('delegate_id')->references('id')->on('delegates')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('delegate_id');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'journal_entry_id')) {
                $table->unsignedBigInteger('journal_entry_id')->nullable()->after('user_id');
                $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'transaction_date')) {
                $table->date('transaction_date')->nullable()->after('journal_entry_id');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'status')) {
                $table->string('status')->default('pending')->after('transaction_date');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'approved_by')) {
                $table->unsignedBigInteger('approved_by')->nullable()->after('status');
                $table->foreign('approved_by')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'approved_at')) {
                $table->timestamp('approved_at')->nullable()->after('approved_by');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'batch_number')) {
                $table->string('batch_number')->nullable()->after('approved_at');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'expiry_date')) {
                $table->date('expiry_date')->nullable()->after('batch_number');
            }
            
            if (!Schema::hasColumn('inventory_transactions', 'location_in_warehouse')) {
                $table->string('location_in_warehouse')->nullable()->after('expiry_date');
            }
        });

        // Add fields to items table
        Schema::table('items', function (Blueprint $table) {
            if (!Schema::hasColumn('items', 'category')) {
                $table->string('category')->nullable()->after('unit');
            }
            
            if (!Schema::hasColumn('items', 'min_stock_level')) {
                $table->integer('min_stock_level')->default(10)->after('category');
            }
            
            if (!Schema::hasColumn('items', 'max_stock_level')) {
                $table->integer('max_stock_level')->nullable()->after('min_stock_level');
            }
            
            if (!Schema::hasColumn('items', 'reorder_point')) {
                $table->integer('reorder_point')->nullable()->after('max_stock_level');
            }
            
            if (!Schema::hasColumn('items', 'barcode')) {
                $table->string('barcode')->nullable()->unique()->after('sku');
            }
            
            if (!Schema::hasColumn('items', 'image_path')) {
                $table->string('image_path')->nullable()->after('barcode');
            }
            
            if (!Schema::hasColumn('items', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('image_path');
            }
        });

        // Add fields to warehouses table
        Schema::table('warehouses', function (Blueprint $table) {
            if (!Schema::hasColumn('warehouses', 'manager_id')) {
                $table->unsignedBigInteger('manager_id')->nullable()->after('location');
                $table->foreign('manager_id')->references('id')->on('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('warehouses', 'phone')) {
                $table->string('phone')->nullable()->after('manager_id');
            }
            
            if (!Schema::hasColumn('warehouses', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }
            
            if (!Schema::hasColumn('warehouses', 'capacity')) {
                $table->integer('capacity')->nullable()->after('address');
            }
            
            if (!Schema::hasColumn('warehouses', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('capacity');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventory_transactions', function (Blueprint $table) {
            $table->dropForeign(['expense_id']);
            $table->dropForeign(['delegate_id']);
            $table->dropForeign(['user_id']);
            $table->dropForeign(['journal_entry_id']);
            $table->dropForeign(['approved_by']);
            
            $table->dropColumn([
                'unit_cost',
                'total_cost',
                'expense_id',
                'delegate_id',
                'user_id',
                'journal_entry_id',
                'transaction_date',
                'status',
                'approved_by',
                'approved_at',
                'batch_number',
                'expiry_date',
                'location_in_warehouse'
            ]);
        });

        Schema::table('items', function (Blueprint $table) {
            $table->dropColumn([
                'category',
                'min_stock_level',
                'max_stock_level',
                'reorder_point',
                'barcode',
                'image_path',
                'is_active'
            ]);
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropColumn([
                'manager_id',
                'phone',
                'address',
                'capacity',
                'is_active'
            ]);
        });
    }
};
