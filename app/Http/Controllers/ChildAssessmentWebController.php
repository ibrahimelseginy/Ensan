<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\BeneficiaryFamilyMember;
use App\Models\ChildAssessment;
use App\Services\ImageUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ChildAssessmentWebController extends Controller
{
    public function storeEntry(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $existing = $familyMember->entryAssessment;
        if ($existing && $existing->is_locked) {
            return back()->with('error', 'تم اعتماد وقفل تقييم الدخول مسبقاً، والنموذج غير قابل للتعديل بعد تنفيذه.');
        }

        $data = $request->validate([
            'assessment_date' => 'nullable|date',
            'reviewer_name' => 'required|string|max:150',
            'reviewer_role' => 'required|in:employee,volunteer',
            'academic_rating' => 'nullable|numeric|min:0|max:100',
            'psychological_rating' => 'nullable|numeric|min:0|max:100',
            'social_rating' => 'nullable|numeric|min:0|max:100',
            'health_rating' => 'nullable|numeric|min:0|max:100',
            'score_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
            'data' => 'nullable|array',
            'test_image' => 'nullable|file|max:15360',
        ]);

        $score = $data['score_percentage'] ?? null;
        if ($score === null) {
            $parts = array_filter([
                $data['academic_rating'] ?? null,
                $data['psychological_rating'] ?? null,
                $data['social_rating'] ?? null,
            ], fn($v) => $v !== null && $v !== '');
            $score = !empty($parts) ? round(array_sum($parts) / count($parts), 1) : null;
        }

        $imagePath = null;
        if ($request->hasFile('test_image')) {
            $imagePath = app(ImageUploadService::class)->upload($request->file('test_image'), 'children/assessments');
        }

        ChildAssessment::create([
            'family_member_id' => $familyMember->id,
            'beneficiary_id' => $familyMember->beneficiary_id,
            'type' => 'entry',
            'assessment_date' => $data['assessment_date'] ?? now()->toDateString(),
            'reviewer_name' => $data['reviewer_name'],
            'reviewer_role' => $data['reviewer_role'],
            'score_percentage' => $score,
            'academic_rating' => $data['academic_rating'] ?? null,
            'psychological_rating' => $data['psychological_rating'] ?? null,
            'social_rating' => $data['social_rating'] ?? null,
            'health_rating' => $data['health_rating'] ?? null,
            'test_image_path' => $imagePath,
            'is_locked' => true,
            'notes' => $data['notes'] ?? null,
            'data' => $data['data'] ?? [],
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم تسجيل تقييم دخول الطفل الشامل واعتماده وإغلاقه بنجاح.');
    }

    public function storeMonthly(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $data = $request->validate([
            'assessment_date' => 'required|date',
            'reviewer_name' => 'required|string|max:150',
            'reviewer_role' => 'required|in:employee,volunteer',
            'academic_rating' => 'required|numeric|min:0|max:100',
            'psychological_rating' => 'required|numeric|min:0|max:100',
            'social_rating' => 'nullable|numeric|min:0|max:100',
            'score_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable|string|max:2000',
            'data' => 'nullable|array',
            'test_image' => 'nullable|file|max:15360',
        ]);

        $score = $data['score_percentage'] ?? null;
        if ($score === null) {
            $ratings = array_filter([
                $data['academic_rating'],
                $data['psychological_rating'],
                $data['social_rating'] ?? null,
            ], fn($v) => $v !== null && $v !== '');
            $score = !empty($ratings) ? round(array_sum($ratings) / count($ratings), 1) : (float)$data['academic_rating'];
        }

        $imagePath = null;
        if ($request->hasFile('test_image')) {
            $imagePath = app(ImageUploadService::class)->upload($request->file('test_image'), 'children/assessments');
        }

        ChildAssessment::create([
            'family_member_id' => $familyMember->id,
            'beneficiary_id' => $familyMember->beneficiary_id,
            'type' => 'monthly',
            'assessment_date' => $data['assessment_date'],
            'reviewer_name' => $data['reviewer_name'],
            'reviewer_role' => $data['reviewer_role'],
            'score_percentage' => $score,
            'academic_rating' => $data['academic_rating'],
            'psychological_rating' => $data['psychological_rating'],
            'social_rating' => $data['social_rating'] ?? null,
            'test_image_path' => $imagePath,
            'is_locked' => false,
            'notes' => $data['notes'] ?? null,
            'data' => $data['data'] ?? [],
            'created_by' => auth()->id(),
        ]);

        return back()->with('success', 'تم حفظ التقييم الشهري للطفل وإرفاق الاختبار بنجاح.');
    }

    public function uploadDocument(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $request->validate([
            'document' => 'required|file|max:20480',
            'name' => 'nullable|string|max:150',
        ]);

        $file = $request->file('document');
        $path = app(ImageUploadService::class)->upload($file, 'children/documents');

        $familyMember->attachments()->create([
            'path' => $path,
            'mime' => $file->getClientMimeType(),
            'original_name' => $request->input('name') ?: $file->getClientOriginalName(),
        ]);

        return back()->with('success', 'تم إرفاق ملف الطفل وحفظه بنجاح.');
    }

    public function show(ChildAssessment $assessment): View
    {
        $assessment->load(['familyMember.sponsors', 'familyMember.beneficiary.project', 'creator']);
        return view('beneficiaries.family-members.assessment-show', compact('assessment'));
    }

    public function report(BeneficiaryFamilyMember $familyMember): View
    {
        $familyMember->load([
            'beneficiary.project',
            'sponsors',
            'entryAssessment',
            'latestMonthlyAssessment',
            'assessments',
            'attachments',
        ]);

        return view('beneficiaries.family-members.child-dossier-report', compact('familyMember'));
    }
}
