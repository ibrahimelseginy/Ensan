@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="donation-mgmt-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #064e3b 0%, #059669 100%);">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #10b981;"></div>
            <div class="glow-orb-2" style="background: #34d399;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">حسابات تبرعات الويبسايت</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-wallet2 me-2"></i> تبرعات الموقع الإلكتروني
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">حسابات متبرعي الويب</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        متابعة سجلات التبرعات القادمة عبر الموقع الإلكتروني والتحقق من المرفقات.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        <div class="card dark-glass-card border-0 shadow-lg">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0" style="color: #e2e8f0;">
                        <thead style="background: rgba(255,255,255,0.03);">
                            <tr>
                                <th class="px-4 py-3 border-0 text-end">اسم المتبرع</th>
                                <th class="px-4 py-3 border-0 text-end">رقم الهاتف</th>
                                <th class="px-4 py-3 border-0 text-end">عدد التبرعات</th>
                                <th class="px-4 py-3 border-0 text-end">إجمالي التبرعات (الموثقة)</th>
                                <th class="px-4 py-3 border-0 text-center">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donors as $donor)
                            <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                <td class="px-4 py-3 text-end fw-bold">{{ $donor->name }}</td>
                                <td class="px-4 py-3 text-end">{{ $donor->phone }}</td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3">{{ $donor->web_donations_count }}</span>
                                </td>
                                <td class="px-4 py-3 text-end text-emerald fw-bold">
                                    {{ number_format($donor->web_donations_sum_amount ?? 0, 2) }} ج.م
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('website.donation-accounts.show', $donor) }}" class="btn btn-glass-purple btn-sm rounded-pill px-3">
                                        <i class="bi bi-eye-fill me-1"></i> سجل التبرعات
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="py-5 text-center text-muted">
                                    <i class="bi bi-inbox display-4 d-block mb-3 opacity-25"></i>
                                    لا توجد تبرعات مسجلة من الموقع حالياً
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="p-4 d-flex justify-content-center">
                    {{ $donors->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    :root {
        --emerald-primary: #10b981;
        --dark-bg: #0b0e14;
        --card-dark: #1a1f2e;
        --slate-900: #0f172a;
        --slate-400: #94a3b8;
    }

    .donation-mgmt-page { min-height: 100vh; background-color: var(--dark-bg); }

    .premium-hero-sleek { position: relative; padding: 80px 0 100px; border-radius: 0 0 40px 40px; overflow: hidden; z-index: 10; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.1; background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3BaseFilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/baseFilter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E"); }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #d1fae5; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }

    .dark-glass-card { background: var(--card-dark); border-radius: 24px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); }
    .btn-glass-purple { background: rgba(124,58,237,0.15); color: #a78bfa; border: 1px solid rgba(124,58,237,0.3); transition: 0.3s; }
    .btn-glass-purple:hover { background: #7c3aed; color: white; transform: translateY(-2px); }
    .text-emerald { color: #34d399 !important; }

    .animate-reveal-right { animation: revealRight 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
      
      
            
      

      

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
@endsection







