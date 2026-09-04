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
        <div class="card-body">
            <form method="POST" action="{{ route('payrolls.update', $payroll) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">الموظف</label><select class="form-select" disabled>
                            <option>{{ $payroll->user?->name }}</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">الشهر</label><input name="month" class="form-control"
                            value="{{ $payroll->month }}"></div>
                    <div class="col-md-4">
                        <label class="form-label">المبلغ</label>
                        <div class="input-group">
                            <input name="amount" type="number" step="1" class="form-control" value="{{ $payroll->amount ? (int)$payroll->amount : '' }}">
                            <select name="currency" class="form-select" style="max-width:110px">
                                <option value="EGP" @selected($payroll->currency == 'EGP')>EGP</option>
                                <option value="USD" @selected($payroll->currency == 'USD')>USD</option>
                                <option value="SAR" @selected($payroll->currency == 'SAR')>SAR</option>
                                <option value="EUR" @selected($payroll->currency == 'EUR')>EUR</option>
                                <option value="AED" @selected($payroll->currency == 'AED')>AED</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label">تاريخ الدفع</label><input type="date" name="paid_at"
                            class="form-control" value="{{ optional($payroll->paid_at)->format('Y-m-d') }}"></div>
                </div>
                <div class="mt-3"><button class="btn btn-primary">حفظ</button><a
                        href="{{ route('payrolls.show', $payroll) }}" class="btn btn-light">رجوع</a></div>
            </form>
        </div>
@endsection

