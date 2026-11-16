@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التطوع 🤝</div>
        <h1 class="hero-title">المتطوعون</h1>
        <p class="hero-subtitle">إدارة وتتبع فريق المتطوعين وملفات تسكينهم في المشاريع</p>
        <div class="hero-actions d-flex flex-wrap gap-2">
            <a href="{{ route('volunteers.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة متطوع
            </a>
            <a href="{{ route('website.volunteer-requests.index') }}" class="btn btn-sm btn-light text-success fw-bold rounded-pill px-4">
                <i class="bi bi-inbox-fill me-1"></i> طلبات التطوع (الموقع)
            </a>
            <a href="{{ route('mobile.volunteer-requests.index') }}" class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-4">
                <i class="bi bi-phone me-1"></i> طلبات التطوع (التطبيق)
            </a>
        </div>
    </div>
    <i class="bi bi-people-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-md-3 animate-slide-up animate-delay-1">
        <div class="stat-card stat-primary">
            <div class="stat-icon"><i class="bi bi-people-fill"></i></div>
            <div class="stat-label">إجمالي المتطوعين</div>
            <div class="stat-value">{{ number_format($totalVolunteers) }}</div>
            <i class="bi bi-people-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-md-3 animate-slide-up animate-delay-2">
        <div class="stat-card stat-success">
            <div class="stat-icon"><i class="bi bi-check-circle-fill"></i></div>
            <div class="stat-label">المتطوعين النشطين</div>
            <div class="stat-value">{{ number_format($activeVolunteers) }}</div>
            <div class="stat-trend up"><i class="bi bi-check"></i> نشط</div>
            <i class="bi bi-check-circle-fill stat-bg-icon"></i>
        </div>
    </div>
    <div class="col-md-6 animate-slide-up animate-delay-3">
        <div class="summary-panel h-100">
            <h5 class="summary-title"><i class="bi bi-briefcase text-primary"></i> المتطوعين حسب المشروع</h5>
            <div class="d-flex flex-wrap gap-2">
                @foreach($projectsWithStats as $p)
                    @if($p->volunteers_count > 0)
                        <a href="{{ route('volunteers.index', ['project_id' => $p->id]) }}"
                           class="badge text-decoration-none {{ (int)($projectId ?? 0) === (int)$p->id ? 'bg-primary text-white' : 'bg-primary-subtle text-primary border border-primary-subtle' }} px-3 py-2">
                            {{ $p->name }}: <span class="fw-bold">{{ $p->volunteers_count }}</span>
                        </a>
                    @endif
                @endforeach
                @if($projectsWithStats->sum('volunteers_count') == 0)
                    <small class="text-muted">لا يوجد متطوعين معينين لمشاريع بعد.</small>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- ملفات المشاريع — كل مشروع وبه قائمة المتطوعين المتسكنين فيه --}}
<div class="card border-0 shadow-sm rounded-4 mb-4 animate-slide-up">
    <div class="card-header bg-transparent border-0 d-flex flex-wrap justify-content-between align-items-center gap-2 pt-4 px-4 pb-2">
        <div>
            <h5 class="fw-bold mb-1 text-dark">
                <i class="bi bi-folder2-open text-success me-2"></i> ملفات المشاريع (المتطوعون المتسكنون بكل مشروع)
            </h5>
            <small class="text-muted">افتح ملف أي مشروع لعرض وطباعة الكشف الكامل لجميع المتطوعين المتسكنين فيه وتقييمهم مباشرة</small>
        </div>
        @if($projectId || $campaignId || $guestHouseId || $unassigned || $q !== '')
            <a href="{{ route('volunteers.index') }}" class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-x-circle me-1"></i> عرض كل المتطوعين
            </a>
        @endif
    </div>
    <div class="card-body px-4 pb-4 pt-2">
        <div class="row g-3">
            @forelse($projectFiles as $pf)
                @php $proj = $pf['project']; $members = $pf['members']; @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-between {{ (int)($projectId ?? 0) === (int)$proj->id ? 'border-primary bg-primary-subtle' : 'bg-light bg-opacity-50' }}">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-folder-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $proj->name }}</div>
                                        <small class="text-muted">ملف متطوعي المشروع</small>
                                    </div>
                                </div>
                                <span class="badge bg-primary text-white rounded-pill px-3 py-1">
                                    {{ $pf['count'] }} متطوع
                                </span>
                            </div>

                            <div class="d-flex flex-wrap gap-1 my-2">
                                @foreach($members->take(4) as $m)
                                    <span class="badge bg-white text-dark border small fw-normal">
                                        {{ $m->name }} ({{ $m->volunteer_code }})
                                    </span>
                                @endforeach
                                @if($members->count() > 4)
                                    <span class="badge bg-secondary-subtle text-secondary small">+{{ $members->count() - 4 }} آخرين</span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-primary flex-grow-1 rounded-pill fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#projectFileModal{{ $proj->id }}">
                                <i class="bi bi-folder2-open me-1"></i> فتح ملف المشروع
                            </button>
                            <a href="{{ route('volunteers.index', ['project_id' => $proj->id]) }}"
                               class="btn btn-sm btn-outline-primary rounded-pill" title="تصفية البطاقات بالأسفل">
                                <i class="bi bi-funnel"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-light border text-muted mb-0 text-center py-3">
                        لا توجد مشاريع تحتوي على متطوعين مسكنين حتى الآن.
                    </div>
                </div>
            @endforelse

            @foreach($campaignFiles as $cf)
                @php $camp = $cf['campaign']; $members = $cf['members']; @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-between bg-warning-subtle bg-opacity-25">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 bg-warning-subtle text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-megaphone-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $camp->name }}</div>
                                        <small class="text-muted">ملف متطوعي الحملة</small>
                                    </div>
                                </div>
                                <span class="badge bg-warning text-dark rounded-pill px-3 py-1">{{ $cf['count'] }} متطوع</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1 my-2">
                                @foreach($members->take(4) as $m)
                                    <span class="badge bg-white text-dark border small fw-normal">{{ $m->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-warning text-dark flex-grow-1 rounded-pill fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#campaignFileModal{{ $camp->id }}">
                                <i class="bi bi-folder2-open me-1"></i> فتح ملف الحملة
                            </button>
                            <a href="{{ route('volunteers.index', ['campaign_id' => $camp->id]) }}"
                               class="btn btn-sm btn-outline-warning text-dark rounded-pill" title="تصفية البطاقات">
                                <i class="bi bi-funnel"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            @foreach($guestHouseFiles as $gf)
                @php $gh = $gf['guest_house']; $members = $gf['members']; @endphp
                <div class="col-md-6 col-xl-4">
                    <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-between bg-info-subtle bg-opacity-25">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 bg-info-subtle text-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-house-door-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $gh->name }}</div>
                                        <small class="text-muted">ملف متطوعي دار الضيافة</small>
                                    </div>
                                </div>
                                <span class="badge bg-info text-white rounded-pill px-3 py-1">{{ $gf['count'] }} متطوع</span>
                            </div>
                            <div class="d-flex flex-wrap gap-1 my-2">
                                @foreach($members->take(4) as $m)
                                    <span class="badge bg-white text-dark border small fw-normal">{{ $m->name }}</span>
                                @endforeach
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <button type="button" class="btn btn-sm btn-info text-white flex-grow-1 rounded-pill fw-bold"
                                    data-bs-toggle="modal" data-bs-target="#guestHouseFileModal{{ $gh->id }}">
                                <i class="bi bi-folder2-open me-1"></i> فتح ملف الدار
                            </button>
                            <a href="{{ route('volunteers.index', ['guest_house_id' => $gh->id]) }}"
                               class="btn btn-sm btn-outline-info rounded-pill" title="تصفية البطاقات">
                                <i class="bi bi-funnel"></i>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            @if($unassignedVolunteers->isNotEmpty())
                <div class="col-md-6 col-xl-4">
                    <div class="border rounded-4 p-3 h-100 d-flex flex-column justify-content-between bg-light">
                        <div>
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-3 bg-secondary-subtle text-secondary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="bi bi-person-dash-fill fs-5"></i>
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">غير مسكنين بمشروع</div>
                                        <small class="text-muted">متطوعون بانتظار التسكين</small>
                                    </div>
                                </div>
                                <span class="badge bg-secondary rounded-pill px-3 py-1">{{ $unassignedVolunteers->count() }} متطوع</span>
                            </div>
                        </div>
                        <div class="d-flex gap-2 mt-2 pt-2 border-top">
                            <a href="{{ route('volunteers.index', ['unassigned' => 1]) }}"
                               class="btn btn-sm btn-outline-secondary flex-grow-1 rounded-pill fw-bold">
                                <i class="bi bi-funnel me-1"></i> عرض غير المسكنين
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Search & Active Filter Banner --}}
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('volunteers.index') }}" class="row g-2 align-items-center">
            @if($projectId) <input type="hidden" name="project_id" value="{{ $projectId }}"> @endif
            @if($campaignId) <input type="hidden" name="campaign_id" value="{{ $campaignId }}"> @endif
            @if($guestHouseId) <input type="hidden" name="guest_house_id" value="{{ $guestHouseId }}"> @endif
            @if($unassigned) <input type="hidden" name="unassigned" value="1"> @endif
            <div class="col-md-9">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="ابحث باسم المتطوع، الكود (V-00003)، الهاتف أو البريد...">
                </div>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary flex-grow-1">بحث</button>
                @if($projectId || $campaignId || $guestHouseId || $unassigned || $q !== '')
                    <a href="{{ route('volunteers.index') }}" class="btn btn-outline-secondary">الكل</a>
                @endif
            </div>
        </form>

        @if($selectedProject)
            <div class="alert alert-primary d-flex justify-content-between align-items-center mt-3 mb-0 py-2 px-3 rounded-3">
                <div>
                    <i class="bi bi-folder2-open me-1"></i>
                    يتم الآن عرض ملف المشروع: <strong>{{ $selectedProject->name }}</strong>
                    ({{ $volunteers->total() }} متطوع)
                </div>
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#projectFileModal{{ $selectedProject->id }}">
                    <i class="bi bi-table me-1"></i> فتح جدول ملف المشروع الكامل
                </button>
            </div>
        @endif
    </div>
</div>

{{-- Volunteers Grid --}}
<div class="row g-3">
    @forelse($volunteers as $v)
      @php
          $latestEval = $v->volunteerEvaluations->first();
      @endphp
      <div class="col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body position-relative">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <div>
                <h5 class="card-title fw-bold text-body mb-1 text-truncate" style="max-width: 220px;">{{ $v->name }}</h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-2 py-0.5 rounded-pill x-small fw-bold">
                  {{ $v->volunteer_code }}
                </span>
                @if($latestEval)
                    <span class="badge bg-warning-subtle text-dark border border-warning-subtle px-2 py-0.5 rounded-pill x-small ms-1" title="آخر تقييم أداء">
                        <i class="bi bi-star-fill text-warning me-1"></i> {{ $latestEval->percentage }}% ({{ $latestEval->rating_label }})
                    </span>
                @endif
              </div>
              @if($v->active)
                <span class="badge bg-success-subtle text-success rounded-pill">نشط</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary rounded-pill">غير نشط</span>
              @endif
            </div>

            <div class="text-muted small mb-2">
              <i class="bi bi-telephone me-1"></i> {{ $v->phone ?? '—' }}
            </div>
            <div class="text-muted small mb-3">
              <i class="bi bi-envelope me-1"></i> {{ $v->email }}
            </div>

            <div class="mb-1">
              @if($v->project)
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i
                    class="bi bi-briefcase me-1"></i> {{ $v->project->name }}</span>
              @elseif($v->campaign)
                <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i
                    class="bi bi-megaphone me-1"></i> {{ $v->campaign->name }}</span>
              @elseif($v->guestHouse)
                <span class="badge bg-info-subtle text-info border border-info-subtle"><i class="bi bi-house me-1"></i>
                  {{ $v->guestHouse->name }}</span>
              @elseif(!empty($v->project_role))
                <span class="badge bg-success-subtle text-success border border-success-subtle"><i
                    class="bi bi-diagram-3 me-1"></i> {{ Str::limit($v->project_role, 45) }}</span>
              @else
                <span class="badge bg-secondary-subtle text-secondary border">غير معين</span>
              @endif
            </div>

            <!-- The main link to show details -->
            <a href="{{ route('volunteers.show', $v) }}" class="stretched-link"></a>
          </div>

          <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end gap-1 py-2"
            style="position: relative; z-index: 2;">
            <a href="{{ route('volunteers.show', $v) }}?open_eval=1" class="btn btn-sm btn-outline-warning text-dark" title="تقييم أداء المتطوع">
                <i class="bi bi-star-fill text-warning"></i>
            </a>
            <a href="{{ route('volunteers.show', $v) }}" class="btn btn-sm btn-outline-primary" title="عرض ملف المتطوع">
                <i class="bi bi-eye"></i>
            </a>
            <a href="{{ route('volunteers.edit', $v) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                <i class="bi bi-pencil"></i>
            </a>
            <form class="d-inline" method="POST" action="{{ route('volunteers.destroy', $v) }}"
              onsubmit="return confirm('هل أنت متأكد؟');">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="col-12">
        <div class="card border-0 shadow-sm p-5 text-center text-muted">
          <i class="bi bi-people display-5 d-block mb-2 opacity-50"></i>
          لا يوجد متطوعون يطابقون معايير البحث الحالية.
        </div>
      </div>
    @endforelse
</div>

<div class="mt-4">{{ $volunteers->links() }}</div>

{{-- Project File Modals --}}
@foreach($projectFiles as $pf)
    @php $proj = $pf['project']; $members = $pf['members']; @endphp
    <div class="modal fade" id="projectFileModal{{ $proj->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-fullscreen-md-down" style="max-width: min(96vw, 1360px); width: 96vw;">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light">
                    <div>
                        <h5 class="modal-title fw-bold mb-0">
                            <i class="bi bi-folder2-open text-primary me-2"></i>
                            ملف المشروع: {{ $proj->name }}
                        </h5>
                        <small class="text-muted">قائمة جميع المتطوعين المتسكنين في هذا المشروع ({{ $pf['count'] }} متطوع)</small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-3">
                    <div id="printableProjectRoster{{ $proj->id }}">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0 text-center w-100">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 36px;">#</th>
                                        <th class="text-nowrap">الكود</th>
                                        <th class="text-start text-nowrap">اسم المتطوع</th>
                                        <th class="text-nowrap">الهاتف</th>
                                        <th>البريد</th>
                                        <th class="text-start" style="min-width: 200px;">الدور / الملاحظات</th>
                                        <th class="text-nowrap">الساعات</th>
                                        <th class="text-nowrap">التقييم</th>
                                        <th class="d-print-none text-nowrap" style="min-width: 120px;">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($members as $idx => $m)
                                        @php $mEval = $m->volunteerEvaluations->first(); @endphp
                                        <tr>
                                            <td>{{ $idx + 1 }}</td>
                                            <td class="text-nowrap"><span class="badge bg-primary-subtle text-primary font-monospace">{{ $m->volunteer_code }}</span></td>
                                            <td class="text-start fw-bold text-nowrap">{{ $m->name }}</td>
                                            <td dir="ltr" class="text-nowrap">{{ $m->phone ?? '—' }}</td>
                                            <td class="small text-muted" style="word-break: break-word;">{{ $m->email }}</td>
                                            <td class="small text-start" style="white-space: normal; line-height: 1.6;">
                                                {{ $m->project_role ?: ($m->projects->firstWhere('id', $proj->id)?->pivot?->role ?? 'متطوع') }}
                                            </td>
                                            <td class="fw-bold text-nowrap">{{ number_format((float)($m->volunteer_hours ?? 0), 1) }}</td>
                                            <td class="text-nowrap">
                                                @if($mEval)
                                                    <span class="badge {{ $mEval->getRatingBadgeClass() }}">{{ $mEval->percentage }}% ({{ $mEval->rating_label }})</span>
                                                @else
                                                    <span class="badge bg-secondary-subtle text-secondary">لم يُقيّم</span>
                                                @endif
                                            </td>
                                            <td class="d-print-none text-nowrap">
                                                <div class="d-inline-flex gap-1">
                                                    <a href="{{ route('volunteers.show', $m) }}?open_eval=1" class="btn btn-sm btn-outline-warning text-dark" title="تقييم المتطوع">
                                                        <i class="bi bi-star-fill text-warning"></i>
                                                    </a>
                                                    <a href="{{ route('volunteers.show', $m) }}" class="btn btn-sm btn-outline-primary" title="ملف المتطوع">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('volunteers.edit', $m) }}" class="btn btn-sm btn-outline-secondary" title="تعديل">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-outline-primary rounded-pill px-3"
                            onclick="printProjectRoster('printableProjectRoster{{ $proj->id }}', 'كشف متطوعي {{ $proj->name }}')">
                        <i class="bi bi-printer me-1"></i> طباعة كشف المشروع
                    </button>
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach($campaignFiles as $cf)
    @php $camp = $cf['campaign']; $members = $cf['members']; @endphp
    <div class="modal fade" id="campaignFileModal{{ $camp->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-megaphone-fill text-warning me-2"></i> ملف الحملة: {{ $camp->name }} ({{ $cf['count'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr><th>الكود</th><th class="text-start">الاسم</th><th>الهاتف</th><th>إجراءات</th></tr>
                        </thead>
                        <tbody>
                            @foreach($members as $m)
                                <tr>
                                    <td>{{ $m->volunteer_code }}</td>
                                    <td class="text-start fw-bold">{{ $m->name }}</td>
                                    <td dir="ltr">{{ $m->phone ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('volunteers.show', $m) }}?open_eval=1" class="btn btn-sm btn-outline-warning text-dark" title="تقييم"><i class="bi bi-star-fill text-warning"></i></a>
                                        <a href="{{ route('volunteers.show', $m) }}" class="btn btn-sm btn-outline-primary" title="ملف"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach

@foreach($guestHouseFiles as $gf)
    @php $gh = $gf['guest_house']; $members = $gf['members']; @endphp
    <div class="modal fade" id="guestHouseFileModal{{ $gh->id }}" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg rounded-4">
                <div class="modal-header bg-light">
                    <h5 class="modal-title fw-bold"><i class="bi bi-house-door-fill text-info me-2"></i> ملف دار الضيافة: {{ $gh->name }} ({{ $gf['count'] }})</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <table class="table table-hover align-middle mb-0 text-center">
                        <thead class="table-light">
                            <tr><th>الكود</th><th class="text-start">الاسم</th><th>الهاتف</th><th>إجراءات</th></tr>
                        </thead>
                        <tbody>
                            @foreach($members as $m)
                                <tr>
                                    <td>{{ $m->volunteer_code }}</td>
                                    <td class="text-start fw-bold">{{ $m->name }}</td>
                                    <td dir="ltr">{{ $m->phone ?? '—' }}</td>
                                    <td>
                                        <a href="{{ route('volunteers.show', $m) }}?open_eval=1" class="btn btn-sm btn-outline-warning text-dark" title="تقييم"><i class="bi bi-star-fill text-warning"></i></a>
                                        <a href="{{ route('volunteers.show', $m) }}" class="btn btn-sm btn-outline-primary" title="ملف"><i class="bi bi-eye"></i></a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endforeach

<script>
    function printProjectRoster(containerId, title) {
        const el = document.getElementById(containerId);
        if (!el) return;
        if (window.EnsanPrintPreview && typeof window.EnsanPrintPreview.open === 'function') {
            window.EnsanPrintPreview.open('#' + containerId, { title: title });
            return;
        }
        const w = window.open('', '_blank');
        w.document.write('<html dir="rtl" lang="ar"><head><title>' + title + '</title><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css"></head><body class="p-4"><h4 class="mb-3">' + title + '</h4>' + el.innerHTML + '</body></html>');
        w.document.close();
        w.print();
    }
</script>
@endsection
