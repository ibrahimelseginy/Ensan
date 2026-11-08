<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryReassessment;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class BeneficiaryReassessmentController extends Controller
{
    public static function generatePublicToken(int $beneficiaryId): string
    {
        return substr(hash_hmac('sha256', "reassessment:{$beneficiaryId}", (string) config('app.key')), 0, 24);
    }

    public static function getPublicUrl(int $beneficiaryId): string
    {
        return route('public.reassessment.form', [
            'beneficiary' => $beneficiaryId,
            'token' => self::generatePublicToken($beneficiaryId),
        ]);
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $data = $this->validateReassessment($request);
        $data['beneficiary_id'] = $beneficiary->id;
        $data['created_by'] = auth()->id();

        $reassessedAt = !empty($data['reassessed_at']) ? Carbon::parse($data['reassessed_at']) : now();
        $data['reassessed_at'] = $reassessedAt->toDateString();
        $data['next_reassessment_due_at'] = $reassessedAt->copy()->addMonths(6)->toDateString();

        BeneficiaryReassessment::create($data);

        return back()->with('success', 'تم حفظ استمارة إعادة البحث الدورية (تحديث الـ 6 أشهر) بنجاح مع الاحتفاظ بكامل البيانات الأساسية للأسرة.');
    }

    public function publicForm(int $beneficiary, string $token): View
    {
        abort_unless(hash_equals(self::generatePublicToken($beneficiary), $token), 403, 'رابط الاستمارة غير صالح.');

        $beneficiaryModel = Beneficiary::with('familyMembers')->findOrFail($beneficiary);

        return view('beneficiaries.reassessment_public', [
            'beneficiary' => $beneficiaryModel,
            'token' => $token,
        ]);
    }

    public function publicSubmit(Request $request, int $beneficiary, string $token): RedirectResponse
    {
        abort_unless(hash_equals(self::generatePublicToken($beneficiary), $token), 403, 'رابط الاستمارة غير صالح.');

        $beneficiaryModel = Beneficiary::findOrFail($beneficiary);
        $data = $this->validateReassessment($request);
        $data['beneficiary_id'] = $beneficiaryModel->id;
        $data['created_by'] = auth()->id();

        $reassessedAt = !empty($data['reassessed_at']) ? Carbon::parse($data['reassessed_at']) : now();
        $data['reassessed_at'] = $reassessedAt->toDateString();
        $data['next_reassessment_due_at'] = $reassessedAt->copy()->addMonths(6)->toDateString();

        BeneficiaryReassessment::create($data);

        return back()->with('submitted_success', 'تم تسجيل استمارة إعادة البحث وتحديث بيانات الـ 6 أشهر للأسرة في النظام بنجاح!');
    }

    public function destroy(BeneficiaryReassessment $reassessment): RedirectResponse
    {
        $reassessment->delete();
        return back()->with('success', 'تم حذف سجل إعادة البحث.');
    }

    private function validateReassessment(Request $request): array
    {
        return $request->validate([
            'family_member_id' => 'nullable|exists:beneficiary_family_members,id',
            'patient_name' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'current_family_count' => 'nullable|integer|min:1|max:50',
            'caregiver_name' => 'nullable|string|max:255',
            'children_marriage_or_dropout' => 'nullable|string|max:2000',
            'other_chronic_illness' => 'nullable|string|max:2000',
            'impact_on_family_work' => 'nullable|string|max:2000',
            'family_status_changes' => 'nullable|string|max:2000',

            'current_diagnosis' => 'nullable|string|max:255',
            'diagnosis_stage' => 'nullable|string|max:100',
            'current_treatment_type' => 'nullable|string|max:100',
            'remaining_sessions' => 'nullable|string|max:255',
            'planned_surgery' => 'nullable|string|max:255',
            'condition_progress' => 'nullable|string|max:100',
            'response_rate' => 'nullable|string|max:255',
            'side_effects_impact' => 'nullable|string|max:2000',
            'current_hospital' => 'nullable|string|max:255',
            'monthly_treatment_cost' => 'nullable|numeric|min:0',
            'unavailable_medications' => 'nullable|string|max:2000',
            'exact_medical_need' => 'nullable|string|max:2000',

            'breadwinner_still_working' => 'nullable|string|max:255',
            'current_family_income' => 'nullable|numeric|min:0',
            'new_income_source' => 'nullable|string|max:255',
            'government_support_or_pension' => 'nullable|string|max:255',
            'new_medical_debts' => 'nullable|string|max:255',
            'current_debts_amount' => 'nullable|numeric|min:0',
            'overdue_rent_or_installments' => 'nullable|string|max:2000',

            'most_needed_support' => 'nullable|array',
            'most_needed_support.*' => 'string|max:100',
            'expected_impact_if_stopped' => 'nullable|string|max:2000',
            'external_aid' => 'nullable|string|max:2000',
            'requested_monthly_aid' => 'nullable|numeric|min:0',

            'yearly_progress_summary' => 'nullable|string|max:3000',
            'treatment_compliance' => 'nullable|string|max:2000',
            'need_for_continued_support' => 'nullable|string|max:2000',

            'researcher_recommendation' => 'nullable|string|max:150',
            'recommendation_reasons' => 'nullable|string|max:3000',
            'researcher_name' => 'nullable|string|max:255',
            'approved_by' => 'nullable|string|max:255',
            'reassessed_at' => 'nullable|date',
        ]);
    }
}
