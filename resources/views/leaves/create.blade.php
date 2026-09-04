@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <h4 class="mb-0">طلب إجازة جديدة</h4>
                <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('leaves.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    <option value="annual">سنوية</option>
                                    <option value="sick">مرضية</option>
                                    <option value="emergency">عارضة</option>
                                    <option value="unpaid">بدون راتب</option>
                                    <option value="other">أخرى</option>
                                </select>
                            </div>
                            <div class="col-md-6"></div>

                            <div class="col-md-6">
                                <label class="form-label">من تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required
                                    value="{{ date('Y-m-d') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">السبب / ملاحظات <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required></textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-send me-1"></i> تقديم الطلب
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

