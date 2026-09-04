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

  <div class="family-member-card spouse-card mb-3">
    <div class="family-member-card__header">
      <div><i class="bi bi-person-heart text-primary"></i> الزوج / الزوجة</div>
      <span class="badge bg-secondary-subtle text-secondary">اختياري</span>
    </div>
    <input type="hidden" name="family_members[0][id]" value="{{ data_get($familyRows, '0.id') }}">
    <div class="row g-3">
      <div class="col-md-2">
        <label class="form-label">صلة القرابة</label>
        <select name="family_members[0][relationship]" class="form-select">
          <option value="husband" @selected(data_get($familyRows, '0.relationship', 'husband') === 'husband')>الزوج</option>
          <option value="wife" @selected(data_get($familyRows, '0.relationship') === 'wife')>الزوجة</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">الاسم بالكامل</label>
        <input name="family_members[0][full_name]" class="form-control" value="{{ data_get($familyRows, '0.full_name') }}" placeholder="اسم الزوج أو الزوجة">
      </div>
      <div class="col-md-2">
        <label class="form-label">تاريخ الميلاد</label>
        <input type="date" name="family_members[0][birth_date]" class="form-control" value="{{ data_get($familyRows, '0.birth_date') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">العمر</label>
        <input type="number" min="0" max="120" name="family_members[0][age]" class="form-control" value="{{ data_get($familyRows, '0.age') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">الكود</label>
        <input name="family_members[0][code]" class="form-control" value="{{ data_get($familyRows, '0.code') }}" placeholder="تلقائي">
      </div>
      <div class="col-md-4">
        <label class="form-label">الهاتف</label>
        <input name="family_members[0][phone]" class="form-control" value="{{ data_get($familyRows, '0.phone') }}" placeholder="01xxxxxxxxx">
      </div>
      <div class="col-md-4">
        <label class="form-label">هاتف إضافي</label>
        <input name="family_members[0][backup_phone]" class="form-control" value="{{ data_get($familyRows, '0.backup_phone') }}" placeholder="01xxxxxxxxx">
      </div>
      <div class="col-md-4">
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
              <input type="date" name="family_members[{{ $index }}][birth_date]" class="form-control" value="{{ data_get($familyRows, $index . '.birth_date') }}">
            </div>
            <div class="col-md-2">
              <label class="form-label">العمر</label>
              <input type="number" min="0" max="120" name="family_members[{{ $index }}][age]" class="form-control" value="{{ data_get($familyRows, $index . '.age') }}" placeholder="بالسنوات">
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
