@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-signpost-2 text-primary"></i>
            إضافة خط سير جديد
        </h4>
        <a href="{{ route('travel-routes.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('travel-routes.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-required">الاسم</label>
                        <input name="name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الوصف</label>
                        <input name="description" class="form-control">
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('travel-routes.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection