@extends('layouts.app')

@section('title', 'م/ إبراهيم السجيني — رائد أعمال ومؤسس فرصة Dev')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">
    
    {{-- Page Header --}}
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item">
                        <a href="{{ auth()->check() ? route('dashboard.index') : route('login') }}" class="text-decoration-none text-muted">
                            <i class="bi bi-house-door me-1"></i> {{ auth()->check() ? 'الرئيسية' : 'تسجيل الدخول' }}
                        </a>
                    </li>
                    <li class="breadcrumb-item active text-primary fw-semibold" aria-current="page">معلومات المطور</li>
                </ol>
            </nav>
            <h3 class="fw-bold mb-0 text-main d-flex align-items-center gap-2">
                <i class="bi bi-person-workspace text-primary"></i>
                <span>م/ إبراهيم السجيني</span>
            </h3>
        </div>

        <div>
            @if(auth()->check())
                <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-arrow-right me-1"></i> العودة للرئيسية
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                    <i class="bi bi-box-arrow-in-right me-1"></i> تسجيل الدخول
                </a>
            @endif
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="row g-4">
        
        {{-- Profile Summary Card (Right Column) --}}
        <div class="col-lg-4 col-md-5">
            <div class="card border rounded-4 shadow-sm h-100 text-center p-4">
                <div class="card-body p-0 d-flex flex-column justify-content-between">
                    <div>
                        {{-- Avatar --}}
                        <div class="position-relative d-inline-block mb-3">
                            <div class="rounded-circle shadow d-flex align-items-center justify-content-center mx-auto" 
                                 style="width: 120px; height: 120px; background: linear-gradient(135deg, #1e40af, #3b82f6); color: #ffffff; font-size: 2.75rem; font-weight: 800; border: 4px solid var(--border);">
                                <span>IE</span>
                            </div>
                            <span class="position-absolute bottom-0 start-0 translate-middle p-2 bg-success border border-white rounded-circle" 
                                  title="متاح للمشاريع والاستشارات" style="width: 18px; height: 18px;">
                            </span>
                        </div>

                        {{-- Name & Title --}}
                        <h4 class="fw-bold mb-1 text-main">م/ إبراهيم السجيني</h4>
                        <div class="text-primary fw-bold mb-2">@Ibrahim Elseginy</div>
                        <p class="text-muted small mb-3">
                            رائد أعمال &bull; صاحب ومؤسس شركة فرصة Dev
                            <br>
                            Entrepreneur & Founder of Forsah Dev
                        </p>

                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-subtle text-success small fw-semibold mb-4 border border-success-subtle">
                            <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
                            صاحب ومؤسس شركة فرصة Dev
                        </div>
                    </div>

                    {{-- Contact Actions --}}
                    <div class="d-flex flex-column gap-2 pt-2 border-top">
                        <!-- Direct Phone Call -->
                        <a href="tel:01069388721" class="btn btn-primary d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 fw-bold shadow-sm">
                            <i class="bi bi-telephone-fill"></i>
                            <span>اتصال هاتفي مباشر (01069388721)</span>
                        </a>

                        <!-- WhatsApp -->
                        <a href="https://wa.me/201556592017?text={{ urlencode('مرحباً م. إبراهيم، بخصوص شركة فرصة Dev...') }}" 
                           target="_blank" rel="noopener" 
                           class="btn btn-success d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 fw-bold shadow-sm">
                            <i class="bi bi-whatsapp"></i>
                            <span>مراسلة عبر واتساب (01556592017)</span>
                        </a>

                        <!-- Copy Number -->
                        <button type="button" class="btn btn-outline-secondary d-flex align-items-center justify-content-center gap-2 py-2 rounded-3 fw-semibold" 
                                onclick="copyPhoneNumber('01069388721')">
                            <i class="bi bi-clipboard"></i>
                            <span>نسخ رقم الاتصال (01069388721)</span>
                        </button>
                    </div>

                    <div class="text-muted small pt-3 mt-3 border-top">
                        <i class="bi bi-geo-alt me-1 text-primary"></i> جمهورية مصر العربية | متاح للمشاريع السحابية
                    </div>
                </div>
            </div>
        </div>

        {{-- Story & Bio Card (Left Column) --}}
        <div class="col-lg-8 col-md-7">
            <div class="card border rounded-4 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center gap-3">
                    <div class="p-2 rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                        <i class="bi bi-rocket-takeoff-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-main">القصة والمسيرة الريادية</h5>
                        <small class="text-muted">م/ إبراهيم السجيني — رائد أعمال وصاحب ومؤسس شركة فرصة Dev</small>
                    </div>
                </div>

                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div>
                        <div class="p-4 rounded-4 mb-4 border" style="background: rgba(37, 99, 235, 0.04); border-color: rgba(37, 99, 235, 0.15) !important;">
                            <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-briefcase-fill"></i>
                                <span>عن م/ إبراهيم السجيني وشركة فرصة Dev</span>
                            </h5>
                            <p class="text-secondary leading-relaxed mb-3" style="font-size: 1.05rem; line-height: 1.9;">
                                رحلة شغف وابتكار تقني في عالم التكنولوجيا وريادة الأعمال. تأسست رؤية <strong>م/ إبراهيم السجيني</strong> على الإيمان العميق بأن الأنظمة والبرمجيات الذكية هي الأساس الحقيقي لتمكين المؤسسات والشركات من إطلاق كامل إمكاناتها وتحقيق أعلى معدلات النجاح والنمو المؤسسي.
                            </p>
                            <p class="text-secondary leading-relaxed mb-0" style="font-size: 1.02rem; line-height: 1.9;">
                                ومن خلال تأسيس وقيادة <strong>شركة فرصة Dev</strong>، يعمل م/ إبراهيم على هندسة وتطوير حلول ومنصات سحابية نوعية تجمع بين المعمارية البرمجية الصلبة، والأمان العالي، وسهولة الاستخدام الفائقة، لتكون لكل فكرة ومؤسسة فرصة حقيقية للريادة والتميز الرقمي في سوق الأعمال.
                            </p>
                        </div>
                    </div>

                    {{-- Card Footer Contact CTA --}}
                    <div class="border-top pt-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span class="text-muted small">لأي استفسار أو شراكات عمل وتطوير أنظمة سحابية:</span>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/201556592017" target="_blank" rel="noopener" class="btn btn-sm btn-success fw-bold px-3 rounded-pill">
                                <i class="bi bi-whatsapp me-1"></i> تواصل عبر واتساب
                            </a>
                            <a href="tel:01069388721" class="btn btn-sm btn-primary fw-bold px-3 rounded-pill">
                                <i class="bi bi-telephone me-1"></i> اتصال مباشر
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

{{-- Copy Toast Notification --}}
<div class="position-fixed bottom-0 start-50 translate-middle-x p-3" style="z-index: 99999;">
    <div id="copyToast" class="toast align-items-center text-white bg-dark border-0 rounded-pill px-3 py-2 shadow-lg" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill text-success fs-5"></i>
            <div class="toast-body p-0 fw-semibold">تم نسخ رقم الاتصال (01069388721) بنجاح!</div>
        </div>
    </div>
</div>

<script>
function copyPhoneNumber(number) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(number).then(() => {
            triggerToast();
        }).catch(() => {
            fallbackCopy(number);
        });
    } else {
        fallbackCopy(number);
    }
}

function fallbackCopy(text) {
    const tempInput = document.createElement("input");
    tempInput.value = text;
    document.body.appendChild(tempInput);
    tempInput.select();
    document.execCommand("copy");
    document.body.removeChild(tempInput);
    triggerToast();
}

function triggerToast() {
    const toastEl = document.getElementById('copyToast');
    if (window.bootstrap && window.bootstrap.Toast) {
        const toast = new bootstrap.Toast(toastEl, { delay: 2500 });
        toast.show();
    } else {
        toastEl.classList.add('show');
        setTimeout(() => toastEl.classList.remove('show'), 2500);
    }
}
</script>
@endsection
