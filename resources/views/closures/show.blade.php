@extends('layouts.app')
@section('content')
@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #3730a3 100%);">
    <div class="hero-content">
        <div class="hero-greeting">تفاصيل الإغلاق 🔐</div>
        <h1 class="hero-title">إغلاق يوم {{ optional($closure->date)->format('Y-m-d') }}</h1>
        <p class="hero-subtitle">
            فرع: {{ $closure->branch ?? 'الرئيسي' }} | الحالة: 
            @if($closure->approved) 
                <span class="text-white fw-bold">معتمد ✅</span>
            @else 
                <span class="text-white-50 fw-bold">قيد المراجعة ⏳</span>
            @endif
        </p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('closures.edit', $closure) }}" class="btn btn-sm rounded-pill px-4 btn-light text-primary">
                <i class="bi bi-pencil me-1"></i> تعديل
            </a>
            <a href="{{ route('closures.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="hero-stat-item">
        <span class="d-block small opacity-75">الإجمالي النقدي</span>
        <span class="fw-bold fs-4">{{ number_format($closure->total_stats['total'], 2) }} <small class="fs-6 fw-normal">ج.م</small></span>
    </div>
</div>

<div class="row g-4 animate-slide-up animate-delay-1">
    {{-- Summary Panel --}}
    <div class="col-md-8">
        <div class="summary-panel h-100">
            <h5 class="fw-bold mb-4 section-title"><i class="bi bi-info-circle me-2"></i>بيانات الإغلاق</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <label class="d-block text-muted small fw-bold mb-2">التاريخ</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary-subtle rounded p-2 text-primary"><i class="bi bi-calendar-event"></i></div>
                            <div class="fw-bold text-dark fs-5">{{ optional($closure->date)->format('Y-m-d') }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3">
                        <label class="d-block text-muted small fw-bold mb-2">الفرع</label>
                        <div class="d-flex align-items-center gap-2">
                            <div class="bg-primary-subtle rounded p-2 text-primary"><i class="bi bi-geo-alt"></i></div>
                            <div class="fw-bold text-dark fs-5">{{ $closure->branch ?? 'الرئيسي' }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-4 rounded-3 text-white d-flex align-items-center justify-content-between" style="background: linear-gradient(135deg, #0d9488 0%, #115e59 100%);">
                        <div>
                            <div class="small text-white-50 fw-bold mb-1">الإجمالي الكلي</div>
                            <div class="display-6 fw-bold">
                                {{ number_format($closure->total_stats['total'], 2) }} <span class="fs-6 opacity-75">ج.م</span>
                            </div>
                        </div>
                        <div class="text-end">
                            <div class="badge bg-white text-dark shadow-sm mb-2 px-3 py-2 d-block text-start">
                                <span class="text-muted small">مكتب:</span> <span class="fw-bold">{{ number_format($closure->total_stats['office'], 2) }}</span>
                            </div>
                            <div class="badge bg-white text-dark shadow-sm px-3 py-2 d-block text-start">
                                <span class="text-muted small">مندوب:</span> <span class="fw-bold">{{ number_format($closure->total_stats['delegate'], 2) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Status Card --}}
    <div class="col-md-4">
        <div class="glass-card h-100 text-center p-4 d-flex flex-column justify-content-center">
            @if($closure->approved)
                <div class="mb-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-check-lg display-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark">تم الاعتماد</h4>
                <p class="text-muted">تم اعتماد هذا الإغلاق المالي بنجاح</p>
            @else
                <div class="mb-4">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-hourglass-split display-5"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark">قيد المراجعة</h4>
                <p class="text-muted">بانتظار المراجعة واعتماد المسؤول</p>
            @endif

            @if(request()->user() && (request()->user()->hasRole('admin') || request()->user()->hasRole('manager')))
                <div class="mt-4 w-100">
                    <form method="POST" action="{{ route('closures.approve', $closure) }}">
                        @csrf
                        <button class="btn btn-{{ $closure->approved ? 'outline-warning' : 'success' }} w-100 py-3 fw-bold rounded-pill shadow-sm transition-all hover-scale">
                            <i class="bi {{ $closure->approved ? 'bi-x-circle' : 'bi-check-circle' }} me-2"></i>
                            {{ $closure->approved ? 'إلغاء الاعتماد' : 'اعتماد الإغلاق' }}
                        </button>
                    </form>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
@endsection

