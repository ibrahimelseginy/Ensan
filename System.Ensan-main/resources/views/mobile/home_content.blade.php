@extends('layouts.app')

@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />
    <style>
        .select2-container--bootstrap-5 .select2-selection {
            background-color: #1e293b !important;
            border: 1px solid #334155 !important;
            color: #f8fafc !important;
            border-radius: 12px !important;
            min-height: 45px;
            display: flex;
            align-items: center;
        }
        .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
            background-color: #3b82f6 !important;
            border: none !important;
            color: white !important;
            border-radius: 6px !important;
            padding: 2px 8px !important;
        }
        .select2-container--bootstrap-5 .select2-dropdown {
            background-color: #1e293b !important;
            border-color: #334155 !important;
            color: #f8fafc !important;
        }
        .select2-container--bootstrap-5 .select2-search__field {
            background-color: #0f172a !important;
            color: #f8fafc !important;
        }
        .select2-container--bootstrap-5 .select2-results__option--highlighted {
            background-color: #3b82f6 !important;
        }
    </style>
@endsection
@section('content')
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
    <div class="hero-content">
        <div class="hero-greeting text-white-50">إدارة تطبيق الموبايل API Unit 📱</div>
        <h1 class="hero-title">محتوى الصفحة الرئيسية</h1>
        <p class="hero-subtitle">تخصيص الأقسام والكروت التي تظهر في شاشة التطبيق الرئيسية</p>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="row g-4">
        {{-- 0. Integrated Services (Ensan Pillars) --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold text-white"><i class="bi bi-grid-fill me-2 text-primary"></i> المبادرات المتكاملة (Ensan Pillars)</h5>
                    <button class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addPillarModal">إضافة مبادرة جديدة <i class="bi bi-plus-lg"></i></button>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @forelse($pillars as $pillar)
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3 border h-100 d-flex flex-column shadow-sm">
                                    <div class="d-flex align-items-start gap-3 mb-3">
                                        <div class="rounded-circle border bg-white p-2 d-flex align-items-center justify-content-center" style="width: 55px; height: 55px;">
                                            @if($pillar->icon_path)
                                                <img src="{{ $pillar->icon_url }}" class="w-100 h-100 object-fit-contain">
                                            @else
                                                <i class="bi bi-grid text-muted"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="fw-bold mb-1 text-white">{{ $pillar->title }}</h6>
                                            <span class="badge bg-primary bg-opacity-10 text-primary x-small">/{{ $pillar->slug }}</span>
                                        </div>
                                        @if(!$pillar->is_active)
                                            <span class="badge bg-danger bg-opacity-10 text-danger x-small border border-danger border-opacity-25">غير نشط</span>
                                        @endif
                                    </div>
                                    <p class="text-muted small mb-1" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $pillar->description }}</p>
                                    <div class="d-flex gap-2 small mb-3">
                                        <span class="text-info"><i class="bi bi-folder2-open me-1"></i> {{ $pillar->projects->count() }} مشاريع</span>
                                        <span class="text-success"><i class="bi bi-gear me-1"></i> {{ $pillar->services->count() }} خدمات</span>
                                    </div>
                                    <div class="d-flex gap-2 justify-content-end mt-auto pt-2 border-top border-secondary border-opacity-25">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editPillarModal{{ $pillar->id }}">تعديل <i class="bi bi-pencil ms-1"></i></button>
                                        <form action="{{ route('mobile.pillars.destroy', $pillar) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه المبادرة؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5 text-muted">
                                <i class="bi bi-grid-3x3-gap display-4 opacity-25 d-block mb-3"></i>
                                لا يوجد مبادرات حالياً
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 6. Final Section --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-flag-fill me-2 text-danger"></i> القسم الأخير (Final Section)</h5>
                </div>
                <div class="p-4">
                    @if($finalSection)
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border">
                            <div class="rounded border bg-white overflow-hidden" style="width: 60px; height: 60px;">
                                @if($finalSection->image_path)
                                    <img src="{{ $finalSection->image_url }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold">{{ $finalSection->title }}</h6>
                                <p class="small text-muted mb-0 text-truncate" style="max-width: 200px;">{{ $finalSection->description }}</p>
                            </div>
                            <button class="btn btn-sm btn-outline-primary ms-auto rounded-pill" data-bs-toggle="modal" data-bs-target="#editFinalModal">تعديل</button>
                        </div>
                    @else
                        <div class="text-center py-3">
                            <p class="text-muted mb-3">لم يتم إعداد القسم الأخير بعد</p>
                            <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addFinalModal">إضافة القسم الأخير</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. Gallery Section --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-images me-2 text-info"></i> صور المعرض (Gallery)</h5>
                    <button class="btn btn-sm btn-info text-white rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addGalleryModal">إضافة صورة <i class="bi bi-plus"></i></button>
                </div>
                <div class="p-4">
                    <div class="row g-3">
                        @forelse($gallery as $img)
                            <div class="col-md-3 col-6">
                                <div class="position-relative rounded-3 overflow-hidden border group" style="height: 150px;">
                                    <img src="{{ $img->image_url }}" class="w-100 h-100 object-fit-cover">
                                    <div class="position-absolute top-0 end-0 p-2">
                                        <form action="{{ route('mobile.home_content.destroy', $img) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger rounded-circle shadow-sm"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-4 text-muted">لا يوجد صور حالياً</div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. Our Services Section --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-gear-fill me-2 text-success"></i> قسم خدماتنا</h5>
                    <button class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addServiceModal">إضافة خدمة <i class="bi bi-plus"></i></button>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>الصورة</th>
                                    <th>الخدمة</th>
                                    <th>سعر السهم</th>
                                    <th class="text-center">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($services as $item)
                                    <tr>
                                        <td>
                                            <div class="rounded border bg-light overflow-hidden" style="width: 50px; height: 50px;">
                                                @if($item->image_path)
                                                    <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <div class="fw-bold text-white">{{ $item->title }}</div>
                                            <small class="text-muted text-truncate d-block" style="max-width: 300px;">{{ $item->description }}</small>
                                        </td>
                                        <td class="text-white">{{ number_format($item->share_price) }} ج.م</td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}"><i class="bi bi-pencil"></i></button>
                                                <form action="{{ route('mobile.home_content.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. Share Section --}}
        <div class="col-lg-6">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-heart-fill me-2 text-danger"></i> شارك بما لا تحتاجه</h5>
                    <button class="btn btn-sm btn-outline-danger rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addShareModal">إضافة <i class="bi bi-plus"></i></button>
                </div>
                <div class="p-4">
                    @forelse($shareItems as $item)
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-2">
                             <div class="rounded border bg-white overflow-hidden" style="width: 50px; height: 50px;">
                                @if($item->image_path)
                                    <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2">
                                    <span class="badge bg-secondary rounded-pill">#{{ $item->sort_order }}</span>
                                    <h6 class="mb-0 fw-bold text-white">{{ $item->title }}</h6>
                                    @if($item->share_price)
                                        <span class="badge bg-success ms-2">{{ number_format($item->share_price) }} ج.م</span>
                                    @endif
                                </div>
                                <small class="text-muted text-truncate d-block" style="max-width: 150px;">{{ $item->description }}</small>
                            </div>
                                <button class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editShareModal{{ $item->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('mobile.home_content.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">لا يوجد عناصر</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 5. Seasonal Campaigns Section --}}
        <div class="col-lg-6">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-event-fill me-2 text-warning"></i> حملات موسمية</h5>
                    <button class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addCampaignModal">إضافة <i class="bi bi-plus"></i></button>
                </div>
                <div class="p-4">
                    @forelse($campaigns as $item)
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-2">
                             <div class="rounded border bg-white overflow-hidden" style="width: 50px; height: 50px;">
                                @if($item->image_path)
                                    <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold text-white">{{ $item->title }}</h6>
                                @if($item->share_price)
                                    <span class="badge bg-success ms-2">{{ number_format($item->share_price) }} ج.م</span>
                                @endif
                                <small class="text-muted text-truncate d-block" style="max-width: 150px;">{{ $item->details }}</small>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editCampModal{{ $item->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('mobile.home_content.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-3">لا يوجد حملات حالياً</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 7. About Us Section --}}
        <div class="col-lg-12" id="about_us_section">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-info-circle-fill me-2 text-primary"></i> معلومات عنا (About Us)</h5>
                </div>
                <div class="p-4">
                    @if($aboutUs)
                        <div class="d-flex align-items-center gap-4 p-3 bg-light rounded-3 border">
                            <div class="rounded border bg-white overflow-hidden shadow-sm" style="width: 120px; height: 120px;">
                                @if($aboutUs->image_path)
                                    <img src="{{ $aboutUs->image_url }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image fs-1"></i></div>
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-1 fw-bold fs-5 text-white">صورة "معلومات عنا" المعتمدة</h6>
                                <p class="text-muted mb-0">تظهر هذه الصورة في شاشة "عن المؤسسة" في التطبيق.</p>
                            </div>
                            <div class="d-flex flex-column gap-2">
                                <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editAboutModal">تعديل <i class="bi bi-pencil"></i></button>
                                <form action="{{ route('mobile.home_content.destroy', $aboutUs) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا القسم؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3 w-100">حذف <i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="bi bi-info-circle display-4 text-muted mb-3 d-block"></i>
                            <p class="text-muted mb-3">لم يتم إعداد قسم "معلومات عنا" لهذا التطبيق</p>
                            <button class="btn btn-primary rounded-pill px-5 shadow-sm" data-bs-toggle="modal" data-bs-target="#addAboutModal">إنشاء قسم معلومات عنا <i class="bi bi-plus-lg"></i></button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals --}}

{{-- Integrated Services (Ensan Pillars) --}}
@foreach($pillars as $pillar)
    <div class="modal fade" id="editPillarModal{{ $pillar->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('mobile.pillars.update', $pillar) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 overflow-hidden shadow-lg" style="border-radius: 24px;">
                @csrf @method('PUT')
                
                {{-- Premium Header with Pattern --}}
                <div class="modal-header border-0 position-relative p-4 overflow-hidden" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);">
                    <div class="position-absolute top-0 start-0 w-100 h-100 opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.4\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3Ccircle cx=\"13\" cy=\"13\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                    <div class="z-index-1 d-flex align-items-center justify-content-between w-100">
                        <div>
                            <h5 class="modal-title fw-bold text-white mb-0">
                                <i class="bi bi-pencil-square me-2 text-primary"></i> تعديل مبادرة: {{ $pillar->title }}
                            </h5>
                            <p class="text-white-50 small mb-0 mt-1">تحديث بيانات وأيقونات المبادرة الأساسية</p>
                        </div>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                </div>

                {{-- Pillar Preview Banner (If cover exists) --}}
                @if($pillar->cover_path)
                    <div class="pillar-edit-banner position-relative" style="height: 120px;">
                        <img src="{{ $pillar->cover_url }}" class="w-100 h-100 object-fit-cover opacity-50">
                        <div class="position-absolute bottom-0 start-50 translate-middle-x bg-white p-2 rounded-circle shadow-lg border-4 border-white" style="margin-bottom: -30px;">
                            <img src="{{ $pillar->icon_url }}" style="width: 60px; height: 60px;" class="object-fit-contain p-1">
                        </div>
                    </div>
                @endif

                <div class="modal-body p-4 {{ $pillar->cover_path ? 'pt-5' : '' }}">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-type me-1"></i> اسم المبادرة</label>
                            <input type="text" name="title" class="form-control form-control-lg bg-light border-0" value="{{ $pillar->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-link-45deg me-1"></i> الرابط الفرعي (Slug)</label>
                            <input type="text" name="slug" class="form-control form-control-lg bg-light border-0" value="{{ $pillar->slug }}" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold text-muted"><i class="bi bi-text-paragraph me-1"></i> الوصف التفصيلي</label>
                            <textarea name="description" class="form-control bg-light border-0" rows="3" style="border-radius: 15px;">{{ $pillar->description }}</textarea>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-4 border bg-faded-success h-100">
                                <label class="form-label small fw-bold d-block mb-2">الأيقونة (Icon)</label>
                                <input type="file" name="icon" class="form-control form-control-sm">
                                <p class="x-small text-muted mt-2 mb-0">يفضل استخدام أيقونة خضراء بخلفية شفافة</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="p-3 rounded-4 border bg-faded-primary h-100">
                                <label class="form-label small fw-bold d-block mb-2">صورة الغلاف (Cover)</label>
                                <input type="file" name="cover" class="form-control form-control-sm">
                                <p class="x-small text-muted mt-2 mb-0">الأبعاد الموصى بها: 1200×600 بكسل</p>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold text-muted">الترتيب</label>
                            <input type="number" name="sort_order" class="form-control border-0 bg-light" value="{{ $pillar->sort_order }}">
                        </div>


                        <div class="col-md-12">
                            <hr class="border-secondary opacity-25">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <label class="form-label small fw-bold text-muted mb-0"><i class="bi bi-wallet2 me-1"></i> كروت التبرع الخاصة بالمبادرة</label>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill" onclick="addPillarCard('editCardsContainer{{ $pillar->id }}')"><i class="bi bi-plus"></i> إضافة كارت تبرع</button>
                            </div>
                            <div id="editCardsContainer{{ $pillar->id }}">
                                @foreach($pillar->cards as $index => $card)
                                    <div class="card bg-dark bg-opacity-25 border-secondary mb-3 shadow-sm card-row">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between mb-2">
                                                <small class="fw-bold text-muted card-number">كارد #{{ $index + 1 }}</small>
                                                <button type="button" class="btn btn-sm btn-outline-danger py-0 border-0" onclick="removePillarCard(this, 'editCardsContainer{{ $pillar->id }}')"><i class="bi bi-x-circle"></i></button>
                                            </div>
                                            <input type="hidden" name="cards[{{ $index }}][id]" value="{{ $card->id }}">
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <input type="text" name="cards[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $card->title }}" placeholder="الاسم (مثال: حملة رمضان)" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <input type="number" step="0.01" name="cards[{{ $index }}][price]" class="form-control form-control-sm" value="{{ $card->price }}" placeholder="سعر التبرع (مثال: 200)" required>
                                                </div>
                                                <div class="col-md-12">
                                                    <input type="text" name="cards[{{ $index }}][description]" class="form-control form-control-sm" value="{{ $card->description }}" placeholder="الوصف (مثال: الحملة جاهزة للبدء)">
                                                </div>
                                                <div class="col-md-12 d-flex align-items-center gap-2">
                                                    @if($card->image_path)
                                                        <img src="{{ $card->image_url }}" alt="card image" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                    <div class="flex-grow-1">
                                                        <label class="x-small text-muted mb-1 d-block">تغيير الصورة (اختياري)</label>
                                                        <input type="file" name="cards[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="col-md-8 d-flex align-items-end">
                            <div class="form-check form-switch p-3 bg-success bg-opacity-10 rounded-pill border border-success border-opacity-10 w-100 d-flex align-items-center justify-content-between px-4">
                                <div>
                                    <label class="form-check-label fw-bold text-success mb-0" for="activePillar{{ $pillar->id }}">تفعيل المبادرة</label>
                                    <div class="x-small text-success text-opacity-75">هل تظهر في الشاشة الرئيسية للتطبيق؟</div>
                                </div>
                                <input class="form-check-input ms-0" style="width: 3em; height: 1.5em;" type="checkbox" name="is_active" id="activePillar{{ $pillar->id }}" value="1" {{ $pillar->is_active ? 'checked' : '' }}>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-lg">حفظ التعديلات <i class="bi bi-check-circle ms-2"></i></button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<div class="modal fade" id="addPillarModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('mobile.pillars.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-plus-square me-2"></i> إضافة مبادرة جديدة</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">اسم المبادرة</label>
                        <input type="text" name="title" class="form-control" placeholder="مثلاً: سُقاء الأمل" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">الرابط الفرعي (Slug)</label>
                        <input type="text" name="slug" class="form-control" placeholder="مثلاً: soqia-hope" required>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">الوصف التفصيلي (يظهر في صفحة التفاصيل)</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="تحدث عن المبادرة وأهدافها ورسالتها..."></textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">الأيقونة (Icon - يفضل ستايل أخضر)</label>
                        <input type="file" name="icon" class="form-control shadow-sm" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">صورة الغلاف (Cover - جودة عالية)</label>
                        <input type="file" name="cover" class="form-control shadow-sm">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>

                    <div class="col-md-12">
                        <hr class="border-secondary opacity-25">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label small fw-bold text-muted mb-0"><i class="bi bi-wallet2 me-1"></i> كروت التبرع الخاصة بالمبادرة</label>
                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill" onclick="addPillarCard('addCardsContainer')"><i class="bi bi-plus"></i> إضافة كارت تبرع</button>
                        </div>
                        <div id="addCardsContainer"></div>
                    </div>

                    <div class="col-md-6 d-flex align-items-end pb-2">
                        <div class="form-check form-switch p-3 bg-dark bg-opacity-25 rounded-3 border border-secondary w-100">
                            <input class="form-check-input" type="checkbox" name="is_active" id="newPillarActive" value="1" checked>
                            <label class="form-check-label fw-bold ms-2" for="newPillarActive">تفعيل المبادرة فوراً</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-sm">إنشاء المبادرة الآن <i class="bi bi-save ms-2"></i></button>
            </div>
        </form>
    </div>
</div>

{{-- Services --}}
@foreach($services as $item)
    <div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog">
            <form action="{{ route('mobile.home_content.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">تعديل الخدمة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الوصف</label>
                        <textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">سعر سهم التبرع</label>
                        <input type="number" name="share_price" class="form-control" value="{{ $item->share_price }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100 py-3 fw-bold">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Share Items --}}
@foreach($shareItems as $item)
    <div class="modal fade" id="editShareModal{{ $item->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog">
            <form action="{{ route('mobile.home_content.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">تعديل العنصر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3"><label class="form-label fw-bold">الاسم</label><input type="text" name="title" class="form-control" value="{{ $item->title }}" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">الوصف</label><textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea></div>
                    <div class="mb-3"><label class="form-label fw-bold">سعر السهم</label><input type="number" name="share_price" class="form-control" value="{{ $item->share_price }}"></div>
                    <div class="mb-3"><label class="form-label fw-bold">الترتيب</label><input type="number" name="sort_order" class="form-control" value="{{ $item->sort_order }}"></div>
                    <div class="mb-3"><label class="form-label fw-bold">تغيير الصورة</label><input type="file" name="image" class="form-control"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 w-100 py-3 fw-bold">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Campaigns --}}
@foreach($campaigns as $item)
    <div class="modal fade" id="editCampModal{{ $item->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog">
            <form action="{{ route('mobile.home_content.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">تعديل الحملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3"><label class="form-label fw-bold">اسم الحملة</label><input type="text" name="title" class="form-control" value="{{ $item->title }}" required></div>
                    <div class="mb-3"><label class="form-label fw-bold">تفاصيل الحملة</label><textarea name="details" class="form-control" rows="2">{{ $item->details }}</textarea></div>
                    <div class="mb-3"><label class="form-label fw-bold">سعر السهم</label><input type="number" name="share_price" class="form-control" value="{{ $item->share_price }}"></div>
                    <div class="mb-3"><label class="form-label fw-bold">تغيير الصورة</label><input type="file" name="image" class="form-control"></div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 w-100 py-3">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Final Section --}}
<div class="modal fade" id="{{ $finalSection ? 'editFinalModal' : 'addFinalModal' }}" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ $finalSection ? route('mobile.home_content.update', $finalSection) : route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            @if($finalSection) @method('PUT') @endif
            <input type="hidden" name="type" value="final">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">القسم الأخير</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label fw-bold">العنوان</label><input type="text" name="title" class="form-control" value="{{ $finalSection ? $finalSection->title : '' }}" required></div>
                <div class="mb-3"><label class="form-label fw-bold">الوصف</label><textarea name="description" class="form-control" rows="3" required>{{ $finalSection ? $finalSection->description : '' }}</textarea></div>
                <div class="mb-3"><label class="form-label fw-bold">الصورة</label><input type="file" name="image" class="form-control"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold">حفظ</button>
            </div>
        </form>
    </div>
</div>

{{-- Gallery --}}
<div class="modal fade" id="addGalleryModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <input type="hidden" name="type" value="gallery">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">إضافة صورة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label fw-bold">الصورة</label><input type="file" name="image" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-bold">الترتيب</label><input type="number" name="sort_order" class="form-control" value="0"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-info text-white fw-bold rounded-pill w-100 py-3">رفع</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addServiceModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <input type="hidden" name="type" value="service">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">إضافة خدمة جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label fw-bold">الاسم</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-bold">الوصف</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="mb-3"><label class="form-label fw-bold">سعر سهم التبرع</label><input type="number" name="share_price" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">الصورة</label><input type="file" name="image" class="form-control" required></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-success fw-bold rounded-pill w-100 py-3">حفظ</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addShareModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <input type="hidden" name="type" value="share">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">إضافة عنصر جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label fw-bold">الاسم</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-bold">الوصف</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                <div class="mb-3"><label class="form-label fw-bold">سعر السهم</label><input type="number" name="share_price" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">الصورة</label><input type="file" name="image" class="form-control" required></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-danger fw-bold rounded-pill w-100 py-3">حفظ</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="addCampaignModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <input type="hidden" name="type" value="campaign">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">إضافة حملة موسمية</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label fw-bold">الاسم</label><input type="text" name="title" class="form-control" required></div>
                <div class="mb-3"><label class="form-label fw-bold">التفاصيل</label><textarea name="details" class="form-control" rows="2"></textarea></div>
                <div class="mb-3"><label class="form-label fw-bold">سعر السهم</label><input type="number" name="share_price" class="form-control"></div>
                <div class="mb-3"><label class="form-label fw-bold">الصورة</label><input type="file" name="image" class="form-control" required></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill w-100 py-3">حفظ</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="{{ $aboutUs ? 'editAboutModal' : 'addAboutModal' }}" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ $aboutUs ? route('mobile.home_content.update', $aboutUs) : route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            @if($aboutUs) @method('PUT') @endif
            <input type="hidden" name="type" value="about_us">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">إدارة قسم "معلومات عنا"</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">الصورة الرئيسية</label>
                    <input type="file" name="image" class="form-control" accept="image/*" required>
                    <div class="form-text small text-muted">يرجى اختيار الصورة التي تريد عرضها في قسم "معلومات عنا" بالكامل.</div>
                </div>
                @if($aboutUs && $aboutUs->image_path)
                    <div class="mt-2 text-center">
                        <img src="{{ $aboutUs->image_url }}" class="rounded shadow-sm" style="max-height: 100px;">
                    </div>
                @endif
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary rounded-pill w-100 shadow-sm py-3 fw-bold">{{ $aboutUs ? 'حفظ التغييرات' : 'إنشاء القسم' }}</button>
            </div>
        </form>
    </div>
</div>

<style>
    .glass-card { 
        background: #0f172a !important; 
        border: 1px solid #1e293b !important; 
        border-radius: 20px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.4); 
    }
    .modal-content { 
        background-color: #0f172a !important; 
        border: 2px solid #3b82f6 !important; 
        border-radius: 20px !important; 
        color: #ffffff !important;
        box-shadow: 0 0 60px rgba(59, 130, 246, 0.4) !important;
        overflow: hidden;
        opacity: 1 !important;
    }
    .modal-header {
        background: #1e293b !important;
        border-bottom: 1px solid #334155 !important;
        color: #ffffff !important;
        padding: 1.5rem !important;
    }
    .modal-footer {
        background: #1e293b !important;
        border-top: 1px solid #334155 !important;
        padding: 1.2rem !important;
    }
    .modal-body {
        background-color: #0f172a !important;
        color: #f8fafc !important;
        opacity: 1 !important;
    }
    .form-control, .form-select {
        background-color: #1e293b !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
        padding: 0.75rem !important;
        border-radius: 12px !important;
    }
    .form-control:focus {
        background-color: #0f172a !important;
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2) !important;
        color: #ffffff !important;
    }
    .modal-backdrop.show { 
        backdrop-filter: blur(10px) !important; 
        -webkit-backdrop-filter: blur(10px) !important; 
        opacity: 0.9 !important; 
        background-color: #000000 !important;
    }
    .animate-slide-up { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2').select2({
            theme: 'bootstrap-5',
            width: '100%',
            dropdownParent: $('.modal:visible').length ? $('.modal:visible').first() : $('body'),
            dir: 'rtl'
        });

        $('.modal').on('shown.bs.modal', function () {
            $(this).find('.select2').select2({
                theme: 'bootstrap-5',
                width: '100%',
                dropdownParent: $(this),
                dir: 'rtl'
            });
        });
    });

    function addPillarCard(containerId) {
        let container = document.getElementById(containerId);
        let uniqueIndex = Date.now(); // Use timestamp for absolute uniqueness
        let html = `
            <div class="card bg-dark bg-opacity-25 border-secondary mb-3 shadow-sm card-row animate-slide-up">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <small class="fw-bold text-muted card-number">كارد #</small>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 border-0" onclick="removePillarCard(this, '${containerId}')"><i class="bi bi-x-circle"></i></button>
                    </div>
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input type="text" name="cards[${uniqueIndex}][title]" class="form-control form-control-sm" placeholder="الاسم (مثال: حملة رمضان)" required>
                        </div>
                        <div class="col-md-6">
                            <input type="number" step="0.01" name="cards[${uniqueIndex}][price]" class="form-control form-control-sm" placeholder="سعر التبرع (مثال: 200)" required>
                        </div>
                        <div class="col-md-12">
                            <input type="text" name="cards[${uniqueIndex}][description]" class="form-control form-control-sm" placeholder="الوصف (مثال: الحملة جاهزة للبدء)">
                        </div>
                            <label class="x-small text-muted mb-1 d-block">الصورة</label>
                            <input type="file" name="cards[${uniqueIndex}][image]" class="form-control form-control-sm" accept="image/*">
                        </div>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
        renumberPillarCards(containerId);
    }

    function removePillarCard(btn, containerId) {
        btn.closest('.card-row').remove();
        if (containerId) {
            renumberPillarCards(containerId);
        }
    }

    function renumberPillarCards(containerId) {
        let container = document.getElementById(containerId);
        if (!container) return;
        let rows = container.querySelectorAll('.card-row');
        rows.forEach((row, idx) => {
            let label = row.querySelector('.card-number');
            if (label) {
                label.innerText = `كارد #${idx + 1}`;
            }
        });
    }
</script>
@endsection
