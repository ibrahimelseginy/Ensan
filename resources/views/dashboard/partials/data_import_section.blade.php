{{-- ============================================================== --}}
{{-- SMART DATA IMPORT SECTION (EXCEL & CSV DISTRIBUTION HUB 2.0)   --}}
{{-- ============================================================== --}}

@php
    $sections = $sections ?? \App\Services\DataImportService::SECTIONS;
    $activeSection = $activeSection ?? 'cases';
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 data-import-hero-card overflow-hidden">
    <div class="card-body p-3 p-md-4">
        
        {{-- Section Step Progress Indicator --}}
        <div class="import-steps-indicator mb-4 pb-3 border-bottom border-light-subtle">
            <div class="row g-2 align-items-center text-center">
                <div class="col-4">
                    <div class="step-pill step-active" id="stepPill1">
                        <span class="step-num"><i class="bi bi-diagram-3-fill"></i></span>
                        <div class="step-text">
                            <span class="step-label">1. تحديد القسم</span>
                            <span class="step-desc d-none d-md-inline">المستهدف أو التوزيع الشامل</span>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="step-pill" id="stepPill2">
                        <span class="step-num"><i class="bi bi-cloud-arrow-up-fill"></i></span>
                        <div class="step-text">
                            <span class="step-label">2. رفع الملف</span>
                            <span class="step-desc d-none d-md-inline">Excel أو جداول CSV</span>
                        </div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="step-pill" id="stepPill3">
                        <span class="step-num"><i class="bi bi-sliders"></i></span>
                        <div class="step-text">
                            <span class="step-label">3. المعاينة والتوزيع</span>
                            <span class="step-desc d-none d-md-inline">المطابقة والاعتماد</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 1: Section Selector Tabs / Pills --}}
        <div class="import-sections-wrapper mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
                <label class="form-label small fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-success rounded-circle p-1" style="width: 24px; height: 24px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.8rem;">1</span>
                    <span class="fs-6 fw-bold">حدد القسم المستهدف لحفظ وتوزيع السجلات:</span>
                </label>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-light text-muted border px-3 py-1.5 rounded-pill small d-none d-md-inline-flex align-items-center gap-1.5">
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>القسم النشط:</span>
                        <strong class="text-primary" id="activeSectionLabelName">التوزيع التلقائي الذكي الشامل (AI)</strong>
                    </span>
                </div>
            </div>

            <div class="import-section-grid" id="importSectionPills">
                <button type="button" 
                        class="import-sec-pill active" 
                        data-section="auto"
                        data-name="التوزيع التلقائي الذكي الشامل (AI)"
                        style="--sec-color: #8b5cf6;">
                    <div class="sec-pill-icon" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6);">
                        <i class="bi bi-magic fs-5"></i>
                    </div>
                    <div class="sec-pill-info">
                        <div class="sec-pill-title d-flex align-items-center gap-1.5">
                            <span>توزيع تلقائي شامل (AI)</span>
                            <span class="badge bg-purple-subtle text-purple x-small px-1.5 py-0.5 rounded-pill fw-bold">موصى به</span>
                        </div>
                        <div class="sec-pill-badge">اكتشاف أقسام كل ورقة وتوزيعها آلياً</div>
                    </div>
                    <div class="sec-pill-check">
                        <i class="bi bi-check-lg"></i>
                    </div>
                </button>
                @foreach($sections as $secKey => $secConfig)
                    <button type="button" 
                            class="import-sec-pill" 
                            data-section="{{ $secKey }}"
                            data-name="{{ $secConfig['name'] }}"
                            style="--sec-color: {{ $secConfig['color'] }};">
                        <div class="sec-pill-icon" style="background-color: {{ $secConfig['color'] }};">
                            <i class="bi {{ $secConfig['icon'] }}"></i>
                        </div>
                        <div class="sec-pill-info">
                            <div class="sec-pill-title">{{ $secConfig['name'] }}</div>
                            <div class="sec-pill-badge">{{ $secConfig['badge'] }}</div>
                        </div>
                        <div class="sec-pill-check">
                            <i class="bi bi-check-lg"></i>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Step 2: Upload Box & Action Strip --}}
        <div class="row g-3 align-items-stretch">
            {{-- Dropzone Area --}}
            <div class="col-lg-8 col-md-12">
                <div class="import-dropzone p-4 p-md-5 rounded-4 text-center position-relative h-100 d-flex flex-column align-items-center justify-content-center" id="quickImportDropzone" role="button" tabindex="0">
                    <input type="file" id="quickImportFileInput" class="d-none" accept=".xlsx,.xls,.csv">
                    
                    {{-- Default State (No File) --}}
                    <div class="dropzone-content py-2" id="dropzoneDefaultState">
                        <div class="dropzone-icon mb-3">
                            <div class="icon-circle shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill text-success fs-1"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-2 text-main-heading">
                            اسحب وأفلت ملف الإكسيل هنا أو <span class="text-success text-decoration-underline" style="cursor: pointer;">تصفح جهازك</span>
                        </h5>
                        <p class="text-muted small mb-3">
                            يدعم ملفات Excel (.xlsx, .xls) وجداول CSV حتى 20 ميجابايت مع معالجة آلاف السجلات بلحظات
                        </p>
                        
                        <div class="d-flex flex-wrap align-items-center justify-content-center gap-2 mb-3">
                            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-file-earmark-spreadsheet text-success me-1"></i> .XLSX
                            </span>
                            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-file-earmark-excel text-success me-1"></i> .XLS
                            </span>
                            <span class="badge bg-light text-muted border px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-filetype-csv text-primary me-1"></i> .CSV
                            </span>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1 rounded-pill small">
                                <i class="bi bi-magic me-1"></i> مطابقة تلقائية بالذكاء الاصطناعي
                            </span>
                        </div>

                        {{-- Instant Demo Sample Loader Button --}}
                        <div class="pt-2">
                            <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-1.5 text-secondary fw-bold d-inline-flex align-items-center gap-1.5 hover-lift shadow-xs" id="btnLoadDemoFile">
                                <i class="bi bi-lightning-charge-fill text-warning"></i>
                                <span>تجربة فورية بملف نموذجي جاهز (1-Click Demo)</span>
                            </button>
                        </div>
                    </div>

                    {{-- Selected File State (Visible once a file is picked) --}}
                    <div class="selected-file-card p-3.5 rounded-4 border bg-white shadow-sm w-100 d-none text-start" id="dropzoneSelectedState">
                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-3">
                                <div class="file-card-icon bg-success-subtle text-success rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 58px; height: 58px;">
                                    <i class="bi bi-file-earmark-excel-fill fs-2"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 x-small fw-bold d-inline-flex align-items-center gap-1">
                                            <span class="live-dot-mini"></span> جاهز للمعاينة والمطابقة
                                        </span>
                                        <span class="badge bg-light text-muted border x-small font-monospace" id="selectedFileSizeBadge">0 KB</span>
                                        <span class="badge bg-info-subtle text-info border border-info-subtle x-small" id="selectedFileTypeBadge">Excel</span>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark text-break fs-6" id="selectedFileNameText">filename.xlsx</h6>
                                </div>
                            </div>

                            <div class="d-flex align-items-center gap-2">
                                <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold d-inline-flex align-items-center gap-1" id="btnRemoveSelectedFile" title="إزالة الملف واختيار غيره">
                                    <i class="bi bi-trash3"></i>
                                    <span>تغيير الملف</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Helper Sidebar & Actions --}}
            <div class="col-lg-4 col-md-12">
                <div class="p-3.5 p-md-4 rounded-4 h-100 d-flex flex-column justify-content-between import-side-helper-card border">
                    <div>
                        <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light-subtle">
                            <h6 class="fw-bold text-main-heading mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-shield-check text-success fs-5"></i>
                                <span>مميزات الاستيراد الذكي</span>
                            </h6>
                            <span class="badge bg-success-subtle text-success rounded-pill px-2.5 py-1 x-small fw-bold">آمن وسريع</span>
                        </div>

                        <div class="helper-features-list mb-3">
                            <div class="helper-feature-item p-2.5 rounded-3 mb-2 d-flex align-items-start gap-2.5">
                                <div class="feature-icon-badge text-success">
                                    <i class="bi bi-magic fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0.5">مطابقة ذكية للأعمدة</div>
                                    <div class="text-muted x-small">ربط حقول ملفك بحقول النظام تلقائياً حتى مع اختلاف المسميات أو التنسيق.</div>
                                </div>
                            </div>

                            <div class="helper-feature-item p-2.5 rounded-3 mb-2 d-flex align-items-start gap-2.5">
                                <div class="feature-icon-badge text-info">
                                    <i class="bi bi-eye-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0.5">معاينة حية وتدقيق</div>
                                    <div class="text-muted x-small">مراجعة وتدقيق عينة من السجلات وتعديل تخصيص أي عمود قبل الحفظ النهائي.</div>
                                </div>
                            </div>

                            <div class="helper-feature-item p-2.5 rounded-3 mb-2 d-flex align-items-start gap-2.5">
                                <div class="feature-icon-badge text-warning">
                                    <i class="bi bi-shield-lock-fill fs-5"></i>
                                </div>
                                <div>
                                    <div class="fw-bold text-dark small mb-0.5">فحص التكرار التلقائي</div>
                                    <div class="text-muted x-small">تخطي السجلات المكررة بالرقم القومي أو الهاتف لمنع الازدواجية تماماً.</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-3 border-top d-flex flex-column gap-2 mt-auto">
                        <button type="button" id="btnDownloadCurrentTemplate" class="btn btn-outline-success rounded-pill w-100 py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 hover-lift">
                            <i class="bi bi-file-earmark-arrow-down-fill fs-6"></i>
                            <span id="btnDownloadTemplateText">تحميل نموذج Excel جاهز للقسم</span>
                        </button>
                        <button type="button" id="btnStartQuickImport" class="btn btn-success rounded-pill w-100 py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm btn-start-import hover-lift" disabled>
                            <i class="bi bi-arrow-left-right fs-5"></i>
                            <span>بدء المعاينة ومطابقة الأعمدة</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: SMART COLUMN MAPPING & LIVE PREVIEW                     --}}
{{-- ============================================================== --}}
<div class="modal fade" id="importMappingModal" tabindex="-1" aria-labelledby="importMappingModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
            <div class="modal-header border-bottom py-3 px-4 bg-light-subtle d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-3">
                    <div class="p-2.5 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-sliders2-vertical fs-4"></i>
                    </div>
                    <div>
                        <div class="d-flex align-items-center gap-2 mb-0.5 flex-wrap">
                            <h5 class="modal-title fw-bold mb-0 text-main-heading" id="importMappingModalLabel">مطابقة الأعمدة وتدقيق البيانات</h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 x-small fw-bold" id="modalTargetSectionBadge">القسم: الحالات</span>
                        </div>
                        <p class="text-muted small mb-0">تم اكتشاف <strong id="modalTotalRowsCount" class="text-success">0</strong> صف بيانات صالح للمعالجة</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            {{-- Multi-Sheet Tabs --}}
            <div class="px-4 pt-2 border-bottom bg-light-subtle d-none" id="modalMultiSheetNavTabs">
                <ul class="nav nav-tabs border-0" id="modalTabsList">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold small border-0 py-2.5 px-3.5 d-inline-flex align-items-center gap-2" id="tabBtnBatchView" type="button">
                            <i class="bi bi-diagram-3-fill text-primary"></i>
                            <span>لوحة التوزيع الشامل لكافة الأوراق (AI)</span>
                            <span class="badge bg-primary text-white rounded-pill px-2 py-0.5 x-small" id="badgeModalTotalSheets">0</span>
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link fw-bold small border-0 py-2.5 px-3.5 d-inline-flex align-items-center gap-2" id="tabBtnDetailView" type="button">
                            <i class="bi bi-sliders2-vertical text-success"></i>
                            <span>المطابقة والمعاينة التفصيلية</span>
                            <span class="badge bg-light text-muted border rounded-pill px-2 py-0.5 x-small" id="badgeCurrentSheetName">ورقة 1</span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="modal-body p-4">
                {{-- BATCH DISTRIBUTION VIEW (All Sheets Overview) --}}
                <div id="batchDistributionView" class="d-none">
                    <div class="alert alert-primary border-0 bg-primary-subtle text-primary-emphasis rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5">
                            <i class="bi bi-magic fs-3 text-primary flex-shrink-0"></i>
                            <div>
                                <strong class="d-block">التوزيع والربط التلقائي الشامل لملف الإكسيل:</strong>
                                <span class="small">اكتشف الذكاء الاصطناعي أقسام كل ورقة عمل ومطابقة أعمدتها بدقة. يمكنك استيراد كل الأوراق دفعة واحدة أو تعديل القسم لأي ورقة.</span>
                            </div>
                        </div>
                        <span class="badge bg-primary text-white px-3 py-2 rounded-pill small fw-bold" id="batchTotalRecordsBadge">0 سجل صالح</span>
                    </div>

                    <div class="card border rounded-3 overflow-hidden shadow-2xs mb-2">
                        <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                            <span class="fw-bold small text-dark"><i class="bi bi-layers-fill me-1 text-primary"></i> أوراق العمل المكتشفة وتوزيعها على الأقسام</span>
                            <span class="text-muted x-small">حدد الأوراق المراد استيرادها ثم اضغط زر التوزيع الجماعي</span>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 small text-nowrap">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 35px;" class="text-center">
                                            <input type="checkbox" id="checkAllBatchSheets" class="form-check-input" checked title="تحديد / إلغاء تحديد الكل">
                                        </th>
                                        <th>ورقة العمل</th>
                                        <th>عدد الصفوف</th>
                                        <th style="min-width: 220px;">القسم المستهدف (مكتشف آلياً)</th>
                                        <th>الحقول المطابقة آلياً</th>
                                        <th>الحالة</th>
                                        <th class="text-center">إجراءات</th>
                                    </tr>
                                </thead>
                                <tbody id="batchSheetsTableBody">
                                    {{-- Populated via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- SINGLE SHEET DETAILED MAPPING VIEW --}}
                <div id="singleSheetMappingView">
                    {{-- Sheet Selector (if multi-sheet XLSX) --}}
                    <div id="modalSheetSelectorWrapper" class="d-none bg-emerald-50 border border-success-subtle rounded-3 p-3 mb-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="p-2 bg-success text-white rounded-2">
                                <i class="bi bi-file-earmark-spreadsheet-fill fs-5"></i>
                            </div>
                            <div>
                                <strong class="small text-dark d-block">ملف الإكسيل يحتوي على عدة أوراق عمل (Sheets):</strong>
                                <span class="text-muted x-small">اختر ورقة العمل التي تريد استيرادها حالياً، ويمكنك التبديل بين الأوراق بسهولة</span>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="small fw-bold text-dark text-nowrap"><i class="bi bi-layers-fill text-success me-1"></i>الورقة الحالية:</span>
                            <select id="modalSheetSelect" class="form-select form-select-sm fw-bold border-success text-success bg-white shadow-2xs" style="min-width: 220px;">
                            </select>
                        </div>
                    </div>

                    {{-- Mapping Status Alert & Counters --}}
                    <div class="alert alert-light border rounded-3 p-3 mb-4 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2.5 small">
                            <i class="bi bi-magic fs-4 flex-shrink-0 text-success"></i>
                            <div>
                                <strong>المطابقة التلقائية الذكية:</strong> قمنا بمطابقة أعمدة ملفك آلياً مع حقول النظام. يمكنك مراجعة وتعديل أي حقل، أو اختيار <em>"تخطي هذا العمود"</em> للأعمدة غير المطلوبة.
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-1.5 fw-bold small" id="mappedFieldsCountBadge">
                                <i class="bi bi-check2-all me-1"></i>
                                <span id="mappedCountNumber">0</span> حقول مطابقة
                            </span>
                        </div>
                    </div>

                    {{-- Column Mapping Grid --}}
                    <div class="card border rounded-3 mb-4 overflow-hidden shadow-2xs">
                        <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold small text-dark"><i class="bi bi-shuffle me-1 text-primary"></i> ربط أعمدة الملف بحقول النظام (Column Mapping)</span>
                                <span class="badge bg-light text-muted border x-small">الحقول التي تحمل علامة (*) إلزامية</span>
                            </div>
                            
                            {{-- Search input inside mapping fields --}}
                            <div class="input-group input-group-sm" style="width: 220px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-search"></i></span>
                                <input type="text" id="mappingSearchInput" class="form-control border-start-0" placeholder="تصفية الأعمدة...">
                            </div>
                        </div>
                        <div class="card-body p-3">
                            <div class="row g-3" id="mappingFieldsContainer">
                                {{-- Populated dynamically via JS --}}
                            </div>
                        </div>
                    </div>

                    {{-- Live Data Preview (First 5 Rows) --}}
                    <div class="card border rounded-3 overflow-hidden shadow-2xs">
                        <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold small text-dark"><i class="bi bi-eye-fill me-1 text-success"></i> معاينة أولية لبيانات الملف (أول 5 صفوف)</span>
                                <span class="badge bg-light text-muted border small">تحقق من دقة الأعمدة وترتيبها</span>
                            </div>

                            <div class="input-group input-group-sm" style="width: 200px;">
                                <span class="input-group-text bg-white border-end-0 text-muted"><i class="bi bi-filter"></i></span>
                                <input type="text" id="previewTableFilterInput" class="form-control border-start-0" placeholder="بحث في المعاينة...">
                            </div>
                        </div>
                        <div class="table-responsive" style="max-height: 260px;">
                            <table class="table table-sm table-hover table-bordered mb-0 align-middle small text-nowrap" id="previewDataTable">
                                <thead class="table-light sticky-top" id="previewTableHead">
                                    {{-- Populated via JS --}}
                                </thead>
                                <tbody id="previewTableBody">
                                    {{-- Populated via JS --}}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Live Execution Progress Bar (Shows during upload/processing) --}}
                <div id="modalExecutionProgressBar" class="d-none mt-3">
                    <div class="p-3 bg-light rounded-3 border">
                        <div class="d-flex align-items-center justify-content-between small fw-bold mb-2">
                            <span class="text-success" id="progressStatusText">جاري حفظ ومعالجة السجلات...</span>
                            <span class="text-muted" id="progressPercentText">100%</span>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top py-3 px-4 bg-light-subtle d-flex justify-content-between align-items-center flex-wrap gap-2">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold d-none" id="btnBackToBatchView">
                        <i class="bi bi-arrow-right me-1"></i> العودة للوحة التوزيع الشامل
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnExecuteBatchImport">
                        <i class="bi bi-check2-all fs-5"></i>
                        <span>تأكيد وتوزيع كافة الأوراق المحددة في أقسامها الآن</span>
                    </button>
                    <button type="button" class="btn btn-success rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2 d-none" id="btnExecuteFinalImport">
                        <i class="bi bi-check2-circle fs-5"></i>
                        <span>تأكيد واستيراد هذه الورقة فقط</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================== --}}
{{-- MODAL: IMPORT RESULTS REPORT                                  --}}
{{-- ============================================================== --}}
<div class="modal fade" id="importResultModal" tabindex="-1" aria-labelledby="importResultModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden text-center p-4">
            <div class="mb-3" id="resultModalIcon">
                <div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success mb-2">
                    <i class="bi bi-check-circle-fill fs-1"></i>
                </div>
            </div>
            <h4 class="fw-bold mb-2 text-main-heading" id="resultModalTitle">تم الاستيراد بنجاح!</h4>
            <p class="text-muted small mb-4" id="resultModalMessage">تمت معالجة ملف الإكسيل وحفظ البيانات في النظام بنجاح.</p>

            <div class="row g-2 mb-4 text-center">
                <div class="col-4">
                    <div class="p-3 rounded-3 bg-light border">
                        <div class="fs-3 fw-black text-primary" id="resTotalCount">0</div>
                        <div class="text-muted x-small fw-bold">إجمالي الصفوف</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 bg-success-subtle border border-success-subtle">
                        <div class="fs-3 fw-black text-success" id="resSuccessCount">0</div>
                        <div class="text-success x-small fw-bold">تم حفظها بنجاح</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-3 rounded-3 bg-warning-subtle border border-warning-subtle">
                        <div class="fs-3 fw-black text-warning-emphasis" id="resDuplicateCount">0</div>
                        <div class="text-warning-emphasis x-small fw-bold">مكررة/مستبعدة</div>
                    </div>
                </div>
            </div>

            <div id="resultErrorsBox" class="alert alert-warning text-start small mb-4 d-none p-3 rounded-3" style="max-height: 140px; overflow-y: auto;">
                <div class="fw-bold mb-1 text-dark"><i class="bi bi-exclamation-triangle me-1"></i> ملاحظات السطور المستبعدة أو غير المكتملة:</div>
                <ul class="mb-0 ps-3 space-y-1" id="resultErrorsList"></ul>
            </div>

            <div class="d-flex justify-content-center gap-2 flex-wrap">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إغلاق</button>
                <button type="button" class="btn btn-outline-success rounded-pill px-4 fw-bold" id="btnImportAnotherFile" data-bs-dismiss="modal">
                    <i class="bi bi-arrow-repeat me-1"></i> استيراد ملف آخر
                </button>
                <a href="#" id="resultTargetLink" class="btn btn-success rounded-pill px-4 fw-bold">
                    <span>الانتقال للقسم وعرض البيانات</span>
                    <i class="bi bi-arrow-left ms-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Styles for Data Import Section --}}
<style>
.data-import-hero-card {
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 25px rgba(0, 0, 0, 0.04) !important;
}

/* Step Indicator */
.step-pill {
    padding: 10px 14px;
    border-radius: 14px;
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    color: #64748b;
    font-size: 0.82rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.2s ease;
    width: 100%;
    justify-content: center;
}
.step-pill .step-num {
    width: 28px;
    height: 28px;
    border-radius: 50%;
    background: #e2e8f0;
    color: #64748b;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.82rem;
    flex-shrink: 0;
}
.step-pill.step-active {
    background: rgba(16, 185, 129, 0.08);
    border-color: #10b981;
    color: #047857;
}
.step-pill.step-active .step-num {
    background: #10b981;
    color: #ffffff;
}
.step-text {
    display: flex;
    flex-direction: column;
    text-align: right;
    line-height: 1.25;
}
.step-desc {
    font-size: 0.72rem;
    color: #94a3b8;
    font-weight: normal;
}
.step-pill.step-active .step-desc {
    color: #059669;
}

/* Section Pills Grid (Step 1) */
.import-section-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 12px;
}
@media (max-width: 1400px) {
    .import-section-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}
@media (max-width: 768px) {
    .import-section-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
@media (max-width: 500px) {
    .import-section-grid {
        grid-template-columns: 1fr;
    }
}

.import-sec-pill {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 12px 14px;
    color: #334155;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
    text-align: right;
    width: 100%;
    position: relative;
    box-shadow: 0 1px 3px rgba(0,0,0,0.02);
    min-height: 68px;
}
.sec-pill-icon {
    width: 40px;
    height: 40px;
    border-radius: 12px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
    transition: transform 0.25s ease;
}
.sec-pill-info {
    flex-grow: 1;
    min-width: 0;
}
.sec-pill-title {
    font-weight: 700;
    font-size: 0.85rem;
    line-height: 1.25;
    color: #1e293b;
    margin-bottom: 2px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sec-pill-badge {
    font-size: 0.72rem;
    color: #64748b;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.sec-pill-check {
    width: 20px;
    height: 20px;
    border-radius: 50%;
    background: var(--sec-color, #10b981);
    color: #ffffff;
    display: none;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
}
.import-sec-pill:hover {
    background: #ffffff;
    border-color: var(--sec-color, #10b981);
    transform: translateY(-2px);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
}
.import-sec-pill:hover .sec-pill-icon {
    transform: scale(1.08);
}
.import-sec-pill.active {
    background: #ffffff !important;
    border-color: var(--sec-color, #10b981) !important;
    border-width: 2px;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06), 0 0 0 1px var(--sec-color, #10b981) !important;
}
.import-sec-pill.active .sec-pill-title {
    color: var(--sec-color, #10b981);
}
.import-sec-pill.active .sec-pill-check {
    display: flex;
}

/* Dropzone (Step 2) */
.import-dropzone {
    background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 100%);
    border: 2px dashed #cbd5e1;
    border-radius: 20px !important;
    cursor: pointer;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    min-height: 270px;
    outline: none;
}
.import-dropzone:hover, .import-dropzone:focus, .import-dropzone.dragover {
    border-color: #10b981;
    background: #f0fdf4;
    box-shadow: 0 0 0 4px rgba(16, 185, 129, 0.08);
}
.import-dropzone .icon-circle {
    width: 74px;
    height: 74px;
    border-radius: 50%;
    background: rgba(16, 185, 129, 0.12);
    border: 1px solid rgba(16, 185, 129, 0.2);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
}
.import-dropzone:hover .icon-circle {
    transform: scale(1.12);
    background: rgba(16, 185, 129, 0.18);
}

/* Helper Sidebar */
.import-side-helper-card {
    background: #f8fafc;
    border-color: #e2e8f0 !important;
}
.helper-feature-item {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.2s ease;
}
.helper-feature-item:hover {
    border-color: #cbd5e1;
    box-shadow: 0 2px 8px rgba(0,0,0,0.03);
}
.feature-icon-badge {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: rgba(0,0,0,0.03);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

/* Start Import Button Pulse */
.btn-start-import:not([disabled]) {
    background: linear-gradient(135deg, #059669, #10b981) !important;
    border: none;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35) !important;
    animation: btnPulseGlow 2.5s infinite ease-in-out;
}
@keyframes btnPulseGlow {
    0%, 100% { box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35); }
    50% { box-shadow: 0 6px 22px rgba(16, 185, 129, 0.6); }
}

.live-dot-mini {
    width: 7px;
    height: 7px;
    background: #ffffff;
    border-radius: 50%;
    display: inline-block;
    animation: miniDotPulse 1.8s infinite ease-in-out;
}
@keyframes miniDotPulse {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.4; transform: scale(1.3); }
}

.bg-purple-subtle {
    background: rgba(139, 92, 246, 0.12) !important;
}
.text-purple {
    color: #8b5cf6 !important;
}

/* Dark Mode Styles */
.theme-dark .data-import-hero-card {
    background: #111d33 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.theme-dark .step-pill {
    background: #0f172a;
    border-color: rgba(255, 255, 255, 0.08);
    color: #94a3b8;
}
.theme-dark .step-pill .step-num {
    background: #1e293b;
    color: #94a3b8;
}
.theme-dark .step-pill.step-active {
    background: rgba(16, 185, 129, 0.15);
    border-color: #10b981;
    color: #34d399;
}
.theme-dark .step-pill.step-active .step-num {
    background: #10b981;
    color: #ffffff;
}
.theme-dark .import-sec-pill {
    background: #0f172a;
    border-color: rgba(255, 255, 255, 0.1);
    color: #f8fafc;
}
.theme-dark .sec-pill-title {
    color: #f8fafc;
}
.theme-dark .sec-pill-badge {
    color: #94a3b8;
}
.theme-dark .import-sec-pill:hover {
    background: #1e293b;
}
.theme-dark .import-sec-pill.active {
    background: #1e293b !important;
    box-shadow: 0 4px 18px rgba(0, 0, 0, 0.3), 0 0 0 1px var(--sec-color, #10b981) !important;
}
.theme-dark .import-dropzone {
    background: linear-gradient(180deg, #0f172a 0%, #1e293b 100%);
    border-color: rgba(255, 255, 255, 0.15);
}
.theme-dark .import-dropzone:hover,
.theme-dark .import-dropzone.dragover {
    background: rgba(16, 185, 129, 0.08);
    border-color: #10b981;
}
.theme-dark .import-side-helper-card {
    background: #0f172a;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.theme-dark .helper-feature-item {
    background: #1e293b;
    border-color: rgba(255, 255, 255, 0.08);
}
.theme-dark .helper-feature-item .text-dark {
    color: #f8fafc !important;
}
.theme-dark .helper-feature-item .text-muted {
    color: #94a3b8 !important;
}
.theme-dark .selected-file-card {
    background: #1e293b !important;
    border-color: rgba(255, 255, 255, 0.12) !important;
}
.theme-dark .selected-file-card .text-dark {
    color: #f8fafc !important;
}
</style>

{{-- Interactive JavaScript for Smart Import --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSelectedSection = 'auto';
    let currentUploadedFile = null;
    let currentImportToken = null;
    let currentWorkbookAnalysis = null;
    let currentImportHeaders = [];
    let currentTargetFields = {};
    let currentSelectedSheetIndex = 0;
    let currentActiveView = 'batch'; // 'batch' or 'detail'

    const templateBaseUrl = "{{ url('data-import/template') }}";
    const previewUrl = "{{ route('data-import.preview') }}";
    const processUrl = "{{ route('data-import.process') }}";
    const batchProcessUrl = "{{ route('data-import.batch-process') }}";
    const csrfToken = "{{ csrf_token() }}";

    const targetSectionLinks = {
        'cases': "{{ Route::has('case-applications.index') ? route('case-applications.index') : url('/case-applications') }}",
        'donors': "{{ Route::has('donors.index') ? route('donors.index') : url('/donors') }}",
        'donations': "{{ Route::has('donations.index') ? route('donations.index') : url('/donations') }}",
        'beneficiaries': "{{ Route::has('beneficiaries.index') ? route('beneficiaries.index') : url('/beneficiaries') }}",
        'tasks': "{{ Route::has('employee-tasks.index') ? route('employee-tasks.index') : url('/employee-tasks') }}"
    };

    const sectionLabels = {
        'auto': 'التوزيع التلقائي الذكي الشامل (AI)',
        'cases': 'الحالات وطلبات المساعدة',
        'donors': 'المتبرعين',
        'donations': 'التبرعات والمعاملات المالية',
        'beneficiaries': 'المستفيدين والبحث الميداني',
        'tasks': 'مهام فريق العمل'
    };

    const activeSectionLabelName = document.getElementById('activeSectionLabelName');
    const btnDownloadTemplateText = document.getElementById('btnDownloadTemplateText');

    function updateActiveSectionLabels() {
        const name = sectionLabels[currentSelectedSection] || 'القسم';
        if (activeSectionLabelName) activeSectionLabelName.textContent = name;
        if (btnDownloadTemplateText) {
            btnDownloadTemplateText.textContent = currentSelectedSection === 'auto'
                ? 'تحميل نموذج Excel قياسي'
                : 'تحميل نموذج ' + name + ' جاهز';
        }
    }
    updateActiveSectionLabels();

    // 1. Section Pill Change
    const sectionPills = document.querySelectorAll('.import-sec-pill');
    sectionPills.forEach(pill => {
        pill.addEventListener('click', function() {
            sectionPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentSelectedSection = this.getAttribute('data-section');
            updateActiveSectionLabels();
        });
    });

    // 2. Download Template Button
    const btnDownloadTemplate = document.getElementById('btnDownloadCurrentTemplate');
    if (btnDownloadTemplate) {
        btnDownloadTemplate.addEventListener('click', function() {
            const targetSec = currentSelectedSection === 'auto' ? 'donations' : currentSelectedSection;
            window.location.href = templateBaseUrl + '/' + targetSec;
        });
    }

    // 3. File Input Handling
    const fileInput = document.getElementById('quickImportFileInput');
    const dropzone = document.getElementById('quickImportDropzone');
    const defaultState = document.getElementById('dropzoneDefaultState');
    const selectedState = document.getElementById('dropzoneSelectedState');
    const selectedFileNameText = document.getElementById('selectedFileNameText');
    const selectedFileSizeBadge = document.getElementById('selectedFileSizeBadge');
    const selectedFileTypeBadge = document.getElementById('selectedFileTypeBadge');
    const btnRemoveFile = document.getElementById('btnRemoveSelectedFile');
    const btnStart = document.getElementById('btnStartQuickImport');
    const stepPill2 = document.getElementById('stepPill2');
    const stepPill3 = document.getElementById('stepPill3');
    const btnLoadDemo = document.getElementById('btnLoadDemoFile');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                handleFileSelected(this.files[0]);
            }
        });
    }

    function handleFileSelected(file) {
        currentUploadedFile = file;
        const sizeFormatted = file.size > 1048576 
            ? (file.size / 1048576).toFixed(2) + ' MB' 
            : (file.size / 1024).toFixed(1) + ' KB';

        selectedFileNameText.textContent = file.name;
        selectedFileSizeBadge.textContent = sizeFormatted;

        const ext = file.name.split('.').pop().toUpperCase();
        if (selectedFileTypeBadge) {
            selectedFileTypeBadge.textContent = ext;
        }

        defaultState.classList.add('d-none');
        selectedState.classList.remove('d-none');
        if (stepPill2) stepPill2.classList.add('step-active');
        btnStart.removeAttribute('disabled');
    }

    function resetFileSelection() {
        currentUploadedFile = null;
        if (fileInput) fileInput.value = '';
        defaultState.classList.remove('d-none');
        selectedState.classList.add('d-none');
        if (stepPill2) stepPill2.classList.remove('step-active');
        if (stepPill3) stepPill3.classList.remove('step-active');
        btnStart.setAttribute('disabled', 'disabled');
    }

    if (btnRemoveFile) {
        btnRemoveFile.addEventListener('click', function(e) {
            e.stopPropagation();
            resetFileSelection();
        });
    }

    // 1-Click Demo File Generator
    if (btnLoadDemo) {
        btnLoadDemo.addEventListener('click', function(e) {
            e.stopPropagation();
            const demoCsvContent = "\uFEFFاسم صاحب الحالة,رقم الهاتف,الرقم القومي,نوع المساعدة,المحافظة,المدينة,العنوان بالتفصيل,عدد أفراد الأسرة,تفاصيل ووصف الحالة\n"
                + "أحمد محمد السيد,01012345678,29001011234567,medical,الغربية,طنطا,شارع البحر بجوار المستشفى,4,عملية جراحية وعلاج عاجل\n"
                + "فاطمة إبراهيم حسن,01298765432,28505051234568,zad,كفر الشيخ,دسوق,قرية كفر مجر,3,أسرة أيتام تحتاج كفالة شهرية\n"
                + "محمود عبد الرحمن علي,01145678901,27810101234569,water,الدقهلية,المنصورة,مركز شربين,5,توصيل وصلة مياه صالحة للشرب\n"
                + "زينب مصطفى كمال,01599887766,29202021234570,hope,البحيرة,دمنهور,شارع الجمهورية,2,فحوصات وأدوية شهرية لمريض أورام";

            const blob = new Blob([demoCsvContent], { type: 'text/csv;charset=utf-8;' });
            const demoFile = new File([blob], 'ملف_حالات_تجريبي_إنسان.csv', { type: 'text/csv' });
            
            // Auto switch section to cases or auto
            const casesPill = document.querySelector('.import-sec-pill[data-section="cases"]');
            if (casesPill) casesPill.click();

            handleFileSelected(demoFile);
        });
    }

    // Drag and Drop & Click Effects
    if (dropzone && fileInput) {
        dropzone.addEventListener('click', function(e) {
            if (e.target.closest('#btnRemoveSelectedFile') || e.target.closest('#btnLoadDemoFile') || e.target.closest('button') || e.target.closest('a')) {
                return;
            }
            fileInput.click();
        });
        dropzone.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                fileInput.click();
            }
        });

        ['dragenter', 'dragover'].forEach(name => {
            dropzone.addEventListener(name, (e) => { e.preventDefault(); dropzone.classList.add('dragover'); }, false);
        });
        ['dragleave', 'drop'].forEach(name => {
            dropzone.addEventListener(name, (e) => { e.preventDefault(); dropzone.classList.remove('dragover'); }, false);
        });
        dropzone.addEventListener('drop', (e) => {
            if (e.dataTransfer.files && e.dataTransfer.files[0]) {
                fileInput.files = e.dataTransfer.files;
                handleFileSelected(e.dataTransfer.files[0]);
            }
        });
    }

    // 4. Start Upload & Preview (Step 2 -> Step 3 Modal)
    if (btnStart) {
        btnStart.addEventListener('click', function() {
            if (!currentUploadedFile) {
                alert('يرجى اختيار ملف الإكسيل أولاً.');
                return;
            }

            const formData = new FormData();
            formData.append('_token', csrfToken);
            formData.append('file', currentUploadedFile);
            formData.append('section', currentSelectedSection);

            btnStart.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري التحليل والمطابقة الذكية...';
            btnStart.setAttribute('disabled', 'disabled');

            fetch(previewUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnStart.innerHTML = '<i class="bi bi-arrow-left-right fs-5 me-1"></i> بدء المعاينة ومطابقة الأعمدة';
                btnStart.removeAttribute('disabled');

                if (!data.success) {
                    alert(data.message || 'حدث خطأ أثناء فحص الملف.');
                    return;
                }

                currentImportToken = data.token;
                currentImportHeaders = data.headers;
                currentTargetFields = data.target_fields;
                currentWorkbookAnalysis = data.workbook_analysis;

                if (stepPill3) stepPill3.classList.add('step-active');

                renderImportModal(data);
                const modal = new bootstrap.Modal(document.getElementById('importMappingModal'));
                modal.show();
            })
            .catch(err => {
                btnStart.innerHTML = '<i class="bi bi-arrow-left-right fs-5 me-1"></i> بدء المعاينة ومطابقة الأعمدة';
                btnStart.removeAttribute('disabled');
                console.error(err);
                alert('تعذر الاتصال بالخادم. يرجى التحقق من الملف والمحاولة مرة أخرى.');
            });
        });
    }

    // Client-side dictionary for technical / English headers fallback
    const clientHeaderDict = {
        'id': 'الكود التعريفي (ID)',
        'created_at': 'تاريخ الإنشاء والوقت',
        'updated_at': 'تاريخ آخر تحديث',
        'user_id': 'معرف المستخدم (User ID)',
        'method': 'نوع الطلب (HTTP Method)',
        'path': 'رابط الصفحة / المسار (Path)',
        'status_code': 'رمز الحالة (Status Code)',
        'ip': 'عنوان IP للشبكة',
        'user_agent': 'متصفح وجهاز المستخدم',
        'payload': 'حمولة وبيانات الطلب (Payload)',
        'phone': 'رقم الهاتف',
        'mobile': 'رقم الموبايل',
        'name': 'الاسم الكامل',
        'national_id': 'الرقم القومي',
        'amount': 'المبلغ المالي',
        'date': 'التاريخ',
        'status': 'الحالة',
        'address': 'العنوان ومكان السكن',
        'notes': 'الملاحظات',
        'project': 'المشروع الخيري',
        'collector': 'المحصل / المندوب',
        'email': 'البريد الإلكتروني'
    };

    function getArabicHeaderLabel(rawHeader, colIdx, headerTranslations) {
        let title = '';
        let original = String(rawHeader || '').trim();

        if (headerTranslations && headerTranslations[colIdx]) {
            const tr = headerTranslations[colIdx];
            if (typeof tr === 'object' && tr !== null) {
                title = tr.main || tr.title || '';
                if (tr.sub) original = tr.sub;
            } else if (typeof tr === 'string') {
                title = tr;
            }
        }

        if (!title && original) {
            const clean = original.toLowerCase();
            if (clientHeaderDict[clean]) {
                title = clientHeaderDict[clean];
            } else {
                title = original;
            }
        }

        if (!title) {
            title = 'عمود بدون عنوان #' + (colIdx + 1);
        }

        const isDifferent = original !== '' && title.trim().toLowerCase() !== original.toLowerCase();

        return {
            title: title,
            original: original,
            isDifferent: isDifferent
        };
    }

    // Navigation and Tab Switching inside Modal
    const modalNavTabs = document.getElementById('modalMultiSheetNavTabs');
    const tabBtnBatchView = document.getElementById('tabBtnBatchView');
    const tabBtnDetailView = document.getElementById('tabBtnDetailView');
    const batchDistributionView = document.getElementById('batchDistributionView');
    const singleSheetMappingView = document.getElementById('singleSheetMappingView');
    const btnExecuteBatchImport = document.getElementById('btnExecuteBatchImport');
    const btnExecuteFinalImport = document.getElementById('btnExecuteFinalImport');
    const btnBackToBatchView = document.getElementById('btnBackToBatchView');

    function switchModalView(view) {
        currentActiveView = view;
        if (view === 'batch') {
            if (tabBtnBatchView) tabBtnBatchView.classList.add('active');
            if (tabBtnDetailView) tabBtnDetailView.classList.remove('active');
            batchDistributionView.classList.remove('d-none');
            singleSheetMappingView.classList.add('d-none');
            btnExecuteBatchImport.classList.remove('d-none');
            btnExecuteFinalImport.classList.add('d-none');
            btnBackToBatchView.classList.add('d-none');
        } else {
            if (tabBtnBatchView) tabBtnBatchView.classList.remove('active');
            if (tabBtnDetailView) tabBtnDetailView.classList.add('active');
            batchDistributionView.classList.add('d-none');
            singleSheetMappingView.classList.remove('d-none');
            btnExecuteBatchImport.classList.add('d-none');
            btnExecuteFinalImport.classList.remove('d-none');
            if (currentWorkbookAnalysis && currentWorkbookAnalysis.sheets && currentWorkbookAnalysis.sheets.length > 1) {
                btnBackToBatchView.classList.remove('d-none');
            } else {
                btnBackToBatchView.classList.add('d-none');
            }
        }
    }

    if (tabBtnBatchView) tabBtnBatchView.addEventListener('click', () => switchModalView('batch'));
    if (tabBtnDetailView) tabBtnDetailView.addEventListener('click', () => switchModalView('detail'));
    if (btnBackToBatchView) btnBackToBatchView.addEventListener('click', () => switchModalView('batch'));

    // Master Render Entrypoint
    function renderImportModal(data) {
        document.getElementById('modalTotalRowsCount').textContent = data.total_rows;
        document.getElementById('modalTargetSectionBadge').textContent = 'القسم: ' + data.section_name;

        const isMulti = data.workbook_analysis && data.workbook_analysis.sheets && data.workbook_analysis.sheets.length > 1;

        if (isMulti) {
            modalNavTabs.classList.remove('d-none');
            document.getElementById('badgeModalTotalSheets').textContent = data.workbook_analysis.total_sheets;
            document.getElementById('batchTotalRecordsBadge').textContent = (data.workbook_analysis.total_records || data.total_rows) + ' سجل صالح';
            
            renderBatchSheetsTable(data.workbook_analysis.sheets);
            switchModalView('batch');
        } else {
            modalNavTabs.classList.add('d-none');
            switchModalView('detail');
        }

        populateSingleSheetView(data);
    }

    // Render All Sheets Table in Batch Hub
    function renderBatchSheetsTable(sheets) {
        const tbody = document.getElementById('batchSheetsTableBody');
        tbody.innerHTML = '';

        sheets.forEach(s => {
            const tr = document.createElement('tr');
            if (s.is_empty) {
                tr.className = 'opacity-50';
            }

            // Build sections dropdown
            let secOptionsHtml = '';
            for (const [sKey, sLabel] of Object.entries(sectionLabels)) {
                if (sKey === 'auto') continue;
                const isSel = (s.detected_section === sKey) ? 'selected' : '';
                secOptionsHtml += `<option value="${sKey}" ${isSel}>${sLabel}</option>`;
            }

            // Mapped preview chips
            let mappedChips = '';
            if (s.mapping) {
                const mappedCols = Object.entries(s.mapping).filter(([_, f]) => f !== null && f !== 'skip');
                mappedCols.slice(0, 4).forEach(([cIdx, fKey]) => {
                    const rawH = s.headers ? s.headers[cIdx] : '';
                    mappedChips += `<span class="badge bg-light text-dark border me-1 x-small font-monospace mb-1">${rawH || fKey}</span>`;
                });
                if (mappedCols.length > 4) {
                    mappedChips += `<span class="badge bg-light text-muted border x-small">+${mappedCols.length - 4} حقول</span>`;
                }
            }

            // Status Badge
            let statusBadge = '';
            if (s.is_empty) {
                statusBadge = '<span class="badge bg-secondary-subtle text-secondary rounded-pill x-small">ورقة فارغة</span>';
            } else if (s.is_ready) {
                statusBadge = `<span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill x-small fw-bold"><i class="bi bi-check-circle-fill me-1"></i>جاهز 100%</span>`;
            } else {
                statusBadge = `<span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill x-small"><i class="bi bi-magic me-1"></i>مطابقة (${s.confidence || 70}%)</span>`;
            }

            tr.innerHTML = `
                <td class="text-center">
                    <input type="checkbox" class="form-check-input batch-sheet-check" data-idx="${s.sheet_index}" ${s.is_ready ? 'checked' : ''} ${s.is_empty ? 'disabled' : ''}>
                </td>
                <td>
                    <div class="fw-bold text-dark d-flex align-items-center gap-1.5">
                        <i class="bi bi-file-earmark-spreadsheet text-success"></i>
                        <span>${s.sheet_name}</span>
                    </div>
                </td>
                <td>
                    <span class="badge bg-light text-muted border font-monospace">${s.rows_count} صف</span>
                </td>
                <td>
                    <select class="form-select form-select-sm batch-sheet-section-select fw-bold border-primary shadow-2xs" data-idx="${s.sheet_index}" ${s.is_empty ? 'disabled' : ''}>
                        ${secOptionsHtml}
                    </select>
                </td>
                <td>
                    <div class="d-flex flex-wrap align-items-center">${mappedChips || '<span class="text-muted x-small">--</span>'}</div>
                </td>
                <td>
                    ${statusBadge}
                </td>
                <td class="text-center">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 x-small fw-bold btn-inspect-sheet" data-idx="${s.sheet_index}" ${s.is_empty ? 'disabled' : ''}>
                        <i class="bi bi-sliders me-1"></i> تدقيق الأعمدة
                    </button>
                </td>
            `;

            tbody.appendChild(tr);
        });

        // Master check/uncheck
        const checkAll = document.getElementById('checkAllBatchSheets');
        if (checkAll) {
            checkAll.onchange = function() {
                const checks = document.querySelectorAll('.batch-sheet-check:not(:disabled)');
                checks.forEach(c => c.checked = checkAll.checked);
            };
        }

        // Sheet section select change
        document.querySelectorAll('.batch-sheet-section-select').forEach(sel => {
            sel.addEventListener('change', function() {
                const idx = parseInt(this.getAttribute('data-idx'), 10);
                const chosenSec = this.value;
                const sh = currentWorkbookAnalysis.sheets.find(x => x.sheet_index === idx);
                if (sh) {
                    sh.detected_section = chosenSec;
                }
            });
        });

        // Inspect button click
        document.querySelectorAll('.btn-inspect-sheet').forEach(btn => {
            btn.addEventListener('click', function() {
                const idx = parseInt(this.getAttribute('data-idx'), 10);
                loadSheetDetail(idx);
            });
        });
    }

    function loadSheetDetail(sheetIdx) {
        currentSelectedSheetIndex = sheetIdx;
        const container = document.getElementById('mappingFieldsContainer');
        container.innerHTML = '<div class="col-12 text-center py-5"><div class="spinner-border text-success mb-2"></div><div class="small text-muted fw-bold">جاري تحميل بيانات الورقة ومطابقة أعمدتها...</div></div>';

        const secSel = document.querySelector(`.batch-sheet-section-select[data-idx="${sheetIdx}"]`);
        const targetSec = secSel ? secSel.value : 'auto';

        const formData = new FormData();
        formData.append('_token', csrfToken);
        formData.append('token', currentImportToken);
        formData.append('section', targetSec);
        formData.append('sheet_index', sheetIdx);

        fetch(previewUrl, {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData
        })
        .then(res => res.json())
        .then(resData => {
            if (!resData.success) {
                alert(resData.message || 'تعذر قراءة الورقة المختارة.');
                return;
            }
            currentImportHeaders = resData.headers;
            currentTargetFields = resData.target_fields;
            populateSingleSheetView(resData);
            switchModalView('detail');
        })
        .catch(err => {
            console.error(err);
            alert('حدث خطأ أثناء تحميل بيانات ورقة العمل.');
        });
    }

    function updateMappedCount() {
        const selects = document.querySelectorAll('.column-map-select');
        let mapped = 0;
        selects.forEach(s => {
            if (s.value && s.value !== 'skip') mapped++;
        });
        const countEl = document.getElementById('mappedCountNumber');
        if (countEl) countEl.textContent = mapped;
    }

    // Populate Detailed Mapping & Preview View for a Single Sheet
    function populateSingleSheetView(data) {
        document.getElementById('modalTotalRowsCount').textContent = data.total_rows;
        document.getElementById('modalTargetSectionBadge').textContent = 'القسم: ' + data.section_name;
        
        const currentSheetObj = data.sheets ? data.sheets.find(x => x.index === (data.active_sheet_index || 0)) : null;
        if (currentSheetObj && document.getElementById('badgeCurrentSheetName')) {
            document.getElementById('badgeCurrentSheetName').textContent = currentSheetObj.name;
        }

        // Handle Sheet Selector in single view
        const sheetWrapper = document.getElementById('modalSheetSelectorWrapper');
        const sheetSelect = document.getElementById('modalSheetSelect');

        if (data.sheets && data.sheets.length > 1) {
            sheetWrapper.classList.remove('d-none');
            sheetSelect.innerHTML = '';
            data.sheets.forEach(s => {
                const opt = document.createElement('option');
                opt.value = s.index;
                const rowsInfo = s.rows_count ? ` (${s.rows_count} صف)` : '';
                opt.textContent = `${s.name}${rowsInfo}`;
                if (s.index === (data.active_sheet_index || 0)) {
                    opt.selected = true;
                }
                sheetSelect.appendChild(opt);
            });

            sheetSelect.onchange = function() {
                loadSheetDetail(parseInt(this.value, 10));
            };
        } else {
            if (sheetWrapper) sheetWrapper.classList.add('d-none');
        }

        // 1. Build mapping fields
        const container = document.getElementById('mappingFieldsContainer');
        container.innerHTML = '';

        data.headers.forEach((headerName, colIdx) => {
            const suggestedField = data.suggested_mapping[colIdx] || '';
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6 col-lg-4 mapping-col-item';

            let optionsHtml = '<option value="skip">-- تخطي هذا العمود (عدم استيراده) --</option>';
            for (const [fKey, fMeta] of Object.entries(data.target_fields)) {
                const isSelected = (suggestedField === fKey) ? 'selected' : '';
                const reqStar = fMeta.required ? ' * (مطلوب)' : '';
                optionsHtml += `<option value="${fKey}" ${isSelected}>${fMeta.label}${reqStar}</option>`;
            }

            const isMatched = suggestedField !== '';
            const hInfo = getArabicHeaderLabel(headerName, colIdx, data.header_translations);

            colDiv.setAttribute('data-header', hInfo.title.toLowerCase());
            colDiv.setAttribute('data-original', hInfo.original.toLowerCase());

            colDiv.innerHTML = `
                <div class="p-3 rounded-3 border h-100 ${isMatched ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-light'}">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="badge bg-secondary-subtle text-secondary x-small">عمود إكسيل #${colIdx + 1}</span>
                        ${isMatched ? '<span class="badge bg-success-subtle text-success x-small fw-bold"><i class="bi bi-magic me-1"></i>مطابقة مؤكدة</span>' : '<span class="badge bg-light text-muted border x-small">غير محدد</span>'}
                    </div>
                    <div class="fw-bold small text-truncate mb-0.5 text-dark" title="${hInfo.title}">
                        ${hInfo.title}
                    </div>
                    ${hInfo.isDifferent ? `<div class="text-muted x-small font-monospace mb-2 text-truncate" title="${hInfo.original}"><i class="bi bi-file-earmark-code me-0.5"></i> ${hInfo.original}</div>` : '<div class="mb-2" style="height: 18px;"></div>'}
                    <select class="form-select form-select-sm column-map-select" data-col="${colIdx}">
                        ${optionsHtml}
                    </select>
                </div>
            `;
            container.appendChild(colDiv);
        });

        updateMappedCount();

        // Listen for change on selects
        document.querySelectorAll('.column-map-select').forEach(sel => {
            sel.addEventListener('change', updateMappedCount);
        });

        // 2. Build Live Preview Table
        const head = document.getElementById('previewTableHead');
        const body = document.getElementById('previewTableBody');
        head.innerHTML = '';
        body.innerHTML = '';

        let headRow = '<tr><th>#</th>';
        data.headers.forEach((h, cIdx) => {
            const hInfo = getArabicHeaderLabel(h, cIdx, data.header_translations);
            headRow += `<th class="fw-bold text-center">
                <div class="text-dark">${hInfo.title}</div>
                ${hInfo.isDifferent ? `<div class="text-muted x-small font-monospace fw-normal">${hInfo.original}</div>` : ''}
            </th>`;
        });
        headRow += '</tr>';
        head.innerHTML = headRow;

        data.preview_rows.forEach((row, rIdx) => {
            let tr = `<tr class="preview-row-item"><td class="text-muted fw-bold">${rIdx + 1}</td>`;
            data.headers.forEach((_, cIdx) => {
                const cellVal = row[cIdx] !== undefined ? row[cIdx] : '';
                tr += `<td>${cellVal}</td>`;
            });
            tr += '</tr>';
            body.innerHTML += tr;
        });

        // Mapping search filter
        const mappingSearchInput = document.getElementById('mappingSearchInput');
        if (mappingSearchInput) {
            mappingSearchInput.value = '';
            mappingSearchInput.oninput = function() {
                const q = this.value.trim().toLowerCase();
                document.querySelectorAll('.mapping-col-item').forEach(item => {
                    const h = item.getAttribute('data-header') || '';
                    const orig = item.getAttribute('data-original') || '';
                    if (!q || h.includes(q) || orig.includes(q)) {
                        item.classList.remove('d-none');
                    } else {
                        item.classList.add('d-none');
                    }
                });
            };
        }

        // Preview table filter
        const previewFilterInput = document.getElementById('previewTableFilterInput');
        if (previewFilterInput) {
            previewFilterInput.value = '';
            previewFilterInput.oninput = function() {
                const q = this.value.trim().toLowerCase();
                document.querySelectorAll('.preview-row-item').forEach(tr => {
                    const text = tr.innerText.toLowerCase();
                    if (!q || text.includes(q)) {
                        tr.style.display = '';
                    } else {
                        tr.style.display = 'none';
                    }
                });
            };
        }
    }

    // 5. Confirm and Execute Import for Single Sheet
    const btnExecute = document.getElementById('btnExecuteFinalImport');
    const progressBar = document.getElementById('modalExecutionProgressBar');

    if (btnExecute) {
        btnExecute.addEventListener('click', function() {
            const selects = document.querySelectorAll('.column-map-select');
            const mapping = {};
            selects.forEach(sel => {
                const colIdx = sel.getAttribute('data-col');
                mapping[colIdx] = sel.value;
            });

            // Check if required fields are mapped
            for (const [fKey, fMeta] of Object.entries(currentTargetFields)) {
                if (fMeta.required) {
                    const isMapped = Object.values(mapping).includes(fKey);
                    if (!isMapped) {
                        alert(`حقل "${fMeta.label}" مطلوب لاستكمال الاستيراد في هذا القسم. يرجى ربطه بأحد الأعمدة.`);
                        return;
                    }
                }
            }

            btnExecute.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري الحفظ وتوزيع البيانات...';
            btnExecute.setAttribute('disabled', 'disabled');
            if (progressBar) progressBar.classList.remove('d-none');

            fetch(processUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    token: currentImportToken,
                    section: currentSelectedSection === 'auto' ? (currentWorkbookAnalysis?.sheets[currentSelectedSheetIndex]?.detected_section || 'donations') : currentSelectedSection,
                    sheet_index: currentSelectedSheetIndex,
                    mapping: mapping
                })
            })
            .then(res => res.json())
            .then(data => {
                btnExecute.innerHTML = '<i class="bi bi-check2-circle fs-5 me-1"></i> تأكيد واستيراد هذه الورقة فقط';
                btnExecute.removeAttribute('disabled');
                if (progressBar) progressBar.classList.add('d-none');

                bootstrap.Modal.getInstance(document.getElementById('importMappingModal')).hide();
                showImportResults(data);
            })
            .catch(err => {
                btnExecute.innerHTML = '<i class="bi bi-check2-circle fs-5 me-1"></i> تأكيد واستيراد هذه الورقة فقط';
                btnExecute.removeAttribute('disabled');
                if (progressBar) progressBar.classList.add('d-none');
                console.error(err);
                alert('حدث خطأ غير متوقع أثناء معالجة البيانات.');
            });
        });
    }

    // 6. Batch Confirm and Execute All Sheets at Once (One-Click AI Distribution)
    if (btnExecuteBatchImport) {
        btnExecuteBatchImport.addEventListener('click', function() {
            const checkedBoxes = document.querySelectorAll('.batch-sheet-check:checked');
            if (checkedBoxes.length === 0) {
                alert('يرجى تحديد ورقة عمل واحدة على الأقل للاستيراد.');
                return;
            }

            const sheetsToImport = [];
            checkedBoxes.forEach(chk => {
                const idx = parseInt(chk.getAttribute('data-idx'), 10);
                const sData = currentWorkbookAnalysis.sheets.find(x => x.sheet_index === idx);
                if (sData && !sData.is_empty) {
                    const secSel = document.querySelector(`.batch-sheet-section-select[data-idx="${idx}"]`);
                    const chosenSec = secSel ? secSel.value : sData.detected_section;
                    sheetsToImport.push({
                        sheet_index: idx,
                        sheet_name: sData.sheet_name,
                        section: chosenSec,
                        mapping: sData.mapping
                    });
                }
            });

            btnExecuteBatchImport.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري التوزيع وحفظ كافة الأوراق...';
            btnExecuteBatchImport.setAttribute('disabled', 'disabled');
            if (progressBar) progressBar.classList.remove('d-none');

            fetch(batchProcessUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    token: currentImportToken,
                    sheets: sheetsToImport
                })
            })
            .then(res => res.json())
            .then(data => {
                btnExecuteBatchImport.innerHTML = '<i class="bi bi-check2-all fs-5 me-1"></i> تأكيد وتوزيع كافة الأوراق المحددة في أقسامها الآن';
                btnExecuteBatchImport.removeAttribute('disabled');
                if (progressBar) progressBar.classList.add('d-none');

                bootstrap.Modal.getInstance(document.getElementById('importMappingModal')).hide();
                showImportResults(data);
            })
            .catch(err => {
                btnExecuteBatchImport.innerHTML = '<i class="bi bi-check2-all fs-5 me-1"></i> تأكيد وتوزيع كافة الأوراق المحددة في أقسامها الآن';
                btnExecuteBatchImport.removeAttribute('disabled');
                if (progressBar) progressBar.classList.add('d-none');
                console.error(err);
                alert('حدث خطأ أثناء توزيع الأوراق في النظام.');
            });
        });
    }

    function showImportResults(data) {
        const titleEl = document.getElementById('resultModalTitle');
        const msgEl = document.getElementById('resultModalMessage');
        const iconEl = document.getElementById('resultModalIcon');
        const totalEl = document.getElementById('resTotalCount');
        const succEl = document.getElementById('resSuccessCount');
        const dupEl = document.getElementById('resDuplicateCount');
        const errBox = document.getElementById('resultErrorsBox');
        const errList = document.getElementById('resultErrorsList');
        const linkEl = document.getElementById('resultTargetLink');

        if (data.success) {
            titleEl.textContent = 'اكتمل الاستيراد والتوزيع بنجاح! 🎉';
            msgEl.textContent = data.message || 'تم توزيع البيانات وحفظها بنجاح في الأقسام المخصصة.';
            iconEl.innerHTML = '<div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success mb-2"><i class="bi bi-check-circle-fill fs-1"></i></div>';
            totalEl.textContent = data.total_rows || data.total_imported || 0;
            succEl.textContent = data.imported || data.total_imported || 0;
            dupEl.textContent = (data.duplicates || data.total_duplicates || 0) + (data.skipped || data.total_skipped || 0);

            if (data.errors && data.errors.length > 0) {
                errBox.classList.remove('d-none');
                errList.innerHTML = '';
                data.errors.forEach(e => {
                    const li = document.createElement('li');
                    li.textContent = e;
                    errList.appendChild(li);
                });
            } else {
                errBox.classList.add('d-none');
            }

            linkEl.href = targetSectionLinks[currentSelectedSection] || "{{ route('dashboard.index') }}";
            linkEl.classList.remove('d-none');
        } else {
            titleEl.textContent = 'فشلت عملية الاستيراد';
            msgEl.textContent = data.message || 'حدث خطأ أثناء حفظ السجلات.';
            iconEl.innerHTML = '<div class="d-inline-flex p-3 rounded-circle bg-danger bg-opacity-10 text-danger mb-2"><i class="bi bi-x-circle-fill fs-1"></i></div>';
            totalEl.textContent = '0';
            succEl.textContent = '0';
            dupEl.textContent = '0';
            errBox.classList.add('d-none');
            linkEl.classList.add('d-none');
        }

        const resModal = new bootstrap.Modal(document.getElementById('importResultModal'));
        resModal.show();
    }

    // Reset view for importing another file
    const btnImportAnother = document.getElementById('btnImportAnotherFile');
    if (btnImportAnother) {
        btnImportAnother.addEventListener('click', function() {
            resetFileSelection();
        });
    }
});
</script>
