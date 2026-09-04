@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-signpost-2 text-primary"></i>
            تعديل خط السير
        </h4>
        <a href="{{ route('travel-routes.show', $route) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('travel-routes.update', $route) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-required">الاسم</label>
                        <input name="name" class="form-control" value="{{ $route->name }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الوصف</label>
                        <textarea name="description" class="form-control" rows="3">{{ $route->description }}</textarea>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('travel-routes.show', $route) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ التغييرات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

