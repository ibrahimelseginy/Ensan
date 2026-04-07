@extends('layouts.app')

@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
    <div class="hero-content">
        <div class="hero-greeting">فريق العمل 👨‍💼</div>
        <h1 class="hero-title">المندوبون</h1>
        <p class="hero-subtitle">إدارة مندوبي التحصيل والتوزيع</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('delegates.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة مندوب
            </a>
        </div>
    </div>
    <i class="bi bi-person-badge-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 animate-slide-up animate-delay-1">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي المندوبين</div>
            <div class="stat-value">{{ number_format($stats['total'] ?? 0) }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-md-4 animate-slide-up animate-delay-2">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="bi bi-person-check-fill"></i></div>
            <div class="stat-label">نشط</div>
            <div class="stat-value">{{ number_format($stats['active'] ?? 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-check"></i> متاح</div>
            <i class="bi bi-person-check-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-md-4 animate-slide-up animate-delay-3">
        <div class="stat-card stat-warning">
            <div class="stat-icon"><i class="bi bi-map-fill"></i></div>
            <div class="stat-label">بدون خط سير</div>
            <div class="stat-value">{{ number_format($stats['no_route'] ?? 0) }}</div>
            <i class="bi bi-map-fill stat-bg-icon"></i>
        </div>
    </div>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-4">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="GET">
        <div class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-bold small text-uppercase text-muted">بحث</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control border-start-0"
                        placeholder="الاسم أو الهاتف...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">خط السير</label>
                <select name="route_id" class="form-select">
                    <option value="">الكل</option>
                    @foreach($routes as $r)
                        <option value="{{ $r->id }}" @selected(($routeId ?? '') == $r->id)>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">الحالة</label>
                <select name="active" class="form-select">
                    <option value="">الكل</option>
                    <option value="1" @selected(($active ?? '') === '1')>نشط</option>
                    <option value="0" @selected(($active ?? '') === '0')>غير نشط</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">خيارات أخرى</label>
                <select name="has_phone" class="form-select">
                    <option value="">الكل</option>
                    <option value="1" @selected(($hasPhone ?? '') === '1')>لديه هاتف فقط</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end gap-2 text-nowrap">
                <button class="btn btn-primary flex-fill fw-bold"><i class="bi bi-funnel me-1"></i> تصفية</button>
                <a class="btn btn-outline-secondary" href="{{ route('delegates.index') }}" title="مسح"><i class="bi bi-x-lg"></i></a>
                <a href="{{ route('delegates.export', request()->query()) }}" class="btn btn-outline-success" title="تصدير CSV">
                    <i class="bi bi-download"></i>
                </a>
            </div>
        </div>
    </form>
</div>

    <!-- Bulk Action Form -->
    <form method="POST" action="{{ route('delegates.bulk') }}" id="bulkDelegates">
        @csrf

        <!-- Delegates Grid -->
        <div class="row g-4">
            @forelse($delegates as $d)
                @php
                    $routesList = $d->donations()->with('route')->get()->pluck('route.name')->filter()->unique()->take(3)->implode(', ');
                    $currentRoute = $d->route?->name;
                @endphp
                <div class="col-md-6 col-lg-4 col-xl-3">
                    <div class="card border-0 shadow-sm h-100 position-relative hover-card"
                        onclick="window.location='{{ route('delegates.show', $d) }}'"
                        style="cursor: pointer; transition: transform 0.2s;">
                        <!-- Checkbox (Top Left) -->
                        <div class="position-absolute top-0 start-0 p-3" onclick="event.stopPropagation()">
                            <input type="checkbox" class="form-check-input del-check" name="ids[]" value="{{ $d->id }}"
                                style="transform: scale(1.2);">
                        </div>

                        <!-- Status Badge (Top Right) -->
                        <div class="position-absolute top-0 end-0 p-3">
                            @if($d->active)
                                <span class="badge bg-success-subtle text-success rounded-pill">نشط</span>
                            @else
                                <span class="badge bg-secondary-subtle text-secondary rounded-pill">غير نشط</span>
                            @endif
                        </div>

                        <div class="card-body text-center pt-5 pb-3 px-3">
                            <!-- Profile Photo -->
                            <div class="mb-3 position-relative d-inline-block">
                                @if($d->profile_photo_path)
                                    <img src="{{ Storage::url($d->profile_photo_path) }}" alt="{{ $d->name }}"
                                        class="rounded-circle object-fit-cover shadow-sm" width="100" height="100">
                                @else
                                    <div class="rounded-circle bg-body-tertiary d-flex align-items-center justify-content-center mx-auto shadow-sm text-secondary"
                                        style="width: 100px; height: 100px;">
                                        <i class="bi bi-person fs-1"></i>
                                    </div>
                                @endif
                            </div>

                            <!-- Info -->
                            <h5 class="fw-bold text-dark mb-1">{{ $d->name }}</h5>
                            <div class="text-muted small mb-2 dir-ltr">
                                @if($d->phone)
                                    <i class="bi bi-telephone me-1"></i> {{ $d->phone }}
                                @else
                                    <span class="fst-italic">لا يوجد هاتف</span>
                                @endif
                            </div>

                            <hr class="my-3 opacity-10">

                            <!-- Stats/Route -->
                            <div class="row g-2 text-start small">
                                <div class="col-12">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-map me-2 text-primary"></i>
                                        <span class="text-truncate">{{ $currentRoute ?? $routesList ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="d-flex align-items-center text-muted">
                                        <i class="bi bi-box-seam me-2 text-success"></i>
                                        <span>{{ $d->donations_count }} تبرع</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Footer Actions -->
                        <div class="card-footer bg-transparent border-0 pt-0 pb-3">
                            <div class="d-flex justify-content-center gap-2">
                                    <a href="{{ route('delegates.edit', $d) }}" class="btn btn-sm btn-outline-primary w-100" onclick="event.stopPropagation()">
                                        <i class="bi bi-pencil-square me-1"></i> طلب تعديل
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-warning w-100"
                                        onclick="event.stopPropagation(); openCancelModal('{{ route('delegates.destroy', $d) }}', true)">
                                        <i class="bi bi-x-circle me-1"></i> طلب إلغاء
                                    </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-people fs-1 d-block mb-3"></i>
                        <h5>لا يوجد مندوبين</h5>
                        <p class="mb-0">لم يتم العثور على نتائج تطابق بحثك.</p>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination & Bulk Actions -->
        <div class="d-flex justify-content-between align-items-center mt-4 flex-wrap gap-3">
            @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
            <div class="d-flex align-items-center gap-2 bg-body p-2 rounded shadow-sm border">
                <div class="form-check ms-2">
                    <input class="form-check-input" type="checkbox" id="selectAll" onclick="toggleAll(this)">
                    <label class="form-check-label" for="selectAll">تحديد الكل</label>
                </div>
                <div class="vr mx-2"></div>
                <select name="bulk_action" class="form-select form-select-sm border-0 bg-transparent" style="width:auto">
                    <option value="">إجراء جماعي...</option>
                    <option value="activate">تفعيل المحدد</option>
                    <option value="deactivate">تعطيل المحدد</option>
                    <option value="delete">حذف المحدد</option>
                </select>
                <button type="submit" class="btn btn-sm btn-primary px-3">تنفيذ</button>
            </div>
            @endif

            <div dir="ltr">{{ $delegates->withQueryString()->links() }}</div>
        </div>
    </form>


    <style>
        .hover-card:hover {
            transform: translateY(-5px) !important;
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
        }

        .dir-ltr {
            direction: ltr;
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
        function toggleAll(source) {
            document.querySelectorAll('.del-check').forEach(cb => cb.checked = source.checked);
        }

        function openCancelModal(actionUrl, isRequest) {
            const form = document.getElementById('cancelForm');
            form.action = actionUrl;
            
            const title = document.getElementById('cancelModalTitle');
            const btn = document.getElementById('cancelModalBtn');
            const reasonDiv = document.getElementById('cancelReasonDiv');
            const reasonInput = document.getElementById('cancelReasonInput');

            if (isRequest) {
                title.textContent = 'طلب إلغاء المندوب';
                title.classList.add('text-warning');
                title.classList.remove('text-danger');
                btn.textContent = 'إرسال الطلب';
                btn.classList.add('btn-warning');
                btn.classList.remove('btn-danger');
                reasonDiv.style.display = 'block';
                reasonInput.required = true;
            } else {
                title.textContent = 'حذف المندوب';
                title.classList.add('text-danger');
                title.classList.remove('text-warning');
                btn.textContent = 'حذف نهائي';
                btn.classList.add('btn-danger');
                btn.classList.remove('btn-warning');
                reasonDiv.style.display = 'block'; // admins can also add a reason if they want, or we can hide it.
                // Let's keep it visible for admins too as "Cancellation/Deletion Reason"
                reasonInput.required = false; 
            }
            
            new bootstrap.Modal(document.getElementById('cancelModal')).show();
        }
    </script>

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
                    <p>هل أنت متأكد من رغبتك في المتابعة؟ لا يمكن التراجع عن هذا الإجراء بسهولة.</p>
                    <div class="mb-3" id="cancelReasonDiv">
                        <label class="form-label">سبب الإلغاء / الحذف <span class="text-danger">*</span></label>
                        <textarea name="cancellation_reason" id="cancelReasonInput" class="form-control" rows="3" placeholder="اكتب سبب الإلغاء هنا..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">تراجع</button>
                    <button type="submit" class="btn" id="cancelModalBtn">تأكيد</button>
                </div>
            </form>
        </div>
    </div>
@endsection

