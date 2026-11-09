<style>
    .gh-ops-card { background:#fff; border:1px solid #dbe3ee; border-radius:14px; box-shadow:0 5px 18px rgba(15,23,42,.07); }
    .theme-dark .gh-ops-card { background:#111827; border-color:#334155; }
    .gh-bed { width:96px; min-height:86px; border:0; border-radius:12px; padding:.5rem .35rem; color:#fff; display:inline-flex; flex-direction:column; align-items:center; justify-content:center; gap:.2rem; font-weight:700; transition: transform 0.15s ease, box-shadow 0.15s ease; }
    .gh-bed:hover { transform: translateY(-2px); box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .gh-bed-available { background-color:#16803c !important; }
    .gh-bed-occupied { background-color:#c62828 !important; }
    .gh-bed-temporary-out { background-color:#d97706 !important; }
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
            @if(!empty($isAdmin) || !empty($isAssigned) || auth()->user()?->hasRole(['manager', 'مدير دار الضيافة']))
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#guestHouseWingModal"><i class="bi bi-grid-3x3-gap me-1"></i> إضافة جناح وأسِرّة</button>
            @endif
            <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#guestHouseCustodyModal"><i class="bi bi-safe me-1"></i> إضافة عهدة</button>
            <button class="btn btn-success btn-sm text-white" data-bs-toggle="modal" data-bs-target="#guestHouseSupplyModal"><i class="bi bi-box-arrow-in-down me-1"></i> توريد للمخزون</button>
            <button class="btn btn-info btn-sm text-white" data-bs-toggle="modal" data-bs-target="#guestHouseIssueModal"><i class="bi bi-box-arrow-up me-1"></i> صرف من المخزون</button>
            <button class="btn btn-outline-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddPettyExpense"><i class="bi bi-cash-coin me-1"></i> تسجيل مصروف نثري</button>
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddAsset"><i class="bi bi-cpu me-1"></i> إضافة جهاز/معدة</button>
            <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#modalAddSupportTicket"><i class="bi bi-headset me-1"></i> مكالمة / دعم</button>
        </div>
    </div>

    {{-- Top KPI Cards for Guest House 4 Pillars --}}
    @php
        $totalPettyCashBalance = $pettyCashCustodies->sum(fn($c) => (float)($c->treasury?->current_balance ?? 0));
        $brokenAssetsCount = ($assetStats['needs_maintenance'] ?? 0) + ($assetStats['out_of_service'] ?? 0);
        $lowFoodStockCount = $guestHouseInventory->filter(function($stock) {
            return (float)$stock->stock <= 5;
        })->count();
        $openTicketsCount = ($ticketStats['open'] ?? 0) + ($ticketStats['emergency'] ?? 0);
    @endphp
    <div class="row g-2 mb-3">
        <div class="col-6 col-md-3">
            <div class="p-2.5 px-3 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between shadow-xs h-100">
                <div>
                    <div class="text-muted small fw-semibold">رصيد النقدية المتاح بالدار</div>
                    <div class="fs-6 fw-bold text-success">{{ number_format($totalPettyCashBalance, 2) }} ج.م</div>
                </div>
                <div class="fs-4 text-success opacity-75"><i class="bi bi-cash-coin"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2.5 px-3 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between shadow-xs h-100">
                <div>
                    <div class="text-muted small fw-semibold">أجهزة تحتاج صيانة / معطلة</div>
                    <div class="fs-6 fw-bold {{ $brokenAssetsCount > 0 ? 'text-danger' : 'text-secondary' }}">{{ $brokenAssetsCount }} جهاز</div>
                </div>
                <div class="fs-4 {{ $brokenAssetsCount > 0 ? 'text-danger' : 'text-secondary' }} opacity-75"><i class="bi bi-tools"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2.5 px-3 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between shadow-xs h-100">
                <div>
                    <div class="text-muted small fw-semibold">نواقص المواد الغذائية</div>
                    <div class="fs-6 fw-bold {{ $lowFoodStockCount > 0 ? 'text-warning-emphasis' : 'text-success' }}">{{ $lowFoodStockCount }} صنف</div>
                </div>
                <div class="fs-4 {{ $lowFoodStockCount > 0 ? 'text-warning' : 'text-success' }} opacity-75"><i class="bi bi-cart-x"></i></div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="p-2.5 px-3 rounded-3 border bg-light-subtle d-flex align-items-center justify-content-between shadow-xs h-100">
                <div>
                    <div class="text-muted small fw-semibold">طلبات دعم ومكالمات مفتوحة</div>
                    <div class="fs-6 fw-bold {{ $openTicketsCount > 0 ? 'text-primary' : 'text-secondary' }}">{{ $openTicketsCount }} بلاغ / مكالمة</div>
                </div>
                <div class="fs-4 text-primary opacity-75"><i class="bi bi-headset"></i></div>
            </div>
        </div>
    </div>

    <ul class="nav nav-tabs gh-ops-tabs mb-3" role="tablist">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#gh-beds-pane"><i class="bi bi-grid-3x3-gap me-1"></i> الأجنحة والأسِرّة</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-residents-pane"><i class="bi bi-people me-1"></i> المقيمون والمغادرون</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-requests-pane"><i class="bi bi-inbox me-1"></i> طلبات جديدة <span class="badge bg-danger">{{ $pendingBookings->count() + $pendingMobileCases->count() }}</span></button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-meals-pane"><i class="bi bi-egg-fried me-1"></i> الوجبات والعهدة الغذائية @if($lowFoodStockCount > 0)<span class="badge bg-warning text-dark">{{ $lowFoodStockCount }}</span>@endif</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-petty-cash-pane"><i class="bi bi-cash-stack me-1"></i> العهدة المالية (النثريات)</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-assets-pane"><i class="bi bi-cpu me-1"></i> عهدة الأجهزة والمعدات @if($brokenAssetsCount > 0)<span class="badge bg-danger">{{ $brokenAssetsCount }}</span>@endif</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#gh-support-callcenter-pane"><i class="bi bi-headset me-1"></i> التواصل والدعم والكول سنتر @if($openTicketsCount > 0)<span class="badge bg-danger">{{ $openTicketsCount }}</span>@endif</button></li>
    </ul>

    <div class="tab-content">
        <div class="tab-pane fade show active" id="gh-beds-pane">
            <div class="d-flex flex-wrap gap-3 small mb-3">
                <span><i class="gh-status-dot" style="background:#16803c"></i> فارغ متاح</span>
                <span><i class="gh-status-dot" style="background:#c62828"></i> مقيم بالدار</span>
                <span><i class="gh-status-dot" style="background:#d97706"></i> خارج للعلاج (مؤقت)</span>
                <span><i class="gh-status-dot" style="background:#64748b"></i> صيانة</span>
            </div>
            @forelse($guestHouseWings as $wing)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <strong class="fs-6">{{ $wing->name }}</strong>
                            @php
                                $wStandard = $wing->beds->filter(fn($b) => !$b->isMattress())->count();
                                $wMattress = $wing->beds->filter(fn($b) => $b->isMattress())->count();
                            @endphp
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">
                                {{ $wing->beds->count() }} سرير
                                @if($wMattress > 0)
                                    <span class="text-primary fw-bold">({{ $wStandard }} أساسي + {{ $wMattress }} مرتبة إضافية)</span>
                                @endif
                            </span>
                            @if(!$wing->is_active)
                                <span class="badge bg-warning text-dark">معطل</span>
                            @endif
                        </div>
                        @if(!empty($isAdmin) || !empty($isAssigned) || auth()->user()?->hasRole(['manager', 'مدير دار الضيافة']))
                            <div class="d-flex flex-wrap gap-1">
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#opsEditWingModal_{{ $wing->id }}">
                                    <i class="bi bi-pencil-square me-1"></i> تعديل الجناح والأسرة
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-success" data-bs-toggle="modal" data-bs-target="#opsAddBedModal_{{ $wing->id }}">
                                    <i class="bi bi-plus-lg me-1"></i> إضافة سرير
                                </button>
                                @if($wing->beds->filter(fn($b) => $b->activeStay)->count() === 0)
                                    <form method="POST" action="{{ route('guest-houses.wings.destroy', [$guest_house, $wing]) }}" class="d-inline" onsubmit="return confirm('تأكيد حذف الجناح ({{ $wing->name }}) وكافة أسِرّته نهائياً؟');">
                                        @csrf 
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف الجناح">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="d-flex flex-wrap gap-2">
                        @foreach($wing->beds as $bed)
                            @php 
                                $occupied = (bool) $bed->activeStay; 
                                $stay = $bed->activeStay;
                                $isTempOut = (bool) ($stay?->is_temporary_out);
                                $patient = $stay?->beneficiary;
                                $patientProfile = $patient?->patientProfile;
                                $isMattress = $bed->isMattress();
                            @endphp

                            @if($occupied)
                                {{-- سرير محجوز (مقيم أو خارج للعلاج مؤقتاً) --}}
                                <button type="button" 
                                        class="gh-bed {{ $isTempOut ? 'gh-bed-temporary-out' : 'gh-bed-occupied' }} {{ $isMattress ? 'border border-warning border-2' : '' }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bedStayDetailsModal_{{ $bed->id }}" 
                                        title="{{ $isMattress ? 'مرتبة إضافية' : 'سرير' }} {{ $bed->number }} ({{ $isTempOut ? 'الحالة خارج للعلاج مؤقتاً' : 'مقيم بالدار' }})">
                                    <i class="bi {{ $isMattress ? 'bi-layers-fill text-warning' : ($isTempOut ? 'bi-clock-history' : 'bi-person-fill') }} fs-5"></i>
                                    <span>{{ $isMattress ? 'مرتبة' : 'سرير' }} {{ $bed->number }}</span>
                                    @if($isMattress)
                                        <span class="badge bg-warning text-dark py-0 px-1" style="font-size:0.62rem; font-weight:700;">مرتبة إضافية</span>
                                    @endif
                                    @if($isTempOut)
                                        <span class="badge bg-light text-dark py-0 px-1" style="font-size:0.65rem;">خارج للعلاج</span>
                                    @endif
                                    <small class="text-truncate px-1 d-inline-block" style="max-width:85px;">{{ $patient?->full_name }}</small>
                                </button>

                                {{-- مودال تفاصيل النزيل والإجراءات --}}
                                <div class="modal fade" id="bedStayDetailsModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow border-0">
                                            <div class="modal-header {{ $isTempOut ? 'bg-warning text-dark' : 'bg-danger text-white' }} py-2">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-hospital me-1"></i> تفاصيل النزيل — سرير {{ $bed->number }} ({{ $wing->name }})
                                                </h6>
                                                <button type="button" class="btn-close {{ $isTempOut ? '' : 'btn-close-white' }}" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                @if($isTempOut)
                                                    <div class="alert alert-warning py-2 px-3 mb-3 d-flex align-items-center gap-2">
                                                        <i class="bi bi-clock-history fs-4"></i>
                                                        <div>
                                                            <strong>تنبيه: الحالة في مغادرة مؤقتة (خارج للعلاج).</strong>
                                                            <div class="small">السرير يظل محجوزاً باسمه منذ: {{ $stay->temporary_out_at?->format('Y-m-d H:i') ?: '—' }}</div>
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="d-flex align-items-center gap-3 mb-3 p-2 bg-light rounded-3">
                                                    <div class="rounded-circle {{ $isTempOut ? 'bg-warning-subtle text-warning' : 'bg-danger-subtle text-danger' }} d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                                        <i class="bi bi-person-fill fs-4"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $patient?->full_name }}</h6>
                                                        <div class="text-muted small">كود النزيل: <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold">{{ $patient?->guest_code }}</span> | قومي: {{ $patient?->national_id ?: 'غير مسجل' }}</div>
                                                    </div>
                                                </div>

                                                <div class="row g-2 small mb-3">
                                                    <div class="col-6">
                                                        <div class="p-2 border rounded-2 bg-light-subtle">
                                                            <span class="text-muted d-block">الهاتف:</span>
                                                            <strong class="text-dark">{{ $patient?->phone ?: 'غير مسجل' }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 border rounded-2 bg-light-subtle">
                                                            <span class="text-muted d-block">تاريخ الوصول:</span>
                                                            <strong class="text-dark">{{ $stay?->arrival_date?->format('Y-m-d') ?: '—' }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 border rounded-2 bg-light-subtle">
                                                            <span class="text-muted d-block">مدة الإقامة:</span>
                                                            <strong class="text-dark">{{ $stay?->expected_days ? $stay->expected_days . ' أيام' : 'غير محدد' }}</strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="p-2 border rounded-2 bg-light-subtle">
                                                            <span class="text-muted d-block">نوع العلاج:</span>
                                                            <strong class="text-primary">
                                                                {{ $patientProfile?->treatment_type === 'chemotherapy' ? 'مريض كيماوي' : ($patientProfile?->treatment_type === 'radiation' ? 'مريض إشعاع' : ($patientProfile?->treatment_type ? 'أخرى' : 'غير محدد')) }}
                                                            </strong>
                                                        </div>
                                                    </div>
                                                    @if($patientProfile?->medical_center)
                                                        <div class="col-12">
                                                            <div class="p-2 border rounded-2 bg-light-subtle">
                                                                <span class="text-muted d-block">المستشفى / المركز:</span>
                                                                <strong class="text-dark">{{ $patientProfile->medical_center }}</strong>
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($patientProfile?->companion_name)
                                                        <div class="col-12">
                                                            <div class="p-2 border rounded-2 bg-light-subtle">
                                                                <span class="text-muted d-block">المرافق:</span>
                                                                <strong class="text-dark">{{ $patientProfile->companion_name }}</strong>
                                                                @if($patientProfile->companion_relation) <span class="badge bg-secondary-subtle text-secondary ms-1">{{ $patientProfile->companion_relation }}</span> @endif
                                                                @if($patientProfile->companion_phone) <span class="text-muted ms-1">({{ $patientProfile->companion_phone }})</span> @endif
                                                            </div>
                                                        </div>
                                                    @endif
                                                    @if($patientProfile?->manager_notes)
                                                        <div class="col-12">
                                                            <div class="p-2 border rounded-2 bg-warning-subtle text-dark">
                                                                <span class="fw-bold d-block text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill me-1"></i> ملاحظات وسلبيات الحالة:</span>
                                                                <div>{{ $patientProfile->manager_notes }}</div>
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="modal-footer py-2 d-flex flex-wrap justify-content-between gap-2">
                                                <div class="d-flex gap-2">
                                                    @if($isTempOut)
                                                        <form method="POST" action="{{ route('guest-houses.stays.returnTemporary', [$guest_house, $stay]) }}">
                                                            @csrf
                                                            <button type="submit" class="btn btn-success btn-sm">
                                                                <i class="bi bi-arrow-return-right me-1"></i> تسجيل عودة للسرير
                                                            </button>
                                                        </form>
                                                    @else
                                                        <form method="POST" action="{{ route('guest-houses.stays.temporaryDepart', [$guest_house, $stay]) }}" onsubmit="return confirm('تسجيل خروج مؤقت لتلقي العلاج بالمستشفى؟ السرير يظل محجوزاً باسمه.');">
                                                            @csrf
                                                            <button type="submit" class="btn btn-warning text-dark btn-sm">
                                                                <i class="bi bi-clock-history me-1"></i> مغادرة مؤقتة (خارج للعلاج)
                                                            </button>
                                                        </form>
                                                    @endif

                                                    <form method="POST" action="{{ route('guest-houses.stays.depart', [$guest_house, $stay]) }}" onsubmit="return confirm('تأكيد مغادرة الحالة وإخلاء السرير نهائياً؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-outline-danger btn-sm">
                                                            <i class="bi bi-box-arrow-left me-1"></i> مغادرة نهائية وإخلاء السرير
                                                        </button>
                                                    </form>
                                                </div>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @elseif($bed->status === 'maintenance')
                                {{-- سرير في الصيانة: الضغط عليه يعرض خيارات إعادة التفعيل أو التعديل أو الحذف --}}
                                <button type="button" 
                                        class="gh-bed gh-bed-maintenance" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bedMaintenanceModal_{{ $bed->id }}" 
                                        title="سرير تحت الصيانة: اضغط للخيارات">
                                    <i class="bi bi-tools fs-5"></i>
                                    <span>سرير {{ $bed->number }}</span>
                                    <small>صيانة (اضغط للخيارات)</small>
                                </button>

                                {{-- مودال سرير الصيانة --}}
                                <div class="modal fade" id="bedMaintenanceModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content shadow border-0 text-center">
                                            <div class="modal-header bg-secondary text-white py-2 justify-content-center position-relative">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-tools me-1"></i> {{ $wing->name }} — سرير {{ $bed->number }}
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-2" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <p class="text-muted small mb-3">السرير حالياً في وضع الصيانة. اختر الإجراء:</p>
                                                <div class="d-grid gap-2">
                                                    <form method="POST" action="{{ route('guest-houses.beds.status', [$guest_house, $bed]) }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="available">
                                                        <button type="submit" class="btn btn-success fw-bold w-100 py-2">
                                                            <i class="bi bi-check2-circle me-1"></i> إعادة تفعيل السرير (متاح)
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-outline-primary w-100 py-2" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#opsEditBedModal_{{ $bed->id }}">
                                                        <i class="bi bi-pencil me-1"></i> تعديل رقم السرير وملاحظاته
                                                    </button>
                                                    <form method="POST" action="{{ route('guest-houses.beds.destroy', [$guest_house, $bed]) }}" onsubmit="return confirm('تأكيد حذف السرير ({{ $bed->number }}) نهائياً؟');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger w-100 py-2">
                                                            <i class="bi bi-trash me-1"></i> حذف السرير
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                {{-- سرير متاح: الضغط عليه يعرض خيارات التسكين أو الصيانة أو التعديل أو الحذف --}}
                                @php
                                    $isMattress = $bed->isMattress();
                                @endphp
                                <button type="button" 
                                        class="gh-bed gh-bed-available {{ $isMattress ? 'border border-warning border-2' : '' }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bedOptionsModal_{{ $bed->id }}" 
                                        title="{{ $isMattress ? 'مرتبة إضافية متاحة' : 'سرير متاح' }}: اضغط لاختيار التسكين أو الصيانة أو التعديل">
                                    <i class="bi {{ $isMattress ? 'bi-layers-fill text-warning' : 'bi-hospital-bed' }} fs-5"></i>
                                    <span>{{ $isMattress ? 'مرتبة' : 'سرير' }} {{ $bed->number }}</span>
                                    @if($isMattress)
                                        <span class="badge bg-warning text-dark py-0 px-1" style="font-size:0.62rem; font-weight:700;">مرتبة إضافية</span>
                                    @else
                                        <small>فارغ (متاح)</small>
                                    @endif
                                </button>

                                {{-- مودال خيارات السرير المتاح --}}
                                <div class="modal fade" id="bedOptionsModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content shadow border-0 text-center">
                                            <div class="modal-header bg-success text-white py-2 justify-content-center position-relative">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-hospital-bed me-1"></i> {{ $wing->name }} — {{ $isMattress ? 'مرتبة' : 'سرير' }} {{ $bed->number }}
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <p class="text-muted small mb-3">{{ $isMattress ? 'المرتبة الإضافية متاحة' : 'السرير متاح' }} حالياً للحجز. اختر الإجراء المطلوب:</p>
                                                <div class="d-grid gap-2">
                                                    <button type="button" class="btn btn-primary fw-bold py-2" onclick="openBeneficiaryModalWithBed('{{ $bed->id }}', '{{ $wing->name }} — {{ $isMattress ? 'مرتبة' : 'سرير' }} {{ $bed->number }}')">
                                                        <i class="bi bi-person-plus-fill me-1"></i> تسكين مريض (جديد أو مسجل مسبقاً)
                                                    </button>
                                                    <form method="POST" action="{{ route('guest-houses.beds.status', [$guest_house, $bed]) }}">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="maintenance">
                                                        <button type="submit" class="btn btn-outline-secondary w-100 py-2" onclick="return confirm('تحويل السرير لوضع الصيانة؟');">
                                                            <i class="bi bi-tools me-1"></i> تحويل لوضع الصيانة
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-outline-primary w-100 py-2" data-bs-dismiss="modal" data-bs-toggle="modal" data-bs-target="#opsEditBedModal_{{ $bed->id }}">
                                                        <i class="bi bi-pencil me-1"></i> تعديل السرير / المرتبة والملاحظات
                                                    </button>
                                                    <form method="POST" action="{{ route('guest-houses.beds.destroy', [$guest_house, $bed]) }}" onsubmit="return confirm('تأكيد حذف {{ $isMattress ? 'المرتبة' : 'السرير' }} ({{ $bed->number }}) نهائياً؟');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger w-100 py-2">
                                                            <i class="bi bi-trash me-1"></i> حذف {{ $isMattress ? 'المرتبة' : 'السرير' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif

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
                    <small class="text-muted">متابعة النزلاء الحاليين، المرافقين، ملف الحالة، وسجل الملاحظات والإقامة</small>
                </div>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-1 font-monospace">
                    {{ $residentStays->count() }} حالة مقيمة حالياً
                </span>
            </div>

            <div class="table-responsive mb-4">
                <table class="table align-middle table-hover border">
                    <thead class="table-light">
                        <tr>
                            <th>المستفيد (المريض)</th>
                            <th>المرافق</th>
                            <th>الجناح / السرير</th>
                            <th>تاريخ الوصول / المدة</th>
                            <th>سجل وتفاصيل الحالة</th>
                            <th>ملاحظات وسلبيات</th>
                            <th>إجراء المغادرة</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($residentStays as $stay)
                        @php
                            $patientProfile = $stay->beneficiary->patientProfile;
                            $hasNotes = !empty($patientProfile?->manager_notes);
                            $companionName = $patientProfile?->companion_name;
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $stay->beneficiary->full_name }}</strong>
                                <div class="small text-muted font-monospace">{{ $stay->beneficiary->phone ?: 'بدون هاتف' }}</div>
                                <div class="mt-1 d-flex align-items-center gap-1">
                                    <a href="{{ route('beneficiaries.show', $stay->beneficiary) }}" target="_blank" class="badge bg-primary text-white text-decoration-none py-1 px-2" title="فتح الملف الشامل للحالة">
                                        <i class="bi bi-person-vcard me-1"></i> ملف المستفيد ({{ $stay->beneficiary->code ?? '—' }})
                                    </a>
                                </div>
                            </td>
                            <td>
                                @if(!empty($companionName))
                                    <div class="fw-bold text-dark small"><i class="bi bi-person me-1 text-primary"></i>{{ $companionName }}</div>
                                    @if($patientProfile?->companion_relation)
                                        <span class="badge bg-secondary-subtle text-secondary small py-0 px-1">{{ $patientProfile->companion_relation }}</span>
                                    @endif
                                    @if($patientProfile?->companion_phone)
                                        <div class="small text-muted font-monospace"><a href="tel:{{ $patientProfile->companion_phone }}" class="text-decoration-none text-muted"><i class="bi bi-telephone text-success me-1"></i>{{ $patientProfile->companion_phone }}</a></div>
                                    @endif
                                @else
                                    <span class="text-muted small">بدون مرافق</span>
                                @endif
                                @if(!empty($patientProfile?->companion_history) && is_array($patientProfile->companion_history))
                                    <div class="mt-1">
                                        <span class="badge bg-info-subtle text-info border border-info-subtle small" title="يوجد سجل مرافقين سابقين محفوظ">
                                            <i class="bi bi-clock-history me-1"></i> {{ count($patientProfile->companion_history) }} مرافق سابق
                                        </span>
                                    </div>
                                @endif
                                <div class="mt-1">
                                    <button type="button" class="btn btn-xs btn-outline-primary rounded-pill py-0 px-2" data-bs-toggle="modal" data-bs-target="#updateCompanionModal_{{ $stay->id }}" title="تحديث بيانات المرافق مع الاحتفاظ ببيانات المرافق السابق">
                                        <i class="bi bi-person-gear me-1"></i> تحديث المرافق
                                    </button>
                                </div>

                                {{-- Modal: تحديث بيانات المرافق مع حفظ المرافق السابق --}}
                                <div class="modal fade" id="updateCompanionModal_{{ $stay->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <form class="modal-content shadow border-0" method="POST" enctype="multipart/form-data" action="{{ route('guest-houses.beneficiaries.update-companion', [$guest_house, $stay->beneficiary]) }}">
                                            @csrf
                                            <div class="modal-header bg-success text-white py-2">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-person-badge me-1"></i> تحديث بيانات المرافق للحالة: {{ $stay->beneficiary->full_name }} ({{ $stay->beneficiary->code ?? '—' }})
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3 text-start">
                                                <div class="alert alert-info py-2 small mb-3">
                                                    <i class="bi bi-shield-check me-1"></i> عند تغيير اسم أو بيانات المرافق، يقوم النظام تلقائياً <strong>بأرشفة وحفظ بيانات المرافق السابق وصور بطاقته</strong> في سجل المرافقين السابقين للحالة دون حذفها.
                                                </div>
                                                <div class="row g-3">
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">اسم المرافق الجديد / الحالي رباعي <span class="text-danger">*</span></label>
                                                        <input name="companion_name" value="{{ $patientProfile?->companion_name }}" class="form-control" required placeholder="اسم المرافق كاملاً">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">صلة القرابة</label>
                                                        <input name="companion_relation" value="{{ $patientProfile?->companion_relation }}" class="form-control" placeholder="مثال: زوج / زوجة / ابن / والد / أخ...">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">الرقم القومي للمرافق</label>
                                                        <input name="companion_national_id" value="{{ $patientProfile?->companion_national_id }}" maxlength="14" class="form-control font-monospace" placeholder="14 رقم">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small">موبايل المرافق</label>
                                                        <input name="companion_phone" value="{{ $patientProfile?->companion_phone }}" class="form-control font-monospace" placeholder="01xxxxxxxxx">
                                                    </div>
                                                    <div class="col-12">
                                                        <label class="form-label fw-bold small">محل إقامة المرافق</label>
                                                        <input name="companion_address" value="{{ $patientProfile?->companion_address }}" class="form-control" placeholder="المحافظة - المركز - العنوان">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small"><i class="bi bi-camera-fill text-primary me-1"></i> صورة بطاقة المرافق (وجه)</label>
                                                        <input type="file" name="companion_id_front" accept="image/*" class="form-control form-control-sm">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <label class="form-label fw-bold small"><i class="bi bi-camera-fill text-primary me-1"></i> صورة بطاقة المرافق (ظهر)</label>
                                                        <input type="file" name="companion_id_back" accept="image/*" class="form-control form-control-sm">
                                                    </div>
                                                </div>

                                                @if(!empty($patientProfile?->companion_history) && is_array($patientProfile->companion_history))
                                                    <div class="mt-3 pt-3 border-top">
                                                        <h6 class="fw-bold text-secondary small mb-2"><i class="bi bi-clock-history me-1"></i> سجل المرافقين السابقين المحفوظين للحالة:</h6>
                                                        <div class="table-responsive">
                                                            <table class="table table-sm table-bordered small mb-0">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>اسم المرافق السابق</th>
                                                                        <th>القرابة</th>
                                                                        <th>الرقم القومي</th>
                                                                        <th>الهاتف</th>
                                                                        <th>تاريخ الأرشفة</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach(array_reverse($patientProfile->companion_history) as $histComp)
                                                                        <tr>
                                                                            <td class="fw-bold">{{ $histComp['companion_name'] ?? '—' }}</td>
                                                                            <td>{{ $histComp['companion_relation'] ?? '—' }}</td>
                                                                            <td class="font-monospace">{{ $histComp['companion_national_id'] ?? '—' }}</td>
                                                                            <td class="font-monospace">{{ $histComp['companion_phone'] ?? '—' }}</td>
                                                                            <td class="text-muted font-monospace">{{ isset($histComp['archived_at']) ? \Carbon\Carbon::parse($histComp['archived_at'])->format('Y-m-d H:i') : '—' }}</td>
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer py-2 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                                                    <i class="bi bi-check-lg me-1"></i> حفظ بيانات المرافق (مع أرشفة السابق)
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $stay->is_temporary_out ? 'bg-warning-subtle text-warning border border-warning' : 'bg-primary-subtle text-primary border' }} px-2 py-1">
                                    {{ $stay->bed?->wing?->name ?: '—' }} / سرير {{ $stay->bed?->number ?: '—' }}
                                </span>
                                @if($stay->is_temporary_out)
                                    <div class="small text-warning fw-bold mt-1"><i class="bi bi-clock-history me-1"></i> خارج للعلاج</div>
                                @endif
                            </td>
                            <td>
                                <div>{{ $stay->arrival_date?->format('Y-m-d') }}</div>
                                <div class="small text-muted">{{ $stay->expected_days ? $stay->expected_days . ' أيام مقررة' : 'غير محدد' }}</div>
                                <div class="mt-0.5">
                                    <span class="badge bg-light text-primary border" style="font-size: 0.72rem;">
                                        حتى الآن: {{ $stay->actual_days }} يوم
                                    </span>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1 fw-bold text-dark" data-bs-toggle="modal" data-bs-target="#residentStayModal_{{ $stay->id }}" title="عرض البيانات الطبية، المرافق، وتفاصيل الإقامة">
                                    <i class="bi bi-info-circle me-1 text-primary"></i> تفاصيل الحالة
                                </button>

                                {{-- Resident Stay Full Details Modal --}}
                                <div class="modal fade" id="residentStayModal_{{ $stay->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content shadow border-0">
                                            <div class="modal-header bg-primary text-white py-2">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-person-vcard me-1"></i> كشف بيانات النزيل: {{ $stay->beneficiary->full_name }} ({{ $stay->beneficiary->code ?? '—' }})
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                {{-- Row 1: Basic Info --}}
                                                <div class="card border rounded-3 mb-3 p-3 bg-light-subtle">
                                                    <h6 class="fw-bold text-primary mb-2 border-bottom pb-1"><i class="bi bi-person-lines-fill me-1"></i> البيانات الشخصية للمستفيد</h6>
                                                    <div class="row g-2 small">
                                                        <div class="col-sm-6 col-md-4"><strong>الاسم الكامل:</strong> {{ $stay->beneficiary->full_name }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>الرقم القومي:</strong> <span class="font-monospace">{{ $stay->beneficiary->national_id ?: '—' }}</span></div>
                                                        <div class="col-sm-6 col-md-4"><strong>رقم الهاتف:</strong> <span class="font-monospace">{{ $stay->beneficiary->phone ?: '—' }}</span></div>
                                                        <div class="col-sm-6 col-md-4"><strong>السن / الميلاد:</strong> {{ $stay->beneficiary->age ? $stay->beneficiary->age . ' سنة' : ($stay->beneficiary->birth_date?->format('Y-m-d') ?: '—') }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>المحافظة / العنوان:</strong> {{ $stay->beneficiary->address ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>التصنيف:</strong> <span class="badge bg-secondary">{{ $stay->beneficiary->classification ?: 'A' }}</span></div>
                                                    </div>
                                                </div>

                                                {{-- Row 2: Companion Info --}}
                                                <div class="card border rounded-3 mb-3 p-3 bg-light-subtle">
                                                    <h6 class="fw-bold text-success mb-2 border-bottom pb-1"><i class="bi bi-people-fill me-1"></i> بيانات المرافق</h6>
                                                    @if(!empty($patientProfile?->companion_name))
                                                        <div class="row g-2 small">
                                                            <div class="col-sm-6 col-md-4"><strong>اسم المرافق:</strong> {{ $patientProfile->companion_name }}</div>
                                                            <div class="col-sm-6 col-md-4"><strong>صلة القرابة:</strong> {{ $patientProfile->companion_relation ?: '—' }}</div>
                                                            <div class="col-sm-6 col-md-4"><strong>الرقم القومي للمرافق:</strong> <span class="font-monospace">{{ $patientProfile->companion_national_id ?: '—' }}</span></div>
                                                            <div class="col-sm-6 col-md-4"><strong>هاتف المرافق:</strong> <span class="font-monospace">{{ $patientProfile->companion_phone ?: '—' }}</span></div>
                                                            <div class="col-sm-6 col-md-8"><strong>عنوان المرافق:</strong> {{ $patientProfile->companion_address ?: '—' }}</div>
                                                        </div>
                                                        @if($patientProfile->companion_id_front_path || $patientProfile->companion_id_back_path)
                                                            <div class="mt-2 pt-2 border-top d-flex gap-2">
                                                                @if($patientProfile->companion_id_front_path)
                                                                    <a href="{{ Storage::disk('public')->url($patientProfile->companion_id_front_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0">
                                                                        <i class="bi bi-card-image me-1"></i> بطاقة المرافق (وجه)
                                                                    </a>
                                                                @endif
                                                                @if($patientProfile->companion_id_back_path)
                                                                    <a href="{{ Storage::disk('public')->url($patientProfile->companion_id_back_path) }}" target="_blank" class="btn btn-sm btn-outline-secondary py-0">
                                                                        <i class="bi bi-card-image me-1"></i> بطاقة المرافق (ظهر)
                                                                    </a>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="text-muted small">لا يوجد مرافق مسجل مع الحالة في هذه الإقامة.</div>
                                                    @endif
                                                    @if(!empty($patientProfile?->companion_history) && is_array($patientProfile->companion_history))
                                                        <div class="mt-2 pt-2 border-top">
                                                            <strong class="small text-secondary d-block mb-1"><i class="bi bi-clock-history me-1"></i> سجل المرافقين السابقين للحالة ({{ count($patientProfile->companion_history) }}):</strong>
                                                            <div class="d-flex flex-wrap gap-2">
                                                                @foreach(array_reverse($patientProfile->companion_history) as $histComp)
                                                                    <span class="badge bg-white text-dark border px-2 py-1">
                                                                        {{ $histComp['companion_name'] ?? '—' }}
                                                                        @if(!empty($histComp['companion_relation'])) ({{ $histComp['companion_relation'] }}) @endif
                                                                        @if(!empty($histComp['companion_phone'])) — {{ $histComp['companion_phone'] }} @endif
                                                                    </span>
                                                                @endforeach
                                                            </div>
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Row 3: Medical & Stay Details --}}
                                                <div class="card border rounded-3 mb-3 p-3 bg-light-subtle">
                                                    <h6 class="fw-bold text-info-emphasis mb-2 border-bottom pb-1"><i class="bi bi-hospital me-1"></i> البيانات الطبية ومتابعة العلاج والإقامة</h6>
                                                    <div class="row g-2 small">
                                                        <div class="col-sm-6 col-md-4"><strong>نوع العلاج:</strong> {{ $patientProfile?->treatment_type ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>المستشفى / المركز:</strong> {{ $patientProfile?->medical_center ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>عدد الجلسات:</strong> {{ $patientProfile?->sessions_count ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>الجناح والسرير:</strong> {{ $stay->bed?->wing?->name ?: '—' }} / سرير {{ $stay->bed?->number ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>تاريخ الوصول:</strong> {{ $stay->arrival_date?->format('Y-m-d') }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>المدة المتوقعة:</strong> {{ $stay->expected_days ?: '—' }} يوم</div>
                                                        @if($patientProfile?->medical_notes)
                                                            <div class="col-12 mt-1"><strong>ملاحظات طبية:</strong> {{ $patientProfile->medical_notes }}</div>
                                                        @endif
                                                    </div>
                                                    @if($patientProfile?->followup_card_path || $patientProfile?->referral_letter_path)
                                                        <div class="mt-2 pt-2 border-top d-flex gap-2">
                                                            @if($patientProfile->followup_card_path)
                                                                <a href="{{ Storage::disk('public')->url($patientProfile->followup_card_path) }}" target="_blank" class="btn btn-sm btn-outline-info py-0">
                                                                    <i class="bi bi-file-medical me-1"></i> كارت المتابعة
                                                                </a>
                                                            @endif
                                                            @if($patientProfile->referral_letter_path)
                                                                <a href="{{ Storage::disk('public')->url($patientProfile->referral_letter_path) }}" target="_blank" class="btn btn-sm btn-outline-warning py-0">
                                                                    <i class="bi bi-file-earmark-text me-1"></i> خطاب التحويل
                                                                </a>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>

                                                {{-- Row 4: Manager Notes --}}
                                                @if($hasNotes)
                                                    <div class="alert alert-warning py-2 mb-0">
                                                        <strong class="d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i> ملاحظات وسلبيات مدونة من إدارة الدار:</strong>
                                                        <span class="small">{{ $patientProfile->manager_notes }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer py-2 d-flex justify-content-between">
                                                <a href="{{ route('beneficiaries.show', $stay->beneficiary) }}" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> فتح ملف الحالة الشامل بالمستفيدين
                                                </a>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm {{ $hasNotes ? 'btn-warning text-dark' : 'btn-outline-secondary' }} rounded-pill px-2 py-1"
                                        data-bs-toggle="modal" data-bs-target="#managerNotesModal{{ $stay->id }}" title="ملاحظات وسلبيات الحالة">
                                    <i class="bi {{ $hasNotes ? 'bi-journal-check' : 'bi-journal-text' }} me-1"></i>
                                    {{ $hasNotes ? 'يوجد ملاحظات' : 'إضافة ملاحظات' }}
                                </button>

                                <div class="modal fade" id="managerNotesModal{{ $stay->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content shadow border-0">
                                            <div class="modal-header bg-warning text-dark py-2">
                                                <h6 class="modal-title fw-bold"><i class="bi bi-shield-exclamation me-1"></i> ملاحظات مدير الدار (مشاكل وسلبيات) — {{ $stay->beneficiary->full_name }}</h6>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <form method="POST" action="{{ route('guest-houses.beneficiaries.saveNotes', [$guest_house, $stay->beneficiary]) }}">
                                                @csrf
                                                <div class="modal-body">
                                                    <p class="text-muted small mb-2">تدوين أي تجاوزات، مشاكل، سلوكيات، أو ملاحظات خاصة بالحالة أو المرافق أثناء الإقامة بالدار.</p>
                                                    <textarea name="manager_notes" class="form-control" rows="5" placeholder="اكتب الملاحظات والمشاكل هنا...">{{ old('manager_notes', $patientProfile?->manager_notes) }}</textarea>
                                                </div>
                                                <div class="modal-footer py-2">
                                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                    <button type="submit" class="btn btn-primary btn-sm px-3 fw-bold"><i class="bi bi-save me-1"></i> حفظ الملاحظات</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="d-flex flex-wrap gap-1">
                                    @if($stay->is_temporary_out)
                                        <form method="POST" action="{{ route('guest-houses.stays.returnTemporary', [$guest_house, $stay]) }}">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm rounded-pill px-2 fw-bold" title="تسجيل العودة للدار وتواجد بالسرير">
                                                <i class="bi bi-arrow-return-right me-1"></i> عودة للسرير
                                            </button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('guest-houses.stays.temporaryDepart', [$guest_house, $stay]) }}" onsubmit="return confirm('تسجيل خروج مؤقت للحالة لتلقي العلاج بالمستشفى؟ السرير يظل محجوزاً باسمه.');">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-warning text-dark btn-sm rounded-pill px-2 fw-bold" title="مغادرة مؤقتة (خارج للعلاج)">
                                                <i class="bi bi-clock-history me-1"></i> خروج للعلاج
                                            </button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('guest-houses.stays.depart', [$guest_house, $stay]) }}" onsubmit="return confirm('تأكيد مغادرة الحالة وإخلاء السرير نهائياً؟')">
                                        @csrf
                                        <button type="submit" class="btn btn-outline-danger btn-sm rounded-pill px-2 fw-bold" title="إنهاء الإقامة وإخلاء السرير">
                                            <i class="bi bi-box-arrow-left me-1"></i> إخلاء السرير
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty <tr><td colspan="7" class="text-center text-muted py-3">لا توجد حالات مقيمة حاليًا بالدار.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
                <div>
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-arrow-repeat me-1"></i> آخر الحالات المغادرة — الاطلاع على السجل وتسجيل عودة الحالة للدار</h6>
                    <small class="text-muted">الاطلاع الكامل على سجل إقامات النزلاء السابقين، المرافقين، وإمكانية إعادة التسكين مباشرة</small>
                </div>
                <span class="badge bg-secondary-subtle text-secondary font-monospace px-3 py-1">{{ $departedStays->count() }} حالة مغادرة</span>
            </div>

            <div class="table-responsive">
                <table class="table align-middle table-hover border">
                    <thead class="table-light">
                        <tr>
                            <th>المستفيد (المريض)</th>
                            <th>المرافق</th>
                            <th>تاريخ المغادرة</th>
                            <th>سجل وتفاصيل الإقامة</th>
                            <th>سرير العودة</th>
                            <th>الوصول / الأيام</th>
                            <th>إجراء التسكين</th>
                        </tr>
                    </thead>
                    <tbody>
                    @forelse($departedStays as $stay)
                        @php
                            $depProfile = $stay->beneficiary->patientProfile;
                        @endphp
                        <tr>
                            <td>
                                <strong class="text-dark">{{ $stay->beneficiary->full_name }}</strong>
                                <div class="small text-muted font-monospace">{{ $stay->beneficiary->phone ?: 'بدون هاتف' }}</div>
                                <a href="{{ route('beneficiaries.show', $stay->beneficiary) }}" target="_blank" class="badge bg-primary text-white text-decoration-none mt-1 d-inline-block">
                                    <i class="bi bi-person-vcard me-1"></i> ملف المستفيد ({{ $stay->beneficiary->code ?? '—' }})
                                </a>
                            </td>
                            <td>
                                @if(!empty($depProfile?->companion_name))
                                    <div class="fw-bold text-dark small"><i class="bi bi-person me-1 text-primary"></i>{{ $depProfile->companion_name }}</div>
                                    @if($depProfile?->companion_relation)
                                        <span class="badge bg-secondary-subtle text-secondary small py-0 px-1">{{ $depProfile->companion_relation }}</span>
                                    @endif
                                @else
                                    <span class="text-muted small">بدون مرافق</span>
                                @endif
                            </td>
                            <td>
                                <div>{{ $stay->departed_at?->format('Y-m-d') }}</div>
                                <div class="small text-muted font-monospace">{{ $stay->departed_at?->format('H:i') }}</div>
                                <div class="mt-1">
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-0.5" title="المدة الفعلية المحتسبة">
                                        الإقامة الفعلية: {{ $stay->actual_days }} يوم
                                    </span>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-2.5 py-1 text-dark" data-bs-toggle="modal" data-bs-target="#departedStayModal_{{ $stay->id }}">
                                    <i class="bi bi-clock-history me-1 text-primary"></i> السجل والتفاصيل
                                </button>

                                {{-- Departed Stay Details Modal --}}
                                <div class="modal fade" id="departedStayModal_{{ $stay->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-lg">
                                        <div class="modal-content shadow border-0">
                                            <div class="modal-header bg-secondary text-white py-2">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-journal-medical me-1"></i> سجل إقامة المغادر: {{ $stay->beneficiary->full_name }}
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <div class="card border rounded-3 mb-3 p-3 bg-light-subtle">
                                                    <h6 class="fw-bold text-primary mb-2 border-bottom pb-1"><i class="bi bi-calendar-check me-1"></i> بيانات الإقامة المنتهية</h6>
                                                    <div class="row g-2 small">
                                                        <div class="col-sm-6 col-md-4"><strong>تاريخ الوصول:</strong> {{ $stay->arrival_date?->format('Y-m-d') }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>تاريخ المغادرة:</strong> {{ $stay->departed_at?->format('Y-m-d H:i') }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>الجناح والسرير:</strong> {{ $stay->bed?->wing?->name ?: '—' }} / سرير {{ $stay->bed?->number ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>المدة المقررة أولاً:</strong> {{ $stay->expected_days ?: '—' }} يوم</div>
                                                        <div class="col-sm-6 col-md-4"><strong>مدة الإقامة الفعلية المحتسبة:</strong> <span class="badge bg-success text-white px-2.5 py-1 fw-bold fs-6">{{ $stay->actual_days }} يوم</span></div>
                                                    </div>
                                                </div>

                                                <div class="card border rounded-3 mb-3 p-3 bg-light-subtle">
                                                    <h6 class="fw-bold text-success mb-2 border-bottom pb-1"><i class="bi bi-people me-1"></i> بيانات النزيل والمرافق</h6>
                                                    <div class="row g-2 small">
                                                        <div class="col-sm-6 col-md-4"><strong>الرقم القومي:</strong> <span class="font-monospace">{{ $stay->beneficiary->national_id ?: '—' }}</span></div>
                                                        <div class="col-sm-6 col-md-4"><strong>الهاتف:</strong> <span class="font-monospace">{{ $stay->beneficiary->phone ?: '—' }}</span></div>
                                                        <div class="col-sm-6 col-md-4"><strong>العنوان:</strong> {{ $stay->beneficiary->address ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>المرافق:</strong> {{ $depProfile?->companion_name ?: 'بدون مرافق' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>صلة القرابة:</strong> {{ $depProfile?->companion_relation ?: '—' }}</div>
                                                        <div class="col-sm-6 col-md-4"><strong>هاتف المرافق:</strong> <span class="font-monospace">{{ $depProfile?->companion_phone ?: '—' }}</span></div>
                                                    </div>
                                                </div>

                                                @if($depProfile?->manager_notes)
                                                    <div class="alert alert-warning py-2 mb-0">
                                                        <strong class="d-block mb-1"><i class="bi bi-shield-exclamation me-1"></i> ملاحظات وسلبيات الإقامة:</strong>
                                                        <span class="small">{{ $depProfile->manager_notes }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="modal-footer py-2 d-flex justify-content-between">
                                                <a href="{{ route('beneficiaries.show', $stay->beneficiary) }}" target="_blank" class="btn btn-primary btn-sm px-3 fw-bold">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> فتح ملف الحالة الشامل
                                                </a>
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <form method="POST" action="{{ route('guest-houses.stays.return', [$guest_house, $stay]) }}">
                                @csrf
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
                                        <i class="bi bi-box-arrow-in-right me-1"></i> تسكين مجدداً
                                    </button>
                                </td>
                            </form>
                        </tr>
                    @empty <tr><td colspan="7" class="text-center text-muted py-3">لا توجد حالات مغادرة مسجلة.</td></tr>
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
                        $savedServingsByBen = $savedMeal?->servings?->keyBy('beneficiary_id') ?? collect();
                    @endphp
                    <div class="col-lg-4">
                        <form class="border rounded-3 p-3 h-100 bg-light-subtle shadow-sm d-flex flex-column justify-content-between" method="POST" enctype="multipart/form-data" action="{{ route('guest-houses.meals.store', $guest_house) }}">
                            @csrf
                            <input type="hidden" name="meal_type" value="{{ $mealType }}">
                            <div>
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
                                        <label class="form-label small text-muted mb-0">تاريخ الوجبة</label>
                                        <input type="date" name="meal_date" value="{{ now()->toDateString() }}" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-5">
                                        <label class="form-label small text-muted mb-0">وقت التقديم</label>
                                        <input type="time" name="served_at" value="{{ $savedMeal?->served_at ? substr($savedMeal->served_at,0,5) : '' }}" class="form-control form-control-sm">
                                    </div>
                                </div>

                                {{-- Inventory & Expense Options (Accordion) --}}
                                <div class="accordion mb-2" id="mealOptionsAcc_{{ $mealType }}">
                                    <div class="accordion-item border rounded-2">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed py-1 px-2 small fw-bold text-muted bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#mealInvExp_{{ $mealType }}">
                                                <i class="bi bi-box-seam me-1 text-primary"></i> خصم من المخزون ومصروف (اختياري)
                                            </button>
                                        </h2>
                                        <div id="mealInvExp_{{ $mealType }}" class="accordion-collapse collapse p-2 bg-light">
                                            <div class="row g-1 mb-2">
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-0" style="font-size:0.75rem">المخزن</label>
                                                    <select name="warehouse_id" class="form-select form-select-sm">
                                                        <option value="">— بدون خصم —</option>
                                                        @foreach($guestHouseWarehouses ?? [] as $wh)
                                                            <option value="{{ $wh->id }}">{{ $wh->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-6">
                                                    <label class="form-label small text-muted mb-0" style="font-size:0.75rem">الصنف</label>
                                                    <select name="item_id" class="form-select form-select-sm">
                                                        <option value="">— اختر الصنف —</option>
                                                        @foreach($guestHouseItems ?? [] as $itm)
                                                            <option value="{{ $itm->id }}">{{ $itm->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-12 mt-1">
                                                    <label class="form-label small text-muted mb-0" style="font-size:0.75rem">الكمية المصروفة</label>
                                                    <input type="number" step="0.01" min="0" name="quantity_deducted" class="form-control form-control-sm" placeholder="مثلاً: 25">
                                                </div>
                                            </div>
                                            <div class="border-top pt-2">
                                                <div class="form-check mb-1">
                                                    <input class="form-check-input" type="checkbox" name="record_expense" value="1" id="recExp_{{ $mealType }}" onchange="document.getElementById('expDetails_{{ $mealType }}').style.display = this.checked ? 'block' : 'none'">
                                                    <label class="form-check-label small fw-bold text-danger" for="recExp_{{ $mealType }}" style="font-size:0.75rem">تسجيل تكلفة الوجبة كمصروف</label>
                                                </div>
                                                <div id="expDetails_{{ $mealType }}" style="display:none;" class="row g-1 mt-1">
                                                    <div class="col-6">
                                                        <select name="expense_treasury_id" class="form-select form-select-sm">
                                                            <option value="">— الخزينة —</option>
                                                            @foreach($guestHouseExpenseTreasuries ?? [] as $tr)
                                                                <option value="{{ $tr->id }}">{{ $tr->name }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <input type="number" step="0.01" min="0" name="expense_amount" class="form-control form-control-sm" placeholder="المبلغ (ج)">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="small fw-bold text-muted">المقيمون والمرافقون:</span>
                                    <div>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-primary small me-2" onclick="toggleAllMeals('{{ $mealType }}', true)">الكل</button>
                                        <button type="button" class="btn btn-link btn-sm p-0 text-secondary small" onclick="toggleAllMeals('{{ $mealType }}', false)">إلغاء</button>
                                    </div>
                                </div>

                                <div class="border rounded p-2 mb-2 bg-white" style="max-height:230px; overflow-y:auto">
                                    @forelse($residentStays as $stay)
                                        @php
                                            $srv = $savedServingsByBen->get($stay->beneficiary_id);
                                            $cNameVal = $srv?->companion_name ?: ($stay->beneficiary->patientProfile?->companion_name ?: '');
                                            $hasCompanion = \App\Models\GuestHouseMealServing::isRealCompanion($cNameVal);
                                            $pMealsVal = $srv ? $srv->patient_meals_count : 1;
                                            // وجبة المرافق تكون 1 افتراضياً فقط إذا كان للمريض مرافق فعلي، أما إذا كان بدون مرافق فتكون 0 قطعاً
                                            $cMealsVal = $hasCompanion ? ($srv ? $srv->companion_meals_count : 1) : 0;
                                        @endphp
                                        <div class="border-bottom pb-2 mb-2">
                                            <div class="form-check mb-1">
                                                <input class="form-check-input meal-chk-{{ $mealType }}" type="checkbox" name="received_beneficiary_ids[]" value="{{ $stay->beneficiary_id }}" id="meal-{{ $mealType }}-{{ $stay->beneficiary_id }}" onchange="handleMealCheckboxChange(this, '{{ $mealType }}', '{{ $stay->beneficiary_id }}', {{ $hasCompanion ? 1 : 0 }})" @checked(in_array((int)$stay->beneficiary_id, $received, true))>
                                                <label class="form-check-label small fw-bold" for="meal-{{ $mealType }}-{{ $stay->beneficiary_id }}">
                                                    {{ $stay->beneficiary->full_name }}
                                                    <span class="text-muted fw-normal" style="font-size:0.75rem">({{ $stay->bed?->wing?->name ?: 'جناح' }} - سرير {{ $stay->bed?->number ?: '—' }})</span>
                                                    @if(!$hasCompanion)
                                                        <span class="badge bg-secondary-subtle text-secondary py-0 px-1 ms-1" style="font-size:0.68rem"><i class="bi bi-person me-0.5"></i> بدون مرافق (وجبة واحدة)</span>
                                                    @else
                                                        <span class="badge bg-info-subtle text-info-emphasis py-0 px-1 ms-1" style="font-size:0.68rem"><i class="bi bi-people me-0.5"></i> مع مرافق (وجبتان)</span>
                                                    @endif
                                                </label>
                                            </div>
                                            <div class="row g-1 ps-4" style="font-size:0.8rem">
                                                <div class="col-4">
                                                    <label class="text-muted d-block" style="font-size:0.7rem">وجبة مريض</label>
                                                    <input type="number" min="0" max="5" name="servings[{{ $stay->beneficiary_id }}][patient_meals_count]" value="{{ $pMealsVal }}" class="form-control form-control-sm text-center py-0" style="height:26px;">
                                                </div>
                                                <div class="col-5">
                                                    <label class="text-muted d-block" style="font-size:0.7rem">اسم المرافق</label>
                                                    <input type="text" name="servings[{{ $stay->beneficiary_id }}][companion_name]" value="{{ $cNameVal }}" placeholder="المرافق (أو بدون)" oninput="handleCompanionNameInput(this, '{{ $stay->beneficiary_id }}')" class="form-control form-control-sm py-0" style="height:26px; font-size:0.75rem;">
                                                </div>
                                                <div class="col-3">
                                                    <label class="text-muted d-block" style="font-size:0.7rem">وجبة مرافق</label>
                                                    <input type="number" min="0" max="5" name="servings[{{ $stay->beneficiary_id }}][companion_meals_count]" value="{{ $cMealsVal }}" class="form-control form-control-sm text-center py-0" style="height:26px;">
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <span class="text-muted small">لا يوجد مقيمون حالياً.</span>
                                    @endforelse
                                </div>

                                <label class="form-label small fw-bold mb-1"><i class="bi bi-camera me-1"></i> إرفاق صورة {{ $mealLabel }}</label>
                                <input type="file" name="meal_image" accept="image/*" class="form-control form-control-sm mb-2">
                                @if($savedMeal?->image_url)
                                    <div class="d-flex align-items-center gap-2 mb-2 p-1 border rounded bg-white">
                                        <img src="{{ $savedMeal->image_url }}" alt="{{ $mealLabel }}" class="rounded" style="width: 44px; height: 38px; object-fit: cover;">
                                        <a href="{{ $savedMeal->image_url }}" target="_blank" class="small text-primary fw-bold">عرض صورة {{ $mealLabel }} المرفقة</a>
                                    </div>
                                @endif
                            </div>
                            <button class="btn btn-primary btn-sm w-100 rounded-pill fw-bold mt-2">حفظ كشف {{ $mealLabel }}</button>
                        </form>
                    </div>
                @endforeach
            </div>

            {{-- Meal History Archive (سجل كشوفات الوجبات السابقة) --}}
            <div class="mt-4 pt-3 border-top">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-journal-album text-primary me-1"></i> سجل كشوفات الوجبات السابقة والأرشيف</h6>
                        <small class="text-muted">أرشيف لجميع كشوفات الوجبات المسجلة، أسماء المستلمين والمرافقين، والصور المرفقة</small>
                    </div>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace px-3 py-1">{{ isset($pastMeals) ? $pastMeals->count() : 0 }} كشف مسجل</span>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle table-hover border">
                        <thead class="table-light">
                            <tr>
                                <th>التاريخ والنوع</th>
                                <th>وقت التقديم</th>
                                <th>عدد الوجبات المستلمة</th>
                                <th>خصم المخزون والمصروفات</th>
                                <th>المسؤول</th>
                                <th>صورة الوجبة</th>
                                <th>كشف المستلمين والمرافقين</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pastMeals ?? [] as $pMeal)
                                @php
                                    $pTypeLabel = match($pMeal->meal_type) {
                                        'breakfast' => '🍳 إفطار',
                                        'lunch' => '🍲 غداء',
                                        'dinner' => '🥪 عشاء',
                                        default => $pMeal->meal_type
                                    };
                                    $receivedServings = $pMeal->servings->where('received', true);
                                    $totalMeals = $receivedServings->sum(fn($s) => $s->total_meals_count ?: ($s->patient_meals_count + $s->companion_meals_count ?: 1));
                                    $pMealsSum = $receivedServings->sum(fn($s) => $s->patient_meals_count ?: 1);
                                    $cMealsSum = $receivedServings->sum('companion_meals_count');
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $pTypeLabel }}</strong>
                                        <div class="small text-muted font-monospace">{{ $pMeal->meal_date?->format('Y-m-d') ?: $pMeal->meal_date }}</div>
                                    </td>
                                    <td>{{ $pMeal->served_at ? substr($pMeal->served_at, 0, 5) : '—' }}</td>
                                    <td>
                                        <span class="badge bg-success text-white fs-6 fw-bold px-2 py-1">
                                            {{ $totalMeals }} وجبة
                                        </span>
                                        <div class="small text-muted mt-1 font-monospace">
                                            <span>مريض: {{ $pMealsSum }}</span> · <span>مرافق: {{ $cMealsSum }}</span>
                                        </div>
                                    </td>
                                    <td>
                                        @if($pMeal->warehouse && $pMeal->item)
                                            <div class="small text-dark mb-1">
                                                <i class="bi bi-box-seam text-info me-1"></i>
                                                <strong>{{ $pMeal->quantity_deducted }}</strong> {{ $pMeal->item->unit ?: 'وحدة' }} من {{ $pMeal->item->name }}
                                                <span class="text-muted d-block" style="font-size:0.75rem">مخزن: {{ $pMeal->warehouse->name }}</span>
                                            </div>
                                        @endif
                                        @if($pMeal->expense_recorded || !empty($pMeal->expense_amount))
                                            <div class="small text-danger fw-bold">
                                                <i class="bi bi-receipt me-1"></i>
                                                مصروف: {{ number_format((float)$pMeal->expense_amount, 2) }} ج
                                                @if($pMeal->treasury)
                                                    <span class="text-muted fw-normal d-block" style="font-size:0.75rem">خزينة: {{ $pMeal->treasury->name }}</span>
                                                @endif
                                            </div>
                                        @endif
                                        @if(!$pMeal->warehouse && !$pMeal->expense_recorded && empty($pMeal->expense_amount))
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">{{ $pMeal->creator?->name ?? 'النظام' }}</div>
                                    </td>
                                    <td>
                                        @if($pMeal->image_url)
                                            <a href="javascript:void(0)" data-bs-toggle="modal" data-bs-target="#mealPhotoModal_{{ $pMeal->id }}" class="d-inline-flex align-items-center gap-1 text-decoration-none">
                                                <img src="{{ $pMeal->image_url }}" alt="صورة الوجبة" class="rounded border shadow-xs" style="width: 44px; height: 38px; object-fit: cover;">
                                                <span class="badge bg-light text-primary border small"><i class="bi bi-zoom-in"></i> عرض</span>
                                            </a>

                                            {{-- Photo Preview Modal --}}
                                            <div class="modal fade" id="mealPhotoModal_{{ $pMeal->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div class="modal-content shadow border-0 text-center">
                                                        <div class="modal-header py-2 bg-dark text-white">
                                                            <h6 class="modal-title fw-bold">
                                                                <i class="bi bi-camera me-1"></i> صورة {{ $pTypeLabel }} — {{ $pMeal->meal_date?->format('Y-m-d') ?: $pMeal->meal_date }}
                                                            </h6>
                                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-2 bg-dark-subtle">
                                                            <img src="{{ $pMeal->image_url }}" alt="صورة الوجبة" class="img-fluid rounded shadow">
                                                        </div>
                                                        <div class="modal-footer py-1 justify-content-between">
                                                            <a href="{{ $pMeal->image_url }}" target="_blank" class="btn btn-sm btn-outline-primary"><i class="bi bi-box-arrow-up-right me-1"></i> فتح بالحجم الكامل</a>
                                                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-muted small">بدون صورة</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#mealArchiveModal_{{ $pMeal->id }}">
                                            <i class="bi bi-people me-1"></i> كشف المستلمين ({{ $totalMeals }} وجبة)
                                        </button>

                                        {{-- Modal for Attendance & Companions --}}
                                        <div class="modal fade" id="mealArchiveModal_{{ $pMeal->id }}" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-lg">
                                                <div class="modal-content shadow border-0">
                                                    <div class="modal-header bg-primary text-white py-2">
                                                        <h6 class="modal-title fw-bold">
                                                            <i class="bi bi-card-checklist me-1"></i> كشف استلام وجبات {{ $pTypeLabel }} — {{ $pMeal->meal_date?->format('Y-m-d') ?: $pMeal->meal_date }}
                                                        </h6>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body p-3">
                                                        {{-- Summary Badges --}}
                                                        <div class="d-flex flex-wrap gap-2 mb-3 p-2 bg-light rounded-3 border">
                                                            <span class="badge bg-success fs-6 py-1.5 px-3">إجمالي الوجبات: {{ $totalMeals }}</span>
                                                            <span class="badge bg-primary-subtle text-primary fs-6 py-1.5 px-3">وجبات المرضى: {{ $pMealsSum }}</span>
                                                            <span class="badge bg-warning-subtle text-warning-emphasis fs-6 py-1.5 px-3">وجبات المرافقين: {{ $cMealsSum }}</span>
                                                            <span class="badge bg-secondary-subtle text-secondary fs-6 py-1.5 px-3">الحالات المسجلة: {{ $receivedServings->count() }}</span>
                                                        </div>

                                                        @if($pMeal->image_url)
                                                            <div class="text-center mb-3">
                                                                <img src="{{ $pMeal->image_url }}" alt="صورة الوجبة" class="img-fluid rounded border shadow-sm" style="max-height: 220px;">
                                                            </div>
                                                        @endif
                                                        @if($pMeal->notes)
                                                            <div class="alert alert-info py-2 small mb-3"><strong>ملاحظات:</strong> {{ $pMeal->notes }}</div>
                                                        @endif
                                                        <div class="table-responsive">
                                                            <table class="table table-bordered table-sm align-middle">
                                                                <thead class="table-light">
                                                                    <tr>
                                                                        <th>#</th>
                                                                        <th>اسم المريض / النزيل</th>
                                                                        <th class="text-center">وجبة مريض</th>
                                                                        <th>اسم المرافق</th>
                                                                        <th class="text-center">وجبة مرافق</th>
                                                                        <th class="text-center">إجمالي الوجبات</th>
                                                                        <th>وقت الاستلام</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @forelse($receivedServings as $idx => $srv)
                                                                        <tr>
                                                                            <td>{{ $loop->iteration }}</td>
                                                                            <td><strong>{{ $srv->beneficiary?->full_name ?? '—' }}</strong></td>
                                                                            <td class="text-center font-monospace">{{ $srv->patient_meals_count ?: 1 }}</td>
                                                                            <td>{{ $srv->companion_name ?: '—' }}</td>
                                                                            <td class="text-center font-monospace">{{ $srv->companion_meals_count ?: 0 }}</td>
                                                                            <td class="text-center fw-bold text-success font-monospace">{{ $srv->total_meals_count ?: ($srv->patient_meals_count + $srv->companion_meals_count ?: 1) }}</td>
                                                                            <td class="small text-muted font-monospace">{{ $srv->received_at ? \Carbon\Carbon::parse($srv->received_at)->format('H:i') : '—' }}</td>
                                                                        </tr>
                                                                    @empty
                                                                        <tr><td colspan="7" class="text-center text-muted py-2">لم يتم تسجيل استلام لأي نزيل في هذا الكشف.</td></tr>
                                                                    @endforelse
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer py-2">
                                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="text-center text-muted py-3">لا توجد كشوفات وجبات سابقة مسجلة.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- ========================================================
                     المحور الأول: العهدة الغذائية والمؤن (Food & Nutrition Custody)
                     ======================================================== --}}
                <div class="mt-4 pt-3 border-top">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div>
                            <h6 class="fw-bold mb-1 text-primary">
                                <i class="bi bi-basket3-fill me-1"></i> العهدة الغذائية ومخزون المؤن والمطبخ
                            </h6>
                            <small class="text-muted">متابعة الأصناف التموينية، الألبان، المعلبات، والخضروات وربطها باستهلاك وجبات النزلاء اليومية</small>
                        </div>
                        <div class="d-flex flex-wrap gap-2">
                            <button class="btn btn-sm btn-success text-white fw-bold" data-bs-toggle="modal" data-bs-target="#guestHouseSupplyModal">
                                <i class="bi bi-box-arrow-in-down me-1"></i> توريد مواد غذائية
                            </button>
                            <button class="btn btn-sm btn-info text-white fw-bold" data-bs-toggle="modal" data-bs-target="#guestHouseIssueModal">
                                <i class="bi bi-box-arrow-up me-1"></i> صرف لمطبخ الدار
                            </button>
                        </div>
                    </div>

                    {{-- Low Stock Smart Alert Strip --}}
                    @php
                        $lowStockItems = $guestHouseInventory->filter(fn($stock) => (float)$stock->stock <= 5);
                    @endphp
                    @if($lowStockItems->isNotEmpty())
                        <div class="alert alert-warning border-warning-subtle shadow-xs d-flex align-items-start gap-3 py-2.5 px-3 mb-3">
                            <div class="fs-4 text-warning-emphasis"><i class="bi bi-exclamation-triangle-fill"></i></div>
                            <div class="flex-grow-1">
                                <strong class="d-block text-warning-emphasis mb-1">تنبيه ذكي: أصناف قاربت على النفاد (حد إعادة الطلب)</strong>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($lowStockItems as $lItem)
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-1">
                                            {{ $lItem->item?->name }}: متبقي {{ number_format((float)$lItem->stock, 1) }} {{ $lItem->item?->unit }} فقط!
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Categories Guide Pills --}}
                    <div class="row g-2 mb-3 small">
                        <div class="col-md-4">
                            <div class="p-2 rounded-3 border bg-light d-flex align-items-center gap-2">
                                <span class="fs-5">🌾</span>
                                <div>
                                    <strong class="d-block">مواد جافة وتموينية</strong>
                                    <span class="text-muted">أرز، مكرونة، سكر، زيت، شاي، بقوليات</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 rounded-3 border bg-light d-flex align-items-center gap-2">
                                <span class="fs-5">🧀</span>
                                <div>
                                    <strong class="d-block">ألبان وجبن ومعلبات</strong>
                                    <span class="text-muted">جبنة، زبادي، حليب، تونة، فول</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-2 rounded-3 border bg-light d-flex align-items-center gap-2">
                                <span class="fs-5">🥩</span>
                                <div>
                                    <strong class="d-block">طازج وخضروات ولحوم</strong>
                                    <span class="text-muted">مستلزمات الطهي وإعداد الوجبات الفورية</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Food Inventory Table --}}
                    <div class="row g-3">
                        <div class="col-lg-7">
                            <div class="border rounded-3 bg-white p-3 shadow-xs h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-boxes me-1 text-primary"></i> أرصدة المؤن والمواد الغذائية بالمخزن</h6>
                                    <span class="badge bg-primary-subtle text-primary font-monospace">{{ $guestHouseInventory->count() }} صنف مسجل</span>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-hover table-sm align-middle mb-0">
                                        <thead class="table-light small">
                                            <tr>
                                                <th>الصنف الغذائي</th>
                                                <th>المخزن / العهدة</th>
                                                <th class="text-center">الرصيد المتاح</th>
                                                <th class="text-center">الحالة</th>
                                                <th class="text-end">إجراء سريع</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($guestHouseInventory as $stock)
                                                @php
                                                    $qty = (float)$stock->stock;
                                                    $isLow = $qty <= 5;
                                                @endphp
                                                <tr>
                                                    <td>
                                                        <strong>{{ $stock->item?->name }}</strong>
                                                        <span class="text-muted small">({{ $stock->item?->unit }})</span>
                                                    </td>
                                                    <td><span class="text-muted small">{{ $stock->warehouse?->name ?: 'مخزن الدار' }}</span></td>
                                                    <td class="text-center font-monospace fw-bold {{ $isLow ? 'text-danger' : 'text-success' }}">
                                                        {{ number_format($qty, 2) }}
                                                    </td>
                                                    <td class="text-center">
                                                        @if($isLow)
                                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle small">منخفض</span>
                                                        @else
                                                            <span class="badge bg-success-subtle text-success border border-success-subtle small">متوفر</span>
                                                        @endif
                                                    </td>
                                                    <td class="text-end">
                                                        <button type="button" class="btn btn-xs btn-outline-info rounded-pill py-0 px-2" data-bs-toggle="modal" data-bs-target="#guestHouseIssueModal" title="صرف للوجبات">
                                                            صرف
                                                        </button>
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="5" class="text-center text-muted py-3">لا توجد مواد غذائية مسجلة بمخزون الدار حالياً.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="border rounded-3 bg-white p-3 shadow-xs h-100">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history me-1 text-primary"></i> آخر حركات التوريد والصرف</h6>
                                    <span class="badge bg-secondary-subtle text-secondary small">آخر 15 حركة</span>
                                </div>
                                <div class="table-responsive" style="max-height: 260px; overflow-y:auto;">
                                    <table class="table table-sm align-middle small mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>التاريخ</th>
                                                <th>الصنف</th>
                                                <th>النوع</th>
                                                <th class="text-end">الكمية</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inventoryLogs->take(15) as $log)
                                                <tr>
                                                    <td class="text-muted font-monospace">{{ $log->transaction_date ? \Carbon\Carbon::parse($log->transaction_date)->format('m-d') : '—' }}</td>
                                                    <td>{{ $log->item?->name }}</td>
                                                    <td>
                                                        <span class="badge {{ $log->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                                            {{ $log->type === 'in' ? 'توريد' : 'صرف' }}
                                                        </span>
                                                    </td>
                                                    <td class="text-end fw-bold {{ $log->type === 'in' ? 'text-success' : 'text-danger' }} font-monospace">
                                                        {{ $log->type === 'in' ? '+' : '-' }}{{ number_format((float)$log->quantity, 2) }}
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="4" class="text-center text-muted py-3">لا توجد حركات مسجلة.</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- ========================================================
             المحور الثاني: العهدة المالية والمصروفات النثرية (Petty Cash Custody)
             ======================================================== --}}
        <div class="tab-pane fade" id="gh-petty-cash-pane">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-cash-stack me-1"></i> الخزينة النثرية والعهدة المالية للدار (Petty Cash)</h6>
                    <small class="text-muted">إدارة المصروفات اليومية الصغيرة والنثرية الفورية مع إرفاق الإيصالات وطلب استعاضة العهدة بشفافية تامة</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-success btn-sm text-white fw-bold px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalAddPettyExpense">
                        <i class="bi bi-plus-circle me-1"></i> تسجيل مصروف نثري فوري
                    </button>
                    <button class="btn btn-warning btn-sm fw-bold px-3 shadow-xs text-dark" data-bs-toggle="modal" data-bs-target="#modalReplenishPettyCash">
                        <i class="bi bi-arrow-repeat me-1"></i> طلب استعاضة العهدة
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> طباعة كشف العهدة
                    </button>
                </div>
            </div>

            {{-- Petty Cash Stat Badges --}}
            <div class="row g-3 mb-4">
                @forelse($pettyCashCustodies as $custody)
                    @php
                        $cBalance = (float)($custody->treasury?->current_balance ?? 0);
                        $isLowPetty = $cBalance < 1000;
                        $cBreakdown = $custody->treasury ? $custody->treasury->balance_breakdown : ['custody_balance' => 0, 'donations_balance' => 0];
                    @endphp
                    <div class="col-md-4">
                        <div class="border rounded-3 p-3 bg-white shadow-xs h-100 border-start border-4 {{ $isLowPetty ? 'border-warning' : 'border-success' }}">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong class="fs-6">{{ $custody->name }}</strong>
                                <span class="badge {{ $isLowPetty ? 'bg-warning text-dark' : 'bg-success' }}">
                                    {{ $isLowPetty ? '⚠️ تحتاج استعاضة' : '🟢 رصيد كافٍ' }}
                                </span>
                            </div>
                            <div class="small text-muted mb-1">{{ $custody->treasury?->name ?: 'خزينة العهدة النثرية' }}</div>
                            <div class="fs-4 fw-bold {{ $isLowPetty ? 'text-warning-emphasis' : 'text-success' }} font-monospace">
                                {{ number_format($cBalance, 2) }} {{ $custody->treasury?->currency ?? 'ج.م' }}
                            </div>
                            <div class="row g-1 mt-2 pt-2 border-top small">
                                <div class="col-6">
                                    <div class="p-1.5 rounded-2 bg-warning-subtle border border-warning-subtle">
                                        <span class="text-dark d-block" style="font-size: 0.7rem;">💼 فلوس العهدة</span>
                                        <strong class="font-monospace text-dark">{{ number_format((float)($cBreakdown['custody_balance'] ?? 0), 2) }}</strong>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-1.5 rounded-2 bg-success-subtle border border-success-subtle">
                                        <span class="text-success-emphasis d-block" style="font-size: 0.7rem;">💚 فلوس التبرعات</span>
                                        <strong class="font-monospace text-success">{{ number_format((float)($cBreakdown['donations_balance'] ?? 0), 2) }}</strong>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top small flex-wrap gap-1">
                                <button type="button" class="btn btn-link btn-sm p-0 text-primary fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#modalReplenishPettyCash">
                                    + تغذية العهدة
                                </button>
                                @if($custody->treasury)
                                    <form method="POST" action="{{ route('treasuries.adjustTotals', $custody->treasury) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من تصفير خزينة ({{ $custody->treasury->name }}) يدوياً إلى 0.00؟');">
                                        @csrf
                                        <input type="hidden" name="zero_treasury" value="1">
                                        <input type="hidden" name="reason" value="تصفير خزينة دار الضيافة يدوياً ({{ $guest_house->name }})">
                                        <button type="submit" class="btn btn-xs btn-outline-danger rounded-pill py-0 px-2">
                                            <i class="bi bi-arrow-counterclockwise"></i> تصفير يدوياً (0.00)
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info py-3 text-center">
                            لم يتم ربط خزينة عهدة مالية بعد للدار. 
                            <button class="btn btn-sm btn-primary ms-2" data-bs-toggle="modal" data-bs-target="#guestHouseCustodyModal">
                                + إضافة عهدة مالية الآن
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            {{-- Quick Category Shortcuts --}}
            <div class="p-3 bg-light rounded-3 border mb-4">
                <div class="fw-bold mb-2 small text-secondary"><i class="bi bi-tag me-1"></i> بنود الصرف النثري السريع:</div>
                <div class="d-flex flex-wrap gap-2">
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-2xs"><i class="bi bi-cart3 text-success me-1"></i> شراء خضروات ومواد طازجة</span>
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-2xs"><i class="bi bi-heart-pulse text-danger me-1"></i> طوارئ مريض وعلاجية</span>
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-2xs"><i class="bi bi-stars text-info me-1"></i> أدوات نظافة ومطهرات</span>
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-2xs"><i class="bi bi-wrench text-warning me-1"></i> صيانة سريعة للأجهزة</span>
                    <span class="badge bg-white text-dark border px-3 py-2 shadow-2xs"><i class="bi bi-taxi-front text-primary me-1"></i> مواصلات عامة</span>
                </div>
            </div>

            {{-- Petty Expenses Table --}}
            <div class="border rounded-3 bg-white p-3 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-receipt me-1 text-primary"></i> سجل المصروفات النثرية والفواتير المسجلة</h6>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $pettyCashExpenses->count() }} فاتورة مسجلة</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>بند المصروف / البيان</th>
                                <th>الخزينة المنصرف منها</th>
                                <th class="text-center">المبلغ</th>
                                <th class="text-center">الإيصال / الفاتورة</th>
                                <th>المسؤول</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($pettyCashExpenses as $exp)
                                <tr>
                                    <td class="text-muted font-monospace small">{{ $loop->iteration }}</td>
                                    <td class="font-monospace small">{{ $exp->paid_at ? \Carbon\Carbon::parse($exp->paid_at)->format('Y-m-d') : ($exp->created_at ? $exp->created_at->format('Y-m-d') : '—') }}</td>
                                    <td>
                                        <strong>{{ $exp->description ?: ($exp->title ?: 'مصروف دار الضيافة') }}</strong>
                                        @if($exp->notes && $exp->notes !== $exp->description)
                                            <div class="text-muted small">{{ $exp->notes }}</div>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $exp->treasury?->name ?: 'الخزينة النثرية' }}</span></td>
                                    <td class="text-center font-monospace fw-bold text-danger fs-6">
                                        {{ number_format((float)$exp->amount, 2) }} {{ $exp->currency ?? 'ج.م' }}
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $attachment = $exp->attachment_path ?: $exp->receipt;
                                        @endphp
                                        @if($attachment)
                                            <a href="{{ asset('storage/' . $attachment) }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                                <i class="bi bi-file-earmark-image me-1"></i> عرض الفاتورة
                                            </a>
                                        @else
                                            <span class="text-muted small">بدون مرفق</span>
                                        @endif
                                    </td>
                                    <td class="small text-muted">{{ $exp->creator?->name ?: ($exp->user?->name ?: '—') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        لم يتم تسجيل أي مصروفات نثرية بعد. انقر على "تسجيل مصروف نثري فوري" للبدء.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================================================
             المحور الثالث: عهدة الأجهزة والمعدات (Equipment & Appliances Custody)
             ======================================================== --}}
        <div class="tab-pane fade" id="gh-assets-pane">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-cpu me-1"></i> عهدة الأجهزة والمعدات وسجل الصيانة الدوري</h6>
                    <small class="text-muted">حصر الأجهزة الكهربائية والطبية، توزيعها بالغرف والأجنحة، تتبع الحالة وسجل بلاغات الأعطال</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary btn-sm fw-bold px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalAddAsset">
                        <i class="bi bi-plus-circle me-1"></i> إضافة جهاز / معدة جديدة
                    </button>
                    <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> طباعة كشف العهدة
                    </button>
                </div>
            </div>

            {{-- Asset Stats Cards --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">إجمالي الأجهزة</div>
                            <div class="fs-5 fw-bold text-dark font-monospace">{{ $assetStats['total'] }} جهاز</div>
                        </div>
                        <div class="fs-4 text-primary opacity-75"><i class="bi bi-boxes"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">تعمل بكفاءة</div>
                            <div class="fs-5 fw-bold text-success font-monospace">{{ $assetStats['working'] }} جهاز</div>
                        </div>
                        <div class="fs-4 text-success opacity-75"><i class="bi bi-check-circle-fill"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">تحتاج صيانة دورية</div>
                            <div class="fs-5 fw-bold text-warning font-monospace">{{ $assetStats['needs_maintenance'] }} جهاز</div>
                        </div>
                        <div class="fs-4 text-warning opacity-75"><i class="bi bi-wrench-adjustable"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">معطل / خارج الخدمة</div>
                            <div class="fs-5 fw-bold text-danger font-monospace">{{ $assetStats['out_of_service'] }} جهاز</div>
                        </div>
                        <div class="fs-4 text-danger opacity-75"><i class="bi bi-x-octagon-fill"></i></div>
                    </div>
                </div>
            </div>

            {{-- Category Filter Pills --}}
            <div class="d-flex flex-wrap gap-2 mb-3">
                <button type="button" class="btn btn-sm btn-dark rounded-pill px-3 asset-filter-btn active" onclick="filterAssets('all', this)">
                    الكل ({{ $guestHouseAssets->count() }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 asset-filter-btn" onclick="filterAssets('medical', this)">
                    🩺 أجهزة طبية ومساعدة ({{ $guestHouseAssets->where('category', 'medical')->count() }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning text-dark rounded-pill px-3 asset-filter-btn" onclick="filterAssets('electrical', this)">
                    ⚡ كهربائية ومنزلية ({{ $guestHouseAssets->where('category', 'electrical')->count() }})
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3 asset-filter-btn" onclick="filterAssets('facility', this)">
                    🏢 تجهيزات ومرافق ({{ $guestHouseAssets->where('category', 'facility')->count() }})
                </button>
            </div>

            {{-- Assets Table --}}
            <div class="border rounded-3 bg-white p-3 shadow-xs">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" id="ghAssetsTable">
                        <thead class="table-light small">
                            <tr>
                                <th>كود الجهاز</th>
                                <th>اسم الجهاز والمواصفات</th>
                                <th class="text-center">العدد</th>
                                <th>التصنيف</th>
                                <th>الموقع والتوزيع</th>
                                <th class="text-center">الحالة التشغيلية</th>
                                <th>الضمان والتوريد</th>
                                <th class="text-end">إجراءات وصيانة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guestHouseAssets as $asset)
                                <tr class="asset-row" data-category="{{ $asset->category }}">
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace">{{ $asset->code ?: 'AS-'.$asset->id }}</span>
                                    </td>
                                    <td>
                                        <strong class="d-block">{{ $asset->name }}</strong>
                                        @if($asset->brand_model)
                                            <span class="text-muted small">{{ $asset->brand_model }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2.5 py-1">
                                            {{ max(1, (int)($asset->quantity ?? 1)) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $asset->category_label }}</span>
                                    </td>
                                    <td>
                                        <div><i class="bi bi-geo-alt text-primary me-1"></i>{{ $asset->wing?->name ?: 'الدار العامة' }}</div>
                                        @if($asset->location_details)
                                            <span class="text-muted small">{{ $asset->location_details }}</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $asset->status_badge_class }} py-1 px-2.5">
                                            {{ $asset->status_label }}
                                        </span>
                                    </td>
                                    <td class="small text-muted font-monospace">
                                        <div>توريد: {{ $asset->purchase_date?->format('Y-m-d') ?: '—' }}</div>
                                        @if($asset->warranty_until)
                                            <div class="text-info">ضمان: {{ $asset->warranty_until->format('Y-m-d') }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <div class="d-inline-flex gap-1">
                                            <button type="button" class="btn btn-xs btn-outline-warning text-dark rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalAddAssetMaintenance_{{ $asset->id }}" title="تسجيل بلاغ عطل أو صيانة">
                                                <i class="bi bi-tools me-1"></i> صيانة
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-secondary rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalEditAssetStatus_{{ $asset->id }}" title="تعديل الحالة">
                                                <i class="bi bi-arrow-repeat"></i>
                                            </button>
                                            <button type="button" class="btn btn-xs btn-outline-info rounded-pill px-2" data-bs-toggle="modal" data-bs-target="#modalAssetHistory_{{ $asset->id }}" title="سجل الصيانات">
                                                <i class="bi bi-clock-history"></i> ({{ $asset->maintenances->count() }})
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">
                                        لم يتم تسجيل أي أجهزة بعد. انقر على "إضافة جهاز / معدة جديدة" للبدء.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- ========================================================
             المحور الرابع: مركز التواصل والدعم والكول سنتر (Call Center & Support)
             ======================================================== --}}
        <div class="tab-pane fade" id="gh-support-callcenter-pane">
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                <div>
                    <h6 class="fw-bold mb-1 text-primary"><i class="bi bi-headset me-1"></i> مركز التواصل والدعم اللوجستي والكول سنتر</h6>
                    <small class="text-muted">سجل المكالمات الواردة والصادرة، بلاغات الطوارئ والإسعاف، وطلبات النزلاء واستبيانات الرضا</small>
                </div>
                <div class="d-flex flex-wrap gap-2">
                    <button class="btn btn-primary btn-sm fw-bold px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalAddSupportTicket">
                        <i class="bi bi-telephone-plus me-1"></i> تسجيل مكالمة / طلب دعم
                    </button>
                    <button class="btn btn-danger btn-sm fw-bold px-3 shadow-xs" data-bs-toggle="modal" data-bs-target="#modalAddSupportTicketEmergency">
                        <i class="bi bi-ambulance me-1"></i> بلاغ طوارئ وإسعاف عاجل
                    </button>
                </div>
            </div>

            {{-- Support Stats --}}
            <div class="row g-2 mb-3">
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">إجمالي المكالمات والبلاغات</div>
                            <div class="fs-5 fw-bold text-dark font-monospace">{{ $ticketStats['total'] }}</div>
                        </div>
                        <div class="fs-4 text-primary opacity-75"><i class="bi bi-telephone-inbound"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">طلبات قيد المتابعة</div>
                            <div class="fs-5 fw-bold text-warning font-monospace">{{ $ticketStats['open'] }}</div>
                        </div>
                        <div class="fs-4 text-warning opacity-75"><i class="bi bi-hourglass-split"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">طوارئ طبية وإسعاف</div>
                            <div class="fs-5 fw-bold text-danger font-monospace">{{ $ticketStats['emergency'] }}</div>
                        </div>
                        <div class="fs-4 text-danger opacity-75"><i class="bi bi-exclamation-octagon-fill"></i></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-2.5 px-3 rounded-3 border bg-white shadow-2xs d-flex align-items-center justify-content-between">
                        <div>
                            <div class="text-muted small">طلبات تم حلها</div>
                            <div class="fs-5 fw-bold text-success font-monospace">{{ $ticketStats['resolved'] }}</div>
                        </div>
                        <div class="fs-4 text-success opacity-75"><i class="bi bi-check-all"></i></div>
                    </div>
                </div>
            </div>

            {{-- Quick WhatsApp Share Card --}}
            <div class="p-3 rounded-3 border bg-light-subtle mb-4 shadow-2xs">
                <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-2 text-success"><i class="bi bi-whatsapp"></i></div>
                        <div>
                            <strong class="d-block text-success">التواصل السريع وإرسال بيانات الدار للنزيل أو المرافق</strong>
                            <small class="text-muted">إرسال عنوان الدار وموقع Google Maps وتفاصيل الاستقبال عبر الواتساب بنقرة واحدة</small>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 flex-wrap">
                        <select id="waQuickSelectPhone" class="form-select form-select-sm" style="min-width: 200px;">
                            <option value="">— اختر نزيلاً حالياً بالدار —</option>
                            @foreach($residentStays as $stay)
                                @if($stay->beneficiary?->phone)
                                    <option value="{{ $stay->beneficiary->phone }}">{{ $stay->beneficiary->full_name }} ({{ $stay->beneficiary->phone }})</option>
                                @endif
                            @endforeach
                        </select>
                        <input id="waQuickCustomPhone" type="text" class="form-control form-control-sm" placeholder="أو اكتب رقم الهاتف..." style="max-width: 170px;">
                        <button type="button" class="btn btn-sm btn-success fw-bold px-3" onclick="sendWhatsAppHouseLocation()">
                            <i class="bi bi-send-fill me-1"></i> إرسال عبر واتساب
                        </button>
                    </div>
                </div>
            </div>

            {{-- Support Tickets & Call Center Table --}}
            <div class="border rounded-3 bg-white p-3 shadow-xs">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-list-check me-1 text-primary"></i> سجل البلاغات ومكالمات الكول سنتر</h6>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $guestHouseTickets->count() }} تذكرة مسجلة</span>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small">
                            <tr>
                                <th>#</th>
                                <th>التاريخ والوقت</th>
                                <th>المتصل / النزيل</th>
                                <th class="text-center">الاتجاه</th>
                                <th>القسم والتصنيف</th>
                                <th class="text-center">الأولوية</th>
                                <th>الموضوع والتفاصيل</th>
                                <th class="text-center">الحالة</th>
                                <th class="text-end">الإجراء والمتابعة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($guestHouseTickets as $ticket)
                                <tr>
                                    <td class="text-muted font-monospace small">{{ $loop->iteration }}</td>
                                    <td class="font-monospace small">
                                        {{ $ticket->created_at ? $ticket->created_at->format('m-d H:i') : '—' }}
                                    </td>
                                    <td>
                                        <strong>{{ $ticket->caller_name ?: ($ticket->beneficiary?->full_name ?: 'نزيل الدار') }}</strong>
                                        @if($ticket->caller_phone)
                                            <div class="d-flex align-items-center gap-1 mt-1 small">
                                                <span class="text-muted font-monospace">{{ $ticket->caller_phone }}</span>
                                                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $ticket->caller_phone) }}" target="_blank" class="text-success text-decoration-none" title="محادثة واتساب">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                                <a href="tel:{{ $ticket->caller_phone }}" class="text-primary text-decoration-none" title="اتصال">
                                                    <i class="bi bi-telephone"></i>
                                                </a>
                                            </div>
                                        @endif
                                        <span class="badge bg-light text-secondary border small mt-1">{{ $ticket->caller_type_label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $ticket->direction === 'inbound' ? 'bg-primary-subtle text-primary' : 'bg-info-subtle text-info' }}">
                                            {{ $ticket->direction === 'inbound' ? '📞 وارد' : '📲 صادر' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary-subtle text-secondary">{{ $ticket->category_label }}</span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $ticket->priority_badge_class }}">
                                            {{ $ticket->priority_label }}
                                        </span>
                                    </td>
                                    <td>
                                        <strong class="d-block">{{ $ticket->subject }}</strong>
                                        <span class="text-muted small">{{ Str::limit($ticket->details, 60) }}</span>
                                        @if($ticket->action_taken)
                                            <div class="text-success small mt-1"><i class="bi bi-check2 me-1"></i>{{ Str::limit($ticket->action_taken, 50) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <span class="badge {{ $ticket->status_badge_class }}">
                                            {{ $ticket->status_label }}
                                        </span>
                                        @if($ticket->satisfaction_rating)
                                            <div class="text-warning small mt-1">{{ str_repeat('⭐', $ticket->satisfaction_rating) }}</div>
                                        @endif
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-xs btn-outline-primary rounded-pill px-2.5" data-bs-toggle="modal" data-bs-target="#modalEditTicket_{{ $ticket->id }}" title="تعديل الحالة وتسجيل الإجراء">
                                            <i class="bi bi-pencil-square me-1"></i> متابعة
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="9" class="text-center text-muted py-4">
                                        لم يتم تسجيل أي مكالمات أو بلاغات دعم بعد. انقر على "تسجيل مكالمة / طلب دعم" للبدء.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function isRealCompanionName(name) {
        if (!name) return false;
        const clean = name.trim().replace(/\s+/g, ' ').toLowerCase();
        const norm = clean.replace(/[أإآ]/g, 'ا').replace(/ة/g, 'ه');
        const negatives = [
            'لا يوجد', 'لايوجد', 'لا يوجد مرافق', 'لايوجد مرافق',
            'لا يوجد مرافقين', 'لايوجد مرافقين', 'بدون مرافق', 'بدون مرافقين',
            'بدون مرافق له', 'بدون', 'لا', 'كلا', 'منفرد', 'لوحده', 'بمفرده',
            'غير متوفر', '-', '--', '---', 'none', 'no', 'null', 'n/a', 'na'
        ];
        return !negatives.includes(norm);
    }

    function handleCompanionNameInput(input, benId) {
        const cInput = document.querySelector(`input[name="servings[${benId}][companion_meals_count]"]`);
        const chk = document.querySelector(`input[name="received_beneficiary_ids[]"][value="${benId}"]`);
        if (!isRealCompanionName(input.value)) {
            if (cInput) cInput.value = 0;
        } else {
            if (cInput && parseInt(cInput.value || 0) === 0 && (!chk || chk.checked)) {
                cInput.value = 1;
            }
        }
    }

    function handleMealCheckboxChange(chk, mealType, benId, initialHasCompanion) {
        const pInput = document.querySelector(`input[name="servings[${benId}][patient_meals_count]"]`);
        const cInput = document.querySelector(`input[name="servings[${benId}][companion_meals_count]"]`);
        const cNameInput = document.querySelector(`input[name="servings[${benId}][companion_name]"]`);

        const hasCompanion = cNameInput ? isRealCompanionName(cNameInput.value) : !!initialHasCompanion;

        if (chk.checked) {
            if (pInput && parseInt(pInput.value || 0) === 0) pInput.value = 1;
            if (cInput) {
                cInput.value = hasCompanion ? (parseInt(cInput.value || 0) > 0 ? cInput.value : 1) : 0;
            }
        } else {
            if (pInput) pInput.value = 0;
            if (cInput) cInput.value = 0;
        }
    }

    function toggleAllMeals(type, check) {
        document.querySelectorAll('.meal-chk-' + type).forEach(chk => {
            chk.checked = check;
            chk.dispatchEvent(new Event('change'));
        });
    }

    function toggleCustodyTypeFields(type) {
        const finBox = document.getElementById('gh_custody_financial_box');
        const inkBox = document.getElementById('gh_custody_inkind_box');
        if (type === 'financial') {
            if (finBox) finBox.classList.remove('d-none');
            if (inkBox) inkBox.classList.add('d-none');
        } else {
            if (finBox) finBox.classList.add('d-none');
            if (inkBox) inkBox.classList.remove('d-none');
        }
    }

    function filterAssets(cat, btn) {
        document.querySelectorAll('.asset-filter-btn').forEach(b => {
            b.classList.remove('active', 'btn-dark');
            b.classList.add('btn-outline-secondary');
        });
        btn.classList.add('active', 'btn-dark');
        btn.classList.remove('btn-outline-secondary');

        const rows = document.querySelectorAll('#ghAssetsTable .asset-row');
        rows.forEach(r => {
            if (cat === 'all' || r.getAttribute('data-category') === cat) {
                r.style.display = '';
            } else {
                r.style.display = 'none';
            }
        });
    }

    function sendWhatsAppHouseLocation() {
        const selectPhone = document.getElementById('waQuickSelectPhone').value;
        const customPhone = document.getElementById('waQuickCustomPhone').value;
        const rawPhone = customPhone || selectPhone || '';
        let phone = rawPhone.replace(/[^0-9]/g, '');

        if (!phone) {
            alert('يرجى اختيار نزيل أو إدخال رقم هاتف المستلم أولاً.');
            return;
        }

        if (phone.startsWith('01')) {
            phone = '2' + phone;
        }

        const houseName = @json($guest_house->name);
        const houseAddress = @json($guest_house->address ?: 'مقر دار الضيافة');
        const housePhone = @json($guest_house->phone ?: '');
        const message = `مرحباً بكم، تفاصيل وإرشادات دار الضيافة (${houseName}):\n📍 العنوان: ${houseAddress}\n📞 هاتف الدار: ${housePhone}\nنتمنى لكم الشفاء العاجل ونسعد باستقبالكم دائماً.`;

        const waUrl = `https://wa.me/${phone}?text=${encodeURIComponent(message)}`;
        window.open(waUrl, '_blank');
    }
</script>

{{-- Modal: إضافة جناح وأسِرّة (للإدارة ومدير الدار) --}}
@if(!empty($isAdmin) || !empty($isAssigned) || auth()->user()?->hasRole(['manager', 'مدير دار الضيافة']))
<div class="modal fade" id="guestHouseWingModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('guest-houses.wings.store', $guest_house) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-grid-3x3-gap text-primary me-1"></i> إضافة جناح وأسِرّة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label form-label-required">اسم الجناح</label>
                <input name="name" class="form-control mb-3" placeholder="مثال: جناح 1" required>
                <label class="form-label form-label-required">عدد الأسِرّة بالجناح</label>
                <input type="number" name="beds_count" min="1" max="100" value="8" class="form-control" required>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary fw-bold">إضافة الجناح</button>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Modal: إضافة عهدة للدار --}}
<div class="modal fade" id="guestHouseCustodyModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('guest-houses.custodies.store', $guest_house) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-safe text-warning me-1"></i> إضافة عهدة للدار</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label form-label-required">اسم العهدة</label>
                <input name="name" class="form-control mb-2" required placeholder="مثال: عهدة مواد غذائية، عهدة نقدية">

                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <label class="form-label form-label-required">نوع العهدة</label>
                        <select name="type" id="gh_custody_type" class="form-select" required onchange="toggleCustodyTypeFields(this.value)">
                            <option value="financial">عهدة مالية</option>
                            <option value="in_kind">عهدة عينية (مخزون)</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">تصنيف العهدة</label>
                        <select name="category" class="form-select">
                            <option value="general">عامة</option>
                            <option value="food">غذائية ومؤن</option>
                            <option value="equipment">أجهزة ومعدات</option>
                            <option value="medical">طبية وإسعافية</option>
                        </select>
                    </div>
                    <div class="col-4">
                        <label class="form-label">العدد / الكمية</label>
                        <input type="number" min="1" name="quantity" value="1" class="form-control font-monospace" placeholder="1">
                    </div>
                </div>

                <div id="gh_custody_financial_box">
                    <label class="form-label">الخزينة المرتبطة (للعهدة المالية)</label>
                    <select name="treasury_id" class="form-select mb-1">
                        <option value="">— إنشاء خزينة عهدة جديدة تلقائيًا —</option>
                        @foreach($guestHouseExpenseTreasuries as $treasury)
                            <option value="{{ $treasury->id }}">{{ $treasury->name }}</option>
                        @endforeach
                    </select>
                    <div class="small text-muted mb-2">عند عدم اختيار خزينة سيتم إنشاء خزينة عهدة مالية للدار برصيد صفر.</div>
                </div>

                <div id="gh_custody_inkind_box" class="d-none">
                    <label class="form-label form-label-required">المخزن المرتبط بالعهدة العينية</label>
                    <select name="warehouse_id" class="form-select mb-2">
                        <option value="">— اختر المخزن —</option>
                        @foreach($guestHouseWarehouses as $warehouse)
                            <option value="{{ $warehouse->id }}">{{ $warehouse->name }}</option>
                        @endforeach
                    </select>

                    <div class="p-2 border rounded-3 bg-light mb-2">
                        <span class="small fw-bold text-primary d-block mb-1"><i class="bi bi-plus-circle me-1"></i> رصيد افتتاحي أو إضافة صنف جديد (اختياري عند الإنشاء):</span>
                        <div class="row g-2 mb-2">
                            <div class="col-7">
                                <select name="initial_item_id" id="gh_custody_initial_item_select" class="form-select form-select-sm" onchange="document.getElementById('gh_custody_new_item_box').classList.toggle('d-none', this.value !== '__new__'); if(this.value === '__new__') this.value = '';">
                                    <option value="">— اختر الصنف المسجل —</option>
                                    <option value="__new__" class="fw-bold text-success">+ كتابة صنف جديد غير موجود بالقائمة...</option>
                                    @foreach($guestHouseItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="number" step="0.001" min="0.001" name="initial_quantity" class="form-control form-control-sm" placeholder="الكمية / العدد">
                            </div>
                        </div>
                        <div id="gh_custody_new_item_box" class="row g-2 pt-1 border-top">
                            <div class="col-8">
                                <input type="text" name="new_initial_item_name" class="form-control form-control-sm" placeholder="أو اكتب اسم صنف جديد مباشرة (يُضاف للأصناف تلقائياً)...">
                            </div>
                            <div class="col-4">
                                <input type="text" name="new_initial_item_unit" class="form-control form-control-sm" value="عدد" placeholder="الوحدة (كجم/عدد/كرتونة)">
                            </div>
                        </div>
                    </div>
                </div>

                <label class="form-label">ملاحظات العهدة</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="أي تفاصيل أو ملاحظات إضافية..."></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-warning fw-bold"><i class="bi bi-check-lg me-1"></i> حفظ العهدة</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: توريد للمخزون --}}
<div class="modal fade" id="guestHouseSupplyModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('guest-houses.inventory.supply', $guest_house) }}">
            @csrf
            <div class="modal-header bg-success text-white py-2">
                <h5 class="modal-title fw-bold"><i class="bi bi-box-arrow-in-down me-1"></i> توريد أصناف للمخزون العيني</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label form-label-required">العهدة العينية المستلمة</label>
                <select name="custody_id" class="form-select mb-2" required>
                    <option value="">— اختر العهدة العينية —</option>
                    @foreach($inventoryCustodies as $custody)
                        <option value="{{ $custody->id }}">{{ $custody->name }} — {{ $custody->warehouse?->name }}</option>
                    @endforeach
                </select>

                <div class="d-flex justify-content-between align-items-center mb-1">
                    <label class="form-label form-label-required mb-0">الصنف المراد توريده</label>
                    <button type="button" class="btn btn-xs btn-outline-success rounded-pill py-0 px-2" onclick="const box = document.getElementById('gh_supply_new_item_box'); box.classList.toggle('d-none'); const sel = document.getElementById('gh_supply_item_select'); if(!box.classList.contains('d-none')) { sel.value = ''; sel.required = false; document.getElementById('gh_supply_new_item_name').focus(); } else { sel.required = true; }">
                        <i class="bi bi-plus-circle me-1"></i> كتابة صنف جديد غير موجود بالقائمة
                    </button>
                </div>
                <select name="item_id" id="gh_supply_item_select" class="form-select mb-2" onchange="if(this.value === '__new__') { this.value = ''; this.required = false; document.getElementById('gh_supply_new_item_box').classList.remove('d-none'); document.getElementById('gh_supply_new_item_name').focus(); }">
                    <option value="">— اختر الصنف من القائمة —</option>
                    <option value="__new__" class="fw-bold text-success">+ كتابة صنف جديد يدوياً (يُضاف تلقائياً للأصناف)...</option>
                    @foreach($guestHouseItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                    @endforeach
                </select>

                <div id="gh_supply_new_item_box" class="p-2.5 border border-success border-opacity-50 rounded-3 bg-success-subtle mb-2 d-none">
                    <div class="small fw-bold text-success-emphasis mb-1"><i class="bi bi-plus-square-fill me-1"></i> إضافة صنف جديد وتوريده فوراً للمخزن:</div>
                    <div class="row g-2">
                        <div class="col-8">
                            <input type="text" name="new_item_name" id="gh_supply_new_item_name" class="form-control form-control-sm" placeholder="اكتب اسم الصنف الجديد (مثال: جبنة فيتا / عدس / بطاطين)...">
                        </div>
                        <div class="col-4">
                            <input type="text" name="new_item_unit" class="form-control form-control-sm" value="كجم" placeholder="الوحدة (كجم/عدد/كرتونة)">
                        </div>
                    </div>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-6">
                        <label class="form-label form-label-required">الكمية الموردة</label>
                        <input type="number" step="0.001" min="0.001" name="quantity" class="form-control" placeholder="مثال: 50" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label">تاريخ التوريد</label>
                        <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" class="form-control">
                    </div>
                </div>

                <label class="form-label">ملاحظات التوريد أو مصدر الشراء</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="مثال: شراء كراتين مياه ومعلبات لصالح الدار"></textarea>
            </div>
            <div class="modal-footer py-2">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success btn-sm fw-bold px-3" @disabled($inventoryCustodies->isEmpty())>
                    <i class="bi bi-check-lg me-1"></i> تنفيذ التوريد وإضافة للرصيد
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: صرف من المخزون --}}
<div class="modal fade" id="guestHouseIssueModal" tabindex="-1">
    <div class="modal-dialog">
        <form class="modal-content" method="POST" action="{{ route('guest-houses.inventory.issue', $guest_house) }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-arrow-up text-info me-1"></i> صرف من مخزون عيني</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label form-label-required">العهدة العينية</label>
                <select name="custody_id" class="form-select mb-2" required>
                    <option value="">— اختر العهدة —</option>
                    @foreach($inventoryCustodies as $custody)
                        <option value="{{ $custody->id }}">{{ $custody->name }} — {{ $custody->warehouse?->name }}</option>
                    @endforeach
                </select>
                <label class="form-label form-label-required">الصنف</label>
                <select name="item_id" class="form-select mb-2" required>
                    <option value="">— اختر الصنف —</option>
                    @foreach($guestHouseItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                    @endforeach
                </select>
                <label class="form-label form-label-required">الكمية</label>
                <input type="number" step="0.001" min="0.001" name="quantity" class="form-control mb-2" required>
                <label class="form-label">المستفيد (اختياري)</label>
                <select name="beneficiary_id" class="form-select mb-2">
                    <option value="">— صرف عام للدار —</option>
                    @foreach($residentStays as $stay)
                        <option value="{{ $stay->beneficiary_id }}">{{ $stay->beneficiary->full_name }}</option>
                    @endforeach
                </select>
                <label class="form-label form-label-required">سبب الصرف</label>
                <textarea name="notes" class="form-control" required placeholder="مثال: وجبات يوم السبت"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-info text-white fw-bold" @disabled($inventoryCustodies->isEmpty())>تنفيذ الصرف</button>
            </div>
        </form>
    </div>
</div>

@push('modals')
{{-- ======================================================
     Operations Wing & Bed Management Modals (Root Level)
     ====================================================== --}}
@foreach($guestHouseWings as $wing)
    {{-- Modal: Edit Wing & Adjust Beds Count (Operations) --}}
    <div class="modal fade" id="opsEditWingModal_{{ $wing->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.wings.update', [$guest_house, $wing]) }}">
                @csrf 
                @method('PUT')
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-1"></i> تعديل جناح: {{ $wing->name }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم الجناح <span class="text-danger">*</span></label>
                        <input name="name" class="form-control" value="{{ $wing->name }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">إجمالي عدد الأسِرّة في الجناح <span class="text-danger">*</span></label>
                        <input type="number" name="beds_count" min="0" max="200" value="{{ $wing->beds->count() }}" class="form-control" required>
                        <div class="form-text small">
                            • زيادة العدد يضيف أسِرّة جديدة تلقائياً.<br>
                            • إنقاص العدد يحذف الأسِرّة الفارغة فقط ولن يتأثر أي سرير به نزيل مقيم.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">حالة الجناح</label>
                        <select name="is_active" class="form-select">
                            <option value="1" @selected($wing->is_active)>نشط وجاهز للتشغيل</option>
                            <option value="0" @selected(!$wing->is_active)>معطل مؤقتاً</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">ملاحظات الجناح</label>
                        <textarea name="notes" class="form-control" rows="2">{{ $wing->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">حفظ تعديلات الجناح</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Add Single Bed to Wing (Operations) --}}
    <div class="modal fade" id="opsAddBedModal_{{ $wing->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.wings.beds.store', [$guest_house, $wing]) }}">
                @csrf
                <div class="modal-header bg-success text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-plus-circle me-1"></i> إضافة سرير / مرتبة إلى {{ $wing->name }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">نوع السرير / المرفق <span class="text-danger">*</span></label>
                        <select name="type" class="form-select">
                            <option value="standard">🛏️ سرير أساسي</option>
                            <option value="mattress">🛏️ مرتبة إضافية (أرضية / طوارئ ضغط مرضى)</option>
                        </select>
                        <div class="form-text small">اختر "مرتبة إضافية" لتمييزها بعلامة خاصة عند زيادة طاقة الجناح.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">رقم أو اسم السرير</label>
                        <input name="number" class="form-control" placeholder="اختياري (اتركه فارغاً للتوليد التلقائي)">
                        <div class="form-text small">إذا تركته فارغاً سيتم تحديد الرقم التالي تلقائياً.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="available">فارغ ومتاح للحجز</option>
                            <option value="maintenance">تحت الصيانة</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات على السرير</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات اختيارية (مثال: مرتبة أرضية لضغط الحالات)"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success btn-sm fw-bold px-3">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    @foreach($wing->beds as $bed)
        @php
            $occupied = (bool) $bed->activeStay;
            $isMattress = $bed->isMattress();
        @endphp
        {{-- Modal: Edit Single Bed (Operations) --}}
        <div class="modal fade" id="opsEditBedModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.beds.update', [$guest_house, $bed]) }}">
                    @csrf 
                    @method('PUT')
                    <div class="modal-header bg-primary text-white py-2">
                        <h6 class="modal-title fw-bold">
                            <i class="bi bi-pencil-square me-1"></i> تعديل {{ $isMattress ? 'مرتبة' : 'سرير' }} {{ $bed->number }} ({{ $wing->name }})
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-3 text-start">
                        <div class="mb-3">
                            <label class="form-label fw-bold small">نوع السرير / المرفق <span class="text-danger">*</span></label>
                            <select name="type" class="form-select">
                                <option value="standard" @selected(!$isMattress)>🛏️ سرير أساسي</option>
                                <option value="mattress" @selected($isMattress)>🛏️ مرتبة إضافية (طوارئ ضغط مرضى)</option>
                            </select>
                            <div class="form-text small">تظهر المراتب الإضافية بعلامة مميزة في مخطط الحجز.</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">رقم أو تسمية السرير <span class="text-danger">*</span></label>
                            <input name="number" class="form-control" value="{{ $bed->number }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold small">حالة السرير</label>
                            <select name="status" class="form-select" @if($occupied) disabled @endif>
                                <option value="available" @selected($bed->status === 'available')>فارغ ومتاح للحجز</option>
                                <option value="maintenance" @selected($bed->status === 'maintenance')>تحت الصيانة</option>
                            </select>
                            @if($occupied)
                                <input type="hidden" name="status" value="{{ $bed->status }}">
                                <div class="form-text text-danger small">السرير مشغول بنزيل مقيم حالياً.</div>
                            @endif
                        </div>
                        <div class="mb-2">
                            <label class="form-label fw-bold small">ملاحظات على السرير</label>
                            <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات خاصة بهذا السرير">{{ $bed->notes }}</textarea>
                        </div>
                    </div>
                    <div class="modal-footer py-2 d-flex justify-content-between">
                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary btn-sm fw-bold">حفظ التعديل</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
@endforeach

{{-- ======================================================
     Modal: تسجيل مصروف نثري فوري (Petty Cash Expense)
     ====================================================== --}}
<div class="modal fade" id="modalAddPettyExpense" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content shadow border-0" method="POST" enctype="multipart/form-data" action="{{ route('guest-houses.petty-cash.expenses.store', $guest_house) }}">
            @csrf
            <div class="modal-header bg-success text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-cash-coin me-1"></i> تسجيل مصروف نثري فوري من عهدة الدار
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="mb-3">
                    <label class="form-label fw-bold small">بند المصروف / البيان <span class="text-danger">*</span></label>
                    <input name="title" class="form-control" placeholder="مثال: شراء خضروات طازجة لإعداد وجبة الغداء" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">المبلغ (ج.م) <span class="text-danger">*</span></label>
                        <input type="number" step="0.5" min="1" name="amount" class="form-control font-monospace" placeholder="مثال: 150" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">تاريخ الصرف <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" value="{{ now()->toDateString() }}" class="form-control" required>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">تصنيف المصروف</label>
                        <select name="category" class="form-select">
                            <option value="food">🥦 شراء مواد غذائية وطازجة</option>
                            <option value="medical_emergency">💊 طوارئ علاجية وإسعافية</option>
                            <option value="cleaning">🧹 أدوات نظافة ومطهرات</option>
                            <option value="maintenance">🔧 صيانة سريعة وأعطال</option>
                            <option value="transportation">🚕 مواصلات عامة</option>
                            <option value="general">📦 نثريات وضيافة عامة</option>
                        </select>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">الخزينة المنصرف منها <span class="text-danger">*</span></label>
                        <select name="treasury_id" class="form-select" required>
                            @php
                                $listedTreasuryIds = [];
                            @endphp
                            @foreach($pettyCashCustodies as $pCustody)
                                @if($pCustody->treasury)
                                    @php $listedTreasuryIds[] = $pCustody->treasury_id; @endphp
                                    <option value="{{ $pCustody->treasury_id }}">
                                        {{ $pCustody->treasury->name }} — {{ $pCustody->name }} (رصيد: {{ number_format((float)$pCustody->treasury->current_balance, 2) }} ج.م)
                                    </option>
                                @endif
                            @endforeach
                            @foreach($guestHouseExpenseTreasuries as $tr)
                                @if(!in_array($tr->id, $listedTreasuryIds))
                                    <option value="{{ $tr->id }}">{{ $tr->name }} (رصيد: {{ number_format((float)$tr->current_balance, 2) }} ج.م)</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small"><i class="bi bi-camera me-1"></i> صورة الإيصال أو الفاتورة (مستحسن للتوثيق)</label>
                    <input type="file" name="receipt" class="form-control form-control-sm" accept="image/*,.pdf">
                    <div class="form-text small">يمكنك التقاط صورة الفاتورة مباشرة من الهاتف أو رفع ملف PDF.</div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">ملاحظات إضافية</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="أي تفاصيل أو ملاحظات بخصوص عملية الشراء..."></textarea>
                </div>
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">
                    <i class="bi bi-check-lg me-1"></i> تسجيل وخصم من العهدة
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================
     Modal: استعاضة وتغذية العهدة النثرية (Petty Cash Replenish)
     ====================================================== --}}
<div class="modal fade" id="modalReplenishPettyCash" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.petty-cash.replenish', $guest_house) }}">
            @csrf
            <div class="modal-header bg-warning text-dark py-2">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-arrow-repeat me-1"></i> استعاضة وتغذية العهدة النثرية
                </h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-info py-2 small mb-3">
                    <i class="bi bi-info-circle me-1"></i> يتم تحويل رصيد نقدي من الخزينة الرئيسية أو البنكية لتغذية صندوق العهدة النثرية بالدار.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">خزينة العهدة النثرية المستلمة (الدار) <span class="text-danger">*</span></label>
                    <select name="target_treasury_id" class="form-select" required>
                        @php $listedTargetTreasuryIds = []; @endphp
                        @foreach($pettyCashCustodies as $pCustody)
                            @if($pCustody->treasury)
                                @php $listedTargetTreasuryIds[] = $pCustody->treasury_id; @endphp
                                <option value="{{ $pCustody->treasury_id }}">{{ $pCustody->treasury->name }} — {{ $pCustody->name }} (رصيد حالي: {{ number_format((float)$pCustody->treasury->current_balance, 2) }} ج.م)</option>
                            @endif
                        @endforeach
                        @foreach($guestHouseExpenseTreasuries as $tr)
                            @if(!in_array($tr->id, $listedTargetTreasuryIds))
                                <option value="{{ $tr->id }}">{{ $tr->name }} (رصيد حالي: {{ number_format((float)$tr->current_balance, 2) }} ج.م)</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">الخزينة المحول منها (المصدر الرئيسي) <span class="text-danger">*</span></label>
                    <select name="source_treasury_id" class="form-select" required>
                        <option value="">— اختر الخزينة المصدر —</option>
                        @foreach(($allSourceTreasuries ?? $guestHouseDonationTreasuries) as $srcTr)
                            <option value="{{ $srcTr->id }}">{{ $srcTr->name }} (الرصيد المتاح: {{ number_format((float)$srcTr->current_balance, 2) }} {{ $srcTr->currency }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">مبلغ التغذية / الاستعاضة (ج.م) <span class="text-danger">*</span></label>
                    <input type="number" step="1" min="10" name="amount" class="form-control font-monospace" placeholder="مثال: 5000" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">البيان وملاحظات الاستعاضة</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="مثال: استعاضة شهرية لمصروفات دار الضيافة بموجب فواتير شهر ..."></textarea>
                </div>
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-warning btn-sm fw-bold px-3 text-dark">
                    <i class="bi bi-check-lg me-1"></i> تأكيد التحويل وتغذية الصندوق
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================
     Modal: إضافة جهاز أو معدة جديدة للدار (Add Asset)
     ====================================================== --}}
<div class="modal fade" id="modalAddAsset" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.assets.store', $guest_house) }}">
            @csrf
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-cpu me-1"></i> تسجيل جهاز أو معدة جديدة بعهدة دار الضيافة
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3">
                    <div class="col-md-5">
                        <label class="form-label fw-bold small">اسم الجهاز أو المعدة <span class="text-danger">*</span></label>
                        <input name="name" class="form-control" placeholder="مثال: مولد أكسجين طبي 10 لتر، غسالة ملابس 12 كيلو، تكييف سبليت..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold small">العدد / الكمية <span class="text-danger">*</span></label>
                        <input type="number" min="1" name="quantity" value="1" class="form-control font-monospace" required placeholder="مثال: 1">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">التصنيف الأساسي <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="medical">🩺 جهاز طبي ومساعد (أكسجين، كراسي، قياس، سراير)</option>
                            <option value="electrical">⚡ كهربائي ومنزلي (تكييف، ثلاجة، غسالة، شاشة، سخان)</option>
                            <option value="facility">🏢 تجهيزات ومرافق (أثاث، مواتير، كاميرات)</option>
                            <option value="other">📦 أخرى</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">كود الجهاز / السيريال</label>
                        <input name="code" class="form-control font-monospace" placeholder="مثال: MED-01 أو اتركه للتوليد التلقائي">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">الماركة والموديل</label>
                        <input name="brand_model" class="form-control" placeholder="مثال: شارب 2.25 حصان / فيليبس">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">الحالة التشغيلية الأولية</label>
                        <select name="status" class="form-select">
                            <option value="working">🟢 يعمل بكفاءة وجاهز</option>
                            <option value="needs_maintenance">🟡 يحتاج فحص أو صيانة دورية</option>
                            <option value="out_of_service">🔴 خارج الخدمة / معطل</option>
                        </select>
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">الجناح التابع له</label>
                        <select name="guest_house_wing_id" class="form-select">
                            <option value="">— عهدة عامة بالدار (غير مقيد بجناح) —</option>
                            @foreach($guestHouseWings as $wing)
                                <option value="{{ $wing->id }}">{{ $wing->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">تفاصيل الموقع الدقيق</label>
                        <input name="location_details" class="form-control" placeholder="مثال: غرفة 2، المطبخ الرئيسي، مكتب الاستقبال، الصالة">
                    </div>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">تاريخ التوريد أو الشراء</label>
                        <input type="date" name="purchase_date" value="{{ now()->toDateString() }}" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">انتهاء فترة الضمان</label>
                        <input type="date" name="warranty_until" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">القيمة التقديرية (ج.م)</label>
                        <input type="number" step="1" name="estimated_cost" class="form-control font-monospace" placeholder="مثال: 15000">
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold small">ملاحظات ومصدر الجهاز (شراء / تبرع عيني)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="مثال: تبرع عيني من فاعل خير أو شراء من ميزانية الدار"></textarea>
                </div>
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">
                    <i class="bi bi-check-lg me-1"></i> حفظ الجهاز بعهدة الدار
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================
     Modal: تسجيل مكالمة أو بلاغ دعم (Support Ticket / Call)
     ====================================================== --}}
<div class="modal fade" id="modalAddSupportTicket" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.support-tickets.store', $guest_house) }}">
            @csrf
            <div class="modal-header bg-primary text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-headset me-1"></i> تسجيل مكالمة أو طلب دعم / بلاغ بدار الضيافة
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="row g-2 mb-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">اتجاه الاتصال <span class="text-danger">*</span></label>
                        <select name="direction" class="form-select" required>
                            <option value="inbound">📞 مكالمة واردة من النزيل / الجهة</option>
                            <option value="outbound">📲 اتصال صادر من الدار للمتابعة</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">صفة المتصل <span class="text-danger">*</span></label>
                        <select name="caller_type" class="form-select" required>
                            <option value="resident">نزيل مقيم حالياً بالدار</option>
                            <option value="companion">مرافق نزيل</option>
                            <option value="prospective_patient">مريض قادم يستفسر عن حجز</option>
                            <option value="hospital">جهة طبية / مستشفى أو معهد أورام</option>
                            <option value="donor">متبرع أو داعم</option>
                            <option value="other">أخرى</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold small">ربط بنزيل مقيم (اختياري)</label>
                        <select name="beneficiary_id" class="form-select">
                            <option value="">— غير مرتبط بنزيل محدد —</option>
                            @foreach($residentStays as $stay)
                                <option value="{{ $stay->beneficiary_id }}">{{ $stay->beneficiary->full_name }} ({{ $stay->bed?->wing?->name }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">اسم المتصل / صاحب البلاغ <span class="text-danger">*</span></label>
                        <input name="caller_name" class="form-control" placeholder="مثال: أحمد محمود" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">رقم الهاتف <span class="text-danger">*</span></label>
                        <input name="caller_phone" class="form-control font-monospace" placeholder="مثال: 01012345678" required>
                    </div>
                </div>

                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">قسم البلاغ / التصنيف <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="inquiry">📞 استفسارات الحجز ومواعيد الوصول وإرشادات الطريق</option>
                            <option value="treatment_followup">🏥 متابعة تلقي جلسات الكيماوي/الإشعاعي بالمستشفى</option>
                            <option value="resident_request">🛎️ طلبات ودعم النزلاء (كرسي متحرك، تمريض، وجبة خاصة)</option>
                            <option value="emergency">🚨 طوارئ طبية وطلب سيارة إسعاف أو تحويل عاجل</option>
                            <option value="feedback">⭐ استبيان وتقييم رضا ومغادرة</option>
                            <option value="general">ℹ️ استفسارات عامة ومواعيد الدار</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">مستوى الأولوية <span class="text-danger">*</span></label>
                        <select name="priority" class="form-select" required>
                            <option value="normal">🟢 عادي (متابعة عادية)</option>
                            <option value="high">🟡 هام (يحتاج استجابة سريعة)</option>
                            <option value="emergency">🔴 طوارئ وإسعاف عاجل</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">موضوع المكالمة / الطلب <span class="text-danger">*</span></label>
                    <input name="subject" class="form-control" placeholder="مثال: طلب كرسي متحرك للحالة لنقلها للمستشفى غداً" required>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold small">تفاصيل المكالمة والطلب</label>
                    <textarea name="details" class="form-control" rows="2" placeholder="اكتب تفاصيل المحادثة أو الطلب بالتفصيل..."></textarea>
                </div>

                <div class="row g-2 mb-2">
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">الإجراء الأولي المتخذ</label>
                        <input name="action_taken" class="form-control" placeholder="مثال: تم إبلاغ المشرف وتجهيز الكرسي المتحرك">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold small">الحالة الأولية</label>
                        <select name="status" class="form-select">
                            <option value="open">⏳ قيد المتابعة والحل</option>
                            <option value="resolved">✅ تم الحل فوراً أثناء المكالمة</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">
                    <i class="bi bi-check-lg me-1"></i> حفظ البلاغ في السجل
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Emergency Quick Shortcut --}}
<div class="modal fade" id="modalAddSupportTicketEmergency" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.support-tickets.store', $guest_house) }}">
            @csrf
            <input type="hidden" name="direction" value="inbound">
            <input type="hidden" name="category" value="emergency">
            <input type="hidden" name="priority" value="emergency">
            <div class="modal-header bg-danger text-white py-2">
                <h6 class="modal-title fw-bold">
                    <i class="bi bi-ambulance me-1"></i> بلاغ طوارئ طبية وإسعاف عاجل
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-3">
                <div class="alert alert-danger py-2 small mb-3">
                    🚨 تسجيل حالة طوارئ عاجلة للمريض لطلب الإسعاف أو النقل الفوري للمستشفى.
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">النزيل أو المريض <span class="text-danger">*</span></label>
                    <select name="beneficiary_id" class="form-select" required>
                        <option value="">— اختر النزيل المقيم —</option>
                        @foreach($residentStays as $stay)
                            <option value="{{ $stay->beneficiary_id }}">{{ $stay->beneficiary->full_name }} ({{ $stay->bed?->wing?->name }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label fw-bold small">اسم المبلغ</label>
                        <input name="caller_name" class="form-control" placeholder="اسم المرافق أو المشرف" required>
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-bold small">هاتف التواصل</label>
                        <input name="caller_phone" class="form-control font-monospace" placeholder="رقم الهاتف" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">وصف الحالة الطارئة <span class="text-danger">*</span></label>
                    <input name="subject" class="form-control" placeholder="مثال: ضيق تنفس حاد وانخفاض أكسجين أو نزيف" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold small">التفاصيل والإجراء الفوري المتخذ</label>
                    <textarea name="details" class="form-control" rows="2" placeholder="مثال: تم الاتصال بهيئة الإسعاف رقم 123 ووضع المريض على مولد الأكسجين"></textarea>
                </div>
                <input type="hidden" name="caller_type" value="resident">
                <input type="hidden" name="status" value="open">
            </div>
            <div class="modal-footer py-2 d-flex justify-content-between">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger btn-sm fw-bold px-3">
                    <i class="bi bi-broadcast me-1"></i> تسجيل بلاغ الطوارئ فوراً
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ======================================================
     Modals: Individual Asset Actions (Status, Maintenance, History)
     ====================================================== --}}
@foreach($guestHouseAssets as $asset)
    {{-- Modal: Edit Asset Status --}}
    <div class="modal fade" id="modalEditAssetStatus_{{ $asset->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.assets.status', [$guest_house, $asset]) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-secondary text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i> تعديل حالة: {{ $asset->name }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3 text-start">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">الحالة التشغيلية للجهاز <span class="text-danger">*</span></label>
                        <select name="status" class="form-select">
                            <option value="working" @selected($asset->status === 'working')>🟢 يعمل بكفاءة</option>
                            <option value="needs_maintenance" @selected($asset->status === 'needs_maintenance')>🟡 يحتاج صيانة / فحص</option>
                            <option value="out_of_service" @selected($asset->status === 'out_of_service')>🔴 خارج الخدمة / معطل</option>
                            <option value="scrapped" @selected($asset->status === 'scrapped')>⚪ كهنة / تم التكهين</option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات الحالة</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات سبب التغيير...">{{ $asset->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold">حفظ الحالة</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Add Asset Maintenance --}}
    <div class="modal fade" id="modalAddAssetMaintenance_{{ $asset->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content shadow border-0" method="POST" enctype="multipart/form-data" action="{{ route('guest-houses.assets.maintenances.store', [$guest_house, $asset]) }}">
                @csrf
                <div class="modal-header bg-warning text-dark py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-tools me-1"></i> تسجيل صيانة / بلاغ عطل — {{ $asset->name }}
                    </h6>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">نوع الصيانة <span class="text-danger">*</span></label>
                            <select name="maintenance_type" class="form-select" required>
                                <option value="repair">🔧 إصلاح عطل مفاجئ</option>
                                <option value="periodic">🔄 صيانة وفحص دوري</option>
                                <option value="inspection">🔍 معاينة وتشخيص فني</option>
                                <option value="replacement">⚙️ استبدال قطع غيار</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">تاريخ الصيانة <span class="text-danger">*</span></label>
                            <input type="date" name="maintenance_date" value="{{ now()->toDateString() }}" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold small">وصف العطل أو أعمال الصيانة <span class="text-danger">*</span></label>
                        <textarea name="issue_description" class="form-control" rows="2" placeholder="اكتب تفاصيل العطل وما تم إصلاحه..." required></textarea>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">تكلفة الصيانة (ج.م)</label>
                            <input type="number" step="0.5" min="0" name="cost" class="form-control font-monospace" placeholder="مثال: 350">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">الفني أو مركز الصيانة</label>
                            <input name="technician_name" class="form-control" placeholder="اسم الفني / ورشة الصيانة">
                        </div>
                    </div>

                    {{-- Deduct from petty cash option --}}
                    <div class="p-2 border rounded-3 bg-light-subtle mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="deduct_from_custody" value="1" id="deductCustody_{{ $asset->id }}">
                            <label class="form-check-label fw-bold small text-success" for="deductCustody_{{ $asset->id }}">
                                💰 خصم تكلفة الصيانة فوراً من الخزينة النثرية للدار
                            </label>
                        </div>
                        <div class="mt-2">
                            <label class="form-label small text-muted mb-0">اختر خزينة العهدة للخصم:</label>
                            <select name="treasury_id" class="form-select form-select-sm">
                                @foreach($pettyCashCustodies as $pCustody)
                                    @if($pCustody->treasury)
                                        <option value="{{ $pCustody->treasury_id }}">{{ $pCustody->name }} (رصيد: {{ number_format((float)$pCustody->treasury->current_balance, 2) }} ج.م)</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">حالة الجهاز بعد الصيانة</label>
                            <select name="new_status" class="form-select">
                                <option value="working">🟢 يعمل بكفاءة (تم الإصلاح)</option>
                                <option value="needs_maintenance">🟡 ما زال يحتاج متابعة</option>
                                <option value="out_of_service">🔴 خارج الخدمة / غير قابل للإصلاح</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">فاتورة أو إيصال الصيانة</label>
                            <input type="file" name="invoice" class="form-control form-control-sm" accept="image/*,.pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-warning btn-sm fw-bold px-3 text-dark">
                        <i class="bi bi-check-lg me-1"></i> تسجيل الصيانة
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal: Asset History --}}
    <div class="modal fade" id="modalAssetHistory_{{ $asset->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content shadow border-0">
                <div class="modal-header bg-info text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-clock-history me-1"></i> سجل صيانة وإصلاحات: {{ $asset->name }} ({{ $asset->code ?: 'AS-'.$asset->id }})
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="d-flex flex-wrap gap-2 mb-3 p-2 bg-light rounded-3">
                        <span class="badge bg-white text-dark border">الموقع: {{ $asset->wing?->name ?: 'الدار' }} ({{ $asset->location_details ?: '—' }})</span>
                        <span class="badge bg-white text-dark border">إجمالي أعمال الصيانة: {{ $asset->maintenances->count() }}</span>
                        <span class="badge bg-white text-success border fw-bold">إجمالي التكلفة: {{ number_format((float)$asset->maintenances->sum('cost'), 2) }} ج.م</span>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-bordered table-sm align-middle mb-0">
                            <thead class="table-light small">
                                <tr>
                                    <th>التاريخ</th>
                                    <th>النوع</th>
                                    <th>وصف العطل والإصلاح</th>
                                    <th>الفني / الورشة</th>
                                    <th class="text-center">التكلفة</th>
                                    <th class="text-center">الفاتورة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($asset->maintenances as $m)
                                    <tr>
                                        <td class="font-monospace small">{{ $m->maintenance_date ? $m->maintenance_date->format('Y-m-d') : '—' }}</td>
                                        <td><span class="badge bg-light text-dark border">{{ $m->maintenance_type_label }}</span></td>
                                        <td class="small">{{ $m->issue_description }}</td>
                                        <td class="small text-muted">{{ $m->technician_name ?: '—' }}</td>
                                        <td class="text-center font-monospace fw-bold text-danger">{{ number_format((float)$m->cost, 2) }}</td>
                                        <td class="text-center">
                                            @if($m->invoice_url)
                                                <a href="{{ $m->invoice_url }}" target="_blank" class="btn btn-xs btn-outline-primary rounded-pill px-2">
                                                    عرض
                                                </a>
                                            @else
                                                <span class="text-muted small">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-3">لا توجد أعمال صيانة سابقة مسجلة لهذا الجهاز.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

{{-- ======================================================
     Modals: Individual Support Ticket Actions
     ====================================================== --}}
@foreach($guestHouseTickets as $ticket)
    <div class="modal fade" id="modalEditTicket_{{ $ticket->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.support-tickets.status', [$guest_house, $ticket]) }}">
                @csrf
                @method('PATCH')
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-pencil-square me-1"></i> متابعة البلاغ #{{ $ticket->id }} — {{ $ticket->caller_name }}
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="p-2 border rounded-3 bg-light mb-3 small">
                        <div><strong>الموضوع:</strong> {{ $ticket->subject }}</div>
                        <div class="text-muted mt-1">{{ $ticket->details }}</div>
                        @if($ticket->caller_phone)
                            <div class="mt-1 font-monospace">📞 {{ $ticket->caller_phone }}</div>
                        @endif
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">حالة الطلب <span class="text-danger">*</span></label>
                            <select name="status" class="form-select">
                                <option value="open" @selected($ticket->status === 'open')>⏳ قيد المتابعة (مفتوح)</option>
                                <option value="in_progress" @selected($ticket->status === 'in_progress')>⚙️ جارٍ التنفيذ</option>
                                <option value="resolved" @selected($ticket->status === 'resolved')>✅ تم الحل بنجاح</option>
                                <option value="closed" @selected($ticket->status === 'closed')>🔒 مغلق</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">تقييم رضا النزيل (استبيان)</label>
                            <select name="satisfaction_rating" class="form-select">
                                <option value="">— بدون تقييم —</option>
                                <option value="5" @selected($ticket->satisfaction_rating == 5)>⭐⭐⭐⭐⭐ ممتاز (5/5)</option>
                                <option value="4" @selected($ticket->satisfaction_rating == 4)>⭐⭐⭐⭐ جيد جداً (4/5)</option>
                                <option value="3" @selected($ticket->satisfaction_rating == 3)>⭐⭐⭐ مقبول (3/5)</option>
                                <option value="2" @selected($ticket->satisfaction_rating == 2)>⭐⭐ ضعيف (2/5)</option>
                                <option value="1" @selected($ticket->satisfaction_rating == 1)>⭐ غير راضٍ تماماً (1/5)</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">الإجراء المتخذ <span class="text-danger">*</span></label>
                        <input name="action_taken" class="form-control" value="{{ $ticket->action_taken }}" placeholder="اكتب ما تم اتخاذه لحل هذا الطلب..." required>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات الحل والإغلاق</label>
                        <textarea name="resolution_notes" class="form-control" rows="2" placeholder="ملاحظات إضافية حول التجاوب أو رأي النزيل...">{{ $ticket->resolution_notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">
                        <i class="bi bi-check-lg me-1"></i> حفظ وتحديث التذكرة
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach
@endpush
