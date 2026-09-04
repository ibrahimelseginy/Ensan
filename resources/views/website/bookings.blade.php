@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="bookings-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #1e3a8a;"></div>
            <div class="glow-orb-2" style="background: #3b82f6;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">طلبات الحجز</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-house-heart-fill me-2"></i> إدارة حجوزات دار الضيافة
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">طلبات الحجز (الموقع الإلكتروني)</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        مراجعة والرد على طلبات حجز الغرف الواردة عبر الموقع الإلكتروني
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left"></div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card overflow-hidden">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">صاحب الطلب</th>
                                <th>تاريخ الوصول / المغادرة</th>
                                <th>الغرفة / الدار</th>
                                <th>الحالة</th>
                                <th class="text-center">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($bookings as $booking)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold">{{ $booking->name }}</div>
                                    <div class="x-small text-muted">{{ $booking->phone }} | {{ $booking->email }}</div>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $booking->check_in }} <i class="bi bi-arrow-left text-muted px-1"></i> {{ $booking->check_out }}</div>
                                    <div class="x-small text-info">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} ليالي</div>
                                </td>
                                <td>
                                    <div class="small fw-bold">{{ $booking->guestHouse->name ?? 'غير محدد' }}</div>
                                    <span class="badge bg-secondary-subtle text-secondary x-small">{{ $booking->room_type }}</span>
                                </td>
                                <td>
                                    @php
                                        $statusClass = [
                                            'pending' => 'bg-warning-subtle text-warning border-warning-subtle',
                                            'confirmed' => 'bg-success-subtle text-success border-success-subtle',
                                            'cancelled' => 'bg-danger-subtle text-danger border-danger-subtle'
                                        ][$booking->status] ?? 'bg-light';
                                        
                                        $statusText = [
                                            'pending' => 'قيد الانتظار',
                                            'confirmed' => 'مؤكد',
                                            'cancelled' => 'ملغي'
                                        ][$booking->status] ?? $booking->status;
                                    @endphp
                                    <span class="badge {{ $statusClass }} border px-3 rounded-pill">{{ $statusText }}</span>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <button class="btn btn-sm btn-light border rounded-pill px-3 shadow-none" data-bs-toggle="modal" data-bs-target="#viewBooking{{ $booking->id }}">
                                            <i class="bi bi-eye me-1"></i> تفاصيل
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-sm btn-light border rounded-pill px-3 dropdown-toggle shadow-none" data-bs-toggle="dropdown">
                                                تغيير الحالة
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 glass-dropdown">
                                                <li>
                                                    <form action="{{ route('website.bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item py-2 small"><i class="bi bi-check-circle me-2 text-success"></i> تأكيد الحجز</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('website.bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item py-2 small"><i class="bi bi-x-circle me-2 text-danger"></i> إلغاء الحجز</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            {{-- Booking Details Modal --}}
                            <div class="modal fade" id="viewBooking{{ $booking->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-lg modal-dialog-centered">
                                    <div class="modal-content modal-premium-dark border-0 shadow-2xl rounded-4 overflow-hidden" style="background-color: #0f172a !important; color: white;">
                                        <div class="modal-header border-bottom border-white border-opacity-10 bg-slate-800 px-4 py-3">
                                            <h5 class="modal-title fw-bold text-white"><i class="bi bi-info-circle-fill me-2 text-info"></i> تفاصيل طلب الحجز #{{ $booking->id }}</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body p-0" style="background-color: #0f172a !important;">
                                            <div class="row g-0">
                                                {{-- Side Info Strip --}}
                                                <div class="col-md-4 bg-slate-800 bg-opacity-50 border-end border-white border-opacity-10 p-4">
                                                    <div class="text-center mb-4">
                                                        <div class="strip-avatar bg-indigo-600 shadow-lg rounded-20 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 25px; position: relative; border: 2px solid rgba(255,255,255,0.1);">
                                                            <i class="bi bi-person-fill fs-1 text-white opacity-75"></i>
                                                            <div style="position: absolute; inset: -4px; border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 28px;"></div>
                                                        </div>
                                                        <h6 class="fw-bold mb-1 text-white">{{ $booking->name }}</h6>
                                                        <div class="x-small text-slate-400 mb-1"><i class="bi bi-telephone me-1"></i> {{ $booking->phone }}</div>
                                                        @if($booking->national_id)
                                                            <div class="badge bg-indigo-500 bg-opacity-20 text-indigo-300 rounded-pill px-3 py-1 x-small mt-2 border border-indigo-500 border-opacity-30">
                                                                {{ $booking->national_id }}
                                                            </div>
                                                        @endif
                                                    </div>

                                                    <div class="vstack gap-3 mt-4">
                                                        <div class="p-3 bg-slate-900 bg-opacity-50 rounded-4 border border-white border-opacity-5">
                                                            <div class="x-small text-uppercase text-indigo-400 fw-800 mb-2"><i class="bi bi-people-fill me-1"></i> بيانات المرافق</div>
                                                            <div class="fw-bold text-white mb-1">{{ $booking->companion_name ?? 'لا يوجد مرافق' }}</div>
                                                            @if($booking->companion_phone)
                                                                <div class="x-small text-slate-400"><i class="bi bi-telephone me-1"></i> {{ $booking->companion_phone }}</div>
                                                            @endif
                                                        </div>

                                                        <div class="p-3 bg-slate-900 bg-opacity-50 rounded-4 border border-white border-opacity-5">
                                                            <div class="x-small text-uppercase text-emerald-400 fw-800 mb-2"><i class="bi bi-geo-alt-fill me-1"></i> مكان الإقامة</div>
                                                            <div class="fw-bold text-white small opacity-90">{{ $booking->address ?? 'العنوان غير محدد' }}</div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Main Details Area --}}
                                                <div class="col-md-8 p-4">
                                                    <div class="mb-4">
                                                        <h6 class="fw-bold text-indigo-400 mb-3 d-flex align-items-center"><i class="bi bi-hospital-fill me-2 fs-5"></i> تفاصيل الإقامة والعلاج</h6>
                                                        <div class="row g-3">
                                                            <div class="col-6">
                                                                <label class="x-small text-slate-500 fw-bold d-block mb-1">نوع الغرفة</label>
                                                                <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-white small border border-white border-opacity-5">{{ $booking->room_type }}</div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="x-small text-slate-500 fw-bold d-block mb-1">المدة المتوقعة</label>
                                                                <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-white small border border-white border-opacity-5">{{ $booking->expected_duration_arabic ?? '-' }}</div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="x-small text-slate-500 fw-bold d-block mb-1">تاريخ الوصول</label>
                                                                <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-emerald-400 small border border-white border-opacity-5 fw-bold">{{ $booking->arrival_date ?? $booking->check_in }}</div>
                                                            </div>
                                                            <div class="col-6">
                                                                <label class="x-small text-slate-500 fw-bold d-block mb-1">المركز الطبي</label>
                                                                <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-white small border border-white border-opacity-5">{{ $booking->medical_center ?? '-' }}</div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="mb-4">
                                                        <h6 class="fw-bold text-warning mb-3 d-flex align-items-center"><i class="bi bi-paperclip me-2 fs-5"></i> المستندات المرفقة</h6>
                                                        <div class="d-flex flex-wrap gap-2">
                                                            @php
                                                                $docs = [
                                                                    ['label' => 'بطاقة المريض', 'path' => $booking->patient_id_path, 'icon' => 'bi-person-vcard'],
                                                                    ['label' => 'بطاقة المرافق', 'path' => $booking->companion_id_path, 'icon' => 'bi-person-badge'],
                                                                    ['label' => 'تحويل المستشفى', 'path' => $booking->medical_transfer_path, 'icon' => 'bi-hospital'],
                                                                    ['label' => 'كارت المتابعة', 'path' => $booking->followup_card_path, 'icon' => 'bi-card-list'],
                                                                    ['label' => 'تقرير الإشعاع', 'path' => $booking->medical_report_path, 'icon' => 'bi-file-earmark-medical'],
                                                                ];
                                                            @endphp

                                                            @foreach($docs as $doc)
                                                                @if($doc['path'])
                                                                    <a href="{{ app(\App\Services\ImageUploadService::class)->url($doc['path']) }}" target="_blank" class="btn btn-sm btn-glass-premium-doc d-flex align-items-center gap-2 px-3 py-2 rounded-3">
                                                                        <i class="bi {{ $doc['icon'] }} text-info"></i>
                                                                        <span class="x-small fw-bold text-white">{{ $doc['label'] }}</span>
                                                                    </a>
                                                                @endif
                                                            @endforeach

                                                            @if(collect($docs)->where('path', '!=', null)->isEmpty())
                                                                <div class="p-3 w-100 bg-slate-800 bg-opacity-30 rounded-3 text-center border border-dashed border-white border-opacity-10 text-slate-500 x-small">
                                                                    <i class="bi bi-folder-x me-1"></i> لا توجد مستندات مرفقة لهذا الطلب
                                                                </div>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if($booking->notes)
                                                        <div>
                                                            <h6 class="fw-bold text-white-50 mb-2 x-small text-uppercase"><i class="bi bi-pencil-square me-1"></i> ملاحظات إضافية</h6>
                                                            <div class="p-3 bg-slate-800 bg-opacity-60 rounded-4 border border-white border-opacity-5 text-slate-300 small italic">
                                                                "{{ $booking->notes }}"
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-top border-white border-opacity-10 bg-slate-800 p-3">
                                            <button type="button" class="btn btn-sm btn-glass-indigo px-5 rounded-pill fw-bold" data-bs-dismiss="modal">إغلاق</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    body { background-color: #0b0e14 !important; }
    .bookings-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #93c5fd; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @media (max-width: 991px) { .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; } .display-4 { font-size: 2.2rem; } }

    .glass-card { background: white; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.04); border: 1px solid #f1f5f9; }
    .glass-dropdown { backdrop-filter: blur(10px); background: rgba(255,255,255,0.9); border-radius: 12px; }
    .x-small { font-size: 0.7rem; }

    /* Premium Modal Dark Styles */
    .modal-premium-dark { background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; }
    .bg-slate-800 { background-color: #1e293b !important; }
    .bg-slate-900 { background-color: #0f172a !important; }
    .text-indigo-400 { color: #818cf8 !important; }
    .text-emerald-400 { color: #34d399 !important; }
    .fw-800 { font-weight: 800; }
    .rounded-20 { border-radius: 20px; }
    
    .btn-glass-premium-doc {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    .btn-glass-premium-doc:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }
    .btn-glass-indigo {
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
        color: #818cf8;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    .btn-glass-indigo:hover {
        background: rgba(99, 102, 241, 0.2);
        border-color: rgba(99, 102, 241, 0.4);
        color: #a5b4fc;
        transform: translateY(-2px);
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



