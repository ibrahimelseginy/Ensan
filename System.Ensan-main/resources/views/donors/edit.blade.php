@extends('layouts.app')
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
          <div class="col-md-6">
            <label class="form-label">الاسم</label>
            <input name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $donor->name) }}">
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
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
            <select name="classification" class="form-select">
              <option value="one_time" @selected($donor->classification === 'one_time')>مرة واحدة</option>
              <option value="recurring" @selected($donor->classification === 'recurring')>متكرر</option>
            </select>
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
              <option value="yearly_sponsor" @selected($donor->sponsorship_type === 'yearly_sponsor')>كفالة سنوية</option>
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
          <div class="col-md-6 ed-alloc ed-alloc-sponsorship" id="edSponsorshipBeneficiaryBox" style="display:none">
            <label class="form-label">الطفل/الأسرة المكفولة</label>
            <select name="sponsored_beneficiary_id" id="edSponsorshipBeneficiary" class="form-select">
              <option value="">—</option>
              @foreach($beneficiaries as $b)
                <option value="{{ $b->id }}" @selected($donor->sponsored_beneficiary_id == $b->id)>{{ $b->full_name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-sponsorship" style="display:none">
            <label class="form-label">مبلغ الكفالة الشهري</label>
            <input name="sponsorship_monthly_amount" class="form-control" value="{{ $donor->sponsorship_monthly_amount }}"
              placeholder="مثال: 500.00">
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
      (function () {
        var at = document.getElementById('edAllocType');
        var sk = document.getElementById('edSponsorshipKind');
        var sth = document.getElementById('edSponsorshipTypeHidden');
        function toggle() {
          document.querySelectorAll('.ed-alloc').forEach(function (e) { e.style.display = 'none'; });
          var v = at ? at.value : '';
          if (!v) { return; }
          var boxKey = v === 'sadaqa_jariya' ? 'project' : v;
          if (v === 'sponsorship') {
            document.querySelectorAll('.ed-alloc-sponsorship').forEach(function (e) { e.style.display = 'block'; });
            if (sth && sk) { sth.value = sk.value; }
            var benBox = document.getElementById('edSponsorshipBeneficiaryBox');
            if (benBox && sk) { benBox.style.display = (sk.value === 'monthly_sponsor' || sk.value === 'yearly_sponsor') ? 'block' : 'none'; }
            return;
          }
          var box = document.querySelector('.ed-alloc-' + boxKey);
          if (box) { box.style.display = 'block'; }
          if (sth) { sth.value = (v === 'sadaqa_jariya') ? 'sadaqa_jariya' : 'none'; }
        }
        if (at) { at.addEventListener('change', toggle); }
        if (sk) { sk.addEventListener('change', function () { var benBox = document.getElementById('edSponsorshipBeneficiaryBox'); if (sth) { sth.value = sk.value; } if (benBox) { benBox.style.display = (sk.value === 'monthly_sponsor' || sk.value === 'yearly_sponsor') ? 'block' : 'none'; } }); }

        // اضبط القيمة الافتراضية إذا لم تكن محددة مسبقاً
        if (at && !at.value) {
          var initialType = (sth && sth.value) || 'none';
          if (initialType === 'sadaqa_jariya') { at.value = 'sadaqa_jariya'; }
          else if (initialType === 'monthly_sponsor' || initialType === 'yearly_sponsor') { at.value = 'sponsorship'; }
        }
        toggle();
      })();
    </script>

@endsection

