@extends('layouts.app')

@section('title', 'م/ إبراهيم السجيني — رائد أعمال ومؤسس فرصة Dev')

@section('content')
<style>
.dev-quote-box {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(6, 182, 212, 0.08));
    border: 1px solid rgba(16, 185, 129, 0.25);
    border-radius: 20px;
    padding: 1.5rem;
    position: relative;
}
.theme-dark .dev-quote-box,
[data-bs-theme="dark"] .dev-quote-box {
    background: linear-gradient(135deg, rgba(16, 185, 129, 0.12), rgba(30, 41, 59, 0.8));
    border-color: rgba(16, 185, 129, 0.35);
}
.dev-point-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 1.25rem 1.4rem;
    transition: all 0.25s ease;
}
.theme-dark .dev-point-card,
[data-bs-theme="dark"] .dev-point-card {
    background: #1e293b;
    border-color: #334155;
}
.dev-point-card:hover {
    transform: translateY(-2px);
    border-color: #10b981;
    box-shadow: 0 8px 24px rgba(16, 185, 129, 0.12);
}
.dev-avatar-img {
    width: 140px;
    height: 140px;
    object-fit: cover;
    object-position: center 15%;
    border-radius: 50%;
    border: 4px solid #10b981;
    box-shadow: 0 10px 25px rgba(16, 185, 129, 0.35);
}
.company-logo-badge {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    border-radius: 999px;
    padding: 0.35rem 0.9rem;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}
.theme-dark .company-logo-badge,
[data-bs-theme="dark"] .company-logo-badge {
    background: #1e293b;
    border-color: #334155;
    color: #f8fafc;
}
</style>

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
                        {{-- Avatar with Real Photo --}}
                        <div class="position-relative d-inline-block mb-3">
                            <img src="{{ asset('images/ibrahim_elseginy.jpg') }}" alt="م/ إبراهيم السجيني" class="dev-avatar-img">
                            <span class="position-absolute bottom-0 start-0 translate-middle p-2 bg-success border border-white rounded-circle shadow-sm" 
                                  title="متاح للمشاريع والاستشارات" style="width: 20px; height: 20px;">
                            </span>
                        </div>

                        {{-- Name & Title --}}
                        <h4 class="fw-bold mb-1 text-main">م/ إبراهيم السجيني</h4>
                        <div class="text-primary fw-bold mb-2">@Ibrahim Elseginy</div>

                        {{-- Company Badge with Official Logo --}}
                        <div class="mb-3">
                            <div class="company-logo-badge">
                                <img src="{{ asset('images/forsah_dev_logo.png') }}" alt="فرصة Dev" style="height: 24px; width: auto; object-fit: contain;">
                                <span class="fw-bold small">شركة فرصة Dev</span>
                            </div>
                        </div>

                        <p class="text-muted small mb-3">
                            رائد أعمال &bull; صاحب ومؤسس شركة فرصة Dev
                            <br>
                            Entrepreneur & Founder of Forsah Dev
                        </p>

                        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill bg-success-subtle text-success small fw-semibold mb-4 border border-success-subtle">
                            <span class="spinner-grow spinner-grow-sm text-success" role="status" style="width: 8px; height: 8px;"></span>
                            مطور ومبرمج منظومة إنسان الخيرية
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
                        <a href="https://wa.me/201556592017?text={{ urlencode('مرحباً م. إبراهيم، بخصوص شركة فرصة Dev ومنظومة إنسان...') }}" 
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
                        <i class="bi bi-geo-alt me-1 text-primary"></i> جمهورية مصر العربية | متاح للمشاريع والحلول البرمجية
                    </div>
                </div>
            </div>
        </div>

        {{-- Story & Bio Card (Left Column) --}}
        <div class="col-lg-8 col-md-7">
            <div class="card border rounded-4 shadow-sm h-100">
                <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="p-2 rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center" style="width: 44px; height: 44px;">
                            <i class="bi bi-heart-pulse-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-0 text-main">كلمة ورسالة من القلب</h5>
                            <small class="text-muted">م/ إبراهيم السجيني — بمناسبة اكتمال منظومة إنسان الخيرية</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <img src="{{ asset('images/forsah_dev_logo.png') }}" alt="فرصة Dev" style="height: 28px; width: auto;">
                        <span class="badge bg-success-subtle text-success fw-bold px-3 py-2 rounded-pill">
                            <i class="bi bi-check2-circle me-1"></i> إنجاز نفتخر به
                        </span>
                    </div>
                </div>

                <div class="card-body p-4 d-flex flex-column justify-content-between">
                    <div class="d-flex flex-column gap-3">
                        
                        {{-- Main Highlight Quote --}}
                        <div class="dev-quote-box">
                            <div class="d-flex align-items-center gap-2 mb-2 text-success fw-bold">
                                <i class="bi bi-quote fs-3 lh-1"></i>
                                <span class="fs-5">"الحمد لله حمداً كثيراً 💚"</span>
                            </div>
                            <p class="fs-6 fw-bold text-main mb-0" style="line-height: 1.8;">
                                محدش متخيل الفرحة الكبيرة اللي حاسس بيها دلوقتي بانتهاء منظومة وتطبيق إنسان 💚، السيستم السحابي المتكامل، والويبسايت الرسمي.
                            </p>
                        </div>

                        {{-- Point 1: الحمد والشكر --}}
                        <div class="dev-point-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded-circle bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-stars fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-success mb-1">أولاً: شكر وحمد لله عز وجل</h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.98rem; line-height: 1.8;">
                                        أول حاجة بحمد ربنا سبحانه وتعالى من كل قلبي على العمل الخيري ده إنه اكتمل.. الحمد لله حمداً كثيراً مباركاً فيه 🥹💚
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Point 2: الفخر بالسعادة وتوظيف العلم في الخير --}}
                        <div class="dev-point-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-mortarboard-fill fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-primary mb-1">ثانياً: فخر وسعادة بتوظيف العلم في خدمة الخير</h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.98rem; line-height: 1.8;">
                                        ثاني حاجة كنت مبسوط أوي أوي أوي في كل مرحلة وكل لحظة اشتغلت فيها على التطبيق والمنظومة، وفخور بيها جداً علشان وظّفت تعليمي ودراستي وخبرتي في عمل خيري وللمؤسسة بالأخص 💚💚💚
                                    </p>
                                </div>
                            </div>
                        </div>

                        {{-- Point 3: ترك البصمة والأثر الطيب --}}
                        <div class="dev-point-card">
                            <div class="d-flex align-items-start gap-3">
                                <div class="p-2 rounded-circle bg-warning-subtle text-warning d-flex align-items-center justify-content-center flex-shrink-0" style="width: 38px; height: 38px;">
                                    <i class="bi bi-award-fill fs-6"></i>
                                </div>
                                <div>
                                    <h6 class="fw-bold text-warning-emphasis mb-1">ثالثاً: بصمة وأثر طيب يدوم للمؤسسة</h6>
                                    <p class="mb-0 text-secondary" style="font-size: 0.98rem; line-height: 1.8;">
                                        ثالث حاجة حبيت أسيب بصمة ليا في المؤسسة تكون معلّمة ومحفورة مع كل اللي موجودين، علشان لو الدنيا لاهتني عن المؤسسة أكون الحمد لله عملت حاجة أكون فخور بيها في يوم من الأيام ومبسوط بيها 💚💚
                                    </p>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Card Footer Contact CTA --}}
                    <div class="border-top pt-3 mt-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <img src="{{ asset('images/forsah_dev_logo.png') }}" alt="فرصة Dev" style="height: 24px; width: auto;">
                            <span class="text-muted small fw-semibold">شركة فرصة Dev — ريادة الأعمال والحلول الرقمية المتطورة</span>
                        </div>
                        <div class="d-flex gap-2">
                            <a href="https://wa.me/201556592017" target="_blank" rel="noopener" class="btn btn-sm btn-success fw-bold px-3 rounded-pill shadow-sm">
                                <i class="bi bi-whatsapp me-1"></i> تواصل عبر واتساب
                            </a>
                            <a href="tel:01069388721" class="btn btn-sm btn-primary fw-bold px-3 rounded-pill shadow-sm">
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
