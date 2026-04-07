@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-pencil text-primary"></i>
            تعديل الإغلاق المالي
        </h4>
        <a href="{{ route('closures.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('closures.update', $closure) }}">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label form-label-required">التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ $closure->date->format('Y-m-d') }}"
                            required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الفرع</label>
                        <input name="branch" class="form-control" value="{{ $closure->branch }}">
                    </div>
                </div>

                {{-- Approval Option --}}
                @if(request()->user() && (request()->user()->hasRole('admin') || request()->user()->hasRole('manager')))
                    <div class="row g-3 mt-3">
                        <div class="col-12">
                            <label class="form-label fw-bold">الاعتماد</label>
                            <div class="d-flex gap-3 align-items-center">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="approved" id="approvedYes" value="1" {{ $closure->approved ? 'checked' : '' }}>
                                    <label class="form-check-label text-success fw-bold" for="approvedYes">
                                        <i class="bi bi-check-circle me-1"></i> نعم (معتمد)
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="approved" id="approvedNo" value="0" {{ !$closure->approved ? 'checked' : '' }}>
                                    <label class="form-check-label text-warning fw-bold" for="approvedNo">
                                        <i class="bi bi-clock me-1"></i> لا (قيد المراجعة)
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <input type="hidden" name="approved" value="{{ $closure->approved ? '1' : '0' }}">
                @endif

                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('closures.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

