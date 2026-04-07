@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --primary-green: #22c55e;
        --primary-hover: #16a34a;
        --bg-light: #f9fafb;
        --text-main: #111111;
        --text-muted: #64748b;
        --border-color: #e5e7eb;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    body {
        background-color: var(--bg-light) !important;
        color: var(--text-main);
        font-family: 'Tajawal', sans-serif;
    }

    /* Premium Hero Section */
    .premium-hero-sleek {
        background: white;
        padding: 4rem 2rem;
        border-radius: 0 0 40px 40px;
        box-shadow: var(--card-shadow);
        border-bottom: 1px solid var(--border-color);
        margin-bottom: 3rem;
        position: relative;
    }

    .hero-bg-visuals {
        position: absolute;
        top: 0;
        right: 0;
        width: 100%;
        height: 100%;
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }

    .glow-orb-1 {
        position: absolute;
        top: -10%;
        right: -5%;
        width: 300px;
        height: 300px;
        background: rgba(34, 197, 94, 0.05);
        filter: blur(80px);
        border-radius: 50%;
    }

    .hero-content-wrapper {
        position: relative;
        z-index: 1;
    }

    .badge-glass-premium {
        display: inline-flex;
        align-items: center;
        background: rgba(34, 197, 94, 0.1);
        color: var(--primary-green);
        padding: 0.5rem 1rem;
        border-radius: 100px;
        font-weight: 700;
        font-size: 0.85rem;
        margin-bottom: 1rem;
    }

    /* News Cards */
    .news-card-premium {
        background: white;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        height: 100%;
        display: flex;
        flex-direction: column;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .news-card-premium:hover {
        transform: translateY(-8px);
        border-color: var(--primary-green);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -6px rgba(0, 0, 0, 0.1);
    }

    .news-card-image {
        position: relative;
        height: 220px;
        overflow: hidden;
    }

    .news-card-image img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }

    .news-card-premium:hover .news-card-image img {
        transform: scale(1.05);
    }

    .news-category-pill {
        position: absolute;
        top: 1rem;
        right: 1rem;
        background: white;
        color: var(--primary-green);
        padding: 0.4rem 1rem;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.75rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .news-card-actions {
        position: absolute;
        bottom: 1rem;
        left: 1rem;
        display: flex;
        gap: 0.5rem;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .news-card-premium:hover .news-card-actions {
        opacity: 1;
        transform: translateY(0);
    }

    .btn-action-sm {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: white;
        color: var(--text-main);
        border: none;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        transition: all 0.2s ease;
    }

    .btn-action-sm:hover {
        background: var(--primary-green);
        color: white;
        transform: scale(1.1);
    }

    .btn-action-danger-sm:hover {
        background: #ef4444;
    }

    .news-card-body {
        padding: 1.5rem;
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .news-card-body h5 {
        font-weight: 800;
        line-height: 1.4;
        margin-bottom: 0.75rem;
        color: var(--text-main);
    }

    .news-card-body p {
        color: var(--text-muted);
        font-size: 0.9rem;
        margin-bottom: 1.25rem;
    }

    .stat-highlight-box {
        background: #f8fafc;
        border-radius: 12px;
        padding: 0.75rem 1rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1rem;
    }

    /* Modal Styling */
    .modal-content-premium {
        border-radius: 24px;
        border: none;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }

    .modal-header-premium {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        background: white;
        border-radius: 24px 24px 0 0;
    }

    .modal-body-premium {
        padding: 2rem;
    }

    .modal-footer-premium {
        padding: 1.5rem 2rem;
        border-top: 1px solid var(--border-color);
        background: #f8fafc;
        border-radius: 0 0 24px 24px;
    }

    /* Form Controls */
    .form-control-p {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        background: #f8fafc;
    }

    .form-control-p:focus {
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }

    .btn-primary-p {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s ease;
    }

    .btn-primary-p:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.3);
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.5s ease forwards; }
</style>

<div class="news-mgmt-page">
    {{-- Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1"></div>
        </div>
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 text-end">
                    <nav aria-label="breadcrumb" class="mb-3">
                        <ol class="breadcrumb mb-0 justify-content-end">
                            <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-muted text-decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-success fw-bold">أخبار التطبيق</li>
                        </ol>
                    </nav>
                    <div class="badge-glass-premium">
                        <i class="bi bi-newspaper me-2"></i> آخر المستجدات والفعاليات
                    </div>
                    <h1 class="display-5 fw-800 text-main mb-3">أخبار الموبايل</h1>
                    <p class="lead text-muted mb-0">إدارة الأخبار والقصص التي تظهر حصرياً لمستخدمي تطبيق الهاتف المحمول.</p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0">
                    <button class="btn btn-primary-p px-4 py-3" data-bs-toggle="modal" data-bs-target="#addNewsModal">
                        <i class="bi bi-plus-lg me-2"></i> إضافة خبر للموبايل
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 mx-lg-4 rounded-4 shadow-sm border-0" role="alert" style="background: #ecfdf5; color: #065f46; border-right: 6px solid var(--primary-green) !important;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 px-lg-4">
            @forelse($news as $item)
            <div class="col-md-6 col-lg-4">
                <div class="news-card-premium animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="news-card-image">
                        @if($item->image_path)
                            <img src="{{ $item->image_url }}" alt="{{ $item->title }}">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" style="background: #f1f5f9;">
                                <i class="bi bi-camera display-4 text-muted opacity-25"></i>
                            </div>
                        @endif
                        <div class="news-card-actions">
                            <button type="button" class="btn-action-sm" title="تعديل" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('mobile.news.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟')" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-action-sm btn-action-danger-sm" title="حذف">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                        @if($item->category)
                        <div class="news-category-pill">{{ $item->category }}</div>
                        @endif
                    </div>
                    <div class="news-card-body">
                        <h5 class="text-truncate" title="{{ $item->title }}">{{ $item->title }}</h5>
                        <p class="small mb-3">{{ Str::limit($item->content, 90) }}</p>

                        @if($item->statistic_number)
                        <div class="stat-highlight-box">
                            <div class="fw-bold text-success fs-5">{{ $item->statistic_number }}</div>
                            <div class="text-muted small">{{ $item->statistic_description }}</div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                            <div class="d-flex align-items-center gap-3 text-muted small">
                                <span><i class="bi bi-calendar3 me-1"></i> {{ $item->published_at ? $item->published_at->format('d M Y') : 'مسودة' }}</span>
                                <span><i class="bi bi-eye me-1"></i> {{ $item->views_count ?? '0' }}</span>
                            </div>
                            <button class="btn btn-outline-success btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#viewNewsModal{{ $item->id }}">
                                التفاصيل <i class="bi bi-arrow-left ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editNewsModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <form action="{{ route('mobile.news.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content modal-content-premium">
                        @csrf @method('PUT')
                        <div class="modal-header modal-header-premium">
                            <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-success"></i>تعديل الخبر</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body modal-body-premium">
                            <div class="row g-3">
                                <div class="col-md-8">
                                    <label class="form-label small fw-bold text-main">عنوان الخبر</label>
                                    <input type="text" name="title" class="form-control form-control-p" required value="{{ $item->title }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-main">نوع الخبر</label>
                                    <select name="category" class="form-select form-control-p">
                                        @foreach(\App\Models\MobileNews::getCategories() as $cat)
                                            <option value="{{ $cat['id'] }}" {{ $item->category == $cat['id'] ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-main">صورة الخبر</label>
                                    <input type="file" name="image" class="form-control form-control-p">
                                    @if($item->image_path)
                                        <div class="mt-2 d-flex align-items-center gap-3">
                                            <img src="{{ $item->image_url }}" class="rounded shadow-sm" style="max-height: 60px;">
                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="document.getElementById('del_img_{{ $item->id }}').value='1'; this.closest('.d-flex').remove();">
                                                <i class="bi bi-trash"></i> حذف
                                            </button>
                                        </div>
                                    @endif
                                    <input type="hidden" name="delete_image" id="del_img_{{ $item->id }}" value="0">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-main">تاريخ النشر</label>
                                    <input type="date" name="published_at" class="form-control form-control-p" value="{{ $item->published_at ? $item->published_at->format('Y-m-d') : date('Y-m-d') }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-main">المشاهدات</label>
                                    <input type="text" name="views_count" class="form-control form-control-p" value="{{ $item->views_count }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-main">المشاركات</label>
                                    <input type="text" name="shares_count" class="form-control form-control-p" value="{{ $item->shares_count }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-main">رقم إحصائي (اختياري)</label>
                                    <input type="text" name="statistic_number" class="form-control form-control-p" value="{{ $item->statistic_number }}" placeholder="95%">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-main">وصف الإحصائية</label>
                                    <input type="text" name="statistic_description" class="form-control form-control-p" value="{{ $item->statistic_description }}">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label small fw-bold text-main">نص الخبر</label>
                                    <textarea name="content" class="form-control form-control-p" rows="6" required>{{ $item->content }}</textarea>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-premium text-end">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary-p">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- View Modal --}}
            <div class="modal fade" id="viewNewsModal{{ $item->id }}" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content modal-content-premium overflow-hidden">
                        <div class="modal-header modal-header-premium">
                            <h5 class="modal-title fw-bold text-truncate" style="max-width: 80%;">{{ $item->title }}</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body p-0">
                            @if($item->image_path)
                                <img src="{{ $item->image_url }}" class="w-100 object-fit-cover" style="height: 350px;">
                            @endif
                            <div class="p-4">
                                <div class="d-flex gap-4 text-muted small mb-4 pb-3 border-bottom">
                                    <span><i class="bi bi-calendar3 text-success me-1"></i> {{ $item->published_at ? $item->published_at->format('Y-m-d') : '' }}</span>
                                    <span><i class="bi bi-folder-fill text-success me-1"></i> {{ $item->category ?? 'عام' }}</span>
                                    <span><i class="bi bi-eye-fill text-success me-1"></i> {{ $item->views_count ?? 0 }} مشاهدة</span>
                                </div>
                                <div class="text-main lh-lg" style="white-space: pre-wrap;">{{ $item->content }}</div>
                                
                                @if($item->statistic_number)
                                <div class="mt-4 p-3 rounded-4 bg-success bg-opacity-5 border border-success border-opacity-10 d-inline-flex align-items-center gap-3">
                                    <span class="fs-2 fw-bold text-success">{{ $item->statistic_number }}</span>
                                    <span class="text-muted fw-bold">{{ $item->statistic_description }}</span>
                                </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer modal-footer-premium">
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                            <button class="btn btn-outline-success rounded-pill px-4" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#editNewsModal{{ $item->id }}">
                                <i class="bi bi-pencil me-1"></i> تعديل الخبر
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border mt-4">
                    <i class="bi bi-newspaper display-1 text-muted opacity-25"></i>
                    <p class="text-muted mt-3 fs-5">لا توجد أخبار بعد. أضف أول خبر الآن!</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Add News Modal --}}
<div class="modal fade" id="addNewsModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <form action="{{ route('mobile.news.store') }}" method="POST" enctype="multipart/form-data" class="modal-content modal-content-premium">
            @csrf
            <div class="modal-header modal-header-premium">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2 text-success"></i>إضافة خبر جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-premium">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label small fw-bold">عنوان الخبر</label>
                        <input type="text" name="title" class="form-control form-control-p" placeholder="أدخل العنوان هنا..." required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">نوع الخبر</label>
                        <select name="category" class="form-select form-control-p">
                            @foreach(\App\Models\MobileNews::getCategories() as $cat)
                                <option value="{{ $cat['id'] }}" {{ $cat['id'] == 'عام' ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label small fw-bold">صورة الخبر</label>
                        <input type="file" name="image" class="form-control form-control-p">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">تاريخ النشر</label>
                        <input type="date" name="published_at" class="form-control form-control-p" value="{{ date('Y-m-d') }}">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">المشاهدات (تخيلي)</label>
                        <input type="text" name="views_count" class="form-control form-control-p" placeholder="مثلاً: 12K">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">المشاركات (تخيلي)</label>
                        <input type="text" name="shares_count" class="form-control form-control-p" placeholder="مثلاً: 500">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">رقم إحصائي (اختياري)</label>
                        <input type="text" name="statistic_number" class="form-control form-control-p" placeholder="95%">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-bold">وصف الإحصائية</label>
                        <input type="text" name="statistic_description" class="form-control form-control-p" placeholder="نسبة الرضا">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-bold">نص الخبر بالكامل</label>
                        <textarea name="content" class="form-control form-control-p" rows="6" placeholder="اكتب تفاصيل الخبر هنا..." required></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer modal-footer-premium">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary-p px-5">نشر الخبر الآن <i class="bi bi-send ms-2"></i></button>
            </div>
        </form>
    </div>
</div>

@endsection
