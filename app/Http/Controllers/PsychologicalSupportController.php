<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\BeneficiaryFamilyMember;
use App\Models\PsychologicalSupportReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PsychologicalSupportController extends Controller
{
    public function index(Request $request): View
    {
        $currentMonth = $request->input('month', now()->format('Y-m'));

        $query = PsychologicalSupportReport::with(['beneficiary', 'familyMember', 'creator'])
            ->where('report_month', $currentMonth)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->whereHas('beneficiary', function ($bq) use ($search) {
                    $bq->where('full_name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhereHas('familyMember', function ($fq) use ($search) {
                    $fq->where('full_name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                })
                ->orWhere('volunteer_name', 'like', "%{$search}%");
            });
        }

        $reports = $query->paginate(20)->withQueryString();

        // Available months list for selector
        $availableMonths = PsychologicalSupportReport::select('report_month')
            ->distinct()
            ->orderByDesc('report_month')
            ->pluck('report_month')
            ->all();

        if (!in_array($currentMonth, $availableMonths, true)) {
            array_unshift($availableMonths, $currentMonth);
        }

        return view('psychological_support.index', compact('reports', 'currentMonth', 'availableMonths'));
    }

    public function store(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'report_month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'volunteer_name' => 'required|string|max:191',
            'medical_condition' => 'nullable|string|max:255',
            'treatment_stage' => 'nullable|string|max:255',
            'call_condition' => 'nullable|string',
            'psychological_state' => 'required|string|in:ممتاز,كويس,راضي,حزين,يشعر بالضجر',
            'rating' => 'required|integer|min:1|max:5',
            'needs_improvement' => 'nullable|string',
            'nutrition_diet' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'supporter_opinion' => 'nullable|string',
            'family_member_id' => 'nullable|exists:beneficiary_family_members,id',
        ], [
            'volunteer_name.required' => 'يرجى كتابة اسم المتطوع ثلاثي.',
            'psychological_state.required' => 'يرجى تحديد الحالة النفسية للمريض.',
            'rating.required' => 'يرجى تحديد تقييم الحالة النفسية من 1 إلى 5.',
        ]);

        $reportMonth = $validated['report_month'];
        $familyMemberId = !empty($validated['family_member_id']) ? (int) $validated['family_member_id'] : null;

        $matchCriteria = [
            'beneficiary_id' => $beneficiary->id,
            'report_month' => $reportMonth,
        ];
        if ($familyMemberId) {
            $matchCriteria['family_member_id'] = $familyMemberId;
        }

        $report = PsychologicalSupportReport::updateOrCreate(
            $matchCriteria,
            array_merge($validated, [
                'beneficiary_id' => $beneficiary->id,
                'family_member_id' => $familyMemberId,
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ])
        );

        $name = $familyMemberId && $report->familyMember ? $report->familyMember->full_name : $beneficiary->full_name;

        return back()->with('success', "تم توثيق تقرير الدعم النفسي لشهر [{$reportMonth}] للحالة [{$name}] بنجاح.");
    }

    public function storeMember(Request $request, BeneficiaryFamilyMember $familyMember): RedirectResponse
    {
        $validated = $request->validate([
            'report_month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'volunteer_name' => 'required|string|max:191',
            'medical_condition' => 'nullable|string|max:255',
            'treatment_stage' => 'nullable|string|max:255',
            'call_condition' => 'nullable|string',
            'psychological_state' => 'required|string|in:ممتاز,كويس,راضي,حزين,يشعر بالضجر',
            'rating' => 'required|integer|min:1|max:5',
            'needs_improvement' => 'nullable|string',
            'nutrition_diet' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'supporter_opinion' => 'nullable|string',
        ], [
            'volunteer_name.required' => 'يرجى كتابة اسم المتطوع ثلاثي.',
            'psychological_state.required' => 'يرجى تحديد الحالة النفسية للمريض.',
            'rating.required' => 'يرجى تحديد تقييم الحالة النفسية من 1 إلى 5.',
        ]);

        $reportMonth = $validated['report_month'];

        $report = PsychologicalSupportReport::updateOrCreate(
            [
                'family_member_id' => $familyMember->id,
                'report_month' => $reportMonth,
            ],
            array_merge($validated, [
                'beneficiary_id' => $familyMember->beneficiary_id,
                'family_member_id' => $familyMember->id,
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ])
        );

        return back()->with('success', "تم توثيق تقرير الدعم النفسي لشهر [{$reportMonth}] للحالة المريضة [{$familyMember->full_name}] بنجاح.");
    }

    public static function generatePublicToken(string $type, int $id): string
    {
        return substr(hash_hmac('sha256', "psych:{$type}:{$id}", (string) config('app.key')), 0, 24);
    }

    public static function getPublicUrl(string $type, int $id): string
    {
        return route('public.psychological-support.form', [
            'type' => $type,
            'id' => $id,
            'token' => self::generatePublicToken($type, $id),
        ]);
    }

    public function publicForm(string $type, int $id, string $token): View
    {
        abort_unless(in_array($type, ['beneficiary', 'member'], true), 404);
        abort_unless(hash_equals(self::generatePublicToken($type, $id), $token), 403, 'رابط الاستمارة غير صالح.');

        if ($type === 'member') {
            $member = BeneficiaryFamilyMember::with('beneficiary')->findOrFail($id);
            $beneficiary = $member->beneficiary;
            $patientName = $member->full_name;
            $patientCode = $member->code;
            $medicalCondition = $member->case_details ?: ($beneficiary?->tumor_location ?? '');
        } else {
            $beneficiary = Beneficiary::with('familyMembers')->findOrFail($id);
            $member = null;
            $patientName = $beneficiary->patient_name ?: $beneficiary->full_name;
            $patientCode = $beneficiary->code;
            $medicalCondition = $beneficiary->tumor_location ?? '';
        }

        return view('psychological_support.public_form', compact(
            'type',
            'id',
            'token',
            'beneficiary',
            'member',
            'patientName',
            'patientCode',
            'medicalCondition'
        ));
    }

    public function publicSubmit(Request $request, string $type, int $id, string $token): RedirectResponse
    {
        abort_unless(in_array($type, ['beneficiary', 'member'], true), 404);
        abort_unless(hash_equals(self::generatePublicToken($type, $id), $token), 403, 'رابط الاستمارة غير صالح.');

        $validated = $request->validate([
            'report_month' => 'required|string|regex:/^\d{4}-\d{2}$/',
            'volunteer_name' => 'required|string|max:191',
            'medical_condition' => 'nullable|string|max:255',
            'treatment_stage' => 'nullable|string|max:255',
            'call_condition' => 'nullable|string',
            'psychological_state' => 'required|string|in:ممتاز,كويس,راضي,حزين,يشعر بالضجر',
            'rating' => 'required|integer|min:1|max:5',
            'needs_improvement' => 'nullable|string',
            'nutrition_diet' => 'nullable|string',
            'general_notes' => 'nullable|string',
            'supporter_opinion' => 'nullable|string',
            'family_member_id' => 'nullable|exists:beneficiary_family_members,id',
        ], [
            'volunteer_name.required' => 'يرجى كتابة اسم المتطوع ثلاثي.',
            'psychological_state.required' => 'يرجى تحديد الحالة النفسية للمريض.',
            'rating.required' => 'يرجى تحديد تقييم الحالة النفسية من 1 إلى 5.',
        ]);

        if ($type === 'member') {
            $member = BeneficiaryFamilyMember::findOrFail($id);
            $beneficiaryId = $member->beneficiary_id;
            $familyMemberId = $member->id;
        } else {
            $beneficiary = Beneficiary::findOrFail($id);
            $beneficiaryId = $beneficiary->id;
            $familyMemberId = !empty($validated['family_member_id']) ? (int) $validated['family_member_id'] : null;
        }

        $matchCriteria = [
            'beneficiary_id' => $beneficiaryId,
            'report_month' => $validated['report_month'],
        ];
        if ($familyMemberId) {
            $matchCriteria['family_member_id'] = $familyMemberId;
        }

        PsychologicalSupportReport::updateOrCreate(
            $matchCriteria,
            array_merge($validated, [
                'beneficiary_id' => $beneficiaryId,
                'family_member_id' => $familyMemberId,
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ])
        );

        return back()->with('submitted_success', 'تم تسجيل وتوثيق استمارة الدعم النفسي في النظام بنجاح. جزاكم الله خيراً!');
    }

    public function showModal(Beneficiary $beneficiary): \Illuminate\Http\JsonResponse
    {
        $reports = $beneficiary->psychologicalSupportReports()->with('creator')->get();
        return response()->json([
            'beneficiary' => [
                'id' => $beneficiary->id,
                'code' => $beneficiary->code,
                'name' => $beneficiary->full_name,
                'phone' => $beneficiary->phone,
                'tumor_location' => $beneficiary->tumor_location,
                'hospital_name' => $beneficiary->hospital_name,
            ],
            'reports' => $reports,
        ]);
    }
}
