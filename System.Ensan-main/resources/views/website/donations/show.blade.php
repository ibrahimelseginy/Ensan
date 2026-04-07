@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="donation-mgmt-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #2563eb;"></div>
            <div class="glow-orb-2" style="background: #60a5fa;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('website.donation-accounts.index') }}" class="text-white-50 decoration-none">حسابات تبرعات الويبسايت</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">{{ $donor->name }}</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-person-fill me-2"></i> ملف المتبرع
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">{{ $donor->name }}</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        عرض كافة التبرعات التي قام بها المتبرع من خلال الموقع الإلكتروني.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="row g-4">
            {{-- Donor Info Card --}}
            <div class="col-lg-4">
                <div class="card dark-glass-card border-0 shadow-lg h-100">
                    <div class="card-header-lux">
                        <h5 class="mb-0 text-white fw-bold"><i class="bi bi-info-circle me-2 text-primary"></i> بيانات المتبرع</h5>
                    </div>
                    <div class="card-body-lux">
                        <div class="mb-4">
                            <label class="label-lux">الاسم</label>
                            <div class="field-value-lux">{{ $donor->name }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="label-lux">الهاتف</label>
                            <div class="field-value-lux">{{ $donor->phone }}</div>
                        </div>
                        <div class="mb-4">
                            <label class="label-lux">إجمالي العمليات</label>
                            <div class="field-value-lux">{{ $history->count() }} عملية</div>
                        </div>
                        <div class="mb-0">
                            <label class="label-lux">إجمالي المبالغ الموثقة</label>
                            <div class="field-value-lux text-emerald fs-4 fw-bold">{{ number_format($history->where('status', 'verified')->sum('amount'), 2) }} ج.م</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- History Table --}}
            <div class="col-lg-8">
                <div class="card dark-glass-card border-0 shadow-lg">
                    <div class="card-header-lux d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 text-white fw-bold"><i class="bi bi-clock-history me-2 text-warning"></i> سجل العمليات</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0" style="color: #e2e8f0;">
                                <thead style="background: rgba(255,255,255,0.03);">
                                    <tr>
                                        <th class="px-4 py-3 border-0 text-end">التاريخ</th>
                                        <th class="px-4 py-3 border-0 text-end">المبلغ</th>
                                        <th class="px-4 py-3 border-0 text-end">النوع</th>
                                        <th class="px-4 py-3 border-0 text-end">طريقة الدفع</th>
                                        <th class="px-4 py-3 border-0 text-center">الحالة</th>
                                        <th class="px-4 py-3 border-0 text-center">الإثبات</th>
                                        <th class="px-4 py-3 border-0 text-center">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($history as $donation)
                                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                        <td class="px-4 py-3 text-end">{{ $donation->created_at->format('Y-m-d H:i') }}</td>
                                        <td class="px-4 py-3 text-end fw-bold">{{ number_format($donation->amount, 2) }} ج.م</td>
                                        <td class="px-4 py-3 text-end">
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $donation->category_label }}</span>
                                                @if($donation->donationable)
                                                    <span class="x-small text-white-50">{{ $donation->donationable->title ?? $donation->donationable->name }}</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td class="px-4 py-3 text-end small">{{ $donation->payment_method_label }}</td>
                                        <td class="px-4 py-3 text-center">
                                            @if($donation->status == 'verified')
                                                <span class="badge bg-success-glass text-emerald rounded-pill px-3">مؤكد</span>
                                            @elseif($donation->status == 'pending')
                                                <span class="badge bg-warning-glass text-warning rounded-pill px-3">قيد المراجعة</span>
                                            @else
                                                <span class="badge bg-danger-glass text-danger rounded-pill px-3">مرفوض</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($donation->proof)
                                                <a href="{{ $donation->proof->image_url }}" target="_blank" class="btn btn-outline-info btn-sm rounded-pill px-3">
                                                    <i class="bi bi-image me-1"></i> عرض
                                                </a>
                                            @else
                                                <span class="text-muted x-small">لا يوجد</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($donation->status == 'pending')
                                                <div class="d-flex justify-content-center gap-2">
                                                    <form action="{{ route('website.donation-accounts.verify', $donation->id) }}" method="POST" onsubmit="return confirm('تأكيد استلام هذا التبرع؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-emerald-glass btn-sm rounded-pill px-3" title="تأكيد">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('website.donation-accounts.reject', $donation->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض هذا التبرع؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-danger-glass btn-sm rounded-pill px-3" title="رفض">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-white-50 small">---</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --dark-bg: #0b0e14;
        --card-dark: #1a1f2e;
        --slate-400: #94a3b8;
    }

    .btn-emerald-glass { background: rgba(16,185,129,0.1); border: 1px solid rgba(16,185,129,0.2); color: #34d399; transition: all 0.3s; }
    .btn-emerald-glass:hover { background: rgba(16,185,129,0.25); color: #6ee7b7; transform: translateY(-2px); }
    
    .btn-danger-glass { background: rgba(239,68,68,0.1); border: 1px solid rgba(239,68,68,0.2); color: #f87171; transition: all 0.3s; }
    .btn-danger-glass:hover { background: rgba(239,68,68,0.25); color: #fca5a5; transform: translateY(-2px); }

    .donation-mgmt-page { min-height: 100vh; background-color: var(--dark-bg); }

    .premium-hero-sleek { position: relative; padding: 60px 0 80px; border-radius: 0 0 30px 30px; overflow: hidden; z-index: 10; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.1; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3BaseFilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/baseFilter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #bfdbfe; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }

    .dark-glass-card { background: var(--card-dark); border-radius: 20px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); }
    .card-header-lux { padding: 20px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .card-body-lux { padding: 20px; }
    .label-lux { color: var(--slate-400); font-weight: 700; font-size: 0.8rem; margin-bottom: 5px; display: block; }
    .field-value-lux { color: #f8fafc; font-weight: 600; }
    
    .bg-success-glass { background: rgba(16,185,129,0.15); }
    .bg-warning-glass { background: rgba(245,158,11,0.15); }
    .bg-danger-glass { background: rgba(239,68,68,0.15); }
    .text-emerald { color: #34d399 !important; }

    .x-small { font-size: 0.7rem; }
    .animate-reveal-right { animation: revealRight 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- GLOBAL LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .text-white, body:not(.theme-dark) .text-white-50 { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .premium-hero-sleek .text-white, body:not(.theme-dark) .premium-hero-sleek .text-white-50 { color: #fff !important; }
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
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 4px 6px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control { background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
</style>
@endsection



