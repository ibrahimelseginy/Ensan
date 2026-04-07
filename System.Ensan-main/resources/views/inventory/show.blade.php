@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}" class="text-decoration-none">حركات المخزون</a></li>
                    <li class="breadcrumb-item active" aria-current="page">حركة #{{ $t->id }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">تفاصيل الحركة</h2>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory-transactions.edit', $t) }}" class="btn btn-primary">
                <i class="bi bi-pencil me-1"></i> تعديل
            </a>
            <form action="{{ route('inventory-transactions.destroy', $t) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحركة؟');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-outline-danger">
                    <i class="bi bi-trash me-1"></i> حذف
                </button>
            </form>
        </div>
    </div>

    <div class="row g-4">
        <!-- Main Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-4">
                        <div class="rounded-circle p-3 me-3 d-flex align-items-center justify-content-center" 
                             style="width: 64px; height: 64px; background-color: {{ $t->type == 'in' ? 'var(--bs-success-bg-subtle)' : ($t->type == 'out' ? 'var(--bs-danger-bg-subtle)' : 'var(--bs-info-bg-subtle)') }}; color: {{ $t->type == 'in' ? 'var(--bs-success)' : ($t->type == 'out' ? 'var(--bs-danger)' : 'var(--bs-info)') }}">
                            <i class="bi {{ $t->type == 'in' ? 'bi-arrow-down' : ($t->type == 'out' ? 'bi-arrow-up' : 'bi-arrow-left-right') }} fs-2"></i>
                        </div>
                        <div>
                            <h4 class="fw-bold mb-1">
                                {{ $t->type == 'in' ? 'حركة وارد' : ($t->type == 'out' ? 'حركة صادر' : 'نقل مخزون') }}
                            </h4>
                            <div class="text-muted">{{ optional($t->created_at)->format('Y-m-d H:i') }}</div>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">الصنف</div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-box me-2 text-secondary"></i>
                                <a href="{{ route('items.show', $t->item_id) }}" class="text-decoration-none fw-bold text-dark fs-5">
                                    {{ $t->item->name ?? 'صنف محذوف' }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">المخزن</div>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-building me-2 text-secondary"></i>
                                <a href="{{ route('warehouses.show', $t->warehouse_id) }}" class="text-decoration-none fw-medium text-dark">
                                    {{ $t->warehouse->name ?? 'مخزن محذوف' }}
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">الكمية</div>
                            <div class="fs-4 fw-bold">{{ $t->quantity }} <span class="fs-6 fw-normal text-muted">{{ $t->item->unit ?? '' }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">المرجع</div>
                            <div class="font-monospace">{{ $t->reference ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Related Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
                    <h5 class="card-title fw-bold mb-0">بيانات مرتبطة</h5>
                </div>
                <div class="card-body p-4">
                    <div class="list-group list-group-flush">
                        @if($t->sourceDonation)
                        <div class="list-group-item px-0 py-3">
                            <div class="small text-muted mb-1">مصدر التبرع</div>
                            <div class="d-flex align-items-center justify-content-between">
                                <div>
                                    <div class="fw-medium">{{ $t->sourceDonation->donor->name ?? 'فاعل خير' }}</div>
                                    @if($t->sourceDonation->delegate)
                                        <div class="small text-muted">بواسطة: {{ $t->sourceDonation->delegate->name }}</div>
                                    @endif
                                </div>
                                <span class="badge bg-body-tertiary text-body border">تبرع #{{ $t->sourceDonation->id }}</span>
                            </div>
                        </div>
                        @endif

                        @if($t->beneficiary)
                        <div class="list-group-item px-0 py-3">
                            <div class="small text-muted mb-1">المستفيد</div>
                            <div class="fw-medium">{{ $t->beneficiary->full_name }}</div>
                        </div>
                        @endif

                        @if($t->project)
                        <div class="list-group-item px-0 py-3">
                            <div class="small text-muted mb-1">المشروع</div>
                            <div class="fw-medium">{{ $t->project->name }}</div>
                        </div>
                        @endif

                        @if($t->campaign)
                        <div class="list-group-item px-0 py-3">
                            <div class="small text-muted mb-1">الحملة</div>
                            <div class="fw-medium">{{ $t->campaign->name }}</div>
                        </div>
                        @endif

                        @if(!$t->sourceDonation && !$t->beneficiary && !$t->project && !$t->campaign)
                        <div class="text-center text-muted py-3">
                            لا توجد بيانات مرتبطة إضافية
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4">
                    <h5 class="card-title fw-bold mb-3">إجراءات سريعة</h5>
                    <div class="d-grid gap-2">
                        <a href="{{ route('inventory-transactions.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-right me-2"></i> العودة للقائمة
                        </a>
                        <a href="{{ route('inventory-transactions.create') }}" class="btn btn-outline-primary">
                            <i class="bi bi-plus-lg me-2"></i> إضافة حركة جديدة
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


