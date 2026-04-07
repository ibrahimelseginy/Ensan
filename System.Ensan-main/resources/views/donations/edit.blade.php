@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-heart-half text-primary"></i>
      تعديل التبرع
    </h4>
    <a href="{{ route('donations.show', $donation) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('donations.update', $donation) }}" id="donationForm">
        @csrf @method('PUT')
        <div class="row g-3">
          {{-- Donor Information Display --}}
          <div class="col-md-12">
            <div class="alert alert-info border-0 shadow-sm">
              <h6 class="alert-heading mb-3">
                <i class="bi bi-person-circle me-2"></i>
                بيانات المتبرع
              </h6>
              <div class="row g-3">
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-muted">الاسم</label>
                  <input type="text" class="form-control" value="{{ $donation->donor->name }}" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-muted">الهاتف</label>
                  <input type="text" class="form-control text-start" value="{{ $donation->donor->phone ?? '—' }}" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-muted">العنوان</label>
                  <input type="text" class="form-control" value="{{ $donation->donor->address ?? '—' }}" readonly>
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold small text-muted">التصنيف</label>
                  <input type="text" class="form-control" value="{{ $donation->donor->classification === 'recurring' ? 'متكرر' : ($donation->donor->classification === 'one_time' ? 'مرة واحدة' : '—') }}" readonly>
                </div>
              </div>
              <input type="hidden" name="donor_id" value="{{ $donation->donor_id }}">
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">النوع</label>
            <select name="type" class="form-select" id="donationType">
              <option value="cash" @selected($donation->type === 'cash')>نقدي</option>
              <option value="in_kind" @selected($donation->type === 'in_kind')>عيني</option>
            </select>
          </div>
          <div class="col-md-4 cash-only">
            <label class="form-label">المبلغ</label>
            <div class="input-group">
              <input name="amount" type="number" step="any" min="0" class="form-control"
                value="{{ (float) $donation->amount }}">
              <select name="currency" class="form-select" style="max-width:140px">
                <option value="EGP" @selected(($donation->currency ?? 'EGP') === 'EGP')>EGP</option>
                <option value="USD" @selected(($donation->currency ?? '') === 'USD')>USD</option>
                <option value="EUR" @selected(($donation->currency ?? '') === 'EUR')>EUR</option>
                <option value="SAR" @selected(($donation->currency ?? '') === 'SAR')>SAR</option>
                <option value="GBP" @selected(($donation->currency ?? '') === 'GBP')>GBP</option>
              </select>
            </div>
          </div>
          <div class="col-md-6 cash-only">
            <label class="form-label">طريقة الدفع</label>
            <select name="cash_channel" class="form-select" id="cashChannelSel">
              <option value="cash" @selected($donation->cash_channel === 'cash')>نقدي</option>
              <option value="instapay" @selected($donation->cash_channel === 'instapay')>انستا باي</option>
              <option value="vodafone_cash" @selected($donation->cash_channel === 'vodafone_cash')>فودافون كاش</option>
              <option value="delegate" @selected($donation->cash_channel === 'delegate')>مندوب</option>
            </select>
          </div>
          <div class="col-md-6 cash-only">
            <label class="form-label">رقم الإيصال</label>
            <input name="receipt_number" class="form-control @error('receipt_number') is-invalid @enderror"
              value="{{ old('receipt_number', $donation->receipt_number) }}" placeholder="مثال: RC-2025-000123">
            @error('receipt_number')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-md-6 in-kind-only">
            <label class="form-label">القيمة التقديرية</label>
            <div class="input-group">
              <input name="estimated_value" type="number" step="0.01" min="0.01" inputmode="decimal" class="form-control"
                value="{{ $donation->estimated_value }}">
              <select name="currency" class="form-select" style="max-width:140px">
                <option value="EGP" @selected(($donation->currency ?? 'EGP') === 'EGP')>EGP</option>
                <option value="USD" @selected(($donation->currency ?? '') === 'USD')>USD</option>
                <option value="EUR" @selected(($donation->currency ?? '') === 'EUR')>EUR</option>
                <option value="SAR" @selected(($donation->currency ?? '') === 'SAR')>SAR</option>
                <option value="GBP" @selected(($donation->currency ?? '') === 'GBP')>GBP</option>
              </select>
            </div>
          </div>
          <div class="col-md-6 in-kind-only">
            <label class="form-label">المخزن</label>
            <select name="warehouse_id" class="form-select">
              @foreach($warehouses as $w)
                <option value="{{ $w->id }}" @selected($donation->warehouse_id == $w->id)>{{ $w->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">التبرع لـ</label>
            <select id="allocType" name="allocation_type" class="form-select">
              @php
                $allocInit = '';
                $note = (string) ($donation->allocation_note ?? '');
                if (str_contains($note, 'sponsorship=sadaqa_jariya')) {
                  $allocInit = 'sadaqa_jariya';
                } elseif (str_contains($note, 'sponsorship=')) {
                  $allocInit = 'sponsorship';
                } elseif ($donation->project_id) {
                  $allocInit = 'project';
                } elseif ($donation->guest_house_id) {
                  $allocInit = 'guest_house';
                } elseif ($donation->campaign_id) {
                  $allocInit = 'campaign';
                }
              @endphp
              <option value="" @selected($allocInit === '')>—</option>
              <option value="project" @selected($allocInit === 'project')>مشروع</option>
              <option value="guest_house" @selected($allocInit === 'guest_house')>دار الضيافة</option>
              <option value="campaign" @selected($allocInit === 'campaign')>حملة</option>
              <option value="sponsorship" @selected($allocInit === 'sponsorship')>كفالة</option>
              <option value="sadaqa_jariya" @selected($allocInit === 'sadaqa_jariya')>صدقة جارية</option>
            </select>
          </div>
          <div class="col-md-6 alloc alloc-project" style="display:none">
            <label class="form-label">المشروع</label>
            <select name="project_id" class="form-select" id="projectSel">
              <option value="">—</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected($donation->project_id == $p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 alloc alloc-campaign" style="display:none">
            <label class="form-label">الحملة</label>
            <select name="campaign_id" class="form-select">
              <option value="">—</option>
              @foreach($campaigns as $c)
                <option value="{{ $c->id }}" @selected($donation->campaign_id == $c->id)>{{ $c->name }}
                  ({{ $c->season_year }})</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 alloc alloc-guest_house" style="display:none">
            <label class="form-label">دار الضيافة</label>
            <select name="guest_house_id" class="form-select">
              <option value="">—</option>
              @foreach($guestHouses as $gh)
                <option value="{{ $gh->id }}" @selected($donation->guest_house_id == $gh->id)>
                  {{ $gh->name }}{{ $gh->location ? (' - ' . $gh->location) : '' }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 alloc alloc-sponsorship" style="display:none">
            <label class="form-label">نوع الكفالة</label>
            <select id="sponsorshipKind" name="sponsorship_type" class="form-select">
              @php $note = (string) ($donation->allocation_note ?? ''); @endphp
              <option value="طفل" @selected(str_contains($note, 'sponsorship=طفل'))>طفل</option>
              <option value="أسرة" @selected(str_contains($note, 'sponsorship=أسرة'))>أسرة</option>
            </select>
          </div>
          <div class="col-md-6 alloc alloc-sponsorship" id="sponsorshipBeneficiaryBox" style="display:none">
            <label class="form-label">الطفل/الأسرة المكفولة</label>
            <select id="sponsorshipBeneficiary" name="beneficiary_id" class="form-select">
              @foreach($beneficiaries as $b)
                <option value="{{ $b->id }}">{{ $b->full_name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">المندوب</label>
            <select name="delegate_id" class="form-select">
              <option value="">—</option>
              @foreach($delegates as $d)
                <option value="{{ $d->id }}" @selected($donation->delegate_id == $d->id)>{{ $d->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 route-only" style="display:none">
            <label class="form-label">خط السير</label>
            <select name="route_id" class="form-select" id="routeSel" disabled>
              <option value="">—</option>
              @foreach($routes as $r)
                <option value="{{ $r->id }}" @selected($donation->route_id == $r->id)>{{ $r->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">ملاحظة تخصيص</label>
            <textarea name="allocation_note" class="form-control" rows="2">{{ $donation->allocation_note }}</textarea>
          </div>
          <div class="col-md-6">
            <label class="form-label">تاريخ الاستلام</label>
            <input name="received_at" type="date" class="form-control"
              value="{{ optional($donation->received_at)->format('Y-m-d') }}">
          </div>
        </div>
        <div class="d-flex gap-2 justify-content-end mt-4">
          <a href="{{ route('donations.show', $donation) }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
          </button>
        </div>
      </form>
    </div>
  </div>
  <script>
    const t = document.getElementById('donationType');
    const cashChannel = document.getElementById('cashChannelSel');
    const routeWrap = document.querySelector('.route-only');
    const routeSel = document.getElementById('routeSel');
    function toggle() {
      const cash = document.querySelectorAll('.cash-only');
      const kind = document.querySelectorAll('.in-kind-only');
      const isCash = t.value === 'cash';
      cash.forEach(e => e.style.display = isCash ? 'block' : 'none');
      kind.forEach(e => e.style.display = isCash ? 'none' : 'block');
      const cashCur = document.querySelector('.cash-only select[name="currency"]');
      const kindCur = document.querySelector('.in-kind-only select[name="currency"]');
      if (cashCur) cashCur.disabled = !isCash;
      if (kindCur) kindCur.disabled = isCash;
      const showRoute = isCash && cashChannel && cashChannel.value === 'delegate';
      if (routeWrap) { routeWrap.style.display = showRoute ? 'block' : 'none'; }
      if (routeSel) { routeSel.disabled = !showRoute; if (!showRoute) { routeSel.value = ''; } }
    }
    toggle();
    t.addEventListener('change', toggle);
    if (cashChannel) { cashChannel.addEventListener('change', toggle); }
    const allocType = document.getElementById('allocType');
    function allocToggle() {
      document.querySelectorAll('.alloc').forEach(function (e) { e.style.display = 'none'; });
      ['project_id', 'guest_house_id', 'campaign_id', 'sponsorship_type', 'beneficiary_id'].forEach(function (n) { var el = document.querySelector('[name="' + n + '"]'); if (el) { el.disabled = true; el.removeAttribute('required'); } });
      var v = allocType ? allocType.value : '';
      if (v) {
        if (v === 'sponsorship') {
          document.querySelectorAll('.alloc.alloc-sponsorship').forEach(function (e) { e.style.display = 'block'; });
          ['sponsorship_type', 'beneficiary_id'].forEach(function (n) { var el = document.querySelector('[name="' + n + '"]'); if (el) { el.disabled = false; el.setAttribute('required', 'required'); } });
        } else if (v === 'sadaqa_jariya') {
          var boxP = document.querySelector('.alloc.alloc-project'); if (boxP) { boxP.style.display = 'block'; var selP = boxP.querySelector('select'); if (selP) { selP.disabled = false; /* لا نجعلها مطلوبة */ } }
        } else {
          var box = document.querySelector('.alloc.alloc-' + v); if (box) { box.style.display = 'block'; var sel = box.querySelector('select'); if (sel) { sel.disabled = false; sel.setAttribute('required', 'required'); } }
        }
      }
      updateAllocationNote();
    }
    if (allocType) { allocType.addEventListener('change', allocToggle); }
    allocToggle();
    var allocationNoteText = document.querySelector('textarea[name="allocation_note"]');
    function updateAllocationNote() {
      if (!allocationNoteText) return;
      var v = allocType ? allocType.value : '';
      if (v === 'sponsorship') {
        var kind = (document.getElementById('sponsorshipKind') || { value: '' }).value;
        var ben = (document.getElementById('sponsorshipBeneficiary') || { value: '' }).value;
        allocationNoteText.value = 'sponsorship=' + kind + (ben ? (';beneficiary_id=' + ben) : '');
      } else if (v === 'sadaqa_jariya') {
        allocationNoteText.value = 'sponsorship=sadaqa_jariya';
      } else {
        // Clear allocation note if not sponsorship or sadaqa_jariya
        if (allocationNoteText.value.includes('sponsorship=')) {
          allocationNoteText.value = '';
        }
      }
    }
    var sk = document.getElementById('sponsorshipKind'); if (sk) { sk.addEventListener('change', function () { updateAllocationNote(); }); }
    var sben = document.getElementById('sponsorshipBeneficiary'); if (sben) { sben.addEventListener('change', updateAllocationNote); }
  </script>
@endsection

