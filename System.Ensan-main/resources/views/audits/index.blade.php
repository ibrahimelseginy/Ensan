@extends('layouts.app')
@section('content')

{{-- Premium Security Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0c4a6e 0%, #155e75 50%, #164e63 100%); border-bottom: 1px solid rgba(56,189,248,0.2);">
    <div class="hero-content">
        <div class="hero-greeting" style="color: #67e8f9;">الأمان والرقابة 🛡️</div>
        <h1 class="hero-title">مركز مراقبة النشاطات</h1>
        <p class="hero-subtitle">تتبع كامل لجميع العمليات التي تتم عبر النظام لضمان الامتثال والأمان</p>
        <div class="hero-actions d-flex gap-2">
            <a class="btn btn-sm rounded-pill px-4 shadow-sm"
                style="background: linear-gradient(135deg, #06b6d4, #0891b2); color: #fff; border: none;"
                href="{{ route('audits.index', array_merge(request()->query(), ['export' => 'csv'])) }}">
                <i class="bi bi-cloud-download me-1"></i> تحميل السجلات (CSV)
            </a>
            <a href="{{ route('dashboard.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light" style="border-color: rgba(255,255,255,0.3);">
                <i class="bi bi-arrow-right me-1"></i> الرجوع للرئيسية
            </a>
        </div>
    </div>
    <i class="bi bi-shield-check hero-icon d-none d-md-block" style="color: #22d3ee;"></i>
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
            <div class="glass-card mb-4 animate-slide-up animate-delay-1">
                <h6 class="fw-bold mb-3 section-title-sm">ملخص العمليات</h6>
                <div class="list-group list-group-flush bg-transparent audit-stats">
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => ''])) }}" 
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span style="color: #94a3b8;"><i class="bi bi-layers me-2" style="color: #22d3ee;"></i>الإجمالي</span>
                        <span class="fw-bold" style="color: #e2e8f0; font-size: 1.1rem;">{{ number_format($stats['total']) }}</span>
                    </a>
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'POST'])) }}"
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="small" style="color: #4ade80;"><i class="bi bi-plus-circle me-2"></i>إضافة (POST)</span>
                        <span class="badge rounded-pill" style="background: rgba(74,222,128,0.12); color: #4ade80;">{{ number_format($stats['POST']) }}</span>
                    </a>
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'PUT'])) }}"
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="small" style="color: #60a5fa;"><i class="bi bi-pencil me-2"></i>تعديل (PUT)</span>
                        <span class="badge rounded-pill" style="background: rgba(96,165,250,0.12); color: #60a5fa;">{{ number_format($stats['PUT'] + $stats['PATCH']) }}</span>
                    </a>
                    <a href="{{ route('audits.index', array_merge(request()->query(), ['method' => 'DELETE'])) }}"
                       class="list-group-item list-group-item-action bg-transparent border-0 px-0 d-flex justify-content-between align-items-center">
                        <span class="small" style="color: #f87171;"><i class="bi bi-trash me-2"></i>حذف (DELETE)</span>
                        <span class="badge rounded-pill" style="background: rgba(248,113,113,0.12); color: #f87171;">{{ number_format($stats['DELETE']) }}</span>
                    </a>
                </div>
            </div>

            <div class="glass-card animate-slide-up animate-delay-2">
                <h6 class="fw-bold mb-3 section-title-sm">أكثر المسارات نشاطاً</h6>
                <div class="small">
                    @forelse($topPaths as $tp)
                        <div class="top-path-item">
                            <div class="d-flex justify-content-between align-items-start mb-1 gap-2">
                                <a href="{{ url($tp->path) }}" target="_blank" class="fw-bold text-decoration-none d-block overflow-hidden" style="font-size: 11px;">
                                    <i class="bi bi-box-arrow-up-right me-1" style="color: #22d3ee;"></i>
                                    {{ $getLabel($tp->path) }} 
                                    <div class="fw-normal font-monospace x-small">/{{ ltrim($tp->path, '/') }}</div>
                                </a>
                                <span class="badge rounded-pill x-small" style="background: rgba(6,182,212,0.15); color: #22d3ee;">{{ $tp->c }}</span>
                            </div>
                            <div class="progress" style="height: 3px; background: rgba(255,255,255,0.06); border-radius: 4px;">
                                <div class="progress-bar" style="width: {{ ($tp->c / max($topPaths->pluck('c')->all())) * 100 }}%; background: linear-gradient(90deg, #06b6d4, #22d3ee); border-radius: 4px;"></div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-2">لا يوجد بيانات كافية</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Main Feed --}}
        <div class="col-xl-9">
            {{-- Search & Filters --}}
            {{-- Search & Filters --}}
            <div class="glass-card mb-4 animate-slide-up animate-delay-1 p-0 overflow-hidden" style="border: 1px solid rgba(6,182,212,0.1) !important;">
                <div class="p-3" style="border-bottom: 1px solid rgba(255,255,255,0.06); background: rgba(6,182,212,0.04);">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-funnel-fill" style="color: #22d3ee;"></i>
                        <span class="fw-bold small" style="color: #94a3b8;">تصفية السجلات والبحث</span>
                    </div>
                </div>
                <div class="p-4 audits-filter">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-5">
                            <label class="form-label x-small fw-bold mb-2">البحث في المسار (Path)</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text border-end-0"><i class="bi bi-search" style="color: #22d3ee;"></i></span>
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
                                <span class="input-group-text border-end-0"><i class="bi bi-calendar3" style="color: #22d3ee;"></i></span>
                                <input type="number" name="days" class="form-control border-start-0 py-2" value="{{ $days }}" placeholder="14">
                            </div>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label x-small fw-bold mb-2">رقم المستخدم</label>
                            <div class="input-group input-group-sm shadow-none">
                                <span class="input-group-text border-end-0"><i class="bi bi-person-badge" style="color: #22d3ee;"></i></span>
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
    /* ===== Audits Page - Dark Mode Optimized ===== */
    .glass-card {
        background: var(--glass-bg, rgba(255,255,255,0.04));
        border: 1px solid var(--border-color, rgba(255,255,255,0.08));
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        padding: 1.5rem;
    }

    .audit-card {
        background: var(--glass-bg, rgba(255,255,255,0.03));
        border: 1px solid var(--border-color, rgba(255,255,255,0.07));
        border-radius: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border-right: 3px solid transparent;
    }
    .audit-card:hover {
        border-color: rgba(6, 182, 212, 0.15);
        border-right-color: #06b6d4;
        box-shadow: 0 6px 24px rgba(6, 182, 212, 0.08);
        transform: translateX(-2px);
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

    .section-title-sm {
        font-size: 0.85rem;
        letter-spacing: 0.5px;
        color: #67e8f9;
        border-bottom: 2px solid rgba(6, 182, 212, 0.2);
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
        background: linear-gradient(180deg, #06b6d4, #0891b2);
        flex-shrink: 0;
    }

    .x-small { font-size: 0.75rem; }
    .text-silver { color: rgba(255,255,255,0.2); }

    /* --- Filter Form Inputs Dark Mode --- */
    .audits-filter .form-control,
    .audits-filter .form-select,
    .audits-filter .input-group-text {
        background: rgba(255,255,255,0.06) !important;
        border-color: rgba(255,255,255,0.1) !important;
        color: var(--text-primary, #e2e8f0) !important;
        transition: all 0.2s ease;
    }
    .audits-filter .form-control:focus,
    .audits-filter .form-select:focus {
        background: rgba(255,255,255,0.1) !important;
        border-color: rgba(6, 182, 212, 0.4) !important;
        box-shadow: 0 0 0 3px rgba(6, 182, 212, 0.1) !important;
    }
    .audits-filter .form-control::placeholder {
        color: rgba(255,255,255,0.3) !important;
    }
    .audits-filter .form-label {
        color: #94a3b8 !important;
    }

    /* --- Stats Sidebar --- */
    .audit-stats .list-group-item {
        color: var(--text-primary, #e2e8f0) !important;
        padding: 0.75rem 0 !important;
        border-bottom: 1px solid rgba(255,255,255,0.05) !important;
    }
    .audit-stats .list-group-item:last-child {
        border-bottom: none !important;
    }
    .audit-stats .list-group-item:hover {
        background: rgba(6, 182, 212, 0.06) !important;
        border-radius: 8px;
    }

    /* --- Top Paths --- */
    .top-path-item {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.06);
        border-radius: 10px;
        padding: 0.75rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s ease;
    }
    .top-path-item:hover {
        background: rgba(6, 182, 212, 0.06);
        border-color: rgba(6, 182, 212, 0.15);
    }
    .top-path-item a {
        color: var(--text-primary, #e2e8f0) !important;
    }
    .top-path-item .font-monospace {
        color: #94a3b8 !important;
    }

    /* --- Audit Card Content Colors --- */
    .audit-card h6 {
        color: var(--text-primary, #e2e8f0);
    }
    .audit-card .text-primary {
        color: #22d3ee !important;
    }
    .audit-card .text-muted {
        color: #94a3b8 !important;
    }
    .audit-card .font-monospace {
        color: #64748b !important;
    }

    /* --- Filter Button --- */
    .audit-filter-btn {
        background: linear-gradient(135deg, #06b6d4, #0891b2);
        color: #fff;
        border: none;
        border-radius: 10px;
        transition: all 0.3s ease;
    }
    .audit-filter-btn:hover {
        background: linear-gradient(135deg, #22d3ee, #06b6d4);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(6, 182, 212, 0.3);
        color: #fff;
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

@endsection


