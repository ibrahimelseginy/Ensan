@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}" class="text-decoration-none">حركات المخزون</a></li>
                    <li class="breadcrumb-item active" aria-current="page">إضافة حركة جديدة</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">إضافة حركة مخزون</h2>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('inventory-transactions.store') }}" id="invForm">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">الصنف <span class="text-danger">*</span></label>
                        <select name="item_id" id="invItemSelect" class="form-select" required>
                            <option value="" disabled selected>اختر الصنف...</option>
                            <option value="new_item" class="text-primary fw-bold">✨ + كتابة صنف جديد يدوياً (يُضاف تلقائياً للأصناف)...</option>
                            @foreach($items as $i)
                                <option value="{{ $i->id }}">{{ $i->name }} ({{ $i->unit ?? 'وحدة' }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4" id="newItemNameWrapper" style="display: none;">
                        <label class="form-label fw-medium text-primary">اسم الصنف الجديد <span class="text-danger">*</span></label>
                        <input type="text" name="new_item_name" id="newItemNameInput" class="form-control border-primary" placeholder="اكتب اسم الصنف الجديد هنا...">
                        <div class="form-text text-muted small">سيتم إنشاء هذا الصنف تلقائياً وإدراجه في قائمة الأصناف.</div>
                    </div>
                    <div class="col-md-2" id="newItemUnitWrapper" style="display: none;">
                        <label class="form-label fw-medium text-primary">وحدة القياس</label>
                        <input type="text" name="new_item_unit" class="form-control" placeholder="كيلو، كرتونة، علبة" value="وحدة">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">المخزن <span class="text-danger">*</span></label>
                        <select name="warehouse_id" class="form-select" required>
                            <option value="" disabled selected>اختر المخزن...</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">نوع الحركة <span class="text-danger">*</span></label>
                        <select name="type" class="form-select" required id="invType">
                            <option value="in">إدخال (In)</option>
                            <option value="out">صرف (Out)</option>
                            <option value="transfer">تحويل (Transfer)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">الكمية <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" class="form-control" required placeholder="0.00">
                    </div>
                    
                    <!-- Conditional Fields -->
                    <div class="col-md-8 in-only">
                        <label class="form-label fw-medium">مصدر التبرع (للوارد فقط)</label>
                        <select name="source_donation_id" class="form-select">
                            <option value="">— لا يوجد / غير مرتبط بتبرع —</option>
                            @foreach($donations as $d)
                                <option value="{{ $d->id }}">#{{ $d->id }} - {{ $d->donor?->name ?? 'غير معروف' }} ({{ $d->amount ?? $d->estimated_value ?? 0 }})</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">يمكنك ربط حركة الوارد بتبرع عيني موجود.</div>
                    </div>

                    <div class="col-md-4 out-only" style="display: none;">
                        <label class="form-label fw-medium">المستفيد (للصرف فقط)</label>
                        <select name="beneficiary_id" class="form-select">
                            <option value="">— اختر المستفيد —</option>
                            @foreach($beneficiaries as $b)
                                <option value="{{ $b->id }}">{{ $b->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">المشروع</label>
                        <select name="project_id" class="form-select">
                            <option value="">— اختر المشروع —</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}">{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">الحملة</label>
                        <select name="campaign_id" class="form-select">
                            <option value="">— اختر الحملة —</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}">{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">مرجع / ملاحظات</label>
                        <input type="text" name="reference" class="form-control" placeholder="رقم مرجعي أو ملاحظات إضافية">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> حفظ الحركة
                    </button>
                    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-light px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const typeSelect = document.getElementById('invType');
    
    function toggleFields() {
        const type = typeSelect.value;
        
        // Handle In fields
        document.querySelectorAll('.in-only').forEach(el => {
            el.style.display = (type === 'in') ? 'block' : 'none';
        });
        
        // Handle Out fields
        document.querySelectorAll('.out-only').forEach(el => {
            el.style.display = (type === 'out') ? 'block' : 'none';
        });
    }

    // Initial run
    toggleFields();
    
    // Event listener
    typeSelect.addEventListener('change', toggleFields);

    const itemSelect = document.getElementById('invItemSelect');
    const newItemNameWrapper = document.getElementById('newItemNameWrapper');
    const newItemUnitWrapper = document.getElementById('newItemUnitWrapper');
    const newItemNameInput = document.getElementById('newItemNameInput');

    function toggleNewItemFields() {
        if (itemSelect.value === 'new_item') {
            newItemNameWrapper.style.display = 'block';
            newItemUnitWrapper.style.display = 'block';
            newItemNameInput.setAttribute('required', 'required');
            newItemNameInput.focus();
        } else {
            newItemNameWrapper.style.display = 'none';
            newItemUnitWrapper.style.display = 'none';
            newItemNameInput.removeAttribute('required');
        }
    }

    itemSelect.addEventListener('change', toggleNewItemFields);
    toggleNewItemFields();
});
</script>
@endsection


