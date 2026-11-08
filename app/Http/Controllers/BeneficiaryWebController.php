<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\ChangeRequest;
use App\Models\GuestHouseStay;
use App\Models\GuestHouseMealServing;
use App\Models\RamadanBag;
use App\Models\Donor;
use App\Models\Donation;
use App\Models\Warehouse;
use Carbon\Carbon;
use App\Services\BeneficiaryService;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\UpdateBeneficiaryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Symfony\Component\HttpFoundation\Response;
use Dompdf\Dompdf;
use Dompdf\Options;

final class BeneficiaryWebController extends Controller
{
    public function __construct(
        private BeneficiaryService $beneficiaryService
    ) {}

    public function index(Request $request): View
    {
        $perPage = (int) $request->get('per_page', 20);
        if (!in_array($perPage, [20, 50, 100], true)) {
            $perPage = 20;
        }

        $filters = $request->only([
            'q', 'code_year', 'status', 'assistance_type', 'project_id', 'campaign_id',
            'guest_house_id', 'sort', 'dir', 'date_from', 'date_to',
            'has_phone', 'has_attachments', 'address_like'
        ]);
        
        $filters['per_page'] = $perPage;

        $beneficiaries = $this->beneficiaryService->getFilteredBeneficiaries($filters, $perPage);
        $stats         = $this->beneficiaryService->getDashboardStats();

        $projects = Project::query()
            ->where(fn($q) => $q->whereNull('status')->orWhere('status', '!=', 'archived'))
            ->orderBy('name')->get();

        $campaigns = Campaign::orderByDesc('season_year')->orderBy('name')->get();

        return view('beneficiaries.index', array_merge(
            compact('beneficiaries', 'projects', 'campaigns'),
            $filters,
            $stats
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        return $this->beneficiaryService->exportToCsv($request->all());
    }

    public function exportPdf(Request $request): Response
    {
        $beneficiaries = $this->beneficiaryService
            ->getFilteredBeneficiaries($request->all(), 10000)
            ->getCollection();

        return $this->pdfResponse(
            view('beneficiaries.pdf-list', compact('beneficiaries'))->render(),
            'beneficiaries-' . now()->format('Y-m-d') . '.pdf',
            'landscape'
        );
    }

    public function pdf(Beneficiary $beneficiary): Response
    {
        $beneficiary->load(['project', 'familyMembers.sponsors']);

        return $this->pdfResponse(
            view('beneficiaries.pdf', compact('beneficiary'))->render(),
            'beneficiary-' . ($beneficiary->code ?: $beneficiary->id) . '.pdf'
        );
    }

    public function create(Request $request): View
    {
        $changeRequest = null;
        $prefillData = [];
        if ($request->filled('change_request_id')) {
            $changeRequest = \App\Models\ChangeRequest::find($request->change_request_id);
            if ($changeRequest && $changeRequest->model_type === Beneficiary::class) {
                $payload = $changeRequest->payload;
                $prefillData = (is_array($payload) && isset($payload['__is_wrapped'])) ? ($payload['data'] ?? []) : (array)$payload;
                if (!empty($prefillData) && empty(old())) {
                    session()->flashInput($prefillData);
                }
            }
        }

        $projects = Project::query()
            ->where(fn($q) => $q->whereNull('status')->orWhere('status', '!=', 'archived'))
            ->orderBy('name')->get();

        return view('beneficiaries.create', compact(
            'projects',
            'changeRequest',
            'prefillData'
        ));
    }

    public function store(StoreBeneficiaryRequest $request): RedirectResponse
    {
        $user = $request->user();

        // If editing/resubmitting an existing change request
        if ($request->filled('change_request_id')) {
            $changeRequest = \App\Models\ChangeRequest::findOrFail($request->change_request_id);
            if ($changeRequest->model_type === Beneficiary::class) {
                $canEdit = $user && ($user->hasRole(['admin', 'super_admin']) || $user->hasPermission('manage_change_requests') || (int)$changeRequest->user_id === (int)$user->id);
                if (!$canEdit) {
                    abort(403, 'غير مصرح بتعديل هذا الطلب.');
                }

                $validated = $request->validated();
                $payload = $changeRequest->payload;
                if (is_array($payload) && isset($payload['__is_wrapped'])) {
                    $payload['data'] = $validated;
                    $changeRequest->payload = $payload;
                } else {
                    $changeRequest->payload = $validated;
                }

                $changeRequest->status = 'pending';
                $changeRequest->rejection_reason = null;
                $changeRequest->reviewer_id = null;
                $changeRequest->save();

                if ($user->hasRole(['admin', 'super_admin']) && $request->boolean('approve_now')) {
                    return app(ChangeRequestWebController::class)->approve($changeRequest);
                }

                return redirect()->route('change-requests.index')->with('success', 'تم تعديل بيانات المستفيد وإعادة إرسال الطلب للموافقة بنجاح.');
            }
        }

        $result = $this->beneficiaryService->createBeneficiary($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.show', $result);
    }

    public function show(Beneficiary $beneficiary): View|RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $beneficiary->load([
            'allocatedBeneficiaries',
            'sponsors',
            'familyMembers.sponsors',
            'creator',
            'updater',
            'attachments',
            'patientProfile.guestHouse',
            'guestHouseStays.guestHouse',
            'guestHouseStays.bed.wing',
        ]);

        $duplicates = $this->beneficiaryService->checkDuplicates($beneficiary);
        $isDup      = !empty($duplicates);
        $donations  = $this->beneficiaryService->getBeneficiaryDonations((int)$beneficiary->id);

        // Fetch activity / movement log (audits and change requests)
        $audits = \App\Models\Audit::query()
            ->where(function($q) use ($beneficiary) {
                $q->where('entity_type', \App\Models\Beneficiary::class)->where('entity_id', $beneficiary->id)
                  ->orWhere('path', 'like', "%beneficiaries/{$beneficiary->id}%");
            })
            ->with('user')
            ->latest('created_at')
            ->limit(50)
            ->get();

        $changeRequests = \App\Models\ChangeRequest::query()
            ->where('model_type', \App\Models\Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->with(['user', 'reviewer'])
            ->latest('created_at')
            ->get();

        $beneficiary->setRelation('changeRequests', $changeRequests);

        // Fetch any linked case applications (for ID card photo & medical report)
        $linkedCaseApplications = \App\Models\MobileCaseApplication::query()
            ->where('beneficiary_id', $beneficiary->id)
            ->orWhere(function($q) use ($beneficiary) {
                if (!empty($beneficiary->national_id)) {
                    $q->where('applicant_id_number', $beneficiary->national_id);
                }
                if (!empty($beneficiary->phone)) {
                    $q->orWhere('applicant_phone', $beneficiary->phone);
                }
            })
            ->latest()
            ->get();

        // Guest house patient profile & stays
        $patientProfile = $beneficiary->patientProfile;
        if (!$patientProfile && (!empty($beneficiary->national_id) || !empty($beneficiary->phone))) {
            $otherBenIds = Beneficiary::where(function($q) use ($beneficiary) {
                if ($beneficiary->national_id) $q->where('national_id', $beneficiary->national_id);
                if ($beneficiary->phone) $q->orWhere('phone', $beneficiary->phone);
            })->pluck('id');
            $patientProfile = \App\Models\GuestHousePatientProfile::whereIn('beneficiary_id', $otherBenIds)->first();
        }

        $isGuestHouseCase = !empty($beneficiary->guest_house_id)
            || str_starts_with((string) $beneficiary->code, 'G-')
            || $patientProfile !== null
            || $beneficiary->guestHouseStays->isNotEmpty();

        $guestHouseDocs = $patientProfile ? $patientProfile->getDocumentList() : [
            'patient_id_front' => [
                'key' => 'patient_id_front', 'column' => 'patient_id_front_path',
                'title' => 'بطاقة المريض (الوجه)', 'subtitle' => 'صورة بطاقة الرقم القومي للمريض - الوجه الأمامي',
                'icon' => 'bi-person-badge', 'badge' => 'بطاقة مريض', 'badge_class' => 'bg-primary-subtle text-primary',
                'path' => null, 'url' => null, 'is_uploaded' => false, 'is_image' => false,
            ],
            'patient_id_back' => [
                'key' => 'patient_id_back', 'column' => 'patient_id_back_path',
                'title' => 'بطاقة المريض (الظهر)', 'subtitle' => 'صورة بطاقة الرقم القومي للمريض - الوجه الخلفي',
                'icon' => 'bi-credit-card-2-front', 'badge' => 'بطاقة مريض', 'badge_class' => 'bg-primary-subtle text-primary',
                'path' => null, 'url' => null, 'is_uploaded' => false, 'is_image' => false,
            ],
            'companion_id_front' => [
                'key' => 'companion_id_front', 'column' => 'companion_id_front_path',
                'title' => 'بطاقة المرافق (الوجه)', 'subtitle' => 'صورة بطاقة الرقم القومي للمرافق - الوجه الأمامي',
                'icon' => 'bi-person-vcard', 'badge' => 'بطاقة مرافق', 'badge_class' => 'bg-secondary-subtle text-secondary',
                'path' => null, 'url' => null, 'is_uploaded' => false, 'is_image' => false,
            ],
            'companion_id_back' => [
                'key' => 'companion_id_back', 'column' => 'companion_id_back_path',
                'title' => 'بطاقة المرافق (الظهر)', 'subtitle' => 'صورة بطاقة الرقم القومي للمرافق - الوجه الخلفي',
                'icon' => 'bi-credit-card', 'badge' => 'بطاقة مرافق', 'badge_class' => 'bg-secondary-subtle text-secondary',
                'path' => null, 'url' => null, 'is_uploaded' => false, 'is_image' => false,
            ],
            'followup_card' => [
                'key' => 'followup_card', 'column' => 'followup_card_path',
                'title' => 'كارت المتابعة (جرعات الكيماوي)', 'subtitle' => 'كارت متابعة جرعات وجلسات العلاج الكيماوي للحالة',
                'icon' => 'bi-file-earmark-medical', 'badge' => 'كارت متابعة', 'badge_class' => 'bg-info-subtle text-info',
                'path' => null, 'url' => null, 'is_uploaded' => false, 'is_image' => false,
            ],
            'referral_letter' => [
                'key' => 'referral_letter', 'column' => 'referral_letter_path',
                'title' => 'خطاب التحويل / التقرير الطبي', 'subtitle' => 'خطاب التحويل الطبي بعدد الجلسات أو التقرير الطبي المعتمد',
                'icon' => 'bi-file-earmark-text', 'badge' => 'تقرير / تحويل', 'badge_class' => 'bg-warning-subtle text-warning',
                'path' => null, 'url' => null, 'is_uploaded' => false, 'is_image' => false,
            ],
        ];

        // Linked web room bookings (if any)
        $linkedBookings = \App\Models\WebRoomBooking::query()
            ->where(function($q) use ($beneficiary) {
                $q->where('beneficiary_id', $beneficiary->id);
                if (!empty($beneficiary->phone)) {
                    $q->orWhere('phone', $beneficiary->phone);
                }
            })
            ->latest()
            ->get();

        // ── 7. Comprehensive Aid & Services Ledger Data ──
        // Guest house stays & active resident status
        $guestHouseStays = $beneficiary->guestHouseStays()
            ->with(['guestHouse', 'bed.wing'])
            ->latest('arrival_date')
            ->latest('id')
            ->get();

        if ($guestHouseStays->isEmpty() && (!empty($beneficiary->national_id) || !empty($beneficiary->phone))) {
            $otherBenIds = Beneficiary::where('id', '!=', $beneficiary->id)
                ->where(function($q) use ($beneficiary) {
                    if ($beneficiary->national_id) $q->where('national_id', $beneficiary->national_id);
                    if ($beneficiary->phone) $q->orWhere('phone', $beneficiary->phone);
                })->pluck('id');
            if ($otherBenIds->isNotEmpty()) {
                $guestHouseStays = GuestHouseStay::whereIn('beneficiary_id', $otherBenIds)
                    ->with(['guestHouse', 'bed.wing'])
                    ->latest('arrival_date')
                    ->latest('id')
                    ->get();
            }
        }

        $activeStay = $guestHouseStays->firstWhere('status', 'resident')
            ?: $guestHouseStays->first(fn($s) => empty($s->departed_at));

        // Guest House Meal Servings
        $mealServings = GuestHouseMealServing::where('beneficiary_id', $beneficiary->id)
            ->with(['guestHouseMeal.guestHouse'])
            ->latest('received_at')
            ->limit(30)
            ->get();

        // Ramadan / Seasonal Food Packages
        $ramadanBags = (!empty($beneficiary->national_id) || !empty($beneficiary->phone))
            ? RamadanBag::where(function($q) use ($beneficiary) {
                if (!empty($beneficiary->national_id)) $q->where('national_id', $beneficiary->national_id);
                if (!empty($beneficiary->phone)) $q->orWhere('phone', $beneficiary->phone);
            })->latest()->get()
            : collect();

        // Beneficiary Sponsors (direct + sponsored_beneficiary_id + family members' sponsors)
        $directSponsoredBy = Donor::where('sponsored_beneficiary_id', $beneficiary->id)->get();
        $memberSponsors = Donor::whereHas('sponsoredFamilyMembers', function($q) use ($beneficiary) {
            $q->where('beneficiary_id', $beneficiary->id);
        })->get();
        $allBeneficiarySponsors = $beneficiary->sponsors->merge($directSponsoredBy)->merge($memberSponsors)->unique('id');

        // All Donors list for quick sponsorship linking
        $allDonors = Donor::orderBy('name')->get(['id', 'name', 'code', 'phone']);
        $allProjects = Project::orderBy('name')->get(['id', 'name']);

        $reassessments = \Illuminate\Support\Facades\Schema::hasTable('beneficiary_reassessments')
            ? $beneficiary->reassessments()->with(['familyMember', 'creator'])->get()
            : collect();
        $psychReports = \Illuminate\Support\Facades\Schema::hasTable('psychological_support_reports')
            ? $beneficiary->psychologicalSupportReports()->with(['familyMember', 'creator'])->get()
            : collect();

        return view('beneficiaries.show', compact(
            'beneficiary',
            'isDup',
            'donations',
            'audits',
            'changeRequests',
            'linkedCaseApplications',
            'patientProfile',
            'isGuestHouseCase',
            'guestHouseDocs',
            'linkedBookings',
            'guestHouseStays',
            'activeStay',
            'mealServings',
            'ramadanBags',
            'allBeneficiarySponsors',
            'allDonors',
            'allProjects',
            'reassessments',
            'psychReports'
        ));
    }

    public function attachSponsor(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'donor_id' => 'required|exists:donors,id',
            'family_member_id' => 'nullable|exists:beneficiary_family_members,id',
            'monthly_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:255',
        ]);

        $donorId = (int) $validated['donor_id'];
        $amount = isset($validated['monthly_amount']) && $validated['monthly_amount'] !== '' ? (float)$validated['monthly_amount'] : null;
        $notes = $validated['notes'] ?? null;

        if (!empty($validated['family_member_id'])) {
            $member = $beneficiary->familyMembers()->find((int)$validated['family_member_id']);
            if ($member) {
                $member->sponsors()->syncWithoutDetaching([
                    $donorId => [
                        'monthly_amount' => $amount,
                        'notes' => $notes,
                    ]
                ]);
                if ($amount !== null && $amount > 0) {
                    $member->update(['sponsorship_amount' => $amount]);
                }
                $beneficiary->sponsors()->syncWithoutDetaching([$donorId]);

                $amtText = $amount ? " بمبلغ كفالة [{$amount} ج.م]" : '';
                return back()->with('success', "تم ربط الكفيل بالطفل/الفرد [{$member->full_name}]{$amtText} بنجاح، ويسمّع تلقائياً في إجمالي كفالات الأسرة.");
            }
        }

        $beneficiary->sponsors()->syncWithoutDetaching([$donorId]);
        if ($amount !== null && $amount > 0) {
            $beneficiary->update(['monthly_sponsorship_amount' => $amount]);
        }

        return back()->with('success', 'تم ربط الكفيل بملف الأسرة بنجاح.');
    }

    public function detachSponsor(Beneficiary $beneficiary, Donor $donor): RedirectResponse
    {
        $beneficiary->sponsors()->detach($donor->id);

        return back()->with('success', 'تم إلغاء ربط الكفيل بالحالة.');
    }

    public function recordDirectAid(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $validated = $request->validate([
            'aid_type' => 'required|string|in:cash,in_kind,service',
            'category_title' => 'nullable|string|max:191',
            'amount' => 'nullable|numeric|min:0',
            'donor_id' => 'nullable|exists:donors,id',
            'project_id' => 'nullable',
            'purpose' => 'nullable|string|max:255',
            'received_at' => 'nullable|date',
            'notes' => 'nullable|string|max:500',
        ]);

        $type = ($validated['aid_type'] ?? 'in_kind') === 'cash' ? 'cash' : 'in_kind';
        $amount = (float) ($validated['amount'] ?? 0);
        $receivedAt = !empty($validated['received_at']) ? Carbon::parse($validated['received_at']) : now();
        $donorId = !empty($validated['donor_id']) ? (int) $validated['donor_id'] : null;

        // Resolve purpose safely without undefined array key errors
        $purpose = null;
        if (!empty($validated['purpose']) && $validated['purpose'] !== 'other') {
            $purpose = $validated['purpose'];
        } elseif (!empty($validated['category_title'])) {
            $purpose = $validated['category_title'];
        } elseif (!empty($validated['purpose'])) {
            $purpose = $validated['purpose'];
        } else {
            $purpose = $type === 'cash' ? 'sadaqat' : 'مساعدة عينية';
        }

        // Project: if user selected "none" / empty, do not link to any project; if specified, link to that project; fallback to beneficiary's project
        $projectId = null;
        if ($request->has('project_id')) {
            $pVal = $request->input('project_id');
            $projectId = (!empty($pVal) && $pVal !== 'none') ? (int) $pVal : null;
        } else {
            $projectId = $beneficiary->project_id;
        }

        $receiptNumber = 'AID-' . strtoupper(substr(uniqid(), -6)) . '-' . $beneficiary->id;

        $donation = Donation::create([
            'source' => 'dashboard',
            'category' => 'beneficiary_direct_aid',
            'user_id' => auth()->id(),
            'beneficiary_id' => $beneficiary->id,
            'donor_id' => $donorId,
            'project_id' => $projectId,
            'guest_house_id' => null, // Do not falsely attribute direct aid to guest house revenue
            'type' => $type,
            'cash_channel' => 'cash',
            'currency' => 'EGP',
            'amount' => $type === 'cash' ? $amount : 0,
            'estimated_value' => $type === 'in_kind' ? $amount : 0,
            'receipt_number' => $receiptNumber,
            'purpose' => $purpose,
            'allocation_note' => $validated['notes'] ?? (!empty($validated['category_title']) ? $validated['category_title'] : 'تم تسجيل المساعدة مباشرة من ملف الحالة'),
            'received_at' => $receivedAt,
            'status' => 'completed',
            'created_by' => auth()->id(),
        ]);

        if ($donorId && !$beneficiary->sponsors->contains($donorId)) {
            $beneficiary->sponsors()->syncWithoutDetaching([$donorId]);
        }

        return back()->with('success', "تم تسجيل استلام المساعدة بنجاح برقم إيصال [{$receiptNumber}] وتم إدراجها فوراً في سجل الحالة.");
    }

    public function edit(Beneficiary $beneficiary): View|RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $projects = Project::query()
            ->where(fn($q) => $q->whereNull('status')->orWhere('status', '!=', 'archived'))
            ->orderBy('name')->get();

        $beneficiary->load(['familyMembers.sponsors']);

        return view('beneficiaries.edit', compact(
            'beneficiary',
            'projects'
        ));
    }

    public function update(UpdateBeneficiaryRequest $request, Beneficiary $beneficiary): RedirectResponse
    {
        if ($this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        try {
            $result = $this->beneficiaryService->updateBeneficiary($beneficiary, $request->validated());

            if ($result instanceof ChangeRequest) {
                return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المستفيد للموافقة.');
            }

            return redirect()->route('beneficiaries.show', $beneficiary)->with('success', 'تم تعديل المستفيد بنجاح.');
        } catch (\Exception $e) {
            return back()->withErrors(['status' => $e->getMessage()]);
        }
    }

    public function destroy(Beneficiary $beneficiary): RedirectResponse
    {
        $user = auth()->user();
        $isManager = $user && ($user->hasRole('admin') || $user->hasRole('manager'));

        if (!$isManager && $this->hasPendingRequest($beneficiary)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المستفيد لديه طلب مراجعة حالياً');
        }

        $result = $this->beneficiaryService->deleteBeneficiary($beneficiary);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المستفيد للموافقة.');
        }

        return redirect()->route('beneficiaries.index')->with('success', 'تم حذف المستفيد بنجاح.');
    }

    public function bulkUpdate(Request $request): RedirectResponse
    {
        $request->validate([
            'ids'         => 'required|array',
            'ids.*'       => 'exists:beneficiaries,id',
            'bulk_action' => 'required|string',
        ]);

        $count = $this->beneficiaryService->bulkUpdate($request->input('ids'), $request->input('bulk_action'));

        $message = "تم تحديث {$count} مستفيد بنجاح.";
        if ($request->input('bulk_action') === 'delete') {
            $message = "تم حذف {$count} مستفيد بنجاح.";
        }

        return redirect()->back()->with('success', $message);
    }

    public function uploadPatientDocument(Request $request, Beneficiary $beneficiary): RedirectResponse
    {
        $allowedTypes = [
            'patient_id_front' => 'patient_id_front_path',
            'patient_id_back' => 'patient_id_back_path',
            'companion_id_front' => 'companion_id_front_path',
            'companion_id_back' => 'companion_id_back_path',
            'followup_card' => 'followup_card_path',
            'referral_letter' => 'referral_letter_path',
        ];

        $request->validate([
            'doc_type' => 'required|string|in:' . implode(',', array_keys($allowedTypes)),
            'file' => 'required|file|mimes:jpg,jpeg,png,webp,pdf,doc,docx|max:15360',
            'companion_name' => 'nullable|string|max:255',
            'companion_national_id' => 'nullable|string|max:20',
            'companion_phone' => 'nullable|string|max:25',
            'companion_relation' => 'nullable|string|max:100',
        ]);

        $ext = strtolower((string) $request->file('file')->getClientOriginalExtension());
        $dangerousExtensions = ['php', 'phtml', 'php3', 'php4', 'php5', 'php7', 'phar', 'sh', 'exe', 'bat', 'cmd', 'js', 'py', 'pl', 'cgi', 'htaccess'];
        if (in_array($ext, $dangerousExtensions, true)) {
            return back()->with('error', 'نوع الملف غير مسموح به لأسباب أمنية.');
        }

        $profile = $beneficiary->patientProfile;
        if (!$profile) {
            $defaultGuestHouseId = $beneficiary->guest_house_id ?: \App\Models\GuestHouse::value('id');
            $profile = $beneficiary->patientProfile()->create([
                'guest_house_id' => $defaultGuestHouseId ?: 1,
                'treatment_type' => 'other',
            ]);
        }

        $docType = $request->input('doc_type');
        $column = $allowedTypes[$docType];

        if ($request->filled('companion_name')) {
            $profile->companion_name = $request->input('companion_name');
        }
        if ($request->filled('companion_national_id')) {
            $profile->companion_national_id = $request->input('companion_national_id');
        }
        if ($request->filled('companion_phone')) {
            $profile->companion_phone = $request->input('companion_phone');
        }
        if ($request->filled('companion_relation')) {
            $profile->companion_relation = $request->input('companion_relation');
        }

        $profile->uploadImage($request->file('file'), 'guest-houses/patients', $column);

        return back()->with('success', 'تم رفع المستند بنجاح وتحديث ملف دار الضيافة.');
    }

    public function deletePatientDocument(Request $request, Beneficiary $beneficiary, string $docType): RedirectResponse
    {
        $allowedTypes = [
            'patient_id_front' => 'patient_id_front_path',
            'patient_id_back' => 'patient_id_back_path',
            'companion_id_front' => 'companion_id_front_path',
            'companion_id_back' => 'companion_id_back_path',
            'followup_card' => 'followup_card_path',
            'referral_letter' => 'referral_letter_path',
        ];

        if (!array_key_exists($docType, $allowedTypes)) {
            return back()->with('error', 'نوع المستند غير صالح.');
        }

        $profile = $beneficiary->patientProfile;
        if ($profile) {
            $column = $allowedTypes[$docType];
            $profile->deleteImage($column, true);
        }

        return back()->with('success', 'تم حذف المستند بنجاح.');
    }

    private function hasPendingRequest(Beneficiary $beneficiary): bool
    {
        return ChangeRequest::where('model_type', Beneficiary::class)
            ->where('model_id', $beneficiary->id)
            ->where('status', 'pending')
            ->exists();
    }

    private function pdfResponse(string $html, string $filename, string $orientation = 'portrait'): Response
    {
        $options = new Options();
        $options->set('defaultFont', 'DejaVu Sans');
        $options->set('isRemoteEnabled', false);

        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html, 'UTF-8');
        $dompdf->setPaper('A4', $orientation);
        $dompdf->render();

        return response($dompdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
