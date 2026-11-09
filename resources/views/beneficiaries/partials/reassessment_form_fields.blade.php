@php
    $defaultPatient = $beneficiary->patient_name ?: ($beneficiary->patientMember()?->full_name ?: $beneficiary->full_name);
    $defaultGuardian = $beneficiary->guardian_name ?: $beneficiary->full_name;
    $defaultFamCount = $beneficiary->family_members_count ?: ($beneficiary->children_count ? ($beneficiary->children_count + 1) : null);
@endphp

<div class="row g-3">
    {{-- Section 1: الأسرة والظروف الاجتماعية --}}
    <div class="col-12">
        <div class="p-2.5 rounded-3 bg-primary-subtle text-primary fw-bold border-start border-4 border-primary">
            <i class="bi bi-people-fill me-1"></i> أولاً: الأسرة والظروف الاجتماعية
        </div>
    </div>

    @if($beneficiary->familyMembers && $beneficiary->familyMembers->isNotEmpty())
        <div class="col-md-6">
            <label class="form-label small fw-bold">الفرد / المريض محل التحديث (اختياري)</label>
            <select name="family_member_id" class="form-select">
                <option value="">— ملف الأسرة العام / المريض الأساسي —</option>
                @foreach($beneficiary->familyMembers as $fm)
                    <option value="{{ $fm->id }}" @selected($fm->is_patient)>{{ $fm->full_name }} ({{ $fm->relationship_label }})</option>
                @endforeach
            </select>
        </div>
    @endif

    <div class="col-md-6">
        <label class="form-label small fw-bold">اسم المريض</label>
        <input type="text" name="patient_name" class="form-control" value="{{ old('patient_name', $defaultPatient) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">اسم ولي الأمر / العائل</label>
        <input type="text" name="guardian_name" class="form-control" value="{{ old('guardian_name', $defaultGuardian) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">عدد أفراد الأسرة الحاليين</label>
        <input type="number" name="current_family_count" class="form-control" min="1" value="{{ old('current_family_count', $defaultFamCount) }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">من يقوم برعاية المريض يومياً؟</label>
        <input type="text" name="caregiver_name" class="form-control" placeholder="الأم / الزوجة / الأب / مرافق..." value="{{ old('caregiver_name') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">هل حدث زواج لأحد الأبناء أو خروج من التعليم؟</label>
        <input type="text" name="children_marriage_or_dropout" class="form-control" placeholder="لا يوجد / نعم (اذكر التفاصيل)..." value="{{ old('children_marriage_or_dropout') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">هل يوجد فرد آخر في الأسرة يعاني من مرض مزمن أو إعاقة؟</label>
        <input type="text" name="other_chronic_illness" class="form-control" placeholder="لا يوجد / نعم (اذكر التفاصيل)..." value="{{ old('other_chronic_illness') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">هل أثر المرض على عمل أحد أفراد الأسرة؟</label>
        <input type="text" name="impact_on_family_work" class="form-control" placeholder="مثال: توقف العائل عن العمل لمرافقة المريض..." value="{{ old('impact_on_family_work') }}">
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold text-danger">مستجدات اجتماعية طارئة (وفاة / شفاء / انفصال / أخرى)</label>
        <input type="text" name="family_status_changes" class="form-control" placeholder="تُسجل هنا أي مستجدات دون مسح البيانات الأساسية للأسرة..." value="{{ old('family_status_changes') }}">
    </div>

    {{-- Section 2: التحديث الطبي --}}
    <div class="col-12 mt-4">
        <div class="p-2.5 rounded-3 bg-danger-subtle text-danger fw-bold border-start border-4 border-danger">
            <i class="bi bi-heart-pulse-fill me-1"></i> ثانياً: التحديث الطبي (خاص ببعثاء الأمل / الحالات المرضية)
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">التشخيص الحالي للمريض</label>
        <input type="text" name="current_diagnosis" class="form-control" value="{{ old('current_diagnosis', $beneficiary->tumor_location) }}" placeholder="نوع الورم أو التشخيص الطبي الحالي">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">المرحلة الحالية</label>
        <select name="diagnosis_stage" class="form-select">
            <option value="">— اختر المرحلة —</option>
            <option value="يتلقى العلاج" @selected(old('diagnosis_stage') === 'يتلقى العلاج')>يتلقى العلاج</option>
            <option value="مرحلة متابعة" @selected(old('diagnosis_stage') === 'مرحلة متابعة')>مرحلة متابعة</option>
        </select>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">نوع العلاج الحالي</label>
        <select name="current_treatment_type" class="form-select">
            <option value="">— اختر نوع العلاج —</option>
            <option value="كيماوي" @selected(old('current_treatment_type') === 'كيماوي')>كيماوي</option>
            <option value="إشعاعي" @selected(old('current_treatment_type') === 'إشعاعي')>إشعاعي</option>
            <option value="مناعي" @selected(old('current_treatment_type') === 'مناعي')>مناعي</option>
            <option value="هرموني" @selected(old('current_treatment_type') === 'هرموني')>هرموني</option>
            <option value="متابعة فقط" @selected(old('current_treatment_type') === 'متابعة فقط')>متابعة فقط</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">عدد الجلسات المتبقية (إن وجد)</label>
        <input type="text" name="remaining_sessions" class="form-control" value="{{ old('remaining_sessions') }}" placeholder="مثال: 4 جلسات">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">هل توجد عملية جراحية مخطط لها؟</label>
        <input type="text" name="planned_surgery" class="form-control" value="{{ old('planned_surgery') }}" placeholder="لا / نعم (اذكر الموعد)...">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">تطور الحالة خلال الفترة الماضية</label>
        <select name="condition_progress" class="form-select">
            <option value="">— اختر التطور —</option>
            <option value="تحسن" @selected(old('condition_progress') === 'تحسن')>تحسن</option>
            <option value="استقرار" @selected(old('condition_progress') === 'استقرار')>استقرار</option>
            <option value="تدهور" @selected(old('condition_progress') === 'تدهور')>تدهور</option>
            <option value="توفي" @selected(old('condition_progress') === 'توفي')>توفي (مع بقاء سجل الأسرة الأساسي)</option>
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">نسبة الاستجابة للعلاج</label>
        <input type="text" name="response_rate" class="form-control" value="{{ old('response_rate') }}" placeholder="مثال: جيدة جداً / 70%...">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">المستشفى أو المركز الطبي الحالي</label>
        <input type="text" name="current_hospital" class="form-control" value="{{ old('current_hospital', $beneficiary->hospital_name) }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">تكلفة العلاج أو الفحوصات الشهرية تقريباً (ج.م)</label>
        <input type="number" step="0.01" min="0" name="monthly_treatment_cost" class="form-control font-monospace" value="{{ old('monthly_treatment_cost') }}">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">أعراض جانبية مؤثرة على قدرة المريض</label>
        <input type="text" name="side_effects_impact" class="form-control" value="{{ old('side_effects_impact') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">أدوية غير متوفرة في التأمين أو نفقة الدولة</label>
        <input type="text" name="unavailable_medications" class="form-control" value="{{ old('unavailable_medications') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">الاحتياج الطبي بالتحديد</label>
        <input type="text" name="exact_medical_need" class="form-control" value="{{ old('exact_medical_need') }}" placeholder="أشعة مسح ذري / تحاليل / علاج شهري...">
    </div>

    {{-- Section 3: تقييم الاحتياج الحالي --}}
    <div class="col-12 mt-4">
        <div class="p-2.5 rounded-3 bg-warning-subtle text-dark fw-bold border-start border-4 border-warning">
            <i class="bi bi-calculator-fill me-1"></i> ثالثاً: تقييم الاحتياج الحالي والوضع الاقتصادي
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">هل عائل الأسرة ما زال يعمل؟</label>
        <input type="text" name="breadwinner_still_working" class="form-control" value="{{ old('breadwinner_still_working') }}" placeholder="نعم / لا / عمل غير منتظم...">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">إجمالي دخل الأسرة الحالي (ج.م)</label>
        <input type="number" step="0.01" min="0" name="current_family_income" class="form-control font-monospace" value="{{ old('current_family_income') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">هل ظهر مصدر دخل جديد للأسرة؟</label>
        <input type="text" name="new_income_source" class="form-control" value="{{ old('new_income_source') }}" placeholder="لا / نعم...">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">معاش أو دعم حكومي (تكافل وكرامة مثلاً)</label>
        <input type="text" name="government_support_or_pension" class="form-control" value="{{ old('government_support_or_pension') }}">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">هل تراكمت ديون جديدة بسبب العلاج؟</label>
        <input type="text" name="new_medical_debts" class="form-control" value="{{ old('new_medical_debts') }}" placeholder="نعم / لا">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">قيمة الديون الحالية إن وجدت (ج.م)</label>
        <input type="number" step="0.01" min="0" name="current_debts_amount" class="form-control font-monospace" value="{{ old('current_debts_amount') }}">
    </div>
    <div class="col-12">
        <label class="form-label small fw-bold">هل توجد التزامات متأخرة (إيجار – كهرباء – أقساط)؟</label>
        <input type="text" name="overdue_rent_or_installments" class="form-control" value="{{ old('overdue_rent_or_installments') }}">
    </div>

    {{-- Section 4: التحديث المادي --}}
    <div class="col-12 mt-4">
        <div class="p-2.5 rounded-3 bg-info-subtle text-info fw-bold border-start border-4 border-info">
            <i class="bi bi-cash-coin me-1"></i> رابعاً: التحديث المادي وأولويات الدعم
        </div>
    </div>

    <div class="col-12">
        <label class="form-label small fw-bold d-block">ما نوع الدعم الأكثر احتياجاً حالياً؟ (يمكن اختيار أكثر من بند)</label>
        <div class="d-flex flex-wrap gap-3">
            @foreach(['علاج', 'أدوية', 'فحوصات', 'مواصلات علاج', 'إعاشة', 'إيجار'] as $needItem)
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="most_needed_support[]" value="{{ $needItem }}" id="need_{{ $loop->index }}">
                    <label class="form-check-label small fw-semibold" for="need_{{ $loop->index }}">{{ $needItem }}</label>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">الأثر المتوقع في حال توقف الدعم</label>
        <input type="text" name="expected_impact_if_stopped" class="form-control" value="{{ old('expected_impact_if_stopped') }}" placeholder="توقف جلسات العلاج / تدهور معيشي...">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">مساعدات من جمعية أو جهة أخرى</label>
        <input type="text" name="external_aid" class="form-control" value="{{ old('external_aid') }}" placeholder="لا يوجد / نعم (اذكر الجهة)...">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">قيمة المساعدة الشهرية المطلوبة (ج.م)</label>
        <input type="number" step="0.01" min="0" name="requested_monthly_aid" class="form-control font-monospace" value="{{ old('requested_monthly_aid', $beneficiary->monthly_sponsorship_amount) }}">
    </div>

    {{-- Section 5 & 6: ملاحظات وتوصية الباحث --}}
    <div class="col-12 mt-4">
        <div class="p-2.5 rounded-3 bg-success-subtle text-success fw-bold border-start border-4 border-success">
            <i class="bi bi-clipboard2-check-fill me-1"></i> خامساً وسادساً: ملاحظات الباحث وتوصية الاعتماد
        </div>
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">وصف مختصر لتطور الحالة خلال الفترة الماضية</label>
        <textarea name="yearly_progress_summary" rows="2" class="form-control">{{ old('yearly_progress_summary') }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">مدى التزام المريض بالعلاج والمتابعة</label>
        <textarea name="treatment_compliance" rows="2" class="form-control">{{ old('treatment_compliance') }}</textarea>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">مدى احتياج الأسرة لاستمرار الدعم</label>
        <textarea name="need_for_continued_support" rows="2" class="form-control">{{ old('need_for_continued_support') }}</textarea>
    </div>

    <div class="col-md-6">
        <label class="form-label small fw-bold text-success">توصية الباحث <span class="text-danger">*</span></label>
        <select name="researcher_recommendation" class="form-select fw-bold" required>
            <option value="استمرار الدعم كما هو" @selected(old('researcher_recommendation') === 'استمرار الدعم كما هو')>✅ استمرار الدعم كما هو</option>
            <option value="زيادة الدعم" @selected(old('researcher_recommendation') === 'زيادة الدعم')>⬆️ زيادة الدعم</option>
            <option value="تخفيض الدعم" @selected(old('researcher_recommendation') === 'تخفيض الدعم')>⬇️ تخفيض الدعم</option>
            <option value="إيقاف الدعم" @selected(old('researcher_recommendation') === 'إيقاف الدعم')>⛔ إيقاف الدعم</option>
        </select>
    </div>
    <div class="col-md-6">
        <label class="form-label small fw-bold">مبررات التوصية</label>
        <input type="text" name="recommendation_reasons" class="form-control" value="{{ old('recommendation_reasons') }}" placeholder="اذكر أسباب التوصية...">
    </div>

    <div class="col-md-4">
        <label class="form-label small fw-bold">اسم الباحث</label>
        <input type="text" name="researcher_name" class="form-control" value="{{ old('researcher_name', auth()->user()->name ?? '') }}" required>
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">اعتماد الإدارة</label>
        <input type="text" name="approved_by" class="form-control" value="{{ old('approved_by') }}" placeholder="اسم المدير المعتمد">
    </div>
    <div class="col-md-4">
        <label class="form-label small fw-bold">تاريخ التحديث (يُجدد كل 6 أشهر)</label>
        <input type="date" name="reassessed_at" class="form-control font-monospace" value="{{ old('reassessed_at', now()->toDateString()) }}">
    </div>
</div>

