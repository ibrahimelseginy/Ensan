@extends('layouts.app')

@section('content')
<div class="honor-wall-mgmt-page">
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
                    <li class="breadcrumb-item active" aria-current="page">جدار الشرف</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-patch-check-fill me-2"></i> جدار الشرف والامتنان 💎
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">شركاء النجاح وقادة العطاء</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                توثيق بصمات الخير لشركاء النجاح والمتميزين في العمل التطوعي الذين سهموا في مسيرة إنسان.
            </p>
            <div class="mt-4 d-flex justify-content-center gap-2 flex-wrap">
                <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addPartner">
                    <i class="bi bi-building-add me-2"></i> إضافة شريك نجاح
                </button>
                <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addLeader">
                    <i class="bi bi-person-heart me-2"></i> إضافة بطل تطوع
                </button>
                <button class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#manageSlider">
                    <i class="bi bi-images me-2"></i> السلايدر
                </button>
                <button class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold shadow-sm border-0" data-bs-toggle="modal" data-bs-target="#manageStats">
                    <i class="bi bi-graph-up-arrow me-2"></i> الإحصائيات
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        {{-- Navigation Tabs --}}
        <div class="d-flex justify-content-center mb-5">
            <div class="tab-modern-pill p-1 bg-light border rounded-pill shadow-sm">
                <ul class="nav nav-pills" id="honorTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4 py-2 fw-bold x-small" id="partners-tab" data-bs-toggle="pill" data-bs-target="#partners-content" type="button">
                            <i class="bi bi-buildings me-1"></i> شركاء النجاح
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4 py-2 fw-bold x-small" id="leaders-tab" data-bs-toggle="pill" data-bs-target="#leaders-content" type="button">
                            <i class="bi bi-stars me-1"></i> قادة العطاء
                        </button>
                    </li>
                </ul>
            </div>
        </div>

        <div class="tab-content" id="honorTabContent">
            {{-- Partners Tab --}}
            <div class="tab-pane fade show active" id="partners-content" role="tabpanel">
                @if(count($partners) > 0)
                    <div class="row g-4">
                        @foreach($partners as $partner)
                        <div class="col-sm-6 col-md-4 col-lg-3 col-xl-2">
                            <div class="card h-100 border-0 shadow-sm partner-card-sleek rounded-4 animate-slide-up overflow-hidden" style="animation-delay: {{ $loop->index * 0.05 }}s">
                                @php
                                    $tiersArr = [
                                        'platinum' => ['label' => 'بلاتيني', 'color' => 'secondary', 'icon' => 'bi-award-fill'],
                                        'gold' => ['label' => 'ذهبي', 'color' => 'warning', 'icon' => 'bi-star-fill'],
                                        'silver' => ['label' => 'فضي', 'color' => 'info', 'icon' => 'bi-shield-fill'],
                                        'bronze' => ['label' => 'برونزي', 'color' => 'primary', 'icon' => 'bi-patch-check-fill'],
                                        'corporate' => ['label' => 'شريك مؤسسي', 'color' => 'dark', 'icon' => 'bi-building-fill'],
                                    ];
                                    $tier = $tiersArr[$partner->type] ?? $tiersArr['bronze'];
                                @endphp
                                
                                <div class="position-absolute top-0 start-0 m-2 z-10">
                                    <span class="badge bg-{{ $tier['color'] }} bg-opacity-10 text-{{ $tier['color'] }} rounded-pill px-2 py-1 x-small border border-{{ $tier['color'] }} border-opacity-10 fw-800">
                                        <i class="bi {{ $tier['icon'] }} me-1"></i> {{ $tier['label'] }}
                                    </span>
                                </div>

                                <div class="p-4 pt-5 pb-0 text-center">
                                    <div class="ratio ratio-1x1 bg-light rounded-4 p-3 border group-hover-zoom overflow-hidden shadow-inner">
                                        @if($partner->logo_path)
                                            <img src="{{ $partner->image_url }}" class="w-100 h-100 object-fit-contain transition-all" alt="{{ $partner->name }}">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center text-muted opacity-25">
                                                <i class="bi bi-building fs-1"></i>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="card-body p-3 text-center">
                                    <h6 class="fw-bold text-dark text-truncate mb-3 small" title="{{ $partner->name }}">{{ $partner->name }}</h6>
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-outline-light text-primary border rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPartner{{ $partner->id }}">
                                            <i class="bi bi-pencil-square"></i>
                                        </button>
                                        <form action="{{ route('website.partners.destroy', $partner) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف الشريك نهائياً؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-light text-danger border rounded-pill px-2">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 opacity-25">
                        <i class="bi bi-buildings fs-1 d-block mb-3"></i>
                        <h6 class="fw-bold">لا يوجد شركاء في القائمة</h6>
                        <button class="btn btn-sm btn-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#addPartner">إضافة أول شريك</button>
                    </div>
                @endif
            </div>

            {{-- Leaders Tab --}}
            <div class="tab-pane fade" id="leaders-content" role="tabpanel">
                @if(count($leaders) > 0)
                    <div class="row g-4">
                        @foreach($leaders as $leader)
                        <div class="col-md-6 col-lg-4 col-xl-3">
                            <div class="card border-0 shadow-sm leader-card-sleek rounded-4 animate-slide-up overflow-hidden" style="animation-delay: {{ $loop->index * 0.1 }}s">
                                <div class="card-body p-4 text-center">
                                    <div class="position-relative d-inline-block mb-3">
                                        <div class="avatar-ring-premium-light"></div>
                                        @if($leader->image_path)
                                            <img src="{{ $leader->image_url }}" class="rounded-circle shadow border border-4 border-white object-fit-cover" style="width: 100px; height: 100px;">
                                        @else
                                            <div class="rounded-circle shadow border border-4 border-white bg-light d-flex align-items-center justify-content-center text-primary" style="width: 100px; height: 100px;">
                                                <i class="bi bi-person fs-1"></i>
                                            </div>
                                        @endif
                                        <div class="position-absolute top-0 end-0 bg-primary text-white rounded-circle shadow-sm d-flex align-items-center justify-content-center fw-bold small" style="width: 32px; height: 32px; border: 3px solid white;">
                                            @if($leader->rank <= 3) <i class="bi bi-trophy-fill small"></i> @else #{{ $leader->rank }} @endif
                                        </div>
                                    </div>
                                    
                                    <h6 class="fw-bold text-dark mb-1">{{ $leader->name }}</h6>
                                    <div class="badge bg-primary-light text-primary rounded-pill px-3 py-1 x-small fw-bold mb-4">
                                        <i class="bi bi-lightning-charge-fill me-1"></i> {{ $leader->hours }} ساعة تطوعية
                                    </div>
                                    
                                    <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                                        <button class="btn btn-sm btn-outline-light text-primary border rounded-pill px-4 fw-bold x-small" data-bs-toggle="modal" data-bs-target="#editLeader{{ $leader->id }}">
                                            <i class="bi bi-pencil-square me-1"></i> تعديل
                                        </button>
                                        <form action="{{ route('website.volunteer-wall.destroy', $leader) }}" method="POST" class="d-inline" onsubmit="return confirm('إزالة البطل من جدار العطاء؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-light text-danger border rounded-pill px-4 fw-bold x-small">
                                                إزالة <i class="bi bi-trash ms-1"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5 opacity-25">
                        <i class="bi bi-person-heart fs-1 d-block mb-3 text-danger"></i>
                        <h6 class="fw-bold">لا يوجد أبطال تطوع حالياً</h6>
                        <button class="btn btn-sm btn-primary rounded-pill mt-2" data-bs-toggle="modal" data-bs-target="#addLeader">تكريم أول بطل</button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- Add Partner Modal --}}
    <div class="modal fade" id="addPartner" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('website.partners.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white text-start">
                @csrf
                <div class="modal-header border-bottom bg-primary text-white px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-building-add me-2"></i> توثيق شريك نجاح جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">اسم الجهة / المؤسسة</label>
                                <input type="text" name="name" class="form-control" required placeholder="مثلاً: شركة كبرى، بنك، مؤسسة...">
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">تصنيف الشراكة</label>
                                    <select name="type" class="form-select">
                                        <option value="platinum">بلاتيني</option>
                                        <option value="gold" selected>ذهبي</option>
                                        <option value="silver">فضي</option>
                                        <option value="bronze">برونزي</option>
                                        <option value="corporate">مؤسسي</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">رابط الموقع (اختياري)</label>
                                    <input type="url" name="website_url" class="form-control" placeholder="https://...">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">نبذة عن الشراكة</label>
                                <textarea name="description" class="form-control" rows="4" placeholder="تفاصيل التعاون بين المؤسسة والجهة..."></textarea>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted d-block text-center">شعار الشريك</label>
                            <div class="p-3 bg-light rounded-4 border upload-zone-mini h-100 d-flex flex-column align-items-center justify-content-center text-center cursor-pointer position-relative">
                                <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50 mb-2"></i>
                                <p class="x-small text-muted mb-0">اسحب الشعار هنا أو انقر للاختيار</p>
                                <input type="file" name="logo" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer" onchange="previewLogoAdd(this)">
                                <div id="addLogoPreviewContainer" class="position-absolute inset-0 p-2 bg-light d-none rounded-4"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">تثبيت الشريك</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Add Leader Modal --}}
    <div class="modal fade" id="addLeader" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('website.volunteer-wall.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white text-start">
                @csrf
                <div class="modal-header border-bottom bg-primary text-white px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-star-fill me-2"></i> تكريم بطل تطوع جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-muted">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" required placeholder="اسم المتطوع المتميز...">
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">الترتيب / المركز</label>
                            <input type="number" name="rank" class="form-control" required placeholder="1">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">إجمالي الساعات</label>
                            <input type="text" name="hours" class="form-control" required placeholder="مثلاً: 150">
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">الصورة الشخصية</label>
                        <input type="file" name="image" class="form-control" onchange="previewLeaderAdd(this)">
                        <div id="leaderPreviewContainerAdd" class="mt-3 text-center d-none"></div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">حفظ في الجدار</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Manage Slider Modal --}}
    <div class="modal fade" id="manageSlider" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white text-start">
                @csrf
                <div class="modal-header border-bottom bg-info text-white px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-images me-2"></i> إدارة صور سلايدر جدار الشرف</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="col-md-4 col-lg-2-4 col-6">
                            <div class="position-relative rounded-4 border overflow-hidden bg-light slider-upload-mini ratio ratio-4x3 group-hover-overlay" style="cursor: pointer;">
                                @php $sliderPath = $settings["honor_wall_slider_$i"] ?? null; @endphp
                                @if($sliderPath)
                                    <img src="{{ asset('storage/' . $sliderPath) }}" class="w-100 h-100 object-fit-cover shadow-sm transition-all" id="honorSliderPrev{{ $i }}">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="honorSliderPlace{{ $i }}">
                                        <i class="bi bi-cloud-arrow-up fs-2 mb-1"></i>
                                        <span class="x-small fw-bold">{{ $i }}</span>
                                    </div>
                                    <img src="" class="w-100 h-100 object-fit-cover d-none shadow-sm transition-all" id="honorSliderPrev{{ $i }}">
                                @endif
                                
                                <input type="file" name="honor_wall_slider_{{ $i }}" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewHonorSlider(this, {{ $i }})">
                                
                                @if($sliderPath)
                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center z-10" 
                                        style="width: 26px; height: 26px;"
                                        onclick="event.stopPropagation(); document.getElementById('delete_honor_wall_slider_{{ $i }}').value='1'; this.closest('.slider-upload-mini').querySelector('.d-none').classList.remove('d-none'); document.getElementById('honorSliderPrev{{ $i }}').classList.add('d-none'); this.remove();">
                                    <i class="bi bi-trash fs-xs"></i>
                                </button>
                                @endif
                                <input type="hidden" name="delete_honor_wall_slider_{{ $i }}" id="delete_honor_wall_slider_{{ $i }}" value="0">
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-info rounded-pill px-5 fw-bold shadow-sm text-dark">حفظ السلايدر</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Manage Stats Modal --}}
    <div class="modal fade" id="manageStats" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('website.settings.update') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white text-start">
                @csrf
                <div class="modal-header border-bottom bg-primary text-white px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-graph-up-arrow me-2"></i> إحصائيات النجاح في جدار الشرف</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-4">
                        @php
                            $statKeys = [
                                ['id' => 'donors', 'label' => 'المتبرعون المتميزون', 'color' => 'primary'],
                                ['id' => 'volunteers', 'label' => 'المتطوعون النشطون', 'color' => 'success'],
                                ['id' => 'institutions', 'label' => 'المؤسسات الشريكة', 'color' => 'warning'],
                                ['id' => 'campaigns', 'label' => 'المبادرات المنفذة', 'color' => 'info'],
                            ];
                        @endphp
                        @foreach($statKeys as $stat)
                        <div class="col-md-6">
                            <div class="p-4 rounded-4 border bg-light h-100">
                                <label class="form-label x-small fw-bold text-muted text-uppercase mb-3 d-block">{{ $stat['label'] }}</label>
                                <div class="row g-2">
                                    <div class="col-8">
                                        <input type="text" name="partners_stats_{{ $stat['id'] }}_label" class="form-control" value="{{ $settings['partners_stats_'.$stat['id'].'_label'] ?? '' }}" placeholder="تسمية الإحصائية">
                                    </div>
                                    <div class="col-4">
                                        <input type="text" name="partners_stats_{{ $stat['id'] }}" class="form-control text-center fw-bold text-{{ $stat['color'] }}" value="{{ $settings['partners_stats_'.$stat['id']] ?? '' }}" placeholder="الرقم">
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">حفظ الإحصائيات</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Partner Modals --}}
    @foreach($partners as $partner)
    <div class="modal fade" id="editPartner{{ $partner->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('website.partners.update', $partner) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white text-start">
                @csrf @method('PUT')
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> تعديل بيانات الشريك</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 p-md-5">
                    <div class="row g-4">
                        <div class="col-md-7">
                            <div class="mb-4">
                                <label class="form-label small fw-bold text-muted">اسم الجهة / المؤسسة</label>
                                <input type="text" name="name" class="form-control" required value="{{ $partner->name }}">
                            </div>
                            <div class="row g-3 mb-4">
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">تصنيف الشراكة</label>
                                    <select name="type" class="form-select">
                                        <option value="platinum" {{ $partner->type == 'platinum' ? 'selected' : '' }}>بلاتيني</option>
                                        <option value="gold" {{ $partner->type == 'gold' ? 'selected' : '' }}>ذهبي</option>
                                        <option value="silver" {{ $partner->type == 'silver' ? 'selected' : '' }}>فضي</option>
                                        <option value="bronze" {{ $partner->type == 'bronze' ? 'selected' : '' }}>برونزي</option>
                                        <option value="corporate" {{ $partner->type == 'corporate' ? 'selected' : '' }}>مؤسسي</option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label small fw-bold text-muted">رابط الموقع</label>
                                    <input type="url" name="website_url" class="form-control" value="{{ $partner->website_url }}">
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="form-label small fw-bold text-muted">نبذة عن الشراكة</label>
                                <textarea name="description" class="form-control" rows="4">{{ $partner->description }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <label class="form-label small fw-bold text-muted d-block text-center">شعار الشريك</label>
                            <div class="p-3 bg-light rounded-4 border upload-zone-mini h-100 d-flex flex-column align-items-center justify-content-center text-center cursor-pointer position-relative">
                                @if($partner->logo_path)
                                    <img src="{{ $partner->image_url }}" class="w-100 h-100 object-fit-contain shadow-sm rounded-3 p-2 bg-white" id="editLogoPreview{{ $partner->id }}">
                                @else
                                    <i class="bi bi-cloud-arrow-up fs-1 text-primary opacity-50 mb-2"></i>
                                    <p class="x-small text-muted mb-0">تغيير الشعار</p>
                                @endif
                                <input type="file" name="logo" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer" onchange="previewLogoEdit(this, {{ $partner->id }})">
                                <div id="editLogoPreviewContainer{{ $partner->id }}" class="position-absolute inset-0 p-2 bg-light d-none rounded-4"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

    {{-- Edit Leader Modals --}}
    @foreach($leaders as $leader)
    <div class="modal fade" id="editLeader{{ $leader->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('website.volunteer-wall.update', $leader) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white text-start">
                @csrf @method('PUT')
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> تعديل بيانات البطل</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">الاسم الكامل</label>
                        <input type="text" name="name" class="form-control" value="{{ $leader->name }}" required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">الترتيب</label>
                            <input type="number" name="rank" class="form-control" value="{{ $leader->rank }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-bold text-muted">الساعات</label>
                            <input type="text" name="hours" class="form-control" value="{{ $leader->hours }}" required>
                        </div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label small fw-bold text-muted">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

</div>

<script>
    function previewLogoAdd(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('addLogoPreviewContainer');
                container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-contain rounded-3 p-2 bg-white">`;
                container.classList.remove('d-none');
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    function previewLogoEdit(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('editLogoPreviewContainer' + id);
                container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-contain rounded-3 p-2 bg-white">`;
                container.classList.remove('d-none');
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

    function previewHonorSlider(input, index) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('honorSliderPrev' + index);
                const place = document.getElementById('honorSliderPlace' + index);
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
</script>

<style>
    .honor-wall-mgmt-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .fs-xs { font-size: 0.75rem; }
    .bg-primary-light { background-color: rgba(34, 197, 94, 0.1); }
    .transition-all { transition: all 0.3s ease; }
    .z-10 { z-index: 10; }

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

    .tab-modern-pill .nav-pills .nav-link { 
        color: var(--text-secondary); 
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .tab-modern-pill .nav-pills .nav-link.active { 
        background: var(--primary); 
        color: white; 
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
    }

    .partner-card-sleek {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid var(--border) !important;
    }
    .partner-card-sleek:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important;
        border-color: var(--primary-light) !important;
    }
    .group-hover-zoom:hover img { transform: scale(1.1); }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05) !important; }

    .leader-card-sleek {
        transition: all 0.4s ease;
        border: 1px solid var(--border) !important;
    }
    .leader-card-sleek:hover {
        transform: translateY(-5px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.06) !important;
        border-color: var(--primary-light) !important;
    }

    .avatar-ring-premium-light {
        position: absolute;
        inset: -10px;
        border: 2px dashed var(--primary-light);
        border-radius: 50%;
        animation: rotate 15s linear infinite;
        opacity: 0.5;
    }

    @keyframes rotate { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .slider-upload-mini {
        transition: all 0.3s ease;
        border: 2px dashed var(--border) !important;
    }
    .slider-upload-mini:hover {
        border-color: var(--primary) !important;
        background: var(--bg-soft) !important;
    }

    @media (min-width: 992px) {
        .col-lg-2-4 { flex: 0 0 20%; max-width: 20%; }
    }
</style>
@endsection
