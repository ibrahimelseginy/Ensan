@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التطوع 🤝</div>
        <h1 class="hero-title">المتطوعون</h1>
        <p class="hero-subtitle">إدارة وتتبع فريق المتطوعين</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('volunteers.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة متطوع
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
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-3 py-2">
                            {{ $p->name }}: <span class="fw-bold">{{ $p->volunteers_count }}</span>
                        </span>
                    @endif
                @endforeach
                @if($projectsWithStats->sum('volunteers_count') == 0)
                    <small class="text-muted">لا يوجد متطوعين معينين لمشاريع بعد.</small>
                @endif
            </div>
        </div>
    </div>
</div>

  <div class="row g-3">
    @foreach($volunteers as $v)
      <div class="col-md-4 col-sm-6">
        <div class="card h-100 shadow-sm border-0">
          <div class="card-body position-relative">
            <div class="d-flex justify-content-between align-items-start mb-2">
              <h5 class="card-title fw-bold text-body mb-0 text-truncate" style="max-width: 70%;">{{ $v->name }}</h5>
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
              @else
                <span class="badge bg-secondary-subtle text-secondary border">غير معين</span>
              @endif
            </div>

            <!-- The main link to show details -->
            <a href="{{ route('volunteers.show', $v) }}" class="stretched-link"></a>
          </div>

          <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end py-2"
            style="position: relative; z-index: 2;">
            <a href="{{ route('volunteers.edit', $v) }}" class="btn btn-sm btn-outline-secondary me-1" title="تعديل"><i
                class="bi bi-pencil"></i></a>
            <form class="d-inline" method="POST" action="{{ route('volunteers.destroy', $v) }}"
              onsubmit="return confirm('هل أنت متأكد؟');">
              @csrf @method('DELETE')
              <button class="btn btn-sm btn-outline-danger" title="حذف"><i class="bi bi-trash"></i></button>
            </form>
          </div>
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-4">{{ $volunteers->links() }}</div>
@endsection