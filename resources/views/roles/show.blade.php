@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Info -->
            <div class="col-md-3 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold display-4"
                                style="width: 100px; height: 100px;">
                                {{ strtoupper(substr($role->key, 0, 1)) }}
                            </div>
                        </div>
                        <h4 class="fw-bold mb-1">{{ $role->name }}</h4>
                        <p class="badge bg-secondary-subtle text-body border mb-3 fs-6">{{ $role->key }}</p>

                        @if($role->description)
                            <p class="text-muted small mb-4">{{ $role->description }}</p>
                        @endif

                        <div class="d-grid gap-2">
                            @if(request()->user()?->hasPermission('roles.edit'))
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square me-2"></i> تعديل الدور
                                </a>
                            @endif
                            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-right me-2"></i> رجوع للقائمة
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissions List -->
            <div class="col-md-9">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3 border-bottom-0">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold"><i class="bi bi-shield-check me-2 text-success"></i> الصلاحيات الممنوحة
                            </h5>
                            <span class="badge bg-success-subtle text-success">{{ $role->permissions->count() }}
                                صلاحية</span>
                            <span class="badge bg-info-subtle text-info">{{ $role->users_count }} مستخدم</span>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($role->permissions->count() > 0)
                            <div class="row g-4">
                                @foreach($permissions as $group => $perms)
                                    <div class="col-md-6 col-xl-4">
                                        <div class="border rounded p-3 h-100 bg-body-tertiary">
                                            <h6
                                                class="text-uppercase fw-bold text-primary mb-3 pb-2 border-bottom d-flex align-items-center">
                                                <i class="bi bi-shield-check me-2"></i>
                                                {{ $group }}
                                            </h6>
                                            <ul class="list-unstyled mb-0">
                                                @foreach($perms as $perm)
                                                    <li class="mb-2 d-flex align-items-start">
                                                        <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                                                        <div>
                                                            <span class="d-block fw-medium">{{ $perm->name }}</span>
                                                            <small class="text-muted font-monospace"
                                                                style="font-size: 0.75rem;">{{ $perm->key }}</small>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <div class="mb-3">
                                    <i class="bi bi-shield-exclamation display-1 text-muted opacity-25"></i>
                                </div>
                                <h5 class="text-muted">لا توجد صلاحيات ممنوحة لهذا الدور</h5>
                                <p class="text-muted small">قم بتعديل الدور لإضافة صلاحيات جديدة.</p>
                                <a href="{{ route('roles.edit', $role) }}" class="btn btn-sm btn-outline-primary mt-2">
                                    <i class="bi bi-plus-lg"></i> إضافة صلاحيات
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

