@extends('layouts.app')

@section('styles')
<style>
    .kpi-card {
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

    .kpi-card:hover {
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

    .kpi-card:hover .stat-bg-icon {
        transform: rotate(0deg) scale(1.1);
        opacity: 0.1;
    }

    .stat-info .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }
    .stat-success .stat-icon { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
    .stat-warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stat-danger .stat-icon { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
    .stat-purple .stat-icon { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
</style>
@endsection

@section('content')

    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up mb-4 bg-primary" style="background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); padding: 2rem; border-radius: var(--radius-lg); position: relative; overflow: hidden; box-shadow: 0 10px 25px rgba(5, 150, 105, 0.2);">
        <div class="hero-content position-relative z-1 text-white">
            <h1 class="hero-title fw-bold text-white">لوحة تحكم اللوجيستك 🚚</h1>
            <p class="hero-subtitle text-white-50 fs-6">ملخص حركة المندوبين، خطوط السير والرحلات لليوم {{ date('Y-m-d') }}</p>
            <div class="hero-actions d-flex gap-2 mt-3">
                <a href="{{ route('delegates.index') }}" class="btn bg-white btn-sm rounded-pill px-4 text-primary fw-bold shadow-sm hover-lift border-0">
                    <i class="bi bi-people-fill me-1"></i> إدارة المندوبين
                </a>
                <a href="{{ route('travel-routes.index') }}" class="btn btn-outline-light btn-sm rounded-pill px-4 shadow-sm hover-lift">
                    <i class="bi bi-geo-alt me-1"></i> إدارة المسارات
                </a>
            </div>
        </div>
        <i class="bi bi-signpost-split position-absolute z-0 text-white opacity-25" style="top: 10%; right: 5%; font-size: 8rem; transform: rotate(15deg);"></i>
    </div>

    {{-- Primary Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg animate-slide-up animate-delay-1">
            <a href="{{ route('delegates.index') }}" class="kpi-card stat-info text-decoration-none">
                <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
                <div class="stat-label">المندوبون (النشطين)</div>
                <div class="stat-value">{{ number_format($activeDelegatesCount) }} <span class="fs-6 text-muted fw-normal">من {{ number_format($delegatesCount) }}</span></div>
                <i class="bi bi-person-badge-fill stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-2">
            <a href="{{ route('travel-routes.index') }}" class="kpi-card stat-success text-decoration-none">
                <div class="stat-icon"><i class="bi bi-geo"></i></div>
                <div class="stat-label">خطوط السير المعرفة</div>
                <div class="stat-value">{{ number_format($routesCount) }}</div>
                <i class="bi bi-geo stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-3">
            <a href="{{ route('trips.index') }}" class="kpi-card stat-purple text-decoration-none">
                <div class="stat-icon"><i class="bi bi-truck"></i></div>
                <div class="stat-label">إجمالي الرحلات</div>
                <div class="stat-value">{{ number_format($tripsCount) }}</div>
                <i class="bi bi-truck stat-bg-icon"></i>
            </a>
        </div>
        <div class="col-6 col-lg animate-slide-up animate-delay-4">
            <div class="kpi-card stat-danger text-decoration-none">
                <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
                <div class="stat-label">إجمالي تكلفة الرحلات</div>
                <div class="stat-value">{{ number_format($totalCost) }}</div>
                <i class="bi bi-currency-dollar stat-bg-icon"></i>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-md-6 animate-slide-up animate-delay-2">
            <a href="{{ route('kafr-el-sheikh-deliveries.index') }}" class="kpi-card stat-warning text-decoration-none hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted fw-bold">توصيلات كفر الشيخ</div>
                    <div class="bg-warning bg-opacity-10 text-warning p-2 rounded-circle">
                        <i class="bi bi-box-seam fs-5"></i>
                    </div>
                </div>
                <div class="h3 fw-bold mb-0 text-main">{{ number_format($deliveriesCount) }}</div>
            </a>
        </div>
        <div class="col-md-6 animate-slide-up animate-delay-3">
            <a href="{{ route('kafr-el-sheikh-services.index') }}" class="kpi-card stat-info text-decoration-none hover-lift">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <div class="small text-muted fw-bold">خدمات كفر الشيخ</div>
                    <div class="bg-info bg-opacity-10 text-info p-2 rounded-circle">
                        <i class="bi bi-tools fs-5"></i>
                    </div>
                </div>
                <div class="h3 fw-bold mb-0 text-main">{{ number_format($servicesCount) }}</div>
            </a>
        </div>
    </div>

    <div class="row g-3">
      <div class="col-md-12">
        <div class="card border-0 shadow-sm h-100 overflow-visible animate-slide-up animate-delay-5">
          <div class="card-header bg-white py-3 border-0 overflow-visible d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold"><i class="bi bi-clock-history me-2 text-primary"></i> أحدث الرحلات</h5>
            <a href="{{ route('trips.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">عرض الكل</a>
          </div>
          <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 text-center">
              <thead class="bg-light">
                <tr>
                  <th class="py-3 text-secondary small text-uppercase">المندوب</th>
                  <th class="py-3 text-secondary small text-uppercase">التاريخ</th>
                  <th class="py-3 text-secondary small text-uppercase">المسافة</th>
                  <th class="py-3 text-secondary small text-uppercase">التكلفة الإجمالية</th>
                  <th class="py-3 text-secondary small text-uppercase">الحالة</th>
                </tr>
              </thead>
              <tbody>
                @forelse($recentTrips as $trip)
                  <tr>
                    <td class="fw-bold">
                        <a href="{{ route('delegates.show', $trip->delegate_id) }}" class="text-decoration-none text-dark">
                            {{ optional($trip->delegate)->name ?? 'مندوب غير محدد' }}
                        </a>
                    </td>
                    <td class="text-nowrap">{{ optional($trip->date)->format('Y-m-d') }}</td>
                    <td>{{ $trip->distance_km ?? 0 }} كم</td>
                    <td class="text-danger fw-bold">{{ number_format($trip->calculateTotalCost(), 2) }}</td>
                    <td>
                        <span class="badge {{ $trip->status === 'completed' ? 'bg-success' : 'bg-warning text-dark' }} rounded-pill">
                            {{ $trip->status === 'completed' ? 'منجزة' : ($trip->status === 'cancelled' ? 'ملغية' : 'قيد التنفيذ') }}
                        </span>
                    </td>
                  </tr>
                @empty
                  <tr>
                      <td colspan="5" class="py-4 text-muted text-center">لا توجد رحلات مسجلة حديثاً.</td>
                  </tr>
                @endforelse
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

@endsection
