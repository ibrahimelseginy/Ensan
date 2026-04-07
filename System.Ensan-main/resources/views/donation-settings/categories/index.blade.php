@extends('layouts.app')

@section('content')
<div class="donation-categories-page">
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
                    <li class="breadcrumb-item active" aria-current="page">تصنيفات التبرع</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-tag-fill me-2"></i> هيكلة أوجه العطاء 🏷️
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">تصنيفات التبرع</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                إدارة التصنيفات الرئيسية التي تظهر للمتبرع، وترتيب ظهورها في قوائم "أوجه التبرع".
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
            {{-- Add Category Column --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="p-4 border-bottom bg-white">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-plus-circle me-2 text-primary"></i> إضافة تصنيف جديد</h6>
                    </div>
                    <div class="card-body p-4">
                        <form action="{{ route('website.donation-settings.categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">اسم التصنيف (مثل: الإطعام، الوقف)</label>
                                <input type="text" name="name" class="form-control" placeholder="أدخل الاسم..." required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label x-small fw-bold text-muted">ترتيب الظهور</label>
                                <input type="number" name="sort_order" class="form-control" value="0">
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch custom-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="catStatus" value="1" checked>
                                    <label class="form-check-label x-small fw-bold text-muted ms-2" for="catStatus">تفعيل الظهور في الموقع</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-plus-lg me-1"></i> إضافة التصنيف
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Categories List Column --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-slide-up">
                    <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-tags-fill me-2 text-primary"></i> التصنيفات الحالية</h6>
                        <a href="{{ route('website.donation-settings.items.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-1 x-small fw-bold transition-all">
                            إدارة البنود الفرعية <i class="bi bi-arrow-left ms-1"></i>
                        </a>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-end">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted x-small fw-bold font-tajawal">التصنيف</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold font-tajawal">الرابط (Slug)</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold text-center font-tajawal">البنود</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold text-center font-tajawal">الحالة</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold text-center font-tajawal">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($categories as $category)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="dot {{ $category->status ? 'bg-primary' : 'bg-secondary opacity-25' }}"></div>
                                                <span class="fw-bold text-dark">{{ $category->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <code class="x-small text-muted bg-light px-2 py-1 rounded">{{ $category->slug }}</code>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 x-small fw-bold">
                                                {{ $category->items->count() }} عنصر
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <form action="{{ route('website.donation-settings.categories.toggle', $category) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <button type="submit" class="btn btn-sm border-0 bg-transparent p-0">
                                                    @if($category->status)
                                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 x-small fw-bold">نشط</span>
                                                    @else
                                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 x-small fw-bold">معطّل</span>
                                                    @endif
                                                </button>
                                            </form>
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-icon-light rounded-pill" data-bs-toggle="modal" data-bs-target="#editCat{{ $category->id }}">
                                                    <i class="bi bi-pencil-fill text-warning"></i>
                                                </button>
                                                <form action="{{ route('website.donation-settings.categories.destroy', $category) }}" method="POST" onsubmit="return confirm('حذف هذا التصنيف وجميع البنود الفرعية التابعة له؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-icon-light rounded-pill">
                                                        <i class="bi bi-trash-fill text-danger"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>

                                    {{-- Edit Modal --}}
                                    <div class="modal fade" id="editCat{{ $category->id }}" tabindex="-1">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content border-0 shadow-lg rounded-4">
                                                <div class="modal-header border-0 p-4 pb-0">
                                                    <h5 class="modal-title fw-bold">تعديل التصنيف: {{ $category->name }}</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                </div>
                                                <form action="{{ route('website.donation-settings.categories.update', $category) }}" method="POST">
                                                    @csrf @method('PUT')
                                                    <div class="modal-body p-4 text-end">
                                                        <div class="mb-3">
                                                            <label class="form-label x-small fw-bold text-muted">اسم التصنيف</label>
                                                            <input type="text" name="name" value="{{ $category->name }}" class="form-control" required>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label x-small fw-bold text-muted">ترتيب الظهور</label>
                                                            <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="form-control">
                                                        </div>
                                                        <div class="form-check form-switch custom-switch">
                                                            <input class="form-check-input" type="checkbox" name="status" id="editCatStatus{{ $category->id }}" value="1" {{ $category->status ? 'checked' : '' }}>
                                                            <label class="form-check-label x-small fw-bold text-muted ms-2" for="editCatStatus{{ $category->id }}">تفعيل التصنيف</label>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer border-0 p-4 pt-0">
                                                        <button type="button" class="btn btn-light rounded-pill px-4 fw-bold x-small" data-bs-dismiss="modal">إلغاء</button>
                                                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold x-small shadow-sm">تحديث التصنيف</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="opacity-25 mb-3">
                                                <i class="bi bi-tag display-4"></i>
                                            </div>
                                            <h6 class="fw-bold text-muted">لا يوجد تصنيفات حالياً</h6>
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
    .donation-categories-page { min-height: 100vh; }
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

    .dot { width: 8px; height: 8px; border-radius: 50%; }

    .btn-icon-light {
        width: 34px;
        height: 34px;
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
