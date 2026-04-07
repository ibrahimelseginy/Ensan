@extends('layouts.app')

@section('content')
<div class="donation-items-page">
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
                    <li class="breadcrumb-item"><a href="{{ route('website.donation-settings.categories.index') }}" class="text-primary text-decoration-none">التصنيفات</a></li>
                    <li class="breadcrumb-item active" aria-current="page">عناصر التبرع</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-grid-fill me-2"></i> تفاصيل بنود العطاء 📦
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">عناصر التبرع الفرعية</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                إدارة "أوجه التبرع" التفصيلية لكل تصنيف، تخصيص الأيقونات، والتحكم في حالات الظهور.
            </p>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        @if(session('success'))
            <div class="alert alert-success border-0 shadow-sm rounded-4 mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ session('success') }}</div>
                </div>
            </div>
        @endif

        <div class="row g-4">
            {{-- Add Item Column --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="p-4 border-bottom bg-white">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i> إضافة بند فرعي جديد</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('website.donation-settings.items.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">التصنيف الرئيسي *</label>
                                <select name="category_id" class="form-control" required>
                                    <option value="">-- اختر تصنيف --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">عنوان البند *</label>
                                <input type="text" name="title" class="form-control" placeholder="أدخل العنوان..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">الوصف المختصر</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="أدخل وصفاً بسيطاً..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">أيقونة البند</label>
                                <input type="file" name="icon" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">صورة توضيحية (اختيارية)</label>
                                <input type="file" name="image" class="form-control" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">ترتيب الظهور</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="itemStatus" value="1" checked>
                                    <label class="form-check-label x-small fw-bold text-muted ms-2" for="itemStatus">تفعيل الظهور في الموقع</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> إضافة البند
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Items List Column --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-slide-up">
                    <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-grid-3x3-gap-fill me-2 text-primary"></i> البنود المضافة</h6>
                        <span class="badge bg-light text-muted border rounded-pill px-3 py-1 x-small fw-bold">{{ $items->count() }} بند</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-end">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-3 py-3 text-muted x-small fw-bold font-tajawal">البند</th>
                                    <th class="px-3 py-3 text-muted x-small fw-bold font-tajawal">التصنيف</th>
                                    <th class="px-3 py-3 text-muted x-small fw-bold text-center font-tajawal">الترتيب</th>
                                    <th class="px-3 py-3 text-muted x-small fw-bold text-center font-tajawal">الحالة</th>
                                    <th class="px-3 py-3 text-muted x-small fw-bold text-center font-tajawal">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($items as $item)
                                    <tr>
                                        <td class="px-3 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-sm-circle bg-light border">
                                                    @if($item->icon_url)
                                                        <img src="{{ $item->icon_url }}" class="rounded-circle" style="width:100%;height:100%;object-fit:cover;">
                                                    @else
                                                        <i class="bi bi-image text-muted opacity-25"></i>
                                                    @endif
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-dark mb-0">{{ $item->title }}</div>
                                                    <div class="x-small text-muted text-truncate" style="max-width: 180px;">{{ Str::limit($item->description, 50) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 x-small fw-bold">
                                                {{ $item->category->name ?? '---' }}
                                            </span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <span class="text-dark fw-bold x-small">{{ $item->sort_order }}</span>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <form action="{{ route('website.donation-settings.items.toggle', $item) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm border-0 bg-transparent p-0">
                                                    @if($item->status)
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 x-small fw-bold">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 x-small fw-bold">معطّل</span>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-icon-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editItem{{ $item->id }}">
                                                    <i class="bi bi-pencil-fill text-warning"></i>
                                                </button>
                                                <form action="{{ route('website.donation-settings.items.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا البند؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-icon-light rounded-pill">
                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Edit Modal --}}
                                    <div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-lg modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 p-4 pb-0">
                                                    <h5 class="modal-title fw-bold">تعديل البند: {{ $item->title }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('website.donation-settings.items.update', $item) }}" method="POST" enctype="multipart/form-data">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 text-end">
                                                        <div class="row g-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label x-small fw-bold text-muted">التصنيف الرئيسي</label>
                                                                <select name="category_id" class="form-control" required>
                                                                    @foreach($categories as $cat)
                                                                        <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label x-small fw-bold text-muted">عنوان البند</label>
                                                                <input type="text" name="title" value="{{ $item->title }}" class="form-control" required>
                                                            </div>
                                                            <div class="col-12">
                                                                <label class="form-label x-small fw-bold text-muted">الوصف</label>
                                                                <textarea name="description" class="form-control" rows="3">{{ $item->description }}</textarea>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label x-small fw-bold text-muted">تحديث الأيقونة</label>
                                                                <input type="file" name="icon" class="form-control" accept="image/*">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label x-small fw-bold text-muted">تحديث الصورة التوضيحية</label>
                                                                <input type="file" name="image" class="form-control" accept="image/*">
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label x-small fw-bold text-muted">الترتيب</label>
                                                                <input type="number" name="sort_order" value="{{ $item->sort_order }}" class="form-control">
                                                            </div>
                                                            <div class="col-md-6 d-flex align-items-end">
                                                                <div class="form-check form-switch custom-switch mb-2">
                                                                    <input class="form-check-input" type="checkbox" name="status" id="editItemStatus{{ $item->id }}" value="1" {{ $item->status ? 'checked' : '' }}>
                                                                    <label class="form-check-label x-small fw-bold text-muted ms-2" for="editItemStatus{{ $item->id }}">تفعيل البند</label>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">تطبيق التعديلات</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="opacity-25 mb-3">
                                                <i class="bi bi-grid-3x3-gap display-4"></i>
                                            </div>
                                            <h6 class="fw-bold text-muted">لا يوجد بنود تبرع حالياً</h6>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .donation-items-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .max-w-600 { max-width: 600px; }
    .transition-all { transition: all 0.3s ease; }
    .font-tajawal { font-family: 'Tajawal', sans-serif !important; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 50px 0 70px; 
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

    .avatar-sm-circle {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        font-size: 1.2rem;
    }

    .btn-icon-light {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #eee;
    }
    .btn-icon-light:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary-light); }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .custom-switch .form-check-input { width: 2.8rem; height: 1.4rem; cursor: pointer; }
    .table thead th { border-bottom: none; }
    .table tbody td { border-bottom: 1px solid #f2f2f2; }
</style>
@endsection
