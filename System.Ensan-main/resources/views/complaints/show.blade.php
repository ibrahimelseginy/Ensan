@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
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
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label">المصدر</span>
                                <span class="info-value">
                                    @php
                                        $sourceText = match ($complaint->source_type) {
                                            'donor' => 'متبرع',
                                            'beneficiary' => 'مستفيد',
                                            'employee' => 'موظف',
                                            default => $complaint->source_type
                                        };
                                    @endphp
                                    <span class="badge bg-secondary-subtle text-secondary">{{ $sourceText }}</span>
                                    #{{ $complaint->source_id }}
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
                                            'in_progress' => 'جارية',
                                            'closed' => 'مغلقة',
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
                        <div class="col-12">
                            <div class="p-3 bg-light rounded mt-2">
                                <div class="text-muted small mb-2">نص الشكوى</div>
                                <div>{{ $complaint->message }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            {{-- Quick Actions --}}
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
        </div>
    </div>
@endsection