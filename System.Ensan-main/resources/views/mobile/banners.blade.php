@extends('layouts.app')

@section('content')
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #7c3aed 0%, #a855f7 100%);">
    <div class="hero-content">
        <div class="hero-greeting text-white-50">إعلانات التطبيق 🖼️</div>
        <h1 class="hero-title">البنرات (Banners)</h1>
        <p class="hero-subtitle">إدارة الصور المتحركة والترويجية التي تظهر في الصفحة الرئيسية للتطبيق</p>
    </div>
    <div class="hero-actions">
        <button class="btn btn-light text-primary fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addBanner">
            إضافة بنر جديد <i class="bi bi-plus-lg ms-1"></i>
        </button>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="row g-4">
        @foreach($banners as $banner)
        <div class="col-md-6 col-lg-4">
            <div class="glass-card overflow-hidden animate-slide-up">
                <div class="position-relative" style="height: 180px;">
                    <img src="{{ $banner->image_url }}" class="w-100 h-100 object-fit-cover">
                    <div class="position-absolute top-0 end-0 m-2">
                        <span class="badge bg-dark bg-opacity-50 blur-10 px-2 py-1 x-small">الترتيب: {{ $banner->sort_order }}</span>
                    </div>
                </div>
                <div class="p-3">
                    <h6 class="fw-bold mb-1">{{ $banner->title ?? 'بدون عنوان' }}</h6>
                    <div class="x-small text-muted mb-3 d-flex align-items-center gap-1">
                        <i class="bi bi-link-45deg"></i>
                        @if($banner->link_type == 'project')
                            رابط لمشروع #{{ $banner->link_id }}
                        @elseif($banner->link_type == 'campaign')
                            رابط لحملة #{{ $banner->link_id }}
                        @elseif($banner->link_type == 'external')
                            رابط خارجي
                        @else
                            لا يوجد رابط
                        @endif
                    </div>
                    
                    <form action="{{ route('mobile.banners.destroy', $banner) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البنر؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100 rounded-pill">حذف البنر <i class="bi bi-trash"></i></button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addBanner" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.banners.store') }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">إضافة بنر إعلاني للتطبيق</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">عنوان البنر (اختياري)</label>
                    <input type="text" name="title" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الصورة (مقاس 16:9 مفضل)</label>
                    <input type="file" name="image" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label small fw-bold">نوع الرابط</label>
                        <select name="link_type" class="form-select form-select-sm">
                            <option value="">لا يوجد</option>
                            <option value="project">مشروع داخلي</option>
                            <option value="campaign">حملة تبرع</option>
                            <option value="external">رابط خارجي</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label small fw-bold">المعرف / الرابط</label>
                        <input type="text" name="link_id" class="form-control form-control-sm" placeholder="ID أو URL">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">ترتيب العرض</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">رفع البنر</button>
            </div>
        </form>
    </div>
</div>

<style>
    .glass-card { background: white; border-radius: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); border: 1px solid #f3f4f6; }
    .blur-10 { backdrop-filter: blur(10px); }
    .x-small { font-size: 0.7rem; }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>
@endsection


