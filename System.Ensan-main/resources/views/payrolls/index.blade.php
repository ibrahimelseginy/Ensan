@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-cash-coin text-primary me-2"></i>الرواتب
            </h4>
        </div>
        <div>
            <a href="{{ route('payrolls.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة راتب
            </a>
        </div>
    </div>

    {{-- Payrolls Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>الموظف</th>
                        <th>الشهر</th>
                        <th>المبلغ</th>
                        <th>تاريخ الدفع</th>
                        <th class="text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrolls as $p)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm avatar-primary">
                                        {{ mb_substr($p->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="fw-medium">{{ $p->user?->name ?? '—' }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $p->month }}</span>
                            </td>
                            <td>
                                <span class="fw-bold">{{ number_format($p->amount, 2) }}</span>
                                <small class="text-muted ms-1">{{ $p->currency }}</small>
                            </td>
                            <td>
                                @if($p->paid_at)
                                    <span class="text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ $p->paid_at->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-warning">
                                        <i class="bi bi-clock me-1"></i>
                                        غير مدفوع
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="action-buttons">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('payrolls.show', $p) }}">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if(request()->user())
                                        @if(request()->user()->hasRole('admin') || request()->user()->hasRole('manager') || request()->user()->hasRole('finance'))
                                            <a class="btn btn-sm btn-outline-secondary" href="{{ route('payrolls.edit', $p) }}" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form class="d-inline" method="POST" action="{{ route('payrolls.destroy', $p) }}"
                                                onsubmit="return confirm('حذف الراتب؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @else
                                            <a class="btn btn-sm btn-outline-warning" href="{{ route('payrolls.edit', $p) }}" title="طلب تعديل">
                                                <i class="bi bi-pencil-square"></i>
                                            </a>
                                            <form class="d-inline" method="POST" action="{{ route('payrolls.destroy', $p) }}"
                                                onsubmit="return confirm('هل أنت متأكد من طلب حذف هذا الراتب؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-warning" title="طلب حذف">
                                                    <i class="bi bi-x-circle"></i>
                                                </button>
                                            </form>
                                        @endif
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-wallet2"></i>
                                    <h5>لا توجد رواتب مسجلة</h5>
                                    <p>اضغط على "إضافة راتب" لتسجيل راتب جديد</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payrolls->links() }}</div>
@endsection

