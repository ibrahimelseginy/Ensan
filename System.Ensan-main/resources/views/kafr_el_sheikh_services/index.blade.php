@extends('layouts.app')

@section('title', 'خدمات كفر الشيخ')

@section('styles')
<style>
    .kfs-page {
        --kfs-primary: #0284c7;
        --kfs-primary-dark: #0369a1;
        --kfs-primary-soft: #e0f2fe;
        --kfs-surface: var(--ws-bg-card, #ffffff);
        --kfs-input: var(--ws-bg-input, #ffffff);
        --kfs-text: var(--ws-text-primary, #0f172a);
        --kfs-muted: var(--ws-text-secondary, #64748b);
        --kfs-border: var(--ws-border, #e2e8f0);
        color: var(--kfs-text);
    }

    .theme-dark .kfs-page {
        --kfs-primary-soft: rgba(14, 165, 233, .14);
        --kfs-surface: #0f172a;
        --kfs-input: #111c30;
        --kfs-text: #f8fafc;
        --kfs-muted: #94a3b8;
        --kfs-border: #263449;
    }

    .kfs-hero {
        position: relative;
        isolation: isolate;
        overflow: hidden;
        padding: clamp(1.4rem, 3vw, 2.25rem);
        border-radius: 1.5rem;
        color: #fff;
        background:
            radial-gradient(circle at 12% 5%, rgba(255, 255, 255, .2), transparent 25%),
            linear-gradient(125deg, #075985 0%, #0284c7 55%, #22d3ee 130%);
        box-shadow: 0 18px 42px rgba(2, 132, 199, .2);
    }

    .kfs-hero::after {
        content: '';
        position: absolute;
        inset-inline-end: -3rem;
        bottom: -5.5rem;
        width: 15rem;
        height: 15rem;
        border: 2.6rem solid rgba(255, 255, 255, .08);
        border-radius: 50%;
        z-index: -1;
    }

    .kfs-hero__content {
        max-width: 680px;
    }

    .kfs-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: .45rem;
        margin-bottom: .75rem;
        padding: .38rem .7rem;
        border: 1px solid rgba(255, 255, 255, .28);
        border-radius: 999px;
        background: rgba(255, 255, 255, .12);
        font-size: .78rem;
        font-weight: 700;
    }

    .kfs-hero h1 {
        margin-bottom: .55rem;
        font-size: clamp(1.55rem, 3vw, 2.35rem);
        font-weight: 800;
    }

    .kfs-hero p {
        max-width: 580px;
        margin: 0;
        color: rgba(255, 255, 255, .82);
        line-height: 1.8;
    }

    .kfs-add-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .55rem;
        min-height: 48px;
        padding: .75rem 1.15rem;
        border: 1px solid rgba(255, 255, 255, .7);
        border-radius: .85rem;
        background: #fff;
        color: #075985;
        font-weight: 800;
        box-shadow: 0 10px 24px rgba(3, 105, 161, .24);
        transition: transform .2s ease, box-shadow .2s ease;
    }

    .kfs-add-button:hover {
        transform: translateY(-2px);
        color: #075985;
        box-shadow: 0 14px 30px rgba(3, 105, 161, .3);
    }

    .kfs-stats {
        display: grid;
        grid-template-columns: repeat(3, minmax(0, 1fr));
        gap: .85rem;
        margin-top: 1rem;
    }

    .kfs-stat {
        display: flex;
        align-items: center;
        gap: .8rem;
        min-width: 0;
        padding: .9rem 1rem;
        border: 1px solid var(--kfs-border);
        border-radius: 1rem;
        background: var(--kfs-surface);
        box-shadow: 0 5px 18px rgba(15, 23, 42, .04);
    }

    .kfs-stat__icon {
        display: grid;
        place-items: center;
        flex: 0 0 42px;
        width: 42px;
        height: 42px;
        border-radius: .8rem;
        background: var(--kfs-primary-soft);
        color: var(--kfs-primary);
        font-size: 1.1rem;
    }

    .kfs-stat__value {
        display: block;
        color: var(--kfs-text);
        font-size: 1.15rem;
        font-weight: 800;
        line-height: 1.2;
    }

    .kfs-stat__label {
        display: block;
        overflow: hidden;
        margin-top: .2rem;
        color: var(--kfs-muted);
        font-size: .78rem;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .kfs-toolbar {
        margin: 1.25rem 0;
        padding: 1rem;
        border: 1px solid var(--kfs-border);
        border-radius: 1.1rem;
        background: var(--kfs-surface);
        box-shadow: 0 8px 24px rgba(15, 23, 42, .04);
    }

    .kfs-field-label {
        display: block;
        margin-bottom: .4rem;
        color: var(--kfs-text);
        font-size: .78rem;
        font-weight: 700;
    }

    .kfs-search {
        position: relative;
    }

    .kfs-search i {
        position: absolute;
        inset-inline-start: .9rem;
        top: 50%;
        color: var(--kfs-muted);
        transform: translateY(-50%);
        pointer-events: none;
    }

    .kfs-search .form-control {
        padding-inline-start: 2.6rem;
    }

    .kfs-page .form-control,
    .kfs-page .form-select,
    .service-modal .form-control {
        min-height: 46px;
        border-color: var(--kfs-border);
        border-radius: .8rem;
        background-color: var(--kfs-input);
        color: var(--kfs-text);
        box-shadow: none;
    }

    .kfs-page .form-control::placeholder,
    .service-modal .form-control::placeholder {
        color: var(--kfs-muted);
        opacity: .8;
    }

    .kfs-page .form-control:focus,
    .kfs-page .form-select:focus,
    .service-modal .form-control:focus {
        border-color: #38bdf8;
        box-shadow: 0 0 0 .22rem rgba(14, 165, 233, .12);
    }

    .kfs-filter-button,
    .kfs-reset-button {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        min-height: 46px;
        border-radius: .8rem;
        font-weight: 700;
    }

    .kfs-filter-button {
        border: 0;
        background: var(--kfs-primary);
        color: #fff;
    }

    .kfs-filter-button:hover {
        background: var(--kfs-primary-dark);
        color: #fff;
    }

    .kfs-reset-button {
        border: 1px solid var(--kfs-border);
        background: transparent;
        color: var(--kfs-text);
    }

    .kfs-reset-button:hover {
        border-color: #94a3b8;
        color: var(--kfs-text);
    }

    .kfs-results-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin: 1.4rem 0 .8rem;
    }

    .kfs-results-bar h2 {
        margin: 0;
        color: var(--kfs-text);
        font-size: 1.05rem;
        font-weight: 800;
    }

    .kfs-results-count {
        color: var(--kfs-muted);
        font-size: .82rem;
    }

    .service-card {
        display: flex;
        flex-direction: column;
        height: 100%;
        overflow: hidden;
        border: 1px solid var(--kfs-border);
        border-radius: 1.15rem;
        background: var(--kfs-surface);
        box-shadow: 0 8px 26px rgba(15, 23, 42, .055);
        transition: transform .2s ease, box-shadow .2s ease, border-color .2s ease;
    }

    .service-card:hover {
        transform: translateY(-3px);
        border-color: rgba(14, 165, 233, .45);
        box-shadow: 0 16px 34px rgba(15, 23, 42, .09);
    }

    .service-card__body {
        flex: 1;
        padding: 1.15rem;
    }

    .service-card__head {
        display: flex;
        align-items: flex-start;
        gap: .8rem;
    }

    .min-w-0 {
        min-width: 0;
    }

    .service-card__avatar {
        display: grid;
        place-items: center;
        flex: 0 0 46px;
        width: 46px;
        height: 46px;
        border-radius: .9rem;
        background: var(--kfs-primary-soft);
        color: var(--kfs-primary);
        font-size: 1.2rem;
    }

    .service-card__title {
        margin: 0 0 .38rem;
        overflow-wrap: anywhere;
        color: var(--kfs-text);
        font-size: 1rem;
        font-weight: 800;
        line-height: 1.45;
    }

    .service-type {
        display: inline-flex;
        align-items: center;
        max-width: 100%;
        padding: .28rem .62rem;
        overflow: hidden;
        border-radius: 999px;
        background: var(--kfs-primary-soft);
        color: var(--kfs-primary-dark);
        font-size: .72rem;
        font-weight: 700;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .theme-dark .service-type {
        color: #7dd3fc;
    }

    .service-card__contact {
        display: flex;
        align-items: center;
        gap: .55rem;
        margin-top: 1rem;
        padding: .72rem .8rem;
        border-radius: .8rem;
        background: rgba(148, 163, 184, .09);
        color: var(--kfs-text);
        font-size: .85rem;
        direction: ltr;
        text-align: left;
    }

    .service-card__contact i {
        color: var(--kfs-primary);
    }

    .service-card__notes {
        display: -webkit-box;
        min-height: 3rem;
        margin: .9rem 0 0;
        overflow: hidden;
        color: var(--kfs-muted);
        font-size: .84rem;
        line-height: 1.75;
        -webkit-box-orient: vertical;
        -webkit-line-clamp: 2;
    }

    .service-card__footer {
        display: flex;
        align-items: center;
        gap: .55rem;
        padding: .85rem 1.15rem;
        border-top: 1px solid var(--kfs-border);
        background: rgba(148, 163, 184, .04);
    }

    .service-action {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .35rem;
        min-height: 38px;
        padding: .45rem .7rem;
        border-radius: .7rem;
        font-size: .78rem;
        font-weight: 700;
    }

    .service-action--call {
        flex: 1;
        border: 0;
        background: var(--kfs-primary);
        color: #fff;
    }

    .service-action--call:hover {
        background: var(--kfs-primary-dark);
        color: #fff;
    }

    .service-action--edit {
        border: 1px solid #f59e0b;
        background: rgba(245, 158, 11, .08);
        color: #b45309;
    }

    .theme-dark .service-action--edit {
        color: #fbbf24;
    }

    .service-action--delete {
        width: 38px;
        padding: 0;
        border: 1px solid rgba(239, 68, 68, .4);
        background: rgba(239, 68, 68, .07);
        color: #dc2626;
    }

    .kfs-empty {
        padding: clamp(2.5rem, 8vw, 5rem) 1rem;
        border: 1px dashed var(--kfs-border);
        border-radius: 1.2rem;
        background: var(--kfs-surface);
        text-align: center;
    }

    .kfs-empty__icon {
        display: grid;
        place-items: center;
        width: 70px;
        height: 70px;
        margin: 0 auto 1rem;
        border-radius: 1.2rem;
        background: var(--kfs-primary-soft);
        color: var(--kfs-primary);
        font-size: 1.8rem;
    }

    .kfs-empty h3 {
        color: var(--kfs-text);
        font-size: 1.1rem;
        font-weight: 800;
    }

    .kfs-empty p {
        color: var(--kfs-muted);
    }

    .service-modal {
        --kfs-surface: var(--ws-bg-card, #ffffff);
        --kfs-input: var(--ws-bg-input, #ffffff);
        --kfs-text: var(--ws-text-primary, #0f172a);
        --kfs-muted: var(--ws-text-secondary, #64748b);
        --kfs-border: var(--ws-border, #e2e8f0);
    }

    .theme-dark .service-modal {
        --kfs-surface: #0f172a;
        --kfs-input: #111c30;
        --kfs-text: #f8fafc;
        --kfs-muted: #94a3b8;
        --kfs-border: #263449;
    }

    .service-modal .modal-dialog {
        max-width: 680px;
        margin-top: 1rem;
        margin-bottom: 1rem;
    }

    .service-modal .modal-content {
        max-height: calc(100dvh - 2rem);
        overflow: hidden;
        border: 1px solid var(--kfs-border);
        border-radius: 1.25rem;
        background: var(--kfs-surface);
        color: var(--kfs-text);
        box-shadow: 0 30px 70px rgba(2, 8, 23, .28);
    }

    .service-modal form {
        display: flex;
        flex-direction: column;
        min-height: 0;
        max-height: calc(100dvh - 2rem);
    }

    .service-modal .modal-header,
    .service-modal .modal-footer {
        flex: 0 0 auto;
        border-color: var(--kfs-border);
        background: var(--kfs-surface);
    }

    .service-modal .modal-header {
        padding: 1rem 1.25rem;
    }

    .service-modal .modal-title {
        display: flex;
        align-items: center;
        gap: .7rem;
        color: var(--kfs-text);
        font-size: 1rem;
        font-weight: 800;
    }

    .service-modal__icon {
        display: grid;
        place-items: center;
        width: 40px;
        height: 40px;
        border-radius: .75rem;
        background: #e0f2fe;
        color: #0284c7;
    }

    .service-modal .btn-close {
        margin: 0 auto 0 0;
    }

    .theme-dark .service-modal .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%);
    }

    .service-modal .modal-body {
        min-height: 0;
        overflow-y: auto;
        padding: 1.25rem;
        overscroll-behavior: contain;
    }

    .service-modal .modal-footer {
        display: flex;
        justify-content: flex-start;
        gap: .6rem;
        padding: .9rem 1.25rem;
    }

    .service-modal .form-label {
        margin-bottom: .42rem;
        color: var(--kfs-text);
        font-size: .82rem;
        font-weight: 700;
    }

    .service-modal .form-text {
        color: var(--kfs-muted);
        font-size: .72rem;
    }

    .service-modal textarea.form-control {
        min-height: 115px;
        resize: vertical;
    }

    .service-modal__save,
    .service-modal__cancel {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: .4rem;
        min-height: 44px;
        padding: .62rem 1rem;
        border-radius: .75rem;
        font-weight: 800;
    }

    .service-modal__save {
        border: 0;
        background: #0284c7;
        color: #fff;
    }

    .service-modal__save:hover {
        background: #0369a1;
    }

    .service-modal__cancel {
        border: 1px solid var(--kfs-border);
        background: transparent;
        color: var(--kfs-text);
    }

    .service-modal .invalid-feedback {
        display: block;
    }

    @media (max-width: 767.98px) {
        .kfs-hero__layout {
            align-items: stretch !important;
            flex-direction: column;
        }

        .kfs-add-button {
            width: 100%;
        }

        .kfs-stats {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 575.98px) {
        .kfs-hero {
            border-radius: 1.15rem;
        }

        .kfs-toolbar {
            padding: .85rem;
        }

        .kfs-filter-actions {
            display: grid !important;
            grid-template-columns: 1fr 1fr;
        }

        .service-card__footer {
            flex-wrap: wrap;
        }

        .service-action--call {
            min-width: 100%;
        }

        .service-action--edit {
            flex: 1;
        }

        .service-modal .modal-dialog {
            align-items: flex-end;
            min-height: 100dvh;
            margin: 0;
        }

        .service-modal .modal-content {
            width: 100%;
            max-height: 94dvh;
            border-width: 1px 0 0;
            border-radius: 1.25rem 1.25rem 0 0;
        }

        .service-modal form {
            max-height: 94dvh;
        }

        .service-modal .modal-header,
        .service-modal .modal-body,
        .service-modal .modal-footer {
            padding-inline: 1rem;
        }

        .service-modal .modal-footer {
            display: grid;
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (prefers-reduced-motion: reduce) {
        .service-card,
        .kfs-add-button {
            transition: none;
        }
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-3 py-lg-4 kfs-page">
    <section class="kfs-hero" aria-labelledby="services-page-title">
        <div class="d-flex align-items-center justify-content-between gap-4 kfs-hero__layout">
            <div class="kfs-hero__content">
                <span class="kfs-eyebrow"><i class="bi bi-geo-alt-fill"></i> دليل الخدمات المحلية</span>
                <h1 id="services-page-title">خدمات كفر الشيخ</h1>
                <p>اعثر على مقدم الخدمة المناسب بسرعة، واتصل به مباشرة أو حدّث بياناته من مكان واحد.</p>
            </div>
            <button type="button" class="btn kfs-add-button" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                <i class="bi bi-plus-circle-fill"></i>
                إضافة خدمة جديدة
            </button>
        </div>
    </section>

    <section class="kfs-stats" aria-label="ملخص الخدمات">
        <div class="kfs-stat">
            <span class="kfs-stat__icon"><i class="bi bi-grid-1x2-fill"></i></span>
            <span>
                <strong class="kfs-stat__value">{{ number_format($stats['total']) }}</strong>
                <small class="kfs-stat__label">إجمالي الخدمات</small>
            </span>
        </div>
        <div class="kfs-stat">
            <span class="kfs-stat__icon"><i class="bi bi-tags-fill"></i></span>
            <span>
                <strong class="kfs-stat__value">{{ number_format($stats['types']) }}</strong>
                <small class="kfs-stat__label">أنواع الخدمات</small>
            </span>
        </div>
        <div class="kfs-stat">
            <span class="kfs-stat__icon"><i class="bi bi-telephone-fill"></i></span>
            <span>
                <strong class="kfs-stat__value">{{ number_format($stats['with_phone']) }}</strong>
                <small class="kfs-stat__label">خدمات متاحة للتواصل</small>
            </span>
        </div>
    </section>

    <section class="kfs-toolbar" aria-label="البحث وتصفية الخدمات">
        <form method="GET" action="{{ route('kafr-el-sheikh-services.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-lg-5">
                    <label for="service-search" class="kfs-field-label">ابحث في دليل الخدمات</label>
                    <div class="kfs-search">
                        <i class="bi bi-search"></i>
                        <input
                            id="service-search"
                            type="search"
                            name="q"
                            value="{{ $search }}"
                            class="form-control"
                            placeholder="الاسم، نوع الخدمة، رقم الهاتف..."
                        >
                    </div>
                </div>
                <div class="col-sm-7 col-lg-3">
                    <label for="service-type-filter" class="kfs-field-label">نوع الخدمة</label>
                    <select id="service-type-filter" name="service_type" class="form-select">
                        <option value="">كل الأنواع</option>
                        @foreach($serviceTypes as $type)
                            <option value="{{ $type }}" @selected($serviceType === $type)>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-sm-5 col-lg-2">
                    <label for="per-page" class="kfs-field-label">عدد النتائج</label>
                    <select id="per-page" name="per_page" class="form-select">
                        @foreach([12, 24, 48] as $option)
                            <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-lg-2 d-flex gap-2 kfs-filter-actions">
                    <button type="submit" class="btn kfs-filter-button flex-fill">
                        <i class="bi bi-funnel"></i>
                        تطبيق
                    </button>
                    <a href="{{ route('kafr-el-sheikh-services.index') }}" class="btn kfs-reset-button" title="إعادة ضبط البحث">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span class="visually-hidden">إعادة ضبط</span>
                    </a>
                </div>
            </div>
        </form>
    </section>

    <div class="kfs-results-bar">
        <h2>دليل مقدمي الخدمات</h2>
        <span class="kfs-results-count">
            @if($services->total())
                عرض {{ $services->firstItem() }}–{{ $services->lastItem() }} من {{ $services->total() }}
            @else
                لا توجد نتائج
            @endif
        </span>
    </div>

    @if($services->isNotEmpty())
        <div class="row g-3 g-xl-4">
            @foreach($services as $item)
                <div class="col-md-6 col-xl-4">
                    <article class="service-card">
                        <div class="service-card__body">
                            <div class="service-card__head">
                                <span class="service-card__avatar" aria-hidden="true">
                                    <i class="bi bi-person-workspace"></i>
                                </span>
                                <div class="min-w-0">
                                    <h3 class="service-card__title">{{ $item->name }}</h3>
                                    <span class="service-type">{{ $item->service_type ?: 'خدمة عامة' }}</span>
                                </div>
                            </div>

                            @if($item->phone)
                                <div class="service-card__contact">
                                    <i class="bi bi-telephone-fill"></i>
                                    <span>{{ $item->phone }}</span>
                                </div>
                            @endif

                            <p class="service-card__notes" @if($item->notes) title="{{ $item->notes }}" @endif>
                                {{ $item->notes ?: 'لا توجد ملاحظات إضافية لهذه الخدمة.' }}
                            </p>
                        </div>

                        <footer class="service-card__footer">
                            @if($item->phone)
                                <a href="tel:{{ preg_replace('/[^0-9+]/', '', $item->phone) }}" class="service-action service-action--call">
                                    <i class="bi bi-telephone-outbound-fill"></i>
                                    اتصال الآن
                                </a>
                            @endif

                            <button
                                type="button"
                                class="service-action service-action--edit"
                                data-edit-service
                                data-action="{{ route('kafr-el-sheikh-services.update', $item) }}"
                                data-name="{{ $item->name }}"
                                data-service-type="{{ $item->service_type }}"
                                data-phone="{{ $item->phone }}"
                                data-notes="{{ $item->notes }}"
                            >
                                <i class="bi bi-pencil-square"></i>
                                تعديل
                            </button>

                            <form
                                method="POST"
                                action="{{ route('kafr-el-sheikh-services.destroy', $item) }}"
                                data-confirm-delete
                                data-service-name="{{ $item->name }}"
                            >
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="service-action service-action--delete" title="حذف {{ $item->name }}" aria-label="حذف {{ $item->name }}">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </footer>
                    </article>
                </div>
            @endforeach
        </div>

        @if($services->hasPages())
            <div class="d-flex justify-content-center mt-4">
                {{ $services->links() }}
            </div>
        @endif
    @else
        <div class="kfs-empty">
            <span class="kfs-empty__icon"><i class="bi bi-search"></i></span>
            @if($search !== '' || $serviceType !== '')
                <h3>لم نعثر على خدمة مطابقة</h3>
                <p>جرّب كلمة بحث أقصر أو اعرض جميع أنواع الخدمات.</p>
                <a href="{{ route('kafr-el-sheikh-services.index') }}" class="btn kfs-filter-button px-4">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    عرض كل الخدمات
                </a>
            @else
                <h3>لا توجد خدمات مضافة حتى الآن</h3>
                <p>ابدأ بإضافة أول مقدم خدمة إلى الدليل.</p>
                <button type="button" class="btn kfs-filter-button px-4" data-bs-toggle="modal" data-bs-target="#createServiceModal">
                    <i class="bi bi-plus-circle"></i>
                    إضافة أول خدمة
                </button>
            @endif
        </div>
    @endif
</div>

<datalist id="serviceTypeSuggestions">
    @foreach($serviceTypes as $type)
        <option value="{{ $type }}"></option>
    @endforeach
</datalist>

<div class="modal fade service-modal" id="createServiceModal" tabindex="-1" aria-labelledby="create-service-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('kafr-el-sheikh-services.store') }}">
                @csrf
                <input type="hidden" name="form_context" value="create">

                <div class="modal-header">
                    <h2 class="modal-title" id="create-service-title">
                        <span class="service-modal__icon"><i class="bi bi-person-plus-fill"></i></span>
                        إضافة خدمة جديدة
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>

                <div class="modal-body">
                    @if($errors->any() && old('form_context') === 'create')
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            يرجى مراجعة الحقول الموضحة أدناه.
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="create-name" class="form-label">اسم مقدم الخدمة <span class="text-danger">*</span></label>
                            <input
                                id="create-name"
                                type="text"
                                name="name"
                                value="{{ old('form_context') === 'create' ? old('name') : '' }}"
                                class="form-control @if(old('form_context') === 'create') @error('name') is-invalid @enderror @endif"
                                maxlength="255"
                                autocomplete="name"
                                placeholder="مثال: محمد أحمد"
                                required
                            >
                            @if(old('form_context') === 'create')
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        <div class="col-md-5">
                            <label for="create-type" class="form-label">نوع الخدمة</label>
                            <input
                                id="create-type"
                                type="text"
                                name="service_type"
                                value="{{ old('form_context') === 'create' ? old('service_type') : '' }}"
                                class="form-control @if(old('form_context') === 'create') @error('service_type') is-invalid @enderror @endif"
                                list="serviceTypeSuggestions"
                                maxlength="100"
                                placeholder="سباكة، كهرباء..."
                            >
                            @if(old('form_context') === 'create')
                                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                            <div class="form-text">يمكنك اختيار نوع موجود أو كتابة نوع جديد.</div>
                        </div>

                        <div class="col-12">
                            <label for="create-phone" class="form-label">رقم الهاتف للتواصل</label>
                            <input
                                id="create-phone"
                                type="tel"
                                name="phone"
                                value="{{ old('form_context') === 'create' ? old('phone') : '' }}"
                                class="form-control @if(old('form_context') === 'create') @error('phone') is-invalid @enderror @endif"
                                maxlength="20"
                                inputmode="tel"
                                autocomplete="tel"
                                dir="ltr"
                                placeholder="01X XXXX XXXX"
                            >
                            @if(old('form_context') === 'create')
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>

                        <div class="col-12">
                            <label for="create-notes" class="form-label">ملاحظات إضافية</label>
                            <textarea
                                id="create-notes"
                                name="notes"
                                class="form-control @if(old('form_context') === 'create') @error('notes') is-invalid @enderror @endif"
                                maxlength="2000"
                                rows="4"
                                placeholder="مواعيد العمل أو نطاق الخدمة أو أي تفاصيل مفيدة..."
                            >{{ old('form_context') === 'create' ? old('notes') : '' }}</textarea>
                            @if(old('form_context') === 'create')
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="service-modal__save">
                        <i class="bi bi-check2-circle"></i>
                        حفظ الخدمة
                    </button>
                    <button type="button" class="service-modal__cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade service-modal" id="editServiceModal" tabindex="-1" aria-labelledby="edit-service-title" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form id="edit-service-form" method="POST" action="">
                @csrf
                @method('PUT')
                <input type="hidden" name="form_context" value="edit">
                <input type="hidden" name="edit_action" id="edit-action" value="{{ old('edit_action') }}">

                <div class="modal-header">
                    <h2 class="modal-title" id="edit-service-title">
                        <span class="service-modal__icon"><i class="bi bi-pencil-square"></i></span>
                        تعديل بيانات الخدمة
                    </h2>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>

                <div class="modal-body">
                    @if($errors->any() && old('form_context') === 'edit')
                        <div class="alert alert-danger py-2 small" role="alert">
                            <i class="bi bi-exclamation-circle me-1"></i>
                            يرجى مراجعة الحقول الموضحة أدناه.
                        </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-md-7">
                            <label for="edit-name" class="form-label">اسم مقدم الخدمة <span class="text-danger">*</span></label>
                            <input id="edit-name" type="text" name="name" class="form-control @if(old('form_context') === 'edit') @error('name') is-invalid @enderror @endif" maxlength="255" required>
                            @if(old('form_context') === 'edit')
                                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                        <div class="col-md-5">
                            <label for="edit-type" class="form-label">نوع الخدمة</label>
                            <input id="edit-type" type="text" name="service_type" class="form-control @if(old('form_context') === 'edit') @error('service_type') is-invalid @enderror @endif" list="serviceTypeSuggestions" maxlength="100">
                            @if(old('form_context') === 'edit')
                                @error('service_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                            <div class="form-text">يمكنك اختيار نوع موجود أو كتابة نوع جديد.</div>
                        </div>
                        <div class="col-12">
                            <label for="edit-phone" class="form-label">رقم الهاتف للتواصل</label>
                            <input id="edit-phone" type="tel" name="phone" class="form-control @if(old('form_context') === 'edit') @error('phone') is-invalid @enderror @endif" maxlength="20" inputmode="tel" dir="ltr">
                            @if(old('form_context') === 'edit')
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                        <div class="col-12">
                            <label for="edit-notes" class="form-label">ملاحظات إضافية</label>
                            <textarea id="edit-notes" name="notes" class="form-control @if(old('form_context') === 'edit') @error('notes') is-invalid @enderror @endif" maxlength="2000" rows="4"></textarea>
                            @if(old('form_context') === 'edit')
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            @endif
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="service-modal__save">
                        <i class="bi bi-save2"></i>
                        حفظ التعديلات
                    </button>
                    <button type="button" class="service-modal__cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var editModalElement = document.getElementById('editServiceModal');
        var editForm = document.getElementById('edit-service-form');
        var editAction = document.getElementById('edit-action');
        var fields = {
            name: document.getElementById('edit-name'),
            serviceType: document.getElementById('edit-type'),
            phone: document.getElementById('edit-phone'),
            notes: document.getElementById('edit-notes')
        };

        function fillEditForm(data) {
            editForm.action = data.action || '';
            editAction.value = data.action || '';
            fields.name.value = data.name || '';
            fields.serviceType.value = data.serviceType || '';
            fields.phone.value = data.phone || '';
            fields.notes.value = data.notes || '';
        }

        document.querySelectorAll('[data-edit-service]').forEach(function (button) {
            button.addEventListener('click', function () {
                fillEditForm({
                    action: button.dataset.action,
                    name: button.dataset.name,
                    serviceType: button.dataset.serviceType,
                    phone: button.dataset.phone,
                    notes: button.dataset.notes
                });

                bootstrap.Modal.getOrCreateInstance(editModalElement).show();
            });
        });

        document.querySelectorAll('[data-confirm-delete]').forEach(function (form) {
            form.addEventListener('submit', function (event) {
                var serviceName = form.dataset.serviceName || 'هذه الخدمة';
                if (!window.confirm('هل أنت متأكد من حذف خدمة «' + serviceName + '»؟')) {
                    event.preventDefault();
                }
            });
        });

        @if($errors->any() && old('form_context') === 'create')
            bootstrap.Modal.getOrCreateInstance(document.getElementById('createServiceModal')).show();
        @elseif($errors->any() && old('form_context') === 'edit' && old('edit_action'))
            fillEditForm({
                action: @json(old('edit_action')),
                name: @json(old('name')),
                serviceType: @json(old('service_type')),
                phone: @json(old('phone')),
                notes: @json(old('notes'))
            });
            bootstrap.Modal.getOrCreateInstance(editModalElement).show();
        @endif
    });
</script>
@endsection
