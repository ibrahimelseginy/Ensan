@extends('layouts.app')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    .receipt-purpose-box { border:1px solid rgba(16,185,129,.3); border-radius:16px; background:rgba(16,185,129,.055); padding:1.25rem; }
    .purpose-title { color:#059669; font-weight:800; }
    .theme-dark .receipt-purpose-box { background:rgba(16,185,129,.08); }
    .month-pill-checkbox {
      border: 1px solid rgba(16, 185, 129, 0.35);
      border-radius: 9999px;
      padding: 0.35rem 0.85rem;
      cursor: pointer;
      transition: all 0.15s ease-in-out;
      background: rgba(255, 255, 255, 0.7);
      display: inline-flex;
      align-items: center;
      gap: 0.4rem;
      font-size: 0.875rem;
      user-select: none;
    }
    .theme-dark .month-pill-checkbox {
      background: rgba(30, 41, 59, 0.7);
      border-color: rgba(16, 185, 129, 0.25);
    }
    .month-pill-checkbox:hover {
      background: rgba(16, 185, 129, 0.12);
      border-color: #10b981;
    }
    .month-pill-checkbox.active {
      background: #10b981 !important;
      color: #ffffff !important;
      border-color: #059669 !important;
      box-shadow: 0 2px 6px rgba(16, 185, 129, 0.35);
    }
    .month-pill-checkbox.active .badge-current {
      background: #ffffff !important;
      color: #059669 !important;
    }
    .month-pill-checkbox input[type="checkbox"] {
      display: none;
    }
    .month-pill-checkbox * {
      pointer-events: none;
    }

    .year-months-section {
      background: #f8fafc;
      border: 1px solid #e2e8f0;
      transition: all 0.2s ease-in-out;
    }
    .theme-dark .year-months-section {
      background: #1e293b;
      border-color: #334155;
    }
    .year-filter-btn {
      font-size: 0.8rem !important;
      font-weight: 600 !important;
      border-radius: 9999px !important;
      transition: all 0.15s ease-in-out;
    }
    .year-filter-btn.active {
      background-color: #0d6efd !important;
      border-color: #0d6efd !important;
      color: #ffffff !important;
    }

    .allocated-case-row {
      transition: all 0.2s ease;
      background: #ffffff;
      border: 1px solid #e2e8f0;
    }
    .allocated-case-row:hover {
      border-color: #10b981;
      box-shadow: 0 4px 12px rgba(16, 185, 129, 0.08);
    }
    .theme-dark .allocated-case-row {
      background: #1e293b;
      border-color: #334155;
    }
    .theme-dark .allocated-case-row:hover {
      border-color: #10b981;
    }

    /* Donor type toggle buttons active state: pure white text and icon */
    .btn-check:checked + label[for="donorTypeNew"],
    .btn-check:checked + .btn-outline-success,
    #donorTypeNew:checked + label {
      background-color: #10b981 !important;
      border-color: #059669 !important;
      color: #ffffff !important;
    }
    .btn-check:checked + label[for="donorTypeNew"] *,
    .btn-check:checked + .btn-outline-success *,
    #donorTypeNew:checked + label * {
      color: #ffffff !important;
    }

    .btn-check:checked + label[for="donorTypeRegistered"],
    .btn-check:checked + .btn-outline-primary,
    #donorTypeRegistered:checked + label {
      background-color: #0d6efd !important;
      border-color: #0b5ed7 !important;
      color: #ffffff !important;
    }
    .btn-check:checked + label[for="donorTypeRegistered"] *,
    .btn-check:checked + .btn-outline-primary *,
    #donorTypeRegistered:checked + label * {
      color: #ffffff !important;
    }

    /* Select2 Clear Button - Centered circular badge */
    .select2-container--bootstrap-5 .select2-selection__clear,
    html[dir="rtl"] .select2-container--bootstrap-5 .select2-selection--single .select2-selection__clear {
      position: absolute !important;
      top: 50% !important;
      transform: translateY(-50%) !important;
      right: auto !important;
      left: 0.75rem !important;
      width: 20px !important;
      height: 20px !important;
      min-width: 20px !important;
      min-height: 20px !important;
      margin: 0 !important;
      padding: 0 !important;
      background: #f1f5f9 !important;
      background-image: none !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 50% !important;
      color: #475569 !important;
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      text-align: center !important;
      vertical-align: middle !important;
      font-family: Arial, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif !important;
      font-size: 13px !important;
      font-weight: 700 !important;
      line-height: 1 !important;
      direction: ltr !important;
      unicode-bidi: normal !important;
      cursor: pointer !important;
      z-index: 10 !important;
      transition: all 0.15s ease-in-out !important;
      box-sizing: border-box !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05) !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear:hover {
      background: #fee2e2 !important;
      border-color: #ef4444 !important;
      color: #ef4444 !important;
      transform: translateY(-50%) scale(1.12) !important;
    }
    .select2-container--bootstrap-5 .select2-selection__clear span {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      line-height: 1 !important;
      margin: 0 !important;
      padding: 0 !important;
    }

    /* Split Items Table Styling to prevent text clipping in RTL */
    #splitItemsTable {
      min-width: 1080px;
    }
    #splitItemsTable th {
      padding: 0.65rem 0.5rem;
      font-size: 0.85rem;
      font-weight: 700;
      white-space: nowrap;
    }
    #splitItemsTable td {
      padding: 0.45rem 0.4rem;
      vertical-align: middle;
    }
    .split-type-select,
    .split-purpose-select {
      font-size: 0.86rem !important;
      font-weight: 600 !important;
      padding-right: 0.6rem !important;
      padding-left: 2rem !important;
      min-height: 38px;
      line-height: 1.4 !important;
      box-sizing: border-box !important;
    }
    .split-target-container {
      min-width: 270px;
    }
    .split-target-container .select2-container {
      width: 100% !important;
    }

    /* Split amount input: unified single-row control, cannot wrap, no detached borders */
    .split-amount-group {
      display: inline-flex !important;
      flex-direction: row !important;
      flex-wrap: nowrap !important;
      align-items: stretch !important;
      width: 100% !important;
      min-width: 130px !important;
      border: 1px solid #cbd5e1 !important;
      border-radius: 8px !important;
      background: #ffffff !important;
      overflow: hidden !important;
      height: 38px !important;
      box-shadow: 0 1px 2px rgba(0, 0, 0, 0.04) !important;
      transition: all 0.15s ease-in-out !important;
      box-sizing: border-box !important;
      vertical-align: middle !important;
    }
    .theme-dark .split-amount-group {
      background: #0f172a !important;
      border-color: #334155 !important;
    }
    .split-amount-group:focus-within {
      border-color: #10b981 !important;
      box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2) !important;
    }
    .split-amount-group .split-amount-input,
    .split-amount-group input {
      flex: 1 1 auto !important;
      min-width: 0 !important;
      width: 1% !important;
      border: none !important;
      border-radius: 0 !important;
      box-shadow: none !important;
      outline: none !important;
      padding: 0 0.5rem !important;
      font-size: 0.95rem !important;
      font-weight: 700 !important;
      color: #059669 !important;
      background: transparent !important;
      direction: ltr !important;
      text-align: center !important;
      height: 100% !important;
      line-height: 38px !important;
      -webkit-appearance: none !important;
      -moz-appearance: textfield !important;
      box-sizing: border-box !important;
    }
    .split-amount-group .split-amount-input::-webkit-outer-spin-button,
    .split-amount-group .split-amount-input::-webkit-inner-spin-button,
    .split-amount-group input::-webkit-outer-spin-button,
    .split-amount-group input::-webkit-inner-spin-button {
      -webkit-appearance: none !important;
      margin: 0 !important;
      display: none !important;
    }
    .split-amount-group .split-amount-addon,
    .split-amount-group .input-group-text {
      display: inline-flex !important;
      align-items: center !important;
      justify-content: center !important;
      padding: 0 0.65rem !important;
      background: #f8fafc !important;
      border: none !important;
      border-inline-start: 1px solid #e2e8f0 !important;
      border-radius: 0 !important;
      color: #64748b !important;
      font-size: 0.82rem !important;
      font-weight: 700 !important;
      white-space: nowrap !important;
      flex-shrink: 0 !important;
      user-select: none !important;
      height: 100% !important;
      box-sizing: border-box !important;
    }
    .theme-dark .split-amount-group .split-amount-addon,
    .theme-dark .split-amount-group .input-group-text {
      background: #1e293b !important;
      border-color: #334155 !important;
      color: #94a3b8 !important;
    }

    /* Fix any Bootstrap input-group inside split table or allocated rows */
    #splitItemsTable td .input-group,
    .allocated-case-row .input-group {
      flex-wrap: nowrap !important;
    }
    #splitItemsTable td .input-group > :not(:last-child),
    .allocated-case-row .input-group > :not(:last-child) {
      border-top-left-radius: 0 !important;
      border-bottom-left-radius: 0 !important;
    }
    #splitItemsTable td .input-group > :not(:first-child),
    .allocated-case-row .input-group > :not(:first-child) {
      border-top-right-radius: 0 !important;
      border-bottom-right-radius: 0 !important;
    }

    /* Force pure white text and icon on Sync buttons */
    #btnSyncSplitToTotal,
    #btnSyncSplitToTotal *,
    #btnSyncAllocatedToTotal,
    #btnSyncAllocatedToTotal *,
    .btn-outline-success:hover,
    .btn-outline-success:hover *,
    .btn-outline-success:focus,
    .btn-outline-success:focus *,
    .btn-outline-success:active,
    .btn-outline-success.active,
    .btn-outline-success:active *,
    .btn-outline-success.active * {
      color: #ffffff !important;
      -webkit-text-fill-color: #ffffff !important;
    }
    #btnSyncSplitToTotal,
    #btnSyncAllocatedToTotal {
      background-color: #10b981 !important;
      border-color: #059669 !important;
      color: #ffffff !important;
    }
    #btnSyncSplitToTotal:hover,
    #btnSyncAllocatedToTotal:hover {
      background-color: #059669 !important;
      border-color: #047857 !important;
      color: #ffffff !important;
    }
  </style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h4 class="mb-1"><i class="bi bi-receipt text-primary"></i> إضافة تبرع وإصدار إيصال</h4>
    <div class="small text-muted">البيانات الأساسية وتفاصيل المتبرع والكفالة، مع ربط الحالة والشهور المسددة.</div>
  </div>
  <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i> رجوع</a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <div class="fw-bold mb-1">راجع البيانات التالية:</div>
    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('donations.store') }}" id="donationForm" enctype="multipart/form-data">
      @csrf

      {{-- 1. بيانات المتبرع --}}
      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-person-circle"></i><span>بيانات المتبرع</span></div>
        <div class="btn-group w-100 mb-3" role="group">
          <input type="radio" class="btn-check" name="donor_type_toggle" id="donorTypeRegistered" value="registered" @checked(!old('new_donor_name'))>
          <label class="btn btn-outline-primary" for="donorTypeRegistered"><i class="bi bi-search me-1"></i> متبرع مسجل مسبقاً</label>
          <input type="radio" class="btn-check" name="donor_type_toggle" id="donorTypeNew" value="new" @checked(old('new_donor_name'))>
          <label class="btn btn-outline-success" for="donorTypeNew"><i class="bi bi-person-plus me-1"></i> متبرع جديد</label>
        </div>

        {{-- المتبرع المسجل --}}
        <div id="registeredDonorSection">
          <div class="d-flex justify-content-between align-items-center mb-1">
            <label class="form-label form-label-required mb-0">ابحث عن المتبرع</label>
            <span class="text-muted small"><i class="bi bi-shield-lock me-1"></i> اكتب حرفين على الأقل للبحث (اسم / كود / هاتف)</span>
          </div>
          <select id="donorSelect" name="donor_id" class="form-select @error('donor_id') is-invalid @enderror">
            <option value=""></option>
            @if($selectedDonor)
              <option value="{{ $selectedDonor->id }}" selected>
                {{ $selectedDonor->name }} — {{ $selectedDonor->display_code }}{{ $selectedDonor->phone ? ' — '.$selectedDonor->phone : '' }}
              </option>
            @endif
          </select>
          @error('donor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror

          {{-- صندوق التنبيه والاقتراح والملء السريع للكفالات والتبرعات الدورية --}}
          <div id="donorSponsorshipSuggestionBox" class="mt-3 p-3 rounded-4 border shadow-sm" style="display:none; background: #ffffff; border-color: #10b981 !important;">
            {{-- 1. الهوية الأساسية للمتبرع --}}
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 pb-2 mb-2 border-bottom">
              <div class="d-flex align-items-center gap-2">
                <span class="avatar avatar-sm bg-success text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:34px;height:34px;font-size:0.95rem;">
                  <i class="bi bi-person-heart"></i>
                </span>
                <div>
                  <span class="fw-bold text-dark fs-6" id="donorInfoName"></span>
                  <span class="badge bg-secondary-subtle text-body ms-1 font-monospace" id="donorInfoCode"></span>
                  <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" id="donorInfoType"></span>
                </div>
              </div>
              <div id="donorInfoPhoneWrapper" class="small text-muted font-monospace">
                <i class="bi bi-telephone me-1"></i><span id="donorInfoPhone"></span>
              </div>
            </div>

            {{-- 2. تنبيه موعد الكفالة والاستحقاق الدوري --}}
            <div id="sponsorshipAlertBox" class="p-2.5 rounded-3 mb-2.5 border" style="background: rgba(245, 158, 11, 0.08); border-color: rgba(245, 158, 11, 0.3) !important;">
              <div class="d-flex align-items-start gap-2">
                <i class="bi bi-alarm-fill text-warning fs-5 flex-shrink-0 mt-0.5"></i>
                <div class="flex-grow-1 small">
                  <div class="fw-bold text-dark mb-1 d-flex align-items-center justify-content-between flex-wrap gap-1">
                    <span><i class="bi bi-calendar-event me-1 text-warning"></i> تنبيه موعد الكفالة والاستحقاق الدوري</span>
                    <span id="sponsorshipDueBadge" class="badge bg-warning text-dark font-monospace"></span>
                  </div>
                  <div class="d-flex flex-wrap gap-3 text-secondary mt-1" id="sponsorshipDueDetails">
                    {{-- تفاصيل الاستحقاق: موعد الشهر، آخر شهر مسدد، الشهر القادم --}}
                  </div>
                </div>
              </div>
            </div>

            {{-- 3. صندوق المقترح الذكي والملء السريع بنقرة واحدة --}}
            <div id="sponsorshipSuggestionSection" class="p-3 rounded-3 border" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-color: #34d399 !important;">
              <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <div class="fw-bold text-success d-flex align-items-center gap-1.5 fs-6">
                  <i class="bi bi-magic fs-5"></i>
                  <span>مقترح الكفالة والملء السريع لهذا المتبرع</span>
                </div>
                <span class="badge bg-success text-white px-2.5 py-1 font-monospace fs-7" id="suggestionAmountBadge"></span>
              </div>

              {{-- بنود المقترح --}}
              <div class="row g-2 mb-3 small" id="suggestionChipsContainer">
                {{-- Dynamic chips --}}
              </div>

              {{-- زر الملء السريع الشامل بنقرة واحدة --}}
              <button type="button" class="btn btn-success w-100 py-2 fw-bold shadow-sm d-flex align-items-center justify-content-center gap-2" id="btnApplyAllSponsorship">
                <i class="bi bi-lightning-charge-fill fs-5"></i>
                <span>تطبيق المقترح والملء السريع بنقرة واحدة (المبلغ + الحالة + البند + الشهر)</span>
              </button>

              {{-- أزرار تطبيق عناصر مفردة --}}
              <div class="d-flex flex-wrap gap-1.5 align-items-center mt-2 pt-2 border-top border-success border-opacity-25" style="font-size: 0.82rem;">
                <span class="text-muted me-1">أو تطبيق عنصر محدد:</span>
                <button type="button" class="btn btn-sm btn-outline-success py-0 px-2.5 rounded-pill" id="btnQuickSetAmount">
                  <i class="bi bi-cash me-1"></i><span id="quickAmountLabel">المبلغ</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success py-0 px-2.5 rounded-pill" id="btnQuickInsertCases" style="display:none;">
                  <i class="bi bi-person-heart me-1"></i><span id="quickCasesLabel">إدراج الحالة</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success py-0 px-2.5 rounded-pill" id="btnQuickSetPurpose" style="display:none;">
                  <i class="bi bi-tag me-1"></i><span id="quickPurposeLabel">بند الإيصال</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success py-0 px-2.5 rounded-pill" id="btnQuickSetMonth" style="display:none;">
                  <i class="bi bi-calendar-check me-1"></i><span id="quickMonthLabel">الشهر المستحق</span>
                </button>
              </div>

              <div id="quickFillSuccessAlert" class="alert alert-success py-1.5 px-3 small mt-2 mb-0 border-0 bg-success text-white rounded-3 text-center" style="display:none;">
                <i class="bi bi-check-circle-fill me-1"></i> تم تطبيق بيانات الكفالة والملء السريع بنجاح!
              </div>
            </div>
          </div>
        </div>

        {{-- متبرع جديد --}}
        <div id="newDonorSection" class="d-none">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">كود المتبرع <span class="badge bg-primary-subtle text-primary">تلقائي</span></label>
              <input name="new_donor_code" id="newDonorCode" value="{{ old('new_donor_code') }}" class="form-control font-monospace" dir="ltr" placeholder="تلقائي أو D-10025">
            </div>
            <div class="col-md-3">
              <label class="form-label form-label-required">اسم المتبرع</label>
              <input name="new_donor_name" id="newDonorName" value="{{ old('new_donor_name') }}" class="form-control" placeholder="الاسم ثلاثي أو ثنائي">
            </div>
            <div class="col-md-4">
              <label class="form-label form-label-required d-flex justify-content-between align-items-center">
                <span>رقم الهاتف (الدولة والرقم)</span>
                <span id="newDonorPhoneBadge" class="badge bg-secondary-subtle text-secondary font-monospace" style="font-size: 0.72rem;">11 رقم</span>
              </label>
              <div class="input-group" dir="ltr">
                <select name="new_donor_phone_country" id="newDonorPhoneCountry" class="form-select bg-light fw-bold" style="max-width: 145px; font-size: 0.85rem;">
                  <option value="EG" @selected(old('new_donor_phone_country', 'EG') === 'EG')>🇪🇬 +20 مصر</option>
                  <option value="SA" @selected(old('new_donor_phone_country') === 'SA')>🇸🇦 +966 السعودية</option>
                  <option value="AE" @selected(old('new_donor_phone_country') === 'AE')>🇦🇪 +971 الإمارات</option>
                  <option value="KW" @selected(old('new_donor_phone_country') === 'KW')>🇰🇼 +965 الكويت</option>
                  <option value="QA" @selected(old('new_donor_phone_country') === 'QA')>🇶🇦 +974 قطر</option>
                  <option value="BH" @selected(old('new_donor_phone_country') === 'BH')>🇧🇭 +973 البحرين</option>
                  <option value="OM" @selected(old('new_donor_phone_country') === 'OM')>🇴🇲 +968 عُمان</option>
                  <option value="JO" @selected(old('new_donor_phone_country') === 'JO')>🇯🇴 +962 الأردن</option>
                  <option value="PS" @selected(old('new_donor_phone_country') === 'PS')>🇵🇸 +970 فلسطين</option>
                  <option value="LB" @selected(old('new_donor_phone_country') === 'LB')>🇱🇧 +961 لبنان</option>
                  <option value="SY" @selected(old('new_donor_phone_country') === 'SY')>🇸🇾 +963 سوريا</option>
                  <option value="IQ" @selected(old('new_donor_phone_country') === 'IQ')>🇮🇶 +964 العراق</option>
                  <option value="YE" @selected(old('new_donor_phone_country') === 'YE')>🇾🇪 +967 اليمن</option>
                  <option value="LY" @selected(old('new_donor_phone_country') === 'LY')>🇱🇾 +218 ليبيا</option>
                  <option value="SD" @selected(old('new_donor_phone_country') === 'SD')>🇸🇩 +249 السودان</option>
                  <option value="TN" @selected(old('new_donor_phone_country') === 'TN')>🇹🇳 +216 تونس</option>
                  <option value="DZ" @selected(old('new_donor_phone_country') === 'DZ')>🇩🇿 +213 الجزائر</option>
                  <option value="MA" @selected(old('new_donor_phone_country') === 'MA')>🇲🇦 +212 المغرب</option>
                  <option value="MR" @selected(old('new_donor_phone_country') === 'MR')>🇲🇷 +222 موريتانيا</option>
                  <option value="SO" @selected(old('new_donor_phone_country') === 'SO')>🇸🇴 +252 الصومال</option>
                  <option value="DJ" @selected(old('new_donor_phone_country') === 'DJ')>🇩🇯 +253 جيبوتي</option>
                  <option value="KM" @selected(old('new_donor_phone_country') === 'KM')>🇰🇲 +269 جزر القمر</option>
                  <option value="TR" @selected(old('new_donor_phone_country') === 'TR')>🇹🇷 +90 تركيا</option>
                  <option value="US" @selected(old('new_donor_phone_country') === 'US')>🇺🇸 +1 أمريكا/كندا</option>
                  <option value="GB" @selected(old('new_donor_phone_country') === 'GB')>🇬🇧 +44 بريطانيا</option>
                  <option value="DE" @selected(old('new_donor_phone_country') === 'DE')>🇩🇪 +49 ألمانيا</option>
                  <option value="FR" @selected(old('new_donor_phone_country') === 'FR')>🇫🇷 +33 فرنسا</option>
                  <option value="IT" @selected(old('new_donor_phone_country') === 'IT')>🇮🇹 +39 إيطاليا</option>
                  <option value="ES" @selected(old('new_donor_phone_country') === 'ES')>🇪🇸 +34 إسبانيا</option>
                  <option value="NL" @selected(old('new_donor_phone_country') === 'NL')>🇳🇱 +31 هولندا</option>
                  <option value="SE" @selected(old('new_donor_phone_country') === 'SE')>🇸🇪 +46 السويد</option>
                  <option value="CH" @selected(old('new_donor_phone_country') === 'CH')>🇨🇭 +41 سويسرا</option>
                  <option value="AT" @selected(old('new_donor_phone_country') === 'AT')>🇦🇹 +43 النمسا</option>
                  <option value="BE" @selected(old('new_donor_phone_country') === 'BE')>🇧🇪 +32 بلجيكا</option>
                  <option value="AU" @selected(old('new_donor_phone_country') === 'AU')>🇦🇺 +61 أستراليا</option>
                  <option value="NZ" @selected(old('new_donor_phone_country') === 'NZ')>🇳🇿 +64 نيوزيلندا</option>
                  <option value="MY" @selected(old('new_donor_phone_country') === 'MY')>🇲🇾 +60 ماليزيا</option>
                  <option value="ID" @selected(old('new_donor_phone_country') === 'ID')>🇮🇩 +62 إندونيسيا</option>
                  <option value="IN" @selected(old('new_donor_phone_country') === 'IN')>🇮🇳 +91 الهند</option>
                  <option value="PK" @selected(old('new_donor_phone_country') === 'PK')>🇵🇰 +92 باكستان</option>
                  <option value="CN" @selected(old('new_donor_phone_country') === 'CN')>🇨🇳 +86 الصين</option>
                  <option value="RU" @selected(old('new_donor_phone_country') === 'RU')>🇷🇺 +7 روسيا</option>
                  <option value="BR" @selected(old('new_donor_phone_country') === 'BR')>🇧🇷 +55 البرازيل</option>
                  <option value="ZA" @selected(old('new_donor_phone_country') === 'ZA')>🇿🇦 +27 جنوب أفريقيا</option>
                </select>
                <input name="new_donor_phone" id="newDonorPhone" type="tel" inputmode="numeric" dir="ltr" value="{{ old('new_donor_phone') }}" class="form-control font-monospace @error('new_donor_phone') is-invalid @enderror" placeholder="01012345678">
              </div>
              <div id="newDonorPhoneHint" class="form-text small text-muted mt-1">مصر (+20): أدخل 11 رقم بالصفر (01xxxxxxxxx) أو 10 أرقام بدون الصفر.</div>
              @error('new_donor_phone')<div class="text-danger small mt-1">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
              <label class="form-label">العنوان / منين</label>
              <input name="new_donor_address" value="{{ old('new_donor_address') }}" class="form-control" placeholder="المركز / المحافظة">
            </div>

            {{-- تصنيف المتبرع الجديد --}}
            <div class="col-md-4">
              <label class="form-label fw-bold">نوع المتبرع</label>
              <select name="new_donor_classification" id="newDonorClassification" class="form-select">
                <option value="one_time" @selected(old('new_donor_classification', 'one_time') === 'one_time')>متبرع عام / غير منتظم (مرة واحدة أو عند الحضور)</option>
                <option value="recurring" @selected(old('new_donor_classification') === 'recurring')>متبرع شهري / كفالة منتظمة</option>
              </select>
            </div>
            <div class="col-md-4 new-recurring-field" style="{{ old('new_donor_classification') === 'recurring' ? '' : 'display:none' }}">
              <label class="form-label">يوم التبرع في الشهر (الاستحقاق)</label>
              <input type="number" name="new_donor_monthly_day" id="newDonorMonthlyDay" min="1" max="31" value="{{ old('new_donor_monthly_day', 1) }}" class="form-control" placeholder="مثال: 1">
              <div class="form-text small text-muted">سيتم إرسال إشعار للاستقبال قبل هذا الموعد بـ 24 ساعة.</div>
            </div>
            <div class="col-md-4 new-recurring-field" style="{{ old('new_donor_classification') === 'recurring' ? '' : 'display:none' }}">
              <label class="form-label">مبلغ الالتزام الشهري (ج.م)</label>
              <input type="number" step="0.01" min="0" name="new_donor_sponsorship_amount" id="newDonorSponsorshipAmount" value="{{ old('new_donor_sponsorship_amount') }}" class="form-control" placeholder="مثال: 500">
            </div>
          </div>
        </div>
      </div>

      {{-- 2. بيانات الإيصال --}}
      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-cash-coin"></i><span>بيانات الإيصال</span></div>
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label form-label-required">نوع التبرع</label>
            <select name="type" id="donationType" class="form-select" required>
              <option value="cash" @selected(old('type','cash') === 'cash')>نقدي</option>
              <option value="in_kind" @selected(old('type') === 'in_kind')>عيني</option>
            </select>
          </div>
          <div class="col-md-3 cash-only">
            <label class="form-label form-label-required">المبلغ</label>
            <div class="input-group">
              <input name="amount" id="donationAmount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" class="form-control" placeholder="0.00">
              <select name="currency" class="form-select" style="max-width:95px">
                <option value="EGP">EGP</option>
                <option value="USD">USD</option>
                <option value="EUR">EUR</option>
                <option value="SAR">SAR</option>
              </select>
            </div>
          </div>
          <div class="col-md-3 cash-only">
            <label class="form-label form-label-required">طريقة الدفع</label>
            <select name="cash_channel" id="cashChannelSelect" class="form-select">
              <option value="cash" @selected(old('cash_channel')==='cash')>نقدي</option>
              <option value="instapay" @selected(old('cash_channel')==='instapay')>انستا باي (InstaPay)</option>
              <option value="vodafone_cash" @selected(old('cash_channel')==='vodafone_cash')>فودافون كاش</option>
              <option value="bank_transfer" @selected(old('cash_channel')==='bank_transfer')>تحويل بنكي</option>
              <option value="delegate" @selected(old('cash_channel')==='delegate')>مندوب</option>
            </select>
          </div>
          <div class="col-md-3 cash-only">
            <label class="form-label form-label-required" id="receiptOrOperationLabel">رقم الإيصال</label>
            <input name="receipt_number" id="receiptNumberInput" value="{{ old('receipt_number', old('operation_number')) }}" class="form-control" required placeholder="مثال: 06501">
            <input type="hidden" name="operation_number" id="operationNumberInput" value="{{ old('operation_number', old('receipt_number')) }}">
          </div>
          <div class="col-md-3 cash-only digital-pay-fields" style="display:none">
            <label class="form-label"><i class="bi bi-paperclip me-1"></i> إرفاق صورة التحويل</label>
            <input type="file" name="transfer_receipt" class="form-control" accept="image/*,.pdf">
          </div>
          <div class="col-md-6 cash-only">
            <label class="form-label form-label-required"><i class="bi bi-safe text-primary me-1"></i> الخزينة</label>
            <select name="treasury_id" class="form-select">
              <option value="">— اختر الخزينة —</option>
              @foreach($treasuries as $treasury)
                <option value="{{ $treasury->id }}" @selected((string)old('treasury_id')===(string)$treasury->id)>{{ $treasury->name }} — {{ number_format((float)$treasury->current_balance,2) }} {{ $treasury->currency }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">تاريخ الاستلام</label>
            <input name="received_at" id="receivedAtInput" type="date" value="{{ old('received_at', date('Y-m-d')) }}" class="form-control">
          </div>

          {{-- حقول التبرع العيني --}}
          <div class="col-md-4 in-kind-only" style="display:none"><label class="form-label form-label-required">القيمة التقديرية</label><input name="estimated_value" type="number" step="0.01" min="0.01" value="{{ old('estimated_value') }}" class="form-control"></div>
          <div class="col-md-4 in-kind-only" style="display:none"><label class="form-label">الكمية</label><input name="quantity" type="number" step="0.001" min="0.001" value="{{ old('quantity') }}" class="form-control"></div>
          <div class="col-md-4 in-kind-only" style="display:none"><label class="form-label">الصنف</label><select name="item_id" class="form-select"><option value="">— اختر الصنف —</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected((string)old('item_id')===(string)$item->id)>{{ $item->name }}</option>@endforeach</select></div>
          <div class="col-md-6 in-kind-only" style="display:none"><label class="form-label">المخزن</label><select name="warehouse_id" class="form-select"><option value="">— اختر المخزن —</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected((string)old('warehouse_id')===(string)$warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select></div>
          <div class="col-md-6 in-kind-only d-flex align-items-end" style="display:none"><div class="form-check mb-2"><input type="hidden" name="add_to_inventory" value="0"><input class="form-check-input" type="checkbox" name="add_to_inventory" value="1" id="addToInventory" @checked(old('add_to_inventory'))><label class="form-check-label" for="addToInventory">إضافة التبرع العيني للمخزون</label></div></div>
        </div>
      </div>

      {{-- 3. وذلك قيمة والمشروع والحالة المكفولة والشهور المسددة --}}
      <div class="receipt-purpose-box">
        <div class="purpose-title mb-3"><i class="bi bi-check2-circle me-1"></i> وذلك قيمة والتخصيص لحالة / كفالة</div>
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">المشروع</label>
            <select name="project_id" id="donationProject" class="form-select">
              <option value="">— اختر مشروع (اختياري) —</option>
              @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected((string)old('project_id', request('project_id'))===(string)$project->id)>{{ $project->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold text-primary">
              <i class="bi bi-megaphone-fill me-1"></i> التبرع للحملات (الحملات النشطة)
            </label>
            <select name="campaign_id" id="donationCampaign" class="form-select border-primary">
              <option value="">— اختر حملة نشطة —</option>
              @foreach(($activeCampaigns ?? []) as $camp)
                <option value="{{ $camp->id }}" @selected((string)old('campaign_id', request('campaign_id'))===(string)$camp->id)>
                  {{ $camp->name }} (المستهدف: {{ number_format($camp->target_amount ?: $camp->goal_amount, 0) }} ج.م)
                </option>
              @endforeach
            </select>
            <div class="form-text small text-muted">التبرع للحملة يسمع مباشرةً في المبلغ المستهدف ونسبة الإنجاز.</div>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold text-success">
              <i class="bi bi-hospital me-1"></i> دار الضيافة
            </label>
            <select name="guest_house_id" id="donationGuestHouse" class="form-select border-success">
              <option value="">— اختر دار ضيافة (اختياري) —</option>
              @foreach(($activeGuestHouses ?? \App\Models\GuestHouse::where('status', 'active')->orderBy('name')->get()) as $gh)
                <option value="{{ $gh->id }}" @selected((string)old('guest_house_id', request('guest_house_id'))===(string)$gh->id)>
                  {{ $gh->name }}
                </option>
              @endforeach
            </select>
            <div class="form-text small text-muted">التبرع يخصص مباشرةً لحساب وإيرادات دار الضيافة.</div>
          </div>
          <div class="col-md-3">
            <label class="form-label form-label-required">بند الإيصال (وذلك قيمة)</label>
            <select name="purpose" id="donationPurpose" class="form-select" required>
              <option value="">— اختر —</option>
              <option value="kafalat_aytam" @selected(old('purpose', request('purpose'))==='kafalat_aytam')>كفالات أيتام</option>
              <option value="kafalat_awram" @selected(old('purpose', request('purpose'))==='kafalat_awram')>كفالات أورام</option>
              <option value="sadaqat" @selected(old('purpose', request('purpose'))==='sadaqat')>صدقات</option>
              <option value="zakat_maal" @selected(old('purpose', request('purpose'))==='zakat_maal')>زكاة مال</option>
              <option value="sadaqa_jariya" @selected(old('purpose', request('purpose'))==='sadaqa_jariya')>صدقات جارية</option>
            </select>
          </div>

          {{-- خيار تقسيم وتوزيع مبالغ التبرع على أكثر من جهة / بند --}}
          <div class="col-12">
            <div class="card border-success border-opacity-50 shadow-sm mb-2" style="background: rgba(240, 253, 244, 0.45);">
              <div class="card-header bg-white bg-opacity-75 py-2.5 d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                  <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" role="switch" id="toggleSplitDonation" style="cursor:pointer; width: 2.4em; height: 1.25em;">
                    <label class="form-check-label fw-bold text-success fs-6 ms-2" for="toggleSplitDonation" style="cursor:pointer;">
                      <i class="bi bi-pie-chart-fill me-1"></i> تقسيم وتوزيع التبرع على أكثر من جهة / بند (مشاريع، حملات، دور ضيافة، حالات)
                    </label>
                  </div>
                  <span class="badge bg-success-subtle text-success border border-success-subtle">مبالغ دقيقة لكل جهة</span>
                </div>
                <button type="button" class="btn btn-sm btn-success split-control-btn" id="btnAddSplitItemBtn" style="display:none;">
                  <i class="bi bi-plus-circle me-1"></i> إضافة سطر توزيع جديد
                </button>
              </div>

              <div class="card-body p-3" id="splitDonationSection" style="display:none;">
                <div class="alert alert-info py-2 px-3 small mb-3 border-0 bg-info bg-opacity-10 text-dark d-flex align-items-center gap-2">
                  <i class="bi bi-info-circle-fill text-info fs-5 flex-shrink-0"></i>
                  <div>
                    يمكنك تحديد المبلغ المخصص لكل جهة وبند إيصال بدقة. سيقوم النظام بتوزيع المبالغ وتسجيلها في كل جهة وتحديث مستهدف الحملة وإيراد دار الضيافة وحساب الحالة تلقائياً.
                  </div>
                </div>

                {{-- Table / Rows --}}
                <div class="table-responsive mb-2">
                  <table class="table table-sm table-bordered align-middle bg-white mb-0" id="splitItemsTable">
                    <thead class="table-light text-center" style="font-size: 0.85rem;">
                      <tr>
                        <th style="width: 215px; min-width: 215px;">الجهة / الوجهة</th>
                        <th style="min-width: 270px;">الجهة المحددة</th>
                        <th style="width: 180px; min-width: 180px;">بند الإيصال</th>
                        <th style="width: 170px; min-width: 170px;">المبلغ المخصص</th>
                        <th style="min-width: 160px;">ملاحظات</th>
                        <th style="width: 48px; min-width: 48px;"></th>
                      </tr>
                    </thead>
                    <tbody id="splitItemsBody">
                      {{-- Dynamic rows --}}
                    </tbody>
                  </table>
                </div>

                <div class="text-center py-3 text-muted border border-dashed rounded-3 bg-white" id="emptySplitNotice" style="display:none;">
                  <i class="bi bi-inbox fs-4 d-block mb-1 text-secondary"></i>
                  <span class="small">لم يتم إضافة أي بند توزيع بعد. انقر على زر "إضافة سطر توزيع جديد" بالأعلى للبدء.</span>
                </div>

                {{-- Summary Footer --}}
                <div class="p-2.5 rounded bg-white border d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2 shadow-xs">
                  <div class="d-flex align-items-center gap-3 flex-wrap">
                    <span class="badge bg-success font-monospace px-2.5 py-1.5 fs-6" id="splitItemsCountBadge">0 بنود</span>
                    <span class="small text-muted">إجمالي الموزع:</span>
                    <span class="fw-bolder text-success font-monospace fs-5" id="splitItemsTotal">0.00 ج.م</span>
                    <span id="splitMatchStatus"></span>
                  </div>
                  <button type="button" class="btn btn-sm btn-success text-white fw-bold shadow-xs px-3 py-1.5" id="btnSyncSplitToTotal" title="نسخ مجموع المبالغ الموزعة إلى خانة المبلغ الأساسي للتبرع" style="background-color: #10b981 !important; border-color: #059669 !important; color: #ffffff !important;">
                    <i class="bi bi-arrow-left-right me-1 text-white" style="color: #ffffff !important;"></i> <span style="color: #ffffff !important;">اعتماد المجموع كإجمالي التبرع</span>
                  </button>
                </div>
              </div>
            </div>
          </div>

          {{-- تحديد الحالات المستفيدة وتوزيع مبالغ التبرع --}}
          <div class="col-md-12">
            <div class="p-3 rounded-3 bg-white bg-opacity-75 border border-success border-opacity-35 shadow-sm">
              <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                <label class="form-label fw-bold text-success mb-0 fs-6">
                  <i class="bi bi-people-fill me-1"></i> الحالات المستفيدة وتوزيع المبالغ (يمكن إضافة أكثر من حالة)
                </label>
                <span class="text-muted small"><i class="bi bi-search me-1"></i> ابحث بكود الحالة (مثال: BEN-0012) أو اسم المستفيد لإضافتها</span>
              </div>
              
              <div class="row g-2 align-items-center mb-2">
                <div class="col-12">
                  <select id="beneficiarySelect" class="form-select">
                    <option value=""></option>
                  </select>
                </div>
              </div>

              {{-- Hidden input for backward compatibility with primary beneficiary --}}
              <input type="hidden" name="beneficiary_id" id="primaryBeneficiaryId" value="{{ $selectedBeneficiary?->id }}">

              {{-- Container for Allocated Cases --}}
              <div id="allocatedCasesList" class="mt-2">
                {{-- Dynamically populated rows --}}
              </div>

              <div id="emptyAllocatedNotice" class="text-center py-3 text-muted border border-dashed rounded-3 bg-light bg-opacity-50" style="display:none;">
                <i class="bi bi-inbox fs-4 d-block mb-1 text-secondary"></i>
                <span class="small">لم يتم اختيار أي حالة مستفيدة بعد. اختر حالة من حقل البحث أعلاه لتخصيص مبلغ لها.</span>
              </div>

              {{-- Summary Bar --}}
              <div id="allocatedCasesSummary" class="p-2.5 rounded bg-light border d-flex justify-content-between align-items-center flex-wrap gap-2 mt-2" style="display:none !important;">
                <div class="d-flex align-items-center gap-2">
                  <span class="badge bg-success-subtle text-success border border-success border-opacity-25" id="allocatedCountBadge">0 حالات</span>
                  <span class="text-muted small">إجمالي المبالغ المخصصة للحالات:</span>
                  <span class="fw-bold text-success font-monospace fs-6" id="allocatedTotalAmount">0.00 ج.م</span>
                </div>
                <button type="button" class="btn btn-sm btn-success text-white fw-bold shadow-xs px-3 py-1.5" id="btnSyncAllocatedToTotal" title="نسخ مجموع مبالغ الحالات إلى حقل قيمة التبرع أعلاه" style="background-color: #10b981 !important; border-color: #059669 !important; color: #ffffff !important;">
                  <i class="bi bi-arrow-left-right me-1 text-white" style="color: #ffffff !important;"></i> <span style="color: #ffffff !important;">اعتماد كقيمة التبرع الإجمالية</span>
                </button>
              </div>

              <div class="form-text small text-muted mt-2">
                <i class="bi bi-info-circle me-1"></i> يمكنك إضافة عدة حالات مستفيدة لنفس التبرع وتحديد المبلغ المخصص لكل حالة بدقة، وسيقوم النظام بتسجيل التبرع في ملف وسجل كل حالة تلقائياً.
              </div>
            </div>
          </div>

          {{-- الشهور المسددة (لتبرعات الكفالة / المتبرع الشهري) --}}
          <div class="col-12" id="paidMonthsSection">
            <div class="p-3 rounded-3 border border-primary border-opacity-25 bg-white bg-opacity-50 shadow-sm">
              <div class="d-flex flex-wrap justify-content-between align-items-center mb-2 gap-2">
                <label class="form-label fw-bold text-primary mb-0">
                  <i class="bi bi-calendar-check me-1"></i> الشهور المسددة (قائمة تحديد الأشهر المدفوعة)
                </label>
                <div class="btn-group btn-group-sm flex-wrap" role="group">
                  <button type="button" class="btn btn-outline-primary" id="btnSelectCurrentMonth">الشهر الحالي فقط</button>
                  <button type="button" class="btn btn-outline-primary" id="btnSelectTwoMonths">شهرين</button>
                  <button type="button" class="btn btn-outline-primary" id="btnSelectThreeMonths">3 شهور</button>
                  <button type="button" class="btn btn-outline-primary" id="btnSelectSixMonths">6 شهور</button>
                  <button type="button" class="btn btn-outline-primary" id="btnSelectFullYear">سنة كاملة (12 شهر)</button>
                  <button type="button" class="btn btn-outline-secondary" id="btnClearMonths">إلغاء التحديد</button>
                </div>
              </div>
              <div class="small text-muted mb-2">
                حدد الشهور التي دفعها المتبرع (شهر الحالي، شهرين، 3 شهور، 6 شهور، سنة كاملة أو متأخرات)، وسيقوم النظام بتسجيلها في ملفه وتحديد <strong>موعد الاستحقاق القادم تلقائياً</strong> لإرسال إشعار للاستقبال قبله بـ 24 ساعة.
              </div>

              {{-- شريط تصفية السنوات السريع --}}
              <div class="d-flex align-items-center gap-1.5 flex-wrap my-2 pb-2 border-bottom">
                <span class="small text-muted me-1 fw-semibold"><i class="bi bi-funnel me-1"></i> تصفية العرض:</span>
                <button type="button" class="btn btn-sm btn-primary year-filter-btn active py-0.5 px-2.5" data-year="all">عرض كل السنوات</button>
                <button type="button" class="btn btn-sm btn-outline-secondary year-filter-btn py-0.5 px-2.5" data-year="{{ now()->format('Y') }}">{{ now()->format('Y') }} (السنة الحالية)</button>
                <button type="button" class="btn btn-sm btn-outline-secondary year-filter-btn py-0.5 px-2.5" data-year="{{ (int)now()->format('Y') + 1 }}">{{ (int)now()->format('Y') + 1 }} (السنة القادمة)</button>
                <button type="button" class="btn btn-sm btn-outline-secondary year-filter-btn py-0.5 px-2.5" data-year="{{ (int)now()->format('Y') - 1 }}">{{ (int)now()->format('Y') - 1 }} (متأخرات سابقة)</button>
              </div>

              @php
                $currentDate = now()->startOfMonth();
                $currentYear = (int)$currentDate->format('Y');
                $currentMonthKey = $currentDate->format('Y-m');
                $oldPaidMonths = (array) old('paid_months', []);

                $arMonthNames = [
                    1  => 'يناير',
                    2  => 'فبراير',
                    3  => 'مارس',
                    4  => 'أبريل',
                    5  => 'مايو',
                    6  => 'يونيو',
                    7  => 'يوليو',
                    8  => 'أغسطس',
                    9  => 'سبتمبر',
                    10 => 'أكتوبر',
                    11 => 'نوفمبر',
                    12 => 'ديسمبر',
                ];

                $yearSections = [
                    [
                        'year' => $currentYear,
                        'title' => 'شهور سنة ' . $currentYear . ' (السنة الحالية)',
                        'icon' => 'bi-calendar-check-fill text-primary',
                        'badge' => 'السنة الحالية',
                        'badge_class' => 'bg-primary-subtle text-primary border border-primary-subtle',
                    ],
                    [
                        'year' => $currentYear + 1,
                        'title' => 'شهور سنة ' . ($currentYear + 1) . ' (السنة القادمة)',
                        'icon' => 'bi-calendar-plus-fill text-success',
                        'badge' => 'السنة القادمة',
                        'badge_class' => 'bg-success-subtle text-success border border-success-subtle',
                    ],
                    [
                        'year' => $currentYear - 1,
                        'title' => 'شهور سنة ' . ($currentYear - 1) . ' (متأخرات السنة السابقة)',
                        'icon' => 'bi-clock-history text-secondary',
                        'badge' => 'متأخرات سابقة',
                        'badge_class' => 'bg-secondary-subtle text-secondary border border-secondary-subtle',
                    ],
                ];
              @endphp

              <div id="monthsCheckboxList" class="d-flex flex-column gap-2.5 pt-1">
                @foreach($yearSections as $sec)
                  <div class="year-months-section p-2.5 rounded-3 bg-light bg-opacity-60 border shadow-2xs" data-year="{{ $sec['year'] }}">
                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-2">
                      <div class="d-flex align-items-center gap-2">
                        <i class="bi {{ $sec['icon'] }} fs-6"></i>
                        <span class="fw-bold text-dark fs-6">{{ $sec['title'] }}</span>
                        <span class="badge {{ $sec['badge_class'] }} rounded-pill" style="font-size:0.75rem;">{{ $sec['badge'] }}</span>
                      </div>
                      <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-primary fw-semibold btn-select-year" data-year="{{ $sec['year'] }}">
                          <i class="bi bi-check-all me-1"></i>تحديد الـ 12 شهراً
                        </button>
                        <span class="text-muted">|</span>
                        <button type="button" class="btn btn-link btn-sm p-0 text-decoration-none text-muted btn-clear-year" data-year="{{ $sec['year'] }}">
                          <i class="bi bi-x-circle me-1"></i>إلغاء السنة
                        </button>
                      </div>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                      @for($m = 1; $m <= 12; $m++)
                        @php
                          $mPad = str_pad($m, 2, '0', STR_PAD_LEFT);
                          $key = "{$sec['year']}-{$mPad}";
                          $arName = $arMonthNames[$m];
                          $isCurrent = ($key === $currentMonthKey);
                          $isChecked = in_array($key, $oldPaidMonths, true) || (empty($oldPaidMonths) && $isCurrent && (request('donor_id') || old('purpose') === 'kafalat_aytam' || old('purpose') === 'kafalat_awram'));
                        @endphp
                        <label class="month-pill-checkbox {{ $isChecked ? 'active' : '' }}" data-month="{{ $key }}" data-year="{{ $sec['year'] }}">
                          <input type="checkbox" name="paid_months[]" value="{{ $key }}" class="paid-month-input" @checked($isChecked)>
                          <i class="bi {{ $isChecked ? 'bi-check2-circle' : 'bi-circle' }} check-icon"></i>
                          <span>{{ $arName }} {{ $sec['year'] }}</span>
                          @if($isCurrent)
                            <span class="badge bg-success-subtle text-success badge-current" style="font-size:0.7rem">الحالي</span>
                          @endif
                        </label>
                      @endfor
                    </div>
                  </div>
                @endforeach
              </div>

              <div class="mt-3 p-2.5 rounded bg-light border d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                  <i class="bi bi-clock-history text-primary fs-5"></i>
                  <div>
                    <span class="text-muted small d-block">موعد التبرع والاستحقاق القادم تلقائياً:</span>
                    <span class="fw-bold text-primary font-monospace" id="previewNextDueDate">—</span>
                  </div>
                </div>
                <div class="text-muted small" id="previewSelectedCount">تم تحديد: 0 شهر</div>
              </div>
            </div>
          </div>

          {{-- قائمة الأطفال أو المرضى المكفولين داخل الأسرة --}}
          <div class="col-12" id="sponsoredMembersWrapper" style="display:none">
            <label class="form-label fw-bold" id="sponsoredMembersLabel">اختر الملفات المكفولة</label>
            <select name="family_member_ids[]" id="sponsoredMembersSelect" class="form-select" multiple data-placeholder="اختر واحدًا أو أكثر..."></select>
            <div id="noSponsoredMembers" class="alert alert-warning py-2 mt-2 mb-0" style="display:none"><i class="bi bi-exclamation-triangle me-1"></i><span></span></div>
            <div class="form-text">يمكن اختيار فرد أو أكثر من أفراد الأسرة المرتبطين بالكفالة.</div>
          </div>

          <div class="col-12">
            <label class="form-label">ملاحظات أو تفاصيل إضافية</label>
            <textarea name="allocation_note" class="form-control" rows="2" placeholder="أي توضيح خاص بالتسديد أو الغرض...">{{ old('allocation_note') }}</textarea>
          </div>
        </div>
      </div>

      <div class="mt-4 text-end">
        <button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-check-lg me-1"></i> حفظ وإصدار الإيصال</button>
      </div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@php
  $sponsoredByDonorData = $donors->mapWithKeys(function ($donor) {
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
  $donorsLookup = $donors->mapWithKeys(function ($donor) {
      return [(string)$donor->id => $donor->getSponsorshipProfile()];
  });
  $allMembersData = $allFamilyMembers->map(function ($member) {
      return [
          'id' => $member->id,
          'name' => $member->full_name,
          'code' => $member->code,
          'relationship' => $member->relationship,
          'is_patient' => (bool)$member->is_patient,
          'family' => $member->beneficiary?->full_name,
      ];
  });
  $initialAllocations = [];
  $oldAllocations = old('beneficiary_allocations');
  if ($oldAllocations && is_array($oldAllocations)) {
      foreach ($oldAllocations as $bId => $aData) {
          $actualId = (int)($aData['beneficiary_id'] ?? $bId);
          $bModel = \App\Models\Beneficiary::find($actualId);
          if ($bModel) {
              $mId = !empty($aData['family_member_id']) ? (int)$aData['family_member_id'] : null;
              $mModel = $mId ? \App\Models\BeneficiaryFamilyMember::find($mId) : null;
              $displayName = $mModel ? $mModel->full_name . ' (أسرة: ' . $bModel->full_name . ')' : $bModel->full_name;
              $displayCode = $mModel?->code ?: ($bModel->code ?? ('BEN-' . $bModel->id));
              $displayPhone = $mModel?->phone ?: $bModel->phone;
              $initialAllocations[] = [
                  'id' => $bModel->id,
                  'code' => $displayCode,
                  'name' => $displayName,
                  'phone' => $displayPhone,
                  'amount' => $aData['amount'] ?? '',
                  'notes' => $aData['notes'] ?? '',
                  'family_member_id' => $mId,
                  'matched_member_id' => $mId,
              ];
          }
      }
  } elseif ($selectedBeneficiary) {
      $initialAllocations[] = [
          'id' => $selectedBeneficiary->id,
          'code' => $selectedBeneficiary->code ?? ('BEN-' . $selectedBeneficiary->id),
          'name' => $selectedBeneficiary->full_name,
          'phone' => $selectedBeneficiary->phone,
          'amount' => old('amount', ''),
          'notes' => old('allocation_note', ''),
          'family_member_id' => null,
          'matched_member_id' => null,
      ];
  }

  $projectsList = $projects->map(fn($p) => ['id' => $p->id, 'name' => $p->name])->values();
  $campaignsList = ($activeCampaigns ?? collect())->map(fn($c) => ['id' => $c->id, 'name' => $c->name])->values();
  $guestHousesList = ($activeGuestHouses ?? collect())->map(fn($g) => ['id' => $g->id, 'name' => $g->name])->values();
  $oldSplits = old('split_allocations', []);
@endphp
<script>
$(function () {
  const initialAllocations = @json($initialAllocations);
  const availableProjects = @json($projectsList);
  const availableCampaigns = @json($campaignsList);
  const availableGuestHouses = @json($guestHousesList);
  const initialSplits = @json($oldSplits);
  const sponsoredByDonor = @json($sponsoredByDonorData);
  const donorsLookup = @json($donorsLookup);
  const selectedDonorProfile = @json($selectedDonorProfile ?? null);
  if (selectedDonorProfile && selectedDonorProfile.donor_id) {
    donorsLookup[String(selectedDonorProfile.donor_id)] = selectedDonorProfile;
  }
  const allMembersList = @json($allMembersData);
  const oldMemberIds = @json(array_map('strval', (array)old('family_member_ids', [])));
  const currentMonthKey = '{{ now()->format("Y-m") }}';

  $('#donationProject').select2({theme:'bootstrap-5',dir:'rtl',width:'100%',allowClear:true,placeholder:'ابحث بالتحديد...'});

  // Donor Search Select2
  $('#donorSelect').select2({
    theme: 'bootstrap-5',
    dir: 'rtl',
    width: '100%',
    allowClear: true,
    minimumInputLength: 2,
    placeholder: '— اكتب حرفين على الأقل للبحث (اسم / كود / هاتف) —',
    ajax: {
      url: '{{ route("api.search.donors") }}',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return { q: params.term };
      },
      processResults: function (data) {
        if (Array.isArray(data)) {
          data.forEach(function (item) {
            if (item.sponsorship_profile) {
              donorsLookup[String(item.id)] = item.sponsorship_profile;
            }
          });
        }
        return {
          results: data
        };
      },
      cache: true
    },
    language: {
      inputTooShort: function () {
        return 'يرجى كتابة حرفين على الأقل للبحث (حفاظاً على الخصوصية)';
      },
      noResults: function () {
        return 'لا يوجد متبرع مطابق للبحث';
      },
      searching: function () {
        return 'جاري البحث...';
      }
    }
  });

  // Beneficiary Search Select2 (with case code display)
  $('#beneficiarySelect').select2({
    theme: 'bootstrap-5',
    dir: 'rtl',
    width: '100%',
    allowClear: true,
    minimumInputLength: 1,
    placeholder: '— ابحث بكود الحالة (مثال: BEN-0012) أو اسم المستفيد —',
    ajax: {
      url: '{{ route("api.search.beneficiaries") }}',
      dataType: 'json',
      delay: 250,
      data: function (params) {
        return { q: params.term };
      },
      processResults: function (data) {
        return {
          results: data.map(function(item) {
            return {
              id: item.id,
              text: item.text || (item.full_name + ' — ' + (item.code || ('ID: ' + item.id)) + (item.phone ? ' (' + item.phone + ')' : '')),
              code: item.code || ('BEN-' + item.id),
              full_name: item.full_name,
              phone: item.phone,
              notes: item.notes || '',
              matched_member_id: item.matched_member_id || null,
              matched_member_name: item.matched_member_name || null
            };
          })
        };
      },
      cache: true
    },
    language: {
      inputTooShort: function () {
        return 'اكتب كود الحالة أو حرفاً من اسمها للبحث';
      },
      noResults: function () {
        return 'لم يتم العثور على حالة مطابقة';
      },
      searching: function () {
        return 'جاري البحث في الحالات...';
      }
    }
  });

  $('#sponsoredMembersSelect').select2({theme:'bootstrap-5',dir:'rtl',width:'100%',placeholder:'اختر واحدًا أو أكثر...'});

  // Multi-Beneficiary Allocation Logic
  let allocRowCounter = 0;
  function addBeneficiaryRow(item) {
    if (!item || !item.id) return;

    const bId = item.id;
    const mId = item.matched_member_id || item.family_member_id || null;
    const rowKey = mId ? `${bId}_${mId}` : `${bId}`;

    // Check if already in list
    const existing = $(`.allocated-case-row[data-row-key="${rowKey}"]`);
    if (existing.length > 0) {
      existing.find('.case-amount-input').focus();
      existing.addClass('border-success').css('transition', 'all 0.3s');
      setTimeout(() => existing.removeClass('border-success'), 1200);
      return;
    }

    allocRowCounter++;
    const rowIdx = allocRowCounter;
    const bCode = item.code || ('BEN-' + bId);
    const bName = item.name || item.full_name || item.text || ('مستفيد #' + bId);
    const bPhone = item.phone || '';
    const bAmt = (item.amount !== undefined && item.amount !== null) ? item.amount : '';
    const bNotes = item.notes || '';

    const rowHtml = `
      <div class="allocated-case-row p-2.5 mb-2 rounded-3 border bg-white shadow-xs" data-id="${bId}" data-member-id="${mId || ''}" data-row-key="${rowKey}">
        <div class="row g-2 align-items-center">
          <div class="col-md-5 d-flex align-items-center gap-2">
            <span class="badge bg-success font-monospace px-2.5 py-1.5">${bCode}</span>
            <div class="overflow-hidden">
              <span class="fw-bold text-dark d-block text-truncate" title="${bName}">${bName}</span>
              ${bPhone ? `<span class="text-muted small font-monospace"><i class="bi bi-telephone me-1"></i>${bPhone}</span>` : ''}
            </div>
            <input type="hidden" name="beneficiary_allocations[${rowIdx}][beneficiary_id]" value="${bId}">
            ${mId ? `<input type="hidden" name="beneficiary_allocations[${rowIdx}][family_member_id]" value="${mId}">` : ''}
            ${mId ? `<input type="hidden" name="family_member_ids[]" class="case-family-member-input" value="${mId}">` : ''}
          </div>
          <div class="col-md-3">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light text-muted">المبلغ</span>
              <input type="number" step="any" min="0" name="beneficiary_allocations[${rowIdx}][amount]" class="form-control fw-bold text-success text-center case-amount-input" placeholder="0.00" value="${bAmt}">
              <span class="input-group-text bg-light text-muted">ج.م</span>
            </div>
          </div>
          <div class="col-md-3">
            <input type="text" name="beneficiary_allocations[${rowIdx}][notes]" class="form-control form-control-sm" placeholder="ملاحظة خاصة بالحالة..." value="${bNotes}">
          </div>
          <div class="col-md-1 text-end">
            <button type="button" class="btn btn-sm btn-outline-danger remove-case-btn py-1 px-2" title="إزالة هذه الحالة">
              <i class="bi bi-trash"></i>
            </button>
          </div>
        </div>
      </div>
    `;

    $('#allocatedCasesList').append(rowHtml);
    updateAllocatedCasesSummary();

    if (mId && $('#sponsoredMembersSelect').length) {
      const select = $('#sponsoredMembersSelect');
      const curVals = select.val() || [];
      const mIdStr = String(mId);
      if (!curVals.includes(mIdStr)) {
        if (!select.find(`option[value="${mIdStr}"]`).length) {
          select.append(new Option(bName, mIdStr, true, true));
        }
        curVals.push(mIdStr);
        select.val(curVals).trigger('change');
      }
    }

    const newRow = $(`.allocated-case-row[data-row-key="${rowKey}"]`);
    newRow.find('.case-amount-input').focus();
  }

  $(document).on('click', '.remove-case-btn', function() {
    const row = $(this).closest('.allocated-case-row');
    const memberId = row.find('.case-family-member-input').val();
    if (memberId && $('#sponsoredMembersSelect').length) {
      const select = $('#sponsoredMembersSelect');
      let curVals = select.val() || [];
      curVals = curVals.filter(v => String(v) !== String(memberId));
      select.val(curVals).trigger('change');
    }
    row.remove();
    updateAllocatedCasesSummary();
  });

  $(document).on('input change', '.case-amount-input', function() {
    updateAllocatedCasesSummary();
  });

  function updateAllocatedCasesSummary() {
    let total = 0;
    let count = 0;
    $('.allocated-case-row').each(function() {
      count++;
      const amt = parseFloat($(this).find('.case-amount-input').val()) || 0;
      total += amt;
    });

    const firstRow = $('.allocated-case-row').first();
    const firstBenId = firstRow.length > 0 ? firstRow.data('id') : '';
    $('#primaryBeneficiaryId').val(firstBenId);

    if (count > 0) {
      $('#allocatedCasesSummary').attr('style', 'display: flex !important;');
      $('#allocatedCountBadge').text(`${count} ${count === 1 ? 'حالة' : 'حالات'}`);
      $('#allocatedTotalAmount').text(total.toFixed(2) + ' ج.م');
      $('#emptyAllocatedNotice').hide();
    } else {
      $('#allocatedCasesSummary').attr('style', 'display: none !important;');
      $('#emptyAllocatedNotice').show();
    }
  }

  $('#btnSyncAllocatedToTotal').on('click', function() {
    let total = 0;
    $('.case-amount-input').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    if (total > 0) {
      $('#donationAmount').val(total.toFixed(2)).trigger('input');
    }
  });

  // When a beneficiary is selected, add to multi-case allocation list and reset search input
  $('#beneficiarySelect').on('select2:select', function(e) {
    const data = e.params.data;
    addBeneficiaryRow({
      id: data.id,
      code: data.member_code ? data.member_code : (data.code || ('BEN-' + data.id)),
      name: data.full_name || data.text,
      phone: data.phone || '',
      amount: '',
      notes: data.notes || '',
      matched_member_id: data.matched_member_id || null
    });

    $(this).val(null).trigger('change');
  });

  // Initialize preloaded allocations
  if (Array.isArray(initialAllocations) && initialAllocations.length > 0) {
    initialAllocations.forEach(function(item) {
      addBeneficiaryRow(item);
    });
  } else {
    updateAllocatedCasesSummary();
  }

  // ==========================================
  // Split Allocations (Multi-Destination) Logic
  // ==========================================
  let splitRowCounter = 0;

  function renderTargetOptions(type, selectedId) {
    if (type === 'campaign') {
      let opts = '<option value="">— اختر حملة نشطة —</option>';
      availableCampaigns.forEach(c => {
        opts += `<option value="${c.id}" ${c.id == selectedId ? 'selected' : ''}>${c.name}</option>`;
      });
      return `<select class="form-select form-select-sm split-target-select">${opts}</select>`;
    } else if (type === 'guest_house') {
      let opts = '<option value="">— اختر دار ضيافة —</option>';
      availableGuestHouses.forEach(g => {
        opts += `<option value="${g.id}" ${g.id == selectedId ? 'selected' : ''}>${g.name}</option>`;
      });
      return `<select class="form-select form-select-sm split-target-select">${opts}</select>`;
    } else if (type === 'project') {
      let opts = '<option value="">— اختر مشروع —</option>';
      availableProjects.forEach(p => {
        opts += `<option value="${p.id}" ${p.id == selectedId ? 'selected' : ''}>${p.name}</option>`;
      });
      return `<select class="form-select form-select-sm split-target-select">${opts}</select>`;
    } else if (type === 'beneficiary') {
      let opt = selectedId ? `<option value="${selectedId}" selected>مستفيد #${selectedId}</option>` : '';
      return `<select class="form-select form-select-sm split-beneficiary-select"><option value=""></option>${opt}</select>`;
    } else {
      return `<input type="text" class="form-control form-control-sm bg-light text-muted" value="عام (بدون تخصيص)" readonly><input type="hidden" class="split-target-hidden" value="">`;
    }
  }

  function addSplitItemRow(item = {}) {
    const idx = splitRowCounter++;
    const type = item.type || 'campaign';
    const targetId = item.target_id || '';
    const purpose = item.purpose || $('#donationPurpose').val() || 'sadaqat';
    const amount = item.amount !== undefined && item.amount !== null ? item.amount : '';
    const notes = item.notes || '';

    const rowHtml = `
      <tr class="split-item-row" data-idx="${idx}">
        <td style="width: 215px; min-width: 215px;">
          <select name="split_allocations[${idx}][type]" class="form-select form-select-sm split-type-select">
            <option value="campaign" ${type === 'campaign' ? 'selected' : ''}>📢 حملة نشطة</option>
            <option value="guest_house" ${type === 'guest_house' ? 'selected' : ''}>🏥 دار ضيافة</option>
            <option value="project" ${type === 'project' ? 'selected' : ''}>📌 مشروع</option>
            <option value="beneficiary" ${type === 'beneficiary' ? 'selected' : ''}>👤 حالة مستفيدة</option>
            <option value="general" ${type === 'general' ? 'selected' : ''}>🏷️ عام / غير مخصص</option>
          </select>
        </td>
        <td style="min-width: 270px;">
          <div class="split-target-container" data-idx="${idx}">
            ${renderTargetOptions(type, targetId)}
          </div>
        </td>
        <td style="width: 180px; min-width: 180px;">
          <select name="split_allocations[${idx}][purpose]" class="form-select form-select-sm split-purpose-select">
            <option value="kafalat_aytam" ${purpose === 'kafalat_aytam' ? 'selected' : ''}>كفالات أيتام</option>
            <option value="kafalat_awram" ${purpose === 'kafalat_awram' ? 'selected' : ''}>كفالات أورام</option>
            <option value="sadaqat" ${purpose === 'sadaqat' ? 'selected' : ''}>صدقات</option>
            <option value="zakat_maal" ${purpose === 'zakat_maal' ? 'selected' : ''}>زكاة مال</option>
            <option value="sadaqa_jariya" ${purpose === 'sadaqa_jariya' ? 'selected' : ''}>صدقات جارية</option>
          </select>
        </td>
        <td style="width: 170px; min-width: 170px;">
          <div class="split-amount-group">
            <input type="number" dir="ltr" step="any" min="0.01" name="split_allocations[${idx}][amount]" class="split-amount-input" placeholder="0.00" value="${amount}">
            <span class="split-amount-addon">ج.م</span>
          </div>
        </td>
        <td style="min-width: 160px;">
          <input type="text" name="split_allocations[${idx}][notes]" class="form-control form-control-sm" placeholder="ملاحظات اختيارية..." value="${notes}">
        </td>
        <td class="text-center" style="width: 48px; min-width: 48px;">
          <button type="button" class="btn btn-sm btn-outline-danger remove-split-row-btn py-1 px-1.5" title="حذف هذا السطر">
            <i class="bi bi-trash"></i>
          </button>
        </td>
      </tr>
    `;

    $('#splitItemsBody').append(rowHtml);
    const newRow = $(`tr.split-item-row[data-idx="${idx}"]`);
    
    // Assign name to target select
    syncTargetInputName(newRow);

    // If beneficiary, init select2
    if (type === 'beneficiary') {
      initSplitBeneficiarySelect2(newRow.find('.split-beneficiary-select'));
    }

    updateSplitAllocationsSummary();
    newRow.find('.split-amount-input').focus();
  }

  function syncTargetInputName(row) {
    const idx = row.data('idx');
    const container = row.find('.split-target-container');
    const select = container.find('select');
    const hidden = container.find('.split-target-hidden');
    if (select.length) {
      select.attr('name', `split_allocations[${idx}][target_id]`);
    } else if (hidden.length) {
      hidden.attr('name', `split_allocations[${idx}][target_id]`);
    }
  }

  function initSplitBeneficiarySelect2(el) {
    el.select2({
      theme: 'bootstrap-5',
      dir: 'rtl',
      width: '100%',
      allowClear: true,
      placeholder: '— ابحث بكود الحالة أو اسمها —',
      ajax: {
        url: '{{ route("api.search.beneficiaries") }}',
        dataType: 'json',
        delay: 250,
        data: function (params) { return { q: params.term }; },
        processResults: function (data) {
          return {
            results: data.map(function(item) {
              return {
                id: item.id,
                text: item.text || (item.full_name + ' — ' + (item.code || ('ID: ' + item.id)))
              };
            })
          };
        },
        cache: true
      }
    });
  }

  // Type change handler
  $(document).on('change', '.split-type-select', function() {
    const row = $(this).closest('tr.split-item-row');
    const type = $(this).val();
    const container = row.find('.split-target-container');
    
    // Auto adjust purpose default
    const purposeSelect = row.find('.split-purpose-select');
    if (type === 'beneficiary' && purposeSelect.val() === 'sadaqat') {
      purposeSelect.val('kafalat_aytam');
    }

    container.html(renderTargetOptions(type, ''));
    syncTargetInputName(row);

    if (type === 'beneficiary') {
      initSplitBeneficiarySelect2(container.find('.split-beneficiary-select'));
    }
  });

  // Remove row handler
  $(document).on('click', '.remove-split-row-btn', function() {
    $(this).closest('tr.split-item-row').remove();
    updateSplitAllocationsSummary();
  });

  // Amount input handler
  $(document).on('input change', '.split-amount-input', function() {
    updateSplitAllocationsSummary();
  });

  $('#donationAmount').on('input change', function() {
    updateSplitAllocationsSummary();
  });

  function updateSplitAllocationsSummary() {
    let total = 0;
    let count = 0;
    $('.split-amount-input').each(function() {
      const amt = parseFloat($(this).val()) || 0;
      if (amt > 0) count++;
      total += amt;
    });

    const rowsCount = $('.split-item-row').length;
    $('#splitItemsCountBadge').text(`${rowsCount} ${rowsCount === 1 ? 'بند' : 'بنود'}`);
    $('#splitItemsTotal').text(total.toFixed(2) + ' ج.م');

    if (rowsCount === 0) {
      $('#emptySplitNotice').show();
      $('#splitItemsTable').hide();
    } else {
      $('#emptySplitNotice').hide();
      $('#splitItemsTable').show();
    }

    const donationAmt = parseFloat($('#donationAmount').val()) || 0;
    const diff = donationAmt - total;
    let statusHtml = '';
    if (rowsCount > 0 && donationAmt > 0) {
      if (Math.abs(diff) < 0.01) {
        statusHtml = '<span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-check-circle-fill me-1"></i> متطابق تماماً مع إجمالي التبرع</span>';
      } else if (diff > 0) {
        statusHtml = `<span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-1"><i class="bi bi-exclamation-triangle-fill text-warning me-1"></i> متبقي لم يوزع: ${diff.toFixed(2)} ج.م</span>`;
      } else {
        statusHtml = `<span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1"><i class="bi bi-x-circle-fill me-1"></i> مجموع البنود يتجاوز إجمالي التبرع بـ ${Math.abs(diff).toFixed(2)} ج.م</span>`;
      }
    }
    $('#splitMatchStatus').html(statusHtml);
  }

  // Sync button
  $('#btnSyncSplitToTotal').on('click', function() {
    let total = 0;
    $('.split-amount-input').each(function() {
      total += parseFloat($(this).val()) || 0;
    });
    if (total > 0) {
      $('#donationAmount').val(total.toFixed(2)).trigger('input');
    }
  });

  // Toggle Split Section Switch
  $('#toggleSplitDonation').on('change', function() {
    const isChecked = $(this).is(':checked');
    if (isChecked) {
      $('#splitDonationSection').slideDown(200);
      $('#btnAddSplitItemBtn').show();
      if ($('.split-item-row').length === 0) {
        const curCamp = $('#donationCampaign').val();
        const curProj = $('#donationProject').val();
        const curGh = $('#donationGuestHouse').val();

        if (curCamp) {
          addSplitItemRow({ type: 'campaign', target_id: curCamp, amount: '' });
        }
        if (curGh) {
          addSplitItemRow({ type: 'guest_house', target_id: curGh, amount: '' });
        }
        if (curProj && !curCamp && !curGh) {
          addSplitItemRow({ type: 'project', target_id: curProj, amount: '' });
        }
        if ($('.split-item-row').length === 0) {
          addSplitItemRow({ type: 'campaign' });
          addSplitItemRow({ type: 'guest_house' });
        }
      }
    } else {
      $('#splitDonationSection').slideUp(200);
      $('#btnAddSplitItemBtn').hide();
    }
  });

  $('#btnAddSplitItemBtn').on('click', function() {
    addSplitItemRow();
  });

  // Initialize preloaded split allocations if present (e.g. from validation error back)
  if (Array.isArray(initialSplits) && initialSplits.length > 0) {
    $('#toggleSplitDonation').prop('checked', true).trigger('change');
    $('#splitItemsBody').empty();
    initialSplits.forEach(function(sp) {
      addSplitItemRow(sp);
    });
  }

  // International Country Phone Rules (National digits without leading 0 + optional leading 0)
  const COUNTRY_PHONE_RULES = {
    'EG': { name: 'مصر', dial: '+20', min: 10, max: 10, allowZero: true, placeholder: '01012345678', regex: /^(1[0125][0-9]{8})$/ },
    'SA': { name: 'السعودية', dial: '+966', min: 9, max: 9, allowZero: true, placeholder: '512345678', regex: /^(5[0-9]{8})$/ },
    'AE': { name: 'الإمارات', dial: '+971', min: 9, max: 9, allowZero: true, placeholder: '501234567', regex: /^(5[0-9]{8})$/ },
    'KW': { name: 'الكويت', dial: '+965', min: 8, max: 8, allowZero: false, placeholder: '51234567' },
    'QA': { name: 'قطر', dial: '+974', min: 8, max: 8, allowZero: false, placeholder: '33123456' },
    'BH': { name: 'البحرين', dial: '+973', min: 8, max: 8, allowZero: false, placeholder: '36123456' },
    'OM': { name: 'سلطنة عُمان', dial: '+968', min: 8, max: 8, allowZero: false, placeholder: '92123456' },
    'JO': { name: 'الأردن', dial: '+962', min: 9, max: 9, allowZero: true, placeholder: '791234567' },
    'PS': { name: 'فلسطين', dial: '+970', min: 9, max: 9, allowZero: true, placeholder: '591234567' },
    'LB': { name: 'لبنان', dial: '+961', min: 7, max: 8, allowZero: true, placeholder: '71123456' },
    'SY': { name: 'سوريا', dial: '+963', min: 9, max: 9, allowZero: true, placeholder: '931234567' },
    'IQ': { name: 'العراق', dial: '+964', min: 10, max: 10, allowZero: true, placeholder: '7712345678' },
    'YE': { name: 'اليمن', dial: '+967', min: 9, max: 9, allowZero: true, placeholder: '771234567' },
    'LY': { name: 'ليبيا', dial: '+218', min: 9, max: 9, allowZero: true, placeholder: '911234567' },
    'SD': { name: 'السودان', dial: '+249', min: 9, max: 9, allowZero: true, placeholder: '911234567' },
    'TN': { name: 'تونس', dial: '+216', min: 8, max: 8, allowZero: false, placeholder: '20123456' },
    'DZ': { name: 'الجزائر', dial: '+213', min: 9, max: 9, allowZero: true, placeholder: '551234567' },
    'MA': { name: 'المغرب', dial: '+212', min: 9, max: 9, allowZero: true, placeholder: '612345678' },
    'MR': { name: 'موريتانيا', dial: '+222', min: 8, max: 8, allowZero: false, placeholder: '22123456' },
    'SO': { name: 'الصومال', dial: '+252', min: 8, max: 9, allowZero: true, placeholder: '611234567' },
    'DJ': { name: 'جيبوتي', dial: '+253', min: 8, max: 8, allowZero: false, placeholder: '77123456' },
    'KM': { name: 'جزر القمر', dial: '+269', min: 7, max: 7, allowZero: false, placeholder: '3212345' },
    'TR': { name: 'تركيا', dial: '+90', min: 10, max: 10, allowZero: true, placeholder: '5321234567' },
    'US': { name: 'أمريكا / كندا', dial: '+1', min: 10, max: 10, allowZero: false, placeholder: '2025550125' },
    'GB': { name: 'بريطانيا', dial: '+44', min: 10, max: 10, allowZero: true, placeholder: '7700900077' },
    'DE': { name: 'ألمانيا', dial: '+49', min: 10, max: 11, allowZero: true, placeholder: '15123456789' },
    'FR': { name: 'فرنسا', dial: '+33', min: 9, max: 9, allowZero: true, placeholder: '612345678' },
    'IT': { name: 'إيطاليا', dial: '+39', min: 9, max: 10, allowZero: false, placeholder: '3123456789' },
    'ES': { name: 'إسبانيا', dial: '+34', min: 9, max: 9, allowZero: false, placeholder: '612345678' },
    'NL': { name: 'هولندا', dial: '+31', min: 9, max: 9, allowZero: true, placeholder: '612345678' },
    'SE': { name: 'السويد', dial: '+46', min: 9, max: 9, allowZero: true, placeholder: '701234567' },
    'CH': { name: 'سويسرا', dial: '+41', min: 9, max: 9, allowZero: true, placeholder: '781234567' },
    'AT': { name: 'النمسا', dial: '+43', min: 10, max: 11, allowZero: true, placeholder: '6641234567' },
    'BE': { name: 'بلجيكا', dial: '+32', min: 9, max: 9, allowZero: true, placeholder: '470123456' },
    'AU': { name: 'أستراليا', dial: '+61', min: 9, max: 9, allowZero: true, placeholder: '412345678' },
    'NZ': { name: 'نيوزيلندا', dial: '+64', min: 8, max: 9, allowZero: true, placeholder: '211234567' },
    'MY': { name: 'ماليزيا', dial: '+60', min: 9, max: 10, allowZero: true, placeholder: '123456789' },
    'ID': { name: 'إندونيسيا', dial: '+62', min: 9, max: 12, allowZero: true, placeholder: '8123456789' },
    'IN': { name: 'الهند', dial: '+91', min: 10, max: 10, allowZero: true, placeholder: '9876543210' },
    'PK': { name: 'باكستان', dial: '+92', min: 10, max: 10, allowZero: true, placeholder: '3001234567' },
    'CN': { name: 'الصين', dial: '+86', min: 11, max: 11, allowZero: false, placeholder: '13800138000' },
    'RU': { name: 'روسيا', dial: '+7', min: 10, max: 10, allowZero: false, placeholder: '9123456789' },
    'BR': { name: 'البرازيل', dial: '+55', min: 10, max: 11, allowZero: false, placeholder: '11912345678' },
    'ZA': { name: 'جنوب أفريقيا', dial: '+27', min: 9, max: 9, allowZero: true, placeholder: '821234567' }
  };

  function normalizeArabicDigits(str) {
    return String(str || '').replace(/[٠-٩]/g, d => '٠١٢٣٤٥٦٧٨٩'.indexOf(d))
                            .replace(/[۰-۹]/g, d => '۰۱۲۳۴۵۶۷۸۹'.indexOf(d));
  }

  function validateAndSyncNewDonorPhone() {
    const registered = $('#donorTypeRegistered').is(':checked');
    const phoneInput = document.getElementById('newDonorPhone');
    const countrySelect = document.getElementById('newDonorPhoneCountry');
    const hintEl = document.getElementById('newDonorPhoneHint');
    const badgeEl = document.getElementById('newDonorPhoneBadge');

    if (!phoneInput || !countrySelect) return;

    const countryCode = countrySelect.value || 'EG';
    const rule = COUNTRY_PHONE_RULES[countryCode] || COUNTRY_PHONE_RULES['EG'];

    // Normalize digits only
    let val = normalizeArabicDigits(phoneInput.value).replace(/[^0-9]/g, '');
    const startsWithZero = val.startsWith('0');
    const allowedMax = (rule.allowZero && startsWithZero) ? (rule.max + 1) : rule.max;
    const allowedMin = (rule.allowZero && startsWithZero) ? (rule.min + 1) : rule.min;

    if (val.length > allowedMax) {
      val = val.slice(0, allowedMax);
    }
    if (phoneInput.value !== val) {
      phoneInput.value = val;
    }

    phoneInput.setAttribute('maxlength', String(rule.allowZero ? (rule.max + 1) : rule.max));
    phoneInput.setAttribute('placeholder', rule.placeholder || '');

    const nationalDigits = (rule.allowZero && startsWithZero) ? val.slice(1) : val;
    const expectedDesc = rule.min === rule.max
      ? (rule.allowZero ? `${rule.min} أرقام (أو ${rule.max + 1} بالصفر)` : `${rule.min} أرقام`)
      : `من ${rule.min} إلى ${rule.max} أرقام`;

    if (registered || val.length === 0) {
      phoneInput.setCustomValidity(registered ? '' : `يرجى إدخال رقم الهاتف لدولة ${rule.name} (${expectedDesc})`);
      phoneInput.classList.remove('is-valid', 'is-invalid');
      if (badgeEl) {
        badgeEl.className = 'badge bg-secondary-subtle text-secondary font-monospace';
        badgeEl.textContent = `${val.length} / ${allowedMax} رقم`;
      }
      if (hintEl) {
        hintEl.className = 'form-text small text-muted mt-1';
        hintEl.textContent = `${rule.name} (${rule.dial}): مطلوب ${expectedDesc}.`;
      }
      return;
    }

    const lenValid = nationalDigits.length >= rule.min && nationalDigits.length <= rule.max;
    const regexValid = !rule.regex || rule.regex.test(nationalDigits);

    if (lenValid && regexValid) {
      phoneInput.setCustomValidity('');
      phoneInput.classList.remove('is-invalid');
      phoneInput.classList.add('is-valid');
      if (badgeEl) {
        badgeEl.className = 'badge bg-success-subtle text-success font-monospace';
        badgeEl.textContent = `${val.length} / ${allowedMax} ✓`;
      }
      if (hintEl) {
        hintEl.className = 'form-text small text-success fw-semibold mt-1';
        hintEl.textContent = `✓ رقم صحيح ومطابق لدولة ${rule.name} (${rule.dial})`;
      }
    } else {
      const errMsg = !lenValid
        ? `رقم الهاتف لدولة ${rule.name} (${rule.dial}) يجب أن يكون ${expectedDesc} (المدخل حالياً: ${val.length})`
        : `بداية رقم الهاتف غير صحيحة لدولة ${rule.name} (${rule.dial})`;
      phoneInput.setCustomValidity(errMsg);
      phoneInput.classList.remove('is-valid');
      phoneInput.classList.add('is-invalid');
      if (badgeEl) {
        badgeEl.className = 'badge bg-danger-subtle text-danger font-monospace';
        badgeEl.textContent = `${val.length} / ${allowedMax}`;
      }
      if (hintEl) {
        hintEl.className = 'form-text small text-danger mt-1';
        hintEl.textContent = errMsg;
      }
    }
  }

  // Toggle Donor Mode (Registered vs New)
  function toggleDonorMode() {
    const registered = $('#donorTypeRegistered').is(':checked');
    $('#registeredDonorSection').toggleClass('d-none', !registered);
    $('#newDonorSection').toggleClass('d-none', registered);
    $('#donorSelect').prop('disabled', !registered).prop('required', registered);
    $('#newDonorSection :input').prop('disabled', registered);
    $('#newDonorName,#newDonorPhone').prop('required', !registered);
    validateAndSyncNewDonorPhone();
    updateDonorInfoBox();
    updateSponsoredMembers();
  }

  $('#newDonorPhoneCountry').on('change', validateAndSyncNewDonorPhone);
  $('#newDonorPhone').on('input change blur', validateAndSyncNewDonorPhone);

  // Toggle New Donor Classification
  $('#newDonorClassification').on('change', function() {
    const isRecurring = $(this).val() === 'recurring';
    $('.new-recurring-field').toggle(isRecurring);
    updateCalculatedNextDue();
  });

  // Sponsorship & Due Date Profile Logic
  let activeSponsorshipProfile = null;

  const purposeLabels = {
    'kafalat_aytam': 'كفالات أيتام',
    'kafalat_awram': 'كفالات أورام',
    'sadaqat': 'صدقات',
    'zakat_maal': 'زكاة مال',
    'sadaqa_jariya': 'صدقات جارية'
  };

  function renderSponsorshipProfile(profile) {
    activeSponsorshipProfile = profile;
    if (!profile) {
      $('#donorSponsorshipSuggestionBox').slideUp(150);
      return;
    }

    // 1. Basic donor info
    $('#donorInfoName').text(profile.name || 'متبرع مسجل');
    $('#donorInfoCode').text(profile.code || '');
    const isRecurring = Boolean(profile.is_recurring);
    if (isRecurring) {
      $('#donorInfoType')
        .text('متبرع شهري / كفالة منتظمة')
        .removeClass('bg-secondary-subtle text-body border-secondary-subtle')
        .addClass('bg-success-subtle text-success border-success-subtle');
    } else {
      $('#donorInfoType')
        .text('متبرع عام')
        .removeClass('bg-success-subtle text-success border-success-subtle')
        .addClass('bg-secondary-subtle text-body border-secondary-subtle');
    }

    if (profile.phone) {
      $('#donorInfoPhone').text(profile.phone);
      $('#donorInfoPhoneWrapper').show();
    } else {
      $('#donorInfoPhoneWrapper').hide();
    }

    // 2. Sponsorship Due Date Alert
    const hasDueDate = Boolean(profile.next_due_date || profile.monthly_donation_day);
    const hasCases = Array.isArray(profile.sponsored_cases) && profile.sponsored_cases.length > 0;
    const hasMonthlyAmount = (parseFloat(profile.monthly_amount) || 0) > 0;
    const showAlert = isRecurring || hasDueDate || hasCases || hasMonthlyAmount;

    if (showAlert) {
      let statusBadgeHtml = '';
      if (profile.next_due_date) {
        const todayStr = new Date().toISOString().slice(0, 10);
        if (profile.next_due_date < todayStr) {
          statusBadgeHtml = `<span class="badge bg-danger text-white"><i class="bi bi-exclamation-triangle-fill me-1"></i> متأخرة / مستحقة السداد (${profile.next_due_date})</span>`;
        } else if (profile.next_due_date.slice(0, 7) === todayStr.slice(0, 7)) {
          statusBadgeHtml = `<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i> مستحقة هذا الشهر (${profile.next_due_date})</span>`;
        } else {
          statusBadgeHtml = `<span class="badge bg-info text-dark"><i class="bi bi-calendar-event me-1"></i> القادم: ${profile.next_due_date}</span>`;
        }
      } else {
        statusBadgeHtml = `<span class="badge bg-warning text-dark">استحقاق شهري</span>`;
      }
      $('#sponsorshipDueBadge').html(statusBadgeHtml);

      let detailsHtml = '';
      detailsHtml += `<div><i class="bi bi-calendar3 text-primary me-1"></i> موعد الشهر: <strong>يوم ${profile.monthly_donation_day || 1} من كل شهر</strong></div>`;
      if (profile.next_due_date) {
        detailsHtml += `<div><i class="bi bi-calendar-check text-success me-1"></i> تاريخ الاستحقاق: <strong class="font-monospace">${profile.next_due_date}</strong></div>`;
      }
      if (profile.last_paid_month) {
        detailsHtml += `<div><i class="bi bi-clock-history text-muted me-1"></i> آخر شهر مسدد: <span class="badge bg-light text-dark border font-monospace">${profile.last_paid_month}</span></div>`;
      }
      if (profile.suggested_month) {
        detailsHtml += `<div><i class="bi bi-arrow-right-circle text-warning me-1"></i> الشهر المستحق: <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace">${profile.suggested_month}</span></div>`;
      }
      $('#sponsorshipDueDetails').html(detailsHtml);
      $('#sponsorshipAlertBox').show();
    } else {
      $('#sponsorshipAlertBox').hide();
    }

    // 3. Quick-fill Suggestion Section
    const hasSuggestions = hasMonthlyAmount || hasCases || profile.suggested_purpose || profile.target_label || profile.suggested_month;
    if (hasSuggestions) {
      if (hasMonthlyAmount) {
        const amtFmt = parseFloat(profile.monthly_amount).toLocaleString();
        $('#suggestionAmountBadge').text(`${amtFmt} ج.م شهرياً`).show();
        $('#quickAmountLabel').text(`المبلغ (${amtFmt} ج.م)`);
        $('#btnQuickSetAmount').show();
      } else {
        $('#suggestionAmountBadge').hide();
        $('#btnQuickSetAmount').hide();
      }

      let chips = '';
      if (hasMonthlyAmount) {
        chips += `
          <div class="col-auto">
            <span class="badge bg-white text-dark border shadow-2xs py-1.5 px-2.5">
              <i class="bi bi-cash-stack text-success me-1"></i> المبلغ المقترح: <strong class="text-success">${parseFloat(profile.monthly_amount).toLocaleString()} ج.م</strong>
            </span>
          </div>`;
      }

      if (hasCases) {
        const casesCount = profile.sponsored_cases.length;
        const caseNames = profile.sponsored_cases.map(c => c.name).join('، ');
        chips += `
          <div class="col-12">
            <span class="badge bg-white text-dark border shadow-2xs py-1.5 px-2.5 d-inline-flex align-items-center flex-wrap gap-1 text-wrap text-start">
              <i class="bi bi-people-fill text-primary me-1"></i> الحالات المكفولة (${casesCount}): <strong>${caseNames}</strong>
            </span>
          </div>`;
        $('#quickCasesLabel').text(`إدراج ${casesCount} حالة`);
        $('#btnQuickInsertCases').show();
      } else {
        $('#btnQuickInsertCases').hide();
      }

      if (profile.target_label && !hasCases) {
        chips += `
          <div class="col-auto">
            <span class="badge bg-white text-dark border shadow-2xs py-1.5 px-2.5">
              <i class="bi bi-bullseye text-danger me-1"></i> الوجهة: <strong>${profile.target_label}</strong>
            </span>
          </div>`;
      }

      if (profile.suggested_purpose) {
        const pLabel = purposeLabels[profile.suggested_purpose] || profile.suggested_purpose;
        chips += `
          <div class="col-auto">
            <span class="badge bg-white text-dark border shadow-2xs py-1.5 px-2.5">
              <i class="bi bi-tag-fill text-info me-1"></i> البند المقترح: <strong>${pLabel}</strong>
            </span>
          </div>`;
        $('#quickPurposeLabel').text(`بند (${pLabel})`);
        $('#btnQuickSetPurpose').show();
      } else {
        $('#btnQuickSetPurpose').hide();
      }

      if (profile.suggested_month) {
        chips += `
          <div class="col-auto">
            <span class="badge bg-white text-dark border shadow-2xs py-1.5 px-2.5 font-monospace">
              <i class="bi bi-calendar2-check text-warning me-1"></i> الشهر المستحق: <strong>${profile.suggested_month}</strong>
            </span>
          </div>`;
        $('#quickMonthLabel').text(`تحديد شهر (${profile.suggested_month})`);
        $('#btnQuickSetMonth').show();
      } else {
        $('#btnQuickSetMonth').hide();
      }

      $('#suggestionChipsContainer').html(chips);
      $('#sponsorshipSuggestionSection').show();
    } else {
      $('#sponsorshipSuggestionSection').hide();
    }

    $('#donorSponsorshipSuggestionBox').slideDown(200);
    updateCalculatedNextDue();
  }

  function applyFullSponsorshipProfile(profile) {
    if (!profile) return;

    // 1. Amount
    if (profile.monthly_amount > 0) {
      $('#donationAmount').val(profile.monthly_amount).trigger('input');
    }

    // 2. Purpose
    if (profile.suggested_purpose) {
      $('#donationPurpose').val(profile.suggested_purpose).trigger('change');
    }

    // 3. Targets (Project / Campaign / Guest House)
    if (profile.project_id) {
      $('#donationProject').val(profile.project_id).trigger('change');
    }
    if (profile.campaign_id) {
      $('#donationCampaign').val(profile.campaign_id).trigger('change');
    }
    if (profile.guest_house_id) {
      $('#donationGuestHouse').val(profile.guest_house_id).trigger('change');
    }

    // 4. Cash channel if any
    if (profile.last_channel) {
      $('#cashChannelSelect').val(profile.last_channel).trigger('change');
    }

    // 5. Insert sponsored cases into allocation table
    if (Array.isArray(profile.sponsored_cases) && profile.sponsored_cases.length > 0) {
      profile.sponsored_cases.forEach(function(c) {
        addBeneficiaryRow({
          id: c.id,
          code: c.code,
          name: c.name,
          phone: c.phone || '',
          amount: c.amount || '',
          notes: c.notes || 'كفالة دورية'
        });
      });
    }

    // 6. Select suggested month
    if (profile.suggested_month) {
      $('.paid-month-input:checked').each(function() {
        $(this).prop('checked', false).trigger('change');
      });
      const targetChk = $(`.paid-month-input[value="${profile.suggested_month}"]`);
      if (targetChk.length) {
        targetChk.prop('checked', true).trigger('change');
      }
    }

    // 7. Show success feedback animation
    $('#quickFillSuccessAlert').stop(true, true).slideDown(200).delay(3500).slideUp(300);
  }

  // Action buttons
  $('#btnApplyAllSponsorship').on('click', function(e) {
    e.preventDefault();
    if (activeSponsorshipProfile) {
      applyFullSponsorshipProfile(activeSponsorshipProfile);
    }
  });

  $('#btnQuickSetAmount').on('click', function(e) {
    e.preventDefault();
    if (activeSponsorshipProfile && activeSponsorshipProfile.monthly_amount > 0) {
      $('#donationAmount').val(activeSponsorshipProfile.monthly_amount).trigger('input');
      $('#donationAmount').focus();
    }
  });

  $('#btnQuickInsertCases').on('click', function(e) {
    e.preventDefault();
    if (activeSponsorshipProfile && Array.isArray(activeSponsorshipProfile.sponsored_cases)) {
      activeSponsorshipProfile.sponsored_cases.forEach(function(c) {
        addBeneficiaryRow({
          id: c.id,
          code: c.code,
          name: c.name,
          phone: c.phone || '',
          amount: c.amount || '',
          notes: c.notes || 'كفالة دورية'
        });
      });
      const casesEl = $('#allocatedCasesList');
      if (casesEl.length && casesEl.offset()) {
        $('html, body').animate({ scrollTop: casesEl.offset().top - 120 }, 300);
      }
    }
  });

  $('#btnQuickSetPurpose').on('click', function(e) {
    e.preventDefault();
    if (activeSponsorshipProfile && activeSponsorshipProfile.suggested_purpose) {
      $('#donationPurpose').val(activeSponsorshipProfile.suggested_purpose).trigger('change');
      if (activeSponsorshipProfile.project_id) $('#donationProject').val(activeSponsorshipProfile.project_id).trigger('change');
      if (activeSponsorshipProfile.campaign_id) $('#donationCampaign').val(activeSponsorshipProfile.campaign_id).trigger('change');
      if (activeSponsorshipProfile.guest_house_id) $('#donationGuestHouse').val(activeSponsorshipProfile.guest_house_id).trigger('change');
    }
  });

  $('#btnQuickSetMonth').on('click', function(e) {
    e.preventDefault();
    if (activeSponsorshipProfile && activeSponsorshipProfile.suggested_month) {
      $('.paid-month-input:checked').each(function() {
        $(this).prop('checked', false).trigger('change');
      });
      const targetChk = $(`.paid-month-input[value="${activeSponsorshipProfile.suggested_month}"]`);
      if (targetChk.length) {
        targetChk.prop('checked', true).trigger('change');
      }
      const monthsEl = $('#monthsCheckboxList');
      if (monthsEl.length && monthsEl.offset()) {
        $('html, body').animate({ scrollTop: monthsEl.offset().top - 120 }, 300);
      }
    }
  });

  // Update Donor Info & Fetch Sponsorship Profile
  function updateDonorInfoBox() {
    const donorId = $('#donorSelect').val();
    if (!donorId) {
      renderSponsorshipProfile(null);
      return;
    }

    const cachedProfile = donorsLookup[String(donorId)];
    if (cachedProfile && (cachedProfile.hasOwnProperty('is_recurring') || cachedProfile.hasOwnProperty('suggested_month') || cachedProfile.hasOwnProperty('sponsored_cases'))) {
      renderSponsorshipProfile(cachedProfile);
    } else {
      $.get('/api/donors/' + donorId + '/sponsorship-profile')
        .done(function(profile) {
          donorsLookup[String(donorId)] = profile;
          if (String($('#donorSelect').val()) === String(donorId)) {
            renderSponsorshipProfile(profile);
          }
        })
        .fail(function() {
          if (cachedProfile) {
            renderSponsorshipProfile(cachedProfile);
          }
        });
    }
  }

  $('#donorSelect').on('change', function() {
    updateDonorInfoBox();
    updateSponsoredMembers();
  });

  function toggleDonationType() {
    const cash = $('#donationType').val() === 'cash';
    $('.cash-only').toggle(cash).find(':input').prop('disabled', !cash);
    $('.in-kind-only').toggle(!cash).find(':input').prop('disabled', cash);
  }

  function updateSponsoredMembers() {
    const purpose = $('#donationPurpose').val();
    const isKafala = ['kafalat_aytam','kafalat_awram'].includes(purpose);
    const donorId = $('#donorTypeRegistered').is(':checked') ? String($('#donorSelect').val() || '') : '';
    const donorMembers = (sponsoredByDonor[donorId] || []).filter(member => purpose === 'kafalat_awram'
      ? member.is_patient
      : (!member.is_patient && member.relationship === 'child'));

    const members = donorMembers.length > 0 ? donorMembers : allMembersList.filter(member => purpose === 'kafalat_awram'
      ? member.is_patient
      : (!member.is_patient && member.relationship === 'child'));

    const select = $('#sponsoredMembersSelect');

    $('#sponsoredMembersWrapper').toggle(isKafala);
    select.prop('disabled', !isKafala).empty();
    if (!isKafala) {
      select.trigger('change');
      $('#noSponsoredMembers').hide();
      return;
    }

    members.forEach(member => {
      const label = `${member.name} — ${member.family || 'الأسرة'}${member.code ? ' — '+member.code : ''}`;
      const selected = oldMemberIds.includes(String(member.id));
      select.append(new Option(label, member.id, selected, selected));
    });

    // Also preserve/add any members that were already chosen from allocated case rows
    $('.case-family-member-input').each(function() {
      const mId = String($(this).val());
      if (mId && !select.find(`option[value="${mId}"]`).length) {
        const row = $(this).closest('.allocated-case-row');
        const name = row.find('.text-truncate').text().trim() || ('ملف #' + mId);
        select.append(new Option(name, mId, true, true));
      }
    });

    select.trigger('change');
    $('#sponsoredMembersLabel').text(purpose === 'kafalat_awram' ? 'اختر مرضى الأورام المكفولين' : 'اختر الأطفال المكفولين');

    const warning = $('#noSponsoredMembers');
    if (!members.length && !$('.case-family-member-input').length) {
      warning.show().find('span').text(purpose === 'kafalat_awram' ? 'لا يوجد مرضى أورام مسجلين حالياً للمتبرع، يمكنك اختيار الحالات من جدول التخصيص أعلاه.' : 'لا يوجد أطفال مسجلين حالياً للمتبرع، يمكنك اختيار الحالات من جدول التخصيص أعلاه.');
    } else {
      warning.hide();
    }
  }

  function toggleCashChannel() {
    const ch = $('#cashChannelSelect').val();
    const isDigital = ['instapay', 'vodafone_cash', 'bank_transfer'].includes(ch);
    $('.digital-pay-fields').toggle(isDigital);
    $('#receiptOrOperationLabel').text(isDigital ? 'رقم العملية / المرجع' : 'رقم الإيصال');
    $('#receiptNumberInput').attr('placeholder', isDigital ? 'رقم عملية التحويل (انستا باي / فودافون كاش)' : 'مثال: 06501');
  }

  $('#receiptNumberInput').on('input change', function() {
    $('#operationNumberInput').val($(this).val());
  });

  // Paid Months Logic & Live Next Due Date Calculation
  $(document).on('click', '.month-pill-checkbox', function(e) {
    e.preventDefault();
    const chk = $(this).find('.paid-month-input');
    const newState = !chk.prop('checked');
    chk.prop('checked', newState).trigger('change');
  });

  $(document).on('change', '.paid-month-input', function() {
    const label = $(this).closest('.month-pill-checkbox');
    const checked = $(this).is(':checked');
    label.toggleClass('active', checked);
    label.find('.check-icon').toggleClass('bi-check2-circle', checked).toggleClass('bi-circle', !checked);
    updateCalculatedNextDue();
  });

  function getActiveMonthlyDay() {
    if ($('#donorTypeRegistered').is(':checked')) {
      const donorId = $('#donorSelect').val();
      const donor = donorsLookup[donorId];
      if (donor && donor.monthly_donation_day) {
        return parseInt(donor.monthly_donation_day, 10);
      }
    } else {
      const val = parseInt($('#newDonorMonthlyDay').val(), 10);
      if (!isNaN(val) && val >= 1 && val <= 31) {
        return val;
      }
    }
    const rDate = $('#receivedAtInput').val();
    if (rDate) {
      const d = new Date(rDate);
      if (!isNaN(d.getTime())) return d.getDate();
    }
    return 1;
  }

  function updateCalculatedNextDue() {
    const checkedMonths = [];
    $('.paid-month-input:checked').each(function() {
      checkedMonths.push($(this).val());
    });

    $('#previewSelectedCount').text(`تم تحديد: ${checkedMonths.length} شهر`);

    if (checkedMonths.length === 0) {
      $('#previewNextDueDate').text('— (لم يتم تحديد شهور مسددة)');
      return;
    }

    checkedMonths.sort();
    const latestMonth = checkedMonths[checkedMonths.length - 1]; // e.g. "2026-11"
    const parts = latestMonth.split('-');
    const year = parseInt(parts[0], 10);
    const month = parseInt(parts[1], 10); // 1-12

    // Next month
    let nextMonth = month + 1;
    let nextYear = year;
    if (nextMonth > 12) {
      nextMonth = 1;
      nextYear += 1;
    }

    const preferredDay = getActiveMonthlyDay();
    const daysInNextMonth = new Date(nextYear, nextMonth, 0).getDate();
    const finalDay = Math.min(preferredDay, daysInNextMonth);

    const pad = n => n < 10 ? '0' + n : n;
    const dateStr = `${nextYear}-${pad(nextMonth)}-${pad(finalDay)}`;

    const arMonths = ['يناير', 'فبراير', 'مارس', 'أبريل', 'مايو', 'يونيو', 'يوليو', 'أغسطس', 'سبتمبر', 'أكتوبر', 'نوفمبر', 'ديسمبر'];
    const friendlyStr = `${finalDay} ${arMonths[nextMonth - 1]} ${nextYear} (${dateStr})`;

    $('#previewNextDueDate').html(`<span class="text-success"><i class="bi bi-calendar-check me-1"></i>${friendlyStr}</span>`);
  }

  // Helper for selecting consecutive months from current month
  function selectConsecutiveMonths(count) {
    $('.paid-month-input').prop('checked', false).trigger('change');
    const allInputs = $('.paid-month-input').toArray();
    // Sort all inputs by month key chronologically
    allInputs.sort((a, b) => $(a).val().localeCompare($(b).val()));

    // Find current month index
    let startIdx = allInputs.findIndex(el => $(el).val() === currentMonthKey);
    if (startIdx === -1) startIdx = 0;

    for (let i = 0; i < count; i++) {
      const idx = startIdx + i;
      if (idx < allInputs.length) {
        $(allInputs[idx]).prop('checked', true).trigger('change');
      }
    }
  }

  // Quick month selection buttons
  $('#btnSelectCurrentMonth').on('click', function() {
    selectConsecutiveMonths(1);
  });

  $('#btnSelectTwoMonths').on('click', function() {
    selectConsecutiveMonths(2);
  });

  $('#btnSelectThreeMonths').on('click', function() {
    selectConsecutiveMonths(3);
  });

  $('#btnSelectSixMonths').on('click', function() {
    selectConsecutiveMonths(6);
  });

  $('#btnSelectFullYear').on('click', function() {
    selectConsecutiveMonths(12);
  });

  $('#btnClearMonths').on('click', function() {
    $('.paid-month-input').prop('checked', false).trigger('change');
  });

  // Select / clear whole year
  $(document).on('click', '.btn-select-year', function() {
    const yr = $(this).data('year');
    $(`.year-months-section[data-year="${yr}"] .paid-month-input`).prop('checked', true).trigger('change');
  });

  $(document).on('click', '.btn-clear-year', function() {
    const yr = $(this).data('year');
    $(`.year-months-section[data-year="${yr}"] .paid-month-input`).prop('checked', false).trigger('change');
  });

  // Year filter buttons
  $('.year-filter-btn').on('click', function() {
    $('.year-filter-btn').removeClass('active btn-primary').addClass('btn-outline-secondary');
    $(this).removeClass('btn-outline-secondary').addClass('active btn-primary');
    const yr = $(this).data('year');
    if (yr === 'all') {
      $('.year-months-section').slideDown(150);
    } else {
      $('.year-months-section').hide();
      $(`.year-months-section[data-year="${yr}"]`).slideDown(150);
    }
  });

  $('input[name="donor_type_toggle"]').on('change', toggleDonorMode);
  $('#donationPurpose').on('change', updateSponsoredMembers);
  $('#donationType').on('change', toggleDonationType);
  $('#cashChannelSelect').on('change', toggleCashChannel);
  $('#receivedAtInput,#newDonorMonthlyDay').on('change input', updateCalculatedNextDue);

  toggleDonorMode();
  toggleDonationType();
  toggleCashChannel();
  updateCalculatedNextDue();
});
</script>
@endsection
