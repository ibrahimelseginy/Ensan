@extends('layouts.app')

@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                    <div>
                        <h4 class="fw-bold text-body mb-0">تفاصيل ساعات التطوع</h4>
                        <p class="text-muted small mt-1 mb-0">عرض بيانات الساعات المسجلة.</p>
                    </div>
                    <div>
                        <a href="{{ route('volunteer-hours.edit', $vh) }}" class="btn btn-primary rounded-pill px-4">
                            <i class="bi bi-pencil me-2"></i>تعديل
                        </a>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">المتطوع</label>
                                <div class="fw-bold fs-5 text-body">{{ $vh->user?->name ?? 'غير محدد' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">التاريخ</label>
                                <div class="fw-bold fs-5 text-body">{{ $vh->date->format('Y-m-d') }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">عدد الساعات</label>
                                <div class="fw-bold fs-4 text-primary">{{ $vh->hours }} <span class="fs-6 text-muted fw-normal">ساعة</span></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <label class="d-block text-secondary small text-uppercase mb-1">المهمة / النشاط</label>
                                <div class="fw-bold fs-5 text-body">{{ $vh->task ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-0 py-3 px-4">
                    <a href="{{ route('volunteer-hours.index') }}" class="btn btn-secondary-subtle rounded-pill px-4">
                        <i class="bi bi-arrow-right me-2"></i>العودة للقائمة
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


