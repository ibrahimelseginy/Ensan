@extends('layouts.app')
@section('styles')
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
  <style>
    /* Premium Portal Hub Styling - Compact & Balanced */
    .donation-portal-card {
        background: rgba(15, 23, 42, 0.7);
        border: 2px solid rgba(255, 255, 255, 0.1);
        border-radius: 18px;
        padding: 20px 18px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        width: 100%;
        height: 100%;
        min-height: 440px;
        box-sizing: border-box;
        text-decoration: none !important;
        cursor: pointer;
    }
    [data-bs-theme="light"] .donation-portal-card,
    body:not(.theme-dark) .donation-portal-card {
        background: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
    }

    .donation-portal-card.card-cash {
        border-color: rgba(16, 185, 129, 0.3);
    }
    .donation-portal-card.card-cash:hover {
        border-color: #10b981;
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(16, 185, 129, 0.22);
    }
    [data-bs-theme="light"] .donation-portal-card.card-cash:hover,
    body:not(.theme-dark) .donation-portal-card.card-cash:hover {
        box-shadow: 0 16px 32px rgba(16, 185, 129, 0.12);
    }

    .donation-portal-card.card-inkind {
        border-color: rgba(245, 158, 11, 0.3);
    }
    .donation-portal-card.card-inkind:hover {
        border-color: #f59e0b;
        transform: translateY(-4px);
        box-shadow: 0 16px 32px rgba(245, 158, 11, 0.22);
    }
    [data-bs-theme="light"] .donation-portal-card.card-inkind:hover,
    body:not(.theme-dark) .donation-portal-card.card-inkind:hover {
        box-shadow: 0 16px 32px rgba(245, 158, 11, 0.12);
    }

    .portal-icon-wrapper {
        width: 58px;
        height: 58px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 14px;
        transition: all 0.3s ease;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.18);
    }
    .donation-portal-card:hover .portal-icon-wrapper {
        transform: scale(1.06) rotate(-3deg);
    }

    .portal-icon-cash {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
    }
    .portal-icon-inkind {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: #ffffff;
        box-shadow: 0 10px 20px rgba(245, 158, 11, 0.3);
    }

    .portal-title {
        font-size: 1.4rem;
        font-weight: 800;
        margin-bottom: 4px;
        color: #ffffff;
        min-height: 30px;
        display: flex;
        align-items: center;
    }
    [data-bs-theme="light"] .portal-title,
    body:not(.theme-dark) .portal-title {
        color: #0f172a;
    }

    .portal-subtitle {
        font-size: 0.88rem;
        color: #94a3b8;
        margin-bottom: 14px;
        font-weight: 500;
        min-height: 20px;
        display: flex;
        align-items: center;
    }
    [data-bs-theme="light"] .portal-subtitle,
    body:not(.theme-dark) .portal-subtitle {
        color: #64748b;
    }

    .portal-stat-badge {
        padding: 10px 14px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 16px;
        min-height: 64px;
        box-sizing: border-box;
    }
    .stat-badge-cash {
        background: rgba(16, 185, 129, 0.12);
        border: 1.5px solid rgba(16, 185, 129, 0.3);
    }
    .stat-badge-inkind {
        background: rgba(245, 158, 11, 0.12);
        border: 1.5px solid rgba(245, 158, 11, 0.3);
    }

    .portal-features-list {
        list-style: none;
        padding: 0;
        margin: 0 0 16px 0;
        min-height: auto;
    }
    .portal-features-list li {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 5px 0;
        font-size: 0.86rem;
        color: #cbd5e1;
        border-bottom: 1px dashed rgba(255, 255, 255, 0.08);
        min-height: 32px;
        box-sizing: border-box;
    }
    [data-bs-theme="light"] .portal-features-list li,
    body:not(.theme-dark) .portal-features-list li {
        color: #475569;
        border-bottom-color: #f1f5f9;
    }
    .portal-features-list li:last-child {
        border-bottom: none;
    }

    .bg-watermark-icon {
        position: absolute;
        bottom: -10px;
        left: -10px;
        top: auto;
        font-size: 5.5rem;
        opacity: 0.06 !important;
        pointer-events: none;
        user-select: none;
        z-index: 0;
    }
    .donation-portal-card > div {
        position: relative;
        z-index: 1;
    }
  </style>
@endsection

@section('content')
{{-- Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up mb-4" style="background: linear-gradient(135deg, #059669 0%, #10b981 50%, #047857 100%);">
    <div class="hero-content">
        <div class="hero-greeting">بوابة التبرعات 🎁</div>
        <h1 class="hero-title">إدارة ومتابعة التبرعات</h1>
        <p class="hero-subtitle">المنظومة المركزية للتحصيلات النقدية، المحافظ الإلكترونية، والتوريدات العينية والمخزون</p>
        <div class="hero-actions d-flex gap-2 flex-wrap">
            <button type="button" class="btn btn-sm btn-white text-dark fw-bold rounded-pill px-3 shadow-sm" style="background: #ffffff !important; color: #0f172a !important;" data-bs-toggle="modal" data-bs-target="#quickReceiptModal">
                <i class="bi bi-receipt text-success me-1"></i> <span>إيصال استلام نقدية سريع</span>
            </button>
            <a href="{{ route('donations.create') }}" class="btn btn-sm btn-outline-light rounded-pill px-3 text-white">
                <i class="bi bi-plus-lg me-1"></i> تسجيل تبرع كامل
            </a>
        </div>
    </div>
    <i class="bi bi-gift-fill hero-icon d-none d-md-block" style="opacity: 0.15; font-size: 7rem;"></i>
</div>

{{-- Channels Summary Strip with Real Totals --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash') }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-wallet2"></i></div>
            <div class="stat-label">الكل (نقدي)</div>
            <div class="stat-value">{{ number_format($channelMetrics['all']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['all']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', ['channel' => 'cash']) }}" class="stat-card stat-primary text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-cash-stack"></i></div>
            <div class="stat-label">خزينة نقدي</div>
            <div class="stat-value">{{ number_format($channelMetrics['cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['cash']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', ['channel' => 'vodafone_cash']) }}" class="stat-card stat-danger text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-phone-fill"></i></div>
            <div class="stat-label">فودافون كاش</div>
            <div class="stat-value">{{ number_format($channelMetrics['vodafone_cash']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['vodafone_cash']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', ['channel' => 'instapay']) }}" class="stat-card stat-info text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-credit-card-fill"></i></div>
            <div class="stat-label">انستا باي</div>
            <div class="stat-value">{{ number_format($channelMetrics['instapay']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['instapay']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-6 col-lg">
        <a href="{{ route('donations.cash', ['channel' => 'delegate']) }}" class="stat-card stat-warning text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-person-badge-fill"></i></div>
            <div class="stat-label">مندوب تحصيل</div>
            <div class="stat-value">{{ number_format($channelMetrics['delegate']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-receipt"></i> {{ $channelMetrics['delegate']['count'] }} عملية</div>
        </a>
    </div>
    <div class="col-12 col-lg">
        <a href="{{ route('donations.in-kind') }}" class="stat-card stat-success text-decoration-none d-block">
            <div class="stat-icon"><i class="bi bi-box-seam-fill"></i></div>
            <div class="stat-label">تبرعات عينية</div>
            <div class="stat-value">{{ number_format($channelMetrics['in_kind']['total'], 0) }}</div>
            <div class="stat-trend up"><i class="bi bi-box-seam"></i> {{ $channelMetrics['in_kind']['count'] }} عملية</div>
        </a>
    </div>
</div>

{{-- TWO PRIMARY CATEGORY ICONS / CARDS (الأيقونتان الرئيستان كما طلب المستخدم تماماً) --}}
<div class="row g-3 g-xl-4 mb-4 align-items-stretch">
    
    {{-- Card 1: تبرعات نقدية --}}
    <div class="col-lg-6 d-flex flex-column">
        <div class="donation-portal-card card-cash w-100 h-100" onclick="window.location.href='{{ route('donations.cash') }}'">
            <i class="bi bi-cash-coin bg-watermark-icon text-success"></i>
            
            <div>
                {{-- Icon --}}
                <div class="portal-icon-wrapper portal-icon-cash">
                    <i class="bi bi-cash-coin"></i>
                </div>

                {{-- Title & Subtitle --}}
                <h3 class="portal-title">تبرعات نقدية</h3>
                <p class="portal-subtitle">التحصيلات النقدية والإلكترونية</p>

                {{-- Current Stats Badge --}}
                <div class="portal-stat-badge stat-badge-cash">
                    <div>
                        <div class="small text-muted fw-bold mb-1" style="font-size: 0.78rem;">إجمالي التحصيلات النقدية</div>
                        <div class="fs-5 fw-bolder text-success font-monospace">
                            {{ number_format($cashTotal, 0) }} <small style="font-size: 0.8rem;">ج.م</small>
                        </div>
                    </div>
                    <div class="text-start">
                        <span class="badge bg-success bg-opacity-20 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-pill small font-monospace">
                            <i class="bi bi-receipt me-1"></i> {{ $cashCount }} عملية مسجلة
                        </span>
                    </div>
                </div>

                {{-- Features / Channels --}}
                <ul class="portal-features-list">
                    <li>
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>الخزينة النقدية الرئيسية والفرعية</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>محافظ فودافون كاش وتحويلات انستا باي (InstaPay)</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>تحصيلات المناديب الميدانية وخطوط السير</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill text-success"></i>
                        <span>طباعة إيصالات الاستلام الرسمية (A4 وحراري)</span>
                    </li>
                </ul>
            </div>

            {{-- Action Button --}}
            <div class="mt-2">
                <a href="{{ route('donations.cash') }}" class="btn btn-success fw-bold rounded-pill w-100 py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm" onclick="event.stopPropagation();">
                    <span>دخول صفحة التبرعات النقدية الكاملة</span>
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

    {{-- Card 2: تبرعات عينية --}}
    <div class="col-lg-6 d-flex flex-column">
        <div class="donation-portal-card card-inkind w-100 h-100" onclick="window.location.href='{{ route('donations.in-kind') }}'">
            <i class="bi bi-box-seam-fill bg-watermark-icon text-warning"></i>
            
            <div>
                {{-- Icon --}}
                <div class="portal-icon-wrapper portal-icon-inkind">
                    <i class="bi bi-box-seam-fill"></i>
                </div>

                {{-- Title & Subtitle --}}
                <h3 class="portal-title">تبرعات عينية</h3>
                <p class="portal-subtitle">المواد والأجهزة والمخزون المورد</p>

                {{-- Current Stats Badge --}}
                <div class="portal-stat-badge stat-badge-inkind">
                    <div>
                        <div class="small text-muted fw-bold mb-1" style="font-size: 0.78rem;">إجمالي القيمة التقديرية</div>
                        <div class="fs-5 fw-bolder text-warning font-monospace">
                            {{ number_format($inKindTotal, 0) }} <small style="font-size: 0.8rem;">ج.م</small>
                        </div>
                    </div>
                    <div class="text-start">
                        <span class="badge bg-warning bg-opacity-20 text-warning border border-warning border-opacity-25 px-2.5 py-1.5 rounded-pill small font-monospace">
                            <i class="bi bi-box-seam me-1"></i> {{ $inKindCount }} عملية مسجلة
                        </span>
                    </div>
                </div>

                {{-- Features / Categories --}}
                <ul class="portal-features-list">
                    <li>
                        <i class="bi bi-check-circle-fill text-warning"></i>
                        <span>المواد الغذائية واللحوم والطرود التموينية</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill text-warning"></i>
                        <span>الأجهزة والمستلزمات الطبية وأجهزة التعويض</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill text-warning"></i>
                        <span>الكسوة والملابس والمفروشات للمستحقين</span>
                    </li>
                    <li>
                        <i class="bi bi-check-circle-fill text-warning"></i>
                        <span>التوريد والتوجيه المباشر لمستودعات ومخازن المؤسسة</span>
                    </li>
                </ul>
            </div>

            {{-- Action Button --}}
            <div class="mt-2">
                <a href="{{ route('donations.in-kind') }}" class="btn btn-warning fw-bold rounded-pill w-100 py-2.5 d-flex align-items-center justify-content-center gap-2 shadow-sm text-dark" onclick="event.stopPropagation();">
                    <span>دخول صفحة التبرعات العينية الكاملة</span>
                    <i class="bi bi-arrow-left"></i>
                </a>
            </div>
        </div>
    </div>

</div>

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
@endsection

@section('scripts')
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
@endsection
