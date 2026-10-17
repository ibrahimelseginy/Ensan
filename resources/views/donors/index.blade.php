@extends('layouts.app')
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    .select2-container--bootstrap-5 .select2-selection {
        min-height: 44px !important;
        height: 44px !important;
        background: #1e293b !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        border-radius: 12px !important;
        display: block !important;
        position: relative !important;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1) !important;
        transition: all 0.2s ease !important;
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
        padding-left: 42px !important;
        line-height: 42px !important;
        height: 42px !important;
        color: #f8fafc !important;
        font-weight: 500 !important;
        font-size: 0.92rem !important;
        direction: rtl !important;
        text-align: right !important;
        display: block !important;
        width: 100% !important;
        box-sizing: border-box !important;
        overflow: hidden !important;
        text-overflow: ellipsis !important;
        white-space: nowrap !important;
    }

    [data-bs-theme="light"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered,
    body:not(.theme-dark) .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
        color: #0f172a !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8 !important;
        font-weight: 400 !important;
    }

    /* Elegant Custom Clear Button on the Left in RTL */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
        position: absolute !important;
        left: 10px !important;
        right: auto !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        width: 22px !important;
        height: 22px !important;
        line-height: 1 !important;
        border-radius: 50% !important;
        background: rgba(255, 255, 255, 0.08) !important;
        background-image: none !important;
        color: #94a3b8 !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Arial, sans-serif !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        cursor: pointer !important;
        text-indent: 0 !important;
        overflow: hidden !important;
        white-space: nowrap !important;
        padding: 0 !important;
        margin: 0 !important;
        z-index: 10 !important;
        transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.15) !important;
    }

    [data-bs-theme="light"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear,
    body:not(.theme-dark) .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
        background: #f1f5f9 !important;
        border-color: #cbd5e1 !important;
        color: #64748b !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear span {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        line-height: 1 !important;
        width: 100% !important;
        height: 100% !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:hover {
        background: rgba(239, 68, 68, 0.15) !important;
        color: #ef4444 !important;
        border-color: rgba(239, 68, 68, 0.4) !important;
        transform: translateY(-50%) scale(1.08) !important;
        box-shadow: 0 2px 6px rgba(239, 68, 68, 0.25) !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear:empty::before {
        content: '×';
        line-height: 1;
        font-size: 14px;
    }

    /* Dropdown Arrow */
    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
        position: absolute !important;
        left: auto !important;
        right: 12px !important;
        top: 50% !important;
        transform: translateY(-50%) !important;
        height: auto !important;
        width: auto !important;
    }

    .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow b {
        border-color: #94a3b8 transparent transparent transparent !important;
        border-width: 5px 4px 0 4px !important;
        transition: transform 0.2s ease !important;
    }

    .select2-container--bootstrap-5.select2-container--open .select2-selection--single .select2-selection__arrow b {
        border-color: transparent transparent #10b981 transparent !important;
        border-width: 0 4px 5px 4px !important;
    }

    /* Dropdown Menu */
    .select2-container--bootstrap-5 .select2-dropdown {
        border-radius: 12px !important;
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.4) !important;
        border: 1px solid rgba(255, 255, 255, 0.12) !important;
        background: #1e293b !important;
        z-index: 1060 !important;
        overflow: hidden !important;
        margin-top: 4px !important;
    }

    [data-bs-theme="light"] .select2-container--bootstrap-5 .select2-dropdown,
    body:not(.theme-dark) .select2-container--bootstrap-5 .select2-dropdown {
        background: #ffffff !important;
        border-color: #e2e8f0 !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1) !important;
    }

    .select2-container--bootstrap-5 .select2-search--dropdown {
        padding: 8px 10px !important;
    }

    .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        background: #0f172a !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        border-radius: 8px !important;
        color: #f8fafc !important;
        padding: 7px 12px !important;
        font-size: 0.88rem !important;
    }

    [data-bs-theme="light"] .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field,
    body:not(.theme-dark) .select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
        background: #f8fafc !important;
        border-color: #cbd5e1 !important;
        color: #0f172a !important;
    }

    .select2-results__option {
        padding: 9px 14px !important;
        font-size: 0.88rem !important;
        color: #e2e8f0 !important;
        border-radius: 6px !important;
        margin: 2px 6px !important;
        transition: background 0.15s ease, color 0.15s ease !important;
    }

    [data-bs-theme="light"] .select2-results__option,
    body:not(.theme-dark) .select2-results__option {
        color: #1e293b !important;
    }

    .select2-results__option--highlighted[aria-selected] {
        background: rgba(16, 185, 129, 0.15) !important;
        color: #10b981 !important;
        font-weight: 600 !important;
    }

    .select2-results__option[aria-selected="true"] {
        background: rgba(16, 185, 129, 0.25) !important;
        color: #34d399 !important;
        font-weight: 600 !important;
    }

    /* Segmented Control Unified Card */
    .segmented-type-card {
        display: flex;
        align-items: center;
        width: 100%;
        height: 44px;
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        border-radius: 12px;
        padding: 3px;
        position: relative;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        transition: all 0.2s ease;
    }

    [data-bs-theme="light"] .segmented-type-card,
    body:not(.theme-dark) .segmented-type-card {
        background: #f8fafc;
        border-color: #cbd5e1;
    }

    #donorBulkBar {
        background: #1e293b;
        border: 1px solid rgba(255, 255, 255, 0.12);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.25);
    }

    [data-bs-theme="light"] #donorBulkBar,
    body:not(.theme-dark) #donorBulkBar {
        background: #ffffff !important;
        border-color: #cbd5e1 !important;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.08) !important;
    }

    [data-bs-theme="light"] #donorBulkBar #donorSelectedCount,
    body:not(.theme-dark) #donorBulkBar #donorSelectedCount {
        color: #0f172a !important;
        background: rgba(0, 0, 0, 0.08) !important;
    }

    .segmented-type-item {
        flex: 1;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        font-size: 0.88rem;
        font-weight: 600;
        color: #94a3b8;
        border-radius: 9px;
        cursor: pointer;
        user-select: none;
        margin: 0;
        padding: 0 6px;
        transition: all 0.2s ease;
        border: none;
        background: transparent;
        text-decoration: none;
    }

    [data-bs-theme="light"] .segmented-type-item,
    body:not(.theme-dark) .segmented-type-item {
        color: #64748b;
    }

    .segmented-type-item:hover {
        color: #ffffff;
        background: rgba(255, 255, 255, 0.05);
    }

    [data-bs-theme="light"] .segmented-type-item:hover,
    body:not(.theme-dark) .segmented-type-item:hover {
        color: #0f172a;
        background: rgba(0, 0, 0, 0.04);
    }

    .btn-check:checked + .segmented-type-item {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        box-shadow: 0 2px 8px rgba(5, 150, 105, 0.35) !important;
    }

    .segmented-divider {
        width: 1px;
        height: 20px;
        background-color: rgba(255, 255, 255, 0.14);
        flex-shrink: 0;
        transition: opacity 0.2s ease;
    }

    [data-bs-theme="light"] .segmented-divider,
    body:not(.theme-dark) .segmented-divider {
        background-color: #cbd5e1;
    }

    .btn-check:checked + .segmented-type-item + .segmented-divider {
        opacity: 0;
    }
    .segmented-divider:has(+ .btn-check:checked) {
        opacity: 0;
    }
  </style>
@endsection

@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #cf6a8b 0%, #b24d6f 50%, #923251 100%); box-shadow: 0 10px 30px -10px rgba(178, 77, 111, 0.45);">
    <div class="hero-content">
        <div class="hero-greeting">إدارة الموارد ❤️</div>
        <h1 class="hero-title">المتبرعون</h1>
        <p class="hero-subtitle">عرض وإدارة قاعدة بيانات المتبرعين وسجل تبرعاتهم</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('donations.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة تبرع
            </a>
        </div>
    </div>
    <i class="bi bi-heart-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-1">
        <a href="{{ route('donors.index') }}" class="stat-card stat-info text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي المتبرعين</div>
            <div class="stat-value">{{ number_format($totals['all']) }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-2">
        <a href="{{ route('donors.index', ['active' => '1']) }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">متبرع نشط</div>
            <div class="stat-value">{{ number_format($totals['active']) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> نشط</div>
            <i class="bi bi-check-circle-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-3">
        <a href="{{ route('donors.index', ['classification' => 'recurring']) }}" class="stat-card stat-purple text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
            <div class="stat-label">النوع: متكرر</div>
            <div class="stat-value">{{ number_format($totals['recurring']) }}</div>
            <i class="bi bi-arrow-repeat stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-4">
        <a href="{{ route('donors.index', ['classification' => 'one_time']) }}" class="stat-card stat-warning text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-1-circle-fill"></i></div>
            <div class="stat-label">النوع: مرة واحدة</div>
            <div class="stat-value">{{ number_format($totals['one_time']) }}</div>
            <i class="bi bi-1-circle-fill stat-bg-icon"></i>
        </a>
    </div>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-5">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">
                <i class="bi bi-search text-primary me-1"></i> بحث سريع
            </label>
            <select name="q" id="quickDonorSearch" class="form-select">
                <option value="">جميع المتبرعين (اختر أو ابحث...)</option>
                @if(!empty($q) && !$allDonors->contains(fn($item) => $item->name === $q || $item->code === $q || $item->phone === $q))
                    <option value="{{ $q }}" selected>{{ $q }} (بحث مخصص)</option>
                @endif
                @foreach($allDonors as $dItem)
                    @php
                        $codeDisplay = $dItem->code ? (' [' . $dItem->code . ']') : (' [DON-' . $dItem->id . ']');
                        $phoneDisplay = $dItem->phone ? (' - ' . $dItem->phone) : '';
                        $isSelected = ($q ?? '') == $dItem->name || ($q ?? '') == $dItem->code || ($q ?? '') == $dItem->phone;
                    @endphp
                    <option value="{{ $dItem->name }}" @selected($isSelected)>
                        {{ $dItem->name }}{{ $codeDisplay }}{{ $phoneDisplay }}
                    </option>
                @endforeach
            </select>
        </div>
        
        <div class="col-lg-2 col-md-6">
            <label class="form-label fw-bold small text-uppercase text-muted">نوع الجهة</label>
            <div class="segmented-type-card" role="group">
                <input type="radio" class="btn-check" name="type" id="typeAll" value="" {{ ($type ?? '') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="segmented-type-item" for="typeAll">
                    <span>الكل</span>
                </label>

                <div class="segmented-divider"></div>

                <input type="radio" class="btn-check" name="type" id="typeInd" value="individual" {{ ($type ?? '') == 'individual' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="segmented-type-item" for="typeInd">
                    <i class="bi bi-person"></i>
                    <span>فرد</span>
                </label>

                <div class="segmented-divider"></div>

                <input type="radio" class="btn-check" name="type" id="typeOrg" value="organization" {{ ($type ?? '') == 'organization' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="segmented-type-item" for="typeOrg">
                    <i class="bi bi-building"></i>
                    <span>منظمة</span>
                </label>
            </div>
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label fw-bold small text-uppercase text-muted">تصنيف التبرع</label>
            <select name="classification" class="form-select" onchange="this.form.submit()" style="height: 44px; border-radius: 10px;">
                <option value="">الكل</option>
                <option value="one_time" @selected(($classification ?? '')==='one_time')>مرة واحدة</option>
                <option value="recurring" @selected(($classification ?? '')==='recurring')>متكرر</option>
            </select>
        </div>
        <div class="col-lg-2 col-md-6">
            <label class="form-label fw-bold small text-uppercase text-muted">الحالة</label>
            <select name="active" class="form-select" onchange="this.form.submit()" style="height: 44px; border-radius: 10px;">
                <option value="">الكل</option>
                <option value="1" @selected(($active ?? '')==='1')>نشط</option>
                <option value="0" @selected(($active ?? '')==='0')>غير نشط</option>
            </select>
        </div>
        <div class="col-lg-3 col-md-12">
            <div class="d-flex flex-wrap gap-2 align-items-center w-100" style="height: 44px;">
                <button type="submit" class="btn btn-primary fw-bold px-2.5 flex-grow-1" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 4px;" title="تطبيق التصفية">
                    <i class="bi bi-funnel"></i>
                    <span>تصفية</span>
                </button>
                <a class="btn btn-outline-secondary btn-icon-square" href="{{ route('donors.index') }}" title="مسح التصفية" style="width: 44px; min-width: 44px; height: 44px; padding: 0; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;">
                    <i class="bi bi-x-lg"></i>
                </a>
                
                {{-- زرار عرض (معاينة قبل الطباعة) --}}
                <button type="button" class="btn btn-outline-info fw-bold px-2.5 flex-grow-1" onclick="previewFilteredDonors()" title="معاينة كشف المتبرعين قبل الطباعة" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                    <i class="bi bi-eye"></i>
                    <span>عرض</span>
                </button>

                {{-- زرار الطباعة --}}
                <button type="button" class="btn btn-outline-success fw-bold px-2.5 flex-grow-1" onclick="directPrintFilteredDonors()" title="طباعة كشف المتبرعين بالتصفية الحالية" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 4px;">
                    <i class="bi bi-printer"></i>
                    <span>طباعة</span>
                </button>

            </div>
        </div>
    </form>
</div>

<div class="row g-3">
@foreach($donors as $donor)
  @php $st = $donStats->get($donor->id); @endphp
  <div class="col-md-6 col-lg-4">
    <div class="kpi-card p-4 h-100 position-relative {{ $donor->classification==='recurring' ? 'kpi-primary' : 'kpi-info' }}">
      <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="d-flex align-items-center gap-3">
             <input type="checkbox" class="form-check-input donor-check" value="{{ $donor->id }}"
                    data-id="{{ $donor->id }}"
                    data-code="{{ $donor->code ?? ('DON-'.$donor->id) }}"
                    data-name="{{ $donor->name }}"
                    data-type="{{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }}"
                    data-phone="{{ $donor->phone ?? '—' }}"
                    data-classification="{{ $donor->classification==='recurring' ? 'متكرر' : 'مرة واحدة' }}"
                    data-total="{{ $st ? $st->total : 0 }}"
                    data-total-formatted="{{ $st ? number_format($st->total, 0) : '0' }}"
                    data-count="{{ $st ? $st->count : 0 }}"
                    data-active="{{ $donor->active ? 'نشط' : 'غير نشط' }}"
                    onchange="updateDonorBulkBar()"
                    onclick="event.stopPropagation(); updateDonorBulkBar();"
                    style="cursor: pointer; width: 1.35rem; height: 1.35rem; z-index: 2;">
             <div class="position-relative">
                <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-primary" style="width: 56px; height: 56px;">
                  <i class="bi {{ $donor->type === 'individual' ? 'bi-person' : 'bi-building' }} fs-3"></i>
                </div>
                 @if($donor->active)
                 <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-white rounded-circle"></span>
                 @else
                 <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle"></span>
                 @endif
             </div>
             <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <a href="{{ route('donors.show',$donor) }}" class="text-decoration-none text-dark stretched-link-custom">{{ $donor->name }}</a>
                </h5>
                <div class="text-muted small">
                    <span class="font-monospace text-primary">{{ $donor->code ?? ('DON-'.$donor->id) }}</span>
                    • {{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }}
                    • {{ $donor->classification==='recurring' ? 'متكرر' : 'مرة واحدة' }}
                </div>
             </div>
          </div>
          @php
              $isManagerUser = auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'));
          @endphp
          @if(isset($donor->pendingRequest) && $donor->pendingRequest && !$isManagerUser)
              <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-2 rounded-pill small">
                  <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
              </span>
          @else
              <div class="dropdown position-relative" style="z-index: 25;">
                  <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow border" style="z-index: 10060; min-width: 220px;">
                      <li><a class="dropdown-item" href="{{ route('donors.show',$donor) }}"><i class="bi bi-eye me-2 opacity-50"></i>عرض الملف</a></li>
                      <li><a class="dropdown-item text-info" href="{{ route('donors.show',$donor) }}?preview=1" target="_blank"><i class="bi bi-file-earmark-text me-2 text-info"></i>معاينة التقرير قبل الطباعة</a></li>
                      
                      @if(auth()->check())
                          @if($isManagerUser)
                              <li><a class="dropdown-item text-primary" href="{{ route('donors.edit',$donor) }}"><i class="bi bi-pencil me-2 opacity-50"></i>تعديل</a></li>
                              <li><hr class="dropdown-divider"></li>
                              <li>
                                <button class="dropdown-item text-danger" onclick="openCancelModal('{{ route('donors.destroy',$donor) }}', false)">
                                    <i class="bi bi-trash me-2 opacity-50"></i>حذف المتبرع
                                </button>
                              </li>
                          @else
                              <li><a class="dropdown-item text-warning" href="{{ route('donors.edit',$donor) }}"><i class="bi bi-pencil me-2 opacity-50"></i>طلب تعديل</a></li>
                              <li><hr class="dropdown-divider"></li>
                              <li>
                                <button class="dropdown-item text-warning" onclick="openCancelModal('{{ route('donors.destroy',$donor) }}', true)">
                                    <i class="bi bi-x-circle me-2 opacity-50"></i>طلب إلغاء
                                </button>
                              </li>
                          @endif
                      @endif
                  </ul>
              </div>
          @endif
      </div>

      <div class="row g-2 mb-3 mt-2 text-center">
          @php $st = $donStats->get($donor->id); @endphp
          <div class="col-6">
              <div class="bg-light rounded p-2">
                  <div class="small text-muted mb-1">إجمالي التبرعات</div>
                  <div class="fw-bold text-dark">{{ $st ? number_format($st->total, 0) : '0' }}</div>
              </div>
          </div>
          <div class="col-6">
              <div class="bg-light rounded p-2">
                  <div class="small text-muted mb-1">عدد المرات</div>
                  <div class="fw-bold text-dark">{{ $st ? $st->count : 0 }}</div>
              </div>
          </div>
      </div>

      <div class="d-flex align-items-center justify-content-between pt-2 border-top">
           <div class="text-muted small">
              <i class="bi bi-telephone me-1"></i> <span dir="ltr">{{ $donor->phone ?? '—' }}</span>
           </div>
           <a href="{{ route('donations.create', ['donor_id' => $donor->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3" style="position: relative; z-index: 1;">
              <i class="bi bi-plus-lg me-1"></i> تبرع
           </a>
      </div>
    </div>
  </div>
@endforeach
</div>

{{-- Bulk Action Bar --}}
<div class="gap-3 align-items-center mt-3 p-3 rounded-4 shadow-lg flex-wrap" id="donorBulkBar" style="display:none !important; background: #0f172a; border: 1px solid rgba(255,255,255,0.15);">
    <div class="d-flex align-items-center gap-2">
        <input type="checkbox" id="selectAllDonors" class="form-check-input mt-0" onclick="toggleAllDonors(this.checked)" style="width: 1.25rem; height: 1.25rem; cursor: pointer;">
        <span class="small fw-bold text-white px-2.5 py-1 bg-white bg-opacity-10 rounded-pill" id="donorSelectedCount">0 محدد</span>
    </div>
    
    <div class="ms-auto d-flex align-items-center gap-2 flex-wrap">
        {{-- Preview Selected Button --}}
        <button type="button" class="btn btn-info btn-sm px-3 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm rounded-pill text-white" onclick="previewSelectedDonors()" title="معاينة بيانات المتبرعين المحددين">
            <i class="bi bi-eye-fill fs-6"></i>
            <span>عرض المحدد</span>
        </button>

        {{-- Print Selected Button --}}
        <button type="button" class="btn btn-success btn-sm px-3 py-2 fw-bold d-flex align-items-center gap-2 shadow-sm rounded-pill" onclick="printSelectedDonors()" title="طباعة كشف المتبرعين المحددين">
            <i class="bi bi-printer-fill fs-6"></i>
            <span>طباعة المحدد</span>
            <span class="badge bg-white text-success rounded-pill font-monospace" id="printSelectedCount">0</span>
        </button>


        {{-- Delete Selected Button --}}
        <button type="button" class="btn btn-outline-danger btn-sm px-3 py-2 fw-bold d-flex align-items-center gap-1 rounded-pill" onclick="submitDonorBulkDelete()">
            <i class="bi bi-trash fs-6"></i>
            <span>حذف المحدد</span>
        </button>
    </div>
</div>

{{-- Hidden Form for Server-Side Print --}}
<form id="donorBulkPrintForm" method="POST" action="{{ route('donors.print-report.post') }}" target="_blank" style="display:none;">
    @csrf
    <div id="donorBulkPrintIds"></div>
</form>

{{-- Hidden Form for Bulk PDF Download --}}
<form id="donorBulkPdfForm" method="POST" action="{{ route('donors.export-pdf.post') }}" target="_blank" style="display:none;">
    @csrf
    <div id="donorBulkPdfIds"></div>
</form>

{{-- Hidden Bulk Delete Form --}}
<form id="donorBulkDeleteForm" method="POST" action="{{ route('donors.bulk-destroy') }}" style="display:none;">
    @csrf
    <div id="donorBulkDeleteIds"></div>
</form>

{{-- Hidden Container for Client-Side Universal Print Preview --}}
<div id="donorsPrintContainer" class="d-none"></div>

<div class="mt-3">{{ $donors->links() }}</div>

{{-- Cancel/Delete Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalTitle">تأكيد الإجراء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في المتابعة؟</p>
                <div class="mb-3" id="cancelReasonDiv">
                    <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea name="reason" id="cancelReasonInput" class="form-control" rows="3" placeholder="اكتب سبب الإلغاء هنا... - مطلوب" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-warning" id="cancelModalBtn">إرسال الطلب</button>
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
        function cleanSelect2Tooltips() {
            $('.select2-selection__rendered').removeAttr('title');
            $('.select2-selection__clear').removeAttr('title').attr('aria-label', 'مسح التحديد');
        }

        if (typeof $.fn.select2 !== 'undefined') {
            const $donorSelect = $('#quickDonorSearch').select2({
                theme: 'bootstrap-5',
                dir: 'rtl',
                allowClear: true,
                placeholder: 'اختر متبرع أو اكتب للبحث (اسم / كود / هاتف)...',
                tags: true,
                width: '100%',
                language: {
                    noResults: function() {
                        return "لا توجد نتائج مطابقة — اضغط Enter للبحث بهذا النص";
                    }
                }
            });

            $donorSelect.on('select2:select', function(e) {
                cleanSelect2Tooltips();
                $(this).closest('form').submit();
            }).on('select2:clear', function(e) {
                setTimeout(() => { $(this).closest('form').submit(); }, 50);
            }).on('select2:open select2:close change', function() {
                cleanSelect2Tooltips();
                setTimeout(cleanSelect2Tooltips, 20);
            });

            // Prevent native intrusive browser tooltips on hover
            cleanSelect2Tooltips();
            setTimeout(cleanSelect2Tooltips, 100);
            $(document).on('mouseenter', '.select2-container, .select2-selection, .select2-selection__rendered, .select2-selection__clear', function() {
                cleanSelect2Tooltips();
            });
        }
    });

    function openCancelModal(actionUrl, isRequest = true) {
        const form = document.getElementById('cancelForm');
        form.action = actionUrl;
        
        const title = document.getElementById('cancelModalTitle');
        const btn = document.getElementById('cancelModalBtn');
        const reasonDiv = document.getElementById('cancelReasonDiv');
        const reasonInput = document.getElementById('cancelReasonInput');

        if (isRequest) {
            title.textContent = 'طلب إلغاء المتبرع';
            title.className = 'modal-title text-warning';
            btn.textContent = 'إرسال طلب الإلغاء';
            btn.className = 'btn btn-warning';
            if (reasonDiv) reasonDiv.style.display = 'block';
            if (reasonInput) reasonInput.required = true;
        } else {
            title.textContent = 'تأكيد حذف المتبرع';
            title.className = 'modal-title text-danger';
            btn.textContent = 'حذف نهائي فوري';
            btn.className = 'btn btn-danger';
            if (reasonDiv) reasonDiv.style.display = 'none';
            if (reasonInput) { reasonInput.required = false; reasonInput.value = 'حذف مباشر من الإدارة'; }
        }
        
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('donor-check')) updateDonorBulkBar();
    });

    document.addEventListener('click', function(e) {
        const card = e.target.closest('.kpi-card');
        if (!card) return;
        if (e.target.closest('button, a, .dropdown, input, select, label')) return;
        const cb = card.querySelector('.donor-check');
        if (cb) {
            cb.checked = !cb.checked;
            updateDonorBulkBar();
        }
    });

    function toggleAllDonors(checked) {
        document.querySelectorAll('.donor-check').forEach(cb => cb.checked = checked);
        updateDonorBulkBar();
    }

    function updateDonorBulkBar() {
        const checkedBoxes = document.querySelectorAll('.donor-check:checked');
        const checked = checkedBoxes.length;
        const total = document.querySelectorAll('.donor-check').length;
        const bar = document.getElementById('donorBulkBar');
        const selectAllCb = document.getElementById('selectAllDonors');

        if (selectAllCb) {
            selectAllCb.checked = (total > 0 && checked === total);
            selectAllCb.indeterminate = (checked > 0 && checked < total);
        }

        const countLabel = document.getElementById('donorSelectedCount');
        if (countLabel) countLabel.textContent = checked + ' محدد';

        const printCountEl = document.getElementById('printSelectedCount');
        if (printCountEl) printCountEl.textContent = checked;

        if (bar) {
            bar.style.setProperty('display', checked > 0 ? 'flex' : 'none', 'important');
        }
    }

    function getDonorObjectFromCheckbox(cb) {
        return {
            id: cb.value,
            code: cb.getAttribute('data-code') || ('DON-' + cb.value),
            name: cb.getAttribute('data-name') || 'متبرع',
            type: cb.getAttribute('data-type') || 'فرد',
            phone: cb.getAttribute('data-phone') || '—',
            classification: cb.getAttribute('data-classification') || 'متكرر',
            total: parseFloat(cb.getAttribute('data-total')) || 0,
            totalFormatted: cb.getAttribute('data-total-formatted') || '0',
            count: parseInt(cb.getAttribute('data-count')) || 0,
            active: cb.getAttribute('data-active') || 'نشط'
        };
    }

    function buildDonorsPrintHTML(donorsList, reportTitle) {
        let totalDonationAmount = 0;
        let totalDonationCount = 0;

        let rowsHtml = '';
        donorsList.forEach((d, idx) => {
            totalDonationAmount += d.total;
            totalDonationCount += d.count;

            rowsHtml += `
                <tr style="text-align: center; background-color: ${idx % 2 === 0 ? '#ffffff' : '#f8fafc'};">
                    <td style="font-weight: bold; width: 35px; color: #0f172a; padding: 6px 4px;">${idx + 1}</td>
                    <td style="font-family: monospace; font-weight: 800; color: #047857; padding: 6px 4px;">${d.code}</td>
                    <td style="text-align: right; font-weight: 700; color: #0f172a; padding: 6px 8px;">${d.name}</td>
                    <td style="padding: 6px 4px;"><span class="badge text-white" style="font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; background-color: ${d.type === 'فرد' ? '#2563eb' : '#0891b2'} !important; color: #ffffff !important;">${d.type}</span></td>
                    <td dir="ltr" style="font-family: monospace; font-weight: 600; color: #0f172a; padding: 6px 4px;">${d.phone}</td>
                    <td style="color: #0f172a; padding: 6px 4px;">${d.classification}</td>
                    <td style="font-weight: 800; color: #047857; font-family: monospace; padding: 6px 6px;">${Number(d.total).toLocaleString()} ج.م</td>
                    <td style="font-weight: 700; color: #0f172a; padding: 6px 4px;">${d.count}</td>
                    <td style="padding: 6px 4px;"><span class="badge text-white" style="font-size: 11px; font-weight: 700; padding: 4px 8px; border-radius: 6px; background-color: ${d.active === 'نشط' ? '#059669' : '#64748b'} !important; color: #ffffff !important;">${d.active}</span></td>
                </tr>
            `;
        });

        return `
            <div id="donorsPrintContent" class="donors-sheet-content position-relative mx-auto" dir="rtl" style="width: 100%; font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif; background: #ffffff !important; color: #0f172a !important; box-sizing: border-box;">
                
                {{-- KPI Summary Boxes --}}
                <div class="row g-2 mb-3 text-center print-kpi-summary">
                    <div class="col-4">
                        <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px;">
                            <div style="font-size: 11.5px; color: #475569; font-weight: bold;">عدد المتبرعين المشمولين بالكشف</div>
                            <div style="font-size: 18px; font-weight: 900; color: #0f172a;">${donorsList.length} متبرع</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background: #ecfdf5; border: 1.5px solid #6ee7b7; border-radius: 8px; padding: 10px;">
                            <div style="font-size: 11.5px; color: #065f46; font-weight: bold;">إجمالي حصيلة التبرعات</div>
                            <div style="font-size: 18px; font-weight: 900; color: #047857; font-family: monospace;">${Number(totalDonationAmount).toLocaleString()} ج.م</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 10px;">
                            <div style="font-size: 11.5px; color: #475569; font-weight: bold;">إجمالي عدد حركات التبرع</div>
                            <div style="font-size: 18px; font-weight: 900; color: #0f172a;">${totalDonationCount} تبرع</div>
                        </div>
                    </div>
                </div>

                {{-- Data Table --}}
                <table class="table table-bordered table-sm align-middle mb-2" style="width: 100%; border-color: #cbd5e1; font-size: 11.5px;">
                    <thead style="background-color: #047857 !important; color: #ffffff !important; text-align: center;">
                        <tr>
                            <th style="width: 35px; color: #ffffff !important; background-color: #047857 !important;">#</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 110px;">كود المتبرع</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; text-align: right;">اسم المتبرع</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 85px;">نوع الجهة</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 115px;">رقم الهاتف</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 95px;">تصنيف التبرع</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 120px;">إجمالي التبرعات</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 75px;">عدد المرات</th>
                            <th style="color: #ffffff !important; background-color: #047857 !important; width: 75px;">الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        ${rowsHtml}
                    </tbody>
                    <tfoot style="background: #f1f5f9; font-weight: bold; border-top: 2px solid #047857;">
                        <tr style="text-align: center;">
                            <td colspan="6" style="text-align: right; padding-right: 14px; font-size: 12.5px; font-weight: 800; color: #0f172a;">الإجمالي العام للكشف:</td>
                            <td style="color: #047857; font-size: 13px; font-weight: 900; font-family: monospace;">${Number(totalDonationAmount).toLocaleString()} ج.م</td>
                            <td style="font-size: 13px; font-weight: 900; color: #0f172a;">${totalDonationCount}</td>
                            <td style="color: #64748b;">—</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        `;
    }

    // معاينة المحدد
    function previewSelectedDonors() {
        let checkedBoxes = Array.from(document.querySelectorAll('.donor-check:checked'));
        if (checkedBoxes.length === 0) {
            alert('يرجى تحديد متبرع واحد على الأقل للمعاينة');
            return;
        }

        const donorsList = checkedBoxes.map(getDonorObjectFromCheckbox);
        const container = document.getElementById('donorsPrintContainer');
        container.innerHTML = buildDonorsPrintHTML(donorsList, 'كشف بيانات المتبرعين المحددين');
        container.style.display = 'none';
        container.classList.add('d-none');

        if (window.EnsanPrintPreview) {
            window.EnsanPrintPreview.open('#donorsPrintContainer', {
                title: 'كشف بيانات المتبرعين المحددين (' + donorsList.length + ' متبرع)',
                orientation: 'landscape'
            });
        }
    }

    // طباعة المحدد
    function printSelectedDonors() {
        let checkedBoxes = Array.from(document.querySelectorAll('.donor-check:checked'));
        if (checkedBoxes.length === 0) {
            const allBoxes = Array.from(document.querySelectorAll('.donor-check'));
            if (allBoxes.length === 0) {
                alert('لا توجد بيانات متبرعين لعرضها أو طباعتها');
                return;
            }
            allBoxes.forEach(cb => cb.checked = true);
            updateDonorBulkBar();
            checkedBoxes = allBoxes;
        }

        const donorsList = checkedBoxes.map(getDonorObjectFromCheckbox);
        const container = document.getElementById('donorsPrintContainer');
        container.innerHTML = buildDonorsPrintHTML(donorsList, 'كشف بيانات المتبرعين المحددين');
        container.style.display = 'none';
        container.classList.add('d-none');

        if (window.EnsanPrintPreview) {
            window.EnsanPrintPreview.open('#donorsPrintContainer', {
                title: 'كشف بيانات المتبرعين المحددين (' + donorsList.length + ' متبرع)',
                orientation: 'landscape'
            });
        } else {
            const form = document.getElementById('donorBulkPrintForm');
            const idsDiv = document.getElementById('donorBulkPrintIds');
            idsDiv.innerHTML = '';
            checkedBoxes.forEach(cb => {
                const inp = document.createElement('input');
                inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
                idsDiv.appendChild(inp);
            });
            form.submit();
        }
    }

    // تنزيل المحدد كـ PDF
    function downloadSelectedDonorsPdf() {
        const checkedBoxes = Array.from(document.querySelectorAll('.donor-check:checked'));
        if (checkedBoxes.length === 0) {
            alert('يرجى تحديد متبرع واحد على الأقل لتنزيل ملف الـ PDF');
            return;
        }

        const form = document.getElementById('donorBulkPdfForm');
        const idsDiv = document.getElementById('donorBulkPdfIds');
        idsDiv.innerHTML = '';
        checkedBoxes.forEach(cb => {
            const inp = document.createElement('input');
            inp.type = 'hidden'; inp.name = 'ids[]'; inp.value = cb.value;
            idsDiv.appendChild(inp);
        });
        form.submit();
    }

    // معاينة نتائج التصفية (زرار عرض)
    function previewFilteredDonors() {
        const checkedBoxes = Array.from(document.querySelectorAll('.donor-check:checked'));
        if (checkedBoxes.length > 0) {
            previewSelectedDonors();
            return;
        }

        const allBoxes = Array.from(document.querySelectorAll('.donor-check'));
        if (allBoxes.length === 0) {
            alert('لا توجد بيانات متبرعين لعرضها بالتصفية الحالية');
            return;
        }

        const donorsList = allBoxes.map(getDonorObjectFromCheckbox);
        const container = document.getElementById('donorsPrintContainer');
        container.innerHTML = buildDonorsPrintHTML(donorsList, 'كشف المتبرعين — نتائج التصفية');
        container.style.display = 'none';
        container.classList.add('d-none');

        if (window.EnsanPrintPreview) {
            window.EnsanPrintPreview.open('#donorsPrintContainer', {
                title: 'كشف المتبرعين — نتائج التصفية (' + donorsList.length + ' متبرع)',
                orientation: 'landscape'
            });
        }
    }

    // طباعة نتائج التصفية (زرار طباعة)
    function directPrintFilteredDonors() {
        const checkedBoxes = Array.from(document.querySelectorAll('.donor-check:checked'));
        if (checkedBoxes.length > 0) {
            printSelectedDonors();
            return;
        }

        const allBoxes = Array.from(document.querySelectorAll('.donor-check'));
        if (allBoxes.length === 0) {
            alert('لا توجد بيانات متبرعين لطباعتها بالتصفية الحالية');
            return;
        }

        const donorsList = allBoxes.map(getDonorObjectFromCheckbox);
        const container = document.getElementById('donorsPrintContainer');
        container.innerHTML = buildDonorsPrintHTML(donorsList, 'كشف المتبرعين — نتائج التصفية');
        container.style.display = 'none';
        container.classList.add('d-none');

        if (window.EnsanPrintPreview) {
            window.EnsanPrintPreview.open('#donorsPrintContainer', {
                title: 'كشف المتبرعين — نتائج التصفية (' + donorsList.length + ' متبرع)',
                orientation: 'landscape'
            });
            setTimeout(() => {
                window.EnsanPrintPreview.executePrint();
            }, 300);
        } else {
            const url = "{{ route('donors.print-report') }}" + window.location.search;
            window.open(url, '_blank');
        }
    }

    function printFilteredDonors() {
        previewFilteredDonors();
    }

    function submitDonorBulkDelete() {
        const ids = [];
        document.querySelectorAll('.donor-check:checked').forEach(cb => ids.push(cb.value));
        if (ids.length === 0) { alert('يرجى اختيار متبرع واحد على الأقل'); return; }
        if (!confirm('هل أنت متأكد من حذف ' + ids.length + ' متبرع؟')) return;
        const form = document.getElementById('donorBulkDeleteForm');
        const container = document.getElementById('donorBulkDeleteIds');
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
