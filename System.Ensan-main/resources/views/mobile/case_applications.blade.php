@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100" style="background-color: #05070a;">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down">
        <div>
            @if(isset($type) && $type == 'zad')
                <h1 class="h2 fw-800 text-white mb-1">طلبات مشروع زاد <span class="text-danger-glow">(الموبايل)</span></h1>
                <p class="text-white-50 small mb-0">إدارة طلبات المساعدة لمشروع زاد الأيتام القادمة من تطبيق الهاتف</p>
            @elseif(isset($type) && $type == 'hope')
                <h1 class="h2 fw-800 text-white mb-1">طلبات مشروع بعثاء الأمل <span class="text-danger-glow">(الموبايل)</span></h1>
                <p class="text-white-50 small mb-0">إدارة طلبات المساعدة لمشروع بعثاء الأمل القادمة من تطبيق الهاتف</p>
            @else
                <h1 class="h2 fw-800 text-white mb-1">طلبات الحالات المستحقة <span class="text-danger-glow">(الموبايل)</span></h1>
                <p class="text-white-50 small mb-0">إدارة طلبات المساعدة (زاد، الأمل، وغيرها) القادمة من تطبيق الهاتف</p>
            @endif
        </div>
        <div class="glass-badge px-4 py-2">
            <i class="bi bi-heart-fill me-2 text-danger"></i>
            <span class="fw-bold">إجمالي الحالات:</span> {{ $applications->count() }}
        </div>
    </div>

    <div class="row g-4">
        @forelse($applications as $app)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-case-card">
                <div class="card-inner-top">
                    <div class="card-meta">
                        <span class="badge-premium @if($app->status == 'pending') status-pending @elseif($app->status == 'reviewed') status-review @elseif($app->status == 'accepted') status-success @else status-danger @endif">
                            {{ $app->status == 'pending' ? 'بانتظار المراجعة' : ($app->status == 'reviewed' ? 'قيد الدراسة' : ($app->status == 'accepted' ? 'مقبول' : 'مرفوض')) }}
                        </span>
                        <div class="case-type-badge">
                            @if($app->case_type == 'zad')
                                <i class="bi bi-star-fill me-1"></i> زاد الأيتام
                            @elseif($app->case_type == 'hope')
                                <i class="bi bi-brightness-high-fill me-1"></i> بعثاء الأمل
                            @else
                                <i class="bi bi-folder-fill me-1"></i> {{ $app->case_type }}
                            @endif
                        </div>
                    </div>
                    
                    <div class="card-user-info">
                        <h4 class="user-name text-truncate" title="{{ $app->applicant_name }}">{{ $app->applicant_name }}</h4>
                        <p class="user-phone font-outfit">{{ $app->applicant_phone }}</p>
                        <div class="location-tag x-small text-white-50">
                            <i class="bi bi-geo-alt me-1"></i> {{ $app->governorate ?? 'غير محدد' }} - {{ $app->city ?? 'غير محدد' }}
                        </div>
                    </div>

                    <div class="description-box mt-3">
                        <p class="small mb-0">{{ Str::limit($app->description, 120) }}</p>
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-details-glow w-100" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}">
                            <i class="bi bi-file-earmark-medical me-2"></i> مراجعة الطلب بالكامل
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom">
                    <div class="row g-2">
                        <div class="col-6">
                            @if($app->id_image_path)
                            <a href="{{ Storage::disk('public')->url($app->id_image_path) }}" target="_blank" class="btn btn-action-card id-card-btn w-100">
                                <i class="bi bi-person-bounding-box"></i> صورة الهوية
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100">لا توجد صورة</button>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($app->medical_report_path)
                            <a href="{{ Storage::disk('public')->url($app->medical_report_path) }}" target="_blank" class="btn btn-action-card report-btn w-100">
                                <i class="bi bi-file-earmark-medical-fill"></i> التقرير
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100">لا يوجد تقرير</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div class="modal fade" id="modal{{ $app->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="background-color: var(--ws-bg-page) !important; border-radius: 24px !important; overflow: hidden;">
                    <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px !important;">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-heart-pulse-fill me-2"></i> دراسة حالة مستحقة (تطبيق)
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" style="background-color: var(--ws-bg-page) !important;">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">إسم مقدم الطلب</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $app->applicant_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">رقم الهاتف</label>
                                <div class="info-val font-outfit" style="color: #0066ff; font-size: 1.1rem; font-weight: 600; font-family: 'Outfit', sans-serif;">{{ $app->applicant_phone }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">نوع المشروع</label>
                                <div class="info-val text-uppercase" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $app->case_type }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">المحافظة</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $app->governorate ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">المدينة/المركز</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $app->city ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">العنوان بالتفصيل</label>
                                <div class="info-val" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $app->address ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">وصف الحالة والإحتياجات</label>
                                <div class="message-box" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; color: #94a3b8; line-height: 1.7;">
                                    {{ $app->description }}
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel mt-5" style="background: rgba(255, 255, 255, 0.02); border-radius: 20px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.05);">
                            <h6 class="mb-3" style="color: var(--ws-text-primary) !important; font-weight: 700; border-right: 4px solid #0066ff; padding-right: 15px;"><i class="bi bi-shield-lock me-2"></i> قرار الإدارة</h6>
                            <form action="{{ route('mobile.case-applications.update', $app->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small opacity-75" style="color: #94a3b8;">تغيير حالة الطلب</label>
                                        <select name="status" class="form-select" style="background: rgba(15, 23, 42, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: var(--ws-text-primary) !important; border-radius: 12px !important; padding: 12px !important;">
                                            <option value="pending" {{ $app->status == 'pending' ? 'selected' : '' }}>بانتظار المراجعة</option>
                                            <option value="reviewed" {{ $app->status == 'reviewed' ? 'selected' : '' }}>قيد الدراسة</option>
                                            <option value="accepted" {{ $app->status == 'accepted' ? 'selected' : '' }}>مقبول (Accepted)</option>
                                            <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small opacity-75" style="color: #94a3b8;">ملاحظات الباحث الاجتماعي / الإدارة</label>
                                        <textarea name="admin_notes" class="form-control" rows="3" style="background: rgba(15, 23, 42, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: var(--ws-text-primary) !important; border-radius: 12px !important; padding: 12px !important;">{{ $app->admin_notes }}</textarea>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between">
                                        <button type="submit" class="btn" style="background: #00d1b2; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px 35px; font-weight: 700;">حفظ القرار والتعديلات</button>
                                        <button type="button" class="btn" style="background: #363636; color: #f8fafc; border-radius: 12px; padding: 12px 20px; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب؟')) document.getElementById('del-form-{{ $app->id }}').submit()">حذف الطلب</button>
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
        <div class="col-12 animate-up">
            <div class="glass-card text-center py-5">
                <i class="bi bi-heart-break display-4 text-white-50"></i>
                <h5 class="text-white mt-4">لا توجد حالات حالياً</h5>
                <p class="text-white-50">لم يقم أي مستخدم بطلب مساعدة لحالة مستحقة عبر التطبيق بعد.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<style>
    :root {
        --dark-bg: #05070a;
        --card-bg: var(--ws-bg-card-header);
        --card-inner: var(--ws-border);
        --primary: #3b82f6;
        --danger: #ef4444;
        --danger-glow: #f87171;
        --success: #10b981;
        --warning: #f59e0b;
    }

    body { background-color: var(--dark-bg); font-family: 'Tajawal', 'Outfit', sans-serif; }
    .fw-800 { font-weight: 800; }
    .text-danger-glow { color: var(--danger-glow); }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Header & Badge */
    .glass-badge { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 100px; color: var(--ws-text-primary); backdrop-filter: blur(10px); }

    /* Premium Card Design */
    .premium-case-card {
        background: var(--card-bg);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .premium-case-card:hover {
        transform: translateY(-10px);
        border-color: var(--danger);
        box-shadow: 0 20px 50px rgba(239, 68, 68, 0.15);
    }

    .card-inner-top { padding: 24px; flex-grow: 1; }
    .card-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    
    .badge-premium { padding: 6px 14px; border-radius: 100px; font-size: 0.7rem; font-weight: 700; }
    .status-pending { background: rgba(255,255,255,0.1); color: var(--ws-text-primary); }
    .status-review { background: rgba(245, 158, 11, 0.15); color: #fbbf24; }
    .status-success { background: rgba(16, 185, 129, 0.15); color: #34d399; }
    .status-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; }

    .case-type-badge { color: var(--danger-glow); font-size: 0.8rem; font-weight: 700; background: rgba(239, 68, 68, 0.1); padding: 5px 12px; border-radius: 8px; }

    .card-user-info { margin-bottom: 15px; }
    .user-name { font-weight: 700; color: var(--ws-text-primary); margin-bottom: 2px; }
    .user-phone { color: var(--danger-glow); font-size: 0.9rem; margin-bottom: 5px; }

    .description-box { background: rgba(0,0,0,0.2); border-radius: 14px; padding: 15px; color: #94a3b8; border: 1px solid rgba(255,255,255,0.03); min-height: 80px; }

    .btn-details-glow { background: rgba(255,255,255,0.05); color: var(--ws-text-primary); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; font-weight: 600; transition: 0.3s; }
    .btn-details-glow:hover { background: var(--danger); border-color: var(--danger); box-shadow: 0 0 20px rgba(239, 68, 68, 0.4); }

    .card-inner-bottom { background: rgba(0,0,0,0.3); padding: 16px; border-top: 1px solid rgba(255,255,255,0.05); }
    .btn-action-card { border-radius: 12px; padding: 10px; font-weight: 700; font-size: 0.8rem; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; }
    .id-card-btn { background: #334155; color: var(--ws-text-primary); }
    .id-card-btn:hover { background: #475569; transform: scale(1.03); }
    .report-btn { background: #991b1b; color: var(--ws-text-primary); }
    .report-btn:hover { background: #b91c1c; transform: scale(1.03); }
    .disabled-btn { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.2); }

    /* Modal Styling */
    .premium-modal { background: #000000 !important; border: 1px solid rgba(239, 68, 68, 0.4); border-radius: 30px; overflow: hidden; box-shadow: 0 0 120px #000 !important; }
    .shadow-danger { box-shadow: 0 0 100px rgba(0, 0, 0, 0.9) !important; }
    .premium-modal .modal-header { background: #1a0505 !important; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 25px; }
    .premium-modal .modal-body { padding: 35px; background: #000000 !important; position: relative; z-index: 1000; }
    
    .info-group label { display: block; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
    .info-val { color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600; }
    .message-box { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; color: #94a3b8; line-height: 1.7; }

    .admin-panel { background: rgba(0,0,0,0.25); border-radius: 20px; padding: 25px; border: 1px solid rgba(255,255,255,0.05); }
    .dark-input { background: var(--ws-bg-page) !important; border: 1px solid var(--ws-border) !important; color: var(--ws-text-primary) !important; border-radius: 12px !important; padding: 12px !important; }
    .btn-save-premium-danger { background: var(--danger); color: var(--ws-text-primary); border: none; border-radius: 100px; padding: 12px 35px; font-weight: 700; transition: 0.3s; }
    .btn-save-premium-danger:hover { background: #dc2626; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(220, 38, 38, 0.3); }
    .btn-delete-danger { background: transparent; color: var(--danger); border: none; font-weight: 600; opacity: 0.7; transition: 0.3s; }

    .glass-card { background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 30px; }

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



