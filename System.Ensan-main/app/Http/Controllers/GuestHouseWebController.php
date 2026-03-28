<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\GuestHouse;
use Illuminate\Http\Request;

final class GuestHouseWebController extends Controller
{
    public function index()
    {
        $q = trim((string) request()->input('q'));
        $status = request()->input('status');
        $houses = GuestHouse::when($q !== '', function ($qb) use ($q) {
            $qb->where('name', 'like', "%$q%")->orWhere('location', 'like', "%$q%");
        })
            ->when($status, function ($qb, $s) {
                $qb->where('status', $s);
            })
            ->orderBy('name')->paginate(24);

        // Add pending check for each house
        $houses->each(function($h) {
            $h->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\GuestHouse::class)
                ->where('model_id', $h->id)
                ->where('status', 'pending')
                ->first();
        });

        return view('guest_houses.index', compact('houses', 'q', 'status'));
    }
    public function create()
    {
        $users = \App\Models\User::orderBy('name')->get();
        return view('guest_houses.create', compact('users'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'status' => 'in:active,archived',
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,id'
        ]);
        $data['status'] = $data['status'] ?? 'active';
        
        $executor = function () use ($data) {
             return GuestHouse::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\GuestHouse::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة دار الضيافة للموافقة.');
        }

        return redirect()->route('guest-houses.show', $result);
    }
    public function show(GuestHouse $guest_house)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $donationsCount = \App\Models\Donation::where('guest_house_id', $guest_house->id)->count();
        $cashSum = (float) \App\Models\Donation::where('guest_house_id', $guest_house->id)->where('type', 'cash')->sum('amount');
        $inKindSum = (float) \App\Models\Donation::where('guest_house_id', $guest_house->id)->where('type', 'in_kind')->sum('estimated_value');
        $beneficiariesCount = \App\Models\Beneficiary::where('guest_house_id', $guest_house->id)->count();
        $expensesCount = \App\Models\Expense::where('guest_house_id', $guest_house->id)->count();

        $latestDonations = \App\Models\Donation::where('guest_house_id', $guest_house->id)->orderByDesc('id')->limit(5)->get();
        $latestExpenses = \App\Models\Expense::where('guest_house_id', $guest_house->id)->orderByDesc('id')->limit(5)->get();
        $latestBeneficiaries = \App\Models\Beneficiary::where('guest_house_id', $guest_house->id)->orderByDesc('id')->limit(5)->get();

        $volunteers = \App\Models\User::where('is_volunteer', true)->orderBy('name')->get();
        $guestHouseVolunteers = $guest_house->volunteers()->orderBy('name')->get();

        $donationsTotal = $cashSum + $inKindSum;
        $expensesTotal = (float) \App\Models\Expense::where('guest_house_id', $guest_house->id)->sum('amount');
        $netBalance = $donationsTotal - $expensesTotal;
        $cashPct = $donationsTotal > 0 ? round(($cashSum / $donationsTotal) * 100) : 0;

        $monthlyVolunteers = $guest_house->monthlyVolunteers()->with('user')->get();

        return view('guest_houses.show', compact('guest_house', 'donationsCount', 'cashSum', 'inKindSum', 'beneficiariesCount', 'expensesCount', 'latestDonations', 'latestExpenses', 'latestBeneficiaries', 'guestHouseVolunteers', 'volunteers', 'donationsTotal', 'expensesTotal', 'netBalance', 'cashPct', 'monthlyVolunteers'));
    }

    public function setManager(GuestHouse $guest_house, Request $request)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'manager_user_id' => 'nullable|exists:users,id',
            'manager_photo' => 'nullable|image|max:5120'
        ]);

        if ($request->hasFile('manager_photo')) {
            $file = $request->file('manager_photo');
            $filename = time() . '_gh_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/managers'), $filename);
            $data['manager_photo_url'] = '/uploads/managers/' . $filename;
        }

        unset($data['manager_photo']);

        $executor = function() use ($guest_house, $data) {
            $guest_house->update($data);
            return $guest_house;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\GuestHouse::class,
            $guest_house->id,
            'update',
            $data,
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تغيير المدير للمراجعة');
        }

        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function attachVolunteer(Request $request, GuestHouse $guest_house)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'nullable|string',
            'started_at' => 'nullable|date',
            'hours' => 'nullable|numeric'
        ]);
        $guest_house->volunteers()->syncWithoutDetaching([
            $data['user_id'] => [
                'role' => $data['role'] ?? null,
                'started_at' => $data['started_at'] ?? null,
                'hours' => $data['hours'] ?? 0
            ]
        ]);
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function detachVolunteer(GuestHouse $guest_house, \App\Models\User $user)
    {
        $guest_house->volunteers()->detach($user->id);
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function storeMonthlyVolunteer(Request $request, GuestHouse $guest_house)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|integer|min:1|max:12',
            'year' => 'required|integer|min:2000|max:2100',
            'notes' => 'nullable|string'
        ]);

        $exists = $guest_house->monthlyVolunteers()
            ->where('user_id', $data['user_id'])
            ->where('month', $data['month'])
            ->where('year', $data['year'])
            ->exists();

        if (!$exists) {
            $guest_house->monthlyVolunteers()->create($data);
        }

        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function destroyMonthlyVolunteer(GuestHouse $guest_house, \App\Models\GuestHouseMonthlyVolunteer $monthlyVolunteer)
    {
        $monthlyVolunteer->delete();
        return redirect()->route('guest-houses.show', $guest_house);
    }

    public function edit(GuestHouse $guest_house)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $users = \App\Models\User::orderBy('name')->get();
        return view('guest_houses.edit', compact('guest_house', 'users'));
    }
    public function update(Request $request, GuestHouse $guest_house)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'name' => 'sometimes|string',
            'location' => 'nullable|string',
            'phone' => 'nullable|string',
            'capacity' => 'nullable|integer',
            'status' => 'in:active,archived',
            'description' => 'nullable|string',
            'manager_user_id' => 'nullable|exists:users,id'
        ]);

        $executor = function () use ($guest_house, $data) {
            $guest_house->update($data);
            return $guest_house;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\GuestHouse::class,
            $guest_house->id,
            'update',
            $data,
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل دار الضيافة للموافقة.');
        }

        return redirect()->route('guest-houses.show', $guest_house);
    }
    public function destroy(GuestHouse $guest_house)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\GuestHouse::class)
            ->where('model_id', $guest_house->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذه الدار لديها طلب مراجعة حالياً');
        }

        $executor = function () use ($guest_house) {
            $guest_house->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\GuestHouse::class,
            $guest_house->id,
            'delete',
            request()->all(),
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف دار الضيافة للموافقة.');
        }

        return redirect()->route('guest-houses.index');
    }
}
