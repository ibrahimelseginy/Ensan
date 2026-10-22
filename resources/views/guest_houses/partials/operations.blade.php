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
                <span><i class="gh-status-dot" style="background:#16803c"></i> فارغ متاح</span>
                <span><i class="gh-status-dot" style="background:#c62828"></i> مقيم بالدار</span>
                <span><i class="gh-status-dot" style="background:#d97706"></i> خارج للعلاج (مؤقت)</span>
                <span><i class="gh-status-dot" style="background:#64748b"></i> صيانة</span>
            </div>
            @forelse($guestHouseWings as $wing)
                <div class="border rounded-3 p-3 mb-3">
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <strong class="fs-6">{{ $wing->name }}</strong>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border">{{ $wing->beds->count() }} سرير</span>
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
                            @endphp

                            @if($occupied)
                                {{-- سرير محجوز (مقيم أو خارج للعلاج مؤقتاً) --}}
                                <button type="button" 
                                        class="gh-bed {{ $isTempOut ? 'gh-bed-temporary-out' : 'gh-bed-occupied' }}" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bedStayDetailsModal_{{ $bed->id }}" 
                                        title="{{ $isTempOut ? 'السرير محجوز (الحالة خارج للعلاج مؤقتاً)' : 'السرير محجوز (مقيم بالدار)' }}">
                                    <i class="bi {{ $isTempOut ? 'bi-clock-history' : 'bi-person-fill' }} fs-5"></i>
                                    <span>سرير {{ $bed->number }}</span>
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
                                                        <div class="text-muted small">كود: <span class="badge bg-secondary-subtle text-dark">{{ $patient?->code ?: '—' }}</span> | قومي: {{ $patient?->national_id ?: 'غير مسجل' }}</div>
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
                                <button type="button" 
                                        class="gh-bed gh-bed-available" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#bedOptionsModal_{{ $bed->id }}" 
                                        title="سرير متاح: اضغط لاختيار التسكين أو الصيانة أو التعديل">
                                    <i class="bi bi-hospital-bed fs-5"></i>
                                    <span>سرير {{ $bed->number }}</span>
                                    <small>فارغ (متاح)</small>
                                </button>

                                {{-- مودال خيارات السرير المتاح --}}
                                <div class="modal fade" id="bedOptionsModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <div class="modal-content shadow border-0 text-center">
                                            <div class="modal-header bg-success text-white py-2 justify-content-center position-relative">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-hospital-bed me-1"></i> {{ $wing->name }} — سرير {{ $bed->number }}
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white position-absolute end-0 me-2" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <p class="text-muted small mb-3">السرير متاح حالياً للحجز. اختر الإجراء المطلوب:</p>
                                                <div class="d-grid gap-2">
                                                    <button type="button" class="btn btn-primary fw-bold py-2" data-bs-dismiss="modal" onclick="openBeneficiaryModalWithBed('{{ $bed->id }}', '{{ $wing->name }} — سرير {{ $bed->number }}')">
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
                            @endif

                            {{-- Modal: Edit Single Bed (Operations) --}}
                            <div class="modal fade" id="opsEditBedModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-sm">
                                    <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.beds.update', [$guest_house, $bed]) }}">
                                        @csrf 
                                        @method('PUT')
                                        <div class="modal-header bg-primary text-white py-2">
                                            <h6 class="modal-title fw-bold">
                                                <i class="bi bi-pencil-square me-1"></i> تعديل سرير {{ $bed->number }}
                                            </h6>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body p-3 text-start">
                                            <div class="mb-3">
                                                <label class="form-label fw-bold small">رقم السرير <span class="text-danger">*</span></label>
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
                                                <label class="form-label fw-bold small">ملاحظات</label>
                                                <textarea name="notes" class="form-control" rows="2">{{ $bed->notes }}</textarea>
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
                    </div>
                </div>

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
                                    <i class="bi bi-plus-circle me-1"></i> إضافة سرير إلى {{ $wing->name }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">رقم السرير</label>
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
                                    <label class="form-label fw-bold small">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="modal-footer py-2 d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-success btn-sm fw-bold px-3">إضافة السرير</button>
                            </div>
                        </form>
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
                    <small class="text-muted">متابعة النزلاء الحاليين، ملاحظات وسلبيات الإقامة، وتسجيل مغادرة الحالات بشكل فردي</small>
                </div>
            </div>

            <div class="table-responsive mb-4">
                <table class="table align-middle table-hover border">
                    <thead class="table-light"><tr><th>المستفيد</th><th>الجناح / السرير</th><th>تاريخ الوصول</th><th>مدة الإقامة</th><th>ملاحظات الدار (السلبيات)</th><th>إجراء المغادرة</th></tr></thead>
                    <tbody>
                    @forelse($residentStays as $stay)
                        @php
                            $patientProfile = $stay->beneficiary->patientProfile;
                            $hasNotes = !empty($patientProfile?->manager_notes);
                        @endphp
                        <tr>
                            <td>
                                <strong>{{ $stay->beneficiary->full_name }}</strong>
                                <div class="small text-muted">{{ $stay->beneficiary->phone ?: 'بدون هاتف' }}</div>
                            </td>
                            <td>
                                <span class="badge {{ $stay->is_temporary_out ? 'bg-warning-subtle text-warning border border-warning' : 'bg-primary-subtle text-primary border' }} px-2 py-1">
                                    {{ $stay->bed?->wing?->name ?: '—' }} / سرير {{ $stay->bed?->number ?: '—' }}
                                </span>
                                @if($stay->is_temporary_out)
                                    <div class="small text-warning fw-bold mt-1"><i class="bi bi-clock-history me-1"></i> خارج للعلاج</div>
                                @endif
                            </td>
                            <td>{{ $stay->arrival_date?->format('Y-m-d') }}</td>
                            <td>{{ $stay->expected_days ? $stay->expected_days . ' أيام' : 'غير محدد' }}</td>
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
                    @empty <tr><td colspan="6" class="text-center text-muted py-3">لا توجد حالات مقيمة حاليًا بالدار.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-3 pt-3 border-top">
                <div>
                    <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-arrow-repeat me-1"></i> آخر الحالات المغادرة — تسجيل عودة الحالة للدار</h6>
                    <small class="text-muted">عند عودة المريض للدار في أي وقت، اختر السرير ومدة الإقامة المتوقعة لتسكينه فوراً</small>
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
                                        <i class="bi bi-box-arrow-in-right me-1"></i> تسكين مجدداً / عودة مقيم
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

                <label class="form-label form-label-required">نوع العهدة</label>
                <select name="type" id="gh_custody_type" class="form-select mb-2" required onchange="toggleCustodyTypeFields(this.value)">
                    <option value="financial">عهدة مالية</option>
                    <option value="in_kind">عهدة عينية (مخزون أصناف)</option>
                </select>

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
                        <span class="small fw-bold text-primary d-block mb-1"><i class="bi bi-plus-circle me-1"></i> رصيد افتتاحي (اختياري عند الإنشاء):</span>
                        <div class="row g-2">
                            <div class="col-7">
                                <select name="initial_item_id" class="form-select form-select-sm">
                                    <option value="">— اختر الصنف —</option>
                                    @foreach($guestHouseItems as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-5">
                                <input type="number" step="0.001" min="0.001" name="initial_quantity" class="form-control form-control-sm" placeholder="الكمية">
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

                <label class="form-label form-label-required">الصنف المراد توريده</label>
                <select name="item_id" class="form-select mb-2" required>
                    <option value="">— اختر الصنف —</option>
                    @foreach($guestHouseItems as $item)
                        <option value="{{ $item->id }}">{{ $item->name }} ({{ $item->unit }})</option>
                    @endforeach
                </select>

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
