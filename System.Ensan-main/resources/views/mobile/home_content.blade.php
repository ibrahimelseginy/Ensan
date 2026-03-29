@extends('layouts.app')

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
        {{-- 1. Hero Section --}}
        <div class="col-lg-6">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-star-fill me-2 text-warning"></i> قسم الهيرو (Hero)</h5>
                    <button class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addHeroModal">إضافة هيرو <i class="bi bi-plus"></i></button>
                </div>
                <div class="p-4">
                    @forelse($heroes as $item)
                        <div class="d-flex align-items-center gap-3 p-3 bg-light rounded-3 border mb-3">
                            <div class="d-flex gap-2">
                                <div class="rounded-circle border bg-white overflow-hidden shadow-sm" style="width: 55px; height: 55px;" title="الأيقونة">
                                    @if($item->icon)
                                        <img src="{{ $item->icon_url }}" class="w-100 h-100 object-fit-contain p-2">
                                    @else
                                        <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-star"></i></div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h6 class="mb-0 fw-bold">{{ $item->title }}</h6>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-sm btn-outline-primary rounded-circle" data-bs-toggle="modal" data-bs-target="#editHeroModal{{ $item->id }}"><i class="bi bi-pencil"></i></button>
                                <form action="{{ route('mobile.home_content.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-3 text-muted">لا يوجد عناصر هيرو حالياً</div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- 6. Final Section --}}
        <div class="col-lg-6">
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

{{-- Heroes --}}
@foreach($heroes as $item)
    <div class="modal fade" id="editHeroModal{{ $item->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog">
            <form action="{{ route('mobile.home_content.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">تعديل الهيرو</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold">العنوان (اسم المشروع)</label>
                        <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الأيقونة (Icon)</label>
                        <input type="file" name="icon" class="form-control">
                        @if($item->icon)
                            <div class="mt-2 text-center">
                                <img src="{{ $item->icon_url }}" class="rounded shadow-sm p-1 border bg-white" style="max-height: 50px;">
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">صورة بداية الصفحة (اختياري)</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        @if($item->image_path)
                            <div class="mt-2 text-center">
                                <img src="{{ $item->image_url }}" class="rounded shadow-sm p-1 border bg-white" style="max-height: 50px;">
                            </div>
                        @endif
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">الوصف (اختياري)</label>
                        <textarea name="description" class="form-control" rows="2">{{ $item->description }}</textarea>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom border-secondary pb-2">
                        <h6 class="fw-bold text-warning mb-0">الكروت الداخلية (اختياري)</h6>
                        <button type="button" class="btn btn-sm btn-outline-warning" onclick="addHeroCard('editHeroCardsContainer{{ $item->id }}')">+ إضافة كارت جديد</button>
                    </div>
                    <div id="editHeroCardsContainer{{ $item->id }}">
                        @foreach($item->cards as $index => $card)
                            <div class="card bg-dark border-secondary mb-3 hero-card-item">
                                <div class="card-body p-3">
                                    <input type="hidden" name="cards[{{ $index }}][id]" value="{{ $card->id }}">
                                    <div class="d-flex justify-content-between mb-2">
                                        <h6 class="text-white">كارت #{{ $index + 1 }}</h6>
                                        <button type="button" class="btn btn-sm btn-danger py-0 px-2" onclick="this.closest('.hero-card-item').remove()">حذف</button>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold" style="font-size: 0.85rem">اسم الكارت</label>
                                        <input type="text" name="cards[{{ $index }}][title]" class="form-control form-control-sm" value="{{ $card->title }}">
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold" style="font-size: 0.85rem">الوصــف</label>
                                        <textarea name="cards[{{ $index }}][description]" class="form-control form-control-sm" rows="2">{{ $card->description }}</textarea>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label fw-bold" style="font-size: 0.85rem">الصـــورة</label>
                                        <input type="file" name="cards[{{ $index }}][image]" class="form-control form-control-sm" accept="image/*">
                                        @if($card->image_path)
                                            <div class="mt-2">
                                                <img src="{{ $card->image_url }}" class="rounded shadow-sm p-1 border bg-white" style="max-height: 40px;">
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

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

{{-- Add Modals --}}
<div class="modal fade" id="addHeroModal" tabindex="-1" style="z-index: 9999;">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0">
            @csrf
            <input type="hidden" name="type" value="hero">
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold text-white">إضافة هيرو جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3"><label class="form-label fw-bold">العنوان (اسم المشروع)</label><input type="text" name="title" class="form-control" placeholder="مثلاً: زاد الأيتام" required></div>
                <div class="mb-3"><label class="form-label fw-bold">الأيقونة (Icon)</label><input type="file" name="icon" class="form-control" accept="image/*" required></div>
                <div class="mb-3"><label class="form-label fw-bold">صورة بداية الصفحة (اختياري)</label><input type="file" name="image" class="form-control" accept="image/*"></div>
                <div class="mb-3"><label class="form-label fw-bold">الوصف (اختياري)</label><textarea name="description" class="form-control" rows="2"></textarea></div>
                
                <div class="d-flex justify-content-between align-items-center mt-4 mb-3 border-bottom border-secondary pb-2">
                    <h6 class="fw-bold text-warning mb-0">الكروت الداخلية (اختياري)</h6>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="addHeroCard('addHeroCardsContainer')">+ إضافة كارت جديد</button>
                </div>
                <div id="addHeroCardsContainer"></div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill w-100 py-3">حفظ</button>
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
<script>
    let heroCardIndex = 1000;
    function addHeroCard(containerId) {
        const container = document.getElementById(containerId);
        const currentIndex = heroCardIndex++;
        
        const html = `
            <div class="card bg-dark border-secondary mb-3 hero-card-item">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="text-white mb-0">كارت جديد</h6>
                        <button type="button" class="btn btn-sm btn-danger py-0 px-2" onclick="this.closest('.hero-card-item').remove()">حذف</button>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size: 0.85rem">اسم الكارت</label>
                        <input type="text" name="cards[${currentIndex}][title]" class="form-control form-control-sm">
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size: 0.85rem">الوصــف</label>
                        <textarea name="cards[${currentIndex}][description]" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold" style="font-size: 0.85rem">الصـــورة</label>
                        <input type="file" name="cards[${currentIndex}][image]" class="form-control form-control-sm" accept="image/*">
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', html);
    }
</script>
@endsection
