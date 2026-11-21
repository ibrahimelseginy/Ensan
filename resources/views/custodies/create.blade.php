@extends('layouts.app')

@section('title', 'صرف عهدة مالية جديدة لموظف')

@section('content')
<div class="container py-4" style="direction: rtl; text-align: right; max-width: 800px;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold mb-1">صرف عهدة مالية جديدة لموظف</h3>
            <p class="text-muted small mb-0">يتم خصم المبلغ من الخزينة المحددة وتسجيله كعهدة مستحقة على الموظف دون تسجيله كمصروف نهائي</p>
        </div>
        <a href="{{ route('custodies.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
            <i class="bi bi-arrow-right me-1"></i> العودة
        </a>
    </div>

    @if(session('error'))
        <div class="alert alert-danger rounded-4 mb-4">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <form method="POST" action="{{ route('custodies.store') }}">
            @csrf

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small">الموظف المستلم للعهدة <span class="text-danger">*</span></label>
                    <select name="holder_user_id" class="form-select rounded-3 @error('holder_user_id') is-invalid @enderror" required>
                        <option value="">-- اختر الموظف --</option>
                        @foreach($employees as $emp)
                            <option value="{{ $emp->id }}" {{ old('holder_user_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }} ({{ $emp->job_title ?? 'موظف' }})</option>
                        @endforeach
                    </select>
                    @error('holder_user_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small">الخزينة المنصرف منها المبلغ <span class="text-danger">*</span></label>
                    <select name="treasury_id" class="form-select rounded-3 @error('treasury_id') is-invalid @enderror" required>
                        <option value="">-- اختر الخزينة --</option>
                        @foreach($treasuries as $t)
                            <option value="{{ $t->id }}" {{ old('treasury_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->name }} (الرصيد المتاح: {{ number_format($t->current_balance, 2) }} ج.م)
                            </option>
                        @endforeach
                    </select>
                    @error('treasury_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold small">قيمة العهدة (ج.م) <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" min="1" name="issued_amount" value="{{ old('issued_amount') }}" class="form-control rounded-3 font-monospace @error('issued_amount') is-invalid @enderror" placeholder="0.00" required>
                    @error('issued_amount') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">تاريخ تسليم العهدة <span class="text-danger">*</span></label>
                    <input type="date" name="issued_on" value="{{ old('issued_on', date('Y-m-d')) }}" class="form-control rounded-3 @error('issued_on') is-invalid @enderror" required>
                    @error('issued_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small">موعد التسوية المتوقع</label>
                    <input type="date" name="due_on" value="{{ old('due_on') }}" class="form-control rounded-3 @error('due_on') is-invalid @enderror">
                    @error('due_on') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold small">المشروع أو النشاط المرتبط بالعهدة (إن وجد)</label>
                <select name="project_id" class="form-select rounded-3 @error('project_id') is-invalid @enderror">
                    <option value="">-- مصروف عام للمؤسسة (غير مقيد بمشروع) --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ old('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
                <small class="text-muted d-block mt-1">عند اختيار مشروع، سيتم تحميل الفواتير المعتمدة فقط على رصيد المشروع بعد التسوية دون خصم العهدة بالكامل مقدماً.</small>
                @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold small">الغرض من العهدة وبيان الصرف <span class="text-danger">*</span></label>
                <textarea name="purpose" class="form-control rounded-3 @error('purpose') is-invalid @enderror" rows="3" placeholder="اكتب الغرض التفصيلي للعهدة، البنود المتوقع الصرف عليها، والملاحظات..." required>{{ old('purpose') }}</textarea>
                @error('purpose') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="alert alert-info py-2 px-3 small rounded-3 mb-4">
                <i class="bi bi-info-circle-fill me-1"></i>
                <strong>تنبيه محاسبي:</strong> العهدة تعتبر التزاماً مالياً على ذمة الموظف حتى يتقدم بفواتير التسوية ويتم اعتمادها ورد المتبقي.
            </div>

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('custodies.index') }}" class="btn btn-secondary rounded-pill px-4">إلغاء</a>
                <button type="submit" class="btn btn-success rounded-pill px-5">اعتماد وصرف العهدة</button>
            </div>
        </form>
    </div>

</div>
@endsection

