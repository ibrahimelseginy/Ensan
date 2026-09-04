<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: "DejaVu Sans", sans-serif; direction: rtl; text-align: right; color: #1f2937; font-size: 12px; }
    h1, h2 { color: #047857; margin: 0 0 10px; }
    .header { border-bottom: 3px solid #10b981; padding-bottom: 12px; margin-bottom: 18px; }
    .muted { color: #64748b; }
    .grid { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    .grid td, .grid th { border: 1px solid #cbd5e1; padding: 7px; vertical-align: top; }
    .grid th { background: #ecfdf5; color: #065f46; }
    .section { margin-top: 18px; }
    .badge { display: inline-block; padding: 3px 7px; margin: 2px; border-radius: 4px; background: #dcfce7; color: #166534; }
  </style>
</head>
<body>
  @php
    $statusLabel = match($beneficiary->status) {
      'pending' => 'تحت التقديم', 'new' => 'جديد', 'under_review' => 'تحت المراجعة',
      'accepted' => 'مقبول', 'rejected' => 'مرفوض',
      'archived_improved' => 'أرشيف — تحسن / شفاء', 'archived_deceased' => 'أرشيف — توفي',
      default => $beneficiary->status,
    };
    $assistanceLabel = match($beneficiary->assistance_type) {
      'monthly' => 'كفالة شهرية', 'one_time' => 'مساعدة مؤقتة', 'in_kind' => 'عينية',
      'service' => 'خدمية / علاجية', 'financial' => 'مالية', default => $beneficiary->assistance_type,
    };
  @endphp
  <div class="header">
    <h1>ملف المستفيد — مؤسسة إنسان</h1>
    <div class="muted">تاريخ إصدار الملف: {{ now()->format('Y-m-d H:i') }}</div>
  </div>

  <h2>{{ $beneficiary->full_name }}</h2>
  <table class="grid">
    <tr><th>الكود</th><td>{{ $beneficiary->code ?? '—' }}</td><th>الحالة</th><td>{{ $statusLabel }}</td></tr>
    <tr><th>الرقم القومي</th><td>{{ $beneficiary->national_id ?? '—' }}</td><th>رقم الفيزا</th><td>{{ $beneficiary->visa_card_number ?? '—' }}</td></tr>
    <tr><th>الهاتف</th><td>{{ $beneficiary->phone ?? '—' }}</td><th>الهاتف الإضافي</th><td>{{ $beneficiary->backup_phone ?? '—' }}</td></tr>
    <tr><th>المشروع</th><td>{{ $beneficiary->project?->name ?? '—' }}</td><th>نوع المساعدة</th><td>{{ $assistanceLabel }}</td></tr>
    <tr><th>العنوان</th><td colspan="3">{{ $beneficiary->address ?? '—' }}</td></tr>
  </table>

  @if($beneficiary->patient_name)
    <div class="section"><h2>بيانات المريض</h2></div>
    <table class="grid">
      <tr><th>اسم المريض</th><td>{{ $beneficiary->patient_name }}</td><th>كود المريض</th><td>{{ $beneficiary->patient_code ?? '—' }}</td></tr>
      <tr><th>السن</th><td>{{ $beneficiary->patient_age ?? '—' }}</td><th>مبلغ الكفالة</th><td>{{ $beneficiary->monthly_sponsorship_amount ?? '—' }}</td></tr>
      <tr><th>تفاصيل الحالة</th><td colspan="3">{{ $beneficiary->notes_cases ?? '—' }}</td></tr>
    </table>
  @endif

  <div class="section"><h2>ملفات أفراد الأسرة</h2></div>
  <table class="grid">
    <thead><tr><th>الاسم</th><th>الصفة</th><th>الكود</th><th>العمر / الميلاد</th><th>التعليم</th><th>الكفالة</th><th>الكفلاء</th></tr></thead>
    <tbody>
      @forelse($beneficiary->familyMembers->where('active', true) as $member)
        <tr>
          <td>{{ $member->full_name }}</td><td>{{ $member->relationship_label }}</td><td>{{ $member->code }}</td>
          <td>{{ $member->birth_date ? $member->birth_date->format('Y-m-d') : ($member->age ?? '—') }}</td>
          <td>{{ $member->education_level ?? '—' }}</td><td>{{ $member->sponsorship_amount ?? '—' }}</td>
          <td>@forelse($member->sponsors as $sponsor)<span class="badge">{{ $sponsor->name }}</span>@empty — @endforelse</td>
        </tr>
      @empty
        <tr><td colspan="7">لا توجد ملفات أفراد أسرة مسجلة.</td></tr>
      @endforelse
    </tbody>
  </table>

  @if($beneficiary->notes)<div class="section"><h2>ملاحظات</h2><p>{{ $beneficiary->notes }}</p></div>@endif
</body>
</html>
