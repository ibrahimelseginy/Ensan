<?php
namespace App\Http\Controllers;

use App\Models\FinancialClosure;
use App\Models\JournalEntry;
use Illuminate\Http\Request;

class FinancialClosureController extends Controller
{
    public function index() { return FinancialClosure::orderByDesc('date')->paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'date' => 'required|date',
            'branch' => 'nullable|string'
        ]);
        $data['closed_by'] = $request->user() ? $request->user()->id : null;
        $closure = FinancialClosure::create($data);
        JournalEntry::whereDate('date','<=',$data['date'])
            ->when($data['branch'] ?? null, function($q,$b){ $q->where('branch',$b); })
            ->update(['locked' => true]);
        return $closure;
    }
    public function approve(Request $request, FinancialClosure $closure)
    {
        $closure->update([
            'approved' => true,
            'approved_by' => $request->user() ? $request->user()->id : null,
        ]);
        return $closure;
    }
}
