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
                        <div class="col-md-4">
                            <label class="form-label fw-medium">القسم التابع له (اختياري)</label>
                            <input type="text" name="department" class="form-control" placeholder="مثال: اللوجستيات، دار الضيافة، الإغاثة">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">ربط بدار ضيافة (اختياري)</label>
                            <select name="guest_house_id" class="form-select">
                                <option value="">— مخزن عام / غير مرتبط بدار —</option>
                                @foreach($guestHouses ?? [] as $gh)
                                    <option value="{{ $gh->id }}">{{ $gh->name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">يمكن ربط أكثر من مخزن بنفس الدار، وسيظهر للدار مخازنها فقط.</div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-medium">ربط بمشروع (اختياري)</label>
                            <select name="project_id" class="form-select">
                                <option value="">— غير مرتبط بمشروع محدد —</option>
                                @foreach($projects ?? [] as $proj)
                                    <option value="{{ $proj->id }}">{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">أمين / مسؤول المخزن</label>
                            <select name="manager_id" class="form-select">
                                <option value="">— اختر المسؤول —</option>
                                @foreach($employees ?? [] as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->job_title ? '('.$emp->job_title.')' : '' }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-medium">موظفون مصرح لهم بالتعامل على المخزن</label>
                            <select name="assigned_user_ids[]" class="form-select" multiple size="4">
                                @foreach($employees ?? [] as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} {{ $emp->department ? ' - '.$emp->department : '' }}</option>
                                @endforeach
                            </select>
                            <div class="form-text small">اضغط Ctrl أو Cmd لتحديد أكثر من موظف.</div>
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

