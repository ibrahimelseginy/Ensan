@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="projects-content-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #06b6d4;"></div>
            <div class="glow-orb-2" style="background: #0ea5e9;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">المشاريع</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-grid-1x2-fill me-2"></i> إدارة محتوى الموقع الإلكتروني
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">المشاريع</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        تحديث الصور والأوصاف والتفاصيل التقنية التي تظهر للجمهور
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left"></div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    {{-- Project Slider Images (Alternative to Hero) --}}
    {{-- Global Form for all Project Content --}}
    <form action="{{ route('website.projects.stats.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Project Slider Images --}}
        <div class="row g-4 mb-4">
            <div class="col-lg-12">
                <div class="glass-card overflow-hidden border-0 shadow-sm animate-slide-up stats-card-dark">
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-images me-2 text-info"></i> صور السلايدر (بديلة للهيرو)</h5>
                    <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm fw-bold">حفظ الصور</button>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-3">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="col-md-2 col-6">
                            <div class="position-relative ratio ratio-1x1 rounded-4 overflow-hidden border border-secondary border-opacity-25 bg-white bg-opacity-5 group-hover-overlay">
                                @if(isset($settings["project_slider_$i"]))
                                    <img src="{{ asset('storage/' . $settings["project_slider_$i"]) }}" class="w-100 h-100 object-fit-cover" id="preview_slider_{{$i}}">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <span class="badge bg-success shadow-sm rounded-pill x-small">مرفوع</span>
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 m-2 shadow-sm rounded-circle" style="width: 32px; height: 32px; left: 10px !important; right: auto !important; z-index: 1050;" onclick="document.getElementById('delete_slider_{{$i}}').value='1'; document.getElementById('preview_slider_{{$i}}').classList.add('d-none'); document.getElementById('preview_container_{{$i}}').classList.remove('d-none'); this.classList.add('d-none'); event.preventDefault(); event.stopPropagation();">
                                        <i class="bi bi-trash3-fill x-small"></i>
                                    </button>
                                    <input type="hidden" name="delete_slider_{{$i}}" id="delete_slider_{{$i}}" value="0">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted" id="preview_container_{{$i}}">
                                        <i class="bi bi-image fs-1 mb-2 opacity-50"></i>
                                        <span class="x-small">صورة {{ $i }}</span>
                                    </div>
                                    <img src="" class="w-100 h-100 object-fit-cover d-none" id="preview_slider_{{$i}}">
                                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 m-2 shadow-sm rounded-circle d-none" style="width: 32px; height: 32px; left: 10px !important; right: auto !important; z-index: 1050;" id="del_btn_{{$i}}" onclick="document.getElementById('preview_slider_{{$i}}').classList.add('d-none'); document.getElementById('preview_container_{{$i}}').classList.remove('d-none'); this.classList.add('d-none'); document.querySelector('input[name=project_slider_{{$i}}]').value=''; event.preventDefault(); event.stopPropagation();">
                                        <i class="bi bi-trash3-fill x-small"></i>
                                    </button>
                                @endif
                                <label class="position-absolute top-0 start-0 w-100 h-100 cursor-pointer">
                                    <input type="file" name="project_slider_{{$i}}" class="d-none" accept="image/*" onchange="previewSliderImage(this, 'preview_slider_{{$i}}', 'preview_container_{{$i}}'); if(document.getElementById('del_btn_{{$i}}')) document.getElementById('del_btn_{{$i}}').classList.remove('d-none');">
                                </label>
                            </div>
                        </div>
                        @endfor
                    </div>
                    <div class="mt-3 text-muted x-small">
                        <i class="bi bi-info-circle me-1"></i> يمكنك رفع حتى 5 صور لعرضها في السلايدر العلوي لصفحة المشاريع.
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{-- Project Stats Section --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-12">
            <div class="glass-card border-0 shadow-sm animate-slide-up stats-card-dark">
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-graph-up-arrow me-2 text-info"></i> إحصائيات المشاريع النوعية وبرامج الكفالة</h5>
                    <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm fw-bold">حفظ التغييرات</button>
                </div>
                <div class="p-4 bg-slate-900 border-bottom border-white border-opacity-10">
                    <div class="row g-4 mb-2">
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-info text-uppercase mb-2"><i class="bi bi-type-h1 me-1"></i> عنوان صفحة المشاريع (Public Title)</label>
                            <input type="text" name="projects_page_title" class="form-control bg-dark text-white border-secondary shadow-none" value="{{ $settings['projects_page_title'] ?? 'مشاريعنا الخيرية' }}" placeholder="مثلاً: مشاريعنا الخيرية">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label x-small fw-bold text-info text-uppercase mb-2"><i class="bi bi-text-paragraph me-1"></i> وصف الصفحة (Public Description)</label>
                            <textarea name="projects_page_description" class="form-control bg-dark text-white border-secondary shadow-none" rows="1" placeholder="مثلاً: تغطية واسعة في محافظات الدلتا...">{{ $settings['projects_page_description'] ?? 'تغطية واسعة في محافظات الدلتا وعلى مستوى الجمهورية منذ أكثر من 8 سنوات' }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-900">
                    {{-- General Achievements Section --}}
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 ps-2 border-start border-3 border-info">
                            <h6 class="text-white mb-0">إنجازاتنا <small class="text-slate-400 ms-2" style="font-size: 0.8em;">(Achievements)</small></h6>
                            <button type="submit" class="btn btn-xs btn-outline-info text-white rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem;">
                                <i class="bi bi-save me-1"></i> حفظ الإنجازات
                            </button>
                        </div>
                        <div class="row g-4">
                            <div class="col-6 col-lg-3">
                                <div class="p-4 rounded-4 stats-inner-card border border-white border-opacity-10 text-center h-100">
                                    <div class="d-inline-flex p-3 bg-success bg-opacity-10 rounded-circle text-success mb-3">
                                        <i class="bi bi-cash-stack fs-4"></i>
                                    </div>
                                    <input type="text" name="stats_donations_label" class="form-control form-control-sm text-center x-small text-slate-400 border-0 bg-transparent mb-1 p-0 fw-bold uppercase" value="{{ $settings['stats_donations_label'] ?? 'التبرعات الخيريه' }}" placeholder="العنوان">
                                    <input type="text" name="stats_donations" class="form-control text-center fw-bold border-0 bg-transparent text-white fs-3" value="{{ $settings['stats_donations'] ?? '13M+' }}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="p-4 rounded-4 stats-inner-card border border-white border-opacity-10 text-center h-100">
                                    <div class="d-inline-flex p-3 bg-info bg-opacity-10 rounded-circle text-info mb-3">
                                        <i class="bi bi-diagram-3-fill fs-4"></i>
                                    </div>
                                    <input type="text" name="stats_projects_label" class="form-control form-control-sm text-center x-small text-slate-400 border-0 bg-transparent mb-1 p-0 fw-bold uppercase" value="{{ $settings['stats_projects_label'] ?? 'المشاريع' }}" placeholder="العنوان">
                                    <input type="text" name="stats_projects" class="form-control text-center fw-bold border-0 bg-transparent text-white fs-3" value="{{ $settings['stats_projects'] ?? '400K' }}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="p-4 rounded-4 stats-inner-card border border-white border-opacity-10 text-center h-100">
                                    <div class="d-inline-flex p-3 bg-warning bg-opacity-10 rounded-circle text-warning mb-3">
                                        <i class="bi bi-geo-alt-fill fs-4"></i>
                                    </div>
                                    <input type="text" name="stats_governorates_label" class="form-control form-control-sm text-center x-small text-slate-400 border-0 bg-transparent mb-1 p-0 fw-bold uppercase" value="{{ $settings['stats_governorates_label'] ?? 'المحافظات' }}" placeholder="العنوان">
                                    <input type="text" name="stats_governorates" class="form-control text-center fw-bold border-0 bg-transparent text-white fs-3" value="{{ $settings['stats_governorates'] ?? '45' }}">
                                </div>
                            </div>
                            <div class="col-6 col-lg-3">
                                <div class="p-4 rounded-4 stats-inner-card border border-white border-opacity-10 text-center h-100">
                                    <div class="d-inline-flex p-3 bg-primary bg-opacity-10 rounded-circle text-primary mb-3">
                                        <i class="bi bi-people-fill fs-4"></i>
                                    </div>
                                    <input type="text" name="stats_beneficiaries_label" class="form-control form-control-sm text-center x-small text-slate-400 border-0 bg-transparent mb-1 p-0 fw-bold uppercase" value="{{ $settings['stats_beneficiaries_label'] ?? 'المستفيدون' }}" placeholder="العنوان">
                                    <input type="text" name="stats_beneficiaries" class="form-control text-center fw-bold border-0 bg-transparent text-white fs-3" value="{{ $settings['stats_beneficiaries'] ?? '300K' }}">
                                </div>
                            </div>
                    </div>
                </div>

                {{-- Sponsorship Programs Section (Editable) --}}
                <div class="p-4 bg-slate-900 border-top border-white border-opacity-10">
                    <div class="mb-5">
                        <div class="d-flex justify-content-between align-items-center mb-4 ps-2 border-start border-3 border-success">
                            <h6 class="text-white mb-0">تعديل برامج الكفالة <small class="text-slate-400 ms-2" style="font-size: 0.8em;">(Sponsorship Programs)</small></h6>
                             <button type="submit" class="btn btn-xs btn-outline-success text-white rounded-pill px-3 py-1 fw-bold shadow-sm" style="font-size: 0.75rem;">
                                <i class="bi bi-save me-1"></i> حفظ البرامج
                            </button>
                        </div>
                        
                        <div class="row g-4">
                        {{-- Program 1: Ba'atha Al Amal --}}
                        <div class="col-md-6">
                            <div class="glass-card p-4 rounded-4 border border-white border-opacity-10 h-100 position-relative overflow-hidden group-hover-overlay" style="background: linear-gradient(145deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.9));">
                                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                    <i class="bi bi-heart-pulse-fill display-1 text-info"></i>
                                </div>
                                
                                <div class="position-relative z-1">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="d-inline-flex p-3 bg-info bg-opacity-10 rounded-circle text-info me-3 shadow-sm border border-info border-opacity-25">
                                            <i class="bi bi-heart-pulse fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-label x-small fw-bold text-info text-uppercase mb-1"><i class="bi bi-type-h1 me-1"></i> عنوان البرنامج</label>
                                            <input type="text" name="sponsorship_prog1_title" class="form-control bg-dark bg-opacity-50 text-white border-info border-opacity-25 fw-bold shadow-none" value="{{ $settings['sponsorship_prog1_title'] ?? 'مشروع بعثاء الأمل' }}" placeholder="اسم المشروع">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label x-small fw-bold text-slate-300 text-uppercase mb-1"><i class="bi bi-text-paragraph me-1"></i> الوصف (سطر واحد يفضل)</label>
                                        <textarea name="sponsorship_prog1_desc" class="form-control bg-dark bg-opacity-50 text-slate-200 border-secondary shadow-none small" rows="2" placeholder="وصف المشروع">{{ $settings['sponsorship_prog1_desc'] ?? 'اكفل شخصاً من ذوي الاحتياجات الخاصة أو طفلاً من أطفال السرطان' }}</textarea>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label x-small fw-bold text-slate-300 text-uppercase mb-1"><i class="bi bi-list-check me-1"></i> مميزات البرنامج (تظهر كنقاط)</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-dark border-secondary text-info"><i class="bi bi-check-circle-fill"></i></span>
                                                    <input type="text" name="sponsorship_prog1_feature1" class="form-control bg-dark bg-opacity-50 text-slate-200 border-secondary shadow-none small" value="{{ $settings['sponsorship_prog1_feature1'] ?? 'دعم طبي ونفسي' }}" placeholder="ميزة 1">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-dark border-secondary text-info"><i class="bi bi-check-circle-fill"></i></span>
                                                    <input type="text" name="sponsorship_prog1_feature2" class="form-control bg-dark bg-opacity-50 text-slate-200 border-secondary shadow-none small" value="{{ $settings['sponsorship_prog1_feature2'] ?? 'تأهيل وتدريب' }}" placeholder="ميزة 2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label x-small fw-bold text-warning text-uppercase mb-1"><i class="bi bi-cash-coin me-1"></i> قيمة الكفالة الدورية</label>
                                        <div class="d-flex align-items-center bg-dark bg-opacity-75 p-2 rounded-3 border border-warning border-opacity-50 shadow-inner">
                                            <div class="flex-grow-1 border-end border-secondary pe-2">
                                                <input type="number" name="sponsorship_prog1_price" class="form-control bg-transparent text-warning border-0 fw-bold fs-3 p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog1_price'] ?? '300' }}">
                                            </div>
                                            <div class="flex-grow-1 ps-2">
                                                <input type="text" name="sponsorship_prog1_currency" class="form-control bg-transparent text-slate-300 border-0 small p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog1_currency'] ?? 'ج.م / شهر' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Program 2: Mashrou' Zad --}}
                        <div class="col-md-6">
                            <div class="glass-card p-4 rounded-4 border border-white border-opacity-10 h-100 position-relative overflow-hidden group-hover-overlay" style="background: linear-gradient(145deg, rgba(30, 41, 59, 0.7), rgba(15, 23, 42, 0.9));">
                                <div class="position-absolute top-0 end-0 p-3 opacity-25">
                                    <i class="bi bi-basket-fill display-1 text-success"></i>
                                </div>
                                
                                <div class="position-relative z-1">
                                    <div class="d-flex align-items-center mb-4">
                                        <div class="d-inline-flex p-3 bg-success bg-opacity-10 rounded-circle text-success me-3 shadow-sm border border-success border-opacity-25">
                                            <i class="bi bi-basket fs-4"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <label class="form-label x-small fw-bold text-success text-uppercase mb-1"><i class="bi bi-type-h1 me-1"></i> عنوان البرنامج</label>
                                            <input type="text" name="sponsorship_prog2_title" class="form-control bg-dark bg-opacity-50 text-white border-success border-opacity-25 fw-bold shadow-none" value="{{ $settings['sponsorship_prog2_title'] ?? 'مشروع زاد' }}" placeholder="اسم المشروع">
                                        </div>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label x-small fw-bold text-slate-300 text-uppercase mb-1"><i class="bi bi-text-paragraph me-1"></i> الوصف (سطر واحد يفضل)</label>
                                        <textarea name="sponsorship_prog2_desc" class="form-control bg-dark bg-opacity-50 text-slate-200 border-secondary shadow-none small" rows="2" placeholder="وصف المشروع">{{ $settings['sponsorship_prog2_desc'] ?? 'كفالة شاملة للأسر المحتاجة تشمل الغذاء والصحة والتعليم وفك الكرب' }}</textarea>
                                    </div>
                                    
                                    <div class="mb-4">
                                        <label class="form-label x-small fw-bold text-slate-300 text-uppercase mb-1"><i class="bi bi-list-check me-1"></i> مميزات البرنامج (تظهر كنقاط)</label>
                                        <div class="row g-2">
                                            <div class="col-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-dark border-secondary text-success"><i class="bi bi-check-circle-fill"></i></span>
                                                    <input type="text" name="sponsorship_prog2_feature1" class="form-control bg-dark bg-opacity-50 text-slate-200 border-secondary shadow-none small" value="{{ $settings['sponsorship_prog2_feature1'] ?? 'كفالة شهرية' }}" placeholder="ميزة 1">
                                                </div>
                                            </div>
                                            <div class="col-6">
                                                <div class="input-group input-group-sm">
                                                    <span class="input-group-text bg-dark border-secondary text-success"><i class="bi bi-check-circle-fill"></i></span>
                                                    <input type="text" name="sponsorship_prog2_feature2" class="form-control bg-dark bg-opacity-50 text-slate-200 border-secondary shadow-none small" value="{{ $settings['sponsorship_prog2_feature2'] ?? 'تقارير دورية' }}" placeholder="ميزة 2">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="form-label x-small fw-bold text-warning text-uppercase mb-1"><i class="bi bi-cash-coin me-1"></i> قيمة الكفالة الدورية</label>
                                        <div class="d-flex align-items-center bg-dark bg-opacity-75 p-2 rounded-3 border border-warning border-opacity-50 shadow-inner">
                                            <div class="flex-grow-1 border-end border-secondary pe-2">
                                                <input type="number" name="sponsorship_prog2_price" class="form-control bg-transparent text-warning border-0 fw-bold fs-3 p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog2_price'] ?? '500' }}">
                                            </div>
                                            <div class="flex-grow-1 ps-2">
                                                <input type="text" name="sponsorship_prog2_currency" class="form-control bg-transparent text-slate-300 border-0 small p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog2_currency'] ?? 'ج.م / شهر' }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>


            </div>
        </div>

    </form>

    {{-- Projects Management Grid --}}
    <div class="row g-4 mt-4 mb-5">
        <div class="col-lg-12">
            <div class="glass-card border-0 shadow-lg animate-slide-up stats-card-dark" style="border-radius: 30px; overflow: hidden;">
                <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center bg-slate-900">
                    <h6 class="text-white mb-0 ps-2 border-start border-3 border-warning"><i class="bi bi-grid-1x2-fill me-2 text-warning"></i> إدارة المشاريع <small class="text-slate-400 ms-2" style="font-size: 0.8em;">({{ $projects->count() }} مشروع)</small></h6>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-info rounded-pill px-4 fw-bold shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                            <i class="bi bi-plus-lg me-1"></i> إضافة مشروع جديد
                        </button>
                    </div>
                </div>
                <div class="p-4 bg-slate-900">
                    <div class="row g-4">
                        @foreach($projects as $project)
                            <div class="col-md-4">
                                <div class="project-card-admin position-relative rounded-4 overflow-hidden border border-white border-opacity-10 bg-white bg-opacity-5" style="transition: transform 0.3s ease, box-shadow 0.3s ease;">
                                    <div class="ratio ratio-16x9">
                                        <img src="{{ $project->image_path ? $project->image_url : 'https://placehold.co/600x400/1e293b/64748b?text=' . urlencode($project->name) }}" class="object-fit-cover">
                                    </div>
                                    @if($project->show_badge && $project->badge_text)
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-warning text-dark rounded-pill shadow-sm px-3 py-2 x-small fw-bold">
                                                @if($project->badge_icon && \Storage::disk('public')->exists($project->badge_icon))
                                                    <img src="{{ $project->getFileUrl('badge_icon') }}" style="width:14px;height:14px;" class="me-1">
                                                @endif
                                                {{ $project->badge_text }}
                                            </span>
                                        </div>
                                    @endif
                                    @if(!$project->is_visible)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-danger bg-opacity-80 rounded-pill x-small"><i class="bi bi-eye-slash me-1"></i>مخفي</span>
                                        </div>
                                    @endif
                                    <div class="p-3">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="text-white fw-bold mb-0 text-truncate" style="max-width: 180px;">{{ $project->name }}</h6>
                                            <span class="badge bg-info bg-opacity-10 text-info x-small">{{ $project->category ?? 'عام' }}</span>
                                        </div>
                                        <p class="text-slate-400 x-small mb-2" style="height: 32px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $project->short_description }}</p>
                                        @if($project->features && count($project->features) > 0)
                                            <div class="d-flex flex-wrap gap-1 mb-2">
                                                @foreach(array_slice($project->features, 0, 3) as $feat)
                                                    <span class="badge bg-info bg-opacity-10 text-info x-small rounded-pill">{{ $feat['text'] ?? '' }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                        @if($project->stats && count($project->stats) > 0)
                                            <div class="d-flex gap-3 mb-3">
                                                @foreach(array_slice($project->stats, 0, 2) as $stat)
                                                    <div class="text-center">
                                                        <div class="text-white fw-bold x-small">{{ $stat['value'] ?? '' }}</div>
                                                        <div class="text-slate-400" style="font-size:0.6rem;">{{ $stat['label'] ?? '' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-outline-warning rounded-pill flex-grow-1 x-small fw-bold" data-bs-toggle="modal" data-bs-target="#editProjectModal{{ $project->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> تعديل
                                            </button>
                                            <form action="{{ route('website.projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3" onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @if($projects->isEmpty())
                        <div class="text-center py-5 text-muted">
                            <i class="bi bi-folder-x fs-1 opacity-25 d-block mb-3"></i>
                            <p>لا توجد مشاريع مضافة حالياً</p>
                            <button type="button" class="btn btn-sm btn-info rounded-pill px-4 fw-bold shadow-sm text-white" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                <i class="bi bi-plus-lg me-1"></i> أضف أول مشروع
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div> {{-- projects-content-page --}}

{{-- Modals Area --}}
@include('website.create_project_modal')
@foreach($projects as $project)
    @include('website.edit_project_modal', ['project' => $project])
@endforeach



<script>
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

    function addEditFeatureRow(projectId) {
        const container = document.getElementById(`features-container-${projectId}`);
        const idx = container.querySelectorAll('.feature-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 feature-row';
        row.innerHTML = `
            <div class="col-4">
                <input type="text" name="features[${idx}][text]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="الميزة">
            </div>
            <div class="col-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-slate-800 text-info border-secondary"><i class="bi bi-star"></i></span>
                    <input type="text" name="features[${idx}][icon]" class="form-control bg-dark text-white border-secondary icon-input" placeholder="أيقونة" readonly onclick="openIconPicker(this)">
                    <button class="btn btn-outline-info" type="button" onclick="openIconPicker(this.previousElementSibling)"><i class="bi bi-grid-3x3-gap"></i></button>
                </div>
            </div>
            <div class="col-4">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.feature-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    }

    function addEditStatRow(projectId) {
        const container = document.getElementById(`stats-container-${projectId}`);
        const idx = container.querySelectorAll('.stat-row').length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 stat-row';
        row.innerHTML = `
            <div class="col-4">
                <input type="text" name="stats[${idx}][value]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="القيمة">
            </div>
            <div class="col-4">
                <div class="input-group input-group-sm">
                    <span class="input-group-text bg-slate-800 text-warning border-secondary"><i class="bi bi-graph-up"></i></span>
                    <input type="text" name="stats[${idx}][icon]" class="form-control bg-dark text-white border-secondary icon-input" placeholder="أيقونة" readonly onclick="openIconPicker(this)">
                </div>
            </div>
            <div class="col-4">
                <input type="text" name="stats[${idx}][label]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="الوصف">
            </div>
            <div class="col-1">
                <button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('.stat-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    }
    function previewCardIcon(input, index) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('prevIcon' + index + 'View');
                if(container) container.innerHTML = `<img src="${e.target.result}" style="width: 20px; height: 20px; object-fit: contain;">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewCardMainImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('prevMainImage');
                if(img) img.src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewCardMainIcon(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const cont = document.getElementById('prevMainIconContainer');
                if(cont) cont.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 rounded-circle object-fit-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function updateCardPreview() {
        const titleInput = document.querySelector('[name="featured_campaign_title"]');
        if(!titleInput || !document.getElementById('prevTitle')) return;

        document.getElementById('prevTitle').innerText = titleInput.value;
        document.getElementById('prevDesc').innerText = (document.querySelector('[name="featured_campaign_desc"]') || {}).value || '';
        document.getElementById('prevBeneficiaries').innerText = (document.querySelector('[name="featured_campaign_beneficiaries"]') || {}).value || '';
        document.getElementById('prevDays').innerText = (document.querySelector('[name="featured_campaign_days"]') || {}).value || '';
        document.getElementById('prevSharePrice').innerText = (document.querySelector('[name="featured_campaign_share_price"]') || {}).value || '';
        
        const startDaysInput = document.querySelector('[name="featured_campaign_starts_days"]');
        if(document.getElementById('prevStartDays') && startDaysInput) document.getElementById('prevStartDays').innerText = startDaysInput.value;
        
        const startHoursInput = document.querySelector('[name="featured_campaign_starts_hours"]');
        if(document.getElementById('prevStartHours') && startHoursInput) document.getElementById('prevStartHours').innerText = startHoursInput.value;

        const statusInput = document.querySelector('[name="featured_campaign_status"]');
        if(!statusInput) return;
        const status = statusInput.value;
        document.getElementById('prevStatus').innerText = status;
        
        const badge = document.getElementById('prevStatusBadge');
        const pulseIcon = document.getElementById('prevStatusIcon');
        if(badge) {
            badge.className = 'badge rounded-pill shadow-sm px-3 py-2'; 
            if(status === 'نشطة الآن') badge.classList.add('bg-danger', 'bg-opacity-90');
            else if(status === 'قادمة') badge.classList.add('bg-info', 'bg-opacity-90');
            else badge.classList.add('bg-secondary', 'bg-opacity-90');
        }
        if(pulseIcon) {
            if(status === 'نشطة الآن') pulseIcon.classList.remove('d-none');
            else pulseIcon.classList.add('d-none');
        }

        const beneCont = document.getElementById('prevBeneficiariesContainer');
        const daysCont = document.getElementById('prevDaysContainer');
        const progCont = document.getElementById('prevProgressContainer');
        const shareCont = document.getElementById('prevSharePriceContainer');
        const goalCont = document.getElementById('prevGoalContainer');
        const barCont = document.querySelector('.progress');
        const startCont = document.getElementById('prevStartsCountdown');
        const upcomingFormGroup = document.getElementById('upcomingSettings');

        const sharePrice = (document.querySelector('[name="featured_campaign_share_price"]') || {}).value || '';

        if(status === 'منتهية') {
            if(upcomingFormGroup) upcomingFormGroup.style.display = 'none';
            if(beneCont) beneCont.classList.remove('d-none');
            if(daysCont) daysCont.classList.add('d-none');
            if(progCont) progCont.classList.add('d-none');
            if(shareCont) { if(sharePrice) shareCont.classList.remove('d-none'); else shareCont.classList.add('d-none'); }
            if(goalCont) goalCont.classList.add('d-none');
            if(barCont) barCont.classList.add('d-none');
            if(startCont) startCont.classList.add('d-none');
        } else if(status === 'قادمة') {
            if(upcomingFormGroup) upcomingFormGroup.style.display = 'block';
            if(beneCont) beneCont.classList.add('d-none');
            if(daysCont) daysCont.classList.add('d-none');
            if(progCont) progCont.classList.add('d-none');
            if(shareCont) { if(sharePrice) shareCont.classList.remove('d-none'); else shareCont.classList.add('d-none'); }
            if(goalCont) goalCont.classList.remove('d-none');
            if(barCont) barCont.classList.add('d-none');
            const showStartInput = document.querySelector('[name="featured_campaign_show_countdown"]');
            if(startCont && showStartInput) {
                if(showStartInput.checked) startCont.classList.remove('d-none');
                else startCont.classList.add('d-none');
            }
        } else {
            if(upcomingFormGroup) upcomingFormGroup.style.display = 'none';
            if(beneCont) beneCont.classList.remove('d-none');
            if(daysCont) daysCont.classList.remove('d-none');
            if(progCont) progCont.classList.remove('d-none');
            if(shareCont) { if(sharePrice) shareCont.classList.remove('d-none'); else shareCont.classList.add('d-none'); }
            if(goalCont) goalCont.classList.remove('d-none');
            if(barCont) barCont.classList.remove('d-none');
            if(startCont) startCont.classList.add('d-none');
        }

        document.getElementById('prevCollected').innerText = (document.querySelector('[name="featured_campaign_collected"]') || {}).value || '';
        document.getElementById('prevGoal').innerText = (document.querySelector('[name="featured_campaign_goal"]') || {}).value || '';
        const progInput = document.querySelector('[name="featured_campaign_progress"]');
        if(progInput) {
            document.getElementById('prevProgressText').innerText = progInput.value;
            document.getElementById('prevProgressBar').style.width = progInput.value + '%';
        }

        let btnText = 'ساهم الآن';
        if(status === 'قادمة') btnText = 'ذكرني عند البدء';
        if(status === 'منتهية') btnText = 'انتهت الحملة';
        document.getElementById('prevBtnText').innerText = btnText;
        
        const btn = document.getElementById('prevBtn');
        if(btn) {
            if(status === 'منتهية') { btn.classList.remove('btn-info'); btn.classList.add('btn-secondary'); }
            else { btn.classList.remove('btn-secondary'); btn.classList.add('btn-info'); }
        }

        const btnColorInput = document.querySelector('[name="featured_campaign_btn_color"]');
        if(btnColorInput && btn) {
            const btnColor = btnColorInput.value;
            btn.style.setProperty('background-color', btnColor, 'important');
            btn.style.setProperty('border-color', btnColor, 'important');
            const r = parseInt(btnColor.substr(1,2), 16), g = parseInt(btnColor.substr(3,2), 16), b = parseInt(btnColor.substr(5,2), 16);
            const brightness = (r * 299 + g * 587 + b * 114) / 1000;
            btn.style.setProperty('color', (brightness < 128) ? '#ffffff' : '#000000', 'important');
        }
        
        const showRegStatsInp = document.querySelector('[name="featured_campaign_show_register_stats"]');
        if(status === 'قادمة' && showRegStatsInp && showRegStatsInp.checked) {
            document.getElementById('prevBeneficiariesLabel').innerText = (document.querySelector('[name="featured_campaign_register_label"]') || {}).value || 'المرحلة';
            document.getElementById('prevBeneficiaries').innerText = (document.querySelector('[name="featured_campaign_register_value1"]') || {}).value || '';
            if(beneCont) beneCont.classList.remove('d-none');
            document.getElementById('prevDaysLabel').innerText = (document.querySelector('[name="featured_campaign_register_label2"]') || {}).value || 'الحالة';
            document.getElementById('prevDays').innerText = (document.querySelector('[name="featured_campaign_register_value2"]') || {}).value || '';
            if(daysCont) daysCont.classList.remove('d-none');
            if(document.getElementById('upcomingProgressInfo')) document.getElementById('upcomingProgressInfo').classList.remove('d-none');
            if(document.getElementById('prevPreparationLabel')) document.getElementById('prevPreparationLabel').innerText = (document.querySelector('[name="featured_campaign_progress_label"]') || {}).value || 'التحضير';
            if(document.getElementById('prevProgressPct')) document.getElementById('prevProgressPct').innerText = (progInput ? progInput.value : '0');
        } else if(status !== 'قادمة') {
            document.getElementById('prevBeneficiariesLabel').innerText = 'مستفيد';
            document.getElementById('prevDaysLabel').innerText = 'يوم متبقي';
            if(document.getElementById('upcomingProgressInfo')) document.getElementById('upcomingProgressInfo').classList.add('d-none');
        }
        
        for(let i=1; i<=2; i++) {
            const labelInp = document.querySelector(`[name="featured_campaign_icon${i}_label"]`);
            if(!labelInp) continue;
            const col = document.getElementById(`prevIcon${i}Col`);
            if(col) {
                if(labelInp.value.trim() === '' && (document.querySelector(`[name="featured_campaign_icon${i}_value"]`) || {}).value.trim() === '') col.classList.add('d-none');
                else {
                    col.classList.remove('d-none');
                    document.getElementById(`prevIcon${i}Label`).innerText = labelInp.value;
                    document.getElementById(`prevIcon${i}Value`).innerText = (document.querySelector(`[name="featured_campaign_icon${i}_value"]`) || {}).value;
                }
            }
        }
    }

    document.addEventListener('DOMContentLoaded', updateCardPreview);
</script>
@endsection

@section('styles')
<style>
    body { background-color: #0b0e14 !important; }
    .projects-content-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #164e63 0%, #0891b2 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #a5f3fc; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @media (max-width: 991px) { .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; } .display-4 { font-size: 2.2rem; } }

    .glass-card { 
        background: rgba(255,255,255,0.85); 
        backdrop-filter: blur(10px); 
        border: 1px solid rgba(255,255,255,0.3); 
        border-radius: 20px; 
        box-shadow: 0 8px 32px rgba(0,0,0,0.05); 
    }
    .x-small { font-size: 0.7rem; }
    .cursor-pointer { cursor: pointer; }
    .animate-delay-1 { animation-delay: 0.1s; }
    /* Fixed Modal Logic for clarity and visibility */
    .modal-backdrop.show {
        opacity: 0.6 !important;
        backdrop-filter: none !important; /* Prevent the foggy/blurry screen */
        -webkit-backdrop-filter: none !important;
        background-color: #000 !important;
    }
    .modal.show {
        display: block !important;
        background: none !important; /* Remove semi-transparent overlay on modal element itself */
        backdrop-filter: none !important; /* Remove blur from modal element */
    }
    .modal-premium-dark {
        background-color: #0f172a !important; /* Solid Slate 900 */
        background-image: none !important;
        color: #f8fafc !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important;
        opacity: 1 !important;
    }
    .modal-premium-dark .modal-body {
        background-color: #0f172a !important;
        opacity: 1 !important;
        position: relative;
        z-index: 1;
    }
    .modal-premium-dark .form-control, 
    .modal-premium-dark .form-select, 
    .modal-premium-dark textarea {
        background-color: #1e293b !important; /* Solid Slate 800 */
        color: #f8fafc !important;
        border-color: #334155 !important;
        opacity: 1 !important;
    }
    .modal-premium-dark .modal-header {
        background: #1e293b !important; /* Slate 800 */
        border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    .modal-premium-dark .form-control {
        background: #020617 !important; /* Slate 950 */
        border-color: #334155 !important; /* Slate 700 */
        color: #f8fafc !important;
    }
    .modal-premium-dark .form-control:focus {
        border-color: #06b6d4 !important;
        box-shadow: 0 0 0 2px rgba(6, 182, 212, 0.2) !important;
    }
    .modal-premium-dark .nav-pills .nav-link {
        color: #94a3b8 !important;
        border-radius: 12px;
        transition: all 0.3s;
    }
    .modal-premium-dark .nav-pills .nav-link.active {
        background: #06b6d4 !important;
        color: #fff !important;
    }
    .modal-header {
        border-bottom: 1px solid #f1f5f9 !important;
    }
    .tab-content {
        padding: 20px 0;
    }
    .nav-tabs .nav-link {
        border: none !important;
        color: #64748b;
        font-weight: 600;
        padding: 12px 20px;
        border-bottom: 2px solid transparent !important;
    }
    .nav-tabs .nav-link.active {
        color: #10b981 !important;
        border-bottom: 2px solid #10b981 !important;
        background: transparent !important;
    }
    
    /* Row management */
    .dynamic-row {
        position: relative;
        padding: 15px;
        background: #f8fafc;
        border-radius: 12px;
        margin-bottom: 15px;
        border: 1px solid #e2e8f0;
    }
    .remove-row-btn {
        position: absolute;
        top: -10px;
        left: -10px;
        width: 25px;
        height: 25px;
        border-radius: 50%;
        background: #ef4444;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        z-index: 10;
        border: none;
        box-shadow: 0 2px 5px rgba(239, 68, 68, 0.3);
    }
    /* Custom Dark Stats Card */
    /* Custom Slate Utilities */
    .bg-slate-900 { background-color: #0f172a !important; }
    .bg-slate-800 { background-color: #1e293b !important; }
    .bg-slate-950 { background-color: #020617 !important; }
    .text-slate-400 { color: #94a3b8 !important; }
    
    .stats-card-dark {
        background-color: #0f172a !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
    }
    
    .stats-inner-card {
        background-color: rgba(30, 41, 59, 0.5);
        transition: all 0.3s ease;
    }
    .stats-inner-card:hover {
        background-color: rgba(30, 41, 59, 0.8);
        transform: translateY(-2px);
    }
    .stats-card-dark input::placeholder { color: #64748b; }
    
    /* Global Dark Mode Support for this page */
    [data-bs-theme="dark"] .glass-card {
        background: rgba(30, 41, 59, 0.7) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #f8fafc;
    }
    
    [data-bs-theme="dark"] .table { color: #f8fafc; }
    [data-bs-theme="dark"] .bg-light { background-color: rgba(30, 41, 59, 0.5) !important; border-color: rgba(255,255,255,0.05) !important; }
    [data-bs-theme="dark"] .text-muted { color: #94a3b8 !important; }
    /* Campaign Card Preview Styles */
    .campaign-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .bg-gradient-overlay { background: linear-gradient(to bottom, rgba(0,0,0,0.3) 0%, rgba(0,0,0,0) 50%, rgba(0,0,0,0.6) 100%); }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .animate-pulse { animation: pulse 2s infinite; }
    @keyframes pulse { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }
    .custom-range::-webkit-slider-runnable-track { background: rgba(255,255,255,0.1); border-radius: 10px; height: 8px; }
    .custom-range::-webkit-slider-thumb { margin-top: -6px; background: #0dcaf0; border: none; }

    /* Modal Clarity Fixes - CRITICAL FIX FOR FOGGINESS */
    .modal-backdrop.show {
        opacity: 0.7 !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        background-color: #000 !important;
    }
    .modal.show {
        display: block !important;
        background: rgba(0,0,0,0.4) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
    .modal-dialog {
        z-index: 2100 !important;
        filter: none !important;
    }
    .modal-content {
        filter: none !important;
        -webkit-filter: none !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    /* Ensure no parent containers have filters that could propagate */
    .projects-content-page, .container-fluid, .content-wrapper {
        filter: none !important;
    }
      
      
            
      

      

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
    function addEditFeatureRow(projectId) {
        const container = document.getElementById('features-container-' + projectId);
        const idx = container.children.length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 feature-row animate-slide-up align-items-center';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="features[${idx}][text]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="نص الميزة">
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-slate-800 text-info border-secondary x-small py-0">أيقونة</label>
                    <input type="file" name="features[${idx}][icon_file]" class="form-control bg-dark text-white border-secondary">
                    <input type="hidden" name="features[${idx}][icon]" value="">
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1" onclick="this.closest('.feature-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    }

    function addEditStatRow(projectId) {
        const container = document.getElementById('stats-container-' + projectId);
        const idx = container.children.length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-3 stat-row animate-slide-up align-items-center';
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="stats[${idx}][value]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="القيمة">
            </div>
            <div class="col-md-3">
                <input type="text" name="stats[${idx}][label]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="الوصف">
            </div>
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-slate-800 text-warning border-secondary x-small py-0">أيقونة</label>
                    <input type="file" name="stats[${idx}][icon_file]" class="form-control bg-dark text-white border-secondary">
                    <input type="hidden" name="stats[${idx}][icon]" value="">
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1" onclick="this.closest('.stat-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
    }
</script>

<style>
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
@endsection









