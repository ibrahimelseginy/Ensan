@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-exclamation-triangle text-primary"></i>
            إضافة شكوى جديدة
        </h4>
        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('complaints.store') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">المصدر</label><select name="source_type"
                            class="form-select" required>
                            <option value="donor">متبرع</option>
                            <option value="beneficiary">مستفيد</option>
                            <option value="employee">موظف</option>
                        </select></div>
                    <div class="col-md-6"><label class="form-label">رقم المصدر</label><input name="source_id"
                            class="form-control" required></div>
                    <div class="col-md-6"><label class="form-label">المسؤول</label><select name="against_user_id"
                            class="form-select">
                            <option value="">—</option>@foreach($users as $u)<option value="{{ $u->id }}">{{ $u->name }}
                            </option>@endforeach
                        </select></div>
                    <div class="col-md-12"><label class="form-label">العنوان</label><input name="subject"
                            class="form-control" required></div>
                    <div class="col-md-12"><label class="form-label">النص</label><textarea name="message"
                            class="form-control" rows="3" required></textarea></div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ الشكوى
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

