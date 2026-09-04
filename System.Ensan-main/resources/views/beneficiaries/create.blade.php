@extends('layouts.app')

@section('styles')
<style>
  .family-member-card { border: 1px solid rgba(148,163,184,.25); border-radius: 14px; padding: 1rem; background: var(--bg-card, #fff); }
  .family-member-card__header { display:flex; align-items:center; justify-content:space-between; gap:1rem; font-weight:800; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px dashed rgba(148,163,184,.3); }
  .child-number { display:inline-grid; place-items:center; width:28px; height:28px; margin-left:.4rem; border-radius:50%; color:#fff; background:var(--primary); }
  .project-special-box { border:1px solid rgba(148,163,184,.25); border-radius:14px; padding:1rem; background:rgba(148,163,184,.07); }
  .theme-dark .family-member-card, .theme-dark .project-special-box { background:rgba(15,23,42,.7); border-color:rgba(255,255,255,.1); }
</style>
@endsection

@section('content')
<div class="page-header">
  <h4 class="mb-0"><i class="bi bi-person-plus text-primary"></i> إضافة مستفيد جديد</h4>
  <a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i> رجوع</a>
</div>

@if($errors->any())
  <div class="alert alert-danger">
    <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle me-1"></i> راجع البيانات التالية:</div>
    <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
  </div>
@endif

<div class="card">
  <div class="card-body">
    <form method="POST" action="{{ route('beneficiaries.store') }}">
      @csrf

      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-person-vcard"></i><span>بيانات ولي الأمر / المعيل</span></div>
        <div class="alert alert-primary py-2"><i class="bi bi-info-circle me-1"></i> الاسم الرئيسي هو اسم ولي الأمر، أما الزوج أو الزوجة والأطفال والمريض فلهم ملفات مستقلة بالأسفل.</div>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label">كود المستفيد</label><input name="code" value="{{ old('code') }}" class="form-control @error('code') is-invalid @enderror" placeholder="يُترك فارغًا للتوليد التلقائي">@error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <div class="col-md-4"><label class="form-label form-label-required">اسم ولي الأمر بالكامل</label><input name="full_name" value="{{ old('full_name') }}" class="form-control @error('full_name') is-invalid @enderror" required placeholder="اسم ولي الأمر أو المعيل">@error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
          <div class="col-md-4"><label class="form-label">الرقم القومي</label><input name="national_id" value="{{ old('national_id') }}" class="form-control" maxlength="14" placeholder="14 رقم"></div>
          <div class="col-md-4"><label class="form-label">رقم الفيزا / كارت الكفالة</label><input name="visa_card_number" value="{{ old('visa_card_number') }}" class="form-control" placeholder="رقم كارت استلام الكفالات"></div>
          <div class="col-md-4"><label class="form-label">رقم الهاتف الأساسي</label><input name="phone" value="{{ old('phone') }}" class="form-control" placeholder="01xxxxxxxxx"></div>
          <div class="col-md-4"><label class="form-label">رقم هاتف إضافي</label><input name="backup_phone" value="{{ old('backup_phone') }}" class="form-control" placeholder="01xxxxxxxxx"></div>
          <div class="col-12"><label class="form-label">العنوان بالتفصيل</label><input name="address" value="{{ old('address') }}" class="form-control" placeholder="المحافظة - المركز - القرية/الشارع - العنوان التفصيلي"></div>
        </div>
      </div>

      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-folder-symlink"></i><span>المشروع وحالة الملف</span></div>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label">المشروع</label>
            <select name="project_id" id="projectSelect" class="form-select">
              <option value="">— اختر المشروع —</option>
              @foreach($projects as $project)<option value="{{ $project->id }}" data-name="{{ $project->name }}" @selected((string) old('project_id') === (string) $project->id)>{{ $project->name }}</option>@endforeach
            </select>
          </div>
          <div class="col-md-6">
            <label class="form-label">حالة الملف</label>
            <select name="status" id="statusSelect" class="form-select">
              <option value="new" @selected(old('status', 'new') === 'new')>تحت التقديم (ملف جديد)</option>
              <option value="under_review" @selected(old('status') === 'under_review')>تحت المراجعة</option>
              <option value="accepted" @selected(old('status') === 'accepted')>مقبول</option>
              <option value="rejected" @selected(old('status') === 'rejected')>مرفوض</option>
              <option value="archived_improved" @selected(old('status') === 'archived_improved')>أرشيف — تحسن مادي / شفاء</option>
              <option value="archived_deceased" @selected(old('status') === 'archived_deceased')>أرشيف — توفي</option>
            </select>
          </div>
          <div class="col-12" id="rejectionReasonBox" style="display:none"><label class="form-label text-danger">سبب الرفض</label><textarea name="rejection_reason" class="form-control" rows="2">{{ old('rejection_reason') }}</textarea></div>
          <div class="col-12" id="archiveReasonBox" style="display:none"><label class="form-label">سبب الأرشفة وتفاصيل الحالة</label><textarea name="archived_reason" class="form-control" rows="2" placeholder="تحسنت الحالة المادية، تم الشفاء، أو تفاصيل الوفاة">{{ old('archived_reason') }}</textarea></div>
        </div>

        <div id="zadProjectFields" class="project-special-box mt-4" style="display:none">
          <h6 class="fw-bold text-success mb-3"><i class="bi bi-basket2 me-1"></i> بيانات مشروع زاد</h6>
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label">عدد الإخوة</label><input name="brothers_count" type="number" min="0" value="{{ old('brothers_count') }}" class="form-control"></div>
            <div class="col-md-8 d-flex align-items-end"><div class="form-text">أسماء الأطفال وبيانات كل طفل تُسجل في ملفات أفراد الأسرة بالأسفل.</div></div>
          </div>
        </div>

        <div id="bathaFields" class="project-special-box mt-4" style="display:none">
          <h6 class="fw-bold text-primary mb-3"><i class="bi bi-hospital me-1"></i> ملف المريض — مشروع بعثاء الأمل</h6>
          <div class="row g-3">
            <div class="col-md-4"><label class="form-label">اسم المريض بالكامل</label><input name="patient_name" value="{{ old('patient_name') }}" class="form-control" placeholder="مختلف عن اسم ولي الأمر"></div>
            <div class="col-md-2"><label class="form-label">صلة المريض</label><select name="patient_relationship" class="form-select"><option value="husband" @selected(old('patient_relationship') === 'husband')>الزوج</option><option value="wife" @selected(old('patient_relationship') === 'wife')>الزوجة</option><option value="child" @selected(old('patient_relationship') === 'child')>ابن / طفلة</option><option value="patient" @selected(old('patient_relationship', 'patient') === 'patient')>المستفيد نفسه</option><option value="other" @selected(old('patient_relationship') === 'other')>فرد آخر</option></select></div>
            <div class="col-md-2"><label class="form-label">تاريخ الميلاد</label><input name="patient_birth_date" type="date" value="{{ old('patient_birth_date') }}" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">السن</label><input name="patient_age" type="number" min="0" max="120" value="{{ old('patient_age') }}" class="form-control"></div>
            <div class="col-md-2"><label class="form-label">كود المريض</label><input name="patient_code" value="{{ old('patient_code') }}" class="form-control" placeholder="تلقائي"></div>
            <div class="col-md-3"><label class="form-label">هاتف المريض</label><input name="patient_phone" value="{{ old('patient_phone') }}" class="form-control" placeholder="01xxxxxxxxx"></div>
            <div class="col-md-3"><label class="form-label">هاتف إضافي</label><input name="patient_backup_phone" value="{{ old('patient_backup_phone') }}" class="form-control" placeholder="01xxxxxxxxx"></div>
            <div class="col-md-3"><label class="form-label">نوع المساعدة</label><select name="sponsorship_scope_type" class="form-select"><option value="kafala" @selected(old('sponsorship_scope_type') === 'kafala')>كفالة شهرية</option><option value="temporary" @selected(old('sponsorship_scope_type') === 'temporary')>مساعدة مؤقتة / عملية / علاج</option></select></div>
            <div class="col-md-3"><label class="form-label">مبلغ الكفالة / المساعدة</label><input name="monthly_sponsorship_amount" type="number" min="0" step="0.01" value="{{ old('monthly_sponsorship_amount') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">عدد إخوته</label><input name="brothers_count" type="number" min="0" value="{{ old('brothers_count') }}" class="form-control"></div>
            <div class="col-md-3"><label class="form-label">عدد أبنائه إن كان كبيرًا</label><input name="adult_children_count" type="number" min="0" value="{{ old('adult_children_count') }}" class="form-control"></div>
            <div class="col-md-6"><label class="form-label">أعمار الأبناء وتفاصيلهم</label><input name="adult_children_ages" value="{{ old('adult_children_ages') }}" class="form-control" placeholder="مثال: 12، 15، 18 سنة"></div>
            <div class="col-12"><label class="form-label">تفاصيل الحالة والتقارير والورق</label><textarea name="notes_cases" class="form-control" rows="3" placeholder="التشخيص، العملية المطلوبة، التقارير الطبية والمستندات...">{{ old('notes_cases') }}</textarea></div>
          </div>
        </div>
      </div>

      @include('beneficiaries._family_members_fields')

      <div class="form-section">
        <div class="form-section-title"><i class="bi bi-hand-thumbs-up"></i><span>نوع المساعدة وآلية التحصيل</span></div>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label form-label-required">نوع المساعدة</label><select name="assistance_type" id="assistanceType" class="form-select" required><option value="monthly" @selected(old('assistance_type', 'monthly') === 'monthly')>كفالة شهرية</option><option value="one_time" @selected(old('assistance_type') === 'one_time')>مساعدة مؤقتة / مرة واحدة</option><option value="in_kind" @selected(old('assistance_type') === 'in_kind')>مساعدة عينية</option><option value="service" @selected(old('assistance_type') === 'service')>مساعدة خدمية / علاجية</option><option value="financial" @selected(old('assistance_type') === 'financial')>مساعدة مالية</option></select></div>
          <div class="col-md-4 monthly-fields"><label class="form-label">يوم استلام الكفالة</label><input name="collection_day" type="number" min="1" max="31" value="{{ old('collection_day') }}" class="form-control" placeholder="1 إلى 31"></div>
          <div class="col-md-4 monthly-fields"><label class="form-label">طريقة الاستلام</label><select name="collection_method" class="form-select"><option value="فيزا" @selected(old('collection_method') === 'فيزا')>فيزا الكفالات</option><option value="مندوب" @selected(old('collection_method') === 'مندوب')>مندوب</option><option value="فودافون كاش" @selected(old('collection_method') === 'فودافون كاش')>فودافون كاش</option><option value="انستا باي" @selected(old('collection_method') === 'انستا باي')>انستا باي</option></select></div>
        </div>
      </div>

      <div class="form-section"><div class="form-section-title"><i class="bi bi-pencil-square"></i><span>ملاحظات عامة</span></div><textarea name="notes" class="form-control" rows="3" placeholder="ملاحظات داخلية حول الأسرة والحالة">{{ old('notes') }}</textarea></div>

      <div class="d-flex gap-2 justify-content-end mt-4"><a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-lg me-1"></i> إلغاء</a><button type="submit" class="btn btn-primary px-4 fw-bold"><i class="bi bi-check-lg me-1"></i> حفظ المستفيد وملفات الأسرة</button></div>
    </form>
  </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const assistanceType = document.getElementById('assistanceType');
  const projectSelect = document.getElementById('projectSelect');
  const statusSelect = document.getElementById('statusSelect');
  function toggleMonthlyFields() { const monthly = assistanceType?.value === 'monthly'; document.querySelectorAll('.monthly-fields').forEach(el => el.style.display = monthly ? '' : 'none'); }
  function toggleProjectFields() { const option = projectSelect?.options[projectSelect.selectedIndex]; const name = option?.dataset.name || option?.text || ''; const zadBox=document.getElementById('zadProjectFields'),bathaBox=document.getElementById('bathaFields'),isZad=name.includes('زاد'),isBatha=name.includes('بعثاء')||name.includes('علاج'); zadBox.style.display=isZad?'':'none'; bathaBox.style.display=isBatha?'':'none'; zadBox.querySelectorAll('input,select,textarea').forEach(field=>field.disabled=!isZad); bathaBox.querySelectorAll('input,select,textarea').forEach(field=>field.disabled=!isBatha); }
  function toggleStatusReason() { const status = statusSelect?.value; const rejectionBox = document.getElementById('rejectionReasonBox'); const archiveBox = document.getElementById('archiveReasonBox'); rejectionBox.style.display = status === 'rejected' ? '' : 'none'; archiveBox.style.display = ['archived_improved','archived_deceased'].includes(status) ? '' : 'none'; rejectionBox.querySelector('textarea').required = status === 'rejected'; archiveBox.querySelector('textarea').required = ['archived_improved','archived_deceased'].includes(status); }
  assistanceType?.addEventListener('change', toggleMonthlyFields); projectSelect?.addEventListener('change', toggleProjectFields); statusSelect?.addEventListener('change', toggleStatusReason); toggleMonthlyFields(); toggleProjectFields(); toggleStatusReason();
});
</script>
@endsection
