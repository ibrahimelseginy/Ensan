@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="share-opinion-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1"></div>
            <div class="glow-orb-2"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">تقييمات المستفيدين</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-chat-quote-fill me-2"></i> آراء ومقترحات المجتمع
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">تقييمات المستفيدين</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        إدارة ومراجعة آراء المستفيدين، المتبرعين، والمتطوعين الواردة عبر الموقع والتطبيق.
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <button class="btn btn-indigo-solid rounded-pill px-5 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#addTestimonial">
                        <i class="bi bi-plus-lg me-2"></i> إضافة رأي جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid content-shift-up-v2 pb-5">
        <div class="premium-card-dark animate-up">
            <div class="card-header-lux d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon bg-indigo-500 shadow-pulse">
                        <i class="bi bi-chat-left-dots-fill"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white">الآراء الواردة</h5>
                        <p class="mb-0 x-small text-slate-500">إجمالي الآراء: {{ $testimonials->count() }}</p>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table premium-table-dark align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="text-end">قائل العبارة</th>
                            <th class="text-end">الدور / الصفة</th>
                            <th class="text-end">التعليق</th>
                            <th class="text-center">التقييم</th>
                            <th class="text-center">الحالة</th>
                            <th class="text-center">الإجراء</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($testimonials as $testimonial)
                        <tr>
                            <td class="text-end">
                                <div class="d-flex align-items-center justify-content-end gap-3">
                                    <div class="fw-bold text-white">{{ $testimonial->name }}</div>
                                    <div class="user-avatar-mini bg-indigo-900 text-indigo-400">
                                        @if($testimonial->image_path)
                                            <img src="{{ $testimonial->image_url }}" class="w-100 h-100 object-fit-cover rounded-3">
                                        @else
                                            {{ mb_substr($testimonial->name, 0, 1) }}
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="text-end">
                                <span class="badge-glass-premium py-1 px-3 x-small">{{ $testimonial->role }}</span>
                            </td>
                            <td class="text-end">
                                <p class="mb-0 x-small text-slate-400" title="{{ $testimonial->content }}">
                                    {{ \Illuminate\Support\Str::limit($testimonial->content, 80) }}
                                </p>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star-fill {{ $i <= $testimonial->rating ? 'text-warning' : 'text-slate-700' }} x-small"></i>
                                    @endfor
                                </div>
                            </td>
                            <td class="text-center">
                                @if($testimonial->status == 'approved')
                                    <span class="badge-status approved">منشور</span>
                                @else
                                    <span class="badge-status pending">في الانتظار</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <button class="btn btn-glass-indigo btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editTestimonial{{ $testimonial->id }}">
                                        <i class="bi bi-pencil-square ms-1"></i> تعديل
                                    </button>
                                    <form action="{{ route('website.testimonials.destroy', $testimonial) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-glass-danger btn-sm rounded-pill px-3">
                                            <i class="bi bi-trash-fill ms-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                        
                        @if($testimonials->isEmpty())
                        <tr>
                            <td colspan="6" class="py-5">
                                <div class="empty-state-card-lux">
                                    <div class="empty-visual-wrapper mx-auto">
                                        <div class="glow-pulse"></div>
                                        <i class="bi bi-chat-square-dots empty-icon-vibe"></i>
                                    </div>
                                    <h5 class="text-white fw-bold">لا توجد آراء حالياً</h5>
                                    <p class="text-slate-500 x-small">سيتم عرض الآراء المرسلة من الموقع والتطبيق هنا فور ورودها.</p>
                                </div>
                            </td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- Add Testimonial Modal --}}
<div class="modal fade" id="addTestimonial" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark shadow-2xl">
            @csrf
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-white">إضافة رأي جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4 text-end">
                    <label class="label-lux">اسم الشخص</label>
                    <input type="text" name="name" class="field-lux text-end" required placeholder="محمد أحمد">
                </div>
                <div class="mb-4 text-end">
                    <label class="label-lux">الدور / الصفة (متطوع / متبرع / شريك)</label>
                    <input type="text" name="role" class="field-lux text-end" placeholder="متطوع متميز">
                </div>
                <div class="mb-4 text-end">
                    <label class="label-lux">الرأي / العبارة</label>
                    <textarea name="content" class="field-lux text-end" rows="4" required placeholder="اكتب الرأي هنا..."></textarea>
                </div>
                <div class="row mb-4">
                    <div class="col-12 text-end">
                        <label class="label-lux">التقييم</label>
                        <select name="rating" class="field-lux text-end">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} نجوم</option>
                            @endfor
                        </select>
                    </div>
                </div>
                <div class="mb-0 text-end">
                    <label class="label-lux">الصورة (اختياري)</label>
                    <div class="gallery-card-lux text-center p-4 position-relative">
                        <input type="file" name="image" class="file-hidden" accept="image/*" onchange="previewImageLux(this, 'add-preview-lux')">
                        <div id="add-preview-container-lux" class="gallery-preview-wrapper h-100 w-100 flex-column">
                            <i class="bi bi-cloud-arrow-up-fill fs-2 mb-2 text-indigo-400"></i>
                            <span class="x-small text-slate-400">اسحب وأفلت أو انقر للاختيار</span>
                        </div>
                        <img id="add-preview-lux" class="img-fluid rounded-4 shadow-lg d-none h-100 w-100 object-fit-cover">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 gap-2">
                <button type="button" class="btn btn-glass-indigo py-3 px-4 rounded-pill fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-indigo-solid flex-grow-1 py-3 rounded-pill fw-bold">حفظ ونشر التقييم <i class="bi bi-check2-circle ms-1"></i></button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Testimonial Modals --}}
@foreach($testimonials as $testimonial)
<div class="modal fade" id="editTestimonial{{ $testimonial->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('website.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data" class="modal-content premium-modal-dark shadow-2xl">
            @csrf @method('PUT')
            <div class="modal-header border-0 p-4 pb-0">
                <h5 class="modal-title fw-bold text-white">تعديل الرأي الوارد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="info-strip-premium mb-4">
                    <div class="strip-avatar bg-indigo-600">
                        {{ mb_substr($testimonial->name, 0, 1) }}
                    </div>
                    <div class="text-end">
                        <h6 class="fw-bold text-white mb-0">{{ $testimonial->name }}</h6>
                        <p class="mb-0 x-small text-slate-500">{{ $testimonial->role }}</p>
                    </div>
                </div>

                <div class="mb-4 text-end">
                    <label class="label-lux">اسم الشخص</label>
                    <input type="text" name="name" class="field-lux text-end" required value="{{ $testimonial->name }}">
                </div>
                <div class="mb-4 text-end">
                    <label class="label-lux">الدور / الصفة</label>
                    <input type="text" name="role" class="field-lux text-end" value="{{ $testimonial->role }}">
                </div>
                <div class="mb-4 text-end">
                    <label class="label-lux">الرأي / العبارة</label>
                    <textarea name="content" class="field-lux text-end" rows="4" required>{{ $testimonial->content }}</textarea>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6 text-end order-md-2">
                        <label class="label-lux">التقييم</label>
                        <select name="rating" class="field-lux text-end">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}" {{ $testimonial->rating == $i ? 'selected' : '' }}>{{ $i }} نجوم</option>
                            @endfor
                        </select>
                    </div>
                    <div class="col-md-6 text-end order-md-1 mt-4 mt-md-0">
                        <label class="label-lux">الحالة</label>
                        <select name="status" class="field-lux text-end">
                            <option value="approved" {{ $testimonial->status == 'approved' ? 'selected' : '' }}>منشور</option>
                            <option value="pending" {{ $testimonial->status == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                        </select>
                    </div>
                </div>
                <div class="mb-0 text-end">
                    <label class="label-lux">تغيير الصورة (اختياري)</label>
                    <div class="gallery-card-lux text-center p-4 position-relative">
                        <input type="file" name="image" class="file-hidden" accept="image/*" onchange="previewImageLux(this, 'edit-preview-lux-{{ $testimonial->id }}', 'edit-preview-container-lux-{{ $testimonial->id }}')">
                        <div id="edit-preview-container-lux-{{ $testimonial->id }}" class="gallery-preview-wrapper h-100 w-100 flex-column {{ $testimonial->image_path ? 'd-none' : '' }}">
                            <i class="bi bi-cloud-arrow-up-fill fs-2 mb-2 text-indigo-400"></i>
                            <span class="x-small text-slate-400">اسحب وأفلت أو انقر لتغيير الصورة</span>
                        </div>
                        <img id="edit-preview-lux-{{ $testimonial->id }}" class="img-fluid rounded-4 shadow-lg h-100 w-100 object-fit-cover {{ $testimonial->image_path ? '' : 'd-none' }}" src="{{ $testimonial->image_url }}">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 gap-2">
                <button type="button" class="btn btn-glass-indigo py-3 px-4 rounded-pill fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-indigo-solid flex-grow-1 py-3 rounded-pill fw-bold">تحديث الرأي <i class="bi bi-save2 ms-1"></i></button>
            </div>
        </form>
    </div>
</div>
@endforeach

<style>
    :root {
        --dark-bg: #0b0e14;
        --card-dark: #1a2332;
        --indigo-500: #6366f1;
        --indigo-600: #4f46e5;
        --slate-300: #cbd5e1;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
        --slate-700: #334155;
    }

    body { background-color: var(--dark-bg) !important; font-family: 'Tajawal', sans-serif; }

    /* Hero Styling */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 120px;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        border-radius: 0 0 60px 60px;
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.3; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; background: #6366f1; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; background: #0ea5e9; }
    .noise-overlay { 
        position: absolute; inset: 0; opacity: 0.05; 
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='noiseFilter'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.65' numOctaves='3' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23noiseFilter)'/%3E%3C/svg%3E");
    }

    .hero-content-wrapper { position: relative; z-index: 15; padding: 0 5%; }
    .badge-glass-premium { 
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px 18px; border-radius: 100px; color: #e0e7ff; font-weight: 700; font-size: 0.85rem;
    }

    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }

    /* Buttons */
    .btn-indigo-solid {
        background: var(--indigo-600);
        color: white; border: none; font-weight: 700;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        transition: 0.4s;
    }
    .btn-indigo-solid:hover { background: #4338ca; color: white; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4); }

    .btn-glass-indigo {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.2);
        backdrop-filter: blur(8px);
        font-weight: 700;
        transition: 0.4s;
    }
    .btn-glass-indigo:hover { background: rgba(99, 102, 241, 0.2); color: white; transform: translateY(-3px); }

    .btn-glass-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
        backdrop-filter: blur(8px);
        font-weight: 700;
        transition: 0.4s;
    }
    .btn-glass-danger:hover { background: rgba(239, 68, 68, 0.2); color: white; transform: translateY(-3px); }

    /* Layout Shift */
    .content-shift-up { margin-top: -60px; position: relative; z-index: 20; padding: 0 5%; }
    .content-shift-up-v2 { margin-top: 30px; position: relative; z-index: 20; padding: 0 5%; }

    /* Cards & Tables */
    .premium-card-dark {
        background: var(--card-dark);
        border-radius: 35px;
        border: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }
    .card-header-lux { padding: 25px 30px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.01); }
    .header-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; }
    .shadow-pulse { animation: pulseIcon 2s infinite; }
    @keyframes pulseIcon { 0% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.4); } 70% { box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); } 100% { box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); } }

    .premium-table-dark { background: transparent; }
    .premium-table-dark thead th { 
        background: rgba(0,0,0,0.2); color: var(--slate-400); 
        text-transform: uppercase; font-size: 0.75rem; font-weight: 800; border: none; padding: 20px;
    }
    .premium-table-dark tbody td { border-bottom: 1px solid rgba(255,255,255,0.03); padding: 20px; color: #f8fafc; }

    .user-avatar-mini {
        width: 45px; height: 45px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem; overflow: hidden;
    }

    /* Modals & Inputs */
    .premium-modal-dark { background: var(--card-dark); border: 1px solid rgba(255,255,255,0.08); border-radius: 40px; }
    .info-strip-premium { 
        background: rgba(255, 255, 255, 0.03); 
        padding: 20px; border-radius: 25px; display: flex; align-items: center; justify-content: space-between;
    }
    .strip-avatar { width: 55px; height: 55px; border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; font-weight: 800; }

    .field-lux {
        width: 100%; background: #0b0e14; border: 1px solid #2d3748;
        border-radius: 14px; padding: 12px 20px; color: white; font-weight: 600; transition: 0.3s;
    }
    .field-lux:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
    .label-lux { color: var(--slate-400); font-weight: 700; margin-bottom: 8px; display: block; font-size: 0.85rem; }

    .gallery-card-lux {
        position: relative; background: rgba(0,0,0,0.2);
        border: 2px dashed #334155; border-radius: 20px;
        overflow: hidden; transition: 0.3s; cursor: pointer; height: 180px;
    }
    .gallery-card-lux:hover { border-color: var(--indigo-500); background: rgba(99, 102, 241, 0.05); }
    .file-hidden { position: absolute; inset: 0; width: 100%; height: 100%; opacity: 0; cursor: pointer; z-index: 10; }
    
    /* Global Badges */
    .badge-status { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; border: 1px solid transparent; }
    .badge-status.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
    .badge-status.approved { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }

    /* Animations */
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(35px); } to { opacity: 1; transform: translateY(0); } }

    /* Utilities */
    .x-small { font-size: 0.75rem; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    .deco-none { text-decoration: none !important; }

    @media (max-width: 991px) {
        .premium-hero-sleek { padding: 60px 0 80px; border-radius: 0 0 35px 35px; }
        .display-4 { font-size: 2.2rem; }
        .text-end { text-align: center !important; }
        .justify-content-end { justify-content: center !important; }
    }
</style>

<script>
function previewImageLux(input, previewId, containerId = null) {
    if (input.files \u0026\u0026 input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var preview = document.getElementById(previewId);
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('d-none');
            }
            if (containerId) {
                var container = document.getElementById(containerId);
                if (container) container.classList.add('d-none');
            } else {
                var addContainer = document.getElementById('add-preview-container-lux');
                if (addContainer) addContainer.classList.add('d-none');
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

