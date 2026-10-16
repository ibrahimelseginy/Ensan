@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="page-header d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-exclamation-triangle-fill text-warning me-2"></i>
                إضافة شكوى جديدة
            </h4>
            <div class="text-muted small">تسجيل شكوى جديدة وتحديد صاحب الشكوى ومتابعتها</div>
        </div>
        <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع للشكاوى
        </a>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2 mb-1">
                <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                <strong>يرجى تصحيح الأخطاء التالية:</strong>
            </div>
            <ul class="mb-0 ps-3">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4 p-md-5">
            <form method="POST" action="{{ route('complaints.store') }}" id="complaintForm">
                @csrf

                {{-- Hidden input for actual source_id sent to backend --}}
                <input type="hidden" name="source_id" id="hidden_source_id" value="{{ old('source_id', '') }}">

                <div class="row g-4">
                    {{-- كود التتبع المعتمد للشكوى مسبقاً --}}
                    <div class="col-12">
                        <div class="p-3.5 rounded-3 d-flex align-items-center justify-content-between flex-wrap gap-3" 
                             style="background: rgba(16, 185, 129, 0.08); border: 1.5px solid #10b981; border-radius: 14px;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-success">
                                    <i class="bi bi-upc-scan fs-3"></i>
                                </div>
                                <div>
                                    <div class="badge bg-success bg-opacity-15 text-success border border-success border-opacity-25 px-2.5 py-1 mb-1 fw-bold">
                                        <i class="bi bi-shield-check me-1"></i> كود التتبع الرسمي المخصص للشاكي
                                    </div>
                                    <div class="fw-bold font-monospace fs-4 text-success" id="displayTrackingCode" style="letter-spacing: 2px;">
                                        {{ $trackingCode ?? ('ENS-' . strtoupper(\Illuminate\Support\Str::random(6))) }}
                                    </div>
                                    <div class="text-muted small mt-0.5">
                                        يمكن إعطاء هذا الكود للشاكي فوراً لمتابعة مراحل الحل عبر الرابط العام: 
                                        <a href="{{ route('complaint.track') }}" target="_blank" class="fw-bold text-success text-decoration-none">
                                            {{ route('complaint.track') }}
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <input type="hidden" name="tracking_code" value="{{ old('tracking_code', $trackingCode ?? '') }}">

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3 py-1.5 fw-bold" 
                                        onclick="navigator.clipboard.writeText('{{ $trackingCode ?? '' }}'); this.innerText='تم النسخ!'; setTimeout(() => this.innerText='نسخ الكود', 2000)">
                                    <i class="bi bi-clipboard me-1"></i> نسخ الكود للشاكي
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- 1. نوع المصدر --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-person-badge text-primary"></i>
                            المصدر (نوع صاحب الشكوى) <span class="text-danger">*</span>
                        </label>
                        <select name="source_type" id="source_type" class="form-select form-select-lg border-2 shadow-none" required onchange="handleSourceTypeChange()">
                            <option value="donor" @selected(old('source_type') === 'donor')>متبرع (Donor)</option>
                            <option value="beneficiary" @selected(old('source_type') === 'beneficiary')>مستفيد (Beneficiary)</option>
                            <option value="employee" @selected(old('source_type') === 'employee')>موظف (Employee)</option>
                        </select>
                        <div class="form-text">اختر فئة الشخص الذي تقدم بالشكوى.</div>
                    </div>

                    {{-- 2. طريقة تحديد الشخص (اختيار من السجل أو كتابة يدوية) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-sliders text-primary"></i>
                            طريقة إدخال بيانات الشخص <span class="text-danger">*</span>
                        </label>
                        <div class="btn-group w-100 p-1 bg-light rounded-3 border" role="group">
                            <input type="radio" class="btn-check" name="input_mode" id="mode_select" value="select" checked onchange="handleModeChange()">
                            <label class="btn btn-sm btn-outline-primary border-0 rounded-2 fw-bold py-2" for="mode_select">
                                <i class="bi bi-list-check me-1"></i> اختيار من السجل
                            </label>

                            <input type="radio" class="btn-check" name="input_mode" id="mode_manual" value="manual" onchange="handleModeChange()">
                            <label class="btn btn-sm btn-outline-primary border-0 rounded-2 fw-bold py-2" for="mode_manual">
                                <i class="bi bi-pencil-square me-1"></i> كتابة الاسم يدوياً
                            </label>
                        </div>
                        <div class="form-text">يمكنك الاختيار من قاعدة البيانات أو كتابة اسم الشخص يدوياً إذا لم يكن مسجلاً.</div>
                    </div>

                    {{-- 3. حاوية اختيار الشخص من السجل (Select Mode) --}}
                    <div class="col-12" id="select_container">
                        <div class="p-3 p-md-4 rounded-3 border bg-light bg-opacity-50">
                            {{-- Donor Select --}}
                            <div id="donor_wrapper" class="source-list-wrapper">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-heart text-danger me-1"></i> حدد المتبرع من السجل:
                                </label>
                                <select id="donor_select" class="form-select form-select-lg" onchange="syncSourceId(this)">
                                    <option value="">-- اضغط لاختيار المتبرع من القائمة --</option>
                                    @foreach($donors as $donor)
                                        <option value="{{ $donor->id }}" data-name="{{ $donor->name }}" data-phone="{{ $donor->phone }}" @selected(old('source_id') == $donor->id)>
                                            {{ $donor->name }} — {{ $donor->phone ?: ($donor->code ?: '#'.$donor->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Beneficiary Select --}}
                            <div id="beneficiary_wrapper" class="source-list-wrapper d-none">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-people text-info me-1"></i> حدد المستفيد من السجل:
                                </label>
                                <select id="beneficiary_select" class="form-select form-select-lg" onchange="syncSourceId(this)">
                                    <option value="">-- اضغط لاختيار المستفيد من القائمة --</option>
                                    @foreach($beneficiaries as $b)
                                        <option value="{{ $b->id }}" data-name="{{ $b->full_name }}" data-phone="{{ $b->phone }}" @selected(old('source_id') == $b->id)>
                                            {{ $b->full_name }} — {{ $b->code ?: ($b->national_id ?: '#'.$b->id) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- Employee Select --}}
                            <div id="employee_wrapper" class="source-list-wrapper d-none">
                                <label class="form-label fw-bold text-dark">
                                    <i class="bi bi-briefcase text-success me-1"></i> حدد الموظف من السجل:
                                </label>
                                <select id="employee_select" class="form-select form-select-lg" onchange="syncSourceId(this)">
                                    <option value="">-- اضغط لاختيار الموظف من القائمة --</option>
                                    @foreach($users as $u)
                                        <option value="{{ $u->id }}" data-name="{{ $u->name }}" data-phone="{{ $u->phone }}" @selected(old('source_id') == $u->id)>
                                            {{ $u->name }} ({{ $u->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mt-2 text-muted small d-flex align-items-center gap-1">
                                <i class="bi bi-info-circle text-primary"></i>
                                <span>إذا لم تجد الشخص في القائمة، اضغط على زر <strong>(كتابة الاسم يدوياً)</strong> بالأعلى لإدخاله مباشرة.</span>
                            </div>
                        </div>
                    </div>

                    {{-- 4. حاوية الإدخال اليدوي (Manual Mode) --}}
                    <div class="col-12 d-none" id="manual_container">
                        <div class="p-3 p-md-4 rounded-3 border border-primary border-opacity-25 bg-primary bg-opacity-10">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">
                                        <i class="bi bi-person-fill text-primary me-1"></i>
                                        اسم الشخص صاحب الشكوى بالكامل <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="complainant_name" id="complainant_name" class="form-control form-control-lg bg-white" 
                                           placeholder="مثال: فاعل خير / أحمد محمود السيد..." value="{{ old('complainant_name') }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-dark">
                                        <i class="bi bi-telephone-fill text-success me-1"></i>
                                        رقم الهاتف للتواصل (اختياري)
                                    </label>
                                    <input type="text" name="complainant_phone" id="complainant_phone" class="form-control form-control-lg bg-white" 
                                           placeholder="مثال: 01012345678" value="{{ old('complainant_phone') }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- 5. الموظف المشكو ضده (اختياري) --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-person-x text-danger"></i>
                            المسؤول / المشكو في حقه (اختياري)
                        </label>
                        <select name="against_user_id" class="form-select border-2 shadow-none">
                            <option value="">— شكوى عامة أو غير محدد لموظف معين —</option>
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" @selected(old('against_user_id') == $u->id)>
                                    {{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text">اختر الموظف إذا كانت الشكوى موجهة ضده مباشرة.</div>
                    </div>

                    {{-- 6. عنوان الشكوى --}}
                    <div class="col-md-6">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-card-heading text-primary"></i>
                            عنوان / موضوع الشكوى <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="subject" class="form-control border-2 shadow-none" placeholder="اكتب ملخصاً واضحاً لموضوع الشكوى..." value="{{ old('subject') }}" required>
                        <div class="form-text">عنوان مختصر يوضح مضمون الشكوى.</div>
                    </div>

                    {{-- 7. تفاصيل ونص الشكوى --}}
                    <div class="col-12">
                        <label class="form-label fw-bold text-dark d-flex align-items-center gap-1">
                            <i class="bi bi-text-paragraph text-primary"></i>
                            تفاصيل ونص الشكوى <span class="text-danger">*</span>
                        </label>
                        <textarea name="message" class="form-control border-2 shadow-none" rows="5" placeholder="اكتب هنا تفاصيل الشكوى الكاملة بدقة..." required>{{ old('message') }}</textarea>
                    </div>
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex gap-2 justify-content-end mt-4 pt-3 border-top">
                    <a href="{{ route('complaints.index') }}" class="btn btn-outline-secondary px-4 py-2">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button type="submit" class="btn btn-primary px-4 py-2 fw-bold shadow-sm">
                        <i class="bi bi-check-lg me-1"></i> حفظ وتسجيل الشكوى
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function handleSourceTypeChange() {
            const sourceType = document.getElementById('source_type').value;
            const donorWrap = document.getElementById('donor_wrapper');
            const benWrap = document.getElementById('beneficiary_wrapper');
            const empWrap = document.getElementById('employee_wrapper');

            // Hide all select wrappers
            donorWrap.classList.add('d-none');
            benWrap.classList.add('d-none');
            empWrap.classList.add('d-none');

            // Show active wrapper
            if (sourceType === 'donor') {
                donorWrap.classList.remove('d-none');
                syncSourceId(document.getElementById('donor_select'));
            } else if (sourceType === 'beneficiary') {
                benWrap.classList.remove('d-none');
                syncSourceId(document.getElementById('beneficiary_select'));
            } else if (sourceType === 'employee') {
                empWrap.classList.remove('d-none');
                syncSourceId(document.getElementById('employee_select'));
            }
        }

        function handleModeChange() {
            const isManual = document.getElementById('mode_manual').checked;
            const selectContainer = document.getElementById('select_container');
            const manualContainer = document.getElementById('manual_container');
            const hiddenId = document.getElementById('hidden_source_id');
            const nameInput = document.getElementById('complainant_name');

            if (isManual) {
                selectContainer.classList.add('d-none');
                manualContainer.classList.remove('d-none');
                hiddenId.value = '0';
                nameInput.focus();
            } else {
                selectContainer.classList.remove('d-none');
                manualContainer.classList.add('d-none');
                nameInput.value = '';
                handleSourceTypeChange();
            }
        }

        function syncSourceId(selectEl) {
            const isManual = document.getElementById('mode_manual').checked;
            if (isManual) {
                document.getElementById('hidden_source_id').value = '0';
                return;
            }
            document.getElementById('hidden_source_id').value = selectEl.value || '';
        }

        // Initialize state on page load
        document.addEventListener('DOMContentLoaded', function() {
            handleSourceTypeChange();
            @if(old('complainant_name'))
                document.getElementById('mode_manual').checked = true;
                handleModeChange();
            @endif
        });
    </script>
@endsection
