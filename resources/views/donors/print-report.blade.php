<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $reportTitle }} — مؤسسة إنسان</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&display=swap');

        :root {
            --primary: #047857;
            --primary-dark: #065f46;
            --primary-light: #ecfdf5;
        }

        * {
            font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
        }

        html, body {
            font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            background-color: #f1f5f9;
            color: #0f172a;
            margin: 0;
            padding: 20px;
        }

        .no-print-toolbar {
            max-width: 1100px;
            margin: 0 auto 20px auto;
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 12px 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .print-paper-sheet {
            max-width: 1100px;
            margin: 0 auto;
            background: #ffffff;
            border: 2.5px solid #059669;
            outline: 1px solid #10b981;
            outline-offset: -5px;
            border-radius: 8px;
            padding: 40px;
            position: relative;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.12);
        }

        .sheet-watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            opacity: 0.035;
            pointer-events: none;
            z-index: 0;
            user-select: none;
        }

        .ensan-report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 16px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .report-kpi-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
            margin-bottom: 24px;
            position: relative;
            z-index: 1;
        }

        .report-kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 16px;
            text-align: center;
        }

        .report-kpi-card.highlight {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .report-kpi-title {
            font-size: 11.5px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
        }

        .report-kpi-value {
            font-size: 20px;
            font-weight: 900;
            color: #0f172a;
        }

        .report-kpi-card.highlight .report-kpi-value {
            color: var(--primary);
        }

        .ensan-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12.5px;
            margin-bottom: 30px;
            position: relative;
            z-index: 1;
        }

        .ensan-table th {
            background-color: var(--primary) !important;
            color: #ffffff !important;
            padding: 10px 8px;
            text-align: center;
            border: 1px solid #065f46;
            font-weight: 700;
        }

        .ensan-table td {
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: center;
            vertical-align: middle;
        }

        .ensan-table tbody tr:nth-child(even) {
            background-color: #f8fafc;
        }

        .ensan-table tfoot td {
            background-color: #f1f5f9;
            font-weight: 800;
            border-top: 2px solid var(--primary);
            padding: 10px;
        }

        .badge,
        .badge * {
            color: #ffffff !important;
            font-weight: 700 !important;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        .report-signatures {
            border-top: 1.5px dashed #cbd5e1;
            padding-top: 24px;
            margin-top: 30px;
            position: relative;
            z-index: 1;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
            }

            .no-print-toolbar {
                display: none !important;
            }

            .print-paper-sheet {
                border: none !important;
                box-shadow: none !important;
                padding: 15px !important;
                max-width: 100% !important;
            }

            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            .ensan-table th {
                background-color: #047857 !important;
                color: #ffffff !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Toolbar (Hidden when printing) --}}
    <div class="no-print-toolbar">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('donors.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-right me-1"></i> رجوع لقائمة المتبرعين
            </a>
            <span class="text-muted">|</span>
            <span class="fw-bold fs-6">{{ $reportTitle }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-dark btn-sm" onclick="window.history.back()">
                <i class="bi bi-x-circle me-1"></i> إلغاء
            </button>
            <button type="button" class="btn btn-success btn-sm px-4 fw-bold shadow-sm" onclick="window.print()">
                <i class="bi bi-printer-fill me-1"></i> أمر الطباعة الآن
            </button>
        </div>
    </div>

    {{-- Printable Paper Sheet --}}
    <div class="print-paper-sheet">
        
        {{-- Watermark --}}
        <div class="sheet-watermark">
            <img src="{{ asset('images/logo.png') }}" alt="" style="width: 320px; filter: grayscale(100%);">
        </div>

        {{-- Official Header --}}
        <div class="ensan-report-header">
            <div style="text-align: right; line-height: 1.45;">
                <div style="font-size: 13px; font-weight: 800; color: #1e293b;">جمهورية مصر العربية</div>
                <div style="font-size: 12px; font-weight: 700; color: #334155;">وزارة التضامن الاجتماعي</div>
                <div style="font-size: 18px; font-weight: 900; color: #047857; margin-top: 2px;">مؤسسة إنسان الخيرية</div>
                <div style="font-size: 11px; font-weight: 600; color: #64748b;">مشهرة برقم رسمي للعمل الخيري والتنموي</div>
            </div>

            <div style="text-align: center; padding: 0 10px;">
                <div style="display: inline-block; background: #ecfdf5; border: 2px solid #059669; border-radius: 8px; padding: 8px 24px;">
                    <div style="margin: 0; font-size: 20px; font-weight: 900; color: #065f46;">{{ $reportTitle }}</div>
                    <div style="font-size: 11px; font-weight: 800; color: #047857; letter-spacing: 1px; font-family: monospace; margin-top: 2px;">OFFICIAL DONORS REPORT & STATEMENT</div>
                </div>
            </div>

            <div style="text-align: right; line-height: 1.6; min-width: 190px;">
                <div style="background: #f8fafc; border: 1.5px solid #cbd5e1; border-radius: 6px; padding: 6px 12px; font-size: 11.5px;">
                    <div><span style="color: #64748b; font-weight: 700;">تاريخ التقرير:</span> <strong style="font-family: monospace; color: #0f172a;">{{ now()->format('Y/m/d') }}</strong></div>
                    <div><span style="color: #64748b; font-weight: 700;">وقت الطباعة:</span> <strong style="font-family: monospace; color: #0f172a;">{{ now()->format('H:i A') }}</strong></div>
                    <div><span style="color: #64748b; font-weight: 700;">حرر بواسطة:</span> <strong style="color: #0f172a;">{{ auth()->user()?->name ?? 'النظام الآلي' }}</strong></div>
                </div>
            </div>
        </div>

        {{-- KPI Summary Grid --}}
        <div class="report-kpi-grid">
            <div class="report-kpi-card">
                <div class="report-kpi-title">إجمالي عدد المتبرعين بالكشف</div>
                <div class="report-kpi-value">{{ count($donors) }} متبرع</div>
            </div>
            <div class="report-kpi-card highlight">
                <div class="report-kpi-title" style="color: #065f46;">إجمالي حصيلة التبرعات المحصلة</div>
                <div class="report-kpi-value">{{ number_format($totalAmount, 0) }} ج.م</div>
            </div>
            <div class="report-kpi-card">
                <div class="report-kpi-title">إجمالي عدد حركات التبرع</div>
                <div class="report-kpi-value">{{ number_format($totalCount, 0) }} تبرع</div>
            </div>
        </div>

        {{-- Donors Table --}}
        <table class="ensan-table">
            <thead>
                <tr>
                    <th style="width: 35px;">#</th>
                    <th style="width: 120px;">كود المتبرع</th>
                    <th style="text-align: right;">اسم المتبرع</th>
                    <th style="width: 90px;">نوع الجهة</th>
                    <th style="width: 120px;">رقم الهاتف</th>
                    <th style="width: 100px;">تصنيف التبرع</th>
                    <th style="width: 130px;">إجمالي التبرعات</th>
                    <th style="width: 80px;">عدد المرات</th>
                    <th style="width: 80px;">الحالة</th>
                </tr>
            </thead>
            <tbody>
                @forelse($donors as $index => $donor)
                    @php $st = $donStats->get($donor->id); @endphp
                    <tr>
                        <td style="font-weight: bold;">{{ $index + 1 }}</td>
                        <td style="font-family: monospace; font-weight: 800; color: #047857;">{{ $donor->code ?? ('DON-'.$donor->id) }}</td>
                        <td style="text-align: right; font-weight: 700; color: #0f172a;">{{ $donor->name }}</td>
                        <td>
                            <span class="badge text-white shadow-sm" style="background-color: {{ $donor->type === 'individual' ? '#2563eb' : '#0891b2' }} !important; color: #ffffff !important; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                {{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }}
                            </span>
                        </td>
                        <td dir="ltr" style="font-family: monospace; font-weight: 600;">{{ $donor->phone ?? '—' }}</td>
                        <td>{{ $donor->classification === 'recurring' ? 'متكرر' : 'مرة واحدة' }}</td>
                        <td style="font-weight: 800; color: #0f172a;">{{ $st ? number_format($st->total, 0) : '0' }} ج.م</td>
                        <td style="font-weight: 700;">{{ $st ? $st->count : 0 }}</td>
                        <td>
                            <span class="badge text-white shadow-sm" style="background-color: {{ $donor->active ? '#059669' : '#64748b' }} !important; color: #ffffff !important; font-weight: 700; padding: 4px 10px; border-radius: 6px;">
                                {{ $donor->active ? 'نشط' : 'غير نشط' }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">لا توجد بيانات مطابقة للعرض</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" style="text-align: right; padding-right: 14px;">الإجمالي العام للكشف:</td>
                    <td style="color: #047857; font-size: 14px;">{{ number_format($totalAmount, 0) }} ج.م</td>
                    <td style="font-size: 14px;">{{ number_format($totalCount, 0) }}</td>
                    <td>—</td>
                </tr>
            </tfoot>
        </table>

        {{-- Signatures Footer --}}
        <div class="report-signatures">
            <div class="row align-items-center text-center g-3">
                <div class="col-4">
                    <div class="text-muted small mb-2">الموظف المختص بإعداد التقرير</div>
                    <div style="border-bottom: 1.5px dashed #475569; width: 140px; margin: 0 auto; height: 18px;"></div>
                    <div class="fw-bold small mt-1">{{ auth()->user()?->name ?? '..................' }}</div>
                </div>
                <div class="col-4">
                    <div class="text-muted small mb-1">ختم المؤسسة الرسمي</div>
                    <div style="width: 60px; height: 60px; border: 2px solid #059669; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; opacity: 0.35;">
                        <i class="bi bi-patch-check-fill text-success fs-2"></i>
                    </div>
                </div>
                <div class="col-4">
                    <div class="text-muted small mb-2">الاعتماد الإداري / المالي</div>
                    <div style="border-bottom: 1.5px dashed #475569; width: 140px; margin: 0 auto; height: 18px;"></div>
                    <div class="text-muted small mt-1">توقيع معتمد</div>
                </div>
            </div>
            <div class="text-center mt-3 pt-2 border-top text-muted" style="font-size: 11px;">
                تم استخراج هذا الكشف آلياً بواسطة نظام مؤسسة إنسان الخيرية لإدارة الموارد والتبرعات — يحظر التعديل اليدوي.
            </div>
        </div>

    </div>

</body>
</html>
