@extends('layouts.app')
@section('content')
<div class="revenue-system-container animate-fade-in">
    {{-- Hero Section --}}
    <div class="premium-hero-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #059669 0%, #047857 100%); border-radius: 24px;">
        <div class="row align-items-center p-5 position-relative" style="z-index: 2;">
            <div class="col-md-7">
                <span class="badge bg-white bg-opacity-10 text-white px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-graph-up-arrow me-1"></i> التحليل المالي للإيرادات
                </span>
                <h1 class="display-5 fw-bold text-white mb-2">لوحة الإيرادات والتبرعات</h1>
                <p class="text-white text-opacity-75 lead">نظرة شاملة على مصادر الدخل، أنواع التبرعات، ونمط النمو المالي.</p>
                <div class="d-flex gap-3 mt-4">
                    <button class="btn btn-outline-light btn-lg px-4 rounded-pill shadow-premium fw-bold" onclick="window.print()">
                        <i class="bi bi-printer me-2"></i> طباعة التقرير
                    </button>
                    <!-- <a href="{{ route('donations.create') }}" class="btn btn-outline-light btn-lg px-4 rounded-pill">
                        <i class="bi bi-plus-lg me-2"></i> تسجيل تبرع
                    </a> -->
                </div>
            </div>
            <div class="col-md-5 d-none d-md-block text-center text-md-end">
                <div class="main-stat-circle" style="border-top-color: #fff;">
                    <div class="stat-value text-white h1 fw-bold mb-0">{{ number_format($totalRevenue, 0) }}</div>
                    <div class="stat-label text-white text-opacity-75">إجمالي الفترة</div>
                </div>
            </div>
        </div>
        <div class="bg-decoration-circle circle-1" style="background: rgba(255, 255, 255, 0.1);"></div>
        <div class="bg-decoration-circle circle-2" style="background: rgba(255, 255, 255, 0.05);"></div>
    </div>

    {{-- Filter Bar --}}
    <div class="glass-container p-4 mb-4">
        <form method="GET" action="{{ route('revenues.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small">من تاريخ</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold text-muted small">إلى تاريخ</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary w-100 fw-bold rounded-pill">
                        <i class="bi bi-funnel-fill me-2"></i> تحديث التحليل
                    </button>
                </div>
            </div>
        </form>
    </div>

    {{-- AI Insights --}}
    @if(count($insights) > 0)
    <div class="row g-4 mb-4">
        @foreach($insights as $insight)
        <div class="col-12">
            <div class="alert alert-{{ $insight['type'] }} border-0 shadow-sm rounded-4 d-flex align-items-center p-3 animate-slide-up">
                <i class="bi bi-{{ $insight['type'] == 'success' ? 'graph-up-arrow' : 'exclamation-triangle' }} fs-3 me-3"></i>
                <div>
                    <h6 class="fw-bold mb-1">تحليل الذكاء الاصطناعي</h6>
                    <p class="mb-0 small">{{ $insight['message'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    <div class="row g-4 mb-4">
        {{-- Charts: Types Breakdown --}}
        <div class="col-xl-6">
            <div class="glass-container p-4 h-100">
                <h5 class="fw-bold mb-4">مصادر الإيرادات (Source)</h5>
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <canvas id="sourceChart" height="200"></canvas>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            @foreach($revenueBySource as $label => $data)
                            <li class="list-group-item bg-transparent d-flex justify-content-between align-items-center border-0 px-0">
                                <span class="small fw-bold"><i class="bi bi-circle-fill me-2 text-success" style="font-size: 8px"></i> 
                                    {{ $label }}
                                </span>
                                <div class="text-end">
                                    <div class="fw-bold">{{ number_format($data['total']) }}</div>
                                    <small class="text-muted">{{ number_format($data['percentage'], 1) }}%</small>
                                </div>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        {{-- Charts: Type Breakdown (Money/Service/Items) --}}
        <div class="col-xl-6">
            <div class="glass-container p-4 h-100">
                <h5 class="fw-bold mb-4">طبيعة الدخل (Type)</h5>
                <canvas id="typeChart" height="250"></canvas>
            </div>
        </div>
    </div>
    
    <div class="row g-4 mb-4">
         {{-- Top Projects --}}
         <div class="col-md-6">
            <div class="glass-container p-4 h-100">
                <h5 class="fw-bold mb-3">أكثر المشاريع/الخدمات دخلاً</h5>
                @foreach($revenueByProject as $name => $total)
                <div class="mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="small fw-bold">{{ $name }}</span>
                        <span class="small fw-bold">{{ number_format($total) }}</span>
                    </div>
                    @php $pct = $totalRevenue > 0 ? ($total / $totalRevenue) * 100 : 0; @endphp
                    <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                @endforeach
            </div>
         </div>

         {{-- Recent Transactions --}}
         <div class="col-md-6">
            <div class="glass-container p-0 h-100 overflow-hidden">
                <div class="p-4 border-bottom bg-light bg-opacity-50">
                    <h5 class="fw-bold mb-0">أحدث العمليات المالية</h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <tbody>
                            @foreach($recentRevenues as $r)
                            <tr class="{{ isset($r['is_outflow']) ? 'bg-danger bg-opacity-10' : '' }}">
                                <td class="px-4 py-3">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-{{ $r['source'] == 'workspace' ? 'primary' : ($r['source'] == 'expense' ? 'danger' : 'success') }} bg-opacity-10 text-{{ $r['source'] == 'workspace' ? 'primary' : ($r['source'] == 'expense' ? 'danger' : 'success') }} p-2 rounded-circle me-3">
                                            <i class="bi bi-{{ $r['source'] == 'workspace' ? 'laptop' : ($r['source'] == 'expense' ? 'dash-circle' : 'cash-coin') }}"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $r['description'] }}</div>
                                            <div class="small text-muted">{{ \Carbon\Carbon::parse($r['date'])->format('Y-m-d') }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end px-4">
                                    <div class="fw-bold text-{{ isset($r['is_outflow']) ? 'danger' : 'success' }}">
                                        {{ isset($r['is_outflow']) ? '-' : '+' }}{{ number_format($r['amount']) }}
                                    </div>
                                    <span class="badge bg-light text-dark border">{{ $r['source_label'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
         </div>
    </div>

</div>

<style>
/* ... existing styles ... */
</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Data for charts
    const sourceData = {
        labels: {!! json_encode($revenueBySource->keys()) !!},
        datasets: [{
            data: {!! json_encode($revenueBySource->pluck('total')) !!},
            backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#8b5cf6', '#ec4899'],
            borderWidth: 0,
            hoverOffset: 4
        }]
    };

    const typeData = {
        labels: {!! json_encode($revenueByType->keys()->map(fn($t) => $t == 'money' ? 'نقدي' : ($t == 'service' ? 'خدمة (إيجار)' : ($t == 'items' ? 'عيني' : $t)))) !!},
        datasets: [{
            label: 'الإيرادات',
            data: {!! json_encode($revenueByType->pluck('total')) !!},
            backgroundColor: '#10b981',
            borderRadius: 8,
            barThickness: 30
        }]
    };

    // Render Source Chart (Pie)
    if(document.getElementById('sourceChart')) {
        new Chart(document.getElementById('sourceChart'), {
            type: 'doughnut',
            data: sourceData,
            options: {
                responsive: true,
                cutout: '60%',
                plugins: { legend: { display: false } }
            }
        });
    }

    // Render Type Chart (Bar)
    if(document.getElementById('typeChart')) {
        new Chart(document.getElementById('typeChart'), {
            type: 'bar',
            data: typeData,
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { borderDash: [4, 4] } },
                    x: { grid: { display: false } }
                }
            }
        });
    }
</script>
@endsection
