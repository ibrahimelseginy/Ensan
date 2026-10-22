@extends('layouts.app')

@section('title', 'استمارة بحث ميداني واجتماعي')

@section('styles')
<style>
  .research-card {
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
    margin-bottom: 1.5rem;
    overflow: hidden;
  }
  .theme-dark .research-card {
    background: #0f172a;
    border-color: rgba(255, 255, 255, 0.1);
  }
  .research-card-header {
    padding: 1rem 1.25rem;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    font-weight: 700;
    font-size: 1.05rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
  }
  .theme-dark .research-card-header {
    background: #1e293b;
    border-color: rgba(255, 255, 255, 0.08);
    color: #f8fafc;
  }
  .research-card-body {
    padding: 1.25rem;
  }
  .official-notice-yellow,
  span.official-notice-yellow,
  label span.official-notice-yellow,
  .form-label span.official-notice-yellow,
  .theme-dark .official-notice-yellow,
  .theme-dark span.official-notice-yellow,
  .theme-dark label span.official-notice-yellow,
  html.theme-dark .official-notice-yellow,
  body.theme-dark .official-notice-yellow,
  [data-bs-theme="dark"] .official-notice-yellow {
    background-color: #fef08a !important;
    color: #0f172a !important;
    -webkit-text-fill-color: #0f172a !important;
    font-weight: 800 !important;
    padding: 0.45rem 0.95rem !important;
    border-radius: 8px !important;
    display: inline-block !important;
    border: 1px solid #fde047 !important;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12) !important;
  }
  .official-notice-green,
  div.official-notice-green,
  .theme-dark .official-notice-green,
  html.theme-dark .official-notice-green,
  body.theme-dark .official-notice-green {
    background-color: #15803d !important;
    color: #ffffff !important;
    -webkit-text-fill-color: #ffffff !important;
    font-weight: 800 !important;
    padding: 0.65rem 1rem !important;
    border-radius: 8px !important;
    text-align: center !important;
    font-size: 0.95rem !important;
  }
  .cancer-toggle-card {
    border: 2px dashed #0ea5e9;
    border-radius: 18px;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.06) 0%, rgba(16, 185, 129, 0.06) 100%);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    text-align: center;
  }
  .theme-dark .cancer-toggle-card {
    border-color: #38bdf8;
    background: linear-gradient(135deg, rgba(14, 165, 233, 0.15) 0%, rgba(16, 185, 129, 0.15) 100%);
  }
  .cancer-radio-btn {
    cursor: pointer;
    border: 2px solid #cbd5e1;
    border-radius: 14px;
    padding: 0.85rem 1.75rem;
    display: inline-flex;
    align-items: center;
    gap: 0.6rem;
    font-size: 1.1rem;
    font-weight: 800;
    transition: all 0.25s ease;
    user-select: none;
    background: #ffffff;
    color: #334155;
  }
  .theme-dark .cancer-radio-btn {
    background: #1e293b;
    border-color: #475569;
    color: #f1f5f9;
  }
  .cancer-radio-btn.active-no {
    border-color: #64748b;
    background: #475569;
    color: #ffffff;
    box-shadow: 0 4px 14px rgba(71, 85, 105, 0.3);
  }
  .cancer-radio-btn.active-yes {
    border-color: #dc2626;
    background: #dc2626;
    color: #ffffff;
    box-shadow: 0 6px 18px rgba(220, 38, 38, 0.35);
  }
  .table-custom-form th {
    background-color: #f1f5f9 !important;
    font-weight: 700;
    font-size: 0.88rem;
    color: #334155;
    text-align: center;
    vertical-align: middle;
  }
  .theme-dark .table-custom-form th {
    background-color: #1e293b !important;
    color: #cbd5e1;
  }
</style>
@endsection

@section('content')
<div class="container-fluid py-3">
  <!-- Page Header -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <div class="d-flex align-items-center gap-2">
        <a href="{{ route('visits.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2" title="رجوع">
          <i class="bi bi-arrow-right fs-6"></i>
        </a>
        <h3 class="mb-0 fw-bolder text-dark">استمارة بحث ميداني واجتماعي</h3>
      </div>
      <p class="text-muted small mb-0 mt-1">مؤسسة إنسان الخيرية — استمارة البحث الاجتماعي وبحث مرضى الأورام</p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-lg me-1"></i> إلغاء
      </a>
      <button type="button" onclick="document.getElementById('researchForm').submit()" class="btn btn-success fw-bold px-4 shadow-sm">
        <i class="bi bi-shield-check me-1"></i> تم الاعتماد من الباحث
      </button>
    </div>
  </div>

  @if($errors->any())
    <div class="alert alert-danger shadow-sm border-0 mb-4">
      <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى مراجعة البيانات التالية:</div>
      <ul class="mb-0 small">
        @foreach($errors->all() as $err)
          <li>{{ $err }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  @php
    $defaultPatientName = old('patient_name', $caseApplication?->applicant_name ?? $selectedBeneficiary?->full_name);
    $defaultGuardianName = old('guardian_name', $caseApplication?->applicant_name ?? $selectedBeneficiary?->full_name);
    $defaultNationalId = old('national_id', $caseApplication?->applicant_id_number ?? $selectedBeneficiary?->national_id);
    $defaultPhone = old('phone', $caseApplication?->applicant_phone ?? $selectedBeneficiary?->phone);
    $defaultAddress = old('address', $caseApplication ? trim(($caseApplication->governorate ? $caseApplication->governorate . ' - ' : '') . ($caseApplication->city ? $caseApplication->city . ' - ' : '') . $caseApplication->address) : $selectedBeneficiary?->address);
    $defaultNeeds = old('case_needs', $caseApplication ? ($caseApplication->case_type_label . ' — ' . $caseApplication->description) : '');
    $defaultGuide = old('case_guide', $caseApplication ? (($caseApplication->submitter_type === 'other') ? ('مقدم من: ' . $caseApplication->submitter_name . ' (' . $caseApplication->submitter_relation . ') - ' . $caseApplication->submitter_phone) : 'تقديم مباشر من الحالة عبر ' . $caseApplication->source) : '');
    $defaultIsCancer = old('has_cancer_patient', $caseApplication ? in_array($caseApplication->case_type, ['hope', 'medical', 'أورام']) : false);
  @endphp

  @if(!empty($caseApplication))
    <input type="hidden" name="application_id" value="{{ $caseApplication->id }}">
    <div class="alert alert-info shadow-sm border-0 mb-4 d-flex align-items-center gap-2">
      <i class="bi bi-link-45deg fs-4 text-info"></i>
      <div>
        <strong>تم استيراد بيانات الحالة من طلب البحث المبدئي رقم #{{ $caseApplication->id }}</strong>: {{ $caseApplication->applicant_name }} ({{ $caseApplication->case_type_label }}). تم نقل البيانات تلقائياً لإكمال الاستمارة الميدانية.
      </div>
    </div>
  @endif

  <form id="researchForm" method="POST" action="{{ route('visits.store') }}" enctype="multipart/form-data">
    @csrf
    @if(!empty($caseApplication))
      <input type="hidden" name="application_id" value="{{ $caseApplication->id }}">
    @endif

    <!-- الأساسيات: المستفيد والباحث وتاريخ البحث -->
    <div class="research-card">
      <div class="research-card-header text-primary">
        <i class="bi bi-person-lines-fill"></i>
        <span>أولاً: بيانات الحالة والباحث الميداني</span>
      </div>
      <div class="research-card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">ربط بملف المستفيد (إن وجد) <span class="text-danger">*</span></label>
            <select name="beneficiary_id" id="beneficiarySelect" class="form-select" required>
              <option value="">-- اختر المستفيد أو الحالة --</option>
              @foreach($beneficiaries as $ben)
                <option value="{{ $ben->id }}"
                  data-name="{{ $ben->full_name ?? $ben->name }}"
                  data-phone="{{ $ben->phone }}"
                  data-national-id="{{ $ben->national_id }}"
                  data-address="{{ $ben->address }}"
                  @selected((string)old('beneficiary_id', $selectedBeneficiary?->id) === (string)$ben->id)>
                  {{ $ben->full_name ?? $ben->name }} ({{ $ben->code ?: 'ID: '.$ben->id }})
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-bold">الباحث الميداني <span class="text-danger">*</span></label>
            <select name="researcher_id" id="researcherSelect" class="form-select" required>
              <option value="">-- اختر الباحث المسؤول --</option>
              @foreach($researchers as $res)
                <option value="{{ $res->id }}"
                  data-phone="{{ $res->phone ?? '' }}"
                  @selected((string)old('researcher_id', $caseApplication?->assigned_researcher_id ?? auth()->id()) === (string)$res->id)>
                  {{ $res->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">تاريخ البحث <span class="text-danger">*</span></label>
            <input type="date" name="visit_date" value="{{ old('visit_date', date('Y-m-d')) }}" class="form-control" required>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">حالة الزيارة</label>
            <select name="status" class="form-select">
              <option value="completed" @selected(old('status', 'completed') === 'completed')>تمت الزيارة والبحث</option>
              <option value="scheduled" @selected(old('status') === 'scheduled')>مجدولة</option>
              <option value="cancelled" @selected(old('status') === 'cancelled')>ملغاة</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">اسم المريض / الحالة كاملاً</label>
            <input type="text" name="patient_name" id="patientNameInput" value="{{ $defaultPatientName }}" class="form-control" placeholder="اسم الحالة محل البحث">
          </div>

          <div class="col-md-4">
            <label class="form-label">اسم ولي الأمر / المعيل</label>
            <input type="text" name="guardian_name" id="guardianNameInput" value="{{ $defaultGuardianName }}" class="form-control" placeholder="ولي الأمر أو رب الأسرة">
          </div>

          <div class="col-md-4">
            <label class="form-label">الرقم القومي (14 رقم)</label>
            <input type="text" name="national_id" id="nationalIdInput" value="{{ $defaultNationalId }}" maxlength="14" class="form-control" placeholder="الرقم القومي">
          </div>

          <div class="col-md-3">
            <label class="form-label">رقم الهاتف</label>
            <input type="text" name="phone" id="phoneInput" value="{{ $defaultPhone }}" class="form-control" placeholder="رقم هاتف الحالة">
          </div>

          <div class="col-md-3">
            <label class="form-label">رقم هاتف الباحث</label>
            <input type="text" name="researcher_phone" id="researcherPhoneInput" value="{{ old('researcher_phone', auth()->user()?->phone) }}" class="form-control" placeholder="هاتف الباحث الميداني">
          </div>

          <div class="col-md-3">
            <label class="form-label">ملكية البيت</label>
            <select name="home_ownership" class="form-select">
              <option value="ملك" @selected(old('home_ownership') === 'ملك')>ملك</option>
              <option value="إيجار" @selected(old('home_ownership') === 'إيجار')>إيجار</option>
              <option value="ورثة" @selected(old('home_ownership') === 'ورثة')>ورثة</option>
              <option value="استضافة" @selected(old('home_ownership') === 'استضافة')>استضافة</option>
              <option value="شركة بيت عيلة" @selected(old('home_ownership') === 'شركة بيت عيلة')>شركة بيت عيلة</option>
              <option value="شركة" @selected(old('home_ownership') === 'شركة')>شركة</option>
              <option value="بيت عيلة" @selected(old('home_ownership') === 'بيت عيلة')>بيت عيلة</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label"><i class="bi bi-camera-fill text-primary me-1"></i> تصوير أو رفع صورة المنزل</label>
            <input type="file" name="house_photo" class="form-control" accept="image/*" capture="environment">
          </div>

          <div class="col-md-3">
            <label class="form-label">توصية الباحث المبدئية</label>
            <select name="recommendation" class="form-select">
              <option value="approve" @selected(old('recommendation', 'approve') === 'approve')>يوصى بالقبول</option>
              <option value="needs_review" @selected(old('recommendation') === 'needs_review')>تحتاج مراجعة وبحث إضافي</option>
              <option value="reject" @selected(old('recommendation') === 'reject')>يوصى بالرفض</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">العنوان بالتفصيل</label>
            <input type="text" name="address" id="addressInput" value="{{ $defaultAddress }}" class="form-control" placeholder="المحافظة - المركز / المدينة - القرية / الشارع التفصيلي">
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">رأي الباحث الميداني :</span>
            </label>
            <textarea name="researcher_opinion" class="form-control" rows="2" placeholder="اكتب رأيك الدقيق وملاحظاتك الميدانية حول مصداقية الحالة وظروفها...">{{ old('researcher_opinion') }}</textarea>
          </div>
        </div>

        <div class="mt-3">
          <div class="official-notice-green d-block">
            ( ملحوظة هامة : يتم تصوير صور البطاقات الشخصية والبيت من الداخل والخارج )
          </div>
        </div>
      </div>
    </div>

    <!-- جدول أفراد الأسرة -->
    <div class="research-card">
      <div class="research-card-header d-flex justify-content-between align-items-center">
        <div>
          <i class="bi bi-people-fill text-success"></i>
          <span>ثانياً: أفراد الأسرة والمقيمين</span>
        </div>
        <button type="button" id="addFamilyRowBtn" class="btn btn-sm btn-outline-success">
          <i class="bi bi-plus-lg me-1"></i> إضافة فرد
        </button>
      </div>
      <div class="research-card-body p-0">
        <div class="table-responsive">
          <table class="table table-bordered table-custom-form table-hover mb-0" id="familyTable">
            <thead>
              <tr>
                <th style="width: 25%;">الاسم</th>
                <th style="width: 15%;">صلة القرابة</th>
                <th style="width: 10%;">السن</th>
                <th style="width: 15%;">الدخل</th>
                <th style="width: 15%;">العمل</th>
                <th style="width: 15%;">ملاحظات</th>
                <th style="width: 5%;">حذف</th>
              </tr>
            </thead>
            <tbody id="familyTableBody">
              @php
                $existingMembers = old('family_members', $selectedBeneficiary?->familyMembers?->toArray() ?? []);
                if (empty($existingMembers)) {
                    $existingMembers = [
                        ['name' => '', 'relation' => 'الزوج / الزوجة', 'age' => '', 'income' => '', 'job' => '', 'notes' => ''],
                        ['name' => '', 'relation' => 'ابن / ابنة', 'age' => '', 'income' => '', 'job' => '', 'notes' => ''],
                    ];
                }
              @endphp
              @foreach($existingMembers as $idx => $member)
                <tr>
                  <td><input type="text" name="family_members[{{ $idx }}][name]" value="{{ $member['name'] ?? $member['full_name'] ?? '' }}" class="form-control form-control-sm" placeholder="الاسم"></td>
                  <td><input type="text" name="family_members[{{ $idx }}][relation]" value="{{ $member['relation'] ?? $member['relationship'] ?? '' }}" class="form-control form-control-sm" placeholder="صلة القرابة"></td>
                  <td><input type="number" name="family_members[{{ $idx }}][age]" value="{{ $member['age'] ?? '' }}" class="form-control form-control-sm" placeholder="السن"></td>
                  <td><input type="text" name="family_members[{{ $idx }}][income]" value="{{ $member['income'] ?? '' }}" class="form-control form-control-sm" placeholder="الدخل"></td>
                  <td><input type="text" name="family_members[{{ $idx }}][job]" value="{{ $member['job'] ?? '' }}" class="form-control form-control-sm" placeholder="العمل"></td>
                  <td><input type="text" name="family_members[{{ $idx }}][notes]" value="{{ $member['notes'] ?? '' }}" class="form-control form-control-sm" placeholder="ملاحظات"></td>
                  <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-family-row p-1"><i class="bi bi-trash"></i></button></td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- مصادر الدخل وبنود الصرف (المصروفات) -->
    <div class="research-card">
      <div class="research-card-header text-info">
        <i class="bi bi-cash-stack"></i>
        <span>ثالثاً: مصادر الدخل وبنود الصرف (المصروفات)</span>
      </div>
      <div class="research-card-body">
        <div class="row g-4">
          <!-- مصادر الدخل -->
          <div class="col-md-6 border-end">
            <h6 class="fw-bold text-success mb-3 pb-2 border-bottom">
              <i class="bi bi-graph-up-arrow me-1"></i> مصادر الدخل (شهرياً بالجنية)
            </h6>
            <div class="row g-2">
              <div class="col-8"><label class="form-label small">وظيفة (في حالة العمل)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_job" id="income_job" value="{{ old('income_job', 0) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">عمل يومي (حرفي / فني / عامل / فاعل)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_daily_work" id="income_daily_work" value="{{ old('income_daily_work', 0) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">معاشات تأمينات (كبار السن / التقاعد)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_pension" id="income_pension" value="{{ old('income_pension', 0) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">الشئون الاجتماعية (طلاق / عجز طبي / ترمل / تكافل)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_social_affairs" id="income_social_affairs" value="{{ old('income_social_affairs', 0) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">الأقارب / الجيران / جمعيات شرعية / خيرية</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_charities" id="income_charities" value="{{ old('income_charities', 0) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-12 mt-2 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-success bg-opacity-10 text-success fw-bold">
                  <span>إجمالي الدخل الشهري:</span>
                  <span id="totalIncomeDisplay" class="fs-5 font-monospace">0.00 ج.م</span>
                </div>
              </div>
            </div>
          </div>

          <!-- بنود الصرف -->
          <div class="col-md-6">
            <h6 class="fw-bold text-danger mb-3 pb-2 border-bottom">
              <i class="bi bi-graph-down-arrow me-1"></i> بنود الصرف / المصروفات (شهرياً بالجنية)
            </h6>
            <div class="row g-2">
              <div class="col-8"><label class="form-label small">إيجار السكن</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_rent" id="expense_rent" value="{{ old('expense_rent', 0) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">مصاريف الدروس والتعليم</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_tuition" id="expense_tuition" value="{{ old('expense_tuition', 0) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">متوسط تكلفة الطعام والشراب</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_food_weekly" id="expense_food_weekly" value="{{ old('expense_food_weekly', 0) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">المواصلات اليومية</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_transportation" id="expense_transportation" value="{{ old('expense_transportation', 0) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">فاتورة الكهرباء / المياه / الغاز</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_utilities" id="expense_utilities" value="{{ old('expense_utilities', 0) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-12 mt-2 pt-2 border-top">
                <div class="d-flex justify-content-between align-items-center p-2 rounded bg-danger bg-opacity-10 text-danger fw-bold">
                  <span>إجمالي المصروفات الشهرية:</span>
                  <span id="totalExpenseDisplay" class="fs-5 font-monospace">0.00 ج.م</span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- جدول الديون ووصف الحالة ووضع البيت -->
    <div class="research-card">
      <div class="research-card-header text-warning">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span>رابعاً: الديون وظروف السكن والحالة الاجتماعية</span>
      </div>
      <div class="research-card-body">
        <div class="row g-3">
          <div class="col-md-3">
            <label class="form-label">إجمالي الديون (ج.م)</label>
            <input type="number" step="0.01" min="0" name="total_debts" id="totalDebtsInput" value="{{ old('total_debts', 0) }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">ما تم سداده (ج.م)</label>
            <input type="number" step="0.01" min="0" name="debt_repaid" id="debtRepaidInput" value="{{ old('debt_repaid', 0) }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">المتبقي (تلقائي)</label>
            <input type="text" id="debtRemainingDisplay" readonly class="form-control bg-light font-monospace fw-bold text-danger" value="0.00">
          </div>
          <div class="col-md-3">
            <label class="form-label">القسط الشهري (ج.م)</label>
            <input type="number" step="0.01" min="0" name="debt_monthly_installment" value="{{ old('debt_monthly_installment', 0) }}" class="form-control">
          </div>

          <div class="col-12">
            <label class="form-label">سبب الدين</label>
            <input type="text" name="debt_reason" value="{{ old('debt_reason') }}" class="form-control" placeholder="علاج، زواج، سداد إيجار، تجهيزات...">
          </div>

          <div class="col-md-6">
            <label class="form-label">دليل الحالة للمؤسسة (كيف عرفت الحالة بالمؤسسة؟)</label>
            <input type="text" name="case_guide" value="{{ $defaultGuide }}" class="form-control" placeholder="جار، متطوع، صفحة الفيس، مسجد...">
          </div>

          <div class="col-md-6">
            <label class="form-label">احتياج الحالة من التقديم</label>
            <input type="text" name="case_needs" value="{{ $defaultNeeds }}" class="form-control" placeholder="سلة غذائية، مساعدة مالية، علاج، سقف، ترميم...">
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">وصف الحالة الاجتماعية للأسرة ، وظروف الأسرة ، ووضع البيت ( محتاج سقف / ترميم / بنى ) :</span>
            </label>
            <textarea name="home_and_social_condition" class="form-control" rows="3" placeholder="وصف كامل لحالة السكن والأسقف، الجدران، الفرش، مستوى المعيشة...">{{ old('home_and_social_condition') }}</textarea>
          </div>

          <div class="col-12 mt-3 pt-3 border-top">
            <label class="form-label fw-bold">
              <span class="official-notice-green d-inline-block px-3 py-1">
                <i class="bi bi-images me-1"></i> رفع عدة صور (صور شيكات، وصولات أمانة، صور البيت، أو أي مستندات تدل على تدهور الحالة) :
              </span>
            </label>
            <input type="file" name="condition_photos[]" class="form-control" accept="image/*" multiple capture="environment">
            <div class="form-text text-muted">يمكنك التقاط أو اختيار عدة صور للشيكات والمنزل وأوجه تدهور الحالة دفعة واحدة وتخزينها في ملف الحالة.</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- السؤال الشرطي التفاعلي: هل يوجد أحد أفراد الأسرة مريض أورام؟ -->
    <!-- ============================================================== -->
    <div class="cancer-toggle-card shadow-sm">
      <h4 class="fw-bolder mb-2 text-dark">
        <i class="bi bi-heart-pulse text-danger me-2"></i> هل يوجد أحد أفراد الأسرة مريض أورام؟
      </h4>
      <p class="text-muted small mb-3">
        إذا اخترت <strong>(لا)</strong> ينتهي البحث الاجتماعي العادي هنا. وإذا اخترت <strong>(نعم)</strong> سيتم فتح باقي استمارة بحث ومتابعة مريض الأورام وتفاصيل العلاج والبروتوكول.
      </p>

      <div class="d-inline-flex gap-3 my-2">
        <label class="cancer-radio-btn {{ $defaultIsCancer ? '' : 'active-no' }}" id="radioBtnNo">
          <input type="radio" name="has_cancer_patient" value="0" class="d-none" @checked(!$defaultIsCancer)>
          <i class="bi bi-x-circle-fill"></i>
          <span>لا (بحث اجتماعي عادي)</span>
        </label>

        <label class="cancer-radio-btn {{ $defaultIsCancer ? 'active-yes' : '' }}" id="radioBtnYes">
          <input type="radio" name="has_cancer_patient" value="1" class="d-none" @checked($defaultIsCancer)>
          <i class="bi bi-check-circle-fill"></i>
          <span>نعم (يوجد مريض أورام)</span>
        </label>
      </div>

      <div id="normalResearchNotice" class="mt-3 text-muted small" style="{{ $defaultIsCancer ? 'display: none;' : '' }}">
        <i class="bi bi-info-circle text-primary me-1"></i> تم تحديد: <strong>بحث اجتماعي عادي</strong>. يمكنك مراجعة البيانات والضغط على حفظ بالأسفل لإنهاء البحث والطباعة.
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- القسم الخاص بمرضى الأورام (يفتح فقط إذا تم اختيار "نعم") -->
    <!-- ============================================================== -->
    <div id="cancerSection" class="research-card border-danger shadow-sm" style="{{ $defaultIsCancer ? '' : 'display: none;' }}">
      <div class="research-card-header bg-danger text-white">
        <i class="bi bi-hospital-fill"></i>
        <span>استمارة بيانات مريض أورام — تفاصيل العلاج والبروتوكول الطبي</span>
      </div>
      <div class="research-card-body">
        <div class="official-notice-green mb-3">
          ( ملحوظة : يتم تصوير كارت المتابعة و جميع التقارير الطبية و الروشتات المذكورة ، و التركيز على احتياجها للسلة الغذائية و الدعم النفسي / الزيارة )
        </div>

        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label fw-bold">اسم المستشفى المتلقى بها العلاج</label>
            <input type="text" name="hospital_name" value="{{ old('hospital_name') }}" class="form-control" placeholder="مثال: مركز أورام كفر الشيخ، معهد الأورام بطنطا...">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">مكان الورم بالجسم</label>
            <input type="text" name="tumor_location" value="{{ old('tumor_location') }}" class="form-control" placeholder="مثال: الثدي، القولون، الرئة، العظام...">
          </div>

          <div class="col-md-3">
            <label class="form-label">تاريخ اكتشاف المرض</label>
            <input type="text" name="disease_discovery_date" value="{{ old('disease_discovery_date') }}" class="form-control" placeholder="مثال: 2024/05">
          </div>

          <div class="col-md-3">
            <label class="form-label">مدة اكتشاف المرض</label>
            <input type="text" name="disease_duration" value="{{ old('disease_duration') }}" class="form-control" placeholder="مثال: سنتين، 6 أشهر...">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الكيماوي المتلقاة</label>
            <input type="number" min="0" name="chemo_sessions_taken" value="{{ old('chemo_sessions_taken', 0) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الكيماوي المتبقية</label>
            <input type="number" min="0" name="chemo_sessions_remaining" value="{{ old('chemo_sessions_remaining', 0) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الإشعاع المتلقاة</label>
            <input type="number" min="0" name="radiation_sessions_taken" value="{{ old('radiation_sessions_taken', 0) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الإشعاع المتبقية</label>
            <input type="number" min="0" name="radiation_sessions_remaining" value="{{ old('radiation_sessions_remaining', 0) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">عدد أيام تردد المريض على المستشفى</label>
            <input type="text" name="hospital_visit_frequency" value="{{ old('hospital_visit_frequency') }}" class="form-control" placeholder="مثال: يومان أسبوعياً، أسبوعياً...">
          </div>

          <div class="col-md-3">
            <label class="form-label">الحالة النفسية للمريض</label>
            <input type="text" name="patient_psychological_state" value="{{ old('patient_psychological_state') }}" class="form-control" placeholder="مثال: جيدة، محبط، يحتاج دعم نفسي...">
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">قبل اكتشاف الورم :</span>
            </label>
            <textarea name="before_tumor_notes" class="form-control" rows="2" placeholder="ظروف عمل المريض ودخله، الاستقرار الأسري والمادي قبل الإصابة بالمرض...">{{ old('before_tumor_notes') }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">بعد اكتشاف الورم :</span>
            </label>
            <textarea name="after_tumor_notes" class="form-control" rows="2" placeholder="تغير الظروف المادية والعمل والقدرة على الإنفاق والأعباء الناتجة عن المرض...">{{ old('after_tumor_notes') }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">البروتوكول الحالي و خطة العلاج :</span>
            </label>
            <textarea name="treatment_protocol" class="form-control" rows="3" placeholder="تفاصيل العلاج الحالي، العمليات الجراحية، التحاليل الدورية، الدواء الشهري...">{{ old('treatment_protocol') }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">مدير المؤسسة</label>
            <input type="text" name="foundation_manager" value="{{ old('foundation_manager', 'إدارة المؤسسة') }}" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label">مدير المشروع</label>
            <input type="text" name="project_manager" value="{{ old('project_manager', 'مسؤول البحث والمشاريع') }}" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <!-- أزرار الإجراءات النهائية -->
    <div class="d-flex justify-content-between align-items-center mb-5 p-3 rounded-3 bg-white shadow-sm border">
      <div>
        <span class="text-muted small">تأكد من مراجعة كافة البيانات ودقتها قبل الحفظ النهائي.</span>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary px-3">
          إلغاء
        </a>
        <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm">
          <i class="bi bi-shield-check me-1"></i> تم الاعتماد من الباحث
        </button>
      </div>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // 1. Autofill when Beneficiary is selected
  const beneficiarySelect = document.getElementById('beneficiarySelect');
  beneficiarySelect?.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.value) {
      const name = opt.dataset.name || '';
      const phone = opt.dataset.phone || '';
      const nationalId = opt.dataset.nationalId || '';
      const address = opt.dataset.address || '';

      const pName = document.getElementById('patientNameInput');
      const gName = document.getElementById('guardianNameInput');
      const nId = document.getElementById('nationalIdInput');
      const ph = document.getElementById('phoneInput');
      const addr = document.getElementById('addressInput');

      if (pName && !pName.value) pName.value = name;
      if (gName && !gName.value) gName.value = name;
      if (nId && !nId.value) nId.value = nationalId;
      if (ph && !ph.value) ph.value = phone;
      if (addr && !addr.value) addr.value = address;
    }
  });

  // 2. Autofill researcher phone
  const researcherSelect = document.getElementById('researcherSelect');
  researcherSelect?.addEventListener('change', function () {
    const opt = this.options[this.selectedIndex];
    if (opt && opt.dataset.phone) {
      const rPhone = document.getElementById('researcherPhoneInput');
      if (rPhone) rPhone.value = opt.dataset.phone;
    }
  });

  // 3. Dynamic Family Members Table
  const familyTableBody = document.getElementById('familyTableBody');
  const addFamilyRowBtn = document.getElementById('addFamilyRowBtn');

  addFamilyRowBtn?.addEventListener('click', function () {
    const rowCount = familyTableBody.children.length;
    const tr = document.createElement('tr');
    tr.innerHTML = `
      <td><input type="text" name="family_members[${rowCount}][name]" class="form-control form-control-sm" placeholder="الاسم"></td>
      <td><input type="text" name="family_members[${rowCount}][relation]" class="form-control form-control-sm" placeholder="صلة القرابة"></td>
      <td><input type="number" name="family_members[${rowCount}][age]" class="form-control form-control-sm" placeholder="السن"></td>
      <td><input type="text" name="family_members[${rowCount}][income]" class="form-control form-control-sm" placeholder="الدخل"></td>
      <td><input type="text" name="family_members[${rowCount}][job]" class="form-control form-control-sm" placeholder="العمل"></td>
      <td><input type="text" name="family_members[${rowCount}][notes]" class="form-control form-control-sm" placeholder="ملاحظات"></td>
      <td class="text-center"><button type="button" class="btn btn-sm btn-outline-danger remove-family-row p-1"><i class="bi bi-trash"></i></button></td>
    `;
    familyTableBody.appendChild(tr);
  });

  familyTableBody?.addEventListener('click', function (e) {
    if (e.target.closest('.remove-family-row')) {
      const row = e.target.closest('tr');
      if (familyTableBody.children.length > 1) {
        row.remove();
      } else {
        row.querySelectorAll('input').forEach(i => i.value = '');
      }
    }
  });

  // 4. Calculations: Income, Expenses, Debts
  function calculateTotals() {
    let totalIncome = 0;
    document.querySelectorAll('.income-input').forEach(input => {
      totalIncome += parseFloat(input.value || 0);
    });
    document.getElementById('totalIncomeDisplay').textContent = totalIncome.toFixed(2) + ' ج.م';

    let totalExpense = 0;
    document.querySelectorAll('.expense-input').forEach(input => {
      totalExpense += parseFloat(input.value || 0);
    });
    document.getElementById('totalExpenseDisplay').textContent = totalExpense.toFixed(2) + ' ج.م';

    const totalDebts = parseFloat(document.getElementById('totalDebtsInput')?.value || 0);
    const debtRepaid = parseFloat(document.getElementById('debtRepaidInput')?.value || 0);
    const debtRemaining = Math.max(0, totalDebts - debtRepaid);
    const remainingEl = document.getElementById('debtRemainingDisplay');
    if (remainingEl) remainingEl.value = debtRemaining.toFixed(2) + ' ج.م';
  }

  document.querySelectorAll('.income-input, .expense-input, #totalDebtsInput, #debtRepaidInput').forEach(input => {
    input.addEventListener('input', calculateTotals);
  });
  calculateTotals();

  // 5. Conditional Oncology Section Reveal
  const radioBtnNo = document.getElementById('radioBtnNo');
  const radioBtnYes = document.getElementById('radioBtnYes');
  const cancerSection = document.getElementById('cancerSection');
  const normalNotice = document.getElementById('normalResearchNotice');

  function setCancerMode(isCancer) {
    if (isCancer) {
      radioBtnYes.classList.add('active-yes');
      radioBtnNo.classList.remove('active-no');
      radioBtnYes.querySelector('input').checked = true;
      radioBtnNo.querySelector('input').checked = false;
      cancerSection.style.display = 'block';
      normalNotice.style.display = 'none';
      cancerSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      radioBtnNo.classList.add('active-no');
      radioBtnYes.classList.remove('active-yes');
      radioBtnNo.querySelector('input').checked = true;
      radioBtnYes.querySelector('input').checked = false;
      cancerSection.style.display = 'none';
      normalNotice.style.display = 'block';
    }
  }

  radioBtnNo?.addEventListener('click', () => setCancerMode(false));
  radioBtnYes?.addEventListener('click', () => setCancerMode(true));
});
</script>
@endsection
