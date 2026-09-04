@extends('layouts.app')

@section('content')
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #ea580c 0%, #f97316 100%);">
    <div class="hero-content">
        <div class="hero-greeting text-white-50">التبرعات العينية ًں“¦</div>
        <h1 class="hero-title">شارك بما لا تحتاج (In-Kind)</h1>
        <p class="hero-subtitle">إدارة عروض التبرع بالأثاث والملابس والأجهزة من المتبرعين</p>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-12">
            <div class="glass-card shadow-sm overflow-hidden animate-slide-up">
                 <div class="d-flex justify-content-between align-items-center p-4 border-bottom bg-light bg-opacity-50">
                    <h5 class="fw-bold mb-0 text-orange"><i class="bi bi-box-seam me-2"></i> القائمة الكاملة</h5>
                    <span class="badge bg-orange rounded-pill px-3">{{ $donations->where('status', 'pending')->count() }} جديد</span>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-white text-muted small fw-bold text-uppercase border-bottom">
                            <tr>
                                <th class="ps-4">المتبرع</th>
                                <th>نوع التبرع</th>
                                <th>الكمية</th>
                                <th>عنوان الاستلام</th>
                                <th>الوقت المفضل</th>
                                <th>الحالة</th>
                                <th class="text-center">الإجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($donations as $donation)
                            <tr class="donation-row {{ $donation->status == 'pending' ? 'bg-orange-subtle bg-opacity-10' : '' }}">
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $donation->donor_name ?? 'فاعل خير' }}</div>
                                    <div class="x-small text-muted font-monospace"><i class="bi bi-telephone me-1"></i>{{ $donation->donor_phone }}</div>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $donation->item_name }}</div>
                                    @if($donation->image_path)
                                        <a href="{{ $donation->image_url }}" target="_blank" class="badge bg-light text-dark border x-small text-decoration-none mt-1"><i class="bi bi-image me-1"></i> صورة</a>
                                    @endif
                                </td>
                                <td><span class="badge bg-dark rounded-circle px-2 py-1">{{ $donation->quantity }}</span></td>
                                <td class="small">{{ Str::limit($donation->pickup_address, 30) }}</td>
                                <td class="small text-muted">{{ $donation->preferred_pickup_time ? $donation->preferred_pickup_time->format('Y-m-d H:i') : 'أي وقت' }}</td>
                                <td>
                                    @php
                                        $badges = [
                                            'pending' => 'bg-warning text-dark',
                                            'scheduled' => 'bg-info text-white',
                                            'collected' => 'bg-success text-white',
                                            'rejected' => 'bg-danger text-white'
                                        ];
                                        $labels = [
                                            'pending' => 'جديد',
                                            'scheduled' => 'مجدول للاستلام',
                                            'collected' => 'تم الاستلام',
                                            'rejected' => 'رفض' 
                                        ];
                                    @endphp
                                    <span class="badge {{ $badges[$donation->status] ?? 'bg-secondary' }} rounded-pill px-3 py-1 fw-normal">{{ $labels[$donation->status] ?? $donation->status }}</span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-outline-dark rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#viewDonation{{ $donation->id }}">
                                        إدارة <i class="bi bi-gear-fill ms-1"></i>
                                    </button>
                                </td>
                            </tr>

                            {{-- Donation Detail Modal --}}
                            <div class="modal fade" id="viewDonation{{ $donation->id }}" tabindex="-1">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content border-0 shadow-lg text-start" style="background-color: var(--ws-bg-page) !important; border-radius: 24px !important; overflow: hidden;">
                                        <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px !important;">
                                            <h5 class="modal-title fw-bold">إدارة استلام التبرع العيني</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-4" style="background-color: var(--ws-bg-page) !important;">
                                            <div class="text-center mb-4">
                                                @if($donation->image_path)
                                                    <img src="{{ $donation->image_url }}" class="rounded-3 shadow-sm w-100 object-fit-cover" style="height: 200px; border: 1px solid rgba(255,255,255,0.1);">
                                                @else
                                                    <div class="rounded-3 d-flex align-items-center justify-content-center text-muted" style="height: 150px; background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1);">
                                                        <div class="text-center">
                                                            <i class="bi bi-box-seam display-1 opacity-25"></i>
                                                            <p class="mb-0 small">لا توجد صورة للتبرع</p>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">وصف العنصر</label>
                                                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; color: var(--ws-text-primary);">{{ $donation->item_description ?? 'لا يوجد وصف' }}</div>
                                            </div>

                                            <div class="mb-3">
                                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">العنوان بالتفصيل</label>
                                                <div style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; color: var(--ws-text-primary);">{{ $donation->pickup_address }}</div>
                                                <div class="d-grid mt-2">
                                                    <a href="https://maps.google.com/?q={{ urlencode($donation->pickup_address) }}" target="_blank" class="btn btn-sm" style="background: rgba(255,255,255,0.05); color: #cbd5e1; border: 1px solid rgba(255,255,255,0.1); border-radius: 8px;"><i class="bi bi-geo-alt-fill me-1 text-danger"></i> عرض على الخريطة</a>
                                                </div>
                                            </div>

                                            <hr class="my-4" style="opacity: 0.1; color: var(--ws-text-primary);">
                                            
                                            <form action="{{ route('mobile.inkind.update', $donation) }}" method="POST">
                                                @csrf @method('PATCH')
                                                <div class="d-grid gap-2">
                                                    <button type="submit" name="status" value="scheduled" class="btn" style="background: #0066ff; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px; font-weight: 700;"><i class="bi bi-calendar-check me-2"></i> جدولة موعد الاستلام</button>
                                                    <button type="submit" name="status" value="collected" class="btn" style="background: #00d1b2; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px; font-weight: 700;"><i class="bi bi-check-circle-fill me-2"></i> تم استلام التبرع</button>
                                                    <button type="submit" name="status" value="rejected" class="btn btn-sm mt-2" style="background: #363636; color: #f8fafc; border-radius: 12px; padding: 8px; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);">رفض التبرع</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="bi bi-check2-circle fs-1 opacity-25 mb-2"></i>
                                        <p class="mb-0">لا توجد تبرعات عينية جديدة</p>
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

<style>
    .glass-card { background: white; border-radius: 16px; box-shadow: 0 4px 24px rgba(0,0,0,0.06); border: 1px solid #fff7ed; }
    .x-small { font-size: 0.7rem; }
    .bg-orange { background-color: #f97316 !important; }
    .text-orange { color: #c2410c !important; }
    .bg-orange-subtle { background-color: #ffedd5 !important; }
    .donation-row:hover { background-color: var(--ws-text-primary)7ed !important; }
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




