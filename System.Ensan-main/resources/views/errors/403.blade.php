@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center fade-in" style="min-height: 80vh;">
    <div class="card border-0 shadow-lg rounded-4 overflow-hidden" style="max-width: 600px; width: 100%;">
        <div class="card-body p-5 text-center position-relative">
            {{-- Background decorative elements --}}
            <div class="position-absolute top-0 start-50 translate-middle-x mt-n5 opacity-10">
                <i class="bi bi-shield-lock-fill text-danger" style="font-size: 10rem;"></i>
            </div>

            <div class="position-relative z-1">
                <div class="mb-4">
                    <div class="d-inline-flex align-items-center justify-content-center bg-danger-subtle text-danger rounded-circle p-4 mb-3" style="width: 100px; height: 100px;">
                        <i class="bi bi-shield-lock display-4"></i>
                    </div>
                </div>
                
                <h1 class="display-6 fw-bold text-dark mb-2">عذراً، الوصول مرفوض</h1>
                <p class="text-muted mb-4 fs-5">ليس لديك الصلاحيات الكافية لعرض هذه الصفحة.</p>

                @if($exception->getMessage())
                    <div class="alert-custom border border-danger-subtle text-danger rounded-3 p-3 mb-4 d-inline-block mx-auto">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        <span class="fw-bold">سبب المنع:</span> <span class="d-inline-block" dir="ltr">{{ $exception->getMessage() }}</span>
                    </div>
                @else
                    <div class="alert-custom border border-danger-subtle text-danger rounded-3 p-3 mb-4 d-inline-block mx-auto">
                        <i class="bi bi-exclamation-octagon me-2"></i>
                        غير مصرح لك بدخول هذه الصفحة
                    </div>
                @endif
                
                <div class="d-flex justify-content-center gap-3 mt-2">
                    <a href="{{ url('/') }}" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm hover-scale">
                        <i class="bi bi-house-door me-2"></i> الرئيسية
                    </a>
                    <button onclick="history.back()" class="btn btn-outline-secondary rounded-pill px-4 py-2 fw-bold hover-scale">
                        <i class="bi bi-arrow-right me-2"></i> رجوع
                    </button>
                </div>
            </div>
        </div>
        <div class="card-footer py-3 border-0 text-center text-muted small">
            Error 403 - Forbidden Access
        </div>
    </div>
</div>

<style>
    .hover-scale { transition: transform 0.2s; }
    .hover-scale:hover { transform: translateY(-2px); }
    
    /* Light Mode Default */
    .alert-custom { background-color: #fef2f2; }
    .card-footer { background-color: #f8f9fa; }
    
    /* Dark Mode Overrides */
    .theme-dark .card {
        background-color: #1e293b !important; /* Slate 800 */
        color: #f8fafc;
        border: 1px solid #334155;
    }
    .theme-dark .text-dark {
        color: #f1f5f9 !important;
    }
    .theme-dark .alert-custom {
        background-color: rgba(220, 38, 38, 0.1);
        color: #fca5a5 !important; /* Red 300 */
        border-color: rgba(220, 38, 38, 0.3) !important;
    }
    .theme-dark .bg-danger-subtle {
        background-color: rgba(220, 38, 38, 0.2) !important;
    }
    .theme-dark .text-danger {
        color: #fca5a5 !important;
    }
    .theme-dark .card-footer {
        background-color: #0f172a; /* Slate 900 */
        border-top: 1px solid #334155 !important;
    }
    .theme-dark .btn-outline-secondary {
        color: #e2e8f0;
        border-color: #64748b;
    }
    .theme-dark .btn-outline-secondary:hover {
        background-color: #334155;
        border-color: #94a3b8;
    }
</style>
@endsection
