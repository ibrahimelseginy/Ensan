@extends('layouts.app')
@section('content')
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-clock-history text-primary"></i>
            تفاصيل حضور موظف
        </h4>
        <div class="btn-group">
            <a href="{{ route('employee-attendance.index') }}" class="btn btn-outline-secondary">
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
                        <h5 class="mb-0">معلومات السجل</h5>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">الموظف</span>
                                <span class="info-value fw-bold fs-6">
                                    @if($rec->user)
                                        <a href="{{ route('users.show', $rec->user_id) }}" class="text-decoration-none">
                                            {{ $rec->user->name }}
                                        </a>
                                    @else
                                        —
                                    @endif
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">التاريخ</span>
                                <span class="info-value fw-bold">
                                    <i class="bi bi-calendar-event me-1 text-muted"></i>
                                    {{ $rec->date->format('Y-m-d') }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">وقت الدخول</span>
                                <span class="badge bg-success-subtle text-success fs-6 fw-normal px-3 py-2">
                                    <i class="bi bi-box-arrow-in-right me-1"></i>
                                    {{ $rec->check_in_at ?? '—' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-row">
                                <span class="info-label text-muted d-block small mb-1">وقت الخروج</span>
                                <span class="badge bg-danger-subtle text-danger fs-6 fw-normal px-3 py-2">
                                    <i class="bi bi-box-arrow-left me-1"></i>
                                    {{ $rec->check_out_at ?? '—' }}
                                </span>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="info-row bg-light p-3 rounded-3">
                                <span class="info-label text-muted d-block small mb-2">ملاحظات</span>
                                <p class="mb-0 text-muted">{{ $rec->notes ?? 'لا توجد ملاحظات' }}</p>
                            </div>
                        </div>

                        <div class="col-12">
                            <hr class="my-2">
                            <h6 class="text-primary mb-3"><i class="bi bi-star me-1"></i> تقييم الأداء</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="info-row">
                                        <span class="info-label text-muted d-block small mb-1">التقييم</span>
                                        <div class="fw-bold fs-5">
                                            @if($rec->rating)
                                                <span class="text-warning">
                                                    @for($i=1; $i<=5; $i++)
                                                        <i class="bi bi-star{{ $i <= $rec->rating ? '-fill' : '' }}"></i>
                                                    @endfor
                                                </span>
                                            @else
                                                <span class="text-muted small fs-6">غير مقيم</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="info-row">
                                        <span class="info-label text-muted d-block small mb-1">ملاحظات التقييم</span>
                                        <p class="mb-0 text-muted small">{{ $rec->evaluation_notes ?? '—' }}</p>
                                    </div>
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
                    <div class="d-grid gap-3">
                        @if(auth()->check())
                            <a href="{{ route('employee-attendance.edit', ['employee_attendance' => $rec->id]) }}" class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2 shadow-sm border-0" style="background-color: #2ecc71;">
                                <i class="bi bi-pencil-square"></i>
                                <span class="fw-bold">تعديل البيانات</span>
                            </a>
                            
                            <form action="{{ route('employee-attendance.destroy', ['employee_attendance' => $rec->id]) }}" method="POST" class="d-grid" onsubmit="return confirm('هل أنت متأكد من طلب إلغاء هذا السجل؟');">
                                @csrf @method('DELETE')
                                <button class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center gap-2 py-2" style="border-color: #2c3e50; color: #fff; background: rgba(255,255,255,0.05);">
                                    <i class="bi bi-x-circle"></i>
                                    <span>طلب إلغاء</span>
                                </button>
                            </form>
                        @endif

                        <a href="{{ route('employee-attendance.index') }}" class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center gap-2 py-2" style="border-color: #2c3e50; color: #fff; background: rgba(255,255,255,0.05);">
                            <i class="bi bi-arrow-right-circle"></i>
                            <span>رجوع للقائمة</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


