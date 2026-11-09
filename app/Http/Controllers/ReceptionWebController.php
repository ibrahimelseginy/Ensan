<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ReceptionLog;
use App\Models\Treasury;
use App\Models\TreasuryTransaction;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class ReceptionWebController extends Controller
{
    public function index()
    {
        $logs = ReceptionLog::latest()->paginate(10);
        $receptionTreasuries = Treasury::forReception(auth()->user());
        $primaryTreasury = $receptionTreasuries->first();
        $treasuryIds = $receptionTreasuries->pluck('id')->all();

        $dateCol = \Illuminate\Support\Facades\Schema::hasColumn('expenses', 'paid_at') ? 'paid_at' : 'created_at';

        $receptionExpenses = Expense::with(['treasury'])
            ->where(function ($q) use ($treasuryIds) {
                if (!empty($treasuryIds)) {
                    $q->whereIn('treasury_id', $treasuryIds);
                }
                $q->orWhere('description', 'like', '%[مصروفات الاستقبال%');
            })
            ->orderByDesc($dateCol)
            ->orderByDesc('id')
            ->limit(25)
            ->get();

        $todayExpensesTotal = (float) Expense::query()
            ->where(function ($q) use ($treasuryIds) {
                if (!empty($treasuryIds)) {
                    $q->whereIn('treasury_id', $treasuryIds);
                }
                $q->orWhere('description', 'like', '%[مصروفات الاستقبال%');
            })
            ->where(function ($q) use ($dateCol) {
                $q->whereDate($dateCol, today())
                  ->orWhere(function ($sq) use ($dateCol) {
                      $sq->whereNull($dateCol)->whereDate('created_at', today());
                  });
            })
            ->sum('amount');

        $monthExpensesTotal = (float) Expense::query()
            ->where(function ($q) use ($treasuryIds) {
                if (!empty($treasuryIds)) {
                    $q->whereIn('treasury_id', $treasuryIds);
                }
                $q->orWhere('description', 'like', '%[مصروفات الاستقبال%');
            })
            ->where(function ($q) use ($dateCol) {
                $q->whereBetween($dateCol, [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
                  ->orWhere(function ($sq) use ($dateCol) {
                      $sq->whereNull($dateCol)->whereBetween('created_at', [now()->startOfMonth(), now()->endOfMonth()]);
                  });
            })
            ->sum('amount');

        $todayStats = [
            'total' => ReceptionLog::whereDate('created_at', today())->count(),
            'personal' => ReceptionLog::whereDate('created_at', today())->where('visit_type', 'personal')->count(),
            'phone' => ReceptionLog::whereDate('created_at', today())->where('visit_type', 'phone')->count(),
            'completed' => ReceptionLog::whereDate('created_at', today())->where('status', 'completed')->count(),
            'today_expenses' => $todayExpensesTotal,
            'month_expenses' => $monthExpensesTotal,
        ];

        return view('dashboard.reception.index', compact(
            'logs',
            'todayStats',
            'receptionTreasuries',
            'primaryTreasury',
            'receptionExpenses'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'visitor_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'visit_type' => 'required|in:personal,phone',
            'purpose' => 'required|in:help_request,complaint,donation,inquiry,other',
            'notes' => 'nullable|string',
            'directed_to' => 'nullable|string'
        ]);

        $log = ReceptionLog::create($validated);

        if ($request->has('directed_to') && $request->directed_to) {
            $log->update(['status' => 'directed']);
        }

        return redirect()->route('reception.index')->with('success', 'تم تسجيل السجل بنجاح');
    }

    public function storeExpense(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'treasury_id' => 'nullable|exists:treasuries,id',
            'expense_item' => 'required|string|max:120',
            'amount' => 'required|numeric|min:0.01',
            'spent_at' => 'nullable|date',
            'description' => 'nullable|string|max:500',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,webp,pdf|max:10240',
        ]);

        $receptionTreasuries = Treasury::forReception(auth()->user());
        $treasury = !empty($validated['treasury_id'])
            ? Treasury::find((int) $validated['treasury_id'])
            : $receptionTreasuries->first();

        if (!$treasury) {
            $treasury = $receptionTreasuries->first();
        }

        $amount = round((float) $validated['amount'], 2);
        $spentAt = !empty($validated['spent_at']) ? Carbon::parse($validated['spent_at']) : now();
        $fullDesc = '[مصروفات الاستقبال - ' . trim($validated['expense_item']) . '] ' . trim((string) ($validated['description'] ?? ''));

        DB::transaction(function () use ($treasury, $amount, $spentAt, $fullDesc, $validated, $request) {
            $attachmentPath = null;
            if ($request->hasFile('attachment') && $request->file('attachment')->isValid()) {
                $attachmentPath = $request->file('attachment')->store('expenses/reception', 'public');
            }

            $expensePayload = [
                'type' => 'operational',
                'category' => $validated['expense_item'] ?? 'operational',
                'amount' => $amount,
                'currency' => $treasury?->currency ?? 'EGP',
                'paid_at' => $spentAt->toDateString(),
                'description' => trim($fullDesc),
                'treasury_id' => $treasury?->id,
                'created_by' => auth()->id(),
                'status' => 'approved',
                'attachment_path' => $attachmentPath,
            ];

            $expense = Expense::create($expensePayload);

            if ($treasury) {
                TreasuryTransaction::create([
                    'treasury_id' => $treasury->id,
                    'type' => 'out',
                    'amount' => $amount,
                    'currency' => $treasury->currency ?? 'EGP',
                    'description' => trim($fullDesc),
                    'reference' => 'REC-EXP-' . $expense->id,
                    'transaction_date' => $spentAt->toDateString(),
                    'expense_id' => $expense->id,
                    'created_by' => auth()->id(),
                ]);
                $treasury->updateBalance();
            }
        });

        return redirect()->route('reception.index')->with('success', "تم تسجيل مصروف الاستقبال بقيمة [{$amount} ج.م] وخصمه من خزينة الاستقبال بنجاح.");
    }

    public function storeCustodyDeposit(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'treasury_id' => 'nullable|exists:treasuries,id',
            'deposit_type' => 'required|in:custody,donation',
            'amount' => 'required|numeric|min:0.01',
            'transaction_date' => 'nullable|date',
            'description' => 'nullable|string|max:500',
        ]);

        $receptionTreasuries = Treasury::forReception(auth()->user());
        $treasury = !empty($validated['treasury_id'])
            ? Treasury::find((int) $validated['treasury_id'])
            : $receptionTreasuries->first();

        $amount = round((float) $validated['amount'], 2);
        $txDate = !empty($validated['transaction_date']) ? Carbon::parse($validated['transaction_date'])->toDateString() : now()->toDateString();
        $prefix = $validated['deposit_type'] === 'donation' ? 'تبرع نقدي بالاستقبال: ' : 'تغذية عهدة خزينة الاستقبال: ';
        $desc = $prefix . trim((string) ($validated['description'] ?: ($validated['deposit_type'] === 'donation' ? 'تبرع وارد بالاستقبال' : 'إيداع عهدة للاستقبال')));

        if ($treasury) {
            TreasuryTransaction::create([
                'treasury_id' => $treasury->id,
                'type' => 'in',
                'amount' => $amount,
                'currency' => $treasury->currency ?? 'EGP',
                'description' => $desc,
                'reference' => 'REC-IN-' . strtoupper(substr(uniqid(), -5)),
                'transaction_date' => $txDate,
                'created_by' => auth()->id(),
            ]);
            $treasury->updateBalance();
        }

        return redirect()->route('reception.index')->with('success', "تم تسجيل إيداع بقيمة [{$amount} ج.م] في خزينة الاستقبال بنجاح.");
    }
}
