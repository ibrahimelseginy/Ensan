@extends('layouts.app')
@section('content')
    @php
        $currentType = old('type', $leave->type);
        $typeLabels = \App\Models\Leave::typeLabels();
    @endphp
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <h4 class="mb-0">{{ $currentType === 'permission' ? 'تعديل طلب إذن' : 'تعديل طلب إجازة / إذن' }}</h4>
                <a href="{{ route('leaves.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-4">
                    @if (isset($errors) && $errors->any())
                        <div class="alert alert-danger rounded-3">
                            <ul class="mb-0 small">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('leaves.update', ['leave' => $leave->id]) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">نوع الطلب <span class="text-danger">*</span></label>
                                <select name="type" id="leaveTypeSelect" class="form-select" required>
                                    @foreach($typeLabels as $val => $label)
                                        <option value="{{ $val }}" @selected($currentType === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                @if(request()->user() && request()->user()->hasPermission('leaves.manage'))
                                    <label class="form-label">الحالة</label>
                                    <select name="status" class="form-select">
                                        <option value="pending" @selected(old('status', $leave->status) == 'pending')>معلق</option>
                                        <option value="approved" @selected(old('status', $leave->status) == 'approved')>مقبول</option>
                                        <option value="rejected" @selected(old('status', $leave->status) == 'rejected')>مرفوض</option>
                                    </select>
                                @endif
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" id="startDateLabel">
                                    {{ $currentType === 'permission' ? 'تاريخ الإذن' : 'من تاريخ' }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="startDateInput" class="form-control" required
                                    value="{{ old('start_date', $leave->start_date->format('Y-m-d')) }}">
                            </div>
                            <div class="col-md-6 {{ $currentType === 'permission' ? 'd-none' : '' }}" id="endDateGroup">
                                <label class="form-label">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="endDateInput" class="form-control"
                                    value="{{ old('end_date', $leave->end_date->format('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6 {{ $currentType === 'permission' ? '' : 'd-none' }}" id="permissionFromTimeGroup">
                                <label class="form-label">من الساعة <span class="text-danger">*</span></label>
                                <input type="time" name="from_time" id="fromTimeInput" class="form-control"
                                    value="{{ old('from_time', $leave->from_time_formatted ?? '09:00') }}">
                            </div>
                            <div class="col-md-6 {{ $currentType === 'permission' ? '' : 'd-none' }}" id="permissionToTimeGroup">
                                <label class="form-label">إلى الساعة <span class="text-danger">*</span></label>
                                <input type="time" name="to_time" id="toTimeInput" class="form-control"
                                    value="{{ old('to_time', $leave->to_time_formatted ?? '11:00') }}">
                            </div>

                            <div class="col-12">
                                <label class="form-label">السبب / ملاحظات <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required>{{ old('reason', $leave->reason) }}</textarea>
                            </div>

                            @if(request()->user() && request()->user()->hasPermission('leaves.manage'))
                                <div class="col-12">
                                    <label class="form-label">سبب الرفض (في حالة الرفض)</label>
                                    <textarea name="rejection_reason" class="form-control" rows="2">{{ old('rejection_reason', $leave->rejection_reason) }}</textarea>
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

    <script>
        (function () {
            const typeSelect = document.getElementById('leaveTypeSelect');
            const startDateInput = document.getElementById('startDateInput');
            const endDateInput = document.getElementById('endDateInput');
            const endDateGroup = document.getElementById('endDateGroup');
            const startDateLabel = document.getElementById('startDateLabel');
            const fromTimeGroup = document.getElementById('permissionFromTimeGroup');
            const toTimeGroup = document.getElementById('permissionToTimeGroup');
            const fromTimeInput = document.getElementById('fromTimeInput');
            const toTimeInput = document.getElementById('toTimeInput');

            function syncLeaveMode() {
                const isPerm = typeSelect.value === 'permission';
                endDateGroup.classList.toggle('d-none', isPerm);
                fromTimeGroup.classList.toggle('d-none', !isPerm);
                toTimeGroup.classList.toggle('d-none', !isPerm);

                endDateInput.required = !isPerm;
                fromTimeInput.required = isPerm;
                toTimeInput.required = isPerm;

                startDateLabel.innerHTML = (isPerm ? 'تاريخ الإذن' : 'من تاريخ') + ' <span class="text-danger">*</span>';
                if (isPerm && startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
            }

            typeSelect.addEventListener('change', syncLeaveMode);
            startDateInput.addEventListener('change', function () {
                if (typeSelect.value === 'permission') {
                    endDateInput.value = startDateInput.value;
                }
            });
            syncLeaveMode();
        })();
    </script>
@endsection
