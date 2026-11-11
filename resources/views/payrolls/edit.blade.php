@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-wallet2 text-primary"></i>
            تعديل الراتب
        </h4>
        <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body p-4">
            <div class="alert alert-info py-2 px-3 small mb-4">
                <i class="bi bi-info-circle me-1"></i> يتم احتساب إجمالي المستحقات (الأساسي + الحوافز) وصافي الراتب (المستحقات - الخصومات) تلقائياً طبقاً لنموذج الرواتب المعتمد.
            </div>
            <form method="POST" action="{{ route('payrolls.update', $payroll) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الموظف</label>
                        <input type="text" class="form-control" value="{{ $payroll->user?->name }} ({{ $payroll->user?->job_title ?: 'موظف' }})" disabled>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الشهر</label>
                        <input name="month" class="form-control font-monospace" value="{{ $payroll->month }}" required>
                    </div>

                    {{-- Salaries Components (الراتب الأساسي - الحوافز - إجمالي المستحقات - الخصومات - صافي الراتب) --}}
                    <div class="col-md-4">
                        <label class="form-label fw-bold">الراتب الأساسي <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input name="amount" id="amountInput" type="number" step="0.01" min="0" class="form-control font-monospace fw-bold payroll-calc-input" value="{{ $payroll->amount }}" required>
                            <select name="currency" class="form-select" style="max-width:100px">
                                <option value="EGP" @selected($payroll->currency == 'EGP')>EGP</option>
                                <option value="USD" @selected($payroll->currency == 'USD')>USD</option>
                                <option value="SAR" @selected($payroll->currency == 'SAR')>SAR</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-success">الحوافز والمكافآت (+)</label>
                        <div class="input-group">
                            <input name="bonuses" id="bonusesInput" type="number" step="0.01" min="0" class="form-control font-monospace payroll-calc-input" value="{{ $payroll->bonuses ?? '0.00' }}">
                            <span class="input-group-text text-muted small">ج.م</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-primary">إجمالي المستحقات (الأساسي + الحوافز)</label>
                        <div class="input-group">
                            <input type="text" id="totalEntitlementsInput" class="form-control font-monospace fw-bold bg-primary-subtle text-primary" readonly value="0.00">
                            <span class="input-group-text bg-primary text-white small">محسوب</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-danger">الخصومات والجزاءات (-)</label>
                        <div class="input-group">
                            <input name="deductions" id="deductionsInput" type="number" step="0.01" min="0" class="form-control font-monospace payroll-calc-input" value="{{ $payroll->deductions ?? '0.00' }}">
                            <span class="input-group-text text-muted small">ج.م</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-success">صافي الراتب المستحق للصرف (=)</label>
                        <div class="input-group">
                            <input type="text" id="netAmountInput" class="form-control font-monospace fw-bolder fs-5 bg-success-subtle text-success" readonly value="0.00">
                            <span class="input-group-text bg-success text-white small">الصافي</span>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تاريخ الدفع الفعلي</label>
                        <input type="date" name="paid_at" class="form-control" value="{{ optional($payroll->paid_at)->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">ملاحظات الراتب</label>
                        <input type="text" name="notes" class="form-control" value="{{ $payroll->notes }}" placeholder="أي تفاصيل أو بدلات إضافية...">
                    </div>
                </div>

                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const amountInput = document.getElementById('amountInput');
                        const bonusesInput = document.getElementById('bonusesInput');
                        const deductionsInput = document.getElementById('deductionsInput');
                        const totalEntitlementsInput = document.getElementById('totalEntitlementsInput');
                        const netAmountInput = document.getElementById('netAmountInput');

                        function recalcPayroll() {
                            const basic = parseFloat(amountInput.value) || 0;
                            const bonuses = parseFloat(bonusesInput.value) || 0;
                            const deductions = parseFloat(deductionsInput.value) || 0;

                            const entitlements = basic + bonuses;
                            const net = entitlements - deductions;

                            totalEntitlementsInput.value = entitlements.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                            netAmountInput.value = net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
                        }

                        document.querySelectorAll('.payroll-calc-input').forEach(function (inp) {
                            inp.addEventListener('input', recalcPayroll);
                        });

                        recalcPayroll();
                    });
                </script>

                <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-right me-1"></i> رجوع
                    </a>
                    <button class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

