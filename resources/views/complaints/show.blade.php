@extends('layouts.app')
@section('content')
    <div class="page-header d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h4 class="mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-exclamation-triangle text-primary"></i>
                تفاصيل الشكوى
                @if($complaint->tracking_code)
                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 font-monospace fs-6 px-2.5 py-1">
                        {{ $complaint->tracking_code }}
                    </span>
                @endif
            </h4>
            <div class="text-muted small">عرض كافة تفاصيل ومجريات حل الشكوى المسجلة</div>
        </div>
        <div class="btn-group">
            <a class="btn btn-outline-primary" href="{{ route('complaints.edit', $complaint) }}">
                <i class="bi bi-pencil me-1"></i> تعديل
            </a>
            <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Info Card --}}
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <i class="bi bi-info-circle"></i>
                        <h5 class="mb-0">{{ $complaint->subject }}</h5>
                    </div>

                    <div class="row g-3">
                        {{-- كود التتبع --}}
                        @php
                            $showCode = $complaint->tracking_code ?: ('ENS-' . str_pad((string)$complaint->id, 6, '0', STR_PAD_LEFT));
                        @endphp
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3.5 rounded-3" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08) 0%, rgba(5, 150, 105, 0.12) 100%); border: 1.5px solid #10b981; border-radius: 14px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2.5 rounded-3 bg-success bg-opacity-15 text-success">
                                        <i class="bi bi-shield-check fs-2"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold">كود الشكوى المعتمد (كود التتبع الخاص)</div>
                                        <div class="fw-bold fs-4" style="font-family: monospace; letter-spacing: 2px; color: #059669;">
                                            {{ $showCode }}
                                        </div>
                                        <div class="small text-muted mt-0.5">
                                            يمكن للشاكي أو الإدارة تتبع مراحل الحل فوراً عبر الرابط العام
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center flex-wrap">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 bg-white" onclick="navigator.clipboard.writeText('{{ $showCode }}'); this.innerText='تم النسخ!'; setTimeout(() => this.innerText='نسخ الكود', 2000)">
                                        <i class="bi bi-clipboard me-1"></i> نسخ الكود
                                    </button>
                                    <a href="{{ route('complaint.track', ['code' => $showCode]) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> تتبع الشكوى الآن
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">صاحب الشكوى</span>
                                <span class="info-value">
                                    @if($complaint->source_type === 'website')
                                        <span class="badge bg-info-subtle text-info border border-info border-opacity-25 me-1">
                                            <i class="bi bi-globe me-1"></i>الموقع الإلكتروني
                                        </span>
                                        <strong class="text-dark">{{ $complaint->source_name }}</strong>
                                        @if($complaint->source_id && \Illuminate\Support\Facades\Route::has('website.contact-messages.index'))
                                            <a href="{{ route('website.contact-messages.index') }}" class="small ms-2 text-decoration-none">
                                                (رسالة الموقع #{{ $complaint->source_id }})
                                            </a>
                                        @endif
                                    @else
                                        <span class="badge bg-primary-subtle text-primary me-1">{{ $complaint->source_type_name }}</span>
                                        @php $sourceModel = $complaint->source_model; @endphp
                                        @if($complaint->source_type === 'donor' && $sourceModel)
                                            <a href="{{ route('donors.show', $sourceModel) }}" class="fw-bold text-decoration-none">
                                                {{ $complaint->source_name }} <i class="bi bi-box-arrow-up-right small"></i>
                                            </a>
                                        @elseif($complaint->source_type === 'beneficiary' && $sourceModel)
                                            <a href="{{ route('beneficiaries.show', $sourceModel) }}" class="fw-bold text-decoration-none">
                                                {{ $complaint->source_name }} <i class="bi bi-box-arrow-up-right small"></i>
                                            </a>
                                        @elseif($complaint->source_type === 'employee' && $sourceModel)
                                            <a href="{{ route('users.show', $sourceModel) }}" class="fw-bold text-decoration-none">
                                                {{ $complaint->source_name }} <i class="bi bi-box-arrow-up-right small"></i>
                                            </a>
                                        @else
                                            <strong class="text-dark">{{ $complaint->source_name }}</strong>
                                        @endif
                                    @endif

                                    @if(!empty($complaint->complainant_phone))
                                        <div class="small text-muted mt-1">
                                            <i class="bi bi-telephone text-success me-1"></i>{{ $complaint->complainant_phone }}
                                        </div>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">الحالة</span>
                                <span class="info-value">
                                    @php
                                        $statusClass = match ($complaint->status) {
                                            'open' => 'bg-warning-subtle text-warning',
                                            'in_progress' => 'bg-primary-subtle text-primary',
                                            'closed' => 'bg-success-subtle text-success',
                                            default => 'bg-secondary-subtle text-secondary'
                                        };
                                        $statusText = match ($complaint->status) {
                                            'open' => 'مفتوحة',
                                            'in_progress' => 'جارية المعالجة',
                                            'closed' => 'مغلقة / محلولة',
                                            default => $complaint->status
                                        };
                                    @endphp
                                    <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                                </span>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">المسؤول</span>
                                <span class="info-value">{{ $complaint->against?->name ?? '—' }}</span>
                            </div>
                        </div>

                        @if($complaint->resolved_at)
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">تاريخ الحل</span>
                                <span class="info-value text-success">
                                    <i class="bi bi-check-circle-fill me-1"></i>
                                    {{ $complaint->resolved_at->format('d/m/Y') }}
                                </span>
                            </div>
                        </div>
                        @endif

                        <div class="col-12">
                            <div class="p-3 bg-light rounded mt-2">
                                <div class="text-muted small mb-2">نص الشكوى</div>
                                <div>{{ $complaint->message }}</div>
                            </div>
                        </div>

                        @if($complaint->resolution)
                        <div class="col-12">
                            <div class="p-3 rounded mt-2" style="background: #f0fdf4; border: 1.5px solid #86efac;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                    <span class="text-success fw-bold">الرد الرسمي والإجراءات المتخذة:</span>
                                </div>
                                <div style="white-space: pre-line; line-height: 1.8;" class="text-dark">{{ $complaint->resolution }}</div>
                            </div>
                        </div>
                        @endif

                        {{-- Reply Form Section --}}
                        <div class="col-12 mt-4">
                            <div class="card border border-success border-opacity-25 shadow-sm rounded-3">
                                <div class="card-header bg-success bg-opacity-10 text-success py-2.5 px-3 d-flex align-items-center gap-2 fw-bold">
                                    <i class="bi bi-reply-fill fs-5"></i>
                                    إضافة رد / حل رسمي على الشكوى
                                </div>
                                <div class="card-body p-3">
                                    <form method="POST" action="{{ route('complaints.reply', $complaint) }}">
                                        @csrf
                                        <div class="mb-3">
                                            <label class="form-label fw-bold small text-dark">نص الرد أو الحل المقترح للشاكي *</label>
                                            <textarea name="reply" rows="4" class="form-control" placeholder="اكتب رد المؤسسة أو توضيح الإجراءات المتخذة..." required></textarea>
                                            <div class="form-text text-muted small">سيظهر هذا الرد مباشرة للشاكي في صفحة تتبع الشكوى بكود التتبع ({{ $complaint->tracking_code }}).</div>
                                        </div>
                                        <div class="row g-2 align-items-center">
                                            <div class="col-md-7">
                                                <label class="form-label fw-bold small text-muted">تحديث حالة الشكوى بعد الرد:</label>
                                                <select name="status" class="form-select form-select-sm">
                                                    <option value="resolved" @selected($complaint->status === 'resolved' || $complaint->status === 'closed')>تم الحل بنجاح (Resolved)</option>
                                                    <option value="in_progress" @selected($complaint->status === 'in_progress')>جارية المتابعة (In Progress)</option>
                                                    <option value="closed">إغلاق الشكوى نهائياً (Closed)</option>
                                                    <option value="open" @selected($complaint->status === 'open')>إبقاء الشكوى مفتوحة (Open)</option>
                                                </select>
                                            </div>
                                            <div class="col-md-5 d-flex justify-content-end align-items-end mt-3 mt-md-0">
                                                <button type="submit" class="btn btn-success btn-sm px-4 py-2 fw-bold shadow-sm w-100 w-md-auto">
                                                    <i class="bi bi-send-fill me-1"></i> حفظ وإرسال الرد
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <i class="bi bi-lightning"></i>
                        <h6 class="mb-0">إجراءات سريعة</h6>
                    </div>
                    <div class="d-grid gap-2">
                        @if(\App\Models\ChangeRequest::where('model_type', \App\Models\Complaint::class)->where('model_id', $complaint->id)->where('status', 'pending')->exists())
                            <div class="alert alert-warning py-2 mb-0 text-center small">
                                <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة حالياً
                            </div>
                        @else
                            <a href="{{ route('complaints.edit', $complaint) }}" class="btn btn-outline-primary">
                                <i class="bi bi-pencil me-1"></i> تعديل الشكوى
                            </a>
                            <a href="{{ route('complaint.track', ['code' => $complaint->tracking_code]) }}" target="_blank" class="btn btn-outline-success">
                                <i class="bi bi-box-arrow-up-right me-1"></i> تتبع الشكوى مباشرة
                            </a>
                            <form method="POST" action="{{ route('complaints.destroy', $complaint) }}"
                                onsubmit="return confirm('هل أنت متأكد من حذف هذه الشكوى؟');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-danger w-100">
                                    <i class="bi bi-trash me-1"></i> حذف الشكوى
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>

            {{-- معلومات التتبع --}}
            <div class="card mt-3">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <i class="bi bi-qr-code"></i>
                        <h6 class="mb-0">معلومات التتبع</h6>
                    </div>
                    <p class="text-muted small mb-2">أعطِ هذا الكود للمُبلِّغ ليتابع مسار شكواه بأمان:</p>
                    <div class="text-center p-3 rounded-3 bg-light border">
                        <div style="font-family: monospace; font-size: 1.4rem; font-weight: bold; letter-spacing: 2px; color: #059669;">
                            {{ $complaint->tracking_code }}
                        </div>
                    </div>
                    <div class="mt-2 text-center">
                        <a href="{{ route('complaint.track', ['code' => $complaint->tracking_code]) }}" target="_blank" class="btn btn-sm btn-success w-100 fw-bold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> فتح صفحة التتبع بالكود
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
