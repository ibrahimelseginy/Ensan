@extends('layouts.app')
@section('content')
    <div class="page-header">
        <h4 class="mb-0">تفاصيل الإجازة</h4>
        <div class="btn-group">
            <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row g-4">
        {{-- Main Info Card --}}
        <div class="col-lg-8">
            <div class="card h-100">
                <div class="card-body">
                    <div class="section-title mb-4">
                        <i class="bi bi-info-circle"></i>
                        <h5 class="mb-0">معلومات الإجازة</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">الموظف</span>
                                <span class="info-value fw-bold fs-6">{{ $leave->user->name ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">نوع الإجازة</span>
                                <div class="info-value">
                                    @php
                                        $types = [
                                            'annual' => 'سنوية',
                                            'sick' => 'مرضية',
                                            'unpaid' => 'بدون راتب',
                                            'emergency' => 'عارضة',
                                            'other' => 'أخرى'
                                        ];
                                    @endphp
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        {{ $types[$leave->type] ?? $leave->type }}
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">تاريخ البداية</span>
                                <span class="info-value fw-bold"><i class="bi bi-calendar-event me-1"></i> {{ optional($leave->start_date)->format('Y-m-d') ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">تاريخ النهاية</span>
                                <span class="info-value fw-bold"><i class="bi bi-calendar-event me-1"></i> {{ optional($leave->end_date)->format('Y-m-d') ?? '—' }}</span>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-row bg-light p-3 rounded-3">
                                <span class="info-label text-muted d-block small mb-2">السبب</span>
                                <p class="mb-0 lh-lg">{{ $leave->reason }}</p>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">الحالة</span>
                                <div class="info-value mt-2">
                                    @if($leave->status == 'approved')
                                        <div class="alert alert-success d-inline-flex align-items-center mb-0 py-2 px-3">
                                            <i class="bi bi-check-circle-fill me-2"></i>
                                            <span>مقبول</span>
                                            @if($leave->approver)
                                                <span class="ms-2 small opacity-75">(بواسطة {{ $leave->approver->name }})</span>
                                            @endif
                                        </div>
                                    @elseif($leave->status == 'rejected')
                                        <div class="alert alert-danger mb-0 py-3 px-4 rounded-4 shadow-sm">
                                            <div class="d-flex align-items-center mb-2">
                                                <i class="bi bi-x-circle-fill me-2 fs-5"></i>
                                                <strong class="fs-5">طلب مرفوض</strong>
                                            </div>
                                            @if($leave->rejection_reason)
                                                <div class="mt-2 pt-2 border-top border-danger-subtle">
                                                    <span class="d-block small text-uppercase fw-bold mb-1" style="letter-spacing: 0.5px; opacity: 0.8;">سبب الرفض:</span>
                                                    <p class="mb-0 fs-6">{{ $leave->rejection_reason }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        <div class="alert alert-warning d-inline-flex align-items-center mb-0 py-2 px-3">
                                            <i class="bi bi-clock-history me-2"></i>
                                            <span>معلق (بانتظار المراجعة)</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body">
                    <div class="section-title mb-3">
                        <i class="bi bi-lightning-fill text-warning"></i>
                        <h6 class="mb-0 fw-bold">إجراءات سريعة</h6>
                    </div>
                    <div class="d-grid gap-2">
                        @if($leave->status !== 'rejected')
                            <a href="{{ route('leaves.edit', ['leave' => $leave->id]) }}" class="btn btn-outline-warning d-flex align-items-center justify-content-center gap-2 py-2">
                                <i class="bi bi-pencil-square"></i>
                                <span>طلب تعديل الإجازة</span>
                            </a>
                            
                            <form action="{{ route('leaves.destroy', ['leave' => $leave->id]) }}" method="POST" class="d-grid" onsubmit="return confirm('هل أنت متأكد من طلب إلغاء هذه الإجازة؟');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-warning d-flex align-items-center justify-content-center gap-2 py-2">
                                    <i class="bi bi-x-circle"></i>
                                    <span>طلب إلغاء الإجازة</span>
                                </button>
                            </form>
                        @elseif($leave->status === 'rejected')
                            <div class="alert alert-secondary mb-0 text-center small">
                                <i class="bi bi-info-circle me-1"></i>
                                لا توجد إجراءات متاحة لطلب مرفوض
                            </div>
                        @else
                            <div class="alert alert-light mb-0 text-center text-muted small">
                                <i class="bi bi-lock me-1"></i>
                                للعرض فقط
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Summary Card --}}
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-primary-subtle text-primary p-3 rounded-circle">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <div>
                            <div class="small text-muted">إجمالي المدة</div>
                            <div class="fw-bold fs-5">{{ $leave->days }} أيام</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
