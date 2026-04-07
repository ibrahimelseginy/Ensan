@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100" style="background-color: #05070a;">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down">
        <div>
            <h1 class="h2 fw-800 text-white mb-1">سجلات طلبات التبرع <span class="text-success-glow">(الموبايل)</span></h1>
            <p class="text-white-50 small mb-0">متابعة عمليات التبرع الواردة من تطبيق الموبايل والحالات الخاصة بها</p>
        </div>
        <div class="glass-badge px-4 py-2">
            <i class="bi bi-cash-stack me-2 text-success"></i>
            <span class="fw-bold">إجمالي العمليات:</span> {{ $donations->count() }}
        </div>
    </div>

    <div class="row g-4">
        @forelse($donations as $donation)
        <div class="col-md-6 col-lg-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-donation-card">
                <div class="card-inner-top">
                    <div class="card-meta">
                        <span class="badge-premium @if($donation->status == 'pending') status-pending @elseif($donation->status == 'completed') status-success @else status-danger @endif">
                            {{ $donation->status == 'pending' ? 'بانتظار التأكيد' : ($donation->status == 'completed' ? 'تم التبرع' : 'فشلت / ملغاة') }}
                        </span>
                        <div class="amount-badge">
                            {{ number_format($donation->donation_amount, 0) }} <small>ج.م</small>
                        </div>
                    </div>
                    
                    <div class="card-user-info">
                        <h4 class="user-name text-truncate" title="{{ $donation->donor_name }}">{{ $donation->donor_name }}</h4>
                        <p class="user-phone font-outfit">{{ $donation->donor_phone }}</p>
                        <div class="donation-for-tag mt-2">
                            <i class="bi bi-bullseye me-1"></i> التبرع لـ: <span class="text-white fw-bold">{{ $donation->donation_for }}</span>
                        </div>
                    </div>

                    <div class="payment-method-box mt-3">
                        <div class="small text-white-50 mb-1">طريقة الدفع</div>
                        <div class="fw-bold text-success-glow">
                            <i class="bi bi-credit-card-2-front me-1"></i> {{ $donation->payment_method }}
                        </div>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-details-glow w-100" data-bs-toggle="modal" data-bs-target="#modal{{ $donation->id }}">
                            <i class="bi bi-eye me-2"></i> عرض تفاصيل المتبرع
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div class="modal fade" id="modal{{ $donation->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="background-color: var(--ws-bg-page) !important; border-radius: 24px !important; overflow: hidden;">
                    <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px !important;">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-cash-coin me-2"></i> تفاصيل طلب التبرع (تطبيق الموبايل)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" style="background-color: var(--ws-bg-page) !important;">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">إسم المتبرع</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $donation->donor_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">رقم الهاتف</label>
                                <div class="info-val font-outfit" style="color: #0066ff; font-size: 1.1rem; font-weight: 600; font-family: 'Outfit', sans-serif;">{{ $donation->donor_phone }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">عنوان المتبرع</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $donation->donor_address ?? 'غير محدد' }}</div>
                            </div>
                            
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">إسم الحساب (انستاباي/فودافون)</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $donation->account_name ?? 'غير متوفر' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">رقم الحساب المحول منه</label>
                                <div class="info-val font-outfit" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $donation->account_number ?? 'غير متوفر' }}</div>
                            </div>

                            <div class="col-12 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">
                                    <i class="bi bi-image me-1"></i> صورة إثبات التحويل (Proof of Transfer)
                                </label>
                                @if($donation->receipt_path)
                                    <div class="receipt-preview-container" style="border-radius: 16px; overflow: hidden; border: 2px solid rgba(0, 102, 255, 0.2); background: rgba(0,0,0,0.2); position: relative;">
                                        <div class="preview-overlay" style="position: absolute; top: 10px; right: 10px; z-index: 5;">
                                            <span class="badge bg-primary rounded-pill shadow-sm">تأكيد التحصيل</span>
                                        </div>
                                        <a href="{{ $donation->image_url }}" target="_blank" class="d-block text-center p-2">
                                            <img src="{{ $donation->image_url }}" class="img-fluid rounded" style="max-height: 450px; object-fit: contain; cursor: zoom-in; transition: 0.3s;" alt="Donation Receipt" onmouseover="this.style.transform='scale(1.02)'" onmouseout="this.style.transform='scale(1)'">
                                        </a>
                                        <div class="p-2 text-center border-top border-white border-opacity-10">
                                            <small class="text-white-50"><i class="bi bi-info-circle me-1"></i> اضغط على الصورة لتكبيرها</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="no-receipt-placeholder p-5 text-center" style="background: rgba(255,255,255,0.03); border: 2px dashed rgba(255,255,255,0.1); border-radius: 16px;">
                                        <i class="bi bi-camera-fill display-6 text-white-50 mb-3 d-block"></i>
                                        <h6 class="text-white-50">لم يتم إرفاق صورة إثبات التحويل بعد</h6>
                                        <p class="small text-muted mb-0">المتبرع لم يقم برفع صورة الوصل/التحويل من الموبايل.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <hr class="my-2" style="opacity: 0.1; color: var(--ws-text-primary);">

                            <div class="col-12 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">ملاحظات المتبرع</label>
                                <div class="message-box" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; color: #94a3b8; line-height: 1.7;">
                                    "{{ $donation->notes ?? 'لا توجد ملاحظات إضافية.' }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel mt-5" style="background: rgba(255, 255, 255, 0.02); border-radius: 20px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.05);">
                            <h6 class="mb-3" style="color: var(--ws-text-primary) !important; font-weight: 700; border-right: 4px solid #0066ff; padding-right: 15px;"><i class="bi bi-shield-lock me-2"></i> التحكم في حالة الطلب</h6>
                            <form action="{{ route('mobile.donations.update', $donation->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small opacity-75" style="color: #94a3b8;">تغيير الحالة</label>
                                        <select name="status" class="form-select" style="background: rgba(15, 23, 42, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: var(--ws-text-primary) !important; border-radius: 12px !important; padding: 12px !important;">
                                            <option value="pending" {{ $donation->status == 'pending' ? 'selected' : '' }}>بانتظار التأكيد (Pending)</option>
                                            <option value="completed" {{ $donation->status == 'completed' ? 'selected' : '' }}>تم التحصيل بنجاح (Completed)</option>
                                            <option value="failed" {{ $donation->status == 'failed' ? 'selected' : '' }}>فشلت / ملغاة (Failed)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between">
                                        <button type="submit" class="btn" style="background: #00d1b2; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px 35px; font-weight: 700;">حفظ التغييرات</button>
                                        <button type="button" class="btn" style="background: #363636; color: #f8fafc; border-radius: 12px; padding: 12px 20px; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);" onclick="if(confirm('هل أنت متأكد من حذف هذا السجل؟')) document.getElementById('del-form-{{ $donation->id }}').submit()">حذف السجل</button>
                                    </div>
                                </div>
                            </form>
                            <form id="del-form-{{ $donation->id }}" action="{{ route('mobile.donations.destroy', $donation->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 animate-up">
            <div class="glass-card text-center py-5">
                <i class="bi bi-cash-stack display-4 text-white-50"></i>
                <h5 class="text-white mt-4">لا توجد سجلات تبرع حالياً</h5>
                <p class="text-white-50">لم يتم إرسال أي طلبات تبرع عبر تطبيق الموبايل بعد.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    :root {
        --dark-bg: #05070a;
        --card-bg: var(--ws-bg-card-header);
        --primary: #3b82f6;
        --success: #10b981;
        --success-glow: #34d399;
        --danger: #ef4444;
    }

    body { background-color: var(--dark-bg); font-family: 'Tajawal', 'Outfit', sans-serif; }
    .fw-800 { font-weight: 800; }
    .text-primary-glow { color: #0066ff; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    .glass-badge { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 100px; color: var(--ws-text-primary); backdrop-filter: blur(10px); }

    /* Card Styling */
    .premium-donation-card {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        overflow: hidden;
        transition: 0.4s;
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .premium-donation-card:hover { transform: translateY(-10px); border-color: var(--success); }

    .card-inner-top { padding: 24px; }
    .card-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    
    .badge-premium { padding: 6px 14px; border-radius: 100px; font-size: 0.7rem; font-weight: 700; }
    .status-pending { background: rgba(255,255,255,0.1); color: var(--ws-text-primary); }
    .status-success { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .status-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

    .amount-badge { background: rgba(16, 185, 129, 0.1); color: var(--success-glow); padding: 8px 16px; border-radius: 12px; font-weight: 800; font-family: 'Outfit'; font-size: 1.2rem; }

    .user-name { font-weight: 700; color: var(--ws-text-primary); margin-bottom: 2px; }
    .user-phone { color: #94a3b8; font-size: 0.9rem; }
    .donation-for-tag { font-size: 0.85rem; color: #64748b; }

    .payment-method-box { background: rgba(0,0,0,0.2); border-radius: 14px; padding: 12px 18px; border: 1px solid rgba(255,255,255,0.03); }

    .btn-details-glow { background: rgba(255,255,255,0.05); color: var(--ws-text-primary); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; font-weight: 600; transition: 0.3s; }
    .btn-details-glow:hover { background: var(--success); border-color: var(--success); box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }

     /* Modal Styling - Premium Blue */
    .premium-modal { 
        background-color: var(--ws-bg-page) !important; 
        border: none !important;
        border-radius: 24px !important; 
        overflow: hidden; 
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; 
    }
    .premium-modal .modal-header { 
        background-color: #0066ff !important; 
        border: none !important;
        padding: 20px 30px; 
    }
    .premium-modal .modal-body { 
        padding: 30px; 
        background-color: var(--ws-bg-page) !important; 
    }
    
    .info-group label { 
        display: block; 
        color: #94a3b8; 
        font-size: 0.85rem; 
        font-weight: 500; 
        margin-bottom: 8px; 
    }
    .info-val { 
        color: var(--ws-text-primary); 
        font-size: 1.1rem; 
        font-weight: 600; 
    }
    .message-box { 
        background: rgba(15, 23, 42, 0.6); 
        border: 1px solid rgba(255,255,255,0.08); 
        border-radius: 12px; 
        padding: 15px; 
        color: #94a3b8; 
        line-height: 1.7; 
    }

    .admin-panel { 
        background: rgba(255, 255, 255, 0.02); 
        border-radius: 20px; 
        padding: 25px; 
        border: 1px solid rgba(255, 255, 255, 0.05); 
    }
    .panel-title {
        color: var(--ws-text-primary) !important;
        font-weight: 700;
        border-right: 4px solid #0066ff;
        padding-right: 15px;
    }
    .dark-input { 
        background: rgba(15, 23, 42, 0.8) !important; 
        border: 1px solid rgba(255, 255, 255, 0.1) !important; 
        color: var(--ws-text-primary) !important; 
        border-radius: 12px !important; 
        padding: 12px !important; 
    }
    .btn-save-premium-success { 
        background: #00d1b2; 
        color: var(--ws-text-primary); 
        border: none; 
        border-radius: 12px; 
        padding: 12px 35px; 
        font-weight: 700; 
        transition: 0.3s; 
    }
    .btn-save-premium-success:hover { 
        background: #00bfa5; 
        transform: translateY(-2px); 
        box-shadow: 0 10px 20px rgba(0, 209, 178, 0.3); 
    }
    .btn-delete-danger { 
        background: #363636; 
        color: #f8fafc; 
        border-radius: 12px;
        padding: 12px 20px;
        font-weight: 600; 
        transition: 0.3s; 
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-delete-danger:hover {
        background: #ef4444;
        color: var(--ws-text-primary);
    }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }
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



