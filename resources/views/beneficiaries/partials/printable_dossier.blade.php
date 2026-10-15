@php
  $statusText = match ($beneficiary->status) {
    'pending' => 'تحت التقديم',
    'new' => 'جديد',
    'under_review' => 'تحت المراجعة',
    'accepted' => 'مقبول ومعتمد',
    'rejected' => 'مرفوض',
    'archived_improved' => 'أرشيف — تحسن / اكتفاء',
    'archived_deceased' => 'أرشيف — متوفى',
    default => $beneficiary->status
  };

  $typeText = match ($beneficiary->assistance_type) {
    'financial' => 'مساعدة مالية',
    'monthly' => 'كفالة شهرية منتظمة',
    'one_time' => 'مساعدة مؤقتة / طارئة',
    'in_kind' => 'مساعدة عينية',
    'service' => 'مساعدة خدمية / علاجية',
    default => $beneficiary->assistance_type ?? 'عام'
  };

  $activeMembers = $beneficiary->familyMembers ? $beneficiary->familyMembers->where('active', true) : collect();
@endphp

<div id="printableBeneficiaryDossier" class="d-none">
  <div class="official-voucher-card a4-paper-sheet bg-white text-dark p-4 p-md-5 rounded-3 position-relative mx-auto"
       style="max-width: 860px; width: 100%; border: 3px double #059669; font-family: 'Cairo', system-ui, -apple-system, sans-serif; box-sizing: border-box; background: #ffffff !important; color: #0f172a !important; line-height: 1.6;"
       dir="rtl">

    {{-- Watermark in background --}}
    <div class="voucher-watermark position-absolute top-50 start-50 translate-middle text-center"
         style="opacity: 0.04; pointer-events: none; z-index: 0; user-select: none;">
      <img src="{{ asset('images/logo.png') }}" alt="" style="width: 320px; filter: grayscale(100%);">
    </div>

    <div class="position-relative" style="z-index: 1;">
      {{-- Official Header --}}
      <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-2" style="border-color: #059669 !important;">
        {{-- Right Official Details --}}
        <div class="text-end" style="line-height: 1.5;">
          <div class="small fw-bold text-muted" style="color: #475569 !important;">جمهورية مصر العربية</div>
          <div class="small fw-bold text-muted" style="color: #475569 !important;">وزارة التضامن الاجتماعي</div>
          <div class="fw-bolder fs-6 text-dark" style="color: #0f172a !important;">مؤسسة إنسان الخيرية</div>
          <div class="text-muted" style="font-size: 0.72rem; color: #64748b !important;">مشهرة برقم رسمي للعمل الخيري والتنموي</div>
        </div>

        {{-- Center Title Box --}}
        <div class="text-center px-2">
          <div class="d-inline-block px-4 py-2 rounded-2 border border-2 border-success" style="background-color: #f0fdf4;">
            <h4 class="fw-bolder mb-0 text-success" style="color: #047857 !important;">استمارة بحث وملف مستفيد</h4>
            <div class="fw-bold font-monospace text-muted mt-1" style="font-size: 0.72rem; letter-spacing: 1px; color: #64748b !important;">BENEFICIARY DOSSIER & SOCIAL ASSESSMENT</div>
          </div>
          <div class="mt-1">
            <span class="badge text-white px-3 py-1 shadow-sm" style="background-color: #059669 !important; color: #ffffff !important; font-weight: 700;">
              {{ $statusText }}
            </span>
          </div>
        </div>

        {{-- Left Document Details --}}
        <div class="text-start" style="line-height: 1.6;">
          <div class="d-inline-block p-2 rounded border border-secondary border-opacity-25 bg-light text-start" style="background: #f8fafc !important;">
            <div class="small text-muted" style="color: #64748b !important;">كود المستفيد:</div>
            <div class="fw-bolder font-monospace text-success fs-5" style="color: #047857 !important;">{{ $beneficiary->code ?: ('BEN-' . $beneficiary->id) }}</div>
            <div class="small text-muted mt-1" style="color: #64748b !important;">تاريخ الاستخراج: <span class="fw-bold text-dark font-monospace" style="color: #0f172a !important;">{{ now()->format('Y/m/d') }}</span></div>
            <div class="small text-muted" style="font-size: 10px; color: #64748b !important;">المستخرج: {{ auth()->user()?->name ?? 'موظف مختص' }}</div>
          </div>
        </div>
      </div>

      {{-- Beneficiary Hero Card --}}
      <div class="p-3 mb-3 rounded-3 border border-2" style="background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border-color: #a7f3d0 !important;">
        <div class="d-flex justify-content-between align-items-center">
          <div>
            <div class="small fw-bold text-muted mb-1" style="color: #065f46 !important;">الاسم الرباعي للمستفيد (رب الأسرة / الحالة):</div>
            <div class="fs-4 fw-bolder text-dark" style="color: #0f172a !important;">{{ $beneficiary->full_name }}</div>
          </div>
          <div class="text-start">
            <div class="small fw-bold text-muted mb-1" style="color: #065f46 !important;">نوع المساعدة المعتمدة:</div>
            <span class="badge bg-success text-white px-3 py-1.5 fs-6">{{ $typeText }}</span>
          </div>
        </div>
      </div>

      {{-- Section 1: Basic & Social Information Table --}}
      <div class="mb-3">
        <div class="fw-bold text-dark pb-1 mb-2 border-bottom border-1 border-secondary border-opacity-25 d-flex align-items-center gap-2" style="font-size: 13.5px; color: #047857 !important;">
          <i class="bi bi-person-vcard text-success"></i>
          <span>أولاً: البيانات الشخصية والتعريفية</span>
        </div>
        <table class="table table-bordered table-sm mb-0" style="border-color: #cbd5e1; font-size: 12.5px;">
          <tbody>
            <tr>
              <td class="bg-light fw-bold text-muted" style="width: 18%; background: #f8fafc !important; color: #475569 !important;">الرقم القومي:</td>
              <td class="fw-bold font-monospace" style="width: 32%; color: #0f172a !important;">{{ $beneficiary->national_id ?: 'غير مسجل' }}</td>
              <td class="bg-light fw-bold text-muted" style="width: 18%; background: #f8fafc !important; color: #475569 !important;">رقم الهاتف:</td>
              <td class="fw-bold font-monospace" style="width: 32%; color: #0f172a !important;">{{ $beneficiary->phone ?: '—' }}</td>
            </tr>
            <tr>
              <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">الهاتف الإضافي:</td>
              <td class="font-monospace" style="color: #0f172a !important;">{{ $beneficiary->backup_phone ?: '—' }}</td>
              <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">رقم فيزا الصرف:</td>
              <td class="font-monospace fw-bold" style="color: #047857 !important;">{{ $beneficiary->visa_card_number ?: 'لا يوجد' }}</td>
            </tr>
            <tr>
              <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">المشروع التابع له:</td>
              <td style="color: #0f172a !important;">{{ $beneficiary->project?->name ?: 'عام / غير مقيد' }}</td>
              <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">حالة الملف:</td>
              <td><span class="badge bg-secondary bg-opacity-25 text-dark border">{{ $statusText }}</span></td>
            </tr>
            <tr>
              <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">العنوان بالتفصيل:</td>
              <td colspan="3" class="fw-medium" style="color: #0f172a !important;">{{ $beneficiary->address ?: 'لم يحدد' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      {{-- Section 2: Family Members (أفراد الأسرة والمكفولين) --}}
      @if($activeMembers->isNotEmpty())
      <div class="mb-3">
        <div class="fw-bold text-dark pb-1 mb-2 border-bottom border-1 border-secondary border-opacity-25 d-flex justify-content-between align-items-center" style="font-size: 13.5px; color: #047857 !important;">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people text-success"></i>
            <span>ثانياً: بيانات أفراد الأسرة والمكفولين ({{ $activeMembers->count() }} أفراد)</span>
          </div>
          <small class="text-muted" style="font-size: 11px;">ملفات كفالة مستقلة</small>
        </div>
        <table class="table table-bordered table-sm align-middle mb-0" style="border-color: #cbd5e1; font-size: 11.5px; text-align: center;">
          <thead style="background: #f1f5f9; color: #0f172a;">
            <tr>
              <th style="width: 35px;">#</th>
              <th style="width: 90px;">الكود</th>
              <th style="text-align: right;">الاسم الكامل</th>
              <th style="width: 85px;">القرابة</th>
              <th style="width: 75px;">العمر</th>
              <th style="width: 100px;">المستوى التعليمي</th>
              <th style="width: 95px;">مبلغ الكفالة</th>
              <th style="width: 120px;">الكافل المرتبط</th>
            </tr>
          </thead>
          <tbody>
            @foreach($activeMembers as $mIdx => $member)
              <tr>
                <td class="fw-bold">{{ $mIdx + 1 }}</td>
                <td class="font-monospace fw-bold text-success">{{ $member->code ?: '—' }}</td>
                <td style="text-align: right; font-weight: 700; color: #0f172a;">
                  {{ $member->full_name }}
                  @if($member->is_patient)
                    <span class="badge bg-danger bg-opacity-25 text-danger border border-danger ms-1" style="font-size: 9px;">حالة مرضية</span>
                  @endif
                </td>
                <td>{{ $member->relationship_label }}</td>
                <td>{{ $member->birth_date ? $member->birth_date->age . ' سنة' : ($member->age ? $member->age . ' سنة' : '—') }}</td>
                <td>{{ $member->education_level ?: '—' }}</td>
                <td class="fw-bold text-success">{{ $member->sponsorship_amount ? number_format((float)$member->sponsorship_amount, 2) . ' ج' : '—' }}</td>
                <td class="small">
                  @forelse($member->sponsors as $sp)
                    <div class="fw-bold text-dark">{{ $sp->name }}</div>
                  @empty
                    <span class="text-muted">—</span>
                  @endforelse
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      @endif

      {{-- Section 3: Donations / Disbursements History (سجل المساعدات والتبرعات المصروفة / الفواتير) --}}
      @if(isset($donations) && $donations->count() > 0)
      <div class="mb-3">
        <div class="fw-bold text-dark pb-1 mb-2 border-bottom border-1 border-secondary border-opacity-25 d-flex justify-content-between align-items-center" style="font-size: 13.5px; color: #047857 !important;">
          <div class="d-flex align-items-center gap-2">
            <i class="bi bi-receipt text-success"></i>
            <span>ثالثاً: سجل المساعدات والتبرعات المنصرفة للحالة (الفواتير والإيصالات)</span>
          </div>
          <small class="text-muted" style="font-size: 11px;">إجمالي الحركات: {{ $donations->count() }}</small>
        </div>
        <table class="table table-bordered table-sm align-middle mb-0" style="border-color: #cbd5e1; font-size: 11.5px; text-align: center;">
          <thead style="background: #f1f5f9; color: #0f172a;">
            <tr>
              <th style="width: 35px;">#</th>
              <th style="width: 90px;">رقم السند/الإيصال</th>
              <th style="width: 90px;">تاريخ الصرف</th>
              <th style="text-align: right;">المتبرع / المصدر</th>
              <th style="width: 90px;">النوع</th>
              <th style="width: 120px;">القيمة المصروفة</th>
            </tr>
          </thead>
          <tbody>
            @php
              $totalAssistanceVal = 0;
            @endphp
            @foreach($donations->take(10) as $dIdx => $d)
              @php
                $curVal = $d->type === 'cash' ? (float)$d->amount : (float)$d->estimated_value;
                $totalAssistanceVal += $curVal;
              @endphp
              <tr>
                <td class="fw-bold">{{ $dIdx + 1 }}</td>
                <td class="font-monospace fw-bold">{{ $d->receipt_number ?: ('REC-' . $d->id) }}</td>
                <td class="font-monospace">{{ optional($d->received_at)->format('Y/m/d') ?? '—' }}</td>
                <td style="text-align: right; color: #0f172a;">{{ $d->donor?->name ?? 'فاعل خير / دعم عام' }}</td>
                <td><span class="badge text-white" style="background-color: {{ $d->type === 'cash' ? '#059669' : '#0284c7' }} !important; color: #ffffff !important; font-weight: 700;">{{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}</span></td>
                <td class="fw-bold text-success font-monospace">{{ number_format($curVal, 2) }} {{ $d->currency ?: 'ج.م' }}</td>
              </tr>
            @endforeach
          </tbody>
          <tfoot style="background: #f8fafc; font-weight: 800;">
            <tr>
              <td colspan="5" style="text-align: right; color: #0f172a;">إجمالي المساعدات المسجلة:</td>
              <td class="text-success font-monospace fs-6">{{ number_format($totalAssistanceVal, 2) }} ج.م</td>
            </tr>
          </tfoot>
        </table>
      </div>
      @endif

      {{-- Section 4: Social Research Notes (تقرير البحث الميداني والملاحظات) --}}
      @if(!empty($beneficiary->notes))
      <div class="p-2 mb-3 rounded-2 border" style="background-color: #f8fafc; border-color: #e2e8f0 !important; font-size: 11.5px;">
        <div class="fw-bold text-dark mb-1 d-flex align-items-center gap-1">
          <i class="bi bi-card-text text-success"></i>
          <span>ملاحظات البحث الاجتماعي وتقرير الحالة:</span>
        </div>
        <div class="text-dark" style="color: #334155 !important;">{{ $beneficiary->notes }}</div>
      </div>
      @endif

      {{-- Official Signatures & Stamp --}}
      <div class="row text-center pt-3 pb-2 border-top border-2 border-dark mt-3">
        <div class="col-4">
          <div class="fw-bold small mb-4 text-dark" style="color: #0f172a !important;">الباحث الاجتماعي المختص</div>
          <div class="border-top border-dark mx-auto pt-1 small text-muted" style="width: 130px; color: #64748b !important;">
            {{ auth()->user()?->name ?? 'التوقيع والاعتماد' }}
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
          <div class="fw-bold small mb-4 text-dark" style="color: #0f172a !important;">مدير إدارة الرعاية الاجتماعية</div>
          <div class="border-top border-dark mx-auto pt-1 small text-muted" style="width: 130px; color: #64748b !important;">
            الاعتماد الإداري والمالي
          </div>
        </div>
      </div>

      {{-- Quranic Verse & Footer Note --}}
      <div class="text-center pt-2 mt-2 border-top" style="font-size: 0.75rem;">
        <div class="fw-bold text-muted mb-1 font-monospace" style="color: #047857 !important;">
          ﴿ وَمَا تُنفِقُوا مِنْ خَيْرٍ يُوَفَّ إِلَيْكُمْ وَأَنْتُمْ لَا تُظْلَمُونَ ﴾
        </div>
        <div class="text-muted small" style="color: #64748b !important;">
          هذه الاستمارة وثيقة بحث ورعاية رسمية معتمدة من مؤسسة إنسان الخيرية — سرية ومخصصة للاستخدام الإداري والرقابي.
        </div>
      </div>
    </div>
  </div>
</div>
