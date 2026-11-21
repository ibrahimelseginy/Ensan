@extends('layouts.app')

@section('title', 'أجهزة بصمة المقرات المعتمدة - مؤسسة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4" style="max-width: 1300px; direction: rtl;">

    {{-- Breadcrumb & Back Action --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 fs-7">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none text-muted">الرئيسية</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('employee-attendance.index') }}" class="text-decoration-none text-muted">الحضور والانصراف</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">أجهزة بصمة المقرات</li>
                </ol>
            </nav>
            <h3 class="fw-black text-dark mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-display text-primary fs-3"></i>
                أجهزة بصمة المقرات المعتمدة
            </h3>
            <p class="text-muted small mb-0">اعتماد وإدارة أجهزة الكمبيوتر المكتبية في المقرات ودور الضيافة لحضور الموظفين التلقائي دون الحاجة لـ GPS.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('employee-attendance.index') }}" class="btn btn-outline-secondary rounded-pill px-3.5 py-1.5 fw-bold shadow-xs">
                <i class="bi bi-arrow-right me-1"></i> العودة لسجل الحضور
            </a>
        </div>
    </div>

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 py-3 px-3.5 mb-4" role="alert">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-check-circle-fill fs-5 text-success"></i>
                <span class="fw-bold">{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- SECTION 1: Status of the CURRENT device this browser is running on --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden" id="currentDeviceStatusCard" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border: 1px solid #e2e8f0 !important;">
        <div class="card-body p-4">
            <div id="currentDeviceLoading" class="text-center py-3">
                <span class="spinner-border spinner-border-sm text-primary me-2"></span>
                <span class="text-muted fw-bold">جارٍ فحص حالة هذا الجهاز...</span>
            </div>

            {{-- State A: Current Device IS Authorized --}}
            <div id="currentDeviceAuthorized" class="d-none">
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 54px; height: 54px; background: #10b981; flex-shrink: 0;">
                            <i class="bi bi-check2-circle fs-2"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success rounded-pill px-3 py-1 fw-bold fs-7">هذا الكمبيوتر معتمد رسمياً ✅</span>
                                <span class="badge bg-light text-dark border rounded-pill px-2.5 py-0.5 small" id="currDeviceIpBadge">IP: {{ request()->ip() }}</span>
                            </div>
                            <h5 class="fw-bold text-dark mb-1" id="currDeviceNameHeader">كمبيوتر المقر المعتمد</h5>
                            <p class="text-muted small mb-0">أي موظف يسجل دخوله من هذا الجهاز سيتم تسجيل حضوره تلقائياً فوراً وبدون طلب GPS.</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" onclick="testCurrentDeviceCheckIn()" class="btn btn-outline-success rounded-pill px-3 py-1.5 fw-bold" id="btnTestDevice">
                            <i class="bi bi-patch-check-fill me-1"></i> تجربة تسجيل الحضور الآن
                        </button>
                        <button type="button" onclick="unauthorizeLocalBrowser()" class="btn btn-outline-danger rounded-pill px-3 py-1.5 fw-bold">
                            <i class="bi bi-shield-x me-1"></i> إلغاء الاعتماد من هذا المتصفح
                        </button>
                    </div>
                </div>
            </div>

            {{-- State B: Current Device is NOT Authorized --}}
            <div id="currentDeviceNotAuthorized" class="d-none">
                <div class="row align-items-center g-3">
                    <div class="col-lg-6 col-md-12">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-circle d-flex align-items-center justify-content-center text-white shadow-sm" style="width: 52px; height: 52px; background: #f59e0b; flex-shrink: 0;">
                                <i class="bi bi-display fs-3"></i>
                            </div>
                            <div>
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-warning text-dark rounded-pill px-3 py-1 fw-bold fs-7">هذا الجهاز غير معتمد حالياً</span>
                                    <span class="badge bg-white text-muted border rounded-pill px-2.5 py-0.5 small">IP: {{ request()->ip() }}</span>
                                </div>
                                <h5 class="fw-bold text-dark mb-1">هل هذا هو كمبيوتر المقر أو الاستقبال؟</h5>
                                <p class="text-muted small mb-0">يمكنك اعتماده الآن لمرة واحدة فقط ليتحول إلى جهاز حضور رسمي ومعتمد للدار/المقر.</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <form id="formAuthorizeCurrentPc" onsubmit="submitAuthorizeFromPage(event)" class="bg-white p-3 rounded-3 shadow-xs border">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-dark mb-1">مقر العمل / الفرع:</label>
                                    <select class="form-select form-select-sm" id="pageAuthLocation" required>
                                        <option value="دار ضيافة كفر الشيخ" data-guest-house="2">دار ضيافة كفر الشيخ</option>
                                        <option value="دار ضيافة طنطا" data-guest-house="1">دار ضيافة طنطا</option>
                                        <option value="مقر المؤسسة الرئيسي">مقر المؤسسة الرئيسي</option>
                                        @foreach($guestHouses as $gh)
                                            @if(!in_array($gh->name, ['دار ضيافة كفر الشيخ', 'دار ضيافة طنطا']))
                                                <option value="{{ $gh->name }}" data-guest-house="{{ $gh->id }}">{{ $gh->name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-dark mb-1">اسم الجهاز:</label>
                                    <input type="text" class="form-control form-select-sm" id="pageAuthDeviceName" placeholder="مثال: كمبيوتر استقبال كفر الشيخ" required value="كمبيوتر المقر المكتبي">
                                </div>
                                <div class="col-md-3">
                                    <button type="submit" id="btnPageSubmitAuth" class="btn btn-primary btn-sm rounded-pill w-100 fw-bold py-1.5 shadow-sm">
                                        <i class="bi bi-shield-check me-1"></i> اعتماد الآن
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- State C: Running on Mobile --}}
            <div id="currentDeviceMobileWarning" class="d-none text-center py-2">
                <div class="d-inline-flex align-items-center gap-2 px-3 py-2 rounded-pill bg-light text-muted border">
                    <i class="bi bi-phone-fill text-primary"></i>
                    <span class="small fw-bold">أنت تتصفح هذه الصفحة من الهاتف المحمول. أجهزة بصمة المقرات تُعتمد من أجهزة الكمبيوتر المكتبية فقط.</span>
                </div>
            </div>
        </div>
    </div>

    {{-- SECTION 2: Table of All Authorized Headquarters Devices --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="p-2 bg-primary bg-opacity-10 text-primary rounded-3 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="bi bi-hdd-network-fill"></i>
                </div>
                <div>
                    <h5 class="fw-bold mb-0 text-dark">قائمة أجهزة المقرات المعتمدة</h5>
                    <small class="text-muted">إجمالي الأجهزة المعتمدة: {{ $devices->count() }} جهاز</small>
                </div>
            </div>
            <button type="button" onclick="window.location.reload()" class="btn btn-sm btn-light rounded-pill px-3 fw-bold text-muted" title="تحديث القائمة">
                <i class="bi bi-arrow-repeat me-1"></i> تحديث
            </button>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-nowrap">
                <thead class="bg-light text-muted small text-uppercase">
                    <tr>
                        <th class="py-3 px-3">#</th>
                        <th class="py-3">اسم الجهاز</th>
                        <th class="py-3">المقر / الفرع</th>
                        <th class="py-3">المعتمد بواسطة</th>
                        <th class="py-3">عنوان IP والمتصفح</th>
                        <th class="py-3">آخر استخدام للحضور</th>
                        <th class="py-3 text-center">الحالة</th>
                        <th class="py-3 text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($devices as $idx => $dev)
                        <tr>
                            <td class="px-3 fw-bold text-muted">{{ $idx + 1 }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="rounded-3 p-2 d-flex align-items-center justify-content-center text-white" style="width: 38px; height: 38px; background: {{ $dev->is_active ? '#0284c7' : '#94a3b8' }};">
                                        <i class="bi bi-display fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $dev->device_name }}</div>
                                        <small class="text-muted font-monospace" style="font-size: 0.72rem;">رمز: {{ substr($dev->device_token, 0, 16) }}...</small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-3 py-1.5 fw-bold" style="background: rgba(14, 165, 233, 0.12); color: #0284c7; font-size: 0.82rem;">
                                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $dev->location_name }}
                                </span>
                            </td>
                            <td>
                                <div class="small fw-bold text-dark">{{ $dev->authorizedBy?->name ?? 'الإدارة' }}</div>
                                <small class="text-muted" style="font-size: 0.75rem;">{{ $dev->created_at ? $dev->created_at->format('Y-m-d h:i A') : '-' }}</small>
                            </td>
                            <td>
                                <div class="small font-monospace fw-bold text-secondary">{{ $dev->ip_address ?: 'غير متوفر' }}</div>
                                <small class="text-muted text-truncate d-block" style="max-width: 180px; font-size: 0.72rem;" title="{{ $dev->user_agent }}">{{ $dev->user_agent ?: '-' }}</small>
                            </td>
                            <td>
                                @if($dev->last_used_at)
                                    <div class="small fw-bold text-success">
                                        <i class="bi bi-clock-history me-1"></i> {{ \Carbon\Carbon::parse($dev->last_used_at)->diffForHumans() }}
                                    </div>
                                    <small class="text-muted" style="font-size: 0.74rem;">{{ \Carbon\Carbon::parse($dev->last_used_at)->format('Y-m-d h:i A') }}</small>
                                @else
                                    <span class="text-muted small">لم يُستخدم بعد</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($dev->is_active)
                                    <span class="badge bg-success bg-opacity-15 text-success rounded-pill px-3 py-1 fw-bold">
                                        <span class="badge-dot bg-success me-1"></span> نشط ومعتمد
                                    </span>
                                @else
                                    <span class="badge bg-secondary bg-opacity-15 text-secondary rounded-pill px-3 py-1 fw-bold">معطل</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex align-items-center gap-1.5">
                                    {{-- Toggle active form --}}
                                    <form action="{{ route('employee-attendance.toggle-device', $dev->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-muted" title="{{ $dev->is_active ? 'تعطيل هذا الجهاز مؤقتاً' : 'تفعيل هذا الجهاز' }}">
                                            @if($dev->is_active)
                                                <i class="bi bi-pause-circle text-warning me-1"></i> تعطيل
                                            @else
                                                <i class="bi bi-play-circle text-success me-1"></i> تفعيل
                                            @endif
                                        </button>
                                    </form>

                                    {{-- Delete device form --}}
                                    <form action="{{ route('employee-attendance.destroy-device', $dev->id) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من إلغاء اعتماد وحذف هذا الجهاز نهائياً؟ لن يتمكن الموظفون من الحضور عبره تلقائياً.')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-light border rounded-pill px-2.5 py-1 text-danger" title="حذف وإلغاء الاعتماد نهائياً">
                                            <i class="bi bi-trash3 me-1"></i> حذف
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted py-3">
                                    <i class="bi bi-display text-muted opacity-50" style="font-size: 3rem;"></i>
                                    <h6 class="fw-bold mt-2">لا توجد أجهزة معتمدة حتى الآن</h6>
                                    <p class="small mb-0">قم بفتح هذه الصفحة من كمبيوتر الدار واضغط على "اعتماد هذا الكمبيوتر للمقر" لتسجيل أول جهاز.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- SECTION 3: Quick How-To Instructions Card --}}
    <div class="card border-0 shadow-sm rounded-4 p-4" style="background: #ffffff; border-right: 4px solid #0284c7 !important;">
        <h5 class="fw-bold text-dark mb-3 d-flex align-items-center gap-2">
            <i class="bi bi-question-circle-fill text-primary"></i>
            كيف تعمل منظومة أجهزة بصمة المقرات المعتمدة؟
        </h5>
        <div class="row g-3">
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-light h-100 border">
                    <div class="fw-bold text-primary mb-1">1️⃣ خطوة الاعتماد (تُجرى لمرة واحدة)</div>
                    <p class="text-muted small mb-0">يقوم المدير بفتح هذه الصفحة من كمبيوتر الاستقبال/الدار، ويضغط زر [اعتماد الآن]. يتم توليد بصمة رقمية مشفرة وتخزينها في متصفح ذلك الجهاز لمدة 5 سنوات.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-light h-100 border">
                    <div class="fw-bold text-success mb-1">2️⃣ حضور الموظفين التلقائي</div>
                    <p class="text-muted small mb-0">يأتي الموظف صباحاً إلى الدار، ويفتح حسابه من ذلك الكمبيوتر المعتمد، فيتعرف النظام على بصمة الجهاز ويسجل حضوره فوراً دون طلب GPS وبدون أي رسائل أخطاء.</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-3 rounded-3 bg-light h-100 border">
                    <div class="fw-bold text-dark mb-1">3️⃣ الأمان والمراقبة</div>
                    <p class="text-muted small mb-0">يمكنك في أي وقت من هذه الصفحة تعطيل أو حذف أي جهاز إذا تم استبدال الكمبيوتر أو نقله، وتظهر لك تواريخ وآخر استخدام لكل جهاز بدقة.</p>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Interactive Current Device Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    var isMobile = /Android|iPhone|iPad|iPod|Mobile/i.test(navigator.userAgent || '') || (window.innerWidth <= 768);
    var token = localStorage.getItem('ensan_authorized_device_token');

    var loadingEl = document.getElementById('currentDeviceLoading');
    var authorizedEl = document.getElementById('currentDeviceAuthorized');
    var notAuthorizedEl = document.getElementById('currentDeviceNotAuthorized');
    var mobileWarnEl = document.getElementById('currentDeviceMobileWarning');

    if (loadingEl) loadingEl.classList.add('d-none');

    if (isMobile) {
        if (mobileWarnEl) mobileWarnEl.classList.remove('d-none');
        return;
    }

    if (token) {
        if (authorizedEl) authorizedEl.classList.remove('d-none');
    } else {
        if (notAuthorizedEl) notAuthorizedEl.classList.remove('d-none');
    }

    window.submitAuthorizeFromPage = function(e) {
        e.preventDefault();
        var locSelect = document.getElementById('pageAuthLocation');
        var nameInput = document.getElementById('pageAuthDeviceName');
        var btn = document.getElementById('btnPageSubmitAuth');

        if (!nameInput || !nameInput.value.trim()) {
            alert('يرجى إدخال اسم الجهاز.');
            return;
        }

        var selectedOpt = locSelect.options[locSelect.selectedIndex];
        var ghId = selectedOpt.getAttribute('data-guest-house') || null;

        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ الاعتماد...';
        }

        fetch('{{ route('employee-attendance.authorize-device') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                device_name: nameInput.value.trim(),
                location_name: locSelect.value,
                guest_house_id: ghId
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data && data.success) {
                localStorage.setItem('ensan_authorized_device_token', data.device_token);
                alert('✅ ' + data.message);
                window.location.reload();
            } else {
                alert((data && data.message) ? data.message : 'تعذر اعتماد الجهاز');
                if (btn) {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> اعتماد الآن';
                }
            }
        })
        .catch(function() {
            alert('حدث خطأ في الاتصال بالسيرفر');
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-shield-check me-1"></i> اعتماد الآن';
            }
        });
    };

    window.unauthorizeLocalBrowser = function() {
        if (confirm('هل تريد بالتأكيد إزالة بصمة الاعتماد من هذا المتصفح فقط؟ (لن يسجل الحضور التلقائي بعد الآن من هذا المتصفح).')) {
            localStorage.removeItem('ensan_authorized_device_token');
            document.cookie = 'ensan_device_token=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;';
            alert('تمت إزالة البصمة من هذا المتصفح بنجاح.');
            window.location.reload();
        }
    };

    window.testCurrentDeviceCheckIn = function() {
        var btn = document.getElementById('btnTestDevice');
        var tok = localStorage.getItem('ensan_authorized_device_token');
        if (!tok) {
            alert('لا توجد بصمة مخزنة في هذا الجهاز.');
            return;
        }
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جارٍ الفحص...';
        }
        fetch('{{ route('employee-attendance.device-auto-checkin') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ device_token: tok })
        })
        .then(function(r){ return r.json(); })
        .then(function(d){
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i> تجربة تسجيل الحضور الآن';
            }
            if (d && d.success) {
                alert('✅ ' + (d.message || 'الجهاز معتمد ويعمل بشكل ممتاز!'));
            } else {
                alert('⚠️ ' + (d.message || 'الجهاز غير معتمد أو تم إلغاء تفعيله.'));
            }
        })
        .catch(function(){
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-patch-check-fill me-1"></i> تجربة تسجيل الحضور الآن';
            }
            alert('حدث خطأ في الاتصال');
        });
    };
});
</script>
@endsection
