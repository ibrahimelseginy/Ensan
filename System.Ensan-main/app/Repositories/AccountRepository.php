<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Account;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class AccountRepository
{
    public function paginate(int $perPage = 50): LengthAwarePaginator
    {
        return Account::paginate($perPage);
    }

    public function create(array $data): Account
    {
        return Account::create($data);
    }

    public function findById(int $id): ?Account
    {
        return Account::find($id);
    }

    public function update(Account $account, array $data): bool
    {
        return $account->update($data);
    }

    public function delete(Account $account): bool
    {
        return $account->delete();
    }
}
