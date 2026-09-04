<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\FinancialClosure;
use Illuminate\Pagination\LengthAwarePaginator;

final readonly class FinancialClosureRepository
{
    public function paginate(int $perPage = 20): LengthAwarePaginator
    {
        return FinancialClosure::orderByDesc('date')->paginate($perPage);
    }

    public function findById(int $id): ?FinancialClosure
    {
        return FinancialClosure::find($id);
    }

    public function create(array $data): FinancialClosure
    {
        return FinancialClosure::create($data);
    }

    public function update(FinancialClosure $closure, array $data): bool
    {
        return $closure->update($data);
    }
}
