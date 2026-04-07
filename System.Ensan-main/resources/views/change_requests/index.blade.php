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
                    <div class="d-none" id="bulk-actions">
                        <button class="btn btn-sm btn-outline-warning" onclick="submitBulk('revert')">
                            <i class="bi bi-arrow-counterclockwise"></i> تراجع
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="submitBulk('destroy')">
                            <i class="bi bi-trash"></i> حذف
                        </button>
                    </div>
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
                            <input type="checkbox" class="form-check-input" id="check-all" onchange="toggleAll(this)">
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
                                <input type="checkbox" class="form-check-input bulk-item" value="{{ $req->id }}" onchange="updateBulkUI()">
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
                                <div class="small text-muted">{{ $req->created_at->format('Y-m-d') }}</div>
                                <div class="small text-muted" style="font-size:0.75rem">{{ $req->created_at->format('H:i') }}</div>
                            </td>
                            <td class="text-end">
                                @if($req->status === 'pending')
                                    <div class="btn-group">
                                        <form action="{{ route('change-requests.approve', $req) }}" method="POST" onsubmit="return confirm('تأكيد الموافقة على الطلب؟');">
                                            @csrf
                                            <button class="btn btn-sm btn-success"><i class="bi bi-check-lg"></i> موافقة</button>
                                        </form>
                                        <button class="btn btn-sm btn-danger ms-1" onclick="openRejectModal('{{ $req->id }}')">
                                            <i class="bi bi-x-lg"></i> رفض
                                        </button>
                                    </div>
                                @else
                                    <div class="d-flex gap-1 align-items-center">
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
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td colspan="8" class="p-0 border-0">
                                <div class="collapse p-4 border-bottom shadow-inner animate-fade-in" id="details-{{ $req->id }}" style="background: rgba(0,0,0,0.2);">
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
                                        $isDeleted = !$req->subject;
                                    @endphp

                                    <div class="mb-4 bg-white bg-opacity-10 p-3 rounded border border-light border-opacity-10 shadow-sm">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0 small text-uppercase text-muted opacity-75">
                                                <i class="bi bi-bullseye me-1"></i> العنصر المستهدف:
                                                @if($isDeleted) <span class="badge bg-danger bg-opacity-10 text-danger ms-2" style="font-size: 8px;">محذوف من النظام</span> @endif
                                            </h6>
                                            @if(!$isDeleted)
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
                                                    <span class="text-muted small font-monospace">#{{ $req->model_id }}</span>
                                                </div>
                                                
                                                @if(!$isDeleted)
                                                    <div class="fw-bold fs-6">
                                                        @if($modelBasename === 'Beneficiary') {{ $req->subject->full_name }}
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
                                                    <div class="fw-bold fs-6 text-muted font-italic">
                                                        @php
                                                            $p = is_array($req->payload) ? ($req->payload['data'] ?? $req->payload) : [];
                                                            $displayName = $p['full_name'] ?? ($p['name'] ?? ($p['title'] ?? ($p['delegate_name'] ?? ($p['volunteer_name'] ?? ($p['employee_name'] ?? ($p['user_name'] ?? '—'))))));
                                                        @endphp
                                                        {{ $displayName ?: "ID: ".$req->model_id }}
                                                    </div>
                                                    <div class="text-muted small opacity-75">
                                                        @if($modelBasename === 'Donation' || $modelBasename === 'Expense') مبلغ: {{ number_format($p['amount'] ?? 0) }} ج.م
                                                        @elseif($modelBasename === 'Task') @if(isset($p['assigned_to'])) مسؤول: {{ $p['assignee_name'] ?? $p['assigned_to'] }} @endif
                                                        @elseif($modelBasename === 'User') {{ $p['job_title'] ?? 'مستخدم' }}
                                                        @elseif($modelBasename === 'Account') كود: {{ $p['code'] ?? '—' }}
                                                        @elseif($modelBasename === 'DelegateTrip') {{ $p['delegate_name'] ?? '—' }} ({{ $p['trip_date'] ?? '—' }})
                                                        @elseif($modelBasename === 'Leave') النوع: {{ $p['type'] ?? '—' }} • التاريخ: {{ $p['start_date'] ?? '—' }}
                                                        @elseif($modelBasename === 'JournalEntry') النوع: {{ $p['entry_type'] ?? '—' }} • الوصف: {{ $p['description'] ?? '—' }}
                                                        @elseif($modelBasename === 'FinancialClosure') فرع: {{ $p['branch'] ?? '—' }} • تاريخ: {{ $p['date'] ?? '—' }}
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
                                                'donor_id' => 'المتبرع',
                                                'delegate_id' => 'المندوب',
                                                'warehouse_id' => 'المخزن',
                                                'item_id' => 'الصنف',
                                                'quantity' => 'الكمية',
                                                'estimated_value' => 'القيمة التقديرية',
                                                'allocation_note' => 'توجيه التبرع',
                                                'status' => 'الحالة',
                                                'name' => 'الاسم الكامل',
                                                'phone' => 'رقم الهاتف',
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
                                                'rejected' => 'مرفوض ❌',
                                                'pending' => 'تحت المراجعة ⏳',
                                                'asset' => 'أصول (Assets)',
                                                'liability' => 'خصوم (Liabilities)',
                                                'equity' => 'حقوق ملكية (Equity)',
                                                'revenue' => 'إيرادات (Revenue)',
                                                'expense' => 'مصروفات (Expense)',
                                            ];

                                            $fmt = function($k, $v) use ($valueMaps) {
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
                                                    return '<pre class="small mb-0 mt-1 bg-white p-2 rounded border" style="font-size: 10px;">' . json_encode($v, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</pre>';
                                                }
                                                
                                                // Handle Passwords & Hashes
                                                if ($k === 'password' || (is_string($v) && strlen($v) > 50 && str_starts_with($v, '$2y$'))) {
                                                    return '<span class="text-muted small"><i class="bi bi-shield-lock-fill"></i> (بيانات مشفرة)</span>';
                                                }

                                                if (isset($valueMaps[$v])) return $valueMaps[$v];
                                                if (str_contains($k, 'id') && is_numeric($v)) return '#' . $v;
                                                if (in_array($k, ['amount','estimated_value','cost','salary'])) return '<strong>' . number_format((float)$v, 2) . '</strong> <small>ج.م</small>';
                                                if ($k === 'quantity') return '<strong>' . number_format((float)$v, 0) . '</strong>';
                                                
                                                if (is_bool($v)) return $v ? '<span class="badge bg-success-subtle text-success">نعم</span>' : '<span class="badge bg-danger-subtle text-danger">لا</span>';
                                                
                                                if (in_array($k, ['received_at', 'date', 'created_at', 'updated_at']) && $v) {
                                                    try { return \Illuminate\Support\Carbon::parse($v)->format('Y-m-d'); } catch(\Exception $e) { return $v; }
                                                }
                                                
                                                return e($v);
                                            };
                                        @endphp

                                        <div class="row g-2">
                                            @if($diffData && $req->action === 'update')
                                                @foreach($diffData as $key => $dVals)
                                                    @continue($key === 'password')
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
                                                    @continue(in_array($key, ['_token', '_method', 'password', 'password_confirmation', 'created_by', 'updated_by', 'id']))
                                                    @continue(is_null($val) || $val === '')
                                                    
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

<script>
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

<form id="bulk-destroy-form" action="{{ route('change-requests.bulk-destroy') }}" method="POST" style="display:none">
    @csrf
</form>
<form id="bulk-revert-form" action="{{ route('change-requests.bulk-revert') }}" method="POST" style="display:none">
    @csrf
</form>
@endsection


