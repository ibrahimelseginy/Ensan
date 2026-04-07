@extends('layouts.app')

@section('content')
<div class="donation-settings-mgmt">
    {{-- Header Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container-fluid hero-content-wrapper px-lg-5">
            <div class="row align-items-center">
                <div class="col-lg-7 text-end">
                    <nav aria-label="breadcrumb" class="mb-3 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active" aria-current="page">مجالات الدعم</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-800 text-dark mb-2">مجالات الدعم والعطاء</h1>
                    <p class="lead text-muted mb-0">تخصيص قطاعات التبرع وبنود الصرف المتاحة للمتبرعين عبر الموقع.</p>
                </div>
                <div class="col-lg-5 text-lg-start mt-4 mt-lg-0">
                    <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
                        <i class="bi bi-folder-plus me-2"></i> إضافة مجال دعم جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        <div class="unified-layout">
            {{-- Categories Sidebar --}}
            <div class="categories-sidebar-wrapper">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 sticky-top" style="top: 100px;">
                    <div class="p-3 border-bottom bg-light">
                        <h6 class="mb-0 fw-bold text-muted x-small text-uppercase tracking-wider">قائمة المجالات</h6>
                    </div>
                    <div class="list-group list-group-flush">
                        @foreach($categories as $cat)
                            <a href="?category={{ $cat->id }}" class="list-group-item list-group-item-action border-0 py-3 px-4 d-flex align-items-center justify-content-between transition-all {{ (request('category') == $cat->id || (!request('category') && $loop->first)) ? 'active bg-primary bg-opacity-10 text-primary fw-bold' : 'text-muted' }}">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="dot {{ (request('category') == $cat->id || (!request('category') && $loop->first)) ? 'bg-primary' : 'bg-secondary bg-opacity-25' }}"></div>
                                    <span>{{ $cat->name }}</span>
                                </div>
                                <span class="badge rounded-pill {{ (request('category') == $cat->id || (!request('category') && $loop->first)) ? 'bg-primary text-white' : 'bg-light text-muted border' }}">
                                    {{ $cat->items->count() }}
                                </span>
                            </a>
                        @endforeach
                    </div>
                    @if($categories->isEmpty())
                        <div class="p-4 text-center">
                            <p class="x-small text-muted mb-0 opacity-50">لا يوجد مجالات مضافة</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Items Content Area --}}
            <div class="items-content-area">
                @php
                    $currentCatId = request('category') ?: ($categories->first()?->id);
                    $currentCat = $categories->find($currentCatId);
                @endphp

                @if($currentCat)
                    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4 animate-slide-up">
                        <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                            <div>
                                <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 x-small fw-bold mb-2">المجال الحالي</span>
                                <h3 class="fw-800 text-dark mb-0">{{ $currentCat->name }}</h3>
                            </div>
                            <div class="d-flex gap-2">
                                <button class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold x-small" data-bs-toggle="modal" data-bs-target="#editCategoryModal{{ $currentCat->id }}">
                                    <i class="bi bi-pencil me-1"></i> تعديل المجال
                                </button>
                                <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold x-small shadow-sm" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                    <i class="bi bi-plus-lg me-1"></i> إضافة بند فرعي
                                </button>
                            </div>
                        </div>

                        <div class="card-body p-4 p-md-5">
                            @if($currentCat->items->isEmpty())
                                <div class="empty-state text-center py-5">
                                    <div class="empty-icon-circle bg-light mx-auto mb-4">
                                        <i class="bi bi-grid-3x3-gap display-4 text-muted opacity-25"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2">لا توجد بنود فرعية حالياً</h5>
                                    <p class="text-muted x-small mb-4 max-w-400 mx-auto">ابدأ بإضافة أول بند لهذا المجال ليظهر في قائمة "أوجه التبرع" بالموقع العام.</p>
                                    <button class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm x-small" data-bs-toggle="modal" data-bs-target="#addItemModal">
                                        إضافة أول بند تبرع
                                    </button>
                                </div>
                            @else
                                <div class="row g-4">
                                    @foreach($currentCat->items->sortBy('sort_order') as $item)
                                        <div class="col-12">
                                            <div class="item-card-premium p-3 p-md-4 rounded-4 border transition-all hover-shadow">
                                                <div class="row align-items-center">
                                                    <div class="col-auto">
                                                        <div class="item-icon-circle bg-primary bg-opacity-10 text-primary">
                                                            @if($item->icon)
                                                                <img src="{{ Str::startsWith($item->icon, 'http') ? $item->icon : Storage::url($item->icon) }}" class="rounded-circle" style="width: 100%; height: 100%; object-fit: cover;">
                                                            @else
                                                                <i class="bi bi-gift fs-4"></i>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="col ms-2">
                                                        <h6 class="fw-bold text-dark mb-1">{{ $item->title }}</h6>
                                                        <p class="x-small text-muted mb-0">{{ Str::limit($item->description, 120) }}</p>
                                                    </div>
                                                    <div class="col-md-auto text-end mt-3 mt-md-0">
                                                        <div class="d-flex gap-2 justify-content-end">
                                                            <button class="btn btn-icon-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editItemModal{{ $item->id }}">
                                                                <i class="bi bi-pencil"></i>
                                                            </button>
                                                            <form action="{{ route('website.donation-settings.items.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البند؟')">
                                                                @csrf @method('DELETE')
                                                                <button type="submit" class="btn btn-icon-danger rounded-pill">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-cursor-fill display-1 text-muted opacity-10 mb-4"></i>
                            <h4 class="fw-bold text-dark">يرجى اختيار مجال دعم</h4>
                            <p class="text-muted small">اختر من القائمة الجانبية لعرض وتعديل البنود الفرعية.</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Category Modals --}}
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">إضافة مجال دعم جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">اسم المجال (مثل: مشروع زاد، صدقة جارية)</label>
                        <input type="text" name="name" class="form-control" placeholder="أدخل الاسم..." required>
                    </div>
                    <div class="mb-0">
                        <label class="form-label x-small fw-bold text-muted">ترتيب الظهور</label>
                        <input type="number" name="sort_order" class="form-control" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">حفظ المجال</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($currentCat)
<div class="modal fade" id="editCategoryModal{{ $currentCat->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">تعديل مجال: {{ $currentCat->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.categories.update', $currentCat) }}" method="POST">
                @csrf @method('PUT')
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">اسم المجال</label>
                        <input type="text" name="name" class="form-control" value="{{ $currentCat->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label x-small fw-bold text-muted">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control" value="{{ $currentCat->sort_order }}">
                    </div>
                    <div class="form-check form-switch custom-switch">
                        <input class="form-check-input" type="checkbox" name="status" id="statusSwitch" value="1" {{ $currentCat->status ? 'checked' : '' }}>
                        <label class="form-check-label x-small fw-bold text-muted" for="statusSwitch">نشط ويظهر في الموقع</label>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0 d-flex justify-content-between">
                    <form action="{{ route('website.donation-settings.categories.destroy', $currentCat) }}" method="POST" onsubmit="return confirm('تحذير: سيتم حذف المجال وجميع البنود التابعة له!')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger text-decoration-none x-small p-0 fw-bold">حذف المجال نهائياً</button>
                    </form>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">تحديث</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Add Item Modal --}}
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">إضافة بند جديد لـ {{ $currentCat->name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="category_id" value="{{ $currentCat->id }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">عنوان البند (مثل: إطعام عائلة، سهم وقف)</label>
                                <input type="text" name="title" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">وصف البند وكيفية الصرف</label>
                                <textarea name="description" class="form-control" rows="4" required></textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label x-small fw-bold text-muted">التنسيق البصري</label>
                                    <select name="bg_style" class="form-select">
                                        <option value="light">كلاسيك (Light)</option>
                                        <option value="dark">مميز (Premium Black)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label x-small fw-bold text-muted">ترتيب الظهور</label>
                                    <input type="number" name="sort_order" class="form-control" value="0">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="p-4 bg-light rounded-4 text-center border h-100 d-flex flex-column justify-content-center">
                                <label class="form-label x-small fw-bold text-muted d-block mb-3">أيقونة البند</label>
                                <div class="preview-upload-box mx-auto mb-3">
                                    <i class="bi bi-cloud-arrow-up fs-2 text-primary opacity-50"></i>
                                </div>
                                <input type="file" name="icon" class="form-control form-control-sm">
                                <p class="x-small text-muted mt-2 mb-0 opacity-75">دقة مفضلة 128x128 بيكسل</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">إضافة البند</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Item Modals --}}
@foreach($currentCat->items as $item)
<div class="modal fade" id="editItemModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold">تعديل بند: {{ $item->title }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.items.update', $item) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="category_id" value="{{ $currentCat->id }}">
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-7">
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">عنوان البند</label>
                                <input type="text" name="title" class="form-control" value="{{ $item->title }}" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">الوصف</label>
                                <textarea name="description" class="form-control" rows="4" required>{{ $item->description }}</textarea>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label x-small fw-bold text-muted">التنسيق البصري</label>
                                    <select name="bg_style" class="form-select">
                                        <option value="light" {{ $item->bg_style == 'light' ? 'selected' : '' }}>كلاسيك (Light)</option>
                                        <option value="dark" {{ $item->bg_style == 'dark' ? 'selected' : '' }}>مميز (Premium Black)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label x-small fw-bold text-muted">الترتيب</label>
                                    <input type="number" name="sort_order" class="form-control" value="{{ $item->sort_order }}">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5">
                            <div class="p-4 bg-light rounded-4 text-center border h-100 d-flex flex-column justify-content-center">
                                <label class="form-label x-small fw-bold text-muted d-block mb-3">تحديث الأيقونة</label>
                                <div class="preview-upload-box mx-auto mb-3 overflow-hidden border-primary">
                                    @if($item->icon)
                                        <img src="{{ Str::startsWith($item->icon, 'http') ? $item->icon : Storage::url($item->icon) }}" style="width: 100%; height: 100%; object-fit: cover;">
                                    @else
                                        <i class="bi bi-image fs-2 text-primary opacity-50"></i>
                                    @endif
                                </div>
                                <input type="file" name="icon" class="form-control form-control-sm">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 pt-0">
                    <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">تحديث البند</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif

<style>
    .donation-settings-mgmt { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .max-w-400 { max-width: 400px; }
    .transition-all { transition: all 0.3s ease; }
    .hover-shadow:hover { box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important; transform: translateY(-3px); }

    .unified-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 2rem;
    }

    @media (max-width: 992px) {
        .unified-layout { grid-template-columns: 1fr; }
    }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 60px 0 80px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }

    .dot { width: 8px; height: 8px; border-radius: 50%; }

    .empty-icon-circle {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-icon-circle {
        width: 54px;
        height: 54px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }

    .item-card-premium {
        background: white;
        transition: all 0.3s ease;
    }

    .btn-icon-light {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #eee;
    }
    .btn-icon-light:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary-light); }
    
    .btn-icon-danger {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff5f5;
        color: #e03131;
        border: 1px solid #ffc9c9;
    }
    .btn-icon-danger:hover { background: #e03131; color: white; border-color: #e03131; }

    .preview-upload-box {
        width: 120px;
        height: 120px;
        border-radius: 20px;
        background: white;
        border: 2px dashed #eee;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .custom-switch .form-check-input { width: 3rem; height: 1.5rem; }
</style>
@endsection
