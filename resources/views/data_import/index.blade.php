@extends('layouts.app')

@section('title', 'مركز استيراد البيانات (Excel Hub) — منظومة إنسان')

@section('content')
<div class="container-fluid py-4 px-3 px-md-4">

    {{-- Breadcrumb & Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-decoration-none">الرئيسية</a></li>
                    <li class="breadcrumb-item active" aria-current="page">مركز استيراد البيانات</li>
                </ol>
            </nav>
            <h2 class="fw-bold mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-file-earmark-spreadsheet-fill text-success"></i>
                <span>مركز الاستيراد الذكي للبيانات (Excel & CSV Hub)</span>
            </h2>
            <p class="text-muted small mb-0">استيراد وتوزيع آلاف السجلات دفعة واحدة في قطاعات الحالات، التبرعات، المتبرعين، المستفيدين، والمهام</p>
        </div>

        <div class="d-flex align-items-center gap-2">
            <a href="{{ route('dashboard.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2 fw-bold small">
                <i class="bi bi-arrow-right me-1"></i> العودة للرئيسية
            </a>
        </div>
    </div>

    {{-- Embed Import Section --}}
    @include('dashboard.partials.data_import_section')

    {{-- Section Templates & Reference Cards --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-2">
            <h5 class="fw-bold mb-1"><i class="bi bi-collection-fill text-primary me-2"></i> نماذج وقوالب الأقسام المتاحة للاستيراد</h5>
            <p class="text-muted small mb-0">قم بتحميل النموذج المخصص لكل قسم لتعبئة البيانات بالأعمدة القياسية أو ارفع أي ملف وقم بمطابقة الأعمدة آلياً</p>
        </div>
        <div class="card-body p-4 pt-2">
            <div class="row g-3">
                @foreach($sections as $secKey => $secConfig)
                    <div class="col-md-6 col-lg-4">
                        <div class="p-3.5 rounded-3 border h-100 d-flex flex-column justify-content-between bg-light-subtle">
                            <div>
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div class="d-flex align-items-center gap-2">
                                        <div class="p-2 rounded-3 text-white d-flex align-items-center justify-content-center" style="background-color: {{ $secConfig['color'] }}; width: 36px; height: 36px;">
                                            <i class="bi {{ $secConfig['icon'] }}"></i>
                                        </div>
                                        <h6 class="fw-bold mb-0">{{ $secConfig['name'] }}</h6>
                                    </div>
                                    <span class="badge rounded-pill bg-light text-dark border x-small">{{ $secConfig['badge'] }}</span>
                                </div>
                                <p class="text-muted x-small mb-3">{{ $secConfig['description'] }}</p>

                                <div class="mb-3">
                                    <span class="text-dark fw-bold x-small d-block mb-1.5">الحقول المتاحة:</span>
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($secConfig['fields'] as $fKey => $fMeta)
                                            <span class="badge {{ $fMeta['required'] ? 'bg-danger-subtle text-danger border border-danger-subtle' : 'bg-secondary-subtle text-secondary' }} x-small" style="font-size: 0.7rem;">
                                                {{ $fMeta['label'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <div class="pt-2 border-top mt-2">
                                <a href="{{ route('data-import.template', $secKey) }}" class="btn btn-sm btn-outline-success rounded-pill w-100 fw-bold d-inline-flex align-items-center justify-content-center gap-1.5">
                                    <i class="bi bi-file-earmark-arrow-down"></i>
                                    <span>تحميل نموذج {{ $secConfig['name'] }}</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Import Guidelines Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-primary-subtle bg-opacity-25 border border-primary border-opacity-25">
        <div class="card-body p-4">
            <h5 class="fw-bold text-primary mb-3 d-flex align-items-center gap-2">
                <i class="bi bi-lightbulb-fill"></i>
                <span>إرشادات ونصائح للحصول على أفضل نتائج أثناء الاستيراد:</span>
            </h5>
            <div class="row g-3 small text-secondary">
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                        <div>
                            <strong class="text-dark">تنسيق الصف الأول:</strong>
                            <div>تأكد من أن الصف الأول في الملف يحتوي على أسماء وعناوين الأعمدة بوضوح ليتمكن النظام من التعرف عليها.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                        <div>
                            <strong class="text-dark">أرقام الهواتف والبطاقات:</strong>
                            <div>تأكد من عدم وجود مسافات أو رموز غريبة في حقول أرقام الهواتف والرقم القومي لضمان الحفظ الصحيح.</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="d-flex align-items-start gap-2">
                        <i class="bi bi-check-circle-fill text-success fs-6 mt-0.5"></i>
                        <div>
                            <strong class="text-dark">منع التكرار والحماية:</strong>
                            <div>يقوم النظام بفحص السجلات المكررة بالرقم القومي أو الهاتف وتخطيها تلقائياً لتفادي ازدواجية البيانات.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
