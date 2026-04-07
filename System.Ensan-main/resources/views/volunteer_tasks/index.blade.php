@extends('layouts.app')
@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-list-task text-primary me-2"></i>مهام المتطوعين
            </h4>
        </div>
        @if(request()->user() && request()->user()->roles->contains('key', 'admin'))
            <div>
                <a href="{{ route('volunteer-tasks.create') }}" class="btn btn-primary shadow-sm">
                    <i class="bi bi-plus-lg me-1"></i> إضافة مهمة
                </a>
            </div>
        @endif
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr class="text-secondary small text-uppercase">
                            <th class="py-3 px-4">المهمة</th>
                            <th class="py-3 px-4">النشاط التطوعي</th>
                            <th class="py-3 px-4">الانتماء</th>
                            <th class="py-3 px-4">المتطوع</th>
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
                                <td class="px-4 text-muted">{{ $t->volunteer_activity_name ?? '—' }}</td>
                                <td class="px-4">
                                    @if($t->project) <span
                                        class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill">مشروع:
                                        {{ Str::limit($t->project->name, 15) }}</span>
                                    @elseif($t->campaign) <span
                                        class="badge bg-info-subtle text-info border border-info-subtle rounded-pill">حملة:
                                        {{ Str::limit($t->campaign->name, 15) }}</span>
                                    @elseif($t->guestHouse) <span
                                        class="badge bg-secondary-subtle text-secondary border border-secondary-subtle rounded-pill">دار:
                                        {{ Str::limit($t->guestHouse->name, 15) }}</span>
                                    @else <span class="text-muted">—</span> @endif
                                </td>
                                <td class="px-4">
                                    @if($t->assignee)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initials bg-success-subtle text-success rounded-circle me-2 d-flex align-items-center justify-content-center"
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
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('volunteer-tasks.show', $t) }}"
                                            title="عرض"><i class="bi bi-eye"></i></a>
                                        @if(request()->user() && request()->user()->roles->contains('key', 'admin'))
                                            <a class="btn btn-sm btn-outline-secondary"
                                                href="{{ route('volunteer-tasks.edit', $t) }}" title="تعديل"><i
                                                    class="bi bi-pencil"></i></a>
                                            <form class="d-inline" method="POST" action="{{ route('volunteer-tasks.destroy', $t) }}"
                                                onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-sm btn-outline-danger" title="حذف"><i
                                                        class="bi bi-trash"></i></button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
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

