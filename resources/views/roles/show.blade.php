@extends('layouts.app')
@section('title', 'تفاصيل الدور: ' . $role->name)
@section('content')
@php $u = auth()->user(); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
    <div>
        <h3 class="mb-1 fw-bold">
            <i class="bi bi-shield-check text-primary me-2"></i> الدور الوظيفي: {{ $role->name }}
        </h3>
        <div class="text-muted small">
            المعرّف البرمجي: <code class="px-2 py-1 bg-light rounded border">{{ $role->key }}</code>
            <span class="mx-2">|</span>
            إجمالي الصلاحيات المفعلة: <strong class="text-success">{{ $role->permissions->count() }} صلاحية</strong>
            <span class="mx-2">|</span>
            الموظفون المسند إليهم الدور: <strong class="text-primary">{{ $role->users->count() }} موظف</strong>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if($u && $u->hasPermission('roles.edit'))
        <a class="btn btn-primary fw-bold" href="{{ route('roles.edit', $role) }}">
            <i class="bi bi-pencil-square me-1"></i> تعديل صلاحيات وموظفي الدور
        </a>
        @endif
        <a class="btn btn-outline-secondary" href="{{ route('roles.index') }}">
            <i class="bi bi-arrow-right me-1"></i> رجوع للأدوار
        </a>
    </div>
</div>

@if($role->description)
    <div class="alert alert-light border mb-4">{{ $role->description }}</div>
@endif

{{-- 1. قسم إدارة الموظفين المسند إليهم هذا الدور --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-header bg-white border-bottom py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
        <div>
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i> الموظفون المسند إليهم دور ({{ $role->name }})
            </h5>
            <small class="text-muted">يمكنك إضافة موظف لهذا الدور مباشرة من هنا أو إزالته، كما يمكنك تفعيل خيار الدور الوحيد لمنع تداخل صلاحيات أدوار قديمة.</small>
        </div>
        <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2 fw-bold">
            {{ $role->users->count() }} موظف في هذا الدور
        </span>
    </div>
    <div class="card-body p-4">
        @if($u && $u->hasPermission('roles.edit'))
        <form method="POST" action="{{ route('roles.attachUser', $role) }}" class="row g-3 align-items-end bg-light p-3 rounded-4 border mb-4">
            @csrf
            <div class="col-md-5">
                <label class="form-label fw-bold small mb-1">اختر الموظف لإسناد هذا الدور إليه</label>
                <select name="user_id" class="form-select" required>
                    <option value="">-- اختر موظفاً من القائمة --</option>
                    @foreach($allUsers as $emp)
                        @php
                            $currentRoleNames = $emp->roles->pluck('name')->implode('، ');
                        @endphp
                        <option value="{{ $emp->id }}">
                            {{ $emp->name }}
                            @if($emp->job_title) ({{ $emp->job_title }}) @endif
                            — [الأدوار الحالية: {{ $currentRoleNames ?: 'بدون دور' }}]
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <div class="form-check form-switch bg-white p-2 px-3 rounded-3 border">
                    <input class="form-check-input ms-0 me-2" type="checkbox" name="exclusive_role" value="1" id="exclusiveRoleSwitch" checked>
                    <label class="form-check-label small fw-bold text-dark" for="exclusiveRoleSwitch">
                        جعله الدور الوحيد للموظف (إلغاء أي أدوار قديمة متراكمة)
                    </label>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-success w-100 fw-bold">
                    <i class="bi bi-person-plus-fill me-1"></i> إسناد الدور للموظف
                </button>
            </div>
        </form>
        @endif

        @if($role->users->isNotEmpty())
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>اسم الموظف</th>
                            <th>المسمى الوظيفي / البريد</th>
                            <th>الأدوار الحالية للموظف</th>
                            <th class="text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($role->users as $assignedUser)
                            <tr>
                                <td class="fw-bold">
                                    <a href="{{ route('users.show', $assignedUser) }}" class="text-decoration-none text-dark">
                                        <i class="bi bi-person-circle text-primary me-1"></i> {{ $assignedUser->name }}
                                    </a>
                                </td>
                                <td class="small text-muted">
                                    {{ $assignedUser->job_title ?: 'موظف' }} — {{ $assignedUser->email }}
                                </td>
                                <td>
                                    @foreach($assignedUser->roles as $r)
                                        <span class="badge {{ $r->id === $role->id ? 'bg-primary' : 'bg-warning text-dark' }} me-1">
                                            {{ $r->name }}
                                        </span>
                                    @endforeach
                                    @if($assignedUser->roles->count() > 1)
                                        <small class="text-danger fw-bold d-block mt-1">
                                            <i class="bi bi-exclamation-triangle-fill"></i> تنبيه: لدى الموظف أكثر من دور (تُجمع صلاحياتها معاً)
                                        </small>
                                    @endif
                                </td>
                                <td class="text-end">
                                    <div class="d-inline-flex gap-1">
                                        @if($u && $u->hasPermission('users.edit'))
                                            <a href="{{ route('users.edit', $assignedUser) }}" class="btn btn-sm btn-outline-primary" title="تخصيص صلاحيات خاصة لهذا الموظف">
                                                <i class="bi bi-sliders me-1"></i> تخصيص صلاحيات الموظف
                                            </a>
                                        @endif
                                        @if($u && $u->hasPermission('roles.edit'))
                                            <form method="POST" action="{{ route('roles.detachUser', [$role, $assignedUser]) }}" class="d-inline" onsubmit="return confirm('هل تريد إزالة هذا الدور من الموظف {{ $assignedUser->name }}؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="سحب الدور من الموظف">
                                                    <i class="bi bi-person-x-fill"></i> إزالة من الدور
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="text-center py-4 text-muted">
                <i class="bi bi-people fs-2 d-block mb-2"></i>
                لا يوجد موظفون مسند إليهم هذا الدور حالياً. يمكنك اختيار موظف من الأعلى لإسناد الدور إليه فوراً.
            </div>
        @endif
    </div>
</div>

{{-- 2. قسم الصلاحيات المفعلة في هذا الدور --}}
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-bold">
        <i class="bi bi-key-fill text-success me-2"></i> الصلاحيات الفعلية الممنوحة لهذا الدور ({{ $role->permissions->count() }} صلاحية)
    </h5>
</div>

<div class="row g-3">
    @forelse($permissions as $group => $items)
        <div class="col-md-4">
            <div class="card h-100 border-0 shadow-sm rounded-4">
                <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center py-3">
                    <span>{{ $group }}</span>
                    <span class="badge bg-success rounded-pill">{{ $items->count() }}</span>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        @foreach($items as $p)
                            <li class="mb-2 d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                <div>
                                    <span class="fw-semibold d-block">{{ $p->name }}</span>
                                    <code class="text-muted" style="font-size: 0.72rem;">{{ $p->key }}</code>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="alert alert-warning">لا توجد صلاحيات مضافة لهذا الدور حتى الآن.</div>
        </div>
    @endforelse
</div>
@endsection
