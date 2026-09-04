<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تتبع الشكوى | مؤسسة إنسان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #2ecc71; --primary-dark: #27ae60; }
        body { background: #f0f4f8; min-height: 100vh; display: flex; flex-direction: column; }
        .brand-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: white; padding: 2rem 0; text-align: center;
        }
        .brand-header .logo-circle {
            width: 70px; height: 70px; background: white; border-radius: 50%;
            display: inline-flex; align-items: center; justify-content: center;
            margin-bottom: 0.75rem; font-size: 2rem;
        }
        .track-card { border-radius: 1rem; border: none; box-shadow: 0 4px 24px rgba(0,0,0,0.08); }
        .status-badge-lg { font-size: 1rem; padding: 0.5rem 1.25rem; border-radius: 2rem; }
        .result-section { border-right: 4px solid var(--primary); padding-right: 1rem; }
        .resolution-box {
            background: #f0fdf4; border: 1px solid #bbf7d0;
            border-radius: 0.75rem; padding: 1.25rem;
        }
        .footer-link { color: rgba(255,255,255,0.8); text-decoration: none; }
        .footer-link:hover { color: white; }
    </style>
</head>
<body>

<div class="brand-header">
    <div class="logo-circle">
        <span>🤝</span>
    </div>
    <h3 class="fw-bold mb-0">مؤسسة إنسان الخيرية</h3>
    <p class="mb-0 opacity-75">نظام تتبع الشكاوي</p>
</div>

<div class="container py-5" style="max-width: 700px;">

    {{-- Search Form --}}
    <div class="card track-card mb-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-1"><i class="bi bi-search text-primary me-2"></i>أدخل كود التتبع</h5>
            <p class="text-muted small mb-3">ستجد كود التتبع في الرسالة التي أُرسلت إليك عند تسجيل الشكوى.</p>

            <form method="POST" action="{{ route('complaint.track.search') }}">
                @csrf
                <div class="input-group input-group-lg">
                    <input type="text" name="tracking_code"
                           class="form-control @error('tracking_code') is-invalid @enderror"
                           placeholder="مثال: ENS-AB1C2D"
                           value="{{ old('tracking_code', $code ?? '') }}"
                           style="letter-spacing: 2px; font-family: monospace; font-size: 1.1rem;"
                           autofocus>
                    <button class="btn btn-success px-4" type="submit">
                        <i class="bi bi-search me-1"></i> بحث
                    </button>
                </div>
                @error('tracking_code')
                    <div class="text-danger small mt-1">{{ $message }}</div>
                @enderror
            </form>
        </div>
    </div>

    {{-- Results --}}
    @isset($code)
        @if(!isset($complaint) || !$complaint)
            <div class="alert alert-warning d-flex align-items-center gap-3 rounded-3">
                <i class="bi bi-exclamation-triangle fs-3"></i>
                <div>
                    <div class="fw-bold">لم يتم العثور على الشكوى</div>
                    <div class="small">لا توجد شكوى بالكود <code>{{ $code }}</code>، يرجى التحقق من الكود وإعادة المحاولة.</div>
                </div>
            </div>
        @else
            <div class="card track-card">
                <div class="card-body p-4">

                    {{-- Header --}}
                    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                        <div>
                            <div class="text-muted small mb-1">كود التتبع</div>
                            <h4 class="fw-bold mb-0" style="font-family: monospace; letter-spacing: 2px; color: var(--primary);">
                                {{ $complaint->tracking_code }}
                            </h4>
                        </div>
                        @php
                            [$statusClass, $statusText, $statusIcon] = match($complaint->status) {
                                'open'        => ['warning',  'مفتوحة',     'bi-clock'],
                                'in_progress' => ['primary',  'جارية المعالجة', 'bi-gear-wide-connected'],
                                'closed'      => ['success',  'مغلقة / محلولة',   'bi-check-circle'],
                                default       => ['secondary', $complaint->status, 'bi-question-circle'],
                            };
                        @endphp
                        <span class="badge bg-{{ $statusClass }}-subtle text-{{ $statusClass }} status-badge-lg">
                            <i class="bi {{ $statusIcon }} me-1"></i>{{ $statusText }}
                        </span>
                    </div>

                    {{-- Subject --}}
                    <div class="result-section mb-4">
                        <div class="text-muted small">موضوع الشكوى</div>
                        <div class="fw-semibold fs-5">{{ $complaint->subject }}</div>
                    </div>

                    {{-- Source Type --}}
                    <div class="row g-3 mb-4">
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">نوع المُبلِّغ</div>
                            <span class="badge bg-secondary-subtle text-secondary px-3 py-2">
                                {{ match($complaint->source_type) {
                                    'donor'         => 'متبرع',
                                    'beneficiary'   => 'مستفيد',
                                    'employee'      => 'موظف',
                                    default         => $complaint->source_type
                                } }}
                            </span>
                        </div>
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">تاريخ التسجيل</div>
                            <div class="fw-semibold">{{ $complaint->created_at->format('d/m/Y') }}</div>
                        </div>
                        @if($complaint->resolved_at)
                        <div class="col-sm-6">
                            <div class="text-muted small mb-1">تاريخ الحل</div>
                            <div class="fw-semibold text-success">{{ $complaint->resolved_at->format('d/m/Y') }}</div>
                        </div>
                        @endif
                    </div>

                    {{-- Resolution --}}
                    @if($complaint->resolution)
                        <div class="resolution-box mb-3">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-success fs-5"></i>
                                <span class="fw-bold text-success">الحل المقدَّم</span>
                            </div>
                            <p class="mb-0" style="line-height: 1.8;">{{ $complaint->resolution }}</p>
                        </div>
                    @else
                        <div class="alert alert-light border d-flex align-items-center gap-2 mb-0">
                            <i class="bi bi-hourglass-split text-muted fs-5"></i>
                            <span class="text-muted">
                                @if($complaint->status === 'open')
                                    شكواك قيد المراجعة الأولية، سنبدأ المعالجة قريباً.
                                @elseif($complaint->status === 'in_progress')
                                    شكواك تُعالَج حالياً، سيتم إخبارك بالحل فور الانتهاء.
                                @else
                                    تم إغلاق الشكوى.
                                @endif
                            </span>
                        </div>
                    @endif

                </div>
            </div>
        @endif
    @endisset

    {{-- Back to system --}}
    <div class="text-center mt-4">
        <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-box-arrow-in-right me-1"></i> دخول النظام
        </a>
    </div>

</div>

<div class="mt-auto py-3 text-center" style="background: #1a1a2e; color: rgba(255,255,255,0.6); font-size: 0.85rem;">
    جميع الحقوق محفوظة &copy; مؤسسة إنسان {{ date('Y') }}
</div>

</body>
</html>
