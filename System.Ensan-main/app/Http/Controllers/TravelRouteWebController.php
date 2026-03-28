<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\TravelRoute;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Http\Request;

final class TravelRouteWebController extends Controller
{
    public function index() {
        $q = trim((string) request()->input('q'));
        $minCities = request()->input('min_cities');
        $maxCities = request()->input('max_cities');
        $hasDelegates = request()->input('has_delegates');
        $hasDonations = request()->input('has_donations');
        $sort = request()->input('sort', 'name');
        $dir = request()->input('dir', 'asc');
        $perPage = (int) (request()->input('per_page') ?? 12);

        $base = TravelRoute::query()
            ->select('travel_routes.*')
            ->when($q !== '', function($qb) use ($q){ $qb->where('name','like',"%$q%"); })
            ->when($minCities !== null && $minCities !== '', function($qb) use ($minCities){ $qb->whereRaw('JSON_LENGTH(cities) >= ?', [(int) $minCities]); })
            ->when($maxCities !== null && $maxCities !== '', function($qb) use ($maxCities){ $qb->whereRaw('JSON_LENGTH(cities) <= ?', [(int) $maxCities]); })
            ->when($hasDelegates !== null && $hasDelegates !== '', function($qb) use ($hasDelegates){
                if ((string) $hasDelegates === '1') { $qb->has('delegates'); }
                if ((string) $hasDelegates === '0') { $qb->doesntHave('delegates'); }
            })
            ->when($hasDonations !== null && $hasDonations !== '', function($qb) use ($hasDonations){
                if ((string) $hasDonations === '1') { $qb->has('donations'); }
                if ((string) $hasDonations === '0') { $qb->doesntHave('donations'); }
            })
            ->withCount(['delegates','donations'])
            ->selectSub(
                DB::table('donations')
                    ->selectRaw("SUM(CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END)")
                    ->whereColumn('donations.route_id','travel_routes.id'),
                'donation_total'
            );

        if ($sort === 'name') { $base->orderBy('name', $dir === 'desc' ? 'desc' : 'asc'); }
        if ($sort === 'cities_count') { $base->orderByRaw('JSON_LENGTH(cities) ' . ($dir === 'desc' ? 'DESC' : 'ASC')); }
        if ($sort === 'delegates_count') { $base->orderBy('delegates_count', $dir === 'desc' ? 'desc' : 'asc'); }
        if ($sort === 'donations_count') { $base->orderBy('donations_count', $dir === 'desc' ? 'desc' : 'asc'); }
        if ($sort === 'donation_total') { $base->orderBy('donation_total', $dir === 'desc' ? 'desc' : 'asc'); }

        $routes = $base->paginate(max($perPage,1))->withQueryString();
        return view('routes.index', compact('routes','q','minCities','maxCities','hasDelegates','hasDonations','sort','dir','perPage'));
    }
    public function create() { return view('routes.create'); }
    public function store(Request $request)
    {
        $data = $request->validate(['name' => 'required|string', 'description' => 'nullable|string']);

        $executor = function () use ($data) {
            return TravelRoute::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\TravelRoute::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('travel-routes.index')->with('success', 'تم إرسال طلب إنشاء خط السير للإدارة.');
        }

        return redirect()->route('travel-routes.index')->with('success', 'تم إنشاء خط السير بنجاح');
    }

    public function show(TravelRoute $travel_route)
    {
        $cities = is_array($travel_route->cities ?? null) ? $travel_route->cities : [];
        $delegates = $travel_route->delegates()->orderBy('name')->get();
        $trips = \App\Models\Donation::with(['donor', 'delegate'])
            ->where('route_id', $travel_route->id)
            ->orderByDesc('received_at')->orderByDesc('id')
            ->paginate(10);
        return view('routes.show', [
            'route' => $travel_route,
            'travel_route' => $travel_route,
            'cities' => $cities,
            'delegates' => $delegates,
            'trips' => $trips,
        ]);
    }

    public function edit(TravelRoute $travel_route) { return view('routes.edit', ['route' => $travel_route]); }

    public function update(Request $request, TravelRoute $travel_route)
    {
        $data = $request->validate(['name' => 'sometimes|string', 'description' => 'nullable|string']);

        $executor = function () use ($travel_route, $data) {
            $travel_route->update($data);
            return $travel_route;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\TravelRoute::class,
            $travel_route->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('travel-routes.show', $travel_route)->with('success', 'تم إرسال طلب تعديل خط السير للإدارة.');
        }

        return redirect()->route('travel-routes.show', $travel_route)->with('success', 'تم تعديل خط السير بنجاح');
    }

    // ... existing addCity/addTrip methods ...

    public function destroy(TravelRoute $travel_route)
    {
        $executor = function () use ($travel_route) {
            $travel_route->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\TravelRoute::class,
            $travel_route->id,
            'delete',
            [],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('travel-routes.index')->with('success', 'تم إرسال طلب حذف خط السير للإدارة.');
        }

        return redirect()->route('travel-routes.index')->with('success', 'تم حذف خط السير بنجاح');
    }

    public function export(Request $request)
    {
        $q = trim((string) $request->input('q'));
        $rows = TravelRoute::query()
            ->select('travel_routes.*')
            ->when($q !== '', function($qb) use ($q){ $qb->where('name','like',"%$q%"); })
            ->withCount(['delegates','donations'])
            ->selectSub(
                DB::table('donations')
                    ->selectRaw("SUM(CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END)")
                    ->whereColumn('donations.route_id','travel_routes.id'),
                'donation_total'
            )
            ->orderBy('name')
            ->limit(2000)
            ->get();

        $cols = ['id','name','description','cities_count','delegates_count','donations_count','donation_total'];
        $filename = 'travel_routes_'.now()->format('Ymd_His').'.csv';
        $headers = ['Content-Type' => 'text/csv; charset=UTF-8','Content-Disposition' => 'attachment; filename="'.$filename.'"'];
        $content = "\xEF\xBB\xBF";
        $content .= implode(',', $cols)."\n";
        foreach ($rows as $r) {
            $citiesCount = is_array($r->cities ?? null) ? count($r->cities) : (int) (is_string($r->cities ?? null) ? 0 : 0);
            $line = [
                $r->id,
                str_replace(["\r","\n"],' ', (string) $r->name),
                str_replace(["\r","\n"],' ', (string) ($r->description ?? '')),
                $citiesCount,
                $r->delegates_count,
                $r->donations_count,
                number_format((float) ($r->donation_total ?? 0), 2, '.', '')
            ];
            $content .= implode(',', array_map(function($v){ return (string) $v; }, $line))."\n";
        }
        return response($content, 200, $headers);
    }

    public function duplicate(Request $request, TravelRoute $travel_route)
    {
        $nameSuffix = trim((string) ($request->input('suffix') ?? 'نسخة'));
        $new = new TravelRoute();
        $new->name = trim(($travel_route->name ?? '').' '.$nameSuffix);
        $new->description = $travel_route->description;
        $new->cities = $travel_route->cities;
        $new->save();
        return redirect()->route('travel-routes.index');
    }
}
