@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);">
        <div class="hero-content">
            <div class="hero-greeting">الموارد البشرية 📋</div>
            <h1 class="hero-title">التقييمات والمتابعة</h1>
            <p class="hero-subtitle">لوحة التحكم الخاصة بمتابعة أداء الموظفين والمتطوعين</p>
        </div>
        <i class="bi bi-clipboard-data-fill hero-icon d-none d-md-block"></i>
    </div>

    <!-- Statistics Section -->
    <div class="row g-4 mb-5">
        <!-- Tasks Stats -->
        <div class="col-md-3 animate-slide-up animate-delay-1">
            <div class="glass-card h-100 hover-lift">
                <a href="{{ route('tasks.index') }}" class="card-body p-4 text-decoration-none text-dark d-block">
                    <div class="d-flex align-items-center justify-content-between mb-4">
                        <h6 class="fw-bold mb-0 fs-5">إحصائيات المهام</h6>
                        <div class="icon-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                            style="width: 48px; height: 48px;">
                            <i class="bi bi-list-check fs-4"></i>
                        </div>
                    </div>
                    <div class="row g-2">
                        {{-- In RTL: Col 1 is Right (All), Col 2 Middle (Completed), Col 3 Left (Pending) --}}
                        <div class="col-4">
                            <div class="p-3 rounded-4 text-center h-100 d-flex flex-column justify-content-center shadow-sm" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                                <div class="small fw-bold mb-1 opacity-75" style="color: #3b82f6;">الكل</div>
                                <div class="h4 fw-bolder mb-0" style="color: #3b82f6;">{{ $tasksTotal }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-4 text-center h-100 d-flex flex-column justify-content-center shadow-sm" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                                <div class="small fw-bold mb-1 opacity-90" style="color: #10b981;">مكتملة</div>
                                <div class="h4 fw-bolder mb-0" style="color: #10b981;">{{ $tasksCompleted }}</div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="p-3 rounded-4 text-center h-100 d-flex flex-column justify-content-center shadow-sm" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                                <div class="small fw-bold mb-1 opacity-90" style="color: #f59e0b;">معلقة</div>
                                <div class="h4 fw-bolder mb-0" style="color: #f59e0b;">{{ $tasksPending }}</div>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>

        <!-- Attendance Stats -->
        <div class="col-md-3 animate-slide-up animate-delay-2">
            <div class="glass-card h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-calendar-check fs-4"></i>
                        </div>
                        <h6 class="fw-bold mb-0">الحضور (هذا الشهر)</h6>
                    </div>
                    <a href="{{ route('employee-attendance.index') }}" class="d-block text-decoration-none text-dark mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">الموظفين ({{ $totalEmployees }})</span>
                            <span class="fw-bold">{{ $employeeAttendanceCount }} <span
                                    class="small text-muted fw-normal">سجل</span></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar"
                                style="width: {{ $totalEmployees > 0 ? min(100, ($employeeAttendanceCount / ($totalEmployees * 22)) * 100) : 0 }}%"
                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </a>
                    <a href="{{ route('volunteer-attendance.index') }}" class="d-block text-decoration-none text-dark">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted small">المتطوعين ({{ $totalVolunteers }})</span>
                            <span class="fw-bold">{{ $volunteerAttendanceCount }} <span
                                    class="small text-muted fw-normal">سجل</span></span>
                        </div>
                        <div class="progress" style="height: 6px;">
                            <div class="progress-bar bg-info" role="progressbar"
                                style="width: {{ $totalVolunteers > 0 ? min(100, ($volunteerAttendanceCount / ($totalVolunteers * 4)) * 100) : 0 }}%"
                                aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Evaluations Stats -->
        <div class="col-md-3 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                <a href="{{ route('tasks.index') }}" class="card-body p-4 text-decoration-none text-dark d-block">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-star fs-4"></i>
                        </div>
                        <h6 class="fw-bold mb-0">جودة الأداء</h6>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="display-5 fw-bold text-dark me-2">{{ number_format($avgRating, 1) }}</div>
                        <div class="text-warning small">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="bi bi-star{{ $i <= round($avgRating) ? '-fill' : '' }}"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="text-center small text-muted">
                        تم تقييم {{ $ratedTasksCount }} من {{ $tasksCompleted }} مهمة مكتملة
                    </div>
                </a>
            </div>
        </div>

        <!-- Salaries Stats -->
        <div class="col-md-3 animate-slide-up animate-delay-4">
            <div class="glass-card h-100 hover-lift">
                <a href="{{ route('payrolls.index') }}" class="card-body p-4 text-decoration-none text-dark d-block">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-circle bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center me-3"
                            style="width: 50px; height: 50px;">
                            <i class="bi bi-cash fs-4"></i>
                        </div>
                        <h6 class="fw-bold mb-0">الرواتب (هذا الشهر)</h6>
                    </div>
                    <div class="text-center py-2">
                        <div class="h3 fw-bold text-dark mb-0">{{ number_format($totalSalaries) }}</div>
                        <div class="small text-muted">إجمالي المصروف</div>
                    </div>
                    <div class="text-center mt-2">
                        <span class="badge bg-secondary-subtle text-body border rounded-pill px-3">
                            {{ $employeesPaidCount }} موظف ({{ $totalSalariesCount }} عملية)
                        </span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <div class="row g-4">
        <!-- Payrolls Card -->
        <div class="col-md-4 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 hover-lift">
                <a href="{{ route('payrolls.index') }}"
                    class="card-body text-center p-5 text-decoration-none text-dark d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-circle bg-primary-subtle text-primary mb-4 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-cash-coin"></i>
                    </div>
                    <h4 class="card-title fw-bold mb-2">الرواتب</h4>
                    <p class="card-text text-muted mb-0">إدارة رواتب الموظفين ومتابعة المدفوعات الشهرية</p>
                </a>
            </div>
        </div>

        <!-- Attendance Card -->
        <div class="col-md-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 hover-lift">
                <div class="card-body text-center p-5 d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-circle bg-success-subtle text-success mb-4 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-calendar-check"></i>
                    </div>
                    <h4 class="card-title fw-bold mb-3">الحضور</h4>
                    <div class="d-grid gap-2 w-100">
                        <a href="{{ route('employee-attendance.index') }}"
                            class="btn btn-outline-success rounded-pill py-2">
                            <i class="bi bi-person-badge me-2"></i>الموظفين
                        </a>
                        <a href="{{ route('volunteer-attendance.index') }}"
                            class="btn btn-outline-info rounded-pill py-2">
                            <i class="bi bi-heart me-2"></i>المتطوعين
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tasks Card -->
        <div class="col-md-4 animate-slide-up animate-delay-7">
            <div class="glass-card h-100 hover-lift">
                <a href="{{ route('tasks.index') }}"
                    class="card-body text-center p-5 text-decoration-none text-dark d-flex flex-column align-items-center justify-content-center">
                    <div class="icon-circle bg-warning-subtle text-warning mb-4 rounded-circle d-flex align-items-center justify-content-center"
                        style="width: 80px; height: 80px; font-size: 2.5rem;">
                        <i class="bi bi-list-task"></i>
                    </div>
                    <h4 class="card-title fw-bold mb-2">المهام</h4>
                    <p class="card-text text-muted mb-0">متابعة المهام الموكلة وإنجازات الفريق</p>
                </a>
            </div>
        </div>
    </div>

    {{-- Official Employee Evaluations Table --}}
    <div class="card border-0 shadow-sm mt-5 mb-4 animate-slide-up">
        <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="mb-0 fw-bold text-dark">
                    <i class="bi bi-award-fill text-warning me-2"></i> تقييمات أداء الموظفين بالمنظومة
                </h5>
                <small class="text-muted">النموذج المعتمد لتقييم الأداء (10 بنود موزونة: إنجاز المهام 25%، التارجيت 15%، الحضور 20%، الجودة 10%، المواعيد 10%، أخرى 20%)</small>
            </div>
            <a href="{{ route('users.index') }}" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                <i class="bi bi-people me-1"></i> دليل الموظفين
            </a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start ps-4">الموظف</th>
                            <th>القسم / الإدارة</th>
                            <th>المسمى الوظيفي</th>
                            <th>المجموع (من 100)</th>
                            <th>النسبة المئوية</th>
                            <th>نتيجة التقييم والأداء</th>
                            <th>تاريخ آخر تقييم</th>
                            <th class="text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            @php
                                $eval = $emp->latestEvaluation;
                            @endphp
                            <tr>
                                <td class="text-start ps-4">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="avatar avatar-sm bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 36px; height: 36px;">
                                            {{ mb_substr($emp->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <a href="{{ route('users.show', $emp) }}" class="fw-bold text-dark text-decoration-none d-block">
                                                {{ $emp->name }}
                                            </a>
                                            <span class="x-small text-muted font-monospace">{{ $emp->code }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $emp->department ?: '—' }}</td>
                                <td>{{ $emp->job_title ?: '—' }}</td>
                                <td>
                                    @if($eval)
                                        <span class="fw-bolder font-monospace fs-6 text-primary">{{ number_format($eval->total_score, 1) }}</span>
                                        <span class="x-small text-muted">/ 100</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($eval)
                                        <span class="badge bg-light text-dark border font-monospace">{{ $eval->percentage }}%</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($eval)
                                        <span class="badge {{ $eval->getRatingBadgeClass() }} px-3 py-1 rounded-pill">
                                            {{ $eval->rating_label }}
                                        </span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">لم يُقيّم</span>
                                    @endif
                                </td>
                                <td>
                                    @if($eval && $eval->evaluation_date)
                                        <span class="small text-muted">{{ $eval->evaluation_date->format('Y-m-d') }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('users.show', $emp) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="bi bi-eye me-1"></i> فتح التقييم والملف
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-people display-6 d-block mb-2 opacity-50"></i>
                                    لا يوجد موظفون مسجلون حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

