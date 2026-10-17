@extends('layouts.app')

@section('title', 'الرئيسية — منظومة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
    @php
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'صباح الخير' : 'مساء الخير';
        $currentUser = auth()->user();

        // Pure PHP Arabic Gregorian Date fallback (zero dependence on intl extension)
        $days = ['Sunday' => 'الأحد', 'Monday' => 'الإثنين', 'Tuesday' => 'الثلاثاء', 'Wednesday' => 'الأربعاء', 'Thursday' => 'الخميس', 'Friday' => 'الجمعة', 'Saturday' => 'السبت'];
        $months = [
            1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل', 5 => 'مايو', 6 => 'يونيو',
            7 => 'يوليو', 8 => 'أغسطس', 9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر'
        ];
        $dayName = $days[date('l')] ?? '';
        $dayNum = date('j');
        $monthName = $months[(int)date('n')] ?? '';
        $yearNum = date('Y');
        $gregDate = "{$dayName}، {$dayNum} {$monthName} {$yearNum}";

        // Hijri Date Pure PHP Converter (Kuwaiti algorithm - zero server extension requirements)
        $hDays = (int)date('d');
        $hMonth = (int)date('m');
        $hYear = (int)date('Y');
        if (($hYear > 1582) || (($hYear == 1582) && ($hMonth > 10)) || (($hYear == 1582) && ($hMonth == 10) && ($hDays > 14))) {
            $jd = (int)((1461 * ($hYear + 4800 + (int)(($hMonth - 14) / 12))) / 4) +
                  (int)((367 * ($hMonth - 2 - 12 * ((int)(($hMonth - 14) / 12)))) / 12) -
                  (int)((3 * (int)((($hYear + 4900 + (int)(($hMonth - 14) / 12)) / 100))) / 4) +
                  $hDays - 32075;
        } else {
            $jd = 367 * $hYear - (int)((7 * ($hYear + 5001 + (int)(($hMonth - 9) / 7))) / 4) +
                  (int)((275 * $hMonth) / 9) + $hDays + 1729777;
        }
        $l = $jd - 1948440 + 10632;
        $n = (int)(($l - 1) / 10631);
        $l = $l - 10631 * $n + 354;
        $j = ((int)((10985 - $l) / 5316)) * ((int)((50 * $l) / 17719)) +
             ((int)($l / 5670)) * ((int)((43 * $l) / 15238));
        $l = $l - ((int)((30 - $j) / 15)) * ((int)((17719 * $j) / 50)) -
             ((int)($j / 16)) * ((int)((15238 * $j) / 43)) + 29;
        $m_h = (int)((24 * $l) / 709);
        $d_h = $l - (int)((709 * $m_h) / 24);
        $y_h = 30 * $n + $j - 30;

        $hijriMonths = [
            1 => 'محرم', 2 => 'صفر', 3 => 'ربيع الأول', 4 => 'ربيع الآخر',
            5 => 'جمادى الأولى', 6 => 'جمادى الآخرة', 7 => 'رجب', 8 => 'شعبان',
            9 => 'رمضان', 10 => 'شوال', 11 => 'ذو القعدة', 12 => 'ذو الحجة'
        ];
        $hijriMonthName = $hijriMonths[$m_h] ?? '';
        $hijriDate = "{$d_h} {$hijriMonthName} {$y_h} هـ";

        $currentTime = date('h:i:s A');
    @endphp

    {{-- Unified Hero Banner & Command Hub (Merged Block) --}}
    <div class="dashboard-hero animate-slide-up mb-4 p-4 rounded-4 shadow-sm position-relative overflow-hidden">
        <style>
            #ensanAppSearch,
            html.theme-dark #ensanAppSearch,
            html[data-bs-theme="dark"] #ensanAppSearch,
            body.theme-dark #ensanAppSearch,
            [data-bs-theme="dark"] #ensanAppSearch {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                border: 0 !important;
                box-shadow: 0 4px 18px rgba(0, 0, 0, 0.15) !important;
            }
            #ensanAppSearch::placeholder {
                color: #64748b !important;
                opacity: 0.9 !important;
            }
            #ensanAppSearch:focus {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                box-shadow: 0 0 0 4px rgba(255, 255, 255, 0.45) !important;
                outline: none !important;
            }
        </style>

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
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mb-1">
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 border border-white border-opacity-10 shadow-xs" title="التاريخ الهجري">
                        <i class="bi bi-moon-stars-fill text-warning"></i>
                        <span id="liveHijriDate" class="fw-bold">{{ $hijriDate }}</span>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 border border-white border-opacity-10 shadow-xs" title="التاريخ الميلادي">
                        <i class="bi bi-calendar3 text-info"></i>
                        <span id="liveGregorianDate" class="fw-bold">{{ $gregDate }}</span>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3 py-2 fs-7 d-inline-flex align-items-center gap-2 border border-white border-opacity-10 shadow-xs" title="الساعة الحالية لحظياً">
                        <i class="bi bi-clock-history text-success"></i>
                        <span id="liveClockTime" class="font-monospace fw-bold" dir="ltr">{{ $currentTime }}</span>
                    </span>
                </div>
            </div>

            {{-- Integrated Search Box on the Left/Hero (Always Solid White in Dark and Light) --}}
            <div class="col-lg-5 col-md-12">
                <div class="p-3 p-md-4 rounded-4 border border-white border-opacity-15 shadow-sm" style="background: rgba(0, 0, 0, 0.2); backdrop-filter: blur(8px); -webkit-backdrop-filter: blur(8px);">
                    <label for="ensanAppSearch" class="text-white small fw-bold mb-2 d-flex align-items-center justify-content-between">
                        <span><i class="bi bi-search me-1"></i> بحث سريع في الأقسام والخدمات</span>
                        <kbd class="bg-white bg-opacity-20 text-white px-2 py-0.5 rounded small">اضغط / للبحث</kbd>
                    </label>
                    <div class="position-relative">
                        <input type="text" id="ensanAppSearch" class="form-control rounded-pill px-4 py-2.5 shadow-sm" 
                               style="background-color: #ffffff !important; background: #ffffff !important; color: #0f172a !important;"
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
            const timeFormatter = new Intl.DateTimeFormat('en-US', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: true
            });
            const timeEl = document.getElementById('liveClockTime');
            if (timeEl) timeEl.textContent = timeFormatter.format(now);
        } catch(e) {}
    }

    try {
        const hijriFormatter = new Intl.DateTimeFormat('ar-SA-u-ca-islamic-umalqura', {
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
        const hijriEl = document.getElementById('liveHijriDate');
        if (hijriEl) hijriEl.textContent = hijriFormatter.format(new Date());
    } catch(e) {}

    setInterval(updateLiveClock, 1000);
    updateLiveClock();
});
</script>
@endsection
