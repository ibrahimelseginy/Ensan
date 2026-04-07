@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="board-members-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #3b82f6;"></div>
            <div class="glow-orb-2" style="background: #06b6d4;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">مجلس الأمناء</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-shield-check me-2"></i> إدارة الكوادر العليا
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">مجلس الأمناء</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        التعريف بأعضاء المجلس ووظائفهم وصورهم على الموقع الرسمي
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <button class="btn btn-action-glow rounded-pill px-5 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#addMember">
                        <i class="bi bi-plus-lg me-2"></i> إضافة عضو جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Members Grid --}}
    <div class="container-fluid py-5 content-shift-up">
        <div class="row g-4">


            @foreach($members as $member)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="member-card-premium animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="member-avatar-wrapper">
                        <div class="avatar-circle-premium">
                            @if($member->image_path)
                                <img src="{{ $member->image_url }}" class="w-100 h-100 object-fit-cover">
                            @else
                                <div class="avatar-placeholder-premium">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                            @endif
                        </div>
                        <div class="avatar-glow"></div>
                    </div>
                    <h5 class="fw-bold text-white mb-2 mt-4 text-center">{{ $member->name }}</h5>
                    <div class="role-pill-premium mx-auto mb-3">{{ $member->role }}</div>
                    <p class="text-slate-400 small mb-4 text-center text-truncate-3">{{ $member->description }}</p>
                    
                    <div class="d-flex justify-content-center gap-2 mt-auto">
                        <button class="btn btn-glass-blue flex-grow-1 py-2" data-bs-toggle="modal" data-bs-target="#editMember{{ $member->id }}">
                            <i class="bi bi-pencil-square me-1"></i> تعديل
                        </button>
                        <form action="{{ route('website.board.destroy', $member) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')" class="d-inline">
                            @csrf @method('DELETE')
                            <button class="btn btn-glass-danger py-2 px-3"><i class="bi bi-trash3"></i></button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- Modals Section - Outside of transformed containers --}}
@foreach($members as $member)
<div class="modal fade" id="editMember{{ $member->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.board.update', $member) }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark border-0">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4 pb-2">
                <h5 class="modal-title fw-bold text-white">تعديل بيانات العضو</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 text-start" dir="rtl">
                <div class="mb-4 text-center">
                    <div class="upload-zone-circle mx-auto position-relative">
                        @if($member->image_path)
                            <img src="{{ $member->image_url }}" class="w-100 h-100 object-fit-cover" id="editImg{{ $member->id }}">
                        @else
                            <div class="placeholder-vibe" id="editPlaceholder{{ $member->id }}"><i class="bi bi-camera fs-3 text-blue-400"></i></div>
                            <img src="" class="w-100 h-100 object-fit-cover d-none" id="editImg{{ $member->id }}">
                        @endif
                        <input type="file" name="image" class="file-hidden" 
                               onchange="const img = document.getElementById('editImg{{ $member->id }}'); img.src = window.URL.createObjectURL(this.files[0]); img.classList.remove('d-none'); const ph = document.getElementById('editPlaceholder{{ $member->id }}'); if(ph) ph.classList.add('d-none');">
                    </div>
                    <p class="x-small text-slate-500 mt-2">اضغط لتغيير الصورة</p>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">الاسم</label>
                    <input type="text" name="name" class="field-lux" value="{{ $member->name }}" required>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">المنصب</label>
                    <input type="text" name="role" class="field-lux" value="{{ $member->role }}" required>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">نبذة تعريفية</label>
                    <textarea name="description" class="field-lux" rows="3">{{ $member->description }}</textarea>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">ترتيب العرض</label>
                    <input type="number" name="sort_order" class="field-lux" value="{{ $member->sort_order }}">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-action-glow rounded-pill w-100 py-3 fw-bold">تحديث البيانات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="addMember" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.board.store') }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark border-0">
            @csrf
            <div class="modal-header border-0 p-4 pb-2">
                <h5 class="modal-title fw-bold text-white">إضافة عضو جديد لمجلس الأمناء</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <div class="upload-zone-circle mx-auto">
                        <img src="" class="w-100 h-100 object-fit-cover d-none" id="addImg">
                        <div class="placeholder-vibe" id="addPlaceholder"><i class="bi bi-camera fs-3 text-blue-400"></i></div>
                        <input type="file" name="image" class="file-hidden" 
                               onchange="document.getElementById('addImg').src = window.URL.createObjectURL(this.files[0]); document.getElementById('addImg').classList.remove('d-none'); document.getElementById('addPlaceholder').classList.add('d-none')">
                    </div>
                    <p class="x-small text-slate-500 mt-2">اضغط لرفع الصورة</p>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">الاسم</label>
                    <input type="text" name="name" class="field-lux" required>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">المنصب</label>
                    <input type="text" name="role" class="field-lux" placeholder="مثلاً: رئيس مجلس الإدارة" required>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">نبذة تعريفية</label>
                    <textarea name="description" class="field-lux" rows="3"></textarea>
                </div>
                <div class="form-group-lux mb-3">
                    <label class="label-lux">ترتيب العرض</label>
                    <input type="number" name="sort_order" class="field-lux" value="0">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-action-glow rounded-pill w-100 py-3 fw-bold">إضافة العضو</button>
            </div>
        </form>
    </div>
</div>

<style>
    :root {
        --blue-primary: #3b82f6;
        --blue-dark: #2563eb;
        --dark-bg: #0b0e14;
        --card-dark: #1a1f2e;
        --slate-900: #0f172a;
        --slate-400: #94a3b8;
    }

    body { background-color: var(--dark-bg) !important; }
    .board-members-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #1e3a8a 0%, #1d4ed8 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #bfdbfe; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .btn-action-glow { background: var(--blue-primary); color: white; border: none; font-weight: 700; box-shadow: 0 0 20px rgba(59,130,246,0.4); transition: 0.4s; }
    .btn-action-glow:hover { background: var(--blue-dark); transform: translateY(-5px); box-shadow: 0 15px 30px rgba(59,130,246,0.6); color: white; }
    .content-shift-up { margin-top: -20px; position: relative; z-index: 20; padding: 0 5%; }

    /* Member Card */
    .member-card-premium {
        background: var(--card-dark); border-radius: 30px; padding: 30px; border: 1px solid rgba(255,255,255,0.05);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); height: 100%; display: flex; flex-direction: column; align-items: center;
    }
    .member-card-premium:hover { transform: translateY(-12px) scale(1.02); border-color: var(--blue-primary); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }

    .member-avatar-wrapper { position: relative; }
    .avatar-circle-premium { width: 110px; height: 110px; border-radius: 50%; overflow: hidden; border: 4px solid rgba(59,130,246,0.3); position: relative; z-index: 2; }
    .avatar-placeholder-premium { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #1e3a8a, #3b82f6); color: white; font-size: 2.5rem; }
    .avatar-glow { position: absolute; inset: -8px; border-radius: 50%; background: var(--blue-primary); opacity: 0.1; filter: blur(15px); z-index: 1; }
    .member-card-premium:hover .avatar-glow { opacity: 0.25; }

    .role-pill-premium { background: rgba(59,130,246,0.1); color: #93c5fd; padding: 6px 18px; border-radius: 100px; font-size: 0.8rem; font-weight: 700; border: 1px solid rgba(59,130,246,0.15); display: inline-block; }
    .text-truncate-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
    .text-slate-400 { color: var(--slate-400); }

    .btn-glass-blue { background: rgba(59,130,246,0.1); color: #93c5fd; border: 1px solid rgba(59,130,246,0.2); border-radius: 14px; font-weight: 700; font-size: 0.85rem; transition: 0.3s; }
    .btn-glass-blue:hover { background: var(--blue-primary); color: white; transform: translateY(-3px); }
    .btn-glass-danger { background: rgba(239,68,68,0.1); color: #fca5a5; border: 1px solid rgba(239,68,68,0.2); border-radius: 14px; transition: 0.3s; }
    .btn-glass-danger:hover { background: #ef4444; color: white; transform: translateY(-3px); }

    /* Modal */
    .premium-modal-dark { background: var(--slate-900); border-radius: 35px; overflow: hidden; }
    .upload-zone-circle { width: 110px; height: 110px; border-radius: 50%; border: 2px dashed #334155; overflow: hidden; position: relative; cursor: pointer; transition: 0.3s; }
    .upload-zone-circle:hover { border-color: var(--blue-primary); }
    .placeholder-vibe { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; background: rgba(0,0,0,0.3); }
    .file-hidden { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }
    .label-lux { color: #94a3b8; font-weight: 700; font-size: 0.85rem; margin-bottom: 10px; display: block; }
    .field-lux { width: 100%; background: #0b0e14; border: 2px solid #1e293b; border-radius: 16px; padding: 14px 20px; color: white; font-weight: 600; transition: 0.3s; }
    .field-lux:focus { border-color: var(--blue-primary); outline: none; background: #000; box-shadow: 0 0 0 4px rgba(59,130,246,0.1); }
    .x-small { font-size: 0.7rem; }

    /* Animations */
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 991px) {
        .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; }
        .display-4 { font-size: 2.2rem; }
    }
      
      
            
      

      

      /* --- SYSTEM LIGHT MODE PATCH --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .text-white, 
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white, 
      body:not(.theme-dark) .premium-hero-sleek .text-white-50,
      body:not(.theme-dark) .badge-glass-premium,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item a {
          color: #fff !important;
      }
      body:not(.theme-dark) .glass-card, 
      body:not(.theme-dark) .premium-modal-dark,
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .stats-card-dark,
      body:not(.theme-dark) .stats-inner-card,
      body:not(.theme-dark) .project-card-admin,
      body:not(.theme-dark) .campaign-card-lux,
      body:not(.theme-dark) .guest-card-lux,
      body:not(.theme-dark) .article-card-lux,
      body:not(.theme-dark) .message-card-lux,
      body:not(.theme-dark) .donation-card-lux,
      body:not(.theme-dark) .member-card-premium,
      body:not(.theme-dark) .partner-card-lux,
      body:not(.theme-dark) .leader-card-lux,
      body:not(.theme-dark) .empty-state-card-lux,
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content,
      body:not(.theme-dark) .categories-sidebar,
      body:not(.theme-dark) .sector-header,
      body:not(.theme-dark) .item-card,
      body:not(.theme-dark) .dark-glass-card {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .category-item {
          color: var(--ws-text-secondary);
          background: rgba(0,0,0,0.02);
      }
      body:not(.theme-dark) .category-item:hover { background: var(--ws-bg-page); color: var(--ws-text-primary); }
      body:not(.theme-dark) .category-item.active { background: var(--ws-bg-page); border-color: var(--ws-primary); color: var(--ws-text-primary); }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control, body:not(.theme-dark) .form-select, body:not(.theme-dark) .form-input-dark { 
          background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; 
      }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
      </style>
@endsection






