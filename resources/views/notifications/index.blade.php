@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-bell-fill text-primary me-2"></i>مركز الإشعارات والتنبيهات
            </h4>
            <p class="text-muted mb-0 small">تابع آخر التحديثات والمهام والتنبيهات الموجهة لك بالترتيب الزمني</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @if(!empty($canCreateNotif))
                <button type="button" class="btn rounded-pill px-3 py-2 shadow-sm border-0 d-inline-flex align-items-center gap-2" 
                        style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%) !important; color: #ffffff !important; font-family: 'IBM Plex Sans Arabic', sans-serif !important; font-weight: 700 !important; font-size: 0.92rem; border: 1px solid rgba(255, 255, 255, 0.15) !important;" 
                        data-bs-toggle="modal" data-bs-target="#broadcastSystemUpdateModal">
                    <i class="bi bi-rocket-takeoff-fill text-warning fs-6" style="color: #f59e0b !important;"></i>
                    <span style="color: #ffffff !important; font-family: 'IBM Plex Sans Arabic', sans-serif !important; font-weight: 700 !important;">إرسال تحديث نظام</span>
                    <span class="badge rounded-pill px-2 py-0.5" style="background: rgba(245, 158, 11, 0.2) !important; color: #fbbf24 !important; border: 1px solid rgba(245, 158, 11, 0.35) !important; font-size: 0.76rem; font-weight: 700;">م. إبراهيم السجيني</span>
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-3 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createEmployeeNotifModal">
                    <i class="bi bi-send-fill me-1"></i> إرسال إشعار لموظف
                </button>
            @endif
            <button type="button" id="pageMarkAllReadBtn" class="btn btn-outline-secondary rounded-pill px-3 fw-semibold">
                <i class="bi bi-check2-all text-primary me-1"></i> تحديد الكل كمقروء
            </button>
        </div>
    </div>

    {{-- Session alerts --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 d-flex align-items-center mb-4" role="alert">
            <i class="bi bi-check-circle-fill fs-5 me-2"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted">الفئة والقطاع</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-tags"></i></span>
                        <select name="category" class="form-select border-start-0 ps-0">
                            <option value="">جميع القطاعات والأقسام</option>
                            <option value="targeted_employee" @selected($category === 'targeted_employee')>🔔 تنبيهات موجهة لموظفي ومكتبي</option>
                            <option value="cases" @selected($category === 'cases')>الحالات وطلبات المساعدة</option>
                            <option value="field_visits" @selected($category === 'field_visits')>الزيارات الميدانية والبحث</option>
                            <option value="tasks" @selected($category === 'tasks')>مهام الموظفين وفريق العمل</option>
                            <option value="hr" @selected($category === 'hr')>الموارد البشرية والإجازات</option>
                            <option value="attendance" @selected($category === 'attendance')>الحضور والانصراف</option>
                            <option value="finance" @selected($category === 'finance')>المالية والخزينة والرواتب</option>
                            <option value="beneficiaries" @selected($category === 'beneficiaries')>المستفيدون</option>
                            <option value="monthly_donations" @selected($category === 'monthly_donations')>التبرعات الشهرية</option>
                            <option value="change_requests" @selected($category === 'change_requests')>طلبات الموافقة والتعديل</option>
                            <option value="complaints" @selected($category === 'complaints')>الشكاوى</option>
                            <option value="inventory" @selected($category === 'inventory')>المخازن والمستودعات</option>
                            <option value="website" @selected($category === 'website')>رسائل واستفسارات الموقع</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">درجة الأهمية</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-filter-circle"></i></span>
                        <select name="type" class="form-select border-start-0 ps-0">
                            <option value="">جميع الحالات</option>
                            <option value="danger" @selected($type === 'danger')>عاجل / هام / مرفوض (أحمر)</option>
                            <option value="warning" @selected($type === 'warning')>تحذير / بانتظار إجراء (برتقالي)</option>
                            <option value="success" @selected($type === 'success')>نجاح / معتمد (أخضر)</option>
                            <option value="info" @selected($type === 'info')>معلومة / قيد التنفيذ (أزرق)</option>
                            <option value="secondary" @selected($type === 'secondary')>عام (رمادي)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted">حالة القراءة</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-eye"></i></span>
                        <select name="read_status" class="form-select border-start-0 ps-0">
                            <option value="">جميع الإشعارات</option>
                            <option value="unread" @selected(($readStatus ?? '') === 'unread')>غير المقروءة فقط (الجديدة)</option>
                            <option value="read" @selected(($readStatus ?? '') === 'read')>المقروءة فقط</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button class="btn btn-primary flex-grow-1"><i class="bi bi-funnel"></i> تصفية</button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-secondary-subtle" title="إلغاء التصفية"><i class="bi bi-x-lg"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Quick Action Suggestions --}}
    @if(!empty($suggestions) && count($suggestions) > 0)
        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-lightning-charge-fill me-1"></i> إجراءات وتوصيات ذكية سريعة</h5>
            <div class="row g-3">
                @foreach($suggestions as $s)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary rounded-3">
                            <div class="card-body p-3 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold small text-dark">{{ $s['text'] }}</div>
                                </div>
                                <a href="{{ $s['link'] }}" class="btn btn-sm btn-primary rounded-pill px-3 ms-2 text-nowrap shadow-xs">تنفيذ <i class="bi bi-arrow-left"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Notifications Grid (Sorted chronologically by newest first) --}}
    <div class="row g-3" id="notificationsPageGrid">
        @forelse($items as $n)
            @php
                $catName = match($n['category'] ?? '') {
                    'targeted_employee' => 'تنبيه موجه للموظف',
                    'cases' => 'الحالات وطلبات المساعدة',
                    'field_visits' => 'الزيارات الميدانية',
                    'tasks' => 'مهام فريق العمل',
                    'hr' => 'شؤون الموظفين والإجازات',
                    'attendance' => 'الحضور والانصراف',
                    'finance', 'payrolls' => 'المالية والخزينة',
                    'beneficiaries' => 'المستفيدون',
                    'monthly_donations' => 'التبرعات الدورية',
                    'change_requests' => 'طلبات الموافقة',
                    'complaints' => 'الشكاوى',
                    'inventory' => 'المستودعات والمخزون',
                    'website' => 'الموقع الإلكتروني',
                    default => 'إشعار عام'
                };

                $icon = match($n['type'] ?? 'secondary') {
                    'success' => 'bi-check-circle-fill',
                    'info' => 'bi-info-circle-fill',
                    'warning' => 'bi-exclamation-triangle-fill',
                    'danger' => 'bi-exclamation-octagon-fill',
                    default => 'bi-bell-fill'
                };

                $color = match($n['type'] ?? 'secondary') {
                    'success' => 'success',
                    'info' => 'info',
                    'warning' => 'warning',
                    'danger' => 'danger',
                    default => 'secondary'
                };

                $isRead = !empty($n['is_read']);
            @endphp
            <div class="col-md-6 col-lg-4 notif-card-col" id="card_{{ $n['id'] }}">
                <div class="card border-0 mb-3 notification-card border-{{ $color }} shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between {{ $isRead ? 'opacity-75 bg-light bg-opacity-40' : '' }}" style="transition: all 0.25s ease;">
                    <div class="card-body p-3.5">
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="width: 34px; height: 34px;">
                                    <i class="bi {{ $icon }} fs-5"></i>
                                </span>
                                <div>
                                    <span class="badge bg-light text-dark border px-2 py-0.5 x-small fw-semibold">
                                        {{ $catName }}
                                    </span>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span class="notif-label bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25 px-2 py-0.5 small rounded-pill fw-bold">
                                    {{ $n['type'] == 'success' ? 'نجاح / معتمد' : ($n['type'] == 'info' ? 'معلومة' : ($n['type'] == 'warning' ? 'تنبيه' : ($n['type'] == 'danger' ? 'هام وعاجل' : 'عام'))) }}
                                </span>
                                @if(!$isRead)
                                    <span class="badge bg-danger rounded-pill px-2 py-0.5 small notif-card-badge">جديد</span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 small notif-card-badge"><i class="bi bi-check2"></i> مقروء</span>
                                @endif
                                <button type="button" class="btn btn-link text-muted p-0 opacity-50 hover-opacity-100 notif-dismiss-btn" data-id="{{ $n['id'] }}" title="إخفاء الإشعار">
                                    <i class="bi bi-x-lg" style="font-size: 0.8rem;"></i>
                                </button>
                            </div>
                        </div>
                        <p class="mb-3 fw-bold text-dark lh-base" style="font-size: 0.95rem;">{{ $n['text'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 px-3.5 pb-3.5 pt-0 d-flex justify-content-between align-items-center">
                        <a href="{{ $n['link'] }}" class="btn btn-{{ $color }} btn-sm rounded-pill px-4 shadow-sm fw-bold notif-open-link" data-id="{{ $n['id'] }}">
                            <i class="bi bi-box-arrow-up-right me-1"></i> فتح ومتابعة
                        </a>
                        <small class="text-muted opacity-75 x-small" title="{{ $n['time_formatted'] ?? '' }}">
                            <i class="bi bi-clock me-1"></i>{{ $n['time_ago'] ?? 'الآن' }}
                        </small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-5">
                    <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px;">
                        <i class="bi bi-check2-all text-success fs-1"></i>
                    </div>
                    <h5 class="text-dark fw-bold">لا توجد إشعارات أو تنبيهات معلقة حالياً</h5>
                    <p class="text-muted small mb-0">جميع المهام والحالات والعمليات في هذا القسم منتظمة ومحدثة بالكامل.</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Modal: Send Notification to Specific Employee or All (For Managers & Admins) --}}
    @if(!empty($canCreateNotif))
    <div class="modal fade" id="createEmployeeNotifModal" tabindex="-1" aria-labelledby="createEmployeeNotifModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('notifications.store-targeted') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header border-bottom border-light pb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="rounded-circle bg-primary bg-opacity-10 text-primary d-inline-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-send-fill fs-5"></i>
                        </span>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0" id="createEmployeeNotifModalLabel">إرسال إشعار مخصص لموظف</h5>
                            <small class="text-muted">توجيه تعليمات أو تنبيهات خاصة تظهر فوراً في حساب الموظف</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">الجهة المستهدفة بالإشعار <span class="text-danger">*</span></label>
                        <select name="target_type" id="modalTargetType" class="form-select" required>
                            <option value="user" selected>موظف محدد (إشعار خاص شخصي)</option>
                            <option value="all">جميع الموظفين (بث عام للجميع)</option>
                            <option value="department">قسم محدد</option>
                            <option value="role">دور وصلاحية محددة</option>
                        </select>
                    </div>

                    <div class="mb-3" id="employeeSelectWrapper">
                        <label class="form-label fw-bold small text-muted">اختر الموظف <span class="text-danger">*</span></label>
                        <select name="target_user_id" id="modalTargetUserId" class="form-select">
                            <option value="">-- اضغط لاختيار الموظف المستهدف --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->name }} — {{ $emp->job_title ?: ($emp->department ?: 'موظف') }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3 d-none" id="deptInputWrapper">
                        <label class="form-label fw-bold small text-muted">اسم القسم المستهدف</label>
                        <input type="text" name="target_department" class="form-control" placeholder="مثال: البحث الميداني، المالية، رعاية الأيتام...">
                    </div>

                    <div class="mb-3 d-none" id="roleInputWrapper">
                        <label class="form-label fw-bold small text-muted">الدور الوظيفي المستهدف</label>
                        <select name="target_role" class="form-select">
                            <option value="social_researcher">باحث اجتماعي / ميداني</option>
                            <option value="manager">مدير فرع / قسم</option>
                            <option value="accountant">محاسب / مالية</option>
                            <option value="hr">الموارد البشرية</option>
                            <option value="guest_house_manager">مسؤول دار الضيافة</option>
                            <option value="warehouse_manager">أمين المخزن</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">عنوان الإشعار <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" placeholder="مثال: يرجى مراجعة تقرير البحث الميداني لحالة اليوم" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">نص الإشعار والتفاصيل <span class="text-danger">*</span></label>
                        <textarea name="body" class="form-control" rows="3" placeholder="اكتب التعليمات أو الملاحظات الموجهة للموظف هنا..." required></textarea>
                    </div>

                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">درجة الأهمية</label>
                            <select name="type" class="form-select">
                                <option value="info" selected>معلومة / توجيه (أزرق)</option>
                                <option value="warning">تنبيه / متابعة هامة (برتقالي)</option>
                                <option value="danger">عاجل / هام جداً (أحمر)</option>
                                <option value="success">نجاح / اعتماد (أخضر)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold text-muted">رابط داخلي (اختياري)</label>
                            <input type="text" name="link" class="form-control" placeholder="مثال: /cases أو /tasks">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-send-fill me-1"></i> إرسال الإشعار فوراً
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Broadcast System Update Modal (Eng. Ibrahim) --}}
    <div class="modal fade" id="broadcastSystemUpdateModal" tabindex="-1" aria-labelledby="broadcastSystemUpdateModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <form action="{{ route('notifications.broadcast-update') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
                @csrf
                <div class="modal-header text-white border-0 py-3 px-4" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-warning bg-opacity-25 p-2 text-warning d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">
                            <i class="bi bi-rocket-takeoff-fill fs-5" style="color: #f59e0b !important;"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="broadcastSystemUpdateModalLabel">
                                إشعار موظفي النظام بتحديث وتطوير الأقسام
                            </h5>
                            <small class="text-white-50">إرسال التحديث لموظفي القسم المعني فقط بتوقيع مهندس إبراهيم السجيني</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body p-4">
                    {{-- Notice Alert --}}
                    <div class="alert alert-info border-0 rounded-3 d-flex align-items-start gap-2 mb-4 p-3" style="background-color: #f0f9ff; color: #0369a1;">
                        <i class="bi bi-info-circle-fill fs-5 mt-0.5"></i>
                        <div class="small">
                            <strong>ميزة التوجيه الذكي:</strong> عند اختيارك لأي قسم (مثل الحالات، المالية، دار الضيافة، إلخ)، سيتم إرسال الإشعار <u>فقط للموظفين والباحثين التابعين لهذا القسم</u> دون إزعاج باقي أقسام المؤسسة.
                        </div>
                    </div>

                    {{-- Module Selector --}}
                    <div class="mb-4">
                        <label class="form-label fw-bold text-dark mb-2">
                            اختر القسم أو الوحدة التي تم تحديثها <span class="text-danger">*</span>
                        </label>
                        <div class="row g-2">
                            @php
                                $modulesList = $systemUpdateModules ?? \App\Services\SystemUpdateNotificationService::getModules();
                            @endphp
                            @foreach($modulesList as $modKey => $mod)
                                <div class="col-md-6">
                                    <label class="d-flex align-items-center p-2.5 border rounded-3 cursor-pointer module-card-option h-100 hover-shadow transition-all" style="cursor: pointer;">
                                        <input type="radio" name="module" value="{{ $modKey }}" class="form-check-input me-2 mt-0" {{ $modKey === 'beneficiaries' ? 'checked' : '' }} required>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="badge bg-light text-dark border p-2 rounded-2">
                                                <i class="bi {{ $mod['icon'] }} text-primary"></i>
                                            </span>
                                            <div>
                                                <div class="fw-bold small text-dark">{{ $mod['short_name'] }}</div>
                                                <div class="text-muted" style="font-size: 11px;">{{ Str::limit($mod['name'], 38) }}</div>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Custom Title --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">عنوان مخصص للإشعار (اختياري)</label>
                        <input type="text" name="custom_title" class="form-control" placeholder="اتركه فارغاً ليتم استخدام العنوان التلقائي: 🛠️ تحديث وتطوير في [اسم القسم]">
                    </div>

                    {{-- Update Details --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold small text-muted">
                            تفاصيل التحديث والميزات الجديدة التي تمت <span class="text-danger">*</span>
                        </label>
                        <textarea name="details" class="form-control" rows="4" placeholder="اكتب ما تم تطويره أو إضافته في هذا القسم ليكون الموظفون على علم به... (مثال: تم تحسين صفحة الحالات وأصبح حساب مرضى بعثاء الأمل مستقلاً، وتسهيل استخراج التقارير...)" required></textarea>
                    </div>

                    {{-- Signature Preview Card --}}
                    <div class="card bg-light border-0 rounded-3 p-3 mt-3">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="small fw-bold text-muted"><i class="bi bi-eye me-1"></i> معاينة توقيع الإشعار التلقائي:</span>
                            <span class="badge bg-dark text-warning px-2 py-1"><i class="bi bi-patch-check-fill me-1"></i> معتمد رسمياً</span>
                        </div>
                        <div class="bg-white p-3 rounded-2 border border-light-subtle shadow-sm text-end" style="font-family: inherit;">
                            <div class="text-muted small mb-2">[تفاصيل التحديث التي ستكتبها بالأعلى]</div>
                            <div class="text-success fw-bold small mb-1">✅ تم إنجاز هذا التحديث لخدمتكم وضمان أفضل أداء للعمل.</div>
                            <div class="fw-bold text-dark pt-1 border-top border-light">— مهندس إبراهيم السجيني 🛠️</div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer border-top border-light p-3">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-dark rounded-pill px-4 fw-bold shadow-sm" style="background: linear-gradient(135deg, #1e293b, #0f172a);">
                        <i class="bi bi-send-check-fill text-warning me-1"></i> بث التحديث للموظفين المعنيين
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Target type selector toggle
            var targetTypeSelect = document.getElementById('modalTargetType');
            var empWrapper = document.getElementById('employeeSelectWrapper');
            var deptWrapper = document.getElementById('deptInputWrapper');
            var roleWrapper = document.getElementById('roleInputWrapper');
            var empSelect = document.getElementById('modalTargetUserId');

            if (targetTypeSelect) {
                targetTypeSelect.addEventListener('change', function () {
                    var val = this.value;
                    if (empWrapper) empWrapper.classList.toggle('d-none', val !== 'user');
                    if (deptWrapper) deptWrapper.classList.toggle('d-none', val !== 'department');
                    if (roleWrapper) roleWrapper.classList.toggle('d-none', val !== 'role');
                    if (empSelect) empSelect.required = (val === 'user');
                });
            }

            // Mark All Read on Page
            var markAllBtn = document.getElementById('pageMarkAllReadBtn');
            if (markAllBtn) {
                markAllBtn.addEventListener('click', function () {
                    var badges = document.querySelectorAll('.notif-card-badge');
                    badges.forEach(function (b) {
                        b.className = 'badge bg-secondary-subtle text-secondary rounded-pill px-2 py-0.5 small notif-card-badge';
                        b.innerHTML = '<i class="bi bi-check2"></i> مقروء';
                    });

                    var cards = document.querySelectorAll('.notification-card');
                    cards.forEach(function (c) {
                        c.classList.add('opacity-75', 'bg-light', 'bg-opacity-40');
                    });

                    fetch('{{ route('notifications.mark-all-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({})
                    }).catch(function () {});

                    // Update bell count in navbar
                    var bellBadge = document.getElementById('notificationBellBadge');
                    if (bellBadge) bellBadge.style.display = 'none';
                });
            }

            // Dismiss single card
            document.querySelectorAll('.notif-dismiss-btn').forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    e.preventDefault();
                    var notifId = this.dataset.id;
                    var col = document.getElementById('card_' + notifId);
                    if (col) {
                        col.style.transition = 'all 0.3s ease';
                        col.style.transform = 'scale(0.9)';
                        col.style.opacity = '0';
                        setTimeout(function () { col.remove(); }, 300);
                    }

                    fetch('{{ route('notifications.dismiss') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: notifId })
                    }).catch(function () {});
                });
            });

            // Mark single card read on link open
            document.querySelectorAll('.notif-open-link').forEach(function (link) {
                link.addEventListener('click', function () {
                    var notifId = this.dataset.id;
                    fetch('{{ route('notifications.mark-read') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ id: notifId })
                    }).catch(function () {});
                });
            });
        });
    </script>
@endsection
