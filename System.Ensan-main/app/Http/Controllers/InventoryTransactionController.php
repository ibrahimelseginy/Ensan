<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\InventoryTransaction;
use App\Models\ChangeRequest;
use App\Services\InventoryTransactionService;
use App\Http\Requests\StoreInventoryTransactionRequest;
use App\Http\Requests\UpdateInventoryTransactionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final readonly class InventoryTransactionController extends Controller
{
    public function __construct(
        private InventoryTransactionService $transactionService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $transactions = $this->transactionService->getFilteredTransactions($request->all(), 20);
        return response()->json($transactions);
    }

    public function store(StoreInventoryTransactionRequest $request): JsonResponse
    {
        try {
            $result = $this->transactionService->createTransaction($request->validated());

            if ($result instanceof ChangeRequest) {
                return response()->json(['message' => 'تم إرسال طلب إضافة الحركة للموافقة', 'change_request_id' => $result->id], 202);
            }

            return response()->json(['message' => 'تم إضافة الحركة بنجاح', 'data' => $result], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    public function show(InventoryTransaction $inventory_transaction): JsonResponse
    {
        return response()->json($inventory_transaction->load(['item', 'warehouse', 'beneficiary', 'project', 'campaign', 'sourceDonation']));
    }

    public function update(UpdateInventoryTransactionRequest $request, InventoryTransaction $inventory_transaction): JsonResponse
    {
        $result = $this->transactionService->updateTransaction($inventory_transaction, $request->validated());

        if ($result instanceof ChangeRequest) {
            return response()->json(['message' => 'تم إرسال طلب تعديل الحركة للمراجعة', 'change_request_id' => $result->id], 202);
        }

        return response()->json(['message' => 'تم تعديل الحركة بنجاح', 'data' => $result]);
    }

    public function destroy(InventoryTransaction $inventory_transaction): JsonResponse
    {
        $result = $this->transactionService->deleteTransaction($inventory_transaction);

        if ($result instanceof ChangeRequest) {
            return response()->json(['message' => 'تم إرسال طلب حذف الحركة للمراجعة', 'change_request_id' => $result->id], 202);
        }

        return response()->json(['message' => 'تم حذف الحركة بنجاح']);
    }
}
