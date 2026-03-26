@extends('layouts.app')

@section('content')
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-bar-chart-line text-primary me-2"></i>التقارير والإحصائيات
            </h4>
            <p class="text-muted mb-0 small">نظرة عامة على أداء المؤسسة والبيانات</p>
        </div>
        @if(!empty($q))
            <div class="d-flex gap-2">
                <button onclick="downloadPDF()" class="btn btn-primary shadow-sm">
                    <i class="bi bi-file-earmark-pdf"></i> تحميل PDF
                </button>
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm">
                    <i class="bi bi-arrow-right"></i> عودة للوحة التقارير
                </a>
            </div>
        @endif
    </div>

    @if(!empty($q))
        <div class="alert alert-info d-flex align-items-center mb-4">
            <i class="bi bi-search me-2 fs-4"></i>
            <div>
                <strong>نتائج البحث عن:</strong> "{{ $q }}"
            </div>
        </div>

        <div class="row g-4">
            <!-- Donors -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2 pt-3 pb-0">
                        <i class="bi bi-people text-primary fs-5"></i>
                        <h6 class="mb-0 fw-bold">المتبرعون</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($donors as $d)
                                <li
                                    class="list-group-item px-0 py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('donors.show', $d) }}"
                                        class="text-decoration-none text-dark d-block text-truncate" style="max-width: 80%;">
                                        {{ $d->name }}
                                    </a>
                                    <small class="text-muted">{{ $d->phone ?? '' }}</small>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">لا توجد نتائج</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Beneficiaries -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2 pt-3 pb-0">
                        <i class="bi bi-person-heart text-danger fs-5"></i>
                        <h6 class="mb-0 fw-bold">المستفيدون</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($beneficiaries as $b)
                                <li
                                    class="list-group-item px-0 py-2 border-bottom-0 d-flex justify-content-between align-items-center">
                                    <a href="{{ route('beneficiaries.show', $b) }}"
                                        class="text-decoration-none text-dark d-block text-truncate" style="max-width: 80%;">
                                        {{ $b->full_name }}
                                    </a>
                                    <small class="text-muted">{{ $b->phone ?? '' }}</small>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">لا توجد نتائج</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Users -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2 pt-3 pb-0">
                        <i class="bi bi-person-badge text-info fs-5"></i>
                        <h6 class="mb-0 fw-bold">المستخدمون</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($users as $u)
                                <li class="list-group-item px-0 py-2 border-bottom-0">
                                    <a href="{{ route('users.show', $u) }}"
                                        class="text-decoration-none text-dark d-flex align-items-center gap-2">
                                        <div class="avatar-sm bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center"
                                            style="width:32px;height:32px">{{ substr($u->name, 0, 1) }}</div>
                                        <div class="d-flex flex-column">
                                            <span class="fw-medium">{{ $u->name }}</span>
                                            <small class="text-muted" style="font-size:0.75rem">{{ $u->email }}</small>
                                        </div>
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">لا توجد نتائج</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2 pt-3 pb-0">
                        <i class="bi bi-box-seam text-warning fs-5"></i>
                        <h6 class="mb-0 fw-bold">الأصناف</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($items as $i)
                                <li class="list-group-item px-0 py-2 border-bottom-0">
                                    <a href="{{ route('items.show', $i) }}" class="text-decoration-none text-dark">
                                        {{ $i->name }} <span
                                            class="badge bg-secondary-subtle text-body ms-1">{{ $i->sku ?? '' }}</span>
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">لا توجد نتائج</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Warehouses -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2 pt-3 pb-0">
                        <i class="bi bi-building text-secondary fs-5"></i>
                        <h6 class="mb-0 fw-bold">المخازن</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($warehouses as $w)
                                <li class="list-group-item px-0 py-2 border-bottom-0">
                                    <a href="{{ route('warehouses.show', $w) }}" class="text-decoration-none text-dark">
                                        {{ $w->name }}
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">لا توجد نتائج</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Delegates -->
            <div class="col-md-6 col-lg-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex align-items-center gap-2 pt-3 pb-0">
                        <i class="bi bi-briefcase text-success fs-5"></i>
                        <h6 class="mb-0 fw-bold">المندوبون</h6>
                    </div>
                    <div class="card-body">
                        <ul class="list-group list-group-flush">
                            @forelse($delegates as $dg)
                                <li class="list-group-item px-0 py-2 border-bottom-0">
                                    <a href="{{ route('delegates.show', $dg) }}" class="text-decoration-none text-dark">
                                        {{ $dg->name }}
                                    </a>
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center py-4">لا توجد نتائج</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>

    @else

        <div class="row g-4 mb-4">
            <!-- Donors Stats -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span
                                    class="d-flex align-items-center justify-content-center bg-primary bg-opacity-10 text-primary rounded-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-people fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">إجمالي المتبرعين</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-baseline">
                            <h3 class="mb-0 fw-bold">{{ $donorsCount }}</h3>
                            <small class="text-success ms-2 fw-medium"><i class="bi bi-arrow-up-short"></i> نشط</small>
                        </div>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">متبرعون متكررون</span>
                                <span class="fw-bold">{{ $donorsRecurring }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Donations Stats -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span
                                    class="d-flex align-items-center justify-content-center bg-success bg-opacity-10 text-success rounded-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-cash-stack fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">التبرعات النقدية</h6>
                            </div>
                        </div>
                        <h3 class="mb-0 fw-bold">{{ number_format($cash, 0) }} <small class="text-muted fs-6">ج.م</small></h3>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">تبرعات عينية (تقديري)</span>
                                <span class="fw-bold">{{ number_format($inKind, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Inventory Stats -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span
                                    class="d-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-box-seam fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">المخزون</h6>
                            </div>
                        </div>
                        <h3 class="mb-0 fw-bold">{{ $inventoryNet }} <small class="text-muted fs-6">وحدة</small></h3>
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted small">حالة المخزون</span>
                                <span class="badge bg-success bg-opacity-10 text-success">جيد</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Finance Stats -->
            <div class="col-md-6 col-xl-3">
                <div class="card border-0 shadow-sm h-100 overflow-hidden">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <div class="flex-shrink-0">
                                <span
                                    class="d-flex align-items-center justify-content-center bg-info bg-opacity-10 text-info rounded-3"
                                    style="width: 48px; height: 48px;">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="mb-0 text-muted">المالية</h6>
                            </div>
                        </div>
                        <div class="d-flex flex-column gap-1">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">مدين</span>
                                <span class="fw-bold text-danger">{{ number_format($finance->debit, 0) }}</span>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span class="text-muted small">دائن</span>
                                <span class="fw-bold text-success">{{ number_format($finance->credit, 0) }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <!-- Beneficiaries Breakdown -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="card-title fw-bold mb-0">حالات المستفيدين</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th scope="col" class="border-0 rounded-start">الحالة</th>
                                        <th scope="col" class="border-0 text-end rounded-end">العدد</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($beneficiariesByStatus as $s)
                                        <tr>
                                            <td>
                                                <span class="d-flex align-items-center gap-2">
                                                    <span class="d-inline-block rounded-circle bg-primary"
                                                        style="width: 8px; height: 8px;"></span>
                                                    {{ $s->status }}
                                                </span>
                                            </td>
                                            <td class="text-end fw-bold">{{ $s->count }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="2" class="text-center text-muted">لا توجد بيانات</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions or Other Reports -->
            <div class="col-md-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4">
                        <h5 class="card-title fw-bold mb-0">روابط سريعة</h5>
                    </div>
                    <div class="card-body px-4">
                        <div class="d-grid gap-2">
                            <a href="{{ route('donations.index') }}"
                                class="btn btn-outline-primary text-start p-3 d-flex align-items-center justify-content-between group-hover">
                                <span><i class="bi bi-file-earmark-plus me-2"></i> إضافة تبرع جديد</span>
                                <i class="bi bi-chevron-left small opacity-50"></i>
                            </a>
                            <a href="{{ route('beneficiaries.index') }}"
                                class="btn btn-outline-success text-start p-3 d-flex align-items-center justify-content-between group-hover">
                                <span><i class="bi bi-person-plus me-2"></i> تسجيل مستفيد جديد</span>
                                <i class="bi bi-chevron-left small opacity-50"></i>
                            </a>
                            <a href="{{ route('reports.index', ['q' => 'today']) }}"
                                class="btn btn-outline-secondary text-start p-3 d-flex align-items-center justify-content-between group-hover">
                                <span><i class="bi bi-calendar-check me-2"></i> تقرير اليوم</span>
                                <i class="bi bi-chevron-left small opacity-50"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    @endif
@endsection

@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    function downloadPDF() {
        const element = document.querySelector('body'); 
        const opt = {
            margin:       10,
            filename:     'Ensan-Report-' + new Date().toISOString().slice(0, 10) + '.pdf',
            image:        { type: 'jpeg', quality: 0.98 },
            html2canvas:  { scale: 2, useCORS: true, ignoreElements: (element) => {
                return element.classList.contains('btn') || 
                       element.classList.contains('navbar') || 
                       element.classList.contains('no-print');
            }},
            jsPDF:        { unit: 'mm', format: 'a4', orientation: 'landscape' }
        };
        html2pdf().set(opt).from(element).save();
    }
</script>
@endsection