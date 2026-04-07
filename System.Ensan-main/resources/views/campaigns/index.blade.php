@extends('layouts.app')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Tajawal', sans-serif; }
    .campaigns-page { min-height: 100vh; }

    /* Premium Hero Section */
    .premium-hero {
        position: relative;
        padding: 80px 0 110px;
        background: linear-gradient(135deg, var(--bg-card) 0%, var(--bg-body) 100%);
        border-radius: 0 0 50px 50px;
        overflow: hidden;
        margin-bottom: -40px;
        z-index: 1;
        border-bottom: 1px solid var(--gray-200);
    }
    .hero-glow {
        position: absolute;
        width: 400px; height: 400px;
        background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
        top: -100px; right: -100px;
        filter: blur(60px);
    }

    .glass-card-premium {
        background: var(--bg-glass);
        backdrop-filter: blur(16px);
        border: 1px solid var(--gray-200);
        border-radius: 20px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: var(--shadow-lg);
    }

    /* Campaign Card */
    .campaign-card-lux {
        background: var(--bg-card);
        border-radius: 28px;
        overflow: hidden;
        border: 1px solid var(--gray-200);
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .campaign-card-lux:hover {
        transform: translateY(-8px);
        border-color: rgba(99, 102, 241, 0.3);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
    }

    .card-banner-premium {
        height: 120px;
        position: relative;
        overflow: hidden;
    }
    .status-pill-lux {
        position: absolute;
        top: 15px;
        left: 15px;
        z-index: 5;
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        backdrop-filter: blur(8px);
    }
    .status-active { background: rgba(16, 185, 129, 0.2); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.3); }
    .status-archived { background: rgba(148, 163, 184, 0.2); color: #cbd5e1; border: 1px solid rgba(148, 163, 184, 0.3); }

    .checkbox-wrapper-lux {
        position: absolute;
        top: 15px;
        right: 15px;
        z-index: 10;
        background: rgba(15, 23, 42, 0.6);
        padding: 5px;
        border-radius: 8px;
        backdrop-filter: blur(4px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .checkbox-lux {
        width: 20px; height: 20px;
        cursor: pointer;
        accent-color: #6366f1;
        background-color: transparent !important;
        border-color: rgba(255, 255, 255, 0.3) !important;
    }

    .card-icon-bubble {
        position: absolute;
        bottom: -25px;
        right: 25px;
        width: 60px; height: 60px;
        background: #6366f1;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--shadow-sm);
        border: 4px solid var(--bg-card);
        z-index: 3;
    }

    .content-area-lux { padding: 35px 24px 24px; flex-grow: 1; display: flex; flex-direction: column; }
    .campaign-title-lux { font-size: 1.1rem; font-weight: 800; color: var(--dark); margin-bottom: 8px; line-height: 1.4; }
    .campaign-meta-lux { font-size: 0.8rem; color: var(--gray-600); display: flex; align-items: center; gap: 8px; }

    .btn-action-lux {
        width: 32px; height: 32px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        background: var(--bg-body);
        border: 1px solid var(--gray-200);
        color: #94a3b8;
        transition: all 0.3s ease;
    }
    .btn-action-lux:hover { background: rgba(99, 102, 241, 0.1); border-color: rgba(99, 102, 241, 0.3); color: #818cf8; transform: translateY(-2px); }

    .x-small { font-size: 0.7rem; }
    .fw-800 { font-weight: 800; }
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

@section('content')
<div class="campaigns-page">
    {{-- Hero Section --}}
    <div class="premium-hero">
        <div class="hero-glow"></div>
        <div class="container text-end py-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="badge bg-indigo-500 bg-opacity-10 text-indigo-400 px-3 py-2 rounded-pill x-small fw-800 mb-3 border border-indigo-500 border-opacity-20 animate__animated animate__fadeIn">
                        <i class="bi bi-megaphone-fill me-1"></i> إدارة الحملات التسويقية
                    </div>
                    <h1 class="display-5 fw-800 mb-2 animate__animated animate__fadeInUp">إدارة الحملات</h1>
                    <p class="text-slate-400 lead animate__animated animate__fadeInUp animate__delay-1s">تخطيط وإطلاق الحملات الموسمية والتسويقية باحترافية</p>
                </div>
                <div class="col-md-4 text-start">
                    <a href="{{ route('campaigns.create') }}" class="btn btn-indigo-600 btn-lg rounded-4 px-4 py-3 shadow-lg hover-scale fw-bold animate__animated animate__zoomIn" style="background: #6366f1; color: white; border: none;">
                        <i class="bi bi-plus-lg me-2"></i> إضافة حملة جديدة
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container pb-5" style="position: relative; z-index: 2; margin-top: 120px;">
        {{-- Advanced Filter Collapse (Separate Form) --}}
        <div class="collapse mb-4" id="advancedFilter">
            <div class="glass-card-premium p-4">
                <form method="GET" action="{{ route('campaigns.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="x-small fw-bold text-slate-500 mb-2">اسم الحملة</label>
                            <input name="q" value="{{ $q ?? '' }}" class="form-control bg-slate-900 border-0 text-white rounded-3" placeholder="بحث...">
                        </div>
                        <div class="col-md-3">
                            <label class="x-small fw-bold text-slate-500 mb-2">الحالة</label>
                            <select name="status" class="form-select bg-slate-900 border-0 text-white rounded-3 shadow-none">
                                <option value="">الكل</option>
                                <option value="active" @selected(($status ?? '') === 'active')>نشط</option>
                                <option value="archived" @selected(($status ?? '') === 'archived')>مؤرشف</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="x-small fw-bold text-muted mb-2">السنة</label>
                            <input name="season_year" value="{{ $year ?? '' }}" class="form-control rounded-3" type="number" placeholder="2025">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-indigo-600 w-100 fw-bold rounded-3 shadow-sm border-0 py-2" style="background: #6366f1; color: white;">تطبيق</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Action Form --}}
        <form action="{{ route('campaigns.bulk-destroy') }}" method="POST" id="bulkDeleteForm">
            @csrf
            {{-- Filter & Selection Bar --}}
            <div class="glass-card-premium p-4 mb-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check p-0 m-0 d-flex align-items-center bg-body-tertiary px-3 py-2 rounded-3 border shadow-sm">
                        <input class="form-check-input ms-2 mt-0 checkbox-lux" type="checkbox" id="selectAll">
                        <label class="form-check-label fw-bold small cursor-pointer" for="selectAll" style="margin-right: 5px; white-space: nowrap;">تحديد الكل</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-danger d-none px-4 rounded-3 border-0 transition-all shadow-sm" id="btnBulkDelete" onclick="return confirm('هل أنت متأكد من حذف الحملات المحددة؟')">
                        <i class="bi bi-trash me-1"></i> حذف المحدد
                    </button>
                    
                    {{-- Quick Search (Minimal - JS filtered) --}}
                    <div class="input-group input-group-sm d-none d-md-flex" style="width: 250px;">
                        <input type="text" class="form-control bg-body-secondary border-0 px-3 rounded-start-3" placeholder="بحث سريع..." onkeyup="filterCampaigns(this)">
                        <span class="input-group-text bg-body-secondary border-0 text-muted rounded-end-3"><i class="bi bi-search"></i></span>
                    </div>
                </div>
                
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-3 px-3" data-bs-toggle="collapse" data-bs-target="#advancedFilter">
                        <i class="bi bi-funnel"></i> تصفية متقدمة
                    </button>
                </div>
            </div>

            {{-- Campaign Grid --}}
            <div class="row g-4" id="campaignGrid">
                @foreach($campaigns as $c)
                <div class="col-md-4 col-lg-4 campaign-item">
                    <div class="campaign-card-lux">
                        {{-- Banner Area --}}
                        <div class="card-banner-premium" style="background: linear-gradient(135deg, {{ $c->status === 'active' ? '#065f46, #059669' : '#334155, #475569' }});">
                            <div class="status-pill-lux {{ $c->status === 'active' ? 'status-active' : 'status-archived' }}">
                                <i class="bi {{ $c->status === 'active' ? 'bi-record-circle-fill' : 'bi-archive-fill' }} me-1"></i>
                                {{ $c->status === 'active' ? 'نشطة حالياً' : 'مؤرشفة' }}
                            </div>

                            <div class="checkbox-wrapper-lux">
                                <input class="form-check-input checkbox-lux record-checkbox" type="checkbox" name="ids[]" value="{{ $c->id }}">
                            </div>

                            <div class="card-icon-bubble">
                                <i class="bi bi-megaphone-fill fs-4 text-white"></i>
                            </div>

                            {{-- Abstract Patterns --}}
                            <div style="position: absolute; inset: 0; background: url('data:image/svg+xml,%3Csvg width=\"20\" height=\"20\" viewBox=\"0 0 20 20\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23ffffff\" fill-opacity=\"0.05\" fill-rule=\"evenodd\"%3E%3Ccircle cx=\"3\" cy=\"3\" r=\"3\"/%3E%3C/g%3E%3C/svg%3E');"></div>
                        </div>

                        {{-- Content Area --}}
                        <div class="content-area-lux">
                            <div class="mb-3">
                                <h5 class="campaign-title-lux">
                                    <a href="{{ route('campaigns.show', $c) }}" class="text-white text-decoration-none hover-indigo">{{ $c->name }}</a>
                                </h5>
                                <div class="campaign-meta-lux">
                                    <i class="bi bi-calendar3 text-indigo-400"></i>
                                    <span>موسم {{ $c->season_year }}</span>
                                    @if($c->project)
                                        <span class="mx-1">•</span>
                                        <i class="bi bi-folder2-open text-indigo-400"></i>
                                        <span class="text-truncate" style="max-width: 120px;">{{ $c->project->name }}</span>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-auto d-flex justify-content-between align-items-center pt-3 border-top border-white border-opacity-5">
                                <div class="d-flex gap-3">
                                    <div class="x-small">
                                        <div class="text-slate-500 mb-1">تاريخ البدء</div>
                                        <div class="text-white fw-bold">{{ $c->start_date?->format('d/m/Y') ?? '—' }}</div>
                                    </div>
                                    <div class="x-small">
                                        <div class="text-slate-500 mb-1">تاريخ الانتهاء</div>
                                        <div class="text-white fw-bold">{{ $c->end_date?->format('d/m/Y') ?? '—' }}</div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2" style="position: relative; z-index: 10;">
                                    <div class="dropdown">
                                        <button type="button" class="btn-action-lux" data-bs-toggle="dropdown">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-start bg-slate-900 border-white border-opacity-10 shadow-lg rounded-3">
                                            <li><a class="dropdown-item small py-2" href="{{ route('campaigns.show', $c) }}"><i class="bi bi-eye me-2 text-indigo-400"></i> عرض التفاصيل</a></li>
                                            @if(auth()->check())
                                            <li><button type="button" class="dropdown-item small py-2" data-bs-toggle="modal" data-bs-target="#editCampaignModal{{ $c->id }}"><i class="bi bi-pencil me-2 text-warning"></i> طلب تعديل</button></li>
                                            <li><button type="button" class="dropdown-item small py-2 text-danger" onclick="openCancelModal('{{ route('campaigns.destroy', $c) }}')"><i class="bi bi-trash me-2"></i> حذف الحملة</button></li>
                                            @endif
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-4 px-2">{{ $campaigns->links() }}</div>
        </form>
    </div>
</div>

{{-- Premium Modals --}}

{{-- Cancel Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form id="cancelForm" method="POST" class="modal-content glass-card-premium border-0 shadow-2xl overflow-hidden">
            @csrf
            @method('DELETE')
            <div class="modal-header border-bottom border-white border-opacity-5 bg-slate-900 p-4">
                <h5 class="modal-title fw-bold text-danger text-end w-100"><i class="bi bi-exclamation-triangle-fill me-2"></i> تأكيد الحذف</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-900 bg-opacity-50 text-center">
                <div class="mb-4">
                    <i class="bi bi-trash-fill text-danger display-1 opacity-20"></i>
                </div>
                <h5 class="text-white mb-2">هل أنت متأكد من حذف هذه الحملة؟</h5>
                <p class="text-slate-400">لا يمكن التراجع عن هذا الإجراء بعد التنفيذ.</p>
            </div>
            <div class="modal-footer border-top border-white border-opacity-5 bg-slate-900 p-3">
                <button type="button" class="btn btn-link text-slate-500 text-decoration-none fw-bold" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-danger rounded-pill px-5 fw-bold shadow-sm">تأكيد الحذف النهائي</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modals Loop --}}
@foreach($campaigns as $c)
<div class="modal fade" id="editCampaignModal{{ $c->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('campaigns.update', $c) }}" method="POST" class="modal-content glass-card-premium border-0 shadow-2xl overflow-hidden">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom border-white border-opacity-5 bg-slate-900 p-4">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-pencil-square me-2 text-warning"></i> تعديل الحملة: {{ $c->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-slate-900 bg-opacity-50">
                {{-- Validation Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger bg-danger bg-opacity-10 border-danger border-opacity-20 text-danger rounded-4 px-4 py-3 mb-4">
                        <ul class="mb-0 small fw-bold">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="x-small fw-bold text-slate-500 mb-2 text-uppercase">اسم الحملة</label>
                        <input type="text" name="name" class="form-control bg-slate-900 border-0 text-white rounded-4 p-3 shadow-none fw-bold" value="{{ $c->name }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="x-small fw-bold text-slate-500 mb-2 text-uppercase">سنة الموسم</label>
                        <input type="number" name="season_year" class="form-control bg-slate-900 border-0 text-white rounded-4 p-3 shadow-none text-center fw-bold" value="{{ $c->season_year }}" required>
                    </div>
                    <div class="col-md-3">
                        <label class="x-small fw-bold text-slate-500 mb-2 text-uppercase">حالة الحملة</label>
                        <select name="status" class="form-select bg-slate-900 border-0 text-white rounded-4 p-3 shadow-none fw-bold">
                            <option value="active" @selected($c->status === 'active')>نشط</option>
                            <option value="archived" @selected($c->status === 'archived')>مؤرشف</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="x-small fw-bold text-slate-500 mb-2 text-uppercase">تاريخ البدء</label>
                        <input type="date" name="start_date" class="form-control bg-slate-900 border-0 text-white rounded-4 p-3 shadow-none" value="{{ $c->start_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-md-6">
                        <label class="x-small fw-bold text-slate-500 mb-2 text-uppercase">تاريخ الانتهاء</label>
                        <input type="date" name="end_date" class="form-control bg-slate-900 border-0 text-white rounded-4 p-3 shadow-none" value="{{ $c->end_date?->format('Y-m-d') }}">
                    </div>
                    <div class="col-12">
                        <label class="x-small fw-bold text-slate-500 mb-2 text-uppercase">المشروع التابع له</label>
                        <select name="project_id" class="form-select bg-slate-900 border-0 text-white rounded-4 p-3 shadow-none fw-bold">
                            <option value="">— اختر المشروع —</option>
                            @foreach(\App\Models\Project::orderBy('name')->get() as $p)
                                <option value="{{ $p->id }}" @selected($c->project_id == $p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top border-white border-opacity-5 bg-slate-900 p-3">
                <button type="button" class="btn btn-link text-slate-500 text-decoration-none fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-indigo-600 rounded-pill px-5 py-2 fw-bold shadow-sm" style="background: #6366f1; color: white; border: none;">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

@endsection

@section('scripts')
<script>
    function openCancelModal(actionUrl) {
        document.getElementById('cancelForm').action = actionUrl;
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    function filterCampaigns(input) {
        const value = input.value.toLowerCase();
        const items = document.querySelectorAll('.campaign-item');
        items.forEach(item => {
            const title = item.querySelector('.campaign-title-lux').textContent.toLowerCase();
            item.style.display = title.includes(value) ? '' : 'none';
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.record-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');

        function toggleBulkDeleteButton() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (anyChecked) {
                btnBulkDelete.classList.remove('d-none');
                btnBulkDelete.classList.add('animate__animated', 'animate__fadeInLeft');
            } else {
                btnBulkDelete.classList.add('d-none');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                toggleBulkDeleteButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkDeleteButton);
        });
    });
</script>
@endsection

