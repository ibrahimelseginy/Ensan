@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-safe me-2"></i>الخزائن</h2>
            <p class="text-muted">إدارة جميع الخزائن المالية</p>
        </div>
        <div class="col-md-4 text-end d-flex gap-2 justify-content-end align-items-center">
            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                <a href="{{ route('change-requests.index') }}" class="btn btn-outline-info shadow-sm rounded-pill px-3">
                    <i class="bi bi-shield-check me-1"></i> طلبات المراجعة
                </a>
            @endif
            <a href="{{ route('treasuries.dashboard') }}" class="btn btn-info text-white shadow-sm rounded-pill px-3">
                <i class="bi bi-speedometer2 me-1"></i> لوحة التحكم
            </a>
            <a href="{{ route('treasuries.create') }}" class="btn btn-primary shadow-sm rounded-pill px-3">
                <i class="bi bi-plus-circle me-1"></i> إضافة خزينة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        @forelse($treasuries as $treasury)
        @php
            $pending = $pendingRequests->get($treasury->id)?->first();
        @endphp
        <div class="col-md-4 mb-4">
            <div class="glass-card shadow-sm border-0 h-100 animate-slide-up position-relative overflow-hidden" style="background: rgba(255,255,255,0.03); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.05) !important;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="icon-box bg-primary bg-opacity-10 text-primary p-3 rounded-4">
                                <i class="bi bi-safe2 fs-3"></i>
                            </div>
                            <div>
                                <h5 class="mb-1 text-white fw-bold">{{ $treasury->name }}</h5>
                                <span class="badge bg-white bg-opacity-10 text-white opacity-50">{{ $treasury->code }}</span>
                            </div>
                        </div>
                        <div>
                            @if($treasury->is_active)
                                <span class="badge rounded-pill px-3 bg-success bg-opacity-10 text-success border border-success border-opacity-25">نشط</span>
                            @else
                                <span class="badge rounded-pill px-3 bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25">غير نشط</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-4">
                        <div class="text-white opacity-50 small mb-1">الرصيد الحالي:</div>
                        <div class="fw-bold text-white fs-3 mb-3">
                            {{ number_format($treasury->current_balance, 2) }} <small class="fs-6 opacity-50">{{ $treasury->currency }}</small>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-white bg-opacity-5">
                                    <div class="text-white opacity-50 x-small mb-1">المدير:</div>
                                    <div class="text-white small fw-bold text-truncate">{{ $treasury->manager->name ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-2 rounded-3 bg-white bg-opacity-5">
                                    <div class="text-white opacity-50 x-small mb-1">الموقع:</div>
                                    <div class="text-white small fw-bold text-truncate">{{ $treasury->location ?? '-' }}</div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="p-2 rounded-3 bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                                    <div class="text-white opacity-50 x-small">عدد الحركات:</div>
                                    <div class="badge bg-white bg-opacity-10 text-white">{{ $treasury->transactions_count }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($pending)
                        <div class="alert alert-warning mb-3 p-3 border-0 bg-warning bg-opacity-10 text-warning rounded-4 shadow-sm animate-pulse">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-hourglass-split fs-5"></i>
                                <span class="fw-bold small">طلب قيد المراجعة</span>
                            </div>
                            <div class="d-flex gap-2 mt-2">
                                <form action="{{ route('change-requests.cancel', $pending) }}" method="POST" class="w-100" onsubmit="return confirm('هل تريد بالتأكيد إلغاء هذا الطلب؟')">
                                    @csrf
                                    <button class="btn btn-sm btn-danger w-100 rounded-pill">
                                        <i class="bi bi-x-circle me-1"></i> إلغاء الطلب
                                    </button>
                                </form>
                                @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                                    <a href="{{ route('change-requests.index') }}" class="btn btn-sm btn-warning w-100 rounded-pill">
                                        <i class="bi bi-shield-check me-1"></i> المراجعة
                                    </a>
                                @endif
                            </div>
                        </div>
                    @endif

                    <div class="d-grid gap-2">
                        <a href="{{ route('treasuries.show', $treasury) }}" class="btn btn-light rounded-pill py-2 shadow-sm hover-lift">
                            <i class="bi bi-eye me-1"></i> عرض التفاصيل الحسابات
                        </a>
                        
                        @if(!$pending)
                            <div class="d-flex gap-2">
                                <a href="{{ route('treasuries.edit', $treasury) }}" class="btn btn-outline-light rounded-pill py-2 flex-grow-1 hover-lift">
                                    <i class="bi bi-pencil me-1"></i> تعديل
                                </a>
                                <form action="{{ route('treasuries.destroy', $treasury) }}" method="POST" class="flex-grow-1" onsubmit="return confirm('هل تريد بالتأكيد حذف هذه الخزينة؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger rounded-pill py-2 w-100 hover-lift">
                                        <i class="bi bi-trash3"></i>
                                    </button>
                                </form>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="glass-card shadow-sm border-0">
                <div class="card-body text-center py-5">
                    <i class="bi bi-safe2 text-muted opacity-25" style="font-size: 5rem;"></i>
                    <h4 class="mt-4 mb-2 text-white opacity-75">لا توجد خزائن بعد</h4>
                    <p class="text-white opacity-50 mb-4">ابدأ بإضافة خزينة جديدة لإدارة الأموال</p>
                    <a href="{{ route('treasuries.create') }}" class="btn btn-primary btn-lg rounded-pill px-5 shadow-sm">
                        <i class="bi bi-plus-circle me-2"></i> إضافة خزينة جديدة
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection
