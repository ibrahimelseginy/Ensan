@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down px-2">
        <div>
            <h1 class="h2 fw-800 text-stats-main mb-1">سجلات التبرع <span class="text-primary">(الموبايل)</span></h1>
            <p class="text-muted-theme small mb-0">متابعة عمليات التبرع الواردة من تطبيق الموبايل والحالات الخاصة بها</p>
        </div>
        <div class="glass-badge-theme px-4 py-2 d-none d-md-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-primary"></i>
                <span class="small fw-bold">إجمالي العمليات:</span> {{ $donations->count() }}
            </div>
            <div class="vr mx-2 opacity-20"></div>
            <div class="d-flex align-items-center gap-2 text-success">
                <i class="bi bi-shield-check"></i>
                <span class="small fw-bold">تأمين مالي مباشر</span>
            </div>
        </div>
    </div>

    <div class="row g-4">
        @forelse($donations as $donation)
        <div class="col-md-6 col-lg-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-donation-card bg-stats-card-main border-light-subtle">
                <div class="card-inner-top">
                    <div class="card-meta mb-4">
                        <span class="badge-premium @if($donation->status == 'pending') status-pending @elseif($donation->status == 'completed') status-success @else status-danger @endif">
                            {{ $donation->status == 'pending' ? 'بانتظار التأكيد' : ($donation->status == 'completed' ? 'تم التبرع' : 'فشلت / ملغاة') }}
                        </span>
                        <div class="amount-badge-premium bg-stats-inner-item border-light-subtle">
                            <span class="num text-primary fw-bold">{{ number_format($donation->donation_amount, 0) }}</span>
                            <span class="curr x-small text-muted-theme">ج.م</span>
                        </div>
                    </div>
                    
                    <div class="card-user-info mb-4">
                        <h4 class="user-name text-truncate text-stats-main fw-bold" title="{{ $donation->donor_name }}">{{ $donation->donor_name }}</h4>
                        <p class="user-phone font-outfit text-primary fw-bold mb-3">{{ $donation->donor_phone }}</p>
                        <div class="donation-target-box bg-stats-inner-item border border-light-subtle rounded-3 p-2 px-3 d-flex align-items-center gap-2">
                            <i class="bi bi-bullseye text-danger"></i>
                            <span class="small text-muted-theme">التبرع لـ:</span>
                            <span class="small text-stats-main fw-bold">{{ $donation->donation_for }}</span>
                        </div>
                    </div>

                    <div class="payment-method-strip mb-4">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="small text-muted-theme fw-bold">طريقة الدفع</span>
                            <div class="text-stats-main fw-bold x-small">
                                <i class="bi bi-credit-card-2-front me-1 text-primary"></i> {{ $donation->payment_method }}
                            </div>
                        </div>
                    </div>

                    <div class="mt-auto">
                        <button class="btn btn-details-glow w-100 fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal{{ $donation->id }}">
                            <i class="bi bi-eye me-2"></i> عرض تفاصيل العملية
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal{{ $donation->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px; overflow: hidden;">
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-title">
                            <i class="bi bi-cash-coin me-2 text-primary"></i> تفاصيل طلب التبرع (تطبيق الموبايل)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-stats-card-main">
                        <div class="row g-4">
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">إسم المتبرع</label>
                                <div class="text-stats-main fw-bold">{{ $donation->donor_name }}</div>
                            </div>
                            <div class="col-md-6 info-group text-end" dir="ltr">
                                <label class="text-muted-theme small fw-bold mb-2 d-block text-end">رقم الهاتف</label>
                                <div class="font-outfit text-primary fw-bold text-end fs-5">{{ $donation->donor_phone }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">عنوان المتبرع</label>
                                <div class="text-stats-main fw-bold">{{ $donation->donor_address ?? 'غير محدد' }}</div>
                            </div>
                            
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">إسم الحساب (انستاباي/فودافون)</label>
                                <div class="text-stats-main fw-bold">{{ $donation->account_name ?? 'غير متوفر' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">رقم الحساب المحول منه</label>
                                <div class="font-outfit text-stats-main fw-bold fs-5">{{ $donation->account_number ?? 'غير متوفر' }}</div>
                            </div>

                            <div class="col-12 info-group mt-4">
                                <label class="text-muted-theme small fw-bold mb-3 d-block">
                                    <i class="bi bi-image me-1 text-primary"></i> صورة إثبات التحويل (Proof of Transfer)
                                </label>
                                @if($donation->receipt_path)
                                    <div class="receipt-preview-container bg-stats-inner-item border border-light-subtle rounded-4 p-3 overflow-hidden position-relative shadow-sm">
                                        <div class="preview-badge position-absolute top-0 start-0 m-3 z-3">
                                            <span class="badge bg-success rounded-pill fw-bold py-2 px-3 shadow-sm"><i class="bi bi-shield-check me-1"></i> إثبات صالح</span>
                                        </div>
                                        <a href="{{ $donation->image_url }}" target="_blank" class="d-block text-center hover-up">
                                            <img src="{{ $donation->image_url }}" class="img-fluid rounded-3 shadow-lg" style="max-height: 450px; object-fit: contain;" alt="Donation Receipt">
                                        </a>
                                        <div class="mt-3 text-center">
                                            <small class="text-muted-theme"><i class="bi bi-zoom-in me-1"></i> انقر على الصورة للمعاينة الكاملة</small>
                                        </div>
                                    </div>
                                @else
                                    <div class="no-receipt-placeholder p-5 text-center bg-stats-inner-item border border-dashed border-light-subtle rounded-4">
                                        <i class="bi bi-camera-fill display-6 text-muted-theme opacity-30 mb-3 d-block"></i>
                                        <h6 class="text-muted-theme fw-bold">لم يتم إرفاق صورة إثبات التحويل بعد</h6>
                                        <p class="small text-muted-theme opacity-75 mb-0">المتبرع لم يقم برفع صورة الوصل من خلال التطبيق.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-12 info-group mt-2">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">ملاحظات المتبرع</label>
                                <div class="message-box bg-stats-inner-item border border-light-subtle rounded-4 p-4 text-muted-theme italic shadow-inner">
                                    "{{ $donation->notes ?? 'لا توجد ملاحظات إضافية من المتبرع.' }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel mt-5 p-4 rounded-4 bg-stats-inner-item border border-light-subtle">
                            <h6 class="mb-4 text-stats-main fw-bold border-start border-primary border-4 ps-3"><i class="bi bi-shield-lock me-2 text-primary"></i> التحكم في حالة الطلب</h6>
                            <form action="{{ route('mobile.donations.update', $donation->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label x-small text-muted-theme fw-bold mb-2">تحديث حالة المعاملة</label>
                                        <select name="status" class="form-select bg-stats-card-main border-light-subtle text-stats-main rounded-3 p-3 fw-bold">
                                            <option value="pending" {{ $donation->status == 'pending' ? 'selected' : '' }}>بانتظار التأكيد (Pending)</option>
                                            <option value="completed" {{ $donation->status == 'completed' ? 'selected' : '' }}>تم التحصيل بنجاح (Completed)</option>
                                            <option value="failed" {{ $donation->status == 'failed' ? 'selected' : '' }}>فشلت / ملغاة (Failed)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between gap-3">
                                        <button type="submit" class="btn btn-success flex-grow-1 rounded-pill fw-bold py-3 shadow-sm">حفظ وتأكيد الحالة</button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-5 fw-bold py-3" onclick="if(confirm('هل أنت متأكد من حذف هذا السجل؟')) document.getElementById('del-form-{{ $donation->id }}').submit()">حذف</button>
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
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-800 { font-weight: 800; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Theme-Aware Financial Styling */
    .bg-stats-card-main { background-color: #ffffff; }
    .bg-stats-inner-item { background-color: var(--gray-50); }
    .text-stats-main { color: var(--dark); }
    .text-muted-theme { color: var(--gray-500); }
    .bg-stats-header { background-color: var(--gray-50); }
    .text-stats-title { color: var(--dark); }

    body.theme-dark .bg-stats-card-main { background-color: var(--bg-card); }
    body.theme-dark .bg-stats-inner-item { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .text-stats-main { color: #ffffff; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .bg-stats-header { background-color: rgba(255, 255, 255, 0.05); }
    body.theme-dark .text-stats-title { color: #ffffff; }

    /* Custom Elements */
    .glass-badge-theme { background: var(--bg-stats-header); border: 1px solid var(--ws-border); border-radius: 100px; color: var(--ws-text-primary); }
    
    .premium-donation-card { border-radius: 24px; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--ws-border); background: var(--ws-bg-card); }
    .premium-donation-card:hover { transform: translateY(-10px); border-color: var(--success); box-shadow: 0 20px 50px rgba(16, 185, 129, 0.1); }

    .card-inner-top { padding: 24px; flex-grow: 1; display: flex; flex-direction: column; }
    .card-meta { display: flex; justify-content: space-between; align-items: center; }
    
    .badge-premium { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border: 1px solid transparent; }
    .status-pending { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
    .status-success { background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.2); }
    .status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.2); }

    .amount-badge-premium { padding: 12px 20px; border-radius: 16px; display: flex; align-items: baseline; gap: 6px; }
    .amount-badge-premium .num { font-size: 1.4rem; letter-spacing: -1px; }

    .btn-details-glow { background: var(--gray-100); color: var(--dark); border: 1px solid var(--ws-border); border-radius: 12px; transition: 0.3s; }
    body.theme-dark .btn-details-glow { background: rgba(255,255,255,0.05); color: #ffffff; }
    .btn-details-glow:hover { background: var(--success); border-color: var(--success); color: #ffffff; box-shadow: 0 0 20px rgba(16, 185, 129, 0.4); }

    /* Modal Styling */
    .modal-glass-theme { background-color: var(--ws-bg-card) !important; }
    .message-box { line-height: 1.8; position: relative; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    
    .receipt-preview-container img { transition: 0.4s; }
    .hover-up:hover img { transform: translateY(-5px); }

    body.theme-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

    .x-small { font-size: 0.7rem; }
    .italic { font-style: italic; }
</style>
@endsection



