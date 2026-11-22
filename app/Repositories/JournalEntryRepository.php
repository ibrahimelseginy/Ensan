<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

final readonly class JournalEntryRepository
{
    public function paginateAll(int $perPage = 20, ?\App\Models\User $user = null): LengthAwarePaginator
    {
        $query = JournalEntry::with(['lines.account'])
            ->orderByDesc('date')
            ->orderByDesc('id');

        if ($user && !$user->isSuperAdmin() && !in_array((int)$user->id, [2, 8, 50], true) && !$user->hasRole(['admin', 'super_admin', 'general_manager'])) {
            $dept = trim((string) $user->department);
            $ghIds = $user->getAccessibleGuestHouseIds();
            $query->where(function($q) use ($user, $dept, $ghIds) {
                $q->where('created_by', $user->id);
                if (!empty($dept)) {
                    $q->orWhere('branch', 'like', '%' . $dept . '%');
                }
                if ($ghIds !== null && in_array(2, $ghIds, true)) {
                    $q->orWhere('branch', 'like', '%كفر%الشيخ%');
                }
                if ($ghIds !== null && in_array(1, $ghIds, true)) {
                    $q->orWhere('branch', 'like', '%الغربيه%')->orWhere('branch', 'like', '%الغربية%');
                }
            });
        }

        return $query->paginate($perPage);
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
