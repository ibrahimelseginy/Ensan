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
            'awaiting_discussion' => FieldVisit::where('status', 'awaiting_discussion')->count(),
            'accepted' => FieldVisit::where('status', 'accepted')->count(),
            'upcoming' => FieldVisit::where('status', 'scheduled')->whereDate('visit_date', '>=', today())->count(),
            'completed_month' => FieldVisit::whereIn('status', ['completed', 'accepted'])->whereMonth('visit_date', today()->month)->count(),
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
            if ($caseApplication && $caseApplication->beneficiary_id && !$selectedBeneficiary) {
                $selectedBeneficiary = Beneficiary::with('familyMembers')->find($caseApplication->beneficiary_id);
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

        // House photo upload
        if ($request->hasFile('house_photo')) {
            $file = $request->file('house_photo');
            if ($file->isValid()) {
                $upload = app(\App\Services\ImageUploadService::class)->upload($file, 'visits/house');
                $validated['house_photo_path'] = $upload['path'];
            }
        }

        // Condition photos upload (checks, house, deterioration proofs)
        if ($request->hasFile('condition_photos')) {
            $conditionPaths = [];
            foreach ($request->file('condition_photos') as $cFile) {
                if ($cFile && $cFile->isValid()) {
                    $upload = app(\App\Services\ImageUploadService::class)->upload($cFile, 'visits/conditions');
                    $conditionPaths[] = $upload['path'];
                }
            }
            if (!empty($conditionPaths)) {
                $validated['condition_photos'] = $conditionPaths;
            }
        }

        $validated['status'] = 'awaiting_discussion';
        $appId = $request->input('case_application_id') ?: $request->input('application_id');
        if (!empty($appId)) {
            $validated['case_application_id'] = $appId;
        }

        $visit = FieldVisit::create($validated);

        // Record attachments
        if (!empty($validated['house_photo_path'])) {
            $visit->attachments()->create([
                'path' => $validated['house_photo_path'],
                'mime' => 'image/jpeg',
                'original_name' => 'صورة_المنزل',
            ]);
        }
        if (!empty($validated['condition_photos'])) {
            foreach ($validated['condition_photos'] as $idx => $cPath) {
                $visit->attachments()->create([
                    'path' => $cPath,
                    'mime' => 'image/jpeg',
                    'original_name' => 'مستند_حالة_' . ($idx + 1),
                ]);
            }
        }

        if (!empty($validated['beneficiary_id'])) {
            Beneficiary::where('id', $validated['beneficiary_id'])->update(['status' => 'under_review']);
        }

        // Link with Case Application if provided
        if (!empty($validated['case_application_id'])) {
            $caseApp = \App\Models\MobileCaseApplication::find($validated['case_application_id']);
            if ($caseApp) {
                $caseApp->update([
                    'status' => 'under_research',
                    'field_visit_id' => $visit->id,
                    'beneficiary_id' => $visit->beneficiary_id,
                ]);
            }
        }

        return redirect()->route('visits.show', $visit)->with('success', 'تم حفظ استمارة البحث الميداني بنجاح، والحالة الآن [في انتظار المناقشة] من لجنة الحالات والإدارة.');
    }

    public function show(FieldVisit $visit)
    {
        $visit->load(['beneficiary', 'researcher']);
        $projects = \App\Models\Project::orderBy('name')->get();
        $user = auth()->user();
        $authorizedForFinalApproval = $user && ($user->hasRole(['admin', 'super_admin', 'manager']) || (int)$user->id === 2 || str_contains(strtolower((string)$user->name), 'salma') || str_contains((string)$user->email, 'ghanemsalma'));
        return view('dashboard.visits.show', compact('visit', 'projects', 'authorizedForFinalApproval'));
    }

    public function finalApproval(Request $request, FieldVisit $visit)
    {
        $user = auth()->user();
        $isAuthorized = $user && (
            $user->hasRole(['admin', 'super_admin', 'manager', 'social_research_manager']) ||
            $user->hasPermission('visits.edit') ||
            $user->hasPermission('beneficiaries.create') ||
            (int)$user->id === 2 ||
            str_contains(strtolower((string)$user->name), 'salma') ||
            str_contains((string)$user->email, 'ghanemsalma')
        );
        abort_unless($isAuthorized, 403, 'غير مصرح لك بالاعتماد النهائي للحالات بعد البحث الميداني.');

        $decision = $request->input('decision');
        if ($decision === 'rejected') {
            $request->validate([
                'rejection_reason' => 'required|string|max:1000',
            ], [
                'rejection_reason.required' => 'يرجى كتابة سبب الرفض إجبارياً.',
            ]);

            $reason = (string) $request->input('rejection_reason');
            $visit->update([
                'status' => 'rejected',
                'recommendation' => 'rejected',
                'recommendation_notes' => ($visit->recommendation_notes ? $visit->recommendation_notes . "\n" : '') . "تم الرفض النهائي من الإدارة: {$reason}",
            ]);

            if ($visit->beneficiary) {
                $visit->beneficiary->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                ]);
            }

            $caseApp = \App\Models\MobileCaseApplication::where('field_visit_id', $visit->id)
                ->orWhere('id', $visit->case_application_id)
                ->orWhere(function ($q) use ($visit) {
                    if (!empty($visit->national_id)) {
                        $q->where('applicant_id_number', $visit->national_id);
                    }
                })
                ->first();
            if ($caseApp) {
                $caseApp->update([
                    'status' => 'rejected',
                    'rejection_reason' => $reason,
                ]);
            }

            return back()->with('success', 'تم تسجيل قرار الرفض النهائي للحالة بنجاح.');
        }

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'monthly_sponsorship_amount' => 'nullable|numeric|min:0',
            'approval_notes' => 'nullable|string',
            'in_kind_notes' => 'nullable|string',
            'fixed_medicine_name' => 'nullable|string|max:255',
            'fixed_medicine_duration_months' => 'nullable|integer|min:1',
            'fixed_medicine_end_date' => 'nullable|date',
        ]);

        $project = \App\Models\Project::findOrFail($request->project_id);
        $medicineDetails = '';
        if ($request->filled('fixed_medicine_name')) {
            $endDate = $request->filled('fixed_medicine_end_date')
                ? $request->input('fixed_medicine_end_date')
                : ($request->filled('fixed_medicine_duration_months') ? now()->addMonths((int)$request->input('fixed_medicine_duration_months'))->toDateString() : null);

            $medicineDetails = "\n[صرف علاج ثابت]: {$request->input('fixed_medicine_name')}"
                . ($request->filled('fixed_medicine_duration_months') ? " | المدة: {$request->input('fixed_medicine_duration_months')} شهور" : "")
                . ($endDate ? " | تاريخ الانتهاء: {$endDate} (تنبيه تجديد قبل 15 يوماً)" : "");
        }

        $inKindDetails = $request->filled('in_kind_notes') ? "\n[مساعدة عينية]: {$request->input('in_kind_notes')}" : '';
        $approvalNotes = $request->filled('approval_notes') ? "\n[ملاحظات الاعتماد]: {$request->input('approval_notes')}" : '';

        $fullNotes = trim(($visit->notes ?? '') . $medicineDetails . $inKindDetails . $approvalNotes);

        $beneficiary = $visit->beneficiary;
        if (!$beneficiary && !empty($visit->national_id)) {
            $beneficiary = Beneficiary::where('national_id', $visit->national_id)->first();
        }
        if (!$beneficiary && !empty($visit->phone)) {
            $beneficiary = Beneficiary::where('phone', $visit->phone)->first();
        }

        $caseApp = \App\Models\MobileCaseApplication::where('field_visit_id', $visit->id)
            ->orWhere('id', $visit->case_application_id)
            ->orWhere(function ($q) use ($visit) {
                if (!empty($visit->national_id)) {
                    $q->where('applicant_id_number', $visit->national_id);
                }
            })
            ->first();

        $patientName = $visit->patient_name ?: ($caseApp?->applicant_name ?: null);
        $guardianName = $visit->guardian_name ?: ($caseApp?->applicant_name ?: $patientName);
        $fullName = $guardianName ?: ($patientName ?: 'مستفيد جديد');

        if (!$beneficiary) {
            $newCode = Beneficiary::generateNextCode();
            $beneficiary = Beneficiary::create([
                'code' => $newCode,
                'full_name' => $fullName,
                'patient_name' => $patientName,
                'guardian_name' => $guardianName,
                'national_id' => $visit->national_id ?: $caseApp?->applicant_id_number,
                'phone' => $visit->phone ?: $caseApp?->applicant_phone,
                'backup_phone' => $caseApp?->backup_phone,
                'address' => $visit->address ?: $caseApp?->address,
                'project_id' => $project->id,
                'status' => 'accepted',
                'monthly_sponsorship_amount' => $request->input('monthly_sponsorship_amount') ?: null,
                'notes_cases' => $fullNotes,
                'created_by' => $user->id,
                'mother_name' => $caseApp?->mother_name,
                'deceased_husband_name' => $caseApp?->deceased_husband_name,
                'transit_details' => $caseApp?->transit_details,
                'death_details' => $caseApp?->death_details,
                'income_aid_sources' => $caseApp?->income_aid_sources,
                'expenses_debts_details' => $caseApp?->expenses_debts_details,
                'hospital_name' => $visit->hospital_name ?: $caseApp?->hospital_name,
                'tumor_location' => $visit->tumor_location ?: $caseApp?->tumor_location,
                'treatment_protocol' => $visit->treatment_protocol ?: $caseApp?->treatment_protocol,
                'family_members_data' => $visit->family_members,
                'assistance_type' => $request->filled('monthly_sponsorship_amount') ? 'كفالة نقدية شهرية' : ($request->filled('in_kind_notes') ? 'مساعدة عينية' : 'رعاية عامة'),
                'collection_method' => 'cash',
                'collection_day' => 1,
                'classification' => 'A',
            ]);
            $visit->update(['beneficiary_id' => $beneficiary->id]);
        } else {
            if (empty($beneficiary->code)) {
                $beneficiary->code = Beneficiary::generateNextCode();
            }
            $beneficiary->update([
                'code' => $beneficiary->code,
                'project_id' => $project->id,
                'status' => 'accepted',
                'monthly_sponsorship_amount' => $request->input('monthly_sponsorship_amount') ?: $beneficiary->monthly_sponsorship_amount,
                'notes_cases' => trim(($beneficiary->notes_cases ?? '') . "\n" . $medicineDetails . $inKindDetails . $approvalNotes),
                'family_members_data' => $visit->family_members ?: $beneficiary->family_members_data,
            ]);
        }

        // Copy family members to beneficiary_family_members
        if (!empty($visit->family_members) && is_array($visit->family_members)) {
            foreach ($visit->family_members as $idx => $fm) {
                $mName = $fm['name'] ?? null;
                if ($mName) {
                    $exists = $beneficiary->familyMembers()->where('full_name', $mName)->exists();
                    if (!$exists) {
                        $isChild = in_array(strtolower((string)($fm['relation'] ?? '')), ['child', 'son', 'daughter', 'orphan', 'ابن', 'ابنة', 'طفل', 'يتيم'], true);
                        $isPat = $visit->has_cancer_patient && ($mName === $visit->patient_name || in_array(strtolower((string)($fm['relation'] ?? '')), ['patient', 'مريض', 'مريضة'], true));
                        $beneficiary->familyMembers()->create([
                            'code' => $beneficiary->code . '-' . ($idx + 1),
                            'full_name' => $mName,
                            'relationship' => $fm['relation'] ?? ($fm['relationship'] ?? ($isChild ? 'child' : ($isPat ? 'patient' : 'family_member'))),
                            'age' => isset($fm['age']) ? (int)$fm['age'] : null,
                            'education_level' => $fm['education'] ?? null,
                            'social_status' => $fm['social_status'] ?? null,
                            'job' => $fm['job'] ?? null,
                            'health_status' => $fm['health'] ?? null,
                            'is_patient' => $isPat,
                            'active' => true,
                        ]);
                    }
                }
            }
        }

        // If cancer patient exists and not in family_members, add them
        if ($visit->has_cancer_patient && !empty($visit->patient_name)) {
            $patientExists = $beneficiary->familyMembers()->where('full_name', $visit->patient_name)->exists();
            if (!$patientExists) {
                $beneficiary->familyMembers()->create([
                    'code' => $beneficiary->code . '-P1',
                    'full_name' => $visit->patient_name,
                    'relationship' => 'patient',
                    'is_patient' => true,
                    'active' => true,
                    'case_details' => ($visit->tumor_location ? "ورم: {$visit->tumor_location}" : '') . ($visit->hospital_name ? " | مستشفى: {$visit->hospital_name}" : ''),
                ]);
            }
        }

        $visit->update([
            'beneficiary_id' => $beneficiary->id,
            'recommendation' => 'approved',
            'status' => 'accepted',
            'recommendation_notes' => "تم قبول واعتماد البحث بمشروع ({$project->name}) وتحويل الحالة لمستفيد نشط برمز [{$beneficiary->code}] بواسطة " . ($user->name ?? 'الإدارة'),
        ]);

        if ($caseApp) {
            $caseApp->update([
                'status' => 'approved',
                'beneficiary_id' => $beneficiary->id,
            ]);
        }

        if ($project->manager_id) {
            \App\Models\Notification::create([
                'user_id' => $project->manager_id,
                'title' => 'اعتماد حالة جديدة في المشروع',
                'message' => "تم قبول واعتماد الحالة: {$beneficiary->full_name} في مشروع ({$project->name}) وتحويلها لمستفيد بالكود [{$beneficiary->code}].",
                'type' => 'project_case_approved',
                'data' => ['beneficiary_id' => $beneficiary->id, 'project_id' => $project->id, 'visit_id' => $visit->id],
                'is_read' => false,
            ]);
        }

        return back()->with('success', "تم قبول واعتماد البحث بنجاح، وتحويل الحالة آلياً إلى مستفيد نشط بالكود [{$beneficiary->code}] في مشروع ({$project->name}).");
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

        // House photo upload
        if ($request->hasFile('house_photo')) {
            $file = $request->file('house_photo');
            if ($file->isValid()) {
                $upload = app(\App\Services\ImageUploadService::class)->upload($file, 'visits/house');
                $validated['house_photo_path'] = $upload['path'];
                $visit->attachments()->create([
                    'path' => $upload['path'],
                    'mime' => 'image/jpeg',
                    'original_name' => 'صورة_المنزل',
                ]);
            }
        }

        // Condition photos upload
        if ($request->hasFile('condition_photos')) {
            $existing = is_array($visit->condition_photos) ? $visit->condition_photos : [];
            foreach ($request->file('condition_photos') as $cFile) {
                if ($cFile && $cFile->isValid()) {
                    $upload = app(\App\Services\ImageUploadService::class)->upload($cFile, 'visits/conditions');
                    $existing[] = $upload['path'];
                    $visit->attachments()->create([
                        'path' => $upload['path'],
                        'mime' => 'image/jpeg',
                        'original_name' => 'مستند_حالة_' . count($existing),
                    ]);
                }
            }
            $validated['condition_photos'] = $existing;
        }

        if ($request->filled('application_id') && empty($visit->case_application_id)) {
            $validated['case_application_id'] = $request->input('application_id');
        }
        if (empty($visit->status) || in_array($visit->status, ['completed', 'under_research'], true)) {
            $validated['status'] = 'awaiting_discussion';
        }

        $visit->update($validated);

        return redirect()->route('visits.show', $visit)->with('success', 'تم تحديث استمارة البحث بنجاح، والحالة (في انتظار المناقشة).');
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
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'case_application_id' => 'nullable|exists:mobile_case_applications,id',
            'application_id' => 'nullable|exists:mobile_case_applications,id',
            'researcher_id' => 'required|exists:users,id',
            'visit_date' => 'required|date',
            'status' => 'nullable|string|in:scheduled,completed,cancelled,awaiting_discussion,under_research,accepted,rejected',
            'recommendation' => 'nullable|in:approve,reject,needs_review',
            'report' => 'nullable|string',

            // General & Case Info
            'patient_name' => 'nullable|string|max:255',
            'guardian_name' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:20',
            'phone' => 'nullable|string|max:25',
            'researcher_phone' => 'nullable|string|max:25',
            'home_ownership' => 'nullable|string|max:50',
            'house_photo' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:15360',
            'condition_photos' => 'nullable|array',
            'condition_photos.*' => 'nullable|file|mimes:jpeg,jpg,png,webp,pdf|max:15360',
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
