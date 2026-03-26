@extends('layouts.app')

@section('title', 'تعديل شنطة رمضان')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="d-flex align-items-center">
            <a href="{{ route('ramadan-bags.index') }}" class="btn btn-outline-secondary btn-sm me-3">
                <i class="bi bi-arrow-right"></i> عودة
            </a>
            <div>
                <h3 class="fw-bold mb-0">تعديل حالة شنطة</h3>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <form method="POST" action="{{ route('ramadan-bags.update', $ramadan_bag) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">اسم المستفيد <span class="text-danger">*</span></label>
                            <input type="text" name="beneficiary_name" class="form-control" required value="{{ old('beneficiary_name', $ramadan_bag->beneficiary_name) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">الرقم القومي</label>
                            <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $ramadan_bag->national_id) }}">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label text-muted small">الحالة الاجتماعية</label>
                            <input type="text" name="marital_status" class="form-control" value="{{ old('marital_status', $ramadan_bag->marital_status) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">اسم الزوج / الزوجة</label>
                            <input type="text" name="spouse_name" class="form-control" value="{{ old('spouse_name', $ramadan_bag->spouse_name) }}">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label text-muted small">رقم التليفون 1</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone', $ramadan_bag->phone) }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">رقم التليفون 2</label>
                            <input type="text" name="phone_2" class="form-control" value="{{ old('phone_2', $ramadan_bag->phone_2) }}">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label text-muted small">عدد الافراد</label>
                            <input type="number" name="family_members" class="form-control" value="{{ old('family_members', $ramadan_bag->family_members) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">المنطقة</label>
                            <input type="text" name="region" class="form-control" value="{{ old('region', $ramadan_bag->region) }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small">اجمالي عدد الشنط</label>
                            <input type="number" name="bags_count" class="form-control" value="{{ old('bags_count', $ramadan_bag->bags_count) }}" min="1">
                        </div>
                        
                        <div class="col-12">
                            <label class="form-label text-muted small">ظروف الحالة</label>
                            <textarea name="case_conditions" class="form-control" rows="2">{{ old('case_conditions', $ramadan_bag->case_conditions) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">العنوان التفصيلي</label>
                            <input type="text" name="address" class="form-control" value="{{ old('address', $ramadan_bag->address) }}">
                        </div>

                        <div class="col-12">
                            <label class="form-label text-muted small">ملاحظات (أو محتويات الشنطة)</label>
                            <textarea name="notes" class="form-control" rows="2">{{ old('notes', $ramadan_bag->notes) }}</textarea>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted small">الحالة <span class="text-danger">*</span></label>
                            <select name="status" class="form-select" required>
                                <option value="جديد" {{ old('status', $ramadan_bag->status) == 'جديد' ? 'selected' : '' }}>جديد</option>
                                <option value="مقبول" {{ old('status', $ramadan_bag->status) == 'مقبول' ? 'selected' : '' }}>مقبول</option>
                                <option value="مرفوض" {{ old('status', $ramadan_bag->status) == 'مرفوض' ? 'selected' : '' }}>مرفوض</option>
                                <option value="تم التسليم" {{ old('status', $ramadan_bag->status) == 'تم التسليم' ? 'selected' : '' }}>تم التسليم</option>
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted small">ارتباط بحملة موسمية</label>
                            <select name="campaign_id" class="form-select">
                                <option value="">-- بدون حملة --</option>
                                @foreach($campaigns as $camp)
                                    <option value="{{ $camp->id }}" {{ old('campaign_id', $ramadan_bag->campaign_id) == $camp->id ? 'selected' : '' }}>{{ $camp->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label text-muted small">ارتباط بمشروع إضافي</label>
                            <select name="project_id" class="form-select">
                                <option value="">-- بدون مشروع --</option>
                                @foreach($projects as $proj)
                                    <option value="{{ $proj->id }}" {{ old('project_id', $ramadan_bag->project_id) == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-12 mt-4 text-end">
                            <button type="submit" class="btn btn-primary px-4">تحديث البيانات</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
