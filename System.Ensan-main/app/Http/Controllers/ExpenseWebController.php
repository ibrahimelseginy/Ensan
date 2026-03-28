<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Beneficiary;
use App\Models\Project;
use App\Models\Campaign;
use App\Models\Workspace;
use App\Models\GuestHouse;
use App\Models\Treasury;
use App\Models\ChangeRequest;
use App\Services\ExpenseService;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

final readonly class ExpenseWebController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService
    ) {}

    public function index(Request $request): View
    {
        $filters = $request->only(['project_id', 'campaign_id', 'workspace_id', 'guest_house_id', 'start_date', 'end_date', 'type']);
        $expenses = $this->expenseService->getFilteredExpenses($filters, 20);
        $analysis = $this->expenseService->getFinancialAnalysis();

        return view('expenses.index', array_merge(compact('expenses'), $analysis));
    }

    public function export(Request $request): StreamedResponse
    {
        return $this->expenseService->exportToCsv($request->all());
    }

    public function create(): View
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $projects      = Project::orderBy('name')->get();
        $campaigns     = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $workspaces    = Workspace::where('status', '!=', 'maintenance')->orderBy('name')->get();
        $guestHouses   = GuestHouse::orderBy('name')->get();
        $treasuries    = Treasury::active()->forDepartment('expenses')->get();

        return view('expenses.create', compact('beneficiaries', 'projects', 'campaigns', 'workspaces', 'guestHouses', 'treasuries'));
    }

    public function store(StoreExpenseRequest $request): RedirectResponse
    {
        try {
            $result = $this->expenseService->createExpense($request->validated());

            if ($result instanceof ChangeRequest) {
                return redirect()->route('expenses.index')->with('success', 'تم إرسال طلب إضافة المصروف للموافقة.');
            }

            return redirect()->route('expenses.show', $result)->with('success', 'تم تسجيل المصروف وخصمه من الخزينة بنجاح');
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'حدث خطأ أثناء حفظ المصروف: ' . $e->getMessage());
        }
    }

    public function show(Expense $expense): View
    {
        $pendingRequest = $this->getPendingRequest($expense);
        return view('expenses.show', compact('expense', 'pendingRequest'));
    }

    public function edit(Expense $expense): View
    {
        $beneficiaries = Beneficiary::orderBy('full_name')->get();
        $projects      = Project::orderBy('name')->get();
        $campaigns     = Campaign::orderByDesc('season_year')->orderBy('name')->get();
        $workspaces    = Workspace::where('status', '!=', 'maintenance')->orderBy('name')->get();
        $guestHouses   = GuestHouse::orderBy('name')->get();

        return view('expenses.edit', compact('expense', 'beneficiaries', 'projects', 'campaigns', 'workspaces', 'guestHouses'));
    }

    public function update(UpdateExpenseRequest $request, Expense $expense): RedirectResponse
    {
        $result = $this->expenseService->updateExpense($expense, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل المصروف للموافقة.');
        }

        return redirect()->route('expenses.show', $expense)->with('success', 'تم تعديل المصروف بنجاح.');
    }

    public function destroy(Expense $expense): RedirectResponse
    {
        $reason = (string) (request('reason') ?? 'إلغاء يدوي من قبل المدير');
        
        try {
            $result = $this->expenseService->cancelExpense($expense, $reason);

            if ($result instanceof ChangeRequest) {
                return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إلغاء المصروف للموافقة.');
            }

            return redirect()->route('expenses.index')->with('success', 'تم إلغاء المصروف وعكس العملية المالية بنجاح.');
        } catch (\Exception $e) {
            return back()->with('error', 'حدث خطأ أثناء إلغاء المصروف: ' . $e->getMessage());
        }
    }

    private function getPendingRequest(Expense $expense): ?ChangeRequest
    {
        return ChangeRequest::where('model_type', Expense::class)
            ->where('model_id', $expense->id)
            ->where('status', 'pending')
            ->first();
    }
}
