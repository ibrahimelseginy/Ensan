@extends('layouts.app')
@section('content')

{{-- Trello Kanban Board Hero Header --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
    <div class="hero-content">
        <div class="d-flex align-items-center gap-2 mb-2">
            <span class="badge bg-primary bg-opacity-25 text-primary-emphasis border border-primary border-opacity-25 rounded-pill px-3 py-1">
                <i class="bi bi-kanban-fill me-1"></i> لوحة كانبان التفاعلية (Trello Style)
            </span>
            <span class="badge bg-success bg-opacity-25 text-success-emphasis border border-success border-opacity-25 rounded-pill px-3 py-1">
                <i class="bi bi-shield-lock-fill me-1"></i> سجلات مؤمّنة ضد الحذف
            </span>
        </div>
        <h1 class="hero-title text-white fw-bold mb-1">لوحة مهام فريق العمل</h1>
        <p class="hero-subtitle text-light opacity-75 mb-3">متابعة دقيقة لكل مهمة من البداية حتى المراجعة والاعتماد النهائي مع توثيق زمني كامل.</p>

        {{-- Top Summary Stats --}}
        <div class="d-flex flex-wrap gap-3 mt-3">
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 px-3 py-2 text-white d-flex align-items-center gap-2">
                <i class="bi bi-layers text-primary fs-5"></i>
                <div>
                    <div class="small opacity-75" style="font-size: 0.72rem;">إجمالي المهام</div>
                    <div class="fw-bold">{{ $stats['total'] }}</div>
                </div>
            </div>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 px-3 py-2 text-white d-flex align-items-center gap-2">
                <i class="bi bi-clipboard2 text-secondary fs-5"></i>
                <div>
                    <div class="small opacity-75" style="font-size: 0.72rem;">قائمة المهام</div>
                    <div class="fw-bold">{{ $stats['pending'] }}</div>
                </div>
            </div>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 px-3 py-2 text-white d-flex align-items-center gap-2">
                <i class="bi bi-hourglass-split text-info fs-5"></i>
                <div>
                    <div class="small opacity-75" style="font-size: 0.72rem;">قيد التنفيذ</div>
                    <div class="fw-bold">{{ $stats['in_progress'] }}</div>
                </div>
            </div>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 px-3 py-2 text-white d-flex align-items-center gap-2">
                <i class="bi bi-search text-warning fs-5"></i>
                <div>
                    <div class="small opacity-75" style="font-size: 0.72rem;">قيد المراجعة</div>
                    <div class="fw-bold">{{ $stats['under_review'] }}</div>
                </div>
            </div>
            <div class="bg-white bg-opacity-10 border border-white border-opacity-10 rounded-3 px-3 py-2 text-white d-flex align-items-center gap-2">
                <i class="bi bi-check2-circle text-success fs-5"></i>
                <div>
                    <div class="small opacity-75" style="font-size: 0.72rem;">مكتملة ومراجعة</div>
                    <div class="fw-bold">{{ $stats['done'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <i class="bi bi-kanban hero-icon d-none d-md-block opacity-25" style="font-size: 7rem;"></i>
</div>

<div class="container-fluid py-4 animate-slide-up animate-delay-1">

    {{-- Filter & Actions Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                {{-- Left Actions & Filters --}}
                <form method="GET" action="{{ route('employee-tasks.index') }}" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

                    <div class="input-group input-group-sm" style="max-width: 250px;">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-light border-0" placeholder="بحث في المهام...">
                    </div>

                    @if($isAdmin)
                    <select name="assigned_to" class="form-select form-select-sm bg-light border-0" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="">كل الموظفين</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    <select name="priority" class="form-select form-select-sm bg-light border-0" style="max-width: 150px;" onchange="this.form.submit()">
                        <option value="">كل الأولويات</option>
                        <option value="urgent" {{ $selectedPriority === 'urgent' ? 'selected' : '' }}>🔥 عاجل جداً</option>
                        <option value="high" {{ $selectedPriority === 'high' ? 'selected' : '' }}>⚡ أولوية قصوى</option>
                        <option value="medium" {{ $selectedPriority === 'medium' ? 'selected' : '' }}>📌 عادية</option>
                        <option value="low" {{ $selectedPriority === 'low' ? 'selected' : '' }}>🌱 منخفضة</option>
                    </select>

                    @if($search || $selectedEmployee || $selectedPriority || $selectedProject)
                    <a href="{{ route('employee-tasks.index', ['view' => $viewMode]) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-x-circle"></i> إلغاء الفلترة
                    </a>
                    @endif
                </form>

                {{-- Right Actions: View switch & New Task --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Toggle Views --}}
                    <div class="btn-group btn-group-sm bg-light p-1 rounded-3">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'board']) }}" class="btn btn-sm {{ $viewMode === 'board' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-muted' }}" title="عرض اللوحة (Kanban)">
                            <i class="bi bi-kanban"></i> لوحة
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="btn btn-sm {{ $viewMode === 'table' ? 'btn-primary shadow-sm' : 'btn-light border-0 text-muted' }}" title="عرض الجدول">
                            <i class="bi bi-table"></i> جدول
                        </a>
                    </div>

                    @if($isAdmin)
                    <button type="button" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm d-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                        <i class="bi bi-plus-lg"></i>
                        <span>إضافة مهمة جديدة</span>
                    </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Alert indicating delete protection and privacy --}}
    <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-4 border" style="background: rgba(16, 185, 129, 0.05); border-color: rgba(16, 185, 129, 0.2) !important;">
        <div class="d-flex align-items-center gap-2 text-success">
            <i class="bi bi-info-circle-fill fs-5"></i>
            <span class="small fw-semibold">
                @if($isAdmin)
                    أنت بصلاحية مسؤول: يمكنك متابعة كافة مهام الموظفين، تكليف مهام جديدة، واعتماد المراجعات النهائية.
                @else
                    تظهر لك هنا مهامك الفردية والمهام المشتركة معك فقط. يمكنك نقل المرحلة، رفع ملفات العمل، وتدوين الملاحظات.
                @endif
                (المهام غير قابلة للحذف نهائياً لحفظ الحقوق والسجلات).
            </span>
        </div>
    </div>

    {{-- ============================================================== --}}
    {{-- VIEW 1: TRELLO KANBAN BOARD VIEW                               --}}
    {{-- ============================================================== --}}
    @if($viewMode === 'board')
    <div class="kanban-board-wrapper">
        <div class="kanban-columns-container">

            @foreach($stages as $stageKey => $stage)
            <div class="kanban-column" data-stage="{{ $stageKey }}">
                {{-- Column Header --}}
                <div class="kanban-column-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="kanban-column-dot" style="background-color: {{ $stage['color'] }};"></span>
                        <h6 class="fw-bold mb-0 text-dark">{{ $stage['title'] }}</h6>
                        <span class="badge {{ $stage['badge_class'] }} rounded-pill px-2 py-1 count-badge">
                            {{ $stage['tasks']->count() }}
                        </span>
                    </div>
                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">{{ $stage['subtitle'] }}</small>
                </div>

                {{-- Column Cards Container --}}
                <div class="kanban-cards-list" id="column-{{ $stageKey }}" ondragover="allowDrop(event)" ondrop="dropTask(event, '{{ $stageKey }}')">
                    @forelse($stage['tasks'] as $task)
                    <div class="kanban-card" id="task-card-{{ $task->id }}" draggable="true" ondragstart="dragTask(event, '{{ $task->id }}')">

                        {{-- Card Header: Priority & Associated Project --}}
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            @php
                                $pBadge = $task->priority_badge;
                            @endphp
                            <span class="badge bg-{{ $pBadge['color'] }}-subtle text-{{ $pBadge['color'] }} rounded-pill" style="font-size: 0.7rem;">
                                <i class="bi bi-{{ $pBadge['icon'] }} me-1"></i>{{ $pBadge['label'] }}
                            </span>

                            @if($task->project)
                                <span class="badge bg-light text-secondary border small" style="font-size: 0.68rem;" title="المشروع: {{ $task->project->name }}">
                                    <i class="bi bi-briefcase me-1"></i>{{ Str::limit($task->project->name, 14) }}
                                </span>
                            @elseif($task->campaign)
                                <span class="badge bg-light text-secondary border small" style="font-size: 0.68rem;" title="الحملة: {{ $task->campaign->name }}">
                                    <i class="bi bi-megaphone me-1"></i>{{ Str::limit($task->campaign->name, 14) }}
                                </span>
                            @endif
                        </div>

                        {{-- Card Title --}}
                        <h6 class="card-title fw-bold text-dark mb-2">
                            <a href="{{ route('employee-tasks.show', $task) }}" class="text-decoration-none text-dark hover-primary">
                                {{ $task->title }}
                            </a>
                        </h6>

                        {{-- Card Description Excerpt --}}
                        @if($task->description)
                        <p class="card-description small text-muted mb-3" style="font-size: 0.8rem; line-height: 1.5;">
                            {{ Str::limit($task->description, 80) }}
                        </p>
                        @endif

                        {{-- Due Date Alert --}}
                        @if($task->due_date)
                        @php
                            $isOverdue = $task->due_date->isPast() && $task->status !== 'done';
                            $isToday = $task->due_date->isToday() && $task->status !== 'done';
                        @endphp
                        <div class="mb-3">
                            <span class="badge {{ $isOverdue ? 'bg-danger text-white' : ($isToday ? 'bg-warning text-dark' : 'bg-light text-muted border') }} rounded-pill" style="font-size: 0.72rem;">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $task->due_date->format('Y-m-d') }}
                                @if($isOverdue) (متأخرة) @elseif($isToday) (اليوم) @endif
                            </span>
                        </div>
                        @endif

                        {{-- Card Footer: Assignee(s), Attachments, Sijill count & Quick Action --}}
                        <div class="card-footer-meta pt-2 border-top d-flex justify-content-between align-items-center">
                            {{-- Team Avatars --}}
                            <div class="d-flex align-items-center avatar-group">
                                @if($task->assignee)
                                    <div class="avatar-circle bg-primary text-white" title="المكلّف الرئيسي: {{ $task->assignee->name }}">
                                        {{ mb_substr($task->assignee->name, 0, 1) }}
                                    </div>
                                @endif
                                @foreach($task->assignees as $collab)
                                    @if(!$task->assignee || $collab->id !== $task->assignee->id)
                                    <div class="avatar-circle bg-info text-white ms-n2" title="مشترك: {{ $collab->name }}">
                                        {{ mb_substr($collab->name, 0, 1) }}
                                    </div>
                                    @endif
                                @endforeach
                            </div>

                            {{-- Counters & Quick Action Dropdown --}}
                            <div class="d-flex align-items-center gap-2">
                                @if($task->attachments->count() > 0)
                                <span class="text-muted small" title="{{ $task->attachments->count() }} ملفات مرفقة" style="font-size: 0.75rem;">
                                    <i class="bi bi-paperclip text-primary"></i> {{ $task->attachments->count() }}
                                </span>
                                @endif

                                @if($task->logs->count() > 0)
                                <span class="text-muted small" title="{{ $task->logs->count() }} أحداث في سجل التغييرات" style="font-size: 0.75rem;">
                                    <i class="bi bi-chat-left-text text-secondary"></i> {{ $task->logs->count() }}
                                </span>
                                @endif

                                {{-- Quick Status Option Dropdown --}}
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light border-0 p-1 rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="تغيير المرحلة">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3 p-2" style="min-width: 190px;">
                                        <li class="dropdown-header small text-muted">نقل المهمة إلى:</li>
                                        <li>
                                            <a class="dropdown-item rounded-2 small d-flex align-items-center gap-2 {{ $task->status === 'pending' ? 'active' : '' }}" href="javascript:void(0)" onclick="quickChangeStatus('{{ $task->id }}', 'pending')">
                                                <i class="bi bi-clipboard2 text-secondary"></i> قائمة المهام
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 small d-flex align-items-center gap-2 {{ $task->status === 'in_progress' ? 'active' : '' }}" href="javascript:void(0)" onclick="quickChangeStatus('{{ $task->id }}', 'in_progress')">
                                                <i class="bi bi-hourglass-split text-primary"></i> قيد التنفيذ
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 small d-flex align-items-center gap-2 {{ $task->status === 'under_review' ? 'active' : '' }}" href="javascript:void(0)" onclick="openStatusModal('{{ $task->id }}', 'under_review', '{{ addslashes($task->title) }}')">
                                                <i class="bi bi-search text-warning"></i> قيد المراجعة
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item rounded-2 small d-flex align-items-center gap-2 {{ $task->status === 'done' ? 'active' : '' }}" href="javascript:void(0)" onclick="openStatusModal('{{ $task->id }}', 'done', '{{ addslashes($task->title) }}')">
                                                <i class="bi bi-check2-circle text-success"></i> مكتملة ومراجعة (Done)
                                            </a>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li>
                                            <a class="dropdown-item rounded-2 small text-primary" href="{{ route('employee-tasks.show', $task) }}">
                                                <i class="bi bi-eye me-1"></i> فتح التفاصيل والملفات
                                            </a>
                                        </li>
                                        @if($isAdmin)
                                        <li>
                                            <a class="dropdown-item rounded-2 small text-secondary" href="{{ route('employee-tasks.edit', $task) }}">
                                                <i class="bi bi-pencil me-1"></i> تعديل المهمة
                                            </a>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>

                    </div>
                    @empty
                    <div class="empty-column-state text-center p-4 text-muted">
                        <i class="bi bi-inbox fs-3 opacity-25 d-block mb-1"></i>
                        <span class="small" style="font-size: 0.78rem;">لا توجد مهام في هذه المرحلة</span>
                    </div>
                    @endforelse
                </div>

            </div>
            @endforeach

        </div>
    </div>
    @else
    {{-- ============================================================== --}}
    {{-- VIEW 2: TRADITIONAL TABLE VIEW                                 --}}
    {{-- ============================================================== --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr class="text-secondary small text-uppercase">
                        <th class="py-3 px-4">#</th>
                        <th class="py-3 px-4">المهمة</th>
                        <th class="py-3 px-4">المكلّف الرئيسي</th>
                        <th class="py-3 px-4">فريق العمل المشترك</th>
                        <th class="py-3 px-4">الأولوية</th>
                        <th class="py-3 px-4">المرحلة / الحالة</th>
                        <th class="py-3 px-4">تاريخ الاستحقاق</th>
                        <th class="py-3 px-4">الملفات والسجل</th>
                        <th class="py-3 px-4 text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allTasks as $index => $t)
                    <tr>
                        <td class="px-4 text-muted small">{{ $index + 1 }}</td>
                        <td class="px-4 fw-bold">
                            <a href="{{ route('employee-tasks.show', $t) }}" class="text-decoration-none text-dark hover-primary">
                                {{ $t->title }}
                            </a>
                        </td>
                        <td class="px-4">
                            @if($t->assignee)
                                <div class="d-flex align-items-center gap-2">
                                    <div class="avatar-circle bg-primary text-white" style="width: 28px; height: 28px; font-size: 0.75rem;">
                                        {{ mb_substr($t->assignee->name, 0, 1) }}
                                    </div>
                                    <span class="small fw-semibold">{{ $t->assignee->name }}</span>
                                </div>
                            @else
                                <span class="text-muted small">غير معين</span>
                            @endif
                        </td>
                        <td class="px-4">
                            @if($t->assignees->count() > 0)
                                <div class="d-flex align-items-center avatar-group">
                                    @foreach($t->assignees as $collab)
                                        <div class="avatar-circle bg-info text-white" title="{{ $collab->name }}" style="width: 26px; height: 26px; font-size: 0.7rem;">
                                            {{ mb_substr($collab->name, 0, 1) }}
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-muted small">—</span>
                            @endif
                        </td>
                        <td class="px-4">
                            @php $pBadge = $t->priority_badge; @endphp
                            <span class="badge bg-{{ $pBadge['color'] }}-subtle text-{{ $pBadge['color'] }} rounded-pill">
                                {{ $pBadge['label'] }}
                            </span>
                        </td>
                        <td class="px-4">
                            @if($t->status === 'pending')
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">قائمة المهام</span>
                            @elseif($t->status === 'in_progress')
                                <span class="badge bg-primary-subtle text-primary rounded-pill px-3">قيد التنفيذ</span>
                            @elseif($t->status === 'under_review')
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-3">قيد المراجعة</span>
                            @elseif($t->status === 'done')
                                <span class="badge bg-success-subtle text-success rounded-pill px-3">مكتملة ومراجعة</span>
                            @endif
                        </td>
                        <td class="px-4 small">
                            {{ $t->due_date ? $t->due_date->format('Y-m-d') : '—' }}
                        </td>
                        <td class="px-4 small text-muted">
                            <span class="me-2"><i class="bi bi-paperclip text-primary"></i> {{ $t->attachments->count() }}</span>
                            <span><i class="bi bi-clock-history text-secondary"></i> {{ $t->logs->count() }}</span>
                        </td>
                        <td class="px-4 text-end">
                            <a href="{{ route('employee-tasks.show', $t) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-eye me-1"></i> التفاصيل
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard2-x fs-1 d-block mb-2 opacity-50"></i>
                            لا توجد مهام مطابقة للشروط
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

</div>

{{-- ============================================================== --}}
{{-- MODAL: UPDATE STATUS & ADD NOTE / ATTACHMENT                  --}}
{{-- ============================================================== --}}
<div class="modal fade" id="updateStatusModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 bg-primary text-white p-4">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-arrow-repeat me-2"></i> تحديث مرحلة المهمة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="updateStatusForm" method="POST" action="">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">المهمة:</label>
                        <div id="modalTaskTitle" class="fw-bold fs-6 text-dark mb-2"></div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">المرحلة الجديدة:</label>
                        <select name="status" id="modalStatusSelect" class="form-select rounded-3">
                            <option value="pending">📋 قائمة المهام (To-Do)</option>
                            <option value="in_progress">⏳ قيد التنفيذ (In Progress)</option>
                            <option value="under_review">🔍 قيد المراجعة (Review)</option>
                            <option value="done">✅ مكتملة ومراجعة (Done)</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small text-muted fw-bold">ملاحظات الإنجاز أو المراجعة (تُسجل في سجل التغييرات):</label>
                        <textarea name="status_note" rows="3" class="form-control rounded-3" placeholder="اكتب ملاحظة أو توضيح لما تم تنفيذه أو أسباب المراجعة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-check-lg me-1"></i> حفظ وتوثيق في السجل
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: CREATE TASK (ADMIN ONLY)                                --}}
{{-- ============================================================== --}}
@if($isAdmin)
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-plus-circle me-2 text-primary"></i> تكليف وإضافة مهمة جديدة
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('employee-tasks.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label small fw-bold">عنوان المهمة <span class="text-danger">*</span></label>
                            <input type="text" name="title" required class="form-control rounded-3" placeholder="مثال: مراجعة كشوفات المستفيدين لشهر أكتوبر">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">تفاصيل ووصف المهمة</label>
                            <textarea name="description" rows="3" class="form-control rounded-3" placeholder="شرح تفصيلي للمطلوب تنفيذه والنتائج المتوقعة..."></textarea>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">المكلّف الرئيسي (الموظف المسؤول) <span class="text-danger">*</span></label>
                            <select name="assigned_to" required class="form-select rounded-3">
                                <option value="">اختر الموظف المسؤول...</option>
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }} ({{ $emp->email }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">فريق العمل المشترك (مهام مشتركة)</label>
                            <select name="shared_with[]" multiple class="form-select rounded-3" style="min-height: 80px;" title="اضغط Ctrl أو Cmd لاختيار أكثر من موظف">
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>
                            <small class="text-muted" style="font-size: 0.7rem;">الموظفون المشتركون ستظهر لهم المهمة في لوحتهم لمتابعتها وإنجازها سوياً.</small>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">الأولوية</label>
                            <select name="priority" class="form-select rounded-3">
                                <option value="medium">📌 عادية</option>
                                <option value="high">⚡ أولوية قصوى</option>
                                <option value="urgent">🔥 عاجل جداً</option>
                                <option value="low">🌱 منخفضة</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">المرحلة المبدئية</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="pending">📋 قائمة المهام (To-Do)</option>
                                <option value="in_progress">⏳ قيد التنفيذ (In Progress)</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label small fw-bold">تاريخ الاستحقاق (Deadline)</label>
                            <input type="date" name="due_date" class="form-control rounded-3">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الارتباط بمشروع (اختياري)</label>
                            <select name="project_id" class="form-select rounded-3">
                                <option value="">بدون مشروع محدد</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الارتباط بحملة (اختياري)</label>
                            <select name="campaign_id" class="form-select rounded-3">
                                <option value="">بدون حملة محددة</option>
                                @foreach($campaigns as $c)
                                    <option value="{{ $c->id }}">{{ $c->name }} ({{ $c->season_year }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">إرفاق ملفات أو مستندات أولية (اختياري)</label>
                            <input type="file" name="attachments[]" multiple class="form-control rounded-3">
                            <small class="text-muted" style="font-size: 0.7rem;">يمكن رفع ملفات PDF، صور، مستندات Word، أو جداول Excel حتى 20 ميجابايت.</small>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4 bg-light">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="bi bi-send-check me-1"></i> حفظ وتكليف المهمة فوراً
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

{{-- ============================================================== --}}
{{-- KANBAN BOARD STYLES & INTERACTIVE SCRIPTS                     --}}
{{-- ============================================================== --}}
<style>
.kanban-board-wrapper {
    overflow-x: auto;
    padding-bottom: 20px;
}

.kanban-columns-container {
    display: grid;
    grid-template-columns: repeat(4, minmax(300px, 1fr));
    gap: 20px;
    min-width: 1200px;
    align-items: start;
}

.kanban-column {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 18px;
    padding: 16px;
    display: flex;
    flex-direction: column;
    min-height: 600px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.kanban-column-header {
    padding-bottom: 12px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 14px;
}

.kanban-column-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    display: inline-block;
}

.kanban-cards-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    flex-grow: 1;
    min-height: 200px;
    padding: 4px;
    transition: background-color 0.2s ease;
}

.kanban-cards-list.drag-over {
    background-color: rgba(59, 130, 246, 0.06);
    border: 2px dashed #3b82f6;
    border-radius: 12px;
}

.kanban-card {
    background: #ffffff;
    border: 1px solid #edf2f7;
    border-radius: 14px;
    padding: 16px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    cursor: grab;
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    user-select: none;
}

.kanban-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.08);
    border-color: #cbd5e1;
}

.kanban-card:active {
    cursor: grabbing;
}

.kanban-card.is-dragging {
    opacity: 0.4;
    transform: scale(0.97);
}

.hover-primary:hover {
    color: #3b82f6 !important;
}

/* Avatar Group styling */
.avatar-group {
    display: flex;
    align-items: center;
}

.avatar-circle {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.72rem;
    border: 2px solid #ffffff;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}

.ms-n2 {
    margin-right: -8px;
}
</style>

<script>
let draggedTaskId = null;

function dragTask(event, taskId) {
    draggedTaskId = taskId;
    event.dataTransfer.setData('text/plain', taskId);
    event.target.classList.add('is-dragging');
}

document.addEventListener('dragend', function(event) {
    if (event.target.classList.contains('kanban-card')) {
        event.target.classList.remove('is-dragging');
    }
    document.querySelectorAll('.kanban-cards-list').forEach(col => col.classList.remove('drag-over'));
});

function allowDrop(event) {
    event.preventDefault();
    const column = event.currentTarget;
    if (!column.classList.contains('drag-over')) {
        column.classList.add('drag-over');
    }
}

document.querySelectorAll('.kanban-cards-list').forEach(col => {
    col.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });
});

function dropTask(event, targetStage) {
    event.preventDefault();
    const column = event.currentTarget;
    column.classList.remove('drag-over');

    const taskId = event.dataTransfer.getData('text/plain') || draggedTaskId;
    if (!taskId) return;

    const card = document.getElementById('task-card-' + taskId);
    if (!card) return;

    // Send AJAX to update status
    quickChangeStatus(taskId, targetStage, card, column);
}

function quickChangeStatus(taskId, newStatus, cardElement = null, targetColumn = null) {
    const url = "{{ url('employee-tasks') }}/" + taskId + "/status";
    const csrfToken = "{{ csrf_token() }}";

    fetch(url, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            status: newStatus
        })
    })
    .then(response => {
        if (!response.ok) throw new Error('فشل تحديث الحالة');
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // If dragging visually, move DOM element
            if (cardElement && targetColumn) {
                targetColumn.appendChild(cardElement);
            } else {
                // Reload page smoothly to refresh counters and positions
                window.location.reload();
            }
        }
    })
    .catch(error => {
        alert('حدث خطأ أثناء تحديث حالة المهمة، يرجى المحاولة مرة أخرى.');
        window.location.reload();
    });
}

function openStatusModal(taskId, targetStatus, taskTitle) {
    const form = document.getElementById('updateStatusForm');
    form.action = "{{ url('employee-tasks') }}/" + taskId + "/status";
    document.getElementById('modalTaskTitle').textContent = taskTitle;
    document.getElementById('modalStatusSelect').value = targetStatus;

    const modal = new bootstrap.Modal(document.getElementById('updateStatusModal'));
    modal.show();
}
</script>

@endsection
