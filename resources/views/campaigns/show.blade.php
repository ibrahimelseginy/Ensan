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
        .gh-metric-card {
            background: var(--bg-card, #fff);
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

        .gh-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .gh-list-item:last-child {
            border-bottom: none;
        }

        .theme-dark .gh-metric-card {
            background: var(--bg-card);
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
</style>

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-megaphone text-primary"></i>
                {{ $campaign->name }} ({{ $campaign->season_year }})
            </h4>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span
                    class="badge {{ $campaign->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $campaign->status === 'active' ? 'نشط' : 'مؤرشف' }}</span>
                @if($campaign->project)
                    <span class="text-muted">•</span>
                    <a href="{{ route('projects.show', $campaign->project) }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-folder me-1"></i> {{ $campaign->project->name }}
                    </a>
                @endif
                <span class="text-muted">•</span>
                <span class="text-muted small">{{ $campaign->start_date?->format('Y-m-d') ?? '—' }} إلى
                    {{ $campaign->end_date?->format('Y-m-d') ?? '—' }}</span>
            </div>
        </div>
        <div class="btn-group">
            @if(request()->user()?->hasPermission('donations.view'))
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#campaignDonationModal">
                    <i class="bi bi-cash-coin me-1"></i> إضافة تبرع
                </button>
            @endif
            @if(request()->user()?->hasPermission('expenses.view'))
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#campaignExpenseModal">
                    <i class="bi bi-receipt me-1"></i> إضافة مصروف
                </button>
            @endif
            @if(\App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)->where('model_id', $campaign->id)->where('status', 'pending')->exists())
                <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-1 rounded-pill small">
                    <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                </span>
            @else
                <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-outline-primary"><i
                        class="bi bi-pencil me-1"></i> تعديل</a>
            @endif
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary"><i
                    class="bi bi-arrow-right me-1"></i> عودة</a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="text-muted small">اجمالي التبرعات</div>
                <h3 class="fw-bold mb-0">{{ number_format($donationsTotal) }}</h3>
                <div class="small text-success mt-1">
                    <i class="bi bi-arrow-up"></i> {{ $donationsCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="text-muted small">اجمالي المصروفات</div>
                <h3 class="fw-bold mb-0">{{ number_format($expensesTotal) }}</h3>
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
                <h3 class="fw-bold mb-0">{{ number_format($netBalance) }}</h3>
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
                <h3 class="fw-bold mb-0">{{ number_format($beneficiariesCount) }}</h3>
                <div class="small text-muted mt-1">
                    مستفيد مسجل
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Main Lists -->
        <div class="col-lg-8">

            @if(Str::contains($campaign->name, 'رمضان') || Str::contains($campaign->name, 'Ramadan'))
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <a href="{{ route('ramadan-bags.index') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded p-3 me-3 fs-3">
                                        <i class="bi bi-bag-heart"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">شنط رمضان</h5>
                                        <div class="small text-white-50">إدارة المستفيدين وتوزيع الشنط الرمضانية</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('ramadan-iftars.index') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm bg-success text-white h-100" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded p-3 me-3 fs-3">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">إفطارات رمضان</h5>
                                        <div class="small text-white-50">إدارة المستفيدين والوجبات وتوزيع الإفطارات</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Executive Structure (Ramadan 2026 Plan) -->
            @if(Str::contains($campaign->name, 'رمضان') || Str::contains($campaign->name, 'Ramadan'))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="gh-section-title">
                            <span><i class="bi bi-diagram-3 text-purple me-2"></i> الهيكل التنفيذي (خطة رمضان 2026)</span>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#structureCollapse" aria-expanded="false" aria-controls="structureCollapse">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="structureCollapse">
                            <div class="row g-4">
                                <!-- Top Management -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">أولاً: الإدارة العليا</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-primary border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark"><i
                                                        class="bi bi-building-check me-2 text-primary"></i>مدير المؤسسة</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>الموافقة النهائية على القرارات والخطط.</li>
                                                    <li>متابعة أداء مدير الحملة والتأكد من التزامه.</li>
                                                    <li>اعتماد الميزانيات والصرف.</li>
                                                    <li>متابعة التقارير العامة أسبوعيًا.</li>
                                                    <li>التدخل عند الحاجة في المشكلات الكبرى.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-primary border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark"><i
                                                        class="bi bi-person-workspace me-2 text-primary"></i>مدير الحملة</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>الإشراف العام على الحملة وقيادة الفرق.</li>
                                                    <li>المتابعة الميدانية واليومية خلال رمضان.</li>
                                                    <li>اتخاذ القرارات العاجلة.</li>
                                                    <li>مراجعة التقارير الميدانية والإدارية.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Team Leaders -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-success mb-3 border-bottom pb-2">ثانياً: قيادة الفرق</h6>
                                    <div class="p-3 bg-light rounded-3 border border-success border-opacity-10">
                                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-people me-2 text-success"></i>تيم
                                            ليدرز</div>
                                        <ul class="small text-muted mb-0 ps-3">
                                            <li>إدارة المتطوعين يومياً وتشغيلهم.</li>
                                            <li>متابعة تنفيذ المهام الميدانية والإدارية.</li>
                                            <li>رفع تقرير يومي لمدير الحملة.</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- HR -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-info mb-3 border-bottom pb-2">ثالثاً: الموارد البشرية HR</h6>
                                    <div class="vstack gap-3">
                                        <div class="p-3 bg-light rounded-3 border border-info border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">مسؤول HR</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>اختيار وتسجيل المتطوعين.</li>
                                                <li>إعداد جداول العمل اليومية.</li>
                                                <li>متابعة الالتزام والانضباط وتقييم الأداء.</li>
                                            </ul>
                                        </div>
                                        <div class="p-3 bg-light rounded-3 border border-info border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">متابعين أونلاين</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>متابعة المتطوعين عبر الجروبات.</li>
                                                <li>تسجيل الحضور والمهام.</li>
                                                <li>رفع التقارير لمسؤول HR ومدير الحملة.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logistics -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-warning mb-3 border-bottom pb-2">رابعاً: قسم اللوجيستيك</h6>
                                    <div class="vstack gap-3">
                                        <div class="p-3 bg-light rounded-3 border border-warning border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">مسؤول لوجيستيك</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li><strong>قبل رمضان:</strong> تجهيز المخازن واستلام المواد.</li>
                                                <li><strong>داخل رمضان:</strong> تنظيم المخزون.</li>
                                            </ul>
                                        </div>
                                        <div class="p-3 bg-light rounded-3 border border-warning border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">مسؤولين لوجيستيك</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>متابعة التعبئة اليومية والإشراف على التوزيعات.</li>
                                                <li>التنسيق اليومي مع بقية الأقسام.</li>
                                                <li>رفع تقرير يومي.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Research -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-danger mb-3 border-bottom pb-2">خامساً: قسم الأبحاث والاعتماد</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-danger border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark">مسؤولي اعتماد</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>مراجعة الأبحاث.</li>
                                                    <li>مناقشة الحالات واعتمادها.</li>
                                                    <li>تحديد أولويات الاستحقاق.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-danger border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark">مسؤول الأبحاث</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>إدارة فريق الباحثين وتوزيع المهام.</li>
                                                    <li>مراجعة البيانات قبل الاعتماد.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-danger border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark">فريق الباحثين</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>جمع بيانات الأسر.</li>
                                                    <li>الزيارات الميدانية.</li>
                                                    <li>رفع نتائج البحث لمسؤول الأبحاث.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accounts -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">سادساً: الحسابات</h6>
                                    <div class="p-3 bg-light rounded-3 border border-secondary border-opacity-10">
                                        <div class="d-flex justify-content-between flex-wrap gap-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>تسجيل التبرعات اليومية</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>مراجعة المصروفات اليومية</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>متابعة الميزانيات</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Generic Beneficiaries Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-people-fill text-primary me-2"></i> ملف المستفيدين</span>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">إضافة مستفيد</button>
                    </div>
                    
                    <div @class(['collapse mb-4', 'show' => old('form_context') === 'beneficiary']) id="beneficiaryForm">
                        <form method="POST" action="{{ route('campaigns.storeBeneficiaryFile', $campaign) }}" class="bg-body-tertiary p-4 rounded shadow-sm">
                            @csrf
                            <input type="hidden" name="form_context" value="beneficiary">

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
                                        <select id="campaign-allocation-type" name="allocation_type" class="form-select @error('allocation_type') is-invalid @enderror">
                                            <option value="">— اختر النوع —</option>
                                            <option value="شخص واحد" @selected(old('allocation_type') === 'شخص واحد')>شخص واحد</option>
                                            <option value="أكثر من مستفيد" @selected(old('allocation_type') === 'أكثر من مستفيد')>أكثر من مستفيد</option>
                                        </select>
                                        @error('allocation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">التخصيص لكل طفل</label>
                                        <select id="campaign-child-sponsorship-type" name="child_sponsorship_type" class="form-select @error('child_sponsorship_type') is-invalid @enderror">
                                            <option value="">— اختر النوع —</option>
                                            <option value="كافل واحد" @selected(old('child_sponsorship_type') === 'كافل واحد')>كافل واحد</option>
                                            <option value="أكثر من كافل" @selected(old('child_sponsorship_type') === 'أكثر من كافل')>أكثر من كافل</option>
                                        </select>
                                        @error('child_sponsorship_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div id="campaign-allocated-beneficiaries-field"
                                        class="col-md-6 {{ old('allocation_type') ? '' : 'd-none' }}">
                                        <label id="campaign-allocated-beneficiaries-label" class="form-label">اختر المستفيد / المستفيدين</label>
                                        <select id="campaign-allocated-beneficiaries" name="allocated_beneficiary_ids[]"
                                            class="form-select @error('allocated_beneficiary_ids') is-invalid @enderror" multiple>
                                            <option value="" data-placeholder>— اختر المستفيد / المستفيدين —</option>
                                            @foreach($beneficiaryOptions as $option)
                                                <option value="{{ $option->id }}" @selected(in_array((int) $option->id, $selectedAllocatedBeneficiaries, true))>
                                                    {{ $option->full_name }}{{ $option->code ? ' — ' . $option->code : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="campaign-allocated-beneficiaries-help" class="form-help-text"></div>
                                        @error('allocated_beneficiary_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div id="campaign-sponsors-field" class="col-md-6 {{ old('child_sponsorship_type') ? '' : 'd-none' }}">
                                        <label id="campaign-sponsors-label" class="form-label">اختر الكافل / الكفلاء</label>
                                        <select id="campaign-sponsors-list" name="sponsor_ids[]"
                                            class="form-select @error('sponsor_ids') is-invalid @enderror" multiple>
                                            <option value="" data-placeholder>— اختر الكافل / الكفلاء —</option>
                                            @foreach($sponsors as $sponsor)
                                                <option value="{{ $sponsor->id }}" @selected(in_array((int) $sponsor->id, $selectedSponsors, true))>
                                                    {{ $sponsor->name }}{{ $sponsor->phone ? ' — ' . $sponsor->phone : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <div id="campaign-sponsors-help" class="form-help-text"></div>
                                        @error('sponsor_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="form-section-title">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>ملاحظات</span>
                                    </div>
                                    <label class="form-label">ملاحظات داخلية</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                        placeholder="أضف أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12 d-flex justify-content-end gap-2">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">
                                        <i class="bi bi-x-lg me-1"></i> إلغاء
                                    </button>
                                    <button class="btn btn-primary px-4">
                                        <i class="bi bi-check-lg me-1"></i> حفظ المستفيد
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        @php $campaignBeneficiaries = $campaign->beneficiaries()->orderByDesc('id')->limit(50)->get(); @endphp
                        <table class="table table-sm table-hover align-middle small">
                            <thead class="bg-body-tertiary">
                                <tr>
                                    <th>الاسم</th>
                                    <th>الهاتف</th>
                                    <th>المساعدة</th>
                                    <th>ملاحظات</th>
                                    <th style="width: 80px">إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaignBeneficiaries as $ben)
                                <tr>
                                    <td>{{ $ben->full_name }}</td>
                                    <td>{{ $ben->phone ?? '—' }}</td>
                                    <td>{{ $ben->assistance_type ?? '—' }}</td>
                                    <td>{{ $ben->notes ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-link text-primary p-0" type="button" data-bs-toggle="modal" data-bs-target="#editBen{{ $ben->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <form action="{{ route('campaigns.destroyBeneficiaryFile', [$campaign, $ben]) }}" method="POST" onsubmit="return confirm('حذف المستفيد؟')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">لا يوجد مستفيدين مسجلين لهذه الحملة</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Edit Modals -->
                    @foreach($campaignBeneficiaries ?? [] as $ben)
                    <div class="modal fade" id="editBen{{ $ben->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">تعديل المستفيد</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('campaigns.updateBeneficiaryFile', [$campaign, $ben]) }}">
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
                                                <option value="financial" @selected($ben->assistance_type === 'financial')>مالية</option>
                                                <option value="in_kind" @selected($ben->assistance_type === 'in_kind')>عينية</option>
                                                <option value="service" @selected($ben->assistance_type === 'service')>خدمية</option>
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

        </div>

        <!-- Right Column: Manager, Stats, Volunteers -->
        <div class="col-lg-4">

            <!-- Manager Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">مدير الحملة</div>
                    @if($campaign->manager)
                        <div class="mb-3">
                            @if($campaign->manager_photo_url)
                                <img src="{{ $campaign->manager_photo_url }}" class="rounded-circle mb-2"
                                    style="width:80px;height:80px;object-fit:cover">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                    style="width:80px;height:80px;font-size:2rem">
                                    {{ mb_substr($campaign->manager->name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="fw-bold mb-0">{{ $campaign->manager->name }}</h5>
                            <div class="text-muted small">{{ $campaign->manager->email }}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#managerModal">تغيير المدير</button>
                    @else
                        <div class="text-muted mb-3">لم يتم تعيين مدير بعد</div>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#managerModal">تعيين
                            مدير</button>
                    @endif
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
                        <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal"
                            data-bs-target="#monthlyVolunteerModal"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($monthlyVolunteers as $mv)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $mv->user->name }}</div>
                                    <div class="small text-muted">{{ $mv->month }}/{{ $mv->year }}</div>
                                </div>
                                <form action="{{ route('campaigns.destroyMonthlyVolunteer', [$campaign, $mv]) }}" method="POST"
                                    onsubmit="return confirm('حذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين لهذا الشهر</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Campaign Volunteers -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو الحملة</span>
                        <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal"
                            data-bs-target="#volunteerModal"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($campaignVolunteers as $v)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $v->name }}</div>
                                    <div class="small text-muted">{{ $v->pivot->role ?? 'متطوع' }}</div>
                                </div>
                                <form action="{{ route('campaigns.detachVolunteer', [$campaign, $v]) }}" method="POST"
                                    onsubmit="return confirm('إزالة؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين مسجلين</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    @if(request()->user()?->hasPermission('donations.view'))
        <div class="modal fade" id="campaignDonationModal" tabindex="-1" aria-labelledby="campaignDonationModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('campaigns.storeDonation', $campaign) }}">
                    @csrf
                    <input type="hidden" name="form_context" value="campaign_donation">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="campaignDonationModalLabel">
                                <i class="bi bi-cash-coin text-success me-1"></i> إضافة تبرع للحملة
                            </h5>
                            <div class="small text-muted mt-1">{{ $campaign->name }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        @if($campaignDonationTreasuries->isEmpty())
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                لا توجد خزينة مفعّلة لاستلام تبرع نقدي حاليًا. يمكن تسجيل تبرع عيني أو تفعيل خزينة أولاً.
                            </div>
                        @endif
                        <div class="form-section mb-4">
                            <div class="form-section-title">
                                <i class="bi bi-person-heart"></i>
                                <span>بيانات المتبرع</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-required">طريقة إدخال المتبرع</label>
                                    <select id="campaign-donor-mode" class="form-select">
                                        <option value="existing" @selected(!old('new_donor_name'))>اختيار متبرع مسجل</option>
                                        <option value="new" @selected((bool) old('new_donor_name'))>إضافة متبرع جديد</option>
                                    </select>
                                </div>
                                <div id="campaign-existing-donor-field" class="col-md-6">
                                    <label class="form-label form-label-required">المتبرع</label>
                                    <select id="campaign-donor-id" name="donor_id" class="form-select @error('donor_id') is-invalid @enderror">
                                        <option value="">— اختر المتبرع —</option>
                                        @foreach($campaignDonors as $donor)
                                            <option value="{{ $donor->id }}" @selected(old('donor_id') == $donor->id)>
                                                {{ $donor->name }} — {{ $donor->code }}{{ $donor->phone ? ' — ' . $donor->phone : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('donor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div id="campaign-new-donor-fields" class="col-12 d-none">
                                    <div class="row g-3">
                                        <div class="col-12">
                                            <label class="form-label">كود المتبرع <span class="badge bg-primary-subtle text-primary">ثابت</span></label>
                                            <input name="new_donor_code" dir="ltr" class="form-control font-monospace @error('new_donor_code') is-invalid @enderror"
                                                value="{{ old('new_donor_code') }}" placeholder="اختياري — يُنشأ تلقائيًا عند تركه فارغًا">
                                            @error('new_donor_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label form-label-required">اسم المتبرع الجديد</label>
                                            <input id="campaign-new-donor-name" name="new_donor_name"
                                                class="form-control @error('new_donor_name') is-invalid @enderror"
                                                value="{{ old('new_donor_name') }}" placeholder="الاسم ثلاثي">
                                            @error('new_donor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label form-label-required">رقم الهاتف</label>
                                            <input id="campaign-new-donor-phone" name="new_donor_phone"
                                                class="form-control @error('new_donor_phone') is-invalid @enderror"
                                                value="{{ old('new_donor_phone') }}" placeholder="01xxxxxxxxx">
                                            @error('new_donor_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">تصنيف المتبرع</label>
                                            <select id="campaign-new-donor-classification" name="new_donor_classification" class="form-select">
                                                <option value="one_time" @selected(old('new_donor_classification', 'one_time') === 'one_time')>مرة واحدة</option>
                                                <option value="recurring" @selected(old('new_donor_classification') === 'recurring')>متكرر</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">دورة التكرار</label>
                                            <select id="campaign-new-donor-cycle" name="new_donor_cycle" class="form-select">
                                                <option value="">— اختر —</option>
                                                <option value="monthly" @selected(old('new_donor_cycle') === 'monthly')>شهري</option>
                                                <option value="yearly" @selected(old('new_donor_cycle') === 'yearly')>سنوي</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-wallet2"></i>
                                <span>بيانات التبرع</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-required">نوع التبرع</label>
                                    <select id="campaign-donation-type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="cash" @selected(old('type', 'cash') === 'cash')>نقدي</option>
                                        <option value="in_kind" @selected(old('type') === 'in_kind')>عيني</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تاريخ الاستلام</label>
                                    <input type="date" name="received_at" class="form-control @error('received_at') is-invalid @enderror"
                                        value="{{ old('received_at', now()->toDateString()) }}">
                                    @error('received_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 campaign-donation-cash-field">
                                    <label class="form-label form-label-required">المبلغ</label>
                                    <div class="input-group">
                                        <input id="campaign-donation-amount" name="amount" type="number" step="0.01" min="0.01"
                                            class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00">
                                        <select name="currency" class="form-select" style="max-width: 110px">
                                            @foreach(['EGP', 'USD', 'EUR', 'SAR'] as $currency)
                                                <option value="{{ $currency }}" @selected(old('currency', 'EGP') === $currency)>{{ $currency }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 campaign-donation-cash-field">
                                    <label class="form-label form-label-required">طريقة الدفع</label>
                                    <select id="campaign-donation-cash-channel" name="cash_channel" class="form-select @error('cash_channel') is-invalid @enderror">
                                        <option value="cash" @selected(old('cash_channel', 'cash') === 'cash')>نقدي</option>
                                        <option value="instapay" @selected(old('cash_channel') === 'instapay')>إنستا باي</option>
                                        <option value="vodafone_cash" @selected(old('cash_channel') === 'vodafone_cash')>فودافون كاش</option>
                                        <option value="delegate" @selected(old('cash_channel') === 'delegate')>مندوب</option>
                                    </select>
                                    @error('cash_channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 campaign-donation-cash-field">
                                    <label class="form-label form-label-required">رقم الإيصال</label>
                                    <input id="campaign-donation-receipt" name="receipt_number"
                                        class="form-control @error('receipt_number') is-invalid @enderror"
                                        value="{{ old('receipt_number') }}" placeholder="مثال: RC-2026-000123">
                                    @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 campaign-donation-cash-field">
                                    <label class="form-label form-label-required">الخزينة</label>
                                    <select id="campaign-donation-treasury" name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror">
                                        <option value="">— اختر الخزينة —</option>
                                        @foreach($campaignDonationTreasuries as $treasury)
                                            <option value="{{ $treasury->id }}" @selected(old('treasury_id') == $treasury->id)>
                                                {{ $treasury->name }} — {{ number_format((float) $treasury->current_balance, 2) }} {{ $treasury->currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-6 campaign-donation-in-kind-field d-none">
                                    <label class="form-label form-label-required">القيمة التقديرية</label>
                                    <div class="input-group">
                                        <input id="campaign-donation-estimated-value" name="estimated_value" type="number" step="0.01" min="0.01"
                                            class="form-control @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value') }}">
                                        <span class="input-group-text">EGP</span>
                                    </div>
                                    @error('estimated_value')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6 campaign-donation-in-kind-field d-none">
                                    <label class="form-label form-label-required">المخزن</label>
                                    <select id="campaign-donation-warehouse" name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                                        <option value="">— اختر المخزن —</option>
                                        @foreach($campaignWarehouses as $warehouse)
                                            <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">ملاحظات التخصيص</label>
                                    <textarea name="allocation_note" class="form-control @error('allocation_note') is-invalid @enderror"
                                        rows="2" placeholder="أي تفاصيل إضافية عن تخصيص التبرع...">{{ old('allocation_note') }}</textarea>
                                    @error('allocation_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-success">
                            <i class="bi bi-check-lg me-1"></i> حفظ التبرع
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    @if(request()->user()?->hasPermission('expenses.view'))
        <div class="modal fade" id="campaignExpenseModal" tabindex="-1" aria-labelledby="campaignExpenseModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <form class="modal-content" method="POST" action="{{ route('campaigns.storeExpense', $campaign) }}">
                    @csrf
                    <input type="hidden" name="form_context" value="campaign_expense">
                    <input type="hidden" name="currency" value="EGP">
                    <div class="modal-header">
                        <div>
                            <h5 class="modal-title" id="campaignExpenseModalLabel">
                                <i class="bi bi-receipt text-danger me-1"></i> إضافة مصروف للحملة
                            </h5>
                            <div class="small text-muted mt-1">{{ $campaign->name }}</div>
                        </div>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                    </div>
                    <div class="modal-body">
                        @if($campaignExpenseTreasuries->isEmpty())
                            <div class="alert alert-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                لا توجد خزينة متاحة للصرف. يجب إنشاء أو تفعيل خزينة أولاً.
                            </div>
                        @endif
                        <div class="form-section">
                            <div class="form-section-title">
                                <i class="bi bi-info-circle"></i>
                                <span>بيانات المصروف</span>
                            </div>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label form-label-required">نوع المصروف</label>
                                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                        <option value="operational" @selected(old('type', 'operational') === 'operational')>تشغيلي</option>
                                        <option value="aid" @selected(old('type') === 'aid')>مساعدات</option>
                                        <option value="logistics" @selected(old('type') === 'logistics')>لوجستي</option>
                                    </select>
                                    @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">البند الفرعي</label>
                                    <input list="campaignExpenseCategoryOptions" name="category"
                                        class="form-control @error('category') is-invalid @enderror"
                                        value="{{ old('category') }}" placeholder="اختر أو اكتب...">
                                    <datalist id="campaignExpenseCategoryOptions">
                                        <option value="إطعام">
                                        <option value="مشتريات">
                                        <option value="نقل ومواصلات">
                                        <option value="دعاية وتسويق">
                                        <option value="نثريات">
                                        <option value="صيانة">
                                    </datalist>
                                    @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-required">المبلغ</label>
                                    <div class="input-group">
                                        <input name="amount" type="number" step="0.01" min="0.01" required
                                            class="form-control @error('amount') is-invalid @enderror"
                                            value="{{ old('amount') }}" placeholder="0.00">
                                        <span class="input-group-text">EGP</span>
                                    </div>
                                    @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label form-label-required">الخزينة (مصدر الصرف)</label>
                                    <select name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror" required>
                                        <option value="">— اختر الخزينة —</option>
                                        @foreach($campaignExpenseTreasuries as $treasury)
                                            <option value="{{ $treasury->id }}" @selected(old('treasury_id') == $treasury->id)>
                                                {{ $treasury->name }} — الرصيد: {{ number_format((float) $treasury->current_balance, 2) }} {{ $treasury->currency }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">المستفيد (اختياري)</label>
                                    <select name="beneficiary_id" class="form-select @error('beneficiary_id') is-invalid @enderror">
                                        <option value="">— بدون مستفيد محدد —</option>
                                        @foreach($campaignBeneficiaryOptions as $beneficiaryOption)
                                            <option value="{{ $beneficiaryOption->id }}" @selected(old('beneficiary_id') == $beneficiaryOption->id)>
                                                {{ $beneficiaryOption->full_name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('beneficiary_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">تاريخ الصرف</label>
                                    <input type="date" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror"
                                        value="{{ old('paid_at', now()->toDateString()) }}">
                                    @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                                <div class="col-12">
                                    <label class="form-label">وصف المصروف</label>
                                    <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                        rows="3" placeholder="أضف تفاصيل المصروف...">{{ old('description') }}</textarea>
                                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-danger" @disabled($campaignExpenseTreasuries->isEmpty())>
                            <i class="bi bi-check-lg me-1"></i> حفظ المصروف
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    <!-- Manager Modal -->
    <div class="modal fade" id="managerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.setManager', $campaign) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تعيين مدير الحملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المدير</label>
                        <select name="manager_user_id" class="form-select">
                            <option value="">اختر مستخدم...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" @selected($campaign->manager_user_id == $u->id)>{{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صورة المدير (اختياري)</label>
                        <input type="file" name="manager_photo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Volunteer Modal -->
    <div class="modal fade" id="volunteerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.attachVolunteer', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة متطوع للحملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتطوع</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر متطوع...</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الدور</label>
                        <input type="text" name="role" class="form-control" placeholder="مثال: مشرف، مساعد...">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">ساعات</label>
                            <input type="number" step="0.5" name="hours" class="form-control" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">تاريخ البدء</label>
                            <input type="date" name="started_at" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daily Menu Modal -->
    <div class="modal fade" id="dailyMenuModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.storeDailyMenu', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة قائمة إطعام</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">التاريخ</label>
                            <input type="date" name="day_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">المسؤول</label>
                            <select name="responsible_user_id" class="form-select">
                                <option value="">اختر مسؤول...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">نوع الوجبة</label>
                            <input list="mealTypeOptions" name="meal_type" class="form-control"
                                placeholder="اختر أو اكتب...">
                            <datalist id="mealTypeOptions">
                                <option value="إفطار">
                                <option value="سحور">
                                <option value="عشاء">
                                <option value="بروتين">
                                <option value="نشويات">
                                <option value="خضار">
                            </datalist>
                        </div>
                        <div class="col-6">
                            <label class="form-label">العدد</label>
                            <input type="number" name="meal_count" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">محتوى الوجبة (المنيو)</label>
                        <input type="text" name="menu" class="form-control" placeholder="مثال: أرز ودجاج...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكميات / المكونات</label>
                        <textarea name="ingredients" class="form-control" rows="3"
                            placeholder="تفاصيل الكميات والمقادير..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Volunteer Modal -->
    <div class="modal fade" id="monthlyVolunteerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.storeMonthlyVolunteer', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة متطوع للشهر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتطوع</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر متطوع...</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">الشهر</label>
                            <select name="month" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($m == date('n'))>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">السنة</label>
                            <select name="year" class="form-select">
                                @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" @selected($y == date('Y'))>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $ = window.jQuery;
            const hasSelect2 = Boolean($ && $.fn && $.fn.select2);

            if (hasSelect2) {
                $('#campaign-allocation-type, #campaign-child-sponsorship-type').select2({
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
                typeSelectId: 'campaign-allocation-type',
                fieldId: 'campaign-allocated-beneficiaries-field',
                listId: 'campaign-allocated-beneficiaries',
                labelId: 'campaign-allocated-beneficiaries-label',
                helpId: 'campaign-allocated-beneficiaries-help',
                singleType: 'شخص واحد',
                multipleType: 'أكثر من مستفيد',
                singleLabel: 'اختر المستفيد',
                multipleLabel: 'اختر المستفيدين'
            });

            configurePeopleList({
                typeSelectId: 'campaign-child-sponsorship-type',
                fieldId: 'campaign-sponsors-field',
                listId: 'campaign-sponsors-list',
                labelId: 'campaign-sponsors-label',
                helpId: 'campaign-sponsors-help',
                singleType: 'كافل واحد',
                multipleType: 'أكثر من كافل',
                singleLabel: 'اختر الكافل',
                multipleLabel: 'اختر الكفلاء'
            });

            const donorMode = document.getElementById('campaign-donor-mode');
            const existingDonorField = document.getElementById('campaign-existing-donor-field');
            const donorId = document.getElementById('campaign-donor-id');
            const newDonorFields = document.getElementById('campaign-new-donor-fields');
            const newDonorName = document.getElementById('campaign-new-donor-name');
            const newDonorPhone = document.getElementById('campaign-new-donor-phone');

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

            const donationType = document.getElementById('campaign-donation-type');
            const cashFields = document.querySelectorAll('.campaign-donation-cash-field');
            const inKindFields = document.querySelectorAll('.campaign-donation-in-kind-field');
            const cashRequiredIds = [
                'campaign-donation-amount',
                'campaign-donation-cash-channel',
                'campaign-donation-receipt',
                'campaign-donation-treasury'
            ];
            const inKindRequiredIds = [
                'campaign-donation-estimated-value',
                'campaign-donation-warehouse'
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
                cashRequiredIds.forEach(id => {
                    const field = document.getElementById(id);
                    if (field) field.required = isCash;
                });
                inKindRequiredIds.forEach(id => {
                    const field = document.getElementById(id);
                    if (field) field.required = !isCash;
                });
            }

            donationType?.addEventListener('change', updateDonationType);
            updateDonationType();

            const previousFormContext = @json(old('form_context'));
            const modalId = previousFormContext === 'campaign_donation'
                ? 'campaignDonationModal'
                : (previousFormContext === 'campaign_expense' ? 'campaignExpenseModal' : null);

            if (modalId && window.bootstrap) {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            }
        });
    </script>
@endsection

