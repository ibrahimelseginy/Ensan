@extends('layouts.app')

@section('title', 'المتبرعين المسجلين من الموبايل')

@section('content')
<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    {{-- Premium Hero Section --}}
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down px-2">
        <div>
            <h1 class="h2 fw-800 text-stats-main mb-1">المتبرعين المسجلين <span class="text-primary">(الموبايل)</span></h1>
            <p class="text-muted-theme small mb-0">إدارة حسابات المتبرعين الذين قاموا بالتسجيل عبر تطبيق الهاتف</p>
        </div>
        <div class="glass-badge-theme px-4 py-2 d-none d-md-flex align-items-center gap-3">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people text-primary"></i>
                <span class="small fw-bold">إجمالي الحسابات:</span> {{ $donors->count() }}
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-4">
        @forelse($donors as $donor)
        <div class="col-md-6 col-lg-4 animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
            <div class="premium-donor-card bg-stats-card-main border-light-subtle shadow-sm">
                <div class="card-inner p-4">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="donor-avatar bg-primary text-white fs-4 fw-bold shadow-sm">
                                {{ mb_substr($donor->name, 0, 1) }}
                            </div>
                            <div>
                                <h5 class="mb-1 fw-bold text-stats-main text-truncate" style="max-width: 150px;">{{ $donor->name }}</h5>
                                <span class="badge bg-stats-inner-item border border-light-subtle text-muted-theme x-small fw-bold">ID: #{{ $donor->id }}</span>
                            </div>
                        </div>
                        @if(isset($donor->active) ? $donor->active : true)
                            <span class="badge-status status-active">
                                <i class="bi bi-check-circle-fill me-1"></i> نشط
                            </span>
                        @else
                            <span class="badge-status status-inactive">
                                <i class="bi bi-x-circle-fill me-1"></i> معطل
                            </span>
                        @endif
                    </div>

                    <div class="donor-info-grid mb-4">
                        <div class="info-item p-3 bg-stats-inner-item rounded-3 border border-light-subtle mb-2">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="x-small text-muted-theme fw-bold">رقم الهاتف</span>
                                <span class="font-outfit text-primary fw-bold">{{ $donor->phone ?? '---' }}</span>
                            </div>
                        </div>
                        <div class="info-item p-3 bg-stats-inner-item rounded-3 border border-light-subtle">
                            <div class="d-flex align-items-center justify-content-between">
                                <span class="x-small text-muted-theme fw-bold">تاريخ الإنضمام</span>
                                <span class="text-stats-main fw-bold small">{{ $donor->created_at->format('Y/m/d') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button class="btn btn-action-primary flex-grow-1" data-bs-toggle="modal" data-bs-target="#editDonor{{ $donor->id }}">
                            <i class="bi bi-pencil-square me-2"></i> تعديل البيانات
                        </button>
                        <button class="btn btn-action-danger p-2 px-3" data-bs-toggle="modal" data-bs-target="#deleteDonor{{ $donor->id }}">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Edit Donor Modal --}}
        <div class="modal fade" id="editDonor{{ $donor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px; overflow: hidden;">
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-title">
                            <i class="bi bi-person-gear me-2 text-primary"></i> تعديل حساب المتبرع
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <form action="{{ route('mobile.donors_auth.update', $donor->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="modal-body p-4 bg-stats-card-main">
                            <div class="mb-4">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">إسم المتبرع</label>
                                <input type="text" name="name" class="form-control premium-field" value="{{ $donor->name }}" required>
                            </div>
                            <div class="mb-4">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">رقم الهاتف</label>
                                <input type="text" name="phone" class="form-control premium-field font-outfit" value="{{ $donor->phone }}" required dir="ltr">
                            </div>
                            <div class="mb-4">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">تغيير كلمة المرور (اختياري)</label>
                                <input type="password" name="password" class="form-control premium-field" placeholder="اتركه فارغاً لعدم التغيير" minlength="6">
                            </div>
                            
                            <div class="p-3 bg-stats-inner-item rounded-4 border border-light-subtle">
                                <div class="form-check form-switch d-flex align-items-center justify-content-between px-0">
                                    <div>
                                        <label class="form-check-label fw-bold text-stats-main mb-0" for="activeSwitch{{ $donor->id }}">تنشيط الحساب</label>
                                        <div class="x-small text-muted-theme">السماح للمستخدم بالوصول للتطبيق</div>
                                    </div>
                                    <input class="form-check-input ms-0 premium-switch" type="checkbox" role="switch" name="active" value="1" id="activeSwitch{{ $donor->id }}" {{ (isset($donor->active) ? $donor->active : true) ? 'checked' : '' }}>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 p-4 bg-stats-card-main d-flex gap-3">
                            <button type="submit" class="btn btn-success flex-grow-1 rounded-pill fw-bold py-3 shadow-sm">حفظ التغييرات</button>
                            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Delete Donor Modal --}}
        <div class="modal fade" id="deleteDonor{{ $donor->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px;">
                    <div class="modal-body text-center p-5 bg-stats-card-main">
                        <div class="mb-4">
                            <div class="icon-warning bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3 shadow-inner" style="width: 80px; height: 80px;">
                                <i class="bi bi-trash3 fs-1"></i>
                            </div>
                            <h4 class="fw-bold text-stats-main mb-2">تأكيد الحذف</h4>
                            <p class="text-muted-theme x-small mb-0">هل أنت متأكد من حذف الحساب لـ <strong class="text-danger">{{ $donor->name }}</strong>؟</p>
                        </div>
                        <div class="d-grid gap-2">
                            <form action="{{ route('mobile.donors_auth.destroy', $donor->id) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger rounded-pill w-100 py-3 fw-bold shadow-sm mb-2">نعم، احذف الحساب</button>
                            </form>
                            <button type="button" class="btn btn-outline-secondary rounded-pill w-100 py-3 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 animate-up">
            <div class="bg-stats-card-main border border-dashed border-light-subtle rounded-4 text-center py-5">
                <i class="bi bi-person-x display-4 text-muted-theme opacity-30"></i>
                <h5 class="text-stats-main mt-4 fw-bold">لا يوجد متبرعين مسجلين حالياً</h5>
                <p class="text-muted-theme opacity-75">لم يقم أحد بالتسجيل في تطبيق الموبايل بعد.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
</div>

<style>
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-800 { font-weight: 800; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Theme-Aware Stats Styling */
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

    /* Donor Card Styling */
    .premium-donor-card { border-radius: 24px; overflow: hidden; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); border: 1px solid var(--ws-border); }
    .premium-donor-card:hover { transform: translateY(-8px); border-color: var(--primary); box-shadow: 0 15px 40px rgba(59, 130, 246, 0.1) !important; }

    .donor-avatar { width: 55px; height: 55px; border-radius: 18px; display: flex; align-items: center; justify-content: center; backdrop-filter: blur(10px); }
    
    .badge-status { padding: 6px 12px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; border: 1px solid transparent; }
    .status-active { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }
    .status-inactive { background: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.2); }

    .btn-action-primary { background: var(--gray-100); color: var(--dark); border-radius: 14px; font-weight: 700; border: 1px solid var(--ws-border); transition: 0.3s; }
    body.theme-dark .btn-action-primary { background: rgba(255,255,255,0.05); color: #ffffff; }
    .btn-action-primary:hover { background: var(--primary); border-color: var(--primary); color: #ffffff; }

    .btn-action-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border-radius: 14px; border: 1px solid rgba(239, 68, 68, 0.1); transition: 0.3s; }
    .btn-action-danger:hover { background: #dc2626; color: #ffffff; }

    /* Modal & Field Styling */
    .modal-glass-theme { background-color: var(--ws-bg-card) !important; }
    .premium-field { background-color: var(--bg-stats-inner-item) !important; border: 1px solid var(--ws-border) !important; color: var(--text-stats-main) !important; border-radius: 14px !important; padding: 14px !important; }
    .premium-field:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important; }
    
    .premium-switch { width: 3.2em !important; height: 1.6em !important; cursor: pointer; }
    .premium-switch:checked { background-color: #10b981 !important; border-color: #10b981 !important; }

    .icon-warning { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

    .glass-badge-theme { background: var(--bg-stats-header); border: 1px solid var(--ws-border); border-radius: 100px; color: var(--ws-text-primary); }
    .x-small { font-size: 0.7rem; }
    body.theme-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
</style>
@endsection



