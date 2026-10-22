@php
  $savedMembers = isset($beneficiary)
      ? $beneficiary->familyMembers->where('active', true)->where('is_patient', false)
      : collect();
  $savedSpouse = $savedMembers->first(fn ($member) => in_array($member->relationship, ['husband', 'wife'], true));
  $savedChildren = $savedMembers->where('relationship', 'child')->values();

  $defaultRows = [];
  $defaultRows[0] = $savedSpouse ? [
      'id' => $savedSpouse->id,
      'relationship' => $savedSpouse->relationship,
      'full_name' => $savedSpouse->full_name,
      'birth_date' => optional($savedSpouse->birth_date)->format('Y-m-d'),
      'age' => $savedSpouse->age,
      'code' => $savedSpouse->code,
      'phone' => $savedSpouse->phone,
      'backup_phone' => $savedSpouse->backup_phone,
      'sponsorship_amount' => $savedSpouse->sponsorship_amount,
      'education_level' => $savedSpouse->education_level,
  ] : ['relationship' => 'husband'];

  for ($index = 1; $index <= 5; $index++) {
      $child = $savedChildren->get($index - 1);
      $defaultRows[$index] = $child ? [
          'id' => $child->id,
          'relationship' => 'child',
          'full_name' => $child->full_name,
          'birth_date' => optional($child->birth_date)->format('Y-m-d'),
          'age' => $child->age,
          'code' => $child->code,
          'phone' => $child->phone,
          'backup_phone' => $child->backup_phone,
          'sponsorship_amount' => $child->sponsorship_amount,
          'education_level' => $child->education_level,
          'case_details' => $child->case_details,
      ] : ['relationship' => 'child'];
  }

  $familyRows = old('family_members', $defaultRows);
@endphp

<div class="form-section family-files-section">
  <div class="form-section-title">
    <i class="bi bi-people-fill"></i>
    <span>ملفات أفراد الأسرة</span>
  </div>

  <div class="alert alert-info py-2 mb-3">
    <i class="bi bi-shield-check me-1"></i>
    كل فرد له ملف وكود مستقل، ويمكن ربط كافل أو أكثر به لاحقًا من شاشة إضافة التبرع دون إظهار بيانات باقي الأسرة.
  </div>

  <div class="family-member-card spouse-card mb-3" id="spouseCardContainer">
    <div class="family-member-card__header d-flex justify-content-between align-items-center">
      <div id="spouseCardTitle"><i class="bi bi-person-heart text-primary"></i> <span>الزوج / الزوجة</span></div>
      <span class="badge bg-secondary-subtle text-secondary" id="spouseCardBadge">اختياري</span>
    </div>
    <input type="hidden" name="family_members[0][id]" value="{{ data_get($familyRows, '0.id') }}">
    <div class="row g-3">
      <div class="col-md-2" id="spouseRelCol">
        <label class="form-label">صلة القرابة</label>
        <select name="family_members[0][relationship]" id="spouseRelationship" class="form-select">
          <option value="husband" @selected(data_get($familyRows, '0.relationship', 'husband') === 'husband')>الزوج</option>
          <option value="wife" @selected(data_get($familyRows, '0.relationship') === 'wife')>الزوجة</option>
        </select>
      </div>
      <div class="col-md-4" id="spouseNameCol">
        <label class="form-label" id="spouseNameLabel">الاسم بالكامل</label>
        <input name="family_members[0][full_name]" id="spouseFullName" class="form-control" value="{{ data_get($familyRows, '0.full_name') }}" placeholder="اسم الزوج أو الزوجة">
      </div>
      <div class="col-md-3" id="spouseBirthDateCol">
        <label class="form-label">تاريخ الميلاد</label>
        <input type="date" name="family_members[0][birth_date]" id="spouseBirthDate" class="form-control" value="{{ data_get($familyRows, '0.birth_date') }}" onchange="updateSpouseAge()">
      </div>
      <div class="col-md-3" id="spouseDeathDateCol" style="display:none;">
        <label class="form-label text-danger fw-bold"><i class="bi bi-clock-history me-1"></i> تاريخ الوفاة</label>
        <input type="date" name="family_members[0][death_date]" id="spouseDeathDate" class="form-control border-danger" value="{{ data_get($familyRows, '0.death_date') }}" onchange="updateSpouseAge()">
      </div>
      <div class="col-md-2" id="spouseAgeCol">
        <label class="form-label" id="spouseAgeLabel">العمر</label>
        <input type="number" min="0" max="120" name="family_members[0][age]" id="spouseAge" class="form-control" value="{{ data_get($familyRows, '0.age') }}" placeholder="تلقائي">
      </div>
      <div class="col-md-2 spouse-standard-field">
        <label class="form-label">الكود</label>
        <input name="family_members[0][code]" class="form-control" value="{{ data_get($familyRows, '0.code') }}" placeholder="تلقائي">
      </div>
      <div class="col-md-4 spouse-standard-field">
        <label class="form-label">الهاتف</label>
        <input name="family_members[0][phone]" class="form-control" value="{{ data_get($familyRows, '0.phone') }}" placeholder="01xxxxxxxxx">
      </div>
      <div class="col-md-4 spouse-standard-field">
        <label class="form-label">هاتف إضافي</label>
        <input name="family_members[0][backup_phone]" class="form-control" value="{{ data_get($familyRows, '0.backup_phone') }}" placeholder="01xxxxxxxxx">
      </div>
      <div class="col-md-4 spouse-standard-field">
        <label class="form-label">مبلغ الكفالة</label>
        <input type="number" min="0" step="0.01" name="family_members[0][sponsorship_amount]" class="form-control" value="{{ data_get($familyRows, '0.sponsorship_amount') }}">
      </div>
    </div>
  </div>

  <div class="d-flex justify-content-between align-items-center mb-2">
    <h6 class="mb-0"><i class="bi bi-emoji-smile text-success me-1"></i> ملفات الأطفال</h6>
    <span class="small text-muted">حتى 5 أطفال</span>
  </div>

  <div class="row g-3">
    @for($index = 1; $index <= 5; $index++)
      <div class="col-12 child-file-wrapper" data-child-number="{{ $index }}">
        <div class="family-member-card child-card">
          <div class="family-member-card__header">
            <div><span class="child-number">{{ $index }}</span> ملف الطفل {{ $index }}</div>
            <span class="badge bg-success-subtle text-success">ملف مستقل</span>
          </div>
          <input type="hidden" name="family_members[{{ $index }}][id]" value="{{ data_get($familyRows, $index . '.id') }}">
          <input type="hidden" name="family_members[{{ $index }}][relationship]" value="child">
          <div class="row g-3">
            <div class="col-md-4">
              <label class="form-label">اسم الطفل بالكامل</label>
              <input name="family_members[{{ $index }}][full_name]" class="form-control" value="{{ data_get($familyRows, $index . '.full_name') }}" placeholder="اسم الطفل">
            </div>
            <div class="col-md-2">
              <label class="form-label">تاريخ الميلاد</label>
              <input type="date" name="family_members[{{ $index }}][birth_date]" id="child_birth_{{ $index }}" class="form-control" value="{{ data_get($familyRows, $index . '.birth_date') }}" onchange="calcAge(this.value, null, 'child_age_{{ $index }}')">
            </div>
            <div class="col-md-2">
              <label class="form-label">العمر (تلقائي)</label>
              <input type="number" min="0" max="120" name="family_members[{{ $index }}][age]" id="child_age_{{ $index }}" class="form-control" value="{{ data_get($familyRows, $index . '.age') }}" placeholder="بالسنوات">
            </div>
            <div class="col-md-2">
              <label class="form-label">كود الطفل</label>
              <input name="family_members[{{ $index }}][code]" class="form-control" value="{{ data_get($familyRows, $index . '.code') }}" placeholder="تلقائي">
            </div>
            <div class="col-md-2">
              <label class="form-label">مبلغ الكفالة</label>
              <input type="number" min="0" step="0.01" name="family_members[{{ $index }}][sponsorship_amount]" class="form-control" value="{{ data_get($familyRows, $index . '.sponsorship_amount') }}">
            </div>
            <div class="col-md-4">
              <label class="form-label">المستوى التعليمي</label>
              <input name="family_members[{{ $index }}][education_level]" class="form-control" value="{{ data_get($familyRows, $index . '.education_level') }}" placeholder="الصف أو المرحلة التعليمية">
            </div>
            <div class="col-md-4">
              <label class="form-label">الهاتف إن وجد</label>
              <input name="family_members[{{ $index }}][phone]" class="form-control" value="{{ data_get($familyRows, $index . '.phone') }}" placeholder="01xxxxxxxxx">
            </div>
            <div class="col-md-4">
              <label class="form-label">ملاحظات خاصة بالطفل</label>
              <input name="family_members[{{ $index }}][case_details]" class="form-control" value="{{ data_get($familyRows, $index . '.case_details') }}" placeholder="صحة، دراسة، احتياجات...">
            </div>
          </div>
        </div>
      </div>
    @endfor
  </div>
</div>

<script>
function calcAge(birthDateVal, deathDateVal, targetId) {
  if (!birthDateVal) return;
  const birth = new Date(birthDateVal);
  if (isNaN(birth.getTime())) return;
  const end = (deathDateVal && !isNaN(new Date(deathDateVal).getTime())) ? new Date(deathDateVal) : new Date();
  let age = end.getFullYear() - birth.getFullYear();
  const m = end.getMonth() - birth.getMonth();
  if (m < 0 || (m === 0 && end.getDate() < birth.getDate())) {
    age--;
  }
  if (age >= 0) {
    const target = document.getElementById(targetId);
    if (target) target.value = age;
  }
}

function updateSpouseAge() {
  const b = document.getElementById('spouseBirthDate')?.value;
  const d = document.getElementById('spouseDeathDate')?.value;
  calcAge(b, d, 'spouseAge');
}

function toggleSpouseZadMode(isZad) {
  const title = document.getElementById('spouseCardTitle');
  const badge = document.getElementById('spouseCardBadge');
  const nameLabel = document.getElementById('spouseNameLabel');
  const nameInput = document.getElementById('spouseFullName');
  const ageLabel = document.getElementById('spouseAgeLabel');
  const relCol = document.getElementById('spouseRelCol');
  const deathCol = document.getElementById('spouseDeathDateCol');
  const stdFields = document.querySelectorAll('.spouse-standard-field');

  if (isZad) {
    if (title) title.innerHTML = '<i class="bi bi-person-x text-danger me-1"></i> <span>بيانات الزوج المتوفى (مشروع زاد للأيتام)</span>';
    if (badge) { badge.className = 'badge bg-danger text-white'; badge.textContent = 'متوفى'; }
    if (nameLabel) nameLabel.textContent = 'اسم الزوج المتوفى رباعياً';
    if (nameInput) nameInput.placeholder = 'اسم الزوج المتوفى رحمه الله';
    if (ageLabel) ageLabel.textContent = 'السن عند الوفاة';
    if (relCol) relCol.style.display = 'none';
    if (deathCol) deathCol.style.display = '';
    stdFields.forEach(el => el.style.display = 'none');
  } else {
    if (title) title.innerHTML = '<i class="bi bi-person-heart text-primary me-1"></i> <span>الزوج / الزوجة</span>';
    if (badge) { badge.className = 'badge bg-secondary-subtle text-secondary'; badge.textContent = 'اختياري'; }
    if (nameLabel) nameLabel.textContent = 'الاسم بالكامل';
    if (nameInput) nameInput.placeholder = 'اسم الزوج أو الزوجة';
    if (ageLabel) ageLabel.textContent = 'العمر';
    if (relCol) relCol.style.display = '';
    if (deathCol) deathCol.style.display = 'none';
    stdFields.forEach(el => el.style.display = '');
  }
  updateSpouseAge();
}

async function checkDuplicateNationalId(inputEl) {
  const val = inputEl.value?.trim();
  const warningEl = document.getElementById('national_id_warning');
  if (!warningEl) return;
  if (!val || val.length < 14) {
    warningEl.innerHTML = '';
    return;
  }
  try {
    const res = await fetch(`{{ route('check-national-id') }}?national_id=${encodeURIComponent(val)}`);
    const data = await res.json();
    if (data.exists) {
      warningEl.innerHTML = `<div class="alert alert-danger p-2 mb-0 small fw-bold"><i class="bi bi-exclamation-triangle-fill me-1"></i> ${data.message}</div>`;
    } else {
      warningEl.innerHTML = `<div class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> الرقم القومي متاح</div>`;
    }
  } catch (e) {
    console.error(e);
  }
}

document.addEventListener('DOMContentLoaded', function() {
  updateSpouseAge();
});
</script>
