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
            .hero-search-wrap {
                max-width: 440px;
                margin-right: auto;
            }
            @media (max-width: 991px) {
                .hero-search-wrap {
                    max-width: 100%;
                    margin-top: 0.75rem;
                }
            }

            .hero-clean-search,
            #ensanAppSearch.hero-clean-search,
            html.theme-dark #ensanAppSearch.hero-clean-search,
            html[data-bs-theme="dark"] #ensanAppSearch.hero-clean-search,
            body.theme-dark #ensanAppSearch.hero-clean-search,
            [data-bs-theme="dark"] #ensanAppSearch.hero-clean-search {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #1e293b !important;
                -webkit-text-fill-color: #1e293b !important;
                border: 1px solid #cbd5e1 !important;
                border-radius: 12px !important;
                padding: 0.75rem 2.85rem 0.75rem 1.25rem !important;
                height: 48px !important;
                font-size: 0.95rem !important;
                box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08) !important;
                transition: all 0.25s ease;
            }

            #ensanAppSearch.hero-clean-search::placeholder {
                color: #64748b !important;
                -webkit-text-fill-color: #64748b !important;
                opacity: 0.9 !important;
                font-size: 0.92rem !important;
            }

            #ensanAppSearch.hero-clean-search:focus {
                background: #ffffff !important;
                background-color: #ffffff !important;
                color: #0f172a !important;
                -webkit-text-fill-color: #0f172a !important;
                border-color: #10b981 !important;
                box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.25) !important;
                outline: none !important;
            }

            .hero-search-icon {
                position: absolute;
                right: 1.1rem;
                top: 50%;
                transform: translateY(-50%);
                color: #059669 !important;
                font-size: 1.15rem;
                pointer-events: none;
                z-index: 3;
            }

            /* Force all Hero texts and labels to be pure brilliant white in both light and dark mode */
            .dashboard-hero h2,
            .dashboard-hero .hero-title,
            .dashboard-hero .text-white,
            .dashboard-hero .text-white-50,
            .dashboard-hero label,
            .dashboard-hero label span,
            .dashboard-hero .badge:not(.bg-white),
            .dashboard-hero .badge:not(.bg-white) span,
            .dashboard-hero .hero-greeting > span:last-child,
            body:not(.theme-dark) .dashboard-hero h2,
            body:not(.theme-dark) .dashboard-hero .hero-title,
            body:not(.theme-dark) .dashboard-hero .text-white,
            body:not(.theme-dark) .dashboard-hero .text-white-50,
            body:not(.theme-dark) .dashboard-hero label,
            body:not(.theme-dark) .dashboard-hero label span,
            body:not(.theme-dark) .dashboard-hero .badge:not(.bg-white),
            body:not(.theme-dark) .dashboard-hero .badge:not(.bg-white) span,
            body:not(.theme-dark) .dashboard-hero .hero-greeting > span:last-child {
                color: #ffffff !important;
                -webkit-text-fill-color: #ffffff !important;
            }

            .dashboard-hero .badge.bg-dark {
                background: rgba(0, 0, 0, 0.35) !important;
                background-color: rgba(0, 0, 0, 0.35) !important;
                border: 1px solid rgba(255, 255, 255, 0.22) !important;
            }

            .dashboard-hero .badge.bg-white,
            body:not(.theme-dark) .dashboard-hero .badge.bg-white {
                background-color: #ffffff !important;
                background: #ffffff !important;
                color: #0f172a !important;
                -webkit-text-fill-color: #0f172a !important;
            }
        </style>

        <div class="row align-items-center g-3 position-relative" style="z-index: 2;">
            <div class="col-lg-7 col-md-12">
                <div class="hero-greeting d-inline-flex align-items-center gap-2 mb-2">
                    <span class="badge bg-white text-dark rounded-pill px-3.5 py-1.5 fw-bold shadow-xs" style="font-size: 0.95rem;">{{ $greeting }} 👋</span>
                    <span class="text-white fw-bold opacity-95" style="font-size: 1.05rem;">مؤسسة إنسان الخيرية</span>
                </div>
                <h2 class="hero-title fw-black text-white mb-2" style="font-size: 2rem; letter-spacing: -0.5px;">
                    مرحباً بك {{ $currentUser->name ?? 'في منظومة إنسان' }}
                </h2>

                {{-- Live Hijri, Gregorian & Clock Badges --}}
                <div class="d-flex flex-wrap align-items-center gap-2 mt-3 mb-1">
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 border border-white border-opacity-15 shadow-xs" style="font-size: 0.95rem;" title="التاريخ الهجري">
                        <i class="bi bi-moon-stars-fill text-warning fs-6"></i>
                        <span id="liveHijriDate" class="fw-bold">{{ $hijriDate }}</span>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 border border-white border-opacity-15 shadow-xs" style="font-size: 0.95rem;" title="التاريخ الميلادي">
                        <i class="bi bi-calendar3 text-info fs-6"></i>
                        <span id="liveGregorianDate" class="fw-bold">{{ $gregDate }}</span>
                    </span>
                    <span class="badge bg-dark bg-opacity-25 text-white rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-2 border border-white border-opacity-15 shadow-xs" style="font-size: 0.95rem;" title="الساعة الحالية لحظياً">
                        <i class="bi bi-clock-history text-success fs-6"></i>
                        <span id="liveClockTime" class="font-monospace fw-bold" dir="ltr">{{ $currentTime }}</span>
                    </span>
                    <a href="{{ route('data-import.index') }}" class="badge bg-white text-success rounded-pill px-3.5 py-2 d-inline-flex align-items-center gap-1.5 shadow-sm text-decoration-none hover-lift fw-bold" style="font-size: 0.95rem;" title="مركز استيراد وتوزيع ملفات الإكسيل">
                        <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-6"></i>
                        <span>استيراد الإكسيل</span>
                    </a>
                </div>
            </div>

            {{-- Clean Standalone Search Box Matching Navbar Style (Image 2) --}}
            <div class="col-lg-5 col-md-12">
                <div class="position-relative hero-search-wrap">
                    <input type="text" id="ensanAppSearch" class="form-control hero-clean-search" 
                           placeholder="ابحث عن تقارير، متبرعين، أو مهام..." autocomplete="off">
                    <i class="bi bi-search hero-search-icon"></i>
                </div>
            </div>
        </div>

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
