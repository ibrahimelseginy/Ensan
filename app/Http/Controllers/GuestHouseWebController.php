<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GuestHouse;
use App\Models\GuestHouseMonthlyVolunteer;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Models\Beneficiary;
use App\Models\Donor;
use App\Models\Treasury;
use App\Models\Warehouse;
use App\Models\Item;
use App\Models\InventoryTransaction;
use App\Models\WebRoomBooking;
use App\Models\MobileCaseApplication;
use App\Models\GuestHouseWing;
use App\Models\GuestHouseBed;
use App\Models\GuestHouseStay;
use App\Models\GuestHouseCustody;
use App\Models\GuestHouseMeal;
use App\Services\GuestHouseService;
use App\Services\DonationService;
use App\Services\ExpenseService;
use App\Http\Requests\StoreBeneficiaryRequest;
use App\Http\Requests\StoreDonationRequest;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\StoreGuestHouseRequest;
use App\Http\Requests\UpdateGuestHouseRequest;
use App\Http\Requests\SetGuestHouseManagerRequest;
use App\Http\Requests\AttachGuestHouseVolunteerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\View\View;

final class GuestHouseWebController extends Controller
{
    public function __construct(
        private GuestHouseService $guestHouseService,
        private DonationService $donationService,
        private ExpenseService $expenseService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status', 'governorate']);
        $houses  = $this->guestHouseService->getFilteredHouses($filters, 24);

        return view('guest_houses.index', array_merge(compact('houses'), $filters));
    }

    public function create(): View
    {
        $users = User::orderBy('name')->get();
        return view('guest_houses.create', compact('users'));
    }

    public function store(StoreGuestHouseRequest $request): RedirectResponse
    {
        $result = $this->guestHouseService->createHouse($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة دار الضيافة للموافقة.');
        }

        return redirect()->route('guest-houses.show', $result);
    }

    public function show(GuestHouse $guest_house): View|RedirectResponse
    {
        if ($this->hasPendingRequest($guest_house)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $stats                = $this->guestHouseService->getHouseStats($guest_house);
        $volunteers           = User::where('is_volunteer', true)->orderBy('name')->get();
        $guestHouseVolunteers = $guest_house->volunteers()->orderBy('name')->get();
        $monthlyVolunteers    = $guest_house->monthlyVolunteers()->with('user')->get();
        $beneficiaryOptions   = Beneficiary::orderBy('full_name')->get(['id', 'code', 'full_name']);
        $sponsors             = Donor::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $guestHouseDonors     = Donor::where('active', true)->orderBy('name')->get(['id', 'name', 'phone']);
        $guestHouseBeneficiaries = Beneficiary::where('guest_house_id', $guest_house->id)
            ->orderBy('full_name')
            ->get(['id', 'full_name']);
        $guestHouseWarehouses = Warehouse::when(
            Schema::hasColumn('warehouses', 'is_active'),
            fn ($query) => $query->where('is_active', true)
        )->orderBy('name')->get(['id', 'name']);
        $guestHouseDonationTreasuries = Treasury::active()
            ->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_DELEGATE, Treasury::TYPE_PETTY_CASH])
            ->orderBy('name')
            ->get();
        $guestHouseExpenseTreasuries = Treasury::active()
            ->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH])
            ->orderBy('name')
            ->get();
        $guestHouseItems = Item::where('is_active', true)->orderBy('name')->get(['id', 'name', 'unit']);
        $guestHouseWings = $guest_house->wings()->with(['beds.activeStay.beneficiary'])->orderBy('name')->get();
        $availableBeds = $guestHouseWings->flatMap(fn ($wing) => $wing->beds->filter(
            fn ($bed) => $bed->status === 'available' && ! $bed->activeStay
        ));
        $residentStays = $guest_house->stays()->where('status', 'resident')
            ->with(['beneficiary.patientProfile', 'bed.wing'])->latest('arrival_date')->get();
        $departedStays = $guest_house->stays()->where('status', 'departed')
            ->with(['beneficiary', 'bed.wing'])->latest('departed_at')->limit(30)->get();
        $guestHouseCustodies = $guest_house->custodies()->with(['treasury', 'warehouse'])->where('is_active', true)->get();
        $inventoryCustodies = $guestHouseCustodies->where('type', 'in_kind');
        $guestHouseInventory = InventoryTransaction::query()
            ->selectRaw('warehouse_id, item_id, SUM(CASE WHEN type = ? THEN quantity ELSE -quantity END) AS stock', ['in'])
            ->where('guest_house_id', $guest_house->id)->where('status', 'approved')
            ->whereIn('type', ['in', 'out'])->groupBy('warehouse_id', 'item_id')
            ->with(['item', 'warehouse'])->having('stock', '!=', 0)->get();
        $todayMeals = $guest_house->meals()->whereDate('meal_date', today())
            ->with('servings.beneficiary')->get()->keyBy('meal_type');
        $pendingBookings = WebRoomBooking::query()
            ->where('status', 'pending')
            ->where(function ($query) use ($guest_house) {
                $query->where('guest_house_id', $guest_house->id)->orWhereNull('guest_house_id');
            })->latest()->limit(50)->get();
        $pendingMobileCases = MobileCaseApplication::query()
            ->where('status', 'pending')->where('case_type', 'medical')
            ->where(function ($query) use ($guest_house) {
                $query->where('guest_house_id', $guest_house->id)->orWhere(function ($nested) use ($guest_house) {
                    $nested->whereNull('guest_house_id')->when($guest_house->governorate, fn ($q) => $q->where('governorate', $guest_house->governorate));
                });
            })->latest()->limit(50)->get();

        return view('guest_houses.show', array_merge(compact(
            'guest_house',
            'volunteers',
            'guestHouseVolunteers',
            'monthlyVolunteers',
            'beneficiaryOptions',
            'sponsors',
            'guestHouseDonors',
            'guestHouseBeneficiaries',
            'guestHouseWarehouses',
            'guestHouseDonationTreasuries',
            'guestHouseExpenseTreasuries'
            ,'guestHouseItems'
            ,'guestHouseWings'
            ,'availableBeds'
            ,'residentStays'
            ,'departedStays'
            ,'guestHouseCustodies'
            ,'inventoryCustodies'
            ,'guestHouseInventory'
            ,'todayMeals'
            ,'pendingBookings'
            ,'pendingMobileCases'
        ), $stats));
    }

    public function storeBeneficiary(StoreBeneficiaryRequest $request, GuestHouse $guest_house): RedirectResponse
    {
        $data = $request->validated();
        $allocatedBeneficiaryIds = $data['allocated_beneficiary_ids'] ?? [];
        $sponsorIds = $data['sponsor_ids'] ?? [];
        $bedId = isset($data['guest_house_bed_id']) ? (int) $data['guest_house_bed_id'] : null;
        $profileData = collect($data)->only([
            'treatment_type', 'medical_center', 'sessions_count', 'medical_notes',
        ])->filter(fn ($value) => $value !== null && $value !== '')->all();
        $arrivalDate = $data['arrival_date'] ?? now()->toDateString();
        $expectedDays = $data['expected_days'] ?? null;

        unset(
            $data['allocated_beneficiary_ids'],
            $data['sponsor_ids'],
            $data['project_id'],
            $data['campaign_id'],
            $data['guest_house_id'],
            $data['status'],
            $data['rejection_reason']
            ,$data['treatment_type']
            ,$data['medical_center']
            ,$data['sessions_count']
            ,$data['medical_notes']
            ,$data['patient_id_front']
            ,$data['patient_id_back']
            ,$data['followup_card']
            ,$data['referral_letter']
            ,$data['guest_house_bed_id']
            ,$data['arrival_date']
            ,$data['expected_days']
        );

        $data['status'] = 'new';
        if (empty($data['code'])) {
            $data['code'] = 'BEN-' . strtoupper(Str::random(6));
        }

        DB::transaction(function () use ($request, $guest_house, $data, $allocatedBeneficiaryIds, $sponsorIds, $bedId, $profileData, $arrivalDate, $expectedDays): void {
            $bed = $bedId ? $this->lockAvailableBed($guest_house, $bedId) : null;
            $beneficiary = $guest_house->beneficiaries()->create($data);
            $beneficiary->allocatedBeneficiaries()->sync($allocatedBeneficiaryIds);
            $beneficiary->sponsors()->sync($sponsorIds);

            $profile = $beneficiary->patientProfile()->create(array_merge($profileData, ['guest_house_id' => $guest_house->id]));
            foreach ([
                'patient_id_front' => 'patient_id_front_path',
                'patient_id_back' => 'patient_id_back_path',
                'followup_card' => 'followup_card_path',
                'referral_letter' => 'referral_letter_path',
            ] as $input => $column) {
                if ($request->hasFile($input)) {
                    $profile->uploadImage($request->file($input), 'guest-houses/patients', $column);
                }
            }

            if ($bed) {
                $guest_house->stays()->create([
                    'beneficiary_id' => $beneficiary->id,
                    'guest_house_bed_id' => $bed->id,
                    'status' => 'resident',
                    'arrival_date' => $arrivalDate,
                    'expected_days' => $expectedDays,
                    'admitted_at' => now(),
                    'approved_by' => auth()->id(),
                ]);
                $beneficiary->update(['status' => 'accepted']);
            }
        });

        return back()->with('success', 'تم إضافة المستفيد لدار الضيافة بنجاح.');
    }

    public function storeDonation(StoreDonationRequest $request, GuestHouse $guest_house): RedirectResponse
    {
        try {
            $data = $request->validated();
            unset($data['project_id'], $data['campaign_id'], $data['guest_house_id']);
            $data['guest_house_id'] = $guest_house->id;
            $data['donationable_type'] = GuestHouse::class;
            $data['donationable_id'] = $guest_house->id;
            $data['auto_added_to_inventory'] = (bool) ($data['add_to_inventory'] ?? false);
            unset($data['add_to_inventory']);

            if (($data['type'] ?? null) === 'cash'
                && ! $this->isAllowedDonationTreasury((int) ($data['treasury_id'] ?? 0))) {
                throw new \RuntimeException('الخزينة المختارة غير متاحة لتبرعات دار الضيافة.');
            }

            $result = $this->donationService->createDonation($data);

            return back()->with('success', $result instanceof ChangeRequest
                ? 'تم إرسال تبرع دار الضيافة للمراجعة.'
                : 'تم تسجيل تبرع دار الضيافة بنجاح.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل التبرع: ' . $exception->getMessage());
        }
    }

    public function storeExpense(StoreExpenseRequest $request, GuestHouse $guest_house): RedirectResponse
    {
        try {
            $data = $request->validated();
            unset($data['project_id'], $data['campaign_id'], $data['workspace_id'], $data['guest_house_id']);
            $data['guest_house_id'] = $guest_house->id;

            if (! $this->isAllowedExpenseTreasury((int) ($data['treasury_id'] ?? 0))) {
                throw new \RuntimeException('الخزينة المختارة غير متاحة لمصروفات دار الضيافة.');
            }

            if (! empty($data['beneficiary_id'])
                && ! Beneficiary::whereKey($data['beneficiary_id'])->where('guest_house_id', $guest_house->id)->exists()) {
                throw new \RuntimeException('المستفيد المختار غير مرتبط بدار الضيافة الحالية.');
            }

            $result = $this->expenseService->createExpense($data);

            return back()->with('success', $result instanceof ChangeRequest
                ? 'تم إرسال مصروف دار الضيافة للمراجعة.'
                : 'تم تسجيل المصروف وخصمه من الخزينة بنجاح.');
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل المصروف: ' . $exception->getMessage());
        }
    }

    public function storeWing(Request $request, GuestHouse $guest_house): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:100',
            'beds_count' => 'required|integer|min:1|max:100',
            'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($guest_house, $data): void {
            $wing = $guest_house->wings()->create(['name' => $data['name'], 'notes' => $data['notes'] ?? null]);
            for ($number = 1; $number <= (int) $data['beds_count']; $number++) {
                $wing->beds()->create(['number' => (string) $number]);
            }
        });

        return back()->with('success', 'تمت إضافة الجناح والأسِرّة بنجاح.');
    }

    public function updateBedStatus(Request $request, GuestHouse $guest_house, GuestHouseBed $bed): RedirectResponse
    {
        $data = $request->validate(['status' => 'required|in:available,maintenance']);
        abort_unless((int) $bed->wing->guest_house_id === (int) $guest_house->id, 404);

        if ($data['status'] === 'maintenance' && $bed->activeStay()->exists()) {
            return back()->with('error', 'لا يمكن تحويل سرير مشغول إلى الصيانة.');
        }

        $bed->update($data);
        return back()->with('success', 'تم تحديث حالة السرير.');
    }

    public function storeCustody(Request $request, GuestHouse $guest_house): RedirectResponse
    {
        $data = $request->validate([
            'name' => 'required|string|max:150',
            'type' => 'required|in:financial,in_kind',
            'treasury_id' => 'nullable|exists:treasuries,id',
            'warehouse_id' => 'nullable|exists:warehouses,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        if ($data['type'] === 'in_kind' && empty($data['warehouse_id'])) {
            return back()->withInput()->with('error', 'اختر المخزن المرتبط بالعهدة العينية.');
        }

        DB::transaction(function () use ($guest_house, $data): void {
            $treasuryId = $data['treasury_id'] ?? null;
            if ($data['type'] === 'financial' && ! $treasuryId) {
                $treasuryId = Treasury::create([
                    'name' => $data['name'] . ' - ' . $guest_house->name,
                    'code' => 'GH-' . $guest_house->id . '-' . strtoupper(Str::random(8)),
                    'type' => Treasury::TYPE_PETTY_CASH, 'currency' => 'EGP',
                    'opening_balance' => 0, 'current_balance' => 0, 'is_active' => true,
                    'manager_id' => $guest_house->manager_user_id, 'location' => $guest_house->location,
                    'description' => 'عهدة مالية مرتبطة بدار الضيافة',
                ])->id;
            }
            $guest_house->custodies()->create([
                'name' => $data['name'], 'type' => $data['type'],
                'treasury_id' => $data['type'] === 'financial' ? $treasuryId : null,
                'warehouse_id' => $data['type'] === 'in_kind' ? $data['warehouse_id'] : null,
                'notes' => $data['notes'] ?? null,
            ]);
        });

        return back()->with('success', 'تم ربط العهدة بالدار بنجاح.');
    }

    public function issueInventory(Request $request, GuestHouse $guest_house): RedirectResponse
    {
        $data = $request->validate([
            'custody_id' => 'required|exists:guest_house_custodies,id',
            'item_id' => 'required|exists:items,id',
            'quantity' => 'required|numeric|min:0.001',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'notes' => 'required|string|max:1000',
            'transaction_date' => 'nullable|date',
        ]);

        try {
            DB::transaction(function () use ($guest_house, $data): void {
                $custody = GuestHouseCustody::whereKey($data['custody_id'])
                    ->where('guest_house_id', $guest_house->id)->where('type', 'in_kind')
                    ->where('is_active', true)->lockForUpdate()->firstOrFail();

                $transactions = InventoryTransaction::where('warehouse_id', $custody->warehouse_id)
                    ->where('item_id', $data['item_id'])->where('status', 'approved')
                    ->whereIn('type', ['in', 'out'])->lockForUpdate()->get(['type', 'quantity']);
                $stock = $transactions->sum(fn ($transaction) => $transaction->type === 'in' ? (float) $transaction->quantity : -(float) $transaction->quantity);
                if ($stock < (float) $data['quantity']) {
                    throw new \RuntimeException('الرصيد المتاح من الصنف غير كافٍ. المتاح: ' . number_format($stock, 3));
                }

                InventoryTransaction::create([
                    'item_id' => $data['item_id'], 'warehouse_id' => $custody->warehouse_id,
                    'guest_house_id' => $guest_house->id, 'beneficiary_id' => $data['beneficiary_id'] ?? null,
                    'type' => 'out', 'quantity' => $data['quantity'],
                    'reference' => 'GH-ISSUE-' . now()->format('YmdHis'), 'notes' => $data['notes'],
                    'transaction_date' => $data['transaction_date'] ?? now()->toDateString(),
                    'status' => 'approved', 'approved_by' => auth()->id(), 'approved_at' => now(), 'user_id' => auth()->id(),
                ]);
            });
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تنفيذ الصرف: ' . $exception->getMessage());
        }

        return back()->with('success', 'تم صرف الصنف وتحديث رصيد المخزون.');
    }

    public function departStay(GuestHouse $guest_house, GuestHouseStay $stay): RedirectResponse
    {
        abort_unless((int) $stay->guest_house_id === (int) $guest_house->id, 404);
        if ($stay->status !== 'resident') {
            return back()->with('error', 'هذه الحالة غير مقيمة حاليًا.');
        }
        $stay->update(['status' => 'departed', 'departed_at' => now()]);
        return back()->with('success', 'تم تسجيل مغادرة الحالة وأصبح السرير متاحًا.');
    }

    public function returnStay(Request $request, GuestHouse $guest_house, GuestHouseStay $stay): RedirectResponse
    {
        abort_unless((int) $stay->guest_house_id === (int) $guest_house->id && $stay->status === 'departed', 404);
        $data = $request->validate([
            'guest_house_bed_id' => 'required|exists:guest_house_beds,id',
            'arrival_date' => 'required|date',
            'expected_days' => 'required|integer|min:1|max:365',
            'notes' => 'nullable|string|max:1000',
        ]);

        try {
            DB::transaction(function () use ($guest_house, $stay, $data): void {
                $bed = $this->lockAvailableBed($guest_house, (int) $data['guest_house_bed_id']);
                $guest_house->stays()->create([
                    'beneficiary_id' => $stay->beneficiary_id, 'guest_house_bed_id' => $bed->id,
                    'previous_stay_id' => $stay->id, 'status' => 'resident',
                    'arrival_date' => $data['arrival_date'], 'expected_days' => $data['expected_days'],
                    'admitted_at' => now(), 'notes' => $data['notes'] ?? null, 'approved_by' => auth()->id(),
                ]);
            });
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر تسجيل العودة: ' . $exception->getMessage());
        }

        return back()->with('success', 'تم تسجيل عودة الحالة وإقامة جديدة.');
    }

    public function storeMeal(Request $request, GuestHouse $guest_house): RedirectResponse
    {
        $data = $request->validate([
            'meal_date' => 'required|date', 'meal_type' => 'required|in:breakfast,lunch,dinner',
            'served_at' => 'nullable|date_format:H:i', 'meal_image' => 'nullable|any_image|max:10240',
            'received_beneficiary_ids' => 'nullable|array',
            'received_beneficiary_ids.*' => 'integer|exists:beneficiaries,id', 'notes' => 'nullable|string|max:1000',
        ]);

        DB::transaction(function () use ($request, $guest_house, $data): void {
            $meal = GuestHouseMeal::updateOrCreate(
                ['guest_house_id' => $guest_house->id, 'meal_date' => $data['meal_date'], 'meal_type' => $data['meal_type']],
                ['served_at' => $data['served_at'] ?? null, 'notes' => $data['notes'] ?? null, 'created_by' => auth()->id()]
            );
            if ($request->hasFile('meal_image')) {
                $meal->uploadImage($request->file('meal_image'), 'guest-houses/meals');
            }
            $receivedIds = array_map('intval', $data['received_beneficiary_ids'] ?? []);
            $residentIds = $guest_house->stays()->where('status', 'resident')->pluck('beneficiary_id')->unique();
            foreach ($residentIds as $beneficiaryId) {
                $received = in_array((int) $beneficiaryId, $receivedIds, true);
                $meal->servings()->updateOrCreate(['beneficiary_id' => $beneficiaryId], [
                    'received' => $received, 'received_at' => $received ? now() : null,
                ]);
            }
        });

        return back()->with('success', 'تم حفظ كشف الوجبة للمقيمين.');
    }

    public function decideBooking(Request $request, GuestHouse $guest_house, WebRoomBooking $booking): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:accept,reject', 'guest_house_bed_id' => 'nullable|required_if:decision,accept|exists:guest_house_beds,id',
            'expected_days' => 'nullable|required_if:decision,accept|integer|min:1|max:365',
            'treatment_type' => 'nullable|in:chemotherapy,radiation,other', 'sessions_count' => 'nullable|integer|min:1|max:1000',
            'admin_notes' => 'nullable|string|max:2000',
        ]);
        if ($booking->status !== 'pending' || ($booking->guest_house_id && (int) $booking->guest_house_id !== (int) $guest_house->id)) {
            return back()->with('error', 'تم التعامل مع الطلب أو أنه موجه لدار أخرى.');
        }
        if ($data['decision'] === 'reject') {
            $booking->update(['status' => 'cancelled', 'guest_house_id' => $guest_house->id, 'admin_notes' => $data['admin_notes'] ?? null]);
            return back()->with('success', 'تم رفض طلب الإقامة.');
        }

        try {
            DB::transaction(function () use ($guest_house, $booking, $data): void {
                $bed = $this->lockAvailableBed($guest_house, (int) $data['guest_house_bed_id']);
                $beneficiary = $this->beneficiaryForIntake($guest_house, [
                    'name' => $booking->name, 'phone' => $booking->phone, 'national_id' => $booking->national_id,
                    'address' => $booking->address, 'notes' => $booking->notes,
                ]);
                $stay = $this->createResidentStay($guest_house, $beneficiary, $bed, [
                    'arrival_date' => $booking->arrival_date ?: $booking->check_in,
                    'expected_days' => $data['expected_days'], 'notes' => $data['admin_notes'] ?? null,
                    'source_type' => WebRoomBooking::class, 'source_id' => $booking->id,
                ]);
                $beneficiary->patientProfile()->updateOrCreate([], [
                    'guest_house_id' => $guest_house->id,
                    'treatment_type' => $data['treatment_type'] ?? $booking->treatment_type,
                    'medical_center' => $booking->medical_center, 'sessions_count' => $data['sessions_count'] ?? $booking->sessions_count,
                    'patient_id_front_path' => $booking->patient_id_path,
                    'followup_card_path' => $booking->followup_card_path,
                    'referral_letter_path' => $booking->medical_transfer_path ?: $booking->medical_report_path,
                ]);
                $booking->update(['status' => 'confirmed', 'guest_house_id' => $guest_house->id,
                    'beneficiary_id' => $beneficiary->id, 'guest_house_stay_id' => $stay->id,
                    'admin_notes' => $data['admin_notes'] ?? null, 'treatment_type' => $data['treatment_type'] ?? null,
                    'sessions_count' => $data['sessions_count'] ?? null]);
            });
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر قبول الطلب: ' . $exception->getMessage());
        }
        return back()->with('success', 'تم قبول الطلب وإنشاء المستفيد وحجز السرير.');
    }

    public function decideMobileCase(Request $request, GuestHouse $guest_house, MobileCaseApplication $application): RedirectResponse
    {
        $data = $request->validate([
            'decision' => 'required|in:accept,reject', 'guest_house_bed_id' => 'nullable|required_if:decision,accept|exists:guest_house_beds,id',
            'arrival_date' => 'nullable|required_if:decision,accept|date', 'expected_days' => 'nullable|required_if:decision,accept|integer|min:1|max:365',
            'admin_notes' => 'nullable|string|max:2000',
        ]);
        if ($application->status !== 'pending') return back()->with('error', 'تم التعامل مع الطلب من قبل.');
        if ($data['decision'] === 'reject') {
            $application->update(['status' => 'rejected', 'guest_house_id' => $guest_house->id, 'admin_notes' => $data['admin_notes'] ?? null]);
            return back()->with('success', 'تم رفض الحالة.');
        }
        try {
            DB::transaction(function () use ($guest_house, $application, $data): void {
                $bed = $this->lockAvailableBed($guest_house, (int) $data['guest_house_bed_id']);
                $beneficiary = $this->beneficiaryForIntake($guest_house, [
                    'name' => $application->applicant_name, 'phone' => $application->applicant_phone,
                    'national_id' => $application->applicant_id_number, 'address' => $application->address,
                    'notes' => $application->description,
                ]);
                $stay = $this->createResidentStay($guest_house, $beneficiary, $bed, [
                    'arrival_date' => $data['arrival_date'], 'expected_days' => $data['expected_days'],
                    'notes' => $data['admin_notes'] ?? null, 'source_type' => MobileCaseApplication::class, 'source_id' => $application->id,
                ]);
                $beneficiary->patientProfile()->updateOrCreate([], [
                    'guest_house_id' => $guest_house->id, 'treatment_type' => 'other',
                    'patient_id_front_path' => $application->id_image_path,
                    'referral_letter_path' => $application->medical_report_path,
                ]);
                $application->update(['status' => 'approved', 'guest_house_id' => $guest_house->id,
                    'beneficiary_id' => $beneficiary->id, 'guest_house_stay_id' => $stay->id,
                    'admin_notes' => $data['admin_notes'] ?? null]);
            });
        } catch (\Throwable $exception) {
            return back()->withInput()->with('error', 'تعذر قبول الحالة: ' . $exception->getMessage());
        }
        return back()->with('success', 'تم قبول الحالة القادمة من التطبيق وحجز السرير.');
    }

    public function setManager(SetGuestHouseManagerRequest $request, GuestHouse $guest_house): RedirectResponse
    {
        if ($this->hasPendingRequest($guest_house)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $result = $this->guestHouseService->setManager($guest_house, $request->except('manager_photo'), $request->file('manager_photo'));

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تغيير المدير للمراجعة');
        }

        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function attachVolunteer(AttachGuestHouseVolunteerRequest $request, GuestHouse $guest_house): RedirectResponse
    {
        $this->guestHouseService->attachVolunteer($guest_house, $request->validated());
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function detachVolunteer(GuestHouse $guest_house, User $user): RedirectResponse
    {
        $this->guestHouseService->detachVolunteer($guest_house, (int)$user->id);
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function storeMonthlyVolunteer(Request $request, GuestHouse $guest_house): RedirectResponse
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month'   => 'required|integer|min:1|max:12',
            'year'    => 'required|integer|min:2000|max:2100',
            'notes'   => 'nullable|string'
        ]);

        $this->guestHouseService->storeMonthlyVolunteer($guest_house, $data);
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function destroyMonthlyVolunteer(GuestHouse $guest_house, GuestHouseMonthlyVolunteer $monthlyVolunteer): RedirectResponse
    {
        $this->guestHouseService->deleteMonthlyVolunteer((int)$monthlyVolunteer->id);
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function edit(GuestHouse $guest_house): View|RedirectResponse
    {
        if ($this->hasPendingRequest($guest_house)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $users = User::orderBy('name')->get();
        return view('guest_houses.edit', compact('guest_house', 'users'));
    }

    public function update(UpdateGuestHouseRequest $request, GuestHouse $guest_house): RedirectResponse
    {
        if ($this->hasPendingRequest($guest_house)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $result = $this->guestHouseService->updateHouse($guest_house, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل دار الضيافة للموافقة.');
        }

        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function destroy(GuestHouse $guest_house): RedirectResponse
    {
        if ($this->hasPendingRequest($guest_house)) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $result = $this->guestHouseService->deleteHouse($guest_house);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف دار الضيافة للموافقة.');
        }

        return redirect()->route('guest-houses.index');
    }

    private function lockAvailableBed(GuestHouse $guestHouse, int $bedId): GuestHouseBed
    {
        $bed = GuestHouseBed::query()->whereKey($bedId)
            ->whereHas('wing', fn ($query) => $query->where('guest_house_id', $guestHouse->id))
            ->lockForUpdate()->firstOrFail();
        if ($bed->status !== 'available' || $bed->stays()->where('status', 'resident')->exists()) {
            throw new \RuntimeException('السرير المختار غير متاح حاليًا.');
        }
        return $bed;
    }

    private function beneficiaryForIntake(GuestHouse $guestHouse, array $data): Beneficiary
    {
        $beneficiary = Beneficiary::query()
            ->when(! empty($data['national_id']), fn ($query) => $query->where('national_id', $data['national_id']))
            ->when(empty($data['national_id']), fn ($query) => $query->where('phone', $data['phone']))
            ->first();

        if (! $beneficiary) {
            $beneficiary = Beneficiary::create([
                'code' => 'BEN-' . strtoupper(Str::random(6)), 'full_name' => $data['name'],
                'national_id' => $data['national_id'] ?? null, 'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null, 'assistance_type' => 'service',
                'status' => 'accepted', 'guest_house_id' => $guestHouse->id, 'notes' => $data['notes'] ?? null,
            ]);
        } else {
            if ($beneficiary->guestHouseStays()->where('status', 'resident')->exists()) {
                throw new \RuntimeException('المستفيد لديه إقامة نشطة بالفعل.');
            }
            $beneficiary->update(['guest_house_id' => $guestHouse->id, 'status' => 'accepted']);
        }
        return $beneficiary;
    }

    private function createResidentStay(GuestHouse $guestHouse, Beneficiary $beneficiary, GuestHouseBed $bed, array $data): GuestHouseStay
    {
        return $guestHouse->stays()->create([
            'beneficiary_id' => $beneficiary->id, 'guest_house_bed_id' => $bed->id,
            'status' => 'resident', 'arrival_date' => $data['arrival_date'] ?: now()->toDateString(),
            'expected_days' => $data['expected_days'] ?? null, 'admitted_at' => now(),
            'notes' => $data['notes'] ?? null, 'source_type' => $data['source_type'] ?? null,
            'source_id' => $data['source_id'] ?? null, 'approved_by' => auth()->id(),
        ]);
    }

    private function hasPendingRequest(GuestHouse $guest_house): bool
    {
        return ChangeRequest::where('model_type', GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->exists();
    }

    private function isAllowedDonationTreasury(int $treasuryId): bool
    {
        return $treasuryId > 0 && Treasury::active()
            ->whereKey($treasuryId)
            ->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_DELEGATE, Treasury::TYPE_PETTY_CASH])
            ->exists();
    }

    private function isAllowedExpenseTreasury(int $treasuryId): bool
    {
        return $treasuryId > 0 && Treasury::active()
            ->whereKey($treasuryId)
            ->whereIn('type', [Treasury::TYPE_MAIN, Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH])
            ->exists();
    }
}
