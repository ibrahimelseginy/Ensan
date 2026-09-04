@extends('layouts.app')

@section('content')
<div class="campaigns-content-page">
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
                    <li class="breadcrumb-item active" aria-current="page">محتوى الحملات</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-megaphone-fill me-2"></i> إدارة محتوى الموقع الإلكتروني
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">محتوى الحملات</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                تحديث الصور والأوصاف والتفاصيل التقنية للحملات التي تظهر للجمهور
            </p>
        </div>
    </div>

<div class="container-fluid py-4 px-lg-5">
    <div class="row g-4">

        {{-- Campaign Page Header --}}
        <div class="col-lg-12">
            <form action="{{ route('website.settings.update') }}" method="POST" class="card border-0 shadow-sm mb-4 animate-slide-up overflow-hidden">
                @csrf
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-type-h1 me-2 text-primary"></i> عنوان ووصف الصفحة</h5>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm fw-bold">حفظ المحتوى</button>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">العنوان الرئيسي للصفحة</label>
                            <input type="text" name="campaigns_title" class="form-control" value="{{ $settings['campaigns_title'] ?? 'حملاتنا' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">الوصف المختصر</label>
                            <input type="text" name="campaigns_subtitle" class="form-control" value="{{ $settings['campaigns_subtitle'] ?? 'شاركنا الخير' }}">
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- General Campaign Stats --}}
        <div class="col-lg-12">
            <form action="{{ route('website.settings.update') }}" method="POST" class="card border-0 shadow-sm mb-4 animate-slide-up overflow-hidden">
                @csrf
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-bar-chart-fill me-2 text-warning"></i> إحصائيات عامة للحملات</h5>
                    <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">تحديث الإحصائيات</button>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-4">
                        <div class="col-md-4">
                            <div class="stats-card-sleek p-4 rounded-4 border text-center shadow-sm h-100">
                                <label class="text-muted x-small fw-bold mb-3 d-block text-uppercase">إجمالي المستفيدين</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="text" name="campaign_stats_beneficiaries" class="form-control bg-transparent text-dark text-center fw-bold border-0 fs-3 p-0 w-auto" value="{{ $settings['campaign_stats_beneficiaries'] ?? '15,000+' }}">
                                    <i class="bi bi-people text-warning ms-2 fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card-sleek p-4 rounded-4 border text-center shadow-sm h-100">
                                <label class="text-muted x-small fw-bold mb-3 d-block text-uppercase">الحملات النشطة</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="text" name="campaign_stats_active" class="form-control bg-transparent text-dark text-center fw-bold border-0 fs-3 p-0 w-auto" value="{{ $settings['campaign_stats_active'] ?? '8' }}">
                                    <i class="bi bi-lightning-fill text-danger ms-2 fs-4"></i>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="stats-card-sleek p-4 rounded-4 border text-center shadow-sm h-100">
                                <label class="text-muted x-small fw-bold mb-3 d-block text-uppercase">إجمالي التبرعات</label>
                                <div class="d-flex align-items-center justify-content-center">
                                    <input type="text" name="campaign_stats_donations" class="form-control bg-transparent text-dark text-center fw-bold border-0 fs-3 p-0 w-auto" value="{{ $settings['campaign_stats_donations'] ?? '2M+' }}">
                                    <i class="bi bi-currency-dollar text-success ms-2 fs-4"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        {{-- Featured Campaign Banner Management --}}
        <div class="col-lg-12">
            <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm mb-4 animate-slide-up overflow-hidden">
                @csrf
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-layout-text-window-reverse me-2 text-primary"></i> خيارات بانر الحملة (Banner View)</h5>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm fw-bold">حفظ إعدادات البانر</button>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-4">
                        {{-- Preview --}}
                        <div class="col-lg-12 mb-2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0 x-small text-muted text-uppercase"><i class="bi bi-eye me-1"></i> مـعايـنة قـسم الـبـانر الـعـريض</h6>
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
                                            <img src="{{ app(\App\Services\ImageUploadService::class)->url($settings['featured_campaign_icon']) }}" class="w-100 h-100 object-fit-cover rounded-3" id="prevBannerIconImg">
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
                                    <button type="button" class="btn btn-light fw-bold px-5 py-3 rounded-pill text-primary shadow-xl border-0 hover-scale" style="min-width: 180px;">
                                        <span id="prevBannerButton" class="fs-5">{{ $settings['featured_campaign_button_text'] ?? 'ساهم الآن' }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Input Form Area --}}
                        <div class="col-12 mt-3">
                            <div class="p-4 bg-light rounded-4 border shadow-inner">
                                <div class="row g-4 d-flex flex-wrap">
                                    <div class="col-md-4 text-start" dir="rtl">
                                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">نص العنوان (Title Text)</label>
                                        <input type="text" name="featured_campaign_title" class="form-control" value="{{ $settings['featured_campaign_title'] ?? 'حملة الشتاء 2024' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">إجمالي الـمستفيدين</label>
                                        <input type="text" name="featured_campaign_beneficiaries" class="form-control text-center" value="{{ $settings['featured_campaign_beneficiaries'] ?? '2,500+' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">نـسـبة الـتـقـدم (%)</label>
                                        <input type="number" name="featured_campaign_progress" class="form-control text-center" value="{{ $settings['featured_campaign_progress'] ?? '65' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">نـص زر الـعمل</label>
                                        <input type="text" name="featured_campaign_button_text" class="form-control text-center" value="{{ $settings['featured_campaign_button_text'] ?? 'ساهم الآن' }}" oninput="updateBannerPreview()">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">أيـقـونة الـبـانر (Img)</label>
                                        <div class="input-group input-group-sm">
                                            <input type="file" name="featured_campaign_icon" class="form-control form-control-sm" accept="image/*" onchange="previewIcon(this)" style="font-size: 0.7rem;">
                                            @if(isset($settings['featured_campaign_icon']) && \Illuminate\Support\Str::contains($settings['featured_campaign_icon'], ['/', '.']))
                                                <button type="button" class="btn btn-outline-danger btn-sm" onclick="document.getElementById('delete_featured_campaign_icon').value='1'; this.closest('.input-group').classList.add('d-none'); document.getElementById('prevBannerIconImg').classList.add('d-none'); document.getElementById('prevBannerIconI').style.display='block';">
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
            <div class="card border-0 shadow-sm mb-4 animate-slide-up overflow-hidden" style="border-radius: 20px;">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-collection-play me-2 text-primary"></i> إدارة الحملات الجارية</h5>
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createCampaignModal">
                        <i class="bi bi-plus-lg me-1"></i> أضف حملة جديدة
                    </button>
                </div>
                
                <div class="p-0 bg-white">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="bg-light">
                                <tr class="text-muted x-small text-uppercase">
                                    <th class="ps-4 fw-bold py-3 border-0">الحملة</th>
                                    <th class="fw-bold py-3 border-0 text-center">الحالة</th>
                                    <th class="fw-bold py-3 border-0 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaigns as $campaign)
                                <tr class="border-bottom transition-all">
                                    <td class="ps-4 py-3">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="position-relative">
                                                @if($campaign->image_path)
                                                    <img src="{{ $campaign->image_url }}" class="rounded-3 shadow-sm object-fit-cover border" width="54" height="54">
                                                @else
                                                    <div class="rounded-3 bg-light d-flex justify-content-center align-items-center border" style="width: 54px; height: 54px;">
                                                        <i class="bi bi-image text-muted"></i>
                                                    </div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark mb-0">{{ $campaign->name }}</div>
                                                <div class="x-small text-muted">تاريخ الحملة: {{ $campaign->start_date_text ?? 'لم يحدد نصياً' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($campaign->status == 'active')
                                            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">نشطة الآن</span>
                                        @elseif($campaign->status == 'upcoming')
                                            <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">قادمة قريباً</span>
                                        @elseif($campaign->status == 'ended')
                                            <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">منتهية</span>
                                        @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">مؤرشفة</span>
                                        @endif
                                    </td>

                                    <td class="py-3 text-center">
                                        <div class="d-flex flex-wrap gap-2 justify-content-center">
                                            <button class="btn btn-sm btn-outline-light text-primary border rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editCampaignModal{{ $campaign->id }}">
                                                <i class="bi bi-pencil-square x-small me-1"></i> تعديل
                                            </button>
                                            
                                            <form action="{{ route('website.campaigns.destroy', $campaign->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من مسح هذه الحملة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-light text-danger border rounded-pill px-3">
                                                    <i class="bi bi-trash x-small"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-2 d-block mb-2 opacity-25"></i>
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
            <form action="{{ route('website.campaigns.stats.update') }}" method="POST" enctype="multipart/form-data" class="card border-0 shadow-sm mb-4 animate-slide-up overflow-hidden">
                @csrf
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-images me-2 text-primary"></i> مـكتبة صـور الـسلايدر (Header Slider)</h5>
                    <button type="submit" class="btn btn-sm btn-primary rounded-pill px-5 shadow-sm fw-bold">حفظ مـكتبة الـصور</button>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-3 justify-content-center">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="col-md-2 col-6">
                            <div class="position-relative rounded-4 overflow-hidden border bg-light slider-upload-box" style="height: 100px;">
                                @php $sliderPath = $settings["campaign_slider_$i"] ?? null; @endphp
                                @if($sliderPath)
                                    <img src="{{ app(\App\Services\ImageUploadService::class)->url($sliderPath) }}" class="w-100 h-100 object-fit-cover" id="preview_slider_{{$i}}">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="preview_container_{{$i}}">
                                        <i class="bi bi-image-fill fs-4 mb-1"></i>
                                        <span class="x-small fw-bold">{{ $i }}</span>
                                    </div>
                                    <img src="" class="w-100 h-100 object-fit-cover d-none position-absolute top-0 start-0" id="preview_slider_{{$i}}">
                                @endif
                                {{-- Clickable File Input Overlay --}}
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
    .campaigns-content-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .opacity-05 { opacity: 0.05; }

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

    /* Cards & Components */
    .stats-card-sleek {
        transition: all 0.3s ease;
        background-color: white;
    }
    .stats-card-sleek:hover {
        border-color: var(--primary) !important;
        background-color: var(--bg-soft);
        transform: translateY(-5px);
    }

    .banner-container-gradient {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        border-radius: 20px;
    }

    .slider-upload-box {
        transition: all 0.3s ease;
        border: 2px dashed var(--border) !important;
    }
    .slider-upload-box:hover {
        border-color: var(--primary) !important;
        background: var(--bg-soft) !important;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05) !important; }
    .backdrop-blur-md { backdrop-filter: blur(12px); }
    .hover-scale:hover { transform: scale(1.05); }

    .table thead th {
        font-weight: 700;
        background-color: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    /* Modal Styling Fixes */
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.1);
    }
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
</div>
@endsection
