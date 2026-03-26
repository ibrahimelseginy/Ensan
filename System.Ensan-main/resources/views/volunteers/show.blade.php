@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <h4 class="mb-0">
                <i class="bi bi-person-heart text-primary"></i>
                ملف المتطوع
            </h4>
            <div class="btn-group">
                <a href="{{ route('volunteers.edit', $volunteer) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> تعديل
                </a>
                <a href="{{ route('volunteers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar / Profile Summary -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            @if($volunteer->profile_photo_path)
                                <img src="{{ $volunteer->image_url }}"
                                    class="rounded-circle img-thumbnail shadow-sm"
                                    style="width: 150px; height: 150px; object-fit: cover;" alt="{{ $volunteer->name }}">
                            @else
                                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold display-4"
                                    style="width: 120px; height: 120px;">
                                    {{ strtoupper(substr($volunteer->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="fw-bold mb-1">{{ $volunteer->name }}</h3>
                        <p class="text-muted mb-3">{{ $volunteer->email }}</p>

                        <div class="mb-4">
                            @if($volunteer->active)
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-check-circle me-1"></i> نشط</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-x-circle me-1"></i> غير نشط</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <a href="{{ route('volunteers.edit', $volunteer) }}" class="btn btn-primary"><i
                                    class="bi bi-pencil-square me-2"></i> تعديل البيانات</a>
                            <a href="{{ route('volunteers.index') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content / Details -->
            <div class="col-md-8">
                <!-- Personal Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i> المعلومات الشخصية
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">رقم الهاتف</label>
                                <div class="fw-medium fs-5">{{ $volunteer->phone ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">الكلية / التخصص</label>
                                <div class="fw-medium fs-5">{{ $volunteer->college ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">المحافظة</label>
                                <div class="fw-medium fs-5">{{ $volunteer->governorate ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">المدينة</label>
                                <div class="fw-medium fs-5">{{ $volunteer->city ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteering Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-briefcase me-2"></i> بيانات التطوع</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="text-muted small mb-1">جهة التطوع الحالية</label>
                                <div class="fw-medium fs-5">
                                    @if($volunteer->project)
                                        <span class="text-dark"><i class="bi bi-kanban text-info me-2"></i> مشروع:
                                            {{ $volunteer->project->name }}</span>
                                    @elseif($volunteer->campaign)
                                        <span class="text-dark"><i class="bi bi-megaphone text-warning me-2"></i> حملة:
                                            {{ $volunteer->campaign->name }}</span>
                                    @elseif($volunteer->guestHouse)
                                        <span class="text-dark"><i class="bi bi-house text-primary me-2"></i> دار ضيافة:
                                            {{ $volunteer->guestHouse->name }}</span>
                                    @else
                                        <span class="text-muted">غير معين لجهة محددة</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">الدور / المسمى التطوعي</label>
                                <div class="fw-medium fs-5">{{ $volunteer->project_role ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">ساعات التطوع الأسبوعية</label>
                                <div class="fw-medium fs-5">{{ $volunteer->volunteer_hours ?? 0 }} ساعة</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Tasks -->
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i> مهام المتطوعين</h5>
                        <div class="d-flex gap-2">
                            @if(request()->user()->hasPermission('volunteer_tasks.create'))
                                <a href="{{ route('volunteer-tasks.create', ['assigned_to' => $volunteer->id]) }}"
                                    class="btn btn-sm btn-primary rounded-pill"><i class="bi bi-plus-lg"></i> إضافة مهمة</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="px-4 py-3">المهمة</th>
                                        <th class="px-4 py-3">تاريخ الاستحقاق</th>
                                        <th class="px-4 py-3">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($volunteer->assignedTasks()->latest()->take(5)->get() as $task)
                                        <tr>
                                            <td class="px-4 fw-medium">{{ $task->title }}</td>
                                            <td class="px-4">{{ $task->due_date ? $task->due_date->format('Y-m-d') : '—' }}</td>
                                            <td class="px-4">
                                                @if($task->status == 'completed' || $task->status == 'done')
                                                    <span
                                                        class="badge bg-success-subtle text-success rounded-pill px-2">منجزة</span>
                                                @elseif($task->status == 'in_progress')
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2">قيد
                                                        التنفيذ</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2">قيد
                                                        الانتظار</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="bi bi-clipboard-x display-6 mb-2 d-block opacity-50"></i>
                                                لا توجد مهام مسندة حالياً
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i> سجل الحضور</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="px-4 py-3">التاريخ</th>
                                        <th class="px-4 py-3">الدخول</th>
                                        <th class="px-4 py-3">الخروج</th>
                                        <th class="px-4 py-3">التقييم</th>
                                        <th class="px-4 py-3">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($volunteer->volunteerAttendances()->latest()->take(5)->get() as $attendance)
                                        <tr>
                                            <td class="px-4">{{ $attendance->date->format('Y-m-d') }}</td>
                                            <td class="px-4">
                                                @if($attendance->check_in_at)
                                                    <span class="badge bg-success-subtle text-success rounded-pill fw-normal px-2">
                                                        {{ \Carbon\Carbon::parse($attendance->check_in_at)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($attendance->check_out_at)
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary rounded-pill fw-normal px-2">
                                                        {{ \Carbon\Carbon::parse($attendance->check_out_at)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($attendance->rating)
                                                    <span class="text-warning small" title="{{ $attendance->evaluation_notes }}">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="bi bi-star{{ $i <= $attendance->rating ? '-fill' : '' }}"></i>
                                                        @endfor
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 small text-muted">{{ Str::limit($attendance->notes ?? '—', 30) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-calendar-x display-6 mb-2 d-block opacity-50"></i>
                                                لا يوجد سجلات حضور حديثة
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


            </div>
        </div>
    </div>
@endsection
