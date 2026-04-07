@extends('layouts.app')

@section('content')
<div class="board-members-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">مجلس الأمناء</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-shield-check me-2"></i> إدارة الكوادر العليا
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">مجلس الأمناء</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                التعريف بأعضاء المجلس ووظائفهم وصورهم على الموقع الرسمي
            </p>
            <div class="mt-4">
                <button class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addMember">
                    <i class="bi bi-plus-lg me-2"></i> إضافة عضو جديد
                </button>
            </div>
        </div>
    </div>

    {{-- Members Grid --}}
    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            @foreach($members as $member)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm member-card-sleek animate-slide-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="p-4 d-flex flex-column align-items-center text-center h-100">
                        <div class="member-avatar-container mb-3">
                            <div class="avatar-inner shadow-sm">
                                @if($member->image_path)
                                    <img src="{{ $member->image_url }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="avatar-placeholder bg-light text-muted">
                                        <i class="bi bi-person-fill"></i>
                                    </div>
                                @endif
                            </div>
                            <div class="avatar-status-dot bg-success"></div>
                        </div>
                        
                        <h5 class="fw-bold text-dark mb-2">{{ $member->name }}</h5>
                        <div class="badge bg-primary-light text-primary rounded-pill px-3 py-2 mb-3 small fw-bold">{{ $member->role }}</div>
                        <p class="text-muted small mb-4 flex-grow-1 line-clamp-3">{{ $member->description }}</p>
                        
                        <div class="d-flex w-100 gap-2 mt-auto pt-3 border-top">
                            <button class="btn btn-outline-light text-muted border flex-grow-1 py-2 rounded-3 btn-edit-hover" data-bs-toggle="modal" data-bs-target="#editMember{{ $member->id }}">
                                <i class="bi bi-pencil-square me-1"></i> تعديل
                            </button>
                            <form action="{{ route('website.board.destroy', $member) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')" class="d-inline">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-light text-danger border py-2 px-3 rounded-3 btn-delete-hover"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach

            @if($members->isEmpty())
            <div class="col-12">
                <div class="card p-5 text-center border-0 shadow-sm bg-white">
                    <div class="opacity-25 mb-3">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <h5 class="text-dark fw-bold">لا يوجد أعضاء مضافون حالياً</h5>
                    <p class="text-muted">ابدأ بإضافة أول عضو لمجلس الأمناء لعرضه على الموقع.</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals Section --}}
@foreach($members as $member)
<div class="modal fade" id="editMember{{ $member->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.board.update', $member) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark">تعديل بيانات العضو</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <div class="upload-circle mx-auto position-relative shadow-sm">
                        @if($member->image_path)
                            <img src="{{ $member->image_url }}" class="w-100 h-100 object-fit-cover rounded-circle" id="editImg{{ $member->id }}">
                        @else
                            <div class="placeholder-icon bg-light rounded-circle" id="editPlaceholder{{ $member->id }}"><i class="bi bi-camera fs-3 text-muted"></i></div>
                            <img src="" class="w-100 h-100 object-fit-cover d-none rounded-circle" id="editImg{{ $member->id }}">
                        @endif
                        <input type="file" name="image" class="file-hidden-input" 
                               onchange="const img = document.getElementById('editImg{{ $member->id }}'); img.src = window.URL.createObjectURL(this.files[0]); img.classList.remove('d-none'); const ph = document.getElementById('editPlaceholder{{ $member->id }}'); if(ph) ph.classList.add('d-none');">
                    </div>
                    <p class="x-small text-muted mt-2">اضغط على الصورة لتغييرها</p>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">الاسم الكامل</label>
                    <input type="text" name="name" class="form-control" value="{{ $member->name }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">المنصب / المسمى الوظيفي</label>
                    <input type="text" name="role" class="form-control" value="{{ $member->role }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">نبذة تعريفية قصيرة</label>
                    <textarea name="description" class="form-control" rows="3">{{ $member->description }}</textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted">ترتيب العرض (أرقام)</label>
                    <input type="number" name="sort_order" class="form-control" value="{{ $member->sort_order }}">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold">تحديث البيانات</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade" id="addMember" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.board.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 24px;">
            @csrf
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-dark">إضافة عضو جديد لمجلس الأمناء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-center">
                    <div class="upload-circle mx-auto shadow-sm">
                        <img src="" class="w-100 h-100 object-fit-cover d-none rounded-circle" id="addImg">
                        <div class="placeholder-icon bg-light rounded-circle" id="addPlaceholder"><i class="bi bi-camera fs-3 text-muted"></i></div>
                        <input type="file" name="image" class="file-hidden-input" 
                               onchange="document.getElementById('addImg').src = window.URL.createObjectURL(this.files[0]); document.getElementById('addImg').classList.remove('d-none'); document.getElementById('addPlaceholder').classList.add('d-none')">
                    </div>
                    <p class="x-small text-muted mt-2">اضغط لرفع صورة العضو</p>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">الاسم الكامل</label>
                    <input type="text" name="name" class="form-control" required placeholder="مثلاً: د. أحمد محمد">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">المنصب / المسمى الوظيفي</label>
                    <input type="text" name="role" class="form-control" placeholder="مثلاً: رئيس مجلس الإدارة" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold text-muted">نبذة تعريفية قصيرة</label>
                    <textarea name="description" class="form-control" rows="3" placeholder="نبذة مختصرة عن العضو وإنجازاته..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label small fw-bold text-muted">ترتيب العرض</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-bold shadow-sm">إضافة العضو</button>
            </div>
        </form>
    </div>
</div>

<style>
    .board-members-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }
    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 18px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }

    /* Member Card */
    .member-card-sleek {
        border-radius: 24px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        background: white;
    }
    .member-card-sleek:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        border-color: var(--primary-light) !important;
    }

    .member-avatar-container {
        position: relative;
        padding: 8px;
    }
    .avatar-inner {
        width: 120px;
        height: 120px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid white;
        background: #fff;
    }
    .avatar-placeholder {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
    }
    .avatar-status-dot {
        position: absolute;
        bottom: 15px;
        right: 15px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        border: 3px solid white;
        z-index: 5;
    }

    .btn-edit-hover:hover {
        background-color: var(--primary-light) !important;
        color: var(--primary) !important;
        border-color: var(--primary) !important;
    }
    .btn-delete-hover:hover {
        background-color: #fee2e2 !important;
        color: #dc2626 !important;
        border-color: #f87171 !important;
    }

    /* Upload Styling */
    .upload-circle {
        width: 110px;
        height: 110px;
        border-radius: 50%;
        border: 2px dashed var(--border);
        cursor: pointer;
        transition: 0.3s;
        position: relative;
        background: #fff;
    }
    .upload-circle:hover { border-color: var(--primary); background: var(--bg-soft); }
    .placeholder-icon { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; }
    .file-hidden-input { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 768px) {
        .premium-hero-sleek { padding: 60px 0 80px; }
        .display-5 { font-size: 1.8rem; }
    }
</style>
@endsection
