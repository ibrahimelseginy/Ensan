@extends('layouts.app')
@section('content')
<div class="container-fluid">
  {{-- Page Header --}}
  <div class="page-header">
    <div class="d-flex align-items-center gap-3">
      <div class="avatar avatar-lg avatar-primary">
        {{ mb_substr($beneficiary->full_name, 0, 1) }}
      </div>
      <div>
        <h4 class="mb-1">{{ $beneficiary->full_name }}</h4>
        <div class="d-flex gap-2">
          @php
            $statusClass = match ($beneficiary->status) {
              'new' => 'bg-info-subtle text-info',
              'under_review' => 'bg-warning-subtle text-warning',
              'accepted' => 'bg-success-subtle text-success',
              default => 'bg-secondary-subtle text-secondary'
            };
            $statusText = match ($beneficiary->status) {
              'new' => 'جديد',
              'under_review' => 'تحت المراجعة',
              'accepted' => 'مقبول',
              default => $beneficiary->status
            };
          @endphp
          <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
          @if(!empty($isDup) && $isDup)
            <span class="badge bg-danger">مكرر محتمل</span>
          @endif
        </div>
      </div>
    </div>
    <div class="btn-group">
      <button onclick="window.print()" class="btn btn-outline-primary">
        <i class="bi bi-printer me-1"></i> طباعة
      </button>
      @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
        <a class="btn btn-outline-primary" href="{{ route('beneficiaries.edit', $beneficiary) }}">
          <i class="bi bi-pencil me-1"></i> تعديل
        </a>
      @else
        <a class="btn btn-outline-warning" href="{{ route('beneficiaries.edit', $beneficiary) }}">
          <i class="bi bi-pencil me-1"></i> طلب تعديل
        </a>
      @endif
      <a href="{{ route('beneficiaries.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-right me-1"></i> رجوع
      </a>
    </div>
  </div>

  {{-- Success/Error Messages --}}
  @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif
  @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  @endif

  <div class="row g-4">
    {{-- Main Info --}}
    <div class="col-lg-8">
      <div class="card mb-4">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-person-lines-fill"></i>
            <h5 class="mb-0">المعلومات الأساسية</h5>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">كود المستفيد</span>
                <span class="info-value font-monospace">{{ $beneficiary->code ?? '—' }}</span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">الحالة</span>
                <span class="info-value">
                  <span class="badge {{ $statusClass }}">{{ $statusText }}</span>
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">نوع المساعدة</span>
                <span class="info-value">
                  @php
                    $typeText = match ($beneficiary->assistance_type) {
                      'financial' => 'مالية',
                      'in_kind' => 'عينية',
                      'service' => 'خدمية',
                      default => $beneficiary->assistance_type
                    };
                  @endphp
                  {{ $typeText }}
                </span>
              </div>
            </div>
            <div class="col-md-6">
              <div class="info-row">
                <span class="info-label">الهاتف</span>
                <span class="info-value font-monospace">{{ $beneficiary->phone ?? '—' }}</span>
              </div>
            </div>
            <div class="col-12">
              <div class="info-row">
                <span class="info-label">العنوان</span>
                <span class="info-value">{{ $beneficiary->address ?? '—' }}</span>
              </div>
            </div>
            @if(!empty($beneficiary->notes))
              <div class="col-12">
                <div class="p-3 bg-light rounded">
                  <div class="text-muted small mb-2">ملاحظات</div>
                  <div>{{ $beneficiary->notes }}</div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Donations History --}}
      <div class="card mb-4">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-gift"></i>
            <h5 class="mb-0">سجل التبرعات الموجهة</h5>
          </div>
          @if(isset($donations) && $donations->count() > 0)
            <div class="table-responsive">
              <table class="table table-hover align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>المتبرع</th>
                    <th>القيمة</th>
                    <th>التاريخ</th>
                    <th>التفاصيل</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach($donations as $d)
                    <tr>
                      <td>{{ $d->id }}</td>
                      <td>
                        <a href="{{ route('donors.show', $d->donor_id) }}" class="text-decoration-none">
                          {{ $d->donor->name ?? 'فاعل خير' }}
                        </a>
                      </td>
                      <td>
                        <span
                          class="fw-bold">{{ $d->type == 'cash' ? number_format($d->amount, 2) : number_format($d->estimated_value, 2) }}</span>
                        <span class="text-muted small">{{ $d->currency }}</span>
                      </td>
                      <td>{{ $d->received_at?->format('Y-m-d') }}</td>
                      <td><a href="{{ route('donations.show', $d) }}" class="btn btn-sm btn-light border"><i
                            class="bi bi-eye"></i></a></td>
                    </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          @else
            <p class="text-muted text-center py-3">لا توجد تبرعات مسجلة لهذا المستفيد</p>
          @endif
        </div>
      </div>

      {{-- Attachments Section --}}
      <div class="card">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-paperclip"></i>
            <h5 class="mb-0">المرفقات</h5>
          </div>

          <form method="POST" action="{{ route('attachments.store') }}" enctype="multipart/form-data" class="mb-3">
            @csrf
            <!-- Use PHP class syntax for clean namespace -->
            <input type="hidden" name="entity_type" value="{{ \App\Models\Beneficiary::class }}">
            <input type="hidden" name="entity_id" value="{{ $beneficiary->id }}">
            <div class="d-flex gap-2 align-items-center">
              <div class="flex-grow-1 input-icon-wrapper">
                <input type="file" name="file" class="form-control" required>
                <i class="bi bi-upload input-icon"></i>
              </div>
              <button class="btn btn-primary">
                <i class="bi bi-cloud-upload me-1"></i> رفع
              </button>
            </div>
          </form>

          @php($atts = $beneficiary->attachments)
          @if($atts->count() > 0)
            <div class="list-group">
              @foreach($atts as $a)
                <div class="list-group-item d-flex justify-content-between align-items-center">
                  <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark text-primary"></i>
                    <a href="{{ $a->image_url }}" target="_blank">{{ basename($a->path) }}</a>
                  </div>
                  <form method="POST" action="{{ route('attachments.destroy', $a) }}">
                    @csrf @method('DELETE')
                    <button class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </div>
              @endforeach
            </div>
          @else
            <div class="empty-state py-4">
              <i class="bi bi-folder2-open"></i>
              <p class="mb-0">لا توجد مرفقات</p>
            </div>
          @endif
        </div>
      </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
      {{-- Quick Actions --}}
      <div class="card">
        <div class="card-body">
          <div class="section-title mb-3">
            <i class="bi bi-lightning"></i>
            <h6 class="mb-0">إجراءات سريعة</h6>
          </div>
          <div class="d-grid gap-2">
            @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
              <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="btn btn-outline-primary">
                <i class="bi bi-pencil me-1"></i> تعديل البيانات
              </a>
            @else
              <a href="{{ route('beneficiaries.edit', $beneficiary) }}" class="btn btn-outline-warning">
                <i class="bi bi-pencil me-1"></i> طلب تعديل بيانات
              </a>
            @endif
            <form method="POST" action="{{ route('beneficiaries.destroy', $beneficiary) }}"
              onsubmit="return confirm('{{ optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager') ? 'هل أنت متأكد من حذف هذا المستفيد؟' : 'هل أنت متأكد من طلب حذف هذا المستفيد؟' }}');">
              @csrf @method('DELETE')
              @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
                <button class="btn btn-outline-danger w-100">
                  <i class="bi bi-trash me-1"></i> حذف المستفيد
                </button>
              @else
                 <button class="btn btn-outline-warning w-100">
                  <i class="bi bi-x-circle me-1"></i> طلب إلغاء المستفيد
                </button>
              @endif
            </form>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- Print Styles --}}
<style>
  @media print {
    /* Hide navigation and buttons */
    .navbar,
    .sidebar-fixed,
    .btn-group,
    .page-header .btn,
    form,
    body {
      padding-top: 0 !important;
    }

    /* Hide action buttons and forms */
    .btn-group,
    form {
      display: none !important;
    }

    /* Optimize layout for print */
    .container-fluid {
      max-width: 100%;
    }

    .card {
      border: 1px solid #dee2e6 !important;
      box-shadow: none !important;
      page-break-inside: avoid;
    }

    /* Add header for print */
    .page-header::before {
      content: "تقرير المستفيد - مؤسسة إنسان";
      display: block;
      text-align: center;
      font-size: 1.5rem;
      font-weight: bold;
      margin-bottom: 1rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid #10b981;
    }

    /* Ensure table fits on page */
    table {
      font-size: 0.85rem;
    }

    /* Preserve colors for badges */
    .badge,
    .bg-primary,
    .bg-success-subtle,
    .bg-info-subtle,
    .bg-warning-subtle {
      -webkit-print-color-adjust: exact;
      print-color-adjust: exact;
    }

    /* Hide action buttons in table */
    table td:last-child {
      display: none;
    }
    table th:last-child {
      display: none;
    }

    /* Adjust sidebar for print */
    .col-lg-4 {
      display: none;
    }
    .col-lg-8 {
      width: 100% !important;
      max-width: 100% !important;
    }
  }
</style>

@endsection
