@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #f43f5e 0%, #e11d48 50%, #be123c 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التفاصيل المالية 📋</div>
        <h1 class="hero-title">تفاصيل المصروف #{{ $expense->id }}</h1>
        <p class="hero-subtitle">
{{ $expense->category ?? 'مصروف عام' }} | {{ optional($expense->created_at)->format('Y-m-d') }}
        </p>
        <div class="hero-actions d-flex gap-2 align-items-center">
            @if(auth()->check() && !isset($pendingRequest))
                @if($expense->status !== 'cancelled')
                     <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-sm rounded-pill px-4 btn-light text-primary shadow-sm hover-lift">
                        <i class="bi bi-pencil me-1"></i> تعديل
                    </a>
                @endif
            @elseif(isset($pendingRequest))
                <span class="badge bg-white bg-opacity-25 text-white p-2 px-3 rounded-pill small">
                    <i class="bi bi-hourglass-split me-1"></i> طلب معلق
                </span>
            @endif
            <a href="{{ route('expenses.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light shadow-sm hover-lift">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>
    </div>
    <div class="hero-stat-item">
        <span class="d-block small opacity-75">المبلغ</span>
        <span class="fw-bold fs-4">{{ number_format($expense->amount, 2) }} <small class="fs-6 fw-normal">{{ $expense->currency }}</small></span>
    </div>
</div>

<div class="row g-4 animate-slide-up animate-delay-1">
    {{-- Main Info --}}
    <div class="col-lg-8">
        <div class="summary-panel mb-4">
            <h5 class="fw-bold mb-3 section-title"><i class="bi bi-info-circle me-2"></i>معلومات المصروف</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <span class="d-block text-muted small mb-1">النوع</span>
                        <span class="fw-bold text-dark">
                            @php
                                $typeText = match ($expense->type) {
                                  'operational' => 'تشغيلي',
                                  'aid' => 'مساعدات',
                                  'logistics' => 'لوجستي',
                                  default => $expense->type
                                };
                            @endphp
                            {{ $typeText }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-3 bg-light rounded-3 h-100">
                        <span class="d-block text-muted small mb-1">طريقة الدفع</span>
                        <span class="fw-bold text-dark">
                            @php
                                $paymentMethods = [
                                  'delegate' => 'مندوب',
                                  'cash' => 'نقدي',
                                  'vodafone_cash' => 'فودافون كاش',
                                  'instapay' => 'انستا باي'
                                ];
                            @endphp
                            {{ $paymentMethods[$expense->payment_method] ?? $expense->payment_method }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <div class="chart-container">
            <h5 class="fw-bold mb-3 section-title"><i class="bi bi-diagram-3 me-2"></i>التخصيص</h5>
            <div class="list-group list-group-flush">
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted"><i class="bi bi-person me-2"></i>المستفيد</span>
                    <span class="fw-bold">
                        @if($expense->beneficiary)
                            <a href="{{ route('beneficiaries.show', $expense->beneficiary) }}" class="text-decoration-none">{{ $expense->beneficiary->full_name }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted"><i class="bi bi-briefcase me-2"></i>المشروع</span>
                    <span class="fw-bold">
                        @if($expense->project)
                            <a href="{{ route('projects.show', $expense->project) }}" class="text-decoration-none">{{ $expense->project->name }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center px-0">
                    <span class="text-muted"><i class="bi bi-megaphone me-2"></i>الحملة</span>
                    <span class="fw-bold">
                        @if($expense->campaign)
                            <a href="{{ route('campaigns.show', $expense->campaign) }}" class="text-decoration-none">{{ $expense->campaign->name }}</a>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </span>
                </div>
            </div>
            
            @if($expense->description)
                <div class="mt-4 p-3 bg-light rounded border border-dashed text-muted small">
                    <strong class="d-block text-dark mb-1">ملاحظات / وصف:</strong>
                    {{ $expense->description }}
                </div>
            @endif

            @if($expense->status === 'cancelled')
              <div class="mt-4 p-4 rounded-4 border border-danger border-opacity-25 shadow-sm" style="background: rgba(220, 38, 38, 0.05); backdrop-filter: blur(10px);">
                <div class="d-flex align-items-center gap-2 text-danger fw-bold mb-3">
                  <i class="bi bi-x-circle-fill fs-5"></i>
                  <span>تفاصيل الإلغاء</span>
                </div>
                <div class="row g-3">
                  <div class="col-md-6 mb-2">
                    <label class="text-muted small d-block mb-1 opacity-75">تم الإلغاء بواسطة</label>
                    <div class="fw-bold text-dark"><i class="bi bi-person me-1"></i> {{ $expense->cancelledBy?->name ?? '—' }}</div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="text-muted small d-block mb-1 opacity-75">تاريخ الإلغاء</label>
                    <div class="fw-bold text-dark"><i class="bi bi-calendar-event me-1"></i> {{ optional($expense->cancelled_at)->format('Y-m-d H:i') ?? '—' }}</div>
                  </div>
                  <div class="col-12">
                    <label class="text-muted small d-block mb-1 opacity-75">سبب الإلغاء</label>
                    <div class="p-3 rounded-3 bg-white bg-opacity-50 border border-white border-opacity-10 text-dark">
                      {{ $expense->cancellation_reason ?? 'لا يوجد سبب محدد' }}
                    </div>
                  </div>
                </div>
              </div>
            @endif
        </div>
    </div>

    {{-- Sidebar --}}
    <div class="col-lg-4">
        {{-- Quick Actions --}}
        <div class="glass-card p-4 border-0 shadow-sm" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1) !important;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 section-title text-white opacity-75"><i class="bi bi-lightning me-2"></i>إجراءات سريعة</h6>
                @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <a href="{{ route('change-requests.index') }}" class="btn btn-sm btn-link text-white text-decoration-none opacity-50 hover-opacity-100 p-0" title="طلبات المراجعة">
                        <i class="bi bi-shield-check fs-5"></i>
                    </a>
                @endif
            </div>

            <div class="d-grid gap-2">
                @if(isset($pendingRequest))
                    <div class="alert alert-warning mb-2 p-3 border-0 bg-warning bg-opacity-10 text-warning rounded-4 shadow-sm animate-pulse">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                            <span class="fw-bold small">طلب قيد المراجعة</span>
                        </div>
                        <p class="x-small mb-0 opacity-75">هذا السجل لديه طلب تعديل أو إلغاء حالياً، يرجى الانتظار للموافقة أو إلغاء الطلب.</p>
                    </div>
                    
                    <form action="{{ route('change-requests.cancel', $pendingRequest) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد إلغاء هذا الطلب وإبقاء السجل كما هو؟')">
                        @csrf
                        <button class="btn btn-outline-danger w-100 rounded-pill shadow-sm py-2 mt-2">
                            <i class="bi bi-x-circle me-1"></i> إلغاء الطلب الحالي
                        </button>
                    </form>
                @elseif($expense->status !== 'cancelled')
                     <a href="{{ route('expenses.edit', $expense) }}" class="btn btn-light text-primary rounded-pill shadow-sm py-2 hover-lift">
                        <i class="bi bi-pencil me-1"></i> تعديل البيانات
                    </a>

                    <button class="btn btn-outline-light rounded-pill shadow-sm py-2 hover-lift" onclick="openCancelModal('{{ $expense->id }}', true)">
                        <i class="bi bi-trash3 me-1"></i> طلب إلغاء المصروف
                    </button>
                @else
                    <div class="alert alert-danger mb-0 text-center fw-bold rounded-pill bg-danger bg-opacity-10 border-danger border-opacity-25 text-danger">
                        <i class="bi bi-x-octagon me-1"></i> هذا المصروف ملغي نهائياً
                    </div>
                @endif
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
                <h5 class="modal-title text-danger">تأكيد إلغاء المصروف</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إلغاء هذا المصروف؟ سيتم عكس العمليات المالية وتحديث حالة المصروف.</p>
                <div class="mb-3">
                    <label for="reason" class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="reason" name="reason" rows="3" required placeholder="يرجى توضيح سبب الإلغاء..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">تراجع</button>
                <button type="submit" class="btn btn-danger">تأكيد الإلغاء</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCancelModal(id, isRequest = false) {
        const form = document.getElementById('cancelForm');
        form.action = `/expenses/${id}`;
        
        const title = document.querySelector('#cancelModal .modal-title');
        const bodyText = document.querySelector('#cancelModal .modal-body p');
        const btn = document.querySelector('#cancelModal button[type="submit"]');

        if (isRequest) {
            title.textContent = 'طلب إلغاء المصروف';
            title.className = 'modal-title text-warning';
            bodyText.textContent = 'هل أنت متأكد من إرسال طلب لإلغاء هذا المصروف؟ سيقوم المدير بمراجعة الطلب والموافقة عليه.';
            btn.textContent = 'إرسال الطلب';
            btn.className = 'btn btn-warning';
        } else {
            title.textContent = 'تأكيد إلغاء المصروف';
            title.className = 'modal-title text-danger';
            bodyText.textContent = 'هل أنت متأكد من رغبتك في إلغاء هذا المصروف؟ سيتم عكس العمليات المالية وتحديث حالة المصروف.';
            btn.textContent = 'تأكيد الإلغاء';
            btn.className = 'btn btn-danger';
        }

        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }
</script>
@endsection