@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100" style="background-color: #05070a;">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down">
        <div>
            <h1 class="h2 fw-800 text-white mb-1">طلبات التطوع <span class="text-primary-glow">(تطبيق الموبايل)</span></h1>
            <p class="text-white-50 small mb-0">إدارة ومتابعة طلبات الانضمام القادمة حصرياً من تطبيق الهاتف</p>
        </div>
        <div class="glass-badge px-4 py-2">
            <i class="bi bi-people-fill me-2 text-primary"></i>
            <span class="fw-bold">إجمالي الطلبات:</span> {{ $requests->count() }}
        </div>
    </div>

    <div class="row g-4">
        @forelse($requests as $request)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-volunteer-card">
                <div class="card-inner-top">
                    <div class="card-meta">
                        <span class="badge-premium @if($request->status == 'new') status-new @elseif($request->status == 'contacted') status-warn @elseif($request->status == 'accepted') status-success @else status-danger @endif">
                            {{ $request->status == 'new' ? 'جديد' : ($request->status == 'contacted' ? 'تم التواصل' : ($request->status == 'accepted' ? 'مقبول' : 'مرفوض')) }}
                        </span>
                        <div class="card-date">
                            <i class="bi bi-calendar3"></i> {{ $request->created_at->format('Y-m-d') }}
                        </div>
                    </div>
                    
                    <div class="card-user-info">
                        <div class="user-avatar-placeholder">
                            {{ mb_substr($request->name, 0, 1) }}
                        </div>
                        <h4 class="user-name text-truncate" title="{{ $request->name }}">{{ $request->name }}</h4>
                        <p class="user-phone font-outfit">{{ $request->phone }}</p>
                    </div>

                    @if($request->area_of_interest)
                    <div class="interest-tag">
                        <i class="bi bi-bookmark-star me-2"></i> {{ $request->area_of_interest }}
                    </div>
                    @endif

                    <div class="mt-4">
                        <button class="btn btn-details-glow w-100" data-bs-toggle="modal" data-bs-target="#modal{{ $request->id }}">
                            <i class="bi bi-eye me-2"></i> عرض كامل التفاصيل
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom">
                    <div class="row g-2">
                        <div class="col-6">
                            @if($request->id_card_path)
                            <a href="{{ Storage::disk('public')->url($request->id_card_path) }}" target="_blank" class="btn btn-action-card id-card-btn w-100">
                                <i class="bi bi-person-badge"></i> البطاقة
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100">مفقود</button>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($request->cv_path)
                            <a href="{{ route('mobile.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn btn-action-card cv-btn w-100">
                                <i class="bi bi-file-earmark-pdf"></i> السيرة الذاتية
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100">مفقود</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Detail Modal --}}
        <div class="modal fade" id="modal{{ $request->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="background-color: var(--ws-bg-page) !important; border-radius: 24px !important; overflow: hidden;">
                    <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important; padding: 20px 30px !important;">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-person-lines-fill me-2"></i> تفاصيل مقدم طلب التطوع
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4" style="background-color: var(--ws-bg-page) !important;">
                        <div class="row g-4">
                            {{-- Basic Info --}}
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">الإسم بالكامل</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->name }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">رقم الهاتف</label>
                                <div class="font-outfit" style="color: #0066ff; font-size: 1.1rem; font-weight: 600; font-family: 'Outfit', sans-serif;">{{ $request->phone }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">البريد الإلكتروني</label>
                                <div class="text-truncate" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->email ?? '-' }}</div>
                            </div>

                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">الرقم القومي</label>
                                <div class="font-outfit" style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600; font-family: 'Outfit', sans-serif;">{{ $request->national_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">تاريخ الميلاد</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->birth_date ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">النوع</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->gender == 'male' ? 'ذكر' : ($request->gender == 'female' ? 'أنثى' : ($request->gender ?? '-')) }}</div>
                            </div>

                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">العنوان الأصلي</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->address ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">العنوان الحالي</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->current_address ?? '-' }}</div>
                            </div>

                            <hr class="my-2" style="opacity: 0.1; color: var(--ws-text-primary);">

                            {{-- Education & Work --}}
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">المؤهل الدراسي</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->education_level ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">الكلية</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->faculty ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">الجامعة</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->university ?? '-' }}</div>
                            </div>

                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">الوظيفة الحالية</label>
                                <div style="color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600;">{{ $request->current_job ?? '-' }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">اهتمامات التطوع</label>
                                <div style="color: #0066ff; font-size: 1.1rem; font-weight: 700;">{{ $request->area_of_interest ?? '-' }}</div>
                            </div>

                            <div class="col-12 info-group">
                                <label style="display: block; color: #94a3b8; font-size: 0.85rem; font-weight: 500; margin-bottom: 8px;">الهدف من الانضمام</label>
                                <div class="message-box" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 15px; color: #94a3b8; line-height: 1.7;">
                                    "{{ $request->goal ?? '-' }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel mt-5" style="background: rgba(255, 255, 255, 0.02); border-radius: 20px; padding: 25px; border: 1px solid rgba(255, 255, 255, 0.05);">
                            <h6 class="mb-3" style="color: var(--ws-text-primary) !important; font-weight: 700; border-right: 4px solid #0066ff; padding-right: 15px;"><i class="bi bi-shield-lock me-2"></i> لوحة الإدارة</h6>
                            <form action="{{ route('mobile.volunteer-requests.update', $request->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small opacity-75" style="color: #94a3b8;">الحالة الحالية</label>
                                        <select name="status" class="form-select" style="background: rgba(15, 23, 42, 0.8) !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; color: var(--ws-text-primary) !important; border-radius: 12px !important; padding: 12px !important;">
                                            <option value="new" {{ $request->status == 'new' ? 'selected' : '' }}>جديد (New)</option>
                                            <option value="contacted" {{ $request->status == 'contacted' ? 'selected' : '' }}>تم التواصل (Contacted)</option>
                                            <option value="accepted" {{ $request->status == 'accepted' ? 'selected' : '' }}>مقبول (Accepted)</option>
                                            <option value="rejected" {{ $request->status == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between">
                                        <button type="submit" class="btn" style="background: #00d1b2; color: var(--ws-text-primary); border: none; border-radius: 12px; padding: 12px 35px; font-weight: 700;">حفظ التعديلات</button>
                                        <button type="button" class="btn" style="background: #363636; color: #f8fafc; border-radius: 12px; padding: 12px 20px; font-weight: 600; border: 1px solid rgba(255,255,255,0.1);" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب؟')) document.getElementById('del-form-{{ $request->id }}').submit()">حذف الطلب</button>
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
        <div class="col-12 animate-up">
            <div class="glass-card text-center py-5">
                <i class="bi bi-inbox display-4 text-white-50"></i>
                <h5 class="text-white mt-4">لا يوجد طلبات حالياً</h5>
                <p class="text-white-50">لم يقم أي مستخدم بإرسال طلبات تطوع عبر تطبيق الموبايل بعد.</p>
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
        --primary-glow: #60a5fa;
        --danger: #ef4444;
        --success: #10b981;
        --warning: #f59e0b;
    }

    body { background-color: var(--dark-bg); font-family: 'Tajawal', 'Outfit', sans-serif; }
    .fw-800 { font-weight: 800; }
    .text-primary-glow { color: var(--primary-glow); }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Header & Badge */
    .glass-badge { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 100px; color: var(--ws-text-primary); backdrop-filter: blur(10px); }

    /* Premium Card Design */
    .premium-volunteer-card {
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
    .premium-volunteer-card:hover {
        transform: translateY(-10px);
        border-color: var(--primary);
        box-shadow: 0 20px 50px rgba(59, 130, 246, 0.2);
    }

    .card-inner-top { padding: 24px; flex-grow: 1; }
    .card-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; }
    
    .badge-premium { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; }
    .status-new { background: rgba(59, 130, 246, 0.15); color: #60a5fa; border: 1px solid rgba(59, 130, 246, 0.2); }
    .status-warn { background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.2); }
    .status-success { background: rgba(16, 185, 129, 0.15); color: #34d399; border: 1px solid rgba(16, 185, 129, 0.2); }
    .status-danger { background: rgba(239, 68, 68, 0.15); color: #f87171; border: 1px solid rgba(239, 68, 68, 0.2); }

    .card-date { color: rgba(255,255,255,0.4); font-size: 0.8rem; font-family: 'Outfit'; }

    .card-user-info { text-align: center; margin-bottom: 20px; }
    .user-avatar-placeholder { width: 60px; height: 60px; background: linear-gradient(135deg, #3b82f6, #1e40af); border-radius: 20px; display: flex; align-items: center; justify-content: center; margin: 0 auto 15px; font-size: 1.8rem; font-weight: 800; color: var(--ws-text-primary); box-shadow: 0 8px 16px rgba(59, 130, 246, 0.3); }
    .user-name { font-weight: 700; color: var(--ws-text-primary); margin-bottom: 4px; }
    .user-phone { color: var(--primary-glow); font-size: 0.95rem; }

    .interest-tag { background: rgba(255,255,255,0.03); border-radius: 12px; padding: 10px 15px; color: #94a3b8; font-size: 0.85rem; border: 1px solid rgba(255,255,255,0.05); }

    .btn-details-glow { background: rgba(255,255,255,0.05); color: var(--ws-text-primary); border: 1px solid rgba(255,255,255,0.1); border-radius: 12px; padding: 12px; font-weight: 600; transition: 0.3s; }
    .btn-details-glow:hover { background: var(--primary); border-color: var(--primary); box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }

    .card-inner-bottom { background: rgba(0,0,0,0.3); padding: 16px; border-top: 1px solid rgba(255,255,255,0.05); }
    .btn-action-card { border-radius: 12px; padding: 10px; font-weight: 700; font-size: 0.85rem; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; }
    .id-card-btn { background: #3b82f6; color: var(--ws-text-primary); }
    .id-card-btn:hover { background: #2563eb; transform: scale(1.03); }
    .cv-btn { background: #ef4444; color: var(--ws-text-primary); }
    .cv-btn:hover { background: #dc2626; transform: scale(1.03); }
    .disabled-btn { background: rgba(255,255,255,0.05); color: rgba(255,255,255,0.2); }

    /* Modal Styling */
    .premium-modal { background: #000000 !important; border: 1px solid rgba(59, 130, 246, 0.4); border-radius: 30px; overflow: hidden; box-shadow: 0 0 120px #000 !important; }
    .premium-modal .modal-header { background: #0a0e17 !important; border-bottom: 1px solid rgba(255,255,255,0.1); padding: 25px; }
    .premium-modal .modal-body { padding: 35px; background: #000000 !important; position: relative; z-index: 1000; }
    
    .info-group label { display: block; color: #64748b; font-size: 0.75rem; font-weight: 800; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
    .info-val { color: var(--ws-text-primary); font-size: 1.1rem; font-weight: 600; }
    .message-box { background: rgba(255, 255, 255, 0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 16px; padding: 20px; color: #94a3b8; line-height: 1.7; font-style: italic; }

    .admin-panel { background: rgba(0,0,0,0.25); border-radius: 20px; padding: 25px; border: 1px solid rgba(255,255,255,0.05); }
    .panel-title { color: var(--primary-glow); font-weight: 700; }
    .dark-input { background: var(--ws-bg-page) !important; border: 1px solid var(--ws-border) !important; color: var(--ws-text-primary) !important; border-radius: 12px !important; padding: 12px !important; }
    .dark-input:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.15) !important; }

    .btn-save-premium { background: var(--primary); color: var(--ws-text-primary); border: none; border-radius: 100px; padding: 12px 35px; font-weight: 700; transition: 0.3s; }
    .btn-save-premium:hover { background: #2563eb; transform: translateY(-2px); box-shadow: 0 10px 20px rgba(37, 99, 235, 0.3); }
    .btn-delete-danger { background: transparent; color: var(--danger); border: none; font-weight: 600; opacity: 0.7; transition: 0.3s; }
    .btn-delete-danger:hover { opacity: 1; color: #f87171; }

    /* Empty state */
    .glass-card { background: rgba(255,255,255,0.02); border: 1px dashed rgba(255,255,255,0.1); border-radius: 30px; }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

    /* Custom Scrollbar for dark theme */
    ::-webkit-scrollbar { width: 8px; }
    ::-webkit-scrollbar-track { background: var(--dark-bg); }
    ::-webkit-scrollbar-thumb { background: var(--ws-border); border-radius: 10px; }
    ::-webkit-scrollbar-thumb:hover { background: #334155; }
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



