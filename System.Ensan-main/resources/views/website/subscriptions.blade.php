@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="contact-messages-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek">
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
                            <li class="breadcrumb-item active text-white" aria-current="page">النشرة الإخبارية</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-envelope-paper ms-2"></i> المشتركون
                        </div>
                    </div>
                    <h1 class="display-3 fw-800 text-white mb-3 text-end">النشرة الإخبارية</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        إدارة البريد الإلكتروني للمشتركين في النشرة الإخبارية من زوار الموقع وإرسال التحديثات.
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="container-fluid py-5">
        <div class="row g-4">
            {{-- Messages Table --}}
            <div class="col-12">
                <div class="premium-card-dark animate-up">
                    <div class="card-header-lux d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="fw-bold mb-0">قائمة المشتركين</h5>
                            <div class="header-icon bg-emerald-500 ms-3"><i class="bi bi-people-fill"></i></div>
                        </div>
                    </div>
                    
                    <div class="card-body-lux p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 premium-table-dark">
                                <thead>
                                    <tr>
                                        <th class="ps-4">البريد الإلكتروني</th>
                                        <th>حالة الاشتراك</th>
                                        <th>تاريخ الاشتراك</th>
                                        <th class="text-center">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscriptions as $sub)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar-mini bg-emerald-900 text-emerald-400">
                                                    <i class="bi bi-envelope-at-fill"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-white mb-1">{{ $sub->email }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @if($sub->is_active)
                                                <span class="badge-status approved">نشط</span>
                                            @else
                                                <span class="badge-status pending">غير نشط</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="small text-slate-400" dir="ltr">
                                                <i class="bi bi-calendar-event ms-1"></i>
                                                {{ $sub->created_at->format('Y/m/d - h:i A') }}
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('website.subscriptions.destroy', $sub) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-glass-danger btn-sm py-2 px-3 rounded-pill">
                                                        حذف <i class="bi bi-trash ms-1"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4">
                                            <div class="empty-state-card-lux py-5 mt-5">
                                                <div class="empty-visual-wrapper mx-auto">
                                                    <div class="glow-pulse"></div>
                                                    <i class="bi bi-envelope-x empty-icon-vibe"></i>
                                                </div>
                                                <h5 class="fw-bold text-white mt-4">لا يوجد مشتركون حالياً</h5>
                                                <p class="text-slate-400">لا يوجد أي عناوين بريد إلكتروني مسجلة في النشرة الإخبارية بعد.</p>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
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
        --card-dark: #1a2332;
        --emerald-500: #10b981;
        --emerald-600: #059669;
        --indigo-500: #6366f1;
        --indigo-600: #4f46e5;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
    }

    body { background-color: var(--dark-bg) !important; font-family: 'Tajawal', sans-serif; }

    /* Hero Styling */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 120px;
        background: linear-gradient(135deg, #0f172a 0%, #064e3b 100%);
        border-radius: 0 0 60px 60px;
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.3; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; background-image: url('data:image/svg+xml,...'); }

    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { 
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px 18px; border-radius: 100px; color: #a7f3d0; font-weight: 700; font-size: 0.85rem;
    }

    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }

    /* Action Buttons */
    .btn-glass-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
        backdrop-filter: blur(8px);
        font-weight: 700;
        transition: 0.4s;
    }
    .btn-glass-danger:hover { background: rgba(239, 68, 68, 0.2); color: white; transform: translateY(-3px); }

    /* Cards */
    .premium-card-dark {
        background: var(--card-dark);
        border-radius: 35px;
        border: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }
    .card-header-lux { padding: 25px 30px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.01); }
    .header-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; }
    .bg-emerald-500 { background: var(--emerald-500); }

    /* Tables */
    .premium-table-dark { background: transparent; }
    .premium-table-dark thead th { 
        background: rgba(0,0,0,0.2); color: var(--slate-400); 
        text-transform: uppercase; font-size: 0.75rem; font-weight: 800; border: none; padding: 20px;
    }
    .premium-table-dark tbody td { border-bottom: 1px solid rgba(255,255,255,0.03); padding: 20px; color: #f8fafc; }

    .user-avatar-mini {
        width: 45px; height: 45px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem;
    }
    .bg-emerald-900 { background: #064e3b; }
    .text-emerald-400 { color: #34d399; }

    /* Status Badges */
    .badge-status { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; border: 1px solid transparent; }
    .badge-status.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
    .badge-status.approved { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }

    /* Empty State */
    .empty-state-card-lux { text-align: center; }
    .empty-visual-wrapper { position: relative; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    .empty-icon-vibe { font-size: 3rem; color: var(--slate-600); position: relative; z-index: 5; }
    .glow-pulse {
        position: absolute; inset: 0; background: var(--emerald-500); border-radius: 50%;
        filter: blur(20px); opacity: 0.2; animation: pulseGlow 3s infinite;
    }
    @keyframes pulseGlow { 0% { opacity: 0.1; transform: scale(0.8); } 50% { opacity: 0.3; transform: scale(1.2); } 100% { opacity: 0.1; transform: scale(0.8); } }

    /* General Utilities */
    .x-small { font-size: 0.75rem; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(35px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 991px) {
        .premium-hero-sleek { padding: 60px 0 80px; border-radius: 0 0 35px 35px; }
        .display-3 { font-size: 2.2rem; }
        .text-end { text-align: center !important; }
        .justify-content-end { justify-content: center !important; }
        .ms-auto.me-0 { margin: 0 auto !important; }
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
@endsection







