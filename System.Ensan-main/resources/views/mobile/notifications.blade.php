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
                            <h6 class="fw-bold mb-0">{{ $notif->title }}</h6>
                            <span class="x-small text-muted">{{ $notif->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="mb-1 text-muted small">{{ $notif->body }}</p>
                        <div class="d-flex align-items-center justify-content-between mt-2">
                             <span class="badge bg-secondary-subtle text-secondary x-small rounded-pill px-2">
                                <i class="bi bi-people me-1"></i> {{ $notif->target_audience == 'all' ? 'الجميع' : ($notif->target_audience ?? 'مخصص') }}
                            </span>
                            @if($notif->is_sent)
                                <span class="text-success x-small fw-bold"><i class="bi bi-check-all me-1"></i> تم الإرسال</span>
                            @else
                                <span class="text-warning x-small fw-bold"><i class="bi bi-clock me-1"></i> قيد المعالجة</span>
                            @endif
                        </div>
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

<style>
    .glass-card { background: white; border-radius: 20px; box-shadow: 0 8px 30px rgba(0,0,0,0.06); border: 1px solid #f0fdf4; }
    .x-small { font-size: 0.7rem; }
    .hover-effect { transition: transform 0.2s; }
    .hover-effect:hover { transform: translateY(-2px); border-color: #22c55e !important; }
</style>
@endsection
