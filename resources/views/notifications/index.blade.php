@extends('layouts.app')

@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-bell text-primary me-2"></i>مركز الإشعارات
            </h4>
            <p class="text-muted mb-0 small">تابع آخر التحديثات والمهام</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label small text-muted">الفئة والقطاع</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-tags"></i></span>
                        <select name="category" class="form-select border-start-0 ps-0">
                            <option value="">جميع القطاعات والأقسام</option>
                            <option value="targeted_employee" @selected($category === 'targeted_employee')>🔔 تنبيهات مخصصة لوظيفتي وحسابي</option>
                            <option value="cases" @selected($category === 'cases')>الحالات وطلبات المساعدة (المقبولة والمرفوضة والجديدة)</option>
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
                <div class="col-md-4">
                    <label class="form-label small text-muted">درجة الأهمية</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i
                                class="bi bi-filter-circle"></i></span>
                        <select name="type" class="form-select border-start-0 ps-0">
                            <option value="">جميع الحالات</option>
                            <option value="success" @selected($type === 'success')>نجاح / مطمئن (أخضر)</option>
                            <option value="danger" @selected($type === 'danger')>عاجل / هام / مرفوض (أحمر)</option>
                            <option value="warning" @selected($type === 'warning')>تحذير / بانتظار اتخاذ إجراء (برتقالي)</option>
                            <option value="info" @selected($type === 'info')>معلومة / قيد التنفيذ (أزرق)</option>
                            <option value="secondary" @selected($type === 'secondary')>عام (رمادي)</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary px-4"><i class="bi bi-funnel"></i> تصفية الإشعارات</button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-secondary-subtle"><i class="bi bi-x-lg"></i>
                        إلغاء التصفية</a>
                </div>
            </form>
        </div>
    </div>

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
                                <a href="{{ $s['link'] }}" class="btn btn-sm btn-primary rounded-pill px-3 ms-2 text-nowrap shadow-xs">تنفيذ <i
                                        class="bi bi-arrow-left"></i></a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="row g-3">
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

                $icon = 'bi-bell-fill';
                $color = 'secondary';
                if ($n['type'] == 'success') {
                    $icon = 'bi-check-circle-fill';
                    $color = 'success';
                }
                if ($n['type'] == 'info') {
                    $icon = 'bi-info-circle-fill';
                    $color = 'info';
                }
                if ($n['type'] == 'warning') {
                    $icon = 'bi-exclamation-triangle-fill';
                    $color = 'warning';
                }
                if ($n['type'] == 'danger') {
                    $icon = 'bi-exclamation-octagon-fill';
                    $color = 'danger';
                }
            @endphp
            <div class="col-md-6 col-lg-4">
                <div class="card border-0 mb-3 notification-card border-{{ $color }} shadow-sm rounded-4 h-100 d-flex flex-column justify-content-between">
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
                            <span class="notif-label bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25 px-2.5 py-0.5 small rounded-pill fw-bold">
                                {{ $n['type'] == 'success' ? 'نجاح / معتمد' : ($n['type'] == 'info' ? 'معلومة' : ($n['type'] == 'warning' ? 'تنبيه' : ($n['type'] == 'danger' ? 'هام وعاجل' : 'عام'))) }}
                            </span>
                        </div>
                        <p class="mb-3 fw-bold text-dark lh-base" style="font-size: 0.95rem;">{{ $n['text'] }}</p>
                    </div>
                    <div class="card-footer bg-transparent border-0 px-3.5 pb-3.5 pt-0 d-flex justify-content-between align-items-center">
                        <a href="{{ $n['link'] }}" class="btn btn-{{ $color }} btn-sm rounded-pill px-4 shadow-sm fw-bold">
                            <i class="bi bi-box-arrow-up-right me-1"></i> فتح ومتابعة
                        </a>
                        <small class="text-muted opacity-75 x-small"><i class="bi bi-clock me-1"></i> محدث لحظياً</small>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5 bg-white rounded-4 shadow-sm border p-5">
                    <div class="bg-success-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-check2-all text-success fs-1"></i>
                    </div>
                    <h5 class="text-dark fw-bold">لا توجد إشعارات أو تنبيهات معلقة حالياً</h5>
                    <p class="text-muted small mb-0">جميع المهام والحالات والعمليات في هذا القسم منتظمة ومحدثة بالكامل.</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection

