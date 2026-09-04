@extends('layouts.app')
@section('styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
    <style>
        #beneficiaryForm .select2-container { width: 100% !important; direction: rtl; }
        #beneficiaryForm .select2-container--bootstrap-5 .select2-selection--multiple { min-height: 42px; }
        #beneficiaryForm .select2-container--bootstrap-5 .select2-selection__choice {
            background: var(--primary) !important;
            color: #fff !important;
            border: 0 !important;
        }
    </style>
@endsection
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
        <div class="d-flex flex-wrap gap-2">
            @if(request()->user()?->hasPermission('donations.view'))
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#projectDonationModal">
                    <i class="bi bi-cash-coin"></i> إضافة تبرع
                </button>
            @endif
            @if(request()->user()?->hasPermission('manage_finance') && request()->user()?->hasPermission('expenses.view'))
                <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#projectExpenseModal">
                    <i class="bi bi-receipt"></i> إضافة مصروف
                </button>
            @endif
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
                <h3 class="fw-bold mb-0">{{ number_format($donationsTotal, 2) }}</h3>
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
                        
                        <div @class(['collapse mb-4', 'show' => old('form_context') === 'project_beneficiary']) id="beneficiaryForm">
                            <form method="POST" action="{{ route('projects.storeBeneficiaryFile', $project) }}" class="bg-body-tertiary p-4 rounded shadow-sm">
                                @csrf
                                <input type="hidden" name="form_context" value="project_beneficiary">

                                <div class="form-section mb-4">
                                    <div class="form-section-title">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <span>البيانات الشخصية</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label">كود المستفيد</label>
                                            <input name="code" class="form-control @error('code') is-invalid @enderror"
                                                value="{{ old('code') }}"
                                                placeholder="مثال: A-101 (يترك فارغاً للتوليد التلقائي)">
                                            <div class="form-help-text">يترك فارغاً وسيتم إنشاؤه تلقائياً</div>
                                            @error('code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label form-label-required">الاسم الكامل</label>
                                            <input name="full_name" class="form-control @error('full_name') is-invalid @enderror"
                                                value="{{ old('full_name') }}" required placeholder="أدخل الاسم بالعربية">
                                            @error('full_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">الرقم القومي</label>
                                            <input name="national_id" class="form-control @error('national_id') is-invalid @enderror"
                                                value="{{ old('national_id') }}"
                                                pattern="^[23]\d{2}(0[1-9]|1[0-2])(0[1-9]|[12]\d|3[01])\d{7}$"
                                                title="الرقم القومي 14 رقم بصيغة صحيحة" placeholder="14 رقم">
                                            <div class="form-help-text">الرقم القومي المصري المكون من 14 رقم</div>
                                            @error('national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">رقم الهاتف</label>
                                            <input name="phone" class="form-control @error('phone') is-invalid @enderror"
                                                value="{{ old('phone') }}" pattern="^(01[0125]\d{8}|\+?201[0125]\d{8})$"
                                                title="ابدأ بـ 010 أو 011 أو 012 أو 015" placeholder="01xxxxxxxxx">
                                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label">العنوان</label>
                                            <input name="address" class="form-control @error('address') is-invalid @enderror"
                                                value="{{ old('address') }}" placeholder="العنوان بالتفصيل">
                                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                @php
                                    $selectedAllocatedBeneficiaries = array_map('intval', old('allocated_beneficiary_ids', []));
                                    $selectedSponsors = array_map('intval', old('sponsor_ids', []));
                                @endphp
                                <div class="form-section mb-4">
                                    <div class="form-section-title">
                                        <i class="bi bi-hand-thumbs-up"></i>
                                        <span>نوع المساعدة والتخصيص</span>
                                    </div>
                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <label class="form-label form-label-required">نوع المساعدة</label>
                                            <select name="assistance_type" class="form-select @error('assistance_type') is-invalid @enderror" required>
                                                <option value="financial" @selected(old('assistance_type', 'financial') === 'financial')>مالية</option>
                                                <option value="in_kind" @selected(old('assistance_type') === 'in_kind')>عينية</option>
                                                <option value="service" @selected(old('assistance_type') === 'service')>خدمية</option>
                                            </select>
                                            @error('assistance_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">نوع التخصيص</label>
                                            <select id="project-allocation-type" name="allocation_type" class="form-select @error('allocation_type') is-invalid @enderror">
                                                <option value="">— اختر النوع —</option>
                                                <option value="شخص واحد" @selected(old('allocation_type') === 'شخص واحد')>شخص واحد</option>
                                                <option value="أكثر من مستفيد" @selected(old('allocation_type') === 'أكثر من مستفيد')>أكثر من مستفيد</option>
                                            </select>
                                            @error('allocation_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label">التخصيص لكل طفل</label>
                                            <select id="project-child-sponsorship-type" name="child_sponsorship_type" class="form-select @error('child_sponsorship_type') is-invalid @enderror">
                                                <option value="">— اختر النوع —</option>
                                                <option value="كافل واحد" @selected(old('child_sponsorship_type') === 'كافل واحد')>كافل واحد</option>
                                                <option value="أكثر من كافل" @selected(old('child_sponsorship_type') === 'أكثر من كافل')>أكثر من كافل</option>
                                            </select>
                                            @error('child_sponsorship_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div id="project-allocated-beneficiaries-field"
                                            class="col-md-6 {{ old('allocation_type') ? '' : 'd-none' }}">
                                            <label id="project-allocated-beneficiaries-label" class="form-label">اختر المستفيد / المستفيدين</label>
                                            <select id="project-allocated-beneficiaries" name="allocated_beneficiary_ids[]"
                                                class="form-select @error('allocated_beneficiary_ids') is-invalid @enderror" multiple>
                                                <option value="" data-placeholder>— اختر المستفيد / المستفيدين —</option>
                                                @foreach($beneficiaryOptions as $option)
                                                    <option value="{{ $option->id }}" @selected(in_array((int) $option->id, $selectedAllocatedBeneficiaries, true))>
                                                        {{ $option->full_name }}{{ $option->code ? ' — ' . $option->code : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="project-allocated-beneficiaries-help" class="form-help-text"></div>
                                            @error('allocated_beneficiary_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                        <div id="project-sponsors-field" class="col-md-6 {{ old('child_sponsorship_type') ? '' : 'd-none' }}">
                                            <label id="project-sponsors-label" class="form-label">اختر الكافل / الكفلاء</label>
                                            <select id="project-sponsors-list" name="sponsor_ids[]"
                                                class="form-select @error('sponsor_ids') is-invalid @enderror" multiple>
                                                <option value="" data-placeholder>— اختر الكافل / الكفلاء —</option>
                                                @foreach($sponsors as $sponsor)
                                                    <option value="{{ $sponsor->id }}" @selected(in_array((int) $sponsor->id, $selectedSponsors, true))>
                                                        {{ $sponsor->name }}{{ $sponsor->phone ? ' — ' . $sponsor->phone : '' }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <div id="project-sponsors-help" class="form-help-text"></div>
                                            @error('sponsor_ids')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="form-section">
                                    <div class="form-section-title">
                                        <i class="bi bi-pencil-square"></i>
                                        <span>ملاحظات</span>
                                    </div>
                                    <label class="form-label">ملاحظات داخلية</label>
                                    <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="3"
                                        placeholder="أضف أي ملاحظات إضافية...">{{ old('notes') }}</textarea>
                                    @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                </div>

                                <div class="d-flex justify-content-end gap-2 mt-3">
                                    <button type="button" class="btn btn-outline-secondary" data-bs-toggle="collapse" data-bs-target="#beneficiaryForm">
                                        <i class="bi bi-x-lg me-1"></i> إلغاء
                                    </button>
                                    <button class="btn btn-primary px-4">
                                        <i class="bi bi-check-lg me-1"></i> حفظ المستفيد
                                    </button>
                                </div>
                            </form>
                        </div>

                        <div class="table-responsive">
                            @php
                                $genericBeneficiaries = $project->beneficiaries()
                                    ->whereNull('mother_name')
                                    ->with(['allocatedBeneficiaries:id,full_name', 'sponsors:id,name'])
                                    ->orderByDesc('id')
                                    ->limit(50)
                                    ->get();
                            @endphp
                            <table class="table table-sm table-hover align-middle small">
                                <thead class="bg-body-tertiary">
                                    <tr>
                                        <th>الكود</th>
                                        <th>الاسم</th>
                                        <th>الرقم القومي</th>
                                        <th>الهاتف</th>
                                        <th>العنوان</th>
                                        <th>المساعدة</th>
                                        <th>التخصيص</th>
                                        <th>كفالة الطفل</th>
                                        <th>ملاحظات</th>
                                        <th style="width: 80px">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($genericBeneficiaries as $ben)
                                    <tr>
                                        <td class="text-nowrap">{{ $ben->code ?? '—' }}</td>
                                        <td>{{ $ben->full_name }}</td>
                                        <td class="text-nowrap">{{ $ben->national_id ?? '—' }}</td>
                                        <td>{{ $ben->phone ?? '—' }}</td>
                                        <td>{{ $ben->address ?? '—' }}</td>
                                        <td>
                                            {{ match($ben->assistance_type) {
                                                'financial' => 'مالية',
                                                'in_kind' => 'عينية',
                                                'service' => 'خدمية',
                                                default => $ben->assistance_type ?? '—'
                                            } }}
                                        </td>
                                        <td>
                                            <div>{{ $ben->allocation_type ?? '—' }}</div>
                                            @if($ben->allocatedBeneficiaries->isNotEmpty())
                                                <div class="text-muted mt-1" style="font-size: .72rem">
                                                    {{ $ben->allocatedBeneficiaries->pluck('full_name')->join('، ') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>
                                            <div>{{ $ben->child_sponsorship_type ?? '—' }}</div>
                                            @if($ben->sponsors->isNotEmpty())
                                                <div class="text-muted mt-1" style="font-size: .72rem">
                                                    {{ $ben->sponsors->pluck('name')->join('، ') }}
                                                </div>
                                            @endif
                                        </td>
                                        <td>{{ $ben->notes ?? '—' }}</td>
                                        <td>
                                            <div class="d-flex gap-2">
                                                <a class="btn btn-link text-primary p-0" href="{{ route('beneficiaries.edit', $ben) }}" title="تعديل كل بيانات المستفيد">
                                                    <i class="bi bi-pencil"></i>
                                                </a>

                                                <form action="{{ route('projects.destroyBeneficiaryFile', [$project, $ben]) }}" method="POST" onsubmit="return confirm('حذف المستفيد؟')" class="d-inline">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr><td colspan="10" class="text-center py-3 text-muted">لا يوجد مستفيدين مسجلين لهذا المشروع</td></tr>
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
                                                <select name="assistance_type" class="form-select form-select-sm" required>
                                                    <option value="in_kind" @selected($ben->assistance_type === 'in_kind')>عينية</option>
                                                    <option value="financial" @selected($ben->assistance_type === 'financial')>مالية</option>
                                                    <option value="service" @selected($ben->assistance_type === 'service')>خدمة</option>
                                                </select>
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

    @include('projects.partials.finance-modals')
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const $ = window.jQuery;
            const hasSelect2 = Boolean($ && $.fn && $.fn.select2);

            if (hasSelect2) {
                $('#project-allocation-type, #project-child-sponsorship-type').select2({
                    theme: 'bootstrap-5',
                    dir: 'rtl',
                    width: '100%',
                    allowClear: true,
                    minimumResultsForSearch: 0,
                    placeholder: 'ابحث واختر النوع...'
                });
            }

            function configurePeopleList(config) {
                const typeSelect = document.getElementById(config.typeSelectId);
                const field = document.getElementById(config.fieldId);
                const list = document.getElementById(config.listId);
                const label = document.getElementById(config.labelId);
                const help = document.getElementById(config.helpId);

                if (!typeSelect || !field || !list) return;

                function updateList() {
                    const isActive = typeSelect.value === config.singleType || typeSelect.value === config.multipleType;
                    const isMultiple = typeSelect.value === config.multipleType;

                    if (hasSelect2 && $(list).hasClass('select2-hidden-accessible')) {
                        $(list).select2('destroy');
                    }

                    field.classList.toggle('d-none', !isActive);
                    list.disabled = !isActive;
                    list.required = isActive;
                    list.multiple = isMultiple;
                    list.size = isMultiple ? Math.min(6, Math.max(2, list.options.length)) : 1;

                    const placeholder = list.querySelector('[data-placeholder]');
                    if (placeholder) {
                        placeholder.disabled = isMultiple;
                        if (isMultiple) placeholder.selected = false;
                    }

                    label.textContent = isMultiple ? config.multipleLabel : config.singleLabel;
                    help.textContent = isMultiple ? 'يمكنك اختيار أكثر من اسم من القائمة.' : '';

                    if (!isActive) {
                        Array.from(list.options).forEach(option => option.selected = false);
                    } else if (!isMultiple && list.selectedOptions.length > 1) {
                        Array.from(list.selectedOptions).slice(1).forEach(option => option.selected = false);
                    }

                    if (isActive && hasSelect2) {
                        $(list).select2({
                            theme: 'bootstrap-5',
                            dir: 'rtl',
                            width: '100%',
                            allowClear: !isMultiple,
                            closeOnSelect: !isMultiple,
                            minimumResultsForSearch: 0,
                            placeholder: isMultiple ? config.multipleLabel : config.singleLabel
                        });
                    }
                }

                typeSelect.addEventListener('change', updateList);
                if (hasSelect2) {
                    $(typeSelect).on('change select2:select select2:clear', updateList);
                }
                updateList();
            }

            configurePeopleList({
                typeSelectId: 'project-allocation-type',
                fieldId: 'project-allocated-beneficiaries-field',
                listId: 'project-allocated-beneficiaries',
                labelId: 'project-allocated-beneficiaries-label',
                helpId: 'project-allocated-beneficiaries-help',
                singleType: 'شخص واحد',
                multipleType: 'أكثر من مستفيد',
                singleLabel: 'اختر المستفيد',
                multipleLabel: 'اختر المستفيدين'
            });

            configurePeopleList({
                typeSelectId: 'project-child-sponsorship-type',
                fieldId: 'project-sponsors-field',
                listId: 'project-sponsors-list',
                labelId: 'project-sponsors-label',
                helpId: 'project-sponsors-help',
                singleType: 'كافل واحد',
                multipleType: 'أكثر من كافل',
                singleLabel: 'اختر الكافل',
                multipleLabel: 'اختر الكفلاء'
            });

            const donorMode = document.getElementById('project-donor-mode');
            const existingDonorField = document.getElementById('project-existing-donor-field');
            const donorId = document.getElementById('project-donor-id');
            const newDonorFields = document.getElementById('project-new-donor-fields');
            const newDonorName = document.getElementById('project-new-donor-name');
            const newDonorPhone = document.getElementById('project-new-donor-phone');

            function updateDonorMode() {
                if (!donorMode) return;

                const isNew = donorMode.value === 'new';
                existingDonorField?.classList.toggle('d-none', isNew);
                newDonorFields?.classList.toggle('d-none', !isNew);

                if (donorId) {
                    donorId.disabled = isNew;
                    donorId.required = !isNew;
                    if (isNew) donorId.value = '';
                }

                newDonorFields?.querySelectorAll('input, select').forEach(function (field) {
                    field.disabled = !isNew;
                });
                if (newDonorName) newDonorName.required = isNew;
                if (newDonorPhone) newDonorPhone.required = isNew;
            }

            donorMode?.addEventListener('change', updateDonorMode);
            updateDonorMode();

            const donationType = document.getElementById('project-donation-type');
            const cashFields = document.querySelectorAll('.project-donation-cash-field');
            const inKindFields = document.querySelectorAll('.project-donation-in-kind-field');
            const cashRequiredIds = [
                'project-donation-amount',
                'project-donation-cash-channel',
                'project-donation-receipt',
                'project-donation-treasury'
            ];
            const inKindRequiredIds = [
                'project-donation-estimated-value',
                'project-donation-warehouse'
            ];

            function setFinanceFieldsState(fields, active) {
                fields.forEach(function (container) {
                    container.classList.toggle('d-none', !active);
                    container.querySelectorAll('input, select, textarea').forEach(function (field) {
                        field.disabled = !active;
                    });
                });
            }

            function updateDonationType() {
                if (!donationType) return;

                const isCash = donationType.value === 'cash';
                setFinanceFieldsState(cashFields, isCash);
                setFinanceFieldsState(inKindFields, !isCash);
                cashRequiredIds.forEach(function (id) {
                    const field = document.getElementById(id);
                    if (field) field.required = isCash;
                });
                inKindRequiredIds.forEach(function (id) {
                    const field = document.getElementById(id);
                    if (field) field.required = !isCash;
                });
            }

            donationType?.addEventListener('change', updateDonationType);
            updateDonationType();

            const previousFormContext = @json(old('form_context'));
            const modalId = previousFormContext === 'project_donation'
                ? 'projectDonationModal'
                : (previousFormContext === 'project_expense' ? 'projectExpenseModal' : null);

            if (modalId && window.bootstrap) {
                const modalElement = document.getElementById(modalId);
                if (modalElement) {
                    window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
                }
            }
        });
    </script>
@endsection

