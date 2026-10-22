@extends('layouts.app')

@section('title', 'عرض استمارة البحث #' . $visit->id)

@section('content')
<div class="container-fluid py-3">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <div class="d-flex align-items-center gap-2">
        <a href="{{ route('visits.index') }}" class="btn btn-sm btn-outline-secondary rounded-circle p-2" title="رجوع">
          <i class="bi bi-arrow-right fs-6"></i>
        </a>
        <h3 class="mb-0 fw-bolder text-dark">
          {{ $visit->has_cancer_patient ? 'استمارة بحث مريض أورام' : 'استمارة بحث اجتماعي' }}
          <span class="fs-6 fw-normal text-muted font-monospace">(#{{ $visit->id }})</span>
        </h3>
      </div>
      <p class="text-muted small mb-0 mt-1">
        المستفيد: <strong>{{ $visit->beneficiary?->full_name ?? $visit->patient_name }}</strong> — تاريخ البحث: {{ $visit->visit_date?->format('Y/m/d') }}
      </p>
    </div>
    <div class="d-flex gap-2">
      @if(!empty($authorizedForFinalApproval))
        <button type="button" class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#finalApprovalModal">
          <i class="bi bi-patch-check-fill me-1"></i> الاعتماد النهائي للحالة
        </button>
      @endif
      <a href="{{ route('visits.print', $visit) }}" target="_blank" class="btn btn-outline-primary fw-bold">
        <i class="bi bi-printer me-1"></i> طباعة الاستمارة الرسمية
      </a>
      <a href="{{ route('visits.edit', $visit) }}" class="btn btn-outline-warning fw-bold">
        <i class="bi bi-pencil-square me-1"></i> تعديل
      </a>
      <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i> القائمة
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Hero Card -->
  <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(135deg, {{ $visit->has_cancer_patient ? '#fee2e2 0%, #fef2f2 100%' : '#f0fdf4 0%, #ecfdf5 100%' }});">
    <div class="card-body p-4">
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
          <span class="badge {{ $visit->has_cancer_patient ? 'bg-danger' : 'bg-success' }} px-3 py-1.5 fs-6 mb-2">
            <i class="bi {{ $visit->has_cancer_patient ? 'bi-heart-pulse-fill' : 'bi-house-heart-fill' }} me-1"></i>
            {{ $visit->research_type_label }}
          </span>
          <h2 class="fw-bolder mb-1 text-dark">{{ $visit->patient_name ?: ($visit->beneficiary?->full_name ?: 'بدون اسم') }}</h2>
          <div class="text-muted small">
            <i class="bi bi-geo-alt me-1"></i> {{ $visit->address ?: 'العنوان لم يحدد' }}
          </div>
        </div>

        <div class="d-flex gap-4">
          <div class="text-center p-3 bg-white bg-opacity-75 rounded-3 shadow-sm border">
            <div class="small text-muted fw-bold">إجمالي الدخل</div>
            <div class="fs-4 fw-bolder text-success font-monospace">{{ number_format($visit->total_income, 2) }} ج.م</div>
          </div>
          <div class="text-center p-3 bg-white bg-opacity-75 rounded-3 shadow-sm border">
            <div class="small text-muted fw-bold">إجمالي المصروفات</div>
            <div class="fs-4 fw-bolder text-danger font-monospace">{{ number_format($visit->total_expenses, 2) }} ج.م</div>
          </div>
          <div class="text-center p-3 bg-white bg-opacity-75 rounded-3 shadow-sm border">
            <div class="small text-muted fw-bold">الديون المتبقية</div>
            <div class="fs-4 fw-bolder text-warning font-monospace">{{ number_format($visit->debt_remaining, 2) }} ج.م</div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <!-- بيانات الحالة والباحث -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
          <h5 class="fw-bold mb-0 text-primary"><i class="bi bi-person-lines-fill me-2"></i>بيانات الحالة والباحث</h5>
        </div>
        <div class="card-body p-4">
          <table class="table table-bordered table-sm mb-0">
            <tbody>
              <tr>
                <th class="bg-light text-muted" style="width: 35%;">اسم الباحث:</th>
                <td class="fw-bold">{{ $visit->researcher?->name ?? 'غير محدد' }}</td>
              </tr>
              <tr>
                <th class="bg-light text-muted">هاتف الباحث:</th>
                <td class="font-monospace">{{ $visit->researcher_phone ?: ($visit->researcher?->phone ?: '—') }}</td>
              </tr>
              <tr>
                <th class="bg-light text-muted">اسم ولي الأمر / المعيل:</th>
                <td>{{ $visit->guardian_name ?: '—' }}</td>
              </tr>
              <tr>
                <th class="bg-light text-muted">الرقم القومي:</th>
                <td class="font-monospace fw-bold">{{ $visit->national_id ?: '—' }}</td>
              </tr>
              <tr>
                <th class="bg-light text-muted">رقم الهاتف:</th>
                <td class="font-monospace">{{ $visit->phone ?: '—' }}</td>
              </tr>
              <tr>
                <th class="bg-light text-muted">ملكية السكن:</th>
                <td><span class="badge bg-secondary">{{ $visit->home_ownership ?: 'غير محدد' }}</span></td>
              </tr>
              <tr>
                <th class="bg-light text-muted">التوصية الميدانية:</th>
                <td><span class="badge bg-info text-dark">{{ $visit->recommendation_label }}</span></td>
              </tr>
              <tr>
                <th class="bg-light text-muted">رأي الباحث:</th>
                <td class="text-dark bg-warning bg-opacity-10 fw-medium">{{ $visit->researcher_opinion ?: 'لا توجد ملاحظات مدونة' }}</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- تقييم السكن والاحتياج -->
    <div class="col-lg-6">
      <div class="card border-0 shadow-sm rounded-4 h-100">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
          <h5 class="fw-bold mb-0 text-warning"><i class="bi bi-house-door-fill me-2"></i>تقييم السكن والحالة الاجتماعية</h5>
        </div>
        <div class="card-body p-4">
          <div class="mb-3">
            <label class="small text-muted fw-bold">احتياج الحالة من التقديم:</label>
            <div class="p-2.5 rounded bg-light border text-dark fw-bold">{{ $visit->case_needs ?: 'لم يحدد' }}</div>
          </div>
          <div class="mb-3">
            <label class="small text-muted fw-bold">دليل الحالة للمؤسسة:</label>
            <div class="p-2.5 rounded bg-light border text-dark">{{ $visit->case_guide ?: 'لم يحدد' }}</div>
          </div>
          <div>
            <label class="small text-muted fw-bold">وصف الحالة الاجتماعية وظروف الأسرة ووضع البيت:</label>
            <div class="p-3 rounded bg-warning bg-opacity-10 border border-warning border-opacity-25 text-dark" style="min-height: 80px;">
              {{ $visit->home_and_social_condition ?: 'لا يوجد وصف مدون' }}
            </div>
          </div>
        </div>
      </div>
    </div>

    @if($visit->house_photo_url || !empty($visit->condition_photo_urls))
      <!-- صور المنزل ومستندات تدهور الحالة -->
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-primary">
              <i class="bi bi-images me-2"></i>صور المعاينة وتدهور الحالة ومستندات الشيكات
            </h5>
          </div>
          <div class="card-body p-4">
            <div class="row g-3">
              @if($visit->house_photo_url)
                <div class="col-md-4">
                  <div class="border rounded-3 p-2 bg-light text-center h-100">
                    <div class="fw-bold small text-muted mb-2"><i class="bi bi-house me-1"></i> صورة المنزل</div>
                    <a href="{{ $visit->house_photo_url }}" target="_blank">
                      <img src="{{ $visit->house_photo_url }}" alt="صورة المنزل" class="img-fluid rounded shadow-sm" style="max-height: 220px; object-fit: cover; width: 100%;">
                    </a>
                  </div>
                </div>
              @endif

              @if(!empty($visit->condition_photo_urls))
                <div class="{{ $visit->house_photo_url ? 'col-md-8' : 'col-12' }}">
                  <div class="border rounded-3 p-3 bg-light h-100">
                    <div class="fw-bold small text-muted mb-2"><i class="bi bi-file-earmark-image me-1"></i> صور الشيكات والوصولات وتدهور الحالة ({{ count($visit->condition_photo_urls) }} صورة)</div>
                    <div class="d-flex flex-wrap gap-2">
                      @foreach($visit->condition_photo_urls as $cPhotoUrl)
                        <a href="{{ $cPhotoUrl }}" target="_blank" class="border rounded p-1 bg-white shadow-sm" title="عرض بالحجم الكامل">
                          <img src="{{ $cPhotoUrl }}" alt="مستند" style="width: 100px; height: 100px; object-fit: cover; border-radius: 6px;">
                        </a>
                      @endforeach
                    </div>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    @endif

    <!-- أفراد الأسرة -->
    <div class="col-12">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
          <h5 class="fw-bold mb-0 text-success"><i class="bi bi-people-fill me-2"></i>أفراد الأسرة والمقيمين</h5>
        </div>
        <div class="card-body p-4">
          @if(!empty($visit->family_members) && count($visit->family_members) > 0)
            <div class="table-responsive">
              <table class="table table-bordered table-hover mb-0 text-center">
                <thead class="table-light">
                  <tr>
                    <th>#</th>
                    <th>الاسم</th>
                    <th>صلة القرابة</th>
                    <th>السن</th>
                    <th>الدخل</th>
                    <th>العمل</th>
                    <th>ملاحظات</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($visit->family_members as $i => $fm)
                    <tr>
                      <td>{{ $i + 1 }}</td>
                      <td class="fw-bold text-start">{{ $fm['name'] ?: '—' }}</td>
                      <td>{{ $fm['relation'] ?: '—' }}</td>
                      <td>{{ $fm['age'] ?: '—' }}</td>
                      <td>{{ $fm['income'] ?: '—' }}</td>
                      <td>{{ $fm['job'] ?: '—' }}</td>
                      <td class="text-muted text-start">{{ $fm['notes'] ?: '—' }}</td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-muted text-center py-3">لم يتم تسجيل أفراد أسرة إضافيين في الاستمارة.</div>
          @endif
        </div>
      </div>
    </div>

    @if($visit->has_cancer_patient)
      <!-- قسم مرضى الأورام -->
      <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 border-top border-4 border-danger">
          <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0 text-danger"><i class="bi bi-hospital-fill me-2"></i>بيانات مريض الأورام والبروتوكول الطبي</h5>
            <span class="badge bg-danger">حالة مريض أورام</span>
          </div>
          <div class="card-body p-4">
            <div class="row g-3 mb-4">
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border">
                  <div class="small text-muted">المستشفى المتلقى بها العلاج</div>
                  <div class="fw-bold text-dark fs-6">{{ $visit->hospital_name ?: '—' }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border">
                  <div class="small text-muted">مكان الورم بالجسم</div>
                  <div class="fw-bold text-danger fs-6">{{ $visit->tumor_location ?: '—' }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border">
                  <div class="small text-muted">تاريخ اكتشاف المرض / المدة</div>
                  <div class="fw-bold text-dark fs-6">{{ $visit->disease_discovery_date ?: '—' }} ({{ $visit->disease_duration ?: '—' }})</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border">
                  <div class="small text-muted">تردد المريض / الحالة النفسية</div>
                  <div class="fw-bold text-dark fs-6">{{ $visit->hospital_visit_frequency ?: '—' }} | {{ $visit->patient_psychological_state ?: '—' }}</div>
                </div>
              </div>

              <div class="col-md-3">
                <div class="p-3 rounded bg-light border text-center">
                  <div class="small text-muted">جلسات الكيماوي المتلقاة</div>
                  <div class="fs-4 fw-bolder text-primary font-monospace">{{ $visit->chemo_sessions_taken }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border text-center">
                  <div class="small text-muted">جلسات الكيماوي المتبقية</div>
                  <div class="fs-4 fw-bolder text-danger font-monospace">{{ $visit->chemo_sessions_remaining }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border text-center">
                  <div class="small text-muted">جلسات الإشعاع المتلقاة</div>
                  <div class="fs-4 fw-bolder text-primary font-monospace">{{ $visit->radiation_sessions_taken }}</div>
                </div>
              </div>
              <div class="col-md-3">
                <div class="p-3 rounded bg-light border text-center">
                  <div class="small text-muted">جلسات الإشعاع المتبقية</div>
                  <div class="fs-4 fw-bolder text-danger font-monospace">{{ $visit->radiation_sessions_remaining }}</div>
                </div>
              </div>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <label class="small text-muted fw-bold">قبل اكتشاف الورم:</label>
                <div class="p-3 rounded bg-light border text-dark">{{ $visit->before_tumor_notes ?: 'لا توجد بيانات' }}</div>
              </div>
              <div class="col-md-6">
                <label class="small text-muted fw-bold">بعد اكتشاف الورم:</label>
                <div class="p-3 rounded bg-light border text-dark">{{ $visit->after_tumor_notes ?: 'لا توجد بيانات' }}</div>
              </div>
              <div class="col-12">
                <label class="small text-muted fw-bold">البروتوكول الحالي وخطة العلاج:</label>
                <div class="p-3 rounded bg-danger bg-opacity-10 border border-danger border-opacity-25 text-dark fw-medium">
                  {{ $visit->treatment_protocol ?: 'لم يتم تدوين البروتوكول' }}
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    @endif
  </div>
</div>

@if(!empty($authorizedForFinalApproval))
<div class="modal fade" id="finalApprovalModal" tabindex="-1" aria-labelledby="finalApprovalModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
    <form class="modal-content border-0 shadow-lg" method="POST" action="{{ route('visits.final-approval', $visit) }}">
      @csrf
      <div class="modal-header bg-primary text-white">
        <h5 class="modal-title fw-bold" id="finalApprovalModalLabel">
          <i class="bi bi-shield-lock-fill me-1"></i> قرار الاعتماد النهائي للحالة بعد البحث الميداني
        </h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="إغلاق"></button>
      </div>

      <div class="modal-body p-4">
        <div class="alert alert-info py-2 mb-3">
          <i class="bi bi-info-circle-fill me-1"></i> الحالة: <strong>{{ $visit->patient_name ?: ($visit->beneficiary?->full_name ?: 'بدون اسم') }}</strong> (رقم قومي: {{ $visit->national_id ?: 'غير مسجل' }})
        </div>

        <div class="mb-4">
          <label class="form-label fw-bold fs-6">نوع القرار النهائي <span class="text-danger">*</span></label>
          <div class="d-flex gap-3">
            <div class="form-check form-check-inline p-3 border rounded-3 flex-grow-1 bg-light">
              <input class="form-check-input ms-2" type="radio" name="decision" id="decisionAccept" value="accepted" checked onchange="toggleFinalDecision(this.value)">
              <label class="form-check-label fw-bold text-success fs-6" for="decisionAccept">
                <i class="bi bi-check-circle-fill me-1"></i> قبول الحالة وإلحاقها بمشروع
              </label>
            </div>
            <div class="form-check form-check-inline p-3 border rounded-3 flex-grow-1 bg-light">
              <input class="form-check-input ms-2" type="radio" name="decision" id="decisionReject" value="rejected" onchange="toggleFinalDecision(this.value)">
              <label class="form-check-label fw-bold text-danger fs-6" for="decisionReject">
                <i class="bi bi-x-circle-fill me-1"></i> رفض الحالة
              </label>
            </div>
          </div>
        </div>

        {{-- صندوق الرفض --}}
        <div id="rejectionSection" class="p-3 border border-danger rounded-3 bg-danger bg-opacity-10 mb-3" style="display:none">
          <label class="form-label fw-bold text-danger">سبب الرفض <span class="text-danger">* (إلزامي في حال الرفض)</span></label>
          <textarea name="rejection_reason" id="finalRejectionReason" class="form-control" rows="3" placeholder="اكتب أسباب عدم قبول الحالة تفصيلاً..."></textarea>
        </div>

        {{-- صندوق القبول --}}
        <div id="acceptanceSection">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">تحديد المشروع <span class="text-danger">*</span></label>
              <select name="project_id" id="finalProjectSelect" class="form-select" onchange="handleFinalProjectChange(this)">
                <option value="">— اختر المشروع —</option>
                @foreach($projects as $proj)
                  <option value="{{ $proj->id }}" data-name="{{ $proj->name }}" @selected($visit->beneficiary?->project_id == $proj->id)>{{ $proj->name }}</option>
                @endforeach
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label fw-bold">مبلغ الكفالة الشهرية (إن وجد)</label>
              <div class="input-group">
                <input type="number" step="0.01" min="0" name="monthly_sponsorship_amount" class="form-control" value="{{ $visit->beneficiary?->monthly_sponsorship_amount }}" placeholder="0.00">
                <span class="input-group-text">ج.م</span>
              </div>
            </div>

            {{-- حقول خاصة بمشروع زاد (الأيتام) --}}
            <div class="col-12 project-conditional-box" id="zadApprovalFields" style="display:none">
              <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3">
                <h6 class="fw-bold text-success mb-2"><i class="bi bi-people-fill me-1"></i> تفاصيل كفالة مشروع زاد (الأيتام)</h6>
                <div class="row g-2">
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">عدد الأطفال المكفولين</label>
                    <input type="number" min="1" max="10" name="zad_children_count" class="form-control" placeholder="عدد الأطفال">
                  </div>
                  <div class="col-md-6">
                    <label class="form-label small fw-bold">كفالة كل طفل</label>
                    <input type="number" step="0.01" min="0" name="zad_child_sponsorship" class="form-control" placeholder="مبلغ كفالة الطفل">
                  </div>
                </div>
              </div>
            </div>

            {{-- حقول خاصة بمرضى الأورام (مشروع بعثاء الأمل) --}}
            <div class="col-12 project-conditional-box" id="bathaApprovalFields" style="display:none">
              <div class="p-3 bg-danger bg-opacity-10 border border-danger rounded-3">
                <h6 class="fw-bold text-danger mb-2"><i class="bi bi-hospital me-1"></i> تفاصيل رعاية مريض الأورام وصرف العلاج الثابت</h6>
                <div class="row g-2">
                  <div class="col-md-4">
                    <label class="form-label small fw-bold">اسم العلاج الثابت</label>
                    <input type="text" name="fixed_medicine_name" class="form-control" placeholder="اسم الدواء / العلاج">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small fw-bold">مدة الصرف (بالشهور)</label>
                    <input type="number" min="1" max="60" name="fixed_medicine_duration_months" id="medMonths" class="form-control" placeholder="مثال: 6" oninput="calcMedEndDate(this.value)">
                  </div>
                  <div class="col-md-4">
                    <label class="form-label small fw-bold">تاريخ انتهاء الصرف</label>
                    <input type="date" name="fixed_medicine_end_date" id="medEndDate" class="form-control">
                    <div class="form-text text-muted small"><i class="bi bi-bell me-1"></i> إشعار تجديد تلقائي قبل الانتهاء بـ 15 يوم</div>
                  </div>
                </div>
              </div>
            </div>

            {{-- خيار صرف مساعدة عينية --}}
            <div class="col-12">
              <label class="form-label fw-bold"><i class="bi bi-box-seam text-secondary me-1"></i> صرف مساعدة عينية (اختياري)</label>
              <input type="text" name="in_kind_notes" class="form-control" placeholder="مثال: كرتونة مواد غذائية شهرية، أجهزة تعويضية، بطاطين...">
            </div>

            {{-- ملاحظات الاعتماد --}}
            <div class="col-12">
              <label class="form-label fw-bold">ملاحظات وقرار الاعتماد</label>
              <textarea name="approval_notes" class="form-control" rows="2" placeholder="أي توجيهات أو ملاحظات إضافية بخصوص اعتماد الحالة..."></textarea>
            </div>
          </div>
        </div>
      </div>

      <div class="modal-footer bg-light">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
        <button type="submit" class="btn btn-success fw-bold px-4">
          <i class="bi bi-check2-circle me-1"></i> تأكيد وحفظ القرار
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function toggleFinalDecision(val) {
  const rej = document.getElementById('rejectionSection');
  const acc = document.getElementById('acceptanceSection');
  const rejInput = document.getElementById('finalRejectionReason');
  if (val === 'rejected') {
    rej.style.display = '';
    acc.style.display = 'none';
    rejInput.required = true;
  } else {
    rej.style.display = 'none';
    acc.style.display = '';
    rejInput.required = false;
  }
}

function handleFinalProjectChange(sel) {
  const opt = sel.options[sel.selectedIndex];
  const name = opt?.dataset.name || opt?.text || '';
  const isZad = name.includes('زاد');
  const isBatha = name.includes('بعثاء') || name.includes('علاج') || name.includes('أورام');
  const zadBox = document.getElementById('zadApprovalFields');
  const bathaBox = document.getElementById('bathaApprovalFields');
  if (zadBox) zadBox.style.display = isZad ? '' : 'none';
  if (bathaBox) bathaBox.style.display = isBatha ? '' : 'none';
}

function calcMedEndDate(months) {
  const m = parseInt(months, 10);
  if (!m || m < 1) return;
  const d = new Date();
  d.setMonth(d.getMonth() + m);
  const target = document.getElementById('medEndDate');
  if (target) {
    target.value = d.toISOString().split('T')[0];
  }
}

document.addEventListener('DOMContentLoaded', function() {
  const pSelect = document.getElementById('finalProjectSelect');
  if (pSelect) handleFinalProjectChange(pSelect);
});
</script>
@endif
@endsection
