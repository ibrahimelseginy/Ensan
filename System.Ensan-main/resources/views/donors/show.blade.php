@extends('layouts.app')
@section('content')

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex gap-3 align-items-center">
            <div class="avatar avatar-lg avatar-primary">
                {{ mb_substr($donor->name, 0, 1) }}
            </div>
            <div>
                <h4 class="mb-1">{{ $donor->name }}</h4>
                <div class="d-flex flex-wrap gap-2">
                    <span
                        class="badge bg-secondary-subtle text-body border">{{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }}</span>
                    <span
                        class="badge {{ $donor->active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        {{ $donor->active ? 'نشط' : 'غير نشط' }}
                    </span>
                    @if($donor->phone)
                        <span class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $donor->phone }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="btn-group">
            <button onclick="window.print()" class="btn btn-outline-primary">
                <i class="bi bi-printer me-1"></i> طباعة
            </button>
            <a href="{{ route('donations.create', ['donor_id' => $donor->id]) }}" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> تبرع جديد
            </a>
            @if(auth()->check())
                <a href="{{ route('donors.edit', $donor) }}" class="btn btn-outline-warning">
                    <i class="bi bi-pencil me-1"></i> طلب تعديل
                </a>
                <button class="btn btn-outline-warning" onclick="openCancelModal('{{ route('donors.destroy', $donor) }}')">
                    <i class="bi bi-x-circle me-1"></i> طلب إلغاء
                </button>
            @endif
            <a href="{{ route('donors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>البيانات الأساسية</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">الهاتف</label>
                            <div class="fw-medium">{{ $donor->phone ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">العنوان</label>
                            <div class="fw-medium">{{ $donor->address ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">التصنيف</label>
                            <div class="fw-medium">
                                {{ $donor->classification === 'recurring' ? 'متكرر' : 'مرة واحدة' }}
                            </div>
                        </div>

                        @if($donor->allocation_type === 'sponsorship' || ($donor->sponsorship_type && $donor->sponsorship_type !== 'none' && $donor->sponsorship_type !== 'sadaqa_jariya'))
                            @if($donor->sponsorship_monthly_amount > 0)
                                <div class="col-12">
                                    <hr class="my-0 opacity-25">
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">قيمة الكفالة</label>
                                    <div class="fw-medium">{{ number_format($donor->sponsorship_monthly_amount, 2) }}</div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">المدفوع ({{ now()->format('m/Y') }})</label>
                                    <div class="fw-medium text-success">{{ number_format($paidThisMonth, 2) }}</div>
                                </div>
                            @endif
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-md-4">
            <div class="row g-3">
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body p-4 text-center">
                            <div class="opacity-75 small mb-1">إجمالي التبرعات</div>
                            <div class="display-6 fw-bold">{{ number_format((float) ($stats->total ?? 0), 2) }}</div>
                            <div class="small opacity-75 mt-1">جنيه مصري</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">عدد التبرعات</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ (int) ($stats->count ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">أخر تبرع</div>
                            <div class="h5 mb-0 fw-bold text-dark">
{{ $history->first() ? optional($history->first()->received_at)->format('Y-m-d') : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>سجل التبرعات</h6>
            <span class="badge bg-secondary rounded-pill px-3">{{ count($history) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>القيمة</th>
                        <th>القناة</th>
                        <th>التوجيه</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $d)
                        <tr>
                            <td class="text-muted small">{{ $d->id }}</td>
                            <td>{{ optional($d->received_at)->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                <span
                                    class="badge {{ $d->type === 'cash' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' }}">
                                    {{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}
                                </span>
                            </td>
                            <td class="fw-bold">
                                @if($d->type === 'cash')
                                    {{ number_format($d->amount, 2) }} <small class="text-muted">{{ $d->currency }}</small>
                                @else
                                    {{ number_format($d->estimated_value, 2) }} <small class="text-muted">{{ $d->currency }}</small>
                                @endif
                            </td>
                            <td class="small">
                                @if($d->type === 'cash')
                                    {{ $d->cash_channel === 'instapay' ? 'انستا باي' : ($d->cash_channel === 'vodafone_cash' ? 'فودافون كاش' : 'نقدي') }}
                                    @if($d->receipt_number)
                                    <div class="text-muted" style="font-size:0.8em">{{ $d->receipt_number }}</div>@endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">
                                @if($d->project)
                                    <div class="text-truncate" style="max-width:150px" title="{{$d->project->name}}"><i
                                class="bi bi-building text-muted me-1"></i>{{$d->project->name}}</div>@endif
                                @if($d->campaign)
                                    <div class="text-truncate" style="max-width:150px" title="{{$d->campaign->name}}"><i
                                class="bi bi-megaphone text-muted me-1"></i>{{$d->campaign->name}}</div>@endif
                            </td>
                            <td class="text-end">
                                <a href="{{ route('donations.show', $d) }}" class="btn btn-sm btn-light text-secondary"
                                    title="عرض التفاصيل"><i class="bi bi-eye"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">لا توجد تبرعات مسجلة لهذا المتبرع حتى الآن</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Print Styles --}}
    <style>
        @media print {
            /* Hide navigation and buttons */
            .navbar,
            .sidebar-fixed,
            .btn-group,
            .page-header .btn,
            body {
                padding-top: 0 !important;
            }

            /* Optimize layout for print */
            .container-fluid {
                max-width: 100%;
            }

            .card {
                border: 1px solid #dee2e6 !important;
                box-shadow: none !important;
                page-break-inside: avoid;
            }

            /* Add header for print */
            .page-header::before {
                content: "تقرير المتبرع - مؤسسة إنسان";
                display: block;
                text-align: center;
                font-size: 1.5rem;
                font-weight: bold;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 2px solid #10b981;
            }

            /* Ensure table fits on page */
            table {
                font-size: 0.85rem;
            }

            /* Add page breaks */
            .card-header {
                background-color: #f8f9fa !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Preserve colors for badges */
            .badge,
            .bg-primary,
            .bg-success-subtle,
            .bg-info-subtle {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }

            /* Add footer */
            @page {
                margin: 2cm;
                @bottom-right {
                    content: "تاريخ الطباعة: " counter(page);
                }
            }

            /* Hide action buttons in table */
            table td:last-child {
                display: none;
            }
            table th:last-child {
                display: none;
            }
        }
    </style>

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
                <p>هل أنت متأكد من رغبتك في إرسال طلب إلغاء لهذا المتبرع؟</p>
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
</script>