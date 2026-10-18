@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<div class="container py-4 min-vh-100">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">

      <!-- Header Card -->
      <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4" style="background: linear-gradient(135deg, #7c3aed 0%, #4f46e5 100%); color: #ffffff;">
        <div class="p-4 p-md-5 text-center position-relative">
          <div class="badge bg-white text-dark rounded-pill px-3.5 py-1.5 fw-bold mb-3 shadow-xs">
            مؤسسة إنسان الخيرية | مشهرة برقم 63 لسنة 2017
          </div>
          <h1 class="fw-black mb-2" style="font-size: 2rem;">إستمارة تقديم مشروع بعثاء الأمل (رعاية مرضى الأورام)</h1>
          <p class="mb-2 opacity-95 mx-auto fw-bold" style="max-width: 650px; font-size: 1.15rem; line-height: 1.7;">
            منظومة الدعم الطبي والاجتماعي الشامل لمرضى الأورام وذويهم
          </p>
          <p class="mb-0 opacity-80 mx-auto small">
            <strong>رؤيتنا :</strong> تقديم عمل خيري تطوعي بإحترافية مع الحفاظ علي كرامة الإنسان
          </p>
        </div>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
          <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
          <h4 class="fw-bold mb-2">تم استلام طلب الدعم الطبي بنجاح</h4>
          <p class="mb-0 text-muted fs-6">{{ session('success') }}</p>
          <div class="mt-3">
            <a href="{{ route('cases.apply-hope') }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold">تقديم استمارة جديدة</a>
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
        <form method="POST" action="{{ route('cases.apply-hope.store') }}" enctype="multipart/form-data">
          @csrf

          <!-- Section 1: بيانات المريض والمرافق -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-person-heart me-2"></i> 1. بيانات المريض وولي الأمر / المرافق
            </h5>
            <div class="row g-3">
              <div class="col-md-7">
                <label class="form-label fw-bold small">اسم المريض رباعياً <span class="text-danger">*</span></label>
                <input type="text" name="patient_name" class="form-control" required value="{{ old('patient_name') }}" placeholder="اسم مريض الأورام رباعياً كما في البطاقة">
              </div>
              <div class="col-md-5">
                <label class="form-label fw-bold small">الرقم القومي للمريض</label>
                <input type="text" name="national_id" class="form-control" maxlength="14" value="{{ old('national_id') }}" placeholder="14 رقم قومي">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">اسم المرافق / ولي الأمر</label>
                <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name') }}" placeholder="اسم من يتابع الحالة وصلة قرابته">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small">رقم تليفون 1 للتواصل <span class="text-danger">*</span></label>
                <input type="tel" name="phone_1" class="form-control" required value="{{ old('phone_1') }}" placeholder="01xxxxxxxxx">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small">رقم تليفون 2</label>
                <input type="tel" name="phone_2" class="form-control" value="{{ old('phone_2') }}" placeholder="رقم بديل">
              </div>
            </div>
          </div>

          <!-- Section 2: العنوان وتفاصيل الوصول -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-geo-alt-fill me-2"></i> 2. العنوان وموقع السكن
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">المحافظة والمركز / المدينة <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control" required value="{{ old('city') }}" placeholder="مثال: الغربية - طنطا / كفر الشيخ...">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">الموقف والمواصلة للوصول للمنزل</label>
                <input type="text" name="transit_details" class="form-control" value="{{ old('transit_details') }}" placeholder="مثال: موقف كذا ثم ركوب سيارة كذا...">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold small">العنوان بالتفصيل <span class="text-danger">*</span></label>
                <textarea name="address" class="form-control" rows="2" required placeholder="القرية / الحي / اسم الشارع / علامة مميزة">{{ old('address') }}</textarea>
              </div>
            </div>
          </div>

          <!-- Section 3: البيانات الطبية ومتابعة الورم -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-hospital-fill me-2"></i> 3. التفاصيل الطبية وموقع الورم
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">المستشفى المتابع بها <span class="text-danger">*</span></label>
                <input type="text" name="hospital_name" class="form-control" required value="{{ old('hospital_name') }}" placeholder="مثال: معهد أورام طنطا، 57357، مستشفى الدمرداش...">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">مكان ونوع الورم بالجسم <span class="text-danger">*</span></label>
                <input type="text" name="tumor_location" class="form-control" required value="{{ old('tumor_location') }}" placeholder="مثال: ورم بالثدي، ورم بالمخ، لوكيميا دم، القولون...">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold small">البروتوكول العلاجي الحالي وخطة العلاج</label>
                <textarea name="treatment_protocol" class="form-control" rows="2" placeholder="توضيح الأدوية الموصوفة، جرعات العلاج الكيماوي، المتابعة الدورية...">{{ old('treatment_protocol') }}</textarea>
              </div>
            </div>
          </div>

          <!-- Section 4: الجلسات والفحوصات -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-activity me-2"></i> 4. جلسات العلاج (الكيماوي والإشعاعي)
            </h5>
            <div class="row g-3">
              <div class="col-md-3">
                <label class="form-label fw-bold small">جلسات كيماوي تم أخذها</label>
                <input type="text" name="chemo_sessions_taken" class="form-control" value="{{ old('chemo_sessions_taken') }}" placeholder="مثال: 4 جلسات">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small">جلسات كيماوي متبقية</label>
                <input type="text" name="chemo_sessions_remaining" class="form-control" value="{{ old('chemo_sessions_remaining') }}" placeholder="مثال: 2 جلسة">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small">جلسات إشعاع تم أخذها</label>
                <input type="text" name="radiation_sessions_taken" class="form-control" value="{{ old('radiation_sessions_taken') }}" placeholder="مثال: 10 جلسات">
              </div>
              <div class="col-md-3">
                <label class="form-label fw-bold small">جلسات إشعاع متبقية</label>
                <input type="text" name="radiation_sessions_remaining" class="form-control" value="{{ old('radiation_sessions_remaining') }}" placeholder="مثال: 5 جلسات">
              </div>
            </div>
          </div>

          <!-- Section 5: المرفقات والمستندات -->
          <div class="mb-4 pb-3 border-bottom">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-paperclip me-2"></i> 5. إرفاق المستندات الطبية (اختياري / يفضل إرفاقها لسرعة الفحص)
            </h5>
            <div class="row g-3">
              <div class="col-md-6">
                <label class="form-label fw-bold small">صورة بطاقة الرقم القومي أو شهادة الميلاد</label>
                <input type="file" name="id_image" class="form-control" accept="image/*,application/pdf">
                <small class="text-muted">صور JPG، PNG أو ملف PDF (بحد أقصى 10 ميجا)</small>
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold small">صورة التقرير الطبي الحديث أو الأشعة</label>
                <input type="file" name="medical_report" class="form-control" accept="image/*,application/pdf">
                <small class="text-muted">تقرير المستشفى أو المعمل يوضح تشخيص الورم</small>
              </div>
            </div>
          </div>

          <!-- Section 6: ملاحظات إضافية -->
          <div class="mb-4">
            <h5 class="fw-bold text-primary mb-3">
              <i class="bi bi-chat-left-text me-2"></i> 6. ملاحظات إضافية والاحتياج المطلوب
            </h5>
            <div class="col-12">
              <label class="form-label fw-bold small">ملاحظات وطلبات الأسرة</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="توضيح أي ظروف معيشية خاصة، تكاليف التحاليل والعلاج، أو احتياج لمسكن بجوار المستشفى">{{ old('notes') }}</textarea>
            </div>
          </div>

          <!-- Consent -->
          <div class="p-3 bg-light rounded-4 border mb-4 text-center">
            <div class="form-check d-inline-block text-start">
              <input class="form-check-input" type="checkbox" id="hopeConsentCheck" required>
              <label class="form-check-label fw-bold small text-dark" for="hopeConsentCheck">
                أقر بصحة البيانات الطبية والشخصية المدخلة وأوافق على مراجعة فريق مؤسسة إنسان الخيرية لترتيب أوجه الدعم المناسبة.
              </label>
            </div>
          </div>

          <div class="text-center">
            <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold fs-5 shadow-sm" style="background: #6d28d9; border-color: #6d28d9;">
              <i class="bi bi-send-fill me-2"></i> إرسال الاستمارة لفريق رعاية الأورام
            </button>
          </div>
        </form>
      </div>

      <!-- Footer Info -->
      <div class="text-center text-muted small pb-4">
        مؤسسة إنسان الخيرية &copy; {{ date('Y') }} — مشروع بعثاء الأمل لرعاية مرضى الأورام | منظومة التقديم الرقمية
      </div>

    </div>
  </div>
</div>
@endsection
