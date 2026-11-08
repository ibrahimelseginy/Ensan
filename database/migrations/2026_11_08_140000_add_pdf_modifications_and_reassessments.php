<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Add national_id to users table (Employees & Volunteers)
        if (Schema::hasTable('users') && !Schema::hasColumn('users', 'national_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('national_id', 25)->nullable()->after('phone');
            });
        }

        // 2. Add department, guest_house_id, project_id, assigned_user_ids to warehouses table
        if (Schema::hasTable('warehouses')) {
            Schema::table('warehouses', function (Blueprint $table) {
                if (!Schema::hasColumn('warehouses', 'department')) {
                    $table->string('department')->nullable();
                }
                if (!Schema::hasColumn('warehouses', 'guest_house_id')) {
                    $table->unsignedBigInteger('guest_house_id')->nullable();
                }
                if (!Schema::hasColumn('warehouses', 'project_id')) {
                    $table->unsignedBigInteger('project_id')->nullable();
                }
                if (!Schema::hasColumn('warehouses', 'assigned_user_ids')) {
                    $table->json('assigned_user_ids')->nullable();
                }
            });
        }

        // 3. Create beneficiary_reassessments table (6-month periodic family & patient reassessment)
        if (!Schema::hasTable('beneficiary_reassessments')) {
            Schema::create('beneficiary_reassessments', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('beneficiary_id')->index();
                $table->unsignedBigInteger('family_member_id')->nullable()->index();
                $table->unsignedBigInteger('created_by')->nullable();

                // Section 1: الأسرة والظروف الاجتماعية
                $table->string('patient_name')->nullable();
                $table->string('guardian_name')->nullable();
                $table->integer('current_family_count')->nullable();
                $table->string('caregiver_name')->nullable();
                $table->text('children_marriage_or_dropout')->nullable();
                $table->text('other_chronic_illness')->nullable();
                $table->text('impact_on_family_work')->nullable();
                $table->text('family_status_changes')->nullable();

                // Section 2: التحديث الطبي
                $table->string('current_diagnosis')->nullable();
                $table->string('diagnosis_stage')->nullable(); // يتلقى العلاج / مرحلة متابعة
                $table->string('current_treatment_type')->nullable(); // كيماوي / إشعاعي / مناعي / هرموني / متابعة فقط
                $table->string('remaining_sessions')->nullable();
                $table->string('planned_surgery')->nullable();
                $table->string('condition_progress')->nullable(); // تحسن / استقرار / تدهور / وفاة
                $table->string('response_rate')->nullable();
                $table->text('side_effects_impact')->nullable();
                $table->string('current_hospital')->nullable();
                $table->decimal('monthly_treatment_cost', 12, 2)->nullable();
                $table->text('unavailable_medications')->nullable();
                $table->text('exact_medical_need')->nullable();

                // Section 3: تقييم الاحتياج الحالي
                $table->string('breadwinner_still_working')->nullable();
                $table->decimal('current_family_income', 12, 2)->nullable();
                $table->string('new_income_source')->nullable();
                $table->string('government_support_or_pension')->nullable();
                $table->string('new_medical_debts')->nullable();
                $table->decimal('current_debts_amount', 12, 2)->nullable();
                $table->text('overdue_rent_or_installments')->nullable();

                // Section 4: التحديث المادي
                $table->json('most_needed_support')->nullable(); // علاج، أدوية، فحوصات، مواصلات علاج، إعاشة، إيجار
                $table->text('expected_impact_if_stopped')->nullable();
                $table->text('external_aid')->nullable();
                $table->decimal('requested_monthly_aid', 12, 2)->nullable();

                // Section 5: ملاحظات الباحث
                $table->text('yearly_progress_summary')->nullable();
                $table->text('treatment_compliance')->nullable();
                $table->text('need_for_continued_support')->nullable();

                // Section 6: توصية الباحث والاعتماد
                $table->string('researcher_recommendation')->nullable(); // استمرار الدعم كما هو / زيادة الدعم / تخفيض الدعم / إيقاف الدعم
                $table->text('recommendation_reasons')->nullable();
                $table->string('researcher_name')->nullable();
                $table->string('approved_by')->nullable();
                $table->date('reassessed_at')->nullable();
                $table->date('next_reassessment_due_at')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('beneficiary_reassessments');

        if (Schema::hasTable('warehouses')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $cols = [];
                foreach (['department', 'guest_house_id', 'project_id', 'assigned_user_ids'] as $col) {
                    if (Schema::hasColumn('warehouses', $col)) {
                        $cols[] = $col;
                    }
                }
                if (!empty($cols)) {
                    $table->dropColumn($cols);
                }
            });
        }

        if (Schema::hasTable('users') && Schema::hasColumn('users', 'national_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('national_id');
            });
        }
    }
};
