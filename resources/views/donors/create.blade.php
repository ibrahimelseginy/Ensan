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
            <label class="form-label fw-bold">الاسم بالكامل (ثنائي أو ثلاثي) <span class="text-danger">*</span></label>
            <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
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
            <select name="type" class="form-select" required>
              <option value="individual">فرد</option>
              <option value="organization">منظمة</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input type="tel" inputmode="numeric" dir="ltr" name="phone" class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone') }}" required>
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">الرقم القومي (اختياري)</label>
            <input type="text" maxlength="14" pattern="^[0-9]{14}$" title="الرقم القومي 14 رقم"
              inputmode="numeric" name="national_id" class="form-control font-monospace @error('national_id') is-invalid @enderror"
              value="{{ old('national_id') }}" placeholder="14 رقم">
            @error('national_id')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-12">
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
                <div class="col-md-6">
                  <label class="form-label fw-bold">إضافة طفل / فرد أسرة مكفول</label>
                  <select name="sponsored_family_member_ids[]" id="sponsoredBeneficiaries"
                    class="form-select @error('sponsored_family_member_ids') is-invalid @enderror"
                    data-placeholder="ابحث باسم الطفل أو كود الحالة..." multiple>
                    @foreach($familyMembers as $member)
                      <option value="{{ $member->id }}"
                        data-default-amount="{{ (float) ($member->sponsorship_amount ?? 0) }}"
                        data-label="{{ $member->full_name }} ({{ $member->code }})"
                        @selected(in_array((int) $member->id, array_map('intval', old('sponsored_family_member_ids', [])), true))>
                        {{ $member->full_name }} — {{ $member->relationship_label }} — أسرة {{ $member->beneficiary?->full_name ?? 'الأسرة' }} — {{ $member->code }} @if($member->sponsorship_amount > 0) (كفالة مطلوبة: {{ number_format((float)$member->sponsorship_amount, 0) }} ج) @endif
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">اختر طفلاً أو أكثر لتخصيص مبلغ كفالة مستقل لكل طفل.</div>
                  @error('sponsored_family_member_ids.*')<div class="text-danger small">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                  <label class="form-label fw-bold">أو إضافة ملف أسرة / حالة كاملة مكفولة</label>
                  <select name="sponsored_beneficiary_ids[]" id="sponsoredFullFamilies"
                    class="form-select @error('sponsored_beneficiary_ids') is-invalid @enderror"
                    data-placeholder="ابحث باسم ولي الأمر أو كود الأسرة..." multiple>
                    @foreach($beneficiaries as $ben)
                      <option value="{{ $ben->id }}"
                        data-default-amount="{{ (float) ($ben->monthly_sponsorship_amount ?? 0) }}"
                        data-label="{{ $ben->full_name }} ({{ $ben->code }})"
                        @selected(in_array((int) $ben->id, array_map('intval', old('sponsored_beneficiary_ids', [])), true))>
                        {{ $ben->full_name }} — {{ $ben->code }} @if($ben->monthly_sponsorship_amount > 0) (كفالة مطلوبة: {{ number_format((float)$ben->monthly_sponsorship_amount, 0) }} ج) @endif
                      </option>
                    @endforeach
                  </select>
                  <div class="form-text">اختر أسرة أو أكثر لتخصيص مبلغ الكفالة لكل حالة على حدة.</div>
                </div>
                <div class="col-12" id="caseAmountsBreakdownWrap" style="display:none;">
                  <div class="p-3 rounded-3 bg-white border border-success-subtle shadow-2xs">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <span class="fw-bold text-success small"><i class="bi bi-calculator me-1"></i>توزيع مبالغ الكفالة على الحالات المختارة (حدد المبلغ الرايح لصالح كل حالة):</span>
                      <span class="badge bg-success-subtle text-success border border-success font-monospace" id="caseAmountsAutoTotalBadge">الإجمالي المحسوب: 0 ج.م</span>
                    </div>
                    <div id="caseAmountsBreakdownRows" class="row g-2"></div>
                  </div>
                </div>
                <div class="col-md-4">
                  <label class="form-label fw-bold">إجمالي المبلغ الشهري (يُحسب تلقائياً أو يدوياً)</label>
                  <input name="sponsorship_monthly_amount" id="sponsorshipMonthlyAmountInput" type="number" min="0" step="0.01"
                    class="form-control fw-bold font-monospace @error('sponsorship_monthly_amount') is-invalid @enderror"
                    value="{{ old('sponsorship_monthly_amount') }}" placeholder="مثال: 800.00">
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

      $('#sponsoredBeneficiaries, #sponsoredFullFamilies').select2({
        theme: 'bootstrap-5', width: '100%', dir: 'rtl',
        closeOnSelect: false
      });

      const savedMemberAmounts = @json(old('sponsorship_family_member_amounts', []));
      const savedBenAmounts = @json(old('sponsorship_beneficiary_amounts', []));

      function renderCaseAmountsInputs() {
        const container = $('#caseAmountsBreakdownRows');
        const wrap = $('#caseAmountsBreakdownWrap');
        // Preserve already typed values before re-rendering
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

        $('#sponsoredBeneficiaries option:selected').each(function () {
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
                <span class="input-group-text bg-light fw-bold text-truncate" style="max-width: 62%;" title="${label}">👶 ${label}</span>
                <input type="number" step="0.01" min="0" name="sponsorship_family_member_amounts[${id}]"
                  data-case-key="fm_${id}" class="form-control font-monospace case-split-amount-input"
                  placeholder="مبلغ الكفالة (مثال: 500)" value="${curVal}">
                <span class="input-group-text">ج.م</span>
              </div>
            </div>
          `);
        });

        $('#sponsoredFullFamilies option:selected').each(function () {
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
                <span class="input-group-text bg-light fw-bold text-truncate" style="max-width: 62%;" title="${label}">🏠 ${label}</span>
                <input type="number" step="0.01" min="0" name="sponsorship_beneficiary_amounts[${id}]"
                  data-case-key="ben_${id}" class="form-control font-monospace case-split-amount-input"
                  placeholder="مبلغ الكفالة (مثال: 300)" value="${curVal}">
                <span class="input-group-text">ج.م</span>
              </div>
            </div>
          `);
        });

        wrap.toggle(count > 0);
        recalcTotalCaseAmounts();
      }

      function recalcTotalCaseAmounts() {
        let sum = 0;
        $('.case-split-amount-input').each(function () {
          const v = parseFloat($(this).val() || '0');
          if (!isNaN(v) && v > 0) sum += v;
        });
        $('#caseAmountsAutoTotalBadge').text('الإجمالي المحسوب: ' + sum.toFixed(2) + ' ج.م');
        if (sum > 0) {
          $('#sponsorshipMonthlyAmountInput').val(sum.toFixed(2));
        }
      }

      $(document).on('input', '.case-split-amount-input', recalcTotalCaseAmounts);
      $('#sponsoredBeneficiaries, #sponsoredFullFamilies').on('change', renderCaseAmountsInputs);
      renderCaseAmountsInputs();

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

