@extends('layouts.app')

@section('content')
<div class="bookings-page">
    {{-- Decorative Header --}}
    <div class="premium-hero-sleek" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #3b82f6;"></div>
            <div class="glow-orb-2" style="background: #60a5fa;"></div>
        </div>
        <div class="container-fluid hero-content-wrapper">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-end">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb mb-0 justify-content-end">
                            <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white">إدارة الحجوزات</li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bold text-white mb-2">طلبات الحجز من الموبايل</h1>
                    <p class="lead text-white-50">مراجعة والرد على طلبات حجز الغرف الواردة عبر تطبيق الموبايل</p>
                </div>
                <div class="icon-box bg-white bg-opacity-10 p-4 rounded-4">
                    <i class="bi bi-house-heart fs-1 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="container-fluid pt-4 px-4 mb-4">
        <div class="row g-3">
            <div class="col-md-6">
                <div class="stat-card glass-card p-4 animate-up" style="border-right: 5px solid #3b82f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-phone"></i>
                        </div>
                        <div>
                            <div class="text-white-50 small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">إجمالي طلبات الموبايل</div>
                            <div class="fs-2 fw-bold text-white lh-1">{{ $mobileBookings->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card glass-card p-4 animate-up" style="animation-delay:0.1s; border-right: 5px solid #f59e0b;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <div>
                            <div class="text-white-50 small fw-bold text-uppercase mb-1" style="letter-spacing: 1px;">طلبات قيد الانتظار</div>
                            <div class="fs-2 fw-bold text-warning lh-1">{{ $mobileBookings->where('status', 'pending')->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        <div class="glass-card overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-5 d-flex justify-content-between align-items-center bg-white bg-opacity-5">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-list-stars me-2 text-primary"></i> قائمة الطلبات الحالية</h5>
                <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-20 rounded-pill px-3">مزامنة تلقائية</span>
            </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="bg-white bg-opacity-5">
                                <tr>
                                    <th class="py-4 ps-4">المستفيد (App)</th>
                                    <th class="py-4">تاريخ الوصول</th>
                                    <th class="py-4 text-center">الحالة</th>
                                    <th class="py-4 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($mobileBookings as $booking)
                                <tr class="border-white border-opacity-5">
                                    <td class="py-4 ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-text bg-primary bg-opacity-20 text-primary rounded-circle">
                                                {{ mb_substr($booking->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold fs-5">{{ $booking->name }}</div>
                                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $booking->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $booking->arrival_date }}</div>
                                        <div class="badge bg-info bg-opacity-10 text-info mt-1 small">{{ $booking->expected_duration_arabic }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-2 {{ $booking->status == 'pending' ? 'bg-warning text-dark' : ($booking->status == 'confirmed' ? 'bg-success' : 'bg-danger') }}">
                                            {{ $booking->status == 'pending' ? 'قيد الانتظار' : ($booking->status == 'confirmed' ? 'مقبول' : 'مرفوض') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-glass-secondary btn-sm rounded-3 px-3" data-bs-toggle="modal" data-bs-target="#modalApp{{ $booking->id }}">
                                            <i class="bi bi-eye me-1"></i> عرض التفاصيل
                                        </button>

                                        {{-- App Booking Detail Modal --}}
                                        <div class="modal fade" id="modalApp{{ $booking->id }}" tabindex="-1">
                                            <div class="modal-dialog modal-dialog-centered">
                                                <div class="modal-content border-0 shadow-lg text-start" style="background-color: var(--ws-bg-page) !important; border-radius: 24px !important; overflow: hidden;">
                                                    <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px !important;">
                                                        <h5 class="modal-title fw-bold">
                                                            <i class="bi bi-house-heart me-2"></i> تفاصيل طلب الحجز (App)
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-4" style="background-color: var(--ws-bg-page) !important;">
                                                        <div class="row g-4 mb-4">
                                                            <div class="col-12">
                                                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">إسم المستفيد</label>
                                                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $booking->name }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">رقم الهاتف</label>
                                                                <div style="color: #0066ff; font-size: 1.1rem; font-weight: 600;">{{ $booking->phone }}</div>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">تاريخ الوصول المتوقع</label>
                                                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $booking->arrival_date }}</div>
                                                            </div>
                                                            <div class="col-12">
                                                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">المدة المتوقعة</label>
                                                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $booking->expected_duration_arabic }}</div>
                                                            </div>
                                                        </div>

                                                        <div class="admin-panel mt-4 pt-4 border-top border-white border-opacity-10">
                                                            <h6 class="mb-3" style="color: var(--ws-text-primary) !important; font-weight: 700; border-right: 4px solid #0066ff; padding-right: 15px;">اتخاذ قرار</h6>
                                                            <div class="d-grid gap-2">
                                                                <div class="row g-2">
                                                                    <div class="col-6">
                                                                        <form action="{{ route('mobile.web_bookings.update', $booking) }}" method="POST">
                                                                            @csrf @method('PATCH')
                                                                            <input type="hidden" name="status" value="confirmed">
                                                                            <button type="submit" class="btn w-100" style="background: #00d1b2; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px; font-weight: 700;">قبول الحجز</button>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-6">
                                                                        <form action="{{ route('mobile.web_bookings.update', $booking) }}" method="POST">
                                                                            @csrf @method('PATCH')
                                                                            <input type="hidden" name="status" value="cancelled">
                                                                            <button type="submit" class="btn w-100" style="background: #dc2626; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px; font-weight: 700;">رفض الحجز</button>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-12">
                                                                        <form action="{{ route('mobile.web_bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                                                            @csrf @method('DELETE')
                                                                            <button type="submit" class="btn w-100 mt-2" style="background: #363636; color: #f8fafc; border-radius: 12px; padding: 12px; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);">حذف السجل</button>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-5 text-center text-muted">لا توجد طلبات من التطبيق</td></tr>
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
</div>

<style>
    .premium-hero-sleek { padding: 60px 5%; border-radius: 0 0 40px 40px; position: relative; overflow: hidden; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15; }
    .glow-orb-1 { width: 300px; height: 300px; top: -50px; right: -50px; }
    .glow-orb-2 { width: 200px; height: 200px; bottom: -50px; left: 50px; }
    
    .glass-card { background: var(--ws-bg-card-header) !important; border: 1px solid rgba(255,255,255,0.05); border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .stat-card { transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); border-color: rgba(59, 130, 246, 0.3); }
    
    .icon-circle { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    
    .avatar-text { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.2rem; }
    
    .btn-glass-secondary { background: rgba(255, 255, 255, 0.05); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.1); }
    
    .mt-n5 { margin-top: -3.5rem !important; }
    .animate-up { animation: fadeInUp 0.5s both; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
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
</style>
@endsection



