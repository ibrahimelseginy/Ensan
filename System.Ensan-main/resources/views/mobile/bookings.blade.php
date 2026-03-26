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
    <div class="container-fluid mt-n5 px-4 mb-5">
        <div class="row g-3">
            <div class="col-md-3">
                <div class="stat-card glass-card p-4 text-center animate-up">
                    <div class="text-muted small mb-1">إجمالي طلبات الموبايل</div>
                    <div class="fs-2 fw-bold text-white">{{ $mobileBookings->count() }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card glass-card p-4 text-center animate-up" style="animation-delay:0.1s">
                    <div class="text-info small mb-1">إجمالي طلبات الموقع</div>
                    <div class="fs-2 fw-bold text-info">{{ $webBookings->count() }}</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card glass-card p-4 text-center animate-up" style="animation-delay:0.2s">
                    <div class="text-warning small mb-1">قيد الانتظار (الكل)</div>
                    <div class="fs-2 fw-bold text-warning">{{ $mobileBookings->where('status', 'pending')->count() + $webBookings->where('status', 'pending')->count() }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        {{-- Tabs for switching between App and Web --}}
        <ul class="nav nav-pills mb-4 gap-2 justify-content-center" id="bookingTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active rounded-pill px-5 py-3 fw-bold shadow-sm border border-white border-opacity-10" id="app-tab" data-bs-toggle="pill" data-bs-target="#app-bookings" type="button" role="tab">
                    طلبات التطبيق (App) <span class="badge bg-white bg-opacity-10 ms-2">{{ $mobileBookings->count() }}</span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link rounded-pill px-5 py-3 fw-bold shadow-sm border border-white border-opacity-10" id="web-tab" data-bs-toggle="pill" data-bs-target="#web-bookings" type="button" role="tab">
                    طلبات الموقع (Web) <span class="badge bg-white bg-opacity-10 ms-2">{{ $webBookings->count() }}</span>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="bookingTabsContent">
            {{-- App Bookings Tab --}}
            <div class="tab-pane fade show active" id="app-bookings" role="tabpanel">
                <div class="glass-card overflow-hidden">
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
                                        <span class="badge rounded-pill px-3 py-2 {{ $booking->status == 'pending' ? 'bg-warning text-dark' : ($booking->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                            {{ $booking->status == 'pending' ? 'قيد الانتظار' : ($booking->status == 'approved' ? 'مقبول' : 'مرفوض') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-glass-secondary btn-sm rounded-3" data-bs-toggle="dropdown">تحكم <i class="bi bi-chevron-down ms-1"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-dark">
                                                <li>
                                                    <form action="{{ route('mobile.bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-1"></i> قبول</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('mobile.bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-1"></i> رفض</button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('mobile.bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-muted"><i class="bi bi-trash me-1"></i> حذف</button>
                                                    </form>
                                                </li>
                                            </ul>
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

            {{-- Web Bookings Tab --}}
            <div class="tab-pane fade" id="web-bookings" role="tabpanel">
                <div class="glass-card overflow-hidden">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0 text-white">
                            <thead class="bg-white bg-opacity-5">
                                <tr>
                                    <th class="py-4 ps-4">المستفيد (Web)</th>
                                    <th class="py-4">تاريخ الوصول</th>
                                    <th class="py-4 text-center">الحالة</th>
                                    <th class="py-4 text-center">الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($webBookings as $booking)
                                <tr class="border-white border-opacity-5">
                                    <td class="py-4 ps-4">
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="avatar-text bg-info bg-opacity-20 text-info rounded-circle">
                                                {{ mb_substr($booking->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold fs-5 d-flex align-items-center gap-2">
                                                    {{ $booking->name }}
                                                    @if($booking->source == 'mobile')
                                                        <span class="badge bg-primary bg-opacity-10 text-primary x-small">M</span>
                                                    @else
                                                        <span class="badge bg-info bg-opacity-10 text-info x-small">W</span>
                                                    @endif
                                                </div>
                                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $booking->phone }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div>{{ $booking->arrival_date }}</div>
                                        <div class="badge bg-info bg-opacity-10 text-info mt-1 small">{{ $booking->expected_duration_arabic }}</div>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge rounded-pill px-3 py-2 {{ $booking->status == 'pending' ? 'bg-warning text-dark' : ($booking->status == 'approved' ? 'bg-success' : 'bg-danger') }}">
                                            {{ $booking->status == 'pending' ? 'قيد الانتظار' : ($booking->status == 'approved' ? 'مقبول' : 'مرفوض') }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-glass-secondary btn-sm rounded-3" data-bs-toggle="dropdown">تحكم <i class="bi bi-chevron-down ms-1"></i></button>
                                            <ul class="dropdown-menu dropdown-menu-dark">
                                                <li>
                                                    <form action="{{ route('mobile.web_bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="approved">
                                                        <button type="submit" class="dropdown-item text-success"><i class="bi bi-check-circle me-1"></i> قبول</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('mobile.web_bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="rejected">
                                                        <button type="submit" class="dropdown-item text-danger"><i class="bi bi-x-circle me-1"></i> رفض</button>
                                                    </form>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form action="{{ route('mobile.web_bookings.destroy', $booking) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="dropdown-item text-muted"><i class="bi bi-trash me-1"></i> حذف</button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="4" class="py-5 text-center text-muted">لا توجد طلبات من الموقع</td></tr>
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
    .premium-hero-sleek { padding: 60px 5%; border-radius: 0 0 40px 40px; position: relative; overflow: hidden; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15; }
    .glow-orb-1 { width: 300px; height: 300px; top: -50px; right: -50px; }
    .glow-orb-2 { width: 200px; height: 200px; bottom: -50px; left: 50px; }
    
    .glass-card { background: #0f172a !important; border: 1px solid rgba(255,255,255,0.05); border-radius: 25px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .stat-card { transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); border-color: rgba(59, 130, 246, 0.3); }
    
    .avatar-text { width: 48px; height: 48px; display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: 1.2rem; }
    
    .btn-glass-secondary { background: rgba(255, 255, 255, 0.05); color: #cbd5e1; border: 1px solid rgba(255, 255, 255, 0.1); }
    
    .nav-pills .nav-link { background: rgba(255,255,255,0.05); color: #cbd5e1; transition: 0.3s; }
    .nav-pills .nav-link.active { background: #3b82f6; color: white; border-color: #3b82f6 !important; }
    
    .mt-n5 { margin-top: -3rem !important; }
    .animate-up { animation: fadeInUp 0.5s both; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>
@endsection
