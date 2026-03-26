@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header mb-3">
    <h4 class="mb-0">
      <i class="bi bi-laptop text-primary"></i>
      إضافة مساحة عمل جديدة
    </h4>
    <a href="{{ route('workspaces.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('workspaces.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">الاسم</label>
            <input name="name" class="form-control" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">الموقع</label>
            <input name="location" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">السعة</label>
            <input name="capacity" type="number" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">سعر الساعة</label>
            <input name="price_per_hour" type="number" step="0.01" class="form-control">
          </div>
          <div class="col-md-3">
            <label class="form-label">سعر اليوم</label>
            <input name="price_per_day" type="number" step="0.01" class="form-control">
          </div>
          <div class="col-md-6">
            <label class="form-label">المدير المسؤول</label>
            <select name="manager_id" class="form-select">
              <option value="">-- اختر المدير --</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select">
              <option value="available">متاح</option>
              <option value="busy">مشغول</option>
              <option value="maintenance">صيانة</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">التجهيزات والمرافق (Amenities)</label>
            <input name="amenities" class="form-control" placeholder="بروجيكتور، واي فاي، تكييف، نظام صوتي...">
          </div>
          <div class="col-12">
            <label class="form-label">وصف</label>
            <textarea name="description" class="form-control" rows="3"></textarea>
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary">حفظ</button>
          <a href="{{ route('workspaces.index') }}" class="btn btn-light">رجوع</a>
        </div>
      </form>
    </div>
  </div>
@endsection