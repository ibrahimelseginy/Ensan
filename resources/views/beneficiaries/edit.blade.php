@extends('layouts.app')

@section('styles')
<style>
  .family-member-card { border:1px solid rgba(148,163,184,.25); border-radius:14px; padding:1rem; background:var(--bg-card,#fff); }
  .family-member-card__header { display:flex; align-items:center; justify-content:space-between; gap:1rem; font-weight:800; margin-bottom:1rem; padding-bottom:.75rem; border-bottom:1px dashed rgba(148,163,184,.3); }
  .child-number { display:inline-grid; place-items:center; width:28px; height:28px; margin-left:.4rem; border-radius:50%; color:#fff; background:var(--primary); }
  .project-special-box { border:1px solid rgba(148,163,184,.25); border-radius:14px; padding:1rem; background:rgba(148,163,184,.07); }
  .theme-dark .family-member-card,.theme-dark .project-special-box { background:rgba(15,23,42,.7); border-color:rgba(255,255,255,.1); }
</style>
@endsection

@section('content')
@php($patientMember = $beneficiary->familyMembers->firstWhere('is_patient', true))
<div class="page-header">
  <div><h4 class="mb-1"><i class="bi bi-person-gear text-primary"></i> تعديل ملف المستفيد</h4><div class="small text-muted">{{ $beneficiary->full_name }} — {{ $beneficiary->code }}</div></div>
  <a href="{{ route('beneficiaries.show', $beneficiary) }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i> رجوع</a>
</div>

@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif

<div class="card"><div class="card-body">
  <form method="POST" action="{{ route('beneficiaries.update', $beneficiary) }}">
    @csrf @method('PUT')

    <div class="form-section">
      <div class="form-section-title"><i class="bi bi-person-vcard"></i><span>بيانات ولي الأمر / المعيل</span></div>
      <div class="row g-3">
        <div class="col-md-4"><label class="form-label">كود المستفيد</label><input name="code" value="{{ old('code', $beneficiary->code) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label form-label-required">اسم ولي الأمر بالكامل</label><input name="full_name" value="{{ old('full_name', $beneficiary->full_name) }}" required class="form-control @error('full_name') is-invalid @enderror">@error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror</div>
        <div class="col-md-4"><label class="form-label">الرقم القومي</label><input name="national_id" maxlength="14" value="{{ old('national_id', $beneficiary->national_id) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">رقم الفيزا / كارت الكفالة</label><input name="visa_card_number" value="{{ old('visa_card_number', $beneficiary->visa_card_number) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">الهاتف الأساسي</label><input name="phone" value="{{ old('phone', $beneficiary->phone) }}" class="form-control"></div>
        <div class="col-md-4"><label class="form-label">هاتف إضافي</label><input name="backup_phone" value="{{ old('backup_phone', $beneficiary->backup_phone) }}" class="form-control"></div>
        <div class="col-12"><label class="form-label">العنوان</label><input name="address" value="{{ old('address', $beneficiary->address) }}" class="form-control"></div>
      </div>
    </div>

    <div class="form-section">
      <div class="form-section-title"><i class="bi bi-folder-symlink"></i><span>المشروع وحالة الملف</span></div>
      <div class="row g-3">
        <div class="col-md-6"><label class="form-label">المشروع</label><select name="project_id" id="projectSelect" class="form-select"><option value="">— اختر المشروع —</option>@foreach($projects as $project)<option value="{{ $project->id }}" data-name="{{ $project->name }}" @selected((string) old('project_id', $beneficiary->project_id) === (string) $project->id)>{{ $project->name }}</option>@endforeach</select></div>
        <div class="col-md-6"><label class="form-label">حالة الملف</label><select name="status" id="statusSelect" class="form-select"><option value="pending" @selected(old('status',$beneficiary->status)==='pending')>تحت التقديم</option><option value="new" @selected(old('status',$beneficiary->status)==='new')>جديد</option><option value="under_review" @selected(old('status',$beneficiary->status)==='under_review')>تحت المراجعة</option><option value="accepted" @selected(old('status',$beneficiary->status)==='accepted')>مقبول</option><option value="rejected" @selected(old('status',$beneficiary->status)==='rejected')>مرفوض</option><option value="archived_improved" @selected(old('status',$beneficiary->status)==='archived_improved')>أرشيف — تحسن مادي / شفاء</option><option value="archived_deceased" @selected(old('status',$beneficiary->status)==='archived_deceased')>أرشيف — توفي</option></select></div>
        <div class="col-12" id="rejectionReasonBox" style="display:none"><label class="form-label text-danger">سبب الرفض</label><textarea name="rejection_reason" class="form-control" rows="2">{{ old('rejection_reason',$beneficiary->rejection_reason) }}</textarea></div>
        <div class="col-12" id="archiveReasonBox" style="display:none"><label class="form-label">سبب الأرشفة</label><textarea name="archived_reason" class="form-control" rows="2">{{ old('archived_reason',$beneficiary->archived_reason) }}</textarea></div>
      </div>

      <div id="zadProjectFields" class="project-special-box mt-4" style="display:none"><h6 class="fw-bold text-success mb-3"><i class="bi bi-basket2 me-1"></i> بيانات مشروع زاد</h6><div class="row g-3"><div class="col-md-4"><label class="form-label">عدد الإخوة</label><input name="brothers_count" type="number" min="0" value="{{ old('brothers_count',$beneficiary->brothers_count) }}" class="form-control"></div><div class="col-md-8 d-flex align-items-end"><div class="form-text">بيانات الأطفال موجودة في ملفات أفراد الأسرة.</div></div></div></div>

      <div id="bathaFields" class="project-special-box mt-4" style="display:none">
        <h6 class="fw-bold text-primary mb-3"><i class="bi bi-hospital me-1"></i> ملف المريض — مشروع بعثاء الأمل</h6>
        <div class="row g-3">
          <div class="col-md-4"><label class="form-label">اسم المريض بالكامل</label><input name="patient_name" value="{{ old('patient_name',$beneficiary->patient_name) }}" class="form-control"></div>
          <div class="col-md-2"><label class="form-label">صلة المريض</label><select name="patient_relationship" class="form-select">@foreach(['husband'=>'الزوج','wife'=>'الزوجة','child'=>'ابن / طفلة','patient'=>'المستفيد نفسه','other'=>'فرد آخر'] as $value=>$label)<option value="{{ $value }}" @selected(old('patient_relationship',$patientMember?->relationship ?? 'patient')===$value)>{{ $label }}</option>@endforeach</select></div>
          <div class="col-md-2"><label class="form-label">تاريخ الميلاد</label><input name="patient_birth_date" type="date" value="{{ old('patient_birth_date',optional($patientMember?->birth_date)->format('Y-m-d')) }}" class="form-control"></div>
          <div class="col-md-2"><label class="form-label">السن</label><input name="patient_age" type="number" min="0" max="120" value="{{ old('patient_age',$beneficiary->patient_age) }}" class="form-control"></div>
          <div class="col-md-2"><label class="form-label">كود المريض</label><input name="patient_code" value="{{ old('patient_code',$beneficiary->patient_code) }}" class="form-control"></div>
          <div class="col-md-3"><label class="form-label">هاتف المريض</label><input name="patient_phone" value="{{ old('patient_phone',$patientMember?->phone) }}" class="form-control"></div>
          <div class="col-md-3"><label class="form-label">هاتف إضافي</label><input name="patient_backup_phone" value="{{ old('patient_backup_phone',$patientMember?->backup_phone) }}" class="form-control"></div>
          <div class="col-md-3"><label class="form-label">نوع المساعدة</label><select name="sponsorship_scope_type" class="form-select"><option value="kafala" @selected(old('sponsorship_scope_type',$beneficiary->sponsorship_scope_type)==='kafala')>كفالة شهرية</option><option value="temporary" @selected(old('sponsorship_scope_type',$beneficiary->sponsorship_scope_type)==='temporary')>مساعدة مؤقتة / علاج</option></select></div>
          <div class="col-md-3"><label class="form-label">مبلغ الكفالة</label><input name="monthly_sponsorship_amount" type="number" min="0" step="0.01" value="{{ old('monthly_sponsorship_amount',$beneficiary->monthly_sponsorship_amount) }}" class="form-control"></div>
          <div class="col-md-3"><label class="form-label">عدد إخوته</label><input name="brothers_count" type="number" min="0" value="{{ old('brothers_count',$beneficiary->brothers_count) }}" class="form-control"></div>
          <div class="col-md-3"><label class="form-label">عدد أبنائه</label><input name="adult_children_count" type="number" min="0" value="{{ old('adult_children_count',$beneficiary->adult_children_count) }}" class="form-control"></div>
          <div class="col-md-6"><label class="form-label">أعمار الأبناء وتفاصيلهم</label><input name="adult_children_ages" value="{{ old('adult_children_ages',$beneficiary->adult_children_ages) }}" class="form-control"></div>
          <div class="col-12"><label class="form-label">تفاصيل الحالة والتقارير والورق</label><textarea name="notes_cases" class="form-control" rows="3">{{ old('notes_cases',$beneficiary->notes_cases) }}</textarea></div>
        </div>
      </div>
    </div>

    @include('beneficiaries._family_members_fields', ['beneficiary' => $beneficiary])

    <div class="form-section"><div class="form-section-title"><i class="bi bi-hand-thumbs-up"></i><span>نوع المساعدة وآلية التحصيل</span></div><div class="row g-3"><div class="col-md-4"><label class="form-label">نوع المساعدة</label><select name="assistance_type" id="assistanceType" class="form-select">@foreach(['monthly'=>'كفالة شهرية','one_time'=>'مساعدة مؤقتة','in_kind'=>'عينية','service'=>'خدمية / علاجية','financial'=>'مالية'] as $value=>$label)<option value="{{ $value }}" @selected(old('assistance_type',$beneficiary->assistance_type)===$value)>{{ $label }}</option>@endforeach</select></div><div class="col-md-4 monthly-fields"><label class="form-label">يوم الاستلام</label><input name="collection_day" type="number" min="1" max="31" value="{{ old('collection_day',$beneficiary->collection_day) }}" class="form-control"></div><div class="col-md-4 monthly-fields"><label class="form-label">طريقة الاستلام</label><select name="collection_method" class="form-select">@foreach(['فيزا','مندوب','فودافون كاش','انستا باي'] as $method)<option value="{{ $method }}" @selected(old('collection_method',$beneficiary->collection_method)===$method)>{{ $method }}</option>@endforeach</select></div></div></div>

    <div class="form-section"><div class="form-section-title"><i class="bi bi-pencil-square"></i><span>ملاحظات عامة</span></div><textarea name="notes" class="form-control" rows="3">{{ old('notes',$beneficiary->notes) }}</textarea></div>
    <div class="d-flex gap-2 justify-content-end"><a href="{{ route('beneficiaries.show',$beneficiary) }}" class="btn btn-outline-secondary">إلغاء</a><button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> حفظ التغييرات</button></div>
  </form>
</div></div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded',function(){const assistanceType=document.getElementById('assistanceType'),projectSelect=document.getElementById('projectSelect'),statusSelect=document.getElementById('statusSelect');function monthly(){document.querySelectorAll('.monthly-fields').forEach(el=>el.style.display=assistanceType?.value==='monthly'?'':'none')}function project(){const option=projectSelect?.options[projectSelect.selectedIndex],name=option?.dataset.name||option?.text||'',zadBox=document.getElementById('zadProjectFields'),bathaBox=document.getElementById('bathaFields'),isZad=name.includes('زاد'),isBatha=name.includes('بعثاء')||name.includes('علاج');zadBox.style.display=isZad?'':'none';bathaBox.style.display=isBatha?'':'none';zadBox.querySelectorAll('input,select,textarea').forEach(field=>field.disabled=!isZad);bathaBox.querySelectorAll('input,select,textarea').forEach(field=>field.disabled=!isBatha)}function status(){const value=statusSelect?.value,rejection=document.getElementById('rejectionReasonBox'),archive=document.getElementById('archiveReasonBox');rejection.style.display=value==='rejected'?'':'none';archive.style.display=['archived_improved','archived_deceased'].includes(value)?'':'none';rejection.querySelector('textarea').required=value==='rejected';archive.querySelector('textarea').required=['archived_improved','archived_deceased'].includes(value)}assistanceType?.addEventListener('change',monthly);projectSelect?.addEventListener('change',project);statusSelect?.addEventListener('change',status);monthly();project();status()});
</script>
@endsection
