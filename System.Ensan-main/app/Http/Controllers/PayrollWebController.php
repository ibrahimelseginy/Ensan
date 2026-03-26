<?php
namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Services\PayrollAccountingService;
use Illuminate\Http\Request;

class PayrollWebController extends Controller
{
    protected $payrollAccountingService;

    public function __construct(PayrollAccountingService $payrollAccountingService)
    {
        $this->payrollAccountingService = $payrollAccountingService;
    }

    public function index()
    {
        $payrolls = Payroll::with(['user', 'journalEntry'])
            ->orderByDesc('id')
            ->paginate(50);
        return view('payrolls.index', compact('payrolls'));
    }

    public function create()
    {
        $users = User::where('is_volunteer', false)
            ->where('is_employee', true)
            ->orderBy('name')
            ->get();
        return view('payrolls.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'month' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string',
            'create_journal_entry' => 'nullable|boolean'
        ]);

        // Set status based on paid_at
        $data['status'] = $request->paid_at ? 'paid' : 'pending';

        $executor = function () use ($data) {
            $payroll = Payroll::create($data);
            $payroll->calculateNetAmount();
            $payroll->save();

            if (request('create_journal_entry')) {
                try {
                    $this->payrollAccountingService->createJournalEntry($payroll);
                } catch (\Exception $e) {}
            }
            return $payroll;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Payroll::class,
            null,
            'create',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('payrolls.index')->with('success', 'تم إرسال طلب إنشاء الراتب للموافقة');
        }

        return redirect()->route('payrolls.index')->with('success', 'تم إنشاء الراتب بنجاح');

        return redirect()->route('payrolls.index');
    }

    public function show(Payroll $payroll)
    {
        $payroll->load(['user', 'journalEntry.lines.account']);
        return view('payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll)
    {
        $users = User::where('is_volunteer', false)
            ->where('is_employee', true)
            ->orderBy('name')
            ->get();
        return view('payrolls.edit', compact('payroll', 'users'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $data = $request->validate([
            'month' => 'nullable|string',
            'amount' => 'nullable|numeric|min:0',
            'bonuses' => 'nullable|numeric|min:0',
            'deductions' => 'nullable|numeric|min:0',
            'currency' => 'nullable|string',
            'paid_at' => 'nullable|date',
            'notes' => 'nullable|string'
        ]);

        $executor = function () use ($payroll, $data, $request) {
            if ($request->has('paid_at')) {
                // If marking as paid and has journal entry
                if ($request->paid_at && !$payroll->paid_at && $payroll->journal_entry_id) {
                    try {
                        $this->payrollAccountingService->markAsPaid($payroll);
                    } catch (\Exception $e) {}
                }
            }
            $payroll->update($data);
            $payroll->calculateNetAmount();
            $payroll->save();
            return $payroll;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Payroll::class,
            $payroll->id,
            'update',
            $data,
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الراتب للموافقة');
        }

        return redirect()->route('payrolls.show', $payroll)->with('success', 'تم تحديث الراتب بنجاح');
    }

    public function destroy(Payroll $payroll)
    {
        $executor = function () use ($payroll) {
            // Reverse journal entry if exists
            if ($payroll->journal_entry_id) {
                try {
                    $this->payrollAccountingService->reverseJournalEntry($payroll);
                } catch (\Exception $e) {}
            }
            $payroll->delete();
            return true;
        };

        $result = \App\Services\ChangeRequestService::handleRequest(
            \App\Models\Payroll::class,
            $payroll->id,
            'delete',
            ['note' => 'حذف راتب'],
            $executor,
            true // Force Request
        );

        if ($result instanceof \App\Models\ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الراتب للموافقة');
        }

        return redirect()->route('payrolls.index')->with('success', 'تم حذف الراتب وعكس القيد المحاسبي بنجاح');
    }

    /**
     * Create journal entry for existing payroll
     */
    public function createJournalEntry(Payroll $payroll)
    {
        if ($payroll->journal_entry_id) {
            return back()->withErrors(['error' => 'يوجد قيد محاسبي مرتبط بالفعل']);
        }

        try {
            $this->payrollAccountingService->createJournalEntry($payroll);
            return back()->with('success', 'تم إنشاء القيد المحاسبي بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'فشل إنشاء القيد المحاسبي: ' . $e->getMessage()]);
        }
    }

    /**
     * Dashboard for payroll overview
     */
    public function dashboard()
    {
        $currentMonth = now()->format('Y-m');
        $currentYear = now()->year;

        // Statistics
        $totalPayrolls = Payroll::count();
        $paidPayrolls = Payroll::where('status', 'paid')->count();
        $pendingPayrolls = Payroll::where('status', 'pending')->count();
        
        $totalPaidAmount = Payroll::where('status', 'paid')->sum('net_amount');
        $totalPendingAmount = Payroll::where('status', 'pending')->sum('net_amount');
        
        $payrollsThisMonth = Payroll::where('month', 'LIKE', $currentMonth . '%')->count();
        $amountThisMonth = Payroll::where('month', 'LIKE', $currentMonth . '%')->sum('net_amount');

        // Monthly trend (last 6 months)
        $trendLabels = [];
        $trendData = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $trendLabels[] = $date->translatedFormat('F');
            $monthKey = $date->format('Y-m');
            $trendData[] = Payroll::where('month', 'LIKE', $monthKey . '%')->sum('net_amount');
        }

        // Latest payrolls
        $latestPayrolls = Payroll::with(['user', 'journalEntry'])
            ->orderByDesc('created_at')
            ->limit(10)
            ->get();

        // Employees without payroll this month
        $employeesWithoutPayroll = User::where('is_employee', true)
            ->where('active', true)
            ->whereDoesntHave('payrolls', function($q) use ($currentMonth) {
                $q->where('month', 'LIKE', $currentMonth . '%');
            })
            ->count();

        // AI Insights
        $insights = [];
        
        if ($pendingPayrolls > 0) {
            $insights[] = ['type' => 'warning', 'icon' => 'clock', 'message' => $pendingPayrolls . ' راتب معلق بقيمة ' . number_format($totalPendingAmount) . ' ج.م'];
        }
        
        if ($employeesWithoutPayroll > 0) {
            $insights[] = ['type' => 'info', 'icon' => 'people', 'message' => $employeesWithoutPayroll . ' موظف لم يتم صرف راتبه هذا الشهر'];
        }
        
        if ($payrollsThisMonth > 0) {
            $insights[] = ['type' => 'success', 'icon' => 'cash-stack', 'message' => 'إجمالي رواتب هذا الشهر: ' . number_format($amountThisMonth) . ' ج.م'];
        }

        return view('payrolls.dashboard', compact(
            'totalPayrolls',
            'paidPayrolls',
            'pendingPayrolls',
            'totalPaidAmount',
            'totalPendingAmount',
            'payrollsThisMonth',
            'amountThisMonth',
            'trendLabels',
            'trendData',
            'latestPayrolls',
            'employeesWithoutPayroll',
            'insights'
        ));
    }
}
