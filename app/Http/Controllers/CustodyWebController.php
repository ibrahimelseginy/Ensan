<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Finance\Custody;
use App\Models\Finance\CustodyExpense;
use App\Models\Finance\CashAccount;
use App\Models\Finance\FinancialAccount;
use App\Models\Treasury;
use App\Models\User;
use App\Models\Project;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final class CustodyWebController extends Controller
{
    public function index(Request $request): View
    {
        $query = Custody::with(['holder', 'project', 'sourceAccount', 'expenses']);

        if ($request->filled('holder_id')) {
            $query->where('holder_user_id', $request->input('holder_id'));
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->input('project_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $custodies = $query->orderByDesc('issued_on')->orderByDesc('id')->paginate(15);

        // Stats
        $allCustodies = Custody::all();
        $totalIssued = $allCustodies->sum('issued_amount');
        $totalSettled = $allCustodies->sum('settled_amount');
        $totalReturned = $allCustodies->sum('returned_amount');
        $openCustodiesCount = $allCustodies->whereIn('status', [
            Custody::STATUS_DELIVERED,
            Custody::STATUS_UNDER_SETTLEMENT,
            Custody::STATUS_PENDING_REVIEW,
            Custody::STATUS_NEEDS_DOCS,
            Custody::STATUS_SETTLED_PENDING_RETURN,
        ])->count();
        $totalOpenAmount = $allCustodies->whereIn('status', [
            Custody::STATUS_DELIVERED,
            Custody::STATUS_UNDER_SETTLEMENT,
            Custody::STATUS_PENDING_REVIEW,
            Custody::STATUS_NEEDS_DOCS,
            Custody::STATUS_SETTLED_PENDING_RETURN,
        ])->sum(fn ($c) => $c->remaining_to_return);

        $employees = User::where('is_employee', true)->orderBy('name')->get();
        if ($employees->isEmpty()) {
            $employees = User::orderBy('name')->get();
        }
        $projects = Project::orderBy('name')->get(['id', 'name']);

        return view('custodies.index', compact(
            'custodies',
            'totalIssued',
            'totalSettled',
            'totalReturned',
            'openCustodiesCount',
            'totalOpenAmount',
            'employees',
            'projects'
        ));
    }

    public function create(): View
    {
        $employees = User::orderBy('name')->get();
        $projects = Project::orderBy('name')->get(['id', 'name']);
        $campaigns = Campaign::orderByDesc('id')->get(['id', 'name']);
        $treasuries = Treasury::where('is_active', true)->orderBy('name')->get();

        return view('custodies.create', compact('employees', 'projects', 'campaigns', 'treasuries'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'holder_user_id' => 'required|exists:users,id',
            'treasury_id' => 'required|exists:treasuries,id',
            'issued_amount' => 'required|numeric|min:1',
            'issued_on' => 'required|date',
            'due_on' => 'nullable|date|after_or_equal:issued_on',
            'project_id' => 'nullable|exists:projects,id',
            'purpose' => 'required|string|max:1000',
        ]);

        $treasury = Treasury::findOrFail($data['treasury_id']);

        if ($treasury->current_balance < (float) $data['issued_amount']) {
            return back()->withInput()->with('error', "رصيد الخزينة ({$treasury->name}) لا يكفي لصرف العهدة. الرصيد المتاح: " . number_format($treasury->current_balance, 2) . ' ج.م');
        }

        DB::transaction(function () use ($data, $treasury) {
            $custodyNumber = 'CUST-' . date('Ymd') . '-' . strtoupper(Str::random(6));

            // Find or dynamically link a cash_account_id & financial_account_id to prevent FK errors
            $cashAccount = CashAccount::where('legacy_treasury_id', $treasury->id)->first();
            if (!$cashAccount) {
                $finAccount = FinancialAccount::firstOrCreate(
                    ['code' => 'TREAS-' . $treasury->id],
                    [
                        'name' => $treasury->name,
                        'classification' => 'asset',
                        'normal_balance' => 'debit',
                        'is_posting' => true,
                        'is_active' => true,
                        'currency' => 'EGP',
                    ]
                );

                $cashAccount = CashAccount::firstOrCreate(
                    ['legacy_treasury_id' => $treasury->id],
                    [
                        'code' => 'CASH-TR-' . $treasury->id,
                        'name' => $treasury->name,
                        'kind' => 'cash',
                        'ledger_account_id' => $finAccount->id,
                        'currency' => 'EGP',
                        'is_active' => true,
                    ]
                );
            }

            $custodyFinAccount = FinancialAccount::firstOrCreate(
                ['code' => 'CUSTODY-ACC-DEFAULT'],
                [
                    'name' => 'حساب العهد المالية',
                    'classification' => 'asset',
                    'normal_balance' => 'debit',
                    'is_posting' => true,
                    'is_active' => true,
                    'currency' => 'EGP',
                ]
            );

            $sourceCashAccountId = $cashAccount->id;
            $custodyAccountId = $custodyFinAccount->id;

            $custody = Custody::create([
                'custody_number' => $custodyNumber,
                'holder_user_id' => $data['holder_user_id'],
                'source_cash_account_id' => $sourceCashAccountId,
                'custody_account_id' => $custodyAccountId,
                'issued_amount' => $data['issued_amount'],
                'settled_amount' => 0,
                'returned_amount' => 0,
                'currency' => 'EGP',
                'project_id' => $data['project_id'] ?? null,
                'issued_on' => $data['issued_on'],
                'due_on' => $data['due_on'] ?? null,
                'status' => Custody::STATUS_DELIVERED,
                'purpose' => $data['purpose'],
            ]);

            // Deduct cash from treasury
            $treasury->current_balance -= (float) $data['issued_amount'];
            $treasury->save();

            // Record transaction
            $treasury->transactions()->create([
                'type' => 'out',
                'amount' => $data['issued_amount'],
                'transaction_date' => $data['issued_on'],
                'description' => "تسليم عهدة مالية للموظف: {$custody->holder->name} (سند: {$custodyNumber}) - {$data['purpose']}",
                'created_by' => auth()->id(),
            ]);
        });

        return redirect()->route('custodies.index')->with('success', 'تم إنشاء وتسليم العهدة المالية بنجاح وخصمها من الخزينة.');
    }

    public function show(Custody $custody): View
    {
        $custody->load(['holder', 'project', 'expenses.project', 'closer']);
        $projects = Project::orderBy('name')->get(['id', 'name']);
        $campaigns = Campaign::orderByDesc('id')->get(['id', 'name']);
        $treasuries = Treasury::where('is_active', true)->orderBy('name')->get();

        return view('custodies.show', compact('custody', 'projects', 'campaigns', 'treasuries'));
    }

    public function addExpense(Request $request, Custody $custody): RedirectResponse
    {
        $data = $request->validate([
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.5',
            'invoice_number' => 'nullable|string|max:128',
            'merchant_name' => 'nullable|string|max:191',
            'project_id' => 'nullable|exists:projects,id',
            'description' => 'required|string|max:1000',
        ]);

        CustodyExpense::create([
            'custody_id' => $custody->id,
            'expense_date' => $data['expense_date'],
            'amount' => $data['amount'],
            'expense_account_id' => 1,
            'project_id' => $data['project_id'] ?? $custody->project_id,
            'invoice_number' => $data['invoice_number'] ?? null,
            'merchant_name' => $data['merchant_name'] ?? null,
            'description' => $data['description'],
            'status' => 'approved',
            'approved_by' => auth()->id(),
        ]);

        // Update settled amount on custody
        $totalExpenses = $custody->expenses()->sum('amount');
        $custody->settled_amount = $totalExpenses;
        if ($custody->status === Custody::STATUS_DELIVERED) {
            $custody->status = Custody::STATUS_UNDER_SETTLEMENT;
        }
        $custody->save();

        return back()->with('success', 'تم تسجيل فاتورة / مصروف العهدة بنجاح وتحديث إجمالي التسوية.');
    }

    public function settle(Request $request, Custody $custody): RedirectResponse
    {
        $data = $request->validate([
            'action' => 'required|in:return_remainder,pay_difference,close_exact',
            'treasury_id' => 'required|exists:treasuries,id',
            'returned_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string|max:500',
        ]);

        $treasury = Treasury::findOrFail($data['treasury_id']);

        DB::transaction(function () use ($custody, $treasury, $data) {
            $issued = (float) $custody->issued_amount;
            $settled = (float) $custody->settled_amount;

            if ($data['action'] === 'return_remainder') {
                $returned = (float) ($data['returned_amount'] ?? ($issued - $settled));
                if ($returned > 0) {
                    $treasury->current_balance += $returned;
                    $treasury->save();

                    $treasury->transactions()->create([
                        'type' => 'in',
                        'amount' => $returned,
                        'transaction_date' => now()->toDateString(),
                        'description' => "رد متبقي عهدة مالية للموظف: {$custody->holder->name} (سند: {$custody->custody_number})",
                        'created_by' => auth()->id(),
                    ]);

                    $custody->returned_amount += $returned;
                }
                $custody->status = Custody::STATUS_CLOSED;
                $custody->closed_by = auth()->id();
                $custody->closed_at = now();
                $custody->save();
            } elseif ($data['action'] === 'pay_difference') {
                $difference = $settled - $issued;
                if ($difference > 0) {
                    if ($treasury->current_balance < $difference) {
                        throw new \Exception("رصيد الخزينة ({$treasury->name}) لا يكفي لصرف فرق العهدة ({$difference} ج.م)");
                    }
                    $treasury->current_balance -= $difference;
                    $treasury->save();

                    $treasury->transactions()->create([
                        'type' => 'out',
                        'amount' => $difference,
                        'transaction_date' => now()->toDateString(),
                        'description' => "صرف مستحقات فرق تسوية عهدة للموظف: {$custody->holder->name} (سند: {$custody->custody_number})",
                        'created_by' => auth()->id(),
                    ]);
                }
                $custody->status = Custody::STATUS_CLOSED;
                $custody->closed_by = auth()->id();
                $custody->closed_at = now();
                $custody->save();
            } else {
                $custody->status = Custody::STATUS_CLOSED;
                $custody->closed_by = auth()->id();
                $custody->closed_at = now();
                $custody->save();
            }
        });

        return back()->with('success', 'تم اعتماد وإغلاق تسوية العهدة بنجاح.');
    }

    public function printVoucher(Custody $custody): View
    {
        $custody->load(['holder', 'project', 'expenses.project', 'closer']);
        return view('custodies.print_voucher', compact('custody'));
    }
}
