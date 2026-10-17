@extends('layouts.app')

@section('title', 'الرئيسية — منظومة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
    {{-- Premium Dashboard Welcome Hero --}}
    @php
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'صباح الخير' : ($hour < 18 ? 'مساء الخير' : 'مساء الخير');
        $currentUser = auth()->user();
    @endphp
    <div class="dashboard-hero animate-slide-up mb-4">
        <div class="hero-content">
            <div class="hero-greeting">{{ $greeting }} 👋</div>
            <h1 class="hero-title">مرحباً بك {{ $currentUser->name ?? 'في منظومة إنسان' }}</h1>
            <p class="hero-subtitle">بوابة التطبيقات والخدمات المركزية — مؤسسة إنسان الخيرية ({{ date('Y-m-d') }})</p>
            <div class="hero-actions d-flex flex-wrap gap-2">
                @if($currentUser && $currentUser->hasPermission('dashboard.view'))
                <a href="{{ route('dashboard.analytics') }}" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold shadow-sm">
                    <i class="bi bi-speedometer2 me-1"></i> لوحة المؤشرات والتحليلات
                </a>
                @endif
                @if($currentUser)
                <a href="{{ route('users.show', $currentUser->id) }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-person-circle me-1"></i> ملفي الشخصي
                </a>
                @endif
                @if($currentUser && $currentUser->hasPermission('reports.view'))
                <a href="{{ route('reports.index') }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> التقارير
                </a>
                @endif
            </div>
        </div>
        <i class="bi bi-grid-3x3-gap-fill hero-icon d-none d-md-block opacity-25"></i>
    </div>

    {{-- Central App Launcher Hub (All System Sections as Interactive Icons) --}}
    @include('dashboard.partials.app_launcher')
</div>
@endsection
