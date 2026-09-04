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
        // Create treasuries table
        if (!Schema::hasTable('treasuries')) {
            Schema::create('treasuries', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('code')->unique();
                $table->text('description')->nullable();
                $table->foreignId('manager_id')->nullable()->constrained('users')->onDelete('set null');
                $table->string('location')->nullable();
                $table->string('currency')->default('EGP');
                $table->decimal('opening_balance', 15, 2)->default(0);
                $table->decimal('current_balance', 15, 2)->default(0);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        }

        // Create treasury_transactions table
        if (!Schema::hasTable('treasury_transactions')) {
            Schema::create('treasury_transactions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('treasury_id')->constrained('treasuries')->onDelete('cascade');
                $table->string('type'); // in, out
                $table->decimal('amount', 15, 2);
                $table->string('currency')->default('EGP');
                $table->text('description')->nullable();
                $table->string('reference')->nullable();
                $table->date('transaction_date');
                $table->foreignId('created_by')->nullable()->constrained('users')->onDelete('set null');
                $table->foreignId('donation_id')->nullable()->constrained('donations')->onDelete('set null');
                $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('set null');
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->onDelete('set null');
                $table->timestamps();
            });
        }

        // Add fields to donations table
        Schema::table('donations', function (Blueprint $table) {
            if (!Schema::hasColumn('donations', 'treasury_id')) {
                $table->foreignId('treasury_id')->nullable()->after('warehouse_id')->constrained('treasuries')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('donations', 'item_id')) {
                $table->foreignId('item_id')->nullable()->after('treasury_id')->constrained('items')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('donations', 'quantity')) {
                $table->decimal('quantity', 10, 2)->nullable()->after('item_id');
            }
            
            if (!Schema::hasColumn('donations', 'created_by')) {
                $table->foreignId('created_by')->nullable()->after('received_at')->constrained('users')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('donations', 'auto_added_to_inventory')) {
                $table->boolean('auto_added_to_inventory')->default(false)->after('created_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropForeign(['treasury_id']);
            $table->dropForeign(['item_id']);
            $table->dropForeign(['created_by']);
            $table->dropColumn(['treasury_id', 'item_id', 'quantity', 'created_by', 'auto_added_to_inventory']);
        });

        Schema::dropIfExists('treasury_transactions');
        Schema::dropIfExists('treasuries');
    }
};
