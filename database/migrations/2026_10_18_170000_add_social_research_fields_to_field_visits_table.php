<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            // Case & Researcher Info
            $table->string('patient_name')->nullable()->after('researcher_id');
            $table->string('guardian_name')->nullable()->after('patient_name');
            $table->string('national_id', 14)->nullable()->after('guardian_name');
            $table->string('phone', 20)->nullable()->after('national_id');
            $table->string('researcher_phone', 20)->nullable()->after('phone');
            $table->string('home_ownership', 50)->nullable()->after('researcher_phone'); // ملك / إيجار
            $table->text('address')->nullable()->after('home_ownership');
            $table->text('researcher_opinion')->nullable()->after('address');

            // Family Members (JSON array of {name, relation, age, income, job, notes})
            $table->json('family_members')->nullable()->after('researcher_opinion');

            // Income Sources
            $table->decimal('income_job', 10, 2)->default(0)->after('family_members');
            $table->decimal('income_daily_work', 10, 2)->default(0)->after('income_job');
            $table->decimal('income_pension', 10, 2)->default(0)->after('income_daily_work');
            $table->decimal('income_social_affairs', 10, 2)->default(0)->after('income_pension');
            $table->decimal('income_charities', 10, 2)->default(0)->after('income_social_affairs');
            $table->decimal('total_income', 10, 2)->default(0)->after('income_charities');

            // Expenses
            $table->decimal('expense_rent', 10, 2)->default(0)->after('total_income');
            $table->decimal('expense_tuition', 10, 2)->default(0)->after('expense_rent');
            $table->decimal('expense_food_weekly', 10, 2)->default(0)->after('expense_tuition');
            $table->decimal('expense_transportation', 10, 2)->default(0)->after('expense_food_weekly');
            $table->decimal('expense_utilities', 10, 2)->default(0)->after('expense_transportation');
            $table->decimal('total_expenses', 10, 2)->default(0)->after('expense_utilities');

            // Debts
            $table->decimal('total_debts', 10, 2)->default(0)->after('total_expenses');
            $table->decimal('debt_repaid', 10, 2)->default(0)->after('total_debts');
            $table->decimal('debt_remaining', 10, 2)->default(0)->after('debt_repaid');
            $table->decimal('debt_monthly_installment', 10, 2)->default(0)->after('debt_remaining');
            $table->text('debt_reason')->nullable()->after('debt_monthly_installment');

            // Case Guide & Needs & Social Condition
            $table->text('case_guide')->nullable()->after('debt_reason');
            $table->text('case_needs')->nullable()->after('case_guide');
            $table->text('home_and_social_condition')->nullable()->after('case_needs');

            // Conditional Question: Has Cancer Patient
            $table->boolean('has_cancer_patient')->default(false)->after('home_and_social_condition');

            // Oncology Patient Details (Visible only when has_cancer_patient == true)
            $table->string('hospital_name')->nullable()->after('has_cancer_patient');
            $table->string('tumor_location')->nullable()->after('hospital_name');
            $table->string('disease_discovery_date')->nullable()->after('tumor_location');
            $table->string('disease_duration')->nullable()->after('disease_discovery_date');
            $table->integer('chemo_sessions_taken')->default(0)->after('disease_duration');
            $table->integer('chemo_sessions_remaining')->default(0)->after('chemo_sessions_taken');
            $table->integer('radiation_sessions_taken')->default(0)->after('chemo_sessions_remaining');
            $table->integer('radiation_sessions_remaining')->default(0)->after('radiation_sessions_taken');
            $table->string('hospital_visit_frequency')->nullable()->after('radiation_sessions_remaining');
            $table->string('patient_psychological_state')->nullable()->after('hospital_visit_frequency');

            // Notes and Protocol
            $table->text('before_tumor_notes')->nullable()->after('patient_psychological_state');
            $table->text('after_tumor_notes')->nullable()->after('before_tumor_notes');
            $table->text('treatment_protocol')->nullable()->after('after_tumor_notes');

            // Approvals
            $table->string('foundation_manager')->nullable()->after('treatment_protocol');
            $table->string('project_manager')->nullable()->after('foundation_manager');
        });
    }

    public function down(): void
    {
        Schema::table('field_visits', function (Blueprint $table) {
            $table->dropColumn([
                'patient_name', 'guardian_name', 'national_id', 'phone', 'researcher_phone',
                'home_ownership', 'address', 'researcher_opinion', 'family_members',
                'income_job', 'income_daily_work', 'income_pension', 'income_social_affairs', 'income_charities', 'total_income',
                'expense_rent', 'expense_tuition', 'expense_food_weekly', 'expense_transportation', 'expense_utilities', 'total_expenses',
                'total_debts', 'debt_repaid', 'debt_remaining', 'debt_monthly_installment', 'debt_reason',
                'case_guide', 'case_needs', 'home_and_social_condition',
                'has_cancer_patient',
                'hospital_name', 'tumor_location', 'disease_discovery_date', 'disease_duration',
                'chemo_sessions_taken', 'chemo_sessions_remaining', 'radiation_sessions_taken', 'radiation_sessions_remaining',
                'hospital_visit_frequency', 'patient_psychological_state',
                'before_tumor_notes', 'after_tumor_notes', 'treatment_protocol',
                'foundation_manager', 'project_manager'
            ]);
        });
    }
};
