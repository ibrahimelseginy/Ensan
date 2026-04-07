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
        overflow: hidden;
    }

    .hero-bg-visuals div {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        opacity: 0.1;
    }

    .glow-orb-1 { width: 300px; height: 300px; top: -50px; right: -50px; background: var(--primary-green); }
    .glow-orb-2 { width: 250px; height: 250px; bottom: -50px; left: 50px; background: #fbbf24; }

    .badge-glass-premium {
        display: inline-flex;
        align-items: center;
        background: rgba(34, 197, 94, 0.1);
        color: var(--primary-green);
        padding: 0.6rem 1.25rem;
        border-radius: 100px;
        font-weight: 800;
        font-size: 0.9rem;
        margin-bottom: 1rem;
    }

    /* Notification Logs Card */
    .premium-notif-container {
        background: white;
        border-radius: 28px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        overflow: hidden;
        margin-bottom: 3rem;
    }

    .notif-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .notif-header h5 { margin: 0; font-weight: 800; color: var(--text-main); }

    .notif-item-p {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: flex-start;
        gap: 1.25rem;
        transition: all 0.2s ease;
    }

    .notif-item-p:hover { background: #fcfdfe; }
    .notif-item-p:last-child { border-bottom: none; }

    .notif-icon-box {
        width: 54px;
        height: 54px;
        background: #f0fdf4;
        color: var(--primary-green);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        flex-shrink: 0;
    }

    .notif-content-p h6 { font-weight: 800; margin-bottom: 0.4rem; color: var(--text-main); font-size: 1.05rem; }
    .notif-content-p p { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 0.75rem; line-height: 1.5; }

    .notif-meta-tags { display: flex; gap: 0.6rem; flex-wrap: wrap; align-items: center; }

    .tag-p {
        padding: 0.4rem 0.8rem;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .tag-category { background: #fffbeb; color: #d97706; }
    .tag-audience { background: #eff6ff; color: #3b82f6; }
    .tag-sent { background: #f0fdf4; color: #16a34a; }
    .tag-processing { background: #fef2f2; color: #dc2626; }

    .notif-time { font-family: 'Outfit', sans-serif; font-size: 0.75rem; color: #94a3b8; font-weight: 600; }

    /* Modals & Forms */
    .modal-content-p { border-radius: 30px; border: none; box-shadow: 0 20px 50px rgba(0,0,0,0.1); overflow: hidden; }
    .modal-header-p { background: var(--primary-green); color: white; padding: 2rem; border: none; }
    .modal-header-p.bg-edit { background: #3b82f6; }

    .modal-body-p { padding: 2.5rem; background: white; }
    .modal-footer-p { padding: 1.5rem 2rem; background: #f8fafc; border-top: 1px solid var(--border-color); }

    .form-label-p { font-weight: 800; color: var(--text-main); font-size: 0.85rem; margin-bottom: 0.6rem; display: block; text-transform: uppercase; letter-spacing: 0.5px; }

    .form-control-p {
        border-radius: 14px;
        border: 1px solid var(--border-color);
        padding: 0.85rem 1.25rem;
        background: #f8fafc;
        transition: all 0.3s ease;
    }

    .form-control-p:focus {
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }

    .btn-submit-notif {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 1rem 3rem;
        border-radius: 14px;
        font-weight: 800;
        transition: all 0.3s ease;
        text-transform: uppercase;
    }

    .btn-submit-notif:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px rgba(34, 197, 94, 0.2);
    }

    /* Animations */
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .animate-up { animation: fadeInUp 0.5s ease forwards; }
</style>

<div class="notifications-mgmt-page">
    {{-- Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1"></div>
            <div class="glow-orb-2"></div>
        </div>
        <div class="container-fluid position-relative z-3">
            <div class="row align-items-center">
                <div class="col-lg-8 text-end">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb mb-2 justify-content-end">
                            <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}" class="text-muted text-decoration-none small">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-success fw-bold small">إشعارات التطبيق</li>
                        </ol>
                    </nav>
                    <div class="badge-glass-premium">
                        <i class="bi bi-broadcast me-2"></i> التواصل المباشر مع المستخدمين
                    </div>
                    <h1 class="display-5 fw-800 text-main mb-3">تنبيهات الموبايل</h1>
                    <p class="lead text-muted mb-0">إرسال التحديثات، التنبيهات، والرسائل الترويجية الفورية لهواتف المتبرعين.</p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0">
                    <button class="btn btn-submit-notif shadow-sm px-4" data-bs-toggle="modal" data-bs-target="#newNotification">
                        <i class="bi bi-send-fill me-2"></i> بث إشعار جديد
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="container py-2 pb-5">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show mb-4 rounded-4 shadow-sm border-0 px-4 py-3" style="background: #ecfdf5; color: #065f46; border-right: 6px solid var(--primary-green) !important;">
                <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row g-4 justify-content-center">
            <div class="col-lg-10">
                <div class="premium-notif-container animate-up">
                    <div class="notif-header">
                        <h5><i class="bi bi-clock-history me-2 text-success"></i> سجل الإشعارات المرسلة</h5>
                        <span class="badge rounded-pill text-muted small border px-3 py-2 fw-bold bg-white">{{ $notifications->count() }} تنبيه</span>
                    </div>

                    @forelse($notifications as $notif)
                    <div class="notif-item-p">
                        <div class="notif-icon-box shadow-sm">
                            <i class="bi bi-bell-fill"></i>
                        </div>
                        <div class="notif-content-p flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6>{{ $notif->title }}</h6>
                                <span class="notif-time"><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</span>
                            </div>
                            <p>{{ $notif->body }}</p>
                            
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="notif-meta-tags">
                                    <span class="tag-p tag-category"><i class="bi bi-tag-fill me-1"></i> {{ $notif->category ?? 'عام' }}</span>
                                    <span class="tag-p tag-audience"><i class="bi bi-people-fill me-1"></i> {{ $notif->target_audience == 'all' ? 'الجميع' : ($notif->target_audience ?? 'مخصص') }}</span>
                                    @if($notif->is_sent)
                                        <span class="tag-p tag-sent"><i class="bi bi-check-all me-1"></i> تم الإرسال</span>
                                    @else
                                        <span class="tag-p tag-processing"><i class="bi bi-hourglass-split me-1"></i> قيد المعالجة</span>
                                    @endif
                                </div>
                                <div class="notif-actions d-flex gap-1">
                                    <button class="btn btn-sm btn-outline-primary border-0 rounded-circle p-2" data-bs-toggle="modal" data-bs-target="#editNotification{{ $notif->id }}" title="تعديل">
                                        <i class="bi bi-pencil-square fs-5"></i>
                                    </button>
                                    <button class="btn btn-sm btn-outline-danger border-0 rounded-circle p-2" data-bs-toggle="modal" data-bs-target="#deleteNotification{{ $notif->id }}" title="حذف">
                                        <i class="bi bi-trash3 fs-5"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="py-5 text-center text-muted">
                        <i class="bi bi-bell-slash display-1 opacity-25 d-block mb-3"></i>
                        <p class="fs-5">لم يتم إرسال أي إشعارات موبايل بعد.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- New Notification Modal --}}
<div class="modal fade" id="newNotification" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('mobile.notifications.store') }}" method="POST" enctype="multipart/form-data" class="modal-content modal-content-p">
            @csrf
            <div class="modal-header modal-header-p">
                <h5 class="modal-title fw-800"><i class="bi bi-broadcast me-2"></i> بث إشعار جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body modal-body-p">
                <div class="mb-4">
                    <label class="form-label-p">عنوان الإشعار</label>
                    <input type="text" name="title" class="form-control form-control-p" placeholder="مثلاً: بادر بالتصدق في يوم الجمعة" required>
                </div>
                <div class="mb-4">
                    <label class="form-label-p">نص الرسالة (سيظهر في التنبيهات)</label>
                    <textarea name="body" class="form-control form-control-p" rows="4" placeholder="اكتب محتوى الرسالة هنا..." required></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-p">تصنيف التنبيه</label>
                        <select name="category" class="form-select form-control-p fw-bold">
                            @foreach(\App\Models\MobileNotification::getCategories() as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-p">الجمهور المستهدف</label>
                        <select name="target_audience" class="form-select form-control-p fw-bold">
                            <option value="all">كل المستخدمين</option>
                            <option value="donors">المتبرعين فقط</option>
                            <option value="beneficiaries">المستفيدين فقط</option>
                        </select>
                    </div>
                </div>
                <div class="mt-4">
                    <label class="form-label-p">صورة مرافقة (اختياري)</label>
                    <input type="file" name="image" class="form-control form-control-p">
                </div>
            </div>
            <div class="modal-footer modal-footer-p border-0">
                <button type="button" class="btn btn-light rounded-pill px-4 fw-800" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-submit-notif flex-grow-1">بث الآن <i class="bi bi-send-fill ms-2"></i></button>
            </div>
        </form>
    </div>
</div>

@foreach($notifications as $notif)
    {{-- Edit Modal --}}
    <div class="modal fade" id="editNotification{{ $notif->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('mobile.notifications.update', $notif) }}" method="POST" enctype="multipart/form-data" class="modal-content modal-content-p">
                @csrf @method('PUT')
                <div class="modal-header modal-header-p bg-edit">
                    <h5 class="modal-title fw-800"><i class="bi bi-pencil-square me-2"></i> تعديل بيانات الإشعار</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body modal-body-p">
                    <div class="mb-4">
                        <label class="form-label-p">عنوان الإشعار</label>
                        <input type="text" name="title" class="form-control form-control-p" value="{{ $notif->title }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label-p">نص الرسالة</label>
                        <textarea name="body" class="form-control form-control-p" rows="4" required>{{ $notif->body }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="form-label-p">تحديث الصورة</label>
                        <input type="file" name="image" class="form-control form-control-p mb-2">
                        @if($notif->image_path)
                            <div class="mt-2 text-center bg-light p-2 rounded-4">
                                <img src="{{ $notif->image_url }}" class="rounded shadow-sm" style="max-height: 100px;">
                            </div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer modal-footer-p">
                    <button type="submit" class="btn btn-primary rounded-pill w-100 py-3 fw-800 shadow-sm" style="background: #3b82f6; border: none;">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteNotification{{ $notif->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content modal-content-p text-center p-5">
                <div class="bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4 mx-auto" style="width: 80px; height: 80px;">
                    <i class="bi bi-trash3 display-5"></i>
                </div>
                <h4 class="fw-800 mb-2">حذف الإشعار؟</h4>
                <p class="text-muted small">سيتم حذف هذا التنبيه نهائياً من سجلات الإرسال. هل تريد المتابعة؟</p>
                <div class="d-grid gap-2 mt-4">
                    <form action="{{ route('mobile.notifications.destroy', $notif) }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger rounded-pill w-100 py-2 fw-800">تأكيد الحذف</button>
                    </form>
                    <button type="button" class="btn btn-light rounded-pill w-100 py-2 fw-800 border" data-bs-dismiss="modal">تراجع</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@endsection
