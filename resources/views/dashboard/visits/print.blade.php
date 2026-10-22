<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $visit->has_cancer_patient ? 'استمارة بحث مريض أورام' : 'استمارة بحث اجتماعي' }} — {{ $visit->patient_name ?: $visit->beneficiary?->full_name }}</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    @page {
      size: A4 portrait;
      margin: 10mm 12mm 10mm 12mm;
    }
    * {
      box-sizing: border-box;
      -webkit-print-color-adjust: exact !important;
      print-color-adjust: exact !important;
    }
    body {
      font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #f1f5f9;
      color: #000000;
      margin: 0;
      padding: 20px;
      direction: rtl;
      font-size: 13px;
      line-height: 1.35;
    }
    .no-print-toolbar {
      max-width: 210mm;
      margin: 0 auto 15px auto;
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #ffffff;
      padding: 12px 20px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    }
    .print-btn {
      background: #059669;
      color: #ffffff;
      border: none;
      padding: 8px 20px;
      border-radius: 8px;
      font-weight: 700;
      font-size: 14px;
      cursor: pointer;
      display: inline-flex;
      align-items: center;
      gap: 6px;
    }
    .print-btn:hover {
      background: #047857;
    }
    .close-btn {
      background: #64748b;
      color: #ffffff;
      border: none;
      padding: 8px 18px;
      border-radius: 8px;
      font-weight: 600;
      text-decoration: none;
      font-size: 13px;
    }
    .sheet-page {
      width: 210mm;
      min-height: 297mm;
      padding: 10mm 12mm;
      margin: 0 auto 20px auto;
      background: #ffffff;
      box-shadow: 0 4px 25px rgba(0,0,0,0.08);
      position: relative;
      page-break-after: always;
      overflow: hidden;
    }
    .sheet-page:last-child {
      page-break-after: auto;
    }
    .watermark {
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 420px;
      opacity: 0.08;
      pointer-events: none;
      z-index: 0;
    }
    .content-wrapper {
      position: relative;
      z-index: 1;
    }
    .header-logo-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      border-bottom: 2px solid #000000;
      padding-bottom: 6px;
      margin-bottom: 8px;
    }
    .header-org-title {
      font-size: 18px;
      font-weight: 800;
      color: #047857;
    }
    .header-org-sub {
      font-size: 12px;
      font-weight: 700;
      color: #334155;
    }
    .header-ensan-logo {
      font-size: 26px;
      font-weight: 900;
      color: #059669;
      display: flex;
      align-items: center;
      gap: 6px;
    }
    .sheet-main-title {
      text-align: center;
      font-size: 19px;
      font-weight: 800;
      margin: 6px 0 2px 0;
      color: #000000;
    }
    .sheet-date-row {
      text-align: center;
      font-size: 14px;
      font-weight: 700;
      margin-bottom: 10px;
    }
    table.official-table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 8px;
      font-size: 12.5px;
    }
    table.official-table th,
    table.official-table td {
      border: 1.5px solid #000000;
      padding: 5px 7px;
      vertical-align: middle;
    }
    table.official-table th {
      background-color: #f8fafc;
      font-weight: 800;
      text-align: center;
    }
    .notice-yellow {
      background-color: #ffff00 !important;
      color: #000000 !important;
      font-weight: 800;
      padding: 2px 6px;
      display: inline-block;
    }
    .notice-green-bar {
      background-color: #00ff00 !important;
      color: #000000 !important;
      font-weight: 900;
      text-align: center;
      padding: 5px 8px;
      border: 1.5px solid #000000;
      margin: 8px 0;
      font-size: 13px;
    }
    .desc-box {
      border: 1.5px solid #000000;
      padding: 8px 10px;
      margin-bottom: 8px;
      min-height: 70px;
      font-size: 12.5px;
      line-height: 1.5;
    }
    .branches-bar {
      border: 1.5px solid #000000;
      background: #ffffff;
      padding: 6px 10px;
      margin-top: 10px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      font-size: 11.5px;
      font-weight: 800;
    }
    .branches-bar .badge-branch {
      background: #059669;
      color: #ffffff;
      padding: 2px 7px;
      border-radius: 4px;
      margin-left: 4px;
    }
    @media print {
      body {
        background: transparent !important;
        padding: 0 !important;
      }
      .no-print-toolbar {
        display: none !important;
      }
      .sheet-page {
        box-shadow: none !important;
        margin: 0 !important;
        width: 100% !important;
        min-height: 100vh !important;
        padding: 6mm 8mm !important;
      }
    }
  </style>
</head>
<body>

  <!-- Toolbar (Screen only) -->
  <div class="no-print-toolbar">
    <div>
      <span class="fw-bold" style="font-size: 15px; font-weight: 800;">
        <i class="bi bi-printer text-success"></i> طباعة {{ $visit->has_cancer_patient ? 'استمارة بحث مريض أورام' : 'استمارة بحث اجتماعي' }}
      </span>
      <span class="text-muted" style="margin-right: 10px;">(#{{ $visit->id }})</span>
    </div>
    <div style="display: flex; gap: 8px;">
      <button onclick="window.print()" class="print-btn">
        <i class="bi bi-printer-fill"></i> طباعة فورية (Print)
      </button>
      <a href="{{ route('visits.edit', $visit) }}" class="close-btn" style="background: #eab308; color: #000;">
        <i class="bi bi-pencil-square"></i> تعديل البيانات
      </a>
      <a href="{{ route('visits.show', $visit) }}" class="close-btn">
        <i class="bi bi-arrow-right"></i> رجوع
      </a>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PAGE 1: بيانات الحالة والأسرة ومصادر الدخل -->
  <!-- ========================================== -->
  <div class="sheet-page">
    <svg class="watermark" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M100 30 C70 0, 10 30, 30 90 C50 140, 100 180, 100 180 C100 180, 150 140, 170 90 C190 30, 130 0, 100 30 Z" stroke="#059669" stroke-width="12"/>
    </svg>

    <div class="content-wrapper">
      <!-- Header -->
      <div class="header-logo-row">
        <div>
          <div class="header-org-title">مؤسسة إنسان الخيرية</div>
          <div class="header-org-sub">المشهرة برقم 63 لسنة 2017</div>
        </div>
        <div class="header-ensan-logo">
          <span>إنسان</span>
          <span style="font-size: 14px; font-weight: 500; color: #64748b;">ensan</span>
        </div>
      </div>

      <!-- Title -->
      <div class="sheet-main-title">
        {{ $visit->has_cancer_patient ? 'استمارة بحث مريض أورام' : 'استمارة بحث اجتماعي' }}
      </div>
      <div class="sheet-date-row">
        تاريخ البحث : {{ $visit->visit_date ? $visit->visit_date->format('Y / m / d') : date('Y / m / d') }}
      </div>

      <!-- General Info Table (بيانات الحالة / بيانات الباحث) -->
      <table class="official-table">
        <thead>
          <tr>
            <th style="width: 50%;">بيانات الحالة</th>
            <th style="width: 50%;">بيانات الباحث</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>
              <strong>اسم المريض كاملاً :</strong> {{ $visit->patient_name ?: ($visit->beneficiary?->full_name ?: '—') }}
            </td>
            <td>
              <strong>اسم الباحث :</strong> {{ $visit->researcher?->name ?: '—' }}
            </td>
          </tr>
          <tr>
            <td>
              <strong>اسم ولي الأمر :</strong> {{ $visit->guardian_name ?: ($visit->beneficiary?->full_name ?: '—') }}
            </td>
            <td>
              <strong>رقم الهاتف :</strong> <span style="direction: ltr; display: inline-block;">{{ $visit->researcher_phone ?: ($visit->researcher?->phone ?: '—') }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <strong>الرقم القومي :</strong> <span style="font-family: monospace; font-size: 13px;">{{ $visit->national_id ?: ($visit->beneficiary?->national_id ?: '—') }}</span>
            </td>
            <td rowspan="4" style="vertical-align: top; background: #fff;">
              <span class="notice-yellow">رأي الباحث :</span>
              <div style="margin-top: 4px; font-size: 12px; line-height: 1.4;">
                {{ $visit->researcher_opinion ?: 'لا توجد ملاحظات مدونة' }}
              </div>
            </td>
          </tr>
          <tr>
            <td>
              <strong>رقم الهاتف :</strong> <span style="direction: ltr; display: inline-block;">{{ $visit->phone ?: ($visit->beneficiary?->phone ?: '—') }}</span>
            </td>
          </tr>
          <tr>
            <td>
              <strong>ملكية البيت :</strong> {{ $visit->home_ownership ?: '—' }}
            </td>
          </tr>
          <tr>
            <td>
              <strong>العنوان بالتفصيل :</strong> {{ $visit->address ?: ($visit->beneficiary?->address ?: '—') }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Notice Bar -->
      <div class="notice-green-bar">
        ( ملحوظة : يتم تصوير صور البطايق و البيت )
      </div>

      <!-- Family Members Table -->
      <table class="official-table" style="text-align: center;">
        <thead>
          <tr>
            <th style="width: 25%;">الاسم</th>
            <th style="width: 15%;">صلة القرابة</th>
            <th style="width: 10%;">السن</th>
            <th style="width: 15%;">الدخل</th>
            <th style="width: 15%;">العمل</th>
            <th style="width: 20%;">ملاحظات</th>
          </tr>
        </thead>
        <tbody>
          @php
            $fMembers = $visit->family_members ?? [];
            $rowsCount = max(5, count($fMembers));
          @endphp
          @for($i = 0; $i < $rowsCount; $i++)
            @php $m = $fMembers[$i] ?? null; @endphp
            <tr>
              <td style="text-align: right;">{{ $m['name'] ?? '' }}</td>
              <td>{{ $m['relation'] ?? '' }}</td>
              <td>{{ $m['age'] ?? '' }}</td>
              <td>{{ $m['income'] ?? '' }}</td>
              <td>{{ $m['job'] ?? '' }}</td>
              <td style="text-align: right;">{{ $m['notes'] ?? '' }}</td>
            </tr>
          @endfor
        </tbody>
      </table>

      <!-- Income & Expenses Table -->
      <table class="official-table">
        <thead>
          <tr>
            <th style="width: 35%;">مصادر الدخل</th>
            <th style="width: 15%;">المبلغ</th>
            <th style="width: 35%;">بنود الصرف ( المصروفات )</th>
            <th style="width: 15%;">المبلغ</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>وظيفة ( في حالة العمل )</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->income_job > 0 ? number_format($visit->income_job, 2) : '—' }}</td>
            <td>إيجار</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->expense_rent > 0 ? number_format($visit->expense_rent, 2) : '—' }}</td>
          </tr>
          <tr>
            <td>عمل يومي ( حرفي / فني / عامل / فاعل )</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->income_daily_work > 0 ? number_format($visit->income_daily_work, 2) : '—' }}</td>
            <td>مصاريف الدروس</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->expense_tuition > 0 ? number_format($visit->expense_tuition, 2) : '—' }}</td>
          </tr>
          <tr>
            <td>معاشات تأمينات ( كبار السن / التقاعد )</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->income_pension > 0 ? number_format($visit->income_pension, 2) : '—' }}</td>
            <td>متوسط تكلفة الطعام الاسبوعي</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->expense_food_weekly > 0 ? number_format($visit->expense_food_weekly, 2) : '—' }}</td>
          </tr>
          <tr>
            <td>الشئون الاجتماعية ( طلاق / عجز طبي / ترمل / تكافل )</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->income_social_affairs > 0 ? number_format($visit->income_social_affairs, 2) : '—' }}</td>
            <td>المواصلات اليومية</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->expense_transportation > 0 ? number_format($visit->expense_transportation, 2) : '—' }}</td>
          </tr>
          <tr>
            <td>الأقارب / الجيران / جمعيات شرعية / الجمعيات الخيرية</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->income_charities > 0 ? number_format($visit->income_charities, 2) : '—' }}</td>
            <td>فاتورة الكهرباء / المياة / الغاز</td>
            <td style="text-align: center; font-family: monospace;">{{ $visit->expense_utilities > 0 ? number_format($visit->expense_utilities, 2) : '—' }}</td>
          </tr>
          <tr style="font-weight: 800; background: #f8fafc;">
            <td>إجمالي الدخل</td>
            <td style="text-align: center; font-family: monospace; font-size: 13px;">{{ number_format($visit->total_income, 2) }}</td>
            <td>إجمالي المصروفات</td>
            <td style="text-align: center; font-family: monospace; font-size: 13px;">{{ number_format($visit->total_expenses, 2) }}</td>
          </tr>
        </tbody>
      </table>

      <!-- Bottom Branch Footer -->
      <div class="branches-bar">
        <div>
          <span class="badge-branch">الفرع الرئيسي</span>
          <span>كفرالشيخ - تقسيم 2 ش تونس المتفرع من ش مخزن الزيت بجوار كازيون 01006090616</span>
        </div>
        <div>
          <span class="badge-branch">مكتب الغربية</span>
          <span>طنطا - شارع مسجد الدعوة المتفرع من شارع الرفاعي أمام لاند سكيب 01026017236</span>
        </div>
      </div>
    </div>
  </div>

  <!-- ========================================== -->
  <!-- PAGE 2: الديون ووصف الحالة (وبحث الأورام إن وجد) -->
  <!-- ========================================== -->
  <div class="sheet-page">
    <svg class="watermark" viewBox="0 0 200 200" fill="none" xmlns="http://www.w3.org/2000/svg">
      <path d="M100 30 C70 0, 10 30, 30 90 C50 140, 100 180, 100 180 C100 180, 150 140, 170 90 C190 30, 130 0, 100 30 Z" stroke="#059669" stroke-width="12"/>
    </svg>

    <div class="content-wrapper">
      <!-- Header -->
      <div class="header-logo-row">
        <div>
          <div class="header-org-title">مؤسسة إنسان الخيرية</div>
          <div class="header-org-sub">المشهرة برقم 63 لسنة 2017</div>
        </div>
        <div class="header-ensan-logo">
          <span>إنسان</span>
          <span style="font-size: 14px; font-weight: 500; color: #64748b;">ensan</span>
        </div>
      </div>

      <!-- Debts Table -->
      <table class="official-table" style="text-align: center;">
        <thead>
          <tr>
            <th style="width: 25%;">إجمالي الديون</th>
            <th style="width: 25%;">ما تم سداده</th>
            <th style="width: 25%;">المتبقي</th>
            <th style="width: 25%;">القسط الشهري</th>
          </tr>
        </thead>
        <tbody>
          <tr style="font-family: monospace; font-size: 13px;">
            <td>{{ number_format($visit->total_debts, 2) }}</td>
            <td>{{ number_format($visit->debt_repaid, 2) }}</td>
            <td style="font-weight: 800; color: #dc2626;">{{ number_format($visit->debt_remaining, 2) }}</td>
            <td>{{ number_format($visit->debt_monthly_installment, 2) }}</td>
          </tr>
          <tr>
            <td colspan="4" style="text-align: right;">
              <strong>سبب الدين :</strong> {{ $visit->debt_reason ?: '—' }}
            </td>
          </tr>
          <tr>
            <td colspan="2" style="text-align: right;">
              <strong>دليل الحالة للمؤسسة :</strong> {{ $visit->case_guide ?: '—' }}
            </td>
            <td colspan="2" style="text-align: right;">
              <strong>احتياج الحالة من التقديم :</strong> {{ $visit->case_needs ?: '—' }}
            </td>
          </tr>
        </tbody>
      </table>

      <!-- Social and Home Condition Box -->
      <div class="desc-box">
        <span class="notice-yellow">وصف الحالة الاجتماعية للأسرة ، و ظروف الأسرة ، و وضع البيت ( محتاج سقف / ترميم / بنى ) :</span>
        <div style="margin-top: 6px;">
          {{ $visit->home_and_social_condition ?: 'لا توجد ملاحظات مدونة' }}
        </div>
      </div>

      @if($visit->has_cancer_patient)
        <!-- ============================================== -->
        <!-- قسم مريض الأورام (يظهر في الطباعة فقط إذا كان أورام) -->
        <!-- ============================================== -->
        <div class="notice-green-bar">
          ( ملحوظة : يتم تصوير كارت المتابعة و جميع التقارير الطبية و الروشتات المذكورة ، و التركيز على احتاجها للسلة الغذائية و الدعم النفسي / الزيارة )
        </div>

        <table class="official-table">
          <tbody>
            <tr>
              <td style="width: 50%;">
                <strong>اسم المستشفى المتلقى بها العلاج :</strong> {{ $visit->hospital_name ?: '—' }}
              </td>
              <td style="width: 50%;">
                <strong>مكان الورم بالجسم :</strong> {{ $visit->tumor_location ?: '—' }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>تاريخ اكتشاف المرض :</strong> {{ $visit->disease_discovery_date ?: '—' }}
              </td>
              <td>
                <strong>مدة اكتشاف المرض :</strong> {{ $visit->disease_duration ?: '—' }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>عدد جلسات الكيماوي المتلقاه :</strong> {{ $visit->chemo_sessions_taken }}
              </td>
              <td>
                <strong>عدد الجلسات المتبقية :</strong> {{ $visit->chemo_sessions_remaining }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>عدد جلسات الإشعاع المتلقاه :</strong> {{ $visit->radiation_sessions_taken }}
              </td>
              <td>
                <strong>عدد الجلسات المتبقية :</strong> {{ $visit->radiation_sessions_remaining }}
              </td>
            </tr>
            <tr>
              <td>
                <strong>عدد أيام تردد المريض على المستشفى :</strong> {{ $visit->hospital_visit_frequency ?: '—' }}
              </td>
              <td>
                <strong>الحالة النفسية للمريض :</strong> {{ $visit->patient_psychological_state ?: '—' }}
              </td>
            </tr>
          </tbody>
        </table>

        <!-- قبل اكتشاف الورم -->
        <div class="desc-box" style="min-height: 55px;">
          <span class="notice-yellow">قبل اكتشاف الورم :</span>
          <div style="margin-top: 4px;">
            {{ $visit->before_tumor_notes ?: '—' }}
          </div>
        </div>

        <!-- بعد اكتشاف الورم -->
        <div class="desc-box" style="min-height: 55px;">
          <span class="notice-yellow">بعد اكتشاف الورم :</span>
          <div style="margin-top: 4px;">
            {{ $visit->after_tumor_notes ?: '—' }}
          </div>
        </div>

        <!-- البروتوكول الحالي -->
        <div class="desc-box" style="min-height: 60px;">
          <span class="notice-yellow">البروتوكول الحالي و خطة العلاج :</span>
          <div style="margin-top: 4px;">
            {{ $visit->treatment_protocol ?: '—' }}
          </div>
        </div>
      @else
        <!-- في حالة البحث الاجتماعي العادي: مساحة ملاحظات وتوصيات ختامية -->
        <div class="desc-box" style="min-height: 180px;">
          <span class="notice-yellow">القرار النهائي وتوصيات لجنة المساعدات والبحث الاجتماعي :</span>
          <div style="margin-top: 10px; line-height: 1.8;">
            بناءً على المعاينة الميدانية وفحص مصادر الدخل والمصروفات، تم تقييم الحالة بأنها : <strong>{{ $visit->recommendation_label }}</strong>.
            <br>
            المساعدات المقترحة : {{ $visit->case_needs ?: 'سلة غذائية / كفالة شهرية / مساعدة عينية' }}.
          </div>
        </div>
      @endif

      <!-- التوقيعات -->
      <table style="width: 100%; margin-top: 15px; text-align: center; font-weight: 800; font-size: 13px;">
        <tr>
          <td style="width: 33%;">
            الباحث الميداني
            <br><br>
            ...........................
          </td>
          <td style="width: 33%;">
            مدير المشروع
            <br><br>
            ...........................
          </td>
          <td style="width: 33%;">
            مدير المؤسسة
            <br><br>
            ...........................
          </td>
        </tr>
      </table>

      <!-- Bottom Branch Footer -->
      <div class="branches-bar" style="margin-top: 25px;">
        <div>
          <span class="badge-branch">الفرع الرئيسي</span>
          <span>كفرالشيخ - تقسيم 2 ش تونس المتفرع من ش مخزن الزيت بجوار كازيون 01006090616</span>
        </div>
        <div>
          <span class="badge-branch">مكتب الغربية</span>
          <span>طنطا - شارع مسجد الدعوة المتفرع من شارع الرفاعي أمام لاند سكيب 01026017236</span>
        </div>
      </div>
    </div>
  </div>

</body>
</html>
