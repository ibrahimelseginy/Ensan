<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Delegate;
use App\Models\DelegateTrip;
use App\Models\TravelRoute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

final class DelegateWebController extends Controller
{
    public function index(Request $request)
    {
        $q = (string) $request->get('q', '');
        $routeId = $request->get('route_id');
        $active = (string) $request->get('active', '');
        $hasPhone = (string) $request->get('has_phone', '');
        $sort = (string) $request->get('sort', 'name');
        $dir = strtolower((string) $request->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $perPage = (int) ($request->get('per_page', 20));
        if (!in_array($perPage, [20, 50, 100], true)) {
            $perPage = 20;
        }
        $sortable = ['id', 'name', 'donations_count'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'name';
        }

        $delegates = Delegate::query()
            ->with('route')
            ->withCount('donations')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%' . $q . '%')->orWhere('phone', 'like', '%' . $q . '%'); }); })
            ->when(!empty($routeId), function ($qb) use ($routeId) {
                $qb->where('route_id', $routeId); })
            ->when($active !== '', function ($qb) use ($active) {
                $qb->where('active', $active === '1'); })
            ->when($hasPhone === '1', function ($qb) {
                $qb->whereNotNull('phone')->where('phone', '<>', ''); })
            ->orderBy($sort, $dir)
            ->paginate($perPage)
            ->appends(['q' => $q, 'route_id' => $routeId, 'active' => $active, 'has_phone' => $hasPhone, 'sort' => $sort, 'dir' => $dir, 'per_page' => $perPage]);

        // Add pending check for each delegate
        $delegates->each(function($d) {
            $d->pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Delegate::class)
                ->where('model_id', $d->id)
                ->where('status', 'pending')
                ->first();
        });

        $routes = TravelRoute::orderBy('name')->get();
        $stats = [
            'total' => Delegate::count(),
            'active' => (int) Delegate::where('active', true)->count(),
            'no_route' => (int) Delegate::whereNull('route_id')->count(),
        ];

        return view('delegates.index', compact('delegates', 'routes', 'q', 'routeId', 'active', 'hasPhone', 'sort', 'dir', 'perPage', 'stats'));
    }
    public function export(Request $request)
    {
        $q = (string) $request->get('q', '');
        $routeId = $request->get('route_id');
        $active = (string) $request->get('active', '');
        $hasPhone = (string) $request->get('has_phone', '');
        $sort = (string) $request->get('sort', 'name');
        $dir = strtolower((string) $request->get('dir', 'asc')) === 'desc' ? 'desc' : 'asc';
        $sortable = ['id', 'name', 'donations_count'];
        if (!in_array($sort, $sortable, true)) {
            $sort = 'name';
        }

        $rows = Delegate::query()->with('route')->withCount('donations')
            ->when($q !== '', function ($qb) use ($q) {
                $qb->where(function ($w) use ($q) {
                    $w->where('name', 'like', '%' . $q . '%')->orWhere('phone', 'like', '%' . $q . '%'); }); })
            ->when(!empty($routeId), function ($qb) use ($routeId) {
                $qb->where('route_id', $routeId); })
            ->when($active !== '', function ($qb) use ($active) {
                $qb->where('active', $active === '1'); })
            ->when($hasPhone === '1', function ($qb) {
                $qb->whereNotNull('phone')->where('phone', '<>', ''); })
            ->orderBy($sort, $dir)->get();

        $headers = ['Content-Type' => 'text/csv; charset=UTF-8', 'Content-Disposition' => 'attachment; filename="delegates.csv"'];
        $callback = function () use ($rows) {
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            fputcsv($out, ['#', 'الاسم', 'الهاتف', 'نشط', 'خط السير', 'عدد التبرعات']);
            foreach ($rows as $d) {
                fputcsv($out, [$d->id, $d->name, $d->phone, $d->active ? 'نعم' : 'لا', optional($d->route)->name, $d->donations_count]);
            }
            fclose($out);
        };
        return response()->stream($callback, 200, $headers);
    }
    public function bulkUpdate(Request $request)
    {
        $ids = (array) $request->input('ids', []);
        $action = (string) $request->input('bulk_action', '');
        if (empty($ids) || !in_array($action, ['activate', 'deactivate', 'delete'], true)) {
            return back()->with('error', 'لم يتم اختيار عناصر أو إجراء');
        }
        $targets = Delegate::whereIn('id', $ids)->get();
        if ($action === 'delete') {
            foreach ($targets as $d) {
                $d->delete();
            }
        } else {
            foreach ($targets as $d) {
                $d->update(['active' => $action === 'activate']);
            }
        }
        return back()->with('success', 'تم تنفيذ الإجراء الجماعي');
    }
    public function create()
    {
        $routes = TravelRoute::orderBy('name')->get();
        $employees = \App\Models\User::where('is_employee', true)->orderBy('name')->get();
        return view('delegates.create', compact('routes', 'employees'));
    }
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'nullable|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'route_id' => 'nullable|exists:travel_routes,id',
            'user_id' => 'nullable|exists:users,id',
            'profile_photo' => 'nullable|image|max:2048'
        ]);

        if (!empty($data['user_id'])) {
            $user = \App\Models\User::find($data['user_id']);
            if ($user) {
                $data['name'] = $user->name;
                $data['phone'] = $user->phone;
                $data['email'] = $user->email;
                if ($user->profile_photo_path && !isset($data['profile_photo_path']) && !$request->hasFile('profile_photo')) {
                    $data['profile_photo_path'] = $user->profile_photo_path;
                }
            }
        }
        if ($request->hasFile('profile_photo')) {
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        unset($data['profile_photo']);

        $executor = function () use ($data) {
            return Delegate::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Delegate::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تفعيل المندوب للإدارة.');
        }

        return redirect()->route('delegates.show', $result);
    }

    public function show(Delegate $delegate)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Delegate::class)
            ->where('model_id', $delegate->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $delegate->load('route');
        $trips = $delegate->trips()->orderBy('date', 'desc')->paginate(20);
        $stats = [
            'total_cost' => $delegate->trips()->sum('cost'),
            'pending_cost' => $delegate->trips()->where('status', 'pending')->sum('cost'),
            'paid_cost' => $delegate->trips()->where('status', 'paid')->sum('cost'),
            'count' => $delegate->trips()->count()
        ];
        return view('delegates.show', compact('delegate', 'trips', 'stats'));
    }

    public function edit(Delegate $delegate)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Delegate::class)
            ->where('model_id', $delegate->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $routes = TravelRoute::orderBy('name')->get();
        $employees = \App\Models\User::where('is_employee', true)->orderBy('name')->get();
        return view('delegates.edit', compact('delegate', 'routes', 'employees'));
    }

    public function update(Request $request, Delegate $delegate)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Delegate::class)
            ->where('model_id', $delegate->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'name' => 'sometimes|string',
            'phone' => 'nullable|string',
            'email' => 'nullable|email',
            'route_id' => 'nullable|exists:travel_routes,id',
            'user_id' => 'nullable|exists:users,id',
            'profile_photo' => 'nullable|image|max:2048'
        ]);
        if (!empty($data['user_id'])) {
            $user = \App\Models\User::find($data['user_id']);
            if ($user) {
                $data['name'] = $user->name;
                $data['phone'] = $user->phone;
                $data['email'] = $user->email;
            }
        }
        if ($request->hasFile('profile_photo')) {
            if ($delegate->profile_photo_path) {
                Storage::disk('public')->delete($delegate->profile_photo_path);
            }
            $data['profile_photo_path'] = $request->file('profile_photo')->store('profile-photos', 'public');
        }
        unset($data['profile_photo']);

        $executor = function () use ($delegate, $data) {
            $delegate->update($data);
            return $delegate;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Delegate::class,
            $delegate->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المندوب للإدارة.');
        }

        return redirect()->route('delegates.show', $delegate);
    }

    public function destroy(Delegate $delegate)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', \App\Models\Delegate::class)
            ->where('model_id', $delegate->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المندوب لديه طلب مراجعة حالياً');
        }

        $executor = function () use ($delegate) {
            $delegate->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Delegate::class,
            $delegate->id,
            'delete',
            request()->all(),
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المندوب للإدارة.');
        }

        return redirect()->route('delegates.index')->with('success', 'تم حذف المندوب بنجاح');
    }


    public function storeTrip(Request $request, Delegate $delegate)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'description' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'fuel_cost' => 'nullable|numeric|min:0',
            'other_expenses' => 'nullable|numeric|min:0',
            'from_location' => 'nullable|string',
            'to_location' => 'nullable|string',
            'distance_km' => 'nullable|numeric|min:0',
            'payment_method' => 'nullable|string',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,paid',
            'create_journal_entry' => 'nullable|boolean'
        ]);
        
        $executor = function () use ($delegate, $data) {
            $trip = $delegate->trips()->create($data);
            if (request()->create_journal_entry) {
                try {
                    $logisticsService = app(\App\Services\LogisticsAccountingService::class);
                    $logisticsService->createJournalEntry($trip);
                } catch (\Exception $e) {}
            }
            return $trip;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\DelegateTrip::class,
            null,
            'create',
            array_merge($data, [
                'delegate_id' => $delegate->id,
                'delegate_name' => $delegate->name
            ]),
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المشوار للإدارة.');
        }
        
        return back()->with('success', 'تم إضافة المشوار بنجاح');
    }

    public function destroyTrip(Delegate $delegate, DelegateTrip $trip)
    {
        if ($trip->delegate_id !== $delegate->id) { abort(403); }

        $executor = function () use ($trip) {
            $trip->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\DelegateTrip::class,
            $trip->id,
            'delete',
            [
                'delegate_id' => $delegate->id,
                'delegate_name' => $delegate->name,
                'trip_date' => $trip->date?->format('Y-m-d'),
                'trip_description' => $trip->description,
                'trip_cost' => $trip->cost
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المشوار للإدارة.');
        }

        return back()->with('success', 'تم حذف المشوار');
    }

    public function updateTripStatus(Request $request, Delegate $delegate, DelegateTrip $trip)
    {
        if ($trip->delegate_id !== $delegate->id) { abort(403); }
        $data = $request->validate(['status' => 'required|in:pending,paid']);

        $executor = function () use ($trip, $data) {
            $trip->update($data);
            return $trip;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\DelegateTrip::class,
            $trip->id,
            'update',
            array_merge($data, [
                'delegate_name' => $delegate->name,
                'trip_date' => $trip->date?->format('Y-m-d'),
                'trip_description' => $trip->description,
                'trip_cost' => $trip->cost
            ]),
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تحديث حالة المشوار للإدارة.');
        }

        return back()->with('success', 'تم تحديث حالة المشوار');
    }
}
