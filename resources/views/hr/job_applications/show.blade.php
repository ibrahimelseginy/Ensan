@extends('layouts.app')

@section('title', 'طلب توظيف ' . $application->tracking_code)

@section('content')
@php $u = auth()->user(); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <div>
        <h3 class="fw-bold mb-1"><i class="bi bi-person-vcard text-primary me-2"></i> طلب توظيف: {{ $application->full_name }}</h3>
        <div class="text-muted small">
            كود المتابعة:
            <code class="px-2 py-1 bg-light rounded border fw-bold">{{ $application->tracking_code }}</code>
            <span class="mx-2">|</span>
            الحالة الحالية: <span class="badge bg-{{ $application->status_color }}">{{ $application->status_label }}</span>
        </div>
    </div>
    <div class="d-flex gap-2">
        @if($application->cv_path)
            <a href="{{ route('hr.job-applications.cv', $application) }}" class="btn btn-outline-primary fw-bold"><i class="bi bi-file-earmark-arrow-down me-1"></i> تحميل CV</a>
        @endif
        <a href="{{ route('hr.job-applications.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right me-1"></i> رجوع للطلبات</a>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle me-1"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">{{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
@endif
@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="row g-4">
    {{-- بيانات المتقدم --}}
    <div class="col-lg-7">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white fw-bold py-3"><i class="bi bi-person-lines-fill text-primary me-1"></i> بيانات المتقدم (كما أرسلها من الموقع)</div>
            <div class="card-body">
                @php
                    $rows = [
                        ['الاسم بالكامل', $application->full_name, 'bi-person'],
                        ['السن', $application->age ? $application->age . ' سنة' : '-', 'bi-calendar3'],
                        ['رقم الهاتف (واتساب)', $application->phone, 'bi-whatsapp'],
                        ['البريد الإلكتروني', $application->email ?: '-', 'bi-envelope'],
                        ['محل الإقامة', $application->residence ?: '-', 'bi-geo-alt'],
                        ['الوظيفة المتقدم عليها', $application->position, 'bi-briefcase'],
                        ['الكلية التي تخرج منها', $application->faculty ?: '-', 'bi-mortarboard'],
                        ['هل تطوع من قبل؟', $application->has_volunteered ? 'نعم' : 'لا', 'bi-heart'],
                    ];
                @endphp
                <table class="table table-borderless mb-0">
                    @foreach($rows as [$label, $value, $icon])
                        <tr class="border-bottom">
                            <th class="text-muted small fw-semibold py-2" style="width: 40%;"><i class="bi {{ $icon }} me-1"></i> {{ $label }}</th>
                            <td class="fw-bold py-2">
                                @if($label === 'رقم الهاتف (واتساب)' && $application->whatsapp_link)
                                    <a href="{{ $application->whatsapp_link }}" target="_blank" class="text-success text-decoration-none">{{ $value }} <i class="bi bi-box-arrow-up-left small"></i></a>
                                @elseif($label === 'البريد الإلكتروني' && $application->email)
                                    <a href="mailto:{{ $application->email }}" class="text-decoration-none">{{ $value }}</a>
                                @else
                                    {{ $value }}
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    @if($application->has_volunteered && $application->volunteer_details)
                        <tr>
                            <th class="text-muted small fw-semibold py-2"><i class="bi bi-pin-map me-1"></i> أماكن التطوع السابقة</th>
                            <td class="py-2" style="white-space: pre-line;">{{ $application->volunteer_details }}</td>
                        </tr>
                    @endif
                </table>

                <div class="mt-3 p-3 bg-light rounded-3 border d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <div class="fw-bold"><i class="bi bi-file-earmark-person text-danger me-1"></i> السيرة الذاتية (CV)</div>
                        <small class="text-muted">تاريخ التقديم: {{ $application->created_at?->format('Y-m-d h:i A') }}</small>
                    </div>
                    @if($application->cv_path)
                        <a href="{{ route('hr.job-applications.cv', $application) }}" class="btn btn-sm btn-danger fw-bold"><i class="bi bi-download me-1"></i> تحميل الملف</a>
                    @else
                        <span class="text-muted small">لم يتم رفع ملف</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- قرار الموارد البشرية --}}
    <div class="col-lg-5">
        <div class="card border-0 shadow-sm rounded-4 mb-3">
            <div class="card-header fw-bold py-3" style="background:#1e293b; color:#fff !important;"><i class="bi bi-clipboard-check me-1"></i> قرار الموارد البشرية</div>
            <div class="card-body">
                @if($u && $u->hasPermission(['job_applications.manage', 'manage_employees']))
                    <form method="POST" action="{{ route('hr.job-applications.status', $application) }}">
                        @csrf
                        <label class="form-label fw-bold small">الحالة</label>
                        <div class="d-grid gap-2 mb-3" style="grid-template-columns: 1fr 1fr;">
                            @foreach($statuses as $key => $label)
                                @php
                                    $color = match($key) { 'accepted' => 'success', 'rejected' => 'danger', 'interview' => 'warning', 'reviewing' => 'info', default => 'primary' };
                                @endphp
                                <input type="radio" class="btn-check job-status-radio" name="status" id="st_{{ $key }}" value="{{ $key }}" @checked(old('status', $application->status) === $key)>
                                <label class="btn btn-outline-{{ $color }} fw-bold btn-sm" for="st_{{ $key }}">{{ $label }}</label>
                            @endforeach
                        </div>

                        <div id="interviewFields" class="p-3 bg-warning-subtle rounded-3 border border-warning-subtle mb-3" style="display:none;">
                            <label class="form-label fw-bold small">موعد المقابلة</label>
                            <input type="datetime-local" name="interview_date" class="form-control mb-2"
                                   value="{{ old('interview_date', $application->interview_date?->format('Y-m-d\TH:i')) }}">
                            <label class="form-label fw-bold small">مكان المقابلة</label>
                            <input type="text" name="interview_location" class="form-control"
                                   value="{{ old('interview_location', $application->interview_location) }}" placeholder="مثال: مقر المؤسسة - كفر الشيخ">
                        </div>

                        <label class="form-label fw-bold small">رسالة تظهر للمتقدم عند متابعة طلبه <span class="text-muted fw-normal">(اختياري)</span></label>
                        <textarea name="applicant_message" rows="2" class="form-control mb-3" placeholder="مثال: نشكرك على اهتمامك، سيتم التواصل معك عبر الواتساب.">{{ old('applicant_message', $application->applicant_message) }}</textarea>

                        <label class="form-label fw-bold small">ملاحظات داخلية للموارد البشرية <span class="text-muted fw-normal">(لا تظهر للمتقدم)</span></label>
                        <textarea name="hr_notes" rows="3" class="form-control mb-3">{{ old('hr_notes', $application->hr_notes) }}</textarea>

                        <button class="btn btn-primary w-100 fw-bold"><i class="bi bi-check2-circle me-1"></i> حفظ القرار</button>
                    </form>
                @else
                    <div class="alert alert-light border mb-0">ليس لديك صلاحية تغيير حالة طلبات التوظيف.</div>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body small">
                <div class="mb-1"><span class="text-muted">آخر مراجعة:</span> <strong>{{ $application->reviewed_at?->format('Y-m-d h:i A') ?? 'لم تتم المراجعة بعد' }}</strong></div>
                <div><span class="text-muted">بواسطة:</span> <strong>{{ $application->reviewer?->name ?? '-' }}</strong></div>
                @if($u && $u->hasPermission('job_applications.delete'))
                    <form method="POST" action="{{ route('hr.job-applications.destroy', $application) }}" class="mt-3" onsubmit="return confirm('هل تريد حذف طلب التوظيف نهائياً؟');">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-outline-danger w-100"><i class="bi bi-trash3 me-1"></i> حذف الطلب</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const init = () => {
        const box = document.getElementById('interviewFields');
        const sync = () => {
            const checked = document.querySelector('.job-status-radio:checked');
            if (box) box.style.display = checked && checked.value === 'interview' ? '' : 'none';
        };
        document.querySelectorAll('.job-status-radio').forEach(r => r.addEventListener('change', sync));
        sync();
    };
    if (document.readyState === 'loading') document.addEventListener('DOMContentLoaded', init); else init();
})();
</script>
@endsection

