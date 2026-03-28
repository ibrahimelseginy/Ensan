<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Models\User;
use App\Models\ChangeRequest;
use App\Services\PayrollService;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

final readonly class PayrollWebController extends Controller
{
    public function __construct(
        private PayrollService $payrollService
    ) {}

    public function index(): View
    {
        $payrolls = $this->payrollService->getAllPayrolls(50);
        return view('payrolls.index', compact('payrolls'));
    }

    public function create(): View
    {
        $users = User::where('is_volunteer', false)
            ->where('is_employee', true)
            ->where('active', true)
            ->orderBy('name')
            ->get();
        return view('payrolls.create', compact('users'));
    }

    public function store(StorePayrollRequest $request): RedirectResponse
    {
        $result = $this->payrollService->createPayroll($request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('payrolls.index')->with('success', 'تم إرسال طلب إنشاء الراتب للموافقة');
        }

        return redirect()->route('payrolls.index')->with('success', 'تم إنشاء الراتب بنجاح');
    }

    public function show(Payroll $payroll): View
    {
        $payroll->load(['user', 'journalEntry.lines.account']);
        return view('payrolls.show', compact('payroll'));
    }

    public function edit(Payroll $payroll): View
    {
        $users = User::where('is_volunteer', false)
            ->where('is_employee', true)
            ->orderBy('name')
            ->get();
        return view('payrolls.edit', compact('payroll', 'users'));
    }

    public function update(UpdatePayrollRequest $request, Payroll $payroll): RedirectResponse
    {
        $result = $this->payrollService->updatePayroll($payroll, $request->validated());

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب تعديل الراتب للموافقة');
        }

        return redirect()->route('payrolls.show', $payroll)->with('success', 'تم تحديث الراتب بنجاح');
    }

    public function destroy(Payroll $payroll): RedirectResponse
    {
        $result = $this->payrollService->deletePayroll($payroll);

        if ($result instanceof ChangeRequest) {
            return redirect()->route('change-requests.index')->with('success', 'تم إرسال طلب حذف الراتب للموافقة');
        }

        return redirect()->route('payrolls.index')->with('success', 'تم حذف الراتب وعكس القيد المحاسبي بنجاح');
    }

    public function createJournalEntry(Payroll $payroll): RedirectResponse
    {
        try {
            $this->payrollService->createAccountingEntry($payroll);
            return back()->with('success', 'تم إنشاء القيد المحاسبي بنجاح');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'فشل إنشاء القيد المحاسبي: ' . $e->getMessage()]);
        }
    }

    public function dashboard(): View
    {
        $data = $this->payrollService->getDashboardData();
        return view('payrolls.dashboard', $data);
    }
}
