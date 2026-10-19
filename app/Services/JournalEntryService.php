<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\ChangeRequest;
use App\Repositories\JournalEntryRepository;
use App\Services\ChangeRequestService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

final readonly class JournalEntryService
{
    public function __construct(
        private JournalEntryRepository $journalEntryRepository
    ) {}

    public function getAllEntries(int $perPage = 20): LengthAwarePaginator
    {
        return $this->journalEntryRepository->paginateAll($perPage);
    }

    public function findEntryById(int $id): ?JournalEntry
    {
        return $this->journalEntryRepository->findById($id);
    }

    public function createEntry(array $data): mixed
    {
        $executor = fn () => app(JournalEntryWriter::class)->execute('create', $data);

        return ChangeRequestService::handleRequest(
            JournalEntry::class,
            null,
            'create',
            $data,
            $executor,
            true
        );
    }

    public function updateEntry(JournalEntry $entry, array $data): mixed
    {
        if ($entry->locked) {
            throw new \Exception('هذا القيد مرحل ولا يمكن تعديله');
        }

        $executor = fn () => app(JournalEntryWriter::class)->execute('update', $data, $entry->id);

        return ChangeRequestService::handleRequest(
            JournalEntry::class,
            $entry->id,
            'update',
            $data,
            $executor,
            true
        );
    }

    public function deleteEntry(JournalEntry $entry): mixed
    {
        if ($entry->locked) {
            throw new \Exception('هذا القيد مرحل ولا يمكن حذفه');
        }

        $executor = fn () => app(JournalEntryWriter::class)->execute('delete', [], $entry->id);

        return ChangeRequestService::handleRequest(
            JournalEntry::class,
            $entry->id,
            'delete',
            [],
            $executor,
            true
        );
    }
}
