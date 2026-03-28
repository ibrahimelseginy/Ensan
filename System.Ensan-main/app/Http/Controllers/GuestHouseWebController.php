<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\GuestHouse;
use App\Models\GuestHouseMonthlyVolunteer;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\GuestHouseService;
use App\Http\Requests\StoreGuestHouseRequest;
use App\Http\Requests\UpdateGuestHouseRequest;
use App\Http\Requests\SetGuestHouseManagerRequest;
use App\Http\Requests\AttachGuestHouseVolunteerRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final readonly class GuestHouseWebController extends Controller
{
    public function __construct(
        private GuestHouseService $guestHouseService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'status']);
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

        return view('guest_houses.show', array_merge(compact('guest_house', 'volunteers', 'guestHouseVolunteers', 'monthlyVolunteers'), $stats));
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

    private function hasPendingRequest(GuestHouse $guest_house): bool
    {
        return ChangeRequest::where('model_type', GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->exists();
    }
}
