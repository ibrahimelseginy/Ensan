@extends('layouts.app')
@section('content')

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-shop text-primary me-2"></i>الموردين
            </h4>
        </div>
        <div>
            <a href="{{ route('suppliers.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة مورد جديد
            </a>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
        <div class="card-body p-3">
            <form method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="q" class="form-control" placeholder="بحث باسم المورد أو الهاتف..."
                        value="{{ request('q') }}">
                </div>
                <div class="col-md-2">
                    <button class="btn btn-secondary w-100">بحث</button>
                </div>
            </form>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr class="text-secondary small text-uppercase">
                            <th class="py-3 px-4">اسم الصنف</th>
                            <th class="py-3 px-4">اسم التاجر</th>
                            <th class="py-3 px-4">رقم التليفون</th>
                            <th class="py-3 px-4">العنوان</th>
                            <th class="py-3 px-4">اسم المصدر</th>
                            <th class="py-3 px-4">ملاحظات</th>
                            <th class="py-3 px-4 text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suppliers as $supplier)
                            <tr>
                                <td class="px-4 fw-medium">{{ $supplier->name }}</td>
                                <td class="px-4">{{ $supplier->merchant_name ?? '—' }}</td>
                                <td class="px-4">{{ $supplier->phone ?? '—' }}</td>
                                <td class="px-4 text-muted small">{{ Str::limit($supplier->address, 30) ?? '—' }}</td>
                                <td class="px-4">{{ $supplier->source_name ?? '—' }}</td>
                                <td class="px-4 text-muted small">{{ Str::limit($supplier->notes, 30) ?? '—' }}</td>
                                 <td class="px-4 text-end">
                                    <div class="d-flex justify-content-end gap-2 text-nowrap">
                                        @if($supplier->pendingRequest)
                                            <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 rounded-pill">
                                                <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                            </span>
                                        @else
                                            <div class="btn-group">
                                                <a href="{{ route('suppliers.show', $supplier) }}"
                                                    class="btn btn-sm btn-outline-primary" title="عرض ومشتريات">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('suppliers.edit', $supplier) }}"
                                                    class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST"
                                                    class="d-inline" onsubmit="return confirm('حذف المورد؟')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-shop display-4 mb-3 d-block opacity-50"></i>
                                    لا يوجد موردين حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($suppliers->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>

@endsection