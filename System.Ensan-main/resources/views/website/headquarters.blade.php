@extends('layouts.app')

@section('content')
<div class="settings-page">
    <div class="premium-hero-sleek mb-4 shadow-sm animate-reveal-right">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1 bg-info"></div>
            <div class="glow-orb-2 bg-primary"></div>
        </div>
        <div class="container-fluid hero-content-wrapper text-center">
            <div class="badge-glass-premium mb-3 pulse-animation" style="color: var(--ws-text-primary);">
                <i class="bi bi-geo-alt-fill me-2"></i> إدارة المقر والفروع
            </div>
            <h1 class="display-4 fw-800 mb-2" style="color: var(--ws-text-primary);">تخصيص بيانات المقر</h1>
            <p class="lead opacity-75 max-w-600 mx-auto" style="color: var(--ws-text-secondary);">إدارة بيانات العنوان، التغطية الجغرافية، وإحصائيات المقر.</p>
        </div>
    </div>



    <div class="container-fluid">
        <div class="row g-4">
            <div class="col-lg-8">
                <form action="{{ route('website.headquarters.update') }}" method="POST">
                    @csrf
                    <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up ws-card">
                        <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center ws-card-header">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-geo-alt me-2 text-info"></i> محتوى صفحة الفروع</h5>
                            <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-4 shadow-sm">حفظ التغييرات</button>
                        </div>
                        <div class="p-4 ws-card-header">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label small fw-bold ws-label">عنوان القسم (يظهر في الهيرو)</label>
                                    <input type="text" name="headquarters_title" class="form-control ws-input" value="{{ $settings['headquarters_title'] ?? 'كفر الشيخ - المقر الرئيسي' }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold ws-label">وصف القسم</label>
                                    <textarea name="headquarters_description" class="form-control ws-input" rows="3">{{ $settings['headquarters_description'] ?? 'المقر الإداري الرئيسي للمؤسسة، يختص بإدارة كافة الأنشطة والمشاريع الخيرية.' }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- HQ Stats --}}
                    <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up ws-card" style="animation-delay: 0.1s;">
                        <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center ws-card-header">
                            <h5 class="mb-0 fw-bold text-white"><i class="bi bi-graph-up me-2 text-success"></i> إحصائيات التغطية (تغطيتنا)</h5>
                            <button type="submit" class="btn btn-sm btn-success text-white rounded-pill px-4 shadow-sm">
                                <i class="bi bi-check-lg me-1"></i> حفظ الإحصائيات
                            </button>
                        </div>
                        <div class="p-4 ws-card-header">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold ws-label">محافظات</label>
                                    <input type="text" name="headquarters_stats_governorates" class="form-control ws-input mb-2" value="{{ $settings['headquarters_stats_governorates'] ?? '2' }}">
                                    <input type="text" name="headquarters_stats_governorates_label" class="form-control form-control-sm ws-input border-secondary text-center" value="{{ $settings['headquarters_stats_governorates_label'] ?? 'محافظات' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold ws-label">موظفين</label>
                                    <input type="text" name="headquarters_stats_employees" class="form-control ws-input mb-2" value="{{ $settings['headquarters_stats_employees'] ?? '+200' }}">
                                    <input type="text" name="headquarters_stats_employees_label" class="form-control form-control-sm ws-input border-secondary text-center" value="{{ $settings['headquarters_stats_employees_label'] ?? 'موظف' }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold ws-label">متبرعين</label>
                                    <input type="text" name="headquarters_stats_donors" class="form-control ws-input mb-2" value="{{ $settings['headquarters_stats_donors'] ?? '+10K' }}">
                                    <input type="text" name="headquarters_stats_donors_label" class="form-control form-control-sm ws-input border-secondary text-center" value="{{ $settings['headquarters_stats_donors_label'] ?? 'متبرع' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>

                {{-- Branches Table --}}
                <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up ws-card" style="animation-delay: 0.2s;">
                    <div class="p-4 border-bottom border-white border-opacity-10 d-flex justify-content-between align-items-center ws-card-header">
                        <h5 class="mb-0 fw-bold text-white"><i class="bi bi-geo-alt me-2 text-warning"></i> قائمة الفروع</h5>
                        <button type="button" class="btn btn-sm btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addBranch">
                            <i class="bi bi-plus-lg me-1"></i> إضافة فرع
                        </button>
                    </div>
                    <div class="p-0 ws-card-header">
                        <div class="table-responsive">
                            <table class="table ws-table table-hover align-middle mb-0 border-secondary">
                                <thead>
                                    <tr class="border-secondary">
                                        <th class="ps-4 bg-transparent ws-label">اسم الفرع</th>
                                        <th class="bg-transparent ws-label">العنوان</th>
                                        <th class="text-center bg-transparent ws-label">الرئيسي</th>
                                        <th class="text-center bg-transparent ws-label">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody class="border-secondary">
                                    @foreach($branches as $branch)
                                    <tr class="border-secondary">
                                        <td class="ps-4">
                                            <div class="fw-bold" style="color: var(--ws-text-primary);">{{ $branch->name }}</div>
                                            <div class="small ws-label">{{ $branch->phone }}</div>
                                            @if($branch->description)
                                                <div class="x-small text-slate-500 mt-1"><i class="bi bi-info-circle me-1"></i>{{ $branch->description }}</div>
                                            @endif
                                        </td>
                                        <td><span class="small ws-heading">{{ \Illuminate\Support\Str::limit($branch->address, 50) }}</span></td>
                                        <td class="text-center">
                                            @if($branch->is_main)
                                                <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill">رئيسي</span>
                                            @else
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill">فرعي</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editBranch{{ $branch->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <form action="{{ route('website.headquarters.branches.destroy', $branch) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الفرع؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                    @if($branches->isEmpty())
                                    <tr>
                                        <td colspan="4" class="text-center py-5 ws-label">
                                            <i class="bi bi-geo-alt fs-1 d-block mb-3 opacity-25"></i>
                                            لم يتم إضافة أي فروع بعد.
                                        </td>
                                    </tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

    {{-- Add Branch Modal --}}
    <div class="modal fade" id="addBranch" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('website.headquarters.branches.store') }}" method="POST" class="modal-content glass-card border-0 ws-card ws-card-header">
                @csrf
                <div class="modal-header border-bottom border-white border-opacity-10 p-4 pb-0">
                    <h5 class="modal-title fw-bold" style="color: var(--ws-text-primary);">إضافة فرع جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">اسم الفرع</label>
                            <input type="text" name="name" class="form-control ws-input" required placeholder="مثلاً: فرع كفر الشيخ">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">حالة الفرع</label>
                            <input type="text" name="status_text" class="form-control ws-input" placeholder="مثلاً: مفتوح الآن، نشط، ..." value="مفتوح الآن">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control ws-input" style="direction: ltr; text-align: left;" placeholder="012xxxxxxx">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold ws-label">العنوان التفصيلي</label>
                            <textarea name="address" class="form-control ws-input" rows="2" required placeholder="اكتب العنوان هنا..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold ws-label">وصف الفرع (مثلاً: فرع من فروع مؤسسة إنسان لخدمة المجتمع)</label>
                            <textarea name="description" class="form-control ws-input" rows="2" placeholder="اكتب وصفاً مختصراً للفرع هنا..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">ساعات العمل</label>
                            <input type="text" name="working_hours" class="form-control ws-input" placeholder="مثلاً: 9 ص - 10 م">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">رابط خرائط جوجل</label>
                            <input type="url" name="google_maps_url" class="form-control ws-input" placeholder="https://goo.gl/maps/...">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" name="is_main" id="isMainSwitch">
                                <label class="form-check-label ms-2" style="color: var(--ws-text-primary);" for="isMainSwitch">تعيين كفرع رئيسي</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 p-4 pt-0">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm">حفظ الفرع</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Edit Branch Modals --}}
    @foreach($branches as $branch)
    <div class="modal fade" id="editBranch{{ $branch->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('website.headquarters.branches.update', $branch) }}" method="POST" class="modal-content glass-card border-0 ws-card ws-card-header">
                @csrf @method('PUT')
                <div class="modal-header border-bottom border-white border-opacity-10 p-4 pb-0">
                    <h5 class="modal-title fw-bold" style="color: var(--ws-text-primary);">تعديل الفرع: {{ $branch->name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">اسم الفرع</label>
                            <input type="text" name="name" class="form-control ws-input" required value="{{ $branch->name }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">حالة الفرع</label>
                            <input type="text" name="status_text" class="form-control ws-input" value="{{ $branch->status_text }}" placeholder="مثلاً: مفتوح الآن، نشط، ...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">رقم الهاتف</label>
                            <input type="text" name="phone" class="form-control ws-input" style="direction: ltr; text-align: left;" value="{{ $branch->phone }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold ws-label">العنوان التفصيلي</label>
                            <textarea name="address" class="form-control ws-input" rows="2" required>{{ $branch->address }}</textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold ws-label">وصف الفرع</label>
                            <textarea name="description" class="form-control ws-input" rows="2">{{ $branch->description }}</textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">ساعات العمل</label>
                            <input type="text" name="working_hours" class="form-control ws-input" value="{{ $branch->working_hours }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold ws-label">رابط خرائط جوجل</label>
                            <input type="url" name="google_maps_url" class="form-control ws-input" value="{{ $branch->google_maps_url }}">
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch custom-switch">
                                <input class="form-check-input" type="checkbox" name="is_main" id="isMainSwitch{{ $branch->id }}" {{ $branch->is_main ? 'checked' : '' }}>
                                <label class="form-check-label ms-2" style="color: var(--ws-text-primary);" for="isMainSwitch{{ $branch->id }}">تعيين كفرع رئيسي</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 p-4 pt-0">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-4 shadow-sm">تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
    @endforeach

<style>.settings-page { min-height: 100vh; }
    
    .display-4 { font-size: 2.5rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    
    .premium-hero-sleek { background: linear-gradient(135deg, var(--ws-border) 0%, var(--ws-bg-card-header) 100%); border-radius: 30px; padding: 60px 0; position: relative; overflow: hidden; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.15; }
    .glow-orb-1 { width: 300px; height: 300px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 250px; height: 250px; bottom: -50px; left: -50px; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); padding: 8px 20px; border-radius: 50px; display: inline-block; border: 1px solid rgba(255,255,255,0.1); font-size: 0.8rem; }
    .x-small { font-size: 0.7rem; }
    
    /* Stats & Cards */
    .glass-card { background: rgba(255,255,255,0.03); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.08); border-radius: 20px; }
.ws-label { color: #94a3b8 !important; }
    .border-secondary { border-color: rgba(255,255,255,0.1) !important; }

    .animate-reveal-right { animation: revealRight 0.8s ease-out forwards; }
    .animate-reveal-left { animation: revealLeft 0.8s ease-out forwards; }
    .animate-slide-up { animation: slideUp 0.8s ease-out forwards; opacity: 0; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-30px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes slideUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    
    .table thead th { font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.5px; border-top: 0; }
    
    /* Custom Dark Select */
    .custom-select-dark {
        background-color: var(--ws-border) !important;
        color: var(--ws-text-primary) !important;
        border-color: #10b981 !important; /* Greenish border as requested */
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e") !important;
    }
    .custom-select-dark:focus {
        background-color: var(--ws-bg-card-header) !important;
        box-shadow: 0 0 0 0.25rem rgba(16, 185, 129, 0.25);
    }
    .custom-select-dark option {
        background-color: var(--ws-bg-card-header) !important;
        color: var(--ws-text-primary);
    }

    .custom-switch .form-check-input { width: 3em; height: 1.5em; cursor: pointer; }
    .custom-switch .form-check-input:checked { background-color: #10b981; border-color: #10b981; }

    /* Modals Overrides */
    .modal-content
.btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }

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





