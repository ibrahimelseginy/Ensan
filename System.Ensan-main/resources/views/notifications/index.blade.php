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
                    <label class="form-label small text-muted">الفئة</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i class="bi bi-tags"></i></span>
                        <select name="category" class="form-select border-start-0 ps-0">
                            <option value="">الكل</option>
                            <option value="complaints" @selected($category === 'complaints')>الشكاوى</option>
                            <option value="tasks" @selected($category === 'tasks')>المهام</option>
                            <option value="attendance" @selected($category === 'attendance')>الحضور</option>
                            <option value="finance" @selected($category === 'finance')>المالية</option>
                            <option value="beneficiaries" @selected($category === 'beneficiaries')>المستفيدون</option>
                            <option value="monthly_donations" @selected($category === 'monthly_donations')>التبرعات الشهرية</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted">النوع</label>
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0"><i
                                class="bi bi-filter-circle"></i></span>
                        <select name="type" class="form-select border-start-0 ps-0">
                            <option value="">الكل</option>
                            <option value="success" @selected($type === 'success')>نجاح</option>
                            <option value="info" @selected($type === 'info')>معلومة</option>
                            <option value="warning" @selected($type === 'warning')>تحذير</option>
                            <option value="danger" @selected($type === 'danger')>هام</option>
                            <option value="secondary" @selected($type === 'secondary')>عام</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 d-flex gap-2">
                    <button class="btn btn-primary px-4"><i class="bi bi-funnel"></i> تصفية</button>
                    <a href="{{ route('notifications.index') }}" class="btn btn-secondary-subtle"><i class="bi bi-x-lg"></i>
                        إعادة تعيين</a>
                </div>
            </form>
        </div>
    </div>

    @if(!empty($suggestions) && count($suggestions) > 0)
        <div class="mb-4">
            <h5 class="fw-bold mb-3 text-primary"><i class="bi bi-lightbulb"></i> اقتراحات ذكية</h5>
            <div class="row g-3">
                @foreach($suggestions as $s)
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 shadow-sm h-100 border-start border-4 border-primary">
                            <div class="card-body d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-medium">{{ $s['text'] }}</div>
                                </div>
                                <a href="{{ $s['link'] }}" class="btn btn-sm btn-primary ms-2 text-nowrap">تنفيذ <i
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
                $icon = 'bi-bell';
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
                <div class="card border-0 mb-3 notification-card border-{{ $color }}">
                    <div class="card-body p-3">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <div class="d-flex align-items-center gap-2">
                                <span class="d-flex align-items-center justify-content-center rounded-circle bg-{{ $color }} bg-opacity-10 text-{{ $color }}" style="width: 32px; height: 32px;">
                                    <i class="bi {{ $icon }} fs-5"></i>
                                </span>
                                <span class="notif-label bg-{{ $color }} bg-opacity-10 text-{{ $color }} border border-{{ $color }} border-opacity-25">
                                    {{ $n['type'] == 'success' ? 'نجاح' : ($n['type'] == 'info' ? 'معلومة' : ($n['type'] == 'warning' ? 'تحذير' : ($n['type'] == 'danger' ? 'هام' : 'عام'))) }}
                                </span>
                            </div>
                        </div>
                        <p class="mb-3 fw-medium lh-base" style="font-size: 0.95rem;">{{ $n['text'] }}</p>
                        <div class="d-flex justify-content-start">
                            <a href="{{ $n['link'] }}" class="btn btn-{{ $color }} btn-sm rounded-pill px-4 shadow-sm">
                                <i class="bi bi-box-arrow-up-right me-1"></i> فتح
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <div class="bg-secondary-subtle rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-bell-slash text-muted fs-1"></i>
                    </div>
                    <h5 class="text-muted">لا توجد إشعارات حالياً</h5>
                    <p class="text-muted small">حاول تغيير خيارات التصفية أو عد لاحقاً</p>
                </div>
            </div>
        @endforelse
    </div>
@endsection

