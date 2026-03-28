<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\ChangeRequest;
use App\Repositories\PurchaseRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class PurchaseService
{
    public function __construct(
        private PurchaseRepository $purchaseRepository
    ) {}

    public function getSupplierPurchases(int $supplierId, int $perPage = 20): LengthAwarePaginator
    {
        return $this->purchaseRepository->paginateBySupplier($supplierId, $perPage);
    }

    public function findPurchaseById(int $id): ?Purchase
    {
        return $this->purchaseRepository->findById($id);
    }

    public function createPurchase(array $data, int $supplierId): mixed
    {
        $data['supplier_id']         = $supplierId;
        $data['discount_percentage'] = $data['discount_percentage'] ?? 0.0;

        $unitPrice           = (float) $data['original_price'];
        $qty                 = (int)   $data['quantity'];
        $discountPerc        = (float) $data['discount_percentage'];
        $totalBeforeDiscount = $unitPrice * $qty;
        $discountValue       = $totalBeforeDiscount * ($discountPerc / 100);
        
        $data['final_price'] = $totalBeforeDiscount - $discountValue;

        $executor = fn() => $this->purchaseRepository->create($data);

        return ChangeRequestService::handleRequest(
            Purchase::class,
            null,
            'create',
            $data,
            $executor,
            true
        );
    }

    public function deletePurchase(Purchase $purchase): mixed
    {
        $executor = function () use ($purchase) {
            return $this->purchaseRepository->delete($purchase);
        };

        return ChangeRequestService::handleRequest(
            Purchase::class,
            $purchase->id,
            'delete',
            ['item_name' => $purchase->item_name],
            $executor,
            true
        );
    }
}
