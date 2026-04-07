@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="news-mgmt-page bg-theme-page min-vh-100">
    {{-- Elite Hero Section --}}
    <div class="elite-hero-broadcast bg-premium-gradient overflow-hidden position-relative mb-5">
        <div class="hero-bg-visuals">
            <div class="glow-orb-purple"></div>
            <div class="glow-orb-cyan"></div>
            <div class="noise-texture"></div>
        </div>
        
        <div class="container-fluid px-4 position-relative z-index-10 py-5">
            <div class="row align-items-center">
                <div class="col-lg-8 text-end animate-revealer">
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <span class="badge-elite-status">
                            <span class="status-dot"></span> إدارة المحتوى المنفصل
                        </span>
                    </div>
                    <h1 class="display-3 fw-900 text-white mb-3">أخبار التطبيق <span class="text-gradient-cyan">(News)</span></h1>
                    <p class="lead text-azure-mist font-outfit mb-0 max-w-600 ms-auto text-end opacity-90">
                        تحكم كامل في القصص والفعاليات التي تصل حصرياً لمستخدمي تطبيق الهواتف الذكية بنظام Ensan.
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0">
                    <button class="btn btn-elite-action rounded-pill px-5 py-3 fw-bold" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="bi bi-plus-circle-dotted me-2 fs-5"></i> إضافة خبر للموبايل
                    </button>
                </div>
            </div>
        </div>
    </div>


<div class="container-fluid px-4 pb-5">
    <div class="row g-4">
        @forelse($news as $item)
        <div class="col-md-6 col-xl-4">
            <div class="broadcast-card-elite bg-stats-card-main border-light-subtle rounded-5 shadow-sm overflow-hidden hover-lift transition-all">
                <div class="card-art position-relative">
                    @if($item->image_path)
                        <img src="{{ $item->image_url }}" class="w-100 object-fit-cover" style="height: 240px;">
                    @else
                        <div class="no-image-placeholder d-flex align-items-center justify-content-center bg-stats-inner-item" style="height: 240px;">
                            <i class="bi bi-newspaper display-4 text-muted-theme"></i>
                        </div>
                    @endif
                    
                    <div class="card-overlay-actions">
                        <div class="d-flex gap-2 p-3">
                            <button class="btn-icon-elite bg-primary bg-opacity-25" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <form action="{{ route('mobile.news.destroy', $item) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-elite bg-danger bg-opacity-25" onclick="return confirm('تأكيد حذف هذا الخبر؟')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @if($item->category)
                        <span class="elite-category-tag">{{ $item->category }}</span>
                    @endif
                </div>

                <div class="card-content-suite p-4">
                    <div class="card-meta d-flex justify-content-between align-items-center mb-3">
                        <span class="badge bg-stats-inner-item border border-light-subtle text-muted-theme rounded-pill px-3 py-1 x-small fw-bold">
                            <i class="bi bi-calendar3 me-1"></i> {{ $item->published_at ? $item->published_at->format('M d, Y') : 'قيد المراجعة' }}
                        </span>
                        <div class="stats-group-elite d-flex gap-2">
                            <span class="badge-mini-stat bg-tag-primary"><i class="bi bi-eye-fill"></i> {{ $item->views_count ?? 0 }}</span>
                            <span class="badge-mini-stat bg-tag-success"><i class="bi bi-share-fill"></i> {{ $item->shares_count ?? 0 }}</span>
                        </div>
                    </div>

                    <h5 class="fw-bold text-stats-main mb-3 lh-base">{{ Str::limit($item->title, 60) }}</h5>
                    <p class="text-muted-theme small mb-4 line-clamp-2">{{ Str::limit($item->content, 120) }}</p>

                    @if($item->statistic_number)
                    <div class="highlight-metric-box mb-4 bg-stats-inner-item border border-light-subtle rounded-4 p-3 d-flex align-items-center gap-3">
                        <div class="metric-val text-primary fw-900 fs-4">{{ $item->statistic_number }}</div>
                        <div class="metric-desc text-muted-theme small leading-tight">{{ $item->statistic_description }}</div>
                    </div>
                    @endif

                    <div class="d-grid mt-auto">
                        <button class="btn btn-outline-primary rounded-pill py-2 fw-bold transition-all" data-bs-toggle="modal" data-bs-target="#viewNewsModal{{ $item->id }}">
                            التفاصيل الكاملة <i class="bi bi-chevron-left ms-2 x-small"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- ===== Elite News Composer (Edit) ===== --}}
        <div class="modal fade" id="editNewsModal{{ $item->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <form action="{{ route('mobile.news.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg modal-glass-theme rounded-5 overflow-hidden">
                    @csrf @method('PUT')
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-main"><i class="bi bi-pencil-square me-2 text-primary"></i> تحرير خبر الموبايل</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-stats-card-main">
                        <div class="row g-4">
                            <div class="col-md-9">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">عنوان الخبر المميز</label>
                                <input type="text" name="title" class="form-control premium-field f-lg" required value="{{ $item->title }}">
                            </div>
                            <div class="col-md-3">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">تصنيف النشر</label>
                                <select name="category" class="form-select premium-field">
                                    @foreach(\App\Models\MobileNews::getCategories() as $cat)
                                        <option value="{{ $cat['id'] }}" {{ $item->category == $cat['id'] ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="col-12">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">نص المحتوى (البرودكاست)</label>
                                <textarea name="content" class="form-control premium-field" rows="6" required>{{ $item->content }}</textarea>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-stats-inner-item border border-light-subtle rounded-4 p-4">
                                    <h6 class="fw-bold text-stats-main mb-3"><i class="bi bi-graph-up text-primary me-2"></i> مقاييس الأداء</h6>
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="x-small text-muted-theme mb-1">المشاهدات</label>
                                            <input type="text" name="views_count" class="form-control premium-field" value="{{ $item->views_count }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="x-small text-muted-theme mb-1">المشاركات</label>
                                            <input type="text" name="shares_count" class="form-control premium-field" value="{{ $item->shares_count }}">
                                        </div>
                                        <div class="col-12">
                                            <label class="x-small text-muted-theme mb-1">تاريخ الجدولة</label>
                                            <input type="date" name="published_at" class="form-control premium-field" value="{{ $item->published_at ? $item->published_at->format('Y-m-d') : date('Y-m-d') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="bg-stats-inner-item border border-light-subtle rounded-4 p-4">
                                    <h6 class="fw-bold text-stats-main mb-3"><i class="bi bi-image text-primary me-2"></i> الوسائط المرفقة</h6>
                                    <div class="upload-zone-elite position-relative text-center py-4 border border-dashed border-light-subtle rounded-4 mb-3">
                                        <input type="file" name="image" class="absolute-opacity-zero w-100 h-100 cursor-pointer">
                                        <i class="bi bi-cloud-arrow-up display-5 text-primary opacity-50"></i>
                                        <p class="small text-muted-theme mt-2">اسحب الصورة هنا أو اضغط للاختيار</p>
                                    </div>
                                    @if($item->image_path)
                                        <div class="preview-mini rounded-3 border border-light-subtle overflow-hidden">
                                            <img src="{{ $item->image_url }}" class="w-100 h-auto">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="bg-primary bg-opacity-10 border border-primary border-opacity-10 rounded-4 p-3 d-flex align-items-center gap-3">
                                    <div class="icon-orb bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-star"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="row g-2">
                                            <div class="col-md-4">
                                                <input type="text" name="statistic_number" class="form-control bg-white border-0 py-2 x-small" value="{{ $item->statistic_number }}" placeholder="الإحصائية (مثلاً: 95%)">
                                            </div>
                                            <div class="col-md-8">
                                                <input type="text" name="statistic_description" class="form-control bg-white border-0 py-2 x-small" value="{{ $item->statistic_description }}" placeholder="وصف الإحصائية المحفز">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 bg-stats-header">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm w-100">تحديث ونشر الخبر</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- ===== View Modal (per item) ===== --}}
        <div class="modal fade" id="viewNewsModal{{ $item->id }}" tabindex="-1" style="z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content dark-glass-card border-0 shadow-lg overflow-hidden" style="border-radius: 20px; background-color: var(--ws-bg-card-header) !important;">
                    <div class="modal-header border-0" style="border-bottom: 1px solid rgba(255,255,255,0.1) !important;">
                        <h5 class="modal-title fw-bold text-white">{{ $item->title }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-0">
                        @if($item->image_path)
                            <img src="{{ $item->image_url }}" class="w-100 object-fit-cover" style="height: 300px;">
                        @endif
                        <div class="p-4">
                            <div class="d-flex gap-3 ws-label small mb-4 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.1) !important;">
                                <span><i class="bi bi-calendar3 text-purple me-1"></i> {{ $item->published_at ? $item->published_at->format('Y-m-d') : '' }}</span>
                                <span><i class="bi bi-folder text-purple me-1"></i> {{ $item->category ?? 'عام' }}</span>
                                <span><i class="bi bi-eye text-purple me-1"></i> {{ $item->views_count ?? 0 }} مشاهدة</span>
                            </div>
                            <div class="text-white lh-lg" style="white-space: pre-wrap;">{{ $item->content }}</div>
                        </div>
                    </div>
                    <div class="modal-footer border-0" style="border-top: 1px solid rgba(255,255,255,0.1) !important;">
                        <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                        <button type="button" class="btn btn-warning rounded-pill px-4 fw-bold" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}">
                            <i class="bi bi-pencil me-2"></i>تعديل
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @empty
        <div class="col-12">
            <div class="text-center py-5">
                <i class="bi bi-newspaper display-1 text-white-50"></i>
                <p class="text-white-50 mt-3">لا توجد أخبار بعد. أضف أول خبر الآن!</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

{{-- ===== Elite News Composer (Add) ===== --}}
<div class="modal fade" id="addNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('mobile.news.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg modal-glass-theme rounded-5 overflow-hidden">
            @csrf
            <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                <h5 class="modal-title fw-bold text-stats-main"><i class="bi bi-broadcast me-2 text-primary"></i> بث خبر جديد للموبايل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-stats-card-main">
                <div class="row g-4">
                    <div class="col-md-9">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">عنوان الخبر المميز</label>
                        <input type="text" name="title" class="form-control premium-field f-lg" placeholder="اكتب عنواناً جذاباً هنا..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">تصنيف النشر</label>
                        <select name="category" class="form-select premium-field">
                            @foreach(\App\Models\MobileNews::getCategories() as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="col-12">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">نص المحتوى (البرودكاست)</label>
                        <textarea name="content" class="form-control premium-field" rows="6" placeholder="ما هي القصة التي تريد مشاركتها؟" required></textarea>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-stats-inner-item border border-light-subtle rounded-4 p-4">
                            <h6 class="fw-bold text-stats-main mb-3"><i class="bi bi-graph-up text-primary me-2"></i> مقاييس الأداء الافتراضية</h6>
                            <div class="row g-3">
                                <div class="col-6">
                                    <label class="x-small text-muted-theme mb-1">المشاهدات</label>
                                    <input type="text" name="views_count" class="form-control premium-field" placeholder="مثلاً: 12K">
                                </div>
                                <div class="col-6">
                                    <label class="x-small text-muted-theme mb-1">المشاركات</label>
                                    <input type="text" name="shares_count" class="form-control premium-field" placeholder="مثلاً: 500">
                                </div>
                                <div class="col-12">
                                    <label class="x-small text-muted-theme mb-1">تاريخ الجدولة</label>
                                    <input type="date" name="published_at" class="form-control premium-field" value="{{ date('Y-m-d') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="bg-stats-inner-item border border-light-subtle rounded-4 p-4 h-100">
                            <h6 class="fw-bold text-stats-main mb-3"><i class="bi bi-image text-primary me-2"></i> الوسائط المرفقة</h6>
                            <div class="upload-zone-elite position-relative text-center py-5 border border-dashed border-light-subtle rounded-4 mb-3 d-flex flex-column align-items-center justify-content-center">
                                <input type="file" name="image" class="absolute-opacity-zero w-100 h-100 cursor-pointer">
                                <i class="bi bi-cloud-arrow-up display-4 text-primary opacity-50 mb-2"></i>
                                <p class="small text-muted-theme px-3">اسحب صورة الخبر هنا أو انقر لاختيار ملف</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-12">
                        <div class="bg-primary bg-opacity-10 border border-primary border-opacity-10 rounded-4 p-3 d-flex align-items-center gap-3">
                            <div class="icon-orb bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                <i class="bi bi-star"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="row g-2">
                                    <div class="col-md-4">
                                        <input type="text" name="statistic_number" class="form-control bg-white border-0 py-2 x-small" placeholder="الإحصائية (مثلاً: 95%)">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="text" name="statistic_description" class="form-control bg-white border-0 py-2 x-small" placeholder="وصف الإحصائية المحفز">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-stats-header">
                <button type="submit" class="btn btn-primary rounded-pill px-5 py-3 fw-bold shadow-sm w-100">بث الخبر الآن</button>
            </div>
        </form>
    </div>
</div>
</div>

<style>
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-900 { font-weight: 900; }
    .font-outfit { font-family: 'Outfit', sans-serif; }
    .z-index-10 { z-index: 10; }

    /* Elite Hero Styles */
    .elite-hero-broadcast { min-height: 380px; display: flex; align-items: center; border-radius: 0 0 80px 80px; box-shadow: 0 20px 60px rgba(0,0,0,0.25); }
    .bg-premium-gradient { background: linear-gradient(135deg, #101828 0%, #1e293b 50%, #0f172a 100%); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(120px); opacity: 0.35; pointer-events: none; }
    .glow-orb-purple { width: 450px; height: 450px; top: -150px; left: -100px; background: #6366f1; }
    .glow-orb-cyan { width: 350px; height: 350px; bottom: -120px; right: -50px; background: #06b6d4; }
    .noise-texture { position: absolute; inset: 0; opacity: 0.04; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
    
    .badge-elite-status { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 100px; color: #cbd5e1; font-weight: 700; font-size: 0.8rem; display: flex; align-items: center; gap: 10px; }
    .status-dot { width: 8px; height: 8px; background: #10b981; border-radius: 50%; box-shadow: 0 0 10px #10b981; animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.4; } 100% { opacity: 1; } }
    .text-gradient-cyan { background: linear-gradient(90deg, #06b6d4, #6366f1); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .text-azure-mist { color: rgba(219, 234, 254, 0.85); text-shadow: 0 2px 4px rgba(0,0,0,0.1); }
    .btn-elite-action { background: #6366f1; color: #fff; border: none; box-shadow: 0 15px 35px rgba(99,102,241,0.35); transition: 0.4s; }
    .btn-elite-action:hover { transform: translateY(-5px); box-shadow: 0 20px 45px rgba(99,102,241,0.5); color: #fff; }

    /* Elite Card Styles */
    .broadcast-card-elite { border: 1px solid var(--ws-border); transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
    .broadcast-card-elite:hover { transform: translateY(-12px); border-color: var(--primary); box-shadow: 0 30px 60px -12px rgba(0,0,0,0.1) !important; }
    .card-overlay-actions { position: absolute; bottom: 0; left: 0; right: 0; background: linear-gradient(to top, rgba(0,0,0,0.6), transparent); opacity: 0; transition: 0.3s; }
    .broadcast-card-elite:hover .card-overlay-actions { opacity: 1; }
    .btn-icon-elite { width: 42px; height: 42px; border-radius: 14px; color: #fff; border: none; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(8px); transition: 0.2s; }
    .btn-icon-elite:hover { transform: scale(1.1); filter: brightness(1.2); }
    .elite-category-tag { position: absolute; top: 15px; left: 15px; background: rgba(15,23,42,0.7); backdrop-filter: blur(10px); color: #fff; font-size: 0.65rem; font-weight: 800; padding: 5px 15px; border-radius: 100px; border: 1px solid rgba(255,255,255,0.1); }
    
    .badge-mini-stat { padding: 4px 10px; border-radius: 8px; font-size: 0.65rem; font-weight: 800; display: flex; align-items: center; gap: 5px; }
    .bg-tag-primary { background: rgba(99,102,241,0.1); color: #6366f1; }
    .bg-tag-success { background: rgba(16,185,129,0.1); color: #10b981; }

    .highlight-metric-box { box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.03); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }

    /* Modal & Field Styling */
    .modal-glass-theme { background: var(--ws-bg-card) !important; }
    .premium-field { background-color: var(--bg-stats-inner-item) !important; border: 2px solid var(--ws-border) !important; color: var(--text-stats-main) !important; border-radius: 16px !important; transition: 0.3s; }
    .premium-field:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 5px rgba(99,102,241,0.1) !important; }
    .f-lg { font-size: 1.1rem; padding: 15px 20px !important; }

    .absolute-opacity-zero { opacity: 0; position: absolute; z-index: 5; }
    .cursor-pointer { cursor: pointer; }

    /* Animations */
    .animate-revealer { animation: revealDown 1s cubic-bezier(0.19, 1, 0.22, 1) both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-40px); } to { opacity: 1; transform: translateY(0); } }

    body.theme-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
</style>

@endsection



