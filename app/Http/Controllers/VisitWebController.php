<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FieldVisit;
use App\Models\Beneficiary;
use App\Models\User;

final class VisitWebController extends Controller
{
    public function index(Request $request)
    {
        $query = FieldVisit::with(['beneficiary', 'researcher'])->latest('visit_date');

        if ($request->filled('type')) {
            if ($request->type === 'cancer') {
                $query->where('has_cancer_patient', true);
            } elseif ($request->type === 'normal') {
                $query->where('has_cancer_patient', false);
            }
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('guardian_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhereHas('beneficiary', function ($bq) use ($search) {
                      $bq->where('full_name', 'like', "%{$search}%")
                         ->orWhere('code', 'like', "%{$search}%");
                  });
            });
        }

        $visits = $query->paginate(12)->withQueryString();

        $stats = [
            'total' => FieldVisit::count(),
            'upcoming' => FieldVisit::where('status', 'scheduled')->whereDate('visit_date', '>=', today())->count(),
            'completed_month' => FieldVisit::where('status', 'completed')->whereMonth('visit_date', today()->month)->count(),
            'needs_review' => FieldVisit::where('recommendation', 'needs_review')->count(),
            'cancer_cases' => FieldVisit::where('has_cancer_patient', true)->count(),
            'normal_cases' => FieldVisit::where('has_cancer_patient', false)->count(),
        ];

        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $researchers = User::where('is_employee', true)->orWhere('role', 'volunteer')->orderBy('name')->get();

        return view('dashboard.visits.index', compact('visits', 'stats', 'beneficiaries', 'researchers'));
    }

    public function create(Request $request)
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $researchers = User::where('is_employee', true)->orWhere('role', 'volunteer')->orderBy('name')->get();

        $selectedBeneficiary = null;
        if ($request->filled('beneficiary_id')) {
            $selectedBeneficiary = Beneficiary::with('familyMembers')->find($request->beneficiary_id);
        }

        $caseApplication = null;
        if ($request->filled('application_id')) {
            $caseApplication = \App\Models\MobileCaseApplication::find($request->application_id);
            if ($caseApplication && !$selectedBeneficiary) {
                if ($caseApplication->beneficiary_id) {
                    $selectedBeneficiary = Beneficiary::with('familyMembers')->find($caseApplication->beneficiary_id);
                } else {
                    // Create or find matching beneficiary automatically to link cleanly
                    $matchedBen = Beneficiary::where('national_id', $caseApplication->applicant_id_number)
                        ->orWhere('phone', $caseApplication->applicant_phone)
                        ->first();

                    if (!$matchedBen) {
                        $matchedBen = Beneficiary::create([
                            'full_name' => $caseApplication->applicant_name,
                            'national_id' => $caseApplication->applicant_id_number,
                            'phone' => $caseApplication->applicant_phone,
                            'backup_phone' => $caseApplication->backup_phone,
                            'address' => trim(($caseApplication->governorate ? $caseApplication->governorate . ' - ' : '') . ($caseApplication->city ? $caseApplication->city . ' - ' : '') . $caseApplication->address),
                            'status' => 'under_review',
                        ]);
                    }
                    $selectedBeneficiary = $matchedBen;
                    $caseApplication->update(['beneficiary_id' => $matchedBen->id]);
                    $beneficiaries = Beneficiary::orderBy('full_name')->get();
                }
            }
        }

        return view('dashboard.visits.create', compact('beneficiaries', 'researchers', 'selectedBeneficiary', 'caseApplication'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateVisitData($request);

        // Calculate Totals
        $validated['total_income'] = (float)($request->input('income_job', 0))
            + (float)($request->input('income_daily_work', 0))
            + (float)($request->input('income_pension', 0))
            + (float)($request->input('income_social_affairs', 0))
            + (float)($request->input('income_charities', 0));

        $validated['total_expenses'] = (float)($request->input('expense_rent', 0))
            + (float)($request->input('expense_tuition', 0))
            + (float)($request->input('expense_food_weekly', 0))
            + (float)($request->input('expense_transportation', 0))
            + (float)($request->input('expense_utilities', 0));

        $totalDebts = (float)($request->input('total_debts', 0));
        $debtRepaid = (float)($request->input('debt_repaid', 0));
        $validated['debt_remaining'] = max(0, $totalDebts - $debtRepaid);

        // Process family members
        $validated['family_members'] = $this->formatFamilyMembers($request->input('family_members', []));

        $validated['has_cancer_patient'] = $request->boolean('has_cancer_patient');

        $visit = FieldVisit::create($validated);

        if (!empty($validated['beneficiary_id'])) {
            Beneficiary::where('id', $validated['beneficiary_id'])->update(['status' => 'under_review']);
        }

        // Link with Case Application if provided
        if ($request->filled('application_id')) {
            $caseApp = \App\Models\MobileCaseApplication::find($request->application_id);
            if ($caseApp) {
                $caseApp->update([
                    'status' => 'completed',
                    'field_visit_id' => $visit->id,
                    'beneficiary_id' => $visit->beneficiary_id,
                ]);
            }
        }

        return redirect()->route('visits.show', $visit)->with('success', 'تم حفظ استمارة البحث الميداني بنجاح وتحويل حالة البحث إلى (تم البحث).');
    }

    public function show(FieldVisit $visit)
    {
        $visit->load(['beneficiary', 'researcher']);
        return view('dashboard.visits.show', compact('visit'));
    }

    public function edit(FieldVisit $visit)
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $researchers = User::where('is_employee', true)->orWhere('role', 'volunteer')->orderBy('name')->get();

        return view('dashboard.visits.edit', compact('visit', 'beneficiaries', 'researchers'));
    }

    public function update(Request $request, FieldVisit $visit)
    {
        $validated = $this->validateVisitData($request, $visit->id);

        $validated['total_income'] = (float)($request->input('income_job', 0))
            + (float)($request->input('income_daily_work', 0))
            + (float)($request->input('income_pension', 0))
            + (float)($request->input('income_social_affairs', 0))
            + (float)($request->input('income_charities', 0));

        $validated['total_expenses'] = (float)($request->input('expense_rent', 0))
            + (float)($request->input('expense_tuition', 0))
            + (float)($request->input('expense_food_weekly', 0))
            + (float)($request->input('expense_transportation', 0))
            + (float)($request->input('expense_utilities', 0));

        $totalDebts = (float)($request->input('total_debts', 0));
        $debtRepaid = (float)($request->input('debt_repaid', 0));
        $validated['debt_remaining'] = max(0, $totalDebts - $debtRepaid);

        $validated['family_members'] = $this->formatFamilyMembers($request->input('family_members', []));
        $validated['has_cancer_patient'] = $request->boolean('has_cancer_patient');

        $visit->update($validated);

        return redirect()->route('visits.show', $visit)->with('success', 'تم تحديث استمارة البحث بنجاح.');
    }

    public function print(FieldVisit $visit)
    {
        $visit->load(['beneficiary', 'researcher']);
        return view('dashboard.visits.print', compact('visit'));
    }

    public function destroy(FieldVisit $visit)
    {
        $visit->delete();
        return redirect()->route('visits.index')->with('success', 'تم حذف استمارة البحث بنجاح.');
    }

    private function validateVisitData(Request $request, ?int $visitId = null): array
    {
        return $request->validate([
            'beneficiary_id' => 'required|exists:beneficiaries,id',
            'researcher_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'status' => 'required|in:scheduled,completed,cancelled',
            'recommendation' => 'nullable|in:approve,reject,needs_review',
            'report' => 'nullable|string',

            // General & Case Info
            'patient_name' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:25',
            'researcher_phone' => 'nullable|string|max:25',
            'home_ownership' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'researcher_opinion' => 'nullable|string',

            // Income & Expenses
            'income_job' => 'nullable|numeric|min:0',
            'income_daily_work' => 'nullable|numeric|min:0',
            'income_pension' => 'nullable|numeric|min:0',
            'income_social_affairs' => 'nullable|numeric|min:0',
            'income_charities' => 'nullable|numeric|min:0',
            'expense_rent' => 'nullable|numeric|min:0',
            'expense_tuition' => 'nullable|numeric|min:0',
            'expense_food_weekly' => 'nullable|numeric|min:0',
            'expense_transportation' => 'nullable|numeric|min:0',
            'expense_utilities' => 'nullable|numeric|min:0',

            // Debts
            'total_debts' => 'nullable|numeric|min:0',
            'debt_repaid' => 'nullable|numeric|min:0',
            'debt_monthly_installment' => 'nullable|numeric|min:0',
            'debt_reason' => 'nullable|string',

            // Needs & Condition
            'case_guide' => 'nullable|string',
            'case_needs' => 'nullable|string',
            'home_and_social_condition' => 'nullable|string',

            // Conditional Flag
            'has_cancer_patient' => 'nullable|boolean',

            // Oncology Patient Info
            'hospital_name' => 'nullable|string|max:255',
            'tumor_location' => 'nullable|string|max:255',
            'disease_discovery_date' => 'nullable|string|max:100',
            'disease_duration' => 'nullable|string|max:100',
            'chemo_sessions_taken' => 'nullable|integer|min:0',
            'chemo_sessions_remaining' => 'nullable|integer|min:0',
            'radiation_sessions_taken' => 'nullable|integer|min:0',
            'radiation_sessions_remaining' => 'nullable|integer|min:0',
            'hospital_visit_frequency' => 'nullable|string|max:150',
            'patient_psychological_state' => 'nullable|string|max:255',
            'before_tumor_notes' => 'nullable|string',
            'after_tumor_notes' => 'nullable|string',
            'treatment_protocol' => 'nullable|string',
            'foundation_manager' => 'nullable|string|max:150',
            'project_manager' => 'nullable|string|max:150',
        ]);
    }

    private function formatFamilyMembers(mixed $rawMembers): array
    {
        if (!is_array($rawMembers)) {
            return [];
        }

        $formatted = [];
        foreach ($rawMembers as $item) {
            if (!empty($item['name']) || !empty($item['relation'])) {
                $formatted[] = [
                    'name' => trim((string)($item['name'] ?? '')),
                    'relation' => trim((string)($item['relation'] ?? '')),
                    'age' => trim((string)($item['age'] ?? '')),
                    'income' => trim((string)($item['income'] ?? '')),
                    'job' => trim((string)($item['job'] ?? '')),
                    'notes' => trim((string)($item['notes'] ?? '')),
                ];
            }
        }
        return $formatted;
    }
}
