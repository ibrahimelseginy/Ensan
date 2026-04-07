@extends('layouts.app', ['hideGlobalAlerts' => true])

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --premium-primary: #e11d48;
        --premium-dark: #0f172a;
        --premium-slate: #1e293b;
        --premium-accent: #fbbf24;
    }

    .accounts-page {
        font-family: 'Tajawal', sans-serif;
        background-color: #020617;
        min-height: 100vh;
        color: #f8fafc;
    }

    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 60px;
        background: linear-gradient(135deg, #0f172a 0%, #020617 100%);
        overflow: hidden;
        border-bottom: 1px solid rgba(255,255,255,0.05);
    }

    .hero-bg-visuals {
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        z-index: 0;
    }

    .glow-orb-1 {
        position: absolute;
        width: 400px; height: 400px;
        top: -100px; right: -50px;
        filter: blur(120px);
        opacity: 0.15;
        border-radius: 50%;
    }

    .glow-orb-2 {
        position: absolute;
        width: 300px; height: 300px;
        bottom: -50px; left: -50px;
        filter: blur(100px);
        opacity: 0.1;
        border-radius: 50%;
    }

    .noise-overlay {
        position: absolute;
        inset: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
        opacity: 0.05;
        pointer-events: none;
    }

    .hero-content-wrapper { position: relative; z-index: 10; }

    .badge-glass-premium {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 8px 16px;
        border-radius: 100px;
        font-size: 0.85rem;
        color: var(--premium-accent);
        display: inline-flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .card-premium-dark {
        background: rgba(30, 41, 59, 0.7);
        backdrop-filter: blur(12px);
        border: 1px solid rgba(255, 255, 255, 0.08);
        border-radius: 24px;
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        overflow: hidden;
    }

    .table-dark-custom {
        --bs-table-bg: transparent;
        --bs-table-color: #f8fafc;
        margin-bottom: 0;
    }

    .table-dark-custom th {
        background: rgba(15, 23, 42, 0.5);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        padding: 18px 25px;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.8rem;
        letter-spacing: 0.05em;
        color: #94a3b8;
    }

    .table-dark-custom td {
        padding: 18px 25px;
        vertical-align: middle;
        border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .btn-premium-gradient {
        background: linear-gradient(135deg, var(--premium-primary) 0%, #be123c 100%);
        border: none;
        color: white;
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 600;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(225, 29, 72, 0.3);
    }

    .btn-premium-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(225, 29, 72, 0.4);
        color: white;
    }

    .animate-reveal-up {
        animation: revealUp 0.8s cubic-bezier(0.165, 0.84, 0.44, 1) forwards;
        opacity: 0;
    }

    @keyframes revealUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .search-input-premium {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px;
        color: white;
        padding: 12px 20px;
        transition: all 0.3s;
    }

    .search-input-premium:focus {
        background: rgba(15, 23, 42, 0.8);
        border-color: var(--premium-primary);
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.15);
        color: white;
        outline: none;
    }

    .modal-content-premium {
        background: var(--premium-dark);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        color: white;
    }

    .form-control-premium {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 12px;
        color: white !important;
        padding: 12px 15px;
    }

    .form-control-premium:focus {
        background: rgba(255,255,255,0.08);
        border-color: var(--premium-primary);
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.1);
        outline: none;
    }

    .input-group-premium {
        background: rgba(255,255,255,0.03);
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 14px;
        overflow: hidden;
        transition: all 0.3s;
        display: flex;
        align-items: center;
    }

    .input-group-premium:focus-within {
        border-color: var(--premium-primary);
        background: rgba(255,255,255,0.06);
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.1);
    }

    .input-group-premium .form-control {
        background: transparent !important;
        border: none !important;
        box-shadow: none !important;
        color: white !important;
        padding: 14px 18px;
    }

    .input-group-premium .input-group-text {
        background: transparent;
        border: none;
        color: #94a3b8;
        padding-left: 20px;
    }

    .premium-alert-glass {
        background: rgba(225, 29, 72, 0.05);
        border: 1px solid rgba(225, 29, 72, 0.2);
        border-radius: 18px;
        padding: 15px;
        color: #fca5a5;
    }

    /* SaaS Style Redesign */
    .saas-modal-content {
        background: #0f172a !important; /* Solid Dark */
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.7);
        overflow: hidden;
    }

    .saas-input-group {
        position: relative;
        display: flex;
        align-items: center;
        background: #0f172a; /* Solid Slate 900 */
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        transition: all 0.2s ease;
    }

    .saas-input-group:focus-within {
        border-color: var(--premium-primary);
        box-shadow: 0 0 0 4px rgba(225, 29, 72, 0.15);
        background: #1a1f2e;
    }

    .saas-form-control {
        background: transparent !important;
        border: none !important;
        color: white !important;
        padding: 14px 16px;
        font-size: 0.95rem;
        width: 100%;
    }

    .saas-input-icon {
        padding-left: 16px;
        color: #64748b; /* Slate 500 */
        font-size: 1.1rem;
    }

    .saas-label {
        font-weight: 600;
        color: #94a3b8; /* Slate 400 */
        margin-bottom: 8px;
        display: block;
        font-size: 0.85rem;
    }

    .saas-btn-primary {
        background: #e11d48;
        color: white;
        border: none;
        padding: 12px 24px;
        border-radius: 10px;
        font-weight: 700;
        transition: all 0.2s;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .saas-btn-primary:hover {
        background: #be123c;
        transform: translateY(-1px);
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.2);
    }

    .saas-info-card {
        background: #0f172a;
        border-left: 4px solid var(--premium-primary);
        padding: 16px;
        border-radius: 8px;
        margin-top: 24px;
    }

    /* Modal Backdrop Overlay */
    .modal-backdrop.show {
        opacity: 0.85;
        background-color: #020617;
    }
      
      
            
      

      

      /* --- SYSTEM LIGHT MODE PATCH --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .text-white, 
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white, 
      body:not(.theme-dark) .premium-hero-sleek .text-white-50,
      body:not(.theme-dark) .badge-glass-premium,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item a {
          color: #fff !important;
      }
      body:not(.theme-dark) .glass-card, 
      body:not(.theme-dark) .premium-modal-dark,
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .stats-card-dark,
      body:not(.theme-dark) .stats-inner-card,
      body:not(.theme-dark) .project-card-admin,
      body:not(.theme-dark) .campaign-card-lux,
      body:not(.theme-dark) .guest-card-lux,
      body:not(.theme-dark) .article-card-lux,
      body:not(.theme-dark) .message-card-lux,
      body:not(.theme-dark) .donation-card-lux,
      body:not(.theme-dark) .member-card-premium,
      body:not(.theme-dark) .partner-card-lux,
      body:not(.theme-dark) .leader-card-lux,
      body:not(.theme-dark) .empty-state-card-lux,
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content,
      body:not(.theme-dark) .categories-sidebar,
      body:not(.theme-dark) .sector-header,
      body:not(.theme-dark) .item-card,
      body:not(.theme-dark) .dark-glass-card {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .category-item {
          color: var(--ws-text-secondary);
          background: rgba(0,0,0,0.02);
      }
      body:not(.theme-dark) .category-item:hover { background: var(--ws-bg-page); color: var(--ws-text-primary); }
      body:not(.theme-dark) .category-item.active { background: var(--ws-bg-page); border-color: var(--ws-primary); color: var(--ws-text-primary); }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control, body:not(.theme-dark) .form-select, body:not(.theme-dark) .form-input-dark { 
          background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; 
      }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
      </style>

<div class="accounts-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #e11d48;"></div>
            <div class="glow-orb-2" style="background: #fbbf24;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('website.content') }}" class="text-white-50 decoration-none">الموقع الإلكتروني</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">حسابات المتبرعين</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-people me-2"></i> إدارة حسابات متبرعي الويب
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">حسابات تسجيل الدخول</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        عرض وإدارة حسابات المتبرعين المسجلين عبر الموقع الإلكتروني والتطبيق
                    </p>
                </div>
                <div class="col-lg-4 text-start animate-reveal-left">
                    <button class="btn btn-premium-gradient" data-bs-toggle="modal" data-bs-target="#createAccountModal">
                        <i class="bi bi-plus-lg me-2"></i> إنشاء حساب جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-5">
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 rounded-4 shadow-sm mb-4" role="alert">
                <div class="d-flex align-items-center">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>{{ Session::get('success') }}</div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card-premium-dark animate-reveal-up">
            <div class="card-header border-0 bg-transparent p-4">
                <div class="row align-items-center">
                    <div class="col">
                        <h5 class="mb-0 fw-bold">قائمة الحسابات</h5>
                    </div>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-dark-custom text-end">
                    <thead>
                        <tr>
                            <th>الاسم</th>
                            <th>رقم الهاتف</th>
                            <th>تاريخ التسجيل</th>
                            <th class="text-center">عدد التبرعات</th>
                            <th class="text-center">الإجمالي</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                            <tr>
                                <td>
                                    @php $displayName = $account->display_name ?? $account->name; @endphp
                                    <div class="d-flex align-items-center justify-content-end gap-3">
                                        <div class="text-end">
                                            <div class="fw-bold text-white mb-1">{{ $displayName }}</div>
                                            @if($account->is_temporary_name ?? (strpos($displayName, 'Donor') !== false))
                                                <span class="badge bg-warning-subtle text-warning rounded-pill x-small" style="font-size: 0.65rem; border: 1px solid rgba(255, 193, 7, 0.2);">حساب مؤقت</span>
                                            @else
                                                <span class="badge bg-success-subtle text-emerald rounded-pill x-small" style="font-size: 0.65rem; border: 1px solid rgba(16, 185, 129, 0.2);">حساب موثق</span>
                                            @endif
                                        </div>
                                        <div class="avatar-sm bg-premium-slate rounded-circle d-flex align-items-center justify-content-center border border-secondary shadow-sm" style="width: 42px; height: 42px;">
                                            <i class="bi bi-person text-white-50 fs-5"></i>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end">
                                        <code class="text-emerald bg-dark-subtle px-2 py-1 rounded border border-secondary">{{ $account->phone }}</code>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-end text-white-50 small">
                                        <i class="bi bi-calendar3 me-1 op-5"></i>
                                        {{ $account->created_at ? $account->created_at->format('Y-m-d') : '---' }}
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold" style="min-width: 60px;">
                                        {{ $account->total_donations_count ?? 0 }} <small class="op-7 fw-normal">تبرع</small>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <div class="fw-800 text-emerald fs-5">
                                        {{ number_format($account->total_donations_amount ?? 0, 2) }} 
                                        <small class="text-white-50 fw-normal fs-6 ms-1">ج.م</small>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        @if($account->donor_id)
                                            <a href="{{ route('website.donation-accounts.show', $account->donor_id) }}" class="btn btn-icon-premium p-0" title="سجل التبرعات" style="width: 34px; height: 34px;">
                                                <i class="bi bi-cash-stack text-info"></i>
                                            </a>
                                        @endif
                                        <button
                                            type="button"
                                            class="btn btn-icon-premium p-0"
                                            title="Edit Name"
                                            style="width: 34px; height: 34px;"
                                            data-bs-toggle="modal"
                                            data-bs-target="#editAccountModal"
                                            data-account-id="{{ $account->id }}"
                                            data-account-name="{{ $displayName }}"
                                            data-account-phone="{{ $account->phone }}"
                                        >
                                            <i class="bi bi-pencil-square text-warning"></i>
                                        </button>
                                        <form action="{{ route('website.accounts.destroy', $account->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب؟')" class="mb-0">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-icon-premium p-0" title="حذف" style="width: 34px; height: 34px;">
                                                <i class="bi bi-trash text-danger"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-white-50">
                                    <div class="d-flex flex-column align-items-center op-3">
                                        <i class="bi bi-people-fill display-1 mb-3"></i>
                                        <p class="fs-4">لا يوجد حسابات متبرعين حالياً</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($accounts->hasPages())
                <div class="card-footer border-0 bg-transparent p-4">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Create Account Modal (SaaS Style Redesign) -->
    <div class="modal fade" id="createAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content saas-modal-content border-0">
                <div class="modal-header border-0 p-5 pb-0">
                    <div class="text-end w-100">
                        <div class="d-inline-flex align-items-center justify-content-center bg-premium-primary rounded-circle mb-3" style="width: 54px; height: 54px; background: rgba(225, 29, 72, 0.1);">
                            <i class="bi bi-person-plus text-premium-primary fs-3"></i>
                        </div>
                        <h3 class="fw-800 text-white mb-2">إنشاء حساب متبرع جديد</h3>
                        <p class="text-white-50 mb-0">أدخل البيانات المطلوبة لتسجيل المتبرع في النظام</p>
                    </div>
                </div>
                
                <form action="{{ route('website.accounts.store') }}" method="POST">
                    @csrf
                    <div class="modal-body p-5 text-end">
                        <!-- Name Input -->
                        <div class="mb-4">
                            <label class="saas-label">الاسم الكامل</label>
                            <div class="saas-input-group">
                                <input type="text" name="name" class="saas-form-control text-end" placeholder="أدخل اسم المتبرع كاملاً..." required>
                                <div class="saas-input-icon"><i class="bi bi-person"></i></div>
                            </div>
                        </div>

                        <!-- Phone Input -->
                        <div class="mb-4">
                            <label class="saas-label">رقم الهاتف (للتواصل والدخول)</label>
                            <div class="saas-input-group">
                                <input type="text" name="phone" class="saas-form-control text-end" placeholder="010xxxxxxxx" required>
                                <div class="saas-input-icon"><i class="bi bi-phone"></i></div>
                            </div>
                        </div>

                        <!-- Email Input -->
                        <div class="mb-4">
                            <label class="saas-label">البريد الإلكتروني (اختياري)</label>
                            <div class="saas-input-group">
                                <input type="email" name="email" class="saas-form-control text-end" placeholder="example@mail.com">
                                <div class="saas-input-icon"><i class="bi bi-envelope"></i></div>
                            </div>
                        </div>

                        <!-- Info Card -->
                        <div class="saas-info-card d-flex align-items-start gap-3">
                            <div class="text-premium-primary mt-1">
                                <i class="bi bi-info-circle-fill fs-5"></i>
                            </div>
                            <div class="small leading-relaxed text-white-50">
                                <strong class="text-white">معلومة الدخول:</strong> سيتم تفعيل الدخول عبر رمز التحقق (OTP). لا يحتاج المتبرع لحفظ كلمة مرور معقدة؛ رقم الهاتف هو مفتاح حسابه.
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-5 pt-0 d-flex justify-content-between flex-row-reverse align-items-center">
                        <button type="submit" class="saas-btn-primary px-5">
                            تأكيد إنشاء الحساب
                        </button>
                        <button type="button" class="btn btn-link text-white-50 text-decoration-none fw-600 p-0" data-bs-dismiss="modal">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="modal fade" id="editAccountModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content saas-modal-content border-0">
                <div class="modal-header border-0 p-5 pb-0">
                    <div class="text-end w-100">
                        <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 54px; height: 54px; background: rgba(245, 158, 11, 0.12);">
                            <i class="bi bi-pencil-square text-warning fs-3"></i>
                        </div>
                        <h3 class="fw-800 text-white mb-2">تعديل اسم الحساب</h3>
                        <p class="text-white-50 mb-0">اكتبي الاسم الحقيقي للمتبرع بدل الاسم المؤقت.</p>
                    </div>
                </div>

                <form id="editAccountForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="modal-body p-5 text-end">
                        <div class="mb-4">
                            <label class="saas-label">الاسم الحقيقي</label>
                            <div class="saas-input-group">
                                <input id="editAccountName" type="text" name="name" class="saas-form-control text-end" required>
                                <div class="saas-input-icon"><i class="bi bi-person"></i></div>
                            </div>
                        </div>

                        <div class="mb-0">
                            <label class="saas-label">رقم الهاتف</label>
                            <div class="saas-input-group">
                                <input id="editAccountPhone" type="text" class="saas-form-control text-end" readonly>
                                <div class="saas-input-icon"><i class="bi bi-phone"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-0 p-5 pt-0 d-flex justify-content-between flex-row-reverse align-items-center">
                        <button type="submit" class="saas-btn-primary px-5">حفظ التعديل</button>
                        <button type="button" class="btn btn-link text-white-50 text-decoration-none fw-600 p-0" data-bs-dismiss="modal">
                            إلغاء
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editAccountModal = document.getElementById('editAccountModal');
    if (!editAccountModal) return;

    const form = document.getElementById('editAccountForm');
    const nameInput = document.getElementById('editAccountName');
    const phoneInput = document.getElementById('editAccountPhone');
    const updateBaseUrl = @json(url('admin/website/accounts'));

    editAccountModal.addEventListener('show.bs.modal', function (event) {
        const trigger = event.relatedTarget;
        if (!trigger) return;

        const accountId = trigger.getAttribute('data-account-id');
        nameInput.value = trigger.getAttribute('data-account-name') || '';
        phoneInput.value = trigger.getAttribute('data-account-phone') || '';
        form.action = `${updateBaseUrl}/${accountId}`;
    });
});
</script>
@endsection







