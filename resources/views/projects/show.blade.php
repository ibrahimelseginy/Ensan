@extends('layouts.app')
@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        #beneficiaryForm .select2-container { width: 100% !important; direction: rtl; }
        #beneficiaryForm .select2-container--bootstrap-5 .select2-selection--multiple { min-height: 42px; }
        #beneficiaryForm .select2-container--bootstrap-5 .select2-selection__choice {
            background: var(--primary) !important;
            color: #fff !important;
            border: 0 !important;
        }
    </style>
@endsection
@section('content')
    <style>
        .project-table-container {
            border: 1px solid var(--border, #e2e8f0);
            border-radius: 12px;
            overflow: hidden;
            background: var(--card-bg, #ffffff);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
        }
        .project-table-scroll {
            overflow-x: auto;
            max-height: 560px;
            scrollbar-width: thin;
        }
        .project-table-scroll::-webkit-scrollbar {
            height: 6px;
            width: 6px;
        }
        .project-table-scroll::-webkit-scrollbar-thumb {
            background: rgba(148, 163, 184, 0.4);
            border-radius: 4px;
        }
        .table-project-beneficiaries {
            min-width: 1080px;
            margin-bottom: 0 !important;
        }
        .table-project-beneficiaries thead th {
            background-color: var(--bg-soft, #f8fafc) !important;
            color: var(--text-muted, #64748b) !important;
            font-size: 0.8rem !important;
            font-weight: 800 !important;
            text-transform: uppercase;
            letter-spacing: 0.3px;
            border-bottom: 1.5px solid var(--border, #e2e8f0) !important;
            padding: 12px 14px !important;
            white-space: nowrap;
        }
        .table-project-beneficiaries tbody tr {
            transition: background-color 0.15s ease;
        }
        .table-project-beneficiaries tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.04) !important;
        }
        .table-project-beneficiaries tbody td {
            padding: 12px 14px !important;
            border-bottom: 1px solid var(--border, #f1f5f9) !important;
            vertical-align: middle;
            font-size: 0.86rem;
        }
        .theme-dark .project-table-container {
            border-color: rgba(255, 255, 255, 0.08) !important;
            background: #111827 !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.25);
        }
        .theme-dark .table-project-beneficiaries thead th {
            background-color: #1e293b !important;
            color: #94a3b8 !important;
            border-bottom-color: rgba(255, 255, 255, 0.1) !important;
        }
        .theme-dark .table-project-beneficiaries tbody td {
            border-bottom-color: rgba(255, 255, 255, 0.06) !important;
            color: #e2e8f0 !important;
        }
        .theme-dark .table-project-beneficiaries tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.04) !important;
        }
        .theme-dark .table-project-beneficiaries .text-dark {
            color: #f8fafc !important;
        }
        .theme-dark .table-project-beneficiaries .badge.bg-light {
            background-color: #334155 !important;
            color: #f1f5f9 !important;
            border-color: #475569 !important;
        }
        .action-btn-circle {
            width: 32px;
            height: 32px;
            border-radius: 50% !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
            padding: 0 !important;
            transition: all 0.2s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .action-btn-circle:hover {
            transform: scale(1.12);
        }

        .gh-metric-card {
            background: #fff;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: transform 0.2s;
        }

        .gh-metric-card:hover {
            transform: translateY(-5px);
        }

        .gh-metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .gh-section-title {
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            transform: translateY(-2px);
        }

        .transition {
            transition: all 0.3s ease;
        }

        .theme-dark .gh-metric-card {
            background: var(--card-bg);
            color: var(--text);
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

      /* --- CHILD-FRIENDLY JOYFUL THEME FOR ZAD CHILDREN --- */
      .zad-children-card-container {
          border: 2px solid #fed7aa;
          border-radius: 1rem;
          overflow: hidden;
          background: var(--card-bg, #ffffff);
          box-shadow: 0 10px 25px rgba(245, 158, 11, 0.07);
      }
      .zad-children-header-bg {
          background: linear-gradient(135deg, rgba(254, 243, 199, 0.7) 0%, rgba(255, 237, 213, 0.6) 50%, rgba(224, 242, 254, 0.5) 100%);
          border-bottom: 2px dashed #fcd34d;
          padding: 1.25rem 1.5rem;
          position: relative;
          overflow: hidden;
      }
      .child-doodle-pattern {
          background-color: #fafaf9;
          background-image: radial-gradient(#f59e0b 0.65px, transparent 0.65px), radial-gradient(#0ea5e9 0.65px, #fafaf9 0.65px);
          background-size: 28px 28px;
          background-position: 0 0, 14px 14px;
      }
      @keyframes floatKidIcon {
          0%, 100% { transform: translateY(0px) rotate(0deg); }
          50% { transform: translateY(-6px) rotate(4deg); }
      }
      .floating-kid-icon {
          display: inline-block;
          animation: floatKidIcon 3s ease-in-out infinite;
      }
      .child-avatar-circle {
          width: 44px;
          height: 44px;
          border-radius: 50%;
          display: flex;
          align-items: center;
          justify-content: center;
          font-size: 1.35rem;
          background: linear-gradient(135deg, #fef08a 0%, #fde047 100%);
          border: 2px solid #fff;
          box-shadow: 0 3px 8px rgba(234, 179, 8, 0.2);
          flex-shrink: 0;
      }
</style>

@php
    $isZadProject = (trim($project->name) === 'مشروع زاد' || str_contains($project->name, 'زاد'));
@endphp

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                style="width:64px;height:64px">
                <i class="bi bi-kanban" style="font-size:1.6rem"></i>
            </div>
            <div>
                <h4 class="mb-1 fw-bold">{{ $project->name }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span
                        class="badge {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $project->status === 'active' ? 'نشط' : 'مؤرشف' }}</span>
                    <span
                        class="badge {{ $project->fixed ? 'bg-info' : 'bg-secondary-subtle' }}">{{ $project->fixed ? 'ثابت' : 'غير ثابت' }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap gap-2">
            @if($isZadProject)
                <button type="button" class="btn btn-warning fw-bold text-dark shadow-sm d-flex align-items-center gap-1.5 px-3 rounded-pill" onclick="activateZadChildrenTab();">
                    <span class="fs-5 floating-kid-icon">🎈</span>
                    <span>قائمة الأطفال المكفولين</span>
                    <span class="badge bg-dark text-white rounded-pill ms-1">{{ isset($zadChildren) ? $zadChildren->count() : 0 }}</span>
                </button>
            @endif
            @if(request()->user()?->hasPermission('donations.view'))
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#projectDonationModal">
                    <i class="bi bi-cash-coin"></i> إضافة تبرع
                </button>
            @endif
            @if(request()->user()?->hasPermission('manage_finance') && request()->user()?->hasPermission('expenses.view'))
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#projectExpenseModal">
                    <i class="bi bi-receipt"></i> إضافة مصروف
                </button>
            @endif
            <a class="btn btn-outline-primary" href="{{ route('projects.edit', $project) }}"><i class="bi bi-pencil"></i>
                تعديل</a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right"></i>
                عودة</a>
        </div>
    </div>

    <div class="text-muted mb-4">{{ $project->description }}</div>

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="text-muted small">اجمالي التبرعات</div>
                <h3 class="fw-bold mb-0">{{ number_format($donationsTotal, 2) }}</h3>
                <div class="small text-success mt-1">
                    <i class="bi bi-arrow-up"></i> {{ $donationsCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="text-muted small">إيرادات الأنشطة</div>
                <h3 class="fw-bold mb-0">{{ number_format($activitiesRevenue, 2) }}</h3>
                <div class="small text-warning mt-1">
                    من المعارض والدعاية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="text-muted small">اجمالي المصروفات</div>
                <h3 class="fw-bold mb-0">{{ number_format($expensesTotal, 2) }}</h3>
                <div class="small text-danger mt-1">
                    <i class="bi bi-arrow-down"></i> {{ $expensesCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="text-muted small">صافي الحسابات</div>
                <h3 class="fw-bold mb-0 {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($netBalance, 2) }}</h3>
                <div class="small text-muted mt-1">
                    الرصيد الحالي
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="text-muted small">المستفيدون</div>
                <h3 class="fw-bold mb-0">{{ $beneficiariesCount }}</h3>
                <div class="small text-muted mt-1">
                    مستفيد مسجل
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Main Content -->
        <div class="col-lg-8">

            <!-- Campaigns Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-flag text-primary me-2"></i> حملات المشروع</span>
                        <a href="{{ route('campaigns.create') }}" class="btn btn-sm btn-outline-primary">إضافة حملة</a>
                    </div>
                    @if($project->campaigns->isEmpty())
                        <div class="text-muted small text-center py-3">لا توجد حملات مرتبطة بهذا المشروع.</div>
                    @else
                        <div class="row g-3">
                            @foreach($project->campaigns as $pc)
                                <div class="col-md-6">
                                    <div class="card h-100 shadow-sm border-0 bg-body-tertiary">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0">
                                                    <a href="{{ route('campaigns.show', $pc) }}"
                                                        class="text-decoration-none text-body">
                                                        {{ $pc->name }} <span
                                                            class="text-muted small">({{ $pc->season_year }})</span>
                                                    </a>
                                                </h6>
                                                <span
                                                    class="badge {{ $pc->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $pc->status == 'active' ? 'نشط' : 'مؤرشف' }}</span>
                                            </div>
                                            <div class="small text-muted mb-3">
                                                من {{ $pc->start_date?->format('Y-m-d') ?? '—' }} إلى
                                                {{ $pc->end_date?->format('Y-m-d') ?? '—' }}
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end border-top pt-2 mt-auto">
                                                <form method="POST" action="{{ route('campaigns.destroy', $pc) }}"
                                                    class="d-inline"
                                                    data-confirm-delete
                                                    data-confirm-title="تأكيد حذف الحملة"
                                                    data-name="{{ $pc->title }}">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-link text-danger btn-sm p-0">حذف</button>
                                                </form>
                                                <span class="text-muted">|</span>
                                                <a href="{{ route('campaigns.edit', $pc) }}"
                                                    class="btn btn-link text-secondary btn-sm p-0">تعديل</a>
                                                <span class="text-muted">|</span>
                                                <a href="{{ route('campaigns.show', $pc) }}"
                                                    class="btn btn-link text-primary btn-sm p-0">عرض</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>



            @php
                $isZadProject = (trim($project->name) === 'مشروع زاد' || str_contains($project->name, 'زاد'));
                $genericBeneficiaries = $project->beneficiaries()
                    ->with(['allocatedBeneficiaries:id,full_name', 'sponsors:id,name'])
                    ->orderByDesc('id')
                    ->limit(200)
                    ->get();
                $zadFamilies = $isZadProject 
                    ? $project->beneficiaries()->whereNotNull('mother_name')->orderByDesc('id')->limit(50)->get() 
                    : collect();
            @endphp

            @if($isZadProject)
                {{-- Navigation Pills for Zad Project --}}
                <ul class="nav nav-pills mb-3 gap-2 p-1.5 bg-body-tertiary rounded-3 border" id="zadProjectTabs" role="tablist" style="width: fit-content;">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-2" id="project-beneficiaries-tab" data-bs-toggle="pill" data-bs-target="#project-beneficiaries-pane" type="button" role="tab">
                            <i class="bi bi-people-fill text-primary"></i>
                            <span>ملف المستفيدين</span>
                            <span class="badge bg-primary text-white rounded-pill px-2">{{ $genericBeneficiaries->count() }}</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-2" id="zad-families-tab" data-bs-toggle="pill" data-bs-target="#zad-families-pane" type="button" role="tab">
                            <i class="bi bi-basket2-fill text-success"></i>
                            <span>ملف أهالي زاد (الاستمارات المخصصة)</span>
                            @if($zadFamilies->count() > 0)
                                <span class="badge bg-success text-white rounded-pill px-2">{{ $zadFamilies->count() }}</span>
                            @endif
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-3 py-1.5 fw-bold d-flex align-items-center gap-2" id="zad-children-tab" data-bs-toggle="pill" data-bs-target="#zad-children-pane" type="button" role="tab">
                            <i class="bi bi-balloon-heart-fill text-warning"></i>
                            <span>قائمة الأطفال المكفولين والتقييمات</span>
                            @if(isset($zadChildren) && $zadChildren->count() > 0)
                                <span class="badge bg-warning text-dark rounded-pill px-2">{{ $zadChildren->count() }}</span>
                            @endif
                        </button>
                    </li>
                </ul>

                <div class="tab-content" id="zadProjectTabsContent">
                    <div class="tab-pane fade show active" id="project-beneficiaries-pane" role="tabpanel">
            @endif

            <!-- Generic Beneficiaries Section -->
            <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center gap-2">
                            <span class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3" style="width: 40px; height: 40px;">
                                <i class="bi bi-people-fill fs-5"></i>
                            </span>
                            <div>
                                <h5 class="fw-bold mb-0 text-dark">ملف المستفيدين</h5>
                                <small class="text-muted">المستفيدين المسجلين تحت هذا المشروع</small>
                            </div>
                            <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-1.5 ms-2 fw-bold" style="font-size: 0.82rem;">
                                {{ $genericBeneficiaries->count() }} مستفيد
                            </span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <button class="btn btn-primary btn-sm px-3 py-1.5 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-1.5" type="button" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">
                                <i class="bi bi-person-plus-fill"></i>
                                <span>إضافة مستفيد</span>
                            </button>
                        </div>
                    </div>

                        {{-- Search Filter Bar --}}
                        <div class="row g-2 mb-3 align-items-center">
                            <div class="col-md-7 col-lg-6">
                                <div class="position-relative">
                                    <input type="text" id="filterBeneficiariesTable" class="form-control form-control-sm pe-5 rounded-pill" placeholder="بحث فوري في المستفيدين (بالاسم، الكود، الرقم القومي، الهاتف)...">
                                    <i class="bi bi-search position-absolute top-50 end-0 translate-middle-y me-3 text-muted"></i>
                                </div>
                            </div>
                            <div class="col-md-5 col-lg-6 text-md-start text-muted small">
                                <span>عرض <strong id="visibleBenCount" class="text-primary">{{ $genericBeneficiaries->count() }}</strong> من <strong class="text-dark">{{ $genericBeneficiaries->count() }}</strong> مستفيد</span>
                            </div>
                        </div>
                        
                        <div @class(['collapse mb-4', 'show' => old('form_context') === 'project_beneficiary']) id="beneficiaryForm">
                            <form method="POST" action="{{ route('projects.storeBeneficiaryFile', $project) }}" class="bg-body-tertiary p-4 rounded shadow-sm">
                                @csrf
                                <input type="hidden" name="form_context" value="project_beneficiary">

                                <div class="form-section mb-4">
                                    <div class="form-section-title">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <span>البيانات الشخصية</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">كود المستفيد</label>
                                            <input name="code" class="form-control @error('code') is-invalid @enderror"
                                                value="{{ old('code') }}"
                                                placeholder="مثال: A-101 (يترك فارغاً للتوليد التلقائي)">
                                            <div class="form-help-text">يترك فارغاً وسيتم إنشاؤه تلقائياً</div>
                                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label form-label-required">الاسم الكامل</label>
                                            <input name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                                value="{{ old('full_name') }}" required placeholder="أدخل الاسم بالعربية">
                                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">الرقم القومي</label>
                                            <input name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                                value="{{ old('national_id') }}"
                                                pattern="^[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{7}$"
                                                title="الرقم القومي 14 رقم بصيغة صحيحة" placeholder="14 رقم">
                                            <div class="form-help-text">الرقم القومي المصري المكون من 14 رقم</div>
                                            @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">رقم الهاتف</label>
                                            <input name="phone" class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone') }}" pattern="^(01[0125]\d{8}|\+?201[0125]\d{8})$"
                                                title="ابدأ بـ 010 أو 011 أو 012 أو 015" placeholder="01xxxxxxxxx">
                                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">العنوان</label>
                                            <input name="address" class="form-control @error('address') is-invalid @enderror"
                                                value="{{ old('address') }}" placeholder="العنوان بالتفصيل">
                                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $selectedAllocatedBeneficiaries = array_map('intval', old('allocated_beneficiary_ids', []));
                                    $selectedSponsors = array_map('intval', old('sponsor_ids', []));
                                @endphp
                                <div class="form-section mb-4">
                                    <div class="form-section-title">
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span>نوع المساعدة والتخصيص</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label form-label-required">نوع المساعدة</label>
                                            <select name="assistance_type" class="form-select @error('assistance_type') is-invalid @enderror" required>
                                                <option value="financial" @selected(old('assistance_type', 'financial') === 'financial')>مالية</option>
                                                <option value="in_kind" @selected(old('assistance_type') === 'in_kind')>عينية</option>
                                                <option value="service" @selected(old('assistance_type') === 'service')>خدمية</option>
                                            </select>
                                            @error('assistance_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">نوع التخصيص</label>
                                            <select id="project-allocation-type" name="allocation_type" class="form-select @error('allocation_type') is-invalid @enderror">
                                                <option value="">— اختر النوع —</option>
                                                <option value="شخص واحد" @selected(old('allocation_type') === 'شخص واحد')>شخص واحد</option>
                                                <option value="أكثر من مستفيد" @selected(old('allocation_type') === 'أكثر من مستفيد')>أكثر من مستفيد</option>
                                            </select>
                                            @error('allocation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">التخصيص لكل طفل</label>
                                            <select id="project-child-sponsorship-type" name="child_sponsorship_type" class="form-select @error('child_sponsorship_type') is-invalid @enderror">
                                                <option value="">— اختر النوع —</option>
                                                <option value="كافل واحد" @selected(old('child_sponsorship_type') === 'كافل واحد')>كافل واحد</option>
                                                <option value="أكثر من كافل" @selected(old('child_sponsorship_type') === 'أكثر من كافل')>أكثر من كافل</option>
                                            </select>
                                            @error('child_sponsorship_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div id="project-allocated-beneficiaries-field"
                                            class="col-md-6 {{ old('allocation_type') ? '' : 'd-none' }}">
                                            <label id="project-allocated-beneficiaries-label" class="form-label">اختر المستفيد / المستفيدين</label>
                                            <select id="project-allocated-beneficiaries" name="allocated_beneficiary_ids[]"
                                                class="form-select @error('allocated_beneficiary_ids') is-invalid @enderror" multiple>
                                                <option value="" data-placeholder>— اختر المستفيد / المستفيدين —</option>
                                                @foreach($beneficiaryOptions as $option)
                                                    <option value="{{ $option->id }}" @selected(in_array((int) $option->id, $selectedAllocatedBeneficiaries, true))>
                                                        {{ $option->full_name }}{{ $option->code ? ' — ' . $option->code : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="project-allocated-beneficiaries-help" class="form-help-text"></div>
                                            @error('allocated_beneficiary_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div id="project-sponsors-field" class="col-md-6 {{ old('child_sponsorship_type') ? '' : 'd-none' }}">
                                            <label id="project-sponsors-label" class="form-label">اختر الكافل / الكفلاء</label>
                                            <select id="project-sponsors-list" name="sponsor_ids[]"
                                                class="form-select @error('sponsor_ids') is-invalid @enderror" multiple>
                                                <option value="" data-placeholder>— اختر الكافل / الكفلاء —</option>
                                                @foreach($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->id }}" @selected(in_array((int) $sponsor->id, $selectedSponsors, true))>
                                                        {{ $sponsor->name }}{{ $sponsor->phone ? ' — ' . $sponsor->phone : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="project-sponsors-help" class="form-help-text"></div>
                                            @error('sponsor_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>ملاحظات</span>
                                    </div>
                                    <label class="form-label">ملاحظات داخلية</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                        placeholder="أضف أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">
                                        <i class="bi bi-x-lg me-1"></i> إلغاء
                                    </button>
                                    <button class="btn btn-primary px-4">
                                        <i class="bi bi-check-lg me-1"></i> حفظ المستفيد
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="project-table-container">
                            <div class="project-table-scroll">
                                <table class="table table-hover align-middle table-project-beneficiaries" id="projectBeneficiariesTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 100px;">الكود</th>
                                            <th style="min-width: 190px;">الاسم الكامل</th>
                                            <th style="min-width: 145px;">الرقم القومي</th>
                                            <th style="min-width: 130px;">الهاتف</th>
                                            <th style="min-width: 150px;">العنوان</th>
                                            <th style="min-width: 100px;">المساعدة</th>
                                            <th style="min-width: 120px;">التخصيص</th>
                                            <th style="min-width: 120px;">كفالة الطفل</th>
                                            <th style="min-width: 130px;">ملاحظات</th>
                                            <th style="width: 120px; text-align: center;">إجراء</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($genericBeneficiaries as $ben)
                                        <tr>
                                            <td>
                                                <span class="badge bg-light text-dark border font-monospace px-2 py-1">{{ $ben->code ?? '—' }}</span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 34px; height: 34px; font-size: 0.85rem; flex-shrink: 0;">
                                                        {{ mb_substr($ben->full_name, 0, 1) }}
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('beneficiaries.show', $ben) }}" class="fw-bold text-dark text-decoration-none hover-primary d-block">
                                                            {{ $ben->full_name }}
                                                        </a>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-nowrap font-monospace text-muted small">
                                                {{ $ben->national_id ?? '—' }}
                                            </td>
                                            <td class="text-nowrap">
                                                @if($ben->phone)
                                                    <a href="tel:{{ $ben->phone }}" class="text-decoration-none text-body small d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-telephone text-success"></i>
                                                        <span dir="ltr">{{ $ben->phone }}</span>
                                                    </a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ben->address)
                                                    <span class="text-muted small d-inline-flex align-items-center gap-1">
                                                        <i class="bi bi-geo-alt text-danger"></i> {{ $ben->address }}
                                                    </span>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($ben->assistance_type === 'financial')
                                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1"><i class="bi bi-cash me-1"></i> مالية</span>
                                                @elseif($ben->assistance_type === 'in_kind')
                                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1"><i class="bi bi-box me-1"></i> عينية</span>
                                                @elseif($ben->assistance_type === 'service')
                                                    <span class="badge bg-purple-subtle text-purple border border-purple-subtle px-2 py-1"><i class="bi bi-gear me-1"></i> خدمية</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary px-2 py-1">{{ $ben->assistance_type ?? '—' }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-light text-dark border">{{ $ben->allocation_type ?? '—' }}</span>
                                                    @if($ben->allocatedBeneficiaries->isNotEmpty())
                                                        <div class="text-muted mt-1 small" style="font-size: 0.72rem;">
                                                            {{ $ben->allocatedBeneficiaries->pluck('full_name')->join('، ') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <div>
                                                    <span class="badge bg-light text-dark border">{{ $ben->child_sponsorship_type ?? '—' }}</span>
                                                    @if($ben->sponsors->isNotEmpty())
                                                        <div class="text-muted mt-1 small" style="font-size: 0.72rem;">
                                                            {{ $ben->sponsors->pluck('name')->join('، ') }}
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                            <td>
                                                <span class="text-muted small text-truncate d-inline-block" style="max-width: 140px;" title="{{ $ben->notes }}">
                                                    {{ $ben->notes ?? '—' }}
                                                </span>
                                            </td>
                                            <td class="text-center" style="padding: 10px 14px;">
                                                <div class="d-inline-flex align-items-center gap-1.5 justify-content-center">
                                                    <a class="action-btn-circle btn btn-sm btn-outline-primary" 
                                                       href="{{ route('beneficiaries.edit', $ben) }}" 
                                                       title="تعديل بيانات المستفيد">
                                                        <i class="bi bi-pencil-fill" style="font-size: 0.75rem;"></i>
                                                    </a>
                                                    <a class="action-btn-circle btn btn-sm btn-outline-info" 
                                                       href="{{ route('beneficiaries.show', $ben) }}" 
                                                       title="عرض ملف المستفيد">
                                                        <i class="bi bi-eye-fill" style="font-size: 0.75rem;"></i>
                                                    </a>
                                                    <form action="{{ route('projects.destroyBeneficiaryFile', [$project, $ben]) }}" 
                                                        method="POST" 
                                                        class="d-inline"
                                                        data-confirm-delete
                                                        data-confirm-title="تأكيد حذف المستفيد من المشروع"
                                                        data-name="{{ $ben->full_name }}">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="action-btn-circle btn btn-sm btn-outline-danger" 
                                                                title="حذف المستفيد من المشروع">
                                                            <i class="bi bi-trash3-fill" style="font-size: 0.75rem;"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="10" class="text-center py-5 text-muted">
                                                <i class="bi bi-people fs-2 d-block mb-2 opacity-50"></i>
                                                <span class="fw-bold">لا يوجد مستفيدون مسجلون لهذا المشروع حتى الآن</span>
                                                <p class="small text-muted mb-0">يمكنك إضافة مستفيد جديد باستخدام الزر أعلاه</p>
                                            </td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Edit Modals -->
                        @foreach($genericBeneficiaries ?? [] as $ben)
                        <div class="modal fade" id="editBen{{ $ben->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">تعديل المستفيد</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('projects.updateBeneficiaryFile', [$project, $ben]) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="mb-2">
                                                <label class="form-label small">الاسم</label>
                                                <input type="text" name="full_name" class="form-control form-control-sm" value="{{ $ben->full_name }}" required>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">الهاتف</label>
                                                <input type="text" name="phone" class="form-control form-control-sm" value="{{ $ben->phone }}">
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">المساعدة</label>
                                                <select name="assistance_type" class="form-select form-select-sm" required>
                                                    <option value="in_kind" @selected($ben->assistance_type === 'in_kind')>عينية</option>
                                                    <option value="financial" @selected($ben->assistance_type === 'financial')>مالية</option>
                                                    <option value="service" @selected($ben->assistance_type === 'service')>خدمة</option>
                                                </select>
                                            </div>
                                            <div class="mb-2">
                                                <label class="form-label small">ملاحظات</label>
                                                <textarea name="notes" class="form-control form-control-sm">{{ $ben->notes }}</textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary btn-sm">حفظ</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @if($isZadProject)
                    </div>
                    {{-- Zad Families Tab Pane --}}
                    <div class="tab-pane fade" id="zad-families-pane" role="tabpanel">
                        <!-- Zad Families Section -->
                        <div class="card border-0 shadow-sm mb-4 rounded-3 overflow-hidden">
                            <div class="card-body p-3 p-md-4">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center gap-2">
                                        <span class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3" style="width: 40px; height: 40px;">
                                            <i class="bi bi-basket2-fill fs-5"></i>
                                        </span>
                                        <div>
                                            <h5 class="fw-bold mb-0 text-dark">ملف أهالي زاد</h5>
                                            <small class="text-muted">الاستمارات المخصصة للحالات وتوزيعات الدواجن واللحوم</small>
                                        </div>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-1.5 ms-2 fw-bold" style="font-size: 0.82rem;">
                                            {{ $zadFamilies->count() }} أسرة
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <button class="btn btn-outline-success btn-sm px-3 py-1.5 rounded-pill fw-bold shadow-sm d-flex align-items-center gap-1.5" type="button" data-bs-toggle="collapse" data-bs-target="#zadFamilyForm">
                                            <i class="bi bi-plus-circle-fill"></i>
                                            <span>إضافة حالة بالملف الميداني</span>
                                        </button>
                                    </div>
                                </div>
                                
                                <div class="collapse mb-4" id="zadFamilyForm">
                                    <form method="POST" action="{{ route('projects.storeZadFamily', $project) }}" class="bg-body-tertiary p-3 rounded-3 shadow-sm border">
                                        @csrf
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">اسم الام <span class="text-danger">*</span></label>
                                                <input type="text" name="mother_name" class="form-control form-control-sm" required placeholder="اسم الأم ثلاثي أو رباعي">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">رقم التلفون</label>
                                                <input type="text" name="phone" class="form-control form-control-sm" placeholder="01xxxxxxxxx">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">اسماء الاطفال</label>
                                                <textarea name="children_names" class="form-control form-control-sm" rows="2" placeholder="اكتب أسماء الأطفال مفصولة بفواصل"></textarea>
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">رقم احتياطي</label>
                                                <input type="text" name="backup_phone" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">عدد الاطفال</label>
                                                <input type="number" name="children_count" class="form-control form-control-sm" min="0">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">المكفولة</label>
                                                <input type="number" name="sponsored_children_count" class="form-control form-control-sm" min="0">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label small fw-bold">الصف الدراسي</label>
                                                <input type="text" name="study_grade" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">فرخة/بطة</label>
                                                <input type="text" name="poultry_type" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-2">
                                                <label class="form-label small fw-bold">لحمة</label>
                                                <input type="text" name="meat" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">ملاحظات</label>
                                                <textarea name="notes_cases" class="form-control form-control-sm" rows="2"></textarea>
                                            </div>
                                            <div class="col-12 text-end">
                                                <button class="btn btn-success btn-sm px-4 fw-bold">حفظ الحالة</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-sm table-hover align-middle small" style="min-width: 820px;">
                                        <thead class="bg-body-tertiary">
                                            <tr>
                                                <th>الام</th>
                                                <th>اسماء الاطفال</th>
                                                <th>الهاتف</th>
                                                <th>عدد الاطفال</th>
                                                <th>المكفولة</th>
                                                <th>الدراسة</th>
                                                <th>فرخة/بطة</th>
                                                <th>لحمة</th>
                                                <th class="text-center">اجراء</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($zadFamilies as $family)
                                            <tr>
                                                <td class="fw-bold">{{ $family->mother_name ?? $family->full_name }}</td>
                                                <td><span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $family->children_names }}">{{ $family->children_names ?? '—' }}</span></td>
                                                <td dir="ltr" class="text-end">{{ $family->phone ?? '—' }}</td>
                                                <td class="text-center">{{ $family->children_count ?? '—' }}</td>
                                                <td class="text-center">{{ $family->sponsored_children_count ?? '—' }}</td>
                                                <td>{{ $family->study_grade ?? '—' }}</td>
                                                <td>{{ $family->poultry_type ?? '—' }}</td>
                                                <td>{{ $family->meat ?? '—' }}</td>
                                                <td class="text-center">
                                                    <div class="d-inline-flex gap-2">
                                                        <!-- Edit Button -->
                                                        <button class="btn btn-link text-primary p-0" type="button" data-bs-toggle="modal" data-bs-target="#editZadFamily{{ $family->id }}" title="تعديل">
                                                            <i class="bi bi-pencil-fill"></i>
                                                        </button>

                                                        <!-- Delete Button -->
                                                        <form action="{{ route('projects.destroyZadFamily', [$project, $family]) }}" 
                                                            method="POST" 
                                                            class="d-inline"
                                                            data-confirm-delete
                                                            data-confirm-title="تأكيد حذف ملف الأسرة"
                                                            data-name="{{ $family->family_name ?? $family->full_name }}">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="btn btn-link text-danger p-0" title="حذف الأسرة"><i class="bi bi-trash3-fill"></i></button>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="9" class="text-center py-4 text-muted">
                                                    <i class="bi bi-basket2 fs-2 d-block mb-1 opacity-50"></i>
                                                    لا توجد سجلات مسجلة في ملف أهالي زاد الميداني حتى الآن (يمكنك استخدام الزر أعلاه لإضافة حالة)
                                                </td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Edit Modals for Zad Families -->
                                @foreach($zadFamilies ?? [] as $family)
                                <div class="modal fade" id="editZadFamily{{ $family->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">تعديل حالة: {{ $family->mother_name ?? $family->full_name }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form method="POST" action="{{ route('projects.updateZadFamily', [$project, $family]) }}">
                                                @csrf @method('PUT')
                                                <div class="modal-body text-start">
                                                    <div class="row g-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">اسم الام</label>
                                                            <input type="text" name="mother_name" class="form-control" value="{{ $family->mother_name }}" required>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">رقم التلفون</label>
                                                            <input type="text" name="phone" class="form-control" value="{{ $family->phone }}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label small fw-bold">اسماء الاطفال</label>
                                                            <textarea name="children_names" class="form-control" rows="2">{{ $family->children_names }}</textarea>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">رقم احتياطي</label>
                                                            <input type="text" name="backup_phone" class="form-control" value="{{ $family->backup_phone }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">عدد الاطفال</label>
                                                            <input type="number" name="children_count" class="form-control" value="{{ $family->children_count }}">
                                                        </div>
                                                        <div class="col-md-4">
                                                            <label class="form-label small fw-bold">المكفولة</label>
                                                            <input type="number" name="sponsored_children_count" class="form-control" value="{{ $family->sponsored_children_count }}">
                                                        </div>
                                                        <div class="col-md-6">
                                                            <label class="form-label small fw-bold">الصف الدراسي</label>
                                                            <input type="text" name="study_grade" class="form-control" value="{{ $family->study_grade }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-bold">فرخة/بطة</label>
                                                            <input type="text" name="poultry_type" class="form-control" value="{{ $family->poultry_type }}">
                                                        </div>
                                                        <div class="col-md-3">
                                                            <label class="form-label small fw-bold">لحمة</label>
                                                            <input type="text" name="meat" class="form-control" value="{{ $family->meat }}">
                                                        </div>
                                                        <div class="col-md-12">
                                                            <label class="form-label small fw-bold">ملاحظات</label>
                                                            <textarea name="notes_cases" class="form-control" rows="2">{{ $family->notes_cases }}</textarea>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Zad Children & Assessments Pane --}}
                    <div class="tab-pane fade" id="zad-children-pane" role="tabpanel">
                        <div class="zad-children-card-container child-doodle-pattern mb-4">
                            {{-- Cheerful Child-Friendly Header --}}
                            <div class="zad-children-header-bg">
                                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="child-avatar-circle">
                                            <span class="floating-kid-icon">🎈</span>
                                        </div>
                                        <div>
                                            <div class="d-flex align-items-center gap-2">
                                                <h5 class="fw-bold mb-0 text-dark">قائمة الأطفال المكفولين والتقييمات الدورية</h5>
                                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold fs-7 shadow-xs">
                                                    {{ isset($zadChildren) ? $zadChildren->count() : 0 }} طفل مكفول
                                                </span>
                                            </div>
                                            <small class="text-muted">متابعة شاملة لملفات الأطفال المكفولين، تقييمات الدخول المقفلة، والتقارير الشهرية المرسلة للكفلاء</small>
                                        </div>
                                    </div>
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="input-group input-group-sm" style="max-width: 260px;">
                                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                                            <input type="text" id="filterZadChildrenInput" class="form-control border-start-0" placeholder="بحث بالاسم أو الكود..." onkeyup="filterZadChildrenTable()">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="p-3 p-md-4 bg-white">
                                <div class="table-responsive">
                                    <table class="table align-middle table-hover border rounded-3 overflow-hidden mb-0" id="zadChildrenTable">
                                        <thead class="table-light">
                                            <tr>
                                                <th style="min-width: 220px;">الطفل المكفول</th>
                                                <th style="min-width: 170px;">ولي الأمر / الأسرة</th>
                                                <th style="min-width: 170px;">الكافل المسؤول</th>
                                                <th style="min-width: 180px;">حالة التقييمات الشهرية</th>
                                                <th style="min-width: 240px;" class="text-center">إجراءات الطفل (التقييم وملف الطفل)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($zadChildren ?? [] as $child)
                                                @php
                                                    $entry = $child->entryAssessment;
                                                    $latestMonthly = $child->latestMonthlyAssessment;
                                                    $childAge = $child->birth_date ? $child->birth_date->age . ' سنة' : ($child->age ? $child->age . ' سنة' : '—');
                                                    $sponsor = $child->sponsors->first();
                                                    $spPhone = preg_replace('/[^0-9]/', '', $sponsor?->phone ?? '');
                                                    if (str_starts_with($spPhone, '01')) { $spPhone = '2' . $spPhone; }

                                                    $academicScore = $latestMonthly ? ($latestMonthly->academic_rating !== null ? $latestMonthly->academic_rating . '%' : '—') : '—';
                                                    $psychoScore = $latestMonthly ? ($latestMonthly->psychological_rating !== null ? $latestMonthly->psychological_rating . '%' : '—') : '—';
                                                    $overallScore = $latestMonthly ? $latestMonthly->score_percentage . '%' : ($entry ? $entry->score_percentage . '%' : 'قيد المتابعة');
                                                    $evalDate = $latestMonthly ? $latestMonthly->assessment_date?->format('Y-m-d') : ($entry ? $entry->assessment_date?->format('Y-m-d') : 'حديث');

                                                    $waMsg = "السلام عليكم ورحمة الله وبركاته،\n" .
                                                             "أهلاً بك كافل الخير أ/ " . ($sponsor?->name ?? 'الكريم') . "،\n" .
                                                             "يسر مؤسسة إنسان للأعمال الخيرية (مشروع زاد لرعاية وتأهيل الأيتام) أن توافيكم بملف وتقرير المتابعة الشهري للطفل المكفول برعايتكم الكريمة:\n\n" .
                                                             "👤 اسم الطفل: " . $child->full_name . " (كود: " . $child->code . ")\n" .
                                                             "📅 تاريخ التقييم: " . $evalDate . "\n" .
                                                             "📚 التقييم الدراسي: " . $academicScore . "\n" .
                                                             "🧠 التقييم النفسي والاجتماعي: " . $psychoScore . "\n" .
                                                             "⭐ النتيجة الإجمالية: " . $overallScore . "\n\n" .
                                                             "🔗 رابط معاينة ملف الطفل المحدث وتقرير التقييم:\n" .
                                                             route('child-assessments.report', $child) . "\n\n" .
                                                             "جزاكم الله خير الجزاء وتقبل منكم صالح الأعمال ودعمكم المستمر.";
                                                @endphp
                                                <tr class="child-row">
                                                    <td>
                                                        <div class="d-flex align-items-center gap-2.5">
                                                            <div class="child-avatar-circle" style="width: 40px; height: 40px; font-size: 1.2rem;">
                                                                👦
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold fs-6 text-dark child-name">{{ $child->full_name }}</div>
                                                                <div class="d-flex align-items-center gap-2 font-monospace" style="font-size: 0.78rem;">
                                                                    <span class="text-muted child-code">كود: {{ $child->code }}</span>
                                                                    <span class="text-primary fw-semibold">{{ $childAge }}</span>
                                                                    @if($child->education_level)
                                                                        <span class="badge bg-light text-secondary border px-1.5">{{ $child->education_level }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="fw-semibold text-dark">{{ $child->beneficiary->mother_name ?: $child->beneficiary->full_name }}</div>
                                                        <div class="small text-muted font-monospace">كود الأسرة: {{ $child->beneficiary->code }}</div>
                                                    </td>
                                                    <td>
                                                        @if($sponsor)
                                                            <div class="d-flex align-items-center gap-1.5 mb-1">
                                                                <a href="{{ route('donors.show', $sponsor) }}" class="fw-bold text-success text-decoration-none small">
                                                                    <i class="bi bi-person-heart me-1"></i>{{ $sponsor->name }}
                                                                </a>
                                                            </div>
                                                            @if(!empty($spPhone))
                                                                <a href="https://wa.me/{{ $spPhone }}?text={{ urlencode($waMsg) }}" target="_blank" class="badge bg-success text-white text-decoration-none px-2 py-1 shadow-xs" title="إرسال التقرير الشهري والملف للكافل عبر الواتساب">
                                                                    <i class="bi bi-whatsapp me-1"></i> واتساب للكافل
                                                                </a>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-secondary-subtle text-secondary">غير مكفول بعد</span>
                                                        @endif
                                                    </td>
                                                    <td>
                                                        @if($latestMonthly)
                                                            <div class="d-flex align-items-center gap-2 mb-1">
                                                                <span class="badge {{ $latestMonthly->score_percentage >= 85 ? 'bg-success' : ($latestMonthly->score_percentage >= 70 ? 'bg-primary' : 'bg-warning text-dark') }} fs-7 fw-bold font-monospace px-2.5 py-1">
                                                                    {{ $latestMonthly->score_percentage }}%
                                                                </span>
                                                                <span class="text-muted small font-monospace">({{ $latestMonthly->assessment_date?->format('Y-m-d') }})</span>
                                                            </div>
                                                            <div class="small text-muted" style="font-size: 0.76rem;">
                                                                <span>دراسي: <strong class="text-primary">{{ $latestMonthly->academic_rating ?? '—' }}%</strong></span>
                                                                · <span>نفسي: <strong class="text-success">{{ $latestMonthly->psychological_rating ?? '—' }}%</strong></span>
                                                            </div>
                                                            @if($latestMonthly->test_image_url)
                                                                <a href="{{ $latestMonthly->test_image_url }}" target="_blank" class="badge bg-white text-primary border shadow-2xs mt-1 text-decoration-none">
                                                                    <i class="bi bi-paperclip me-1"></i> ورقة الاختبار
                                                                </a>
                                                            @endif
                                                        @else
                                                            <span class="badge bg-light text-muted border mb-1">لا يوجد تقييم شهري بعد</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="d-flex align-items-center justify-content-center gap-2 flex-wrap">
                                                            
                                                            {{-- الأيقونة 1: ( التقييم ) / تقييم عام الطفل --}}
                                                            <div class="dropdown">
                                                                <button class="btn btn-sm btn-primary rounded-pill px-3 py-1.5 dropdown-toggle fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" type="button" data-bs-toggle="dropdown">
                                                                    <i class="bi bi-award-fill text-warning"></i>
                                                                    <span>التقييم</span>
                                                                </button>
                                                                <ul class="dropdown-menu dropdown-menu-end shadow border-0 p-2" style="min-width: 250px;">
                                                                    <li class="dropdown-header small text-muted fw-bold">تقييمات الطفل</li>
                                                                    
                                                                    {{-- تقييم الدخول (غير قابل للتعديل بعد تنفيذه) --}}
                                                                    <li>
                                                                        @if($entry)
                                                                            <button type="button" class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#viewEntryAssessment_{{ $child->id }}">
                                                                                <i class="bi bi-shield-lock-fill text-success fs-6"></i>
                                                                                <div>
                                                                                    <div class="fw-bold small">معاينة تقييم الدخول</div>
                                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle" style="font-size: 0.68rem;">🔒 معتمد ومغلق</span>
                                                                                </div>
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-2 text-warning-emphasis bg-warning bg-opacity-10" data-bs-toggle="modal" data-bs-target="#addEntryAssessment_{{ $child->id }}">
                                                                                <i class="bi bi-journal-plus text-warning fs-6"></i>
                                                                                <div>
                                                                                    <div class="fw-bold small text-dark">إجراء تقييم الدخول</div>
                                                                                    <span class="text-muted" style="font-size: 0.7rem;">شامل + مرفق ملف (يُقفل بعد الحفظ)</span>
                                                                                </div>
                                                                            </button>
                                                                        @endif
                                                                    </li>
                                                                    
                                                                    <li><hr class="dropdown-divider my-1"></li>

                                                                    {{-- تقييم دوري أو شهري --}}
                                                                    <li>
                                                                        <button type="button" class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-2 text-primary" data-bs-toggle="modal" data-bs-target="#addMonthlyAssessment_{{ $child->id }}">
                                                                            <i class="bi bi-plus-circle-fill text-primary fs-6"></i>
                                                                            <div>
                                                                                <div class="fw-bold small text-dark">إضافة تقييم شهري</div>
                                                                                <span class="text-muted" style="font-size: 0.7rem;">دراسي ونفسي % + صورة الاختبار</span>
                                                                            </div>
                                                                        </button>
                                                                    </li>

                                                                    <li><hr class="dropdown-divider my-1"></li>

                                                                    {{-- سجل التقييمات الكامل --}}
                                                                    <li>
                                                                        <a class="dropdown-item py-2 rounded-2 d-flex align-items-center gap-2" href="{{ route('child-assessments.report', $child) }}" target="_blank">
                                                                            <i class="bi bi-clock-history text-secondary fs-6"></i>
                                                                            <div class="small fw-semibold text-secondary">سجل التقييمات والملف الكامل</div>
                                                                        </a>
                                                                    </li>
                                                                </ul>
                                                            </div>

                                                            {{-- الأيقونة 2: ( ملف الطفل ) --}}
                                                            <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1.5 shadow-xs" data-bs-toggle="modal" data-bs-target="#childDossierModal_{{ $child->id }}">
                                                                <i class="bi bi-folder-fill text-info"></i>
                                                                <span>ملف الطفل</span>
                                                            </button>

                                                        </div>
                                                    </td>
                                                </tr>

                                                {{-- Modal: Add Monthly Assessment (تقييم دوري / شهري) --}}
                                                <div class="modal fade" id="addMonthlyAssessment_{{ $child->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
                                                            <div class="modal-header bg-primary text-white py-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="fs-4">📅</span>
                                                                    <div>
                                                                        <h6 class="modal-title fw-bold mb-0">استمارة التقييم الشهري — {{ $child->full_name }}</h6>
                                                                        <small class="text-white-50">مشروع زاد لرعاية وتأهيل الأيتام المكفولين</small>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST" action="{{ route('child-assessments.store-monthly', $child) }}" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body p-4 bg-light">
                                                                    <div class="row g-3 mb-3">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label small fw-bold text-dark mb-1">
                                                                                <i class="bi bi-calendar-event me-1 text-primary"></i> تاريخ التقييم <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="date" name="assessment_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label small fw-bold text-dark mb-1">
                                                                                <i class="bi bi-person-check me-1 text-primary"></i> من قام بالاختبار (الاسم) <span class="text-danger">*</span>
                                                                            </label>
                                                                            <input type="text" name="reviewer_name" value="{{ auth()->user()?->name }}" class="form-control form-control-sm" placeholder="اسم الموظف أو المتطوع" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label small fw-bold text-dark mb-1">
                                                                                <i class="bi bi-patch-check me-1 text-primary"></i> صفة القائم بالاختبار <span class="text-danger">*</span>
                                                                            </label>
                                                                            <select name="reviewer_role" class="form-select form-select-sm" required>
                                                                                <option value="employee" selected>موظف</option>
                                                                                <option value="volunteer">متطوع</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                        <h6 class="fw-bold text-primary mb-2.5"><i class="bi bi-award me-1"></i> درجات التقييم الشهري (%)</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-4">
                                                                                <label class="form-label small fw-bold text-success mb-1">
                                                                                    <i class="bi bi-book me-1"></i> تقييم دراسي (%) <span class="text-danger">*</span>
                                                                                </label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="academic_rating" placeholder="مثال: 95" class="form-control form-control-sm" required>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label class="form-label small fw-bold text-info mb-1">
                                                                                    <i class="bi bi-emoji-smile me-1"></i> تقييم نفسي واجتماعي (%) <span class="text-danger">*</span>
                                                                                </label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="psychological_rating" placeholder="مثال: 90" class="form-control form-control-sm" required>
                                                                            </div>
                                                                            <div class="col-md-4">
                                                                                <label class="form-label small fw-bold text-warning mb-1">
                                                                                    <i class="bi bi-percent me-1"></i> النتيجة الإجمالية (%)
                                                                                </label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="score_percentage" placeholder="تترك فارغة للحساب التلقائي" class="form-control form-control-sm">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                        <label class="form-label small fw-bold text-dark mb-1">
                                                                            <i class="bi bi-camera me-1 text-danger"></i> إرفاق صورة ورقة الاختبار أو التقييم <span class="text-danger">*</span>
                                                                        </label>
                                                                        <input type="file" name="test_image" accept="image/*,.pdf" class="form-control form-control-sm" required>
                                                                        <small class="text-muted mt-1 d-block">قم برفع صورة أو ملف ورقة الاختبار التي تم تصحيحها للطفل.</small>
                                                                    </div>

                                                                    <div class="mb-0">
                                                                        <label class="form-label small fw-bold text-dark mb-1">
                                                                            <i class="bi bi-chat-left-text me-1"></i> ملاحظات المشرف وتوصيات الرعاية
                                                                        </label>
                                                                        <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="تطور الحفظ، السلوك، التفاعل والملاحظات الإضافية..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer py-2 bg-white">
                                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-4 fw-bold">
                                                                        <i class="bi bi-save me-1"></i> حفظ التقييم الشهري
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Modal: Add Comprehensive Entry Assessment (تقييم الدخول - غير قابل للتعديل بعد تنفيذه) --}}
                                                @if(!$entry)
                                                <div class="modal fade" id="addEntryAssessment_{{ $child->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
                                                            <div class="modal-header bg-warning text-dark py-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="fs-4">📝</span>
                                                                    <div>
                                                                        <h6 class="modal-title fw-bold mb-0">استمارة التقييم العام الشامل لدخول المشروع — {{ $child->full_name }}</h6>
                                                                        <small class="text-dark-50">تُجرى عند الالتحاق لمرة واحدة وتُقفل تلقائياً</small>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <form method="POST" action="{{ route('child-assessments.store-entry', $child) }}" enctype="multipart/form-data">
                                                                @csrf
                                                                <div class="modal-body p-4 bg-light">
                                                                    <div class="alert alert-warning py-2 small mb-3 border-warning d-flex align-items-center gap-2">
                                                                        <i class="bi bi-shield-lock-fill fs-5 text-warning-emphasis"></i>
                                                                        <div>
                                                                            <strong>تنبيه هام ومؤكد:</strong> تقييم الدخول هذا <u>غير قابل للتعديل بعد تنفيذه وحفظه</u>، حيث يُعتمد كوثيقة مرجعية ثابتة لقياس تطور الطفل في المشروع.
                                                                        </div>
                                                                    </div>

                                                                    <div class="row g-3 mb-3">
                                                                        <div class="col-md-4">
                                                                            <label class="form-label small fw-bold text-dark mb-1">تاريخ الدخول والتقييم <span class="text-danger">*</span></label>
                                                                            <input type="date" name="assessment_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label small fw-bold text-dark mb-1">من قام بالاختبار (الاسم) <span class="text-danger">*</span></label>
                                                                            <input type="text" name="reviewer_name" value="{{ auth()->user()?->name }}" class="form-control form-control-sm" placeholder="اسم الأخصائي أو المتطوع" required>
                                                                        </div>
                                                                        <div class="col-md-4">
                                                                            <label class="form-label small fw-bold text-dark mb-1">صفة القائم بالاختبار <span class="text-danger">*</span></label>
                                                                            <select name="reviewer_role" class="form-select form-select-sm" required>
                                                                                <option value="employee" selected>موظف</option>
                                                                                <option value="volunteer">متطوع</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                        <h6 class="fw-bold text-primary mb-2.5"><i class="bi bi-award me-1"></i> محاور تقييم الدخول الشامل (%)</h6>
                                                                        <div class="row g-3">
                                                                            <div class="col-md-3">
                                                                                <label class="form-label small fw-bold mb-1 text-success">التقييم القرآني/الدراسي (%)</label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="academic_rating" placeholder="مثال: 85" class="form-control form-control-sm">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label small fw-bold mb-1 text-info">التقييم النفسي والاجتماعي (%)</label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="psychological_rating" placeholder="مثال: 80" class="form-control form-control-sm">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label small fw-bold mb-1 text-secondary">الوضع الصحي والبدني (%)</label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="health_rating" placeholder="مثال: 90" class="form-control form-control-sm">
                                                                            </div>
                                                                            <div class="col-md-3">
                                                                                <label class="form-label small fw-bold mb-1 text-warning">النتيجة الإجمالية العامة (%)</label>
                                                                                <input type="number" step="0.5" min="0" max="100" name="score_percentage" placeholder="تترك فارغة للحساب" class="form-control form-control-sm">
                                                                            </div>
                                                                        </div>
                                                                    </div>

                                                                    <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                        <label class="form-label small fw-bold text-dark mb-1">
                                                                            <i class="bi bi-file-earmark-arrow-up text-primary me-1"></i> إرفاق استمارة / ملف تقييم الدخول (مرفق ملف)
                                                                        </label>
                                                                        <input type="file" name="test_image" accept="image/*,.pdf" class="form-control form-control-sm">
                                                                        <small class="text-muted mt-1 d-block">إرفاق استمارة تقييم الدخول الورقية الموقعة أو تقرير الأخصائي.</small>
                                                                    </div>

                                                                    <div>
                                                                        <label class="form-label small fw-bold text-dark mb-1">ملاحظات وخطة الرعاية الفردية</label>
                                                                        <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="أبرز المهارات، نقاط القوة، التوصيات..."></textarea>
                                                                    </div>
                                                                </div>
                                                                <div class="modal-footer py-2 bg-white">
                                                                    <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">إلغاء</button>
                                                                    <button type="submit" class="btn btn-warning btn-sm rounded-pill px-4 fw-bold text-dark">
                                                                        <i class="bi bi-lock-fill me-1"></i> حفظ واعتماد وقفل تقييم الدخول
                                                                    </button>
                                                                </div>
                                                            </form>
                                                        </div>
                                                    </div>
                                                </div>
                                                @else
                                                {{-- Modal: View Locked Entry Assessment (معاينة تقييم الدخول المقفل) --}}
                                                <div class="modal fade" id="viewEntryAssessment_{{ $child->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
                                                            <div class="modal-header bg-success text-white py-3">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="fs-4">🔒</span>
                                                                    <div>
                                                                        <h6 class="modal-title fw-bold mb-0">وثيقة تقييم الدخول المعتمدة — {{ $child->full_name }}</h6>
                                                                        <span class="badge bg-white text-success px-2 py-0.5 rounded-pill small">معتمد ومغلق نهائياً</span>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4 bg-light">
                                                                <div class="d-flex justify-content-between align-items-center mb-3 p-3 bg-white rounded-3 border">
                                                                    <div>
                                                                        <span class="text-muted small d-block">القائم بالاختبار</span>
                                                                        <strong>{{ $entry->reviewer_name }}</strong>
                                                                        <span class="badge bg-light text-secondary border ms-1">{{ $entry->reviewer_role_label }}</span>
                                                                    </div>
                                                                    <div class="text-end">
                                                                        <span class="text-muted small d-block">تاريخ التقييم</span>
                                                                        <strong class="font-monospace text-primary">{{ $entry->assessment_date?->format('Y-m-d') }}</strong>
                                                                    </div>
                                                                </div>

                                                                <div class="row g-2 mb-3 text-center">
                                                                    <div class="col-3">
                                                                        <div class="p-2 bg-success-subtle rounded border">
                                                                            <div class="small text-muted">الدراسي/القرآني</div>
                                                                            <div class="fs-5 fw-bold text-success font-monospace">{{ $entry->academic_rating ?? '—' }}%</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="p-2 bg-info-subtle rounded border">
                                                                            <div class="small text-muted">النفسي والاجتماعي</div>
                                                                            <div class="fs-5 fw-bold text-info font-monospace">{{ $entry->psychological_rating ?? '—' }}%</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="p-2 bg-secondary-subtle rounded border">
                                                                            <div class="small text-muted">الصحي والبدني</div>
                                                                            <div class="fs-5 fw-bold text-secondary font-monospace">{{ $entry->health_rating ?? '—' }}%</div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-3">
                                                                        <div class="p-2 bg-warning-subtle rounded border">
                                                                            <div class="small text-muted">النتيجة الإجمالية</div>
                                                                            <div class="fs-5 fw-bold text-warning font-monospace">{{ $entry->score_percentage ?? '—' }}%</div>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @if($entry->notes)
                                                                    <div class="p-3 rounded bg-white border mb-3">
                                                                        <strong class="text-dark d-block mb-1">الملاحظات والتوصيات:</strong>
                                                                        <div class="text-muted small">{{ $entry->notes }}</div>
                                                                    </div>
                                                                @endif

                                                                @if($entry->test_image_url)
                                                                    <div class="p-3 bg-white rounded border text-center">
                                                                        <strong class="text-muted d-block small mb-2">استمارة / ملف التقييم المرفق:</strong>
                                                                        <a href="{{ $entry->test_image_url }}" target="_blank">
                                                                            <img src="{{ $entry->test_image_url }}" alt="استمارة تقييم الدخول" class="img-fluid rounded border shadow-sm" style="max-height: 300px;">
                                                                        </a>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer py-2 bg-white">
                                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif

                                                {{-- Modal: Child Dossier & WhatsApp (ملف الطفل والمرفقات وإرسال الواتساب للكافل) --}}
                                                <div class="modal fade" id="childDossierModal_{{ $child->id }}" tabindex="-1" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                                        <div class="modal-content shadow border-0 rounded-4 overflow-hidden">
                                                            <div class="modal-header text-white py-3" style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 100%);">
                                                                <div class="d-flex align-items-center gap-2">
                                                                    <span class="fs-4">📁</span>
                                                                    <div>
                                                                        <h6 class="modal-title fw-bold mb-0">ملف الطفل الشامل — {{ $child->full_name }}</h6>
                                                                        <small class="text-white-50 font-monospace">كود: {{ $child->code }} · {{ $childAge }}</small>
                                                                    </div>
                                                                </div>
                                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body p-4 bg-light">
                                                                {{-- General Info Card --}}
                                                                <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                    <h6 class="fw-bold text-primary mb-2.5"><i class="bi bi-person-vcard me-1"></i> بيانات الطفل العامة</h6>
                                                                    <div class="row g-2 small">
                                                                        <div class="col-md-6"><strong>الاسم:</strong> {{ $child->full_name }}</div>
                                                                        <div class="col-md-6"><strong>كود الطفل:</strong> <span class="font-monospace text-primary">{{ $child->code }}</span></div>
                                                                        <div class="col-md-6"><strong>تاريخ الميلاد والعمر:</strong> {{ $child->birth_date?->format('Y-m-d') ?? '—' }} ({{ $childAge }})</div>
                                                                        <div class="col-md-6"><strong>المرحلة والصف:</strong> {{ $child->education_level ?: '—' }}</div>
                                                                        <div class="col-md-6"><strong>ولي الأمر / الأسرة:</strong> {{ $child->beneficiary->mother_name ?: $child->beneficiary->full_name }}</div>
                                                                        <div class="col-md-6"><strong>كود الأسرة:</strong> <span class="font-monospace">{{ $child->beneficiary->code }}</span></div>
                                                                        <div class="col-md-6"><strong>الكافل المسؤول:</strong> <span class="text-success fw-bold">{{ $sponsor?->name ?? 'غير مكفول بعد' }}</span></div>
                                                                        <div class="col-md-6"><strong>مبلغ الكفالة الشهري:</strong> {{ $child->sponsorship_amount ? number_format((float)$child->sponsorship_amount) . ' ج.م' : '—' }}</div>
                                                                    </div>
                                                                </div>

                                                                {{-- Attached Documents (مرفق ملف فيه ملف الطفل) --}}
                                                                <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                                                        <h6 class="fw-bold text-warning mb-0"><i class="bi bi-folder-fill me-1"></i> وثائق وملفات الطفل المرفقة</h6>
                                                                        <span class="badge bg-light text-secondary border font-monospace">{{ $child->attachments->count() }} ملف</span>
                                                                    </div>

                                                                    @if($child->attachments->isNotEmpty())
                                                                        <div class="row g-2 mb-3">
                                                                            @foreach($child->attachments as $doc)
                                                                                <div class="col-md-6">
                                                                                    <div class="p-2 bg-light rounded-3 border d-flex align-items-center justify-content-between gap-2">
                                                                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                                                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                                                                                            <div class="text-truncate small">
                                                                                                <div class="fw-bold text-truncate">{{ $doc->original_name ?: 'ملف مرفق' }}</div>
                                                                                                <span class="text-muted font-monospace" style="font-size: 0.7rem;">{{ $doc->created_at?->format('Y-m-d') }}</span>
                                                                                            </div>
                                                                                        </div>
                                                                                        <a href="{{ $doc->view_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-0.5 small">
                                                                                            معاينة
                                                                                        </a>
                                                                                    </div>
                                                                                </div>
                                                                            @endforeach
                                                                        </div>
                                                                    @else
                                                                        <div class="text-muted small text-center py-2 mb-2">لا توجد ملفات إضافية مرفقة بملف هذا الطفل حالياً.</div>
                                                                    @endif

                                                                    {{-- Form: Upload New Document to Child Dossier --}}
                                                                    <form action="{{ route('child-assessments.upload-document', $child) }}" method="POST" enctype="multipart/form-data" class="p-2.5 bg-body-tertiary rounded-3 border">
                                                                        @csrf
                                                                        <div class="row g-2 align-items-end">
                                                                            <div class="col-md-5">
                                                                                <label class="form-label small fw-bold mb-1">وصف الملف (مثل: شهادة الميلاد، تقرير دراسي)</label>
                                                                                <input type="text" name="name" class="form-control form-control-sm" placeholder="اسم أو نوع الملف...">
                                                                            </div>
                                                                            <div class="col-md-5">
                                                                                <label class="form-label small fw-bold mb-1">اختر الملف (مستند / صورة / PDF)</label>
                                                                                <input type="file" name="document" class="form-control form-control-sm" required>
                                                                            </div>
                                                                            <div class="col-md-2">
                                                                                <button type="submit" class="btn btn-warning btn-sm w-100 fw-bold">
                                                                                    <i class="bi bi-upload me-1"></i> إرفاق
                                                                                </button>
                                                                            </div>
                                                                        </div>
                                                                    </form>
                                                                </div>

                                                                {{-- Latest Monthly Evaluation Card --}}
                                                                <div class="card p-3 bg-white rounded-3 border mb-3 shadow-2xs">
                                                                    <h6 class="fw-bold text-success mb-2"><i class="bi bi-graph-up me-1"></i> أحدث تقييم شهري للطفل</h6>
                                                                    @if($latestMonthly)
                                                                        <div class="row g-2 text-center mb-2">
                                                                            <div class="col-4">
                                                                                <div class="p-2 bg-primary-subtle rounded border">
                                                                                    <div class="small text-muted">التقييم الدراسي</div>
                                                                                    <strong class="fs-5 text-primary font-monospace">{{ $latestMonthly->academic_rating ?? '—' }}%</strong>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="p-2 bg-success-subtle rounded border">
                                                                                    <div class="small text-muted">التقييم النفسي والاجتماعي</div>
                                                                                    <strong class="fs-5 text-success font-monospace">{{ $latestMonthly->psychological_rating ?? '—' }}%</strong>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-4">
                                                                                <div class="p-2 bg-warning-subtle rounded border">
                                                                                    <div class="small text-muted">النسبة العامة</div>
                                                                                    <strong class="fs-5 text-warning font-monospace">{{ $latestMonthly->score_percentage ?? '—' }}%</strong>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="small text-muted d-flex justify-content-between align-items-center">
                                                                            <span>القائم بالاختبار: <strong>{{ $latestMonthly->reviewer_name }} ({{ $latestMonthly->reviewer_role_label }})</strong></span>
                                                                            <span class="font-monospace">التاريخ: {{ $latestMonthly->assessment_date?->format('Y-m-d') }}</span>
                                                                        </div>
                                                                        @if($latestMonthly->test_image_url)
                                                                            <div class="mt-2 text-center">
                                                                                <a href="{{ $latestMonthly->test_image_url }}" target="_blank" class="badge bg-light text-primary border py-1.5 px-3">
                                                                                    <i class="bi bi-image me-1"></i> معاينة ورقة الاختبار المرفقة
                                                                                </a>
                                                                            </div>
                                                                        @endif
                                                                    @else
                                                                        <div class="text-muted small text-center py-2">لم يُسجل تقييم شهري بعد، سيظهر التقييم فور إدخاله.</div>
                                                                    @endif
                                                                </div>

                                                                {{-- WhatsApp Button for Sponsor --}}
                                                                @if($sponsor && !empty($spPhone))
                                                                    <div class="mt-3">
                                                                        <a href="https://wa.me/{{ $spPhone }}?text={{ urlencode($waMsg) }}" target="_blank" class="btn btn-success fw-bold w-100 py-2.5 rounded-pill shadow-sm d-flex align-items-center justify-content-center gap-2">
                                                                            <i class="bi bi-whatsapp fs-5"></i>
                                                                            <span>إرسال التقرير والملف المحدث للكافل عبر الواتساب (أ/ {{ $sponsor->name }})</span>
                                                                        </a>
                                                                    </div>
                                                                @else
                                                                    <div class="alert alert-secondary py-2 small mb-0 text-center">
                                                                        <i class="bi bi-info-circle me-1"></i> لا يوجد كافل مرتبط أو رقم هاتف كافل لإرسال التقرير عبر الواتساب.
                                                                    </div>
                                                                @endif
                                                            </div>
                                                            <div class="modal-footer py-2 bg-white">
                                                                <a href="{{ route('child-assessments.report', $child) }}" target="_blank" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                                    <i class="bi bi-printer me-1"></i> فتح ملف وتقرير الطفل للطباعة
                                                                </a>
                                                                <button type="button" class="btn btn-secondary btn-sm rounded-pill px-3" data-bs-dismiss="modal">إغلاق</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center py-5 text-muted">
                                                        <span class="fs-1 d-block mb-2 floating-kid-icon">🎈</span>
                                                        <h6 class="fw-bold text-dark">لا يوجد أطفال مكفولين مسجلين في هذا المشروع حتى الآن</h6>
                                                        <small>سيتم إدراج الأطفال تلقائياً عند تسجيلهم في مشروع زاد.</small>
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
            @endif

            @if($project->name === 'مشروع كسوة')
                <!-- Weekly Activities Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="gh-section-title">
                            <span><i class="bi bi-calendar-week text-info me-2"></i> الأنشطة الأسبوعية</span>
                            <div class="badge bg-success">إيرادات المعارض: {{ number_format($exhibitionsRevenue, 2) }}</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Exhibitions -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-body-tertiary">
                                    <div class="card-header bg-transparent fw-bold text-center small">المعارض (الجمعة)</div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        @if($exhibitions->isEmpty())
                                            <div class="text-center text-muted small">لا توجد معارض</div>
                                        @else
                                            <ul class="list-group list-group-flush small bg-transparent">
                                                @foreach($exhibitions as $activity)
                                                    <li
                                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-1">
                                                        <div>
                                                            <div class="fw-bold">{{ $activity->activity_date->format('Y-m-d') }}</div>
                                                            <div class="text-muted" style="font-size: 0.8em">
                                                                {{ $activity->location ?? '—' }}</div>
                                                            <div class="text-muted" style="font-size: 0.8em">
                                                                {{ $activity->description }}</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold text-success">{{ number_format($activity->revenue, 2) }}
                                                            </div>
                                                            <form method="POST"
                                                                action="{{ route('projects.destroyActivity', ['project' => $project->id, 'activity' => $activity->id]) }}"
                                                                class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-link text-danger p-0" style="font-size: 0.7rem"
                                                                    onclick="return confirm('هل أنت متأكد؟')">×</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Advertising -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-body-tertiary">
                                    <div class="card-header bg-transparent fw-bold text-center small">الدعايا الأسبوعية</div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        @if($advertisingDays->isEmpty())
                                            <div class="text-center text-muted small">لا توجد بيانات</div>
                                        @else
                                            <ul class="list-group list-group-flush small bg-transparent">
                                                @foreach($advertisingDays as $activity)
                                                    <li
                                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-1">
                                                        <div>
                                                            <div class="fw-bold">{{ $activity->activity_date->format('Y-m-d') }}</div>
                                                            @if($activity->location)
                                                                <div class="text-muted" style="font-size: 0.8em">{{ $activity->location }}
                                                                </div>
                                                            @endif
                                                            <div class="text-muted" style="font-size: 0.8em">
                                                                {{ $activity->description }}</div>
                                                        </div>
                                                        <div class="text-end">
                                                            @if($activity->revenue > 0)
                                                                <div class="fw-bold text-success">{{ number_format($activity->revenue, 2) }}
                                                                </div>
                                                            @endif
                                                            <form method="POST"
                                                                action="{{ route('projects.destroyActivity', ['project' => $project->id, 'activity' => $activity->id]) }}"
                                                                class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-link text-danger p-0" style="font-size: 0.7rem"
                                                                    onclick="return confirm('هل أنت متأكد؟')">×</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('projects.storeActivity', $project) }}">
                            @csrf
                            <div class="row g-2 align-items-end bg-body-tertiary p-3 rounded">
                                <div class="col-md-12 mb-2">
                                    <div class="fw-bold small">إضافة نشاط جديد</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">النوع</label>
                                    <select name="type" class="form-select form-select-sm" id="activityType"
                                        onchange="toggleRevenue()">
                                        <option value="exhibition">معرض</option>
                                        <option value="advertising">دعاية</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">التاريخ</label>
                                    <input type="date" name="activity_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">المكان/الوصف</label>
                                    <input type="text" name="location" class="form-control form-control-sm"
                                        placeholder="المكان">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">الإيراد</label>
                                    <input type="number" step="0.01" name="revenue" class="form-control form-control-sm"
                                        placeholder="0">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-sm w-100">إضافة</button>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <input type="text" name="description" class="form-control form-control-sm"
                                        placeholder="ملاحظات إضافية">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
    @endif

</div>

<!-- Left Column (in RTL): Sidebar -->
<div class="col-lg-4">

            <!-- Manager Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">مدير المشروع</div>
                    <div class="mb-3">
                        @if($project->manager_photo_url)
                            <img src="{{ $project->manager_photo_url }}" class="rounded-circle mb-2"
                                style="width:80px;height:80px;object-fit:cover">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                style="width:80px;height:80px;font-size:2rem">
                                {{ mb_substr($project->manager?->name ?? '?', 0, 1) }}
                            </div>
                        @endif
                        <h5 class="fw-bold mb-0">{{ $project->manager?->name ?? '—' }}</h5>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#managerForm">تغيير المدير</button>
                    <div class="collapse mt-3" id="managerForm">
                        <form method="POST" action="{{ route('projects.setManager', $project) }}"
                            enctype="multipart/form-data">
                            @csrf
                            @php
                                $staffUsers = ($users ?? null) ?: \App\Models\User::orderBy('name')->get();
                            @endphp
                            <select name="manager_user_id" class="form-select form-select-sm mb-2">
                                <option value="">— اختر مدير المشروع —</option>
                                @foreach($staffUsers as $v)
                                    <option value="{{ $v->id }}" @selected($project->manager_user_id == $v->id)>
                                        {{ $v->name }} ({{ $v->job_title ?: ($v->role ?: 'مستخدم') }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="file" name="manager_photo" class="form-control form-control-sm mb-2"
                                accept="image/*">
                            <button class="btn btn-sm btn-primary w-100">حفظ</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Deputy Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">نائب المدير</div>
                    <div class="mb-3">
                        @if($project->deputy_photo_url)
                            <img src="{{ $project->deputy_photo_url }}" class="rounded-circle mb-2"
                                style="width:80px;height:80px;object-fit:cover">
                        @else
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                style="width:80px;height:80px;font-size:2rem">
                                {{ mb_substr($project->deputy?->name ?? '?', 0, 1) }}
                            </div>
                        @endif
                        <h5 class="fw-bold mb-0">{{ $project->deputy?->name ?? '—' }}</h5>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#deputyForm">تغيير النائب</button>
                    <div class="collapse mt-3" id="deputyForm">
                        <form method="POST" action="{{ route('projects.setDeputy', $project) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <select name="deputy_user_id" class="form-select form-select-sm mb-2">
                                <option value="">— اختر نائب المدير —</option>
                                @foreach($staffUsers as $v)
                                    <option value="{{ $v->id }}" @selected($project->deputy_user_id == $v->id)>
                                        {{ $v->name }} ({{ $v->job_title ?: ($v->role ?: 'مستخدم') }})
                                    </option>
                                @endforeach
                            </select>
                            <input type="file" name="deputy_photo" class="form-control form-control-sm mb-2"
                                accept="image/*">
                            <button class="btn btn-sm btn-primary w-100">حفظ</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Donation Details (Chart) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">تفصيل التبرعات</div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>نقدي ({{ $cashPct }}%)</span>
                                <span>{{ number_format($cashSum) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $cashPct }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>عيني ({{ 100 - $cashPct }}%)</span>
                                <span>{{ number_format($inKindSum) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ 100 - $cashPct }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Volunteers -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو الشهر</span>
                    </div>
                    <div class="list-group list-group-flush mb-3">
                        @forelse($monthlyVolunteers as $mv)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $mv->user->name }}</div>
                                    <div class="small text-muted">{{ $mv->month }}/{{ $mv->year }}</div>
                                </div>
                                <form action="{{ route('projects.destroyMonthlyVolunteer', [$project, $mv]) }}" 
                                    method="POST"
                                    class="d-inline"
                                    data-confirm-delete
                                    data-confirm-title="تأكيد حذف المتطوع"
                                    data-name="{{ $mv->user->name }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-link text-danger p-0" title="حذف"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين لهذا الشهر</div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('projects.storeMonthlyVolunteer', $project) }}"
                        class="bg-body-tertiary p-2 rounded">
                        @csrf
                        <div class="mb-2">
                            <select name="user_id" class="form-select form-select-sm mb-1" required>
                                <option value="">اختر متطوع...</option>
                                @foreach($volunteers as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1 mb-2">
                            <div class="col-6">
                                <select name="month" class="form-select form-select-sm">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($m == date('n'))>{{ $m }}</option> @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" name="year" class="form-control form-control-sm"
                                    value="{{ date('Y') }}">
                            </div>
                        </div>
                        <input type="text" name="notes" class="form-control form-control-sm mb-2" placeholder="ملاحظات">
                        <button class="btn btn-sm btn-primary w-100">إضافة</button>
                    </form>
                </div>
            </div>

            <!-- Project Volunteers -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو المشروع</span>
                        <span class="badge bg-secondary">{{ count($projectVolunteers) }}</span>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($projectVolunteers as $pv)
                            <div
                                class="d-flex align-items-center justify-content-between p-2 border rounded hover-shadow transition">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;font-weight:bold;font-size:0.8rem">
                                        {{ mb_substr($pv->name, 0, 1) }}
                                    </div>
                                    <div style="line-height:1.1">
                                        <div class="fw-bold small">{{ $pv->name }}</div>
                                        <div class="text-muted" style="font-size:0.7rem">{{ $pv->pivot->role ?? 'متطوع' }}</div>
                                    </div>
                                </div>
                                <form method="POST"
                                    action="{{ route('projects.detachVolunteer', ['project' => $project->id, 'user' => $pv->id]) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link text-danger p-0 btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('projects.attachVolunteer', $project) }}"
                        class="bg-body-tertiary p-2 rounded">
                        @csrf
                        <div class="mb-2">
                            <select name="user_id" class="form-select form-select-sm mb-1">
                                <option value="">اختر متطوع...</option>
                                @foreach($volunteers as $v)
                                    @if(!$projectVolunteers->contains('id', $v->id))
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="role" class="form-control form-control-sm" placeholder="الدور">
                        </div>
                        <div class="row g-1 mb-2">
                            <div class="col-6">
                                <input type="number" step="0.5" name="hours" class="form-control form-control-sm"
                                    placeholder="ساعات">
                            </div>
                            <div class="col-6">
                                <input type="date" name="started_at" class="form-control form-control-sm"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary w-100">إضافة متطوع</button>
                    </form>
                </div>
            </div>

        </div>
    </div>

    @include('projects.partials.finance-modals')
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $ = window.jQuery;
            const hasSelect2 = Boolean($ && $.fn && $.fn.select2);

            if (hasSelect2) {
                $('#project-allocation-type, #project-child-sponsorship-type').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    width: '100%',
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    placeholder: 'ابحث واختر النوع...'
                });
            }

            function configurePeopleList(config) {
                const typeSelect = document.getElementById(config.typeSelectId);
                const field = document.getElementById(config.fieldId);
                const list = document.getElementById(config.listId);
                const label = document.getElementById(config.labelId);
                const help = document.getElementById(config.helpId);

                if (!typeSelect || !field || !list) return;

                function updateList() {
                    const isActive = typeSelect.value === config.singleType || typeSelect.value === config.multipleType;
                    const isMultiple = typeSelect.value === config.multipleType;

                    if (hasSelect2 && $(list).hasClass('select2-hidden-accessible')) {
                        $(list).select2('destroy');
                    }

                    field.classList.toggle('d-none', !isActive);
                    list.disabled = !isActive;
                    list.required = isActive;
                    list.multiple = isMultiple;
                    list.size = isMultiple ? Math.min(6, Math.max(2, list.options.length)) : 1;

                    const placeholder = list.querySelector('[data-placeholder]');
                    if (placeholder) {
                        placeholder.disabled = isMultiple;
                        if (isMultiple) placeholder.selected = false;
                    }

                    label.textContent = isMultiple ? config.multipleLabel : config.singleLabel;
                    help.textContent = isMultiple ? 'يمكنك اختيار أكثر من اسم من القائمة.' : '';

                    if (!isActive) {
                        Array.from(list.options).forEach(option => option.selected = false);
                    } else if (!isMultiple && list.selectedOptions.length > 1) {
                        Array.from(list.selectedOptions).slice(1).forEach(option => option.selected = false);
                    }

                    if (isActive && hasSelect2) {
                        $(list).select2({
                            theme: 'bootstrap-5',
                            dir: 'rtl',
                            width: '100%',
                            allowClear: !isMultiple,
                            closeOnSelect: !isMultiple,
                            minimumResultsForSearch: 0,
                            placeholder: isMultiple ? config.multipleLabel : config.singleLabel
                        });
                    }
                }

                typeSelect.addEventListener('change', updateList);
                if (hasSelect2) {
                    $(typeSelect).on('change select2:select select2:clear', updateList);
                }
                updateList();
            }

            configurePeopleList({
                typeSelectId: 'project-allocation-type',
                fieldId: 'project-allocated-beneficiaries-field',
                listId: 'project-allocated-beneficiaries',
                labelId: 'project-allocated-beneficiaries-label',
                helpId: 'project-allocated-beneficiaries-help',
                singleType: 'شخص واحد',
                multipleType: 'أكثر من مستفيد',
                singleLabel: 'اختر المستفيد',
                multipleLabel: 'اختر المستفيدين'
            });

            configurePeopleList({
                typeSelectId: 'project-child-sponsorship-type',
                fieldId: 'project-sponsors-field',
                listId: 'project-sponsors-list',
                labelId: 'project-sponsors-label',
                helpId: 'project-sponsors-help',
                singleType: 'كافل واحد',
                multipleType: 'أكثر من كافل',
                singleLabel: 'اختر الكافل',
                multipleLabel: 'اختر الكفلاء'
            });

            const donorMode = document.getElementById('project-donor-mode');
            const existingDonorField = document.getElementById('project-existing-donor-field');
            const donorId = document.getElementById('project-donor-id');
            const newDonorFields = document.getElementById('project-new-donor-fields');
            const newDonorName = document.getElementById('project-new-donor-name');
            const newDonorPhone = document.getElementById('project-new-donor-phone');

            function updateDonorMode() {
                if (!donorMode) return;

                const isNew = donorMode.value === 'new';
                existingDonorField?.classList.toggle('d-none', isNew);
                newDonorFields?.classList.toggle('d-none', !isNew);

                if (donorId) {
                    donorId.disabled = isNew;
                    donorId.required = !isNew;
                    if (isNew) donorId.value = '';
                }

                newDonorFields?.querySelectorAll('input, select').forEach(function (field) {
                    field.disabled = !isNew;
                });
                if (newDonorName) newDonorName.required = isNew;
                if (newDonorPhone) newDonorPhone.required = isNew;
            }

            donorMode?.addEventListener('change', updateDonorMode);
            updateDonorMode();

            const donationType = document.getElementById('project-donation-type');
            const cashFields = document.querySelectorAll('.project-donation-cash-field');
            const inKindFields = document.querySelectorAll('.project-donation-in-kind-field');
            const cashRequiredIds = [
                'project-donation-amount',
                'project-donation-cash-channel',
                'project-donation-receipt',
                'project-donation-treasury'
            ];
            const inKindRequiredIds = [
                'project-donation-estimated-value',
                'project-donation-warehouse'
            ];

            function setFinanceFieldsState(fields, active) {
                fields.forEach(function (container) {
                    container.classList.toggle('d-none', !active);
                    container.querySelectorAll('input, select, textarea').forEach(function (field) {
                        field.disabled = !active;
                    });
                });
            }

            function updateDonationType() {
                if (!donationType) return;

                const isCash = donationType.value === 'cash';
                setFinanceFieldsState(cashFields, isCash);
                setFinanceFieldsState(inKindFields, !isCash);
                cashRequiredIds.forEach(function (id) {
                    const field = document.getElementById(id);
                    if (field) field.required = isCash;
                });
                inKindRequiredIds.forEach(function (id) {
                    const field = document.getElementById(id);
                    if (field) field.required = !isCash;
                });
            }

            donationType?.addEventListener('change', updateDonationType);
            updateDonationType();

            const previousFormContext = @json(old('form_context'));
            const modalId = previousFormContext === 'project_donation'
                ? 'projectDonationModal'
                : (previousFormContext === 'project_expense' ? 'projectExpenseModal' : null);

            if (modalId && window.bootstrap) {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            }

            // Instant Beneficiaries Search Filter
            const filterBenInput = document.getElementById('filterBeneficiariesTable');
            const projectBenTable = document.getElementById('projectBeneficiariesTable');
            const visibleBenCount = document.getElementById('visibleBenCount');
            if (filterBenInput && projectBenTable) {
                filterBenInput.addEventListener('input', function () {
                    const q = this.value.toLowerCase().trim();
                    const rows = projectBenTable.querySelectorAll('tbody tr');
                    let visible = 0;
                    rows.forEach(function (r) {
                        if (r.cells.length < 2) return;
                        const text = r.textContent.toLowerCase();
                        const match = !q || text.includes(q);
                        r.style.display = match ? '' : 'none';
                        if (match) visible++;
                    });
                    if (visibleBenCount) visibleBenCount.textContent = visible;
                });
            }

            // Zad Children Table Filter
            window.filterZadChildrenTable = function() {
                const input = document.getElementById('filterZadChildrenInput');
                if (!input) return;
                const filter = input.value.toLowerCase().trim();
                const table = document.getElementById('zadChildrenTable');
                if (!table) return;
                const rows = table.querySelectorAll('tbody tr.child-row');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(filter) ? '' : 'none';
                });
            };

            window.activateZadChildrenTab = function() {
                const tabBtn = document.getElementById('zad-children-tab');
                if (tabBtn && window.bootstrap) {
                    const tab = bootstrap.Tab.getOrCreateInstance(tabBtn);
                    tab.show();
                    const pane = document.getElementById('zad-children-pane');
                    if (pane) {
                        pane.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                }
            };

            if (window.location.hash === '#zad-children' || new URLSearchParams(window.location.search).get('tab') === 'children') {
                setTimeout(function() {
                    if (typeof window.activateZadChildrenTab === 'function') {
                        window.activateZadChildrenTab();
                    }
                }, 200);
            }
        });
    </script>
@endsection

