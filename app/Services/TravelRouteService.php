<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\TravelRoute;
use App\Models\ChangeRequest;
use App\Repositories\TravelRouteRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

final readonly class TravelRouteService
{
    public function __construct(
        private TravelRouteRepository $travelRouteRepository
    ) {}

    public function getFilteredRoutes(array $filters, int $perPage = 12): LengthAwarePaginator
    {
        return $this->travelRouteRepository->paginateFiltered($filters, $perPage);
    }

    public function findRouteById(int $id): ?TravelRoute
    {
        return $this->travelRouteRepository->findById($id);
    }

    public function createRoute(array $data): mixed
    {
        $executor = fn() => $this->travelRouteRepository->create($data);

        return ChangeRequestService::handleRequest(
            TravelRoute::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateRoute(TravelRoute $route, array $data): mixed
    {
        $executor = function () use ($route, $data) {
            $this->travelRouteRepository->update($route, $data);
            return $route;
        };

        return ChangeRequestService::handleRequest(
            TravelRoute::class,
            $route->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteRoute(TravelRoute $route): mixed
    {
        $executor = fn() => $this->travelRouteRepository->delete($route);

        return ChangeRequestService::handleRequest(
            TravelRoute::class,
            $route->id,
            'delete',
            [],
            $executor,
            true
        );
    }

    public function duplicateRoute(TravelRoute $route, string $suffix = 'نسخة'): TravelRoute
    {
        $new = new TravelRoute();
        $new->name        = trim(($route->name ?? '') . ' ' . $suffix);
        $new->description = $route->description;
        $new->cities      = $route->cities;
        $new->save();

        return $new;
    }

    public function exportToCsv(array $filters): Response
    {
        $filters['per_page'] = 2000;
        $rows = $this->travelRouteRepository->paginateFiltered($filters, 2000);

        $cols     = ['id', 'name', 'description', 'cities_count', 'delegates_count', 'donations_count', 'donation_total'];
        $filename = 'travel_routes_' . now()->format('Ymd_His') . '.csv';
        $headers  = [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"'
        ];

        $content = "\xEF\xBB\xBF";
        $content .= implode(',', $cols) . "\n";

        foreach ($rows as $r) {
            $citiesCount = is_array($r->cities ?? null) ? count($r->cities) : 0;
            $line = [
                $r->id,
                str_replace(["\r", "\n"], ' ', (string)$r->name),
                str_replace(["\r", "\n"], ' ', (string)($r->description ?? '')),
                $citiesCount,
                $r->delegates_count,
                $r->donations_count,
                number_format((float)($r->donation_total ?? 0), 2, '.', '')
            ];
            $content .= implode(',', array_map(fn($v) => (string)$v, $line)) . "\n";
        }

        return response($content, 200, $headers);
    }
}
