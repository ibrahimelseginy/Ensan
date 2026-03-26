@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);">
    <div class="hero-content">
        <div class="hero-greeting">خدمات المستفيدين 👨‍👩‍👧‍👦</div>
        <h1 class="hero-title">إدارة المستفيدين</h1>
        <p class="hero-subtitle">إدارة ملفات الأسر والأفراد المستفيدين من المساعدات</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('beneficiaries.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة مستفيد
            </a>
        </div>
    </div>
    <i class="bi bi-people-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg animate-slide-up animate-delay-1">
        <a href="{{ route('beneficiaries.index') }}" class="stat-card stat-primary text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-2">
        <a href="{{ route('beneficiaries.index', ['status' => 'new']) }}" class="stat-card stat-info text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-plus-circle-fill"></i></div>
            <div class="stat-label">جديد</div>
            <div class="stat-value">{{ number_format($stats['new']) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> حالات جديدة</div>
            <i class="bi bi-plus-circle-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-3">
        <a href="{{ route('beneficiaries.index', ['status' => 'under_review']) }}" class="stat-card stat-warning text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-hourglass-split"></i></div>
            <div class="stat-label">تحت المراجعة</div>
            <div class="stat-value">{{ number_format($stats['under_review']) }}</div>
            <i class="bi bi-hourglass-split stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-4">
        <a href="{{ route('beneficiaries.index', ['status' => 'accepted']) }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">مقبول</div>
            <div class="stat-value">{{ number_format($stats['accepted']) }}</div>
            <div class="stat-trend up"><i class="bi bi-check"></i> مقبول</div>
            <i class="bi bi-check-circle-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-5">
        <a href="{{ route('beneficiaries.index', ['status' => 'rejected']) }}" class="stat-card stat-danger text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-x-circle-fill"></i></div>
            <div class="stat-label">مرفوض</div>
            <div class="stat-value">{{ number_format($stats['rejected']) }}</div>
            <i class="bi bi-x-circle-fill stat-bg-icon"></i>
        </a>
    </div>
</div>
{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-5">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="GET">
        <div class="row g-3">
            {{-- Row 1 --}}
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">بحث</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input name="q" value="{{ $q ?? '' }}" class="form-control border-start-0" placeholder="بالاسم/الهاتف/الكود...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">الحالة</label>
                <select name="status" class="form-select">
                    <option value="">الكل</option>
                    <option value="new" @selected(($status ?? '') === 'new')>جديد</option>
                    <option value="under_review" @selected(($status ?? '') === 'under_review')>تحت المراجعة</option>
                    <option value="accepted" @selected(($status ?? '') === 'accepted')>مقبول</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">نوع المساعدة</label>
                <select name="assistance_type" class="form-select">
                    <option value="">الكل</option>
                    <option value="financial" @selected(($atype ?? '') === 'financial')>مالية</option>
                    <option value="in_kind" @selected(($atype ?? '') === 'in_kind')>عينية</option>
                    <option value="service" @selected(($atype ?? '') === 'service')>خدمية</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">المشروع</label>
                <select name="project_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" @selected(($projectId ?? '') == $p->id)>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">الحملة</label>
                <select name="campaign_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach($campaigns as $c)
                        <option value="{{ $c->id }}" @selected(($campaignId ?? '') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Row 2 --}}
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">من تاريخ</label>
                <input type="date" name="date_from" value="{{ $dateFrom ?? '' }}" class="form-control">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">إلى تاريخ</label>
                <input type="date" name="date_to" value="{{ $dateTo ?? '' }}" class="form-control">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">العنوان يحتوي</label>
                <input name="address_like" value="{{ $addressLike ?? '' }}" class="form-control" placeholder="مدينة/حي...">
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">لديهم هاتف</label>
                <select name="has_phone" class="form-select">
                    <option value="">الكل</option>
                    <option value="1" @selected(($hasPhone ?? '') === '1')>نعم</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold small text-uppercase text-muted">لديهم مرفقات</label>
                <select name="has_attachments" class="form-select">
                    <option value="">الكل</option>
                    <option value="1" @selected(($hasAttachments ?? '') === '1')>نعم</option>
                </select>
            </div>

            {{-- Row 3 --}}
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">عدد الصفوف</label>
                <select name="per_page" class="form-select">
                    <option value="20" @selected(($perPage ?? 20) == 20)>20</option>
                    <option value="50" @selected(($perPage ?? 20) == 50)>50</option>
                    <option value="100" @selected(($perPage ?? 20) == 100)>100</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end gap-2">
                <button class="btn btn-primary flex-fill fw-bold"><i class="bi bi-funnel me-1"></i> تصفية</button>
                <a class="btn btn-outline-secondary" href="{{ route('beneficiaries.index') }}" title="مسح التصفية"><i class="bi bi-x-lg"></i></a>
                <a class="btn btn-outline-secondary" href="{{ route('beneficiaries.export', request()->query()) }}" title="تصدير Excel"><i class="bi bi-download"></i></a>
            </div>
        </div>
    </form>
</div>
  <div class="table-responsive">
    @php
      $curSort = $sort ?? request('sort');
      $curDir = $dir ?? request('dir');
      $toggle = ($curDir === 'asc' ? 'desc' : 'asc');
    @endphp
    <div id="tableContainer">
      <table class="table table-striped">
        <thead>
          <tr>
            <th><input type="checkbox"
                onclick="document.querySelectorAll('.row-check').forEach(cb=>cb.checked=this.checked)"></th>
            <th><a
                href="{{ route('beneficiaries.index', array_merge(request()->query(), ['sort' => 'id', 'dir' => $toggle])) }}">#</a>
            </th>
            <th>الكود</th>
            <th><a
                href="{{ route('beneficiaries.index', array_merge(request()->query(), ['sort' => 'full_name', 'dir' => $toggle])) }}">الاسم</a>
            </th>
            <th>الرقم القومي</th>
            <th>الهاتف</th>
            <th><a
                href="{{ route('beneficiaries.index', array_merge(request()->query(), ['sort' => 'status', 'dir' => $toggle])) }}">الحالة</a>
            </th>
            <th><a
                href="{{ route('beneficiaries.index', array_merge(request()->query(), ['sort' => 'assistance_type', 'dir' => $toggle])) }}">المساعدة</a>
            </th>
            <th>المشروع</th>
            <th>الحملة</th>
            <th><a
                href="{{ route('beneficiaries.index', array_merge(request()->query(), ['sort' => 'created_at', 'dir' => $toggle])) }}">تاريخ
                الإنشاء</a></th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          @foreach($beneficiaries as $b)
            <tr>
              <td><input type="checkbox" class="row-check" name="ids[]" value="{{ $b->id }}"></td>
              <td>{{ $b->id }}</td>
              <td>{{ $b->full_name }}</td>
              <td>{{ $b->national_id ?? '—' }}</td>
              <td>{{ $b->phone ?? '—' }}</td>
              <td>
                @php
                  $s = $b->status;
                  $cls = $s === 'accepted' ? 'success' : ($s === 'under_review' ? 'warning' : 'secondary');
                  $s_ar = $s === 'new' ? 'جديد' : ($s === 'under_review' ? 'تحت المراجعة' : ($s === 'accepted' ? 'مقبول' : $s));
                @endphp
                <span class="badge bg-{{ $cls }}">{{ $s_ar }}</span>
                @php $isDup = ((in_array($b->phone, $dupPhones ?? []) && $b->phone) || (in_array($b->national_id, $dupNids ?? []) && $b->national_id)); @endphp
                @if($isDup)
                  <span class="badge bg-danger">مكرر</span>
                @endif
              </td>
              <td>
                @php
                  $t = $b->assistance_type;
                  $tcls = $t === 'financial' ? 'primary' : ($t === 'in_kind' ? 'info' : 'dark');
                  $t_ar = $t === 'financial' ? 'مالية' : ($t === 'in_kind' ? 'عينية' : ($t === 'service' ? 'خدمية' : $t));
                @endphp
                <span class="badge bg-{{ $tcls }}">{{ $t_ar }}</span>
              </td>
              <td>{{ $b->project?->name ?? '—' }}</td>
              <td>{{ $b->campaign?->name ? ($b->campaign->name . ' (' . $b->campaign->season_year . ')') : '—' }}</td>
              <td>{{ optional($b->created_at)->format('Y-m-d') ?? '—' }}</td>
              <td class="text-end">
                @if(isset($b->pendingRequest) && $b->pendingRequest)
                    <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-2 py-1 rounded-pill small">
                        <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                    </span>
                @else
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                          <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="z-index: 9999;">
                            <li><a class="dropdown-item" href="{{ route('beneficiaries.show', $b) }}"><i class="bi bi-eye me-2 text-primary"></i> عرض</a></li>
                            
                            <li><a class="dropdown-item text-warning" href="{{ route('beneficiaries.edit', $b) }}"><i class="bi bi-pencil me-2 text-warning"></i> طلب تعديل</a></li>

                            @php
                              $attCount = $b->attachments_count ?? 0;
                              $next = $b->status === 'new' ? 'under_review' : ($b->status === 'under_review' ? 'accepted' : null);
                              $next_ar = $next === 'under_review' ? 'نقل إلى تحت المراجعة' : ($next === 'accepted' ? 'نقل إلى مقبول' : '');
                            @endphp

                            @if($next)
                            <li>
                                <form action="{{ route('beneficiaries.update', $b) }}" method="POST">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="{{ $next }}">
                                    <button class="dropdown-item text-warning"><i class="bi bi-arrow-repeat me-2 text-warning"></i> طلب {{ $next_ar }}</button>
                                </form>
                            </li>
                            @endif

                            @if($b->status !== 'rejected')
                            <li>
                                <form action="{{ route('beneficiaries.update', $b) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من طلب نقل هذا المستفيد إلى المرفوضين؟');">
                                    @csrf @method('PUT')
                                    <input type="hidden" name="status" value="rejected">
                                    <button class="dropdown-item text-warning"><i class="bi bi-x-circle me-2"></i> طلب نقل إلى مرفوض</button>
                                </form>
                            </li>
                            @endif

                            <li><a class="dropdown-item" href="{{ route('tasks.create', ['assigned_to' => request()->user()?->id, 'subject_beneficiary_id' => $b->id]) }}"><i class="bi bi-check-square me-2"></i> مهمة مرتبطة</a></li>

                            <li><hr class="dropdown-divider"></li>

                            <li>
                                <button class="dropdown-item text-warning" onclick="openCancelModal('{{ route('beneficiaries.destroy', $b) }}')">
                                    <i class="bi bi-x-circle me-2"></i> طلب إلغاء
                                </button>
                            </li>
                        </ul>
                    </div>
                @endif
                @if(($b->attachments_count ?? 0) > 0)
                  <div class="mt-1"><span class="badge bg-info bg-opacity-10 text-info" style="font-size:0.7rem"><i
                        class="bi bi-paperclip"></i> {{ $b->attachments_count }}</span></div>
                @endif
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
    <div class="d-flex gap-2 align-items-center mt-2">
      <select id="bulkActionSelect" class="form-select" style="width:auto">
        <option value="">إجراء جماعي...</option>
        <option value="status_under_review">نقل إلى تحت المراجعة</option>
        <option value="status_accepted">نقل إلى مقبول</option>
        <option value="delete" class="text-danger">حذف المحدد</option>
      </select>
      <button class="btn btn-success" type="button" onclick="submitBulkForm()">تنفيذ</button>
    </div>
    @endif
  </div>

  {{-- Hidden Bulk Form --}}
  <form id="hiddenBulkForm" method="POST" action="{{ route('beneficiaries.bulk') }}" style="display:none;">
    @csrf
    <input type="hidden" name="bulk_action" id="hiddenBulkAction">
    <div id="hiddenBulkIds"></div>
  </form>

  <script>
    function submitBulkForm() {
      const action = document.getElementById('bulkActionSelect').value;
      if (!action) {
        alert('يرجى اختيار إجراء أولاً');
        return;
      }

      const selectedIds = [];
      document.querySelectorAll('.row-check:checked').forEach(cb => {
        selectedIds.push(cb.value);
      });

      if (selectedIds.length === 0) {
        alert('يرجى اختيار مستفيد واحد على الأقل');
        return;
      }

      if (!confirm('هل أنت متأكد من تنفيذ هذا الإجراء على ' + selectedIds.length + ' مستفيد؟')) {
        return;
      }

      const form = document.getElementById('hiddenBulkForm');
      document.getElementById('hiddenBulkAction').value = action;
      
      const idsContainer = document.getElementById('hiddenBulkIds');
      idsContainer.innerHTML = '';
      selectedIds.forEach(id => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = id;
        idsContainer.appendChild(input);
      });

      form.submit();
    }
  </script>
  <div class="mt-4 d-flex justify-content-center">
    {{ $beneficiaries->links() }}
  </div>
  <div class="row g-3 mt-3">
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <div class="small text-muted">آخر 7 أيام</div>
        <div class="h4 mb-0">{{ $last7 }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <div class="small text-muted">مالية</div>
        <div class="h4 mb-0">{{ $assistDist['financial'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <div class="small text-muted">عينية</div>
        <div class="h4 mb-0">{{ $assistDist['in_kind'] ?? 0 }}</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card p-3 text-center">
        <div class="small text-muted">خدمية</div>
        <div class="h4 mb-0">{{ $assistDist['service'] ?? 0 }}</div>
      </div>
    </div>
  </div>
@endsection

{{-- Cancel/Delete Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-warning">طلب حذف مستفيد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إرسال طلب حذف لهذا المستفيد؟</p>
                <div class="mb-3">
                    <label class="form-label">سبب الحذف <span class="text-danger">*</span></label>
                    <textarea name="rejection_reason" class="form-control" rows="3" placeholder="اكتب سبب الحذف هنا... - مطلوب" required></textarea>
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
</script>