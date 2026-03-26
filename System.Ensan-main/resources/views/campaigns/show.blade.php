@extends('layouts.app')
@section('content')
    <style>
        .gh-metric-card {
            background: var(--bg-card, #fff);
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            height: 100%;
            transition: transform 0.2s;
        }

        .gh-metric-card:hover {
            transform: translateY(-5px);
        }

        .gh-metric-icon {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .gh-section-title {
            font-weight: 700;
            margin-bottom: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .gh-list-item {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .gh-list-item:last-child {
            border-bottom: none;
        }

        .theme-dark .gh-metric-card {
            background: var(--bg-card);
        }
    </style>

    {{-- Page Header --}}
    <div class="page-header mb-4">
        <div>
            <h4 class="mb-1">
                <i class="bi bi-megaphone text-primary"></i>
                {{ $campaign->name }} ({{ $campaign->season_year }})
            </h4>
            <div class="d-flex flex-wrap gap-2 align-items-center">
                <span
                    class="badge {{ $campaign->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $campaign->status === 'active' ? 'نشط' : 'مؤرشف' }}</span>
                @if($campaign->project)
                    <span class="text-muted">•</span>
                    <a href="{{ route('projects.show', $campaign->project) }}" class="text-decoration-none text-muted small">
                        <i class="bi bi-folder me-1"></i> {{ $campaign->project->name }}
                    </a>
                @endif
                <span class="text-muted">•</span>
                <span class="text-muted small">{{ $campaign->start_date?->format('Y-m-d') ?? '—' }} إلى
                    {{ $campaign->end_date?->format('Y-m-d') ?? '—' }}</span>
            </div>
        </div>
        <div class="btn-group">
            @if(\App\Models\ChangeRequest::where('model_type', \App\Models\Campaign::class)->where('model_id', $campaign->id)->where('status', 'pending')->exists())
                <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-3 py-1 rounded-pill small">
                    <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                </span>
            @else
                <a href="{{ route('campaigns.edit', $campaign) }}" class="btn btn-outline-primary"><i
                        class="bi bi-pencil me-1"></i> تعديل</a>
            @endif
            <a href="{{ route('campaigns.index') }}" class="btn btn-outline-secondary"><i
                    class="bi bi-arrow-right me-1"></i> عودة</a>
        </div>
    </div>

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="text-muted small">اجمالي التبرعات</div>
                <h3 class="fw-bold mb-0">{{ number_format($donationsTotal) }}</h3>
                <div class="small text-success mt-1">
                    <i class="bi bi-arrow-up"></i> {{ $donationsCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="text-muted small">اجمالي المصروفات</div>
                <h3 class="fw-bold mb-0">{{ number_format($expensesTotal) }}</h3>
                <div class="small text-danger mt-1">
                    <i class="bi bi-arrow-down"></i> {{ $expensesCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-primary bg-opacity-10 text-primary">
                    <i class="bi bi-wallet2"></i>
                </div>
                <div class="text-muted small">صافي الحسابات</div>
                <h3 class="fw-bold mb-0">{{ number_format($netBalance) }}</h3>
                <div class="small text-muted mt-1">
                    الرصيد الحالي
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-info bg-opacity-10 text-info">
                    <i class="bi bi-people"></i>
                </div>
                <div class="text-muted small">المستفيدون</div>
                <h3 class="fw-bold mb-0">{{ number_format($beneficiariesCount) }}</h3>
                <div class="small text-muted mt-1">
                    مستفيد مسجل
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Main Lists -->
        <div class="col-lg-8">

            @if(Str::contains($campaign->name, 'رمضان') || Str::contains($campaign->name, 'Ramadan'))
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <a href="{{ route('ramadan-bags.index') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm bg-primary text-white h-100" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded p-3 me-3 fs-3">
                                        <i class="bi bi-bag-heart"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">شنط رمضان</h5>
                                        <div class="small text-white-50">إدارة المستفيدين وتوزيع الشنط الرمضانية</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6">
                        <a href="{{ route('ramadan-iftars.index') }}" class="text-decoration-none">
                            <div class="card border-0 shadow-sm bg-success text-white h-100" style="transition: transform 0.2s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                                <div class="card-body d-flex align-items-center">
                                    <div class="bg-white bg-opacity-25 rounded p-3 me-3 fs-3">
                                        <i class="bi bi-cup-hot"></i>
                                    </div>
                                    <div>
                                        <h5 class="fw-bold mb-1">إفطارات رمضان</h5>
                                        <div class="small text-white-50">إدارة المستفيدين والوجبات وتوزيع الإفطارات</div>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            @endif

            <!-- Executive Structure (Ramadan 2026 Plan) -->
            @if(Str::contains($campaign->name, 'رمضان') || Str::contains($campaign->name, 'Ramadan'))
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="gh-section-title">
                            <span><i class="bi bi-diagram-3 text-purple me-2"></i> الهيكل التنفيذي (خطة رمضان 2026)</span>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse"
                                data-bs-target="#structureCollapse" aria-expanded="false" aria-controls="structureCollapse">
                                <i class="bi bi-chevron-down"></i>
                            </button>
                        </div>
                        <div class="collapse show" id="structureCollapse">
                            <div class="row g-4">
                                <!-- Top Management -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2">أولاً: الإدارة العليا</h6>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-primary border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark"><i
                                                        class="bi bi-building-check me-2 text-primary"></i>مدير المؤسسة</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>الموافقة النهائية على القرارات والخطط.</li>
                                                    <li>متابعة أداء مدير الحملة والتأكد من التزامه.</li>
                                                    <li>اعتماد الميزانيات والصرف.</li>
                                                    <li>متابعة التقارير العامة أسبوعيًا.</li>
                                                    <li>التدخل عند الحاجة في المشكلات الكبرى.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-primary border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark"><i
                                                        class="bi bi-person-workspace me-2 text-primary"></i>مدير الحملة</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>الإشراف العام على الحملة وقيادة الفرق.</li>
                                                    <li>المتابعة الميدانية واليومية خلال رمضان.</li>
                                                    <li>اتخاذ القرارات العاجلة.</li>
                                                    <li>مراجعة التقارير الميدانية والإدارية.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Team Leaders -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-success mb-3 border-bottom pb-2">ثانياً: قيادة الفرق</h6>
                                    <div class="p-3 bg-light rounded-3 border border-success border-opacity-10">
                                        <div class="fw-bold mb-2 text-dark"><i class="bi bi-people me-2 text-success"></i>تيم
                                            ليدرز</div>
                                        <ul class="small text-muted mb-0 ps-3">
                                            <li>إدارة المتطوعين يومياً وتشغيلهم.</li>
                                            <li>متابعة تنفيذ المهام الميدانية والإدارية.</li>
                                            <li>رفع تقرير يومي لمدير الحملة.</li>
                                        </ul>
                                    </div>
                                </div>

                                <!-- HR -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-info mb-3 border-bottom pb-2">ثالثاً: الموارد البشرية HR</h6>
                                    <div class="vstack gap-3">
                                        <div class="p-3 bg-light rounded-3 border border-info border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">مسؤول HR</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>اختيار وتسجيل المتطوعين.</li>
                                                <li>إعداد جداول العمل اليومية.</li>
                                                <li>متابعة الالتزام والانضباط وتقييم الأداء.</li>
                                            </ul>
                                        </div>
                                        <div class="p-3 bg-light rounded-3 border border-info border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">متابعين أونلاين</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>متابعة المتطوعين عبر الجروبات.</li>
                                                <li>تسجيل الحضور والمهام.</li>
                                                <li>رفع التقارير لمسؤول HR ومدير الحملة.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Logistics -->
                                <div class="col-md-6">
                                    <h6 class="fw-bold text-warning mb-3 border-bottom pb-2">رابعاً: قسم اللوجيستيك</h6>
                                    <div class="vstack gap-3">
                                        <div class="p-3 bg-light rounded-3 border border-warning border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">مسؤول لوجيستيك</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li><strong>قبل رمضان:</strong> تجهيز المخازن واستلام المواد.</li>
                                                <li><strong>داخل رمضان:</strong> تنظيم المخزون.</li>
                                            </ul>
                                        </div>
                                        <div class="p-3 bg-light rounded-3 border border-warning border-opacity-10">
                                            <div class="fw-bold mb-2 text-dark">مسؤولين لوجيستيك</div>
                                            <ul class="small text-muted mb-0 ps-3">
                                                <li>متابعة التعبئة اليومية والإشراف على التوزيعات.</li>
                                                <li>التنسيق اليومي مع بقية الأقسام.</li>
                                                <li>رفع تقرير يومي.</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- Research -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-danger mb-3 border-bottom pb-2">خامساً: قسم الأبحاث والاعتماد</h6>
                                    <div class="row g-3">
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-danger border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark">مسؤولي اعتماد</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>مراجعة الأبحاث.</li>
                                                    <li>مناقشة الحالات واعتمادها.</li>
                                                    <li>تحديد أولويات الاستحقاق.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-danger border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark">مسؤول الأبحاث</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>إدارة فريق الباحثين وتوزيع المهام.</li>
                                                    <li>مراجعة البيانات قبل الاعتماد.</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="p-3 bg-light rounded-3 h-100 border border-danger border-opacity-10">
                                                <div class="fw-bold mb-2 text-dark">فريق الباحثين</div>
                                                <ul class="small text-muted mb-0 ps-3">
                                                    <li>جمع بيانات الأسر.</li>
                                                    <li>الزيارات الميدانية.</li>
                                                    <li>رفع نتائج البحث لمسؤول الأبحاث.</li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Accounts -->
                                <div class="col-12">
                                    <h6 class="fw-bold text-dark mb-3 border-bottom pb-2">سادساً: الحسابات</h6>
                                    <div class="p-3 bg-light rounded-3 border border-secondary border-opacity-10">
                                        <div class="d-flex justify-content-between flex-wrap gap-4">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>تسجيل التبرعات اليومية</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>مراجعة المصروفات اليومية</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-check-circle-fill text-success me-2"></i>
                                                <span>متابعة الميزانيات</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Generic Beneficiaries Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-people-fill text-primary me-2"></i> ملف المستفيدين</span>
                        <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">إضافة مستفيد</button>
                    </div>
                    
                    <div class="collapse mb-4" id="beneficiaryForm">
                        <form method="POST" action="{{ route('campaigns.storeBeneficiaryFile', $campaign) }}" class="bg-body-tertiary p-3 rounded shadow-sm">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small">اسم المستفيد</label>
                                    <input type="text" name="full_name" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">رقم التلفون</label>
                                    <input type="text" name="phone" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">المساعدة</label>
                                    <input type="text" name="assistance_type" class="form-control form-control-sm">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">ملاحظات</label>
                                    <input type="text" name="notes" class="form-control form-control-sm">
                                </div>
                                <div class="col-12 text-end">
                                    <button class="btn btn-primary btn-sm px-4">حفظ المستفيد</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="table-responsive">
                        @php $campaignBeneficiaries = $campaign->beneficiaries()->orderByDesc('id')->limit(50)->get(); @endphp
                        <table class="table table-sm table-hover align-middle small">
                            <thead class="bg-body-tertiary">
                                <tr>
                                    <th>الاسم</th>
                                    <th>الهاتف</th>
                                    <th>المساعدة</th>
                                    <th>ملاحظات</th>
                                    <th style="width: 80px">إجراء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($campaignBeneficiaries as $ben)
                                <tr>
                                    <td>{{ $ben->full_name }}</td>
                                    <td>{{ $ben->phone ?? '—' }}</td>
                                    <td>{{ $ben->assistance_type ?? '—' }}</td>
                                    <td>{{ $ben->notes ?? '—' }}</td>
                                    <td>
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-link text-primary p-0" type="button" data-bs-toggle="modal" data-bs-target="#editBen{{ $ben->id }}">
                                                <i class="bi bi-pencil"></i>
                                            </button>

                                            <form action="{{ route('campaigns.destroyBeneficiaryFile', [$campaign, $ben]) }}" method="POST" onsubmit="return confirm('حذف المستفيد؟')" class="d-inline">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr><td colspan="5" class="text-center py-3 text-muted">لا يوجد مستفيدين مسجلين لهذه الحملة</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Edit Modals -->
                    @foreach($campaignBeneficiaries ?? [] as $ben)
                    <div class="modal fade" id="editBen{{ $ben->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">تعديل المستفيد</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <form method="POST" action="{{ route('campaigns.updateBeneficiaryFile', [$campaign, $ben]) }}">
                                    @csrf @method('PUT')
                                    <div class="modal-body text-start">
                                        <div class="mb-2">
                                            <label class="form-label small">الاسم</label>
                                            <input type="text" name="full_name" class="form-control form-control-sm" value="{{ $ben->full_name }}" required>
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">الهاتف</label>
                                            <input type="text" name="phone" class="form-control form-control-sm" value="{{ $ben->phone }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">المساعدة</label>
                                            <input type="text" name="assistance_type" class="form-control form-control-sm" value="{{ $ben->assistance_type }}">
                                        </div>
                                        <div class="mb-2">
                                            <label class="form-label small">ملاحظات</label>
                                            <textarea name="notes" class="form-control form-control-sm">{{ $ben->notes }}</textarea>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">إلغاء</button>
                                        <button type="submit" class="btn btn-primary btn-sm">حفظ</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        <!-- Right Column: Manager, Stats, Volunteers -->
        <div class="col-lg-4">

            <!-- Manager Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">مدير الحملة</div>
                    @if($campaign->manager)
                        <div class="mb-3">
                            @if($campaign->manager_photo_url)
                                <img src="{{ $campaign->manager_photo_url }}" class="rounded-circle mb-2"
                                    style="width:80px;height:80px;object-fit:cover">
                            @else
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                    style="width:80px;height:80px;font-size:2rem">
                                    {{ mb_substr($campaign->manager->name, 0, 1) }}
                                </div>
                            @endif
                            <h5 class="fw-bold mb-0">{{ $campaign->manager->name }}</h5>
                            <div class="text-muted small">{{ $campaign->manager->email }}</div>
                        </div>
                        <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal"
                            data-bs-target="#managerModal">تغيير المدير</button>
                    @else
                        <div class="text-muted mb-3">لم يتم تعيين مدير بعد</div>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#managerModal">تعيين
                            مدير</button>
                    @endif
                </div>
            </div>

            <!-- Donation Details (Chart) -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">تفصيل التبرعات</div>
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>نقدي ({{ $cashPct }}%)</span>
                                <span>{{ number_format($cashSum) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $cashPct }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>عيني ({{ 100 - $cashPct }}%)</span>
                                <span>{{ number_format($inKindSum) }}</span>
                            </div>
                            <div class="progress" style="height: 8px;">
                                <div class="progress-bar bg-info" role="progressbar" style="width: {{ 100 - $cashPct }}%">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Monthly Volunteers -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو الشهر</span>
                        <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal"
                            data-bs-target="#monthlyVolunteerModal"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($monthlyVolunteers as $mv)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $mv->user->name }}</div>
                                    <div class="small text-muted">{{ $mv->month }}/{{ $mv->year }}</div>
                                </div>
                                <form action="{{ route('campaigns.destroyMonthlyVolunteer', [$campaign, $mv]) }}" method="POST"
                                    onsubmit="return confirm('حذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين لهذا الشهر</div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Campaign Volunteers -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو الحملة</span>
                        <button class="btn btn-sm btn-primary rounded-circle" data-bs-toggle="modal"
                            data-bs-target="#volunteerModal"><i class="bi bi-plus"></i></button>
                    </div>
                    <div class="list-group list-group-flush">
                        @forelse($campaignVolunteers as $v)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $v->name }}</div>
                                    <div class="small text-muted">{{ $v->pivot->role ?? 'متطوع' }}</div>
                                </div>
                                <form action="{{ route('campaigns.detachVolunteer', [$campaign, $v]) }}" method="POST"
                                    onsubmit="return confirm('إزالة؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين مسجلين</div>
                        @endforelse
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Manager Modal -->
    <div class="modal fade" id="managerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.setManager', $campaign) }}" method="POST"
                enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">تعيين مدير الحملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المدير</label>
                        <select name="manager_user_id" class="form-select">
                            <option value="">اختر مستخدم...</option>
                            @foreach(\App\Models\User::orderBy('name')->get() as $u)
                                <option value="{{ $u->id }}" @selected($campaign->manager_user_id == $u->id)>{{ $u->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صورة المدير (اختياري)</label>
                        <input type="file" name="manager_photo" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Volunteer Modal -->
    <div class="modal fade" id="volunteerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.attachVolunteer', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة متطوع للحملة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتطوع</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر متطوع...</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الدور</label>
                        <input type="text" name="role" class="form-control" placeholder="مثال: مشرف، مساعد...">
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">ساعات</label>
                            <input type="number" step="0.5" name="hours" class="form-control" placeholder="0">
                        </div>
                        <div class="col-6">
                            <label class="form-label">تاريخ البدء</label>
                            <input type="date" name="started_at" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Daily Menu Modal -->
    <div class="modal fade" id="dailyMenuModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.storeDailyMenu', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة قائمة إطعام</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">التاريخ</label>
                            <input type="date" name="day_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label">المسؤول</label>
                            <select name="responsible_user_id" class="form-select">
                                <option value="">اختر مسؤول...</option>
                                @foreach($users as $u)
                                    <option value="{{ $u->id }}">{{ $u->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label">نوع الوجبة</label>
                            <input list="mealTypeOptions" name="meal_type" class="form-control"
                                placeholder="اختر أو اكتب...">
                            <datalist id="mealTypeOptions">
                                <option value="إفطار">
                                <option value="سحور">
                                <option value="عشاء">
                                <option value="بروتين">
                                <option value="نشويات">
                                <option value="خضار">
                            </datalist>
                        </div>
                        <div class="col-6">
                            <label class="form-label">العدد</label>
                            <input type="number" name="meal_count" class="form-control" value="0" min="0">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">محتوى الوجبة (المنيو)</label>
                        <input type="text" name="menu" class="form-control" placeholder="مثال: أرز ودجاج...">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">الكميات / المكونات</label>
                        <textarea name="ingredients" class="form-control" rows="3"
                            placeholder="تفاصيل الكميات والمقادير..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Monthly Volunteer Modal -->
    <div class="modal fade" id="monthlyVolunteerModal" tabindex="-1">
        <div class="modal-dialog">
            <form class="modal-content" action="{{ route('campaigns.storeMonthlyVolunteer', $campaign) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">إضافة متطوع للشهر</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">المتطوع</label>
                        <select name="user_id" class="form-select" required>
                            <option value="">اختر متطوع...</option>
                            @foreach($volunteers as $v)
                                <option value="{{ $v->id }}">{{ $v->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="row g-2">
                        <div class="col-6 mb-3">
                            <label class="form-label">الشهر</label>
                            <select name="month" class="form-select">
                                @foreach(range(1, 12) as $m)
                                    <option value="{{ $m }}" @selected($m == date('n'))>{{ $m }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-6 mb-3">
                            <label class="form-label">السنة</label>
                            <select name="year" class="form-select">
                                @foreach(range(date('Y') - 1, date('Y') + 1) as $y)
                                    <option value="{{ $y }}" @selected($y == date('Y'))>{{ $y }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </div>
            </form>
        </div>
    </div>
@endsection