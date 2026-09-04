@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);">
    <div class="hero-content">
        <div class="hero-greeting">الموارد البشرية 👥</div>
        <h1 class="hero-title">الموظفين والمستخدمين</h1>
        <p class="hero-subtitle">إدارة حسابات النظام والموارد البشرية</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('users.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة مستخدم
            </a>
        </div>
    </div>
    <i class="bi bi-people-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Filters Indicator --}}
@if(request()->anyFilled(['active', 'project_id', 'type']))
    <div class="animate-slide-up mb-3">
        <a href="{{ route('users.index') }}" class="btn btn-sm btn-light border rounded-pill px-3">
            <i class="bi bi-x-lg me-1"></i> عرض الكل (مسح الفلتر)
        </a>
    </div>
@endif

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 animate-slide-up animate-delay-1">
        <a href="{{ route('users.index', ['type' => 'employee']) }}" class="stat-card stat-primary text-decoration-none">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي الموظفين</div>
            <div class="stat-value">{{ number_format($totalEmployees) }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-md-3 animate-slide-up animate-delay-2">
        <a href="{{ route('users.index', ['type' => 'employee', 'active' => '1']) }}" class="stat-card stat-success text-decoration-none">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">الموظفين النشطين</div>
            <div class="stat-value">{{ number_format($activeEmployees) }}</div>
            <div class="stat-trend up"><i class="bi bi-check"></i> نشط</div>
            <i class="bi bi-check-circle-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-md-6 animate-slide-up animate-delay-3">
        <div class="summary-panel h-100">
            <h5 class="summary-title"><i class="bi bi-briefcase text-primary"></i> الموظفين حسب المشروع</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($projectsWithStats as $p)
                    @if($p->employees_count > 0)
                        <a href="{{ route('users.index', ['type' => 'employee', 'project_id' => $p->id]) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2 text-decoration-none hover-lift">
                            {{ $p->name }}: <span class="fw-bold">{{ $p->employees_count }}</span>
                        </a>
                    @endif
                @endforeach
                @if($projectsWithStats->sum('employees_count') == 0)
                    <small class="text-muted">لا يوجد موظفين معينين لمشاريع بعد.</small>
                @endif
            </div>
        </div>
    </div>
</div>

    <!-- Users Cards Grid -->
    <div class="row g-3">
        @foreach($users as $u)
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm border-0">
                    <div class="card-body position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title fw-bold text-body mb-0 text-truncate" style="max-width: 70%;">{{ $u->name }}
                            </h5>
                            @if($u->active)
                                <span class="badge bg-success-subtle text-success rounded-pill">نشط</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">غير نشط</span>
                            @endif
                        </div>

                        <div class="text-muted small mb-2">
                            <i class="bi bi-person-badge me-1"></i> {{ $u->job_title ?? 'غير محدد' }}
                        </div>
                        <div class="text-muted small mb-2">
                            <i class="bi bi-building me-1"></i> {{ $u->department ?? 'غير محدد' }}
                        </div>
                        <div class="text-muted small mb-3">
                            <i class="bi bi-envelope me-1"></i> {{ $u->email }}
                        </div>

                        <div class="mb-1">
                            @if($u->project)
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i
                                        class="bi bi-briefcase me-1"></i> {{ $u->project->name }}</span>
                            @elseif($u->campaign)
                                <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i
                                        class="bi bi-megaphone me-1"></i> {{ $u->campaign->name }}</span>
                            @elseif($u->guestHouse)
                                <span class="badge bg-info-subtle text-info border border-info-subtle"><i
                                        class="bi bi-house me-1"></i> {{ $u->guestHouse->name }}</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary border">غير معين</span>
                            @endif
                        </div>

                        <!-- Roles -->
                        @if($u->roles->count() > 0)
                            <div class="mt-2">
                                @foreach($u->roles as $role)
                                    <span class="badge bg-secondary-subtle text-secondary"
                                        style="font-size: 0.75rem;">{{ $role->name }}</span>
                                @endforeach
                            </div>
                        @endif

                        <a href="{{ route('users.show', $u) }}" class="stretched-link"></a>
                    </div>

                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end py-2"
                        style="position: relative; z-index: 2;">
                        @php $currentUser = request()->user(); @endphp
                        @if($currentUser)
                            @if($u->pendingRequest)
                                <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-1 rounded-pill small">
                                    <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                </span>
                            @else
                                @if($currentUser->hasRole('admin') || $currentUser->hasRole('manager') || $currentUser->hasRole('finance'))
                                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-secondary me-1" title="تعديل"><i
                                            class="bi bi-pencil"></i></a>
                                    <form class="d-inline" method="POST" action="{{ route('users.destroy', $u) }}"
                                        onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم نهائياً؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                    </form>
                                @else
                                    <a href="{{ route('users.edit', $u) }}" class="btn btn-sm btn-outline-warning me-1" title="طلب تعديل"><i
                                            class="bi bi-pencil-square"></i></a>
                                    <form class="d-inline" method="POST" action="{{ route('users.destroy', $u) }}"
                                        onsubmit="return confirm('هل أنت متأكد من طلب حذف هذا المستخدم؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-warning" title="طلب حذف"><i class="bi bi-x-circle"></i></button>
                                    </form>
                                @endif
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $users->links() }}</div>
@endsection

