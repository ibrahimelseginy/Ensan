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
    <div class="d-flex gap-2">
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
        <div class="text-muted small fw-bold">تحتاج مراجعة</div>
        <div class="h3 fw-bolder mb-0 text-warning font-monospace">{{ $stats['needs_review'] ?? 0 }}</div>
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
                    <span class="badge bg-{{ $visit->status_color }}">{{ $visit->status_label }}</span>
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
        <div class="modal-body">
          <div class="mb-3">
            <label class="form-label fw-bold">المستفيد <span class="text-danger">*</span></label>
            <select name="beneficiary_id" class="form-select" required>
              <option value="">-- اختر المستفيد --</option>
              @foreach($beneficiaries as $ben)
                <option value="{{ $ben->id }}">{{ $ben->full_name ?? $ben->name }} ({{ $ben->code ?: $ben->id }})</option>
              @endforeach
            </select>
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
@endsection
