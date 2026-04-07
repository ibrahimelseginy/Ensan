@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="donation-settings-page">
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #0e7490 0%, #22d3ee 100%);">
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('website.donation-settings.categories.index') }}" class="text-white-50">التصنيفات</a></li>
                            <li class="breadcrumb-item active text-white">عناصر التبرع</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-800 text-white mb-2">عناصر التبرع</h1>
                    <p class="lead text-white-50 mb-0">إدارة البطاقات التي تظهر على صفحة التبرع لكل تصنيف.</p>
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
            {{-- Add Item Form --}}
            <div class="col-lg-4">
                <div class="card dark-glass-card border-0 shadow-lg">
                    <div class="card-header-lux">
                        <h5 class="mb-0 text-white fw-bold"><i class="bi bi-plus-circle me-2 text-cyan"></i> إضافة عنصر جديد</h5>
                    </div>
                    <div class="card-body-lux">
                        <form action="{{ route('website.donation-settings.items.store') }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="mb-3">
                                <label class="label-lux">التصنيف *</label>
                                <select name="category_id" class="form-control form-input-dark" required>
                                    <option value="">-- اختر تصنيف --</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="label-lux">العنوان *</label>
                                <input type="text" name="title" class="form-control form-input-dark" placeholder="مثال: كفالة أيتام وأسر" required>
                            </div>
                            <div class="mb-3">
                                <label class="label-lux">الوصف</label>
                                <textarea name="description" class="form-control form-input-dark" rows="3" placeholder="وصف مختصر..."></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="label-lux">الأيقونة (Icon)</label>
                                <input type="file" name="icon" class="form-control form-input-dark" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="label-lux">الصورة (اختياري)</label>
                                <input type="file" name="image" class="form-control form-input-dark" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label class="label-lux">الترتيب</label>
                                <input type="number" name="sort_order" class="form-control form-input-dark" value="0">
                            </div>
                            <div class="mb-4">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" name="status" id="itemStatus" checked>
                                    <label class="form-check-label text-white-50" for="itemStatus">مفعّل</label>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-cyan-solid w-100"><i class="bi bi-plus me-2"></i>إضافة</button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- Items Table --}}
            <div class="col-lg-8">
                <div class="card dark-glass-card border-0 shadow-lg">
                    <div class="card-header-lux">
                        <h5 class="mb-0 text-white fw-bold"><i class="bi bi-grid me-2 text-warning"></i> العناصر الحالية ({{ $items->count() }})</h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" style="color:#e2e8f0;">
                            <thead style="background:rgba(255,255,255,0.03);">
                                <tr>
                                    <th class="px-3 py-3 border-0">الأيقونة</th>
                                    <th class="px-3 py-3 border-0">العنوان</th>
                                    <th class="px-3 py-3 border-0">التصنيف</th>
                                    <th class="px-3 py-3 border-0 text-center">الترتيب</th>
                                    <th class="px-3 py-3 border-0 text-center">الحالة</th>
                                    <th class="px-3 py-3 border-0 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($items as $item)
                                <tr style="border-bottom:1px solid rgba(255,255,255,0.05);">
                                    <td class="px-3 py-3">
                                        @if($item->icon_url)
                                            <img src="{{ $item->icon_url }}" alt="" style="width:40px;height:40px;object-fit:cover;border-radius:8px;">
                                        @else
                                            <div class="icon-placeholder"><i class="bi bi-image text-muted fs-4"></i></div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="fw-bold">{{ $item->title }}</div>
                                        @if($item->description)
                                            <div class="text-muted small text-truncate" style="max-width:200px;">{{ $item->description }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 py-3">
                                        <span class="badge bg-purple-glass text-purple rounded-pill px-3">{{ $item->category->name ?? '---' }}</span>
                                    </td>
                                    <td class="px-3 py-3 text-center">{{ $item->sort_order }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <form action="{{ route('website.donation-settings.items.toggle', $item) }}" method="POST" class="d-inline">
                                            @csrf @method('PATCH')
                                            <button type="submit" class="badge border-0 {{ $item->status ? 'bg-success-glass text-emerald' : 'bg-danger-glass text-danger' }} rounded-pill px-3 py-2">
                                                {{ $item->status ? 'مفعّل' : 'معطّل' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="px-3 py-3 text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            <button class="btn btn-sm btn-outline-warning rounded-pill px-2"
                                                data-bs-toggle="modal" data-bs-target="#editItem{{ $item->id }}" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('website.donation-settings.items.destroy', $item) }}" method="POST"
                                                onsubmit="return confirm('حذف هذا العنصر؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-2"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                {{-- Edit Modal --}}
                                <div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content" style="background:#1a1f2e;color:#e2e8f0;">
                                            <div class="modal-header border-0">
                                                <h5 class="modal-title">تعديل: {{ $item->title }}</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="{{ route('website.donation-settings.items.update', $item) }}" method="POST" enctype="multipart/form-data">
                                                @csrf @method('PUT')
                                                <div class="modal-body">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="label-lux">التصنيف</label>
                                                            <select name="category_id" class="form-control form-input-dark" required>
                                                                @foreach($categories as $cat)
                                                                    <option value="{{ $cat->id }}" {{ $item->category_id == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="label-lux">العنوان</label>
                                                            <input type="text" name="title" value="{{ $item->title }}" class="form-control form-input-dark" required>
                                                        </div>
                                                        <div class="col-12">
                                                            <label class="label-lux">الوصف</label>
                                                            <textarea name="description" class="form-control form-input-dark" rows="2">{{ $item->description }}</textarea>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="label-lux">أيقونة جديدة (اتركه فارغاً للإبقاء على الحالي)</label>
                                                            <input type="file" name="icon" class="form-control form-input-dark" accept="image/*">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="label-lux">صورة جديدة</label>
                                                            <input type="file" name="image" class="form-control form-input-dark" accept="image/*">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="label-lux">الترتيب</label>
                                                            <input type="number" name="sort_order" value="{{ $item->sort_order }}" class="form-control form-input-dark">
                                                        </div>
                                                        <div class="col-md-6 d-flex align-items-end">
                                                            <div class="form-check form-switch">
                                                                <input class="form-check-input" type="checkbox" name="status" {{ $item->status ? 'checked' : '' }}>
                                                                <label class="form-check-label text-white-50">مفعّل</label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer border-0">
                                                    <button type="submit" class="btn btn-cyan-solid">حفظ التعديلات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @empty
                                <tr><td colspan="6" class="py-5 text-center text-muted"><i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>لا توجد عناصر بعد</td></tr>
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
    .premium-hero-sleek { position:relative; padding:60px 0 80px; border-radius:0 0 30px 30px; }
    .hero-content-wrapper { position:relative; z-index:5; padding:0 5%; }
    .fw-800 { font-weight:800; }
    .animate-reveal { animation:revealRight 1s both; }
    @keyframes revealRight { from{opacity:0;transform:translateX(50px)} to{opacity:1;transform:translateX(0)} }
    .dark-glass-card { background:#1a1f2e; border-radius:20px; overflow:hidden; border:1px solid rgba(255,255,255,0.05); }
    .card-header-lux { padding:20px; border-bottom:1px solid rgba(255,255,255,0.05); }
    .card-body-lux { padding:20px; }
    .label-lux { color:#94a3b8; font-weight:700; font-size:0.8rem; margin-bottom:5px; display:block; }
    .form-input-dark { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); color:#f8fafc; border-radius:10px; }
    .form-input-dark:focus { background:rgba(255,255,255,0.08); border-color:#0e7490; color:#f8fafc; box-shadow:0 0 0 3px rgba(14,116,144,0.2); }
    .btn-cyan-solid { background:#0e7490; border:none; color:#fff; border-radius:12px; padding:10px 20px; font-weight:700; transition:0.3s; }
    .btn-cyan-solid:hover { background:#0c6277; color:#fff; transform:translateY(-2px); }
    .text-cyan { color:#22d3ee; }
    .bg-purple-glass { background:rgba(124,58,237,0.15); }
    .text-purple { color:#a78bfa!important; }
    .icon-placeholder { width:40px; height:40px; background:rgba(255,255,255,0.05); border-radius:8px; display:flex; align-items:center; justify-content:center; }
    .bg-success-glass { background:rgba(16,185,129,0.15); }
    .bg-danger-glass { background:rgba(239,68,68,0.15); }
    .text-emerald { color:#34d399!important; }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>
@endsection


