@extends('layouts.app')
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    .select2-container--bootstrap-5 .select2-selection {
        height: 44px !important;
        min-height: 44px !important;
        border-color: #dee2e6;
        border-radius: 10px !important;
        display: flex !important;
        align-items: center !important;
    }
    .select2-container--bootstrap-5 .select2-selection__rendered {
        line-height: 44px !important;
        font-size: 0.95rem;
    }
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        border: 1px solid #e2e8f0;
        z-index: 1060;
    }

    /* Modern Donations UX Overhaul */
    .donations-view-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 18px;
    }

    .donation-view-switcher {
        display: inline-flex;
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 4px;
        gap: 4px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    [data-bs-theme="light"] .donation-view-switcher,
    body:not(.theme-dark) .donation-view-switcher {
        background: #f1f5f9;
        border-color: #cbd5e1;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .view-switch-btn {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding: 7px 16px;
        border-radius: 8px;
        font-weight: 700;
        font-size: 0.86rem;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .view-switch-btn:hover {
        color: #f8fafc;
        background: rgba(255, 255, 255, 0.06);
    }

    [data-bs-theme="light"] .view-switch-btn:hover,
    body:not(.theme-dark) .view-switch-btn:hover {
        color: #0f172a;
        background: rgba(0, 0, 0, 0.05);
    }

    .view-switch-btn.active {
        background: #0f172a;
        color: #ffffff;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    }

    [data-bs-theme="light"] .view-switch-btn.active,
    body:not(.theme-dark) .view-switch-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1);
    }

    /* Column Panels */
    .donation-column-panel {
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.2);
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.25s ease;
    }

    [data-bs-theme="light"] .donation-column-panel,
    body:not(.theme-dark) .donation-column-panel {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
    }

    .panel-header-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 14px;
        margin-bottom: 16px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    [data-bs-theme="light"] .panel-header-box,
    body:not(.theme-dark) .panel-header-box {
        border-bottom-color: #f1f5f9;
    }

    .panel-header-icon {
        width: 38px;
        height: 38px;
        min-width: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.15rem;
    }

    .bg-emerald-gradient {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.4));
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .bg-purple-gradient {
        background: linear-gradient(135deg, rgba(168, 85, 247, 0.2), rgba(126, 34, 206, 0.4));
        color: #c084fc;
        border: 1px solid rgba(168, 85, 247, 0.3);
    }

    .badge-stat-pill {
        font-size: 0.82rem;
        padding: 5px 12px;
        border-radius: 20px;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .badge-stat-cash {
        background: rgba(16, 185, 129, 0.12);
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.25);
    }

    .badge-stat-inkind {
        background: rgba(245, 158, 11, 0.12);
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.25);
    }

    /* Modern Donation Row Card */
    .donation-row-card {
        background: #0f172a;
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 14px;
        padding: 13px 16px;
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }

    [data-bs-theme="light"] .donation-row-card,
    body:not(.theme-dark) .donation-row-card {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    .donation-row-card:hover {
        background: #1e293b;
        border-color: rgba(16, 185, 129, 0.4);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
    }

    [data-bs-theme="light"] .donation-row-card:hover,
    body:not(.theme-dark) .donation-row-card:hover {
        background: #ffffff;
        border-color: #10b981;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
    }

    .donation-row-card.is-cancelled {
        background: rgba(239, 68, 68, 0.05) !important;
        border-color: rgba(239, 68, 68, 0.25) !important;
        opacity: 0.75;
    }

    /* Channel Avatar Icons */
    .donation-channel-avatar {
        width: 42px;
        height: 42px;
        min-width: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    }

    .channel-instapay {
        background: linear-gradient(135deg, rgba(99, 102, 241, 0.2), rgba(79, 70, 229, 0.35));
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.3);
    }

    .channel-vodafone_cash {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2), rgba(220, 38, 38, 0.35));
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.3);
    }

    .channel-delegate {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.2), rgba(2, 132, 199, 0.35));
        color: #38bdf8;
        border: 1px solid rgba(14, 165, 233, 0.3);
    }

    .channel-cash {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2), rgba(5, 150, 105, 0.35));
        color: #34d399;
        border: 1px solid rgba(16, 185, 129, 0.3);
    }

    .channel-in_kind {
        background: linear-gradient(135deg, rgba(245, 158, 11, 0.2), rgba(217, 119, 6, 0.35));
        color: #fbbf24;
        border: 1px solid rgba(245, 158, 11, 0.3);
    }

    .donor-name-title {
        color: #f8fafc;
        font-weight: 700;
        font-size: 0.96rem;
        text-decoration: none;
        transition: color 0.15s ease;
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
        font-size: 0.72rem;
        padding: 2px 7px;
        border-radius: 6px;
        background: rgba(255, 255, 255, 0.08);
        color: #cbd5e1;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    [data-bs-theme="light"] .badge-receipt-mini,
    body:not(.theme-dark) .badge-receipt-mini {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }

    /* Amount & Actions in Horizontal Row */
    .donation-amount-val {
        font-size: 1.15rem;
        font-weight: 800;
        font-family: monospace, system-ui;
        letter-spacing: -0.5px;
        line-height: 1.2;
    }

    .amount-cash {
        color: #10b981;
    }

    .amount-inkind {
        color: #f59e0b;
    }

    .amount-currency {
        font-size: 0.75rem;
        font-weight: 600;
        font-family: system-ui, -apple-system;
        opacity: 0.85;
        margin-inline-start: 2px;
    }

    .btn-icon-action {
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.06);
        color: #94a3b8;
        border: 1px solid rgba(255, 255, 255, 0.12);
        cursor: pointer;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    [data-bs-theme="light"] .btn-icon-action,
    body:not(.theme-dark) .btn-icon-action {
        background: #f1f5f9;
        color: #475569;
        border-color: #cbd5e1;
    }

    .btn-icon-action:hover {
        background: #10b981;
        color: #ffffff;
        border-color: #10b981;
        transform: scale(1.08);
        box-shadow: 0 0 12px rgba(16, 185, 129, 0.4);
    }

    .in-kind-cta-box {
        background: rgba(245, 158, 11, 0.05);
        border: 1.5px dashed rgba(245, 158, 11, 0.25);
        border-radius: 12px;
    }
  </style>
@endsection
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التبرعات 🎁</div>
        <h1 class="hero-title">سجل التبرعات</h1>
        <p class="hero-subtitle">إدارة ومتابعة التبرعات النقدية والعينية الواردة</p>
        <div class="hero-actions d-flex gap-2">
            <button type="button" class="btn btn-sm btn-white text-dark fw-bold rounded-pill px-3 shadow-sm" style="background: #ffffff !important; color: #0f172a !important; border: 1px solid #ffffff !important;" data-bs-toggle="modal" data-bs-target="#quickReceiptModal">
                <i class="bi bi-receipt text-success me-1"></i> <span style="color: #0f172a !important; font-weight: 700;">إيصال استلام نقدية سريع</span>
            </button>
            <a href="{{ route('donations.create') }}" class="btn btn-sm rounded-pill px-4 text-white" style="color: #ffffff !important; font-weight: 600;">
                <i class="bi bi-plus-lg me-1"></i> إضافة تبرع كامل
            </a>
        </div>
    </div>
    <i class="bi bi-gift-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-1">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="get">
        <div class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase text-muted">
                    <i class="bi bi-search text-primary me-1"></i> بحث سريع
                </label>
                <select name="q" id="quickDonationSearch" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">جميع التبرعات (اختر أو ابحث...)</option>
                    @if(!empty($q))
                        <option value="{{ $q }}" selected>{{ $q }} (بحث مخصص)</option>
                    @endif
                    <optgroup label="المتبرعون المسجلون">
                        @foreach($allDonors ?? $receiptDonors as $dItem)
                            @php
                                $codeDisplay = $dItem->code ? (' [' . $dItem->code . ']') : (' [DON-' . $dItem->id . ']');
                                $phoneDisplay = $dItem->phone ? (' - ' . $dItem->phone) : '';
                                $isSelected = ($q ?? '') == $dItem->name || ($q ?? '') == $dItem->code || ($q ?? '') == $dItem->phone;
                            @endphp
                            <option value="{{ $dItem->name }}" @selected($isSelected)>
                                {{ $dItem->name }}{{ $codeDisplay }}{{ $phoneDisplay }}
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
                <label class="form-label fw-bold small text-uppercase text-muted">الشهر</label>
                <select name="month" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">الكل</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month') == $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">السنة</label>
                <select name="year" class="form-select" style="height: 44px; border-radius: 10px;">
                    <option value="">الكل</option>
                    @foreach(range(date('Y'), 2023) as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2 align-items-center w-100" style="height: 44px;">
                    <button class="btn btn-primary flex-fill fw-bold" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                        <i class="bi bi-funnel fs-6"></i>
                        <span>تصفية</span>
                    </button>
                    <a class="btn btn-outline-secondary btn-icon-square" href="{{ route('donations.index') }}" title="مسح التصفية" style="width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                        <i class="bi bi-x-lg fs-6"></i>
                    </a>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle no-arrow btn-icon-square" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="تحميل وتصدير" style="width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                            <i class="bi bi-download fs-6"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2">
                            <li><a class="dropdown-item rounded-2 py-2" data-no-loader download href="{{ route('donations.export', array_merge(request()->query(), ['type' => 'cash'])) }}">
                                <i class="bi bi-cash me-2 text-success"></i> تبرعات نقدية
                            </a></li>
                            <li><a class="dropdown-item rounded-2 py-2" data-no-loader download href="{{ route('donations.export', array_merge(request()->query(), ['type' => 'in_kind'])) }}">
                                <i class="bi bi-box me-2 text-info"></i> تبرعات عينية
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Today's Channel Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg animate-slide-up animate-delay-2">
        <a href="{{ route('donations.index', ['channel' => 'cash']) }}" class="stat-card stat-primary text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">نقدي</div>
            <div class="stat-value">{{ number_format($todayByChannel['cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['cash']['count'] }} عملية</div>
            <i class="bi bi-cash-stack stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-3">
        <a href="{{ route('donations.index', ['channel' => 'vodafone_cash']) }}" class="stat-card stat-danger text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-phone-fill"></i></div>
            <div class="stat-label">فودافون كاش</div>
            <div class="stat-value">{{ number_format($todayByChannel['vodafone_cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['vodafone_cash']['count'] }} عملية</div>
            <i class="bi bi-phone-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-4">
        <a href="{{ route('donations.index', ['channel' => 'instapay']) }}" class="stat-card stat-info text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-credit-card-fill"></i></div>
            <div class="stat-label">انستا باي</div>
            <div class="stat-value">{{ number_format($todayByChannel['instapay']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['instapay']['count'] }} عملية</div>
            <i class="bi bi-credit-card-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-5">
        <a href="{{ route('donations.index', ['channel' => 'delegate']) }}" class="stat-card stat-warning text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-label">مندوب</div>
            <div class="stat-value">{{ number_format($todayByChannel['delegate']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['delegate']['count'] }} عملية</div>
            <i class="bi bi-person-badge-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-5">
        <a href="{{ route('donations.index', ['type' => 'in_kind']) }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-label">عيني</div>
            <div class="stat-value">{{ number_format((float) ($inKindToday->total ?? 0), 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ (int) ($inKindToday->count ?? 0) }} عملية</div>
            <i class="bi bi-box-seam-fill stat-bg-icon"></i>
        </a>
    </div>
</div>

{{-- Modern View Switcher --}}
<div class="donations-view-header animate-slide-up animate-delay-2">
    <div class="donation-view-switcher" role="group">
        <button type="button" class="view-switch-btn active" id="btnViewBoth" onclick="switchDonationsView('both')">
            <i class="bi bi-layout-split"></i>
            <span>عرض متجاور (كلاهما)</span>
        </button>
        <button type="button" class="view-switch-btn" id="btnViewCash" onclick="switchDonationsView('cash')">
            <i class="bi bi-cash-coin text-success"></i>
            <span>نقدية فقط ({{ $cashDonations->total() }})</span>
        </button>
        <button type="button" class="view-switch-btn" id="btnViewInKind" onclick="switchDonationsView('in_kind')">
            <i class="bi bi-gift text-warning"></i>
            <span>عينية فقط ({{ $inKindDonations->total() }})</span>
        </button>
    </div>
    <div class="text-muted small fw-bold">
        <span>إجمالي العمليات المعروضة: <strong class="text-primary fs-6">{{ $cashDonations->total() + $inKindDonations->total() }}</strong> عملية</span>
    </div>
</div>

{{-- Donations Lists --}}
<div class="row g-4 mb-4">
    @if(!request('type') || request('type') !== 'in_kind')
    <div class="col-lg-{{ request('channel') ? '12' : '6' }}" id="cashColumn">
        <div class="donation-column-panel">
            <div class="panel-header-box">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="selectAllCash" onclick="toggleAll('cash-check', this.checked)" class="form-check-input mt-0" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <div class="panel-header-icon bg-emerald-gradient">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">تبرعات نقدية</h5>
                        <small class="text-muted">التحصيلات النقدية والإلكترونية</small>
                    </div>
                </div>
                <div class="badge-stat-pill badge-stat-cash">
                    <i class="bi bi-wallet2"></i>
                    <span>{{ number_format($cashDonations->sum('amount'), 0) }} ج.م</span>
                    <span class="opacity-50">|</span>
                    <span>{{ $cashDonations->total() }} عملية</span>
                </div>
            </div>

            <div class="activity-feed flex-grow-1">
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
                    <div class="donation-row-card {{ $d->status === 'cancelled' ? 'is-cancelled' : '' }}">
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
                                </div>
                            </div>
                        </div>

                        {{-- Left Side: Amount & Action Button in HORIZONTAL ALIGNMENT --}}
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
                                    <li><a class="dropdown-item py-1.5 text-info" href="{{ route('donations.show', $d) }}?preview=1" target="_blank"><i class="bi bi-file-earmark-text text-info me-2"></i>معاينة الوصل قبل الطباعة</a></li>
                                    <li><a class="dropdown-item py-1.5 text-success" href="{{ route('donations.show', $d) }}?print=1" target="_blank"><i class="bi bi-printer text-success me-2"></i>طباعة الإيصال</a></li>
                                    @if(auth()->check() && $d->status !== 'cancelled')
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item py-1.5 text-warning" href="{{ route('donations.edit', $d) }}"><i class="bi bi-pencil me-2"></i>طلب تعديل</a></li>
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 text-danger" onclick="openCancelModal('{{ $d->id }}', true)">
                                                <i class="bi bi-x-circle me-2"></i>طلب إلغاء
                                            </button>
                                        </li>
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

            {{-- Bulk Actions Bar --}}
            <div class="donations-bulk-bar d-flex gap-2 align-items-center mt-3 p-3 flex-wrap" id="cashBulkBar" style="display:none;">
                <span class="small fw-bold text-white px-2 py-1 bg-white bg-opacity-10 rounded-pill" id="cashSelectedCount">0 محدد</span>
                <button class="btn btn-outline-danger btn-sm ms-auto rounded-pill px-3" onclick="submitBulkDelete('cash-check')"><i class="bi bi-trash me-1"></i> حذف المحدد</button>
            </div>

            <div class="mt-4 d-flex justify-content-center" dir="ltr">{{ $cashDonations->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @endif

    @if(!request('channel'))
    <div class="col-lg-{{ request('type') === 'in_kind' ? '12' : '6' }}" id="inKindColumn">
        <div class="donation-column-panel">
            <div class="panel-header-box">
                <div class="d-flex align-items-center gap-2">
                    <input type="checkbox" id="selectAllInKind" onclick="toggleAll('inkind-check', this.checked)" class="form-check-input mt-0" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
                    <div class="panel-header-icon bg-purple-gradient">
                        <i class="bi bi-gift-fill"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-white">تبرعات عينية</h5>
                        <small class="text-muted">المواد والأجهزة والمخزون المورد</small>
                    </div>
                </div>
                <div class="badge-stat-pill badge-stat-inkind">
                    <i class="bi bi-box-seam"></i>
                    <span>{{ number_format($inKindDonations->sum('estimated_value'), 0) }} ج.م</span>
                    <span class="opacity-50">|</span>
                    <span>{{ $inKindDonations->total() }} عملية</span>
                </div>
            </div>

            <div class="activity-feed flex-grow-1">
                @forelse($inKindDonations as $d)
                    <div class="donation-row-card {{ $d->status === 'cancelled' ? 'is-cancelled' : '' }}">
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
                                    @if($d->project)
                                        <span>•</span>
                                        <span class="text-muted"><i class="bi bi-folder2 me-1"></i>{{ $d->project->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Left Side: Amount & Action Button in HORIZONTAL ALIGNMENT --}}
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
                                    <li><a class="dropdown-item py-1.5 text-info" href="{{ route('donations.show', $d) }}?preview=1" target="_blank"><i class="bi bi-file-earmark-text text-info me-2"></i>معاينة الوصل قبل الطباعة</a></li>
                                    <li><a class="dropdown-item py-1.5 text-success" href="{{ route('donations.show', $d) }}?print=1" target="_blank"><i class="bi bi-printer text-success me-2"></i>طباعة الإيصال</a></li>
                                    @if(auth()->check() && $d->status !== 'cancelled')
                                        <li><hr class="dropdown-divider my-1"></li>
                                        <li><a class="dropdown-item py-1.5 text-warning" href="{{ route('donations.edit', $d) }}"><i class="bi bi-pencil me-2"></i>طلب تعديل</a></li>
                                        <li>
                                            <button type="button" class="dropdown-item py-1.5 text-danger" onclick="openCancelModal('{{ $d->id }}', true)">
                                                <i class="bi bi-x-circle me-2"></i>طلب إلغاء
                                            </button>
                                        </li>
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

                {{-- Balanced CTA when few in-kind items to eliminate awkward empty space --}}
                @if($inKindDonations->count() < 4)
                    <div class="in-kind-cta-box mt-3 p-3 text-center">
                        <div class="d-flex align-items-center justify-content-center gap-2 mb-1 text-warning">
                            <i class="bi bi-box-seam-fill fs-5"></i>
                            <span class="fw-bold small">تسجيل وتوريد تبرعات عينية</span>
                        </div>
                        <p class="text-muted small mb-2">يمكنك استلام وتوريد المواد الغذائية والملابس والأجهزة وتوجيهها للمخازن مباشرة.</p>
                        <a href="{{ route('donations.create', ['type' => 'in_kind']) }}" class="btn btn-sm btn-outline-warning rounded-pill px-3 fw-bold">
                            <i class="bi bi-plus-lg me-1"></i> إضافة تبرع عيني جديد
                        </a>
                    </div>
                @endif
            </div>

            {{-- Bulk Actions Bar --}}
            <div class="donations-bulk-bar d-flex gap-2 align-items-center mt-3 p-3 flex-wrap" id="inkindBulkBar" style="display:none;">
                <span class="small fw-bold text-white px-2 py-1 bg-white bg-opacity-10 rounded-pill" id="inkindSelectedCount">0 محدد</span>
                <button class="btn btn-outline-danger btn-sm ms-auto rounded-pill px-3" onclick="submitBulkDelete('inkind-check')"><i class="bi bi-trash me-1"></i> حذف المحدد</button>
            </div>

            <div class="mt-4 d-flex justify-content-center" dir="ltr">{{ $inKindDonations->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @endif
</div>

{{-- Hidden Bulk Delete Form --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('donations.bulk-destroy') }}" style="display:none;">
    @csrf
    <div id="bulkDeleteIds"></div>
</form>
@endsection

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

<script>
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
        if (e.target.classList.contains('cash-check')) updateBulkBar('cash-check');
        if (e.target.classList.contains('inkind-check')) updateBulkBar('inkind-check');
    });

    function updateBulkBar(className) {
        const checked = document.querySelectorAll('.' + className + ':checked').length;
        if (className === 'cash-check') {
            const bar = document.getElementById('cashBulkBar');
            document.getElementById('cashSelectedCount').textContent = checked + ' محدد';
            bar.style.display = checked > 0 ? 'flex' : 'none';
            bar.style.setProperty('display', checked > 0 ? 'flex' : 'none', 'important');
        } else {
            const bar = document.getElementById('inkindBulkBar');
            document.getElementById('inkindSelectedCount').textContent = checked + ' محدد';
            bar.style.display = checked > 0 ? 'flex' : 'none';
            bar.style.setProperty('display', checked > 0 ? 'flex' : 'none', 'important');
        }
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

    function switchDonationsView(mode) {
        const cashCol = document.getElementById('cashColumn');
        const inKindCol = document.getElementById('inKindColumn');
        const btnBoth = document.getElementById('btnViewBoth');
        const btnCash = document.getElementById('btnViewCash');
        const btnInKind = document.getElementById('btnViewInKind');

        [btnBoth, btnCash, btnInKind].forEach(b => b?.classList.remove('active'));

        if (mode === 'both') {
            btnBoth?.classList.add('active');
            if (cashCol) {
                cashCol.classList.remove('d-none');
                cashCol.classList.remove('col-lg-12');
                cashCol.classList.add('col-lg-6');
            }
            if (inKindCol) {
                inKindCol.classList.remove('d-none');
                inKindCol.classList.remove('col-lg-12');
                inKindCol.classList.add('col-lg-6');
            }
        } else if (mode === 'cash') {
            btnCash?.classList.add('active');
            if (cashCol) {
                cashCol.classList.remove('d-none');
                cashCol.classList.remove('col-lg-6');
                cashCol.classList.add('col-lg-12');
            }
            if (inKindCol) {
                inKindCol.classList.add('d-none');
            }
        } else if (mode === 'in_kind') {
            btnInKind?.classList.add('active');
            if (inKindCol) {
                inKindCol.classList.remove('d-none');
                inKindCol.classList.remove('col-lg-6');
                inKindCol.classList.add('col-lg-12');
            }
            if (cashCol) {
                cashCol.classList.add('d-none');
            }
        }
        try {
            localStorage.setItem('donations_view_mode', mode);
        } catch(e) {}
    }

    document.addEventListener('DOMContentLoaded', function() {
        try {
            const saved = localStorage.getItem('donations_view_mode');
            const urlParams = new URLSearchParams(window.location.search);
            if (saved && ['both', 'cash', 'in_kind'].includes(saved) && !urlParams.has('type') && !urlParams.has('channel')) {
                switchDonationsView(saved);
            }
        } catch(e) {}
    });
</script>

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
@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(function() {
        if (typeof $.fn.select2 !== 'undefined') {
            $('#quickDonationSearch').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'اختر متبرع أو مشروع أو اكتب للبحث...',
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

