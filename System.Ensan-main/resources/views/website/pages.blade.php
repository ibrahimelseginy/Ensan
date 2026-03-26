@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="pages-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #0f172a;"></div>
            <div class="glow-orb-2" style="background: #06b6d4;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">الصفحات العامة</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-file-earmark-text-fill me-2"></i> إدارة المحتوى الديناميكي
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">الصفحات العامة</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        إنشاء وإدارة صفحات الموقع الإضافية
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <button class="btn btn-action-glow rounded-pill px-5 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#addPage">
                        <i class="bi bi-plus-lg me-2"></i> إضافة صفحة جديدة
                    </button>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    <div class="row g-4">
        @foreach($pages as $page)
        <div class="col-md-6 col-lg-4">
            <div class="glass-card p-4 animate-slide-up h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <h5 class="fw-bold mb-0 text-truncate">{{ $page->title }}</h5>
                        <span class="badge {{ $page->is_published ? 'bg-success' : 'bg-secondary' }} rounded-pill">
                            {{ $page->is_published ? 'منشور' : 'مسودة' }}
                        </span>
                    </div>
                    <p class="text-muted small mb-2"><i class="bi bi-link-45deg me-1"></i> /pages/{{ $page->slug }}</p>
                    <p class="text-muted small text-truncate">{{ Str::limit(strip_tags($page->content), 100) }}</p>
                </div>
                
                <div class="d-flex justify-content-end gap-2 mt-3 border-top pt-3">
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPage{{ $page->id }}">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </button>
                    <form action="{{ route('website.pages.destroy', $page->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الصفحة؟');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                            <i class="bi bi-trash me-1"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Edit Modal --}}
        <div class="modal fade" id="editPage{{ $page->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <form action="{{ route('website.pages.update', $page->id) }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
                    @csrf
                    @method('PUT')
                    <div class="modal-header border-0">
                        <h5 class="modal-title fw-bold">تعديل الصفحة: {{ $page->title }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4">
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label class="form-label small fw-bold">عنوان الصفحة</label>
                                <input type="text" name="title" class="form-control" value="{{ $page->title }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">Slug (الرابط)</label>
                                <input type="text" name="slug" class="form-control" value="{{ $page->slug }}" placeholder="auto-generated">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">محتوى الصفحة</label>
                                <textarea name="content" class="form-control" rows="10">{{ $page->content }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold">صورة الغلاف (اختياري)</label>
                                <input type="file" name="image" class="form-control">
                                @if($page->image_path)
                                    <div class="mt-2 text-muted x-small">صورة حالية: {{ $page->image_path }}</div>
                                @endif
                            </div>
                            
                            <hr>
                            <h6 class="fw-bold text-muted small">تحسين محركات البحث (SEO)</h6>
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold">Meta Title</label>
                                <input type="text" name="meta_title" class="form-control form-control-sm" value="{{ $page->meta_title }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold">Meta Keywords</label>
                                <input type="text" name="meta_keywords" class="form-control form-control-sm" value="{{ $page->meta_keywords }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label x-small fw-bold">Meta Description</label>
                                <textarea name="meta_description" class="form-control form-control-sm" rows="2">{{ $page->meta_description }}</textarea>
                            </div>
                            
                            <div class="col-md-6">
                                <label class="form-label small fw-bold">ترتيب الظهور</label>
                                <input type="number" name="sort_order" class="form-control" value="{{ $page->sort_order }}">
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div class="form-check form-switch mb-2">
                                    <input class="form-check-input" type="checkbox" name="is_published" id="editPub{{ $page->id }}" {{ $page->is_published ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold" for="editPub{{ $page->id }}">نشر الصفحة</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0">
                        <button type="submit" class="btn btn-info text-white rounded-pill px-5 shadow-sm">حفظ التعديلات</button>
                    </div>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>

{{-- Add Modal --}}
<div class="modal fade" id="addPage" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('website.pages.store') }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">إضافة صفحة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">عنوان الصفحة</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">Slug (الرابط)</label>
                        <input type="text" name="slug" class="form-control" placeholder="يترك فارغاً للتوليد التلقائي">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">محتوى الصفحة</label>
                        <textarea name="content" class="form-control" rows="10"></textarea>
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">صورة الغلاف (اختياري)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                    
                    <hr>
                    <h6 class="fw-bold text-muted small">تحسين محركات البحث (SEO)</h6>
                    <div class="col-md-6">
                        <label class="form-label x-small fw-bold">Meta Title</label>
                        <input type="text" name="meta_title" class="form-control form-control-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label x-small fw-bold">Meta Keywords</label>
                        <input type="text" name="meta_keywords" class="form-control form-control-sm">
                    </div>
                    <div class="col-12">
                        <label class="form-label x-small fw-bold">Meta Description</label>
                        <textarea name="meta_description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">ترتيب الظهور</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                    <div class="col-md-6 d-flex align-items-end">
                        <div class="form-check form-switch mb-2">
                            <input class="form-check-input" type="checkbox" name="is_published" id="addPub" checked>
                            <label class="form-check-label fw-bold" for="addPub">نشر الصفحة</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-info text-white rounded-pill px-5 shadow-sm">حفظ الصفحة</button>
            </div>
        </form>
    </div>
</div>

<style>
    body { background-color: #0b0e14 !important; }
    .pages-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #0f172a 0%, #334155 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #67e8f9; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .btn-action-glow { background: #06b6d4; color: white; border: none; font-weight: 700; box-shadow: 0 0 20px rgba(6,182,212,0.4); transition: 0.4s; }
    .btn-action-glow:hover { background: #0891b2; transform: translateY(-5px); box-shadow: 0 15px 30px rgba(6,182,212,0.6); color: white; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @media (max-width: 991px) { .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; } .display-4 { font-size: 2.2rem; } }

    .glass-card { background: rgba(255,255,255,0.9); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; box-shadow: 0 4px 20px rgba(0,0,0,0.05); }
    .x-small { font-size: 0.65rem; }
</style>
@endsection
