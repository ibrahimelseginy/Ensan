<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Finance V2 is deliberately additive.  Historical V1 tables remain readable
 * while their balances are reconciled and migrated; no financial history is
 * overwritten by this migration.
 */
return new class extends Migration {
    public function up(): void
    {
        Schema::create('financial_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 191);
            $table->string('classification', 24); // asset, liability, net_asset, revenue, expense
            $table->string('normal_balance', 6); // debit or credit
            $table->foreignId('parent_id')->nullable()->constrained('financial_accounts')->nullOnDelete();
            $table->boolean('is_posting')->default(true);
            $table->boolean('is_active')->default(true);
            $table->string('currency', 3)->default('EGP');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['classification', 'is_active']);
        });

        Schema::create('accounting_periods', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique();
            $table->date('starts_on');
            $table->date('ends_on');
            $table->string('status', 16)->default('open'); // open, soft_closed, closed
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('close_note')->nullable();
            $table->timestamps();
            $table->unique(['starts_on', 'ends_on']);
            $table->index(['status', 'starts_on', 'ends_on']);
        });

        Schema::create('funds', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 191);
            $table->string('restriction_type', 24)->default('unrestricted');
            $table->string('spending_policy', 32)->default('approval_required');
            $table->boolean('allow_negative')->default(false);
            $table->boolean('is_active')->default(true);
            $table->text('restriction_note')->nullable();
            $table->timestamps();
            $table->index(['restriction_type', 'is_active']);
        });

        // A locked projection used for concurrent availability checks. The ledger
        // and fund_movements remain the audit source; this table is never edited manually.
        Schema::create('fund_balances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained('funds')->cascadeOnDelete();
            $table->string('currency', 3)->default('EGP');
            $table->decimal('available_amount', 19, 4)->default(0);
            $table->decimal('reserved_amount', 19, 4)->default(0);
            $table->unsignedBigInteger('lock_version')->default(0);
            $table->timestamps();
            $table->unique(['fund_id', 'currency']);
        });

        Schema::create('cash_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('code', 64)->unique();
            $table->string('name', 191);
            $table->string('kind', 24); // cash, bank, wallet, clearing
            $table->foreignId('ledger_account_id')->unique()->constrained('financial_accounts')->restrictOnDelete();
            $table->string('currency', 3)->default('EGP');
            $table->unsignedBigInteger('legacy_treasury_id')->nullable()->unique();
            $table->string('bank_name', 191)->nullable();
            $table->string('account_mask', 64)->nullable();
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['kind', 'is_active']);
        });

        Schema::create('cash_account_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->boolean('can_view')->default(false);
            $table->boolean('can_receive')->default(false);
            $table->boolean('can_disburse')->default(false);
            $table->boolean('can_transfer')->default(false);
            $table->boolean('can_reconcile')->default(false);
            $table->timestamps();
            $table->unique(['cash_account_id', 'user_id']);
        });

        Schema::create('financial_documents', function (Blueprint $table) {
            $table->id();
            $table->string('document_type', 32);
            $table->string('document_number', 64)->unique();
            $table->date('issued_on');
            $table->string('status', 20)->default('issued');
            $table->string('subject_type', 80)->nullable();
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('voided_at')->nullable();
            $table->foreignId('voided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->string('void_reason', 500)->nullable();
            $table->timestamps();
            $table->index(['document_type', 'issued_on']);
            $table->index(['subject_type', 'subject_id']);
        });

        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->string('entry_number', 64)->unique();
            $table->foreignId('accounting_period_id')->constrained('accounting_periods')->restrictOnDelete();
            $table->date('entry_date');
            $table->timestamp('posted_at');
            $table->string('entry_type', 32);
            $table->string('currency', 3)->default('EGP');
            $table->string('status', 20)->default('posted'); // posted or reversed
            $table->string('source_type', 80)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('idempotency_key', 128)->nullable()->unique();
            $table->foreignId('reversal_of_id')->nullable()->constrained('ledger_entries')->restrictOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('financial_documents')->nullOnDelete();
            $table->foreignId('posted_by')->constrained('users')->restrictOnDelete();
            $table->string('narrative', 1000);
            $table->string('reference', 191)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
            $table->index(['entry_date', 'status']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('ledger_entry_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ledger_entry_id')->constrained('ledger_entries')->cascadeOnDelete();
            $table->unsignedSmallInteger('line_no');
            $table->foreignId('financial_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->decimal('debit', 19, 4)->default(0);
            $table->decimal('credit', 19, 4)->default(0);
            $table->foreignId('fund_id')->nullable()->constrained('funds')->restrictOnDelete();
            $table->foreignId('cash_account_id')->nullable()->constrained('cash_accounts')->restrictOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->unsignedBigInteger('custody_id')->nullable();
            $table->string('counterparty_type', 80)->nullable();
            $table->unsignedBigInteger('counterparty_id')->nullable();
            $table->string('description', 500)->nullable();
            $table->json('dimensions')->nullable();
            $table->timestamps();
            $table->unique(['ledger_entry_id', 'line_no']);
            $table->index(['fund_id', 'project_id', 'campaign_id']);
            $table->index(['cash_account_id', 'created_at']);
        });

        Schema::create('fund_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fund_id')->constrained('funds')->restrictOnDelete();
            $table->foreignId('ledger_entry_id')->nullable()->constrained('ledger_entries')->restrictOnDelete();
            $table->foreignId('fund_balance_id')->constrained('fund_balances')->restrictOnDelete();
            $table->string('movement_type', 24); // allocate, consume, release, reserve, unreserve, adjustment
            $table->decimal('amount', 19, 4);
            $table->string('currency', 3)->default('EGP');
            $table->string('source_type', 80)->nullable();
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('note', 1000)->nullable();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->timestamps();
            $table->index(['fund_id', 'currency', 'created_at']);
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('donation_allocations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained('donations')->restrictOnDelete();
            $table->unsignedSmallInteger('line_no');
            $table->decimal('amount', 19, 4);
            $table->string('currency', 3)->default('EGP');
            $table->foreignId('fund_id')->nullable()->constrained('funds')->restrictOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->string('status', 20)->default('allocated');
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['donation_id', 'line_no']);
            $table->index(['fund_id', 'project_id', 'campaign_id']);
        });

        Schema::create('cash_transfers', function (Blueprint $table) {
            $table->id();
            $table->string('transfer_number', 64)->unique();
            $table->foreignId('from_cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->foreignId('to_cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->decimal('amount', 19, 4);
            $table->string('currency', 3)->default('EGP');
            $table->date('requested_on');
            $table->timestamp('received_at')->nullable();
            $table->string('status', 20)->default('draft'); // draft, submitted, received, rejected, cancelled
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ledger_entry_id')->nullable()->unique()->constrained('ledger_entries')->nullOnDelete();
            $table->foreignId('document_id')->nullable()->constrained('financial_documents')->nullOnDelete();
            $table->string('reference', 191)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['status', 'requested_on']);
        });

        Schema::create('cash_transfer_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_transfer_id')->constrained('cash_transfers')->cascadeOnDelete();
            $table->string('source_type', 80);
            $table->unsignedBigInteger('source_id');
            $table->decimal('amount', 19, 4);
            $table->string('currency', 3)->default('EGP');
            $table->timestamps();
            $table->index(['source_type', 'source_id']);
        });

        Schema::create('payment_instruments', function (Blueprint $table) {
            $table->id();
            $table->string('instrument_number', 64)->unique();
            $table->string('instrument_type', 24); // cheque, bank_transfer, wallet_transfer
            $table->foreignId('source_cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->decimal('amount', 19, 4);
            $table->string('currency', 3)->default('EGP');
            $table->string('beneficiary_type', 80)->nullable();
            $table->unsignedBigInteger('beneficiary_id')->nullable();
            $table->string('beneficiary_name', 191)->nullable();
            $table->string('status', 24)->default('draft');
            $table->date('issued_on')->nullable();
            $table->date('cleared_on')->nullable();
            $table->string('external_reference', 191)->nullable();
            $table->foreignId('fund_id')->nullable()->constrained('funds')->restrictOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->foreignId('expense_account_id')->nullable()->constrained('financial_accounts')->restrictOnDelete();
            $table->foreignId('ledger_entry_id')->nullable()->unique()->constrained('ledger_entries')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users')->restrictOnDelete();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['source_cash_account_id', 'status', 'issued_on'], 'pay_inst_src_cash_stat_issued_idx');
        });

        Schema::create('custodies', function (Blueprint $table) {
            $table->id();
            $table->string('custody_number', 64)->unique();
            $table->foreignId('holder_user_id')->constrained('users')->restrictOnDelete();
            $table->foreignId('source_cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->foreignId('custody_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->decimal('issued_amount', 19, 4);
            $table->decimal('settled_amount', 19, 4)->default(0);
            $table->decimal('returned_amount', 19, 4)->default(0);
            $table->string('currency', 3)->default('EGP');
            $table->foreignId('fund_id')->nullable()->constrained('funds')->restrictOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->date('issued_on');
            $table->date('due_on')->nullable();
            $table->string('status', 24)->default('draft');
            $table->foreignId('issue_entry_id')->nullable()->unique()->constrained('ledger_entries')->nullOnDelete();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('closed_at')->nullable();
            $table->text('purpose')->nullable();
            $table->timestamps();
            $table->index(['holder_user_id', 'status', 'due_on']);
        });

        Schema::create('custody_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('custody_id')->constrained('custodies')->cascadeOnDelete();
            $table->date('expense_date');
            $table->decimal('amount', 19, 4);
            $table->foreignId('expense_account_id')->constrained('financial_accounts')->restrictOnDelete();
            $table->foreignId('fund_id')->nullable()->constrained('funds')->restrictOnDelete();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('campaign_id')->nullable();
            $table->string('invoice_number', 128)->nullable();
            $table->string('merchant_name', 191)->nullable();
            $table->string('status', 20)->default('draft');
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ledger_entry_id')->nullable()->unique()->constrained('ledger_entries')->nullOnDelete();
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['custody_id', 'status']);
            $table->unique(['custody_id', 'invoice_number']);
        });

        Schema::create('bank_reconciliations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cash_account_id')->constrained('cash_accounts')->restrictOnDelete();
            $table->date('statement_from');
            $table->date('statement_to');
            $table->decimal('statement_closing_balance', 19, 4);
            $table->string('status', 20)->default('draft');
            $table->foreignId('prepared_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->unique(['cash_account_id', 'statement_from', 'statement_to'], 'bank_rec_acc_stmt_dates_unique');
        });

        Schema::create('bank_reconciliation_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_reconciliation_id')->constrained('bank_reconciliations')->cascadeOnDelete();
            $table->foreignId('ledger_entry_id')->nullable()->constrained('ledger_entries')->restrictOnDelete();
            $table->date('statement_date');
            $table->decimal('amount', 19, 4);
            $table->string('direction', 6); // debit or credit on the statement
            $table->string('bank_reference', 191)->nullable();
            $table->string('status', 24)->default('unmatched');
            $table->text('note')->nullable();
            $table->timestamps();
            $table->index(['bank_reconciliation_id', 'status'], 'bank_rec_lines_rec_stat_idx');
        });

        Schema::create('financial_approvals', function (Blueprint $table) {
            $table->id();
            $table->string('approvable_type', 80);
            $table->unsignedBigInteger('approvable_id');
            $table->string('stage', 32);
            $table->string('decision', 20)->default('pending');
            $table->foreignId('requested_by')->constrained('users')->restrictOnDelete();
            $table->foreignId('decided_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('decided_at')->nullable();
            $table->string('comment', 1000)->nullable();
            $table->timestamps();
            $table->unique(['approvable_type', 'approvable_id', 'stage'], 'fin_appr_type_id_stage_unique');
            $table->index(['decision', 'created_at']);
        });
    }

    public function down(): void
    {
        foreach ([
            'financial_approvals', 'bank_reconciliation_lines', 'bank_reconciliations',
            'custody_expenses', 'custodies', 'payment_instruments', 'cash_transfer_items',
            'cash_transfers', 'donation_allocations', 'fund_movements',
            'ledger_entry_lines', 'ledger_entries', 'financial_documents', 'cash_account_users',
            'cash_accounts', 'fund_balances', 'funds', 'accounting_periods', 'financial_accounts',
        ] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
