<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use App\Models\ChangeRequest;
use App\Services\PurchaseService;
use App\Http\Requests\StorePurchaseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

final readonly class PurchaseWebController extends Controller
{
    public function __construct(
        private PurchaseService $purchaseService
    ) {}

    public function store(StorePurchaseRequest $request, Supplier $supplier): RedirectResponse
    {
        $result = $this->purchaseService->createPurchase($request->validated(), (int)$supplier->id);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تسجيل المشتريات للموافقة');
        }

        return redirect()->route('suppliers.show', $supplier)->with('success', 'تم تسجيل المشتريات بنجاح');
    }

    public function destroy(Supplier $supplier, Purchase $purchase): RedirectResponse
    {
        $result = $this->purchaseService->deletePurchase($purchase);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف السجل للموافقة');
        }

        return back()->with('success', 'تم حذف السجل');
    }
}
