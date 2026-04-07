@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);">
        <div class="hero-content">
            <div class="hero-greeting">الموارد البشرية 📋</div>
            <h1 class="hero-title">لوحة التحكم المركزية HR</h1>
            <p class="hero-subtitle">تحليل الأداء، الرواتب، والحضور بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-cpu me-2"></i>تحليل ذكي
             </div>
            <i class="bi bi-person-lines-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(count($insights) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-info fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-info">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($insights as $insight)
                    <div class="col-md-6 col-lg-3">
                        <div class="alert alert-{{ $insight['type'] }} bg-opacity-10 border-0 mb-0 d-flex align-items-center py-2 px-3 rounded-3 h-100">
                            <i class="bi bi-{{ $insight['icon'] }} me-2 fs-5"></i>
                            <span class="small fw-semibold">{{ $insight['message'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Main Statistics Section -->
    <div class="row g-4 mb-4">
        <!-- 1. Workforce Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <div class="glass-card h-100 hover-lift relative overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                         <div class="badge bg-primary bg-opacity-10 text-primary">القوى العاملة</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                             <div class="h2 fw-bold mb-0 text-dark">{{ $totalEmployees + $totalVolunteers }}</div>
                             <small class="text-muted">إجمالي الفريق</small>
                        </div>
                        <div class="text-end small">
                             <div class="d-block"><span class="fw-bold text-dark">{{ $totalEmployees }}</span> <span class="text-muted">موظف</span></div>
                             <div class="d-block"><span class="fw-bold text-dark">{{ $totalVolunteers }}</span> <span class="text-muted">متطوع</span></div>
                        </div>
                    </div>
                </div>
                <div class="progress h-1 mt-auto rounded-0 bg-transparent">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ ($totalEmployees / max(1, $totalEmployees + $totalVolunteers)) * 100 }}%"></div>
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ ($totalVolunteers / max(1, $totalEmployees + $totalVolunteers)) * 100 }}%"></div>
                </div>
            </div>
        </div>

        <!-- 2. Attendance Health -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-heart-pulse-fill fs-5"></i>
                        </div>
                         <div class="badge bg-success bg-opacity-10 text-success">صحة الحضور</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="h2 fw-bold mb-0 text-dark me-2">{{ number_format($attendanceRate, 0) }}%</div>
                        @if($attendanceRate >= 85)
                            <small class="text-success fw-bold"><i class="bi bi-arrow-up-right"></i> ممتاز</small>
                        @elseif($attendanceRate >= 70)
                              <small class="text-warning fw-bold"><i class="bi bi-dash"></i> جيد</small>
                        @else
                             <small class="text-danger fw-bold"><i class="bi bi-arrow-down-right"></i> منخفض</small>
                        @endif
                    </div>
                    <div class="small text-muted mt-1">نسبة الالتزام بالحضور اليومي</div>
                     <div class="mt-3 small d-flex justify-content-between text-muted">
                        <span>{{ $employeeAttendanceCount }} سجل موظفين</span>
                        <span>{{ $volunteerAttendanceCount }} سجل متطوعين</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3. Pending Requests -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-inbox-fill fs-5"></i>
                        </div>
                         <div class="badge bg-warning bg-opacity-10 text-warning">طلبات معلقة</div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h3 fw-bold mb-0 text-dark">{{ $pendingLeaves }}</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">إجازات</small>
                        </div>
                        <div class="col-6">
                             <div class="h3 fw-bold mb-0 text-dark">{{ $tasksPending }}</div>
                             <small class="text-muted d-block" style="font-size: 0.75rem;">مهام</small>
                        </div>
                    </div>
                 </div>
            </div>
        </div>

        <!-- 4. Payroll Current -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                         <div class="badge bg-danger bg-opacity-10 text-danger">رواتب الشهر</div>
                    </div>
                    <div class="h3 fw-bold mb-0 text-start" dir="ltr">{{ number_format($totalSalaries) }}</div>
                    <div class="small text-muted text-end">تم صرفها لـ {{ $employeesPaidCount }} موظف</div>
                 </div>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Salary Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">اتجاه الرواتب (Salary Trend)</h5>
                    <button class="btn btn-sm btn-light border rounded-pill px-3">آخر 6 أشهر</button>
                </div>
                <div style="height: 300px;">
                    <canvas id="salaryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Top Performers -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 border-bottom bg-light bg-opacity-50 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0">نجوم الشهر 🌟</h5>
                    <small class="text-muted">الأعلى تقييماً</small>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($topPerformers as $user)
                    <div class="list-group-item bg-transparent border-bottom-0 py-3 d-flex align-items-center px-4">
                         <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">{{ $user->name }}</div>
                            <small class="text-muted">{{ $user->role_label ?? 'عضو فريق' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-warning"><i class="bi bi-star-fill me-1"></i>{{ number_format($user->avg_rating, 1) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        لا توجد تقييمات كافية هذا الشهر
                    </div>
                    @endforelse
                </div>
                 <div class="p-3 bg-light bg-opacity-10 text-center border-top">
                    <a href="{{ route('tasks.index') }}" class="text-decoration-none small fw-bold">عرض تقارير الأداء الكاملة <i class="bi bi-arrow-left"></i></a>
                 </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7">وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-3 col-6">
            <a href="{{ route('employee-tasks.create') }}" class="glass-card p-3 text-center text-decoration-none text-dark d-block hover-lift">
                 <i class="bi bi-plus-circle-fill text-primary fs-3 mb-2"></i>
                 <div class="fw-bold">مهمة جديدة</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('payrolls.create') }}" class="glass-card p-3 text-center text-decoration-none text-dark d-block hover-lift">
                 <i class="bi bi-cash-stack text-success fs-3 mb-2"></i>
                 <div class="fw-bold">صرف راتب</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('users.create') }}" class="glass-card p-3 text-center text-decoration-none text-dark d-block hover-lift">
                 <i class="bi bi-person-plus-fill text-info fs-3 mb-2"></i>
                 <div class="fw-bold">إضافة موظف</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('volunteers.create') }}" class="glass-card p-3 text-center text-decoration-none text-dark d-block hover-lift">
                 <i class="bi bi-heart-fill text-danger fs-3 mb-2"></i>
                 <div class="fw-bold">إضافة متطوع</div>
            </a>
         </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Salary Trend Chart
    const ctx = document.getElementById('salaryChart');
    if(ctx){
         new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($salaryTrendLabels) !!},
                datasets: [{
                    label: 'إجمالي الرواتب',
                    data: {!! json_encode($salaryTrendData) !!},
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#8b5cf6',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                         backgroundColor: '#1e293b',
                         padding: 12,
                         titleFont: { size: 13 },
                         bodyFont: { size: 13 },
                         rtl: true
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { borderDash: [5, 5], color: '#e2e8f0' },
                        ticks: { font: { family: 'Cairo' } }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo' } }
                    }
                }
            }
        });
    }
</script>
@endsection

