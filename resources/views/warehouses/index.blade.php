@extends('layouts.app')

@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 50%, #6d28d9 100%);">
    <div class="hero-content">
        <div class="hero-greeting">اللوجستيات 📦</div>
        <h1 class="hero-title">إدارة المخازن</h1>
        <p class="hero-subtitle">إدارة ومتابعة المخازن وحركات المخزون</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('warehouses.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة مخزن جديد
            </a>
            <a href="{{ route('inventory-transactions.index') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-arrow-left-right me-1"></i> حركات المخزون
            </a>
        </div>
    </div>
    <i class="bi bi-building hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-4 animate-slide-up animate-delay-1">
        <a href="{{ route('warehouses.index') }}" class="stat-card stat-purple text-decoration-none bg-hover">
            <div class="stat-icon"><i class="bi bi-houses-fill"></i></div>
            <div class="stat-label">إجمالي المخازن</div>
            <div class="stat-value">{{ number_format($stats['total']) }}</div>
            <i class="bi bi-houses-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-md-4 animate-slide-up animate-delay-2">
        <a href="{{ route('items.index') }}" class="stat-card stat-success text-decoration-none bg-hover">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-label">الأصناف النشطة</div>
            <div class="stat-value">{{ number_format($stats['active_items']) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> نشط</div>
            <i class="bi bi-box-seam-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-md-4 animate-slide-up animate-delay-3">
        <a href="{{ route('inventory-transactions.index') }}" class="stat-card stat-info text-decoration-none bg-hover">
            <div class="stat-icon"><i class="bi bi-arrow-left-right"></i></div>
            <div class="stat-label">إجمالي الحركات</div>
            <div class="stat-value">{{ number_format($stats['total_transactions']) }}</div>
            <i class="bi bi-arrow-left-right stat-bg-icon"></i>
        </a>
    </div>
</div>

    <!-- Warehouses Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr>
                            <th class="border-0 p-3 ps-4">المخزن</th>
                            <th class="border-0 p-3">الموقع</th>
                            <th class="border-0 p-3 text-center">عدد الحركات</th>
                            <th class="border-0 p-3 text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($warehouses as $w)
                            <tr onclick="window.location='{{ route('warehouses.show', $w) }}'" style="cursor: pointer;">
                                <td class="p-3 ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-secondary-subtle text-secondary rounded-circle d-flex align-items-center justify-content-center me-3"
                                            style="width: 40px; height: 40px;">
                                            <i class="bi bi-building"></i>
                                        </div>
                                        <div class="fw-bold text-body">{{ $w->name }}</div>
                                    </div>
                                </td>
                                <td class="p-3 text-muted">
                                    @if($w->location)
                                        <i class="bi bi-geo-alt me-1"></i> {{ $w->location }}
                                    @else
                                        <span class="text-muted small">غير محدد</span>
                                    @endif
                                </td>
                                <td class="p-3 text-center">
                                    <span class="badge bg-secondary-subtle text-secondary rounded-pill px-3">
                                        {{ $w->transactions_count }}
                                    </span>
                                </td>
                                <td class="p-3 text-end pe-4">
                                    <div class="d-flex justify-content-end gap-2 text-nowrap" onclick="event.stopPropagation()">
                                        @if($w->pendingRequest)
                                            <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-1 rounded-pill small">
                                                <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                                            </span>
                                        @else
                                            <div class="btn-group">
                                                <a href="{{ route('warehouses.show', $w) }}" class="btn btn-sm btn-outline-primary"
                                                    title="عرض التفاصيل">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('warehouses.edit', $w) }}" class="btn btn-sm btn-outline-secondary"
                                                    title="تعديل">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <form action="{{ route('warehouses.destroy', $w) }}" method="POST" class="d-inline"
                                                    onsubmit="return confirm('هل أنت متأكد من حذف هذا المخزن؟');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center p-5 text-muted">
                                    <i class="bi bi-box-seam display-1 d-block mb-3 opacity-25"></i>
                                    لا توجد مخازن مضافة حتى الآن
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($warehouses->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $warehouses->links() }}
            </div>
        @endif
    </div>

@endsection

