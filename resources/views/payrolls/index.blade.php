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
        <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-table me-2 text-primary"></i> كشف مسير الرواتب والمستحقات</h5>
            <span class="badge bg-light text-muted border font-monospace">{{ $payrolls->total() }} سجل</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
                <thead class="table-light">
                    <tr>
                        <th class="text-start ps-3">اسم الموظف</th>
                        <th>الراتب الأساسي</th>
                        <th>الحوافز</th>
                        <th class="table-primary-subtle text-primary">إجمالي المستحقات</th>
                        <th class="text-danger">الخصومات</th>
                        <th class="table-success-subtle text-success">صافي الراتب</th>
                        <th class="text-start">ملاحظات</th>
                        <th>الشهر</th>
                        <th>تاريخ الدفع</th>
                        <th class="text-end pe-3">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        $sumBasic = 0;
                        $sumBonuses = 0;
                        $sumEntitlements = 0;
                        $sumDeductions = 0;
                        $sumNet = 0;
                    @endphp
                    @forelse($payrolls as $p)
                        @php
                            $basic = (float) $p->amount;
                            $bonuses = (float) ($p->bonuses ?? 0);
                            $entitlements = $basic + $bonuses;
                            $deductions = (float) ($p->deductions ?? 0);
                            $net = (float) ($p->net_amount ?? ($entitlements - $deductions));

                            $sumBasic += $basic;
                            $sumBonuses += $bonuses;
                            $sumEntitlements += $entitlements;
                            $sumDeductions += $deductions;
                            $sumNet += $net;
                        @endphp
                        <tr>
                            <td class="text-start ps-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 32px; height: 32px;">
                                        {{ mb_substr($p->user?->name ?? '?', 0, 1) }}
                                    </div>
                                    <div>
                                        <span class="fw-bold text-dark d-block">{{ $p->user?->name ?? '—' }}</span>
                                        <span class="x-small text-muted font-monospace">{{ $p->user?->code }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="fw-bold font-monospace">{{ number_format($basic, 2) }}</span>
                                <small class="text-muted ms-1">{{ $p->currency }}</small>
                            </td>
                            <td>
                                @if($bonuses > 0)
                                    <span class="fw-bold font-monospace text-success">+{{ number_format($bonuses, 2) }}</span>
                                @else
                                    <span class="text-muted font-monospace">0.00</span>
                                @endif
                            </td>
                            <td class="bg-primary-subtle bg-opacity-25">
                                <span class="fw-bold font-monospace text-primary fs-6">{{ number_format($entitlements, 2) }}</span>
                            </td>
                            <td>
                                @if($deductions > 0)
                                    <span class="fw-bold font-monospace text-danger">-{{ number_format($deductions, 2) }}</span>
                                @else
                                    <span class="text-muted font-monospace">0.00</span>
                                @endif
                            </td>
                            <td class="bg-success-subtle bg-opacity-25">
                                <span class="fw-bolder font-monospace text-success fs-6">{{ number_format($net, 2) }}</span>
                                <small class="text-muted ms-1">{{ $p->currency }}</small>
                            </td>
                            <td class="text-start small text-muted text-truncate" style="max-width: 160px;" title="{{ $p->notes }}">
                                {{ $p->notes ?: '—' }}
                            </td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $p->month }}</span>
                            </td>
                            <td>
                                @if($p->paid_at)
                                    <span class="badge bg-success-subtle text-success">
                                        <i class="bi bi-check-circle me-1"></i>
                                        {{ $p->paid_at->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="badge bg-warning-subtle text-warning">
                                        <i class="bi bi-clock me-1"></i>
                                        غير مدفوع
                                    </span>
                                @endif
                            </td>
                            <td class="text-end pe-3">
                                <div class="action-buttons">
                                    <a class="btn btn-sm btn-outline-primary" href="{{ route('payrolls.show', $p) }}" title="عرض">
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
                            <td colspan="10" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-wallet2 display-4 opacity-50 mb-2 d-block"></i>
                                    <h5>لا توجد رواتب مسجلة</h5>
                                    <p class="text-muted">اضغط على "إضافة راتب" لتسجيل مسير رواتب جديد</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                @if($payrolls->isNotEmpty())
                    <tfoot class="table-light fw-bold border-top-2">
                        <tr>
                            <td class="text-start ps-3">الإجمالي (هذه الصفحة):</td>
                            <td class="font-monospace">{{ number_format($sumBasic, 2) }}</td>
                            <td class="font-monospace text-success">+{{ number_format($sumBonuses, 2) }}</td>
                            <td class="font-monospace text-primary">{{ number_format($sumEntitlements, 2) }}</td>
                            <td class="font-monospace text-danger">-{{ number_format($sumDeductions, 2) }}</td>
                            <td class="font-monospace text-success fs-6">{{ number_format($sumNet, 2) }}</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $payrolls->links() }}</div>
@endsection

