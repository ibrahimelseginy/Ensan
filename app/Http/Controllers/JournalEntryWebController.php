<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\Account;
use App\Models\ChangeRequest;
use App\Services\JournalEntryService;
use App\Http\Requests\StoreJournalEntryRequest;
use App\Http\Requests\UpdateJournalEntryRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final class JournalEntryWebController extends Controller
{
    public function __construct(
        private JournalEntryService $journalEntryService
    ) {}

    public function index(): View
    {
        $entries = $this->journalEntryService->getAllEntries(20);
        return view('journal_entries.index', compact('entries'));
    }

    public function create(): View
    {
        $accounts = Account::orderBy('code')->get();
        return view('journal_entries.create', compact('accounts'));
    }

    public function store(StoreJournalEntryRequest $request): RedirectResponse
    {
        $result = $this->journalEntryService->createEntry($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('journal-entries.index')->with('success', 'تم إرسال طلب إضافة القيد للموافقة');
        }

        return redirect()->route('journal-entries.index');
    }

    public function show(JournalEntry $journalEntry): View
    {
        $journalEntry->load('lines.account');
        $pendingRequest = $this->getPendingRequest($journalEntry);

        return view('journal_entries.show', compact('journalEntry', 'pendingRequest'));
    }

    public function edit(JournalEntry $journalEntry): View|RedirectResponse
    {
        if ($journalEntry->locked) {
            return back()->with('error', 'هذا القيد مرحل ولا يمكن تعديله');
        }

        $journalEntry->load('lines.account');
        $accounts = Account::orderBy('code')->get();
        return view('journal_entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(UpdateJournalEntryRequest $request, JournalEntry $journalEntry): RedirectResponse
    {
        try {
            $result = $this->journalEntryService->updateEntry($journalEntry, $request->validated());

            if ($result instanceof ChangeRequest) {
                return redirect()->route('journal-entries.index')->with('success', 'تم إرسال طلب تعديل القيد للموافقة');
            }

            return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'تم تحديث القيد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function destroy(JournalEntry $journalEntry): RedirectResponse
    {
        try {
            $result = $this->journalEntryService->deleteEntry($journalEntry);

            if ($result instanceof ChangeRequest) {
                return redirect()->route('journal-entries.index')->with('success', 'تم إرسال طلب حذف القيد للموافقة');
            }

            return redirect()->route('journal-entries.index')->with('success', 'تم حذف القيد بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    private function getPendingRequest(JournalEntry $entry): ?ChangeRequest
    {
        return ChangeRequest::where('model_type', JournalEntry::class)
            ->where('model_id', $entry->id)
            ->where('status', 'pending')
            ->first();
    }
}
