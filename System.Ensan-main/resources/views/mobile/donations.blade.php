@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-green: #22c55e;
        --primary-hover: #16a34a;
        --bg-light: #f9fafb;
        --text-main: #111111;
        --text-muted: #64748b;
        --border-color: #e5e7eb;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--bg-light) !important;
        color: var(--text-main);
        font-family: 'Tajawal', sans-serif;
    }

    /* Page Header */
    .premium-header-section {
        background: white;
        padding: 3rem 2rem;
        border-radius: 0 0 40px 40px;
        box-shadow: var(--card-shadow);
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 3rem;
    }

    .glass-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(34, 197, 94, 0.1);
        color: var(--primary-green);
        padding: 0.6rem 1.25rem;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.9rem;
        margin-top: 1rem;
    }

    /* Donation Cards */
    .premium-donation-card {
        background: white;
        border-radius: 28px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }

    .premium-donation-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary-green);
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .card-inner-top {
        padding: 2.25rem;
        flex-grow: 1;
    }

    .card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.75rem;
    }

    .badge-premium {
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-pending { background: #f1f5f9; color: #64748b; }
    .status-success { background: #f0fdf4; color: #16a34a; }
    .status-danger { background: #fef2f2; color: #dc2626; }

    .amount-badge {
        background: #f0fdf4;
        color: #16a34a;
        padding: 0.5rem 1.25rem;
        border-radius: 14px;
        font-weight: 800;
        font-family: 'Outfit', sans-serif;
        font-size: 1.3rem;
        box-shadow: 0 2px 4px rgba(22, 163, 74, 0.05);
    }

    .card-user-info {
        margin-bottom: 1.5rem;
    }

    .user-name {
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.25rem;
        font-size: 1.25rem;
    }

    .user-phone {
        color: var(--text-muted);
        font-size: 0.95rem;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
    }

    .donation-for-tag {
        font-size: 0.85rem;
        color: var(--text-muted);
        margin-top: 0.75rem;
        display: flex;
        align-items: center;
        gap: 0.4rem;
    }

    .payment-method-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1.25rem;
        border: 1px solid var(--border-color);
        margin-top: 1.5rem;
    }

    .method-label {
        font-size: 0.75rem;
        font-weight: 800;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 0.4rem;
        display: block;
    }

    .method-val {
        color: var(--primary-green);
        font-weight: 700;
        font-size: 0.95rem;
    }

    .btn-details-glow {
        background: white;
        color: var(--text-main);
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 0.9rem;
        font-weight: 800;
        transition: all 0.3s ease;
        font-size: 0.9rem;
        margin-top: 1.75rem;
    }

    .btn-details-glow:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
        transform: scale(1.02);
    }

    /* Modal Styling */
    .modal-content-premium {
        border-radius: 32px;
        border: none;
        overflow: hidden;
    }

    .modal-header-premium {
        background: #0066ff;
        padding: 2.25rem;
        color: white;
        border: none;
    }

    .modal-body-premium {
        padding: 2.5rem;
        background: white;
    }

    .info-group label {
        display: block;
        color: var(--text-muted);
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
        letter-spacing: 1px;
    }

    .info-val {
        color: var(--text-main);
        font-size: 1.15rem;
        font-weight: 700;
    }

    .receipt-preview-container {
        border-radius: 20px;
        overflow: hidden;
        border: 2px dashed #0066ff;
        background: #f8fafc;
        position: relative;
        padding: 1rem;
    }

    .receipt-preview-container img {
        max-height: 480px;
        width: 100%;
        object-fit: contain;
        border-radius: 12px;
        cursor: zoom-in;
        transition: transform 0.4s ease;
    }

    .receipt-preview-container img:hover {
        transform: scale(1.02);
    }

    .no-receipt-placeholder {
        padding: 3rem 1rem;
        text-align: center;
        background: #f1f5f9;
        border: 2px dashed #cbd5e1;
        border-radius: 20px;
    }

    .admin-actions-panel {
        background: #f9fafb;
        border: 1px solid var(--border-color);
        border-radius: 22px;
        padding: 2rem;
        margin-top: 2.5rem;
    }

    .form-select-p {
        border-radius: 14px;
        border: 1px solid var(--border-color);
        padding: 0.85rem;
        background: white;
        font-weight: 700;
    }

    .btn-save-p {
        background: #00d1b2;
        color: white;
        border: none;
        padding: 1rem 3rem;
        border-radius: 14px;
        font-weight: 800;
        transition: all 0.3s ease;
    }

    .btn-save-p:hover {
        background: #00bfa5;
        box-shadow: 0 10px 15px rgba(0, 209, 178, 0.2);
        transform: translateY(-2px);
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.6s ease forwards; }
</style>

<div class="container-fluid py-4 min-vh-100">
    {{-- Header Section --}}
    <div class="premium-header-section animate-up">
        <div class="row align-items-center">
            <div class="col-md-7 text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-muted text-decoration-none small">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active text-success fw-bold small">سجلات تبرع الموبايل</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-800 text-main mb-1">سجلات التبرع <span style="color: var(--primary-green)">(الموبايل)</span></h1>
                <p class="text-muted mb-0 small">متابعة عمليات التبرع الواردة من تطبيق الموبايل والحالات الخاصة بها</p>
            </div>
            <div class="col-md-5 text-start mt-3 mt-md-0">
                <div class="glass-badge px-4 py-2">
                    <i class="bi bi-cash-stack me-2 fs-5"></i>
                    <span class="fw-bold">إجمالي العمليات:</span> <span class="ms-1 fs-5">{{ $donations->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 px-lg-4">
        @forelse($donations as $donation)
        <div class="col-md-6 col-lg-4 animate-up" style="animation-delay: {{ $loop->index * 0.08 }}s">
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
                        <div class="donation-for-tag">
                            <i class="bi bi-bullseye text-success me-1"></i> التبرع لـ: <span class="text-main fw-bold">{{ $donation->donation_for }}</span>
                        </div>
                    </div>

                    <div class="payment-method-box">
                        <span class="method-label">طريقة الدفع المختارة</span>
                        <div class="method-val">
                            <i class="bi bi-credit-card-2-front-fill me-2 opacity-75"></i> {{ $donation->payment_method }}
                        </div>
                    </div>

                    <button class="btn btn-details-glow w-100" data-bs-toggle="modal" data-bs-target="#modal{{ $donation->id }}">
                        <i class="bi bi-receipt me-2"></i> عرض تفاصيل المتبرع والوصل
                    </button>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div class="modal fade" id="modal{{ $donation->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modal-content-premium">
                    <div class="modal-header modal-header-premium shadow-sm">
                        <h5 class="modal-title fw-800">
                            <i class="bi bi-cash-coin me-2"></i> تفاصيل تبرع الموبايل #{{ $donation->id }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body modal-body-premium">
                        <div class="row g-4 mb-4 pb-4 border-bottom">
                            <div class="col-md-6 info-group">
                                <label>إسم المتبرع</label>
                                <div class="info-val">{{ $donation->donor_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label>رقم هاتف المتبرع</label>
                                <div class="info-val font-outfit" style="color: #0066ff;">{{ $donation->donor_phone }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label>عنوان المتبرع</label>
                                <div class="info-val">{{ $donation->donor_address ?? 'غير متوفر في ملف التبرع' }}</div>
                            </div>
                            
                            <div class="col-md-6 info-group">
                                <label>إسم الحساب المحول منه</label>
                                <div class="info-val">{{ $donation->account_name ?? 'غير متوفر' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label>رقم الحساب / المحفظة</label>
                                <div class="info-val font-outfit">{{ $donation->account_number ?? 'غير متوفر' }}</div>
                            </div>

                            <div class="col-12 info-group">
                                <label class="mb-3 d-flex align-items-center">
                                    <i class="bi bi-camera-fill me-2 text-primary"></i> صـورة إثبـات التحويـل (الـوصـل)
                                </label>
                                @if($donation->receipt_path)
                                    <div class="receipt-preview-container">
                                        <a href="{{ $donation->image_url }}" target="_blank" class="d-block text-center">
                                            <img src="{{ $donation->image_url }}" alt="Donation Receipt">
                                        </a>
                                        <div class="mt-2 text-center small text-muted">
                                            <i class="bi bi-info-circle me-1"></i> اضغط على الصورة لفتحها في شاشة كاملة
                                        </div>
                                    </div>
                                @else
                                    <div class="no-receipt-placeholder">
                                        <i class="bi bi-image-fill display-5 text-muted opacity-25 mb-3 d-block"></i>
                                        <h6 class="text-muted fw-bold">لم يتم إرفاق صورة إثبات التحويل</h6>
                                        <p class="small text-muted mb-0">المتبرع لم يقم برفع صورة الوصل من الموبايل.</p>
                                    </div>
                                @endif
                            </div>
                            
                            <div class="col-12 info-group">
                                <label>ملاحظات المتبرع</label>
                                <div class="message-box">
                                    "{{ $donation->notes ?? 'لا توجد ملاحظات إضافية.' }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-actions-panel">
                            <h6 class="mb-4 fw-800 text-main"><i class="bi bi-shield-check-fill me-2 text-success"></i> اعتماد عملية التبرع</h6>
                            <form action="{{ route('mobile.donations.update', $donation->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-4 align-items-end">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-2">تحديث الحالة</label>
                                        <select name="status" class="form-select form-select-p">
                                            <option value="pending" {{ $donation->status == 'pending' ? 'selected' : '' }}>بانتظار التأكيد (الوصل تحت المراجعة)</option>
                                            <option value="completed" {{ $donation->status == 'completed' ? 'selected' : '' }}>تم التحصيل بنجاح (معتمد)</option>
                                            <option value="failed" {{ $donation->status == 'failed' ? 'selected' : '' }}>فشلت / ملغاة (غير جاد)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex justify-content-end gap-2">
                                        <button type="submit" class="btn btn-save-p flex-grow-1">
                                            <i class="bi bi-check-lg me-1"></i> حفظ الحالة الجديدة
                                        </button>
                                        <button type="button" class="btn btn-outline-danger border-0 rounded-3 px-3" onclick="if(confirm('هل أنت متأكد من حذف هذا السجل؟')) document.getElementById('del-form-{{ $donation->id }}').submit()">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </button>
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
        <div class="col-12 text-center py-5">
            <div class="bg-white rounded-4 shadow-sm border p-5">
                <i class="bi bi-cash-stack display-1 text-muted opacity-25"></i>
                <h5 class="text-muted mt-4">لا توجد سجلات تبرع بانتظار المراجعة</h5>
                <p class="text-muted small">جميع عمليات التبرع عبر الموبايل تمت معالجتها أو لم يتم التبرع بعد.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
