@php
  $isGuestHouse = $beneficiary->is_guest_house_case 
      || !empty($beneficiary->guest_house_id) 
      || str_starts_with((string)$beneficiary->code, 'G-')
      || ($beneficiary->patientProfile !== null);

  $latestStay = $beneficiary->guestHouseStays ? $beneficiary->guestHouseStays->sortByDesc('id')->first() : null;
  $bed = $latestStay?->bed;
  $wing = $bed?->wing;
  $wingName = $wing?->name ?? ($beneficiary->guestHouse?->name ?? 'دار ضيافة إنسان');
  $bedLabel = $bed ? (($bed->is_mattress ? 'مرتبة ' : 'سرير ') . $bed->number) : '—';
  $arrivalDate = $latestStay?->arrival_date ?? $latestStay?->admitted_at ?? $beneficiary->created_at;
  $expectedDays = $latestStay?->expected_days;
  $departureDate = $arrivalDate && $expectedDays 
      ? \Carbon\Carbon::parse($arrivalDate)->addDays((int)$expectedDays)->format('Y/m/d')
      : ($arrivalDate ? \Carbon\Carbon::parse($arrivalDate)->addDays(1)->format('Y/m/d') : null);
  $patientProfile = $beneficiary->patientProfile;

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
  <div class="official-voucher-card bg-white text-dark p-4 p-md-5 position-relative mx-auto"
       style="max-width: 860px; width: 100%; min-height: auto !important; height: auto !important; border: 2px solid {{ $isGuestHouse ? '#1e293b' : '#059669' }}; outline: none; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif !important; box-sizing: border-box; background: #ffffff !important; color: #0f172a !important; line-height: 1.6; margin-bottom: 0 !important;"
       dir="rtl">

    {{-- Embedded Scoped Styles: Guarantee pure white paper & dark text in any theme --}}
    <style>
      #printableBeneficiaryDossier,
      #printableBeneficiaryDossier *,
      body.theme-dark #printableBeneficiaryDossier,
      body.theme-dark #printableBeneficiaryDossier *,
      html.theme-dark #printableBeneficiaryDossier,
      html.theme-dark #printableBeneficiaryDossier *,
      [data-bs-theme="dark"] #printableBeneficiaryDossier,
      [data-bs-theme="dark"] #printableBeneficiaryDossier * {
          color-scheme: light !important;
          box-sizing: border-box !important;
          font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif !important;
      }
      #printableBeneficiaryDossier,
      #printableBeneficiaryDossier .official-voucher-card {
          background: #ffffff !important;
          color: #0f172a !important;
          min-height: auto !important;
          height: auto !important;
      }
      #printableBeneficiaryDossier div,
      #printableBeneficiaryDossier p,
      #printableBeneficiaryDossier span:not(.badge):not(.badge *),
      #printableBeneficiaryDossier td,
      #printableBeneficiaryDossier th,
      #printableBeneficiaryDossier strong,
      #printableBeneficiaryDossier b,
      #printableBeneficiaryDossier small,
      body.theme-dark #printableBeneficiaryDossier div,
      body.theme-dark #printableBeneficiaryDossier p,
      body.theme-dark #printableBeneficiaryDossier span:not(.badge):not(.badge *),
      body.theme-dark #printableBeneficiaryDossier td,
      body.theme-dark #printableBeneficiaryDossier th,
      body.theme-dark #printableBeneficiaryDossier strong,
      body.theme-dark #printableBeneficiaryDossier b,
      body.theme-dark #printableBeneficiaryDossier small,
      html.theme-dark #printableBeneficiaryDossier div,
      html.theme-dark #printableBeneficiaryDossier p,
      html.theme-dark #printableBeneficiaryDossier span:not(.badge):not(.badge *),
      html.theme-dark #printableBeneficiaryDossier td,
      html.theme-dark #printableBeneficiaryDossier th,
      html.theme-dark #printableBeneficiaryDossier strong,
      html.theme-dark #printableBeneficiaryDossier b,
      html.theme-dark #printableBeneficiaryDossier small {
          color: #0f172a !important;
      }
      #printableBeneficiaryDossier .badge,
      #printableBeneficiaryDossier .badge *,
      body.theme-dark #printableBeneficiaryDossier .badge,
      body.theme-dark #printableBeneficiaryDossier .badge *,
      html.theme-dark #printableBeneficiaryDossier .badge,
      html.theme-dark #printableBeneficiaryDossier .badge * {
          color: #ffffff !important;
          font-weight: 700 !important;
      }
      .dotted-line-fill {
          border-bottom: 1.5px dotted #334155 !important;
          display: inline-block;
          padding: 0 6px 2px 6px;
          min-height: 22px;
      }
    </style>

    {{-- Watermark in background --}}
    <div class="voucher-watermark position-absolute top-50 start-50 translate-middle text-center"
         style="opacity: 0.035; pointer-events: none; z-index: 0; user-select: none;">
      <img src="{{ asset('images/logo.png') }}" alt="" style="width: 320px; filter: grayscale(100%);">
    </div>

    <div class="position-relative" style="z-index: 1;">

      @if($isGuestHouse)
        {{-- ========================================================================= --}}
        {{-- قالب رسمي: إستمارة دخول دار الضيافة (مطابق تماماً للنموذج المعتمد) --}}
        {{-- ========================================================================= --}}

        {{-- الترويسة الرسمية --}}
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-2 border-dark">
          {{-- أعلى اليمين --}}
          <div class="text-end" style="line-height: 1.4;">
            <div class="small fw-bold text-muted" style="color: #475569 !important;">جمهورية مصر العربية</div>
            <div class="fw-bolder fs-5 text-dark" style="color: #047857 !important; font-weight: 800;">مؤسسة إنسان الخيرية</div>
            <div class="fw-bold" style="font-size: 0.95rem; color: #1e293b !important;">المشهرة برقم 63 لسنة 2017م</div>
          </div>

          {{-- عنوان الاستمارة في المنتصف --}}
          <div class="text-center px-2">
            <div class="d-inline-block px-4 py-2 rounded-2 border border-2 border-dark" style="background-color: #ffffff;">
              <h4 class="fw-bolder mb-0 text-dark" style="letter-spacing: 0.5px; font-size: 1.35rem;">إستمارة دخول دار الضيافة</h4>
            </div>
            @if($beneficiary->guestHouse)
              <div class="small fw-bold text-success mt-1"><i class="bi bi-hospital me-1"></i>{{ $beneficiary->guestHouse->name }}</div>
            @endif
          </div>

          {{-- أعلى اليسار: الشعار وتفاصيل الوثيقة --}}
          <div class="text-start" style="line-height: 1.4;">
            <img src="{{ asset('images/logo.png') }}" alt="إنسان" style="height: 76px; max-width: 175px; object-fit: contain;" onerror="this.style.display='none'">
            <div class="small text-muted font-monospace mt-1">كود: <strong class="text-dark">{{ $beneficiary->code ?: ('G-' . $beneficiary->id) }}</strong></div>
          </div>
        </div>

        {{-- 1. بيانات المريض --}}
        <div class="mb-3 pt-1">
          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-7 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">أسم المريض رباعي :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bolder text-dark fs-6">{{ $beneficiary->full_name }}</span>
            </div>
            <div class="col-5 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">رقم الملف :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $beneficiary->code ?: ('G-' . $beneficiary->id) }}</span>
            </div>
          </div>

          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-7 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">رقـــــم قـــــومي :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $beneficiary->national_id ?: '—' }}</span>
            </div>
            <div class="col-5 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">النوع :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bold text-dark">{{ ($patientProfile?->gender === 'female' || $beneficiary->gender === 'female') ? 'أنثى' : 'ذكر' }}</span>
            </div>
          </div>

          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-7 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">محل إقامة المريض :</span>
              <span class="flex-grow-1 dotted-line-fill text-dark">{{ $beneficiary->address ?: '—' }}</span>
            </div>
            <div class="col-5 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">موبيل :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $beneficiary->phone ?: '—' }}</span>
            </div>
          </div>

          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-6 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">تاريخ الدخول :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $arrivalDate ? \Carbon\Carbon::parse($arrivalDate)->format('Y/m/d') : now()->format('Y/m/d') }}م</span>
            </div>
            <div class="col-6 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">مده الإقامة :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bold text-dark">{{ $expectedDays ?? 1 }} ليالي <small class="text-muted fw-normal">"قابله للتجديد حسب تقرير المستشفي"</small></span>
            </div>
          </div>

          @if(!empty($patientProfile?->medical_center) || !empty($patientProfile?->treatment_type))
          <div class="row g-2 align-items-center mb-2" style="font-size: 0.9rem;">
            <div class="col-6 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2 text-muted">مستشفى / مركز العلاج:</span>
              <span class="flex-grow-1 dotted-line-fill text-dark">{{ $patientProfile?->medical_center ?: '—' }}</span>
            </div>
            <div class="col-6 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2 text-muted">نوع العلاج / الجلسات:</span>
              <span class="flex-grow-1 dotted-line-fill text-dark">{{ $patientProfile?->treatment_type ?: '—' }} ({{ $patientProfile?->sessions_count ?? 1 }} جلسة)</span>
            </div>
          </div>
          @endif
        </div>

        {{-- 2. بيانات المرافق --}}
        <div class="text-center my-3">
          <div class="d-inline-block px-4 py-0.5 fw-bolder fs-6 border-bottom border-2 border-dark" style="letter-spacing: 1px;">
            بيــــــانات المُـــــرافق
          </div>
        </div>

        <div class="mb-3">
          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-7 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">أسم المُرافق رباعي :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bolder text-dark">{{ $patientProfile?->companion_name ?: 'لا يوجد مرافق (حالة فردية)' }}</span>
            </div>
            <div class="col-5 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">صله القرابة :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bold text-dark">{{ $patientProfile?->companion_relation ?: '—' }}</span>
            </div>
          </div>

          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-7 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">رقـــــم قـــــومي :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $patientProfile?->companion_national_id ?: '—' }}</span>
            </div>
            <div class="col-5 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">موبيل :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $patientProfile?->companion_phone ?: '—' }}</span>
            </div>
          </div>

          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-12 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">محل إقامة المُرافق :</span>
              <span class="flex-grow-1 dotted-line-fill text-dark">{{ $patientProfile?->companion_address ?: ($beneficiary->address ?: '—') }}</span>
            </div>
          </div>
        </div>

        {{-- 3. بيانات الإقامة --}}
        <div class="text-center my-3">
          <div class="d-inline-block px-4 py-0.5 fw-bolder fs-6 border-bottom border-2 border-dark" style="letter-spacing: 1px;">
            بيــــــانات الإقـــــامة
          </div>
        </div>

        <div class="mb-3">
          <div class="row g-2 align-items-center mb-2.5" style="font-size: 0.96rem;">
            <div class="col-7 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">الإقامة بغرفة / جناح :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bold text-dark">{{ $wingName }}</span>
            </div>
            <div class="col-5 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">رقم السرير :</span>
              <span class="flex-grow-1 dotted-line-fill font-monospace fw-bold text-dark">{{ $bedLabel }}</span>
            </div>
          </div>

          <div class="row g-2 align-items-center mb-2" style="font-size: 0.96rem;">
            <div class="col-12 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">موعد المغادرة يوم :</span>
              <span class="dotted-line-fill font-monospace fw-bold text-dark px-3" style="min-width: 140px;">{{ $departureDate ?? 'حسب التقرير' }}</span>
              <span class="ms-2 fw-bold text-dark">الساعة 10 صباحاً و غير مسموح بالتأخير</span>
            </div>
          </div>
        </div>

        {{-- 4. تعليمات هامة --}}
        <div class="my-3 p-3 rounded-2 border border-1 border-dark" style="background-color: #fdfdfd; font-size: 0.91rem; line-height: 1.75;">
          <div class="fw-bold text-decoration-underline mb-2 fs-6">تعليمات هــــامة :</div>
          <div class="ps-2">
            <div><strong>1 -</strong> يحذر نهائياً المرافقين الرجال والشباب والأطفال "المرافق من النساء فقط".</div>
            <div><strong>2 -</strong> ممنوع نهائياً تغيير المرافق.</div>
            <div><strong>3 -</strong> ممنوع نهائياً الزيارات مهما كانت القرابة.</div>
            <div><strong>4 -</strong> موعد المغادرة الساعة 10 صباحا.</div>
            <div><strong>5 -</strong> في حاله الطوارئ أو اعطاء الحقن أو المحاليل تذهب الحاله للمستشفي فوراً.</div>
          </div>
        </div>

        {{-- 5. تعهد --}}
        <div class="my-3 p-3 rounded-2 border border-1 border-dark" style="background-color: #ffffff; font-size: 0.91rem; line-height: 1.75;">
          <div class="text-center fw-bolder fs-6 mb-2" style="letter-spacing: 2px;">تـــــعـــــهـــــد</div>
          <p class="mb-3" style="text-align: justify; text-justify: inter-word;">
            يتعهد المُرافق الحفاظ علي المكان وخصوصيته من محتويات و خصوصية المرضي الأخرين وأنه المسؤول عن متعلقاته الشخصية والحفاظ عليها وأن يتم الإخلاء في الموعد المحدد.
          </p>
          <div class="row g-3 pt-1">
            <div class="col-6 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">الأسم رباعي :</span>
              <span class="flex-grow-1 dotted-line-fill fw-bold">{{ $patientProfile?->companion_name ?: '..........................................................' }}</span>
            </div>
            <div class="col-6 d-flex align-items-baseline">
              <span class="fw-bold text-nowrap me-2">تـــوقيـــــع :</span>
              <span class="flex-grow-1 dotted-line-fill">..........................................................</span>
            </div>
          </div>
        </div>

        {{-- 6. التوقيع في الأسفل --}}
        <div class="row pt-4 pb-2 text-center" style="font-size: 0.95rem;">
          <div class="col-4">
            <div class="fw-bold mb-4">توقيع المُرافق / المستلم</div>
            <div class="border-top border-dark mx-auto pt-1 small text-muted" style="width: 140px;">
              التوقيع
            </div>
          </div>
          <div class="col-4">
            <div class="fw-bold mb-2">ختم دار الضيافة</div>
            <div class="rounded-circle border border-2 border-dark mx-auto d-flex align-items-center justify-content-center"
                 style="width: 65px; height: 65px; opacity: 0.35;">
              <i class="bi bi-hospital fs-3"></i>
            </div>
          </div>
          <div class="col-4">
            <div class="fw-bold mb-4">مدير دار الضيافة</div>
            <div class="border-top border-dark mx-auto pt-1 small text-dark fw-bold" style="width: 140px;">
              {{ $beneficiary->guestHouse?->manager?->name ?? 'الاعتماد والتوقيع' }}
            </div>
          </div>
        </div>

        {{-- 7. تذييل الصفحة الرسمي المعتمد --}}
        <div class="text-center pt-3 mt-3 border-top border-2 border-dark" style="font-size: 0.84rem; line-height: 1.6; color: #1e293b;">
          <div class="fw-bold mb-1">
            <span class="font-monospace fs-6">01006090616</span>
            <span class="mx-2">•</span>
            <span>المقر الرئيسي : كفرالشيخ - شارع أبو العلاء المعري "مخزن الزيت"</span>
          </div>
          <div class="text-muted">
            <span class="font-monospace">www.ensaneg.com</span>
            <span class="mx-2">•</span>
            <span>مكتب الغربية: طنطا - شارع الدعوة المتفرع من شارع الرفاعي</span>
          </div>
        </div>

      @else
        {{-- ========================================================================= --}}
        {{-- قالب رسمي: استمارة بحث وملف مستفيد (للحالات العامة والرعاية الاجتماعية) --}}
        {{-- ========================================================================= --}}

        {{-- Official Header --}}
        <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-2" style="border-color: #059669 !important;">
          {{-- Right Official Details --}}
          <div class="text-end" style="line-height: 1.5;">
            <div class="small fw-bold text-muted" style="color: #475569 !important;">جمهورية مصر العربية</div>
            <div class="fw-bolder fs-6 text-dark" style="color: #0f172a !important;">مؤسسة إنسان الخيرية</div>
            <div class="text-muted fw-bold" style="font-size: 0.78rem; color: #475569 !important;">المشهرة برقم 63 لسنة 2017م</div>
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
                <td class="fw-bold text-success font-monospace" style="color: #047857 !important;">{{ $beneficiary->visa_number ?: 'لا يوجد' }}</td>
              </tr>
              <tr>
                <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">المشروع التابع له:</td>
                <td style="color: #0f172a !important;">{{ $beneficiary->project?->name ?: 'عام / غير مقيد' }}</td>
                <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">حالة الملف:</td>
                <td><span class="badge text-white" style="background-color: #059669 !important; color: #ffffff !important; font-weight: 700;">{{ $statusText }}</span></td>
              </tr>
              <tr>
                <td class="bg-light fw-bold text-muted" style="background: #f8fafc !important; color: #475569 !important;">العنوان بالتفصيل:</td>
                <td colspan="3" style="color: #0f172a !important;">{{ $beneficiary->address ?: 'لم يسجل عنوان دقيق' }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        {{-- Section 2: Family Members (أفراد الأسرة التابعين) --}}
        @if($activeMembers->isNotEmpty())
        <div class="mb-3">
          <div class="fw-bold text-dark pb-1 mb-2 border-bottom border-1 border-secondary border-opacity-25 d-flex align-items-center justify-content-between" style="font-size: 13.5px; color: #047857 !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-people text-success"></i>
              <span>ثانياً: أفراد الأسرة المسجلين بالملف</span>
            </div>
            <span class="badge bg-light text-dark border">{{ $activeMembers->count() }} أفراد</span>
          </div>
          <table class="table table-bordered table-sm text-center mb-0" style="border-color: #cbd5e1; font-size: 11.5px;">
            <thead class="bg-light" style="background-color: #f1f5f9 !important;">
              <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 30%;">الاسم</th>
                <th style="width: 20%;">صلة القرابة</th>
                <th style="width: 25%;">الرقم القومي</th>
                <th style="width: 20%;">المرحلة / المهنة</th>
              </tr>
            </thead>
            <tbody>
              @foreach($activeMembers as $mIdx => $member)
                <tr>
                  <td>{{ $mIdx + 1 }}</td>
                  <td class="fw-bold text-dark" style="text-align: right; color: #0f172a;">{{ $member->full_name }}</td>
                  <td>{{ $member->relationship ?: '—' }}</td>
                  <td class="font-monospace">{{ $member->national_id ?: '—' }}</td>
                  <td>{{ $member->education_level ?: ($member->job ?: '—') }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        @endif

        {{-- Section 3: Financial & Donations Summary (سجل المساعدات السابقة) --}}
        @if(isset($donations) && $donations->isNotEmpty())
        <div class="mb-3">
          <div class="fw-bold text-dark pb-1 mb-2 border-bottom border-1 border-secondary border-opacity-25 d-flex align-items-center justify-content-between" style="font-size: 13.5px; color: #047857 !important;">
            <div class="d-flex align-items-center gap-2">
              <i class="bi bi-cash-stack text-success"></i>
              <span>ثالثاً: ملخص المساعدات والتبرعات الواردة للحالة (آخر العمليات)</span>
            </div>
            <span class="badge bg-light text-dark border">{{ $donations->count() }} مساعدة</span>
          </div>
          <table class="table table-bordered table-sm text-center mb-0" style="border-color: #cbd5e1; font-size: 11.5px;">
            <thead class="bg-light" style="background-color: #f1f5f9 !important;">
              <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 20%;">رقم الإيصال</th>
                <th style="width: 18%;">تاريخ الصرف</th>
                <th style="width: 25%;">المتبرع / المصدر</th>
                <th style="width: 15%;">النوع</th>
                <th style="width: 17%;">القيمة</th>
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

        {{-- Section 4: Social Research Notes --}}
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

      @endif

    </div>
  </div>
</div>
