<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class BeneficiaryReassessment extends Model
{
    protected $fillable = [
        'beneficiary_id',
        'family_member_id',
        'created_by',
        // Section 1: الأسرة والظروف الاجتماعية
        'patient_name',
        'guardian_name',
        'current_family_count',
        'caregiver_name',
        'children_marriage_or_dropout',
        'other_chronic_illness',
        'impact_on_family_work',
        'family_status_changes',
        // Section 2: التحديث الطبي
        'current_diagnosis',
        'diagnosis_stage',
        'current_treatment_type',
        'remaining_sessions',
        'planned_surgery',
        'condition_progress',
        'response_rate',
        'side_effects_impact',
        'current_hospital',
        'monthly_treatment_cost',
        'unavailable_medications',
        'exact_medical_need',
        // Section 3: تقييم الاحتياج الحالي
        'breadwinner_still_working',
        'current_family_income',
        'new_income_source',
        'government_support_or_pension',
        'new_medical_debts',
        'current_debts_amount',
        'overdue_rent_or_installments',
        // Section 4: التحديث المادي
        'most_needed_support',
        'expected_impact_if_stopped',
        'external_aid',
        'requested_monthly_aid',
        // Section 5: ملاحظات الباحث
        'yearly_progress_summary',
        'treatment_compliance',
        'need_for_continued_support',
        // Section 6: توصية الباحث والاعتماد
        'researcher_recommendation',
        'recommendation_reasons',
        'researcher_name',
        'approved_by',
        'reassessed_at',
        'next_reassessment_due_at',
    ];

    protected $casts = [
        'most_needed_support' => 'array',
        'monthly_treatment_cost' => 'decimal:2',
        'current_family_income' => 'decimal:2',
        'current_debts_amount' => 'decimal:2',
        'requested_monthly_aid' => 'decimal:2',
        'reassessed_at' => 'date',
        'next_reassessment_due_at' => 'date',
    ];

    public function beneficiary(): BelongsTo
    {
        return $this->belongsTo(Beneficiary::class);
    }

    public function familyMember(): BelongsTo
    {
        return $this->belongsTo(BeneficiaryFamilyMember::class, 'family_member_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

