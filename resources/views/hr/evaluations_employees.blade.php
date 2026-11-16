@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #d97706 0%, #f59e0b 50%, #b45309 100%);">
        <div class="hero-content">
            <div class="hero-greeting">الموارد البشرية HR ⭐</div>
            <h1 class="hero-title">تقييمات الموظفين</h1>
            <p class="hero-subtitle">نماذج تقييم الأداء الوظيفي المعتمدة (10 بنود موزونة من 100 درجة)</p>
            <div class="hero-actions d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-light text-dark fw-bold rounded-pill px-4" onclick="open_empEvalPageModal()">
                    <i class="bi bi-plus-circle-fill text-warning me-1"></i> تسجيل تقييم موظف جديد
                </button>
                <a href="{{ route('hr.evaluations.volunteers') }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-heart-pulse me-1"></i> تقييمات المتطوعين
                </a>
                <a href="{{ route('hr.evaluations') }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-speedometer2 me-1"></i> لوحة تحكم HR
                </a>
            </div>
        </div>
        <i class="bi bi-award-fill hero-icon d-none d-md-block"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Search & Filter --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('hr.evaluations.employees') }}" class="row g-2 align-items-end">
                <div class="col-md-5">
                    <label class="form-label small text-muted mb-1">بحث بالاسم أو الكود أو المسمى</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="ابحث عن موظف...">
                </div>
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">القسم / الإدارة</label>
                    <select name="department" class="form-select">
                        <option value="">كل الأقسام</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept }}" @selected($department === $dept)>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1"><i class="bi bi-search me-1"></i> بحث</button>
                    <a href="{{ route('hr.evaluations.employees') }}" class="btn btn-outline-secondary">الكل</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Employees Evaluations Table --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-people-fill text-primary me-2"></i> قائمة تقييم أداء الموظفين ({{ $employees->count() }})
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start ps-4">الموظف</th>
                            <th>القسم / الإدارة</th>
                            <th>المسمى الوظيفي</th>
                            <th>المجموع (من 100)</th>
                            <th>النسبة المئوية</th>
                            <th>التقدير</th>
                            <th>آخر تقييم</th>
                            <th class="text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($employees as $emp)
                            @php $eval = $emp->latestEvaluation; @endphp
                            <tr>
                                <td class="text-start ps-4">
                                    <a href="{{ route('users.show', $emp) }}" class="fw-bold text-dark text-decoration-none d-block">{{ $emp->name }}</a>
                                    <span class="x-small text-muted font-monospace">{{ $emp->code }}</span>
                                </td>
                                <td>{{ $emp->department ?: '—' }}</td>
                                <td>{{ $emp->job_title ?: '—' }}</td>
                                <td>
                                    @if($eval)
                                        <span class="fw-bolder font-monospace fs-6 text-primary">{{ number_format($eval->total_score, 1) }}</span>
                                        <span class="x-small text-muted">/ 100</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($eval)
                                        <span class="badge bg-light text-dark border font-monospace">{{ $eval->percentage }}%</span>
                                    @else
                                        <span class="text-muted small">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($eval)
                                        <span class="badge {{ $eval->getRatingBadgeClass() }} px-3 py-1 rounded-pill">{{ $eval->rating_label }}</span>
                                    @else
                                        <span class="badge bg-secondary-subtle text-secondary px-2 py-1 rounded-pill small">لم يُقيّم</span>
                                    @endif
                                </td>
                                <td class="small text-muted">{{ $eval && $eval->evaluation_date ? $eval->evaluation_date->format('Y-m-d') : '—' }}</td>
                                <td class="text-end pe-4">
                                    <div class="d-inline-flex gap-1">
                                        <button type="button" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3" onclick="open_empEvalPageModal({{ $emp->id }})">
                                            <i class="bi bi-star-fill me-1"></i> تقييم
                                        </button>
                                        <a href="{{ route('users.show', $emp) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye me-1"></i> الملف
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">لا يوجد موظفون مطابقون للبحث.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Recent History --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3 border-bottom">
            <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-clock-history text-secondary me-2"></i> أحدث تقييمات الموظفين المسجلة</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0 text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-start ps-4">الموظف</th>
                            <th>الفترة</th>
                            <th>التاريخ</th>
                            <th>المجموع</th>
                            <th>التقدير</th>
                            <th>المُقيّم</th>
                            <th class="text-end pe-4">حذف</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentEvaluations as $rev)
                            <tr>
                                <td class="text-start ps-4 fw-bold">{{ $rev->user?->name ?? '—' }}</td>
                                <td>{{ $rev->evaluation_period ?: '—' }}</td>
                                <td class="small text-muted">{{ $rev->evaluation_date ? $rev->evaluation_date->format('Y-m-d') : '—' }}</td>
                                <td class="fw-bold text-primary font-monospace">{{ number_format($rev->total_score, 1) }} / 100</td>
                                <td><span class="badge {{ $rev->getRatingBadgeClass() }} rounded-pill">{{ $rev->rating_label }}</span></td>
                                <td class="small text-muted">{{ $rev->evaluator?->name ?? '—' }}</td>
                                <td class="text-end pe-4">
                                    <form action="{{ route('employee-evaluations.destroy', $rev) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف هذا التقييم؟');">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center py-4 text-muted">لا توجد تقييمات مسجلة بعد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@include('hr.partials._evaluation_form_modal', [
    'modalId' => 'empEvalPageModal',
    'actionRoute' => route('employee-evaluations.store'),
    'title' => 'تسجيل تقييم أداء موظف',
    'personLabel' => 'الموظف',
    'people' => $employees,
    'criteria' => $criteria,
])
@endsection

