@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="page-header">
        <h4 class="mb-0">
            <i class="bi bi-person-plus text-primary"></i>
            إضافة مستخدم جديد
        </h4>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('users.store') }}" enctype="multipart/form-data">@csrf
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">الاسم</label><input name="name" class="form-control"
                            required></div>
                    <div class="col-md-6"><label class="form-label">البريد</label><input name="email" type="email"
                            class="form-control" required></div>
                    <div class="col-md-6">
                        <label class="form-label">كلمة المرور</label>
                        <div class="input-group">
                            <input name="password" type="password" class="form-control" required id="password-input">
                            <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                <i class="bi bi-eye" id="toggle-icon"></i>
                            </button>
                        </div>
                    </div>
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
                    <div class="col-md-6"><label class="form-label">الهاتف</label><input name="phone" class="form-control">
                    </div>
                    <div class="col-md-6"><label class="form-label">القسم</label><input name="department"
                            class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">المسمى الوظيفي</label><input name="job_title"
                            class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">الراتب</label><input name="salary" type="number"
                            step="0.01" class="form-control"></div>
                    <div class="col-md-6"><label class="form-label">تاريخ الانضمام</label><input name="join_date"
                            type="date" class="form-control"></div>

                    <div class="col-md-6"><label class="form-label">الرصيد السنوي لطلب الإجازات</label><input name="annual_leave_quota"
                            type="number" class="form-control" value="21"></div>
                    <div class="col-md-6"><label class="form-label">رصيد الإجازات الحالي</label><input name="leave_balance"
                            type="number" class="form-control" value="21"></div>

                    <div class="col-md-6"><label class="form-label">تاريخ بداية العقد</label><input
                            name="contract_start_date" type="date" class="form-control"></div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ نهاية العقد</label>
                        <input name="contract_end_date" type="date" class="form-control">
                        <div class="form-text text-info">
                            <i class="bi bi-info-circle me-1"></i>
                            ملاحظة: سيتم إرسال إشعار قبل نهاية العقد بـ 60 يوم
                        </div>
                    </div>

                    <div class="col-md-6"><label class="form-label">الصورة الشخصية</label><input type="file"
                            name="profile_photo" class="form-control" accept="image/*"></div>

                    <div class="col-md-6"><label class="form-label">صورة العقد</label><input type="file"
                            name="contract_image" class="form-control" accept="image/*"></div>
                    <div class="col-md-6"><label class="form-label">صورة الفيش الجنائي</label><input type="file"
                            name="criminal_record_image" class="form-control" accept="image/*"></div>
                    <div class="col-md-6"><label class="form-label">صورة البطاقة الشخصية</label><input type="file"
                            name="id_card_image" class="form-control" accept="image/*"></div>
                    <div class="col-md-6">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label mb-0">الأدوار</label>
                            <a href="{{ route('roles.index') }}" target="_blank" class="small text-decoration-none"><i
                                    class="bi bi-gear"></i> إدارة الأدوار</a>
                        </div>
                        <div class="card p-3 bg-body-tertiary" style="max-height: 200px; overflow-y: auto;">
                            @foreach($roles as $r)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $r->id }}"
                                        id="role_{{ $r->id }}">
                                    <label class="form-check-label" for="role_{{ $r->id }}">
                                        {{ $r->name }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-check"><input class="form-check-input" type="checkbox" name="active" value="1"
                                checked><label class="form-check-label">نشط</label></div>
                    </div>
                </div>
                <div class="d-flex gap-2 justify-content-end mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-x-lg me-1"></i> إلغاء
                    </a>
                    <button class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> حفظ المستخدم
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>
@endsection

