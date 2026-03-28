<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\TravelRoute;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Collection;

final readonly class TravelRouteRepository
{
    public function paginateFiltered(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        $q            = $filters['q'] ?? '';
        $minCities    = $filters['min_cities'] ?? null;
        $maxCities    = $filters['max_cities'] ?? null;
        $hasDelegates = $filters['has_delegates'] ?? null;
        $hasDonations = $filters['has_donations'] ?? null;
        $sort         = $filters['sort'] ?? 'name';
        $dir          = $filters['dir'] ?? 'asc';

        return TravelRoute::query()
            ->select('travel_routes.*')
            ->when($q !== '', fn($qb) => $qb->where('name', 'like', "%$q%"))
            ->when($minCities !== null && $minCities !== '', fn($qb) => $qb->whereRaw('JSON_LENGTH(cities) >= ?', [(int) $minCities]))
            ->when($maxCities !== null && $maxCities !== '', fn($qb) => $qb->whereRaw('JSON_LENGTH(cities) <= ?', [(int) $maxCities]))
            ->when($hasDelegates !== null && $hasDelegates !== '', function($qb) use ($hasDelegates){
                if ((string) $hasDelegates === '1') { $qb->has('delegates'); }
                if ((string) $hasDelegates === '0') { $qb->doesntHave('delegates'); }
            })
            ->when($hasDonations !== null && $hasDonations !== '', function($qb) use ($hasDonations){
                if ((string) $hasDonations === '1') { $qb->has('donations'); }
                if ((string) $hasDonations === '0') { $qb->doesntHave('donations'); }
            })
            ->withCount(['delegates', 'donations'])
            ->selectSub(
                DB::table('donations')
                    ->selectRaw("SUM(CASE WHEN type='cash' THEN COALESCE(amount,0) ELSE COALESCE(estimated_value,0) END)")
                    ->whereColumn('donations.route_id', 'travel_routes.id'),
                'donation_total'
            )
            ->when($sort === 'name', fn($base) => $base->orderBy('name', $dir))
            ->when($sort === 'cities_count', fn($base) => $base->orderByRaw('JSON_LENGTH(cities) ' . $dir))
            ->when($sort === 'delegates_count', fn($base) => $base->orderBy('delegates_count', $dir))
            ->when($sort === 'donations_count', fn($base) => $base->orderBy('donations_count', $dir))
            ->when($sort === 'donation_total', fn($base) => $base->orderBy('donation_total', $dir))
            ->paginate($perPage);
    }

    public function findById(int $id): ?TravelRoute
    {
        return TravelRoute::with(['delegates'])->find($id);
    }

    public function create(array $data): TravelRoute
    {
        return TravelRoute::create($data);
    }

    public function update(TravelRoute $route, array $data): bool
    {
        return $route->update($data);
    }

    public function delete(TravelRoute $route): bool
    {
        return $route->delete();
    }
}
