@extends('layouts.app')
@section('content')
    <style>
        .gh-metric-card {
            background: #fff;
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

        .hover-shadow:hover {
            box-shadow: 0 .5rem 1rem rgba(0, 0, 0, .15) !important;
            transform: translateY(-2px);
        }

        .transition {
            transition: all 0.3s ease;
        }

        .theme-dark .gh-metric-card {
            background: var(--card-bg);
            color: var(--text);
        }
          /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
      /* --- LIGHT MODE ADAPTATION --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .member-card-premium {
          background: var(--ws-bg-card);
          border-color: var(--ws-border-card);
          box-shadow: 0 10px 25px rgba(0,0,0,0.05);
      }
      body:not(.theme-dark) .text-white,
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white,
      body:not(.theme-dark) .premium-hero-sleek .text-white-50 {
          color: #fff !important;
      }
      body:not(.theme-dark) .role-pill-premium {
          color: var(--blue-dark);
          background: rgba(59,130,246,0.15);
          border-color: rgba(59,130,246,0.2);
      }
      body:not(.theme-dark) .text-slate-400 {
          color: var(--ws-text-secondary);
      }
      body:not(.theme-dark) .btn-glass-blue {
          color: var(--blue-dark);
          background: rgba(37, 99, 235, 0.1);
          border-color: rgba(37, 99, 235, 0.2);
      }
      body:not(.theme-dark) .btn-glass-danger {
          color: #dc2626;
          background: rgba(220, 38, 38, 0.1);
          border-color: rgba(220, 38, 38, 0.2);
      }
      body:not(.theme-dark) .premium-modal-dark {
          background: var(--ws-bg-card);
      }
      body:not(.theme-dark) .premium-modal-dark .modal-header .text-white {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .field-lux {
          background: var(--ws-bg-input);
          color: var(--ws-text-primary);
          border-color: var(--ws-border);
      }
      body:not(.theme-dark) .field-lux:focus {
          background: var(--ws-bg-input);
      }
      body:not(.theme-dark) .avatar-placeholder-premium {
          color: #fff; /* Keep placeholder icon white because of gradient */
      }
      body:not(.theme-dark) .btn-close-white {
          filter: invert(1) grayscale(100%) brightness(200%);
      }
</style>

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                style="width:64px;height:64px">
                <i class="bi bi-kanban" style="font-size:1.6rem"></i>
            </div>
            <div>
                <h4 class="mb-1 fw-bold">{{ $project->name }}</h4>
                <div class="d-flex align-items-center gap-2">
                    <span
                        class="badge {{ $project->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $project->status === 'active' ? 'نشط' : 'مؤرشف' }}</span>
                    <span
                        class="badge {{ $project->fixed ? 'bg-info' : 'bg-secondary-subtle' }}">{{ $project->fixed ? 'ثابت' : 'غير ثابت' }}</span>
                </div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <a class="btn btn-outline-primary" href="{{ route('projects.edit', $project) }}"><i class="bi bi-pencil"></i>
                تعديل</a>
            <a href="{{ route('projects.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-right"></i>
                عودة</a>
        </div>
    </div>

    <div class="text-muted mb-4">{{ $project->description }}</div>

    <!-- Metrics Row -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-success bg-opacity-10 text-success">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="text-muted small">اجمالي التبرعات</div>
                <h3 class="fw-bold mb-0">{{ number_format($donationsTotal - $activitiesRevenue, 2) }}</h3>
                <div class="small text-success mt-1">
                    <i class="bi bi-arrow-up"></i> {{ $donationsCount }} عملية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-warning bg-opacity-10 text-warning">
                    <i class="bi bi-graph-up-arrow"></i>
                </div>
                <div class="text-muted small">إيرادات الأنشطة</div>
                <h3 class="fw-bold mb-0">{{ number_format($activitiesRevenue, 2) }}</h3>
                <div class="small text-warning mt-1">
                    من المعارض والدعاية
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="gh-metric-card">
                <div class="gh-metric-icon bg-danger bg-opacity-10 text-danger">
                    <i class="bi bi-cart"></i>
                </div>
                <div class="text-muted small">اجمالي المصروفات</div>
                <h3 class="fw-bold mb-0">{{ number_format($expensesTotal, 2) }}</h3>
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
                <h3 class="fw-bold mb-0 {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
                    {{ number_format($netBalance, 2) }}</h3>
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
                <h3 class="fw-bold mb-0">{{ $beneficiariesCount }}</h3>
                <div class="small text-muted mt-1">
                    مستفيد مسجل
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left Column: Main Content -->
        <div class="col-lg-8">

            <!-- Campaigns Section -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span><i class="bi bi-flag text-primary me-2"></i> حملات المشروع</span>
                        <a href="{{ route('campaigns.create') }}" class="btn btn-sm btn-outline-primary">إضافة حملة</a>
                    </div>
                    @if($project->campaigns->isEmpty())
                        <div class="text-muted small text-center py-3">لا توجد حملات مرتبطة بهذا المشروع.</div>
                    @else
                        <div class="row g-3">
                            @foreach($project->campaigns as $pc)
                                <div class="col-md-6">
                                    <div class="card h-100 shadow-sm border-0 bg-body-tertiary">
                                        <div class="card-body p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h6 class="fw-bold mb-0">
                                                    <a href="{{ route('campaigns.show', $pc) }}"
                                                        class="text-decoration-none text-body">
                                                        {{ $pc->name }} <span
                                                            class="text-muted small">({{ $pc->season_year }})</span>
                                                    </a>
                                                </h6>
                                                <span
                                                    class="badge {{ $pc->status == 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $pc->status == 'active' ? 'نشط' : 'مؤرشف' }}</span>
                                            </div>
                                            <div class="small text-muted mb-3">
                                                من {{ $pc->start_date?->format('Y-m-d') ?? '—' }} إلى
                                                {{ $pc->end_date?->format('Y-m-d') ?? '—' }}
                                            </div>
                                            <div class="d-flex gap-2 justify-content-end border-top pt-2 mt-auto">
                                                <form method="POST" action="{{ route('campaigns.destroy', $pc) }}"
                                                    onsubmit="return confirm('حذف الحملة؟');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-link text-danger btn-sm p-0">حذف</button>
                                                </form>
                                                <span class="text-muted">|</span>
                                                <a href="{{ route('campaigns.edit', $pc) }}"
                                                    class="btn btn-link text-secondary btn-sm p-0">تعديل</a>
                                                <span class="text-muted">|</span>
                                                <a href="{{ route('campaigns.show', $pc) }}"
                                                    class="btn btn-link text-primary btn-sm p-0">عرض</a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>



            @if($project->name === 'مشروع زاد')
                <!-- Zad Families Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="gh-section-title">
                            <span><i class="bi bi-people-fill text-primary me-2"></i> ملف أهالي زاد</span>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#zadFamilyForm">إضافة حالة</button>
                        </div>
                        
                        <div class="collapse mb-4" id="zadFamilyForm">
                            <form method="POST" action="{{ route('projects.storeZadFamily', $project) }}" class="bg-body-tertiary p-3 rounded shadow-sm">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small">اسم الام</label>
                                        <input type="text" name="mother_name" class="form-control form-control-sm" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small">رقم التلفون</label>
                                        <input type="text" name="phone" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small">اسماء الاطفال</label>
                                        <textarea name="children_names" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">رقم احتياطي</label>
                                        <input type="text" name="backup_phone" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">عدد الاطفال</label>
                                        <input type="number" name="children_count" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">المكفولة</label>
                                        <input type="number" name="sponsored_children_count" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label small">الصف الدراسي</label>
                                        <input type="text" name="study_grade" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">فرخة/بطة</label>
                                        <input type="text" name="poultry_type" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label small">لحمة</label>
                                        <input type="text" name="meat" class="form-control form-control-sm">
                                    </div>
                                    <div class="col-md-12">
                                        <label class="form-label small">ملاحظات</label>
                                        <textarea name="notes_cases" class="form-control form-control-sm" rows="2"></textarea>
                                    </div>
                                    <div class="col-12 text-end">
                                        <button class="btn btn-primary btn-sm px-4">حفظ الحالة</button>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            @php $zadFamilies = $project->beneficiaries()->whereNotNull('mother_name')->orderByDesc('id')->limit(50)->get(); @endphp
                            <table class="table table-sm table-hover align-middle small">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th>الام</th>
                                        <th>اسماء الاطفال</th>
                                        <th>الهاتف</th>
                                        <th>عدد الاطفال</th>
                                        <th>المكفولة</th>
                                        <th>الدراسة</th>
                                        <th>فرخة/بطة</th>
                                        <th>لحمة</th>
                                        <th>اجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($zadFamilies as $family)
                                    <tr>
                                        <td>{{ $family->mother_name }}</td>
                                        <td><span class="text-truncate d-inline-block" style="max-width: 150px;" title="{{ $family->children_names }}">{{ $family->children_names }}</span></td>
                                        <td>{{ $family->phone }}</td>
                                        <td class="text-center">{{ $family->children_count }}</td>
                                        <td class="text-center">{{ $family->sponsored_children_count }}</td>
                                        <td>{{ $family->study_grade }}</td>
                                        <td>{{ $family->poultry_type }}</td>
                                        <td>{{ $family->meat }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <!-- Edit Button -->
                                                <button class="btn btn-link text-primary p-0" type="button" data-bs-toggle="modal" data-bs-target="#editZadFamily{{ $family->id }}">
                                                    <i class="bi bi-pencil"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <form action="{{ route('projects.destroyZadFamily', [$project, $family]) }}" method="POST" onsubmit="return confirm('حذف؟')" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="9" class="text-center py-3 text-muted">لا توجد سجلات في ملف الأهالي</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Edit Modals -->
                        @foreach($zadFamilies ?? [] as $family)
                        <div class="modal fade" id="editZadFamily{{ $family->id }}" tabindex="-1">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">تعديل حالة: {{ $family->mother_name }}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('projects.updateZadFamily', [$project, $family]) }}">
                                        @csrf @method('PUT')
                                        <div class="modal-body text-start">
                                            <div class="row g-3">
                                                <div class="col-md-6">
                                                    <label class="form-label small">اسم الام</label>
                                                    <input type="text" name="mother_name" class="form-control" value="{{ $family->mother_name }}" required>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small">رقم التلفون</label>
                                                    <input type="text" name="phone" class="form-control" value="{{ $family->phone }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small">اسماء الاطفال</label>
                                                    <textarea name="children_names" class="form-control" rows="2">{{ $family->children_names }}</textarea>
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">رقم احتياطي</label>
                                                    <input type="text" name="backup_phone" class="form-control" value="{{ $family->backup_phone }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">عدد الاطفال</label>
                                                    <input type="number" name="children_count" class="form-control" value="{{ $family->children_count }}">
                                                </div>
                                                <div class="col-md-4">
                                                    <label class="form-label small">المكفولة</label>
                                                    <input type="number" name="sponsored_children_count" class="form-control" value="{{ $family->sponsored_children_count }}">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label small">الصف الدراسي</label>
                                                    <input type="text" name="study_grade" class="form-control" value="{{ $family->study_grade }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">فرخة/بطة</label>
                                                    <input type="text" name="poultry_type" class="form-control" value="{{ $family->poultry_type }}">
                                                </div>
                                                <div class="col-md-3">
                                                    <label class="form-label small">لحمة</label>
                                                    <input type="text" name="meat" class="form-control" value="{{ $family->meat }}">
                                                </div>
                                                <div class="col-md-12">
                                                    <label class="form-label small">ملاحظات</label>
                                                    <textarea name="notes_cases" class="form-control" rows="2">{{ $family->notes_cases }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary">حفظ التغييرات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            @else
                <!-- Generic Beneficiaries Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="gh-section-title">
                            <span><i class="bi bi-people-fill text-primary me-2"></i> ملف المستفيدين</span>
                            <button class="btn btn-sm btn-outline-primary" type="button" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">إضافة مستفيد</button>
                        </div>
                        
                        <div class="collapse mb-4" id="beneficiaryForm">
                            <form method="POST" action="{{ route('projects.storeBeneficiaryFile', $project) }}" class="bg-body-tertiary p-3 rounded shadow-sm">
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
                            @php $genericBeneficiaries = $project->beneficiaries()->whereNull('mother_name')->orderByDesc('id')->limit(50)->get(); @endphp
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
                                    @forelse($genericBeneficiaries as $ben)
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

                                                <form action="{{ route('projects.destroyBeneficiaryFile', [$project, $ben]) }}" method="POST" onsubmit="return confirm('حذف المستفيد؟')" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="5" class="text-center py-3 text-muted">لا يوجد مستفيدين مسجلين لهذا المشروع</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Edit Modals -->
                        @foreach($genericBeneficiaries ?? [] as $ben)
                        <div class="modal fade" id="editBen{{ $ben->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title">تعديل المستفيد</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <form method="POST" action="{{ route('projects.updateBeneficiaryFile', [$project, $ben]) }}">
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
            @endif

            @if($project->name === 'مشروع كسوة')
                <!-- Weekly Activities Section -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body">
                        <div class="gh-section-title">
                            <span><i class="bi bi-calendar-week text-info me-2"></i> الأنشطة الأسبوعية</span>
                            <div class="badge bg-success">إيرادات المعارض: {{ number_format($exhibitionsRevenue, 2) }}</div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Exhibitions -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-body-tertiary">
                                    <div class="card-header bg-transparent fw-bold text-center small">المعارض (الجمعة)</div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        @if($exhibitions->isEmpty())
                                            <div class="text-center text-muted small">لا توجد معارض</div>
                                        @else
                                            <ul class="list-group list-group-flush small bg-transparent">
                                                @foreach($exhibitions as $activity)
                                                    <li
                                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-1">
                                                        <div>
                                                            <div class="fw-bold">{{ $activity->activity_date->format('Y-m-d') }}</div>
                                                            <div class="text-muted" style="font-size: 0.8em">
                                                                {{ $activity->location ?? '—' }}</div>
                                                            <div class="text-muted" style="font-size: 0.8em">
                                                                {{ $activity->description }}</div>
                                                        </div>
                                                        <div class="text-end">
                                                            <div class="fw-bold text-success">{{ number_format($activity->revenue, 2) }}
                                                            </div>
                                                            <form method="POST"
                                                                action="{{ route('projects.destroyActivity', ['project' => $project->id, 'activity' => $activity->id]) }}"
                                                                class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-link text-danger p-0" style="font-size: 0.7rem"
                                                                    onclick="return confirm('هل أنت متأكد؟')">×</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Advertising -->
                            <div class="col-md-6">
                                <div class="card h-100 border-0 bg-body-tertiary">
                                    <div class="card-header bg-transparent fw-bold text-center small">الدعايا الأسبوعية</div>
                                    <div class="card-body p-2" style="max-height: 200px; overflow-y: auto;">
                                        @if($advertisingDays->isEmpty())
                                            <div class="text-center text-muted small">لا توجد بيانات</div>
                                        @else
                                            <ul class="list-group list-group-flush small bg-transparent">
                                                @foreach($advertisingDays as $activity)
                                                    <li
                                                        class="list-group-item bg-transparent d-flex justify-content-between align-items-center px-0 py-1">
                                                        <div>
                                                            <div class="fw-bold">{{ $activity->activity_date->format('Y-m-d') }}</div>
                                                            @if($activity->location)
                                                                <div class="text-muted" style="font-size: 0.8em">{{ $activity->location }}
                                                                </div>
                                                            @endif
                                                            <div class="text-muted" style="font-size: 0.8em">
                                                                {{ $activity->description }}</div>
                                                        </div>
                                                        <div class="text-end">
                                                            @if($activity->revenue > 0)
                                                                <div class="fw-bold text-success">{{ number_format($activity->revenue, 2) }}
                                                                </div>
                                                            @endif
                                                            <form method="POST"
                                                                action="{{ route('projects.destroyActivity', ['project' => $project->id, 'activity' => $activity->id]) }}"
                                                                class="d-inline">
                                                                @csrf @method('DELETE')
                                                                <button class="btn btn-link text-danger p-0" style="font-size: 0.7rem"
                                                                    onclick="return confirm('هل أنت متأكد؟')">×</button>
                                                            </form>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        <form method="POST" action="{{ route('projects.storeActivity', $project) }}">
                            @csrf
                            <div class="row g-2 align-items-end bg-body-tertiary p-3 rounded">
                                <div class="col-md-12 mb-2">
                                    <div class="fw-bold small">إضافة نشاط جديد</div>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">النوع</label>
                                    <select name="type" class="form-select form-select-sm" id="activityType"
                                        onchange="toggleRevenue()">
                                        <option value="exhibition">معرض</option>
                                        <option value="advertising">دعاية</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">التاريخ</label>
                                    <input type="date" name="activity_date" class="form-control form-control-sm"
                                        value="{{ date('Y-m-d') }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small">المكان/الوصف</label>
                                    <input type="text" name="location" class="form-control form-control-sm"
                                        placeholder="المكان">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small">الإيراد</label>
                                    <input type="number" step="0.01" name="revenue" class="form-control form-control-sm"
                                        placeholder="0">
                                </div>
                                <div class="col-md-2">
                                    <button class="btn btn-primary btn-sm w-100">إضافة</button>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <input type="text" name="description" class="form-control form-control-sm"
                                        placeholder="ملاحظات إضافية">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Column: Sidebar -->
        <div class="col-lg-4">

            <!-- Manager Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">مدير المشروع</div>
                    <div class="mb-3">
                        @if($project->manager_photo_url)
                            <img src="{{ $project->manager_photo_url }}" class="rounded-circle mb-2"
                                style="width:80px;height:80px;object-fit:cover">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                style="width:80px;height:80px;font-size:2rem">
                                {{ mb_substr($project->manager?->name ?? '?', 0, 1) }}
                            </div>
                        @endif
                        <h5 class="fw-bold mb-0">{{ $project->manager?->name ?? '—' }}</h5>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#managerForm">تغيير المدير</button>
                    <div class="collapse mt-3" id="managerForm">
                        <form method="POST" action="{{ route('projects.setManager', $project) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <select name="manager_user_id" class="form-select form-select-sm mb-2">
                                <option value="">—</option>
                                @foreach($volunteers as $v)
                                    <option value="{{ $v->id }}" @selected($project->manager_user_id == $v->id)>{{ $v->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="file" name="manager_photo" class="form-control form-control-sm mb-2"
                                accept="image/*">
                            <button class="btn btn-sm btn-primary w-100">حفظ</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Deputy Card -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body text-center p-4">
                    <div class="gh-section-title justify-content-center">نائب المدير</div>
                    <div class="mb-3">
                        @if($project->deputy_photo_url)
                            <img src="{{ $project->deputy_photo_url }}" class="rounded-circle mb-2"
                                style="width:80px;height:80px;object-fit:cover">
                        @else
                            <div class="rounded-circle bg-info text-white d-flex align-items-center justify-content-center mx-auto mb-2"
                                style="width:80px;height:80px;font-size:2rem">
                                {{ mb_substr($project->deputy?->name ?? '?', 0, 1) }}
                            </div>
                        @endif
                        <h5 class="fw-bold mb-0">{{ $project->deputy?->name ?? '—' }}</h5>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse"
                        data-bs-target="#deputyForm">تغيير النائب</button>
                    <div class="collapse mt-3" id="deputyForm">
                        <form method="POST" action="{{ route('projects.setDeputy', $project) }}"
                            enctype="multipart/form-data">
                            @csrf
                            <select name="deputy_user_id" class="form-select form-select-sm mb-2">
                                <option value="">—</option>
                                @foreach($volunteers as $v)
                                    <option value="{{ $v->id }}" @selected($project->deputy_user_id == $v->id)>{{ $v->name }}
                                    </option>
                                @endforeach
                            </select>
                            <input type="file" name="deputy_photo" class="form-control form-control-sm mb-2"
                                accept="image/*">
                            <button class="btn btn-sm btn-primary w-100">حفظ</button>
                        </form>
                    </div>
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
                    </div>
                    <div class="list-group list-group-flush mb-3">
                        @forelse($monthlyVolunteers as $mv)
                            <div class="list-group-item px-0 d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">{{ $mv->user->name }}</div>
                                    <div class="small text-muted">{{ $mv->month }}/{{ $mv->year }}</div>
                                </div>
                                <form action="{{ route('projects.destroyMonthlyVolunteer', [$project, $mv]) }}" method="POST"
                                    onsubmit="return confirm('حذف؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-circle"></i></button>
                                </form>
                            </div>
                        @empty
                            <div class="text-center text-muted small py-2">لا يوجد متطوعين لهذا الشهر</div>
                        @endforelse
                    </div>

                    <form method="POST" action="{{ route('projects.storeMonthlyVolunteer', $project) }}"
                        class="bg-body-tertiary p-2 rounded">
                        @csrf
                        <div class="mb-2">
                            <select name="user_id" class="form-select form-select-sm mb-1" required>
                                <option value="">اختر متطوع...</option>
                                @foreach($volunteers as $v)
                                    <option value="{{ $v->id }}">{{ $v->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="row g-1 mb-2">
                            <div class="col-6">
                                <select name="month" class="form-select form-select-sm">
                                    @for($m = 1; $m <= 12; $m++)
                                    <option value="{{ $m }}" @selected($m == date('n'))>{{ $m }}</option> @endfor
                                </select>
                            </div>
                            <div class="col-6">
                                <input type="number" name="year" class="form-control form-control-sm"
                                    value="{{ date('Y') }}">
                            </div>
                        </div>
                        <input type="text" name="notes" class="form-control form-control-sm mb-2" placeholder="ملاحظات">
                        <button class="btn btn-sm btn-primary w-100">إضافة</button>
                    </form>
                </div>
            </div>

            <!-- Project Volunteers -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="gh-section-title">
                        <span>متطوعو المشروع</span>
                        <span class="badge bg-secondary">{{ count($projectVolunteers) }}</span>
                    </div>

                    <div class="d-flex flex-column gap-2 mb-3">
                        @foreach($projectVolunteers as $pv)
                            <div
                                class="d-flex align-items-center justify-content-between p-2 border rounded hover-shadow transition">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center"
                                        style="width:32px;height:32px;font-weight:bold;font-size:0.8rem">
                                        {{ mb_substr($pv->name, 0, 1) }}
                                    </div>
                                    <div style="line-height:1.1">
                                        <div class="fw-bold small">{{ $pv->name }}</div>
                                        <div class="text-muted" style="font-size:0.7rem">{{ $pv->pivot->role ?? 'متطوع' }}</div>
                                    </div>
                                </div>
                                <form method="POST"
                                    action="{{ route('projects.detachVolunteer', ['project' => $project->id, 'user' => $pv->id]) }}">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-link text-danger p-0 btn-sm"><i class="bi bi-trash"></i></button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    <form method="POST" action="{{ route('projects.attachVolunteer', $project) }}"
                        class="bg-body-tertiary p-2 rounded">
                        @csrf
                        <div class="mb-2">
                            <select name="user_id" class="form-select form-select-sm mb-1">
                                <option value="">اختر متطوع...</option>
                                @foreach($volunteers as $v)
                                    @if(!$projectVolunteers->contains('id', $v->id))
                                        <option value="{{ $v->id }}">{{ $v->name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <input type="text" name="role" class="form-control form-control-sm" placeholder="الدور">
                        </div>
                        <div class="row g-1 mb-2">
                            <div class="col-6">
                                <input type="number" step="0.5" name="hours" class="form-control form-control-sm"
                                    placeholder="ساعات">
                            </div>
                            <div class="col-6">
                                <input type="date" name="started_at" class="form-control form-control-sm"
                                    value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <button class="btn btn-sm btn-primary w-100">إضافة متطوع</button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection

