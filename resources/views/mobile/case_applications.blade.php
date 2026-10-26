@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700;800&display=swap" rel="stylesheet">

<div class="case-app-container container-fluid py-4 min-vh-100 bg-theme-page">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 border-start border-success border-4 d-flex align-items-center justify-content-between p-3" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success fs-5"></i> 
            <span class="fw-bold">{{ session('success') }}</span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4 border-0 border-start border-danger border-4 p-3" role="alert">
        <div class="d-flex align-items-center gap-2 mb-2">
            <i class="bi bi-exclamation-triangle-fill text-danger fs-5"></i> 
            <strong>يرجى مراجعة الملاحظات التالية:</strong>
        </div>
        <ul class="mb-0 small ps-4">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- 1. Executive Page Header -->
    <div class="page-executive-header mb-4 p-4 rounded-4 shadow-xs bg-stats-card-main border-0">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                    <span class="badge rounded-pill px-3 py-1.5 fw-bold fs-7 shadow-xs d-inline-flex align-items-center gap-1.5" style="background-color: #065f46 !important; color: #ffffff !important; border: 1px solid #047857 !important;">
                        <i class="bi bi-shield-heart-fill"></i>
                        <span>مؤسسة إنسان الخيرية</span>
                    </span>
                    <span class="badge rounded-pill px-2.5 py-1.5 fw-bold fs-7 bg-info bg-opacity-10 text-info border border-info border-opacity-25">
                        <i class="bi bi-layers-half me-1"></i> المرحلة الأولى: الفحص والبحث المبدئي
                    </span>
                    @if(request('source') || request('status') || request('type') || request('q'))
                        <span class="badge rounded-pill px-2.5 py-1.5 fw-bold fs-7 bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25">
                            <i class="bi bi-funnel-fill me-1"></i> الفلترة نشطة
                        </span>
                    @endif
                </div>
                <h1 class="h2 fw-800 text-stats-main mb-1 d-flex align-items-center gap-2 flex-wrap">
                    <span>منظومة استقبال وبحث الحالات المبدئية</span>
                    <span class="badge rounded-pill px-3 py-1 fs-6 bg-primary bg-opacity-10 text-primary border border-primary border-opacity-20 font-monospace">
                        {{ $applications->total() }} طلب مسجل
                    </span>
                </h1>
                <p class="text-muted-theme small mb-0">استقبال وتدقيق وفرز طلبات الحالات والمساعدات الإنسانية الواردة من الويبسايت وتطبيق الموبايل والسيستم الداخلي وتوجيهها للبحث الميداني.</p>
            </div>
            
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Dropdown for Public Direct Application Forms -->
                <div class="dropdown">
                    <button class="btn btn-outline-secondary rounded-pill px-3.5 py-2.5 fw-bold small dropdown-toggle d-flex align-items-center gap-2 shadow-xs" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-link-45deg fs-6 text-primary"></i>
                        <span>استمارات التقديم المباشرة</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 p-2" style="min-width: 250px;">
                        <li>
                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold" href="{{ route('cases.apply-zad') }}" target="_blank">
                                <span class="badge bg-success bg-opacity-10 text-success p-2 rounded-2"><i class="bi bi-basket2-fill"></i></span>
                                <div>
                                    <div class="fw-bold fs-7 text-dark">استمارة زاد (الأيتام)</div>
                                    <small class="text-muted x-small">كفالة الأسر والأيتام 2026</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold" href="{{ route('cases.apply-midrar') }}" target="_blank">
                                <span class="badge bg-info bg-opacity-10 text-info p-2 rounded-2"><i class="bi bi-droplet-half"></i></span>
                                <div>
                                    <div class="fw-bold fs-7 text-dark">استمارة مدرار (المياه والأسقف)</div>
                                    <small class="text-muted x-small">وصلات مياه شرب وتسقيف منازل</small>
                                </div>
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item rounded-3 py-2 d-flex align-items-center gap-2 fw-semibold" href="{{ route('cases.apply-hope') }}" target="_blank">
                                <span class="badge p-2 rounded-2" style="background: rgba(124, 58, 237, 0.1); color: #7c3aed;"><i class="bi bi-activity"></i></span>
                                <div>
                                    <div class="fw-bold fs-7 text-dark">استمارة بعثاء الأمل (الأورام)</div>
                                    <small class="text-muted x-small">رعاية واستضافة مرضى الأورام</small>
                                </div>
                            </a>
                        </li>
                    </ul>
                </div>

                <a href="{{ route('visits.create') }}" class="btn btn-outline-success rounded-pill px-3.5 py-2.5 fw-bold small d-flex align-items-center gap-1.5 shadow-xs">
                    <i class="bi bi-file-earmark-medical-fill"></i>
                    <span>استمارة البحث الميداني</span>
                </a>

                <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm d-flex align-items-center gap-2 btn-gradient-primary" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                    <i class="bi bi-plus-circle-fill fs-6"></i>
                    <span>تسجيل طلب بحث جديد</span>
                </button>
            </div>
        </div>
    </div>

    <!-- 2. Interactive KPI Summary Cards (Clickable Quick Filters) -->
    <div class="row g-3 mb-4">
        <!-- KPI: Total Cases -->
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('status', 'page'), [])) }}" class="text-decoration-none d-block h-100">
                <div class="kpi-card-lux kpi-theme-all {{ !request('status') ? 'is-active' : '' }}">
                    <div class="kpi-content-box">
                        <span class="kpi-label-text">إجمالي الطلبات</span>
                        <div class="kpi-number-display text-stats-main">{{ $statusCounts['all'] ?? $applications->total() }}</div>
                        <span class="kpi-micro-badge kpi-badge-all">جميع الحالات الواردة</span>
                    </div>
                    <div class="kpi-icon-wrap kpi-icon-all">
                        <i class="bi bi-folder2-open"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI: Pending -->
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" class="text-decoration-none d-block h-100">
                <div class="kpi-card-lux kpi-theme-pending {{ request('status') === 'pending' ? 'is-active' : '' }}">
                    <div class="kpi-content-box">
                        <span class="kpi-label-text">في انتظار البحث</span>
                        <div class="kpi-number-display kpi-num-pending">{{ $statusCounts['pending'] ?? 0 }}</div>
                        <span class="kpi-micro-badge kpi-badge-pending">بانتظار الفرز المبدئي</span>
                    </div>
                    <div class="kpi-icon-wrap kpi-icon-pending">
                        <i class="bi bi-hourglass-split"></i>
                        <span class="pulse-indicator-dot dot-warning"></span>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI: Under Research -->
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'under_research'])) }}" class="text-decoration-none d-block h-100">
                <div class="kpi-card-lux kpi-theme-research {{ request('status') === 'under_research' ? 'is-active' : '' }}">
                    <div class="kpi-content-box">
                        <span class="kpi-label-text">تحت البحث الميداني</span>
                        <div class="kpi-number-display kpi-num-research">{{ $statusCounts['under_research'] ?? 0 }}</div>
                        <span class="kpi-micro-badge kpi-badge-research">جاري الفحص والزيارة</span>
                    </div>
                    <div class="kpi-icon-wrap kpi-icon-research">
                        <i class="bi bi-search"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI: Approved -->
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" class="text-decoration-none d-block h-100">
                <div class="kpi-card-lux kpi-theme-approved {{ request('status') === 'approved' ? 'is-active' : '' }}">
                    <div class="kpi-content-box">
                        <span class="kpi-label-text">تم الاعتماد والموافقة</span>
                        <div class="kpi-number-display kpi-num-approved">{{ $statusCounts['approved'] ?? 0 }}</div>
                        <span class="kpi-micro-badge kpi-badge-approved">مستوفية للشروط</span>
                    </div>
                    <div class="kpi-icon-wrap kpi-icon-approved">
                        <i class="bi bi-check2-circle"></i>
                    </div>
                </div>
            </a>
        </div>

        <!-- KPI: Rejected -->
        <div class="col-6 col-md-4 col-xl">
            <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" class="text-decoration-none d-block h-100">
                <div class="kpi-card-lux kpi-theme-rejected {{ request('status') === 'rejected' ? 'is-active' : '' }}">
                    <div class="kpi-content-box">
                        <span class="kpi-label-text">طلبات مرفوضة</span>
                        <div class="kpi-number-display kpi-num-rejected">{{ $statusCounts['rejected'] ?? 0 }}</div>
                        <span class="kpi-micro-badge kpi-badge-rejected">غير مطابقة للمعايير</span>
                    </div>
                    <div class="kpi-icon-wrap kpi-icon-rejected">
                        <i class="bi bi-x-circle"></i>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <!-- 3. Smart Integrated Control Bar (Source, Projects, Search, View Switcher) -->
    <div class="card border-0 shadow-xs rounded-4 p-3 mb-4 bg-stats-card-main">
        <!-- Row 1: Source Segmented Tabs (جهة التقديم) -->
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 pb-3 mb-3 border-bottom border-light-subtle">
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <span class="small fw-bold text-muted d-flex align-items-center gap-1.5 ms-1">
                    <i class="bi bi-diagram-3-fill text-primary"></i> جهة التقديم (المصدر):
                </span>
                
                <div class="source-segmented-group d-flex flex-wrap gap-1.5 p-1 bg-light rounded-pill border">
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('source', 'page'), [])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold source-seg-btn {{ !request('source') ? 'active-seg shadow-xs' : '' }}">
                        <i class="bi bi-grid-fill me-1"></i> جميع المصادر <span class="badge ms-1 font-monospace {{ !request('source') ? 'bg-white bg-opacity-25 text-white' : 'bg-secondary bg-opacity-15 text-muted' }}">{{ $sourceCounts['all'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['source' => 'website'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold source-seg-btn {{ request('source') == 'website' ? 'active-seg-website shadow-xs' : '' }}">
                        <i class="bi bi-globe2 me-1 {{ request('source') == 'website' ? 'text-white' : 'text-info' }}"></i> الويبسايت <span class="badge ms-1 font-monospace {{ request('source') == 'website' ? 'bg-white bg-opacity-25 text-white' : 'bg-info bg-opacity-15 text-info' }}">{{ $sourceCounts['website'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['source' => 'system'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold source-seg-btn {{ request('source') == 'system' ? 'active-seg-system shadow-xs' : '' }}">
                        <i class="bi bi-laptop me-1 {{ request('source') == 'system' ? 'text-white' : 'text-success' }}"></i> السيستم <span class="badge ms-1 font-monospace {{ request('source') == 'system' ? 'bg-white bg-opacity-25 text-white' : 'bg-success bg-opacity-15 text-success' }}">{{ $sourceCounts['system'] ?? 0 }}</span>
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['source' => 'mobile'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold source-seg-btn {{ request('source') == 'mobile' ? 'active-seg-mobile shadow-xs' : '' }}">
                        <i class="bi bi-phone-fill me-1 {{ request('source') == 'mobile' ? 'text-white' : 'text-warning' }}"></i> الموبايل <span class="badge ms-1 font-monospace {{ request('source') == 'mobile' ? 'bg-white bg-opacity-25 text-white' : 'bg-warning bg-opacity-15 text-warning-emphasis' }}">{{ $sourceCounts['mobile'] ?? 0 }}</span>
                    </a>
                </div>
            </div>

            <!-- View Switcher (Cards / Table) & Select All -->
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="btn-group p-1 bg-light rounded-pill border shadow-xs" role="group">
                    <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold view-toggle-btn active" id="btnViewCards" onclick="switchView('cards')">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> البطاقات
                    </button>
                    <button type="button" class="btn btn-sm rounded-pill px-3 fw-bold view-toggle-btn" id="btnViewTable" onclick="switchView('table')">
                        <i class="bi bi-table me-1"></i> جدول البيانات
                    </button>
                </div>

                <div class="form-check form-check-inline select-all-wrapper glass-badge-theme px-3 py-1.5 mb-0 border rounded-pill shadow-xs d-flex align-items-center">
                    <input class="form-check-input mt-0 me-1 cursor-pointer" type="checkbox" id="selectAllItems" onclick="toggleAllCheckboxes(this)">
                    <label class="form-check-label fw-bold small cursor-pointer" for="selectAllItems">تحديد الكل</label>
                </div>
            </div>
        </div>

        <!-- Row 2: Project Type Filters & Search Input -->
        <div class="row g-3 align-items-center">
            <!-- Project Types Pills -->
            <div class="col-xl-7 col-lg-12">
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <span class="small fw-bold text-muted me-1"><i class="bi bi-tags-fill text-primary"></i> المشروع:</span>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('type', 'page'), [])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ !request('type') ? 'btn-secondary text-white shadow-xs' : 'btn-outline-secondary' }}">
                        كل المشاريع
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['type' => 'zad'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('type') == 'zad' ? 'btn-success text-white shadow-xs' : 'btn-outline-success' }}">
                        <i class="bi bi-basket2-fill me-1"></i> زاد (الأيتام) <span class="font-monospace">({{ $typeCounts['zad'] ?? 0 }})</span>
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['type' => 'midrar'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold {{ request('type') == 'midrar' ? 'btn-info text-white shadow-xs' : 'btn-outline-info' }}">
                        <i class="bi bi-droplet-half me-1"></i> مدرار (المياه) <span class="font-monospace">({{ $typeCounts['midrar'] ?? 0 }})</span>
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['type' => 'hope'])) }}" 
                       class="btn btn-sm rounded-pill px-2.5 fw-bold" style="{{ request('type') == 'hope' ? 'background: #7c3aed; color: #fff; box-shadow: 0 2px 6px rgba(124,58,237,0.3);' : 'border: 1px solid #7c3aed; color: #7c3aed;' }}">
                        <i class="bi bi-activity me-1"></i> بعثاء الأمل <span class="font-monospace">({{ $typeCounts['hope'] ?? 0 }})</span>
                    </a>
                </div>
            </div>

            <!-- Instant Search Input -->
            <div class="col-xl-5 col-lg-12">
                <form method="GET" action="{{ route('mobile.case-applications.index') }}" class="d-flex gap-2">
                    @if(request('source')) <input type="hidden" name="source" value="{{ request('source') }}"> @endif
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    
                    <div class="input-group search-input-lux shadow-xs">
                        <span class="input-group-text bg-white border-end-0 rounded-start-pill ps-3 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="q" id="searchBoxInput" value="{{ request('q') }}" class="form-control border-start-0 border-end-0 py-2" placeholder="ابحث بالاسم، الهاتف، الرقم القومي، رقم الطلب..." autocomplete="off">
                        @if(request('q'))
                            <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('q', 'page'), [])) }}" class="input-group-text bg-white border-start-0 border-end-0 text-muted text-decoration-none">
                                <i class="bi bi-x-circle-fill"></i>
                            </a>
                        @endif
                        <button class="btn btn-primary rounded-end-pill px-4 fw-bold" type="submit">بحث</button>
                    </div>

                    @if(request('q') || request('type') || request('status') || request('source'))
                        <a href="{{ route('mobile.case-applications.index') }}" class="btn btn-outline-danger rounded-pill px-3 text-nowrap d-flex align-items-center gap-1 shadow-xs" title="إلغاء كل الفلاتر">
                            <i class="bi bi-arrow-counterclockwise"></i> <span class="d-none d-sm-inline">تفريغ</span>
                        </a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Delete Form -->
    <form id="bulk-delete-form" action="{{ route('mobile.case-applications.bulk-destroy') }}" method="POST" class="d-none">
        @csrf
        <div id="bulk-ids-container"></div>
    </form>

    <!-- Floating Multi-Select Dock -->
    <div id="bulk-action-bar" class="bulk-action-bar-floating d-none">
        <div class="bulk-dock-pill shadow-lg d-flex align-items-center gap-3 px-4 py-2.5 rounded-pill border">
            <div class="d-flex align-items-center gap-2">
                <span class="badge bg-primary rounded-pill px-3 py-1.5 font-monospace fw-800 fs-7">
                    <span id="selected-count">0</span> محدد
                </span>
                <span class="fw-bold small text-white d-none d-md-inline">من طلبات الحالات</span>
            </div>
            <div class="vr bg-white opacity-25" style="height: 24px;"></div>
            <div class="d-flex align-items-center gap-2">
                <button type="button" class="btn btn-danger btn-sm rounded-pill px-3.5 py-1.5 fw-bold shadow-xs d-flex align-items-center gap-1.5" onclick="submitBulkDelete()">
                    <i class="bi bi-trash3-fill"></i> حذف المحدد نهائياً
                </button>
                <button type="button" class="btn btn-outline-light btn-sm rounded-pill px-3 py-1.5 fw-bold" onclick="deselectAll()">
                    إلغاء
                </button>
            </div>
        </div>
    </div>

    <!-- 4. VIEW A: CARDS VIEW (عرض البطاقات التنفيذية) -->
    <div id="cardsViewContainer" class="view-mode-section">
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
                $cleanPhone = preg_replace('/[^0-9]/', '', (string)$app->applicant_phone);
                if (str_starts_with($cleanPhone, '0')) {
                    $whatsappNumber = '2' . $cleanPhone;
                } else {
                    $whatsappNumber = $cleanPhone;
                }
                $whatsappMsg = urlencode("السلام عليكم ورحمة الله، معكم باحث مؤسسة إنسان الخيرية بخصوص طلب المساعدة رقم #" . $app->id . " المسجل باسم: " . ($app->applicant_name ?? ''));
            @endphp
            <div class="col-md-6 col-lg-4 col-xl-4 animate-up">
                <div class="case-card-executive case-card-status-{{ $statusKey }} d-flex flex-column h-100 position-relative">
                    
                    <!-- Card Top Header -->
                    <div class="case-card-header p-3 pb-2.5">
                        <div class="d-flex align-items-center justify-content-between gap-2 mb-2">
                            <!-- Status Badge -->
                            @if($statusKey === 'pending')
                                <span class="case-pill-badge badge-pending">
                                    <span class="case-dot-pulse dot-pending"></span> في انتظار البحث
                                </span>
                            @elseif($statusKey === 'research')
                                <span class="case-pill-badge badge-research">
                                    <i class="bi bi-search me-1"></i> تحت البحث الميداني
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
                                <span class="case-sector-badge" title="نوع المشروع / الحالة">
                                    {{ $app->case_type_label }}
                                </span>
                                <div class="case-checkbox-wrapper ms-1">
                                    <input type="checkbox" class="case-checkbox form-check-input m-0 cursor-pointer" value="{{ $app->id }}" onclick="updateBulkBar()">
                                </div>
                            </div>
                        </div>

                        <!-- Source Badge & Reference ID -->
                        <div class="d-flex align-items-center justify-content-between x-small pt-1.5 border-top border-light-subtle">
                            <span class="case-source-tag">
                                <i class="bi {{ $srcBadge['icon'] }} me-1"></i>
                                <span class="fw-bold">{{ $srcBadge['label'] }}</span>
                                @if(!empty($srcBadge['sub']))
                                    <span class="text-muted opacity-75">({{ $srcBadge['sub'] }})</span>
                                @endif
                            </span>
                            <span class="font-monospace fw-800 text-muted opacity-75 cursor-pointer copy-trigger" title="اضغط لنسخ رقم الطلب" onclick="copyToClipboard('{{ $app->id }}', 'تم نسخ رقم الطلب')">
                                #{{ $app->id }} <i class="bi bi-clipboard ms-0.5"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Submitter strip if proxy -->
                    @if($app->submitter_type === 'other' || !empty($app->submitter_name))
                        <div class="case-submitter-strip px-3 py-1.5 mx-3 mb-2 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-1">
                            <div class="d-flex align-items-center gap-1.5">
                                <i class="bi bi-person-heart text-primary fs-7"></i>
                                <span class="text-muted x-small">مُقدم الطلب:</span>
                                <strong class="text-dark x-small">{{ $app->submitter_name ?? 'فاعل خير' }}</strong>
                                @if($app->submitter_relation) <span class="text-muted x-small">({{ $app->submitter_relation }})</span> @endif
                            </div>
                            @if($app->submitter_phone)
                                <a href="tel:{{ $app->submitter_phone }}" class="text-decoration-none font-monospace x-small text-primary fw-bold">
                                    <i class="bi bi-telephone-outbound"></i> {{ $app->submitter_phone }}
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Card Body -->
                    <div class="case-card-body px-3 pb-3 flex-grow-1 d-flex flex-column">
                        <div class="d-flex align-items-start gap-2.5 mb-2.5">
                            <div class="case-applicant-avatar avatar-status-{{ $statusKey }} flex-shrink-0">
                                {{ $initial }}
                            </div>
                            <div class="flex-grow-1 min-w-0">
                                <h5 class="case-applicant-title mb-1.5 text-truncate" title="{{ $app->applicant_name }}">
                                    {{ $app->applicant_name }}
                                </h5>
                                
                                <!-- Communication Action Pills (Call + WhatsApp + Copy) -->
                                <div class="d-flex flex-wrap align-items-center gap-1.5">
                                    <a href="tel:{{ $app->applicant_phone }}" class="case-phone-link shadow-xs" title="اتصال هاتفي مباشر">
                                        <i class="bi bi-telephone-fill"></i>
                                        <span>{{ $app->applicant_phone }}</span>
                                    </a>
                                    
                                    @if(!empty($cleanPhone))
                                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMsg }}" target="_blank" class="case-whatsapp-link shadow-xs" title="مراسلة واتساب فورية بنموذج مسبق">
                                            <i class="bi bi-whatsapp"></i>
                                            <span>واتساب</span>
                                        </a>
                                    @endif

                                    <button type="button" class="btn btn-xs case-copy-btn" title="نسخ رقم الهاتف" onclick="copyToClipboard('{{ $app->applicant_phone }}', 'تم نسخ رقم الهاتف')">
                                        <i class="bi bi-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Meta details (National ID & Address with Google Maps link) -->
                        <div class="case-meta-details mb-2.5">
                            @if($app->national_id)
                                <div class="case-meta-row mb-1 justify-content-between">
                                    <div class="d-flex align-items-center gap-1.5">
                                        <i class="bi bi-credit-card-2-front text-muted"></i>
                                        <span class="text-muted x-small">الرقم القومي:</span>
                                        <span class="font-monospace fw-bold text-dark x-small">{{ $app->national_id }}</span>
                                    </div>
                                    <button type="button" class="btn btn-xs text-muted p-0" title="نسخ الرقم القومي" onclick="copyToClipboard('{{ $app->national_id }}', 'تم نسخ الرقم القومي')">
                                        <i class="bi bi-clipboard fs-7"></i>
                                    </button>
                                </div>
                            @endif

                            <div class="case-meta-row justify-content-between">
                                <div class="d-flex align-items-center gap-1.5 text-truncate">
                                    <i class="bi bi-geo-alt-fill text-danger flex-shrink-0"></i>
                                    <span class="fw-bold x-small text-dark flex-shrink-0">{{ $app->governorate ?? 'غير محدد' }} - {{ $app->city ?? 'غير محدد' }}</span>
                                    @if($app->address)
                                        <span class="case-address-sub text-muted text-truncate" title="{{ $app->address }}">— {{ $app->address }}</span>
                                    @endif
                                </div>
                                @if($app->address || $app->city)
                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($app->address ?? '') . ' ' . ($app->city ?? '') . ' ' . ($app->governorate ?? '')) }}" target="_blank" class="text-primary text-decoration-none x-small flex-shrink-0 ms-1" title="عرض الموقع على Google Maps">
                                        <i class="bi bi-box-arrow-up-right"></i>
                                    </a>
                                @endif
                            </div>
                        </div>

                        <!-- Specialized Project Badges -->
                        @if($app->case_type === 'midrar' && $app->need_type)
                            <div class="specialized-strip midrar-strip p-2 rounded-3 mb-2 small d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-info-emphasis"><i class="bi bi-droplet-half me-1"></i> {{ $app->need_type }}</span>
                                @if($app->guide_name)
                                    <span class="x-small text-muted">الدليل: {{ $app->guide_name }}</span>
                                @endif
                            </div>
                        @elseif($app->case_type === 'hope' && $app->tumor_location)
                            <div class="specialized-strip hope-strip p-2 rounded-3 mb-2 small d-flex align-items-center justify-content-between">
                                <span class="fw-bold" style="color: #6d28d9;"><i class="bi bi-activity me-1"></i> {{ $app->tumor_location }}</span>
                                @if($app->hospital_name)
                                    <span class="x-small text-muted">{{ $app->hospital_name }}</span>
                                @endif
                            </div>
                        @elseif($app->case_type === 'zad' && $app->children_count)
                            <div class="specialized-strip zad-strip p-2 rounded-3 mb-2 small d-flex align-items-center justify-content-between">
                                <span class="fw-bold text-success"><i class="bi bi-basket2-fill me-1"></i> أيتام وأرامل</span>
                                <span class="badge bg-danger rounded-pill x-small">{{ $app->children_count }} أولاد</span>
                            </div>
                        @endif

                        <!-- Description Box -->
                        <div class="case-description-box p-2.5 rounded-3 mb-3 flex-grow-1">
                            <p class="mb-0 text-muted lh-base x-small">
                                <i class="bi bi-chat-left-text me-1 text-primary opacity-50"></i>
                                {{ Str::limit($app->description, 130) }}
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

                        <!-- Direct Field Visit Link if Approved -->
                        @if(in_array($app->status, ['approved', 'completed', 'accepted']))
                            <div class="mb-2">
                                <a href="{{ route('visits.create', ['application_id' => $app->id]) }}" class="btn case-btn-visit w-100 fw-bold py-2 shadow-xs d-flex align-items-center justify-content-center gap-1.5">
                                    <i class="bi bi-file-earmark-check-fill"></i> تحويل إلى استمارة البحث الاجتماعي الميداني
                                </a>
                            </div>
                        @endif

                        <!-- Action Button: Open Detailed Modal -->
                        <div class="mt-auto pt-1">
                            <button class="btn case-btn-review w-100 fw-bold py-2.5 shadow-sm d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}">
                                <i class="bi bi-pencil-square"></i>
                                <span>مراجعة وتحديث ملف البحث</span>
                            </button>
                        </div>
                    </div>

                    <!-- Card Footer: Timestamps -->
                    <div class="case-card-footer px-3 py-2 border-top border-light-subtle d-flex align-items-center justify-content-between x-small text-muted">
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-calendar3"></i> {{ $app->created_at->format('Y-m-d') }}
                        </span>
                        <span class="d-flex align-items-center gap-1">
                            <i class="bi bi-clock-history"></i> {{ $app->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Detail & Decision Modal -->
            <div class="modal fade" id="modal{{ $app->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 24px; overflow: hidden;">
                        
                        <!-- Official Print-Only Letterhead -->
                        <div class="print-official-header d-none w-100 p-3 mb-2 border-bottom border-2 border-success align-items-center justify-content-between">
                            <div>
                                <h4 class="fw-bold mb-1" style="color: #059669 !important;">مؤسسة إنسان الخيرية</h4>
                                <div class="small text-muted">إدارة البحث الميداني والرعاية الاجتماعية — استمارة فحص وبحث مبدئي</div>
                            </div>
                            <div class="text-start">
                                <span class="badge bg-success bg-opacity-10 text-success border border-success px-2.5 py-1 small fw-bold">#{{ $app->id }} - {{ $app->case_type_label }}</span>
                                <div class="small text-muted mt-1">تاريخ القيد: {{ $app->created_at->format('Y-m-d') }}</div>
                            </div>
                        </div>

                        <!-- Modal Header -->
                        <div class="modal-header bg-stats-header px-4 py-3 border-bottom border-light-subtle d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-2.5">
                                <div class="case-applicant-avatar avatar-status-{{ $statusKey }}">
                                    {{ $initial }}
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2">
                                        <h5 class="modal-title fw-800 text-stats-main mb-0">{{ $app->applicant_name }}</h5>
                                        <span class="badge bg-{{ $app->status_color }} bg-opacity-15 text-{{ $app->status_color }} border border-{{ $app->status_color }} border-opacity-25 rounded-pill px-2.5 py-0.5 x-small fw-bold">
                                            {{ $app->status_label }}
                                        </span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge {{ $app->source_badge['class'] }} rounded-pill px-2 py-0.5 x-small fw-bold">
                                            <i class="bi {{ $app->source_badge['icon'] }}"></i> {{ $app->source_badge['label'] }}
                                        </span>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark rounded-pill px-2 py-0.5 x-small font-monospace">#{{ $app->id }}</span>
                                        <span class="text-muted x-small">{{ $app->case_type_label }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('mobile.case-applications.print', $app->id) }}?autoprint=1" target="_blank" class="btn btn-primary btn-sm rounded-pill px-3.5 py-1.5 fw-bold shadow-sm d-flex align-items-center gap-1.5" title="طباعة الاستمارة الرسمية A4 أو الحفظ كـ PDF">
                                    <i class="bi bi-printer-fill"></i>
                                    <span>طباعة الاستمارة الرسمية (A4)</span>
                                </a>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                        </div>

                        <!-- Modal Body: 2 Columns Layout -->
                        <div class="modal-body p-4 bg-stats-card-main">
                            <div class="row g-4">
                                <!-- Col 1: Case Dossier Information -->
                                <div class="col-lg-7">
                                    <div class="dossier-card p-3.5 rounded-4 bg-stats-inner-item border mb-3">
                                        <h6 class="fw-bold text-primary mb-3 border-bottom pb-2 d-flex align-items-center gap-2">
                                            <i class="bi bi-person-vcard fs-5"></i> بيانات الحالة الديموغرافية والاتصال
                                        </h6>

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="text-muted small fw-bold mb-1 d-block">رقم الهاتف الأساسي</label>
                                                <div class="d-flex align-items-center gap-2 flex-wrap">
                                                    <a href="tel:{{ $app->applicant_phone }}" class="fw-bold fs-6 font-monospace text-primary text-decoration-none">
                                                        <i class="bi bi-telephone-fill me-1"></i>{{ $app->applicant_phone }}
                                                    </a>
                                                    <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2 py-0.5" onclick="navigator.clipboard.writeText('{{ $app->applicant_phone }}'); const el=this; el.innerHTML='<i class=\'bi bi-check2\'></i> تم النسخ'; setTimeout(() => el.innerHTML='<i class=\'bi bi-copy\'></i> نسخ', 1500);" title="نسخ رقم الهاتف">
                                                        <i class="bi bi-copy"></i> نسخ
                                                    </button>
                                                    @if(!empty($cleanPhone))
                                                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMsg }}" target="_blank" class="btn btn-xs btn-success rounded-pill px-2.5 py-1 fw-bold text-white d-inline-flex align-items-center gap-1 shadow-xs">
                                                            <i class="bi bi-whatsapp"></i> واتساب
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($app->backup_phone)
                                                <div class="col-md-6">
                                                    <label class="text-muted small fw-bold mb-1 d-block">رقم هاتف بديل</label>
                                                    <div class="fw-bold fs-6 font-monospace text-dark">{{ $app->backup_phone }}</div>
                                                </div>
                                            @endif

                                            <div class="col-md-6">
                                                <label class="text-muted small fw-bold mb-1 d-block">الرقم القومي</label>
                                                <div class="font-monospace fw-bold fs-6">{{ $app->national_id ?? 'غير مسجل' }}</div>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="text-muted small fw-bold mb-1 d-block">المحافظة والمركز</label>
                                                <div class="fw-bold fs-6">{{ $app->governorate ?? '-' }} - {{ $app->city ?? '-' }}</div>
                                            </div>

                                            <div class="col-12">
                                                <label class="text-muted small fw-bold mb-1 d-block">العنوان التفصيلي</label>
                                                <div class="p-2.5 bg-light rounded-3 border small d-flex align-items-center justify-content-between">
                                                    <span>{{ $app->address ?? 'غير محدد' }}</span>
                                                    @if($app->address)
                                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode(($app->address ?? '') . ' ' . ($app->city ?? '') . ' ' . ($app->governorate ?? '')) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                                            خرائط Google <i class="bi bi-box-arrow-up-right ms-1"></i>
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>

                                            @if($app->submitter_name)
                                                <div class="col-12">
                                                    <div class="p-3 bg-light rounded-3 border border-success border-opacity-25">
                                                        <div class="fw-bold text-success mb-1"><i class="bi bi-person-heart me-1"></i> مُقدم الطلب بالنيابة:</div>
                                                        <div class="small">{{ $app->submitter_name }} ({{ $app->submitter_relation ?? 'غير محدد' }}) — هاتف: {{ $app->submitter_phone ?? '-' }}</div>
                                                    </div>
                                                </div>
                                            @endif

                                            <div class="col-12">
                                                <label class="text-muted small fw-bold mb-1 d-block">شرح الحالة وظروف الأسرة</label>
                                                <div class="p-3 bg-light rounded-3 border small lh-base">
                                                    {{ $app->description }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Project Specific Details -->
                                    @if($app->case_type === 'zad' || $app->deceased_husband_name || $app->death_details)
                                        <div class="p-3.5 rounded-4 border border-success bg-success bg-opacity-10 mb-3">
                                            <h6 class="fw-bold text-success mb-2 border-bottom border-success border-opacity-25 pb-2">
                                                <i class="bi bi-basket2-fill me-1"></i> استمارة زاد (كفالة أيتام) 2026:
                                            </h6>
                                            <div class="row g-2 small">
                                                <div class="col-md-6"><strong>اسم الأم:</strong> {{ $app->mother_name ?? $app->applicant_name }}</div>
                                                <div class="col-md-6"><strong>اسم الزوج المتوفي:</strong> {{ $app->deceased_husband_name ?? '-' }}</div>
                                                <div class="col-md-6"><strong>عدد الأولاد:</strong> <span class="badge bg-danger rounded-pill">{{ $app->children_count ?? '-' }}</span></div>
                                                <div class="col-md-6"><strong>صفة مقدم الطلب:</strong> {{ $app->submitter_relation_to_mother ?? '-' }}</div>
                                                @if($app->children_details)
                                                    <div class="col-12"><strong>بيانات الأطفال:</strong> {{ $app->children_details }}</div>
                                                @endif
                                                @if($app->income_aid_sources)
                                                    <div class="col-12"><strong>المعاشات والمساعدات:</strong> {{ $app->income_aid_sources }}</div>
                                                @endif
                                                @if($app->expenses_debts_details)
                                                    <div class="col-12"><strong>الديون والمصاريف:</strong> {{ $app->expenses_debts_details }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if($app->case_type === 'midrar' || $app->need_type || $app->midrar_details)
                                        <div class="p-3.5 rounded-4 border border-info bg-info bg-opacity-10 mb-3">
                                            <h6 class="fw-bold text-info-emphasis mb-2 border-bottom border-info border-opacity-25 pb-2">
                                                <i class="bi bi-droplet-half me-1"></i> استمارة مشروع مدرار (مياه وأسقف):
                                            </h6>
                                            <div class="row g-2 small">
                                                <div class="col-md-6"><strong>نوع الاحتياج المطلوب:</strong> <span class="badge bg-primary text-white">{{ $app->need_type ?? 'غير محدد' }}</span></div>
                                                <div class="col-md-6"><strong>أفراد الأسرة بالمنزل:</strong> {{ $app->family_members_count ?? '-' }}</div>
                                                @if($app->guide_name || $app->guide_phone)
                                                    <div class="col-12"><strong>بيانات الدليل:</strong> {{ $app->guide_name ?? '-' }} (هاتف: {{ $app->guide_phone ?? '-' }})</div>
                                                @endif
                                                @if($app->social_status_description)
                                                    <div class="col-12"><strong>وصف المسكن والمعيشة:</strong> {{ $app->social_status_description }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    @if($app->case_type === 'hope' || $app->tumor_location || $app->hospital_name || $app->tumor_details)
                                        <div class="p-3.5 rounded-4 border mb-3" style="background: rgba(124, 58, 237, 0.08); border-color: rgba(124, 58, 237, 0.3);">
                                            <h6 class="fw-bold mb-2 pb-2 border-bottom" style="color: #6d28d9; border-color: rgba(124, 58, 237, 0.2);">
                                                <i class="bi bi-activity me-1"></i> استمارة بعثاء الأمل (رعاية الأورام):
                                            </h6>
                                            <div class="row g-2 small">
                                                <div class="col-md-6"><strong>المستشفى المتابع بها:</strong> {{ $app->hospital_name ?? '-' }}</div>
                                                <div class="col-md-6"><strong>مكان / نوع الورم:</strong> <span class="badge text-white" style="background: #7c3aed;">{{ $app->tumor_location ?? '-' }}</span></div>
                                                @if($app->treatment_protocol)
                                                    <div class="col-12"><strong>البروتوكول العلاجي:</strong> {{ $app->treatment_protocol }}</div>
                                                @endif
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Attachments: ID / Medical Report Images -->
                                    @if($app->id_image_url || $app->medical_report_url)
                                        <div class="p-3.5 rounded-4 bg-stats-inner-item border">
                                            <h6 class="fw-bold text-dark mb-2.5"><i class="bi bi-paperclip me-1 text-primary"></i> المستندات والوثائق المرفقة:</h6>
                                            <div class="d-flex gap-3 flex-wrap">
                                                @if($app->id_image_url)
                                                    <a href="{{ $app->id_image_url }}" target="_blank" class="attachment-preview-box text-decoration-none">
                                                        <img src="{{ $app->id_image_url }}" alt="صورة البطاقة" class="img-thumbnail rounded-3" style="max-height: 100px;">
                                                        <div class="x-small text-center fw-bold mt-1 text-dark">صورة البطاقة</div>
                                                    </a>
                                                @endif
                                                @if($app->medical_report_url)
                                                    <a href="{{ $app->medical_report_url }}" target="_blank" class="attachment-preview-box text-decoration-none">
                                                        <img src="{{ $app->medical_report_url }}" alt="التقرير الطبي" class="img-thumbnail rounded-3" style="max-height: 100px;">
                                                        <div class="x-small text-center fw-bold mt-1 text-dark">التقرير الطبي</div>
                                                    </a>
                                                @endif
                                            </div>
                                        </div>
                                    @endif
                                </div>

                                <!-- Col 2: Decision & Status Update Panel -->
                                <div class="col-lg-5">
                                    <div class="admin-decision-panel p-4 rounded-4 bg-stats-inner-item border shadow-xs h-100">
                                        <h6 class="mb-3 text-stats-main fw-bold border-start border-primary border-4 ps-3">
                                            <i class="bi bi-shield-check me-2 text-primary"></i> قرار وتحديث نتيجة البحث المبدئي
                                        </h6>

                                        <form action="{{ route('mobile.case-applications.update', $app->id) }}" method="POST">
                                            @csrf
                                            <div class="row g-3">
                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">حالة البحث المبدئي <span class="text-danger">*</span></label>
                                                    <select name="status" id="statusSelect{{ $app->id }}" class="form-select p-2.5 fw-bold rounded-3 shadow-xs" onchange="toggleRejectionBox({{ $app->id }})">
                                                        <option value="pending" {{ in_array($app->status, ['pending', 'في انتظار البحث']) ? 'selected' : '' }}>⏳ في انتظار البحث</option>
                                                        <option value="under_research" {{ in_array($app->status, ['under_research', 'تحت البحث', 'reviewed']) ? 'selected' : '' }}>🔍 تحت البحث (جاري المسح الميداني)</option>
                                                        <option value="approved" {{ in_array($app->status, ['approved', 'completed', 'تم البحث', 'accepted']) ? 'selected' : '' }}>✅ تم البحث (اعتماد للبحث الميداني)</option>
                                                        <option value="rejected" {{ in_array($app->status, ['rejected', 'مرفوض']) ? 'selected' : '' }}>❌ مرفوض (عدم استيفاء الشروط)</option>
                                                    </select>
                                                </div>

                                                <div class="col-12">
                                                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25">
                                                        <div class="form-check">
                                                            <input class="form-check-input cursor-pointer" type="checkbox" name="start_visit" value="1" id="startVisit{{ $app->id }}">
                                                            <label class="form-check-label fw-bold small text-success cursor-pointer" for="startVisit{{ $app->id }}">
                                                                التحويل فوراً إلى استمارة البحث الميداني بعد الحفظ
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Rejection Reason Textarea -->
                                                <div class="col-12 {{ in_array($app->status, ['rejected', 'مرفوض']) ? '' : 'd-none' }}" id="rejectionReasonBox{{ $app->id }}">
                                                    <div class="d-flex align-items-center justify-content-between mb-1">
                                                        <label class="form-label small fw-bold text-danger mb-0">سبب الرفض <span class="text-danger">* (إلزامي في حال الرفض)</span></label>
                                                    </div>
                                                    <div class="quick-rejection-chips d-flex gap-1 flex-wrap mb-2">
                                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill py-0.5 px-2" onclick="setRejectionReason({{ $app->id }}, 'الحالة خارج نطاق التغطية الجغرافية المعتمدة للمؤسسة حالياً')">خارج النطاق الجغرافي</button>
                                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill py-0.5 px-2" onclick="setRejectionReason({{ $app->id }}, 'عدم استيفاء شروط ومعايير القبول المعتمدة للمشروع')">عدم استيفاء الشروط</button>
                                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill py-0.5 px-2" onclick="setRejectionReason({{ $app->id }}, 'أرقام الهاتف غير صحيحة أو مغلقة وتعذر الوصول لصاحب الحالة')">تعذر التواصل</button>
                                                        <button type="button" class="btn btn-xs btn-outline-danger rounded-pill py-0.5 px-2" onclick="setRejectionReason({{ $app->id }}, 'تكرار الطلب، توجد استمارة سابقة مسجلة لنفس الحالة')">طلب مكرر</button>
                                                    </div>
                                                    <textarea name="rejection_reason" id="rejectionInput{{ $app->id }}" class="form-control border-danger rounded-3" rows="3" placeholder="اكتب سبب عدم قبول الطلب بالتفصيل لتسجيله رسمياً في السجل...">{{ $app->rejection_reason }}</textarea>
                                                </div>

                                                <div class="col-12">
                                                    <label class="form-label small fw-bold">ملاحظات إدارية / توجيهات الباحث</label>
                                                    <textarea name="admin_notes" class="form-control rounded-3" rows="3" placeholder="اكتب ملاحظات وتوجيهات تخص الحالة أو الباحث الميداني...">{{ $app->admin_notes }}</textarea>
                                                </div>

                                                <div class="col-12 mt-4 pt-2 border-top d-flex justify-content-between gap-2 flex-wrap">
                                                    <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill fw-bold py-2.5 shadow-sm btn-gradient-primary">
                                                        <i class="bi bi-save me-1"></i> حفظ وتحديث النتيجة
                                                    </button>
                                                    
                                                    <button type="button" class="btn btn-outline-danger rounded-pill px-3.5 fw-bold" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب نهائياً من المنظومة؟')) document.getElementById('del-form-{{ $app->id }}').submit()">
                                                        <i class="bi bi-trash3 me-1"></i> حذف
                                                    </button>
                                                </div>
                                            </div>
                                        </form>

                                        <form id="del-form-{{ $app->id }}" action="{{ route('mobile.case-applications.destroy', $app->id) }}" method="POST" class="d-none">
                                            @csrf @method('DELETE')
                                        </form>

                                        <!-- Official Print-Only Summary & Signatures Block -->
                                        <div class="print-only-decision d-none">
                                            <div class="p-3 border rounded-3 bg-light" style="border: 1px solid #cbd5e1 !important; page-break-inside: avoid;">
                                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                                                    <span class="fw-bold">قرار ونتيجة البحث المبدئي: <span class="badge bg-secondary text-dark">{{ $app->status_label }}</span></span>
                                                    <span class="small text-muted font-monospace">كود الطلب: #{{ $app->id }}</span>
                                                </div>
                                                @if($app->status === 'rejected' && $app->rejection_reason)
                                                    <div class="alert alert-danger py-1.5 px-2.5 small mb-2">
                                                        <strong>سبب عدم القبول / الرفض:</strong> {{ $app->rejection_reason }}
                                                    </div>
                                                @endif
                                                @if($app->admin_notes)
                                                    <div class="small text-muted mb-2">
                                                        <strong>ملاحظات وتوجيهات إدارية:</strong> {{ $app->admin_notes }}
                                                    </div>
                                                @endif
                                                <div class="row text-center mt-3 pt-2 border-top">
                                                    <div class="col-4">
                                                        <div class="x-small text-muted mb-3">باحث الحالة / المستلم</div>
                                                        <div class="border-top pt-1 small fw-bold">التوقيع: .....................</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="x-small text-muted mb-3">مدير إدارة البحث الاجتماعي</div>
                                                        <div class="border-top pt-1 small fw-bold">الاعتماد: .....................</div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border border-secondary border-dashed rounded p-2 x-small text-muted h-100 d-flex align-items-center justify-content-center">
                                                            خاتم مؤسسة إنسان
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 animate-up">
                <div class="empty-state-lux text-center py-5 bg-stats-card-main rounded-4 border">
                    <div class="empty-icon-wrapper mb-3 text-center d-flex justify-content-center">
                        <div class="p-4 bg-light rounded-circle text-muted">
                            <i class="bi bi-search" style="font-size: 3.5rem; opacity: 0.4;"></i>
                        </div>
                    </div>
                    <h5 class="fw-800 text-stats-main">لا توجد طلبات بحث مبدئي مطابقة</h5>
                    <p class="text-muted-theme px-4 px-md-5 small">لم يتم العثور على طلبات بحث مبدئي وفقاً للفلاتر وكلمات البحث المحددة حالياً.</p>
                    <div class="mt-3 d-flex justify-content-center gap-2">
                        <a href="{{ route('mobile.case-applications.index') }}" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold small">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> إلغاء كل الفلاتر
                        </a>
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold small" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                            <i class="bi bi-plus-circle me-1"></i> تسجيل طلب جديد
                        </button>
                    </div>
                </div>
            </div>
            @endforelse
        </div>
    </div>

    <!-- 5. VIEW B: HIGH-DENSITY DATA TABLE (عرض جدول البيانات) -->
    <div id="tableViewContainer" class="view-mode-section d-none">
        <div class="card border-0 shadow-xs rounded-4 overflow-hidden bg-stats-card-main">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 executive-data-table">
                    <thead>
                        <tr>
                            <th class="ps-3" style="width: 40px;">#</th>
                            <th>كود الطلب</th>
                            <th>صاحب الحالة</th>
                            <th>بيانات الاتصال</th>
                            <th>المحافظة والمركز</th>
                            <th>المشروع</th>
                            <th>المصدر</th>
                            <th>الحالة</th>
                            <th>تاريخ التقديم</th>
                            <th class="text-center pe-3">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $app)
                        @php
                            $statusKey = match($app->status) {
                                'pending', 'في انتظار البحث' => 'pending',
                                'under_research', 'تحت البحث', 'reviewed' => 'research',
                                'approved', 'completed', 'تم البحث', 'accepted' => 'approved',
                                default => 'rejected'
                            };
                            $srcBadge = $app->source_badge;
                            $cleanPhone = preg_replace('/[^0-9]/', '', (string)$app->applicant_phone);
                            if (str_starts_with($cleanPhone, '0')) {
                                $whatsappNumber = '2' . $cleanPhone;
                            } else {
                                $whatsappNumber = $cleanPhone;
                            }
                            $whatsappMsg = urlencode("السلام عليكم ورحمة الله، معكم باحث مؤسسة إنسان الخيرية بخصوص طلب المساعدة رقم #" . $app->id . " المسجل باسم: " . ($app->applicant_name ?? ''));
                        @endphp
                        <tr>
                            <td class="ps-3">
                                <input type="checkbox" class="case-checkbox form-check-input m-0 cursor-pointer" value="{{ $app->id }}" onclick="updateBulkBar()">
                            </td>
                            <td>
                                <span class="font-monospace fw-800 text-muted copy-trigger cursor-pointer" onclick="copyToClipboard('{{ $app->id }}', 'تم نسخ رقم الطلب')">
                                    #{{ $app->id }}
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="case-applicant-avatar-sm avatar-status-{{ $statusKey }} flex-shrink-0">
                                        {{ mb_substr($app->applicant_name ?? 'ح', 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark text-truncate" style="max-width: 170px;" title="{{ $app->applicant_name }}">
                                            {{ $app->applicant_name }}
                                        </div>
                                        @if($app->national_id)
                                            <div class="font-monospace x-small text-muted">{{ $app->national_id }}</div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-1.5">
                                    <a href="tel:{{ $app->applicant_phone }}" class="btn btn-xs btn-outline-success rounded-pill px-2 py-0.5 font-monospace fw-bold" title="اتصال">
                                        <i class="bi bi-telephone-fill"></i> {{ $app->applicant_phone }}
                                    </a>
                                    @if(!empty($cleanPhone))
                                        <a href="https://wa.me/{{ $whatsappNumber }}?text={{ $whatsappMsg }}" target="_blank" class="btn btn-xs btn-success rounded-pill px-2 py-0.5 text-white" title="واتساب">
                                            <i class="bi bi-whatsapp"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="small fw-semibold text-dark">{{ $app->governorate ?? '-' }}</div>
                                <div class="x-small text-muted">{{ $app->city ?? '-' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border x-small fw-bold">
                                    {{ $app->case_type_label }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $srcBadge['class'] }} rounded-pill px-2 py-0.5 x-small fw-bold">
                                    <i class="bi {{ $srcBadge['icon'] }}"></i> {{ $srcBadge['label'] }}
                                </span>
                            </td>
                            <td>
                                @if($statusKey === 'pending')
                                    <span class="badge badge-pending rounded-pill px-2.5 py-1 x-small fw-bold">في انتظار البحث</span>
                                @elseif($statusKey === 'research')
                                    <span class="badge badge-research rounded-pill px-2.5 py-1 x-small fw-bold">تحت البحث</span>
                                @elseif($statusKey === 'approved')
                                    <span class="badge badge-approved rounded-pill px-2.5 py-1 x-small fw-bold">تم البحث</span>
                                @else
                                    <span class="badge badge-rejected rounded-pill px-2.5 py-1 x-small fw-bold">مرفوض</span>
                                @endif
                            </td>
                            <td>
                                <div class="x-small text-muted">{{ $app->created_at->format('Y-m-d') }}</div>
                                <div class="x-small text-muted opacity-75">{{ $app->created_at->diffForHumans() }}</div>
                            </td>
                            <td class="text-center pe-3">
                                <div class="d-flex align-items-center justify-content-center gap-1.5">
                                    <button class="btn btn-sm btn-primary rounded-pill px-2.5 py-1 fw-bold shadow-xs" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}" title="مراجعة الملف">
                                        <i class="bi bi-pencil-square"></i> مراجعة
                                    </button>
                                    @if(in_array($app->status, ['approved', 'completed', 'accepted']))
                                        <a href="{{ route('visits.create', ['application_id' => $app->id]) }}" class="btn btn-sm btn-success rounded-pill px-2 py-1 text-white shadow-xs" title="استمارة البحث الميداني">
                                            <i class="bi bi-file-earmark-check"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center py-5 text-muted">
                                لا توجد طلبات مطابقة للفلاتر المحددة.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-4 d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div class="small text-muted">
            عرض الحالات من <span class="fw-bold font-monospace">{{ $applications->firstItem() ?? 0 }}</span> إلى <span class="fw-bold font-monospace">{{ $applications->lastItem() ?? 0 }}</span> من إجمالي <span class="fw-bold font-monospace">{{ $applications->total() }}</span> حالة
        </div>
        <div>
            {{ $applications->links() }}
        </div>
    </div>
</div>

<!-- Modal: تسجيل طلب بحث مبدئي جديد -->
<div class="modal fade" id="createCaseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i> تسجيل طلب بحث مبدئي جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-stats-card-main">
                <form method="POST" action="{{ route('mobile.case-applications.store') }}">
                    @csrf

                    <!-- صفة مقدم الطلب -->
                    <div class="mb-3.5 p-3 rounded-4 bg-light border">
                        <label class="form-label fw-bold mb-2 text-primary"><i class="bi bi-diagram-2-fill me-1"></i> جهة تقديم الطلب:</label>
                        <div class="d-flex gap-4 flex-wrap">
                            <div class="form-check">
                                <input class="form-check-input cursor-pointer" type="radio" name="submitter_type" id="newSubSelf" value="self" checked onchange="toggleNewSubmitter()">
                                <label class="form-check-label fw-bold cursor-pointer" for="newSubSelf">مقدم من الحالة نفسها</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input cursor-pointer" type="radio" name="submitter_type" id="newSubOther" value="other" onchange="toggleNewSubmitter()">
                                <label class="form-check-label fw-bold cursor-pointer" for="newSubOther">مقدم نيابة عن الحالة (فاعل خير / جار / قريب)</label>
                            </div>
                        </div>
                    </div>

                    <!-- حقول مقدم الطلب بالنيابة -->
                    <div id="newSubmitterFields" class="p-3.5 rounded-4 bg-light border mb-3.5 d-none">
                        <h6 class="fw-bold text-dark mb-2.5"><i class="bi bi-person-heart me-1 text-primary"></i> بيانات الشخص مُقدم الطلب:</h6>
                        <div class="row g-2.5">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">اسم مقدم الطلب</label>
                                <input type="text" name="submitter_name" class="form-control rounded-3" placeholder="الاسم ثلاثي">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">رقم هاتف مقدم الطلب</label>
                                <input type="tel" name="submitter_phone" class="form-control rounded-3 font-monospace" placeholder="01xxxxxxxxx">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">صلة القرابة / الصفة</label>
                                <input type="text" name="submitter_relation" class="form-control rounded-3" placeholder="جار / فاعل خير / شقيق...">
                            </div>
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">اسم صاحب الحالة (المستفيد) <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_name" class="form-control rounded-3" required placeholder="الاسم رباعي كما في البطاقة">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم الهاتف الأساسي <span class="text-danger">*</span></label>
                            <input type="tel" name="applicant_phone" class="form-control rounded-3 font-monospace" required placeholder="01xxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم هاتف بديل (اختياري)</label>
                            <input type="tel" name="backup_phone" class="form-control rounded-3 font-monospace" placeholder="01xxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الرقم القومي (14 رقم)</label>
                            <input type="text" name="national_id" class="form-control rounded-3 font-monospace" maxlength="14" placeholder="الرقم القومي للحالة">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label small fw-bold">المشروع / نوع الاستمارة المبدئية <span class="text-danger">*</span></label>
                            <select name="case_type" id="newCaseType" class="form-select rounded-3 fw-bold" required onchange="toggleCaseCreateFields(this.value)">
                                <option value="general">طلب مساعدة إنسانية عامة</option>
                                <option value="zad">مشروع زاد (كفالة أسر وأيتام)</option>
                                <option value="midrar">مشروع مدرار (وصلات مياه وتسقيف منازل)</option>
                                <option value="hope">مشروع بعثاء الأمل (رعاية مرضى الأورام)</option>
                                <option value="medical">مساعدة طبية وعلاجية عامة</option>
                                <option value="debt">سداد ديون وغارمين</option>
                            </select>
                        </div>

                        <!-- Specialized Zad Fields -->
                        <div id="newZadFields" class="col-12 p-3 rounded-4 border border-success bg-success bg-opacity-10 d-none">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-basket2-fill me-1"></i> حقول استمارة مشروع زاد (الأيتام):</h6>
                            <div class="row g-2">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">اسم المصدر / الدليل</label>
                                    <input type="text" name="source_name" class="form-control form-control-sm rounded-2" placeholder="اسم وسيط الحالة">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">اسم الزوج المتوفي</label>
                                    <input type="text" name="deceased_husband_name" class="form-control form-control-sm rounded-2" placeholder="اسم الزوج المتوفي">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold">عدد الأطفال الأيتام</label>
                                    <input type="number" name="children_count" class="form-control form-control-sm rounded-2" min="1" max="20" placeholder="مثال: 3">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">تفاصيل الأولاد والسن والتعليم والمصاريف</label>
                                    <textarea name="children_details" class="form-control form-control-sm rounded-2" rows="2" placeholder="أسماء وسن ومدارس ومصاريف الأطفال..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Specialized Midrar Fields -->
                        <div id="newMidrarFields" class="col-12 p-3 rounded-4 border border-info bg-info bg-opacity-10 d-none">
                            <h6 class="fw-bold text-info-emphasis mb-2"><i class="bi bi-droplet-half me-1"></i> حقول مشروع مدرار (مياه وأسقف):</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">تحديد الاحتياج المطلوب <span class="text-danger">*</span></label>
                                    <select name="need_type" class="form-select form-select-sm rounded-2">
                                        <option value="وصله مياه">وصله مياه (توصيل مياه شرب نقية)</option>
                                        <option value="سقف">سقف (تسقيف منزل لحماية الأسرة)</option>
                                        <option value="اخري">أخرى (صدقات جارية وترميم)</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">عدد أفراد الأسرة بالمنزل</label>
                                    <input type="number" name="family_members_count" class="form-control form-control-sm rounded-2" min="1" max="30" placeholder="مثال: 5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">اسم الدليل بالقرية</label>
                                    <input type="text" name="guide_name" class="form-control form-control-sm rounded-2" placeholder="شخص يدِل على المنزل">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">رقم تليفون الدليل</label>
                                    <input type="tel" name="guide_phone" class="form-control form-control-sm rounded-2 font-monospace" placeholder="01xxxxxxxxx">
                                </div>
                            </div>
                        </div>

                        <!-- Specialized Hope Fields -->
                        <div id="newHopeFields" class="col-12 p-3 rounded-4 border d-none" style="background: rgba(124, 58, 237, 0.08); border-color: rgba(124, 58, 237, 0.3);">
                            <h6 class="fw-bold mb-2" style="color: #6d28d9;"><i class="bi bi-activity me-1"></i> حقول مشروع بعثاء الأمل (الأورام):</h6>
                            <div class="row g-2">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">المستشفى المتابع بها <span class="text-danger">*</span></label>
                                    <input type="text" name="hospital_name" class="form-control form-control-sm rounded-2" placeholder="معهد أورام طنطا، 57357...">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold">مكان ونوع الورم <span class="text-danger">*</span></label>
                                    <input type="text" name="tumor_location" class="form-control form-control-sm rounded-2" placeholder="مثال: ثدي، رئة، لوكيميا...">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold">البروتوكول وخطة العلاج</label>
                                    <textarea name="treatment_protocol" class="form-control form-control-sm rounded-2" rows="2" placeholder="الجرعات وجداول العلاج الكيماوي / الإشعاعي..."></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">المحافظة</label>
                            <input type="text" name="governorate" class="form-control rounded-3" placeholder="كفر الشيخ / الغربية / القاهرة...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">المركز / المدينة</label>
                            <input type="text" name="city" class="form-control rounded-3" placeholder="المدينة أو المركز">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">العنوان التفصيلي</label>
                            <input type="text" name="address" class="form-control rounded-3" placeholder="القرية / الشارع / علامة مميزة">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">شرح الحالة والظروف المعيشية <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control rounded-3" rows="3" required placeholder="شرح موجز لظروف الأسرة ومبررات طلب المساعدة..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الحالة الأولية للطلب</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="pending">⏳ في انتظار البحث</option>
                                <option value="under_research">🔍 تحت البحث</option>
                                <option value="approved">✅ تم البحث (تحويل للبحث الميداني)</option>
                                <option value="rejected">❌ مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check pb-2">
                                <input class="form-check-input cursor-pointer" type="checkbox" name="start_visit" value="1" id="newStartVisit">
                                <label class="form-check-label fw-bold small text-success cursor-pointer" for="newStartVisit">
                                    التحويل فوراً إلى استمارة البحث الميداني بعد الحفظ
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold btn-gradient-primary">
                            <i class="bi bi-check2-circle me-1"></i> حفظ وتسجيل الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Toast Container for One-Click Copy Feedback -->
<div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 9999;">
    <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 rounded-pill px-3 py-1 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center">
            <i class="bi bi-check-circle-fill text-success fs-5 me-2"></i>
            <div class="toast-body fw-bold small" id="copyToastMsg">تم النسخ بنجاح</div>
        </div>
    </div>
</div>

<script>
// Switch between Cards & Table views
function switchView(mode) {
    const cards = document.getElementById('cardsViewContainer');
    const table = document.getElementById('tableViewContainer');
    const btnCards = document.getElementById('btnViewCards');
    const btnTable = document.getElementById('btnViewTable');

    if (mode === 'table') {
        cards.classList.add('d-none');
        table.classList.remove('d-none');
        btnTable.classList.add('active');
        btnCards.classList.remove('active');
        localStorage.setItem('case_app_view_mode', 'table');
    } else {
        table.classList.add('d-none');
        cards.classList.remove('d-none');
        btnCards.classList.add('active');
        btnTable.classList.remove('active');
        localStorage.setItem('case_app_view_mode', 'cards');
    }
}

// Restore saved view mode preference
document.addEventListener('DOMContentLoaded', function() {
    const savedMode = localStorage.getItem('case_app_view_mode');
    if (savedMode === 'table') {
        switchView('table');
    }
});

// Copy to clipboard with toast notification
function copyToClipboard(text, msg) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(function() {
        showToast(msg || 'تم النسخ بنجاح');
    }).catch(function() {
        const dummy = document.createElement('textarea');
        document.body.appendChild(dummy);
        dummy.value = text;
        dummy.select();
        document.execCommand('copy');
        document.body.removeChild(dummy);
        showToast(msg || 'تم النسخ بنجاح');
    });
}

function showToast(msg) {
    const toastEl = document.getElementById('copyToast');
    const msgEl = document.getElementById('copyToastMsg');
    if (msgEl) msgEl.textContent = msg;
    if (toastEl && window.bootstrap) {
        const toast = new bootstrap.Toast(toastEl, { delay: 2200 });
        toast.show();
    }
}

function toggleRejectionBox(appId) {
    const select = document.getElementById('statusSelect' + appId);
    const box = document.getElementById('rejectionReasonBox' + appId);
    const input = document.getElementById('rejectionInput' + appId);
    if (!select || !box || !input) return;
    if (select.value === 'rejected') {
        box.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        box.classList.add('d-none');
        input.removeAttribute('required');
    }
}

function setRejectionReason(appId, reason) {
    const input = document.getElementById('rejectionInput' + appId);
    if (input) {
        input.value = reason;
        input.focus();
    }
}

function toggleNewSubmitter() {
    const isOther = document.getElementById('newSubOther').checked;
    const box = document.getElementById('newSubmitterFields');
    if (!box) return;
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
    if (!confirm('هل أنت متأكد من حذف الحالات المحددة (' + checked.length + ' حالة) نهائياً من المنظومة؟')) return;
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
   Executive Case Applications Suite UI/UX Styles
   ======================================================== */
:root {
    --case-font: 'IBM Plex Sans Arabic', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    --font-outfit: 'Outfit', sans-serif;
    --emerald-primary: #065f46;
    --emerald-light: #ecfdf5;
}

.case-app-container {
    font-family: var(--case-font);
}

.bg-theme-page {
    background-color: #f8fafc;
}
.theme-dark .bg-theme-page {
    background-color: #0b1324 !important;
}
.bg-stats-card-main {
    background-color: #ffffff;
    border: 1px solid #e2e8f0;
}
.theme-dark .bg-stats-card-main {
    background-color: #111d33 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.text-stats-main {
    color: #0f172a;
}
.theme-dark .text-stats-main {
    color: #f8fafc !important;
}
.text-muted-theme {
    color: #64748b;
}
.theme-dark .text-muted-theme {
    color: #94a3b8 !important;
}

.fw-800 { font-weight: 800 !important; }
.fs-7 { font-size: 0.82rem !important; }
.x-small { font-size: 0.74rem !important; }
.cursor-pointer { cursor: pointer; }
.transition-all { transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1); }

/* ========================================================
   Executive KPI Cards
   ======================================================== */
.kpi-card-lux {
    background: #ffffff;
    border: 1.5px solid #e2e8f0;
    border-radius: 18px;
    padding: 16px 18px;
    height: 100%;
    min-height: 104px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 14px;
    position: relative;
    overflow: hidden;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
}

.kpi-card-lux::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    left: 0;
    height: 3px;
    background: var(--kpi-accent, #94a3b8);
    opacity: 0.9;
}

.kpi-card-lux:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.06);
    border-color: var(--kpi-accent, #cbd5e1) !important;
}

.kpi-card-lux.is-active {
    border-color: var(--kpi-accent, #065f46) !important;
    box-shadow: 0 0 0 2px var(--kpi-accent, #065f46), 0 8px 24px rgba(0, 0, 0, 0.08) !important;
}

.theme-dark .kpi-card-lux {
    background: #111d33 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.theme-dark .kpi-card-lux.is-active {
    background: #152238 !important;
}

.kpi-content-box {
    display: flex;
    flex-direction: column;
    text-align: right;
    min-width: 0;
}

.kpi-label-text {
    font-size: 0.8rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 2px;
    white-space: nowrap;
}
.theme-dark .kpi-label-text {
    color: #94a3b8;
}

.kpi-number-display {
    font-size: 1.85rem;
    font-weight: 800;
    line-height: 1.15;
    letter-spacing: -0.5px;
    font-family: 'Outfit', 'IBM Plex Sans Arabic', sans-serif;
    margin: 2px 0 4px 0;
}

.kpi-micro-badge {
    font-size: 0.72rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 2px 8px;
    border-radius: 20px;
    width: fit-content;
    white-space: nowrap;
}

/* Specific themes */
.kpi-theme-all {
    --kpi-accent: #059669;
}
.kpi-icon-all {
    background: rgba(5, 150, 105, 0.08) !important;
    color: #059669 !important;
    border: 1px solid rgba(5, 150, 105, 0.2) !important;
}
.kpi-badge-all {
    background: rgba(5, 150, 105, 0.08);
    color: #047857;
}

.kpi-theme-pending {
    --kpi-accent: #f59e0b;
}
.kpi-icon-pending {
    background: rgba(245, 158, 11, 0.1) !important;
    color: #d97706 !important;
    border: 1px solid rgba(245, 158, 11, 0.25) !important;
}
.kpi-badge-pending {
    background: rgba(245, 158, 11, 0.1);
    color: #b45309;
}
.kpi-num-pending {
    color: #d97706 !important;
}

.kpi-theme-research {
    --kpi-accent: #0284c7;
}
.kpi-icon-research {
    background: rgba(2, 132, 199, 0.08) !important;
    color: #0284c7 !important;
    border: 1px solid rgba(2, 132, 199, 0.2) !important;
}
.kpi-badge-research {
    background: rgba(2, 132, 199, 0.08);
    color: #0369a1;
}
.kpi-num-research {
    color: #0284c7 !important;
}

.kpi-theme-approved {
    --kpi-accent: #10b981;
}
.kpi-icon-approved {
    background: rgba(16, 185, 129, 0.08) !important;
    color: #059669 !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
}
.kpi-badge-approved {
    background: rgba(16, 185, 129, 0.08);
    color: #047857;
}
.kpi-num-approved {
    color: #059669 !important;
}

.kpi-theme-rejected {
    --kpi-accent: #ef4444;
}
.kpi-icon-rejected {
    background: rgba(239, 68, 68, 0.08) !important;
    color: #dc2626 !important;
    border: 1px solid rgba(239, 68, 68, 0.2) !important;
}
.kpi-badge-rejected {
    background: rgba(239, 68, 68, 0.08);
    color: #b91c1c;
}
.kpi-num-rejected {
    color: #dc2626 !important;
}

/* Dark Mode Icon Overrides */
.theme-dark .kpi-icon-all {
    background: rgba(5, 150, 105, 0.18) !important;
    color: #34d399 !important;
    border-color: rgba(5, 150, 105, 0.35) !important;
}
.theme-dark .kpi-badge-all {
    background: rgba(5, 150, 105, 0.2);
    color: #34d399;
}
.theme-dark .kpi-icon-pending {
    background: rgba(245, 158, 11, 0.18) !important;
    color: #fbbf24 !important;
    border-color: rgba(245, 158, 11, 0.35) !important;
}
.theme-dark .kpi-badge-pending {
    background: rgba(245, 158, 11, 0.2);
    color: #fbbf24;
}
.theme-dark .kpi-icon-research {
    background: rgba(2, 132, 199, 0.18) !important;
    color: #38bdf8 !important;
    border-color: rgba(2, 132, 199, 0.35) !important;
}
.theme-dark .kpi-badge-research {
    background: rgba(2, 132, 199, 0.2);
    color: #38bdf8;
}
.theme-dark .kpi-icon-approved {
    background: rgba(16, 185, 129, 0.18) !important;
    color: #34d399 !important;
    border-color: rgba(16, 185, 129, 0.35) !important;
}
.theme-dark .kpi-badge-approved {
    background: rgba(16, 185, 129, 0.2);
    color: #34d399;
}
.theme-dark .kpi-icon-rejected {
    background: rgba(239, 68, 68, 0.18) !important;
    color: #f87171 !important;
    border-color: rgba(239, 68, 68, 0.35) !important;
}
.theme-dark .kpi-badge-rejected {
    background: rgba(239, 68, 68, 0.2);
    color: #f87171;
}

/* Icon wrap */
.kpi-icon-wrap {
    width: 50px;
    height: 50px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    flex-shrink: 0;
    transition: transform 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
}
.kpi-card-lux:hover .kpi-icon-wrap {
    transform: scale(1.08);
}
.pulse-indicator-dot {
    position: absolute;
    top: 4px;
    right: 4px;
    width: 8px;
    height: 8px;
    border-radius: 50%;
}
.dot-warning {
    background: #f59e0b;
    box-shadow: 0 0 0 3px rgba(245, 158, 11, 0.35);
    animation: livePulse 1.8s infinite ease-in-out;
}
@keyframes livePulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.4); opacity: 0.5; }
}

/* Source Segmented Control */
.source-seg-btn {
    border: none;
    color: #64748b;
    font-size: 0.85rem;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.source-seg-btn:hover {
    background: rgba(0, 0, 0, 0.04);
    color: #0f172a;
}
.active-seg {
    background: linear-gradient(135deg, #0d9488, #059669) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(13, 148, 136, 0.35) !important;
}
.active-seg-website {
    background: linear-gradient(135deg, #0284c7, #2563eb) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(2, 132, 199, 0.35) !important;
}
.active-seg-system {
    background: linear-gradient(135deg, #059669, #10b981) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(5, 150, 105, 0.35) !important;
}
.active-seg-mobile {
    background: linear-gradient(135deg, #d97706, #f59e0b) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 14px rgba(217, 119, 6, 0.35) !important;
}
.active-seg .badge, .active-seg-website .badge, .active-seg-system .badge, .active-seg-mobile .badge {
    background: rgba(255, 255, 255, 0.25) !important;
    color: #ffffff !important;
}

/* View Mode Buttons */
.view-toggle-btn {
    border: none;
    color: #64748b;
    transition: all 0.2s;
}
.view-toggle-btn.active {
    background: #ffffff;
    color: #065f46;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.08);
}
.theme-dark .view-toggle-btn.active {
    background: rgba(255, 255, 255, 0.15);
    color: #ffffff;
}

/* Search Box Lux */
.search-input-lux .form-control:focus {
    box-shadow: none;
    border-color: #059669;
}

/* Executive Cards */
.case-card-executive {
    background: #ffffff;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 20px;
    overflow: hidden;
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.04);
}
.theme-dark .case-card-executive {
    background: rgba(255, 255, 255, 0.035);
    border-color: rgba(255, 255, 255, 0.08);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2);
}
.case-card-executive:hover {
    transform: translateY(-4px);
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.08);
}
.theme-dark .case-card-executive:hover {
    box-shadow: 0 14px 32px rgba(0, 0, 0, 0.35);
}

/* Card Accent Line */
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
}
.badge-pending {
    background: #fffbeb;
    color: #92400e !important;
    border: 1px solid #fde68a;
}
.badge-research {
    background: #f0f9ff;
    color: #0369a1 !important;
    border: 1px solid #bae6fd;
}
.badge-approved {
    background: #ecfdf5;
    color: #065f46 !important;
    border: 1px solid #a7f3d0;
}
.badge-rejected {
    background: #fef2f2;
    color: #991b1b !important;
    border: 1px solid #fecaca;
}

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

.case-sector-badge {
    background: rgba(0, 0, 0, 0.04);
    color: #334155;
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

.case-source-tag {
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.case-submitter-strip {
    background: rgba(6, 95, 70, 0.06);
    border: 1px solid rgba(6, 95, 70, 0.15);
}
.theme-dark .case-submitter-strip {
    background: rgba(6, 95, 70, 0.12);
    border-color: rgba(6, 95, 70, 0.25);
}

/* Avatar */
.case-applicant-avatar {
    width: 46px;
    height: 46px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 3px 8px rgba(0, 0, 0, 0.07);
}
.avatar-status-pending { background: #fef3c7; color: #92400e; border: 1.5px solid #fde68a; }
.avatar-status-research { background: #e0f2fe; color: #0369a1; border: 1.5px solid #bae6fd; }
.avatar-status-approved { background: #d1fae5; color: #065f46; border: 1.5px solid #a7f3d0; }
.avatar-status-rejected { background: #fee2e2; color: #991b1b; border: 1.5px solid #fecaca; }

.case-applicant-avatar-sm {
    width: 32px;
    height: 32px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 800;
    font-size: 0.85rem;
}

.case-applicant-title {
    font-size: 1.08rem;
    font-weight: 800;
    color: #0f172a;
    line-height: 1.3;
}
.theme-dark .case-applicant-title { color: #f8fafc; }

/* Phone & WhatsApp Links */
.case-phone-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #ecfdf5;
    color: #065f46 !important;
    border: 1px solid #a7f3d0;
    padding: 3px 8px;
    border-radius: 8px;
    font-size: 0.78rem;
    font-weight: 700;
    font-family: var(--font-outfit), system-ui, sans-serif;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.case-phone-link:hover {
    background: #065f46;
    color: #ffffff !important;
    transform: translateY(-1px);
}

.case-whatsapp-link {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    background: #25D366;
    color: #ffffff !important;
    padding: 3px 8px;
    border-radius: 8px;
    font-size: 0.74rem;
    font-weight: 700;
    text-decoration: none !important;
    transition: all 0.2s ease;
}
.case-whatsapp-link:hover {
    background: #128C7E;
    transform: translateY(-1px);
    box-shadow: 0 4px 10px rgba(37, 211, 102, 0.3);
}

.case-copy-btn {
    padding: 2px 6px;
    font-size: 0.75rem;
    color: #64748b;
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 6px;
    background: #ffffff;
}
.case-copy-btn:hover {
    background: #f1f5f9;
    color: #0f172a;
}

/* Meta Details */
.case-meta-details {
    padding: 8px 12px;
    background: rgba(0, 0, 0, 0.02);
    border-radius: 10px;
    border: 1px dashed rgba(0, 0, 0, 0.08);
}
.theme-dark .case-meta-details {
    background: rgba(255, 255, 255, 0.02);
    border-color: rgba(255, 255, 255, 0.08);
}
.case-meta-row {
    display: flex;
    align-items: center;
    gap: 6px;
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

.specialized-strip {
    border: 1px solid transparent;
}
.midrar-strip { background: rgba(2, 132, 199, 0.08); border-color: rgba(2, 132, 199, 0.2); }
.hope-strip { background: rgba(124, 58, 237, 0.08); border-color: rgba(124, 58, 237, 0.2); }
.zad-strip { background: rgba(5, 150, 105, 0.08); border-color: rgba(5, 150, 105, 0.2); }

.case-rejection-alert {
    background: #fef2f2;
    border: 1px solid #fecaca;
}

.case-btn-review {
    background: linear-gradient(135deg, #059669 0%, #065f46 100%);
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

.btn-gradient-primary {
    background: linear-gradient(135deg, #059669 0%, #065f46 100%);
    border: none;
}
.btn-gradient-primary:hover {
    background: linear-gradient(135deg, #047857 0%, #064e3b 100%);
}

/* Floating Bulk Dock */
.bulk-action-bar-floating {
    position: fixed;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    z-index: 1050;
    animation: slideUpFloat 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.bulk-dock-pill {
    background: rgba(15, 23, 42, 0.95);
    backdrop-filter: blur(12px);
    border-color: rgba(255, 255, 255, 0.15) !important;
}
@keyframes slideUpFloat {
    from { opacity: 0; transform: translate(-50%, 20px); }
    to { opacity: 1; transform: translate(-50%, 0); }
}

/* Executive Table */
.executive-data-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 0.82rem;
    font-weight: 700;
    padding: 14px 12px;
    border-bottom: 2px solid #e2e8f0;
}
.theme-dark .executive-data-table th {
    background: rgba(255, 255, 255, 0.05);
    color: #cbd5e1;
    border-color: rgba(255, 255, 255, 0.08);
}
.executive-data-table td {
    padding: 12px;
    border-bottom: 1px solid #f1f5f9;
}
.theme-dark .executive-data-table td {
    border-color: rgba(255, 255, 255, 0.05);
}

/* Modal Dossier Card & Panel Modernization */
.dossier-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
}
.theme-dark .dossier-card {
    background: #152238 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

.admin-decision-panel {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    box-shadow: 0 1px 3px rgba(0,0,0,0.03) !important;
}
.theme-dark .admin-decision-panel {
    background: #152238 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}

@media print {
    @page {
        size: A4 portrait;
        margin: 8mm 10mm;
    }

    body {
        background: #ffffff !important;
        color: #0f172a !important;
        padding: 0 !important;
        margin: 0 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    /* Hide UI navigation, action bars, search, hero headers, buttons */
    header, nav, .navbar, .sidebar, .btn, .dashboard-hero, #bulk-action-bar, 
    .kpi-card-lux, .source-seg-btn, .view-toggle-btn, .search-input-lux, 
    .pagination, .modal-backdrop, .modal-header .btn-close, .modal-header .btn, 
    #createCaseModal, .quick-rejection-chips, .case-card-item, .view-mode-section:not(#tableViewContainer) {
        display: none !important;
    }

    /* Hide any modal that is NOT currently open (Prevents 5 duplicate pages) */
    .modal:not(.show) {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: hidden !important;
    }
    body.modal-open .modal:not(.show) {
        display: none !important;
    }

    /* Only show the currently opened modal */
    .modal.show {
        position: static !important;
        display: block !important;
        background: none !important;
        width: 100% !important;
        max-width: 100% !important;
        padding: 0 !important;
        margin: 0 !important;
        overflow: visible !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
    }

    .modal-dialog {
        max-width: 100% !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        transform: none !important;
    }

    .modal-content {
        border: none !important;
        box-shadow: none !important;
        background: #ffffff !important;
        padding: 0 !important;
    }

    .modal-header {
        background: transparent !important;
        padding: 0 0 8px 0 !important;
        border-bottom: 2px solid #059669 !important;
        margin-bottom: 10px !important;
    }

    .modal-body {
        padding: 0 !important;
        background: transparent !important;
        overflow: visible !important;
    }

    /* Show Print Letterhead & Decision Summary */
    .print-official-header {
        display: flex !important;
    }

    .print-only-decision {
        display: block !important;
        margin-top: 10px !important;
    }

    /* Hide edit forms during print */
    .admin-decision-panel form {
        display: none !important;
    }

    .dossier-card, .admin-decision-panel {
        background: #ffffff !important;
        border: 1px solid #cbd5e1 !important;
        box-shadow: none !important;
        page-break-inside: avoid !important;
        break-inside: avoid !important;
        margin-bottom: 8px !important;
        padding: 10px !important;
    }

    /* Grid formatting for print */
    .col-lg-7 {
        width: 58% !important;
        float: right !important;
    }
    .col-lg-5 {
        width: 42% !important;
        float: left !important;
    }
    .row {
        display: flex !important;
        flex-wrap: wrap !important;
    }

    /* If no modal is open and user prints the table */
    body:not(.modal-open) #tableViewContainer {
        display: block !important;
    }
    body:not(.modal-open) .executive-data-table {
        display: table !important;
        width: 100% !important;
    }
    body:not(.modal-open) .modal {
        display: none !important;
    }
}
</style>
@endsection
