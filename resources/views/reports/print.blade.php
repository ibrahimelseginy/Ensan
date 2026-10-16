<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        @if($tab === 'all') التقرير الإحصائي الشامل
        @elseif($tab === 'donations') تقرير التبرعات والمتبرعين
        @elseif($tab === 'beneficiaries') تقرير المستفيدين والمساعدات
        @elseif($tab === 'guest_houses') تقرير دور الضيافة وإشغال الأسرة
        @elseif($tab === 'inventory') تقرير المخازن والمستودعات
        @elseif($tab === 'finance') تقرير المالية والمصروفات
        @elseif($tab === 'campaigns') تقرير الحملات والمشاريع
        @elseif($tab === 'volunteers') تقرير المتطوعين والمهام
        @elseif($tab === 'complaints') تقرير الشكاوى والمقترحات
        @else تقرير إحصائي @endif
        — مؤسسة إنسان الخيرية
    </title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #047857;
            --primary-dark: #065f46;
            --primary-light: #ecfdf5;
        }

        body {
            font-family: 'Cairo', -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
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
            padding: 35px;
            position: relative;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        }

        .ensan-report-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 2px solid var(--primary);
            padding-bottom: 16px;
            margin-bottom: 20px;
        }

        .report-kpi-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin-bottom: 24px;
        }

        .report-kpi-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 10px 14px;
            text-align: center;
        }

        .report-kpi-card.highlight {
            background: #ecfdf5;
            border-color: #a7f3d0;
        }

        .report-kpi-title {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            margin-bottom: 4px;
        }

        .report-kpi-value {
            font-size: 17px;
            font-weight: 900;
            color: #0f172a;
        }

        .table-print {
            width: 100%;
            margin-bottom: 20px;
            font-size: 12px;
            border-collapse: collapse;
        }

        .table-print th {
            background: #f1f5f9;
            color: #1e293b;
            font-weight: 800;
            padding: 8px 10px;
            border: 1px solid #cbd5e1;
            text-align: right;
        }

        .table-print td {
            padding: 7px 10px;
            border: 1px solid #e2e8f0;
            color: #334155;
            vertical-align: middle;
        }

        .table-print tr:nth-child(even) td {
            background: #f8fafc;
        }

        .section-heading {
            font-size: 14px;
            font-weight: 800;
            color: var(--primary-dark);
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 6px;
            margin-top: 20px;
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .signatures-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px dashed #cbd5e1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            text-align: center;
            font-size: 12px;
        }

        .sig-box {
            padding: 0 10px;
        }

        .sig-title {
            font-weight: 700;
            color: #475569;
            margin-bottom: 45px;
        }

        .sig-line {
            border-top: 1px dotted #94a3b8;
            margin: 0 20px;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print-toolbar {
                display: none !important;
            }
            .print-paper-sheet {
                border: none !important;
                outline: none !important;
                box-shadow: none !important;
                padding: 15px !important;
                max-width: 100% !important;
            }
            @page {
                size: A4;
                margin: 12mm;
            }
        }
    </style>
</head>
<body>

    {{-- Top Action Bar --}}
    <div class="no-print-toolbar">
        <div class="d-flex align-items-center gap-2">
            <span class="badge bg-success rounded-pill px-3 py-2 fs-6">
                <i class="bi bi-file-earmark-text me-1"></i>
                @if($tab === 'all') التقرير الشامل الجماعي
                @elseif($tab === 'donations') تقرير التبرعات المنفصل
                @elseif($tab === 'beneficiaries') تقرير المستفيدين المنفصل
                @elseif($tab === 'guest_houses') تقرير دور الضيافة المنفصل
                @elseif($tab === 'inventory') تقرير المخازن المنفصل
                @elseif($tab === 'finance') تقرير المالية المنفصل
                @elseif($tab === 'campaigns') تقرير الحملات المنفصل
                @elseif($tab === 'volunteers') تقرير المتطوعين المنفصل
                @elseif($tab === 'complaints') تقرير الشكاوى المنفصل
                @endif
            </span>
            <small class="text-muted">الفترة: {{ $range === 'all' ? 'كافة السجلات' : ($from . ' إلى ' . $to) }}</small>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary btn-sm px-4 fw-bold">
                <i class="bi bi-printer me-1"></i> طباعة فورية
            </button>
            <button onclick="window.close()" class="btn btn-outline-secondary btn-sm px-3">
                إغلاق
            </button>
        </div>
    </div>

    {{-- Printable Sheet --}}
    <div class="print-paper-sheet">

        {{-- Header --}}
        <div class="ensan-report-header">
            <div class="d-flex align-items-center gap-3">
                <img src="/images/heart-icon.png" alt="شعار إنسان" style="height: 55px; width: auto;" onerror="this.style.display='none'">
                <div>
                    <h5 class="fw-bold mb-0 text-success">مؤسسة إنسان الخيرية</h5>
                    <small class="text-muted d-block" style="font-size: 11px;">رقم الإشهار المركزي | محافظة كفر الشيخ والغربية</small>
                    <small class="text-muted" style="font-size: 11px;">نظام إدارة المؤسسة المتكامل</small>
                </div>
            </div>

            <div class="text-center">
                <h5 class="fw-bold mb-1 text-dark">
                    @if($tab === 'all') التقرير الإحصائي الشامل لجميع القطاعات
                    @elseif($tab === 'donations') التقرير التفصيلي للتبرعات والمتبرعين
                    @elseif($tab === 'beneficiaries') التقرير التفصيلي للمستفيدين والمساعدات
                    @elseif($tab === 'guest_houses') تقرير دور الضيافة وإشغال الأسرة
                    @elseif($tab === 'inventory') التقرير التفصيلي للمخازن والمستودعات
                    @elseif($tab === 'finance') تقرير التدفق المالي والمصروفات
                    @elseif($tab === 'campaigns') تقرير متابعة إنجاز الحملات والمشاريع
                    @elseif($tab === 'volunteers') تقرير أنشطة المتطوعين وساعات العمل
                    @elseif($tab === 'complaints') تقرير خدمة المستفيدين والشكاوى
                    @endif
                </h5>
                <span class="badge bg-light text-dark border px-3 py-1" style="font-size: 11px;">
                    كود التقرير: RPT-{{ strtoupper($tab) }}-{{ date('Ymd-Hi') }}
                </span>
            </div>

            <div class="text-start" style="font-size: 11px;">
                <div><strong>تاريخ الاستخراج:</strong> {{ date('Y-m-d H:i') }}</div>
                <div><strong>الفترة:</strong> {{ $range === 'all' ? 'كافة السجلات' : ($from . ' إلى ' . $to) }}</div>
                <div><strong>الفرع / المحافظة:</strong> {{ $governorate === 'all' ? 'جميع الفروع' : $governorate }}</div>
                <div><strong>المستخرج:</strong> {{ auth()->user()->name ?? 'الإدارة' }}</div>
            </div>
        </div>

        {{-- ========================================================================= --}}
        {{-- SECTION 1: MASTER COLLECTIVE (التقرير الشامل) --}}
        {{-- ========================================================================= --}}
        @if($tab === 'all')
            <div class="report-kpi-grid">
                <div class="report-kpi-card highlight">
                    <div class="report-kpi-title">إجمالي التبرعات المحصلة</div>
                    <div class="report-kpi-value text-success">{{ number_format($donationsTotalAmount, 2) }} ج.م</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">إجمالي المستفيدين</div>
                    <div class="report-kpi-value text-primary">{{ $beneficiariesTotal }} مستفيد</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">نسبة إشغال دور الضيافة</div>
                    <div class="report-kpi-value text-purple">{{ $overallOccupancyRate }}% ({{ $occupiedBeds }}/{{ $totalBeds }})</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">صافي الرصيد المخزني</div>
                    <div class="report-kpi-value text-dark">{{ number_format($netInventoryQty, 0) }} وحدة</div>
                </div>
            </div>

            <div class="section-heading">
                <span>1. جدول المؤشرات التنفيذية الشاملة للجمعية</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th style="width: 40px;">#</th>
                        <th>المؤشر والقطاع</th>
                        <th class="text-end">القيمة الإحصائية</th>
                    </tr>
                </thead>
                <tbody>
                    @php $idx = 1; @endphp
                    @foreach($kpis as $title => $val)
                        <tr>
                            <td>{{ $idx++ }}</td>
                            <td class="fw-bold">{{ $title }}</td>
                            <td class="text-end fw-bold text-success">{{ $val }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="section-heading">
                <span>2. إشغال دور الضيافة (كفر الشيخ والغربية)</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>دار الضيافة</th>
                        <th>المحافظة</th>
                        <th>الأجنحة</th>
                        <th>إجمالي الأسرة</th>
                        <th>المشغول</th>
                        <th>المتاح</th>
                        <th>نسبة الإشغال</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guestHousesList as $gh)
                        <tr>
                            <td class="fw-bold">{{ $gh->name }}</td>
                            <td>{{ $gh->governorate ?? '-' }}</td>
                            <td>{{ $gh->wings_count }}</td>
                            <td>{{ $gh->beds_count }}</td>
                            <td class="text-danger fw-bold">{{ $gh->occupied_beds }}</td>
                            <td class="text-success fw-bold">{{ $gh->available_beds }}</td>
                            <td class="fw-bold">{{ $gh->occupancy_rate }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="section-heading">
                <span>3. خلاصة الحملات والمشاريع</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>اسم الحملة</th>
                        <th>الموسم</th>
                        <th>الهدف المالي</th>
                        <th>المحصل</th>
                        <th>نسبة التحقيق</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($campaignsList as $c)
                        <tr>
                            <td class="fw-bold">{{ $c->name }}</td>
                            <td>{{ $c->season_title ?? '-' }}</td>
                            <td>{{ number_format($c->goal_amount, 2) }} ج.م</td>
                            <td class="fw-bold text-success">{{ number_format($c->current_amount, 2) }} ج.م</td>
                            <td class="fw-bold">{{ $c->progress_percentage }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- ========================================================================= --}}
        {{-- SECTION 2: DONATIONS PRINT --}}
        {{-- ========================================================================= --}}
        @if($tab === 'donations')
            <div class="report-kpi-grid">
                <div class="report-kpi-card highlight">
                    <div class="report-kpi-title">إجمالي التبرعات</div>
                    <div class="report-kpi-value text-success">{{ number_format($donationsTotalAmount, 2) }} ج.م</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">التبرعات النقدية</div>
                    <div class="report-kpi-value text-dark">{{ number_format($donationsCash, 2) }} ج.م</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">التبرعات العينية</div>
                    <div class="report-kpi-value text-info">{{ number_format($donationsInKind, 2) }} ج.م</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">عدد العمليات</div>
                    <div class="report-kpi-value text-primary">{{ $donationsCount }}</div>
                </div>
            </div>

            <div class="section-heading">
                <span>سجل عمليات التبرع التفصيلي</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>رقم الإيصال</th>
                        <th>المتبرع</th>
                        <th>المبلغ / القيمة</th>
                        <th>النوع</th>
                        <th>الغرض / التصنيف</th>
                        <th>طريقة الدفع</th>
                        <th>التاريخ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentDonations as $d)
                        <tr>
                            <td class="fw-bold">{{ $d->receipt_number ?? ('#' . $d->id) }}</td>
                            <td>{{ $d->donor->name ?? 'فاعل خير' }}</td>
                            <td class="fw-bold text-success">{{ number_format($d->type === 'cash' ? $d->amount : $d->estimated_value, 2) }} ج.م</td>
                            <td>{{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}</td>
                            <td>{{ $d->purpose_label ?? $d->category ?? 'عام' }}</td>
                            <td>{{ $d->payment_method_label ?? $d->payment_method ?? 'نقدي' }}</td>
                            <td>{{ $d->received_at ? $d->received_at->format('Y-m-d') : ($d->created_at ? $d->created_at->format('Y-m-d') : '-') }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-3">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- ========================================================================= --}}
        {{-- SECTION 3: BENEFICIARIES PRINT --}}
        {{-- ========================================================================= --}}
        @if($tab === 'beneficiaries')
            <div class="report-kpi-grid">
                <div class="report-kpi-card highlight">
                    <div class="report-kpi-title">إجمالي المستفيدين</div>
                    <div class="report-kpi-value text-primary">{{ $beneficiariesTotal }}</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">مجموع الكفالات الشهرية</div>
                    <div class="report-kpi-value text-success">{{ number_format($totalSponsorshipAmount, 0) }} ج.م</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">حالات معتمدة</div>
                    <div class="report-kpi-value text-dark">{{ $beneficiariesByStatus->where('status', 'approved')->first()->count ?? 0 }}</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">حالات قيد الدراسة</div>
                    <div class="report-kpi-value text-warning">{{ $beneficiariesByStatus->where('status', 'new')->first()->count ?? 0 }}</div>
                </div>
            </div>

            <div class="section-heading">
                <span>كشف بيانات المستفيدين والمساعدات</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>الاسم بالكامل</th>
                        <th>الرقم القومي</th>
                        <th>الهاتف</th>
                        <th>المحافظة / العنوان</th>
                        <th>نوع المساعدة</th>
                        <th>مبلغ الكفالة</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentBeneficiaries as $b)
                        <tr>
                            <td class="fw-bold">{{ $b->code ?? ('#' . $b->id) }}</td>
                            <td class="fw-bold">{{ $b->full_name }}</td>
                            <td>{{ $b->national_id ?? '-' }}</td>
                            <td>{{ $b->phone ?? '-' }}</td>
                            <td>{{ $b->address ?? '-' }}</td>
                            <td>{{ $b->assistance_type ?? 'عام' }}</td>
                            <td class="fw-bold text-success">{{ number_format((float)($b->monthly_sponsorship_amount ?? 0), 0) }} ج.م</td>
                            <td>{{ $b->status ?? 'نشط' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="8" class="text-center py-3">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- ========================================================================= --}}
        {{-- SECTION 4: GUEST HOUSES PRINT --}}
        {{-- ========================================================================= --}}
        @if($tab === 'guest_houses')
            <div class="report-kpi-grid">
                <div class="report-kpi-card highlight">
                    <div class="report-kpi-title">عدد دور الضيافة</div>
                    <div class="report-kpi-value text-primary">{{ $totalGuestHouses }}</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">إجمالي الأسرة</div>
                    <div class="report-kpi-value text-dark">{{ $totalBeds }} سرير</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">الأسرة المشغولة</div>
                    <div class="report-kpi-value text-danger">{{ $occupiedBeds }} سرير</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">نسبة الإشغال الإجمالية</div>
                    <div class="report-kpi-value text-success">{{ $overallOccupancyRate }}%</div>
                </div>
            </div>

            <div class="section-heading">
                <span>بيان الطاقة الاستيعابية بالأجنحة والأسرة</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>اسم دار الضيافة</th>
                        <th>المحافظة</th>
                        <th>الأجنحة</th>
                        <th>الأسرة الكلية</th>
                        <th>المشغول</th>
                        <th>المتاح</th>
                        <th>نسبة الإشغال</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($guestHousesList as $gh)
                        <tr>
                            <td class="fw-bold">{{ $gh->name }}</td>
                            <td>{{ $gh->governorate ?? '-' }}</td>
                            <td>{{ $gh->wings_count }} أجنحة</td>
                            <td>{{ $gh->beds_count }}</td>
                            <td class="text-danger fw-bold">{{ $gh->occupied_beds }}</td>
                            <td class="text-success fw-bold">{{ $gh->available_beds }}</td>
                            <td class="fw-bold">{{ $gh->occupancy_rate }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="section-heading">
                <span>قائمة النزلاء الحاليين بدار الضيافة</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>الكود</th>
                        <th>الاسم</th>
                        <th>دار الضيافة</th>
                        <th>الهاتف</th>
                        <th>نوع الدعم</th>
                        <th>تاريخ الدخول</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentGuestHousePatients as $p)
                        <tr>
                            <td class="fw-bold">{{ $p->code ?? ('#' . $p->id) }}</td>
                            <td class="fw-bold">{{ $p->full_name }}</td>
                            <td>{{ $p->guestHouse->name ?? '-' }}</td>
                            <td>{{ $p->phone ?? '-' }}</td>
                            <td>{{ $p->assistance_type ?? 'علاج وإيواء' }}</td>
                            <td>{{ $p->created_at ? $p->created_at->format('Y-m-d') : '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-3">لا يوجد نزلاء حالياً</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- ========================================================================= --}}
        {{-- SECTION 5: INVENTORY PRINT --}}
        {{-- ========================================================================= --}}
        @if($tab === 'inventory')
            <div class="report-kpi-grid">
                <div class="report-kpi-card highlight">
                    <div class="report-kpi-title">عدد المستودعات</div>
                    <div class="report-kpi-value text-dark">{{ $warehousesCount }}</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">الأصناف المعرفة</div>
                    <div class="report-kpi-value text-primary">{{ $itemsCount }}</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">صافي الرصيد المخزني</div>
                    <div class="report-kpi-value text-success">{{ number_format($netInventoryQty, 1) }}</div>
                </div>
                <div class="report-kpi-card">
                    <div class="report-kpi-title">أصناف منخفضة / نافذة</div>
                    <div class="report-kpi-value text-danger">{{ $lowStockCount }}</div>
                </div>
            </div>

            <div class="section-heading">
                <span>جدول أرصدة وحركة الأصناف بالمستودعات</span>
            </div>
            <table class="table-print">
                <thead>
                    <tr>
                        <th>كود الصنف</th>
                        <th>اسم الصنف</th>
                        <th>الوحدة</th>
                        <th>الوارد</th>
                        <th>المنصرف</th>
                        <th>الرصيد الحالي</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($itemsStockList as $item)
                        <tr>
                            <td>{{ $item->sku ?? ('#' . $item->id) }}</td>
                            <td class="fw-bold">{{ $item->name }}</td>
                            <td>{{ $item->unit ?? 'قطعة' }}</td>
                            <td class="text-success">{{ number_format((float)$item->total_in, 1) }}</td>
                            <td class="text-danger">{{ number_format((float)$item->total_out, 1) }}</td>
                            <td class="fw-bold">{{ number_format((float)$item->net_qty, 1) }}</td>
                            <td>{{ $item->net_qty <= 0 ? 'منخفض / نافذ' : 'متوفر' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center py-3">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
        @endif

        {{-- Signatures Section --}}
        <div class="signatures-section">
            <div class="sig-box">
                <div class="sig-title">إعداد ومراجعة التقرير</div>
                <div class="sig-line"></div>
                <small class="text-muted mt-2 d-block">مسؤول الإحصاء والتقارير</small>
            </div>
            <div class="sig-box">
                <div class="sig-title">التدقيق المالي / الإداري</div>
                <div class="sig-line"></div>
                <small class="text-muted mt-2 d-block">المدير المالي والتنفيذي</small>
            </div>
            <div class="sig-box">
                <div class="sig-title">اعتماد مجلس الإدارة</div>
                <div class="sig-line"></div>
                <small class="text-muted mt-2 d-block">رئيس مجلس الإدارة</small>
            </div>
        </div>

    </div>

</body>
</html>
