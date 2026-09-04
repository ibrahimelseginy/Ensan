<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <style>
    body { font-family: "DejaVu Sans", sans-serif; direction: rtl; text-align: right; font-size: 9px; color: #1f2937; }
    h1 { color: #047857; text-align: center; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #cbd5e1; padding: 5px; vertical-align: top; }
    th { background: #d1fae5; color: #065f46; }
    tr:nth-child(even) { background: #f8fafc; }
    .meta { margin-bottom: 12px; color: #64748b; }
  </style>
</head>
<body>
  <h1>كشف المستفيدين — مؤسسة إنسان</h1>
  <div class="meta">تاريخ الإصدار: {{ now()->format('Y-m-d H:i') }} — العدد: {{ $beneficiaries->count() }}</div>
  <table>
    <thead><tr><th>#</th><th>الكود</th><th>ولي الأمر</th><th>القومي</th><th>الفيزا</th><th>الهاتفان</th><th>المريض</th><th>المشروع</th><th>الحالة</th><th>أفراد الأسرة</th></tr></thead>
    <tbody>
      @foreach($beneficiaries as $beneficiary)
        <tr>
          <td>{{ $beneficiary->id }}</td><td>{{ $beneficiary->code }}</td><td>{{ $beneficiary->full_name }}</td>
          <td>{{ $beneficiary->national_id }}</td><td>{{ $beneficiary->visa_card_number }}</td>
          <td>{{ $beneficiary->phone }}<br>{{ $beneficiary->backup_phone }}</td><td>{{ $beneficiary->patient_name }}</td>
          <td>{{ $beneficiary->project?->name }}</td><td>{{ $beneficiary->status }}</td>
          <td>{{ $beneficiary->familyMembers->where('active', true)->pluck('full_name')->implode('، ') }}</td>
        </tr>
      @endforeach
    </tbody>
  </table>
</body>
</html>
