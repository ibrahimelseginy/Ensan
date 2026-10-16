<style>
    .gh-ops-card { background:#fff; border:1px solid #dbe3ee; border-radius:14px; box-shadow:0 5px 18px rgba(15,23,42,.07); }
    .theme-dark .gh-ops-card { background:#111827; border-color:#334155; }
    .gh-bed { width:92px; min-height:82px; border:0; border-radius:12px; padding:.55rem; color:#fff; display:inline-flex; flex-direction:column; align-items:center; justify-content:center; gap:.2rem; font-weight:700; }
    .gh-bed-available { background-color:#16803c !important; }
    .gh-bed-occupied { background-color:#c62828 !important; }
    .gh-bed-maintenance { background-color:#64748b !important; }
    .gh-status-dot { width:11px; height:11px; border-radius:50%; display:inline-block; }
    .gh-ops-tabs .nav-link { color:inherit; font-weight:700; }
    .gh-ops-tabs .nav-link.active { color:#0d6efd; }
    .gh-file-links a { margin-inline-end:.5rem; }
</style>

@php
    $mealLabels = ['breakfast' => 'الإفطار', 'lunch' => 'الغداء', 'dinner' => 'العشاء'];
@endphp

<div class="gh-ops-card p-3 p-lg-4 mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="fw-bold mb-1"><i class="bi bi-hospital me-1 text-primary"></i> تشغيل دار الضيافة</h5>
            <span class="text-muted small">{{ $guest_house->governorate ?: 'المحافظة غير محددة' }} · {{ $residentStays->count() }} مقيم · {{ $availableBeds->count() }} سرير متاح</span>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#guestHouseWingModal"><i class="bi bi-grid-3x3-gap me-1"></i> إضافة جناح وأسِرّة</button>
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#guestHouseCustodyModal"><i class="bi bi-safe me-1"></i> إضافة عهدة</button>
            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#guestHouseIssueModal"><i class="bi bi-box-arrow-up me-1"></i> صرف من المخزون</button>
        </div>
    </div>

    <ul class="nav nav-tabs gh-ops-tabs mb-3" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#gh-beds-pane">الأجنحة والأسِرّة</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-residents-pane">المقيمون والمغادرون</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-requests-pane">طلبات جديدة <span class="badge bg-danger">{{ $pendingBookings->count() + $pendingMobileCases->count() }}</span></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-meals-pane">الوجبات</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-custody-pane">العهدة والمخزون</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="gh-beds-pane">
            <div class="d-flex flex-wrap gap-3 small mb-3">
                <span><i class="gh-status-dot" style="background:#16803c"></i> فارغ</span>
                <span><i class="gh-status-dot" style="background:#c62828"></i> محجوز</span>
                <span><i class="gh-status-dot" style="background:#64748b"></i> صيانة</span>
            </div>
            @forelse($guestHouseWings as $wing)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>{{ $wing->name }}</strong><span class="text-muted small">{{ $wing->beds->count() }} سرير</span>
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($wing->beds as $bed)
                            @php $occupied = (bool) $bed->activeStay; @endphp
                            <form method="POST" action="{{ route('guest-houses.beds.status', [$guest_house, $bed]) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="{{ $bed->status === 'maintenance' ? 'available' : 'maintenance' }}">
                                <button class="gh-bed {{ $occupied ? 'gh-bed-occupied' : ($bed->status === 'maintenance' ? 'gh-bed-maintenance' : 'gh-bed-available') }}" type="submit" @disabled($occupied) title="{{ $occupied ? 'السرير محجوز ولا يمكن تغيير حالته' : 'اضغط للتبديل بين متاح وصيانة' }}">
                                    <i class="bi bi-hospital-bed fs-5"></i><span>سرير {{ $bed->number }}</span>
                                    <small>{{ $occupied ? $bed->activeStay->beneficiary->full_name : ($bed->status === 'maintenance' ? 'صيانة' : 'فارغ') }}</small>
                                </button>
                            </form>
                        @endforeach
                    </div>
                </div>
            @empty
                <div class="text-center text-muted py-4">أضف أول جناح وحدد عدد الأسِرّة ليظهر مخطط الحجز.</div>
            @endforelse
        </div>

        <div class="tab-pane fade" id="gh-residents-pane">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h6 class="fw-bold mb-0 text-success"><i class="bi bi-person-check-fill me-1"></i> الحالات المقيمة حاليًا بالدار</h6>
                    <small class="text-muted">متابعة النزلاء الحاليين والأسِرّة المشغولة</small>
                </div>
                @if($residentStays->isNotEmpty())
                    <form method="POST" action="{{ route('guest-houses.stays.departAll', $guest_house) }}" onsubmit="return confirm('تأكيد تسجيل مغادرة نهاية الأسبوع (الخميس) لجميع الحالات المقيمة وإخلاء جميع الأسِرّة؟');">
                        @csrf
                        <button type="submit" class="btn btn-danger btn-sm rounded-pill px-3 shadow-sm fw-bold">
                            <i class="bi bi-calendar2-check me-1"></i> تسجيل مغادرة الخميس لجميع المقيمين
                        </button>
                    </form>
                @endif
            </div>

            <div class="table-responsive mb-4">
                <table class="table align-middle table-hover border">
                    <thead class="table-light"><tr><th>المستفيد</th><th>الجناح / السرير</th><th>تاريخ الوصول</th><th>مدة الإقامة</th><th>إجراء المغادرة</th></tr></thead>
                    <tbody>
                    @forelse($residentStays as $stay)
                        <tr>
                            <td>
                                <strong>{{ $stay->beneficiary->full_name }}</strong>
                                <div class="small text-muted">{{ $stay->beneficiary->phone ?: 'بدون هاتف' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-primary-subtle text-primary border px-2 py-1">
                                    {{ $stay->bed?->wing?->name ?: '—' }} / سرير {{ $stay->bed?->number ?: '—' }}
                                </span>
                            </td>
                            <td>{{ $stay->arrival_date?->format('Y-m-d') }}</td>
                            <td>{{ $stay->expected_days ? $stay->expected_days . ' أيام' : 'غير محدد' }}</td>
                            <td>
                                <form method="POST" action="{{ route('guest-houses.stays.depart', [$guest_house, $stay]) }}" onsubmit="return confirm('تأكيد مغادرة الحالة وإخلاء السرير؟')">
                                    @csrf
                                    <button class="btn btn-outline-danger btn-sm rounded-pill px-3 fw-bold">
                                        <i class="bi bi-box-arrow-left me-1"></i> تم المغادرة (الخميس)
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty <tr><td colspan="5" class="text-center text-muted py-3">لا توجد حالات مقيمة حاليًا بالدار.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
                <div>
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-arrow-repeat me-1"></i> آخر الحالات المغادرة — تسجيل عودة السبت</h6>
                    <small class="text-muted">عند عودة المريض يوم السبت اختر السرير وعدد الأيام لتسجيله مقيماً فوراً</small>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover border">
                    <thead class="table-light"><tr><th>المستفيد</th><th>تاريخ المغادرة</th><th>سرير العودة</th><th>الوصول / الأيام</th><th>إجراء</th></tr></thead>
                    <tbody>
                    @forelse($departedStays as $stay)
                        <tr>
                            <form method="POST" action="{{ route('guest-houses.stays.return', [$guest_house, $stay]) }}">
                                @csrf
                                <td>
                                    <strong>{{ $stay->beneficiary->full_name }}</strong>
                                    <div class="small text-muted">{{ $stay->beneficiary->phone ?: 'بدون هاتف' }}</div>
                                </td>
                                <td>{{ $stay->departed_at?->format('Y-m-d H:i') }}</td>
                                <td>
                                    <select name="guest_house_bed_id" class="form-select form-select-sm" required style="min-width: 140px;">
                                        <option value="">— اختر سرير —</option>
                                        @foreach($availableBeds as $bed)
                                            <option value="{{ $bed->id }}">{{ $bed->wing->name }} / سرير {{ $bed->number }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <input type="date" name="arrival_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required style="width: 130px;">
                                        <input type="number" name="expected_days" min="1" max="365" value="7" class="form-control form-control-sm" style="width: 75px;" placeholder="أيام" required>
                                    </div>
                                </td>
                                <td>
                                    <button class="btn btn-success btn-sm rounded-pill px-3 fw-bold" @disabled($availableBeds->isEmpty())>
                                        <i class="bi bi-box-arrow-in-right me-1"></i> عاد (السبت) / مقيم
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty <tr><td colspan="5" class="text-center text-muted py-3">لا توجد حالات مغادرة مسجلة.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="tab-pane fade" id="gh-requests-pane">
            <h6 class="fw-bold mb-3"><i class="bi bi-envelope-paper me-1 text-primary"></i> طلبات حجز دار الضيافة من الويب والتطبيق</h6>
            <div class="table-responsive mb-4">
                <table class="table align-middle table-hover border">
                    <thead class="table-light"><tr><th>الاسم والبيانات</th><th>المرفقات الطبية</th><th>تحديد السرير / المدة</th><th>القرار</th></tr></thead>
                    <tbody>
                    @forelse($pendingBookings as $booking)
                        <tr>
                            <form method="POST" action="{{ route('guest-houses.bookings.decision', [$guest_house, $booking]) }}">
                                @csrf
                                <td>
                                    <strong>{{ $booking->name }}</strong>
                                    <div class="small text-muted mb-1">
                                        <a href="tel:{{ $booking->phone }}" class="text-decoration-none me-2"><i class="bi bi-telephone text-success"></i> {{ $booking->phone }}</a>
                                        <span>· {{ $booking->source === 'mobile' ? 'التطبيق' : 'الويب' }}</span>
                                    </div>
                                    <textarea name="admin_notes" class="form-control form-control-sm" rows="1" placeholder="ملاحظات الموظف"></textarea>
                                </td>
                                <td class="gh-file-links small">
                                    @if($booking->patient_id_url)<a href="{{ $booking->patient_id_url }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 mb-1"><i class="bi bi-card-image me-1"></i>البطاقة</a>@endif
                                    @if($booking->followup_card_url)<a href="{{ $booking->followup_card_url }}" target="_blank" class="btn btn-sm btn-outline-success py-0 mb-1"><i class="bi bi-file-medical me-1"></i>كارت المتابعة</a>@endif
                                    @if($booking->medical_transfer_url)<a href="{{ $booking->medical_transfer_url }}" target="_blank" class="btn btn-sm btn-outline-warning py-0 mb-1"><i class="bi bi-file-earmark-text me-1"></i>التحويل</a>@endif
                                    @if($booking->medical_report_url)<a href="{{ $booking->medical_report_url }}" target="_blank" class="btn btn-sm btn-outline-info py-0 mb-1"><i class="bi bi-file-text me-1"></i>التقرير</a>@endif
                                </td>
                                <td>
                                    <select name="guest_house_bed_id" class="form-select form-select-sm mb-1">
                                        <option value="">— اختر السرير —</option>
                                        @foreach($availableBeds as $bed)<option value="{{ $bed->id }}">{{ $bed->wing->name }} / سرير {{ $bed->number }}</option>@endforeach
                                    </select>
                                    <input type="number" name="expected_days" min="1" value="7" class="form-control form-control-sm" placeholder="عدد الأيام">
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <button name="decision" value="accept" class="btn btn-success btn-sm rounded-pill px-3 fw-bold" @disabled($availableBeds->isEmpty())>قبول وإدخال</button>
                                        <button name="decision" value="reject" class="btn btn-outline-danger btn-sm rounded-pill px-2" formnovalidate>رفض</button>
                                    </div>
                                </td>
                            </form>
                        </tr>
                    @empty <tr><td colspan="4" class="text-center text-muted py-3">لا توجد طلبات حجز معلقة.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if($pendingMobileCases->isNotEmpty())
                <h6 class="fw-bold mb-3"><i class="bi bi-phone me-1 text-primary"></i> حالات طبية مقدمة من التطبيق</h6>
                <div class="table-responsive">
                    <table class="table align-middle table-hover border">
                        <thead class="table-light"><tr><th>الحالة</th><th>المرفقات</th><th>الإقامة والسرير</th><th>القرار</th></tr></thead>
                        <tbody>
                        @foreach($pendingMobileCases as $application)
                            <tr>
                                <form method="POST" action="{{ route('guest-houses.mobile-cases.decision', [$guest_house, $application]) }}">
                                    @csrf
                                    <td>
                                        <strong>{{ $application->applicant_name }}</strong>
                                        <div class="small text-muted mb-1">
                                            <a href="tel:{{ $application->applicant_phone }}" class="text-decoration-none me-2"><i class="bi bi-telephone text-success"></i> {{ $application->applicant_phone }}</a>
                                            <span>· {{ $application->governorate }}</span>
                                        </div>
                                        <textarea name="admin_notes" class="form-control form-control-sm" rows="1" placeholder="ملاحظات الموظف"></textarea>
                                    </td>
                                    <td class="small">
                                        @if($application->id_image_url)<a href="{{ $application->id_image_url }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0 me-1">البطاقة</a>@endif
                                        @if($application->medical_report_url)<a href="{{ $application->medical_report_url }}" target="_blank" class="btn btn-sm btn-outline-info py-0">التقرير الطبي</a>@endif
                                    </td>
                                    <td>
                                        <select name="guest_house_bed_id" class="form-select form-select-sm mb-1">
                                            <option value="">— السرير —</option>
                                            @foreach($availableBeds as $bed)<option value="{{ $bed->id }}">{{ $bed->wing->name }} / سرير {{ $bed->number }}</option>@endforeach
                                        </select>
                                        <div class="d-flex gap-1">
                                            <input type="date" name="arrival_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm">
                                            <input type="number" name="expected_days" value="7" min="1" class="form-control form-control-sm" style="width:75px">
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1">
                                            <button name="decision" value="accept" class="btn btn-success btn-sm rounded-pill px-3 fw-bold" @disabled($availableBeds->isEmpty())>قبول</button>
                                            <button name="decision" value="reject" class="btn btn-outline-danger btn-sm rounded-pill px-2" formnovalidate>رفض</button>
                                        </div>
                                    </td>
                                </form>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div class="tab-pane fade" id="gh-meals-pane">
            <div class="row g-3">
                @foreach($mealLabels as $mealType => $mealLabel)
                    @php
                        $savedMeal = $todayMeals->get($mealType);
                        $received = $savedMeal?->servings?->where('received', true)->pluck('beneficiary_id')->map(fn($id)=>(int)$id)->all() ?? [];
                    @endphp
                    <div class="col-lg-4">
                        <form class="border rounded-3 p-3 h-100 bg-light-subtle shadow-sm" method="POST" enctype="multipart/form-data" action="{{ route('guest-houses.meals.store', $guest_house) }}">
                            @csrf
                            <input type="hidden" name="meal_type" value="{{ $mealType }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h6 class="fw-bold mb-0">
                                    @if($mealType === 'breakfast') 🍳 @elseif($mealType === 'lunch') 🍲 @else 🥪 @endif
                                    {{ $mealLabel }}
                                </h6>
                                @if($savedMeal)
                                    <span class="badge bg-success small"><i class="bi bi-check me-1"></i> مسجلة</span>
                                @endif
                            </div>

                            <div class="row g-2 mb-2">
                                <div class="col-7">
                                    <input type="date" name="meal_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-5">
                                    <input type="time" name="served_at" value="{{ $savedMeal?->served_at ? substr($savedMeal->served_at,0,5) : '' }}" class="form-control form-control-sm">
                                </div>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small fw-bold text-muted">قائمة المقيمين (استلام الوجبة):</span>
                                <div>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-primary small me-2" onclick="toggleAllMeals('{{ $mealType }}', true)">الكل</button>
                                    <button type="button" class="btn btn-link btn-sm p-0 text-secondary small" onclick="toggleAllMeals('{{ $mealType }}', false)">إلغاء</button>
                                </div>
                            </div>

                            <div class="border rounded p-2 mb-2 bg-white" style="max-height:190px; overflow-y:auto">
                                @forelse($residentStays as $stay)
                                    <div class="form-check mb-1">
                                        <input class="form-check-input meal-chk-{{ $mealType }}" type="checkbox" name="received_beneficiary_ids[]" value="{{ $stay->beneficiary_id }}" id="meal-{{ $mealType }}-{{ $stay->beneficiary_id }}" @checked(in_array((int)$stay->beneficiary_id, $received, true))>
                                        <label class="form-check-label small" for="meal-{{ $mealType }}-{{ $stay->beneficiary_id }}">
                                            {{ $stay->beneficiary->full_name }}
                                            <span class="text-muted" style="font-size:0.75rem">({{ $stay->bed?->wing?->name ?: 'جناح' }} - سرير {{ $stay->bed?->number ?: '—' }})</span>
                                        </label>
                                    </div>
                                @empty
                                    <span class="text-muted small">لا يوجد مقيمون حالياً.</span>
                                @endforelse
                            </div>

                            <label class="form-label small fw-bold"><i class="bi bi-camera me-1"></i> إرفاق صورة {{ $mealLabel }}</label>
                            <input type="file" name="meal_image" accept="image/*" class="form-control form-control-sm mb-2">
                            @if($savedMeal?->image_url)
                                <div class="d-flex align-items-center gap-2 mb-2 p-1 border rounded bg-white">
                                    <img src="{{ $savedMeal->image_url }}" alt="{{ $mealLabel }}" class="rounded" style="width: 44px; height: 38px; object-fit: cover;">
                                    <a href="{{ $savedMeal->image_url }}" target="_blank" class="small text-primary fw-bold">عرض صورة {{ $mealLabel }} المرفقة</a>
                                </div>
                            @endif
                            <button class="btn btn-primary btn-sm w-100 rounded-pill fw-bold">حفظ كشف {{ $mealLabel }}</button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="tab-pane fade" id="gh-custody-pane">
            <div class="row g-3 mb-4">
                @forelse($guestHouseCustodies as $custody)
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 h-100 bg-white shadow-sm">
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>{{ $custody->name }}</strong>
                                <span class="badge {{ $custody->type === 'financial' ? 'bg-success' : 'bg-info' }}">
                                    {{ $custody->type === 'financial' ? 'عهدة مالية' : 'عهدة عينية' }}
                                </span>
                            </div>
                            <div class="small text-muted mt-2">{{ $custody->treasury?->name ?: $custody->warehouse?->name }}</div>
                            @if($custody->treasury)
                                <div class="fw-bold mt-1 text-success fs-5">{{ number_format((float)$custody->treasury->current_balance, 2) }} {{ $custody->treasury->currency }}</div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-12 text-center text-muted py-3">لم تتم إضافة عهدة للدار بعد. استخدم زر "إضافة عهدة" أعلاه.</div>
                @endforelse
            </div>

            <div class="row g-4">
                <div class="col-lg-6">
                    <h6 class="fw-bold mb-2"><i class="bi bi-boxes me-1 text-primary"></i> رصيد المخزون العيني المتاح بالدار</h6>
                    <div class="table-responsive border rounded-3 bg-white">
                        <table class="table mb-0 align-middle">
                            <thead class="table-light"><tr><th>الصنف</th><th>المخزن</th><th>الرصيد المتبقي</th></tr></thead>
                            <tbody>
                            @forelse($guestHouseInventory as $stock)
                                <tr>
                                    <td><strong>{{ $stock->item?->name }}</strong></td>
                                    <td><span class="text-muted small">{{ $stock->warehouse?->name }}</span></td>
                                    <td class="fw-bold text-success">{{ number_format((float)$stock->stock, 2) }} {{ $stock->item?->unit }}</td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center text-muted py-3">لا توجد أصناف في مخزون الدار حالياً.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="col-lg-6">
                    <h6 class="fw-bold mb-2"><i class="bi bi-clock-history me-1 text-primary"></i> سجل حركة المخزون (الوارد والمنصرف)</h6>
                    <div class="table-responsive border rounded-3 bg-white" style="max-height: 280px; overflow-y:auto;">
                        <table class="table mb-0 align-middle small">
                            <thead class="table-light"><tr><th>التاريخ</th><th>الصنف</th><th>النوع</th><th>الكمية</th></tr></thead>
                            <tbody>
                            @forelse($inventoryLogs ?? [] as $log)
                                <tr>
                                    <td>{{ $log->transaction_date ? \Carbon\Carbon::parse($log->transaction_date)->format('m-d') : '—' }}</td>
                                    <td>{{ $log->item?->name }}</td>
                                    <td>
                                        <span class="badge {{ $log->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                            {{ $log->type === 'in' ? 'وارد' : 'منصرف' }}
                                        </span>
                                    </td>
                                    <td class="fw-bold {{ $log->type === 'in' ? 'text-success' : 'text-danger' }}">
                                        {{ $log->type === 'in' ? '+' : '-' }}{{ number_format((float)$log->quantity, 2) }}
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">لا توجد حركات مسجلة.</td></tr>
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleAllMeals(type, check) {
        document.querySelectorAll('.meal-chk-' + type).forEach(chk => chk.checked = check);
    }
</script>
    </div>
</div>

<div class="modal fade" id="guestHouseWingModal" tabindex="-1"><div class="modal-dialog"><form class="modal-content" method="POST" action="{{ route('guest-houses.wings.store', $guest_house) }}">@csrf<div class="modal-header"><h5 class="modal-title">إضافة جناح وأسِرّة</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">اسم الجناح</label><input name="name" class="form-control mb-3" placeholder="مثال: جناح 1" required><label class="form-label">عدد الأسِرّة</label><input type="number" name="beds_count" min="1" max="100" value="8" class="form-control" required></div><div class="modal-footer"><button class="btn btn-primary">إضافة</button></div></form></div></div>

<div class="modal fade" id="guestHouseCustodyModal" tabindex="-1"><div class="modal-dialog"><form class="modal-content" method="POST" action="{{ route('guest-houses.custodies.store', $guest_house) }}">@csrf<div class="modal-header"><h5 class="modal-title">إضافة عهدة للدار</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">اسم العهدة</label><input name="name" class="form-control mb-2" required><label class="form-label">النوع</label><select name="type" class="form-select mb-2" required><option value="financial">مالية</option><option value="in_kind">عينية</option></select><label class="form-label">الخزينة — للعهدة المالية</label><select name="treasury_id" class="form-select mb-1"><option value="">— إنشاء خزينة عهدة جديدة تلقائيًا —</option>@foreach($guestHouseExpenseTreasuries as $treasury)<option value="{{ $treasury->id }}">{{ $treasury->name }}</option>@endforeach</select><div class="small text-muted mb-2">عند عدم اختيار خزينة سيتم إنشاء خزينة عهدة مالية للدار برصيد صفر.</div><label class="form-label">المخزن — للعهدة العينية</label><select name="warehouse_id" class="form-select"><option value="">— اختر —</option>@foreach($guestHouseWarehouses as $warehouse)<option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>@endforeach</select></div><div class="modal-footer"><button class="btn btn-warning">حفظ العهدة</button></div></form></div></div>

<div class="modal fade" id="guestHouseIssueModal" tabindex="-1"><div class="modal-dialog"><form class="modal-content" method="POST" action="{{ route('guest-houses.inventory.issue', $guest_house) }}">@csrf<div class="modal-header"><h5 class="modal-title">صرف من مخزون عيني</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div><div class="modal-body"><label class="form-label">العهدة العينية</label><select name="custody_id" class="form-select mb-2" required><option value="">— اختر —</option>@foreach($inventoryCustodies as $custody)<option value="{{ $custody->id }}">{{ $custody->name }} — {{ $custody->warehouse?->name }}</option>@endforeach</select><label class="form-label">الصنف</label><select name="item_id" class="form-select mb-2" required><option value="">— اختر —</option>@foreach($guestHouseItems as $item)<option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>@endforeach</select><label class="form-label">الكمية</label><input type="number" step="0.001" min="0.001" name="quantity" class="form-control mb-2" required><label class="form-label">المستفيد (اختياري)</label><select name="beneficiary_id" class="form-select mb-2"><option value="">— صرف عام للدار —</option>@foreach($residentStays as $stay)<option value="{{ $stay->beneficiary_id }}">{{ $stay->beneficiary->full_name }}</option>@endforeach</select><label class="form-label">سبب الصرف</label><textarea name="notes" class="form-control" required placeholder="مثال: وجبات يوم السبت"></textarea></div><div class="modal-footer"><button class="btn btn-info text-white" @disabled($inventoryCustodies->isEmpty())>تنفيذ الصرف</button></div></form></div></div>
