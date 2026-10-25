@extends('layouts.app')

@section('title', 'مركز استيراد وتوزيع البيانات (Excel & CSV Hub) — منظومة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    {{-- Executive Hero Banner --}}
    <div class="excel-hub-hero p-4 p-md-5 rounded-4 mb-4 text-white shadow-sm position-relative overflow-hidden">
        <div class="position-relative" style="z-index: 2;">
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0 bg-white bg-opacity-15 rounded-pill px-3 py-1.5 small border border-white border-opacity-20">
                        <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white text-decoration-none opacity-90"><i class="bi bi-house-door-fill me-1"></i> الرئيسية</a></li>
                        <li class="breadcrumb-item active text-white fw-bold" aria-current="page">مركز استيراد البيانات</li>
                    </ol>
                </nav>

                <a href="{{ route('dashboard.index') }}" class="btn btn-sm btn-light rounded-pill px-3.5 py-2 fw-bold text-success d-inline-flex align-items-center gap-1.5 shadow-sm hover-lift">
                    <i class="bi bi-arrow-right"></i>
                    <span>العودة للرئيسية</span>
                </a>
            </div>

            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-3 mb-2">
                        <div class="hero-excel-icon shadow-sm">
                            <i class="bi bi-file-earmark-spreadsheet-fill fs-2"></i>
                        </div>
                        <div>
                            <h2 class="fw-black mb-1 text-white" style="letter-spacing: -0.5px;">
                                مركز الاستيراد والتوزيع الذكي للبيانات
                            </h2>
                            <p class="mb-0 text-white-50 small fs-6">
                                معالجة وتوزيع ملفات Excel و CSV آلياً لحفظ آلاف السجلات دفعة واحدة في قطاعات الحالات، التبرعات، المتبرعين، المستفيدين، والمهام
                            </p>
                        </div>
                    </div>

                    {{-- Live Capability Badges --}}
                    <div class="d-flex flex-wrap align-items-center gap-2 mt-3 pt-1">
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small">
                            <i class="bi bi-magic text-warning"></i>
                            <span>مطابقة ذكية للأعمدة بالذكاء الاصطناعي</span>
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small">
                            <i class="bi bi-shield-check text-info"></i>
                            <span>فحص وتخطي التكرار تلقائياً</span>
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small">
                            <i class="bi bi-filetype-xlsx text-light"></i>
                            <span>يدعم .xlsx و .xls و .csv</span>
                        </span>
                        <span class="badge bg-white bg-opacity-20 text-white rounded-pill px-3 py-2 d-inline-flex align-items-center gap-1.5 border border-white border-opacity-25 small">
                            <i class="bi bi-eye-fill text-light"></i>
                            <span>معاينة حية وتدقيق قبل الاعتماد</span>
                        </span>
                    </div>
                </div>

                <div class="col-lg-4 text-lg-end d-none d-lg-block">
                    <div class="excel-hero-illustration">
                        <div class="stat-pill-float">
                            <i class="bi bi-check2-circle text-success fs-4"></i>
                            <div>
                                <div class="fw-bold small text-dark">توزيع تلقائي 100%</div>
                                <div class="text-muted x-small">مباشرة في قاعدة البيانات</div>
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
    <div class="card border-0 shadow-sm rounded-4 mb-4 templates-overview-card overflow-hidden">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h5 class="fw-bold mb-1 text-main d-flex align-items-center gap-2">
                    <span class="p-2 rounded-3 bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center">
                        <i class="bi bi-collection-fill fs-5"></i>
                    </span>
                    <span>نماذج وقوالب الأقسام المتاحة للاستيراد</span>
                </h5>
                <p class="text-muted small mb-0 mt-1">يمكنك تحميل النموذج الفارغ لكل قسم لتعبئة البيانات بالأعمدة القياسية، أو رفع أي ملف واستخدام المطابقة الذكية</p>
            </div>
            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-check2-all"></i>
                <span>{{ count($sections) }} أقسام مدعومة</span>
            </span>
        </div>
        <div class="card-body p-4 pt-3">
            <div class="row g-3 g-lg-4">
                @foreach($sections as $secKey => $secConfig)
                    <div class="col-md-6 col-lg-4">
                        <div class="template-box p-3.5 p-md-4 rounded-4 border h-100 d-flex flex-column justify-content-between position-relative" style="--sec-accent: {{ $secConfig['color'] }};">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-3 pb-2 border-bottom border-light-subtle">
                                    <div class="d-flex align-items-center gap-2.5">
                                        <div class="template-icon text-white shadow-xs" style="background-color: {{ $secConfig['color'] }};">
                                            <i class="bi {{ $secConfig['icon'] }}"></i>
                                        </div>
                                        <div>
                                            <h6 class="fw-bold mb-0 text-main">{{ $secConfig['name'] }}</h6>
                                            <span class="text-muted x-small">{{ $secConfig['badge'] }}</span>
                                        </div>
                                    </div>
                                    <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill x-small fw-bold d-inline-flex align-items-center gap-1">
                                        <i class="bi bi-columns-gap"></i>
                                        <span>{{ count($secConfig['fields']) }} حقول</span>
                                    </span>
                                </div>
                                
                                <p class="text-muted small mb-3 template-desc lh-base">{{ $secConfig['description'] }}</p>

                                <div class="mb-3">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <span class="text-dark fw-bold x-small d-inline-flex align-items-center gap-1">
                                            <i class="bi bi-tags-fill text-muted"></i>
                                            <span>الأعمدة والحقول:</span>
                                        </span>
                                        <span class="text-muted x-small opacity-75">(*) حقل مطلوب</span>
                                    </div>
                                    <div class="template-fields-wrap d-flex flex-wrap gap-1.5">
                                        @foreach($secConfig['fields'] as $fKey => $fMeta)
                                            <span class="template-field-pill {{ $fMeta['required'] ? 'field-pill-req' : 'field-pill-opt' }}" title="{{ $fMeta['required'] ? 'حقل إلزامي للاستيراد' : 'حقل اختياري' }}">
                                                @if($fMeta['required'])
                                                    <span class="field-req-dot"></span>
                                                @endif
                                                <span>{{ $fMeta['label'] }}</span>
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="template-actions pt-3 border-top mt-auto d-flex align-items-center gap-2">
                                <a href="{{ route('data-import.template', $secKey) }}" class="btn btn-sm btn-outline-success rounded-pill flex-grow-1 py-2 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5 hover-lift" title="تحميل قالب Excel جاهز بالأعمدة القياسية">
                                    <i class="bi bi-file-earmark-arrow-down-fill"></i>
                                    <span>تحميل النموذج</span>
                                </a>
                                <button type="button" class="btn btn-sm btn-light border rounded-pill px-3 py-2 fw-bold text-dark d-inline-flex align-items-center gap-1 btn-quick-select-section hover-lift" data-section="{{ $secKey }}" title="تحديد هذا القسم للاستيراد الآن">
                                    <i class="bi bi-arrow-up-circle-fill text-primary"></i>
                                    <span>استيراد</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Import Guidelines Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 import-guidelines-card border border-success border-opacity-25 overflow-hidden">
        <div class="card-body p-4">
            <h5 class="fw-bold text-success mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightbulb-fill"></i>
                <span>إرشادات ونصائح للحصول على أفضل نتائج أثناء استيراد البيانات:</span>
            </h5>
            <div class="row g-3 small text-secondary">
                <div class="col-md-4">
                    <div class="guideline-item p-3.5 rounded-3 h-100 bg-white shadow-2xs border">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">الصف الأول يحتوي العناوين:</strong>
                                <div>تأكد من أن الصف الأول في الملف يحتوي على أسماء وعناوين الأعمدة بوضوح ليتعرف عليها محرك المطابقة الذكي تلقائياً.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="guideline-item p-3.5 rounded-3 h-100 bg-white shadow-2xs border">
                        <div class="d-flex align-items-start gap-2.5">
                            <i class="bi bi-check-circle-fill text-success fs-5 mt-0.5"></i>
                            <div>
                                <strong class="text-dark d-block mb-1">أرقام الهواتف والأرقام القومية:</strong>
                                <div>تأكد من كتابة الأرقام بدون مسافات أو رموز خاصة لضمان حفظها ومطابقتها بشكل صحيح مع سجلات النظام.</div>
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
                                <div>يقوم النظام بفحص السجلات المكررة بالرقم القومي أو الهاتف وتخطيها تلقائياً لتفادي ازدواجية البيانات بقاعدة البيانات.</div>
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
.excel-hub-hero::before {
    content: '';
    position: absolute;
    top: -50px;
    left: -50px;
    width: 250px;
    height: 250px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0) 70%);
    border-radius: 50%;
    pointer-events: none;
}
.hero-excel-icon {
    width: 58px;
    height: 58px;
    border-radius: 16px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
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
}
.template-box {
    background: var(--bg-card, #ffffff);
    border-color: #e2e8f0 !important;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02);
}
.template-box:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.07);
    border-color: var(--sec-accent, #10b981) !important;
}
.template-icon {
    width: 42px;
    height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
    flex-shrink: 0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
}
.template-desc {
    min-height: 38px;
    font-size: 0.84rem;
}
.template-fields-wrap {
    min-height: 72px;
    align-content: flex-start;
}
.template-field-pill {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 3px 8px;
    border-radius: 8px;
    font-size: 0.72rem;
    font-weight: 600;
    line-height: 1.3;
    transition: all 0.15s ease;
}
.field-pill-opt {
    background: #f1f5f9;
    color: #475569;
    border: 1px solid #e2e8f0;
}
.field-pill-req {
    background: #fef2f2;
    color: #dc2626;
    border: 1px solid #fecaca;
}
.field-req-dot {
    width: 5px;
    height: 5px;
    border-radius: 50%;
    background: #dc2626;
    display: inline-block;
}
.template-field-pill:hover {
    transform: translateY(-1px);
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
.theme-dark .field-pill-opt {
    background: #1e293b;
    color: #94a3b8;
    border-color: rgba(255, 255, 255, 0.08);
}
.theme-dark .field-pill-req {
    background: rgba(220, 38, 38, 0.15);
    color: #f87171;
    border-color: rgba(220, 38, 38, 0.3);
}
.theme-dark .stat-pill-float {
    background: #1e293b !important;
    color: #f8fafc !important;
}
.theme-dark .stat-pill-float .text-dark {
    color: #f8fafc !important;
}
</style>

{{-- Quick Select Section Script --}}
<script>
document.addEventListener('DOMContentLoaded', function() {
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
});
</script>
@endsection
