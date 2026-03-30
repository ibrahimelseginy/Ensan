<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\TravelRoute;
use App\Models\Donation;
use App\Models\ChangeRequest;
use App\Services\TravelRouteService;
use App\Http\Requests\StoreTravelRouteRequest;
use App\Http\Requests\UpdateTravelRouteRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

final class TravelRouteWebController extends Controller
{
    public function __construct(
        private TravelRouteService $travelRouteService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['q', 'min_cities', 'max_cities', 'has_delegates', 'has_donations', 'sort', 'dir', 'per_page']);
        $routes  = $this->travelRouteService->getFilteredRoutes($filters, (int)($filters['per_page'] ?? 12));

        return view('routes.index', array_merge(compact('routes'), $filters));
    }

    public function export(Request $request): Response
    {
        return $this->travelRouteService->exportToCsv($request->all());
    }

    public function create(): View
    {
        return view('routes.create');
    }

    public function store(StoreTravelRouteRequest $request): RedirectResponse
    {
        $result = $this->travelRouteService->createRoute($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('travel-routes.index')->with('success', 'تم إرسال طلب إنشاء خط السير للإدارة.');
        }

        return redirect()->route('travel-routes.index')->with('success', 'تم إنشاء خط السير بنجاح');
    }

    public function show(TravelRoute $travel_route): View
    {
        $cities    = is_array($travel_route->cities) ? $travel_route->cities : [];
        $delegates = $travel_route->delegates()->orderBy('name')->get();
        $trips     = Donation::with(['donor', 'delegate'])
            ->where('route_id', $travel_route->id)
            ->orderByDesc('received_at')->orderByDesc('id')
            ->paginate(10);

        return view('routes.show', [
            'route'        => $travel_route,
            'travel_route' => $travel_route,
            'cities'       => $cities,
            'delegates'    => $delegates,
            'trips'        => $trips,
        ]);
    }

    public function edit(TravelRoute $travel_route): View
    {
        return view('routes.edit', ['route' => $travel_route]);
    }

    public function update(UpdateTravelRouteRequest $request, TravelRoute $travel_route): RedirectResponse
    {
        $result = $this->travelRouteService->updateRoute($travel_route, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('travel-routes.show', $travel_route)->with('success', 'تم إرسال طلب تعديل خط السير للإدارة.');
        }

        return redirect()->route('travel-routes.show', $travel_route)->with('success', 'تم تعديل خط السير بنجاح');
    }

    public function destroy(TravelRoute $travel_route): RedirectResponse
    {
        $result = $this->travelRouteService->deleteRoute($travel_route);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('travel-routes.index')->with('success', 'تم إرسال طلب حذف خط السير للإدارة.');
        }

        return redirect()->route('travel-routes.index')->with('success', 'تم حذف خط السير بنجاح');
    }

    public function duplicate(Request $request, TravelRoute $travel_route): RedirectResponse
    {
        $suffix = (string) ($request->input('suffix') ?? 'نسخة');
        $this->travelRouteService->duplicateRoute($travel_route, $suffix);
        return redirect()->route('travel-routes.index');
    }
}
