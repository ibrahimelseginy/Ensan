<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->decimal('income_job', 10, 2)->default(0)->nullable()->change();
            $table->decimal('income_daily_work', 10, 2)->default(0)->nullable()->change();
            $table->decimal('income_pension', 10, 2)->default(0)->nullable()->change();
            $table->decimal('income_social_affairs', 10, 2)->default(0)->nullable()->change();
            $table->decimal('income_charities', 10, 2)->default(0)->nullable()->change();
            $table->decimal('total_income', 10, 2)->default(0)->nullable()->change();

            $table->decimal('expense_rent', 10, 2)->default(0)->nullable()->change();
            $table->decimal('expense_tuition', 10, 2)->default(0)->nullable()->change();
            $table->decimal('expense_food_weekly', 10, 2)->default(0)->nullable()->change();
            $table->decimal('expense_transportation', 10, 2)->default(0)->nullable()->change();
            $table->decimal('expense_utilities', 10, 2)->default(0)->nullable()->change();
            $table->decimal('total_expenses', 10, 2)->default(0)->nullable()->change();

            $table->decimal('total_debts', 10, 2)->default(0)->nullable()->change();
            $table->decimal('debt_repaid', 10, 2)->default(0)->nullable()->change();
            $table->decimal('debt_remaining', 10, 2)->default(0)->nullable()->change();
            $table->decimal('debt_monthly_installment', 10, 2)->default(0)->nullable()->change();

            $table->integer('chemo_sessions_taken')->default(0)->nullable()->change();
            $table->integer('chemo_sessions_remaining')->default(0)->nullable()->change();
            $table->integer('radiation_sessions_taken')->default(0)->nullable()->change();
            $table->integer('radiation_sessions_remaining')->default(0)->nullable()->change();
        });
    }

    public function down(): void
    {
        // No-op
    }
};

