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
        $executor = function() use ($data) {
            return DB::transaction(function() use ($data) {
                $lines = $data['lines'];
                unset($data['lines']);

                $entry = $this->journalEntryRepository->create($data);

                foreach ($lines as $line) {
                    $this->journalEntryRepository->createLine([
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $line['account_id'],
                        'debit'            => $line['debit'] ?? 0,
                        'credit'           => $line['credit'] ?? 0,
                    ]);
                }
                return $entry;
            });
        };

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

        $executor = function () use ($entry, $data) {
            return DB::transaction(function() use ($entry, $data) {
                $lines = $data['lines'];
                unset($data['lines']);

                $this->journalEntryRepository->update($entry, $data);
                $this->journalEntryRepository->deleteLines($entry);

                foreach ($lines as $line) {
                    $this->journalEntryRepository->createLine([
                        'journal_entry_id' => $entry->id,
                        'account_id'       => $line['account_id'],
                        'debit'            => $line['debit'] ?? 0,
                        'credit'           => $line['credit'] ?? 0,
                    ]);
                }

                return $entry;
            });
        };

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

        $executor = function () use ($entry) {
            return $this->journalEntryRepository->delete($entry);
        };

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
