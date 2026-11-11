@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-plus-circle me-2"></i>إضافة خزينة جديدة</h2>
            <p class="text-muted">أضف خزينة جديدة لإدارة الأموال</p>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('treasuries.store') }}" method="POST">
                        @csrf

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم الخزينة <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code') }}" placeholder="مثال: MAIN-001" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 p-3 bg-light rounded-3 border mx-0">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">تخصص الخزينة / القسم التابع لها <span class="text-danger">*</span></label>
                                <select name="type" id="treasuryTypeSelect" class="form-select" required>
                                    <option value="main" {{ old('type', 'main') === 'main' ? 'selected' : '' }}>الخزينة العامة / الحسابات</option>
                                    <option value="reception" {{ old('type') === 'reception' ? 'selected' : '' }}>خزينة الاستقبال (تظهر في الاستقبال فقط)</option>
                                    <option value="guest_house" {{ old('type') === 'guest_house' ? 'selected' : '' }}>خزينة دار ضيافة (تظهر في دار الضيافة المحددة فقط)</option>
                                    <option value="project" {{ old('type') === 'project' ? 'selected' : '' }}>خزينة مشروع (تظهر في المشروع المحدد فقط)</option>
                                    <option value="campaign" {{ old('type') === 'campaign' ? 'selected' : '' }}>خزينة حملة (تظهر في الحملة المحددة فقط)</option>
                                    <option value="delegate" {{ old('type') === 'delegate' ? 'selected' : '' }}>عهدة مندوب</option>
                                    <option value="petty_cash" {{ old('type') === 'petty_cash' ? 'selected' : '' }}>صندوق مصروفات نثرية</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="ghTargetBox" style="display: none;">
                                <label class="form-label fw-bold">دار الضيافة التابعة لها الخزينة</label>
                                <select name="guest_house_id" class="form-select">
                                    <option value="">-- اختر دار الضيافة --</option>
                                    @foreach($guestHouses ?? [] as $gh)
                                        <option value="{{ $gh->id }}" {{ old('guest_house_id') == $gh->id ? 'selected' : '' }}>{{ $gh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id="projectTargetBox" style="display: none;">
                                <label class="form-label fw-bold">المشروع التابع له الخزينة</label>
                                <select name="project_id" class="form-select">
                                    <option value="">-- اختر المشروع --</option>
                                    @foreach($projects ?? [] as $proj)
                                        <option value="{{ $proj->id }}" {{ old('project_id') == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id="campaignTargetBox" style="display: none;">
                                <label class="form-label fw-bold">الحملة التابعة لها الخزينة</label>
                                <select name="campaign_id" class="form-select">
                                    <option value="">-- اختر الحملة --</option>
                                    @foreach($campaigns ?? [] as $camp)
                                        <option value="{{ $camp->id }}" {{ old('campaign_id') == $camp->id ? 'selected' : '' }}>{{ $camp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <script>
                        (function() {
                            const toggleSpec = () => {
                                const val = document.getElementById('treasuryTypeSelect')?.value;
                                const gh = document.getElementById('ghTargetBox');
                                const pr = document.getElementById('projectTargetBox');
                                const ca = document.getElementById('campaignTargetBox');
                                if (gh) gh.style.display = val === 'guest_house' ? '' : 'none';
                                if (pr) pr.style.display = val === 'project' ? '' : 'none';
                                if (ca) ca.style.display = val === 'campaign' ? '' : 'none';
                            };
                            document.getElementById('treasuryTypeSelect')?.addEventListener('change', toggleSpec);
                            toggleSpec();
                        })();
                        </script>

                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">المدير المسؤول</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">-- اختر المدير --</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" {{ old('manager_id') == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموقع</label>
                                <input type="text" name="location" class="form-control" value="{{ old('location') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">العملة <span class="text-danger">*</span></label>
                                <select name="currency" class="form-select" required>
                                    <option value="EGP" {{ old('currency') == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                    <option value="USD" {{ old('currency') == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                    <option value="EUR" {{ old('currency') == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                    <option value="SAR" {{ old('currency') == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الرصيد الافتتاحي <span class="text-danger">*</span></label>
                                <input type="number" name="opening_balance" class="form-control @error('opening_balance') is-invalid @enderror" 
                                       value="{{ old('opening_balance', 0) }}" step="0.01" min="0" required>
                                @error('opening_balance')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                       {{ old('is_active', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    خزينة نشطة
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                حفظ الخزينة
                            </button>
                            <a href="{{ route('treasuries.index') }}" class="btn btn-secondary">
                                <i class="bi bi-x-circle me-2"></i>
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0 bg-light">
                <div class="card-body">
                    <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>معلومات مهمة</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            الكود يجب أن يكون فريداً
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            الرصيد الافتتاحي سيكون الرصيد الحالي
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            يمكن تعيين مدير مسؤول عن الخزينة
                        </li>
                        <li class="mb-2">
                            <i class="bi bi-check-circle text-success me-2"></i>
                            الخزينة غير النشطة لن تظهر في التبرعات
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


