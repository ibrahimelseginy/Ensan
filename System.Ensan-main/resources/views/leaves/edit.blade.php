@extends('layouts.app')
@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <h4 class="mb-0">تعديل طلب إجازة</h4>
                <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    <form action="{{ route('leaves.update', ['leave' => $leave->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">نوع الإجازة <span class="text-danger">*</span></label>
                                <select name="type" class="form-select" required>
                                    @foreach(['annual' => 'سنوية', 'sick' => 'مرضية', 'emergency' => 'عارضة', 'unpaid' => 'بدون راتب', 'other' => 'أخرى'] as $val => $label)
                                        <option value="{{ $val }}" {{ $leave->type == $val ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                @if(request()->user() && request()->user()->hasPermission('leaves.manage'))
                                    <label class="form-label">الحالة</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" {{ $leave->status == 'pending' ? 'selected' : '' }}>معلق</option>
                                        <option value="approved" {{ $leave->status == 'approved' ? 'selected' : '' }}>مقبول</option>
                                        <option value="rejected" {{ $leave->status == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">من تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="start_date" class="form-control" required
                                    value="{{ $leave->start_date->format('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" class="form-control" required
                                    value="{{ $leave->end_date->format('Y-m-d') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">السبب / ملاحظات <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required>{{ $leave->reason }}</textarea>
                            </div>
                            
                            @if(request()->user() && request()->user()->hasPermission('leaves.manage'))
                                <div class="col-12">
                                    <label class="form-label">سبب الرفض (في حالة الرفض)</label>
                                    <textarea name="rejection_reason" class="form-control" rows="2">{{ $leave->rejection_reason }}</textarea>
                                </div>
                            @endif

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-save me-1"></i> حفظ التعديلات
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
