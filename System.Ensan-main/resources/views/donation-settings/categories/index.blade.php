@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="donation-settings-page">
    {{-- Hero --}}
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background:#7c3aed;"></div>
            <div class="glow-orb-2" style="background:#a78bfa;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white">إعدادات التبرع</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-800 text-white mb-2">تصنيفات التبرع</h1>
                    <p class="lead text-white-50 mb-0">إدارة وترتيب تصنيفات التبرع التي تظهر على صفحة التبرع.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4">
            {{-- Add Category Card --}}
            <div class="col-lg-4">
                <div class="card dark-glass-card border-0 shadow-lg h-100">
                    <div class="card-header-lux">
                        <h5 class="mb-0 text-white fw-bold"><i class="bi bi-plus-circle me-2 text-purple"></i> إضافة تصنيف جديد</h5>
                    </div>
                    <div class="card-body-lux">
                        <form action="{{ route('website.donation-settings.categories.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="label-lux">اسم التصنيف *</label>
                                <input type="text" name="name" class="form-control form-input-dark" placeholder="مثال: المشاريع" required>
                            </div>
                            <div class="mb-3">
                                <label class="label-lux">الترتيب</label>
                                <input type="number" name="sort_order" class="form-control form-input-dark" value="0">
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="catStatus" checked>
                                    <label class="form-check-label text-white-50" for="catStatus">مفعّل</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-purple-solid w-100"><i class="bi bi-plus me-2"></i>إضافة</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Categories List --}}
            <div class="col-lg-8">
                <div class="card dark-glass-card border-0 shadow-lg">
                    <div class="card-header-lux d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white fw-bold"><i class="bi bi-tags me-2 text-warning"></i> التصنيفات الحالية ({{ $categories->count() }})</h5>
                        <a href="{{ route('website.donation-settings.items.index') }}" class="btn btn-sm btn-outline-info rounded-pill px-3">
                            <i class="bi bi-grid me-1"></i> عناصر التبرع
                        </a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="color:#e2e8f0;">
                            <thead style="background:rgba(255,255,255,0.03);">
                                <tr>
                                    <th class="px-4 py-3 border-0">الاسم</th>
                                    <th class="px-4 py-3 border-0">المعرف (Slug)</th>
                                    <th class="px-4 py-3 border-0 text-center">العناصر</th>
                                    <th class="px-4 py-3 border-0 text-center">الترتيب</th>
                                    <th class="px-4 py-3 border-0 text-center">الحالة</th>
                                    <th class="px-4 py-3 border-0 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($categories as $category)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                    <td class="px-4 py-3 fw-bold">{{ $category->name }}</td>
                                    <td class="px-4 py-3"><code class="text-info">{{ $category->slug }}</code></td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ $category->items->count() }}</span>
                                    </td>
                                    <td class="px-4 py-3 text-center">{{ $category->sort_order }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <form action="{{ route('website.donation-settings.categories.toggle', $category) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="badge border-0 {{ $category->status ? 'bg-success-glass text-emerald' : 'bg-danger-glass text-danger' }} rounded-pill px-3 py-2">
                                                {{ $category->status ? 'مفعّل' : 'معطّل' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-warning rounded-pill px-2"
                                                data-bs-toggle="modal" data-bs-target="#editCat{{ $category->id }}"
                                                title="تعديل"><i class="bi bi-pencil"></i></button>
                                            <form action="{{ route('website.donation-settings.categories.destroy', $category) }}" method="POST"
                                                onsubmit="return confirm('حذف هذا التصنيف وجميع عناصره؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2" title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editCat{{ $category->id }}" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content" style="background:#1a1f2e;color:#e2e8f0;">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title">تعديل: {{ $category->name }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('website.donation-settings.categories.update', $category) }}" method="POST">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label class="label-lux">الاسم</label>
                                                        <input type="text" name="name" value="{{ $category->name }}" class="form-control form-input-dark" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="label-lux">الترتيب</label>
                                                        <input type="number" name="sort_order" value="{{ $category->sort_order }}" class="form-control form-input-dark">
                                                    </div>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" name="status" {{ $category->status ? 'checked' : '' }}>
                                                        <label class="form-check-label text-white-50">مفعّل</label>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-purple-solid">حفظ التعديلات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr><td colspan="6" class="py-5 text-center text-muted">لا توجد تصنيفات بعد</td></tr>
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
    .donation-settings-page { min-height:100vh; background:#0b0e14; font-family:'Tajawal',sans-serif; }
    .premium-hero-sleek { position:relative; padding:60px 0 80px; border-radius:0 0 30px 30px; overflow:hidden; }
    .hero-bg-visuals div { position:absolute; border-radius:50%; filter:blur(100px); opacity:0.4; }
    .glow-orb-1 { width:400px; height:400px; top:-100px; right:-50px; }
    .glow-orb-2 { width:300px; height:300px; bottom:-150px; left:-50px; }
    .noise-overlay { position:absolute; inset:0; opacity:0.05; }
    .hero-content-wrapper { position:relative; z-index:5; padding:0 5%; }
    .fw-800 { font-weight:800; }
    .animate-reveal { animation:revealRight 1s both; }
    @keyframes revealRight { from{opacity:0;transform:translateX(50px)} to{opacity:1;transform:translateX(0)} }

    .dark-glass-card { background:#1a1f2e; border-radius:20px; overflow:hidden; border:1px solid rgba(255,255,255,0.05); }
    .card-header-lux { padding:20px; border-bottom:1px solid rgba(255,255,255,0.05); }
    .card-body-lux { padding:20px; }
    .label-lux { color:#94a3b8; font-weight:700; font-size:0.8rem; margin-bottom:5px; display:block; }
    .form-input-dark { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#f8fafc; border-radius:10px; }
    .form-input-dark:focus { background:rgba(255,255,255,0.08); border-color:#7c3aed; color:#f8fafc; box-shadow:0 0 0 3px rgba(124,58,237,0.2); }
    .btn-purple-solid { background:#7c3aed; border:none; color:#fff; border-radius:12px; padding:10px 20px; font-weight:700; transition:0.3s; }
    .btn-purple-solid:hover { background:#6d28d9; color:#fff; transform:translateY(-2px); }
    .text-purple { color:#a78bfa; }
    .bg-success-glass { background:rgba(16,185,129,0.15); }
    .bg-danger-glass { background:rgba(239,68,68,0.15); }
    .text-emerald { color:#34d399!important; }
</style>
@endsection
