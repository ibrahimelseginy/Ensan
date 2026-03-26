@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="news-mgmt-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #7c3aed;"></div>
            <div class="glow-orb-2" style="background: #a855f7;"></div>
            <div class="noise-overlay"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">الأخبار والفعاليات</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-newspaper me-2"></i> آخر المستجدات والفعاليات
                        </div>
                    </div>
                    <h1 class="display-4 fw-800 text-white mb-3 text-end">المركز الإعلامي</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        إدارة الأخبار والقصص التي يتم نشرها على واجهة الموقع
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <div class="d-flex flex-column gap-3 align-items-lg-start">
                        <button class="btn btn-warning-glow rounded-pill px-4 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#manageSlider">
                            <i class="bi bi-images me-2"></i> إدارة صور السلايدر
                        </button>
                        <button class="btn btn-action-glow rounded-pill px-4 py-3 fw-bold shadow-lg" data-bs-toggle="modal" data-bs-target="#addNews">
                            <i class="bi bi-plus-lg me-2"></i> إضافة خبر جديد
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>


<div class="container-fluid py-4">
    {{-- News Coverage Stats Management --}}

    <div class="row g-4">
        @foreach($news as $item)
        <div class="col-md-6 col-lg-4">
            <div class="news-card-premium animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                <div class="news-card-image">
                    @if($item->image_path)
                        <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover">
                    @else
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: linear-gradient(135deg, #1e1b4b, #4c1d95);"><i class="bi bi-camera display-4 text-white-50"></i></div>
                    @endif
                    <div class="news-card-actions">
                        <button class="btn btn-glass-sm" data-bs-toggle="modal" data-bs-target="#editNews{{ $item->id }}" title="تعديل">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <form action="{{ route('website.news.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-glass-sm btn-glass-danger-sm" title="حذف">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                    @if($item->category)
                    <div class="news-category-pill">{{ $item->category }}</div>
                    @endif
                </div>
                <div class="news-card-body">
                    <h5 class="fw-bold mb-3 text-white lh-base text-truncate" title="{{ $item->title }}">{{ $item->title }}</h5>
                    <p class="text-slate-400 small mb-3">{{ Str::limit($item->content, 100) }}</p>
                    
                    @if($item->statistic_number)
                    <div class="stat-highlight-box mb-3">
                        <div class="fw-bold text-emerald fs-5">{{ $item->statistic_number }}</div>
                        <div class="text-slate-500 x-small">{{ $item->statistic_description }}</div>
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top border-white border-opacity-5">
                        <div class="d-flex align-items-center gap-3 text-slate-500 x-small">
                            <span><i class="bi bi-calendar3 text-purple me-1"></i> {{ $item->published_at ? $item->published_at->format('d M Y') : 'مسودة' }}</span>
                            <span><i class="bi bi-eye text-purple me-1"></i> {{ $item->views_count ?? '0' }}</span>
                        </div>
                        <button class="btn btn-glass-purple btn-sm rounded-pill px-3 py-1 x-small fw-bold" data-bs-toggle="modal" data-bs-target="#viewNews{{ $item->id }}">
                            اقرأ المزيد <i class="bi bi-arrow-left ms-1"></i>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editNews{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <form action="{{ route('website.news.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content dark-glass-card border-0">
                        @csrf @method('PUT')
                        <div class="modal-header border-0">
                            <h5 class="modal-title fw-bold">تعديل الخبر: {{ $item->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-md-8 mb-3">
                                    <label class="form-label small fw-bold">عنوان الخبر</label>
                                    <input type="text" name="title" class="form-control" required value="{{ $item->title }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">نوع الخبر</label>
                                    <select name="category" class="form-select">
                                        <option value="طبي" {{ $item->category == 'طبي' ? 'selected' : '' }}>طبي</option>
                                        <option value="تعليمي" {{ $item->category == 'تعليمي' ? 'selected' : '' }}>تعليمي</option>
                                        <option value="مساعدات" {{ $item->category == 'مساعدات' ? 'selected' : '' }}>مساعدات</option>
                                        <option value="فعاليات" {{ $item->category == 'فعاليات' ? 'selected' : '' }}>فعاليات</option>
                                        <option value="عام" {{ ($item->category == 'عام' || !$item->category) ? 'selected' : '' }}>عام</option>
                                    </select>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">صورة الخبر (اتركه فارغاً للاحتفاظ بالقديمة)</label>
                                <div class="input-group">
                                    <input type="file" name="image" class="form-control">
                                    @if($item->image_path)
                                        <button type="button" class="btn btn-danger" onclick="document.getElementById('delete_image_{{ $item->id }}').value='1'; this.closest('.mb-3').querySelector('.mt-2').classList.add('d-none'); this.remove();">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    @endif
                                </div>
                                <input type="hidden" name="delete_image" id="delete_image_{{ $item->id }}" value="0">
                                @if($item->image_path)
                                    <div class="mt-2 text-center">
                                        <img src="{{ $item->image_url }}" class="rounded shadow-sm" style="max-height: 100px; border: 1px solid #dee2e6; padding: 3px;">
                                    </div>
                                @endif
                            </div>

                            <div class="row g-3">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">تاريخ النشر</label>
                                    <input type="date" name="published_at" class="form-control" value="{{ $item->published_at ? $item->published_at->format('Y-m-d') : date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">عدد المشاهدات (رقم)</label>
                                    <input type="text" name="views_count" class="form-control" value="{{ $item->views_count }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label small fw-bold">عدد المشاركات (رقم)</label>
                                    <input type="text" name="shares_count" class="form-control" value="{{ $item->shares_count }}">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">رقم إحصائي (اختياري)</label>
                                    <input type="text" name="statistic_number" class="form-control" value="{{ $item->statistic_number }}" placeholder="مثلاً: 95%">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">وصف الإحصائية (اختياري)</label>
                                    <input type="text" name="statistic_description" class="form-control" value="{{ $item->statistic_description }}" placeholder="مثلاً: نسبة الإنجاز">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">اسم المسؤول / جهة الاتصال (اختياري)</label>
                                    <input type="text" name="contact_name" class="form-control" value="{{ $item->contact_name }}" placeholder="اسم الشخص">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label small fw-bold">رقم التواصل (اختياري)</label>
                                    <input type="text" name="contact_number" class="form-control" value="{{ $item->contact_number }}" placeholder="0123456789">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label small fw-bold">نص الخبر</label>
                                <textarea name="content" class="form-control" rows="10" required>{{ $item->content }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="submit" class="btn btn-warning rounded-pill px-5 shadow-sm fw-bold">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- View Modal --}}
            <div class="modal fade" id="viewNews{{ $item->id }}" tabindex="-1" style="z-index: 1060;">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content dark-glass-card border-0 shadow-lg overflow-hidden" style="border-radius: 20px; background-color: #0f172a !important;">
                        <div class="modal-header border-0" style="border-bottom: 1px solid rgba(255,255,255,0.1) !important;">
                            <h5 class="modal-title fw-bold text-white">{{ $item->title }}</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            @if($item->image_path)
                                <img src="{{ $item->image_url }}" class="w-100 object-fit-cover" style="height: 300px;">
                            @endif
                            <div class="p-4">
                                <div class="d-flex gap-3 text-slate-400 small mb-4 pb-3" style="border-bottom: 1px solid rgba(255,255,255,0.1) !important;">
                                    <span><i class="bi bi-calendar3 text-purple me-1"></i> {{ $item->published_at ? $item->published_at->format('Y-m-d') : '' }}</span>
                                    <span><i class="bi bi-folder text-purple me-1"></i> {{ $item->category ?? 'عام' }}</span>
                                    <span><i class="bi bi-eye text-purple me-1"></i> {{ $item->views_count ?? 0 }} مشاهدة</span>
                                </div>
                                <div class="text-white lh-lg" style="white-space: pre-wrap;">{{ $item->content }}</div>
                            </div>
                        </div>
                        <div class="modal-footer border-0" style="border-top: 1px solid rgba(255,255,255,0.1) !important;">
                            <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>




{{-- Add Modal --}}
<div class="modal fade" id="addNews" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('website.news.store') }}" method="POST" enctype="multipart/form-data" class="modal-content dark-glass-card border-0">
            @csrf
            <div class="modal-header border-0">
                <h5 class="modal-title fw-bold">إضافة خبر جديد للموقع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-3">
                    <div class="col-md-8 mb-3">
                        <label class="form-label small fw-bold">عنوان الخبر</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">نوع الخبر</label>
                        <select name="category" class="form-select">
                            <option value="طبي">طبي</option>
                            <option value="تعليمي">تعليمي</option>
                            <option value="مساعدات">مساعدات</option>
                            <option value="فعاليات">فعاليات</option>
                            <option value="عام" selected>عام</option>
                        </select>
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-12 mb-3">
                        <label class="form-label small fw-bold">صورة الخبر</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">تاريخ النشر</label>
                        <input type="date" name="published_at" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">عدد المشاهدات (رقم)</label>
                        <input type="text" name="views_count" class="form-control" placeholder="مثلاً: 50K+">
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label small fw-bold">عدد المشاركات (رقم)</label>
                        <input type="text" name="shares_count" class="form-control" placeholder="مثلاً: 10K+">
                    </div>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">رقم إحصائي (اختياري)</label>
                        <input type="text" name="statistic_number" class="form-control" placeholder="مثلاً: 95%">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">وصف الإحصائية (اختياري)</label>
                        <input type="text" name="statistic_description" class="form-control" placeholder="مثلاً: نسبة الإنجاز">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">اسم المسؤول / جهة الاتصال (اختياري)</label>
                        <input type="text" name="contact_name" class="form-control" placeholder="اسم الشخص">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label small fw-bold">رقم التواصل (اختياري)</label>
                        <input type="text" name="contact_number" class="form-control" placeholder="0123456789">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">نص الخبر</label>
                    <textarea name="content" class="form-control" rows="10" required></textarea>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm">نشر الخبر على الموقع</button>
            </div>
        </form>
    </div>
</div>
</div>

<style>
    :root {
        --purple-primary: #7c3aed;
        --purple-dark: #6d28d9;
        --dark-bg: #0b0e14;
        --card-dark: #1a1f2e;
        --slate-900: #0f172a;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
    }

    body { background-color: var(--dark-bg) !important; }
    .news-mgmt-page { min-height: 100vh; }

    /* Premium Hero */
    .premium-hero-sleek { position: relative; padding: 100px 0 120px; background: linear-gradient(135deg, #4c1d95 0%, #7c3aed 100%); border-radius: 0 0 60px 60px; overflow: hidden; z-index: 10; box-shadow: 0 20px 50px rgba(0,0,0,0.3); }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; }
    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { background: rgba(255,255,255,0.1); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); padding: 8px 18px; border-radius: 100px; color: #ddd6fe; font-weight: 700; font-size: 0.85rem; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .btn-action-glow { background: var(--purple-primary); color: white; border: none; font-weight: 700; box-shadow: 0 0 20px rgba(124,58,237,0.4); transition: 0.4s; }
    .btn-action-glow:hover { background: var(--purple-dark); transform: translateY(-5px); box-shadow: 0 15px 30px rgba(124,58,237,0.6); color: white; }
    .btn-warning-glow { background: #f59e0b; color: #1a1a2e; border: none; font-weight: 700; box-shadow: 0 0 20px rgba(245,158,11,0.3); transition: 0.4s; }
    .btn-warning-glow:hover { background: #d97706; transform: translateY(-5px); box-shadow: 0 15px 30px rgba(245,158,11,0.5); color: #1a1a2e; }

    /* Dark Card Base */
    .premium-card-dark { background: var(--card-dark); border-radius: 30px; border: 1px solid rgba(255,255,255,0.05); overflow: hidden; color: white; }
    .card-header-lux { padding: 20px 25px; border-bottom: 1px solid rgba(255,255,255,0.05); }
    .card-body-lux { padding: 25px; }
    .header-icon-box-lux { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; }
    .bg-purple-500 { background: var(--purple-primary); }
    .label-lux { color: #94a3b8; font-weight: 700; font-size: 0.85rem; margin-bottom: 10px; display: block; }
    .field-lux { width: 100%; background: #0b0e14; border: 2px solid #1e293b; border-radius: 16px; padding: 14px 20px; color: white; font-weight: 600; transition: 0.3s; }
    .field-lux:focus { border-color: var(--purple-primary); outline: none; background: #000; box-shadow: 0 0 0 4px rgba(124,58,237,0.1); }
    .stat-input-box { background: rgba(0,0,0,0.2); border-radius: 20px; padding: 20px; border: 1px solid rgba(255,255,255,0.05); }
    .bg-purple-subtle { background: rgba(124,58,237,0.15); }
    .text-purple { color: #a78bfa !important; }
    .bg-success-glass { background: rgba(16,185,129,0.15); }
    .text-emerald { color: #34d399 !important; }

    /* News Card Premium */
    .news-card-premium {
        background: var(--card-dark); border-radius: 24px; overflow: hidden; border: 1px solid rgba(255,255,255,0.05);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); height: 100%; display: flex; flex-direction: column;
    }
    .news-card-premium:hover { transform: translateY(-10px); border-color: var(--purple-primary); box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5); }
    .news-card-image { position: relative; height: 220px; overflow: hidden; }
    .news-card-actions { position: absolute; bottom: 12px; right: 12px; display: flex; gap: 8px; opacity: 0; transition: 0.3s; }
    .news-card-premium:hover .news-card-actions { opacity: 1; }
    .btn-glass-sm { width: 38px; height: 38px; border-radius: 12px; background: rgba(255,255,255,0.15); backdrop-filter: blur(10px); color: white; border: 1px solid rgba(255,255,255,0.1); display: flex; align-items: center; justify-content: center; transition: 0.3s; }
    .btn-glass-sm:hover { background: var(--purple-primary); color: white; }
    .btn-glass-danger-sm:hover { background: #ef4444 !important; }
    .news-category-pill { position: absolute; top: 12px; left: 12px; background: rgba(124,58,237,0.8); backdrop-filter: blur(10px); color: white; font-size: 0.7rem; font-weight: 700; padding: 4px 14px; border-radius: 100px; }
    .news-card-body { padding: 20px; display: flex; flex-direction: column; flex: 1; }
    .stat-highlight-box { display: flex; align-items: center; gap: 12px; background: rgba(0,0,0,0.2); border-radius: 14px; padding: 10px 16px; border: 1px solid rgba(255,255,255,0.05); }
    .text-slate-400 { color: var(--slate-400); }
    .text-slate-500 { color: var(--slate-500); }
    .btn-glass-purple { background: rgba(124,58,237,0.1); color: #a78bfa; border: 1px solid rgba(124,58,237,0.2); transition: 0.3s; }
    .btn-glass-purple:hover { background: var(--purple-primary); color: white; }

    /* Dark Theme Modal Styles */
    .dark-glass-card { background: var(--slate-900); color: #f8fafc; border-radius: 30px; box-shadow: 0 20px 40px rgba(0,0,0,0.3); }
    .dark-glass-card .modal-header { border-bottom: 1px solid rgba(255,255,255,0.1); }
    .dark-glass-card .modal-footer { border-top: 1px solid rgba(255,255,255,0.1); }
    .dark-glass-card .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
    .dark-glass-card .form-control, .dark-glass-card .form-select { background-color: #0b0e14; border: 2px solid #1e293b; color: #f8fafc; border-radius: 14px; padding: 12px 16px; transition: 0.3s; }
    .dark-glass-card .form-control:focus, .dark-glass-card .form-select:focus { background-color: #0b0e14; border-color: var(--purple-primary); color: #f8fafc; box-shadow: 0 0 0 4px rgba(124,58,237,0.15); }
    .dark-glass-card .form-control::placeholder { color: #64748b; }
    .dark-glass-card .form-select { color-scheme: dark; }
    .dark-glass-card option { background-color: #0f172a !important; color: #f8fafc !important; }
    .dark-glass-card .form-label { color: #94a3b8; font-weight: 700; font-size: 0.85rem; }

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
</style>

{{-- Manage Slider Modal --}}
<div class="modal fade" id="manageSlider" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg" style="border-radius: 30px; background: #1e293b; color: white;">
            @csrf
            <div class="modal-header border-0 bg-warning text-dark p-4" style="border-radius: 30px 30px 0 0;">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-images fs-4 text-warning"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0">إدارة صور سلايدر الأخبار</h5>
                        <p class="small mb-0 opacity-75">تحديث الصور المتحركة في قسم الأخبار</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-5">
                <div class="row g-4">
                    @for($i = 1; $i <= 10; $i++)
                    <div class="col-md-4">
                        <label class="fw-bold mb-3 d-block text-center text-warning">الشريحة {{ $i }}</label>
                        <div class="position-relative border-2 border-dashed border-secondary rounded-4 overflow-hidden" style="height: 200px; transition: 0.3s;">
                            <input type="file" name="news_slider_{{ $i }}" class="position-absolute w-100 h-100 opacity-0" style="z-index: 10; cursor: pointer;" onchange="previewNewsSlider(this, {{ $i }})">
                            
                            {{-- Delete Button --}}
                            @if(isset($settings["news_slider_$i"]))
                            <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-lg" 
                                    style="z-index: 20; width: 30px; height: 30px; padding: 0;"
                                    onclick="event.stopPropagation(); document.getElementById('delete_news_slider_{{ $i }}').value='1'; this.closest('.position-relative').querySelector('#newsSliderPrev{{ $i }}').innerHTML='<div class=\'text-center text-secondary\'><i class=\'bi bi-cloud-upload fs-1\'></i><p class=\'small mb-0 mt-2\'>محذوف - حفظ للتنفيذ</p></div>'; this.remove();">
                                <i class="bi bi-trash"></i>
                            </button>
                            @endif
                            <input type="hidden" name="delete_news_slider_{{ $i }}" id="delete_news_slider_{{ $i }}" value="0">

                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-dark" id="newsSliderPrev{{ $i }}">
                                @if(isset($settings["news_slider_$i"]))
                                    <img src="{{ asset('storage/' . $settings["news_slider_$i"]) }}" class="w-100 h-100 object-fit-cover">
                                @else
                                    <div class="text-center text-secondary">
                                        <i class="bi bi-cloud-upload fs-1"></i>
                                        <p class="small mb-0 mt-2">اختر صورة</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
            <div class="modal-footer border-0 p-4">
                <button type="submit" class="btn btn-warning w-100 py-3 rounded-pill fw-bold shadow">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>

<script>
function previewNewsSlider(input, index) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('newsSliderPrev' + index);
            container.innerHTML = `<img src="${e.target.result}" class="w-100 h-100 object-fit-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection

