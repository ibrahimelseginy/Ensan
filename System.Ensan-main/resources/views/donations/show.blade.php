@extends('layouts.app')
@section('content')
  <div class="container-fluid">
    {{-- Page Header --}}
    <div class="page-header">
      <h4 class="mb-0">
        <i class="bi bi-heart-fill text-danger"></i>
        تفاصيل التبرع
      </h4>
      <div class="btn-group">
        <a class="btn btn-outline-primary" href="{{ route('donations.edit', $donation) }}">
          <i class="bi bi-pencil me-1"></i> تعديل
        </a>
        <a href="{{ route('donations.index') }}" class="btn btn-outline-secondary">
          <i class="bi bi-arrow-right me-1"></i> رجوع
        </a>
      </div>
    </div>

    <div class="row g-4">
      {{-- Main Info Card --}}
      <div class="col-lg-8">
        <div class="card">
          <div class="card-body">
            <div class="section-title mb-3">
              <i class="bi bi-info-circle"></i>
              <h5 class="mb-0">معلومات التبرع</h5>
            </div>

            <div class="row g-3">
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">المتبرع</span>
                  <span class="info-value fw-bold">{{ $donation->donor?->name ?? 'فاعل خير' }}</span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">الحالة</span>
                  <span class="info-value">
                    @if($donation->status === 'cancelled')
                      <span class="badge bg-danger">ملغي</span>
                    @else
                      <span class="badge bg-success">نشط</span>
                    @endif
                  </span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">النوع</span>
                  <span class="info-value">
                    @if($donation->type === 'cash')
                      <span class="badge bg-success-subtle text-success">نقدي</span>
                    @else
                      <span class="badge bg-info-subtle text-info">عيني</span>
                    @endif
                  </span>
                </div>
              </div>

              {{-- Row 2: Amount/Value & Receipt --}}
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">{{ $donation->type === 'cash' ? 'المبلغ' : 'القيمة التقديرية' }}</span>
                  <span class="info-value">
                    <span class="fs-5 fw-bold {{ $donation->type === 'cash' ? 'text-success' : 'text-info' }}">
                      {{ number_format($donation->type === 'cash' ? $donation->amount : $donation->estimated_value, 2) }}
                    </span>
                    <small class="text-muted">{{ $donation->currency }}</small>
                  </span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">رقم الإيصال</span>
                  <span class="info-value font-monospace user-select-all">{{ $donation->receipt_number ?: '—' }}</span>
                </div>
              </div>

              {{-- Row 3: Payment Method / Warehouse --}}
              @if($donation->type === 'cash')
                <div class="col-md-6">
                  <div class="info-row">
                    <span class="info-label">طريقة الدفع</span>
                    <span class="info-value">
                      @php
                        $channelText = match ($donation->cash_channel) {
                          'instapay' => 'انستا باي',
                          'vodafone_cash' => 'فودافون كاش',
                          'delegate' => 'مندوب',
                          default => 'نقدي'
                        };
                      @endphp
                      {{ $channelText }}
                    </span>
                  </div>
                </div>
              @else
                <div class="col-md-6">
                  <div class="info-row">
                    <span class="info-label">المخزن</span>
                    <span class="info-value">{{ $donation->warehouse?->name ?? '—' }}</span>
                  </div>
                </div>
              @endif

              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">تاريخ الاستلام</span>
                  <span class="info-value">{{ optional($donation->received_at)->format('Y-m-d') ?? '—' }}</span>
                </div>
              </div>

              {{-- Row 4: Allocation (Project/Campaign) --}}
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">المشروع</span>
                  <span class="info-value">
                    @if($donation->project)
                      <a href="{{ route('projects.show', $donation->project) }}" class="text-decoration-none fw-bold">
                        <i class="bi bi-box-seam text-primary me-1"></i> {{ $donation->project->name }}
                      </a>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </span>
                </div>
              </div>
              <div class="col-md-6">
                <div class="info-row">
                  <span class="info-label">الحملة</span>
                  <span class="info-value">
                    @if($donation->campaign)
                      <a href="{{ route('campaigns.show', $donation->campaign) }}" class="text-decoration-none fw-bold">
                        <i class="bi bi-flag text-danger me-1"></i> {{ $donation->campaign->name }}
                      </a>
                    @else
                      <span class="text-muted">—</span>
                    @endif
                  </span>
                </div>
              </div>
            </div>

            @if($donation->allocation_note)
              <div class="mt-4 p-3 bg-light rounded">
                <div class="text-muted small mb-2">ملاحظات</div>
                <div>{{ $donation->allocation_note }}</div>
              </div>
            @endif

            @if($donation->status === 'cancelled')
              <div class="mt-4 p-4 rounded-4 border border-danger border-opacity-25 shadow-sm" style="background: rgba(220, 38, 38, 0.05); backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center gap-2 text-danger fw-bold mb-3">
                  <i class="bi bi-x-circle-fill fs-5"></i>
                  <span>تفاصيل الإلغاء</span>
                </div>
                <div class="row g-3">
                  <div class="col-md-6 mb-2">
                    <label class="text-muted small d-block mb-1 opacity-75">تم الإلغاء بواسطة</label>
                    <div class="fw-bold"><i class="bi bi-person me-1"></i> {{ $donation->cancelledBy?->name ?? '—' }}</div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="text-muted small d-block mb-1 opacity-75">تاريخ الإلغاء</label>
                    <div class="fw-bold"><i class="bi bi-calendar-event me-1"></i> {{ optional($donation->cancelled_at)->format('Y-m-d H:i') ?? '—' }}</div>
                  </div>
                  <div class="col-12">
                    <label class="text-muted small d-block mb-1 opacity-75">سبب الإلغاء</label>
                    <div class="p-3 rounded-3 bg-white bg-opacity-10 border border-white border-opacity-10">
                      {{ $donation->cancellation_reason ?? 'لا يوجد سبب محدد' }}
                    </div>
                  </div>
                </div>
              </div>
            @endif
          </div>
        </div>
      </div>

      {{-- Side Info --}}
      <div class="col-lg-4">
        {{-- Donor Card --}}
        @if($donation->donor)
          <div class="card mb-3">
            <div class="card-body text-center p-4">
              <div class="avatar avatar-lg avatar-primary mx-auto mb-3">
                {{ mb_substr($donation->donor->name, 0, 1) }}
              </div>
              <h5 class="fw-bold mb-1">{{ $donation->donor->name }}</h5>
              @if($donation->donor->phone)
                <div class="text-muted small">
                  <i class="bi bi-telephone"></i> {{ $donation->donor->phone }}
                </div>
              @endif
              <a href="{{ route('donors.show', $donation->donor) }}" class="btn btn-sm btn-outline-primary mt-3">
                <i class="bi bi-person"></i> عرض الملف
              </a>
            </div>
          </div>
        @endif

        {{-- Quick Actions --}}
        <div class="card">
          <div class="card-body">
            <div class="section-title mb-3">
              <i class="bi bi-lightning"></i>
              <h6 class="mb-0">إجراءات سريعة</h6>
            </div>
            <div class="d-grid gap-2">
              @if($donation->status !== 'cancelled')
                <a href="{{ route('donations.edit', $donation) }}" class="btn btn-outline-warning">
                  <i class="bi bi-pencil me-1"></i> طلب تعديل التبرع
                </a>
                <button class="btn btn-outline-warning w-100" onclick="openCancelModal('{{ $donation->id }}', true)">
                    <i class="bi bi-x-circle me-1"></i> طلب إلغاء التبرع
                </button>
              @else
                <div class="alert alert-danger mb-0 text-center fw-bold">
                  <i class="bi bi-x-octagon"></i> هذا التبرع ملغي
                </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- Cancellation Modal --}}
  <div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog">
          <form id="cancelForm" method="POST" class="modal-content">
              @csrf
              @method('DELETE')
              <div class="modal-header">
                  <h5 class="modal-title text-danger" id="cancelModalTitle">تأكيد إلغاء التبرع</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                  <p id="cancelModalText">هل أنت متأكد من رغبتك في إلغاء هذا التبرع؟ سيتم عكس العمليات المالية وتحديث حالة التبرع.</p>
                  <div class="mb-3">
                      <label for="cancellation_reason" class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                      <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required placeholder="يرجى توضيح سبب الإلغاء..."></textarea>
                  </div>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                  <button type="submit" class="btn btn-danger" id="cancelModalSubmit">تأكيد الإلغاء</button>
              </div>
          </form>
      </div>
  </div>

  <script>
      function openCancelModal(id, isRequest = false) {
          const form = document.getElementById('cancelForm');
          form.action = `/donations/${id}`;
          
          const title = document.getElementById('cancelModalTitle');
          const text = document.getElementById('cancelModalText');
          const submitBtn = document.getElementById('cancelModalSubmit');

          if (isRequest) {
              title.textContent = 'طلب إلغاء التبرع';
              title.className = 'modal-title text-warning';
              text.textContent = 'هل أنت متأكد من إرسال طلب لإلغاء هذا التبرع؟ سيقوم المدير بمراجعة الطلب والموافقة عليه.';
              submitBtn.textContent = 'إرسال الطلب للمراجعة';
              submitBtn.className = 'btn btn-warning';
          } else {
              title.textContent = 'تأكيد إلغاء التبرع';
              title.className = 'modal-title text-danger';
              text.textContent = 'هل أنت متأكد من رغبتك في إلغاء هذا التبرع؟ سيتم عكس العمليات المالية وتحديث حالة التبرع.';
              submitBtn.textContent = 'تأكيد الإلغاء الفوري';
              submitBtn.className = 'btn btn-danger';
          }

          new bootstrap.Modal(document.getElementById('cancelModal')).show();
      }
  </script>
@endsection