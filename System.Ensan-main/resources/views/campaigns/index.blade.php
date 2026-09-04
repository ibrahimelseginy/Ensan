@extends('layouts.app')

@section('styles')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;700;800&display=swap" rel="stylesheet">
<style>
    body { font-family: 'Tajawal', sans-serif; }
    .campaigns-page { min-height: 100vh; }

    /* Campaign Card */
    .campaign-card-lux {
        background: #ffffff;
        border-radius: 24px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
        transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
    }
    .theme-dark .campaign-card-lux {
        background: #131c2e !important;
        border-color: rgba(255, 255, 255, 0.12) !important;
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35) !important;
    }
    .campaign-card-lux:hover {
        transform: translateY(-6px);
        box-shadow: 0 16px 36px rgba(0, 0, 0, 0.18) !important;
        border-color: #6366f1 !important;
    }

    .card-banner-active {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }
    .card-banner-archived {
        background: linear-gradient(135deg, #475569 0%, #334155 100%) !important;
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .campaign-banner-title {
        color: #ffffff !important;
        font-size: 1.2rem;
        font-weight: 800;
        margin-bottom: 0.25rem;
        line-height: 1.4;
    }
    .campaign-banner-season {
        color: rgba(255, 255, 255, 0.85) !important;
        font-size: 0.82rem;
        font-weight: 600;
    }

    .campaign-badge-active {
        background: rgba(255, 255, 255, 0.22) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.35) !important;
        backdrop-filter: blur(8px);
        font-weight: 700;
        font-size: 0.76rem;
    }
    .campaign-badge-archived {
        background: rgba(255, 255, 255, 0.15) !important;
        color: #e2e8f0 !important;
        border: 1px solid rgba(255, 255, 255, 0.25) !important;
        backdrop-filter: blur(8px);
        font-weight: 700;
        font-size: 0.76rem;
    }

    .campaign-date-box {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 0.65rem 0.5rem;
        text-align: center;
    }
    .theme-dark .campaign-date-box {
        background: #1e293b !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    .campaign-date-label {
        font-size: 0.72rem;
        font-weight: 700;
        color: #64748b;
        margin-bottom: 0.2rem;
    }
    .theme-dark .campaign-date-label {
        color: #94a3b8 !important;
    }
    .campaign-date-value {
        font-size: 0.88rem;
        font-weight: 800;
        color: #0f172a;
    }
    .theme-dark .campaign-date-value {
        color: #f8fafc !important;
    }

    .project-pill-tag {
        background: rgba(99, 102, 241, 0.1);
        color: #4f46e5;
        border: 1px solid rgba(99, 102, 241, 0.2);
        border-radius: 12px;
        padding: 0.4rem 0.8rem;
        font-size: 0.82rem;
        font-weight: 700;
    }
    .theme-dark .project-pill-tag {
        background: rgba(99, 102, 241, 0.2) !important;
        color: #a5b4fc !important;
        border-color: rgba(99, 102, 241, 0.3) !important;
    }

    .btn-campaign-details {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff !important;
        border: none;
        border-radius: 999px;
        padding: 0.45rem 1.25rem;
        font-weight: 700;
        font-size: 0.85rem;
        box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
        transition: all 0.2s ease;
    }
    .btn-campaign-details:hover {
        background: linear-gradient(135deg, #059669 0%, #047857 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.5);
    }
</style>
@endsection

@section('content')
<div class="campaign-system-container animate-fade-in">
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up bg-primary shadow-sm mb-4" style="background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%); padding: 2.8rem 2rem; border-radius: 0 0 36px 36px;">
        <div class="hero-content">
            <div class="hero-greeting text-white mb-2 opacity-90 fw-bold">الحملات والمبادرات 📣</div>
            <h1 class="hero-title fw-bold text-white mb-2" style="color: #ffffff !important;">إدارة الحملات</h1>
            <p class="hero-subtitle text-white opacity-85 mb-4" style="color: #ffffff !important;">تخطيط وإطلاق الحملات الموسمية والتسويقية باحترافية لزيادة التأثير والوصول.</p>
            <div class="hero-actions d-flex gap-2">
                <a href="{{ route('campaigns.create') }}" class="btn btn-sm rounded-pill px-4 btn-light fw-bold hover-lift shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> إضافة حملة جديدة
                </a>
                <button type="button" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift shadow-sm" style="border-width: 2px;" data-bs-toggle="collapse" data-bs-target="#advancedFilter">
                    <i class="bi bi-funnel me-1"></i> تصفية متقدمة
                </button>
            </div>
        </div>
        <i class="bi bi-megaphone hero-icon text-white opacity-20 d-none d-md-block" style="font-size: 8rem; position: absolute; left: 5%; top: 50%; transform: translateY(-50%) rotate(-15deg);"></i>
    </div>

    <div class="container-fluid px-4 pb-5">
        {{-- Advanced Filter Collapse --}}
        <div class="collapse mb-4" id="advancedFilter">
            <div class="card border p-4 rounded-4 shadow-sm">
                <form method="GET" action="{{ route('campaigns.index') }}">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="x-small fw-bold text-muted mb-2">اسم الحملة</label>
                            <input name="q" value="{{ $q ?? '' }}" class="form-control rounded-3" placeholder="بحث باسم الحملة...">
                        </div>
                        <div class="col-md-3">
                            <label class="x-small fw-bold text-muted mb-2">الحالة</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="">الكل</option>
                                <option value="active" @selected(($status ?? '') === 'active')>نشط</option>
                                <option value="archived" @selected(($status ?? '') === 'archived')>مؤرشف</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="x-small fw-bold text-muted mb-2">السنة</label>
                            <input name="season_year" value="{{ $season_year ?? '' }}" class="form-control rounded-3" type="number" placeholder="2025">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">تطبيق الفلتر</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Bulk Action & Quick Search --}}
        <form action="{{ route('campaigns.bulk-destroy') }}" method="POST" id="bulkDeleteForm">
            @csrf
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="form-check p-0 m-0 d-flex align-items-center bg-body-tertiary px-3 py-2 rounded-3 border shadow-sm">
                        <input class="form-check-input ms-2 mt-0" type="checkbox" id="selectAll" style="width: 18px; height: 18px;">
                        <label class="form-check-label fw-bold small mb-0" for="selectAll" style="cursor: pointer;">تحديد الكل</label>
                    </div>
                    <button type="submit" class="btn btn-sm btn-danger d-none px-4 rounded-pill border-0 shadow-sm" id="btnBulkDelete" onclick="return confirm('هل أنت متأكد من حذف الحملات المحددة؟')">
                        <i class="bi bi-trash me-1"></i> حذف المحدد
                    </button>
                </div>
                
                <div class="input-group shadow-sm" style="width: 300px;">
                    <span class="input-group-text bg-body border-end-0 rounded-start-pill"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" class="form-control border-start-0 rounded-end-pill" placeholder="بحث سريع في النتائج..." onkeyup="filterCampaigns(this)">
                </div>
            </div>

            {{-- Campaign Grid --}}
            <div class="row g-4" id="campaignGrid">
                @foreach($campaigns as $c)
                <div class="col-md-6 col-xl-4 campaign-item">
                    <div class="campaign-card-lux">
                        {{-- Card Header/Banner --}}
                        <div class="{{ $c->status === 'active' ? 'card-banner-active' : 'card-banner-archived' }}">
                            <div class="d-flex justify-content-between align-items-start position-relative" style="z-index: 2;">
                                <span class="badge rounded-pill px-3 py-2 {{ $c->status === 'active' ? 'campaign-badge-active' : 'campaign-badge-archived' }}">
                                    <i class="bi {{ $c->status === 'active' ? 'bi-record-circle-fill' : 'bi-archive-fill' }} me-1"></i>
                                    {{ $c->status === 'active' ? 'نشطة حالياً' : 'مؤرشفة' }}
                                </span>
                                <input class="form-check-input record-checkbox shadow-none" type="checkbox" name="ids[]" value="{{ $c->id }}" style="width: 20px; height: 20px; border-color: rgba(255,255,255,0.4);">
                            </div>
                            
                            <div class="mt-3 text-white">
                                <h5 class="campaign-banner-title">
                                    <a href="{{ route('campaigns.show', $c) }}" class="text-white text-decoration-none" style="color: #ffffff !important;">{{ $c->name }}</a>
                                </h5>
                                <div class="campaign-banner-season">موسم {{ $c->season_year }}</div>
                            </div>
                            
                            {{-- Decorative Icon --}}
                            <i class="bi bi-megaphone-fill position-absolute text-white opacity-10" style="font-size: 5rem; left: -10px; bottom: -20px; transform: rotate(-15deg);"></i>
                        </div>

                        {{-- Card Body --}}
                        <div class="p-4 d-flex flex-column flex-grow-1 justify-content-between">
                            @if($c->project)
                            <div class="d-flex align-items-center gap-2 mb-3">
                                <div class="project-pill-tag text-truncate" style="max-width: 100%;">
                                    <i class="bi bi-folder2-open me-1"></i> {{ $c->project->name }}
                                </div>
                            </div>
                            @endif

                            <div class="row g-2 mb-4">
                                <div class="col-6">
                                    <div class="campaign-date-box">
                                        <div class="campaign-date-label">تاريخ البدء</div>
                                        <div class="campaign-date-value">{{ $c->start_date?->format('Y/m/d') ?? '—' }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="campaign-date-box">
                                        <div class="campaign-date-label">تاريخ الانتهاء</div>
                                        <div class="campaign-date-value">{{ $c->end_date?->format('Y/m/d') ?? '—' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-3 border-top">
                                <a href="{{ route('campaigns.show', $c) }}" class="btn btn-campaign-details">
                                    التفاصيل <i class="bi bi-arrow-left ms-1"></i>
                                </a>
                                
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle shadow-sm border" type="button" data-bs-toggle="dropdown" style="width: 36px; height: 36px;">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-3">
                                        <li><button type="button" class="dropdown-item py-2" data-bs-toggle="modal" data-bs-target="#editCampaignModal{{ $c->id }}"><i class="bi bi-pencil me-2 text-warning"></i> تعديل</button></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li><button type="button" class="dropdown-item py-2 text-danger" onclick="openCancelModal('{{ route('campaigns.destroy', $c) }}')"><i class="bi bi-trash me-2"></i> حذف نهائي</button></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="mt-5 d-flex justify-content-center">
                {{ $campaigns->links() }}
            </div>
        </form>
    </div>
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
            const title = item.querySelector('.campaign-title-lux')?.textContent.toLowerCase() ?? '';
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

