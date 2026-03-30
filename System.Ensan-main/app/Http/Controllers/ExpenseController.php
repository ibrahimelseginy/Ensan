<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Services\ExpenseService;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService
    ) {}

    public function index(): LengthAwarePaginator
    {
        return $this->expenseService->getAllExpenses(20);
    }

    public function store(StoreExpenseRequest $request): Expense
    {
        $userId = $request->user()?->id;
        return $this->expenseService->createExpense($request->validated(), $userId);
    }

    public function show(Expense $expense): Expense
    {
        return $expense->load(['project', 'campaign', 'beneficiary', 'creator']);
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): Expense
    {
        return $this->expenseService->updateExpense($expense, $request->validated());
    }

    public function destroy(Expense $expense): Response
    {
        $this->expenseService->deleteExpense($expense);
        return response()->noContent();
    }
}
