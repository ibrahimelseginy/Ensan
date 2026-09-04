@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #22c55e 0%, #16a34a 50%, #15803d 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التبرعات 🎁</div>
        <h1 class="hero-title">سجل التبرعات</h1>
        <p class="hero-subtitle">إدارة ومتابعة التبرعات النقدية والعينية الواردة</p>
        <div class="hero-actions d-flex gap-2">
            <button type="button" class="btn btn-sm btn-light text-success fw-bold rounded-pill px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#quickReceiptModal">
                <i class="bi bi-receipt me-1"></i> إيصال استلام نقدية سريع
            </button>
            <a href="{{ route('donations.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة تبرع كامل
            </a>
        </div>
    </div>
    <i class="bi bi-gift-fill hero-icon d-none d-md-block"></i>
</div>

{{-- Filter Section --}}
<div class="chart-container mb-4 animate-slide-up animate-delay-1">
    <div class="chart-header">
        <h5 class="chart-title"><i class="bi bi-funnel-fill"></i> تصفية والبحث</h5>
    </div>
    <form method="get">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label fw-bold small text-uppercase text-muted">بحث</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="bi bi-search text-muted"></i></span>
                    <input type="text" name="q" value="{{ $q ?? '' }}" class="form-control border-start-0"
                        placeholder="اسم المتبرع / رقم الإيصال / المشروع...">
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">الشهر</label>
                <select name="month" class="form-select">
                    <option value="">الكل</option>
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" @selected(request('month') == $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label fw-bold small text-uppercase text-muted">السنة</label>
                <select name="year" class="form-select">
                    <option value="">الكل</option>
                    @foreach(range(date('Y'), 2023) as $y)
                        <option value="{{ $y }}" @selected(request('year') == $y)>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="d-flex gap-2">
                    <button class="btn btn-primary flex-fill fw-bold">
                        <i class="bi bi-funnel me-1"></i> تصفية
                    </button>
                    <a class="btn btn-outline-secondary" href="{{ route('donations.index') }}" title="مسح"><i class="bi bi-x-lg"></i></a>
                    
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle no-arrow" type="button" data-bs-toggle="dropdown" title="تحميل">
                            <i class="bi bi-download"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                            <li><a class="dropdown-item" data-no-loader download href="{{ route('donations.export', array_merge(request()->query(), ['type' => 'cash'])) }}">
                                <i class="bi bi-cash me-2 text-success"></i> تبرعات نقدية
                            </a></li>
                            <li><a class="dropdown-item" data-no-loader download href="{{ route('donations.export', array_merge(request()->query(), ['type' => 'in_kind'])) }}">
                                <i class="bi bi-box me-2 text-info"></i> تبرعات عينية
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Today's Channel Stats --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg animate-slide-up animate-delay-2">
        <a href="{{ route('donations.index', ['channel' => 'cash']) }}" class="stat-card stat-primary text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">نقدي</div>
            <div class="stat-value">{{ number_format($todayByChannel['cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['cash']['count'] }} عملية</div>
            <i class="bi bi-cash-stack stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-3">
        <a href="{{ route('donations.index', ['channel' => 'vodafone_cash']) }}" class="stat-card stat-danger text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-phone-fill"></i></div>
            <div class="stat-label">فودافون كاش</div>
            <div class="stat-value">{{ number_format($todayByChannel['vodafone_cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['vodafone_cash']['count'] }} عملية</div>
            <i class="bi bi-phone-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-4">
        <a href="{{ route('donations.index', ['channel' => 'instapay']) }}" class="stat-card stat-info text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-credit-card-fill"></i></div>
            <div class="stat-label">انستا باي</div>
            <div class="stat-value">{{ number_format($todayByChannel['instapay']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['instapay']['count'] }} عملية</div>
            <i class="bi bi-credit-card-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-5">
        <a href="{{ route('donations.index', ['channel' => 'delegate']) }}" class="stat-card stat-warning text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-label">مندوب</div>
            <div class="stat-value">{{ number_format($todayByChannel['delegate']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ $todayByChannel['delegate']['count'] }} عملية</div>
            <i class="bi bi-person-badge-fill stat-bg-icon"></i>
        </a>
    </div>
    <div class="col-6 col-lg animate-slide-up animate-delay-5">
        <a href="{{ route('donations.index', ['type' => 'in_kind']) }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-label">عيني</div>
            <div class="stat-value">{{ number_format((float) ($inKindToday->total ?? 0), 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-arrow-up-short"></i> {{ (int) ($inKindToday->count ?? 0) }} عملية</div>
            <i class="bi bi-box-seam-fill stat-bg-icon"></i>
        </a>
    </div>
</div>

{{-- Donations Lists --}}
<div class="row g-4 mb-4">
    @if(!request('type') || request('type') !== 'in_kind')
    <div class="col-lg-{{ request('channel') ? '12' : '6' }}">
        <div class="summary-panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="summary-title mb-0">
                    <input type="checkbox" id="selectAllCash" onclick="toggleAll('cash-check', this.checked)" class="form-check-input me-2">
                    <i class="bi bi-cash-coin text-primary"></i> تبرعات نقدية
                </h5>
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle">نقدي</span>
            </div>
            <div class="activity-feed">
                @forelse($cashDonations as $d)
                    <div class="activity-item {{ $d->status === 'cancelled' ? 'cancelled-item' : '' }}" style="{{ $d->status === 'cancelled' ? 'background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);' : '' }}">
                        <input type="checkbox" class="form-check-input me-2 cash-check" value="{{ $d->id }}">
                        <div class="activity-icon {{ $d->status === 'cancelled' ? 'bg-danger text-white' : 'bg-primary-subtle text-primary' }}">
                            <i class="bi bi-{{ $d->cash_channel === 'instapay' ? 'qr-code' : ($d->cash_channel === 'vodafone_cash' ? 'phone' : ($d->cash_channel === 'delegate' ? 'person-badge' : 'cash')) }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title d-flex align-items-center gap-2">
                                {{ $d->donor?->name ?? 'فاعل خير' }}
                                @if($d->status === 'cancelled')
                                    <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.65rem;">ملغي</span>
                                @endif
                            </div>
                            <div class="activity-meta">
                                {{ $d->received_at?->format('Y-m-d') }} • 
                                {{ $d->cash_channel === 'instapay' ? 'انستا باي' : ($d->cash_channel === 'vodafone_cash' ? 'فودافون كاش' : 'نقدي') }}
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <div class="activity-value {{ $d->status === 'cancelled' ? 'text-muted text-decoration-line-through opacity-50' : 'text-success' }}">+{{ number_format($d->amount, 0) }}</div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('donations.show', $d) }}"><i class="bi bi-eye me-2 text-primary"></i> عرض</a></li>
                                    @if(auth()->check())
                                        @if($d->status !== 'cancelled')
                                            <li><a class="dropdown-item text-warning" href="{{ route('donations.edit', $d) }}"><i class="bi bi-pencil me-2 text-warning"></i> طلب تعديل</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-warning" onclick="openCancelModal('{{ $d->id }}', true)">
                                                    <i class="bi bi-x-circle me-2"></i> طلب إلغاء
                                                </button>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">لا توجد تبرعات نقدية</div>
                @endforelse
            </div>
            <div class="d-flex gap-2 align-items-center mt-3 p-2 bg-body-tertiary rounded" id="cashBulkBar" style="display:none !important;">
                <span class="small text-muted" id="cashSelectedCount">0 محدد</span>
                <button class="btn btn-danger btn-sm" onclick="submitBulkDelete('cash-check')"><i class="bi bi-trash me-1"></i> حذف المحدد</button>
            </div>
            <div class="mt-4 d-flex justify-content-center" dir="ltr">{{ $cashDonations->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @endif

    @if(!request('channel'))
    <div class="col-lg-{{ request('type') === 'in_kind' ? '12' : '6' }}">
        <div class="summary-panel h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="summary-title mb-0">
                    <input type="checkbox" id="selectAllInKind" onclick="toggleAll('inkind-check', this.checked)" class="form-check-input me-2">
                    <i class="bi bi-gift text-success"></i> تبرعات عينية
                </h5>
                <span class="badge bg-success-subtle text-success border border-success-subtle">عيني</span>
            </div>
            <div class="activity-feed">
                @forelse($inKindDonations as $d)
                    <div class="activity-item {{ $d->status === 'cancelled' ? 'cancelled-item' : '' }}" style="{{ $d->status === 'cancelled' ? 'background: rgba(239, 68, 68, 0.05); border: 1px solid rgba(239, 68, 68, 0.2);' : '' }}">
                        <input type="checkbox" class="form-check-input me-2 inkind-check" value="{{ $d->id }}">
                    <div class="activity-icon {{ $d->status === 'cancelled' ? 'bg-danger text-white' : 'bg-success text-white' }}">
                            <i class="bi bi-{{ $d->status === 'cancelled' ? 'x-lg' : 'gift' }}"></i>
                        </div>
                        <div class="activity-content">
                            <div class="activity-title d-flex align-items-center gap-2">
                                {{ $d->donor?->name ?? 'متبرع مجهول' }}
                                @if($d->status === 'cancelled')
                                    <span class="badge bg-danger rounded-pill px-2" style="font-size: 0.65rem;">ملغي</span>
                                @endif
                            </div>
                            <div class="activity-meta">
                                {{ $d->received_at?->format('Y-m-d') ?? '—' }} • 
                                {{ $d->warehouse?->name ?? '—' }}
                            </div>
                        </div>
                        <div class="d-flex flex-column align-items-end gap-1">
                            <div class="activity-value {{ $d->status === 'cancelled' ? 'text-muted text-decoration-line-through opacity-50' : 'text-success' }}">+{{ number_format($d->estimated_value, 0) }}</div>
                            <div class="dropdown">
                                <button class="btn btn-sm btn-light border-0 rounded-circle p-2" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="viewport">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                    <li><a class="dropdown-item" href="{{ route('donations.show', $d) }}"><i class="bi bi-eye me-2 text-primary"></i> عرض</a></li>
                                    @if(auth()->check())
                                        @if($d->status !== 'cancelled')
                                            <li><a class="dropdown-item text-warning" href="{{ route('donations.edit', $d) }}"><i class="bi bi-pencil me-2 text-warning"></i> طلب تعديل</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <button class="dropdown-item text-warning" onclick="openCancelModal('{{ $d->id }}', true)">
                                                    <i class="bi bi-x-circle me-2"></i> طلب إلغاء
                                                </button>
                                            </li>
                                        @endif
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-muted text-center py-4">لا توجد تبرعات عينية</div>
                @endforelse
            </div>
            <div class="d-flex gap-2 align-items-center mt-3 p-2 bg-body-tertiary rounded" id="inkindBulkBar" style="display:none !important;">
                <span class="small text-muted" id="inkindSelectedCount">0 محدد</span>
                <button class="btn btn-danger btn-sm" onclick="submitBulkDelete('inkind-check')"><i class="bi bi-trash me-1"></i> حذف المحدد</button>
            </div>
            <div class="mt-4 d-flex justify-content-center" dir="ltr">{{ $inKindDonations->links('pagination::bootstrap-5') }}</div>
        </div>
    </div>
    @endif
</div>

{{-- Hidden Bulk Delete Form --}}
<form id="bulkDeleteForm" method="POST" action="{{ route('donations.bulk-destroy') }}" style="display:none;">
    @csrf
    <div id="bulkDeleteIds"></div>
</form>
@endsection

{{-- Cancellation Modal --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <form id="cancelForm" method="POST" class="modal-content">
            @csrf
            @method('DELETE')
            <div class="modal-header">
                <h5 class="modal-title text-danger">تأكيد إلغاء التبرع</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>هل أنت متأكد من رغبتك في إلغاء هذا التبرع؟ سيتم عكس العمليات المالية وتحديث حالة التبرع.</p>
                <div class="mb-3">
                    <label for="cancellation_reason" class="form-label">سبب الإلغاء <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="cancellation_reason" name="cancellation_reason" rows="3" required placeholder="يرجى توضيح سبب الإلغاء..."></textarea>
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
        form.action = `/donations/${id}`;
        
        const title = document.querySelector('#cancelModal .modal-title');
        const bodyText = document.querySelector('#cancelModal .modal-body p');
        const btn = document.querySelector('#cancelModal button[type="submit"]');

        if (isRequest) {
            title.textContent = 'طلب إلغاء التبرع';
            title.className = 'modal-title text-warning';
            bodyText.textContent = 'هل أنت متأكد من إرسال طلب لإلغاء هذا التبرع؟ سيقوم المدير بمراجعة الطلب والموافقة عليه.';
            btn.textContent = 'إرسال الطلب';
            btn.className = 'btn btn-warning';
        } else {
            title.textContent = 'تأكيد إلغاء التبرع';
            title.className = 'modal-title text-danger';
            bodyText.textContent = 'هل أنت متأكد من رغبتك في إلغاء هذا التبرع؟ سيتم عكس العمليات المالية وتحديث حالة التبرع.';
            btn.textContent = 'تأكيد الإلغاء';
            btn.className = 'btn btn-danger';
        }

        new bootstrap.Modal(document.getElementById('cancelModal')).show();
    }

    function toggleAll(className, checked) {
        document.querySelectorAll('.' + className).forEach(cb => cb.checked = checked);
        updateBulkBar(className);
    }

    document.addEventListener('change', function(e) {
        if (e.target.classList.contains('cash-check')) updateBulkBar('cash-check');
        if (e.target.classList.contains('inkind-check')) updateBulkBar('inkind-check');
    });

    function updateBulkBar(className) {
        const checked = document.querySelectorAll('.' + className + ':checked').length;
        if (className === 'cash-check') {
            const bar = document.getElementById('cashBulkBar');
            document.getElementById('cashSelectedCount').textContent = checked + ' محدد';
            bar.style.display = checked > 0 ? 'flex' : 'none';
            bar.style.setProperty('display', checked > 0 ? 'flex' : 'none', 'important');
        } else {
            const bar = document.getElementById('inkindBulkBar');
            document.getElementById('inkindSelectedCount').textContent = checked + ' محدد';
            bar.style.display = checked > 0 ? 'flex' : 'none';
            bar.style.setProperty('display', checked > 0 ? 'flex' : 'none', 'important');
        }
    }

    function submitBulkDelete(className) {
        const ids = [];
        document.querySelectorAll('.' + className + ':checked').forEach(cb => ids.push(cb.value));
        if (ids.length === 0) { alert('يرجى اختيار تبرع واحد على الأقل'); return; }
        if (!confirm('هل أنت متأكد من حذف ' + ids.length + ' تبرع؟')) return;
        const form = document.getElementById('bulkDeleteForm');
        const container = document.getElementById('bulkDeleteIds');
        container.innerHTML = '';
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden'; input.name = 'ids[]'; input.value = id;
            container.appendChild(input);
        });
        form.submit();
    }
</script>

{{-- Quick Receipt Modal (إيصال استلام نقدية سريع) --}}
<div class="modal fade" id="quickReceiptModal" tabindex="-1" aria-labelledby="quickReceiptModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <form method="POST" action="{{ route('donations.store') }}" class="modal-content border-0 shadow-lg">
            @csrf
            <input type="hidden" name="type" value="cash">
            <input type="hidden" name="currency" value="EGP">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title fw-bold" id="quickReceiptModalLabel">
                    <i class="bi bi-receipt me-2"></i> إيصال استلام نقدية سريع
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4" style="background:#fafafa">
                <div class="card p-3 border-success mb-3 bg-white">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-dark">رقم الإيصال <span class="text-danger">*</span></label>
                            <input name="receipt_number" class="form-control fw-bold text-success border-success" placeholder="مثال: 06501" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">المبلغ (جنيه)</label>
                            <input name="amount" type="number" step="0.01" class="form-control fw-bold text-dark" placeholder="مثال: 250.00" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">تحريراً في (التاريخ)</label>
                            <input name="received_at" type="date" value="{{ date('Y-m-d') }}" class="form-control" required>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label fw-bold">وصل من (اسم المتبرع) <span class="text-danger">*</span></label>
                        <select name="donor_id" id="quickDonorSelect" class="form-select" required>
                            <option value="">— اختر المتبرع —</option>
                            @foreach($receiptDonors as $donorItem)
                                <option value="{{ $donorItem->id }}">{{ $donorItem->name }} {{ $donorItem->phone ? '('.$donorItem->phone.')' : '' }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">المشروع</label>
                        <select name="project_id" class="form-select">
                            <option value="">— اختر مشروع (اختياري) —</option>
                            @foreach($receiptProjects as $projectItem)
                                <option value="{{ $projectItem->id }}">{{ $projectItem->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">وذلك قيمة (نوع التخصيص)</label>
                        <select name="purpose" id="quickPurposeSelect" class="form-select" required>
                            <option value="">— اختر —</option>
                            <option value="kafalat_aytam">كفالات أيتام</option>
                            <option value="kafalat_awram">كفالات أورام</option>
                            <option value="sadaqat">صدقات</option>
                            <option value="zakat_maal">زكاة مال</option>
                            <option value="sadaqa_jariya">صدقات جارية</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-bold">طريقة وسيلة التحصيل</label>
                        <select name="cash_channel" class="form-select" required>
                            <option value="cash">نقدي</option>
                            <option value="instapay">انستا باي (Instapay)</option>
                            <option value="vodafone_cash">فودافون كاش</option>
                            <option value="delegate">مندوب تحصيل</option>
                        </select>
                    </div>

                    <div class="col-md-12" id="quickMembersWrapper" style="display:none">
                        <label class="form-label fw-bold" id="quickMembersLabel">اختر الملفات المكفولة</label>
                        <select name="family_member_ids[]" id="quickMembersSelect" class="form-select" multiple></select>
                        <div id="quickMembersWarning" class="form-text text-muted"></div>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">الخزينة <span class="text-danger">*</span></label>
                        <select name="treasury_id" class="form-select" required>
                            <option value="">— اختر الخزينة —</option>
                            @foreach($receiptTreasuries as $treasuryItem)
                                <option value="{{ $treasuryItem->id }}">{{ $treasuryItem->name }} — {{ number_format((float)$treasuryItem->current_balance, 2) }} {{ $treasuryItem->currency }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-bold">ملاحظات التخصيص أو كود الحالة</label>
                        <input name="allocation_note" class="form-control" placeholder="مثال: كود حالة 105 أو تخصيص خاص...">
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-white">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success px-4 fw-bold">
                    <i class="bi bi-check-lg me-1"></i> تسجيل وإصدار الإيصال
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $quickDonorMembersData = $receiptDonors->mapWithKeys(function ($donor) {
        return [(string)$donor->id => $donor->sponsoredFamilyMembers->map(function ($member) {
            return [
                'id' => $member->id,
                'name' => $member->full_name,
                'code' => $member->code,
                'relationship' => $member->relationship,
                'is_patient' => (bool)$member->is_patient,
                'family' => $member->beneficiary?->full_name,
            ];
        })->values()];
    });
    $quickAllMembersData = $allFamilyMembers->map(function ($member) {
        return [
            'id' => $member->id,
            'name' => $member->full_name,
            'code' => $member->code,
            'relationship' => $member->relationship,
            'is_patient' => (bool)$member->is_patient,
            'family' => $member->beneficiary?->full_name,
        ];
    });
@endphp
<script>
document.addEventListener('DOMContentLoaded', function () {
    const donorMembers = @json($quickDonorMembersData);
    const allMembersList = @json($quickAllMembersData);
    const donorSelect = document.getElementById('quickDonorSelect');
    const purposeSelect = document.getElementById('quickPurposeSelect');
    const membersWrapper = document.getElementById('quickMembersWrapper');
    const membersSelect = document.getElementById('quickMembersSelect');
    const membersLabel = document.getElementById('quickMembersLabel');
    const warning = document.getElementById('quickMembersWarning');

    function refreshQuickMembers() {
        const purpose = purposeSelect?.value || '';
        const donorId = String(donorSelect?.value || '');
        const isKafala = ['kafalat_aytam', 'kafalat_awram'].includes(purpose);

        const donorSpecific = (donorMembers[donorId] || []).filter(member => purpose === 'kafalat_awram'
            ? member.is_patient
            : (!member.is_patient && member.relationship === 'child'));

        const members = donorSpecific.length > 0 ? donorSpecific : allMembersList.filter(member => purpose === 'kafalat_awram'
            ? member.is_patient
            : (!member.is_patient && member.relationship === 'child'));

        membersWrapper.style.display = isKafala ? '' : 'none';
        membersSelect.disabled = !isKafala;
        membersSelect.required = isKafala;
        membersSelect.innerHTML = '';
        if (!isKafala) return;

        members.forEach(member => {
            const option = document.createElement('option');
            option.value = member.id;
            option.textContent = `${member.name} — ${member.family || 'الأسرة'}${member.code ? ' — ' + member.code : ''}`;
            membersSelect.appendChild(option);
        });
        membersLabel.textContent = purpose === 'kafalat_awram' ? 'اختر مرضى الأورام المكفولين' : 'اختر الأطفال المكفولين';
        warning.textContent = members.length ? 'يمكن اختيار اسم واحد أو أكثر من الحالات المتاحة.' : 'لا توجد حالات مسجلة حالياً لهذا البند.';
    }

    donorSelect?.addEventListener('change', refreshQuickMembers);
    purposeSelect?.addEventListener('change', refreshQuickMembers);
});
</script>

