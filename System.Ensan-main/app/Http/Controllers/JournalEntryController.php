<?php
namespace App\Http\Controllers;

use App\Models\JournalEntry;
use App\Models\JournalEntryLine;
use Illuminate\Http\Request;

class JournalEntryController extends Controller
{
    public function index() { return JournalEntry::with('lines')->paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'branch' => 'nullable|string',
            'gate' => 'nullable|string',
            'entry_type' => 'required|string',
            'locked' => 'boolean',
            'lines' => 'required|array',
            'lines.*.account_id' => 'required|exists:accounts,id',
            'lines.*.debit' => 'nullable|numeric',
            'lines.*.credit' => 'nullable|numeric'
        ]);
        $entry = JournalEntry::create($data);
        foreach ($data['lines'] as $line) {
            JournalEntryLine::create([
                'journal_entry_id' => $entry->id,
                'account_id' => $line['account_id'],
                'debit' => $line['debit'] ?? 0,
                'credit' => $line['credit'] ?? 0,
            ]);
        }
        return $entry->load('lines');
    }
    public function show(JournalEntry $journalEntry) { return $journalEntry->load('lines'); }
    public function update(Request $request, JournalEntry $journalEntry)
    {
        if ($journalEntry->locked) {
            return response()->json(['message' => 'entry is locked'], 423);
        }
        $data = $request->validate([
            'date' => 'nullable|date',
            'branch' => 'nullable|string',
            'gate' => 'nullable|string',
            'entry_type' => 'nullable|string',
            'locked' => 'boolean'
        ]);
        $journalEntry->update($data);
        return $journalEntry->load('lines');
    }
    public function destroy(JournalEntry $journalEntry)
    {
        if ($journalEntry->locked) {
            return response()->json(['message' => 'entry is locked'], 423);
        }
        $journalEntry->delete();
        return response()->noContent();
    }
}
