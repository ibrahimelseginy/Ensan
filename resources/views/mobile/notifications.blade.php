@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    {{-- Premium Hero Section --}}
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down px-2">
        <div>
            <h1 class="h2 fw-800 text-stats-main mb-1">إشعارات التطبيق <span class="text-primary">(Notifications)</span></h1>
            <p class="text-muted-theme small mb-0">إرسال رسائل ترويجية وتحديثات فورية لهواتف المستخدمين</p>
        </div>
        <div class="hero-actions">
            <button class="btn btn-primary rounded-pill px-4 py-2 shadow-lg fw-bold" data-bs-toggle="modal" data-bs-target="#newNotification">
                <i class="bi bi-broadcast me-2"></i> إرسال إشعار جديد
            </button>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-9 mx-auto">
            <div class="notifications-ledger bg-stats-card-main border-light-subtle rounded-4 p-4 shadow-sm">
                <div class="d-flex align-items-center justify-content-between mb-4 border-bottom border-light-subtle pb-3">
                    <h5 class="fw-bold text-stats-main mb-0"><i class="bi bi-clock-history me-2 text-primary"></i> سجل البث المباشر</h5>
                    <span class="badge bg-stats-inner-item border border-light-subtle text-muted-theme rounded-pill px-3 py-2 fw-bold">إجمالي الإشعارات: {{ $notifications->count() }}</span>
                </div>
                
                @forelse($notifications as $notif)
                <div class="notification-broadcast-card bg-stats-inner-item border border-light-subtle rounded-4 p-4 mb-3 hover-lift transition-all shadow-sm">
                    <div class="d-flex align-items-start gap-4">
                        <div class="broadcast-icon bg-stats-card-main text-primary rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; min-width: 55px;">
                            @if($notif->image_path)
                                <img src="{{ $notif->image_url }}" class="rounded-circle w-100 h-100 object-fit-cover">
                            @else
                                <i class="bi bi-megaphone fs-4"></i>
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div>
                                    <h6 class="fw-bold text-stats-main mb-1 fs-5">{{ $notif->title }}</h6>
                                    <p class="text-muted-theme small mb-3 lh-base" style="max-width: 500px;">{{ $notif->body }}</p>
                                </div>
                                <span class="x-small text-muted-theme font-outfit bg-stats-card-main px-3 py-1 rounded-pill border border-light-subtle fw-bold">
                                    <i class="bi bi-calendar3 me-1"></i> {{ $notif->created_at->format('M d, Y') }}
                                </span>
                            </div>
                            
                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                <span class="badge-custom bg-tag-primary">
                                    <i class="bi bi-tag-fill me-1 opacity-50"></i> {{ $notif->category ?? 'عام' }}
                                </span>
                                <span class="badge-custom bg-tag-info">
                                    @if($notif->target_audience == 'all')
                                        <i class="bi bi-globe me-1 opacity-50"></i> الجميع
                                    @elseif($notif->target_audience == 'donors')
                                        <i class="bi bi-heart-fill me-1 opacity-50"></i> المتبرعين
                                    @else
                                        <i class="bi bi-people-fill me-1 opacity-50"></i> مخصص
                                    @endif
                                </span>
                                @if($notif->is_sent)
                                    <span class="badge-sent">
                                        <i class="bi bi-check-all me-1"></i> بث ناجح
                                    </span>
                                @else
                                    <span class="badge-pending">
                                        <i class="bi bi-hourglass-split me-1"></i> قيد الإرسال
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="broadcast-actions d-flex flex-column gap-2">
                            <button class="btn btn-action-icon edit-btn" data-bs-toggle="modal" data-bs-target="#editNotification{{ $notif->id }}">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-action-icon delete-btn" data-bs-toggle="modal" data-bs-target="#deleteNotification{{ $notif->id }}">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div class="empty-state-icon bg-stats-inner-item text-muted-theme rounded-circle d-inline-flex align-items-center justify-content-center mb-4" style="width: 100px; height: 100px;">
                        <i class="bi bi-bell-slash display-5 opacity-30"></i>
                    </div>
                    <h5 class="text-stats-main fw-bold">لا يوجد سجل إشعارات</h5>
                    <p class="text-muted-theme small">لم يتم إرسال أي حملات بث للموبايل بعد.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- New Notification Modal --}}
<div class="modal fade" id="newNotification" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('mobile.notifications.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px; overflow: hidden;">
            @csrf
            <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                <h5 class="modal-title fw-bold text-stats-title"><i class="bi bi-broadcast me-2 text-primary"></i> إرسال تنبيه جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 bg-stats-card-main">
                <div class="mb-4">
                    <label class="text-muted-theme small fw-bold mb-2 d-block">عنوان الإشعار</label>
                    <input type="text" name="title" class="form-control premium-field" placeholder="مثلاً: حملة رمضان بدأت!" required>
                </div>
                <div class="mb-4">
                    <label class="text-muted-theme small fw-bold mb-2 d-block">نص الرسالة</label>
                    <textarea name="body" class="form-control premium-field" rows="3" placeholder="محتوى الإشعار المختصر..." required></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6 mb-4">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">تصنيف التنبيه</label>
                        <select name="category" class="form-select premium-field">
                            @foreach(\App\Models\MobileNotification::getCategories() as $cat)
                                <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-4">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">الجمهور المستهدف</label>
                        <select name="target_audience" class="form-select premium-field">
                            <option value="all">الجميع (Everyone)</option>
                            <option value="donors">المتبرعين فقط</option>
                            <option value="beneficiaries">المستفيدين فقط</option>
                        </select>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="text-muted-theme small fw-bold mb-2 d-block">صورة مرفقة (اختياري)</label>
                    <div class="bg-stats-inner-item border border-dashed border-light-subtle rounded-3 p-3 text-center">
                        <i class="bi bi-cloud-upload text-primary fs-3 mb-2 d-block"></i>
                        <input type="file" name="image" class="form-control form-control-sm border-0 bg-transparent text-muted-theme">
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-stats-card-main d-flex gap-3">
                <button type="submit" class="btn btn-success flex-grow-1 rounded-pill fw-bold py-3 shadow-sm">إرسال البث الآن</button>
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit & Delete Modals Loop --}}
@foreach($notifications as $notif)
    {{-- Edit Modal --}}
    <div class="modal fade" id="editNotification{{ $notif->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('mobile.notifications.update', $notif) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px; overflow: hidden;">
                @csrf @method('PUT')
                <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                    <h5 class="modal-title fw-bold text-stats-title"><i class="bi bi-pencil-square me-2 text-primary"></i> تعديل الإشعار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-stats-card-main">
                    <div class="mb-4">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">عنوان الإشعار</label>
                        <input type="text" name="title" class="form-control premium-field" value="{{ $notif->title }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">نص الرسالة</label>
                        <textarea name="body" class="form-control premium-field" rows="3" required>{{ $notif->body }}</textarea>
                    </div>
                    <div class="mb-4">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">تصنيف التنبيه</label>
                        <select name="category" class="form-select premium-field">
                            @foreach(\App\Models\MobileNotification::getCategories() as $cat)
                                <option value="{{ $cat['id'] }}" {{ $notif->category == $cat['id'] ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="text-muted-theme small fw-bold mb-2 d-block">تغيير الصورة</label>
                        <div class="bg-stats-inner-item border border-light-subtle rounded-3 p-3">
                            <input type="file" name="image" class="form-control form-control-sm border-0 bg-transparent text-muted-theme mb-2">
                            @if($notif->image_path)
                                <div class="text-center bg-stats-card-main rounded-2 p-2 border border-light-subtle">
                                    <img src="{{ $notif->image_url }}" class="rounded shadow-sm" style="max-height: 100px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-stats-card-main">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold w-100 py-3 mt-n2">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteNotification{{ $notif->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px;">
                <div class="modal-body p-5 text-center bg-stats-card-main">
                    <div class="icon-warning bg-danger bg-opacity-10 text-danger rounded-circle d-inline-flex align-items-center justify-content-center mb-4 shadow-inner" style="width: 80px; height: 80px;">
                        <i class="bi bi-trash3 fs-1"></i>
                    </div>
                    <h4 class="fw-bold text-stats-main mb-2">تأكيد الحذف</h4>
                    <p class="text-muted-theme x-small">سيتم حذف هذا الإشعار نهائياً من أرشيف البث.</p>
                    <div class="d-grid gap-2 mt-4">
                        <form action="{{ route('mobile.notifications.destroy', $notif) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill w-100 py-3 fw-bold shadow-sm mb-2">نعم، احذف</button>
                        </form>
                        <button type="button" class="btn btn-outline-secondary rounded-pill w-100 py-3 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-800 { font-weight: 800; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Theme-Aware Broadcast Styling */
    .bg-stats-card-main { background-color: #ffffff; }
    .bg-stats-inner-item { background-color: var(--gray-50); }
    .text-stats-main { color: var(--dark); }
    .text-muted-theme { color: var(--gray-500); }
    .bg-stats-header { background-color: var(--gray-50); }
    .text-stats-title { color: var(--dark); }

    body.theme-dark .bg-stats-card-main { background-color: var(--bg-card); }
    body.theme-dark .bg-stats-inner-item { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .text-stats-main { color: #ffffff; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .bg-stats-header { background-color: rgba(255, 255, 255, 0.05); }
    body.theme-dark .text-stats-title { color: #ffffff; }

    /* Notification Card Styling */
    .notification-broadcast-card { transition: 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .notification-broadcast-card:hover { transform: translateY(-3px); border-color: var(--primary) !important; box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important; }
    
    .broadcast-icon { border: 1px solid var(--ws-border); }
    
    .badge-custom { padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; display: inline-flex; align-items: center; }
    .bg-tag-primary { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .bg-tag-info { background: rgba(16, 185, 129, 0.1); color: #10b981; }

    .badge-sent { background: rgba(16, 185, 129, 0.1); color: #059669; padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; fw-bold; border: 1px solid rgba(16, 185, 129, 0.2); }
    .badge-pending { background: rgba(245, 158, 11, 0.1); color: #d97706; padding: 6px 14px; border-radius: 100px; font-size: 0.75rem; fw-bold; border: 1px solid rgba(245, 158, 11, 0.2); }

    .btn-action-icon { width: 38px; height: 38px; border-radius: 12px; display: flex; align-items: center; justify-content: center; transition: 0.2s; border: 1px solid var(--ws-border); background: var(--ws-bg-card); }
    .edit-btn { color: var(--primary); }
    .delete-btn { color: var(--danger); }
    .edit-btn:hover { background: var(--primary); color: white; }
    .delete-btn:hover { background: var(--danger); color: white; }

    /* Modal & Field Styling */
    .modal-glass-theme { background-color: var(--ws-bg-card) !important; }
    .premium-field { background-color: var(--bg-stats-inner-item) !important; border: 1px solid var(--ws-border) !important; color: var(--text-stats-main) !important; border-radius: 14px !important; padding: 14px !important; }
    .premium-field:focus { border-color: var(--primary) !important; box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1) !important; }

    .icon-warning { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }

    .x-small { font-size: 0.7rem; }
    body.theme-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }
</style>
@endsection


