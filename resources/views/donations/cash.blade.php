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
        border-color: #10b981 !important;
        box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        padding-right: 14px !important;
        padding-left: 48px !important;
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

    /* Select2 Clear Button (مسح البحث) */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear,
    .select2-container--default .select2-selection--single .select2-selection__clear {
        position: absolute !important;
        left: 12px !important;
        right: auto !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 22px !important;
        height: 22px !important;
        background: #f1f5f9 !important;
        border: 1px solid #cbd5e1 !important;
        border-radius: 50% !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        font-size: 0 !important;
        cursor: pointer !important;
        transition: all 0.2s ease !important;
        z-index: 10 !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear::before,
    .select2-container--default .select2-selection--single .select2-selection__clear::before {
        content: "×" !important;
        font-size: 15px !important;
        line-height: 1 !important;
        font-family: Arial, sans-serif !important;
        font-weight: 700 !important;
        color: #64748b !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover,
    .select2-container--default .select2-selection--single .select2-selection__clear:hover {
        background: #fee2e2 !important;
        border-color: #f87171 !important;
        transform: translateY(-50%) scale(1.12) !important;
    }
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover::before,
    .select2-container--default .select2-selection--single .select2-selection__clear:hover::before {
        color: #dc2626 !important;
    }
    .theme-dark .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
        background: #334155 !important;
        border-color: #475569 !important;
    }
    .theme-dark .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear::before {
        color: #94a3b8 !important;
    }
    .theme-dark .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover {
        background: rgba(239, 68, 68, 0.25) !important;
        border-color: #ef4444 !important;
    }
    .theme-dark .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover::before {
        color: #fca5a5 !important;
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
        border-color: rgba(16, 185, 129, 0.4);
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
        border-color: #10b981;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.12);
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
    .channel-cash { background: rgba(16, 185, 129, 0.15); color: #10b981; }
    .channel-vodafone_cash { background: rgba(239, 68, 68, 0.15); color: #ef4444; }
    .channel-instapay { background: rgba(14, 165, 233, 0.15); color: #0ea5e9; }
    .channel-delegate { background: rgba(245, 158, 11, 0.15); color: #f59e0b; }

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
        color: #10b981;
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
    .amount-cash { color: #10b981; }

    .donations-bulk-bar {
        position: fixed !important;
        bottom: 28px !important;
        left: 50% !important;
        transform: translateX(-50%) translateY(100px) scale(0.95);
        background: rgba(15, 23, 42, 0.94) !important;
        border: 1px solid rgba(255, 255, 255, 0.16) !important;
        border-radius: 50px !important;
        box-shadow: 0 20px 45px -10px rgba(0, 0, 0, 0.5), 0 0 0 1px rgba(255, 255, 255, 0.08) !important;
        z-index: 1060 !important;
        padding: 8px 18px !important;
        backdrop-filter: blur(14px) !important;
        -webkit-backdrop-filter: blur(14px) !important;
        opacity: 0;
        pointer-events: none;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.25s ease !important;
    }
    .donations-bulk-bar.active {
        display: flex !important;
        opacity: 1 !important;
        pointer-events: auto !important;
        transform: translateX(-50%) translateY(0) scale(1) !important;
    }
    .bulk-count-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 12px;
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 30px;
        font-size: 0.85rem;
        color: #e2e8f0;
        font-weight: 600;
    }
    .bulk-clear-btn {
        color: #94a3b8 !important;
        font-size: 0.82rem !important;
        font-weight: 500 !important;
        transition: color 0.15s ease !important;
    }
    .bulk-clear-btn:hover {
        color: #f8fafc !important;
    }
    .bulk-delete-btn {
        background: #dc2626 !important;
        border-color: #ef4444 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 0.82rem !important;
        transition: all 0.2s ease !important;
    }
    .bulk-delete-btn:hover {
        background: #b91c1c !important;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.4) !important;
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
        <span class="fw-bold text-success">التبرعات النقدية والإلكترونية</span>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="window.EnsanPrintPreview ? window.EnsanPrintPreview.open('#cashDonationsFeed', {title: 'كشف التبرعات النقدية والإلكترونية'}) : window.print()">
            <i class="bi bi-printer me-1"></i> طباعة الكشف
        </button>
        <a href="{{ route('donations.export', array_merge(request()->query(), ['type' => 'cash'])) }}" data-no-loader download class="btn btn-sm btn-outline-light rounded-pill px-3">
            <i class="bi bi-download me-1"></i> تصدير CSV
        </a>
    </div>
</div>

{{-- Hero Banner --}}
<div class="dashboard-hero animate-slide-up mb-4" style="background: linear-gradient(135deg, #059669 0%, #047857 50%, #064e3b 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التحصيلات النقدية والإلكترونية 💵</div>
        <h1 class="hero-title">سجل التبرعات النقدية</h1>
        <p class="hero-subtitle">إدارة ومتابعة تحصيلات الخزينة النقدية، فودافون كاش، انستاباي، والمناديب</p>
        <div class="hero-actions d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-white text-dark fw-bold rounded-pill px-3 shadow-sm" style="background: #ffffff !important; color: #0f172a !important;" data-bs-toggle="modal" data-bs-target="#quickReceiptModal">
                <i class="bi bi-receipt text-success me-1"></i> <span>إيصال استلام نقدية سريع</span>
            </button>
            <a href="{{ route('donations.create') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 text-white">
                <i class="bi bi-plus-lg me-1"></i> إضافة تبرع مفصل
            </a>
        </div>
    </div>
    <i class="bi bi-cash-coin hero-icon d-none d-md-block" style="opacity: 0.15; font-size: 7rem;"></i>
</div>

{{-- Channel Stats Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', array_merge(request()->except(['channel', 'page']))) }}" class="stat-card stat-success text-decoration-none d-block {{ !request('channel') ? 'border-success shadow' : '' }}">
            <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
            <div class="stat-label">الكل (نقدي)</div>
            <div class="stat-value">{{ number_format($channelMetrics['all']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['all']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', array_merge(request()->except(['channel', 'page']), ['channel' => 'cash'])) }}" class="stat-card stat-primary text-decoration-none d-block {{ request('channel') === 'cash' ? 'border-primary shadow' : '' }}">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">خزينة نقدي</div>
            <div class="stat-value">{{ number_format($channelMetrics['cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['cash']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', array_merge(request()->except(['channel', 'page']), ['channel' => 'vodafone_cash'])) }}" class="stat-card stat-danger text-decoration-none d-block {{ request('channel') === 'vodafone_cash' ? 'border-danger shadow' : '' }}">
            <div class="stat-icon"><i class="bi bi-phone-fill"></i></div>
            <div class="stat-label">فودافون كاش</div>
            <div class="stat-value">{{ number_format($channelMetrics['vodafone_cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['vodafone_cash']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', array_merge(request()->except(['channel', 'page']), ['channel' => 'instapay'])) }}" class="stat-card stat-info text-decoration-none d-block {{ request('channel') === 'instapay' ? 'border-info shadow' : '' }}">
            <div class="stat-icon"><i class="bi bi-credit-card-fill"></i></div>
            <div class="stat-label">انستا باي</div>
            <div class="stat-value">{{ number_format($channelMetrics['instapay']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['instapay']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', array_merge(request()->except(['channel', 'page']), ['channel' => 'delegate'])) }}" class="stat-card stat-warning text-decoration-none d-block {{ request('channel') === 'delegate' ? 'border-warning shadow' : '' }}">
            <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-label">مندوب تحصيل</div>
            <div class="stat-value">{{ number_format($channelMetrics['delegate']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['delegate']['count'] }} عملية</div>
        </a>
    </div>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill text-success me-1"></i> تصفية وبحث التبرعات النقدية</h5>
    </div>
    <form method="get" action="{{ route('donations.cash') }}">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase text-muted">
                    <i class="bi bi-search text-primary me-1"></i> بحث سريع (متبرع / مشروع / كود / رقم قومي)
                </label>
                <select name="q" id="quickCashDonationSearch" class="form-select" style="height: 44px; border-radius: 10px;">
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
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">وسيلة التحصيل</label>
                <select name="channel" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">جميع القنوات</option>
                    <option value="cash" @selected(request('channel') === 'cash')>نقدي (خزينة)</option>
                    <option value="vodafone_cash" @selected(request('channel') === 'vodafone_cash')>فودافون كاش</option>
                    <option value="instapay" @selected(request('channel') === 'instapay')>انستا باي</option>
                    <option value="delegate" @selected(request('channel') === 'delegate')>مندوب تحصيل</option>
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
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">السنة</label>
                <select name="year" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">كل السنوات</option>
                    @foreach(range(date('Y'), 2023) as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <div class="d-flex gap-2 align-items-center w-100" style="height: 44px;">
                    <button class="btn btn-success flex-fill fw-bold" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="bi bi-funnel"></i>
                        <span>تصفية</span>
                    </button>
                    <a class="btn btn-outline-secondary btn-icon-square" href="{{ route('donations.cash') }}" title="مسح التصفية" style="width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-x-lg"></i>
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Full Width Cash Donations List --}}
<div class="donation-column-panel mb-4" id="cashDonationsFeed">
    <div class="panel-header-box">
        <div class="d-flex align-items-center gap-2">
            <input type="checkbox" id="selectAllCash" onclick="toggleAll('cash-check', this.checked)" class="form-check-input mt-0" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
            <div class="rounded-circle bg-success bg-opacity-20 text-success p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="bi bi-cash-coin fs-5"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0 text-white">كشف التبرعات النقدية والإلكترونية</h5>
                <small class="text-muted">عرض تفصيلي لعمليات الخزينة والمحافظ والتحويلات</small>
            </div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="px-3 py-2 rounded-pill fs-6 font-monospace d-inline-flex align-items-center" style="background-color: #047857 !important; color: #ffffff !important; font-weight: 800; border: 1px solid #065f46; box-shadow: 0 2px 6px rgba(0,0,0,0.2);">
                <i class="bi bi-wallet2 me-1" style="color: #ffffff !important;"></i>
                <span style="color: #ffffff !important;">{{ number_format($cashDonations->sum('amount'), 0) }} ج.م</span>
                <span class="opacity-75 mx-1" style="color: #ffffff !important;">|</span>
                <span style="color: #ffffff !important;">{{ $cashDonations->total() }} عملية</span>
            </div>
        </div>
    </div>

    <div class="activity-feed">
        @forelse($cashDonations as $d)
            @php
                $channelIcon = match($d->cash_channel) {
                    'instapay' => 'qr-code-scan',
                    'vodafone_cash' => 'phone',
                    'delegate' => 'person-badge',
                    default => 'cash-stack'
                };
                $channelLabel = match($d->cash_channel) {
                    'instapay' => 'انستا باي',
                    'vodafone_cash' => 'فودافون كاش',
                    'delegate' => 'مندوب تحصيل',
                    default => 'خزينة نقدي'
                };
            @endphp
            <div class="donation-row-card {{ $d->status === 'cancelled' ? 'opacity-50' : '' }}">
                <div class="d-flex align-items-center gap-3 flex-grow-1 min-w-0">
                    <input type="checkbox" class="form-check-input cash-check" value="{{ $d->id }}" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    
                    <div class="donation-channel-avatar channel-{{ $d->cash_channel ?? 'cash' }}">
                        <i class="bi bi-{{ $channelIcon }}"></i>
                    </div>

                    <div class="min-w-0 flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                            @if($d->donor)
                                <a href="{{ route('donors.show', $d->donor) }}" class="donor-name-title text-truncate">
                                    {{ $d->donor->name }}
                                </a>
                                <span class="badge-receipt-mini text-primary">{{ $d->donor->code ?? ('DON-'.$d->donor->id) }}</span>
                                @if($d->donor->national_id)
                                    <span class="badge-receipt-mini text-secondary font-monospace" title="الرقم القومي للمتبرع"><i class="bi bi-card-heading me-1"></i>{{ $d->donor->national_id }}</span>
                                @endif
                            @else
                                <span class="donor-name-title text-truncate">فاعل خير</span>
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
                            <span>•</span>
                            <span class="text-info"><i class="bi bi-credit-card-2-front me-1"></i>{{ $channelLabel }}</span>
                            @if($d->project)
                                <span>•</span>
                                <span class="text-muted"><i class="bi bi-folder2 me-1"></i>{{ $d->project->name }}</span>
                            @endif
                            @if($d->purpose_label)
                                <span>•</span>
                                <span class="badge bg-white bg-opacity-10 text-white rounded-pill px-2">{{ $d->purpose_label }}</span>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Left Side: Amount & Action Button --}}
                <div class="d-flex align-items-center gap-3 flex-shrink-0">
                    <div class="text-end">
                        <div class="donation-amount-val {{ $d->status === 'cancelled' ? 'text-decoration-line-through text-muted' : 'amount-cash' }}">
                            +{{ number_format($d->amount, 0) }}
                            <span class="amount-currency">ج.م</span>
                        </div>
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
                <i class="bi bi-inbox fs-2 opacity-50 d-block mb-2"></i>
                <span>لا توجد تبرعات نقدية مطابقة للعرض</span>
            </div>
        @endforelse
    </div>

    {{-- Floating Bulk Actions Bar --}}
    <div class="donations-bulk-bar d-none align-items-center gap-3" id="cashBulkBar">
        <div class="d-flex align-items-center gap-2">
            <span class="bulk-count-badge">
                <i class="bi bi-check2-circle text-success fs-6"></i>
                <span>تم تحديد <b id="cashSelectedCount" class="text-white">0</b> تبرع</span>
            </span>
            <button type="button" class="btn btn-sm btn-link text-white-50 text-decoration-none p-1 bulk-clear-btn" onclick="clearBulkSelection('cash-check')" title="إلغاء التحديد">
                <i class="bi bi-x-circle"></i> إلغاء
            </button>
        </div>
        <div class="vr bg-secondary opacity-50 my-1 mx-1" style="height: 24px;"></div>
        <div class="d-flex align-items-center gap-2 ms-auto">
            <button type="button" class="btn btn-danger btn-sm rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm bulk-delete-btn" onclick="submitBulkDelete('cash-check')">
                <i class="bi bi-trash3-fill"></i>
                <span>حذف المحدد</span>
            </button>
        </div>
    </div>

    <div class="mt-4 d-flex justify-content-center" dir="ltr">
        {{ $cashDonations->appends(request()->query())->links('pagination::bootstrap-5') }}
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

{{-- Quick Receipt Modal (إيصال استلام نقدية سريع) --}}
<div class="modal fade" id="quickReceiptModal" tabindex="-1" aria-labelledby="quickReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('donations.store') }}" class="modal-content border-0 shadow-lg">
            @csrf
            <input type="hidden" name="type" value="cash">
            <input type="hidden" name="currency" value="EGP">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="quickReceiptModalLabel">
                    <i class="bi bi-receipt me-2"></i> إيصال استلام نقدية سريع
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background:#fafafa">
                <div class="card p-3 border-success mb-3 bg-white">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">رقم الإيصال <span class="text-danger">*</span></label>
                            <input name="receipt_number" class="form-control fw-bold text-success border-success" placeholder="مثال: 06501" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">المبلغ (جنيه)</label>
                            <input name="amount" type="number" step="0.01" class="form-control fw-bold text-dark" placeholder="مثال: 250.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">تحريراً في (التاريخ)</label>
                            <input name="received_at" type="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">وصل من (اسم المتبرع) <span class="text-danger">*</span></label>
                        <select name="donor_id" id="quickDonorSelect" class="form-select" required>
                            <option value="">— اختر المتبرع —</option>
                            @foreach($receiptDonors as $donorItem)
                                <option value="{{ $donorItem->id }}">{{ $donorItem->name }} {{ $donorItem->phone ? '('.$donorItem->phone.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">المشروع</label>
                        <select name="project_id" class="form-select">
                            <option value="">— اختر مشروع (اختياري) —</option>
                            @foreach($receiptProjects as $projectItem)
                                <option value="{{ $projectItem->id }}">{{ $projectItem->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold text-primary"><i class="bi bi-megaphone-fill me-1"></i> التبرع للحملات النشطة</label>
                        <select name="campaign_id" class="form-select border-primary">
                            <option value="">— اختر حملة نشطة للتبرع لها —</option>
                            @foreach(($receiptCampaigns ?? []) as $campItem)
                                <option value="{{ $campItem->id }}">{{ $campItem->name }} (المستهدف: {{ number_format($campItem->target_amount ?: $campItem->goal_amount, 0) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">وذلك قيمة (نوع التخصيص)</label>
                        <select name="purpose" id="quickPurposeSelect" class="form-select" required>
                            <option value="">— اختر —</option>
                            <option value="kafalat_aytam">كفالات أيتام</option>
                            <option value="kafalat_awram">كفالات أورام</option>
                            <option value="sadaqat">صدقات</option>
                            <option value="zakat_maal">زكاة مال</option>
                            <option value="sadaqa_jariya">صدقات جارية</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">طريقة وسيلة التحصيل</label>
                        <select name="cash_channel" class="form-select" required>
                            <option value="cash">نقدي</option>
                            <option value="instapay">انستا باي (Instapay)</option>
                            <option value="vodafone_cash">فودافون كاش</option>
                            <option value="delegate">مندوب تحصيل</option>
                        </select>
                    </div>

                    <div class="col-md-12" id="quickMembersWrapper" style="display:none">
                        <label class="form-label fw-bold" id="quickMembersLabel">اختر الملفات المكفولة</label>
                        <select name="family_member_ids[]" id="quickMembersSelect" class="form-select" multiple></select>
                        <div id="quickMembersWarning" class="form-text text-muted"></div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">الخزينة <span class="text-danger">*</span></label>
                        <select name="treasury_id" class="form-select" required>
                            <option value="">— اختر الخزينة —</option>
                            @foreach($receiptTreasuries as $treasuryItem)
                                <option value="{{ $treasuryItem->id }}">{{ $treasuryItem->name }} — {{ number_format((float)$treasuryItem->current_balance, 2) }} {{ $treasuryItem->currency }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">ملاحظات التخصيص أو كود الحالة</label>
                        <input name="allocation_note" class="form-control" placeholder="مثال: كود حالة 105 أو تخصيص خاص...">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success px-4 fw-bold">
                    <i class="bi bi-check-lg me-1"></i> تسجيل وإصدار الإيصال
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $quickDonorMembersData = $receiptDonors->mapWithKeys(function ($donor) {
        return [(string)$donor->id => $donor->sponsoredFamilyMembers->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'code' => $member->code,
                'relationship' => $member->relationship,
                'is_patient' => (bool)$member->is_patient,
                'family' => $member->beneficiary?->full_name,
            ];
        })->values()];
    });
    $quickAllMembersData = $allFamilyMembers->map(function ($member) {
        return [
            'id' => $member->id,
            'name' => $member->full_name,
            'code' => $member->code,
            'relationship' => $member->relationship,
            'is_patient' => (bool)$member->is_patient,
            'family' => $member->beneficiary?->full_name,
        ];
    });
@endphp
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#quickCashDonationSearch').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'الكل (اختر متبرع أو مشروع للبحث...)',
                tags: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج مطابقة — اضغط Enter للبحث بهذا النص";
                    },
                    removeAllItems: function() {
                        return "مسح الاختيار";
                    }
                }
            }).on('select2:select', function(e) {
                $(this).closest('form').submit();
            }).on('select2:clear', function(e) {
                $(this).val('').trigger('change');
                setTimeout(() => { $(this).closest('form').submit(); }, 50);
            });
            // Update tooltip on clear button
            $('#quickCashDonationSearch').siblings('.select2-container').find('.select2-selection__clear').attr('title', 'مسح الاختيار');
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

    function clearBulkSelection(className) {
        document.querySelectorAll('.' + className).forEach(cb => cb.checked = false);
        const masterCheck = document.getElementById('selectAll');
        if (masterCheck) masterCheck.checked = false;
        updateBulkBar(className);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('cash-check')) updateBulkBar('cash-check');
    });

    function updateBulkBar(className) {
        const checked = document.querySelectorAll('.' + className + ':checked').length;
        const bar = document.getElementById('cashBulkBar');
        const countEl = document.getElementById('cashSelectedCount');
        if (countEl) countEl.textContent = checked;
        if (!bar) return;
        if (checked > 0) {
            bar.classList.remove('d-none');
            requestAnimationFrame(() => bar.classList.add('active'));
        } else {
            bar.classList.remove('active');
            setTimeout(() => {
                if (!bar.classList.contains('active')) {
                    bar.classList.add('d-none');
                }
            }, 250);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateBulkBar('cash-check');
    });

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

document.addEventListener('DOMContentLoaded', function () {
    const donorMembers = @json($quickDonorMembersData);
    const allMembersList = @json($quickAllMembersData);
    const donorSelect = document.getElementById('quickDonorSelect');
    const purposeSelect = document.getElementById('quickPurposeSelect');
    const membersWrapper = document.getElementById('quickMembersWrapper');
    const membersSelect = document.getElementById('quickMembersSelect');
    const membersLabel = document.getElementById('quickMembersLabel');
    const warning = document.getElementById('quickMembersWarning');

    function refreshQuickMembers() {
        const purpose = purposeSelect?.value || '';
        const donorId = String(donorSelect?.value || '');
        const isKafala = ['kafalat_aytam', 'kafalat_awram'].includes(purpose);

        const donorSpecific = (donorMembers[donorId] || []).filter(member => purpose === 'kafalat_awram'
            ? member.is_patient
            : (!member.is_patient && member.relationship === 'child'));

        const members = donorSpecific.length > 0 ? donorSpecific : allMembersList.filter(member => purpose === 'kafalat_awram'
            ? member.is_patient
            : (!member.is_patient && member.relationship === 'child'));

        membersWrapper.style.display = isKafala ? '' : 'none';
        membersSelect.disabled = !isKafala;
        membersSelect.required = isKafala;
        membersSelect.innerHTML = '';
        if (!isKafala) return;

        members.forEach(member => {
            const option = document.createElement('option');
            option.value = member.id;
            option.textContent = `${member.name} — ${member.family || 'الأسرة'}${member.code ? ' — ' + member.code : ''}`;
            membersSelect.appendChild(option);
        });
        membersLabel.textContent = purpose === 'kafalat_awram' ? 'اختر مرضى الأورام المكفولين' : 'اختر الأطفال المكفولين';
        warning.textContent = members.length ? 'يمكن اختيار اسم واحد أو أكثر من الحالات المتاحة.' : 'لا توجد حالات مسجلة حالياً لهذا البند.';
    }

    donorSelect?.addEventListener('change', refreshQuickMembers);
    purposeSelect?.addEventListener('change', refreshQuickMembers);
});
</script>
@endsection
