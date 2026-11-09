@extends('layouts.app')
@section('content')

    @php
        $sponsored = $donor->sponsoredBeneficiaries;
        $familyMembers = $donor->sponsoredFamilyMembers;

        // حساب قيمة الكفالة الشهرية الفعلية
        $effectiveMonthlyAmount = 0.0;
        if ((float) $donor->sponsorship_monthly_amount > 0) {
            $effectiveMonthlyAmount = (float) $donor->sponsorship_monthly_amount;
        } else {
            // محاولة جلب القيمة من آخر تبرع نقدي نشط
            $latestCashDonation = $history->first(fn($d) => $d->type === 'cash' && $d->status !== 'cancelled');
            if ($latestCashDonation && (float)$latestCashDonation->amount > 0) {
                $effectiveMonthlyAmount = (float) $latestCashDonation->amount;
            } elseif ($sponsored->isNotEmpty() && (float) ($sponsored->first()->monthly_sponsorship_amount ?? 0) > 0) {
                $effectiveMonthlyAmount = (float) $sponsored->first()->monthly_sponsorship_amount;
            }
        }

        $hasSponsorshipOrRecurring = ($donor->classification === 'recurring')
            || ((float) $donor->sponsorship_monthly_amount > 0)
            || ($effectiveMonthlyAmount > 0)
            || $sponsored->isNotEmpty()
            || $familyMembers->isNotEmpty()
            || ($donor->allocation_type === 'sponsorship');
    @endphp

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div class="d-flex gap-3 align-items-center">
            <div class="avatar avatar-lg avatar-primary">
                {{ mb_substr($donor->name, 0, 1) }}
            </div>
            <div>
                <h4 class="mb-1">{{ $donor->name }}</h4>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace fw-bold px-2.5 py-1">
                        {{ $donor->display_code }}
                    </span>
                    <span
                        class="badge bg-secondary-subtle text-body border">{{ $donor->type === 'individual' ? 'فرد' : 'منظمة' }}</span>
                    <span
                        class="badge {{ $donor->active ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                        {{ $donor->active ? 'نشط' : 'غير نشط' }}
                    </span>
                    @if($history->isEmpty())
                        <span class="badge bg-warning text-dark border border-warning px-2.5 py-1 fw-bold">
                            <i class="bi bi-hourglass-split me-1"></i> لم يتبرع بعد (مضاف جديد)
                        </span>
                    @endif
                    @if($effectiveMonthlyAmount > 0)
                        <span class="badge bg-success text-white border border-success px-2.5 py-1 fw-bold shadow-sm">
                            <i class="bi bi-heart-pulse-fill me-1"></i>كفالة: {{ number_format($effectiveMonthlyAmount, 2) }} ج.م شهرياً
                        </span>
                    @endif
                    @if($donor->phone)
                        <span class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $donor->phone }}</span>
                    @endif
                </div>
            </div>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-outline-info" data-bs-toggle="modal" data-bs-target="#printPreviewModal" onclick="window.showModalElement ? window.showModalElement(document.getElementById('printPreviewModal')) : null" title="معاينة التقرير ومحتواه قبل إرساله للطباعة">
                <i class="bi bi-eye me-1"></i> معاينة قبل الطباعة
            </button>
            <button onclick="printDonorReport()" class="btn btn-outline-primary" title="طباعة فورية">
                <i class="bi bi-printer me-1"></i> طباعة
            </button>
            <a href="{{ route('donations.create', ['donor_id' => $donor->id]) }}" class="btn btn-success">
                <i class="bi bi-plus-lg me-1"></i> تبرع جديد
            </a>
            @if(auth()->check())
                @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                    <a href="{{ route('donors.edit', $donor) }}" class="btn btn-outline-primary">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                    <button class="btn btn-outline-danger" onclick="openCancelModal('{{ route('donors.destroy', $donor) }}', false)">
                        <i class="bi bi-trash me-1"></i> حذف
                    </button>
                @else
                    <a href="{{ route('donors.edit', $donor) }}" class="btn btn-outline-warning">
                        <i class="bi bi-pencil me-1"></i> طلب تعديل
                    </a>
                    <button class="btn btn-outline-warning" onclick="openCancelModal('{{ route('donors.destroy', $donor) }}', true)">
                        <i class="bi bi-x-circle me-1"></i> طلب إلغاء
                    </button>
                @endif
            @endif
            <a href="{{ route('donors.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Main Info -->
        <div class="col-md-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-transparent py-3">
                    <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-info-circle me-2"></i>البيانات الأساسية</h6>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">كود المتبرع</label>
                            <div class="fw-bold text-primary font-monospace">{{ $donor->code ?? ('D-' . $donor->id) }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">الهاتف</label>
                            <div class="fw-medium">{{ $donor->phone ?? '—' }}</div>
                        </div>
                        @if($donor->national_id)
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">الرقم القومي</label>
                            <div class="fw-bold font-monospace text-dark">{{ $donor->national_id }}</div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">العنوان</label>
                            <div class="fw-medium">{{ $donor->address ?? '—' }}</div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-muted small mb-1">التصنيف</label>
                            <div class="fw-medium">
                                {{ $donor->classification === 'recurring' ? 'متكرر' : 'مرة واحدة' }}
                            </div>
                        </div>

                        @if($donor->monthly_allocation_target)
                            <div class="col-md-12">
                                <label class="text-muted small mb-1">وجهة التبرع الشهري (محددة من المتبرع)</label>
                                <div class="fw-medium text-dark bg-light p-2 rounded"><i class="bi bi-geo-alt text-primary me-1"></i>{{ $donor->monthly_allocation_target }}</div>
                            </div>
                        @endif

                        @if($donor->classification === 'recurring' && $donor->recurring_cycle === 'monthly')
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">موعد التبرع الشهري</label>
                                <div class="fw-medium"><i class="bi bi-calendar-event text-warning me-1"></i>يوم {{ $donor->monthly_donation_day ?? 1 }} من كل شهر</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">موعد الاستحقاق والتبرع القادم</label>
                                <div class="fw-bold font-monospace">
                                    @if($donor->next_due_date)
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5 fs-6">
                                            <i class="bi bi-calendar-check me-1"></i>{{ $donor->next_due_date->format('Y-m-d') }}
                                        </span>
                                    @else
                                        <span class="text-muted small">يتم احتسابه تلقائياً عند تسجيل التبرع</span>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="text-muted small mb-0">الأطفال / الحالات المكفولة ({{ $sponsored->count() }})</label>
                                @if($effectiveMonthlyAmount > 0)
                                    <span class="text-success small fw-bold font-monospace">
                                        <i class="bi bi-cash-stack me-1"></i>إجمالي الكفالة: {{ number_format($effectiveMonthlyAmount, 2) }} ج.م شهرياً
                                    </span>
                                @endif
                            </div>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @forelse($sponsored as $sb)
                                    @php
                                        $childCommitment = (float) ($sb->pivot?->monthly_amount ?? 0);
                                        $donorMembersForFamily = $donor->sponsoredFamilyMembers->where('beneficiary_id', $sb->id);
                                        if ($childCommitment <= 0 && $donorMembersForFamily->isNotEmpty()) {
                                            $childCommitment = (float) $donorMembersForFamily->sum(fn ($m) => (float) ($m->pivot?->monthly_amount ?? 0));
                                        }
                                        if ($childCommitment <= 0 && $effectiveMonthlyAmount > 0 && $sponsored->count() === 1 && $donor->sponsoredFamilyMembers->count() <= 1) {
                                            $childCommitment = (float) $effectiveMonthlyAmount;
                                        }
                                        if ($childCommitment <= 0) {
                                            foreach ($history as $h) {
                                                if (!empty($h->beneficiary_allocations) && is_array($h->beneficiary_allocations)) {
                                                    foreach ($h->beneficiary_allocations as $ba) {
                                                        if (($ba['beneficiary_id'] ?? null) == $sb->id || ($ba['code'] ?? null) == $sb->code) {
                                                            $childCommitment = (float)($ba['amount'] ?? 0);
                                                            break 2;
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                        $sbRequiredTotal = (float) ($sb->total_family_sponsorship_amount ?? $sb->monthly_sponsorship_amount ?? 0);
                                        $sbRemainingTotal = (float) ($sb->remaining_family_sponsorship_amount ?? max(0, $sbRequiredTotal - $childCommitment));
                                    @endphp
                                    <div class="d-inline-flex align-items-center flex-wrap gap-1 bg-white border border-success rounded-pill px-3 py-1.5 shadow-sm">
                                        <a href="{{ route('beneficiaries.show', $sb) }}" class="text-success text-decoration-none fw-bold me-1">
                                            <i class="bi bi-person-heart me-1"></i> {{ $sb->full_name }} ({{ $sb->code ?? ('BEN-'.$sb->id) }})
                                        </a>
                                        @if($childCommitment > 0)
                                            <span class="badge bg-success text-white font-monospace fs-7 px-2.5 py-1 rounded-pill" title="المبلغ المخصص من هذا المتبرع لصالح الحالة">
                                                مساهمة الكفيل: {{ number_format($childCommitment, 2) }} ج.م
                                            </span>
                                        @endif
                                        @if($sbRequiredTotal > 0 && abs($sbRequiredTotal - $childCommitment) > 0.01)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace rounded-pill">
                                                إجمالي كفالة الحالة: {{ number_format($sbRequiredTotal, 0) }} ج.م
                                            </span>
                                            @if($sbRemainingTotal > 0)
                                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning font-monospace rounded-pill">
                                                    باقي للحالة: {{ number_format($sbRemainingTotal, 0) }} ج.م
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                @empty
                                    <span class="text-muted small">لا توجد حالات مكفولة مسجلة باسم هذا المتبرع</span>
                                @endforelse
                            </div>
                        </div>

                        @if($donor->sponsoredFamilyMembers->isNotEmpty())
                            <div class="col-md-12">
                                <label class="text-muted small mb-1">توزيع الكفالة على الأفراد / الأطفال المرتبطين تحديدًا ({{ $donor->sponsoredFamilyMembers->count() }})</label>
                                <div class="row g-2 mt-1">
                                    @foreach($donor->sponsoredFamilyMembers as $familyMember)
                                        @php
                                            $fmAmount = (float) ($familyMember->pivot->monthly_amount ?? 0);
                                            if ($fmAmount <= 0 && $effectiveMonthlyAmount > 0 && $donor->sponsoredFamilyMembers->count() === 1) {
                                                $fmAmount = (float) $effectiveMonthlyAmount;
                                            }
                                            if ($fmAmount <= 0) {
                                                foreach ($history as $h) {
                                                    if (!empty($h->beneficiary_allocations) && is_array($h->beneficiary_allocations)) {
                                                        foreach ($h->beneficiary_allocations as $ba) {
                                                            if (($ba['code'] ?? null) == $familyMember->code || ($ba['beneficiary_id'] ?? null) == $familyMember->beneficiary_id) {
                                                                $fmAmount = (float)($ba['amount'] ?? 0);
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                            $fmRequired = (float) ($familyMember->sponsorship_amount ?? 0);
                                            $fmCoveredAll = (float) $familyMember->sponsors->sum(fn ($s) => (float) ($s->pivot->monthly_amount ?? 0));
                                            $fmRemaining = max(0.0, $fmRequired - ($fmCoveredAll > 0 ? $fmCoveredAll : $fmAmount));
                                        @endphp
                                        <div class="col-md-6">
                                            <a href="{{ route('beneficiary-family-members.show', $familyMember) }}" class="text-decoration-none d-block border rounded-3 p-2.5 bg-light bg-opacity-25 hover-shadow">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-1">
                                                    <span class="fw-bold text-success"><i class="bi bi-person-badge me-1"></i>{{ $familyMember->full_name }}</span>
                                                    @if($fmAmount > 0)
                                                        <span class="badge bg-success text-white font-monospace px-2.5 py-1 rounded-pill">
                                                            مساهمة الكفيل: {{ number_format((float)$fmAmount, 2) }} ج.م
                                                        </span>
                                                    @endif
                                                </div>
                                                <div class="small text-muted mt-1 d-flex flex-wrap align-items-center gap-1">
                                                    <span>{{ $familyMember->relationship_label }} — أسرة {{ $familyMember->beneficiary?->full_name }} — كود: {{ $familyMember->code }}</span>
                                                    @if($fmRequired > 0 && abs($fmRequired - $fmAmount) > 0.01)
                                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle">المطلوب: {{ number_format($fmRequired, 0) }} ج</span>
                                                        @if($fmRemaining > 0)
                                                            <span class="badge bg-warning-subtle text-warning-emphasis border border-warning">باقي: {{ number_format($fmRemaining, 0) }} ج</span>
                                                        @endif
                                                    @endif
                                                </div>
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($hasSponsorshipOrRecurring)
                            <div class="col-12">
                                <hr class="my-1 opacity-25">
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1"><i class="bi bi-cash-coin text-success me-1"></i>قيمة الكفالة / الالتزام الشهري</label>
                                <div>
                                    @if($donor->sponsorship_monthly_amount > 0)
                                        <span class="fs-5 fw-bold text-success font-monospace">{{ number_format($donor->sponsorship_monthly_amount, 2) }}</span>
                                        <span class="text-muted small">جنيه مصري / شهرياً</span>
                                    @elseif($effectiveMonthlyAmount > 0)
                                        <span class="fs-5 fw-bold text-success font-monospace">{{ number_format($effectiveMonthlyAmount, 2) }}</span>
                                        <span class="text-muted small">جنيه مصري (محسوب من التبرعات)</span>
                                    @else
                                        <span class="text-muted small">غير محددة — <a href="{{ route('donors.edit', $donor) }}" class="text-primary text-decoration-none"><i class="bi bi-pencil me-1"></i>تحديد القيمة</a></span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1"><i class="bi bi-check2-circle text-primary me-1"></i>حالة سداد هذا الشهر ({{ now()->format('m / Y') }})</label>
                                <div>
                                    @if($paidThisMonth > 0)
                                        <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1.5 fs-6">
                                            <i class="bi bi-check2-all me-1"></i>تم سداد {{ number_format($paidThisMonth, 2) }} جنيه
                                        </span>
                                    @else
                                        <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2.5 py-1.5 fs-6">
                                            <i class="bi bi-hourglass-split me-1"></i>لم يُسدد بعد لهذا الشهر
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats -->
        <div class="col-md-4">
            <div class="row g-3">
                @if($hasSponsorshipOrRecurring && $effectiveMonthlyAmount > 0)
                    <div class="col-12">
                        <div class="card border-0 shadow-sm text-white" style="background: linear-gradient(135deg, #0d7a5f 0%, #15803d 100%);">
                            <div class="card-body p-3 text-center">
                                <div class="opacity-90 small mb-1"><i class="bi bi-heart-pulse-fill me-1"></i>قيمة الكفالة / التبرع الشهري</div>
                                <div class="h3 fw-bold mb-0 font-monospace">{{ number_format($effectiveMonthlyAmount, 2) }} <span class="fs-6 fw-normal opacity-75">ج.م / شهرياً</span></div>
                                <div class="mt-2 pt-2 border-top border-white border-opacity-25 d-flex justify-content-between align-items-center small">
                                    <span class="opacity-90">سداد {{ now()->format('m / Y') }}:</span>
                                    @if($paidThisMonth > 0)
                                        <span class="badge bg-white text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>تم السداد ({{ number_format($paidThisMonth, 2) }})</span>
                                    @else
                                        <span class="badge bg-white text-warning text-dark fw-bold"><i class="bi bi-clock-history me-1"></i>بانتظار السداد</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-12">
                    <div class="card border-0 shadow-sm bg-primary text-white">
                        <div class="card-body p-4 text-center">
                            <div class="opacity-75 small mb-1">إجمالي التبرعات</div>
                            <div class="display-6 fw-bold">{{ number_format((float) ($stats->total ?? 0), 2) }}</div>
                            <div class="small opacity-75 mt-1">جنيه مصري</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">عدد التبرعات</div>
                            <div class="h4 mb-0 fw-bold text-dark">{{ (int) ($stats->count ?? 0) }}</div>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card border-0 shadow-sm text-center h-100">
                        <div class="card-body p-3">
                            <div class="text-muted small mb-1">أخر تبرع</div>
                            <div class="h5 mb-0 fw-bold text-dark">
{{ $history->first() ? optional($history->first()->received_at)->format('Y-m-d') : '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm">
        <div class="card-header bg-transparent py-3 d-flex justify-content-between align-items-center">
            <h6 class="mb-0 fw-bold text-primary"><i class="bi bi-clock-history me-2"></i>سجل التبرعات</h6>
            <span class="badge bg-secondary rounded-pill px-3">{{ count($history) }}</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>النوع</th>
                        <th>القيمة</th>
                        <th>القناة</th>
                        <th>التوجيه</th>
                        <th>الحالة / الشهور المسددة</th>
                        <th class="text-center" style="min-width: 160px;">الإجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $d)
                        <tr>
                            <td class="text-muted small">{{ $d->id }}</td>
                            <td>{{ optional($d->received_at)->format('Y-m-d') ?? '—' }}</td>
                            <td>
                                <span
                                    class="badge {{ $d->type === 'cash' ? 'bg-success-subtle text-success' : 'bg-info-subtle text-info' }}">
                                    {{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}
                                </span>
                            </td>
                            <td class="fw-bold">
                                @if($d->type === 'cash')
                                    {{ number_format($d->amount, 2) }} <small class="text-muted">{{ $d->currency }}</small>
                                @else
                                    {{ number_format($d->estimated_value, 2) }} <small class="text-muted">{{ $d->currency }}</small>
                                @endif
                            </td>
                            <td class="small">
                                @if($d->type === 'cash')
                                    {{ $d->cash_channel === 'instapay' ? 'انستا باي' : ($d->cash_channel === 'vodafone_cash' ? 'فودافون كاش' : 'نقدي') }}
                                    @if($d->receipt_number)
                                    <div class="text-muted" style="font-size:0.8em">#{{ $d->receipt_number }}</div>@endif
                                @else
                                    —
                                @endif
                            </td>
                            <td class="small">
                                @if($d->project)
                                    <div class="text-truncate" style="max-width:150px" title="{{$d->project->name}}"><i
                                class="bi bi-building text-muted me-1"></i>{{$d->project->name}}</div>@endif
                                @if($d->campaign)
                                    <div class="text-truncate" style="max-width:150px" title="{{$d->campaign->name}}"><i
                                class="bi bi-megaphone text-muted me-1"></i>{{$d->campaign->name}}</div>@endif
                                @if($d->purpose_label)
                                    <span class="badge bg-light text-dark border">{{ $d->purpose_label }}</span>
                                @endif
                            </td>
                            <td class="small">
                                @if($d->beneficiary)
                                    <a href="{{ route('beneficiaries.show', $d->beneficiary) }}" class="badge bg-success-subtle text-success text-decoration-none d-inline-block mb-1">
                                        <i class="bi bi-person-heart me-1"></i>{{ $d->beneficiary->code ?? ('BEN-'.$d->beneficiary->id) }}
                                    </a>
                                @endif
                                @if(!empty($d->paid_months) && is_array($d->paid_months))
                                    <div class="d-flex flex-wrap gap-1">
                                        @foreach($d->paid_months as $pm)
                                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace" style="font-size:0.7rem">{{ $pm }}</span>
                                        @endforeach
                                    </div>
                                @elseif(!$d->beneficiary)
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="d-inline-flex gap-1">
                                    <a href="{{ route('donations.show', $d) }}" class="btn btn-sm btn-outline-primary px-2 py-1"
                                        title="عرض التفاصيل">
                                        <i class="bi bi-eye me-1"></i> عرض
                                    </a>
                                    <a href="{{ route('donations.show', $d) }}?print=1" target="_blank" class="btn btn-sm btn-outline-success px-2 py-1"
                                        title="طباعة إيصال التبرع">
                                        <i class="bi bi-printer me-1"></i> طباعة
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="py-4">
                                    <i class="bi bi-gift text-warning display-4 mb-3 d-inline-block"></i>
                                    <h5 class="fw-bold text-dark">هذا المتبرع مضاف جديد ولم يسجل أي تبرع حتى الآن</h5>
                                    <p class="text-muted small">يمكنك تسجيل تبرع جديد له بالضغط على الزر أدناه</p>
                                    <a href="{{ route('donations.create', ['donor_id' => $donor->id]) }}" class="btn btn-success fw-bold rounded-pill px-4 mt-2">
                                        <i class="bi bi-plus-lg me-1"></i> تسجيل أول تبرع للمتبرع
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Print Preview Modal --}}
    <div class="modal fade" id="printPreviewModal" tabindex="-1" aria-labelledby="printPreviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-light py-2 border-bottom">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-success bg-opacity-10 text-success p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="bi bi-file-earmark-text-fill fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold text-dark mb-0 fs-6" id="printPreviewModalLabel">معاينة نموذج التقرير وكشف الحساب الرسمي</h5>
                            <small class="text-muted">هذا هو الشكل النهائي الدقيق للتقرير كما سيظهر في الطباعة (A4)</small>
                        </div>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <button type="button" onclick="printDonorReport()" class="btn btn-success btn-sm px-3 shadow-sm fw-bold">
                            <i class="bi bi-printer me-1"></i> طباعة التقرير الآن
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                </div>
                <div class="modal-body p-3 p-md-4" style="background-color: #f1f5f9;">
                    <div class="print-preview-wrapper d-flex justify-content-center">
                        
                        {{-- The Official Printable Sheet (Rendered for Preview and Print) --}}
                        <div id="printableDonorSheet" class="w-100 position-relative" 
                             style="max-width: 900px; margin: 0 auto; background: #ffffff !important; color: #0f172a !important; border: 2.5px solid #059669; outline: 1px solid #10b981; outline-offset: -5px; padding: 22px 26px; border-radius: 8px; font-family: 'IBM Plex Sans Arabic', system-ui, -apple-system, sans-serif; box-sizing: border-box;">
                            
                            {{-- Dedicated Embedded Sheet Styles to guarantee 100% layout and font clarity everywhere --}}
                            <style>
                                #printableDonorSheet,
                                #printableDonorSheet *,
                                body.theme-dark #printableDonorSheet,
                                body.theme-dark #printableDonorSheet *,
                                html.theme-dark #printableDonorSheet,
                                html.theme-dark #printableDonorSheet *,
                                [data-bs-theme="dark"] #printableDonorSheet,
                                [data-bs-theme="dark"] #printableDonorSheet * {
                                    color-scheme: light !important;
                                    box-sizing: border-box !important;
                                }

                                #printableDonorSheet {
                                    background: #ffffff !important;
                                    color: #0f172a !important;
                                    direction: rtl !important;
                                }

                                /* Absolute font clarity: prevent theme-dark from turning text white on white paper */
                                #printableDonorSheet div,
                                #printableDonorSheet p,
                                #printableDonorSheet span,
                                #printableDonorSheet td,
                                #printableDonorSheet th,
                                #printableDonorSheet strong,
                                #printableDonorSheet b,
                                #printableDonorSheet small,
                                body.theme-dark #printableDonorSheet div,
                                body.theme-dark #printableDonorSheet p,
                                body.theme-dark #printableDonorSheet span,
                                body.theme-dark #printableDonorSheet td,
                                body.theme-dark #printableDonorSheet th,
                                body.theme-dark #printableDonorSheet strong,
                                body.theme-dark #printableDonorSheet b,
                                body.theme-dark #printableDonorSheet small,
                                html.theme-dark #printableDonorSheet div,
                                html.theme-dark #printableDonorSheet p,
                                html.theme-dark #printableDonorSheet span,
                                html.theme-dark #printableDonorSheet td,
                                html.theme-dark #printableDonorSheet th,
                                html.theme-dark #printableDonorSheet strong,
                                html.theme-dark #printableDonorSheet b,
                                html.theme-dark #printableDonorSheet small {
                                    color: #0f172a !important;
                                }

                                .ensan-report-header {
                                    display: flex !important;
                                    justify-content: space-between !important;
                                    align-items: center !important;
                                    border-bottom: 2.5px solid #059669 !important;
                                    padding-bottom: 12px !important;
                                    margin-bottom: 14px !important;
                                }
                                .ensan-meta-grid {
                                    display: grid !important;
                                    grid-template-columns: repeat(3, 1fr) !important;
                                    gap: 8px 14px !important;
                                }
                                .ensan-stats-grid {
                                    display: grid !important;
                                    grid-template-columns: repeat(3, 1fr) !important;
                                    gap: 12px !important;
                                    margin-bottom: 12px !important;
                                }
                                .ensan-signatures-grid {
                                    display: flex !important;
                                    justify-content: space-between !important;
                                    align-items: flex-end !important;
                                    text-align: center !important;
                                    border-top: 1.5px solid #94a3b8 !important;
                                    padding-top: 16px !important;
                                    margin-top: 16px !important;
                                }
                                .ensan-report-table {
                                    width: 100% !important;
                                    border-collapse: collapse !important;
                                    text-align: center !important;
                                    font-size: 12.5px !important;
                                    margin-bottom: 14px !important;
                                    background: #ffffff !important;
                                }
                                .ensan-report-table th {
                                    background-color: #e2e8f0 !important;
                                    color: #0f172a !important;
                                    font-weight: 800 !important;
                                    border: 1.5px solid #94a3b8 !important;
                                    padding: 8px 6px !important;
                                }
                                .ensan-report-table td {
                                    border: 1px solid #cbd5e1 !important;
                                    padding: 7px 8px !important;
                                    color: #0f172a !important;
                                    font-weight: 600 !important;
                                }
                                .ensan-report-table tbody tr:nth-child(even) {
                                    background-color: #f8fafc !important;
                                }
                                @media print {
                                    #printableDonorSheet {
                                        padding: 16px !important;
                                        border: 2px solid #059669 !important;
                                        outline: none !important;
                                    }
                                }
                            </style>

                            {{-- Watermark in background --}}
                            <div class="sheet-watermark position-absolute top-50 start-50 translate-middle text-center" 
                                 style="opacity: 0.035; pointer-events: none; z-index: 0; user-select: none;">
                                <img src="{{ asset('images/logo.png') }}" alt="" style="width: 320px; filter: grayscale(100%);">
                            </div>

                            <div class="position-relative" style="z-index: 1;">
                                {{-- Official Header --}}
                                <div class="ensan-report-header">
                                    {{-- Right Column: Official Authority Details --}}
                                    <div style="text-align: right; line-height: 1.45; min-width: 180px;">
                                        <div style="font-size: 13px; font-weight: 800; color: #1e293b !important;">جمهورية مصر العربية</div>
                                        <div style="font-size: 17px; font-weight: 900; color: #047857 !important; margin-top: 2px;">مؤسسة إنسان الخيرية</div>
                                        <div style="font-size: 12px; font-weight: 700; color: #475569 !important;">المشهرة برقم 63 لسنة 2017م</div>
                                    </div>

                                    {{-- Center Column: Document Title & Branding --}}
                                    <div style="text-align: center; padding: 0 10px;">
                                        <div style="display: inline-block; background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%) !important; border: 2px solid #059669 !important; border-radius: 8px; padding: 8px 24px; box-shadow: 0 2px 6px rgba(5, 150, 105, 0.1);">
                                            <div style="margin: 0; font-size: 19px; font-weight: 900; color: #065f46 !important; letter-spacing: -0.2px;">تقرير ملف وكشف حساب المتبرع</div>
                                            <div style="font-size: 10.5px; font-weight: 800; color: #047857 !important; letter-spacing: 1.2px; font-family: monospace; margin-top: 3px;">OFFICIAL DONOR ACCOUNT STATEMENT</div>
                                        </div>
                                    </div>

                                    {{-- Left Column: Official Metadata Badge --}}
                                    <div style="text-align: right; line-height: 1.6; min-width: 180px;">
                                        <div style="display: inline-block; background: #f8fafc !important; border: 1.5px solid #cbd5e1 !important; border-radius: 6px; padding: 8px 12px; font-size: 12px; width: 100%;">
                                            <div><span style="color: #475569 !important; font-weight: 700;">كود المتبرع:</span> <strong style="font-family: monospace; color: #0f172a !important; font-size: 12.5px; font-weight: 900;">{{ $donor->code ?? ('DON-' . $donor->id) }}</strong></div>
                                            <div><span style="color: #475569 !important; font-weight: 700;">تاريخ الإصدار:</span> <strong style="font-family: monospace; color: #0f172a !important; font-weight: 800;">{{ now()->format('Y/m/d') }}</strong></div>
                                            <div><span style="color: #475569 !important; font-weight: 700;">حرر بواسطة:</span> <strong style="color: #0f172a !important; font-weight: 700;">{{ auth()->user()?->name ?? 'النظام الآلي' }}</strong></div>
                                            <div><span style="color: #475569 !important; font-weight: 700;">رقم السجل:</span> <strong style="font-family: monospace; color: #047857 !important; font-weight: 900;">#{{ $donor->id }}</strong></div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Basic Info Box --}}
                                <div style="background-color: #f8fafc !important; border: 1.5px solid #cbd5e1 !important; border-radius: 6px; padding: 12px 14px; margin-bottom: 12px;">
                                    <div style="font-size: 13.5px; font-weight: 800; color: #047857 !important; border-bottom: 1px solid #cbd5e1; padding-bottom: 6px; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <span>بيانات المتبرع الأساسية وحالة الحساب</span>
                                    </div>
                                    <div class="ensan-meta-grid" style="font-size: 12.5px; line-height: 1.8;">
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">اسم المتبرع:</span>
                                            <strong style="color: #0f172a !important; font-size: 13.5px; font-weight: 900;">{{ $donor->name }}</strong>
                                        </div>
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">نوع الحساب:</span>
                                            <strong style="color: #0f172a !important; font-weight: 800;">{{ $donor->type === 'individual' ? 'فرد' : 'منظمة / شركة' }} ({{ $donor->active ? 'نشط' : 'غير نشط' }})</strong>
                                        </div>
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">رقم الهاتف:</span>
                                            <strong style="font-family: monospace; color: #0f172a !important; font-weight: 900;">{{ $donor->phone ?? '—' }}</strong>
                                        </div>
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">العنوان:</span>
                                            <span style="color: #0f172a !important; font-weight: 700;">{{ $donor->address ?? '—' }}</span>
                                        </div>
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">التصنيف الدوري:</span>
                                            <strong style="color: #047857 !important; font-weight: 900;">{{ $donor->classification === 'recurring' ? 'متبرع دوري متكرر' : 'تبرع لمرة واحدة' }}</strong>
                                        </div>
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">الكفالات المسجلة:</span>
                                            <strong style="color: #1d4ed8 !important; font-weight: 900;">{{ $donor->sponsoredBeneficiaries->count() }} كفالة منتظمة</strong>
                                        </div>
                                        @if($effectiveMonthlyAmount > 0)
                                        <div>
                                            <span style="color: #475569 !important; font-weight: 700;">قيمة الكفالة الشهرية:</span>
                                            <strong style="color: #047857 !important; font-weight: 900; font-family: monospace;">{{ number_format($effectiveMonthlyAmount, 2) }} ج.م</strong>
                                        </div>
                                        @endif
                                        @if($donor->monthly_allocation_target)
                                        <div style="grid-column: span 3; border-top: 1px dashed #94a3b8; padding-top: 6px; margin-top: 2px;">
                                            <span style="color: #475569 !important; font-weight: 700;">وجهة التبرع الشهري المحددة:</span>
                                            <strong style="color: #047857 !important; font-weight: 900;">{{ $donor->monthly_allocation_target }}</strong>
                                        </div>
                                        @endif
                                    </div>
                                </div>

                                {{-- Financial Snapshot Cards --}}
                                @php
                                    $computedTotal = (float)($stats->total ?? 0);
                                    if ($computedTotal == 0 && count($history) > 0) {
                                        $computedTotal = (float)$history->sum(fn($h) => $h->type === 'cash' ? $h->amount : $h->estimated_value);
                                    }
                                    $computedCount = (int)($stats->count ?? count($history));
                                    $totalTafqeet = \App\Helpers\TafqeetHelper::inWords($computedTotal, 'جنيه مصري');
                                @endphp
                                <div class="ensan-stats-grid" style="text-align: center;">
                                    <div style="background-color: #f0fdf4 !important; border: 1.5px solid #86efac !important; border-radius: 8px; padding: 10px 8px;">
                                        <div style="font-size: 12px; color: #166534 !important; font-weight: 800;">إجمالي التبرعات المودعة</div>
                                        <div style="font-size: 21px; font-weight: 900; color: #047857 !important; font-family: monospace; margin-top: 3px;">
                                            {{ number_format($computedTotal, 2) }} <span style="font-size: 12.5px; font-weight: 800; color: #047857 !important;">ج.م</span>
                                        </div>
                                    </div>
                                    <div style="background-color: #eff6ff !important; border: 1.5px solid #93c5fd !important; border-radius: 8px; padding: 10px 8px;">
                                        <div style="font-size: 12px; color: #1e40af !important; font-weight: 800;">إجمالي عدد التبرعات</div>
                                        <div style="font-size: 21px; font-weight: 900; color: #1d4ed8 !important; font-family: monospace; margin-top: 3px;">
                                            {{ $computedCount }} <span style="font-size: 12.5px; font-weight: 800; color: #1d4ed8 !important;">عمليات</span>
                                        </div>
                                    </div>
                                    <div style="background-color: #fffbeb !important; border: 1.5px solid #fde68a !important; border-radius: 8px; padding: 10px 8px;">
                                        <div style="font-size: 12px; color: #92400e !important; font-weight: 800;">تاريخ آخر تبرع مسجل</div>
                                        <div style="font-size: 18px; font-weight: 900; color: #b45309 !important; font-family: monospace; margin-top: 4px;">
                                            {{ $history->first() ? optional($history->first()->received_at)->format('Y-m-d') : '—' }}
                                        </div>
                                    </div>
                                </div>

                                {{-- Tafqeet Row --}}
                                <div style="background-color: #f8fafc !important; border: 1.5px solid #cbd5e1 !important; border-radius: 6px; padding: 9px 14px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 12.5px;">
                                    <div>
                                        <span style="color: #334155 !important; font-weight: 800;">المبلغ الإجمالي بالحروف:</span>
                                        <strong style="color: #047857 !important; font-weight: 900; margin-right: 4px; font-size: 13px;">{{ $totalTafqeet }}</strong>
                                    </div>
                                    <div style="color: #475569 !important; font-family: monospace; font-size: 11.5px; font-weight: 700;">كشف الحساب موثق آلياً</div>
                                </div>

                                {{-- Donations Table --}}
                                <div style="margin-bottom: 12px;">
                                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                                        <div style="font-size: 13.5px; font-weight: 900; color: #047857 !important; display: flex; align-items: center; gap: 6px;">
                                            <i class="bi bi-clock-history"></i>
                                            <span>كشف تفصيلي بحركات وسجل التبرعات</span>
                                        </div>
                                        <span style="font-size: 12px; color: #334155 !important; font-weight: 700;">عدد العمليات المسجلة ({{ count($history) }})</span>
                                    </div>
                                    <table class="ensan-report-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 38px; color: #0f172a !important; font-weight: 900;">م</th>
                                                <th style="width: 100px; color: #0f172a !important; font-weight: 900;">تاريخ التوريد</th>
                                                <th style="width: 80px; color: #0f172a !important; font-weight: 900;">نوع التبرع</th>
                                                <th style="width: 130px; color: #0f172a !important; font-weight: 900;">القيمة المحصلة</th>
                                                <th style="width: 150px; color: #0f172a !important; font-weight: 900;">القناة ورقم الإيصال</th>
                                                <th style="text-align: right; color: #0f172a !important; font-weight: 900;">التوجيه والمشروع</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php $sumTable = 0; @endphp
                                            @forelse($history as $idx => $d)
                                                @php
                                                    $val = $d->type === 'cash' ? $d->amount : $d->estimated_value;
                                                    $sumTable += (float)$val;
                                                @endphp
                                                <tr>
                                                    <td style="color: #475569 !important; font-family: monospace; font-weight: 700;">{{ $idx + 1 }}</td>
                                                    <td style="font-family: monospace; font-weight: 800; color: #0f172a !important;">{{ optional($d->received_at)->format('Y-m-d') ?? '—' }}</td>
                                                    <td>
                                                        <span style="display: inline-block; padding: 2px 8px; border-radius: 4px; font-size: 11.5px; font-weight: 800; background: {{ $d->type === 'cash' ? '#dcfce7' : '#e0f2fe' }}; color: {{ $d->type === 'cash' ? '#166534' : '#075985' }} !important;">
                                                            {{ $d->type === 'cash' ? 'نقدي' : 'عيني' }}
                                                        </span>
                                                    </td>
                                                    <td style="font-weight: 900; font-family: monospace; color: #047857 !important; font-size: 13.5px;">{{ number_format($val, 2) }} {{ $d->currency }}</td>
                                                    <td style="font-size: 12px; color: #0f172a !important;">
                                                        <div style="font-weight: 700;">{{ $d->type === 'cash' ? ($d->cash_channel === 'instapay' ? 'انستا باي' : ($d->cash_channel === 'vodafone_cash' ? 'فودافون كاش' : 'نقدي بالخزينة')) : '—' }}</div>
                                                        @if($d->receipt_number)
                                                            <div style="color: #475569 !important; font-family: monospace; font-size: 11px; font-weight: 700;">وصل: {{ $d->receipt_number }}</div>
                                                        @endif
                                                    </td>
                                                    <td style="text-align: right; font-size: 12px; color: #0f172a !important;">
                                                        @if($d->project) <div style="font-weight: 700;"><i class="bi bi-building text-muted me-1"></i>{{ $d->project->name }}</div> @endif
                                                        @if($d->campaign) <div style="font-weight: 700;"><i class="bi bi-megaphone text-muted me-1"></i>{{ $d->campaign->name }}</div> @endif
                                                        @if(!$d->project && !$d->campaign) <span style="color: #64748b !important; font-weight: 600;">—</span> @endif
                                                    </td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="6" style="padding: 18px; color: #475569 !important; text-align: center; font-weight: 700;">لا توجد تبرعات مسجلة لهذا المتبرع حتى الآن</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                        @if(count($history) > 0)
                                        <tfoot>
                                            <tr style="background-color: #f1f5f9 !important; font-weight: 900; border-top: 2px solid #64748b; border-bottom: 2px solid #64748b;">
                                                <td colspan="3" style="text-align: left; padding: 9px 10px; font-size: 13.5px; color: #0f172a !important;">الإجمالي العام:</td>
                                                <td style="color: #047857 !important; font-size: 16px; font-family: monospace; padding: 9px 10px; font-weight: 900;">{{ number_format($sumTable, 2) }} ج.م</td>
                                                <td colspan="2" style="text-align: right; color: #1e293b !important; font-size: 12px; font-weight: 800; padding: 9px 10px;">
                                                    فقط وقدره {{ \App\Helpers\TafqeetHelper::inWords($sumTable, 'جنيه مصري') }}
                                                </td>
                                            </tr>
                                        </tfoot>
                                        @endif
                                    </table>
                                </div>

                                {{-- Official Endorsement Signatures --}}
                                <div class="ensan-signatures-grid">
                                    <div style="width: 32%;">
                                        <div style="font-weight: 900; font-size: 13px; color: #0f172a !important; margin-bottom: 38px;">مسؤول شؤون المتبرعين</div>
                                        <div style="border-top: 1.5px dashed #475569; width: 140px; margin: 0 auto; padding-top: 4px; font-size: 11.5px; color: #334155 !important; font-weight: 700;">التوقيع والاسم</div>
                                    </div>
                                    <div style="width: 32%;">
                                        <div style="font-weight: 900; font-size: 13px; color: #0f172a !important; margin-bottom: 8px;">ختم المؤسسة الرسمي</div>
                                        <div style="width: 68px; height: 68px; border: 2.5px dashed #059669; border-radius: 50%; margin: 0 auto; display: flex; align-items: center; justify-content: center; opacity: 0.75; color: #059669 !important;">
                                            <i class="bi bi-patch-check" style="font-size: 34px; color: #059669 !important;"></i>
                                        </div>
                                    </div>
                                    <div style="width: 32%;">
                                        <div style="font-weight: 900; font-size: 13px; color: #0f172a !important; margin-bottom: 38px;">المدير التنفيذي / المالي</div>
                                        <div style="border-top: 1.5px dashed #475569; width: 140px; margin: 0 auto; padding-top: 4px; font-size: 11.5px; color: #334155 !important; font-weight: 700;">الاعتماد والتوقيع</div>
                                    </div>
                                </div>

                                {{-- Quranic Footer --}}
                                <div style="text-align: center; margin-top: 14px; padding-top: 8px; border-top: 1px solid #cbd5e1; font-size: 11.5px; color: #334155 !important; line-height: 1.6;">
                                    <div style="font-weight: 900; color: #047857 !important; margin-bottom: 2px; font-size: 13px;">
                                        ﴿ وَمَا تُنفِقُوا مِنْ خَيْرٍ يُوَفَّ إِلَيْكُمْ وَأَنتُمْ لَا تُظْلَمُونَ ﴾
                                    </div>
                                    <div style="font-weight: 700; color: #475569 !important;">مستند رسمي صادر وموثق إلكترونياً من نظام مؤسسة إنسان الخيرية — أي كشط أو تعديل يدوي يلغي صلاحية التقرير.</div>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
                <div class="modal-footer bg-light py-2">
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إغلاق</button>
                    <button type="button" onclick="printDonorReport()" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-printer me-1"></i> أمر الطباعة
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Cancel/Delete Modal --}}
    <div class="modal fade" id="cancelModal" tabindex="-1">
        <div class="modal-dialog">
            <form id="cancelForm" method="POST" class="modal-content">
                @csrf
                @method('DELETE')
                <div class="modal-header">
                    <h5 class="modal-title text-warning">طلب إلغاء</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>هل أنت متأكد من رغبتك في إرسال طلب إلغاء لهذا المتبرع؟</p>
                    <div class="mb-3">
                        <label class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                        <textarea name="reason" class="form-control" rows="3" placeholder="اكتب سبب الإلغاء هنا... - مطلوب" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">تراجع</button>
                    <button type="submit" class="btn btn-warning">إرسال الطلب</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Print Styles (Native Fallback) --}}
    <style>
        @media print {
            @page {
                size: A4 portrait;
                margin: 8mm 10mm;
            }
            html, body {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                padding: 0 !important;
                height: auto !important;
                min-height: auto !important;
                overflow: visible !important;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .sidebar, .sidebar-fixed, .navbar, .page-header, .card:not(#printableDonorSheet .card), .modal-header, .modal-footer, .btn, .modal-backdrop, .toast-container, #cancelModal, #universalPrintPreviewModal {
                display: none !important;
            }
            .main-content {
                margin: 0 !important;
                padding: 0 !important;
                width: 100% !important;
                overflow: visible !important;
            }
            #printPreviewModal {
                position: static !important;
                display: block !important;
                overflow: visible !important;
                width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
            }
            #printPreviewModal .modal-dialog,
            #printPreviewModal .modal-content,
            #printPreviewModal .modal-body,
            #printPreviewModal .print-preview-wrapper {
                position: static !important;
                display: block !important;
                overflow: visible !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 0 !important;
                background: transparent !important;
                border: none !important;
                box-shadow: none !important;
                transform: none !important;
            }
            #printableDonorSheet {
                display: block !important;
                position: relative !important;
                width: 100% !important;
                max-width: 100% !important;
                margin: 0 !important;
                padding: 8mm !important;
                background: #ffffff !important;
                color: #0f172a !important;
                border: 2px solid #059669 !important;
                box-shadow: none !important;
                box-sizing: border-box !important;
            }
        }
    </style>

@endsection

@section('scripts')
<script>
    function printDonorReport() {
        var sheet = document.getElementById('printableDonorSheet');
        if (!sheet) {
            window.print();
            return;
        }
        if (typeof window.printHtmlDocument === 'function') {
            window.printHtmlDocument(sheet.outerHTML, 'تقرير ملف وكشف حساب المتبرع - {{ $donor->name }}', false);
        } else {
            window.print();
        }
    }

    function openCancelModal(actionUrl, isRequest = true) {
        const form = document.getElementById('cancelForm');
        form.action = actionUrl;
        const title = form.querySelector('.modal-title');
        const btn = form.querySelector('button[type="submit"]');
        const reasonDiv = form.querySelector('.mb-3');
        const reasonInput = form.querySelector('textarea[name="reason"]');

        if (isRequest) {
            if (title) { title.textContent = 'طلب إلغاء'; title.className = 'modal-title text-warning'; }
            if (btn) { btn.textContent = 'إرسال الطلب'; btn.className = 'btn btn-warning'; }
            if (reasonDiv) reasonDiv.style.display = 'block';
            if (reasonInput) reasonInput.required = true;
        } else {
            if (title) { title.textContent = 'تأكيد حذف المتبرع'; title.className = 'modal-title text-danger'; }
            if (btn) { btn.textContent = 'حذف نهائي فوري'; btn.className = 'btn btn-danger'; }
            if (reasonDiv) reasonDiv.style.display = 'none';
            if (reasonInput) { reasonInput.required = false; reasonInput.value = 'حذف مباشر من الإدارة'; }
        }
        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }
    @if(request('preview'))
    window.addEventListener('load', function() {
        var m = document.getElementById('printPreviewModal');
        if (m && typeof bootstrap !== 'undefined') {
            bootstrap.Modal.getOrCreateInstance(m).show();
        }
    });
    @endif
    @if(request('print'))
    window.addEventListener('load', function() {
        setTimeout(function() { printDonorReport(); }, 400);
    });
    @endif
</script>
@endsection
