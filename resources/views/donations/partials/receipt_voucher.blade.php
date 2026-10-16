@php
  $amountVal = $donation->type === 'cash' ? $donation->amount : $donation->estimated_value;
  $currencyStr = $donation->currency ?: 'جنيه مصري';
  $tafqeetText = \App\Helpers\TafqeetHelper::inWords($amountVal, $currencyStr);
  $channelName = match ($donation->cash_channel) {
    'instapay' => 'انستا باي (InstaPay)',
    'vodafone_cash' => 'فودافون كاش (Vodafone Cash)',
    'delegate' => 'محصل مندوب',
    default => 'نقدي بالخزينة'
  };
@endphp

<div class="official-voucher-card bg-white text-dark p-4 p-md-5 position-relative mx-auto" 
     style="max-width: 860px; width: 100%; border: 2.5px solid #059669; outline: 1px solid #10b981; outline-offset: -5px; border-radius: 8px; box-shadow: 0 10px 40px rgba(0,0,0,0.15); font-family: 'IBM Plex Sans Arabic', system-ui, sans-serif !important; box-sizing: border-box; background: #ffffff !important; color: #0f172a !important;">
  
  {{-- Embedded Scoped Styles: Guarantee pure white paper & dark text in any theme --}}
  <style>
    .official-voucher-card,
    .official-voucher-card *,
    body.theme-dark .official-voucher-card,
    body.theme-dark .official-voucher-card *,
    html.theme-dark .official-voucher-card,
    html.theme-dark .official-voucher-card *,
    [data-bs-theme="dark"] .official-voucher-card,
    [data-bs-theme="dark"] .official-voucher-card * {
        color-scheme: light !important;
        box-sizing: border-box !important;
        font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif !important;
    }

    .official-voucher-card {
        background: #ffffff !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        direction: rtl !important;
    }

    .official-voucher-card div,
    .official-voucher-card p,
    .official-voucher-card span:not(.badge):not(.badge *),
    .official-voucher-card td,
    .official-voucher-card th,
    .official-voucher-card strong,
    .official-voucher-card b,
    .official-voucher-card small,
    body.theme-dark .official-voucher-card div,
    body.theme-dark .official-voucher-card p,
    body.theme-dark .official-voucher-card span:not(.badge):not(.badge *),
    body.theme-dark .official-voucher-card td,
    body.theme-dark .official-voucher-card th,
    body.theme-dark .official-voucher-card strong,
    body.theme-dark .official-voucher-card b,
    body.theme-dark .official-voucher-card small,
    html.theme-dark .official-voucher-card div,
    html.theme-dark .official-voucher-card p,
    html.theme-dark .official-voucher-card span:not(.badge):not(.badge *),
    html.theme-dark .official-voucher-card td,
    html.theme-dark .official-voucher-card th,
    html.theme-dark .official-voucher-card strong,
    html.theme-dark .official-voucher-card b,
    html.theme-dark .official-voucher-card small {
        color: #0f172a !important;
    }

    .official-voucher-card .text-muted,
    body.theme-dark .official-voucher-card .text-muted,
    html.theme-dark .official-voucher-card .text-muted {
        color: #475569 !important;
    }

    .official-voucher-card .text-success,
    body.theme-dark .official-voucher-card .text-success,
    html.theme-dark .official-voucher-card .text-success {
        color: #047857 !important;
    }

    .official-voucher-card .text-dark,
    body.theme-dark .official-voucher-card .text-dark,
    html.theme-dark .official-voucher-card .text-dark {
        color: #0f172a !important;
    }

    .official-voucher-card .bg-light,
    body.theme-dark .official-voucher-card .bg-light,
    html.theme-dark .official-voucher-card .bg-light {
        background-color: #f8fafc !important;
        border-color: #cbd5e1 !important;
    }

    .official-voucher-card .voucher-amount-box {
        background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%) !important;
        border: 2px solid #059669 !important;
    }

    .official-voucher-card .voucher-amount-box * {
        color: #047857 !important;
    }

    .official-voucher-card .voucher-title-box {
        background-color: #f0fdf4 !important;
        border: 2px solid #059669 !important;
    }

    .official-voucher-card .voucher-body-box {
        background-color: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
    }

    .official-voucher-card .badge,
    .official-voucher-card .badge *,
    body.theme-dark .official-voucher-card .badge,
    html.theme-dark .official-voucher-card .badge {
        color: #ffffff !important;
        font-weight: 700 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    .official-voucher-card .badge.bg-secondary-subtle {
        background-color: #e2e8f0 !important;
        color: #0f172a !important;
        border-color: #cbd5e1 !important;
    }

    .official-voucher-card .badge.bg-secondary-subtle * {
        color: #0f172a !important;
    }
  </style>

  {{-- Watermark in background --}}
  <div class="voucher-watermark position-absolute top-50 start-50 translate-middle text-center" 
       style="opacity: 0.04; pointer-events: none; z-index: 0; user-select: none;">
    <img src="{{ asset('images/logo.png') }}" alt="" style="width: 280px; filter: grayscale(100%);">
  </div>

  <div class="position-relative" style="z-index: 1;">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center pb-3 mb-3 border-bottom border-2" style="border-color: #059669 !important;">
      {{-- Right Official Details --}}
      <div class="text-start" style="line-height: 1.5;">
        <div class="small fw-bold text-muted">جمهورية مصر العربية</div>
        <div class="small fw-bold text-muted">وزارة التضامن الاجتماعي</div>
        <div class="fw-bolder fs-6 text-dark">مؤسسة إنسان الخيرية</div>
        <div class="text-muted" style="font-size: 0.72rem;">مشهرة برقم رسمي للعمل الخيري</div>
      </div>

      {{-- Center Title Box --}}
      <div class="text-center px-3">
        <div class="voucher-title-box d-inline-block px-4 py-2 rounded-2">
          <h4 class="fw-bolder mb-0 text-success">إيصال استلام تبرع</h4>
          <div class="fw-bold font-monospace text-muted mt-1" style="font-size: 0.75rem; letter-spacing: 1px;">DONATION OFFICIAL RECEIPT</div>
        </div>
        <div class="mt-1">
          <span class="badge {{ $donation->type === 'cash' ? 'bg-success' : 'bg-info' }} px-3 py-1 text-white shadow-sm" style="background-color: {{ $donation->type === 'cash' ? '#059669' : '#0891b2' }} !important;">
            تبرع {{ $donation->type === 'cash' ? 'نقدي' : 'عيني' }}
          </span>
        </div>
      </div>

      {{-- Left Voucher Details --}}
      <div class="text-end" style="line-height: 1.6;">
        <div class="d-inline-block p-2 rounded border border-secondary border-opacity-25 bg-light text-end">
          <div class="small text-muted">رقم الوصل:</div>
          <div class="fw-bolder font-monospace text-dark fs-5">{{ $donation->receipt_number ?: ('REC-' . $donation->id) }}</div>
          <div class="small text-muted mt-1">التاريخ: <span class="fw-bold text-dark font-monospace">{{ optional($donation->received_at)->format('Y/m/d') ?? now()->format('Y/m/d') }}</span></div>
        </div>
      </div>
    </div>

    {{-- Prominent Amount Box --}}
    <div class="voucher-amount-box d-flex justify-content-between align-items-center p-3 mb-4 rounded-3">
      <div class="fw-bold fs-6 text-success d-flex align-items-center gap-2">
        <i class="bi bi-cash-stack fs-4"></i>
        <span>المبلغ المستلم:</span>
      </div>
      <div class="fs-3 fw-bolder font-monospace text-success">
        {{ number_format($amountVal, 2) }} <span class="fs-6 fw-bold">{{ $currencyStr }}</span>
      </div>
    </div>

    {{-- Voucher Body Details --}}
    <div class="voucher-body-box p-3 mb-4 rounded-3" style="line-height: 1.9;">
      <div class="row g-2 align-items-center border-bottom pb-2 mb-2" style="border-color: #e2e8f0 !important;">
        <div class="col-sm-3 text-muted fw-bold">وصلنا من السيد / السيدة:</div>
        <div class="col-sm-9 fw-bolder fs-6 text-dark d-flex flex-wrap align-items-center gap-2">
          <span>{{ $donation->donor?->name ?? 'فاعل خير' }}</span>
          @if($donation->donor)
            <span class="badge bg-secondary-subtle text-dark border font-monospace px-2 py-1" style="font-size: 0.75rem;">
              كود: {{ $donation->donor->code ?? ('DON-'.$donation->donor->id) }}
            </span>
            @if($donation->donor->phone)
              <span class="text-muted small font-monospace"><i class="bi bi-telephone me-1"></i>{{ $donation->donor->phone }}</span>
            @endif
          @endif
        </div>
      </div>

      <div class="row g-2 align-items-center border-bottom pb-2 mb-2" style="border-color: #e2e8f0 !important;">
        <div class="col-sm-3 text-muted fw-bold">مبلغ وقدره (بالحروف):</div>
        <div class="col-sm-9 fw-bold text-success fs-6">
          {{ $tafqeetText }}
        </div>
      </div>

      <div class="row g-2 align-items-center border-bottom pb-2 mb-2" style="border-color: #e2e8f0 !important;">
        <div class="col-sm-3 text-muted fw-bold">وذلك تبرعاً عن:</div>
        <div class="col-sm-9 fw-bolder text-dark">
          {{ $donation->purpose_label }}
          @if($donation->project)
            <span class="badge bg-light text-dark border ms-2"><i class="bi bi-building me-1"></i>مشروع: {{ $donation->project->name }}</span>
          @endif
          @if($donation->campaign)
            <span class="badge bg-light text-dark border ms-2"><i class="bi bi-megaphone me-1"></i>حملة: {{ $donation->campaign->name }}</span>
          @endif
        </div>
      </div>

      <div class="row g-2 align-items-center">
        <div class="col-sm-3 text-muted fw-bold">طريقة السداد:</div>
        <div class="col-sm-9 fw-medium text-dark d-flex align-items-center gap-3">
          <span>{{ $channelName }}</span>
          @if($donation->receipt_number)
            <span class="text-muted small font-monospace">(رقم المعاملة: {{ $donation->receipt_number }})</span>
          @endif
        </div>
      </div>

      @if($donation->display_allocation_note)
      <div class="row g-2 align-items-center border-top pt-2 mt-2" style="border-color: #e2e8f0 !important;">
        <div class="col-sm-3 text-muted fw-bold">ملاحظات التخصيص:</div>
        <div class="col-sm-9 text-dark">{{ $donation->display_allocation_note }}</div>
      </div>
      @endif
    </div>

    {{-- Official Signatures & Stamp --}}
    <div class="row text-center pt-3 pb-2 border-top border-2 mt-4" style="border-color: #0f172a !important;">
      <div class="col-4">
        <div class="fw-bold small mb-5 text-dark">المستلم / المحصل</div>
        <div class="border-top mx-auto pt-1 small text-muted" style="border-color: #475569 !important; width: 140px;">
          {{ auth()->user()?->name ?? 'التوقيع' }}
        </div>
      </div>
      <div class="col-4">
        <div class="fw-bold small mb-4 text-dark">ختم المؤسسة الرسمي</div>
        <div class="rounded-circle border border-2 border-success mx-auto d-flex align-items-center justify-content-center" 
             style="width: 75px; height: 75px; opacity: 0.35;">
          <i class="bi bi-patch-check text-success fs-2"></i>
        </div>
      </div>
      <div class="col-4">
        <div class="fw-bold small mb-5 text-dark">أمين الصندوق / الإدارة المالية</div>
        <div class="border-top mx-auto pt-1 small text-muted" style="border-color: #475569 !important; width: 140px;">الاعتماد والتوقيع</div>
      </div>
    </div>

    {{-- Quranic Verse & Footer Note --}}
    <div class="text-center pt-3 mt-3 border-top" style="border-color: #cbd5e1 !important; font-size: 0.78rem;">
      <div class="fw-bold mb-1 font-monospace" style="color: #047857 !important;">
        ﴿ وَمَا تُنفِقُوا مِنْ خَيْرٍ فَإِنَّ اللَّهَ بِهِ عَلِيمٌ ﴾
      </div>
      <div class="text-muted small">
        هذا الإيصال سند قبض رسمي معتمد من الإدارة المالية بمؤسسة إنسان الخيرية — شكراً لعطائكم المستمر.
      </div>
    </div>

  </div>
</div>
