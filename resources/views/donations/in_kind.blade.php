@extends('layouts.app')
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    .select2-container--bootstrap-5 .select2-selection {
        height: 44px !important;
        min-height: 44px !important;
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 12px !important;
        display: block !important;
        position: relative !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    [data-bs-theme="light"] .select2-container--bootstrap-5 .select2-selection,
    body:not(.theme-dark) .select2-container--bootstrap-5 .select2-selection {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }
    .select2-container--bootstrap-5.select2-container--focus .select2-selection,
    .select2-container--bootstrap-5.select2-container--open .select2-selection {
        border-color: #f59e0b !important;
        box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.2) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-right: 14px !important;
        padding-left: 42px !important;
        line-height: 42px !important;
        height: 42px !important;
        color: #f8fafc !important;
        font-weight: 500 !important;
        font-size: 0.92rem !important;
        direction: rtl !important;
        text-align: right !important;
    }
    [data-bs-theme="light"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered,
    body:not(.theme-dark) .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
    }

    .donation-column-panel {
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 20px;
        padding: 20px;
        box-shadow: 0 12px 36px rgba(0, 0, 0, 0.2);
    }
    [data-bs-theme="light"] .donation-column-panel,
    body:not(.theme-dark) .donation-column-panel {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
    }

    .panel-header-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        padding-bottom: 16px;
        margin-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }
    [data-bs-theme="light"] .panel-header-box,
    body:not(.theme-dark) .panel-header-box {
        border-bottom-color: #e2e8f0;
    }

    .donation-row-card {
        background: rgba(30, 41, 59, 0.5);
        border: 1px solid rgba(255, 255, 255, 0.06);
        border-radius: 14px;
        padding: 14px 18px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        transition: all 0.2s ease;
    }
    .donation-row-card:hover {
        background: rgba(30, 41, 59, 0.85);
        border-color: rgba(245, 158, 11, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.25);
    }
    [data-bs-theme="light"] .donation-row-card,
    body:not(.theme-dark) .donation-row-card {
        background: #f8fafc;
        border-color: #e2e8f0;
    }
    [data-bs-theme="light"] .donation-row-card:hover,
    body:not(.theme-dark) .donation-row-card:hover {
        background: #ffffff;
        border-color: #f59e0b;
        box-shadow: 0 8px 24px rgba(245, 158, 11, 0.12);
    }

    .donation-channel-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        flex-shrink: 0;
    }
    .channel-in_kind { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

    .donor-name-title {
        font-weight: 800;
        font-size: 1.05rem;
        color: #ffffff;
        text-decoration: none;
    }
    [data-bs-theme="light"] .donor-name-title,
    body:not(.theme-dark) .donor-name-title {
        color: #0f172a;
    }
    .donor-name-title:hover {
        color: #f59e0b;
    }

    .badge-receipt-mini {
        font-family: monospace;
        font-size: 0.78rem;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.08);
        color: #94a3b8;
    }
    [data-bs-theme="light"] .badge-receipt-mini,
    body:not(.theme-dark) .badge-receipt-mini {
        background: #e2e8f0;
        color: #475569;
    }

    .donation-amount-val {
        font-family: monospace;
        font-size: 1.25rem;
        font-weight: 900;
    }
    .amount-inkind { color: #f59e0b; }

    .donations-bulk-bar {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.15);
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.35);
    }
  </style>
@endsection

@section('content')
{{-- Navigation & Breadcrumb Header --}}
<div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-3">
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1">
            <i class="bi bi-arrow-right"></i>
            <span>العودة لبوابة التبرعات</span>
        </a>
        <span class="text-muted">/</span>
        <span class="fw-bold text-warning">التبرعات العينية والمخزون المورد</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3" onclick="window.EnsanPrintPreview ? window.EnsanPrintPreview.open('#inKindDonationsFeed', {title: 'كشف التبرعات العينية والمخزون المورد'}) : window.print()">
            <i class="bi bi-printer me-1"></i> طباعة الكشف
        </button>
        <a href="{{ route('donations.export', array_merge(request()->query(), ['type' => 'in_kind'])) }}" data-no-loader download class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-download me-1"></i> تصدير CSV
        </a>
    </div>
</div>

{{-- Hero Banner --}}
<div class="dashboard-hero animate-slide-up mb-4" style="background: linear-gradient(135deg, #d97706 0%, #b45309 50%, #78350f 100%);">
    <div class="hero-content">
        <div class="hero-greeting">المواد والأجهزة والمخزون المورد 📦</div>
        <h1 class="hero-title">سجل التبرعات العينية</h1>
        <p class="hero-subtitle">إدارة ومتابعة التوريدات العينية والمواد والأجهزة والمساعدات المخزنية</p>
        <div class="hero-actions d-flex gap-2 flex-wrap">
            <a href="{{ route('donations.create', ['type' => 'in_kind']) }}" class="btn btn-sm btn-white text-dark fw-bold rounded-pill px-3 shadow-sm" style="background: #ffffff !important; color: #0f172a !important;">
                <i class="bi bi-plus-lg text-warning me-1"></i> <span>تسجيل تبرع عيني جديد</span>
            </a>
            <a href="{{ route('warehouses.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 text-white">
                <i class="bi bi-building me-1"></i> مستودعات ومخازن المؤسسة
            </a>
        </div>
    </div>
    <i class="bi bi-gift-fill hero-icon d-none d-md-block" style="opacity: 0.15; font-size: 7rem;"></i>
</div>

{{-- Stats Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-4">
        <div class="stat-card stat-warning d-block">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-label">إجمالي القيمة التقديرية</div>
            <div class="stat-value font-monospace">{{ number_format($totalEstimated, 0) }} ج.م</div>
            <div class="stat-trend up"><i class="bi bi-shield-check"></i> تقييم معتمد</div>
        </div>
    </div>
    <div class="col-6 col-lg-4">
        <div class="stat-card stat-success d-block">
            <div class="stat-icon"><i class="bi bi-arrow-down-left-circle-fill"></i></div>
            <div class="stat-label">عدد الحركات والتوريدات</div>
            <div class="stat-value font-monospace">{{ $totalCount }} عملية</div>
            <div class="stat-trend up"><i class="bi bi-check-circle"></i> موردة للمخازن</div>
        </div>
    </div>
    <div class="col-12 col-lg-4">
        <div class="stat-card stat-info d-block">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            <div class="stat-label">وارد اليوم</div>
            <div class="stat-value font-monospace">{{ number_format((float)($inKindToday->total ?? 0), 0) }} ج.م</div>
            <div class="stat-trend up"><i class="bi bi-calendar-check"></i> {{ (int)($inKindToday->count ?? 0) }} حركة اليوم</div>
        </div>
    </div>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill text-warning me-1"></i> تصفية وبحث التبرعات العينية</h5>
    </div>
    <form method="get" action="{{ route('donations.in-kind') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase text-muted">
                    <i class="bi bi-search text-primary me-1"></i> بحث (متبرع / كود / غرض / رقم قومي)
                </label>
                <select name="q" id="quickInKindDonationSearch" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="" @selected(empty($q))>الكل (عرض جميع العمليات)</option>
                    @if(!empty($q))
                        <option value="{{ $q }}" selected>{{ $q }} (بحث حالي)</option>
                    @endif
                    <optgroup label="المتبرعون المسجلون">
                        @foreach($allDonors as $dItem)
                            @php
                                $codeDisplay = $dItem->code ? (' [' . $dItem->code . ']') : (' [DON-' . $dItem->id . ']');
                                $phoneDisplay = $dItem->phone ? (' - ' . $dItem->phone) : '';
                                $nidDisplay = $dItem->national_id ? (' - قومي: ' . $dItem->national_id) : '';
                                $isSelected = ($q ?? '') == $dItem->name || ($q ?? '') == $dItem->code || ($q ?? '') == $dItem->phone || ($q ?? '') == $dItem->national_id;
                            @endphp
                            <option value="{{ $dItem->name }}" @selected($isSelected)>
                                {{ $dItem->name }}{{ $codeDisplay }}{{ $phoneDisplay }}{{ $nidDisplay }}
                            </option>
                        @endforeach
                    </optgroup>
                    <optgroup label="المشاريع">
                        @foreach($receiptProjects as $pItem)
                            @php
                                $isSelected = ($q ?? '') == $pItem->name || ($q ?? '') == ($pItem->code ?? '');
                            @endphp
                            <option value="{{ $pItem->name }}" @selected($isSelected)>
                                مشروع: {{ $pItem->name }}
                            </option>
                        @endforeach
                    </optgroup>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">المخزن المستلم</label>
                <select name="warehouse_id" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">جميع المخازن</option>
                    @foreach($warehouses as $wh)
                        <option value="{{ $wh->id }}" @selected(request('warehouse_id') == $wh->id)>{{ $wh->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">الشهر</label>
                <select name="month" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">كل الشهور</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month') == $m)>شهر {{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-1">
                <label class="form-label fw-bold small text-uppercase text-muted">السنة</label>
                <select name="year" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">الكل</option>
                    @foreach(range(date('Y'), 2023) as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2 align-items-center w-100" style="height: 44px;">
                    <button class="btn btn-warning flex-fill fw-bold text-dark" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="bi bi-funnel"></i>
                        <span>تصفية</span>
                    </button>
                    <a class="btn btn-outline-secondary btn-icon-square" href="{{ route('donations.in-kind') }}" title="مسح التصفية" style="width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Full Width In-Kind Donations List --}}
<div class="donation-column-panel mb-4" id="inKindDonationsFeed">
    <div class="panel-header-box">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="selectAllInKind" onclick="toggleAll('inkind-check', this.checked)" class="form-check-input mt-0" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
            <div class="rounded-circle bg-warning bg-opacity-20 text-warning p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-gift-fill fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-white">كشف التبرعات العينية والمخزون</h5>
                <small class="text-muted">عرض تفصيلي للمواد والأجهزة والمساعدات الواردة للمخازن</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="px-3 py-2 rounded-pill fs-6 font-monospace d-inline-flex align-items-center" style="background-color: #d97706 !important; color: #ffffff !important; font-weight: 800; border: 1px solid #b45309; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                <i class="bi bi-box-seam me-1" style="color: #ffffff !important;"></i>
                <span style="color: #ffffff !important;">{{ number_format($inKindDonations->sum('estimated_value'), 0) }} ج.م</span>
                <span class="opacity-75 mx-1" style="color: #ffffff !important;">|</span>
                <span style="color: #ffffff !important;">{{ $inKindDonations->total() }} عملية</span>
            </div>
        </div>
    </div>

    <div class="activity-feed">
        @forelse($inKindDonations as $d)
            <div class="donation-row-card {{ $d->status === 'cancelled' ? 'opacity-50' : '' }}">
                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                    <input type="checkbox" class="form-check-input inkind-check" value="{{ $d->id }}" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    
                    <div class="donation-channel-avatar channel-in_kind">
                        <i class="bi bi-gift-fill"></i>
                    </div>

                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            @if($d->donor)
                                <a href="{{ route('donors.show', $d->donor) }}" class="donor-name-title text-truncate">
                                    {{ $d->donor->name }}
                                </a>
                                <span class="badge-receipt-mini text-warning">{{ $d->donor->code ?? ('DON-'.$d->donor->id) }}</span>
                                @if($d->donor->national_id)
                                    <span class="badge-receipt-mini text-secondary font-monospace" title="الرقم القومي للمتبرع"><i class="bi bi-card-heading me-1"></i>{{ $d->donor->national_id }}</span>
                                @endif
                            @else
                                <span class="donor-name-title text-truncate">متبرع مجهول</span>
                            @endif

                            @if($d->status === 'cancelled')
                                <span class="badge bg-danger rounded-pill px-2 py-0.5" style="font-size: 0.65rem;">ملغي</span>
                            @endif

                            @if($d->receipt_number)
                                <span class="badge-receipt-mini">#{{ $d->receipt_number }}</span>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-2 flex-wrap text-muted small">
                            <span><i class="bi bi-calendar-event me-1"></i>{{ $d->received_at?->format('Y-m-d') ?? '—' }}</span>
                            @if($d->warehouse)
                                <span>•</span>
                                <span class="text-warning"><i class="bi bi-building me-1"></i>{{ $d->warehouse->name }}</span>
                            @endif
                            @if($d->purpose_label)
                                <span>•</span>
                                <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 rounded-pill px-2">{{ $d->purpose_label }}</span>
                            @endif
                            @if($d->project)
                                <span>•</span>
                                <span class="text-muted"><i class="bi bi-folder2 me-1"></i>{{ $d->project->name }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Left Side: Amount & Action Button --}}
                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                    <div class="text-end">
                        <div class="donation-amount-val {{ $d->status === 'cancelled' ? 'text-decoration-line-through text-muted' : 'amount-inkind' }}">
                            +{{ number_format($d->estimated_value, 0) }}
                            <span class="amount-currency">ج.م</span>
                        </div>
                        <div class="text-muted" style="font-size: 0.7rem;">قيمة تقديرية</div>
                    </div>

                    <div class="dropdown">
                        <button class="btn btn-sm btn-icon-action" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport" title="خيارات">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 py-2 rounded-3" style="min-width: 200px; z-index: 1060;">
                            <li><a class="dropdown-item py-1.5" href="{{ route('donations.show', $d) }}"><i class="bi bi-eye text-primary me-2"></i>عرض التفاصيل</a></li>
                            <li><a class="dropdown-item py-1.5 text-success" href="{{ route('donations.show', $d) }}?print=1" target="_blank"><i class="bi bi-printer text-success me-2"></i>طباعة الإيصال</a></li>
                            @if(auth()->check() && $d->status !== 'cancelled')
                                @php
                                    $isManagerUser = auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager');
                                @endphp
                                <li><hr class="dropdown-divider my-1"></li>
                                @if($isManagerUser)
                                    <li><a class="dropdown-item py-1.5 text-warning" href="{{ route('donations.edit', $d) }}"><i class="bi bi-pencil me-2"></i>تعديل</a></li>
                                    <li>
                                        <form action="{{ route('donations.destroy', $d) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رغبتك في إلغاء وحذف هذا التبرع مباشرة؟');">
                                            @csrf @method('DELETE')
                                            <input type="hidden" name="cancellation_reason" value="تم الإلغاء المباشر بواسطة المدير">
                                            <button type="submit" class="dropdown-item py-1.5 text-danger">
                                                <i class="bi bi-trash me-2"></i>حذف / إلغاء التبرع
                                            </button>
                                        </form>
                                    </li>
                                @else
                                    <li><a class="dropdown-item py-1.5 text-warning" href="{{ route('donations.edit', $d) }}"><i class="bi bi-pencil me-2"></i>طلب تعديل</a></li>
                                    <li>
                                        <button type="button" class="dropdown-item py-1.5 text-danger" onclick="openCancelModal('{{ $d->id }}', true)">
                                            <i class="bi bi-x-circle me-2"></i>طلب إلغاء
                                        </button>
                                    </li>
                                @endif
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-muted text-center py-5">
                <i class="bi bi-gift fs-2 opacity-50 d-block mb-2"></i>
                <span>لا توجد تبرعات عينية مطابقة للعرض</span>
            </div>
        @endforelse
    </div>

    {{-- Bulk Actions Bar --}}
    <div class="donations-bulk-bar d-flex gap-2 align-items-center mt-3 p-3 flex-wrap" id="inkindBulkBar" style="display:none;">
        <span class="small fw-bold text-white px-2 py-1 bg-white bg-opacity-10 rounded-pill" id="inkindSelectedCount">0 محدد</span>
        <button class="btn btn-outline-danger btn-sm ms-auto rounded-pill px-3" onclick="submitBulkDelete('inkind-check')"><i class="bi bi-trash me-1"></i> حذف المحدد</button>
    </div>

    <div class="mt-4 d-flex justify-content-center" dir="ltr">
        {{ $inKindDonations->appends(request()->query())->links('pagination::bootstrap-5') }}
    </div>
</div>

{{-- Hidden Bulk Delete Form --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('donations.bulk-destroy') }}" style="display:none;">
    @csrf
    <div id="bulkDeleteIds"></div>
</form>

{{-- Cancellation Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-danger">تأكيد إلغاء التبرع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إلغاء هذا التبرع؟ سيتم عكس العمليات المالية وتحديث حالة التبرع.</p>
                <div class="mb-3">
                    <label for="cancellation_reason" class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required placeholder="يرجى توضيح سبب الإلغاء..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#quickInKindDonationSearch').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'اختر متبرع أو اكتب للبحث...',
                tags: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج مطابقة — اضغط Enter للبحث بهذا النص";
                    }
                }
            }).on('select2:select', function(e) {
                $(this).closest('form').submit();
            }).on('select2:clear', function(e) {
                setTimeout(() => { $(this).closest('form').submit(); }, 50);
            });
        }
    });

    function openCancelModal(id, isRequest = false) {
        const form = document.getElementById('cancelForm');
        form.action = `/donations/${id}`;
        
        const title = document.querySelector('#cancelModal .modal-title');
        const bodyText = document.querySelector('#cancelModal .modal-body p');
        const btn = document.querySelector('#cancelModal button[type="submit"]');

        if (isRequest) {
            title.textContent = 'طلب إلغاء التبرع';
            title.className = 'modal-title text-warning';
            bodyText.textContent = 'هل أنت متأكد من إرسال طلب لإلغاء هذا التبرع؟ سيقوم المدير بمراجعة الطلب والموافقة عليه.';
            btn.textContent = 'إرسال الطلب';
            btn.className = 'btn btn-warning';
        } else {
            title.textContent = 'تأكيد إلغاء التبرع';
            title.className = 'modal-title text-danger';
            bodyText.textContent = 'هل أنت متأكد من رغبتك في إلغاء هذا التبرع؟ سيتم عكس العمليات المالية وتحديث حالة التبرع.';
            btn.textContent = 'تأكيد الإلغاء';
            btn.className = 'btn btn-danger';
        }

        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    function toggleAll(className, checked) {
        document.querySelectorAll('.' + className).forEach(cb => cb.checked = checked);
        updateBulkBar(className);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('inkind-check')) updateBulkBar('inkind-check');
    });

    function updateBulkBar(className) {
        const checked = document.querySelectorAll('.' + className + ':checked').length;
        const bar = document.getElementById('inkindBulkBar');
        document.getElementById('inkindSelectedCount').textContent = checked + ' محدد';
        bar.style.display = checked > 0 ? 'flex' : 'none';
        bar.style.setProperty('display', checked > 0 ? 'flex' : 'none', 'important');
    }

    function submitBulkDelete(className) {
        const ids = [];
        document.querySelectorAll('.' + className + ':checked').forEach(cb => ids.push(cb.value));
        if (ids.length === 0) { alert('يرجى اختيار تبرع واحد على الأقل'); return; }
        if (!confirm('هل أنت متأكد من حذف ' + ids.length + ' تبرع؟')) return;
        const form = document.getElementById('bulkDeleteForm');
        const container = document.getElementById('bulkDeleteIds');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
            container.appendChild(input);
        });
        form.submit();
    }
</script>
@endsection
