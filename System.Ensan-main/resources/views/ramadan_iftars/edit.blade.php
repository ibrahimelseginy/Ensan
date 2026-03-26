@extends('layouts.app')

@section('title', 'تعديل إفطار رمضان')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center">
            <a href="{{ route('ramadan-iftars.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="bi bi-arrow-right"></i> عودة
            </a>
            <div>
                <h3 class="fw-bold mb-0">تعديل حالة إفطار</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-9 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('ramadan-iftars.update', $ramadan_iftar) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">اسم المستفيد <span class="text-danger">*</span></label>
                            <input type="text" name="beneficiary_name" class="form-control" required value="{{ old('beneficiary_name', $ramadan_iftar->beneficiary_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">رقم المستفيد</label>
                            <input type="text" name="nickname" class="form-control" value="{{ old('nickname', $ramadan_iftar->nickname) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">المنطقة</label>
                            <input type="text" name="region" class="form-control" value="{{ old('region', $ramadan_iftar->region) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">رقم الهوية</label>
                            <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $ramadan_iftar->national_id) }}">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label text-muted small">عدد الوجبات <span class="text-danger">*</span></label>
                            <input type="number" name="meals_count" class="form-control" min="1" required value="{{ old('meals_count', $ramadan_iftar->meals_count) }}">
                        </div>

                        <div class="col-12 mt-3">
                            <hr>
                            <h6 class="fw-bold mb-3"><i class="bi bi-truck text-primary me-2"></i>بيانات التوصيل (الأدلة والتكاتك)</h6>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">اسم الدليل</label>
                            <input type="text" name="guide_name" class="form-control" value="{{ old('guide_name', $ramadan_iftar->guide_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">هاتف الدليل</label>
                            <input type="text" name="guide_phone" class="form-control" value="{{ old('guide_phone', $ramadan_iftar->guide_phone) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small">هاتف الدليل 2</label>
                            <input type="text" name="guide_phone_2" class="form-control" value="{{ old('guide_phone_2', $ramadan_iftar->guide_phone_2) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">الوسيلة</label>
                            <input type="text" name="delivery_method" class="form-control" value="{{ old('delivery_method', $ramadan_iftar->delivery_method) }}" placeholder="توكتوك، سيارة...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">تكلفة التوصيل</label>
                            <input type="number" step="0.01" min="0" name="delivery_cost" class="form-control" value="{{ old('delivery_cost', $ramadan_iftar->delivery_cost) }}">
                        </div>

                        <div class="col-12 mt-3">
                            <hr>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">العنوان</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $ramadan_iftar->address) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">ملاحظات إضافية</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $ramadan_iftar->notes) }}</textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted small">ارتباط بحملة موسمية</label>
                            <select name="campaign_id" class="form-select">
                                <option value="">-- بدون حملة --</option>
                                @foreach($campaigns as $camp)
                                    <option value="{{ $camp->id }}" {{ old('campaign_id', $ramadan_iftar->campaign_id) == $camp->id ? 'selected' : '' }}>{{ $camp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted small">ارتباط بمشروع إضافي</label>
                            <select name="project_id" class="form-select">
                                <option value="">-- بدون مشروع --</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id', $ramadan_iftar->project_id) == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-success px-4">تحديث البيانات</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
