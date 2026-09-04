@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('items.index') }}"
                                class="text-decoration-none">الأصناف</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $item->name }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0">
                    <i class="bi bi-box text-primary"></i>
                    تعديل الصنف
                </h4>
            </div>
            <a href="{{ route('items.show', $item) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('items.update', $item) }}">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">اسم الصنف <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">SKU (رمز الصنف)</label>
                            <input type="text" name="sku" class="form-control" value="{{ $item->sku }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">الوحدة</label>
                            <input type="text" name="unit" class="form-control" value="{{ $item->unit }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">القيمة التقديرية (EGP)</label>
                            <input type="number" step="0.01" name="estimated_value" class="form-control"
                                value="{{ $item->estimated_value }}">
                        </div>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> حفظ التعديلات
                        </button>
                        <a href="{{ route('items.show', $item) }}" class="btn btn-light px-4">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

