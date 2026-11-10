@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h2><i class="bi bi-pencil me-2"></i>تعديل خزينة: {{ $treasury->name }}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <form action="{{ route('treasuries.update', $treasury) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">اسم الخزينة <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                       value="{{ old('name', $treasury->name) }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الكود <span class="text-danger">*</span></label>
                                <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" 
                                       value="{{ old('code', $treasury->code) }}" required>
                                @error('code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3 p-3 bg-light rounded-3 border mx-0">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">تخصص الخزينة / القسم التابع لها <span class="text-danger">*</span></label>
                                <select name="type" id="treasuryTypeEditSelect" class="form-select" required>
                                    @php $curType = old('type', $treasury->type ?: 'main'); @endphp
                                    <option value="main" {{ $curType === 'main' ? 'selected' : '' }}>الخزينة العامة / الحسابات</option>
                                    <option value="reception" {{ $curType === 'reception' ? 'selected' : '' }}>خزينة الاستقبال (تظهر في الاستقبال فقط)</option>
                                    <option value="guest_house" {{ $curType === 'guest_house' ? 'selected' : '' }}>خزينة دار ضيافة (تظهر في دار الضيافة المحددة فقط)</option>
                                    <option value="project" {{ $curType === 'project' ? 'selected' : '' }}>خزينة مشروع (تظهر في المشروع المحدد فقط)</option>
                                    <option value="campaign" {{ $curType === 'campaign' ? 'selected' : '' }}>خزينة حملة (تظهر في الحملة المحددة فقط)</option>
                                    <option value="delegate" {{ $curType === 'delegate' ? 'selected' : '' }}>عهدة مندوب</option>
                                    <option value="petty_cash" {{ $curType === 'petty_cash' ? 'selected' : '' }}>صندوق مصروفات نثرية</option>
                                </select>
                            </div>
                            <div class="col-md-6" id="ghTargetEditBox" style="display: none;">
                                <label class="form-label fw-bold">دار الضيافة التابعة لها الخزينة</label>
                                <select name="guest_house_id" class="form-select">
                                    <option value="">-- اختر دار الضيافة --</option>
                                    @foreach($guestHouses ?? [] as $gh)
                                        <option value="{{ $gh->id }}" {{ old('guest_house_id', $treasury->guest_house_id) == $gh->id ? 'selected' : '' }}>{{ $gh->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id="projectTargetEditBox" style="display: none;">
                                <label class="form-label fw-bold">المشروع التابع له الخزينة</label>
                                <select name="project_id" class="form-select">
                                    <option value="">-- اختر المشروع --</option>
                                    @foreach($projects ?? [] as $proj)
                                        <option value="{{ $proj->id }}" {{ old('project_id', $treasury->project_id) == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6" id="campaignTargetEditBox" style="display: none;">
                                <label class="form-label fw-bold">الحملة التابعة لها الخزينة</label>
                                <select name="campaign_id" class="form-select">
                                    <option value="">-- اختر الحملة --</option>
                                    @foreach($campaigns ?? [] as $camp)
                                        <option value="{{ $camp->id }}" {{ old('campaign_id', $treasury->campaign_id) == $camp->id ? 'selected' : '' }}>{{ $camp->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <script>
                        (function() {
                            const toggleSpecEdit = () => {
                                const val = document.getElementById('treasuryTypeEditSelect')?.value;
                                const gh = document.getElementById('ghTargetEditBox');
                                const pr = document.getElementById('projectTargetEditBox');
                                const ca = document.getElementById('campaignTargetEditBox');
                                if (gh) gh.style.display = val === 'guest_house' ? '' : 'none';
                                if (pr) pr.style.display = val === 'project' ? '' : 'none';
                                if (ca) ca.style.display = val === 'campaign' ? '' : 'none';
                            };
                            document.getElementById('treasuryTypeEditSelect')?.addEventListener('change', toggleSpecEdit);
                            toggleSpecEdit();
                        })();
                        </script>

                        <div class="mb-3">
                            <label class="form-label">الوصف</label>
                            <textarea name="description" class="form-control" rows="3">{{ old('description', $treasury->description) }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">المدير المسؤول</label>
                                <select name="manager_id" class="form-select">
                                    <option value="">-- اختر المدير --</option>
                                    @foreach($managers as $manager)
                                        <option value="{{ $manager->id }}" 
                                                {{ old('manager_id', $treasury->manager_id) == $manager->id ? 'selected' : '' }}>
                                            {{ $manager->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">الموقع</label>
                                <input type="text" name="location" class="form-control" 
                                       value="{{ old('location', $treasury->location) }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">العملة <span class="text-danger">*</span></label>
                                <select name="currency" class="form-select" required>
                                    <option value="EGP" {{ old('currency', $treasury->currency) == 'EGP' ? 'selected' : '' }}>جنيه مصري (EGP)</option>
                                    <option value="USD" {{ old('currency', $treasury->currency) == 'USD' ? 'selected' : '' }}>دولار أمريكي (USD)</option>
                                    <option value="EUR" {{ old('currency', $treasury->currency) == 'EUR' ? 'selected' : '' }}>يورو (EUR)</option>
                                    <option value="SAR" {{ old('currency', $treasury->currency) == 'SAR' ? 'selected' : '' }}>ريال سعودي (SAR)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-primary">الرصيد الحالي</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="current_balance" class="form-control @error('current_balance') is-invalid @enderror" 
                                           value="{{ old('current_balance', $treasury->current_balance) }}">
                                    <span class="input-group-text">{{ $treasury->currency }}</span>
                                    @error('current_balance')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">يمكنك تعديل الرصيد الحالي مباشرة يدوياً</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-success">إجمالي الوارد (تعديل يدوي)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_in" class="form-control @error('total_in') is-invalid @enderror"
                                           value="{{ old('total_in', $treasury->getTotalIn()) }}">
                                    <span class="input-group-text text-success fw-bold">{{ $treasury->currency }}</span>
                                    @error('total_in')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">تعديل إجمالي الوارد للخزينة مباشرة (اكتب 0 لتصفير الوارد)</small>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">إجمالي الصادر (تعديل يدوي)</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" name="total_out" class="form-control @error('total_out') is-invalid @enderror"
                                           value="{{ old('total_out', $treasury->getTotalOut()) }}">
                                    <span class="input-group-text text-danger fw-bold">{{ $treasury->currency }}</span>
                                    @error('total_out')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                <small class="text-muted">تعديل إجمالي الصادر للخزينة مباشرة (اكتب 0 لتصفير الصادر)</small>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input type="hidden" name="is_active" value="0">
                                <input class="form-check-input" type="checkbox" name="is_active" id="isActive" value="1"
                                       {{ old('is_active', $treasury->is_active) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isActive">
                                    خزينة نشطة
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>
                                حفظ التعديلات
                            </button>
                            <a href="{{ route('treasuries.show', $treasury) }}" class="btn btn-secondary">
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
                    <h5 class="mb-3"><i class="bi bi-info-circle me-2"></i>معلومات</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <strong>تاريخ الإنشاء:</strong><br>
                            {{ $treasury->created_at->format('Y-m-d H:i') }}
                        </li>
                        <li class="mb-2">
                            <strong>آخر تحديث:</strong><br>
                            {{ $treasury->updated_at->format('Y-m-d H:i') }}
                        </li>
                        <li class="mb-2">
                            <strong>الرصيد الافتتاحي:</strong><br>
                            {{ number_format($treasury->opening_balance, 2) }} {{ $treasury->currency }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


