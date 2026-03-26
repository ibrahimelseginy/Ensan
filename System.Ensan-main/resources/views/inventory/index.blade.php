@extends('layouts.app')

@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 50%, #b45309 100%);">
    <div class="hero-content">
        <div class="hero-greeting">اللوجستيات 📦</div>
        <h1 class="hero-title">حركات المخزون</h1>
        <p class="hero-subtitle">إدارة ومتابعة حركات الوارد والصادر والتحويلات</p>
        <div class="hero-actions d-flex gap-2 flex-wrap">
            <a href="{{ route('inventory-transactions.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة حركة
            </a>
            <a href="{{ route('inventory-transactions.create-transfer') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-arrow-left-right me-1"></i> تحويل
            </a>
            <a href="{{ route('inventory-transactions.create-reconcile') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-clipboard-check me-1"></i> جرد/تسوية
            </a>
        </div>
    </div>
    <i class="bi bi-arrow-left-right hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 animate-slide-up animate-delay-1">
        <div class="summary-panel h-100">
            <h5 class="summary-title"><i class="bi bi-calendar-check text-warning"></i> ملخص اليوم</h5>
            <div class="text-muted small mb-3">{{ $stats['date'] }}</div>
            <div class="summary-item">
                <span class="summary-label">عدد الحركات</span>
                <span class="summary-value">{{ $stats['trips_today'] }}</span>
            </div>
            <div class="summary-item">
                <span class="summary-label">القيمة المقدرة</span>
                <span class="summary-value text-success">{{ number_format($stats['value_today'], 0) }}</span>
            </div>
        </div>
    </div>
    <div class="col-md-8 animate-slide-up animate-delay-2">
        <div class="summary-panel h-100">
            <h5 class="summary-title"><i class="bi bi-person-badge text-primary"></i> نشاط المندوبين (تحصيل)</h5>
            @if(count($stats['delegateDaily']) > 0)
                <div class="row g-3">
                    @foreach($stats['delegateDaily'] as $r)
                        <div class="col-sm-6 col-lg-4">
                            <div class="p-3 bg-body-tertiary rounded-3">
                                <div class="fw-bold mb-2 text-primary">
                                    {{ $stats['delegatesMap'][$r->delegate_id]->name ?? 'غير معروف' }}
                                </div>
                                <div class="small mb-1">
                                    <span class="text-muted">اليوم:</span>
                                    <span class="fw-medium">{{ (int) $r->count }} عملية</span>
                                </div>
                                <div class="small mb-2">
                                    <span class="fw-bold text-success">{{ number_format((float) $r->total, 0) }}</span>
                                </div>
                                @php($mm = $stats['delegateMonthly']->firstWhere('delegate_id', $r->delegate_id))
                                <div class="border-top pt-2 mt-2">
                                    <div class="small text-muted mb-1">الشهر الحالي</div>
                                    <div class="d-flex justify-content-between small">
                                        <span>{{ (int) ($mm->count ?? 0) }} عملية</span>
                                        <span class="fw-medium">{{ number_format((float) ($mm->total ?? 0), 0) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-3">لا يوجد نشاط للمندوبين اليوم</div>
            @endif
        </div>
    </div>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-3">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية النتائج</h5>
    </div>
    <form action="{{ route('inventory-transactions.index') }}" method="GET" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">نوع الحركة</label>
            <select name="type" class="form-select">
                <option value="">الكل</option>
                <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>وارد (In)</option>
                <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>صادر (Out)</option>
                <option value="transfer" {{ request('type') == 'transfer' ? 'selected' : '' }}>نقل (Transfer)</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">المخزن</label>
            <select name="warehouse_id" class="form-select">
                <option value="">الكل</option>
                @foreach($warehouses as $id => $name)
                    <option value="{{ $id }}" {{ request('warehouse_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-bold small text-uppercase text-muted">الصنف</label>
            <select name="item_id" class="form-select">
                <option value="">الكل</option>
                @foreach($items as $id => $name)
                    <option value="{{ $id }}" {{ request('item_id') == $id ? 'selected' : '' }}>{{ $name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <button type="submit" class="btn btn-primary w-100 fw-bold">
                <i class="bi bi-funnel me-1"></i> تصفية
            </button>
        </div>
    </form>
</div>

<!-- Transactions Table -->
<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th class="ps-4">#</th>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>الصنف</th>
                        <th>الكمية</th>
                        <th>المخزن</th>
                        <th>تفاصيل المصدر / الوجهة</th>
                        <th class="text-end pe-4">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $t)
                        <tr>
                            <td class="ps-4 text-muted small">{{ $t->id }}</td>
                            <td>{{ optional($t->created_at)->format('Y-m-d') }} <br> <small
                                    class="text-muted">{{ optional($t->created_at)->format('H:i') }}</small></td>
                            <td>
                                @if($t->type == 'in')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">وارد</span>
                                @elseif($t->type == 'out')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3">صادر</span>
                                @elseif($t->type == 'transfer')
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3">نقل (قديم)</span>
                                @elseif($t->type == 'transfer_in')
                                    <span class="badge bg-info-subtle text-info rounded-pill px-3">تحويل (وارد)</span>
                                @elseif($t->type == 'transfer_out')
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-3">تحويل (صادر)</span>
                                @elseif($t->type == 'stock_count_shortage')
                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3">عجز جرد</span>
                                @elseif($t->type == 'stock_count_increase')
                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">زيادة جرد</span>
                                @elseif($t->type == 'reconciliation')
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">تسوية</span>
                                @endif
                            </td>
                            <td>
                                <div class="fw-medium">{{ $t->item->name ?? '—' }}</div>
                                @if($t->item && $t->item->sku)
                                    <div class="small text-muted font-monospace">{{ $t->item->sku }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold">{{ $t->quantity }}</span>
                                <small class="text-muted">{{ $t->item->unit ?? '' }}</small>
                            </td>
                            <td>{{ $t->warehouse->name ?? '—' }}</td>
                            <td>
                                <div class="small">
                                    @if($t->sourceDonation)
                                        <div class="text-muted">تبرع من:</div>
                                        <div class="fw-medium">{{ $t->sourceDonation->donor->name ?? 'فاعل خير' }}</div>
                                        @if($t->sourceDonation->delegate)
                                            <div class="text-muted text-xs">بواسطة: {{ $t->sourceDonation->delegate->name }}</div>
                                        @endif
                                    @elseif($t->beneficiary)
                                        <div class="text-muted">إلى المستفيد:</div>
                                        <div class="fw-medium">{{ $t->beneficiary->full_name }}</div>
                                    @elseif($t->project)
                                        <div class="text-muted">مشروع:</div>
                                        <div class="fw-medium">{{ $t->project->name }}</div>
                                    @elseif($t->reference)
                                        <div class="text-muted">مرجع:</div>
                                        <div class="font-monospace">{{ $t->reference }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </div>
                            </td>
                             <td class="text-end pe-4">
                                <div class="d-flex justify-content-end gap-2 text-nowrap">
                                    @if(isset($t->pendingRequest) && $t->pendingRequest)
                                        <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 rounded-pill">
                                            <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                        </span>
                                    @else
                                        <div class="btn-group">
                                            <a href="{{ route('inventory-transactions.show', $t) }}"
                                                class="btn btn-sm btn-outline-secondary" title="عرض">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('inventory-transactions.edit', $t) }}"
                                                class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <button type="button" class="btn btn-sm btn-outline-danger" title="حذف"
                                                onclick="if(confirm('هل أنت متأكد من حذف هذه الحركة؟')) document.getElementById('delete-t-{{ $t->id }}').submit()">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
                                        <form id="delete-t-{{ $t->id }}" action="{{ route('inventory-transactions.destroy', $t) }}"
                                            method="POST" class="d-none">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-clipboard-x fs-1 d-block mb-3"></i>
                                    <h5>لا توجد حركات</h5>
                                    <p class="mb-0">لم يتم العثور على حركات مخزون تطابق خيارات البحث.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($transactions->hasPages())
        <div class="card-footer bg-transparent border-0 py-3">
            {{ $transactions->withQueryString()->links() }}
        </div>
    @endif
</div>

@endsection