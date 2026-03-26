@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4">
                    <h4 class="fw-bold text-body mb-0">تعديل سجل ساعات</h4>
                    <p class="text-muted small mt-1">تحديث بيانات ساعات التطوع والنشاط المنجز.</p>
                </div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('volunteer-hours.update', $vh) }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">المتطوع</label>
                                <input type="text" class="form-control form-control-lg fs-6 bg-body-secondary" value="{{ $vh->user?->name }}" disabled>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">التاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="date" class="form-control form-control-lg fs-6" value="{{ $vh->date->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">عدد الساعات <span class="text-danger">*</span></label>
                                <input type="number" step="0.5" name="hours" class="form-control form-control-lg fs-6" value="{{ $vh->hours }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-medium text-secondary">المهمة / النشاط</label>
                                <input type="text" name="task" class="form-control form-control-lg fs-6" value="{{ $vh->task }}">
                            </div>
                        </div>
                        <div class="d-flex justify-content-end align-items-center mt-5">
                            <a href="{{ route('volunteer-hours.index') }}" class="btn btn-secondary-subtle btn-lg px-4 me-2 rounded-pill text-secondary">إلغاء</a>
                            <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
