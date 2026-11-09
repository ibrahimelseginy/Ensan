<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>استمارة إعادة بحث دوري (كل 6 أشهر) — {{ $beneficiary->full_name }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background: linear-gradient(135deg, #f0fdfa 0%, #f8fafc 100%);
            min-height: 100vh;
            padding: 24px 12px;
        }
        .form-card {
            max-width: 900px;
            margin: 0 auto;
            border-radius: 20px;
            overflow: hidden;
            border: 0;
            box-shadow: 0 15px 40px rgba(13, 148, 136, 0.12);
        }
        .form-header {
            background: linear-gradient(135deg, #0d9488 0%, #0f766e 100%);
            color: #fff;
            padding: 28px 24px;
        }
        .section-badge {
            background: #f0fdfa;
            color: #0f766e;
            border-right: 4px solid #0d9488;
            padding: 10px 14px;
            border-radius: 8px;
            font-weight: 700;
            margin-bottom: 16px;
        }
    </style>
</head>
<body>
    <div class="card form-card bg-white">
        <div class="form-header">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-white bg-opacity-25 rounded-circle d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                        <i class="bi bi-arrow-repeat fs-3 text-white"></i>
                    </div>
                    <div>
                        <div class="small opacity-75 fw-semibold">مؤسسة إنسان الخيرية — بعثاء الأمل</div>
                        <h4 class="fw-bold mb-0">استمارة إعادة بحث وتحديث بيانات الأسرة (كل 6 أشهر)</h4>
                    </div>
                </div>
                <span class="badge bg-white text-teal font-monospace px-3 py-2 rounded-pill fw-bold" style="color:#0f766e;">
                    كود الملف: {{ $beneficiary->code }}
                </span>
            </div>
            <div class="mt-3 pt-2 border-top border-white border-opacity-25 small">
                <i class="bi bi-house-heart me-1"></i> الأسرة / ولي الأمر: <strong class="fs-6">{{ $beneficiary->full_name }}</strong>
                @if($beneficiary->patient_name)
                    <span class="ms-3"><i class="bi bi-person-Vcard me-1"></i> المريض الأساسي: <strong>{{ $beneficiary->patient_name }}</strong></span>
                @endif
            </div>
        </div>

        <div class="card-body p-4">
            @if(session('submitted_success'))
                <div class="text-center py-5">
                    <div class="mb-3">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle bg-success-subtle text-success" style="width: 80px; height: 80px; font-size: 2.5rem;">
                            <i class="bi bi-check-lg"></i>
                        </span>
                    </div>
                    <h4 class="fw-bold text-success mb-2">تم حفظ استمارة إعادة البحث بنجاح!</h4>
                    <p class="text-muted mb-4">{{ session('submitted_success') }}</p>
                    <a href="{{ request()->url() }}" class="btn btn-outline-success rounded-pill px-4 fw-bold">
                        <i class="bi bi-arrow-repeat me-1"></i> تسجيل تحديث جديد
                    </a>
                </div>
            @else
                <div class="alert alert-info border-0 rounded-3 small mb-4">
                    <i class="bi bi-shield-check me-1"></i>
                    <strong>ملاحظة هامة:</strong> هذه الاستمارة تُسجّل كتحديث دوري كل 6 أشهر وتُحفظ في سجل التحديثات التاريخي للملف <strong>دون مسح أو فقدان أي بيانات أساسية مسجلة مسبقاً للأسرة</strong>.
                </div>

                <form method="POST" action="{{ route('public.reassessment.submit', ['beneficiary' => $beneficiary->id, 'token' => $token]) }}">
                    @csrf
                    @include('beneficiaries.partials.reassessment_form_fields', ['beneficiary' => $beneficiary])
                    <div class="mt-4 pt-3 border-top d-grid">
                        <button type="submit" class="btn btn-success btn-lg rounded-pill fw-bold shadow-sm">
                            <i class="bi bi-check2-circle me-1"></i> حفظ واعتماد استمارة إعادة البحث في النظام
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</body>
</html>

