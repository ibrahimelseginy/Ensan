<?php

declare(strict_types=1);
namespace App\Http\Controllers;

use App\Models\Account;
use Illuminate\Http\Request;

final class AccountWebController extends Controller
{
    public function index()
    {
        $accounts = Account::whereNull('parent_id')->with('children')->orderBy('type')->orderBy('code')->get();
        $allAccounts = Account::with('parent')->orderBy('type')->orderBy('code')->paginate(50);
        
        // Add pending check for each account
        $allAccounts->each(function($a) {
            $a->pendingRequest = \App\Models\ChangeRequest::where('model_type', Account::class)
                ->where('model_id', $a->id)
                ->where('status', 'pending')
                ->first();
        });

        return view('accounts.index', compact('accounts', 'allAccounts'));
    }

    public function dashboard()
    {
        // Total Accounts by Type
        $totalAccounts = Account::count();
        $assetAccounts = Account::where('type', 'asset')->count();
        $liabilityAccounts = Account::where('type', 'liability')->count();
        $equityAccounts = Account::where('type', 'equity')->count();
        $revenueAccounts = Account::where('type', 'revenue')->count();
        $expenseAccounts = Account::where('type', 'expense')->count();

        // Journal Entries Stats
        $totalEntries = \App\Models\JournalEntry::count();
        $lockedEntries = \App\Models\JournalEntry::where('locked', true)->count();
        $unlockedEntries = $totalEntries - $lockedEntries;
        
        $currentMonth = now()->format('m');
        $currentYear = now()->format('Y');
        $entriesThisMonth = \App\Models\JournalEntry::whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->count();

        // Calculate Balances by Type
        $assetBalance = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'asset');
        })->sum(\DB::raw('debit - credit'));

        $liabilityBalance = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'liability');
        })->sum(\DB::raw('credit - debit'));

        $equityBalance = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'equity');
        })->sum(\DB::raw('credit - debit'));

        $revenueBalance = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'revenue');
        })->sum(\DB::raw('credit - debit'));

        $expenseBalance = \App\Models\JournalEntryLine::whereHas('account', function($q) {
            $q->where('type', 'expense');
        })->sum(\DB::raw('debit - credit'));

        // Net Income (Revenue - Expenses)
        $netIncome = $revenueBalance - $expenseBalance;

        // Monthly Trend (Last 6 Months)
        $trendLabels = [];
        $revenueTrendData = [];
        $expenseTrendData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = \Carbon\Carbon::now()->subMonths($i);
            $trendLabels[] = $date->translatedFormat('F');
            
            $monthRevenue = \App\Models\JournalEntryLine::whereHas('account', function($q) {
                $q->where('type', 'revenue');
            })->whereHas('journalEntry', function($q) use ($date) {
                $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
            })->sum(\DB::raw('credit - debit'));
            
            $monthExpense = \App\Models\JournalEntryLine::whereHas('account', function($q) {
                $q->where('type', 'expense');
            })->whereHas('journalEntry', function($q) use ($date) {
                $q->whereMonth('date', $date->month)->whereYear('date', $date->year);
            })->sum(\DB::raw('debit - credit'));
            
            $revenueTrendData[] = $monthRevenue;
            $expenseTrendData[] = $monthExpense;
        }

        // Top Accounts by Activity
        $topAccounts = Account::withCount('lines')
            ->orderByDesc('lines_count')
            ->limit(10)
            ->get();

        // Latest Journal Entries
        $latestEntries = \App\Models\JournalEntry::with('lines.account')
            ->orderByDesc('date')
            ->limit(10)
            ->get();

        // AI Insights
        $insights = [];
        
        if ($netIncome > 0) {
            $insights[] = ['type' => 'success', 'icon' => 'graph-up-arrow', 'message' => 'صافي الربح: ' . number_format($netIncome) . ' ج.م'];
        } elseif ($netIncome < 0) {
            $insights[] = ['type' => 'danger', 'icon' => 'graph-down-arrow', 'message' => 'صافي الخسارة: ' . number_format(abs($netIncome)) . ' ج.م'];
        }
        
        if ($assetBalance > $liabilityBalance) {
            $insights[] = ['type' => 'success', 'icon' => 'shield-check', 'message' => 'الأصول تتجاوز الالتزامات بـ ' . number_format($assetBalance - $liabilityBalance) . ' ج.م'];
        }
        
        if ($unlockedEntries > 10) {
            $insights[] = ['type' => 'warning', 'icon' => 'unlock', 'message' => 'يوجد ' . $unlockedEntries . ' قيد غير مقفل'];
        }
        
        if ($entriesThisMonth > 50) {
            $insights[] = ['type' => 'info', 'icon' => 'graph-up', 'message' => 'نشاط مرتفع! ' . $entriesThisMonth . ' قيد هذا الشهر'];
        }

        return view('accounts.dashboard', compact(
            'totalAccounts',
            'assetAccounts',
            'liabilityAccounts',
            'equityAccounts',
            'revenueAccounts',
            'expenseAccounts',
            'totalEntries',
            'lockedEntries',
            'unlockedEntries',
            'entriesThisMonth',
            'assetBalance',
            'liabilityBalance',
            'equityBalance',
            'revenueBalance',
            'expenseBalance',
            'netIncome',
            'trendLabels',
            'revenueTrendData',
            'expenseTrendData',
            'topAccounts',
            'latestEntries',
            'insights'
        ));
    }

    public function create()
    {
        $parents = Account::orderBy('code')->get();
        return view('accounts.create', compact('parents'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|string|unique:accounts,code',
            'name' => 'required|string',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string'
        ]);
        $executor = function () use ($data) {
             return Account::create($data);
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Account::class,
            null,
            'create',
            $data,
            $executor
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب إضافة الحساب للموافقة.');
        }

        return redirect()->route('accounts.index');
    }

    public function show(Account $account)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', Account::class)
            ->where('model_id', $account->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        $account->load('parent', 'children');
        $lines = $account->lines()->with('journalEntry')->orderByDesc('id')->paginate(20);
        $pendingRequest = \App\Models\ChangeRequest::where('model_type', Account::class)
            ->where('model_id', $account->id)
            ->where('status', 'pending')
            ->first();
        return view('accounts.show', compact('account', 'lines', 'pendingRequest'));
    }

    public function edit(Account $account)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', Account::class)
            ->where('model_id', $account->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        $parents = Account::where('id', '<>', $account->id)->orderBy('code')->get();
        return view('accounts.edit', compact('account', 'parents'));
    }

    public function update(Request $request, Account $account)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', Account::class)
            ->where('model_id', $account->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        $data = $request->validate([
            'code' => 'required|string|unique:accounts,code,' . $account->id,
            'name' => 'required|string',
            'type' => 'required|in:asset,liability,equity,revenue,expense',
            'parent_id' => 'nullable|exists:accounts,id',
            'description' => 'nullable|string'
        ]);

        $executor = function () use ($account, $data) {
            $account->update($data);
            return $account;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Account::class,
            $account->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الحساب للموافقة');
        }

        return redirect()->route('accounts.index')->with('success', 'تم تحديث الحساب بنجاح');
    }

    public function destroy(Account $account)
    {
        $pending = \App\Models\ChangeRequest::where('model_type', Account::class)
            ->where('model_id', $account->id)
            ->where('status', 'pending')
            ->first();

        if ($pending) {
            return redirect()->route('change-requests.index')->with('info', 'هذا الحساب لديه طلب مراجعة حالياً');
        }

        if ($account->children()->exists() || $account->lines()->exists()) {
            return back()->withErrors(['error' => 'لا يمكن حذف حساب لديه أبناء أو قيود']);
        }

        $executor = function () use ($account) {
            $account->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Account::class,
            $account->id,
            'delete',
            [
                'note' => 'حذف حساب مالي',
                'name' => $account->name,
                'code' => $account->code
            ],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الحساب للموافقة');
        }

        return redirect()->route('accounts.index')->with('success', 'تم حذف الحساب بنجاح');
    }
}
