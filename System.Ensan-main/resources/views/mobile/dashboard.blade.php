@extends('layouts.app')

@section('content')
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);">
    <div class="hero-content">
        <div class="hero-greeting text-white-50">إدارة تطبيق الموبايل API Unit ًں“±</div>
        <h1 class="hero-title">المشاريع والحملات (الموبايل)</h1>
        <p class="hero-subtitle">تخصيص المحتوى والصور التي تظهر لمستخدمي التطبيق وتفعيل/تعطيل العرض</p>
    </div>
</div>

<div class="container-fluid py-4">
    {{-- Quick Access Stats --}}
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <a href="{{ route('mobile.volunteer-requests.index') }}" class="text-decoration-none">
                <div class="glass-card p-4 d-flex align-items-center gap-3 animate-slide-up hover-tilt">
                    <div class="rounded-4 bg-primary bg-opacity-10 p-3">
                        <i class="bi bi-person-heart fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">طلبات التطوع</h5>
                        <p class="text-muted small mb-0">إدارة طلبات المتطوعين من التطبيق</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('mobile.cases.index') }}" class="text-decoration-none">
                <div class="glass-card p-4 d-flex align-items-center gap-3 animate-slide-up hover-tilt" style="animation-delay: 0.1s">
                    <div class="rounded-4 bg-info bg-opacity-10 p-3">
                        <i class="bi bi-file-earmark-medical fs-2 text-info"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">طلبات الحالات</h5>
                        <p class="text-muted small mb-0">مراجعة طلبات المساعدات الميدانية</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('mobile.inkind.index') }}" class="text-decoration-none">
                <div class="glass-card p-4 d-flex align-items-center gap-3 animate-slide-up hover-tilt" style="animation-delay: 0.2s">
                    <div class="rounded-4 bg-warning bg-opacity-10 p-3">
                        <i class="bi bi-box-seam fs-2 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">تبرعات عينية</h5>
                        <p class="text-muted small mb-0">إدارة تبرعات (ملابس، أثاث، إلخ)</p>
                    </div>
        <div class="col-md-3">
            <a href="{{ route('mobile.contact_info.index') }}" class="text-decoration-none">
                <div class="glass-card p-4 d-flex align-items-center gap-3 animate-slide-up hover-tilt" style="animation-delay: 0.3s">
                    <div class="rounded-4 bg-success bg-opacity-10 p-3">
                        <i class="bi bi-telephone-outbound fs-2 text-success"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">إدارة تواصل معنا</h5>
                        <p class="text-muted small mb-0">تعديل الأرقام والأسماء</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('mobile.bookings.index') }}" class="text-decoration-none">
                <div class="glass-card p-4 d-flex align-items-center gap-3 animate-slide-up hover-tilt" style="animation-delay: 0.4s">
                    <div class="rounded-4 bg-info bg-opacity-10 p-3">
                        <i class="bi bi-building-check fs-2 text-info"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">طلبات الحجز (موبايل)</h5>
                        <p class="text-muted small mb-0">مراجعة والرد على طلبات حجز الغرف الواردة عبر الموبايل</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="{{ route('mobile.home_content.index') }}" class="text-decoration-none">
                <div class="glass-card p-4 d-flex align-items-center gap-3 animate-slide-up hover-tilt" style="animation-delay: 0.5s">
                    <div class="rounded-4 bg-danger bg-opacity-10 p-3">
                        <i class="bi bi-layout-wysiwyg fs-2 text-primary"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">محتوى الصفحة الرئيسية</h5>
                        <p class="text-muted small mb-0">تخصيص الأقسام والكروت الأساسية</p>
                    </div>
                </div>
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Projects Section --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                 <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-grid-3x3-gap me-2 text-info"></i> مشاريع التطبيق</h5>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>المشروع</th>
                                    <th>الحالة على التطبيق</th>
                                    <th>محتوى مخصص</th>
                                    <th class="text-center">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded border bg-light overflow-hidden" style="width: 50px; height: 50px;">
                                                @if($project->image_path)
                                                    <img src="{{ $project->image_url }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                                @endif
                                            </div>
                                            <span class="fw-bold">{{ $project->name }}</span>
                                        </div>
                                    </td>
                                    <td>
                                         @if($project->show_on_mobile)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">نشط <i class="bi bi-check-circle ms-1"></i></span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">مخفي <i class="bi bi-eye-slash ms-1"></i></span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($project->mobile_content)
                                            <span class="text-success"><i class="bi bi-file-earmark-text"></i> يوجد</span>
                                        @else
                                            <span class="text-muted small">افتراضي (نفس الويب)</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editProjectMobile{{ $project->id }}">
                                            تخصيص للموبايل <i class="bi bi-phone ms-1"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Campaigns Section --}}
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up animate-delay-1">
                 <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-megaphone me-2 text-warning"></i> حملات التبرع (App)</h5>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>الحملة</th>
                                    <th>مقياس التقدم</th>
                                    <th>الحالة</th>
                                    <th class="text-center">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($campaigns as $campaign)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                             <div class="rounded border bg-light overflow-hidden" style="width: 50px; height: 50px;">
                                                @if($campaign->image_path)
                                                    <img src="{{ $campaign->image_url }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-image"></i></div>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $campaign->name }}</div>
                                                <span class="x-small text-muted">{{ $campaign->remaining_days ?? 0 }} يوم متبقي</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td style="width: 30%">
                                        <div class="d-flex justify-content-between x-small mb-1">
                                            <span>{{ number_format($campaign->current_amount) }}</span>
                                            <span class="text-muted">من {{ number_format($campaign->goal_amount) }}</span>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar bg-warning" style="width: {{ $campaign->goal_amount > 0 ? min(100, ($campaign->current_amount / $campaign->goal_amount) * 100) : 0 }}%"></div>
                                        </div>
                                    </td>
                                    <td>
                                         @if($campaign->show_on_mobile)
                                            <span class="badge bg-success-subtle text-success border border-success-subtle">نشط</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle">مخفي</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editCampMobile{{ $campaign->id }}">
                                            تخصيص <i class="bi bi-phone ms-1"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modals for Projects --}}
@foreach($projects as $project)
    <div class="modal fade" id="editProjectMobile{{ $project->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('mobile.projects.update.mobile', $project) }}" method="POST" class="modal-content border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">إعدادات الموبايل: {{ $project->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-check form-switch mb-4 p-3 bg-dark bg-opacity-25 rounded-3 border border-secondary">
                        <input class="form-check-input ms-0 me-2 float-end" type="checkbox" name="show_on_mobile" id="showProj{{ $project->id }}" {{ $project->show_on_mobile ? 'checked' : '' }} value="1">
                        <label class="form-check-label fw-bold text-white" for="showProj{{ $project->id }}">عرض هذا المشروع في تطبيق الموبايل</label>
                        <div class="small text-muted mt-1 me-4">عند التعطيل لن يظهر المشروع لمستخدمي التطبيق نهائياً.</div>
                    </div>

                    <label class="form-label fw-bold text-primary">وصف مخصص للتطبيق (اختياري)</label>
                    <textarea name="mobile_content" class="form-control" rows="6" placeholder="اكتب وصفاً مختصراً وجذاباً يناسب شاشات الموبايل... (اتركه فارغاً لاستخدام الوصف العام)">{{ $project->mobile_content }}</textarea>
                    <div class="form-text text-muted"><i class="bi bi-info-circle"></i> يفضل أن يكون النص قصيراً ومباشراً لمستخدمي الهواتف.</div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-4 shadow-sm w-100 py-3 fw-bold">حفظ الإعدادات</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

{{-- Modals for Campaigns --}}
@foreach($campaigns as $campaign)
    <div class="modal fade" id="editCampMobile{{ $campaign->id }}" tabindex="-1" style="z-index: 9999;">
        <div class="modal-dialog modal-lg">
            <form action="{{ route('mobile.campaigns.update.mobile', $campaign) }}" method="POST" class="modal-content border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold text-white">إعدادات الموبايل: {{ $campaign->name }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="form-check form-switch mb-4 p-3 bg-dark bg-opacity-25 rounded-3 border border-secondary">
                        <input class="form-check-input ms-0 me-2 float-end" type="checkbox" name="show_on_mobile" id="showCamp{{ $campaign->id }}" {{ $campaign->show_on_mobile ? 'checked' : '' }} value="1">
                        <label class="form-check-label fw-bold text-white" for="showCamp{{ $campaign->id }}">ظهور الحملة في التطبيق</label>
                    </div>

                    <label class="form-label fw-bold text-warning">نص ترويجي للتطبيق (Pitch)</label>
                    <textarea name="mobile_content" class="form-control" rows="6" placeholder="اكتب رسالة قصيرة ومؤثرة تظهر تحت الحملة في التطبيق...">{{ $campaign->mobile_content }}</textarea>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4 shadow-sm w-100 py-3">حفظ الإعدادات</button>
                </div>
            </form>
        </div>
    </div>
@endforeach

<style>
    .glass-card { 
        background: var(--ws-bg-card-header) !important; 
        border: 1px solid var(--ws-border) !important; 
        border-radius: 20px; 
        box-shadow: 0 10px 40px rgba(0,0,0,0.4); 
    }
    .modal-content { 
        background-color: var(--ws-bg-card-header) !important; 
        border: 2px solid #3b82f6 !important; 
        border-radius: 20px !important; 
        color: var(--ws-text-primary) !important;
        box-shadow: 0 0 60px rgba(59, 130, 246, 0.4) !important;
        overflow: hidden;
        opacity: 1 !important;
    }
    .modal-header {
        background: var(--ws-border) !important;
        border-bottom: 1px solid #334155 !important;
        color: var(--ws-text-primary) !important;
        padding: 1.5rem !important;
    }
    .modal-footer {
        background: var(--ws-border) !important;
        border-top: 1px solid #334155 !important;
        padding: 1.2rem !important;
    }
    .modal-body {
        background-color: var(--ws-bg-card-header) !important;
        color: #f8fafc !important;
        opacity: 1 !important;
    }
    .bg-light { background-color: var(--ws-border) !important; }
    .border { border-color: #334155 !important; }
    .text-muted { color: #94a3b8 !important; }
    .form-control {
        background-color: var(--ws-border) !important;
        border: 1px solid #334155 !important;
        color: #f8fafc !important;
        border-radius: 12px !important;
    }
    .form-control:focus {
        background-color: var(--ws-bg-card-header) !important;
        border-color: #3b82f6 !important;
        color: var(--ws-text-primary) !important;
    }
    .modal-backdrop.show { 
        backdrop-filter: blur(10px) !important; 
        -webkit-backdrop-filter: blur(10px) !important; 
        opacity: 0.9 !important; 
        background-color: #000000 !important;
    }
    .x-small { font-size: 0.7rem; }
    .animate-delay-1 { animation-delay: 0.1s; }
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




