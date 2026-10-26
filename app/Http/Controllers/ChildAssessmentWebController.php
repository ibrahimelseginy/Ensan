<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BeneficiaryFamilyMember;
use App\Models\ChildAssessment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ChildAssessmentWebController extends Controller
{
    public function storeEntry(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $existing = $familyMember->entryAssessment;
        if ($existing && $existing->is_locked) {
            return back()->with('error', 'تم إجراء التقييم الشامل واعتماده وإغلاقه مسبقاً، لا يمكن التعديل عليه.');
        }

        $data = $request->validate([
            'evaluator_name' => 'required|string|max:150',
            'evaluator_role' => 'nullable|string|max:100',
            'quran_memorized_parts' => 'nullable|string|max:100',
            'quran_grade_percentage' => 'nullable|numeric|min:0|max:100',
            'hadith_grade_percentage' => 'nullable|numeric|min:0|max:100',
            'prayer_commitment_level' => 'nullable|string|max:100',
            'morals_grade_percentage' => 'nullable|numeric|min:0|max:100',
            'overall_percentage' => 'nullable|numeric|min:0|max:100',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'form_data' => 'nullable|array',
            'test_image' => 'nullable|any_image|max:10240',
        ]);

        $overall = $data['overall_percentage'] ?? null;
        if ($overall === null && isset($data['quran_grade_percentage'], $data['hadith_grade_percentage'], $data['morals_grade_percentage'])) {
            $overall = round(((float)$data['quran_grade_percentage'] + (float)$data['hadith_grade_percentage'] + (float)$data['morals_grade_percentage']) / 3, 1);
        }

        $assessment = ChildAssessment::create([
            'child_id' => $familyMember->id,
            'beneficiary_id' => $familyMember->beneficiary_id,
            'assessment_type' => ChildAssessment::TYPE_ENTRY,
            'assessment_month' => (int)now()->month,
            'assessment_year' => (int)now()->year,
            'is_locked' => true,
            'evaluator_name' => $data['evaluator_name'],
            'evaluator_role' => $data['evaluator_role'] ?? 'أخصائي التقييم والمتابعة',
            'quran_memorized_parts' => $data['quran_memorized_parts'] ?? null,
            'quran_grade_percentage' => $data['quran_grade_percentage'] ?? null,
            'hadith_grade_percentage' => $data['hadith_grade_percentage'] ?? null,
            'prayer_commitment_level' => $data['prayer_commitment_level'] ?? null,
            'morals_grade_percentage' => $data['morals_grade_percentage'] ?? null,
            'overall_percentage' => $overall,
            'strengths' => $data['strengths'] ?? null,
            'weaknesses' => $data['weaknesses'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
            'notes' => $data['notes'] ?? null,
            'form_data' => $data['form_data'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if ($request->hasFile('test_image')) {
            $assessment->uploadImage($request->file('test_image'), 'children/assessments');
        }

        return back()->with('success', 'تم تسجيل واعتماد التقييم العام الشامل لدخول المشروع وقفل النموذج بنجاح.');
    }

    public function storeMonthly(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $data = $request->validate([
            'assessment_month' => 'required|integer|between:1,12',
            'assessment_year' => 'required|integer|between:2020,2035',
            'evaluator_name' => 'required|string|max:150',
            'evaluator_role' => 'nullable|string|max:100',
            'quran_memorized_parts' => 'nullable|string|max:100',
            'quran_grade_percentage' => 'required|numeric|min:0|max:100',
            'hadith_grade_percentage' => 'nullable|numeric|min:0|max:100',
            'prayer_commitment_level' => 'nullable|string|max:100',
            'morals_grade_percentage' => 'nullable|numeric|min:0|max:100',
            'overall_percentage' => 'nullable|numeric|min:0|max:100',
            'strengths' => 'nullable|string|max:1000',
            'weaknesses' => 'nullable|string|max:1000',
            'recommendations' => 'nullable|string|max:1000',
            'notes' => 'nullable|string|max:2000',
            'test_image' => 'nullable|any_image|max:10240',
        ]);

        $overall = $data['overall_percentage'] ?? null;
        if ($overall === null) {
            $parts = array_filter([
                $data['quran_grade_percentage'],
                $data['hadith_grade_percentage'] ?? null,
                $data['morals_grade_percentage'] ?? null,
            ], fn($v) => $v !== null && $v !== '');
            $overall = !empty($parts) ? round(array_sum($parts) / count($parts), 1) : $data['quran_grade_percentage'];
        }

        $assessment = ChildAssessment::create([
            'child_id' => $familyMember->id,
            'beneficiary_id' => $familyMember->beneficiary_id,
            'assessment_type' => ChildAssessment::TYPE_MONTHLY,
            'assessment_month' => (int)$data['assessment_month'],
            'assessment_year' => (int)$data['assessment_year'],
            'is_locked' => false,
            'evaluator_name' => $data['evaluator_name'],
            'evaluator_role' => $data['evaluator_role'] ?? 'معلم/مشرف المتابعة',
            'quran_memorized_parts' => $data['quran_memorized_parts'] ?? null,
            'quran_grade_percentage' => $data['quran_grade_percentage'],
            'hadith_grade_percentage' => $data['hadith_grade_percentage'] ?? null,
            'prayer_commitment_level' => $data['prayer_commitment_level'] ?? null,
            'morals_grade_percentage' => $data['morals_grade_percentage'] ?? null,
            'overall_percentage' => $overall,
            'strengths' => $data['strengths'] ?? null,
            'weaknesses' => $data['weaknesses'] ?? null,
            'recommendations' => $data['recommendations'] ?? null,
            'notes' => $data['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        if ($request->hasFile('test_image')) {
            $assessment->uploadImage($request->file('test_image'), 'children/assessments');
        }

        return back()->with('success', 'تم حفظ نتيجة التقييم الشهري للطفل وإرفاق الاختبار بنجاح.');
    }

    public function show(ChildAssessment $assessment): View
    {
        $assessment->load(['child.sponsors', 'child.beneficiary', 'creator']);
        return view('beneficiaries.family-members.assessment-show', compact('assessment'));
    }
}
