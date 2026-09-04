@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <h4 class="mb-1">
                    <i class="bi bi-shield-lock text-primary"></i>
                    إضافة دور جديد
                </h4>
                <p class="text-muted mb-0">إنشاء دور جديد وتحديد صلاحياته</p>
            </div>
            <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <form method="POST" action="{{ route('roles.store') }}">
            @csrf

            <div class="row">
                <div class="col-lg-4 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent py-3">
                            <h5 class="mb-0 fw-bold">بيانات الدور</h5>
                        </div>
                        <div class="card-body p-4">
                            <div class="mb-3">
                                <label class="form-label fw-bold">الاسم</label>
                                <input name="name" class="form-control" required placeholder="مثال: مدير النظام">
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">المعرف (Key)</label>
                                <input name="key" class="form-control font-monospace" required placeholder="مثال: admin">
                                <div class="form-text">يجب أن يكون فريداً وباللغة الإنجليزية.</div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-bold">الوصف</label>
                                <textarea name="description" class="form-control" rows="4"
                                    placeholder="وصف مختصر لمهام هذا الدور..."></textarea>
                            </div>
                            <div class="d-grid">
                                <button class="btn btn-primary py-2">
                                    <i class="bi bi-plus-lg me-1"></i> إنشاء الدور
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                            <h5 class="mb-0 fw-bold">الصلاحيات</h5>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="selectAll">
                                <label class="form-check-label" for="selectAll">تحديد الكل</label>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                @foreach($permissions as $group => $perms)
                                    <div class="col-md-6">
                                        <div class="border rounded bg-body-tertiary h-100">
                                            <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                                                <h6 class="text-uppercase fw-bold text-primary mb-0">{{ $group }}</h6>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input group-toggle" type="checkbox"
                                                        data-group="group-{{ $loop->index }}">
                                                </div>
                                            </div>
                                            <div class="p-3">
                                                @foreach($perms as $perm)
                                                    <div class="form-check mb-2">
                                                        @if($perm->key === 'dashboard.view')
                                                            <input type="hidden" name="permissions[]" value="{{ $perm->id }}">
                                                            <input class="form-check-input perm-checkbox group-{{ $loop->parent->index }}"
                                                                type="checkbox" id="perm_{{ $perm->id }}" checked disabled>
                                                        @else
                                                            <input class="form-check-input perm-checkbox group-{{ $loop->parent->index }}"
                                                                type="checkbox" name="permissions[]" value="{{ $perm->id }}"
                                                                id="perm_{{ $perm->id }}" @checked(in_array($perm->id, old('permissions', [])))>
                                                        @endif
                                                        <label class="form-check-label d-block" for="perm_{{ $perm->id }}">
                                                            <span class="fw-medium">{{ $perm->name }}</span>
                                                            @if($perm->key === 'dashboard.view')
                                                                <span class="badge bg-primary-subtle text-primary ms-1">أساسية</span>
                                                            @elseif(str_contains($perm->key, '.manage.'))
                                                                <span class="badge bg-success-subtle text-success ms-1"><i class="bi bi-pin-angle-fill me-1"></i>مخصصة</span>
                                                            @endif
                                                            <br>
                                                            <small class="text-muted font-monospace"
                                                                style="font-size: 0.75rem;">{{ $perm->key }}</small>
                                                        </label>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Select All
            document.getElementById('selectAll').addEventListener('change', function () {
                const checkboxes = document.querySelectorAll('.perm-checkbox:not(:disabled)');
                checkboxes.forEach(cb => cb.checked = this.checked);
                document.querySelectorAll('.group-toggle').forEach(cb => cb.checked = this.checked);
            });

            // Group Toggle
            document.querySelectorAll('.group-toggle').forEach(toggle => {
                toggle.addEventListener('change', function () {
                    const group = this.dataset.group;
                    document.querySelectorAll('.' + group + ':not(:disabled)').forEach(cb => cb.checked = this.checked);
                });
            });
        });
    </script>
@endsection

