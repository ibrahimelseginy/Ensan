<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <title>سند صرف عهدة مالية - {{ $custody->custody_number }}</title>
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
            <div class="text-muted small">إدارة الحسابات والمالية — قسم العهد النقدية</div>
        </div>
        <div class="text-start">
            <h5 class="fw-bold text-primary mb-1">سند صرف عهدة مالية</h5>
            <div class="font-monospace fw-bold">{{ $custody->custody_number }}</div>
            <div class="text-muted small">التاريخ: {{ $custody->issued_on ? $custody->issued_on->format('Y/m/d') : date('Y/m/d') }}</div>
        </div>
    </div>

    {{-- Details Table --}}
    <table class="table table-bordered mb-4">
        <tbody>
            <tr>
                <th class="bg-light" style="width: 25%;">اسم الموظف المستلم:</th>
                <td class="fw-bold">{{ $custody->holder->name ?? '—' }}</td>
                <th class="bg-light" style="width: 25%;">الوظيفة / القسم:</th>
                <td>{{ $custody->holder->job_title ?? ($custody->holder->department ?? 'موظف') }}</td>
            </tr>
            <tr>
                <th class="bg-light">مبلغ العهدة رقماً:</th>
                <td class="fw-bold fs-5 text-success font-monospace">{{ number_format($custody->issued_amount, 2) }} ج.م</td>
                <th class="bg-light">موعد التسوية الأقصى:</th>
                <td class="font-monospace">{{ $custody->due_on ? $custody->due_on->format('Y/m/d') : 'خلال شهر من تاريخه' }}</td>
            </tr>
            <tr>
                <th class="bg-light">المشروع / الحملة:</th>
                <td colspan="3">{{ $custody->project->name ?? 'مصروف عام للمؤسسة' }}</td>
            </tr>
            <tr>
                <th class="bg-light">الغرض والبيان:</th>
                <td colspan="3">{{ $custody->purpose }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Invoices Summary if exists --}}
    @if($custody->expenses->isNotEmpty())
        <h6 class="fw-bold mb-2">الفواتير المعتمدة بالتسوية:</h6>
        <table class="table table-sm table-bordered text-center mb-4 small">
            <thead class="table-light">
                <tr>
                    <th>#</th>
                    <th>التاريخ</th>
                    <th>رقم الفاتورة</th>
                    <th>التاجر</th>
                    <th>المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($custody->expenses as $i => $e)
                    <tr>
                        <td>{{ $i + 1 }}</td>
                        <td>{{ $e->expense_date ? $e->expense_date->format('Y/m/d') : '—' }}</td>
                        <td>{{ $e->invoice_number ?? '—' }}</td>
                        <td>{{ $e->merchant_name ?? '—' }}</td>
                        <td class="font-monospace fw-bold">{{ number_format($e->amount, 2) }} ج.م</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td colspan="4" class="text-end">الإجمالي المصروف:</td>
                    <td class="font-monospace text-primary">{{ number_format($custody->settled_amount, 2) }} ج.م</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Signatures --}}
    <div class="row g-3 text-center mt-4">
        <div class="col-4">
            <div class="fw-bold mb-1">المستلم (الموظف)</div>
            <div class="text-muted small mb-2">{{ $custody->holder->name ?? '' }}</div>
            <div class="stamp-box">توقيع المستلم بما يفيد استلام النقدية</div>
        </div>
        <div class="col-4">
            <div class="fw-bold mb-1">المحاسب المسؤول</div>
            <div class="text-muted small mb-2">إدارة الحسابات</div>
            <div class="stamp-box">توقيع المحاسب</div>
        </div>
        <div class="col-4">
            <div class="fw-bold mb-1">اعتماد الإدارة المالية</div>
            <div class="text-muted small mb-2">المدير المالي / الأمين</div>
            <div class="stamp-box">ختم وتوقيع الاعتماد</div>
        </div>
    </div>

    <div class="text-center text-muted small mt-4 pt-3 border-top">
        نظام إنسان السحابي لإدارة المؤسسات الخيرية — رقم السند: {{ $custody->custody_number }} — طبع بتاريخ: {{ now()->format('Y/m/d h:i A') }}
    </div>
</div>

</body>
</html>
