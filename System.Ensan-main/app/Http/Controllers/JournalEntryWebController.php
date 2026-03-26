<?php
namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use App\Models\Account;
use Illuminate\Http\Request;

class JournalEntryWebController extends Controller
{
    public function index()
    {
        $entries = JournalEntry::with('lines.account')->orderByDesc('date')->orderByDesc('id')->paginate(20);
        return view('journal_entries.index', compact('entries'));
    }

    public function create()
    {
        $accounts = Account::orderBy('code')->get();
        return view('journal_entries.create', compact('accounts'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'branch' => 'nullable|string',
            'gate' => 'nullable|string',
            'entry_type' => 'required|string',
            'description' => 'nullable|string',
            'locked' => 'boolean',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric',
            'lines.*.credit' => 'nullable|numeric',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');
        
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['lines' => 'القيد غير متزن. إجمالي المدين: ' . $totalDebit . ' - إجمالي الدائن: ' . $totalCredit])->withInput();
        }

        $executor = function() use ($data) {
            $entry = JournalEntry::create([
                'date' => $data['date'],
                'branch' => $data['branch'],
                'gate' => $data['gate'],
                'entry_type' => $data['entry_type'],
                'description' => $data['description'],
                'locked' => $data['locked'] ?? false,
            ]);

            foreach ($data['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $entry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                ]);
            }
            return $entry;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            JournalEntry::class,
            null,
            'create',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('journal-entries.index')->with('success', 'تم إرسال طلب إضافة القيد للموافقة');
        }

        return redirect()->route('journal-entries.index');
    }

    public function show(JournalEntry $journalEntry)
    {
        $journalEntry->load('lines.account');
        $pendingRequest = \App\Models\ChangeRequest::where('model_type', JournalEntry::class)
            ->where('model_id', $journalEntry->id)
            ->where('status', 'pending')
            ->first();
        return view('journal_entries.show', compact('journalEntry', 'pendingRequest'));
    }

    public function edit(JournalEntry $journalEntry)
    {
        if ($journalEntry->locked) {
            return back()->with('error', 'هذا القيد مرحل ولا يمكن تعديله');
        }

        $journalEntry->load('lines.account');
        $accounts = Account::orderBy('code')->get();
        return view('journal_entries.edit', compact('journalEntry', 'accounts'));
    }

    public function update(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->locked) {
            return back()->with('error', 'هذا القيد مرحل ولا يمكن تعديله');
        }

        $data = $request->validate([
            'date' => 'required|date',
            'branch' => 'nullable|string',
            'gate' => 'nullable|string',
            'entry_type' => 'required|string',
            'description' => 'nullable|string',
            'locked' => 'boolean',
            'lines' => 'required|array|min:2',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric',
            'lines.*.credit' => 'nullable|numeric',
        ]);

        $totalDebit = collect($data['lines'])->sum('debit');
        $totalCredit = collect($data['lines'])->sum('credit');
        
        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['lines' => 'القيد غير متزن. إجمالي المدين: ' . $totalDebit . ' - إجمالي الدائن: ' . $totalCredit])->withInput();
        }

        $executor = function () use ($journalEntry, $data) {
            $journalEntry->update([
                'date' => $data['date'],
                'branch' => $data['branch'],
                'gate' => $data['gate'],
                'entry_type' => $data['entry_type'],
                'description' => $data['description'],
                'locked' => $data['locked'] ?? false,
            ]);

            $journalEntry->lines()->delete();

            foreach ($data['lines'] as $line) {
                JournalEntryLine::create([
                    'journal_entry_id' => $journalEntry->id,
                    'account_id' => $line['account_id'],
                    'debit' => $line['debit'] ?? 0,
                    'credit' => $line['credit'] ?? 0,
                ]);
            }

            return $journalEntry;
        };

        $isAdminOrManager = auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager');

        $result = \App\Services\ChangeRequestService::handleRequest(
            JournalEntry::class,
            $journalEntry->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('journal-entries.index')->with('success', 'تم إرسال طلب تعديل القيد للموافقة');
        }

        return redirect()->route('journal-entries.show', $journalEntry)->with('success', 'تم تحديث القيد بنجاح');
    }

    public function destroy(JournalEntry $journalEntry)
    {
        if ($journalEntry->locked) {
            return back()->with('error', 'هذا القيد مرحل ولا يمكن حذفه');
        }

        $executor = function () use ($journalEntry) {
            $journalEntry->delete();
            return true;
        };

        $isAdminOrManager = auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager');

        $result = \App\Services\ChangeRequestService::handleRequest(
            JournalEntry::class,
            $journalEntry->id,
            'delete',
            [],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('journal-entries.index')->with('success', 'تم إرسال طلب حذف القيد للموافقة');
        }

        return redirect()->route('journal-entries.index')->with('success', 'تم حذف القيد بنجاح');
    }
}
