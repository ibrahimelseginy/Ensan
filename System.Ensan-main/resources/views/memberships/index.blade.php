@extends('layouts.app')

@section('title', 'العضويات')

@section('content')
<style>
    .membership-table-wrapper {
        overflow-x: auto;
        border-radius: 0.5rem;
    }
    .membership-table {
        min-width: 2200px;
        font-size: 0.85rem;
    }
    .membership-table thead th {
        background: linear-gradient(135deg, #1a5c2e 0%, #2d8a4e 100%);
        color: #fff;
        font-weight: 600;
        font-size: 0.8rem;
        padding: 10px 8px;
        white-space: nowrap;
        border: none;
        text-align: center;
        vertical-align: middle;
    }
    .membership-table tbody td {
        padding: 8px;
        vertical-align: middle;
        text-align: center;
        white-space: nowrap;
        border-bottom: 1px solid rgba(0,0,0,0.05);
    }
    .membership-table tbody tr:hover {
        background-color: rgba(45, 138, 78, 0.05);
    }
    .membership-table .sticky-col {
        position: sticky;
        right: 0;
        background: inherit;
        z-index: 2;
    }
    .membership-table thead .sticky-col {
        z-index: 3;
    }
    .membership-table tbody tr:nth-child(even) {
        background-color: rgba(0,0,0,0.015);
    }
    .membership-table tbody tr:nth-child(even) .sticky-col {
        background-color: #f9fafb;
    }
    .membership-table tbody tr:nth-child(odd) .sticky-col {
        background-color: #fff;
    }
    .membership-table tbody tr:hover .sticky-col {
        background-color: rgba(45, 138, 78, 0.05);
    }
    .badge-status {
        font-size: 0.75rem;
        padding: 4px 10px;
        border-radius: 20px;
    }

    /* Fix modal scrolling */
    #createModal .modal-dialog,
    [id^="editModal"] .modal-dialog {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    #createModal .modal-content,
    [id^="editModal"] .modal-content {
        max-height: 90vh;
        display: flex;
        flex-direction: column;
    }
    #createModal .modal-body,
    [id^="editModal"] .modal-body {
        overflow-y: auto !important;
        max-height: calc(90vh - 130px);
        flex: 1 1 auto;
    }
    #createModal .modal-header,
    #createModal .modal-footer,
    [id^="editModal"] .modal-header,
    [id^="editModal"] .modal-footer {
        flex-shrink: 0;
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

<div class="row align-items-center mb-4">
    <div class="col-md-6">
        <h3 class="fw-bold mb-0">
            <i class="bi bi-person-badge text-success me-2"></i>العضويات
        </h3>
        <p class="text-muted small mb-0 mt-1">إدارة الأعضاء والاشتراكات</p>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-success px-4" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i> إضافة جديد
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-body p-0">
        <div class="membership-table-wrapper">
            <table class="table table-hover align-middle mb-0 membership-table">
                <thead>
                    <tr>
                        <th class="sticky-col" style="min-width:100px;">إجراءات</th>
                        <th style="min-width:160px;">اسم الجهة / المكان</th>
                        <th style="min-width:110px;">نوع الجهة</th>
                        <th style="min-width:120px;">الخدمة المقدمة</th>
                        <th style="min-width:130px;">نسبة الخصم / العرض</th>
                        <th style="min-width:130px;">شروط الخصم</th>
                        <th style="min-width:120px;">الفئة المستفيدة</th>
                        <th style="min-width:130px;">طريقة تفعيل الخصم</th>
                        <th style="min-width:100px;">ساعات العمل</th>
                        <th style="min-width:120px;">عنوان الجهة</th>
                        <th style="min-width:110px;">موقع الجهة</th>
                        <th style="min-width:110px;">رقم التواصل</th>
                        <th style="min-width:130px;">رقم مسؤول التواصل</th>
                        <th style="min-width:140px;">البريد الإلكتروني</th>
                        <th style="min-width:130px;">اسم مسؤول الجهة</th>
                        <th style="min-width:120px;">اسم مصدر الجهة</th>
                        <th style="min-width:120px;">تاريخ بدء التعاون</th>
                        <th style="min-width:130px;">تاريخ انتهاء التعاون</th>
                        <th style="min-width:100px;">حالة التعاون</th>
                        <th style="min-width:100px;">درجة الأولوية</th>
                        <th style="min-width:110px;">عدد المستفيدين</th>
                        <th style="min-width:140px;">تقييم المتعاملين للجهة</th>
                        <th style="min-width:140px;">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($memberships as $item)
                        <tr>
                            <td class="sticky-col text-nowrap">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                    data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="تعديل">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <form method="POST" action="{{ route('memberships.destroy', $item) }}" class="d-inline"
                                    onsubmit="return confirm('هل أنت متأكد من حذف {{ $item->entity_name }}؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="fw-bold text-start">{{ $item->entity_name }}</td>
                            <td>{{ $item->entity_type ?? '—' }}</td>
                            <td>{{ $item->service_provided ?? '—' }}</td>
                            <td>{{ $item->discount_percentage ?? '—' }}</td>
                            <td>{{ Str::limit($item->discount_conditions, 30) ?? '—' }}</td>
                            <td>{{ $item->beneficiary_category ?? '—' }}</td>
                            <td>{{ $item->discount_activation_method ?? '—' }}</td>
                            <td>{{ $item->working_hours ?? '—' }}</td>
                            <td>{{ $item->entity_address ?? '—' }}</td>
                            <td>{{ $item->entity_location ?? '—' }}</td>
                            <td dir="ltr">{{ $item->contact_number ?? '—' }}</td>
                            <td dir="ltr">{{ $item->contact_person_number ?? '—' }}</td>
                            <td dir="ltr">{{ $item->email ?? '—' }}</td>
                            <td>{{ $item->entity_contact_name ?? '—' }}</td>
                            <td>{{ $item->entity_source_name ?? '—' }}</td>
                            <td>{{ $item->cooperation_start_date ? $item->cooperation_start_date->format('Y-m-d') : '—' }}</td>
                            <td>{{ $item->cooperation_end_date ? $item->cooperation_end_date->format('Y-m-d') : '—' }}</td>
                            <td>
                                @if($item->cooperation_status)
                                    <span class="badge badge-status bg-info bg-opacity-10 text-info">{{ $item->cooperation_status }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                @if($item->priority_level)
                                    <span class="badge badge-status bg-warning bg-opacity-10 text-warning">{{ $item->priority_level }}</span>
                                @else
                                    —
                                @endif
                            </td>
                            <td>{{ $item->beneficiaries_count ?? '—' }}</td>
                            <td>{{ $item->entity_rating ?? '—' }}</td>
                            <td>{{ Str::limit($item->notes, 30) ?? '—' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="23" class="text-center text-muted py-5">
                                <i class="bi bi-inbox fs-1 d-block mb-2 opacity-50"></i>
                                لا توجد سجلات حتى الآن
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if(method_exists($memberships, 'hasPages') && $memberships->hasPages())
        <div class="card-footer bg-white pt-3 pb-1 border-top-0">
            {{ $memberships->links() }}
        </div>
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0" style="background-color: var(--bs-body-bg) !important; opacity: 1 !important; z-index: 1055;">
            <form method="POST" action="{{ route('memberships.store') }}">
                @csrf
                <div class="modal-header bg-success text-white border-0" style="background-color: var(--bs-success) !important;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i>إضافة عضوية جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-body p-4">
                    {{-- معلومات الجهة الأساسية --}}
                    <h6 class="fw-bold text-success mb-3 border-bottom pb-2"><i class="bi bi-building me-1"></i> معلومات الجهة</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">اسم الجهة / المكان <span class="text-danger">*</span></label>
                            <input type="text" name="entity_name" class="form-control" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">نوع الجهة</label>
                            <input type="text" name="entity_type" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">الخدمة المقدمة</label>
                            <input type="text" name="service_provided" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">عنوان الجهة</label>
                            <input type="text" name="entity_address" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">موقع الجهة</label>
                            <input type="text" name="entity_location" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">ساعات العمل</label>
                            <input type="text" name="working_hours" class="form-control">
                        </div>
                    </div>

                    {{-- معلومات الخصم --}}
                    <h6 class="fw-bold text-success mb-3 mt-2 border-bottom pb-2"><i class="bi bi-percent me-1"></i> معلومات الخصم</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">نسبة الخصم / العرض</label>
                            <input type="text" name="discount_percentage" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">الفئة المستفيدة</label>
                            <input type="text" name="beneficiary_category" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">طريقة تفعيل الخصم</label>
                            <input type="text" name="discount_activation_method" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">شروط الخصم</label>
                        <textarea name="discount_conditions" class="form-control" rows="2"></textarea>
                    </div>

                    {{-- معلومات التواصل --}}
                    <h6 class="fw-bold text-success mb-3 mt-2 border-bottom pb-2"><i class="bi bi-telephone me-1"></i> معلومات التواصل</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">رقم التواصل</label>
                            <input type="text" name="contact_number" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">رقم مسؤول التواصل</label>
                            <input type="text" name="contact_person_number" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">اسم مسؤول الجهة</label>
                            <input type="text" name="entity_contact_name" class="form-control">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">اسم مصدر الجهة</label>
                            <input type="text" name="entity_source_name" class="form-control">
                        </div>
                    </div>

                    {{-- معلومات التعاون --}}
                    <h6 class="fw-bold text-success mb-3 mt-2 border-bottom pb-2"><i class="bi bi-handshake me-1"></i> معلومات التعاون</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">تاريخ بدء التعاون</label>
                            <input type="date" name="cooperation_start_date" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">تاريخ انتهاء التعاون</label>
                            <input type="date" name="cooperation_end_date" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">حالة التعاون</label>
                            <input type="text" name="cooperation_status" class="form-control">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">درجة الأولوية</label>
                            <input type="text" name="priority_level" class="form-control">
                        </div>
                    </div>

                    {{-- التقييم --}}
                    <h6 class="fw-bold text-success mb-3 mt-2 border-bottom pb-2"><i class="bi bi-star me-1"></i> التقييم والمتابعة</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">عدد المستفيدين</label>
                            <input type="number" name="beneficiaries_count" class="form-control" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">تقييم المتعاملين للجهة</label>
                            <input type="text" name="entity_rating" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-lg me-1"></i> حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($memberships as $item)
<div class="modal fade" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0" style="background-color: var(--bs-body-bg) !important; opacity: 1 !important; z-index: 1055;">
            <form method="POST" action="{{ route('memberships.update', $item) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white border-0" style="background-color: var(--bs-primary) !important;">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>تعديل: {{ $item->entity_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body bg-body p-4">
                    {{-- معلومات الجهة الأساسية --}}
                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="bi bi-building me-1"></i> معلومات الجهة</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">اسم الجهة / المكان <span class="text-danger">*</span></label>
                            <input type="text" name="entity_name" class="form-control" value="{{ $item->entity_name }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">نوع الجهة</label>
                            <input type="text" name="entity_type" class="form-control" value="{{ $item->entity_type }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">الخدمة المقدمة</label>
                            <input type="text" name="service_provided" class="form-control" value="{{ $item->service_provided }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">عنوان الجهة</label>
                            <input type="text" name="entity_address" class="form-control" value="{{ $item->entity_address }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">موقع الجهة</label>
                            <input type="text" name="entity_location" class="form-control" value="{{ $item->entity_location }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">ساعات العمل</label>
                            <input type="text" name="working_hours" class="form-control" value="{{ $item->working_hours }}">
                        </div>
                    </div>

                    {{-- معلومات الخصم --}}
                    <h6 class="fw-bold text-primary mb-3 mt-2 border-bottom pb-2"><i class="bi bi-percent me-1"></i> معلومات الخصم</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">نسبة الخصم / العرض</label>
                            <input type="text" name="discount_percentage" class="form-control" value="{{ $item->discount_percentage }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">الفئة المستفيدة</label>
                            <input type="text" name="beneficiary_category" class="form-control" value="{{ $item->beneficiary_category }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">طريقة تفعيل الخصم</label>
                            <input type="text" name="discount_activation_method" class="form-control" value="{{ $item->discount_activation_method }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">شروط الخصم</label>
                        <textarea name="discount_conditions" class="form-control" rows="2">{{ $item->discount_conditions }}</textarea>
                    </div>

                    {{-- معلومات التواصل --}}
                    <h6 class="fw-bold text-primary mb-3 mt-2 border-bottom pb-2"><i class="bi bi-telephone me-1"></i> معلومات التواصل</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">رقم التواصل</label>
                            <input type="text" name="contact_number" class="form-control" value="{{ $item->contact_number }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">رقم مسؤول التواصل</label>
                            <input type="text" name="contact_person_number" class="form-control" value="{{ $item->contact_person_number }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">البريد الإلكتروني</label>
                            <input type="email" name="email" class="form-control" value="{{ $item->email }}">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">اسم مسؤول الجهة</label>
                            <input type="text" name="entity_contact_name" class="form-control" value="{{ $item->entity_contact_name }}">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">اسم مصدر الجهة</label>
                            <input type="text" name="entity_source_name" class="form-control" value="{{ $item->entity_source_name }}">
                        </div>
                    </div>

                    {{-- معلومات التعاون --}}
                    <h6 class="fw-bold text-primary mb-3 mt-2 border-bottom pb-2"><i class="bi bi-handshake me-1"></i> معلومات التعاون</h6>
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">تاريخ بدء التعاون</label>
                            <input type="date" name="cooperation_start_date" class="form-control" value="{{ $item->cooperation_start_date ? $item->cooperation_start_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">تاريخ انتهاء التعاون</label>
                            <input type="date" name="cooperation_end_date" class="form-control" value="{{ $item->cooperation_end_date ? $item->cooperation_end_date->format('Y-m-d') : '' }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">حالة التعاون</label>
                            <input type="text" name="cooperation_status" class="form-control" value="{{ $item->cooperation_status }}">
                        </div>
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">درجة الأولوية</label>
                            <input type="text" name="priority_level" class="form-control" value="{{ $item->priority_level }}">
                        </div>
                    </div>

                    {{-- التقييم --}}
                    <h6 class="fw-bold text-primary mb-3 mt-2 border-bottom pb-2"><i class="bi bi-star me-1"></i> التقييم والمتابعة</h6>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">عدد المستفيدين</label>
                            <input type="number" name="beneficiaries_count" class="form-control" value="{{ $item->beneficiaries_count }}" min="0">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label fw-semibold">تقييم المتعاملين للجهة</label>
                            <input type="text" name="entity_rating" class="form-control" value="{{ $item->entity_rating }}">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $item->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-body">
                    <button type="button" class="btn btn-light px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check-lg me-1"></i> تحديث</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection


