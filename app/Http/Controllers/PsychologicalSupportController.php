<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\PsychologicalSupportReport;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class PsychologicalSupportController extends Controller
{
    public function index(Request $request): View
    {
        $currentMonth = $request->input('month', now()->format('Y-m'));

        $query = PsychologicalSupportReport::with(['beneficiary', 'creator'])
            ->where('report_month', $currentMonth)
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->whereHas('beneficiary', function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            })->orWhere('volunteer_name', 'like', "%{$search}%");
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
        ], [
            'volunteer_name.required' => 'يرجى كتابة اسم المتطوع ثلاثي.',
            'psychological_state.required' => 'يرجى تحديد الحالة النفسية للمريض.',
            'rating.required' => 'يرجى تحديد تقييم الحالة النفسية من 1 إلى 5.',
        ]);

        $reportMonth = $validated['report_month'];

        $report = PsychologicalSupportReport::updateOrCreate(
            [
                'beneficiary_id' => $beneficiary->id,
                'report_month' => $reportMonth,
            ],
            array_merge($validated, [
                'user_id' => auth()->id(),
                'created_by' => auth()->id(),
            ])
        );

        return back()->with('success', "تم توثيق تقرير الدعم النفسي لشهر [{$reportMonth}] للحالة [{$beneficiary->full_name}] بنجاح.");
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
