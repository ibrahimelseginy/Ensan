@extends('layouts.app')

@section('content')
    @php
        $currentUser = request()->user();
        $backRoute = $canViewUsers ? route('users.index') : route('dashboard.index');
    @endphp
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <h4 class="mb-0">
                <i class="bi bi-person-circle text-primary"></i>
                ملف المستخدم
            </h4>
            <div class="btn-group">
                @if(!$pendingRequest && ($isOwnProfile || $canManageUsers))
                    <a href="{{ route('users.edit', $user) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                @endif
                <a href="{{ $backRoute }}" class="btn btn-outline-secondary">
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
                            <div class="rounded-circle shadow-lg overflow-hidden bg-white d-flex align-items-center justify-content-center mx-auto profile-avatar-container" style="width: 160px; height: 160px; border: 5px solid white; position: relative;">
                                @if($user->image_url)
                                    <img src="{{ $user->image_url }}"
                                        class="w-100 h-100"
                                        style="object-fit: cover;" alt="{{ $user->name }}"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                    <div class="w-100 h-100 bg-primary-subtle text-primary align-items-center justify-content-center fw-bold display-4" style="display: none;">
                                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </div>
                                @else
                                    <div class="w-100 h-100 bg-primary-subtle text-primary d-flex align-items-center justify-content-center fw-bold display-4">
                                        {{ strtoupper(mb_substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                            </div>

                            @if(!$pendingRequest && ($isOwnProfile || $canManageUsers))
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
                            @endif
                        </div>
                        <h3 class="fw-bold mb-1">{{ $user->name }}</h3>
                        <p class="text-muted mb-3">{{ $user->email }}</p>

                        <div class="mb-3">
                            @if($user->active)
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-check-circle me-1"></i> نشط</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-x-circle me-1"></i> غير نشط</span>
                            @endif
                        </div>

                        {{-- تقييم أداء الموظف تحت اسمه مباشرة --}}
                        @php
                            $latestEval = $user->latestEvaluation;
                        @endphp
                        @if($latestEval)
                            <div class="p-3 rounded-4 mb-4 border shadow-2xs text-center" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.08) 0%, rgba(99, 102, 241, 0.08) 100%); border-color: rgba(139, 92, 246, 0.25) !important;">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="small text-muted fw-bold"><i class="bi bi-award-fill text-warning me-1"></i>تقييم الأداء</span>
                                    <span class="badge {{ $latestEval->getRatingBadgeClass() }} px-2 py-1 rounded-pill">{{ $latestEval->rating_label }}</span>
                                </div>
                                <div class="d-flex align-items-baseline justify-content-center gap-1 my-1">
                                    <span class="display-6 fw-bolder text-dark">{{ number_format($latestEval->total_score, 1) }}</span>
                                    <span class="text-muted fw-bold">/ 100</span>
                                    <span class="badge bg-light text-dark border ms-2 font-monospace">{{ $latestEval->percentage }}%</span>
                                </div>
                                @if($latestEval->evaluation_date)
                                    <div class="x-small text-muted mt-1"><i class="bi bi-calendar3 me-1"></i>{{ $latestEval->evaluation_date->format('Y-m-d') }}</div>
                                @endif
                            </div>
                        @else
                            <div class="p-2 rounded-3 mb-4 border border-dashed text-center text-muted small bg-light">
                                <i class="bi bi-clipboard-x me-1"></i> لا يوجد تقييم مسجل بعد
                            </div>
                        @endif

                        <div class="d-grid gap-2">
                            @if($pendingRequest)
                                <div class="alert alert-warning mb-0 p-3 border-0 bg-warning bg-opacity-10 text-warning rounded-4 shadow-sm text-start">
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-hourglass-split fs-5"></i>
                                        <span class="fw-bold small">طلب قيد المراجعة</span>
                                    </div>
                                    <p class="x-small mb-0 mt-1 opacity-75">يوجد طلب تعديل أو حذف لهذا المستخدم قيد المراجعة حالياً.</p>
                                </div>
                            @elseif($currentUser && ($isOwnProfile || $canManageUsers))
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">
                                    <i class="bi bi-pencil-square me-2"></i>
                                    {{ $canManageUsers ? 'تعديل البيانات' : 'طلب تعديل البيانات' }}
                                </a>
                                
                                @if($canDeleteUsers)
                                    <form action="{{ route('users.destroy', $user) }}" method="POST"
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger w-100">
                                            <i class="bi bi-x-circle me-2"></i> حذف المستخدم
                                        </button>
                                    </form>
                                @endif
                            @endif

                            <a href="{{ $backRoute }}" class="btn btn-outline-secondary">
                                {{ $canViewUsers ? 'رجوع للقائمة' : 'رجوع للرئيسية' }}
                            </a>
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
                                    {{ $user->salary ? number_format($user->salary, 0) . ' ج.م' : '—' }}</div>
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

                {{-- Performance Evaluation Section (نموذج تقييم وأداء الموظفين المعتمد) --}}
                @php
                    $evaluations = $user->employeeEvaluations;
                    $currentEval = $evaluations->first();
                    $criteriaList = \App\Models\EmployeeEvaluation::CRITERIA;
                @endphp
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="bi bi-award-fill me-2 text-warning"></i> تقييم أداء الموظف
                            <span class="badge bg-secondary-subtle text-secondary small fw-normal ms-2">نموذج معتمد من 10 بنود</span>
                        </h5>
                        @if($canManageUsers || auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasPermission('hr.evaluations'))
                            <button type="button" class="btn btn-sm btn-primary shadow-2xs" data-bs-toggle="modal" data-bs-target="#newEvaluationModal">
                                <i class="bi bi-plus-lg me-1"></i> تسجيل تقييم جديد
                            </button>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        @if($currentEval)
                            <div class="row g-3 align-items-center mb-4 p-3 rounded-4" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.05) 0%, rgba(59, 130, 246, 0.05) 100%); border: 1px solid rgba(139, 92, 246, 0.2);">
                                <div class="col-md-3 text-center border-end">
                                    <div class="text-muted small fw-bold mb-1">المجموع الكلي</div>
                                    <div class="display-5 fw-bolder text-primary mb-0 font-monospace">{{ number_format($currentEval->total_score, 1) }}</div>
                                    <div class="small text-muted">من 100 درجة</div>
                                </div>
                                <div class="col-md-3 text-center border-end">
                                    <div class="text-muted small fw-bold mb-1">النسبة المئوية</div>
                                    <div class="display-6 fw-bold text-dark mb-0 font-monospace">{{ $currentEval->percentage }}%</div>
                                    <div class="small text-muted">إجمالي وزن 100%</div>
                                </div>
                                <div class="col-md-3 text-center border-end">
                                    <div class="text-muted small fw-bold mb-1">نتيجة التقييم والأداء</div>
                                    <span class="badge {{ $currentEval->getRatingBadgeClass() }} fs-6 px-3 py-2 rounded-pill shadow-xs">
                                        <i class="bi bi-patch-check-fill me-1"></i> {{ $currentEval->rating_label }}
                                    </span>
                                    <div class="x-small text-muted mt-1">
                                        @if($currentEval->percentage >= 90) متميز (90% فأكثر)
                                        @elseif($currentEval->percentage >= 80) جيد جداً (80% - 89%)
                                        @elseif($currentEval->percentage >= 65) جيد (65% - 79%)
                                        @elseif($currentEval->percentage >= 50) مقبول (50% - 64%)
                                        @else ضعيف (أقل من 50%)
                                        @endif
                                    </div>
                                </div>
                                <div class="col-md-3 text-center">
                                    <div class="text-muted small fw-bold mb-1">تاريخ وفترة التقييم</div>
                                    <div class="fw-bold text-dark">{{ $currentEval->evaluation_date ? $currentEval->evaluation_date->format('Y-m-d') : '—' }}</div>
                                    <div class="small text-muted">الفترة: {{ $currentEval->evaluation_period ?: '—' }}</div>
                                    @if($currentEval->evaluator)
                                        <div class="x-small text-muted mt-1">بواسطة: {{ $currentEval->evaluator->name }}</div>
                                    @endif
                                </div>
                            </div>

                            {{-- Detailed Criteria Breakdown --}}
                            <h6 class="fw-bold text-dark mb-3"><i class="bi bi-list-stars me-1 text-primary"></i> تفاصيل درجات بنود التقييم (الدرجة من 5.0):</h6>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered table-sm align-middle text-center mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="text-start" style="width: 25%;">بند التقييم</th>
                                            <th style="width: 12%;">الوزن النسبي</th>
                                            <th style="width: 15%;">الدرجة الحاصل عليها (من 5)</th>
                                            <th style="width: 15%;">الدرجة الموزونة (من 100)</th>
                                            <th style="width: 33%;">المؤشر البياني</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($criteriaList as $key => $meta)
                                            @php
                                                $score = (float) ($currentEval->{$key} ?? 0);
                                                $weighted = round(($score * $meta['weight']) * 20, 2);
                                                $pct = ($score / 5.0) * 100;
                                            @endphp
                                            <tr>
                                                <td class="text-start fw-medium">
                                                    <span class="d-block">{{ $meta['label'] }}</span>
                                                    <span class="x-small text-muted">{{ $meta['desc'] }}</span>
                                                </td>
                                                <td><span class="badge bg-light text-dark border font-monospace">{{ ($meta['weight'] * 100) }}%</span></td>
                                                <td>
                                                    <span class="fw-bold font-monospace fs-6 text-dark">{{ number_format($score, 1) }}</span>
                                                    <span class="x-small text-muted">/ 5.0</span>
                                                </td>
                                                <td>
                                                    <span class="fw-bold font-monospace text-primary">{{ number_format($weighted, 1) }}</span>
                                                    <span class="x-small text-muted">/ {{ ($meta['weight'] * 100) }}</span>
                                                </td>
                                                <td>
                                                    <div class="progress" style="height: 10px;">
                                                        <div class="progress-bar {{ $pct >= 80 ? 'bg-success' : ($pct >= 60 ? 'bg-primary' : ($pct >= 50 ? 'bg-warning' : 'bg-danger')) }}"
                                                             role="progressbar" style="width: {{ $pct }}%;" aria-valuenow="{{ $pct }}" aria-valuemin="0" aria-valuemax="100">
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            @if($currentEval->notes || $currentEval->strengths || $currentEval->improvements)
                                <div class="row g-3 mb-4">
                                    @if($currentEval->strengths)
                                        <div class="col-md-4">
                                            <div class="p-3 bg-success-subtle rounded-3 border border-success-subtle h-100">
                                                <div class="fw-bold text-success small mb-1"><i class="bi bi-hand-thumbs-up me-1"></i> نقاط القوة والتميز:</div>
                                                <p class="small mb-0 text-dark">{{ $currentEval->strengths }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($currentEval->improvements)
                                        <div class="col-md-4">
                                            <div class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle h-100">
                                                <div class="fw-bold text-warning small mb-1"><i class="bi bi-graph-up-arrow me-1"></i> مجالات التحسين والتطوير:</div>
                                                <p class="small mb-0 text-dark">{{ $currentEval->improvements }}</p>
                                            </div>
                                        </div>
                                    @endif
                                    @if($currentEval->notes)
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 border h-100">
                                                <div class="fw-bold text-secondary small mb-1"><i class="bi bi-chat-left-text me-1"></i> ملاحظات الإدارة:</div>
                                                <p class="small mb-0 text-dark">{{ $currentEval->notes }}</p>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            {{-- Past Evaluations History --}}
                            @if($evaluations->count() > 1)
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fw-bold text-muted small mb-2"><i class="bi bi-clock-history me-1"></i> سجل التقييمات السابقة لهذا الموظف:</h6>
                                    <div class="table-responsive">
                                        <table class="table table-hover table-sm text-center mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>تاريخ التقييم</th>
                                                    <th>الفترة</th>
                                                    <th>المجموع</th>
                                                    <th>النسبة</th>
                                                    <th>التقدير</th>
                                                    <th>المُقيّم</th>
                                                    <th>إجراءات</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($evaluations as $ev)
                                                    <tr>
                                                        <td>{{ $ev->evaluation_date ? $ev->evaluation_date->format('Y-m-d') : '—' }}</td>
                                                        <td>{{ $ev->evaluation_period ?: '—' }}</td>
                                                        <td class="fw-bold font-monospace">{{ $ev->total_score }}</td>
                                                        <td class="font-monospace">{{ $ev->percentage }}%</td>
                                                        <td><span class="badge {{ $ev->getRatingBadgeClass() }}">{{ $ev->rating_label }}</span></td>
                                                        <td>{{ $ev->evaluator?->name ?? '—' }}</td>
                                                        <td>
                                                            @if($canManageUsers || auth()->user()->hasRole('admin'))
                                                                <form action="{{ route('employee-evaluations.destroy', $ev) }}" method="POST" class="d-inline"
                                                                      onsubmit="return confirm('هل تريد حذف هذا التقييم؟');">
                                                                    @csrf @method('DELETE')
                                                                    <button type="submit" class="btn btn-outline-danger btn-sm p-1" title="حذف التقييم">
                                                                        <i class="bi bi-trash"></i>
                                                                    </button>
                                                                </form>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-award display-4 mb-2 d-block opacity-25"></i>
                                <h6 class="fw-bold mb-1">لا يوجد تقييم أداء مسجل لهذا الموظف حتى الآن</h6>
                                <p class="small mb-3">يمكنك تسجيل أول تقييم للموظف طبقاً لنموذج الأداء والبنود العشرة المعتمدة.</p>
                                @if($canManageUsers || auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || auth()->user()->hasPermission('hr.evaluations'))
                                    <button type="button" class="btn btn-primary btn-sm px-3" data-bs-toggle="modal" data-bs-target="#newEvaluationModal">
                                        <i class="bi bi-plus-lg me-1"></i> تسجيل تقييم الموظف الآن
                                    </button>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-shield-lock me-2"></i> المعلومات الإضافية</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-muted small mb-1">رقم الهاتف</label>
                                <div class="fw-medium fs-5"><a href="tel:{{ $user->phone }}" class="text-decoration-none text-dark">{{ $user->phone ?? '—' }}</a></div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small mb-1">الرقم القومي</label>
                                <div class="fw-medium fs-5 font-monospace">{{ $user->national_id ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small mb-1">البريد الإلكتروني</label>
                                <div class="d-flex align-items-center gap-2 mt-1">
                                    <span class="fw-medium">{{ $user->email }}</span>
                                    <button class="btn btn-sm btn-light border-0 py-0" onclick="navigator.clipboard.writeText('{{ $user->email }}'); alert('تم النسخ')"><i class="bi bi-copy"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="text-muted small mb-0">الأدوار الممنوحة والصلاحيات الفعلية</label>
                                    @if($canManageUsers)
                                        <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-sliders me-1"></i> تعديل الأدوار وتخصيص الصلاحيات
                                        </a>
                                    @endif
                                </div>
                                <div class="d-flex flex-wrap gap-2 mt-1">
                                    @forelse($user->roles as $role)
                                        <a href="{{ route('roles.show', $role) }}" class="badge bg-primary-subtle text-primary border border-primary-subtle fs-6 px-3 py-2 text-decoration-none">
                                            <i class="bi bi-shield-check me-1"></i> {{ $role->name }}
                                            <small class="opacity-75">({{ $role->permissions->count() }} صلاحية)</small>
                                        </a>
                                    @empty
                                        <span class="text-muted small italic">لا توجد أدوار معينة</span>
                                    @endforelse
                                </div>

                                @php
                                    $directPermsList = ($user->relationLoaded('directPermissions') && $user->directPermissions) ? $user->directPermissions : collect();
                                    $allRolePermsList = $user->roles->flatMap->permissions;
                                    $effectivePermsList = $allRolePermsList->merge($directPermsList)->unique('id')->values();
                                @endphp

                                @if($directPermsList->isNotEmpty())
                                    <div class="mt-3">
                                        <label class="text-muted small mb-1 d-block">صلاحيات إضافية مخصصة مباشرة لهذا الموظف ({{ $directPermsList->count() }}):</label>
                                        <div class="d-flex flex-wrap gap-1">
                                            @foreach($directPermsList as $dp)
                                                <span class="badge bg-warning-subtle text-dark border border-warning px-2 py-1">{{ $dp->name }}</span>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                @if($effectivePermsList->isNotEmpty())
                                    <div class="mt-3">
                                        <button class="btn btn-sm btn-light border fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#effectivePermsCollapse">
                                            <i class="bi bi-key-fill text-success me-1"></i> عرض كافة الصلاحيات الفعلية النشطة في حساب هذا الموظف ({{ $effectivePermsList->count() }} صلاحية)
                                        </button>
                                        <div class="collapse mt-2" id="effectivePermsCollapse">
                                            <div class="card card-body bg-light border-0 p-3">
                                                <div class="d-flex flex-wrap gap-1">
                                                    @foreach($effectivePermsList as $ep)
                                                        <span class="badge bg-white text-dark border px-2 py-1">
                                                            <i class="bi bi-check2 text-success"></i> {{ $ep->name }}
                                                        </span>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
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
                            {{-- 1. صورة العقد --}}
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-body-tertiary text-center h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <label class="text-muted small d-block mb-2 fw-semibold">صورة العقد</label>
                                        @if($user->getFileUrl('contract_image'))
                                            <a href="{{ $user->getFileUrl('contract_image') }}" target="_blank" class="d-block mb-2 text-decoration-none">
                                                @if(Str::endsWith($user->contract_image, '.pdf'))
                                                    <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" style="height: 110px; width: 100%;">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                    </div>
                                                @else
                                                    <img src="{{ $user->getFileUrl('contract_image') }}" class="img-thumbnail rounded shadow-sm" style="height: 110px; width: 100%; object-fit: cover;" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div class="text-muted small py-3" style="display: none;"><i class="bi bi-exclamation-circle text-warning me-1"></i> تعذر معاينة الملف</div>
                                                @endif
                                            </a>
                                        @else
                                            <div class="py-4 text-muted small"><i class="bi bi-file-earmark-x text-secondary fs-4 d-block mb-1"></i> لا توجد صورة مرفقة</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 d-flex gap-1.5 justify-content-center">
                                        @if($user->getFileUrl('contract_image'))
                                            <a href="{{ $user->getFileUrl('contract_image') }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                                                <i class="bi bi-eye me-1"></i> عرض
                                            </a>
                                        @endif
                                        @if(!$pendingRequest && ($isOwnProfile || $canManageUsers))
                                            <label for="upload_contract_image" class="btn btn-sm {{ $user->getFileUrl('contract_image') ? 'btn-outline-secondary' : 'btn-primary' }} flex-fill mb-0 cursor-pointer text-nowrap" style="cursor: pointer;">
                                                <i class="bi bi-upload me-1"></i> {{ $user->getFileUrl('contract_image') ? 'استبدال' : 'إضافة المستند' }}
                                            </label>
                                            <form id="form_contract_image" action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="d-none">
                                                @csrf @method('PUT')
                                                <input type="file" id="upload_contract_image" name="contract_image" accept="image/*,application/pdf"
                                                    onchange="if(confirm('هل تريد رفع صورة العقد؟')) this.form.submit()">
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 2. الفيش الجنائي --}}
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-body-tertiary text-center h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <label class="text-muted small d-block mb-2 fw-semibold">الفيش الجنائي</label>
                                        @if($user->getFileUrl('criminal_record_image'))
                                            <a href="{{ $user->getFileUrl('criminal_record_image') }}" target="_blank" class="d-block mb-2 text-decoration-none">
                                                @if(Str::endsWith($user->criminal_record_image, '.pdf'))
                                                    <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" style="height: 110px; width: 100%;">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                    </div>
                                                @else
                                                    <img src="{{ $user->getFileUrl('criminal_record_image') }}" class="img-thumbnail rounded shadow-sm" style="height: 110px; width: 100%; object-fit: cover;" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div class="text-muted small py-3" style="display: none;"><i class="bi bi-exclamation-circle text-warning me-1"></i> تعذر معاينة الملف</div>
                                                @endif
                                            </a>
                                        @else
                                            <div class="py-4 text-muted small"><i class="bi bi-file-earmark-x text-secondary fs-4 d-block mb-1"></i> لا توجد صورة مرفقة</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 d-flex gap-1.5 justify-content-center">
                                        @if($user->getFileUrl('criminal_record_image'))
                                            <a href="{{ $user->getFileUrl('criminal_record_image') }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                                                <i class="bi bi-eye me-1"></i> عرض
                                            </a>
                                        @endif
                                        @if(!$pendingRequest && ($isOwnProfile || $canManageUsers))
                                            <label for="upload_criminal_record_image" class="btn btn-sm {{ $user->getFileUrl('criminal_record_image') ? 'btn-outline-secondary' : 'btn-primary' }} flex-fill mb-0 cursor-pointer text-nowrap" style="cursor: pointer;">
                                                <i class="bi bi-upload me-1"></i> {{ $user->getFileUrl('criminal_record_image') ? 'استبدال' : 'إضافة المستند' }}
                                            </label>
                                            <form id="form_criminal_record_image" action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="d-none">
                                                @csrf @method('PUT')
                                                <input type="file" id="upload_criminal_record_image" name="criminal_record_image" accept="image/*,application/pdf"
                                                    onchange="if(confirm('هل تريد رفع صورة الفيش الجنائي؟')) this.form.submit()">
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- 3. البطاقة الشخصية --}}
                            <div class="col-md-4">
                                <div class="p-3 border rounded bg-body-tertiary text-center h-100 d-flex flex-column justify-content-between">
                                    <div>
                                        <label class="text-muted small d-block mb-2 fw-semibold">البطاقة الشخصية</label>
                                        @if($user->getFileUrl('id_card_image'))
                                            <a href="{{ $user->getFileUrl('id_card_image') }}" target="_blank" class="d-block mb-2 text-decoration-none">
                                                @if(Str::endsWith($user->id_card_image, '.pdf'))
                                                    <div class="img-thumbnail bg-light d-flex align-items-center justify-content-center" style="height: 110px; width: 100%;">
                                                        <i class="bi bi-file-earmark-pdf text-danger fs-1"></i>
                                                    </div>
                                                @else
                                                    <img src="{{ $user->getFileUrl('id_card_image') }}" class="img-thumbnail rounded shadow-sm" style="height: 110px; width: 100%; object-fit: cover;" onerror="this.onerror=null; this.style.display='none'; this.nextElementSibling.style.display='block';">
                                                    <div class="text-muted small py-3" style="display: none;"><i class="bi bi-exclamation-circle text-warning me-1"></i> تعذر معاينة الملف</div>
                                                @endif
                                            </a>
                                        @else
                                            <div class="py-4 text-muted small"><i class="bi bi-file-earmark-x text-secondary fs-4 d-block mb-1"></i> لا توجد صورة مرفقة</div>
                                        @endif
                                    </div>
                                    <div class="mt-2 d-flex gap-1.5 justify-content-center">
                                        @if($user->getFileUrl('id_card_image'))
                                            <a href="{{ $user->getFileUrl('id_card_image') }}" target="_blank" class="btn btn-sm btn-outline-primary flex-fill">
                                                <i class="bi bi-eye me-1"></i> عرض
                                            </a>
                                        @endif
                                        @if(!$pendingRequest && ($isOwnProfile || $canManageUsers))
                                            <label for="upload_id_card_image" class="btn btn-sm {{ $user->getFileUrl('id_card_image') ? 'btn-outline-secondary' : 'btn-primary' }} flex-fill mb-0 cursor-pointer text-nowrap" style="cursor: pointer;">
                                                <i class="bi bi-upload me-1"></i> {{ $user->getFileUrl('id_card_image') ? 'استبدال' : 'إضافة المستند' }}
                                            </label>
                                            <form id="form_id_card_image" action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="d-none">
                                                @csrf @method('PUT')
                                                <input type="file" id="upload_id_card_image" name="id_card_image" accept="image/*,application/pdf"
                                                    onchange="if(confirm('هل تريد رفع صورة البطاقة الشخصية؟')) this.form.submit()">
                                            </form>
                                        @endif
                                    </div>
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

    {{-- New Evaluation Modal --}}
    <div class="modal fade" id="newEvaluationModal" tabindex="-1" aria-labelledby="newEvaluationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <form action="{{ route('employee-evaluations.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $user->id }}">
                    <div class="modal-header bg-primary text-white py-3">
                        <h5 class="modal-title fw-bold" id="newEvaluationModalLabel">
                            <i class="bi bi-award-fill me-2 text-warning"></i> تسجيل تقييم أداء للموظف: {{ $user->name }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        {{-- Live Score Banner --}}
                        <div class="p-3 mb-4 rounded-4 text-center border shadow-xs" style="background: linear-gradient(135deg, rgba(139, 92, 246, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%); border-color: rgba(139, 92, 246, 0.3) !important;">
                            <div class="row align-items-center">
                                <div class="col-4 border-end">
                                    <div class="small text-muted fw-bold">المجموع المحسوب</div>
                                    <div class="h3 fw-bolder text-primary mb-0 font-monospace" id="modalLiveTotal">100.0</div>
                                    <div class="x-small text-muted">من 100 درجة</div>
                                </div>
                                <div class="col-4 border-end">
                                    <div class="small text-muted fw-bold">النسبة المئوية</div>
                                    <div class="h4 fw-bold text-dark mb-0 font-monospace" id="modalLivePct">100%</div>
                                </div>
                                <div class="col-4">
                                    <div class="small text-muted fw-bold mb-1">التقدير المتوقع</div>
                                    <span id="modalLiveRating" class="badge bg-success fs-6 px-3 py-2 rounded-pill shadow-xs">متميز</span>
                                </div>
                            </div>
                            <div class="x-small text-muted mt-2 border-top pt-2">
                                <span class="badge bg-light text-dark border">الدرجة الكبرى لكل بند: 5.0</span>
                                <span class="badge bg-light text-dark border ms-1">الحد الأدنى: 0.5</span>
                                <span class="ms-2">90%+ متميز | 80%-89% جيد جداً | 65%-79% جيد | 50%-64% مقبول | أقل من 50% ضعيف</span>
                            </div>
                        </div>

                        {{-- Metadata --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">تاريخ التقييم <span class="text-danger">*</span></label>
                                <input type="date" name="evaluation_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small">فترة التقييم (مثال: نوفمبر 2026 أو 2026-11)</label>
                                <input type="text" name="evaluation_period" class="form-control" value="{{ date('Y-m') }}" placeholder="2026-11" required>
                            </div>
                        </div>

                        {{-- 10 Criteria Inputs --}}
                        <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                            <i class="bi bi-ui-checks me-1 text-primary"></i> درجات البنود العشرة (أدخل درجة من 0.5 إلى 5.0 لكل بند):
                        </h6>
                        <div class="row g-3 mb-4">
                            @foreach(\App\Models\EmployeeEvaluation::CRITERIA as $k => $c)
                                <div class="col-md-6">
                                    <div class="p-3 border rounded-3 bg-light-subtle h-100">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <label class="form-label fw-bold mb-0 text-dark small">{{ $c['label'] }}</label>
                                            <span class="badge bg-primary-subtle text-primary font-monospace">وزن {{ ($c['weight'] * 100) }}%</span>
                                        </div>
                                        <div class="x-small text-muted mb-2">{{ $c['desc'] }}</div>
                                        <div class="input-group">
                                            <input type="number" step="0.1" min="0.5" max="5.0" name="{{ $k }}"
                                                   class="form-control font-monospace fw-bold eval-criteria-input text-center"
                                                   data-weight="{{ $c['weight'] }}" value="5.0" required>
                                            <span class="input-group-text bg-white text-muted small">من 5.0</span>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        {{-- Qualitative Inputs --}}
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><i class="bi bi-hand-thumbs-up text-success me-1"></i> نقاط القوة والتميز (اختياري)</label>
                                <textarea name="strengths" rows="2" class="form-control" placeholder="أبرز الإنجازات والصفات الإيجابية للموظف..."></textarea>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold small"><i class="bi bi-graph-up-arrow text-warning me-1"></i> مجالات التحسين والتطوير (اختياري)</label>
                                <textarea name="improvements" rows="2" class="form-control" placeholder="النقاط التي تحتاج إلى تطوير ومتابعة..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold small"><i class="bi bi-chat-left-text text-secondary me-1"></i> ملاحظات الإدارة وتوصيات الموارد البشرية</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="أي توصيات عامة بخصوص الترقية أو المكافأة أو التدريب..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer bg-light py-3">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary px-4 fw-bold">
                            <i class="bi bi-check-lg me-1"></i> حفظ التقييم واعتماده
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function recalcModalEvaluation() {
                let weightedSum = 0;
                document.querySelectorAll('.eval-criteria-input').forEach(function (input) {
                    let val = parseFloat(input.value) || 0;
                    if (val < 0) val = 0;
                    if (val > 5.0) val = 5.0;
                    let weight = parseFloat(input.dataset.weight) || 0;
                    weightedSum += (val * weight);
                });

                let totalScore = Math.round((weightedSum * 20) * 100) / 100;
                let pct = totalScore;

                let rating = 'ضعيف';
                let badgeClass = 'badge bg-danger fs-6 px-3 py-2 rounded-pill shadow-xs';

                if (pct >= 90) {
                    rating = 'متميز';
                    badgeClass = 'badge bg-success fs-6 px-3 py-2 rounded-pill shadow-xs';
                } else if (pct >= 80) {
                    rating = 'جيد جداً';
                    badgeClass = 'badge bg-primary fs-6 px-3 py-2 rounded-pill shadow-xs';
                } else if (pct >= 65) {
                    rating = 'جيد';
                    badgeClass = 'badge bg-info text-dark fs-6 px-3 py-2 rounded-pill shadow-xs';
                } else if (pct >= 50) {
                    rating = 'مقبول';
                    badgeClass = 'badge bg-warning text-dark fs-6 px-3 py-2 rounded-pill shadow-xs';
                }

                const totalEl = document.getElementById('modalLiveTotal');
                const pctEl = document.getElementById('modalLivePct');
                const ratingEl = document.getElementById('modalLiveRating');

                if (totalEl) totalEl.textContent = totalScore.toFixed(1);
                if (pctEl) pctEl.textContent = pct.toFixed(1) + '%';
                if (ratingEl) {
                    ratingEl.textContent = rating;
                    ratingEl.className = badgeClass;
                }
            }

            document.querySelectorAll('.eval-criteria-input').forEach(function (input) {
                input.addEventListener('input', recalcModalEvaluation);
            });
            recalcModalEvaluation();
        });
    </script>
    @endpush
@endsection



