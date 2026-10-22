@extends('layouts.app')
@section('content')
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up bg-primary shadow-sm mb-4" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 2.5rem 2rem; border-radius: 0 0 40px 40px;">
        <div class="hero-content">
            <div class="hero-greeting text-white mb-2 opacity-75 fw-bold">الضيافة والإيواء 🏠</div>
            <h1 class="hero-title fw-bold text-white mb-3" style="color: #ffffff !important;">تعديل دار الضيافة: {{ $guest_house->name }}</h1>
            <p class="hero-subtitle text-white opacity-75 mb-0" style="color: #ffffff !important;">تحديث بيانات الدار، إدارة الأجنحة والأسِرّة وتعديل طاقتها الاستيعابية بدقة.</p>
        </div>
        <div class="ms-auto d-flex gap-2">
            <a href="{{ route('guest-houses.show', $guest_house) }}" class="btn btn-sm rounded-pill px-4 btn-light fw-bold hover-lift shadow-sm">
                <i class="bi bi-door-open-fill me-1"></i> تشغيل الدار
            </a>
            <a href="{{ route('guest-houses.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light fw-bold hover-lift" style="border-width: 2px;">
                <i class="bi bi-arrow-right me-1"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="container-fluid px-4 pb-5">
        {{-- Card 1: Basic Information --}}
        <div class="glass-card border-0 shadow-sm p-4 p-md-5 animate-slide-up mb-4">
            <div class="d-flex align-items-center gap-2 mb-4 border-bottom pb-3">
                <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                    <i class="bi bi-pencil-square fs-5"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">البيانات الأساسية للمرفق</h5>
                    <small class="text-muted">المعلومات الإدارية والموقع والمدير المسؤول</small>
                </div>
            </div>

            <form method="POST" action="{{ route('guest-houses.update', $guest_house) }}">
                @csrf 
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">اسم الدار <span class="text-danger">*</span></label>
                        <input name="name" class="form-control" value="{{ old('name', $guest_house->name) }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الموقع / العنوان</label>
                        <input name="location" class="form-control" value="{{ old('location', $guest_house->location) }}" placeholder="مثال: بجوار مستشفى الأورام">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المحافظة <span class="text-danger">*</span></label>
                        <select name="governorate" class="form-select" required>
                            <option value="كفر الشيخ" @selected(old('governorate', $guest_house->governorate) === 'كفر الشيخ')>كفر الشيخ</option>
                            <option value="الغربية" @selected(old('governorate', $guest_house->governorate) === 'الغربية')>الغربية</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">رقم الهاتف</label>
                        <input name="phone" class="form-control" value="{{ old('phone', $guest_house->phone) }}" placeholder="01xxxxxxxxx">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">السعة الكلية التقديرية (أفراد)</label>
                        <input name="capacity" type="number" class="form-control" value="{{ old('capacity', $guest_house->capacity) }}">
                        <div class="form-text">إجمالي الأسِرّة الفعلي المسجل حالياً: <strong class="text-primary">{{ $guest_house->beds()->count() }} سرير</strong></div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المدير المسؤول</label>
                        <select name="manager_user_id" class="form-select">
                            <option value="">-- اختر المدير --</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}" @selected(old('manager_user_id', $guest_house->manager_user_id) == $user->id)>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">الحالة التشغيلية</label>
                        <select name="status" class="form-select">
                            <option value="active" @selected(old('status', $guest_house->status) === 'active')>نشط</option>
                            <option value="archived" @selected(old('status', $guest_house->status) === 'archived')>مؤرشف</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">وصف وملاحظات عامة</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $guest_house->description) }}</textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">
                        <i class="bi bi-check2-circle me-1"></i> حفظ البيانات الأساسية
                    </button>
                    <a href="{{ route('guest-houses.show', $guest_house) }}" class="btn btn-light px-4">إلغاء</a>
                </div>
            </form>
        </div>

        {{-- Card 2: Wings and Beds Management --}}
        <div class="glass-card border-0 shadow-sm p-4 p-md-5 animate-slide-up">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4 border-bottom pb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                        <i class="bi bi-grid-3x3-gap-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">الأجنحة والأسِرّة في الدار</h5>
                        <small class="text-muted">إضافة أجنحة جديدة، تعديل بياناتها، وضبط عدد الأسِرّة داخل كل جناح</small>
                    </div>
                </div>
                <div>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm hover-lift" data-bs-toggle="modal" data-bs-target="#createWingModal">
                        <i class="bi bi-plus-circle me-1"></i> إضافة جناح جديد
                    </button>
                </div>
            </div>

            {{-- Wings Summary Counters --}}
            @php
                $allBeds = $guestHouseWings->flatMap->beds;
                $totalBedsCount = $allBeds->count();
                $occupiedCount = $allBeds->filter(fn($b) => $b->activeStay)->count();
                $maintenanceCount = $allBeds->where('status', 'maintenance')->count();
                $availableCount = $allBeds->where('status', 'available')->filter(fn($b) => !$b->activeStay)->count();
            @endphp
            <div class="row g-2 mb-4">
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-light border text-center">
                        <div class="text-muted small mb-1">إجمالي الأجنحة</div>
                        <div class="fw-bold fs-5 text-dark">{{ $guestHouseWings->count() }} جناح</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-light border text-center">
                        <div class="text-muted small mb-1">إجمالي الأسِرّة</div>
                        <div class="fw-bold fs-5 text-primary">{{ $totalBedsCount }} سرير</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-success bg-opacity-10 border border-success border-opacity-25 text-center">
                        <div class="text-success small fw-bold mb-1">أسِرّة متاحة</div>
                        <div class="fw-bold fs-5 text-success">{{ $availableCount }}</div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 bg-danger bg-opacity-10 border border-danger border-opacity-25 text-center">
                        <div class="text-danger small fw-bold mb-1">أسِرّة مشغولة</div>
                        <div class="fw-bold fs-5 text-danger">{{ $occupiedCount }}</div>
                    </div>
                </div>
            </div>

            {{-- Wings List --}}
            @forelse($guestHouseWings as $wing)
                @php
                    $wingBeds = $wing->beds;
                    $wTotal = $wingBeds->count();
                    $wOccupied = $wingBeds->filter(fn($b) => $b->activeStay)->count();
                    $wAvail = $wingBeds->where('status', 'available')->filter(fn($b) => !$b->activeStay)->count();
                    $wMaint = $wingBeds->where('status', 'maintenance')->count();
                @endphp
                <div class="card border rounded-3 mb-4 shadow-none bg-white">
                    <div class="card-header bg-light d-flex flex-wrap justify-content-between align-items-center py-3 px-3 gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-primary rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-grid me-1"></i> {{ $wing->name }}
                            </span>
                            <span class="badge bg-secondary bg-opacity-10 text-secondary border px-2 py-1">
                                {{ $wTotal }} سرير
                            </span>
                            @if(!$wing->is_active)
                                <span class="badge bg-warning text-dark">معطل</span>
                            @endif
                            @if($wing->notes)
                                <span class="text-muted small text-truncate d-none d-md-inline ms-2" style="max-width: 250px;" title="{{ $wing->notes }}">
                                    <i class="bi bi-info-circle me-1"></i>{{ $wing->notes }}
                                </span>
                            @endif
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            {{-- Edit Wing & Beds Count Button --}}
                            <button type="button" class="btn btn-sm btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#editWingModal_{{ $wing->id }}">
                                <i class="bi bi-pencil-square me-1"></i> تعديل الجناح والأسرة
                            </button>

                            {{-- Add Single Bed to Wing Button --}}
                            <button type="button" class="btn btn-sm btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#addBedModal_{{ $wing->id }}">
                                <i class="bi bi-plus-lg me-1"></i> إضافة سرير للجناح
                            </button>

                            {{-- Delete Wing Button (if no active stays) --}}
                            @if($wOccupied === 0)
                                <form method="POST" action="{{ route('guest-houses.wings.destroy', [$guest_house, $wing]) }}" class="d-inline" onsubmit="return confirm('تأكيد حذف الجناح ({{ $wing->name }}) وكافة أسِرّته؟');">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف الجناح">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @else
                                <button type="button" class="btn btn-sm btn-outline-secondary opacity-50" disabled title="لا يمكن حذف الجناح لوجود نزلاء مقيمين">
                                    <i class="bi bi-trash"></i>
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="card-body p-3">
                        <div class="small text-muted mb-2 d-flex justify-content-between">
                            <span>الأسِرّة المتاحة: <strong class="text-success">{{ $wAvail }}</strong> · المشغولة: <strong class="text-danger">{{ $wOccupied }}</strong> · الصيانة: <strong class="text-secondary">{{ $wMaint }}</strong></span>
                            <span class="text-muted">اضغط على أي سرير لتعديل رقمه أو حالته أو حذفه</span>
                        </div>

                        <div class="row g-2">
                            @forelse($wingBeds as $bed)
                                @php
                                    $isOcc = (bool) $bed->activeStay;
                                    $isMaint = $bed->status === 'maintenance';
                                    $patient = $bed->activeStay?->beneficiary;
                                @endphp
                                <div class="col-6 col-sm-4 col-md-3 col-xl-2">
                                    <div class="p-2 rounded-3 border text-center position-relative h-100 d-flex flex-column justify-content-between {{ $isOcc ? 'bg-danger bg-opacity-10 border-danger border-opacity-25' : ($isMaint ? 'bg-secondary bg-opacity-10 border-secondary border-opacity-25' : 'bg-success bg-opacity-10 border-success border-opacity-25') }}">
                                        <div>
                                            <div class="fw-bold mb-1 d-flex align-items-center justify-content-center gap-1 {{ $isOcc ? 'text-danger' : ($isMaint ? 'text-secondary' : 'text-success') }}">
                                                <i class="bi bi-hospital-bed"></i> سرير {{ $bed->number }}
                                            </div>
                                            <div class="small mb-1">
                                                @if($isOcc)
                                                    <span class="badge bg-danger py-1 px-2 text-truncate d-block" style="font-size: 0.72rem;" title="{{ $patient?->full_name }}">
                                                        {{ $patient?->full_name ?: 'نزيل مقيم' }}
                                                    </span>
                                                @elseif($isMaint)
                                                    <span class="badge bg-secondary py-1 px-2" style="font-size: 0.72rem;">تحت الصيانة</span>
                                                @else
                                                    <span class="badge bg-success py-1 px-2" style="font-size: 0.72rem;">فارغ (متاح)</span>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="d-flex justify-content-center gap-1 mt-2 pt-1 border-top border-light">
                                            {{-- Edit Bed Button --}}
                                            <button type="button" class="btn btn-xs btn-outline-dark py-0 px-2" style="font-size: 0.75rem;" data-bs-toggle="modal" data-bs-target="#editBedModal_{{ $bed->id }}" title="تعديل السرير">
                                                <i class="bi bi-pencil"></i> تعديل
                                            </button>

                                            {{-- Delete Bed Button --}}
                                            @if(!$isOcc)
                                                <form method="POST" action="{{ route('guest-houses.beds.destroy', [$guest_house, $bed]) }}" class="d-inline" onsubmit="return confirm('تأكيد حذف السرير ({{ $bed->number }})؟');">
                                                    @csrf 
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-xs btn-outline-danger py-0 px-1" style="font-size: 0.75rem;" title="حذف السرير">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal: Edit Single Bed --}}
                                <div class="modal fade" id="editBedModal_{{ $bed->id }}" tabindex="-1" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered modal-sm">
                                        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.beds.update', [$guest_house, $bed]) }}">
                                            @csrf 
                                            @method('PUT')
                                            <div class="modal-header bg-primary text-white py-2">
                                                <h6 class="modal-title fw-bold">
                                                    <i class="bi bi-pencil-square me-1"></i> تعديل سرير {{ $bed->number }} ({{ $wing->name }})
                                                </h6>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body p-3">
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">رقم السرير <span class="text-danger">*</span></label>
                                                    <input name="number" class="form-control" value="{{ old('number', $bed->number) }}" required>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label fw-bold small">حالة السرير <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" @if($isOcc) disabled @endif>
                                                        <option value="available" @selected($bed->status === 'available')>فارغ ومتاح للحجز</option>
                                                        <option value="maintenance" @selected($bed->status === 'maintenance')>تحت الصيانة</option>
                                                    </select>
                                                    @if($isOcc)
                                                        <input type="hidden" name="status" value="{{ $bed->status }}">
                                                        <div class="form-text text-danger small">السرير مشغول بنزيل حالياً ولا يمكن تغيير حالته لصيانة.</div>
                                                    @endif
                                                </div>
                                                <div class="mb-2">
                                                    <label class="form-label fw-bold small">ملاحظات على السرير</label>
                                                    <textarea name="notes" class="form-control" rows="2" placeholder="أي ملاحظات خاصة بهذا السرير">{{ old('notes', $bed->notes) }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer py-2 d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                                <button type="submit" class="btn btn-primary btn-sm fw-bold">حفظ التعديل</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 py-3 text-center text-muted">
                                    لا توجد أسِرّة مضافة في هذا الجناح بعد. استخدم زر "إضافة سرير للجناح" لإضافة أسِرّة.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                {{-- Modal: Edit Wing & Adjust Beds Count --}}
                <div class="modal fade" id="editWingModal_{{ $wing->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.wings.update', [$guest_house, $wing]) }}">
                            @csrf 
                            @method('PUT')
                            <div class="modal-header bg-primary text-white py-2">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-pencil-square me-1"></i> تعديل بيانات الجناح: {{ $wing->name }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold">اسم الجناح <span class="text-danger">*</span></label>
                                    <input name="name" class="form-control" value="{{ old('name', $wing->name) }}" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold">إجمالي عدد الأسِرّة في الجناح <span class="text-danger">*</span></label>
                                    <input type="number" name="beds_count" min="0" max="200" value="{{ $wing->beds->count() }}" class="form-control" required>
                                    <div class="form-text small">
                                        • إذا قمت بزيادة العدد (مثلاً من {{ $wing->beds->count() }} إلى {{ $wing->beds->count() + 2 }}): سيتم تلقائياً توليد أسِرّة جديدة بالأرقام التالية.<br>
                                        • إذا قمت بإنقاص العدد: سيتم حذف الأسِرّة الفارغة غير المشغولة فقط ولن يتأثر أي سرير به نزيل مقيم.
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
                                    <textarea name="notes" class="form-control" rows="2" placeholder="مثال: جناح خاص بالنساء / دور أرضي">{{ old('notes', $wing->notes) }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer py-2 d-flex justify-content-between">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">حفظ تعديلات الجناح</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Modal: Add Single Bed to This Wing --}}
                <div class="modal fade" id="addBedModal_{{ $wing->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-sm">
                        <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.wings.beds.store', [$guest_house, $wing]) }}">
                            @csrf
                            <div class="modal-header bg-success text-white py-2">
                                <h6 class="modal-title fw-bold">
                                    <i class="bi bi-plus-circle me-1"></i> إضافة سرير إلى: {{ $wing->name }}
                                </h6>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-3">
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">رقم السرير</label>
                                    <input name="number" class="form-control" placeholder="اختياري (يُترك فارغاً للتوليد التلقائي)">
                                    <div class="form-text small">إذا تركته فارغاً سيتم تحديد الرقم التالي تلقائياً.</div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label fw-bold small">الحالة الأولية</label>
                                    <select name="status" class="form-select">
                                        <option value="available">فارغ ومتاح للحجز</option>
                                        <option value="maintenance">تحت الصيانة</option>
                                    </select>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label fw-bold small">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات اختيارية"></textarea>
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
                <div class="text-center py-5 border rounded-3 bg-light">
                    <div class="text-muted mb-3"><i class="bi bi-grid-3x3-gap fs-1 opacity-50"></i></div>
                    <h6 class="fw-bold text-dark">لا توجد أجنحة مسجلة في دار الضيافة حتى الآن</h6>
                    <p class="text-muted small mb-3">ابدأ بإضافة أول جناح وتحديد عدد الأسِرّة به لتتمكن من تسكين الحالات وتشغيل الدار.</p>
                    <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createWingModal">
                        <i class="bi bi-plus-circle me-1"></i> إضافة جناح جديد الآن
                    </button>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Global Modal: Create New Wing --}}
    <div class="modal fade" id="createWingModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form class="modal-content shadow border-0" method="POST" action="{{ route('guest-houses.wings.store', $guest_house) }}">
                @csrf
                <div class="modal-header bg-primary text-white py-2">
                    <h6 class="modal-title fw-bold">
                        <i class="bi bi-grid-3x3-gap-fill me-1"></i> إضافة جناح جديد وتوليد الأسِرّة
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="mb-3">
                        <label class="form-label fw-bold">اسم الجناح <span class="text-danger">*</span></label>
                        <input name="name" class="form-control" placeholder="مثال: جناح 1 (رجال) / جناح 2 (نساء)" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">عدد الأسِرّة في الجناح <span class="text-danger">*</span></label>
                        <input type="number" name="beds_count" min="0" max="100" value="8" class="form-control" required>
                        <div class="form-text small">سيتم توليد هذه الأسِرّة بأرقام متسلسلة (من 1 إلى العدد المحدد) وتكون متاحة للحجز مباشرة.</div>
                    </div>
                    <div class="mb-2">
                        <label class="form-label fw-bold">ملاحظات الجناح</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="ملاحظات اختيارية (الموقع بالجناح، الدور، إلخ)"></textarea>
                    </div>
                </div>
                <div class="modal-footer py-2 d-flex justify-content-between">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary btn-sm fw-bold px-3">إضافة الجناح والأسِرّة</button>
                </div>
            </form>
        </div>
    </div>
@endsection
