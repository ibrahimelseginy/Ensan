<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

final class PurchaseWebController extends Controller
{
    public function store(Request $request, Supplier $supplier)
    {
        $data = $request->validate([
            'item_name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:1',
            'original_price' => 'required|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'purchase_date' => 'required|date',
            'notes' => 'nullable|string'
        ]);

        $data['supplier_id'] = $supplier->id;
        $data['discount_percentage'] = $data['discount_percentage'] ?? 0;

        // Calculate final price automatically
        $discountAmount = ($data['original_price'] * $data['quantity']) * ($data['discount_percentage'] / 100);
        $totalOriginal = $data['original_price'] * $data['quantity'];
        $data['final_price'] = $totalOriginal - $discountAmount;

        // However, user might want per item price logic.
        // Usually purchase records are line items. 
        // Let's assume price is PER ITEM in input, but we store TOTAL final price? 
        // Or store unit prices? Migration has 'original_price' (decimal).
        // Let's assume original_price is UNIT price.
        // And 'final_price' is total for that line item after discount?
        // Or 'final_price' is unit price after discount?
        // Let's stick to: original_price is UNIT price.
        // We will calculate final TOTAL for this line item.

        // Wait, looking at migration:
        // 'quantity' (int)
        // 'original_price' (decimal)
        // 'final_price' (decimal)
        // It's ambiguous. Let's assume final_price is the ACTUAL COST of this transaction row.

        $unitPrice = $data['original_price'];
        $qty = $data['quantity'];
        $discount = $data['discount_percentage'];

        $totalBeforeDiscount = $unitPrice * $qty;
        $discountValue = $totalBeforeDiscount * ($discount / 100);
        $data['final_price'] = $totalBeforeDiscount - $discountValue;

        $executor = function () use ($data) {
            return Purchase::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Purchase::class,
            null,
            'create',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تسجيل المشتريات للموافقة');
        }

        return redirect()->route('suppliers.show', $supplier)->with('success', 'تم تسجيل المشتريات بنجاح');
    }

    public function destroy(Supplier $supplier, Purchase $purchase)
    {
        $executor = function () use ($purchase) {
            $purchase->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Purchase::class,
            $purchase->id,
            'delete',
            ['item_name' => $purchase->item_name],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف السجل للموافقة');
        }

        return back()->with('success', 'تم حذف السجل');
    }
}
