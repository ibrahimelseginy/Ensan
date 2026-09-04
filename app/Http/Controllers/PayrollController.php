<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Payroll;
use App\Services\PayrollService;
use App\Http\Requests\StorePayrollRequest;
use App\Http\Requests\UpdatePayrollRequest;
use Illuminate\Http\Response;
use Illuminate\Pagination\LengthAwarePaginator;

final class PayrollController extends Controller
{
    public function __construct(
        private PayrollService $payrollService
    ) {}

    public function index(): LengthAwarePaginator
    {
        return $this->payrollService->getAllPayrolls(50);
    }

    public function store(StorePayrollRequest $request): mixed
    {
        return $this->payrollService->createPayroll($request->validated());
    }

    public function show(Payroll $payroll): Payroll
    {
        return $payroll->load('user');
    }

    public function update(UpdatePayrollRequest $request, Payroll $payroll): mixed
    {
        return $this->payrollService->updatePayroll($payroll, $request->validated());
    }

    public function destroy(Payroll $payroll): Response
    {
        $this->payrollService->deletePayroll($payroll);
        return response()->noContent();
    }
}
