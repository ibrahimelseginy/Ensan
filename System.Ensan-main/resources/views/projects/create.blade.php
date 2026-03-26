@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-folder-plus text-primary"></i>
      إضافة مشروع جديد
    </h4>
    <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('projects.store') }}">
        @csrf

        {{-- Basic Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-info-circle"></i>
            <span>المعلومات الأساسية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">اسم المشروع</label>
              <input name="name" class="form-control" placeholder="أدخل اسم المشروع" required>
            </div>
            <div class="col-md-3">
              <label class="form-label form-label-required">الحالة</label>
              <select name="status" class="form-select" required>
                <option value="active">نشط</option>
                <option value="archived">مؤرشف</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="form-label">مشروع ثابت</label>
              <select name="fixed" class="form-select">
                <option value="1">نعم</option>
                <option value="0">لا</option>
              </select>
              <div class="form-help-text">المشاريع الثابتة تظهر دائماً في القوائم</div>
            </div>
          </div>
        </div>

        {{-- Description Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-card-text"></i>
            <span>الوصف</span>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">وصف المشروع</label>
              <textarea name="description" class="form-control" rows="4"
                placeholder="أضف وصفاً تفصيلياً للمشروع..."></textarea>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> إنشاء المشروع
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection