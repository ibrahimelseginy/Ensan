<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Supplier;
use App\Models\ChangeRequest;
use App\Repositories\SupplierRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class SupplierService
{
    public function __construct(
        private SupplierRepository $supplierRepository
    ) {}

    public function getFilteredSuppliers(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $suppliers = $this->supplierRepository->paginateFiltered($filters, $perPage);

        $suppliers->each(function(Supplier $supplier) {
            $supplier->pendingRequest = ChangeRequest::where('model_type', Supplier::class)
                ->where('model_id', $supplier->id)
                ->where('status', 'pending')
                ->first();
        });

        return $suppliers;
    }

    public function findSupplierById(int $id): ?Supplier
    {
        return $this->supplierRepository->findById($id);
    }

    public function createSupplier(array $data): mixed
    {
        $executor = fn() => $this->supplierRepository->create($data);

        return ChangeRequestService::handleRequest(
            Supplier::class,
            null,
            'create',
            $data,
            $executor,
            true
        );
    }

    public function updateSupplier(Supplier $supplier, array $data): mixed
    {
        $executor = function () use ($supplier, $data) {
            $this->supplierRepository->update($supplier, $data);
            return $supplier;
        };

        return ChangeRequestService::handleRequest(
            Supplier::class,
            $supplier->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteSupplier(Supplier $supplier): mixed
    {
        $executor = function () use ($supplier) {
            return $this->supplierRepository->delete($supplier);
        };

        return ChangeRequestService::handleRequest(
            Supplier::class,
            $supplier->id,
            'delete',
            ['name' => $supplier->name],
            $executor,
            true
        );
    }
}
