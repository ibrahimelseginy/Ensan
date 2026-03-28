<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Expense;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class ExpenseRepository
{
    public function paginateFiltered(array $filters, int $perPage = 20): LengthAwarePaginator
    {
        return Expense::with(['beneficiary', 'project', 'campaign', 'workspace'])
            ->when($filters['project_id'] ?? null, fn($q, $id) => $q->where('project_id', $id))
            ->when($filters['campaign_id'] ?? null, fn($q, $id) => $q->where('campaign_id', $id))
            ->when($filters['workspace_id'] ?? null, fn($q, $id) => $q->where('workspace_id', $id))
            ->when($filters['guest_house_id'] ?? null, fn($q, $id) => $q->where('guest_house_id', $id))
            ->when($filters['type'] ?? null, fn($q, $type) => $q->where('type', $type))
            ->when($filters['start_date'] ?? null, fn($q, $date) => $q->whereDate('created_at', '>=', $date))
            ->when($filters['end_date'] ?? null, fn($q, $date) => $q->whereDate('created_at', '<=', $date))
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?Expense
    {
        return Expense::with(['beneficiary', 'project', 'campaign', 'workspace'])->find($id);
    }

    public function create(array $data): Expense
    {
        return Expense::create($data);
    }

    public function update(Expense $expense, array $data): bool
    {
        return $expense->update($data);
    }

    public function delete(Expense $expense): bool
    {
        return $expense->delete();
    }
}
