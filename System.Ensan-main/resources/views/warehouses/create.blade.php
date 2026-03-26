@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('warehouses.index') }}"
                                class="text-decoration-none">المخازن</a></li>
                        <li class="breadcrumb-item active" aria-current="page">إضافة مخزن جديد</li>
                    </ol>
                </nav>
                <h4 class="mb-0">
                    <i class="bi bi-box-seam text-primary"></i>
                    إضافة مخزن جديد
                </h4>
            </div>
            <a href="{{ route('warehouses.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('warehouses.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">اسم المخزن <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: المخزن الرئيسي">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">الموقع</label>
                            <input type="text" name="location" class="form-control" placeholder="العنوان أو وصف المكان">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> حفظ المخزن
                        </button>
                        <a href="{{ route('warehouses.index') }}" class="btn btn-light px-4">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection