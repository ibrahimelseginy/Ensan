<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Warehouse;
use App\Models\ChangeRequest;
use App\Services\WarehouseService;
use App\Http\Requests\StoreWarehouseRequest;
use App\Http\Requests\UpdateWarehouseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class WarehouseWebController extends Controller
{
    public function __construct(
        private WarehouseService $warehouseService
    ) {}

    public function index(): View
    {
        $warehouses = $this->warehouseService->getAllWarehouses(20);
        $stats      = $this->warehouseService->getGlobalStats();

        return view('warehouses.index', compact('warehouses', 'stats'));
    }

    public function create(): View
    {
        return view('warehouses.create');
    }

    public function store(StoreWarehouseRequest $request): RedirectResponse
    {
        $result = $this->warehouseService->createWarehouse($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة المخزن للموافقة.');
        }

        return redirect()->route('warehouses.show', $result);
    }

    public function show(Warehouse $warehouse): View|RedirectResponse
    {
        if ($this->hasPendingRequest($warehouse)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        $stock               = $this->warehouseService->getStock($warehouse->id);
        $recent_transactions = $this->warehouseService->getRecentTransactions($warehouse, 10);

        return view('warehouses.show', compact('warehouse', 'stock', 'recent_transactions'));
    }

    public function edit(Warehouse $warehouse): View|RedirectResponse
    {
        if ($this->hasPendingRequest($warehouse)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        return view('warehouses.edit', compact('warehouse'));
    }

    public function update(UpdateWarehouseRequest $request, Warehouse $warehouse): RedirectResponse
    {
        if ($this->hasPendingRequest($warehouse)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        $result = $this->warehouseService->updateWarehouse($warehouse, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المخزن للموافقة');
        }

        return redirect()->route('warehouses.show', $warehouse)->with('success', 'تم تحديث المخزن بنجاح');
    }

    public function destroy(Warehouse $warehouse): RedirectResponse
    {
        if ($this->hasPendingRequest($warehouse)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المخزن لديه طلب مراجعة حالياً');
        }

        $result = $this->warehouseService->deleteWarehouse($warehouse);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف المخزن للموافقة');
        }

        return redirect()->route('warehouses.index')->with('success', 'تم حذف المخزن بنجاح');
    }

    private function hasPendingRequest(Warehouse $warehouse): bool
    {
        return ChangeRequest::where('model_type', Warehouse::class)
            ->where('model_id', $warehouse->id)
            ->where('status', 'pending')
            ->exists();
    }
}
