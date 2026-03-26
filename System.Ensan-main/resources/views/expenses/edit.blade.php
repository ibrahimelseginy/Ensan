@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-cart-check text-primary"></i>
      تعديل المصروف
    </h4>
    <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('expenses.update', $expense) }}">
        @csrf @method('PUT')

        {{-- Basic Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-info-circle"></i>
            <span>المعلومات الأساسية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">النوع</label>
              <select name="type" class="form-select" required>
                <option value="operational" @selected($expense->type === 'operational')>تشغيلي</option>
                <option value="aid" @selected($expense->type === 'aid')>مساعدات</option>
                <option value="logistics" @selected($expense->type === 'logistics')>لوجستي</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">البند الفرعي (التصنيف)</label>
              <input list="categoryOptions" name="category" class="form-control" value="{{ $expense->category }}"
                placeholder="اختر أو اكتب...">
              <datalist id="categoryOptions">
                <option value="المرتبات">
                <option value="الايجارات">
                <option value="فواتير الموبايل والإنترنت">
                <option value="فواتير الكهرباء والمياه">
                <option value="أدوات مكتبية وورق">
                <option value="نثريات">
                <option value="صيانة">
                <option value="وقود ومواصلات">
              </datalist>
            </div>
          </div>
        </div>

        {{-- Financial Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-cash-stack"></i>
            <span>البيانات المالية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label form-label-required">المبلغ</label>
              <div class="input-group">
                <input name="amount" type="number" step="0.01" class="form-control" value="{{ $expense->amount }}"
                  required>
                <select name="currency" class="form-select" style="max-width:110px">
                  <option value="EGP" @selected($expense->currency === 'EGP')>EGP</option>
                  <option value="USD" @selected($expense->currency === 'USD')>USD</option>
                  <option value="SAR" @selected($expense->currency === 'SAR')>SAR</option>
                  <option value="EUR" @selected($expense->currency === 'EUR')>EUR</option>
                  <option value="AED" @selected($expense->currency === 'AED')>AED</option>
                </select>
              </div>
            </div>
            <div class="col-md-6">
              <label class="form-label">طريقة الدفع</label>
              <select name="payment_method" class="form-select">
                <option value="cash" @selected($expense->payment_method === 'cash')>نقدي</option>
                <option value="vodafone_cash" @selected($expense->payment_method === 'vodafone_cash')>فودافون كاش</option>
                <option value="instapay" @selected($expense->payment_method === 'instapay')>انستا باي</option>
              </select>
            </div>
          </div>
        </div>

        {{-- Allocation Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-diagram-3"></i>
            <span>التخصيص</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">المستفيد</label>
              <select name="beneficiary_id" class="form-select">
                <option value="">— اختر المستفيد —</option>
                @foreach($beneficiaries as $b)
                  <option value="{{ $b->id }}" @selected($expense->beneficiary_id == $b->id)>{{ $b->full_name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">المشروع</label>
              <select name="project_id" class="form-select">
                <option value="">— اختر المشروع —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}" @selected($expense->project_id == $p->id)>{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">الحملة</label>
              <select name="campaign_id" class="form-select">
                <option value="">— اختر الحملة —</option>
                @foreach($campaigns as $c)
                  <option value="{{ $c->id }}" @selected($expense->campaign_id == $c->id)>{{ $c->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">دار الضيافة</label>
              <select name="guest_house_id" class="form-select">
                <option value="">— اختر دار الضيافة —</option>
                @foreach($guestHouses as $g)
                  <option value="{{ $g->id }}" @selected($expense->guest_house_id == $g->id)>{{ $g->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">مساحة العمل (Workspace)</label>
              <select name="workspace_id" class="form-select">
                <option value="">— اختر مساحة العمل —</option>
                @foreach($workspaces as $w)
                  <option value="{{ $w->id }}" @selected($expense->workspace_id == $w->id)>{{ $w->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>

        {{-- Notes Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-pencil-square"></i>
            <span>ملاحظات</span>
          </div>
          <div class="row g-3">
            <div class="col-12">
              <label class="form-label">وصف المصروف</label>
              <textarea name="description" class="form-control" rows="3">{{ $expense->description }}</textarea>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('expenses.show', $expense) }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> حفظ التغييرات
          </button>
        </div>
      </form>
    </div>
  </div>
@endsection