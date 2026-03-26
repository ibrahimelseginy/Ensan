@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('warehouses.index') }}"
                                class="text-decoration-none">المخازن</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $warehouse->name }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0">
                    <i class="bi bi-box-seam text-primary"></i>
                    {{ $warehouse->name }}
                </h4>
            </div>
            <div class="btn-group">
                <a href="{{ route('warehouses.edit', $warehouse) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> تعديل
                </a>
                <form action="{{ route('warehouses.destroy', $warehouse) }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟');">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- Sidebar / Info -->
            <div class="col-lg-4">
                <!-- Warehouse Info Card -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">بيانات المخزن</h5>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-primary-subtle rounded-circle p-2 me-3 text-primary">
                                <i class="bi bi-geo-alt fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">الموقع</div>
                                <div class="fw-medium">{{ $warehouse->location ?? 'غير محدد' }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success-subtle rounded-circle p-2 me-3 text-success">
                                <i class="bi bi-box-seam fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">إجمالي الأصناف المتوفرة</div>
                                <div class="fw-medium">{{ $stock->count() }} صنف</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-info-subtle rounded-circle p-2 me-3 text-info">
                                <i class="bi bi-clock-history fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">آخر نشاط</div>
                                <div class="fw-medium">
                                    {{ $recent_transactions->first() ? $recent_transactions->first()->created_at->diffForHumans() : 'لا يوجد' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="card-title fw-bold mb-0">آخر التحركات</h5>
                    </div>
                    <div class="card-body px-0">
                        @forelse($recent_transactions as $transaction)
                            <div class="px-4 py-3 border-bottom border-secondary-subtle hover-bg-subtle">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <span
                                        class="badge bg-{{ $transaction->type === 'in' ? 'success' : 'danger' }}-subtle text-{{ $transaction->type === 'in' ? 'success' : 'danger' }} rounded-pill">
                                        {{ $transaction->type === 'in' ? 'وارد' : 'صادر' }}
                                    </span>
                                    <small class="text-muted">{{ optional($transaction->created_at)->format('Y-m-d H:i') }}</small>
                                </div>
                                <div class="fw-medium mb-1">{{ $transaction->item->name ?? 'صنف محذوف' }}</div>
                                <div class="small text-muted d-flex justify-content-between">
                                    <span>الكمية: {{ $transaction->quantity }}</span>
                                    @if($transaction->reference)
                                        <span class="text-truncate" style="max-width: 100px;"
                                            title="{{ $transaction->reference }}">#{{ $transaction->reference }}</span>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-4 text-muted">
                                <i class="bi bi-clipboard-x fs-1 d-block mb-2"></i>
                                لا توجد تحركات حديثة
                            </div>
                        @endforelse

                        @if($recent_transactions->count() > 0)
                            <div class="p-3 text-center">
                                <a href="{{ route('inventory-transactions.index') }}?warehouse_id={{ $warehouse->id }}"
                                    class="btn btn-link text-decoration-none small">عرض كل التحركات</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Main Content / Stock -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div
                        class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h5 class="card-title fw-bold mb-0">المخزون الحالي</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="ps-4">الصنف</th>
                                        <th>SKU</th>
                                        <th>الوحدة</th>
                                        <th>الكمية المتوفرة</th>
                                        <th class="text-end pe-4">القيمة التقديرية</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($stock as $item_stock)
                                        <tr>
                                            <td class="ps-4">
                                                <div class="fw-bold">{{ $item_stock->item->name ?? 'غير معروف' }}</div>
                                            </td>
                                            <td><span
                                                    class="font-monospace small text-muted">{{ $item_stock->item->sku ?? '-' }}</span>
                                            </td>
                                            <td>{{ $item_stock->item->unit ?? '-' }}</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="fw-bold fs-5 me-2">{{ $item_stock->current_stock }}</span>
                                                </div>
                                            </td>
                                            <td class="text-end pe-4">
                                                @if(isset($item_stock->item->estimated_value) && $item_stock->item->estimated_value > 0)
                                                    {{ number_format($item_stock->current_stock * $item_stock->item->estimated_value, 2) }}
                                                @else
                                                    <span class="text-muted small">-</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="bi bi-box2 fs-1 d-block mb-3"></i>
                                                    <h5>المخزن فارغ</h5>
                                                    <p class="mb-0">لا توجد أصناف متوفرة في هذا المخزن حالياً.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-subtle:hover {
            background-color: var(--bs-tertiary-bg);
        }
    </style>
@endsection