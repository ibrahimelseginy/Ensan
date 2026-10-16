@extends('layouts.app')
@section('content')
<style>
    .guest-house-solid-card { background:#fff !important; backdrop-filter:none !important; }
    .theme-dark .guest-house-solid-card { background:#111827 !important; }

    /* Mobile Responsive Optimizations */
    @media (max-width: 768px) {
        .dashboard-hero {
            padding: 1.75rem 1.15rem !important;
            border-radius: 0 0 24px 24px !important;
            margin-bottom: 1.25rem !important;
        }
        .hero-title {
            font-size: 1.45rem !important;
            margin-bottom: 0.35rem !important;
        }
        .hero-subtitle {
            font-size: 0.85rem !important;
            margin-bottom: 1rem !important;
        }
        .hero-actions {
            flex-direction: column !important;
            gap: 0.5rem !important;
            width: 100% !important;
        }
        .hero-actions .btn {
            width: 100% !important;
            text-align: center !important;
            padding: 0.6rem 1.25rem !important;
            font-size: 0.9rem !important;
            display: inline-flex !important;
            align-items: center !important;
            justify-content: center !important;
        }
        .container-fluid.px-4 {
            padding-left: 0.5rem !important;
            padding-right: 0.5rem !important;
        }
        .gov-tabs-scroll {
            display: flex !important;
            flex-wrap: nowrap !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
            padding-bottom: 8px !important;
            scrollbar-width: none !important;
        }
        .gov-tabs-scroll::-webkit-scrollbar {
            display: none !important;
        }
        .gov-tabs-scroll .btn {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
            font-size: 0.82rem !important;
            padding: 0.4rem 0.85rem !important;
        }
        .guest-house-filter-box {
            padding: 1rem 1.15rem !important;
            border-radius: 18px !important;
            margin-bottom: 1.25rem !important;
        }
        .guest-house-card-mobile {
            border-radius: 18px !important;
        }
        .card-header-mobile {
            padding: 1rem 1.15rem !important;
        }
        .card-body-mobile {
            padding: 1.15rem !important;
        }
        .card-footer-mobile {
            padding-top: 0.85rem !important;
            flex-wrap: wrap !important;
            gap: 0.5rem !important;
        }
        .btn-details-mobile {
            flex: 1 !important;
            text-align: center !important;
        }
        .stat-card-mobile {
            padding: 0.85rem 1rem !important;
            border-radius: 16px !important;
        }
    }
</style>

<div class="guest-house-system-container animate-fade-in">
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up bg-primary shadow-sm mb-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 3rem 2rem; border-radius: 0 0 40px 40px; position: relative; overflow: hidden;">
        <div class="hero-content" style="position: relative; z-index: 2;">
            <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">الضيافة والإيواء 🏠</div>
            <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">إدارة دار الضيافة</h1>
            <p class="hero-subtitle text-white opacity-75 mb-4" style="color: #ffffff !important;">إدارة مرافق الإقامة والضيافة التابعة للمؤسسة لضمان أفضل خدمة للمستفيدين.</p>
            <div class="hero-actions d-flex gap-2">
                @php
                    $isManager = auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->is_admin);
                @endphp
                @if($isManager)
                    <a href="{{ route('guest-houses.create') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift shadow-sm" style="border-width: 2px;">
                        <i class="bi bi-plus-lg me-1"></i> إضافة دار جديدة
                    </a>
                @endif
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift shadow-sm" style="border-width: 2px;">
                    <i class="bi bi-arrow-right me-1"></i> العودة للرئيسية
                </a>
            </div>
        </div>
        <i class="bi bi-house-heart hero-icon text-white opacity-25 d-none d-md-block" style="font-size: 8rem; position: absolute; left: 5%; top: 50%; transform: translateY(-50%) rotate(-15deg); z-index: 1;"></i>
    </div>

    <div class="container-fluid px-4 pb-5">
        {{-- Stats Bar --}}
        <div class="row g-3 mb-4">
            <div class="col-12 col-md-4">
                <div class="glass-card guest-house-solid-card stat-card-mobile p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">إجمالي الدور</div>
                        <div class="h4 fw-bold mb-0">{{ $houses->total() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="glass-card guest-house-solid-card stat-card-mobile p-3 d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">الدور النشطة</div>
                        <div class="h4 fw-bold mb-0 text-success">{{ $houses->where('status', 'active')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-4">
                <div class="glass-card guest-house-solid-card stat-card-mobile p-3 d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; flex-shrink: 0;">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">إجمالي السعة الاستيعابية</div>
                        <div class="h4 fw-bold mb-0 text-info">{{ number_format($houses->sum('capacity')) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick Governorate Tabs (Horizontal scroll on mobile) --}}
        <div class="d-flex gap-2 mb-3 align-items-center gov-tabs-scroll">
            <span class="text-muted small fw-bold me-1 flex-shrink-0"><i class="bi bi-geo me-1 text-primary"></i> تصفية المحافظات:</span>
            <a href="{{ route('guest-houses.index', array_merge(request()->except('governorate'), ['governorate' => ''])) }}"
               class="btn btn-sm rounded-pill px-3 fw-bold {{ empty($governorate) ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="bi bi-buildings me-1"></i> كل الدور
            </a>
            <a href="{{ route('guest-houses.index', array_merge(request()->except('governorate'), ['governorate' => 'كفر الشيخ'])) }}"
               class="btn btn-sm rounded-pill px-3 fw-bold {{ ($governorate ?? '') === 'كفر الشيخ' ? 'btn-success' : 'btn-outline-secondary' }}">
                <i class="bi bi-geo-alt-fill me-1 text-success"></i> دور كفر الشيخ
            </a>
            <a href="{{ route('guest-houses.index', array_merge(request()->except('governorate'), ['governorate' => 'الغربية'])) }}"
               class="btn btn-sm rounded-pill px-3 fw-bold {{ ($governorate ?? '') === 'الغربية' ? 'btn-info text-white' : 'btn-outline-secondary' }}">
                <i class="bi bi-geo-alt-fill me-1 text-info"></i> دور الغربية
            </a>
        </div>

        {{-- Filter Bar --}}
        <div class="glass-container guest-house-solid-card guest-house-filter-box p-4 mb-4 shadow-sm">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-12 col-md-4">
                        <label class="form-label fw-bold text-muted small">بحث بالاسم أو الموقع</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0" placeholder="اسم الدار، المدينة، أو العنوان...">
                        </div>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-bold text-muted small">حالة الدار</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="active" @selected(($status ?? '') === 'active')>نشط</option>
                            <option value="archived" @selected(($status ?? '') === 'archived')>مؤرشف</option>
                        </select>
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label fw-bold text-muted small">المحافظة</label>
                        <select name="governorate" class="form-select">
                            <option value="">كل المحافظات</option>
                            <option value="كفر الشيخ" @selected(($governorate ?? '')==='كفر الشيخ')>كفر الشيخ</option>
                            <option value="الغربية" @selected(($governorate ?? '')==='الغربية')>الغربية</option>
                        </select>
                    </div>
                    <div class="col-12 col-md-3">
                        <button class="btn btn-primary w-100 fw-bold rounded-pill py-2">
                            <i class="bi bi-funnel me-1"></i> تطبيق التصفية
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Entity Grid --}}
        <div class="row g-3 g-md-4">
            @forelse($houses as $h)
            <div class="col-12 col-md-6 col-xl-4">
                <div class="glass-card guest-house-solid-card guest-house-card-mobile h-100 hover-lift border-0 shadow-sm p-0 overflow-hidden">
                    <div class="card-header-mobile p-4 d-flex align-items-center justify-content-between border-bottom gap-2">
                        <div class="d-flex align-items-center gap-3 min-w-0" style="min-width: 0;">
                            <div class="bg-primary text-white guest-house-avatar rounded-4 d-flex align-items-center justify-content-center fw-bold fs-4 shadow-sm" style="width: 50px; height: 50px; flex-shrink: 0;">
                                {{ mb_substr($h->name, 0, 1) }}
                            </div>
                            <div class="text-truncate" style="min-width: 0;">
                                <h6 class="fw-bold mb-0 text-main text-truncate">{{ $h->name }}</h6>
                                <div class="text-muted x-small text-truncate"><i class="bi bi-geo-alt me-1"></i>{{ $h->governorate ?: 'غير محدد' }}{{ $h->location ? ' · '.$h->location : '' }}</div>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 flex-shrink-0 {{ $h->status === 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} fw-bold x-small">
                            {{ $h->status === 'active' ? 'نشط' : 'مؤرشف' }}
                        </span>
                    </div>
                    
                    <div class="card-body-mobile p-4">
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-2 rounded-3" style="background: rgba(var(--bs-primary-rgb), 0.04);">
                                    <div class="x-small text-muted mb-1">الأجنحة والأسِرّة</div>
                                    <div class="fw-bold small"><i class="bi bi-hospital-bed text-primary me-1"></i>{{ $h->wings_count }} جناح · {{ $h->beds_count }} سرير</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3" style="background: rgba(var(--bs-primary-rgb), 0.04);">
                                    <div class="x-small text-muted mb-1">المقيمون حاليًا</div>
                                    <div class="fw-bold small"><i class="bi bi-people text-primary me-1"></i>{{ $h->resident_stays_count }} مقيم</div>
                                </div>
                            </div>
                            @if($h->manager)
                            <div class="col-12 mt-2">
                                <div class="small text-muted mb-1">مدير الدار: <span class="fw-bold text-dark">{{ $h->manager->name }}</span></div>
                            </div>
                            @endif

                            @if($h->wings && $h->wings->isNotEmpty())
                            <div class="col-12 pt-2 border-top mt-2">
                                <div class="x-small text-muted mb-1.5"><i class="bi bi-grid-3x3-gap text-primary me-1"></i> الأجنحة الداخلية:</div>
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($h->wings as $wing)
                                        <span class="badge bg-light text-dark border px-2 py-1 small fw-semibold" style="font-size: 0.75rem;">
                                            {{ $wing->name }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>

                        <div class="card-footer-mobile d-flex gap-2 justify-content-between align-items-center pt-3 border-top">
                            <div class="d-flex gap-2 flex-grow-1">
                                <a class="btn btn-sm btn-primary bg-opacity-10 text-primary border-0 rounded-pill px-3 py-2 fw-bold btn-details-mobile" href="{{ route('guest-houses.show', $h) }}">
                                    <i class="bi bi-eye me-1"></i> عرض التفاصيل
                                </a>
                                @if(!(isset($h->pendingRequest) && $h->pendingRequest))
                                <a class="btn btn-sm btn-light rounded-pill px-3 py-2 fw-bold" href="{{ route('guest-houses.edit', $h) }}">
                                    <i class="bi bi-pencil me-1"></i> تعديل
                                </a>
                                @endif
                            </div>

                            @if(isset($h->pendingRequest) && $h->pendingRequest)
                                <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-2 rounded-pill x-small fw-bold">
                                    <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                </span>
                            @else
                                <form method="POST" action="{{ route('guest-houses.destroy', $h) }}" 
                                      class="d-inline"
                                      data-confirm-delete
                                      data-confirm-title="تأكيد حذف دار الضيافة"
                                      data-name="{{ $h->name }}">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0 rounded-circle d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;" title="حذف دار الضيافة"><i class="bi bi-trash"></i></button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="glass-card text-center py-5">
                    <i class="bi bi-house-slash display-1 text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted">لا يوجد دور ضيافة حالياً</h5>
                    <p class="text-muted small">قم بإضافة دار جديدة للبدء في إدارة المرفق.</p>
                </div>
            </div>
            @endforelse
        </div>
        
        <div class="mt-5 d-flex justify-content-center">
            {{ $houses->links() }}
        </div>
    </div>
</div>
@endsection
