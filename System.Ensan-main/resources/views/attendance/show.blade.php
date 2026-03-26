@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-body mb-0">تفاصيل سجل الحضور</h4>
                        <p class="text-muted small mt-1 mb-0">عرض بيانات الحضور والانصراف.</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-attendance.edit', $rec) }}" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-pencil me-2"></i>تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">المتطوع</label>
                                <div class="fw-bold fs-5 text-body">{{ $rec->user?->name ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">التاريخ</label>
                                <div class="fw-bold fs-5 text-body">{{ $rec->date->format('Y-m-d') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">وقت الدخول</label>
                                <div class="fw-bold fs-5 {{ $rec->check_in_at ? 'text-success' : 'text-muted' }}">
                                    {{ $rec->check_in_at ? \Carbon\Carbon::parse($rec->check_in_at)->format('H:i') : '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">وقت الخروج</label>
                                <div class="fw-bold fs-5 {{ $rec->check_out_at ? 'text-secondary' : 'text-muted' }}">
                                    {{ $rec->check_out_at ? \Carbon\Carbon::parse($rec->check_out_at)->format('H:i') : '—' }}
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">ملاحظات</label>
                                <div class="text-body">{{ $rec->notes ?? 'لا توجد ملاحظات' }}</div>
                            </div>
                        </div>

                        <div class="col-12 mt-4">
                            <h5 class="fw-bold text-body border-bottom pb-2 mb-3">تقييم الأداء</h5>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-body-tertiary rounded-3">
                                        <label class="d-block text-secondary small text-uppercase mb-1">التقييم</label>
                                        <div class="fw-bold fs-5">
                                            @if($rec->rating)
                                                <span class="text-warning">
                                                    @for($i=1; $i<=5; $i++)
                                                        <i class="bi bi-star{{ $i <= $rec->rating ? '-fill' : '' }}"></i>
                                                    @endfor
                                                </span>
                                            @else
                                                <span class="text-muted small">غير مقيم</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="p-3 bg-body-tertiary rounded-3">
                                        <label class="d-block text-secondary small text-uppercase mb-1">ملاحظات التقييم</label>
                                        <div class="text-body">{{ $rec->evaluation_notes ?? 'لا توجد ملاحظات تقييم' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-3 px-4">
                    <a href="{{ route('volunteer-attendance.index') }}" class="btn btn-secondary-subtle rounded-pill px-4">
                        <i class="bi bi-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
