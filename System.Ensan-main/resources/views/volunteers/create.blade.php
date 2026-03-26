@extends('layouts.app')
@section('content')
  <div class="mx-auto" style="max-width: 820px">
    {{-- Page Header --}}
    <div class="page-header mb-4">
      <h4 class="mb-0">
        <i class="bi bi-person-hearts text-primary"></i>
        إضافة متطوع جديد
      </h4>
      <a href="{{ route('volunteers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i> رجوع
      </a>
    </div>

    <div class="card">
      <div class="card-body">
        @if ($errors->any())
          <div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i> الرجاء تصحيح الأخطاء ثم المحاولة
            مرة أخرى.</div>
        @endif
        <form method="POST" action="{{ route('volunteers.store') }}" enctype="multipart/form-data">@csrf
          <div class="row g-3">
            <div class="col-md-6"><label class="form-label">الاسم</label><input type="text" name="name"
                class="form-control" required autofocus placeholder="مثال: محمد أحمد" value="{{ old('name') }}"></div>
            <div class="col-md-6"><label class="form-label">البريد الإلكتروني</label><input type="email" name="email"
                class="form-control" required placeholder="example@mail.com" value="{{ old('email') }}"></div>
            <div class="col-md-6"><label class="form-label">كلمة المرور</label>
              <div class="input-group">
                <input type="password" name="password" class="form-control" required placeholder="••••••">
                <button class="btn btn-outline-secondary" type="button" id="genPwdBtn">توليد</button>
              </div>
            </div>
            <div class="col-md-6"><label class="form-label">الهاتف</label><input type="text" name="phone"
                class="form-control" placeholder="010xxxxxxxx" pattern="^[0-9٠-٩\+\-\s]+$" value="{{ old('phone') }}">
            </div>

            <div class="col-md-6"><label class="form-label">الكلية</label><input type="text" name="college"
                class="form-control" value="{{ old('college') }}"></div>
            <div class="col-md-6">
              <label class="form-label">الصورة الشخصية</label>
              <div class="d-flex align-items-center gap-3">
                <div class="profile-photo-preview" id="photoPreview" style="width: 80px; height: 80px; border-radius: 50%; overflow: hidden; border: 3px solid #e9ecef; background: #f8f9fa; display: flex; align-items: center; justify-content: center;">
                  <i class="bi bi-person-circle" style="font-size: 3rem; color: #adb5bd;"></i>
                </div>
                <div class="flex-grow-1">
                  <input type="file" name="profile_photo" class="form-control" id="profilePhotoInput">
                  <small class="text-muted d-block mt-1">
                    <i class="bi bi-info-circle me-1"></i>اختر الصورة الشخصية (أي نوع وأي حجم)
                  </small>
                </div>
              </div>
            </div>
            <div class="col-md-3"><label class="form-label">المحافظة</label><input type="text" name="governorate"
                class="form-control" value="{{ old('governorate') }}"></div>
            <div class="col-md-3"><label class="form-label">المدينة</label><input type="text" name="city"
                class="form-control" value="{{ old('city') }}"></div>

            <div class="col-md-6">
              <label class="form-label">ينتمي إلى</label>
              <div class="input-group mb-2">
                <select class="form-select" id="affiliationType">
                  <option value="">-- اختر النوع --</option>
                  <option value="project" {{ old('project_id') ? 'selected' : '' }}>مشروع</option>
                  <option value="campaign" {{ old('campaign_id') ? 'selected' : '' }}>حملة</option>
                  <option value="guest_house" {{ old('guest_house_id') ? 'selected' : '' }}>دار ضيافة</option>
                </select>
              </div>
              <select name="project_id" class="form-select affiliation-select" id="projectSelect" style="display:none">
                <option value="">-- اختر المشروع --</option>
                @foreach($projects as $p) <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>
                {{ $p->name }}</option> @endforeach
              </select>
              <select name="campaign_id" class="form-select affiliation-select" id="campaignSelect" style="display:none">
                <option value="">-- اختر الحملة ({{ count($campaigns) }}) --</option>
                @foreach($campaigns as $c) <option value="{{ $c->id }}" {{ old('campaign_id') == $c->id ? 'selected' : '' }}>{{ $c->name }}</option> @endforeach
              </select>
              <select name="guest_house_id" class="form-select affiliation-select" id="guestHouseSelect"
                style="display:none">
                <option value="">-- اختر الدار --</option>
                @foreach($guestHouses as $gh) <option value="{{ $gh->id }}" {{ old('guest_house_id') == $gh->id ? 'selected' : '' }}>{{ $gh->name }}</option> @endforeach
              </select>
            </div>

            <div class="col-md-6"><label class="form-label">الوظيفة في المشروع</label><input type="text"
                name="project_role" class="form-control" value="{{ old('project_role') }}"></div>
            <div class="col-md-6"><label class="form-label">عدد ساعات التطوع في الأسبوع</label><input type="number"
                step="0.1" name="volunteer_hours" class="form-control" value="{{ old('volunteer_hours', 0) }}"></div>
            <div class="col-md-6"><label class="form-label">تاريخ الانضمام</label><input type="date" name="join_date"
                class="form-control" value="{{ old('join_date', date('Y-m-d')) }}"></div>

            <div class="col-md-6">
              <div class="form-check mt-4"><input class="form-check-input" type="checkbox" name="active" value="1"
                  id="active" {{ old('active', '1') ? 'checked' : '' }}><label class="form-check-label"
                  for="active">نشط</label></div>
            </div>
          </div>
          <div class="mt-3"><button class="btn btn-primary">حفظ</button><a href="{{ route('volunteers.index') }}"
              class="btn btn-light">إلغاء</a></div>
        </form>
      </div>
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        var btn = document.getElementById('genPwdBtn');
        if (btn) { btn.addEventListener('click', function () { var f = btn.previousElementSibling; var s = Math.random().toString(36).slice(2) + Math.random().toString(36).slice(2); f.value = s.slice(0, 12); f.type = 'text'; setTimeout(function () { f.type = 'password'; }, 1500); }); }

        var affType = document.getElementById('affiliationType');
        var toggleAff = function () {
          var val = affType.value;
          document.querySelectorAll('.affiliation-select').forEach(el => el.style.display = 'none');
          if (val === 'project') document.getElementById('projectSelect').style.display = 'block';
          if (val === 'campaign') document.getElementById('campaignSelect').style.display = 'block';
          if (val === 'guest_house') document.getElementById('guestHouseSelect').style.display = 'block';
        };
        if (affType) {
          affType.addEventListener('change', toggleAff);
          // Init based on old value
          if ("{{ old('project_id') }}") affType.value = 'project';
          if ("{{ old('campaign_id') }}") affType.value = 'campaign';
          if ("{{ old('guest_house_id') }}") affType.value = 'guest_house';
          toggleAff();
        }

        // Profile photo preview
        var photoInput = document.getElementById('profilePhotoInput');
        var photoPreview = document.getElementById('photoPreview');
        if (photoInput && photoPreview) {
          photoInput.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
              var reader = new FileReader();
              reader.onload = function(event) {
                photoPreview.innerHTML = '<img src="' + event.target.result + '" style="width: 100%; height: 100%; object-fit: cover;">';
              };
              reader.readAsDataURL(file);
            }
          });
        }
      });
    </script>
@endsection