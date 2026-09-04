<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ChangeRequest;
use App\Services\ItemService;
use App\Http\Requests\StoreItemRequest;
use App\Http\Requests\UpdateItemRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class ItemWebController extends Controller
{
    public function __construct(
        private ItemService $itemService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['search']);
        $items   = $this->itemService->getFilteredItems($filters, 20);
        $stats   = $this->itemService->getGlobalStats();

        return view('items.index', compact('items', 'stats'));
    }

    public function create(): View
    {
        return view('items.create');
    }

    public function store(StoreItemRequest $request): RedirectResponse
    {
        $result = $this->itemService->createItem($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الصنف للموافقة.');
        }

        return redirect()->route('items.show', $result);
    }

    public function show(Item $item): View|RedirectResponse
    {
        if ($this->hasPendingRequest($item)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        $stock_by_warehouse  = $this->itemService->getStockByWarehouse($item->id);
        $recent_transactions = $this->itemService->getRecentTransactions($item, 10);

        return view('items.show', compact('item', 'stock_by_warehouse', 'recent_transactions'));
    }

    public function edit(Item $item): View|RedirectResponse
    {
        if ($this->hasPendingRequest($item)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        return view('items.edit', compact('item'));
    }

    public function update(UpdateItemRequest $request, Item $item): RedirectResponse
    {
        if ($this->hasPendingRequest($item)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        $result = $this->itemService->updateItem($item, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تحديث الصنف للمراجعة');
        }

        return redirect()->route('items.index')->with('success', 'تم تحديث بيانات الصنف');
    }

    public function destroy(Item $item): RedirectResponse
    {
        if ($this->hasPendingRequest($item)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا السجل لديه طلب مراجعة حالياً');
        }

        $result = $this->itemService->deleteItem($item);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الصنف للموافقة');
        }

        return redirect()->route('items.index')->with('success', 'تم حذف الصنف بنجاح');
    }

    private function hasPendingRequest(Item $item): bool
    {
        return ChangeRequest::where('model_type', Item::class)
            ->where('model_id', $item->id)
            ->where('status', 'pending')
            ->exists();
    }
}
