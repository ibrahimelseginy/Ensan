@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold text-dark mb-1">
        <i class="bi bi-list-check text-primary me-2"></i>مهام المتطوعين
      </h4>
    </div>
    <div>
      <a href="{{ route('tasks.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> إضافة مهمة
      </a>
    </div>
  </div>

  {{-- Tasks Table --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="table-responsive">
      <table class="table table-hover align-middle mb-0">
        <thead>
          <tr>
            <th>العنوان</th>
            <th>اسم التطوع</th>
            <th>الانتماء</th>
            <th>المكلّف</th>
            <th>المكلِّف</th>
            <th>تاريخ الاستحقاق</th>
            <th>الحالة</th>
            <th class="text-end">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          @forelse($tasks as $t)
            <tr>
              <td class="fw-medium">{{ $t->title }}</td>
              <td class="text-muted">{{ $t->volunteer_activity_name ?? '—' }}</td>
              <td>
                @if($t->project)
                  <span class="badge bg-primary-subtle text-primary">مشروع: {{ $t->project->name }}</span>
                @elseif($t->campaign)
                  <span class="badge bg-info-subtle text-info">حملة:
                    {{ $t->campaign->name }}{{ $t->campaign->season_year ? ' (' . $t->campaign->season_year . ')' : '' }}</span>
                @elseif($t->guestHouse)
                  <span class="badge bg-secondary-subtle text-secondary">دار ضيافة: {{ $t->guestHouse->name }}</span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>{{ $t->assignee?->name ?? '—' }}</td>
              <td>{{ $t->assigner?->name ?? '—' }}</td>
              <td>
                @if($t->due_date)
                  <span class="{{ $t->due_date < now() && $t->status != 'done' ? 'text-danger' : '' }}">
                    {{ optional($t->due_date)->format('Y-m-d') }}
                  </span>
                @else
                  <span class="text-muted">—</span>
                @endif
              </td>
              <td>
                @php
                  $statusClass = match ($t->status) {
                    'done' => 'bg-success-subtle text-success',
                    'in_progress' => 'bg-primary-subtle text-primary',
                    'pending' => 'bg-warning-subtle text-warning',
                    default => 'bg-secondary-subtle text-secondary'
                  };
                @endphp
                <span class="badge {{ $statusClass }}">{{ $t->status }}</span>
              </td>
              <td class="text-end">
                <div class="action-buttons">
                  <a class="btn btn-sm btn-outline-primary" href="{{ route('tasks.show', $t) }}">
                    <i class="bi bi-eye"></i>
                  </a>
                  <a class="btn btn-sm btn-outline-secondary" href="{{ route('tasks.edit', $t) }}">
                    <i class="bi bi-pencil"></i>
                  </a>
                  <form class="d-inline" method="POST" action="{{ route('tasks.destroy', $t) }}"
                    onsubmit="return confirm('حذف المهمة؟');">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="8" class="text-center py-5">
                <div class="empty-state">
                  <i class="bi bi-clipboard-x"></i>
                  <h5>لا توجد مهام مسجلة حالياً</h5>
                  <p>اضغط على "إضافة مهمة" لإنشاء مهمة جديدة</p>
                </div>
              </td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="mt-3">{{ $tasks->links() }}</div>
@endsection

