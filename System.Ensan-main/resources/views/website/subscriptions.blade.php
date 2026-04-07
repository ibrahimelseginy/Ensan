@extends('layouts.app')

@section('content')
<div class="subscriptions-mgmt-page">
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
                    <li class="breadcrumb-item active" aria-current="page">النشرة الإخبارية</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-envelope-paper me-2"></i> المشتركون والبريد
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">النشرة الإخبارية</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                إدارة البريد الإلكتروني للمشتركين في النشرة الإخبارية من زوار الموقع وإرسال التحديثات الدورية.
            </p>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            {{-- Subscribers Table --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm animate-slide-up overflow-hidden" style="border-radius: 24px;">
                    <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon-small bg-primary"><i class="bi bi-people-fill"></i></div>
                            <h5 class="fw-bold mb-0 text-dark">قائمة المشتركين الحاليين</h5>
                        </div>
                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold small">{{ $subscriptions->count() }} مشترك</span>
                    </div>
                    
                    <div class="card-body p-0 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="x-small text-uppercase fw-bold text-muted">
                                        <th class="ps-4 py-3 border-0">البريد الإلكتروني</th>
                                        <th class="py-3 border-0 text-center">حالة الاشتراك</th>
                                        <th class="py-3 border-0 text-center">تاريخ الانضمام</th>
                                        <th class="py-3 border-0 text-center pe-4">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subscriptions as $sub)
                                    <tr class="transition-all">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-soft bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px;">
                                                    <i class="bi bi-envelope-at"></i>
                                                </div>
                                                <div>
                                                    <div class="text-dark fw-bold mb-0 lh-1">{{ $sub->email }}</div>
                                                    <div class="x-small text-muted mt-1">مشترك عبر الواجهة الرئيسية</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            @if($sub->is_active)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">نشط</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">غير نشط</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center">
                                            <div class="x-small text-muted" dir="ltr">
                                                <i class="bi bi-calendar-event me-1"></i>
                                                {{ $sub->created_at->format('Y/m/d - h:i A') }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <form action="{{ route('website.subscriptions.destroy', $sub) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاشتراك؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-light text-danger border rounded-pill px-3 transition-all hover-danger">
                                                        حذف الاشتراك <i class="bi bi-trash ms-1"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <div class="py-5 opacity-25">
                                                <div class="mb-3 d-inline-block p-4 bg-light rounded-circle">
                                                    <i class="bi bi-envelope-slash fs-1 text-muted"></i>
                                                </div>
                                                <h6 class="fw-bold">لا يوجد مشتركون حالياً</h6>
                                                <p class="small mb-0">لم يقم أحد بالتسجيل في النشرة الإخبارية بعد</p>
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
    .subscriptions-mgmt-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .bg-primary-light { background-color: rgba(34, 197, 94, 0.1); }
    .transition-all { transition: all 0.3s ease; }

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

    .header-icon-small {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }

    .hover-danger:hover {
        background-color: #ef4444 !important;
        color: white !important;
        border-color: #ef4444 !important;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .avatar-soft {
        box-shadow: inset 0 0 0 1px rgba(34, 197, 94, 0.1);
    }
</style>
@endsection
