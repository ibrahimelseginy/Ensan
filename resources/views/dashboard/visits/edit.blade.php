@extends('layouts.app')

@section('title', 'تعديل استمارة بحث ميداني #' . $visit->id)

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
  .official-notice-yellow {
    background-color: #fef08a !important;
    color: #854d0e !important;
    font-weight: 700;
    padding: 0.45rem 0.85rem;
    border-radius: 8px;
    display: inline-block;
  }
  .official-notice-green {
    background-color: #22c55e !important;
    color: #ffffff !important;
    font-weight: 800;
    padding: 0.6rem 1rem;
    border-radius: 8px;
    text-align: center;
    font-size: 0.95rem;
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
        <a href="{{ route('visits.show', $visit) }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2" title="رجوع">
          <i class="bi bi-arrow-right fs-6"></i>
        </a>
        <h3 class="mb-0 fw-bolder text-dark">تعديل استمارة البحث الميداني (#{{ $visit->id }})</h3>
      </div>
      <p class="text-muted small mb-0 mt-1">
        المستفيد: <strong>{{ $visit->beneficiary?->full_name ?? $visit->patient_name }}</strong> — تاريخ البحث: {{ $visit->visit_date?->format('Y/m/d') }}
      </p>
    </div>
    <div class="d-flex gap-2">
      <a href="{{ route('visits.print', $visit) }}" target="_blank" class="btn btn-outline-primary">
        <i class="bi bi-printer me-1"></i> طباعة الاستمارة الرسمية
      </a>
      <button type="button" onclick="document.getElementById('researchEditForm').submit()" class="btn btn-success fw-bold px-4">
        <i class="bi bi-check2-circle me-1"></i> تحديث وحفظ التعديلات
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

  <form id="researchEditForm" method="POST" action="{{ route('visits.update', $visit) }}">
    @csrf
    @method('PUT')

    <!-- الأساسيات: المستفيد والباحث وتاريخ البحث -->
    <div class="research-card">
      <div class="research-card-header text-primary">
        <i class="bi bi-person-lines-fill"></i>
        <span>أولاً: بيانات الحالة والباحث الميداني</span>
      </div>
      <div class="research-card-body">
        <div class="row g-3">
          <div class="col-md-4">
            <label class="form-label fw-bold">ربط بملف المستفيد <span class="text-danger">*</span></label>
            <select name="beneficiary_id" id="beneficiarySelect" class="form-select" required>
              <option value="">-- اختر المستفيد أو الحالة --</option>
              @foreach($beneficiaries as $ben)
                <option value="{{ $ben->id }}"
                  data-name="{{ $ben->full_name ?? $ben->name }}"
                  data-phone="{{ $ben->phone }}"
                  data-national-id="{{ $ben->national_id }}"
                  data-address="{{ $ben->address }}"
                  @selected((string)old('beneficiary_id', $visit->beneficiary_id) === (string)$ben->id)>
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
                  @selected((string)old('researcher_id', $visit->researcher_id) === (string)$res->id)>
                  {{ $res->name }}
                </option>
              @endforeach
            </select>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">تاريخ البحث <span class="text-danger">*</span></label>
            <input type="date" name="visit_date" value="{{ old('visit_date', $visit->visit_date?->format('Y-m-d')) }}" class="form-control" required>
          </div>

          <div class="col-md-2">
            <label class="form-label fw-bold">حالة الزيارة</label>
            <select name="status" class="form-select">
              <option value="completed" @selected(old('status', $visit->status) === 'completed')>تمت الزيارة والبحث</option>
              <option value="scheduled" @selected(old('status', $visit->status) === 'scheduled')>مجدولة</option>
              <option value="cancelled" @selected(old('status', $visit->status) === 'cancelled')>ملغاة</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label">اسم المريض / الحالة كاملاً</label>
            <input type="text" name="patient_name" id="patientNameInput" value="{{ old('patient_name', $visit->patient_name) }}" class="form-control" placeholder="اسم الحالة محل البحث">
          </div>

          <div class="col-md-4">
            <label class="form-label">اسم ولي الأمر / المعيل</label>
            <input type="text" name="guardian_name" id="guardianNameInput" value="{{ old('guardian_name', $visit->guardian_name) }}" class="form-control" placeholder="ولي الأمر أو رب الأسرة">
          </div>

          <div class="col-md-4">
            <label class="form-label">الرقم القومي (14 رقم)</label>
            <input type="text" name="national_id" id="nationalIdInput" value="{{ old('national_id', $visit->national_id) }}" maxlength="14" class="form-control" placeholder="الرقم القومي">
          </div>

          <div class="col-md-3">
            <label class="form-label">رقم الهاتف</label>
            <input type="text" name="phone" id="phoneInput" value="{{ old('phone', $visit->phone) }}" class="form-control" placeholder="رقم هاتف الحالة">
          </div>

          <div class="col-md-3">
            <label class="form-label">رقم هاتف الباحث</label>
            <input type="text" name="researcher_phone" id="researcherPhoneInput" value="{{ old('researcher_phone', $visit->researcher_phone) }}" class="form-control" placeholder="هاتف الباحث الميداني">
          </div>

          <div class="col-md-3">
            <label class="form-label">ملكية البيت</label>
            <select name="home_ownership" class="form-select">
              <option value="ملك" @selected(old('home_ownership', $visit->home_ownership) === 'ملك')>ملك</option>
              <option value="إيجار" @selected(old('home_ownership', $visit->home_ownership) === 'إيجار')>إيجار</option>
              <option value="ورثة" @selected(old('home_ownership', $visit->home_ownership) === 'ورثة')>ورثة</option>
              <option value="استضافة" @selected(old('home_ownership', $visit->home_ownership) === 'استضافة')>استضافة</option>
            </select>
          </div>

          <div class="col-md-3">
            <label class="form-label">توصية الباحث الميداني</label>
            <select name="recommendation" class="form-select">
              <option value="approve" @selected(old('recommendation', $visit->recommendation) === 'approve')>يوصى بالقبول</option>
              <option value="needs_review" @selected(old('recommendation', $visit->recommendation) === 'needs_review')>تحتاج مراجعة وبحث إضافي</option>
              <option value="reject" @selected(old('recommendation', $visit->recommendation) === 'reject')>يوصى بالرفض</option>
            </select>
          </div>

          <div class="col-12">
            <label class="form-label">العنوان بالتفصيل</label>
            <input type="text" name="address" id="addressInput" value="{{ old('address', $visit->address) }}" class="form-control" placeholder="المحافظة - المركز / المدينة - القرية / الشارع التفصيلي">
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">رأي الباحث الميداني :</span>
            </label>
            <textarea name="researcher_opinion" class="form-control" rows="2">{{ old('researcher_opinion', $visit->researcher_opinion) }}</textarea>
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
                $existingMembers = old('family_members', $visit->family_members ?? []);
                if (empty($existingMembers)) {
                    $existingMembers = [
                        ['name' => '', 'relation' => 'الزوج / الزوجة', 'age' => '', 'income' => '', 'job' => '', 'notes' => '']
                    ];
                }
              @endphp
              @foreach($existingMembers as $idx => $member)
                <tr>
                  <td><input type="text" name="family_members[{{ $idx }}][name]" value="{{ $member['name'] ?? '' }}" class="form-control form-control-sm" placeholder="الاسم"></td>
                  <td><input type="text" name="family_members[{{ $idx }}][relation]" value="{{ $member['relation'] ?? '' }}" class="form-control form-control-sm" placeholder="صلة القرابة"></td>
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
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_job" id="income_job" value="{{ old('income_job', $visit->income_job) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">عمل يومي (حرفي / فني / عامل / فاعل)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_daily_work" id="income_daily_work" value="{{ old('income_daily_work', $visit->income_daily_work) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">معاشات تأمينات (كبار السن / التقاعد)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_pension" id="income_pension" value="{{ old('income_pension', $visit->income_pension) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">الشئون الاجتماعية (طلاق / عجز طبي / ترمل / تكافل)</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_social_affairs" id="income_social_affairs" value="{{ old('income_social_affairs', $visit->income_social_affairs) }}" class="form-control form-control-sm income-input text-end"></div>

              <div class="col-8"><label class="form-label small">الأقارب / الجيران / جمعيات شرعية / خيرية</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="income_charities" id="income_charities" value="{{ old('income_charities', $visit->income_charities) }}" class="form-control form-control-sm income-input text-end"></div>

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
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_rent" id="expense_rent" value="{{ old('expense_rent', $visit->expense_rent) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">مصاريف الدروس والتعليم</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_tuition" id="expense_tuition" value="{{ old('expense_tuition', $visit->expense_tuition) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">متوسط تكلفة الطعام والشراب</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_food_weekly" id="expense_food_weekly" value="{{ old('expense_food_weekly', $visit->expense_food_weekly) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">المواصلات اليومية</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_transportation" id="expense_transportation" value="{{ old('expense_transportation', $visit->expense_transportation) }}" class="form-control form-control-sm expense-input text-end"></div>

              <div class="col-8"><label class="form-label small">فاتورة الكهرباء / المياه / الغاز</label></div>
              <div class="col-4"><input type="number" step="0.01" min="0" name="expense_utilities" id="expense_utilities" value="{{ old('expense_utilities', $visit->expense_utilities) }}" class="form-control form-control-sm expense-input text-end"></div>

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
            <input type="number" step="0.01" min="0" name="total_debts" id="totalDebtsInput" value="{{ old('total_debts', $visit->total_debts) }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">ما تم سداده (ج.م)</label>
            <input type="number" step="0.01" min="0" name="debt_repaid" id="debtRepaidInput" value="{{ old('debt_repaid', $visit->debt_repaid) }}" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">المتبقي (تلقائي)</label>
            <input type="text" id="debtRemainingDisplay" readonly class="form-control bg-light font-monospace fw-bold text-danger" value="0.00">
          </div>
          <div class="col-md-3">
            <label class="form-label">القسط الشهري (ج.م)</label>
            <input type="number" step="0.01" min="0" name="debt_monthly_installment" value="{{ old('debt_monthly_installment', $visit->debt_monthly_installment) }}" class="form-control">
          </div>

          <div class="col-12">
            <label class="form-label">سبب الدين</label>
            <input type="text" name="debt_reason" value="{{ old('debt_reason', $visit->debt_reason) }}" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label">دليل الحالة للمؤسسة</label>
            <input type="text" name="case_guide" value="{{ old('case_guide', $visit->case_guide) }}" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label">احتياج الحالة من التقديم</label>
            <input type="text" name="case_needs" value="{{ old('case_needs', $visit->case_needs) }}" class="form-control">
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">وصف الحالة الاجتماعية للأسرة ، وظروف الأسرة ، ووضع البيت ( محتاج سقف / ترميم / بنى ) :</span>
            </label>
            <textarea name="home_and_social_condition" class="form-control" rows="3">{{ old('home_and_social_condition', $visit->home_and_social_condition) }}</textarea>
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

      @php
        $isCancerActive = old('has_cancer_patient', $visit->has_cancer_patient);
      @endphp

      <div class="d-inline-flex gap-3 my-2">
        <label class="cancer-radio-btn {{ $isCancerActive ? '' : 'active-no' }}" id="radioBtnNo">
          <input type="radio" name="has_cancer_patient" value="0" class="d-none" @checked(!$isCancerActive)>
          <i class="bi bi-x-circle-fill"></i>
          <span>لا (بحث اجتماعي عادي)</span>
        </label>

        <label class="cancer-radio-btn {{ $isCancerActive ? 'active-yes' : '' }}" id="radioBtnYes">
          <input type="radio" name="has_cancer_patient" value="1" class="d-none" @checked($isCancerActive)>
          <i class="bi bi-check-circle-fill"></i>
          <span>نعم (يوجد مريض أورام)</span>
        </label>
      </div>

      <div id="normalResearchNotice" class="mt-3 text-muted small" style="{{ $isCancerActive ? 'display: none;' : '' }}">
        <i class="bi bi-info-circle text-primary me-1"></i> تم تحديد: <strong>بحث اجتماعي عادي</strong>.
      </div>
    </div>

    <!-- ============================================================== -->
    <!-- القسم الخاص بمرضى الأورام (يفتح فقط إذا تم اختيار "نعم") -->
    <!-- ============================================================== -->
    <div id="cancerSection" class="research-card border-danger shadow-sm" style="{{ $isCancerActive ? '' : 'display: none;' }}">
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
            <input type="text" name="hospital_name" value="{{ old('hospital_name', $visit->hospital_name) }}" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label fw-bold">مكان الورم بالجسم</label>
            <input type="text" name="tumor_location" value="{{ old('tumor_location', $visit->tumor_location) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">تاريخ اكتشاف المرض</label>
            <input type="text" name="disease_discovery_date" value="{{ old('disease_discovery_date', $visit->disease_discovery_date) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">مدة اكتشاف المرض</label>
            <input type="text" name="disease_duration" value="{{ old('disease_duration', $visit->disease_duration) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الكيماوي المتلقاة</label>
            <input type="number" min="0" name="chemo_sessions_taken" value="{{ old('chemo_sessions_taken', $visit->chemo_sessions_taken) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الكيماوي المتبقية</label>
            <input type="number" min="0" name="chemo_sessions_remaining" value="{{ old('chemo_sessions_remaining', $visit->chemo_sessions_remaining) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الإشعاع المتلقاة</label>
            <input type="number" min="0" name="radiation_sessions_taken" value="{{ old('radiation_sessions_taken', $visit->radiation_sessions_taken) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">جلسات الإشعاع المتبقية</label>
            <input type="number" min="0" name="radiation_sessions_remaining" value="{{ old('radiation_sessions_remaining', $visit->radiation_sessions_remaining) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">عدد أيام تردد المريض على المستشفى</label>
            <input type="text" name="hospital_visit_frequency" value="{{ old('hospital_visit_frequency', $visit->hospital_visit_frequency) }}" class="form-control">
          </div>

          <div class="col-md-3">
            <label class="form-label">الحالة النفسية للمريض</label>
            <input type="text" name="patient_psychological_state" value="{{ old('patient_psychological_state', $visit->patient_psychological_state) }}" class="form-control">
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">قبل اكتشاف الورم :</span>
            </label>
            <textarea name="before_tumor_notes" class="form-control" rows="2">{{ old('before_tumor_notes', $visit->before_tumor_notes) }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">بعد اكتشاف الورم :</span>
            </label>
            <textarea name="after_tumor_notes" class="form-control" rows="2">{{ old('after_tumor_notes', $visit->after_tumor_notes) }}</textarea>
          </div>

          <div class="col-12">
            <label class="form-label">
              <span class="official-notice-yellow">البروتوكول الحالي و خطة العلاج :</span>
            </label>
            <textarea name="treatment_protocol" class="form-control" rows="3">{{ old('treatment_protocol', $visit->treatment_protocol) }}</textarea>
          </div>

          <div class="col-md-6">
            <label class="form-label">مدير المؤسسة</label>
            <input type="text" name="foundation_manager" value="{{ old('foundation_manager', $visit->foundation_manager ?? 'إدارة المؤسسة') }}" class="form-control">
          </div>

          <div class="col-md-6">
            <label class="form-label">مدير المشروع</label>
            <input type="text" name="project_manager" value="{{ old('project_manager', $visit->project_manager ?? 'مسؤول البحث والمشاريع') }}" class="form-control">
          </div>
        </div>
      </div>
    </div>

    <!-- أزرار الإجراءات النهائية -->
    <div class="d-flex justify-content-between align-items-center mb-5 p-3 rounded-3 bg-white shadow-sm border">
      <div>
        <span class="text-muted small">تأكد من مراجعة كافة البيانات ودقتها قبل التحديث.</span>
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('visits.show', $visit) }}" class="btn btn-outline-secondary px-3">
          إلغاء
        </a>
        <button type="submit" class="btn btn-primary px-4 fw-bold">
          <i class="bi bi-save me-1"></i> حفظ التعديلات
        </button>
      </div>
    </div>
  </form>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  // Calculations
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

  // Dynamic Family Members Table
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

  // Conditional Oncology Section Reveal
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
