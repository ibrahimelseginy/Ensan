@extends('layouts.app')
@section('content')
  {{-- Welcome Card --}}
  <div class="card welcome-card mb-4 border-0"
    style="background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);">
    <div class="d-flex justify-content-between align-items-center p-4">
      <div class="d-flex align-items-center gap-4">
        <div class="bg-primary text-white p-3 rounded-circle shadow-lg d-flex align-items-center justify-content-center"
          style="width: 60px; height: 60px;">
          <i class="bi bi-kanban fs-3"></i>
        </div>
        <div>
          <h2 class="fw-bold mb-1">إدارة المشاريع</h2>
          <p class="text-muted mb-0 lead">تتبع وإدارة البرامج والمشاريع الخيرية</p>
        </div>
      </div>
      <div>
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-lg shadow-sm">
          <i class="bi bi-plus-lg me-2"></i> إضافة مشروع
        </a>
      </div>
    </div>
  </div>

  {{-- Filter Card --}}
  <div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
      <h5 class="mb-0 fw-bold"><i class="bi bi-funnel me-2 text-primary"></i> تصفية والبحث</h5>
    </div>
    <div class="card-body">
      <form method="GET">
        <div class="row g-3 align-items-end">
          <div class="col-md-4">
            <label class="form-label fw-bold small mb-1">بحث</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
              <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0 py-2" placeholder="اسم المشروع...">
            </div>
          </div>
          <div class="col-md-2">
            <label class="form-label fw-bold small mb-1">الحالة</label>
            <select name="status" class="form-select form-select-sm py-2">
              <option value="">الجميع</option>
              <option value="active" @selected(($status ?? '') === 'active')>نشط</option>
              <option value="archived" @selected(($status ?? '') === 'archived')>مؤرشف</option>
            </select>
          </div>
          <div class="col-md-4">
            <label class="form-label fw-bold small mb-1">النوع</label>
            <div class="btn-group w-100" role="group">
                <input type="radio" class="btn-check" name="fixed" id="fixed_all" value="" @checked(($fixed ?? '') === '') autocomplete="off">
                <label class="btn btn-sm btn-outline-secondary py-2" for="fixed_all">الكل</label>

                <input type="radio" class="btn-check" name="fixed" id="fixed_yes" value="1" @checked(($fixed ?? '') == '1') autocomplete="off">
                <label class="btn btn-sm btn-outline-secondary py-2" for="fixed_yes">ثابت</label>

                <input type="radio" class="btn-check" name="fixed" id="fixed_no" value="0" @checked(($fixed ?? '') == '0') autocomplete="off">
                <label class="btn btn-sm btn-outline-secondary py-2" for="fixed_no">غير ثابت</label>
            </div>
          </div>
          <div class="col-md-2">
            <button class="btn btn-sm btn-primary w-100 fw-bold py-2">
              <i class="bi bi-funnel me-1"></i> تطبيق
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <form action="{{ route('projects.bulk-destroy') }}" method="POST" id="bulkDeleteForm">
    @csrf
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check">
            <input class="form-check-input" type="checkbox" id="selectAll">
            <label class="form-check-label fw-bold" for="selectAll">تحديد الكل</label>
        </div>
        <button type="submit" class="btn btn-sm btn-outline-danger d-none" id="btnBulkDelete" onclick="return confirm('هل أنت متأكد من حذف المشاريع المحددة؟')">
            <i class="bi bi-trash"></i> حذف المحدد
        </button>
    </div>
  <div class="row g-3">
    @foreach($projects as $p)
      <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 position-relative animate-hover">
          <div class="card-body">
            <div class="position-absolute top-0 end-0 p-3" style="z-index: 3;">
                <input class="form-check-input record-checkbox" type="checkbox" name="ids[]" value="{{ $p->id }}">
            </div>
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="d-flex align-items-center gap-3 w-100 pe-4">
                    <div class="bg-primary-subtle text-primary rounded-3 d-flex align-items-center justify-content-center"
                    style="width:50px;height:50px">
                    <i class="bi bi-kanban fs-4"></i>
                    </div>
                    <div>
                        <a href="{{ route('projects.show', $p) }}" class="fw-bold text-dark fs-5 text-decoration-none stretched-link">{{ $p->name }}</a>
                        <div class="small text-muted mt-1">{{ Str::limit($p->description, 50) }}</div>
                    </div>
                </div>
                @if(isset($p->pendingRequest) && $p->pendingRequest)
                    <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-2 rounded-pill small ms-auto" style="z-index: 2; height: fit-content;">
                        <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                    </span>
                @endif
                {{-- Dropdown removed as per user request --}}
            </div>
            
            <div class="d-flex flex-wrap gap-2 mb-3">
                @if($p->status === 'active')
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-3 py-2 rounded-pill"><i class="bi bi-check-circle me-1"></i> نشط</span>
                @else
                    <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-3 py-2 rounded-pill"><i class="bi bi-archive me-1"></i> مؤرشف</span>
                @endif

                @if($p->fixed)
                    <span class="badge bg-info-subtle text-info border border-info-subtle px-3 py-2 rounded-pill"><i class="bi bi-pin-angle me-1"></i> ثابت</span>
                @endif
            </div>

            <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                <div class="small text-muted">
                    <i class="bi bi-people me-1"></i> {{ $p->volunteers()->count() }} متطوع
                </div>
                <div class="small text-muted">
                    created {{ optional($p->created_at)->format('Y-m-d') }}
                </div>
            </div>
          </div>
        </div>
      </div>
    @endforeach
  </div>
  </form>
  <div class="mt-4">{{ $projects->links() }}</div>
@endsection

{{-- Cancel/Delete Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-warning">طلب إلغاء</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إرسال طلب إلغاء لهذا المشروع؟</p>
                <div class="mb-3">
                    <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea name="reason" class="form-control" rows="3" placeholder="اكتب سبب الإلغاء هنا... - مطلوب" required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-warning">إرسال الطلب</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(actionUrl) {
        document.getElementById('cancelForm').action = actionUrl;
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.record-checkbox');
        const btnBulkDelete = document.getElementById('btnBulkDelete');

        function toggleBulkDeleteButton() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            if (anyChecked) {
                btnBulkDelete.classList.remove('d-none');
            } else {
                btnBulkDelete.classList.add('d-none');
            }
        }

        if (selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => {
                    cb.checked = selectAll.checked;
                });
                toggleBulkDeleteButton();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', toggleBulkDeleteButton);
        });
    });
</script>

