@extends('layouts.app')

@section('title', 'الخزائن والحسابات النقدية')

@push('styles')
<style>
    .finance-stat-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.03);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .finance-stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.06);
    }
    .fund-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 22px;
        position: relative;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
        transition: all 0.2s ease;
    }
    .fund-card:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.06);
    }
    .fund-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }
    .fund-icon-cash {
        background: rgba(16, 185, 129, 0.12);
        color: #10b981;
    }
    .fund-icon-bank {
        background: rgba(59, 130, 246, 0.12);
        color: #2563eb;
    }
    .fund-icon-wallet {
        background: rgba(168, 85, 247, 0.12);
        color: #9333ea;
    }
    .summary-overview-box {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 28px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.03);
    }
    .summary-tile {
        padding: 20px;
        border-radius: 14px;
        text-align: center;
        transition: all 0.2s ease;
    }
    .summary-tile-bank { background: #eff6ff; border: 1px solid #bfdbfe; }
    .summary-tile-central { background: #fffbeb; border: 1px solid #fde68a; }
    .summary-tile-sub { background: #f8fafc; border: 1px solid #e2e8f0; cursor: pointer; }
    .summary-tile-sub:hover { background: #f1f5f9; border-color: #cbd5e1; transform: translateY(-2px); }
    .summary-tile-accounts { background: #f0fdf4; border: 1px solid #bbf7d0; }
    .tab-pill-btn {
        padding: 8px 20px;
        font-weight: 600;
        border-radius: 10px;
        color: #64748b;
        border: none;
        background: transparent;
        transition: all 0.2s ease;
    }
    .tab-pill-btn.active {
        background: #ffffff;
        color: #0f172a;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
</style>
@endpush

@section('content')
@php
    $fmt = fn($num) => \App\Models\Treasury::formatAmount($num);
@endphp
<div class="container-fluid pt-2 pb-4 px-lg-4" style="direction: rtl; text-align: right;">

    {{-- Alert Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2 fs-5 align-middle"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Top Action Row --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-safe2 text-success"></i>
                الخزائن
            </h2>
            <p class="text-muted mb-0 small">إدارة الأرصدة النقدية والحسابات البنكية والتوريدات والعهد المالية</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <button type="button" class="btn btn-outline-secondary rounded-pill px-3 py-2 d-flex align-items-center gap-2" onclick="window.location.reload();">
                <i class="bi bi-arrow-clockwise"></i>
                <span>تحديث</span>
                <span class="badge bg-light text-muted border ms-1" style="font-size: 0.72rem;">{{ now()->format('h:i A') }}</span>
            </button>
            <a href="{{ route('transfers.index') }}" class="btn btn-outline-primary rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-arrow-left-right"></i>
                <span>التوريدات والتحويلات</span>
            </a>
            <a href="{{ route('custodies.index') }}" class="btn btn-outline-success rounded-pill px-3 py-2 d-flex align-items-center gap-2">
                <i class="bi bi-wallet2"></i>
                <span>العهد المالية</span>
            </a>
            @if(auth()->user()?->hasPermission('treasuries.create'))
            <a href="{{ route('treasuries.create') }}" class="btn btn-primary rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i>
                <span>إضافة خزينة / صندوق</span>
            </a>
            @endif
        </div>
    </div>

    {{-- Top Stat Cards (Exact match to proposed wireframe) --}}
    <div class="row g-3 mb-4">
        {{-- Total Balance --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="finance-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold">إجمالي الرصيد</span>
                    <div class="fund-icon-box" style="background: rgba(37,99,235,0.1); color: #2563eb; width: 34px; height: 34px;">
                        <i class="bi bi-cash-stack fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-0 font-monospace">{{ $fmt($totalPhysicalBalance) }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">ج.م (الرصيد الفعلي للمؤسسة)</small>
            </div>
        </div>

        {{-- Cash Total --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="finance-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold">النقدي</span>
                    <div class="fund-icon-box" style="background: rgba(16,185,129,0.1); color: #059669; width: 34px; height: 34px;">
                        <i class="bi bi-wallet fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-success mb-0 font-monospace">{{ $fmt($cashBalance) }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">ج.م (الصناديق ودور الضيافة)</small>
            </div>
        </div>

        {{-- Bank Total --}}
        <div class="col-xl col-md-4 col-sm-6">
            <div class="finance-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold">البنوك</span>
                    <div class="fund-icon-box" style="background: rgba(59,130,246,0.1); color: #1d4ed8; width: 34px; height: 34px;">
                        <i class="bi bi-bank fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-primary mb-0 font-monospace">{{ $fmt($bankBalance) }}</h4>
                <small class="text-muted" style="font-size: 0.75rem;">ج.م (الحسابات البنكية المعتمدة)</small>
            </div>
        </div>

        {{-- Today In --}}
        <div class="col-xl col-md-6 col-sm-6">
            <div class="finance-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold">قبض اليوم</span>
                    <div class="fund-icon-box" style="background: rgba(16,185,129,0.1); color: #10b981; width: 34px; height: 34px;">
                        <i class="bi bi-arrow-down-left fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-dark mb-0 font-monospace">{{ $fmt($todayIn) }} <small class="text-muted" style="font-size: 0.75rem;">ج.م</small></h4>
                <small class="text-success" style="font-size: 0.75rem;">واردات وتبرعات اليوم</small>
            </div>
        </div>

        {{-- Today Out --}}
        <div class="col-xl col-md-6 col-sm-6">
            <div class="finance-stat-card">
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <span class="text-muted small fw-bold">صرف اليوم</span>
                    <div class="fund-icon-box" style="background: rgba(239,68,68,0.1); color: #dc2626; width: 34px; height: 34px;">
                        <i class="bi bi-arrow-up-right fs-6"></i>
                    </div>
                </div>
                <h4 class="fw-bold text-danger mb-0 font-monospace">{{ $fmt($todayOut) }} <small class="text-muted" style="font-size: 0.75rem;">ج.م</small></h4>
                <small class="text-danger" style="font-size: 0.75rem;">مصروفات ومساعدات اليوم</small>
            </div>
        </div>
    </div>

    {{-- Main Tabs Navigation --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: #ffffff;">
        <div class="card-header bg-white border-0 pt-3 px-4">
            <ul class="nav nav-pills p-1 bg-light rounded-3 d-inline-flex gap-1" id="treasuryMainTabs" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active tab-pill-btn" id="funds-tab" data-bs-toggle="tab" data-bs-target="#tab-funds" type="button" role="tab">
                        <i class="bi bi-grid-fill me-1"></i> الصناديق
                        <span class="badge bg-secondary bg-opacity-25 text-dark ms-1">{{ $physicalTreasuries->count() }}</span>
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-pill-btn" id="transactions-tab" data-bs-toggle="tab" data-bs-target="#tab-transactions" type="button" role="tab">
                        <i class="bi bi-receipt-cutoff me-1"></i> الحركات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-pill-btn" id="transfers-tab" data-bs-toggle="tab" data-bs-target="#tab-transfers" type="button" role="tab">
                        <i class="bi bi-arrow-left-right me-1"></i> التوريدات والتحويلات
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link tab-pill-btn" id="custodies-tab" data-bs-toggle="tab" data-bs-target="#tab-custodies" type="button" role="tab">
                        <i class="bi bi-wallet2 me-1"></i> العهد المالية
                    </button>
                </li>
            </ul>
        </div>

        <div class="card-body p-4">
            <div class="tab-content" id="treasuryMainTabsContent">

                {{-- Tab 1: الصناديق (Treasury Cards) --}}
                <div class="tab-pane fade show active" id="tab-funds" role="tabpanel">
                    <div class="row g-4">
                        @forelse($physicalTreasuries as $treasury)
                            @php
                                $isBank = $treasury->type === 'bank' || str_contains($treasury->name, 'بنك') || str_contains($treasury->name, 'البنك');
                                $isWallet = $treasury->type === 'wallet' || str_contains($treasury->name, 'فودافون') || str_contains($treasury->name, 'انستاباي') || str_contains($treasury->name, 'إنستاباي');
                            @endphp
                            <div class="col-lg-4 col-md-6">
                                <div class="fund-card h-100 d-flex flex-column justify-content-between">
                                    {{-- Card Header --}}
                                    <div>
                                        @php
                                            $canManageThisTreasury = auth()->user()?->canManageTreasury($treasury) ?? false;
                                        @endphp
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            {{-- Dropdown Menu --}}
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-light rounded-circle text-muted" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px;">
                                                    <i class="bi bi-three-dots-vertical"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('treasuries.show', $treasury) }}">
                                                            <i class="bi bi-eye text-primary me-2"></i> تفاصيل الخزينة
                                                        </a>
                                                    </li>
                                                    @if($canManageThisTreasury)
                                                    <li>
                                                        <a class="dropdown-item py-2" href="{{ route('treasuries.edit', $treasury) }}">
                                                            <i class="bi bi-pencil text-warning me-2"></i> تعديل البيانات
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item py-2 text-success" href="#" data-bs-toggle="modal" data-bs-target="#quickSupplyModal" onclick="document.getElementById('quickFromTreasury').value='{{ $treasury->id }}';">
                                                            <i class="bi bi-box-arrow-up-right me-2"></i> توريد مالي من هذه الخزينة
                                                        </a>
                                                    </li>
                                                    @endif
                                                </ul>
                                            </div>

                                            {{-- Name & Icon --}}
                                            <div class="d-flex align-items-center gap-2">
                                                <div class="text-end">
                                                    <h6 class="fw-bold text-dark mb-0">{{ $treasury->name }}</h6>
                                                    <small class="text-muted" style="font-size: 0.78rem;">
                                                        @if($isBank)
                                                            <span class="text-primary fw-semibold"><i class="bi bi-bank me-1"></i>حساب بنكي</span>
                                                        @elseif($isWallet)
                                                            <span class="text-purple fw-semibold" style="color: #9333ea;"><i class="bi bi-phone me-1"></i>محفظة إلكترونية</span>
                                                        @else
                                                            <span class="text-success fw-semibold"><i class="bi bi-cash me-1"></i>صندوق نقدي</span>
                                                        @endif
                                                    </small>
                                                </div>
                                                <div class="fund-icon-box {{ $isBank ? 'fund-icon-bank' : ($isWallet ? 'fund-icon-wallet' : 'fund-icon-cash') }}">
                                                    <i class="bi {{ $isBank ? 'bi-bank2' : ($isWallet ? 'bi-phone-flip' : 'bi-safe2') }}"></i>
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Badges row --}}
                                        <div class="d-flex flex-wrap gap-1 mb-3">
                                            @if(!$canManageThisTreasury)
                                                <span class="badge bg-secondary-subtle text-secondary border border-secondary rounded-pill px-2.5 py-1">
                                                    <i class="bi bi-eye me-1"></i> عرض فقط
                                                </span>
                                            @endif
                                            @if($treasury->type === 'main' || $treasury->id == 1)
                                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 rounded-pill px-2.5 py-1">افتراضي</span>
                                            @endif
                                            <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1">{{ $treasury->code ?? 'ACC-'.$treasury->id }}</span>
                                            @if($treasury->manager)
                                                <span class="badge bg-light text-muted border rounded-pill px-2 py-1"><i class="bi bi-person me-1"></i>{{ Str::limit($treasury->manager->name, 16) }}</span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Card Bottom Balance --}}
                                    <div class="pt-3 border-top d-flex justify-content-between align-items-baseline">
                                        <div class="text-muted small">الرصيد:</div>
                                        <div class="text-end">
                                            <span class="fs-4 fw-bold font-monospace {{ (float)$treasury->current_balance < 0 ? 'text-danger' : 'text-success' }}">
                                                {{ $fmt($treasury->current_balance) }}
                                            </span>
                                            <small class="text-muted ms-1" style="font-size: 0.8rem;">{{ $treasury->currency ?? 'ج.م' }}</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12 text-center py-5">
                                <div class="text-muted mb-3"><i class="bi bi-safe fs-1 opacity-25"></i></div>
                                <h5 class="text-muted">لا توجد خزائن فعلية مسجلة بعد</h5>
                                <a href="{{ route('treasuries.create') }}" class="btn btn-primary mt-2">إضافة أول خزينة</a>
                            </div>
                        @endforelse
                    </div>
                </div>

                {{-- Tab 2: الحركات (Transactions) --}}
                <div class="tab-pane fade" id="tab-transactions" role="tabpanel">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>#</th>
                                    <th>التاريخ</th>
                                    <th>الخزينة</th>
                                    <th>النوع</th>
                                    <th>المبلغ</th>
                                    <th>البيان والملاحظات</th>
                                    <th>المسؤول</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransactions as $tx)
                                    <tr>
                                        <td class="text-muted small">#{{ $tx->id }}</td>
                                        <td class="font-monospace small">{{ $tx->transaction_date ? \Carbon\Carbon::parse($tx->transaction_date)->format('Y-m-d') : $tx->created_at->format('Y-m-d') }}</td>
                                        <td class="fw-semibold">{{ $tx->treasury->name ?? '—' }}</td>
                                        <td>
                                            @if($tx->type === 'in')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">قبض (وارد)</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-2.5 py-1">صرف (صادر)</span>
                                            @endif
                                        </td>
                                        <td class="fw-bold font-monospace {{ $tx->type === 'in' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'in' ? '+' : '-' }}{{ $fmt($tx->amount) }} ج.م
                                        </td>
                                        <td class="small text-muted">{{ $tx->description ?? '—' }}</td>
                                        <td class="small">{{ $tx->createdBy->name ?? 'النظام' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد حركات حديثة مسجلة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab 3: التوريدات والتحويلات (Transfers) --}}
                <div class="tab-pane fade" id="tab-transfers" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">سجل التوريدات والتحويلات النقدية بين الخزائن</h6>
                        <button type="button" class="btn btn-primary btn-sm rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#quickSupplyModal">
                            <i class="bi bi-plus-lg me-1"></i> تسجيل طلب توريد جديد
                        </button>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>رقم السند</th>
                                    <th>التاريخ</th>
                                    <th>من الخزينة</th>
                                    <th>إلى الخزينة</th>
                                    <th>المبلغ</th>
                                    <th>الحالة</th>
                                    <th>المسلم / المستلم</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentTransfers as $trf)
                                    <tr>
                                        <td class="font-monospace fw-bold text-primary">{{ $trf->transfer_number }}</td>
                                        <td class="font-monospace small">{{ $trf->requested_on ? $trf->requested_on->format('Y-m-d') : '—' }}</td>
                                        <td class="fw-semibold">{{ $trf->fromAccount->name ?? '—' }}</td>
                                        <td class="fw-semibold">{{ $trf->toAccount->name ?? '—' }}</td>
                                        <td class="fw-bold font-monospace text-dark">{{ $fmt($trf->amount) }} ج.م</td>
                                        <td>
                                            @if($trf->status === 'received')
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">تم الاستلام والمطابقة</span>
                                            @elseif($trf->status === 'submitted')
                                                <span class="badge bg-warning bg-opacity-10 text-warning text-dark border border-warning border-opacity-25 px-2.5 py-1">بانتظار تأكيد الاستلام</span>
                                            @else
                                                <span class="badge bg-secondary px-2 py-1">{{ $trf->status }}</span>
                                            @endif
                                        </td>
                                        <td class="small">
                                            <div><span class="text-muted">مُسلّم:</span> {{ $trf->requester->name ?? '—' }}</div>
                                            @if($trf->receiver)
                                                <div><span class="text-success">مُستلم:</span> {{ $trf->receiver->name }}</div>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                @if($trf->status === 'submitted')
                                                    <form method="POST" action="{{ route('transfers.confirmReceipt', $trf) }}" onsubmit="return confirm('هل تؤكد استلام المبلغ فعلياً وعدّه؟');">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success px-2 py-1" title="تأكيد الاستلام">
                                                            <i class="bi bi-check-lg"></i> تأكيد الاستلام
                                                        </button>
                                                    </form>
                                                @endif
                                                <a href="{{ route('transfers.printVoucher', $trf) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1" title="طباعة سند التوريد">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد تحويلات أو توريدات مسجلة حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Tab 4: العهد المالية (Custodies) --}}
                <div class="tab-pane fade" id="tab-custodies" role="tabpanel">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">كشف العهد المالية الحالية للموظفين</h6>
                        <a href="{{ route('custodies.create') }}" class="btn btn-success btn-sm rounded-pill px-3">
                            <i class="bi bi-plus-lg me-1"></i> صرف عهدة جديدة
                        </a>
                    </div>

                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr class="text-muted small">
                                    <th>رقم العهدة</th>
                                    <th>الموظف المستلم</th>
                                    <th>تاريخ الصرف</th>
                                    <th>قيمة العهدة</th>
                                    <th>المصروف / المعتمد</th>
                                    <th>المتبقي للرد</th>
                                    <th>الحالة</th>
                                    <th>إجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($recentCustodies as $custody)
                                    <tr>
                                        <td class="font-monospace fw-bold text-dark">
                                            <a href="{{ route('custodies.show', $custody) }}" class="text-decoration-none">
                                                {{ $custody->custody_number }}
                                            </a>
                                        </td>
                                        <td class="fw-semibold">
                                            <i class="bi bi-person-badge text-muted me-1"></i>
                                            {{ $custody->holder->name ?? '—' }}
                                        </td>
                                        <td class="font-monospace small">{{ $custody->issued_on ? $custody->issued_on->format('Y-m-d') : '—' }}</td>
                                        <td class="fw-bold font-monospace">{{ $fmt($custody->issued_amount) }} ج.م</td>
                                        <td class="font-monospace text-primary">{{ $fmt($custody->settled_amount) }} ج.م</td>
                                        <td class="font-monospace text-danger fw-bold">{{ $fmt($custody->remaining_to_return) }} ج.م</td>
                                        <td>
                                            @php $st = $custody->status_meta; @endphp
                                            <span class="badge {{ $st['class'] }} px-2.5 py-1">{{ $st['label'] }}</span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('custodies.show', $custody) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1" title="كشف العهدة والتسوية">
                                                    <i class="bi bi-card-checklist me-1"></i> التسوية
                                                </a>
                                                <a href="{{ route('custodies.printVoucher', $custody) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1" title="طباعة سند الصرف">
                                                    <i class="bi bi-printer"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="text-center py-4 text-muted">لا توجد عهد مالية مفتوحة حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Bottom Summary Card (ملخص الخزائن الفعلية - للإدارة والمالية الشاملة فقط) --}}
    @if($isGlobalTreasuryAccess ?? true)
    <div class="summary-overview-box mb-4">
        <h5 class="fw-bold mb-4 text-dark d-flex align-items-center gap-2">
            <i class="bi bi-pie-chart-fill text-primary"></i>
            ملخص الخزائن الفعلية
        </h5>

        <div class="row g-3 mb-4">
            {{-- Bank Treasury Tile --}}
            <div class="col-md-3 col-sm-6">
                <div class="summary-tile summary-tile-bank">
                    <div class="text-muted small fw-semibold mb-1">خزينة البنك</div>
                    <div class="fs-3 fw-bold text-primary font-monospace">{{ number_format($bankBalance, 0) }}</div>
                    <small class="text-muted">جنيه</small>
                </div>
            </div>

            {{-- Central Treasury Tile --}}
            <div class="col-md-3 col-sm-6">
                <div class="summary-tile summary-tile-central">
                    <div class="text-muted small fw-semibold mb-1">الخزينة المركزية (الصندوق)</div>
                    <div class="fs-3 fw-bold text-dark font-monospace">
                        {{ number_format($centralTreasury ? $centralTreasury->current_balance : 0, 0) }}
                    </div>
                    <small class="text-muted">جنيه</small>
                </div>
            </div>

            {{-- Accounts Treasury Tile --}}
            <div class="col-md-3 col-sm-6">
                <div class="summary-tile summary-tile-accounts">
                    <div class="text-muted small fw-semibold mb-1">خزينة الحسابات</div>
                    <div class="fs-3 fw-bold text-success font-monospace">
                        {{ number_format($accountsTreasury ? $accountsTreasury->current_balance : 0, 0) }}
                    </div>
                    <small class="text-muted">جنيه</small>
                </div>
            </div>

            {{-- Sub Treasuries Tile (Clickable to open modal) --}}
            <div class="col-md-3 col-sm-6">
                <div class="summary-tile summary-tile-sub" data-bs-toggle="modal" data-bs-target="#subTreasuriesModal" title="اضغط لعرض تفاصيل الخزائن الفرعية">
                    <div class="text-muted small fw-semibold mb-1 d-flex justify-content-center align-items-center gap-1">
                        <span>الخزائن الفرعية</span>
                        <i class="bi bi-box-arrow-up-right text-primary" style="font-size: 0.75rem;"></i>
                    </div>
                    <div class="fs-3 fw-bold text-dark font-monospace">
                        {{ number_format($subTreasuries->sum('current_balance'), 0) }}
                    </div>
                    <small class="text-primary fw-semibold" style="font-size: 0.75rem;">(اضغط لعرض التفاصيل)</small>
                </div>
            </div>
        </div>

        {{-- Grand Total Physical Balance --}}
        <div class="pt-3 border-top text-center">
            <div class="text-muted small mb-1">إجمالي الأموال الفعلية في المؤسسة:</div>
            <div class="display-6 fw-bold text-success font-monospace mb-1">
                {{ $fmt($totalPhysicalBalance) }} <span class="fs-5">جنيه</span>
            </div>
            <div class="text-muted small">
                <i class="bi bi-shield-check text-success me-1"></i>
                أرقام فعلية ومطابقة محاسبياً لمنع التكرار (Double-Counting)
            </div>
        </div>
    </div>
    @endif

</div>

{{-- MODAL 1: الخزائن الفرعية المنبثقة --}}
<div class="modal fade" id="subTreasuriesModal" tabindex="-1" aria-labelledby="subTreasuriesModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                <h5 class="modal-title fw-bold" id="subTreasuriesModalLabel">
                    <i class="bi bi-layers-half text-primary me-2"></i>
                    تفاصيل الخزائن الفرعية (الفروع ودور الضيافة والاستقبال)
                </h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-muted small">
                                <th>اسم الخزينة الفرعية</th>
                                <th>النوع / التبعية</th>
                                <th>المسؤول</th>
                                <th>الرصيد الفعلي الحالي</th>
                                <th>إجراء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($subTreasuries as $sub)
                                <tr>
                                    <td class="fw-bold text-dark">
                                        <i class="bi bi-safe me-1 text-muted"></i>
                                        {{ $sub->name }}
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-muted border px-2 py-1">{{ $sub->type_name }}</span>
                                    </td>
                                    <td class="small">{{ $sub->manager->name ?? 'غير محدد' }}</td>
                                    <td class="fw-bold font-monospace text-success fs-6">
                                        {{ $fmt($sub->current_balance) }} ج.م
                                    </td>
                                    <td>
                                        <a href="{{ route('treasuries.show', $sub) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            عرض
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">لا توجد خزائن فرعية مسجلة</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light">
                            <tr class="fw-bold">
                                <td colspan="3" class="text-end">الإجمالي الفرعي:</td>
                                <td class="text-success font-monospace fs-6">{{ $fmt($subTreasuries->sum('current_balance')) }} ج.م</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
            </div>
        </div>
    </div>
</div>

{{-- MODAL 2: تسجيل طلب توريد سريع --}}
<div class="modal fade" id="quickSupplyModal" tabindex="-1" aria-labelledby="quickSupplyModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" action="{{ route('transfers.storeSupply') }}">
                @csrf
                <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold" id="quickSupplyModalLabel">
                        <i class="bi bi-box-arrow-in-left text-success me-2"></i>
                        تسجيل طلب توريد نقدية بين الخزائن
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label fw-bold small">الخزينة الموردة (المصدر) <span class="text-danger">*</span></label>
                        <select name="from_treasury_id" id="quickFromTreasury" class="form-select rounded-3" required>
                            <option value="">-- اختر الخزينة الموردة --</option>
                            @foreach($physicalTreasuries as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} (رصيد: {{ $fmt($t->current_balance) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">الخزينة المستلمة (الهدف) <span class="text-danger">*</span></label>
                        <select name="to_treasury_id" class="form-select rounded-3" required>
                            <option value="">-- اختر الخزينة المستلمة --</option>
                            @foreach($physicalTreasuries as $t)
                                <option value="{{ $t->id }}">{{ $t->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">المبلغ المراد توريده <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <input type="number" step="0.01" min="1" name="amount" class="form-control rounded-start-3" placeholder="0.00" required>
                                <span class="input-group-text">ج.م</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">نوع التوريد</label>
                            <select name="transfer_type" class="form-select rounded-3">
                                <option value="supply">توريد دوري للخزينة</option>
                                <option value="bank_deposit">إيداع بنكي مباشر</option>
                                <option value="internal_transfer">تحويل داخلي</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">رقم الإيصال / المرجع البنكي (إن وجد)</label>
                        <input type="text" name="reference" class="form-control rounded-3" placeholder="رقم حافظة الإيداع أو إيصال التوريد">
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات التوريد</label>
                        <textarea name="note" class="form-control rounded-3" rows="2" placeholder="أي ملاحظات حول التوريد أو أسماء الإيصالات"></textarea>
                    </div>

                    <div class="alert alert-info py-2 px-3 small rounded-3 mb-0">
                        <i class="bi bi-info-circle me-1"></i>
                        يتم خصم المبلغ من الخزينة الموردة فوراً، ولا يُضاف للخزينة المستلمة إلا بعد تأكيد الاستلام الفعلي من المحاسب/المستلم.
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">تأكيد وإصدار طلب التوريد</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
