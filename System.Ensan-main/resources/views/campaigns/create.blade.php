@extends('layouts.app')
@section('content')
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up bg-primary shadow-sm mb-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 2.5rem 2rem; border-radius: 0 0 40px 40px;">
        <div class="hero-content">
            <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">إضافة جديدة ➕</div>
            <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">إنشاء حملة جديدة</h1>
            <p class="hero-subtitle text-white opacity-75 mb-0" style="color: #ffffff !important;">قم بإدخال تفاصيل الحملة الموسمية أو التسويقية الجديدة للبدء في تتبعها.</p>
        </div>
        <div class="ms-auto">
            <a href="{{ route('campaigns.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift" style="border-width: 2px;">
                <i class="bi bi-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        <div class="glass-card border-0 shadow-sm p-5 animate-slide-up">
            <form method="POST" action="{{ route('campaigns.store') }}">
                @csrf

        {{-- Basic Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-info-circle"></i>
            <span>المعلومات الأساسية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">اسم الحملة</label>
              <input name="name" class="form-control" required placeholder="أدخل اسم الحملة">
            </div>
            <div class="col-md-3">
              <label class="form-label form-label-required">السنة</label>
              <input name="season_year" class="form-control" type="number" required value="{{ date('Y') }}" min="2020"
                max="2030">
            </div>
            <div class="col-md-3">
              <label class="form-label form-label-required">الحالة</label>
              <select name="status" class="form-select" required>
                <option value="active">نشط</option>
                <option value="archived">مؤرشف</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Dates Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-calendar-range"></i>
            <span>التواريخ</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">تاريخ البداية</label>
              <input name="start_date" class="form-control" type="date">
            </div>
            <div class="col-md-6">
              <label class="form-label">تاريخ النهاية</label>
              <input name="end_date" class="form-control" type="date">
            </div>
          </div>
        </div>

        {{-- Project Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-folder"></i>
            <span>الربط بمشروع</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">المشروع التابع له</label>
              <select name="project_id" class="form-select">
                <option value="">— اختر المشروع —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}">{{ $p->name }}</option>
                @endforeach
              </select>
              <div class="form-help-text">اختر المشروع الذي تنتمي له هذه الحملة</div>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> إنشاء الحملة
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

