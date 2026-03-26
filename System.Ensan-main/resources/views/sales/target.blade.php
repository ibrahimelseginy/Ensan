@extends('layouts.app')

@section('title', 'متابعة التارجت وعمولات الموظفين')

@section('content')

{{-- Hero Header --}}
<div class="sales-hero mb-4 rounded-4 text-white p-4 d-flex align-items-center justify-content-between"
    style="background: linear-gradient(135deg, #5b21b6 0%, #7c3aed 50%, #4f46e5 100%); min-height: 130px;">
    <div>
        <h3 class="fw-bold mb-1">
            <i class="bi bi-currency-dollar me-2 fs-3"></i>متابعة التارجت وعمولات الموظفين
        </h3>
        <p class="mb-0 opacity-75">حساب استحقاقات الموظفين بناءً على مبيعاتهم من الطلبات المنفذة</p>
    </div>
    <span class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center"
        style="width:60px;height:60px;font-size:1.8rem">💰</span>
</div>

{{-- Filters --}}
<div class="card border-0 shadow-sm mb-4 rounded-4">
    <div class="card-body py-3">
        <form method="GET" action="{{ route('sales.target') }}" class="row g-2 align-items-center">
            <div class="col-auto ms-auto text-end">
                <small class="text-muted d-block mb-1">اخر شهر المتابعة</small>
            </div>
            <div class="col-auto">
                <input type="month" name="month" class="form-control" value="{{ $month }}"
                    style="min-width: 180px;">
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary px-3">
                    <i class="bi bi-search me-1"></i> عرض التقرير
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Settings Link --}}
<div class="mb-3">
    <a href="#settingsModal" data-bs-toggle="modal"
        class="text-decoration-none text-muted small">
        <i class="bi bi-gear me-1"></i> إعدادات فئات التارجت
    </a>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100"
            style="background: linear-gradient(135deg, #10b981, #059669); color:white;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold opacity-75">إجمالي استحقاقات الموظفين (عمولات ومكافآت التارجت)</span>
                    <i class="bi bi-wallet2 fs-4 opacity-75"></i>
                </div>
                <h2 class="fw-bold mb-0">{{ number_format($totalEntitlements, 2) }}
                    <small class="fs-6 opacity-75">ج.م</small></h2>
                <small class="opacity-75">
                    عمولات: {{ number_format($totalCommission, 2) }} ج.م &nbsp;|&nbsp;
                    رواتب: {{ number_format($totalBaseSalary, 2) }} ج.م
                </small>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100"
            style="background: linear-gradient(135deg, #6366f1, #4f46e5); color:white;">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold opacity-75">إجمالي قريبي للطلبات المنفذة (بمشاركة الموظفين)</span>
                    <i class="bi bi-bag-check fs-4 opacity-75"></i>
                </div>
                <h2 class="fw-bold mb-0">{{ number_format($totalDonationsByAll, 2) }}
                    <small class="fs-6 opacity-75">ج.م</small></h2>
                <small class="opacity-75">
                    {{ $monthDate->translatedFormat('F Y') }}
                    &nbsp;|&nbsp; {{ count($employeeStats) }} موظف مبيعات
                </small>
            </div>
        </div>
    </div>
</div>

{{-- Employees Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        @if(count($employeeStats) > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr class="bg-light">
                        <th class="ps-4 py-3">الترتيب</th>
                        <th class="py-3">الموظف</th>
                        <th class="py-3">إجمالي الطلبات المشارك بها</th>
                        <th class="py-3">عدد الطلبات</th>
                        <th class="py-3">الفئة المُحققة</th>
                        <th class="py-3">الراتب الأساسي</th>
                        <th class="py-3">العمولة</th>
                        <th class="py-3 pe-4">إجمالي المستحقات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($employeeStats as $rank => $stat)
                    <tr>
                        <td class="ps-4">
                            @if($rank === 0)
                                <span class="badge rounded-pill bg-warning text-dark"
                                    style="font-size:1rem; padding: 6px 10px;">
                                    🥇 #1
                                </span>
                            @elseif($rank === 1)
                                <span class="badge rounded-pill bg-secondary"
                                    style="font-size:0.9rem; padding: 5px 9px;">
                                    🥈 #2
                                </span>
                            @elseif($rank === 2)
                                <span class="badge rounded-pill"
                                    style="background:#cd7f32;font-size:0.9rem; padding: 5px 9px;">
                                    🥉 #3
                                </span>
                            @else
                                <span class="text-muted fw-bold">#{{ $rank + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                @php
                                    $colors = ['primary','success','danger','warning','info','secondary'];
                                    $color = $colors[$stat['user']->id % count($colors)];
                                    $initial = mb_substr($stat['user']->name, 0, 1);
                                @endphp
                                @if($stat['user']->profile_photo_path)
                                    <img src="{{ asset('storage/' . $stat['user']->profile_photo_path) }}"
                                        class="rounded-circle" width="38" height="38"
                                        style="object-fit:cover;">
                                @else
                                    <div class="rounded-circle bg-{{ $color }} text-white d-flex align-items-center justify-content-center"
                                        style="width:38px;height:38px;font-weight:600;">
                                        {{ $initial }}
                                    </div>
                                @endif
                                <div>
                                    <div class="fw-semibold">{{ $stat['user']->name }}</div>
                                    @if($stat['user']->job_title)
                                    <small class="text-muted">{{ $stat['user']->job_title }}</small>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ number_format($stat['donations_total'], 2) }} <small class="text-muted">ج.م</small></div>
                            @if($stat['target'] > 0)
                            <div class="mt-1" style="max-width: 160px;">
                                <div class="progress" style="height: 6px; border-radius: 10px;">
                                    @php
                                        $pct = min($stat['target_pct'], 100);
                                        $barColor = $stat['target_pct'] >= 100 ? 'success' : ($stat['target_pct'] >= 75 ? 'info' : ($stat['target_pct'] >= 50 ? 'primary' : 'danger'));
                                    @endphp
                                    <div class="progress-bar bg-{{ $barColor }}" style="width: {{ $pct }}%"></div>
                                </div>
                                <small class="text-muted">{{ $stat['target_pct'] }}% من التارجت
                                    ({{ number_format($stat['target'], 0) }} ج.م)</small>
                            </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                <i class="bi bi-file-earmark-text me-1"></i>
                                {{ $stat['donations_count'] }} طلب
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $stat['tier_color'] }} bg-opacity-15 text-{{ $stat['tier_color'] }} border border-{{ $stat['tier_color'] }} border-opacity-25 px-3 py-2 rounded-pill">
                                {{ $stat['tier_label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark">{{ number_format($stat['base_salary'], 2) }}</span>
                            <small class="text-muted d-block">ج.م</small>
                        </td>
                        <td>
                            @if($stat['commission'] > 0)
                                <span class="text-success fw-bold">{{ number_format($stat['commission'], 2) }}</span>
                                <small class="text-muted d-block">ج.م ({{ $stat['commission_rate'] }}%)</small>
                            @else
                                <span class="text-muted">
                                    0.00
                                    @if($stat['commission_rate'] > 0)
                                        <small class="d-block text-danger">لم يحقق التارجت</small>
                                    @endif
                                </span>
                            @endif
                        </td>
                        <td class="pe-4">
                            <span class="fw-bold fs-6 text-primary">{{ number_format($stat['total_entitlement'], 2) }}</span>
                            <small class="text-muted d-block">ج.م</small>
                            {{-- Quick edit button --}}
                            <button type="button" class="btn btn-sm btn-outline-secondary mt-1"
                                data-bs-toggle="modal"
                                data-bs-target="#editModal{{ $stat['user']->id }}"
                                title="تعديل التارجت">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="2" class="ps-4 fw-bold py-3">الإجمالي</td>
                        <td class="fw-bold">{{ number_format($totalDonationsByAll, 2) }} ج.م</td>
                        <td>—</td>
                        <td>—</td>
                        <td class="fw-bold">{{ number_format($totalBaseSalary, 2) }} ج.م</td>
                        <td class="fw-bold text-success">{{ number_format($totalCommission, 2) }} ج.م</td>
                        <td class="pe-4 fw-bold text-primary">{{ number_format($totalEntitlements, 2) }} ج.م</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @else
        <div class="text-center py-5">
            <div class="mb-3" style="font-size: 4rem;">📊</div>
            <h5 class="text-muted fw-bold">لا يوجد موظفو مبيعات مُحددون</h5>
            <p class="text-muted">لإظهار الموظفين هنا، یجب تحديد موظفي المبيعات أولاً.</p>
            <a href="/dev/mark-sales-users" class="btn btn-outline-primary" target="_blank">
                <i class="bi bi-people me-1"></i> تحديد موظفي المبيعات (مؤقت)
            </a>
            <div class="mt-2 text-muted small">
                أو اضغط على "إعدادات فئات التارجت" لإضافة وإعداد الموظفين
            </div>
        </div>
        @endif
    </div>
</div>

{{-- Edit Modals --}}
@foreach($salesEmployees as $emp)
<div class="modal fade" id="editModal{{ $emp->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <form method="POST" action="{{ route('sales.target.update-employee') }}">
                @csrf
                <input type="hidden" name="user_id" value="{{ $emp->id }}">
                <input type="hidden" name="is_sales" value="1">
                <div class="modal-header border-0 pb-0">
                    <h5 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-2 text-primary"></i>
                        تعديل: {{ $emp->name }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body py-3">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">التارجت الشهري <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" name="monthly_target" class="form-control"
                                value="{{ $emp->monthly_target ?? 0 }}"
                                min="0" step="100" required>
                            <span class="input-group-text">ج.م</span>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">نسبة العمولة عند تحقيق التارجت</label>
                        <div class="input-group">
                            <input type="number" name="commission_rate" class="form-control"
                                value="{{ $emp->commission_rate ?? 0 }}"
                                min="0" max="100" step="0.5">
                            <span class="input-group-text">%</span>
                        </div>
                        <small class="text-muted">يُحسب على إجمالي المبيعات عند تحقيق التارجت</small>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-3 px-4">
                        <i class="bi bi-check2 me-1"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

{{-- Settings Modal --}}
<div class="modal fade" id="settingsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-gear me-2 text-primary"></i> إعدادات فئات التارجت
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info rounded-3 mb-4">
                    <i class="bi bi-info-circle me-1"></i>
                    حدد موظفي المبيعات ثم اضبط تارجت ونسبة عمولة كل موظف من جدول النتائج أعلاه.
                </div>

                <h6 class="fw-bold mb-3">فئات التحقيق</h6>
                <div class="table-responsive">
                    <table class="table table-sm border rounded-3">
                        <thead class="table-light">
                            <tr>
                                <th>الفئة</th>
                                <th>نسبة التحقيق</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>🥇 استثنائي</td>
                                <td>≥ 150%</td>
                                <td><span class="badge bg-warning text-dark">مكافأة خاصة</span></td>
                            </tr>
                            <tr>
                                <td>✓ حقق التارجت</td>
                                <td>100% - 149%</td>
                                <td><span class="badge bg-success">عمولة كاملة</span></td>
                            </tr>
                            <tr>
                                <td>قريب من التارجت</td>
                                <td>75% - 99%</td>
                                <td><span class="badge bg-info">بدون عمولة</span></td>
                            </tr>
                            <tr>
                                <td>نصف الطريق</td>
                                <td>50% - 74%</td>
                                <td><span class="badge bg-primary">بدون عمولة</span></td>
                            </tr>
                            <tr>
                                <td>لم يحقق تارجت</td>
                                <td>< 50%</td>
                                <td><span class="badge bg-danger">بدون عمولة</span></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-outline-secondary rounded-3" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

@if(session('success'))
<div class="position-fixed bottom-0 end-0 p-3" style="z-index:9999">
    <div class="toast show bg-success text-white shadow rounded-3">
        <div class="toast-body">
            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        </div>
    </div>
</div>
@endif

@endsection

@section('styles')
<style>
.sales-hero {
    box-shadow: 0 8px 32px rgba(109, 40, 217, 0.25);
}
.table th {
    font-size: 0.8rem;
    text-transform: uppercase;
    color: #6b7280;
    font-weight: 600;
    border-bottom: 2px solid #f3f4f6;
    background: #f9fafb;
}
.table td {
    vertical-align: middle;
    font-size: 0.9rem;
}
.progress {
    background-color: #f3f4f6;
}
</style>
@endsection
