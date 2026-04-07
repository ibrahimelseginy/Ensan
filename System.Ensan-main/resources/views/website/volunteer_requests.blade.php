@extends('layouts.app')

@section('content')
<div class="volunteer-requests-mgmt-page">
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
                    <li class="breadcrumb-item active" aria-current="page">تطوع معنا</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-person-heart me-2"></i> بناء مجتمع إنسان المعطاء
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">طلبات التطوع والانضمام</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                {{ $settings['volunteer_description'] ?? 'نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي .' }}
            </p>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="container-fluid py-4 px-lg-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0 text-dark border-start border-4 border-primary ps-3">طلبات التطوع الواردة</h5>
            <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold small shadow-sm">{{ $requests->count() }} طلب إجمالي</span>
        </div>

        <div class="row g-4">
            @forelse($requests as $request)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm volunteer-card-sleek rounded-4 animate-slide-up overflow-hidden" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    @php
                        $statusMap = [
                            'new' => ['label' => 'طلب جديد', 'color' => 'primary'],
                            'contacted' => ['label' => 'تم التواصل', 'color' => 'warning'],
                            'accepted' => ['label' => 'مقبول', 'color' => 'success'],
                            'rejected' => ['label' => 'مرفوض', 'color' => 'danger'],
                        ];
                        $currStatus = $statusMap[$request->status] ?? ['label' => $request->status, 'color' => 'primary'];
                    @endphp
                    
                    <div class="position-absolute top-0 start-0 m-3 z-10">
                        <span class="badge bg-{{ $currStatus['color'] }} bg-opacity-10 text-{{ $currStatus['color'] }} rounded-pill px-3 py-1 x-small fw-bold border border-{{ $currStatus['color'] }} border-opacity-10">
                            {{ $currStatus['label'] }}
                        </span>
                    </div>

                    <div class="card-body p-4 pt-5 bg-stats-card-main">
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="avatar-soft bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold shadow-sm border border-white" style="width: 58px; height: 58px; border: 4px solid white;">
                                {{ mb_substr($request->name, 0, 1) }}
                            </div>
                            <div class="overflow-hidden">
                                <h6 class="fw-800 text-stats-main text-truncate mb-0" title="{{ $request->name }}">{{ $request->name }}</h6>
                                <div class="x-small text-primary fw-bold text-truncate mt-1">
                                    <i class="bi bi-briefcase me-1"></i> {{ $request->current_job ?? 'باحث عن عمل' }}
                                </div>
                                <div class="x-small text-muted-theme d-flex align-items-center gap-1 mt-1 opacity-75">
                                    <i class="bi bi-calendar3"></i> {{ $request->created_at->translatedFormat('d M Y') }}
                                </div>
                            </div>
                        </div>

                        <div class="p-3 bg-stats-inner-item rounded-4 border border-light-subtle mb-4">
                            <div class="x-small text-muted-theme mb-1 fw-bold opacity-75">مجال التطوع المفضل</div>
                            <div class="fw-bold text-stats-main small"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> {{ $request->area_of_interest ?? 'انضمام عام' }}</div>
                        </div>

                        <div class="vstack gap-2 mb-4 bg-stats-inner-item p-3 rounded-4 border border-light-subtle">
                            <div class="d-flex justify-content-between align-items-center x-small">
                                <span class="text-muted-theme">الجوال:</span>
                                <span class="text-stats-main fw-bold font-outfit" style="direction: ltr;">{{ $request->phone }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center x-small overflow-hidden">
                                <span class="text-muted-theme text-nowrap">البريد:</span>
                                <span class="text-stats-main fw-bold text-truncate ms-2" title="{{ $request->email }}">{{ $request->email }}</span>
                            </div>
                        </div>

                        <div class="row g-2 mt-auto">
                            <div class="col-8">
                                <button class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm x-small" data-bs-toggle="modal" data-bs-target="#viewReq{{ $request->id }}">
                                    عرض التفاصيل <i class="bi bi-arrow-left ms-1"></i>
                                </button>
                            </div>
                            <div class="col-4">
                                @if($request->cv_path && $request->cvExists())
                                    <a href="{{ route('website.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn btn-outline-danger border-0 bg-danger bg-opacity-10 w-100 rounded-pill py-2 x-small fw-bold" title="تحميل السيرة الذاتية">
                                        CV <i class="bi bi-file-earmark-pdf ms-1"></i>
                                    </a>
                                @else
                                    <button disabled class="btn btn-outline-secondary border-0 bg-light w-100 rounded-pill py-2 x-small opacity-50"><i class="bi bi-file-earmark-x"></i></button>
                                @endif
                            </div>
                            <div class="col-12 mt-2">
                                <form action="{{ route('website.volunteer-requests.destroy', $request) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-link text-danger text-decoration-none w-100 x-small fw-bold opacity-75 hover-opacity-100">
                                        <i class="bi bi-trash-fill me-1"></i> حذف الطلب نهائياً
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <div class="py-5 opacity-25">
                    <i class="bi bi-mailbox2 fs-1 d-block mb-3"></i>
                    <h6 class="fw-bold">صندوق طلبات التطوع فارغ</h6>
                    <p class="small mb-0">لم تصل أي طلبات انضمام جديدة بعد</p>
                </div>
            </div>
            @endforelse
        </div>

        {{-- Configuration Section --}}
        <div class="mt-5 pt-5 border-top border-light">
            <form action="{{ route('website.volunteer-content.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-5">
                    <div class="p-4 border-bottom bg-stats-header d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon-small bg-warning"><i class="bi bi-pencil-square text-dark"></i></div>
                            <h5 class="fw-bold mb-0 text-stats-title">تخصيص محتوى صفحة تطوع معنا</h5>
                        </div>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">حفظ التغييرات</button>
                    </div>
                    <div class="card-body p-4 p-md-5">
                        <div class="row g-4 mb-5">
                            <div class="col-lg-8">
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">عنوان الهيرو الرئيسي</label>
                                    <input type="text" name="volunteer_title" class="form-control" value="{{ $settings['volunteer_title'] ?? '' }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label small fw-bold text-muted">وصف القسم التعريفي</label>
                                    <textarea name="volunteer_description" class="form-control" rows="4">{{ $settings['volunteer_description'] ?? '' }}</textarea>
                                </div>
                                
                                <h6 class="fw-bold text-dark mb-4 border-start border-3 border-primary ps-3 small text-uppercase">إحصائيات النجاح</h6>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-muted">متطوع مسجل</label>
                                        <input type="text" name="volunteer_stats_volunteers" class="form-control text-center fw-bold text-primary" value="{{ $settings['volunteer_stats_volunteers'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-muted">ساعة تطوعية</label>
                                        <input type="text" name="volunteer_stats_hours" class="form-control text-center fw-bold text-primary" value="{{ $settings['volunteer_stats_hours'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-muted">مشروع تطوعي</label>
                                        <input type="text" name="volunteer_stats_projects" class="form-control text-center fw-bold text-primary" value="{{ $settings['volunteer_stats_projects'] ?? '' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-muted">عدد الفروع</label>
                                        <input type="text" name="volunteer_stats_branches" class="form-control text-center fw-bold text-primary" value="{{ $settings['volunteer_stats_branches'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-4">
                                <label class="form-label small fw-bold text-muted d-block text-center">صورة خلفية الهيرو (Hero Image)</label>
                                <div class="p-4 bg-light rounded-4 border upload-zone-mini h-100 d-flex flex-column align-items-center justify-content-center text-center cursor-pointer position-relative min-h-300">
                                    @php $heroPath = $settings['volunteer_hero_image'] ?? null; @endphp
                                    @if($heroPath)
                                        <img src="{{ asset('storage/' . $heroPath) }}" class="w-100 h-100 object-fit-cover shadow-sm rounded-4" id="heroPrevImg">
                                    @else
                                        <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50 mb-2"></i>
                                        <p class="x-small text-muted mb-0">رفع صورة خلفية جديدة</p>
                                    @endif
                                    <input type="file" name="volunteer_hero_image" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewHero(this)">
                                    
                                    @if($heroPath)
                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-3 shadow-sm z-10" onclick="event.stopPropagation(); document.getElementById('delete_volunteer_hero_image').value='1'; this.closest('.upload-zone-mini').querySelector('img').classList.add('opacity-25'); this.remove();">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    @endif
                                    <input type="hidden" name="delete_volunteer_hero_image" id="delete_volunteer_hero_image" value="0">
                                </div>
                            </div>
                        </div>

                        <h6 class="fw-bold text-dark mb-4 border-start border-3 border-info ps-3 small text-uppercase">سلايدر الصور البديل</h6>
                        <div class="row g-3">
                            @for($i = 1; $i <= 10; $i++)
                            <div class="col-md-4 col-lg-2-4 col-6">
                                <div class="position-relative rounded-4 border overflow-hidden bg-light slider-upload-mini ratio ratio-4x3 group-hover-overlay" style="cursor: pointer;">
                                    @php $sliderPath = $settings["volunteer_slider_$i"] ?? null; @endphp
                                    @if($sliderPath)
                                        <img src="{{ asset('storage/' . $sliderPath) }}" class="w-100 h-100 object-fit-cover shadow-sm transition-all" id="volSliderPrev{{ $i }}">
                                    @else
                                        <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="volSliderPlace{{ $i }}">
                                            <i class="bi bi-images fs-2 mb-1"></i>
                                            <span class="x-small fw-bold">{{ $i }}</span>
                                        </div>
                                        <img src="" class="w-100 h-100 object-fit-cover d-none shadow-sm transition-all" id="volSliderPrev{{ $i }}">
                                    @endif
                                    
                                    <input type="file" name="volunteer_slider_{{ $i }}" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewVolSlider(this, {{ $i }})">
                                    
                                    @if($sliderPath)
                                    <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center z-10" 
                                            style="width: 26px; height: 26px;"
                                            onclick="event.stopPropagation(); document.getElementById('delete_volunteer_slider_{{ $i }}').value='1'; this.closest('.slider-upload-mini').querySelector('.d-none').classList.remove('d-none'); document.getElementById('volSliderPrev{{ $i }}').classList.add('d-none'); this.remove();">
                                        <i class="bi bi-trash fs-xs"></i>
                                    </button>
                                    @endif
                                    <input type="hidden" name="delete_volunteer_slider_{{ $i }}" id="delete_volunteer_slider_{{ $i }}" value="0">
                                </div>
                            </div>
                            @endfor
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Detail Modals --}}
    @foreach($requests as $request)
    <div class="modal fade" id="viewReq{{ $request->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="modal-header border-bottom bg-stats-header px-4 py-3">
                    <h5 class="modal-title fw-bold text-stats-title"><i class="bi bi-info-circle-fill me-2 text-primary"></i> تفاصيل طلب المتطوع: {{ $request->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="p-4 p-md-5">
                        <section class="mb-5">
                            <h6 class="fw-bold text-primary mb-4 border-bottom pb-2 d-flex align-items-center gap-1">
                                <i class="bi bi-person-fill"></i> البيانات الشخصية والاتصال
                            </h6>
                            <div class="row g-4">
                                <div class="col-md-6 x-small">
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold d-block mb-1">الرقم القومي (ID)</label>
                                        <div class="text-dark font-outfit fs-6">{{ $request->national_id ?? '-' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold d-block mb-1">تاريخ الميلاد</label>
                                        <div class="text-dark font-outfit fs-6">{{ $request->birth_date ?? '-' }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold d-block mb-1">النوع</label>
                                        <div class="text-dark">{{ $request->gender == 'male' ? 'ذكر' : ($request->gender == 'female' ? 'أنثى' : '-') }}</div>
                                    </div>
                                </div>
                                <div class="col-md-6 x-small">
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold d-block mb-1">رقم الهاتف</label>
                                        <div class="text-primary fw-bold font-outfit fs-5" style="direction: ltr; text-align: right;">{{ $request->phone }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold d-block mb-1">البريد الإلكتروني</label>
                                        <div class="text-dark font-outfit fs-6">{{ $request->email }}</div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="text-muted fw-bold d-block mb-1">العنوان الحالي</label>
                                        <div class="text-dark">{{ $request->current_address ?? $request->address ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h6 class="fw-bold text-success mb-4 border-bottom pb-2 d-flex align-items-center gap-1">
                                <i class="bi bi-mortarboard-fill"></i> المعلومات الأكاديمية والمهنية
                            </h6>
                            <div class="row g-4">
                                <div class="col-md-4 x-small">
                                    <label class="text-muted fw-bold d-block mb-1">المرحلة الدراسية</label>
                                    <div class="text-dark">{{ $request->education_level == 'student' ? 'طالب' : ($request->education_level == 'graduated' ? 'خريج' : '-') }}</div>
                                </div>
                                <div class="col-md-4 x-small">
                                    <label class="text-muted fw-bold d-block mb-1">الكلية / التخصص</label>
                                    <div class="text-dark">{{ $request->faculty ?? '-' }}</div>
                                </div>
                                <div class="col-md-4 x-small">
                                    <label class="text-muted fw-bold d-block mb-1">الجامعة</label>
                                    <div class="text-dark">{{ $request->university ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 x-small">
                                    <label class="text-muted fw-bold d-block mb-1">الوظيفة الحالية</label>
                                    <div class="text-dark">{{ $request->current_job ?? '-' }}</div>
                                </div>
                                <div class="col-md-6 x-small">
                                    <label class="text-muted fw-bold d-block mb-1">خبرة تطوعية سابقة</label>
                                    <div class="text-dark">{{ $request->previous_experience ? 'نعم لديه خبرة سابقة' : 'لا يوجد خبرة سابقة' }}</div>
                                </div>
                            </div>
                        </section>

                        <section>
                            <h6 class="fw-bold text-warning mb-4 border-bottom pb-2 d-flex align-items-center gap-1">
                                <i class="bi bi-clipboard-data-fill"></i> اهتمامات التطوع والمهارات
                            </h6>
                            <div class="p-4 bg-light rounded-4 border">
                                <div class="row g-4">
                                    <div class="col-12 x-small">
                                        <label class="text-muted fw-bold d-block mb-2 text-uppercase">مجال الرغبة الرئيسي</label>
                                        <span class="badge bg-primary rounded-pill px-4 py-2 fs-7 mb-3 shadow-sm">{{ $request->area_of_interest ?? 'عام' }}</span>
                                    </div>
                                    <div class="col-md-6 x-small">
                                        <label class="text-muted fw-bold d-block mb-1">المهارات والخبرات المتاحة</label>
                                        <div class="text-dark lh-lg">{{ $request->skills ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-6 x-small">
                                        <label class="text-muted fw-bold d-block mb-1">سبب الرغبة في التطوع / الهدف</label>
                                        <div class="text-dark lh-lg">{{ $request->goal ?? '-' }}</div>
                                    </div>
                                    <div class="col-md-12 x-small border-top pt-3">
                                        <label class="text-muted fw-bold d-block mb-1">التوقعات من التطوع</label>
                                        <p class="text-dark lh-lg mb-0">"{{ $request->expectations ?? '-' }}"</p>
                                    </div>
                                    <div class="col-md-12 x-small">
                                        <label class="text-muted fw-bold d-block mb-1">ساعات التطوع الأسبوعية المتاحة</label>
                                        <div class="fw-bold text-primary fs-6">{{ $request->volunteer_hours ?? '-' }}</div>
                                    </div>
                                    @if($request->message)
                                    <div class="col-12 x-small border-top pt-3 italic text-muted">
                                        <label class="text-muted fw-bold d-block mb-1 not-italic">ملاحظات إضافية</label>
                                        {{ $request->message }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3 gap-2">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    @if($request->cv_path && $request->cvExists())
                        <a href="{{ route('website.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm ms-auto">
                            <i class="bi bi-file-earmark-pdf me-1"></i> تحميل السيرة الذاتية (CV)
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>
    function previewHero(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('heroPrevImg');
                if (img) {
                    img.src = e.target.result;
                } else {
                    const container = input.closest('.upload-zone-mini');
                    container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-cover shadow-sm rounded-4" id="heroPrevImg">
                    <input type="file" name="volunteer_hero_image" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewHero(this)">`;
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewVolSlider(input, index) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('volSliderPrev' + index);
                const place = document.getElementById('volSliderPlace' + index);
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if (place) {
                    place.classList.add('d-none');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewLeaderAdd(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('leaderPreviewContainerAdd');
                container.innerHTML = `<img src="${e.target.result}" class="rounded-circle border border-4 border-primary-light shadow-sm" style="width: 100px; height: 100px; object-fit: cover;">`;
                container.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .volunteer-requests-mgmt-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .fs-xs { font-size: 0.75rem; }
    .bg-primary-light { background-color: rgba(34, 197, 94, 0.1); }
    .transition-all { transition: all 0.3s ease; }
    .z-10 { z-index: 10; }
    .min-h-300 { min-height: 300px; }

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

    .volunteer-card-sleek {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid var(--border) !important;
    }
    .volunteer-card-sleek:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important;
        border-color: var(--primary-light) !important;
    }
    
    .avatar-soft {
        box-shadow: inset 0 0 0 1px rgba(34, 197, 94, 0.1);
    }

    .header-icon-small {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }

    .slider-upload-mini {
        transition: all 0.3s ease;
        border: 2px dashed var(--border) !important;
    }
    .slider-upload-mini:hover {
        border-color: var(--primary) !important;
        background: var(--bg-soft) !important;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Theme-Aware Stats Styling */
    .bg-stats-card-main { background-color: #ffffff; }
    .bg-stats-inner-item { background-color: var(--gray-50); }
    .text-stats-main { color: var(--dark); }
    .text-muted-theme { color: var(--gray-500); }

    body.theme-dark .bg-stats-card-main { background-color: var(--bg-card); }
    body.theme-dark .bg-stats-inner-item { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .text-stats-main { color: #ffffff; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .avatar-soft { border-color: var(--bg-card) !important; }

    @media (min-width: 992px) {
        .col-lg-2-4 { flex: 0 0 20%; max-width: 20%; }
    }

    .hover-opacity-100:hover { opacity: 1 !important; }
</style>
@endsection
