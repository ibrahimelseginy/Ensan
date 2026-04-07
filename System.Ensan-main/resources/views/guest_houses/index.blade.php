@extends('layouts.app')
@section('content')

  {{-- Page Header --}}
  <div class="d-flex justify-content-between align-items-center mb-4">
    <div>
      <h4 class="fw-bold text-dark mb-1">
        <i class="bi bi-house text-primary me-2"></i>إدارة دار الضيافة
      </h4>
    </div>
    <div>
      <a href="{{ route('guest-houses.create') }}" class="btn btn-primary shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> إضافة دار
      </a>
    </div>
  </div>

  {{-- Filter Card --}}
  <div class="card border-0 shadow-sm mb-4 p-4">
    <form method="GET">
      <div class="row g-3 align-items-end">
        <div class="col-md-5">
          <label class="form-label">بحث بالاسم أو الموقع</label>
          <div class="input-icon-wrapper">
            <input name="q" value="{{ $q ?? '' }}" class="form-control" placeholder="طنطا، كفر الشيخ...">
            <i class="bi bi-search input-icon"></i>
          </div>
        </div>
        <div class="col-md-3">
          <label class="form-label">الحالة</label>
          <select name="status" class="form-select">
            <option value="">الكل</option>
            <option value="active" @selected(($status ?? '') === 'active')>نشط</option>
            <option value="archived" @selected(($status ?? '') === 'archived')>مؤرشف</option>
          </select>
        </div>
        <div class="col-md-2">
          <button class="btn btn-primary w-100">
            <i class="bi bi-funnel me-1"></i> تصفية
          </button>
        </div>
      </div>
    </form>
  </div>
  <div class="entity-grid">
    @foreach($houses as $h)
      <div class="entity-card">
        <a href="{{ route('guest-houses.show', $h) }}" class="stretched-link"></a>
        <div class="entity-header">
          <div class="entity-title">
            <div class="entity-chip">{{ mb_substr($h->name, 0, 1) }}</div>
            <div>
              <div class="fw-bold">{{ $h->name }}</div>
              <div class="small" style="opacity:.9">{{ $h->location ?? '—' }}</div>
            </div>
          </div>
          <span
            class="badge {{ $h->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ $h->status === 'active' ? 'نشط' : 'مؤرشف' }}</span>
        </div>
        <div class="entity-body">
          <div class="kv"><span class="text-muted small">السعة</span><span class="fw-bold">{{ $h->capacity ?? '—' }}</span>
          </div>
          <div class="kv"><span class="text-muted small">الهاتف</span><span class="fw-bold">{{ $h->phone ?? '—' }}</span>
          </div>
          @if($h->manager)
            <div class="kv"><span class="text-muted small">المدير</span><span
                class="fw-bold text-primary">{{ $h->manager->name }}</span></div>
          @endif
        </div>
        <div class="entity-actions">
          <a class="btn btn-outline-primary btn-sm" href="{{ route('guest-houses.show', $h) }}">عرض</a>
          @if(isset($h->pendingRequest) && $h->pendingRequest)
              <span class="badge bg-warning bg-opacity-10 text-warning d-flex align-items-center px-2 py-1 rounded-pill small ms-auto">
                  <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
              </span>
          @else
              <a class="btn btn-outline-secondary btn-sm" href="{{ route('guest-houses.edit', $h) }}">تعديل</a>
              <form method="POST" action="{{ route('guest-houses.destroy', $h) }}" onsubmit="return confirm('حذف الدار؟');">
                @csrf @method('DELETE')
                <button class="btn btn-outline-danger btn-sm">حذف</button>
              </form>
          @endif
        </div>
      </div>
    @endforeach
  </div>
  <div class="mt-3">{{ $houses->links() }}</div>
@endsection

