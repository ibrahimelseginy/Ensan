<!doctype html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="utf-8">
  <title>{{ $reportTitle ?? 'كشف المتبرعين' }} — مؤسسة إنسان</title>
  <style>
    @page {
      margin: 12mm 10mm;
      size: A4 landscape;
    }
    body {
      font-family: "DejaVu Sans", sans-serif;
      direction: rtl;
      text-align: right;
      font-size: 10px;
      color: #0f172a;
      background: #ffffff;
      margin: 0;
      padding: 0;
    }
    .header-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
      border-bottom: 2px solid #047857;
      padding-bottom: 8px;
    }
    .header-table td {
      border: none;
      vertical-align: middle;
      padding: 4px;
    }
    .report-title-box {
      text-align: center;
    }
    .report-title {
      font-size: 14px;
      font-weight: bold;
      color: #047857;
      margin: 0 0 4px 0;
    }
    .report-subtitle {
      font-size: 8px;
      color: #64748b;
    }
    .kpi-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 12px;
    }
    .kpi-table td {
      border: 1px solid #cbd5e1;
      padding: 6px 10px;
      text-align: center;
      background: #f8fafc;
    }
    .kpi-title {
      font-size: 9px;
      color: #475569;
      font-weight: bold;
      margin-bottom: 3px;
    }
    .kpi-value {
      font-size: 13px;
      font-weight: bold;
      color: #047857;
    }
    .data-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 14px;
    }
    .data-table th, .data-table td {
      border: 1px solid #cbd5e1;
      padding: 5px 6px;
      font-size: 9px;
    }
    .data-table th {
      background-color: #047857;
      color: #ffffff;
      text-align: center;
      font-weight: bold;
    }
    .data-table tbody tr:nth-child(even) {
      background-color: #f8fafc;
    }
    .data-table tfoot td {
      background-color: #f1f5f9;
      font-weight: bold;
      border-top: 2px solid #047857;
    }
    .text-center { text-align: center; }
    .text-left { text-align: left; }
    .text-right { text-align: right; }
    .footer-table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 15px;
      border-top: 1.5px solid #334155;
      padding-top: 8px;
    }
    .footer-table td {
      border: none;
      text-align: center;
      vertical-align: top;
      padding: 8px 4px;
      font-size: 9px;
    }
    .signature-line {
      border-bottom: 1px dashed #475569;
      width: 120px;
      margin: 15px auto 4px auto;
    }
    .legal-notice {
      text-align: center;
      font-size: 8px;
      color: #64748b;
      margin-top: 10px;
      border-top: 1px solid #e2e8f0;
      padding-top: 5px;
    }
    .badge {
      padding: 3px 6px;
      border-radius: 4px;
      font-size: 8.5px;
      font-weight: bold;
      color: #ffffff !important;
    }
    .badge-success { background: #059669; color: #ffffff !important; }
    .badge-secondary { background: #64748b; color: #ffffff !important; }
    .badge-info { background: #2563eb; color: #ffffff !important; }
  </style>
</head>
<body>

  {{-- Official Header --}}
  <table class="header-table">
    <tr>
      <td style="width: 32%; text-align: right;">
        <div style="font-weight: bold; font-size: 9.5px; color: #334155;">جمهورية مصر العربية</div>
        <div style="font-weight: bold; font-size: 9.5px; color: #334155;">وزارة التضامن الاجتماعي</div>
        <div style="font-weight: bold; font-size: 11px; color: #047857;">مؤسسة إنسان الخيرية</div>
        <div style="font-size: 8px; color: #64748b;">مشهرة برقم رسمي للعمل التنموي والخيري</div>
      </td>
      <td style="width: 36%;" class="report-title-box">
        <div class="report-title">{{ $reportTitle ?? 'كشف بيانات المتبرعين' }}</div>
        <div class="report-subtitle">OFFICIAL DONORS LIST REPORT</div>
      </td>
      <td style="width: 32%; text-align: left; font-size: 8.5px; color: #475569;">
        <div><strong>تاريخ الاستخراج:</strong> {{ now()->format('Y-m-d') }}</div>
        <div><strong>وقت الاستخراج:</strong> {{ now()->format('H:i') }}</div>
        <div><strong>المستخدم:</strong> {{ auth()->user()->name ?? 'موظف النظام' }}</div>
      </td>
    </tr>
  </table>

  {{-- KPI Summary Boxes --}}
  <table class="kpi-table">
    <tr>
      <td style="width: 33.3%;">
        <div class="kpi-title">عدد المتبرعين بالكشف</div>
        <div class="kpi-value">{{ $donors->count() }} متبرع</div>
      </td>
      <td style="width: 33.3%; background: #ecfdf5;">
        <div class="kpi-title">إجمالي حصيلة التبرعات</div>
        <div class="kpi-value">{{ number_format($totalAmount, 2) }} ج.م</div>
      </td>
      <td style="width: 33.3%;">
        <div class="kpi-title">إجمالي عدد حركات التبرع</div>
        <div class="kpi-value">{{ $totalCount }} تبرع</div>
      </td>
    </tr>
  </table>

  {{-- Donors Data Table --}}
  <table class="data-table">
    <thead>
      <tr>
        <th style="width: 25px;">#</th>
        <th style="width: 80px;">كود المتبرع</th>
        <th style="text-align: right;">اسم المتبرع</th>
        <th style="width: 60px;">النوع</th>
        <th style="width: 85px;">الهاتف</th>
        <th style="width: 70px;">التصنيف</th>
        <th style="width: 90px;">إجمالي التبرعات</th>
        <th style="width: 50px;">المرات</th>
        <th style="width: 50px;">الحالة</th>
      </tr>
    </thead>
    <tbody>
      @forelse($donors as $index => $donor)
        @php
          $st = $donStats->get($donor->id);
          $sum = $st ? $st->total : 0;
          $cnt = $st ? $st->count : 0;
        @endphp
        <tr>
          <td class="text-center">{{ $index + 1 }}</td>
          <td class="text-center" style="font-family: monospace; font-weight: bold; color: #047857;">{{ $donor->code ?? ('DON-'.$donor->id) }}</td>
          <td class="text-right" style="font-weight: bold;">{{ $donor->name }}</td>
          <td class="text-center"><span class="badge {{ $donor->type === 'individual' ? 'badge-info' : 'badge-secondary' }}">{{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }}</span></td>
          <td class="text-center" style="direction: ltr; font-family: monospace;">{{ $donor->phone ?? '—' }}</td>
          <td class="text-center">{{ $donor->classification === 'recurring' ? 'متكرر' : 'مرة واحدة' }}</td>
          <td class="text-center" style="font-weight: bold; color: #047857; font-family: monospace;">{{ number_format($sum, 0) }} ج.م</td>
          <td class="text-center" style="font-weight: bold;">{{ $cnt }}</td>
          <td class="text-center"><span class="badge {{ $donor->active ? 'badge-success' : 'badge-secondary' }}">{{ $donor->active ? 'نشط' : 'غير نشط' }}</span></td>
        </tr>
      @empty
        <tr>
          <td colspan="9" class="text-center" style="padding: 15px; color: #64748b;">لا توجد بيانات متبرعين مطابقة</td>
        </tr>
      @endforelse
    </tbody>
    <tfoot>
      <tr>
        <td colspan="6" class="text-right" style="padding-right: 10px; font-weight: bold;">الإجمالي العام للكشف:</td>
        <td class="text-center" style="color: #047857; font-weight: bold; font-size: 10px;">{{ number_format($totalAmount, 0) }} ج.م</td>
        <td class="text-center" style="font-weight: bold;">{{ $totalCount }}</td>
        <td class="text-center">—</td>
      </tr>
    </tfoot>
  </table>

  {{-- Signatures & Stamp Footer --}}
  <table class="footer-table">
    <tr>
      <td style="width: 33.3%;">
        <div><strong>الموظف المختص / الإعداد</strong></div>
        <div class="signature-line"></div>
        <div style="color: #64748b;">{{ auth()->user()->name ?? 'توقيع المختص' }}</div>
      </td>
      <td style="width: 33.3%;">
        <div><strong>ختم المؤسسة الرسمي</strong></div>
        <div style="margin-top: 10px; font-size: 22px; color: #047857; opacity: 0.5;">[ خـتـم معـتمـد ]</div>
      </td>
      <td style="width: 33.3%;">
        <div><strong>الاعتماد الإداري / المالي</strong></div>
        <div class="signature-line"></div>
        <div style="color: #64748b;">توقيع معتمد</div>
      </td>
    </tr>
  </table>

  <div class="legal-notice">
    تم استخراج هذه الوثيقة آلياً من نظام مؤسسة إنسان الخيرية — يحظر التعديل اليدوي أو الكشط.
  </div>

</body>
</html>
