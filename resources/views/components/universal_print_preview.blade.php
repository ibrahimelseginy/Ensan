{{-- Universal Print Preview Modal (معاينة قبل الطباعة الشاملة لنظام مؤسسة إنسان) --}}
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
            <div class="modal-body p-3 p-md-4 print-preview-canvas" id="printPreviewCanvas">
                <div class="d-flex justify-content-center">
                    <div id="universalPrintPaper" class="a4-paper-sheet portrait bg-white text-dark shadow-2xl position-relative">
                        
                        {{-- Background Watermark --}}
                        <div class="print-paper-watermark position-absolute top-50 start-50 translate-middle text-center" style="opacity: 0.035; pointer-events: none; z-index: 0; user-select: none;">
                            <img src="{{ asset('images/heart-icon.png') }}" alt="" style="width: 280px; filter: grayscale(100%);">
                        </div>

                        {{-- Official Header (ترويسة رسمية) --}}
                        <div class="print-paper-header pb-3 mb-3 border-bottom border-2 border-success position-relative" style="z-index: 1;">
                            <div class="d-flex justify-content-between align-items-center">
                                <div class="text-start" style="line-height: 1.4;">
                                    <div class="small fw-bold text-muted">جمهورية مصر العربية</div>
                                    <div class="small fw-bold text-muted">وزارة التضامن الاجتماعي</div>
                                    <div class="fw-bolder fs-6 text-dark">مؤسسة إنسان الخيرية</div>
                                    <div class="text-muted" style="font-size: 0.72rem;">مشهرة برقم رسمي للعمل التنموي والخيري</div>
                                </div>
                                <div class="text-center">
                                    <img src="{{ asset('images/heart-icon.png') }}" alt="شعار إنسان" style="height: 48px; width: auto;" onerror="this.style.display='none'">
                                    <div class="fw-bold fs-5 text-success mt-1" id="universalPreviewHeading">وثيقة رسمية معتمدة</div>
                                </div>
                                <div class="text-end" style="line-height: 1.5; font-size: 0.78rem;">
                                    <div><span class="text-muted">تاريخ الطباعة:</span> <span class="fw-bold font-monospace">{{ date('Y/m/d') }}</span></div>
                                    <div><span class="text-muted">وقت الطباعة:</span> <span class="fw-bold font-monospace">{{ date('H:i A') }}</span></div>
                                    <div><span class="text-muted">المستخدم:</span> <span class="fw-bold">{{ auth()->user()->name ?? 'مستخدم النظام' }}</span></div>
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2 py-1 mt-1">
                                        <i class="bi bi-shield-check me-1"></i> وثيقة رسمية
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
                                    <div class="text-muted mb-2">الموظف المسؤول / المختص</div>
                                    <div class="border-bottom border-dark border-dashed mx-auto" style="width: 120px; height: 18px;"></div>
                                    <div class="fw-bold small mt-1 text-dark">{{ auth()->user()->name ?? '..................' }}</div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted mb-1">ختم المؤسسة الرسمي</div>
                                    <div class="rounded-circle border border-2 border-success mx-auto d-flex align-items-center justify-content-center" style="width: 55px; height: 55px; opacity: 0.35;">
                                        <i class="bi bi-patch-check text-success fs-3"></i>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="text-muted mb-2">الاعتماد الإداري / المالي</div>
                                    <div class="border-bottom border-dark border-dashed mx-auto" style="width: 120px; height: 18px;"></div>
                                    <div class="text-muted small mt-1">توقيع معتمد</div>
                                </div>
                            </div>
                            <div class="text-center pt-2 mt-2 border-top text-muted" style="font-size: 0.7rem;">
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
