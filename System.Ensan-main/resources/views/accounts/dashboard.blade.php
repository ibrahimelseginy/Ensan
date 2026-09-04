@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    
    {{-- Hero Section --}}
    <div class="dashboard-hero animate-slide-up mb-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
        <div class="hero-content">
            <div class="hero-greeting">لوحة تحكم الحسابات 💰</div>
            <h1 class="hero-title">نظام الحسابات والقيود</h1>
            <p class="hero-subtitle">إدارة مالية ذكية ومتكاملة</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-calculator me-2"></i>{{ $totalAccounts }} حساب
             </div>
            <i class="bi bi-cash-stack hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights --}}
    @if(count($insights) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-success">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-success fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-success">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($insights as $insight)
                    <div class="col-md-6 col-lg-4">
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

    {{-- Financial Summary Cards --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <div class="glass-card h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-wallet2 fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">الأصول</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ number_format($assetBalance) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م</div>
                    <div class="mt-2 small" style="color: #9ca3af;">{{ $assetAccounts }} حساب</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-credit-card fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">الالتزامات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ number_format($liabilityBalance) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م</div>
                    <div class="mt-2 small" style="color: #9ca3af;">{{ $liabilityAccounts }} حساب</div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(59, 130, 246, 0.1);">
                            <i class="bi bi-graph-up-arrow fs-5" style="color: #3b82f6;"></i>
                        </div>
                         <div class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">الإيرادات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #3b82f6;">{{ number_format($revenueBalance) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م</div>
                    <div class="mt-2 small" style="color: #9ca3af;">{{ $revenueAccounts }} حساب</div>
                 </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-graph-down-arrow fs-5" style="color: #f59e0b;"></i>
                        </div>
                         <div class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">المصروفات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f59e0b;">{{ number_format($expenseBalance) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م</div>
                    <div class="mt-2 small" style="color: #9ca3af;">{{ $expenseAccounts }} حساب</div>
                 </div>
             </div>
        </div>
    </div>

    {{-- Net Income Card --}}
    <div class="row mb-4 animate-slide-up animate-delay-5">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba({{ $netIncome >= 0 ? '16, 185, 129' : '239, 68, 68' }}, 0.1); border: 2px solid rgba({{ $netIncome >= 0 ? '16, 185, 129' : '239, 68, 68' }}, 0.3);">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; background: linear-gradient(135deg, {{ $netIncome >= 0 ? '#10b981, #059669' : '#ef4444, #dc2626' }}); color: white;">
                                <i class="bi bi-{{ $netIncome >= 0 ? 'trophy' : 'exclamation-triangle' }} fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1" style="color: {{ $netIncome >= 0 ? '#10b981' : '#ef4444' }};">
                                    {{ $netIncome >= 0 ? 'صافي الربح' : 'صافي الخسارة' }}
                                </h5>
                                <p class="mb-0 small" style="color: #6b7280;">الإيرادات - المصروفات</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 text-md-end mt-3 mt-md-0">
                        <div class="h1 fw-bold mb-0" style="color: {{ $netIncome >= 0 ? '#10b981' : '#ef4444' }};">
                            {{ number_format(abs($netIncome)) }} ج.م
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        {{-- Revenue vs Expenses Chart --}}
        <div class="col-lg-8 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #10b981;"></i>الإيرادات والمصروفات (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="accountsChart"></canvas>
                </div>
            </div>
        </div>

        {{-- Journal Entries Stats --}}
        <div class="col-lg-4 animate-slide-up animate-delay-7">
            <div class="glass-card h-100 p-4">
                <h5 class="fw-bold mb-4"><i class="bi bi-journal-text me-2" style="color: #8b5cf6;"></i>القيود المحاسبية</h5>
                
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small" style="color: #9ca3af;">إجمالي القيود</span>
                        <span class="fw-bold" style="color: #8b5cf6;">{{ $totalEntries }}</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(139, 92, 246, 0.1);">
                        <div class="progress-bar" style="width: 100%; background: #8b5cf6;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small" style="color: #9ca3af;">قيود مقفلة</span>
                        <span class="fw-bold" style="color: #10b981;">{{ $lockedEntries }}</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(16, 185, 129, 0.1);">
                        <div class="progress-bar" style="width: {{ $totalEntries > 0 ? ($lockedEntries / $totalEntries * 100) : 0 }}%; background: #10b981;"></div>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small" style="color: #9ca3af;">قيود غير مقفلة</span>
                        <span class="fw-bold" style="color: #f59e0b;">{{ $unlockedEntries }}</span>
                    </div>
                    <div class="progress" style="height: 8px; background: rgba(245, 158, 11, 0.1);">
                        <div class="progress-bar" style="width: {{ $totalEntries > 0 ? ($unlockedEntries / $totalEntries * 100) : 0 }}%; background: #f59e0b;"></div>
                    </div>
                </div>

                <div class="p-3 rounded-4 text-center" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                    <div class="small mb-1" style="color: #6b7280;">قيود هذا الشهر</div>
                    <div class="h3 fw-bold mb-0" style="color: #3b82f6;">{{ $entriesThisMonth }}</div>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Accounts & Latest Entries --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6 animate-slide-up animate-delay-8">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-trophy me-2" style="color: #f59e0b;"></i>أكثر الحسابات نشاطاً</h5>
                <div class="list-group list-group-flush">
                    @forelse($topAccounts as $index => $account)
                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #10b981, #059669); color: white; font-weight: bold;">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $account->name }}</div>
                                <small style="color: #9ca3af;">{{ $account->code }} - {{ __('accounts.types.' . $account->type) }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color: #10b981;">{{ $account->lines_count ?? 0 }}</div>
                                <small style="color: #6b7280;">قيد</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد حسابات</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6 animate-slide-up animate-delay-9">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-clock-history me-2" style="color: #3b82f6;"></i>آخر القيود المحاسبية</h5>
                <div class="list-group list-group-flush" style="max-height: 400px; overflow-y: auto;">
                    @forelse($latestEntries as $entry)
                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div class="d-flex align-items-start">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: {{ $entry->locked ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                                <i class="bi bi-{{ $entry->locked ? 'lock' : 'unlock' }}" style="color: {{ $entry->locked ? '#10b981' : '#f59e0b' }};"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold small">{{ $entry->description ?? 'قيد محاسبي' }}</div>
                                <small style="color: #9ca3af;">{{ optional($entry->date)->format('Y-m-d') }} - {{ $entry->lines->count() }} سطر</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد قيود</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    {{-- Quick Actions --}}
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-10"><i class="bi bi-lightning-charge me-2" style="color: #10b981;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-11">
         <div class="col-md-2 col-4">
            <a href="{{ route('accounts.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-list-ul fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold small">كل الحسابات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('accounts.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-plus-circle fs-3 mb-2" style="color: #3b82f6;"></i>
                 <div class="fw-bold small">حساب جديد</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('journal-entries.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-journal-text fs-3 mb-2" style="color: #8b5cf6;"></i>
                 <div class="fw-bold small">القيود</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('journal-entries.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-plus-square fs-3 mb-2" style="color: #ec4899;"></i>
                 <div class="fw-bold small">قيد جديد</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('reports.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-file-earmark-bar-graph fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold small">التقارير</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('closures.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-calendar-check fs-3 mb-2" style="color: #06b6d4;"></i>
                 <div class="fw-bold small">الإقفالات</div>
            </a>
         </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const accountsCtx = document.getElementById('accountsChart');
    if(accountsCtx){
         new Chart(accountsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($trendLabels) !!},
                datasets: [{
                    label: 'الإيرادات (ج.م)',
                    data: {!! json_encode($revenueTrendData) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderRadius: 8
                },{
                    label: 'المصروفات (ج.م)',
                    data: {!! json_encode($expenseTrendData) !!},
                    backgroundColor: 'rgba(245, 158, 11, 0.6)',
                    borderColor: '#f59e0b',
                    borderWidth: 2,
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#9ca3af' } },
                    x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                }
            }
        });
    }
</script>
@endsection


