{{-- Detailed Case Dossier Content --}}
@php
    $p = is_array($req->payload ?? null) ? ($req->payload['data'] ?? $req->payload) : [];
    $displayName = $displayName ?? ($req->subject?->full_name ?? ($p['full_name'] ?? ($p['name'] ?? '—')));
    $cleanNat = $cleanNat ?? preg_replace('/[^0-9]/', '', (string)($req->subject?->national_id ?? ($p['national_id'] ?? '')));
    $rawPhone = $rawPhone ?? ($req->subject?->phone ?? ($p['phone'] ?? ''));
    $projectId = $p['project_id'] ?? ($req->subject?->project_id ?? null);
    $projectName = $projectName ?? (($projectsMap ?? [])[$projectId] ?? ($req->subject?->project?->name ?? '—'));
    $familyList = $familyList ?? ($p['family_members'] ?? ($p['family'] ?? ($req->subject?->familyMembers?->toArray() ?? [])));
@endphp

<div class="case-dossier-wrapper">
    {{-- Card 1: Primary Beneficiary Information --}}
    <div class="card border rounded-3 mb-3 shadow-none bg-white">
        <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
            <i class="bi bi-person-lines-fill text-primary"></i>
            <strong class="text-dark small">البيانات الأساسية للحالة</strong>
        </div>
        <div class="card-body p-3">
            <div class="row g-2 small">
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">الاسم بالكامل:</span>
                        <strong class="text-dark fs-6">{{ $displayName }}</strong>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">الرقم القومي:</span>
                        <strong class="text-dark font-monospace">{{ $cleanNat ?: '—' }}</strong>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">كود الحالة:</span>
                        <strong class="text-primary font-monospace">{{ $p['code'] ?? ($req->subject?->code ?? '—') }}</strong>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">رقم الهاتف:</span>
                        <strong class="text-dark">{{ $rawPhone ?: '—' }}</strong>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">هاتف بديل:</span>
                        <strong class="text-dark">{{ $p['backup_phone'] ?? ($req->subject?->backup_phone ?? '—') }}</strong>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">المشروع التابع له:</span>
                        <strong class="text-dark">{{ $projectName ?: '—' }}</strong>
                    </div>
                </div>
                <div class="col-md-6 col-sm-12">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">العنوان السكني:</span>
                        <strong class="text-dark">{{ $p['address'] ?? ($req->subject?->address ?? '—') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">طريقة الصرف:</span>
                        <strong class="text-dark">{{ $p['collection_method'] ?? ($req->subject?->collection_method ?? '—') }}</strong>
                    </div>
                </div>
                <div class="col-md-3 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">رقم بطاقة الفيزا:</span>
                        <strong class="text-dark font-monospace">{{ $p['visa_card_number'] ?? ($req->subject?->visa_card_number ?? '—') }}</strong>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">نوع المساعدة:</span>
                        <span class="badge bg-info bg-opacity-10 text-info fw-bold">{{ $p['assistance_type'] ?? ($req->subject?->assistance_type ?? '—') }}</span>
                    </div>
                </div>
                <div class="col-md-4 col-sm-6">
                    <div class="p-2 border rounded-2 bg-light-subtle">
                        <span class="text-muted d-block x-small">حالة الحالة:</span>
                        <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ $p['status'] ?? ($req->subject?->status ?? 'مقبول') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Card 2: Family Members & Research Data --}}
    <div class="card border rounded-3 mb-3 shadow-none bg-white">
        <div class="card-header bg-light py-2 px-3 border-bottom d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center gap-2">
                <i class="bi bi-people-fill text-primary"></i>
                <strong class="text-dark small">دراسة أفراد الأسرة والأبناء والكفالات</strong>
            </div>
            @if(!empty($familyList))
                <span class="badge bg-primary rounded-pill px-2.5 py-1">{{ count($familyList) }} فرد مسجل</span>
            @endif
        </div>
        <div class="card-body p-0">
            @if(!empty($familyList))
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 align-middle small text-center">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 40px;">#</th>
                                <th>الاسم</th>
                                <th>صلة القرابة</th>
                                <th>السن / تاريخ الميلاد</th>
                                <th>المرحلة التعليمية</th>
                                <th>مبلغ الكفالة</th>
                                <th>كود الفرد</th>
                                <th>ملاحظات الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalSponsorship = 0; @endphp
                            @foreach($familyList as $idx => $member)
                                @php
                                    $mName = $member['full_name'] ?? ($member['name'] ?? '—');
                                    $mRel = match($member['relationship'] ?? ($member['relation'] ?? '')) {
                                        'husband' => 'الزوج',
                                        'wife' => 'الزوجة',
                                        'child' => 'ابن/ابنة',
                                        'father' => 'الأب',
                                        'mother' => 'الأم',
                                        'brother' => 'الأخ',
                                        'sister' => 'الأخت',
                                        default => ($member['relationship'] ?? ($member['relation'] ?? '—'))
                                    };
                                    $mAge = $member['age'] ?? ($member['age_dob'] ?? '—');
                                    $mBirth = $member['birth_date'] ?? null;
                                    $mEdu = $member['education_level'] ?? ($member['education'] ?? '—');
                                    $mAmt = (float)($member['sponsorship_amount'] ?? ($member['amount'] ?? 0));
                                    $mCode = $member['code'] ?? '—';
                                    $mDetails = $member['case_details'] ?? '—';
                                    $totalSponsorship += $mAmt;
                                @endphp
                                <tr>
                                    <td class="text-muted">{{ $idx + 1 }}</td>
                                    <td class="fw-bold text-start">{{ $mName }}</td>
                                    <td><span class="badge bg-light text-dark border">{{ $mRel }}</span></td>
                                    <td>
                                        {{ $mAge }}
                                        @if($mBirth) <div class="text-muted x-small">({{ $mBirth }})</div> @endif
                                    </td>
                                    <td>{{ $mEdu }}</td>
                                    <td>
                                        @if($mAmt > 0)
                                            <span class="badge bg-success bg-opacity-10 text-success fw-bold">{{ number_format($mAmt) }} ج.م</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="font-monospace text-muted">{{ $mCode }}</span></td>
                                    <td class="text-muted">{{ $mDetails }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        @if($totalSponsorship > 0)
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="5" class="text-end fw-bold">إجمالي مبالغ الكفالة المقترحة للأسرة:</th>
                                    <th class="fw-bold text-success">{{ number_format($totalSponsorship) }} ج.م</th>
                                    <th colspan="2"></th>
                                </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-people fs-2 d-block mb-1 opacity-50"></i>
                    لا توجد بيانات أفراد أسرة مسجلة في هذا الطلب.
                </div>
            @endif
        </div>
    </div>

    {{-- Card 3: Notes & Quick Field Visit Action --}}
    <div class="card border rounded-3 mb-0 shadow-none bg-white">
        <div class="card-header bg-light py-2 px-3 border-bottom d-flex align-items-center gap-2">
            <i class="bi bi-chat-left-text text-primary"></i>
            <strong class="text-dark small">الملاحظات وإجراءات الزيارة الميدانية</strong>
        </div>
        <div class="card-body p-3">
            <div class="mb-3">
                <span class="text-muted d-block small mb-1">الملاحظات وتفاصيل التقديم:</span>
                <div class="p-2 border rounded-2 bg-light-subtle text-dark small">
                    {{ $p['notes'] ?? ($req->subject?->notes ?? 'لا توجد ملاحظات إضافية مسجلة على هذا الطلب.') }}
                </div>
            </div>

            <div class="d-flex flex-wrap align-items-center justify-content-between gap-2 pt-2 border-top">
                <span class="text-muted small">
                    <i class="bi bi-info-circle me-1"></i> يمكنك اعتماد الطلب الحالي مباشرة، أو تسجيل زيارة ميدانية رسمية للحالة في جدول الزيارات.
                </span>
                <a href="{{ route('visits.create') }}?national_id={{ $cleanNat }}&patient_name={{ urlencode($displayName) }}&phone={{ $rawPhone }}" 
                   target="_blank" 
                   class="btn btn-sm btn-outline-primary fw-bold rounded-pill px-3 shadow-xs">
                    <i class="bi bi-plus-circle me-1"></i> تسجيل زيارة ميدانية جديدة للحالة
                </a>
            </div>
        </div>
    </div>
</div>
