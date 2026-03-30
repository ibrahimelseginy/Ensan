@extends('layouts.app')

@section('content')
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #16a34a 0%, #22c55e 100%);">
    <div class="hero-content">
        <div class="hero-greeting text-white-50">تنبيهات المستخدمين 🔔</div>
        <h1 class="hero-title">إشعارات التطبيق (Notifications)</h1>
        <p class="hero-subtitle">إرسال رسائل ترويجية وتحديثات فورية لهواتف المستخدمين</p>
    </div>
    <div class="hero-actions">
        <button class="btn btn-light text-success fw-bold rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#newNotification">
            إرسال إشعار جديد <i class="bi bi-send ms-1"></i>
        </button>
    </div>
</div>

<div class="container-fluid py-4">
    <div class="row g-4">
        <div class="col-md-9 mx-auto">
            <div class="glass-card p-4 animate-slide-up">
                <h5 class="fw-bold mb-4 border-bottom pb-2">سجل الإشعارات المرسلة</h5>
                
                @forelse($notifications as $notif)
                <div class="d-flex align-items-start gap-3 p-3 mb-3 bg-light rounded-3 shadow-sm border hover-effect">
                    <div class="rounded-circle bg-success-subtle text-success p-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="bi bi-bell-fill fs-5"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="fw-bold mb-0 text-success">{{ $notif->title }}</h6>
                            <span class="x-small text-muted"><i class="bi bi-clock me-1"></i>{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mb-2 text-muted small lh-sm">{{ $notif->body }}</p>
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <span class="badge bg-success bg-opacity-10 text-success x-small rounded-pill px-2">
                                <i class="bi bi-tag me-1"></i> {{ $notif->category ?? 'عام' }}
                            </span>
                            <span class="badge bg-primary bg-opacity-10 text-primary x-small rounded-pill px-2">
                                <i class="bi bi-people me-1"></i> {{ $notif->target_audience == 'all' ? 'الجميع' : ($notif->target_audience ?? 'مخصص') }}
                            </span>
                            @if($notif->is_sent)
                                <span class="badge bg-emerald-500 bg-opacity-10 text-emerald-600 x-small rounded-pill px-2 border border-emerald-500 border-opacity-10">
                                    <i class="bi bi-check-all me-1"></i> تم الإرسال
                                </span>
                            @else
                                <span class="badge bg-warning bg-opacity-10 text-warning x-small rounded-pill px-2 border border-warning border-opacity-10">
                                    <i class="bi bi-hourglass-split me-1"></i> قيد المعالجة
                                </span>
                            @endif
                        </div>
                    </div>
                    <div class="d-flex gap-1 align-self-center pt-1">
                        <button class="btn btn-sm btn-outline-primary border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#editNotification{{ $notif->id }}" title="تعديل">
                            <i class="bi bi-pencil-square"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger border-0 rounded-circle" data-bs-toggle="modal" data-bs-target="#deleteNotification{{ $notif->id }}" title="حذف">
                            <i class="bi bi-trash3"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">
                    <i class="bi bi-bell-slash display-4 opacity-25"></i>
                    <p class="mt-2">لم يتم إرسال أي إشعارات بعد.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

{{-- New Notification Modal --}}
<div class="modal fade" id="newNotification" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.notifications.store') }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
            @csrf
            <div class="modal-header border-0 bg-success text-white rounded-top-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-broadcast me-2"></i> إرسال تنبيه جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label small fw-bold">عنوان الإشعار</label>
                    <input type="text" name="title" class="form-control form-control-lg" placeholder="مثلاً: حملة رمضان بدأت!" required>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">نص الرسالة</label>
                    <textarea name="body" class="form-control" rows="3" placeholder="محتوى الإشعار المختصر..." required></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">تصنيف التنبيه</label>
                    <select name="category" class="form-select">
                        @foreach(\App\Models\MobileNotification::getCategories() as $cat)
                            <option value="{{ $cat['id'] }}">{{ $cat['label'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">الجمهور المستهدف</label>
                    <select name="target_audience" class="form-select">
                        <option value="all">كل المستخدمين (Everyone)</option>
                        <option value="donors">المتبرعين فقط</option>
                        <option value="beneficiaries">المستفيدين فقط</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label small fw-bold">صورة مرفقة (اختياري)</label>
                    <input type="file" name="image" class="form-control">
                </div>
            </div>
            <div class="modal-footer border-0 bg-light rounded-bottom-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success rounded-pill px-5 shadow-sm fw-bold">إرسال الآن <i class="bi bi-send-fill ms-2"></i></button>
            </div>
        </form>
    </div>
</div>

{{-- Edit & Delete Modals Loop --}}
@foreach($notifications as $notif)
    {{-- Edit Modal --}}
    <div class="modal fade" id="editNotification{{ $notif->id }}" tabindex="-1">
        <div class="modal-dialog">
            <form action="{{ route('mobile.notifications.update', $notif) }}" method="POST" enctype="multipart/form-data" class="modal-content glass-card border-0">
                @csrf @method('PUT')
                <div class="modal-header border-0 bg-primary text-white rounded-top-4">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i> تعديل الإشعار</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">عنوان الإشعار</label>
                        <input type="text" name="title" class="form-control" value="{{ $notif->title }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">نص الرسالة</label>
                        <textarea name="body" class="form-control" rows="3" required>{{ $notif->body }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">تصنيف التنبيه</label>
                        <select name="category" class="form-select">
                            @foreach(\App\Models\MobileNotification::getCategories() as $cat)
                                <option value="{{ $cat['id'] }}" {{ $notif->category == $cat['id'] ? 'selected' : '' }}>{{ $cat['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control">
                        @if($notif->image_path)
                            <div class="mt-2 text-center">
                                <img src="{{ $notif->image_url }}" class="rounded shadow-sm" style="max-height: 80px;">
                            </div>
                        @else
                            <div class="mt-1 small text-muted">لا توجد صورة حالياً</div>
                        @endif
                    </div>
                </div>
                <div class="modal-footer border-0 bg-light rounded-bottom-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 shadow-sm fw-bold w-100 py-3">حفظ التغييرات</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Delete Modal --}}
    <div class="modal fade" id="deleteNotification{{ $notif->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
                <div class="modal-body p-4 text-center">
                    <div class="mb-3">
                        <i class="bi bi-exclamation-circle text-danger display-3 py-2"></i>
                    </div>
                    <h5 class="fw-bold">هل أنت متأكد؟</h5>
                    <p class="text-muted small">سيتم حذف هذا الإشعار نهائياً من سجلات التطبيق.</p>
                    <div class="d-grid gap-2 mt-4">
                        <form action="{{ route('mobile.notifications.destroy', $notif) }}" method="POST">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger rounded-pill w-100 py-2 fw-bold">نعم، احذف</button>
                        </form>
                        <button type="button" class="btn btn-light rounded-pill w-100 py-2 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

<style>
    .glass-card { background: white; border-radius: 20px; box-shadow: 0 10px 40px rgba(0,0,0,0.08); border: 1px solid rgba(0,0,0,0.03); }
    .x-small { font-size: 0.7rem; }
    .hover-effect { transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); }
    .hover-effect:hover { transform: translateY(-3px); border-color: rgba(34, 197, 94, 0.4) !important; box-shadow: 0 10px 20px rgba(0,0,0,0.05) !important; }
    .bg-emerald-500 { background-color: #10b981 !important; }
    .text-emerald-600 { color: #059669 !important; }
    
    .form-control, .form-select {
        border-radius: 12px !important;
        border: 1px solid #e2e8f0 !important;
        padding: 0.75rem 1rem !important;
        background-color: #f8fafc !important;
    }
    .form-control:focus, .form-select:focus {
        border-color: #22c55e !important;
        box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.1) !important;
    }
</style>
@endsection
