{{-- Universal Print Preview Modal (معاينة قبل الطباعة الشاملة لنظام مؤسسة إنسان) --}}
<style>
    /* Virtual Paper Workbench Canvas */
    .print-preview-canvas {
        background: #f1f5f9 !important;
        background-image: radial-gradient(rgba(15, 23, 42, 0.08) 1px, transparent 0) !important;
        background-size: 20px 20px !important;
        min-height: 80vh !important;
        overflow-y: auto !important;
        padding: 24px 16px !important;
    }

    /* Force Authentic White Paper Sheet regardless of system dark/light theme */
    #universalPrintPaper,
    #universalPrintPaper *,
    body.theme-dark #universalPrintPaper,
    body.theme-dark #universalPrintPaper *,
    html.theme-dark #universalPrintPaper,
    html.theme-dark #universalPrintPaper *,
    [data-bs-theme="dark"] #universalPrintPaper,
    [data-bs-theme="dark"] #universalPrintPaper * {
        color-scheme: light !important;
        box-sizing: border-box !important;
        font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif !important;
    }

    #universalPrintPaper {
        background: #ffffff !important;
        color: #0f172a !important;
        direction: rtl !important;
        border: none !important;
        outline: none !important;
        border-radius: 8px !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important;
        padding: 0 !important;
        margin: 0 auto !important;
        transform-origin: top center;
        transition: transform 0.2s ease, width 0.2s ease;
    }

    /* When containing a self-contained official card, ensure no duplicate outer border, padding, or excess height */
    #universalPrintPaper:has(.official-voucher-card),
    #universalPrintPaper.is-voucher {
        border: none !important;
        outline: none !important;
        padding: 0 !important;
        background: transparent !important;
        box-shadow: none !important;
        min-height: auto !important;
        height: auto !important;
    }

    #universalPrintPaper.portrait {
        width: 210mm !important;
        max-width: 100% !important;
        min-height: auto !important;
    }

    #universalPrintPaper.landscape {
        width: 297mm !important;
        max-width: 100% !important;
        min-height: auto !important;
    }

    .a4-paper-sheet,
    .official-voucher-card {
        min-height: auto !important;
    }

    /* Dark Mode Defeat: Prevent white-on-white or dark-on-dark text */
    #universalPrintPaper div,
    #universalPrintPaper p,
    #universalPrintPaper span,
    #universalPrintPaper td,
    #universalPrintPaper th,
    #universalPrintPaper strong,
    #universalPrintPaper b,
    #universalPrintPaper small,
    #universalPrintPaper h1,
    #universalPrintPaper h2,
    #universalPrintPaper h3,
    #universalPrintPaper h4,
    #universalPrintPaper h5,
    #universalPrintPaper h6,
    body.theme-dark #universalPrintPaper div,
    body.theme-dark #universalPrintPaper p,
    body.theme-dark #universalPrintPaper span,
    body.theme-dark #universalPrintPaper td,
    body.theme-dark #universalPrintPaper th,
    body.theme-dark #universalPrintPaper strong,
    body.theme-dark #universalPrintPaper b,
    body.theme-dark #universalPrintPaper small,
    body.theme-dark #universalPrintPaper h1,
    body.theme-dark #universalPrintPaper span:not(.badge):not(.badge *),
    body.theme-dark #universalPrintPaper td:not(:has(.badge)),
    body.theme-dark #universalPrintPaper th,
    body.theme-dark #universalPrintPaper strong:not(.badge *),
    body.theme-dark #universalPrintPaper b:not(.badge *),
    body.theme-dark #universalPrintPaper small:not(.badge *),
    body.theme-dark #universalPrintPaper h1,
    body.theme-dark #universalPrintPaper h2,
    body.theme-dark #universalPrintPaper h3,
    body.theme-dark #universalPrintPaper h4,
    body.theme-dark #universalPrintPaper h5,
    body.theme-dark #universalPrintPaper h6,
    html.theme-dark #universalPrintPaper div:not(.badge),
    html.theme-dark #universalPrintPaper p,
    html.theme-dark #universalPrintPaper span:not(.badge):not(.badge *),
    html.theme-dark #universalPrintPaper td:not(:has(.badge)),
    html.theme-dark #universalPrintPaper th,
    html.theme-dark #universalPrintPaper strong:not(.badge *),
    html.theme-dark #universalPrintPaper b:not(.badge *),
    html.theme-dark #universalPrintPaper small:not(.badge *),
    html.theme-dark #universalPrintPaper h1,
    html.theme-dark #universalPrintPaper h2,
    html.theme-dark #universalPrintPaper h3,
    html.theme-dark #universalPrintPaper h4,
    html.theme-dark #universalPrintPaper h5,
    html.theme-dark #universalPrintPaper h6 {
        color: #0f172a !important;
    }

    /* Badges on Paper: Always solid color with WHITE text */
    #universalPrintPaper .badge,
    #universalPrintPaper .badge *,
    #universalPrintPaper span.badge,
    #universalPrintPaper td span.badge,
    body.theme-dark #universalPrintPaper .badge,
    body.theme-dark #universalPrintPaper .badge *,
    body.theme-dark #universalPrintPaper span.badge,
    body.theme-dark #universalPrintPaper td span.badge,
    html.theme-dark #universalPrintPaper .badge,
    html.theme-dark #universalPrintPaper .badge *,
    html.theme-dark #universalPrintPaper span.badge,
    html.theme-dark #universalPrintPaper td span.badge {
        color: #ffffff !important;
        font-weight: 700 !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
    }

    #universalPrintPaper .text-muted,
    body.theme-dark #universalPrintPaper .text-muted,
    html.theme-dark #universalPrintPaper .text-muted {
        color: #475569 !important;
    }

    #universalPrintPaper .text-success,
    body.theme-dark #universalPrintPaper .text-success,
    html.theme-dark #universalPrintPaper .text-success {
        color: #047857 !important;
    }

    #universalPrintPaper .text-primary,
    body.theme-dark #universalPrintPaper .text-primary,
    html.theme-dark #universalPrintPaper .text-primary {
        color: #1d4ed8 !important;
    }

    #universalPrintPaper .text-danger,
    body.theme-dark #universalPrintPaper .text-danger,
    html.theme-dark #universalPrintPaper .text-danger {
        color: #dc2626 !important;
    }

    #universalPrintPaper .text-warning,
    body.theme-dark #universalPrintPaper .text-warning,
    html.theme-dark #universalPrintPaper .text-warning {
        color: #b45309 !important;
    }

    /* Cards and KPI summaries on Paper: Always PURE WHITE */
    #universalPrintPaper,
    body.theme-dark #universalPrintPaper,
    html.theme-dark #universalPrintPaper,
    [data-bs-theme="dark"] #universalPrintPaper {
        --bg-card: #ffffff !important;
        --ws-bg-card-header: #ffffff !important;
        --bs-card-bg: #ffffff !important;
        --bs-body-bg: #ffffff !important;
        --bs-card-color: #0f172a !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
    }

    #universalPrintPaper .card,
    #universalPrintPaper .card-header,
    #universalPrintPaper .card-body,
    #universalPrintPaper .card-footer,
    #universalPrintPaper .stat-card,
    #universalPrintPaper .kpi-card,
    #universalPrintPaper .chart-container,
    #universalPrintPaper .list-group,
    #universalPrintPaper .list-group-item,
    #universalPrintPaper [class*="card"],
    body.theme-dark #universalPrintPaper .card,
    body.theme-dark #universalPrintPaper .card-header,
    body.theme-dark #universalPrintPaper .card-body,
    body.theme-dark #universalPrintPaper .card-footer,
    body.theme-dark #universalPrintPaper .stat-card,
    body.theme-dark #universalPrintPaper .kpi-card,
    body.theme-dark #universalPrintPaper .chart-container,
    body.theme-dark #universalPrintPaper .list-group,
    body.theme-dark #universalPrintPaper .list-group-item,
    body.theme-dark #universalPrintPaper [class*="card"],
    html.theme-dark #universalPrintPaper .card,
    html.theme-dark #universalPrintPaper .card-header,
    html.theme-dark #universalPrintPaper .card-body,
    html.theme-dark #universalPrintPaper .card-footer,
    html.theme-dark #universalPrintPaper .stat-card,
    html.theme-dark #universalPrintPaper .kpi-card,
    html.theme-dark #universalPrintPaper .chart-container,
    html.theme-dark #universalPrintPaper .list-group,
    html.theme-dark #universalPrintPaper .list-group-item,
    html.theme-dark #universalPrintPaper [class*="card"],
    [data-bs-theme="dark"] #universalPrintPaper .card,
    [data-bs-theme="dark"] #universalPrintPaper .card-header,
    [data-bs-theme="dark"] #universalPrintPaper .card-body,
    [data-bs-theme="dark"] #universalPrintPaper .card-footer,
    [data-bs-theme="dark"] #universalPrintPaper .stat-card,
    [data-bs-theme="dark"] #universalPrintPaper .kpi-card,
    [data-bs-theme="dark"] #universalPrintPaper .chart-container,
    [data-bs-theme="dark"] #universalPrintPaper .list-group,
    [data-bs-theme="dark"] #universalPrintPaper .list-group-item,
    [data-bs-theme="dark"] #universalPrintPaper [class*="card"] {
        background-color: #ffffff !important;
        background: #ffffff !important;
        color: #0f172a !important;
        border-color: #e2e8f0 !important;
        box-shadow: none !important;
    }

    /* Crisp Official Table Styling on Paper */
    #universalPrintPaper table,
    body.theme-dark #universalPrintPaper table,
    html.theme-dark #universalPrintPaper table {
        width: 100% !important;
        background-color: #ffffff !important;
        color: #0f172a !important;
        border-collapse: collapse !important;
        margin-bottom: 0 !important;
        border: 1px solid #cbd5e1 !important;
    }

    #universalPrintPaper table thead th,
    body.theme-dark #universalPrintPaper table thead th,
    html.theme-dark #universalPrintPaper table thead th {
        background-color: #047857 !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        border: 1px solid #065f46 !important;
        padding: 8px 10px !important;
        text-align: center !important;
    }

    #universalPrintPaper table thead th *,
    body.theme-dark #universalPrintPaper table thead th *,
    html.theme-dark #universalPrintPaper table thead th * {
        color: #ffffff !important;
    }

    #universalPrintPaper table tbody td,
    body.theme-dark #universalPrintPaper table tbody td,
    html.theme-dark #universalPrintPaper table tbody td {
        background-color: #ffffff !important;
        color: #0f172a !important;
        border: 1px solid #cbd5e1 !important;
        padding: 6px 10px !important;
    }

    #universalPrintPaper table tbody tr:nth-child(even) td,
    body.theme-dark #universalPrintPaper table tbody tr:nth-child(even) td,
    html.theme-dark #universalPrintPaper table tbody tr:nth-child(even) td {
        background-color: #f8fafc !important;
    }

    #universalPrintPaper table tfoot td,
    body.theme-dark #universalPrintPaper table tfoot td,
    html.theme-dark #universalPrintPaper table tfoot td {
        background-color: #f1f5f9 !important;
        color: #0f172a !important;
        font-weight: 800 !important;
        border: 1px solid #cbd5e1 !important;
        border-top: 2px solid #047857 !important;
        padding: 8px 10px !important;
    }
</style>

<div class="modal fade" id="universalPrintPreviewModal" tabindex="-1" aria-labelledby="universalPrintPreviewModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-fullscreen-lg-down modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-2xl rounded-4 overflow-hidden">
            <!-- Modal Header / Toolbar (Image 2 Look: Clean Light Bar with Green Accent) -->
            <div class="modal-header bg-light py-2 px-3 d-flex justify-content-between align-items-center border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-file-earmark-text-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="universalPrintPreviewModalLabel">
                            معاينة الوثيقة والتقرير الرسمي
                        </h5>
                        <small class="text-muted" id="universalPreviewDocSubtitle">هذا هو الشكل النهائي الدقيق للمستند كما سيظهر في الطباعة (A4)</small>
                    </div>
                </div>

                {{-- Preview Controls Toolbar --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Orientation Toggle --}}
                    <div class="btn-group btn-group-sm bg-white border rounded p-0.5" role="group">
                        <button type="button" class="btn btn-sm btn-outline-success active px-2 py-1" id="btnPreviewPortrait" onclick="window.EnsanPrintPreview.setOrientation('portrait')" title="وضع طولي (A4 Portrait)">
                            <i class="bi bi-file-earmark me-1"></i> طولي
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-success px-2 py-1" id="btnPreviewLandscape" onclick="window.EnsanPrintPreview.setOrientation('landscape')" title="وضع عرضي (A4 Landscape)">
                            <i class="bi bi-file-earmark-break me-1"></i> عرضي
                        </button>
                    </div>

                    {{-- Zoom / Scale Controls --}}
                    <div class="btn-group btn-group-sm bg-white border rounded p-0.5 d-none d-md-inline-flex" role="group">
                        <button type="button" class="btn btn-sm btn-light border-0 px-2" onclick="window.EnsanPrintPreview.zoom(-0.1)" title="تصغير">
                            <i class="bi bi-zoom-out text-dark"></i>
                        </button>
                        <span class="px-2 text-dark small d-flex align-items-center font-monospace" id="previewZoomLevel">100%</span>
                        <button type="button" class="btn btn-sm btn-light border-0 px-2" onclick="window.EnsanPrintPreview.zoom(0.1)" title="تكبير">
                            <i class="bi bi-zoom-in text-dark"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-light border-0 px-2" onclick="window.EnsanPrintPreview.resetZoom()" title="إعادة الضبط للحجم الطبيعي">
                            <i class="bi bi-arrows-fullscreen text-dark"></i>
                        </button>
                    </div>

                    {{-- Print Now Button --}}
                    <button type="button" class="btn btn-success btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-2" onclick="window.EnsanPrintPreview.executePrint()">
                        <i class="bi bi-printer me-1"></i>
                        <span>طباعة الآن</span>
                    </button>

                    {{-- Close Button --}}
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body (Virtual Paper Workbench) -->
            <div class="modal-body p-3 p-md-4 print-preview-canvas" id="printPreviewCanvas" style="background: #f1f5f9 !important;">
                <div class="d-flex justify-content-center">
                    <div id="universalPrintPaper" class="a4-paper-sheet portrait position-relative"
                         style="background: #ffffff !important; color: #0f172a !important; border: none !important; outline: none !important; border-radius: 8px !important; box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15) !important; padding: 0 !important;">
                        
                        {{-- Background Watermark --}}
                        <div class="print-paper-watermark position-absolute top-50 start-50 translate-middle text-center" style="opacity: 0.035; pointer-events: none; z-index: 0; user-select: none;">
                            <img src="{{ asset('images/heart-icon.png') }}" alt="" style="width: 280px; filter: grayscale(100%);">
                        </div>

                        {{-- Official Header (ترويسة رسمية) --}}
                        <div class="print-paper-header pb-3 mb-3 border-bottom border-2 border-success position-relative" style="z-index: 1;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-start" style="line-height: 1.4;">
                                    <div class="small fw-bold" style="color: #475569 !important;">جمهورية مصر العربية</div>
                                    <div class="small fw-bold" style="color: #475569 !important;">وزارة التضامن الاجتماعي</div>
                                    <div class="fw-bolder fs-6" style="color: #0f172a !important;">مؤسسة إنسان الخيرية</div>
                                    <div style="font-size: 0.72rem; color: #64748b !important;">مشهرة برقم رسمي للعمل التنموي والخيري</div>
                                </div>
                                <div class="text-center">
                                    <img src="{{ asset('images/heart-icon.png') }}" alt="شعار إنسان" style="height: 48px; width: auto;" onerror="this.style.display='none'">
                                    <div class="fw-bold fs-5 mt-1" style="color: #047857 !important;" id="universalPreviewHeading">وثيقة رسمية معتمدة</div>
                                </div>
                                <div class="text-end" style="line-height: 1.5; font-size: 0.78rem;">
                                    <div><span style="color: #64748b !important;">تاريخ الطباعة:</span> <span class="fw-bold font-monospace" style="color: #0f172a !important;">{{ date('Y/m/d') }}</span></div>
                                    <div><span style="color: #64748b !important;">وقت الطباعة:</span> <span class="fw-bold font-monospace" style="color: #0f172a !important;">{{ date('H:i A') }}</span></div>
                                    <div><span style="color: #64748b !important;">المستخدم:</span> <span class="fw-bold" style="color: #0f172a !important;">{{ auth()->user()->name ?? 'مستخدم النظام' }}</span></div>
                                    <span class="badge text-white px-2.5 py-1 mt-1 shadow-sm" style="background-color: #059669 !important; color: #ffffff !important; font-weight: 700;">
                                        <i class="bi bi-shield-check me-1" style="color: #ffffff !important;"></i> وثيقة رسمية
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Injected Cloned Content Container --}}
                        <div id="universalPrintPaperBody" class="print-paper-content position-relative" style="z-index: 1;">
                            <!-- Injected Dynamically by JS -->
                        </div>

                        {{-- Official Footer (تذييل واعتمادات رسمية) --}}
                        <div class="print-paper-footer pt-2 mt-2 border-top border-dark border-opacity-25 position-relative" style="z-index: 1; break-inside: avoid !important; page-break-inside: avoid !important;">
                            <div class="row align-items-center text-center g-1 small">
                                <div class="col-4">
                                    <div class="mb-1" style="color: #475569 !important; font-size: 0.78rem;">الموظف المسؤول / المختص</div>
                                    <div class="border-bottom border-dark border-dashed mx-auto" style="width: 110px; height: 12px;"></div>
                                    <div class="fw-bold small mt-1" style="color: #0f172a !important;">{{ auth()->user()->name ?? '..................' }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-1" style="color: #475569 !important; font-size: 0.78rem;">ختم المؤسسة الرسمي</div>
                                    <div class="rounded-circle border border-2 border-success mx-auto d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; opacity: 0.35;">
                                        <i class="bi bi-patch-check text-success fs-4"></i>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-1" style="color: #475569 !important; font-size: 0.78rem;">الاعتماد الإداري / المالي</div>
                                    <div class="border-bottom border-dark border-dashed mx-auto" style="width: 110px; height: 12px;"></div>
                                    <div class="small mt-1" style="color: #64748b !important;">توقيع معتمد</div>
                                </div>
                            </div>
                            <div class="text-center pt-1 mt-1 border-top" style="font-size: 0.68rem; color: #64748b !important;">
                                <div>تم استخراج هذه الوثيقة آلياً من نظام مؤسسة إنسان الخيرية — يحظر التعديل اليدوي أو الكشط.</div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                <div class="text-muted small d-flex align-items-center gap-1">
                    <i class="bi bi-info-circle text-primary"></i>
                    <span>نصيحة طباعة: يفضل اختيار <strong>(حفظ بتنسيق PDF أو طباعة ملونة)</strong> وتفعيل <strong>الرسومات في الخلفية</strong>.</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" class="btn btn-success btn-sm px-4 fw-bold shadow-sm" onclick="window.EnsanPrintPreview.executePrint()">
                        <i class="bi bi-printer me-1"></i> أمر الطباعة
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
