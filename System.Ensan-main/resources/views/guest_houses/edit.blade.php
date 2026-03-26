@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header mb-3">
    <h4 class="mb-0">
      <i class="bi bi-house-heart text-primary"></i>
      تعديل دار الضيافة
    </h4>
    <a href="{{ route('guest-houses.show', $guest_house) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('guest-houses.update', $guest_house) }}">
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