@extends('layouts.app')

@section('title', 'تقارير الدعم النفسي - بعثاء الأمل')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    {{-- Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #ec4899, #f43f5e);">
                    <i class="bi bi-heart-pulse-fill fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-slate-800">سجل متابعة الدعم النفسي الشهري</h3>
                    <p class="text-muted small mb-0">متابعة الاتصالات الشهرية والدعم المعنوي لمرضى الأورام وحالات بعثاء الأمل المكفولة</p>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('beneficiaries.index', ['needs' => 'بعثاء الأمل']) }}" class="btn btn-outline-primary d-inline-flex align-items-center gap-1.5">
                <i class="bi bi-people-fill"></i>
                <span>قائمة حالات بعثاء الأمل</span>
            </a>
            <a href="{{ route('psychological-support.index') }}" class="btn btn-light border d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-clockwise"></i>
                <span>تحديث</span>
            </a>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('psychological-support.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">الشهر المستهدف</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-calendar-month text-primary"></i></span>
                        <input type="month" name="month" class="form-control bg-light border-0 fw-bold" value="{{ $currentMonth }}" onchange="this.form.submit()">
                    </div>
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-bold text-muted">البحث عن حالة أو متطوع</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="اسم الحالة، الكود (B26...)، الهاتف، أو اسم المتطوع..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-md-2 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100 fw-bold"><i class="bi bi-filter me-1"></i> تصفية</button>
                    @if(request()->filled('search') || request('month') !== now()->format('Y-m'))
                        <a href="{{ route('psychological-support.index') }}" class="btn btn-light" title="إلغاء التصفية"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Reports Listing --}}
    @if($reports->count() > 0)
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom px-4 py-3 d-flex align-items-center justify-content-between">
                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-medical-fill text-danger"></i>
                    <span>تقارير شهر: <span class="badge bg-primary-subtle text-primary px-2.5 py-1">{{ $currentMonth }}</span></span>
                    <span class="badge bg-secondary-subtle text-secondary small">({{ $reports->total() }} تقرير)</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" style="min-width: 950px;">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4">المستفيد / الحالة</th>
                            <th>المتطوع المتواصل</th>
                            <th>الحالة الصحية والمرحلة</th>
                            <th>الحالة النفسية والتقييم</th>
                            <th>التغذية والعلاج</th>
                            <th>ملاحظات المتطوع ورأيه</th>
                            <th>تاريخ التوثيق</th>
                            <th class="pe-4 text-center">الملف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                            @php
                                $b = $report->beneficiary;
                                $badgeClass = match($report->psychological_state) {
                                    'ممتاز' => 'success',
                                    'كويس' => 'primary',
                                    'راضي' => 'info',
                                    'حزين' => 'warning',
                                    'يشعر بالضجر' => 'danger',
                                    default => 'secondary',
                                };
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    @php
                                        $patientName = $report->familyMember ? $report->familyMember->full_name : ($b ? $b->full_name : 'غير محدد');
                                        $patientCode = $report->familyMember ? $report->familyMember->code : ($b ? $b->code : '-');
                                        $patientPhone = $report->familyMember && $report->familyMember->phone ? $report->familyMember->phone : ($b ? $b->phone : null);
                                    @endphp
                                    <div class="fw-bold text-dark">{{ $patientName }}</div>
                                    <div class="small text-muted font-monospace">
                                        <span class="badge bg-light text-dark border">{{ $patientCode }}</span>
                                        @if($report->familyMember)
                                            <span class="badge bg-info-subtle text-info border border-info ms-1">{{ $report->familyMember->relationship_label }}</span>
                                            @if($b)
                                                <span class="text-muted ms-1" style="font-size: 0.75rem;">(أسرة: {{ $b->full_name }})</span>
                                            @endif
                                        @endif
                                        @if($patientPhone)
                                            <span class="ms-1">{{ $patientPhone }}</span>
                                        @endif
                                    </div>
                                    @if($report->familyMember && $report->familyMember->case_details)
                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">
                                            <i class="bi bi-activity text-danger me-0.5"></i>{{ $report->familyMember->case_details }}
                                        </div>
                                    @elseif($b && $b->tumor_location)
                                        <div class="text-muted extra-small" style="font-size: 0.75rem;">
                                            <i class="bi bi-activity text-danger me-0.5"></i>{{ $b->tumor_location }}
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark"><i class="bi bi-person me-1 text-primary"></i>{{ $report->volunteer_name ?: ($report->creator->name ?? 'غير محدد') }}</div>
                                </td>
                                <td>
                                    @if($report->medical_condition)
                                        <div class="small text-dark fw-semibold">{{ $report->medical_condition }}</div>
                                    @endif
                                    @if($report->treatment_stage)
                                        <div class="small text-muted"><span class="badge bg-light text-muted border">{{ $report->treatment_stage }}</span></div>
                                    @endif
                                    @if(!$report->medical_condition && !$report->treatment_stage)
                                        <span class="text-muted small">-</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-2 mb-1">
                                        <span class="badge bg-{{ $badgeClass }}-subtle text-{{ $badgeClass }} px-2 py-1 rounded-pill">
                                            {{ $report->psychological_state ?: 'غير محدد' }}
                                        </span>
                                    </div>
                                    <div class="text-warning small font-monospace">
                                        {{ str_repeat('★', $report->rating) . str_repeat('☆', 5 - $report->rating) }}
                                        <span class="text-muted small ms-1">({{ $report->rating }}/5)</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-dark" style="max-width: 200px;">
                                        {{ Str::limit($report->nutrition_diet ?: ($report->needs_improvement ?: 'مستقرة'), 80) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted" style="max-width: 250px;">
                                        @if($report->supporter_opinion)
                                            <div class="fw-medium text-dark">{{ Str::limit($report->supporter_opinion, 70) }}</div>
                                        @endif
                                        @if($report->general_notes)
                                            <div class="extra-small text-muted">{{ Str::limit($report->general_notes, 70) }}</div>
                                        @endif
                                        @if(!$report->supporter_opinion && !$report->general_notes)
                                            <span>-</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="small text-muted">{{ $report->created_at ? $report->created_at->format('Y-m-d') : '-' }}</div>
                                    <div class="extra-small text-muted">{{ $report->created_at ? $report->created_at->format('h:i A') : '' }}</div>
                                </td>
                                <td class="pe-4 text-center">
                                    @php
                                        $shareUrl = $report->familyMember
                                            ? \App\Http\Controllers\PsychologicalSupportController::getPublicUrl('member', $report->familyMember->id)
                                            : ($b ? \App\Http\Controllers\PsychologicalSupportController::getPublicUrl('beneficiary', $b->id) : null);
                                    @endphp
                                    <div class="d-inline-flex gap-1">
                                        @if($report->familyMember)
                                            <a href="{{ route('beneficiary-family-members.show', $report->familyMember) }}" class="btn btn-sm btn-outline-danger rounded-pill px-3" title="عرض ملف الحالة المريضة">
                                                <i class="bi bi-heart-pulse-fill me-1"></i> ملف الحالة
                                            </a>
                                        @elseif($b)
                                            <a href="{{ route('beneficiaries.show', $b) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3" title="عرض ملف الحالة">
                                                <i class="bi bi-folder-fill me-1"></i> الملف
                                            </a>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white border-top p-3 d-flex justify-content-center">
                {{ $reports->links() }}
            </div>
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <div class="mb-3 text-muted">
                <i class="bi bi-heartbreak fs-1 text-secondary"></i>
            </div>
            <h5 class="fw-bold">لا توجد تقارير دعم نفسي مسجلة لشهر [{{ $currentMonth }}]</h5>
            <p class="text-muted small">يمكنك تسجيل تقرير دعم نفسي مباشرة من بطاقة أي حالة مكفولة في صفحة المستفيدين.</p>
            <div>
                <a href="{{ route('beneficiaries.index') }}" class="btn btn-primary">
                    <i class="bi bi-person-heart me-1"></i> الذهاب لملفات الحالات والمستفيدين
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
