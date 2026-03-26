@extends('layouts.app')
@section('content')

{{-- Premium Security Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%); border-bottom: 1px solid rgba(255,255,255,0.1);">
    <div class="hero-content">
        <div class="hero-greeting text-info">الأمان والرقابة ًں›،ï¸ڈ</div>
        <h1 class="hero-title">مركز مراقبة النشاطات</h1>
        <p class="hero-subtitle">تتبع كامل لجميع العمليات التي تتم عبر النظام لضمان الامتثال والأمان</p>
        <div class="hero-actions d-flex gap-2">
            <a class="btn btn-sm rounded-pill px-4 btn-info text-white"
                href="{{ route('audits.index', array_merge(request()->query(), ['export' => 'csv'])) }}">
                <i class="bi bi-cloud-download me-1"></i> تحميل السجلات (CSV)
            </a>
            <a href="{{ route('dashboard.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light">
                <i class="bi bi-arrow-right me-1"></i> الرجوع للرئيسية
            </a>
        </div>
    </div>
    <div class="d-none d-lg-block position-absolute top-50 end-0 translate-middle-y me-5 opacity-25">
        <i class="bi bi-shield-check" style="font-size: 10rem; color: #38bdf8;"></i>
    </div>
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
        'active' => 'نشط âœ…',
        'inactive' => 'غير نشط â‌Œ',
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
            <div class="glass-card mb-4 animate-slide-up animate-delay-1">
                <h6 class="fw-bold mb-3 section-title-sm">ملخص العمليات</h6>
                <div class="list-group list-group-flush bg-transparent">
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => ''])) }}" 
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="text-muted"><i class="bi bi-layers me-2"></i>الإجمالي</span>
                        <span class="fw-bold text-dark">{{ number_format($stats['total']) }}</span>
                    </a>
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'POST'])) }}"
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="text-success small"><i class="bi bi-plus-circle me-2"></i>إضافة (POST)</span>
                        <span class="badge bg-success-subtle text-success rounded-pill">{{ number_format($stats['POST']) }}</span>
                    </a>
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'PUT'])) }}"
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="text-primary small"><i class="bi bi-pencil me-2"></i>تعديل (PUT)</span>
                        <span class="badge bg-primary-subtle text-primary rounded-pill">{{ number_format($stats['PUT'] + $stats['PATCH']) }}</span>
                    </a>
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'DELETE'])) }}"
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="text-danger small"><i class="bi bi-trash me-2"></i>حذف (DELETE)</span>
                        <span class="badge bg-danger-subtle text-danger rounded-pill">{{ number_format($stats['DELETE']) }}</span>
                    </a>
                </div>
            </div>

            <div class="glass-card animate-slide-up animate-delay-2">
                <h6 class="fw-bold mb-3 section-title-sm">أكثر المسارات نشاطاً</h6>
                <div class="small">
                    @forelse($topPaths as $tp)
                        <div class="mb-3 p-2 rounded bg-light bg-opacity-50 border-start border-4 border-info">
                            <div class="d-flex justify-content-between align-items-start mb-1 gap-2">
                                <a href="{{ url($tp->path) }}" target="_blank" class="text-dark fw-bold text-decoration-none d-block overflow-hidden" style="font-size: 11px;">
                                    <i class="bi bi-box-arrow-up-right me-1 text-info"></i>
                                    {{ $getLabel($tp->path) }} 
                                    <div class="text-muted fw-normal font-monospace x-small">/{{ ltrim($tp->path, '/') }}</div>
                                </a>
                                <span class="badge bg-info text-white rounded-pill x-small">{{ $tp->c }}</span>
                            </div>
                            <div class="progress" style="height: 3px; background: rgba(0,0,0,0.05);">
                                <div class="progress-bar bg-info" style="width: {{ ($tp->c / max($topPaths->pluck('c')->all())) * 100 }}%"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-2">لا يوجد بيانات ع©افغŒة</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Main Feed --}}
        <div class="col-xl-9">
            {{-- Search & Filters --}}
            {{-- Search & Filters --}}
            <div class="glass-card mb-4 animate-slide-up animate-delay-1 p-0 overflow-hidden border-0 shadow-sm" style="background: rgba(255,255,255,0.02); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.05) !important;">
                <div class="p-3 border-bottom border-light border-opacity-10 bg-white bg-opacity-5">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-filter-right text-info"></i>
                        <span class="fw-bold small text-muted">تصفية السجلات والبحث</span>
                    </div>
                </div>
                <div class="p-4">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label x-small text-muted fw-bold mb-2">البحث في المسار (Path)</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text bg-white bg-opacity-5 border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" name="q" class="form-control bg-white bg-opacity-5 border-start-0 py-2" value="{{ $q }}" placeholder="مثلاً: users أو donations...">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small text-muted fw-bold mb-2">نوع العملية</label>
                            <select name="method" class="form-select form-select-sm bg-white bg-opacity-5 py-2 shadow-none">
                                <option value="">الكل</option>
                                @foreach(['GET', 'POST', 'PUT', 'DELETE'] as $m)
                                    <option value="{{ $m }}" @selected($method === $m)>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small text-muted fw-bold mb-2">خلال (يوم)</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text bg-white bg-opacity-5 border-end-0 text-muted"><i class="bi bi-calendar3"></i></span>
                                <input type="number" name="days" class="form-control bg-white bg-opacity-5 border-start-0 py-2" value="{{ $days }}" placeholder="14">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small text-muted fw-bold mb-2">رقم المستخدم</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text bg-white bg-opacity-5 border-end-0 text-muted"><i class="bi bi-person-badge"></i></span>
                                <input type="number" name="user_id" class="form-control bg-white bg-opacity-5 border-start-0 py-2" value="{{ $uid }}" placeholder="ID">
                            </div>
                        </div>
                        <div class="col-md-1">
                            <button class="btn btn-info text-white w-100 py-2 rounded shadow-sm border-0 transition-all hover-lift">
                                <i class="bi bi-arrow-repeat"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Audit Timeline --}}
            <div class="audit-timeline animate-slide-up animate-delay-2">
                @forelse($audits as $a)
                    @php
                        $user = $usersMap->get($a->user_id);
                        $methodColor = match($a->method) {
                            'POST' => 'success',
                            'PUT', 'PATCH' => 'primary',
                            'DELETE' => 'danger',
                            default => 'secondary'
                        };
                        $methodLabel = match($a->method) {
                            'POST' => 'إضافة',
                            'PUT', 'PATCH' => 'تعديل',
                            'DELETE' => 'حذف',
                            'GET' => 'عرض',
                            default => $a->method
                        };
                    @endphp
                    <div class="audit-card mb-3 p-3">
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
                                    <span class="badge bg-{{ $methodColor }} bg-opacity-10 text-{{ $methodColor }} border border-{{ $methodColor }} border-opacity-10 py-1 px-2 x-small">
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
    .glass-card {
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 16px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
    }
    .audit-card {
        background: #ffffff;
        border: 1px solid #eef2f6;
        border-radius: 12px;
        transition: all 0.2s ease;
    }
    .audit-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 4px 12px rgba(0,0,0,0.03);
    }
    .audit-avatar {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        flex-shrink: 0;
    }
    .section-title-sm {
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        border-bottom: 2px solid #f1f5f9;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
    }
    .x-small { font-size: 0.75rem; }
    .text-silver { color: #cbd5e1; }
    
    /* Arabic Font Support */
    body { font-family: 'Inter', 'Noto Sans Arabic', sans-serif; }
</style>

@endsection
