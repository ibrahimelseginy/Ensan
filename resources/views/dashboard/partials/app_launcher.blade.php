@php
    $u = auth()->user();
    if (!$u) {
        return;
    }
@endphp

<style>
/* ==============================================================
 * ENSAN APP LAUNCHER & HUB STYLES (100% Solid Non-Transparent)
 * ============================================================== */
.ensan-launcher-section {
    margin-bottom: 2.5rem;
}

.launcher-header-bar {
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px;
    padding: 1.25rem 1.5rem;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
    margin-bottom: 1.75rem;
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.theme-dark .launcher-header-bar,
html.theme-dark .launcher-header-bar,
html[data-bs-theme="dark"] .launcher-header-bar,
body.theme-dark .launcher-header-bar,
[data-bs-theme="dark"] .launcher-header-bar {
    background: linear-gradient(145deg, #131d31 0%, #0e1728 100%) !important;
    background-color: #0e1728 !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.45) !important;
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.theme-dark .launcher-header-bar h4,
html.theme-dark .launcher-header-bar h4,
[data-bs-theme="dark"] .launcher-header-bar h4 {
    color: #ffffff !important;
}

.theme-dark .launcher-header-bar p,
html.theme-dark .launcher-header-bar p,
[data-bs-theme="dark"] .launcher-header-bar p {
    color: #94a3b8 !important;
}

.launcher-search-box {
    position: relative;
    min-width: 260px;
    flex-grow: 1;
    max-width: 450px;
}

.launcher-search-input {
    width: 100%;
    padding: 0.75rem 2.75rem 0.75rem 1rem;
    border-radius: 999px;
    border: 1px solid #cbd5e1 !important;
    background: #f8fafc !important;
    color: var(--text-main);
    font-size: 0.95rem;
    font-weight: 500;
    transition: all 0.25s ease;
}

.theme-dark .launcher-search-input {
    background: #0f172a !important;
    border-color: #334155 !important;
    color: #f8fafc !important;
}

.launcher-search-input:focus {
    outline: none;
    border-color: #10b981 !important;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.2) !important;
}

.launcher-search-icon {
    position: absolute;
    right: 1.1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 1.1rem;
    pointer-events: none;
}

/* App Grid */
.ensan-apps-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(185px, 1fr));
    gap: 1.15rem;
}

@media (max-width: 576px) {
    .ensan-apps-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 0.85rem;
    }
}

@media (min-width: 1200px) {
    .ensan-apps-grid {
        grid-template-columns: repeat(auto-fill, minmax(195px, 1fr));
    }
}

/* App Card - 100% Solid Non-Transparent */
.ensan-app-card {
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px;
    padding: 1.35rem 0.95rem 1.2rem;
    text-align: center;
    text-decoration: none !important;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    position: relative;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    overflow: hidden;
    min-height: 162px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.05) !important;
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.theme-dark .ensan-app-card,
html.theme-dark .ensan-app-card,
html[data-bs-theme="dark"] .ensan-app-card,
body.theme-dark .ensan-app-card,
[data-bs-theme="dark"] .ensan-app-card {
    background: linear-gradient(145deg, #131d31 0%, #0b1220 100%) !important;
    background-color: #0b1220 !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3) !important;
    opacity: 1 !important;
}

.ensan-app-card:hover {
    transform: translateY(-5px);
    border-color: #10b981 !important;
    box-shadow: 0 14px 28px rgba(16, 185, 129, 0.2) !important;
}

.theme-dark .ensan-app-card:hover,
html.theme-dark .ensan-app-card:hover,
html[data-bs-theme="dark"] .ensan-app-card:hover,
body.theme-dark .ensan-app-card:hover,
[data-bs-theme="dark"] .ensan-app-card:hover {
    background: linear-gradient(145deg, #18253d 0%, #0e1728 100%) !important;
    background-color: #0e1728 !important;
    border-color: #10b981 !important;
    box-shadow: 0 18px 36px rgba(16, 185, 129, 0.28) !important;
}

.ensan-app-card:active {
    transform: scale(0.97);
}

/* Icon Container */
.ensan-app-icon {
    width: 62px;
    height: 62px;
    border-radius: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    color: #ffffff;
    margin-bottom: 0.85rem;
    transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
    flex-shrink: 0;
    opacity: 1 !important;
}

.ensan-app-card:hover .ensan-app-icon {
    transform: scale(1.1) rotate(2deg);
}

/* Typography */
.ensan-app-title {
    font-size: 1.02rem;
    font-weight: 800;
    color: var(--text-main);
    margin-bottom: 0.4rem;
    line-height: 1.35;
    display: -webkit-box;
    -webkit-line-clamp: 1;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.ensan-app-badge {
    font-size: 0.78rem;
    font-weight: 700;
    padding: 0.25rem 0.75rem;
    border-radius: 999px;
    display: inline-flex;
    align-items: center;
    gap: 5px;
    transition: all 0.2s ease;
    opacity: 1 !important;
}

.badge-sub {
    background: rgba(16, 185, 129, 0.15) !important;
    color: #10b981 !important;
}

.theme-dark .badge-sub {
    background: rgba(16, 185, 129, 0.25) !important;
    color: #34d399 !important;
}

.badge-direct {
    background: rgba(148, 163, 184, 0.2) !important;
    color: var(--text-muted) !important;
}

.ensan-app-card:hover .badge-sub {
    background: #10b981 !important;
    color: #ffffff !important;
}

/* Hide scrollbars completely on modals and their children */
.modal::-webkit-scrollbar,
.modal *::-webkit-scrollbar,
.ensan-sub-modal::-webkit-scrollbar,
.ensan-sub-modal *::-webkit-scrollbar {
    display: none !important;
    width: 0 !important;
    height: 0 !important;
}

.modal,
.modal *,
.ensan-sub-modal,
.ensan-sub-modal * {
    -ms-overflow-style: none !important;
    scrollbar-width: none !important;
}

/* ==============================================================
 * ENSAN SUB-MODALS & SUB-TILES: 100% SOLID BLACK (معتمة تماماً وغير شفافة)
 * ============================================================== */

/* Universal Solid Black & Opaque Modal Box */
.ensan-sub-modal,
.ensan-sub-modal .modal-dialog,
.ensan-sub-modal .modal-content,
.ensan-sub-modal.modal .modal-content,
.ensan-sub-modal .modal-header,
.ensan-sub-modal .modal-body,
.ensan-sub-tile {
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
}

.ensan-sub-modal .modal-content,
.ensan-sub-modal.modal .modal-content,
.ensan-sub-modal .modal-dialog .modal-content,
html .ensan-sub-modal .modal-content,
body .ensan-sub-modal .modal-content,
html.theme-dark .ensan-sub-modal .modal-content,
body.theme-dark .ensan-sub-modal .modal-content,
[data-bs-theme="dark"] .ensan-sub-modal .modal-content {
    border-radius: 24px !important;
    border: 1px solid #cbd5e1 !important;
    background-color: #ffffff !important;
    background: #ffffff !important;
    box-shadow: 0 35px 95px rgba(0, 0, 0, 0.55), 0 0 0 1px rgba(0, 0, 0, 0.08) !important;
    overflow: hidden !important;
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    color: #0f172a !important;
}

.ensan-sub-modal .modal-header,
html .ensan-sub-modal .modal-header,
body .ensan-sub-modal .modal-header,
html.theme-dark .ensan-sub-modal .modal-header,
body.theme-dark .ensan-sub-modal .modal-header,
[data-bs-theme="dark"] .ensan-sub-modal .modal-header {
    background: linear-gradient(135deg, #059669 0%, #10b981 100%) !important;
    background-color: #059669 !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15) !important;
    padding: 1.25rem 1.5rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    color: #ffffff !important;
}

.ensan-sub-modal .modal-header .modal-title,
.ensan-sub-modal .modal-header h5,
html .ensan-sub-modal .modal-header .modal-title,
body .ensan-sub-modal .modal-header .modal-title,
html.theme-dark .ensan-sub-modal .modal-header .modal-title,
body.theme-dark .ensan-sub-modal .modal-header .modal-title,
[data-bs-theme="dark"] .ensan-sub-modal .modal-header .modal-title {
    color: #ffffff !important;
    font-weight: 800 !important;
    font-size: 1.15rem !important;
}

.ensan-sub-modal .modal-header .text-muted,
.ensan-sub-modal .modal-header p,
html .ensan-sub-modal .modal-header .text-muted,
body .ensan-sub-modal .modal-header .text-muted,
html.theme-dark .ensan-sub-modal .modal-header .text-muted,
body.theme-dark .ensan-sub-modal .modal-header .text-muted,
[data-bs-theme="dark"] .ensan-sub-modal .modal-header .text-muted {
    color: rgba(255, 255, 255, 0.9) !important;
    font-weight: 500 !important;
}

.ensan-sub-modal .btn-close,
html .ensan-sub-modal .btn-close,
body .ensan-sub-modal .btn-close,
html.theme-dark .ensan-sub-modal .btn-close,
body.theme-dark .ensan-sub-modal .btn-close,
[data-bs-theme="dark"] .ensan-sub-modal .btn-close {
    filter: brightness(0) invert(1) !important;
    opacity: 0.9 !important;
}

.ensan-sub-modal .btn-close:hover {
    opacity: 1 !important;
}

.ensan-sub-modal .modal-body,
html .ensan-sub-modal .modal-body,
body .ensan-sub-modal .modal-body,
html.theme-dark .ensan-sub-modal .modal-body,
body.theme-dark .ensan-sub-modal .modal-body,
[data-bs-theme="dark"] .ensan-sub-modal .modal-body {
    background-color: #f8fafc !important;
    background: #f8fafc !important;
    opacity: 1 !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    padding: 0.5rem 0.25rem 1.25rem !important;
}

.ensan-sub-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
    padding: 1.25rem;
}

@media (max-width: 576px) {
    .ensan-sub-grid {
        grid-template-columns: 1fr;
    }
}

/* Sub-Service Tiles - Solid White Background with High-Contrast Dark Text */
.ensan-sub-tile,
html .ensan-sub-tile,
body .ensan-sub-tile,
html.theme-dark .ensan-sub-tile,
body.theme-dark .ensan-sub-tile,
[data-bs-theme="dark"] .ensan-sub-tile {
    display: flex !important;
    align-items: center !important;
    gap: 0.9rem !important;
    padding: 1rem 1.2rem !important;
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    border-radius: 16px !important;
    text-decoration: none !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1) !important;
    cursor: pointer !important;
    opacity: 1 !important;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05) !important;
    backdrop-filter: none !important;
    -webkit-backdrop-filter: none !important;
    color: #0f172a !important;
}

.ensan-sub-tile:hover,
html .ensan-sub-tile:hover,
body .ensan-sub-tile:hover,
html.theme-dark .ensan-sub-tile:hover,
body.theme-dark .ensan-sub-tile:hover,
[data-bs-theme="dark"] .ensan-sub-tile:hover {
    transform: translateY(-3px) !important;
    background: #ffffff !important;
    background-color: #ffffff !important;
    border-color: #10b981 !important;
    box-shadow: 0 10px 28px rgba(16, 185, 129, 0.22) !important;
}

.ensan-sub-tile-title,
.ensan-sub-tile .ensan-sub-tile-title,
html .ensan-sub-tile-title,
body .ensan-sub-tile-title,
html.theme-dark .ensan-sub-tile-title,
body.theme-dark .ensan-sub-tile-title,
[data-bs-theme="dark"] .ensan-sub-tile-title {
    color: #0f172a !important;
    font-weight: 800 !important;
    font-size: 0.95rem !important;
    margin-bottom: 2px !important;
    line-height: 1.35 !important;
}

.ensan-sub-tile small,
.ensan-sub-tile .text-muted,
html .ensan-sub-tile small,
body .ensan-sub-tile small,
html.theme-dark .ensan-sub-tile small,
body.theme-dark .ensan-sub-tile small,
[data-bs-theme="dark"] .ensan-sub-tile small {
    color: #475569 !important;
    font-weight: 600 !important;
    font-size: 0.82rem !important;
    line-height: 1.35 !important;
}

.ensan-sub-tile-arrow,
html .ensan-sub-tile-arrow,
body .ensan-sub-tile-arrow,
html.theme-dark .ensan-sub-tile-arrow,
body.theme-dark .ensan-sub-tile-arrow,
[data-bs-theme="dark"] .ensan-sub-tile-arrow {
    color: #64748b !important;
    font-size: 0.95rem !important;
    transition: transform 0.2s ease, color 0.2s ease !important;
}

.ensan-sub-tile:hover .ensan-sub-tile-arrow,
html .ensan-sub-tile:hover .ensan-sub-tile-arrow,
body .ensan-sub-tile:hover .ensan-sub-tile-arrow,
html.theme-dark .ensan-sub-tile:hover .ensan-sub-tile-arrow,
body.theme-dark .ensan-sub-tile:hover .ensan-sub-tile-arrow,
[data-bs-theme="dark"] .ensan-sub-tile:hover .ensan-sub-tile-arrow {
    color: #10b981 !important;
    transform: translateX(-4px) !important;
}

.ensan-sub-tile-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
    opacity: 1 !important;
}

/* Ensure pastel icon badges look identical in light and dark mode */
.ensan-sub-modal .bg-warning-subtle,
html.theme-dark .ensan-sub-modal .bg-warning-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-warning-subtle {
    background-color: #fef3c7 !important;
    color: #d97706 !important;
}
.ensan-sub-modal .bg-success-subtle,
html.theme-dark .ensan-sub-modal .bg-success-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-success-subtle {
    background-color: #dcfce7 !important;
    color: #16a34a !important;
}
.ensan-sub-modal .bg-info-subtle,
html.theme-dark .ensan-sub-modal .bg-info-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-info-subtle {
    background-color: #e0f2fe !important;
    color: #0284c7 !important;
}
.ensan-sub-modal .bg-danger-subtle,
html.theme-dark .ensan-sub-modal .bg-danger-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-danger-subtle {
    background-color: #fee2e2 !important;
    color: #dc2626 !important;
}
.ensan-sub-modal .bg-primary-subtle,
html.theme-dark .ensan-sub-modal .bg-primary-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-primary-subtle {
    background-color: #e0e7ff !important;
    color: #4f46e5 !important;
}
.ensan-sub-modal .bg-secondary-subtle,
html.theme-dark .ensan-sub-modal .bg-secondary-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-secondary-subtle {
    background-color: #f1f5f9 !important;
    color: #475569 !important;
}
.ensan-sub-modal .bg-dark-subtle,
html.theme-dark .ensan-sub-modal .bg-dark-subtle,
[data-bs-theme="dark"] .ensan-sub-modal .bg-dark-subtle {
    background-color: #e2e8f0 !important;
    color: #1e293b !important;
}

.ensan-sub-tile-content {
    flex-grow: 1;
}
/* Category Filter Pills */
.launcher-categories-bar {
    display: flex;
    align-items: center;
    gap: 0.6rem;
    overflow-x: auto;
    padding-bottom: 0.6rem;
    scrollbar-width: none;
}
.launcher-categories-bar::-webkit-scrollbar { display: none; }
.launcher-cat-btn {
    background: #ffffff !important;
    background-color: #ffffff !important;
    border: 1px solid #cbd5e1 !important;
    color: var(--text-main);
    padding: 0.55rem 1.25rem;
    border-radius: 999px;
    font-size: 0.9rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    white-space: nowrap;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    opacity: 1 !important;
}
.launcher-cat-btn:hover {
    background: #f1f5f9 !important;
    border-color: #94a3b8 !important;
    transform: translateY(-2px);
}
.launcher-cat-btn.active {
    background: #10b981 !important;
    background-color: #10b981 !important;
    color: #ffffff !important;
    border-color: #10b981 !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
}
.theme-dark .launcher-cat-btn,
html.theme-dark .launcher-cat-btn,
html[data-bs-theme="dark"] .launcher-cat-btn,
body.theme-dark .launcher-cat-btn,
[data-bs-theme="dark"] .launcher-cat-btn {
    background: #182337 !important;
    background-color: #182337 !important;
    border-color: rgba(255, 255, 255, 0.1) !important;
    color: #e2e8f0 !important;
    opacity: 1 !important;
}
.theme-dark .launcher-cat-btn:hover,
html.theme-dark .launcher-cat-btn:hover,
html[data-bs-theme="dark"] .launcher-cat-btn:hover,
body.theme-dark .launcher-cat-btn:hover,
[data-bs-theme="dark"] .launcher-cat-btn:hover {
    background: #24354f !important;
    color: #ffffff !important;
    border-color: rgba(255, 255, 255, 0.2) !important;
}
.theme-dark .launcher-cat-btn.active,
html.theme-dark .launcher-cat-btn.active,
html[data-bs-theme="dark"] .launcher-cat-btn.active,
body.theme-dark .launcher-cat-btn.active,
[data-bs-theme="dark"] .launcher-cat-btn.active {
    background: #10b981 !important;
    background-color: #10b981 !important;
    color: #ffffff !important;
    border-color: #10b981 !important;
    box-shadow: 0 4px 15px rgba(16, 185, 129, 0.4) !important;
}

</style>

<div class="ensan-launcher-section animate-slide-up">
    {{-- Header Bar with Search & Counter --}}
    @if(!isset($hideSearchHeader) || !$hideSearchHeader)
    <div class="launcher-header-bar">
        <div class="d-flex align-items-center gap-3">
            <div class="p-2 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 46px; height: 46px;">
                <i class="bi bi-grid-3x3-gap-fill fs-4"></i>
            </div>
            <div>
                <h4 class="mb-0 fw-black text-main">أقسام وتطبيقات المنظومة</h4>
                <p class="text-muted small mb-0">اختر أي قسم للدخول المباشر أو استعراض خدماته الفرعية</p>
            </div>
        </div>

        <div class="launcher-search-box">
            <i class="bi bi-search launcher-search-icon"></i>
            <input type="text" id="ensanAppSearch" class="launcher-search-input" placeholder="ابحث في الأقسام والخدمات... (أو اضغط / للبحث السريع)" autocomplete="off">
        </div>
    </div>
    @endif

    {{-- Categories / Domain Tabs --}}
    <div class="launcher-categories-bar mb-4">
        <button type="button" class="launcher-cat-btn active" data-category="all">
            <i class="bi bi-grid-fill"></i> جميع الأقسام
        </button>
        <button type="button" class="launcher-cat-btn" data-category="finance">
            <i class="bi bi-cash-stack"></i> التبرعات والمالية
        </button>
        <button type="button" class="launcher-cat-btn" data-category="operations">
            <i class="bi bi-truck"></i> العمليات واللوجيستك
        </button>
        <button type="button" class="launcher-cat-btn" data-category="hr">
            <i class="bi bi-people-fill"></i> الموارد البشرية
        </button>
        <button type="button" class="launcher-cat-btn" data-category="digital">
            <i class="bi bi-globe2"></i> الرقمية والموقع
        </button>
        <button type="button" class="launcher-cat-btn" data-category="admin">
            <i class="bi bi-shield-check"></i> الإدارة والنظام
        </button>
    </div>

    {{-- Main App Cards Grid --}}
    <div class="ensan-apps-grid" id="ensanAppsGrid">

        {{-- 0. المؤشرات والتحليلات (Dashboard Analytics & KPIs) --}}
        @if($u && $u->hasPermission('dashboard.view'))
        <a href="{{ route('dashboard.analytics') }}" class="ensan-app-card" data-category="finance digital" data-title="المؤشرات والتحليلات" data-keywords="مؤشرات إحصائيات مستهدف مالي تحليلات رسوم بيانية تقارير analytics dashboard">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #0ea5e9, #2563eb); box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.45);">
                <i class="bi bi-speedometer2"></i>
            </div>
            <div class="ensan-app-title">المؤشرات والتحليلات</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-graph-up-arrow"></i> المستهدف والرسوم</span>
        </a>
        @endif

        {{-- 1. المتبرعون --}}
        @if($u && !$u->roles->contains('key', 'finance') && $u->hasPermission('donors.view'))
        <a href="{{ route('donors.index') }}" class="ensan-app-card" data-category="finance" data-title="المتبرعون" data-keywords="متبرع تبرع donors كفلاء">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #2563eb, #3b82f6); box-shadow: 0 10px 20px -5px rgba(37, 99, 235, 0.45);">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="ensan-app-title">المتبرعون</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 2. التبرعات --}}
        @if($u && !$u->roles->contains('key', 'finance') && $u->hasPermission('donations.view'))
        <a href="{{ route('donations.index') }}" class="ensan-app-card" data-category="finance" data-title="التبرعات" data-keywords="تبرعات إيصالات كاش عيني donations">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #059669, #10b981); box-shadow: 0 10px 20px -5px rgba(16, 185, 129, 0.45);">
                <i class="bi bi-gift-fill"></i>
            </div>
            <div class="ensan-app-title">التبرعات</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 3. المستفيدون --}}
        @if($u && !$u->roles->contains('key', 'finance') && $u->hasPermission('beneficiaries.view'))
        <a href="{{ route('beneficiaries.index') }}" class="ensan-app-card" data-category="operations finance" data-title="المستفيدون" data-keywords="مستفيد حالات أسر أيتام beneficiaries">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #e11d48, #f43f5e); box-shadow: 0 10px 20px -5px rgba(225, 29, 72, 0.45);">
                <i class="bi bi-person-heart"></i>
            </div>
            <div class="ensan-app-title">المستفيدون</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 3.5 التقديمات (Applications & Preliminary Research) --}}
        @if($u && ($u->hasPermission('mobile.case_applications.view') || $u->hasPermission('beneficiaries.view') || $u->hasRole('admin') || $u->hasRole('super_admin')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppSubmissions" data-category="operations digital" data-title="التقديمات" data-keywords="تقديمات طلبات حالات بحث مبدئي زاد مدرار أورام استمارات submissions applications">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #0284c7, #06b6d4); box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.45);">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="ensan-app-title">التقديمات</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 5 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 4. اللوجيستك (Popup) --}}
        @if($u && ($u->hasPermission('manage_logistics') || $u->hasPermission('delegates.view') || $u->hasPermission('travel_routes.view') || $u->hasPermission('trips.view') || $u->hasPermission('kafr_el_sheikh_deliveries.view')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppLogistics" data-category="operations" data-title="اللوجيستك" data-keywords="مناديب خطوط سير رحلات كفر الشيخ طنطا logistics">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #ea580c, #f97316); box-shadow: 0 10px 20px -5px rgba(234, 88, 12, 0.45);">
                <i class="bi bi-signpost-2-fill"></i>
            </div>
            <div class="ensan-app-title">اللوجيستك</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 7 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 5. الموارد البشرية HR (Popup) --}}
        @if($u && ($u->hasRole('hr') || $u->hasPermission('volunteers.view') || $u->hasPermission('users.view') || $u->hasPermission('hr.evaluations') || $u->hasPermission('payrolls.view') || $u->hasPermission('tasks.view')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppHR" data-category="hr" data-title="الموارد البشرية HR" data-keywords="موظفين متطوعين حضور انصراف رواتب مهام تقييمات hr">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6); box-shadow: 0 10px 20px -5px rgba(124, 58, 237, 0.45);">
                <i class="bi bi-person-lines-fill"></i>
            </div>
            <div class="ensan-app-title">الموارد البشرية HR</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 10 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 6. الحسابات والمالية (Popup) --}}
        @if($u && ($u->hasPermission('treasuries.view') || $u->hasPermission('closures.view') || $u->hasPermission('accounts.view') || $u->hasPermission('expenses.view') || $u->roles->contains('key', 'finance')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppFinance" data-category="finance" data-title="الحسابات والمالية" data-keywords="خزائن مصروفات إيرادات قيود إغلاق يوميات شجرة حسابات finance">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #0d9488, #14b8a6); box-shadow: 0 10px 20px -5px rgba(13, 148, 136, 0.45);">
                <i class="bi bi-calculator-fill"></i>
            </div>
            <div class="ensan-app-title">الحسابات والمالية</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 7 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 7. إدارة المخازن (Popup) --}}
        @if($u && ($u->hasPermission('inventory.view') || $u->hasPermission('items.view') || $u->hasPermission('suppliers.view') || $u->hasPermission('inventory_transactions.view')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppInventory" data-category="operations" data-title="إدارة المخازن" data-keywords="مستودعات أصناف موردين حركات مخزون جرد inventory">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #d97706, #f59e0b); box-shadow: 0 10px 20px -5px rgba(217, 119, 6, 0.45);">
                <i class="bi bi-boxes"></i>
            </div>
            <div class="ensan-app-title">إدارة المخازن</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 4 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 8. إدارة المشاريع --}}
        @if($u && !$u->hasRole('hr') && !$u->hasRole('marketer') && ($u->hasPermission('projects.view') || $u->hasPermission('manage_project')))
        <a href="{{ route('projects.index') }}" class="ensan-app-card" data-category="operations" data-title="إدارة المشاريع" data-keywords="مشاريع إعمار آبار تنموية projects">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #4f46e5, #6366f1); box-shadow: 0 10px 20px -5px rgba(79, 70, 229, 0.45);">
                <i class="bi bi-kanban-fill"></i>
            </div>
            <div class="ensan-app-title">إدارة المشاريع</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 9. حملات موسمية (Popup) --}}
        @if($u && ($u->hasPermission('campaigns.view') || $u->hasPermission('ramadan_bags.view') || $u->hasPermission('ramadan_iftars.view')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppCampaigns" data-category="operations finance" data-title="حملات موسمية" data-keywords="حملات رمضان شنط إفطار أضاحي شتاء campaigns">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #dc2626, #ef4444); box-shadow: 0 10px 20px -5px rgba(220, 38, 38, 0.45);">
                <i class="bi bi-megaphone-fill"></i>
            </div>
            <div class="ensan-app-title">حملات موسمية</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 3 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 10. الشراكات والعضويات (Popup) --}}
        @if($u && ($u->hasPermission('manage_specialized_services') || $u->hasPermission('school_collaborations.view') || $u->hasPermission('memberships.view') || $u->hasPermission('oncology_medicine_reps.view')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppCollaborations" data-category="hr" data-title="الشراكات والعضوية" data-keywords="مدارس جامعات عضويات اشتراكات أدوية أورام partnerships">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #0891b2, #06b6d4); box-shadow: 0 10px 20px -5px rgba(8, 145, 178, 0.45);">
                <i class="bi bi-diagram-3-fill"></i>
            </div>
            <div class="ensan-app-title">الشراكات والعضوية</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 3 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 11. الاستقبال وخدمة العملاء --}}
        @if($u && $u->hasPermission('reception.view'))
        <a href="{{ route('reception.index') }}" class="ensan-app-card" data-category="operations" data-title="الاستقبال وخدمة العملاء" data-keywords="استقبال خدمة عملاء زوار استفسارات reception">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #0284c7, #38bdf8); box-shadow: 0 10px 20px -5px rgba(2, 132, 199, 0.45);">
                <i class="bi bi-telephone-inbound-fill"></i>
            </div>
            <div class="ensan-app-title">الاستقبال والعملاء</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 12. إدارة دار الضيافة --}}
        @if($u && $u->hasPermission('guest_houses.view'))
        <a href="{{ route('guest-houses.index') }}" class="ensan-app-card" data-category="operations" data-title="إدارة دار الضيافة" data-keywords="دار ضيافة غرف نزلاء إقامة guest house">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.45);">
                <i class="bi bi-house-door-fill"></i>
            </div>
            <div class="ensan-app-title">إدارة دار الضيافة</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 13. مساحات العمل Ensan Workspace --}}
        @if($u && $u->hasPermission('workspaces.view'))
        <a href="{{ route('workspaces.index') }}" class="ensan-app-card" data-category="operations" data-title="مساحات العمل" data-keywords="مساحات عمل قاعات مكاتب مشتركة workspaces">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #0d9488, #2dd4bf); box-shadow: 0 10px 20px -5px rgba(13, 148, 136, 0.45);">
                <i class="bi bi-easel2-fill"></i>
            </div>
            <div class="ensan-app-title">مساحات العمل</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 12. الزيارات الميدانية --}}
        @if($u && $u->hasPermission('beneficiaries.view'))
        <a href="{{ route('visits.index') }}" class="ensan-app-card" data-category="operations" data-title="الزيارات الميدانية" data-keywords="زيارات باحثين فحص ميداني visits">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #db2777, #ec4899); box-shadow: 0 10px 20px -5px rgba(219, 39, 119, 0.45);">
                <i class="bi bi-geo-alt-fill"></i>
            </div>
            <div class="ensan-app-title">الزيارات الميدانية</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 13. الموقع الإلكتروني (Popup) --}}
        @if($u && ($u->hasPermission('website.view') || $u->hasPermission('website.settings.view_edit') || $u->hasPermission('website.headquarters.view') || $u->hasPermission('website.partners.view') || $u->hasPermission('website.board.view') || $u->hasPermission('website.content.view_edit')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppWebsite" data-category="digital" data-title="الموقع الإلكتروني" data-keywords="موقع إعدادات شركاء مجلس أمناء تبرعات صفحات website">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #4338ca, #6366f1); box-shadow: 0 10px 20px -5px rgba(67, 56, 202, 0.45);">
                <i class="bi bi-globe2"></i>
            </div>
            <div class="ensan-app-title">الموقع الإلكتروني</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 15 خدمة <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 14. تطبيق الموبايل (Popup) --}}
        @if($u && ($u->hasPermission('mobile.view') || $u->hasPermission('mobile.home_content.view_edit') || $u->hasPermission('mobile.news.view') || $u->hasPermission('mobile.volunteer_requests.view')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppMobile" data-category="digital" data-title="تطبيق الموبايل" data-keywords="تطبيق موبايل إشعارات حجوزات تبرعات mobile app">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #c026d3, #d946ef); box-shadow: 0 10px 20px -5px rgba(192, 38, 211, 0.45);">
                <i class="bi bi-phone-fill"></i>
            </div>
            <div class="ensan-app-title">تطبيق الموبايل</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 7 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 15. الإعدادات والنظام (Popup) --}}
        @if($u && ($u->hasPermission('roles.view') || $u->hasPermission('users.view') || $u->hasPermission('audits.view') || $u->hasPermission('change_requests.view') || $u->hasPermission('reports.view') || $u->hasRole('admin') || $u->hasRole('super_admin')))
        <div class="ensan-app-card" data-bs-toggle="modal" data-bs-target="#modalAppSettings" data-category="admin finance" data-title="الإعدادات والنظام" data-keywords="مستخدمين صلاحيات أدوار سجل تدقيق طلبات تعديل تقارير شاملة إحصاءات settings admin reports">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #475569, #64748b); box-shadow: 0 10px 20px -5px rgba(71, 85, 105, 0.45);">
                <i class="bi bi-gear-wide-connected"></i>
            </div>
            <div class="ensan-app-title">الإعدادات والنظام</div>
            <span class="ensan-app-badge badge-sub"><i class="bi bi-layers-fill"></i> 5 خدمات <i class="bi bi-chevron-down"></i></span>
        </div>
        @endif

        {{-- 16. الشكاوى والمقترحات --}}
        @if($u && ($u->hasPermission('complaints.view') || $u->hasPermission('complaints.create') || $u->roles->contains('key', 'finance')))
        <a href="{{ route('complaints.index') }}" class="ensan-app-card" data-category="admin" data-title="الشكاوى والمقترحات" data-keywords="شكاوى مقترحات تتبع complaints">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #f59e0b, #fbbf24); box-shadow: 0 10px 20px -5px rgba(245, 158, 11, 0.45);">
                <i class="bi bi-chat-dots-fill"></i>
            </div>
            <div class="ensan-app-title">الشكاوى والمقترحات</div>
            <span class="ensan-app-badge badge-direct"><i class="bi bi-box-arrow-up-left"></i> دخول مباشر</span>
        </a>
        @endif

        {{-- 18. معلومات المطور (Developer Profile) --}}
        <a href="{{ route('developer.info') }}" class="ensan-app-card" data-category="admin" data-title="معلومات المطور" data-keywords="مطور ابراهيم السجيني برمجة تواصل واتساب دعم فني developer ibrahim elseginy 01556592017">
            <div class="ensan-app-icon" style="background: linear-gradient(135deg, #1e40af, #3b82f6); box-shadow: 0 10px 20px -5px rgba(30, 64, 175, 0.45);">
                <i class="bi bi-code-slash"></i>
            </div>
            <div class="ensan-app-title">معلومات المطور</div>
            <span class="ensan-app-badge badge-direct" style="background: rgba(37, 211, 102, 0.15); color: #10b981; border: 1px solid rgba(16, 185, 129, 0.3);"><i class="bi bi-person-badge"></i> @Ibrahim Elseginy</span>
        </a>

    </div>

    {{-- Empty Search State --}}
    <div id="launcherEmptyState" class="text-center py-5 d-none bg-card rounded-4 border p-4 my-3">
        <div class="p-3 rounded-circle bg-warning-subtle text-warning d-inline-flex mb-3">
            <i class="bi bi-search fs-2"></i>
        </div>
        <h5 class="fw-bold mb-1 text-main">لم يتم العثور على أي قسم يطابق بحثك</h5>
        <p class="text-muted small mb-3">تأكد من كتابة الكلمة بشكل صحيح، أو جرّب اختيار تصنيف آخر.</p>
        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-4" id="clearSearchBtn">
            إعادة تعيين البحث
        </button>
    </div>
</div>

{{-- ==============================================================
     MODALS FOR SUB-PAGES
     ============================================================== --}}

{{-- 0. Modal: التقديمات والبحث المبدئي --}}
<div class="modal fade ensan-sub-modal" id="modalAppSubmissions" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #0284c7, #06b6d4);">
                        <i class="bi bi-file-earmark-text-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">قطاع التقديمات والبحث المبدئي</h5>
                        <p class="text-muted small mb-0">إدارة طلبات المساعدة، نماذج التقديم الإلكترونية، وفلترة الحالات الجديدة</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    {{-- 1. لوحة إدارة طلبات التقديم --}}
                    <a href="{{ route('mobile.case-applications.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-inbox-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">سجل طلبات التقديم</div>
                            <small class="text-muted">متابعة وفحص الطلبات وتحديد حالة البحث</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    {{-- 2. استمارة مشروع زاد (الأيتام) --}}
                    <a href="{{ route('cases.apply-zad') }}" target="_blank" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-heart-pulse-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">استمارة مشروع زاد (الأيتام)</div>
                            <small class="text-muted">نموذج التقديم الإلكتروني لكفالات الأيتام</small>
                        </div>
                        <i class="bi bi-box-arrow-up-right ensan-sub-tile-arrow"></i>
                    </a>

                    {{-- 3. استمارة مشروع مدرار --}}
                    <a href="{{ route('cases.apply-midrar') }}" target="_blank" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-droplet-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">استمارة مشروع مدرار</div>
                            <small class="text-muted">سقيا ماء، وصلات مياه، أسقف، وصدقات جارية</small>
                        </div>
                        <i class="bi bi-box-arrow-up-right ensan-sub-tile-arrow"></i>
                    </a>

                    {{-- 4. استمارة مشروع بعثاء الأمل (الأورام) --}}
                    <a href="{{ route('cases.apply-hope') }}" target="_blank" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-hospital-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">استمارة بعثاء الأمل (الأورام)</div>
                            <small class="text-muted">نموذج تقديم رعاية مرضى الأورام الطبي</small>
                        </div>
                        <i class="bi bi-box-arrow-up-right ensan-sub-tile-arrow"></i>
                    </a>

                    {{-- 5. استمارة طلب مساعدة عامة --}}
                    <a href="{{ route('cases.public-apply') }}" target="_blank" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-file-earmark-person-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">استمارة طلب مساعدة عامة</div>
                            <small class="text-muted">نموذج البحث المبدئي للحالات الإنسانية العامة</small>
                        </div>
                        <i class="bi bi-box-arrow-up-right ensan-sub-tile-arrow"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 1. Modal: اللوجيستك --}}
<div class="modal fade ensan-sub-modal" id="modalAppLogistics" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #ea580c, #f97316);">
                        <i class="bi bi-signpost-2-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">قطاع اللوجيستك والتوصيل</h5>
                        <p class="text-muted small mb-0">إدارة خطوط السير والمناديب والرحلات الميدانية</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && $u->hasPermission('manage_logistics'))
                    <a href="{{ route('logistics.dashboard') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-grid-1x2"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">لوحة تحكم اللوجيستك</div>
                            <small class="text-muted">مؤشرات الأداء ومتابعة العمليات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('delegates.view'))
                    <a href="{{ route('delegates.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">المندوبون</div>
                            <small class="text-muted">إدارة المناديب وتكليفات التوصيل</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('travel_routes.view'))
                    <a href="{{ route('travel-routes.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-geo"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">خطوط السير</div>
                            <small class="text-muted">تحديد وتوزيع مسارات التوزيع</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('trips.view'))
                    <a href="{{ route('trips.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-pin-map"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الرحلات</div>
                            <small class="text-muted">جدول الرحلات ومواعيد التحرك</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('kafr_el_sheikh_deliveries.view'))
                    <a href="{{ route('kafr-el-sheikh-services.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-truck"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">توصيلات كفر الشيخ</div>
                            <small class="text-muted">خدمات وتسليمات كفر الشيخ</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('kafr-el-sheikh-brokers.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">سماسرة كفر الشيخ</div>
                            <small class="text-muted">إدارة الوسطاء الميدانيين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && ($u->hasPermission('manage_logistics') || $u->hasRole('admin')))
                    <a href="{{ route('tanta-workers.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-purple-subtle text-purple">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">عمال طنطا</div>
                            <small class="text-muted">إدارة ومتابعة عمال فرع طنطا</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 2. Modal: الموارد البشرية HR --}}
<div class="modal fade ensan-sub-modal" id="modalAppHR" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">الموارد البشرية وفريق العمل</h5>
                        <p class="text-muted small mb-0">الموظفون، الحضور والانصراف، المهام، والمتطوعون</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && ($u->hasRole('hr') || $u->hasPermission('hr.evaluations')))
                    <a href="{{ route('hr.evaluations') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">لوحة تحكم HR</div>
                            <small class="text-muted">مؤشرات الأداء والتقييمات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('users.view'))
                    <a href="{{ route('users.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الموظفون</div>
                            <small class="text-muted">دليل وبيانات العاملين بالمنظومة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    <a href="{{ route('employee-attendance.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">حضور وانصراف الموظفين</div>
                            <small class="text-muted">سجلات الدوام والتأخيرات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('employee-tasks.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-check2-circle"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">مهام الموظفين</div>
                            <small class="text-muted">توزيع ومتابعة إنجاز المهام</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('leaves.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-calendar-x"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الإجازات والأذونات</div>
                            <small class="text-muted">طلبات ورصيد الإجازات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    @if($u && $u->hasPermission('payrolls.view'))
                    <a href="{{ route('payrolls.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">مسير الرواتب</div>
                            <small class="text-muted">رواتب واستحقاقات الموظفين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('volunteers.view'))
                    <a href="{{ route('volunteers.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-purple-subtle text-purple">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">المتطوعون</div>
                            <small class="text-muted">دليل المتطوعين والفرق التطوعية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('volunteer-attendance.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-clock"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">حضور المتطوعين</div>
                            <small class="text-muted">تسجيل حضور الفعاليات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('volunteer-hours.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-hourglass-split"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">ساعات التطوع</div>
                            <small class="text-muted">احتساب وإحصاء ساعات التطوع</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('tasks.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-list-task"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">مهام التطوع</div>
                            <small class="text-muted">تكليفات الفرق التطوعية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 3. Modal: الحسابات والمالية --}}
<div class="modal fade ensan-sub-modal" id="modalAppFinance" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #0d9488, #14b8a6);">
                        <i class="bi bi-calculator-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">الحسابات والمالية</h5>
                        <p class="text-muted small mb-0">الخزائن، القيود، المصروفات والإيرادات، وإغلاق اليوميات</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && $u->hasPermission('treasuries.view'))
                    <a href="{{ route('treasuries.dashboard') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-speedometer2"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">لوحة تحكم الخزائن</div>
                            <small class="text-muted">الأرصدة الحالية وحركة السيولة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('treasuries.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-safe2"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الخزائن النقدية</div>
                            <small class="text-muted">إدارة الخزائن الرئيسية والفرعية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    <a href="{{ route('expenses.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-arrow-up-right-circle"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">المصروفات</div>
                            <small class="text-muted">سندات الصرف والبنود التشغيلية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('revenues.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-arrow-down-left-circle"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الإيرادات</div>
                            <small class="text-muted">سندات القبض وموارد المنظومة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('journal-entries.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">القيود اليومية</div>
                            <small class="text-muted">سجل القيود المحاسبية المزدوجة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    @if($u && $u->hasPermission('closures.view'))
                    <a href="{{ route('closures.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-lock"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">إغلاق اليوميات والعهد</div>
                            <small class="text-muted">تقفيل الخزائن واعتماد التسويات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('accounts.view'))
                    <a href="{{ route('accounts.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-diagram-3"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">دليل الحسابات</div>
                            <small class="text-muted">شجرة الحسابات ومراكز التكلفة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 4. Modal: إدارة المخازن --}}
<div class="modal fade ensan-sub-modal" id="modalAppInventory" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #d97706, #f59e0b);">
                        <i class="bi bi-boxes"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إدارة المخازن والمستودعات</h5>
                        <p class="text-muted small mb-0">الأصناف، المستودعات، الموردون، وحركات الصرف والإضافة</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && $u->hasPermission('inventory.view'))
                    <a href="{{ route('warehouses.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">المستودعات والمخازن</div>
                            <small class="text-muted">دليل المخازن ومواقع التخزين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('items.view'))
                    <a href="{{ route('items.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-box-seam"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الأصناف والمواد</div>
                            <small class="text-muted">بطاقات الأصناف والأسعار والأرصدة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('suppliers.view'))
                    <a href="{{ route('suppliers.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-shop"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الموردون</div>
                            <small class="text-muted">سجل الموردين وبيانات الاتصال</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('inventory_transactions.view'))
                    <a href="{{ route('inventory-transactions.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-arrow-left-right"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">حركات المخزون</div>
                            <small class="text-muted">أذون الإضافة والصرف والتحويل</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 5. Modal: حملات موسمية --}}
<div class="modal fade ensan-sub-modal" id="modalAppCampaigns" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #dc2626, #ef4444);">
                        <i class="bi bi-megaphone-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">الحملات والمبادرات الموسمية</h5>
                        <p class="text-muted small mb-0">حملات التبرع، كراتين وشنط رمضان، وإفطارات الصائمين</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && $u->hasPermission('campaigns.view'))
                    <a href="{{ route('campaigns.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">إدارة الحملات</div>
                            <small class="text-muted">إنشاء ومتابعة حملات التبرع</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('ramadan_bags.view'))
                    <a href="{{ route('ramadan-bags.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-bag-heart"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">شنط وكراتين رمضان</div>
                            <small class="text-muted">تعبئة وتوزيع الشنط الغذائية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('ramadan_iftars.view'))
                    <a href="{{ route('ramadan-iftars.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-cup-hot"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">إفطارات رمضان</div>
                            <small class="text-muted">وجبات ومطابخ إفطار الصائم</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 6. Modal: الشراكات والعضويات --}}
<div class="modal fade ensan-sub-modal" id="modalAppCollaborations" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #0891b2, #06b6d4);">
                        <i class="bi bi-diagram-3-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">التعاونات والشراكات والعضوية</h5>
                        <p class="text-muted small mb-0">بروتوكولات الجامعات والمدارس، العضويات، ومندوبي الأدوية</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && $u->hasPermission('school_collaborations.view'))
                    <a href="{{ route('school-collaborations.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-building"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">بروتوكولات المدارس والجامعات</div>
                            <small class="text-muted">الأنشطة والشراكات التعليمية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('memberships.view'))
                    <a href="{{ route('memberships.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">العضويات والاشتراكات</div>
                            <small class="text-muted">إدارة أعضاء الجمعية والاشتراكات</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('oncology_medicine_reps.view'))
                    <a href="{{ route('oncology-medicine-reps.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-capsule"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">مندوبو أدوية الأورام</div>
                            <small class="text-muted">تنسيق وتوفير أدوية مرضى الأورام</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>



{{-- 8. Modal: الموقع الإلكتروني --}}
<div class="modal fade ensan-sub-modal" id="modalAppWebsite" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #4338ca, #6366f1);">
                        <i class="bi bi-globe2"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إدارة الموقع الإلكتروني</h5>
                        <p class="text-muted small mb-0">المحتوى، الفروع، الشركاء، التبرعات، والصفحات الثابتة</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && $u->hasPermission('website.settings.view_edit'))
                    <a href="{{ route('website.settings.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-gear-wide-connected"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">محتوى الرئيسية</div>
                            <small class="text-muted">البانرات والأقسام الرئيسية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.headquarters.view'))
                    <a href="{{ route('website.headquarters.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-geo-alt"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">المقر والفروع</div>
                            <small class="text-muted">عناوين الفروع وأرقام التواصل</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.partners.view'))
                    <a href="{{ route('website.partners.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-award"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">جدار الشرف والشركاء</div>
                            <small class="text-muted">الداعمون والشركاء الاستراتيجيون</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.board.view'))
                    <a href="{{ route('website.board.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">مجلس الأمناء</div>
                            <small class="text-muted">أعضاء مجلس الإدارة والإدارة التنفيذية</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.content.view_edit'))
                    <a href="{{ route('website.content') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-window-sidebar"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">محتوى المشاريع</div>
                            <small class="text-muted">تفاصيل المشاريع المعروضة للجمهور</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.campaigns_content.view_edit'))
                    <a href="{{ route('website.campaigns.content') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-megaphone"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">محتوى الحملات</div>
                            <small class="text-muted">صفحات الحملات التبرعية أونلاين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.volunteer_requests.view'))
                    <a href="{{ route('website.volunteer-requests.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">طلبات التطوع</div>
                            <small class="text-muted">استمارات التطوع الواردة من الموقع</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && ($u->hasPermission('mobile.case_applications.view') || $u->hasPermission('beneficiaries.view') || $u->hasRole('admin') || $u->hasRole('super_admin')))
                    <a href="{{ route('mobile.case-applications.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-clipboard2-check-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">استمارات التقديم والبحث المبدئي</div>
                            <small class="text-muted">نماذج التقديم الإلكترونية (زاد، مدرار، الأورام، والبحث المبدئي)</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.donation_page.view_edit'))
                    <a href="{{ route('website.donation-page.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-heart-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">إعدادات صفحة التبرع</div>
                            <small class="text-muted">بوابات الدفع والخيارات المتاحة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.donation_settings.view_edit'))
                    <a href="{{ route('website.donation-settings.unified') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-purple-subtle text-purple">
                            <i class="bi bi-ui-checks-grid"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">مجالات الدعم (الفئات)</div>
                            <small class="text-muted">فئات وأسهم التبرع السريع</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.accounts.view'))
                    <a href="{{ route('website.accounts.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-person-badge"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">حسابات المتبرعين</div>
                            <small class="text-muted">حسابات وبوابة المتبرعين الأونلاين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('website.donation_accounts.view'))
                    <a href="{{ route('website.donation-accounts.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-wallet2"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">حسابات تبرعات الموقع</div>
                            <small class="text-muted">البنوك والمحافظ الإلكترونية للتحويل</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    <a href="{{ route('website.contact-messages.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">رسائل تواصل معنا</div>
                            <small class="text-muted">استفسارات ورسائل الزوار</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('website.pages.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-secondary-subtle text-secondary">
                            <i class="bi bi-file-earmark-text"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الصفحات الثابتة</div>
                            <small class="text-muted">من نحن، سياسة الخصوصية، والشروط</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('website.cards.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-card-list"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">البطاقات التعريفية</div>
                            <small class="text-muted">كروت المبادرات والمشاريع</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>

                    <a href="{{ route('website.testimonials.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-chat-heart"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">آراء المستفيدين والمجتمع</div>
                            <small class="text-muted">قصص النجاح وانطباعات المستفيدين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 9. Modal: تطبيق الموبايل --}}
<div class="modal fade ensan-sub-modal" id="modalAppMobile" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #c026d3, #d946ef);">
                        <i class="bi bi-phone-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إدارة تطبيق الموبايل</h5>
                        <p class="text-muted small mb-0">المحتوى، الأخبار، الإشعارات الفورية، وسجلات تبرعات التطبيق</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && ($u->hasPermission('mobile.case_applications.view') || $u->hasPermission('beneficiaries.view') || $u->hasRole('admin') || $u->hasRole('super_admin')))
                    <a href="{{ route('mobile.case-applications.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-file-earmark-person-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">طلبات الحالات والبحث المبدئي</div>
                            <small class="text-muted">استمارات التقديم (زاد، مدرار، الأورام، والبحث المبدئي)</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.home_content.view_edit'))
                    <a href="{{ route('mobile.home_content.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-sliders"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">محتوى التطبيق الرئيسي</div>
                            <small class="text-muted">الشرائح الترويجية وأقسام الموبايل</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.news.view'))
                    <a href="{{ route('mobile.news.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-newspaper"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الأخبار والإعلانات</div>
                            <small class="text-muted">أخبار الجمعية على التطبيق</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.volunteer_requests.view'))
                    <a href="{{ route('mobile.volunteer-requests.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">طلبات التطوع</div>
                            <small class="text-muted">طلبات الانضمام عبر الهاتف</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.bookings.view'))
                    <a href="{{ route('mobile.bookings.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-calendar2-check"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">حجوزات التطبيق</div>
                            <small class="text-muted">حجوزات القاعات والضيافة أونلاين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.donations.view'))
                    <a href="{{ route('mobile.donations.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-cash-coin"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">سجلات تبرعات التطبيق</div>
                            <small class="text-muted">عمليات الدفع عبر الهواتف</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.donors.view'))
                    <a href="{{ route('mobile.donors_auth.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-person-badge-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">تسجيل الدخول للموبايل</div>
                            <small class="text-muted">إدارة حسابات مستخدمي التطبيق</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('mobile.notifications.view'))
                    <a href="{{ route('mobile.notifications.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-danger-subtle text-danger">
                            <i class="bi bi-bell"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الإشعارات (Push Notifications)</div>
                            <small class="text-muted">إرسال التنبيهات الفورية للمستخدمين</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- 10. Modal: الإعدادات والنظام --}}
<div class="modal fade ensan-sub-modal" id="modalAppSettings" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="ensan-app-icon mb-0" style="width: 44px; height: 44px; border-radius: 12px; font-size: 1.3rem; background: linear-gradient(135deg, #475569, #64748b);">
                        <i class="bi bi-gear-wide-connected"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إعدادات النظام والتحكم</h5>
                        <p class="text-muted small mb-0">المستخدمون، الصلاحيات والأدوار، التقارير الشاملة، سجل التدقيق، وطلبات التعديل</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <div class="ensan-sub-grid">
                    @if($u && ($u->hasPermission('reports.view') || $u->hasRole('admin') || $u->hasRole('super_admin')))
                    <a href="{{ route('reports.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-bar-chart-fill"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">التقارير الشاملة</div>
                            <small class="text-muted">التقارير المالية، الإحصاءات العامة، والطباعة</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('users.view'))
                    <a href="{{ route('users.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-primary-subtle text-primary">
                            <i class="bi bi-person-gear"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">المستخدمون وحسابات النظام</div>
                            <small class="text-muted">إدارة حسابات مسؤولي وموظفي النظام</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('roles.view'))
                    <a href="{{ route('roles.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-warning-subtle text-warning">
                            <i class="bi bi-shield-lock"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">الأدوار والصلاحيات</div>
                            <small class="text-muted">تحديد صلاحيات الوصول والتحكم</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('audits.view'))
                    <a href="{{ route('audits.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-info-subtle text-info">
                            <i class="bi bi-journal-text"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">سجل العمليات والتدقيق (Audits)</div>
                            <small class="text-muted">مراقبة كافة حركات التعديل والإدخال</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif

                    @if($u && $u->hasPermission('change_requests.view'))
                    <a href="{{ route('change-requests.index') }}" class="ensan-sub-tile">
                        <div class="ensan-sub-tile-icon bg-success-subtle text-success">
                            <i class="bi bi-arrow-repeat"></i>
                        </div>
                        <div class="ensan-sub-tile-content">
                            <div class="ensan-sub-tile-title">طلبات التعديل والاعتماد</div>
                            <small class="text-muted">مراجعة التغييرات المطلوب اعتمادها</small>
                        </div>
                        <i class="bi bi-arrow-left ensan-sub-tile-arrow"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Real-time Client-side Search & Category Filtering Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('ensanAppSearch');
    const cards = document.querySelectorAll('#ensanAppsGrid .ensan-app-card');
    const catButtons = document.querySelectorAll('.launcher-cat-btn');
    const emptyState = document.getElementById('launcherEmptyState');
    const clearBtn = document.getElementById('clearSearchBtn');

    let activeCategory = 'all';

    function applyFilter() {
        const query = (searchInput ? searchInput.value.trim().toLowerCase() : '');
        let visibleCount = 0;

        cards.forEach(card => {
            const title = (card.getAttribute('data-title') || '').toLowerCase();
            const keywords = (card.getAttribute('data-keywords') || '').toLowerCase();
            const categories = (card.getAttribute('data-category') || '').split(' ');

            const matchesQuery = !query || title.includes(query) || keywords.includes(query);
            const matchesCat = activeCategory === 'all' || categories.includes(activeCategory);

            if (matchesQuery && matchesCat) {
                card.style.display = 'flex';
                card.style.opacity = '1';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        if (emptyState) {
            emptyState.classList.toggle('d-none', visibleCount > 0);
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', applyFilter);
    }

    catButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            catButtons.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            activeCategory = this.getAttribute('data-category');
            applyFilter();
        });
    });

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            if (searchInput) searchInput.value = '';
            activeCategory = 'all';
            catButtons.forEach(b => b.classList.remove('active'));
            const allBtn = document.querySelector('.launcher-cat-btn[data-category="all"]');
            if (allBtn) allBtn.classList.add('active');
            applyFilter();
        });
    }

    // Keyboard shortcut: Press / to search anywhere
    document.addEventListener('keydown', function(e) {
        if ((e.key === '/' || ((e.ctrlKey || e.metaKey) && e.key.toLowerCase() === 'k')) && searchInput && document.activeElement !== searchInput) {
            e.preventDefault();
            searchInput.focus();
            searchInput.select();
        }
    });
});
</script>