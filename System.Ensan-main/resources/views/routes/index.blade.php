@extends('layouts.app')
@section('content')
<style>
    .card { overflow: visible !important; }
    .dropdown-menu { transform: translateY(0); transition: all 0.3s ease; }
    .animate-fade-in { animation: fadeIn 0.2s ease-out; }
    @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 50%, #0f766e 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التوزيع 🗺️</div>
        <h1 class="hero-title">خطوط السير</h1>
        <p class="hero-subtitle">إدارة مناطق التحصيل والتوزيع</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('travel-routes.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة خط
            </a>
            <a href="{{ route('travel-routes.export', request()->query()) }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-download me-1"></i> تصدير
            </a>
        </div>
    </div>
    <i class="bi bi-signpost-2-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-1">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">بحث بالاسم</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0" placeholder="القاهرة، الجيزة...">
            </div>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">عدد المدن من</label>
            <input name="min_cities" value="{{ $minCities ?? '' }}" type="number" min="0" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">إلى</label>
            <input name="max_cities" value="{{ $maxCities ?? '' }}" type="number" min="0" class="form-control">
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">مندوبون</label>
            <select name="has_delegates" class="form-select">
                <option value="">الكل</option>
                <option value="1" @selected(($hasDelegates ?? '') === '1')>يوجد</option>
                <option value="0" @selected(($hasDelegates ?? '') === '0')>لا يوجد</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">تبرعات</label>
            <select name="has_donations" class="form-select">
                <option value="">الكل</option>
                <option value="1" @selected(($hasDonations ?? '') === '1')>يوجد</option>
                <option value="0" @selected(($hasDonations ?? '') === '0')>لا يوجد</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">الترتيب</label>
            <select name="sort" class="form-select">
                <option value="name" @selected(($sort ?? 'name') === 'name')>الاسم</option>
                <option value="cities_count" @selected(($sort ?? '') === 'cities_count')>عدد المدن</option>
                <option value="delegates_count" @selected(($sort ?? '') === 'delegates_count')>عدد المندوبين</option>
                <option value="donations_count" @selected(($sort ?? '') === 'donations_count')>عدد التبرعات</option>
                <option value="donation_total" @selected(($sort ?? '') === 'donation_total')>إجمالي التبرعات</option>
            </select>
        </div>
        <div class="col-md-1">
            <select name="dir" class="form-select">
                <option value="asc" @selected(($dir ?? 'asc') === 'asc')>↑</option>
                <option value="desc" @selected(($dir ?? 'asc') === 'desc')>↓</option>
            </select>
        </div>
        <div class="col-md-1">
            <select name="per_page" class="form-select">
                @php $pp = (int) ($perPage ?? 12); @endphp
                <option value="12" @selected($pp === 12)>12</option>
                <option value="24" @selected($pp === 24)>24</option>
                <option value="48" @selected($pp === 48)>48</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100 fw-bold"><i class="bi bi-funnel me-1"></i> تصفية</button>
        </div>
    </form>
</div>

@php
    $sumCities = 0;
    $sumDelegates = 0;
    $sumDonations = 0;
    $sumDonationTotal = 0.0;
    foreach ($routes as $rr) {
      $sumCities += is_array($rr->cities ?? null) ? count($rr->cities) : 0;
      $sumDelegates += (int) ($rr->delegates_count ?? 0);
      $sumDonations += (int) ($rr->donations_count ?? 0);
      $sumDonationTotal += (float) ($rr->donation_total ?? 0);
    }
@endphp

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-2">
        <div class="stat-card stat-info">
            <div class="stat-icon"><i class="bi bi-signpost-2-fill"></i></div>
            <div class="stat-label">عدد الخطوط</div>
            <div class="stat-value">{{ count($routes) }}</div>
            <i class="bi bi-signpost-2-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-3">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="bi bi-geo-alt-fill"></i></div>
            <div class="stat-label">إجمالي المدن</div>
            <div class="stat-value">{{ $sumCities }}</div>
            <i class="bi bi-geo-alt-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-4">
        <div class="stat-card stat-purple">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي المندوبين</div>
            <div class="stat-value">{{ $sumDelegates }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-5">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">إجمالي التبرعات</div>
            <div class="stat-value">{{ number_format($sumDonationTotal, 0) }}</div>
            <i class="bi bi-cash-stack stat-bg-icon"></i>
        </div>
    </div>
</div>
  <div class="row g-4">
    @foreach($routes as $r)
      <div class="col-md-6 col-lg-4">
        <div class="card border-0 shadow-sm h-100" role="button"
          onclick="location.href='{{ route('travel-routes.show', $r) }}'">
          <div class="card-body">
            <div class="d-flex align-items-center gap-3 mb-3">
              <div class="rounded-circle bg-primary-subtle text-primary d-flex align-items-center justify-content-center"
                style="width:48px;height:48px;font-weight:bold;flex-shrink:0">
                {{ $r->name ? mb_substr($r->name, 0, 1) : '-' }}
              </div>
              <div class="overflow-hidden">
                <h5 class="fw-bold mb-0 text-truncate">{{ $r->name ?? '—' }}</h5>
              </div>
            </div>

            @php $citiesCount = is_array($r->cities ?? null) ? count($r->cities) : 0; @endphp
            <div class="d-flex flex-wrap gap-2">
              <span class="badge bg-secondary-subtle text-secondary">مدن: {{ $citiesCount }}</span>
              <span class="badge bg-secondary-subtle text-secondary">مندوبون: {{ $r->delegates_count ?? 0 }}</span>
              <span class="badge bg-secondary-subtle text-secondary">تبرعات: {{ $r->donations_count ?? 0 }}</span>
            </div>
            <div class="mt-2">
              <span class="badge bg-success-subtle text-success">إجمالي:
                {{ number_format((float) ($r->donation_total ?? 0), 2) }}</span>
            </div>
          </div>
          </div>
          <div class="card-footer bg-transparent border-top-0 d-flex justify-content-between align-items-center pb-3"
            onclick="event.stopPropagation()">
            <a class="btn btn-sm btn-light text-primary fw-bold px-3 rounded-pill" href="{{ route('travel-routes.show', $r) }}">
              عرض التفاصيل <i class="bi bi-arrow-left ms-1"></i>
            </a>
            
            <div class="dropdown position-static">
              <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle border-0" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                خيارات <i class="bi bi-three-dots-vertical ms-1"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 animate-fade-in">
                @if(auth()->check())
                  @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <li><a class="dropdown-item py-2" href="{{ route('travel-routes.edit', $r) }}"><i class="bi bi-pencil me-2 text-primary"></i> تعديل</a></li>
                    <li>
                      <form method="POST" action="{{ route('travel-routes.duplicate', $r) }}" onsubmit="return confirm('استنساخ هذا الخط؟');">
                        @csrf
                        <button class="dropdown-item py-2"><i class="bi bi-files me-2 text-dark"></i> استنساخ</button>
                      </form>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                      <form method="POST" action="{{ route('travel-routes.destroy', $r) }}" onsubmit="return confirm('حذف خط السير؟');">
                        @csrf @method('DELETE')
                        <button class="dropdown-item py-2 text-danger"><i class="bi bi-trash me-2"></i> حذف نهائي</button>
                      </form>
                    </li>
                  @else
                    <li><a class="dropdown-item py-2" href="{{ route('travel-routes.edit', $r) }}"><i class="bi bi-pencil-square me-2 text-warning"></i> طلب تعديل</a></li>
                    <li>
                      <form method="POST" action="{{ route('travel-routes.destroy', $r) }}" onsubmit="return confirm('هل أنت متأكد من طلب إلغاء خط السير؟');">
                        @csrf @method('DELETE')
                        <button class="dropdown-item py-2 text-warning"><i class="bi bi-x-circle me-2"></i> طلب إلغاء</button>
                      </form>
                    </li>
                  @endif
                @endif
              </ul>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  <div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">الإجمالي: {{ method_exists($routes, 'total') ? $routes->total() : count($routes) }}</div>
    @if(method_exists($routes, 'links'))
      <div>{{ $routes->links() }}</div>
    @endif
  </div>
@endsection