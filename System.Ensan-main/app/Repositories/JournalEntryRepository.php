<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class JournalEntryRepository
{
    public function paginateAll(int $perPage = 20): LengthAwarePaginator
    {
        return JournalEntry::with(['lines.account'])
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate($perPage);
    }

    public function findById(int $id): ?JournalEntry
    {
        return JournalEntry::with(['lines.account'])->find($id);
    }

    public function create(array $data): JournalEntry
    {
        return JournalEntry::create($data);
    }

    public function update(JournalEntry $entry, array $data): bool
    {
        return $entry->update($data);
    }

    public function delete(JournalEntry $entry): bool
    {
        return $entry->delete();
    }

    public function createLine(array $data): JournalEntryLine
    {
        return JournalEntryLine::create($data);
    }

    public function deleteLines(JournalEntry $entry): void
    {
        $entry->lines()->delete();
    }
}
