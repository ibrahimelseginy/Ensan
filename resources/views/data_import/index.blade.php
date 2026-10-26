@extends('layouts.app')

@section('title', 'مركز استيراد وتوزيع البيانات (Excel & CSV Hub) — منظومة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    {{-- Executive Hero Banner --}}
    <div class="excel-hub-hero p-4 p-md-5 rounded-4 mb-4 text-white shadow-sm position-relative overflow-hidden">
        {{-- Background decorative shapes --}}
        <div class="hero-bg-circle hero-bg-circle-1"></div>
        <div class="hero-bg-circle hero-bg-circle-2"></div>
        <div class="hero-grid-pattern"></div>

        <div class="position-relative" style="z-index: 2;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-white bg-opacity-15 rounded-pill px-3 py-1.5 small border border-white border-opacity-20 backdrop-blur">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white text-decoration-none opacity-90"><i class="bi bi-house-door-fill me-1"></i> الرئيسية</a></li>
                        <li class="breadcrumb-item active text-white fw-bold" aria-current="page">مركز استيراد وتوزيع البيانات</li>
                    </ol>
                </nav>

                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-light bg-opacity-20 text-white border border-white border-opacity-25 rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5 shadow-sm hover-lift backdrop-blur" id="btnScrollToTemplates">
                        <i class="bi bi-collection-fill"></i>
                        <span>نماذج وقوالب الأقسام</span>
                    </button>
                    <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-light rounded-pill px-3.5 py-2 fw-bold text-success d-inline-flex align-items-center gap-1.5 shadow-sm hover-lift">
                        <i class="bi bi-arrow-right"></i>
                        <span>العودة للرئيسية</span>
                    </a>
                </div>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="hero-excel-icon shadow-sm">
                            <i class="bi bi-file-earmark-spreadsheet-fill fs-2"></i>
                        </div>
                        <div>
                            <div class="d-flex align-items-center gap-2 mb-1 flex-wrap">
                                <h2 class="fw-black mb-0 text-white" style="letter-spacing: -0.5px;">
                                    مركز الاستيراد والتوزيع الذكي للبيانات
                                </h2>
                                <span class="badge bg-white text-success rounded-pill px-2.5 py-1 x-small fw-bold shadow-xs">
                                    <i class="bi bi-stars text-warning me-1"></i> الجيل الثاني 2.0
                                </span>
                            </div>
                            <p class="mb-0 text-white text-opacity-85 small fs-6">
                                منصة متطورة لمعالجة وتوزيع ملفات Excel و CSV آلياً لدمج آلاف السجلات دفعة واحدة في قطاعات الحالات، التبرعات، المتبرعين، المستفيدين، والمهام
                            </p>
                        </div>
                    </div>

                    {{-- Live Capability Badges --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-1">
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small backdrop-blur">
                            <i class="bi bi-magic text-warning fs-6"></i>
                            <span>مطابقة ذكية للأعمدة بالذكاء الاصطناعي</span>
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small backdrop-blur">
                            <i class="bi bi-shield-check text-info fs-6"></i>
                            <span>فحص وتخطي التكرار تلقائياً</span>
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small backdrop-blur">
                            <i class="bi bi-layers-fill text-light fs-6"></i>
                            <span>توزيع شامل لملفات متعددة الأوراق (Multi-Sheet)</span>
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small backdrop-blur">
                            <i class="bi bi-eye-fill text-light fs-6"></i>
                            <span>معاينة حية وتدقيق قبل الحفظ</span>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end d-none d-lg-block">
                    <div class="excel-hero-illustration">
                        <div class="stat-pill-float shadow-lg mb-2">
                            <div class="stat-pill-icon bg-success-subtle text-success">
                                <i class="bi bi-check2-circle fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">توزيع تلقائي 100%</div>
                                <div class="text-muted x-small">مباشرة في قاعدة بيانات النظام</div>
                            </div>
                        </div>
                        <div class="stat-pill-float shadow-lg">
                            <div class="stat-pill-icon bg-primary-subtle text-primary">
                                <i class="bi bi-shield-lock-fill fs-4"></i>
                            </div>
                            <div>
                                <div class="fw-bold small text-dark">حماية من التكرار المزدوج</div>
                                <div class="text-muted x-small">بالرقم القومي ورقم الهاتف</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Interactive Smart Import Hub Section --}}
    <div id="importSectionCard">
        @include('dashboard.partials.data_import_section')
    </div>

    {{-- Section Templates & Reference Cards --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 templates-overview-card overflow-hidden" id="sectionTemplatesContainer">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-2">
                <div>
                    <h5 class="fw-bold mb-1 text-main d-flex align-items-center gap-2">
                        <span class="p-2 rounded-3 bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center">
                            <i class="bi bi-collection-fill fs-5"></i>
                        </span>
                        <span>نماذج وقوالب الأقسام المتاحة للاستيراد</span>
                    </h5>
                    <p class="text-muted small mb-0 mt-1">يمكنك تحميل النموذج القياسي الفارغ لكل قسم، أو رفع أي ملف واستخدام المطابقة الذكية بالذكاء الاصطناعي</p>
                </div>
                
                <div class="d-flex align-items-center gap-2">
                    {{-- Search Box for Templates --}}
                    <div class="input-group input-group-sm template-search-box" style="width: 250px;">
                        <span class="input-group-text bg-light border-end-0 text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" id="templateSearchInput" class="form-control border-start-0 bg-light" placeholder="ابحث في الأقسام أو الحقول...">
                    </div>
                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5">
                        <i class="bi bi-check2-all"></i>
                        <span>{{ count($sections) }} أقسام مدعومة</span>
                    </span>
                </div>
            </div>
        </div>
        <div class="card-body p-4 pt-3">
            <div class="row g-3 g-lg-4" id="templateCardsRow">
                @foreach($sections as $secKey => $secConfig)
                    <div class="col-12 col-md-6 col-xl-6 template-card-col mb-1" data-name="{{ $secConfig['name'] }}" data-desc="{{ $secConfig['description'] }}" data-fields="{{ implode(' ', array_column($secConfig['fields'], 'label')) }}">
                        <div class="template-box p-4 rounded-4 border h-100 d-flex flex-column justify-content-between position-relative" style="--sec-accent: {{ $secConfig['color'] }};">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-3 border-bottom border-light-subtle">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="template-icon shadow-sm" style="background: linear-gradient(135deg, {{ $secConfig['color'] }}, color-mix(in srgb, {{ $secConfig['color'] }} 80%, black));">
                                            <i class="bi {{ $secConfig['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-1 text-main fs-6">{{ $secConfig['name'] }}</h6>
                                            <span class="badge rounded-pill px-2.5 py-0.5 x-small fw-bold" style="background: {{ $secConfig['color'] }}1a; color: {{ $secConfig['color'] }}; border: 1px solid {{ $secConfig['color'] }}33;">
                                                {{ $secConfig['badge'] }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border px-3 py-1.5 rounded-pill small fw-bold d-inline-flex align-items-center gap-1.5 shadow-2xs">
                                        <i class="bi bi-columns-gap text-muted"></i>
                                        <span>{{ count($secConfig['fields']) }} أعمدة</span>
                                    </span>
                                </div>
                                
                                <p class="text-secondary small mb-3 template-desc lh-base">{{ $secConfig['description'] }}</p>

                                <div class="template-fields-section p-3 rounded-3 mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2 flex-wrap gap-1">
                                        <span class="text-dark fw-bold x-small d-inline-flex align-items-center gap-1.5">
                                            <i class="bi bi-tag-fill" style="color: {{ $secConfig['color'] }};"></i>
                                            <span>الأعمدة والحقول المعتمدة:</span>
                                        </span>
                                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2 py-0.5 x-small fw-bold">
                                            <span class="field-req-dot me-1"></span> (*) حقول إلزامية
                                        </span>
                                    </div>
                                    <div class="template-fields-wrap d-flex flex-wrap gap-1.5">
                                        @foreach($secConfig['fields'] as $fKey => $fMeta)
                                            <span class="template-field-pill {{ $fMeta['required'] ? 'field-pill-req' : 'field-pill-opt' }}" title="{{ $fMeta['required'] ? 'حقل إلزامي للاستيراد في هذا النموذج' : 'حقل اختياري' }}">
                                                @if($fMeta['required'])
                                                    <span class="field-req-dot"></span>
                                                @endif
                                                <span>{{ $fMeta['label'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="template-actions pt-3 border-top mt-auto d-flex align-items-stretch gap-2.5">
                                <a href="{{ route('data-import.template', $secKey) }}" class="btn btn-template-download rounded-pill flex-fill py-2.5 fw-bold d-inline-flex align-items-center justify-content-center gap-2 hover-lift" title="تحميل قالب Excel جاهز بالأعمدة القياسية">
                                    <i class="bi bi-file-earmark-arrow-down-fill text-success fs-5"></i>
                                    <span>تحميل النموذج (Excel)</span>
                                </a>
                                <button type="button" class="btn btn-template-import rounded-pill flex-fill py-2.5 fw-bold text-white d-inline-flex align-items-center justify-content-center gap-2 btn-quick-select-section hover-lift shadow-sm" data-section="{{ $secKey }}" style="--sec-btn-bg: {{ $secConfig['color'] }};" title="تحديد هذا القسم للاستيراد والرفع الآن">
                                    <i class="bi bi-cloud-arrow-up-fill fs-5"></i>
                                    <span>استيراد السجلات الآن</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- No templates search result --}}
            <div id="noTemplatesFoundAlert" class="alert alert-light border text-center py-4 rounded-4 d-none">
                <i class="bi bi-search fs-2 text-muted mb-2 d-block"></i>
                <div class="fw-bold text-dark mb-1">لم يتم العثور على نتائج مطابقة للبحث</div>
                <div class="text-muted small">يرجى تجربة كلمات بحث أخرى مثل "حالات"، "متبرع"، "هاتف"، أو "تبرعات"</div>
            </div>
        </div>
    </div>

    {{-- Import Guidelines Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 import-guidelines-card border border-success border-opacity-25 overflow-hidden">
        <div class="card-body p-4">
            <h5 class="fw-bold text-success mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightbulb-fill"></i>
                <span>إرشادات ونصائح للحصول على أفضل نتائج أثناء استيراد وتوزيع البيانات:</span>
            </h5>
            <div class="row g-3 small text-secondary">
                <div class="col-md-4">
                    <div class="guideline-item p-3.5 rounded-3 h-100 bg-white shadow-2xs border">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">الصف الأول يحتوي عناوين الأعمدة:</strong>
                                <div>تأكد من أن الصف الأول في الملف يحتوي على أسماء وعناوين الأعمدة بوضوح ليتعرف عليها محرك المطابقة الذكي تلقائياً حتى مع اختلاف صياغتها.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="guideline-item p-3.5 rounded-3 h-100 bg-white shadow-2xs border">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">تنسيق أرقام الهواتف والأرقام القومية:</strong>
                                <div>تأكد من كتابة الأرقام بدون مسافات أو فواصل أو رموز خاصة لضمان حفظها ومطابقتها وفحص تكرارها بشكل دقيق مع سجلات النظام.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="guideline-item p-3.5 rounded-3 h-100 bg-white shadow-2xs border">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">الحماية من التكرار المزدوج:</strong>
                                <div>يقوم النظام بفحص السجلات المكررة بالرقم القومي أو الهاتف وتخطيها تلقائياً لتفادي ازدواجية البيانات بقاعدة البيانات مع إظهار تقرير تفصيلي بالصفوف المستبعدة.</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

{{-- Page Specific Styles --}}
<style>
.excel-hub-hero {
    background: linear-gradient(135deg, #064e3b 0%, #047857 50%, #059669 100%) !important;
    position: relative;
    box-shadow: 0 10px 30px rgba(4, 120, 87, 0.25) !important;
}
.hero-bg-circle {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    z-index: 1;
}
.hero-bg-circle-1 {
    top: -80px;
    left: -80px;
    width: 320px;
    height: 320px;
    background: radial-gradient(circle, rgba(255,255,255,0.18) 0%, rgba(255,255,255,0) 70%);
}
.hero-bg-circle-2 {
    bottom: -100px;
    right: 15%;
    width: 280px;
    height: 280px;
    background: radial-gradient(circle, rgba(16,185,129,0.3) 0%, rgba(255,255,255,0) 70%);
}
.hero-grid-pattern {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
    background-size: 24px 24px;
    opacity: 0.6;
    pointer-events: none;
    z-index: 1;
}
.backdrop-blur {
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
}
.hero-excel-icon {
    width: 62px;
    height: 62px;
    border-radius: 18px;
    background: rgba(255, 255, 255, 0.22);
    border: 1px solid rgba(255, 255, 255, 0.4);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    backdrop-filter: blur(8px);
}
.stat-pill-float {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: #ffffff;
    padding: 12px 18px;
    border-radius: 18px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
    text-align: right;
    transition: transform 0.2s ease;
}
.stat-pill-float:hover {
    transform: translateY(-2px);
}
.stat-pill-icon {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}
.template-box {
    background: #ffffff;
    border: 1px solid #e2e8f0 !important;
    border-radius: 20px !important;
    overflow: hidden;
    position: relative;
    transition: all 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
}
.template-box::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--sec-accent, #10b981);
    transition: height 0.25s ease;
}
.template-box:hover {
    transform: translateY(-5px);
    border-color: var(--sec-accent, #10b981) !important;
    box-shadow: 0 16px 36px -6px rgba(0, 0, 0, 0.08), 0 0 0 1px var(--sec-accent, #10b981) !important;
}
.template-box:hover::before {
    height: 6px;
}
.template-icon {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.35rem;
    color: #ffffff;
    flex-shrink: 0;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.12);
}
.template-desc {
    min-height: 42px;
    font-size: 0.86rem;
    color: #475569;
}
.template-fields-section {
    background: #f8fafc;
    border: 1px solid #f1f5f9;
    border-radius: 14px;
}
.template-fields-wrap {
    min-height: 52px;
    align-content: flex-start;
}
.template-field-pill {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 9999px;
    font-size: 0.76rem;
    font-weight: 600;
    line-height: 1.3;
    transition: all 0.15s ease;
}
.field-pill-opt {
    background: #ffffff;
    color: #475569;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
}
.field-pill-req {
    background: #fff1f2;
    color: #be123c;
    border: 1px solid #fecdd3;
    font-weight: 700;
}
.field-req-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #e11d48;
    display: inline-block;
    box-shadow: 0 0 0 2px rgba(225, 29, 72, 0.2);
}
.template-field-pill:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.06);
}
.btn-template-download {
    background: #ffffff;
    color: #047857 !important;
    border: 1.5px solid #10b981;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}
.btn-template-download:hover {
    background: #ecfdf5;
    border-color: #059669;
    color: #065f46 !important;
    transform: translateY(-1px);
}
.btn-template-import {
    background: linear-gradient(135deg, var(--sec-btn-bg, #059669), color-mix(in srgb, var(--sec-btn-bg, #059669) 80%, black));
    border: none;
    font-size: 0.85rem;
    transition: all 0.2s ease;
}
.btn-template-import:hover {
    filter: brightness(1.08);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
}
.templates-overview-card, .import-guidelines-card {
    background: var(--bg-card, #ffffff);
}
.theme-dark .excel-hub-hero {
    background: linear-gradient(135deg, #022c22 0%, #064e3b 50%, #047857 100%) !important;
}
.theme-dark .templates-overview-card,
.theme-dark .template-box,
.theme-dark .import-guidelines-card,
.theme-dark .guideline-item {
    background: #111d33 !important;
    border-color: rgba(255, 255, 255, 0.08) !important;
}
.theme-dark .template-desc {
    color: #94a3b8 !important;
}
.theme-dark .template-fields-section {
    background: rgba(255, 255, 255, 0.03) !important;
    border-color: rgba(255, 255, 255, 0.06) !important;
}
.theme-dark .field-pill-opt {
    background: #1e293b;
    color: #cbd5e1;
    border-color: rgba(255, 255, 255, 0.1);
}
.theme-dark .field-pill-req {
    background: rgba(220, 38, 38, 0.15);
    color: #f87171;
    border-color: rgba(220, 38, 38, 0.3);
}
.theme-dark .btn-template-download {
    background: transparent;
    color: #34d399 !important;
    border-color: #059669;
}
.theme-dark .btn-template-download:hover {
    background: rgba(5, 150, 105, 0.15);
}
.theme-dark .stat-pill-float {
    background: #1e293b !important;
    color: #f8fafc !important;
}
.theme-dark .stat-pill-float .text-dark {
    color: #f8fafc !important;
}
</style>

{{-- Page Scripts --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick select section buttons
    const quickSelectBtns = document.querySelectorAll('.btn-quick-select-section');
    quickSelectBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const sec = this.getAttribute('data-section');
            const targetPill = document.querySelector(`.import-sec-pill[data-section="${sec}"]`);
            if (targetPill) {
                targetPill.click();
                const card = document.getElementById('importSectionCard');
                if (card) {
                    card.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            }
        });
    });

    // Scroll to templates
    const btnScrollToTemplates = document.getElementById('btnScrollToTemplates');
    if (btnScrollToTemplates) {
        btnScrollToTemplates.addEventListener('click', function() {
            const templatesCard = document.getElementById('sectionTemplatesContainer');
            if (templatesCard) {
                templatesCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }

    // Live search for template cards
    const templateSearchInput = document.getElementById('templateSearchInput');
    const templateCols = document.querySelectorAll('.template-card-col');
    const noTemplatesFoundAlert = document.getElementById('noTemplatesFoundAlert');

    if (templateSearchInput) {
        templateSearchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            let visibleCount = 0;

            templateCols.forEach(col => {
                const name = (col.getAttribute('data-name') || '').toLowerCase();
                const desc = (col.getAttribute('data-desc') || '').toLowerCase();
                const fields = (col.getAttribute('data-fields') || '').toLowerCase();

                if (!query || name.includes(query) || desc.includes(query) || fields.includes(query)) {
                    col.classList.remove('d-none');
                    visibleCount++;
                } else {
                    col.classList.add('d-none');
                }
            });

            if (noTemplatesFoundAlert) {
                if (visibleCount === 0) {
                    noTemplatesFoundAlert.classList.remove('d-none');
                } else {
                    noTemplatesFoundAlert.classList.add('d-none');
                }
            }
        });
    }
});
</script>
@endsection
