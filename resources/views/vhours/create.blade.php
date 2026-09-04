@extends('layouts.app')
@section('content')
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <h4 class="mb-0">
                    <i class="bi bi-clock-history text-primary"></i>
                    تسجيل ساعات تطوع
                </h4>
                <p class="text-muted small mt-1 mb-0">قم بتسجيل عدد ساعات العمل التطوعي والنشاط المنجز.</p>
            </div>
            <a href="{{ route('volunteer-hours.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('volunteer-hours.store') }}">
                            @csrf
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">المتطوع <span
                                            class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select form-select-lg fs-6" required>
                                        <option value="">اختر المتطوع...</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">التاريخ <span
                                            class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control form-control-lg fs-6"
                                        value="{{ now()->format('Y-m-d') }}" required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">عدد الساعات <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.5" name="hours" class="form-control form-control-lg fs-6"
                                        required placeholder="مثال: 3.5">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">المهمة / النشاط</label>
                                    <input type="text" name="task" class="form-control form-control-lg fs-6"
                                        placeholder="مثال: تنظيم الحفل، إدخال بيانات">
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center mt-5">
                                <a href="{{ route('volunteer-hours.index') }}"
                                    class="btn btn-light btn-lg px-4 me-2 rounded-pill text-secondary">إلغاء</a>
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">حفظ
                                    السجل</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

