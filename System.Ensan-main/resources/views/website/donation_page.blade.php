@extends('layouts.app', ['hideGlobalAlerts' => true])

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="settings-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #e11d48;"></div>
            <div class="glow-orb-2" style="background: #fbbf24;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('website.content') }}" class="text-white-50 decoration-none">الموقع الإلكتروني</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">صفحة التبرعات</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-heart-pulse me-2"></i> إدارة محتوى صفحة التبرعات
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">إعدادات صفحة التبرعات</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        تعديل البنرات، النصوص، الإحصائيات، ومجالات الدعم المتاحة للتبرع
                    </p>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
            <div class="d-flex align-items-center">
                <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                <div>
                    <h6 class="mb-1 fw-bold">حدث خطأ ما!</h6>
                    <ul class="mb-0 small">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row g-4">
        <div class="col-lg-8">
            {{-- Banner Section --}}
            <form action="{{ route('website.donation-page.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-flag me-2 text-danger"></i> حملة عاجلة (البنر العلوي)</h5>
                    <button type="submit" class="btn btn-sm btn-danger text-white rounded-pill px-4 shadow-sm">حفظ البنر</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">عنوان الحملة</label>
                            <input type="text" name="donation_page_campaign_title" class="form-control bg-dark text-white border-secondary" value="{{ $settings['donation_page_campaign_title'] ?? 'مساعدات الشتاء للأسر المحتاجة' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">رابط الحملة (Direct Link)</label>
                            <input type="text" name="donation_page_campaign_link" class="form-control bg-dark text-white border-secondary" 
                                   value="{{ $settings['donation_page_campaign_link'] ?? ($settings['donation_banner_urgent_link'] ?? '#') }}" 
                                   placeholder="أدخل رابط الحملة هنا...">
                            <div class="form-text text-slate-400 small mt-1">هذا الرابط سيتم توجيه المتبرع إليه عند الضغط على البنر في الموقع.</div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Hero & Description Section --}}
            <form action="{{ route('website.donation-page.update') }}" method="POST" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-stars me-2 text-warning"></i> الصفحة الرئيسية (Hero)</h5>
                    <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">حفظ النصوص</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">عنوان الهيرو (Hero Title)</label>
                            <input type="text" name="donation_page_hero_text" class="form-control bg-dark text-white border-secondary" value="{{ $settings['donation_page_hero_text'] ?? 'ساهم في صناعة الفرق' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">وصف الهيرو</label>
                            <textarea name="donation_page_hero_desc" class="form-control bg-dark text-white border-secondary" rows="3">{{ $settings['donation_page_hero_desc'] ?? 'تبرعك اليوم يضيء حياة الآلاف، كل مساهمة مهما كانت صغيرة تصنع أثراً كبيراً.' }}</textarea>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Statistics Section --}}
            <form action="{{ route('website.donation-page.update') }}" method="POST" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-bar-chart me-2 text-info"></i> الإحصائيات المباشرة</h5>
                    <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm">حفظ الإحصائيات</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-3">
                        <div class="col-md-6 text-center">
                            <div class="p-4 rounded-4 bg-dark bg-opacity-50 border border-secondary shadow-sm">
                                <label class="small d-block text-slate-400 mb-2">عدد المتبرعين</label>
                                <input type="text" name="donation_page_stats_donors" class="form-control form-control-lg text-center fw-bold bg-transparent text-white border-0" value="{{ $settings['donation_page_stats_donors'] ?? '523,450' }}">
                                <div class="text-info mt-2 small">متبرع كريم يشاركنا اللحظة</div>
                            </div>
                        </div>
                        <div class="col-md-6 text-center">
                            <div class="p-4 rounded-4 bg-dark bg-opacity-50 border border-secondary shadow-sm">
                                <label class="small d-block text-slate-400 mb-2">تبرعات اليوم (جنيه)</label>
                                <input type="text" name="donation_page_stats_today_collected" class="form-control form-control-lg text-center fw-bold bg-transparent text-white border-0" value="{{ $settings['donation_page_stats_today_collected'] ?? '1,247' }}">
                                <div class="text-success mt-2 small">جنيه تم جمعها اليوم</div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>

            {{-- Projects Section Header --}}
            <form action="{{ route('website.donation-page.update') }}" method="POST" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-grid me-2 text-primary"></i> قسم مشاريع التبرع</h5>
                    <button type="submit" class="btn btn-sm btn-primary text-white rounded-pill px-4 shadow-sm">حفظ</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">عنوان المشاريع</label>
                            <input type="text" name="donation_page_projects_title" class="form-control bg-dark text-white border-secondary" value="{{ $settings['donation_page_projects_title'] ?? 'مشاريع اليوم' }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold text-slate-400">وصف المشاريع</label>
                            <input type="text" name="donation_page_projects_desc" class="form-control bg-dark text-white border-secondary" value="{{ $settings['donation_page_projects_desc'] ?? 'أثرك ينمو باستمرار' }}">
                        </div>
                    </div>
                </div>
            </form>

            {{-- Categories / Support Fields --}}
            <div class="glass-card mb-5 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-list-stars me-2 text-success"></i> مجالات الدعم (الفئات)</h5>
                    <a href="{{ route('website.donation-settings.unified') }}" class="btn btn-sm btn-outline-success rounded-pill px-4 shadow-sm">
                        <i class="bi bi-pencil-square me-1"></i> إدارة المجالات
                    </a>
                </div>
                <div class="p-4 bg-slate-900">
                    <p class="text-slate-400 small mb-4">يتم مزامنة هذه المجالات تلقائياً من نظام "مجالات الدعم" الموحد.</p>
                    <div class="row g-3">
                        @foreach($categories as $category)
                        <div class="col-md-4">
                            <div class="p-3 rounded-4 bg-dark bg-opacity-50 border border-secondary d-flex align-items-center gap-3">
                                <div class="badge bg-success bg-opacity-20 text-success rounded-circle p-2">
                                    <i class="bi bi-check2"></i>
                                </div>
                                <span class="text-white fw-bold">{{ $category->name }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar / Preview Info --}}
        <div class="col-lg-4">
            <div class="glass-card p-4 sticky-top animate-reveal-left bg-slate-900" style="top: 100px;">
                <h6 class="fw-bold text-white mb-3"><i class="bi bi-info-circle me-2 text-primary"></i> معلومات النشر</h6>
                <p class="text-slate-400 small mb-4">
                    التغييرات التي تجريها هنا تظهر فوراً على صفحة التبرعات في الموقع الإلكتروني (الواجهة الأمامية).
                </p>
                <div class="d-grid gap-2">
                    <a href="#" class="btn btn-outline-light rounded-pill px-4 py-2 small fw-bold disabled">
                        <i class="bi bi-eye me-2"></i> معاينة الصفحة
                    </a>
                    <div class="text-center mt-3">
                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">
                            <i class="bi bi-check-circle-fill me-1"></i> متصل بالمباشر
                        </span>
                    </div>
                </div>
                
                <hr class="my-4 border-white border-opacity-10">
                
                <h6 class="fw-bold text-white mb-2">تعليمات الفئات:</h6>
                <ul class="text-slate-400 small ps-3">
                    <li>تستخدم الفئات لتصفية المشاريع في صفحة التبرعات.</li>
                    <li>تأكد من وجود فئة "الكل" لعرض كافة المشاريع.</li>
                    <li>يمكنك سحب وإفلات الفئات (قريباً) لترتيبها.</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
function addCategory() {
    const container = document.getElementById('categories-container');
    const div = document.createElement('div');
    div.className = 'col-md-4 category-item';
    div.innerHTML = `
        <div class="input-group">
            <input type="text" name="donation_page_categories[]" class="form-control bg-dark text-white border-secondary" value="">
            <button type="button" class="btn btn-outline-danger border-secondary" onclick="this.closest('.category-item').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
}
</script>

<style>
    body { background-color: #0b0e14 !important; }
    .settings-page { min-height: 100vh; }
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #1e293b 0%, #334155 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #fecaca; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }

    .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; }
    .stats-card-dark { background-color: #0f172a !important; border: 1px solid rgba(255,255,255,0.1); }
    .bg-slate-900 { background-color: #0f172a !important; }
    .text-slate-400 { color: #94a3b8 !important; }
    .border-secondary { border-color: rgba(255,255,255,0.1) !important; }
    
    .form-control:focus { background-color: #1e293b !important; border-color: #475569 !important; color: white !important; box-shadow: none; }
    .btn-outline-danger:hover { background-color: #ef4444; color: white; border-color: #ef4444; }
      
      
            
      

      

      /* --- SYSTEM LIGHT MODE PATCH --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .text-white, 
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white, 
      body:not(.theme-dark) .premium-hero-sleek .text-white-50,
      body:not(.theme-dark) .badge-glass-premium,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item a {
          color: #fff !important;
      }
      body:not(.theme-dark) .glass-card, 
      body:not(.theme-dark) .premium-modal-dark,
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .stats-card-dark,
      body:not(.theme-dark) .stats-inner-card,
      body:not(.theme-dark) .project-card-admin,
      body:not(.theme-dark) .campaign-card-lux,
      body:not(.theme-dark) .guest-card-lux,
      body:not(.theme-dark) .article-card-lux,
      body:not(.theme-dark) .message-card-lux,
      body:not(.theme-dark) .donation-card-lux,
      body:not(.theme-dark) .member-card-premium,
      body:not(.theme-dark) .partner-card-lux,
      body:not(.theme-dark) .leader-card-lux,
      body:not(.theme-dark) .empty-state-card-lux,
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content,
      body:not(.theme-dark) .categories-sidebar,
      body:not(.theme-dark) .sector-header,
      body:not(.theme-dark) .item-card,
      body:not(.theme-dark) .dark-glass-card {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .category-item {
          color: var(--ws-text-secondary);
          background: rgba(0,0,0,0.02);
      }
      body:not(.theme-dark) .category-item:hover { background: var(--ws-bg-page); color: var(--ws-text-primary); }
      body:not(.theme-dark) .category-item.active { background: var(--ws-bg-page); border-color: var(--ws-primary); color: var(--ws-text-primary); }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control, body:not(.theme-dark) .form-select, body:not(.theme-dark) .form-input-dark { 
          background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; 
      }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
      </style>

{{-- Toast for success --}}
@if(session('success'))
<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 1100">
    <div id="liveToast" class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
            <div class="toast-body fw-bold">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
@endif

@endsection







