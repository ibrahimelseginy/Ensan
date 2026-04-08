@extends('layouts.app')

@section('styles')
<style>
    /* Premium Bar Chart Enhancements */
    .premium-bar-chart, .bar-grid {
        display: flex;
        align-items: flex-end;
        justify-content: space-around;
        height: auto;
        min-height: 250px;
        padding-bottom: 5px;
        border-bottom: 1px solid rgba(0,0,0,0.05);
        gap: 8px;
        position: relative;
    }
    
    .theme-dark .premium-bar-chart, .theme-dark .bar-grid {
        border-bottom-color: rgba(255,255,255,0.1);
    }

    .bar-group {
        flex: 1;
        display: flex;
        align-items: flex-end;
        justify-content: center;
        gap: 3px;
        height: 100%;
        position: relative;
        padding: 0 2px;
    }

    .stat-card {
        padding: 1.5rem;
        border-radius: var(--radius-lg);
        position: relative;
        overflow: hidden;
        transition: var(--transition-smooth);
        display: flex;
        flex-direction: column;
        border: 1px solid var(--border);
        height: 100%;
        background: var(--bg-card);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-premium) !important;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
        transition: var(--transition-smooth);
        position: relative;
        z-index: 2;
    }

    .stat-label {
        font-size: 0.85rem;
        color: var(--gray-600);
        font-weight: 600;
        margin-bottom: 0.25rem;
        position: relative;
        z-index: 2;
    }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--dark);
        position: relative;
        z-index: 2;
    }

    .stat-bg-icon {
        position: absolute;
        bottom: -10px;
        left: -10px;
        font-size: 5rem;
        opacity: 0.05;
        transform: rotate(-15deg);
        transition: var(--transition-smooth);
    }

    .stat-card:hover .stat-bg-icon {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.1;
    }

    /* Variant Colors with Mesh Gradients */
    .stat-info .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-success .stat-icon { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .stat-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stat-danger .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .stat-purple .stat-icon { background: rgba(168, 85, 247, 0.1); color: #a855f7; }

    /* Bar Chart High-End Styling */
    .bar {
        width: 12px !important;
        border-radius: 6px 6px 0 0;
        background: var(--primary);
        transition: var(--transition-smooth);
        position: relative;
    }

    .bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 30%;
        background: linear-gradient(180deg, rgba(255,255,255,0.4) 0%, rgba(255,255,255,0) 100%);
        border-radius: 6px 6px 0 0;
    }

    .bar.cash { 
        background: linear-gradient(180deg, #22c55e 0%, #16a34a 100%) !important;
        box-shadow: 0 4px 12px rgba(34, 197, 94, 0.2);
    }
    .bar.kind { 
        background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%) !important;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.2);
    }

    .bar-labels {
        display: flex;
        justify-content: space-around;
        margin-top: 15px;
        padding-top: 5px;
        margin-bottom: 20px;
    }

    .bar-labels span {
        flex: 1;
        text-align: center;
        font-size: 0.75rem;
        color: var(--gray-600);
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        transform: rotate(0deg); /* Reset generic rotation */
    }
    
    @media (max-width: 768px) {
         .bar-labels span {
             font-size: 0.6rem;
             transform: rotate(-45deg); /* Slant labels on mobile */
             white-space: nowrap;
             text-align: right;
             margin-top: 5px;
         }
         .premium-bar-chart, .bar-grid {
             min-height: 180px;
         }
    }

    .theme-dark .bar-labels span {
        color: var(--gray-400);
    }

    /* Fixed overlapping background issue */
    .chart-container, .chart-wrap {
        position: relative;
        overflow: hidden; /* Changed from visible to hidden to constrain content */
        background: var(--bg-card);
        border-radius: var(--radius-lg);
        padding: 1.5rem !important;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        height: 100%; /* Ensure it fills the column */
        min-height: auto; /* Let content dictate height if needed, or row stretch */
    }

    /* Dropdown UI fix - handled by table-responsive scrolling now */
    .table-responsive {
        padding-bottom: 0;
    }
    
    .dropdown-menu {
        z-index: 1060 !important;
        box-shadow: var(--shadow-xl) !important;
    }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>
@endsection

@section('content')

  @if($isAdmin ?? false)
    {{-- Premium Dashboard Hero --}}
    @php
        $hour = (int) date('H');
        $greeting = $hour < 12 ? 'صباح الخير' : ($hour < 18 ? 'مساء الخير' : 'مساء الخير');
    @endphp
    <div class="dashboard-hero animate-slide-up">
        <div class="hero-content">
            <div class="hero-greeting">{{ $greeting }} 👋</div>
            <h1 class="hero-title">مرحباً بك {{ $user->name }}</h1>
            <p class="hero-subtitle">إحصائيات وأداء المؤسسة لليوم {{ date('Y-m-d') }} — لوحة تحكم المسؤول</p>
            <div class="hero-actions d-flex gap-2">
                <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-person-circle me-1"></i> ملفي الشخصي
                </a>
                <a href="{{ route('reports.index') }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-file-earmark-bar-graph me-1"></i> التقارير
                </a>
                <button class="btn btn-sm rounded-pill px-4" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i> طباعة
                </button>
            </div>
        </div>
        <i class="bi bi-speedometer2 hero-icon d-none d-md-block"></i>
    </div>

    {{-- Primary Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg animate-slide-up animate-delay-1">
            <a href="{{ route('donors.index') }}" class="stat-card stat-info text-decoration-none">
                <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
                <div class="stat-label">المتبرعون</div>
                <div class="stat-value">{{ number_format($donorsCount) }}</div>
                <i class="bi bi-people-fill stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-2">
            <a href="{{ route('beneficiaries.index') }}" class="stat-card stat-success text-decoration-none">
                <div class="stat-icon"><i class="bi bi-heart-fill"></i></div>
                <div class="stat-label">المستفيدون</div>
                <div class="stat-value">{{ number_format($beneficiariesCount) }}</div>
                <i class="bi bi-heart-fill stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-3">
            <a href="{{ route('warehouses.index') }}" class="stat-card stat-warning text-decoration-none">
                <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
                <div class="stat-label">المخازن</div>
                <div class="stat-value">{{ number_format($warehousesCount) }}</div>
                <i class="bi bi-box-seam-fill stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-4">
            <a href="{{ route('volunteers.index') }}" class="stat-card stat-purple text-decoration-none">
                <div class="stat-icon"><i class="bi bi-hand-thumbs-up-fill"></i></div>
                <div class="stat-label">المتطوعون</div>
                <div class="stat-value">{{ number_format($volunteersCount) }}</div>
                <i class="bi bi-hand-thumbs-up-fill stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-5">
            <a href="{{ route('users.index') }}?type=employee" class="stat-card stat-danger text-decoration-none">
                <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="stat-label">الموظفون</div>
                <div class="stat-value">{{ number_format($employeesCount) }}</div>
                <i class="bi bi-person-badge-fill stat-bg-icon"></i>
            </a>
        </div>
    </div>

    {{-- Financial Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 animate-slide-up animate-delay-1">
            <a href="{{ route('donations.index') }}" class="stat-card stat-success text-decoration-none">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">تبرعات نقدية (الشهر)</div>
                <div class="stat-value">{{ number_format($cashMonth, 0) }}</div>
                <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> هذا الشهر</div>
                <i class="bi bi-cash-stack stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-md-3 animate-slide-up animate-delay-2">
            <a href="{{ route('donations.index') }}" class="stat-card stat-warning text-decoration-none">
                <div class="stat-icon"><i class="bi bi-gift-fill"></i></div>
                <div class="stat-label">تبرعات عينية (الشهر)</div>
                <div class="stat-value">{{ number_format($inKindMonth, 0) }}</div>
                <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> هذا الشهر</div>
                <i class="bi bi-gift-fill stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-md-3 animate-slide-up animate-delay-3">
            <a href="{{ route('expenses.index') }}" class="stat-card stat-danger text-decoration-none">
                <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
                <div class="stat-label">المصروفات (الشهر)</div>
                <div class="stat-value">{{ number_format($expensesMonth, 0) }}</div>
                <div class="stat-trend down"><i class="bi bi-arrow-down-short"></i> مصروف</div>
                <i class="bi bi-wallet2 stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-md-3 animate-slide-up animate-delay-4">
            <a href="{{ route('reports.index') }}" class="stat-card stat-primary text-decoration-none">
                <div class="stat-icon"><i class="bi bi-graph-up-arrow"></i></div>
                <div class="stat-label">صافي التدفق</div>
                <div class="stat-value {{ $netFlowMonth >= 0 ? 'text-success' : 'text-danger' }}">{{ number_format($netFlowMonth, 0) }}</div>
                <div class="stat-trend {{ $netFlowMonth >= 0 ? 'up' : 'down' }}">
                    <i class="bi bi-{{ $netFlowMonth >= 0 ? 'arrow-up' : 'arrow-down' }}-short"></i> الرصيد الصافي
                </div>
                <i class="bi bi-graph-up-arrow stat-bg-icon"></i>
            </a>
        </div>
    </div>

    @php $maxVal = max(array_merge($cashSeries, $inKindSeries));
    $maxVal = $maxVal > 0 ? $maxVal : 1; @endphp
    
    {{-- Charts Row --}}
    <div class="row g-4 mb-4">
      <div class="col-lg-8">
        <div class="chart-container h-100">
          <div class="chart-header">
            <h5 class="chart-title"><i class="bi bi-bar-chart-line-fill"></i> توزيع التبرعات آخر 12 شهرًا</h5>
            <div class="chart-legend">
              <span class="legend-item"><span class="legend-dot" style="background: linear-gradient(180deg, #10b981 0%, #059669 100%);"></span> نقدي</span>
              <span class="legend-item"><span class="legend-dot" style="background: linear-gradient(180deg, #f59e0b 0%, #d97706 100%);"></span> عيني</span>
            </div>
          </div>
          <div class="premium-bar-chart">
            @for($i = 0; $i < count($months); $i++)
              @php $cm = $cashSeries[$i];
                $km = $inKindSeries[$i];
                $ch = max(round(($cm / $maxVal) * 180), 8);
                $kh = max(round(($km / $maxVal) * 180), 8); @endphp
              <div class="bar-group" title="{{ $months[$i] }}">
                <div class="bar cash" style="height:{{ $ch }}px" title="نقدي: {{ number_format($cm, 0) }}" data-bs-toggle="tooltip"></div>
                <div class="bar kind" style="height:{{ $kh }}px" title="عيني: {{ number_format($km, 0) }}" data-bs-toggle="tooltip"></div>
              </div>
            @endfor
          </div>
          <div class="bar-labels">
            @foreach($months as $m)<span>{{ $m }}</span>@endforeach
          </div>
        </div>
      </div>
      <div class="col-lg-4">
        <div class="summary-panel h-100">
          <h5 class="summary-title"><i class="bi bi-people-fill text-success"></i> حالة المستفيدين</h5>
          @foreach($beneficiaryStatus as $s)
            <div class="summary-item">
              <span class="summary-label">
                <i class="bi bi-circle-fill" style="font-size: 6px; color: var(--primary);"></i>
                {{ $s['status'] }}
              </span>
              <span class="summary-value">{{ number_format($s['count']) }}</span>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-8">
        <div class="card border-0 shadow-sm h-100 overflow-visible">
          <div class="card-header bg-white py-3 border-0 overflow-visible">
            <h5 class="mb-0 fw-bold"><i class="bi bi-activity me-2 text-primary"></i> آخر سجلات النظام</h5>
          </div>
          @if($isAdmin)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                  <tr>
                    <th class="py-3 ps-3 text-secondary small text-uppercase">#</th>
                    <th class="py-3 text-secondary small text-uppercase">التاريخ</th>
                    <th class="py-3 text-secondary small text-uppercase">المستخدم</th>
                    <th class="py-3 text-secondary small text-uppercase">الطريقة</th>
                    <th class="py-3 text-secondary small text-uppercase">المسار</th>
                    <th class="py-3 text-secondary small text-uppercase">الحالة</th>
                    <th class="py-3 text-secondary small text-uppercase">IP</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($audits as $a)
                    <tr>
                      <td class="ps-3 text-muted">{{ $a->id }}</td>
                      <td class="text-nowrap">{{ optional($a->created_at)->format('Y-m-d H:i') }}</td>
                      <td class="fw-bold">{{ optional($audUserMap->get($a->user_id))->name ?? '—' }}</td>
                      <td>
                          @php
                              $methodColors = ['GET' => 'info', 'POST' => 'success', 'PUT' => 'warning', 'DELETE' => 'danger'];
                              $color = $methodColors[$a->method] ?? 'secondary';
                          @endphp
                          <span class="badge bg-{{ $color }}-subtle text-{{ $color }} border border-{{ $color }}-subtle">{{ $a->method }}</span>
                      </td>
                      <td class="small text-muted font-monospace">{{ Str::limit($a->path, 25) }}</td>
                      <td>
                          <span class="badge {{ $a->status_code >= 400 ? 'bg-danger-subtle text-danger' : 'bg-success-subtle text-success' }}">
                              {{ $a->status_code ?? '—' }}
                          </span>
                      </td>
                      <td><span class="small text-muted font-monospace">{{ $a->ip ?? '—' }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-muted py-3 text-center">لا تُعرض السجلات إلا للمسؤولين</div>
          @endif
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 h-100">
          <h5 class="mb-3 fw-bold">التنبيهات</h5>
          @forelse($notifications as $n)
            <div class="alert alert-{{ $n['type'] }} py-2 mb-2 small shadow-sm border-0">{{ $n['text'] }}</div>
          @empty
            <div class="text-muted text-center py-4">لا توجد تنبيهات</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">الحملات النشطة</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
              <thead class="bg-transparent text-muted">
                <tr>
                  <th class="text-start">الحملة</th>
                  <th>التبرعات</th>
                  <th>المصروفات</th>
                  <th>الصافي</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activeCampaigns as $camp)
                  <tr>
                    <td class="text-start fw-bold"><a href="{{ route('campaigns.show', $camp) }}"
                        class="text-decoration-none text-dark">{{ $camp->name }}</a></td>
                    <td class="text-success">{{ number_format($camp->total_donations) }}</td>
                    <td class="text-danger">{{ number_format($camp->total_expenses) }}</td>
                    <td class="fw-bold {{ $camp->net_balance >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ number_format($camp->net_balance) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-2">لا توجد حملات نشطة حالياً</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">المشاريع النشطة</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
              <thead class="bg-transparent text-muted">
                <tr>
                  <th class="text-start">المشروع</th>
                  <th>التبرعات</th>
                  <th>المصروفات</th>
                  <th>الصافي</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activeProjects as $proj)
                  <tr>
                    <td class="text-start fw-bold"><a href="{{ route('projects.show', $proj) }}"
                        class="text-decoration-none text-dark">{{ $proj->name }}</a></td>
                    <td class="text-success">{{ number_format($proj->total_donations) }}</td>
                    <td class="text-danger">{{ number_format($proj->total_expenses) }}</td>
                    <td class="fw-bold {{ $proj->net_balance >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ number_format($proj->net_balance) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-2">لا توجد مشاريع نشطة حالياً</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    {{-- Activity Feeds Row --}}
    <div class="row g-4 mb-4 mt-3">
      <div class="col-lg-6">
        <div class="summary-panel h-100">
          <h5 class="summary-title"><i class="bi bi-cash-coin text-success"></i> آخر التبرعات</h5>
          <div class="activity-feed">
            @foreach($latestDonations as $d)
              <div class="activity-item">
                <div class="activity-icon bg-success text-white">
                  <i class="bi bi-{{ $d->type === 'cash' ? 'cash' : 'gift' }}"></i>
                </div>
                <div class="activity-content">
                  <div class="activity-title">
                    @if($d->donor_id)
                      <a href="{{ route('donors.show', $d->donor_id) }}" class="text-decoration-none text-dark">{{ $d->donor?->name ?? '—' }}</a>
                    @else
                      {{ $d->donor?->name ?? 'متبرع مجهول' }}
                    @endif
                  </div>
                  <div class="activity-meta">{{ $d->type === 'cash' ? 'تبرع نقدي' : 'تبرع عيني' }}</div>
                </div>
                <div class="activity-value text-success">+{{ number_format($d->type === 'cash' ? $d->amount : $d->estimated_value, 0) }}</div>
              </div>
            @endforeach
          </div>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="summary-panel h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="summary-title mb-0"><i class="bi bi-wallet2 text-danger"></i> آخر المصروفات</h5>
            <a class="btn btn-sm btn-outline-secondary rounded-pill px-3" href="{{ route('expenses.index') }}">عرض الكل</a>
          </div>
          @if(count($latestExpenses) > 0)
            <div class="activity-feed">
              @foreach($latestExpenses as $ex)
                <div class="activity-item">
                  <div class="activity-icon bg-danger text-white">
                    <i class="bi bi-receipt"></i>
                  </div>
                  <div class="activity-content">
                    <div class="activity-title">{{ $ex->category ?? 'غير محدد' }}</div>
                    <div class="activity-meta">{{ optional($ex->created_at)->format('Y-m-d') }}</div>
                  </div>
                  <div class="activity-value text-danger">-{{ number_format($ex->amount, 0) }}</div>
                </div>
              @endforeach
            </div>
          @else
            <div class="text-center py-4 text-muted">لا توجد مصروفات حديثة</div>
          @endif
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">أعلى 5 أصناف في المخزون</h5>
            <a class="btn btn-sm btn-outline-secondary rounded-pill px-3" href="{{ route('warehouses.index') }}">عرض
              الكل</a>
          </div>
          <ul class="list-unstyled list-simple mb-0">
            @forelse($inventoryLevels as $inv)
              <li>
                <div>
                  <div class="fw-bold">{{ $inv->item->name ?? '—' }}</div>
                  <div class="small text-muted">{{ $inv->item->category ?? '' }}</div>
                </div>
                <span class="chip">{{ $inv->current_stock }}</span>
              </li>
            @empty
              <li class="text-center text-muted py-2">المخزون فارغ</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>

  @elseif($isFinance ?? false)
    {{-- Finance Dashboard --}}
    <div class="welcome-card d-flex align-items-center justify-content-between p-4 mb-4" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);">
      <div class="position-relative z-1">
        <h2 class="h3 fw-bold mb-1">لوحة المحاسبة والمالية 💰</h2>
        <p class="mb-3 text-white-50 fs-6">مرحباً {{ $user->name }}، إليك الملخص المالي لليوم {{ date('Y-m-d') }}</p>
        
        <div class="welcome-actions d-flex gap-2">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm rounded-pill px-3">
                <i class="bi bi-person-circle me-1"></i> ملفي الشخصي
            </a>
            <a href="{{ route('expenses.index') }}" class="btn btn-sm rounded-pill px-3">
                <i class="bi bi-cash-stack me-1"></i> المصروفات
            </a>
        </div>
      </div>
      <div class="d-none d-md-block position-relative z-1">
          <i class="bi bi-wallet2 text-white opacity-25" style="font-size: 5rem;"></i>
      </div>
    </div>

    {{-- Row 0: My Stats (Employee) --}}
    <div class="row g-3 mb-4">
      <div class="col-6 col-md-3">
          <div class="kpi-card kpi-success p-4 h-100 position-relative">
              <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="small text-muted">الراتب ({{ $financeStats['salary_month'] }})</div>
                  <i class="bi bi-wallet2 text-success opacity-50 fs-5"></i>
              </div>
              <div class="h3 fw-bold text-dark mb-0">{{ number_format($financeStats['salary']) }}</div>
              <div class="small text-muted mt-1">آخر راتب مسجل</div>
              <a href="{{ route('payrolls.index') }}" class="stretched-link"></a>
          </div>
      </div>
      <div class="col-6 col-md-3">
          <div class="kpi-card kpi-primary p-4 h-100 position-relative">
               <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="small text-muted">حضوري هذا الشهر</div>
                  <i class="bi bi-calendar-check text-primary opacity-50 fs-5"></i>
              </div>
              <div class="h3 fw-bold text-dark mb-0">{{ $financeStats['attendance'] }} <span class="fs-6 fw-normal text-muted">يوم</span></div>
              {{-- Link removed as requested --}}
          </div>
      </div>
      <div class="col-6 col-md-3">
          <div class="kpi-card kpi-warning p-4 h-100 position-relative">
               <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="small text-muted">رصيد الإجازات</div>
                  <i class="bi bi-sun text-warning opacity-50 fs-5"></i>
              </div>
              <div class="h3 fw-bold text-dark mb-0">{{ $financeStats['vacations_balance'] }} <span class="fs-6 fw-normal text-muted">يوم</span></div>
              <div class="progress mt-2" style="height: 4px;">
                  <div class="progress-bar bg-warning" style="width: {{ max(0, min(100, (($financeStats['vacations_balance'] ?? 21) / 21) * 100)) }}%"></div>
              </div>
              <a href="{{ route('leaves.index') }}" class="stretched-link"></a>
          </div>
      </div>
      <div class="col-6 col-md-3">
          <div class="kpi-card kpi-danger p-4 h-100">
               <div class="d-flex justify-content-between align-items-center mb-2">
                  <div class="small text-muted">مهام معلقة</div>
                  <i class="bi bi-list-task text-danger opacity-50 fs-5"></i>
              </div>
              <div class="h3 fw-bold text-dark mb-0">{{ $financeStats['tasks_pending'] }}</div>
              <a href="{{ route('employee-tasks.index') }}" class="stretched-link"></a>
          </div>
      </div>
    </div>

    {{-- Row 1: Counts --}}
    <div class="row g-3 mb-3">
      <div class="col-md-6"><div class="kpi-card kpi-info p-4 text-center h-100 d-block">
          <div class="small text-muted mb-2">المخازن</div>
          <div class="display-6 fw-bold text-dark">{{ $warehousesCount }}</div>
      </div></div>
      <div class="col-md-6"><div class="kpi-card kpi-success p-4 text-center h-100 d-block">
          <div class="small text-muted mb-2">المستفيدون</div>
          <div class="display-6 fw-bold text-dark">{{ $beneficiariesCount }}</div>
      </div></div>
    </div>

    {{-- Row 2: Financials Month --}}
    <div class="row g-3 mb-3">
      <div class="col-6 col-md-6"><a href="{{ route('expenses.index') }}" class="kpi-card kpi-danger p-4 text-center text-decoration-none h-100 d-block hover-lift">
          <div class="small text-muted mb-2">مصروفات (هذا الشهر)</div>
          <div class="h3 mb-0 fw-bold text-danger">{{ number_format($expensesMonth, 2) }}</div>
      </a></div>
      <div class="col-6 col-md-6"><div class="kpi-card kpi-warning p-4 text-center h-100 d-block">
          <div class="small text-muted mb-2">صافي التدفق (هذا الشهر)</div>
          <div class="h3 mb-0 fw-bold text-dark" style="direction: ltr;">{{ number_format($netFlowMonth, 2) }}</div>
      </div></div>
    </div>

    {{-- Row 3: Charts --}}
    {{-- Using existing variables for charts --}}
    @php $maxVal = max(array_merge($cashSeries, $inKindSeries));
    $maxVal = $maxVal > 0 ? $maxVal : 1; @endphp
    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <div class="card chart-wrap p-3 h-100 d-flex flex-column">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">توزيع التبرعات (آخر 12 شهرًا)</h5>
            <span class="chip">نقدي مقابل عيني</span>
          </div>
          
          {{-- Chart Area --}}
          <div style="flex: 1; min-height: 250px; display: flex; flex-direction: column;">
              <div class="premium-bar-chart" style="flex: 1;">
                @for($i = 0; $i < count($months); $i++)
                  @php 
                    $cm = $cashSeries[$i];
                    $km = $inKindSeries[$i];
                    $ch = max(round(($cm / $maxVal) * 180), 4);
                    $kh = max(round(($km / $maxVal) * 180), 4); 
                  @endphp
                  <div class="bar-group" title="{{ $months[$i] }}">
                    <div class="bar cash" style="height:{{ $ch }}px" title="{{ $months[$i] }} (نقدي): {{ number_format($cm, 2) }}" data-bs-toggle="tooltip"></div>
                    <div class="bar kind" style="height:{{ $kh }}px" title="{{ $months[$i] }} (عيني): {{ number_format($km, 2) }}" data-bs-toggle="tooltip"></div>
                  </div>
                @endfor
              </div>
              <div class="bar-labels mb-3">
                @foreach($months as $m)<span>{{ $m }}</span>@endforeach
              </div>
          </div>

          {{-- Data Table for Chart --}}
          <div class="table-responsive mt-auto pt-3 border-top" style="max-height: 200px; overflow-y: auto;">
            <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
              <thead class="table-light sticky-top">
                <tr>
                  <th>الشهر</th>
                  <th class="text-success">نقدي</th>
                  <th class="text-danger">عيني</th>
                  <th class="text-primary fw-bold">الإجمالي</th>
                </tr>
              </thead>
              <tbody>
                @for($i = 0; $i < count($months); $i++)
                  <tr>
                    <td class="fw-bold">{{ $months[$i] }}</td>
                    <td class="text-success">{{ number_format($cashSeries[$i], 0) }}</td>
                    <td class="text-danger">{{ number_format($inKindSeries[$i], 0) }}</td>
                    <td class="text-primary fw-bold">{{ number_format($cashSeries[$i] + $inKindSeries[$i], 0) }}</td>
                  </tr>
                @endfor
              </tbody>
              <tfoot class="border-top fw-bold bg-light">
                <tr>
                  <th class="ps-3">الإجمالي</th>
                  <td class="text-success">{{ number_format(array_sum($cashSeries), 0) }}</td>
                  <td class="text-danger">{{ number_format(array_sum($inKindSeries), 0) }}</td>
                  <td class="text-primary">{{ number_format(array_sum($cashSeries) + array_sum($inKindSeries), 0) }}</td>
                </tr>
              </tfoot>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-4">
          {{-- Net Flow Chart - Simple custom visual since we don't have a JS chart lib configured yet --}}
          <div class="card chart-wrap p-3 h-100">
              <h5 class="mb-3 fw-bold">صافي التدفق (آخر 12 شهرًا)</h5>
              <div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%;">
                  <div style="flex: 1; display: flex; align-items: flex-end; gap: 6px;">
                      @php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                  $maxExp = max($expenseSeries) > 0 ? max($expenseSeries) : 1;
                        $maxInc = $maxVal > 0 ? $maxVal : 1; // From donation max
                        $globalMax = max($maxExp, $maxInc);
                      @endphp
                      @for($i = 0; $i < count($months); $i++)
                        @php 
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                      $inc = $cashSeries[$i] + $inKindSeries[$i]; // Total Income
                          $exp = $expenseSeries[$i] ?? 0;
                          $net = $inc - $exp;
                          $h = max(round(($inc / $globalMax) * 80), 2); // Income height
                          $eh = max(round(($exp / $globalMax) * 80), 2); // Expense height
                        @endphp
                         <div style="flex:1; display:flex; flex-direction:column; justify-content:flex-end; align-items:center;">
                             {{-- Stackedish or Side by side simple bars --}}
                             <div style="width:6px; height:{{ $h }}px; background:var(--primary); opacity:0.7; border-radius:2px;" title="Income: {{ number_format($inc) }}" data-bs-toggle="tooltip"></div>
                             <div style="width:6px; height:{{ $eh }}px; background:#dc3545; opacity:0.7; border-radius:2px; margin-top:-{{ min($h, $eh) }}px; margin-left:8px;" title="Expense: {{ number_format($exp) }}" data-bs-toggle="tooltip"></div>
                         </div>
                      @endfor
                  </div>
                  <div class="text-center small text-muted mt-2">
                       <span class="text-primary fs-7">â—ڈ دخل</span> <span class="text-danger fs-7">â—ڈ مصروف</span>
                  </div>
              </div>
          </div>
      </div>
    </div>

    {{-- Row 4: Details & Widgets --}}
    <div class="row g-3 mt-1">
      <div class="col-md-4">
        <div class="card kpi-card p-3 h-100">
          <h5 class="mb-3 fw-bold">توزيع هذا الشهر</h5>
          <div class="d-flex gap-3 align-items-center justify-content-center py-4">
            <div class="text-center">
              <h6 class="text-muted">نقدي</h6>
              <div class="h3 text-primary">{{ $cashMonthPct }}%</div>
            </div>
            <div class="divider-white" style="width: 2px; height: 50px; background-color: #fff; opacity: 0.8;"></div>
            <div class="text-center">
              <h6 class="text-muted">عيني</h6>
              <div class="h3 text-success">{{ $inKindMonthPct }}%</div>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
          <div class="card kpi-card p-3 h-100">
              <h5 class="mb-3 fw-bold">أفضل المتبرعين (هذا الشهر)</h5>
              <ul class="list-unstyled list-simple mb-0">
              @foreach($topDonors as $td)
                <li>
                    <span>{{ $td['name'] }}</span>
                    <span class="chip">{{ number_format($td['total'], 2) }}</span>
                </li>
              @endforeach
              </ul>
          </div>
      </div>

      <div class="col-md-4">
          <div class="card kpi-card p-3 h-100">
              <h5 class="mb-3 fw-bold">أفضل 5 أصناف مخزون</h5>
              <ul class="list-unstyled list-simple mb-0">
              @forelse($inventoryLevels as $inv)
                <li>
                <span>{{ $inv->item->name ?? '—' }}</span>
                <span class="chip">{{ $inv->current_stock }}</span>
                </li>
              @empty
                <li class="text-center text-muted">المخزون فارغ</li>
              @endforelse
              </ul>
          </div>
      </div>
    </div>

    <div class="row g-3 mt-3">
         <div class="col-md-6">
              <div class="card kpi-card p-3 h-100">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                      <h5 class="mb-0 fw-bold">آخر التبرعات</h5>
                  </div>
                  <ul class="list-unstyled list-simple mb-0">
                  @foreach($latestDonations as $d)
                    <li>
                    <div>
                        <div class="fw-bold">{{ $d->donor?->name ?? '—' }}</div>
                        <div class="small text-muted">{{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}</div>
                    </div>
                    <span class="chip">{{ number_format($d->amount ?? $d->estimated_value, 2) }}</span>
                    </li>
                  @endforeach
                  </ul>
              </div>
         </div>
         <div class="col-md-6">
             {{-- Expense Distribution --}}
            <div class="card kpi-card p-3 h-100">
              <h5 class="mb-3 fw-bold">توزيع المصروفات (هذا الشهر)</h5>
              @if(count($expenseByCat) > 0)
                <ul class="list-unstyled list-simple mb-0">
                  @foreach($expenseByCat as $ec)
                    <li>
                      <span>{{ $ec->category ?? 'أخرى' }}</span>
                      <span class="fw-bold text-dark">{{ number_format($ec->total, 2) }}</span>
                    </li>
                  @endforeach
                </ul>
              @else
                <div class="text-center py-4 text-muted">لا توجد مصروفات هذا الشهر</div>
              @endif
            </div>
         </div>
    </div>




  @elseif($isHr ?? false)
    {{-- HR Dashboard - Advanced Analytics --}}
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
    @if(isset($hrDashboardData['insights']) && count($hrDashboardData['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-info fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-info">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($hrDashboardData['insights'] as $insight)
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

    {{-- HR Employee Personal Profile Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(139, 92, 246, 0.08); border: 1px solid rgba(139, 92, 246, 0.2);">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #8b5cf6 0%, #6d28d9 100%); color: white;">
                        {{ mb_substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3);">موظف موارد بشرية</span>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles->take(2) as $role)
                                    <span class="badge" style="background: rgba(255,255,255,0.1); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">{{ $role->name }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('users.show', $user->id) }}" class="btn rounded-pill px-4 d-none d-md-inline-flex" style="background: rgba(139, 92, 246, 0.2); color: #a78bfa; border: 1px solid rgba(139, 92, 246, 0.3);">
                        <i class="bi bi-person-circle me-2"></i>عرض الملف الكامل
                    </a>
                </div>

                <div class="row g-3">
                    {{-- Salary Card --}}
                    <div class="col-md-3 col-6">
                        <a href="{{ route('payrolls.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-wallet2 fs-4 mb-2" style="color: #10b981;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #10b981;">{{ number_format($hrStats['salary'] ?? 0) }}</div>
                            <div class="small" style="color: #6b7280;">الراتب ({{ $hrStats['salary_month'] ?? now()->format('Y-m') }})</div>
                        </a>
                    </div>

                    {{-- Attendance Card --}}
                    <div class="col-md-3 col-6">
                        <a href="{{ route('employee-attendance.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <i class="bi bi-calendar-check fs-4 mb-2" style="color: #a78bfa;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #a78bfa;">{{ $hrStats['attendance'] ?? 0 }} <span class="fs-6 fw-normal" style="color: #6b7280;">يوم</span></div>
                            <div class="small" style="color: #6b7280;">حضوري هذا الشهر</div>
                        </a>
                    </div>

                    {{-- Leave Balance Card --}}
                    <div class="col-md-3 col-6">
                        <a href="{{ route('leaves.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <i class="bi bi-sun fs-4 mb-2" style="color: #f59e0b;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #f59e0b;">{{ $hrStats['vacations_balance'] ?? 21 }} <span class="fs-6 fw-normal" style="color: #6b7280;">يوم</span></div>
                            <div class="small" style="color: #6b7280;">رصيد الإجازات</div>
                            <div class="progress mt-2 mx-auto" style="height: 4px; max-width: 80%; background: rgba(245, 158, 11, 0.2);">
                                <div class="progress-bar" style="width: {{ max(0, (($hrStats['vacations_balance'] ?? 21) / 21) * 100) }}%; background: #f59e0b;"></div>
                            </div>
                        </a>
                    </div>

                    {{-- Pending Tasks Card --}}
                    <div class="col-md-3 col-6">
                        <a href="{{ route('employee-tasks.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <i class="bi bi-list-task fs-4 mb-2" style="color: #ef4444;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #ef4444;">{{ $hrStats['tasks_pending'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">مهام معلقة</div>
                        </a>
                    </div>
                </div>

                {{-- Additional Info Row --}}
                <div class="row mt-3 pt-3 g-2" style="border-top: 1px solid rgba(255,255,255,0.1);">
                    <div class="col-md-4 col-6">
                        <div class="d-flex align-items-center small" style="color: #9ca3af;">
                            <i class="bi bi-calendar-range me-2" style="color: #a78bfa;"></i>
                            <span>الإجازات المستخدمة: <strong style="color: #e5e7eb;">{{ $hrStats['vacations_taken'] ?? 0 }} يوم</strong></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="d-flex align-items-center small" style="color: #9ca3af;">
                            <i class="bi bi-clock-history me-2" style="color: #38bdf8;"></i>
                            <span>الأذونات: <strong style="color: #e5e7eb;">{{ $hrStats['permissions_count'] ?? 0 }}</strong></span>
                        </div>
                    </div>
                    <div class="col-md-4 col-6">
                        <div class="d-flex align-items-center small" style="color: #9ca3af;">
                            <i class="bi bi-patch-check me-2" style="color: #10b981;"></i>
                            <span>الرصيد السنوي: <strong style="color: #e5e7eb;">{{ $hrStats['annual_balance'] ?? 21 }} يوم</strong></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Section -->
    <div class="row g-4 mb-4">
        <!-- 1. Workforce Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <a href="{{ route('users.index', ['type' => 'employee']) }}" class="glass-card h-100 hover-lift relative overflow-hidden text-decoration-none d-block">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-people-fill fs-5"></i>
                        </div>
                         <div class="badge bg-primary bg-opacity-10 text-primary">القوى العاملة</div>
                    </div>
                    <div class="d-flex justify-content-between align-items-end">
                        <div>
                             <div class="h2 fw-bold mb-0 text-dark">{{ ($hrDashboardData['totalEmployees'] ?? 0) + ($hrDashboardData['totalVolunteers'] ?? 0) }}</div>
                             <small class="text-muted">إجمالي الفريق</small>
                        </div>
                        <div class="text-end small">
                             <div class="d-block"><span class="fw-bold text-dark">{{ $hrDashboardData['totalEmployees'] ?? 0 }}</span> <span class="text-muted">موظف</span></div>
                             <div class="d-block"><span class="fw-bold text-dark">{{ $hrDashboardData['totalVolunteers'] ?? 0 }}</span> <span class="text-muted">متطوع</span></div>
                        </div>
                    </div>
                </div>
                @php 
                    $totalWf = max(1, ($hrDashboardData['totalEmployees'] ?? 0) + ($hrDashboardData['totalVolunteers'] ?? 0));
                @endphp
                <div class="progress h-1 mt-auto rounded-0 bg-transparent">
                    <div class="progress-bar bg-primary" role="progressbar" style="width: {{ (($hrDashboardData['totalEmployees'] ?? 0) / $totalWf) * 100 }}%"></div>
                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ (($hrDashboardData['totalVolunteers'] ?? 0) / $totalWf) * 100 }}%"></div>
                </div>
            </a>
        </div>

        <!-- 2. Attendance Health -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <a href="{{ route('employee-attendance.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-heart-pulse-fill fs-5"></i>
                        </div>
                         <div class="badge bg-success bg-opacity-10 text-success">صحة الحضور</div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="h2 fw-bold mb-0 text-dark me-2">{{ number_format($hrDashboardData['attendanceRate'] ?? 0, 0) }}%</div>
                        @if(($hrDashboardData['attendanceRate'] ?? 0) >= 85)
                            <small class="text-success fw-bold"><i class="bi bi-arrow-up-right"></i> ممتاز</small>
                        @elseif(($hrDashboardData['attendanceRate'] ?? 0) >= 70)
                               <small class="text-warning fw-bold"><i class="bi bi-dash"></i> جيد</small>
                        @else
                             <small class="text-danger fw-bold"><i class="bi bi-arrow-down-right"></i> منخفض</small>
                        @endif
                    </div>
                    <div class="small text-muted mt-1">نسبة الالتزام بالحضور اليومي</div>
                     <div class="mt-3 small d-flex justify-content-between text-muted">
                        <span>{{ $hrDashboardData['employeeAttendanceCount'] ?? 0 }} سجل موظفين</span>
                        <span>{{ $hrDashboardData['volunteerAttendanceCount'] ?? 0 }} سجل متطوعين</span>
                    </div>
                </div>
            </a>
        </div>

        <!-- 3. Pending Requests -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <a href="{{ route('leaves.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-inbox-fill fs-5"></i>
                        </div>
                         <div class="badge bg-warning bg-opacity-10 text-warning">طلبات معلقة</div>
                    </div>
                    <div class="row text-center">
                        <div class="col-6 border-end">
                            <div class="h3 fw-bold mb-0 text-dark">{{ $hrDashboardData['pendingLeaves'] ?? 0 }}</div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">إجازات</small>
                        </div>
                        <div class="col-6">
                             <div class="h3 fw-bold mb-0 text-dark">{{ $hrDashboardData['tasksPending'] ?? 0 }}</div>
                             <small class="text-muted d-block" style="font-size: 0.75rem;">مهام</small>
                        </div>
                    </div>
                 </div>
            </a>
        </div>

        <!-- 4. Payroll Current -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <a href="{{ route('payrolls.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle bg-danger-subtle text-danger rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="bi bi-wallet2 fs-5"></i>
                        </div>
                         <div class="badge bg-danger bg-opacity-10 text-danger">رواتب الشهر</div>
                    </div>
                    <div class="h3 fw-bold mb-0 text-start" dir="ltr">{{ number_format($hrDashboardData['totalSalaries'] ?? 0) }}</div>
                    <div class="small text-muted text-end">تم صرفها لـ {{ $hrDashboardData['employeesPaidCount'] ?? 0 }} موظف</div>
                 </div>
             </a>
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
                    <canvas id="hrSalaryChart"></canvas>
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
                    @forelse($hrDashboardData['topPerformers'] ?? [] as $performer)
                    <div class="list-group-item bg-transparent border-bottom-0 py-3 d-flex align-items-center px-4">
                         <div class="avatar-circle bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                            {{ mb_substr($performer->name, 0, 1) }}
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold text-dark">{{ $performer->name }}</div>
                            <small class="text-muted">{{ $performer->role_label ?? 'عضو فريق' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-warning"><i class="bi bi-star-fill me-1"></i>{{ number_format($performer->avg_rating ?? 0, 1) }}</div>
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // HR Salary Trend Chart
    const hrCtx = document.getElementById('hrSalaryChart');
    if(hrCtx){
         new Chart(hrCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($hrDashboardData['salaryTrendLabels'] ?? []) !!},
                datasets: [{
                    label: 'إجمالي الرواتب',
                    data: {!! json_encode($hrDashboardData['salaryTrendData'] ?? []) !!},
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

  @elseif(($isProjectManager ?? false) || ($isDeputyManager ?? false))
    {{-- Project Manager / Deputy Manager Dashboard - Advanced AI-Powered --}}
    @php
        $pm = $projectManagerDashboardData ?? [];
        $roleTitle = ($pm['isProjectManager'] ?? false) ? 'مدير المشروع' : 'نائب مدير المشروع';
        $roleIcon = ($pm['isProjectManager'] ?? false) ? '👨‍💼' : '👨‍💻';
        $accentColor = ($pm['isProjectManager'] ?? false) ? '#f59e0b' : '#3b82f6';
    @endphp

    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ ($pm['isProjectManager'] ?? false) ? '#d97706' : '#1d4ed8' }} 100%);">
        <div class="hero-content">
            <div class="hero-greeting">{{ $roleTitle }} {{ $roleIcon }}</div>
            <h1 class="hero-title">{{ $pm['projectName'] ?? 'لوحة التحكم' }}</h1>
            <p class="hero-subtitle">إدارة المشروع بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-cpu me-2"></i>تحليل ذكي
             </div>
            <i class="bi bi-briefcase-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($pm['insights']) && count($pm['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-warning fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-warning">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($pm['insights'] as $insight)
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

    {{-- Project Info + Team Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba({{ ($pm['isProjectManager'] ?? false) ? '245, 158, 11' : '59, 130, 246' }}, 0.08); border: 1px solid rgba({{ ($pm['isProjectManager'] ?? false) ? '245, 158, 11' : '59, 130, 246' }}, 0.2);">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ ($pm['isProjectManager'] ?? false) ? '#d97706' : '#1d4ed8' }} 100%); color: white;">
                            <i class="bi bi-folder-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $pm['projectName'] ?? 'المشروع' }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge" style="background: rgba({{ ($pm['isProjectManager'] ?? false) ? '245, 158, 11' : '59, 130, 246' }}, 0.2); color: {{ ($pm['isProjectManager'] ?? false) ? '#fbbf24' : '#60a5fa' }}; border: 1px solid rgba({{ ($pm['isProjectManager'] ?? false) ? '245, 158, 11' : '59, 130, 246' }}, 0.3);">{{ $roleTitle }}</span>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">{{ $pm['projectStatus'] ?? 'نشط' }}</span>
                            </div>
                        </div>
                    </div>
                    <a href="{{ !empty($pm['projectId']) ? route('projects.show', $pm['projectId']) : route('projects.index') }}" class="btn rounded-pill px-4" style="background: rgba({{ ($pm['isProjectManager'] ?? false) ? '245, 158, 11' : '59, 130, 246' }}, 0.2); color: {{ ($pm['isProjectManager'] ?? false) ? '#fbbf24' : '#60a5fa' }}; border: 1px solid rgba({{ ($pm['isProjectManager'] ?? false) ? '245, 158, 11' : '59, 130, 246' }}, 0.3);">
                        <i class="bi bi-eye me-2"></i>عرض المشروع
                    </a>
                </div>

                {{-- Team Info --}}
                <div class="row g-3 mb-3">
                    @if($pm['projectManager'] ?? null)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                                    {{ mb_substr($pm['projectManager']->name ?? 'م', 0, 1) }}
                                </div>
                                <div>
                                    <div class="small" style="color: #fbbf24;">مدير المشروع</div>
                                    <div class="fw-bold">{{ $pm['projectManager']->name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($pm['projectDeputy'] ?? null)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white;">
                                    {{ mb_substr($pm['projectDeputy']->name ?? 'ن', 0, 1) }}
                                </div>
                                <div>
                                    <div class="small" style="color: #60a5fa;">نائب المدير</div>
                                    <div class="fw-bold">{{ $pm['projectDeputy']->name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Stats Row --}}
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-cash-stack fs-4 mb-2" style="color: #10b981;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #10b981;">{{ number_format($pm['totalDonations'] ?? 0) }}</div>
                            <div class="small" style="color: #6b7280;">إجمالي التبرعات</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <i class="bi bi-cart-dash fs-4 mb-2" style="color: #ef4444;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #ef4444;">{{ number_format($pm['totalExpenses'] ?? 0) }}</div>
                            <div class="small" style="color: #6b7280;">المصروفات</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba({{ ($pm['netBalance'] ?? 0) >= 0 ? '16, 185, 129' : '239, 68, 68' }}, 0.1); border: 1px solid rgba({{ ($pm['netBalance'] ?? 0) >= 0 ? '16, 185, 129' : '239, 68, 68' }}, 0.2);">
                            <i class="bi bi-graph-up-arrow fs-4 mb-2" style="color: {{ ($pm['netBalance'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }};"></i>
                            <div class="h5 fw-bold mb-0" style="color: {{ ($pm['netBalance'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format($pm['netBalance'] ?? 0) }}</div>
                            <div class="small" style="color: #6b7280;">صافي الرصيد</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <i class="bi bi-people-fill fs-4 mb-2" style="color: #a78bfa;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #a78bfa;">{{ $pm['totalTeam'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">فريق العمل</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
             <a href="{{ route('tasks.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-list-task fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">المهام المعلقة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ $pm['tasksPending'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">من {{ $pm['tasksTotal'] ?? 0 }} مهمة</div>
                    <div class="progress mt-2" style="height: 4px;">
                        <div class="progress-bar bg-success" style="width: {{ $pm['tasksCompletionRate'] ?? 0 }}%;"></div>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <a href="{{ route('beneficiaries.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-people-fill fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">المستفيدون</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ number_format($pm['beneficiariesTotal'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر: {{ $pm['beneficiariesThisMonth'] ?? 0 }}</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
             <a href="{{ route('campaigns.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-megaphone-fill fs-5" style="color: #f59e0b;"></i>
                        </div>
                         <div class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">الحملات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f59e0b;">{{ $pm['activeCampaigns'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">نشطة من {{ $pm['totalCampaigns'] ?? 0 }}</div>
                 </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
              <a href="{{ route('tasks.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-calendar-check fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">معدل الإنجاز</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ $pm['tasksCompletionRate'] ?? 0 }}%</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                 </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Financial Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: {{ $accentColor }};"></i>التبرعات والمصروفات (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="pmFinancialChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Tasks -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2" style="color: {{ $accentColor }};"></i>آخر المهام</h5>
                    <a href="{{ route('tasks.index') }}" class="small" style="color: {{ $accentColor }};">عرض الكل</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($pm['latestTasks'] ?? [] as $task)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: {{ $task->status == 'done' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                            <i class="bi bi-{{ $task->status == 'done' ? 'check-lg' : 'clock' }}" style="color: {{ $task->status == 'done' ? '#10b981' : '#f59e0b' }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ Str::limit($task->title, 20) }}</div>
                            <small style="color: #6b7280;">{{ optional($task->due_date)->format('Y-m-d') ?? '—' }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد مهام</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7"><i class="bi bi-lightning-charge me-2" style="color: {{ $accentColor }};"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-2 col-4">
            <a href="{{ route('projects.show', $pm['projectId'] ?? 1) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-folder-fill fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold small">المشروع</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('tasks.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-list-task fs-3 mb-2" style="color: #ef4444;"></i>
                 <div class="fw-bold small">المهام</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('donations.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-cash-stack fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold small">التبرعات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('expenses.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-cart-dash fs-3 mb-2" style="color: #ec4899;"></i>
                 <div class="fw-bold small">المصروفات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('campaigns.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-megaphone-fill fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold small">الحملات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #3b82f6;"></i>
                 <div class="fw-bold small">ملفي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const pmCtx = document.getElementById('pmFinancialChart');
    if(pmCtx){
         new Chart(pmCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($pm['donationTrendLabels'] ?? []) !!},
                datasets: [{
                    label: 'التبرعات',
                    data: {!! json_encode($pm['donationTrendData'] ?? []) !!},
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
                },{
                    label: 'المصروفات',
                    data: {!! json_encode($pm['expenseTrendData'] ?? []) !!},
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true
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

  @elseif(($isCampaignManager ?? false) || ($isDeputyCampaignManager ?? false))
    {{-- Campaign Manager / Deputy Manager Dashboard - Advanced AI-Powered --}}
    @php
        $cm = $campaignManagerDashboardData ?? [];
        $roleTitle = ($cm['isCampaignManager'] ?? false) ? 'مدير الحملة' : 'نائب مدير الحملة';
        $roleIcon = ($cm['isCampaignManager'] ?? false) ? '📢' : '📋';
        $accentColor = ($cm['isCampaignManager'] ?? false) ? '#8b5cf6' : '#06b6d4';
    @endphp

    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ ($cm['isCampaignManager'] ?? false) ? '#7c3aed' : '#0891b2' }} 100%);">
        <div class="hero-content">
            <div class="hero-greeting">{{ $roleTitle }} {{ $roleIcon }}</div>
            <h1 class="hero-title">{{ $cm['campaignName'] ?? 'الحملة' }}</h1>
            <p class="hero-subtitle">إدارة الحملة بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-calendar me-2"></i>{{ $cm['seasonYear'] ?? '' }}
             </div>
            <i class="bi bi-megaphone-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($cm['insights']) && count($cm['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4" style="border-color: {{ $accentColor }} !important;">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars fs-4 me-2" style="color: {{ $accentColor }};"></i>
                    <h5 class="fw-bold mb-0" style="color: {{ $accentColor }};">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($cm['insights'] as $insight)
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

    {{-- Campaign Info + Progress Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba({{ ($cm['isCampaignManager'] ?? false) ? '139, 92, 246' : '6, 182, 212' }}, 0.08); border: 1px solid rgba({{ ($cm['isCampaignManager'] ?? false) ? '139, 92, 246' : '6, 182, 212' }}, 0.2);">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, {{ $accentColor }} 0%, {{ ($cm['isCampaignManager'] ?? false) ? '#7c3aed' : '#0891b2' }} 100%); color: white;">
                            <i class="bi bi-megaphone-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $cm['campaignName'] ?? 'الحملة' }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge" style="background: rgba({{ ($cm['isCampaignManager'] ?? false) ? '139, 92, 246' : '6, 182, 212' }}, 0.2); color: {{ ($cm['isCampaignManager'] ?? false) ? '#a78bfa' : '#22d3ee' }}; border: 1px solid rgba({{ ($cm['isCampaignManager'] ?? false) ? '139, 92, 246' : '6, 182, 212' }}, 0.3);">{{ $roleTitle }}</span>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">{{ $cm['campaignStatus'] ?? 'نشط' }}</span>
                                @if($cm['campaignProject'] ?? null)
                                <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;"><i class="bi bi-folder me-1"></i>{{ $cm['campaignProject']->name ?? '' }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('campaigns.show', $cm['campaignId'] ?? 1) }}" class="btn rounded-pill px-4" style="background: rgba({{ ($cm['isCampaignManager'] ?? false) ? '139, 92, 246' : '6, 182, 212' }}, 0.2); color: {{ ($cm['isCampaignManager'] ?? false) ? '#a78bfa' : '#22d3ee' }}; border: 1px solid rgba({{ ($cm['isCampaignManager'] ?? false) ? '139, 92, 246' : '6, 182, 212' }}, 0.3);">
                        <i class="bi bi-eye me-2"></i>عرض الحملة
                    </a>
                </div>

                {{-- Team Info --}}
                <div class="row g-3 mb-3">
                    @if($cm['campaignManager'] ?? null)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                                    {{ mb_substr($cm['campaignManager']->name ?? 'م', 0, 1) }}
                                </div>
                                <div>
                                    <div class="small" style="color: #a78bfa;">مدير الحملة</div>
                                    <div class="fw-bold">{{ $cm['campaignManager']->name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($cm['campaignDeputy'] ?? null)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(6, 182, 212, 0.1); border: 1px solid rgba(6, 182, 212, 0.2);">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white;">
                                    {{ mb_substr($cm['campaignDeputy']->name ?? 'ن', 0, 1) }}
                                </div>
                                <div>
                                    <div class="small" style="color: #22d3ee;">نائب المدير</div>
                                    <div class="fw-bold">{{ $cm['campaignDeputy']->name ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Campaign Progress --}}
                <div class="p-3 rounded-4 mb-3" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="small" style="color: #9ca3af;">تقدم الحملة</span>
                        <span class="small fw-bold" style="color: {{ $accentColor }};">{{ $cm['progressRate'] ?? 0 }}%</span>
                    </div>
                    <div class="progress" style="height: 10px; background: rgba(255,255,255,0.1); border-radius: 10px;">
                        <div class="progress-bar" style="width: {{ $cm['progressRate'] ?? 0 }}%; background: linear-gradient(90deg, {{ $accentColor }}, {{ ($cm['isCampaignManager'] ?? false) ? '#7c3aed' : '#0891b2' }}); border-radius: 10px;"></div>
                    </div>
                    <div class="d-flex justify-content-between mt-2 small" style="color: #6b7280;">
                        <span><i class="bi bi-calendar-check me-1"></i>{{ optional($cm['startDate'])->format('Y-m-d') ?? '—' }}</span>
                        <span><i class="bi bi-hourglass-split me-1"></i>باقي {{ $cm['daysRemaining'] ?? 0 }} يوم</span>
                        <span><i class="bi bi-calendar-x me-1"></i>{{ optional($cm['endDate'])->format('Y-m-d') ?? '—' }}</span>
                    </div>
                </div>

                {{-- Stats Row --}}
                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-cash-stack fs-4 mb-2" style="color: #10b981;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #10b981;">{{ number_format($cm['totalDonations'] ?? 0) }}</div>
                            <div class="small" style="color: #6b7280;">إجمالي التبرعات</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(59, 130, 246, 0.1); border: 1px solid rgba(59, 130, 246, 0.2);">
                            <i class="bi bi-people-fill fs-4 mb-2" style="color: #3b82f6;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #3b82f6;">{{ $cm['beneficiariesTotal'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">المستفيدون</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(249, 115, 22, 0.1); border: 1px solid rgba(249, 115, 22, 0.2);">
                            <i class="bi bi-heart-fill fs-4 mb-2" style="color: #f97316;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #f97316;">{{ $cm['volunteersCount'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">المتطوعون</div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <i class="bi bi-gift fs-4 mb-2" style="color: #a78bfa;"></i>
                            <div class="h5 fw-bold mb-0" style="color: #a78bfa;">{{ $cm['donationsCount'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">عدد التبرعات</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <a href="{{ route('donations.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-cash-coin fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">تبرعات الشهر</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ number_format($cm['donationsThisMonth'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م هذا الشهر</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
             <a href="{{ route('beneficiaries.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                  <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(59, 130, 246, 0.1);">
                            <i class="bi bi-person-plus fs-5" style="color: #3b82f6;"></i>
                        </div>
                         <div class="badge" style="background: rgba(59, 130, 246, 0.1); color: #3b82f6;">مستفيدين الشهر</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #3b82f6;">{{ $cm['beneficiariesThisMonth'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-currency-dollar fs-5" style="color: #f59e0b;"></i>
                        </div>
                         <div class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">متوسط التبرع</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f59e0b;">{{ number_format($cm['avgDonation'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م / تبرع</div>
                 </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(249, 115, 22, 0.1);">
                            <i class="bi bi-clock-history fs-5" style="color: #f97316;"></i>
                        </div>
                         <div class="badge" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">ساعات التطوع</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f97316;">{{ number_format($cm['volunteerHours'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">ساعة</div>
                 </div>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Donations & Beneficiaries Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: {{ $accentColor }};"></i>التبرعات والمستفيدين (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="cmTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Donations -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-cash-stack me-2" style="color: {{ $accentColor }};"></i>آخر التبرعات</h5>
                    <a href="{{ route('donations.index') }}" class="small" style="color: {{ $accentColor }};">الكل</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($cm['latestDonations'] ?? [] as $donation)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-cash" style="color: #10b981;"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $donation->donor->name ?? 'متبرع' }}</div>
                            <small style="color: #6b7280;">{{ optional($donation->created_at)->format('Y-m-d') }}</small>
                        </div>
                        <span class="fw-bold" style="color: #10b981;">{{ number_format($donation->amount) }}</span>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد تبرعات</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7"><i class="bi bi-lightning-charge me-2" style="color: {{ $accentColor }};"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-2 col-4">
            <a href="{{ route('campaigns.show', $cm['campaignId'] ?? 1) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-megaphone-fill fs-3 mb-2" style="color: #8b5cf6;"></i>
                 <div class="fw-bold small">الحملة</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('donations.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-cash-stack fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold small">التبرعات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('beneficiaries.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-people-fill fs-3 mb-2" style="color: #3b82f6;"></i>
                 <div class="fw-bold small">المستفيدون</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('donors.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-hearts fs-3 mb-2" style="color: #f97316;"></i>
                 <div class="fw-bold small">المتبرعون</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('campaigns.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-collection fs-3 mb-2" style="color: #ec4899;"></i>
                 <div class="fw-bold small">الحملات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold small">ملفي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const cmTrendCtx = document.getElementById('cmTrendChart');
    if(cmTrendCtx){
         new Chart(cmTrendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($cm['trendLabels'] ?? []) !!},
                datasets: [{
                    label: 'التبرعات (ج.م)',
                    data: {!! json_encode($cm['donationTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(139, 92, 246, 0.6)',
                    borderColor: '#8b5cf6',
                    borderWidth: 2,
                    borderRadius: 8,
                    yAxisID: 'y'
                },{
                    label: 'المستفيدين',
                    data: {!! json_encode($cm['beneficiaryTrendData'] ?? []) !!},
                    type: 'line',
                    borderColor: '#3b82f6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: { type: 'linear', position: 'left', beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#9ca3af' } },
                    y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { display: false }, ticks: { color: '#3b82f6' } },
                    x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                }
            }
        });
    }
</script>

  @elseif($isWarehouseManager ?? false)
    {{-- Warehouse Manager Dashboard - Advanced AI-Powered --}}
    @php
        $wm = $warehouseManagerDashboardData ?? [];
    @endphp

    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);">
        <div class="hero-content">
            <div class="hero-greeting">أمين المخزن 📦</div>
            <h1 class="hero-title">إدارة المخزون</h1>
            <p class="hero-subtitle">نظام ذكي لإدارة المخزون والمستودعات</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-boxes me-2"></i>{{ $wm['totalItems'] ?? 0 }} صنف
             </div>
            <i class="bi bi-box-seam-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($wm['insights']) && count($wm['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-warning fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-warning">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($wm['insights'] as $insight)
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

    <!-- Stock Alerts -->
    @if(($wm['outOfStockCount'] ?? 0) > 0 || ($wm['lowStockCount'] ?? 0) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-2">
        @if(($wm['outOfStockCount'] ?? 0) > 0)
        <div class="col-md-6">
            <div class="glass-card p-4 border-start border-4 border-danger">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-x-circle-fill text-danger fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-danger">أصناف نفذت</h5>
                            <small style="color: #6b7280;">تحتاج لتوريد فوري</small>
                        </div>
                    </div>
                    <span class="badge bg-danger fs-5">{{ $wm['outOfStockCount'] }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach(array_slice($wm['outOfStockItems'] ?? [], 0, 3) as $item)
                    <div class="list-group-item bg-transparent px-0 py-2 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">{{ $item->name }}</div>
                                <small style="color: #9ca3af;">SKU: {{ $item->sku }}</small>
                            </div>
                            <span class="badge bg-danger">نفذ</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
        @if(($wm['lowStockCount'] ?? 0) > 0)
        <div class="col-md-6">
            <div class="glass-card p-4 border-start border-4 border-warning">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-warning fs-3 me-3"></i>
                        <div>
                            <h5 class="fw-bold mb-0 text-warning">مخزون منخفض</h5>
                            <small style="color: #6b7280;">على وشك النفاذ</small>
                        </div>
                    </div>
                    <span class="badge bg-warning fs-5">{{ $wm['lowStockCount'] }}</span>
                </div>
                <div class="list-group list-group-flush">
                    @foreach(array_slice($wm['lowStockItems'] ?? [], 0, 3) as $item)
                    <div class="list-group-item bg-transparent px-0 py-2 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <div class="fw-bold small">{{ $item->name }}</div>
                                <small style="color: #9ca3af;">المتبقي: {{ $item->current_stock ?? 0 }} {{ $item->unit }}</small>
                            </div>
                            <span class="badge bg-warning">{{ $item->current_stock ?? 0 }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
    @endif

    <!-- Main Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <a href="{{ route('items.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-boxes fs-5" style="color: #f59e0b;"></i>
                        </div>
                         <div class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">الأصناف</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f59e0b;">{{ $wm['totalItems'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">إجمالي الأصناف</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
             <a href="{{ route('inventory-transactions.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-arrow-down-circle fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">الواردات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ number_format($wm['incomingThisMonth'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
             <a href="{{ route('inventory-transactions.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-arrow-up-circle fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">الصادرات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ number_format($wm['outgoingThisMonth'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                 </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <a href="{{ route('items.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-cash-stack fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">قيمة المخزون</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ number_format($wm['totalStockValue'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م</div>
                 </div>
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Inventory Movement Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #f59e0b;"></i>حركة المخزون (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="wmInventoryChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Transactions -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2" style="color: #f59e0b;"></i>آخر العمليات</h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($wm['latestTransactions'] ?? [] as $trans)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: {{ $trans->type == 'in' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(239, 68, 68, 0.1)' }};">
                            <i class="bi bi-{{ $trans->type == 'in' ? 'arrow-down' : 'arrow-up' }}" style="color: {{ $trans->type == 'in' ? '#10b981' : '#ef4444' }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $trans->item->name ?? 'صنف' }}</div>
                            <small style="color: #6b7280;">{{ $trans->quantity }} {{ $trans->item->unit ?? '' }}</small>
                        </div>
                        <span class="badge" style="background: {{ $trans->type == 'in' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.2)' }}; color: {{ $trans->type == 'in' ? '#10b981' : '#ef4444' }};">
                            {{ $trans->type == 'in' ? 'وارد' : 'صادر' }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد عمليات</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Warehouses -->
    @if(($wm['warehousesCount'] ?? 0) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-7">
        <div class="col-12">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-building me-2" style="color: #f59e0b;"></i>المستودعات ({{ $wm['warehousesCount'] }})</h5>
                <div class="row g-3">
                    @foreach($wm['warehouses'] ?? [] as $warehouse)
                    <div class="col-md-4">
                        <div class="p-3 rounded-4" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; background: linear-gradient(135deg, #f59e0b, #d97706); color: white;">
                                    <i class="bi bi-building"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold">{{ $warehouse->name }}</div>
                                    <small style="color: #9ca3af;">{{ $warehouse->location ?? 'الموقع' }}</small>
                                </div>
                                <span class="badge" style="background: rgba(245, 158, 11, 0.2); color: #fbbf24;">{{ $warehouse->transactions_count ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-8"><i class="bi bi-lightning-charge me-2" style="color: #f59e0b;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-9">
         <div class="col-md-2 col-4">
            <a href="{{ route('items.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-boxes fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold small">الأصناف</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('inventory-transactions.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-arrow-left-right fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold small">الحركات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('warehouses.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-building fs-3 mb-2" style="color: #3b82f6;"></i>
                 <div class="fw-bold small">المستودعات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('items.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-plus-circle fs-3 mb-2" style="color: #ec4899;"></i>
                 <div class="fw-bold small">صنف جديد</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('inventory-transactions.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-plus-square fs-3 mb-2" style="color: #8b5cf6;"></i>
                 <div class="fw-bold small">حركة جديدة</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold small">ملفي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const wmInventoryCtx = document.getElementById('wmInventoryChart');
    if(wmInventoryCtx){
         new Chart(wmInventoryCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($wm['trendLabels'] ?? []) !!},
                datasets: [{
                    label: 'الواردات',
                    data: {!! json_encode($wm['incomingTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(16, 185, 129, 0.6)',
                    borderColor: '#10b981',
                    borderWidth: 2,
                    borderRadius: 8
                },{
                    label: 'الصادرات',
                    data: {!! json_encode($wm['outgoingTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(239, 68, 68, 0.6)',
                    borderColor: '#ef4444',
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

  @elseif($isLogisticsManager ?? false)
    {{-- Logistics Manager Dashboard - Advanced AI-Powered --}}
    @php
        $lm = $logisticsManagerDashboardData ?? [];
    @endphp

    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);">
        <div class="hero-content">
            <div class="hero-greeting">مدير اللوجستيات 🚚</div>
            <h1 class="hero-title">إدارة النقل والتوصيل</h1>
            <p class="hero-subtitle">نظام ذكي لإدارة المندوبين والرحلات</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-people me-2"></i>{{ $lm['activeDelegates'] ?? 0 }} مندوب نشط
             </div>
            <i class="bi bi-truck hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($lm['insights']) && count($lm['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-info fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-info">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($lm['insights'] as $insight)
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

    <!-- Main Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <a href="{{ route('delegates.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(6, 182, 212, 0.1);">
                            <i class="bi bi-people-fill fs-5" style="color: #06b6d4;"></i>
                        </div>
                         <div class="badge" style="background: rgba(6, 182, 212, 0.1); color: #06b6d4;">المندوبون</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #06b6d4;">{{ $lm['activeDelegates'] ?? 0 }}/{{ $lm['totalDelegates'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">نشط / إجمالي</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
             <a href="{{ route('delegate-trips.index') }}" class="glass-card h-100 hover-lift text-decoration-none d-block">
                  <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-check-circle fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">مكتملة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ $lm['tripsCompleted'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">رحلة مكتملة</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-hourglass-split fs-5" style="color: #f59e0b;"></i>
                        </div>
                         <div class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">معلقة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f59e0b;">{{ $lm['tripsPending'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">رحلة معلقة</div>
                 </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-cash-stack fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">التكلفة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ number_format($lm['tripsCostThisMonth'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">ج.م هذا الشهر</div>
                 </div>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Trips & Cost Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #06b6d4;"></i>الرحلات والتكلفة (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="lmTripsChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Trips -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-clock-history me-2" style="color: #06b6d4;"></i>آخر الرحلات</h5>
                </div>
                <div class="list-group list-group-flush" style="max-height: 300px; overflow-y: auto;">
                    @forelse($lm['latestTrips'] ?? [] as $trip)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: {{ $trip->status == 'completed' ? 'rgba(16, 185, 129, 0.1)' : ($trip->status == 'in_progress' ? 'rgba(59, 130, 246, 0.1)' : 'rgba(245, 158, 11, 0.1)') }};">
                            <i class="bi bi-{{ $trip->status == 'completed' ? 'check-lg' : ($trip->status == 'in_progress' ? 'arrow-repeat' : 'clock') }}" style="color: {{ $trip->status == 'completed' ? '#10b981' : ($trip->status == 'in_progress' ? '#3b82f6' : '#f59e0b') }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ $trip->delegate->name ?? 'مندوب' }}</div>
                            <small style="color: #6b7280;">{{ optional($trip->date)->format('Y-m-d') }}</small>
                        </div>
                        <span class="badge" style="background: {{ $trip->status == 'completed' ? 'rgba(16, 185, 129, 0.2)' : ($trip->status == 'in_progress' ? 'rgba(59, 130, 246, 0.2)' : 'rgba(245, 158, 11, 0.2)') }}; color: {{ $trip->status == 'completed' ? '#10b981' : ($trip->status == 'in_progress' ? '#3b82f6' : '#f59e0b') }};">
                            {{ $trip->cost ? number_format($trip->cost) . ' ج.م' : '—' }}
                        </span>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد رحلات</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Top Delegates & Routes -->
    <div class="row g-4 mb-4">
        <div class="col-lg-6 animate-slide-up animate-delay-7">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-trophy me-2" style="color: #f59e0b;"></i>أفضل المندوبين</h5>
                <div class="list-group list-group-flush">
                    @forelse($lm['topDelegates'] ?? [] as $index => $delegate)
                    <div class="list-group-item bg-transparent px-0 py-3 border-bottom" style="border-color: rgba(255,255,255,0.1) !important;">
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #06b6d4, #0891b2); color: white; font-weight: bold;">
                                {{ $index + 1 }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="fw-bold">{{ $delegate->name }}</div>
                                <small style="color: #9ca3af;">{{ $delegate->phone ?? 'لا يوجد رقم' }}</small>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold" style="color: #06b6d4;">{{ $delegate->trips_count ?? 0 }}</div>
                                <small style="color: #6b7280;">رحلة</small>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا يوجد مندوبون</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-6 animate-slide-up animate-delay-8">
            <div class="glass-card p-4">
                <h5 class="fw-bold mb-3"><i class="bi bi-signpost-2 me-2" style="color: #8b5cf6;"></i>المسارات ({{ $lm['totalRoutes'] ?? 0 }})</h5>
                <div class="row g-3">
                    @forelse($lm['routes'] ?? [] as $route)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px; background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white;">
                                    <i class="bi bi-geo-alt"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="fw-bold small">{{ $route->name }}</div>
                                    <small style="color: #9ca3af;">{{ $route->delegates_count ?? 0 }} مندوب</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="col-12 text-center py-3" style="color: #6b7280;">لا توجد مسارات</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-9"><i class="bi bi-lightning-charge me-2" style="color: #06b6d4;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-10">
         <div class="col-md-2 col-4">
            <a href="{{ route('delegates.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-people-fill fs-3 mb-2" style="color: #06b6d4;"></i>
                 <div class="fw-bold small">المندوبون</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('delegate-trips.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-truck fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold small">الرحلات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('travel-routes.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-signpost-2 fs-3 mb-2" style="color: #8b5cf6;"></i>
                 <div class="fw-bold small">المسارات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('delegates.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-plus fs-3 mb-2" style="color: #ec4899;"></i>
                 <div class="fw-bold small">مندوب جديد</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('delegate-trips.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-plus-square fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold small">رحلة جديدة</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold small">ملفي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const lmTripsCtx = document.getElementById('lmTripsChart');
    if(lmTripsCtx){
         new Chart(lmTripsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($lm['trendLabels'] ?? []) !!},
                datasets: [{
                    label: 'عدد الرحلات',
                    data: {!! json_encode($lm['tripsTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(6, 182, 212, 0.6)',
                    borderColor: '#06b6d4',
                    borderWidth: 2,
                    borderRadius: 8,
                    yAxisID: 'y'
                },{
                    label: 'التكلفة (ج.م)',
                    data: {!! json_encode($lm['costTrendData'] ?? []) !!},
                    type: 'line',
                    borderColor: '#8b5cf6',
                    backgroundColor: 'rgba(139, 92, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: { type: 'linear', position: 'left', beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#9ca3af' } },
                    y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { display: false }, ticks: { color: '#8b5cf6' } },
                    x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                }
            }
        });
    }
</script>

  @elseif($isGuestHouseManager ?? false)
    {{-- Guest House Manager Dashboard - Advanced AI-Powered --}}
    @php
        $gh = $guestHouseManagerDashboardData ?? [];
    @endphp

    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 50%, #155e75 100%);">
        <div class="hero-content">
            <div class="hero-greeting">مدير دار الضيافة 🏠</div>
            <h1 class="hero-title">{{ $gh['guestHouseName'] ?? 'دار الضيافة' }}</h1>
            <p class="hero-subtitle">إدارة الدار بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-geo-alt me-2"></i>{{ $gh['guestHouseLocation'] ?? '' }}
             </div>
            <i class="bi bi-house-heart-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($gh['insights']) && count($gh['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-info fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-info">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($gh['insights'] as $insight)
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

    {{-- Guest House Info Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(8, 145, 178, 0.08); border: 1px solid rgba(8, 145, 178, 0.2);">
                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #0891b2 0%, #155e75 100%); color: white;">
                            <i class="bi bi-house-door-fill"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">{{ $gh['guestHouseName'] ?? 'دار الضيافة' }}</h5>
                            <div class="d-flex flex-wrap gap-2">
                                <span class="badge" style="background: rgba(8, 145, 178, 0.2); color: #22d3ee; border: 1px solid rgba(8, 145, 178, 0.3);">مدير الدار</span>
                                <span class="badge" style="background: rgba(16, 185, 129, 0.2); color: #34d399;">{{ $gh['guestHouseStatus'] ?? 'نشط' }}</span>
                                @if($gh['guestHouseLocation'] ?? null)
                                <span class="badge" style="background: rgba(255,255,255,0.1); color: #94a3b8;"><i class="bi bi-geo-alt me-1"></i>{{ $gh['guestHouseLocation'] }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <a href="{{ route('guest-houses.show', $gh['guestHouseId'] ?? 1) }}" class="btn rounded-pill px-4" style="background: rgba(8, 145, 178, 0.2); color: #22d3ee; border: 1px solid rgba(8, 145, 178, 0.3);">
                        <i class="bi bi-eye me-2"></i>عرض الدار
                    </a>
                </div>

                {{-- Occupancy Gauge --}}
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 text-center" style="background: rgba(8, 145, 178, 0.1); border: 1px solid rgba(8, 145, 178, 0.2);">
                            <div class="h6 mb-3" style="color: #22d3ee;">نسبة الإشغال</div>
                            <div class="position-relative d-inline-block" style="width: 120px; height: 120px;">
                                <svg viewBox="0 0 36 36" style="width: 100%; height: 100%;">
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="3"/>
                                    <path d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="{{ ($gh['occupancyRate'] ?? 0) >= 90 ? '#ef4444' : (($gh['occupancyRate'] ?? 0) >= 70 ? '#f59e0b' : '#10b981') }}" stroke-width="3" stroke-dasharray="{{ $gh['occupancyRate'] ?? 0 }}, 100"/>
                                </svg>
                                <div class="position-absolute top-50 start-50 translate-middle">
                                    <div class="h3 fw-bold mb-0" style="color: {{ ($gh['occupancyRate'] ?? 0) >= 90 ? '#ef4444' : (($gh['occupancyRate'] ?? 0) >= 70 ? '#f59e0b' : '#10b981') }};">{{ $gh['occupancyRate'] ?? 0 }}%</div>
                                </div>
                            </div>
                            <div class="mt-2 small" style="color: #6b7280;">{{ $gh['currentGuests'] ?? 0 }} / {{ $gh['guestHouseCapacity'] ?? 0 }} ضيف</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="row g-3 h-100">
                            <div class="col-6">
                                <div class="p-3 rounded-4 text-center h-100" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                                    <i class="bi bi-cash-stack fs-4 mb-2" style="color: #10b981;"></i>
                                    <div class="h5 fw-bold mb-0" style="color: #10b981;">{{ number_format($gh['totalDonations'] ?? 0) }}</div>
                                    <div class="small" style="color: #6b7280;">التبرعات</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 text-center h-100" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                                    <i class="bi bi-cart-dash fs-4 mb-2" style="color: #ef4444;"></i>
                                    <div class="h5 fw-bold mb-0" style="color: #ef4444;">{{ number_format($gh['totalExpenses'] ?? 0) }}</div>
                                    <div class="small" style="color: #6b7280;">المصروفات</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 text-center h-100" style="background: rgba({{ ($gh['netBalance'] ?? 0) >= 0 ? '16, 185, 129' : '239, 68, 68' }}, 0.1); border: 1px solid rgba({{ ($gh['netBalance'] ?? 0) >= 0 ? '16, 185, 129' : '239, 68, 68' }}, 0.2);">
                                    <i class="bi bi-graph-up-arrow fs-4 mb-2" style="color: {{ ($gh['netBalance'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }};"></i>
                                    <div class="h5 fw-bold mb-0" style="color: {{ ($gh['netBalance'] ?? 0) >= 0 ? '#10b981' : '#ef4444' }};">{{ number_format($gh['netBalance'] ?? 0) }}</div>
                                    <div class="small" style="color: #6b7280;">الرصيد</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 text-center h-100" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                                    <i class="bi bi-heart-fill fs-4 mb-2" style="color: #a78bfa;"></i>
                                    <div class="h5 fw-bold mb-0" style="color: #a78bfa;">{{ $gh['volunteersCount'] ?? 0 }}</div>
                                    <div class="small" style="color: #6b7280;">متطوع</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <div class="glass-card h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(8, 145, 178, 0.1);">
                            <i class="bi bi-people-fill fs-5" style="color: #0891b2;"></i>
                        </div>
                         <div class="badge" style="background: rgba(8, 145, 178, 0.1); color: #22d3ee;">الضيوف</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #0891b2;">{{ $gh['beneficiariesTotal'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">إجمالي الضيوف</div>
                    <div class="mt-2 small" style="color: #9ca3af;">هذا الشهر: {{ $gh['beneficiariesThisMonth'] ?? 0 }}</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-list-task fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">المهام المعلقة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ $gh['tasksPending'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">تحتاج إنجازها</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-check-circle fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">مكتملة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ $gh['tasksCompleted'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                 </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-heart fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">المتطوعون</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ $gh['volunteersCount'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">مسجل بالدار</div>
                 </div>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Guests & Expenses Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #22d3ee;"></i>الضيوف والمصروفات (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="ghTrendChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Tasks -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2" style="color: #22d3ee;"></i>آخر المهام</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($gh['latestTasks'] ?? [] as $task)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: {{ $task->status == 'done' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                            <i class="bi bi-{{ $task->status == 'done' ? 'check-lg' : 'clock' }}" style="color: {{ $task->status == 'done' ? '#10b981' : '#f59e0b' }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ Str::limit($task->title, 20) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد مهام</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7"><i class="bi bi-lightning-charge me-2" style="color: #22d3ee;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-2 col-4">
            <a href="{{ route('guest-houses.show', $gh['guestHouseId'] ?? 1) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-house-door-fill fs-3 mb-2" style="color: #0891b2;"></i>
                 <div class="fw-bold small">الدار</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('beneficiaries.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-people-fill fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold small">الضيوف</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('tasks.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-list-task fs-3 mb-2" style="color: #ef4444;"></i>
                 <div class="fw-bold small">المهام</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('expenses.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-cart-dash fs-3 mb-2" style="color: #ec4899;"></i>
                 <div class="fw-bold small">المصروفات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('donations.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-cash-stack fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold small">التبرعات</div>
            </a>
         </div>
         <div class="col-md-2 col-4">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold small">ملفي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ghTrendCtx = document.getElementById('ghTrendChart');
    if(ghTrendCtx){
         new Chart(ghTrendCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($gh['trendLabels'] ?? []) !!},
                datasets: [{
                    label: 'الضيوف',
                    data: {!! json_encode($gh['guestsTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(8, 145, 178, 0.6)',
                    borderColor: '#0891b2',
                    borderWidth: 2,
                    borderRadius: 8,
                    yAxisID: 'y'
                },{
                    label: 'المصروفات',
                    data: {!! json_encode($gh['expensesTrendData'] ?? []) !!},
                    type: 'line',
                    borderColor: '#ef4444',
                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    yAxisID: 'y1'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: true, position: 'top' } },
                scales: {
                    y: { type: 'linear', position: 'left', beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#9ca3af' } },
                    y1: { type: 'linear', position: 'right', beginAtZero: true, grid: { display: false }, ticks: { color: '#ef4444' } },
                    x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                }
            }
        });
    }
</script>

  @elseif($isManager ?? false)
    {{-- Fallback Manager Dashboard --}}
    <div class="alert alert-info shadow-sm border-0">
        <div class="d-flex gap-2 align-items-center">
            <i class="bi bi-info-circle-fill fs-4 text-info"></i>
            <div>
                <h5 class="alert-heading fw-bold mb-1">لوحة المدير</h5>
                <p class="mb-0">مرحباً بك. يرجى التأكد من ربط حسابك بمشروع محدد للوصول إلى لوحة التحكم الكاملة.</p>
            </div>
        </div>
    </div>

  @elseif($isRepresent ?? false)
    {{-- Project Representative Dashboard --}}
    <div class="welcome-card d-flex align-items-center justify-content-between p-4 mb-4" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
      <div class="position-relative z-1">
        <h2 class="h3 fw-bold mb-1">لوحة ممثل المشروع 🤝</h2>
        <div class="d-flex align-items-center gap-2 mb-3">
             <span class="text-white-50 fs-6">مرحباً {{ $user->name }}</span>
             @if(!empty($representStats['name']))
                 <span class="badge bg-white text-success border-0">{{ $representStats['name'] }}</span>
             @else
                 <span class="badge bg-white text-muted border-0 opacity-75">لا يوجد مشروع محدد</span>
             @endif
        </div>
        
        <div class="welcome-actions d-flex gap-2">
            <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm rounded-pill px-3">
                <i class="bi bi-person-circle me-1"></i> ملفي الشخصي
            </a>
              <a href="{{ !empty($representStats['id']) ? route('projects.show', $representStats['id']) : route('projects.index') }}" class="btn btn-sm rounded-pill px-3">
                <i class="bi bi-info-circle me-1"></i> تفاصيل المشروع
            </a>
        </div>
      </div>
      <div class="d-none d-md-block position-relative z-1">
          <i class="bi bi-diagram-2-fill text-white opacity-25" style="font-size: 5rem;"></i>
      </div>
    </div>

    @if(!empty($representStats))
      <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted">إجمالي التبرعات</div>
                    <i class="bi bi-cash-coin text-success opacity-50 fs-5"></i>
                </div>
                <div class="h4 fw-bold text-dark mb-0">{{ number_format($representStats['total_donations']) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm h-100 position-relative overflow-hidden">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted">المصروفات</div>
                    <i class="bi bi-cart-x text-danger opacity-50 fs-5"></i>
                </div>
                <div class="h4 fw-bold text-dark mb-0">{{ number_format($representStats['total_expenses']) }}</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 border-0 shadow-sm h-100 position-relative overflow-hidden">
                 <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted">مهامي المعلقة</div>
                    <i class="bi bi-list-task text-primary opacity-50 fs-5"></i>
                </div>
                <div class="h4 fw-bold text-dark mb-0">{{ $representStats['tasks_count'] }}</div>
            </div>
        </div>
      </div>

      <div class="row g-3 mb-3">
          <div class="col-12">
              <div class="card kpi-card p-3 h-100">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                       <h5 class="mb-0 fw-bold">آخر أنشطة المشروع</h5>
                  </div>
                  <div class="table-responsive">
                      <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.85rem;">
                          <thead class="text-muted">
                              <tr>
                                  <th>التاريخ</th>
                                  <th>النشاط</th>
                                  <th class="text-center">التكلفة</th>
                                  <th class="text-center">الإيراد</th>
                              </tr>
                          </thead>
                          <tbody>
                              @forelse($representStats['latest_activities'] as $act)
                                <tr>
                                    <td>{{ $act->activity_date ? $act->activity_date->format('Y-m-d') : '—' }}</td>
                                    <td class="text-truncate" style="max-width: 250px;">{{ $act->name ?? '—' }}</td>
                                    <td class="text-center text-danger">{{ number_format($act->expenses) }}</td>
                                    <td class="text-center text-success">{{ number_format($act->revenue) }}</td>
                                </tr>
                              @empty
                                <tr><td colspan="4" class="text-center text-muted py-3">لا توجد أنشطة مسجلة</td></tr>
                              @endforelse
                          </tbody>
                      </table>
                  </div>
              </div>
          </div>
      </div>
    @else
      <div class="alert alert-info shadow-sm border-0">
          <div class="d-flex gap-2 align-items-center">
              <i class="bi bi-info-circle-fill fs-4 text-info"></i>
              <div>
                  <h5 class="alert-heading fw-bold mb-1">لا يوجد مشروع معين</h5>
                  <p class="mb-0">مرحباً بك. ليس لديك مشروع مسند حالياً. يرجى التواصل مع الإدارة لتعيين مشروع لك.</p>
              </div>
          </div>
      </div>
    @endif

  @elseif($isFieldResearcher ?? false)
    {{-- Field Researcher Dashboard - Advanced AI-Powered --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0891b2 0%, #0e7490 50%, #155e75 100%);">
        <div class="hero-content">
            <div class="hero-greeting">الباحث الميداني 🔍</div>
            <h1 class="hero-title">لوحة التحكم الذكية</h1>
            <p class="hero-subtitle">متابعة الزيارات الميدانية والمستفيدين بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-cpu me-2"></i>تحليل ذكي
             </div>
            <i class="bi bi-geo-alt-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($fieldResDashboardData['insights']) && count($fieldResDashboardData['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-info fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-info">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($fieldResDashboardData['insights'] as $insight)
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

    {{-- Personal Profile Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(8, 145, 178, 0.08); border: 1px solid rgba(8, 145, 178, 0.2);">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #0891b2 0%, #155e75 100%); color: white;">
                        {{ mb_substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background: rgba(8, 145, 178, 0.2); color: #22d3ee; border: 1px solid rgba(8, 145, 178, 0.3);">باحث ميداني</span>
                            @if($user->roles->count() > 0)
                                @foreach($user->roles->take(2) as $role)
                                    <span class="badge" style="background: rgba(255,255,255,0.1); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">{{ $role->name }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <a href="{{ route('users.show', $user->id) }}" class="btn rounded-pill px-4 d-none d-md-inline-flex" style="background: rgba(8, 145, 178, 0.2); color: #22d3ee; border: 1px solid rgba(8, 145, 178, 0.3);">
                        <i class="bi bi-person-circle me-2"></i>عرض الملف الكامل
                    </a>
                </div>

                <div class="row g-3">
                    {{-- Performance Rating --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <i class="bi bi-star-fill fs-4 mb-2" style="color: #f59e0b;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #f59e0b;">{{ number_format($fieldResDashboardData['avgRating'] ?? 0, 1) }}</div>
                            <div class="small" style="color: #6b7280;">متوسط التقييم</div>
                            <div class="text-warning small mt-1">
                                @for($i = 1; $i <= 5; $i++)
                                    <i class="bi bi-star{{ $i <= round($fieldResDashboardData['avgRating'] ?? 0) ? '-fill' : '' }}"></i>
                                @endfor
                            </div>
                        </div>
                    </div>

                    {{-- Completion Rate --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-speedometer2 fs-4 mb-2" style="color: #10b981;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #10b981;">{{ $fieldResDashboardData['completionRate'] ?? 0 }}%</div>
                            <div class="small" style="color: #6b7280;">معدل الإنجاز</div>
                            <div class="progress mt-2 mx-auto" style="height: 4px; max-width: 80%; background: rgba(16, 185, 129, 0.2);">
                                <div class="progress-bar" style="width: {{ $fieldResDashboardData['completionRate'] ?? 0 }}%; background: #10b981;"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Tasks Completed --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(8, 145, 178, 0.1); border: 1px solid rgba(8, 145, 178, 0.2);">
                            <i class="bi bi-check-circle fs-4 mb-2" style="color: #22d3ee;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #22d3ee;">{{ $fieldResDashboardData['tasksCompleted'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">مهام مكتملة (الشهر)</div>
                        </div>
                    </div>

                    {{-- Visits This Month --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <i class="bi bi-geo-alt fs-4 mb-2" style="color: #a78bfa;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #a78bfa;">{{ $fieldResDashboardData['visitsThisMonth'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">زيارات هذا الشهر</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Section -->
    <div class="row g-4 mb-4">
        <!-- Tasks Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <div class="glass-card h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-list-task fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">المهام المعلقة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ $fieldResDashboardData['tasksPending'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">تحتاج إنجازها</div>
                    <div class="mt-2 small d-flex justify-content-between" style="color: #9ca3af;">
                        <span>قيد التنفيذ: {{ $fieldResDashboardData['tasksInProgress'] ?? 0 }}</span>
                        <span>الإجمالي: {{ $fieldResDashboardData['tasksTotal'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Beneficiaries Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-people-fill fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">المستفيدون</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ number_format($fieldResDashboardData['beneficiariesTotal'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">إجمالي المسجلين</div>
                    <div class="mt-2 small d-flex justify-content-between" style="color: #9ca3af;">
                        <span>هذا الشهر: {{ $fieldResDashboardData['beneficiariesThisMonth'] ?? 0 }}</span>
                        <span>مقبول: {{ $fieldResDashboardData['beneficiariesAccepted'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Reviews -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(245, 158, 11, 0.1);">
                            <i class="bi bi-hourglass-split fs-5" style="color: #f59e0b;"></i>
                        </div>
                         <div class="badge" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b;">قيد المراجعة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f59e0b;">{{ $fieldResDashboardData['beneficiariesPending'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">مستفيدين ينتظرون</div>
                    <a href="{{ route('beneficiaries.index') }}?status=under_review" class="btn btn-sm mt-2 w-100" style="background: rgba(245, 158, 11, 0.1); color: #f59e0b; border: 1px solid rgba(245, 158, 11, 0.2);">
                        <i class="bi bi-arrow-left me-1"></i>عرض الكل
                    </a>
                 </div>
            </div>
        </div>

        <!-- Total Visits -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-pin-map-fill fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">الزيارات الميدانية</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ $fieldResDashboardData['visitsTotal'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">إجمالي الزيارات</div>
                    <div class="mt-2 small" style="color: #9ca3af;">
                        <i class="bi bi-clock me-1"></i>معلق: {{ $fieldResDashboardData['visitsPending'] ?? 0 }} زيارة
                    </div>
                 </div>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Performance Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #22d3ee;"></i>أداء الإنجاز (آخر 6 أشهر)</h5>
                    <span class="badge" style="background: rgba(8, 145, 178, 0.1); color: #22d3ee;">مهام مكتملة شهرياً</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="frPerformanceChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Tasks -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2" style="color: #22d3ee;"></i>آخر المهام</h5>
                    <a href="{{ route('employee-tasks.index') }}" class="small" style="color: #22d3ee;">عرض الكل</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($fieldResDashboardData['latestTasks'] ?? [] as $task)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; 
                            background: {{ $task->status == 'done' ? 'rgba(16, 185, 129, 0.1)' : ($task->status == 'in_progress' ? 'rgba(245, 158, 11, 0.1)' : 'rgba(239, 68, 68, 0.1)') }};">
                            <i class="bi bi-{{ $task->status == 'done' ? 'check-lg' : ($task->status == 'in_progress' ? 'arrow-clockwise' : 'clock') }}" 
                               style="color: {{ $task->status == 'done' ? '#10b981' : ($task->status == 'in_progress' ? '#f59e0b' : '#ef4444') }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ Str::limit($task->title, 25) }}</div>
                            <small style="color: #6b7280;">{{ optional($task->due_date)->format('Y-m-d') ?? '—' }}</small>
                        </div>
                        @if($task->rating)
                        <div class="text-warning small">
                            <i class="bi bi-star-fill"></i> {{ $task->rating }}
                        </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">
                        <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                        لا توجد مهام حديثة
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7"><i class="bi bi-lightning-charge me-2" style="color: #22d3ee;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-3 col-6">
            <a href="{{ route('beneficiaries.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-people-fill fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold">المستفيدون</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('employee-tasks.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-list-task fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold">مهامي</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('beneficiaries.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-plus-fill fs-3 mb-2" style="color: #22d3ee;"></i>
                 <div class="fw-bold">إضافة مستفيد</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold">ملفي الشخصي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Field Researcher Performance Trend Chart
    const frCtx = document.getElementById('frPerformanceChart');
    if(frCtx){
         new Chart(frCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($fieldResDashboardData['performanceTrendLabels'] ?? []) !!},
                datasets: [{
                    label: 'مهام مكتملة',
                    data: {!! json_encode($fieldResDashboardData['performanceTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(8, 145, 178, 0.6)',
                    borderColor: '#0891b2',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 40
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
                        grid: { borderDash: [5, 5], color: 'rgba(255,255,255,0.1)' },
                        ticks: { font: { family: 'Cairo' }, color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo' }, color: '#9ca3af' }
                    }
                }
            }
        });
    }
</script>

  @elseif($isReceptionist ?? false)
    {{-- Receptionist Dashboard - Advanced AI-Powered --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 50%, #be185d 100%);">
        <div class="hero-content">
            <div class="hero-greeting">الاستقبال 🎯</div>
            <h1 class="hero-title">لوحة التحكم الذكية</h1>
            <p class="hero-subtitle">إدارة الزوار والشكاوى بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-cpu me-2"></i>تحليل ذكي
             </div>
            <i class="bi bi-headset hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($receptionDashboardData['insights']) && count($receptionDashboardData['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-info">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-info fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-info">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($receptionDashboardData['insights'] as $insight)
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

    {{-- Personal Profile Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(236, 72, 153, 0.08); border: 1px solid rgba(236, 72, 153, 0.2);">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #ec4899 0%, #be185d 100%); color: white;">
                        {{ mb_substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background: rgba(236, 72, 153, 0.2); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.3);">موظف استقبال</span>
                        </div>
                    </div>
                    <a href="{{ route('users.show', $user->id) }}" class="btn rounded-pill px-4 d-none d-md-inline-flex" style="background: rgba(236, 72, 153, 0.2); color: #f472b6; border: 1px solid rgba(236, 72, 153, 0.3);">
                        <i class="bi bi-person-circle me-2"></i>عرض الملف الكامل
                    </a>
                </div>

                <div class="row g-3">
                    {{-- Beneficiaries Today --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-person-plus fs-4 mb-2" style="color: #10b981;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #10b981;">{{ $receptionDashboardData['beneficiariesToday'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">مستفيدين اليوم</div>
                        </div>
                    </div>

                    {{-- Complaints Open --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(239, 68, 68, 0.1); border: 1px solid rgba(239, 68, 68, 0.2);">
                            <i class="bi bi-exclamation-triangle fs-4 mb-2" style="color: #ef4444;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #ef4444;">{{ $receptionDashboardData['complaintsOpen'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">شكاوى مفتوحة</div>
                        </div>
                    </div>

                    {{-- Reception Logs Today --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(236, 72, 153, 0.1); border: 1px solid rgba(236, 72, 153, 0.2);">
                            <i class="bi bi-telephone fs-4 mb-2" style="color: #f472b6;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #f472b6;">{{ $receptionDashboardData['receptionLogsToday'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">سجلات اليوم</div>
                        </div>
                    </div>

                    {{-- Tasks Pending --}}
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-4 text-center h-100" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <i class="bi bi-list-task fs-4 mb-2" style="color: #f59e0b;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #f59e0b;">{{ $receptionDashboardData['tasksPending'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">مهام معلقة</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Statistics Section -->
    <div class="row g-4 mb-4">
        <!-- Beneficiaries Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <div class="glass-card h-100 hover-lift">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-people-fill fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">المستفيدون</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ $receptionDashboardData['beneficiariesThisMonth'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                    <div class="mt-2 small d-flex justify-content-between" style="color: #9ca3af;">
                        <span>اليوم: {{ $receptionDashboardData['beneficiariesToday'] ?? 0 }}</span>
                        <span>الأسبوع: {{ $receptionDashboardData['beneficiariesThisWeek'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Complaints Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-exclamation-circle fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">الشكاوى</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ $receptionDashboardData['complaintsOpen'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">مفتوحة</div>
                    <div class="mt-2 small d-flex justify-content-between" style="color: #9ca3af;">
                        <span>اليوم: {{ $receptionDashboardData['complaintsToday'] ?? 0 }}</span>
                        <span>محلولة: {{ $receptionDashboardData['complaintsResolved'] ?? 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Reception Logs -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(236, 72, 153, 0.1);">
                            <i class="bi bi-journal-text fs-5" style="color: #ec4899;"></i>
                        </div>
                         <div class="badge" style="background: rgba(236, 72, 153, 0.1); color: #ec4899;">سجلات الاستقبال</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ec4899;">{{ $receptionDashboardData['receptionLogsTotal'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                    <div class="mt-2 small" style="color: #9ca3af;">
                        <i class="bi bi-clock me-1"></i>اليوم: {{ $receptionDashboardData['receptionLogsToday'] ?? 0 }}
                    </div>
                 </div>
            </div>
        </div>

        <!-- Tasks Stats -->
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <div class="glass-card h-100 hover-lift">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-check-circle fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">المهام</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ $receptionDashboardData['tasksCompleted'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">مكتملة هذا الشهر</div>
                    <div class="mt-2 small" style="color: #9ca3af;">
                        <i class="bi bi-hourglass me-1"></i>معلقة: {{ $receptionDashboardData['tasksPending'] ?? 0 }}
                    </div>
                 </div>
             </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Weekly Trend Chart -->
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #f472b6;"></i>حركة المستفيدين (آخر 7 أيام)</h5>
                    <span class="badge" style="background: rgba(236, 72, 153, 0.1); color: #f472b6;">مستفيدين يومياً</span>
                </div>
                <div style="height: 300px;">
                    <canvas id="recWeeklyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Latest Tasks -->
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2" style="color: #f472b6;"></i>آخر المهام</h5>
                    <a href="{{ route('employee-tasks.index') }}" class="small" style="color: #f472b6;">عرض الكل</a>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($receptionDashboardData['latestTasks'] ?? [] as $task)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; 
                            background: {{ $task->status == 'done' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                            <i class="bi bi-{{ $task->status == 'done' ? 'check-lg' : 'clock' }}" 
                               style="color: {{ $task->status == 'done' ? '#10b981' : '#f59e0b' }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ Str::limit($task->title, 25) }}</div>
                            <small style="color: #6b7280;">{{ optional($task->due_date)->format('Y-m-d') ?? '—' }}</small>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">
                        <i class="bi bi-inbox fs-1 mb-2 d-block"></i>
                        لا توجد مهام حديثة
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions Grid -->
    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7"><i class="bi bi-lightning-charge me-2" style="color: #f472b6;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-3 col-6">
            <a href="{{ route('beneficiaries.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-people-fill fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold">المستفيدون</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('complaints.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-exclamation-circle fs-3 mb-2" style="color: #ef4444;"></i>
                 <div class="fw-bold">الشكاوى</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('beneficiaries.create') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-plus-fill fs-3 mb-2" style="color: #f472b6;"></i>
                 <div class="fw-bold">إضافة مستفيد</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold">ملفي الشخصي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Receptionist Weekly Trend Chart
    const recCtx = document.getElementById('recWeeklyChart');
    if(recCtx){
         new Chart(recCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode($receptionDashboardData['weeklyTrendLabels'] ?? []) !!},
                datasets: [{
                    label: 'مستفيدين',
                    data: {!! json_encode($receptionDashboardData['weeklyTrendData'] ?? []) !!},
                    borderColor: '#ec4899',
                    backgroundColor: 'rgba(236, 72, 153, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#ec4899',
                    pointBorderWidth: 2,
                    pointRadius: 5
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
                        grid: { borderDash: [5, 5], color: 'rgba(255,255,255,0.1)' },
                        ticks: { font: { family: 'Cairo' }, color: '#9ca3af' }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { family: 'Cairo' }, color: '#9ca3af' }
                    }
                }
            }
        });
    }
</script>

  @elseif($isVolunteer ?? false)
    {{-- Volunteer Dashboard - Advanced AI-Powered --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #f97316 0%, #ea580c 50%, #c2410c 100%);">
        <div class="hero-content">
            <div class="hero-greeting">المتطوع 💪</div>
            <h1 class="hero-title">لوحة التحكم الذكية</h1>
            <p class="hero-subtitle">متابعة ساعاتك ومهامك التطوعية بتقنيات الذكاء الاصطناعي</p>
        </div>
        <div class="d-flex align-items-center">
             <div class="glass-tag me-3 d-none d-md-block">
                <i class="bi bi-cpu me-2"></i>تحليل ذكي
             </div>
            <i class="bi bi-heart-fill hero-icon d-none d-md-block"></i>
        </div>
    </div>

    {{-- AI Insights Section --}}
    @if(isset($volunteerDashboardData['insights']) && count($volunteerDashboardData['insights']) > 0)
    <div class="row mb-4 animate-slide-up animate-delay-1">
        <div class="col-12">
            <div class="glass-card p-3 border-start border-4 border-warning">
                <div class="d-flex align-items-center mb-2">
                    <i class="bi bi-stars text-warning fs-4 me-2"></i>
                    <h5 class="fw-bold mb-0 text-warning">رؤى الذكاء الاصطناعي</h5>
                </div>
                <div class="row g-2">
                    @foreach($volunteerDashboardData['insights'] as $insight)
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

    {{-- Personal Profile Section --}}
    <div class="row mb-4 animate-slide-up animate-delay-2">
        <div class="col-12">
            <div class="glass-card p-4" style="background: rgba(249, 115, 22, 0.08); border: 1px solid rgba(249, 115, 22, 0.2);">
                <div class="d-flex align-items-center mb-4">
                    <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 60px; height: 60px; font-size: 1.5rem; background: linear-gradient(135deg, #f97316 0%, #c2410c 100%); color: white;">
                        {{ mb_substr($user->name ?? 'U', 0, 1) }}
                    </div>
                    <div class="flex-grow-1">
                        <h5 class="fw-bold mb-1">{{ $user->name }}</h5>
                        <div class="d-flex flex-wrap gap-2">
                            <span class="badge" style="background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3);">متطوع</span>
                            <span class="badge" style="background: rgba(255,255,255,0.1); color: #94a3b8; border: 1px solid rgba(255,255,255,0.1);">
                                <i class="bi bi-calendar me-1"></i>منذ {{ $volunteerDashboardData['joinDate'] ?? '' }}
                            </span>
                        </div>
                    </div>
                    <a href="{{ route('users.show', $user->id) }}" class="btn rounded-pill px-4 d-none d-md-inline-flex" style="background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.3);">
                        <i class="bi bi-person-circle me-2"></i>عرض الملف الكامل
                    </a>
                </div>

                <div class="row g-3">
                    <div class="col-md-3 col-6">
                        <a href="{{ route('volunteer-hours.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(249, 115, 22, 0.1); border: 1px solid rgba(249, 115, 22, 0.2);">
                            <i class="bi bi-clock-history fs-4 mb-2" style="color: #f97316;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #f97316;">{{ number_format($volunteerDashboardData['totalHours'] ?? 0, 1) }}</div>
                            <div class="small" style="color: #6b7280;">إجمالي الساعات</div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('volunteer-hours.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(16, 185, 129, 0.1); border: 1px solid rgba(16, 185, 129, 0.2);">
                            <i class="bi bi-graph-up-arrow fs-4 mb-2" style="color: #10b981;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #10b981;">{{ number_format($volunteerDashboardData['hoursThisMonth'] ?? 0, 1) }}</div>
                            <div class="small" style="color: #6b7280;">ساعات هذا الشهر</div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('volunteer-attendance.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(139, 92, 246, 0.1); border: 1px solid rgba(139, 92, 246, 0.2);">
                            <i class="bi bi-calendar-check fs-4 mb-2" style="color: #a78bfa;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #a78bfa;">{{ $volunteerDashboardData['attendanceThisMonth'] ?? 0 }}</div>
                            <div class="small" style="color: #6b7280;">حضور هذا الشهر</div>
                        </a>
                    </div>
                    <div class="col-md-3 col-6">
                        <a href="{{ route('volunteer-tasks.index') }}" class="p-3 rounded-4 text-center h-100 d-block text-decoration-none hover-lift" style="background: rgba(245, 158, 11, 0.1); border: 1px solid rgba(245, 158, 11, 0.2);">
                            <i class="bi bi-star-fill fs-4 mb-2" style="color: #f59e0b;"></i>
                            <div class="h4 fw-bold mb-0" style="color: #f59e0b;">{{ number_format($volunteerDashboardData['avgRating'] ?? 0, 1) }}</div>
                            <div class="small" style="color: #6b7280;">متوسط التقييم</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-1">
            <a href="{{ route('volunteer-tasks.index') }}" class="glass-card h-100 hover-lift d-block text-decoration-none">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(239, 68, 68, 0.1);">
                            <i class="bi bi-list-task fs-5" style="color: #ef4444;"></i>
                        </div>
                         <div class="badge" style="background: rgba(239, 68, 68, 0.1); color: #ef4444;">المهام المعلقة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #ef4444;">{{ $volunteerDashboardData['tasksPending'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">تحتاج إنجازها</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-2">
            <a href="{{ route('volunteer-tasks.index') }}?status=done" class="glass-card h-100 hover-lift d-block text-decoration-none">
                 <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(16, 185, 129, 0.1);">
                            <i class="bi bi-check-circle fs-5" style="color: #10b981;"></i>
                        </div>
                         <div class="badge" style="background: rgba(16, 185, 129, 0.1); color: #10b981;">مكتملة</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #10b981;">{{ $volunteerDashboardData['tasksCompleted'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">هذا الشهر</div>
                </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-3">
            <a href="{{ route('volunteer-hours.index') }}" class="glass-card h-100 hover-lift d-block text-decoration-none">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(249, 115, 22, 0.1);">
                            <i class="bi bi-hourglass-split fs-5" style="color: #f97316;"></i>
                        </div>
                         <div class="badge" style="background: rgba(249, 115, 22, 0.1); color: #f97316;">إجمالي الساعات</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #f97316;">{{ number_format($volunteerDashboardData['totalHours'] ?? 0) }}</div>
                    <div class="small" style="color: #6b7280;">ساعة تطوعية</div>
                 </div>
            </a>
        </div>
        <div class="col-xl-3 col-md-6 animate-slide-up animate-delay-4">
             <a href="{{ route('volunteer-attendance.index') }}" class="glass-card h-100 hover-lift d-block text-decoration-none">
                 <div class="card-body p-4">
                     <div class="d-flex justify-content-between align-items-center mb-3">
                         <div class="icon-circle rounded-circle d-flex align-items-center justify-content-center" style="width: 45px; height: 45px; background: rgba(139, 92, 246, 0.1);">
                            <i class="bi bi-calendar2-check fs-5" style="color: #a78bfa;"></i>
                        </div>
                         <div class="badge" style="background: rgba(139, 92, 246, 0.1); color: #a78bfa;">الحضور</div>
                    </div>
                    <div class="h2 fw-bold mb-0" style="color: #a78bfa;">{{ $volunteerDashboardData['attendanceThisMonth'] ?? 0 }}</div>
                    <div class="small" style="color: #6b7280;">يوم هذا الشهر</div>
                 </div>
             </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-lg-8 animate-slide-up animate-delay-5">
            <div class="glass-card h-100 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0"><i class="bi bi-graph-up me-2" style="color: #fb923c;"></i>ساعات التطوع (آخر 6 أشهر)</h5>
                </div>
                <div style="height: 300px;">
                    <canvas id="volHoursChart"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4 animate-slide-up animate-delay-6">
            <div class="glass-card h-100 p-0 overflow-hidden">
                <div class="p-4 d-flex justify-content-between align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.1);">
                    <h5 class="fw-bold mb-0"><i class="bi bi-list-check me-2" style="color: #fb923c;"></i>مهامي</h5>
                </div>
                <div class="list-group list-group-flush">
                    @forelse($volunteerDashboardData['latestTasks'] ?? [] as $task)
                    <div class="list-group-item bg-transparent py-3 px-4 d-flex align-items-center" style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; background: {{ $task->status == 'done' ? 'rgba(16, 185, 129, 0.1)' : 'rgba(245, 158, 11, 0.1)' }};">
                            <i class="bi bi-{{ $task->status == 'done' ? 'check-lg' : 'clock' }}" style="color: {{ $task->status == 'done' ? '#10b981' : '#f59e0b' }};"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-bold small">{{ Str::limit($task->title, 25) }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5" style="color: #6b7280;">لا توجد مهام</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <h5 class="fw-bold mb-3 animate-slide-up animate-delay-7"><i class="bi bi-lightning-charge me-2" style="color: #fb923c;"></i>وصول سريع</h5>
    <div class="row g-3 animate-slide-up animate-delay-8">
         <div class="col-md-3 col-6">
            <a href="{{ route('volunteer-tasks.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-list-task fs-3 mb-2" style="color: #f97316;"></i>
                 <div class="fw-bold">مهامي</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('volunteer-hours.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-clock-history fs-3 mb-2" style="color: #10b981;"></i>
                 <div class="fw-bold">ساعاتي</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('volunteer-attendance.index') }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-calendar-check fs-3 mb-2" style="color: #a78bfa;"></i>
                 <div class="fw-bold">الحضور</div>
            </a>
         </div>
         <div class="col-md-3 col-6">
            <a href="{{ route('users.show', $user->id) }}" class="glass-card p-3 text-center text-decoration-none d-block hover-lift">
                 <i class="bi bi-person-circle fs-3 mb-2" style="color: #f59e0b;"></i>
                 <div class="fw-bold">ملفي</div>
            </a>
         </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const volCtx = document.getElementById('volHoursChart');
    if(volCtx){
         new Chart(volCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($volunteerDashboardData['hoursTrendLabels'] ?? []) !!},
                datasets: [{
                    label: 'ساعات',
                    data: {!! json_encode($volunteerDashboardData['hoursTrendData'] ?? []) !!},
                    backgroundColor: 'rgba(249, 115, 22, 0.6)',
                    borderColor: '#f97316',
                    borderWidth: 2,
                    borderRadius: 8,
                    barThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(255,255,255,0.1)' }, ticks: { color: '#9ca3af' } },
                    x: { grid: { display: false }, ticks: { color: '#9ca3af' } }
                }
            }
        });
    }
</script>

  @elseif($isAdmin ?? false)
    <div class="d-flex justify-content-between align-items-center mb-4 dashboard-header">
      <h2 class="h4 fw-bold text-primary-custom">لوحة التحكم</h2>

      <div class="d-flex gap-3 align-items-center">
           {{-- Attendance buttons removed --}}

        <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-mobile-icon d-md-none">
            <i class="bi bi-person-circle"></i>
        </a>

        <div class="text-muted small d-none d-md-block">{{ date('Y-m-d') }}</div>
      </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md"><a href="{{ route('donors.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">المتبرعون</div>
          <div class="display-6 fw-bold text-dark">{{ $donorsCount }}</div>
        </a></div>
      <div class="col-md"><a href="{{ route('beneficiaries.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">المستفيدون</div>
          <div class="display-6 fw-bold text-dark">{{ $beneficiariesCount }}</div>
        </a></div>
      <div class="col-md"><a href="{{ route('warehouses.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">المخازن</div>
          <div class="display-6 fw-bold text-dark">{{ $warehousesCount }}</div>
        </a></div>
      <div class="col-md"><a href="{{ route('volunteers.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">المتطوعون</div>
          <div class="display-6 fw-bold text-dark">{{ $volunteersCount }}</div>
        </a></div>
      <div class="col-md"><a href="{{ route('users.index') }}?type=employee"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">الموظفون</div>
          <div class="display-6 fw-bold text-dark">{{ $employeesCount }}</div>
        </a></div>
    </div>

    <div class="row g-3 mb-3 animate-slide-up">
        <div class="col-md-4">
            <div class="glass-card p-4 h-100" style="background: rgba(15, 23, 42, 0.6); border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-start mb-4">
                    <span class="badge" style="background: #ef8e19; color: white; padding: 6px 12px; border-radius: 6px; font-weight: 500;">طلبات معلقة</span>
                    <div class="icon-circle" style="background: rgba(255,255,255,0.05); width: 40px; height: 40px; display: flex; align-items:center; justify-content: center; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1);">
                        <i class="bi bi-inbox-fill text-white-50"></i>
                    </div>
                </div>
                <div class="d-flex align-items-center justify-content-center py-3 mt-2">
                    <a href="{{ route('tasks.index') }}" class="text-center px-4 text-decoration-none hover-lift">
                        <div class="h2 fw-bold text-white mb-0" style="text-shadow: 0 0 20px rgba(255,255,255,0.2);">{{ $openTasks }}</div>
                        <div class="small text-muted" style="letter-spacing: 1px;">مهام</div>
                    </a>
                    <div style="width: 1px; height: 50px; background: linear-gradient(to bottom, transparent, rgba(255,255,255,0.1), transparent);"></div>
                    <a href="{{ route('leaves.index') }}" class="text-center px-4 text-decoration-none hover-lift">
                        <div class="h2 fw-bold text-white mb-0" style="text-shadow: 0 0 20px rgba(255,255,255,0.2);">{{ $pendingLeaves }}</div>
                        <div class="small text-muted" style="letter-spacing: 1px;">إجازات</div>
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
             <div class="glass-card p-4 h-100" style="border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-white"><i class="bi bi-person-walking me-2 text-primary"></i>من في إجازة حالياً</h6>
                    <a href="{{ route('leaves.index') }}" class="x-small text-muted text-decoration-none">عرض الكل</a>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($currentlyOnLeave as $leave)
                        <div class="list-group-item bg-transparent border-0 px-0 py-2 d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 35px; height: 35px; font-size: 0.9rem; background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%); color: white;">
                                {{ mb_substr($leave->user->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold text-white">{{ $leave->user->name }}</div>
                                <div style="font-size: 0.7rem;" class="text-muted">حتى {{ $leave->end_date }}</div>
                            </div>
                            <span class="badge bg-primary-subtle text-primary border-0 rounded-pill x-small">{{ $leave->type }}</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-info-circle d-block mb-2 fs-4 op-50"></i>
                            لا يوجد أحد في إجازة اليوم
                        </div>
                    @endforelse
                </div>
             </div>
        </div>

        <div class="col-md-4">
             <div class="glass-card p-4 h-100" style="border: 1px solid rgba(255,255,255,0.05);">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0 text-white"><i class="bi bi-list-stars me-2 text-warning"></i>المهام المفتوحة</h6>
                    <a href="{{ route('tasks.index') }}" class="x-small text-muted text-decoration-none">عرض الكل</a>
                </div>
                <div class="list-group list-group-flush bg-transparent">
                    @forelse($latestTasks->take(4) as $task)
                        <div class="list-group-item bg-transparent border-0 px-0 py-2 d-flex align-items-center">
                            <div class="rounded-circle d-flex align-items-center justify-content-center me-3" 
                                 style="width: 8px; height: 8px; background: {{ $task->status == 'in_progress' ? '#f59e0b' : '#3b82f6' }}; box-shadow: 0 0 10px {{ $task->status == 'in_progress' ? '#f59e0b' : '#3b82f6' }};">
                            </div>
                            <div class="flex-grow-1">
                                <div class="small fw-bold text-white text-truncate" style="max-width: 150px;">{{ $task->title }}</div>
                                <div style="font-size: 0.7rem;" class="text-muted">{{ $task->created_at->diffForHumans() }}</div>
                            </div>
                            <span class="x-small text-muted">{{ $task->status_label ?? $task->status }}</span>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted small">
                            <i class="bi bi-check2-all d-block mb-2 fs-4 op-50 text-success"></i>
                            لا توجد مهام مفتوحة
                        </div>
                    @endforelse
                </div>
             </div>
        </div>
    </div>

    <div class="row g-3 mb-3">
      <div class="col-md-3"><a href="{{ route('donations.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">تبرعات نقدية (هذا الشهر)</div>
          <div class="h4 mb-0 fw-bold text-primary-custom">{{ number_format($cashMonth, 2) }}</div>
        </a></div>
      <div class="col-md-3"><a href="{{ route('donations.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">تبرعات عينية (هذا الشهر)</div>
          <div class="h4 mb-0 fw-bold text-success">{{ number_format($inKindMonth, 2) }}</div>
        </a></div>
      <div class="col-md-3"><a href="{{ route('expenses.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">مصروفات (هذا الشهر)</div>
          <div class="h4 mb-0 fw-bold text-danger">{{ number_format($expensesMonth, 2) }}</div>
        </a></div>
      <div class="col-md-3"><a href="{{ route('reports.index') }}"
          class="card kpi-card p-3 text-center text-decoration-none">
          <div class="small text-muted mb-1">صافي التدفق</div>
          <div class="h4 mb-0 fw-bold text-dark">{{ number_format($netFlowMonth, 2) }}</div>
        </a></div>
    </div>

    @php $maxVal = max(array_merge($cashSeries, $inKindSeries));
    $maxVal = $maxVal > 0 ? $maxVal : 1; @endphp
    <div class="row g-3 mb-3">
      <div class="col-md-8">
        <div class="card chart-wrap p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">توزيع التبرعات آخر 12 شهرًا</h5>
            <span class="chip">نقدي مقابل عيني</span>
          </div>
          <div class="bar-grid">
            @for($i = 0; $i < count($months); $i++)
              @php $cm = $cashSeries[$i];
                $km = $inKindSeries[$i];
                $ch = max(round(($cm / $maxVal) * 180), 4);
              $kh = max(round(($km / $maxVal) * 180), 4); @endphp
              <div style="display:flex;gap:4px;align-items:flex-end;flex:1;justify-content:center;">
                <div class="bar cash" style="height:{{ $ch }}px" title="{{ $months[$i] }}: {{ number_format($cm, 2) }}"
                  data-bs-toggle="tooltip"></div>
                <div class="bar kind" style="height:{{ $kh }}px" title="{{ $months[$i] }}: {{ number_format($km, 2) }}"
                  data-bs-toggle="tooltip"></div>
              </div>
            @endfor
          </div>
          <div class="bar-labels mt-2 small text-muted text-center">
            @foreach($months as $m)<span style="flex:1">{{ $m }}</span>@endforeach
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card kpi-card p-3 h-100">
          <h5 class="mb-3 fw-bold">حالة المستفيدين</h5>
          <ul class="list-unstyled list-simple mb-0">
            @foreach($beneficiaryStatus as $s)
              <li><span>{{ $s['status'] }}</span><span class="chip">{{ $s['count'] }}</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>

    <div class="row g-3">
      <div class="col-md-8">
        <div class="card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">آخر سجلات النظام</h5>
          </div>
          @if($isAdmin)
            <div class="table-responsive">
              <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                  <tr>
                    <th>#</th>
                    <th>التاريخ</th>
                    <th>المستخدم</th>
                    <th>الطريقة</th>
                    <th>المسار</th>
                    <th>الحالة</th>
                    <th>IP</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($audits as $a)
                    <tr>
                      <td>{{ $a->id }}</td>
                      <td>{{ optional($a->created_at)->format('Y-m-d H:i') }}</td>
                      <td>{{ optional($audUserMap->get($a->user_id))->name ?? '—' }}</td>
                      <td><span class="badge bg-secondary-subtle text-body border">{{ $a->method }}</span></td>
                      <td class="small text-muted">{{ Str::limit($a->path, 20) }}</td>
                      <td>{{ $a->status_code ?? '—' }}</td>
                      <td><span class="small text-muted">{{ $a->ip ?? '—' }}</span></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <div class="text-muted py-3 text-center">لا تُعرض السجلات إلا للمسؤولين</div>
          @endif
        </div>
      </div>
      <div class="col-md-4">
        <div class="card p-3 h-100">
          <h5 class="mb-3 fw-bold">التنبيهات</h5>
          @forelse($notifications as $n)
            <div class="alert alert-{{ $n['type'] }} py-2 mb-2 small shadow-sm border-0">{{ $n['text'] }}</div>
          @empty
            <div class="text-muted text-center py-4">لا توجد تنبيهات</div>
          @endforelse
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">آخر التبرعات</h5>
          </div>
          <ul class="list-unstyled list-simple mb-0">
            @foreach($latestDonations as $d)
              <li>
                <div>
                  <div class="fw-bold">
                    @if($d->donor_id)
                      <a href="{{ route('donors.show', $d->donor_id) }}"
                        class="text-decoration-none text-dark">{{ $d->donor?->name ?? '—' }}</a>
                    @else
                      {{ $d->donor?->name ?? '—' }}
                    @endif
                  </div>
                  <div class="small text-muted">{{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}</div>
                </div>
                <span
                  class="chip">{{ ($d->type === 'cash') ? number_format($d->amount, 2) : number_format($d->estimated_value, 2) }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">آخر المهام</h5>
          </div>
          <ul class="list-unstyled list-simple mb-0">
            @foreach($latestTasks as $t)
              <li>
                <div>
                  <div class="fw-bold">{{ $t->title ?? ('مهمة #' . $t->id) }}</div>
                  @if($t->due_date)
                    <div class="small {{ $t->due_date < now() && $t->status != 'done' ? 'text-danger' : 'text-muted' }}">
                      <i class="bi bi-calendar-event me-1"></i>{{ $t->due_date->format('Y-m-d') }}
                    </div>
                  @endif
                </div>
                <span class="badge bg-secondary-subtle text-body border">{{ $t->status ?? '—' }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">حضور اليوم</h5>
          </div>
          <ul class="list-unstyled list-simple mb-0">
            @foreach($latestAttendance as $a)
              <li><span>{{ $a->user?->name ?? '—' }}</span><span
                  class="small text-muted">{{ $a->date?->format('H:i') ?? '—' }}</span></li>
            @endforeach
          </ul>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <h5 class="mb-3 fw-bold">أفضل المتبرعين (هذا الشهر)</h5>
          <ul class="list-unstyled list-simple mb-0">
            @foreach($topDonors as $td)
              <li>
                @if(!empty($td['id']))
                  <a href="{{ route('donors.show', $td['id']) }}" class="text-decoration-none text-dark">{{ $td['name'] }}</a>
                @else
                  <span>{{ $td['name'] }}</span>
                @endif
                <span class="chip">{{ number_format($td['total'], 2) }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <h5 class="mb-3 fw-bold">توزيع هذا الشهر</h5>
          <div class="d-flex gap-3 align-items-center justify-content-center py-4">
            <div class="text-center">
              <h6 class="text-muted">نقدي</h6>
              <div class="h3 text-primary">{{ $cashMonthPct }}%</div>
            </div>
            <div class="vr"></div>
            <div class="text-center">
              <h6 class="text-muted">عيني</h6>
              <div class="h3 text-success">{{ $inKindMonthPct }}%</div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-12">
        <div class="card p-3">
          <h5 class="mb-3 fw-bold">لوحة شرف المتطوعين (هذا الشهر)</h5>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="bg-transparent">
                <tr>
                  <th>المتطوع</th>
                  <th class="text-center">الساعات</th>
                  <th class="text-center">الحضور</th>
                  <th class="text-center">المهام المنجزة</th>
                  <th class="text-center">النقاط</th>
                  <th style="width:200px">التقييم</th>
                </tr>
              </thead>
              <tbody>
                @foreach(collect($evaluations)->sortByDesc('score')->take(5) as $ev)
                  <tr>
                    <td class="fw-bold">{{ $ev['name'] }}</td>
                    <td class="text-center">{{ number_format($ev['hours'], 1) }}</td>
                    <td class="text-center">{{ $ev['attendance'] }}</td>
                    <td class="text-center">{{ $ev['tasks'] }}</td>
                    <td class="text-center fw-bold text-primary">{{ $ev['score'] }}</td>
                    <td>
                      <div class="progress" style="height: 6px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ min($ev['score'], 100) }}%">
                        </div>
                      </div>
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">آخر المصروفات</h5>
          </div>
          <ul class="list-unstyled list-simple mb-0">
            @foreach($latestExpenses as $ex)
              <li>
                <div>
                  <div class="fw-bold">{{ $ex->category ?? 'غير محدد' }}</div>
                  <div class="small text-muted">{{ optional($ex->created_at)->format('Y-m-d') }} | {{ $ex->creator?->name ?? '—' }}
                  </div>
                </div>
                <span class="chip text-danger">{{ number_format($ex->amount, 2) }} {{ $ex->currency }}</span>
              </li>
            @endforeach
          </ul>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <h5 class="mb-3 fw-bold">توزيع المصروفات (هذا الشهر)</h5>
          @if(count($expenseByCat) > 0)
            <ul class="list-unstyled list-simple mb-0">
              @foreach($expenseByCat as $ec)
                <li>
                  <span>{{ $ec->category ?? 'أخرى' }}</span>
                  <span class="fw-bold text-dark">{{ number_format($ec->total, 2) }}</span>
                </li>
              @endforeach
            </ul>
          @else
            <div class="text-center py-4 text-muted">لا توجد مصروفات هذا الشهر</div>
          @endif
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">الحملات النشطة</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
              <thead class="bg-transparent text-muted">
                <tr>
                  <th class="text-start">الحملة</th>
                  <th>التبرعات</th>
                  <th>المصروفات</th>
                  <th>الصافي</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activeCampaigns as $camp)
                  <tr>
                    <td class="text-start fw-bold"><a href="{{ route('campaigns.show', $camp) }}"
                        class="text-decoration-none text-dark">{{ $camp->name }}</a></td>
                    <td class="text-success">{{ number_format($camp->total_donations) }}</td>
                    <td class="text-danger">{{ number_format($camp->total_expenses) }}</td>
                    <td class="fw-bold {{ $camp->net_balance >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ number_format($camp->net_balance) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-2">لا توجد حملات نشطة حالياً</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">المشاريع النشطة</h5>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center" style="font-size: 0.85rem;">
              <thead class="bg-transparent text-muted">
                <tr>
                  <th class="text-start">المشروع</th>
                  <th>التبرعات</th>
                  <th>المصروفات</th>
                  <th>الصافي</th>
                </tr>
              </thead>
              <tbody>
                @forelse($activeProjects as $proj)
                  <tr>
                    <td class="text-start fw-bold"><a href="{{ route('projects.show', $proj) }}"
                        class="text-decoration-none text-dark">{{ $proj->name }}</a></td>
                    <td class="text-success">{{ number_format($proj->total_donations) }}</td>
                    <td class="text-danger">{{ number_format($proj->total_expenses) }}</td>
                    <td class="fw-bold {{ $proj->net_balance >= 0 ? 'text-success' : 'text-danger' }}">
                      {{ number_format($proj->net_balance) }}</td>
                  </tr>
                @empty
                  <tr>
                    <td colspan="4" class="text-center text-muted py-2">لا توجد مشاريع نشطة حالياً</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="row g-3 mt-1">
      <div class="col-md-6">
        <div class="card kpi-card p-3 h-100">
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0 fw-bold">أعلى 5 أصناف في المخزون</h5>
            <a class="btn btn-sm btn-outline-secondary rounded-pill px-3" href="{{ route('warehouses.index') }}">عرض
              الكل</a>
          </div>
          <ul class="list-unstyled list-simple mb-0">
            @forelse($inventoryLevels as $inv)
              <li>
                <div>
                  <div class="fw-bold">{{ $inv->item->name ?? '—' }}</div>
                  <div class="small text-muted">{{ $inv->item->category ?? '' }}</div>
                </div>
                <span class="chip">{{ $inv->current_stock }}</span>
              </li>
            @empty
              <li class="text-center text-muted py-2">المخزون فارغ</li>
            @endforelse
          </ul>
        </div>
      </div>
    </div>
  @else
    {{-- Generic Employee / No Specific Dashboard Role --}}
    <div class="d-flex justify-content-between align-items-center mb-4 dashboard-header">
       <h2 class="h4 fw-bold text-primary-custom">لوحة التحكم</h2>
       <div class="d-flex gap-3 align-items-center">
           <a href="{{ route('users.show', $user->id) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 btn-mobile-icon">
               <i class="bi bi-person-circle me-1"></i> <span>ملفي الشخصي</span>
           </a>
           <div class="text-muted small d-none d-md-block">{{ date('Y-m-d') }}</div>
       </div>
    </div>
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="alert alert-light shadow-sm border-0 text-center p-5">
                <div class="mb-4">
                    @if($user->profile_photo_path)
                      <img src="{{ $user->image_url }}" alt="{{ $user->name }}" class="rounded-circle shadow-sm" style="width: 80px; height: 80px; object-fit: cover;">
                    @else
                      <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto" style="width: 80px; height: 80px; font-size: 2rem; font-weight: bold;">
                           {{ strtoupper(substr($user->name, 0, 1)) }}
                      </div>
                    @endif
                </div>
                <h4 class="alert-heading fw-bold mb-3">مرحباً بك، {{ $user->name }}</h4>
                <p class="text-muted lead mb-4">
                    أهلاً بك في نظام إدارة مؤسسة إنسان.<br>
                    يمكنك الوصول إلى الأدوات والوظائف المتاحة لك من خلال القائمة الجانبية.
                </p>
                @if($user->roles->count() > 0)
                  <div class="d-inline-block px-4 py-2 bg-body-tertiary rounded-pill border">
                      <small class="text-muted fw-bold">الأدوار المسندة: </small>
                      <span class="text-primary fw-bold">{{ $user->roles->pluck('name')->implode('، ') }}</span>
                  </div>
                @endif
            </div>
        </div>
    </div>
  @endif
@endsection


