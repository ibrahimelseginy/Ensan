{{-- Universal Print Preview Modal (معاينة قبل الطباعة الشاملة لنظام مؤسسة إنسان) --}}
<style>
    /* Virtual Paper Workbench Canvas */
    .print-preview-canvas {
        background: #090e17 !important;
        background-image: radial-gradient(rgba(255, 255, 255, 0.06) 1px, transparent 0) !important;
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
    }

    #universalPrintPaper {
        background: #ffffff !important;
        color: #0f172a !important;
        direction: rtl !important;
        border: 2px solid #059669 !important;
        border-radius: 6px !important;
        box-shadow: 0 15px 45px rgba(0, 0, 0, 0.6) !important;
        padding: 24px 28px !important;
        margin: 0 auto !important;
        transform-origin: top center;
        transition: transform 0.2s ease, width 0.2s ease;
    }

    #universalPrintPaper.portrait {
        width: 210mm !important;
        max-width: 100% !important;
        min-height: 297mm !important;
    }

    #universalPrintPaper.landscape {
        width: 297mm !important;
        max-width: 100% !important;
        min-height: 210mm !important;
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

    /* Cards and KPI summaries on Paper */
    #universalPrintPaper .card,
    #universalPrintPaper .stat-card,
    #universalPrintPaper .kpi-card,
    #universalPrintPaper .chart-container,
    body.theme-dark #universalPrintPaper .card,
    body.theme-dark #universalPrintPaper .stat-card,
    body.theme-dark #universalPrintPaper .kpi-card,
    body.theme-dark #universalPrintPaper .chart-container {
        background: #f8fafc !important;
        border: 1px solid #cbd5e1 !important;
        color: #0f172a !important;
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
            <!-- Modal Header / Toolbar -->
            <div class="modal-header bg-dark text-white py-2 px-3 d-flex justify-content-between align-items-center border-bottom border-secondary border-opacity-25">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-success bg-opacity-20 text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="bi bi-file-earmark-pdf-fill fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0 fs-6" id="universalPrintPreviewModalLabel">
                            معاينة قبل الطباعة الرسمية
                        </h5>
                        <small class="text-white-50" id="universalPreviewDocSubtitle">محاكاة دقيقة لمظهر الورقة المطبوعة (A4)</small>
                    </div>
                </div>

                {{-- Preview Controls Toolbar --}}
                <div class="d-flex align-items-center gap-2">
                    {{-- Orientation Toggle --}}
                    <div class="btn-group btn-group-sm bg-secondary bg-opacity-25 rounded p-1" role="group">
                        <button type="button" class="btn btn-sm btn-outline-light active px-2 py-1" id="btnPreviewPortrait" onclick="window.EnsanPrintPreview.setOrientation('portrait')" title="وضع طولي (A4 Portrait)">
                            <i class="bi bi-file-earmark me-1"></i> طولي
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light px-2 py-1" id="btnPreviewLandscape" onclick="window.EnsanPrintPreview.setOrientation('landscape')" title="وضع عرضي (A4 Landscape)">
                            <i class="bi bi-file-earmark-break me-1"></i> عرضي
                        </button>
                    </div>

                    {{-- Zoom / Scale Controls --}}
                    <div class="btn-group btn-group-sm bg-secondary bg-opacity-25 rounded p-1 d-none d-md-inline-flex" role="group">
                        <button type="button" class="btn btn-sm btn-outline-light px-2" onclick="window.EnsanPrintPreview.zoom(-0.1)" title="تصغير">
                            <i class="bi bi-zoom-out"></i>
                        </button>
                        <span class="px-2 text-white small d-flex align-items-center font-monospace" id="previewZoomLevel">100%</span>
                        <button type="button" class="btn btn-sm btn-outline-light px-2" onclick="window.EnsanPrintPreview.zoom(0.1)" title="تكبير">
                            <i class="bi bi-zoom-in"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-light px-2" onclick="window.EnsanPrintPreview.resetZoom()" title="إعادة الضبط للحجم الطبيعي">
                            <i class="bi bi-arrows-fullscreen"></i>
                        </button>
                    </div>

                    {{-- Print Now Button --}}
                    <button type="button" class="btn btn-success btn-sm px-3 fw-bold shadow-sm d-flex align-items-center gap-2" onclick="window.EnsanPrintPreview.executePrint()">
                        <i class="bi bi-printer-fill fs-6"></i>
                        <span>أمر الطباعة الآن</span>
                    </button>

                    {{-- Close Button --}}
                    <button type="button" class="btn-close btn-close-white ms-2" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>

            <!-- Modal Body (Virtual Paper Workbench) -->
            <div class="modal-body p-3 p-md-4 print-preview-canvas" id="printPreviewCanvas" style="background: #090e17 !important;">
                <div class="d-flex justify-content-center">
                    <div id="universalPrintPaper" class="a4-paper-sheet portrait position-relative"
                         style="background: #ffffff !important; color: #0f172a !important; border: 2.5px solid #059669 !important; outline: 1px solid #10b981 !important; outline-offset: -5px !important; box-shadow: 0 15px 45px rgba(0, 0, 0, 0.6) !important; padding: 24px 28px !important;">
                        
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
                        <div class="print-paper-footer pt-3 mt-4 border-top border-dark border-opacity-25 position-relative" style="z-index: 1;">
                            <div class="row align-items-center text-center g-2 small">
                                <div class="col-4">
                                    <div class="mb-2" style="color: #475569 !important;">الموظف المسؤول / المختص</div>
                                    <div class="border-bottom border-dark border-dashed mx-auto" style="width: 120px; height: 18px;"></div>
                                    <div class="fw-bold small mt-1" style="color: #0f172a !important;">{{ auth()->user()->name ?? '..................' }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-1" style="color: #475569 !important;">ختم المؤسسة الرسمي</div>
                                    <div class="rounded-circle border border-2 border-success mx-auto d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; opacity: 0.35;">
                                        <i class="bi bi-patch-check text-success fs-3"></i>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="mb-2" style="color: #475569 !important;">الاعتماد الإداري / المالي</div>
                                    <div class="border-bottom border-dark border-dashed mx-auto" style="width: 120px; height: 18px;"></div>
                                    <div class="small mt-1" style="color: #64748b !important;">توقيع معتمد</div>
                                </div>
                            </div>
                            <div class="text-center pt-2 mt-2 border-top" style="font-size: 0.7rem; color: #64748b !important;">
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
