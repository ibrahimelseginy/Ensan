@extends('layouts.app')
@section('content')


  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold text-dark mb-1">
        <i class="bi bi-easel text-primary me-2"></i>إدارة workspace Ensan
      </h4>
    </div>
    <div>
      <a href="{{ route('workspaces.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> إضافة مساحة
      </a>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-4">
      <div class="card kpi-card border-0 shadow-sm p-3 h-100" style="border-radius: 16px; border-right: 4px solid #059669 !important;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="ws-stat-val text-success fw-bold fs-4">{{ number_format($totalRevenue, 2) }} ج.م</div>
            <div class="ws-stat-lbl text-muted small fw-bold mt-1">إجمالي الأرباح (الإيرادات)</div>
          </div>
          <div class="bg-success bg-opacity-10 p-2.5 rounded-3 text-success"><i class="bi bi-graph-up-arrow fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card kpi-card border-0 shadow-sm p-3 h-100" style="border-radius: 16px; border-right: 4px solid #dc3545 !important;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="ws-stat-val text-danger fw-bold fs-4">{{ number_format($totalExpenses, 2) }} ج.م</div>
            <div class="ws-stat-lbl text-muted small fw-bold mt-1">إجمالي المصروفات</div>
          </div>
          <div class="bg-danger bg-opacity-10 p-2.5 rounded-3 text-danger"><i class="bi bi-graph-down-arrow fs-4"></i></div>
        </div>
      </div>
    </div>
    <div class="col-md-4">
      <div class="card kpi-card border-0 shadow-sm p-3 h-100" style="border-radius: 16px; border-right: 4px solid {{ $netBalance >= 0 ? '#059669' : '#fd7e14' }} !important;">
        <div class="d-flex justify-content-between align-items-start">
          <div>
            <div class="ws-stat-val {{ $netBalance >= 0 ? 'text-success' : 'text-warning' }} fw-bold fs-4">
              {{ number_format($netBalance, 2) }} ج.م
            </div>
            <div class="ws-stat-lbl text-muted small fw-bold mt-1">صافي الحسابات</div>
          </div>
          <div class="bg-primary bg-opacity-10 p-2.5 rounded-3 text-primary"><i class="bi bi-wallet2 fs-4"></i></div>
        </div>
      </div>
    </div>
  </div>

  <div class="row g-4">
    <div class="col-lg-8">
      <form method="GET" class="card border-0 shadow-sm mb-4 p-4" style="border-radius: 16px;">
        <div class="row g-3 align-items-end">
          <div class="col-md-6">
            <label class="form-label fw-bold small text-muted">بحث بالاسم أو الموقع</label>
            <input name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="القاعة الرئيسية..." style="height: 44px; border-radius: 10px;">
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold small text-muted">الحالة</label>
            <select name="status" class="form-select" style="height: 44px; border-radius: 10px;">
              <option value="">الكل</option>
              <option value="available" @selected(($status ?? '') === 'available')>متاح</option>
              <option value="busy" @selected(($status ?? '') === 'busy')>مشغول</option>
              <option value="maintenance" @selected(($status ?? '') === 'maintenance')>صيانة</option>
            </select>
          </div>
          <div class="col-md-3">
            <label class="form-label fw-bold small text-muted d-none d-md-block" style="visibility: hidden;">إجراء</label>
            <div class="d-flex gap-2">
              <button class="btn btn-primary fw-bold w-100" style="height: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center; gap: 6px;">
                <i class="bi bi-funnel"></i>
                <span>تصفية</span>
              </button>
              @if(!empty($q) || !empty($status))
                <a href="{{ route('workspaces.index') }}" class="btn btn-outline-secondary" style="height: 44px; width: 44px; min-width: 44px; border-radius: 10px; display: inline-flex; align-items: center; justify-content: center;" title="إعادة تعيين">
                  <i class="bi bi-x-lg"></i>
                </a>
              @endif
            </div>
          </div>
        </div>
      </form>
      <div class="entity-grid">
        @foreach($workspaces as $w)
          <div class="entity-card">
            <a href="{{ route('workspaces.show', $w) }}" class="stretched-link"></a>
            <div class="entity-header">
              <div class="entity-title">
                <div class="entity-chip">{{ mb_substr($w->name, 0, 1) }}</div>
                <div>
                  <div class="fw-bold">{{ $w->name }}</div>
                  <div class="small" style="opacity:.9">{{ $w->location ?? '—' }}</div>
                </div>
              </div>
              <span
                class="badge {{ $w->status === 'available' ? 'bg-success' : ($w->status === 'busy' ? 'bg-danger' : 'bg-warning') }}">
                {{ $w->status === 'available' ? 'متاح' : ($w->status === 'busy' ? 'مشغول' : 'صيانة') }}
              </span>
            </div>
            <div class="entity-body">
              <div class="kv"><span class="text-muted small">السعة</span><span
                  class="fw-bold">{{ $w->capacity ?? '—' }}</span></div>
              <div class="kv"><span class="text-muted small">سعر الساعة</span><span
                  class="fw-bold">{{ $w->price_per_hour ?? '—' }}</span></div>
              @if($w->manager)
                <div class="kv"><span class="text-muted small">المدير</span><span
                    class="fw-bold text-primary">{{ $w->manager->name }}</span></div>
              @endif
              @if($w->amenities)
                <div class="mt-2 pt-2 border-top small text-muted text-truncate"><i class="bi bi-stars text-warning"></i>
                  {{ $w->amenities }}</div>
              @endif
            </div>
            <div class="entity-actions">
              <a class="btn btn-outline-primary btn-sm" href="{{ route('workspaces.show', $w) }}">عرض</a>
              @if($w->pendingRequest)
                  <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-2 py-1 rounded-pill small ms-auto">
                      <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                  </span>
              @else
                  <a class="btn btn-outline-secondary btn-sm" href="{{ route('workspaces.edit', $w) }}">تعديل</a>
                  <form method="POST" action="{{ route('workspaces.destroy', $w) }}" onsubmit="return confirm('حذف المساحة؟');">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger btn-sm">حذف</button>
                  </form>
              @endif
            </div>
          </div>
        @endforeach
      </div>
      <div class="mt-3">{{ $workspaces->links() }}</div>
    </div>

    <div class="col-lg-4">
      <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent py-3 border-0">
          <h5 class="mb-0 fw-bold"><i class="bi bi-calendar-event text-primary me-2"></i> جدول اليوم</h5>
        </div>
        <div class="card-body p-0">
          @forelse($todayRentals as $rental)
            <div class="p-3 border-bottom {{ $loop->last ? 'border-0' : '' }}">
              <div class="d-flex justify-content-between mb-1">
                <span class="fw-bold text-dark">{{ $rental->workspace->name }}</span>
                <span
                  class="badge bg-{{ $rental->status == 'confirmed' ? 'success' : ($rental->status == 'pending' ? 'warning' : 'primary') }}">{{ $rental->status }}</span>
              </div>
              <div class="small text-muted mb-2"><i class="bi bi-clock"></i> {{ $rental->start_time->format('H:i') }} -
                {{ $rental->end_time->format('H:i') }}
              </div>
              <div class="d-flex align-items-center gap-2">
                <div class="avatar-sm bg-secondary-subtle rounded-circle d-flex align-items-center justify-content-center"
                  style="width:24px;height:24px"><i class="bi bi-person small"></i></div>
                <span class="small">{{ $rental->renter_name }}</span>
              </div>
            </div>
          @empty
            <div class="text-center py-5 text-muted">
              <i class="bi bi-calendar-x display-6 d-block mb-2 opacity-50"></i>
              لا توجد حجوزات لليوم
            </div>
          @endforelse
        </div>
      </div>
    </div>
  </div>

@endsection

