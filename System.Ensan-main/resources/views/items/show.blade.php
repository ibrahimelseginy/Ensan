@extends('layouts.app')

@section('content')
    <div class="container-fluid">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-1">
                        <li class="breadcrumb-item"><a href="{{ route('items.index') }}"
                                class="text-decoration-none">الأصناف</a></li>
                        <li class="breadcrumb-item active" aria-current="page">{{ $item->name }}</li>
                    </ol>
                </nav>
                <h4 class="mb-0">
                    <i class="bi bi-box text-primary"></i>
                    {{ $item->name }}
                </h4>
            </div>
            <div class="btn-group">
                <a href="{{ route('items.edit', $item) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> تعديل
                </a>
                <form action="{{ route('items.destroy', $item) }}" method="POST"
                    onsubmit="return confirm('هل أنت متأكد من حذف هذا الصنف؟');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-outline-danger">
                        <i class="bi bi-trash me-1"></i> حذف
                    </button>
                </form>
            </div>
        </div>

        <div class="row g-4">
            <!-- Item Info -->
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h5 class="card-title fw-bold mb-4">بيانات الصنف</h5>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-secondary-subtle rounded-circle p-2 me-3 text-secondary">
                                <i class="bi bi-upc-scan fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">SKU</div>
                                <div class="fw-medium font-monospace">{{ $item->sku ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-secondary-subtle rounded-circle p-2 me-3 text-secondary">
                                <i class="bi bi-rulers fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">الوحدة</div>
                                <div class="fw-medium">{{ $item->unit ?? '—' }}</div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-success-subtle rounded-circle p-2 me-3 text-success">
                                <i class="bi bi-currency-dollar fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">القيمة التقديرية</div>
                                <div class="fw-medium">
                                    @if($item->estimated_value)
                                        {{ number_format($item->estimated_value, 2) }} EGP
                                    @else
                                        —
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex align-items-center">
                            <div class="bg-primary-subtle rounded-circle p-2 me-3 text-primary">
                                <i class="bi bi-layers fs-5"></i>
                            </div>
                            <div>
                                <div class="text-muted small">إجمالي المخزون</div>
                                <div class="fw-bold fs-5">{{ $stock_by_warehouse->sum('current_stock') }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Stock Distribution -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="card-title fw-bold mb-0">توزيع المخزون</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="list-group list-group-flush">
                            @forelse($stock_by_warehouse as $stock)
                                <div class="list-group-item px-4 py-3 d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary-subtle text-primary rounded-circle p-1 me-3 d-flex align-items-center justify-content-center"
                                            style="width: 32px; height: 32px;">
                                            <i class="bi bi-building small"></i>
                                        </div>
                                        <a href="{{ route('warehouses.show', $stock->warehouse_id) }}"
                                            class="text-decoration-none text-dark fw-medium">
                                            {{ $stock->warehouse->name }}
                                        </a>
                                    </div>
                                    <span class="badge bg-primary rounded-pill">{{ $stock->current_stock }}</span>
                                </div>
                            @empty
                                <div class="text-center py-4 text-muted">
                                    <i class="bi bi-box2 fs-1 d-block mb-2"></i>
                                    لا يوجد مخزون حالياً
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- History -->
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                        <h5 class="card-title fw-bold mb-0">سجل التحركات</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="ps-4">التاريخ</th>
                                        <th>النوع</th>
                                        <th>المخزن</th>
                                        <th>الكمية</th>
                                        <th>المرجع</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($recent_transactions as $transaction)
                                        <tr>
                                            <td class="ps-4 text-muted small">
                                                {{ optional($transaction->created_at)->format('Y-m-d H:i') }}</td>
                                            <td>
                                                <span
                                                    class="badge bg-{{ $transaction->type === 'in' ? 'success' : 'danger' }}-subtle text-{{ $transaction->type === 'in' ? 'success' : 'danger' }} rounded-pill">
                                                    {{ $transaction->type === 'in' ? 'وارد' : 'صادر' }}
                                                </span>
                                            </td>
                                            <td>
                                                @if($transaction->warehouse)
                                                    <a href="{{ route('warehouses.show', $transaction->warehouse_id) }}"
                                                        class="text-decoration-none">
                                                        {{ $transaction->warehouse->name }}
                                                    </a>
                                                @else
                                                    <span class="text-muted small">مخزن محذوف</span>
                                                @endif
                                            </td>
                                            <td class="fw-bold">{{ $transaction->quantity }}</td>
                                            <td class="text-muted small">
                                                @if($transaction->reference)
                                                    #{{ $transaction->reference }}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center py-5">
                                                <div class="text-muted">
                                                    <i class="bi bi-clock-history fs-1 d-block mb-3"></i>
                                                    <p class="mb-0">لا توجد تحركات سابقة لهذا الصنف.</p>
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
@endsection