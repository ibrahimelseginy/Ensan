@extends('layouts.app')
@section('content')

{{-- Hero Section --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #6366f1 0%, #4f46e5 50%, #4338ca 100%);">
    <div class="hero-content">
        <div class="hero-greeting">سجل التغييرات 📝</div>
        <h1 class="hero-title">طلبات الموافقة والتغييرات</h1>
        <p class="hero-subtitle">سجل كامل لجميع طلبات التعديل والحذف والإلغاء وحالتها</p>
    </div>
    <i class="bi bi-ui-checks hero-icon d-none d-md-block"></i>
</div>

<div class="container-fluid animate-slide-up animate-delay-1">
    <div class="card border-0 shadow-sm mt-n4">
        <div class="card-header bg-white py-3 border-bottom-0">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-4">
                    <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i>السجل ({{ $requests->total() }})</h5>
                    @if(!empty($isAdmin))
                    <div class="d-none" id="bulk-actions">
                        <button class="btn btn-sm btn-outline-warning" onclick="submitBulk('revert')">
                            <i class="bi bi-arrow-counterclockwise"></i> تراجع
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="submitBulk('destroy')">
                            <i class="bi bi-trash"></i> حذف
                        </button>
                    </div>
                    @endif
                </div>

                <div class="btn-group bg-light p-1 rounded-3">
                    <a href="{{ route('change-requests.index', ['status' => 'all']) }}" 
                       class="btn btn-sm px-3 {{ $currentStatus === 'all' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">الكل</a>
                    <a href="{{ route('change-requests.index', ['status' => 'pending']) }}" 
                       class="btn btn-sm px-3 {{ $currentStatus === 'pending' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">معلق</a>
                    <a href="{{ route('change-requests.index', ['status' => 'approved']) }}" 
                       class="btn btn-sm px-3 {{ $currentStatus === 'approved' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">تمت الموافقة</a>
                    <a href="{{ route('change-requests.index', ['status' => 'rejected']) }}" 
                       class="btn btn-sm px-3 {{ $currentStatus === 'rejected' ? 'btn-primary shadow-sm' : 'btn-light border-0' }}">مرفوض</a>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0" style="width: 40px;">
                            @if(!empty($isAdmin))
                                <input type="checkbox" class="form-check-input" id="check-all" onchange="toggleAll(this)">
                            @else
                                <i class="bi bi-hash text-muted"></i>
                            @endif
                        </th>
                        <th class="border-0">#</th>
                        <th class="border-0">المستخدم</th>
                        <th class="border-0">نوع الطلب</th>
                        <th class="border-0">العنصر</th>
                        <th class="border-0">الحالة</th>
                        <th class="border-0">التفاصيل</th>
                        <th class="border-0">التاريخ</th>
                        <th class="border-0 text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($requests as $req)
                        @php
                            $modelName = class_basename($req->model_type);
                            $modelAr = match($modelName) {
                                'Donation' => 'تبرع',
                                'Delegate' => 'مندوب',
                                'TravelRoute' => 'خط سير',
                                'Beneficiary' => 'مستفيد',
                                'Account' => 'حساب مالي',
                                'Leave' => 'إجازة',
                                'Expense' => 'مصروف',
                                'JournalEntry' => 'قيد يومية',
                                'User' => 'مستخدم',
                                'FinancialClosure' => 'إغلاق مالي',
                                default => $modelName
                            };

                            $actionColors = [
                                'create' => 'success',
                                'update' => 'primary',
                                'delete' => 'danger',
                                'cancel' => 'warning'
                            ];
                            $actionAr = [
                                'create' => 'إضافة',
                                'update' => 'تعديل',
                                'delete' => 'حذف',
                                'cancel' => 'إلغاء'
                            ];
                            $color = $actionColors[$req->action] ?? 'secondary';
                            $actionText = $actionAr[$req->action] ?? $req->action;
                            
                            $statusInfo = match($req->status) {
                                'approved' => ['color' => 'success', 'text' => 'تمت الموافقة', 'icon' => 'check-circle'],
                                'rejected' => ['color' => 'danger', 'text' => 'مرفوض', 'icon' => 'x-circle'],
                                'pending' => ['color' => 'warning', 'text' => 'معلق', 'icon' => 'hourglass-split'],
                                default => ['color' => 'secondary', 'text' => $req->status, 'icon' => 'circle']
                            };
                        @endphp
                        <tr>
                            <td>
                                @if(!empty($isAdmin))
                                    <input type="checkbox" class="form-check-input bulk-item" value="{{ $req->id }}" onchange="updateBulkUI()">
                                @else
                                    <span class="text-muted small">{{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}</span>
                                @endif
                            </td>
                            <td>{{ ($requests->currentPage() - 1) * $requests->perPage() + $loop->iteration }}</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle small">
                                        {{ mb_substr($req->user->name ?? '?', 0, 1) }}
                                    </div>
                                    <div class="small fw-bold">{{ $req->user->name ?? 'غير معروف' }}</div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-{{ $color }}-subtle text-{{ $color }}">
                                    {{ $actionText }} {{ $modelAr }}
                                </span>
                            </td>
                            <td>
                                @if($req->action === 'create')
                                    <span class="text-muted small">عنصر جديد</span>
                                @else
                                    <span class="font-monospace small">ID: {{ $req->model_id }}</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex flex-column">
                                    <span class="badge bg-{{ $statusInfo['color'] }}-subtle text-{{ $statusInfo['color'] }} mb-1">
                                        <i class="bi bi-{{ $statusInfo['icon'] }} me-1"></i> {{ $statusInfo['text'] }}
                                    </span>
                                    @if($req->status !== 'pending' && $req->reviewer)
                                        <div class="small text-muted" style="font-size: 10px;">
                                            بواسطة: {{ $req->reviewer->name }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-light border" type="button" data-bs-toggle="collapse" data-bs-target="#details-{{ $req->id }}">
                                    <i class="bi bi-chevron-down"></i> عرض
                                </button>
                            </td>
                            <td>
                                @if($req->created_at)
                                    @php
                                        $cairoTime = $req->created_at->copy()->timezone('Africa/Cairo');
                                    @endphp
                                    <div class="small fw-semibold text-dark">{{ $cairoTime->format('Y-m-d') }}</div>
                                    <div class="small text-muted" style="font-size:0.78rem">
                                        <i class="bi bi-clock me-1"></i>{{ $cairoTime->format('h:i A') }}
                                    </div>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                @if($req->status === 'pending')
                                    @if(!empty($isAdmin))
                                        <div class="btn-group">
                                            <form action="{{ route('change-requests.approve', $req) }}" method="POST" onsubmit="return confirm('تأكيد الموافقة على الطلب وتنفيذه فوراً؟');" class="d-inline">
                                                @csrf
                                                <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> موافقة</button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-primary ms-1" onclick="openPayloadEditModal({{ $req->id }}, 'pending')" title="تعديل البيانات قبل القبول">
                                                <i class="bi bi-pencil-square"></i> تعديل
                                            </button>
                                            <button class="btn btn-sm btn-danger ms-1" onclick="openRejectModal('{{ $req->id }}')">
                                                <i class="bi bi-x-lg"></i> رفض
                                            </button>
                                        </div>
                                    @else
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="openPayloadEditModal({{ $req->id }}, 'pending')">
                                            <i class="bi bi-pencil"></i> تعديل البيانات
                                        </button>
                                        <span class="badge bg-warning-subtle text-dark border ms-1"><i class="bi bi-hourglass-split"></i> معلق</span>
                                    @endif
                                @elseif($req->status === 'rejected')
                                    <div class="d-flex gap-1 align-items-center justify-content-end flex-wrap">
                                        <button type="button" class="btn btn-sm btn-success fw-bold shadow-xs" onclick="openPayloadEditModal({{ $req->id }}, 'rejected')" title="تعديل البيانات وإعادة تقديم الطلب للموافقة">
                                            <i class="bi bi-pencil-square me-1"></i> تعديل وإعادة تقديم
                                        </button>
                                        @if($modelBasename === 'Beneficiary' && $isCreate)
                                            <a href="{{ route('beneficiaries.create', ['change_request_id' => $req->id]) }}" class="btn btn-sm btn-outline-success" title="تعديل في استمارة المستفيد الكاملة">
                                                <i class="bi bi-ui-checks"></i>
                                            </a>
                                        @endif
                                        @if(!empty($isAdmin))
                                            <form action="{{ route('change-requests.revert', $req) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من التراجع عن هذا القرار؟');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-warning" title="تراجع عن الرفض"><i class="bi bi-arrow-counterclockwise"></i></button>
                                            </form>
                                            <form action="{{ route('change-requests.destroy', $req) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل نهائياً؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                @else
                                    @if(!empty($isAdmin))
                                        <div class="d-flex gap-1 align-items-center justify-content-end">
                                            <form action="{{ route('change-requests.revert', $req) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من التراجع عن هذا القرار؟ سيتم محاولة عكس أي تعديلات برمجية تمت وإعادة الطلب لحالة التعليق.');">
                                                @csrf
                                                <button class="btn btn-sm btn-outline-warning" title="تراجع عن القرار">
                                                    <i class="bi bi-arrow-counterclockwise"></i>
                                                </button>
                                            </form>
                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick='openEditModal(@json($req))'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form action="{{ route('change-requests.destroy', $req) }}" method="POST" class="d-inline-block" onsubmit="return confirm('هل أنت متأكد من حذف هذا السجل نهائياً؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    @else
                                        <span class="badge bg-light text-muted border py-1 px-2 small"><i class="bi bi-eye"></i> للاطلاع</span>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="9" class="p-0 border-0">
                                <div class="collapse p-4 border-bottom shadow-sm animate-fade-in change-request-detail-collapse" id="details-{{ $req->id }}">
                                    @php
                                        $modelBasename = class_basename($req->model_type);
                                        $typeLabels = [
                                            'Beneficiary' => ['label' => 'مستفيد', 'icon' => 'people', 'color' => 'info'],
                                            'Donation' => ['label' => 'تبرع', 'icon' => 'currency-dollar', 'color' => 'success'],
                                            'Expense' => ['label' => 'مصروف', 'icon' => 'receipt', 'color' => 'danger'],
                                            'Delegate' => ['label' => 'مندوب', 'icon' => 'person-badge', 'color' => 'warning'],
                                            'DelegateTrip' => ['label' => 'مشوار مندوب', 'icon' => 'truck', 'color' => 'info'],
                                            'Campaign' => ['label' => 'حملة', 'icon' => 'megaphone', 'color' => 'primary'],
                                            'User' => ['label' => 'مستخدم', 'icon' => 'person-circle', 'color' => 'purple'],
                                            'Account' => ['label' => 'حساب مالي', 'icon' => 'bank', 'color' => 'secondary'],
                                            'Task' => ['label' => 'مهمة', 'icon' => 'list-task', 'color' => 'primary'],
                                            'Role' => ['label' => 'دور/صلاحية', 'icon' => 'shield-lock', 'color' => 'warning'],
                                            'TravelRoute' => ['label' => 'خط سير', 'icon' => 'map', 'color' => 'dark'],
                                            'VolunteerHour' => ['label' => 'ساعات تطوع', 'icon' => 'clock-history', 'color' => 'info'],
                                            'VolunteerAttendance' => ['label' => 'حضور متطوع', 'icon' => 'calendar-check', 'color' => 'success'],
                                            'EmployeeAttendance' => ['label' => 'حضور موظف', 'icon' => 'calendar-date', 'color' => 'success'],
                                            'Leave' => ['label' => 'إجازة', 'icon' => 'calendar-event', 'color' => 'purple'],
                                            'JournalEntry' => ['label' => 'قيد يومية', 'icon' => 'journal-text', 'color' => 'dark'],
                                            'Donor' => ['label' => 'متبرع', 'icon' => 'heart-fill', 'color' => 'danger'],
                                            'Project' => ['label' => 'مشروع', 'icon' => 'briefcase', 'color' => 'primary'],
                                            'FinancialClosure' => ['label' => 'إغلاق مالي', 'icon' => 'file-lock2-fill', 'color' => 'success'],
                                        ];
                                        $tInfo = $typeLabels[$modelBasename] ?? ['label' => $modelBasename, 'icon' => 'box', 'color' => 'secondary'];
                                        $isCreate = strtolower((string)$req->action) === 'create' || empty($req->model_id);
                                        $isDeleted = !$isCreate && !$req->subject;

                                        $pData = is_array($req->payload) ? ($req->payload['data'] ?? $req->payload) : [];
                                        $beneficiaryCleanNat = preg_replace('/[^0-9]/', '', (string)($req->subject->national_id ?? ($pData['national_id'] ?? '')));
                                        $beneficiaryName = trim((string)($req->subject->full_name ?? ($pData['full_name'] ?? ($pData['name'] ?? ''))));
                                        $beneficiaryPhone = trim((string)($req->subject->phone ?? ($pData['phone'] ?? '')));

                                        $vItem = null;
                                        if ($modelBasename === 'Beneficiary') {
                                            if (!empty($req->model_id) && isset($visitsByBeneficiary[$req->model_id])) {
                                                $vItem = $visitsByBeneficiary[$req->model_id];
                                            } elseif (!empty($beneficiaryCleanNat) && isset($visitsByNationalId[$beneficiaryCleanNat])) {
                                                $vItem = $visitsByNationalId[$beneficiaryCleanNat];
                                            } elseif (!empty($beneficiaryName) && isset($visitsByName[$beneficiaryName])) {
                                                $vItem = $visitsByName[$beneficiaryName];
                                            } elseif (!empty($beneficiaryPhone) && isset($visitsByPhone[$beneficiaryPhone])) {
                                                $vItem = $visitsByPhone[$beneficiaryPhone];
                                            }
                                        }
                                    @endphp

                                    <div class="mb-4 change-request-target-card p-3 rounded-4 border shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                            <div class="d-flex align-items-center gap-2 flex-wrap">
                                                <h6 class="fw-bold mb-0 small text-uppercase text-muted opacity-75 d-inline-flex align-items-center">
                                                    <i class="bi bi-bullseye me-1"></i> العنصر المستهدف:
                                                </h6>
                                                @if($isCreate)
                                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25" style="font-size: 10px;">
                                                        <i class="bi bi-plus-circle me-1"></i> طلب جديد (قيد الإنشاء)
                                                    </span>
                                                @elseif($isDeleted)
                                                    <span class="badge bg-danger bg-opacity-10 text-danger ms-1" style="font-size: 8px;">محذوف من النظام</span>
                                                @endif

                                                @if($modelBasename === 'Beneficiary')
                                                    <button type="button" 
                                                        class="btn btn-sm btn-info text-white rounded-pill px-3 py-1 shadow-sm d-inline-flex align-items-center gap-2 border-0"
                                                        style="background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%); font-size: 11px; font-weight: 600;"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#caseResearchModal_{{ $req->id }}">
                                                        <i class="bi bi-clipboard2-pulse-fill"></i>
                                                        <span>البحث الميداني المرتبط بالحالة</span>
                                                        @if($vItem)
                                                            <span class="badge bg-white text-primary rounded-pill px-2 py-0" style="font-size: 9px;">زيارة معتمدة #{{ $vItem->id }}</span>
                                                        @else
                                                            <span class="badge bg-warning text-dark rounded-pill px-2 py-0" style="font-size: 9px;">استمارة ودراسة الأسرة</span>
                                                        @endif
                                                    </button>
                                                @endif
                                            </div>

                                            @if(!$isDeleted && !$isCreate)
                                                @php
                                                    $modelUrl = '#';
                                                    try {
                                                        $modelUrl = match($modelBasename) {
                                                            'Donation' => route('donations.show', $req->model_id),
                                                            'Expense' => route('expenses.show', $req->model_id),
                                                            'Leave' => route('leaves.show', $req->model_id),
                                                            'JournalEntry' => route('journal-entries.show', $req->model_id),
                                                            'FinancialClosure' => route('closures.show', $req->model_id),
                                                            'Beneficiary' => route('beneficiaries.show', $req->model_id),
                                                            'Delegate' => route('delegates.show', $req->model_id),
                                                            'User' => route('users.show', $req->model_id),
                                                            'Account' => route('accounts.show', $req->model_id),
                                                            'Task' => route('tasks.show', $req->model_id),
                                                            'Project' => route('projects.show', $req->model_id),
                                                            'Campaign' => route('campaigns.show', $req->model_id),
                                                            'Donor' => route('donors.show', $req->model_id),
                                                            'Volunteer' => route('volunteers.show', $req->model_id),
                                                            'GuestHouse' => route('guest-houses.show', $req->model_id),
                                                            default => url(\Illuminate\Support\Str::plural(\Illuminate\Support\Str::kebab($modelBasename)) . '/' . $req->model_id)
                                                        };
                                                    } catch (\Exception $e) {
                                                        $modelUrl = url(\Illuminate\Support\Str::plural(\Illuminate\Support\Str::kebab($modelBasename)) . '/' . $req->model_id);
                                                    }
                                                @endphp
                                                <a href="{{ $modelUrl }}" class="btn btn-sm btn-primary py-1 px-3 shadow-sm rounded-pill" style="font-size: 11px;" target="_blank">
                                                    <i class="bi bi-box-arrow-up-right me-1"></i> عرض السجل الحالي بالكامل
                                                </a>
                                            @endif
                                        </div>

                                        <div class="d-flex align-items-center gap-3">
                                            <div class="rounded-3 p-2 d-flex align-items-center justify-content-center bg-{{ $tInfo['color'] }} bg-opacity-10 text-{{ $tInfo['color'] }}" style="width: 42px; height: 42px;">
                                                <i class="bi bi-{{ $tInfo['icon'] }} fs-4"></i>
                                            </div>
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center gap-2 mb-1">
                                                    <span class="badge bg-{{ $tInfo['color'] }} text-white shadow-sm" style="font-size: 10px;">{{ $tInfo['label'] }}</span>
                                                    <span class="text-muted small font-monospace">#{{ $req->model_id ?: 'جديد' }}</span>
                                                </div>
                                                
                                                @if(!$isDeleted && $req->subject)
                                                    <div class="fw-bold fs-6">
                                                        @if($modelBasename === 'Beneficiary')
                                                            {{ $req->subject->full_name }}
                                                        @elseif($modelBasename === 'Donation' || $modelBasename === 'Expense') {{ number_format($req->subject->amount) }} <small class="fw-normal">ج.م</small>
                                                        @elseif($modelBasename === 'Task') {{ $req->subject->title }}
                                                        @elseif($modelBasename === 'Leave') {{ $req->subject->user->name ?? 'موظف' }}
                                                        @elseif($modelBasename === 'FinancialClosure') إغلاق فرع {{ $req->subject->branch }} بتاريخ {{ $req->subject->date->format('Y-m-d') }}
                                                        @elseif(isset($req->subject->name)) {{ $req->subject->name }}
                                                        @elseif(isset($req->subject->title)) {{ $req->subject->title }}
                                                        @else ID: {{ $req->model_id }} @endif
                                                    </div>
                                                    <div class="text-muted small">
                                                        @if($modelBasename === 'Beneficiary') {{ $req->subject->national_id ?? $req->subject->phone ?? 'لا يوجد معرف' }}
                                                        @elseif($modelBasename === 'Donation') من: {{ $req->subject->donor->name ?? 'فاعل خير' }}
                                                        @elseif($modelBasename === 'Expense') {{ $req->subject->description }}
                                                        @elseif($modelBasename === 'DelegateTrip') {{ $req->subject->delegate->name ?? '—' }} ({{ $req->subject->date?->format('Y-m-d') }})
                                                        @elseif($modelBasename === 'User') {{ $req->subject->job_title ?? ($req->subject->is_employee ? 'موظف' : ($req->subject->is_volunteer ? 'متطوع' : 'مستخدم')) }}
                                                        @elseif($modelBasename === 'Account') {{ $req->subject->code ?? 'بدون كود' }}
                                                        @elseif($modelBasename === 'Task') المسند إليه: {{ $req->subject->assignedUser->name ?? 'غير محدد' }}
                                                        @elseif($modelBasename === 'Leave') النوع: {{ $req->subject->type }} • من {{ $req->subject->start_date->format('Y-m-d') }}
                                                        @elseif($modelBasename === 'JournalEntry') التاريخ: {{ $req->subject->date->format('Y-m-d') }} • النوع: {{ $req->subject->entry_type }}
                                                        @elseif($modelBasename === 'FinancialClosure') تم بواسطة: {{ $req->subject->closed_by }}
                                                        @endif
                                                    </div>
                                                @else
                                                    <div class="fw-bold fs-6 {{ $isDeleted ? 'text-muted font-italic' : 'text-dark' }}">
                                                        @php
                                                            $displayName = $pData['full_name'] ?? ($pData['name'] ?? ($pData['title'] ?? ($pData['delegate_name'] ?? ($pData['volunteer_name'] ?? ($pData['employee_name'] ?? ($pData['user_name'] ?? '—'))))));
                                                        @endphp
                                                        {{ $displayName ?: "ID: ".$req->model_id }}
                                                    </div>
                                                    <div class="text-muted small opacity-75">
                                                        @if($modelBasename === 'Beneficiary')
                                                            الرقم القومي: {{ $pData['national_id'] ?? '—' }} • الهاتف: {{ $pData['phone'] ?? '—' }}
                                                        @elseif($modelBasename === 'Donation' || $modelBasename === 'Expense') مبلغ: {{ number_format($pData['amount'] ?? 0) }} ج.م
                                                        @elseif($modelBasename === 'Task') @if(isset($pData['assigned_to'])) مسؤول: {{ $pData['assignee_name'] ?? $pData['assigned_to'] }} @endif
                                                        @elseif($modelBasename === 'User') {{ $pData['job_title'] ?? 'مستخدم' }}
                                                        @elseif($modelBasename === 'Account') كود: {{ $pData['code'] ?? '—' }}
                                                        @elseif($modelBasename === 'DelegateTrip') {{ $pData['delegate_name'] ?? '—' }} ({{ $pData['trip_date'] ?? '—' }})
                                                        @elseif($modelBasename === 'Leave') النوع: {{ $pData['type'] ?? '—' }} • التاريخ: {{ $pData['start_date'] ?? '—' }}
                                                        @elseif($modelBasename === 'JournalEntry') النوع: {{ $pData['entry_type'] ?? '—' }} • الوصف: {{ $pData['description'] ?? '—' }}
                                                        @elseif($modelBasename === 'FinancialClosure') فرع: {{ $pData['branch'] ?? '—' }} • تاريخ: {{ $pData['date'] ?? '—' }}
                                                        @endif
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <h6 class="fw-bold mb-3 small text-uppercase text-primary d-flex align-items-center gap-2">
                                        <i class="bi bi-info-circle"></i> تفاصيل التغيير:
                                    </h6>
                                    @if($req->action === 'cancel')
                                        <div class="alert alert-warning border-0 bg-warning bg-opacity-10 text-warning d-flex align-items-center gap-3 shadow-sm rounded-4 p-3 mb-4">
                                            <i class="bi bi-exclamation-triangle-fill fs-4"></i>
                                            <div>
                                                <div class="fw-bold fs-6" style="color: #ffc107;">سبب الإلغاء:</div>
                                                <div class="mt-1 text-white opacity-75">{{ $req->payload['reason'] ?? 'لا يوجد سبب محدد' }}</div>
                                            </div>
                                        </div>
                                    @else
                                        @php
                                            $payloadData = $req->payload;
                                            $diffData = null;
                                            
                                            if (is_array($req->payload) && (isset($req->payload['__is_wrapped']) || isset($req->payload['diff']))) {
                                                $payloadData = $req->payload['data'] ?? [];
                                                $diffData = $req->payload['diff'] ?? null;
                                            }

                                            $labels = [
                                                'name' => 'الاسم',
                                                'full_name' => 'الاسم بالكامل',
                                                'title' => 'العنوان',
                                                'amount' => 'المبلغ',
                                                'type' => 'النوع',
                                                'cash_channel' => 'طريقة الدفع',
                                                'payment_method' => 'طريقة الدفع',
                                                'description' => 'الوصف/الملاحظات',
                                                'notes' => 'ملاحظات إضافية',
                                                'receipt_number' => 'رقم الإيصال',
                                                'project_id' => 'المشروع',
                                                'campaign_id' => 'الحملة',
                                                'target_amount' => 'المبلغ المستهدف',
                                                'target_families' => 'عدد الأسر المستهدفة',
                                                'donor_id' => 'المتبرع',
                                                'delegate_id' => 'المندوب',
                                                'warehouse_id' => 'المخزن',
                                                'item_id' => 'الصنف',
                                                'quantity' => 'الكمية',
                                                'estimated_value' => 'القيمة التقديرية',
                                                'allocation_note' => 'توجيه التبرع',
                                                'allocation_type' => 'نوع تخصيص المستفيدين',
                                                'allocated_beneficiary_ids' => 'المستفيدون المختارون',
                                                'child_sponsorship_type' => 'نوع كفالة الطفل',
                                                'sponsor_ids' => 'الكفلاء المختارون',
                                                'notes_cases' => 'تشخيص الحالة والتقرير الطبي',
                                                'patient_code' => 'كود المريض',
                                                'patient_name' => 'اسم المريض بالكامل',
                                                'patient_age' => 'سن المريض',
                                                'patient_birth_date' => 'تاريخ ميلاد المريض',
                                                'patient_phone' => 'هاتف المريض',
                                                'patient_backup_phone' => 'هاتف إضافي للمريض',
                                                'patient_relationship' => 'صلة قرابة المريض',
                                                'status' => 'الحالة',
                                                'brothers_count' => 'عدد الإخوة',
                                                'children_count' => 'عدد الأبناء',
                                                'sponsored_children_count' => 'عدد الأبناء المكفولين',
                                                'guardian_name' => 'اسم ولي الأمر',
                                                'mother_name' => 'اسم الأم',
                                                'children_names' => 'أسماء الأبناء',
                                                'study_grade' => 'المرحلة الدراسية',
                                                'poultry_type' => 'نوع الدواجن',
                                                'meat' => 'اللحوم',
                                                'source_name' => 'اسم المصدر',
                                                'reference_number' => 'الرقم المرجعي',
                                                'deceased_husband_name' => 'اسم الزوج المتوفى',
                                                'deceased_husband_birth_date' => 'تاريخ ميلاد الزوج المتوفى',
                                                'deceased_husband_death_date' => 'تاريخ وفاة الزوج',
                                                'deceased_husband_age' => 'سن الزوج عند الوفاة',
                                                'transit_details' => 'تفاصيل الترانزيت',
                                                'death_details' => 'تفاصيل الوفاة',
                                                'income_aid_sources' => 'مصادر الدخل والمساعدات',
                                                'expenses_debts_details' => 'المصروفات والديون',
                                                'zad_details' => 'تفاصيل زاد',
                                                'midrar_details' => 'تفاصيل مدرار',
                                                'hospital_name' => 'اسم المستشفى',
                                                'tumor_location' => 'مكان الورم',
                                                'treatment_protocol' => 'بروتوكول العلاج',
                                                'tumor_details' => 'تفاصيل الورم',
                                                'collection_day' => 'يوم استلام الكفالة',
                                                'rejection_reason' => 'سبب الرفض',
                                                'archived_reason' => 'سبب الأرشفة',
                                                'age' => 'السن',
                                                'classification' => 'التصنيف',
                                                'adult_children_count' => 'عدد الأبناء البالغين',
                                                'adult_children_ages' => 'أعمار الأبناء البالغين',
                                                'monthly_sponsorship_amount' => 'مبلغ الكفالة الشهري',
                                                'sponsorship_scope_type' => 'نوع الكفالة',
                                                'visa_card_number' => 'رقم كارت الفيزا',
                                                'phone' => 'رقم الهاتف',
                                                'applicant_phone' => 'هاتف الحالة الأساسي',
                                                'backup_phone' => 'هاتف بديل',
                                                'national_id' => 'الرقم القومي',
                                                'birth_date' => 'تاريخ الميلاد',
                                                'gender' => 'النوع / الجنس',
                                                'marital_status' => 'الحالة الاجتماعية',
                                                'health_status' => 'الحالة الصحية',
                                                'education_level' => 'المستوى التعليمي',
                                                'housing_type' => 'نوع المسكن',
                                                'home_ownership' => 'ملكية السكن',
                                                'house_photo' => 'صورة المنزل',
                                                'condition_photos' => 'صور شروخ وتلفيات السكن',
                                                'collection_method' => 'طريقة استلام الكفالة',
                                                'income_source' => 'مصدر الدخل',
                                                'monthly_income' => 'الدخل الشهري',
                                                'assistance_type' => 'نوع المساعدة',
                                                'assistance_amount' => 'مبلغ المساعدة',
                                                'family_members_count' => 'عدد أفراد الأسرة',
                                                'social_status_description' => 'الوصف الاجتماعي للحالة',
                                                'need_type' => 'نوع الاحتياج',
                                                'guide_name' => 'اسم الدليل',
                                                'guide_phone' => 'رقم هاتف الدليل',
                                                'patient_id_front_path' => 'صورة بطاقة المريض (وجه)',
                                                'patient_id_back_path' => 'صورة بطاقة المريض (ظهر)',
                                                'companion_name' => 'اسم المرافق',
                                                'companion_relation' => 'صلة قرابة المرافق',
                                                'companion_national_id' => 'الرقم القومي للمرافق',
                                                'companion_phone' => 'هاتف المرافق',
                                                'companion_address' => 'عنوان المرافق',
                                                'companion_id_front_path' => 'صورة بطاقة المرافق (وجه)',
                                                'companion_id_back_path' => 'صورة بطاقة المرافق (ظهر)',
                                                'treatment_type' => 'نوع العلاج',
                                                'medical_center' => 'المركز الطبي / المستشفى',
                                                'sessions_count' => 'عدد الجلسات',
                                                'medical_notes' => 'الملاحظات الطبية',
                                                'manager_notes' => 'ملاحظات وسلبيات مدير الدار',
                                                'departure_notes' => 'ملاحظات المغادرة',
                                                'arrival_date' => 'تاريخ الوصول',
                                                'expected_days' => 'مدة الإقامة المتوقعة',
                                                'guest_house_bed_id' => 'رقم السرير / الجناح',
                                                'email' => 'البريد الإلكتروني',
                                                'address' => 'العنوان السكني',
                                                'category' => 'التصنيف',
                                                'treasury_id' => 'الخزنة المتأثرة',
                                                'received_at' => 'تاريخ الاستلام',
                                                'date' => 'التاريخ',
                                                'salary' => 'الراتب الأساسي',
                                                'allowances' => 'البدلات والمكافآت',
                                                'active' => 'حالة النشاط',
                                                'password' => 'كلمة المرور',
                                                'role_id' => 'الدور/الوظيفة',
                                                'is_active' => 'الحالة',
                                                'is_employee' => 'موظف',
                                                'is_volunteer' => 'متطوع',
                                                'department' => 'القسم',
                                                'job_title' => 'المسمى الوظيفي',
                                                'join_date' => 'تاريخ الانضمام',
                                                'college' => 'الكلية/الجامعة',
                                                'governorate' => 'المحافظة',
                                                'city' => 'المدينة/المركز',
                                                'project_role' => 'الدور في المشروع',
                                                'guest_house_id' => 'دار الضيافة',
                                                'contract_start_date' => 'بداية العقد',
                                                'contract_end_date' => 'نهاية العقد',
                                                'volunteer_hours' => 'ساعات التطوع المستهدفة',
                                                'code' => 'كود الحساب',
                                                'type' => 'نوع الحساب',
                                                'parent_id' => 'الحساب الأب (Parent)',
                                                'check_in_at' => 'وقت الحضور',
                                                'check_out_at' => 'وقت الانصراف',
                                                'evaluation_notes' => 'ملاحظات التقييم',
                                                'rating' => 'التقييم',
                                                'start_date' => 'تاريخ البدء',
                                                'end_date' => 'تاريخ الانتهاء',
                                                'reason' => 'السبب/التبرير',
                                                'user_id' => 'المستخدم المعني',
                                                'entry_type' => 'نوع القيد',
                                                'branch' => 'الفرع',
                                                'gate' => 'البوابة',
                                                'locked' => 'حالة الإقفال',
                                                'lines' => 'سطور القيد/العمليات',
                                            ];

                                            $valueMaps = [
                                                'cash' => 'نقدي',
                                                'in_kind' => 'عيني',
                                                'instapay' => 'انستا باي',
                                                'vodafone_cash' => 'فودافون كاش',
                                                'bank_transfer' => 'تحويل بنكي', 
                                                'operational' => 'تشغيلي',
                                                'aid' => 'مساعدات',
                                                'active' => 'نشط ✅',
                                                'inactive' => 'غير نشط ❌',
                                                'approved' => 'مقبول ✅',
                                                'accepted' => 'مقبول ✅',
                                                'rejected' => 'مرفوض ❌',
                                                'monthly' => 'شهري',
                                                'kafala' => 'كفالة',
                                                'patient' => 'المستفيد نفسه',
                                                'child' => 'ابن / ابنة',
                                                'husband' => 'الزوج',
                                                'wife' => 'الزوجة',
                                                'other' => 'فرد آخر',
                                                'pending' => 'تحت المراجعة ⏳',
                                                'under_research' => 'قيد البحث الميداني 🔍',
                                                'completed' => 'مكتمل ✅',
                                                'canceled' => 'ملغي ❌',
                                                'resident' => 'مقيم حالياً بالدار',
                                                'departed' => 'غادر الدار',
                                                'male' => 'ذكر',
                                                'female' => 'أنثى',
                                                'single' => 'أعزب',
                                                'married' => 'متزوج',
                                                'widowed' => 'أرمل / أرملة',
                                                'divorced' => 'مطلق / مطلقة',
                                                'visa' => 'فيزا',
                                                'doorstep' => 'باب البيت',
                                                'office' => 'مقر المؤسسة',
                                                'in_kind_shipping' => 'شحن عيني',
                                                'company' => 'شركة',
                                                'family_house' => 'بيت عيلة',
                                                'owned' => 'ملك',
                                                'rent' => 'إيجار',
                                                'service' => 'خدمي / عيني',
                                                'financial' => 'مالي',
                                                'water' => 'وصلة مياه',
                                                'roof' => 'سقف منزل',
                                                'asset' => 'أصول (Assets)',
                                                'liability' => 'خصوم (Liabilities)',
                                                'equity' => 'حقوق ملكية (Equity)',
                                                'revenue' => 'إيرادات (Revenue)',
                                                'expense' => 'مصروفات (Expense)',
                                            ];

                                            $fmt = function($k, $v) use ($valueMaps, $permissionsMap, $beneficiariesMap, $sponsorsMap) {
                                                if (is_null($v) || $v === '') return '<span class="text-muted italic small">فارغ</span>';

                                                if (is_array($v)) {
                                                    if ($k === 'permissions') {
                                                        $html = '<div class="d-flex flex-wrap gap-1 mt-1">';
                                                        foreach ($v as $pId) {
                                                            $pName = $permissionsMap[$pId] ?? ($permissionsMap[(int)$pId] ?? "#$pId");
                                                            $html .= '<span class="badge rounded-pill bg-light text-dark border p-1 px-2" style="font-size: 10px;">' . e($pName) . '</span>';
                                                        }
                                                        $html .= '</div>';
                                                        return $html;
                                                    }
                                                    if (in_array($k, ['allocated_beneficiary_ids', 'sponsor_ids'], true)) {
                                                        $namesMap = $k === 'allocated_beneficiary_ids' ? $beneficiariesMap : $sponsorsMap;
                                                        $html = '<div class="d-flex flex-wrap gap-1 mt-1">';
                                                        foreach ($v as $entityId) {
                                                            $entityName = $namesMap[$entityId] ?? ($namesMap[(int) $entityId] ?? "#$entityId");
                                                            $html .= '<span class="badge rounded-pill bg-light text-dark border p-1 px-2" style="font-size: 10px;">' . e($entityName) . '</span>';
                                                        }
                                                        $html .= '</div>';
                                                        return $html;
                                                    }
                                                    if (in_array($k, ['condition_photos'])) {
                                                        $html = '<div class="d-flex flex-wrap gap-1 mt-1">';
                                                        foreach ($v as $img) {
                                                            $html .= '<span class="badge bg-secondary-subtle text-secondary border p-1 px-2"><i class="bi bi-image me-1"></i> صورة مرفقة</span>';
                                                        }
                                                        $html .= '</div>';
                                                        return $html;
                                                    }
                                                    return '<pre class="small mb-0 mt-1 bg-white p-2 rounded border" style="font-size: 10px;">' . json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</pre>';
                                                }
                                                
                                                // Handle Passwords & Hashes
                                                if ($k === 'password' || (is_string($v) && strlen($v) > 50 && str_starts_with($v, '$2y$'))) {
                                                    return '<span class="text-muted small"><i class="bi bi-shield-lock-fill"></i> (بيانات مشفرة)</span>';
                                                }

                                                if (isset($valueMaps[$v])) return $valueMaps[$v];
                                                if (str_contains($k, 'id') && is_numeric($v)) return '#' . $v;
                                                if (in_array($k, ['amount','target_amount','estimated_value','cost','salary','monthly_income','assistance_amount'])) return '<strong>' . number_format((float)$v, 2) . '</strong> <small>ج.م</small>';
                                                if (in_array($k, ['quantity', 'target_families', 'family_members_count', 'sessions_count', 'expected_days'])) return '<strong>' . number_format((float)$v, 0) . '</strong>';
                                                
                                                if (is_bool($v)) return $v ? '<span class="badge bg-success-subtle text-success">نعم</span>' : '<span class="badge bg-danger-subtle text-danger">لا</span>';
                                                
                                                if (in_array($k, ['received_at', 'date', 'created_at', 'updated_at', 'arrival_date', 'birth_date']) && $v) {
                                                    try { return \Illuminate\Support\Carbon::parse($v)->format('Y-m-d'); } catch(\Exception $e) { return $v; }
                                                }
                                                
                                                return e($v);
                                            };
                                        @endphp

                                        <div class="row g-2">
                                            @if($diffData && $req->action === 'update')
                                                @foreach($diffData as $key => $dVals)
                                                    @continue(in_array($key, ['password', 'family_members', 'family_members_data']))
                                                    <div class="col-12 col-md-6 col-lg-4">
                                                        <div class="p-3 rounded-4 border-start border-primary border-4 shadow-sm h-100" style="background: rgba(255,255,255,0.03);">
                                                            <div class="small fw-bold text-primary mb-2">{{ $labels[$key] ?? $key }}</div>
                                                            <div class="d-flex align-items-center justify-content-between gap-2 border border-secondary border-opacity-25 bg-black bg-opacity-25 p-2 rounded-3">
                                                                <div class="text-secondary text-decoration-line-through small flex-fill text-center">{!! $fmt($key, $dVals['from']) !!}</div>
                                                                <div class="px-2"><i class="bi bi-arrow-left text-primary"></i></div>
                                                                <div class="fw-bold text-success flex-fill text-center">{!! $fmt($key, $dVals['to']) !!}</div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @else
                                                @foreach($payloadData as $key => $val)
                                                    @continue(in_array($key, ['_token', '_method', 'password', 'password_confirmation', 'created_by', 'updated_by', 'id', 'family_members', 'family_members_data']))
                                                    @continue(is_null($val) || $val === '')
                                                    @continue(is_array($val) && empty($val))
                                                    
                                                    <div class="col-6 col-md-4 col-lg-3">
                                                        <div class="p-3 rounded-4 border border-secondary border-opacity-10 shadow-sm h-100" style="background: rgba(255,255,255,0.03);">
                                                            <div class="small text-muted mb-1">{{ $labels[$key] ?? $key }}</div>
                                                            <div class="fw-bold text-dark">{!! $fmt($key, $val) !!}</div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                    @if($req->status === 'approved' && $req->reviewer)
                                         <div class="mt-2 pt-2 border-top small text-success">
                                            <i class="bi bi-check-all"></i> تمت الموافقة بواسطة {{ $req->reviewer->name }}
                                         </div>
                                    @elseif($req->status === 'rejected' && $req->reviewer)
                                         <div class="mt-2 pt-2 border-top small text-danger">
                                            <i class="bi bi-x-circle"></i> تم الرفض بواسطة {{ $req->reviewer->name }}
                                            @if($req->rejection_reason)
                                                <div class="text-muted mt-1">السبب: {{ $req->rejection_reason }}</div>
                                            @endif
                                         </div>
                                    @endif

                                    {{-- Quick Action Bar inside Details Card --}}
                                    @if($req->status === 'pending')
                                        <div class="d-flex align-items-center justify-content-between flex-wrap gap-2 p-3 rounded-4 bg-light border mt-3">
                                            <div class="small text-muted">
                                                <i class="bi bi-info-circle-fill text-primary me-1"></i>
                                                يمكنك فحص وتعديل أي من الحقول أعلاه قبل اعتمادها، أو الموافقة فوراً بالبيانات الحالية.
                                            </div>
                                            <div class="d-flex align-items-center gap-2">
                                                <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-semibold shadow-xs" onclick="openPayloadEditModal({{ $req->id }}, 'pending')">
                                                    <i class="bi bi-pencil-square me-1"></i> تعديل البيانات قبل القبول
                                                </button>
                                                @if(!empty($isAdmin))
                                                    <form action="{{ route('change-requests.approve', $req) }}" method="POST" class="d-inline" onsubmit="return confirm('تأكيد الموافقة على الطلب وتنفيذه فوراً؟');">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success px-3 rounded-pill fw-bold shadow-xs">
                                                            <i class="bi bi-check-circle-fill me-1"></i> موافقة وتنفيذ فوري
                                                        </button>
                                                    </form>
                                                    <button type="button" class="btn btn-sm btn-outline-danger px-3 rounded-pill shadow-xs" onclick="openRejectModal('{{ $req->id }}')">
                                                        <i class="bi bi-x-circle me-1"></i> رفض الطلب
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    @elseif($req->status === 'rejected')
                                        <div class="p-3 rounded-4 bg-danger bg-opacity-10 border border-danger border-opacity-25 mt-3">
                                            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                                                <div>
                                                    <div class="fw-bold text-danger d-flex align-items-center gap-1.5 mb-1">
                                                        <i class="bi bi-exclamation-octagon-fill fs-5"></i>
                                                        <span>سبب رفض الطلب:</span>
                                                        <span class="badge bg-danger text-white ms-1">{{ $req->rejection_reason ?: 'لم يتم توضيح سبب محدد' }}</span>
                                                    </div>
                                                    <div class="text-muted small">
                                                        لا داعي لإعادة إدخال أي بيانات من الصفر! اضغط على <strong>"تعديل وإعادة تقديم"</strong> لتصحيح الملاحظات وتحديث الطلب مباشرة.
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center gap-2 flex-wrap mt-1">
                                                    <button type="button" class="btn btn-sm btn-success rounded-pill px-3 py-1.5 fw-bold shadow-xs" onclick="openPayloadEditModal({{ $req->id }}, 'rejected')">
                                                        <i class="bi bi-pencil-square me-1"></i> تعديل سريع وإعادة إرسال
                                                    </button>
                                                    @if($modelBasename === 'Beneficiary' && $isCreate)
                                                        <a href="{{ route('beneficiaries.create', ['change_request_id' => $req->id]) }}" class="btn btn-sm btn-outline-dark rounded-pill px-3 py-1.5 fw-semibold shadow-xs">
                                                            <i class="bi bi-ui-checks me-1"></i> تعديل في استمارة المستفيد الكاملة
                                                        </a>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-3 opacity-50"></i>
                                <h5>السجل فارغ</h5>
                                <p class="mb-0">لا توجد أي طلبات أو تغييرات مسجلة حتى الآن.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($requests->hasPages())
        <div class="card-footer bg-white py-3">
            {{ $requests->links() }}
        </div>
        @endif
    </div>
</div>

{{-- Case Research & Field Visits Modals for Beneficiaries --}}
@foreach($requests as $req)
    @php
        $modelBasename = class_basename($req->model_type);
    @endphp
    @if($modelBasename === 'Beneficiary')
        @php
            $pData = is_array($req->payload) ? ($req->payload['data'] ?? $req->payload) : [];
            $bCleanNat = preg_replace('/[^0-9]/', '', (string)($req->subject->national_id ?? ($pData['national_id'] ?? '')));
            $bName = trim((string)($req->subject->full_name ?? ($pData['full_name'] ?? ($pData['name'] ?? ''))));
            $bPhone = trim((string)($req->subject->phone ?? ($pData['phone'] ?? '')));

            $modalVItem = null;
            if (!empty($req->model_id) && isset($visitsByBeneficiary[$req->model_id])) {
                $modalVItem = $visitsByBeneficiary[$req->model_id];
            } elseif (!empty($bCleanNat) && isset($visitsByNationalId[$bCleanNat])) {
                $modalVItem = $visitsByNationalId[$bCleanNat];
            } elseif (!empty($bName) && isset($visitsByName[$bName])) {
                $modalVItem = $visitsByName[$bName];
            } elseif (!empty($bPhone) && isset($visitsByPhone[$bPhone])) {
                $modalVItem = $visitsByPhone[$bPhone];
            }

            $caseDisplayName = $bName ?: 'حالة مستفيد';
        @endphp

        <div class="modal fade" id="caseResearchModal_{{ $req->id }}" tabindex="-1" aria-labelledby="caseResearchModalLabel_{{ $req->id }}" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable" style="max-width: 90vw;">
                <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                    <div class="modal-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <span class="p-2 rounded-3 bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-clipboard2-pulse-fill fs-5"></i>
                            </span>
                            <div>
                                <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="caseResearchModalLabel_{{ $req->id }}">
                                    البحث الميداني ودراسة الحالة: <span class="text-primary">{{ $caseDisplayName }}</span>
                                </h5>
                                <div class="text-muted x-small">
                                    طلب تغيير رقم #{{ $req->id }} • {{ $bCleanNat ? 'الرقم القومي: ' . $bCleanNat : '' }}
                                </div>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            @if($modalVItem)
                                <a href="{{ route('visits.show', $modalVItem->id) }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 11px;">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> فتح استمارة الزيارة
                                </a>
                            @endif
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
                        </div>
                    </div>

                    @if($modalVItem)
                        <div class="bg-white border-bottom px-4 pt-2">
                            <ul class="nav nav-tabs border-0" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active fw-bold small text-dark border-0 border-bottom border-primary border-3 py-2 px-3" 
                                            id="visitTabBtn_{{ $req->id }}" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#visitTabPane_{{ $req->id }}" 
                                            type="button" 
                                            role="tab">
                                        <i class="bi bi-file-earmark-medical me-1 text-primary"></i> استمارة الزيارة الميدانية المعتمدة #{{ $modalVItem->id }}
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link fw-bold small text-muted border-0 py-2 px-3" 
                                            id="dossierTabBtn_{{ $req->id }}" 
                                            data-bs-toggle="tab" 
                                            data-bs-target="#dossierTabPane_{{ $req->id }}" 
                                            type="button" 
                                            role="tab">
                                        <i class="bi bi-person-vcard me-1 text-info"></i> بيانات طلب الحالة والأسرة
                                    </button>
                                </li>
                            </ul>
                        </div>
                    @endif

                    <div class="modal-body p-3 p-md-4" style="background-color: #f8fafc; max-height: calc(85vh - 120px); overflow-y: auto;">
                        @if($modalVItem)
                            <div class="tab-content">
                                <div class="tab-pane fade show active" id="visitTabPane_{{ $req->id }}" role="tabpanel">
                                    <div class="card border rounded-3 overflow-hidden shadow-xs">
                                        <div class="card-header bg-white py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
                                            <span class="small text-muted">
                                                <i class="bi bi-info-circle me-1"></i> استمارة البحث الميداني للباحث: <strong>{{ $modalVItem->researcher_name ?? '—' }}</strong> (تاريخ الزيارة: {{ $modalVItem->visit_date ? \Carbon\Carbon::parse($modalVItem->visit_date)->format('Y-m-d') : '—' }})
                                            </span>
                                            <span class="badge bg-success bg-opacity-10 text-success">زيارة معتمدة</span>
                                        </div>
                                        <div class="card-body p-0">
                                            <iframe src="{{ route('visits.show', $modalVItem->id) }}" style="width: 100%; height: 70vh; border: none;" loading="lazy"></iframe>
                                        </div>
                                    </div>
                                </div>
                                <div class="tab-pane fade" id="dossierTabPane_{{ $req->id }}" role="tabpanel">
                                    @include('change_requests.partials.case_dossier_content', [
                                        'req' => $req,
                                        'displayName' => $caseDisplayName,
                                        'cleanNat' => $bCleanNat,
                                        'rawPhone' => $bPhone,
                                        'projectsMap' => $projectsMap ?? []
                                    ])
                                </div>
                            </div>
                        @else
                            <div class="alert alert-info py-2 px-3 mb-3 d-flex align-items-center gap-2 small">
                                <i class="bi bi-info-circle-fill fs-5 text-info"></i>
                                <div>
                                    <strong>ملف البحث الميداني ودراسة الحالة:</strong>
                                    يمكنك الاطلاع والتمرير (Scroll) لمراجعة كافة بيانات الحالة وتفاصيل أفراد الأسرة المقيدين بالطلب ومبالغ الكفالة دون مغادرة الصفحة.
                                </div>
                            </div>

                            @include('change_requests.partials.case_dossier_content', [
                                'req' => $req,
                                'displayName' => $caseDisplayName,
                                'cleanNat' => $bCleanNat,
                                'rawPhone' => $bPhone,
                                'projectsMap' => $projectsMap ?? []
                            ])
                        @endif
                    </div>

                    <div class="modal-footer bg-light py-2 px-3 border-top d-flex justify-content-between align-items-center">
                        <span class="text-muted x-small">
                            يمكنك اتخاذ قرار الموافقة أو الرفض مباشرة من الجدول في الصفحة.
                        </span>
                        <button type="button" class="btn btn-secondary btn-sm px-4 rounded-pill" data-bs-dismiss="modal">إغلاق</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endforeach

{{-- Reject Modal --}}
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="rejectForm" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title text-danger">رفض الطلب</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رفض هذا الطلب؟ لن يتم تنفيذ التغييرات.</p>
                <div class="mb-3">
                    <label class="form-label">سبب الرفض (اختياري)</label>
                    <textarea name="rejection_reason" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                <button class="btn btn-danger">تأكيد الرفض</button>
            </div>
        </form>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="editForm" method="POST" class="modal-content">
            @csrf
            @method('PUT')
            <div class="modal-header">
                <h5 class="modal-title">تعديل السجل</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select" id="editStatus">
                        <option value="pending">معلق</option>
                        <option value="approved">تمت الموافقة</option>
                        <option value="rejected">مرفوض</option>
                        <option value="cancelled">ملغي</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">سبب الرفض</label>
                    <textarea name="rejection_reason" class="form-control" rows="3" id="editReason"></textarea>
                </div>
                <div class="alert alert-warning small">
                    <i class="bi bi-exclamation-triangle"></i> تنبيه: تغيير الحالة يدوياً قد يؤثر على تناسق البيانات. يفضل استخدام الأزرار القياسية للموافقة والرفض.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button class="btn btn-primary">حفظ التغييرات</button>
            </div>
        </form>
    </div>
</div>

{{-- Payload Edit Modal (For Admin editing before approval AND Employee resubmitting rejected requests) --}}
<div class="modal fade" id="payloadEditModal" tabindex="-1" aria-labelledby="payloadEditModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <form id="payloadEditForm" method="POST" class="modal-content shadow-lg border-0 rounded-4">
            @csrf
            <div class="modal-header border-bottom py-3 px-4 bg-white" id="payloadEditModalHeader">
                <div class="d-flex align-items-center gap-2.5">
                    <span class="avatar avatar-sm bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center" style="width:38px;height:38px;">
                        <i class="bi bi-pencil-square fs-5"></i>
                    </span>
                    <div>
                        <h5 class="modal-title fw-bold text-dark fs-6 mb-0" id="payloadEditModalTitle">
                            تعديل بيانات الطلب
                        </h5>
                        <div class="text-muted small" id="payloadEditModalSubtitle"></div>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-4 bg-light bg-opacity-40">
                {{-- Dynamic Alert Notice --}}
                <div id="payloadEditNotice" class="alert mb-3 rounded-3 shadow-2xs border"></div>

                {{-- Fields Container --}}
                <div id="payloadEditFieldsContainer" class="row g-3">
                    {{-- Dynamically injected inputs --}}
                </div>

                {{-- Notes / Resubmit reason --}}
                <div class="mt-3 pt-3 border-top" id="payloadEditNotesSection">
                    <label class="form-label fw-bold small text-muted">ملاحظات التعديل / توضيح للمراجعة (اختياري)</label>
                    <textarea name="resubmit_notes" id="payloadEditNotes" class="form-control" rows="2" placeholder="اكتب أي ملاحظة توضيحية حول البيانات التي تم تعديلها..."></textarea>
                </div>
            </div>
            <div class="modal-footer border-top py-2.5 px-4 bg-white d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div id="payloadEditSecondaryActions"></div>
                <div class="d-flex align-items-center gap-2 ms-auto" id="payloadEditPrimaryActions">
                    {{-- Dynamically injected action buttons (Save, Save & Approve, Resubmit) --}}
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    const arabicFieldLabels = {
        'name': 'الاسم',
        'full_name': 'الاسم بالكامل',
        'national_id': 'الرقم القومي',
        'phone': 'رقم الهاتف الأساسي',
        'backup_phone': 'رقم هاتف إضافي',
        'birth_date': 'تاريخ الميلاد',
        'age': 'السن',
        'address': 'العنوان بالتفصيل',
        'governorate': 'المحافظة',
        'center': 'المركز / الحي',
        'village': 'القرية',
        'street': 'الشارع',
        'marital_status': 'الحالة الاجتماعية',
        'health_status': 'الحالة الصحية',
        'housing_type': 'نوع السكن',
        'home_ownership': 'ملكية السكن',
        'classification': 'تصنيف الحالة',
        'status': 'حالة الملف',
        'assistance_type': 'نوع المساعدة',
        'assistance_amount': 'مبلغ المساعدة',
        'collection_method': 'طريقة الاستلام',
        'collection_day': 'يوم الاستلام',
        'income_source': 'مصدر الدخل',
        'monthly_income': 'الدخل الشهري',
        'family_members_count': 'عدد أفراد الأسرة',
        'social_status_description': 'الوصف الاجتماعي',
        'notes': 'ملاحظات عامة',
        'project_id': 'المشروع',
        'campaign_id': 'الحملة',
        'amount': 'المبلغ',
        'description': 'الوصف',
        'patient_name': 'اسم المريض',
        'patient_code': 'كود المريض',
        'patient_age': 'سن المريض',
        'patient_birth_date': 'تاريخ ميلاد المريض',
        'patient_phone': 'هاتف المريض',
        'patient_backup_phone': 'هاتف إضافي للمريض',
        'patient_relationship': 'صلة قرابة المريض',
        'notes_cases': 'تقرير الحالة والتشخيص الطبي',
        'donor_id': 'المتبرع',
        'receipt_number': 'رقم الإيصال'
    };

    const requestsLookup = @json($requestsLookup ?? []);
    const projectsList = @json($projectsMap ?? []);
    const isUserAdmin = {{ !empty($isAdmin) ? 'true' : 'false' }};

    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function openPayloadEditModal(reqId, mode) {
        const req = requestsLookup[reqId];
        if (!req) return;

        const form = document.getElementById('payloadEditForm');
        form.action = req.update_url;

        document.getElementById('payloadEditModalTitle').innerText = `تعديل بيانات طلب ${req.action === 'create' ? 'إضافة' : (req.action === 'update' ? 'تعديل' : req.action)} ${req.model_basename} #${req.id}`;
        document.getElementById('payloadEditModalSubtitle').innerText = `مقدم الطلب: ${req.user_name} • الحالة: ${req.status === 'pending' ? 'قيد المراجعة' : (req.status === 'rejected' ? 'مرفوض' : req.status)}`;

        // Set Alert Notice
        const noticeEl = document.getElementById('payloadEditNotice');
        if (mode === 'rejected') {
            noticeEl.className = 'alert alert-danger mb-3 rounded-3 shadow-2xs border border-danger border-opacity-30';
            noticeEl.innerHTML = `
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-exclamation-octagon-fill fs-5 mt-0.5 text-danger flex-shrink-0"></i>
                    <div>
                        <div class="fw-bold text-danger">سبب الرفض السابق: ${escapeHtml(req.rejection_reason || 'غير محدد')}</div>
                        <div class="text-muted small mt-1">تم استرجاع كافة بيانات الطلب المحفوظة أدناه لتتمكن من تصحيحها وإعادة تقديمها للموافقة بنقرة واحدة، دون الحاجة لإعادة كتابة أي شيء من البداية.</div>
                    </div>
                </div>
            `;
        } else {
            noticeEl.className = 'alert alert-primary mb-3 rounded-3 shadow-2xs border border-primary border-opacity-30';
            noticeEl.innerHTML = `
                <div class="d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle-fill fs-5 mt-0.5 text-primary flex-shrink-0"></i>
                    <div>
                        <div class="fw-bold text-primary">تعديل بيانات الطلب قبل الموافقة</div>
                        <div class="text-muted small mt-1">يمكنك تصحيح أو إضافة أي بيانات مطلوبة. اضغط <strong>"حفظ وموافقة فورية"</strong> لاعتمادها وتنفيذها في قاعدة البيانات مباشرة، أو <strong>"حفظ التعديلات"</strong> للإبقاء عليها قيد المراجعة.</div>
                    </div>
                </div>
            `;
        }

        // Build fields
        const container = document.getElementById('payloadEditFieldsContainer');
        container.innerHTML = '';

        const payload = req.payload || {};
        const keys = Object.keys(payload).filter(k => !['_token', '_method', 'password', 'password_confirmation', 'created_by', 'updated_by', 'id', 'family_members_data'].includes(k));

        if (keys.length === 0) {
            container.innerHTML = '<div class="col-12 text-center text-muted py-3">لا توجد حقول إضافية قابلة للتعديل في هذا الطلب.</div>';
        } else {
            keys.forEach(k => {
                const val = payload[k];
                const label = arabicFieldLabels[k] || k;

                if (k === 'family_members') {
                    if (Array.isArray(val) && val.length > 0) {
                        const famHtml = `
                            <div class="col-12">
                                <div class="p-3 rounded-3 bg-white border d-flex align-items-center justify-content-between flex-wrap gap-2">
                                    <div>
                                        <span class="fw-bold text-dark"><i class="bi bi-people-fill text-info me-1"></i> أفراد الأسرة المسجلون في الطلب:</span>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle ms-1 font-monospace">${val.length} أفراد</span>
                                        <div class="text-muted small mt-1">${val.map(f => f.full_name || 'فرد').join('، ')}</div>
                                    </div>
                                    ${req.create_form_url ? `
                                        <a href="${req.create_form_url}" class="btn btn-sm btn-outline-primary rounded-pill">
                                            <i class="bi bi-pencil-square me-1"></i> تعديل تفصيلي في استمارة المستفيد
                                        </a>
                                    ` : ''}
                                </div>
                            </div>
                        `;
                        container.insertAdjacentHTML('beforeend', famHtml);
                    }
                    return;
                }

                // Project Select
                if (k === 'project_id') {
                    let optionsHtml = '<option value="">— اختر المشروع —</option>';
                    Object.keys(projectsList).forEach(pId => {
                        const pName = projectsList[pId];
                        const selected = String(val) === String(pId) ? 'selected' : '';
                        optionsHtml += `<option value="${pId}" ${selected}>${escapeHtml(pName)}</option>`;
                    });
                    const fieldHtml = `
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">${escapeHtml(label)}</label>
                            <select name="fields[${k}]" class="form-select">${optionsHtml}</select>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', fieldHtml);
                    return;
                }

                // Status select if status field in payload
                if (k === 'status' && req.model_basename === 'Beneficiary') {
                    const statuses = {
                        'new': 'تحت التقديم (جديد)',
                        'under_review': 'تحت المراجعة',
                        'accepted': 'مقبول',
                        'rejected': 'مرفوض',
                        'archived_improved': 'أرشيف — تحسن مادي / شفاء',
                        'archived_deceased': 'أرشيف — توفي'
                    };
                    let optionsHtml = '';
                    Object.keys(statuses).forEach(stKey => {
                        const selected = String(val) === String(stKey) ? 'selected' : '';
                        optionsHtml += `<option value="${stKey}" ${selected}>${statuses[stKey]}</option>`;
                    });
                    const fieldHtml = `
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">${escapeHtml(label)}</label>
                            <select name="fields[${k}]" class="form-select">${optionsHtml}</select>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', fieldHtml);
                    return;
                }

                // Classification A/B
                if (k === 'classification') {
                    const fieldHtml = `
                        <div class="col-md-6">
                            <label class="form-label fw-bold small text-dark">${escapeHtml(label)}</label>
                            <select name="fields[${k}]" class="form-select">
                                <option value="A" ${val === 'A' ? 'selected' : ''}>تصنيف (A)</option>
                                <option value="B" ${val === 'B' ? 'selected' : ''}>تصنيف (B)</option>
                            </select>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', fieldHtml);
                    return;
                }

                // Textarea for large text
                if (['notes', 'notes_cases', 'description', 'social_status_description', 'address', 'archived_reason', 'rejection_reason'].includes(k)) {
                    const fieldHtml = `
                        <div class="col-12">
                            <label class="form-label fw-bold small text-dark">${escapeHtml(label)}</label>
                            <textarea name="fields[${k}]" class="form-control" rows="2">${escapeHtml(val || '')}</textarea>
                        </div>
                    `;
                    container.insertAdjacentHTML('beforeend', fieldHtml);
                    return;
                }

                // If object or array (other than family_members)
                if (typeof val === 'object' && val !== null) {
                    return;
                }

                // Regular input
                const isWide = ['full_name', 'name', 'patient_name', 'title'].includes(k);
                const colClass = isWide ? 'col-md-6' : 'col-md-4 col-sm-6';
                const inputType = ['amount', 'age', 'patient_age', 'monthly_income', 'collection_day', 'family_members_count'].includes(k) ? 'number' : (['birth_date', 'patient_birth_date', 'date', 'received_at'].includes(k) ? 'date' : 'text');
                const fieldHtml = `
                    <div class="${colClass}">
                        <label class="form-label fw-bold small text-dark">${escapeHtml(label)}</label>
                        <input type="${inputType}" name="fields[${k}]" value="${escapeHtml(val || '')}" class="form-control">
                    </div>
                `;
                container.insertAdjacentHTML('beforeend', fieldHtml);
            });
        }

        document.getElementById('payloadEditNotes').value = '';

        // Action buttons
        const secondaryContainer = document.getElementById('payloadEditSecondaryActions');
        const primaryContainer = document.getElementById('payloadEditPrimaryActions');
        secondaryContainer.innerHTML = '';
        primaryContainer.innerHTML = '';

        if (mode === 'rejected') {
            if (req.create_form_url) {
                secondaryContainer.innerHTML = `
                    <a href="${req.create_form_url}" class="btn btn-outline-dark btn-sm rounded-pill px-3">
                        <i class="bi bi-ui-checks me-1"></i> فتح في استمارة المستفيد الكاملة
                    </a>
                `;
            }
            primaryContainer.innerHTML = `
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" name="action_type" value="save" class="btn btn-outline-secondary px-3">
                    <i class="bi bi-save me-1"></i> حفظ كمسودة
                </button>
                <button type="submit" name="action_type" value="resubmit" class="btn btn-success fw-bold px-4 shadow-sm">
                    <i class="bi bi-send-check-fill me-1"></i> حفظ وإعادة الإرسال للموافقة
                </button>
            `;
        } else {
            // Pending
            if (isUserAdmin) {
                primaryContainer.innerHTML = `
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" name="action_type" value="save" class="btn btn-outline-primary fw-semibold px-3">
                        <i class="bi bi-save me-1"></i> حفظ التعديلات فقط
                    </button>
                    <button type="submit" name="action_type" value="approve" class="btn btn-success fw-bold px-4 shadow-sm">
                        <i class="bi bi-check-circle-fill me-1"></i> حفظ وموافقة فورية
                    </button>
                `;
            } else {
                primaryContainer.innerHTML = `
                    <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" name="action_type" value="save" class="btn btn-primary fw-bold px-4 shadow-sm">
                        <i class="bi bi-save me-1"></i> حفظ تعديلات الطلب
                    </button>
                `;
            }
        }

        new bootstrap.Modal(document.getElementById('payloadEditModal')).show();
    }

    function openRejectModal(id) {
        const form = document.getElementById('rejectForm');
        form.action = "{{ url('admin/change-requests') }}/" + id + "/reject";
        new bootstrap.Modal(document.getElementById('rejectModal')).show();
    }

    function openEditModal(req) {
        const form = document.getElementById('editForm');
        form.action = "{{ url('admin/change-requests') }}/" + req.id;
        
        document.getElementById('editStatus').value = req.status;
        document.getElementById('editReason').value = req.rejection_reason || '';
        
        new bootstrap.Modal(document.getElementById('editModal')).show();
    }

    // Bulk Actions Logic
    function toggleAll(source) {
        document.querySelectorAll('.bulk-item').forEach(checkbox => {
            checkbox.checked = source.checked;
        });
        updateBulkUI();
    }

    function updateBulkUI() {
        const count = document.querySelectorAll('.bulk-item:checked').length;
        const toolbar = document.getElementById('bulk-actions');
        if (count > 0) {
            toolbar.classList.remove('d-none');
            toolbar.classList.add('d-inline-flex', 'gap-2');
        } else {
            toolbar.classList.add('d-none');
            toolbar.classList.remove('d-inline-flex', 'gap-2');
        }
    }

    function submitBulk(action) {
        const ids = Array.from(document.querySelectorAll('.bulk-item:checked')).map(cb => cb.value);
        if (ids.length === 0) return;

        const msg = action === 'destroy' ? 'حذف' : 'تراجع عن';
        if (!confirm(`هل أنت متأكد من ${msg} ${ids.length} عنصر محدد؟`)) return;

        const formId = action === 'destroy' ? 'bulk-destroy-form' : 'bulk-revert-form';
        const form = document.getElementById(formId);
        
        // Clear previous inputs
        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

        // Add new inputs
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        form.submit();
    }
</script>

<!-- Modal Quick View Field Visit -->
<div class="modal fade" id="visitQuickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header bg-light py-3 border-bottom d-flex align-items-center justify-content-between">
                <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2 mb-0">
                    <i class="bi bi-file-earmark-medical-fill text-primary"></i>
                    <span>استمارة البحث الميداني: <span id="quickViewCaseName" class="text-primary"></span></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="إغلاق"></button>
            </div>
            <div class="modal-body p-0" style="min-height: 550px; background-color: #f8fafc;">
                <div id="quickViewLoader" class="d-flex align-items-center justify-content-center p-5 text-muted">
                    <div class="spinner-border text-primary me-2" role="status"></div>
                    <span>جارٍ تحميل استمارة البحث...</span>
                </div>
                <iframe id="quickViewFrame" src="" style="width: 100%; height: 75vh; border: none; display: none;" onload="document.getElementById('quickViewLoader').style.display='none'; this.style.display='block';"></iframe>
            </div>
        </div>
    </div>
</div>

<script>
function openQuickVisitModal(url, name) {
    document.getElementById('quickViewCaseName').innerText = name || '';
    const frame = document.getElementById('quickViewFrame');
    const loader = document.getElementById('quickViewLoader');
    frame.style.display = 'none';
    loader.style.display = 'flex';
    frame.src = url;
    const modalEl = document.getElementById('visitQuickViewModal');
    const modal = new bootstrap.Modal(modalEl);
    modal.show();
}
</script>

<form id="bulk-destroy-form" action="{{ route('change-requests.bulk-destroy') }}" method="POST" style="display:none">
    @csrf
</form>
<form id="bulk-revert-form" action="{{ route('change-requests.bulk-revert') }}" method="POST" style="display:none">
    @csrf
</form>
@endsection


