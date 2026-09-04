@if(request()->user()?->hasPermission('donations.view'))
    <div class="modal fade" id="projectDonationModal" tabindex="-1" aria-labelledby="projectDonationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('projects.storeDonation', $project) }}">
                @csrf
                <input type="hidden" name="form_context" value="project_donation">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="projectDonationModalLabel">
                            <i class="bi bi-cash-coin text-success me-1"></i> إضافة تبرع للمشروع
                        </h5>
                        <div class="small text-muted mt-1">{{ $project->name }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    @if($projectDonationTreasuries->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            لا توجد خزينة مفعّلة لاستلام تبرع نقدي حاليًا. يمكنك تسجيل تبرع عيني أو تفعيل خزينة أولًا.
                        </div>
                    @endif
                    @if($projectWarehouses->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-box-seam me-1"></i>
                            لا يوجد مخزن مفعّل لاستلام التبرعات العينية.
                        </div>
                    @endif

                    <div class="form-section mb-4">
                        <div class="form-section-title">
                            <i class="bi bi-person-heart"></i> <span>بيانات المتبرع</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">طريقة إدخال المتبرع</label>
                                <select id="project-donor-mode" class="form-select">
                                    <option value="existing" @selected(!old('new_donor_name'))>اختيار متبرع مسجل</option>
                                    <option value="new" @selected((bool) old('new_donor_name'))>إضافة متبرع جديد</option>
                                </select>
                            </div>
                            <div id="project-existing-donor-field" class="col-md-6">
                                <label class="form-label form-label-required">المتبرع</label>
                                <select id="project-donor-id" name="donor_id" class="form-select @error('donor_id') is-invalid @enderror">
                                    <option value="">— اختر المتبرع —</option>
                                    @foreach($projectDonors as $donor)
                                        <option value="{{ $donor->id }}" @selected(old('donor_id') == $donor->id)>
                                            {{ $donor->name }} — {{ $donor->code }}{{ $donor->phone ? ' — '.$donor->phone : '' }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('donor_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div id="project-new-donor-fields" class="col-12 d-none">
                                <div class="row g-3">
                                    <div class="col-12">
                                        <label class="form-label">كود المتبرع <span class="badge bg-primary-subtle text-primary">ثابت</span></label>
                                        <input name="new_donor_code" dir="ltr" class="form-control font-monospace @error('new_donor_code') is-invalid @enderror"
                                            value="{{ old('new_donor_code') }}" placeholder="اختياري — يُنشأ تلقائيًا عند تركه فارغًا">
                                        @error('new_donor_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-required">اسم المتبرع الجديد</label>
                                        <input id="project-new-donor-name" name="new_donor_name"
                                            class="form-control @error('new_donor_name') is-invalid @enderror"
                                            value="{{ old('new_donor_name') }}" placeholder="الاسم ثلاثي">
                                        @error('new_donor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label form-label-required">رقم الهاتف</label>
                                        <input id="project-new-donor-phone" name="new_donor_phone"
                                            class="form-control @error('new_donor_phone') is-invalid @enderror"
                                            value="{{ old('new_donor_phone') }}" placeholder="01xxxxxxxxx">
                                        @error('new_donor_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">تصنيف المتبرع</label>
                                        <select name="new_donor_classification" class="form-select">
                                            <option value="one_time" @selected(old('new_donor_classification', 'one_time') === 'one_time')>مرة واحدة</option>
                                            <option value="recurring" @selected(old('new_donor_classification') === 'recurring')>متكرر</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label">دورة التكرار</label>
                                        <select name="new_donor_cycle" class="form-select">
                                            <option value="">— اختر —</option>
                                            <option value="monthly" @selected(old('new_donor_cycle') === 'monthly')>شهري</option>
                                            <option value="yearly" @selected(old('new_donor_cycle') === 'yearly')>سنوي</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-wallet2"></i> <span>بيانات التبرع</span>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">نوع التبرع</label>
                                <select id="project-donation-type" name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="cash" @selected(old('type', 'cash') === 'cash')>نقدي</option>
                                    <option value="in_kind" @selected(old('type') === 'in_kind')>عيني</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الاستلام</label>
                                <input type="date" name="received_at" class="form-control @error('received_at') is-invalid @enderror"
                                    value="{{ old('received_at', now()->toDateString()) }}">
                                @error('received_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 project-donation-cash-field">
                                <label class="form-label form-label-required">المبلغ</label>
                                <div class="input-group">
                                    <input id="project-donation-amount" name="amount" type="number" step="0.01" min="0.01"
                                        class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00">
                                    <select name="currency" class="form-select" style="max-width:110px">
                                        @foreach(['EGP', 'USD', 'EUR', 'SAR'] as $currency)
                                            <option value="{{ $currency }}" @selected(old('currency', 'EGP') === $currency)>{{ $currency }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 project-donation-cash-field">
                                <label class="form-label form-label-required">طريقة التحصيل</label>
                                <select id="project-donation-cash-channel" name="cash_channel" class="form-select @error('cash_channel') is-invalid @enderror">
                                    <option value="cash" @selected(old('cash_channel', 'cash') === 'cash')>نقدي</option>
                                    <option value="instapay" @selected(old('cash_channel') === 'instapay')>إنستاباي</option>
                                    <option value="vodafone_cash" @selected(old('cash_channel') === 'vodafone_cash')>فودافون كاش</option>
                                    <option value="delegate" @selected(old('cash_channel') === 'delegate')>مندوب</option>
                                </select>
                                @error('cash_channel')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 project-donation-cash-field">
                                <label class="form-label form-label-required">رقم الإيصال</label>
                                <input id="project-donation-receipt" name="receipt_number"
                                    class="form-control @error('receipt_number') is-invalid @enderror"
                                    value="{{ old('receipt_number') }}" placeholder="REC-0001">
                                @error('receipt_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 project-donation-cash-field">
                                <label class="form-label form-label-required">الخزينة المستلمة</label>
                                <select id="project-donation-treasury" name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror">
                                    <option value="">— اختر الخزينة —</option>
                                    @foreach($projectDonationTreasuries as $treasury)
                                        <option value="{{ $treasury->id }}" @selected(old('treasury_id') == $treasury->id)>
                                            {{ $treasury->name }} — {{ number_format((float) $treasury->current_balance, 2) }} {{ $treasury->currency }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 project-donation-in-kind-field d-none">
                                <label class="form-label form-label-required">القيمة التقديرية</label>
                                <input id="project-donation-estimated-value" name="estimated_value" type="number" step="0.01" min="0.01"
                                    class="form-control @error('estimated_value') is-invalid @enderror"
                                    value="{{ old('estimated_value') }}" placeholder="0.00">
                                @error('estimated_value')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 project-donation-in-kind-field d-none">
                                <label class="form-label form-label-required">المخزن المستلم</label>
                                <select id="project-donation-warehouse" name="warehouse_id" class="form-select @error('warehouse_id') is-invalid @enderror">
                                    <option value="">— اختر المخزن —</option>
                                    @foreach($projectWarehouses as $warehouse)
                                        <option value="{{ $warehouse->id }}" @selected(old('warehouse_id') == $warehouse->id)>{{ $warehouse->name }}</option>
                                    @endforeach
                                </select>
                                @error('warehouse_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">ملاحظات التخصيص</label>
                                <textarea name="allocation_note" class="form-control @error('allocation_note') is-invalid @enderror"
                                    rows="2" placeholder="أي تفاصيل إضافية عن تخصيص التبرع...">{{ old('allocation_note') }}</textarea>
                                @error('allocation_note')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success"><i class="bi bi-check-lg me-1"></i> حفظ التبرع</button>
                </div>
            </form>
        </div>
    </div>
@endif

@if(request()->user()?->hasPermission('manage_finance') && request()->user()?->hasPermission('expenses.view'))
    <div class="modal fade" id="projectExpenseModal" tabindex="-1" aria-labelledby="projectExpenseModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <form class="modal-content" method="POST" action="{{ route('projects.storeExpense', $project) }}">
                @csrf
                <input type="hidden" name="form_context" value="project_expense">
                <input type="hidden" name="currency" value="EGP">
                <div class="modal-header">
                    <div>
                        <h5 class="modal-title" id="projectExpenseModalLabel">
                            <i class="bi bi-receipt text-danger me-1"></i> إضافة مصروف للمشروع
                        </h5>
                        <div class="small text-muted mt-1">{{ $project->name }}</div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                </div>
                <div class="modal-body">
                    @if($projectExpenseTreasuries->isEmpty())
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            لا توجد خزينة متاحة للصرف. يجب إنشاء أو تفعيل خزينة أولًا.
                        </div>
                    @endif
                    <div class="form-section">
                        <div class="form-section-title"><i class="bi bi-info-circle"></i> <span>بيانات المصروف</span></div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label form-label-required">نوع المصروف</label>
                                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                                    <option value="operational" @selected(old('type', 'operational') === 'operational')>تشغيلي</option>
                                    <option value="aid" @selected(old('type') === 'aid')>مساعدات</option>
                                    <option value="logistics" @selected(old('type') === 'logistics')>لوجستي</option>
                                </select>
                                @error('type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">البند الفرعي</label>
                                <input list="projectExpenseCategoryOptions" name="category"
                                    class="form-control @error('category') is-invalid @enderror"
                                    value="{{ old('category') }}" placeholder="اختر أو اكتب...">
                                <datalist id="projectExpenseCategoryOptions">
                                    <option value="إطعام"><option value="مشتريات"><option value="نقل ومواصلات">
                                    <option value="دعاية وتسويق"><option value="نثريات"><option value="صيانة">
                                </datalist>
                                @error('category')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-required">المبلغ</label>
                                <div class="input-group">
                                    <input name="amount" type="number" step="0.01" min="0.01" required
                                        class="form-control @error('amount') is-invalid @enderror" value="{{ old('amount') }}" placeholder="0.00">
                                    <span class="input-group-text">EGP</span>
                                </div>
                                @error('amount')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label form-label-required">الخزينة (مصدر الصرف)</label>
                                <select name="treasury_id" class="form-select @error('treasury_id') is-invalid @enderror" required>
                                    <option value="">— اختر الخزينة —</option>
                                    @foreach($projectExpenseTreasuries as $treasury)
                                        <option value="{{ $treasury->id }}" @selected(old('treasury_id') == $treasury->id)>
                                            {{ $treasury->name }} — الرصيد: {{ number_format((float) $treasury->current_balance, 2) }} {{ $treasury->currency }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('treasury_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">المستفيد (اختياري)</label>
                                <select name="beneficiary_id" class="form-select @error('beneficiary_id') is-invalid @enderror">
                                    <option value="">— بدون مستفيد محدد —</option>
                                    @foreach($projectBeneficiaryOptions as $beneficiaryOption)
                                        <option value="{{ $beneficiaryOption->id }}" @selected(old('beneficiary_id') == $beneficiaryOption->id)>
                                            {{ $beneficiaryOption->full_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('beneficiary_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">تاريخ الصرف</label>
                                <input type="date" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror"
                                    value="{{ old('paid_at', now()->toDateString()) }}">
                                @error('paid_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">وصف المصروف</label>
                                <textarea name="description" class="form-control @error('description') is-invalid @enderror"
                                    rows="3" placeholder="أضف تفاصيل المصروف...">{{ old('description') }}</textarea>
                                @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger" @disabled($projectExpenseTreasuries->isEmpty())>
                        <i class="bi bi-check-lg me-1"></i> حفظ المصروف
                    </button>
                </div>
            </form>
        </div>
    </div>
@endif
