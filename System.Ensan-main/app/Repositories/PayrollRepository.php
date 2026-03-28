<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Payroll;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class PayrollRepository
{
    public function paginate(int $perPage = 50): LengthAwarePaginator
    {
        return Payroll::with(['user', 'journalEntry'])->orderByDesc('id')->paginate($perPage);
    }

    public function findById(int $id): ?Payroll
    {
        return Payroll::with(['user', 'journalEntry.lines.account'])->find($id);
    }

    public function create(array $data): Payroll
    {
        return Payroll::create($data);
    }

    public function update(Payroll $payroll, array $data): bool
    {
        return $payroll->update($data);
    }

    public function delete(Payroll $payroll): bool
    {
        return $payroll->delete();
    }
}
