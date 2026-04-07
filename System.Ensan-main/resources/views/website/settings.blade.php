@extends('layouts.app', ['hideGlobalAlerts' => true])

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="settings-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="hero-content-wrapper container">
            <div class="row align-items-center">
                <div class="col-lg-12 animate-reveal-right text-center">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active" aria-current="page">محتوى الصفحة الرئيسية</li>
                        </ol>
                    </nav>
                    <div class="badge-glass-premium mb-3">
                        <i class="bi bi-gear-wide-connected me-2"></i> إعدادات الموقع العام
                    </div>
                    <h1 class="display-5 fw-800 mb-3 text-dark">محتوى الصفحة الرئيسية</h1>
                    <p class="lead mb-0 mx-auto text-muted max-w-600">
                        تحديث أرقام الإنجاز المباشرة والإعدادات العامة للموقع
                    </p>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    {{-- Local Error Alerts (Success handled by toast) --}}
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
            {{-- Notification Bar Section --}}
            <div class="col-lg-8">
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="card mb-4 overflow-hidden shadow-sm animate-slide-up" style="animation-delay: 0.05s;">
                    @csrf
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-megaphone me-2 text-primary"></i> شريط الإشعارات (Breaking News)</h5>
                        <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">حفظ</button>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" name="notification_active" id="notifActive" {{ ($settings['notification_active'] ?? '') == 'on' ? 'checked' : '' }}>
                            <label class="form-check-label fw-bold" for="notifActive">تفعيل الشريط العلوي</label>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">النص المميز (Label)</label>
                                <input type="text" name="notification_label" class="form-control form-control-sm" placeholder="مثلاً: جديد" value="{{ $settings['notification_label'] ?? 'جديد' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">نص الإشعار</label>
                                <input type="text" name="notification_text" class="form-control form-control-sm" placeholder="مثلاً: انطلاق حملة الشتاء..." value="{{ $settings['notification_text'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">نص الزر</label>
                                <input type="text" name="notification_link_text" class="form-control form-control-sm" placeholder="مثلاً: اعرف المزيد" value="{{ $settings['notification_link_text'] ?? 'اعرف المزيد' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">رابط الزر (URL)</label>
                                <input type="text" name="notification_link_url" class="form-control form-control-sm" placeholder="https://..." value="{{ $settings['notification_link_url'] ?? '#' }}">
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Hero Content Section --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="card mb-4 overflow-hidden shadow-sm animate-slide-up" style="animation-delay: 0.1s;">
                    @csrf
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-layout-text-window me-2 text-primary"></i> محتوى الواجهة الرئيسية (Hero)</h5>
                        <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm fw-bold">حفظ</button>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold ws-label">العنوان الرئيسي (الجزء الأول)</label>
                                <input type="text" name="hero_title_primary" class="form-control ws-input" value="{{ $settings['hero_title_primary'] ?? '' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold ws-label">العنوان الرئيسي (الجزء الثاني - لون أخضر)</label>
                                <input type="text" name="hero_title_secondary" class="form-control ws-input" value="{{ $settings['hero_title_secondary'] ?? '' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold ws-label">وصف المؤسسة (عن إنسان)</label>
                                <textarea name="hero_description" class="form-control ws-input" rows="4">{{ $settings['hero_description'] ?? '' }}</textarea>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Statistics Section --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="card mb-4 overflow-hidden border-primary border-opacity-10 shadow-sm animate-slide-up" style="animation-delay: 0.15s; border-right: 4px solid var(--primary);">
                    @csrf
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-primary bg-opacity-5">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i> أرقام الإنجازات (Stats)</h5>
                        <button type="submit" class="btn btn-sm btn-primary px-4 shadow-sm">حفظ الإحصائيات</button>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="row g-3">
                            <div class="col-md-6 col-lg">
                                <div class="p-4 rounded-4 bg-light border text-center statistics-box h-100">
                                    <input type="text" name="stats_beneficiaries_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['stats_beneficiaries_label'] ?? 'المشاريع' }}" placeholder="العنوان">
                                    <input type="text" name="stats_beneficiaries" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['stats_beneficiaries'] ?? '600K' }}">
                                    <div class="mt-2 text-success"><i class="bi bi-people-fill fs-5"></i></div>
                                </div>
                            </div>

                            <div class="col-md-6 col-lg">
                                <div class="p-4 rounded-4 bg-light border text-center statistics-box h-100">
                                    <input type="text" name="stats_governorates_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['stats_governorates_label'] ?? 'المحافظات (كفر الشيخ ...)' }}" placeholder="العنوان">
                                    <input type="text" name="stats_governorates" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['stats_governorates'] ?? '45' }}">
                                    <div class="mt-2 text-info"><i class="bi bi-geo-alt fs-5"></i></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg">
                                <div class="p-4 rounded-4 bg-light border text-center statistics-box h-100">
                                    <input type="text" name="stats_donations_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['stats_donations_label'] ?? 'التبرعات' }}" placeholder="العنوان">
                                    <input type="text" name="stats_donations" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['stats_donations'] ?? '' }}">
                                    <div class="mt-2 text-info"><i class="bi bi-cash-stack fs-5"></i></div>
                                </div>
                            </div>
                            <div class="col-md-6 col-lg">
                                    <div class="statistics-box p-4 rounded-4 bg-light border text-center h-100">
                                        <input type="text" name="stats_volunteers_label" class="form-control form-control-sm text-center x-small fw-bold text-muted border-0 bg-transparent mb-1 p-0" value="{{ $settings['stats_volunteers_label'] ?? 'المتطوعون' }}" placeholder="العنوان">
                                        <input type="text" name="stats_volunteers" class="form-control form-control-lg text-center fw-bold border-0 bg-transparent p-0" value="{{ $settings['stats_volunteers'] ?? '' }}">
                                        <div class="mt-2 text-info"><i class="bi bi-heart fs-5"></i></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Main Slider Section --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up" style="animation-delay: 0.2s;">
                    @csrf
                    <div class="p-4 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center bg-body-tertiary">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-images me-2 text-warning"></i> صور شريط الإعلانات الرئيسي (Slider)</h5>
                        <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">حفظ الصور</button>
                    </div>
                    <div class="p-4 bg-transparent">
                        <div class="row g-3">
                            @for($i = 1; $i <= 6; $i++)
                            <div class="col-6">
                                <label class="form-label d-block ws-label small fw-bold">الصورة #{{ $i }}</label>
                                <div class="position-relative shadow-sm rounded-3 overflow-hidden" 
                                     style="height: 100px; background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.2);">
                                    @php 
                                        $path = $settings['gallery_image_'.$i] ?? null; 
                                        if($path) $path = str_replace('\\', '/', $path);
                                    @endphp
                                    <input type="hidden" name="delete_gallery_image_{{ $i }}" id="delete_gallery_image_{{ $i }}" value="0">
                                    <img src="{{ $path ? asset('storage/' . $path) . '?v=' . time() : '' }}" class="w-100 h-100 object-fit-cover {{ !$path ? 'd-none' : '' }}" id="galImg{{ $i }}">
                                    
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted {{ $path ? 'd-none' : '' }}" id="galPlaceholder{{ $i }}">
                                        <i class="bi bi-image x-small"></i>
                                    </div>
                                    <input type="file" name="gallery_image_{{ $i }}" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                           onchange="previewItem(this, {{ $i }}, 'gal')">
                                    
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 rounded-circle {{ !$path ? 'd-none' : '' }}" 
                                            style="width: 24px; height: 24px; z-index: 5;"
                                            onclick="deleteImage('gal', {{ $i }})">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </form>

                {{-- Ongoing Campaigns Section --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up" style="animation-delay: 0.25s;">
                    @csrf
                    <div class="p-4 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center bg-body-tertiary">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-megaphone me-2 text-warning"></i> قسم حملاتنا الجارية (Campaigns Section)</h5>
                        <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-4 shadow-sm fw-bold">حفظ</button>
                    </div>
                    <div class="p-4 bg-transparent">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">عنوان القسم الرئيسي</label>
                                <input type="text" name="campaigns_title" class="form-control" value="{{ $settings['campaigns_title'] ?? 'حملاتنا الجارية تنتظر مساهمتك' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">العنوان الفرعي</label>
                                <input type="text" name="campaigns_subtitle" class="form-control" value="{{ $settings['campaigns_subtitle'] ?? 'مساهمتك قد تغير حياة الآخرين للأفضل' }}">
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Guest House Home Content --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up" style="animation-delay: 0.3s;">
                    @csrf
                    <div class="p-4 border-bottom border-secondary border-opacity-10 d-flex justify-content-between align-items-center bg-body-tertiary">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-house-heart me-2 text-danger"></i> قسم ضيافة إنسان (الصفحة الرئيسية)</h5>
                        <button type="submit" class="btn btn-sm btn-danger text-white rounded-pill px-4 shadow-sm fw-bold">حفظ</button>
                    </div>
                    <div class="p-4 bg-transparent">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">عنوان القسم</label>
                                <input type="text" name="gh_home_title" class="form-control" value="{{ $settings['gh_home_title'] ?? 'ضيافة إنسان' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">المحتوى المختصر</label>
                                <textarea name="gh_home_content" class="form-control" rows="3">{{ $settings['gh_home_content'] ?? '' }}</textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">صورة القسم</label>
                                @php $ghImg = $settings['gh_home_image'] ?? null; @endphp
                                @if($ghImg)
                                    <div class="mb-2 position-relative d-inline-block">
                                        <img src="{{ asset('storage/' . $ghImg) }}?v={{ time() }}" class="rounded-3 shadow-sm" style="max-height: 150px; max-width: 100%;">
                                        <input type="hidden" name="delete_gh_home_image" id="delete_gh_home_image" value="0">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 rounded-circle" style="width: 28px; height: 28px;"
                                                onclick="if(confirm('هل أنت متأكد من حذف الصورة؟')){ document.getElementById('delete_gh_home_image').value='1'; this.closest('form').submit(); }">
                                            <i class="bi bi-x"></i>
                                        </button>
                                    </div>
                                    <div class="text-success small mb-2"><i class="bi bi-check-circle"></i> صورة مرفوعة بنجاح</div>
                                @endif
                                <input type="file" name="gh_home_image" class="form-control">
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Ideal Partner Section --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up ws-card" style="animation-delay: 0.35s;">
                    @csrf
                    <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center ws-card-header">
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-patch-check me-2 text-info"></i> قسم الشريك الأمثل لتبرعاتك (Ideal Partner)</h5>
                        <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm fw-bold">حفظ</button>
                    </div>
                    <div class="p-4 ws-card-header">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold ws-label">العنوان الرئيسي للقسم</label>
                                <input type="text" name="ideal_partner_title" class="form-control ws-input" value="{{ $settings['ideal_partner_title'] ?? 'الشريك الأمثل لتبرعاتك' }}">
                            </div>
                            
                            <h6 class="mt-4 mb-2 small fw-bold ws-label">العناصر الأربعة</h6>
                            
                            @for($i = 1; $i <= 4; $i++)
                            @php
                                $defLabels = ['', 'شفافية', 'موظف', 'قرية', 'مؤسسة'];
                                $defValues = ['', '100%', '200+', '200', 'معتمدة'];
                            @endphp
                            <div class="col-md-6">
                                <div class="p-3 rounded-4" style="background: var(--ws-stats-box-bg); border: 1px solid var(--ws-border-card);">
                                    <label class="form-label x-small ws-label mb-1">العنصر {{ $i }}</label>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <input type="text" name="ideal_partner_item{{ $i }}_value" class="form-control form-control-sm ws-input text-center fw-bold" placeholder="القيمة (مثلاً: 100%)" value="{{ $settings['ideal_partner_item'.$i.'_value'] ?? $defValues[$i] }}">
                                        </div>
                                        <div class="col-6">
                                            <input type="text" name="ideal_partner_item{{ $i }}_label" class="form-control form-control-sm ws-input text-center opacity-75" placeholder="الوصف" value="{{ $settings['ideal_partner_item'.$i.'_label'] ?? $defLabels[$i] }}" readonly>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </form>

                {{-- Field Images --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up ws-card" style="animation-delay: 0.4s;">
                    @csrf
                    <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center ws-card-header">
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-camera me-2 text-primary"></i> صور من الميدان (Field)</h5>
                        <button type="submit" class="btn btn-sm btn-primary text-white rounded-pill px-4 shadow-sm fw-bold">حفظ الصور</button>
                    </div>
                    <div class="p-4 ws-card-header">
                        <div class="row g-3">
                            @for($i = 1; $i <= 4; $i++)
                            <div class="col-6">
                                <label class="form-label d-block ws-label small fw-bold">صورة الميدان #{{ $i }}</label>
                                <div class="position-relative shadow-sm rounded-3 overflow-hidden" 
                                     style="height: 100px; background: rgba(255,255,255,0.05); border: 1px dashed rgba(255,255,255,0.2);">
                                    @php 
                                        $path = $settings['field_image_'.$i] ?? null; 
                                        if($path) $path = str_replace('\\', '/', $path);
                                    @endphp
                                    <input type="hidden" name="delete_field_image_{{ $i }}" id="delete_field_image_{{ $i }}" value="0">
                                    <img src="{{ $path ? asset('storage/' . $path) . '?v=' . time() : '' }}" class="w-100 h-100 object-fit-cover {{ !$path ? 'd-none' : '' }}" id="fieldImg{{ $i }}">
                                    <div class="w-100 h-100 d-flex flex-column align-items-center justify-content-center text-muted {{ $path ? 'd-none' : '' }}" id="fieldPlaceholder{{ $i }}">
                                        <i class="bi bi-geo-alt x-small"></i>
                                    </div>
                                    <input type="file" name="field_image_{{ $i }}" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 cursor-pointer" 
                                           onchange="previewItem(this, {{ $i }}, 'field')">
                                    
                                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 p-1 rounded-circle {{ !$path ? 'd-none' : '' }}" 
                                            style="width: 24px; height: 24px; z-index: 5;"
                                            onclick="deleteImage('field', {{ $i }})">
                                        <i class="bi bi-x"></i>
                                    </button>
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </form>

                {{-- Bottom CTA Section --}}
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="glass-card mb-5 overflow-hidden border-0 shadow-sm animate-slide-up ws-card" style="animation-delay: 0.45s;">
                    @csrf
                    <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center ws-card-header">
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-megaphone-fill me-2 text-success"></i> قسم الدعوة للعمل (Bottom CTA)</h5>
                        <button type="submit" class="btn btn-sm btn-success text-white rounded-pill px-4 shadow-sm fw-bold">حفظ</button>
                    </div>
                    <div class="p-4 ws-card-header">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold ws-label">العنوان الرئيسي</label>
                                <input type="text" name="cta_title" class="form-control ws-input" value="{{ $settings['cta_title'] ?? 'كن جزءاً من قصة نجاح' }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label small fw-bold ws-label">النص التحفيزي</label>
                                <textarea name="cta_text" class="form-control ws-input" rows="3">{{ $settings['cta_text'] ?? '' }}</textarea>
                            </div>
                            <h6 class="mt-3 mb-2 small fw-bold ws-label">إحصائيات CTA</h6>
                            <div class="col-md-4">
                                <label class="form-label x-small ws-label">قيمة 1</label>
                                <input type="text" name="cta_stat1_value" class="form-control form-control-sm mb-1 ws-input" value="{{ $settings['cta_stat1_value'] ?? '50M+' }}">
                                <input type="text" name="cta_stat1_label" class="form-control form-control-sm ws-input" value="{{ $settings['cta_stat1_label'] ?? 'تبرعات' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label x-small ws-label">قيمة 2</label>
                                <input type="text" name="cta_stat2_value" class="form-control form-control-sm mb-1 ws-input" value="{{ $settings['cta_stat2_value'] ?? '150K+' }}">
                                <input type="text" name="cta_stat2_label" class="form-control form-control-sm ws-input" value="{{ $settings['cta_stat2_label'] ?? 'ابتسامة' }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label x-small ws-label">قيمة 3</label>
                                <input type="text" name="cta_stat3_value" class="form-control form-control-sm mb-1 ws-input" value="{{ $settings['cta_stat3_value'] ?? '8+' }}">
                                <input type="text" name="cta_stat3_label" class="form-control form-control-sm ws-input" value="{{ $settings['cta_stat3_label'] ?? 'سنوات' }}">
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>



<script>
    function previewItem(input, index, prefix) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById(prefix + 'Img' + index);
                const placeholder = document.getElementById(prefix + 'Placeholder' + index);
                const deleteBtn = input.nextElementSibling; // Adjust if structure changes
                
                if(img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if(placeholder) {
                    placeholder.classList.add('d-none');
                }
                
                // Show delete button for new upload
                const parent = input.parentElement;
                const btn = parent.querySelector('.btn-danger');
                if(btn) btn.classList.remove('d-none');
                
                // Reset delete flag just in case
                const delInput = document.getElementById('delete_' + (prefix === 'gal' ? 'gallery' : 'field') + '_image_' + index);
                if(delInput) delInput.value = '0';
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function deleteImage(prefix, index) {
        if(!confirm('هل أنت متأكد من حذف هذه الصورة؟')) return;

        // Visual update
        const img = document.getElementById(prefix + 'Img' + index);
        const placeholder = document.getElementById(prefix + 'Placeholder' + index);
        const fileInput = document.querySelector(`input[name="${prefix === 'gal' ? 'gallery' : 'field'}_image_${index}"]`);
        
        if(img) {
            img.src = '';
            img.classList.add('d-none');
        }
        if(placeholder) {
            placeholder.classList.remove('d-none');
        }
        if(fileInput) {
            fileInput.value = ''; // Clear file input
        }

        // Set hidden input flag
        const type = prefix === 'gal' ? 'gallery' : 'field';
        const delInput = document.getElementById(`delete_${type}_image_${index}`);
        if(delInput) {
            delInput.value = '1';
        }

        // Hide delete button
        const btn = event.target.closest('button');
        if(btn) btn.classList.add('d-none');
    }
</script>

    </div> {{-- Closing container-fluid started at line 37 --}}
</div> {{-- Closing settings-page started at line 6 --}}

<style>
    body { font-family: 'Tajawal', sans-serif; }
    .settings-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important;
        border-bottom: 1px solid var(--border);
        overflow: hidden; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }
    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 20px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    
    .statistics-box {
        transition: all 0.3s ease;
        background: #f8fafc !important;
        border: 1px solid #e2e8f0 !important;
    }
    .statistics-box:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        border-color: var(--primary) !important;
    }

    /* Redesign slider inputs for Light Mode */
    .ws-label { color: var(--text-muted) !important; }
    .animate-reveal-right { animation: revealRight 0.8s ease-out; }
    @keyframes revealRight { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Campaign Card Preview Styles */
    .campaign-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-elevate:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
    }
    .bg-gradient-overlay {
        background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.6) 100%);
    }
    .backdrop-blur {
        backdrop-filter: blur(4px);
    }
    .glass-icon {
        background: rgba(255, 255, 255, 0.25);
        border: 1px solid rgba(255, 255, 255, 0.4);
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .shadow-inner {
        box-shadow: inset 0 2px 4px rgba(0,0,0,0.06);
    }
    .animate-pulse {
        animation: pulse 2s infinite;
    }
    @keyframes pulse {
        0% { opacity: 1; }
        50% { opacity: 0.5; }
        100% { opacity: 1; }
    }
    .btn-hover-scale {
        transition: transform 0.2s;
    }
    .btn-hover-scale:hover {
        transform: scale(1.02);
    }
</style>


<div class="toast-container position-fixed bottom-0 end-0 p-3" style="z-index: 9999">
  <div id="saveToast" class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
    <div class="d-flex">
      <div class="toast-body fw-bold">
        <i class="bi bi-check-circle-fill me-2"></i> <span id="toastMessage">{{ session('success') ?? 'تم حفظ التعديلات بنجاح!' }}</span>
      </div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
    </div>
  </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Scroll Position Restoration
        const scrollPos = localStorage.getItem('settingsScrollPos');
        if (scrollPos) {
            window.scrollTo(0, parseInt(scrollPos));
            localStorage.removeItem('settingsScrollPos');
        }

        // Show success toast if session success exists
        const hasSuccess = {!! session('success') ? 'true' : 'false' !!};
        if (hasSuccess) {
            const toastEl = document.getElementById('saveToast');
            if (toastEl) {
                const toast = new bootstrap.Toast(toastEl, { delay: 4000 });
                toast.show();
            }
        }

        // Attach listener to all relevant forms to save scroll position
        const allForms = document.querySelectorAll('form');
        allForms.forEach(form => {
            if (form.method.toLowerCase() === 'post') {
                form.addEventListener('submit', function() {
                    localStorage.setItem('settingsScrollPos', window.scrollY);
                });
            }
        });
    });
</script>
@endsection


