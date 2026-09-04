@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-megaphone text-primary"></i>
      تعديل الحملة
    </h4>
    <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('campaigns.update', $campaign) }}">
        @csrf @method('PUT')

        {{-- Basic Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-info-circle"></i>
            <span>المعلومات الأساسية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">اسم الحملة</label>
              <input name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $campaign->name) }}" required>
              @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
              <label class="form-label form-label-required">السنة</label>
              <input name="season_year" class="form-control" type="number" min="2000" max="2100" value="{{ old('season_year', $campaign->season_year) }}" required>
            </div>
            <div class="col-md-3">
              <label class="form-label form-label-required">الحالة</label>
              <select name="status" class="form-select" required>
                <option value="active" @selected(old('status', $campaign->status) === 'active')>نشط</option>
                <option value="archived" @selected(old('status', $campaign->status) === 'archived')>مؤرشف</option>
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
              <input name="start_date" class="form-control" type="date"
                value="{{ old('start_date', $campaign->start_date?->format('Y-m-d')) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">تاريخ النهاية</label>
              <input name="end_date" class="form-control @error('end_date') is-invalid @enderror" type="date" value="{{ old('end_date', $campaign->end_date?->format('Y-m-d')) }}">
              @error('end_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
                  <option value="{{ $p->id }}" @selected(old('project_id', $campaign->project_id) == $p->id)>{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('campaigns.show', $campaign) }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> حفظ التغييرات
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection

