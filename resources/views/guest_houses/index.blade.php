@extends('layouts.app')
@section('content')
<style>
    .guest-house-solid-card { background:#fff !important; backdrop-filter:none !important; }
    .theme-dark .guest-house-solid-card { background:#111827 !important; }
</style>

<div class="guest-house-system-container animate-fade-in">
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up bg-primary shadow-sm mb-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 3rem 2rem; border-radius: 0 0 40px 40px;">
        <div class="hero-content">
            <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">الضيافة والإيواء 🏠</div>
            <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">إدارة دار الضيافة</h1>
            <p class="hero-subtitle text-white opacity-75 mb-4" style="color: #ffffff !important;">إدارة مرافق الإقامة والضيافة التابعة للمؤسسة لضمان أفضل خدمة للمستفيدين.</p>
            <div class="hero-actions d-flex gap-2">
                <a href="{{ route('guest-houses.create') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift shadow-sm" style="border-width: 2px;">
                    <i class="bi bi-plus-lg me-1"></i> إضافة دار جديدة
                </a>
                <a href="{{ route('dashboard.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift shadow-sm" style="border-width: 2px;">
                    <i class="bi bi-arrow-right me-1"></i> العودة للرئيسية
                </a>
            </div>
        </div>
        <i class="bi bi-house-heart hero-icon text-white opacity-25 d-none d-md-block" style="font-size: 8rem; position: absolute; left: 5%; top: 50%; transform: translateY(-50%) rotate(-15deg);"></i>
    </div>

    <div class="container-fluid px-4 pb-5">
        {{-- Stats Bar --}}
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <div class="glass-card guest-house-solid-card p-3 d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle">
                        <i class="bi bi-building fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">إجمالي الدور</div>
                        <div class="h4 fw-bold mb-0">{{ $houses->total() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card guest-house-solid-card p-3 d-flex align-items-center gap-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-circle">
                        <i class="bi bi-check-circle fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">الدور النشطة</div>
                        <div class="h4 fw-bold mb-0 text-success">{{ $houses->where('status', 'active')->count() }}</div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="glass-card guest-house-solid-card p-3 d-flex align-items-center gap-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-circle">
                        <i class="bi bi-people fs-4"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">إجمالي السعة الاستيعابية</div>
                        <div class="h4 fw-bold mb-0 text-info">{{ number_format($houses->sum('capacity')) }}</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="glass-container guest-house-solid-card p-4 mb-4">
            <form method="GET">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label fw-bold text-muted small">بحث بالاسم أو الموقع</label>
                        <div class="input-group">
                            <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                            <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0" placeholder="اسم الدار، المدينة، أو العنوان...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small">حالة الدار</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="active" @selected(($status ?? '') === 'active')>نشط</option>
                            <option value="archived" @selected(($status ?? '') === 'archived')>مؤرشف</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small">المحافظة</label>
                        <select name="governorate" class="form-select"><option value="">كل المحافظات</option><option value="كفر الشيخ" @selected(($governorate ?? '')==='كفر الشيخ')>كفر الشيخ</option><option value="الغربية" @selected(($governorate ?? '')==='الغربية')>الغربية</option></select>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn-primary w-100 fw-bold rounded-pill">
                            <i class="bi bi-funnel me-1"></i> تطبيق التصفية
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Entity Grid --}}
        <div class="row g-4">
            @forelse($houses as $h)
            <div class="col-md-6 col-xl-4">
                <div class="glass-card guest-house-solid-card h-100 hover-lift border-0 shadow-sm p-0 overflow-hidden">
                    <div class="p-4 d-flex align-items-center justify-content-between border-bottom">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-primary text-white rounded-4 d-flex align-items-center justify-content-center fw-bold fs-4 shadow-sm" style="width: 54px; height: 54px;">
                                {{ mb_substr($h->name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="fw-bold mb-0 text-main">{{ $h->name }}</h6>
                                <div class="text-muted x-small"><i class="bi bi-geo-alt me-1"></i>{{ $h->governorate ?: 'غير محدد' }}{{ $h->location ? ' · '.$h->location : '' }}</div>
                            </div>
                        </div>
                        <span class="badge rounded-pill px-3 py-2 {{ $h->status === 'active' ? 'bg-success bg-opacity-10 text-success' : 'bg-secondary bg-opacity-10 text-secondary' }} fw-bold x-small">
                            {{ $h->status === 'active' ? 'نشط' : 'مؤرشف' }}
                        </span>
                    </div>
                    
                    <div class="p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-6">
                                <div class="small text-muted mb-1">الأجنحة والأسِرّة</div>
                                <div class="fw-bold"><i class="bi bi-hospital-bed text-primary me-2"></i>{{ $h->wings_count }} جناح · {{ $h->beds_count }} سرير</div>
                            </div>
                            <div class="col-6">
                                <div class="small text-muted mb-1">المقيمون حاليًا</div>
                                <div class="fw-bold"><i class="bi bi-people text-primary me-2"></i>{{ $h->resident_stays_count }} مقيم</div>
                            </div>
                            @if($h->manager)
                            <div class="col-12">
                                <div class="small text-muted mb-1">مدير الدار</div>
                                <div class="fw-bold"><i class="bi bi-person-badge text-primary me-2"></i>{{ $h->manager->name }}</div>
                            </div>
                            @endif
                        </div>

                        <div class="d-flex gap-2 justify-content-between pt-3 border-top">
                            <div class="d-flex gap-2">
                                <a class="btn btn-sm btn-primary bg-opacity-10 text-primary border-0 rounded-pill px-3 fw-bold" href="{{ route('guest-houses.show', $h) }}">
                                    عرض التفاصيل
                                </a>
                                @if(!(isset($h->pendingRequest) && $h->pendingRequest))
                                <a class="btn btn-sm btn-light rounded-pill px-3 fw-bold" href="{{ route('guest-houses.edit', $h) }}">تعديل</a>
                                @endif
                            </div>

                            @if(isset($h->pendingRequest) && $h->pendingRequest)
                                <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-2 rounded-pill x-small fw-bold">
                                    <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                </span>
                            @else
                                <form method="POST" action="{{ route('guest-houses.destroy', $h) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذه الدار؟');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle"><i class="bi bi-trash"></i></button>
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

