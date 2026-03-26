@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-wallet2 text-primary"></i>
            إضافة راتب جديد
        </h4>
        <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('payrolls.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">الموظف</label>
                        <select name="user_id" class="form-select" id="userSelect" required>
                            <option value="">اختر موظف...</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" data-salary="{{ $u->salary }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6"><label class="form-label">الشهر</label><input name="month" type="month"
                            class="form-control" value="{{ date('Y-m') }}" required></div>
                    <div class="col-md-4">
                        <label class="form-label">المبلغ</label>
                        <div class="input-group">
                            <input name="amount" id="amountInput" class="form-control" required>
                            <select name="currency" class="form-select" style="max-width:110px">
                                <option value="EGP" selected>EGP</option>
                                <option value="USD">USD</option>
                                <option value="SAR">SAR</option>
                                <option value="EUR">EUR</option>
                                <option value="AED">AED</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6"><label class="form-label">تاريخ الدفع</label><input type="date" name="paid_at"
                            class="form-control" value="{{ date('Y-m-d') }}"></div>
                </div>
                <script>
                    document.getElementById('userSelect').addEventListener('change', function () {
                        const selectedOption = this.options[this.selectedIndex];
                        const salary = selectedOption.getAttribute('data-salary');
                        if (salary) {
                            document.getElementById('amountInput').value = salary;
                        }
                    });
                </script>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ الراتب
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection