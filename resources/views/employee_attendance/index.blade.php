@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);">
    <div class="hero-content">
        <div class="hero-greeting">الموارد البشرية ⏰</div>
        <h1 class="hero-title">سجل حضور الموظفين</h1>
        <p class="hero-subtitle">تسجيل ومتابعة حضور وانصراف الموظفين</p>
        <div class="hero-actions d-flex gap-2 flex-wrap align-items-center">
            @php
                $u = auth()->user();
                $myTodayAtt = $u
                    ? \App\Models\EmployeeAttendance::where('user_id', $u->id)->where('date', now('Africa/Cairo')->toDateString())->first()
                    : null;
                $isKafrStaff = $u && (
                    (int) $u->guest_house_id === 2
                    || (int) $u->id === 74
                    || str_contains((string) $u->job_title, 'كفر الشيخ')
                    || str_contains((string) $u->job_title, 'مدير دار الضيافة')
                    || (str_contains((string) $u->department, 'دار الضيافة') && !str_contains((string) $u->job_title, 'الغربيه'))
                );
                $primaryLocationName = $isKafrStaff ? 'دار ضيافة كفر الشيخ' : 'مقر المؤسسة الرئيسي';
                $directionsUrl = $isKafrStaff
                    ? 'https://www.google.com/maps/dir/?api=1&destination=31.1061705,30.9378786'
                    : 'https://www.google.com/maps/dir/?api=1&destination=31.1112969,30.9489307';
            @endphp
            @if($myTodayAtt && empty($myTodayAtt->check_out_at))
                <button type="button" onclick="startAttendanceCapture('checkout')" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill px-4 shadow-sm">
                    <i class="bi bi-box-arrow-left me-1"></i> تسجيل انصراف الآن بالصورة — حضور: {{ $myTodayAtt->check_in_at }}
                </button>
            @elseif($myTodayAtt && !empty($myTodayAtt->check_out_at))
                <span class="badge rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1" style="background: #ffffff !important; color: #047857 !important; border: 1px solid rgba(255, 255, 255, 0.9); box-shadow: 0 4px 12px rgba(0, 0, 0, 0.14); font-weight: 700; font-size: 0.84rem;">
                    <i class="bi bi-check-circle-fill" style="color: #10b981 !important;"></i>
                    <span style="color: #047857 !important;">تم تسجيل الحضور ({{ $myTodayAtt->check_in_at }}) والانصراف ({{ $myTodayAtt->check_out_at }})</span>
                </span>
            @else
                <button type="button" onclick="startAttendanceCapture('checkin')" class="btn btn-light text-primary fw-bold btn-sm rounded-pill px-4 shadow-sm">
                    <i class="bi bi-camera-fill text-danger me-1"></i> تسجيل الحضور بالصورة والموقع (GPS)
                </button>
            @endif

            <a href="{{ $directionsUrl }}" target="_blank" rel="noopener" class="btn btn-sm rounded-pill px-4 fw-bold" style="background: rgba(255,255,255,0.2); color: #ffffff; border: 1px solid rgba(255,255,255,0.45);">
                <i class="bi bi-sign-turn-right-fill me-1"></i> اتجاهات {{ $primaryLocationName }} (Directions)
            </a>

            @if(!empty($isHrOrAdmin))
                <a href="{{ route('employee-attendance.devices') }}" class="btn btn-sm rounded-pill px-3.5 fw-bold" style="background: rgba(255,255,255,0.22); color: #ffffff; border: 1px solid rgba(255,255,255,0.5);" title="إدارة واعتماد أجهزة الكمبيوتر المكتبية للمقرات">
                    <i class="bi bi-display me-1"></i> أجهزة بصمة المقرات
                </a>

                <div class="dropdown d-inline-block">
                    <button class="btn btn-sm rounded-pill px-3 fw-bold dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: rgba(255,255,255,0.15); color: #ffffff; border: 1px solid rgba(255,255,255,0.35);">
                        <i class="bi bi-geo-alt me-1"></i> مقرات الحضور
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                        <li><a class="dropdown-item small" href="https://www.google.com/maps/dir/?api=1&destination=31.1112969,30.9489307" target="_blank" rel="noopener"><i class="bi bi-building text-primary me-2"></i>المقر الرئيسي للمؤسسة</a></li>
                        <li><a class="dropdown-item small" href="https://www.google.com/maps/dir/?api=1&destination=31.1061705,30.9378786" target="_blank" rel="noopener"><i class="bi bi-house-door text-success me-2"></i>دار ضيافة كفر الشيخ</a></li>
                        <li><a class="dropdown-item small" href="https://www.google.com/maps/dir/?api=1&destination=30.8038626,30.9969359" target="_blank" rel="noopener"><i class="bi bi-geo-alt text-warning me-2"></i>دار ضيافة طنطا</a></li>
                    </ul>
                </div>
            @endif

            <a href="{{ route('employee-attendance.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> تسجيل يدوي
            </a>
        </div>
        <div id="gpsCheckInFeedback" class="mt-2 small fw-bold d-none" style="background: rgba(255,255,255,0.95); color: #0f172a; padding: 8px 14px; border-radius: 12px; display: inline-block;"></div>
    </div>
    <i class="bi bi-calendar-check-fill hero-icon d-none d-md-block"></i>
</div>

{{-- بطاقات مؤشرات الحضور والتأخير اليومية --}}
<div class="row g-3 mb-4 animate-slide-up">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); border-right: 4px solid #16a34a !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-success">حضور اليوم الإجمالي</span>
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-dark mb-0">{{ $totalToday ?? 0 }} <span class="fs-6 text-muted fw-normal">موظف</span></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%); border-right: 4px solid #059669 !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-success">في الموعد (قبل 10:15)</span>
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-check2-circle"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-success mb-0">{{ $onTimeToday ?? 0 }} <span class="fs-6 text-muted fw-normal">منضبط</span></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #fff7ed 0%, #ffedd5 100%); border-right: 4px solid #ea580c !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold" style="color: #c2410c;">متأخرون (بعد 10:15)</span>
                <div class="rounded-circle d-flex align-items-center justify-content-center text-white" style="background: #ea580c; width: 32px; height: 32px;">
                    <i class="bi bi-clock-history"></i>
                </div>
            </div>
            <div class="h3 fw-bold mb-0" style="color: #ea580c;">{{ $lateToday ?? 0 }} <span class="fs-6 text-muted fw-normal">متأخر</span></div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%); border-right: 4px solid #64748b !important;">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="small fw-bold text-secondary">موعد الحضور الرسمي</span>
                <div class="bg-secondary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-alarm-fill"></i>
                </div>
            </div>
            <div class="h3 fw-bold text-dark mb-0">10:15 <span class="fs-6 text-muted fw-normal">صباحاً</span></div>
            <div class="small text-muted mt-1" style="font-size: 0.72rem;">يُحسب التأخير تلقائياً لأي تسجيل بعد 10:15</div>
        </div>
    </div>
</div>

@if(!empty($isHrOrAdmin))
{{-- شريط الفلترة والبحث للموارد البشرية والإدارة --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 p-3 bg-white animate-slide-up">
    <form method="GET" action="{{ route('employee-attendance.index') }}" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-person me-1"></i>تصفية بالموظف</label>
            <select name="user_id" class="form-select form-select-sm rounded-pill">
                <option value="">-- كافة الموظفين --</option>
                @foreach($usersList ?? [] as $emp)
                    <option value="{{ $emp->id }}" {{ (string)($filters['user_id'] ?? '') === (string)$emp->id ? 'selected' : '' }}>
                        {{ $emp->name }} ({{ $emp->job_title ?: 'موظف' }})
                    </option>
                @endforeach
            </select>
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-calendar3 me-1"></i>التاريخ</label>
            <input type="date" name="date" value="{{ $filters['date'] ?? '' }}" class="form-control form-control-sm rounded-pill">
        </div>
        <div class="col-6 col-md-3">
            <label class="form-label small fw-bold text-secondary mb-1"><i class="bi bi-funnel me-1"></i>حالة الانضباط والتأخير</label>
            <select name="delay_filter" class="form-select form-select-sm rounded-pill">
                <option value="">-- كل الحالات --</option>
                <option value="late" {{ ($filters['delay_filter'] ?? '') === 'late' ? 'selected' : '' }}>⏰ المتأخرون فقط (بعد 10:15)</option>
                <option value="on_time" {{ ($filters['delay_filter'] ?? '') === 'on_time' ? 'selected' : '' }}>✅ في الموعد (قبل 10:15)</option>
            </select>
        </div>
        <div class="col-12 col-md-2 d-flex gap-1">
            <button type="submit" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold">
                <i class="bi bi-search me-1"></i> بحث
            </button>
            @if(!empty($filters['user_id']) || !empty($filters['date']) || !empty($filters['delay_filter']))
                <a href="{{ route('employee-attendance.index') }}" class="btn btn-light btn-sm rounded-pill text-muted" title="إلغاء الفلتر">
                    <i class="bi bi-x-lg"></i>
                </a>
            @endif
        </div>
    </form>
</div>
@endif

<div class="chart-container animate-slide-up animate-delay-1">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3 px-4">
             <h5 class="mb-0 fw-bold text-primary">السجل ({{ $records->total() }})</h5>
             <div class="d-none animate-fade-in" id="bulk-actions">
                 <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2" onclick="submitBulk('destroy')">
                     <i class="bi bi-trash"></i> حذف المحدد
                 </button>
             </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase">
                            <th class="py-3 px-4" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="check-all" onchange="toggleAll(this)">
                            </th>
                            <th class="py-3 px-4">الموظف</th>
                            <th class="py-3 px-4">التاريخ</th>
                            <th class="py-3 px-4">وقت الدخول والانضباط</th>
                            <th class="py-3 px-3 text-center">صورة الحضور</th>
                            <th class="py-3 px-4">وقت الخروج</th>
                            <th class="py-3 px-3 text-center">صورة الانصراف</th>
                            <th class="py-3 px-4">ملاحظات</th>
                            <th class="py-3 px-4">التقييم</th>
                            <th class="py-3 px-4 text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($records as $r)
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input bulk-item" value="{{ $r->id }}" onchange="updateBulkUI()">
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px; font-weight: bold;">
                                            {{ strtoupper(substr($r->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="fw-medium d-block">{{ $r->user?->name }}</span>
                                            <small class="text-muted" style="font-size: 0.75rem;">{{ $r->user?->job_title ?: 'موظف' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 text-muted">{{ $r->date->format('Y-m-d') }}</td>
                                <td class="px-4">
                                    @if(!empty($r->check_in_at))
                                        <div class="d-flex flex-column gap-1">
                                            <span class="badge bg-light text-dark border rounded-pill px-2.5 py-1 fw-bold font-monospace" style="font-size: 0.85rem; width: fit-content;">
                                                <i class="bi bi-clock me-1 text-primary"></i>{{ substr($r->check_in_at, 0, 5) }}
                                            </span>
                                            @if($r->is_late)
                                                <span class="badge rounded-pill fw-bold" style="background: #fff7ed; color: #c2410c; border: 1px solid #ffedd5; font-size: 0.72rem; width: fit-content;" title="تسجيل بعد موعد الدخول الرسمي 10:15 ص">
                                                    <i class="bi bi-clock-history me-1 text-danger"></i>{{ $r->delay_text }}
                                                </span>
                                            @else
                                                <span class="badge rounded-pill fw-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #d1fae5; font-size: 0.72rem; width: fit-content;" title="حضور في الموعد المحدد (قبل 10:15 ص)">
                                                    <i class="bi bi-check2 me-1 text-success"></i>في الموعد
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border small" style="font-size: 0.74rem;">—</span>
                                    @endif
                                </td>
                                <td class="px-3 text-center">
                                    @if($r->check_in_photo)
                                        <a href="{{ asset($r->check_in_photo) }}" target="_blank" class="d-inline-block position-relative" title="عرض صورة الحضور">
                                            <img src="{{ asset($r->check_in_photo) }}" alt="صورة الحضور" class="rounded-3 shadow-xs border" style="width: 44px; height: 44px; object-fit: cover;">
                                            <span class="position-absolute bottom-0 start-0 bg-success text-white rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 16px; height: 16px; font-size: 9px;">
                                                <i class="bi bi-camera-fill"></i>
                                            </span>
                                        </a>
                                    @elseif($r->notes && (str_contains($r->notes, 'جهاز المقر') || str_contains($r->notes, 'جهاز معتمد')))
                                        <span class="badge rounded-pill fw-bold" style="background: rgba(14, 165, 233, 0.12); color: #0284c7; border: 1px solid rgba(14, 165, 233, 0.28); font-size: 0.74rem; padding: 5px 10px;">
                                            <i class="bi bi-display me-1"></i> كمبيوتر المقر
                                        </span>
                                    @elseif($r->notes && str_contains($r->notes, 'إدار'))
                                        <span class="badge rounded-pill fw-bold" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.74rem; padding: 5px 10px;">
                                            <i class="bi bi-shield-check me-1"></i> حضور إداري
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border small" style="font-size: 0.74rem;">—</span>
                                    @endif
                                </td>
                                <td class="px-4"><span
                                        class="badge bg-danger-subtle text-danger rounded-pill px-3">{{ $r->check_out_at ?? '—' }}</span>
                                </td>
                                <td class="px-3 text-center">
                                    @if($r->check_out_photo)
                                        <a href="{{ asset($r->check_out_photo) }}" target="_blank" class="d-inline-block position-relative" title="عرض صورة الانصراف">
                                            <img src="{{ asset($r->check_out_photo) }}" alt="صورة الانصراف" class="rounded-3 shadow-xs border" style="width: 44px; height: 44px; object-fit: cover;">
                                            <span class="position-absolute bottom-0 start-0 bg-danger text-white rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 16px; height: 16px; font-size: 9px;">
                                                <i class="bi bi-camera-fill"></i>
                                            </span>
                                        </a>
                                    @elseif(!empty($r->check_out_at))
                                        <span class="badge rounded-pill fw-bold" style="background: rgba(16, 185, 129, 0.12); color: #047857; border: 1px solid rgba(16, 185, 129, 0.28); font-size: 0.74rem; padding: 5px 10px;">
                                            <i class="bi bi-check2 me-1"></i> تم الانصراف
                                        </span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="px-4 text-muted small">{{ Str::limit($r->notes, 30) ?? '—' }}</td>
                                <td class="px-4">
                                    @if($r->rating)
                                        <span class="text-warning" title="{{ $r->evaluation_notes }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $r->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </span>
                                    @else
                                        <span class="text-muted small">غير مقيم</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Delete Action --}}
                                        @if(auth()->check() && (!empty($isHrOrAdmin) || $r->user_id == auth()->id()))
                                            <form action="{{ route('employee-attendance.destroy', ['employee_attendance' => $r->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من طلب حذف هذا السجل؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger start-radius" title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit Action --}}
                                        @if(auth()->check() && (!empty($isHrOrAdmin) || $r->user_id == auth()->id()))
                                            <a href="{{ route('employee-attendance.edit', ['employee_attendance' => $r->id]) }}" class="btn btn-outline-primary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        {{-- View Action --}}
                                        <a href="{{ route('employee-attendance.show', ['employee_attendance' => $r->id]) }}" class="btn btn-outline-secondary end-radius" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x display-4 mb-3 d-block opacity-50"></i>
                                    لا توجد سجلات حضور حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($records->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $records->links() }}
            </div>
        @endif
    </div>

    <script>
    function toggleAll(source) {
        document.querySelectorAll('.bulk-item').forEach(checkbox => {
            checkbox.checked = source.checked;
        });
        updateBulkUI();
    }

    function updateBulkUI() {
        const count = document.querySelectorAll('.bulk-item:checked').length;
        const toolbar = document.getElementById('bulk-actions');
        if (count > 0) {
            toolbar.classList.remove('d-none');
        } else {
            toolbar.classList.add('d-none');
        }
    }

    function submitBulk(action) {
        const ids = Array.from(document.querySelectorAll('.bulk-item:checked')).map(cb => cb.value);
        if (ids.length === 0) return;

        if (!confirm(`هل أنت متأكد من ${action === 'destroy' ? 'حذف' : 'معالجة'} ${ids.length} عنصر محدد؟`)) return;

        const form = document.getElementById('bulk-destroy-form');
        
        // Clear previous inputs
        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

        // Add new inputs
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        form.submit();
    }

    (function () {
        const btn = document.getElementById('manualGpsCheckInBtn');
        const feedback = document.getElementById('gpsCheckInFeedback');
        if (!btn) return;

        function showFeedback(html, isError) {
            if (!feedback) return;
            feedback.classList.remove('d-none');
            feedback.style.color = isError ? '#b91c1c' : '#047857';
            feedback.innerHTML = html;
        }

        btn.addEventListener('click', function () {
            if (!navigator.geolocation) {
                showFeedback('<i class="bi bi-exclamation-triangle-fill me-1"></i> المتصفح لا يدعم تحديد الموقع الجغرافي (GPS).', true);
                return;
            }
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ التحقق من موقعك...';

            navigator.geolocation.getCurrentPosition(function (pos) {
                fetch('{{ route('employee-attendance.checkIn') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        latitude: pos.coords.latitude,
                        longitude: pos.coords.longitude
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data && data.success) {
                        showFeedback('<i class="bi bi-check-circle-fill me-1"></i> ' + (data.message || 'تم تسجيل حضورك بنجاح'), false);
                        setTimeout(() => window.location.reload(), 900);
                    } else {
                        showFeedback('<i class="bi bi-geo-alt-fill me-1"></i> ' + (data.message || 'أنت خارج نطاق المقر المسموح به.'), true);
                        btn.disabled = false;
                        btn.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> إعادة فحص الموقع وتسجيل الحضور (GPS)';
                    }
                })
                .catch(() => {
                    showFeedback('<i class="bi bi-exclamation-triangle-fill me-1"></i> حدث خطأ أثناء الاتصال بالخادم.', true);
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> تسجيل الحضور الذكي (GPS)';
                });
            }, function () {
                showFeedback('<i class="bi bi-exclamation-triangle-fill me-1"></i> يرجى السماح بصلاحية الوصول للموقع (Location) في المتصفح لتسجيل الحضور.', true);
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-geo-alt-fill text-danger me-1"></i> تسجيل الحضور الذكي (GPS)';
            }, { enableHighAccuracy: true, timeout: 10000 });
        });
    })();
    </script>
    <form id="bulk-destroy-form" action="{{ route('employee-attendance.bulk-destroy') }}" method="POST" style="display:none">
        @csrf
    </form>
@endsection

