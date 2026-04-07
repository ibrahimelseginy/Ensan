@extends('layouts.app')

@section('styles')
<style>
    :root {
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.08);
        --purple-glow: rgba(139, 92, 246, 0.3);
        --cyan-glow: rgba(6, 182, 212, 0.3);
        --emerald-glow: rgba(16, 185, 129, 0.3);
    }

    .unified-layout {
        display: grid;
        grid-template-columns: 320px 1fr;
        gap: 2rem;
        min-height: calc(100vh - 120px);
    }

    /* Left Sidebar: Categories */
    .categories-sidebar {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 1.5rem;
        display: flex;
        flex-direction: column;
        gap: 1rem;
        backdrop-filter: blur(10px);
    }

    .category-item {
        padding: 1.2rem;
        border-radius: 16px;
        border: 1px solid transparent;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: rgba(255, 255, 255, 0.02);
        color: #94a3b8;
        text-decoration: none;
    }

    .category-item:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: var(--glass-border);
        transform: translateX(-5px);
        color: #fff;
    }

    .category-item.active {
        background: linear-gradient(135deg, rgba(139, 92, 246, 0.1), rgba(6, 182, 212, 0.1));
        border-color: rgba(139, 92, 246, 0.3);
        color: #fff;
        box-shadow: 0 0 20px var(--purple-glow);
    }

    /* Premium Modal Styling */
    .btn-save-premium {
        background-color: #00d1b2 !important;
        border: none !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 10px 25px !important;
        border-radius: 8px !important;
    }
    .btn-cancel-premium {
        background-color: #363636 !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 10px 20px !important;
        border-radius: 8px !important;
    }

    /* Main Content Area */
    .items-container {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .sector-header {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .sector-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, var(--purple-glow) 0%, transparent 70%);
        opacity: 0.1;
        z-index: 0;
    }

    .item-card {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 20px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        gap: 1.5rem;
        align-items: center;
    }

    .item-card:hover {
        border-color: rgba(6, 182, 212, 0.3);
        background: rgba(255, 255, 255, 0.05);
        transform: translateY(-5px);
    }

    .item-card-dark {
        background: rgba(15, 23, 42, 0.9) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
    }

    .item-card-dark .item-info h5 {
        color: #fff !important;
    }

    .item-card-dark .item-info p {
        color: #94a3b8 !important;
    }

    /* Missing Premium Styles */
    .label-lux {
        display: block;
        margin-bottom: 8px;
        color: #94a3b8;
        font-weight: 500;
        font-size: 0.9rem;
    }

    .form-input-dark {
        background: rgba(15, 23, 42, 0.6) !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
        border-radius: 12px !important;
        padding: 12px 15px !important;
        transition: all 0.3s ease;
    }

    .form-input-dark:focus {
        background: rgba(15, 23, 42, 0.8) !important;
        border-color: var(--primary-accent) !important;
        box-shadow: 0 0 0 4px rgba(6, 182, 212, 0.1) !important;
        outline: none;
    }

    .dark-glass-card {
        background: rgba(15, 23, 42, 0.9) !important;
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        border-radius: 24px !important;
    }

    .modal-header.border-0 {
        padding: 30px 30px 10px;
    }

    .modal-body.p-4 {
        padding: 30px !important;
    }

    .modal-footer.border-0 {
        padding: 10px 30px 30px;
    }

    .btn-lux {
        background: var(--primary-accent);
        color: #0f172a;
        border: none;
        padding: 12px 25px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-lux:hover {
        background: #22d3ee;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(6, 182, 212, 0.2);
    }

    .item-icon-box {
        width: 80px;
        height: 80px;
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.05);
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
        border: 1px solid var(--glass-border);
    }

    .item-icon-box img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .item-info {
        flex: 1;
    }

    .item-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-lux {
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-weight: 500;
        transition: all 0.3s ease;
        border: 1px solid transparent;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-lux-primary {
        background: linear-gradient(135deg, #8b5cf6, #06b6d4);
        color: white;
        border: none;
    }

    .btn-lux-primary:hover {
        box-shadow: 0 0 15px var(--purple-glow);
        transform: scale(1.02);
    }

    .empty-state {
        padding: 5rem;
        text-align: center;
        border: 2px dashed var(--glass-border);
        border-radius: 24px;
        color: #64748b;
    }

    @media (max-width: 992px) {
        .unified-layout {
            grid-template-columns: 1fr;
        }
    }
      
      
      

      

      /* --- SYSTEM LIGHT MODE PATCH --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .text-white, 
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white, 
      body:not(.theme-dark) .premium-hero-sleek .text-white-50,
      body:not(.theme-dark) .badge-glass-premium,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item a {
          color: #fff !important;
      }
      body:not(.theme-dark) .glass-card, 
      body:not(.theme-dark) .premium-modal-dark,
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .stats-card-dark,
      body:not(.theme-dark) .stats-inner-card,
      body:not(.theme-dark) .project-card-admin,
      body:not(.theme-dark) .campaign-card-lux,
      body:not(.theme-dark) .guest-card-lux,
      body:not(.theme-dark) .article-card-lux,
      body:not(.theme-dark) .message-card-lux,
      body:not(.theme-dark) .donation-card-lux,
      body:not(.theme-dark) .member-card-premium,
      body:not(.theme-dark) .partner-card-lux,
      body:not(.theme-dark) .leader-card-lux,
      body:not(.theme-dark) .empty-state-card-lux,
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content,
      body:not(.theme-dark) .categories-sidebar,
      body:not(.theme-dark) .sector-header,
      body:not(.theme-dark) .item-card,
      body:not(.theme-dark) .dark-glass-card {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .category-item {
          color: var(--ws-text-secondary);
          background: rgba(0,0,0,0.02);
      }
      body:not(.theme-dark) .category-item:hover { background: var(--ws-bg-page); color: var(--ws-text-primary); }
      body:not(.theme-dark) .category-item.active { background: var(--ws-bg-page); border-color: var(--ws-primary); color: var(--ws-text-primary); }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control, body:not(.theme-dark) .form-select, body:not(.theme-dark) .form-input-dark { 
          background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; 
      }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
      </style>
@endsection

@section('content')
<div class="row align-items-center mb-5">
    <div class="col-md-6">
        <h1 class="display-5 fw-bold text-white mb-2">مجالات الدعم</h1>
        <p class="text-white-50 lead">إدارة قطاعات التبرع والعناصر التابعة لها من مكان واحد.</p>
    </div>
    <div class="col-md-6 text-md-end">
        <button class="btn btn-lux btn-lux-primary py-3 px-4" data-bs-toggle="modal" data-bs-target="#addCategoryModal">
            <i class="bi bi-folder-plus fs-5"></i>
            إضافة مجال جديد
        </button>
    </div>
</div>

<div class="unified-layout">
    {{-- Categories Sidebar --}}
    <div class="categories-sidebar">
        <h6 class="text-white-50 px-2 mb-3 small text-uppercase tracking-wider fw-bold">المجالات المتاحة</h6>
        <div class="list-group list-group-flush border-0">
            @foreach($categories as $cat)
                <a href="?category={{ $cat->id }}" class="category-item {{ (request('category') == $cat->id || (!request('category') && $loop->first)) ? 'active' : '' }}">
                    <div class="d-flex align-items-center gap-3">
                        <span class="fw-bold">{{ $cat->name }}</span>
                    </div>
                    <span class="badge rounded-pill bg-white bg-opacity-10 text-white-50">{{ $cat->items->count() }}</span>
                </a>
            @endforeach
        </div>
    </div>

    {{-- Items Content Area --}}
    <div class="items-content">
        @php
            $currentCatId = request('category') ?: ($categories->first()?->id);
            $currentCat = $categories->find($currentCatId);
        @endphp

        @if($currentCat)
            <div class="items-container">
                <div class="sector-header d-flex justify-content-between align-items-end">
                    <div class="position-relative z-1">
                        <span class="badge bg-purple-solid mb-3">مجال الدعم</span>
                        <h2 class="display-6 fw-bold text-white mb-0">{{ $currentCat->name }}</h2>
                    </div>
                    <div class="position-relative z-1">
                        <button class="btn btn-lux btn-lux-primary" data-bs-toggle="modal" data-bs-target="#addItemModal">
                            <i class="bi bi-plus-lg"></i> إضافة جزء لهذا المجال
                        </button>
                    </div>
                </div>

                @if($currentCat->items->isEmpty())
                    <div class="empty-state">
                        <i class="bi bi-grid-3x3-gap display-1 opacity-10 mb-4 d-block"></i>
                        <h4>لا توجد عناصر في هذا المجال بعد</h4>
                        <p>ابدأ بإضافة أول جزء (عنصر تبرع) لهذا القطاع ليظهر في صفحة التبرعات.</p>
                    </div>
                @else
                    <div class="row g-4">
                        @foreach($currentCat->items->sortBy('sort_order') as $item)
                            <div class="col-12">
                                <div class="item-card {{ $item->bg_style === 'dark' ? 'item-card-dark' : '' }}">
                                    <div class="item-icon-box">
                                        @if($item->icon)
                                            <img src="{{ Str::startsWith($item->icon, 'http') ? $item->icon : Storage::url($item->icon) }}" alt="icon">
                                        @else
                                            <i class="bi bi-image text-white-50 fs-2"></i>
                                        @endif
                                    </div>
                                    <div class="item-info">
                                        <h5 class="text-white fw-bold mb-1">{{ $item->title }}</h5>
                                        <p class="text-white-50 mb-0 small">{{ Str::limit($item->description, 100) }}</p>
                                    </div>
                                    <div class="item-actions">
                                        <button class="btn btn-sm btn-outline-info rounded-pill px-3" 
                                                data-bs-toggle="modal" data-bs-target="#editItem{{ $item->id }}">
                                            <i class="bi bi-pencil me-1"></i> تعديل
                                        </button>
                                        <form action="{{ route('website.donation-settings.items.destroy', $item) }}" method="POST" onsubmit="return confirm('حذف هذا الجزء؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @else
            <div class="empty-state">
                <h4>يرجى اختيار مجال من القائمة الجانبية</h4>
            </div>
        @endif
    </div>
</div>

{{-- Modals from previous views should be included here or kept separately --}}
{{-- For speed, I will include the Add Category and Add Item modals here briefly --}}

<!-- Add Category Modal -->
<div class="modal fade" id="addCategoryModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content dark-glass-card shadow-lg border-0" style="background-color: #0b0e14 !important; opacity: 1 !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
            <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px;">
                <h5 class="modal-title fw-bold">إضافة مجال دعم جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.categories.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="label-lux">اسم المجال *</label>
                        <input type="text" name="name" class="form-control form-input-dark" required>
                    </div>
                    <div class="mb-3">
                        <label class="label-lux">الترتيب</label>
                        <input type="number" name="sort_order" class="form-control form-input-dark" value="0">
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body" style="background-color: #0b0e14 !important;">
                    <button type="submit" class="btn btn-save-premium px-4">حفظ المجال</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($currentCat)
<!-- Add Item Modal -->
<div class="modal fade" id="addItemModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content dark-glass-card shadow-lg border-0" style="background-color: #0b0e14 !important; opacity: 1 !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
            <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px;">
                <h5 class="modal-title fw-bold">إضافة جزء جديد لـ: {{ $currentCat->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.items.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="category_id" value="{{ $currentCat->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="label-lux">العنوان *</label>
                            <input type="text" name="title" class="form-control form-input-dark" required>
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">الأيقونة (Icon)</label>
                            <input type="file" name="icon" class="form-control form-input-dark">
                        </div>
                        <div class="col-12">
                            <label class="label-lux">وصف قصير *</label>
                            <textarea name="description" class="form-control form-input-dark" rows="3" required></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">الصورة (اختياري)</label>
                            <input type="file" name="image" class="form-control form-input-dark">
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">تنسيق الكارد</label>
                            <select name="bg_style" class="form-select form-input-dark">
                                <option value="light">فاتح (Glassmorphism)</option>
                                <option value="dark">داكن (Deep Dark)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">الترتيب</label>
                            <input type="number" name="sort_order" class="form-control form-input-dark" value="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body" style="background-color: #0b0e14 !important;">
                    <button type="submit" class="btn btn-save-premium px-4">حفظ الجزء</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

@foreach($currentCat->items as $item)
<!-- Edit Item Modal -->
<div class="modal fade" id="editItem{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content dark-glass-card shadow-lg border-0" style="background-color: #0b0e14 !important; opacity: 1 !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
            <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px;">
                <h5 class="modal-title fw-bold">تعديل جزء: {{ $item->title }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('website.donation-settings.items.update', $item) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <input type="hidden" name="category_id" value="{{ $currentCat->id }}">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="label-lux">العنوان *</label>
                            <input type="text" name="title" class="form-control form-input-dark" value="{{ $item->title }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">الأيقونة (تغيير؟)</label>
                            <input type="file" name="icon" class="form-control form-input-dark">
                        </div>
                        <div class="col-12">
                            <label class="label-lux">وصف قصير *</label>
                            <textarea name="description" class="form-control form-input-dark" rows="3" required>{{ $item->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">الصورة (تغيير؟)</label>
                            <input type="file" name="image" class="form-control form-input-dark">
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">تنسيق الكارد</label>
                            <select name="bg_style" class="form-select form-input-dark">
                                <option value="light" {{ $item->bg_style === 'light' ? 'selected' : '' }}>فاتح (Glassmorphism)</option>
                                <option value="dark" {{ $item->bg_style === 'dark' ? 'selected' : '' }}>داكن (Deep Dark)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux">الترتيب</label>
                            <input type="number" name="sort_order" class="form-control form-input-dark" value="{{ $item->sort_order }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body" style="background-color: #0b0e14 !important;">
                    <button type="submit" class="btn btn-save-premium px-4">تحديث</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endif


<style>
      /* --- SYSTEM LIGHT MODE PATCH --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .text-white, 
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white, 
      body:not(.theme-dark) .premium-hero-sleek .text-white-50,
      body:not(.theme-dark) .badge-glass-premium,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item a {
          color: #fff !important;
      }
      body:not(.theme-dark) .glass-card, 
      body:not(.theme-dark) .premium-modal-dark,
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .stats-card-dark,
      body:not(.theme-dark) .stats-inner-card,
      body:not(.theme-dark) .project-card-admin,
      body:not(.theme-dark) .campaign-card-lux,
      body:not(.theme-dark) .guest-card-lux,
      body:not(.theme-dark) .article-card-lux,
      body:not(.theme-dark) .message-card-lux,
      body:not(.theme-dark) .donation-card-lux,
      body:not(.theme-dark) .member-card-premium,
      body:not(.theme-dark) .partner-card-lux,
      body:not(.theme-dark) .leader-card-lux,
      body:not(.theme-dark) .empty-state-card-lux,
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content,
      body:not(.theme-dark) .categories-sidebar,
      body:not(.theme-dark) .sector-header,
      body:not(.theme-dark) .item-card,
      body:not(.theme-dark) .dark-glass-card {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .category-item {
          color: var(--ws-text-secondary);
          background: rgba(0,0,0,0.02);
      }
      body:not(.theme-dark) .category-item:hover { background: var(--ws-bg-page); color: var(--ws-text-primary); }
      body:not(.theme-dark) .category-item.active { background: var(--ws-bg-page); border-color: var(--ws-primary); color: var(--ws-text-primary); }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control, body:not(.theme-dark) .form-select, body:not(.theme-dark) .form-input-dark { 
          background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; 
      }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
</style>
@endsection





