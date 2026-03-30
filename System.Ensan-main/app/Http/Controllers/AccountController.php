<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Account;
use App\Services\AccountService;
use App\Http\Requests\StoreAccountRequest;
use App\Http\Requests\UpdateAccountRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class AccountController extends Controller
{
    public function __construct(
        private AccountService $accountService
    ) {}

    public function index(): LengthAwarePaginator
    {
        return $this->accountService->getAllAccounts(50);
    }

    public function store(StoreAccountRequest $request): Account
    {
        return $this->accountService.createAccount($request->validated());
    }

    public function show(Account $account): Account
    {
        return $account;
    }

    public function update(UpdateAccountRequest $request, Account $account): Account
    {
        return $this->accountService.updateAccount($account, $request->validated());
    }

    public function destroy(Account $account): Response
    {
        $this->accountService.deleteAccount($account);
        return response()->noContent();
    }
}
