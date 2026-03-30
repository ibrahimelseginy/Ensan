<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Services\JournalEntryService;
use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class JournalEntryController extends Controller
{
    public function __construct(
        private JournalEntryService $journalEntryService
    ) {}

    public function index(): LengthAwarePaginator
    {
        return $this->journalEntryService->getAllJournalEntries(20);
    }

    public function store(StoreJournalEntryRequest $request): JournalEntry
    {
        return $this->journalEntryService->createJournalEntry($request->validated());
    }

    public function show(JournalEntry $journalEntry): JournalEntry
    {
        return $journalEntry->load('lines');
    }

    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry): JournalEntry|JsonResponse
    {
        try {
            return $this->journalEntryService->updateJournalEntry($journalEntry, $request->validated());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], (int)$e->getCode() ?: 400);
        }
    }

    public function destroy(JournalEntry $journalEntry): Response|JsonResponse
    {
        try {
            $this->journalEntryService->deleteJournalEntry($journalEntry);
            return response()->noContent();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], (int)$e->getCode() ?: 400);
        }
    }
}
