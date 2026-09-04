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
        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'journal_entry_id')) {
                $table->unsignedBigInteger('journal_entry_id')->nullable()->after('paid_at');
                $table->foreign('journal_entry_id')->references('id')->on('journal_entries')->onDelete('set null');
            }
            
            if (!Schema::hasColumn('payrolls', 'status')) {
                $table->string('status')->default('pending')->after('journal_entry_id'); // pending, paid, cancelled
            }
            
            if (!Schema::hasColumn('payrolls', 'notes')) {
                $table->text('notes')->nullable()->after('status');
            }
            
            if (!Schema::hasColumn('payrolls', 'deductions')) {
                $table->decimal('deductions', 10, 2)->default(0)->after('amount');
            }
            
            if (!Schema::hasColumn('payrolls', 'bonuses')) {
                $table->decimal('bonuses', 10, 2)->default(0)->after('deductions');
            }
            
            if (!Schema::hasColumn('payrolls', 'net_amount')) {
                $table->decimal('net_amount', 10, 2)->nullable()->after('bonuses');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            $table->dropForeign(['journal_entry_id']);
            $table->dropColumn([
                'journal_entry_id',
                'status',
                'notes',
                'deductions',
                'bonuses',
                'net_amount'
            ]);
        });
    }
};
