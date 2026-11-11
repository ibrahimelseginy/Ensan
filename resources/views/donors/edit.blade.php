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
            <label class="form-label fw-bold">الاسم بالكامل (ثنائي أو ثلاثي) <span class="text-danger">*</span></label>
            <input name="name" class="form-control @error('name') is-invalid @enderror"
              value="{{ old('name', $donor->name) }}"
              placeholder="مثال: محمد أحمد"
              pattern="^(\S+\s+){1,}\S+.*$"
              title="يجب إدخال اسم ثنائي أو ثلاثي على الأقل (كلمتين أو أكثر مفصولة بمسافات)"
              required>
            <div class="form-text">يمكن إدخال الاسم ثنائياً أو ثلاثياً بشرط أن يكون مميزاً غير مكرر.</div>
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
            <input type="tel" inputmode="numeric" dir="ltr" name="phone" class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone', $donor->phone) }}">
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">الرقم القومي (اختياري)</label>
            <input type="text" maxlength="14" pattern="^[0-9]{14}$" title="الرقم القومي 14 رقم"
              inputmode="numeric" name="national_id" class="form-control font-monospace @error('national_id') is-invalid @enderror"
              value="{{ old('national_id', $donor->national_id) }}" placeholder="14 رقم">
            @error('national_id')
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
              <option value="sponsorship" @selected($donor->allocation_type === 'sponsorship' || (!$donor->allocation_type && ($donor->sponsorship_monthly_amount > 0 || $donor->sponsoredBeneficiaries->isNotEmpty() || $donor->sponsoredFamilyMembers->isNotEmpty())))>كفالة</option>
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
              <option value="monthly_sponsor" @selected($donor->sponsorship_type === 'monthly_sponsor' || (!$donor->sponsorship_type && ($donor->sponsorship_monthly_amount > 0 || $donor->sponsoredBeneficiaries->isNotEmpty() || $donor->sponsoredFamilyMembers->isNotEmpty())))>كفالة شهرية</option>
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
            @php
              $selectedSponsoredBeneficiaries = array_map(
                'intval',
                old('sponsored_family_member_ids', $donor->sponsoredFamilyMembers->modelKeys())
              );
              $familyMemberParentIds = $donor->sponsoredFamilyMembers->pluck('beneficiary_id')->filter()->unique()->all();
              $directSponsoredBeneficiaryIds = $donor->sponsoredBeneficiaries
                ->filter(fn ($sb) => !in_array($sb->id, $familyMemberParentIds, true) || ((float)($sb->pivot?->monthly_amount ?? 0) > 0))
                ->pluck('id')
                ->all();
              $selectedDirectBeneficiaries = array_map(
                'intval',
                old('sponsored_beneficiary_ids', $directSponsoredBeneficiaryIds)
              );
              $existingMemberAmounts = [];
              foreach ($donor->sponsoredFamilyMembers as $fm) {
                if ((float) ($fm->pivot?->monthly_amount ?? 0) > 0) {
                  $existingMemberAmounts[$fm->id] = (float) $fm->pivot->monthly_amount;
                }
              }
              $existingBenAmounts = [];
              foreach ($donor->sponsoredBeneficiaries as $sb) {
                if ((float) ($sb->pivot?->monthly_amount ?? 0) > 0) {
                  $existingBenAmounts[$sb->id] = (float) $sb->pivot->monthly_amount;
                }
              }
            @endphp
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">الأطفال / أفراد الأسر المكفولون</label>
                <select name="sponsored_family_member_ids[]" id="edSponsorshipBeneficiaries" class="form-select"
                  data-placeholder="ابحث باسم الطفل أو كود الحالة..." multiple>
                  @foreach($familyMembers as $member)
                    <option value="{{ $member->id }}"
                      data-default-amount="{{ (float) ($member->sponsorship_amount ?? 0) }}"
                      data-label="{{ $member->full_name }} ({{ $member->code }})"
                      @selected(in_array((int) $member->id, $selectedSponsoredBeneficiaries, true))>
                      {{ $member->full_name }} — {{ $member->relationship_label }} — أسرة {{ $member->beneficiary?->full_name ?? 'الأسرة' }} — {{ $member->code }} @if($member->sponsorship_amount > 0) (كفالة مطلوبة: {{ number_format((float)$member->sponsorship_amount, 0) }} ج) @endif
                    </option>
                  @endforeach
                </select>
                <div class="form-text">اختر طفلاً أو أكثر لتخصيص مبلغ كفالة مستقل لكل طفل.</div>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">أو ملفات الأسر / الحالات الكاملة المكفولة</label>
                <select name="sponsored_beneficiary_ids[]" id="edSponsorshipFullFamilies" class="form-select"
                  data-placeholder="ابحث باسم ولي الأمر أو كود الأسرة..." multiple>
                  @foreach($beneficiaries as $ben)
                    <option value="{{ $ben->id }}"
                      data-default-amount="{{ (float) ($ben->monthly_sponsorship_amount ?? 0) }}"
                      data-label="{{ $ben->full_name }} ({{ $ben->code }})"
                      @selected(in_array((int) $ben->id, $selectedDirectBeneficiaries, true))>
                      {{ $ben->full_name }} — {{ $ben->code }} @if($ben->monthly_sponsorship_amount > 0) (كفالة مطلوبة: {{ number_format((float)$ben->monthly_sponsorship_amount, 0) }} ج) @endif
                    </option>
                  @endforeach
                </select>
                <div class="form-text">اختر أسرة أو أكثر لتخصيص مبلغ الكفالة لكل حالة على حدة.</div>
              </div>
              <div class="col-12" id="edCaseAmountsBreakdownWrap" style="display:none;">
                <div class="p-3 rounded-3 bg-light border border-success-subtle">
                  <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                    <span class="fw-bold text-success small"><i class="bi bi-calculator me-1"></i>توزيع مبالغ الكفالة على الحالات المختارة (حدد المبلغ الرايح لصالح كل حالة):</span>
                    <div class="d-flex align-items-center gap-1">
                      <span class="badge bg-success-subtle text-success border border-success font-monospace" id="edCaseAmountsAutoTotalBadge">الإجمالي المحسوب: 0 ج.م</span>
                      <button type="button" class="btn btn-sm btn-outline-success py-0 px-2 small" id="edBtnSyncCalculatedTotal" title="نسخ هذا المجموع إلى إجمالي المبلغ الشهري">
                        <i class="bi bi-arrow-down-left-square me-1"></i>تطبيق المجموع
                      </button>
                    </div>
                  </div>
                  <div id="edCaseAmountsBreakdownRows" class="row g-2"></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 ed-alloc ed-alloc-sponsorship" style="display:none">
            <label class="form-label fw-bold">إجمالي مبلغ الكفالة الشهري (يُحسب تلقائياً من مجموع الحالات)</label>
            <input name="sponsorship_monthly_amount" id="edSponsorshipMonthlyAmountInput" type="number" step="0.01" min="0" class="form-control fw-bold font-monospace" value="{{ $donor->sponsorship_monthly_amount }}"
              placeholder="مثال: 800.00">
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
            value="{{ ($donor->sponsorship_type && $donor->sponsorship_type !== 'none') ? $donor->sponsorship_type : (($donor->sponsorship_monthly_amount > 0 || $donor->sponsoredBeneficiaries->isNotEmpty() || $donor->sponsoredFamilyMembers->isNotEmpty()) ? 'monthly_sponsor' : 'none') }}">
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
      $('#edSponsorshipBeneficiaries, #edSponsorshipFullFamilies').select2({
        theme: 'bootstrap-5', width: '100%', dir: 'rtl',
        closeOnSelect: false
      });

      const savedMemberAmounts = @json(old('sponsorship_family_member_amounts', $existingMemberAmounts ?? []));
      const savedBenAmounts = @json(old('sponsorship_beneficiary_amounts', $existingBenAmounts ?? []));

      function renderEdCaseAmountsInputs() {
        const container = $('#edCaseAmountsBreakdownRows');
        const wrap = $('#edCaseAmountsBreakdownWrap');
        container.find('input[data-case-key]').each(function () {
          const k = $(this).attr('data-case-key');
          const v = $(this).val();
          if (k.startsWith('fm_')) {
            savedMemberAmounts[k.replace('fm_', '')] = v;
          } else if (k.startsWith('ben_')) {
            savedBenAmounts[k.replace('ben_', '')] = v;
          }
        });

        container.empty();
        let count = 0;

        $('#edSponsorshipBeneficiaries option:selected').each(function () {
          count++;
          const id = $(this).val();
          const label = $(this).attr('data-label') || $(this).text().trim();
          const defAmt = parseFloat($(this).attr('data-default-amount') || '0');
          const curVal = (savedMemberAmounts[id] !== undefined && savedMemberAmounts[id] !== null && savedMemberAmounts[id] !== '')
            ? savedMemberAmounts[id]
            : (defAmt > 0 ? defAmt : '');
          container.append(`
            <div class="col-md-6">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white fw-bold text-truncate" style="max-width: 62%;" title="${label}">👶 ${label}</span>
                <input type="number" step="0.01" min="0" name="sponsorship_family_member_amounts[${id}]"
                  data-case-key="fm_${id}" class="form-control font-monospace ed-case-split-amount-input"
                  placeholder="مبلغ الكفالة (مثال: 500)" value="${curVal}">
                <span class="input-group-text">ج.م</span>
              </div>
            </div>
          `);
        });

        $('#edSponsorshipFullFamilies option:selected').each(function () {
          count++;
          const id = $(this).val();
          const label = $(this).attr('data-label') || $(this).text().trim();
          const defAmt = parseFloat($(this).attr('data-default-amount') || '0');
          const curVal = (savedBenAmounts[id] !== undefined && savedBenAmounts[id] !== null && savedBenAmounts[id] !== '')
            ? savedBenAmounts[id]
            : (defAmt > 0 ? defAmt : '');
          container.append(`
            <div class="col-md-6">
              <div class="input-group input-group-sm">
                <span class="input-group-text bg-white fw-bold text-truncate" style="max-width: 62%;" title="${label}">🏠 ${label}</span>
                <input type="number" step="0.01" min="0" name="sponsorship_beneficiary_amounts[${id}]"
                  data-case-key="ben_${id}" class="form-control font-monospace ed-case-split-amount-input"
                  placeholder="مبلغ الكفالة (مثال: 300)" value="${curVal}">
                <span class="input-group-text">ج.م</span>
              </div>
            </div>
          `);
        });

        wrap.toggle(count > 0);
        recalcEdTotalCaseAmounts();
      }

      let edUserManuallyEditedTotal = false;
      if (parseFloat($('#edSponsorshipMonthlyAmountInput').val() || '0') > 0) {
        edUserManuallyEditedTotal = true;
      }

      $('#edSponsorshipMonthlyAmountInput').on('input change', function () {
        edUserManuallyEditedTotal = true;
      });

      function getEdCalculatedCaseSum() {
        let sum = 0;
        $('.ed-case-split-amount-input').each(function () {
          const v = parseFloat($(this).val() || '0');
          if (!isNaN(v) && v > 0) sum += v;
        });
        return sum;
      }

      function recalcEdTotalCaseAmounts() {
        const sum = getEdCalculatedCaseSum();
        $('#edCaseAmountsAutoTotalBadge').text('الإجمالي المحسوب: ' + sum.toFixed(2) + ' ج.م');
        
        const currentVal = $('#edSponsorshipMonthlyAmountInput').val().trim();
        if (!edUserManuallyEditedTotal && currentVal === '' && sum > 0) {
          $('#edSponsorshipMonthlyAmountInput').val(sum.toFixed(2));
        }
      }

      $('#edBtnSyncCalculatedTotal').on('click', function () {
        const sum = getEdCalculatedCaseSum();
        if (sum > 0) {
          $('#edSponsorshipMonthlyAmountInput').val(sum.toFixed(2));
          edUserManuallyEditedTotal = true;
        }
      });

      $(document).on('input', '.ed-case-split-amount-input', recalcEdTotalCaseAmounts);
      $('#edSponsorshipBeneficiaries, #edSponsorshipFullFamilies').on('change', renderEdCaseAmountsInputs);
      renderEdCaseAmountsInputs();
      window.dispatchEvent(new Event('donor-edit-ready'));
    });
  </script>
@endsection

