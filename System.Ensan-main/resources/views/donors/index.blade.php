@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #ec4899 0%, #db2777 50%, #be185d 100%);">
    <div class="hero-content">
        <div class="hero-greeting">إدارة الموارد ❤️</div>
        <h1 class="hero-title">المتبرعون</h1>
        <p class="hero-subtitle">عرض وإدارة قاعدة بيانات المتبرعين وسجل تبرعاتهم</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('donations.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة تبرع
            </a>
        </div>
    </div>
    <i class="bi bi-heart-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-1">
        <a href="{{ route('donors.index') }}" class="stat-card stat-info text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي المتبرعين</div>
            <div class="stat-value">{{ number_format($totals['all']) }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-2">
        <a href="{{ route('donors.index', ['active' => '1']) }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">متبرع نشط</div>
            <div class="stat-value">{{ number_format($totals['active']) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> نشط</div>
            <i class="bi bi-check-circle-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-3">
        <a href="{{ route('donors.index', ['classification' => 'recurring']) }}" class="stat-card stat-purple text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-arrow-repeat"></i></div>
            <div class="stat-label">النوع: متكرر</div>
            <div class="stat-value">{{ number_format($totals['recurring']) }}</div>
            <i class="bi bi-arrow-repeat stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg-3 animate-slide-up animate-delay-4">
        <a href="{{ route('donors.index', ['classification' => 'one_time']) }}" class="stat-card stat-warning text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-1-circle-fill"></i></div>
            <div class="stat-label">النوع: مرة واحدة</div>
            <div class="stat-value">{{ number_format($totals['one_time']) }}</div>
            <i class="bi bi-1-circle-fill stat-bg-icon"></i>
        </a>
    </div>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-5">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">بحث سريع</label>
            <div class="input-group">
                <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0" placeholder="بحث بالاسم...">
            </div>
        </div>
        
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">نوع الجهة</label>
             <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="type" id="typeAll" value="" {{ ($type ?? '') == '' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-secondary btn-sm d-flex align-items-center justify-content-center" for="typeAll">الكل</label>

                <input type="radio" class="btn-check" name="type" id="typeInd" value="individual" {{ ($type ?? '') == 'individual' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center" for="typeInd">
                    <i class="bi bi-person me-1"></i> فرد
                </label>

                <input type="radio" class="btn-check" name="type" id="typeOrg" value="organization" {{ ($type ?? '') == 'organization' ? 'checked' : '' }} onchange="this.form.submit()">
                <label class="btn btn-outline-primary btn-sm d-flex align-items-center justify-content-center" for="typeOrg">
                    <i class="bi bi-building me-1"></i> منظمة
                </label>
            </div>
        </div>

        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">تصنيف التبرع</label>
            <select name="classification" class="form-select" onchange="this.form.submit()">
                <option value="">الكل</option>
                <option value="one_time" @selected(($classification ?? '')==='one_time')>مرة واحدة</option>
                <option value="recurring" @selected(($classification ?? '')==='recurring')>متكرر</option>
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label fw-bold small text-uppercase text-muted">حالة الحساب</label>
            <select name="active" class="form-select" onchange="this.form.submit()">
                <option value="">الكل</option>
                <option value="1" @selected(($active ?? '')==='1')>نشط</option>
                <option value="0" @selected(($active ?? '')==='0')>غير نشط</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100 fw-bold">
                <i class="bi bi-funnel me-1"></i> تطبيق
            </button>
        </div>
    </form>
</div>

<div class="row g-3">
@foreach($donors as $donor)
  <div class="col-md-6 col-lg-4">
    <div class="kpi-card p-4 h-100 position-relative {{ $donor->classification==='recurring' ? 'kpi-primary' : 'kpi-info' }}">
      <div class="d-flex justify-content-between align-items-start mb-3">
          <div class="d-flex align-items-center gap-3">
             <input type="checkbox" class="form-check-input donor-check" value="{{ $donor->id }}">
             <div class="position-relative">
                <div class="rounded-circle bg-white shadow-sm d-flex align-items-center justify-content-center text-primary" style="width: 56px; height: 56px;">
                  <i class="bi {{ $donor->type === 'individual' ? 'bi-person' : 'bi-building' }} fs-3"></i>
                </div>
                 @if($donor->active)
                 <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-success border border-white rounded-circle"></span>
                 @else
                 <span class="position-absolute bottom-0 start-100 translate-middle p-1 bg-danger border border-white rounded-circle"></span>
                 @endif
             </div>
             <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <a href="{{ route('donors.show',$donor) }}" class="text-decoration-none text-dark stretched-link-custom">{{ $donor->name }}</a>
                </h5>
                <div class="text-muted small">
                    {{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }} • {{ $donor->classification==='recurring' ? 'متكرر' : 'مرة واحدة' }}
                </div>
             </div>
          </div>
          @if(isset($donor->pendingRequest) && $donor->pendingRequest)
              <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-2 rounded-pill small">
                  <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
              </span>
          @else
              <div class="dropdown">
                  <button class="btn btn-sm btn-light border-0" type="button" data-bs-toggle="dropdown">
                      <i class="bi bi-three-dots-vertical"></i>
                  </button>
                  <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                      <li><a class="dropdown-item" href="{{ route('donors.show',$donor) }}"><i class="bi bi-eye me-2 opacity-50"></i>عرض الملف</a></li>
                      
                      @if(auth()->check())
                              <li><a class="dropdown-item text-warning" href="{{ route('donors.edit',$donor) }}"><i class="bi bi-pencil me-2 opacity-50"></i>طلب تعديل</a></li>
                          
                          <li><hr class="dropdown-divider"></li>
                          <li>
                            <button class="dropdown-item text-warning" onclick="openCancelModal('{{ route('donors.destroy',$donor) }}', true)">
                                <i class="bi bi-x-circle me-2 opacity-50"></i>طلب إلغاء
                            </button>
                          </li>
                      @endif
                  </ul>
              </div>
          @endif
      </div>

      <div class="row g-2 mb-3 mt-2 text-center">
          @php $st = $donStats->get($donor->id); @endphp
          <div class="col-6">
              <div class="bg-light rounded p-2">
                  <div class="small text-muted mb-1">إجمالي التبرعات</div>
                  <div class="fw-bold text-dark">{{ $st ? number_format($st->total, 0) : '0' }}</div>
              </div>
          </div>
          <div class="col-6">
              <div class="bg-light rounded p-2">
                  <div class="small text-muted mb-1">عدد المرات</div>
                  <div class="fw-bold text-dark">{{ $st ? $st->count : 0 }}</div>
              </div>
          </div>
      </div>

      <div class="d-flex align-items-center justify-content-between pt-2 border-top">
           <div class="text-muted small">
              <i class="bi bi-telephone me-1"></i> <span dir="ltr">{{ $donor->phone ?? '—' }}</span>
           </div>
           <a href="{{ route('donations.create', ['donor_id' => $donor->id]) }}" class="btn btn-sm btn-primary rounded-pill px-3">
              <i class="bi bi-plus-lg me-1"></i> تبرع
           </a>
      </div>
    </div>
  </div>
@endforeach
</div>

{{-- Bulk Action Bar --}}
<div class="d-flex gap-2 align-items-center mt-3 p-3 bg-body-tertiary rounded shadow-sm" id="donorBulkBar" style="display:none;">
    <input type="checkbox" id="selectAllDonors" class="form-check-input" onclick="document.querySelectorAll('.donor-check').forEach(cb=>cb.checked=this.checked); updateDonorBulkBar();">
    <span class="small fw-bold" id="donorSelectedCount">0 محدد</span>
    <button class="btn btn-danger btn-sm ms-auto" onclick="submitDonorBulkDelete()"><i class="bi bi-trash me-1"></i> حذف المحدد</button>
</div>

{{-- Hidden Bulk Delete Form --}}
<form id="donorBulkDeleteForm" method="POST" action="{{ route('donors.bulk-destroy') }}" style="display:none;">
    @csrf
    <div id="donorBulkDeleteIds"></div>
</form>

<div class="mt-3">{{ $donors->links() }}</div>
@endsection

{{-- Cancel/Delete Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalTitle">تأكيد الإجراء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في المتابعة؟</p>
                <div class="mb-3" id="cancelReasonDiv">
                    <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea name="reason" id="cancelReasonInput" class="form-control" rows="3" placeholder="اكتب سبب الإلغاء هنا... - مطلوب" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-warning" id="cancelModalBtn">إرسال الطلب</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(actionUrl, isRequest) {
        const form = document.getElementById('cancelForm');
        form.action = actionUrl;
        
        const title = document.getElementById('cancelModalTitle');
        const btn = document.getElementById('cancelModalBtn');

        title.textContent = 'طلب إلغاء المتبرع';
        title.className = 'modal-title text-warning';
        
        btn.textContent = 'إرسال طلب الإلغاء';
        btn.className = 'btn btn-warning';
        
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('donor-check')) updateDonorBulkBar();
    });

    function updateDonorBulkBar() {
        const checked = document.querySelectorAll('.donor-check:checked').length;
        const bar = document.getElementById('donorBulkBar');
        document.getElementById('donorSelectedCount').textContent = checked + ' محدد';
        bar.style.display = checked > 0 ? 'flex' : 'none';
    }

    function submitDonorBulkDelete() {
        const ids = [];
        document.querySelectorAll('.donor-check:checked').forEach(cb => ids.push(cb.value));
        if (ids.length === 0) { alert('يرجى اختيار متبرع واحد على الأقل'); return; }
        if (!confirm('هل أنت متأكد من حذف ' + ids.length + ' متبرع؟')) return;
        const form = document.getElementById('donorBulkDeleteForm');
        const container = document.getElementById('donorBulkDeleteIds');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
            container.appendChild(input);
        });
        form.submit();
    }
</script>


