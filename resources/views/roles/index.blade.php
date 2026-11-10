@extends('layouts.app')

@section('title', 'إدارة الأدوار والصلاحيات - مؤسسة إنسان')

@push('styles')
<style>
    .roles-hero {
        background: linear-gradient(135deg, #1e293b 0%, #334155 100%);
        border-radius: 20px;
        padding: 2rem;
        color: #ffffff !important;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 10px 25px -5px rgba(30, 41, 59, 0.25);
    }

    .roles-hero h1,
    .roles-hero .roles-hero-title {
        color: #ffffff !important;
    }

    .roles-hero .roles-hero-subtitle {
        color: rgba(255, 255, 255, 0.9) !important;
        font-size: 1rem;
        font-weight: 500;
    }

    .roles-hero .roles-hero-badge {
        background: rgba(255, 255, 255, 0.16) !important;
        color: #ffffff !important;
        border: 1px solid rgba(255, 255, 255, 0.28) !important;
    }

    .roles-hero .roles-hero-badge i {
        color: #34d399 !important;
    }

    .roles-hero .roles-hero-btn-outline {
        color: #ffffff !important;
        border: 1.5px solid rgba(255, 255, 255, 0.75) !important;
        background: rgba(255, 255, 255, 0.08) !important;
    }

    .roles-hero .roles-hero-btn-outline i {
        color: #ffffff !important;
    }

    .roles-hero .roles-hero-btn-outline:hover {
        background: #ffffff !important;
        color: #0f172a !important;
        border-color: #ffffff !important;
    }

    .roles-hero .roles-hero-btn-outline:hover i {
        color: #059669 !important;
    }

    .roles-hero::after {
        content: "";
        position: absolute;
        top: -40%;
        left: -10%;
        width: 280px;
        height: 280px;
        background: radial-gradient(circle, rgba(255,255,255,0.06) 0%, rgba(255,255,255,0) 70%);
        border-radius: 50%;
    }

    .stat-card-role {
        background: var(--card-bg);
        border: 1px solid var(--border-color);
        border-radius: 16px;
        padding: 1.5rem;
        transition: all 0.2s ease;
    }

    .stat-card-role:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.06);
    }

    .role-key-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.78rem;
        padding: 0.3rem 0.65rem;
        border-radius: 8px;
        background: rgba(99, 102, 241, 0.08);
        color: #6366f1;
        border: 1px solid rgba(99, 102, 241, 0.2);
        font-weight: 700;
    }

    .role-count-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.75rem;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.82rem;
    }

    .action-btn {
        width: 34px;
        height: 34px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 10px;
        transition: all 0.15s ease;
    }

    .action-btn:hover {
        transform: scale(1.1);
    }

    .user-chip {
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
        padding: 0.2rem 0.6rem;
        border-radius: 999px;
        background: rgba(16, 185, 129, 0.1);
        color: #047857;
        border: 1px solid rgba(16, 185, 129, 0.25);
        font-size: 0.76rem;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.15s ease;
    }

    .user-chip:hover {
        background: #10b981;
        color: #fff;
    }
</style>
@endpush

@section('content')
@php $u = auth()->user(); @endphp

{{-- Hero Header --}}
<div class="roles-hero dashboard-hero">
    <div class="row align-items-center position-relative" style="z-index: 1;">
        <div class="col-md-7">
            <span class="badge roles-hero-badge mb-2 px-3 py-2 rounded-pill fw-semibold">
                <i class="bi bi-shield-lock me-1"></i> الأمان والتحكم الكامل بالوصول (RBAC)
            </span>
            <h1 class="fw-black mb-2 roles-hero-title" style="font-size: 2.2rem; color: #ffffff !important;">إدارة الأدوار والصلاحيات</h1>
            <p class="roles-hero-subtitle mb-0" style="color: rgba(255, 255, 255, 0.92) !important;">
                تحكم دقيق 100% في صلاحيات كل قسم وكل موظف — الصلاحيات المحددة فقط هي التي تظهر وتعمل في حساب الموظف دون أي صلاحيات إضافية.
            </p>
        </div>
        <div class="col-md-5 text-md-end mt-3 mt-md-0 d-flex flex-wrap gap-2 justify-content-md-end">
            @if($u && $u->hasPermission('users.view'))
            <a href="{{ route('users.index') }}" class="btn btn-outline-light roles-hero-btn-outline fw-bold px-3 py-3 rounded-4">
                <i class="bi bi-people-fill me-1"></i> <span>سجل الموظفين</span>
            </a>
            @endif
            @if($u && $u->hasPermission('roles.create'))
            <a href="{{ route('roles.create') }}" class="btn btn-light btn-lg fw-bold px-4 py-3 rounded-4 shadow-sm" style="background: #ffffff !important; color: #0f172a !important;">
                <i class="bi bi-plus-circle-fill me-2 text-primary"></i> إنشاء دور جديد
            </a>
            @endif
        </div>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="stat-card-role">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-bold small">إجمالي الأدوار</span>
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2">
                    <i class="bi bi-diagram-3-fill fs-5"></i>
                </div>
            </div>
            <h2 class="fw-black mb-0">{{ $roles->total() }}</h2>
            <small class="text-muted">دور وظيفي مسجل في النظام</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card-role">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-bold small">إجمالي الصلاحيات المسندة</span>
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-2">
                    <i class="bi bi-key-fill fs-5"></i>
                </div>
            </div>
            <h2 class="fw-black mb-0">{{ $roles->sum('permissions_count') }}</h2>
            <small class="text-muted">صلاحية موزعة على الأدوار الحالية</small>
        </div>
    </div>
    <div class="col-md-4">
        <div class="stat-card-role">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-bold small">الموظفون المرتبطون</span>
                <div class="bg-info bg-opacity-10 text-info rounded-3 p-2">
                    <i class="bi bi-people-fill fs-5"></i>
                </div>
            </div>
            <h2 class="fw-black mb-0">{{ $roles->sum('users_count') }}</h2>
            <small class="text-muted">تعيين وظيفي مرتبط بالأدوار</small>
        </div>
    </div>
</div>

{{-- Search Filter --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('roles.index') }}" class="row g-2 align-items-center">
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-transparent border-end-0 text-muted">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" name="q" value="{{ request('q') }}" class="form-control border-start-0 ps-0" placeholder="ابحث باسم الدور أو الكود التعريفي (مثال: admin, finance, استقبال)...">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100 fw-bold rounded-3">
                    <i class="bi bi-funnel me-1"></i> بحث
                </button>
                @if(request('q'))
                    <a href="{{ route('roles.index') }}" class="btn btn-light border rounded-3" title="إعادة ضبط">
                        <i class="bi bi-arrow-counterclockwise"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Roles Table --}}
<div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-transparent border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-bold">
            <i class="bi bi-list-ul me-2 text-primary"></i> قائمة الأدوار الوظيفية والموظفين المسندين
        </h5>
        <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3 py-2">
            {{ $roles->total() }} دور
        </span>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-4 py-3 text-muted small fw-bold">اسم الدور</th>
                    <th class="py-3 text-muted small fw-bold">المعرّف (Key)</th>
                    <th class="py-3 text-muted small fw-bold">الموظفون المسند إليهم الدور</th>
                    <th class="py-3 text-muted small fw-bold text-center">الصلاحيات المفعلة</th>
                    <th class="pe-4 py-3 text-muted small fw-bold text-end">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                    <tr>
                        <td class="ps-4 py-3">
                            <div class="d-flex align-items-center gap-3">
                                <div class="rounded-3 bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px;">
                                    <i class="bi bi-shield-check fs-5"></i>
                                </div>
                                <div>
                                    <a href="{{ route('roles.show', $role) }}" class="fw-bold text-dark text-decoration-none d-block">
                                        {{ $role->name }}
                                    </a>
                                    <small class="text-muted">{{ Str::limit($role->description ?? 'اضغط لعرض الصلاحيات وإدارة الموظفين', 50) }}</small>
                                </div>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="role-key-badge">{{ $role->key }}</span>
                        </td>
                        <td class="py-3" style="max-width: 340px;">
                            @if($role->users && $role->users->isNotEmpty())
                                <div class="d-flex flex-wrap gap-1">
                                    @foreach($role->users->take(5) as $assignedUser)
                                        <a href="{{ route('users.show', $assignedUser) }}" class="user-chip" title="عرض ملف وصلاحيات {{ $assignedUser->name }}">
                                            <i class="bi bi-person-check-fill"></i> {{ $assignedUser->name }}
                                        </a>
                                    @endforeach
                                    @if($role->users->count() > 5)
                                        <a href="{{ route('roles.show', $role) }}" class="badge bg-secondary-subtle text-secondary rounded-pill text-decoration-none px-2 py-1">
                                            +{{ $role->users->count() - 5 }} آخرين
                                        </a>
                                    @endif
                                </div>
                            @else
                                <span class="text-muted small"><i class="bi bi-person-dash me-1"></i> لا يوجد موظفون حالياً</span>
                            @endif
                        </td>
                        <td class="py-3 text-center">
                            <span class="role-count-pill bg-success bg-opacity-10 text-success">
                                <i class="bi bi-key"></i> {{ $role->permissions_count ?? 0 }} صلاحية
                            </span>
                        </td>
                        <td class="pe-4 py-3 text-end">
                            <div class="d-inline-flex gap-1">
                                <a href="{{ route('roles.show', $role) }}" class="action-btn btn btn-light border text-secondary" title="عرض التفاصيل وإسناد موظفين">
                                    <i class="bi bi-eye"></i>
                                </a>
                                @if($u && $u->hasPermission('roles.edit'))
                                <a href="{{ route('roles.edit', $role) }}" class="action-btn btn btn-light border text-primary" title="تعديل الصلاحيات والموظفين">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                @endif
                                @if($u && $u->hasPermission('roles.delete') && $role->key !== 'admin')
                                    <form method="POST" action="{{ route('roles.destroy', $role) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="action-btn btn btn-light border text-danger" title="حذف الدور">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5">
                            <div class="py-4">
                                <i class="bi bi-shield-slash display-4 text-muted d-block mb-3"></i>
                                <h5 class="fw-bold text-muted">لا توجد أدوار مطابقة</h5>
                                <p class="text-muted small mb-3">لم يتم العثور على أي أدوار مطابقة لعملية البحث الحالية.</p>
                                @if($u && $u->hasPermission('roles.create'))
                                <a href="{{ route('roles.create') }}" class="btn btn-primary rounded-3 fw-bold">
                                    <i class="bi bi-plus-lg me-1"></i> إضافة دور جديد
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($roles->hasPages())
        <div class="card-footer bg-transparent border-top py-3 px-4">
            {{ $roles->withQueryString()->links() }}
        </div>
    @endif
</div>
@endsection
