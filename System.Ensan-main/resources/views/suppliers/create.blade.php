@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <h4 class="mb-0">إضافة مورد جديد</h4>
                <a href="{{ route('suppliers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('suppliers.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم الصنف <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">اسم التاجر</label>
                                <input type="text" name="merchant_name" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">رقم التليفون</label>
                                <input type="text" name="phone" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">العنوان</label>
                                <input type="text" name="address" class="form-control">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">اسم المصدر</label>
                                <input type="text" name="source_name" class="form-control">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> حفظ
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection