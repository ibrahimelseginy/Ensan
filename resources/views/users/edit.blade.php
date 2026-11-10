@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-person-gear text-primary"></i>
            تعديل المستخدم
        </h4>
        <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            @if(!$canManageUsers)
                <div class="alert alert-info border-0 bg-info bg-opacity-10 text-info-emphasis">
                    <i class="bi bi-info-circle me-2"></i>
                    أي تعديل ترسله سيظل معلقًا حتى يراجعه الأدمن ويوافق عليه.
                </div>
            @endif
            <form method="POST" action="{{ route('users.update', $user) }}" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">الاسم</label><input name="name" class="form-control"
                            value="{{ $user->name }}"></div>
                    <div class="col-md-6"><label class="form-label">البريد</label><input name="email" type="email"
                            class="form-control" value="{{ $user->email }}"></div>
                    @if($canManageUsers)
                        <div class="col-md-6">
                            <label class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <input name="password" type="password" class="form-control" id="password-input"
                                    autocomplete="new-password" placeholder="أدخل كلمة مرور جديدة فقط إن أردت تغييرها">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                    <i class="bi bi-eye" id="toggle-icon"></i>
                                </button>
                            </div>
                        </div>
                    @endif
                    <script>
                        function togglePassword() {
                            const passwordInput = document.getElementById('password-input');
                            const toggleIcon = document.getElementById('toggle-icon');
                            if (passwordInput.type === 'password') {
                                passwordInput.type = 'text';
                                toggleIcon.classList.remove('bi-eye');
                                toggleIcon.classList.add('bi-eye-slash');
                            } else {
                                passwordInput.type = 'password';
                                toggleIcon.classList.remove('bi-eye-slash');
                                toggleIcon.classList.add('bi-eye');
                            }
                        }
                    </script>
                    <div class="col-md-6"><label class="form-label">الهاتف</label><input name="phone" class="form-control"
                            value="{{ $user->phone }}"></div>
                    <div class="col-md-6"><label class="form-label">الرقم القومي</label><input name="national_id" class="form-control font-monospace"
                            maxlength="14" placeholder="14 رقم" value="{{ $user->national_id }}"></div>
                    <div class="col-md-6"><label class="form-label">القسم</label><input name="department"
                            class="form-control" value="{{ $user->department }}"></div>
                    <div class="col-md-6"><label class="form-label">المسمى الوظيفي</label><input name="job_title"
                            class="form-control" value="{{ $user->job_title }}"></div>
                    @if($canManageUsers)
                        <div class="col-md-6"><label class="form-label">الراتب</label><input name="salary" type="number"
                                step="1" class="form-control" value="{{ $user->salary ? (int)$user->salary : '' }}"></div>
                        <div class="col-md-6"><label class="form-label">تاريخ الانضمام</label><input name="join_date"
                                type="date" class="form-control" value="{{ $user->join_date }}"></div>
                        <div class="col-md-6"><label class="form-label">الرصيد السنوي لطلب الإجازات</label><input name="annual_leave_quota"
                                type="number" class="form-control" value="{{ $user->annual_leave_quota ?? 21 }}"></div>
                        <div class="col-md-6"><label class="form-label">رصيد الإجازات الحالي</label><input name="leave_balance"
                                type="number" class="form-control" value="{{ $user->leave_balance ?? 21 }}"></div>

                        <div class="col-md-6"><label class="form-label">تاريخ بداية العقد</label><input
                                name="contract_start_date" type="date" class="form-control"
                                value="{{ $user->contract_start_date ? $user->contract_start_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">تاريخ نهاية العقد</label>
                            <input name="contract_end_date" type="date" class="form-control"
                                value="{{ $user->contract_end_date ? $user->contract_end_date->format('Y-m-d') : '' }}">
                            <div class="form-text text-info">
                                <i class="bi bi-info-circle me-1"></i>
                                ملاحظة: سيتم إرسال إشعار قبل نهاية العقد بـ 60 يوم
                            </div>
                        </div>
                    @endif

                    @if($canManageUsers)
                    <div class="col-md-6">
                        <label class="form-label">الصورة الشخصية</label>
                        <input type="file" name="profile_photo" class="form-control" accept="image/*">
                        @if($user->profile_photo_path)
                            <div class="mt-2">
                                <img src="{{ $user->image_url }}" alt="Current Photo"
                                    class="img-thumbnail" style="height: 100px;">
                            </div>
                        @endif
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">صورة العقد</label>
                        <input type="file" name="contract_image" class="form-control" accept="image/*">
                        @if($user->contract_image)
                            <div class="mt-2">
                                <a href="{{ $user->getFileUrl('contract_image') }}" target="_blank">عرض صورة العقد
                                    الحالية</a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">صورة الفيش الجنائي</label>
                        <input type="file" name="criminal_record_image" class="form-control" accept="image/*">
                        @if($user->criminal_record_image)
                            <div class="mt-2">
                                <a href="{{ $user->getFileUrl('criminal_record_image') }}" target="_blank">عرض صورة الفيش
                                    الحالية</a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">صورة البطاقة الشخصية</label>
                        <input type="file" name="id_card_image" class="form-control" accept="image/*">
                        @if($user->id_card_image)
                            <div class="mt-2">
                                <a href="{{ $user->getFileUrl('id_card_image') }}" target="_blank">عرض صورة البطاقة
                                    الحالية</a>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <input type="hidden" name="roles_present" value="1">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0"><i class="bi bi-shield-lock text-primary me-1"></i> الأدوار الوظيفية للموظف</label>
                            <a href="{{ route('roles.index') }}" target="_blank" class="small text-decoration-none"><i
                                    class="bi bi-gear"></i> إدارة الأدوار</a>
                        </div>
                        <div class="card p-3 bg-body-tertiary" style="max-height: 220px; overflow-y: auto;">
                            @foreach($roles as $r)
                                <div class="form-check mb-2 d-flex justify-content-between align-items-center">
                                    <div>
                                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}"
                                            id="role_{{ $r->id }}" @checked($user->roles->contains($r->id))>
                                        <label class="form-check-label fw-semibold" for="role_{{ $r->id }}">
                                            {{ $r->name }} <code class="text-muted small">({{ $r->key }})</code>
                                        </label>
                                    </div>
                                    <a href="{{ route('roles.show', $r) }}" target="_blank" class="badge bg-light text-primary border text-decoration-none">عرض</a>
                                </div>
                            @endforeach
                        </div>
                        <small class="text-muted d-block mt-1">نصيحة: اختر دوراً واحداً فقط للموظف لتجنب تراكم صلاحيات من أدوار متعددة.</small>
                    </div>

                    @if(isset($permissionGroups) && $permissionGroups->isNotEmpty())
                    <div class="col-12 mt-3">
                        <input type="hidden" name="direct_permissions_present" value="1">
                        <div class="card border shadow-sm rounded-4 overflow-hidden">
                            <div class="card-header bg-dark text-white py-3 px-4 d-flex flex-wrap justify-content-between align-items-center gap-2">
                                <div>
                                    <h6 class="mb-0 fw-bold">
                                        <i class="bi bi-sliders text-warning me-2"></i> تخصيص صلاحيات إضافية مباشرة لهذا الموظف ({{ $user->name }})
                                    </h6>
                                    <small class="text-white-50">يمكنك منح هذا الموظف أي صلاحية محددة في النظام مباشرة بالإضافة إلى صلاحيات دوره الوظيفي.</small>
                                </div>
                                <div class="d-flex gap-2 align-items-center">
                                    <input type="text" id="empPermSearch" class="form-control form-control-sm" style="width: 240px;" placeholder="ابحث عن صلاحية أو قسم...">
                                </div>
                            </div>
                            <div class="card-body p-3" style="max-height: 420px; overflow-y: auto;">
                                @php
                                    $checkedDirect = old('direct_permissions', $directPermissionIds ?? []);
                                    $inheritedRolePerms = $rolePermissionIds ?? [];
                                @endphp
                                <div class="row g-3">
                                    @foreach($permissionGroups as $groupName => $groupPerms)
                                        <div class="col-md-4 emp-perm-group">
                                            <div class="card h-100 border rounded-3">
                                                <div class="card-header bg-light py-2 px-3 fw-bold small">{{ $groupName }}</div>
                                                <div class="card-body py-2 px-3" style="max-height: 240px; overflow-y: auto;">
                                                    @foreach($groupPerms as $p)
                                                        @php
                                                            $fromRole = in_array($p->id, $inheritedRolePerms);
                                                            $isDirect = in_array($p->id, $checkedDirect);
                                                        @endphp
                                                        <div class="form-check py-1 emp-perm-item" data-search="{{ mb_strtolower($p->name . ' ' . $p->key . ' ' . $groupName) }}">
                                                            <input class="form-check-input" type="checkbox" name="direct_permissions[]" value="{{ $p->id }}" id="dp_{{ $p->id }}"
                                                                @checked($isDirect)>
                                                            <label class="form-check-label d-block" for="dp_{{ $p->id }}" style="cursor: pointer;">
                                                                <span class="small fw-semibold">{{ $p->name }}</span>
                                                                @if($fromRole)
                                                                    <span class="badge bg-success-subtle text-success border border-success-subtle ms-1" style="font-size: 0.65rem;">ممنوحة من الدور</span>
                                                                @endif
                                                                <code class="text-muted d-block" style="font-size: 0.68rem;">{{ $p->key }}</code>
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
                    @push('scripts')
                    <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        document.getElementById('empPermSearch')?.addEventListener('input', function () {
                            const q = this.value.trim().toLowerCase();
                            document.querySelectorAll('.emp-perm-group').forEach(card => {
                                let visible = 0;
                                card.querySelectorAll('.emp-perm-item').forEach(item => {
                                    const txt = item.getAttribute('data-search') || '';
                                    const match = !q || txt.includes(q);
                                    item.style.display = match ? '' : 'none';
                                    if (match) visible++;
                                });
                                card.style.display = visible > 0 ? '' : 'none';
                            });
                        });
                    });
                    </script>
                    @endpush
                    @endif

                    <div class="col-12">
                        <input type="hidden" name="is_employee" value="{{ $user->is_volunteer ? 0 : 1 }}">
                        <input type="hidden" name="active" value="0">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="active" value="1"
                                @checked($user->active)><label class="form-check-label">نشط</label></div>
                    </div>
                    @endif
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('users.show', $user) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>
                        {{ $canManageUsers ? 'حفظ التغييرات' : 'إرسال طلب التعديل' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection



