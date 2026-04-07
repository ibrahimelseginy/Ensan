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

    /* Volunteer Cards */
    .premium-volunteer-card {
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

    .premium-volunteer-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary-green);
        box-shadow: 0 20px 30px -10px rgba(0, 0, 0, 0.1);
    }

    .card-inner-top {
        padding: 2rem;
        flex-grow: 1;
    }

    .card-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.5rem;
    }

    .badge-premium {
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
    }

    .status-new { background: #eff6ff; color: #3b82f6; }
    .status-warn { background: #fffbeb; color: #d97706; }
    .status-success { background: #f0fdf4; color: #16a34a; }
    .status-danger { background: #fef2f2; color: #dc2626; }

    .card-date {
        color: var(--text-muted);
        font-size: 0.8rem;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
    }

    .card-user-info {
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .user-avatar-placeholder {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, var(--primary-green), #059669);
        border-radius: 22px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.25rem;
        font-size: 2rem;
        font-weight: 800;
        color: white;
        box-shadow: 0 10px 20px rgba(34, 197, 94, 0.2);
    }

    .user-name {
        font-weight: 800;
        color: var(--text-main);
        margin-bottom: 0.25rem;
        font-size: 1.25rem;
    }

    .user-phone {
        color: var(--primary-green);
        font-size: 1rem;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
    }

    .interest-tag {
        background: #f8fafc;
        border-radius: 14px;
        padding: 0.75rem 1rem;
        color: var(--text-muted);
        font-size: 0.85rem;
        border: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: center;
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
    }

    .btn-details-glow:hover {
        background: var(--primary-green);
        color: white;
        border-color: var(--primary-green);
        transform: scale(1.02);
    }

    .card-inner-bottom {
        padding: 1.25rem;
        background: #f8fafc;
        border-top: 1px solid var(--border-color);
    }

    .btn-action-card {
        border-radius: 12px;
        padding: 0.75rem;
        font-weight: 700;
        font-size: 0.85rem;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .id-card-btn { background: #f1f5f9; color: #475569; border: 1px solid #e2e8f0; }
    .id-card-btn:hover { background: #e2e8f0; transform: translateY(-2px); }
    .cv-btn { background: #fff1f2; color: #e11d48; border: 1px solid #fecaca; }
    .cv-btn:hover { background: #ffe4e6; transform: translateY(-2px); }

    /* Modal Styling */
    .modal-content-premium {
        border-radius: 32px;
        border: none;
        overflow: hidden;
    }

    .modal-header-premium {
        background: var(--primary-green);
        padding: 2rem;
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
        font-size: 1.1rem;
        font-weight: 700;
    }

    .message-box {
        background: #f8fafc;
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        color: var(--text-muted);
        line-height: 1.7;
        font-style: italic;
    }

    .admin-actions-panel {
        background: #fdfdfd;
        border: 2px dashed var(--border-color);
        border-radius: 20px;
        padding: 1.75rem;
        margin-top: 2rem;
    }

    .form-select-p {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.75rem;
        background: white;
        font-weight: 700;
    }

    .btn-save-p {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 0.85rem 2.5rem;
        border-radius: 12px;
        font-weight: 800;
        transition: all 0.3s ease;
    }

    .btn-save-p:hover {
        background: var(--primary-hover);
        box-shadow: 0 10px 15px rgba(34, 197, 94, 0.2);
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.6s ease forwards; }
</style>

<div class="container-fluid py-4 min-vh-100">
    {{-- Header --}}
    <div class="premium-header-section animate-up">
        <div class="row align-items-center">
            <div class="col-md-7 text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-muted text-decoration-none small">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active text-success fw-bold small">طلبات التطوع الموبايل</li>
                    </ol>
                </nav>
                <h1 class="h2 fw-800 text-main mb-1">طلبات التطوع <span style="color: var(--primary-green)">(الموبايل)</span></h1>
                <p class="text-muted mb-0">إدارة ومتابعة طلبات الانضمام القادمة من تطبيق الهاتف</p>
            </div>
            <div class="col-md-5 text-start mt-3 mt-md-0">
                <div class="glass-badge px-4 py-2">
                    <i class="bi bi-people-fill me-2 fs-5"></i>
                    <span class="fw-bold">إجمالي الطلبات:</span> <span class="ms-1 fs-5">{{ $requests->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 px-lg-4">
        @forelse($requests as $request)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.08 }}s">
            <div class="premium-volunteer-card">
                <div class="card-inner-top">
                    <div class="card-meta">
                        <span class="badge-premium @if($request->status == 'new') status-new @elseif($request->status == 'contacted') status-warn @elseif($request->status == 'accepted') status-success @else status-danger @endif">
                            {{ $request->status == 'new' ? 'جديد' : ($request->status == 'contacted' ? 'تم التواصل' : ($request->status == 'accepted' ? 'مقبول' : 'مرفوض')) }}
                        </span>
                        <div class="card-date">
                            <i class="bi bi-calendar3 me-1"></i> {{ $request->created_at->format('Y-m-d') }}
                        </div>
                    </div>
                    
                    <div class="card-user-info">
                        <div class="user-avatar-placeholder">
                            {{ mb_substr($request->name, 0, 1) }}
                        </div>
                        <h4 class="user-name text-truncate" title="{{ $request->name }}">{{ $request->name }}</h4>
                        <p class="user-phone">{{ $request->phone }}</p>
                    </div>

                    @if($request->area_of_interest)
                    <div class="interest-tag mb-4">
                        <i class="bi bi-bookmark-star-fill text-success me-2"></i> {{ $request->area_of_interest }}
                    </div>
                    @endif

                    <div class="mt-2">
                        <button class="btn btn-details-glow w-100" data-bs-toggle="modal" data-bs-target="#modal{{ $request->id }}">
                            <i class="bi bi-eye-fill me-2"></i> عرض كامل التفاصيل
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom">
                    <div class="row g-2">
                        <div class="col-6">
                            @if($request->id_card_path)
                            <a href="{{ Storage::disk('public')->url($request->id_card_path) }}" target="_blank" class="btn-action-card id-card-btn w-100">
                                <i class="bi bi-person-vcard"></i> البطاقة
                            </a>
                            @else
                            <button disabled class="btn-action-card w-100 text-muted border-0 bg-light">مفقود</button>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($request->cv_path)
                            <a href="{{ route('mobile.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn-action-card cv-btn w-100">
                                <i class="bi bi-file-earmark-pdf-fill"></i> السيرة الذاتية
                            </a>
                            @else
                            <button disabled class="btn-action-card w-100 text-muted border-0 bg-light">مفقود</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div class="modal fade" id="modal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modal-content-premium">
                    <div class="modal-header modal-header-premium">
                        <h5 class="modal-title fw-800">
                            <i class="bi bi-person-lines-fill me-2"></i> تفاصيل مقدم طلب التطوع
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body modal-body-premium">
                        <div class="row g-4">
                            {{-- Basic Info --}}
                            <div class="col-md-4 info-group">
                                <label>الإسم بالكامل</label>
                                <div class="info-val">{{ $request->name }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>رقم الهاتف</label>
                                <div class="info-val" style="color: var(--primary-green);">{{ $request->phone }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>البريد الإلكتروني</label>
                                <div class="info-val text-truncate">{{ $request->email ?? '-' }}</div>
                            </div>

                            <div class="col-md-4 info-group">
                                <label>الرقم القومي</label>
                                <div class="info-val font-outfit">{{ $request->national_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>تاريخ الميلاد</label>
                                <div class="info-val">{{ $request->birth_date ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>النوع</label>
                                <div class="info-val">{{ $request->gender == 'male' ? 'ذكر' : ($request->gender == 'female' ? 'أنثى' : ($request->gender ?? '-')) }}</div>
                            </div>

                            <div class="col-md-6 info-group">
                                <label>العنوان الأصلي</label>
                                <div class="info-val">{{ $request->address ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label>العنوان الحالي</label>
                                <div class="info-val">{{ $request->current_address ?? '-' }}</div>
                            </div>

                            <div class="col-12"><hr class="opacity-25" style="border-style: dashed;"></div>

                            {{-- Education & Work --}}
                            <div class="col-md-4 info-group">
                                <label>المؤهل الدراسي</label>
                                <div class="info-val">{{ $request->education_level ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>الكلية / التخصص</label>
                                <div class="info-val">{{ $request->faculty ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>الجامعة</label>
                                <div class="info-val">{{ $request->university ?? '-' }}</div>
                            </div>

                            <div class="col-md-6 info-group">
                                <label>الوظيفة الحالية</label>
                                <div class="info-val">{{ $request->current_job ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label>اهتمامات التطوع</label>
                                <div class="info-val text-success">{{ $request->area_of_interest ?? '-' }}</div>
                            </div>

                            <div class="col-12 info-group">
                                <label>الهدف من الانضمام</label>
                                <div class="message-box">
                                    "{{ $request->goal ?? '-' }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-actions-panel">
                            <h6 class="mb-4 fw-800 text-main"><i class="bi bi-shield-lock-fill me-2 text-success"></i> إجراءات الإدارة</h6>
                            <form action="{{ route('mobile.volunteer-requests.update', $request->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row align-items-end g-3">
                                    <div class="col-md-6">
                                        <label class="form-label-p x-small fw-bold text-muted text-uppercase mb-2">تحديث حالة الطلب</label>
                                        <select name="status" class="form-select form-select-p w-100">
                                            <option value="new" {{ $request->status == 'new' ? 'selected' : '' }}>جديد (New)</option>
                                            <option value="contacted" {{ $request->status == 'contacted' ? 'selected' : '' }}>تم التواصل (Contacted)</option>
                                            <option value="accepted" {{ $request->status == 'accepted' ? 'selected' : '' }}>مقبول (Accepted)</option>
                                            <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 d-flex gap-2">
                                        <button type="submit" class="btn btn-save-p flex-grow-1">
                                            <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                                        </button>
                                        <button type="button" class="btn btn-outline-danger border-0 rounded-3" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب؟')) document.getElementById('del-form-{{ $request->id }}').submit()">
                                            <i class="bi bi-trash3 fs-5"></i>
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <form id="del-form-{{ $request->id }}" action="{{ route('mobile.volunteer-requests.destroy', $request->id) }}" method="POST" class="d-none">
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
                <i class="bi bi-inbox display-1 text-muted opacity-25"></i>
                <h5 class="text-muted mt-4">لا يوجد طلبات حالياً</h5>
                <p class="text-muted small">لم يتم استلام أي طلبات تطوع عبر التطبيق حتى اللحظة.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
