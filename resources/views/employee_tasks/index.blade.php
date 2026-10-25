@extends('layouts.app')
@section('content')

{{-- Trello Kanban Board Hero Header --}}
<div class="tasks-dashboard-hero animate-slide-up">
    <div class="hero-bg-accent-1"></div>
    <div class="hero-bg-accent-2"></div>
    
    <div class="hero-content position-relative z-1">
        <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
            <span class="badge hero-pill-trello">
                <i class="bi bi-kanban-fill me-1 text-warning"></i> لوحة كانبان التفاعلية (Trello Style)
            </span>
            <span class="badge hero-pill-secure">
                <i class="bi bi-shield-lock-fill me-1 text-success"></i> سجلات مؤمّنة ضد الحذف
            </span>
        </div>
        <h1 class="hero-title text-white fw-bold mb-2">لوحة مهام فريق العمل</h1>
        <p class="hero-subtitle mb-4">متابعة دقيقة لكل مهمة من البداية حتى المراجعة والاعتماد النهائي مع توثيق زمني كامل.</p>

        {{-- Top Summary Stats (High Contrast Glass Cards) --}}
        <div class="d-flex flex-wrap gap-3 mt-2">
            <div class="hero-stat-card">
                <div class="hero-stat-icon-wrap" style="background: rgba(255, 255, 255, 0.2); color: #ffffff;">
                    <i class="bi bi-layers-fill"></i>
                </div>
                <div>
                    <div class="hero-stat-label">إجمالي المهام</div>
                    <div class="hero-stat-value">{{ $stats['total'] }}</div>
                </div>
            </div>

            <div class="hero-stat-card">
                <div class="hero-stat-icon-wrap" style="background: rgba(148, 163, 184, 0.3); color: #f1f5f9;">
                    <i class="bi bi-clipboard2-check-fill"></i>
                </div>
                <div>
                    <div class="hero-stat-label">قائمة المهام</div>
                    <div class="hero-stat-value">{{ $stats['pending'] }}</div>
                </div>
            </div>

            <div class="hero-stat-card">
                <div class="hero-stat-icon-wrap" style="background: rgba(56, 189, 248, 0.3); color: #bae6fd;">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div>
                    <div class="hero-stat-label">قيد التنفيذ</div>
                    <div class="hero-stat-value">{{ $stats['in_progress'] }}</div>
                </div>
            </div>

            <div class="hero-stat-card">
                <div class="hero-stat-icon-wrap" style="background: rgba(251, 191, 36, 0.3); color: #fef08a;">
                    <i class="bi bi-search"></i>
                </div>
                <div>
                    <div class="hero-stat-label">قيد المراجعة</div>
                    <div class="hero-stat-value">{{ $stats['under_review'] }}</div>
                </div>
            </div>

            <div class="hero-stat-card">
                <div class="hero-stat-icon-wrap" style="background: rgba(52, 211, 153, 0.35); color: #a7f3d0;">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div>
                    <div class="hero-stat-label">مكتملة ومعتمدة</div>
                    <div class="hero-stat-value">{{ $stats['done'] }}</div>
                </div>
            </div>
        </div>
    </div>
    <i class="bi bi-kanban hero-watermark-icon d-none d-lg-block"></i>
</div>

<div class="container-fluid py-4 animate-slide-up animate-delay-1">

    {{-- Filter & Actions Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white task-action-bar-card">
        <div class="card-body p-3">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">

                {{-- Left Actions & Filters --}}
                <form method="GET" action="{{ route('employee-tasks.index') }}" class="d-flex flex-wrap align-items-center gap-2 flex-grow-1">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

                    <div class="input-group input-group-sm search-input-group" style="max-width: 260px;">
                        <span class="input-group-text bg-light border-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" value="{{ $search }}" class="form-control bg-light border-0 fw-semibold" placeholder="بحث في المهام...">
                    </div>

                    @if($isAdmin)
                    <select name="assigned_to" class="form-select form-select-sm bg-light border-0 fw-semibold" style="max-width: 200px;" onchange="this.form.submit()">
                        <option value="">👤 كل الموظفين</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ $selectedEmployee == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                        @endforeach
                    </select>
                    @endif

                    <select name="priority" class="form-select form-select-sm bg-light border-0 fw-semibold" style="max-width: 155px;" onchange="this.form.submit()">
                        <option value="">🎯 كل الأولويات</option>
                        <option value="urgent" {{ $selectedPriority === 'urgent' ? 'selected' : '' }}>🔥 عاجل جداً</option>
                        <option value="high" {{ $selectedPriority === 'high' ? 'selected' : '' }}>⚡ أولوية قصوى</option>
                        <option value="medium" {{ $selectedPriority === 'medium' ? 'selected' : '' }}>📌 عادية</option>
                        <option value="low" {{ $selectedPriority === 'low' ? 'selected' : '' }}>🌱 منخفضة</option>
                    </select>

                    @if($search || $selectedEmployee || $selectedPriority || $selectedProject)
                    <a href="{{ route('employee-tasks.index', ['view' => $viewMode]) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3 fw-semibold">
                        <i class="bi bi-x-circle me-1"></i> إلغاء الفلترة
                    </a>
                    @endif
                </form>

                {{-- Right Actions: View switch & New Task --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Toggle Views --}}
                    <div class="btn-group btn-group-sm bg-light p-1 rounded-pill view-toggle-group">
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'board']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $viewMode === 'board' ? 'btn-emerald-active' : 'text-muted' }}" title="عرض اللوحة (Kanban)">
                            <i class="bi bi-kanban me-1"></i> لوحة
                        </a>
                        <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="btn btn-sm rounded-pill px-3 fw-bold {{ $viewMode === 'table' ? 'btn-emerald-active' : 'text-muted' }}" title="عرض الجدول">
                            <i class="bi bi-table me-1"></i> جدول
                        </a>
                    </div>

                    @if($isAdmin)
                    <button type="button" class="btn btn-sm btn-add-task rounded-pill px-3 shadow-sm d-flex align-items-center gap-2 fw-bold" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                        <i class="bi bi-plus-lg fs-6"></i>
                        <span>إضافة مهمة جديدة</span>
                    </button>
                    @endif
                </div>

            </div>
        </div>
    </div>

    {{-- Alert indicating delete protection and privacy --}}
    <div class="task-info-notice-bar mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="notice-icon-circle">
                <i class="bi bi-shield-check"></i>
            </div>
            <div class="notice-text">
                @if($isAdmin)
                    <strong class="text-emerald-dark">صلاحية مسؤول النظام:</strong> يمكنك متابعة كافة مهام الموظفين، تكليف مهام جديدة، واعتماد المراجعات النهائية.
                @else
                    <strong class="text-emerald-dark">صلاحية موظف:</strong> تظهر لك هنا مهامك الفردية والمهام المشتركة معك فقط. يمكنك نقل المرحلة، رفع ملفات العمل، وتدوين الملاحظات.
                @endif
                <span class="text-muted ms-1">(المهام غير قابلة للحذف نهائياً لحفظ جهود فريق العمل والأرشيف الزمني).</span>
            </div>
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

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-dark d-flex justify-content-between align-items-center mb-1">
                                <span><i class="bi bi-people-fill text-success me-1"></i> فريق العمل المشترك (المهام المشتركة)</span>
                                <span class="badge bg-light text-muted border px-2 py-1" id="createSharedBadge">تم اختيار 0</span>
                            </label>

                            <!-- Selected Chips Display Area -->
                            <div class="employee-chips-box p-2 rounded-3 border bg-light d-flex flex-wrap gap-2 align-items-center mb-2" id="createSharedChipsContainer" style="min-height: 48px;">
                                <span class="text-muted small empty-chips-placeholder" id="createSharedPlaceholder">
                                    <i class="bi bi-person-plus me-1"></i> اختر من القائمة أدناه لإضافة أعضاء لفريق المهمة
                                </span>
                            </div>

                            <!-- Hidden Native Multiple Select -->
                            <select name="shared_with[]" id="createSharedSelect" multiple class="d-none">
                                @foreach($employees as $emp)
                                    <option value="{{ $emp->id }}">{{ $emp->name }}</option>
                                @endforeach
                            </select>

                            <!-- Employee Picker Box with Live Search -->
                            <div class="employee-picker-card rounded-3 border bg-white shadow-sm overflow-hidden">
                                <div class="p-2 border-bottom bg-light d-flex align-items-center gap-2">
                                    <div class="input-group input-group-sm">
                                        <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                                        <input type="text" class="form-control border-0 bg-white" placeholder="بحث باسم الموظف..." oninput="filterEmployeePicker('createShared', this.value)">
                                    </div>
                                    <button type="button" class="btn btn-xs btn-outline-secondary text-nowrap rounded-pill px-2 py-1" style="font-size: 0.72rem;" onclick="clearAllEmployeePicker('createShared')">
                                        إلغاء الكل
                                    </button>
                                </div>
                                <div class="employee-picker-list p-2" id="createSharedList" style="max-height: 160px; overflow-y: auto;">
                                    @foreach($employees as $emp)
                                    <div class="employee-picker-item d-flex align-items-center justify-content-between p-2 rounded-2 mb-1" 
                                         data-id="{{ $emp->id }}" 
                                         data-name="{{ $emp->name }}"
                                         onclick="toggleEmployeePicker('createShared', '{{ $emp->id }}', '{{ addslashes($emp->name) }}')">
                                        <div class="d-flex align-items-center gap-2">
                                            <div class="avatar-circle-sm bg-primary text-white">{{ mb_substr($emp->name, 0, 1) }}</div>
                                            <span class="emp-name small fw-semibold text-dark">{{ $emp->name }}</span>
                                        </div>
                                        <div class="check-box-indicator">
                                            <i class="bi bi-check-circle-fill text-success fs-5 check-icon d-none"></i>
                                            <i class="bi bi-circle text-muted fs-5 uncheck-icon"></i>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">الموظفون المشتركون تظهر لهم المهمة في لوحة كانبان الخاصة بهم ويمكنك إضافتهم أو حذفهم بضغطة واحدة.</small>
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
@import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap');

/* Main Page Font Enforcement */
body, .tasks-dashboard-hero, .kanban-board-wrapper, .modal {
    font-family: 'IBM Plex Sans Arabic', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
}

/* Hero Header Styling */
.tasks-dashboard-hero {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #064e3b 0%, #065f46 45%, #0f766e 100%) !important;
    border-radius: 24px;
    padding: 36px 36px 32px;
    margin-bottom: 24px;
    box-shadow: 0 12px 36px -8px rgba(6, 78, 59, 0.35);
}

.hero-bg-accent-1 {
    position: absolute;
    top: -60px;
    right: -60px;
    width: 280px;
    height: 280px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(52, 211, 153, 0.25) 0%, transparent 70%);
    pointer-events: none;
}

.hero-bg-accent-2 {
    position: absolute;
    bottom: -80px;
    left: 20%;
    width: 240px;
    height: 240px;
    border-radius: 50%;
    background: radial-gradient(circle, rgba(20, 184, 166, 0.22) 0%, transparent 70%);
    pointer-events: none;
}

.hero-watermark-icon {
    position: absolute;
    left: 45px;
    top: 50%;
    transform: translateY(-50%);
    font-size: 8.5rem;
    color: rgba(255, 255, 255, 0.1);
    pointer-events: none;
    line-height: 1;
}

.hero-title {
    font-size: 2.15rem;
    font-weight: 800;
    letter-spacing: -0.5px;
    color: #ffffff !important;
}

.hero-subtitle {
    font-size: 0.98rem;
    font-weight: 500;
    color: rgba(255, 255, 255, 0.9) !important;
    line-height: 1.6;
    max-width: 680px;
}

.hero-pill-trello {
    background: rgba(255, 255, 255, 0.16) !important;
    color: #ffffff !important;
    border: 1px solid rgba(255, 255, 255, 0.25) !important;
    border-radius: 50px;
    padding: 6px 14px;
    font-weight: 700;
    font-size: 0.82rem;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.hero-pill-secure {
    background: rgba(16, 185, 129, 0.25) !important;
    color: #a7f3d0 !important;
    border: 1px solid rgba(16, 185, 129, 0.4) !important;
    border-radius: 50px;
    padding: 6px 14px;
    font-weight: 700;
    font-size: 0.82rem;
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

/* Stat Glass Cards (High Contrast & Legibility) */
.hero-stat-card {
    background: rgba(255, 255, 255, 0.14) !important;
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.24) !important;
    border-radius: 16px;
    padding: 10px 18px;
    color: #ffffff !important;
    display: flex;
    align-items: center;
    gap: 12px;
    min-width: 145px;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}

.hero-stat-card:hover {
    background: rgba(255, 255, 255, 0.24) !important;
    border-color: rgba(255, 255, 255, 0.4) !important;
    transform: translateY(-3px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.18);
}

.hero-stat-icon-wrap {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.hero-stat-label {
    font-size: 0.78rem;
    color: rgba(255, 255, 255, 0.88) !important;
    font-weight: 600;
    margin-bottom: 2px;
}

.hero-stat-value {
    font-family: 'Outfit', 'IBM Plex Sans Arabic', sans-serif !important;
    font-size: 1.5rem;
    font-weight: 800;
    color: #ffffff !important;
    line-height: 1;
}

/* Action & Filter Bar */
.task-action-bar-card {
    border: 1px solid #e2e8f0 !important;
    border-radius: 18px !important;
    box-shadow: 0 4px 20px -4px rgba(0, 0, 0, 0.05) !important;
}

.search-input-group .form-control,
.task-action-bar-card select.form-select {
    background: #f8fafc !important;
    border: 1.5px solid #e2e8f0 !important;
    border-radius: 12px !important;
    font-size: 0.88rem !important;
    font-weight: 600 !important;
    color: #1e293b !important;
    padding: 8px 14px !important;
    transition: all 0.2s ease;
}

.search-input-group .form-control:focus,
.task-action-bar-card select.form-select:focus {
    border-color: #10b981 !important;
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.15) !important;
    background: #ffffff !important;
}

.view-toggle-group .btn-emerald-active {
    background: #065f46 !important;
    color: #ffffff !important;
    box-shadow: 0 2px 8px rgba(6, 95, 70, 0.25) !important;
}

.btn-add-task {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #ffffff !important;
    border: none !important;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
    font-weight: 700 !important;
    font-size: 0.92rem !important;
    transition: all 0.25s ease;
}

.btn-add-task:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(16, 185, 129, 0.45) !important;
    color: #ffffff !important;
}

/* Notice Bar */
.task-info-notice-bar {
    background: #f0fdf4;
    border: 1.5px solid #bbf7d0;
    border-radius: 16px;
    padding: 13px 20px;
    box-shadow: 0 2px 8px rgba(16, 185, 129, 0.05);
}

.notice-icon-circle {
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: #dcfce7;
    color: #059669;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
}

.notice-text {
    font-size: 0.9rem;
    font-weight: 500;
    color: #14532d;
    line-height: 1.6;
}

.text-emerald-dark {
    color: #065f46;
    font-weight: 700;
}

/* Kanban Board Styling */
.kanban-board-wrapper {
    overflow-x: auto;
    padding-bottom: 24px;
}

.kanban-columns-container {
    display: grid;
    grid-template-columns: repeat(4, minmax(310px, 1fr));
    gap: 24px;
    min-width: 1280px;
    align-items: start;
}

.kanban-column {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    padding: 18px;
    display: flex;
    flex-direction: column;
    min-height: 620px;
    box-shadow: 0 2px 10px rgba(0, 0, 0, 0.02);
}

.kanban-column-header {
    padding-bottom: 14px;
    border-bottom: 2px solid #e2e8f0;
    margin-bottom: 16px;
}

.kanban-column-header h6 {
    font-size: 1.05rem;
    font-weight: 800;
    color: #0f172a;
}

.kanban-column-dot {
    width: 11px;
    height: 11px;
    border-radius: 50%;
    display: inline-block;
    box-shadow: 0 0 0 3px rgba(0, 0, 0, 0.05);
}

.count-badge {
    font-family: 'Outfit', sans-serif !important;
    font-weight: 800;
    font-size: 0.85rem;
    padding: 3px 10px;
    border-radius: 50px;
}

.kanban-cards-list {
    display: flex;
    flex-direction: column;
    gap: 14px;
    flex-grow: 1;
    min-height: 200px;
    padding: 4px;
    transition: background-color 0.2s ease;
}

.kanban-cards-list.drag-over {
    background-color: rgba(16, 185, 129, 0.08);
    border: 2px dashed #10b981;
    border-radius: 16px;
}

/* Kanban Card */
.kanban-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 18px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
    cursor: grab;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    position: relative;
    user-select: none;
}

.kanban-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px -6px rgba(0, 0, 0, 0.09);
    border-color: #cbd5e1;
}

.kanban-card:active {
    cursor: grabbing;
}

.kanban-card.is-dragging {
    opacity: 0.35;
    transform: scale(0.97);
}

.kanban-card .card-title a {
    font-size: 0.98rem;
    font-weight: 700;
    color: #0f172a !important;
    line-height: 1.5;
    transition: color 0.2s ease;
}

.kanban-card .card-title a:hover {
    color: #059669 !important;
}

.kanban-card .card-description {
    font-size: 0.84rem;
    color: #64748b;
    line-height: 1.6;
    font-weight: 500;
}

.hover-primary:hover {
    color: #059669 !important;
}

/* Avatar Group styling */
.avatar-group {
    display: flex;
    align-items: center;
}

.avatar-circle {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Outfit', sans-serif !important;
    font-weight: 800;
    font-size: 0.75rem;
    border: 2px solid #ffffff;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.ms-n2 {
    margin-right: -8px;
}

/* ============================================================== */
/* EMPLOYEE MULTI-PICKER & CHIPS STYLING                          */
/* ============================================================== */
.employee-chips-box {
    background: #f8fafc;
    border: 1.5px dashed #cbd5e1;
    transition: all 0.2s ease;
}

.employee-chips-box:hover {
    border-color: #10b981;
}

.employee-chip {
    background: #ffffff;
    border: 1px solid #10b981;
    color: #065f46;
    border-radius: 50px;
    padding: 4px 10px 4px 6px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.82rem;
    font-weight: 700;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
    animation: fadeInChip 0.2s ease;
}

@keyframes fadeInChip {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

.employee-chip .chip-avatar {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.68rem;
    font-weight: 800;
}

.employee-chip .btn-remove-chip {
    background: rgba(239, 68, 68, 0.12);
    color: #dc2626;
    border: none;
    border-radius: 50%;
    width: 18px;
    height: 18px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s ease;
    padding: 0;
    line-height: 1;
}

.employee-chip .btn-remove-chip:hover {
    background: #dc2626;
    color: #ffffff;
    transform: scale(1.15);
}

.employee-picker-card {
    border: 1px solid #e2e8f0;
}

.employee-picker-item {
    cursor: pointer;
    transition: all 0.15s ease;
    border: 1px solid transparent;
}

.employee-picker-item:hover {
    background: #f1f5f9;
}

.employee-picker-item.is-selected {
    background: #ecfdf5 !important;
    border-color: #a7f3d0;
}

.employee-picker-item.is-selected .emp-name {
    color: #065f46 !important;
    font-weight: 700 !important;
}

.avatar-circle-sm {
    width: 26px;
    height: 26px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.72rem;
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

/* ============================================================== */
/* EMPLOYEE MULTI-PICKER CONTROLLER JAVASCRIPT                    */
/* ============================================================== */
window.employeePickers = window.employeePickers || {};

function initEmployeePicker(prefix) {
    const select = document.getElementById(prefix + 'Select');
    if (!select) return;

    window.employeePickers[prefix] = new Map();

    Array.from(select.options).forEach(opt => {
        if (opt.selected) {
            window.employeePickers[prefix].set(String(opt.value), opt.text);
        }
    });

    renderEmployeePicker(prefix);
}

function renderEmployeePicker(prefix) {
    const map = window.employeePickers[prefix];
    const container = document.getElementById(prefix + 'ChipsContainer');
    const placeholder = document.getElementById(prefix + 'Placeholder');
    const badge = document.getElementById(prefix + 'Badge');
    const select = document.getElementById(prefix + 'Select');
    const list = document.getElementById(prefix + 'List');

    if (!container || !select) return;

    // Remove existing rendered chips
    container.querySelectorAll('.employee-chip').forEach(el => el.remove());

    if (map.size === 0) {
        if (placeholder) placeholder.classList.remove('d-none');
        if (badge) badge.textContent = 'تم اختيار 0';
    } else {
        if (placeholder) placeholder.classList.add('d-none');
        if (badge) badge.textContent = 'تم اختيار ' + map.size;
    }

    // Synchronize native select
    const selectedIds = new Set(Array.from(map.keys()));
    Array.from(select.options).forEach(opt => {
        opt.selected = selectedIds.has(String(opt.value));
    });

    // Render chips
    map.forEach((name, id) => {
        const chip = document.createElement('div');
        chip.className = 'employee-chip';
        chip.innerHTML = `
            <div class="chip-avatar">${name.trim().charAt(0)}</div>
            <span>${name}</span>
            <button type="button" class="btn-remove-chip" title="حذف" onclick="event.stopPropagation(); removeEmployeePicker('${prefix}', '${id}')">
                <i class="bi bi-x"></i>
            </button>
        `;
        container.appendChild(chip);
    });

    // Synchronize checkmarks in list
    if (list) {
        list.querySelectorAll('.employee-picker-item').forEach(row => {
            const rowId = String(row.getAttribute('data-id'));
            const isSelected = selectedIds.has(rowId);
            const checkIcon = row.querySelector('.check-icon');
            const uncheckIcon = row.querySelector('.uncheck-icon');

            if (isSelected) {
                row.classList.add('is-selected');
                if (checkIcon) checkIcon.classList.remove('d-none');
                if (uncheckIcon) uncheckIcon.classList.add('d-none');
            } else {
                row.classList.remove('is-selected');
                if (checkIcon) checkIcon.classList.add('d-none');
                if (uncheckIcon) uncheckIcon.classList.remove('d-none');
            }
        });
    }
}

function toggleEmployeePicker(prefix, id, name) {
    const map = window.employeePickers[prefix];
    if (!map) return;

    const key = String(id);
    if (map.has(key)) {
        map.delete(key);
    } else {
        map.set(key, name);
    }
    renderEmployeePicker(prefix);
}

function removeEmployeePicker(prefix, id) {
    const map = window.employeePickers[prefix];
    if (!map) return;

    map.delete(String(id));
    renderEmployeePicker(prefix);
}

function clearAllEmployeePicker(prefix) {
    const map = window.employeePickers[prefix];
    if (!map) return;
    map.clear();
    renderEmployeePicker(prefix);
}

function filterEmployeePicker(prefix, query) {
    const list = document.getElementById(prefix + 'List');
    if (!list) return;

    const q = (query || '').toLowerCase().trim();
    list.querySelectorAll('.employee-picker-item').forEach(row => {
        const name = (row.getAttribute('data-name') || '').toLowerCase();
        if (!q || name.includes(q)) {
            row.classList.remove('d-none');
        } else {
            row.classList.add('d-none');
        }
    });
}

// Auto-initialize picker on DOM ready
document.addEventListener('DOMContentLoaded', function() {
    initEmployeePicker('createShared');
});
</script>

@endsection
