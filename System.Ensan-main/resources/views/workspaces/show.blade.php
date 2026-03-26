@extends('layouts.app')
@section('content')
  <style>
    .ws-hero {
      background: linear-gradient(135deg, #0d6efd, #00c2a8);
      padding: 2rem;
      border-radius: 16px;
      color: #fff;
      margin-bottom: 1.5rem;
      box-shadow: 0 10px 30px rgba(13, 110, 253, .15)
    }

    .ws-stat-card {
      background: var(--card-bg, #fff);
      border-radius: 12px;
      padding: 1rem;
      box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
      height: 100%
    }

    .ws-stat-val {
      font-size: 1.5rem;
      font-weight: 700;
      color: var(--primary)
    }

    .ws-stat-lbl {
      color: #6c757d;
      font-size: .875rem
    }

    .rental-card {
      background: var(--card-bg, #fff);
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, .05);
      margin-bottom: 1rem;
      border-right: 4px solid transparent
    }

    .rental-card.status-pending {
      border-right-color: #ffc107
    }

    .rental-card.status-confirmed {
      border-right-color: #198754
    }

    .rental-card.status-completed {
      border-right-color: #0d6efd
    }

    .rental-card.status-cancelled {
      border-right-color: #dc3545
    }
  </style>

  <div class="ws-hero">
    <div class="d-flex justify-content-between align-items-start">
      <div>
        <h2 class="fw-bold mb-1">{{ $workspace->name }}</h2>
        <p class="mb-2 opacity-75"><i class="bi bi-geo-alt"></i> {{ $workspace->location ?? 'غير محدد' }}</p>
        <div class="d-flex gap-2">
          <span class="badge bg-primary-subtle text-primary">{{ $workspace->status }}</span>
          <span class="badge bg-secondary-subtle text-body"><i class="bi bi-people"></i> {{ $workspace->capacity }}
            شخص</span>
        </div>
        @if($workspace->amenities)
          <div class="mt-2 d-flex gap-2 flex-wrap">
            @foreach(explode(',', $workspace->amenities) as $amenity)
              <span class="badge bg-body-tertiary border border-secondary-subtle text-body">{{ trim($amenity) }}</span>
            @endforeach
          </div>
        @endif
      </div>
      <div class="d-flex gap-2">
        <a href="{{ route('workspaces.edit', $workspace) }}" class="btn btn-secondary-subtle"><i class="bi bi-pencil"></i>
          تعديل</a>
        <a href="{{ route('workspaces.index') }}" class="btn btn-outline-light">رجوع</a>
      </div>
    </div>
  </div>

  <div class="row g-4 mb-4">
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val">{{ $workspace->rentals()->count() }}</div>
        <div class="ws-stat-lbl">إجمالي الحجوزات</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val text-success">
          {{ number_format($totalRevenue, 2) }} ج.م
        </div>
        <div class="ws-stat-lbl">الإيرادات (المؤكدة)</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val text-danger">
          {{ number_format($totalExpenses, 2) }} ج.م
        </div>
        <div class="ws-stat-lbl">إجمالي المصروفات</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val {{ $netBalance >= 0 ? 'text-success' : 'text-danger' }}">
          {{ number_format($netBalance, 2) }} ج.م
        </div>
        <div class="ws-stat-lbl">صافي الحسابات</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val">{{ $workspace->rentals()->where('status', 'confirmed')->count() }}</div>
        <div class="ws-stat-lbl">حجوزات مؤكدة</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val">{{ $workspace->price_per_hour }} ج.م</div>
        <div class="ws-stat-lbl">سعر الساعة</div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="ws-stat-card">
        <div class="ws-stat-val">{{ $workspace->price_per_day }} ج.م</div>
        <div class="ws-stat-lbl">سعر اليوم</div>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-header bg-transparent py-3">
          <h5 class="mb-0">سجل الحجوزات</h5>
        </div>
        <div class="card-body">
          @forelse($rentals as $rental)
            <div class="rental-card p-3 status-{{ $rental->status }}">
              <div class="d-flex justify-content-between align-items-start">
                <div>
                  <h6 class="fw-bold mb-1">{{ $rental->renter_name }}</h6>
                  <div class="small text-muted mb-1"><i class="bi bi-telephone"></i> {{ $rental->renter_phone }}</div>
                  <div class="small">
                    <i class="bi bi-clock"></i> {{ $rental->start_time->format('Y-m-d H:i') }}
                    <i class="bi bi-arrow-left mx-1"></i>
                    {{ $rental->end_time->format('Y-m-d H:i') }}
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold text-primary mb-1">{{ $rental->total_price }} ج.م</div>
                  <div
                    class="badge bg-{{ $rental->status == 'confirmed' ? 'success' : ($rental->status == 'pending' ? 'warning' : ($rental->status == 'completed' ? 'primary' : 'danger')) }} mb-2">
                    {{ $rental->status }}
                  </div>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-secondary-subtle dropdown-toggle" type="button"
                      data-bs-toggle="dropdown">إجراءات</button>
                    <ul class="dropdown-menu">
                      <li>
                        <form action="{{ route('workspaces.updateRentalStatus', [$workspace, $rental]) }}" method="POST">
                          @csrf @method('PATCH')
                          <input type="hidden" name="status" value="confirmed">
                          <button class="dropdown-item">تأكيد الحجز</button>
                        </form>
                      </li>
                      <li>
                        <form action="{{ route('workspaces.updateRentalStatus', [$workspace, $rental]) }}" method="POST">
                          @csrf @method('PATCH')
                          <input type="hidden" name="status" value="completed">
                          <button class="dropdown-item">اكتمل</button>
                        </form>
                      </li>
                      <li>
                        <form action="{{ route('workspaces.updateRentalStatus', [$workspace, $rental]) }}" method="POST">
                          @csrf @method('PATCH')
                          <input type="hidden" name="status" value="cancelled">
                          <button class="dropdown-item text-danger">إلغاء</button>
                        </form>
                      </li>
                      <li>
                        <hr class="dropdown-divider">
                      </li>
                      <li>
                        <form action="{{ route('workspaces.destroyRental', [$workspace, $rental]) }}" method="POST"
                          onsubmit="return confirm('حذف هذا الحجز؟');">
                          @csrf @method('DELETE')
                          <button class="dropdown-item text-danger">حذف نهائي</button>
                        </form>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
              @if($rental->notes)
                <div class="mt-2 small text-muted bg-body-tertiary p-2 rounded">{{ $rental->notes }}</div>
              @endif
            </div>
          @empty
            <div class="text-center py-4 text-muted">لا توجد حجوزات حتى الآن</div>
          @endforelse
          {{ $rentals->links() }}
        </div>
      </div>
    </div>

    <div class="col-lg-4">
      <div class="card">
        <div class="card-header bg-transparent py-3">
          <h5 class="mb-0">تسجيل تأجير القاعة</h5>
        </div>
        <div class="card-body">
          <form action="{{ route('workspaces.storeRental', $workspace) }}" method="POST">
            @csrf
            <div class="mb-3">
              <label class="form-label">اسم الجهة/الشخص المستأجر</label>
              <input type="text" name="renter_name" class="form-control" required
                placeholder="مثال: جمعية الخير، محمد أحمد...">
            </div>
            <div class="mb-3">
              <label class="form-label">رقم الهاتف</label>
              <input type="text" name="renter_phone" class="form-control">
            </div>
            <div class="mb-3">
              <label class="form-label">بداية الحجز</label>
              <input type="datetime-local" name="start_time" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">نهاية الحجز</label>
              <input type="datetime-local" name="end_time" class="form-control" required>
            </div>
            <div class="mb-3">
              <label class="form-label">التكلفة الإجمالية (ج.م)</label>
              <input type="number" step="0.01" name="total_price" class="form-control"
                placeholder="اترك فارغاً للحساب لاحقاً">
            </div>
            <div class="mb-3">
              <label class="form-label">حالة الحجز</label>
              <select name="status" class="form-select">
                <option value="pending">حجز مبدئي (معلق)</option>
                <option value="confirmed">مؤكد</option>
                <option value="completed">تم التنفيذ (مكتمل)</option>
              </select>
            </div>
            <div class="mb-3">
              <label class="form-label">تفاصيل النشاط / ملاحظات</label>
              <textarea name="notes" class="form-control" rows="3"
                placeholder="نوع النشاط، التجهيزات المطلوبة، إلخ..."></textarea>
            </div>
            <button class="btn btn-primary w-100"><i class="bi bi-calendar-check"></i> تسجيل الحجز</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection