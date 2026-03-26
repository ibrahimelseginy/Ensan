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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'contract_start_date')) {
                $table->date('contract_start_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'contract_end_date')) {
                $table->date('contract_end_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'contract_image')) {
                $table->string('contract_image')->nullable();
            }
            if (!Schema::hasColumn('users', 'criminal_record_image')) {
                $table->string('criminal_record_image')->nullable();
            }
            if (!Schema::hasColumn('users', 'id_card_image')) {
                $table->string('id_card_image')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_photo_path')) {
                $table->string('profile_photo_path')->nullable();
            }
            if (!Schema::hasColumn('users', 'department')) {
                $table->string('department')->nullable();
            }
            if (!Schema::hasColumn('users', 'job_title')) {
                $table->string('job_title')->nullable();
            }
            if (!Schema::hasColumn('users', 'salary')) {
                $table->decimal('salary', 12, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'join_date')) {
                $table->date('join_date')->nullable();
            }
            if (!Schema::hasColumn('users', 'college')) {
                $table->string('college')->nullable();
            }
            if (!Schema::hasColumn('users', 'governorate')) {
                $table->string('governorate')->nullable();
            }
            if (!Schema::hasColumn('users', 'city')) {
                $table->string('city')->nullable();
            }
            if (!Schema::hasColumn('users', 'project_role')) {
                $table->string('project_role')->nullable();
            }
            if (!Schema::hasColumn('users', 'volunteer_hours')) {
                $table->decimal('volunteer_hours', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('users', 'project_id')) {
                $table->foreignId('project_id')->nullable()->constrained('projects')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'campaign_id')) {
                $table->foreignId('campaign_id')->nullable()->constrained('campaigns')->nullOnDelete();
            }
            if (!Schema::hasColumn('users', 'guest_house_id')) {
                $table->foreignId('guest_house_id')->nullable()->constrained('guest_houses')->nullOnDelete();
            }
        });

        Schema::table('payrolls', function (Blueprint $table) {
            if (!Schema::hasColumn('payrolls', 'treasury_id')) {
                $table->foreignId('treasury_id')->nullable()->constrained('treasuries')->nullOnDelete();
            }
            if (!Schema::hasColumn('payrolls', 'notes')) {
                $table->text('notes')->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'bonuses')) {
                $table->decimal('bonuses', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'deductions')) {
                $table->decimal('deductions', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'net_amount')) {
                $table->decimal('net_amount', 12, 2)->default(0);
            }
            if (!Schema::hasColumn('payrolls', 'journal_entry_id')) {
                $table->foreignId('journal_entry_id')->nullable()->constrained('journal_entries')->nullOnDelete();
            }
            // Add status for cancellation if missing
            if (!Schema::hasColumn('payrolls', 'status')) {
                $table->enum('status', ['pending', 'paid', 'cancelled'])->default('pending');
            }
            if (!Schema::hasColumn('payrolls', 'cancelled_at')) {
                $table->timestamp('cancelled_at')->nullable();
            }
            if (!Schema::hasColumn('payrolls', 'cancelled_by')) {
                $table->foreignId('cancelled_by')->nullable()->constrained('users')->nullOnDelete();
            }
            if (!Schema::hasColumn('payrolls', 'cancellation_reason')) {
                $table->text('cancellation_reason')->nullable();
            }
        });

        Schema::table('treasury_transactions', function (Blueprint $table) {
            if (!Schema::hasColumn('treasury_transactions', 'payroll_id')) {
                $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Not implemented to avoid losing data if columns existed partially
    }
};
