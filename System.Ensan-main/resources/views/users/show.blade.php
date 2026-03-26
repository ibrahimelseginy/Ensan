@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <h4 class="mb-0">
                <i class="bi bi-person-circle text-primary"></i>
                ملف المستخدم
            </h4>
            <div class="btn-group">
                @if(!$pendingRequest)
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                @endif
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar / Profile Summary -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-4 position-relative d-inline-block">
                            <div class="rounded-circle shadow-lg overflow-hidden bg-white d-flex align-items-center justify-content-center mx-auto profile-avatar-container" style="width: 160px; height: 160px; border: 5px solid white;">
                                @if($user->profile_photo_path)
                                    <img src="{{ $user->image_url }}"
                                        class="w-100 h-100"
                                        style="object-fit: cover;" alt="{{ $user->name }}"
                                        onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&color=7F9CF5&background=EBF4FF';">
                                @else
                                    <div class="w-100 h-100 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold display-4">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            <label for="profile_photo_upload"
                                class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle shadow-lg border border-white profile-camera-btn"
                                style="cursor: pointer; width: 45px; height: 45px; display: flex; align-items: center; justify-content: center; transform: translate(-10%, -10%); transition: all 0.3s ease;"
                                title="تغيير الصورة">
                                <i class="bi bi-camera-fill fs-5"></i>
                            </label>
                            <form id="avatar-form" action="{{ route('users.update', $user) }}" method="POST"
                                enctype="multipart/form-data" class="d-none">
                                @csrf
                                @method('PUT')
                                <input type="file" id="profile_photo_upload" name="profile_photo" accept="image/*"
                                    onchange="if(confirm('هل تريد تغيير الصورة الشخصية؟')) document.getElementById('avatar-form').submit()">
                            </form>
                        </div>
                        <h3 class="fw-bold mb-1">{{ $user->name }}</h3>
                        <p class="text-muted mb-3">{{ $user->email }}</p>

                        <div class="mb-4">
                            @if($user->active)
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-check-circle me-1"></i> نشط</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-x-circle me-1"></i> غير نشط</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            @php $currentUser = request()->user(); @endphp
                            @if($pendingRequest)
                                <div class="alert alert-warning mb-0 p-3 border-0 bg-warning bg-opacity-10 text-warning rounded-4 shadow-sm text-start">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-hourglass-split fs-5"></i>
                                        <span class="fw-bold small">طلب قيد المراجعة</span>
                                    </div>
                                    <p class="x-small mb-0 mt-1 opacity-75">يوجد طلب تعديل أو حذف لهذا المستخدم قيد المراجعة حالياً.</p>
                                </div>
                            @elseif($currentUser)
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    {{ ($currentUser->hasRole('admin') || $currentUser->hasRole('manager') || $currentUser->hasRole('finance')) ? 'تعديل البيانات' : 'طلب تعديل البيانات' }}
                                </a>
                                
                                <form action="{{ route('users.destroy', $user) }}" method="POST"
                                      onsubmit="return confirm('{{ ($currentUser->hasRole('admin') || $currentUser->hasRole('manager') || $currentUser->hasRole('finance')) ? 'هل أنت متأكد من حذف هذا المستخدم؟' : 'هل أنت متأكد من طلب إلغاء هذا المستخدم؟' }}');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger w-100">
                                        <i class="bi bi-x-circle me-2"></i>
                                        {{ ($currentUser->hasRole('admin') || $currentUser->hasRole('manager') || $currentUser->hasRole('finance')) ? 'حذف المستخدم' : 'طلب إلغاء' }}
                                    </button>
                                </form>
                            @endif

                            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content / Details -->
            <div class="col-md-8">
                <!-- Job Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-briefcase me-2"></i> المعلومات الوظيفية</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">المسمى الوظيفي</label>
                                <div class="fw-medium fs-5">{{ $user->job_title ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">القسم / الإدارة</label>
                                <div class="fw-medium fs-5">{{ $user->department ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">الراتب</label>
                                <div class="fw-medium fs-5">
                                    {{ $user->salary ? number_format($user->salary, 2) . ' ج.م' : '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">تاريخ الانضمام</label>
                                <div class="fw-medium fs-5">
                                    {{ $user->join_date ? \Carbon\Carbon::parse($user->join_date)->format('Y-m-d') : '—' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Additional Info & Roles -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-shield-lock me-2"></i> المعلومات الإضافية</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">رقم الهاتف</label>
                                <div class="fw-medium fs-5"><a href="tel:{{ $user->phone }}" class="text-decoration-none text-dark">{{ $user->phone ?? '—' }}</a></div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">البريد الإلكتروني</label>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="fw-medium">{{ $user->email }}</span>
                                    <button class="btn btn-sm btn-light border-0 py-0" onclick="navigator.clipboard.writeText('{{ $user->email }}'); alert('تم النسخ')"><i class="bi bi-copy"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="text-muted small mb-1">الأدوار الممنوحة</label>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @forelse($user->roles as $role)
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis fs-6 px-3 py-2">
                                            {{ $role->name }}
                                            @if($role->description)
                                                <i class="bi bi-info-circle ms-1" data-bs-toggle="tooltip"
                                                    title="{{ $role->description }}"></i>
                                            @endif
                                        </span>
                                    @empty
                                        <span class="text-muted small italic">لا توجد أدوار معينة</span>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attached Documents -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-file-earmark-image me-2"></i> المستندات المرفقة</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <label class="text-muted small d-block mb-2">صورة العقد</label>
                                    @if($user->contract_image)
                                        <a href="{{ $user->getFileUrl('contract_image') }}" target="_blank" class="d-block mb-2">
                                            @if(Str::endsWith($user->contract_image, '.pdf'))
                                                <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" style="height: 80px; width: 100%;">
                                                    <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                </div>
                                            @else
                                                <img src="{{ $user->getFileUrl('contract_image') }}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                            @endif
                                        </a>
                                        <a href="{{ $user->getFileUrl('contract_image') }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">عرض</a>
                                    @else
                                        <div class="py-4 text-muted small"><i class="bi bi-x-circle me-1"></i> لا توجد صورة</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <label class="text-muted small d-block mb-2">الفيش الجنائي</label>
                                    @if($user->criminal_record_image)
                                        <a href="{{ $user->getFileUrl('criminal_record_image') }}" target="_blank" class="d-block mb-2">
                                            @if(Str::endsWith($user->criminal_record_image, '.pdf'))
                                                <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" style="height: 80px; width: 100%;">
                                                    <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                </div>
                                            @else
                                                <img src="{{ $user->getFileUrl('criminal_record_image') }}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                            @endif
                                        </a>
                                        <a href="{{ $user->getFileUrl('criminal_record_image') }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">عرض</a>
                                    @else
                                        <div class="py-4 text-muted small"><i class="bi bi-x-circle me-1"></i> لا توجد صورة</div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-light text-center">
                                    <label class="text-muted small d-block mb-2">البطاقة الشخصية</label>
                                    @if($user->id_card_image)
                                        <a href="{{ $user->getFileUrl('id_card_image') }}" target="_blank" class="d-block mb-2">
                                            @if(Str::endsWith($user->id_card_image, '.pdf'))
                                                <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" style="height: 80px; width: 100%;">
                                                    <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                </div>
                                            @else
                                                <img src="{{ $user->getFileUrl('id_card_image') }}" class="img-thumbnail" style="height: 80px; width: 100%; object-fit: cover;">
                                            @endif
                                        </a>
                                        <a href="{{ $user->getFileUrl('id_card_image') }}" target="_blank" class="btn btn-sm btn-outline-primary w-100">عرض</a>
                                    @else
                                        <div class="py-4 text-muted small"><i class="bi bi-x-circle me-1"></i> لا توجد صورة</div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i> سجل الحضور</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="px-4 py-3">التاريخ</th>
                                        <th class="px-4 py-3">الدخول</th>
                                        <th class="px-4 py-3">الخروج</th>
                                        <th class="px-4 py-3">التقييم</th>
                                        <th class="px-4 py-3">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->employeeAttendances()->latest()->take(5)->get() as $attendance)
                                        <tr>
                                            <td class="px-4">{{ $attendance->date->format('Y-m-d') }}</td>
                                            <td class="px-4">
                                                @if($attendance->check_in_at)
                                                    <span class="badge bg-success-subtle text-success rounded-pill fw-normal px-2">
                                                        {{ \Carbon\Carbon::parse($attendance->check_in_at)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($attendance->check_out_at)
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary rounded-pill fw-normal px-2">
                                                        {{ \Carbon\Carbon::parse($attendance->check_out_at)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($attendance->rating)
                                                    <span class="text-warning small" title="{{ $attendance->evaluation_notes }}">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="bi bi-star{{ $i <= $attendance->rating ? '-fill' : '' }}"></i>
                                                        @endfor
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 small text-muted">{{ Str::limit($attendance->notes ?? '—', 30) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-calendar-x display-6 mb-2 d-block opacity-50"></i>
                                                لا يوجد سجلات حضور حديثة
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Assigned Tasks -->
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i> المهام المسندة</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="px-4 py-3">المهمة</th>
                                        <th class="px-4 py-3">تاريخ الاستحقاق</th>
                                        <th class="px-4 py-3">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->assignedTasks()->latest()->take(5)->get() as $task)
                                        <tr>
                                            <td class="px-4 fw-medium">{{ $task->title }}</td>
                                            <td class="px-4">{{ $task->due_date ? $task->due_date->format('Y-m-d') : '—' }}</td>
                                            <td class="px-4">
                                                @if($task->status == 'completed' || $task->status == 'done')
                                                    <span
                                                        class="badge bg-success-subtle text-success rounded-pill px-2">منجزة</span>
                                                @elseif($task->status == 'in_progress')
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2">قيد
                                                        التنفيذ</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2">قيد
                                                        الانتظار</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="bi bi-clipboard-x display-6 mb-2 d-block opacity-50"></i>
                                                لا توجد مهام مسندة حالياً
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

