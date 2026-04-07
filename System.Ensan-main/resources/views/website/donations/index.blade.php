@extends('layouts.app')

@section('content')
<div class="website-donations-index">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">سجلات تبرعات الموقع</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-wallet2 me-2"></i> سجلات التبرع الإلكتروني 💳
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">متبرعو الموقع الإلكتروني</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                متابعة وإدارة نشاط المتبرعين عبر المنصة، تدقيق المرفقات، وإحصائيات الدعم الرقمي.
            </p>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-slide-up">
            <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-activity me-2 text-primary"></i> النشاط المباشر للمتبرعين</h6>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-end">
                    <thead class="bg-light">
                        <tr>
                            <th class="px-4 py-3 text-muted x-small fw-bold">اسم المتبرع</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold">رقم الهاتف</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold text-center">عدد العمليات</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold text-center">إجمالي المساهمات</th>
                            <th class="px-4 py-3 text-muted x-small fw-bold text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($donors as $donor)
                            <tr>
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-circle-sm bg-primary bg-opacity-10 text-primary">
                                            <i class="bi bi-person-check-fill"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $donor->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span class="text-muted font-monospace x-small">{{ $donor->phone }}</span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span class="badge bg-light text-primary border rounded-pill px-3 py-1 x-small fw-bold">
                                        {{ $donor->web_donations_count }} مساهمة
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="fw-bold text-success">
                                        {{ number_format($donor->web_donations_sum_amount ?? 0, 2) }}
                                        <span class="x-small fw-normal">ج.م</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('website.donation-accounts.show', $donor) }}" class="btn btn-outline-primary rounded-pill px-4 fw-bold shadow-xs x-small transition-all">
                                        <i class="bi bi-receipt-cutoff me-1"></i> عرض السجل الكامل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5">
                                    <div class="opacity-25 mb-3">
                                        <i class="bi bi-clipboard2-x display-4"></i>
                                    </div>
                                    <h6 class="fw-bold text-muted">لا يوجد سجلات تبرع حالياً</h6>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($donors->hasPages())
                <div class="p-4 border-top bg-light">
                    {{ $donors->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .website-donations-index { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .max-w-600 { max-width: 600px; }
    .transition-all { transition: all 0.3s ease; }
    .shadow-xs { box-shadow: 0 2px 4px rgba(0,0,0,0.02) !important; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }

    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 18px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }

    .avatar-circle-sm {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .table thead th { border-bottom: none; }
    .table tbody td { border-bottom: 1px solid #f2f2f2; }
    .table-hover tbody tr:hover { background-color: rgba(34, 197, 94, 0.02); }
</style>
@endsection
