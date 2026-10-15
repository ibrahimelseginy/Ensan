@php
  $totalCount = $beneficiaries->count();
  $acceptedCount = $beneficiaries->where('status', 'accepted')->count();
  $underReviewCount = $beneficiaries->whereIn('status', ['under_review', 'pending', 'new'])->count();
  $monthlyCount = $beneficiaries->where('assistance_type', 'monthly')->count();
@endphp

<div id="printableBeneficiariesRoster" class="d-none">
  <div class="official-voucher-card a4-paper-sheet bg-white text-dark p-3 p-md-4 rounded-3 position-relative mx-auto"
       style="max-width: 1100px; width: 100%; border: 3px double #059669; font-family: 'Cairo', system-ui, -apple-system, sans-serif; box-sizing: border-box; background: #ffffff !important; color: #0f172a !important; line-height: 1.5;"
       dir="rtl">

    {{-- Watermark in background --}}
    <div class="voucher-watermark position-absolute top-50 start-50 translate-middle text-center"
         style="opacity: 0.035; pointer-events: none; z-index: 0; user-select: none;">
      <img src="{{ asset('images/logo.png') }}" alt="" style="width: 380px; filter: grayscale(100%);">
    </div>

    <div class="position-relative" style="z-index: 1;">
      {{-- Official Institutional Header --}}
      <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-2" style="border-color: #059669 !important;">
        <div class="text-end" style="line-height: 1.4;">
          <div class="small fw-bold text-muted" style="color: #475569 !important;">جمهورية مصر العربية</div>
          <div class="small fw-bold text-muted" style="color: #475569 !important;">وزارة التضامن الاجتماعي</div>
          <div class="fw-bolder fs-6 text-dark" style="color: #0f172a !important;">مؤسسة إنسان الخيرية</div>
          <div class="text-muted" style="font-size: 0.72rem; color: #64748b !important;">مشهرة برقم رسمي للعمل الخيري والتنموي</div>
        </div>

        <div class="text-center px-2">
          <div class="d-inline-block px-4 py-2 rounded-2 border border-2 border-success" style="background-color: #f0fdf4;">
            <h4 class="fw-bolder mb-0 text-success" style="color: #047857 !important;">كشف المستفيدين والحالات</h4>
            <div class="fw-bold font-monospace text-muted mt-1" style="font-size: 0.72rem; letter-spacing: 1px; color: #64748b !important;">OFFICIAL BENEFICIARIES & CASES ROSTER</div>
          </div>
          <div class="mt-1">
            <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-3 py-1">
              كشف معتمد ورسمي
            </span>
          </div>
        </div>

        <div class="text-start" style="line-height: 1.5;">
          <div class="d-inline-block p-2 rounded border border-secondary border-opacity-25 bg-light text-start" style="background: #f8fafc !important;">
            <div class="small text-muted" style="color: #64748b !important;">تاريخ الاستخراج: <span class="fw-bold text-dark font-monospace" style="color: #0f172a !important;">{{ now()->format('Y/m/d') }}</span></div>
            <div class="small text-muted" style="color: #64748b !important;">وقت الاستخراج: <span class="fw-bold text-dark font-monospace" style="color: #0f172a !important;">{{ now()->format('h:i A') }}</span></div>
            <div class="small text-muted" style="font-size: 10px; color: #64748b !important;">المستخرج: {{ auth()->user()?->name ?? 'موظف مختص' }}</div>
          </div>
        </div>
      </div>

      {{-- KPI Summary Boxes --}}
      <div class="row g-2 mb-3 text-center print-kpi-summary">
        <div class="col-3">
          <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 8px; padding: 8px;">
            <div style="font-size: 11px; color: #475569; font-weight: bold;">إجمالي الحالات بالكشف</div>
            <div style="font-size: 17px; font-weight: 900; color: #0f172a;">{{ $totalCount }} حالة</div>
          </div>
        </div>
        <div class="col-3">
          <div style="background: #ecfdf5; border: 1.5px solid #6ee7b7; border-radius: 8px; padding: 8px;">
            <div style="font-size: 11px; color: #065f46; font-weight: bold;">حالات مقبولة ومعتمدة</div>
            <div style="font-size: 17px; font-weight: 900; color: #047857;">{{ $acceptedCount }} حالة</div>
          </div>
        </div>
        <div class="col-3">
          <div style="background: #fffbeb; border: 1.5px solid #fde68a; border-radius: 8px; padding: 8px;">
            <div style="font-size: 11px; color: #92400e; font-weight: bold;">قيد البحث والمراجعة</div>
            <div style="font-size: 17px; font-weight: 900; color: #b45309;">{{ $underReviewCount }} حالة</div>
          </div>
        </div>
        <div class="col-3">
          <div style="background: #eff6ff; border: 1.5px solid #bfdbfe; border-radius: 8px; padding: 8px;">
            <div style="font-size: 11px; color: #1e40af; font-weight: bold;">كفالات شهرية منتظمة</div>
            <div style="font-size: 17px; font-weight: 900; color: #1d4ed8;">{{ $monthlyCount }} كفالة</div>
          </div>
        </div>
      </div>

      {{-- High Contrast Official Table --}}
      <table class="table table-bordered table-sm align-middle mb-3" style="width: 100%; border-color: #cbd5e1; font-size: 11.5px;">
        <thead style="background-color: #047857 !important; color: #ffffff !important; text-align: center;">
          <tr>
            <th style="width: 35px; color: #ffffff !important; background-color: #047857 !important;">#</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 100px;">الكود</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; text-align: right;">اسم المستفيد / ولي الأمر</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 135px;">الرقم القومي / فيزا</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 110px;">الهاتف</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 90px;">الحالة</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 95px;">نوع المساعدة</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 110px;">المشروع</th>
            <th style="color: #ffffff !important; background-color: #047857 !important; width: 85px;">تاريخ القيد</th>
          </tr>
        </thead>
        <tbody>
          @forelse($beneficiaries as $idx => $b)
            @php
              $s = $b->status;
              $cls = match($s) {
                'accepted' => 'success',
                'under_review' => 'warning',
                'rejected' => 'danger',
                'archived_improved' => 'secondary',
                'archived_deceased' => 'dark',
                default => 'info'
              };
              $s_ar = match($s) {
                'pending', 'new' => 'تحت التقديم',
                'under_review' => 'تحت المراجعة',
                'accepted' => 'مقبول ومعتمد',
                'rejected' => 'مرفوض',
                'archived_improved' => 'تحسن / اكتفاء',
                'archived_deceased' => 'متوفى',
                default => $s
              };
              $t = $b->assistance_type;
              $t_ar = match($t) {
                'monthly' => 'كفالة شهرية',
                'one_time' => 'مؤقتة طارئة',
                'in_kind' => 'عينية',
                'service' => 'خدمية',
                'financial' => 'مالية',
                default => $t ?? 'عام'
              };
            @endphp
            <tr style="text-align: center; background-color: {{ $idx % 2 === 0 ? '#ffffff' : '#f8fafc' }};" class="roster-row" data-id="{{ $b->id }}">
              <td style="font-weight: bold; width: 35px; color: #0f172a; padding: 6px 4px;">{{ $idx + 1 }}</td>
              <td style="font-family: monospace; font-weight: 800; color: #047857; padding: 6px 4px;">{{ $b->code ?: ('BEN-' . $b->id) }}</td>
              <td style="text-align: right; font-weight: 700; color: #0f172a; padding: 6px 8px;">
                <div style="font-size: 12.5px; font-weight: 800; color: #0f172a !important;">{{ $b->full_name }}</div>
                @if($b->guardian_name)
                  <div style="font-size: 10.5px; color: #64748b !important;">ولي الأمر: {{ $b->guardian_name }}</div>
                @endif
                @if($b->patient_name)
                  <div style="font-size: 10.5px; color: #0284c7 !important;">المريض: {{ $b->patient_name }}</div>
                @endif
              </td>
              <td style="padding: 6px 4px;">
                @if($b->national_id)
                  <div class="font-monospace fw-bold" style="color: #0f172a !important; font-size: 11px;">{{ $b->national_id }}</div>
                @endif
                @if($b->visa_card_number)
                  <div class="font-monospace small" style="color: #047857 !important; font-size: 10px;">فيزا: {{ $b->visa_card_number }}</div>
                @endif
                @if(!$b->national_id && !$b->visa_card_number)
                  <span style="color: #94a3b8;">—</span>
                @endif
              </td>
              <td dir="ltr" style="font-family: monospace; font-weight: 600; color: #0f172a; padding: 6px 4px;">
                {{ $b->phone ?: '—' }}
              </td>
              <td style="padding: 6px 4px;">
                <span class="badge bg-{{ $cls }} bg-opacity-25 text-{{ $cls === 'warning' ? 'dark' : $cls }} border border-{{ $cls }}" style="font-size: 10.5px;">
                  {{ $s_ar }}
                </span>
              </td>
              <td style="padding: 6px 4px;">
                <span class="badge bg-secondary bg-opacity-15 text-dark border" style="font-size: 10.5px;">
                  {{ $t_ar }}
                </span>
              </td>
              <td style="color: #0f172a; padding: 6px 4px; font-size: 11px;">
                {{ $b->project?->name ?: 'عام' }}
              </td>
              <td style="font-family: monospace; font-size: 10.5px; color: #475569; padding: 6px 4px;">
                {{ optional($b->created_at)->format('Y/m/d') ?? '—' }}
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="9" class="text-center py-4 text-muted">لا توجد بيانات مستفيدين مسجلة بالتصفية المحددة</td>
            </tr>
          @endforelse
        </tbody>
        <tfoot style="background: #f1f5f9; font-weight: bold; border-top: 2px solid #047857;">
          <tr style="text-align: center;">
            <td colspan="3" style="text-align: right; padding-right: 14px; font-size: 12px; font-weight: 800; color: #0f172a;">إجمالي الحالات المشمولة:</td>
            <td colspan="6" style="text-align: start; padding-right: 8px; color: #047857; font-size: 12.5px; font-weight: 900;">
              {{ $totalCount }} حالة (معتمد وموثق)
            </td>
          </tr>
        </tfoot>
      </table>

      {{-- Signatures and Seal Footer --}}
      <div class="row text-center pt-3 pb-2 border-top border-2 border-dark mt-3">
        <div class="col-4">
          <div class="fw-bold small mb-4 text-dark" style="color: #0f172a !important;">إعداد الباحث / مسؤول الحالات</div>
          <div class="border-top border-dark mx-auto pt-1 small text-muted" style="width: 140px; color: #64748b !important;">
            {{ auth()->user()?->name ?? 'التوقيع' }}
          </div>
        </div>
        <div class="col-4">
          <div class="fw-bold small mb-2 text-dark" style="color: #0f172a !important;">ختم المؤسسة الرسمي</div>
          <div class="rounded-circle border border-2 border-success mx-auto d-flex align-items-center justify-content-center"
               style="width: 65px; height: 65px; opacity: 0.35;">
            <i class="bi bi-patch-check text-success fs-3"></i>
          </div>
        </div>
        <div class="col-4">
          <div class="fw-bold small mb-4 text-dark" style="color: #0f172a !important;">الاعتماد الإداري / مدير الرعاية</div>
          <div class="border-top border-dark mx-auto pt-1 small text-muted" style="width: 140px; color: #64748b !important;">
            الاعتماد والختم
          </div>
        </div>
      </div>

      {{-- Legal Notice Footer --}}
      <div class="text-center pt-2 mt-2 border-top" style="font-size: 0.75rem;">
        <div class="text-muted small" style="color: #64748b !important;">
          تم استخراج هذا الكشف آلياً من نظام مؤسسة إنسان الخيرية — سرّي ومخصص للاستخدام الإداري والرقابي فقط.
        </div>
      </div>
    </div>
  </div>
</div>
