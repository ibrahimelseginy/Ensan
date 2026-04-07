@extends('layouts.app')
@section('content')
  {{-- Page Header --}}
  <div class="page-header">
    <h4 class="mb-0">
      <i class="bi bi-person-gear text-primary"></i>
      تعديل المستفيد
    </h4>
    <a href="{{ route('beneficiaries.show', $beneficiary) }}" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-right me-1"></i> رجوع
    </a>
  </div>

  <div class="card">
    <div class="card-body">
      <form method="POST" action="{{ route('beneficiaries.update', $beneficiary) }}">
        @csrf @method('PUT')

        {{-- Personal Info Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-person-lines-fill"></i>
            <span>البيانات الشخصية</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">كود المستفيد</label>
              <input name="code" class="form-control" value="{{ $beneficiary->code }}" placeholder="سيتم توليده تلقائياً إذا ترك فارغاً">
              @if(empty($beneficiary->code))
                <div class="form-text text-warning">
                  <i class="bi bi-info-circle me-1"></i>سيتم توليد كود تلقائياً عند الحفظ
                </div>
              @endif
            </div>
            <div class="col-md-6">
              <label class="form-label form-label-required">الاسم الكامل</label>
              <input name="full_name" class="form-control @error('full_name') is-invalid @enderror" value="{{ old('full_name', $beneficiary->full_name) }}" required>
              @error('full_name')
                  <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-md-6">
              <label class="form-label form-label-required">الرقم القومي</label>
              <input name="national_id" class="form-control" value="{{ $beneficiary->national_id }}" required
                pattern="^[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{7}$" title="الرقم القومي 14 رقم بصيغة صحيحة">
              <div class="form-help-text">الرقم القومي المصري المكون من 14 رقم</div>
            </div>
            <div class="col-md-6">
              <label class="form-label form-label-required">رقم الهاتف</label>
              <input name="phone" class="form-control" value="{{ $beneficiary->phone }}" required
                pattern="^(01[0125]\d{8}|\+?201[0125]\d{8})$" title="ابدأ بـ 010 أو 011 أو 012 أو 015">
            </div>
            <div class="col-md-6">
              <label class="form-label form-label-required">العنوان</label>
              <input name="address" class="form-control" value="{{ $beneficiary->address }}" required>
            </div>
          </div>
        </div>

        {{-- Status & Assistance Section --}}
        <div class="form-section">
          <div class="form-section-title">
            <i class="bi bi-hand-thumbs-up"></i>
            <span>الحالة ونوع المساعدة</span>
          </div>
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label form-label-required">نوع المساعدة</label>
              <select name="assistance_type" class="form-select" required>
                <option value="financial" @selected($beneficiary->assistance_type === 'financial')>مالية</option>
                <option value="in_kind" @selected($beneficiary->assistance_type === 'in_kind')>عينية</option>
                <option value="service" @selected($beneficiary->assistance_type === 'service')>خدمية</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">حالة الطلب</label>
              <select name="status" class="form-select">
                <option value="new" @selected($beneficiary->status === 'new')>جديد</option>
                <option value="under_review" @selected($beneficiary->status === 'under_review')>تحت المراجعة</option>
                <option value="accepted" @selected($beneficiary->status === 'accepted')>مقبول</option>
                <option value="rejected" @selected($beneficiary->status === 'rejected')>مرفوض</option>
              </select>
            </div>
            <div class="col-12" id="rejectionReasonBox" style="display:none">
              <label class="form-label text-danger">سبب الرفض</label>
              <textarea name="rejection_reason" class="form-control" rows="2"
                placeholder="يرجى توضيح سبب الرفض...">{{ $beneficiary->rejection_reason ?? '' }}</textarea>
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
            <div class="col-md-4">
              <label class="form-label">المشروع</label>
              <select name="project_id" class="form-select">
                <option value="">— اختر المشروع —</option>
                @foreach($projects as $p)
                  <option value="{{ $p->id }}" @selected($beneficiary->project_id == $p->id)>{{ $p->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">دار الضيافة</label>
              <select name="guest_house_id" class="form-select">
                <option value="">— اختر دار الضيافة —</option>
                @foreach($guestHouses as $gh)
                  <option value="{{ $gh->id }}" @selected($beneficiary->guest_house_id == $gh->id)>{{ $gh->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="form-label">الحملة</label>
              <select name="campaign_id" class="form-select">
                <option value="">— اختر الحملة —</option>
                @foreach($campaigns as $c)
                  <option value="{{ $c->id }}" @selected($beneficiary->campaign_id == $c->id)>{{ $c->name }}
                    ({{ $c->season_year }})</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">نوع التخصيص</label>
              <select name="allocation_type" class="form-select">
                <option value="">— اختر النوع —</option>
                <option value="شخص واحد" @selected($beneficiary->allocation_type === 'شخص واحد')>شخص واحد</option>
                <option value="أكثر من مستفيد" @selected($beneficiary->allocation_type === 'أكثر من مستفيد')>أكثر من مستفيد</option>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">التخصيص لكل طفل</label>
              <select name="child_sponsorship_type" class="form-select">
                <option value="">— اختر النوع —</option>
                <option value="كافل واحد" @selected($beneficiary->child_sponsorship_type === 'كافل واحد')>كافل واحد</option>
                <option value="أكثر من كافل" @selected($beneficiary->child_sponsorship_type === 'أكثر من كافل')>أكثر من كافل</option>
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
              <label class="form-label">ملاحظات داخلية</label>
              <textarea name="notes" class="form-control" rows="3">{{ $beneficiary->notes }}</textarea>
            </div>
          </div>
        </div>

        {{-- Action Buttons --}}
        <div class="d-flex gap-2 justify-content-end">
          <a href="{{ route('beneficiaries.show', $beneficiary) }}" class="btn btn-outline-secondary">
            <i class="bi bi-x-lg me-1"></i> إلغاء
          </a>
          <button class="btn btn-primary">
            <i class="bi bi-check-lg me-1"></i> حفظ التغييرات
          </button>
        </div>
      </form>
    </div>
  </div>
  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const statusSelect = document.querySelector('select[name="status"]');
      const rejectionBox = document.getElementById('rejectionReasonBox');

      function toggleRejection() {
        if (statusSelect && rejectionBox) {
          rejectionBox.style.display = statusSelect.value === 'rejected' ? 'block' : 'none';
          const input = rejectionBox.querySelector('textarea');
          if (input) {
            input.required = statusSelect.value === 'rejected';
          }
        }
      }

      if (statusSelect) {
        statusSelect.addEventListener('change', toggleRejection);
        toggleRejection(); // Initial check
      }
    });
  </script>
@endsection

