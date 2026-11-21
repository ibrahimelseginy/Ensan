@extends('layouts.app')

@section('title', 'كشف العهدة المالية - ' . $custody->custody_number)

@section('content')
<div class="container py-4" style="direction: rtl; text-align: right; max-width: 960px;">

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

    {{-- Top Action Bar --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <h3 class="fw-bold mb-0">كشف العهد المالية</h3>
                <span class="badge bg-light text-muted border font-monospace">{{ $custody->custody_number }}</span>
            </div>
            <p class="text-muted small mb-0">سجل حركة العهدة ومتابعة الفواتير والمصروفات المقدمة وإجراءات التسوية</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('custodies.index') }}" class="btn btn-outline-secondary rounded-pill px-3">
                <i class="bi bi-arrow-right me-1"></i> العودة
            </a>
            <a href="{{ route('custodies.printVoucher', $custody) }}" target="_blank" class="btn btn-outline-dark rounded-pill px-3">
                <i class="bi bi-printer me-1"></i> طباعة كشف العهدة
            </a>
            @if($custody->status !== \App\Models\Finance\Custody::STATUS_CLOSED)
                <button type="button" class="btn btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="bi bi-plus-lg me-1"></i> إضافة فاتورة / مصروف
                </button>
                <button type="button" class="btn btn-success rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#settleModal">
                    <i class="bi bi-check2-circle me-1"></i> اعتماد وإغلاق التسوية
                </button>
            @endif
        </div>
    </div>

    {{-- Main Custody Card (Exact Wireframe match) --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4 mb-4">
        {{-- Header row --}}
        <div class="d-flex justify-content-between align-items-center pb-3 border-bottom mb-4">
            <div>
                <h5 class="fw-bold mb-1 text-dark">
                    <i class="bi bi-person-badge text-primary me-2"></i>
                    الموظف: {{ $custody->holder->name ?? 'غير محدد' }}
                </h5>
                <small class="text-muted">
                    تاريخ الصرف: {{ $custody->issued_on ? $custody->issued_on->format('Y-m-d') : '—' }}
                    @if($custody->due_on)
                        • موعد الاستحقاق: {{ $custody->due_on->format('Y-m-d') }}
                    @endif
                </small>
            </div>
            <div>
                @php $st = $custody->status_meta; @endphp
                <span class="badge {{ $st['class'] }} px-3 py-2 fs-6 rounded-pill">
                    {{ $st['label'] }}
                </span>
            </div>
        </div>

        {{-- 4 Big Stat Metrics (Exact match to image) --}}
        <div class="row g-3 mb-4 text-center">
            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small fw-semibold mb-1">قيمة العهدة</div>
                    <div class="fs-4 fw-bold text-dark font-monospace">{{ number_format($custody->issued_amount, 2) }}</div>
                    <small class="text-muted">ج.م</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small fw-semibold mb-1">الفواتير المقدمة</div>
                    <div class="fs-4 fw-bold text-primary font-monospace">{{ number_format($custody->settled_amount, 2) }}</div>
                    <small class="text-muted">ج.م</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small fw-semibold mb-1">المشروع المستفيد</div>
                    <div class="fs-5 fw-bold text-success text-truncate">{{ $custody->project->name ?? 'مصروف عام للمؤسسة' }}</div>
                    <small class="text-muted">النشاط المرتبط</small>
                </div>
            </div>

            <div class="col-md-3 col-sm-6">
                <div class="p-3 bg-light rounded-3">
                    <div class="text-muted small fw-semibold mb-1">المتبقي للرد</div>
                    <div class="fs-4 fw-bold {{ $custody->remaining_to_return > 0 ? 'text-danger' : 'text-success' }} font-monospace">
                        {{ number_format($custody->remaining_to_return, 2) }}
                    </div>
                    <small class="text-muted">ج.م</small>
                </div>
            </div>
        </div>

        {{-- الغرض من العهدة --}}
        <div class="p-3 bg-light bg-opacity-50 rounded-3 mb-4">
            <div class="fw-bold small text-muted mb-1"><i class="bi bi-info-circle me-1"></i>الغرض والبيان:</div>
            <p class="mb-0 text-dark">{{ $custody->purpose }}</p>
        </div>

        {{-- حالة التسوية (Stepped Milestones - Exact match to image) --}}
        <div class="pt-3 border-top">
            <h6 class="fw-bold mb-3 text-dark">حالة التسوية</h6>
            <div class="row g-3">
                {{-- Milestone 1: Review Invoices --}}
                <div class="col-md-4">
                    <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center {{ $custody->expenses->isNotEmpty() ? 'bg-success bg-opacity-10 border-success' : 'bg-light' }}">
                        <div>
                            <div class="fw-bold small">مراجعة الفواتير</div>
                            <small class="text-muted">{{ $custody->expenses->count() }} فواتير مقدمة</small>
                        </div>
                        @if($custody->expenses->isNotEmpty())
                            <span class="badge bg-success rounded-pill px-3 py-1.5">مكتملة</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-2.5 py-1">قيد الانتظار</span>
                        @endif
                    </div>
                </div>

                {{-- Milestone 2: Return Remainder --}}
                <div class="col-md-4">
                    <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center {{ $custody->returned_amount > 0 || $custody->remaining_to_return == 0 ? 'bg-success bg-opacity-10 border-success' : ($custody->remaining_to_return > 0 ? 'bg-warning bg-opacity-10 border-warning' : 'bg-light') }}">
                        <div>
                            <div class="fw-bold small">رد المبلغ المتبقي</div>
                            <small class="text-muted">
                                @if($custody->returned_amount > 0)
                                    تم رد: {{ number_format($custody->returned_amount, 2) }} ج.م
                                @else
                                    متبقي: {{ number_format($custody->remaining_to_return, 2) }} ج.م
                                @endif
                            </small>
                        </div>
                        @if($custody->returned_amount > 0 || $custody->remaining_to_return == 0)
                            <span class="badge bg-success rounded-pill px-3 py-1.5">تم الرد</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1.5">بانتظار الرد</span>
                        @endif
                    </div>
                </div>

                {{-- Milestone 3: Close Custody --}}
                <div class="col-md-4">
                    <div class="p-3 rounded-3 border d-flex justify-content-between align-items-center {{ $custody->status === \App\Models\Finance\Custody::STATUS_CLOSED ? 'bg-success bg-opacity-10 border-success' : 'bg-light' }}">
                        <div>
                            <div class="fw-bold small">إغلاق العهدة</div>
                            <small class="text-muted">
                                @if($custody->status === \App\Models\Finance\Custody::STATUS_CLOSED)
                                    أغلقت بواسطة: {{ $custody->closer->name ?? 'المحاسب' }}
                                @else
                                    التسوية النهائية
                                @endif
                            </small>
                        </div>
                        @if($custody->status === \App\Models\Finance\Custody::STATUS_CLOSED)
                            <span class="badge bg-success rounded-pill px-3 py-1.5">مكتملة ومغلقة</span>
                        @else
                            <span class="badge bg-secondary rounded-pill px-2.5 py-1">لم يتم</span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Invoices Table Card --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0">الفواتير والمصروفات المقدمة</h5>
            @if($custody->status !== \App\Models\Finance\Custody::STATUS_CLOSED)
                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                    <i class="bi bi-plus-lg me-1"></i> إضافة فاتورة جديدة
                </button>
            @endif
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr class="text-muted small">
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>رقم الفاتورة</th>
                        <th>الجهة / التاجر</th>
                        <th>المشروع المحمل عليه</th>
                        <th>المبلغ</th>
                        <th>البيان</th>
                        <th>الحالة</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($custody->expenses as $idx => $exp)
                        <tr>
                            <td class="text-muted small">{{ $idx + 1 }}</td>
                            <td class="font-monospace small">{{ $exp->expense_date ? $exp->expense_date->format('Y-m-d') : '—' }}</td>
                            <td class="font-monospace fw-semibold">{{ $exp->invoice_number ?? '—' }}</td>
                            <td>{{ $exp->merchant_name ?? '—' }}</td>
                            <td class="small">{{ $exp->project->name ?? 'عام للمؤسسة' }}</td>
                            <td class="fw-bold font-monospace text-primary">{{ number_format($exp->amount, 2) }} ج.م</td>
                            <td class="small text-muted">{{ $exp->description }}</td>
                            <td>
                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1">معتمدة</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                لم يتم تسجيل أي فواتير لهذه العهدة حتى الآن.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="table-light">
                    <tr class="fw-bold">
                        <td colspan="5" class="text-end">إجمالي المصروفات المقدمة:</td>
                        <td class="text-primary font-monospace fs-6">{{ number_format($custody->settled_amount, 2) }} ج.م</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>

{{-- MODAL 1: إضافة فاتورة / مصروف للعهدة --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" action="{{ route('custodies.addExpense', $custody) }}">
                @csrf
                <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold" id="addExpenseModalLabel">
                        <i class="bi bi-receipt text-primary me-2"></i>
                        تسجيل فاتورة / مصروف على العهدة
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">تاريخ الفاتورة <span class="text-danger">*</span></label>
                            <input type="date" name="expense_date" value="{{ date('Y-m-d') }}" class="form-control rounded-3" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">قيمة الفاتورة (ج.م) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0.5" name="amount" class="form-control rounded-3 font-monospace" placeholder="0.00" required>
                        </div>
                    </div>

                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-bold small">رقم الفاتورة / الإيصال</label>
                            <input type="text" name="invoice_number" class="form-control rounded-3" placeholder="اختياري">
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-bold small">اسم التاجر / الجهة</label>
                            <input type="text" name="merchant_name" class="form-control rounded-3" placeholder="اختياري">
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">المشروع المحمل عليه المصروف</label>
                        <select name="project_id" class="form-select rounded-3">
                            <option value="">-- افتراضي: نفس مشروع العهدة --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $custody->project_id == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">بيان ووصف المصروف <span class="text-danger">*</span></label>
                        <textarea name="description" class="form-control rounded-3" rows="2" placeholder="وصف المصروف والأصناف المشتراة..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ الفاتورة</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL 2: اعتماد وإغلاق تسوية العهدة --}}
<div class="modal fade" id="settleModal" tabindex="-1" aria-labelledby="settleModalLabel" aria-hidden="true" style="direction: rtl; text-align: right;">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form method="POST" action="{{ route('custodies.settle', $custody) }}">
                @csrf
                <div class="modal-header bg-light rounded-top-4 px-4 py-3">
                    <h5 class="modal-title fw-bold" id="settleModalLabel">
                        <i class="bi bi-shield-check text-success me-2"></i>
                        اعتماد وتسوية العهدة المالية
                    </h5>
                    <button type="button" class="btn-close m-0" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="p-3 bg-light rounded-3 mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>قيمة العهدة المنصرفة:</span>
                            <span class="fw-bold font-monospace">{{ number_format($custody->issued_amount, 2) }} ج.م</span>
                        </div>
                        <div class="d-flex justify-content-between small mb-1">
                            <span>إجمالي الفواتير المعتمدة:</span>
                            <span class="fw-bold font-monospace text-primary">{{ number_format($custody->settled_amount, 2) }} ج.م</span>
                        </div>
                        <div class="d-flex justify-content-between fw-bold pt-2 border-top">
                            <span>الفارق المالي:</span>
                            @if($custody->issued_amount > $custody->settled_amount)
                                <span class="text-danger font-monospace">متبقي مطلوب رده: {{ number_format($custody->remaining_to_return, 2) }} ج.م</span>
                            @elseif($custody->issued_amount < $custody->settled_amount)
                                <span class="text-success font-monospace">مستحقات للموظف: {{ number_format($custody->employee_dues, 2) }} ج.م</span>
                            @else
                                <span class="text-success font-monospace">متطابق تماماً (0.00 ج.م)</span>
                            @endif
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">إجراء التسوية المطلوب <span class="text-danger">*</span></label>
                        <select name="action" class="form-select rounded-3" required>
                            @if($custody->issued_amount > $custody->settled_amount)
                                <option value="return_remainder" selected>الحالة الأولى: رد المبلغ المتبقي ({{ number_format($custody->remaining_to_return, 2) }} ج.م) إلى الخزينة وإغلاق العهدة</option>
                            @elseif($custody->issued_amount < $custody->settled_amount)
                                <option value="pay_difference" selected>الحالة الثالثة: صرف فرق المصروفات الزائدة ({{ number_format($custody->employee_dues, 2) }} ج.م) للموظف وإغلاق العهدة</option>
                            @else
                                <option value="close_exact" selected>الحالة الثانية: الفواتير مطابقة تماماً للعهدة — إغلاق وتسوية نهائية</option>
                            @endif
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold small">الخزينة المرتبطة برد المتبقي أو صرف الفرق <span class="text-danger">*</span></label>
                        <select name="treasury_id" class="form-select rounded-3" required>
                            @foreach($treasuries as $t)
                                <option value="{{ $t->id }}">{{ $t->name }} (الرصيد: {{ number_format($t->current_balance, 2) }} ج.م)</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label fw-bold small">ملاحظات اعتماد التسوية</label>
                        <textarea name="notes" class="form-control rounded-3" rows="2" placeholder="أي ملاحظات حول اعتماد الفواتير وتسوية العهدة..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light rounded-bottom-4 px-4 py-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success rounded-pill px-4">تأكيد التسوية والإغلاق</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

