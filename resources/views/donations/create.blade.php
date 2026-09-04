@extends('layouts.app')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    .receipt-purpose-box { border:1px solid rgba(16,185,129,.3); border-radius:16px; background:rgba(16,185,129,.055); padding:1.25rem; }
    .purpose-title { color:#059669; font-weight:800; }
    .theme-dark .receipt-purpose-box { background:rgba(16,185,129,.08); }
  </style>
@endsection

@section('content')
<div class="page-header">
  <div>
    <h4 class="mb-1"><i class="bi bi-receipt text-primary"></i> إضافة تبرع وإصدار إيصال</h4>
    <div class="small text-muted">البيانات الأساسية فقط، مع تحديد بند «وذلك قيمة».</div>
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
    <form method="POST" action="{{ route('donations.store') }}" id="donationForm">
      @csrf

      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-person-circle"></i><span>بيانات المتبرع</span></div>
        <div class="btn-group w-100 mb-3" role="group">
          <input type="radio" class="btn-check" name="donor_type_toggle" id="donorTypeRegistered" value="registered" @checked(!old('new_donor_name'))>
          <label class="btn btn-outline-primary" for="donorTypeRegistered"><i class="bi bi-search me-1"></i> متبرع مسجل</label>
          <input type="radio" class="btn-check" name="donor_type_toggle" id="donorTypeNew" value="new" @checked(old('new_donor_name'))>
          <label class="btn btn-outline-success" for="donorTypeNew"><i class="bi bi-person-plus me-1"></i> متبرع جديد</label>
        </div>

        <div id="registeredDonorSection">
          <label class="form-label form-label-required">ابحث عن المتبرع</label>
          <select id="donorSelect" name="donor_id" class="form-select @error('donor_id') is-invalid @enderror">
            <option value=""></option>
            @foreach($donors as $donor)
              <option value="{{ $donor->id }}" @selected((string)old('donor_id', request('donor_id')) === (string)$donor->id)>
                {{ $donor->name }} — {{ $donor->code }}{{ $donor->phone ? ' — '.$donor->phone : '' }}
              </option>
            @endforeach
          </select>
          @error('donor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div id="newDonorSection" class="d-none">
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label">كود المتبرع <span class="badge bg-primary-subtle text-primary">ثابت</span></label><input name="new_donor_code" id="newDonorCode" value="{{ old('new_donor_code') }}" class="form-control font-monospace" dir="ltr" placeholder="تلقائي أو DON-10025"></div>
            <div class="col-md-4"><label class="form-label form-label-required">اسم المتبرع</label><input name="new_donor_name" id="newDonorName" value="{{ old('new_donor_name') }}" class="form-control"></div>
            <div class="col-md-4"><label class="form-label form-label-required">رقم الهاتف</label><input name="new_donor_phone" id="newDonorPhone" value="{{ old('new_donor_phone') }}" class="form-control" placeholder="01xxxxxxxxx"></div>
            <div class="col-12"><label class="form-label">العنوان</label><input name="new_donor_address" value="{{ old('new_donor_address') }}" class="form-control"></div>
          </div>
        </div>
      </div>

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
            <div class="input-group"><input name="amount" type="number" step="0.01" min="0.01" value="{{ old('amount') }}" class="form-control" placeholder="0.00"><select name="currency" class="form-select" style="max-width:95px"><option value="EGP">EGP</option><option value="USD">USD</option><option value="EUR">EUR</option><option value="SAR">SAR</option></select></div>
          </div>
          <div class="col-md-3 cash-only"><label class="form-label form-label-required">طريقة الدفع</label><select name="cash_channel" class="form-select"><option value="cash" @selected(old('cash_channel')==='cash')>نقدي</option><option value="instapay" @selected(old('cash_channel')==='instapay')>انستا باي</option><option value="vodafone_cash" @selected(old('cash_channel')==='vodafone_cash')>فودافون كاش</option><option value="delegate" @selected(old('cash_channel')==='delegate')>مندوب</option></select></div>
          <div class="col-md-3 cash-only"><label class="form-label form-label-required">رقم الإيصال</label><input name="receipt_number" value="{{ old('receipt_number') }}" class="form-control" required placeholder="مثال: 06501"></div>
          <div class="col-md-6 cash-only">
            <label class="form-label form-label-required"><i class="bi bi-safe text-primary me-1"></i> الخزينة</label>
            <select name="treasury_id" class="form-select">
              <option value="">— اختر الخزينة —</option>
              @foreach($treasuries as $treasury)<option value="{{ $treasury->id }}" @selected((string)old('treasury_id')===(string)$treasury->id)>{{ $treasury->name }} — {{ number_format((float)$treasury->current_balance,2) }} {{ $treasury->currency }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6"><label class="form-label">تاريخ الاستلام</label><input name="received_at" type="date" value="{{ old('received_at', date('Y-m-d')) }}" class="form-control"></div>

          <div class="col-md-4 in-kind-only" style="display:none"><label class="form-label form-label-required">القيمة التقديرية</label><input name="estimated_value" type="number" step="0.01" min="0.01" value="{{ old('estimated_value') }}" class="form-control"></div>
          <div class="col-md-4 in-kind-only" style="display:none"><label class="form-label">الكمية</label><input name="quantity" type="number" step="0.001" min="0.001" value="{{ old('quantity') }}" class="form-control"></div>
          <div class="col-md-4 in-kind-only" style="display:none"><label class="form-label">الصنف</label><select name="item_id" class="form-select"><option value="">— اختر الصنف —</option>@foreach($items as $item)<option value="{{ $item->id }}" @selected((string)old('item_id')===(string)$item->id)>{{ $item->name }}</option>@endforeach</select></div>
          <div class="col-md-6 in-kind-only" style="display:none"><label class="form-label">المخزن</label><select name="warehouse_id" class="form-select"><option value="">— اختر المخزن —</option>@foreach($warehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected((string)old('warehouse_id')===(string)$warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select></div>
          <div class="col-md-6 in-kind-only d-flex align-items-end" style="display:none"><div class="form-check mb-2"><input type="hidden" name="add_to_inventory" value="0"><input class="form-check-input" type="checkbox" name="add_to_inventory" value="1" id="addToInventory" @checked(old('add_to_inventory'))><label class="form-check-label" for="addToInventory">إضافة التبرع العيني للمخزون</label></div></div>
        </div>
      </div>

      <div class="receipt-purpose-box">
        <div class="purpose-title mb-3"><i class="bi bi-check2-circle me-1"></i> وذلك قيمة والمشروع</div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">المشروع</label>
            <select name="project_id" id="donationProject" class="form-select">
              <option value="">— اختر مشروع (اختياري) —</option>
              @foreach($projects as $project)
                <option value="{{ $project->id }}" @selected((string)old('project_id', request('project_id'))===(string)$project->id)>{{ $project->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label form-label-required">بند الإيصال</label>
            <select name="purpose" id="donationPurpose" class="form-select" required>
              <option value="">— اختر —</option>
              <option value="kafalat_aytam" @selected(old('purpose')==='kafalat_aytam')>كفالات أيتام</option>
              <option value="kafalat_awram" @selected(old('purpose')==='kafalat_awram')>كفالات أورام</option>
              <option value="sadaqat" @selected(old('purpose')==='sadaqat')>صدقات</option>
              <option value="zakat_maal" @selected(old('purpose')==='zakat_maal')>زكاة مال</option>
              <option value="sadaqa_jariya" @selected(old('purpose')==='sadaqa_jariya')>صدقات جارية</option>
            </select>
          </div>

          <div class="col-12" id="sponsoredMembersWrapper" style="display:none">
            <label class="form-label fw-bold" id="sponsoredMembersLabel">اختر الملفات المكفولة</label>
            <select name="family_member_ids[]" id="sponsoredMembersSelect" class="form-select" multiple data-placeholder="اختر واحدًا أو أكثر..."></select>
            <div id="noSponsoredMembers" class="alert alert-warning py-2 mt-2 mb-0" style="display:none"><i class="bi bi-exclamation-triangle me-1"></i><span></span></div>
            <div class="form-text">يمكن اختيار اسم واحد أو أكثر من قائمة الحالات المتاحة.</div>
          </div>

          <div class="col-12">
            <label class="form-label">ملاحظات أو تخصيص لحالة معينة</label>
            <textarea name="allocation_note" class="form-control" rows="3" placeholder="مثال: وصلة مياه للحالة رقم 105، عملية محددة، أو أي توضيح خاص...">{{ old('allocation_note') }}</textarea>
          </div>
        </div>
      </div>

      <div class="mt-4 text-end"><button type="submit" class="btn btn-primary px-5 fw-bold"><i class="bi bi-check-lg me-1"></i> حفظ التبرع</button></div>
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
  const allMembersList = @json($allMembersData);
  const oldMemberIds = @json(array_map('strval', (array)old('family_member_ids', [])));

  $('#donorSelect,#donationProject').select2({theme:'bootstrap-5',dir:'rtl',width:'100%',allowClear:true,placeholder:'ابحث بالتحديد...'});
  $('#sponsoredMembersSelect').select2({theme:'bootstrap-5',dir:'rtl',width:'100%',placeholder:'اختر واحدًا أو أكثر...'});

  function toggleDonorMode() {
    const registered = $('#donorTypeRegistered').is(':checked');
    $('#registeredDonorSection').toggleClass('d-none', !registered);
    $('#newDonorSection').toggleClass('d-none', registered);
    $('#donorSelect').prop('disabled', !registered).prop('required', registered);
    $('#newDonorSection :input').prop('disabled', registered);
    $('#newDonorName,#newDonorPhone').prop('required', !registered);
    updateSponsoredMembers();
  }

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

    // Use donor's sponsored members if available; otherwise show all active system members matching the purpose
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

  $('input[name="donor_type_toggle"]').on('change', toggleDonorMode);
  $('#donorSelect,#donationPurpose').on('change', updateSponsoredMembers);
  $('#donationType').on('change', toggleDonationType);
  toggleDonorMode();
  toggleDonationType();
});
</script>
@endsection
