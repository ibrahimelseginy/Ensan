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
        --danger: #ef4444;
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
        background: rgba(239, 68, 68, 0.1);
        color: var(--danger);
        padding: 0.6rem 1.25rem;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.9rem;
        margin-top: 1rem;
    }

    /* Case Application Cards */
    .premium-case-card {
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

    .premium-case-card:hover {
        transform: translateY(-10px);
        border-color: var(--danger);
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

    .status-pending { background: #f1f5f9; color: #64748b; }
    .status-review { background: #fffbeb; color: #d97706; }
    .status-success { background: #f0fdf4; color: #16a34a; }
    .status-danger { background: #fef2f2; color: #dc2626; }

    .case-type-pill {
        color: var(--danger);
        font-size: 0.8rem;
        font-weight: 800;
        background: rgba(239, 68, 68, 0.05);
        padding: 0.4rem 1rem;
        border-radius: 100px;
        border: 1px solid rgba(239, 68, 68, 0.1);
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
        color: var(--danger);
        font-size: 1rem;
        font-family: 'Outfit', sans-serif;
        font-weight: 600;
    }

    .location-tag {
        color: var(--text-muted);
        font-size: 0.8rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin-top: 0.5rem;
    }

    .description-box {
        background: #f8fafc;
        border-radius: 16px;
        padding: 1.25rem;
        color: var(--text-muted);
        font-size: 0.9rem;
        border: 1px solid var(--border-color);
        min-height: 80px;
        line-height: 1.6;
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
        background: var(--danger);
        color: white;
        border-color: var(--danger);
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
        font-size: 0.8rem;
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
    .report-btn { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .report-btn:hover { background: #fee2e2; transform: translateY(-2px); }

    /* Modal Styling */
    .modal-content-premium {
        border-radius: 32px;
        border: none;
        overflow: hidden;
    }

    .modal-header-premium {
        background: #0066ff;
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
    }

    .admin-decisions-panel {
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

    .btn-save-decision {
        background: #00d1b2;
        color: white;
        border: none;
        padding: 0.85rem 2.5rem;
        border-radius: 12px;
        font-weight: 800;
        transition: all 0.3s ease;
    }

    .btn-save-decision:hover {
        background: #00bfa5;
        box-shadow: 0 10px 15px rgba(0, 209, 178, 0.2);
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.6s ease forwards; }
</style>

<div class="container-fluid py-4 min-vh-100">
    {{-- Header Content --}}
    <div class="premium-header-section animate-up">
        <div class="row align-items-center">
            <div class="col-md-7 text-end">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-2 justify-content-end">
                        <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-muted text-decoration-none small">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active text-danger fw-bold small">طلبات الحالات (الموبايل)</li>
                    </ol>
                </nav>
                @if(isset($type) && $type == 'zad')
                    <h1 class="h2 fw-800 text-main mb-1">طلبات مشروع <span style="color: var(--danger)">زاد الأيتام</span></h1>
                    <p class="text-muted mb-0 small">إدارة طلبات المساعدة القادمة من تطبيق الهاتف</p>
                @elseif(isset($type) && $type == 'hope')
                    <h1 class="h2 fw-800 text-main mb-1">طلبات مشروع <span style="color: var(--danger)">بعثاء الأمل</span></h1>
                    <p class="text-muted mb-0 small">إدارة طلبات المساعدة لبعثاء الأمل من التطبيق</p>
                @else
                    <h1 class="h2 fw-800 text-main mb-1">طلبات الحالات <span style="color: var(--danger)">المستحقة</span></h1>
                    <p class="text-muted mb-0 small">إدارة طلبات المساعدة (زاد، الأمل، وغيرها) القادمة من الموبايل</p>
                @endif
            </div>
            <div class="col-md-5 text-start mt-3 mt-md-0">
                <div class="glass-badge px-4 py-2">
                    <i class="bi bi-heart-pulse-fill me-2 fs-5"></i>
                    <span class="fw-bold">إجمالي الحالات:</span> <span class="ms-1 fs-5">{{ $applications->count() }}</span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 px-lg-4">
        @forelse($applications as $app)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-case-card">
                <div class="card-inner-top">
                    <div class="card-meta">
                        <span class="badge-premium @if($app->status == 'pending') status-pending @elseif($app->status == 'reviewed') status-review @elseif($app->status == 'accepted') status-success @else status-danger @endif">
                            {{ $app->status == 'pending' ? 'بانتظار المراجعة' : ($app->status == 'reviewed' ? 'قيد الدراسة' : ($app->status == 'accepted' ? 'مقبول' : 'مرفوض')) }}
                        </span>
                        <div class="case-type-pill text-uppercase">
                            @if($app->case_type == 'zad')
                                <i class="bi bi-star-fill me-1"></i> زاد
                            @elseif($app->case_type == 'hope')
                                <i class="bi bi-brightness-high-fill me-1"></i> الأمل
                            @else
                                <i class="bi bi-folder-fill me-1"></i> {{ $app->case_type }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-user-info">
                        <h4 class="user-name text-truncate" title="{{ $app->applicant_name }}">{{ $app->applicant_name }}</h4>
                        <p class="user-phone font-outfit">{{ $app->applicant_phone }}</p>
                        <div class="location-tag">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $app->governorate ?? 'غير محدد' }} - {{ $app->city ?? 'غير محدد' }}
                        </div>
                    </div>

                    <div class="description-box mt-3">
                        <p class="small mb-0">{{ Str::limit($app->description, 100) }}</p>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-details-glow w-100" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}">
                            <i class="bi bi-clipboard2-pulse-fill me-2"></i> مراجعة الطلب بالتفصيل
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom">
                    <div class="row g-2">
                        <div class="col-6">
                            @if($app->id_image_path)
                            <a href="{{ Storage::disk('public')->url($app->id_image_path) }}" target="_blank" class="btn-action-card id-card-btn w-100">
                                <i class="bi bi-person-video"></i> الهوية
                            </a>
                            @else
                            <button disabled class="btn-action-card w-100 text-muted border-0 bg-light">لا توجد</button>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($app->medical_report_path)
                            <a href="{{ Storage::disk('public')->url($app->medical_report_path) }}" target="_blank" class="btn-action-card report-btn w-100">
                                <i class="bi bi-file-medical"></i> التقرير
                            </a>
                            @else
                            <button disabled class="btn-action-card w-100 text-muted border-0 bg-light">لا يوجد</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div class="modal fade" id="modal{{ $app->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content modal-content-premium">
                    <div class="modal-header modal-header-premium shadow-sm">
                        <h5 class="modal-title fw-800">
                            <i class="bi bi-heart-pulse-fill me-2"></i> دراسة حالة مستحقة من التطبيق
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body modal-body-premium">
                        <div class="row g-4 mb-4 pb-2 border-bottom border-light">
                            <div class="col-md-6 info-group">
                                <label>إسم مقدم الطلب</label>
                                <div class="info-val">{{ $app->applicant_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label>رقم الهاتف</label>
                                <div class="info-val font-outfit" style="color: #0066ff;">{{ $app->applicant_phone }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>نوع المشروع</label>
                                <div class="info-val text-uppercase text-danger fw-800">{{ $app->case_type }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>المحافظة</label>
                                <div class="info-val">{{ $app->governorate ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label>المدينة / المركز</label>
                                <div class="info-val">{{ $app->city ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label>العنوان التفصيلي</label>
                                <div class="info-val small">{{ $app->address ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label>وصف الحالة والإحتياجات الاجتماعية / الصحية</label>
                                <div class="message-box">
                                    {{ $app->description }}
                                </div>
                            </div>
                        </div>

                        <div class="admin-decisions-panel">
                            <h6 class="mb-4 fw-800 text-main"><i class="bi bi-shield-check-fill me-2 text-primary"></i> لجنة مراجعة الحالات</h6>
                            <form action="{{ route('mobile.case-applications.update', $app->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold text-muted mb-2">تحديث الحالة</label>
                                        <select name="status" class="form-select form-select-p">
                                            <option value="pending" {{ $app->status == 'pending' ? 'selected' : '' }}>بانتظار المراجعة</option>
                                            <option value="reviewed" {{ $app->status == 'reviewed' ? 'selected' : '' }}>قيد الدراسة</option>
                                            <option value="accepted" {{ $app->status == 'accepted' ? 'selected' : '' }}>مقبول للتمويل</option>
                                            <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>مرفوض (عدم استحقاق)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold text-muted mb-2">ملاحظات الإدارة / الباحث</label>
                                        <textarea name="admin_notes" class="form-control form-select-p" rows="3" placeholder="أدخل تفاصيل التقييم هنا...">{{ $app->admin_notes }}</textarea>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between align-items-center">
                                        <button type="submit" class="btn btn-save-decision">
                                            <i class="bi bi-save me-1"></i> حفظ القرار النهائي
                                        </button>
                                        <button type="button" class="btn btn-link text-danger text-decoration-none fw-bold" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب؟')) document.getElementById('del-form-{{ $app->id }}').submit()">
                                            <i class="bi bi-trash3 me-1"></i> حذف الطلب نهائياً
                                        </button>
                                    </div>
                                </div>
                            </form>
                            <form id="del-form-{{ $app->id }}" action="{{ route('mobile.case-applications.destroy', $app->id) }}" method="POST" class="d-none">
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
                <i class="bi bi-clipboard-x display-1 text-muted opacity-25"></i>
                <h5 class="text-muted mt-4">لا توجد طلبات حالات بانتظار الدراسة</h5>
                <p class="text-muted small">لم يتم تقديم أي طلبات للحالات المستحقة عبر التطبيق حتى اللحظة.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

@endsection
