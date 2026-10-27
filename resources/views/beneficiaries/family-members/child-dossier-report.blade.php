@extends('layouts.app')

@section('content')
<div class="container py-4" dir="rtl">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 no-print">
        <div class="d-flex align-items-center gap-2">
            <a href="{{ url()->previous() }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
            <h4 class="mb-0 fw-bold text-dark">
                <i class="bi bi-file-earmark-person-fill text-primary me-2"></i> ملف وتقرير متابعة الطفل المكفول
            </h4>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            @php
                $sponsor = $familyMember->sponsors->first();
                $spPhone = preg_replace('/[^0-9]/', '', $sponsor?->phone ?? '');
                if (str_starts_with($spPhone, '01')) { $spPhone = '2' . $spPhone; }

                $latestEval = $familyMember->latestMonthlyAssessment;
                $academicScore = $latestEval ? $latestEval->academic_rating . '%' : '—';
                $psychoScore = $latestEval ? $latestEval->psychological_rating . '%' : '—';
                $overallScore = $latestEval ? $latestEval->score_percentage . '%' : ($familyMember->entryAssessment ? $familyMember->entryAssessment->score_percentage . '%' : 'قيد المتابعة');
                $evalDate = $latestEval ? $latestEval->assessment_date?->format('Y-m-d') : ($familyMember->entryAssessment ? $familyMember->entryAssessment->assessment_date?->format('Y-m-d') : 'حديث');

                $waText = "السلام عليكم ورحمة الله وبركاته،\n" .
                          "أهلاً بك كافل الخير أ/ " . ($sponsor?->name ?? 'الكريم') . "،\n" .
                          "يسر مؤسسة إنسان للأعمال الخيرية (مشروع زاد لرعاية وتأهيل الأيتام) أن توافيكم بملف وتقرير المتابعة الشهري للطفل المكفول برعايتكم الكريمة:\n\n" .
                          "👤 اسم الطفل: " . $familyMember->full_name . " (كود: " . $familyMember->code . ")\n" .
                          "📅 تاريخ التقييم: " . $evalDate . "\n" .
                          "📚 التقييم الدراسي: " . $academicScore . "\n" .
                          "🧠 التقييم النفسي والاجتماعي: " . $psychoScore . "\n" .
                          "⭐ النتيجة الإجمالية: " . $overallScore . "\n\n" .
                          "🔗 رابط معاينة ملف الطفل المحدث وتقرير التقييم:\n" .
                          route('child-assessments.report', $familyMember) . "\n\n" .
                          "جزاكم الله خير الجزاء وتقبل منكم صالح الأعمال ودعمكم المستمر.";
            @endphp

            @if($sponsor && !empty($spPhone))
                <a href="https://wa.me/{{ $spPhone }}?text={{ urlencode($waText) }}" target="_blank" class="btn btn-success fw-bold rounded-pill px-3 shadow-sm">
                    <i class="bi bi-whatsapp me-1 fs-5"></i> إرسال للكافل عبر الواتساب ({{ $sponsor->name }})
                </a>
            @endif

            <button type="button" onclick="window.print()" class="btn btn-primary rounded-pill px-3 shadow-sm">
                <i class="bi bi-printer me-1"></i> طباعة الملف
            </button>
        </div>
    </div>

    {{-- Main Printable Child Dossier Card --}}
    <div class="card border-0 shadow rounded-4 overflow-hidden position-relative bg-white" style="border: 2px solid #e2e8f0;">
        {{-- Cheerful Child Header Banner --}}
        <div class="p-4 text-white position-relative" style="background: linear-gradient(135deg, #0284c7 0%, #0d9488 50%, #f59e0b 100%);">
            <div class="position-absolute opacity-10 end-0 top-0 p-3 fs-1 select-none pointer-events-none" style="font-size: 8rem !important; line-height: 1;">
                🎈
            </div>
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative z-1">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle bg-white text-primary p-3 shadow d-flex align-items-center justify-content-center" style="width: 80px; height: 80px; font-size: 2.2rem;">
                        👦
                    </div>
                    <div>
                        <div class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-1">
                            مشروع زاد لرعاية وتأهيل الأيتام المكفولين
                        </div>
                        <h2 class="fw-bold mb-1 text-white">{{ $familyMember->full_name }}</h2>
                        <div class="text-white-50 font-monospace">
                            كود الطفل: <span class="text-white fw-bold">{{ $familyMember->code }}</span>
                            @if($familyMember->education_level)
                                · المرحلة الدراسية: <span class="text-white">{{ $familyMember->education_level }}</span>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="text-start bg-white bg-opacity-15 p-3 rounded-3 border border-white border-opacity-25">
                    <div class="small text-white-50">الكافل المسؤول</div>
                    <div class="fs-5 fw-bold text-white">{{ $sponsor?->name ?? 'غير مكفول بعد' }}</div>
                    @if($sponsor)
                        <div class="small text-warning font-monospace">{{ $sponsor->phone }}</div>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            {{-- General Child Info Grid --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block">تاريخ الميلاد والعمر</span>
                        <strong class="fs-6 text-dark">
                            {{ $familyMember->birth_date?->format('Y-m-d') ?? '—' }}
                            ({{ $familyMember->birth_date ? $familyMember->birth_date->age . ' سنة' : ($familyMember->age ? $familyMember->age . ' سنة' : '—') }})
                        </strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block">ولي الأمر / الأسرة</span>
                        <strong class="fs-6 text-dark">{{ $familyMember->beneficiary->mother_name ?: $familyMember->beneficiary->full_name }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block">كود الأسرة في النظام</span>
                        <strong class="fs-6 font-monospace text-primary">{{ $familyMember->beneficiary->code }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded-3 border">
                        <span class="text-muted small d-block">مبلغ الكفالة الشهري</span>
                        <strong class="fs-6 text-success">{{ $familyMember->sponsorship_amount ? number_format((float)$familyMember->sponsorship_amount) . ' ج.م' : '—' }}</strong>
                    </div>
                </div>
            </div>

            {{-- Latest Monthly Evaluation Card --}}
            <div class="card mb-4 border-0 shadow-sm" style="background: #f8fafc; border: 1px solid #cbd5e1 !important;">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 bg-primary-subtle text-primary rounded-3">
                            <i class="bi bi-graph-up-arrow fs-5"></i>
                        </span>
                        <div>
                            <h5 class="mb-0 fw-bold">تقرير التقييم الشهري الأحدث</h5>
                            <small class="text-muted">متابعة دورية لقياس التحصيل الدراسي والنمو النفسي والسلوكي للطفل</small>
                        </div>
                    </div>
                    @if($latestEval)
                        <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill font-monospace">
                            {{ $latestEval->assessment_date?->format('Y-m-d') }}
                        </span>
                    @endif
                </div>
                <div class="card-body p-4">
                    @if($latestEval)
                        <div class="row g-3 mb-3">
                            <div class="col-md-4 text-center">
                                <div class="p-3 bg-white rounded-3 border shadow-2xs h-100">
                                    <div class="text-muted small mb-1">التقييم الدراسي</div>
                                    <div class="fs-2 fw-bold text-primary font-monospace">{{ $latestEval->academic_rating ?? '—' }}%</div>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-primary" role="progressbar" style="width: {{ $latestEval->academic_rating ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="p-3 bg-white rounded-3 border shadow-2xs h-100">
                                    <div class="text-muted small mb-1">التقييم النفسي والاجتماعي</div>
                                    <div class="fs-2 fw-bold text-success font-monospace">{{ $latestEval->psychological_rating ?? '—' }}%</div>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $latestEval->psychological_rating ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4 text-center">
                                <div class="p-3 bg-white rounded-3 border shadow-2xs h-100">
                                    <div class="text-muted small mb-1">النتيجة الإجمالية العامة</div>
                                    <div class="fs-2 fw-bold text-warning font-monospace">{{ $latestEval->score_percentage ?? '—' }}%</div>
                                    <div class="progress mt-2" style="height: 6px;">
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $latestEval->score_percentage ?? 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-person-check-fill text-primary me-1"></i> القائم بالاختبار والتقييم:</strong>
                                    <div class="text-secondary">
                                        {{ $latestEval->reviewer_name }}
                                        <span class="badge {{ $latestEval->reviewer_role === 'volunteer' ? 'bg-info-subtle text-info border border-info-subtle' : 'bg-primary-subtle text-primary border border-primary-subtle' }} ms-1">
                                            {{ $latestEval->reviewer_role_label }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-white rounded-3 border h-100">
                                    <strong class="text-dark d-block mb-1"><i class="bi bi-chat-left-quote-fill text-muted me-1"></i> ملاحظات المشرف وتوصياته:</strong>
                                    <div class="text-secondary small">
                                        {{ $latestEval->notes ?: 'لا توجد ملاحظات إضافية.' }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        @if($latestEval->test_image_url)
                            <div class="p-3 bg-white rounded-3 border text-center mt-3">
                                <div class="small fw-bold text-muted mb-2"><i class="bi bi-paperclip me-1"></i> صورة ورقة الاختبار / نموذج التقييم المرفق:</div>
                                <a href="{{ $latestEval->test_image_url }}" target="_blank">
                                    <img src="{{ $latestEval->test_image_url }}" alt="صورة الاختبار" class="img-fluid rounded border shadow-sm" style="max-height: 380px;">
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-clock-history fs-2 d-block mb-2 text-warning"></i>
                            لم يتم تسجيل تقييم شهري لهذا الطفل بعد، سيتم تحديث التقرير فور إتمام التقييم.
                        </div>
                    @endif
                </div>
            </div>

            {{-- Entry Assessment Summary (Locked) --}}
            @if($familyMember->entryAssessment)
                @php $entry = $familyMember->entryAssessment; @endphp
                <div class="card mb-4 border-0 shadow-sm" style="background: #fffbeb; border: 1px solid #fde68a !important;">
                    <div class="card-header bg-white bg-opacity-75 border-bottom py-2.5 d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-success text-white px-2 py-1"><i class="bi bi-shield-lock-fill me-1"></i> معتمد ومغلق</span>
                            <strong class="text-dark">استمارة التقييم الشامل عند دخول المشروع</strong>
                        </div>
                        <div class="text-muted small font-monospace">
                            تاريخ الدخول: {{ $entry->assessment_date?->format('Y-m-d') }}
                        </div>
                    </div>
                    <div class="card-body p-3">
                        <div class="row g-2 text-center mb-2">
                            <div class="col-md-3 col-6">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted">التقييم القرآني/الدراسي</div>
                                    <strong class="text-primary fs-5">{{ $entry->academic_rating ?? $entry->score_percentage ?? '—' }}%</strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted">التقييم النفسي والاجتماعي</div>
                                    <strong class="text-success fs-5">{{ $entry->psychological_rating ?? '—' }}%</strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted">الوضع الصحي والبدني</div>
                                    <strong class="text-info fs-5">{{ $entry->health_rating ?? '—' }}%</strong>
                                </div>
                            </div>
                            <div class="col-md-3 col-6">
                                <div class="p-2 bg-white rounded border">
                                    <div class="small text-muted">النتيجة الإجمالية</div>
                                    <strong class="text-warning fs-5">{{ $entry->score_percentage ?? '—' }}%</strong>
                                </div>
                            </div>
                        </div>
                        @if($entry->test_image_url)
                            <div class="text-center mt-2">
                                <a href="{{ $entry->test_image_url }}" target="_blank" class="btn btn-sm btn-outline-warning text-dark rounded-pill">
                                    <i class="bi bi-file-earmark-image me-1"></i> استعراض استمارة الدخول المرفقة
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Child Attached Documents (ملف الطفل المرفق) --}}
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-folder-fill text-warning me-2"></i> المستندات والملفات المرفقة بملف الطفل</h5>
                    <span class="badge bg-secondary-subtle text-secondary font-monospace">{{ $familyMember->attachments->count() }} ملف</span>
                </div>
                <div class="card-body p-3">
                    @if($familyMember->attachments->isNotEmpty())
                        <div class="row g-2">
                            @foreach($familyMember->attachments as $att)
                                <div class="col-md-4">
                                    <div class="p-2.5 bg-light rounded-3 border d-flex align-items-center justify-content-between gap-2">
                                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-4"></i>
                                            <div class="text-truncate">
                                                <div class="fw-bold small text-truncate">{{ $att->original_name ?: 'مستند مرفق' }}</div>
                                                <div class="text-muted font-monospace" style="font-size: 0.72rem;">{{ $att->created_at?->format('Y-m-d') }}</div>
                                            </div>
                                        </div>
                                        <a href="{{ $att->view_url }}" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-2.5 py-1 small">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-muted small text-center py-2">لا توجد ملفات إضافية مرفقة بملف هذا الطفل حالياً.</div>
                    @endif
                </div>
            </div>

            {{-- Evaluation History Table --}}
            @if($familyMember->assessments->count() > 1)
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="mb-0 fw-bold"><i class="bi bi-clock-history text-secondary me-2"></i> سجل التقييمات الشهرية السابقة</h6>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-sm table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>تاريخ التقييم</th>
                                    <th>النوع</th>
                                    <th>الدراسي</th>
                                    <th>النفسي والاجتماعي</th>
                                    <th>الدرجة الإجمالية</th>
                                    <th>القائم بالاختبار</th>
                                    <th>صورة الاختبار</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($familyMember->assessments as $item)
                                    <tr>
                                        <td class="font-monospace fw-semibold">{{ $item->assessment_date?->format('Y-m-d') }}</td>
                                        <td>
                                            <span class="badge {{ $item->type === 'entry' ? 'bg-warning text-dark' : 'bg-primary' }}">
                                                {{ $item->type_label }}
                                            </span>
                                        </td>
                                        <td class="text-primary fw-bold">{{ $item->academic_rating ?? '—' }}%</td>
                                        <td class="text-success fw-bold">{{ $item->psychological_rating ?? '—' }}%</td>
                                        <td class="text-dark fw-bold font-monospace">{{ $item->score_percentage ?? '—' }}%</td>
                                        <td>{{ $item->reviewer_name }} ({{ $item->reviewer_role_label }})</td>
                                        <td>
                                            @if($item->test_image_url)
                                                <a href="{{ $item->test_image_url }}" target="_blank" class="badge bg-light text-primary border">
                                                    <i class="bi bi-image me-1"></i> معاينة
                                                </a>
                                            @else
                                                <span class="text-muted">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
@media print {
    .navbar, .sidebar-fixed, .no-print, header, footer, .btn {
        display: none !important;
    }
    body {
        padding: 0 !important;
        background: #fff !important;
    }
    .container {
        max-width: 100% !important;
        width: 100% !important;
        padding: 0 !important;
    }
    .card {
        box-shadow: none !important;
        border: 1px solid #ccc !important;
        page-break-inside: avoid;
    }
}
</style>
@endsection
