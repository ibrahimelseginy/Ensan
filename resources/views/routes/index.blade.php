@extends('layouts.app')
@section('content')
  <style>
    .card {
      overflow: visible !important;
    }

    .dropdown-menu {
      transform: translateY(0);
      transition: all 0.3s ease;
    }

    .animate-fade-in {
      animation: fadeIn 0.2s ease-out;
    }

    @keyframes fadeIn {
      from {
        opacity: 0;
        transform: translateY(10px);
      }

      to {
        opacity: 1;
        transform: translateY(0);
      }
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

  {{-- Premium Dashboard Hero --}}
  <div class="dashboard-hero animate-slide-up"
    style="background: linear-gradient(135deg, #14b8a6 0%, #0d9488 50%, #0f766e 100%);">
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
  {{-- Governorate Cards Grid --}}
  <style>
    .gov-grid {
      display: grid;
      grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
      gap: 1rem;
    }

    @media (min-width: 1200px) {
      .gov-grid {
        grid-template-columns: repeat(4, 1fr);
      }
    }

    @media (min-width: 992px) and (max-width: 1199px) {
      .gov-grid {
        grid-template-columns: repeat(3, 1fr);
      }
    }

    @media (min-width: 576px) and (max-width: 991px) {
      .gov-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    .gov-card {
      background: var(--glass-bg, rgba(255, 255, 255, 0.04));
      border: 1px solid var(--border-color, rgba(255, 255, 255, 0.08));
      border-radius: 16px;
      padding: 1.25rem;
      transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
      cursor: pointer;
      position: relative;
      overflow: visible;
    }

    .gov-card:hover {
      transform: translateY(-4px);
      border-color: rgba(20, 184, 166, 0.4);
      box-shadow: 0 8px 32px rgba(20, 184, 166, 0.15);
    }

    .gov-card-header {
      display: flex;
      align-items: center;
      gap: 0.75rem;
      margin-bottom: 0.875rem;
    }

    .gov-avatar {
      width: 44px;
      height: 44px;
      border-radius: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 700;
      font-size: 1.1rem;
      flex-shrink: 0;
      background: linear-gradient(135deg, #14b8a6, #0d9488);
      color: #fff;
      box-shadow: 0 4px 12px rgba(20, 184, 166, 0.3);
    }

    .gov-name {
      font-size: 1rem;
      font-weight: 700;
      margin: 0;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .gov-stats {
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      margin-bottom: 0.75rem;
    }

    .gov-stat {
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
      font-size: 0.75rem;
      padding: 0.25rem 0.625rem;
      border-radius: 20px;
      font-weight: 600;
    }

    .gov-stat-cities {
      background: rgba(59, 130, 246, 0.12);
      color: #60a5fa;
    }

    .gov-stat-delegates {
      background: rgba(168, 85, 247, 0.12);
      color: #c084fc;
    }

    .gov-stat-donations {
      background: rgba(34, 197, 94, 0.12);
      color: #4ade80;
    }

    .gov-total {
      font-size: 0.8rem;
      font-weight: 700;
      color: #4ade80;
      display: flex;
      align-items: center;
      gap: 0.3rem;
    }

    .gov-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 0.75rem;
      padding-top: 0.75rem;
      border-top: 1px solid var(--border-color, rgba(255, 255, 255, 0.06));
    }

    .gov-btn-details {
      font-size: 0.8rem;
      padding: 0.35rem 0.9rem;
      border-radius: 20px;
      font-weight: 600;
      background: rgba(20, 184, 166, 0.12);
      color: #14b8a6;
      border: 1px solid rgba(20, 184, 166, 0.2);
      text-decoration: none;
      transition: all 0.2s ease;
      display: inline-flex;
      align-items: center;
      gap: 0.3rem;
    }

    .gov-btn-details:hover {
      background: rgba(20, 184, 166, 0.25);
      color: #14b8a6;
      border-color: rgba(20, 184, 166, 0.4);
    }

    .gov-btn-options {
      font-size: 0.78rem;
      padding: 0.3rem 0.75rem;
      border-radius: 20px;
      font-weight: 500;
      background: transparent;
      color: var(--text-muted, #94a3b8);
      border: 1px solid var(--border-color, rgba(255, 255, 255, 0.1));
      transition: all 0.2s ease;
    }

    .gov-btn-options:hover {
      background: rgba(255, 255, 255, 0.06);
      color: var(--text-primary, #e2e8f0);
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

  <div class="gov-grid">
    @foreach($routes as $r)
      @php $citiesCount = is_array($r->cities ?? null) ? count($r->cities) : 0; @endphp
      <div class="gov-card animate-slide-up" onclick="location.href='{{ route('travel-routes.show', $r) }}'">
        {{-- Header --}}
        <div class="gov-card-header">
          <div class="gov-avatar">{{ $r->name ? mb_substr($r->name, 0, 1) : '-' }}</div>
          <h5 class="gov-name">{{ $r->name ?? '—' }}</h5>
        </div>

        {{-- Stats --}}
        <div class="gov-stats">
          <span class="gov-stat gov-stat-cities">
            <i class="bi bi-geo-alt-fill"></i> مدن: {{ $citiesCount }}
          </span>
          <span class="gov-stat gov-stat-delegates">
            <i class="bi bi-person-fill"></i> مندوبون: {{ $r->delegates_count ?? 0 }}
          </span>
          <span class="gov-stat gov-stat-donations">
            <i class="bi bi-heart-fill"></i> تبرعات: {{ $r->donations_count ?? 0 }}
          </span>
        </div>

        {{-- Total --}}
        <div class="gov-total">
          <i class="bi bi-cash-stack"></i>
          إجمالي: {{ number_format((float) ($r->donation_total ?? 0), 2) }}
        </div>

        {{-- Footer Actions --}}
        <div class="gov-footer" onclick="event.stopPropagation()">
          <a class="gov-btn-details" href="{{ route('travel-routes.show', $r) }}">
            عرض التفاصيل <i class="bi bi-arrow-left ms-1"></i>
          </a>

          <div class="dropdown position-static">
            <button class="gov-btn-options dropdown-toggle border-0" type="button" data-bs-toggle="dropdown"
              data-bs-boundary="viewport">
              خيارات <i class="bi bi-three-dots-vertical ms-1"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 animate-fade-in">
              @if(auth()->check())
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                  <li><a class="dropdown-item py-2" href="{{ route('travel-routes.edit', $r) }}"><i
                        class="bi bi-pencil me-2 text-primary"></i> تعديل</a></li>
                  <li>
                    <form method="POST" action="{{ route('travel-routes.duplicate', $r) }}"
                      onsubmit="return confirm('استنساخ هذا الخط؟');">
                      @csrf
                      <button class="dropdown-item py-2"><i class="bi bi-files me-2 text-dark"></i> استنساخ</button>
                    </form>
                  </li>
                  <li>
                    <hr class="dropdown-divider">
                  </li>
                  <li>
                    <form method="POST" action="{{ route('travel-routes.destroy', $r) }}"
                      onsubmit="return confirm('حذف خط السير؟');">
                      @csrf @method('DELETE')
                      <button class="dropdown-item py-2 text-danger"><i class="bi bi-trash me-2"></i> حذف نهائي</button>
                    </form>
                  </li>
                @else
                  <li><a class="dropdown-item py-2" href="{{ route('travel-routes.edit', $r) }}"><i
                        class="bi bi-pencil-square me-2 text-warning"></i> طلب تعديل</a></li>
                  <li>
                    <form method="POST" action="{{ route('travel-routes.destroy', $r) }}"
                      onsubmit="return confirm('هل أنت متأكد من طلب إلغاء خط السير؟');">
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
    @endforeach
  </div>
  <div class="mt-3 d-flex justify-content-between align-items-center">
    <div class="text-muted small">الإجمالي: {{ method_exists($routes, 'total') ? $routes->total() : count($routes) }}
    </div>
    @if(method_exists($routes, 'links'))
      <div>{{ $routes->links() }}</div>
    @endif
  </div>
@endsection

