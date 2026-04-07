@extends('layouts.app')

@section('title', 'المتبرعين المسجلين من الموبايل')

@section('content')
<div class="donors-page">
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
                            <li class="breadcrumb-item active text-white">إدارة المتبرعين</li>
                        </ol>
                    </nav>
                    <h1 class="display-4 fw-bold text-white mb-2">المتبرعين المسجلين من الموبايل</h1>
                    <p class="lead text-white-50">عرض الأشخاص الذين قاموا بإنشاء حسابات عبر تطبيق الهاتف الذكي</p>
                </div>
                <div class="icon-box bg-white bg-opacity-10 p-4 rounded-4">
                    <i class="bi bi-person-badge-fill fs-1 text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="container-fluid pt-4 px-4 mb-4">
        <div class="row g-3">
            <div class="col-md-4 col-sm-6">
                <div class="stat-card glass-card p-4 animate-up" style="border-right: 5px solid #3b82f6;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="icon-circle bg-primary bg-opacity-10 text-primary" style="width: 60px; height: 60px; font-size: 1.8rem;">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div>
                            <div class="text-white-50 small fw-bold text-uppercase mb-2" style="letter-spacing: 1px;">إجمالي المسجلين (الموبايل)</div>
                            <div class="fs-1 fw-bold text-white lh-1">{{ $donors->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        <div class="glass-card overflow-hidden">
            <div class="p-4 border-bottom border-white border-opacity-5 d-flex justify-content-between align-items-center bg-white bg-opacity-5">
                <h5 class="mb-0 fw-bold text-white"><i class="bi bi-list-stars me-2 text-primary"></i> بيانات المتبرعين ومسجلين التطبيق</h5>
                <span class="badge bg-primary bg-opacity-20 text-primary border border-primary border-opacity-20 rounded-pill px-3">مزامنة مباشر</span>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-white">
                    <thead class="bg-white bg-opacity-5">
                        <tr>
                            <th class="py-4 ps-4" style="width: 30%;">المتبرع</th>
                            <th class="py-4" style="width: 25%;">رقم الهاتف</th>
                            <th class="py-4 text-center" style="width: 15%;">تاريخ الانضمام</th>
                            <th class="py-4 text-center" style="width: 15%;">الحالة</th>
                            <th class="py-4 text-center" style="width: 15%;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($donors as $donor)
                        <tr class="border-white border-opacity-5">
                            <td class="py-4 ps-4">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="avatar-text bg-primary bg-opacity-20 text-primary rounded-circle">
                                        {{ mb_substr($donor->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold fs-5 text-white">{{ $donor->name }}</div>
                                        <div class="text-muted small">ID: #{{ $donor->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="mb-1 text-white fs-5"><i class="bi bi-telephone-fill me-2 text-primary"></i>{{ $donor->phone ?? 'لا يوجد هاتف' }}</div>
                            </td>
                            <td class="text-center">
                                <div class="fw-bold text-emerald-400">{{ $donor->created_at->format('Y-m-d') }}</div>
                                <div class="small text-muted">{{ $donor->created_at->format('h:i A') }}</div>
                            </td>
                            <td class="text-center">
                                @if(isset($donor->active) ? $donor->active : true)
                                    <span class="badge rounded-pill px-3 py-2 bg-success bg-opacity-20 text-success border border-success border-opacity-20 d-inline-flex align-items-center">
                                        <span class="d-inline-block bg-success rounded-circle me-2" style="width:6px;height:6px;"></span> نشط
                                    </span>
                                @else
                                    <span class="badge rounded-pill px-3 py-2 bg-danger bg-opacity-20 text-danger border border-danger border-opacity-20 d-inline-flex align-items-center">
                                        <span class="d-inline-block bg-danger rounded-circle me-2" style="width:6px;height:6px;"></span> غير نشط
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-2">
                                    <button class="btn btn-sm btn-outline-light rounded-2 border-opacity-25" style="backdrop-filter: blur(5px);" data-bs-toggle="modal" data-bs-target="#editDonor{{ $donor->id }}">
                                        <i class="bi bi-pencil-square"></i> تعديل
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger rounded-2 border-opacity-25" style="backdrop-filter: blur(5px);" data-bs-toggle="modal" data-bs-target="#deleteDonor{{ $donor->id }}">
                                        <i class="bi bi-trash3-fill"></i> مسح
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Edit Donor Modal --}}
                        <div class="modal fade" id="editDonor{{ $donor->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content glass-card border-0 overflow-hidden shadow-lg" style="border-radius: 24px; background: #111827 !important;">
                                    <div class="modal-header border-0 p-4" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
                                        <div class="z-index-1 d-flex align-items-center justify-content-between w-100">
                                            <div>
                                                <h5 class="modal-title fw-bold text-white mb-0">
                                                    <i class="bi bi-person-gear me-2"></i> تعديل بيانات المتبرع
                                                </h5>
                                                <p class="text-white-50 x-small mb-0 mt-1">تحديث معلومات الحساب وحالة الوصول</p>
                                            </div>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                    </div>
                                    
                                    <form action="{{ route('mobile.donors_auth.update', $donor->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-body p-4">
                                            <div class="mb-4">
                                                <label class="form-label small fw-bold text-white-50 mb-2"><i class="bi bi-person me-1"></i> اسم المتبرع</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark border-0 text-primary" style="border-radius: 0 12px 12px 0;"><i class="bi bi-person-circle"></i></span>
                                                    <input type="text" name="name" class="form-control form-control-lg bg-dark border-0 text-white" value="{{ $donor->name }}" required style="border-radius: 12px 0 0 12px !important;">
                                                </div>
                                            </div>
                                            <div class="mb-4">
                                                <label class="form-label small fw-bold text-white-50 mb-2"><i class="bi bi-telephone me-1"></i> رقم الهاتف</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark border-0 text-primary" style="border-radius: 0 12px 12px 0;"><i class="bi bi-phone-fill"></i></span>
                                                    <input type="text" name="phone" dir="ltr" class="form-control form-control-lg bg-dark border-0 text-white text-end" value="{{ $donor->phone }}" required style="border-radius: 12px 0 0 12px !important;">
                                                </div>
                                            </div>

                                            <div class="mb-4">
                                                <label class="form-label small fw-bold text-white-50 mb-2"><i class="bi bi-key-fill me-1"></i> تعيين كلمة مرور جديدة</label>
                                                <div class="input-group">
                                                    <span class="input-group-text bg-dark border-0 text-primary" style="border-radius: 0 12px 12px 0;"><i class="bi bi-lock-fill"></i></span>
                                                    <input type="password" name="password" class="form-control form-control-lg bg-dark border-0 text-white" placeholder="اتركه فارغاً لعدم التغيير" minlength="6" style="border-radius: 12px 0 0 12px !important;">
                                                </div>
                                            </div>
                                            
                                            <div class="p-3 bg-primary bg-opacity-5 rounded-4 border border-primary border-opacity-10">
                                                <div class="form-check form-switch d-flex align-items-center justify-content-between px-0">
                                                    <div>
                                                        <label class="form-check-label fw-bold text-primary mb-0" for="activeSwitch{{ $donor->id }}">تنشيط الحساب</label>
                                                        <div class="x-small text-white-50">السماح للمستخدم بتسجيل الدخول</div>
                                                    </div>
                                                    <input class="form-check-input ms-0" style="width: 2.8em; height: 1.4em;" type="checkbox" role="switch" name="active" value="1" id="activeSwitch{{ $donor->id }}" {{ (isset($donor->active) ? $donor->active : true) ? 'checked' : '' }}>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer border-0 p-4 bg-dark d-flex gap-2">
                                            <button type="button" class="btn btn-secondary rounded-pill px-4 py-2 fw-bold" data-bs-dismiss="modal" style="background: rgba(255,255,255,0.1); border: none;">إلغاء</button>
                                            <button type="submit" class="btn btn-success rounded-pill px-5 py-3 fw-bold flex-grow-1" style="background: #10b981; border: none;">حفظ التغييرات <i class="bi bi-check-lg ms-2"></i></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        {{-- Delete Donor Modal --}}
                        <div class="modal fade" id="deleteDonor{{ $donor->id }}" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered modal-sm">
                                <div class="modal-content border-0 overflow-hidden shadow-lg" style="border-radius: 24px;">
                                    <div class="modal-body text-center p-5">
                                        <div class="mb-4">
                                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                                                <i class="bi bi-trash3 fs-1"></i>
                                            </div>
                                            <h4 class="fw-bold mb-2">تأكيد الحذف</h4>
                                            <p class="text-muted small">هل أنت متأكد من حذف المتبرع <strong class="text-dark">{{ $donor->name }}</strong> نهائياً؟ هذا الإجراء لا يمكن التراجع عنه.</p>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <form action="{{ route('mobile.donors_auth.destroy', $donor->id) }}" method="POST">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-danger rounded-pill w-100 py-3 fw-bold">نعم، احذف الحساب</button>
                                            </form>
                                            <button type="button" class="btn btn-light rounded-pill w-100 py-3 fw-bold border" data-bs-dismiss="modal">إلغاء</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center text-muted border-0">
                                <i class="bi bi-person-x mb-3 d-block opacity-50" style="font-size: 3rem;"></i>
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

<style>.donors-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { padding: 80px 5%; border-radius: 0 0 40px 40px; position: relative; overflow: hidden; z-index: 1; }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(80px); opacity: 0.15; }
    .glow-orb-1 { width: 300px; height: 300px; top: -50px; right: -50px; }
    .glow-orb-2 { width: 200px; height: 200px; bottom: -50px; left: 50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }
    .icon-box { border: 1px solid rgba(255,255,255,0.1); backdrop-filter: blur(10px); }
    
    .z-index-10 { z-index: 10; }
    .mt-n5 { margin-top: -3.5rem !important; }
    
    .glass-card { background: var(--ws-bg-card-header) !important; border: 1px solid rgba(255,255,255,0.05); border-radius: 20px; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .stat-card { transition: 0.3s; }
    .stat-card:hover { transform: translateY(-5px); border-color: rgba(59, 130, 246, 0.3) !important; box-shadow: 0 10px 25px rgba(59, 130, 246, 0.1); }
    
    .icon-circle { width: 50px; height: 50px; border-radius: 15px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; }
    .avatar-text { width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; font-weight: 800; font-size: 1.2rem; }
    
    .text-indigo-400 { color: #818cf8 !important; }
    .text-emerald-400 { color: #34d399 !important; }
    
    .table > :not(caption) > * > * { background-color: transparent !important; color: inherit; border-bottom-color: rgba(255,255,255,0.05); }
    .table-hover tbody tr:hover { background-color: rgba(255,255,255,0.02) !important; }

    .modal-content { background: white !important; color: var(--ws-border) !important; }
    .form-control-lg { border-radius: 12px !important; font-size: 1rem !important; }
    .input-group-text { border-radius: 12px 0 0 12px !important; }
    .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
    
    .animate-up { animation: fadeInUp 0.5s both; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
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



