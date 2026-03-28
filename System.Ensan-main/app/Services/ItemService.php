<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Item;
use App\Models\ChangeRequest;
use App\Repositories\ItemRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class ItemService
{
    public function __construct(
        private ItemRepository $itemRepository
    ) {}

    public function getFilteredItems(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        $items = $this->itemRepository->paginateFiltered($filters, $perPage);

        $items->each(function(Item $item) {
            $item->pendingRequest = ChangeRequest::where('model_type', Item::class)
                ->where('model_id', $item->id)
                ->where('status', 'pending')
                ->first();
        });

        return $items;
    }

    public function findItemById(int $id): ?Item
    {
        return $this->itemRepository->findById($id);
    }

    public function createItem(array $data): mixed
    {
        $executor = fn() => $this->itemRepository->create($data);

        return ChangeRequestService::handleRequest(
            Item::class,
            null,
            'create',
            $data,
            $executor
        );
    }

    public function updateItem(Item $item, array $data): mixed
    {
        $executor = function () use ($item, $data) {
            $this->itemRepository->update($item, $data);
            return $item;
        };

        return ChangeRequestService::handleRequest(
            Item::class,
            $item->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteItem(Item $item): mixed
    {
        $executor = function () use ($item) {
            return $this->itemRepository->delete($item);
        };

        return ChangeRequestService::handleRequest(
            Item::class,
            $item->id,
            'delete',
            ['note' => 'حذف صنف'],
            $executor,
            true
        );
    }

    public function getStockByWarehouse(int $itemId): Collection
    {
        return $this->itemRepository->getStockByWarehouse($itemId);
    }

    public function getRecentTransactions(Item $item, int $limit = 10): Collection
    {
        return $this->itemRepository->getRecentTransactions($item, $limit);
    }

    public function getGlobalStats(): array
    {
        return [
            'total'      => Item::count(),
            'with_value' => Item::whereNotNull('estimated_value')->where('estimated_value', '>', 0)->count(),
        ];
    }
}
