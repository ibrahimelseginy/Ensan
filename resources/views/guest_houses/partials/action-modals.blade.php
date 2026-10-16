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
                    <div class="form-section mb-4">
                        <div class="form-section-title"><i class="bi bi-person-lines-fill"></i> <span>البيانات الشخصية</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">كود المستفيد</label>
                                <input name="code" class="form-control @error('code') is-invalid @enderror"
                                    value="{{ old('code') }}" placeholder="يُنشأ تلقائيًا عند تركه فارغًا">
                                @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-required">الاسم الكامل</label>
                                <input name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                    value="{{ old('full_name') }}" required placeholder="اسم المستفيد">
                                @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">الرقم القومي</label>
                                <input name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                    value="{{ old('national_id') }}" placeholder="14 رقم">
                                @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">رقم الهاتف</label>
                                <input name="phone" class="form-control @error('phone') is-invalid @enderror"
                                    value="{{ old('phone') }}" placeholder="01xxxxxxxxx">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">العنوان</label>
                                <input name="address" class="form-control @error('address') is-invalid @enderror"
                                    value="{{ old('address') }}" placeholder="العنوان بالتفصيل">
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-hand-thumbs-up"></i> <span>المساعدة والتخصيص</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">نوع المساعدة</label>
                                <select name="assistance_type" class="form-select @error('assistance_type') is-invalid @enderror" required>
                                    <option value="financial" @selected(old('assistance_type', 'financial') === 'financial')>مالية</option>
                                    <option value="in_kind" @selected(old('assistance_type') === 'in_kind')>عينية</option>
                                    <option value="service" @selected(old('assistance_type') === 'service')>خدمة</option>
                                </select>
                                @error('assistance_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">نوع التخصيص</label>
                                <select id="guest-house-allocation-type" name="allocation_type" class="form-select">
                                    <option value="">— بدون تخصيص —</option>
                                    <option value="شخص واحد" @selected(old('allocation_type') === 'شخص واحد')>فردي — شخص واحد</option>
                                    <option value="أكثر من مستفيد" @selected(old('allocation_type') === 'أكثر من مستفيد')>جماعي — أكثر من مستفيد</option>
                                </select>
                            </div>
                            <div id="guest-house-allocated-field" class="col-md-6 {{ old('allocation_type') ? '' : 'd-none' }}">
                                <label id="guest-house-allocated-label" class="form-label">اسم المستفيد</label>
                                <select id="guest-house-allocated-list" name="allocated_beneficiary_ids[]"
                                    class="form-select @error('allocated_beneficiary_ids') is-invalid @enderror">
                                    <option value="" data-placeholder>— ابحث بالاسم أو الكود —</option>
                                    @foreach($beneficiaryOptions as $option)
                                        <option value="{{ $option->id }}" @selected(in_array((int) $option->id, $selectedAllocatedBeneficiaries, true))>
                                            {{ $option->full_name }}{{ $option->code ? ' — '.$option->code : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="guest-house-allocated-help" class="form-help-text"></div>
                                @error('allocated_beneficiary_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">عدد الكفلاء</label>
                                <select id="guest-house-sponsorship-type" name="child_sponsorship_type" class="form-select">
                                    <option value="">— بدون كافل —</option>
                                    <option value="كافل واحد" @selected(old('child_sponsorship_type') === 'كافل واحد')>كافل واحد</option>
                                    <option value="أكثر من كافل" @selected(old('child_sponsorship_type') === 'أكثر من كافل')>أكثر من كافل</option>
                                </select>
                            </div>
                            <div id="guest-house-sponsors-field" class="col-md-6 {{ old('child_sponsorship_type') ? '' : 'd-none' }}">
                                <label id="guest-house-sponsors-label" class="form-label">اسم الكافل</label>
                                <select id="guest-house-sponsors-list" name="sponsor_ids[]"
                                    class="form-select @error('sponsor_ids') is-invalid @enderror">
                                    <option value="" data-placeholder>— ابحث بالاسم أو الهاتف —</option>
                                    @foreach($sponsors as $sponsor)
                                        <option value="{{ $sponsor->id }}" @selected(in_array((int) $sponsor->id, $selectedSponsors, true))>
                                            {{ $sponsor->name }}{{ $sponsor->phone ? ' — '.$sponsor->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                <div id="guest-house-sponsors-help" class="form-help-text"></div>
                                @error('sponsor_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات</label>
                                <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3">{{ old('notes') }}</textarea>
                                @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                    <div class="form-section mt-4">
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

                            {{-- National ID Images --}}
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-card-image text-primary me-1"></i> صورة وجه البطاقة الشخصية</label>
                                <input type="file" name="patient_id_front" accept="image/*" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold"><i class="bi bi-card-image text-primary me-1"></i> صورة ظهر البطاقة الشخصية</label>
                                <input type="file" name="patient_id_back" accept="image/*" class="form-control">
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
