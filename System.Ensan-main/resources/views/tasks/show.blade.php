@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-list-task text-primary"></i>
      تفاصيل المهمة
    </h4>
    <div class="btn-group">
      <a class="btn btn-outline-primary" href="{{ route('tasks.edit', $task) }}">
        <i class="bi bi-pencil me-1"></i> تعديل
      </a>
      <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i> رجوع
      </a>
    </div>
  </div>

  <div class="row g-4">
    {{-- Main Info Card --}}
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-info-circle"></i>
            <h5 class="mb-0">معلومات المهمة</h5>
          </div>

          <div class="row g-3">
            <div class="col-12">
              <div class="info-row">
                <span class="info-label">العنوان</span>
                <span class="info-value fw-bold fs-5">{{ $task->title }}</span>
              </div>
            </div>
            @if($task->volunteer_activity_name)
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">اسم النشاط التطوعي</span>
                  <span class="info-value">{{ $task->volunteer_activity_name }}</span>
                </div>
              </div>
            @endif
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">تاريخ الاستحقاق</span>
                <span class="info-value">{{ $task->due_date?->format('Y-m-d') ?? '—' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">الحالة</span>
                <span class="info-value">
                  @php
                    $statusClass = match ($task->status) {
                      'completed', 'done' => 'bg-success-subtle text-success',
                      'in_progress' => 'bg-primary-subtle text-primary',
                      'pending' => 'bg-warning-subtle text-warning',
                      default => 'bg-secondary-subtle text-secondary'
                    };
                    $statusText = match ($task->status) {
                      'completed', 'done' => 'منجزة',
                      'in_progress' => 'قيد التنفيذ',
                      'pending' => 'قيد الانتظار',
                      default => $task->status
                    };
                  @endphp
                  <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </span>
              </div>
            </div>
            <div class="col-12">
              <div class="info-row">
                <span class="info-label">الانتماء</span>
                <span class="info-value">
                  @if($task->project)
                    <i class="bi bi-folder text-primary me-1"></i> مشروع: <a
                      href="{{ route('projects.show', $task->project) }}">{{ $task->project->name }}</a>
                  @elseif($task->campaign)
                    <i class="bi bi-megaphone text-info me-1"></i> حملة:
                    {{ $task->campaign->name }}{{ $task->campaign->season_year ? ' (' . $task->campaign->season_year . ')' : '' }}
                  @elseif($task->guestHouse)
                    <i class="bi bi-house-heart text-success me-1"></i> دار الضيافة: {{ $task->guestHouse->name }}
                  @else
                    —
                  @endif
                </span>
              </div>
            </div>
            @if($task->description)
              <div class="col-12">
                <div class="p-3 bg-light rounded">
                  <div class="text-muted small mb-2">الوصف</div>
                  <div>{{ $task->description }}</div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
      {{-- Assignees Card --}}
      <div class="card mb-3">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-people"></i>
            <h6 class="mb-0">المسؤولون</h6>
          </div>

          <div class="mb-3">
            <div class="text-muted small mb-1">المكلّف بالمهمة</div>
            @if($task->assignee)
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm avatar-primary">{{ mb_substr($task->assignee->name, 0, 1) }}</div>
                <span class="fw-medium">{{ $task->assignee->name }}</span>
              </div>
            @else
              <span class="text-muted">—</span>
            @endif
          </div>

          <div>
            <div class="text-muted small mb-1">المكلِّف (المدير)</div>
            @if($task->assigner)
              <div class="d-flex align-items-center gap-2">
                <div class="avatar avatar-sm avatar-secondary">{{ mb_substr($task->assigner->name, 0, 1) }}</div>
                <span class="fw-medium">{{ $task->assigner->name }}</span>
              </div>
            @else
              <span class="text-muted">—</span>
            @endif
          </div>
        </div>
      </div>

      {{-- Quick Actions --}}
      <div class="card border-0 shadow-sm overflow-hidden" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(10px);">
        <div class="card-body p-4">
          <div class="d-flex align-items-center gap-2 mb-4">
            <i class="bi bi-lightning-fill text-warning"></i>
            <h6 class="mb-0 fw-bold">إجراءات سريعة</h6>
          </div>
          <div class="d-grid gap-3">
            <a href="{{ route('tasks.edit', $task) }}" class="btn btn-success btn-lg d-flex align-items-center justify-content-center gap-2 shadow-sm border-0" style="background-color: #2ecc71;">
                <i class="bi bi-pencil-square"></i>
                <span class="fw-bold">تعديل المهمة</span>
            </a>
            
            <form method="POST" action="{{ route('tasks.destroy', $task) }}" class="d-grid" onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟ سيتم إرسال طلب حذف للمراجعة.');">
                @csrf @method('DELETE')
                <button class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center gap-2 py-2" style="border-color: #2c3e50; color: #fff; background: rgba(255,255,255,0.05);">
                    <i class="bi bi-trash"></i>
                    <span>حذف المهمة</span>
                </button>
            </form>

            <a href="{{ route('tasks.index') }}" class="btn btn-outline-secondary btn-lg d-flex align-items-center justify-content-center gap-2 py-2" style="border-color: #2c3e50; color: #fff; background: rgba(255,255,255,0.05);">
                <i class="bi bi-arrow-right-circle"></i>
                <span>رجوع للقائمة</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

