@extends('layouts.app')

@section('content')
<div class="container py-4">
  <div class="row justify-content-center">
    <div class="col-lg-9 col-md-11">
      
      <div class="text-center mb-4">
        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1.5 fw-bold mb-2">
          مؤسسة إنسان الخيرية
        </span>
        <h2 class="fw-black text-dark mb-2">استمارة التقديم على مساعدة (البحث المبدئي)</h2>
        <p class="text-muted">نسعد باستقبال طلباتكم ليتم دراستها مبدئياً ومتابعتها من قِبل فريق البحث الاجتماعي بالمؤسسة</p>
      </div>

      @if(session('success'))
        <div class="alert alert-success border-0 shadow-sm rounded-4 p-4 mb-4 text-center">
          <i class="bi bi-check-circle-fill text-success fs-1 d-block mb-2"></i>
          <h5 class="fw-bold mb-1">تم إرسال طلبكم بنجاح!</h5>
          <p class="mb-0 text-muted">{{ session('success') }}</p>
        </div>
      @endif

      @if($errors->any())
        <div class="alert alert-danger border-0 shadow-sm rounded-4 p-3 mb-4">
          <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى مراجعة الحقول التالية:</div>
          <ul class="mb-0 small">
            @foreach($errors->all() as $err)
              <li>{{ $err }}</li>
            @endforeach
          </ul>
        </div>
      @endif

      <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-primary text-white p-3 p-md-4">
          <h5 class="fw-bold mb-0"><i class="bi bi-pencil-square me-2"></i> بيانات الطلب المبدئي</h5>
        </div>
        <div class="card-body p-4">
          <form method="POST" action="{{ route('cases.public-apply.store') }}">
            @csrf

            <!-- صفة مقدم الطلب -->
            <div class="mb-4 p-3 rounded-3 bg-light border">
              <label class="form-label fw-bold mb-2">من يقوم بتقديم هذا الطلب؟ <span class="text-danger">*</span></label>
              <div class="d-flex gap-4">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="submitter_type" id="subSelf" value="self" {{ old('submitter_type', 'self') === 'self' ? 'checked' : '' }} onchange="toggleSubmitterInfo()">
                  <label class="form-check-label fw-bold" for="subSelf">أقدم الطلب عن نفسي أو أسرتي</label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="submitter_type" id="subOther" value="other" {{ old('submitter_type') === 'other' ? 'checked' : '' }} onchange="toggleSubmitterInfo()">
                  <label class="form-check-label fw-bold" for="subOther">أقدم الطلب نيابة عن شخص آخر / فاعل خير</label>
                </div>
              </div>
            </div>

            <!-- بيانات مقدم الطلب لو كان غير صاحب الحالة -->
            <div id="submitterFields" class="mb-4 p-3 rounded-3 border border-primary bg-primary bg-opacity-10 {{ old('submitter_type') === 'other' ? '' : 'd-none' }}">
              <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-heart me-1"></i> بيانات الشخص مقدم الطلب</h6>
              <div class="row g-3">
                <div class="col-md-4">
                  <label class="form-label small fw-bold">اسمك بالكامل <span class="text-danger">*</span></label>
                  <input type="text" name="submitter_name" class="form-control" value="{{ old('submitter_name') }}" placeholder="اسم فاعل الخير أو مقدم الطلب">
                </div>
                <div class="col-md-4">
                  <label class="form-label small fw-bold">رقم هاتفك للتواصل <span class="text-danger">*</span></label>
                  <input type="tel" name="submitter_phone" class="form-control" value="{{ old('submitter_phone') }}" placeholder="01xxxxxxxxx">
                </div>
                <div class="col-md-4">
                  <label class="form-label small fw-bold">صلتك بالحالة</label>
                  <input type="text" name="submitter_relation" class="form-control" value="{{ old('submitter_relation') }}" placeholder="جار / قريب / متطوع...">
                </div>
              </div>
            </div>

            <!-- بيانات الحالة الأساسية -->
            <h6 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-person-vcard me-1 text-primary"></i> بيانات صاحب الحالة (المستفيد)</h6>
            <div class="row g-3 mb-4">
              <div class="col-md-6">
                <label class="form-label fw-bold">اسم المستفيد / ولي الأمر بالكامل <span class="text-danger">*</span></label>
                <input type="text" name="applicant_name" class="form-control" required value="{{ old('applicant_name') }}" placeholder="الاسم رباعياً">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">الرقم القومي (إن توفر)</label>
                <input type="text" name="applicant_id_number" class="form-control" maxlength="14" value="{{ old('applicant_id_number') }}" placeholder="14 رقم من بطاقة الرقم القومي">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">رقم الهاتف الأساسي <span class="text-danger">*</span></label>
                <input type="tel" name="applicant_phone" class="form-control" required value="{{ old('applicant_phone') }}" placeholder="01xxxxxxxxx">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">رقم هاتف بديل / إضافي</label>
                <input type="tel" name="backup_phone" class="form-control" value="{{ old('backup_phone') }}" placeholder="رقم هاتف آخر أو قريب">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">نوع المساعدة المطلوبة <span class="text-danger">*</span></label>
                <select name="case_type" class="form-select" required>
                  <option value="">— اختر نوع المساعدة —</option>
                  <option value="zad" {{ old('case_type') == 'zad' ? 'selected' : '' }}>كفالة أيتام (مشروع زاد)</option>
                  <option value="hope" {{ old('case_type') == 'hope' ? 'selected' : '' }}>مرضى أورام وعلاج (بعثاء الأمل)</option>
                  <option value="roof" {{ old('case_type') == 'roof' ? 'selected' : '' }}>سقف وترميم منازل</option>
                  <option value="financial" {{ old('case_type') == 'financial' ? 'selected' : '' }}>مساعدة مالية شهرية / عاجلة</option>
                  <option value="food" {{ old('case_type') == 'food' ? 'selected' : '' }}>سلة غذائية / مواد عينية</option>
                  <option value="medical" {{ old('case_type') == 'medical' ? 'selected' : '' }}>مساعدات طبية وأدوية وعمليات</option>
                  <option value="other" {{ old('case_type') == 'other' ? 'selected' : '' }}>أخرى</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">المحافظة <span class="text-danger">*</span></label>
                <input type="text" name="governorate" class="form-control" required value="{{ old('governorate') }}" placeholder="مثال: القاهرة / الجيزة / الإسكندرية">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold">المدينة أو المركز <span class="text-danger">*</span></label>
                <input type="text" name="city" class="form-control" required value="{{ old('city') }}" placeholder="مثال: أطفيح / الصف / العياط">
              </div>
              <div class="col-12">
                <label class="form-label fw-bold">العنوان التفصيلي <span class="text-danger">*</span></label>
                <input type="text" name="address" class="form-control" required value="{{ old('address') }}" placeholder="القرية أو المنطقة، الشارع، علامة مميزة">
              </div>
            </div>

            <!-- شرح الحالة -->
            <div class="mb-4">
              <label class="form-label fw-bold">وصف الحالة وظروف الأسرة واحتياجها <span class="text-danger">*</span></label>
              <textarea name="description" class="form-control" rows="4" required placeholder="اشرح لنا تفاصيل الوضع المعيشي، عدد أفراد الأسرة، الدخل إن وجد، والأسباب التي دعت لطلب المساعدة...">{{ old('description') }}</textarea>
            </div>

            <div class="text-center mt-4">
              <button type="submit" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                <i class="bi bi-send-fill me-2"></i> إرسال طلب البحث المبدئي
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</div>

<script>
function toggleSubmitterInfo() {
  const isOther = document.getElementById('subOther').checked;
  const fields = document.getElementById('submitterFields');
  if (isOther) {
    fields.classList.remove('d-none');
  } else {
    fields.classList.add('d-none');
  }
}
</script>
@endsection
