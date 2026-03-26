@extends('layouts.app')
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
          <div class="col-md-6">
            <label class="form-label">الاسم</label>
            <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}"
              required>
            @error('name')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-md-6">
            <label class="form-label">النوع</label>
            <select name="type" class="form-select" required>
              <option value="individual">فرد</option>
              <option value="organization">منظمة</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input type="tel" pattern="^(01[0125][0-9]{8})$" title="رقم هاتف مصري يبدأ بـ 010, 011, 012, 015"
              inputmode="numeric" name="phone" class="form-control @error('phone') is-invalid @enderror"
              value="{{ old('phone') }}" required>
            @error('phone')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>
          <div class="col-12">
            <label class="form-label">العنوان</label>
            <input name="address" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">تصنيف</label>
            <select name="classification" class="form-select" required>
              <option value="one_time">مرة واحدة</option>
              <option value="recurring">متكرر</option>
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">دورة التكرار</label>
            <select name="recurring_cycle" class="form-select">
              <option value="">—</option>
              <option value="monthly">شهري</option>
              <option value="yearly">سنوي</option>
            </select>
          </div>
          <div class="col-12">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="active" value="1" checked>
              <label class="form-check-label">نشط</label>
            </div>
          </div>
          <div class="col-md-6">
            <label class="form-label">نوع الكفالة/الصدقة</label>
            <select name="sponsorship_type" class="form-select" id="sType">
              <option value="none">—</option>
              <option value="monthly_sponsor">كافل شهري</option>
              <option value="sadaqa_jariya">صدقات جارية</option>
            </select>
          </div>
          <div class="col-md-6 s-fields" style="display:none">
            <label class="form-label">المشروع</label>
            <select name="sponsorship_project_id" class="form-select">
              @foreach($projects as $p)
                <option value="{{ $p->id }}" @selected($p->name === 'بعثاء الامل')>{{ $p->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6 s-fields" style="display:none">
            <label class="form-label">مبلغ الكفالة الشهري</label>
            <input name="sponsorship_monthly_amount" class="form-control" placeholder="مثال: 500.00">
          </div>
          <div class="col-md-6 s-fields" style="display:none">
            <label class="form-label">الطفل المكفول</label>
            <select name="sponsored_beneficiary_id" class="form-select">
              <option value="">—</option>
              @foreach($beneficiaries as $b)
                <option value="{{ $b->id }}">{{ $b->full_name }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="mt-3">
          <button class="btn btn-primary">حفظ</button>
          <a href="{{ route('donors.index') }}" class="btn btn-light">رجوع</a>
        </div>
      </form>
    </div>
    <script>
      (function () { var s = document.getElementById('sType'); function t() { var on = s.value !== 'none'; document.querySelectorAll('.s-fields').forEach(e => e.style.display = on ? 'block' : 'none'); } t(); s.addEventListener('change', t); })();
    </script>
@endsection