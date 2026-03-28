<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Treasury;
use Illuminate\Database\Eloquent\Collection;

final readonly class TreasuryRepository
{
    public function all(): Collection
    {
        return Treasury::with('manager')->withCount('transactions')->get();
    }

    public function findById(int $id): ?Treasury
    {
        return Treasury::with(['manager', 'transactions.createdBy', 'donations'])->find($id);
    }

    public function create(array $data): Treasury
    {
        return Treasury::create($data);
    }

    public function update(Treasury $treasury, array $data): bool
    {
        return $treasury->update($data);
    }

    public function delete(Treasury $treasury): bool
    {
        return $treasury->delete();
    }

    public function getActive(): Collection
    {
        return Treasury::where('is_active', true)->get();
    }
}
