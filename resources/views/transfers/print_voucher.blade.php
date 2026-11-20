<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>سند توريد واستلام نقدية - {{ $transfer->transfer_number }}</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #fff; color: #1e293b; }
        .voucher-box { border: 2px solid #0f172a; border-radius: 12px; padding: 28px; max-width: 820px; margin: 30px auto; }
        .voucher-header { border-bottom: 2px dashed #94a3b8; padding-bottom: 20px; margin-bottom: 24px; }
        .stamp-box { border: 2px dashed #cbd5e1; height: 110px; border-radius: 8px; display: flex; align-items: center; justify-content: center; color: #94a3b8; font-size: 0.85rem; }
        @media print {
            .no-print { display: none !important; }
            body { margin: 0; padding: 0; }
            .voucher-box { border: 1.5px solid #000; box-shadow: none !important; }
        }
    </style>
</head>
<body>

<div class="no-print text-center py-3 bg-light border-bottom">
    <button onclick="window.print();" class="btn btn-primary rounded-pill px-4 me-2">
        <i class="bi bi-printer"></i> طباعة السند
    </button>
    <button onclick="window.close();" class="btn btn-secondary rounded-pill px-4">إغلاق</button>
</div>

<div class="voucher-box shadow-sm">
    {{-- Header --}}
    <div class="voucher-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="fw-bold mb-1">مؤسسة إنسان الخيرية</h4>
            <div class="text-muted small">إدارة الحسابات والمالية — قسم التحويلات والتوريدات</div>
        </div>
        <div class="text-start">
            <h5 class="fw-bold text-success mb-1">سند توريد واستلام نقدية</h5>
            <div class="font-monospace fw-bold">{{ $transfer->transfer_number }}</div>
            <div class="text-muted small">التاريخ: {{ $transfer->requested_on ? $transfer->requested_on->format('Y/m/d') : date('Y/m/d') }}</div>
        </div>
    </div>

    {{-- Details Table --}}
    <table class="table table-bordered mb-4">
        <tbody>
            <tr>
                <th class="bg-light" style="width: 25%;">الخزينة الموردة (المصدر):</th>
                <td class="fw-bold">{{ $transfer->fromAccount->name ?? '—' }}</td>
                <th class="bg-light" style="width: 25%;">الخزينة المستلمة (الهدف):</th>
                <td class="fw-bold text-success">{{ $transfer->toAccount->name ?? '—' }}</td>
            </tr>
            <tr>
                <th class="bg-light">المبلغ المورد رقماً:</th>
                <td class="fw-bold fs-5 text-dark font-monospace">{{ number_format($transfer->amount, 2) }} ج.م</td>
                <th class="bg-light">حالة العملية:</th>
                <td>
                    @if($transfer->status === 'received')
                        <span class="badge bg-success px-2.5 py-1">تم الاستلام والمطابقة</span>
                    @else
                        <span class="badge bg-warning text-dark px-2.5 py-1">قيد انتظار الاستلام</span>
                    @endif
                </td>
            </tr>
            <tr>
                <th class="bg-light">الموظف المسلّم (المورد):</th>
                <td>{{ $transfer->requester->name ?? '—' }}</td>
                <th class="bg-light">الموظف المستلم:</th>
                <td>{{ $transfer->receiver->name ?? 'في انتظار تأكيد الاستلام' }}</td>
            </tr>
            <tr>
                <th class="bg-light">رقم المرجع / الحافظة:</th>
                <td colspan="3">{{ $transfer->reference ?? '—' }}</td>
            </tr>
            <tr>
                <th class="bg-light">ملاحظات وبيان التوريد:</th>
                <td colspan="3">{{ $transfer->note ?? 'لا توجد ملاحظات إضافية' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Signatures --}}
    <div class="row g-3 text-center mt-4">
        <div class="col-4">
            <div class="fw-bold mb-1">المسلم (أمين الخزينة الموردة)</div>
            <div class="text-muted small mb-2">{{ $transfer->requester->name ?? '' }}</div>
            <div class="stamp-box">توقيع المسلّم</div>
        </div>
        <div class="col-4">
            <div class="fw-bold mb-1">المستلم (أمين الخزينة المستلمة)</div>
            <div class="text-muted small mb-2">{{ $transfer->receiver->name ?? '' }}</div>
            <div class="stamp-box">توقيع المستلم بعد العد الفعلي</div>
        </div>
        <div class="col-4">
            <div class="fw-bold mb-1">اعتماد الإدارة المالية</div>
            <div class="text-muted small mb-2">المحاسب المسؤول</div>
            <div class="stamp-box">ختم وتوقيع الاعتماد</div>
        </div>
    </div>

    <div class="text-center text-muted small mt-4 pt-3 border-top">
        نظام إنسان السحابي لإدارة المؤسسات الخيرية — رقم السند: {{ $transfer->transfer_number }} — طبع بتاريخ: {{ now()->format('Y/m/d h:i A') }}
    </div>
</div>

</body>
</html>
