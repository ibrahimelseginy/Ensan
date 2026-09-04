@extends('layouts.app')

@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    .monthly-support-card { border: 1px solid rgba(5, 150, 105, .25); background: rgba(5, 150, 105, .04); border-radius: 12px; }
    .select2-container--bootstrap-5 .select2-selection--multiple { min-height: 48px; }
  </style>
@endsection

@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-person-heart text-primary"></i>
      إضافة متبرع جديد
    </h4>
    <a href="{{ route('donors.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('donors.store') }}">
        @csrf
        @if(request('return_to'))
          <input type="hidden" name="return_to" value="{{ request('return_to') }}">
        @endif
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">كود المتبرع <span class="badge bg-primary-subtle text-primary">ثابت</span></label>
            <input name="code" dir="ltr" class="form-control font-monospace @error('code') is-invalid @enderror" value="{{ old('code') }}"
              placeholder="يُنشأ تلقائيًا مثل DON-000013">
            <div class="form-text">يمكنك كتابة الكود أو تركه فارغًا لإنشائه تلقائيًا. بعد الحفظ لن يمكن تغييره.</div>
            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">الاسم بالكامل</label>
            <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-4">
            <label class="form-label">النوع</label>
            <select name="type" class="form-select" required>
              <option value="individual">فرد</option>
              <option value="organization">منظمة</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input type="tel" pattern="^(01[0125][0-9]{8})$" title="رقم هاتف مصري يبدأ بـ 010, 011, 012, 015"
              inputmode="numeric" name="phone" class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone') }}" required>
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">العنوان</label>
            <input name="address" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">تصنيف المتبرع</label>
            <select name="classification" class="form-select" id="donorClassification" required>
              <option value="one_time" @selected(old('classification', 'one_time') === 'one_time')>مرة واحدة</option>
              <option value="recurring" @selected(old('classification') === 'recurring')>متكرر (شهري/سنوي)</option>
            </select>
          </div>
          <div class="col-md-6" id="recurringCycleField">
            <label class="form-label">دورة التكرار</label>
            <select name="recurring_cycle" id="recurringCycle" class="form-select @error('recurring_cycle') is-invalid @enderror">
              <option value="">—</option>
              <option value="monthly" @selected(old('recurring_cycle') === 'monthly')>شهري</option>
              <option value="yearly" @selected(old('recurring_cycle') === 'yearly')>سنوي</option>
            </select>
            @error('recurring_cycle')<div class="invalid-feedback">{{ $message }}</div>@enderror
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="active" value="1" checked>
              <label class="form-check-label fw-bold">حالة المتبرع: نشط</label>
            </div>
          </div>

          <input type="hidden" name="sponsorship_type" id="sponsorshipType" value="{{ old('sponsorship_type', 'none') }}">
          <input type="hidden" name="sync_sponsored_beneficiaries" value="1">
          <input type="hidden" name="sync_sponsored_family_members" value="1">

          <div class="col-12" id="monthlySupportSection" style="display:none">
            <div class="monthly-support-card p-3 p-md-4">
              <h6 class="fw-bold text-primary mb-3"><i class="bi bi-calendar-heart me-1"></i> تخصيص التبرع الشهري</h6>
              <div class="row g-3">
                <div class="col-12">
                  <label class="form-label fw-bold">إضافة طفل أو حالة مكفولة</label>
                  <select name="sponsored_family_member_ids[]" id="sponsoredBeneficiaries"
                    class="form-select @error('sponsored_family_member_ids') is-invalid @enderror"
                    data-placeholder="ابحث باسم الطفل أو كود الحالة..." multiple>
                    @foreach($familyMembers as $member)
                      <option value="{{ $member->id }}" @selected(in_array((int) $member->id, array_map('intval', old('sponsored_family_member_ids', [])), true))>
                        {{ $member->full_name }} — {{ $member->relationship_label }} — أسرة {{ $member->beneficiary->full_name }} — {{ $member->code }}
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">يمكن اختيار أي عدد من الأطفال أو الحالات بدون حد أقصى.</div>
                  @error('sponsored_family_member_ids.*')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">إجمالي المبلغ الشهري</label>
                  <input name="sponsorship_monthly_amount" type="number" min="0" step="0.01"
                    class="form-control @error('sponsorship_monthly_amount') is-invalid @enderror"
                    value="{{ old('sponsorship_monthly_amount') }}" placeholder="مثال: 5000.00">
                  @error('sponsorship_monthly_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">يوم التبرع الشهري</label>
                  <input name="monthly_donation_day" type="number" min="1" max="31"
                    class="form-control @error('monthly_donation_day') is-invalid @enderror"
                    value="{{ old('monthly_donation_day', 1) }}" placeholder="من 1 إلى 31">
                  <div class="form-text">سيظهر التنبيه في هذا اليوم، أو آخر يوم بالشهر إذا كان أقصر.</div>
                  @error('monthly_donation_day')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-4">
                  <label class="form-label">المشروع المرتبط (اختياري)</label>
                  <select name="sponsorship_project_id" class="form-select">
                    <option value="">— بدون مشروع محدد —</option>
                    @foreach($projects as $p)
                      <option value="{{ $p->id }}" @selected((string) old('sponsorship_project_id') === (string) $p->id)>{{ $p->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="col-12">
                  <label class="form-label fw-bold">إذا لم تحدد طفلًا، اكتب التبرع الشهري موجهًا لإيه</label>
                  <textarea name="monthly_allocation_target" rows="2"
                    class="form-control @error('monthly_allocation_target') is-invalid @enderror"
                    placeholder="مثال: شهري لدار الضيافة، شهري للعلاج، مساعدة الحالة كود BEN-000105، أو أي توجيه آخر">{{ old('monthly_allocation_target') }}</textarea>
                  <div class="form-text">يكفي اختيار طفل/حالة أو كتابة وجهة التبرع، ويمكن استخدام الاثنين معًا.</div>
                  @error('monthly_allocation_target')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="mt-4">
          <button class="btn btn-primary px-4 fw-bold"><i class="bi bi-check-lg me-1"></i> حفظ المتبرع</button>
          <a href="{{ route('donors.index') }}" class="btn btn-light me-2">رجوع</a>
        </div>
      </form>
    </div>
@endsection

@section('scripts')
  <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(function () {
      const classification = $('#donorClassification');
      const cycle = $('#recurringCycle');
      const cycleField = $('#recurringCycleField');
      const monthlySection = $('#monthlySupportSection');
      const sponsorshipType = $('#sponsorshipType');

      $('#sponsoredBeneficiaries').select2({
        theme: 'bootstrap-5', width: '100%', dir: 'rtl',
        placeholder: 'ابحث باسم الطفل أو كود الحالة...', closeOnSelect: false
      });

      function updateRecurringFields() {
        const recurring = classification.val() === 'recurring';
        const monthly = recurring && cycle.val() === 'monthly';
        cycleField.toggle(recurring);
        cycle.prop('required', recurring);
        monthlySection.toggle(monthly);
        sponsorshipType.val(monthly ? 'monthly_sponsor' : 'none');
      }

      classification.on('change', updateRecurringFields);
      cycle.on('change', updateRecurringFields);
      updateRecurringFields();
    });
  </script>
@endsection

