@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<div class="container py-4 min-vh-100">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">

      <style>
        /* Default / Light Mode Typography & Contrast */
        .apply-header-card {
            background-color: #ffffff !important;
            background: #ffffff !important;
            border: 1px solid #e2e8f0 !important;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05) !important;
        }

        .apply-header-badge {
            background-color: #f1f5f9 !important;
            background: #f1f5f9 !important;
            color: #000000 !important;
            border: 1px solid #cbd5e1 !important;
        }

        .apply-header-title {
            color: #000000 !important;
            font-weight: 900 !important;
        }

        .apply-header-subtitle {
            color: #000000 !important;
            font-weight: 600 !important;
        }

        .apply-header-subtitle strong {
            color: #000000 !important;
            font-weight: 800 !important;
        }

        /* Dark Mode */
        html.theme-dark .apply-header-card,
        html[data-bs-theme="dark"] .apply-header-card {
            background-color: #0f172a !important;
            background: #0f172a !important;
            border: 1px solid #334155 !important;
        }

        html.theme-dark .apply-header-badge,
        html[data-bs-theme="dark"] .apply-header-badge {
            background-color: #1e293b !important;
            background: #1e293b !important;
            color: #f8fafc !important;
            border: 1px solid #334155 !important;
        }

        html.theme-dark .apply-header-title,
        html[data-bs-theme="dark"] .apply-header-title {
            color: #ffffff !important;
            font-weight: 900 !important;
        }

        html.theme-dark .apply-header-subtitle,
        html[data-bs-theme="dark"] .apply-header-subtitle {
            color: #cbd5e1 !important;
        }

        html.theme-dark .apply-header-subtitle strong,
        html[data-bs-theme="dark"] .apply-header-subtitle strong {
            color: #ffffff !important;
        }
      </style>

      <!-- Header Card -->
      <div class="card shadow-sm rounded-4 overflow-hidden mb-4 apply-header-card">
        <div class="p-4 p-md-5 text-center position-relative">
          <div class="badge apply-header-badge rounded-pill px-3.5 py-1.5 fw-bold mb-3 shadow-xs">
            مؤسسة إنسان الخيرية | مشهرة برقم 63 لسنة 2017
          </div>
          <h1 class="fw-black mb-2 apply-header-title" style="font-size: 2rem;">إستمارة تقديم طلب مساعدة في مشروع مدرار</h1>
          <p class="mb-2 mx-auto fw-bold apply-header-subtitle" style="max-width: 650px; font-size: 1.15rem; line-height: 1.7;">
            مشروع مدرار أحد مشاريع مؤسسة إنسان الخيرية مهتم بالصدقات الجارية والوقف الخيري (وصلات مياه - أسقف)
          </p>
          <p class="mb-0 mx-auto small apply-header-subtitle">
            <strong>رؤيتنا :</strong> تقديم عمل خيري تطوعي بإحترافية مع الحفاظ علي كرامة الإنسان
          </p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
          <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
          <h4 class="fw-bold mb-2">تم تسجيل طلب مشروع مدرار بنجاح</h4>
          <p class="mb-0 text-muted fs-6">{{ session('success') }}</p>
          <div class="mt-3">
            <a href="{{ route('cases.apply-midrar') }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">تقديم استمارة جديدة</a>
          </div>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-4 mb-4">
          <h6 class="fw-bold text-danger mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى استكمال الحقول الإلزامية المطلوبة:</h6>
          <ul class="mb-0 small ps-3">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <!-- Main Form -->
      <div class="card border-0 shadow-sm rounded-4 p-4 p-md-5 mb-5 bg-stats-card-main">
        <form method="POST" action="{{ route('cases.apply-midrar.store') }}">
          @csrf

          <!-- Section 1: بيانات المسجل للحالة -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-person-badge-fill me-2"></i> 1. بيانات المُسجّل للحالة
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">اسم المسجل للحالة <span class="text-danger">*</span></label>
                <input type="text" name="submitter_name" class="form-control" required value="{{ old('submitter_name') }}" placeholder="اسم الشخص الذي يقوم بتسجيل الطلب">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم تليفون المسجل <span class="text-danger">*</span></label>
                <input type="tel" name="submitter_phone" class="form-control" required value="{{ old('submitter_phone') }}" placeholder="01xxxxxxxxx">
              </div>
            </div>
          </div>

          <!-- Section 2: بيانات الحالة الأساسية -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-person-lines-fill me-2"></i> 2. بيانات الحالة (رب الأسرة / المستفيد)
            </h5>
            <div class="row g-3">
              <div class="col-12">
                <label class="form-label fw-bold small">اسم الحالة رباعي <span class="text-danger">*</span></label>
                <input type="text" name="applicant_name" class="form-control" required value="{{ old('applicant_name') }}" placeholder="الاسم رباعياً كما في بطاقة الرقم القومي">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم تليفون الحالة <span class="text-danger">*</span></label>
                <input type="tel" name="applicant_phone" class="form-control" required value="{{ old('applicant_phone') }}" placeholder="رقم الهاتف الأساسي للحالة">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم تليفون احتياطي</label>
                <input type="tel" name="backup_phone" class="form-control" value="{{ old('backup_phone') }}" placeholder="رقم بديل للوصول للحالة">
              </div>
            </div>
          </div>

          <!-- Section 3: المركز والعنوان بالتفصيل -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-geo-alt-fill me-2"></i> 3. المركز وتفاصيل العنوان
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">المركز <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control" required value="{{ old('city') }}" placeholder="مثال: مركز طنطا، كفر الشيخ، المحلة، قطور...">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">عدد أفراد الأسرة <span class="text-danger">*</span></label>
                <input type="number" name="family_members_count" class="form-control" required min="1" max="25" value="{{ old('family_members_count') }}" placeholder="إجمالي عدد المقيمين بالمنزل">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold small">العنوان بالتفصيل <span class="text-danger">*</span></label>
                <textarea name="address" class="form-control" rows="3" required placeholder="القرية / الشارع / علامة مميزة بجوار المنزل بالتفصيل">{{ old('address') }}</textarea>
              </div>
            </div>
          </div>

          <!-- Section 4: نوع الاحتياج والوصف الاجتماعي -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-droplet-half me-2"></i> 4. تحديد الاحتياج المطلوب للحالة
            </h5>
            
            <div class="row g-3 mb-3">
              <div class="col-md-4">
                <div class="form-check p-3 rounded-4 border bg-stats-card-main h-100">
                  <input class="form-check-input ms-2" type="radio" name="need_type" id="need_water" value="وصله مياه" {{ old('need_type') == 'وصله مياه' ? 'checked' : '' }} required>
                  <label class="form-check-label fw-bold cursor-pointer d-block" for="need_water">
                    <span class="text-primary fs-5 d-block mb-1"><i class="bi bi-water"></i> وصلة مياه</span>
                    <small class="text-muted">توصيل مياه شرب صالحة ونقية للمنزل</small>
                  </label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-check p-3 rounded-4 border bg-stats-card-main h-100">
                  <input class="form-check-input ms-2" type="radio" name="need_type" id="need_roof" value="سقف" {{ old('need_type') == 'سقف' ? 'checked' : '' }}>
                  <label class="form-check-label fw-bold cursor-pointer d-block" for="need_roof">
                    <span class="text-warning-emphasis fs-5 d-block mb-1"><i class="bi bi-house-door-fill"></i> سقف منزل</span>
                    <small class="text-muted">تسقيف لحماية الأسرة من الأمطار والشمس</small>
                  </label>
                </div>
              </div>

              <div class="col-md-4">
                <div class="form-check p-3 rounded-4 border bg-stats-card-main h-100">
                  <input class="form-check-input ms-2" type="radio" name="need_type" id="need_other" value="اخري" {{ old('need_type') == 'اخري' ? 'checked' : '' }}>
                  <label class="form-check-label fw-bold cursor-pointer d-block" for="need_other">
                    <span class="text-success fs-5 d-block mb-1"><i class="bi bi-stars"></i> احتياج آخر</span>
                    <small class="text-muted">صدقات جارية أو وقف خيري متنوع</small>
                  </label>
                </div>
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label fw-bold small">وصف الحالة الاجتماعية للحالة <span class="text-danger">*</span></label>
              <textarea name="social_status_description" class="form-control" rows="3" required placeholder="توضيح الوضع المعيشي، عمل رب الأسرة، الدخل، حالة المنزل والمأوى...">{{ old('social_status_description') }}</textarea>
            </div>
          </div>

          <!-- Section 5: بيانات الدليل والملاحظات -->
          <div class="mb-4">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-signpost-2-fill me-2"></i> 5. بيانات الدليل وملاحظات إضافية
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">اسم الدليل</label>
                <input type="text" name="guide_name" class="form-control" value="{{ old('guide_name') }}" placeholder="شخص معروف في المنطقة يدل على المنزل">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">رقم تليفون الدليل</label>
                <input type="tel" name="guide_phone" class="form-control" value="{{ old('guide_phone') }}" placeholder="رقم تليفون الدليل">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold small">ملاحظات على الحالة</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات هامة أو توضيحات إضافية تفيد فريق البحث الميداني">{{ old('notes') }}</textarea>
              </div>
            </div>
          </div>

          <!-- Confirmation and Submit -->
          <div class="p-3 bg-light rounded-4 border mb-4 text-center">
            <div class="form-check d-inline-block text-start">
              <input class="form-check-input" type="checkbox" id="consentCheck" required>
              <label class="form-check-label fw-bold small text-dark" for="consentCheck">
                أقر بصحة جميع البيانات المسجلة وأوافق على تواصل فريق مؤسسة إنسان الخيرية لإجراء المعاينة والبحث الميداني.
              </label>
            </div>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold fs-5 shadow-sm">
              <i class="bi bi-send-fill me-2"></i> إرسال الاستمارة لفريق إنسان
            </button>
          </div>
        </form>
      </div>

      <!-- Footer Info -->
      <div class="text-center text-muted small pb-4">
        مؤسسة إنسان الخيرية &copy; {{ date('Y') }} — جميع الحقوق محفوظة | منظومة تقديم الحالات الرقمية
      </div>

    </div>
  </div>
</div>

<style>
.cursor-pointer { cursor: pointer; }
.form-check-input:checked {
  background-color: #0284c7;
  border-color: #0284c7;
}
</style>
@endsection
