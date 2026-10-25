@extends('layouts.app')
@section('content')

{{-- Task Detail Header --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #334155 100%);">
    <div class="hero-content">
        <div class="d-flex align-items-center gap-2 mb-2">
            <a href="{{ route('employee-tasks.index') }}" class="btn btn-sm btn-outline-light rounded-pill px-3">
                <i class="bi bi-arrow-right me-1"></i> العودة للوحة المهام
            </a>
            @php $pBadge = $task->priority_badge; @endphp
            <span class="badge bg-{{ $pBadge['color'] }} rounded-pill px-3 py-1">
                <i class="bi bi-{{ $pBadge['icon'] }} me-1"></i>{{ $pBadge['label'] }}
            </span>
            <span class="badge bg-success bg-opacity-25 text-success border border-success border-opacity-25 rounded-pill px-3 py-1">
                <i class="bi bi-shield-lock-fill me-1"></i> مهمة مؤمّنة ضد الحذف
            </span>
        </div>
        <h1 class="hero-title text-white fw-bold mb-1">{{ $task->title }}</h1>
        <p class="hero-subtitle text-light opacity-75 mb-0">
            تاريخ التكليف: {{ $task->created_at?->timezone('Africa/Cairo')->format('Y-m-d h:i A') }} • بواسطة: {{ $task->assigner?->name ?? 'المسؤول' }}
        </p>
    </div>
    <i class="bi bi-kanban hero-icon d-none d-md-block opacity-25" style="font-size: 6rem;"></i>
</div>

<div class="container-fluid py-4 animate-slide-up animate-delay-1">

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-4 border-0 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row g-4">

        {{-- LEFT COLUMN: Task Details, Progress Selector & Attachments --}}
        <div class="col-lg-8">

            {{-- 1. Status Update Control Card ("هو يعدل المهم وصلت لفين من الخيارات") --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-arrow-repeat text-primary me-2"></i> مرحلة وحالة المهمة الحالية
                        </h5>
                        @if($task->status === 'done')
                            <span class="badge bg-success-subtle text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-check2-circle me-1"></i> مكتملة ومراجعة (Done)
                            </span>
                        @elseif($task->status === 'under_review')
                            <span class="badge bg-warning-subtle text-warning border border-warning border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-search me-1"></i> قيد المراجعة والتدقيق
                            </span>
                        @elseif($task->status === 'in_progress')
                            <span class="badge bg-primary-subtle text-primary border border-primary border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-hourglass-split me-1"></i> قيد التنفيذ
                            </span>
                        @else
                            <span class="badge bg-secondary-subtle text-secondary border border-secondary border-opacity-25 rounded-pill px-3 py-2 fs-6">
                                <i class="bi bi-clipboard2 me-1"></i> قائمة المهام (To-Do)
                            </span>
                        @endif
                    </div>

                    {{-- Form to update status --}}
                    <form method="POST" action="{{ route('employee-tasks.update-status', $task) }}" class="bg-light p-3 rounded-4 border">
                        @csrf
                        <div class="row g-3 align-items-end">
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-muted">تحديث المرحلة إلى:</label>
                                <select name="status" class="form-select rounded-3">
                                    <option value="pending" {{ $task->status === 'pending' ? 'selected' : '' }}>📋 قائمة المهام (To-Do)</option>
                                    <option value="in_progress" {{ $task->status === 'in_progress' ? 'selected' : '' }}>⏳ قيد التنفيذ (In Progress)</option>
                                    <option value="under_review" {{ $task->status === 'under_review' ? 'selected' : '' }}>🔍 قيد المراجعة (Review)</option>
                                    <option value="done" {{ $task->status === 'done' ? 'selected' : '' }}>✅ مكتملة ومراجعة (Done)</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label small fw-bold text-muted">ملاحظات التحديث (تظهر في سجل التغييرات):</label>
                                <input type="text" name="status_note" class="form-control rounded-3" placeholder="ملاحظة إنجاز أو توضيح...">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100 rounded-3">
                                    <i class="bi bi-check-lg"></i> حفظ
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Review confirmation banner if done --}}
                    @if($task->status === 'done' && $task->reviewed_by)
                    <div class="alert alert-success border-0 rounded-4 mt-3 mb-0 d-flex align-items-center gap-3">
                        <i class="bi bi-patch-check-fill fs-2 text-success"></i>
                        <div>
                            <div class="fw-bold">تمت مراجعة واعتماد المهمة بنجاح (Done)</div>
                            <small class="text-muted">
                                المراجع المعتمد: <strong>{{ $task->reviewer?->name ?? 'المسؤول' }}</strong> في 
                                {{ $task->reviewed_at?->timezone('Africa/Cairo')->format('Y-m-d h:i A') }}
                            </small>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- 2. Task Description & Scope --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-dark mb-3">
                        <i class="bi bi-file-text text-primary me-2"></i> تفاصيل ووصف المهمة
                    </h5>
                    <div class="p-3 bg-light rounded-4 text-dark fs-6" style="line-height: 1.8; white-space: pre-line;">
                        {{ $task->description ?: 'لا يوجد وصف تفصيلي مضاف لهذه المهمة.' }}
                    </div>
                </div>
            </div>

            {{-- 3. File Attachments ("وتبقي فيت رفع ملفات") --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="bi bi-paperclip text-primary me-2"></i> المرفقات والملفات ({{ $task->attachments->count() }})
                        </h5>
                    </div>

                    {{-- Upload Area Form --}}
                    <form method="POST" action="{{ route('employee-tasks.upload-attachment', $task) }}" enctype="multipart/form-data" class="p-3 rounded-4 border mb-4" style="background: rgba(59, 130, 246, 0.03); border-color: rgba(59, 130, 246, 0.2) !important;">
                        @csrf
                        <div class="row g-2 align-items-center">
                            <div class="col-md-7">
                                <label class="form-label small fw-bold text-muted mb-1">رفع ملف جديد (إثبات إنجاز / مستند / صورة):</label>
                                <input type="file" name="file" required class="form-control form-control-sm rounded-3">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label small fw-bold text-muted mb-1">وصف الملف (اختياري):</label>
                                <input type="text" name="note" class="form-control form-control-sm rounded-3" placeholder="مثال: تقرير الزيارة...">
                            </div>
                            <div class="col-md-2 mt-auto">
                                <button type="submit" class="btn btn-sm btn-primary w-100 rounded-3">
                                    <i class="bi bi-cloud-arrow-up me-1"></i> رفع الآن
                                </button>
                            </div>
                        </div>
                    </form>

                    {{-- Uploaded Files List --}}
                    @if($task->attachments->count() > 0)
                    <div class="row g-3">
                        @foreach($task->attachments as $att)
                        <div class="col-md-6">
                            <div class="p-3 rounded-4 border bg-light d-flex align-items-center justify-content-between gap-3">
                                <div class="d-flex align-items-center gap-3 overflow-hidden">
                                    <div class="rounded-3 bg-white p-2 border d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; min-width: 44px;">
                                        @if($att->is_image)
                                            <i class="bi bi-image text-success fs-4"></i>
                                        @elseif(str_contains($att->mime_type ?? '', 'pdf'))
                                            <i class="bi bi-file-earmark-pdf text-danger fs-4"></i>
                                        @else
                                            <i class="bi bi-file-earmark-text text-primary fs-4"></i>
                                        @endif
                                    </div>
                                    <div class="overflow-hidden">
                                        <div class="fw-bold text-dark text-truncate" title="{{ $att->file_name }}" style="font-size: 0.88rem;">
                                            {{ $att->file_name }}
                                        </div>
                                        <small class="text-muted d-block" style="font-size: 0.72rem;">
                                            {{ $att->formatted_size }} • بواسطة {{ $att->user?->name ?? 'المستخدم' }}
                                        </small>
                                        <small class="text-muted d-block" style="font-size: 0.7rem;">
                                            {{ $att->created_at?->timezone('Africa/Cairo')->format('Y-m-d h:i A') }}
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="{{ route('employee-tasks.download-attachment', [$task, $att]) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="تحميل">
                                        <i class="bi bi-download"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-muted bg-light rounded-4">
                        <i class="bi bi-files fs-3 opacity-25 d-block mb-1"></i>
                        <span class="small">لا توجد مرفقات مضافة لهذه المهمة حتى الآن.</span>
                    </div>
                    @endif
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: Team, Dates, and Change Log / Audit Trail --}}
        <div class="col-lg-4">

            {{-- 1. Team & Assignments --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">
                        <i class="bi bi-people text-primary me-2"></i> فريق العمل والمكلفون
                    </h6>

                    {{-- Primary Assignee --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">المكلّف الرئيسي:</small>
                        @if($task->assignee)
                        <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light">
                            <div class="avatar-circle bg-primary text-white">
                                {{ mb_substr($task->assignee->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="fw-bold text-dark small">{{ $task->assignee->name }}</div>
                                <div class="text-muted" style="font-size: 0.7rem;">{{ $task->assignee->email }}</div>
                            </div>
                        </div>
                        @else
                        <span class="text-muted small">غير معين</span>
                        @endif
                    </div>

                    {{-- Shared Team ("المهام المشتركة") --}}
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">فريق العمل المشترك:</small>
                        @if($task->assignees->count() > 0)
                        <div class="d-flex flex-column gap-2">
                            @foreach($task->assignees as $collab)
                            <div class="d-flex align-items-center gap-2 p-2 rounded-3 bg-light">
                                <div class="avatar-circle bg-info text-white">
                                    {{ mb_substr($collab->name, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small">{{ $collab->name }}</div>
                                    <div class="text-muted" style="font-size: 0.7rem;">{{ $collab->email }}</div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <span class="text-muted small">لا يوجد موظفون إضافيون مشتركون</span>
                        @endif
                    </div>

                    {{-- Deadlines & Associations --}}
                    <div class="border-top pt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">تاريخ الاستحقاق:</span>
                            <span class="small fw-bold {{ $task->due_date?->isPast() && $task->status !== 'done' ? 'text-danger' : 'text-dark' }}">
                                {{ $task->due_date ? $task->due_date->format('Y-m-d') : '—' }}
                            </span>
                        </div>
                        @if($task->project)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">المشروع:</span>
                            <span class="badge bg-primary-subtle text-primary small">{{ $task->project->name }}</span>
                        </div>
                        @endif
                        @if($task->campaign)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="small text-muted">الحملة:</span>
                            <span class="badge bg-info-subtle text-info small">{{ $task->campaign->name }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- 2. Audit Trail & Change Log ("ويكون فيه سجل تغييرات يعني المهم تمت امتي من قبل فلان وتمت مراجعها وخلاص Done") --}}
            <div class="card border-0 shadow-sm rounded-4 bg-white">
                <div class="card-body p-4">
                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2 d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-clock-history text-primary me-2"></i> سجل التغييرات والمتابعة</span>
                        <span class="badge bg-light text-muted border">{{ $task->logs->count() }}</span>
                    </h6>

                    <div class="task-timeline">
                        @forelse($task->logs as $log)
                        <div class="timeline-item">
                            <div class="timeline-dot bg-primary"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span class="fw-bold text-dark small">{{ $log->action_title }}</span>
                                    <span class="text-muted" style="font-size: 0.7rem;">
                                        {{ $log->created_at?->timezone('Africa/Cairo')->format('Y-m-d h:i A') }}
                                    </span>
                                </div>
                                <p class="small text-muted mb-1" style="font-size: 0.78rem; line-height: 1.5;">
                                    {{ $log->details }}
                                </p>
                                <small class="text-primary fw-semibold" style="font-size: 0.7rem;">
                                    <i class="bi bi-person me-1"></i> {{ $log->user?->name ?? 'النظام' }}
                                </small>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-journal-text fs-3 opacity-25 d-block mb-1"></i>
                            <span class="small">لا توجد سجلات تغييرات حتى الآن.</span>
                        </div>
                        @endforelse
                    </div>

                </div>
            </div>

        </div>

    </div>

</div>

<style>
/* Task Timeline Styling */
.task-timeline {
    position: relative;
    padding-right: 20px;
}

.task-timeline::before {
    content: '';
    position: absolute;
    top: 5px;
    bottom: 5px;
    right: 6px;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-dot {
    position: absolute;
    top: 4px;
    right: -20px;
    width: 14px;
    height: 14px;
    border-radius: 50%;
    border: 3px solid #ffffff;
    box-shadow: 0 0 0 2px #3b82f6;
}

.timeline-content {
    background: #f8fafc;
    border: 1px solid #edf2f7;
    border-radius: 12px;
    padding: 12px 14px;
}

.avatar-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.8rem;
    box-shadow: 0 1px 3px rgba(0,0,0,0.1);
}
</style>

@endsection
