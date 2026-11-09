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
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="bi bi-safe me-2"></i>{{ $treasury->name }}</h2>
            <p class="text-muted">{{ $treasury->code }}</p>
        </div>
        <div class="col-md-4 text-end">
            @php $currentUser = request()->user(); @endphp
            @if($currentUser && ($currentUser->hasRole('admin') || $currentUser->hasRole('manager')))
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
    @endphp
    <div class="row g-3 mb-4 animate-slide-up">
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                <button type="button" class="btn btn-sm btn-link text-primary position-absolute top-0 start-0 m-2 p-1" title="تعديل الرصيد الحالي يدوياً" onclick="openAdjustTotalsModal('balance')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <h6 class="text-muted mb-1 small">إجمالي الرصيد الحالي</h6>
                <h3 class="text-primary mb-1 fw-bold font-monospace">{{ number_format($treasury->current_balance, 2) }}</h3>
                <small class="text-muted d-block mb-2">{{ $treasury->currency }}</small>
                <div class="d-flex justify-content-center gap-1 flex-wrap border-top pt-2">
                    <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle font-monospace" title="صافي رصيد العهدة">عهدة: {{ number_format($txBreakdown['custody_balance'], 2) }}</span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle font-monospace" title="صافي رصيد التبرعات">تبرعات: {{ number_format($txBreakdown['donations_balance'], 2) }}</span>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                <h6 class="text-muted mb-1 small">تفصيل الخزينة (العهدة والتبرعات)</h6>
                <div class="d-flex justify-content-around align-items-center my-2">
                    <div>
                        <div class="small text-info fw-bold"><i class="bi bi-wallet2 me-1"></i>العهدة</div>
                        <div class="fw-bold font-monospace fs-5 text-dark">{{ number_format($txBreakdown['custody_balance'], 2) }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">وارد: {{ number_format($txBreakdown['custody_in'], 0) }}</small>
                    </div>
                    <div class="vr"></div>
                    <div>
                        <div class="small text-success fw-bold"><i class="bi bi-heart-fill me-1"></i>التبرعات</div>
                        <div class="fw-bold font-monospace fs-5 text-dark">{{ number_format($txBreakdown['donations_balance'], 2) }}</div>
                        <small class="text-muted" style="font-size: 0.72rem;">وارد: {{ number_format($txBreakdown['donations_in'], 0) }}</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                <button type="button" class="btn btn-sm btn-link text-success position-absolute top-0 start-0 m-2 p-1" title="تعديل إجمالي الوارد يدوياً" onclick="openAdjustTotalsModal('in')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <h6 class="text-muted mb-2 small">إجمالي الوارد</h6>
                <h3 class="text-success mb-0 fw-bold font-monospace">{{ number_format($stats['total_in'], 2) }}</h3>
                <small class="text-muted">{{ $treasury->currency }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center position-relative h-100">
                <button type="button" class="btn btn-sm btn-link text-danger position-absolute top-0 start-0 m-2 p-1" title="تعديل إجمالي الصادر يدوياً" onclick="openAdjustTotalsModal('out')">
                    <i class="bi bi-pencil-square"></i>
                </button>
                <h6 class="text-muted mb-2 small">إجمالي الصادر</h6>
                <h3 class="text-danger mb-0 fw-bold font-monospace">{{ number_format($stats['total_out'], 2) }}</h3>
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
                                <th>المبلغ</th>
                                <th>الوصف</th>
                                <th class="text-center" style="width: 110px;">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentTransactions as $transaction)
                            <tr>
                                <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                <td>
                                    @if($transaction->type === 'in')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">وارد</span>
                                    @else
                                        <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">صادر</span>
                                    @endif
                                </td>
                                <td class="fw-bold font-monospace">{{ number_format($transaction->amount, 2) }} {{ $treasury->currency }}</td>
                                <td class="text-muted">{{ $transaction->description }}</td>
                                <td class="text-center">
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
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-5">
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
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Quick Actions --}}
            <div class="glass-card mb-4 p-4 border-0 shadow-sm" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1) !important;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0 section-title text-white opacity-75"><i class="bi bi-lightning me-2"></i>إجراءات سريعة</h6>
                    @if($currentUser && ($currentUser->hasRole('admin') || $currentUser->hasRole('manager')))
                        <a href="{{ route('change-requests.index') }}" class="btn btn-sm btn-link text-white text-decoration-none opacity-50 hover-opacity-100 p-0">
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

                    @if(isset($pendingRequest))
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

            {{-- Add Transaction --}}
            <div class="summary-panel mb-4">
                <h5 class="fw-bold mb-3 section-title small text-uppercase letter-spacing-1"><i class="bi bi-plus-circle me-2"></i>إضافة حركة مالية</h5>
                <form action="{{ route('treasuries.addTransaction', $treasury) }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <select name="type" class="form-select form-select-sm rounded-3" required>
                            <option value="in">وارد (إيداع)</option>
                            <option value="out">صادر (سحب)</option>
                        </select>
                    </div>
                    <div class="input-group input-group-sm mb-3">
                        <input type="number" name="amount" class="form-control rounded-start-3" placeholder="المبلغ" step="0.01" min="0.01" required>
                        <span class="input-group-text bg-light border-start-0">{{ $treasury->currency }}</span>
                    </div>
                    <div class="mb-3">
                        <textarea name="description" class="form-control form-control-sm rounded-3" placeholder="وصف الحركة..." rows="2" required></textarea>
                    </div>
                    <div class="mb-3">
                        <input type="date" name="transaction_date" class="form-control form-control-sm rounded-3" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm w-100 rounded-pill shadow-sm">
                        إضافة الحركة بنجاح
                    </button>
                </form>
            </div>
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
                <td style="font-weight: 800; font-size: 13px; padding: 6px;">{{ number_format($treasury->opening_balance, 2) }} <small>{{ $treasury->currency }}</small></td>
                <td class="text-success" style="font-weight: 800; font-size: 13px; padding: 6px; color: #047857 !important;">{{ number_format($stats['total_in'], 2) }} <small>{{ $treasury->currency }}</small></td>
                <td class="text-danger" style="font-weight: 800; font-size: 13px; padding: 6px; color: #dc2626 !important;">{{ number_format($stats['total_out'], 2) }} <small>{{ $treasury->currency }}</small></td>
                <td class="text-primary" style="font-weight: 800; font-size: 13px; padding: 6px; color: #1d4ed8 !important;">{{ number_format($treasury->current_balance, 2) }} <small>{{ $treasury->currency }}</small></td>
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
                <td style="text-align: center; font-weight: 800; font-family: monospace;">{{ number_format($transaction->amount, 2) }} {{ $treasury->currency }}</td>
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
                <td style="text-align: center; font-weight: 800; font-family: monospace; background-color: #f1f5f9 !important; color: #047857 !important;">{{ number_format($treasury->current_balance, 2) }} {{ $treasury->currency }}</td>
                <td colspan="2" style="text-align: center; font-size: 10.5px; background-color: #f1f5f9 !important;">
                    وارد: {{ number_format($stats['total_in'], 2) }} | صادر: {{ number_format($stats['total_out'], 2) }}
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
</script>
@endsection


