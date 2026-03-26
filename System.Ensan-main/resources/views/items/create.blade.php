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
                        <li class="breadcrumb-item active" aria-current="page">إضافة صنف جديد</li>
                    </ol>
                </nav>
                <h4 class="mb-0">
                    <i class="bi bi-box text-primary"></i>
                    إضافة صنف جديد
                </h4>
            </div>
            <a href="{{ route('items.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('items.store') }}">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-medium">اسم الصنف <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required
                                placeholder="مثال: أرز، سكر، زيت...">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">SKU (رمز الصنف)</label>
                            <input type="text" name="sku" class="form-control" placeholder="اختياري">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">الوحدة</label>
                            <input type="text" name="unit" class="form-control" placeholder="مثال: كيلو، كرتونة، قطعة...">
                        </div>

                        <div class="col-12 mt-3 mb-2">
                            <h6 class="text-primary border-bottom pb-2">بيانات السعر</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">السعر قبل الخصم (EGP)</label>
                            <input type="number" step="0.01" name="original_price" id="original_price" class="form-control"
                                placeholder="0.00">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">نسبة الخصم %</label>
                            <input type="number" step="0.01" name="discount_percentage" id="discount_percentage"
                                class="form-control" value="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">السعر بعد الخصم / التقديري (EGP)</label>
                            <input type="number" step="0.01" name="estimated_value" id="estimated_value"
                                class="form-control" placeholder="0.00">
                        </div>

                        <script>
                            document.addEventListener('DOMContentLoaded', function () {
                                const originalInput = document.getElementById('original_price');
                                const discountInput = document.getElementById('discount_percentage');
                                const finalInput = document.getElementById('estimated_value');

                                function calc() {
                                    const original = parseFloat(originalInput.value) || 0;
                                    const discount = parseFloat(discountInput.value) || 0;
                                    const final = original - (original * discount / 100);
                                    finalInput.value = final.toFixed(2);
                                }

                                originalInput.addEventListener('input', calc);
                                discountInput.addEventListener('input', calc);
                            });
                        </script>
                    </div>

                    <div class="mt-4 pt-3 border-top d-flex gap-2">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i> حفظ الصنف
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-light px-4">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection