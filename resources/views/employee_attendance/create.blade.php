@extends('layouts.app')
@section('content')
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <h4 class="mb-0" id="pageTitle">
                    <i class="bi bi-person-check text-primary"></i>
                    تسجيل حضور أو غياب موظف
                </h4>
                <p class="text-muted small mt-1 mb-0">قم بتسجيل بيانات الحضور أو اعتماد الغياب مع الخصم التلقائي من رصيد الإجازات.</p>
            </div>
            <a href="{{ route('employee-attendance.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('employee-attendance.store') }}" id="attendanceForm">
                            @csrf
                            <input type="hidden" name="is_absence" id="isAbsenceInput" value="0">

                            {{-- Record Type Selector --}}
                            <div class="p-3 mb-4 rounded-3 border bg-light d-flex flex-wrap align-items-center justify-content-between gap-3">
                                <div>
                                    <span class="fw-bold text-dark d-block mb-1">نوع التسجيل:</span>
                                    <small class="text-muted" id="typeHelpText">اختر نوع السجل؛ تسجيل الغياب يخصم يوماً تلقائياً من الرصيد الحالي للموظف.</small>
                                </div>
                                <div class="btn-group" role="group">
                                    <input type="radio" class="btn-check" name="record_mode" id="modeAttendance" value="attendance" checked onchange="toggleRecordMode()">
                                    <label class="btn btn-outline-success fw-bold px-3" for="modeAttendance">
                                        <i class="bi bi-person-check me-1"></i> تسجيل حضور
                                    </label>

                                    <input type="radio" class="btn-check" name="record_mode" id="modeAbsence" value="absence" onchange="toggleRecordMode()">
                                    <label class="btn btn-outline-danger fw-bold px-3" for="modeAbsence">
                                        <i class="bi bi-person-x me-1"></i> تسجيل غياب (خصم من الرصيد)
                                    </label>
                                </div>
                            </div>

                            {{-- Absence Info Notice --}}
                            <div id="absenceNotice" class="alert alert-danger d-none rounded-3 py-2 px-3 mb-4 small fw-bold">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                                تنبيه: سيتم خصم يوم واحد (1 يوم) تلقائياً من رصيد الإجازات الحالي للموظف المختار فور حفظ هذا السجل.
                            </div>

                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">الموظف <span class="text-danger">*</span></label>
                                    <select name="user_id" id="userSelect" class="form-select form-select-lg fs-6" required>
                                        <option value="">اختر الموظف...</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}">
                                                {{ $u->name }} (الرصيد المتبقي: {{ $u->leave_balance ?? $u->annual_leave_quota ?? 21 }} يوم)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">التاريخ <span class="text-danger">*</span></label>
                                    <input type="date" name="date" class="form-control form-control-lg fs-6"
                                        value="{{ date('Y-m-d') }}" required>
                                </div>
                                
                                {{-- Check-in / Out inputs (visible only for attendance) --}}
                                <div class="col-md-6 time-fields">
                                    <label class="form-label fw-medium text-secondary">وقت الدخول</label>
                                    <input type="text" name="check_in_at" id="checkInInput"
                                        class="form-control form-control-lg fs-6 time-picker" placeholder="10:00">
                                </div>
                                <div class="col-md-6 time-fields">
                                    <label class="form-label fw-medium text-secondary">وقت الخروج</label>
                                    <input type="text" name="check_out_at" id="checkOutInput"
                                        class="form-control form-control-lg fs-6 time-picker" placeholder="17:00">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-medium text-secondary" id="notesLabel">ملاحظات</label>
                                    <textarea name="notes" id="notesInput" class="form-control" rows="3"
                                        placeholder="أي ملاحظات إضافية حول الحضور..."></textarea>
                                </div>

                                <div class="col-12 mt-4" id="evalSection">
                                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">تقييم الأداء</h5>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium text-secondary d-block">التقييم (من 5)</label>
                                            <div class="btn-group" role="group" aria-label="Rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" class="btn-check" name="rating" id="rating{{$i}}"
                                                        value="{{$i}}" autocomplete="off">
                                                    <label class="btn btn-outline-warning" for="rating{{$i}}">{{$i}} <i
                                                            class="bi bi-star-fill"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium text-secondary">ملاحظات التقييم</label>
                                            <textarea name="evaluation_notes" class="form-control" rows="2"
                                                placeholder="أضف ملاحظات حول أداء الموظف في هذا اليوم..."></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end align-items-center mt-5">
                                <a href="{{ route('employee-attendance.index') }}"
                                    class="btn btn-light btn-lg px-4 me-2 rounded-pill text-secondary">إلغاء</a>
                                <button type="submit" id="submitBtn" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                    حفظ السجل
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleRecordMode() {
            const isAbsence = document.getElementById('modeAbsence').checked;
            document.getElementById('isAbsenceInput').value = isAbsence ? '1' : '0';
            
            const timeFields = document.querySelectorAll('.time-fields');
            const evalSection = document.getElementById('evalSection');
            const notice = document.getElementById('absenceNotice');
            const notesInput = document.getElementById('notesInput');
            const notesLabel = document.getElementById('notesLabel');
            const submitBtn = document.getElementById('submitBtn');

            if (isAbsence) {
                timeFields.forEach(el => el.classList.add('d-none'));
                evalSection.classList.add('d-none');
                notice.classList.remove('d-none');
                notesLabel.textContent = 'سبب الغياب (ملاحظات)';
                notesInput.placeholder = 'غياب بدون إذن مسبق / عذر شخصي...';
                if (!notesInput.value) {
                    notesInput.value = 'غياب';
                }
                submitBtn.className = 'btn btn-danger btn-lg px-5 rounded-pill shadow-sm';
                submitBtn.textContent = 'تأكيد تسجيل الغياب والخصم من الرصيد';
            } else {
                timeFields.forEach(el => el.classList.remove('d-none'));
                evalSection.classList.remove('d-none');
                notice.classList.add('d-none');
                notesLabel.textContent = 'ملاحظات الحضور';
                notesInput.placeholder = 'أي ملاحظات إضافية حول الحضور...';
                if (notesInput.value === 'غياب') {
                    notesInput.value = '';
                }
                submitBtn.className = 'btn btn-primary btn-lg px-5 rounded-pill shadow-sm';
                submitBtn.textContent = 'حفظ السجل';
            }
        }
    </script>
@endsection
