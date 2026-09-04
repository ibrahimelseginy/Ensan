@extends('layouts.app')

@section('content')
<div class="projects-content-page">
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
                    <li class="breadcrumb-item active" aria-current="page">المشاريع</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-grid-1x2-fill me-2"></i> إدارة محتوى الموقع الإلكتروني
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">المشاريع</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                تحديث الصور والأوصاف والتفاصيل التقنية التي تظهر للجمهور
            </p>
        </div>
    </div>

<div class="container-fluid py-4 px-lg-5">
    {{-- Global Form for all Project Content --}}
    <form action="{{ route('website.projects.stats.update') }}" method="POST" enctype="multipart/form-data">
        @csrf
        {{-- Project Slider Images --}}
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate-slide-up overflow-hidden">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-images me-2 text-primary"></i> صور السلايدر (بديلة للهيرو)</h5>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm fw-bold">حفظ الصور</button>
                    </div>
                    <div class="p-4 bg-white">
                        <div class="row g-3">
                            @for($i = 1; $i <= 10; $i++)
                            <div class="col-md-2 col-6">
                                <div class="position-relative ratio ratio-1x1 rounded-4 overflow-hidden border bg-light slider-upload-box group-hover-overlay">
                                    @if(isset($settings["project_slider_$i"]))
                                        <img src="{{ app(\App\Services\ImageUploadService::class)->url($settings["project_slider_$i"]) }}" class="w-100 h-100 object-fit-cover shadow-sm" id="preview_slider_{{$i}}">
                                        <div class="position-absolute top-0 end-0 p-2">
                                            <span class="badge bg-success shadow-sm rounded-pill x-small">مرفوع</span>
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 m-2 shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width: 28px; height: 28px; left: 8px !important; right: auto !important; z-index: 10;" onclick="document.getElementById('delete_slider_{{$i}}').value='1'; document.getElementById('preview_slider_{{$i}}').classList.add('d-none'); document.getElementById('preview_container_{{$i}}').classList.remove('d-none'); this.classList.add('d-none'); event.preventDefault(); event.stopPropagation();">
                                            <i class="bi bi-trash3-fill x-small"></i>
                                        </button>
                                        <input type="hidden" name="delete_slider_{{$i}}" id="delete_slider_{{$i}}" value="0">
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="preview_container_{{$i}}">
                                            <i class="bi bi-image fs-2 mb-1"></i>
                                            <span class="x-small fw-bold">صورة {{ $i }}</span>
                                        </div>
                                        <img src="" class="w-100 h-100 object-fit-cover d-none shadow-sm" id="preview_slider_{{$i}}">
                                        <button type="button" class="btn btn-danger btn-sm position-absolute top-0 m-2 shadow-sm rounded-circle d-none align-items-center justify-content-center" style="width: 28px; height: 28px; left: 8px !important; right: auto !important; z-index: 10;" id="del_btn_{{$i}}" onclick="document.getElementById('preview_slider_{{$i}}').classList.add('d-none'); document.getElementById('preview_container_{{$i}}').classList.remove('d-none'); this.classList.add('d-none'); document.querySelector('input[name=project_slider_{{$i}}]').value=''; event.preventDefault(); event.stopPropagation();">
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
                            <i class="bi bi-info-circle me-1"></i> يمكنك رفع حتى 10 صور لعرضها في السلايدر العلوي لصفحة المشاريع.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Project Title & Stats Section --}}
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate-slide-up overflow-hidden">
                    <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                        <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-graph-up-arrow me-2 text-primary"></i> إحصائيات المشاريع والبيانات العامة</h5>
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm fw-bold">حفظ التغييرات</button>
                    </div>
                    
                    <div class="p-4 border-bottom bg-white">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">عنوان صفحة المشاريع (Public Title)</label>
                                <input type="text" name="projects_page_title" class="form-control" value="{{ $settings['projects_page_title'] ?? 'مشاريعنا الخيرية' }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-2">وصف الصفحة (Public Description)</label>
                                <textarea name="projects_page_description" class="form-control" rows="1">{{ $settings['projects_page_description'] ?? 'تغطية واسعة في محافظات الدلتا وعلى مستوى الجمهورية منذ أكثر من 8 سنوات' }}</textarea>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-white">
                        {{-- General Achievements Section --}}
                        <div class="mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-4 ps-2 border-start border-3 border-primary">
                                <h6 class="text-dark fw-bold mb-0">إنجازاتنا العامة <small class="text-muted ms-2" style="font-size: 0.8em;">(Achievements)</small></h6>
                            </div>
                            <div class="row g-4">
                                @php
                                    $achievements = [
                                        ['key' => 'donations', 'label' => 'التبرعات الخيرية', 'val' => '13M+', 'icon' => 'bi-cash-stack', 'color' => 'success'],
                                        ['key' => 'projects', 'label' => 'المشاريع المنفذة', 'val' => '400K', 'icon' => 'bi-diagram-3-fill', 'color' => 'info'],
                                        ['key' => 'governorates', 'label' => 'المحافظات', 'val' => '45', 'icon' => 'bi-geo-alt-fill', 'color' => 'warning'],
                                        ['key' => 'beneficiaries', 'label' => 'المستفيدون', 'val' => '300K', 'icon' => 'bi-people-fill', 'color' => 'primary'],
                                    ];
                                @endphp
                                @foreach($achievements as $ach)
                                <div class="col-6 col-lg-3">
                                    <div class="p-4 rounded-4 bg-light border text-center h-100 stats-ach-card">
                                        <div class="d-inline-flex p-3 bg-{{ $ach['color'] }} bg-opacity-10 rounded-circle text-{{ $ach['color'] }} mb-3 shadow-sm border border-{{ $ach['color'] }} border-opacity-10">
                                            <i class="bi {{ $ach['icon'] }} fs-4"></i>
                                        </div>
                                        <input type="text" name="stats_{{ $ach['key'] }}_label" class="form-control form-control-sm text-center x-small text-muted border-0 bg-transparent mb-1 p-0 fw-bold" value="{{ $settings['stats_'.$ach['key'].'_label'] ?? $ach['label'] }}">
                                        <input type="text" name="stats_{{ $ach['key'] }}" class="form-control text-center fw-bold border-0 bg-transparent text-dark fs-3 p-0" value="{{ $settings['stats_'.$ach['key']] ?? $ach['val'] }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>

                        {{-- Sponsorship Programs Section --}}
                        <div class="border-top pt-5">
                            <div class="d-flex justify-content-between align-items-center mb-4 ps-2 border-start border-3 border-success">
                                <h6 class="text-dark fw-bold mb-0">تعديل برامج الكفالة <small class="text-muted ms-2" style="font-size: 0.8em;">(Sponsorship Programs)</small></h6>
                            </div>
                            
                            <div class="row g-4">
                                {{-- Program 1: Ba'atha Al Amal --}}
                                <div class="col-md-6">
                                    <div class="card p-4 rounded-4 border shadow-sm h-100 position-relative overflow-hidden program-card-sleek">
                                        <div class="position-absolute top-0 end-0 p-3 opacity-05">
                                            <i class="bi bi-heart-pulse-fill display-1 text-primary"></i>
                                        </div>
                                        
                                        <div class="position-relative z-1 text-start" dir="rtl">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="d-inline-flex p-3 bg-primary bg-opacity-10 rounded-circle text-primary me-3 border border-primary border-opacity-10">
                                                    <i class="bi bi-heart-pulse fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">اسم البرنامج</label>
                                                    <input type="text" name="sponsorship_prog1_title" class="form-control fw-bold" value="{{ $settings['sponsorship_prog1_title'] ?? 'مشروع بعثاء الأمل' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">وصف البرنامج</label>
                                                <textarea name="sponsorship_prog1_desc" class="form-control small text-muted" rows="2">{{ $settings['sponsorship_prog1_desc'] ?? 'اكفل شخصاً من ذوي الاحتياجات الخاصة أو طفلاً من أطفال السرطان' }}</textarea>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">المميزات الأساسية</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light border text-primary"><i class="bi bi-check-circle-fill"></i></span>
                                                            <input type="text" name="sponsorship_prog1_feature1" class="form-control border shadow-none small" value="{{ $settings['sponsorship_prog1_feature1'] ?? 'دعم طبي ونفسي' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light border text-primary"><i class="bi bi-check-circle-fill"></i></span>
                                                            <input type="text" name="sponsorship_prog1_feature2" class="form-control border shadow-none small" value="{{ $settings['sponsorship_prog1_feature2'] ?? 'تأهيل وتدريب' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">قيمة الكفالة</label>
                                                <div class="d-flex align-items-center bg-light p-2 rounded-3 border shadow-sm">
                                                    <div class="flex-grow-1 border-end pe-2">
                                                        <input type="number" name="sponsorship_prog1_price" class="form-control bg-transparent text-primary border-0 fw-bold fs-3 p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog1_price'] ?? '300' }}">
                                                    </div>
                                                    <div class="flex-grow-1 ps-2">
                                                        <input type="text" name="sponsorship_prog1_currency" class="form-control bg-transparent text-muted border-0 small p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog1_currency'] ?? 'ج.م / شهر' }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Program 2: Mashrou' Zad --}}
                                <div class="col-md-6">
                                    <div class="card p-4 rounded-4 border shadow-sm h-100 position-relative overflow-hidden program-card-sleek">
                                        <div class="position-absolute top-0 end-0 p-3 opacity-05">
                                            <i class="bi bi-basket-fill display-1 text-success"></i>
                                        </div>
                                        
                                        <div class="position-relative z-1 text-start" dir="rtl">
                                            <div class="d-flex align-items-center mb-4">
                                                <div class="d-inline-flex p-3 bg-success bg-opacity-10 rounded-circle text-success me-3 border border-success border-opacity-10">
                                                    <i class="bi bi-basket fs-4"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">اسم البرنامج</label>
                                                    <input type="text" name="sponsorship_prog2_title" class="form-control fw-bold" value="{{ $settings['sponsorship_prog2_title'] ?? 'مشروع زاد' }}">
                                                </div>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">وصف البرنامج</label>
                                                <textarea name="sponsorship_prog2_desc" class="form-control small text-muted" rows="2">{{ $settings['sponsorship_prog2_desc'] ?? 'كفالة شاملة للأسر المحتاجة تشمل الغذاء والصحة والتعليم وفك الكرب' }}</textarea>
                                            </div>
                                            
                                            <div class="mb-4">
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">المميزات الأساسية</label>
                                                <div class="row g-2">
                                                    <div class="col-6">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light border text-success"><i class="bi bi-check-circle-fill"></i></span>
                                                            <input type="text" name="sponsorship_prog2_feature1" class="form-control border shadow-none small" value="{{ $settings['sponsorship_prog2_feature1'] ?? 'كفالة شهرية' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="input-group input-group-sm">
                                                            <span class="input-group-text bg-light border text-success"><i class="bi bi-check-circle-fill"></i></span>
                                                            <input type="text" name="sponsorship_prog2_feature2" class="form-control border shadow-none small" value="{{ $settings['sponsorship_prog2_feature2'] ?? 'تقارير دورية' }}">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div>
                                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-1">قيمة الكفالة</label>
                                                <div class="d-flex align-items-center bg-light p-2 rounded-3 border shadow-sm">
                                                    <div class="flex-grow-1 border-end pe-2">
                                                        <input type="number" name="sponsorship_prog2_price" class="form-control bg-transparent text-success border-0 fw-bold fs-3 p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog2_price'] ?? '500' }}">
                                                    </div>
                                                    <div class="flex-grow-1 ps-2">
                                                        <input type="text" name="sponsorship_prog2_currency" class="form-control bg-transparent text-muted border-0 small p-0 text-center shadow-none" value="{{ $settings['sponsorship_prog2_currency'] ?? 'ج.م / شهر' }}">
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
        </div>
    </form>

    {{-- Projects Management Grid --}}
    <div class="row g-4 mt-2 mb-5">
        <div class="col-lg-12">
            <div class="card border-0 shadow-sm animate-slide-up overflow-hidden" style="border-radius: 24px;">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center bg-light">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-grid-1x2-fill me-2 text-primary"></i> إدارة المشاريع <small class="text-muted ms-2" style="font-size: 0.8em;">({{ $projects->count() }} مشروع)</small></h5>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                        <i class="bi bi-plus-lg me-1"></i> إضافة مشروع جديد
                    </button>
                </div>
                <div class="p-4 bg-white">
                    <div class="row g-4">
                        @foreach($projects as $project)
                            <div class="col-md-4">
                                <div class="card h-100 border shadow-sm project-admin-card rounded-4 overflow-hidden">
                                    <div class="ratio ratio-16x9">
                                        <img src="{{ $project->image_path ? $project->image_url : 'https://placehold.co/600x400/f8fafc/64748b?text=' . urlencode($project->name) }}" class="object-fit-cover">
                                    </div>
                                    @if($project->show_badge && $project->badge_text)
                                        <div class="position-absolute top-0 start-0 m-2">
                                            <span class="badge bg-primary rounded-pill shadow-sm px-3 py-2 x-small fw-bold">
                                                @if($project->badge_icon && \Storage::disk('public')->exists($project->badge_icon))
                                                    <img src="{{ $project->getFileUrl('badge_icon') }}" style="width:14px;height:14px;" class="me-1">
                                                @endif
                                                {{ $project->badge_text }}
                                            </span>
                                        </div>
                                    @endif
                                    @if(!$project->is_visible)
                                        <div class="position-absolute top-0 end-0 m-2">
                                            <span class="badge bg-secondary rounded-pill x-small px-3"><i class="bi bi-eye-slash me-1"></i>مسودة</span>
                                        </div>
                                    @endif
                                    <div class="p-3 d-flex flex-column h-100">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="text-dark fw-bold mb-0 text-truncate" style="max-width: 180px;">{{ $project->name }}</h6>
                                            <span class="badge bg-primary-light text-primary border border-primary-light x-small">{{ $project->category ?? 'عام' }}</span>
                                        </div>
                                        <p class="text-muted x-small mb-3 line-clamp-2" style="height: 34px;">{{ $project->short_description }}</p>
                                        
                                        @if($project->stats && count($project->stats) > 0)
                                            <div class="d-flex gap-3 mb-3 border-top pt-2">
                                                @foreach(array_slice($project->stats, 0, 2) as $stat)
                                                    <div class="text-start">
                                                        <div class="text-primary fw-bold x-small">{{ $stat['value'] ?? '' }}</div>
                                                        <div class="text-muted" style="font-size:0.65rem;">{{ $stat['label'] ?? '' }}</div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif

                                        <div class="d-flex gap-2 mt-auto pt-2">
                                            <button type="button" class="btn btn-sm btn-outline-light text-muted border flex-grow-1 x-small fw-bold rounded-3" data-bs-toggle="modal" data-bs-target="#editProjectModal{{ $project->id }}">
                                                <i class="bi bi-pencil-square me-1"></i> تعديل
                                            </button>
                                            <form action="{{ route('website.projects.destroy', $project->id) }}" method="POST" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-light text-danger border px-3 rounded-3" onclick="return confirm('هل أنت متأكد من حذف هذا المشروع؟')">
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
                        <div class="text-center py-5 text-muted border rounded-4 bg-light bg-opacity-50">
                            <i class="bi bi-folder-x fs-1 opacity-25 d-block mb-3 text-primary"></i>
                            <h6 class="fw-bold">لا توجد مشاريع مضافة حالياً</h6>
                            <p class="small mb-4">ابدأ بإضافة أول مشروع ليتم عرضه للجمهور في الموقع الإلكتروني.</p>
                            <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createProjectModal">
                                <i class="bi bi-plus-lg me-1"></i> أضف أول مشروع
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

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
</script>

<style>
    .projects-content-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
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
    .slider-upload-box {
        transition: all 0.3s ease;
        border: 2px dashed var(--border) !important;
    }
    .slider-upload-box:hover {
        border-color: var(--primary) !important;
        background: var(--bg-soft) !important;
    }

    .stats-ach-card {
        transition: all 0.3s ease;
    }
    .stats-ach-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
        border-color: var(--primary) !important;
        background: white !important;
    }

    .program-card-sleek {
        transition: all 0.3s ease;
        border-radius: 20px !important;
    }
    .program-card-sleek:hover {
        border-color: var(--primary) !important;
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
    }

    .project-admin-card {
        transition: all 0.3s ease;
    }
    .project-admin-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.1) !important;
        border-color: var(--primary-light) !important;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Modal Tweaks for this page */
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.1);
    }
</style>
<script>
    function addEditFeatureRow(projectId) {
        const container = document.getElementById('features-container-' + projectId);
        const idx = container.children.length;
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 feature-row animate-slide-up align-items-center';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="features[${idx}][text]" class="form-control form-control-sm" placeholder="نص الميزة">
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-light text-primary border-secondary x-small py-0">أيقونة</label>
                    <input type="file" name="features[${idx}][icon_file]" class="form-control">
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
                <input type="text" name="stats[${idx}][value]" class="form-control form-control-sm" placeholder="القيمة">
            </div>
            <div class="col-md-3">
                <input type="text" name="stats[${idx}][label]" class="form-control form-control-sm" placeholder="الوصف">
            </div>
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-light text-primary border-secondary x-small py-0">أيقونة</label>
                    <input type="file" name="stats[${idx}][icon_file]" class="form-control">
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
@endsection
