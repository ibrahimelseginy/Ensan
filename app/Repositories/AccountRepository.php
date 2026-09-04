<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class AccountRepository
{
    public function getRootAccounts(): Collection
    {
        return Account::whereNull('parent_id')
            ->with(['children'])
            ->orderBy('type')
            ->orderBy('code')
            ->get();
    }

    public function paginateAll(int $perPage = 50): LengthAwarePaginator
    {
        return Account::with(['parent'])
            ->orderBy('type')
            ->orderBy('code')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Account
    {
        return Account::with(['parent', 'children'])->find($id);
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function update(Account $account, array $data): bool
    {
        return $account->update($data);
    }

    public function delete(Account $account): bool
    {
        return $account->delete();
    }

    public function getAllOrdered(): Collection
    {
        return Account::orderBy('code')->get();
    }
}
