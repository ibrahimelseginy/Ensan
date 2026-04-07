@extends('layouts.app')

@section('content')

    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-box-seam text-primary me-2"></i>الأصناف
            </h4>
        </div>
        <div>
            <a href="{{ route('items.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة صنف جديد
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card kpi-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-primary">
                        <i class="bi bi-box-seam"></i>
                    </div>
                    <div>
                        <div class="kpi-label">إجمالي الأصناف</div>
                        <div class="kpi-value">{{ $stats['total'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card kpi-card border-0 shadow-sm p-3 h-100">
                <div class="d-flex align-items-center gap-3">
                    <div class="avatar avatar-success">
                        <i class="bi bi-currency-dollar"></i>
                    </div>
                    <div>
                        <div class="kpi-label">أصناف مقيمة</div>
                        <div class="kpi-value">{{ $stats['with_value'] }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters & Search -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-3">
            <form action="{{ route('items.index') }}" method="GET" class="row g-3 align-items-center">
                <div class="col-md-6 col-lg-4">
                    <div class="input-group">
                        <span class="input-group-text bg-transparent border-end-0 text-muted">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" name="search" class="form-control border-start-0 ps-0"
                            placeholder="بحث باسم الصنف أو SKU..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-secondary w-100">تصفية</button>
                </div>
                @if(request()->has('search'))
                    <div class="col-md-2">
                        <a href="{{ route('items.index') }}" class="btn btn-link text-decoration-none text-muted">
                            <i class="bi bi-x-circle me-1"></i> مسح
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr>
                            <th class="ps-4 py-3">اسم الصنف / SKU</th>
                            <th>الوحدة</th>
                            <th>القيمة التقديرية</th>
                            <th>عدد التحركات</th>
                            <th class="text-end pe-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $item)
                            <tr onclick="window.location='{{ route('items.show', $item) }}'" style="cursor: pointer;">
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                         <div class="bg-primary bg-opacity-10 text-primary rounded p-2 me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-box-seam fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-body">{{ $item->name }}</div>
                                            @if($item->sku)
                                                <div class="small text-muted font-monospace">{{ $item->sku }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    @if($item->unit)
                                        <span
                                            class="badge bg-secondary-subtle text-secondary rounded-pill px-3">{{ $item->unit }}</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->estimated_value)
                                        <span class="fw-medium">{{ number_format($item->estimated_value, 2) }}</span>
                                        <small class="text-muted ms-1">EGP</small>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info-subtle text-info rounded-pill">
                                        {{ $item->transactions_count }} حركة
                                    </span>
                                </td>
                                 <td class="text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2 text-nowrap" onclick="event.stopPropagation()">
                                        @if($item->pendingRequest)
                                            <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 rounded-pill">
                                                <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                            </span>
                                        @else
                                            <div class="btn-group">
                                                <a href="{{ route('items.edit', $item) }}" class="btn btn-sm btn-outline-secondary"
                                                    title="تعديل">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <button type="button" class="btn btn-sm btn-outline-danger" title="حذف"
                                                    onclick="if(confirm('هل أنت متأكد من حذف هذا الصنف؟')) document.getElementById('delete-item-{{ $item->id }}').submit()">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                            <form id="delete-item-{{ $item->id }}" action="{{ route('items.destroy', $item) }}"
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
                                <td colspan="5" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="bi bi-search fs-1 d-block mb-3"></i>
                                        <h5>لا توجد نتائج</h5>
                                        <p class="mb-0">لم يتم العثور على أصناف تطابق بحثك.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($items->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $items->withQueryString()->links() }}
            </div>
        @endif
    </div>

@endsection

