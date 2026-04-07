@extends('layouts.app', ['hideGlobalAlerts' => true])

@section('content')
<div class="donation-page-mgmt">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">إعدادات صفحة التبرع</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-heart-fill me-2"></i> إدارة محتوى صفحة التبرعات 💖
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">صناعة الأثر وسهولة العطاء</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                تخصيص واجهة التبرع، البنرات الدعائية، وإحصائيات الإنجاز المباشرة للمتبرعين.
            </p>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        @if ($errors->any())
            <div class="alert alert-danger border-0 shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <h6 class="mb-1 fw-bold">حدث خطأ في البيانات!</h6>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            <div class="col-lg-8">
                {{-- Banner Section --}}
                <form action="{{ route('website.donation-page.update') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 animate-slide-up">
                    @csrf
                    <div class="p-4 border-bottom bg-danger d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-white"><i class="bi bi-megaphone-fill me-2"></i> حملة عاجلة (البنر العلوي)</h6>
                        <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm x-small">حفظ البنر</button>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-12">
                                <label class="form-label x-small fw-bold text-muted">عنوان الحملة / النص الظاهر</label>
                                <input type="text" name="donation_page_campaign_title" class="form-control" value="{{ $settings['donation_page_campaign_title'] ?? '' }}" placeholder="مثلاً: مساعدات الشتاء العاجلة...">
                            </div>
                            <div class="col-12">
                                <label class="form-label x-small fw-bold text-muted">رابط التوجيه المباشر</label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-link-45deg"></i></span>
                                    <input type="text" name="donation_page_campaign_link" class="form-control border-start-0" value="{{ $settings['donation_page_campaign_link'] ?? '' }}" placeholder="https://...">
                                </div>
                                <div class="form-text x-small text-muted mt-2">يتم توجيه المتبرع لهذا الرابط عند الضغط على شريط التنبيه العلوي.</div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Hero Texts Section --}}
                <form action="{{ route('website.donation-page.update') }}" method="POST" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 animate-slide-up" style="animation-delay: 0.1s">
                    @csrf
                    <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-fonts me-2 text-primary"></i> واجهة صفحة التبرع (Hero)</h6>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm x-small">حفظ المحتوى</button>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="mb-4">
                            <label class="form-label x-small fw-bold text-muted">العنوان الرئيسي للصفحة</label>
                            <input type="text" name="donation_page_hero_text" class="form-control form-control-lg fw-bold" value="{{ $settings['donation_page_hero_text'] ?? '' }}">
                        </div>
                        <div class="mb-0">
                            <label class="form-label x-small fw-bold text-muted">الوصف التعريفي</label>
                            <textarea name="donation_page_hero_desc" class="form-control" rows="3">{{ $settings['donation_page_hero_desc'] ?? '' }}</textarea>
                        </div>
                    </div>
                </form>

                {{-- Statistics Section --}}
                <form action="{{ route('website.donation-page.update') }}" method="POST" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 animate-slide-up" style="animation-delay: 0.2s">
                    @csrf
                    <div class="p-4 border-bottom bg-success d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-white"><i class="bi bi-graph-up me-2"></i> إحصائيات الإنجاز</h6>
                        <button type="submit" class="btn btn-light text-success rounded-pill px-4 fw-bold shadow-sm x-small">حفظ الإحصائيات</button>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 border bg-stats-inner text-center h-100">
                                    <div class="header-icon-small bg-primary mx-auto mb-3 shadow-sm"><i class="bi bi-people-fill text-white"></i></div>
                                    <label class="form-label x-small fw-bold text-muted-theme mb-2">إجمالي المتبرعين</label>
                                    <input type="text" name="donation_page_stats_donors" class="form-control text-center fw-bold fs-4 border-0 bg-transparent text-stats-main" value="{{ $settings['donation_page_stats_donors'] ?? '' }}">
                                    <p class="x-small text-primary-theme mb-0 mt-2 fw-bold">مساهم كريم في مسيرة العطاء</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-4 rounded-4 border bg-stats-inner text-center h-100">
                                    <div class="header-icon-small bg-success mx-auto mb-3 shadow-sm"><i class="bi bi-cash-stack text-white"></i></div>
                                    <label class="form-label x-small fw-bold text-muted-theme mb-2">تبرعات تم جمعها (اليوم)</label>
                                    <input type="text" name="donation_page_stats_today_collected" class="form-control text-center fw-bold fs-4 border-0 bg-transparent text-stats-main" value="{{ $settings['donation_page_stats_today_collected'] ?? '' }}">
                                    <p class="x-small text-success-theme mb-0 mt-2 fw-bold">جنيه مصري اليوم</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Projects Header Section --}}
                <form action="{{ route('website.donation-page.update') }}" method="POST" class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5 animate-slide-up" style="animation-delay: 0.3s">
                    @csrf
                    <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-grid-fill me-2 text-warning"></i> عناوين قسم المشاريع</h6>
                        <button type="submit" class="btn btn-warning rounded-pill px-4 fw-bold shadow-sm x-small text-dark">حفظ</button>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold text-muted">عنوان قسم الصور/المشاريع</label>
                                <input type="text" name="donation_page_projects_title" class="form-control" value="{{ $settings['donation_page_projects_title'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold text-muted">الوصف الفرعي (Slogan)</label>
                                <input type="text" name="donation_page_projects_desc" class="form-control" value="{{ $settings['donation_page_projects_desc'] ?? '' }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="sticky-top" style="top: 100px;">
                    {{-- Categories Preview Card --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 animate-reveal-left">
                        <div class="p-4 border-bottom bg-primary d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold text-white"><i class="bi bi-tags-fill me-2"></i> فئات التبرع</h6>
                            <a href="{{ route('website.donation-settings.unified') }}" class="btn btn-link text-white text-decoration-none fw-bold x-small p-0 opacity-75">
                                إدارة الفئات <i class="bi bi-chevron-left ms-1"></i>
                            </a>
                        </div>
                        <div class="card-body p-4">
                            <div class="d-flex flex-wrap gap-2">
                                @forelse($categories as $category)
                                    <span class="badge bg-white border text-dark rounded-pill px-3 py-2 small fw-bold shadow-xs">
                                        <i class="bi bi-check2-circle text-primary me-1"></i> {{ $category->name }}
                                    </span>
                                @empty
                                    <div class="text-center w-100 py-3 opacity-50 x-small">لا يوجد فئات مضافة حالياً</div>
                                @endforelse
                            </div>
                            <div class="mt-4 p-3 bg-light rounded-4 border">
                                <p class="x-small text-muted mb-0 lh-base">
                                    <i class="bi bi-info-circle-fill text-primary me-1"></i> تظهر هذه الفئات كفلاتر (Filters) في الواجهة الأمامية لمساعدة المتبرعين على اختيار مجال الدعم.
                                </p>
                            </div>
                        </div>
                    </div>

                    {{-- Publishing Info --}}
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-reveal-left" style="animation-delay: 0.1s">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-dark mb-3">حالة النشر</h6>
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2 fw-bold x-small">
                                    <span class="pulse-dot me-1"></span> متصل وبالبث المباشر
                                </div>
                            </div>
                            <p class="x-small text-muted mb-4">
                                أي تعديل يتم حفظه سيظهر فوراً للمتبرعين في صفحة التبرع بالموقع العام. يرجى التأكد من الروابط والصور قبل الحفظ.
                            </p>
                            <div class="d-grid">
                                <a href="{{ config('app.url') }}/donate" target="_blank" class="btn btn-outline-primary rounded-pill py-2 fw-bold shadow-xs x-small">
                                    معاينة الصفحة العامة <i class="bi bi-box-arrow-up-right ms-1"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Toast for success --}}
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="liveToast" class="toast show align-items-center text-white bg-primary border-0 rounded-4" role="alert">
        <div class="d-flex p-2">
            <div class="toast-body fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
@endif

<style>
    .donation-page-mgmt { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .bg-primary-light { background-color: rgba(34, 197, 94, 0.1); }
    .transition-all { transition: all 0.3s ease; }
    .z-10 { z-index: 10; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }
    
    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 18px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }

    .header-icon-small {
        width: 38px;
        height: 38px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }

    .pulse-dot {
        width: 8px;
        height: 8px;
        background: #22C55E;
        border-radius: 50%;
        display: inline-block;
        animation: pulse-green 2s infinite;
    }
    @keyframes pulse-green {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 6px rgba(34, 197, 94, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(34, 197, 94, 0); }
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    .animate-reveal-left { animation: revealLeft 0.8s ease-out forwards; opacity: 0; }
    
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }

    .form-control-lg { border-radius: 16px; border-width: 2px; }
    .form-control:focus { border-color: var(--primary); box-shadow: 0 0 0 4px var(--primary-light); }

    /* Theme-Aware Stats Styling */
    .bg-stats-inner { background-color: var(--gray-50); }
    .text-stats-main { color: var(--dark) !important; }
    .text-muted-theme { color: var(--gray-500); }
    .text-primary-theme { color: var(--primary); }
    .text-success-theme { color: var(--primary-dark); }

    body.theme-dark .bg-stats-inner { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .text-stats-main { color: #ffffff !important; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .text-primary-theme { color: var(--primary); }
    body.theme-dark .text-success-theme { color: #34d399; }
</style>
@endsection
