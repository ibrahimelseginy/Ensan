@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 50%, #be185d 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التحصيل 🚗</div>
        <h1 class="hero-title">الرحلات</h1>
        <p class="hero-subtitle">إدارة رحلات التحصيل والتبرعات</p>
    </div>
    <i class="bi bi-car-front-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 d-print-none animate-slide-up animate-delay-1">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="GET">
        <div class="row g-3">
            {{-- Row 1: Primary Filters --}}
            <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase text-muted">بحث</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0" placeholder="متبرع / مندوب / عنوان...">
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">خط السير</label>
                <select name="route_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}" @selected(($routeId ?? null) === $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">نوع التبرع</label>
                <select name="type" class="form-select">
                    <option value="">الكل</option>
                    <option value="cash" @selected(($type ?? '') === 'cash')>نقدي</option>
                    <option value="in_kind" @selected(($type ?? '') === 'in_kind')>عيني</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">المندوب</label>
                <select name="delegate_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach(($delegatesAllPayload ?? []) as $d)
                        <option value="{{ $d['id'] }}" @selected(($delegateId ?? null) === $d['id'])>{{ $d['name'] }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Row 2: Secondary Filters & Actions --}}
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">ترتيب</label>
                <select name="sort" class="form-select">
                    <option value="date_desc" @selected(($sort ?? 'date_desc') === 'date_desc')>الأحدث</option>
                    <option value="date_asc" @selected(($sort ?? 'date_desc') === 'date_asc')>الأقدم</option>
                    <option value="amount_desc" @selected(($sort ?? 'date_desc') === 'amount_desc')>المبلغ ↓</option>
                    <option value="amount_asc" @selected(($sort ?? 'date_desc') === 'amount_asc')>المبلغ ↑</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">الصفوف</label>
                <select name="per_page" class="form-select">
                    @foreach([12, 24, 50, 100] as $pp)
                        <option value="{{ $pp }}" @selected(($perPage ?? 12) === $pp)>{{ $pp }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2 text-nowrap">
                <button class="btn btn-primary flex-fill fw-bold"><i class="bi bi-funnel me-1"></i> تصفية</button>
                <a class="btn btn-outline-secondary" id="clearFiltersBtn" title="مسح"><i class="bi bi-x-lg"></i></a>
                <a class="btn btn-outline-secondary" href="{{ request()->fullUrlWithQuery(['export' => 'csv']) }}" title="تصدير CSV"><i class="bi bi-download"></i></a>
            </div>
        </div>
    </form>
</div>

@if(isset($printTrip) && $printTrip)
    @php
      $pt = $printTrip;
      $title = null;
      $desc = null;
      $place = null;
      $cur = null;
      $recvVia = null;
      foreach (explode("\n", (string) ($pt->allocation_note ?? '')) as $line) {
        if (str_starts_with($line, 'مشوار')) {
          $parts = explode(':', $line, 2);
          $title = isset($parts[1]) ? trim($parts[1]) : '';
        } elseif (str_starts_with($line, 'وصف')) {
          $parts = explode(':', $line, 2);
          $desc = isset($parts[1]) ? trim($parts[1]) : '';
        } elseif (str_starts_with($line, 'مكان')) {
          $parts = explode(':', $line, 2);
          $place = isset($parts[1]) ? trim($parts[1]) : '';
        } elseif (str_starts_with($line, 'عملة التبرع')) {
          $parts = explode(':', $line, 2);
          $cur = isset($parts[1]) ? trim($parts[1]) : '';
        } elseif (str_starts_with($line, 'قناة الاستلام')) {
          $parts = explode(':', $line, 2);
          $recvVia = isset($parts[1]) ? trim($parts[1]) : '';
        }
      }
      $clean = function ($s) {
        return str_replace('�', '', (string) $s);
      };
      $title = $clean($title);
      $desc = $clean($desc);
      $cur = $clean($cur);
      $recvVia = $clean($recvVia);
      $amtVal = $pt->type === 'cash' ? ($pt->amount ?? null) : ($pt->estimated_value ?? null);
      $amtText = is_null($amtVal) ? '—' : number_format((float) $amtVal, 2, '.', '');
    @endphp
    <div id="printCard" class="print-card-container" style="display:none">
      <div class="print-card-frame">
        <div class="print-header">
          <div class="row align-items-center">
            <div class="col-6 text-end">
              <h2 class="fw-bold mb-0">مؤسسة إنسان</h2>
            </div>
            <div class="col-6 text-start">
            </div>
          </div>
        </div>

        <hr class="my-4" style="border-color:#000;opacity:1">

        <div class="print-section">
          <h5 class="section-title">بيانات الرحلة</h5>
          <table class="table table-bordered print-table">
            <tr>
              <td class="bg-transparent fw-bold" width="20%">التاريخ</td>
              <td width="30%">{{ $pt->received_at?->format('Y-m-d') ?? '—' }}</td>
              <td class="bg-transparent fw-bold" width="20%">المدينة</td>
              <td width="30%">{{ $place ?? '—' }}</td>
            </tr>
            <tr>
              <td class="bg-transparent fw-bold">عنوان المشوار</td>
              <td colspan="3">{{ $title ?? '—' }}</td>
            </tr>
            <tr>
              <td class="bg-transparent fw-bold">الوصف</td>
              <td colspan="3">{{ $desc ?? '—' }}</td>
            </tr>
            <tr>
              <td class="bg-transparent fw-bold">خط السير</td>
              <td>{{ $pt->route?->name ?? '—' }}</td>
              <td class="bg-transparent fw-bold">النوع</td>
              <td>{{ $pt->type === 'cash' ? 'نقدي' : 'عيني' }} @if($recvVia) ({{ $recvVia }}) @endif</td>
            </tr>
            <tr>
              <td class="bg-transparent fw-bold">المبلغ/القيمة</td>
              <td colspan="3" class="fs-5 fw-bold">{{ $amtText }} {{ $cur ?? '' }}</td>
            </tr>
          </table>
        </div>

        <div class="print-section mt-4">
          <h5 class="section-title">أطراف الرحلة</h5>
          <table class="table table-bordered print-table">
            <tr>
              <td class="bg-transparent fw-bold" width="20%">المتبرع</td>
              <td width="30%">
                <div class="fw-bold">{{ $pt->donor?->name ?? '—' }}</div>
                @if($pt->donor?->phone)
                <div class="small">{{ $pt->donor->phone }}</div>@endif
              </td>
              <td class="bg-transparent fw-bold" width="20%">المندوب</td>
              <td width="30%">
                <div class="fw-bold">{{ $pt->delegate?->name ?? '—' }}</div>
                @if($pt->delegate?->phone)
                <div class="small">{{ $pt->delegate->phone }}</div>@endif
              </td>
            </tr>
          </table>
        </div>

        <div class="print-footer mt-5 pt-5">
          <div class="row text-center">
            <div class="col-6">
              <div class="mb-4">توقيع المندوب</div>
              <div>..................................</div>
            </div>
            <div class="col-6">
              <div class="mb-4">توقيع المحاسب/المسؤول</div>
              <div>..................................</div>
            </div>
          </div>
          <div class="text-center mt-5 small text-muted">
            تمت الطباعة بواسطة النظام - {{ date('Y-m-d H:i A') }}
          </div>
        </div>
      </div>

      <div class="d-print-none text-center mt-4">
        <a href="{{ route('trips.index') }}" class="btn btn-secondary">العودة للقائمة</a>
        <button onclick="window.print()" class="btn btn-primary">طباعة مرة أخرى</button>
      </div>
    </div>
@endif

{{-- Add Trip Form --}}
<div class="summary-panel mb-4 d-print-none animate-slide-up animate-delay-2">
    <h5 class="summary-title"><i class="bi bi-plus-circle text-success"></i> إضافة رحلة جديدة</h5>
    <form method="POST" action="{{ route('trips.store') }}">
        @csrf
        @if($errors->any())
            <div class="alert alert-danger">يرجى مراجعة الحقول</div>
        @endif
        <div class="row g-2">
            <div class="col-md-4">
                <label class="form-label">خط السير</label>
                <select name="route_id" class="form-select" id="routeSelect" required>
                    <option value="">— اختر خط —</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}">{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">عنوان المشوار</label>
                <input name="trip_title" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">التاريخ</label>
                <input name="trip_date" type="date" class="form-control" required>
            </div>
            <div class="col-md-12">
                <label class="form-label">وصف المشوار</label>
                <textarea name="trip_description" class="form-control" rows="2"></textarea>
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-4">
                <label class="form-label">نوع التبرع</label>
                <select name="donation_type" id="donationTypeSelect" class="form-select" required>
                    <option value="cash">نقدي</option>
                    <option value="in_kind">عيني</option>
                </select>
            </div>
            <div class="col-md-4" id="cashAmountWrap">
                <label class="form-label">المبلغ (للنقدي)</label>
                <div class="input-group">
                    <input name="amount" id="cashAmountInput" type="number" step="0.01" min="0" class="form-control">
                    <select name="donation_currency" id="donationCurrencyCashSelect" class="form-select" style="max-width:110px">
                        <option value="EGP">EGP</option>
                        <option value="USD">USD</option>
                        <option value="SAR">SAR</option>
                        <option value="EUR">EUR</option>
                        <option value="AED">AED</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-none" id="kindValueWrap">
                <label class="form-label">القيمة التقديرية (للعيني)</label>
                <div class="input-group">
                    <input name="estimated_value" id="kindValueInput" type="number" step="0.01" min="0" class="form-control">
                    <select name="donation_currency" id="donationCurrencyKindSelect" class="form-select" style="max-width:110px" disabled>
                        <option value="EGP">EGP</option>
                        <option value="USD">USD</option>
                        <option value="SAR">SAR</option>
                        <option value="EUR">EUR</option>
                        <option value="AED">AED</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-none in-kind-only" id="warehouseWrap">
                <label class="form-label">المخزن</label>
                <select name="warehouse_id" id="warehouseSelect" class="form-select">
                    <option value="">— اختر مخزن —</option>
                    @foreach(($warehouses ?? []) as $w)
                        <option value="{{ $w->id }}">{{ $w->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-3"><label class="form-label">اسم المتبرع</label><input name="donor_name" class="form-control" required placeholder="الاسم"></div>
            <div class="col-md-3"><label class="form-label">هاتف المتبرع</label><input name="donor_phone" class="form-control" required inputmode="numeric" dir="ltr" pattern="^(01[0125][0-9]{8})$" title="رقم هاتف مصري يبدأ بـ 010, 011, 012, 015" placeholder="مثال: 01012345678"></div>
            <div class="col-md-3"><label class="form-label">اسم المندوب</label>
                <div class="input-group">
                    <span class="input-group-text">بحث</span>
                    <input type="text" class="form-control" id="delegateSearchInput" placeholder="اكتب اسم/هاتف للبحث">
                </div>
                <select name="delegate_id" class="form-select mt-1" id="delegateSelect">
                    <option value="">— بدون —</option>
                </select>
                <a href="#" class="small d-inline-block mt-1" id="delegateProfileLink" target="_blank" style="display:none">فتح ملف المندوب</a>
            </div>
            <div class="col-md-3">
                <label class="form-label">هاتف المندوب</label>
                <input id="delegatePhoneDisplay" class="form-control" placeholder="يُعرض تلقائيًا" readonly>
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-6">
                <label class="form-label">مدينة المشوار</label>
                <select name="trip_city" class="form-select" id="citySelect">
                    <option value="">— اختر مدينة —</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label">التسعيرة</label>
                <input class="form-control" id="fareDisplay" placeholder="تُحدد تلقائيًا حسب المدينة" readonly>
            </div>
        </div>
        <div class="mt-3"><button class="btn btn-success" id="saveTripBtn" disabled>حفظ المشوار</button></div>
    </form>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4 d-print-none">
    <div class="col-md-4 animate-slide-up animate-delay-3">
        <a href="{{ route('trips.index') }}" class="stat-card stat-info text-decoration-none">
            <div class="stat-icon"><i class="bi bi-car-front-fill"></i></div>
            <div class="stat-label">عدد الرحلات</div>
            <div class="stat-value">{{ number_format($stats['count'] ?? 0) }}</div>
            <i class="bi bi-car-front-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-md-4 animate-slide-up animate-delay-4">
        <a href="{{ route('donations.index', ['type' => 'cash']) }}" class="stat-card stat-success text-decoration-none">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">إجمالي النقدي</div>
            <div class="stat-value">{{ number_format((float) ($stats['cash'] ?? 0), 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-currency-pound"></i></div>
            <i class="bi bi-cash-stack stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-md-4 animate-slide-up animate-delay-5">
        <a href="{{ route('donations.index', ['type' => 'in_kind']) }}" class="stat-card stat-purple text-decoration-none">
            <div class="stat-icon"><i class="bi bi-gift-fill"></i></div>
            <div class="stat-label">إجمالي العيني (قيمة)</div>
            <div class="stat-value">{{ number_format((float) ($stats['in_kind'] ?? 0), 0) }}</div>
            <i class="bi bi-gift-fill stat-bg-icon"></i>
        </a>
    </div>
</div>

  <div class="table-responsive">
    <table class="table table-striped align-middle trips-table text-nowrap">
      <thead>
        <tr>
          <th>عنوان المشوار</th>
          <th>الوصف</th>
          <th>التاريخ</th>
          <th>المدينة</th>
          <th>الخط</th>
          <th>نوع التبرع</th>
          <th>المبلغ/القيمة</th>
          <th>المتبرع</th>
          <th>هاتف المتبرع</th>
          <th>المندوب</th>
          <th>هاتف المندوب</th>
          <th class="d-print-none text-end">خيارات</th>
        </tr>
      </thead>
      <tbody>
        @forelse($trips as $t)
          @php
            $title = null;
            $desc = null;
            $place = null;
            $cur = null;
            $recvVia = null;
            foreach (explode("\n", (string) ($t->allocation_note ?? '')) as $line) {
              if (str_starts_with($line, 'مشوار')) {
                $parts = explode(':', $line, 2);
                $title = isset($parts[1]) ? trim($parts[1]) : '';
              } elseif (str_starts_with($line, 'وصف')) {
                $parts = explode(':', $line, 2);
                $desc = isset($parts[1]) ? trim($parts[1]) : '';
              } elseif (str_starts_with($line, 'مكان')) {
                $parts = explode(':', $line, 2);
                $place = isset($parts[1]) ? trim($parts[1]) : '';
              } elseif (str_starts_with($line, 'عملة التبرع')) {
                $parts = explode(':', $line, 2);
                $cur = isset($parts[1]) ? trim($parts[1]) : '';
              } elseif (str_starts_with($line, 'قناة الاستلام')) {
                $parts = explode(':', $line, 2);
                $recvVia = isset($parts[1]) ? trim($parts[1]) : '';
              }
            }
            $clean = function ($s) {
              return str_replace('', '', (string) $s);
            };
            $title = $clean($title);
            $desc = $clean($desc);
            $cur = $clean($cur);
            $recvVia = $clean($recvVia);
            $amtVal = $t->type === 'cash' ? ($t->amount ?? null) : ($t->estimated_value ?? null);
            $amtText = is_null($amtVal) ? '—' : number_format((float) $amtVal, 2, '.', '');
          @endphp
          <tr>
            <td class="text-wrap" style="min-width: 150px;">{{ $title ?? '—' }}</td>
            <td class="text-wrap small text-muted" style="min-width: 200px;">{{ Str::limit($desc ?? '—', 50) }}</td>
            <td>{{ $t->received_at?->format('Y-m-d') ?? '—' }}</td>
            <td>{{ $place ?? '—' }}</td>
            <td>{{ $t->route?->name ?? '—' }}</td>
            <td>
              {!! $t->type === 'cash' ? '<span class="badge bg-success">نقدي</span>' : '<span class="badge bg-info">عيني</span>' !!}
            </td>
            <td>{{ $amtText }} {{ $cur ?? '' }} @if($recvVia) <span
            class="badge bg-secondary ms-1">{{ $recvVia }}</span> @endif</td>
            <td>@if($t->donor) <a href="{{ route('donors.show', $t->donor->id) }}">{{ $t->donor->name }}</a> @else — @endif
            </td>
            <td>{{ $t->donor?->phone ?? '—' }}</td>
            <td>@if($t->delegate) <a href="{{ route('delegates.show', $t->delegate->id) }}">{{ $t->delegate->name }}</a>
            @else — @endif</td>
            <td>{{ $t->delegate?->phone ?? '—' }}</td>
            <td class="text-end d-print-none position-static">
              <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                  <i class="bi bi-three-dots"></i> خيارات
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-lg border-0 animate-fade-in" style="z-index: 9999;">
                  <li><a class="dropdown-item py-2" href="{{ route('donations.show', $t->id) }}"><i class="bi bi-eye me-2 text-primary"></i> عرض التفاصيل</a></li>
                  <li><a class="dropdown-item py-2" href="{{ route('trips.index', ['print_id' => $t->id]) }}"><i class="bi bi-printer me-2 text-dark"></i> طباعة الإيصال</a></li>
                  @php $currentUser = request()->user(); @endphp
                  @if($currentUser)
                    @if($currentUser->hasRole('admin') || $currentUser->hasRole('manager'))
                      <li><a class="dropdown-item py-2" href="{{ route('donations.edit', $t->id) }}"><i class="bi bi-pencil me-2 text-secondary"></i> تعديل بيانات</a></li>
                      <li><hr class="dropdown-divider"></li>
                      <li>
                        <form method="POST" action="{{ route('donations.destroy', $t->id) }}" onsubmit="return confirm('إلغاء هذا التبرع؟');">
                          @csrf @method('DELETE')
                          <button class="dropdown-item py-2 text-danger"><i class="bi bi-trash me-2"></i> إلغاء نهائي</button>
                        </form>
                      </li>
                    @else
                      <li><a class="dropdown-item py-2" href="{{ route('donations.edit', $t->id) }}"><i class="bi bi-pencil-square me-2 text-warning"></i> طلب تعديل</a></li>
                      <li>
                        <form method="POST" action="{{ route('donations.destroy', $t->id) }}" onsubmit="return confirm('هل أنت متأكد من طلب إلغاء هذا التبرع والرحلة؟');">
                          @csrf @method('DELETE')
                          <button class="dropdown-item py-2 text-warning"><i class="bi bi-x-circle me-2"></i> طلب إلغاء</button>
                        </form>
                      </li>
                    @endif
                  @endif
                </ul>
              </div>
            </td>
          </tr>
        @empty
          <tr>
            <td colspan="12" class="text-center py-5">
              <div class="text-muted d-flex flex-column align-items-center">
                <i class="bi bi-inbox fs-1 mb-2"></i>
                <p>لا توجد رحلات مسجلة</p>
              </div>
            </td>
          </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div class="mt-4 d-print-none d-flex justify-content-center">{{ $trips->links() }}</div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      var routes = @json($routesPayload);
      var delegatesAll = @json($delegatesAllPayload);
      var routeSel = document.getElementById('routeSelect');
      var citySel = document.getElementById('citySelect');
      var delSel = document.getElementById('delegateSelect');
      var delSearch = document.getElementById('delegateSearchInput');
      var fareDisp = document.getElementById('fareDisplay');
      var delPhoneDisp = document.getElementById('delegatePhoneDisplay');
      var delProfileLink = document.getElementById('delegateProfileLink');
      var delegatesMasterList = [];
      var filterForm = document.querySelector('form[method="GET"]');
      function renderDelegates(list) {
        delSel.innerHTML = '<option value="">— بدون —</option>';
        list.forEach(function (d) { var opt = document.createElement('option'); opt.value = d.id; opt.textContent = d.name; opt.dataset.phone = d.phone || ''; delSel.appendChild(opt); });
      }
      function updateRouteDeps() {
        var rid = routeSel.value; var r = routes.find(function (x) { return String(x.id) === String(rid); });
        citySel.innerHTML = '<option value="">— اختر مدينة —</option>';
        delSel.innerHTML = '<option value="">— بدون —</option>';
        fareDisp.value = '';
        if (delPhoneDisp) { delPhoneDisp.value = ''; }
        if (r) {
          (r.cities || []).forEach(function (c) {
            var opt = document.createElement('option'); opt.value = c.name || ''; opt.textContent = c.name || ''; opt.dataset.fare = c.fare || ''; opt.dataset.currency = c.currency || ''; citySel.appendChild(opt);
          });
        }
        delegatesMasterList = (r && Array.isArray(r.delegates) && r.delegates.length > 0) ? r.delegates : (delegatesAll || []);
        renderDelegates(delegatesMasterList);
      }
      function updateFare() { var opt = citySel.options[citySel.selectedIndex]; var f = opt && opt.dataset ? (opt.dataset.fare || '') : ''; var cur = opt && opt.dataset ? (opt.dataset.currency || '') : ''; fareDisp.value = f ? (cur ? (f + ' ' + cur) : f) : ''; }
      if (routeSel) { routeSel.addEventListener('change', updateRouteDeps); }
      if (citySel) { citySel.addEventListener('change', updateFare); }
      function updateDelegatePhone() { var opt = delSel.options[delSel.selectedIndex]; var p = opt && opt.dataset ? (opt.dataset.phone || '') : ''; if (delPhoneDisp) { delPhoneDisp.value = p; } if (delProfileLink) { var id = delSel.value; if (id) { delProfileLink.href = '/delegates/' + id; delProfileLink.style.display = 'inline-block'; } else { delProfileLink.removeAttribute('href'); delProfileLink.style.display = 'none'; } } }
      if (delSel) { delSel.addEventListener('change', updateDelegatePhone); }
      function filterDelegates() { var term = (delSearch && delSearch.value || '').toLowerCase(); var filtered = delegatesMasterList.filter(function (d) { return (d.name || '').toLowerCase().includes(term) || (String(d.phone || '')).includes(term); }); renderDelegates(filtered); updateDelegatePhone(); }
      if (delSearch) { delSearch.addEventListener('input', filterDelegates); }

      // Persist filters
      var filtersKey = 'trips.filters';
      function saveFilters() { if (!filterForm) return; var fd = new FormData(filterForm); var obj = {}; fd.forEach(function (v, k) { obj[k] = v; }); localStorage.setItem(filtersKey, JSON.stringify(obj)); }
      function loadFilters() { try { var obj = JSON.parse(localStorage.getItem(filtersKey) || '{}'); if (!obj) return; Object.keys(obj).forEach(function (k) { var el = filterForm && filterForm.querySelector('[name="' + k + '"]'); if (el) { el.value = obj[k]; } }); } catch (e) { } }
      function clearFilters() { localStorage.removeItem(filtersKey); if (!filterForm) return; filterForm.reset(); filterForm.submit(); }
      if (filterForm) { loadFilters(); filterForm.addEventListener('change', saveFilters); }
      var clearBtn = document.getElementById('clearFiltersBtn'); if (clearBtn) { clearBtn.addEventListener('click', function (e) { e.preventDefault(); clearFilters(); }); }

      var typeSel = document.getElementById('donationTypeSelect');
      var cashWrap = document.getElementById('cashAmountWrap');
      var kindWrap = document.getElementById('kindValueWrap');
      var cashInput = document.getElementById('cashAmountInput');
      var kindInput = document.getElementById('kindValueInput');
      var curCashSel = document.getElementById('donationCurrencyCashSelect');
      var curKindSel = document.getElementById('donationCurrencyKindSelect');
      var warehouseWrap = document.getElementById('warehouseWrap');
      var warehouseSelect = document.getElementById('warehouseSelect');

      var saveBtn = document.getElementById('saveTripBtn');
      var titleInput = document.querySelector('input[name="trip_title"]');
      var dateInput = document.querySelector('input[name="trip_date"]');
      var donorNameInput = document.querySelector('input[name="donor_name"]');
      function updateDonationFields() {
        var t = typeSel ? typeSel.value : 'cash';
        if (t === 'cash') {
          cashWrap.classList.remove('d-none');
          kindWrap.classList.add('d-none');
          if (cashInput) { cashInput.required = true; }
          if (kindInput) { kindInput.required = false; kindInput.value = ''; }
          if (curCashSel) { curCashSel.disabled = false; curCashSel.required = true; }
          if (curKindSel) { curKindSel.disabled = true; curKindSel.required = false; }
          if (warehouseWrap) { warehouseWrap.classList.add('d-none'); }
          if (warehouseSelect) { warehouseSelect.required = false; warehouseSelect.value = ''; }
        } else {
          cashWrap.classList.add('d-none');
          kindWrap.classList.remove('d-none');
          if (kindInput) { kindInput.required = true; }
          if (cashInput) { cashInput.required = false; cashInput.value = ''; }
          if (curKindSel) { curKindSel.disabled = false; curKindSel.required = true; }
          if (curCashSel) { curCashSel.disabled = true; curCashSel.required = false; }
          if (warehouseWrap) { warehouseWrap.classList.remove('d-none'); }
          if (warehouseSelect) { warehouseSelect.required = true; }
        }
        validateForm();
      }
      function validateForm() { var t = typeSel ? typeSel.value : 'cash'; var curSel = t === 'cash' ? curCashSel : curKindSel; var ok = routeSel && routeSel.value && titleInput && titleInput.value && dateInput && dateInput.value && donorNameInput && donorNameInput.value && curSel && curSel.value && ((t === 'cash' && cashInput && cashInput.value) || (t === 'in_kind' && kindInput && kindInput.value)); if (saveBtn) { saveBtn.disabled = !ok; } }
      // Default date to today
      if (dateInput && !dateInput.value) { var dt = new Date(); var y = dt.getFullYear(); var m = String(dt.getMonth() + 1).padStart(2, '0'); var d = String(dt.getDate()).padStart(2, '0'); dateInput.value = y + '-' + m + '-' + d; }
      if (typeSel) { typeSel.addEventListener('change', updateDonationFields); updateDonationFields(); }

      ['input', 'change'].forEach(function (ev) { if (cashInput) cashInput.addEventListener(ev, validateForm); if (kindInput) kindInput.addEventListener(ev, validateForm); if (routeSel) routeSel.addEventListener(ev, validateForm); if (titleInput) titleInput.addEventListener(ev, validateForm); if (dateInput) dateInput.addEventListener(ev, validateForm); if (donorNameInput) donorNameInput.addEventListener(ev, validateForm); });
      updateRouteDeps();
      updateDelegatePhone();
      validateForm();
    });
  </script>
  <script>
    document.addEventListener('DOMContentLoaded', function () { var btn = document.getElementById('printTripsBtn'); if (btn) { btn.addEventListener('click', function () { window.print(); }); } });
  </script>
  <style>
    .trips-table td,
    .trips-table th {
      padding: .6rem .9rem
    }

    .trips-table .badge {
      font-size: .82rem
    }

    @media print {

      /* Global print resets */
      .navbar,
      .sidebar-fixed,
      .sidebar,
      .header,
      #printTripsBtn {
        display: none !important
      }

      .content-wrapper {
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important
      }

      body {
        background: white !important;
        color: black !important
      }

      /* If we have a print card, hide everything else */
      body.has-print-card>* {
        display: none !important
      }

      body.has-print-card .content-wrapper {
        display: block !important;
        margin: 0 !important;
        padding: 0 !important;
        width: 100% !important;
      }

      body.has-print-card .content-wrapper>* {
        display: none !important
      }

      body.has-print-card .content-wrapper #printCard {
        display: block !important;
        width: 100% !important;
        position: absolute;
        left: 0;
        top: 0;
        background: white;
        z-index: 9999;
      }


      .print-table td {
        padding: 0.75rem !important;
        vertical-align: middle;
      }

      .print-table .bg-light {
        background-color: #f8f9fa !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
      }

      .section-title {
        border-bottom: 2px solid #eee;
        padding-bottom: 0.5rem;
        margin-bottom: 1rem;
        font-weight: bold;
        color: #0d6efd;
      }

      /* Reset page margins for card printing */
      @page {
        size: A4;
        margin: 0;
      }

      .print-card-frame {
        border: 2px solid #000;
        padding: 2rem;
        min-height: 100vh;
        position: relative;
        box-sizing: border-box;
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
  <style>
    body.has-print-card {
      font-family: 'Segoe UI', 'Tahoma', 'Arial', 'Cairo', sans-serif
    }

    .print-card {
      font-family: 'Segoe UI', 'Tahoma', 'Arial', 'Cairo', sans-serif;
      line-height: 1.6
    }

    .print-card .title-org {
      font-size: 1.8rem
    }

    .print-card .title-main {
      font-size: 1.6rem
    }

    .print-card .field-label {
      font-weight: 600
    }

    .print-card .field-value {
      font-size: 1.05rem
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
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      if (document.getElementById('printCard')) {
        document.body.classList.add('has-print-card');
        try { window.__oldTitle = document.title; document.title = ' '; } catch (e) { }
        window.print();
      }
    });
  </script>
@endsection

