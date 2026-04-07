@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="campaigns-content-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #f59e0b;"></div>
            <div class="glow-orb-2" style="background: #10b981;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">محتوى الحملات</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-megaphone-fill me-2"></i> إدارة محتوى الموقع الإلكتروني
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">محتوى الحملات</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        تحديث الصور والأوصاف والتفاصيل التقنية للحملات التي تظهر للجمهور
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left"></div>
            </div>
        </div>
    </div>


<div class="container-fluid py-4" style="margin-top: 100px;">
    <div class="row g-4">


        {{-- Campaign Page Header --}}
        <div class="col-lg-12">
            <form action="{{ route('website.settings.update') }}" method="POST" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-type-h1 me-2 text-primary"></i> عنوان ووصف الصفحة</h5>
                    <button type="submit" class="btn btn-sm btn-primary text-white rounded-pill px-4 shadow-sm fw-bold">حفظ المحتوى</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-slate-400">العنوان الرئيسي للصفحة</label>
                            <input type="text" name="campaigns_title" class="form-control bg-dark text-white border-secondary" value="{{ $settings['campaigns_title'] ?? 'حملاتنا' }}" placeholder="مثلاً: حملاتنا الجارية">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-slate-400">الوصف المختصر</label>
                            <input type="text" name="campaigns_subtitle" class="form-control bg-dark text-white border-secondary" value="{{ $settings['campaigns_subtitle'] ?? 'شاركنا الخير' }}" placeholder="مثلاً: شارك في دعم الأيتام والمحتاجين">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- General Campaign Stats --}}
        <div class="col-lg-12">
            <form action="{{ route('website.settings.update') }}" method="POST" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-bar-chart-fill me-2 text-warning"></i> إحصائيات عامة للحملات</h5>
                    <button type="submit" class="btn btn-sm btn-warning rounded-pill px-4 shadow-sm fw-bold">تحديث الإحصائيات</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="stats-inner-card p-3 rounded-4 border border-white border-opacity-10 text-center shadow-hover">
                                <label class="text-slate-400 x-small fw-bold mb-2 d-block">إجمالي المستفيدين</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="text" name="campaign_stats_beneficiaries" class="form-control bg-transparent text-white text-center fw-bold border-0 fs-4 p-0" value="{{ $settings['campaign_stats_beneficiaries'] ?? '15,000+' }}">
                                    <i class="bi bi-people text-warning opacity-50 ms-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-inner-card p-3 rounded-4 border border-white border-opacity-10 text-center shadow-hover">
                                <label class="text-slate-400 x-small fw-bold mb-2 d-block">الحملات النشطة</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="text" name="campaign_stats_active" class="form-control bg-transparent text-white text-center fw-bold border-0 fs-4 p-0" value="{{ $settings['campaign_stats_active'] ?? '8' }}">
                                    <i class="bi bi-lightning-fill text-danger opacity-50 ms-2"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-inner-card p-3 rounded-4 border border-white border-opacity-10 text-center shadow-hover">
                                <label class="text-slate-400 x-small fw-bold mb-2 d-block">إجمالي التبرعات</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="text" name="campaign_stats_donations" class="form-control bg-transparent text-white text-center fw-bold border-0 fs-4 p-0" value="{{ $settings['campaign_stats_donations'] ?? '2M+' }}">
                                    <i class="bi bi-currency-dollar text-success opacity-50 ms-2"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Featured Campaign Banner Management --}}
        <div class="col-lg-12">
            <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-layout-text-window-reverse me-2 text-emerald-400"></i> خيارات بانر الحملة (Banner View)</h5>
                    <button type="submit" class="btn btn-sm btn-emerald-500 rounded-pill px-4 shadow-sm fw-bold text-white">حفظ إعدادات البانر</button>
                </div>
                <div class="p-4 bg-slate-900 text-white">
                    <div class="row g-4">
                        {{-- Preview --}}
                        <div class="col-lg-12 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 x-small text-secondary text-uppercase"><i class="bi bi-eye me-1"></i> مـعايـنة قـسم الـبـانر الـعـريض</h6>
                            </div>
                            <div class="rounded-4 p-4 d-flex flex-column flex-md-row justify-content-between align-items-center text-white shadow-lg position-relative overflow-hidden banner-container-gradient" 
                                 id="bannerPreview">
                                
                                {{-- Background Glows --}}
                                <div class="position-absolute top-0 start-0 w-100 h-100 overflow-hidden" style="pointer-events: none;">
                                    <div class="position-absolute top-0 start-0 w-50 h-100 bg-white opacity-10" style="filter: blur(80px); transform: translate(-30%, -30%);"></div>
                                    <div class="position-absolute bottom-0 end-0 w-50 h-100 bg-black opacity-10" style="filter: blur(80px); transform: translate(30%, 30%);"></div>
                                </div>

                                {{-- Right: Text Content --}}
                                <div class="d-flex align-items-center gap-4 order-1 order-md-2 w-100 justify-content-center justify-content-md-end mb-4 mb-md-0">
                                    <div class="text-center text-md-end">
                                        <div class="badge bg-white bg-opacity-20 rounded-pill px-3 py-1 x-small fw-bold mb-2 shadow-sm border border-white border-opacity-10" id="prevBannerStatus">الحملة النشطة الآن</div>
                                        <h2 class="fw-bold mb-0 display-6" id="prevBannerTitle" style="text-shadow: 0 2px 4px rgba(0,0,0,0.2);">{{ $settings['featured_campaign_title'] ?? 'حملة الشتاء 2024' }}</h2>
                                    </div>
                                    <div class="bg-white bg-opacity-20 p-3 rounded-4 d-flex align-items-center justify-content-center backdrop-blur-md border border-white border-opacity-20 shadow-lg" style="width: 85px; height: 85px;">
                                        @if(isset($settings['featured_campaign_icon']) && \Illuminate\Support\Str::contains($settings['featured_campaign_icon'], ['/', '.']))
                                            <img src="{{ asset('storage/' . $settings['featured_campaign_icon']) }}" class="w-100 h-100 object-fit-cover rounded-3" id="prevBannerIconImg">
                                        @else
                                            <img src="" class="w-100 h-100 object-fit-cover d-none rounded-3" id="prevBannerIconImg">
                                            <i class="bi bi-snow2 fs-1 text-white" id="prevBannerIconI"></i>
                                        @endif
                                    </div>
                                </div>

                                {{-- Middle: Quick Stats Grid --}}
                                <div class="d-flex gap-5 order-2 order-md-2 mb-4 mb-md-0 mx-md-4 border-md-start border-md-end border-white border-opacity-20 px-md-5 w-100 w-md-auto justify-content-center py-2">
                                    <div class="text-center">
                                        <div class="fw-bold h2 mb-0"><span id="prevBannerBeneficiaries">{{ $settings['featured_campaign_beneficiaries'] ?? '2,500+' }}</span></div>
                                        <div class="x-small opacity-75 fw-bold text-uppercase">مستفيد</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="fw-bold h2 mb-0"><span id="prevBannerProgress">{{ $settings['featured_campaign_progress'] ?? '65' }}</span>%</div>
                                        <div class="x-small opacity-75 fw-bold text-uppercase">مكتمل</div>
                                    </div>
                                </div>

                                {{-- Left: Call to Action --}}
                                <div class="order-3 order-md-1 w-100 w-md-auto text-center text-md-start">
                                    <button type="button" class="btn btn-light fw-bold px-5 py-3 rounded-pill text-emerald-600 shadow-xl border-0 hover-scale" style="min-width: 180px;">
                                        <span id="prevBannerButton" class="fs-5">{{ $settings['featured_campaign_button_text'] ?? 'ساهم الآن' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Input Form Area --}}
                        <div class="col-12 mt-3">
                            <div class="p-4 bg-white bg-opacity-5 rounded-4 border border-white border-opacity-10 shadow-inner">
                                <div class="row g-4 d-flex flex-wrap">
                                    <div class="col-md-4">
                                        <label class="form-label x-small fw-bold text-slate-400">نص العنوان (Title Text)</label>
                                        <input type="text" name="featured_campaign_title" class="form-control bg-dark text-white border-secondary py-2" value="{{ $settings['featured_campaign_title'] ?? 'حملة الشتاء 2024' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-slate-400">إجمالي الـمستفيدين</label>
                                        <input type="text" name="featured_campaign_beneficiaries" class="form-control bg-dark text-white border-secondary py-2 text-center" value="{{ $settings['featured_campaign_beneficiaries'] ?? '2,500+' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-slate-400">نـسـبة الـتـقـدم (%)</label>
                                        <input type="number" name="featured_campaign_progress" class="form-control bg-dark text-white border-secondary py-2 text-center" value="{{ $settings['featured_campaign_progress'] ?? '65' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-slate-400">نـص زر الـعمل</label>
                                        <input type="text" name="featured_campaign_button_text" class="form-control bg-dark text-white border-secondary py-2 text-center" value="{{ $settings['featured_campaign_button_text'] ?? 'ساهم الآن' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-slate-400">أيـقـونة الـبـانر (Img)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="file" name="featured_campaign_icon" class="form-control form-control-sm bg-dark text-white border-secondary" accept="image/*" onchange="previewIcon(this)" style="font-size: 0.7rem;">
                                            @if(isset($settings['featured_campaign_icon']) && \Illuminate\Support\Str::contains($settings['featured_campaign_icon'], ['/', '.']))
                                                <button type="button" class="btn btn-danger btn-sm" onclick="document.getElementById('delete_featured_campaign_icon').value='1'; this.closest('.input-group').classList.add('d-none'); document.getElementById('prevBannerIconImg').classList.add('d-none'); document.getElementById('prevBannerIconI').style.display='block';">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                        <input type="hidden" name="delete_featured_campaign_icon" id="delete_featured_campaign_icon" value="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Campaigns Management --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 border-0 shadow-sm animate-slide-up stats-card-dark pe-0 pb-0">
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900 rounded-top-4">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-collection-play me-2 text-warning"></i> إدارة الحملات</h5>
                    <button type="button" class="btn btn-sm btn-warning rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                        <i class="bi bi-plus-lg me-1"></i> أضف حملة جديدة
                    </button>
                </div>
                
                <div class="p-0 bg-slate-900 rounded-bottom-4">
                    <div class="table-responsive">
                        <table class="table table-dark table-hover mb-0 align-middle border-secondary border-opacity-25" style="--bs-table-bg: transparent; --bs-table-hover-bg: rgba(255,255,255,0.02)">
                            <thead>
                                <tr class="text-slate-400 x-small text-uppercase">
                                    <th class="ps-4 fw-bold py-3 border-0 bg-opacity-10 bg-black">الحملة</th>
                                    <th class="fw-bold py-3 border-0 bg-opacity-10 bg-black text-center">الحالة</th>
                                    <th class="fw-bold py-3 border-0 bg-opacity-10 bg-black text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                <tr class="border-bottom border-white border-opacity-10 group-hover-parent transition-all">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="position-relative">
                                                @if($campaign->image_path)
                                                    <img src="{{ $campaign->image_url }}" class="rounded-3 shadow-sm object-fit-cover border border-white border-opacity-10" width="50" height="50">
                                                @else
                                                    <div class="rounded-3 bg-secondary bg-opacity-25 d-flex justify-content-center align-items-center border border-white border-opacity-10" style="width: 50px; height: 50px;">
                                                        <i class="bi bi-image text-slate-400"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-white mb-1">{{ $campaign->name }}</div>
                                                <div class="x-small text-slate-400">تاريخ الحملة: {{ $campaign->start_date_text ?? 'لم يحدد نصياً' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($campaign->status == 'active')
                                            <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 rounded-pill px-3 py-1 x-small fw-bold">نشطة الآن</span>
                                        @elseif($campaign->status == 'upcoming')
                                            <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 rounded-pill px-3 py-1 x-small fw-bold">قادمة (تبدأ قريباً)</span>
                                        @elseif($campaign->status == 'ended')
                                            <span class="badge bg-danger bg-opacity-20 text-danger border border-danger border-opacity-25 rounded-pill px-3 py-1 x-small fw-bold">منتهية</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-20 text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-1 x-small fw-bold">مؤرشفة</span>
                                        @endif
                                    </td>

                                    <td class="py-3 text-center">
                                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-outline-info rounded-pill px-3 mb-1 mb-md-0" data-bs-toggle="modal" data-bs-target="#editCampaignModal{{ $campaign->id }}">
                                                <i class="bi bi-pencil-square x-small"></i> تعديل
                                            </button>
                                            
                                            <form action="{{ route('website.campaigns.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من مسح هذه الحملة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    <i class="bi bi-trash x-small"></i> حذف
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>

                                @empty
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-slate-400">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-50"></i>
                                        لا توجد حملات مضافة حالياً
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


        {{-- Campaign Slider Images --}}
        <div class="col-lg-12">
            <form action="{{ route('website.campaigns.stats.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up animate-delay-2 stats-card-dark">
                @csrf
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-images me-2 text-primary"></i> مـكتبة صـور الـسلايدر (Header Slider)</h5>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-5 shadow-sm fw-bold">حفظ مـكتبة الـصور</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-3 justify-content-center">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="col-md-2 col-6">
                            <div class="position-relative rounded-4 overflow-hidden border border-white border-opacity-10 bg-white bg-opacity-5 shadow-inner" style="height: 100px;">
                                @php $sliderPath = $settings["campaign_slider_$i"] ?? null; @endphp
                                @if($sliderPath)
                                    <img src="{{ asset('storage/' . $sliderPath) }}" class="w-100 h-100 object-fit-cover" id="preview_slider_{{$i}}">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="preview_container_{{$i}}">
                                        <i class="bi bi-image-fill fs-4 mb-1"></i>
                                        <span class="x-small fw-bold">{{ $i }}</span>
                                    </div>
                                    <img src="" class="w-100 h-100 object-fit-cover d-none position-absolute top-0 start-0" id="preview_slider_{{$i}}">
                                @endif
                                {{-- Clickable File Input Overlay (always works) --}}
                                <input type="file" name="campaign_slider_{{$i}}" accept="image/*"
                                       class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" style="z-index: 5;"
                                       onchange="previewSliderImage(this, 'preview_slider_{{$i}}', 'preview_container_{{$i}}')">
                                {{-- Status Badge --}}
                                <div class="position-absolute bottom-0 start-0 w-100 p-1 text-center" style="z-index: 3;">
                                    <span class="badge rounded-pill {{ $sliderPath ? 'bg-success' : 'bg-secondary opacity-50' }} x-small px-2 py-1">
                                        {{ $sliderPath ? '✓' : 'فارغ' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    body { background-color: #0b0e14 !important; }
    .campaigns-content-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #92400e 0%, #d97706 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #fde68a; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @media (max-width: 991px) { .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; } .display-4 { font-size: 2.2rem; } }

    :root {
        --primary-glow: rgba(13, 110, 253, 0.15);
        --warning-glow: rgba(255, 193, 7, 0.15);
        --emerald-glow: rgba(16, 185, 129, 0.15);
    }

    .glass-card { 
        background: rgba(255,255,255,0.03); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255,255,255,0.08); 
        border-radius: 24px; 
    }
    
    .stats-card-dark { background-color: #0f172a !important; }
    .bg-slate-900 { background-color: #0f172a !important; }
    .text-slate-400 { color: #94a3b8 !important; }
    .x-small { font-size: 0.72rem; letter-spacing: 0.02em; }
    .cursor-pointer { cursor: pointer; }
    
    .shadow-warning { box-shadow: 0 10px 40px -10px var(--warning-glow) !important; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.2) !important; }
    .backdrop-blur-md { backdrop-filter: blur(12px); }
    
    .stats-inner-card {
        background-color: rgba(30, 41, 59, 0.4);
        border: 1px solid rgba(255,255,255,0.05) !important;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .stats-inner-card:hover {
        background-color: rgba(30, 41, 59, 0.8);
        transform: translateY(-5px);
        border-color: rgba(255, 193, 7, 0.3) !important;
        box-shadow: 0 10px 20px -5px rgba(0,0,0,0.3);
    }

    /* Custom Input Range */
    .custom-range::-webkit-slider-runnable-track {
        background: rgba(255,255,255,0.1);
        height: 8px;
        border-radius: 10px;
    }
    .custom-range::-webkit-slider-thumb {
        background: #ffc107;
        margin-top: -6px;
        box-shadow: 0 0 10px rgba(255,193,7,0.5);
    }
    
    /* Animation Utilities */
    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .group-hover-parent:hover .group-hover-blur { filter: blur(3px) brightness(0.6); }
    .group-hover-parent:hover .group-hover-opacity-100 { opacity: 1 !important; }
    .hover-scale-sm:hover { transform: scale(1.03); }
    .transition-all { transition: all 0.3s ease; }
    .duration-500 { transition-duration: 0.5s; }

    .banner-container-gradient {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.2), 0 10px 10px -5px rgba(0, 0, 0, 0.1) !important; }
    .hover-scale:hover { transform: scale(1.05); }

    /* Fix image preview in slider */
    .group-hover-overlay label {
        z-index: 10;
        background: rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    .group-hover-overlay:hover label { opacity: 1; }
      
      
            
      

      


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

<script>
    function updateBannerPreview() {
        const titleEl = document.getElementById('prevBannerTitle');
        const benEl = document.getElementById('prevBannerBeneficiaries');
        const progEl = document.getElementById('prevBannerProgress');
        const btnEl = document.getElementById('prevBannerButton');

        if(titleEl) titleEl.textContent = document.getElementsByName('featured_campaign_title')[0].value || 'حملة الشتاء 2024';
        if(benEl) benEl.textContent = document.getElementsByName('featured_campaign_beneficiaries')[0].value || '2,500+';
        if(progEl) progEl.textContent = document.getElementsByName('featured_campaign_progress')[0].value || '65';
        if(btnEl) btnEl.textContent = document.getElementsByName('featured_campaign_button_text')[0].value || 'ساهم الآن';
    }


    function previewIcon(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('prevBannerIconImg');
                const icon = document.getElementById('prevBannerIconI');
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if (icon) {
                    icon.style.display = 'none';
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewCardIcon(input, index) {
         if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('prevIcon' + index + 'View');
                container.innerHTML = `<img src="${e.target.result}" style="width: 20px; height: 20px; object-fit: contain;">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewCardMainImage(input) {
         if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('prevMainImage').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewCardMainIcon(input) {
         if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('prevMainIconContainer').innerHTML = `<img src="${e.target.result}" class="w-100 h-100 rounded-circle object-fit-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewSliderImage(input, imgId, containerId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(imgId);
                const container = document.getElementById(containerId);
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if (container) {
                    container.classList.add('d-none');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        updateBannerPreview();
    });
</script>
@include('website.create_campaign_modal')
@foreach($campaigns as $camp)
    @include('website.edit_campaign_modal', ['campaign' => $camp])
@endforeach


<script>
    document.addEventListener("DOMContentLoaded", function(event) { 
        var scrollpos = sessionStorage.getItem('scrollpos');
        if (scrollpos) window.scrollTo(0, scrollpos);
        sessionStorage.removeItem('scrollpos');
    });

    window.onbeforeunload = function(e) {
        sessionStorage.setItem('scrollpos', window.scrollY);
    };
</script>
@endsection








