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
              'pending' => 'bg-info-subtle text-info',
              'new' => 'bg-info-subtle text-info',
              'under_review' => 'bg-warning-subtle text-warning',
              'accepted' => 'bg-success-subtle text-success',
              'rejected' => 'bg-danger-subtle text-danger',
              'archived_improved' => 'bg-secondary-subtle text-secondary',
              'archived_deceased' => 'bg-dark text-white',
              default => 'bg-secondary-subtle text-secondary'
            };
            $statusText = match ($beneficiary->status) {
              'pending' => 'تحت التقديم',
              'new' => 'جديد',
              'under_review' => 'تحت المراجعة',
              'accepted' => 'مقبول',
              'rejected' => 'مرفوض',
              'archived_improved' => 'أرشيف — تحسن / شفاء',
              'archived_deceased' => 'أرشيف — توفي',
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
                      'monthly' => 'كفالة شهرية',
                      'one_time' => 'مساعدة مؤقتة',
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
            <div class="col-md-6"><div class="info-row"><span class="info-label">الهاتف الإضافي</span><span class="info-value font-monospace">{{ $beneficiary->backup_phone ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">الرقم القومي</span><span class="info-value font-monospace">{{ $beneficiary->national_id ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">رقم الفيزا</span><span class="info-value font-monospace">{{ $beneficiary->visa_card_number ?? '—' }}</span></div></div>
            <div class="col-md-6"><div class="info-row"><span class="info-label">المشروع</span><span class="info-value">{{ $beneficiary->project?->name ?? '—' }}</span></div></div>
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
            @if(in_array($beneficiary->status, ['rejected', 'archived_improved', 'archived_deceased'], true))
              <div class="col-12"><div class="alert alert-secondary mb-0"><strong>سبب الحالة:</strong> {{ $beneficiary->status === 'rejected' ? ($beneficiary->rejection_reason ?? '—') : ($beneficiary->archived_reason ?? '—') }}</div></div>
            @endif
          </div>
        </div>
      </div>

      @php($activeFamilyMembers = $beneficiary->familyMembers->where('active', true))
      @if($activeFamilyMembers->isNotEmpty())
        <div class="card mb-4">
          <div class="card-body">
            <div class="section-title mb-3"><i class="bi bi-people-fill"></i><h5 class="mb-0">ملفات أفراد الأسرة</h5></div>
            <div class="alert alert-info py-2"><i class="bi bi-shield-lock me-1"></i> كل فرد ملف مستقل، والكفلاء الظاهرون أمامه مرتبطون بهذا الفرد فقط.</div>
            <div class="row g-3">
              @foreach($activeFamilyMembers as $member)
                <div class="col-md-6">
                  <div class="border rounded-3 p-3 h-100">
                    <div class="d-flex justify-content-between align-items-start gap-2 mb-3">
                      <div><div class="fw-bold fs-6">{{ $member->full_name }}</div><div class="small text-muted">{{ $member->relationship_label }} @if($member->is_patient) — مريض @endif</div></div>
                      <div class="d-flex align-items-center gap-1">
                        <span class="badge bg-primary-subtle text-primary font-monospace">{{ $member->code }}</span>
                        <a href="{{ route('beneficiary-family-members.show', $member) }}" class="btn btn-sm btn-outline-primary" title="فتح الملف المستقل"><i class="bi bi-box-arrow-up-left"></i></a>
                      </div>
                    </div>
                    <div class="row g-2 small mb-3">
                      <div class="col-6"><span class="text-muted">العمر:</span> {{ $member->birth_date ? $member->birth_date->age . ' سنة' : ($member->age ? $member->age . ' سنة' : '—') }}</div>
                      <div class="col-6"><span class="text-muted">الميلاد:</span> {{ optional($member->birth_date)->format('Y-m-d') ?? '—' }}</div>
                      <div class="col-6"><span class="text-muted">التعليم:</span> {{ $member->education_level ?? '—' }}</div>
                      <div class="col-6"><span class="text-muted">الكفالة:</span> {{ $member->sponsorship_amount ? number_format((float)$member->sponsorship_amount, 2) . ' ج' : '—' }}</div>
                      @if($member->phone)<div class="col-12"><span class="text-muted">الهاتف:</span> {{ $member->phone }}</div>@endif
                      @if($member->case_details)<div class="col-12"><span class="text-muted">التفاصيل:</span> {{ $member->case_details }}</div>@endif
                    </div>
                    <div class="border-top pt-2">
                      <div class="small fw-bold mb-2">الكفلاء / المتبرعون</div>
                      <div class="d-flex flex-wrap gap-1">
                        @forelse($member->sponsors as $sponsor)
                          <a href="{{ route('donors.show', $sponsor) }}" class="badge bg-success-subtle text-success text-decoration-none p-2"><i class="bi bi-person-heart me-1"></i>{{ $sponsor->name }}</a>
                        @empty
                          <span class="small text-muted">لم يتم ربط كافل بهذا الفرد حتى الآن.</span>
                        @endforelse
                      </div>
                    </div>
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        </div>
      @endif

      {{-- Allocation & Sponsorship --}}
      @if($beneficiary->allocation_type || $beneficiary->sponsors->isNotEmpty())
        <div class="card mb-4">
          <div class="card-body">
            <div class="section-title mb-3">
              <i class="bi bi-diagram-3"></i>
              <h5 class="mb-0">التخصيص والكفالة</h5>
            </div>
            <div class="row g-3">
              @if($beneficiary->allocation_type)
                <div class="col-md-6">
                  <div class="info-row align-items-start">
                    <span class="info-label">نوع التخصيص</span>
                    <span class="info-value">{{ $beneficiary->allocation_type }}</span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="info-row align-items-start">
                    <span class="info-label">المستفيدون</span>
                    <span class="info-value d-flex flex-wrap gap-1">
                      @forelse($beneficiary->allocatedBeneficiaries as $allocatedBeneficiary)
                        <a href="{{ route('beneficiaries.show', $allocatedBeneficiary) }}" class="badge bg-primary-subtle text-primary text-decoration-none">
                          {{ $allocatedBeneficiary->full_name }}
                        </a>
                      @empty
                        —
                      @endforelse
                    </span>
                  </div>
                </div>
              @endif
              @if($beneficiary->sponsors->isNotEmpty())
                <div class="col-12">
                  <div class="info-row align-items-start">
                    <span class="info-label">المتبرعون / الكفلاء</span>
                    <span class="info-value d-flex flex-wrap gap-2">
                      @foreach($beneficiary->sponsors as $sponsor)
                        <a href="{{ route('donors.show', $sponsor) }}" class="badge bg-success-subtle text-success text-decoration-none p-2">
                          <i class="bi bi-person-heart me-1"></i>{{ $sponsor->name }} — {{ $sponsor->code ?? ('DON-'.$sponsor->id) }}
                        </a>
                      @endforeach
                    </span>
                  </div>
                </div>
              @endif
            </div>
          </div>
        </div>
      @endif

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


