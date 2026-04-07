@extends('layouts.app')
@section('content')
    <div class="row">
        <div class="col-md-4">
            {{-- Supplier Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-body">
                    <h5 class="card-title fw-bold mb-3">{{ $supplier->name }}</h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-2">
                        @if($supplier->phone)
                            <li><i class="bi bi-telephone text-muted me-2"></i> {{ $supplier->phone }}</li>
                        @endif
                        @if($supplier->website)
                            <li><i class="bi bi-globe text-muted me-2"></i> <a href="{{ $supplier->website }}" target="_blank">زيارة الموقع</a></li>
                        @endif
                        @if($supplier->address)
                            <li><i class="bi bi-geo-alt text-muted me-2"></i> {{ $supplier->address }}</li>
                        @endif
                    </ul>
                    <hr>
                    <div class="d-grid gap-2">
                        <a href="{{ route('suppliers.edit', $supplier) }}" class="btn btn-outline-secondary btn-sm">تعديل البيانات</a>
                    </div>
                </div>
            </div>

            {{-- Add Purchase Form --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-cart-plus me-1"></i> تسجيل مشتريات جديدة</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('suppliers.purchases.store', $supplier) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label small">اسم الصنف / الخدمة</label>
                            <input name="item_name" class="form-control" required placeholder="مثال: لابتوب ديل">
                        </div>
                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <label class="form-label small">الكمية</label>
                                <input type="number" name="quantity" class="form-control" value="1" min="1" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label small">سعر الوحدة</label>
                                <input type="number" name="original_price" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">نسبة الخصم (%)</label>
                            <input type="number" name="discount_percentage" class="form-control" value="0" min="0" max="100" step="0.1">
                        </div>
                        <div class="mb-3">
                            <label class="form-label small">تاريخ الشراء</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="mb-3">
                             <label class="form-label small">ملاحظات</label>
                             <textarea name="notes" class="form-control" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary w-100">إضافة</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">سجل المشتريات</h5>
                    <span class="badge bg-secondary">{{ $purchases->total() }} سجل</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-3">الصنف</th>
                                <th class="py-3 px-3">الكمية</th>
                                <th class="py-3 px-3">السعر (قبل/بعد)</th>
                                <th class="py-3 px-3">التاريخ</th>
                                <th class="py-3 px-3"></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($purchases as $p)
                                <tr>
                                    <td class="px-3 fw-medium">{{ $p->item_name }}</td>
                                    <td class="px-3">x{{ $p->quantity }}</td>
                                    <td class="px-3">
                                        @if($p->discount_percentage > 0)
                                            <div class="text-decoration-line-through text-muted small">{{ number_format($p->original_price * $p->quantity, 2) }}</div>
                                            <div class="text-success fw-bold">{{ number_format($p->final_price, 2) }}</div>
                                            <div class="badge bg-danger-subtle text-danger small">-{{ $p->discount_percentage }}%</div>
                                        @else
                                            <div class="fw-bold">{{ number_format($p->final_price, 2) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-3 text-muted small">{{ optional($p->purchase_date)->format('Y-m-d') }}</td>
                                    <td class="px-3 text-end">
                                        <form action="{{ route('suppliers.purchases.destroy', [$supplier, $p]) }}" method="POST" onsubmit="return confirm('حذف؟')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">لا توجد مشتريات مسجلة لهذا المورد</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($purchases->hasPages())
                    <div class="card-footer bg-transparent border-0 py-3">
                        {{ $purchases->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection


