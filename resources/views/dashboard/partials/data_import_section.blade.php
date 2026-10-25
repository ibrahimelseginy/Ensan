{{-- ============================================================== --}}
{{-- SMART DATA IMPORT SECTION (EXCEL & CSV DISTRIBUTION HUB)       --}}
{{-- ============================================================== --}}

@php
    $sections = $sections ?? \App\Services\DataImportService::SECTIONS;
    $activeSection = $activeSection ?? 'cases';
@endphp

<div class="card border-0 shadow-sm rounded-4 mb-4 data-import-hero-card overflow-hidden">
    <div class="card-body p-3 p-md-4">
        
        {{-- Section Step Progress Indicator --}}
        <div class="import-steps-indicator mb-4 pb-3 border-bottom border-light border-opacity-50">
            <div class="row g-2 align-items-center text-center">
                <div class="col-4">
                    <div class="step-pill step-active" id="stepPill1">
                        <span class="step-num">1</span>
                        <span class="step-label">تحديد القسم المستهدف</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="step-pill" id="stepPill2">
                        <span class="step-num">2</span>
                        <span class="step-label">رفع ملف الإكسيل</span>
                    </div>
                </div>
                <div class="col-4">
                    <div class="step-pill" id="stepPill3">
                        <span class="step-num">3</span>
                        <span class="step-label">المعاينة وتوزيع البيانات</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Step 1: Section Selector Tabs / Pills --}}
        <div class="import-sections-wrapper mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2.5">
                <label class="form-label small fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                    <span class="badge bg-primary rounded-circle p-1" style="width: 20px; height: 20px; display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem;">1</span>
                    <span>حدد القسم المستهدف لحفظ وتوزيع السجلات:</span>
                </label>
                <span class="badge bg-light text-muted border small d-none d-md-inline-block">
                    القسم الحالي: <strong class="text-primary" id="activeSectionLabelName">الحالات وطلبات المساعدة</strong>
                </span>
            </div>

            <div class="d-flex flex-wrap gap-2.5" id="importSectionPills">
                @foreach($sections as $secKey => $secConfig)
                    <button type="button" 
                            class="btn import-sec-pill {{ $secKey === $activeSection ? 'active' : '' }}" 
                            data-section="{{ $secKey }}"
                            data-name="{{ $secConfig['name'] }}"
                            style="--sec-color: {{ $secConfig['color'] }};">
                        <div class="sec-pill-icon" style="background-color: {{ $secConfig['color'] }};">
                            <i class="bi {{ $secConfig['icon'] }}"></i>
                        </div>
                        <div class="text-start">
                            <div class="sec-pill-title">{{ $secConfig['name'] }}</div>
                            <div class="sec-pill-badge">{{ $secConfig['badge'] }}</div>
                        </div>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Step 2: Upload Box & Action Strip --}}
        <div class="row g-3 align-items-stretch">
            {{-- Dropzone Area --}}
            <div class="col-lg-8 col-md-12">
                <div class="import-dropzone p-4 rounded-4 text-center position-relative h-100 d-flex flex-column align-items-center justify-content-center" id="quickImportDropzone">
                    <input type="file" id="quickImportFileInput" class="import-file-input" accept=".xlsx,.xls,.csv">
                    
                    {{-- Default State (No File) --}}
                    <div class="dropzone-content" id="dropzoneDefaultState">
                        <div class="dropzone-icon mb-3">
                            <div class="icon-circle shadow-sm">
                                <i class="bi bi-cloud-arrow-up-fill text-success fs-1"></i>
                            </div>
                        </div>
                        <h5 class="fw-bold mb-1 text-main-heading">اسحب وأفلت ملف الإكسيل هنا أو <span class="text-success text-decoration-underline" style="cursor: pointer;">تصفح جهازك</span></h5>
                        <p class="text-muted small mb-3">يدعم ملفات Excel (.xlsx, .xls) وجداول CSV حتى 20 ميجابايت</p>
                        <div class="d-inline-flex align-items-center gap-2 bg-light px-3 py-1.5 rounded-pill border small text-muted">
                            <i class="bi bi-info-circle text-primary"></i>
                            <span>سيتم فحص أعمدة الملف ومطابقتها آلياً فور اختياره</span>
                        </div>
                    </div>

                    {{-- Selected File State (Visible once a file is picked) --}}
                    <div class="selected-file-card p-3 rounded-4 border bg-white shadow-sm w-100 d-none text-start" id="dropzoneSelectedState">
                        <div class="d-flex align-items-center justify-content-between gap-3 flex-wrap">
                            <div class="d-flex align-items-center gap-3">
                                <div class="file-card-icon bg-success-subtle text-success rounded-3 p-2.5 d-flex align-items-center justify-content-center" style="width: 52px; height: 52px;">
                                    <i class="bi bi-file-earmark-excel-fill fs-2"></i>
                                </div>
                                <div>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-success text-white rounded-pill px-2.5 py-1 x-small fw-bold d-inline-flex align-items-center gap-1">
                                            <span class="live-dot-mini"></span> جاهز للمعاينة والمطابقة
                                        </span>
                                        <span class="badge bg-light text-muted border x-small" id="selectedFileSizeBadge">0 KB</span>
                                    </div>
                                    <h6 class="fw-bold mb-0 text-dark text-break" id="selectedFileNameText">filename.xlsx</h6>
                                </div>
                            </div>

                            <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 py-1.5 fw-bold" id="btnRemoveSelectedFile" title="إزالة الملف واختيار غيره">
                                <i class="bi bi-trash3 me-1"></i> تغيير الملف
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Helper Sidebar & Actions --}}
            <div class="col-lg-4 col-md-12">
                <div class="p-3.5 rounded-4 h-100 d-flex flex-column justify-content-between import-side-helper-card border">
                    <div>
                        <h6 class="fw-bold text-main-heading mb-3 d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>مميزات الاستيراد الذكي:</span>
                        </h6>
                        <ul class="list-unstyled small text-muted mb-3 space-y-2.5">
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span><strong>مطابقة ذكية:</strong> ربط الأعمدة آلياً حتى مع اختلاف المسميات.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span><strong>معاينة حية:</strong> تدقيق وفحص البيانات قبل حفظها نهائياً.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                                <span><strong>فحص التكرار:</strong> تخطي السجلات المكررة بالرقم القومي أو الهاتف.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-3 border-top d-flex flex-column gap-2">
                        <button type="button" id="btnDownloadCurrentTemplate" class="btn btn-sm btn-outline-success rounded-pill w-100 py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2">
                            <i class="bi bi-file-earmark-arrow-down-fill fs-6"></i>
                            <span id="btnDownloadTemplateText">تحميل نموذج Excel جاهز للقسم</span>
                        </button>
                        <button type="button" id="btnStartQuickImport" class="btn btn-success rounded-pill w-100 py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 shadow-sm btn-start-import" disabled>
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
                        <div class="d-flex align-items-center gap-2 mb-0.5">
                            <h5 class="modal-title fw-bold mb-0 text-main-heading" id="importMappingModalLabel">مطابقة الأعمدة وتدقيق البيانات</h5>
                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2.5 py-1 x-small fw-bold" id="modalTargetSectionBadge">القسم: الحالات</span>
                        </div>
                        <p class="text-muted small mb-0">تم اكتشاف <strong id="modalTotalRowsCount" class="text-success">0</strong> صف بيانات صالح للمعالجة</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Info Alert --}}
                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3 p-3 mb-4 d-flex align-items-center gap-2.5 small">
                    <i class="bi bi-magic fs-4 flex-shrink-0 text-info"></i>
                    <div>
                        <strong>المطابقة التلقائية:</strong> قمنا بمطابقة أعمدة ملفك آلياً بالذكاء الاصطناعي مع الحقول المناسبة. يمكنك مراجعة وتعديل أي حقل، أو اختيار <em>"تخطي هذا العمود"</em> للأعمدة الإضافية التي لا ترغب باستيرادها.
                    </div>
                </div>

                {{-- Column Mapping Grid --}}
                <div class="card border rounded-3 mb-4 overflow-hidden shadow-2xs">
                    <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold small text-dark"><i class="bi bi-shuffle me-1 text-primary"></i> ربط أعمدة الملف بحقول النظام (Column Mapping)</span>
                        <span class="text-muted x-small">الحقول التي تحمل علامة (*) إلزامية لاستكمال الاستيراد</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3" id="mappingFieldsContainer">
                            {{-- Populated dynamically via JS --}}
                        </div>
                    </div>
                </div>

                {{-- Live Data Preview (First 5 Rows) --}}
                <div class="card border rounded-3 overflow-hidden shadow-2xs">
                    <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold small text-dark"><i class="bi bi-eye-fill me-1 text-success"></i> معاينة أولية لبيانات الملف (أول 5 صفوف)</span>
                        <span class="badge bg-light text-muted border small">تحقق من صحة ترتيب الأعمدة</span>
                    </div>
                    <div class="table-responsive" style="max-height: 240px;">
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

            <div class="modal-footer border-top py-3 px-4 bg-light-subtle d-flex justify-content-between">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إلغاء</button>
                <button type="button" class="btn btn-success rounded-pill px-5 py-2.5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnExecuteFinalImport">
                    <i class="bi bi-check2-circle fs-5"></i>
                    <span>تأكيد واستيراد البيانات الآن</span>
                </button>
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

            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إغلاق</button>
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
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
}

/* Step Indicator */
.step-pill {
    padding: 8px 12px;
    border-radius: 9999px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    color: #64748b;
    font-size: 0.8rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.2s ease;
    width: 100%;
    justify-content: center;
}
.step-pill .step-num {
    width: 22px;
    height: 22px;
    border-radius: 50%;
    background: #cbd5e1;
    color: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
}
.step-pill.step-active {
    background: rgba(16, 185, 129, 0.1);
    border-color: #10b981;
    color: #047857;
}
.step-pill.step-active .step-num {
    background: #10b981;
    color: #ffffff;
}

/* Section Pills */
.import-sec-pill {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 16px;
    padding: 9px 14px;
    color: #334155;
    font-size: 0.86rem;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    cursor: pointer;
}
.sec-pill-icon {
    width: 34px;
    height: 34px;
    border-radius: 10px;
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.05rem;
    flex-shrink: 0;
}
.sec-pill-title {
    font-weight: 800;
    line-height: 1.2;
}
.sec-pill-badge {
    font-size: 0.72rem;
    color: #64748b;
    margin-top: 2px;
}
.import-sec-pill:hover {
    background: #ffffff;
    border-color: var(--sec-color, #10b981);
    transform: translateY(-2px);
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
}
.import-sec-pill.active {
    background: #ffffff !important;
    border-color: var(--sec-color, #10b981) !important;
    box-shadow: 0 0 0 2px var(--sec-color, #10b981), 0 8px 20px rgba(0,0,0,0.06);
}
.import-sec-pill.active .sec-pill-title {
    color: var(--sec-color, #10b981);
}

/* Dropzone */
.import-dropzone {
    background: #f8fafc;
    border: 2.5px dashed #cbd5e1;
    cursor: pointer;
    transition: all 0.25s ease;
    min-height: 220px;
}
.import-dropzone:hover, .import-dropzone.dragover {
    border-color: #10b981;
    background: #f0fdf4;
}
.import-dropzone .icon-circle {
    width: 68px;
    height: 68px;
    border-radius: 50%;
    background: rgba(16, 185, 129, 0.1);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
}
.import-dropzone:hover .icon-circle {
    transform: scale(1.1);
}
.import-file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
    z-index: 5;
}
.import-side-helper-card {
    background: #f8fafc;
    border-color: #e2e8f0 !important;
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
.theme-dark .import-sec-pill {
    background: #0f172a;
    border-color: rgba(255, 255, 255, 0.1);
    color: #f8fafc;
}
.theme-dark .import-sec-pill.active {
    background: #1e293b !important;
}
.theme-dark .import-dropzone {
    background: #0f172a;
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
    let currentSelectedSection = '{{ $activeSection }}';
    let currentUploadedFile = null;
    let currentImportToken = null;
    let currentImportHeaders = [];
    let currentTargetFields = {};

    const templateBaseUrl = "{{ url('data-import/template') }}";
    const previewUrl = "{{ route('data-import.preview') }}";
    const processUrl = "{{ route('data-import.process') }}";
    const csrfToken = "{{ csrf_token() }}";

    const targetSectionLinks = {
        'cases': "{{ Route::has('case-applications.index') ? route('case-applications.index') : url('/case-applications') }}",
        'donors': "{{ Route::has('donors.index') ? route('donors.index') : url('/donors') }}",
        'donations': "{{ Route::has('donations.index') ? route('donations.index') : url('/donations') }}",
        'beneficiaries': "{{ Route::has('beneficiaries.index') ? route('beneficiaries.index') : url('/beneficiaries') }}",
        'tasks': "{{ Route::has('employee-tasks.index') ? route('employee-tasks.index') : url('/employee-tasks') }}"
    };

    const sectionLabels = {
        'cases': 'الحالات وطلبات المساعدة',
        'donors': 'المتبرعين',
        'donations': 'التبرعات المالية',
        'beneficiaries': 'المستفيدين والبحث الميداني',
        'tasks': 'مهام فريق العمل'
    };

    const activeSectionLabelName = document.getElementById('activeSectionLabelName');
    const btnDownloadTemplateText = document.getElementById('btnDownloadTemplateText');

    function updateActiveSectionLabels() {
        const name = sectionLabels[currentSelectedSection] || 'القسم';
        if (activeSectionLabelName) activeSectionLabelName.textContent = name;
        if (btnDownloadTemplateText) btnDownloadTemplateText.textContent = 'تحميل نموذج ' + name + ' جاهز';
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
            window.location.href = templateBaseUrl + '/' + currentSelectedSection;
        });
    }

    // 3. File Input Handling
    const fileInput = document.getElementById('quickImportFileInput');
    const dropzone = document.getElementById('quickImportDropzone');
    const defaultState = document.getElementById('dropzoneDefaultState');
    const selectedState = document.getElementById('dropzoneSelectedState');
    const selectedFileNameText = document.getElementById('selectedFileNameText');
    const selectedFileSizeBadge = document.getElementById('selectedFileSizeBadge');
    const btnRemoveFile = document.getElementById('btnRemoveSelectedFile');
    const btnStart = document.getElementById('btnStartQuickImport');
    const stepPill2 = document.getElementById('stepPill2');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
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
        btnStart.setAttribute('disabled', 'disabled');
    }

    if (btnRemoveFile) {
        btnRemoveFile.addEventListener('click', function(e) {
            e.stopPropagation();
            resetFileSelection();
        });
    }

    // Drag and Drop Effects
    if (dropzone) {
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

    // 4. Start Upload & Preview (Step 1 -> Modal)
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

            btnStart.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> جاري فحص ومطابقة الملف...';
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

                populateMappingModal(data);
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

    // Populate Mapping UI and Preview Table inside Modal
    function populateMappingModal(data) {
        document.getElementById('modalTotalRowsCount').textContent = data.total_rows;
        document.getElementById('modalTargetSectionBadge').textContent = 'القسم: ' + data.section_name;

        // 1. Build mapping fields
        const container = document.getElementById('mappingFieldsContainer');
        container.innerHTML = '';

        data.headers.forEach((headerName, colIdx) => {
            const suggestedField = data.suggested_mapping[colIdx] || '';
            const colDiv = document.createElement('div');
            colDiv.className = 'col-md-6 col-lg-4';

            let optionsHtml = '<option value="skip">-- تخطي هذا العمود (عدم استيراده) --</option>';
            for (const [fKey, fMeta] of Object.entries(data.target_fields)) {
                const isSelected = (suggestedField === fKey) ? 'selected' : '';
                const reqStar = fMeta.required ? ' * (مطلوب)' : '';
                optionsHtml += `<option value="${fKey}" ${isSelected}>${fMeta.label}${reqStar}</option>`;
            }

            const isMatched = suggestedField !== '';
            colDiv.innerHTML = `
                <div class="p-3 rounded-3 border ${isMatched ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-light'}">
                    <div class="d-flex align-items-center justify-content-between mb-1.5">
                        <span class="badge bg-secondary-subtle text-secondary x-small">عمود إكسيل #${colIdx + 1}</span>
                        ${isMatched ? '<span class="badge bg-success-subtle text-success x-small fw-bold"><i class="bi bi-magic me-1"></i>مطابقة مؤكدة</span>' : '<span class="badge bg-light text-muted border x-small">غير محدد</span>'}
                    </div>
                    <div class="fw-bold small text-truncate mb-2 text-dark" title="${headerName}">
                        ${headerName || ('عمود بدون عنوان #' + (colIdx + 1))}
                    </div>
                    <select class="form-select form-select-sm column-map-select" data-col="${colIdx}">
                        ${optionsHtml}
                    </select>
                </div>
            `;
            container.appendChild(colDiv);
        });

        // 2. Build Live Preview Table
        const head = document.getElementById('previewTableHead');
        const body = document.getElementById('previewTableBody');
        head.innerHTML = '';
        body.innerHTML = '';

        let headRow = '<tr><th>#</th>';
        data.headers.forEach(h => {
            headRow += `<th class="fw-bold">${h}</th>`;
        });
        headRow += '</tr>';
        head.innerHTML = headRow;

        data.preview_rows.forEach((row, rIdx) => {
            let tr = `<tr><td class="text-muted fw-bold">${rIdx + 1}</td>`;
            data.headers.forEach((_, cIdx) => {
                const cellVal = row[cIdx] !== undefined ? row[cIdx] : '';
                tr += `<td>${cellVal}</td>`;
            });
            tr += '</tr>';
            body.innerHTML += tr;
        });
    }

    // 5. Confirm and Execute Import (Step 2)
    const btnExecute = document.getElementById('btnExecuteFinalImport');
    if (btnExecute) {
        btnExecute.addEventListener('click', function() {
            // Collect mapping
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

            fetch(processUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({
                    token: currentImportToken,
                    section: currentSelectedSection,
                    mapping: mapping
                })
            })
            .then(res => res.json())
            .then(data => {
                btnExecute.innerHTML = '<i class="bi bi-check2-circle fs-5 me-1"></i> تأكيد واستيراد البيانات الآن';
                btnExecute.removeAttribute('disabled');

                // Hide mapping modal
                bootstrap.Modal.getInstance(document.getElementById('importMappingModal')).hide();

                // Show Result Modal
                showImportResults(data);
            })
            .catch(err => {
                btnExecute.innerHTML = '<i class="bi bi-check2-circle fs-5 me-1"></i> تأكيد واستيراد البيانات الآن';
                btnExecute.removeAttribute('disabled');
                console.error(err);
                alert('حدث خطأ غير متوقع أثناء معالجة البيانات.');
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
            titleEl.textContent = 'اكتمل الاستيراد بنجاح! 🎉';
            msgEl.textContent = data.message || 'تم توزيع البيانات وحفظها بنجاح في القسم المخصص.';
            iconEl.innerHTML = '<div class="d-inline-flex p-3 rounded-circle bg-success bg-opacity-10 text-success mb-2"><i class="bi bi-check-circle-fill fs-1"></i></div>';
            totalEl.textContent = data.total_rows || 0;
            succEl.textContent = data.imported || 0;
            dupEl.textContent = (data.duplicates || 0) + (data.skipped || 0);

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

            linkEl.href = targetSectionLinks[currentSelectedSection] || '#';
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
});
</script>
