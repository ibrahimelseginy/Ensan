@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="guest-house-mgmt-page">
    {{-- Dynamic Hero Section --}}
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%); background-size: cover; background-position: center;">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #3b82f6;"></div>
            <div class="glow-orb-2" style="background: #0ea5e9;"></div>
            <div class="noise-overlay"></div>
        </div>
        
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-images me-2"></i> إدارة المحتوى
                        </div>
                    </div>
                    <h1 class="display-3 fw-800 text-white mb-3 text-end">سلايدر دار الضيافة</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        تحكم في الصور المتحركة التي تظهر في قسم دار الضيافة
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Area --}}
    <div class="container-fluid py-5">
        <div class="row g-4">
            <div class="col-12">
                <form action="{{ route('website.guest-house.stats.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="premium-card-dark animate-up">
                        <div class="card-header-lux d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="header-icon bg-indigo-500"><i class="bi bi-play-btn"></i></div>
                                <h5 class="fw-bold mb-0">سلايدر الصور المتحركة</h5>
                            </div>
                            <button type="submit" class="btn btn-indigo-solid py-2 px-4 rounded-pill fw-bold shadow-indigo">حفظ التعديلات</button>
                        </div>
                        <div class="card-body-lux p-4 p-md-5">
                            {{-- Statistics Section --}}
                            <h6 class="fw-bold text-white mb-4"><i class="bi bi-bar-chart-fill me-2 text-amber-500"></i> إحصائيات الدار</h6>
                            <div class="row g-4 mb-5">
                                {{-- Stat 1: Beds --}}
                                <div class="col-md-3 col-6">
                                    <div class="stat-input-box">
                                        <div class="stat-icon-vibe bg-blue-500 bg-opacity-10 text-blue-500">
                                            <i class="bi bi-hospital"></i>
                                        </div>
                                        <div class="w-100">
                                            <input type="text" name="gh_stats_beds_label" class="stat-label-field" value="{{ $settings['gh_stats_beds_label'] ?? 'عدد الأسرّة' }}">
                                            <input type="text" name="gh_stats_beds" class="stat-value-field" value="{{ $settings['gh_stats_beds'] ?? '50+' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Stat 2: Patients --}}
                                <div class="col-md-3 col-6">
                                    <div class="stat-input-box">
                                        <div class="stat-icon-vibe bg-emerald-500 bg-opacity-10 text-emerald-500">
                                            <i class="bi bi-people-fill"></i>
                                        </div>
                                        <div class="w-100">
                                            <input type="text" name="gh_stats_patients_label" class="stat-label-field" value="{{ $settings['gh_stats_patients_label'] ?? 'المرضى سنوياً' }}">
                                            <input type="text" name="gh_stats_patients" class="stat-value-field" value="{{ $settings['gh_stats_patients'] ?? '3000+' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Stat 3: Branches --}}
                                <div class="col-md-3 col-6">
                                    <div class="stat-input-box">
                                        <div class="stat-icon-vibe bg-amber-500 bg-opacity-10 text-amber-500">
                                            <i class="bi bi-geo-alt-fill"></i>
                                        </div>
                                        <div class="w-100">
                                            <input type="text" name="gh_stats_branches_label" class="stat-label-field" value="{{ $settings['gh_stats_branches_label'] ?? 'عدد الفروع' }}">
                                            <input type="text" name="gh_stats_branches" class="stat-value-field" value="{{ $settings['gh_stats_branches'] ?? '2' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Stat 4: Reception --}}
                                <div class="col-md-3 col-6">
                                    <div class="stat-input-box">
                                        <div class="stat-icon-vibe bg-indigo-500 bg-opacity-10 text-indigo-500">
                                            <i class="bi bi-clock-history"></i>
                                        </div>
                                        <div class="w-100">
                                            <input type="text" name="gh_stats_reception_label" class="stat-label-field" value="{{ $settings['gh_stats_reception_label'] ?? 'ساعات الاستقبال' }}">
                                            <input type="text" name="gh_stats_reception" class="stat-value-field" value="{{ $settings['gh_stats_reception'] ?? '24/7' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="divider-lux mb-5"></div>

                            <h6 class="fw-bold text-white mb-4"><i class="bi bi-images me-2 text-indigo-400"></i> معرض الصور (سلايدر)</h6>
                            <div class="row g-4 mb-5">
                                 @for($i = 1; $i <= 10; $i++)
                                 <div class="col-md-4">
                                     <div class="gallery-card-lux position-relative" id="ghSliderZone{{ $i }}" style="height: 180px; border-color: #6366f1;">
                                         <input type="file" name="gh_slider_{{ $i }}" class="file-hidden" onchange="previewGhSlider(this, {{ $i }})">
                                         
                                         @if(isset($settings["gh_slider_$i"]))
                                             <div class="position-absolute top-0 end-0 p-2 z-index-20">
                                                 <div class="form-check bg-danger bg-opacity-75 rounded-pill px-3 py-1 text-white x-small">
                                                     <input class="form-check-input" type="checkbox" name="delete_gh_slider_{{ $i }}" value="1" id="del_gh_{{ $i }}">
                                                     <label class="form-check-label fw-bold" for="del_gh_{{ $i }}">حذف</label>
                                                 </div>
                                             </div>
                                         @endif

                                         <div class="gallery-preview-wrapper" id="ghSliderPreview{{ $i }}">
                                             @if(isset($settings["gh_slider_$i"]))
                                                 <img src="{{ asset('storage/' . $settings["gh_slider_$i"]) }}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover">
                                             @else
                                                 <div class="gallery-placeholder">
                                                     <i class="bi bi-images fs-3 text-indigo-400"></i>
                                                     <p class="x-small mb-0 mt-2">شريحة {{ $i }}</p>
                                                 </div>
                                             @endif
                                         </div>
                                     </div>
                                 </div>
                                 @endfor
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function previewGhSlider(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('ghSliderPreview' + id);
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
    :root {
        --dark-bg: #0b0e14;
        --card-dark: #1a2332;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-400: #94a3b8;
        --blue-500: #3b82f6;
        --cyan-400: #22d3ee;
        --emerald-500: #10b981;
        --amber-500: #f59e0b;
        --indigo-500: #6366f1;
    }

    body {
        background-color: var(--dark-bg) !important;
        font-family: 'Tajawal', sans-serif;
        color: #f8fafc;
    }

    .guest-house-mgmt-page { min-height: 100vh; }

    /* Premium Hero Sleek */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 120px;
        border-radius: 0;
        margin: -30px -30px 0 -30px;
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }

    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.3; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.03; background-image: url('data:image/svg+xml,...'); }

    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { 
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px 18px;
        border-radius: 100px;
        color: #e0e7ff;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }

    .btn-action-glow-blue {
        background: var(--blue-500);
        color: white;
        border: none;
        font-weight: 800;
        box-shadow: 0 0 25px rgba(59, 130, 246, 0.4);
        transition: 0.4s;
    }
    .btn-action-glow-blue:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.6);
        color: white;
    }

    /* Content Area */
    .content-shift-up { margin-top: -60px; position: relative; z-index: 20; padding: 0 5%; }

    /* Premium Card Dark */
    .premium-card-dark {
        background: var(--card-dark);
        border-radius: 35px;
        border: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    .card-header-lux {
        padding: 25px 30px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex;
        align-items: center;
        gap: 15px;
        background: rgba(255,255,255,0.01);
    }
    .header-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }
    .bg-blue-500 { background: var(--blue-500); }
    .bg-cyan-500 { background: #06b6d4; }
    .bg-emerald-500 { background: var(--emerald-500); }
    .bg-indigo-500 { background: #6366f1; }

    .form-group-lux { position: relative; }
    .label-lux { color: var(--slate-400); font-weight: 700; font-size: 0.85rem; margin-bottom: 12px; display: block; }
    
    .field-lux {
        width: 100%;
        background: #0f172a;
        border: 2px solid #2d3748;
        border-radius: 18px;
        padding: 16px 22px;
        color: white;
        font-weight: 600;
        transition: 0.3s;
    }
    .field-lux:focus {
        border-color: var(--blue-500);
        outline: none;
        background: #000;
        box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.1);
    }
    .text-xl { font-size: 1.4rem; color: var(--cyan-400); }

    /* Large Upload Zone */
    .upload-zone-lux-large {
        height: 380px;
        border: 2px dashed #2d3748;
        border-radius: 30px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: 0.3s;
    }
    .upload-zone-lux-large:hover { border-color: #06b6d4; background: rgba(6, 182, 212, 0.03); }
    
    .preview-area-lux-large { height: 100%; display: flex; align-items: center; justify-content: center; }
    .upload-placeholder-lux { text-align: center; }
    
    .upload-hover-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: 0.3s;
    }
    .upload-zone-lux-large:hover .upload-hover-overlay { opacity: 1; }
    .btn-cyan-solid { background: #06b6d4; color: white; border: none; font-weight: 700; }

    .info-alert-lux {
        background: rgba(6, 182, 212, 0.08);
        color: #67e8f9;
        padding: 15px 20px;
        border-radius: 18px;
        font-size: 0.8rem;
    }

    /* Stats Inputs */
    .stat-input-box {
        background: #0f172a;
        border: 1px solid #1e293b;
        border-radius: 24px;
        padding: 20px;
        display: flex;
        gap: 15px;
        align-items: center;
        transition: 0.3s;
    }
    .stat-input-box:hover { transform: translateY(-3px); border-color: var(--slate-400); }
    
    .stat-icon-vibe {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .stat-label-field {
        background: transparent;
        border: none;
        color: var(--slate-500);
        font-size: 0.75rem;
        font-weight: 800;
        width: 100%;
        margin-bottom: 2px;
    }
    .stat-value-field {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.25rem;
        font-weight: 800;
        width: 100%;
        font-family: 'Outfit';
    }

    /* Gallery Card */
    .gallery-card-lux {
        height: 200px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 2px dashed #1e293b;
        cursor: pointer;
        transition: 0.4s;
    }
    .gallery-card-lux:hover { border-color: var(--emerald-500); transform: scale(1.03); }
    
    .gallery-preview-wrapper { height: 100%; }
    .gallery-placeholder { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--slate-800); }
    
    .gallery-overlay {
        position: absolute;
        inset: 0;
        background: rgba(16, 185, 129, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: 0.3s;
    }
    .gallery-card-lux:hover .gallery-overlay { opacity: 1; }

    .file-hidden {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .divider-lux { height: 1px; background: rgba(255,255,255,0.05); }

    /* Animations */
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(35px); } to { opacity: 1; transform: translateY(0); } }

    .scroll-thin::-webkit-scrollbar { width: 6px; }
    .scroll-thin::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

    .bg-blue-500 { background: #3b82f6; }
    .bg-emerald-500 { background: #10b981; }
    .bg-amber-500 { background: #f59e0b; }
    .bg-indigo-500 { background: #6366f1; }

    @media (max-width: 991px) {
        .premium-hero-sleek { padding: 60px 0 80px; border-radius: 0 0 35px 35px; }
        .display-4 { font-size: 2.2rem; }
    }
</style>
@endsection
