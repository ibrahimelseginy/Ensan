@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}" class="text-decoration-none">حركات المخزون</a></li>
                    <li class="breadcrumb-item active" aria-current="page">تعديل حركة #{{ $t->id }}</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">تعديل حركة مخزون</h2>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('inventory-transactions.update', $t) }}" id="invForm">
                @csrf
                @method('PUT')
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-medium">الصنف</label>
                        <select class="form-select bg-body-secondary" disabled>
                            @foreach($items as $i)
                                <option value="{{ $i->id }}" @selected($t->item_id==$i->id)>{{ $i->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">لا يمكن تعديل الصنف بعد الإنشاء.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">المخزن</label>
                        <select class="form-select bg-body-secondary" disabled>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}" @selected($t->warehouse_id==$w->id)>{{ $w->name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text text-muted">لا يمكن تعديل المخزن بعد الإنشاء.</div>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">نوع الحركة</label>
                        <select name="type" class="form-select" id="invType">
                            <option value="in" @selected($t->type==='in')>إدخال (In)</option>
                            <option value="out" @selected($t->type==='out')>صرف (Out)</option>
                            <option value="transfer" @selected($t->type==='transfer')>تحويل (Transfer)</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">الكمية</label>
                        <input type="number" step="0.01" name="quantity" class="form-control" value="{{ $t->quantity }}" required>
                    </div>
                    
                    <!-- Conditional Fields -->
                    <div class="col-md-4 out-only" style="display: none;">
                        <label class="form-label fw-medium">المستفيد (للصرف فقط)</label>
                        <select name="beneficiary_id" class="form-select">
                            <option value="">— اختر المستفيد —</option>
                            @foreach($beneficiaries as $b)
                                <option value="{{ $b->id }}" @selected($t->beneficiary_id==$b->id)>{{ $b->full_name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-medium">المشروع</label>
                        <select name="project_id" class="form-select">
                            <option value="">— اختر المشروع —</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" @selected($t->project_id==$p->id)>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-medium">الحملة</label>
                        <select name="campaign_id" class="form-select">
                            <option value="">— اختر الحملة —</option>
                            @foreach($campaigns as $c)
                                <option value="{{ $c->id }}" @selected($t->campaign_id==$c->id)>{{ $c->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-medium">مرجع / ملاحظات</label>
                        <input type="text" name="reference" class="form-control" value="{{ $t->reference }}">
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-save me-1"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('inventory-transactions.show', $t) }}" class="btn btn-secondary-subtle px-4">إلغاء</a>
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
        
        // Handle Out fields
        document.querySelectorAll('.out-only').forEach(el => {
            el.style.display = (type === 'out') ? 'block' : 'none';
        });
    }

    // Initial run
    toggleFields();
    
    // Event listener
    typeSelect.addEventListener('change', toggleFields);
});
</script>
@endsection
