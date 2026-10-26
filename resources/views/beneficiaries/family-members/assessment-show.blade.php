@extends('layouts.app')

@section('content')
<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    {{-- Header with Navigation & Print Button --}}
    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-1 small">
                    <li class="breadcrumb-item"><a href="{{ route('projects.index') }}" class="text-decoration-none text-muted">المشاريع</a></li>
                    <li class="breadcrumb-item active text-primary fw-bold" aria-current="page">استمارة تقييم ومتابعة الطفل المكفول</li>
                </ol>
            </nav>
            <h3 class="fw-800 text-dark mb-0">
                <i class="bi bi-file-earmark-check-fill text-primary me-2"></i>
                استمارة تقييم: <span class="text-primary">{{ $assessment->child?->full_name ?? $assessment->child?->name }}</span>
            </h3>
        </div>
        <div class="d-flex align-items-center gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-4 fw-bold shadow-xs" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> طباعة الاستمارة
            </button>
            <a href="javascript:history.back()" class="btn btn-light rounded-pill px-4 fw-bold border">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    {{-- Printable Assessment Dossier --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden printable-assessment-card bg-white mx-auto" style="max-width: 960px;">
        {{-- Official Document Header --}}
        <div class="p-4 border-bottom bg-light bg-opacity-50">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="d-flex align-items-center gap-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="bi bi-heart-pulse-fill fs-3"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-1 text-dark">مؤسسة إنسان الخيرية — مشروع زاد (كفالة ورعاية الأيتام)</h5>
                        <p class="text-muted small mb-0">
                            {{ $assessment->assessment_type === \App\Models\ChildAssessment::TYPE_ENTRY ? 'استمارة التقييم الشامل للالتحاق بالمشروع (نموذج معتمد ومقفل)' : 'تقرير المتابعة والتقييم الشهري للأطفال المكفولين' }}
                        </p>
                    </div>
                </div>
                <div class="text-end">
                    <span class="badge {{ $assessment->assessment_type === \App\Models\ChildAssessment::TYPE_ENTRY ? 'bg-primary' : 'bg-info' }} rounded-pill px-3 py-2 fs-7 fw-bold">
                        {{ $assessment->assessment_type === \App\Models\ChildAssessment::TYPE_ENTRY ? 'تقييم دخول شامل' : 'تقييم شهري (' . $assessment->assessment_month . ' / ' . $assessment->assessment_year . ')' }}
                    </span>
                    <div class="text-muted x-small mt-1 font-monospace">تاريخ التحرير: {{ $assessment->created_at->format('Y-m-d') }}</div>
                </div>
            </div>
        </div>

        <div class="card-body p-4 p-md-5">
            {{-- Child & Family Summary --}}
            <div class="row g-4 mb-4 pb-4 border-bottom">
                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-person-badge me-1"></i> بيانات الطفل والأسرة:</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-0" style="width: 140px;">اسم الطفل:</td>
                            <td class="fw-bold text-dark">{{ $assessment->child?->full_name ?? $assessment->child?->name ?? 'غير محدد' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">صلة القرابة:</td>
                            <td class="fw-bold text-dark">{{ $assessment->child?->relationship ?? 'ابن / ابنة' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">تاريخ الميلاد / السن:</td>
                            <td class="fw-bold text-dark">
                                {{ $assessment->child?->birth_date ? $assessment->child->birth_date->format('Y-m-d') . ' (' . $assessment->child->birth_date->age . ' سنة)' : 'غير محدد' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">الرقم القومي:</td>
                            <td class="fw-bold font-monospace text-dark">{{ $assessment->child?->national_id ?? 'غير مسجل' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">اسم الأم / الأسرة:</td>
                            <td class="fw-bold text-dark">
                                @if($assessment->beneficiary)
                                    <a href="{{ route('beneficiaries.show', $assessment->beneficiary_id) }}" class="text-decoration-none text-primary">
                                        {{ $assessment->beneficiary->name }} (#{{ $assessment->beneficiary->code }})
                                    </a>
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-md-6">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-award me-1"></i> بيانات الكافل والتقييم:</h6>
                    <table class="table table-sm table-borderless mb-0">
                        <tr>
                            <td class="text-muted ps-0" style="width: 140px;">الكافل المباشر:</td>
                            <td class="fw-bold text-dark">
                                @php $sponsors = $assessment->child?->sponsors; @endphp
                                @if($sponsors && $sponsors->count() > 0)
                                    @foreach($sponsors as $sp)
                                        <span class="badge bg-light text-dark border px-2 py-1 me-1">{{ $sp->name }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">في انتظار كافل</span>
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">القائم بالتقييم:</td>
                            <td class="fw-bold text-dark">{{ $assessment->evaluator_name }} ({{ $assessment->evaluator_role }})</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">مسجل التقييم:</td>
                            <td class="fw-bold text-dark">{{ $assessment->creator?->name ?? 'النظام' }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted ps-0">حالة النموذج:</td>
                            <td>
                                @if($assessment->is_locked)
                                    <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 rounded-pill px-2.5 py-1">
                                        <i class="bi bi-lock-fill me-1"></i> معتمد ومغلق نهائياً
                                    </span>
                                @else
                                    <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2.5 py-1">
                                        <i class="bi bi-check-circle me-1"></i> نشط
                                    </span>
                                @endif
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Performance Metrics & Scores --}}
            <div class="mb-4">
                <h6 class="fw-bold text-primary mb-3"><i class="bi bi-graph-up-arrow me-1"></i> درجات ونتائج التقييم:</h6>
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-4 border text-center">
                            <div class="text-muted x-small mb-1 fw-bold">النسبة الإجمالية العامة</div>
                            <div class="display-6 fw-800 {{ ($assessment->overall_percentage ?? 0) >= 80 ? 'text-success' : (($assessment->overall_percentage ?? 0) >= 60 ? 'text-primary' : 'text-danger') }}">
                                {{ $assessment->overall_percentage !== null ? $assessment->overall_percentage . '%' : '—' }}
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-4 border text-center">
                            <div class="text-muted x-small mb-1 fw-bold">درجة اختبار القرآن الكريم</div>
                            <div class="h3 fw-bold text-dark mb-0">
                                {{ $assessment->quran_grade_percentage !== null ? $assessment->quran_grade_percentage . '%' : '—' }}
                            </div>
                            <div class="text-muted x-small mt-1">حفظ: {{ $assessment->quran_memorized_parts ?: 'غير محدد' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-4 border text-center">
                            <div class="text-muted x-small mb-1 fw-bold">درجة الحديث والأذكار</div>
                            <div class="h3 fw-bold text-dark mb-0">
                                {{ $assessment->hadith_grade_percentage !== null ? $assessment->hadith_grade_percentage . '%' : '—' }}
                            </div>
                            <div class="text-muted x-small mt-1">المستوى: {{ $assessment->prayer_commitment_level ?: '—' }}</div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="p-3 bg-light rounded-4 border text-center">
                            <div class="text-muted x-small mb-1 fw-bold">السلوك والآداب العامة</div>
                            <div class="h3 fw-bold text-dark mb-0">
                                {{ $assessment->morals_grade_percentage !== null ? $assessment->morals_grade_percentage . '%' : '—' }}
                            </div>
                            <div class="text-muted x-small mt-1">التزام الصلاة: {{ $assessment->prayer_commitment_level ?: '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Qualitative Observations --}}
            <div class="row g-3 mb-4">
                @if($assessment->strengths)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-success bg-opacity-10 border border-success border-opacity-25 h-100">
                            <h6 class="fw-bold text-success mb-2"><i class="bi bi-star-fill me-1"></i> نقاط القوة والتميز:</h6>
                            <p class="mb-0 small text-dark lh-base">{{ $assessment->strengths }}</p>
                        </div>
                    </div>
                @endif
                @if($assessment->weaknesses)
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-warning bg-opacity-10 border border-warning border-opacity-25 h-100">
                            <h6 class="fw-bold text-warning mb-2"><i class="bi bi-exclamation-triangle-fill me-1"></i> نقاط بحاجة لتحسين ومتابعة:</h6>
                            <p class="mb-0 small text-dark lh-base">{{ $assessment->weaknesses }}</p>
                        </div>
                    </div>
                @endif
                @if($assessment->recommendations)
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <h6 class="fw-bold text-info-emphasis mb-2"><i class="bi bi-lightbulb-fill me-1"></i> توصيات وتوجيهات الكفالة:</h6>
                            <p class="mb-0 small text-dark lh-base">{{ $assessment->recommendations }}</p>
                        </div>
                    </div>
                @endif
                @if($assessment->notes)
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-light border">
                            <h6 class="fw-bold text-muted mb-2"><i class="bi bi-chat-left-text me-1"></i> ملاحظات إضافية:</h6>
                            <p class="mb-0 small text-dark lh-base">{{ $assessment->notes }}</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Attached Test Paper Photo --}}
            @if($assessment->test_image_url)
                <div class="mb-4 pb-3 border-bottom">
                    <h6 class="fw-bold text-primary mb-3"><i class="bi bi-image me-1"></i> صورة ورقة الاختبار المرفقة:</h6>
                    <div class="text-center p-3 bg-light rounded-4 border">
                        <a href="{{ $assessment->test_image_url }}" target="_blank">
                            <img src="{{ $assessment->test_image_url }}" alt="ورقة الاختبار" class="img-fluid rounded-3 shadow-xs" style="max-height: 500px;">
                        </a>
                        <div class="small text-muted mt-2">اضغط على الصورة لفتحها بالحجم الكامل</div>
                    </div>
                </div>
            @endif

            {{-- Official Signatures Area for Printing --}}
            <div class="row pt-4 mt-4 border-top text-center print-signatures">
                <div class="col-4">
                    <div class="text-muted small fw-bold mb-4">أخصائي / معلم التقييم</div>
                    <div class="fw-bold text-dark">{{ $assessment->evaluator_name }}</div>
                    <div class="text-muted x-small">التوقيع: ..........................</div>
                </div>
                <div class="col-4">
                    <div class="text-muted small fw-bold mb-4">مدير مشروع زاد</div>
                    <div class="fw-bold text-dark">إدارة كفالة الأيتام</div>
                    <div class="text-muted x-small">التوقيع: ..........................</div>
                </div>
                <div class="col-4">
                    <div class="text-muted small fw-bold mb-4">خاتم واعتماد المؤسسة</div>
                    <div class="border border-dashed rounded-3 mx-auto d-flex align-items-center justify-content-center text-muted x-small" style="width: 100px; height: 60px;">
                        خاتم إنسان
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    body { background: #ffffff !important; }
    .btn, nav, .navbar, .sidebar { display: none !important; }
    .printable-assessment-card { border: none !important; box-shadow: none !important; max-width: 100% !important; }
    @page { size: A4 portrait; margin: 10mm; }
}
</style>
@endsection
