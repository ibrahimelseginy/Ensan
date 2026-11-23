@extends('layouts.app')
@section('content')
    @php
        $initialType = old('type', request('type') === 'permission' ? 'permission' : 'annual');
        $typeLabels = \App\Models\Leave::typeLabels();
    @endphp
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header">
                <h4 class="mb-0" id="leavePageTitle">
                    {{ $initialType === 'permission' ? 'طلب إذن جديد (بالساعات)' : 'طلب إجازة أو إذن جديد' }}
                </h4>
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

                    <form action="{{ route('leaves.store') }}" method="POST">
                        @csrf
                        <div class="row g-3">
                            @if(!empty($employees) && $employees->isNotEmpty())
                                <div class="col-md-6">
                                    <label class="form-label fw-bold"><i class="bi bi-person me-1"></i>الموظف صاحب الإجازة <span class="text-danger">*</span></label>
                                    <select name="user_id" class="form-select" required>
                                        @foreach($employees as $emp)
                                            <option value="{{ $emp->id }}" @selected(old('user_id', auth()->id()) == $emp->id)>
                                                {{ $emp->name }} (الرصيد: {{ $emp->leave_balance ?? $emp->annual_leave_quota ?? 21 }} / {{ $emp->annual_leave_quota ?? 21 }} يوم)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold"><i class="bi bi-shield-check me-1"></i>حالة الطلب</label>
                                    <select name="status" class="form-select">
                                        <option value="approved" @selected(old('status', 'approved') === 'approved')>مقبول فوراً (خصم تلقائي من الرصيد)</option>
                                        <option value="pending" @selected(old('status') === 'pending')>معلق (بانتظار المراجعة)</option>
                                    </select>
                                </div>
                            @endif

                            <div class="col-md-6">
                                <label class="form-label">نوع الطلب <span class="text-danger">*</span></label>
                                <select name="type" id="leaveTypeSelect" class="form-select" required>
                                    @foreach($typeLabels as $val => $label)
                                        <option value="{{ $val }}" @selected($initialType === $val)>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6 d-flex align-items-end">
                                <div id="permissionBadgeInfo" class="alert alert-info py-2 px-3 mb-0 w-100 small {{ $initialType === 'permission' ? '' : 'd-none' }}">
                                    <i class="bi bi-clock-history me-1"></i>
                                    طلب إذن بالساعات (من الساعة ... إلى الساعة ...) بدون خصم من رصيد الإجازات السنوية.
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label" id="startDateLabel">
                                    {{ $initialType === 'permission' ? 'تاريخ الإذن' : 'من تاريخ' }} <span class="text-danger">*</span>
                                </label>
                                <input type="date" name="start_date" id="startDateInput" class="form-control" required
                                    value="{{ old('start_date', date('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6 {{ $initialType === 'permission' ? 'd-none' : '' }}" id="endDateGroup">
                                <label class="form-label">إلى تاريخ <span class="text-danger">*</span></label>
                                <input type="date" name="end_date" id="endDateInput" class="form-control"
                                    value="{{ old('end_date', date('Y-m-d')) }}">
                            </div>

                            <div class="col-md-6 {{ $initialType === 'permission' ? '' : 'd-none' }}" id="permissionFromTimeGroup">
                                <label class="form-label">من الساعة <span class="text-danger">*</span></label>
                                <input type="time" name="from_time" id="fromTimeInput" class="form-control"
                                    value="{{ old('from_time', '09:00') }}">
                            </div>

                            <div class="col-md-6 {{ $initialType === 'permission' ? '' : 'd-none' }}" id="permissionToTimeGroup">
                                <label class="form-label">إلى الساعة <span class="text-danger">*</span></label>
                                <input type="time" name="to_time" id="toTimeInput" class="form-control"
                                    value="{{ old('to_time', '11:00') }}">
                            </div>

                            <div class="col-12 {{ $initialType === 'permission' ? '' : 'd-none' }}" id="permissionDurationSummary">
                                <div class="bg-light border rounded-3 px-3 py-2 small text-primary fw-semibold">
                                    <i class="bi bi-hourglass-split me-1"></i>
                                    مدة الإذن المحسوبة: <span id="permissionDurationText">2 ساعة</span>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label">السبب / ملاحظات <span class="text-danger">*</span></label>
                                <textarea name="reason" class="form-control" rows="3" required>{{ old('reason') }}</textarea>
                            </div>

                            <div class="col-12 text-end mt-4">
                                <button type="submit" class="btn btn-primary px-4">
                                    <i class="bi bi-send me-1"></i> تقديم الطلب
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
            const durationBox = document.getElementById('permissionDurationSummary');
            const durationText = document.getElementById('permissionDurationText');
            const badgeInfo = document.getElementById('permissionBadgeInfo');
            const pageTitle = document.getElementById('leavePageTitle');

            function updateDuration() {
                if (!fromTimeInput.value || !toTimeInput.value) {
                    durationText.textContent = '—';
                    return;
                }
                const [fh, fm] = fromTimeInput.value.split(':').map(Number);
                const [th, tm] = toTimeInput.value.split(':').map(Number);
                const diff = (th * 60 + tm) - (fh * 60 + fm);
                if (diff <= 0) {
                    durationText.textContent = 'وقت النهاية يجب أن يكون بعد وقت البداية';
                    return;
                }
                const h = Math.floor(diff / 60);
                const m = diff % 60;
                if (h > 0 && m > 0) durationText.textContent = `${h} ساعة و ${m} دقيقة`;
                else if (h > 0) durationText.textContent = `${h} ساعة`;
                else durationText.textContent = `${m} دقيقة`;
            }

            function syncLeaveMode() {
                const isPerm = typeSelect.value === 'permission';
                endDateGroup.classList.toggle('d-none', isPerm);
                fromTimeGroup.classList.toggle('d-none', !isPerm);
                toTimeGroup.classList.toggle('d-none', !isPerm);
                durationBox.classList.toggle('d-none', !isPerm);
                badgeInfo.classList.toggle('d-none', !isPerm);

                endDateInput.required = !isPerm;
                fromTimeInput.required = isPerm;
                toTimeInput.required = isPerm;

                startDateLabel.innerHTML = (isPerm ? 'تاريخ الإذن' : 'من تاريخ') + ' <span class="text-danger">*</span>';
                if (pageTitle) {
                    pageTitle.textContent = isPerm ? 'طلب إذن جديد (بالساعات)' : 'طلب إجازة أو إذن جديد';
                }
                if (isPerm && startDateInput.value) {
                    endDateInput.value = startDateInput.value;
                }
                if (isPerm) updateDuration();
            }

            typeSelect.addEventListener('change', syncLeaveMode);
            startDateInput.addEventListener('change', function () {
                if (typeSelect.value === 'permission') {
                    endDateInput.value = startDateInput.value;
                }
            });
            fromTimeInput.addEventListener('input', updateDuration);
            toTimeInput.addEventListener('input', updateDuration);

            syncLeaveMode();
        })();
    </script>
@endsection
