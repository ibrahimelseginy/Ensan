@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="testimonials-page">
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #0ea5e9;"></div>
            <div class="glow-orb-2" style="background: #2563eb;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">طلبات الآراء</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-chat-quote-fill me-2"></i> آراء ومقترحات المجتمع
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">طلبات الآراء (Testimonials)</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        إدارة ومراجعة آراء المتبرعين والمتطوعين قبل نشرها على الموقع
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <button class="btn btn-action-glow rounded-pill px-5 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#addTestimonial">
                        <i class="bi bi-plus-lg me-2"></i> إضافة رأي جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

<div class="container-fluid py-4">
    {{-- Add Testimonial Modal --}}
    <div class="modal fade" id="addTestimonial" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('website.testimonials.store') }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
                @csrf
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold">إضافة رأي جديد</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">اسم الشخص</label>
                        <input type="text" name="name" class="form-control" required placeholder="مثلاً: محمد أحمد">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الدور / الصفة (متطوع / متبرع / شريك)</label>
                        <input type="text" name="role" class="form-control" placeholder="مثلاً: متطوع متميز">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الرأي / العبارة</label>
                        <textarea name="content" class="form-control" rows="4" required placeholder="اكتب الرأي هنا..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">التقييم</label>
                        <select name="rating" class="form-select">
                            @for($i = 5; $i >= 1; $i--)
                                <option value="{{ $i }}">{{ $i }} نجوم</option>
                            @endfor
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">الصورة (اختياري)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">حفظ ونشر</button>
                </div>
            </form>
        </div>
    </div>
    {{-- Testimonials Section --}}
    <div class="row g-4">
        <div class="col-lg-12">
            <div class="glass-card mb-4 overflow-hidden border-0 shadow-sm animate-slide-up">
                <div class="p-4 border-bottom bg-white bg-opacity-5 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-chat-quote-fill me-2 text-info"></i> الآراء الواردة</h5>
                </div>
                <div class="p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light">
                                <tr>
                                    <th>قائل العبارة</th>
                                    <th>الدور (متبرع/متطوع)</th>
                                    <th>التعليق</th>
                                    <th class="text-center">التقييم</th>
                                    <th class="text-center">الحالة</th>
                                    <th class="text-center">الإجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($testimonials as $testimonial)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-circle border bg-light overflow-hidden" style="width: 40px; height: 40px;">
                                                @if($testimonial->image_path)
                                                    <img src="{{ $testimonial->image_url }}" class="w-100 h-100 object-fit-cover">
                                                @else
                                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center text-muted"><i class="bi bi-person"></i></div>
                                                @endif
                                            </div>
                                            <div class="fw-bold">{{ $testimonial->name }}</div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-info-subtle text-info">{{ $testimonial->role }}</span></td>
                                    <td><p class="mb-0 small text-muted" title="{{ $testimonial->content }}">{{ Str::limit($testimonial->content, 60) }}</p></td>
                                    <td class="text-center">
                                        @for($i = 1; $i <= 5; $i++)
                                            <i class="bi bi-star-fill {{ $i <= $testimonial->rating ? 'text-warning' : 'text-muted' }} x-small"></i>
                                        @endfor
                                    </td>
                                    <td class="text-center">
                                        @if($testimonial->status == 'approved')
                                            <span class="badge bg-success-subtle text-success">منشور</span>
                                        @elseif($testimonial->status == 'rejected')
                                            <span class="badge bg-danger-subtle text-danger">مرفوض</span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">معلق</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-2">
                                            @if($testimonial->status !== 'approved')
                                            <form action="{{ route('website.testimonials.update', $testimonial) }}" method="POST">
                                                @csrf @method('PUT')
                                                <input type="hidden" name="status" value="approved">
                                                <input type="hidden" name="name" value="{{ $testimonial->name }}">
                                                <input type="hidden" name="content" value="{{ $testimonial->content }}">
                                                <button type="submit" class="btn btn-sm btn-success rounded-circle shadow-sm" title="نشر">
                                                    <i class="bi bi-check-lg"></i>
                                                </button>
                                            </form>
                                            @endif
                                            
                                            <button class="btn btn-sm btn-outline-warning rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#editTestimonial{{ $testimonial->id }}">
                                                تعديل <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <form action="{{ route('website.testimonials.destroy', $testimonial) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الرأي؟')">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                                    حذف <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                                @if($testimonials->isEmpty())
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-chat-dots display-4 d-block mb-3 opacity-25"></i>
                                        لا توجد آراء مضافة حالياً
                                    </td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Edit Testimonial Modals --}}
@foreach($testimonials as $testimonial)
<div class="modal fade" id="editTestimonial{{ $testimonial->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('website.testimonials.update', $testimonial) }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
            @csrf @method('PUT')
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">تعديل الرأي</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">اسم الشخص</label>
                    <input type="text" name="name" class="form-control" required value="{{ $testimonial->name }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الدور / الصفة (متطوع / متبرع / شريك)</label>
                    <input type="text" name="role" class="form-control" value="{{ $testimonial->role }}">
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الرأي / العبارة</label>
                    <textarea name="content" class="form-control" rows="4" required>{{ $testimonial->content }}</textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">التقييم</label>
                    <select name="rating" class="form-select">
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ $testimonial->rating == $i ? 'selected' : '' }}>{{ $i }} نجوم</option>
                        @endfor
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="approved" {{ $testimonial->status == 'approved' ? 'selected' : '' }}>منشور (Approved)</option>
                        <option value="pending" {{ $testimonial->status == 'pending' ? 'selected' : '' }}>معلق (Pending)</option>
                        <option value="rejected" {{ $testimonial->status == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الصورة (اتركها فارغة للاحتفاظ بالسابقة)</label>
                    <input type="file" name="image" class="form-control">
                    @if($testimonial->image_path)
                        <div class="mt-2 text-center">
                            <img src="{{ $testimonial->image_url }}" class="rounded-circle shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                        </div>
                    @endif
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold">تحديث الرأي</button>
            </div>
        </form>
    </div>
</div>
@endforeach

<style>
    body { background-color: #0b0e14 !important; }
    .testimonials-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #0c4a6e 0%, #0ea5e9 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #bae6fd; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .btn-action-glow { background: #0ea5e9; color: white; border: none; font-weight: 700; box-shadow: 0 0 20px rgba(14,165,233,0.4); transition: 0.4s; }
    .btn-action-glow:hover { background: #0284c7; transform: translateY(-5px); box-shadow: 0 15px 30px rgba(14,165,233,0.6); color: white; }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @media (max-width: 991px) { .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; } .display-4 { font-size: 2.2rem; } }

    .glass-card { background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.3); border-radius: 20px; box-shadow: 0 8px 32px rgba(0,0,0,0.05); }
    .x-small { font-size: 0.7rem; }
    
    [data-bs-theme="dark"] .glass-card {
        background: rgba(30, 41, 59, 0.7) !important;
        border-color: rgba(255, 255, 255, 0.1) !important;
        color: #f8fafc;
    }
    [data-bs-theme="dark"] .bg-light { background-color: rgba(255,255,255,0.05) !important; border-color: rgba(255,255,255,0.1) !important; }
</style>
@endsection

