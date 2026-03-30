<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ChangeRequest;
use App\Services\SupplierService;
use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class SupplierWebController extends Controller
{
    public function __construct(
        private SupplierService $supplierService
    ) {}

    public function index(Request $request): View
    {
        $filters   = $request->only(['q']);
        $suppliers = $this->supplierService->getFilteredSuppliers($filters, 20);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $result = $this->supplierService->createSupplier($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('suppliers.index')->with('success', 'تم إرسال طلب إضافة المورد للمراجعة');
        }

        return redirect()->route('suppliers.index')->with('success', 'تم إضافة المورد بنجاح');
    }

    public function show(Supplier $supplier): View|RedirectResponse
    {
        if ($this->hasPendingRequest($supplier)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المورد لديه طلب مراجعة حالياً');
        }

        $purchases = $supplier->purchases()->latest('purchase_date')->paginate(20);
        return view('suppliers.show', compact('supplier', 'purchases'));
    }

    public function edit(Supplier $supplier): View|RedirectResponse
    {
        if ($this->hasPendingRequest($supplier)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المورد لديه طلب مراجعة حالياً');
        }

        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        if ($this->hasPendingRequest($supplier)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المورد لديه طلب مراجعة حالياً');
        }

        $result = $this->supplierService->updateSupplier($supplier, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('suppliers.index')->with('success', 'تم إرسال طلب تحديث بيانات المورد للمراجعة');
        }

        return redirect()->route('suppliers.index')->with('success', 'تم تحديث بيانات المورد');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        if ($this->hasPendingRequest($supplier)) {
            return redirect()->route('change-requests.index')->with('info', 'هذا المورد لديه طلب مراجعة حالياً');
        }

        $result = $this->supplierService->deleteSupplier($supplier);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('suppliers.index')->with('success', 'تم إرسال طلب حذف المورد للمراجعة');
        }

        return redirect()->route('suppliers.index')->with('success', 'تم حذف المورد');
    }

    private function hasPendingRequest(Supplier $supplier): bool
    {
        return ChangeRequest::where('model_type', Supplier::class)
            ->where('model_id', $supplier->id)
            ->where('status', 'pending')
            ->exists();
    }
}
