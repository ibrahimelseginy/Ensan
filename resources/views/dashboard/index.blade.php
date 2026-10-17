@extends('layouts.app')

@section('title', 'الرئيسية — منظومة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
    @php
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'صباح الخير' : 'مساء الخير';
        $currentUser = auth()->user();

        // Hijri Date
        try {
            $hijriFormatter = new \IntlDateFormatter('ar_SA@calendar=islamic-umalqura', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Africa/Cairo', \IntlDateFormatter::TRADITIONAL, 'd MMMM yyyy G');
            $hijriDate = $hijriFormatter->format(time());
        } catch (\Exception $e) {
            $hijriDate = '';
        }

        // Gregorian Date
        try {
            $gregFormatter = new \IntlDateFormatter('ar_EG', \IntlDateFormatter::FULL, \IntlDateFormatter::NONE, 'Africa/Cairo', \IntlDateFormatter::GREGORIAN, 'EEEE، d MMMM yyyy');
            $gregDate = $gregFormatter->format(time());
        } catch (\Exception $e) {
            $gregDate = date('Y-m-d');
        }

        $currentTime = date('h:i A');
    @endphp

    {{-- Unified Hero Banner & Command Hub (Merged Block) --}}
    <div class="dashboard-hero animate-slide-up mb-4 p-4 rounded-4 shadow-sm position-relative overflow-hidden">
        <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
            <div class="col-lg-7 col-md-12">
                <div class="hero-greeting d-inline-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-dark rounded-pill px-3 py-1 fw-bold shadow-xs">{{ $greeting }} 👋</span>
                    <span class="text-white fw-bold small opacity-90">مؤسسة إنسان الخيرية</span>
                </div>
                <h2 class="hero-title fw-black text-white mb-2">
                    مرحباً بك، {{ $currentUser->name ?? 'في منظومة إنسان' }}
                </h2>

                {{-- Live Hijri, Gregorian & Clock Badges --}}
                <div class="d-flex flex-wrap align-items-center gap-2 my-3">
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 border border-white border-opacity-10 shadow-xs" title="التاريخ الهجري">
                        <i class="bi bi-moon-stars-fill text-warning"></i>
                        <span id="liveHijriDate">{{ $hijriDate }}</span>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 border border-white border-opacity-10 shadow-xs" title="التاريخ الميلادي">
                        <i class="bi bi-calendar3 text-info"></i>
                        <span id="liveGregorianDate">{{ $gregDate }}</span>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 border border-white border-opacity-10 shadow-xs" title="الساعة الحالية لحظياً">
                        <i class="bi bi-clock-history text-success"></i>
                        <span id="liveClockTime" class="font-monospace fw-bold">{{ $currentTime }}</span>
                    </span>
                </div>

                {{-- Quick Navigation Actions --}}
                <div class="hero-actions d-flex flex-wrap gap-2 mt-2">
                    @if($currentUser && $currentUser->hasPermission('dashboard.view'))
                    <a href="{{ route('dashboard.analytics') }}" class="btn btn-sm btn-white text-primary rounded-pill px-3.5 py-1.5 fw-bold shadow-sm">
                        <i class="bi bi-speedometer2 me-1"></i> لوحة المؤشرات والتحليلات
                    </a>
                    @endif
                    @if($currentUser)
                    <a href="{{ route('users.show', $currentUser->id) }}" class="btn btn-sm btn-outline-light rounded-pill px-3.5 py-1.5 fw-semibold">
                        <i class="bi bi-person-circle me-1"></i> ملفي الشخصي
                    </a>
                    @endif
                    @if($currentUser && $currentUser->hasPermission('reports.view'))
                    <a href="{{ route('reports.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3.5 py-1.5 fw-semibold">
                        <i class="bi bi-file-earmark-bar-graph me-1"></i> التقارير
                    </a>
                    @endif
                </div>
            </div>

            {{-- Integrated Search Box on the Left/Hero --}}
            <div class="col-lg-5 col-md-12">
                <div class="p-3 p-md-4 rounded-4 border border-white border-opacity-15 shadow-sm" style="background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                    <label for="ensanAppSearch" class="text-white small fw-bold mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-search me-1"></i> بحث سريع في الأقسام والخدمات</span>
                        <kbd class="bg-white bg-opacity-20 text-white px-2 py-0.5 rounded small">اضغط / للبحث</kbd>
                    </label>
                    <div class="position-relative">
                        <input type="text" id="ensanAppSearch" class="form-control rounded-pill px-4 py-2.5 text-dark bg-white border-0 shadow-sm" 
                               placeholder="ابحث عن متبرعين، كفالات، تقارير، إعدادات..." autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <i class="bi bi-grid-3x3-gap-fill hero-icon d-none d-md-block opacity-25 position-absolute" style="left: -20px; bottom: -30px; font-size: 160px; pointer-events: none; z-index: 1;"></i>
    </div>

    {{-- Central App Launcher Hub (All System Sections as Interactive Icons) --}}
    @include('dashboard.partials.app_launcher', ['hideSearchHeader' => true])
</div>

{{-- Real-time Clock & Date Updater Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    function updateLiveClock() {
        const now = new Date();
        try {
            const timeFormatter = new Intl.DateTimeFormat('ar-EG', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            const timeEl = document.getElementById('liveClockTime');
            if (timeEl) timeEl.textContent = timeFormatter.format(now);
        } catch(e) {}
    }
    setInterval(updateLiveClock, 1000);
    updateLiveClock();
});
</script>
@endsection
