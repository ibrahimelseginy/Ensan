@if(request()->user()?->hasPermission('beneficiaries.view'))
    @php
        $selectedAllocatedBeneficiaries = array_map('intval', old('allocated_beneficiary_ids', []));
        $selectedSponsors = array_map('intval', old('sponsor_ids', []));
    @endphp
    <div class="modal fade" id="guestHouseBeneficiaryModal" tabindex="-1" aria-labelledby="guestHouseBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" enctype="multipart/form-data" action="{{ route('guest-houses.storeBeneficiary', $guest_house) }}">
                @csrf
                <input type="hidden" name="form_context" value="guest_house_beneficiary">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="guestHouseBeneficiaryModalLabel">
                            <i class="bi bi-person-plus text-primary me-1"></i> إضافة مستفيد لدار الضيافة
                        </h5>
                        <div class="small text-muted mt-1">{{ $guest_house->name }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    {{-- Selected Bed Notice at Top --}}
                    <div id="modalBedNoticeTop" class="alert alert-success py-2 px-3 mb-3 d-flex justify-content-between align-items-center {{ old('guest_house_bed_id') ? '' : 'd-none' }}">
                        <div>
                            <i class="bi bi-check-circle-fill text-success me-1"></i>
                            <span>السرير المختار للحجز:</span>
                            <strong id="modalBedNoticeTopLabel" class="ms-1">{{ old('guest_house_bed_id') ? 'سرير مسجل مسبقاً' : '' }}</strong>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="clearModalBedSelection()">إلغاء حجز السرير</button>
                    </div>
                    {{-- 1. اختيار نزيل مسجل مسبقاً (لحالات التردد والعودة) --}}
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold text-primary fs-6 mb-0">
                                <i class="bi bi-clock-history me-1"></i> هل النزيل مسجل مسبقاً؟ (اختيار حالة مسجلة مسبقاً)
                            </label>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">
                                <i class="bi bi-search me-1"></i> بحث فوري
                            </span>
                        </div>
                        <select name="existing_beneficiary_id" id="existingGuestSelect" class="form-select" onchange="handleGuestHouseSelectExisting(this)">
                            <option value="">— نزيل جديد (تسجيل حالة جديدة لأول مرة) —</option>
                            @foreach($beneficiaryOptions as $opt)
                                <option value="{{ $opt->id }}" 
                                    data-name="{{ $opt->full_name }}" 
                                    data-code="{{ $opt->code }}" 
                                    data-nid="{{ $opt->national_id }}" 
                                    data-phone="{{ $opt->phone }}" 
                                    data-address="{{ $opt->address }}">
                                    {{ $opt->full_name }} (كود: {{ $opt->code ?: '—' }} | قومي: {{ $opt->national_id ?: '—' }}@if($opt->phone) | هاتف: {{ $opt->phone }}@endif)
                                </option>
                            @endforeach
                        </select>
                        <div class="form-text small text-muted mt-2">
                            <i class="bi bi-info-circle me-1"></i> يمكنك البحث بالاسم أو الرقم القومي أو الكود أو رقم الهاتف. عند اختيار مريض يتم استرجاع بياناته تلقائياً.
                        </div>
                    </div>

                    {{-- 2. البيانات الشخصية للمريض --}}
                    <div class="form-section mb-4">
                        <div class="form-section-title"><i class="bi bi-person-lines-fill"></i> <span>بيانات المريض (الحالة)</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">كود المستفيد</label>
                                <input name="code" id="gh_patient_code" class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code') }}" placeholder="يُنشأ تلقائيًا عند تركه فارغًا">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-required">اسم المريض رباعي</label>
                                <input name="full_name" id="gh_patient_name" class="form-control @error('full_name') is-invalid @enderror"
                                    value="{{ old('full_name') }}" placeholder="اسم المريض كاملاً">
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label form-label-required">النوع</label>
                                <select name="gender" id="gh_patient_gender" class="form-select @error('gender') is-invalid @enderror">
                                    <option value="">— اختر النوع —</option>
                                    <option value="male" @selected(old('gender') === 'male')>ذكر</option>
                                    <option value="female" @selected(old('gender') === 'female')>أنثى</option>
                                </select>
                                @error('gender')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">الرقم القومي للمريض</label>
                                <input name="national_id" id="gh_patient_nid" class="form-control @error('national_id') is-invalid @enderror"
                                    value="{{ old('national_id') }}" maxlength="14" placeholder="14 رقم">
                                @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">رقم الهاتف / الموبايل</label>
                                <input name="phone" id="gh_patient_phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" placeholder="01xxxxxxxxx">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-camera-fill text-primary me-1"></i> تصوير بطاقة المريض (وجه)</label>
                                <input type="file" name="patient_id_front" accept="image/*" capture="environment" class="form-control">
                                <div class="form-text small">التقاط بالكاميرا أو رفع صورة وجه بطاقة الرقم القومي للمريض.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-camera-fill text-primary me-1"></i> تصوير بطاقة المريض (ظهر)</label>
                                <input type="file" name="patient_id_back" accept="image/*" capture="environment" class="form-control">
                                <div class="form-text small">التقاط بالكاميرا أو رفع صورة ظهر بطاقة الرقم القومي للمريض.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">محل إقامة المريض والعنوان بالتفصيل</label>
                                <input name="address" id="gh_patient_address" class="form-control @error('address') is-invalid @enderror"
                                    value="{{ old('address') }}" placeholder="المحافظة - المركز / المدينة - القرية">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    {{-- 3. بيانات المرافق (استمارة الدخول الرسمية) --}}
                    <div class="form-section mb-4">
                        <div class="form-section-title"><i class="bi bi-person-badge"></i> <span>بيانات المرافق</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم المرافق رباعي</label>
                                <input name="companion_name" value="{{ old('companion_name') }}" class="form-control" placeholder="اسم المرافق كاملاً">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">صلة القرابة</label>
                                <input name="companion_relation" value="{{ old('companion_relation') }}" class="form-control" placeholder="مثال: زوج / زوجة / ابن / والد / أخ...">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرقم القومي للمرافق</label>
                                <input name="companion_national_id" value="{{ old('companion_national_id') }}" maxlength="14" class="form-control" placeholder="14 رقم">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">موبايل المرافق</label>
                                <input name="companion_phone" value="{{ old('companion_phone') }}" class="form-control" placeholder="01xxxxxxxxx">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-camera-fill text-primary me-1"></i> تصوير بطاقة المرافق (وجه)</label>
                                <input type="file" name="companion_id_front" accept="image/*" capture="environment" class="form-control">
                                <div class="form-text small">التقاط بالكاميرا أو رفع صورة وجه بطاقة الرقم القومي للمرافق.</div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-camera-fill text-primary me-1"></i> تصوير بطاقة المرافق (ظهر)</label>
                                <input type="file" name="companion_id_back" accept="image/*" capture="environment" class="form-control">
                                <div class="form-text small">التقاط بالكاميرا أو رفع صورة ظهر بطاقة الرقم القومي للمرافق.</div>
                            </div>
                            <div class="col-12">
                                <label class="form-label">محل إقامة المرافق</label>
                                <input name="companion_address" value="{{ old('companion_address') }}" class="form-control" placeholder="المحافظة - المركز - العنوان">
                            </div>
                        </div>
                    </div>

                    {{-- 4. ملاحظات مدير الدار (مشاكل وسلبيات الحالة) --}}
                    <div class="form-section mb-4">
                        <div class="form-section-title text-warning"><i class="bi bi-exclamation-triangle-fill"></i> <span>ملاحظات مدير الدار (مشاكل أو سلبيات)</span></div>
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label fw-bold">أي مشاكل أو سلبيات أو تنبيهات تدون بمعرفة مدير الدار في ملف الحالة:</label>
                                <textarea name="manager_notes" class="form-control" rows="2" placeholder="تدوين المشاكل أو السلبيات الخاصة بالحالة أثناء ترددها أو إقامتها بالدار...">{{ old('manager_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- 5. الملف الطبي والمرفقات --}}
                    <div class="form-section mt-4">
                        <div class="form-section-title"><i class="bi bi-heart-pulse"></i> <span>الملف الطبي والمرفقات</span></div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label form-label-required fw-bold">نوع الحالة / العلاج</label>
                                <select id="beneficiary-treatment-type" name="treatment_type" class="form-select" onchange="toggleTreatmentDocs(this.value)">
                                    <option value="">— اختر نوع العلاج —</option>
                                    <option value="chemotherapy" @selected(old('treatment_type')==='chemotherapy')>مريض كيماوي</option>
                                    <option value="radiation" @selected(old('treatment_type')==='radiation')>مريض إشعاع</option>
                                    <option value="other" @selected(old('treatment_type')==='other')>أخرى / جراحة</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">المركز الطبي / المستشفى</label>
                                <input name="medical_center" value="{{ old('medical_center') }}" class="form-control" placeholder="مثال: مركز أورام كفر الشيخ">
                            </div>
                            <div class="col-md-3" id="sessions-count-container">
                                <label class="form-label">عدد الجلسات المقررة</label>
                                <input type="number" min="1" name="sessions_count" value="{{ old('sessions_count') }}" class="form-control" placeholder="عدد الجلسات">
                            </div>

                            {{-- Treatment Specific Documents --}}
                            <div class="col-md-6" id="chemo-card-container">
                                <div class="p-2 border rounded-3 bg-light">
                                    <label class="form-label fw-bold text-success mb-1">
                                        <i class="bi bi-file-earmark-medical me-1"></i> كارت المتابعة لمريض الكيماوي
                                    </label>
                                    <input type="file" name="followup_card" accept="image/*,.pdf" class="form-control form-control-sm">
                                    <div class="form-text small">مرفق كارت المتابعة الخاص بجرعات الكيماوي.</div>
                                </div>
                            </div>
                            <div class="col-md-6" id="radiation-card-container">
                                <div class="p-2 border rounded-3 bg-light">
                                    <label class="form-label fw-bold text-warning mb-1">
                                        <i class="bi bi-file-earmark-text me-1"></i> جواب تحويل بعدد الجلسات (الإشعاع)
                                    </label>
                                    <input type="file" name="referral_letter" accept="image/*,.pdf" class="form-control form-control-sm">
                                    <div class="form-text small">خطاب أو جواب التحويل الطبي بعدد جلسات الإشعاع.</div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">تاريخ الوصول</label>
                                <input type="date" name="arrival_date" value="{{ old('arrival_date', now()->toDateString()) }}" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عدد أيام الإقامة المتوقعة</label>
                                <input type="number" min="1" max="365" name="expected_days" value="{{ old('expected_days', 7) }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات طبية خاصة بالحالة</label>
                                <textarea name="medical_notes" class="form-control" rows="2" placeholder="أي تفاصيل خاصة بالحالة الصحية أو المرافقين...">{{ old('medical_notes') }}</textarea>
                            </div>
                        </div>
                    </div>

                    {{-- Visual Bed Selection Grid --}}
                    <div class="form-section mt-4">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="form-section-title mb-0"><i class="bi bi-hospital-bed"></i> <span>حجز السرير (مخطط المربعات)</span></div>
                            <div class="d-flex gap-2 small">
                                <span class="badge bg-success"><i class="bi bi-check-circle me-1"></i> أخضر: فاضي متاح</span>
                                <span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i> أحمر: محجوز</span>
                            </div>
                        </div>

                        {{-- Selected Bed Notice --}}
                        <div id="modalBedNotice" class="alert alert-success py-2 px-3 mb-3 d-flex justify-content-between align-items-center {{ old('guest_house_bed_id') ? '' : 'd-none' }}">
                            <div>
                                <i class="bi bi-check-circle-fill me-1"></i>
                                <span>السرير المختار للحجز:</span>
                                <strong id="modalBedNoticeLabel" class="ms-1">{{ old('guest_house_bed_id') ? 'سرير مسجل مسبقاً' : '' }}</strong>
                            </div>
                            <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="clearModalBedSelection()">إلغاء حجز السرير</button>
                        </div>

                        <input type="hidden" name="guest_house_bed_id" id="modalBedInput" value="{{ old('guest_house_bed_id') }}">

                        @forelse($guestHouseWings as $wing)
                            <div class="p-3 border rounded-3 mb-3 bg-light-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <strong class="text-primary"><i class="bi bi-door-closed me-1"></i> {{ $wing->name }}</strong>
                                    <span class="badge bg-secondary-subtle text-secondary small">{{ $wing->beds->count() }} سرير</span>
                                </div>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($wing->beds as $bed)
                                        @php
                                            $isOccupied = (bool) $bed->activeStay;
                                            $isMaintenance = $bed->status === 'maintenance';
                                            $isSelected = (string)old('guest_house_bed_id') === (string)$bed->id;
                                        @endphp
                                        @if($isOccupied)
                                            <div class="p-2 rounded-3 text-center text-white bg-danger shadow-sm d-flex flex-column align-items-center justify-content-center"
                                                 style="width: 86px; height: 74px; opacity: 0.85; cursor: not-allowed;"
                                                 title="محجوز: {{ $bed->activeStay?->beneficiary?->full_name }}">
                                                <i class="bi bi-person-fill fs-5 mb-0"></i>
                                                <span class="fw-bold small">سرير {{ $bed->number }}</span>
                                                <span style="font-size: 0.65rem;">محجوز</span>
                                            </div>
                                        @elseif($isMaintenance)
                                            <div class="p-2 rounded-3 text-center text-white bg-secondary shadow-sm d-flex flex-column align-items-center justify-content-center"
                                                 style="width: 86px; height: 74px; opacity: 0.7; cursor: not-allowed;"
                                                 title="صيانة">
                                                <i class="bi bi-tools fs-5 mb-0"></i>
                                                <span class="fw-bold small">سرير {{ $bed->number }}</span>
                                                <span style="font-size: 0.65rem;">صيانة</span>
                                            </div>
                                        @else
                                            <button type="button"
                                                    class="btn p-2 rounded-3 text-center text-white bg-success shadow-sm d-flex flex-column align-items-center justify-content-center modal-bed-square {{ $isSelected ? 'border border-3 border-warning ring' : '' }}"
                                                    data-bed-id="{{ $bed->id }}"
                                                    data-bed-label="{{ $wing->name }} — سرير {{ $bed->number }}"
                                                    style="width: 86px; height: 74px; transition: transform 0.15s, box-shadow 0.15s;"
                                                    onclick="selectModalBed(this)">
                                                <i class="bi bi-hospital-bed fs-5 mb-0"></i>
                                                <span class="fw-bold small">سرير {{ $bed->number }}</span>
                                                <span style="font-size: 0.65rem;">فاضي</span>
                                            </button>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                        @empty
                            <div class="text-center text-muted py-3 border rounded-3 bg-light">
                                <i class="bi bi-info-circle me-1"></i> لم يتم إضافة أجنحة أو أسِرّة لهذه الدار بعد. يمكنك حفظ الملف دون حجز سرير.
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> حفظ المستفيد</button>
                </div>
            </form>
        </div>
    </div>
@endif

@if(request()->user()?->hasPermission('donations.view'))
    <div class="modal fade" id="guestHouseDonationModal" tabindex="-1" aria-labelledby="guestHouseDonationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('guest-houses.storeDonation', $guest_house) }}">
                @csrf
                <input type="hidden" name="form_context" value="guest_house_donation">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="guestHouseDonationModalLabel"><i class="bi bi-cash-coin text-success me-1"></i> إضافة تبرع لدار الضيافة</h5>
                        <div class="small text-muted mt-1">{{ $guest_house->name }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    @if($guestHouseDonationTreasuries->isEmpty())
                        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i> لا توجد خزينة مفعّلة للتبرع النقدي. يمكنك تسجيل تبرع عيني أو تفعيل خزينة أولًا.</div>
                    @endif
                    @if($guestHouseWarehouses->isEmpty())
                        <div class="alert alert-warning"><i class="bi bi-box-seam me-1"></i> لا يوجد مخزن مفعّل لاستلام التبرعات العينية.</div>
                    @endif
                    <div class="form-section mb-4">
                        <div class="form-section-title"><i class="bi bi-person-heart"></i> <span>بيانات المتبرع</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">طريقة إدخال المتبرع</label>
                                <select id="guest-house-donor-mode" class="form-select">
                                    <option value="existing" @selected(!old('new_donor_name'))>اختيار متبرع مسجل</option>
                                    <option value="new" @selected((bool) old('new_donor_name'))>إضافة متبرع جديد</option>
                                </select>
                            </div>
                            <div id="guest-house-existing-donor-field" class="col-md-6">
                                <label class="form-label form-label-required">المتبرع</label>
                                <select id="guest-house-donor-id" name="donor_id" class="form-select @error('donor_id') is-invalid @enderror">
                                    <option value="">— اختر المتبرع —</option>
                                    @foreach($guestHouseDonors as $donor)
                                        <option value="{{ $donor->id }}" @selected(old('donor_id') == $donor->id)>{{ $donor->name }} — {{ $donor->code }}{{ $donor->phone ? ' — '.$donor->phone : '' }}</option>
                                    @endforeach
                                </select>
                                @error('donor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div id="guest-house-new-donor-fields" class="col-12 d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">كود المتبرع <span class="badge bg-primary-subtle text-primary">ثابت</span></label>
                                        <input name="new_donor_code" dir="ltr" class="form-control font-monospace @error('new_donor_code') is-invalid @enderror"
                                            value="{{ old('new_donor_code') }}" placeholder="اختياري — يُنشأ تلقائيًا عند تركه فارغًا">
                                        @error('new_donor_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-required">اسم المتبرع الجديد</label>
                                        <input id="guest-house-new-donor-name" name="new_donor_name" class="form-control @error('new_donor_name') is-invalid @enderror" value="{{ old('new_donor_name') }}">
                                        @error('new_donor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-required">رقم الهاتف</label>
                                        <input id="guest-house-new-donor-phone" name="new_donor_phone" class="form-control @error('new_donor_phone') is-invalid @enderror" value="{{ old('new_donor_phone') }}" placeholder="01xxxxxxxxx">
                                        @error('new_donor_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">التصنيف</label>
                                        <select name="new_donor_classification" class="form-select"><option value="one_time">مرة واحدة</option><option value="recurring">متكرر</option></select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">دورة التكرار</label>
                                        <select name="new_donor_cycle" class="form-select"><option value="">— اختر —</option><option value="monthly">شهري</option><option value="yearly">سنوي</option></select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-wallet2"></i> <span>بيانات التبرع</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">نوع التبرع</label>
                                <select id="guest-house-donation-type" name="type" class="form-select" required>
                                    <option value="cash" @selected(old('type', 'cash') === 'cash')>نقدي</option>
                                    <option value="in_kind" @selected(old('type') === 'in_kind')>عيني</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاستلام</label>
                                <input type="date" name="received_at" class="form-control" value="{{ old('received_at', now()->toDateString()) }}">
                            </div>
                            <div class="col-md-6 guest-house-donation-cash-field">
                                <label class="form-label form-label-required">المبلغ</label>
                                <div class="input-group">
                                    <input id="guest-house-donation-amount" name="amount" type="number" step="0.01" min="0.01" class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}">
                                    <select name="currency" class="form-select" style="max-width:110px">@foreach(['EGP','USD','EUR','SAR'] as $currency)<option value="{{ $currency }}" @selected(old('currency','EGP') === $currency)>{{ $currency }}</option>@endforeach</select>
                                </div>
                                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 guest-house-donation-cash-field">
                                <label class="form-label form-label-required">طريقة التحصيل</label>
                                <select id="guest-house-donation-channel" name="cash_channel" class="form-select"><option value="cash">نقدي</option><option value="instapay">إنستاباي</option><option value="vodafone_cash">فودافون كاش</option><option value="delegate">مندوب</option></select>
                            </div>
                            <div class="col-md-6 guest-house-donation-cash-field">
                                <label class="form-label form-label-required">رقم الإيصال</label>
                                <input id="guest-house-donation-receipt" name="receipt_number" class="form-control @error('receipt_number') is-invalid @enderror" value="{{ old('receipt_number') }}">
                                @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 guest-house-donation-cash-field">
                                <label class="form-label form-label-required">الخزينة المستلمة</label>
                                <select id="guest-house-donation-treasury" name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror">
                                    <option value="">— اختر الخزينة —</option>
                                    @foreach($guestHouseDonationTreasuries as $treasury)<option value="{{ $treasury->id }}" @selected(old('treasury_id') == $treasury->id)>{{ $treasury->name }} — {{ number_format((float) $treasury->current_balance, 2) }} {{ $treasury->currency }}</option>@endforeach
                                </select>
                                @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 guest-house-donation-in-kind-field d-none">
                                <label class="form-label form-label-required">القيمة التقديرية</label>
                                <input id="guest-house-donation-estimated" name="estimated_value" type="number" step="0.01" min="0.01" class="form-control @error('estimated_value') is-invalid @enderror" value="{{ old('estimated_value') }}">
                                @error('estimated_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 guest-house-donation-in-kind-field d-none">
                                <div class="form-check form-switch mb-2"><input class="form-check-input" type="checkbox" role="switch" id="guest-house-add-to-inventory" name="add_to_inventory" value="1" @checked(old('add_to_inventory'))><label class="form-check-label fw-bold" for="guest-house-add-to-inventory">إضافة إلى مخزون العهدة العينية</label><div class="small text-muted">ألغِها للأصول مثل السرير التي تُسجل محاسبيًا ولا تُصرف.</div></div>
                            </div>
                            <div class="col-md-6 guest-house-donation-in-kind-field guest-house-stock-detail d-none">
                                <label class="form-label">المخزن المستلم</label>
                                <select id="guest-house-donation-warehouse" name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror"><option value="">— اختر المخزن —</option>@foreach($guestHouseWarehouses as $warehouse)<option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>@endforeach</select>
                                @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4 guest-house-donation-in-kind-field guest-house-stock-detail d-none">
                                <label class="form-label">الصنف</label><select id="guest-house-donation-item" name="item_id" class="form-select"><option value="">— اختر الصنف —</option>@foreach($guestHouseItems as $item)<option value="{{ $item->id }}" @selected(old('item_id')==$item->id)>{{ $item->name }} ({{ $item->unit }})</option>@endforeach</select>
                            </div>
                            <div class="col-md-2 guest-house-donation-in-kind-field guest-house-stock-detail d-none">
                                <label class="form-label">الكمية</label><input id="guest-house-donation-quantity" type="number" step="0.001" min="0.001" name="quantity" value="{{ old('quantity') }}" class="form-control">
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات التخصيص</label>
                                <textarea name="allocation_note" class="form-control" rows="2">{{ old('allocation_note') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> حفظ التبرع</button></div>
            </form>
        </div>
    </div>
@endif

@if(request()->user()?->hasPermission('manage_finance') && request()->user()?->hasPermission('expenses.view'))
    <div class="modal fade" id="guestHouseExpenseModal" tabindex="-1" aria-labelledby="guestHouseExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('guest-houses.storeExpense', $guest_house) }}">
                @csrf
                <input type="hidden" name="form_context" value="guest_house_expense">
                <input type="hidden" name="currency" value="EGP">
                <div class="modal-header">
                    <div><h5 class="modal-title" id="guestHouseExpenseModalLabel"><i class="bi bi-receipt text-danger me-1"></i> إضافة مصروف لدار الضيافة</h5><div class="small text-muted mt-1">{{ $guest_house->name }}</div></div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    @if($guestHouseExpenseTreasuries->isEmpty())
                        <div class="alert alert-warning"><i class="bi bi-exclamation-triangle me-1"></i> لا توجد خزينة متاحة للصرف. يجب إنشاء أو تفعيل خزينة أولًا.</div>
                    @endif
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> <span>بيانات المصروف</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">نوع المصروف</label>
                                <select name="type" class="form-select" required><option value="operational">تشغيلي</option><option value="aid">مساعدات</option><option value="logistics">لوجستي</option></select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">البند الفرعي</label>
                                <input name="category" class="form-control @error('category') is-invalid @enderror" value="{{ old('category') }}" placeholder="مثال: إقامة، طعام، صيانة">
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-required">المبلغ</label>
                                <div class="input-group"><input name="amount" type="number" step="0.01" min="0.01" required class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}"><span class="input-group-text">EGP</span></div>
                                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-required">الخزينة</label>
                                <select name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror" required>
                                    <option value="">— اختر مصدر الصرف —</option>
                                    @foreach($guestHouseExpenseTreasuries as $treasury)<option value="{{ $treasury->id }}" @selected(old('treasury_id') == $treasury->id)>{{ $treasury->name }} — {{ number_format((float) $treasury->current_balance, 2) }} {{ $treasury->currency }}</option>@endforeach
                                </select>
                                @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المستفيد (اختياري)</label>
                                <select name="beneficiary_id" class="form-select"><option value="">— بدون مستفيد محدد —</option>@foreach($guestHouseBeneficiaries as $beneficiaryOption)<option value="{{ $beneficiaryOption->id }}" @selected(old('beneficiary_id') == $beneficiaryOption->id)>{{ $beneficiaryOption->full_name }}</option>@endforeach</select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الصرف</label>
                                <input type="date" name="paid_at" class="form-control" value="{{ old('paid_at', now()->toDateString()) }}">
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف المصروف</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror" rows="3">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button><button type="submit" class="btn btn-danger" @disabled($guestHouseExpenseTreasuries->isEmpty())><i class="bi bi-check-lg me-1"></i> حفظ المصروف</button></div>
            </form>
        </div>
    </div>
@endif
