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
        Schema::table('donations', function (Blueprint $table) {
            // Add status field for soft cancellation
            if (!Schema::hasColumn('donations', 'status')) {
                $table->enum('status', ['active', 'cancelled'])->default('active')->after('received_at');
            }
            
            // Add cancellation metadata
            if (!Schema::hasColumn('donations', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('donations', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete()->after('cancelled_at');
            }
            
            if (!Schema::hasColumn('donations', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            }
        });
        
        // Add same fields to expenses
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'status')) {
                $table->enum('status', ['active', 'cancelled'])->default('active')->after('paid_at');
            }
            
            if (!Schema::hasColumn('expenses', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('expenses', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete()->after('cancelled_at');
            }
            
            if (!Schema::hasColumn('expenses', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            }
        });
        
        // Add same fields to treasury_transactions
        Schema::table('treasury_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('treasury_transactions', 'status')) {
                $table->enum('status', ['active', 'cancelled'])->default('active')->after('transaction_date');
            }
            
            if (!Schema::hasColumn('treasury_transactions', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('treasury_transactions', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete()->after('cancelled_at');
            }
            
            if (!Schema::hasColumn('treasury_transactions', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable()->after('cancelled_by');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('donations', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
        
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
        
        Schema::table('treasury_transactions', function (Blueprint $table) {
            $table->dropColumn(['status', 'cancelled_at', 'cancelled_by', 'cancellation_reason']);
        });
    }
};
