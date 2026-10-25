@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>يرجى مراجعة الملاحظات:</strong>
        <ul class="mb-0 mt-1 small">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animate-reveal-down px-2 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge rounded-pill px-3 py-1.5 fw-bold fs-7 shadow-xs d-inline-flex align-items-center gap-1" style="background-color: #047857 !important; color: #ffffff !important; font-weight: 800; border: 1px solid #065f46 !important;">
                    <i class="bi bi-shield-heart"></i>
                    <span>مؤسسة إنسان الخيرية</span>
                </span>
                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis px-2.5 py-1 rounded-pill fw-bold fs-7">
                    المرحلة الأولى
                </span>
            </div>
            <h1 class="h2 fw-800 text-stats-main mb-1">منظومة البحث المبدئي للحالات</h1>
            <p class="text-muted-theme small mb-0">استقبال وفلترة طلبات الحالات والمساعدات (في انتظار البحث - تحت البحث - تم البحث - مرفوض)</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <a href="{{ route('cases.apply-zad') }}" target="_blank" class="btn btn-outline-success rounded-pill px-3 py-2 fw-bold small">
                <i class="bi bi-basket2-fill me-1"></i> استمارة زاد (الأيتام)
            </a>

            <a href="{{ route('cases.apply-midrar') }}" target="_blank" class="btn btn-outline-info rounded-pill px-3 py-2 fw-bold small">
                <i class="bi bi-droplet-half me-1"></i> استمارة مدرار (مياه وأسقف)
            </a>

            <a href="{{ route('cases.apply-hope') }}" target="_blank" class="btn rounded-pill px-3 py-2 fw-bold small" style="color: #7c3aed; border: 1px solid #7c3aed;">
                <i class="bi bi-activity me-1"></i> استمارة بعثاء الأمل (أورام)
            </a>

            <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                <i class="bi bi-plus-circle me-1"></i> تسجيل طلب بحث مبدئي جديد
            </button>

            <a href="{{ route('visits.create') }}" class="btn btn-outline-success rounded-pill px-3 py-2.5 fw-bold">
                <i class="bi bi-file-earmark-medical me-1"></i> استمارة البحث الاجتماعي
            </a>

            <!-- Bulk Action Bar -->
            <div id="bulk-action-bar" class="bulk-action-bar-elite shadow-premium d-none">
                <div class="d-flex align-items-center gap-4 bg-glass-onyx px-4 py-2 rounded-24 border border-primary animate-reveal-up">
                    <div class="selected-badge bg-primary px-3 py-1 rounded-pill">
                        <span class="text-stats-main small fw-bold"><span id="selected-count">0</span> محدد</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-800 shadow-sm btn-hover-scale" onclick="submitBulkDelete()">
                            <i class="bi bi-trash3-fill me-2"></i> حذف المحدد
                        </button>
                        <button type="button" class="btn btn-outline-light rounded-pill px-3 fw-bold" onclick="deselectAll()">إلغاء</button>
                    </div>
                </div>
            </div>
            
            <div class="form-check form-check-inline select-all-wrapper glass-badge-theme px-3 py-1 mb-0 border-0">
                <input class="form-check-input" type="checkbox" id="selectAllItems" onclick="toggleAllCheckboxes(this)">
                <label class="form-check-label fw-bold x-small ms-1" for="selectAllItems" style="cursor: pointer;">تحديد الكل</label>
            </div>

            <div class="glass-badge-theme px-3 py-2 border-0">
                <span class="fw-bold">إجمالي الحالات:</span> {{ $applications->total() }}
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-xs rounded-4 p-3 mb-4 bg-stats-card-main">
        <!-- Top Row: Source Filters (الويبسايت / السيستم / الموبايل) -->
        <div class="border-bottom border-light-subtle pb-3 mb-3">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 mb-2">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-funnel-fill text-primary"></i>
                    <span class="fw-bold small text-stats-title">فلترة حسب جهة التقديم (المصدر):</span>
                </div>
                @if(request('source'))
                    @php
                        $srcConfig = [
                            'website' => [
                                'label' => 'الموقع الإلكتروني (الويبسايت)',
                                'icon' => 'bi-globe2',
                                'bg' => '#e0f2fe',
                                'text' => '#0369a1',
                                'border' => '#7dd3fc',
                                'iconColor' => '#0284c7',
                            ],
                            'system' => [
                                'label' => 'السيستم الداخلي',
                                'icon' => 'bi-laptop',
                                'bg' => '#d1fae5',
                                'text' => '#065f46',
                                'border' => '#6ee7b7',
                                'iconColor' => '#059669',
                            ],
                            'mobile' => [
                                'label' => 'تطبيق الموبايل',
                                'icon' => 'bi-phone-fill',
                                'bg' => '#fef3c7',
                                'text' => '#92400e',
                                'border' => '#fcd34d',
                                'iconColor' => '#d97706',
                            ],
                        ][request('source')] ?? [
                            'label' => request('source'),
                            'icon' => 'bi-funnel-fill',
                            'bg' => '#f1f5f9',
                            'text' => '#334155',
                            'border' => '#cbd5e1',
                            'iconColor' => '#64748b',
                        ];
                    @endphp
                    <div class="active-source-badge d-inline-flex align-items-center gap-2 px-3 py-1.5 rounded-pill shadow-xs"
                         style="background: {{ $srcConfig['bg'] }}; border: 1.5px solid {{ $srcConfig['border'] }}; color: {{ $srcConfig['text'] }} !important; font-size: 0.84rem; font-weight: 700;">
                        <span style="width: 7px; height: 7px; border-radius: 50%; background: {{ $srcConfig['iconColor'] }}; display: inline-block;"></span>
                        <i class="bi {{ $srcConfig['icon'] }}" style="color: {{ $srcConfig['iconColor'] }}; font-size: 0.95rem;"></i>
                        <span style="color: {{ $srcConfig['text'] }} !important; opacity: 0.85; font-weight: 600;">فلتر نشط:</span>
                        <strong style="color: {{ $srcConfig['text'] }} !important; font-weight: 800;">{{ $srcConfig['label'] }}</strong>
                        <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('source', 'page'), [])) }}" 
                           class="btn-close-filter d-inline-flex align-items-center justify-content-center text-decoration-none ms-1" 
                           title="إلغاء الفلتر"
                           style="width: 20px; height: 20px; border-radius: 50%; background: rgba(0,0,0,0.1); color: {{ $srcConfig['text'] }}; font-size: 0.8rem; transition: all 0.2s;">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    </div>
                @endif
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('source', 'page'), [])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-bold {{ !request('source') ? 'btn-primary shadow-xs' : 'btn-outline-secondary' }}">
                    <i class="bi bi-grid-fill me-1"></i> جميع المصادر ({{ $sourceCounts['all'] ?? \App\Models\MobileCaseApplication::count() }})
                </a>
                <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['source' => 'website'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-bold {{ request('source') == 'website' ? 'btn-info text-white shadow-xs' : 'btn-outline-info' }}">
                    <i class="bi bi-globe2 me-1"></i> الويبسايت ({{ $sourceCounts['website'] ?? 0 }})
                </a>
                <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['source' => 'system'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-bold {{ request('source') == 'system' ? 'btn-success text-white shadow-xs' : 'btn-outline-success' }}">
                    <i class="bi bi-laptop me-1"></i> السيستم ({{ $sourceCounts['system'] ?? 0 }})
                </a>
                <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['source' => 'mobile'])) }}" 
                   class="btn btn-sm rounded-pill px-3 fw-bold {{ request('source') == 'mobile' ? 'btn-warning text-dark shadow-xs' : 'btn-outline-warning text-warning-emphasis' }}">
                    <i class="bi bi-phone-fill me-1"></i> الموبايل ({{ $sourceCounts['mobile'] ?? 0 }})
                </a>
            </div>
        </div>

        <!-- Second Row: Status, Type & Search -->
        <div class="row g-3 align-items-center">
            <!-- Status & Type Tabs -->
            <div class="col-xl-7 col-lg-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('status', 'type', 'page'), [])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ !request('status') && !request('type') ? 'btn-secondary text-white' : 'btn-outline-secondary' }}">
                        كل الحالات
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('status') == 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-warning-emphasis' }}">
                        <i class="bi bi-hourglass-split me-1"></i> في انتظار البحث ({{ \App\Models\MobileCaseApplication::where('status', 'pending')->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'under_research'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('status') == 'under_research' ? 'btn-info text-white' : 'btn-outline-info' }}">
                        <i class="bi bi-search me-1"></i> تحت البحث ({{ \App\Models\MobileCaseApplication::whereIn('status', ['under_research', 'reviewed'])->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('status') == 'approved' ? 'btn-success text-white' : 'btn-outline-success' }}">
                        <i class="bi bi-check2-circle me-1"></i> تم البحث ({{ \App\Models\MobileCaseApplication::whereIn('status', ['approved', 'completed', 'accepted'])->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('status') == 'rejected' ? 'btn-danger text-white' : 'btn-outline-danger' }}">
                        <i class="bi bi-x-circle me-1"></i> مرفوض ({{ \App\Models\MobileCaseApplication::where('status', 'rejected')->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page', 'status'), ['type' => 'zad'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('type') == 'zad' ? 'btn-success text-white' : 'btn-outline-success' }}">
                        <i class="bi bi-basket2-fill me-1"></i> زاد
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page', 'status'), ['type' => 'midrar'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('type') == 'midrar' ? 'btn-info text-white' : 'btn-outline-info' }}">
                        <i class="bi bi-droplet-half me-1"></i> مدرار
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page', 'status'), ['type' => 'hope'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold" style="{{ request('type') == 'hope' ? 'background: #7c3aed; color: #fff;' : 'border: 1px solid #7c3aed; color: #7c3aed;' }}">
                        <i class="bi bi-activity me-1"></i> بعثاء الأمل
                    </a>
                </div>
            </div>

            <!-- Search box -->
            <div class="col-xl-5 col-lg-12">
                <form method="GET" action="{{ route('mobile.case-applications.index') }}" class="d-flex gap-2">
                    @if(request('source')) <input type="hidden" name="source" value="{{ request('source') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    <div class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-start-pill" placeholder="ابحث بالاسم، الهاتف، الرقم القومي، رقم الطلب...">
                        <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    @if(request('q') || request('type') || request('status') || request('source'))
                        <a href="{{ route('mobile.case-applications.index') }}" class="btn btn-outline-secondary rounded-pill px-3 text-nowrap">إلغاء الفلاتر</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Form -->
    <form id="bulk-delete-form" action="{{ route('mobile.case-applications.bulk-destroy') }}" method="POST" class="d-none">
        @csrf
        <div id="bulk-ids-container"></div>
    </form>

    <div class="row g-4">
        @forelse($applications as $app)
        @php
            $statusKey = match($app->status) {
                'pending', 'في انتظار البحث' => 'pending',
                'under_research', 'تحت البحث', 'reviewed' => 'research',
                'approved', 'completed', 'تم البحث', 'accepted' => 'approved',
                default => 'rejected'
            };
            $srcBadge = $app->source_badge;
            $initial = mb_substr($app->applicant_name ?? 'ح', 0, 1);
        @endphp
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.04 }}s">
            <div class="case-card-executive case-card-status-{{ $statusKey }} d-flex flex-column h-100">
                <!-- Top Header: Status & Tags & Checkbox -->
                <div class="case-card-header p-3 pb-2">
                    <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                        <!-- Status Badge -->
                        @if($statusKey === 'pending')
                            <span class="case-pill-badge badge-pending">
                                <span class="case-dot-pulse dot-pending"></span> في انتظار البحث
                            </span>
                        @elseif($statusKey === 'research')
                            <span class="case-pill-badge badge-research">
                                <i class="bi bi-search me-1"></i> تحت البحث
                            </span>
                        @elseif($statusKey === 'approved')
                            <span class="case-pill-badge badge-approved">
                                <i class="bi bi-check2-circle me-1"></i> تم البحث والاعتماد
                            </span>
                        @else
                            <span class="case-pill-badge badge-rejected">
                                <i class="bi bi-x-circle-fill me-1"></i> مرفوض
                            </span>
                        @endif

                        <div class="d-flex align-items-center gap-1.5">
                            <!-- Sector / Project Badge -->
                            <span class="case-sector-badge" title="القطاع / نوع الحالة">
                                {{ $app->case_type_label }}
                            </span>
                            <div class="case-checkbox-wrapper ms-1">
                                <input type="checkbox" class="case-checkbox form-check-input m-0 cursor-pointer" value="{{ $app->id }}" onclick="updateBulkBar()">
                            </div>
                        </div>
                    </div>

                    <!-- Source Tag & ID Bar -->
                    <div class="d-flex align-items-center justify-content-between x-small text-muted pt-1.5 border-top border-light-subtle">
                        <span class="case-source-tag">
                            <i class="bi {{ $srcBadge['icon'] }} text-primary me-1"></i>
                            <span class="fw-semibold text-dark">{{ $srcBadge['label'] }}</span>
                            @if(!empty($srcBadge['sub']))
                                <span class="text-muted opacity-75">({{ $srcBadge['sub'] }})</span>
                            @endif
                        </span>
                        <span class="font-monospace fw-bold text-muted opacity-75">#{{ $app->id }}</span>
                    </div>
                </div>

                <!-- Submitter Alert Strip (If submitted on behalf of case) -->
                @if($app->submitter_type === 'other' || !empty($app->submitter_name))
                    <div class="case-submitter-strip px-3 py-1.5 mx-3 mb-2 rounded-2">
                        <i class="bi bi-person-heart text-primary me-1"></i>
                        <span class="text-muted x-small">مُقدم الطلب:</span>
                        <strong class="text-dark x-small">{{ $app->submitter_name ?? 'فاعل خير' }}</strong>
                        @if($app->submitter_relation) <span class="text-muted x-small">({{ $app->submitter_relation }})</span> @endif
                        @if($app->submitter_phone) <span class="font-monospace text-muted x-small ms-1 font-bold">({{ $app->submitter_phone }})</span> @endif
                    </div>
                @endif

                <!-- Card Body: Applicant Profile -->
                <div class="case-card-body px-3 pb-3 flex-grow-1 d-flex flex-column">
                    <div class="d-flex align-items-start gap-2.5 mb-2.5">
                        <div class="case-applicant-avatar avatar-status-{{ $statusKey }}">
                            {{ $initial }}
                        </div>
                        <div class="flex-grow-1 min-w-0">
                            <h5 class="case-applicant-title mb-1 text-truncate" title="{{ $app->applicant_name }}">
                                {{ $app->applicant_name }}
                            </h5>
                            <!-- Phone Numbers -->
                            <div class="d-flex flex-wrap align-items-center gap-1.5">
                                <a href="tel:{{ $app->applicant_phone }}" class="case-phone-link" title="اتصال بالهاتف">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span>{{ $app->applicant_phone }}</span>
                                </a>
                                @if($app->backup_phone)
                                    <span class="case-backup-phone" title="رقم بديل">
                                        <i class="bi bi-phone"></i>
                                        <span>{{ $app->backup_phone }}</span>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Meta Tags (National ID & Address) -->
                    <div class="case-meta-details mb-2.5">
                        @if($app->national_id)
                            <div class="case-meta-row mb-1">
                                <i class="bi bi-credit-card-2-front text-muted"></i>
                                <span class="text-muted x-small">الرقم القومي:</span>
                                <span class="font-monospace fw-bold text-dark x-small">{{ $app->national_id }}</span>
                            </div>
                        @endif

                        <div class="case-meta-row">
                            <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                            <span class="fw-bold x-small text-dark flex-shrink-0">{{ $app->governorate ?? 'غير محدد' }} - {{ $app->city ?? 'غير محدد' }}</span>
                            @if($app->address)
                                <span class="case-address-sub text-muted text-truncate ms-1" title="{{ $app->address }}">— {{ $app->address }}</span>
                            @endif
                        </div>
                    </div>

                    <!-- Description Box -->
                    <div class="case-description-box p-2.5 rounded-3 mb-3 flex-grow-1">
                        <p class="mb-0 text-muted lh-base x-small">
                            <i class="bi bi-chat-left-text me-1 text-primary opacity-50"></i>
                            {{ Str::limit($app->description, 135) }}
                        </p>
                    </div>

                    <!-- Rejection Alert -->
                    @if($app->status === 'rejected' && $app->rejection_reason)
                        <div class="case-rejection-alert p-2.5 rounded-3 mb-3">
                            <div class="d-flex align-items-center gap-1.5 fw-bold text-danger mb-1 x-small">
                                <i class="bi bi-exclamation-octagon-fill"></i>
                                <span>سبب الرفض المسجل:</span>
                            </div>
                            <div class="text-danger-emphasis x-small">{{ $app->rejection_reason }}</div>
                        </div>
                    @endif

                    <!-- Direct Social Research Visit Link if Approved -->
                    @if(in_array($app->status, ['approved', 'completed', 'accepted']))
                        <div class="mb-2">
                            <a href="{{ route('visits.create', ['application_id' => $app->id]) }}" class="btn case-btn-visit w-100 fw-bold py-2 shadow-xs">
                                <i class="bi bi-file-earmark-check-fill me-1"></i> استمارة البحث الاجتماعي الميداني
                            </a>
                        </div>
                    @endif

                    <!-- Action Button: Open Modal -->
                    <div class="mt-auto">
                        <button class="btn case-btn-review w-100 fw-bold py-2.5 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}">
                            <i class="bi bi-pencil-square me-1.5"></i> مراجعة وتحديث حالة البحث
                        </button>
                    </div>
                </div>

                <!-- Footer -->
                <div class="case-card-footer px-3 py-2 border-top border-light-subtle d-flex align-items-center justify-content-between x-small text-muted">
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-calendar3"></i> {{ $app->created_at->format('Y-m-d') }}
                    </span>
                    <span class="d-flex align-items-center gap-1">
                        <i class="bi bi-clock"></i> {{ $app->created_at->diffForHumans() }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Detail & Decision Modal -->
        <div class="modal fade" id="modal{{ $app->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-title">
                            <i class="bi bi-clipboard2-check me-2 text-primary"></i> مراجعة وقرار البحث المبدئي
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-stats-card-main">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">اسم صاحب الحالة (المستفيد)</label>
                                <div class="text-stats-main fw-bold fs-6">{{ $app->applicant_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">رقم الهاتف</label>
                                <div class="font-outfit text-primary fw-bold fs-5">{{ $app->applicant_phone }}</div>
                            </div>

                            @if($app->submitter_name)
                                <div class="col-12 p-3 bg-light rounded-3 border">
                                    <div class="fw-bold text-primary mb-1"><i class="bi bi-person-heart me-1"></i> مُقدم الطلب نيابة عن الحالة:</div>
                                    <div>{{ $app->submitter_name }} ({{ $app->submitter_relation ?? 'غير محدد' }}) — هاتف: {{ $app->submitter_phone ?? '-' }}</div>
                                </div>
                            @endif

                            <div class="col-md-3 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">المشروع / نوع المساعدة</label>
                                <div class="fw-bold">{{ $app->case_type_label }}</div>
                            </div>
                            <div class="col-md-3 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">جهة تقديم الطلب (المصدر)</label>
                                <div>
                                    <span class="badge {{ $app->source_badge['class'] }} rounded-pill px-2.5 py-1 fw-bold">
                                        <i class="bi {{ $app->source_badge['icon'] }} me-1"></i> {{ $app->source_badge['label'] }} ({{ $app->source_badge['sub'] }})
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-3 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">الرقم القومي</label>
                                <div class="font-monospace fw-bold">{{ $app->national_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-3 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">المحافظة والمركز</label>
                                <div class="fw-bold">{{ $app->governorate ?? '-' }} - {{ $app->city ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">العنوان التفصيلي</label>
                                <div class="text-stats-main">{{ $app->address ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">وصف الحالة وظروف الأسرة</label>
                                <div class="p-3 bg-stats-inner-item border rounded-3 small">
                                    {{ $app->description }}
                                </div>
                            </div>

                            @if($app->case_type === 'zad' || $app->deceased_husband_name || $app->death_details)
                                <div class="col-12 p-3 rounded-3 border border-success bg-success bg-opacity-10 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-success border-opacity-25 pb-2">
                                        <h6 class="fw-bold text-success mb-0"><i class="bi bi-basket2-fill me-1"></i> بيانات استمارة زاد (الأيتام) 2026:</h6>
                                        @if($app->reference_number)
                                            <span class="badge bg-success text-white">رقم الدليل: {{ $app->reference_number }}</span>
                                        @endif
                                    </div>
                                    <div class="row g-2 small">
                                        <div class="col-md-6"><strong>اسم المصدر:</strong> {{ $app->source_name ?? '-' }}</div>
                                        <div class="col-md-6"><strong>اسم الأم:</strong> {{ $app->mother_name ?? $app->applicant_name }}</div>
                                        <div class="col-md-6"><strong>اسم الزوج المتوفي:</strong> {{ $app->deceased_husband_name ?? '-' }}</div>
                                        <div class="col-md-6"><strong>مين مقدم للأم (صفته):</strong> {{ $app->submitter_relation_to_mother ?? $app->submitter_relation ?? '-' }}</div>
                                        <div class="col-md-6"><strong>عدد الأولاد:</strong> <span class="badge bg-danger">{{ $app->children_count ?? '-' }}</span></div>
                                        @if($app->death_details)
                                            <div class="col-12"><strong>تفاصيل الوفاة والعمل:</strong> {{ $app->death_details }}</div>
                                        @endif
                                        @if($app->transit_details)
                                            <div class="col-12"><strong>العنوان والمواصلات (الموقف):</strong> {{ $app->transit_details }}</div>
                                        @endif
                                        @if($app->children_details)
                                            <div class="col-12"><strong>أسماء الأطفال وسنهم ومصاريفهم وأمراضهم:</strong> {{ $app->children_details }}</div>
                                        @endif
                                        @if($app->income_aid_sources)
                                            <div class="col-12"><strong>المعاشات والمساعدات:</strong> {{ $app->income_aid_sources }}</div>
                                        @endif
                                        @if($app->expenses_debts_details)
                                            <div class="col-12"><strong>المصاريف والإيجار والديون:</strong> {{ $app->expenses_debts_details }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($app->case_type === 'midrar' || $app->need_type || $app->midrar_details)
                                <div class="col-12 p-3 rounded-3 border border-info bg-info bg-opacity-10 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom border-info border-opacity-25 pb-2">
                                        <h6 class="fw-bold text-info-emphasis mb-0"><i class="bi bi-droplet-half me-1"></i> بيانات استمارة مشروع مدرار (وصلات مياه وأسقف):</h6>
                                        @if($app->need_type)
                                            <span class="badge bg-primary text-white fs-7">{{ $app->need_type }}</span>
                                        @endif
                                    </div>
                                    <div class="row g-2 small">
                                        <div class="col-md-6"><strong>اسم المسجل للحالة:</strong> {{ $app->submitter_name ?? '-' }}</div>
                                        <div class="col-md-6"><strong>رقم تليفون المسجل:</strong> {{ $app->submitter_phone ?? '-' }}</div>
                                        <div class="col-md-6"><strong>اسم الحالة رباعي:</strong> {{ $app->applicant_name }}</div>
                                        <div class="col-md-6"><strong>رقم تليفون الحالة:</strong> {{ $app->applicant_phone }}</div>
                                        <div class="col-md-6"><strong>المركز:</strong> {{ $app->city ?? '-' }}</div>
                                        <div class="col-md-6"><strong>عدد أفراد الأسرة:</strong> <span class="badge bg-secondary">{{ $app->family_members_count ?? $app->children_count ?? '-' }}</span></div>
                                        <div class="col-12"><strong>العنوان بالتفصيل:</strong> {{ $app->address }}</div>
                                        @if($app->social_status_description)
                                            <div class="col-12"><strong>وصف الحالة الاجتماعية:</strong> {{ $app->social_status_description }}</div>
                                        @endif
                                        @if($app->guide_name || $app->guide_phone)
                                            <div class="col-12"><strong>بيانات الدليل:</strong> {{ $app->guide_name ?? '-' }} (هاتف: {{ $app->guide_phone ?? '-' }})</div>
                                        @endif
                                    </div>
                                </div>
                            @endif

                            @if($app->case_type === 'hope' || $app->tumor_location || $app->hospital_name || $app->tumor_details)
                                <div class="col-12 p-3 rounded-3 border mb-3" style="background: rgba(124, 58, 237, 0.08); border-color: rgba(124, 58, 237, 0.3);">
                                    <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2" style="border-color: rgba(124, 58, 237, 0.2);">
                                        <h6 class="fw-bold mb-0" style="color: #6d28d9;"><i class="bi bi-activity me-1"></i> بيانات استمارة بعثاء الأمل (رعاية مرضى الأورام):</h6>
                                        @if($app->tumor_location)
                                            <span class="badge text-white" style="background: #7c3aed;">{{ $app->tumor_location }}</span>
                                        @endif
                                    </div>
                                    <div class="row g-2 small">
                                        <div class="col-md-6"><strong>اسم المريض:</strong> {{ $app->applicant_name }}</div>
                                        <div class="col-md-6"><strong>المستشفى المتابع بها:</strong> {{ $app->hospital_name ?? '-' }}</div>
                                        <div class="col-md-6"><strong>مكان / نوع الورم:</strong> {{ $app->tumor_location ?? '-' }}</div>
                                        <div class="col-md-6"><strong>المرافق / ولي الأمر:</strong> {{ $app->submitter_name ?? '-' }} ({{ $app->submitter_relation ?? 'مرافق' }})</div>
                                        @if($app->treatment_protocol)
                                            <div class="col-12"><strong>البروتوكول وخطة العلاج:</strong> {{ $app->treatment_protocol }}</div>
                                        @endif
                                        @if($app->transit_details)
                                            <div class="col-12"><strong>المواصلات والموقف:</strong> {{ $app->transit_details }}</div>
                                        @endif
                                        @if(!empty($app->tumor_details['chemo_sessions_taken']) || !empty($app->tumor_details['chemo_sessions_remaining']))
                                            <div class="col-md-6"><strong>جلسات الكيماوي:</strong> أخذ {{ $app->tumor_details['chemo_sessions_taken'] ?? 0 }} / متبقي {{ $app->tumor_details['chemo_sessions_remaining'] ?? 0 }}</div>
                                        @endif
                                        @if(!empty($app->tumor_details['radiation_sessions_taken']) || !empty($app->tumor_details['radiation_sessions_remaining']))
                                            <div class="col-md-6"><strong>جلسات الإشعاعي:</strong> أخذ {{ $app->tumor_details['radiation_sessions_taken'] ?? 0 }} / متبقي {{ $app->tumor_details['radiation_sessions_remaining'] ?? 0 }}</div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Decision Panel -->
                        <div class="admin-panel p-4 rounded-4 bg-stats-inner-item border">
                            <h6 class="mb-3 text-stats-main fw-bold border-start border-primary border-4 ps-3">
                                <i class="bi bi-shield-check me-2 text-primary"></i> قرار وتحديث نتيجة البحث المبدئي
                            </h6>

                            <form action="{{ route('mobile.case-applications.update', $app->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">حالة البحث المبدئي <span class="text-danger">*</span></label>
                                        <select name="status" id="statusSelect{{ $app->id }}" class="form-select p-2.5 fw-bold" onchange="toggleRejectionBox({{ $app->id }})">
                                            <option value="pending" {{ in_array($app->status, ['pending', 'في انتظار البحث']) ? 'selected' : '' }}>⏳ في انتظار البحث</option>
                                            <option value="under_research" {{ in_array($app->status, ['under_research', 'تحت البحث', 'reviewed']) ? 'selected' : '' }}>🔍 تحت البحث</option>
                                            <option value="approved" {{ in_array($app->status, ['approved', 'completed', 'تم البحث', 'accepted']) ? 'selected' : '' }}>✅ تم البحث (اعتماد للحث الميداني)</option>
                                            <option value="rejected" {{ in_array($app->status, ['rejected', 'مرفوض']) ? 'selected' : '' }}>❌ مرفوض</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check pb-2">
                                            <input class="form-check-input" type="checkbox" name="start_visit" value="1" id="startVisit{{ $app->id }}">
                                            <label class="form-check-label fw-bold small text-success" for="startVisit{{ $app->id }}">
                                                التحويل فوراً إلى استمارة البحث الميداني بعد الحفظ
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Rejection Reason Input -->
                                    <div class="col-12 {{ in_array($app->status, ['rejected', 'مرفوض']) ? '' : 'd-none' }}" id="rejectionReasonBox{{ $app->id }}">
                                        <label class="form-label small fw-bold text-danger">سبب الرفض <span class="text-danger">* (إلزامي في حال الرفض)</span></label>
                                        <textarea name="rejection_reason" id="rejectionInput{{ $app->id }}" class="form-control border-danger" rows="2" placeholder="اكتب سبب رفض الطلب بالتفصيل لتسجيله رسمياً...">{{ $app->rejection_reason }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold">ملاحظات إدارية / توجيهات الباحث المبدئي</label>
                                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="ملاحظات وتوجيهات تخص الحالة...">{{ $app->admin_notes }}</textarea>
                                    </div>

                                    <div class="col-12 mt-4 d-flex justify-content-between gap-3">
                                        <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill fw-bold py-2.5 shadow-sm">
                                            <i class="bi bi-save me-1"></i> حفظ وتحديث نتيجة البحث
                                        </button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟')) document.getElementById('del-form-{{ $app->id }}').submit()">
                                            <i class="bi bi-trash3 me-1"></i> حذف
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <form id="del-form-{{ $app->id }}" action="{{ route('mobile.case-applications.destroy', $app->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 animate-up">
            <div class="empty-state-lux text-center py-5">
                <div class="empty-icon-wrapper mb-4 text-center d-flex justify-content-center">
                    <i class="bi bi-search" style="font-size: 4.5rem; opacity: 0.25;"></i>
                </div>
                <h5 class="fw-800 text-stats-main">لا توجد طلبات بحث مبدئي مطابقة</h5>
                <p class="text-muted-theme px-4 px-md-5">لم يتم العثور على طلبات بحث مبدئي وفقاً للفلاتر المحددة.</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                        <i class="bi bi-plus-circle me-1"></i> تسجيل طلب جديد الآن
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $applications->links() }}
    </div>
</div>

<!-- Modal: تسجيل طلب بحث مبدئي جديد -->
<div class="modal fade" id="createCaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> تسجيل طلب بحث مبدئي جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('mobile.case-applications.store') }}">
                    @csrf

                    <!-- صفة مقدم الطلب -->
                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <label class="form-label fw-bold mb-2">جهة التقديم:</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="submitter_type" id="newSubSelf" value="self" checked onchange="toggleNewSubmitter()">
                                <label class="form-check-label fw-bold" for="newSubSelf">مقدم من الحالة نفسها</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="submitter_type" id="newSubOther" value="other" onchange="toggleNewSubmitter()">
                                <label class="form-check-label fw-bold" for="newSubOther">مقدم نيابة عن الحالة (فاعل خير / جار)</label>
                            </div>
                        </div>
                    </div>

                    <div id="newSubmitterFields" class="mb-3 p-3 rounded-3 border border-primary bg-primary bg-opacity-10 d-none">
                        <h6 class="fw-bold text-primary mb-2">بيانات مقدم الطلب نيابة عن الحالة</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">اسم مقدم الطلب</label>
                                <input type="text" name="submitter_name" class="form-control form-control-sm" placeholder="الاسم بالكامل">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">هاتف مقدم الطلب</label>
                                <input type="tel" name="submitter_phone" class="form-control form-control-sm" placeholder="01xxxxxxxxx">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">صلته بالحالة</label>
                                <input type="text" name="submitter_relation" class="form-control form-control-sm" placeholder="جار / قريب...">
                            </div>
                        </div>
                    </div>

                    <!-- بيانات الحالة -->
                    <h6 class="fw-bold border-bottom pb-2 mb-3">بيانات المستفيد (صاحب الحالة)</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">اسم المستفيد بالكامل <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_name" class="form-control" required placeholder="اسم الحالة أو ولي الأمر">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الرقم القومي (14 رقم)</label>
                            <input type="text" name="national_id" class="form-control" maxlength="14" placeholder="الرقم القومي إن وجد">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم الهاتف الأساسي <span class="text-danger">*</span></label>
                            <input type="tel" name="applicant_phone" class="form-control" required placeholder="01xxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم هاتف بديل</label>
                            <input type="tel" name="backup_phone" class="form-control" placeholder="رقم هاتف إضافي">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">نوع المساعدة / المشروع</label>
                            <select name="case_type" id="newCaseType" class="form-select" onchange="toggleCaseCreateFields(this.value)">
                                <option value="zad" selected>كفالة أيتام (مشروع زاد)</option>
                                <option value="midrar">مشروع مدرار (وصلات مياه / أسقف)</option>
                                <option value="hope">مرضى أورام وعلاج (بعثاء الأمل)</option>
                                <option value="roof">سقف وترميم منازل</option>
                                <option value="financial">مساعدة مالية</option>
                                <option value="food">مساعدات عينية وغذائية</option>
                                <option value="medical">علاج وأدوية</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>

                        <!-- حقول استمارة زاد التخصصية -->
                        <div id="newZadFields" class="col-12 p-3 rounded-3 border border-success bg-success bg-opacity-10">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-basket2-fill me-1"></i> حقول استمارة زاد (الأيتام) 2026:</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">اسم المصدر</label>
                                    <input type="text" name="source_name" class="form-control form-control-sm" placeholder="اسم المصدر / الدال على الحالة">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">رقم المسجل / رقم الدليل</label>
                                    <input type="text" name="reference_number" class="form-control form-control-sm" placeholder="رقم القيد أو الدليل">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">اسم الزوج المتوفي</label>
                                    <input type="text" name="deceased_husband_name" class="form-control form-control-sm" placeholder="اسم الزوج المتوفي رباعياً">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">مين مقدم للأم (صفته)</label>
                                    <input type="text" name="submitter_relation_to_mother" class="form-control form-control-sm" placeholder="صلة مقدم الطلب بالأم">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">عدد الأولاد (ممنوع لطفل واحد)</label>
                                    <select name="children_count" class="form-select form-select-sm">
                                        <option value="2">2 أولاد</option>
                                        <option value="3">3 أولاد</option>
                                        <option value="4">4 أولاد</option>
                                        <option value="5">5 أولاد</option>
                                        <option value="غير ذلك">غير ذلك (أكثر من 5)</option>
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold">العنوان والمواصلات (البلد / موقف إيه / بيتركب ليها إيه)</label>
                                    <input type="text" name="transit_details" class="form-control form-control-sm" placeholder="الموقف والمواصلات بالتفصيل">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">متوفى من إمتى / كان شغال إيه / سبب الوفاة</label>
                                    <textarea name="death_details" class="form-control form-control-sm" rows="2" placeholder="تاريخ وسبب الوفاة وعمل الزوج..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">أسماء الأطفال وسنهم ومصاريف دروسهم والأمراض المزمنة</label>
                                    <textarea name="children_details" class="form-control form-control-sm" rows="2" placeholder="أسماء الأطفال وأعمارهم ومصاريفهم وأمراضهم..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">الدخل والمعاشات (تكافل / تأمين / مساعدات جمعيات / أهلها / أهل زوجها / جيران / مساجد)</label>
                                    <textarea name="income_aid_sources" class="form-control form-control-sm" rows="2" placeholder="تفاصيل المعاشات والمساعدات ومصادر الدخل..."></textarea>
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">الحالة الاجتماعية للأم والمصاريف (كهرباء، غاز، مياه، إيجار، ديون، العجز)</label>
                                    <textarea name="expenses_debts_details" class="form-control form-control-sm" rows="2" placeholder="الحالة الاجتماعية والمرافق والإيجار والديون..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- حقول استمارة مشروع مدرار التخصصية -->
                        <div id="newMidrarFields" class="col-12 p-3 rounded-3 border border-info bg-info bg-opacity-10 d-none">
                            <h6 class="fw-bold text-info-emphasis mb-2"><i class="bi bi-droplet-half me-1"></i> حقول استمارة مشروع مدرار (وصلات مياه وأسقف):</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">تحديد الاحتياج المطلوب للحالة <span class="text-danger">*</span></label>
                                    <select name="need_type" class="form-select form-select-sm">
                                        <option value="وصله مياه">وصله مياه (توصيل مياه شرب نقية)</option>
                                        <option value="سقف">سقف (تسقيف منزل لحماية الأسرة)</option>
                                        <option value="اخري">اخري (صدقات جارية ووقف خيري)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">عدد أفراد الأسرة المقيمين بالمنزل</label>
                                    <input type="number" name="family_members_count" class="form-control form-control-sm" min="1" max="30" placeholder="مثال: 5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">اسم الدليل (شخص يدِل على المنزل بالقرية)</label>
                                    <input type="text" name="guide_name" class="form-control form-control-sm" placeholder="اسم الدليل">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">رقم تليفون الدليل</label>
                                    <input type="tel" name="guide_phone" class="form-control form-control-sm" placeholder="01xxxxxxxxx">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">وصف الحالة الاجتماعية والوضع المعيشي وحالة المسكن</label>
                                    <textarea name="social_status_description" class="form-control form-control-sm" rows="2" placeholder="وصف الحالة الاجتماعية بالتفصيل..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- حقول استمارة بعثاء الأمل (الأورام) -->
                        <div id="newHopeFields" class="col-12 p-3 rounded-3 border d-none" style="background: rgba(124, 58, 237, 0.08); border-color: rgba(124, 58, 237, 0.3);">
                            <h6 class="fw-bold mb-2" style="color: #6d28d9;"><i class="bi bi-activity me-1"></i> حقول مشروع بعثاء الأمل (رعاية مرضى الأورام):</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">المستشفى المتابع بها <span class="text-danger">*</span></label>
                                    <input type="text" name="hospital_name" class="form-control form-control-sm" placeholder="مثال: معهد أورام طنطا، 57357...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">مكان ونوع الورم بالجسم <span class="text-danger">*</span></label>
                                    <input type="text" name="tumor_location" class="form-control form-control-sm" placeholder="مثال: ثدي، رئة، لوكيميا...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">البروتوكول العلاجي الحالي وخطة العلاج</label>
                                    <textarea name="treatment_protocol" class="form-control form-control-sm" rows="2" placeholder="الجرعات وجداول العلاج الكيماوي / الإشعاعي..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">المحافظة</label>
                            <input type="text" name="governorate" class="form-control" placeholder="القاهرة / الجيزة...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">المركز / المدينة</label>
                            <input type="text" name="city" class="form-control" placeholder="المدينة أو المركز">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">العنوان التفصيلي</label>
                            <input type="text" name="address" class="form-control" placeholder="القرية / الشارع / علامة مميزة">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">وصف الحالة والاحتياجات <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="شرح موجز لظروف الأسرة ومبررات الطلب..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الحالة الأولية للطلب</label>
                            <select name="status" class="form-select">
                                <option value="pending">⏳ في انتظار البحث</option>
                                <option value="under_research">🔍 تحت البحث</option>
                                <option value="approved">✅ تم البحث (تحويل للبحث الميداني)</option>
                                <option value="rejected">❌ مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check pb-2">
                                <input class="form-check-input" type="checkbox" name="start_visit" value="1" id="newStartVisit">
                                <label class="form-check-label fw-bold small text-success" for="newStartVisit">
                                    التحويل فوراً إلى استمارة البحث الميداني
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> حفظ وتسجيل الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRejectionBox(appId) {
    const select = document.getElementById('statusSelect' + appId);
    const box = document.getElementById('rejectionReasonBox' + appId);
    const input = document.getElementById('rejectionInput' + appId);
    if (select.value === 'rejected') {
        box.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        box.classList.add('d-none');
        input.removeAttribute('required');
    }
}

function toggleNewSubmitter() {
    const isOther = document.getElementById('newSubOther').checked;
    const box = document.getElementById('newSubmitterFields');
    if (isOther) {
        box.classList.remove('d-none');
    } else {
        box.classList.add('d-none');
    }
}

function toggleCaseCreateFields(val) {
    const zadBox = document.getElementById('newZadFields');
    const midrarBox = document.getElementById('newMidrarFields');
    const hopeBox = document.getElementById('newHopeFields');

    if (zadBox) zadBox.classList.toggle('d-none', val !== 'zad');
    if (midrarBox) midrarBox.classList.toggle('d-none', val !== 'midrar');
    if (hopeBox) hopeBox.classList.toggle('d-none', val !== 'hope');
}

function toggleZadCreateFields(val) {
    toggleCaseCreateFields(val);
}

function toggleAllCheckboxes(master) {
    document.querySelectorAll('.case-checkbox').forEach(cb => cb.checked = master.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.case-checkbox:checked');
    const bar = document.getElementById('bulk-action-bar');
    const count = document.getElementById('selected-count');
    if (count) count.textContent = checked.length;
    if (bar) {
        if (checked.length > 0) bar.classList.remove('d-none');
        else bar.classList.add('d-none');
    }
}

function deselectAll() {
    document.querySelectorAll('.case-checkbox').forEach(cb => cb.checked = false);
    const master = document.getElementById('selectAllItems');
    if (master) master.checked = false;
    updateBulkBar();
}

function submitBulkDelete() {
    const checked = document.querySelectorAll('.case-checkbox:checked');
    if (!checked.length) return;
    if (!confirm('هل أنت متأكد من حذف الحالات المحددة (' + checked.length + ' حالة)؟')) return;
    const container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
    document.getElementById('bulk-delete-form').submit();
}
</script>

<style>
/* ========================================================
   Executive Case Applications Cards UI System
   ======================================================== */
.case-card-executive {
    background: var(--bg-card, #ffffff);
    border: 1px solid var(--border-color, rgba(0, 0, 0, 0.08));
    border-radius: 18px;
    position: relative;
    overflow: hidden;
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.04);
}
.theme-dark .case-card-executive {
    background: rgba(255, 255, 255, 0.035);
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.2);
}
.case-card-executive:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.08);
}
.theme-dark .case-card-executive:hover {
    box-shadow: 0 14px 30px rgba(0, 0, 0, 0.35);
}

/* Status Top Indicator Accent Lines */
.case-card-executive::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    z-index: 2;
}
.case-card-status-pending::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
.case-card-status-research::before { background: linear-gradient(90deg, #0284c7, #38bdf8); }
.case-card-status-approved::before { background: linear-gradient(90deg, #059669, #10b981); }
.case-card-status-rejected::before { background: linear-gradient(90deg, #dc2626, #f87171); }

/* Header & Status Badges */
.case-card-header {
    background: rgba(0, 0, 0, 0.015);
}
.theme-dark .case-card-header {
    background: rgba(255, 255, 255, 0.02);
}

.case-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 9999px;
    font-size: 0.76rem;
    font-weight: 800;
    line-height: 1.2;
    letter-spacing: -0.2px;
}
.badge-pending {
    background: #fffbeb;
    color: #b45309 !important;
    border: 1px solid #fde68a;
}
.theme-dark .badge-pending {
    background: rgba(245, 158, 11, 0.18);
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.35);
}

.badge-research {
    background: #f0f9ff;
    color: #0369a1 !important;
    border: 1px solid #bae6fd;
}
.theme-dark .badge-research {
    background: rgba(2, 132, 199, 0.18);
    color: #7dd3fc !important;
    border-color: rgba(2, 132, 199, 0.35);
}

.badge-approved {
    background: #ecfdf5;
    color: #047857 !important;
    border: 1px solid #a7f3d0;
}
.theme-dark .badge-approved {
    background: rgba(16, 185, 129, 0.18);
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.35);
}

.badge-rejected {
    background: #fef2f2;
    color: #b91c1c !important;
    border: 1px solid #fecaca;
}
.theme-dark .badge-rejected {
    background: rgba(239, 68, 68, 0.18);
    color: #fca5a5 !important;
    border-color: rgba(239, 68, 68, 0.35);
}

/* Pulsing Dot */
.case-dot-pulse {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    display: inline-block;
}
.dot-pending {
    background: #f59e0b;
    box-shadow: 0 0 0 2px rgba(245, 158, 11, 0.25);
    animation: livePulseAmber 2s infinite ease-in-out;
}
@keyframes livePulseAmber {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.3); opacity: 0.6; }
}

/* Sector / Project Badge */
.case-sector-badge {
    background: rgba(0, 0, 0, 0.04);
    color: var(--text-main, #334155);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 8px;
    padding: 3px 8px;
    font-size: 0.72rem;
    font-weight: 700;
}
.theme-dark .case-sector-badge {
    background: rgba(255, 255, 255, 0.06);
    color: #cbd5e1;
    border-color: rgba(255, 255, 255, 0.1);
}

/* Source Tag */
.case-source-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

/* Submitter Alert Strip */
.case-submitter-strip {
    background: rgba(5, 150, 105, 0.07);
    border: 1px solid rgba(5, 150, 105, 0.2);
}
.theme-dark .case-submitter-strip {
    background: rgba(5, 150, 105, 0.12);
    border-color: rgba(5, 150, 105, 0.25);
}

/* Avatar / Initial */
.case-applicant-avatar {
    width: 44px;
    height: 44px;
    border-radius: 13px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.15rem;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.avatar-status-pending { background: #fef3c7; color: #b45309; border: 1.5px solid #fde68a; }
.avatar-status-research { background: #e0f2fe; color: #0369a1; border: 1.5px solid #bae6fd; }
.avatar-status-approved { background: #d1fae5; color: #047857; border: 1.5px solid #a7f3d0; }
.avatar-status-rejected { background: #fee2e2; color: #b91c1c; border: 1.5px solid #fecaca; }

.theme-dark .avatar-status-pending { background: rgba(245, 158, 11, 0.2); color: #fbbf24; border-color: rgba(245, 158, 11, 0.4); }
.theme-dark .avatar-status-research { background: rgba(2, 132, 199, 0.2); color: #7dd3fc; border-color: rgba(2, 132, 199, 0.4); }
.theme-dark .avatar-status-approved { background: rgba(16, 185, 129, 0.2); color: #6ee7b7; border-color: rgba(16, 185, 129, 0.4); }
.theme-dark .avatar-status-rejected { background: rgba(239, 68, 68, 0.2); color: #fca5a5; border-color: rgba(239, 68, 68, 0.4); }

/* Applicant Title */
.case-applicant-title {
    font-size: 1.05rem;
    font-weight: 800;
    color: var(--text-main, #0f172a);
    line-height: 1.3;
}
.theme-dark .case-applicant-title {
    color: #f8fafc;
}

/* Phone Pills */
.case-phone-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #ecfdf5;
    color: #047857 !important;
    border: 1px solid #a7f3d0;
    padding: 2px 8px;
    border-radius: 7px;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: system-ui, -apple-system, sans-serif;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.case-phone-link:hover {
    background: #047857;
    color: #ffffff !important;
    transform: scale(1.02);
}
.theme-dark .case-phone-link {
    background: rgba(16, 185, 129, 0.16);
    color: #6ee7b7 !important;
    border-color: rgba(16, 185, 129, 0.35);
}
.theme-dark .case-phone-link:hover {
    background: #10b981;
    color: #064e3b !important;
}

.case-backup-phone {
    display: inline-flex;
    align-items: center;
    gap: 3px;
    font-size: 0.74rem;
    color: #64748b;
    font-family: system-ui, -apple-system, sans-serif;
}

/* Meta Details */
.case-meta-details {
    padding: 6px 10px;
    background: rgba(0, 0, 0, 0.02);
    border-radius: 9px;
    border: 1px dashed rgba(0, 0, 0, 0.08);
}
.theme-dark .case-meta-details {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.08);
}
.case-meta-row {
    display: flex;
    align-items: center;
    gap: 5px;
    overflow: hidden;
}

/* Description Box */
.case-description-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
}
.theme-dark .case-description-box {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.06);
}

/* Rejection Alert */
.case-rejection-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
}
.theme-dark .case-rejection-alert {
    background: rgba(239, 68, 68, 0.15);
    border-color: rgba(239, 68, 68, 0.3);
}

/* Action Buttons */
.case-btn-review {
    background: linear-gradient(135deg, #059669 0%, #047857 100%);
    color: #ffffff !important;
    border: none;
    border-radius: 12px;
    font-size: 0.88rem;
    transition: all 0.25s ease;
}
.case-btn-review:hover {
    background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(4, 120, 87, 0.35) !important;
}

.case-btn-visit {
    background: #10b981;
    color: #ffffff !important;
    border: none;
    border-radius: 10px;
    font-size: 0.82rem;
    transition: all 0.2s ease;
}
.case-btn-visit:hover {
    background: #059669;
    transform: translateY(-1px);
}

/* Card Footer */
.case-card-footer {
    background: rgba(0, 0, 0, 0.015);
}
.theme-dark .case-card-footer {
    background: rgba(255, 255, 255, 0.015);
}
</style>
@endsection
