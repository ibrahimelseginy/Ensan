<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Warehouse;
use App\Models\InventoryTransaction;
use App\Models\ChangeRequest;
use App\Repositories\WarehouseRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class WarehouseService
{
    public function __construct(
        private WarehouseRepository $warehouseRepository
    ) {}

    public function getAllWarehouses(int $perPage = 20): LengthAwarePaginator
    {
        $warehouses = $this->warehouseRepository->paginate($perPage);

        $warehouses->each(function(Warehouse $warehouse) {
            $warehouse->pendingRequest = ChangeRequest::where('model_type', Warehouse::class)
                ->where('model_id', $warehouse->id)
                ->where('status', 'pending')
                ->first();
        });

        return $warehouses;
    }

    public function findWarehouseById(int $id): ?Warehouse
    {
        return $this->warehouseRepository->findById($id);
    }

    public function createWarehouse(array $data): mixed
    {
        $executor = fn() => $this->warehouseRepository->create($data);

        return ChangeRequestService::handleRequest(
            Warehouse::class,
            null,
            'create',
            $data,
            $executor,
            false // Optional Change Request for create
        );
    }

    public function updateWarehouse(Warehouse $warehouse, array $data): mixed
    {
        $executor = function () use ($warehouse, $data) {
            $this->warehouseRepository->update($warehouse, $data);
            return $warehouse;
        };

        return ChangeRequestService::handleRequest(
            Warehouse::class,
            $warehouse->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteWarehouse(Warehouse $warehouse): mixed
    {
        $executor = function () use ($warehouse) {
            return $this->warehouseRepository->delete($warehouse);
        };

        return ChangeRequestService::handleRequest(
            Warehouse::class,
            $warehouse->id,
            'delete',
            ['note' => 'حذف مخزن'],
            $executor,
            true
        );
    }

    public function getStock(int $warehouseId): Collection
    {
        return $this->warehouseRepository->getStock($warehouseId);
    }

    public function getRecentTransactions(Warehouse $warehouse, int $limit = 10): Collection
    {
        return $this->warehouseRepository->getRecentTransactions($warehouse, $limit);
    }

    public function getGlobalStats(): array
    {
        return [
            'total'              => Warehouse::count(),
            'total_transactions' => InventoryTransaction::count(),
            'active_items'       => InventoryTransaction::distinct('item_id')->count('item_id')
        ];
    }
}
