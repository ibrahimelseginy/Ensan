<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Treasury;
use App\Models\TreasuryTransaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

final class TreasuryController extends Controller
{
    /**
     * Check if treasuries table exists
     */
    protected function checkTableExists()
    {
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php')
                ->with('error', 'جدول الخزائن غير موجود. يرجى تشغيل التثبيت أولاً.');
        }
        return null;
    }

    /**
     * Display a listing of treasuries
     */
    public function index()
    {
        // Check if table exists
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php');
        }

        $treasuries = Treasury::with('manager')
            ->withCount('transactions')
            ->get();

        $pendingRequests = \App\Models\ChangeRequest::where('model_type', Treasury::class)
            ->where('status', 'pending')
            ->get()
            ->groupBy('model_id');

        // Calculate statistics
        $totalBalance = $treasuries->sum('current_balance');
        $activeTreasuries = $treasuries->where('is_active', true)->count();
        $totalTreasuries = $treasuries->count();

        // Recent transactions
        $recentTransactions = collect();
        if (Schema::hasTable('treasury_transactions')) {
            $recentTransactions = TreasuryTransaction::with(['treasury', 'createdBy', 'donation'])
                ->orderByDesc('transaction_date')
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        // Monthly trends (last 6 months)
        $monthlyData = $this->getMonthlyTrends();

        // AI Insights
        $insights = $this->generateInsights($treasuries);

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

    /**
     * Show the form for creating a new treasury
     */
    public function create()
    {
        $managers = User::orderBy('name')->get();
        return view('treasuries.create', compact('managers'));
    }

    /**
     * Store a newly created treasury
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:treasuries,code',
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'opening_balance' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $data['current_balance'] = $data['opening_balance'];
        $data['is_active'] = $request->boolean('is_active');

        $treasury = Treasury::create($data);

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم إنشاء الخزينة بنجاح');
    }

    /**
     * Display the specified treasury
     */
    public function show(Treasury $treasury)
    {
        $treasury->load('manager', 'transactions.createdBy', 'donations');

        // Statistics
        $stats = [
            'total_in' => $treasury->transactions()->where('type', 'in')->sum('amount'),
            'total_out' => $treasury->transactions()->where('type', 'out')->sum('amount'),
            'total_transactions' => $treasury->transactions()->count(),
            'total_donations' => $treasury->donations()->count(),
            'donations_amount' => $treasury->donations()->sum('amount')
        ];

        // Recent transactions
        $recentTransactions = $treasury->transactions()
            ->with('createdBy', 'donation')
            ->orderByDesc('transaction_date')
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        // Monthly chart data
        $monthlyData = $this->getTreasuryMonthlyData($treasury->id);

        return view('treasuries.show', compact('treasury', 'stats', 'recentTransactions', 'monthlyData'));
    }

    /**
     * Show the form for editing the specified treasury
     */
    public function edit(Treasury $treasury)
    {
        $managers = User::orderBy('name')->get();
        return view('treasuries.edit', compact('treasury', 'managers'));
    }

    /**
     * Update the specified treasury
     */
    public function update(Request $request, Treasury $treasury)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:255|unique:treasuries,code,' . $treasury->id,
            'description' => 'nullable|string',
            'manager_id' => 'nullable|exists:users,id',
            'location' => 'nullable|string',
            'currency' => 'required|string|max:10',
            'is_active' => 'boolean'
        ]);

        $executor = function () use ($treasury, $data) {
            $data['is_active'] = request()->boolean('is_active');
            $treasury->update($data);
            return $treasury;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Treasury::class,
            $treasury->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('treasuries.show', $treasury)
                ->with('success', 'تم إرسال طلب تعديل الخزينة للموافقة');
        }

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم تحديث الخزينة بنجاح');
    }

    /**
     * Remove the specified treasury
     */
    public function destroy(Treasury $treasury)
    {
        if ($treasury->transactions()->exists()) {
            return back()->with('error', 'لا يمكن حذف الخزينة لأنها تحتوي على حركات مالية. يمكنك تجميدها بدلاً من ذلك.');
        }

        $executor = function () use ($treasury) {
            $treasury->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Treasury::class,
            $treasury->id,
            'delete',
            ['note' => 'حذف خزينة'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('treasuries.index')
                ->with('success', 'تم إرسال طلب حذف الخزينة للموافقة');
        }

        $treasury->delete();
        return redirect()->route('treasuries.index')
            ->with('success', 'تم حذف الخزينة بنجاح');
    }

    /**
     * Add transaction to treasury
     */
    public function addTransaction(Request $request, Treasury $treasury)
    {
        $data = $request->validate([
            'type' => 'required|in:in,out',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'transaction_date' => 'required|date',
            'reference' => 'nullable|string'
        ]);

        $data['treasury_id'] = $treasury->id;
        $data['created_by'] = auth()->id();
        $data['currency'] = $treasury->currency;

        $transaction = TreasuryTransaction::create($data);

        // Update treasury balance
        $treasury->updateBalance();

        return redirect()->route('treasuries.show', $treasury)
            ->with('success', 'تم إضافة الحركة بنجاح');
    }

    /**
     * Treasury dashboard
     */
    public function dashboard()
    {
        // Check if table exists
        if (!Schema::hasTable('treasuries')) {
            return redirect('/create_treasuries_tables.php');
        }

        $treasuries = Treasury::with('manager')->get();
        
        $stats = [
            'total_balance' => $treasuries->sum('current_balance'),
            'total_treasuries' => $treasuries->count(),
            'active_treasuries' => $treasuries->where('is_active', true)->count(),
            'total_transactions_today' => 0,
            'total_in_today' => 0,
            'total_out_today' => 0,
        ];

        if (Schema::hasTable('treasury_transactions')) {
            $stats['total_transactions_today'] = TreasuryTransaction::whereDate('transaction_date', today())->count();
            $stats['total_in_today'] = TreasuryTransaction::whereDate('transaction_date', today())->where('type', 'in')->sum('amount');
            $stats['total_out_today'] = TreasuryTransaction::whereDate('transaction_date', today())->where('type', 'out')->sum('amount');
        }

        // Top treasuries by balance
        $topTreasuries = $treasuries->sortByDesc('current_balance')->take(5);

        // Recent transactions
        $recentTransactions = collect();
        if (Schema::hasTable('treasury_transactions')) {
            $recentTransactions = TreasuryTransaction::with(['treasury', 'createdBy'])
                ->orderByDesc('created_at')
                ->limit(10)
                ->get();
        }

        // Monthly trends
        $monthlyData = $this->getMonthlyTrends();

        // AI Insights
        $insights = $this->generateInsights($treasuries);

        return view('treasuries.dashboard', compact(
            'treasuries',
            'stats',
            'topTreasuries',
            'recentTransactions',
            'monthlyData',
            'insights'
        ));
    }

    /**
     * Get monthly trends
     */
    protected function getMonthlyTrends()
    {
        $labels = [];
        $inData = [];
        $outData = [];
        $balanceData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('F');

            $monthIn = TreasuryTransaction::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'in')
                ->sum('amount');

            $monthOut = TreasuryTransaction::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'out')
                ->sum('amount');

            $inData[] = $monthIn;
            $outData[] = $monthOut;
            $balanceData[] = $monthIn - $monthOut;
        }

        return [
            'labels' => $labels,
            'in' => $inData,
            'out' => $outData,
            'balance' => $balanceData
        ];
    }

    /**
     * Get treasury monthly data
     */
    protected function getTreasuryMonthlyData($treasuryId)
    {
        $labels = [];
        $inData = [];
        $outData = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->translatedFormat('F');

            $monthIn = TreasuryTransaction::where('treasury_id', $treasuryId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'in')
                ->sum('amount');

            $monthOut = TreasuryTransaction::where('treasury_id', $treasuryId)
                ->whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->where('type', 'out')
                ->sum('amount');

            $inData[] = $monthIn;
            $outData[] = $monthOut;
        }

        return [
            'labels' => $labels,
            'in' => $inData,
            'out' => $outData
        ];
    }

    /**
     * Generate AI insights
     */
    protected function generateInsights($treasuries)
    {
        $insights = [];

        // Low balance warning
        foreach ($treasuries as $treasury) {
            if ($treasury->current_balance < 1000 && $treasury->is_active) {
                $insights[] = [
                    'type' => 'warning',
                    'icon' => 'exclamation-triangle',
                    'message' => "رصيد {$treasury->name} منخفض: " . number_format($treasury->current_balance, 2) . " {$treasury->currency}"
                ];
            }
        }

        // High balance notification
        foreach ($treasuries as $treasury) {
            if ($treasury->current_balance > 100000) {
                $insights[] = [
                    'type' => 'info',
                    'icon' => 'info-circle',
                    'message' => "رصيد {$treasury->name} مرتفع: " . number_format($treasury->current_balance, 2) . " {$treasury->currency}"
                ];
            }
        }

        // Inactive treasuries
        $inactiveTreasuries = $treasuries->where('is_active', false)->count();
        if ($inactiveTreasuries > 0) {
            $insights[] = [
                'type' => 'info',
                'icon' => 'pause-circle',
                'message' => "يوجد {$inactiveTreasuries} خزينة غير نشطة"
            ];
        }

        // Today's activity
        $todayTransactions = TreasuryTransaction::whereDate('transaction_date', today())->count();
        if ($todayTransactions > 10) {
            $insights[] = [
                'type' => 'success',
                'icon' => 'chart-line',
                'message' => "نشاط مرتفع اليوم: {$todayTransactions} حركة"
            ];
        }

        return $insights;
    }

    /**
     * Sync treasury accounts
     */
    public function syncAccounts()
    {
        $treasuries = Treasury::all();
        $createdCount = 0;

        foreach ($treasuries as $treasury) {
            $accountCode = $this->getAccountCodeForTreasury($treasury);
            
            $account = \App\Models\Account::where('code', $accountCode)->first();
            
            if (!$account) {
                \App\Models\Account::create([
                    'name' => $treasury->name,
                    'code' => $accountCode,
                    'type' => 'asset',
                    'is_active' => true,
                    'description' => 'حساب تلقائي للخزينة: ' . $treasury->code
                ]);
                $createdCount++;
            }
        }

        return redirect()->route('treasuries.index')
            ->with('success', "تم مزامنة الحسابات بنجاح. تم إنشاء {$createdCount} حساب جديد.");
    }

    /**
     * Get account code based on treasury type or ID
     */
    protected function getAccountCodeForTreasury($treasury)
    {
        // Simple logic for now: 110 + ID padded
        // You can make this smarter based on type if needed
        return '110' . str_pad($treasury->id, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Export treasuries data
     */
    public function export()
    {
        $treasuries = Treasury::with('manager')->get();

        $filename = 'treasuries_' . date('Y-m-d_H-i-s') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM
        
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
