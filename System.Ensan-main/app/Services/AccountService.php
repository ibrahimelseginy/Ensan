<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Account;
use App\Repositories\AccountRepository;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository
    ) {}

    public function getAllAccounts(int $perPage = 50): LengthAwarePaginator
    {
        return $this->accountRepository->paginate($perPage);
    }

    public function createAccount(array $data): Account
    {
        return $this->accountRepository->create($data);
    }

    public function getAccountById(int $id): ?Account
    {
        return $this->accountRepository->findById($id);
    }

    public function updateAccount(Account $account, array $data): Account
    {
        $this->accountRepository->update($account, $data);
        return $account->fresh();
    }

    public function deleteAccount(Account $account): bool
    {
        return $this->accountRepository->delete($account);
    }
}
