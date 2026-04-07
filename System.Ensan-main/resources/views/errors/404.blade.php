@extends('layouts.app')

@section('content')
<div class="container d-flex flex-column justify-content-center align-items-center" style="min-height: 70vh;">
    <div class="text-center">
        <div class="mb-4">
            <i class="bi bi-exclamation-triangle display-1 text-warning"></i>
        </div>
        <h1 class="display-1 fw-bold text-dark">404</h1>
        <h2 class="h3 mb-3">عذراً، الصفحة غير موجودة</h2>
        <p class="text-muted mb-4 lead">
            يبدو أن الصفحة التي تحاول الوصول إليها غير موجودة أو تم نقلها.
        </p>
        <div class="d-flex justify-content-center gap-3">
            <a href="{{ url('/') }}" class="btn btn-primary btn-lg px-4">
                <i class="bi bi-house-door me-2"></i> الصفحة الرئيسية
            </a>
            <button onclick="history.back()" class="btn btn-outline-secondary btn-lg px-4">
                <i class="bi bi-arrow-right me-2"></i> رجوع
            </button>
        </div>
    </div>
</div>
@endsection


