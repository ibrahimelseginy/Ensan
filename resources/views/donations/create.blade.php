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

          <div id="donorDetailsInfoBox" class="mt-2 p-2 rounded bg-light border" style="{{ $selectedDonor ? '' : 'display:none' }}">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 small">
              <div>
                <span class="fw-bold text-primary" id="donorInfoName">{{ $selectedDonor?->name }}</span>
                <span class="badge bg-secondary-subtle text-body ms-1 font-monospace" id="donorInfoCode">{{ $selectedDonor?->display_code }}</span>
                <span class="badge bg-info-subtle text-info ms-1" id="donorInfoType">
                  {{ ($selectedDonor?->classification === 'recurring') ? 'متبرع شهري / كفالة' : 'متبرع غير منتظم' }}
                </span>
              </div>
              <div id="donorDueInfo">
                @if($selectedDonor && $selectedDonor->classification === 'recurring')
                  <span class="text-muted">الاستحقاق: يوم {{ $selectedDonor->monthly_donation_day ?? 1 }} من الشهر</span>
                  @if($selectedDonor->next_due_date)
                    <span class="badge bg-warning-subtle text-dark ms-1">القادم: {{ $selectedDonor->next_due_date->format('Y-m-d') }}</span>
                  @endif
                @endif
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
            <div class="col-md-3">
              <label class="form-label form-label-required">رقم الهاتف</label>
              <input name="new_donor_phone" id="newDonorPhone" value="{{ old('new_donor_phone') }}" class="form-control" placeholder="01xxxxxxxxx">
            </div>
            <div class="col-md-3">
              <label class="form-label">العنوان / منين</label>
              <input name="new_donor_address" value="{{ old('new_donor_address') }}" class="form-control" placeholder="المركز / القرية / المحافظة">
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
            <label class="form-label form-label-required">رقم الإيصال</label>
            <input name="receipt_number" value="{{ old('receipt_number') }}" class="form-control" required placeholder="مثال: 06501">
          </div>
          <div class="col-md-3 cash-only digital-pay-fields" style="display:none">
            <label class="form-label">رقم العملية / المرجع</label>
            <input name="operation_number" id="operationNumberInput" value="{{ old('operation_number') }}" class="form-control" placeholder="رقم المعاملة أو التحويل">
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
          <div class="col-md-4">
            <label class="form-label">المشروع</label>
            <select name="project_id" id="donationProject" class="form-select">
              <option value="">— اختر مشروع (اختياري) —</option>
              @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected((string)old('project_id', request('project_id'))===(string)$project->id)>{{ $project->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-4">
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
            <div class="form-text small text-muted">التبرع للحملة يسمع مباشرةً في المبلغ المستهدف للحملة ونسبة الإنجاز.</div>
          </div>
          <div class="col-md-4">
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

          {{-- تحديد الحالة المستفيدة برقم الكود أو الاسم --}}
          <div class="col-md-12">
            <div class="p-3 rounded-3 bg-white bg-opacity-75 border border-success border-opacity-25 shadow-sm">
              <div class="d-flex justify-content-between align-items-center mb-1">
                <label class="form-label fw-bold text-success mb-0">
                  <i class="bi bi-person-heart me-1"></i> الحالة المستفيدة (كود الحالة أو الاسم)
                </label>
                <span class="text-muted small"><i class="bi bi-search me-1"></i> اكتب كود الحالة (مثال: BEN-0012) أو اسم المستفيد</span>
              </div>
              <select name="beneficiary_id" id="beneficiarySelect" class="form-select">
                <option value=""></option>
                @if($selectedBeneficiary)
                  <option value="{{ $selectedBeneficiary->id }}" selected>
                    {{ $selectedBeneficiary->full_name }} — {{ $selectedBeneficiary->code ?? ('BEN-' . $selectedBeneficiary->id) }}
                  </option>
                @endif
              </select>
              <div class="form-text small text-muted">
                إذا كان التبرع موجهاً لحالة معينة أو كفالة، اختر الحالة وسيتم تسجيل التبرع مباشرةً في ملفها وسجلها المالي.
              </div>

              <div id="beneficiarySelectedBadge" class="mt-2" style="{{ $selectedBeneficiary ? '' : 'display:none' }}">
                <div class="p-2.5 rounded bg-success bg-opacity-10 border border-success border-opacity-25 d-flex align-items-center justify-content-between">
                  <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-success font-monospace px-2.5 py-1.5" id="badgeCaseCode">{{ $selectedBeneficiary?->code ?? ('BEN-' . $selectedBeneficiary?->id) }}</span>
                    <span class="fw-bold text-success" id="badgeCaseName">{{ $selectedBeneficiary?->full_name }}</span>
                    @if($selectedBeneficiary?->phone)
                      <span class="text-muted small">({{ $selectedBeneficiary->phone }})</span>
                    @endif
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" id="clearBeneficiaryBtn" title="إلغاء تخصيص الحالة">
                    <i class="bi bi-x-circle me-1"></i> مسح
                  </button>
                </div>
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
                <div class="btn-group btn-group-sm" role="group">
                  <button type="button" class="btn btn-outline-primary" id="btnSelectCurrentMonth">الشهر الحالي فقط</button>
                  <button type="button" class="btn btn-outline-primary" id="btnSelectTwoMonths">شهرين</button>
                  <button type="button" class="btn btn-outline-primary" id="btnSelectThreeMonths">3 شهور</button>
                  <button type="button" class="btn btn-outline-secondary" id="btnClearMonths">إلغاء التحديد</button>
                </div>
              </div>
              <div class="small text-muted mb-2.5">
                حدد الشهور التي دفعها المتبرع (شهر الحالي، شهرين، 3 شهور أو متأخرات)، وسيقوم النظام بتسجيلها في ملفه وتحديد <strong>موعد الاستحقاق القادم تلقائياً</strong> لإرسال إشعار للاستقبال قبله بـ 24 ساعة.
              </div>

              @php
                $currentDate = now();
                $monthOptions = [];
                // 2 previous months + current month + 6 upcoming months
                for ($i = -2; $i <= 6; $i++) {
                    $mDate = $currentDate->copy()->addMonths($i);
                    $key = $mDate->format('Y-m');
                    $arMonthName = match($mDate->format('n')) {
                        '1' => 'يناير', '2' => 'فبراير', '3' => 'مارس', '4' => 'أبريل',
                        '5' => 'مايو', '6' => 'يونيو', '7' => 'يوليو', '8' => 'أغسطس',
                        '9' => 'سبتمبر', '10' => 'أكتوبر', '11' => 'نوفمبر', '12' => 'ديسمبر',
                        default => ''
                    };
                    $monthOptions[] = [
                        'key' => $key,
                        'label' => $arMonthName . ' ' . $mDate->format('Y'),
                        'is_current' => ($i === 0),
                        'index' => $i,
                    ];
                }
                $oldPaidMonths = (array) old('paid_months', []);
              @endphp

              <div class="d-flex flex-wrap gap-2 pt-1" id="monthsCheckboxList">
                @foreach($monthOptions as $mOpt)
                  @php
                    $isChecked = in_array($mOpt['key'], $oldPaidMonths, true) || (empty($oldPaidMonths) && $mOpt['is_current'] && (request('donor_id') || old('purpose') === 'kafalat_aytam' || old('purpose') === 'kafalat_awram'));
                  @endphp
                  <label class="month-pill-checkbox {{ $isChecked ? 'active' : '' }}" data-month="{{ $mOpt['key'] }}" data-index="{{ $mOpt['index'] }}">
                    <input type="checkbox" name="paid_months[]" value="{{ $mOpt['key'] }}" class="paid-month-input" @checked($isChecked)>
                    <i class="bi {{ $isChecked ? 'bi-check2-circle' : 'bi-circle' }} check-icon"></i>
                    <span>{{ $mOpt['label'] }}</span>
                    @if($mOpt['is_current'])
                      <span class="badge bg-success-subtle text-success badge-current" style="font-size:0.7rem">الحالي</span>
                    @endif
                  </label>
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
      return [(string)$donor->id => [
          'id' => $donor->id,
          'name' => $donor->name,
          'code' => $donor->display_code,
          'classification' => $donor->classification,
          'recurring_cycle' => $donor->recurring_cycle,
          'monthly_donation_day' => (int) ($donor->monthly_donation_day ?: 1),
          'sponsorship_monthly_amount' => (float) $donor->sponsorship_monthly_amount,
          'next_due_date' => $donor->next_due_date ? $donor->next_due_date->format('Y-m-d') : null,
          'sponsored_beneficiary_id' => $donor->sponsored_beneficiary_id,
          'sponsored_beneficiary_name' => $donor->sponsoredBeneficiary?->full_name,
          'sponsored_beneficiary_code' => $donor->sponsoredBeneficiary?->code,
      ]];
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
@endphp
<script>
$(function () {
  const sponsoredByDonor = @json($sponsoredByDonorData);
  const donorsLookup = @json($donorsLookup);
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
              text: item.full_name + ' — ' + (item.code || ('ID: ' + item.id)) + (item.phone ? ' (' + item.phone + ')' : ''),
              code: item.code || ('BEN-' + item.id),
              full_name: item.full_name,
              phone: item.phone
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

  // Update Beneficiary Badge
  function updateBeneficiaryBadge(data) {
    if (data && data.id) {
      const code = data.code || 'BEN-' + data.id;
      const name = data.full_name || data.text || '';
      $('#badgeCaseCode').text(code);
      $('#badgeCaseName').text(name);
      $('#beneficiarySelectedBadge').show();
    } else {
      $('#beneficiarySelectedBadge').hide();
    }
  }

  $('#beneficiarySelect').on('select2:select', function(e) {
    updateBeneficiaryBadge(e.params.data);
  });

  $('#beneficiarySelect').on('select2:clear', function() {
    updateBeneficiaryBadge(null);
  });

  $('#clearBeneficiaryBtn').on('click', function() {
    $('#beneficiarySelect').val(null).trigger('change');
    updateBeneficiaryBadge(null);
  });

  // Toggle Donor Mode (Registered vs New)
  function toggleDonorMode() {
    const registered = $('#donorTypeRegistered').is(':checked');
    $('#registeredDonorSection').toggleClass('d-none', !registered);
    $('#newDonorSection').toggleClass('d-none', registered);
    $('#donorSelect').prop('disabled', !registered).prop('required', registered);
    $('#newDonorSection :input').prop('disabled', registered);
    $('#newDonorName,#newDonorPhone').prop('required', !registered);
    updateDonorInfoBox();
    updateSponsoredMembers();
  }

  // Toggle New Donor Classification
  $('#newDonorClassification').on('change', function() {
    const isRecurring = $(this).val() === 'recurring';
    $('.new-recurring-field').toggle(isRecurring);
    updateCalculatedNextDue();
  });

  // Update Donor Info Box & Auto-link
  function updateDonorInfoBox() {
    const donorId = $('#donorSelect').val();
    const donor = donorsLookup[donorId];
    if (donor) {
      $('#donorInfoName').text(donor.name);
      $('#donorInfoCode').text(donor.code);
      const isRecurring = donor.classification === 'recurring';
      $('#donorInfoType').text(isRecurring ? 'متبرع شهري / كفالة منتظمة' : 'متبرع غير منتظم');
      
      let dueHtml = '';
      if (isRecurring) {
        dueHtml = `<span class="text-muted">الاستحقاق الشهري: يوم ${donor.monthly_donation_day || 1} من كل شهر</span>`;
        if (donor.next_due_date) {
          dueHtml += ` <span class="badge bg-warning-subtle text-dark ms-1">القادم: ${donor.next_due_date}</span>`;
        }
      }
      $('#donorDueInfo').html(dueHtml);
      $('#donorDetailsInfoBox').show();

      // If donor has a sponsored beneficiary and none selected, auto-select
      if (donor.sponsored_beneficiary_id && !$('#beneficiarySelect').val()) {
        const option = new Option(
          donor.sponsored_beneficiary_name + ' — ' + (donor.sponsored_beneficiary_code || ('BEN-' + donor.sponsored_beneficiary_id)),
          donor.sponsored_beneficiary_id,
          true,
          true
        );
        $('#beneficiarySelect').append(option).trigger('change');
        updateBeneficiaryBadge({
          id: donor.sponsored_beneficiary_id,
          code: donor.sponsored_beneficiary_code || ('BEN-' + donor.sponsored_beneficiary_id),
          full_name: donor.sponsored_beneficiary_name
        });
      }

      // If amount is empty and donor has a sponsorship amount, suggest it
      if (!$('#donationAmount').val() && donor.sponsorship_monthly_amount > 0) {
        $('#donationAmount').val(donor.sponsorship_monthly_amount);
      }
    } else {
      $('#donorDetailsInfoBox').hide();
    }
    updateCalculatedNextDue();
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
    select.trigger('change');
    $('#sponsoredMembersLabel').text(purpose === 'kafalat_awram' ? 'اختر مرضى الأورام المكفولين' : 'اختر الأطفال المكفولين');

    const warning = $('#noSponsoredMembers');
    if (!members.length) {
      warning.show().find('span').text(purpose === 'kafalat_awram' ? 'لا يوجد مرضى أورام مسجلين حالياً.' : 'لا يوجد أطفال مسجلين حالياً.');
    } else {
      warning.hide();
    }
  }

  function toggleCashChannel() {
    const ch = $('#cashChannelSelect').val();
    const isDigital = ['instapay', 'vodafone_cash', 'bank_transfer'].includes(ch);
    $('.digital-pay-fields').toggle(isDigital);
  }

  // Paid Months Logic & Live Next Due Date Calculation
  $('.month-pill-checkbox').on('click', function(e) {
    if (e.target.tagName.toLowerCase() !== 'input') {
      const chk = $(this).find('input[type="checkbox"]');
      chk.prop('checked', !chk.prop('checked')).trigger('change');
    }
  });

  $('.paid-month-input').on('change', function() {
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

  // Quick month selection buttons
  $('#btnSelectCurrentMonth').on('click', function() {
    $('.paid-month-input').prop('checked', false).trigger('change');
    $(`.paid-month-input[value="${currentMonthKey}"]`).prop('checked', true).trigger('change');
  });

  $('#btnSelectTwoMonths').on('click', function() {
    $('.paid-month-input').prop('checked', false).trigger('change');
    $('.month-pill-checkbox').each(function() {
      const idx = parseInt($(this).data('index'), 10);
      if (idx === 0 || idx === 1) {
        $(this).find('.paid-month-input').prop('checked', true).trigger('change');
      }
    });
  });

  $('#btnSelectThreeMonths').on('click', function() {
    $('.paid-month-input').prop('checked', false).trigger('change');
    $('.month-pill-checkbox').each(function() {
      const idx = parseInt($(this).data('index'), 10);
      if (idx === 0 || idx === 1 || idx === 2) {
        $(this).find('.paid-month-input').prop('checked', true).trigger('change');
      }
    });
  });

  $('#btnClearMonths').on('click', function() {
    $('.paid-month-input').prop('checked', false).trigger('change');
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
