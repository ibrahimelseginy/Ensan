@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-person-plus text-primary"></i>
      إضافة مستفيد جديد
    </h4>
    <a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('beneficiaries.store') }}">
        @csrf

        {{-- Personal Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-person-lines-fill"></i>
            <span>البيانات الشخصية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">كود المستفيد</label>
              <input name="code" class="form-control" placeholder="مثال: A-101 (يترك فارغاً للتوليد التلقائي)">
              <div class="form-help-text">يترك فارغاً وسيتم إنشاؤه تلقائياً</div>
            </div>
            <div class="col-md-6">
              <label class="form-label form-label-required">الاسم الكامل</label>
              <input name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name') }}" required 
                placeholder="أدخل الاسم بالعربية">
              @error('full_name')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label">الرقم القومي</label>
              <input name="national_id" class="form-control"
                pattern="^[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{7}$" title="الرقم القومي 14 رقم بصيغة صحيحة"
                placeholder="14 رقم">
              <div class="form-help-text">الرقم القومي المصري المكون من 14 رقم</div>
            </div>
            <div class="col-md-6">
              <label class="form-label">رقم الهاتف</label>
              <input name="phone" class="form-control" pattern="^(01[0125]\d{8}|\+?201[0125]\d{8})$"
                title="ابدأ بـ 010 أو 011 أو 012 أو 015" placeholder="01xxxxxxxxx">
            </div>
            <div class="col-md-6">
              <label class="form-label">العنوان</label>
              <input name="address" class="form-control" placeholder="العنوان بالتفصيل">
            </div>
          </div>
        </div>

        {{-- Assistance Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-hand-thumbs-up"></i>
            <span>نوع المساعدة والتخصيص</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">نوع المساعدة</label>
              <select name="assistance_type" class="form-select" required>
                <option value="financial">مالية</option>
                <option value="in_kind">عينية</option>
                <option value="service">خدمية</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">المشروع</label>
              <select name="project_id" class="form-select">
                <option value="">— اختر المشروع —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">دار الضيافة</label>
              <select name="guest_house_id" class="form-select">
                <option value="">— اختر دار الضيافة —</option>
                @foreach($guestHouses as $gh)
                  <option value="{{ $gh->id }}">{{ $gh->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">الحملة</label>
              <select name="campaign_id" class="form-select">
                <option value="">— اختر الحملة —</option>
                @foreach($campaigns as $c)
                  <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->season_year }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">نوع التخصيص</label>
              <select name="allocation_type" class="form-select">
                <option value="">— اختر النوع —</option>
                <option value="شخص واحد">شخص واحد</option>
                <option value="أكثر من مستفيد">أكثر من مستفيد</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">التخصيص لكل طفل</label>
              <select name="child_sponsorship_type" class="form-select">
                <option value="">— اختر النوع —</option>
                <option value="كافل واحد">كافل واحد</option>
                <option value="أكثر من كافل">أكثر من كافل</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Notes Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-pencil-square"></i>
            <span>ملاحظات</span>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">ملاحظات داخلية</label>
              <textarea name="notes" class="form-control" rows="3" placeholder="أضف أي ملاحظات إضافية..."></textarea>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> حفظ المستفيد
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection