@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="honor-wall-page">
    {{-- Dynamic Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1"></div>
            <div class="glow-orb-2"></div>
            <div class="noise-overlay"></div>
        </div>
        
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right">
                    <nav aria-label="breadcrumb" class="mb-4">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">جدار الشرف</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-patch-check-fill me-2"></i> قائمة الفخر والامتنان 💎
                        </div>
                    </div>
                    <h1 class="display-3 fw-800 text-white mb-3">جدار الشرف والمساهمين</h1>
                    <p class="lead text-white-50 mb-0 max-w-600">
                        نوثق هنا بصمات الخير لشركاء النجاح والمتميزين في العمل التطوعي الذين سطروا معاني الوفاء في مسيرة إنسان.
                    </p>
                </div>
                <div class="col-lg-4 text-lg-end mt-4 mt-lg-0 animate-reveal-left">
                    <div class="d-flex flex-column gap-3 align-items-lg-end">
                        <button class="btn btn-action-glow-emerald px-4 py-3 rounded-4 shadow-xl" data-bs-toggle="modal" data-bs-target="#addPartner">
                            <i class="bi bi-plus-circle-fill me-2"></i>
                            <span>توثيق شريك نجاح</span>
                        </button>
                        <button class="btn btn-glass-indigo rounded-pill px-4 py-2 border-opacity-25" data-bs-toggle="modal" data-bs-target="#manageSlider">
                            <i class="bi bi-images me-2"></i> إدارة صور السلايدر
                        </button>
                        <button class="btn btn-glass-indigo rounded-pill px-4 py-2 border-opacity-25" data-bs-toggle="modal" data-bs-target="#manageStats">
                            <i class="bi bi-graph-up-arrow me-2"></i> إحصائيات النجاح
                        </button>
                        <button class="btn btn-glass-indigo rounded-pill px-4 py-2 border-opacity-25" data-bs-toggle="modal" data-bs-target="#addLeader">
                            <i class="bi bi-person-heart me-2"></i> إضافة بطل تطوع
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5 content-shift-up">
        {{-- Navigation Tabs --}}
        <div class="row justify-content-center mb-5 mt-5">
            <div class="col-auto animate-up">
                <div class="tab-pill-lux p-2 rounded-pill shadow-2xl">
                    <ul class="nav nav-pills" id="honorTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active rounded-pill px-5 py-3 fw-bold" id="partners-tab" data-bs-toggle="pill" data-bs-target="#partners-content" type="button">
                                <i class="bi bi-building-fill me-2"></i> شركاء النجاح
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link rounded-pill px-5 py-3 fw-bold" id="leaders-tab" data-bs-toggle="pill" data-bs-target="#leaders-content" type="button">
                                <i class="bi bi-stars me-2"></i> قادة العطاء
                            </button>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="tab-content" id="honorTabContent">
            {{-- Partners Tab --}}
            <div class="tab-pane fade show active" id="partners-content" role="tabpanel">
                @if(count($partners) > 0)
                    <div class="row g-4">
                        @foreach($partners as $partner)
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="partner-card-lux animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                                @php
                                    $tiers = [
                                        'platinum' => ['label' => 'بلاتيني', 'class' => 'tier-platinum', 'icon' => 'bi-award-fill'],
                                        'gold' => ['label' => 'ذهبي', 'class' => 'tier-gold', 'icon' => 'bi-star-fill'],
                                        'silver' => ['label' => 'فضي', 'class' => 'tier-silver', 'icon' => 'bi-shield-fill'],
                                        'bronze' => ['label' => 'برونزي', 'class' => 'tier-bronze', 'icon' => 'bi-patch-check-fill'],
                                        'corporate' => ['label' => 'شريك مؤسسي', 'class' => 'tier-platinum', 'icon' => 'bi-building-fill'],
                                    ];
                                    $tier = $tiers[$partner->type] ?? $tiers['bronze'];
                                @endphp

                                <div class="card-status-pill tier-label {{ $tier['class'] }}">
                                    <i class="bi {{ $tier['icon'] }} me-1"></i> {{ $tier['label'] }}
                                </div>

                                <div class="card-inner-lux">
                                    <div class="logo-box-lux">
                                        @if($partner->logo_path)
                                            <img src="{{ $partner->image_url }}" class="partner-logo-img" alt="{{ $partner->name }}">
                                        @else
                                            <div class="placeholder-vibe text-white-50"><i class="bi bi-building fs-1"></i></div>
                                        @endif
                                    </div>
                                    <div class="partner-info-lux text-center mt-3">
                                        <h6 class="partner-name-lux text-white mb-2">{{ $partner->name }}</h6>
                                        <div class="d-flex justify-content-center gap-1">
                                            <button class="btn btn-action-sm edit-btn" data-bs-toggle="modal" data-bs-target="#editPartner{{ $partner->id }}">
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('website.partners.destroy', $partner) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-action-sm delete-btn" onclick="return confirm('حذف الشريك نهائياً؟')">
                                                    <i class="bi bi-trash3"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        @endforeach
                    </div>
                    

                @else
                    <div class="empty-state-card-lux animate-up">
                        <div class="empty-visual-wrapper">
                            <div class="glow-pulse"></div>
                            <i class="bi bi-building-dash empty-icon-vibe"></i>
                        </div>
                        <h3 class="fw-bold text-white mt-4">لا يوجد شركاء حالياً</h3>
                        <p class="text-slate-400 max-w-400 mx-auto">ابدأ بتوثيق أول شراكة نجاح للمؤسسة لتظهر في جدار الشرف بالموقع الرسمي.</p>
                        <button class="btn btn-action-glow-emerald mt-3 px-4 py-2" data-bs-toggle="modal" data-bs-target="#addPartner">إضافة أول شريك</button>
                    </div>
                @endif
            </div>

            {{-- Leaders Tab --}}
            <div class="tab-pane fade" id="leaders-content" role="tabpanel">
                @if(count($leaders) > 0)


                <div class="row g-4 justify-content-center">


                    @foreach($leaders as $leader)
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="leader-card-lux animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
                            <div class="rank-badge-lux">
                                @if($leader->rank == 1) <i class="bi bi-trophy-fill text-amber-400"></i>
                                @elseif($leader->rank == 2) <i class="bi bi-award-fill text-slate-300"></i>
                                @elseif($leader->rank == 3) <i class="bi bi-patch-check-fill text-orange-400"></i>
                                @else <span class="rank-num">#{{ $leader->rank }}</span> @endif
                            </div>
                            
                            <div class="leader-visual">
                                <div class="avatar-ring-premium"></div>
                                @if($leader->image_path)
                                    <img src="{{ $leader->image_url }}" class="leader-pfp shadow-2xl" alt="{{ $leader->name }}">
                                @else
                                    <div class="leader-pfp-placeholder"><i class="bi bi-person-heart"></i></div>
                                @endif
                                @if($leader->rank == 1) <div class="crown-vibe"><i class="bi bi-crown-fill"></i></div> @endif
                            </div>

                            <div class="leader-details-lux mt-4">
                                <h5 class="fw-bold mb-1 text-white">{{ $leader->name }}</h5>
                                <div class="hours-chip-lux mt-2">
                                    <i class="bi bi-stopwatch-fill me-2"></i> {{ $leader->hours }} ساعة تطوعية
                                </div>
                                <div class="actions-lux-hover mt-4">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-glass-indigo rounded-pill px-4 x-small" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> تعديل
                                        </button>
                                        <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-glass-danger rounded-pill px-4 x-small" onclick="return confirm('إزالة البطل من جدار العطاء؟')">
                                                <i class="bi bi-trash3 me-1"></i> إزالة
                                            </button>
                                        </form>
                                    </div>
                                </div>


                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                    <div class="empty-state-card-lux animate-up">
                        <div class="empty-visual-wrapper">
                            <div class="glow-pulse"></div>
                            <i class="bi bi-person-heart empty-icon-vibe text-rose-500"></i>
                        </div>
                        <h3 class="fw-bold text-white mt-4">جدار العطاء بانتظار أبطاله</h3>
                        <p class="text-slate-400 max-w-400 mx-auto">لم يتم تكريم أي متطوعين في جدار العطاء لهذا الموسم بعد.</p>
                        <button class="btn btn-glass-indigo mt-3 border-opacity-50" data-bs-toggle="modal" data-bs-target="#addLeader">إضافة بطل تطوع</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Add Partner Modal --}}
<div class="modal fade" id="addPartner" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('website.partners.store') }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark">
            @csrf
            <div class="modal-header border-0 bg-emerald-900 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box-lux bg-emerald-500">
                        <i class="bi bi-patch-plus-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-800 mb-0">توثيق شراكة استراتيجية</h5>
                        <p class="text-emerald-200 x-small mb-0">إضافة شعار وجهة جديدة إلى قائمة شركاء النجاح</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="form-group-lux mb-4">
                            <label class="label-lux">اسم الجهة / المؤسسة <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="field-lux" required placeholder="مثلاً: شركة أرامكو السعودية">
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <div class="form-group-lux">
                                    <label class="label-lux">تصنيف الشراكة <span class="text-danger">*</span></label>
                                    <select name="type" class="field-lux">
                                        <option value="platinum">بلاتيني (Platinum)</option>
                                        <option value="gold" selected>ذهبي (Gold)</option>
                                        <option value="silver">فضي (Silver)</option>
                                        <option value="bronze">برونزي (Bronze)</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group-lux">
                                    <label class="label-lux">رابط الموقع الرسمي</label>
                                    <input type="url" name="website_url" class="field-lux" placeholder="https://...">
                                </div>
                            </div>
                        </div>

                        <div class="form-group-lux">
                            <label class="label-lux">وصف مختصر (اختياري)</label>
                            <textarea name="description" class="field-lux no-resize" rows="3" placeholder="نبذة عن طبيعة التعاون..."></textarea>
                        </div>
                    </div>

                    <div class="col-md-5">
                        <label class="label-lux mb-3 text-center d-block">شعار المؤسسة</label>
                        <div class="upload-zone-lux" id="addPartnerZone">
                            <input type="file" name="logo" class="file-hidden" onchange="previewLogoAdd(this)" accept="image/*">
                            <div class="preview-area-lux" id="addLogoPreviewContainer">
                                <div class="upload-placeholder-lux">
                                    <i class="bi bi-cloud-arrow-up-fill fs-1 text-emerald-400"></i>
                                    <p class="x-small text-slate-400 mt-2">اختر الشعار</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-emerald-solid w-100 py-3 rounded-pill fw-bold shadow-lg">
                    <i class="bi bi-check2-all me-2"></i> تثبيت في جدار الشرف
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Add Leader Modal --}}
<div class="modal fade" id="addLeader" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.volunteer-wall.store') }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark">
            @csrf
            <div class="modal-header border-0 bg-indigo-950 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box-lux bg-indigo-500">
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">تكريم بطل تطوع جديد</h5>
                        <p class="text-indigo-200 x-small mb-0">إضافة متطوع متميز لجدول قادة العطاء</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="form-group-lux mb-4">
                    <label class="label-lux">اسم المتطوع المتميز</label>
                    <input type="text" name="name" class="field-lux" required placeholder="الاسم الكامل كما سيظهر في الموقع">
                </div>
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <div class="form-group-lux">
                            <label class="label-lux">الترتيب / المركز</label>
                            <input type="number" name="rank" class="field-lux" required placeholder="1">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-lux">
                            <label class="label-lux">إجمالي الساعات</label>
                            <input type="text" name="hours" class="field-lux" required placeholder="120 ساعة">
                        </div>
                    </div>
                </div>
                <div class="form-group-lux">
                    <label class="label-lux text-center d-block">الصورة الشخصية</label>
                    <div class="upload-zone-lux h-auto py-5" id="leaderAddZone">
                        <input type="file" name="image" class="file-hidden" onchange="previewLeaderAdd(this)">
                        <div id="leaderPreviewContainerAdd" class="text-center">
                            <i class="bi bi-camera-fill fs-1 text-slate-600 mb-2"></i>
                            <p class="x-small text-slate-500 mb-0">رفع صورة شخصية لائقة</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-indigo-solid w-100 py-3 rounded-pill fw-bold shadow-lg">حفظ في الجدار</button>
            </div>
        </form>
    </div>
</div>

{{-- Manage Slider Modal --}}
<div class="modal fade" id="manageSlider" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark">
            @csrf
            <div class="modal-header border-0 bg-indigo-900 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box-lux bg-indigo-500">
                        <i class="bi bi-images"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إدارة صور السلايدر</h5>
                        <p class="text-indigo-200 x-small mb-0">جدار الشرف والمساهمين</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="row g-4">
                    @for($i = 1; $i <= 10; $i++)
                    <div class="col-md-4 col-lg-3">
                        <label class="label-lux text-center d-block">الشريحة {{ $i }}</label>
                        <div class="upload-zone-lux" style="height: 180px;">
                            <input type="file" name="honor_wall_slider_{{ $i }}" class="file-hidden" onchange="previewSlider(this, {{ $i }})">
                            <div class="preview-area-lux" id="sliderPreviewContainer{{ $i }}">
                                @if(isset($settings["honor_wall_slider_$i"]))
                                    <div class="position-relative h-100">
                                        <img src="{{ asset('storage/' . $settings["honor_wall_slider_$i"]) }}" class="img-fluid rounded-3 h-100 w-100 object-fit-cover shadow-sm">
                                        <div class="position-absolute top-0 end-0 p-1">
                                            <div class="form-check">
                                                <input class="form-check-input bg-danger border-0" type="checkbox" name="delete_honor_wall_slider_{{ $i }}" value="1" id="deleteSlider{{ $i }}">
                                                <label class="form-check-label text-white x-small" for="deleteSlider{{ $i }}">حذف</label>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-center">
                                        <i class="bi bi-image fs-1 text-slate-600 mb-2"></i>
                                        <p class="x-small text-slate-500 mb-0">ارفع الصورة</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-indigo-solid w-100 py-3 rounded-pill fw-bold shadow-lg">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>

{{-- Manage Stats Modal --}}
<div class="modal fade" id="manageStats" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('website.settings.update') }}" method="POST" class="modal-content premium-modal-dark">
            @csrf
            <div class="modal-header border-0 bg-indigo-900 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box-lux bg-indigo-500">
                        <i class="bi bi-graph-up-arrow"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إحصائيات شركاء النجاح</h5>
                        <p class="text-indigo-200 x-small mb-0">تظهر في صفحة جدار الشرف</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="form-group-lux mb-3">
                            <label class="label-lux">عدد المتبرعين</label>
                            <div class="row g-2">
                                <div class="col-8"><input type="text" name="partners_stats_donors_label" class="field-lux" value="{{ $settings['partners_stats_donors_label'] ?? 'متبرع' }}"></div>
                                <div class="col-4"><input type="text" name="partners_stats_donors" class="field-lux text-center" value="{{ $settings['partners_stats_donors'] ?? '+5000' }}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-lux mb-3">
                            <label class="label-lux">عدد المتطوعين</label>
                            <div class="row g-2">
                                <div class="col-8"><input type="text" name="partners_stats_volunteers_label" class="field-lux" value="{{ $settings['partners_stats_volunteers_label'] ?? 'متطوع' }}"></div>
                                <div class="col-4"><input type="text" name="partners_stats_volunteers" class="field-lux text-center" value="{{ $settings['partners_stats_volunteers'] ?? '+1000' }}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-lux mb-3">
                            <label class="label-lux">المؤسسات الشريكة</label>
                            <div class="row g-2">
                                <div class="col-8"><input type="text" name="partners_stats_institutions_label" class="field-lux" value="{{ $settings['partners_stats_institutions_label'] ?? 'مؤسسة شريكة' }}"></div>
                                <div class="col-4"><input type="text" name="partners_stats_institutions" class="field-lux text-center" value="{{ $settings['partners_stats_institutions'] ?? '+10' }}"></div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group-lux mb-3">
                            <label class="label-lux">الحملات المكتملة</label>
                            <div class="row g-2">
                                <div class="col-8"><input type="text" name="partners_stats_campaigns_label" class="field-lux" value="{{ $settings['partners_stats_campaigns_label'] ?? 'حملة مكتملة' }}"></div>
                                <div class="col-4"><input type="text" name="partners_stats_campaigns" class="field-lux text-center" value="{{ $settings['partners_stats_campaigns'] ?? '+100' }}"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-indigo-solid w-100 py-3 rounded-pill fw-bold shadow-lg">حفظ الإحصائيات</button>
            </div>
        </form>
    </div>
</div>

@foreach($partners as $partner)
<div class="modal fade" id="editPartner{{ $partner->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('website.partners.update', $partner) }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark">
            @csrf @method('PUT')
            <div class="modal-header border-0 bg-slate-900 text-white p-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box-lux bg-indigo-500">
                        <i class="bi bi-pencil-square"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">تعديل بيانات الشريك</h5>
                        <p class="text-indigo-200 x-small mb-0">تحديث معلومات جهة الشراكة</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="row g-4">
                    <div class="col-md-7">
                        <div class="form-group-lux mb-4">
                            <label class="label-lux">اسم الجهة / المؤسسة</label>
                            <input type="text" name="name" class="field-lux" value="{{ $partner->name }}" required>
                        </div>
                        <div class="form-group-lux mb-4">
                            <label class="label-lux">تصنيف الشراكة</label>
                            <select name="type" class="field-lux">
                                @php
                                    $tiers = [
                                        'platinum' => ['label' => 'بلاتيني'],
                                        'gold' => ['label' => 'ذهبي'],
                                        'silver' => ['label' => 'فضي'],
                                        'bronze' => ['label' => 'برونزي'],
                                    ];
                                @endphp
                                @foreach($tiers as $key => $t)
                                    <option value="{{ $key }}" {{ $partner->type == $key ? 'selected' : '' }}>{{ $t['label'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group-lux mb-4">
                            <label class="label-lux">رابط الموقع</label>
                            <input type="url" name="website_url" class="field-lux" value="{{ $partner->website_url }}">
                        </div>
                        <div class="form-group-lux">
                            <label class="label-lux">وصف مختصر</label>
                            <textarea name="description" class="field-lux no-resize" rows="3">{{ $partner->description }}</textarea>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="label-lux mb-3 text-center d-block">شعار الجهة</label>
                        <div class="upload-zone-lux" id="editZone{{ $partner->id }}">
                            <input type="file" name="logo" class="file-hidden" onchange="previewLogoEdit(this, {{ $partner->id }})">
                            <div class="preview-area-lux" id="editLogoPreviewContainer{{ $partner->id }}">
                                @if($partner->logo_path)
                                    <img src="{{ $partner->image_url }}" class="img-fluid rounded-4 shadow-lg p-2" id="editLogoPreview{{ $partner->id }}">
                                @else
                                    <div class="placeholder-vibe"><i class="bi bi-cloud-arrow-up-fill fs-1 text-slate-500"></i></div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-indigo-solid w-100 py-3 rounded-pill fw-bold shadow-lg">حفظ التغييرات الجديدة</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@foreach($leaders as $leader)
<div class="modal fade" id="editLeader{{ $leader->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.volunteer-wall.update', $leader) }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4">
                <h5 class="modal-title fw-bold text-white">تعديل بيانات البطل</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="form-group-lux mb-3">
                    <label class="label-lux">الاسم</label>
                    <input type="text" name="name" class="field-lux" value="{{ $leader->name }}" required>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">الساعات التطوعية</label>
                    <input type="text" name="hours" class="field-lux" value="{{ $leader->hours }}" required>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">الترتيب (Rank)</label>
                    <input type="number" name="rank" class="field-lux" value="{{ $leader->rank }}" required>
                </div>
                <div class="form-group-lux">
                    <label class="label-lux">الصورة الشخصية</label>
                    <input type="file" name="image" class="field-lux">
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-indigo-solid w-100 py-3 rounded-pill fw-bold">حفظ التعديلات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<script>
function previewSlider(input, index) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('sliderPreviewContainer' + index);
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-3 h-100 object-fit-cover shadow-sm">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<script>
function previewLogoAdd(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('addLogoPreviewContainer');
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 shadow-lg p-2 h-100 w-100 object-fit-contain">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewLogoEdit(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('editLogoPreviewContainer' + id);
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 shadow-lg p-2 h-100 w-100 object-fit-contain">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewLeaderAdd(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('leaderPreviewContainerAdd').innerHTML = `
                <div class="position-relative d-inline-block">
                    <img src="${e.target.result}" class="rounded-pill shadow-2xl border border-3 border-indigo-500" style="width: 120px; height: 120px; object-fit: cover;">
                    <div class="position-absolute bottom-0 end-0 bg-indigo-500 rounded-circle p-1 px-2 text-white shadow">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </div>`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
    :root {
        --indigo-primary: #6366f1;
        --indigo-dark: #4f46e5;
        --dark-bg: #0b0e14;
        --card-dark: #1a1f2e;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-400: #94a3b8;
        --emerald-500: #10b981;
        --emerald-dark: #059669;
    }

    body {
        background-color: var(--dark-bg) !important;
        font-family: 'Tajawal', sans-serif;
        color: #f8fafc;
    }

    .honor-wall-page { min-height: 100vh; }

    /* Premium Hero Sleek */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 140px;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 0;
        margin: -30px -30px 0 -30px; /* Offset parent container padding */
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    }

    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.3; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; background: var(--indigo-primary); top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; background: #c084fc; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; background-image: url('data:image/svg+xml,...'); }

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

    .btn-action-glow-emerald {
        background: var(--emerald-500);
        color: white;
        border: none;
        font-weight: 800;
        box-shadow: 0 0 20px rgba(16, 185, 129, 0.4);
        transition: 0.4s;
    }
    .btn-action-glow-emerald:hover { background: var(--emerald-dark); transform: translateY(-5px); box-shadow: 0 15px 30px rgba(16, 185, 129, 0.6); color: white; }

    .btn-glass-indigo { background: rgba(99, 102, 241, 0.1); color: #a5b4fc; border: 1px solid rgba(99, 102, 241, 0.2); backdrop-filter: blur(10px); transition: 0.3s; }
    .btn-glass-indigo:hover { background: rgba(99, 102, 241, 0.2); transform: translateY(-3px); color: white; }

    /* Content Area */
    .content-shift-up { margin-top: -60px; position: relative; z-index: 20; padding: 0 5%; }

    /* Tabs Pill */
    .tab-pill-lux { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(20px); border: 1px solid rgba(255,255,255,0.05); padding: 8px !important; }
    .nav-pills { gap: 16px; border: none; }
    .nav-pills .nav-link { color: #94a3b8; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); border: 1px solid transparent; white-space: nowrap; }
    .nav-pills .nav-link.active { background: var(--indigo-primary); color: white; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.4); }
    .nav-pills .nav-link:not(.active):hover { color: white; background: rgba(255,255,255,0.1); }

    /* Partner Card LUX */
    .partner-card-lux {
        background: var(--card-dark);
        border-radius: 30px;
        padding: 5px;
        position: relative;
        transition: 0.4s;
        border: 1px solid rgba(255,255,255,0.05);
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    .partner-card-lux:hover { transform: translateY(-12px) scale(1.03); border-color: var(--indigo-primary); box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6); }

    .card-status-pill.tier-label { 
        position: absolute; 
        top: -12px; 
        left: 50%; 
        transform: translateX(-50%); 
        padding: 6px 16px; 
        border-radius: 100px; 
        font-size: 0.72rem; 
        font-weight: 900; 
        white-space: nowrap; 
        z-index: 5; 
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.3);
    }
    
    .tier-platinum { 
        background: linear-gradient(145deg, #ffffff 0%, #f1f5f9 30%, #cbd5e1 70%, #94a3b8 100%); 
        color: #000000 !important; 
        border: 1px solid #fff;
        box-shadow: 0 4px 15px rgba(255,255,255,0.3), inset 0 2px 4px rgba(255,255,255,0.8);
    }
    .tier-gold { 
        background: linear-gradient(145deg, #fff7ed 0%, #fbbf24 30%, #b45309 80%, #78350f 100%); 
        color: #2b1100 !important; 
        border: 1px solid #fde68a;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3), inset 0 2px 4px rgba(255,255,255,0.4);
    }
    .tier-silver { 
        background: linear-gradient(145deg, #f8fafc 0%, #94a3b8 40%, #475569 80%, #1e293b 100%); 
        color: #000000 !important; 
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 15px rgba(148, 163, 184, 0.2), inset 0 2px 4px rgba(255,255,255,0.3);
    }
    .tier-bronze { 
        background: linear-gradient(145deg, #fff2e2 0%, #d97706 40%, #92400e 80%, #451a03 100%); 
        color: #2b1100 !important; 
        border: 1px solid #fed7aa;
        box-shadow: 0 4px 15px rgba(146, 64, 14, 0.3), inset 0 2px 4px rgba(255,255,255,0.2);
    }
    .tier-strategic { 
        background: linear-gradient(145deg, #f0f9ff 0%, #bae6fd 100%); /* Lighter Sky Blue */
        color: #000000 !important; 
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.5);
        font-weight: 900;
    }
    .tier-supporter { 
        background: linear-gradient(145deg, #fff1f2 0%, #fbcfe8 100%); /* Lighter Pink */
        color: #000000 !important; 
        box-shadow: inset 0 2px 4px rgba(255,255,255,0.5);
        font-weight: 900;
        text-shadow: none; /* Ensure no shadow interferes with darkness */
    }

    .card-inner-lux { padding: 25px 15px; }
    .logo-box-lux { height: 110px; background: rgba(0,0,0,0.2); border-radius: 20px; display: flex; align-items: center; justify-content: center; overflow: hidden; }
    .partner-logo-img { max-width: 80%; max-height: 80%; object-fit: contain; filter: brightness(0.9) contrast(1.1); transition: 0.4s; }
    .partner-card-lux:hover .partner-logo-img { filter: brightness(1.1) contrast(1.1); transform: scale(1.15) rotate(2deg); }

    .partner-name-lux { font-weight: 700; font-size: 0.85rem; }
    
    .btn-action-sm { width: 34px; height: 34px; border-radius: 10px; display: flex; align-items: center; justify-content: center; border: none; transition: 0.3s; font-size: 0.9rem; }
    .edit-btn { background: rgba(99, 102, 241, 0.1); color: var(--indigo-primary); }
    .edit-btn:hover { background: var(--indigo-primary); color: white; }
    .delete-btn { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .delete-btn:hover { background: #ef4444; color: white; }

    /* Leader Card LUX */
    .leader-card-lux {
        background: var(--card-dark);
        border-radius: 40px;
        padding: 40px 20px;
        position: relative;
        text-align: center;
        transition: 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border: 1px solid rgba(255,255,255,0.05);
    }
    .leader-card-lux:hover { transform: translateY(-15px); border-color: var(--indigo-primary); box-shadow: 0 30px 60px -15px rgba(0,0,0,0.7); }

    .rank-badge-lux { position: absolute; top: 30px; right: 30px; font-size: 1.8rem; font-family: 'Outfit'; color: rgba(255,255,255,0.1); }
    .rank-num { font-weight: 900; }
    
    .leader-visual { position: relative; width: 140px; height: 140px; margin: 0 auto; z-index: 10; }
    .avatar-ring-premium { position: absolute; inset: -4px; border: 3px solid var(--indigo-primary); border-radius: 50%; padding: 4px; animation: rotate 10s linear infinite; opacity: 0.4; }
    .leader-pfp { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; border: 6px solid #1a2332; position: relative; z-index: 2; }
    .leader-pfp-placeholder { width: 100%; height: 100%; background: #0f172a; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--indigo-primary); opacity: 0.5; }

    .crown-vibe { position: absolute; top: -20px; left: 50%; transform: translateX(-50%); color: #fbbf24; font-size: 2.5rem; filter: drop-shadow(0 5px 15px rgba(251, 191, 36, 0.4)); z-index: 20; }

    .hours-chip-lux { background: rgba(99, 102, 241, 0.08); color: #a5b4fc; padding: 8px 18px; border-radius: 100px; display: inline-block; font-size: 0.8rem; font-weight: 800; border: 1px solid rgba(99, 102, 241, 0.1); }
    
    .btn-glass-danger { background: rgba(239, 68, 68, 0.05); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.1); transition: 0.3s; }
    .btn-glass-danger:hover { background: #ef4444; color: white; border-color: #ef4444; }

    /* Empty States LUX */
    .empty-state-card-lux { text-align: center; padding: 100px 30px; background: var(--card-dark); border-radius: 40px; border: 2px dashed rgba(255,255,255,0.05); }
    .empty-visual-wrapper { position: relative; width: 120px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
    .glow-pulse { position: absolute; inset: 0; background: var(--indigo-primary); opacity: 0.1; border-radius: 50%; animation: ping-lux 3s infinite; }
    .empty-icon-vibe { font-size: 4rem; color: var(--indigo-primary); position: relative; z-index: 2; }

    /* Forms & Modals LUX */
    .premium-modal-dark { background: var(--slate-900); border-radius: 35px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); }
    .header-icon-box-lux { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; }
    
    .label-lux { color: #94a3b8; font-weight: 700; font-size: 0.85rem; margin-bottom: 10px; display: block; }
    .field-lux { width: 100%; background: #0b0e14; border: 2px solid #1e293b; border-radius: 16px; padding: 14px 20px; color: white; font-weight: 600; transition: 0.3s; }
    .field-lux:focus { border-color: var(--indigo-primary); outline: none; background: #000; box-shadow: 0 0 0 5px rgba(99, 102, 241, 0.1); }
    
    .upload-zone-lux { border: 2px dashed #1e293b; border-radius: 25px; height: 180px; position: relative; overflow: hidden; transition: 0.3s; cursor: pointer; }
    .upload-zone-lux:hover { border-color: var(--indigo-primary); background: rgba(99, 102, 241, 0.03); }
    .file-hidden { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }
    .preview-area-lux { height: 100%; display: flex; align-items: center; justify-content: center; text-align: center; }
    .placeholder-vibe { color: var(--slate-800); }

    .btn-emerald-solid { background: var(--emerald-500); color: white; border: none; transition: 0.3s; }
    .btn-emerald-solid:hover { background: var(--emerald-dark); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3); }
    
    .btn-indigo-solid { background: var(--indigo-primary); color: white; border: none; transition: 0.3s; }
    .btn-indigo-solid:hover { background: var(--indigo-dark); transform: translateY(-3px); box-shadow: 0 10px 20px rgba(99, 102, 241, 0.3); }

    /* Animations */
    @keyframes ping-lux { 75%, 100% { transform: scale(1.6); opacity: 0; } }
    @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(35px); } to { opacity: 1; transform: translateY(0); } }

    .x-small { font-size: 0.75rem; }
    .text-emerald-400 { color: #34d399; }
    .text-rose-500 { color: #f43f5e; }

    @media (max-width: 991px) {
        .premium-hero-sleek { border-radius: 0 0 35px 35px; padding: 70px 0 90px; }
        .display-3 { font-size: 2.4rem; }
    }
</style>
@endsection

