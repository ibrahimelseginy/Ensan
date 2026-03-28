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

final readonly class TreasuryController extends Controller
{
    public function __construct(
        private TreasuryService $treasuryService
    ) {}

    public function index(): View|RedirectResponse
    {
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php');
        }

        $treasuries = $this->treasuryService->getAllTreasuries();
        
        $pendingRequests = \App\Models\ChangeRequest::where('model_type', Treasury::class)
            ->where('status', 'pending')
            ->get()
            ->groupBy('model_id');

        $totalBalance     = $treasuries->sum('current_balance');
        $activeTreasuries = $treasuries->where('is_active', true)->count();
        $totalTreasuries  = $treasuries->count();

        $recentTransactions = collect();
        if (Schema::hasTable('treasury_transactions')) {
            $recentTransactions = \App\Models\TreasuryTransaction::with(['treasury', 'createdBy', 'donation'])
                ->orderByDesc('transaction_date')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        $monthlyData = $this->treasuryService->getMonthlyTrends();
        $insights    = $this->treasuryService->generateInsights($treasuries);

        return view('treasuries.index', compact(
            'treasuries',
            'pendingRequests',
            'totalBalance',
            'activeTreasuries',
            'totalTreasuries',
            'recentTransactions',
            'monthlyData',
            'insights'
        ));
    }

    public function create(): View
    {
        $managers = User::orderBy('name')->get();
        return view('treasuries.create', compact('managers'));
    }

    public function store(StoreTreasuryRequest $request): RedirectResponse
    {
        $treasury = $this->treasuryService->createTreasury($request->validated());

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم إنشاء الخزينة بنجاح');
    }

    public function show(Treasury $treasury): View
    {
        $treasury->load('manager', 'transactions.createdBy', 'donations');

        $stats = [
            'total_in'           => $treasury->transactions()->where('type', 'in')->sum('amount'),
            'total_out'          => $treasury->transactions()->where('type', 'out')->sum('amount'),
            'total_transactions' => $treasury->transactions()->count(),
            'total_donations'    => $treasury->donations()->count(),
            'donations_amount'   => $treasury->donations()->sum('amount')
        ];

        $recentTransactions = $treasury->transactions()
            ->with('createdBy', 'donation')
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $monthlyData = $this->treasuryService->getTreasuryMonthlyData($treasury->id);

        return view('treasuries.show', compact('treasury', 'stats', 'recentTransactions', 'monthlyData'));
    }

    public function edit(Treasury $treasury): View
    {
        $managers = User::orderBy('name')->get();
        return view('treasuries.edit', compact('treasury', 'managers'));
    }

    public function update(UpdateTreasuryRequest $request, Treasury $treasury): RedirectResponse
    {
        $data     = $request->validated();
        $executor = function () use ($treasury, $data) {
            $data['is_active'] = request()->boolean('is_active');
            $this->treasuryService->updateTreasury($treasury, $data);
            return $treasury;
        };

        $result = ChangeRequestService::handleRequest(
            Treasury::class,
            $treasury->id,
            'update',
            $data,
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

    public function destroy(Treasury $treasury): RedirectResponse
    {
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
        $this->treasuryService->addTransaction($treasury, $request->validated(), (int)auth()->id());

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم إضافة الحركة بنجاح');
    }

    public function dashboard(): View|RedirectResponse
    {
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php');
        }

        $treasuries = $this->treasuryService->getAllTreasuries();
        
        $stats = [
            'total_balance'            => $treasuries->sum('current_balance'),
            'total_treasuries'         => $treasuries->count(),
            'active_treasuries'        => $treasuries->where('is_active', true)->count(),
            'total_transactions_today' => 0,
            'total_in_today'           => 0,
            'total_out_today'          => 0,
        ];

        if (Schema::hasTable('treasury_transactions')) {
            $stats['total_transactions_today'] = \App\Models\TreasuryTransaction::whereDate('transaction_date', Carbon::today())->count();
            $stats['total_in_today']           = \App\Models\TreasuryTransaction::whereDate('transaction_date', Carbon::today())->where('type', 'in')->sum('amount');
            $stats['total_out_today']          = \App\Models\TreasuryTransaction::whereDate('transaction_date', Carbon::today())->where('type', 'out')->sum('amount');
        }

        $topTreasuries      = $treasuries->sortByDesc('current_balance')->take(5);
        $recentTransactions = collect();
        if (Schema::hasTable('treasury_transactions')) {
            $recentTransactions = \App\Models\TreasuryTransaction::with(['treasury', 'createdBy'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        $monthlyData = $this->treasuryService->getMonthlyTrends();
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
        $treasuries = $this->treasuryService->getAllTreasuries();
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
