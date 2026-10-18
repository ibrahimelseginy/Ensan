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
@endsection
