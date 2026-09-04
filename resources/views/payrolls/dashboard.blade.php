@extends('layouts.app')

@section('content')
<div class="container-fluid py-4" dir="rtl">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h3 fw-bold mb-1">لوحة متابعة الرواتب</h1>
            <p class="text-muted mb-0">ملخص حالة الرواتب والمدفوعات للشهر الحالي</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('payrolls.index') }}" class="btn btn-outline-primary">سجل الرواتب</a>
            <a href="{{ route('payrolls.create') }}" class="btn btn-primary">إضافة راتب</a>
        </div>
    </div>

    <div class="row g-3 mb-4">
        @php
            $cards = [
                ['label' => 'إجمالي الرواتب', 'value' => $totalPayrolls, 'color' => 'primary', 'icon' => 'bi-people'],
                ['label' => 'الرواتب المدفوعة', 'value' => $paidPayrolls, 'color' => 'success', 'icon' => 'bi-check-circle'],
                ['label' => 'الرواتب المعلقة', 'value' => $pendingPayrolls, 'color' => 'warning', 'icon' => 'bi-clock'],
                ['label' => 'موظفون بدون راتب هذا الشهر', 'value' => $employeesWithoutPayroll, 'color' => 'danger', 'icon' => 'bi-person-exclamation'],
            ];
        @endphp
        @foreach($cards as $card)
            <div class="col-sm-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body d-flex align-items-center gap-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-{{ $card['color'] }} bg-opacity-10 text-{{ $card['color'] }}" style="width:48px;height:48px">
                            <i class="bi {{ $card['icon'] }} fs-4"></i>
                        </span>
                        <div>
                            <div class="text-muted small">{{ $card['label'] }}</div>
                            <div class="fs-3 fw-bold">{{ number_format($card['value']) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">إجمالي المدفوع</div>
                    <div class="h3 text-success fw-bold mb-0">{{ number_format($totalPaidAmount, 2) }} ج.م</div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="text-muted mb-2">إجمالي المعلق</div>
                    <div class="h3 text-warning fw-bold mb-0">{{ number_format($totalPendingAmount, 2) }} ج.م</div>
                </div>
            </div>
        </div>
    </div>

    @if(count($insights))
        <div class="row g-2 mb-4">
            @foreach($insights as $insight)
                <div class="col-lg-4">
                    <div class="alert alert-{{ $insight['type'] }} mb-0">
                        <i class="bi bi-{{ $insight['icon'] }} ms-2"></i>{{ $insight['message'] }}
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3">
            <h2 class="h5 fw-bold mb-0">أحدث الرواتب</h2>
        </div>
        <div class="table-responsive">
            <table class="table align-middle mb-0">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>الشهر</th>
                        <th>صافي الراتب</th>
                        <th>الحالة</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($latestPayrolls as $payroll)
                        <tr>
                            <td>{{ $payroll->user?->name ?? 'مستخدم محذوف' }}</td>
                            <td>{{ $payroll->month }}</td>
                            <td>{{ number_format((float) $payroll->net_amount, 2) }} {{ $payroll->currency }}</td>
                            <td>
                                <span class="badge bg-{{ $payroll->status === 'paid' ? 'success' : 'warning' }}">
                                    {{ $payroll->status === 'paid' ? 'مدفوع' : 'معلق' }}
                                </span>
                            </td>
                            <td><a href="{{ route('payrolls.show', $payroll) }}" class="btn btn-sm btn-outline-primary">عرض</a></td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">لا توجد رواتب مسجلة</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
