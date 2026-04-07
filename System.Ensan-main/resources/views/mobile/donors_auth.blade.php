@extends('layouts.app')

@section('title', 'المتبرعين المسجلين من الموبايل')

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

    /* Premium Hero Header */
    .premium-hero-sleek {
        background: white;
        padding: 3.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        border-radius: 0 0 40px 40px;
        margin-bottom: 2.5rem;
        position: relative;
        overflow: hidden;
    }

    .hero-bg-visuals div {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.08;
    }

    .glow-orb-1 { width: 300px; height: 300px; top: -50px; right: -50px; background: var(--primary-green); }
    .glow-orb-2 { width: 250px; height: 250px; bottom: -50px; left: 50px; background: #3b82f6; }

    .hero-content-wrapper { position: relative; z-index: 5; }

    .header-icon-box {
        width: 70px;
        height: 70px;
        background: rgba(34, 197, 94, 0.1);
        color: var(--primary-green);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
    }

    /* Stat Cards */
    .premium-stat-card {
        background: white;
        border-radius: 24px;
        padding: 1.75rem;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border-right: 5px solid var(--primary-green);
    }

    .premium-stat-card:hover { transform: translateY(-5px); }

    .icon-circle {
        width: 55px;
        height: 55px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0fdf4;
        color: var(--primary-green);
        font-size: 1.5rem;
    }

    /* Table Styling */
    .premium-table-container {
        background: white;
        border-radius: 28px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-top: 2rem;
    }

    .table-header-custom {
        background: #f8fafc;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .table-header-custom h5 { margin: 0; font-weight: 800; color: var(--text-main); }

    .table-premium { margin: 0; }
    .table-premium thead th {
        background: #f8fafc;
        padding: 1.25rem 1rem;
        font-weight: 700;
        color: var(--text-muted);
        text-transform: uppercase;
        font-size: 0.8rem;
        border-bottom: 1px solid var(--border-color);
    }

    .table-premium tbody td { padding: 1.5rem 1rem; border-bottom: 1px solid #f1f5f9; }
    .table-premium tbody tr:last-child td { border-bottom: none; }

    .donor-avatar {
        width: 48px;
        height: 48px;
        background: rgba(34, 197, 94, 0.1);
        color: var(--primary-green);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 800;
        font-size: 1.25rem;
    }

    .status-pill {
        padding: 0.4rem 1rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
    }

    .status-active { background: #f0fdf4; color: #16a34a; }
    .status-inactive { background: #fef2f2; color: #dc2626; }

    /* Modal Redesign */
    .modal-content-p { border-radius: 30px; border: none; box-shadow: 0 20px 40px rgba(0,0,0,0.1); overflow: hidden; }
    .modal-header-p { background: var(--primary-green); color: white; padding: 2rem; border: none; }
    .modal-body-p { padding: 2.5rem; background: white; }
    .modal-footer-p { padding: 1.5rem 2rem; background: #f8fafc; border-top: 1px solid var(--border-color); }

    .form-control-p {
        border-radius: 14px;
        border: 1px solid var(--border-color);
        padding: 0.85rem 1.25rem;
        background: #f9fafb;
    }

    .form-control-p:focus {
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }

    .btn-save-donor {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 0.9rem 2.5rem;
        border-radius: 14px;
        font-weight: 800;
        transition: all 0.3s ease;
    }

    .btn-save-donor:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px rgba(34, 197, 94, 0.2);
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(25px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.5s ease forwards; }
</style>

<div class="donors-mgmt-page">
    {{-- Header Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1"></div>
            <div class="glow-orb-2"></div>
        </div>
        <div class="container-fluid hero-content-wrapper">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2 justify-content-end">
                            <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-muted text-decoration-none small">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-success fw-bold small">إدارة المتبرعين</li>
                        </ol>
                    </nav>
                    <h1 class="display-5 fw-800 text-main mb-2">المتبرعين <span style="color: var(--primary-green)">(الموبايل)</span></h1>
                    <p class="text-muted mb-0">عرض وحسابات المتبرعين المسجلين عبر تطبيق الهاتف الذكي</p>
                </div>
                <div class="header-icon-box shadow-sm">
                    <i class="bi bi-person-badge-fill"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="container-fluid px-4 mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="premium-stat-card animate-up">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle shadow-sm">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-muted small fw-800 text-uppercase mb-1" style="letter-spacing: 0.5px;">إجمالي المسجلين</div>
                            <div class="fs-2 fw-800 text-main lh-1">{{ $donors->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 px-4 py-3" style="background: #ecfdf5; color: #065f46; border-right: 6px solid var(--primary-green) !important;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="premium-table-container animate-up" style="animation-delay: 0.1s">
            <div class="table-header-custom">
                <h5><i class="bi bi-list-stars me-2 text-success"></i> قائمة المتبرعين ومستخدمي التطبيق</h5>
                <span class="badge rounded-pill px-3 py-2" style="background: rgba(34, 197, 94, 0.1); color: var(--primary-green); font-weight: 800;">مزامنة الموبايل</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-premium align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="ps-4">المتبرع</th>
                            <th>رقم الهاتف</th>
                            <th class="text-center">تاريخ الانضمام</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donors as $donor)
                        <tr>
                            <td class="ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="donor-avatar">
                                        {{ mb_substr($donor->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-800 text-main fs-6">{{ $donor->name }}</div>
                                        <div class="text-muted small font-outfit">#{{ $donor->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="fw-700 text-main font-outfit fs-6"><i class="bi bi-phone-fill me-2 text-success opacity-75"></i>{{ $donor->phone ?? 'لا يوجد' }}</div>
                            </td>
                            <td class="text-center">
                                <div class="fw-800 text-main font-outfit">{{ $donor->created_at->format('Y-m-d') }}</div>
                                <div class="small text-muted">{{ $donor->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-center">
                                @if(isset($donor->active) ? $donor->active : true)
                                    <span class="status-pill status-active">
                                        <i class="bi bi-check-circle-fill"></i> نشط
                                    </span>
                                @else
                                    <span class="status-pill status-inactive">
                                        <i class="bi bi-x-circle-fill"></i> غير نشط
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-outline-success btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#editDonor{{ $donor->id }}">
                                        <i class="bi bi-pencil-square me-1"></i> تعديل
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#deleteDonor{{ $donor->id }}">
                                        <i class="bi bi-trash3 me-1"></i> مسح
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Donor Modal --}}
                        <div class="modal fade" id="editDonor{{ $donor->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content modal-content-p">
                                    <div class="modal-header modal-header-p">
                                        <h5 class="modal-title fw-800"><i class="bi bi-person-gear me-2"></i> تعديل بيانات المتبرع</h5>
                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form action="{{ route('mobile.donors_auth.update', $donor->id) }}" method="POST">
                                        @csrf @method('PUT')
                                        <div class="modal-body modal-body-p">
                                            <div class="mb-4">
                                                <label class="form-label small fw-800 text-muted mb-2">اسم المتبرع بالكامل</label>
                                                <input type="text" name="name" class="form-control form-control-p" value="{{ $donor->name }}" required>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label small fw-800 text-muted mb-2">رقم الهاتف المسجل</label>
                                                <input type="text" name="phone" dir="ltr" class="form-control form-control-p font-outfit" value="{{ $donor->phone }}" required>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label small fw-800 text-muted mb-2">كلمة مرور جديدة (اختياري)</label>
                                                <input type="password" name="password" class="form-control form-control-p" placeholder="أدخل كلمة المرور الجديدة...">
                                            </div>
                                            <div class="form-check form-switch p-3 bg-light rounded-4">
                                                <input class="form-check-input" type="checkbox" name="active" value="1" id="active{{ $donor->id }}" {{ (isset($donor->active) ? $donor->active : true) ? 'checked' : '' }}>
                                                <label class="form-check-label fw-800 text-success ms-2" for="active{{ $donor->id }}">الحساب نشط (Active Account)</label>
                                            </div>
                                        </div>
                                        <div class="modal-footer modal-footer-p">
                                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-save-donor w-100 flex-grow-1">
                                                <i class="bi bi-check-lg me-1"></i> تحديث الحساب
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Donor Modal --}}
                        <div class="modal fade" id="deleteDonor{{ $donor->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content modal-content-p shadow-lg text-center">
                                    <div class="modal-body p-5">
                                        <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 80px; height: 80px;">
                                            <i class="bi bi-trash3 display-6"></i>
                                        </div>
                                        <h4 class="fw-800 mb-2">تأكيد الحذف</h4>
                                        <p class="text-muted small">هل أنت متأكد من حذف حساب المتبرع <strong>{{ $donor->name }}</strong>؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                                        <div class="d-grid gap-2 mt-4">
                                            <form action="{{ route('mobile.donors_auth.destroy', $donor->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-pill w-100 py-2 fw-800">نعم، احذف الحساب</button>
                                            </form>
                                            <button type="button" class="btn btn-light rounded-pill w-100 py-2 fw-800 border" data-bs-dismiss="modal">إلغاء</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center text-muted border-0">
                                <i class="bi bi-person-x display-1 opacity-25 d-block mb-3"></i>
                                <p class="mb-0 fs-5">لا يوجد متبرعين مسجلين من الموبايل حتى الآن</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
