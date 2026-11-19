@extends('layouts.app')

@section('title', 'الزيارات الميدانية واستمارات البحث')

@section('content')
<div class="container-fluid py-3">
  <!-- Header -->
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <div>
      <h3 class="mb-0 fw-bolder text-dark">
        <i class="bi bi-geo-alt-fill text-danger me-1"></i> الزيارات الميدانية واستمارات البحث
      </h3>
      <p class="text-muted small mb-0 mt-1">متابعة الزيارات الميدانية، البحث الاجتماعي، وبحث مرضى الأورام</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('visits.researcher-route') }}" class="btn btn-outline-danger fw-bold">
        <i class="bi bi-signpost-split-fill me-1"></i> خط سير الباحث
      </a>
      <button type="button" class="btn btn-outline-primary fw-bold" data-bs-toggle="modal" data-bs-target="#newVisitModal">
        <i class="bi bi-calendar-plus me-1"></i> جدولة موعد زيارة
      </button>
      <a href="{{ route('visits.create') }}" class="btn btn-success fw-bold px-3">
        <i class="bi bi-file-earmark-plus-fill me-1"></i> استمارة بحث ميداني جديدة
      </a>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert">
      <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <!-- Quick Stats -->
  <div class="row g-3 mb-4">
    <div class="col-md-2 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100">
        <div class="text-muted small fw-bold">إجمالي الأبحاث</div>
        <div class="h3 fw-bolder mb-0 text-dark font-monospace">{{ $stats['total'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-danger">
        <div class="text-muted small fw-bold">أبحاث مرضى الأورام</div>
        <div class="h3 fw-bolder mb-0 text-danger font-monospace">{{ $stats['cancer_cases'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-success">
        <div class="text-muted small fw-bold">أبحاث اجتماعية عادية</div>
        <div class="h3 fw-bolder mb-0 text-success font-monospace">{{ $stats['normal_cases'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-primary">
        <div class="text-muted small fw-bold">زيارات قادمة</div>
        <div class="h3 fw-bolder mb-0 text-primary font-monospace">{{ $stats['upcoming'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-info">
        <div class="text-muted small fw-bold">تمت هذا الشهر</div>
        <div class="h3 fw-bolder mb-0 text-info font-monospace">{{ $stats['completed_month'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-2 col-sm-6">
      <div class="card border-0 shadow-sm rounded-4 p-3 bg-white h-100 border-start border-4 border-warning">
        <div class="text-muted small fw-bold">في انتظار المناقشة</div>
        <div class="h3 fw-bolder mb-0 text-warning font-monospace">{{ $stats['awaiting_discussion'] ?? 0 }}</div>
      </div>
    </div>
  </div>

  <!-- Filters & Search -->
  <div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
      <form method="GET" action="{{ route('visits.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
          <div class="input-group">
            <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="بحث بالاسم، الرقم القومي، الهاتف، الكود...">
          </div>
        </div>

        <div class="col-md-3">
          <select name="type" class="form-select" onchange="this.form.submit()">
            <option value="">-- كل أنواع الأبحاث --</option>
            <option value="cancer" @selected(request('type') === 'cancer')>مرضى أورام فقط</option>
            <option value="normal" @selected(request('type') === 'normal')>بحث اجتماعي عادي فقط</option>
          </select>
        </div>

        <div class="col-md-3">
          <select name="status" class="form-select" onchange="this.form.submit()">
            <option value="">-- كل الحالات --</option>
            <option value="awaiting_discussion" @selected(request('status') === 'awaiting_discussion')>⏳ في انتظار المناقشة</option>
            <option value="accepted" @selected(request('status') === 'accepted')>✅ تم قبول واعتماد البحث</option>
            <option value="rejected" @selected(request('status') === 'rejected')>❌ تم الرفض</option>
            <option value="completed" @selected(request('status') === 'completed')>تمت الزيارة والبحث</option>
            <option value="scheduled" @selected(request('status') === 'scheduled')>مجدولة</option>
            <option value="cancelled" @selected(request('status') === 'cancelled')>ملغاة</option>
          </select>
        </div>

        <div class="col-md-2 d-flex gap-2">
          <button type="submit" class="btn btn-primary w-100">تصفية</button>
          @if(request()->hasAny(['search', 'type', 'status']))
            <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary" title="إعادة ضبط"><i class="bi bi-x-lg"></i></a>
          @endif
        </div>
      </form>
    </div>
  </div>

  <!-- Visits List Table -->
  <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
    <div class="card-header bg-white py-3 px-4 d-flex justify-content-between align-items-center border-bottom">
      <h6 class="m-0 fw-bold text-dark"><i class="bi bi-list-task me-1"></i> جدول الزيارات والأبحاث المسجلة</h6>
      <span class="text-muted small">إجمالي المعروض: {{ $visits->total() }} سجل</span>
    </div>
    <div class="card-body p-0">
      @if($visits->count() > 0)
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
              <tr>
                <th style="width: 50px;" class="text-center">#</th>
                <th>نوع البحث</th>
                <th>المستفيد / الحالة</th>
                <th>المكان / اللوكيشن</th>
                <th>الباحث المسؤول</th>
                <th>تاريخ البحث</th>
                <th>الدخل / المصروفات</th>
                <th>حالة الزيارة</th>
                <th>التوصية</th>
                <th class="text-center" style="width: 170px;">الإجراءات</th>
              </tr>
            </thead>
            <tbody>
              @foreach($visits as $visit)
                <tr>
                  <td class="text-center font-monospace text-muted">{{ $visit->id }}</td>
                  <td>
                    @if($visit->has_cancer_patient)
                      <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1 fw-bold">
                        <i class="bi bi-heart-pulse-fill me-1"></i> مريض أورام
                      </span>
                    @else
                      <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1 fw-bold">
                        <i class="bi bi-house-heart me-1"></i> بحث عادي
                      </span>
                    @endif
                  </td>
                  <td>
                    <div class="fw-bold text-dark">
                      <a href="{{ route('visits.show', $visit) }}" class="text-decoration-none text-dark">
                        {{ $visit->patient_name ?: ($visit->beneficiary?->full_name ?? '—') }}
                      </a>
                    </div>
                    @if($visit->beneficiary)
                      <small class="text-muted">كود: {{ $visit->beneficiary->code ?: $visit->beneficiary->id }} | هاتف: {{ $visit->phone ?: $visit->beneficiary->phone }}</small>
                    @elseif($visit->phone)
                      <small class="text-muted font-monospace">هاتف: {{ $visit->phone }}</small>
                    @endif
                  </td>
                  <td>
                    @php
                      $visitAddr = $visit->address ?: ($visit->beneficiary?->address ?? null);
                      $mapLink = $visit->location_url
                        ?: ($visitAddr ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($visitAddr) : null);
                    @endphp
                    @if($visitAddr)
                      <div class="small text-dark fw-semibold text-truncate" style="max-width: 190px;" title="{{ $visitAddr }}">
                        <i class="bi bi-geo-alt text-danger me-1"></i>{{ $visitAddr }}
                      </div>
                    @endif
                    @if($mapLink)
                      <a href="{{ $mapLink }}" target="_blank" rel="noopener" class="badge bg-success-subtle text-success border border-success-subtle text-decoration-none mt-1">
                        <i class="bi bi-pin-map-fill me-1"></i>فتح اللوكيشن
                      </a>
                    @else
                      <span class="text-muted small">—</span>
                    @endif
                  </td>
                  <td>
                    <div class="fw-semibold text-dark">{{ $visit->researcher?->name ?? 'غير محدد' }}</div>
                    @if($visit->researcher_phone)
                      <small class="text-muted font-monospace">{{ $visit->researcher_phone }}</small>
                    @endif
                  </td>
                  <td>
                    <div>{{ $visit->visit_date ? $visit->visit_date->format('Y-m-d') : '—' }}</div>
                    <small class="text-muted">{{ $visit->visit_date ? $visit->visit_date->diffForHumans() : '' }}</small>
                  </td>
                  <td class="font-monospace small">
                    <span class="text-success fw-bold">+{{ number_format($visit->total_income, 0) }}</span> /
                    <span class="text-danger fw-bold">-{{ number_format($visit->total_expenses, 0) }}</span>
                  </td>
                  <td>
                    @if($visit->status === 'awaiting_discussion')
                      <span class="badge bg-warning text-dark"><i class="bi bi-hourglass-split me-1"></i> في انتظار المناقشة</span>
                    @elseif($visit->status === 'accepted')
                      <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i> مقبول ومعتمد</span>
                    @elseif($visit->status === 'rejected')
                      <span class="badge bg-danger"><i class="bi bi-x-circle-fill me-1"></i> مرفوض</span>
                    @else
                      <span class="badge bg-{{ $visit->status_color }}">{{ $visit->status_label }}</span>
                    @endif
                  </td>
                  <td>
                    <span class="small fw-semibold">{{ $visit->recommendation_label }}</span>
                  </td>
                  <td class="text-center">
                    <div class="btn-group btn-group-sm">
                      <a href="{{ route('visits.show', $visit) }}" class="btn btn-light text-primary" title="عرض التفاصيل">
                        <i class="bi bi-eye"></i>
                      </a>
                      <a href="{{ route('visits.print', $visit) }}" target="_blank" class="btn btn-light text-success" title="طباعة الاستمارة الرسمية">
                        <i class="bi bi-printer"></i>
                      </a>
                      <a href="{{ route('visits.edit', $visit) }}" class="btn btn-light text-warning" title="تعديل">
                        <i class="bi bi-pencil"></i>
                      </a>
                      <form action="{{ route('visits.destroy', $visit) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الاستمارة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-light text-danger" title="حذف">
                          <i class="bi bi-trash"></i>
                        </button>
                      </form>
                    </div>
                  </td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <div class="p-3 border-top">
          {{ $visits->links() }}
        </div>
      @else
        <div class="text-center py-5">
          <i class="bi bi-folder2-open display-4 text-muted opacity-50"></i>
          <p class="mt-3 text-muted fw-bold">لا يوجد أبحاث مسجلة تطابق البحث.</p>
          <a href="{{ route('visits.create') }}" class="btn btn-sm btn-success">
            <i class="bi bi-plus-lg me-1"></i> تسجيل استمارة بحث جديدة
          </a>
        </div>
      @endif
    </div>
  </div>
</div>

<!-- Modal جدولة موعد سريع -->
<div class="modal fade" id="newVisitModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title fw-bold">جدولة موعد زيارة جديد</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <form action="{{ route('visits.store') }}" method="POST">
        @csrf
        <input type="hidden" name="is_quick_schedule" value="1">
        <div class="modal-body">
          {{-- بحث سريع داخل تقديمات الأبحاث غير المبحوثة --}}
          <div class="mb-2">
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light"><i class="bi bi-search text-muted"></i></span>
              <input type="text" id="schedulePersonSearch" class="form-control" placeholder="ابحث بالاسم أو رقم الهاتف أو المحافظة...">
            </div>
          </div>

          {{-- قائمة تقديمات الأبحاث غير المبحوثة فقط --}}
          <div class="mb-3" id="scheduleAppGroup">
            <label class="form-label fw-bold">الحالة (من تقديمات الأبحاث غير المبحوثة) <span class="text-danger">*</span></label>
            <select name="case_application_id" id="scheduleCaseAppSelect" class="form-select" required>
              <option value="">-- اختر الحالة من تقديمات الأبحاث --</option>
              @foreach($caseApplications as $app)
                @php
                  $appLocation = trim(implode(' - ', array_filter([$app->governorate, $app->city, $app->address])));
                @endphp
                <option value="{{ $app->id }}" data-address="{{ $appLocation }}">
                  {{ $app->applicant_name }} ({{ $app->phone ?? 'بدون هاتف' }})@if($app->governorate) - {{ $app->governorate }}@endif
                </option>
              @endforeach
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">المكان أو المنطقة <span class="text-danger">*</span></label>
            <input type="text" name="address" id="scheduleVisitAddress" class="form-control" required placeholder="مثال: كفر الشيخ - دسوق - قرية ...">
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">اللوكيشن / رابط الموقع (Google Maps)</label>
            <div class="input-group">
              <input type="url" name="location_url" id="scheduleVisitLocationUrl" class="form-control" dir="ltr" placeholder="https://maps.google.com/?q=...">
              <button type="button" class="btn btn-outline-success fw-bold" id="detectGpsLocationBtn" title="تحديد موقعك الحالي تلقائياً">
                <i class="bi bi-crosshair"></i> موقعي
              </button>
            </div>
            <div class="form-text small text-muted">يمكنك لصق رابط اللوكيشن من واتساب/خرائط جوجل أو الضغط على "موقعي".</div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">الباحث الميداني <span class="text-danger">*</span></label>
            <select name="researcher_id" class="form-select" required>
              <option value="">-- اختر الباحث --</option>
              @foreach($researchers as $res)
                <option value="{{ $res->id }}" @selected(auth()->id() === $res->id)>{{ $res->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">تاريخ الزيارة <span class="text-danger">*</span></label>
            <input type="date" name="visit_date" class="form-control" required value="{{ date('Y-m-d') }}">
          </div>
          <div class="mb-3">
            <label class="form-label fw-bold">حالة الموعد</label>
            <select name="status" class="form-select" required>
              <option value="scheduled">مجدولة</option>
              <option value="completed">تمت الزيارة</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">ملاحظات مبدئية</label>
            <textarea name="report" class="form-control" rows="2" placeholder="ملاحظات حول موعد أو مسار الزيارة..."></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
          <button type="submit" class="btn btn-primary fw-bold">حفظ الموعد</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const typeApp = document.getElementById('targetTypeApp');
    const typeBen = document.getElementById('targetTypeBen');
    const appGroup = document.getElementById('scheduleAppGroup');
    const benGroup = document.getElementById('scheduleBenGroup');
    const caseSelect = document.getElementById('scheduleCaseAppSelect');
    const benSelect = document.getElementById('scheduleBenSelect');
    const searchInput = document.getElementById('schedulePersonSearch');
    const addressInput = document.getElementById('scheduleVisitAddress');
    const locationInput = document.getElementById('scheduleVisitLocationUrl');
    const gpsBtn = document.getElementById('detectGpsLocationBtn');

    function syncTargetMode() {
      const isBen = typeBen && typeBen.checked;
      if (appGroup) appGroup.classList.toggle('d-none', isBen);
      if (benGroup) benGroup.classList.toggle('d-none', !isBen);
      if (caseSelect) {
        caseSelect.required = !isBen;
        if (isBen) caseSelect.value = '';
      }
      if (benSelect) {
        benSelect.required = isBen;
        if (!isBen) benSelect.value = '';
      }
      if (searchInput) {
        searchInput.value = '';
        filterActiveSelect('');
      }
    }

    function filterActiveSelect(term) {
      const activeSelect = (typeBen && typeBen.checked) ? benSelect : caseSelect;
      if (!activeSelect) return;
      const q = term.trim().toLowerCase();
      Array.from(activeSelect.options).forEach((opt, idx) => {
        if (idx === 0) return;
        const text = (opt.textContent || '').toLowerCase();
        opt.hidden = q !== '' && !text.includes(q);
      });
    }

    if (typeApp) typeApp.addEventListener('change', syncTargetMode);
    if (typeBen) typeBen.addEventListener('change', syncTargetMode);
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        filterActiveSelect(this.value);
      });
    }

    [caseSelect, benSelect].forEach(function (sel) {
      if (!sel || !addressInput) return;
      sel.addEventListener('change', function () {
        const opt = sel.options[sel.selectedIndex];
        const addr = opt ? (opt.getAttribute('data-address') || '') : '';
        if (addr) addressInput.value = addr;
      });
    });

    if (gpsBtn && locationInput) {
      gpsBtn.addEventListener('click', function () {
        if (!navigator.geolocation) {
          alert('المتصفح لا يدعم تحديد الموقع الجغرافي (GPS).');
          return;
        }
        gpsBtn.disabled = true;
        gpsBtn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
        navigator.geolocation.getCurrentPosition(
          function (pos) {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            locationInput.value = `https://www.google.com/maps?q=${lat},${lng}`;
            gpsBtn.disabled = false;
            gpsBtn.innerHTML = '<i class="bi bi-check-lg"></i> تم';
          },
          function () {
            alert('تعذر الحصول على الموقع الحالي. يرجى السماح بصلاحية الموقع أو لصق الرابط يدوياً.');
            gpsBtn.disabled = false;
            gpsBtn.innerHTML = '<i class="bi bi-crosshair"></i> موقعي';
          },
          { enableHighAccuracy: true, timeout: 10000 }
        );
      });
    }
  });
</script>
@endsection
