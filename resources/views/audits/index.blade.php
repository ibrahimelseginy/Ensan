@extends('layouts.app')
@section('content')

{{-- Premium Security Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up bg-primary shadow-sm" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 3rem 2rem;">
    <div class="hero-content">
        <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">الأمان والرقابة 🛡️</div>
        <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">مركز مراقبة النشاطات</h1>
        <p class="hero-subtitle text-white opacity-75 mb-4" style="color: #ffffff !important;">تتبع كامل لجميع العمليات التي تتم عبر النظام لضمان الامتثال والأمان</p>
        <div class="hero-actions d-flex gap-2">
            <a class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold shadow-sm hover-lift" style="border-width: 2px;"
                href="{{ route('audits.index', array_merge(request()->query(), ['export' => 'csv'])) }}">
                <i class="bi bi-cloud-download me-1"></i> تحميل السجلات (CSV)
            </a>
            <a href="{{ route('dashboard.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold shadow-sm hover-lift" style="border-width: 2px;">
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
        'case-applications' => 'طلبات واستمارات الحالات',
        'cases' => 'إدارة الحالات والمساعدات',
        'data-import' => 'استيراد البيانات الذكي',
        'employee-tasks' => 'مهام الموظفين',
        'guest-houses' => 'دار الضيافة لمرضى الأورام',
        'audits' => 'سجل العمليات والأمان',
        'notifications' => 'إشعارات وتنبيهات النظام',
        'livewire' => 'تحديث مباشر للبيانات',
        'reports' => 'التقارير والإحصائيات',
        'journal-entries' => 'القيود اليومية',
        'suppliers' => 'بيانات الموردين',
        'warehouses' => 'إدارة المستودعات',
        'travel-routes' => 'خطوط السير والعمليات اللوجستية',
        'trips' => 'رحلات المندوبين',
    ];

    $getLabel = function($path) use ($routeMap) {
        $cleanPath = ltrim(parse_url($path, PHP_URL_PATH) ?? '', '/');
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

    $formatDisplayPath = function($path) {
        $clean = ltrim(parse_url($path, PHP_URL_PATH) ?? $path, '/');
        $segments = explode('/', $clean);
        $simplified = array_map(function($segment) {
            if (strlen($segment) > 16 && preg_match('/^[a-zA-Z0-9_\-]+$/', $segment)) {
                return substr($segment, 0, 8) . '…';
            }
            return $segment;
        }, $segments);
        return '/' . implode('/', $simplified);
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
            {{-- Operations Summary Card --}}
            <div class="glass-card mb-4 animate-slide-up animate-delay-1 p-3 p-xl-4 border border-light border-opacity-25 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light border-opacity-25">
                    <h6 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-pie-chart-fill fs-5"></i>ملخص العمليات
                    </h6>
                    <span class="audit-total-counter-pill">
                        <i class="bi bi-activity"></i>
                        <span>{{ number_format($stats['total']) }} عملية</span>
                    </span>
                </div>
                
                <div class="d-flex flex-column gap-2">
                    @php
                        $totalOps = max($stats['total'], 1);
                        $currentMethod = request('method');
                    @endphp

                    {{-- Total Stats --}}
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => ''])) }}" 
                       class="stat-pill-card stat-pill-all {{ empty($currentMethod) ? 'active' : '' }}">
                        <div class="stat-pill-icon">
                            <i class="bi bi-layers-fill"></i>
                        </div>
                        <div class="stat-pill-info">
                            <div class="stat-pill-title">إجمالي النشاط</div>
                            <div class="stat-pill-sub">كافة العمليات والأنشطة</div>
                        </div>
                        <div class="stat-pill-val">
                            <span class="stat-num text-emerald">{{ number_format($stats['total']) }}</span>
                            <span class="stat-percent-badge percent-emerald">100%</span>
                        </div>
                    </a>

                    {{-- POST - Add --}}
                    @php $postPercent = round(($stats['POST'] / $totalOps) * 100); @endphp
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'POST'])) }}"
                       class="stat-pill-card stat-pill-post {{ $currentMethod === 'POST' ? 'active' : '' }}">
                        <div class="stat-pill-icon">
                            <i class="bi bi-plus-lg"></i>
                        </div>
                        <div class="stat-pill-info">
                            <div class="stat-pill-title">إضافة سجلات</div>
                            <div class="stat-pill-sub">عمليات POST</div>
                        </div>
                        <div class="stat-pill-val">
                            <span class="stat-num text-success">
                                {{ number_format($stats['POST']) }}
                            </span>
                            @if($stats['POST'] > 0)
                                <span class="stat-percent-badge percent-success">{{ $postPercent }}%</span>
                            @endif
                        </div>
                    </a>

                    {{-- PUT/PATCH - Edit --}}
                    @php 
                        $editCount = $stats['PUT'] + $stats['PATCH'];
                        $editPercent = round(($editCount / $totalOps) * 100); 
                    @endphp
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'PUT'])) }}"
                       class="stat-pill-card stat-pill-edit {{ in_array($currentMethod, ['PUT', 'PATCH']) ? 'active' : '' }}">
                        <div class="stat-pill-icon">
                            <i class="bi bi-pencil-fill"></i>
                        </div>
                        <div class="stat-pill-info">
                            <div class="stat-pill-title">تعديل وتحديث</div>
                            <div class="stat-pill-sub">عمليات PUT & PATCH</div>
                        </div>
                        <div class="stat-pill-val">
                            <span class="stat-num text-info">
                                {{ number_format($editCount) }}
                            </span>
                            @if($editCount > 0)
                                <span class="stat-percent-badge percent-info">{{ $editPercent }}%</span>
                            @endif
                        </div>
                    </a>

                    {{-- DELETE --}}
                    @php $delPercent = round(($stats['DELETE'] / $totalOps) * 100); @endphp
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'DELETE'])) }}"
                       class="stat-pill-card stat-pill-delete {{ $currentMethod === 'DELETE' ? 'active' : '' }}">
                        <div class="stat-pill-icon">
                            <i class="bi bi-trash3-fill"></i>
                        </div>
                        <div class="stat-pill-info">
                            <div class="stat-pill-title">حذف نهائي</div>
                            <div class="stat-pill-sub">عمليات DELETE</div>
                        </div>
                        <div class="stat-pill-val">
                            <span class="stat-num text-danger">
                                {{ number_format($stats['DELETE']) }}
                            </span>
                            @if($stats['DELETE'] > 0)
                                <span class="stat-percent-badge percent-danger">{{ $delPercent }}%</span>
                            @endif
                        </div>
                    </a>
                </div>
            </div>

            {{-- Top Active Routes Card --}}
            <div class="glass-card animate-slide-up animate-delay-2 p-3 p-xl-4 border border-light border-opacity-25 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light border-opacity-25">
                    <h6 class="fw-bold mb-0 text-primary d-flex align-items-center gap-2">
                        <i class="bi bi-graph-up-arrow fs-5"></i>أكثر المسارات نشاطاً
                    </h6>
                    <span class="live-pulse-badge">
                        <span class="live-dot"></span>تحديث مباشر
                    </span>
                </div>
                <div class="top-paths-list d-flex flex-column gap-2.5">
                    @php $maxCount = max($topPaths->pluck('c')->all() ?: [1]); @endphp
                    @forelse($topPaths as $tp)
                        @php
                            $label = $getLabel($tp->path);
                            $cleanPath = $formatDisplayPath($tp->path);
                            $icon = match(true) {
                                str_contains($tp->path, 'donation') => 'bi-heart-fill',
                                str_contains($tp->path, 'donor') => 'bi-person-heart',
                                str_contains($tp->path, 'user') => 'bi-people-fill',
                                str_contains($tp->path, 'task') => 'bi-check2-circle',
                                str_contains($tp->path, 'finance') || str_contains($tp->path, 'expense') || str_contains($tp->path, 'treasur') => 'bi-cash-coin',
                                str_contains($tp->path, 'project') => 'bi-diagram-3-fill',
                                str_contains($tp->path, 'login') => 'bi-door-open-fill',
                                str_contains($tp->path, 'case') => 'bi-file-earmark-medical-fill',
                                default => 'bi-activity'
                            };
                            $ratio = round(($tp->c / $maxCount) * 100);
                        @endphp
                        <div class="top-path-item-modern p-3 rounded-3 border">
                            <div class="d-flex justify-content-between align-items-center mb-2 gap-2">
                                <div class="d-flex align-items-center gap-2.5 overflow-hidden flex-grow-1 min-w-0">
                                    <div class="path-icon-sq rounded-2 d-flex align-items-center justify-content-center flex-shrink-0">
                                        <i class="bi {{ $icon }} fs-6"></i>
                                    </div>
                                    <div class="overflow-hidden min-w-0 flex-grow-1">
                                        <a href="{{ url($tp->path) }}" target="_blank" class="fw-bold text-decoration-none text-main d-block text-truncate" style="font-size: 0.88rem;" title="{{ $label }}">
                                            {{ $label }}
                                        </a>
                                        <div class="text-muted font-monospace x-small text-truncate mt-0.5" dir="ltr" style="text-align: right; opacity: 0.8;" title="{{ $tp->path }}">
                                            {{ $cleanPath }}
                                        </div>
                                    </div>
                                </div>
                                <div class="text-end flex-shrink-0">
                                    <span class="audit-route-count-badge">
                                        <span class="fw-bold">{{ number_format($tp->c) }}</span>
                                        <span class="audit-route-unit">عملية</span>
                                    </span>
                                </div>
                            </div>
                            <div class="progress" style="height: 6px; border-radius: 9999px; background: rgba(5, 150, 105, 0.08);">
                                <div class="progress-bar rounded-pill" 
                                     role="progressbar" 
                                     style="width: {{ $ratio }}%; background: linear-gradient(90deg, #059669 0%, #10b981 100%); transition: width 0.8s ease;" 
                                     aria-valuenow="{{ $tp->c }}" 
                                     aria-valuemin="0" 
                                     aria-valuemax="{{ $maxCount }}">
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-inbox fs-2 opacity-25 d-block mb-2"></i>
                            <p class="small mb-0">لا توجد بيانات مسجلة حالياً</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Main Feed --}}
        <div class="col-xl-9" style="position: relative; overflow-x: clip;">
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
        /* High-Contrast Counter Pills */
        .audit-total-counter-pill {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 12px;
            border-radius: 9999px;
            font-size: 0.82rem;
            font-weight: 800;
            background: #ecfdf5;
            color: #047857 !important;
            border: 1.5px solid #a7f3d0;
            box-shadow: 0 1px 3px rgba(4, 120, 87, 0.08);
            line-height: 1.2;
        }
        .theme-dark .audit-total-counter-pill {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7 !important;
            border-color: rgba(16, 185, 129, 0.4);
        }

        .audit-route-count-badge {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 9999px;
            font-size: 0.82rem;
            background: #f0fdf4;
            color: #15803d !important;
            border: 1.5px solid #86efac;
            box-shadow: 0 1px 3px rgba(21, 128, 61, 0.08);
            line-height: 1.2;
            white-space: nowrap;
        }
        .audit-route-count-badge .audit-route-unit {
            font-size: 0.7rem;
            font-weight: 600;
            opacity: 0.85;
        }
        .theme-dark .audit-route-count-badge {
            background: rgba(16, 185, 129, 0.22);
            color: #86efac !important;
            border-color: rgba(134, 239, 172, 0.4);
        }

        /* Modern Stat Pill Cards */
        .stat-pill-card {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 14px;
            border-radius: 14px;
            background: var(--bg-card, #ffffff);
            border: 1px solid var(--border-color, rgba(0,0,0,0.08));
            text-decoration: none !important;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            position: relative;
            overflow: hidden;
        }
        .theme-dark .stat-pill-card {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .stat-pill-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
        }

        /* Specific card styles */
        .stat-pill-all .stat-pill-icon {
            background: linear-gradient(135deg, #059669 0%, #047857 100%);
            color: #ffffff;
        }
        .stat-pill-all.active {
            border-color: #059669;
            background: #f0fdf4;
            box-shadow: 0 0 0 1.5px #059669;
        }
        .theme-dark .stat-pill-all.active {
            background: rgba(5, 150, 105, 0.15);
        }

        .stat-pill-post .stat-pill-icon {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            color: #ffffff;
        }
        .stat-pill-post.active {
            border-color: #10b981;
            background: #ecfdf5;
            box-shadow: 0 0 0 1.5px #10b981;
        }
        .theme-dark .stat-pill-post.active {
            background: rgba(16, 185, 129, 0.15);
        }

        .stat-pill-edit .stat-pill-icon {
            background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
            color: #ffffff;
        }
        .stat-pill-edit.active {
            border-color: #0284c7;
            background: #f0f9ff;
            box-shadow: 0 0 0 1.5px #0284c7;
        }
        .theme-dark .stat-pill-edit.active {
            background: rgba(2, 132, 199, 0.15);
        }

        .stat-pill-delete .stat-pill-icon {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: #ffffff;
        }
        .stat-pill-delete.active {
            border-color: #ef4444;
            background: #fef2f2;
            box-shadow: 0 0 0 1.5px #ef4444;
        }
        .theme-dark .stat-pill-delete.active {
            background: rgba(239, 68, 68, 0.15);
        }

        .stat-pill-icon {
            width: 40px;
            height: 40px;
            border-radius: 11px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.15rem;
            flex-shrink: 0;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }
        .stat-pill-info {
            flex: 1;
            min-width: 0;
        }
        .stat-pill-title {
            font-weight: 800;
            font-size: 0.9rem;
            color: var(--text-main, #1e293b);
            line-height: 1.25;
        }
        .theme-dark .stat-pill-title {
            color: #f1f5f9;
        }
        .stat-pill-sub {
            font-size: 0.72rem;
            color: #64748b;
            line-height: 1.2;
            margin-top: 2px;
        }
        .stat-pill-val {
            display: flex;
            flex-direction: column;
            align-items: flex-end;
            gap: 3px;
            flex-shrink: 0;
        }
        .stat-num {
            font-weight: 900;
            font-size: 1.2rem;
            line-height: 1;
            font-family: system-ui, -apple-system, sans-serif;
        }
        .text-emerald { color: #047857 !important; }
        .theme-dark .text-emerald { color: #34d399 !important; }

        /* Percent Badges */
        .stat-percent-badge {
            font-size: 0.68rem;
            font-weight: 800;
            padding: 2px 7px;
            border-radius: 9999px;
            line-height: 1.2;
            border: 1px solid transparent;
        }
        .percent-emerald {
            background: #ecfdf5;
            color: #047857 !important;
            border-color: #a7f3d0;
        }
        .percent-success {
            background: #f0fdf4;
            color: #15803d !important;
            border-color: #bbf7d0;
        }
        .percent-info {
            background: #f0f9ff;
            color: #0369a1 !important;
            border-color: #bae6fd;
        }
        .percent-danger {
            background: #fef2f2;
            color: #b91c1c !important;
            border-color: #fecaca;
        }

        /* Modern Top Path Items */
        .top-path-item-modern {
            background: var(--bg-card, #ffffff);
            border: 1px solid var(--border-color, rgba(0,0,0,0.08));
            border-radius: 12px;
            transition: all 0.2s ease;
        }
        .theme-dark .top-path-item-modern {
            background: rgba(255, 255, 255, 0.04);
            border-color: rgba(255, 255, 255, 0.08);
        }
        .top-path-item-modern:hover {
            border-color: #10b981;
            transform: translateX(-3px);
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
        }
        .path-icon-sq {
            width: 36px;
            height: 36px;
            background: #ecfdf5;
            color: #059669;
            border: 1px solid #a7f3d0;
        }
        .theme-dark .path-icon-sq {
            background: rgba(16, 185, 129, 0.15);
            color: #34d399;
            border-color: rgba(16, 185, 129, 0.3);
        }

        /* Live Pulse Badge */
        .live-pulse-badge {
            background: #ecfdf5;
            color: #047857 !important;
            border: 1.5px solid #a7f3d0;
            border-radius: 9999px;
            padding: 3px 10px;
            font-size: 0.74rem;
            font-weight: 800;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .theme-dark .live-pulse-badge {
            background: rgba(16, 185, 129, 0.18);
            color: #6ee7b7 !important;
            border-color: rgba(16, 185, 129, 0.35);
        }
        .live-pulse-badge .live-dot {
            width: 8px;
            height: 8px;
            background: #10b981;
            border-radius: 50%;
            display: inline-block;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
            animation: livePulseDot 2s infinite ease-in-out;
        }
        @keyframes livePulseDot {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.35); opacity: 0.65; }
        }

        /* Timeline containment - prevent dots from leaking across columns */
        .col-xl-9 {
            position: relative;
            overflow-x: clip;
        }
        .audit-timeline {
            position: relative;
            padding-right: 28px;
            padding-left: 6px;
        }
        .audit-timeline::before {
            content: '';
            position: absolute;
            right: 13px;
            top: 15px;
            bottom: 15px;
            width: 2px;
            background: linear-gradient(to bottom, var(--primary, #059669) 0%, rgba(var(--primary-rgb, 5, 150, 105), 0.12) 100%);
            border-radius: 2px;
        }
        .audit-card {
            position: relative;
            border: 1px solid var(--border-color, rgba(0,0,0,0.06));
            border-radius: 16px;
            background: var(--bg-card, #ffffff);
        }
        .theme-dark .audit-card {
            background: rgba(255, 255, 255, 0.03);
            border-color: rgba(255, 255, 255, 0.07);
        }
        .audit-card::after {
            content: '';
            position: absolute;
            right: -20px;
            top: 24px;
            width: 10px;
            height: 10px;
            background: var(--primary, #059669);
            border-radius: 50%;
            border: 2px solid var(--bg-card, #ffffff);
            box-shadow: 0 0 0 3px rgba(var(--primary-rgb, 5, 150, 105), 0.18);
            z-index: 2;
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


