@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%);">
    <div class="hero-content">
        <div class="hero-greeting">إدارة المهام 📋</div>
        <h1 class="hero-title">مهام الموظفين</h1>
        <p class="hero-subtitle">متابعة وإدارة مهام فريق العمل</p>
        @if(request()->user() && request()->user()->roles->contains('key', 'admin'))
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('employee-tasks.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة مهمة
            </a>
        </div>
        @endif
    </div>
    <i class="bi bi-list-task hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr class="text-secondary small text-uppercase">
                            <th class="py-3 px-4">المهمة</th>
                            <th class="py-3 px-4">الموظف</th>
                            <th class="py-3 px-4">المكلِّف</th>
                            <th class="py-3 px-4">تاريخ الاستحقاق</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">التقييم</th>
                            <th class="py-3 px-4 text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tasks as $t)
                            <tr>
                                <td class="px-4 fw-medium">{{ $t->title }}</td>
                                <td class="px-4">
                                    @if($t->assignee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initials bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                style="width: 25px; height: 25px; font-size: 0.8rem;">
                                                {{ strtoupper(substr($t->assignee->name, 0, 1)) }}
                                            </div>
                                            <span>{{ $t->assignee->name }}</span>
                                        </div>
                                    @else
                                        <span class="text-muted small">غير معين</span>
                                    @endif
                                </td>
                                <td class="px-4 small text-muted">{{ $t->assigner?->name ?? '—' }}</td>
                                <td class="px-4">
                                    @if($t->due_date)
                                        <span
                                            class="{{ $t->due_date->isPast() && $t->status != 'done' ? 'text-danger fw-bold' : 'text-muted' }}">
                                            {{ $t->due_date->format('Y-m-d') }}
                                        </span>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td class="px-4">
                                    @if($t->status == 'pending') <span
                                        class="badge bg-secondary-subtle text-secondary rounded-pill px-3">معلق</span>
                                    @elseif($t->status == 'in_progress') <span
                                        class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">جاري
                                        العمل</span>
                                    @elseif($t->status == 'done') <span
                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">مكتمل</span>
                                    @endif
                                </td>
                                <td class="px-4">
                                    @if($t->rating)
                                        <span class="text-warning" title="{{ $t->evaluation_notes }}">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $t->rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </span>
                                    @else
                                        <span class="text-muted small">غير مقيم</span>
                                    @endif
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('employee-tasks.show', $t) }}"
                                            title="عرض"><i class="bi bi-eye"></i></a>
                                        @if(request()->user() && request()->user()->roles->contains('key', 'admin'))
                                            <a href="{{ route('employee-tasks.edit', $t->id) }}"
                                                class="btn btn-sm btn-outline-primary" data-bs-toggle="tooltip" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form action="{{ route('employee-tasks.destroy', $t->id) }}" method="POST"
                                                class="d-inline-block"
                                                onsubmit="return confirm('هل أنت متأكد من حذف هذه المهمة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" data-bs-toggle="tooltip"
                                                    title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-check display-4 mb-3 d-block opacity-50"></i>
                                    لا توجد مهام مسجلة حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($tasks->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $tasks->links() }}
            </div>
        @endif
    </div>

@endsection