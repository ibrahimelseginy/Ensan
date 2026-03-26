@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1">
                    <li class="breadcrumb-item"><a href="{{ route('inventory-transactions.index') }}" class="text-decoration-none">حركات المخزون</a></li>
                    <li class="breadcrumb-item active" aria-current="page">تحويل بين المخازن</li>
                </ol>
            </nav>
            <h2 class="fw-bold text-dark mb-0">تحويل بين المخازن</h2>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('inventory-transactions.store-transfer') }}">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-medium">من المخزن (المصدر) <span class="text-danger">*</span></label>
                        <select name="from_warehouse_id" class="form-select" required>
                            <option value="" disabled selected>اختر المخزن...</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">إلى المخزن (المستلم) <span class="text-danger">*</span></label>
                        <select name="to_warehouse_id" class="form-select" required>
                            <option value="" disabled selected>اختر المخزن...</option>
                            @foreach($warehouses as $w)
                                <option value="{{ $w->id }}">{{ $w->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-medium">الصنف <span class="text-danger">*</span></label>
                        <select name="item_id" class="form-select" required>
                            <option value="" disabled selected>اختر الصنف...</option>
                            @foreach($items as $i)
                                <option value="{{ $i->id }}">{{ $i->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-medium">الكمية <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="quantity" class="form-control" required placeholder="0.00">
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
                    
                    <div class="col-md-4">
                        <label class="form-label fw-medium">التاريخ</label>
                        <input type="date" name="date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>

                    <div class="col-12">
                        <label class="form-label fw-medium">ملاحظات التحويل</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="سبب التحويل أو أي ملاحظات أخرى"></textarea>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="bi bi-arrow-left-right me-1"></i> إتمام التحويل
                    </button>
                    <a href="{{ route('inventory-transactions.index') }}" class="btn btn-light px-4">إلغاء</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
