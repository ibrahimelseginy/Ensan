@extends('layouts.app')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>.select2-container--bootstrap-5 .select2-selection--multiple { min-height: 48px; }</style>
@endsection

@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-person-heart text-primary"></i>
      تعديل المتبرع
    </h4>
    <a href="{{ route('donors.show', $donor) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('donors.update', $donor) }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold"><i class="bi bi-lock-fill text-muted me-1"></i>كود المتبرع</label>
            <input class="form-control font-monospace bg-body-tertiary" dir="ltr"
              value="{{ $donor->code }}" readonly aria-readonly="true">
            <div class="form-text">كود ثابت لا يمكن تغييره بعد إنشاء المتبرع.</div>
          </div>
          <div class="col-md-4">
            <label class="form-label">الاسم</label>
            <input name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $donor->name) }}">
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">النوع</label>
            <select name="type" class="form-select">
              <option value="individual" @selected($donor->type === 'individual')>فرد</option>
              <option value="organization" @selected($donor->type === 'organization')>منظمة</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input type="tel" pattern="^(01[0125][0-9]{8})$" title="رقم هاتف مصري يبدأ بـ 010, 011, 012, 015"
              inputmode="numeric" name="phone" class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone', $donor->phone) }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="col-12">
            <label class="form-label">العنوان</label>
            <input name="address" class="form-control" value="{{ $donor->address }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">تصنيف</label>
            <select name="classification" id="edClassification" class="form-select">
              <option value="one_time" @selected(old('classification', $donor->classification) === 'one_time')>مرة واحدة</option>
              <option value="recurring" @selected(old('classification', $donor->classification) === 'recurring')>متكرر</option>
            </select>
          </div>
          <div class="col-md-6" id="edRecurringCycleField">
            <label class="form-label">دورة التكرار</label>
            <select name="recurring_cycle" id="edRecurringCycle" class="form-select @error('recurring_cycle') is-invalid @enderror">
              <option value="">—</option>
              <option value="monthly" @selected(old('recurring_cycle', $donor->recurring_cycle) === 'monthly')>شهري</option>
              <option value="yearly" @selected(old('recurring_cycle', $donor->recurring_cycle) === 'yearly')>سنوي</option>
            </select>
            @error('recurring_cycle')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-6" id="edMonthlyDonationDayField">
            <label class="form-label fw-bold">يوم التبرع الشهري</label>
            <input name="monthly_donation_day" type="number" min="1" max="31"
              class="form-control @error('monthly_donation_day') is-invalid @enderror"
              value="{{ old('monthly_donation_day', $donor->monthly_donation_day ?? 1) }}">
            <div class="form-text">سيظهر التنبيه للموظف في هذا اليوم.</div>
            @error('monthly_donation_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="active" value="1" @checked($donor->active)>
              <label class="form-check-label">نشط</label>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">التبرع لـ</label>
            <select name="allocation_type" id="edAllocType" class="form-select">
              <option value="">—</option>
              <option value="project" @selected($donor->allocation_type === 'project')>مشروع</option>
              <option value="guest_house" @selected($donor->allocation_type === 'guest_house')>دار الضيافة</option>
              <option value="campaign" @selected($donor->allocation_type === 'campaign')>حملة</option>
              <option value="sponsorship" @selected($donor->allocation_type === 'sponsorship')>كفالة</option>
              <option value="sadaqa_jariya" @selected($donor->allocation_type === 'sadaqa_jariya')>صدقة جارية</option>
            </select>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-project" style="display:none">
            <label class="form-label">المشروع</label>
            <select name="sponsorship_project_id" class="form-select">
              <option value="">—</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected($donor->sponsorship_project_id == $p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-guest_house" style="display:none">
            <label class="form-label">دار الضيافة</label>
            <select name="guest_house_id" class="form-select">
              <option value="">—</option>
              @foreach($guestHouses as $gh)
                <option value="{{ $gh->id }}" @selected($donor->guest_house_id == $gh->id)>
                  {{ $gh->name }}{{ $gh->location ? (' - ' . $gh->location) : '' }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-campaign" style="display:none">
            <label class="form-label">الحملة</label>
            <select name="campaign_id" class="form-select">
              <option value="">—</option>
              @foreach($campaigns as $c)
                <option value="{{ $c->id }}" @selected($donor->campaign_id == $c->id)>{{ $c->name }} ({{ $c->season_year }})
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-sponsorship" style="display:none">
            <label class="form-label">نوع الكفالة</label>
            <select id="edSponsorshipKind" class="form-select">
              <option value="none">—</option>
              <option value="monthly_sponsor" @selected($donor->sponsorship_type === 'monthly_sponsor')>كفالة شهرية</option>
            </select>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-sponsorship" style="display:none">
            <label class="form-label">المشروع</label>
            <select name="sponsorship_project_id" class="form-select">
              <option value="">—</option>
              @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected($donor->sponsorship_project_id == $p->id)>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <input type="hidden" name="sync_sponsored_beneficiaries" value="1">
          <input type="hidden" name="sync_sponsored_family_members" value="1">
          <div class="col-12 ed-alloc ed-alloc-sponsorship" id="edSponsorshipBeneficiaryBox" style="display:none">
            <label class="form-label fw-bold">الأطفال / الحالات المكفولة</label>
            @php
              $selectedSponsoredBeneficiaries = array_map(
                'intval',
                old('sponsored_family_member_ids', $donor->sponsoredFamilyMembers->modelKeys())
              );
            @endphp
            <select name="sponsored_family_member_ids[]" id="edSponsorshipBeneficiaries" class="form-select"
              data-placeholder="ابحث باسم الطفل أو كود الحالة..." multiple>
              @foreach($familyMembers as $member)
                <option value="{{ $member->id }}" @selected(in_array((int) $member->id, $selectedSponsoredBeneficiaries, true))>
                  {{ $member->full_name }} — {{ $member->relationship_label }} — أسرة {{ $member->beneficiary->full_name }} — {{ $member->code }}
                </option>
              @endforeach
            </select>
            <div class="form-text">يمكن اختيار أي عدد من الأطفال أو الحالات بدون حد أقصى.</div>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-sponsorship" style="display:none">
            <label class="form-label">مبلغ الكفالة الشهري</label>
            <input name="sponsorship_monthly_amount" class="form-control" value="{{ $donor->sponsorship_monthly_amount }}"
              placeholder="مثال: 500.00">
          </div>
          <div class="col-12 ed-alloc ed-alloc-sponsorship" style="display:none">
            <label class="form-label fw-bold">إذا لم تحدد طفلًا، اكتب التبرع الشهري موجهًا لإيه</label>
            <textarea name="monthly_allocation_target" rows="2"
              class="form-control @error('monthly_allocation_target') is-invalid @enderror"
              placeholder="مثال: شهري لدار الضيافة، شهري للعلاج، أو مساعدة الحالة كود BEN-000105">{{ old('monthly_allocation_target', $donor->monthly_allocation_target) }}</textarea>
            <div class="form-text">يكفي اختيار طفل/حالة أو كتابة وجهة التبرع، ويمكن استخدام الاثنين معًا.</div>
            @error('monthly_allocation_target')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <input type="hidden" name="sponsorship_type" id="edSponsorshipTypeHidden"
            value="{{ $donor->sponsorship_type ?? 'none' }}">
        </div>
        <div class="mt-3">
          <button class="btn btn-primary">حفظ</button>
          <a href="{{ route('donors.show', $donor) }}" class="btn btn-light">رجوع</a>
        </div>
      </form>
    </div>
    <script>
      window.addEventListener('donor-edit-ready', function () {
        var at = document.getElementById('edAllocType');
        var sk = document.getElementById('edSponsorshipKind');
        var sth = document.getElementById('edSponsorshipTypeHidden');
        var classification = document.getElementById('edClassification');
        var cycle = document.getElementById('edRecurringCycle');
        var cycleField = document.getElementById('edRecurringCycleField');
        var monthlyDayField = document.getElementById('edMonthlyDonationDayField');

        function updateRecurring() {
          var recurring = classification && classification.value === 'recurring';
          var monthly = recurring && cycle && cycle.value === 'monthly';
          if (cycleField) { cycleField.style.display = recurring ? 'block' : 'none'; }
          if (monthlyDayField) { monthlyDayField.style.display = monthly ? 'block' : 'none'; }
          if (cycle) { cycle.required = recurring; }
          if (monthly && at && sk) {
            at.value = 'sponsorship';
            sk.value = 'monthly_sponsor';
            if (sth) { sth.value = 'monthly_sponsor'; }
          }
          toggle();
        }

        function toggle() {
          document.querySelectorAll('.ed-alloc').forEach(function (e) { e.style.display = 'none'; });
          var v = at ? at.value : '';
          if (!v) { return; }
          var boxKey = v === 'sadaqa_jariya' ? 'project' : v;
          if (v === 'sponsorship') {
            document.querySelectorAll('.ed-alloc-sponsorship').forEach(function (e) { e.style.display = 'block'; });
            if (sth && sk) { sth.value = sk.value; }
            var benBox = document.getElementById('edSponsorshipBeneficiaryBox');
            if (benBox && sk) { benBox.style.display = sk.value === 'monthly_sponsor' ? 'block' : 'none'; }
            return;
          }
          var box = document.querySelector('.ed-alloc-' + boxKey);
          if (box) { box.style.display = 'block'; }
          if (sth) { sth.value = (v === 'sadaqa_jariya') ? 'sadaqa_jariya' : 'none'; }
        }
        if (at) { at.addEventListener('change', toggle); }
        if (sk) { sk.addEventListener('change', function () { var benBox = document.getElementById('edSponsorshipBeneficiaryBox'); if (sth) { sth.value = sk.value; } if (benBox) { benBox.style.display = sk.value === 'monthly_sponsor' ? 'block' : 'none'; } }); }
        if (classification) { classification.addEventListener('change', updateRecurring); }
        if (cycle) { cycle.addEventListener('change', updateRecurring); }

        // اضبط القيمة الافتراضية إذا لم تكن محددة مسبقاً
        if (at && !at.value) {
          var initialType = (sth && sth.value) || 'none';
          if (initialType === 'sadaqa_jariya') { at.value = 'sadaqa_jariya'; }
          else if (initialType === 'monthly_sponsor') { at.value = 'sponsorship'; }
        }
        updateRecurring();
      });
    </script>

@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(function () {
      $('#edSponsorshipBeneficiaries').select2({
        theme: 'bootstrap-5', width: '100%', dir: 'rtl',
        placeholder: 'ابحث باسم الطفل أو كود الحالة...', closeOnSelect: false
      });
      window.dispatchEvent(new Event('donor-edit-ready'));
    });
  </script>
@endsection

