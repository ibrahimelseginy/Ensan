<?php
namespace App\Http\Controllers;

use App\Models\Expense;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function index() { return Expense::with(['project','campaign','beneficiary','creator'])->paginate(20); }
    public function store(Request $request)
    {
        $data = $request->validate([
            'type' => 'required|in:operational,aid,logistics',
            'amount' => 'required|numeric',
            'currency' => 'nullable|string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'paid_at' => 'nullable|date',
            'attachment_path' => 'nullable|string'
        ]);
        $data['created_by'] = $request->user() ? $request->user()->id : null;
        $expense = Expense::create($data);
        \App\Services\ExpenseService::postCreate($expense);
        return $expense->load(['project','campaign','beneficiary','creator']);
    }
    public function show(Expense $expense) { return $expense->load(['project','campaign','beneficiary','creator']); }
    public function update(Request $request, Expense $expense)
    {
        $data = $request->validate([
            'type' => 'sometimes|in:operational,aid,logistics',
            'amount' => 'nullable|numeric',
            'currency' => 'nullable|string',
            'description' => 'nullable|string',
            'project_id' => 'nullable|exists:projects,id',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'beneficiary_id' => 'nullable|exists:beneficiaries,id',
            'paid_at' => 'nullable|date',
            'attachment_path' => 'nullable|string'
        ]);
        $expense->update($data);
        return $expense->load(['project','campaign','beneficiary','creator']);
    }
    public function destroy(Expense $expense)
    {
        $expense->delete();
        return response()->noContent();
    }
}
