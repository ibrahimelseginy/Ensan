<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Treasury;
use App\Models\User;
use App\Services\TreasuryService;
use App\Services\ChangeRequestService;
use App\Http\Requests\StoreTreasuryRequest;
use App\Http\Requests\UpdateTreasuryRequest;
use App\Http\Requests\AddTreasuryTransactionRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

final class TreasuryController extends Controller
{
    public function __construct(
        private TreasuryService $treasuryService
    ) {}

    private function authorizeTreasuryAccess(Treasury $treasury): void
    {
        $user = auth()->user();
        if (!$user || !$user->canAccessTreasury((int) $treasury->id)) {
            abort(403, 'ليس لديك صلاحية للوصول إلى هذه الخزينة.');
        }
    }

    private function authorizeTreasuryManage(Treasury $treasury): void
    {
        $user = auth()->user();
        if (!$user || !$user->canManageTreasury($treasury)) {
            abort(403, 'ليس لديك صلاحية لإجراء تعديلات أو حركات على هذه الخزينة (صلاحية عرض فقط).');
        }
    }

    public function index(): View|RedirectResponse
    {
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php');
        }

        $treasuries = Treasury::with(['manager', 'transactions', 'donations'])
            ->accessibleForUser(auth()->user())
            ->orderByDesc('is_active')
            ->orderBy('name')
            ->get();
        
        $pendingRequests = \App\Models\ChangeRequest::where('model_type', Treasury::class)
            ->where('status', 'pending')
            ->get()
            ->groupBy('model_id');

        // Distinguish physical actual treasuries from project/campaign allocations
        $physicalTypes = [
            Treasury::TYPE_MAIN,
            Treasury::TYPE_CENTRAL ?? 'central',
            Treasury::TYPE_BANK ?? 'bank',
            Treasury::TYPE_WALLET ?? 'wallet',
            Treasury::TYPE_RECEPTION,
            Treasury::TYPE_GUEST_HOUSE,
            Treasury::TYPE_BRANCH,
            Treasury::TYPE_PETTY_CASH,
        ];

        $physicalTreasuries = $treasuries->filter(function ($t) use ($physicalTypes) {
            return in_array($t->type, $physicalTypes, true)
                || !in_array($t->type, [Treasury::TYPE_PROJECT, Treasury::TYPE_CAMPAIGN], true);
        });

        $bankTreasuries = $physicalTreasuries->filter(function ($t) {
            return $t->type === (Treasury::TYPE_BANK ?? 'bank') || str_contains($t->name, 'بنك') || str_contains($t->name, 'البنك');
        });

        $cashTreasuries = $physicalTreasuries->filter(function ($t) {
            return !($t->type === (Treasury::TYPE_BANK ?? 'bank') || str_contains($t->name, 'بنك') || str_contains($t->name, 'البنك'));
        });

        $bankBalance = (float) $bankTreasuries->sum('current_balance');
        $cashBalance = (float) $cashTreasuries->sum('current_balance');
        $totalPhysicalBalance = $bankBalance + $cashBalance;

        // Today In / Out
        $todayStr = now()->toDateString();
        $todayIn = 0.0;
        $todayOut = 0.0;
        if (Schema::hasTable('treasury_transactions')) {
            $todayIn = (float) \App\Models\TreasuryTransaction::whereIn('treasury_id', $physicalTreasuries->pluck('id'))
                ->where('transaction_date', $todayStr)
                ->where('type', 'in')
                ->sum('amount');
            $todayOut = (float) \App\Models\TreasuryTransaction::whereIn('treasury_id', $physicalTreasuries->pluck('id'))
                ->where('transaction_date', $todayStr)
                ->where('type', 'out')
                ->sum('amount');
        }

        // Sub treasuries for the popup modal
        $subTreasuries = $physicalTreasuries->filter(function ($t) {
            return in_array($t->type, [Treasury::TYPE_RECEPTION, Treasury::TYPE_GUEST_HOUSE, Treasury::TYPE_WALLET ?? 'wallet', Treasury::TYPE_BRANCH, Treasury::TYPE_PETTY_CASH], true);
        });

        $centralTreasury = $physicalTreasuries->first(function ($t) {
            return $t->type === (Treasury::TYPE_CENTRAL ?? 'central') || str_contains($t->name, 'المركزية') || str_contains($t->name, 'الصندوق');
        });

        $accountsTreasury = $physicalTreasuries->first(function ($t) {
            return $t->type === Treasury::TYPE_MAIN || str_contains($t->name, 'الحسابات');
        });

        $accessibleTreasuryIds = auth()->user()?->getAccessibleTreasuryIds();
        $isGlobalTreasuryAccess = ($accessibleTreasuryIds === null);

        // Recent transfers and custodies for tabs
        $recentTransfers = collect();
        if (Schema::hasTable('cash_transfers')) {
            $transferQuery = \App\Models\Finance\CashTransfer::with(['fromAccount', 'toAccount', 'requester', 'receiver']);
            if ($accessibleTreasuryIds !== null) {
                $allowedCashAccountIds = \App\Models\Finance\CashAccount::whereIn('legacy_treasury_id', $accessibleTreasuryIds)->pluck('id')->all();
                $transferQuery->where(function ($q) use ($allowedCashAccountIds) {
                    $q->whereIn('from_cash_account_id', $allowedCashAccountIds)
                      ->orWhereIn('to_cash_account_id', $allowedCashAccountIds);
                });
            }
            $recentTransfers = $transferQuery->orderByDesc('id')->limit(15)->get();
        }

        $recentCustodies = collect();
        if (Schema::hasTable('custodies')) {
            $custodyQuery = \App\Models\Finance\Custody::with(['holder', 'project']);
            if ($accessibleTreasuryIds !== null) {
                $allowedCashAccountIds = \App\Models\Finance\CashAccount::whereIn('legacy_treasury_id', $accessibleTreasuryIds)->pluck('id')->all();
                $custodyQuery->where(function ($q) use ($allowedCashAccountIds) {
                    $q->whereIn('source_cash_account_id', $allowedCashAccountIds)
                      ->orWhere('holder_user_id', auth()->id());
                });
            }
            $recentCustodies = $custodyQuery->orderByDesc('id')->limit(10)->get();
        }

        $totalBalance     = $totalPhysicalBalance;
        $activeTreasuries = $treasuries->where('is_active', true)->count();
        $totalTreasuries  = $treasuries->count();

        $recentTransactions = collect();
        if (Schema::hasTable('treasury_transactions') && $treasuries->isNotEmpty()) {
            $recentTransactions = \App\Models\TreasuryTransaction::with(['treasury', 'createdBy', 'donation'])
                ->whereIn('treasury_id', $treasuries->pluck('id'))
                ->orderByDesc('transaction_date')
                ->orderByDesc('created_at')
                ->limit(15)
                ->get();
        }

        $monthlyData = $this->treasuryService->getMonthlyTrends($accessibleTreasuryIds);
        $insights    = $this->treasuryService->generateInsights($treasuries);

        return view('treasuries.index', compact(
            'treasuries',
            'physicalTreasuries',
            'pendingRequests',
            'totalBalance',
            'totalPhysicalBalance',
            'bankBalance',
            'cashBalance',
            'todayIn',
            'todayOut',
            'bankTreasuries',
            'subTreasuries',
            'centralTreasury',
            'accountsTreasury',
            'recentTransfers',
            'recentCustodies',
            'activeTreasuries',
            'totalTreasuries',
            'recentTransactions',
            'monthlyData',
            'insights',
            'isGlobalTreasuryAccess'
        ));
    }

    public function create(): View
    {
        $managers    = User::orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get(['id', 'name']);
        $projects    = \App\Models\Project::orderBy('name')->get(['id', 'name']);
        $campaigns   = \App\Models\Campaign::orderByDesc('id')->get(['id', 'name']);
        return view('treasuries.create', compact('managers', 'guestHouses', 'projects', 'campaigns'));
    }

    public function store(StoreTreasuryRequest $request): RedirectResponse
    {
        $treasury = $this->treasuryService->createTreasury($request->validated());

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم إنشاء الخزينة بنجاح');
    }

    public function show(Treasury $treasury): View
    {
        $this->authorizeTreasuryAccess($treasury);
        $treasury->load('manager', 'donations', 'linkedTreasury', 'linkedSources');

        $stats = [
            'total_in'           => $treasury->transactions()->where('type', 'in')->sum('amount'),
            'total_out'          => $treasury->transactions()->where('type', 'out')->sum('amount'),
            'total_transactions' => $treasury->transactions()->count(),
            'total_donations'    => $treasury->donations()->count(),
            'donations_amount'   => $treasury->donations()->sum('amount')
        ];

        $recentTransactions = $treasury->transactions()
            ->with(['createdBy.roles', 'donation.guestHouse', 'donation.project', 'donation.user.roles'])
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $monthlyData = $this->treasuryService->getTreasuryMonthlyData($treasury->id);

        $accessibleTreasuries = Treasury::accessibleForUser(auth()->user())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        if (!$accessibleTreasuries->contains('id', $treasury->id)) {
            $accessibleTreasuries->prepend($treasury);
        }

        $canManage = auth()->user()?->canManageTreasury($treasury) ?? false;

        return view('treasuries.show', compact('treasury', 'stats', 'recentTransactions', 'monthlyData', 'accessibleTreasuries', 'canManage'));
    }

    public function edit(Treasury $treasury): View
    {
        $this->authorizeTreasuryManage($treasury);
        $managers    = User::orderBy('name')->get();
        $guestHouses = \App\Models\GuestHouse::orderBy('name')->get(['id', 'name']);
        $projects    = \App\Models\Project::orderBy('name')->get(['id', 'name']);
        $campaigns   = \App\Models\Campaign::orderByDesc('id')->get(['id', 'name']);
        return view('treasuries.edit', compact('treasury', 'managers', 'guestHouses', 'projects', 'campaigns'));
    }

    public function update(UpdateTreasuryRequest $request, Treasury $treasury): RedirectResponse
    {
        $this->authorizeTreasuryManage($treasury);
        $data = $request->validated();

        $executor = function () use ($treasury, $data) {
            $data['is_active'] = request()->boolean('is_active');
            $this->treasuryService->updateTreasury($treasury, $data);
            return $treasury;
        };

        $payloadForLog = $data;
        unset($payloadForLog['total_in'], $payloadForLog['total_out']);

        $result = ChangeRequestService::handleRequest(
            Treasury::class,
            $treasury->id,
            'update',
            $payloadForLog,
            $executor,
            true
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('treasuries.show', $treasury)
                ->with('success', 'تم إرسال طلب تعديل الخزينة للموافقة');
        }

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم تحديث الخزينة بنجاح');
    }

    public function adjustTotals(\Illuminate\Http\Request $request, Treasury $treasury): RedirectResponse
    {
        $this->authorizeTreasuryManage($treasury);
        if ($request->boolean('zero_treasury')) {
            $zeroMode = (string) $request->input('zero_mode', 'balance_only');
            if ($zeroMode === 'clear_all') {
                $this->treasuryService->syncManualTotals($treasury, 0.0, 0.0, 0.0, (int) auth()->id());
            } else {
                $netTransactions = (float) $treasury->getTotalIn() - (float) $treasury->getTotalOut();
                $treasury->current_balance = 0.0;
                $treasury->opening_balance = -$netTransactions;
                $treasury->save();
            }

            if ($request->filled('redirect_back')) {
                return back()->with('success', "تم تصفير رصيد خزينة [{$treasury->name}] يدوياً إلى (0.00 ج.م) بنجاح.");
            }

            return redirect()->route('treasuries.show', $treasury)
                ->with('success', "تم تصفير رصيد خزينة [{$treasury->name}] يدوياً إلى (0.00 ج.م) بنجاح.");
        }

        $validated = $request->validate([
            'current_balance' => 'nullable|numeric',
            'total_in'        => 'nullable|numeric|min:0',
            'total_out'       => 'nullable|numeric|min:0',
        ]);

        $targetIn      = array_key_exists('total_in', $validated) && $validated['total_in'] !== null && $validated['total_in'] !== '' ? (float) $validated['total_in'] : null;
        $targetOut     = array_key_exists('total_out', $validated) && $validated['total_out'] !== null && $validated['total_out'] !== '' ? (float) $validated['total_out'] : null;
        $targetBalance = array_key_exists('current_balance', $validated) && $validated['current_balance'] !== null && $validated['current_balance'] !== '' ? (float) $validated['current_balance'] : null;

        $this->treasuryService->syncManualTotals($treasury, $targetIn, $targetOut, $targetBalance, (int) auth()->id());

        if ($request->filled('redirect_back')) {
            return back()->with('success', 'تم تعديل أرصدة الوارد والصادر والرصيد الحالي للخزينة بنجاح');
        }

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم تعديل أرصدة الوارد والصادر والرصيد الحالي للخزينة بنجاح');
    }

    public function updateTransaction(\Illuminate\Http\Request $request, Treasury $treasury, \App\Models\TreasuryTransaction $transaction): RedirectResponse
    {
        $this->authorizeTreasuryManage($treasury);
        abort_if((int) $transaction->treasury_id !== (int) $treasury->id, 404);

        $validated = $request->validate([
            'type'             => 'required|in:in,out',
            'amount'           => 'required|numeric|min:0.01',
            'description'      => 'required|string|max:500',
            'transaction_date' => 'required|date',
        ]);

        $recalculateBalance = $request->boolean('recalculate_balance', true);
        $this->treasuryService->updateTransaction($treasury, $transaction, $validated, $recalculateBalance);

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم تعديل الحركة المالية بنجاح');
    }

    public function deleteTransaction(\Illuminate\Http\Request $request, Treasury $treasury, \App\Models\TreasuryTransaction $transaction): RedirectResponse
    {
        $this->authorizeTreasuryManage($treasury);
        abort_if((int) $transaction->treasury_id !== (int) $treasury->id, 404);

        $recalculateBalance = $request->boolean('recalculate_balance', true);
        $this->treasuryService->deleteTransaction($treasury, $transaction, $recalculateBalance);

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم حذف الحركة المالية بنجاح');
    }

    public function destroy(Treasury $treasury): RedirectResponse
    {
        $this->authorizeTreasuryManage($treasury);
        try {
            $executor = function () use ($treasury) {
                $this->treasuryService->deleteTreasury($treasury);
                return true;
            };

            $result = ChangeRequestService::handleRequest(
                Treasury::class,
                $treasury->id,
                'delete',
                ['note' => 'حذف خزينة'],
                $executor,
                true
            );

            if ($result instanceof \App\Models\ChangeRequest) {
                return redirect()->route('treasuries.index')
                    ->with('success', 'تم إرسال طلب حذف الخزينة للموافقة');
            }

            return redirect()->route('treasuries.index')
                ->with('success', 'تم حذف الخزينة بنجاح');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function addTransaction(AddTreasuryTransactionRequest $request, Treasury $treasury): RedirectResponse
    {
        $validated = $request->validated();
        $type = $validated['type'] ?? 'in';
        $amount = (float) $validated['amount'];
        $desc = (string) $validated['description'];
        $date = $validated['transaction_date'];

        // Determine if this is a dual-treasury transfer / supply
        $fromId = null;
        $toId = null;

        if ($type === 'transfer') {
            $fromId = (int) ($request->input('from_treasury_id') ?: $request->input('source_treasury_id'));
            $toId = (int) ($request->input('to_treasury_id') ?: ($request->input('treasury_id') ?: $treasury->id));
        } elseif ($type === 'in' && $request->filled('source_treasury_id')) {
            $fromId = (int) $request->input('source_treasury_id');
            $toId = (int) ($request->input('treasury_id') ?: $treasury->id);
        } elseif ($type === 'out' && $request->filled('destination_treasury_id')) {
            $fromId = (int) ($request->input('treasury_id') ?: $treasury->id);
            $toId = (int) $request->input('destination_treasury_id');
        }

        // Execute inter-treasury transfer if both from and to are specified
        if ($fromId && $toId) {
            if ($fromId === $toId) {
                return back()->withInput()->with('error', 'لا يمكن التحويل لنفس الخزينة، يرجى اختيار خزينة أخرى للخصم أو التوريد.');
            }

            $fromTreasury = Treasury::findOrFail($fromId);
            $toTreasury = Treasury::findOrFail($toId);

            $this->authorizeTreasuryManage($fromTreasury);
            abort_if(!$toTreasury->is_active, 400, 'الخزينة المستلمة غير نشطة ولا يمكن التوريد إليها.');

            if ((float) $fromTreasury->current_balance < $amount) {
                return back()->withInput()->with('error', "رصيد الخزينة المخصوم منها ({$fromTreasury->name}) لا يكفي للتحويل. الرصيد الحالي: " . number_format($fromTreasury->current_balance, 2) . " ج.م");
            }

            \Illuminate\Support\Facades\DB::transaction(function () use ($fromTreasury, $toTreasury, $amount, $date, $desc, $validated) {
                // 1. الخصم من الخزينة المصدر (صادر)
                $fromTreasury->transactions()->create([
                    'type'             => 'out',
                    'amount'           => $amount,
                    'currency'         => $fromTreasury->currency,
                    'transaction_date' => $date,
                    'description'      => "تحويل وتوريد صادر إلى ({$toTreasury->name}) - {$desc}",
                    'created_by'       => auth()->id(),
                    'reference'        => $validated['reference'] ?? null,
                ]);
                $fromTreasury->updateBalance();

                // 2. التوريد والإيداع في الخزينة المستلمة (وارد)
                $toTreasury->transactions()->create([
                    'type'             => 'in',
                    'amount'           => $amount,
                    'currency'         => $toTreasury->currency,
                    'transaction_date' => $date,
                    'description'      => "تحويل وتوريد وارد من ({$fromTreasury->name}) - {$desc}",
                    'created_by'       => auth()->id(),
                    'reference'        => $validated['reference'] ?? null,
                ]);
                $toTreasury->updateBalance();
            });

            return redirect()->route('treasuries.show', $treasury)
                ->with('success', "تم تنفيذ الحركة بنجاح: تم خصم " . number_format($amount, 2) . " ج.م من ({$fromTreasury->name}) وتوريدها إلى ({$toTreasury->name}) وتحديث رصيد الخزنتين بالفعل.");
        }

        // Single treasury movement
        $targetTreasury = $treasury;
        if ($request->filled('treasury_id') && (int)$request->input('treasury_id') !== (int)$treasury->id) {
            $targetTreasury = Treasury::findOrFail((int)$request->input('treasury_id'));
        }

        $this->authorizeTreasuryManage($targetTreasury);
        $this->treasuryService->addTransaction($targetTreasury, $validated, (int)auth()->id());

        $typeLabel = $type === 'in' ? 'إيداع وارد' : 'صرف صادر';
        return redirect()->route('treasuries.show', $targetTreasury)
            ->with('success', "تم تسجيل حركة ({$typeLabel}) بنجاح في ({$targetTreasury->name}) وتحديث الرصيد الفعلي.");
    }

    public function dashboard(): View|RedirectResponse
    {
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php');
        }

        $accessibleTreasuryIds = auth()->user()?->getAccessibleTreasuryIds();
        $treasuries = Treasury::with('manager')
            ->withCount('transactions')
            ->accessibleForUser(auth()->user())
            ->get();

        $treasuryIds = $treasuries->pluck('id')->all();

        $stats = [
            'total_balance'            => $treasuries->sum('current_balance'),
            'total_treasuries'         => $treasuries->count(),
            'active_treasuries'        => $treasuries->where('is_active', true)->count(),
            'total_transactions_today' => 0,
            'total_in_today'           => 0,
            'total_out_today'          => 0,
        ];

        if (Schema::hasTable('treasury_transactions') && !empty($treasuryIds)) {
            $stats['total_transactions_today'] = \App\Models\TreasuryTransaction::whereIn('treasury_id', $treasuryIds)
                ->whereDate('transaction_date', Carbon::today())
                ->count();
            $stats['total_in_today'] = \App\Models\TreasuryTransaction::whereIn('treasury_id', $treasuryIds)
                ->whereDate('transaction_date', Carbon::today())
                ->where('type', 'in')
                ->sum('amount');
            $stats['total_out_today'] = \App\Models\TreasuryTransaction::whereIn('treasury_id', $treasuryIds)
                ->whereDate('transaction_date', Carbon::today())
                ->where('type', 'out')
                ->sum('amount');
        }

        $topTreasuries      = $treasuries->sortByDesc('current_balance')->take(5);
        $recentTransactions = collect();
        if (Schema::hasTable('treasury_transactions') && !empty($treasuryIds)) {
            $recentTransactions = \App\Models\TreasuryTransaction::with(['treasury', 'createdBy'])
                ->whereIn('treasury_id', $treasuryIds)
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        $monthlyData = $this->treasuryService->getMonthlyTrends($accessibleTreasuryIds);
        $insights    = $this->treasuryService->generateInsights($treasuries);

        return view('treasuries.dashboard', compact(
            'treasuries',
            'stats',
            'topTreasuries',
            'recentTransactions',
            'monthlyData',
            'insights'
        ));
    }

    public function syncAccounts(): RedirectResponse
    {
        $createdCount = $this->treasuryService->syncAccounts();

        return redirect()->route('treasuries.index')
            ->with('success', "تم مزامنة الحسابات بنجاح. تم إنشاء {$createdCount} حساب جديد.");
    }

    public function export(): void
    {
        $treasuries = Treasury::with('manager')
            ->accessibleForUser(auth()->user())
            ->get();
        $filename   = 'treasuries_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, ['الكود', 'الاسم', 'المدير', 'الموقع', 'العملة', 'الرصيد الافتتاحي', 'الرصيد الحالي', 'الحالة']);
        
        foreach ($treasuries as $treasury) {
            fputcsv($output, [
                $treasury->code,
                $treasury->name,
                $treasury->manager ? $treasury->manager->name : '-',
                $treasury->location ?? '-',
                $treasury->currency,
                $treasury->opening_balance,
                $treasury->current_balance,
                $treasury->is_active ? 'نشط' : 'غير نشط'
            ]);
        }
        
        fclose($output);
        exit;
    }
}
