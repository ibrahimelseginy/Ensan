@extends('layouts.app')

@section('title', 'خط سير الباحث للزيارات الميدانية')

@section('content')
<div class="container-fluid py-3" dir="rtl">
  {{-- Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 d-print-none">
    <div>
      <h3 class="mb-0 fw-bolder text-dark">
        <i class="bi bi-signpost-split-fill text-danger me-1"></i> خط سير الباحث للزيارات الميدانية
      </h3>
      <p class="text-muted small mb-0 mt-1">ترتيب المحطات اليومية للباحث مع روابط اللوكيشن على خرائط Google وإرسال المسار عبر واتساب</p>
    </div>
    <div class="d-flex gap-2 flex-wrap">
      <a href="{{ route('visits.index') }}" class="btn btn-outline-secondary fw-bold">
        <i class="bi bi-arrow-right me-1"></i> العودة للزيارات
      </a>
      @if($multiStopMapsUrl)
        <a href="{{ $multiStopMapsUrl }}" target="_blank" rel="noopener" class="btn btn-primary fw-bold">
          <i class="bi bi-map-fill me-1"></i> فتح خط السير كامل على خرائط Google
        </a>
      @endif
      @if($routeVisits->isNotEmpty())
        <a href="{{ $whatsappUrl }}" target="_blank" rel="noopener" class="btn btn-success fw-bold">
          <i class="bi bi-whatsapp me-1"></i> إرسال خط السير للباحث
        </a>
        <button type="button" onclick="window.print()" class="btn btn-dark fw-bold">
          <i class="bi bi-printer-fill me-1"></i> طباعة خط السير
        </button>
      @endif
    </div>
  </div>

  {{-- Filter Bar --}}
  <div class="card border-0 shadow-sm rounded-4 mb-4 d-print-none">
    <div class="card-body">
      <form method="GET" action="{{ route('visits.researcher-route') }}" class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label fw-bold small">الباحث الميداني</label>
          <select name="researcher_id" class="form-select">
            <option value="">-- جميع الباحثين في هذا اليوم --</option>
            @foreach($researchers as $res)
              <option value="{{ $res->id }}" @selected((string)$researcherId === (string)$res->id)>
                {{ $res->name }}@if($res->phone) ({{ $res->phone }})@endif
              </option>
            @endforeach
          </select>
        </div>
        <div class="col-md-4">
          <label class="form-label fw-bold small">تاريخ خط السير</label>
          <input type="date" name="date" class="form-control" value="{{ $date }}">
        </div>
        <div class="col-md-3 d-flex gap-2">
          <button type="submit" class="btn btn-primary fw-bold w-100">
            <i class="bi bi-funnel-fill me-1"></i> عرض خط السير
          </button>
          <a href="{{ route('visits.researcher-route') }}" class="btn btn-outline-secondary" title="اليوم">
            اليوم
          </a>
        </div>
      </form>
    </div>
  </div>

  {{-- Print Header --}}
  <div class="d-none d-print-block mb-4 pb-3 border-bottom">
    <h4 class="fw-bold mb-1">مؤسسة إنسان الخيرية — خط سير الباحث للزيارات الميدانية</h4>
    <div class="small text-muted">
      التاريخ: <strong>{{ $date }}</strong>
      @if($selectedResearcher) | الباحث: <strong>{{ $selectedResearcher->name }}</strong> ({{ $selectedResearcher->phone ?? '—' }}) @endif
      | إجمالي المحطات: <strong>{{ $routeVisits->count() }}</strong>
    </div>
  </div>

  {{-- Route Summary Strip --}}
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 border-start border-4 border-primary">
        <div class="text-muted small fw-bold">إجمالي المحطات في خط السير</div>
        <div class="h3 fw-bolder mb-0 text-primary font-monospace">{{ $routeVisits->count() }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 border-start border-4 border-warning">
        <div class="text-muted small fw-bold">زيارات مجدولة قيد التنفيذ</div>
        <div class="h3 fw-bolder mb-0 text-warning font-monospace">{{ $routeVisits->where('status', 'scheduled')->count() }}</div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card border-0 shadow-sm rounded-4 p-3 h-100 border-start border-4 border-success">
        <div class="text-muted small fw-bold">زيارات مكتملة / بانتظار المناقشة</div>
        <div class="h3 fw-bolder mb-0 text-success font-monospace">{{ $routeVisits->whereIn('status', ['completed', 'awaiting_discussion', 'accepted'])->count() }}</div>
      </div>
    </div>
  </div>

  {{-- Stops Timeline --}}
  @if($routeVisits->isEmpty())
    <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
      <i class="bi bi-signpost-2 display-4 text-muted opacity-50"></i>
      <h5 class="fw-bold text-dark mt-3">لا توجد زيارات مجدولة في خط السير لهذا التاريخ</h5>
      <p class="text-muted small">اختر تاريخاً آخر أو قم بجدولة موعد زيارة جديد من صفحة الزيارات الميدانية.</p>
      <div>
        <a href="{{ route('visits.index') }}" class="btn btn-primary fw-bold px-4">
          <i class="bi bi-calendar-plus me-1"></i> الذهاب لجدولة زيارة
        </a>
      </div>
    </div>
  @else
    <div class="position-relative" style="padding-right: 12px;">
      @foreach($routeVisits as $index => $visit)
        @php
          $stopName = $visit->patient_name ?: ($visit->beneficiary?->full_name ?? 'حالة #' . $visit->id);
          $stopPhone = $visit->phone ?: ($visit->beneficiary?->phone ?? null);
          $cleanPhone = preg_replace('/\D/', '', (string) $stopPhone);
          if (str_starts_with($cleanPhone, '01') && strlen($cleanPhone) === 11) {
              $cleanPhone = '2' . $cleanPhone;
          }
          $stopAddr = $visit->address ?: ($visit->beneficiary?->address ?? null);
          $stopMap = $visit->location_url ?: ($stopAddr ? 'https://www.google.com/maps/search/?api=1&query=' . urlencode($stopAddr) : null);
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-3 overflow-hidden">
          <div class="card-body p-3 p-md-4">
            <div class="d-flex flex-wrap align-items-start justify-content-between gap-3">
              <div class="d-flex align-items-start gap-3">
                <div class="rounded-circle d-flex align-items-center justify-content-center fw-bolder text-white flex-shrink-0 shadow-sm"
                     style="width: 44px; height: 44px; background: linear-gradient(135deg, #0d6efd, #2563eb); font-size: 1.1rem;">
                  {{ $index + 1 }}
                </div>
                <div>
                  <div class="d-flex align-items-center flex-wrap gap-2 mb-1">
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2.5">المحطة {{ $index + 1 }}</span>
                    <h5 class="fw-bolder text-dark mb-0">{{ $stopName }}</h5>
                    @if($visit->status === 'scheduled')
                      <span class="badge bg-info text-dark">مجدولة</span>
                    @elseif($visit->status === 'awaiting_discussion')
                      <span class="badge bg-warning text-dark">في انتظار المناقشة</span>
                    @elseif($visit->status === 'accepted')
                      <span class="badge bg-success">مقبول ومعتمد</span>
                    @else
                      <span class="badge bg-secondary">{{ $visit->status_label }}</span>
                    @endif
                  </div>

                  <div class="d-flex flex-wrap gap-3 small text-muted mt-2">
                    <span>
                      <i class="bi bi-person-badge text-primary me-1"></i>
                      <strong>الباحث:</strong> {{ $visit->researcher?->name ?? 'غير محدد' }}
                    </span>
                    @if($stopPhone)
                      <span dir="ltr" class="font-monospace">
                        <i class="bi bi-telephone-fill text-success me-1"></i>
                        {{ $stopPhone }}
                      </span>
                    @endif
                    @if($stopAddr)
                      <span>
                        <i class="bi bi-geo-alt-fill text-danger me-1"></i>
                        <strong>المكان:</strong> {{ $stopAddr }}
                      </span>
                    @endif
                  </div>

                  @if(!empty($visit->report))
                    <div class="mt-2 p-2 px-3 rounded-3 bg-light small text-dark border">
                      <i class="bi bi-sticky text-warning me-1"></i> {{ $visit->report }}
                    </div>
                  @endif
                </div>
              </div>

              <div class="d-flex flex-wrap gap-2 d-print-none">
                @if($stopMap)
                  <a href="{{ $stopMap }}" target="_blank" rel="noopener" class="btn btn-sm btn-success fw-bold rounded-pill px-3">
                    <i class="bi bi-pin-map-fill me-1"></i> اللوكيشن
                  </a>
                @endif
                @if($stopPhone)
                  <a href="tel:{{ $stopPhone }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                    <i class="bi bi-telephone me-1"></i> اتصال
                  </a>
                  @if($cleanPhone)
                    <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" rel="noopener" class="btn btn-sm btn-outline-success rounded-pill px-3">
                      <i class="bi bi-whatsapp me-1"></i> واتساب
                    </a>
                  @endif
                @endif
                <a href="{{ route('visits.edit', $visit) }}" class="btn btn-sm btn-outline-warning text-dark fw-bold rounded-pill px-3">
                  <i class="bi bi-pencil-square me-1"></i> استكمال الاستمارة
                </a>
                <a href="{{ route('visits.show', $visit) }}" class="btn btn-sm btn-light border rounded-pill px-3">
                  <i class="bi bi-eye me-1"></i> عرض
                </a>
              </div>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  @endif
</div>
@endsection

