@extends('layouts.app')
@section('content')

{{-- Premium Security Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up bg-primary shadow-sm" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 3rem 2rem;">
    <div class="hero-content">
        <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">الأمان والرقابة 🛡️</div>
        <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">مركز مراقبة النشاطات</h1>
        <p class="hero-subtitle text-white opacity-75 mb-4" style="color: #ffffff !important;">تتبع كامل لجميع العمليات التي تتم عبر النظام لضمان الامتثال والأمان</p>
        <div class="hero-actions d-flex gap-2">
            <a class="btn btn-sm rounded-pill px-4 bg-white text-primary fw-bold shadow hover-lift border-0"
                href="{{ route('audits.index', array_merge(request()->query(), ['export' => 'csv'])) }}">
                <i class="bi bi-cloud-download me-1"></i> تحميل السجلات (CSV)
            </a>
            <a href="{{ route('dashboard.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift" style="border-width: 2px;">
                <i class="bi bi-arrow-right me-1"></i> الرجوع للرئيسية
            </a>
        </div>
    </div>
    <i class="bi bi-shield-check hero-icon text-white opacity-25 d-none d-md-block"></i>
</div>

@php
    $routeMap = [
        'donors' => 'إدارة المتبرعين',
        'donations' => 'تسجيل التبرعات',
        'beneficiaries' => 'بيانات المستفيدين',
        'users' => 'إدارة الموظفين والمستخدمين',
        'items' => 'مخزن الأصناف',
        'expenses' => 'المصروفات المالية',
        'volunteer-attendance' => 'سجل حضور المتطوعين',
        'employee-attendance' => 'سجل حضور الموظفين',
        'tasks' => 'المهام العامة',
        'roles' => 'الأدوار والصلاحيات',
        'leaves' => 'طلبات الإجازات',
        'payrolls' => 'مسيرات الرواتب',
        'projects' => 'المشاريع التنموية',
        'campaigns' => 'الحملات الإغاثية',
        'change-requests' => 'نظام مراجعة التعديلات والموافقة',
        'login' => 'عملية دخول للنظام',
        'logout' => 'عملية خروج من النظام',
        'dashboard' => 'لوحة التحكم الرئيسية',
        'treasuries' => 'إدارة الخزائن المالية',
        'accounts' => 'الدليل المحاسبي',
        'journal-entries' => 'القيود اليومية',
        'suppliers' => 'بيانات الموردين',
        'warehouses' => 'إدارة المستودعات',
        'travel-routes' => 'خطوط السير والعمليات اللوجستية',
        'trips' => 'رحلات المندوبين',
    ];

    $getLabel = function($path) use ($routeMap) {
        $cleanPath = ltrim(parse_url($path, PHP_URL_PATH), '/');
        // Sort keys by length descending to match most specific first
        $keys = array_keys($routeMap);
        usort($keys, function($a, $b) { return strlen($b) - strlen($a); });
        
        foreach ($keys as $key) {
            if (str_starts_with($cleanPath, $key) || str_contains($cleanPath, '/'.$key)) {
                return $routeMap[$key];
            }
        }
        return 'عملية فنية داخلية';
    };

    $permissionsMap = $permissionsMap ?? [];
    $labels = [
        'name' => 'الاسم',
        'title' => 'العنوان',
        'amount' => 'المبلغ',
        'type' => 'النوع',
        'description' => 'الوصف/الملاحظات',
        'notes' => 'ملاحظات إضافية',
        'check_in_at' => 'وقت الحضور',
        'check_out_at' => 'وقت الانصراف',
        'code' => 'كود الحساب',
        'is_active' => 'الحالة',
        'status' => 'الحالة',
        'permissions' => 'الصلاحيات',
        'phone' => 'رقم الهاتف',
        'job_title' => 'المسمى الوظيفي',
        'reason' => 'السبب',
    ];

    $valueMaps = [
        'active' => 'نشط ✅',
        'inactive' => 'غير نشط ❌',
        'asset' => 'أصول',
        'liability' => 'خصوم',
        'equity' => 'حقوق ملكية',
        'revenue' => 'إيرادات',
        'expense' => 'مصروفات',
    ];

    $fmtPayload = function($k, $v) use ($labels, $valueMaps, $permissionsMap) {
        if (is_null($v) || $v === '') return '<span class="text-muted small">فارغ</span>';
        if (is_array($v)) {
            if ($k === 'permissions') {
                $html = '<div class="d-flex flex-wrap gap-1 mt-1">';
                foreach ($v as $pId) {
                    $pName = $permissionsMap[$pId] ?? ($permissionsMap[(int)$pId] ?? "#$pId");
                    $html .= '<span class="badge rounded-pill bg-light text-dark border p-1 px-2" style="font-size: 9px;">' . e($pName) . '</span>';
                }
                $html .= '</div>';
                return $html;
            }
            return '<pre class="x-small mb-0 mt-1 bg-white p-2 rounded border" style="font-size: 9px;">' . json_encode($v, JSON_UNESCAPED_UNICODE) . '</pre>';
        }

        if (isset($valueMaps[$v])) return $valueMaps[$v];
        if (in_array($k, ['amount','cost','salary'])) return '<strong>' . number_format((float)$v, 2) . '</strong> <small>ج.م</small>';
        if (is_bool($v)) return $v ? '<span class="text-success">نعم</span>' : '<span class="text-danger">لا</span>';
        
        return e($v);
    };
@endphp

<div class="container-fluid py-4">
    <div class="row g-4">
        {{-- Stats Overview --}}
        <div class="col-xl-3">
            <div class="glass-card mb-4 animate-slide-up animate-delay-1 p-4">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-light border-opacity-10">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-pie-chart-fill me-2"></i>ملخص العمليات
                    </h6>
                </div>
                
                <div class="row g-3">
                    {{-- Total Stats --}}
                    <div class="col-12">
                        <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => ''])) }}" 
                           class="d-block p-3 rounded-4 bg-primary bg-opacity-10 text-decoration-none hover-lift border border-primary border-opacity-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-primary text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-layers-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-primary fw-bold" style="font-size: 0.9rem;">إجمالي النشاط</div>
                                        <div class="text-muted x-small">كافة العمليات</div>
                                    </div>
                                </div>
                                <div class="h4 fw-bold mb-0 text-primary">{{ number_format($stats['total']) }}</div>
                            </div>
                        </a>
                    </div>

                    {{-- POST - Add --}}
                    <div class="col-12">
                        <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'POST'])) }}"
                           class="d-block p-3 rounded-4 bg-success bg-opacity-10 text-decoration-none hover-lift border border-success border-opacity-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-success text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-plus-lg fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-success fw-bold" style="font-size: 0.9rem;">إضافة سجلات</div>
                                        <div class="text-muted x-small">عمليات POST</div>
                                    </div>
                                </div>
                                <div class="h4 fw-bold mb-0 text-success">{{ number_format($stats['POST']) }}</div>
                            </div>
                        </a>
                    </div>

                    {{-- PUT/PATCH - Edit --}}
                    <div class="col-12">
                        <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'PUT'])) }}"
                           class="d-block p-3 rounded-4 bg-info bg-opacity-10 text-decoration-none hover-lift border border-info border-opacity-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-info text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-pencil-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-info fw-bold" style="font-size: 0.9rem;">تعديل بيانات</div>
                                        <div class="text-muted x-small">عمليات Update</div>
                                    </div>
                                </div>
                                <div class="h4 fw-bold mb-0 text-info">{{ number_format($stats['PUT'] + $stats['PATCH']) }}</div>
                            </div>
                        </a>
                    </div>

                    {{-- DELETE --}}
                    <div class="col-12">
                        <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'DELETE'])) }}"
                           class="d-block p-3 rounded-4 bg-danger bg-opacity-10 text-decoration-none hover-lift border border-danger border-opacity-10">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="bg-danger text-white rounded-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-trash3-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="text-danger fw-bold" style="font-size: 0.9rem;">حذف نهائي</div>
                                        <div class="text-muted x-small">عمليات Delete</div>
                                    </div>
                                </div>
                                <div class="h4 fw-bold mb-0 text-danger">{{ number_format($stats['DELETE']) }}</div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>

            <div class="glass-card animate-slide-up animate-delay-2 p-4">
                <div class="d-flex align-items-center justify-content-between mb-4 pb-2 border-bottom border-light border-opacity-10">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="bi bi-graph-up-arrow me-2"></i>أكثر المسارات نشاطاً
                    </h6>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill small">تحديث اللحظي</span>
                </div>
                <div class="top-paths-list">
                    @forelse($topPaths as $tp)
                        @php
                            $label = $getLabel($tp->path);
                            $icon = match(true) {
                                str_contains($tp->path, 'donation') => 'bi-heart-fill',
                                str_contains($tp->path, 'donor') => 'bi-person-heart',
                                str_contains($tp->path, 'user') => 'bi-people-fill',
                                str_contains($tp->path, 'task') => 'bi-check2-circle',
                                str_contains($tp->path, 'finance') || str_contains($tp->path, 'expense') => 'bi-cash-coin',
                                str_contains($tp->path, 'project') => 'bi-diagram-3-fill',
                                str_contains($tp->path, 'login') => 'bi-door-open-fill',
                                default => 'bi-activity'
                            };
                        @endphp
                        <div class="top-path-item-premium mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="path-icon-wrapper bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px; flex-shrink: 0;">
                                        <i class="bi {{ $icon }} small"></i>
                                    </div>
                                    <div class="overflow-hidden">
                                        <a href="{{ url($tp->path) }}" target="_blank" class="fw-bold text-decoration-none text-main d-block text-truncate" style="font-size: 0.85rem;" title="{{ $label }}">
                                            {{ $label }}
                                        </a>
                                        <div class="text-muted font-monospace x-small text-truncate" style="opacity: 0.6;">/{{ ltrim($tp->path, '/') }}</div>
                                    </div>
                                </div>
                                <div class="text-end ms-2">
                                    <span class="fw-bold text-primary">{{ number_format($tp->c) }}</span>
                                    <div class="x-small text-muted opacity-50">عملية</div>
                                </div>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 10px; background: rgba(var(--primary-rgb), 0.05);">
                                <div class="progress-bar bg-primary shadow-sm" 
                                     role="progressbar" 
                                     style="width: {{ ($tp->c / max($topPaths->pluck('c')->all() ?: [1])) * 100 }}%; border-radius: 10px; transition: width 1s ease-in-out;" 
                                     aria-valuenow="{{ $tp->c }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ max($topPaths->pluck('c')->all() ?: [1]) }}">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 opacity-25 d-block mb-2"></i>
                            <p class="small mb-0">لا يوجد بيانات كافية حالياً</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Main Feed --}}
        <div class="col-xl-9">
            {{-- Search & Filters --}}
            <div class="glass-card mb-4 animate-slide-up animate-delay-1 p-0 overflow-hidden audits-filter-card">
                <div class="p-3 border-bottom bg-light bg-opacity-25 filter-header">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-funnel-fill text-primary"></i>
                        <span class="fw-bold small text-muted">تصفية السجلات والبحث</span>
                    </div>
                </div>
                <div class="p-4 audits-filter">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label x-small fw-bold mb-2">البحث في المسار (Path)</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text border-end-0 text-primary"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control border-start-0 py-2" value="{{ $q }}" placeholder="مثلاً: users أو donations...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small fw-bold mb-2">نوع العملية</label>
                            <select name="method" class="form-select form-select-sm py-2 shadow-none">
                                <option value="">الكل</option>
                                @foreach(['GET', 'POST', 'PUT', 'DELETE'] as $m)
                                    <option value="{{ $m }}" @selected($method === $m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small fw-bold mb-2">خلال (يوم)</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text border-end-0"><i class="bi bi-calendar3 text-primary"></i></span>
                                <input type="number" name="days" class="form-control border-start-0 py-2" value="{{ $days }}" placeholder="14">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small fw-bold mb-2">رقم المستخدم</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text border-end-0"><i class="bi bi-person-badge text-primary"></i></span>
                                <input type="number" name="user_id" class="form-control border-start-0 py-2" value="{{ $uid }}" placeholder="ID">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button class="btn audit-filter-btn w-100 py-2">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Audit Timeline --}}
            <div class="audit-timeline animate-slide-up animate-delay-2 p-1">
                @forelse($audits as $a)
                    @php
                        $user = $usersMap->get($a->user_id);
                        $methodColor = match($a->method) {
                            'POST' => 'success',
                            'PUT', 'PATCH' => 'primary',
                            'DELETE' => 'danger',
                            default => 'secondary'
                        };
                        $methodGlow = match($a->method) {
                            'POST' => 'method-glow-success',
                            'PUT', 'PATCH' => 'method-glow-primary',
                            'DELETE' => 'method-glow-danger',
                            default => ''
                        };
                        $methodLabel = match($a->method) {
                            'POST' => 'إضافة',
                            'PUT', 'PATCH' => 'تعديل',
                            'DELETE' => 'حذف',
                            'GET' => 'عرض',
                            default => $a->method
                        };
                    @endphp
                    <div class="audit-card mb-4 p-3 hover-lift">
                        <div class="d-flex align-items-start gap-3">
                            <div class="audit-avatar shadow-sm border {{ $a->id % 2 == 0 ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }} overflow-hidden">
                                @if($user && $user->profile_photo_path)
                                    <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    {{ mb_substr($user->name ?? '?', 0, 1) }}
                                @endif
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <h6 class="mb-0 fw-bold">{{ $user->name ?? 'مستخدم مجهول' }} <span class="text-muted small fw-normal ms-1">(ID: {{ $a->user_id ?? '—' }})</span></h6>
                                    <span class="text-muted x-small"><i class="bi bi-clock me-1"></i>{{ $a->created_at->diffForHumans() }}</span>
                                </div>
                                <div class="d-flex align-items-center flex-wrap gap-2 mb-2">
                                    <span class="badge bg-{{ $methodColor }} bg-opacity-10 text-{{ $methodColor }} border border-{{ $methodColor }} border-opacity-10 py-1 px-2 x-small {{ $methodGlow }}">
                                        {{ $methodLabel }}
                                    </span>
                                    <span class="text-primary fw-bold small"><i class="bi bi-tag-fill me-1"></i> {{ $getLabel($a->path) }}</span>
                                    <span class="text-muted x-small font-monospace opacity-75">/{{ ltrim($a->path, '/') }}</span>
                                </div>
                                
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-muted x-small">
                                        <i class="bi bi-laptop me-1"></i> {{ $a->ip }} 
                                        <span class="mx-2 text-silver">|</span>
                                        @if($a->status_code)
                                            <span class="text-{{ $a->status_code < 400 ? 'success' : 'danger' }}">الحالة: {{ $a->status_code }}</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 glass-card">
                        <i class="bi bi-shield-slash display-4 text-muted opacity-25 mb-3 d-block"></i>
                        <h5 class="text-muted">السجل نظيف تماماً</h5>
                        <p class="text-muted small">لا توجد أي نشاطات مسجلة ضمن المعايير المختارة.</p>
                    </div>
                @endforelse

                <div class="mt-4">
                    {{ $audits->appends(request()->query())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

    <style>
        .section-title-sm {
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            color: var(--primary);
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .section-title-sm::before {
            content: '';
            width: 4px;
            height: 18px;
            border-radius: 4px;
            background: var(--primary);
            flex-shrink: 0;
        }
        .top-path-item {
            background: var(--bg-card);
            border: 1px solid var(--gray-200);
            border-radius: 10px;
            padding: 0.75rem;
            margin-bottom: 0.75rem;
            transition: all 0.2s ease;
        }
        .theme-dark .top-path-item {
            background: rgba(255,255,255,0.03);
            border-color: rgba(255,255,255,0.06);
        }
        .top-path-item:hover {
            border-color: var(--primary);
            background: var(--primary-subtle);
        }
        
        .audits-filter .form-control, .audits-filter .form-select, .audits-filter .input-group-text {
            background-color: var(--bg-card);
            border-color: var(--gray-200);
            color: var(--text-main);
        }
        .theme-dark .audits-filter .form-control, .theme-dark .audits-filter .form-select, .theme-dark .audits-filter .input-group-text {
            background-color: rgba(255,255,255,0.06);
            border-color: rgba(255,255,255,0.1);
            color: #fff;
        }
        
        .audit-avatar {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1rem;
            flex-shrink: 0;
        }
        .audit-filter-btn {
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        .audit-filter-btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
            color: #fff;
        }
    </style>

@endsection


