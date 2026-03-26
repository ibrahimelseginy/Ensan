@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-exclamation-triangle text-primary"></i>
            تعديل الشكوى
        </h4>
        <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('complaints.update', $complaint) }}">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label">العنوان</label>
                        <input type="text" name="subject" class="form-control" value="{{ $complaint->subject }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">النص</label>
                        <textarea name="message" class="form-control" rows="4" required>{{ $complaint->message }}</textarea>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="open" @selected($complaint->status === 'open')>مفتوحة</option>
                            <option value="in_progress" @selected($complaint->status === 'in_progress')>جارية</option>
                            <option value="closed" @selected($complaint->status === 'closed')>مغلقة</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">المسؤول</label>
                        <select name="against_user_id" class="form-select">
                            <option value="">—</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected($complaint->against_user_id == $u->id)>{{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('complaints.show', $complaint) }}" class="btn btn-outline-secondary">
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