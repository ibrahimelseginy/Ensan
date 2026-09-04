@extends('layouts.app')

@section('content')
<div class="donor-details-page">
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
                    <li class="breadcrumb-item"><a href="{{ route('website.donation-accounts.index') }}" class="text-primary text-decoration-none">سجلات الويب</a></li>
                    <li class="breadcrumb-item active" aria-current="page">تفاصيل المتبرع</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-person-badge me-2"></i> ملف المتبرع الرقمي 🔖
            </div>
            <h1 class="display-5 fw-800 text-dark mb-2">{{ $donor->name }}</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                استعراض سجل المعاملات المالية، تدقيق الإثباتات، وتأكيد عمليات الدعم المقدمة.
            </p>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            {{-- Donor Profile Card --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden sticky-top" style="top: 100px;">
                    <div class="p-4 border-bottom bg-stats-header">
                        <h6 class="mb-0 fw-bold text-stats-title"><i class="bi bi-info-circle-fill me-2 text-primary"></i> هويّة المتبرع</h6>
                    </div>
                    <div class="card-body p-4 bg-stats-card-main">
                        <div class="mb-4 text-center pb-4 border-bottom border-light-subtle">
                            <div class="avatar-large-circle bg-primary-light text-primary mx-auto mb-3 shadow-sm border border-white">
                                <i class="bi bi-person-fill"></i>
                            </div>
                            <h5 class="fw-bold text-stats-main mb-1">{{ $donor->name }}</h5>
                            <span class="text-muted-theme font-monospace x-small">{{ $donor->phone }}</span>
                        </div>

                        <div class="donor-stats-grid grid-2 gap-3 mb-4">
                            <div class="p-3 bg-stats-inner-item rounded-4 text-center border">
                                <span class="x-small text-muted-theme d-block mb-1">العمليات</span>
                                <span class="fw-bold text-stats-main">{{ $history->count() }}</span>
                            </div>
                            <div class="p-3 bg-stats-inner-item rounded-4 text-center border">
                                <span class="x-small text-muted-theme d-block mb-1">الموثقة</span>
                                <span class="fw-bold text-success-theme">{{ $history->where('status', 'verified')->count() }}</span>
                            </div>
                        </div>

                        <div class="p-4 bg-stats-highlight rounded-4 border border-primary border-opacity-10 shadow-sm">
                            <label class="x-small text-muted-theme fw-bold d-block mb-2 text-uppercase tracking-wider">إجمالي الدعم الموثق</label>
                            <div class="d-flex align-items-baseline gap-2">
                                <span class="display-6 fw-800 text-primary-theme">{{ number_format($history->where('status', 'verified')->sum('amount'), 2) }}</span>
                                <span class="fw-bold text-primary-theme opacity-75 small">ج.م</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Transactions History --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-slide-up">
                    <div class="p-4 border-bottom bg-white d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history me-2 text-warning"></i> سجل العمليات التفصيلي</h6>
                        <span class="badge bg-light text-muted border rounded-pill px-3 py-1 x-small fw-bold">{{ $history->count() }} سجل</span>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-end">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-muted x-small fw-bold">التاريخ</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold">المبلغ والقيمة</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold">نوع التبرع</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold text-center">الحالة</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold text-center">إثبات الدفع</th>
                                    <th class="px-4 py-3 text-muted x-small fw-bold text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody class="border-top-0">
                                @forelse($history as $donation)
                                    <tr>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold text-dark mb-0">{{ $donation->created_at->format('Y-m-d') }}</div>
                                            <div class="x-small text-muted">{{ $donation->created_at->format('H:i A') }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold text-dark">{{ number_format($donation->amount, 2) }} <span class="x-small fw-normal">ج.م</span></div>
                                            <div class="x-small text-muted">{{ $donation->payment_method_label }}</div>
                                        </td>
                                        <td class="px-4 py-3">
                                            <div class="fw-bold text-primary mb-0">{{ $donation->category_label }}</div>
                                            @if($donation->donationable)
                                                <div class="x-small text-muted text-truncate" style="max-width: 150px;">{{ $donation->donationable->title ?? $donation->donationable->name }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($donation->status == 'verified')
                                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1 x-small fw-bold">مؤكد ✅</span>
                                            @elseif($donation->status == 'pending')
                                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3 py-1 x-small fw-bold">قيد المراجعة ⏳</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3 py-1 x-small fw-bold">مرفوض ❌</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($donation->proof)
                                                <a href="{{ $donation->proof->image_url }}" target="_blank" class="btn btn-icon-light rounded-pill" title="عرض الإيصال">
                                                    <i class="bi bi-file-earmark-image"></i>
                                                </a>
                                            @else
                                                <i class="bi bi-dash-circle text-muted opacity-25"></i>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-center">
                                            @if($donation->status == 'pending')
                                                <div class="d-flex justify-content-center gap-2">
                                                    <form action="{{ route('website.donation-accounts.verify', $donation->id) }}" method="POST" onsubmit="return confirm('تأكيد استلام هذا التبرع؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icon-success rounded-pill" title="تأكيد">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    </form>
                                                    <form action="{{ route('website.donation-accounts.reject', $donation->id) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من رفض هذا التبرع؟')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-icon-danger rounded-pill" title="رفض">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-muted x-small">تمت المعالجة</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="opacity-25 mb-3">
                                                <i class="bi bi-inbox display-4"></i>
                                            </div>
                                            <h6 class="fw-bold text-muted">لا يوجد عمليات مسجلة لهذا المتبرع</h6>
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

<style>
    .donor-details-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .x-small { font-size: 0.75rem; }
    .max-w-600 { max-width: 600px; }
    .transition-all { transition: all 0.3s ease; }
    .grid-2 { display: grid; grid-template-columns: 1fr 1fr; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 50px 0 70px; 
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

    .avatar-large-circle {
        width: 80px;
        height: 80px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }

    .btn-icon-light {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #eee;
    }
    .btn-icon-light:hover { background: var(--primary-light); color: var(--primary); border-color: var(--primary-light); }

    .btn-icon-success {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0fff4;
        color: #22c55e;
        border: 1px solid #c6f6d5;
    }
    .btn-icon-success:hover { background: #22c55e; color: white; border-color: #22c55e; }

    .btn-icon-danger {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff5f5;
        color: #e03131;
        border: 1px solid #ffc9c9;
    }
    .btn-icon-danger:hover { background: #e03131; color: white; border-color: #e03131; }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .table thead th { border-bottom: none; }
    .table tbody td { border-bottom: 1px solid #f2f2f2; }
    .table-hover tbody tr:hover { background-color: rgba(34, 197, 94, 0.02); }

    /* Theme-Aware Stats Styling */
    .bg-stats-header { background-color: var(--gray-50); }
    .bg-stats-card-main { background-color: #ffffff; }
    .bg-stats-inner-item { background-color: var(--gray-50); }
    .bg-stats-highlight { background-color: rgba(34, 197, 94, 0.05); }
    .text-stats-title { color: var(--dark); }
    .text-stats-main { color: var(--dark); }
    .text-muted-theme { color: var(--gray-500); }
    .text-primary-theme { color: var(--primary); }
    .text-success-theme { color: var(--primary-dark); }

    body.theme-dark .bg-stats-header { background-color: rgba(255, 255, 255, 0.02); }
    body.theme-dark .bg-stats-card-main { background-color: var(--bg-card); }
    body.theme-dark .bg-stats-inner-item { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .bg-stats-highlight { background-color: rgba(34, 197, 94, 0.1); }
    body.theme-dark .text-stats-title { color: #ffffff; }
    body.theme-dark .text-stats-main { color: #ffffff; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .text-primary-theme { color: #34d399; }
    body.theme-dark .text-success-theme { color: #34d399; }
    body.theme-dark .table-hover tbody tr:hover { background-color: rgba(255, 255, 255, 0.02); }
</style>
@endsection
