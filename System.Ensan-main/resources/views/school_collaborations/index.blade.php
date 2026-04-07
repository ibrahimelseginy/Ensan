@extends('layouts.app')

@section('title', 'تعاونات المدارس')
@section('styles')
<style>
    /* Premium Modal Styling */
    .btn-save-premium {
        background-color: #00d1b2 !important;
        border: none !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 10px 25px !important;
        border-radius: 8px !important;
    }
    .btn-cancel-premium {
        background-color: #363636 !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 10px 20px !important;
        border-radius: 8px !important;
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
@endsection

@section('content')
<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-building text-primary me-2"></i>تعاونات المدارس
        </h3>
        <p class="text-muted small mb-0 mt-1">إدارة تعاونات المحلات مع المدارس</p>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i> إضافة جديد
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3">المحل</th>
                        <th>العنوان</th>
                        <th>رقم التليفون</th>
                        <th>الخصم</th>
                        <th>التعاملات</th>
                        <th>الحملة</th>
                        <th>ملاحظات على المحل</th>
                        <th class="text-center">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collaborations as $item)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $item->shop_name }}</td>
                            <td>{{ $item->address ?? '—' }}</td>
                            <td>{{ $item->phone ?? '—' }}</td>
                            <td>{{ $item->discount ?? '—' }}</td>
                            <td>{{ $item->transactions ?? '—' }}</td>
                            <td>{{ $item->campaign ?? '—' }}</td>
                            <td>{{ $item->notes ?? '—' }}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <form method="POST" action="{{ route('school-collaborations.destroy', $item) }}" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">لا توجد سجلات.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($collaborations, 'hasPages') && $collaborations->hasPages())
        <div class="card-footer bg-white pt-3 pb-1 border-top-0">
            {{ $collaborations->links() }}
        </div>
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="background-color: #0b0e14 !important; opacity: 1 !important; backdrop-filter: none !important; -webkit-backdrop-filter: none !important;">
            <form method="POST" action="{{ route('school-collaborations.store') }}">
                @csrf
                <div class="modal-header border-0 bg-primary text-white" style="background-color: #0066ff !important;">
                    <h5 class="modal-title fw-bold">إضافة تعاون مدرسة جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-body" style="background-color: #0b0e14 !important; color: white !important;">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المحل <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="address" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم التليفون</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الخصم</label>
                            <input type="text" name="discount" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">التعاملات</label>
                            <input type="text" name="transactions" class="form-control">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحملة</label>
                            <input type="text" name="campaign" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات على المحل</label>
                        <textarea name="notes" class="form-control" rows="3" style="background-color: #121212 !important; border: 1px solid #333 !important; color: white !important;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body" style="background-color: #0b0e14 !important;">
                    <button type="submit" class="btn btn-save-premium px-4">حفظ</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($collaborations as $item)
<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content shadow-lg border-0" style="background-color: var(--bs-body-bg) !important; opacity: 1 !important; z-index: 1055;">
            <form method="POST" action="{{ route('school-collaborations.update', $item) }}">
                @csrf
                @method('PUT')
                <div class="modal-header border-0 bg-primary text-white" style="background-color: var(--bs-primary) !important;">
                    <h5 class="modal-title fw-bold">تعديل: {{ $item->shop_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">المحل <span class="text-danger">*</span></label>
                            <input type="text" name="shop_name" class="form-control" value="{{ $item->shop_name }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">العنوان</label>
                            <input type="text" name="address" class="form-control" value="{{ $item->address }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">رقم التليفون</label>
                            <input type="text" name="phone" class="form-control" value="{{ $item->phone }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الخصم</label>
                            <input type="text" name="discount" class="form-control" value="{{ $item->discount }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">التعاملات</label>
                            <input type="text" name="transactions" class="form-control" value="{{ $item->transactions }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الحملة</label>
                            <input type="text" name="campaign" class="form-control" value="{{ $item->campaign }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات على المحل</label>
                        <textarea name="notes" class="form-control" rows="3" style="background-color: #121212 !important; border: 1px solid #333 !important; color: white !important;">{{ $item->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body" style="background-color: #0b0e14 !important;">
                    <button type="submit" class="btn btn-save-premium px-4">تحديث</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection


