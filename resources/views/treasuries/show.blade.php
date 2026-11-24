@extends('layouts.app')

@section('content')
<style>
    @media print {
        @page {
            size: A4 portrait;
            margin: 6mm 8mm;
        }
        body * {
            visibility: hidden;
        }
        .no-print,
        .alert,
        nav,
        header,
        footer,
        .sidebar,
        #universalPrintPreviewModal {
            display: none !important;
        }
        #printableTreasuryStatement,
        #printableTreasuryStatement * {
            visibility: visible !important;
        }
        #printableTreasuryStatement {
            display: block !important;
            position: absolute !important;
            top: 0 !important;
            right: 0 !important;
            left: 0 !important;
            width: 100% !important;
            background: #ffffff !important;
            color: #0f172a !important;
            padding: 0 !important;
            margin: 0 !important;
        }
    }
</style>

<div class="container-fluid py-4 no-print">
    @php
        $currentUser = auth()->user();
        $canManage = $canManage ?? ($currentUser?->canManageTreasury($treasury) ?? false);
    @endphp
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-safe me-2"></i>{{ $treasury->name }}</h2>
            <p class="text-muted">{{ $treasury->code }}</p>
        </div>
        <div class="col-md-4 text-end">
            @if($canManage)
            <a href="{{ route('treasuries.edit', $treasury) }}" class="btn btn-warning">
                <i class="bi bi-pencil me-2"></i>
                تعديل
            </a>
            <form action="{{ route('treasuries.destroy', $treasury) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخزينة؟ لا يمكن حذف الخزائن التي تحتوي على حركات مالية.')">
                @csrf @method('DELETE')
                <button class="btn btn-danger">
                    <i class="bi bi-trash me-2"></i>
                    حذف
                </button>
            </form>
            @else
            <span class="badge bg-secondary-subtle text-secondary border border-secondary px-3 py-2 fs-6">
                <i class="bi bi-eye me-1"></i> صلاحية عرض فقط
            </span>
            @endif
            <a href="{{ route('treasuries.index') }}" class="btn btn-secondary">
                <i class="bi bi-arrow-right me-2"></i>
                رجوع
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show no-print" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistics --}}
    @php
        $txBreakdown = $treasury->balance_breakdown;
        $fmt = fn($num) => \App\Models\Treasury::formatAmount($num);
    @endphp
    <div class="row g-3 mb-4 animate-slide-up">
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                @if($canManage)
                <button type="button" class="btn btn-sm btn-link text-primary position-absolute top-0 start-0 m-2 p-1" title="تعديل الرصيد الحالي يدوياً" onclick="openAdjustTotalsModal('balance')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                @endif
                <h6 class="text-muted mb-1 small">إجمالي الرصيد الحالي</h6>
                <h3 class="text-primary mb-1 fw-bold font-monospace">{{ $fmt($treasury->current_balance) }}</h3>
                <small class="text-muted d-block mb-2">{{ $treasury->currency }}</small>
                <div class="d-flex justify-content-center gap-1 flex-wrap border-top pt-2">
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-monospace" title="صافي رصيد العهدة">عهدة: {{ $fmt($txBreakdown['custody_balance']) }}</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" title="صافي رصيد التبرعات">تبرعات: {{ $fmt($txBreakdown['donations_balance']) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                <h6 class="text-muted mb-1 small">تفصيل الخزينة (العهدة والتبرعات)</h6>
                <div class="d-flex justify-content-around align-items-center my-2">
                    <div>
                        <div class="small text-info fw-bold"><i class="bi bi-wallet2 me-1"></i>العهدة</div>
                        <div class="fw-bold font-monospace fs-5 text-dark">{{ $fmt($txBreakdown['custody_balance']) }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">وارد: {{ $fmt($txBreakdown['custody_in']) }}</small>
                    </div>
                    <div class="vr"></div>
                    <div>
                        <div class="small text-success fw-bold"><i class="bi bi-heart-fill me-1"></i>التبرعات</div>
                        <div class="fw-bold font-monospace fs-5 text-dark">{{ $fmt($txBreakdown['donations_balance']) }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">وارد: {{ $fmt($txBreakdown['donations_in']) }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                @if($canManage)
                <button type="button" class="btn btn-sm btn-link text-success position-absolute top-0 start-0 m-2 p-1" title="تعديل إجمالي الوارد يدوياً" onclick="openAdjustTotalsModal('in')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                @endif
                <h6 class="text-muted mb-2 small">إجمالي الوارد</h6>
                <h3 class="text-success mb-0 fw-bold font-monospace">{{ $fmt($stats['total_in']) }}</h3>
                <small class="text-muted">{{ $treasury->currency }}@if((float)$treasury->opening_balance > 0) <span class="badge bg-light text-secondary border ms-1" style="font-size: 0.68rem;" title="الرصيد الافتتاحي للخزينة">+ افتتاحي: {{ $fmt($treasury->opening_balance) }}</span>@endif</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                @if($canManage)
                <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 start-0 m-2 p-1" title="تعديل إجمالي الصادر يدوياً" onclick="openAdjustTotalsModal('out')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                @endif
                <h6 class="text-muted mb-2 small">إجمالي الصادر</h6>
                <h3 class="text-danger mb-0 fw-bold font-monospace">{{ $fmt($stats['total_out']) }}</h3>
                <small class="text-muted">{{ $treasury->currency }} ({{ $stats['total_transactions'] }} حركة)</small>
            </div>
        </div>
    </div>

    <div class="row g-4 animate-slide-up">
        <div class="col-lg-8">
            {{-- Recent Transactions --}}
            <div class="chart-container mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 section-title"><i class="bi bi-clock-history me-2"></i>آخر الحركات</h5>
                    <div class="d-flex gap-2 flex-wrap">
                        @if($canManage)
                        <form action="{{ route('treasuries.adjustTotals', $treasury) }}" method="POST" class="d-inline" onsubmit="return confirm('هل تريد بالتأكيد تصفير الرصيد الحالي لهذه الخزينة يدوياً إلى (0.00 ج.م)؟');">
                            @csrf
                            <input type="hidden" name="zero_treasury" value="1">
                            <input type="hidden" name="zero_mode" value="balance_only">
                            <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> تصفير الخزينة يدوياً (0.00)
                            </button>
                        </form>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill px-3" onclick="openAdjustTotalsModal('in')">
                            <i class="bi bi-sliders me-1"></i> تعديل الصادر والوارد يدوياً
                        </button>
                        @endif
                        <button type="button" class="btn btn-sm btn-outline-info rounded-pill px-3" onclick="previewTreasuryStatement()">
                            <i class="bi bi-eye me-1"></i> معاينة قبل الطباعة
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="printTreasuryStatement()">
                            <i class="bi bi-printer me-1"></i> طباعة كشف حساب
                        </button>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>طريقة الدفع / القناة</th>
                                <th>المبلغ</th>
                                <th>الوصف</th>
                                <th>المسجل بواسطة</th>
                                <th>المصدر / الجهة</th>
                                <th class="text-center" style="width: 110px;">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            @php
                                $don = $transaction->donation;
                                $method = $don ? $don->payment_method : null;
                                $desc = (string)$transaction->description;
                                if (!$method) {
                                    if (str_contains($desc, 'فودافون') || str_contains($desc, 'vodafone')) $method = 'vodafone_cash';
                                    elseif (str_contains($desc, 'انستا') || str_contains($desc, 'instapay') || str_contains($desc, 'إنستا')) $method = 'instapay';
                                    elseif (str_contains($desc, 'بنك') || str_contains($desc, 'تحويل')) $method = 'bank_transfer';
                                    elseif (str_contains($desc, 'نقدي') || str_contains($desc, 'كاش')) $method = 'cash';
                                    elseif (str_contains($desc, 'فوري')) $method = 'fawry';
                                }
                            @endphp
                            <tr>
                                <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($transaction->type === 'in')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">وارد</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">صادر</span>
                                    @endif
                                </td>
                                <td>
                                    @if($method === 'vodafone_cash')
                                        <span class="badge rounded-pill fw-bold" style="background: #fee2e2; color: #dc2626; border: 1px solid #fca5a5; font-size: 0.74rem; padding: 4px 9px;">
                                            <i class="bi bi-phone-fill me-1"></i> فودافون كاش
                                        </span>
                                    @elseif($method === 'instapay')
                                        <span class="badge rounded-pill fw-bold" style="background: #f3e8ff; color: #7e22ce; border: 1px solid #d8b4fe; font-size: 0.74rem; padding: 4px 9px;">
                                            <i class="bi bi-lightning-charge-fill me-1"></i> إنستاباي
                                        </span>
                                    @elseif($method === 'cash')
                                        <span class="badge rounded-pill fw-bold" style="background: #ecfdf5; color: #047857; border: 1px solid #a7f3d0; font-size: 0.74rem; padding: 4px 9px;">
                                            <i class="bi bi-cash-stack me-1"></i> نقدي (كاش)
                                        </span>
                                    @elseif($method === 'bank_transfer')
                                        <span class="badge rounded-pill fw-bold" style="background: #e0f2fe; color: #0284c7; border: 1px solid #bae6fd; font-size: 0.74rem; padding: 4px 9px;">
                                            <i class="bi bi-bank2 me-1"></i> تحويل بنكي
                                        </span>
                                    @elseif($method === 'fawry')
                                        <span class="badge rounded-pill fw-bold" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a; font-size: 0.74rem; padding: 4px 9px;">
                                            <i class="bi bi-upc-scan me-1"></i> فوري
                                        </span>
                                    @else
                                        <span class="badge bg-light text-muted border rounded-pill px-2.5 py-1 small">
                                            {{ $transaction->type === 'in' ? 'نقدي' : 'مصروف' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="fw-bold font-monospace">{{ $fmt($transaction->amount) }} {{ $treasury->currency }}</td>
                                <td class="text-dark">
                                    <div class="fw-semibold">{{ $transaction->description }}</div>
                                    @if($transaction->donation_id)
                                        <div class="mt-1">
                                            <a href="{{ route('donations.show', $transaction->donation_id) }}" target="_blank" class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 text-decoration-none">
                                                <i class="bi bi-receipt me-1"></i> إيصال تبرع #{{ $transaction->donation_id }}
                                            </a>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $transaction->registrar_name }}</div>
                                    @if($transaction->registrar_title)
                                        <span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-25 small" style="font-size: 0.7rem;">
                                            {{ $transaction->registrar_title }}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge rounded-pill fw-semibold" style="background: #eef2ff; color: #4338ca; border: 1px solid #c7d2fe; font-size: 0.74rem; padding: 4px 8px;">
                                        <i class="bi bi-geo-alt-fill me-1"></i> {{ $transaction->source_label }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    @if($canManage)
                                    <div class="d-inline-flex gap-1">
                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary py-0 px-2"
                                            title="تعديل الحركة"
                                            data-tx-id="{{ $transaction->id }}"
                                            data-tx-url="{{ route('treasuries.updateTransaction', [$treasury, $transaction]) }}"
                                            data-tx-type="{{ $transaction->type }}"
                                            data-tx-amount="{{ (float) $transaction->amount }}"
                                            data-tx-description="{{ e($transaction->description) }}"
                                            data-tx-date="{{ $transaction->transaction_date->format('Y-m-d') }}"
                                            onclick="openEditTransactionModalFromBtn(this)">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('treasuries.deleteTransaction', [$treasury, $transaction]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الحركة المالية؟')">
                                            @csrf
                                            @method('DELETE')
                                            <input type="hidden" name="recalculate_balance" value="1">
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-0 px-2" title="حذف الحركة">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="badge bg-light text-muted border" style="font-size: 0.72rem;"><i class="bi bi-lock me-1"></i>عرض فقط</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-5">
                                    <i class="bi bi-folder-x fa-3x mb-3 d-block opacity-25"></i>
                                    لا توجد حركات مالية متاحة حالياً
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Treasury Info --}}
            <div class="summary-panel">
                <h5 class="fw-bold mb-4 section-title"><i class="bi bi-info-circle me-2"></i>معلومات عامة</h5>
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50">
                            <label class="text-muted small d-block mb-1">اسم الخزينة</label>
                            <span class="fw-bold text-dark">{{ $treasury->name }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50">
                            <label class="text-muted small d-block mb-1">الكود التعريفي</label>
                            <span class="fw-bold font-monospace text-dark">{{ $treasury->code }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50">
                            <label class="text-muted small d-block mb-1">مدير المسؤول</label>
                            <span class="fw-bold text-dark">{{ $treasury->manager->name ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50">
                            <label class="text-muted small d-block mb-1">موقع الخزينة</label>
                            <span class="fw-bold text-dark">{{ $treasury->location ?? 'غير محدد' }}</span>
                        </div>
                    </div>
                    @if($treasury->description)
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-light bg-opacity-50">
                            <label class="text-muted small d-block mb-1">ملاحظات إضافية</label>
                            <p class="mb-0 text-dark opacity-75">{{ $treasury->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($treasury->linkedTreasury)
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-primary bg-opacity-10 border border-primary border-opacity-25 d-flex align-items-center justify-content-between">
                            <div>
                                <label class="text-primary small d-block mb-1 fw-bold"><i class="bi bi-link-45deg me-1"></i>الخزينة المرتبطة (تسميع آلي فوري للأرصدة والحركات)</label>
                                <span class="fw-bold text-dark">{{ $treasury->linkedTreasury->name }} ({{ $treasury->linkedTreasury->code }})</span>
                            </div>
                            <a href="{{ route('treasuries.show', $treasury->linkedTreasury) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                <i class="bi bi-box-arrow-up-right me-1"></i> عرض الخزينة المرتبطة
                            </a>
                        </div>
                    </div>
                    @endif

                    @if($treasury->linkedSources && $treasury->linkedSources->isNotEmpty())
                    <div class="col-12">
                        <div class="p-3 rounded-4 bg-info bg-opacity-10 border border-info border-opacity-25">
                            <label class="text-info-emphasis small d-block mb-1 fw-bold"><i class="bi bi-arrow-down-left-square me-1"></i>قنوات وخزائن فرعية مرتبطة تسمّع في هذا الحساب:</label>
                            <div class="d-flex flex-wrap gap-2 mt-1">
                                @foreach($treasury->linkedSources as $source)
                                    <a href="{{ route('treasuries.show', $source) }}" class="badge bg-white text-dark border text-decoration-none p-2 shadow-sm rounded-pill">
                                        <i class="bi bi-wallet2 text-primary me-1"></i> {{ $source->name }} ({{ $source->code }})
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="glass-card mb-4 p-4 border-0 shadow-sm" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1) !important;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 section-title text-white opacity-75"><i class="bi bi-lightning me-2"></i>إجراءات سريعة</h6>
                    @php $cu = $currentUser ?? auth()->user(); @endphp
                    @if($cu && ($cu->hasRole('admin') || $cu->hasRole('manager')))<a href="{{ route('change-requests.index') }}" class="btn btn-sm btn-link text-white text-decoration-none opacity-50 hover-opacity-100 p-0">
                            <i class="bi bi-shield-check fs-5"></i>
                        </a>
                    @endif
                </div>

                <div class="d-grid gap-3">
                    @php
                        $pendingRequest = \App\Models\ChangeRequest::where('model_type', \App\Models\Treasury::class)
                            ->where('model_id', $treasury->id)
                            ->where('status', 'pending')
                            ->first();
                    @endphp

                    @if(!$canManage)
                        <div class="alert alert-light text-secondary border small text-center mb-0">
                            <i class="bi bi-info-circle me-1"></i> لديك صلاحية <strong>عرض فقط</strong> على هذه الخزينة. لا يمكنك إجراء تعديلات أو إضافة حركات مالية.
                        </div>
                    @elseif(isset($pendingRequest))
                        <div class="alert alert-warning mb-0 p-3 border-0 bg-warning bg-opacity-10 text-warning rounded-4 shadow-sm animate-pulse">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-hourglass-split fs-5"></i>
                                <span class="fw-bold small">طلب قيد المراجعة</span>
                            </div>
                            <p class="x-small mb-3 opacity-75">يوجد طلب تعديل أو حذف لهذه الخزينة قيد المراجعة حالياً.</p>
                            
                            <form action="{{ route('change-requests.cancel', $pendingRequest) }}" method="POST" class="mt-2" onsubmit="return confirm('هل تريد بالتأكيد إلغاء هذا الطلب؟')">
                                @csrf
                                <button class="btn btn-outline-danger w-100 rounded-pill shadow-sm py-2">
                                    <i class="bi bi-x-circle me-1"></i> إلغاء الطلب
                                </button>
                            </form>
                        </div>
                    @else
                        <button type="button" class="btn btn-success rounded-pill shadow-sm py-2 hover-lift fw-bold w-100" onclick="openAdjustTotalsModal('in')">
                            <i class="bi bi-sliders me-1"></i> تعديل الصادر والوارد والرصيد
                        </button>

                        <a href="{{ route('treasuries.edit', $treasury) }}" class="btn btn-light text-primary rounded-pill shadow-sm py-2 hover-lift fw-bold">
                            <i class="bi bi-pencil me-1"></i> تعديل بيانات الخزينة
                        </a>

                        <form action="{{ route('treasuries.destroy', $treasury) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد حذف هذه الخزينة؟')">
                            @csrf @method('DELETE')
                            <button class="btn btn-outline-light rounded-pill shadow-sm py-2 w-100 hover-lift">
                                <i class="bi bi-trash3 me-1"></i> طلب حذف الخزينة
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            @if($canManage)
            {{-- Add Transaction --}}
            <div class="summary-panel mb-4 shadow-sm border rounded-4 bg-white p-3">
                <h5 class="fw-bold mb-3 section-title small text-uppercase letter-spacing-1 text-danger">
                    <i class="bi bi-box-arrow-up me-2"></i>إضافة حركة مالية (صادر وتوريد)
                </h5>
                <form id="addTransactionForm" action="{{ route('treasuries.addTransaction', $treasury) }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" value="out">
                    @php
                        $authUser = auth()->user();
                        $treasuryList = $accessibleTreasuries ?? \App\Models\Treasury::accessibleForUser($authUser)->where('is_active', true)->orderBy('name')->get();
                        if ($treasuryList->isEmpty() || !$treasuryList->contains('id', $treasury->id)) {
                            $treasuryList = $treasuryList->prepend($treasury);
                        }

                        // الخزائن التي يديرها الموظف ومسؤول عنها فقط (الخزنة اللي أنا ماسكها بس)
                        $manageableTreasuries = $treasuryList->filter(function ($t) use ($authUser) {
                            return $authUser->canManageTreasury($t);
                        });
                        if ($canManage && !$manageableTreasuries->contains('id', $treasury->id)) {
                            $manageableTreasuries = $manageableTreasuries->prepend($treasury);
                        }

                        // جميع الخزائن المتاحة للتوريد إليها
                        $allActiveTreasuries = \App\Models\Treasury::where('is_active', true)->orderBy('name')->get();

                        // صلاحية رؤية رصيد الخزائن الأخرى (فقط للمدير أو الحسابات)
                        $canSeeOtherBalances = $authUser && (
                            $authUser->isSuperAdmin()
                            || $authUser->hasRole('admin')
                            || $authUser->hasRole('manager')
                            || $authUser->hasRole('مدير المؤسسة بالكامل')
                            || $authUser->hasRole('حسابات ماليه')
                            || $authUser->hasRole('accountant')
                            || str_contains(strtolower((string) $authUser->department), 'حسابات')
                            || str_contains(strtolower((string) $authUser->job_title), 'محاسب')
                        );
                    @endphp

                    {{-- 1. الخزينة المخصوم منها (المصدر: الخزنة اللي أنا ماسكها بس) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-box-arrow-up text-danger me-1"></i>الخزينة المخصوم منها (المصدر) <span class="text-danger">*</span>
                        </label>
                        <select name="treasury_id" id="outSourceTreasurySelect" class="form-select form-select-sm rounded-3 fw-bold" onchange="onSourceTreasuryChange(this)">
                            @foreach($manageableTreasuries as $t)
                                <option value="{{ $t->id }}" @selected($t->id === $treasury->id) data-name="{{ $t->name }}" data-currency="{{ $t->currency }}">
                                    {{ $t->name }} (الرصيد: {{ $fmt($t->current_balance) }} {{ $t->currency }})
                                </option>
                            @endforeach
                        </select>
                        <div class="x-small text-muted mt-1">
                            <i class="bi bi-shield-check text-success me-1"></i>الخزائن التي تديرها ومسؤول عنها فقط.
                        </div>
                    </div>

                    {{-- 2. الخزينة المورّد إليها (المستلمة: يظهر كل الخزنات، والرصيد يظهر فقط للمدير أو الحسابات) --}}
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-dark mb-1">
                            <i class="bi bi-box-arrow-in-down text-success me-1"></i>الخزينة المورّد إليها (المستلمة) <span class="text-danger">*</span>
                        </label>
                        <select name="destination_treasury_id" id="outDestTreasurySelect" class="form-select form-select-sm rounded-3" onchange="updateTxSyncNotice()">
                            <option value="">-- صرف خارجي / مصروفات / مساعدات / أخرى --</option>
                            @foreach($allActiveTreasuries as $destT)
                                <option value="{{ $destT->id }}" @if($destT->id === $treasury->id) class="d-none" disabled @endif data-name="{{ $destT->name }}" data-id="{{ $destT->id }}">
                                    @if($canSeeOtherBalances || $authUser->canManageTreasury($destT))
                                        توريد وإيداع في: {{ $destT->name }} (الرصيد: {{ $fmt($destT->current_balance) }} {{ $destT->currency }})
                                    @else
                                        توريد وإيداع في: {{ $destT->name }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        <div class="x-small text-muted mt-1">
                            اختر إحدى الخزائن لتوريد وإيداع المبلغ فيها مباشرة، أو اتركها صرف خارجي.
                        </div>
                    </div>

                    {{-- رسالة توضيحية للتسميع الفوري في الخزنتين --}}
                    <div id="txSyncNotice" class="alert alert-danger py-2 px-3 rounded-3 x-small mb-3 d-flex align-items-center gap-2 border border-danger">
                        <i class="bi bi-info-circle-fill text-danger flex-shrink-0 fs-6"></i>
                        <span id="txSyncNoticeText">سيتم تسجيل حركة صادر مباشر وخصم المبلغ من الخزينة فوراً.</span>
                    </div>

                    {{-- 3. المبلغ والعملة --}}
                    <div class="input-group input-group-sm mb-3">
                        <input type="number" name="amount" id="txAmountInput" class="form-control rounded-start-3" placeholder="المبلغ المطلوب" step="0.01" min="0.01" required oninput="updateTxSyncNotice()">
                        <span class="input-group-text bg-light border-start-0 fw-bold" id="txCurrencyLabel">{{ $treasury->currency }}</span>
                    </div>

                    {{-- 4. وصف الحركة --}}
                    <div class="mb-3">
                        <textarea name="description" class="form-control form-control-sm rounded-3" placeholder="وصف الحركة / سبب الصرف أو التوريد..." rows="2" required></textarea>
                    </div>

                    {{-- 5. التاريخ --}}
                    <div class="mb-3">
                        <input type="date" name="transaction_date" class="form-control form-control-sm rounded-3" value="{{ date('Y-m-d') }}" required>
                    </div>

                    <button type="submit" class="btn btn-danger btn-sm w-100 rounded-pill shadow-sm fw-bold py-2">
                        <i class="bi bi-check2-circle me-1"></i> تسجيل حركة الصادر / التوريد
                    </button>
                </form>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Official Printable Treasury Statement (Single-Page A4 Layout) --}}
<div id="printableTreasuryStatement" class="d-none" data-printable>
    {{-- Metadata Strip --}}
    <table class="table table-bordered mb-2" style="width: 100%; border-collapse: collapse; font-size: 11.5px; margin-bottom: 8px !important;">
        <tbody>
            <tr>
                <td style="background-color: #f8fafc !important; font-weight: 700; width: 14%; padding: 5px 8px;">اسم الخزينة</td>
                <td style="font-weight: 800; width: 22%; padding: 5px 8px;">{{ $treasury->name }}</td>
                <td style="background-color: #f8fafc !important; font-weight: 700; width: 12%; padding: 5px 8px;">كود الخزينة</td>
                <td style="font-family: monospace; font-weight: 700; width: 16%; padding: 5px 8px;">{{ $treasury->code }}</td>
                <td style="background-color: #f8fafc !important; font-weight: 700; width: 14%; padding: 5px 8px;">المدير / الموقع</td>
                <td style="width: 22%; padding: 5px 8px;">{{ $treasury->manager->name ?? 'غير محدد' }} — {{ $treasury->location ?? 'غير محدد' }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Financial Summary Strip (4 Horizontal Cells in 1 Row) --}}
    <table class="table table-bordered text-center mb-2" style="width: 100%; border-collapse: collapse; margin-bottom: 10px !important;">
        <thead>
            <tr>
                <th style="background-color: #f1f5f9 !important; color: #0f172a !important; width: 25%; padding: 5px; font-size: 11px;">الرصيد الافتتاحي</th>
                <th style="background-color: #ecfdf5 !important; color: #047857 !important; width: 25%; padding: 5px; font-size: 11px;">إجمالي الوارد (+)</th>
                <th style="background-color: #fef2f2 !important; color: #dc2626 !important; width: 25%; padding: 5px; font-size: 11px;">إجمالي الصادر (-)</th>
                <th style="background-color: #eff6ff !important; color: #1d4ed8 !important; width: 25%; padding: 5px; font-size: 11px;">الرصيد الحالي الصافي</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="font-weight: 800; font-size: 13px; padding: 6px;">{{ $fmt($treasury->opening_balance) }} <small>{{ $treasury->currency }}</small></td>
                <td class="text-success" style="font-weight: 800; font-size: 13px; padding: 6px; color: #047857 !important;">{{ $fmt($stats['total_in']) }} <small>{{ $treasury->currency }}</small></td>
                <td class="text-danger" style="font-weight: 800; font-size: 13px; padding: 6px; color: #dc2626 !important;">{{ $fmt($stats['total_out']) }} <small>{{ $treasury->currency }}</small></td>
                <td class="text-primary" style="font-weight: 800; font-size: 13px; padding: 6px; color: #1d4ed8 !important;">{{ $fmt($treasury->current_balance) }} <small>{{ $treasury->currency }}</small></td>
            </tr>
        </tbody>
    </table>

    {{-- Transactions Table --}}
    <table class="table table-bordered align-middle mb-0" style="width: 100%; border-collapse: collapse; font-size: 11.5px;">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">#</th>
                <th style="width: 15%; text-align: center;">التاريخ</th>
                <th style="width: 13%; text-align: center;">نوع الحركة</th>
                <th style="width: 20%; text-align: center;">المبلغ</th>
                <th style="width: 32%; text-align: right;">البيان / الوصف</th>
                <th style="width: 14%; text-align: center;">المسجل بواسطة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentTransactions as $index => $transaction)
            <tr>
                <td style="text-align: center; font-weight: 600;">{{ $index + 1 }}</td>
                <td style="text-align: center; font-family: monospace;">{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                <td style="text-align: center; font-weight: 700;">
                    @if($transaction->type === 'in')
                        <span style="color: #047857 !important; font-weight: 700;">وارد (إيداع)</span>
                    @else
                        <span style="color: #dc2626 !important; font-weight: 700;">صادر (سحب)</span>
                    @endif
                </td>
                <td style="text-align: center; font-weight: 800; font-family: monospace;">{{ $fmt($transaction->amount) }} {{ $treasury->currency }}</td>
                <td style="text-align: right;">{{ $transaction->description }}</td>
                <td style="text-align: center;">{{ $transaction->createdBy->name ?? 'النظام' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; padding: 14px;">لا توجد حركات مالية مسجلة</td>
            </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" style="text-align: right; font-weight: 800; background-color: #f1f5f9 !important;">إجمالي الحركات ({{ $stats['total_transactions'] }} حركة) — الرصيد الصافي النهائي:</td>
                <td style="text-align: center; font-weight: 800; font-family: monospace; background-color: #f1f5f9 !important; color: #047857 !important;">{{ $fmt($treasury->current_balance) }} {{ $treasury->currency }}</td>
                <td colspan="2" style="text-align: center; font-size: 10.5px; background-color: #f1f5f9 !important;">
                    وارد: {{ $fmt($stats['total_in']) }} | صادر: {{ $fmt($stats['total_out']) }}
                </td>
            </tr>
        </tfoot>
    </table>
</div>

{{-- Modal: Direct Manual Adjustment of Total In, Total Out, and Current Balance --}}
<div class="modal fade no-print" id="adjustTreasuryTotalsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('treasuries.adjustTotals', $treasury) }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-sliders text-primary me-2"></i>تعديل الوارد والصادر والرصيد يدوياً
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3">
                    <label class="form-label fw-bold text-success">إجمالي الوارد (+)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" name="total_in" id="adjustTotalInInput" class="form-control fw-bold" value="{{ (float) $stats['total_in'] }}" required>
                        <span class="input-group-text text-success fw-bold">{{ $treasury->currency }}</span>
                    </div>
                    <small class="text-muted">اكتب القيمة الجديدة لإجمالي الوارد (أو 0 لتصفير الوارد)</small>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold text-danger">إجمالي الصادر (-)</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0" name="total_out" id="adjustTotalOutInput" class="form-control fw-bold" value="{{ (float) $stats['total_out'] }}" required>
                        <span class="input-group-text text-danger fw-bold">{{ $treasury->currency }}</span>
                    </div>
                    <small class="text-muted">اكتب القيمة الجديدة لإجمالي الصادر (أو 0 لتصفير الصادر)</small>
                </div>

                <div class="mb-2">
                    <label class="form-label fw-bold text-primary">الرصيد الحالي الصافي</label>
                    <div class="input-group">
                        <input type="number" step="0.01" name="current_balance" id="adjustCurrentBalanceInput" class="form-control fw-bold" value="{{ (float) $treasury->current_balance }}" required>
                        <span class="input-group-text text-primary fw-bold">{{ $treasury->currency }}</span>
                    </div>
                    <small class="text-muted">يمكنك تحديد الرصيد الحالي يدوياً أو الضغط على زر الحساب التلقائي أدناه</small>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill" onclick="zeroAllTreasuryTotals()">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> تصفير الكل (0.00)
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" onclick="calcNetBalanceFromInAndOut()">
                        <i class="bi bi-calculator me-1"></i> حساب الرصيد الصافي = (الوارد - الصادر)
                    </button>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal: Edit Individual Treasury Transaction --}}
<div class="modal fade no-print" id="editTreasuryTransactionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form id="editTreasuryTransactionForm" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold">
                    <i class="bi bi-pencil-square text-primary me-2"></i>تعديل الحركة المالية
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="mb-3">
                    <label class="form-label fw-bold">نوع الحركة</label>
                    <select name="type" id="editTxType" class="form-select" required>
                        <option value="in">وارد (إيداع)</option>
                        <option value="out">صادر (سحب)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">المبلغ</label>
                    <div class="input-group">
                        <input type="number" step="0.01" min="0.01" name="amount" id="editTxAmount" class="form-control fw-bold" required>
                        <span class="input-group-text">{{ $treasury->currency }}</span>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">التاريخ</label>
                    <input type="date" name="transaction_date" id="editTxDate" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">الوصف / البيان</label>
                    <textarea name="description" id="editTxDescription" class="form-control" rows="2" required></textarea>
                </div>
                <div class="form-check">
                    <input type="hidden" name="recalculate_balance" value="0">
                    <input class="form-check-input" type="checkbox" name="recalculate_balance" id="editTxRecalc" value="1" checked>
                    <label class="form-check-label small" for="editTxRecalc">
                        تحديث الرصيد الحالي للخزينة تلقائياً بفرق التعديل
                    </label>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">
                    <i class="bi bi-check-lg me-1"></i> حفظ تعديل الحركة
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function openAdjustTotalsModal(focusField) {
        var modalEl = document.getElementById('adjustTreasuryTotalsModal');
        if (!modalEl || typeof bootstrap === 'undefined') return;
        var modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        modal.show();
        setTimeout(function () {
            var targetId = focusField === 'in'
                ? 'adjustTotalInInput'
                : (focusField === 'out' ? 'adjustTotalOutInput' : 'adjustCurrentBalanceInput');
            var input = document.getElementById(targetId);
            if (input) {
                input.focus();
                input.select();
            }
        }, 250);
    }

    function calcNetBalanceFromInAndOut() {
        var inVal = parseFloat(document.getElementById('adjustTotalInInput')?.value || '0') || 0;
        var outVal = parseFloat(document.getElementById('adjustTotalOutInput')?.value || '0') || 0;
        var balInput = document.getElementById('adjustCurrentBalanceInput');
        if (balInput) {
            balInput.value = (inVal - outVal).toFixed(2);
        }
    }

    function zeroAllTreasuryTotals() {
        var inInput = document.getElementById('adjustTotalInInput');
        var outInput = document.getElementById('adjustTotalOutInput');
        var balInput = document.getElementById('adjustCurrentBalanceInput');
        if (inInput) inInput.value = '0';
        if (outInput) outInput.value = '0';
        if (balInput) balInput.value = '0';
    }

    function openEditTransactionModalFromBtn(btn) {
        if (!btn) return;
        openEditTransactionModal({
            id: btn.getAttribute('data-tx-id'),
            url: btn.getAttribute('data-tx-url'),
            type: btn.getAttribute('data-tx-type'),
            amount: btn.getAttribute('data-tx-amount'),
            description: btn.getAttribute('data-tx-description'),
            transaction_date: btn.getAttribute('data-tx-date'),
        });
    }

    function openEditTransactionModal(tx) {
        var form = document.getElementById('editTreasuryTransactionForm');
        var modalEl = document.getElementById('editTreasuryTransactionModal');
        if (!form || !modalEl || typeof bootstrap === 'undefined') return;
        form.action = tx.url;
        document.getElementById('editTxType').value = tx.type || 'in';
        document.getElementById('editTxAmount').value = tx.amount || '';
        document.getElementById('editTxDate').value = tx.transaction_date || '';
        document.getElementById('editTxDescription').value = tx.description || '';
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    }

    function previewTreasuryStatement() {
        if (window.EnsanPrintPreview) {
            window.EnsanPrintPreview.open('#printableTreasuryStatement', {
                title: 'كشف حساب الخزينة — {{ $treasury->name }} ({{ $treasury->code }})'
            });
        } else {
            window.print();
        }
    }

    function printTreasuryStatement() {
        if (window.EnsanPrintPreview) {
            window.EnsanPrintPreview.open('#printableTreasuryStatement', {
                title: 'كشف حساب الخزينة — {{ $treasury->name }} ({{ $treasury->code }})'
            });
            setTimeout(function () {
                window.EnsanPrintPreview.executePrint();
            }, 250);
        } else {
            window.print();
        }
    }

    function onSourceTreasuryChange(select) {
        if (!select) return;
        var opt = select.options[select.selectedIndex];
        var curr = opt ? opt.getAttribute('data-currency') : 'EGP';
        var label = document.getElementById('txCurrencyLabel');
        if (label && curr) {
            label.textContent = curr;
        }

        var srcId = select.value;
        var destSelect = document.getElementById('outDestTreasurySelect');
        if (destSelect) {
            for (var i = 0; i < destSelect.options.length; i++) {
                var o = destSelect.options[i];
                var dId = o.getAttribute('data-id');
                if (dId) {
                    if (dId === srcId) {
                        o.disabled = true;
                        o.classList.add('d-none');
                        if (destSelect.value === srcId) {
                            destSelect.value = '';
                        }
                    } else {
                        o.disabled = false;
                        o.classList.remove('d-none');
                    }
                }
            }
        }

        updateTxSyncNotice();
    }

    function updateTxSyncNotice() {
        var noticeEl = document.getElementById('txSyncNotice');
        var noticeText = document.getElementById('txSyncNoticeText');
        var amountInput = document.getElementById('txAmountInput');
        var outSourceSel = document.getElementById('outSourceTreasurySelect');
        var outDestSel = document.getElementById('outDestTreasurySelect');
        if (!noticeEl || !noticeText) return;

        var amount = amountInput && amountInput.value ? parseFloat(amountInput.value).toLocaleString() : 'المبلغ المكتوب';
        var srcName = outSourceSel && outSourceSel.selectedIndex >= 0 ? outSourceSel.options[outSourceSel.selectedIndex].getAttribute('data-name') : 'الخزينة';
        var destVal = outDestSel ? outDestSel.value : '';

        if (destVal) {
            var destName = outDestSel.options[outDestSel.selectedIndex].getAttribute('data-name');
            noticeEl.className = 'alert alert-warning py-2 px-3 rounded-3 x-small mb-3 d-flex align-items-center gap-2 border border-warning';
            noticeText.innerHTML = `<strong>⚡ خصم وتوريد مزدوج:</strong> سيتم خصم (${amount}) من <strong>${srcName}</strong> وتوريدها وإيداعها مباشرة في <strong>${destName}</strong> وتحديث رصيد الخزنتين فوراً.`;
        } else {
            noticeEl.className = 'alert alert-danger py-2 px-3 rounded-3 x-small mb-3 d-flex align-items-center gap-2 border border-danger';
            noticeText.innerHTML = `<strong>🔴 صرف صادر مباشر:</strong> سيتم خصم (${amount}) من <strong>${srcName}</strong> وتخفيض رصيدها فوراً (مصروفات / مساعدات / أخرى).`;
        }
    }
</script>
@endsection


