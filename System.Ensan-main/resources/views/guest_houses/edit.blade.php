@extends('layouts.app')
@section('content')
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up bg-primary shadow-sm mb-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 2.5rem 2rem; border-radius: 0 0 40px 40px;">
        <div class="hero-content">
            <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">تعديل البيانات ✏️</div>
            <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">تعديل دار الضيافة</h1>
            <p class="hero-subtitle text-white opacity-75 mb-0" style="color: #ffffff !important;">تحديث معلومات المرفق والمدير المسؤول لضمان دقة السجلات.</p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('guest-houses.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift" style="border-width: 2px;">
                <i class="bi bi-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        <div class="glass-card border-0 shadow-sm p-5 animate-slide-up">
            <form method="POST" action="{{ route('guest-houses.update', $guest_house) }}">
                @csrf @method('PUT')
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">الاسم</label>
            <input name="name" class="form-control" value="{{ $guest_house->name }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">الموقع</label>
            <input name="location" class="form-control" value="{{ $guest_house->location }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">الهاتف</label>
            <input name="phone" class="form-control" value="{{ $guest_house->phone }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">السعة</label>
            <input name="capacity" type="number" class="form-control" value="{{ $guest_house->capacity }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">المدير المسؤول</label>
            <select name="manager_user_id" class="form-select">
              <option value="">-- اختر المدير --</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}" @selected($guest_house->manager_user_id == $user->id)>{{ $user->name }}
                </option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select">
              <option value="active" @selected($guest_house->status === 'active')>نشط</option>
              <option value="archived" @selected($guest_house->status === 'archived')>مؤرشف</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">وصف</label>
            <textarea name="description" class="form-control" rows="3">{{ $guest_house->description }}</textarea>
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary">حفظ</button>
          <a href="{{ route('guest-houses.show', $guest_house) }}" class="btn btn-light">رجوع</a>
        </div>
      </form>
    </div>
  </div>
@endsection

