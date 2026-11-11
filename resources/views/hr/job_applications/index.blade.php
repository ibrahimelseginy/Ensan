@extends('layouts.app')

@section('title', 'طلبات التوظيف - الموارد البشرية')

@push('styles')
<style>
    .jobs-hero {
        background: linear-gradient(135deg, #5b21b6 0%, #7c3aed 55%, #8b5cf6 100%);
        border-radius: 20px;
        padding: 1.75rem 2rem;
        color: #fff !important;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 25px -8px rgba(124, 58, 237, .45);
    }
    .jobs-hero h1, .jobs-hero p, .jobs-hero span, .jobs-hero i { color: #fff !important; }
    .jobs-hero .jobs-hero-sub { color: rgba(255,255,255,.9) !important; }
    .job-stat {
        background: var(--card-bg, #fff);
        border: 1px solid var(--border-color, #e5e7eb);
        border-radius: 14px;
        padding: 1rem 1.1rem;
        text-decoration: none;
        display: block;
        transition: transform .15s ease, box-shadow .15s ease;
    }
    .job-stat:hover { transform: translateY(-2px); box-shadow: 0 6px 16px rgba(0,0,0,.06); }
    .job-stat.active { outline: 2px solid #7c3aed; }
    .job-stat .num { font-size: 1.6rem; font-weight: 800; line-height: 1.1; }
    .job-code { font-family: 'Courier New', monospace; font-weight: 700; font-size: .82rem; background: rgba(124,58,237,.08); color: #6d28d9; border: 1px solid rgba(124,58,237,.2); padding: .2rem .5rem; border-radius: 8px; }
</style>
@endpush

@section('content')
@php $u = auth()->user(); @endphp

<div class="jobs-hero">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
            <span class="badge rounded-pill px-3 py-2 mb-2" style="background: rgba(255,255,255,.16); border: 1px solid rgba(255,255,255,.3);">
                <i class="bi bi-briefcase-fill me-1"></i> الموارد البشرية — التوظيف
            </span>
            <h1 class="fw-bold mb-1" style="font-size: 1.9rem;">طلبات التوظيف الواردة من الموقع</h1>
            <p class="jobs-hero-sub mb-0">كل متقدم له كود متابعة خاص يتابع به حالة طلبه من الموقع (قيد المراجعة / مقابلة / مقبول / مرفوض).</p>
        </div>
        <a href="https://ensaneg.com/jobs" target="_blank" class="btn btn-light fw-bold rounded-pill px-4" style="color:#5b21b6 !important;">
            <i class="bi bi-box-arrow-up-left me-1" style="color:#5b21b6 !important;"></i> صفحة التقديم على الموقع
        </a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle me-1"></i> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif

{{-- Stats --}}
@php
    $statCards = [
        ['key' => 'all',       'label' => 'إجمالي الطلبات', 'value' => $stats['total'],     'color' => 'secondary', 'icon' => 'bi-collection'],
        ['key' => 'new',       'label' => 'جديد',           'value' => $stats['new'],       'color' => 'primary',   'icon' => 'bi-inbox'],
        ['key' => 'reviewing', 'label' => 'قيد المراجعة',    'value' => $stats['reviewing'], 'color' => 'info',      'icon' => 'bi-hourglass-split'],
        ['key' => 'interview', 'label' => 'مقابلات',        'value' => $stats['interview'], 'color' => 'warning',   'icon' => 'bi-calendar-event'],
        ['key' => 'accepted',  'label' => 'مقبول',          'value' => $stats['accepted'],  'color' => 'success',   'icon' => 'bi-check-circle'],
        ['key' => 'rejected',  'label' => 'مرفوض',          'value' => $stats['rejected'],  'color' => 'danger',    'icon' => 'bi-x-circle'],
    ];
@endphp
<div class="row g-2 mb-4">
    @foreach($statCards as $card)
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('hr.job-applications.index', array_filter(['status' => $card['key'], 'position' => $position, 'q' => $q])) }}"
               class="job-stat {{ $status === $card['key'] ? 'active' : '' }}">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted small fw-bold">{{ $card['label'] }}</span>
                    <i class="bi {{ $card['icon'] }} text-{{ $card['color'] }} fs-5"></i>
                </div>
                <div class="num text-{{ $card['color'] }}">{{ $card['value'] }}</div>
            </a>
        </div>
    @endforeach
</div>

{{-- Filters --}}
<form method="GET" class="card border-0 shadow-sm rounded-4 p-3 mb-3">
    <div class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small fw-bold mb-1">بحث</label>
            <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="الاسم / كود المتابعة / الهاتف / البريد / الكلية / محل الإقامة">
        </div>
        <div class="col-md-3">
            <label class="form-label small fw-bold mb-1">الوظيفة</label>
            <select name="position" class="form-select">
                <option value="">كل الوظائف</option>
                @foreach($positions as $pos)
                    <option value="{{ $pos }}" @selected($position === $pos)>{{ $pos }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label small fw-bold mb-1">الحالة</label>
            <select name="status" class="form-select">
                <option value="all">كل الحالات</option>
                @foreach($statuses as $k => $label)
                    <option value="{{ $k }}" @selected($status === $k)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button class="btn btn-primary w-100 fw-bold"><i class="bi bi-funnel me-1"></i> تصفية</button>
            <a href="{{ route('hr.job-applications.index') }}" class="btn btn-outline-secondary" title="مسح"><i class="bi bi-x-lg"></i></a>
        </div>
    </div>
</form>

{{-- Table --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>كود المتابعة</th>
                    <th>المتقدم</th>
                    <th>الوظيفة</th>
                    <th>الكلية / الإقامة</th>
                    <th>تطوع سابق</th>
                    <th>الحالة</th>
                    <th>تاريخ التقديم</th>
                    <th class="text-end">إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td><span class="job-code">{{ $app->tracking_code }}</span></td>
                        <td>
                            <div class="fw-bold">{{ $app->full_name }}</div>
                            <div class="small text-muted">
                                {{ $app->age ? $app->age . ' سنة' : '' }}
                                @if($app->whatsapp_link)
                                    · <a href="{{ $app->whatsapp_link }}" target="_blank" class="text-success text-decoration-none"><i class="bi bi-whatsapp"></i> {{ $app->phone }}</a>
                                @endif
                            </div>
                            @if($app->email)<div class="small text-muted">{{ $app->email }}</div>@endif
                        </td>
                        <td class="fw-semibold">{{ $app->position }}</td>
                        <td class="small">
                            <div><i class="bi bi-mortarboard text-primary"></i> {{ $app->faculty ?: '-' }}</div>
                            <div class="text-muted"><i class="bi bi-geo-alt"></i> {{ $app->residence ?: '-' }}</div>
                        </td>
                        <td>
                            @if($app->has_volunteered)
                                <span class="badge bg-success-subtle text-success border border-success-subtle" title="{{ $app->volunteer_details }}">نعم</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border">لا</span>
                            @endif
                        </td>
                        <td><span class="badge bg-{{ $app->status_color }}">{{ $app->status_label }}</span></td>
                        <td class="small text-muted">{{ $app->created_at?->format('Y-m-d') }}</td>
                        <td class="text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('hr.job-applications.show', $app) }}" class="btn btn-sm btn-primary"><i class="bi bi-eye me-1"></i> مراجعة</a>
                                @if($app->cv_path)
                                    <a href="{{ route('hr.job-applications.cv', $app) }}" class="btn btn-sm btn-outline-secondary" title="تحميل السيرة الذاتية"><i class="bi bi-file-earmark-arrow-down"></i> CV</a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-briefcase fs-1 d-block mb-2"></i>
                            لا توجد طلبات توظيف مطابقة حالياً.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($applications->hasPages())
        <div class="card-footer bg-transparent">{{ $applications->links() }}</div>
    @endif
</div>
@endsection

