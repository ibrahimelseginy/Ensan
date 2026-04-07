@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #3730a3 100%);">
    <div class="hero-content">
        <div class="hero-greeting">عملية جديدة ➕</div>
        <h1 class="hero-title">إغلاق مالي يومي</h1>
        <p class="hero-subtitle">تسجيل إغلاق مالي جديد للفرع أو المندوب</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('closures.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <i class="bi bi-file-earmark-lock hero-icon d-none d-md-block"></i>
</div>

<div class="row justify-content-center animate-slide-up animate-delay-1">
    <div class="col-md-10">
    <div class="card-body">
      <form method="POST" action="{{ route('closures.store') }}">
        @csrf
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label form-label-required">التاريخ</label>
            <input type="date" name="date" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">الفرع</label>
            <input name="branch" class="form-control">
          </div>
        </div>

        {{-- Approval Option --}}
        @if(request()->user() && (request()->user()->hasRole('admin') || request()->user()->hasRole('manager')))
          <div class="row g-3 mt-3">
            <div class="col-12">
              <label class="form-label fw-bold">الاعتماد</label>
              <div class="d-flex gap-3 align-items-center">
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="approved" id="approvedYes" value="1">
                  <label class="form-check-label text-success fw-bold" for="approvedYes">
                    <i class="bi bi-check-circle me-1"></i> نعم (اعتماد فوري)
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="radio" name="approved" id="approvedNo" value="0" checked>
                  <label class="form-check-label text-warning fw-bold" for="approvedNo">
                    <i class="bi bi-clock me-1"></i> لا (قيد المراجعة)
                  </label>
                </div>
              </div>
              <small class="text-muted">اختر "نعم" لاعتماد الإغلاق فوراً، أو "لا" ليبقى قيد المراجعة.</small>
            </div>
          </div>
        @endif
        <div class="d-flex gap-2 justify-content-end mt-4">
          <a href="{{ route('closures.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-lock me-1"></i> إغلاق اليوم
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

