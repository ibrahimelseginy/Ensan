@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header mb-3">
    <h4 class="mb-0">
      <i class="bi bi-laptop text-primary"></i>
      تعديل مساحة العمل
    </h4>
    <a href="{{ route('workspaces.show', $workspace) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('workspaces.update', $workspace) }}">
        @csrf @method('PUT')
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">الاسم</label>
            <input name="name" class="form-control" value="{{ $workspace->name }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">الموقع</label>
            <input name="location" class="form-control" value="{{ $workspace->location }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">السعة</label>
            <input name="capacity" type="number" class="form-control" value="{{ $workspace->capacity }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">سعر الساعة</label>
            <input name="price_per_hour" type="number" step="0.01" class="form-control"
              value="{{ $workspace->price_per_hour }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">سعر اليوم</label>
            <input name="price_per_day" type="number" step="0.01" class="form-control"
              value="{{ $workspace->price_per_day }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">المدير المسؤول</label>
            <select name="manager_id" class="form-select">
              <option value="">-- اختر المدير --</option>
              @foreach($users as $user)
                <option value="{{ $user->id }}" @selected($workspace->manager_id == $user->id)>{{ $user->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">الحالة</label>
            <select name="status" class="form-select">
              <option value="available" @selected($workspace->status == 'available')>متاح</option>
              <option value="busy" @selected($workspace->status == 'busy')>مشغول</option>
              <option value="maintenance" @selected($workspace->status == 'maintenance')>صيانة</option>
            </select>
          </div>
          <div class="col-12">
            <label class="form-label">التجهيزات والمرافق (Amenities)</label>
            <input name="amenities" class="form-control" value="{{ $workspace->amenities }}"
              placeholder="بروجيكتور، واي فاي، تكييف، نظام صوتي...">
          </div>
          <div class="col-12">
            <label class="form-label">وصف</label>
            <textarea name="description" class="form-control" rows="3">{{ $workspace->description }}</textarea>
          </div>
        </div>
        <div class="mt-3 d-flex gap-2">
          <button class="btn btn-primary">حفظ التغييرات</button>
          <a href="{{ route('workspaces.show', $workspace) }}" class="btn btn-light">إلغاء</a>
        </div>
      </form>
    </div>
  </div>
@endsection

