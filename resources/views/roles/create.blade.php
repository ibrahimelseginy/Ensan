@extends('layouts.app')
@section('title', 'إنشاء دور وظيفي جديد')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h3 class="mb-1 fw-bold"><i class="bi bi-shield-plus text-primary me-2"></i> إنشاء دور وظيفي جديد</h3>
        <p class="text-muted small mb-0">حدد اسم الدور، واختر الصلاحيات الدقيقة التي تريد منحها، ويمكنك إسناد الدور لموظف أو أكثر مباشرة.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i> رجوع للأدوار
    </a>
</div>

<form method="POST" action="{{ route('roles.store') }}" class="card border-0 shadow-sm rounded-4 p-4">
    @csrf
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <label class="form-label fw-bold">اسم الدور (بالعربية) <span class="text-danger">*</span></label>
            <input name="name" id="roleNameInput" class="form-control" value="{{ old('name') }}" placeholder="مثال: محاسب فرع طنطا / مسؤول مشروع زاد" required>
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">المعرّف البرمجي (Key) <small class="text-muted">(يتولد تلقائياً أو اكتبه بالإنجليزية)</small></label>
            <input name="key" id="roleKeyInput" class="form-control" value="{{ old('key') }}" placeholder="مثال: tanta_accountant">
        </div>
        <div class="col-md-4">
            <label class="form-label fw-bold">وصف الدور (اختياري)</label>
            <input name="description" class="form-control" value="{{ old('description') }}" placeholder="وصف مختصر لمهام هذا الدور">
        </div>
    </div>

    {{-- اختيار الموظفين لإسناد الدور إليهم فوراً --}}
    @if(isset($users) && $users->isNotEmpty())
    <div class="card bg-light border rounded-4 p-3 mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-2">
            <div>
                <h6 class="fw-bold mb-0 text-primary"><i class="bi bi-people-fill me-1"></i> إسناد هذا الدور لموظفين محددين (اختياري)</h6>
                <small class="text-muted">يمكنك اختيار موظف أو أكثر ليتم منحهم هذا الدور فور إنشائه.</small>
            </div>
            <div class="form-check form-switch bg-white px-3 py-1 rounded-3 border">
                <input class="form-check-input ms-0 me-2" type="checkbox" name="exclusive_users" value="1" id="exclusiveUsersCreate" checked>
                <label class="form-check-label small fw-bold text-dark" for="exclusiveUsersCreate">
                    إلغاء أي أدوار قديمة متراكمة للموظف المختار والاكتفاء بهذا الدور فقط
                </label>
            </div>
        </div>
        <div class="mb-2">
            <input type="text" id="userSearchBox" class="form-control form-control-sm" placeholder="ابحث عن اسم الموظف أو المسمى الوظيفي...">
        </div>
        <div class="row g-2" style="max-height: 210px; overflow-y: auto;" id="usersListContainer">
            @foreach($users as $emp)
                @php
                    $empRoleNames = $emp->roles->pluck('name')->implode('، ');
                @endphp
                <div class="col-md-4 user-item-row" data-search="{{ mb_strtolower($emp->name . ' ' . $emp->job_title . ' ' . $emp->email) }}">
                    <label class="d-flex align-items-center gap-2 p-2 bg-white rounded-3 border w-100" style="cursor: pointer;">
                        <input class="form-check-input mt-0" type="checkbox" name="users[]" value="{{ $emp->id }}"
                            @checked(in_array($emp->id, old('users', [])))>
                        <div class="text-truncate">
                            <span class="fw-bold small d-block text-truncate">{{ $emp->name }}</span>
                            <small class="text-muted d-block text-truncate" style="font-size: 0.72rem;">
                                {{ $emp->job_title ?: 'موظف' }}
                                @if($empRoleNames) — <span class="text-primary">[{{ $empRoleNames }}]</span> @endif
                            </small>
                        </div>
                    </label>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- شريط التحكم والبحث في الصلاحيات --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3 bg-dark p-3 rounded-4" style="background: #1e293b !important; color: #ffffff !important;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-key-fill fs-5 text-warning"></i>
            <h5 class="mb-0 fw-bold" style="color: #ffffff !important;">جدول صلاحيات النظام الكامل</h5>
            <span class="badge bg-warning text-dark ms-2" id="selectedPermsCounter">0 صلاحية محددة</span>
        </div>
        <div class="d-flex flex-wrap gap-2 align-items-center" style="min-width: 320px;">
            <input type="text" id="permSearchBox" class="form-control form-control-sm" style="width: 240px;" placeholder="ابحث عن صلاحية أو قسم...">
            <button type="button" class="btn btn-sm btn-success fw-bold" id="btnCheckAllPerms">تحديد الكل</button>
            <button type="button" class="btn btn-sm btn-outline-light fw-bold" id="btnUncheckAllPerms" style="color: #ffffff !important; border-color: rgba(255,255,255,0.6) !important;">إلغاء الكل</button>
        </div>
    </div>

    <div class="row g-3" id="permissionsGrid">
        @foreach($permissions as $group => $items)
            <div class="col-md-4 perm-group-card" data-group-title="{{ mb_strtolower($group) }}">
                <div class="card h-100 border shadow-sm rounded-4">
                    <div class="card-header bg-light fw-bold d-flex justify-content-between align-items-center py-2 px-3">
                        <span class="small fw-bold">{{ $group }}</span>
                        <div class="btn-group btn-group-sm">
                            <button type="button" class="btn btn-outline-primary btn-xs py-0 px-2 toggle-group-btn" data-check="1">الكل</button>
                            <button type="button" class="btn btn-outline-secondary btn-xs py-0 px-2 toggle-group-btn" data-check="0">إلغاء</button>
                        </div>
                    </div>
                    <div class="card-body py-2 px-3" style="max-height: 320px; overflow-y: auto;">
                        @foreach($items as $p)
                            <div class="form-check py-1 perm-item-row" data-perm-text="{{ mb_strtolower($p->name . ' ' . $p->key . ' ' . $group) }}">
                                <input class="form-check-input perm-checkbox" type="checkbox" name="permissions[]" value="{{ $p->id }}" id="p{{ $p->id }}"
                                    @checked(in_array($p->id, old('permissions', [])))>
                                <label class="form-check-label d-block" for="p{{ $p->id }}" style="cursor: pointer;">
                                    <span class="fw-semibold small">{{ $p->name }}</span>
                                    <code class="text-muted d-block" style="font-size: 0.7rem;">{{ $p->key }}</code>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4 d-flex gap-2 border-top pt-3">
        <button class="btn btn-primary btn-lg px-5 fw-bold" type="submit">
            <i class="bi bi-check2-circle me-1"></i> حفظ الدور والصلاحيات
        </button>
        <a class="btn btn-outline-secondary btn-lg" href="{{ route('roles.index') }}">إلغاء</a>
    </div>
</form>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const updateCounter = () => {
        const count = document.querySelectorAll('.perm-checkbox:checked').length;
        const badge = document.getElementById('selectedPermsCounter');
        if (badge) badge.textContent = count + ' صلاحية محددة';
    };

    document.querySelectorAll('.perm-checkbox').forEach(cb => {
        cb.addEventListener('change', updateCounter);
    });
    updateCounter();

    document.getElementById('btnCheckAllPerms')?.addEventListener('click', () => {
        document.querySelectorAll('.perm-item-row:not([style*="display: none"]) .perm-checkbox').forEach(cb => cb.checked = true);
        updateCounter();
    });

    document.getElementById('btnUncheckAllPerms')?.addEventListener('click', () => {
        document.querySelectorAll('.perm-item-row:not([style*="display: none"]) .perm-checkbox').forEach(cb => cb.checked = false);
        updateCounter();
    });

    document.querySelectorAll('.toggle-group-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            const shouldCheck = this.getAttribute('data-check') === '1';
            const card = this.closest('.perm-group-card');
            card?.querySelectorAll('.perm-item-row:not([style*="display: none"]) .perm-checkbox').forEach(cb => cb.checked = shouldCheck);
            updateCounter();
        });
    });

    document.getElementById('permSearchBox')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('.perm-group-card').forEach(card => {
            let visibleCount = 0;
            card.querySelectorAll('.perm-item-row').forEach(row => {
                const txt = row.getAttribute('data-perm-text') || '';
                const match = !q || txt.includes(q);
                row.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });
            card.style.display = visibleCount > 0 ? '' : 'none';
        });
    });

    document.getElementById('userSearchBox')?.addEventListener('input', function () {
        const q = this.value.trim().toLowerCase();
        document.querySelectorAll('.user-item-row').forEach(row => {
            const txt = row.getAttribute('data-search') || '';
            row.style.display = (!q || txt.includes(q)) ? '' : 'none';
        });
    });
});
</script>
@endpush
@endsection
