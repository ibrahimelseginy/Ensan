@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-wallet2 text-primary"></i>
            تفاصيل الراتب
        </h4>
        <div class="btn-group">
            @if(auth()->check())
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <a class="btn btn-outline-primary" href="{{ route('payrolls.edit', $payroll) }}">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                @else
                    <a class="btn btn-outline-warning" href="{{ route('payrolls.edit', $payroll) }}">
                        <i class="bi bi-pencil-square me-1"></i> طلب تعديل
                    </a>
                @endif
            @endif
            <a href="{{ route('payrolls.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Info Card --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <i class="bi bi-info-circle"></i>
                        <h5 class="mb-0">معلومات الراتب</h5>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">الموظف</span>
                                <span class="info-value">
                                    @if($payroll->user)
                                        <a href="{{ route('users.show', $payroll->user) }}">{{ $payroll->user->name }}</a>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">الشهر</span>
                                <span class="info-value">{{ $payroll->month }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">الراتب الأساسي</span>
                                <span class="info-value font-monospace fw-bold">
                                    {{ number_format($payroll->amount, 2) }}
                                    <small class="text-muted">{{ $payroll->currency }}</small>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">الحوافز والمكافآت</span>
                                <span class="info-value font-monospace text-success fw-bold">
                                    +{{ number_format($payroll->bonuses ?? 0, 2) }} {{ $payroll->currency }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">إجمالي المستحقات</span>
                                <span class="info-value font-monospace text-primary fw-bold">
                                    {{ number_format(($payroll->amount + ($payroll->bonuses ?? 0)), 2) }} {{ $payroll->currency }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">الخصومات والجزاءات</span>
                                <span class="info-value font-monospace text-danger fw-bold">
                                    -{{ number_format($payroll->deductions ?? 0, 2) }} {{ $payroll->currency }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row bg-success-subtle p-2 rounded-3">
                                <span class="info-label fw-bold text-success">صافي الراتب المنصرف</span>
                                <span class="info-value font-monospace fs-5 fw-bolder text-success">
                                    {{ number_format(($payroll->net_amount ?? ($payroll->amount + ($payroll->bonuses ?? 0) - ($payroll->deductions ?? 0))), 2) }}
                                    <small>{{ $payroll->currency }}</small>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">تاريخ الدفع</span>
                                <span class="info-value">{{ optional($payroll->paid_at)->format('Y-m-d') ?? '—' }}</span>
                            </div>
                        </div>
                        @if($payroll->notes)
                            <div class="col-12">
                                <div class="info-row bg-light p-2 rounded">
                                    <span class="info-label">ملاحظات</span>
                                    <span class="info-value">{{ $payroll->notes }}</span>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <i class="bi bi-lightning"></i>
                        <h6 class="mb-0">إجراءات سريعة</h6>
                    </div>
                    <div class="d-grid gap-2">
                        <a href="{{ route('payrolls.edit', $payroll) }}" class="btn btn-outline-warning">
                            <i class="bi bi-pencil-square me-1"></i> طلب تعديل
                        </a>
                        <form method="POST" action="{{ route('payrolls.destroy', $payroll) }}"
                            onsubmit="return confirm('هل أنت متأكد من طلب إلغاء هذا الراتب؟ سيتم إرسال طلب للموافقة.');">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-warning w-100">
                                <i class="bi bi-x-circle me-1"></i> طلب إلغاء
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

