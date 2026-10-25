{{-- ============================================================== --}}
{{-- SMART DATA IMPORT SECTION (EXCEL & CSV DISTRIBUTION HUB)       --}}
{{-- ============================================================== --}}

<div class="card border-0 shadow-sm rounded-4 mb-4 data-import-hero-card overflow-hidden">
    <div class="card-body p-4">
        {{-- Section Header --}}
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
            <div class="d-flex align-items-center gap-3">
                <div class="import-header-icon-wrap shadow-sm">
                    <i class="bi bi-file-earmark-spreadsheet-fill text-success fs-3"></i>
                </div>
                <div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="badge rounded-pill bg-success-subtle text-success border border-success-subtle px-2.5 py-1 fw-bold fs-7">
                            <i class="bi bi-lightning-charge-fill me-1"></i> توزيع تلقائي ذكي
                        </span>
                        <span class="badge rounded-pill bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 fw-bold fs-7">
                            يدعم .xlsx و .csv
                        </span>
                    </div>
                    <h4 class="fw-bold mb-0 text-main-heading">استيراد وتوزيع البيانات آلياً من ملفات الإكسيل (Excel Hub)</h4>
                    <p class="text-muted small mb-0 mt-0.5">ارفع ملف إكسيل ليتم توزيع البيانات وحفظها في القسم المخصص لها فوراً بدلاً من الإدخال اليدوي</p>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="{{ route('data-import.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 fw-bold small d-inline-flex align-items-center gap-1.5 shadow-2xs">
                    <i class="bi bi-window-stack"></i>
                    <span>مركز الاستيراد الكامل</span>
                </a>
            </div>
        </div>

        {{-- Section Selector Tabs / Pills --}}
        <div class="import-sections-tabs mb-3">
            <label class="form-label small fw-bold text-muted mb-2 d-flex align-items-center gap-1">
                <i class="bi bi-pin-angle-fill text-primary"></i> 1. حدد القسم المستهدف في النظام:
            </label>
            <div class="d-flex flex-wrap gap-2" id="importSectionPills">
                <button type="button" class="btn import-sec-pill active" data-section="cases">
                    <i class="bi bi-file-earmark-medical me-1.5 text-emerald"></i>
                    <span class="fw-bold">الحالات وطلبات المساعدة</span>
                </button>
                <button type="button" class="btn import-sec-pill" data-section="donors">
                    <i class="bi bi-heart-fill me-1.5 text-warning"></i>
                    <span class="fw-bold">المتبرعين</span>
                </button>
                <button type="button" class="btn import-sec-pill" data-section="donations">
                    <i class="bi bi-cash-coin me-1.5 text-info"></i>
                    <span class="fw-bold">التبرعات المالية</span>
                </button>
                <button type="button" class="btn import-sec-pill" data-section="beneficiaries">
                    <i class="bi bi-people-fill me-1.5 text-purple"></i>
                    <span class="fw-bold">المستفيدين والبحث الميداني</span>
                </button>
                <button type="button" class="btn import-sec-pill" data-section="tasks">
                    <i class="bi bi-kanban-fill me-1.5 text-pink"></i>
                    <span class="fw-bold">مهام فريق العمل</span>
                </button>
            </div>
        </div>

        {{-- Upload Box & Action Strip --}}
        <div class="row g-3 align-items-stretch">
            <div class="col-lg-8 col-md-12">
                <div class="import-dropzone p-4 rounded-4 text-center position-relative" id="quickImportDropzone">
                    <input type="file" id="quickImportFileInput" class="import-file-input" accept=".xlsx,.xls,.csv">
                    <div class="dropzone-content">
                        <div class="dropzone-icon mb-2">
                            <i class="bi bi-cloud-arrow-up-fill text-success fs-1"></i>
                        </div>
                        <h6 class="fw-bold mb-1 text-main-heading">اسحب وأفلت ملف الإكسيل هنا أو <span class="text-primary text-decoration-underline" style="cursor: pointer;">تصفح جهازك</span></h6>
                        <p class="text-muted small mb-2">يدعم ملفات Excel (.xlsx, .xls) وجداول CSV حتى 20 ميجابايت</p>
                        <div class="selected-file-name badge bg-light text-dark border px-3 py-1.5 fw-bold d-none" id="quickSelectedFileBadge">
                            <i class="bi bi-file-earmark-check-fill text-success me-1"></i> <span id="quickSelectedFileName"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4 col-md-12">
                <div class="p-3.5 rounded-4 h-100 d-flex flex-column justify-content-between import-side-helper-card border">
                    <div>
                        <h6 class="fw-bold text-main-heading mb-2 d-flex align-items-center gap-1.5">
                            <i class="bi bi-shield-check text-success fs-5"></i>
                            <span>مميزات الاستيراد الذكي:</span>
                        </h6>
                        <ul class="list-unstyled small text-muted mb-3 space-y-2">
                            <li class="d-flex align-items-start gap-2 mb-1.5">
                                <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                <span>مطابقة الأعمدة آلياً بالذكاء الاصطناعي حتى لو اختلفت مسمياتها.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-1.5">
                                <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                <span>معاينة حية وتدقيق البيانات قبل اعتماد حفظها في القاعدة.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2 mb-1.5">
                                <i class="bi bi-check-circle-fill text-success mt-0.5"></i>
                                <span>حماية ضد السجلات المكررة بالرقم القومي أو الهاتف.</span>
                            </li>
                        </ul>
                    </div>

                    <div class="pt-2 border-top d-flex flex-column gap-2">
                        <button type="button" id="btnDownloadCurrentTemplate" class="btn btn-sm btn-outline-success rounded-pill w-100 py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5">
                            <i class="bi bi-download"></i>
                            <span>تحميل نموذج Excel جاهز للقسم</span>
                        </button>
                        <button type="button" id="btnStartQuickImport" class="btn btn-sm btn-success rounded-pill w-100 py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5 shadow-sm" disabled>
                            <i class="bi bi-arrow-left-right"></i>
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
            <div class="modal-header border-bottom py-3 px-4 bg-light-subtle">
                <div class="d-flex align-items-center gap-2.5">
                    <div class="p-2 rounded-3 bg-success bg-opacity-10 text-success">
                        <i class="bi bi-sliders fs-5"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0 text-main-heading" id="importMappingModalLabel">مطابقة الأعمدة وتدقيق البيانات قبل الحفظ</h5>
                        <p class="text-muted small mb-0">تم التعرف على <span id="modalTotalRowsCount" class="fw-bold text-success">0</span> صف بيانات في الملف</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                {{-- Info Alert --}}
                <div class="alert alert-info border-0 bg-info-subtle text-info-emphasis rounded-3 p-3 mb-4 d-flex align-items-center gap-2 small">
                    <i class="bi bi-info-circle-fill fs-5 flex-shrink-0"></i>
                    <div>
                        <strong>المطابقة الذكية:</strong> قمنا بمطابقة أعمدة ملفك آلياً مع حقول النظام. يمكنك مراجعة وتعديل أي حقل، أو اختيار "تخطي العمود" للأعمدة التي لا تريد استيرادها.
                    </div>
                </div>

                {{-- Column Mapping Grid --}}
                <div class="card border rounded-3 mb-4 overflow-hidden">
                    <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold small text-dark"><i class="bi bi-shuffle me-1 text-primary"></i> خريطة مطابقة الأعمدة (Column Mapping)</span>
                        <span class="badge bg-secondary-subtle text-secondary small" id="modalTargetSectionBadge">القسم: الحالات</span>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-3" id="mappingFieldsContainer">
                            {{-- Populated dynamically via JS --}}
                        </div>
                    </div>
                </div>

                {{-- Live Data Preview (First 5 Rows) --}}
                <div class="card border rounded-3 overflow-hidden">
                    <div class="card-header bg-light py-2.5 px-3 border-bottom d-flex align-items-center justify-content-between">
                        <span class="fw-bold small text-dark"><i class="bi bi-eye-fill me-1 text-success"></i> معاينة أول 5 صفوف من الملف</span>
                        <span class="badge bg-light text-muted border small">معاينة أولية</span>
                    </div>
                    <div class="table-responsive" style="max-height: 240px;">
                        <table class="table table-sm table-hover table-bordered mb-0 align-middle small text-nowrap" id="previewDataTable">
                            <thead class="table-light" id="previewTableHead">
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
                <button type="button" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm d-inline-flex align-items-center gap-2" id="btnExecuteFinalImport">
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
                    <div class="p-2.5 rounded-3 bg-light border">
                        <div class="fs-4 fw-bold text-primary" id="resTotalCount">0</div>
                        <div class="text-muted x-small">إجمالي الصفوف</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2.5 rounded-3 bg-success-subtle border border-success-subtle">
                        <div class="fs-4 fw-bold text-success" id="resSuccessCount">0</div>
                        <div class="text-success x-small fw-bold">تم حفظها</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="p-2.5 rounded-3 bg-warning-subtle border border-warning-subtle">
                        <div class="fs-4 fw-bold text-warning" id="resDuplicateCount">0</div>
                        <div class="text-warning-emphasis x-small">مكررة/تم تخطيها</div>
                    </div>
                </div>
            </div>

            <div id="resultErrorsBox" class="alert alert-warning text-start small mb-4 d-none p-3 rounded-3" style="max-height: 140px; overflow-y: auto;">
                <div class="fw-bold mb-1 text-dark"><i class="bi bi-exclamation-triangle me-1"></i> ملاحظات السطور المستبعدة:</div>
                <ul class="mb-0 ps-3 space-y-1" id="resultErrorsList"></ul>
            </div>

            <div class="d-flex justify-content-center gap-2">
                <button type="button" class="btn btn-secondary rounded-pill px-4 fw-bold" data-bs-dismiss="modal">إغلاق</button>
                <a href="#" id="resultTargetLink" class="btn btn-primary rounded-pill px-4 fw-bold">
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

.import-header-icon-wrap {
    width: 52px;
    height: 52px;
    border-radius: 16px;
    background: #ecfdf5;
    border: 1px solid #a7f3d0;
    display: flex;
    align-items: center;
    justify-content: center;
}

.import-sec-pill {
    background: #f8fafc;
    border: 1.5px solid #e2e8f0;
    border-radius: 50px;
    padding: 7px 16px;
    color: #475569;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}

.import-sec-pill:hover {
    background: #f1f5f9;
    border-color: #cbd5e1;
    color: #0f172a;
}

.import-sec-pill.active {
    background: #0f172a !important;
    border-color: #0f172a !important;
    color: #ffffff !important;
    box-shadow: 0 3px 10px rgba(15, 23, 42, 0.25);
}

.import-dropzone {
    background: #f8fafc;
    border: 2px dashed #cbd5e1;
    cursor: pointer;
    transition: all 0.25s ease;
}

.import-dropzone:hover, .import-dropzone.dragover {
    border-color: #10b981;
    background: #f0fdf4;
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

/* Dark Mode Styles */
.theme-dark .data-import-hero-card,
html.theme-dark .data-import-hero-card,
body.theme-dark .data-import-hero-card {
    background: #111d33 !important;
    border: 1px solid rgba(148, 163, 184, 0.18) !important;
}

.theme-dark .import-header-icon-wrap {
    background: rgba(16, 185, 129, 0.18);
    border-color: rgba(16, 185, 129, 0.35);
}

.theme-dark .import-sec-pill {
    background: #1e293b;
    border-color: rgba(148, 163, 184, 0.2);
    color: #94a3b8;
}

.theme-dark .import-sec-pill.active {
    background: #10b981 !important;
    border-color: #10b981 !important;
    color: #ffffff !important;
}

.theme-dark .import-dropzone {
    background: #0f172a;
    border-color: rgba(148, 163, 184, 0.25);
}

.theme-dark .import-dropzone:hover,
.theme-dark .import-dropzone.dragover {
    background: rgba(16, 185, 129, 0.1);
    border-color: #10b981;
}

.theme-dark .import-side-helper-card {
    background: #1e293b;
    border-color: rgba(148, 163, 184, 0.18) !important;
}

.theme-dark .text-main-heading {
    color: #f8fafc !important;
}
</style>

{{-- Interactive JavaScript for Smart Import --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentSelectedSection = 'cases';
    let currentUploadedFile = null;
    let currentImportToken = null;
    let currentImportHeaders = [];
    let currentTargetFields = {};

    const templateBaseUrl = "{{ url('data-import/template') }}";
    const previewUrl = "{{ route('data-import.preview') }}";
    const processUrl = "{{ route('data-import.process') }}";
    const csrfToken = "{{ csrf_token() }}";

    const targetSectionLinks = {
        'cases': "{{ route('cases.case-applications') }}",
        'donors': "{{ route('donors.index') }}",
        'donations': "{{ route('donations.index') }}",
        'beneficiaries': "{{ route('beneficiaries.index') }}",
        'tasks': "{{ route('employee-tasks.index') }}"
    };

    // 1. Section Pill Change
    const sectionPills = document.querySelectorAll('.import-sec-pill');
    sectionPills.forEach(pill => {
        pill.addEventListener('click', function() {
            sectionPills.forEach(p => p.classList.remove('active'));
            this.classList.add('active');
            currentSelectedSection = this.getAttribute('data-section');
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
    const selectedBadge = document.getElementById('quickSelectedFileBadge');
    const selectedFileName = document.getElementById('quickSelectedFileName');
    const btnStart = document.getElementById('btnStartQuickImport');

    if (fileInput) {
        fileInput.addEventListener('change', function(e) {
            if (this.files && this.files[0]) {
                handleFileSelected(this.files[0]);
            }
        });
    }

    function handleFileSelected(file) {
        currentUploadedFile = file;
        selectedFileName.textContent = file.name + ' (' + (file.size / 1024).toFixed(1) + ' KB)';
        selectedBadge.classList.remove('d-none');
        btnStart.removeAttribute('disabled');
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

            btnStart.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> جاري فحص الملف...';
            btnStart.setAttribute('disabled', 'disabled');

            fetch(previewUrl, {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btnStart.innerHTML = '<i class="bi bi-arrow-left-right me-1"></i> بدء المعاينة ومطابقة الأعمدة';
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
                btnStart.innerHTML = '<i class="bi bi-arrow-left-right me-1"></i> بدء المعاينة ومطابقة الأعمدة';
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
                <div class="p-2.5 rounded-3 border ${isMatched ? 'border-success-subtle bg-success-subtle bg-opacity-10' : 'bg-light'}">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="badge bg-secondary-subtle text-secondary x-small">عمود الملف #${colIdx + 1}</span>
                        ${isMatched ? '<span class="badge bg-success-subtle text-success x-small fw-bold"><i class="bi bi-magic me-1"></i>مطابقة ذكية</span>' : ''}
                    </div>
                    <div class="fw-bold small text-truncate mb-1.5" title="${headerName}">
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
