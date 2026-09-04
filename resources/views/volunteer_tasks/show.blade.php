@extends('layouts.app')
@section('content')
    <div class="container-fluid p-0">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4">
                    <div
                        class="card-header bg-transparent border-bottom-0 pt-4 pb-0 px-4 d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="fw-bold text-body mb-0">تفاصيل مهمة متطوع</h4>
                            <p class="text-muted small mt-1">عرض تفاصيل المهمة وحالتها.</p>
                        </div>
                        @if(request()->user() && request()->user()->roles->contains('key', 'admin'))
                            <a href="{{ route('volunteer-tasks.edit', $task) }}"
                                class="btn btn-primary px-4 rounded-pill shadow-sm">
                                <i class="bi bi-pencil me-1"></i> تعديل
                            </a>
                        @endif
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-8">
                                <label class="d-block text-secondary small text-uppercase mb-1">العنوان</label>
                                <div class="fw-bold fs-5">{{ $task->title }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="d-block text-secondary small text-uppercase mb-1">الحالة</label>
                                <div>
                                    @if($task->status == 'pending') <span
                                        class="badge bg-secondary-subtle text-secondary rounded-pill px-3">معلق</span>
                                    @elseif($task->status == 'in_progress') <span
                                        class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill px-3">جاري
                                        العمل</span>
                                    @elseif($task->status == 'done') <span
                                        class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3">مكتمل</span>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="d-block text-secondary small text-uppercase mb-1">اسم النشاط التطوعي</label>
                                <div class="fw-bold">{{ $task->volunteer_activity_name ?? '—' }}</div>
                            </div>

                            <div class="col-12">
                                <label class="d-block text-secondary small text-uppercase mb-1">الوصف</label>
                                <div class="p-3 bg-body-tertiary rounded-3 text-body">
                                    {{ $task->description ?? 'لا يوجد وصف' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="d-block text-secondary small text-uppercase mb-1">المتطوع (المكلّف)</label>
                                <div class="fw-bold">{{ $task->assignee?->name ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="d-block text-secondary small text-uppercase mb-1">المكلِّف (المشرف)</label>
                                <div class="fw-bold">{{ $task->assigner?->name ?? '—' }}</div>
                            </div>

                            <div class="col-md-6">
                                <label class="d-block text-secondary small text-uppercase mb-1">تاريخ الاستحقاق</label>
                                <div class="fw-bold">{{ $task->due_date?->format('Y-m-d') ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="d-block text-secondary small text-uppercase mb-1">الارتباط</label>
                                <div>
                                    @if($task->project) <span class="badge bg-primary">مشروع</span>
                                        {{ $task->project->name }}
                                    @elseif($task->campaign) <span class="badge bg-info">حملة</span>
                                        {{ $task->campaign->name }}{{ $task->campaign->season_year ? ' (' . $task->campaign->season_year . ')' : '' }}
                                    @elseif($task->guestHouse) <span class="badge bg-secondary">دار الضيافة</span>
                                        {{ $task->guestHouse->name }}
                                    @else <span class="text-muted">—</span> @endif
                                </div>
                            </div>

                            <div class="col-12 mt-4">
                                <h5 class="fw-bold text-body border-bottom pb-2 mb-3">تقييم الأداء</h5>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <div class="p-3 bg-body-tertiary rounded-3">
                                            <label class="d-block text-secondary small text-uppercase mb-1">التقييم</label>
                                            <div class="fw-bold fs-5">
                                                @if($task->rating)
                                                    <span class="text-warning">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="bi bi-star{{ $i <= $task->rating ? '-fill' : '' }}"></i>
                                                        @endfor
                                                    </span>
                                                @else
                                                    <span class="text-muted small">غير مقيم</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="p-3 bg-body-tertiary rounded-3">
                                            <label class="d-block text-secondary small text-uppercase mb-1">ملاحظات
                                                التقييم</label>
                                            <div class="text-body">{{ $task->evaluation_notes ?? 'لا توجد ملاحظات تقييم' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex justify-content-end align-items-center mt-4 pt-3 border-top">
                                <a href="{{ route('volunteer-tasks.index') }}"
                                    class="btn btn-secondary-subtle btn-lg px-4 rounded-pill text-secondary">رجوع
                                    للقائمة</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

