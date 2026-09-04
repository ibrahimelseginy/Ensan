<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Donation;
use App\Models\TravelRoute;
use App\Models\Delegate;
use App\Models\Warehouse;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\GuestHouse;
use App\Models\Beneficiary;
use App\Models\ChangeRequest;
use App\Services\TripService;
use App\Http\Requests\StoreTripRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class TripWebController extends Controller
{
    public function __construct(
        private TripService $tripService
    ) {}

    public function index(Request $request): View|Response
    {
        $filters = $request->only(['route_id', 'delegate_id', 'q', 'type', 'sort']);
        $perPage = (int) ($request->input('per_page') ?? 12);

        if ($request->input('export') === 'csv') {
            $trips = $this->tripService->getFilteredTrips($filters, 5000); // Higher limit for export
            return $this->tripService->exportCsv($trips->getCollection());
        }

        $trips       = $this->tripService->getFilteredTrips($filters, $perPage);
        $stats       = $this->tripService->getTripStats($filters);
        
        $routes      = TravelRoute::with(['delegates'])->orderBy('name')->get();
        $warehouses  = Warehouse::orderBy('name')->get();
        $projects    = Project::orderBy('name')->where('name', 'not like', '%دار الضيافة%')->get();
        $campaigns   = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $guestHouses = GuestHouse::orderBy('name')->get();
        $beneficiaries = Beneficiary::orderBy('full_name')->get(['id', 'full_name']);

        $routesPayload = $routes->map(fn($r) => [
            'id'        => $r->id,
            'name'      => $r->name,
            'cities'    => is_array($r->cities) ? $r->cities : [],
            'delegates' => $r->delegates->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values()->all(),
        ])->values();

        $delegatesAllPayload = Delegate::orderBy('name')->get()->map(fn($d) => ['id' => $d->id, 'name' => $d->name, 'phone' => $d->phone])->values();

        $printTrip = null;
        if ($request->input('print_id')) {
            $printTrip = $this->tripService->findTripById((int)$request->input('print_id'));
        }

        return view('trips.index', array_merge(
            compact('trips', 'stats', 'routes', 'routesPayload', 'delegatesAllPayload', 'warehouses', 'projects', 'campaigns', 'guestHouses', 'beneficiaries', 'printTrip'),
            $request->only(['route_id', 'delegate_id', 'q', 'type', 'per_page', 'sort'])
        ));
    }

    public function store(StoreTripRequest $request): RedirectResponse
    {
        try {
            $result = $this->tripService->createTrip($request->validated());

            if ($result instanceof ChangeRequest) {
                return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المشوار/التبرع للإدارة للموافقة.');
            }

            return redirect()->route('trips.index', ['print_id' => $result->id]);
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ المشوار: ' . $e->getMessage());
        }
    }
}
