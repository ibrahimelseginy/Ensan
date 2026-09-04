@extends('layouts.app')
@section('content')
<div class="expense-system-container animate-fade-in">
    {{-- High-Tech Dashboard Header --}}
    <div class="premium-hero-card mb-4 overflow-hidden position-relative" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); border-radius: 24px;">
        <div class="row align-items-center p-5 position-relative" style="z-index: 2;">
            <div class="col-md-7">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill mb-3">
                    <i class="bi bi-cpu me-1"></i> مُحلل الذكاء الاصطناعي نشط
                </span>
                <h1 class="display-5 fw-bold text-white mb-2" style="color: #ffffff !important;">إدارة المصروفات الذكية</h1>
                <p class="text-white opacity-75 lead" style="color: rgba(255,255,255,0.8) !important;">تحليل ذكي للبيانات المالية وتتبع النفقات التشغيلية والخيرية بدقة فائقة.</p>
                <div class="d-flex gap-3 mt-4">
                    <a href="{{ route('expenses.create') }}" class="btn btn-primary btn-lg px-4 rounded-pill shadow-premium">
                        <i class="bi bi-plus-circle me-2"></i> إضافة مصروف جديد
                    </a>
                </div>
            </div>
            <div class="col-md-5 d-none d-md-block text-center text-md-end">
                <div class="main-stat-circle">
                    <div class="stat-value text-white h1 fw-bold mb-0" style="color: #ffffff !important;">{{ number_format($totalCurrentMonth, 0) }}</div>
                    <div class="stat-label text-white opacity-75" style="color: rgba(255,255,255,0.7) !important;">إجمالي الشهر الحالي</div>
                </div>
            </div>
        </div>
        {{-- Background Decorations --}}
        <div class="bg-decoration-circle circle-1"></div>
        <div class="bg-decoration-circle circle-2"></div>
    </div>

    {{-- Smart Insights Grid --}}
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="insight-card p-4">
                <div class="d-flex justify-content-between mb-3">
                    <div class="icon-box bg-success bg-opacity-10 text-success rounded-3">
                        <i class="bi bi-graph-up-arrow fs-4"></i>
                    </div>
                    @if($growth != 0)
                        <span class="badge {{ $growth > 0 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }} rounded-pill px-2">
                             {{ $growth > 0 ? '+' : '' }}{{ round($growth, 1) }}%
                        </span>
                    @endif
                </div>
                <h6 class="text-muted small fw-bold mb-1">نسبة التغير الشهري</h6>
                <div class="h3 fw-bold mb-0">{{ abs(round($growth, 1)) }}%</div>
                <div class="progress mt-3" style="height: 6px; border-radius: 10px; background: rgba(0,0,0,0.05);">
                    <div class="progress-bar bg-{{ $growth > 0 ? 'danger' : 'success' }}" style="width: 70%"></div>
                </div>
            </div>
        </div>
        @foreach($insights as $insight)
        <div class="col-xl-3 col-md-6">
            <div class="insight-card p-4 border-start border-4 border-{{ $insight['type'] }}">
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-{{ $insight['type'] }} bg-opacity-10 text-{{ $insight['type'] }} p-2 rounded-circle me-3">
                        <i class="bi bi-lightbulb fs-5"></i>
                    </div>
                    <span class="text-{{ $insight['type'] }} small fw-bold">تنبيه ذكي</span>
                </div>
                <p class="mb-0 fw-medium">{{ $insight['msg'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    <div class="row g-4 mb-4">
        {{-- Charts Section --}}
        <div class="col-xl-8">
            <div class="glass-container p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0">تحليل مقارنة الأشهر</h5>
                    <div class="btn-group btn-group-sm">
                        <button class="btn btn-outline-secondary px-3 active">آخر 6 شهور</button>
                        <button class="btn btn-outline-secondary px-3">منذ البداية</button>
                    </div>
                </div>
                <canvas id="monthlyComparisonChart" height="280"></canvas>
            </div>
        </div>
        
        {{-- Category View --}}
        <div class="col-xl-4">
            <div class="glass-container p-4 h-100">
                <h5 class="fw-bold mb-4 text-center">توزيع المصروفات</h5>
                <canvas id="categoryPieChart" height="280"></canvas>
                <div class="mt-4">
                    @foreach($categoryBreakdown as $cat => $val)
                        <div class="d-flex justify-content-between align-items-center mb-2 small">
                            <span><i class="bi bi-circle-fill me-2" style="font-size: 8px;"></i> {{ strtoupper($cat) }}</span>
                            <span class="fw-bold">{{ number_format($val) }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    {{-- Advanced Filter --}}
    <div class="glass-container p-4 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-12 mb-2">
                <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i> فلاتر البحث المتقدمة</h6>
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">من تاريخ</label>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold text-muted">إلى تاريخ</label>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">التوع</label>
                <select name="type" class="form-select">
                    <option value="">كافة الأنواع</option>
                    <option value="operational" @selected(request('type') == 'operational')>تشغيلي</option>
                    <option value="aid" @selected(request('type') == 'aid')>مساعدات</option>
                    <option value="logistics" @selected(request('type') == 'logistics')>لوجستيات</option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill fw-bold rounded-pill" onclick="window.location.href = window.location.pathname + '?' + serializeForm()">
                         تحديث النتائج
                    </button>
                    <button class="btn btn-dark px-4 rounded-pill" onclick="window.location.href='{{ route('expenses.index') }}'">
                        رست
                    </button>
                    <a href="{{ route('expenses.export', request()->query()) }}" class="btn btn-outline-dark rounded-pill">
                        <i class="bi bi-download"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Data Table --}}
    <div class="glass-container h-100 overflow-hidden p-0">
        <div class="p-4 border-bottom bg-light bg-opacity-10 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0">سجل العمليات الأخير</h5>
            <span class="badge bg-primary px-3 rounded-pill">{{ $expenses->total() }} عملية</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light bg-opacity-50">
                    <tr>
                        <th class="py-3 px-4">العملية</th>
                        <th class="py-3">النوع والتصنيف</th>
                        <th class="py-3">المبلغ</th>
                        <th class="py-3">المشروع / الحملة</th>
                        <th class="py-3">التاريخ</th>
                        <th class="py-3 px-4 text-end">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($expenses as $e)
                    <tr class="animate-row {{ $e->status === 'cancelled' ? 'cancelled-row' : '' }}" style="{{ $e->status === 'cancelled' ? 'background: rgba(239, 68, 68, 0.05);' : '' }}">
                        <td class="px-4">
                            <div class="d-flex align-items-center">
                                <div class="p-2 rounded-3 me-3" style="{{ $e->status === 'cancelled' ? 'background: rgba(239, 68, 68, 0.1); color: #ef4444;' : 'background: rgba(var(--primary-rgb), 0.1); color: var(--primary);' }}">
                                    <i class="bi {{ $e->status === 'cancelled' ? 'bi-x-circle' : ($e->type == 'operational' ? 'bi-building' : 'bi-heart') }} fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold {{ $e->status === 'cancelled' ? 'text-decoration-line-through opacity-50' : '' }}">{{ Str::limit($e->description, 40) }}</div>
                                    <div class="text-muted small">#EX-{{ $e->id }} @if($e->status === 'cancelled') <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.65rem;">ملغي</span> @endif</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="badge bg-light text-dark border p-2 px-3 rounded-pill">{{ $e->type }}</div>
                            <div class="small text-muted mt-1">{{ $e->category }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-danger h5 mb-0">{{ number_format($e->amount, 2) }}</div>
                            <small class="text-muted">{{ $e->currency ?? 'EGP' }}</small>
                        </td>
                        <td>
                            <div class="small fw-bold">{{ $e->project?->name ?? 'غير محدد' }}</div>
                            <div class="text-muted small">{{ $e->campaign?->name ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="small">{{ optional($e->created_at)->format('Y-m-d') }}</div>
                            <div class="text-muted small opacity-50">{{ optional($e->created_at)->diffForHumans() }}</div>
                        </td>
                        <td class="px-4 text-end">
                            <button class="btn btn-link link-dark p-0" data-bs-toggle="dropdown" data-bs-boundary="viewport"><i class="bi bi-three-dots-vertical fs-5"></i></button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-premium border-0">
                                <li><a class="dropdown-item py-2" href="{{ route('expenses.show', $e) }}"><i class="bi bi-eye me-2"></i> تفاصيل</a></li>
                                    @if(auth()->check())
                                        @if($e->status !== 'cancelled')
                                            <li><a class="dropdown-item py-2 text-warning" href="{{ route('expenses.edit', $e) }}"><i class="bi bi-pencil me-2"></i> طلب تعديل</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-warning py-2" onclick="openCancelModal('{{ $e->id }}', true)">
                                                    <i class="bi bi-x-circle me-2"></i> طلب إلغاء
                                                </button>
                                            </li>
                                        @endif
                                    @endif
                            </ul>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $expenses->links() }}</div>
</div>


<style>
    .glass-container {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 20px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    
    .insight-card {
        background: #fff;
        border-radius: 18px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.03);
        transition: transform 0.3s ease;
    }
    
    .insight-card:hover { transform: translateY(-5px); }
    
    .main-stat-circle {
        width: 180px;
        height: 180px;
        border: 8px solid rgba(255,255,255,0.05);
        border-top-color: #3b82f6;
        border-radius: 50%;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        animation: rotateGlow 5s infinite alternate;
    }

    .pulse-icon {
        width: 12px;
        height: 12px;
        background: #10b981;
        border-radius: 50%;
        box-shadow: 0 0 0 rgba(16, 185, 129, 0.4);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .bg-decoration-circle {
        position: absolute;
        border-radius: 50%;
        filter: blur(80px);
        z-index: 1;
    }
    .circle-1 { width: 300px; height: 300px; background: rgba(59, 130, 246, 0.1); top: -100px; right: -50px; }
    .circle-2 { width: 200px; height: 200px; background: rgba(244, 63, 94, 0.1); bottom: -50px; left: -20px; }

    .animate-row { opacity: 0; animation: fadeInRow 0.5s forwards; }
    @keyframes fadeInRow {
        to { opacity: 1; transform: translateY(0); }
    }

</style>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Monthly Comparison Chart ---
        const ctxComparison = document.getElementById('monthlyComparisonChart').getContext('2d');
        new Chart(ctxComparison, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_column($monthlyComparison, 'label')) !!},
                datasets: [{
                    label: 'المصروفات الشهرية',
                    data: {!! json_encode(array_column($monthlyComparison, 'total')) !!},
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 4,
                    tension: 0.4,
                    fill: true,
                    pointRadius: 6,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { 
                    y: { beginAtZero: true, grid: { borderDash: [5, 5] } },
                    x: { grid: { display: false } }
                }
            }
        });

        // --- Category Pie Chart ---
        const ctxPie = document.getElementById('categoryPieChart').getContext('2d');
        new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($categoryBreakdown->keys()) !!},
                datasets: [{
                    data: {!! json_encode($categoryBreakdown->values()) !!},
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                cutout: '70%',
                plugins: { legend: { display: false } }
            }
        });
    });

    function serializeForm() {
        let params = new URLSearchParams();
        document.querySelectorAll('input[name], select[name]').forEach(el => {
            if(el.value) params.set(el.name, el.value);
        });
        return params.toString();
    }

    function openCancelModal(id, isRequest = false) {
        const form = document.getElementById('cancelForm');
        form.action = `/expenses/${id}`;
        
        const title = document.querySelector('#cancelModal .modal-title');
        const bodyText = document.querySelector('#cancelModal .modal-body p');
        const btn = document.querySelector('#cancelModal button[type="submit"]');

        if (isRequest) {
            title.textContent = 'طلب إلغاء المصروف';
            title.className = 'modal-title text-warning';
            bodyText.textContent = 'هل أنت متأكد من إرسال طلب لإلغاء هذا المصروف؟ سيقوم المدير بمراجعة الطلب والموافقة عليه.';
            btn.textContent = 'إرسال الطلب';
            btn.className = 'btn btn-warning';
        } else {
            title.textContent = 'تأكيد إلغاء المصروف';
            title.className = 'modal-title text-danger';
            bodyText.textContent = 'هل أنت متأكد من رغبتك في إلغاء هذا المصروف؟ سيتم عكس العمليات المالية وتحديث حالة المصروف.';
            btn.textContent = 'تأكيد الإلغاء';
            btn.className = 'btn btn-danger';
        }

        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }
</script>

{{-- Cancellation Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-danger">تأكيد إلغاء المصروف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إلغاء هذا المصروف؟ سيتم عكس العمليات المالية وتحديث حالة المصروف.</p>
                <div class="mb-3">
                    <label for="reason" class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="يرجى توضيح سبب الإلغاء..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
            </div>
        </form>
    </div>
</div>
@endsection

