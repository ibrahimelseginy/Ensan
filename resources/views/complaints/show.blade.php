@extends('layouts.app')
@section('content')
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-exclamation-triangle text-primary"></i>
            تفاصيل الشكوى
        </h4>
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
                        <div class="col-12">
                            <div class="d-flex align-items-center justify-content-between flex-wrap gap-3 p-3 rounded-3" style="background: #f0fdf4; border: 1.5px solid #86efac;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-success">
                                        <i class="bi bi-shield-check fs-2"></i>
                                    </div>
                                    <div>
                                        <div class="text-muted small fw-bold">كود التتبع المعتمد للشكوى</div>
                                        <div class="fw-bold fs-4" style="font-family: monospace; letter-spacing: 2px; color: #059669;">
                                            {{ $complaint->tracking_code }}
                                        </div>
                                        <div class="small text-muted mt-0.5">
                                            يمكن للمراجع أو الموظف تتبع مراحل الحل فوراً عبر الرابط العام
                                        </div>
                                    </div>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="navigator.clipboard.writeText('{{ $complaint->tracking_code }}'); this.innerText='تم النسخ!'; setTimeout(() => this.innerText='نسخ الكود', 2000)">
                                        <i class="bi bi-clipboard me-1"></i> نسخ الكود
                                    </button>
                                    <a href="{{ route('complaint.track', ['code' => $complaint->tracking_code]) }}" target="_blank" class="btn btn-sm btn-success rounded-pill px-3 fw-bold">
                                        <i class="bi bi-box-arrow-up-right me-1"></i> تتبع الشكوى الآن
                                    </a>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">صاحب الشكوى</span>
                                <span class="info-value">
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
                            <div class="p-3 rounded mt-2" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                <div class="d-flex align-items-center gap-2 mb-2">
                                    <i class="bi bi-check-circle-fill text-success"></i>
                                    <span class="text-success fw-bold small">الحل المقدَّم</span>
                                </div>
                                <div>{{ $complaint->resolution }}</div>
                            </div>
                        </div>
                        @endif
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
