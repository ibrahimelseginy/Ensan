@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">سجل ومصروفات الاستقبال</h1>
            <p class="text-muted small mb-0">إدارة الزوار والمكالمات الواردة، وعهدة ومصروفات خزينة الاستقبال</p>
        </div>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-outline-success fw-bold" data-bs-toggle="modal" data-bs-target="#newReceptionDepositModal">
                <i class="bi bi-wallet2 me-1"></i> تغذية عهدة / تبرع بالاستقبال
            </button>
            <button type="button" class="btn btn-danger fw-bold" data-bs-toggle="modal" data-bs-target="#newReceptionExpenseModal">
                <i class="bi bi-cash-Stack me-1"></i> تسجيل مصروف استقبال
            </button>
            <button type="button" class="btn btn-primary fw-bold" data-bs-toggle="modal" data-bs-target="#newLogModal">
                <i class="bi bi-plus-lg me-1"></i> تسجيل زائر / مكالمة
            </button>
        </div>
    </div>

    @if(isset($primaryTreasury) && $primaryTreasury)
        @php
            $recBreakdown = $primaryTreasury->balance_breakdown;
        @endphp
        <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
            <div class="card-body p-3 p-md-4 bg-white">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                    <div class="d-flex align-items-center gap-2">
                        <span class="p-2 rounded-3 bg-primary-subtle text-primary"><i class="bi bi-safe2-fill fs-5"></i></span>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark">{{ $primaryTreasury->name }} <span class="badge bg-light text-secondary border font-monospace ms-1">{{ $primaryTreasury->code }}</span></h6>
                            <small class="text-muted">الخزينة المخصصة للاستقبال فقط (عهدة الموظف + التبرعات الواردة بالاستقبال)</small>
                        </div>
                    </div>
                    <a href="{{ route('treasuries.show', $primaryTreasury) }}" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="bi bi-eye me-1"></i> كشف حساب خزينة الاستقبال
                    </a>
                </div>
                <div class="row g-3 text-center">
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 bg-light border h-100">
                            <div class="text-muted small mb-1">إجمالي رصيد خزينة الاستقبال</div>
                            <div class="fs-4 fw-bold text-primary font-monospace">{{ number_format($primaryTreasury->current_balance, 2) }} <small class="fs-6">ج.م</small></div>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 border border-info-subtle h-100" style="background: rgba(14, 165, 233, 0.06);">
                            <div class="text-info-emphasis small fw-bold mb-1"><i class="bi bi-wallet2 me-1"></i>فلوس العهدة بالخزينة</div>
                            <div class="fs-4 fw-bold text-dark font-monospace">{{ number_format($recBreakdown['custody_balance'], 2) }} <small class="fs-6">ج.م</small></div>
                            <small class="text-muted">إجمالي وارد العهدة: {{ number_format($recBreakdown['custody_in'], 0) }} ج.م</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 border border-success-subtle h-100" style="background: rgba(16, 185, 129, 0.06);">
                            <div class="text-success small fw-bold mb-1"><i class="bi bi-heart-fill me-1"></i>فلوس التبرعات بالخزينة</div>
                            <div class="fs-4 fw-bold text-dark font-monospace">{{ number_format($recBreakdown['donations_balance'], 2) }} <small class="fs-6">ج.م</small></div>
                            <small class="text-muted">إجمالي وارد التبرعات: {{ number_format($recBreakdown['donations_in'], 0) }} ج.م</small>
                        </div>
                    </div>
                    <div class="col-md-3 col-6">
                        <div class="p-3 rounded-3 border border-danger-subtle h-100" style="background: rgba(239, 68, 68, 0.06);">
                            <div class="text-danger small fw-bold mb-1"><i class="bi bi-receipt me-1"></i>مصروفات الاستقبال (الشهر)</div>
                            <div class="fs-4 fw-bold text-danger font-monospace">{{ number_format($todayStats['month_expenses'] ?? 0, 2) }} <small class="fs-6">ج.م</small></div>
                            <small class="text-muted">مصروفات اليوم: {{ number_format($todayStats['today_expenses'] ?? 0, 2) }} ج.م</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Stats -->
    <div class="row g-4 mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-primary bg-opacity-10 text-primary p-3 rounded-4 me-3">
                        <i class="bi bi-people fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">إجمالي اليوم</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['total'] }}</div>
                    </div>
                </div>
                <div class="bg-primary py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-success bg-opacity-10 text-success p-3 rounded-4 me-3">
                        <i class="bi bi-person-walking fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">زيارات شخصية</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['personal'] }}</div>
                    </div>
                </div>
                <div class="bg-success py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-info bg-opacity-10 text-info p-3 rounded-4 me-3">
                        <i class="bi bi-telephone fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">مكالمات هاتفية</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['phone'] }}</div>
                    </div>
                </div>
                <div class="bg-info py-1 opacity-25"></div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm h-100 overflow-hidden">
                <div class="d-flex align-items-center p-3">
                    <div class="bg-warning bg-opacity-10 text-warning p-3 rounded-4 me-3">
                        <i class="bi bi-check-circle fs-3"></i>
                    </div>
                    <div>
                        <div class="text-muted small fw-bold">تم الإنجاز</div>
                        <div class="h4 fw-bold mb-0 text-dark">{{ $todayStats['completed'] }}</div>
                    </div>
                </div>
                <div class="bg-warning py-1 opacity-25"></div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">سجل العمليات الأخير</h6>
        </div>
        <div class="card-body">
            @if($logs->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>النوع</th>
                                <th>الغرض</th>
                                <th>الحالة</th>
                                <th>محول إلى</th>
                                <th>التوقيت</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($logs as $log)
                                <tr>
                                    <td>{{ $log->id }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $log->visitor_name }}</div>
                                        <span class="text-muted small">{{ $log->phone }}</span>
                                    </td>
                                    <td>
                                        @if($log->visit_type == 'personal')
                                            <span class="badge bg-soft-primary text-primary"><i class="bi bi-person"></i> شخصي</span>
                                        @else
                                            <span class="badge bg-soft-info text-info"><i class="bi bi-telephone"></i> هاتف</span>
                                        @endif
                                    </td>
                                    <td>{{ $log->purpose_label }}</td>
                                    <td>
                                        <span class="badge bg-{{ $log->status_color }}">{{ $log->status_label }}</span>
                                    </td>
                                    <td>{{ $log->directed_to ?? '-' }}</td>
                                    <td>{{ optional($log->created_at)->format('H:i') }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-light text-primary" title="تعديل"><i class="bi bi-pencil"></i></button>
                                        <button class="btn btn-sm btn-light text-success" title="إكمال"><i class="bi bi-check"></i></button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $logs->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <img src="https://cdni.iconscout.com/illustration/premium/thumb/empty-state-2130362-1800926.png" alt="No Data" style="max-width: 200px; opacity: 0.5">
                    <p class="mt-3 text-muted">لا توجد سجلات استقبال حتى الآن</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Reception Expenses Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between bg-white">
            <div>
                <h6 class="m-0 fw-bold text-danger"><i class="bi bi-receipt-cutoff me-1"></i> سجل مصروفات الاستقبال</h6>
                <small class="text-muted">جميع المصروفات النثرية والتشغيلية المسجلة على خزينة الاستقبال</small>
            </div>
            <button type="button" class="btn btn-sm btn-danger rounded-pill px-3 fw-bold" data-bs-toggle="modal" data-bs-target="#newReceptionExpenseModal">
                <i class="bi bi-plus-circle me-1"></i> إضافة مصروف استقبال
            </button>
        </div>
        <div class="card-body">
            @if(isset($receptionExpenses) && $receptionExpenses->isNotEmpty())
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>التاريخ</th>
                                <th>البيان / تفاصيل المصروف</th>
                                <th>الخزينة</th>
                                <th>المبلغ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($receptionExpenses as $exp)
                                <tr>
                                    <td class="font-monospace">{{ $exp->id }}</td>
                                    <td class="font-monospace">{{ optional($exp->paid_at)->format('Y-m-d') ?? optional($exp->created_at)->format('Y-m-d') }}</td>
                                    <td class="fw-bold text-dark">
                                        {{ $exp->description }}
                                        @if(!empty($exp->attachment_path))
                                            <a href="{{ asset('storage/' . $exp->attachment_path) }}" target="_blank" class="badge bg-info-subtle text-info border border-info-subtle text-decoration-none ms-1">
                                                <i class="bi bi-paperclip"></i> المرفق
                                            </a>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $exp->treasury?->name ?? 'خزينة الاستقبال' }}</span></td>
                                    <td class="fw-bold text-danger font-monospace">{{ number_format((float)$exp->amount, 2) }} ج.م</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-4 text-muted">
                    <i class="bi bi-cash-coin fs-2 d-block mb-2 opacity-50"></i>
                    لا توجد مصروفات مسجلة للاستقبال حتى الآن.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- New Reception Expense Modal -->
<div class="modal fade" id="newReceptionExpenseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('reception.expenses.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-danger text-white border-0">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-receipt me-1"></i> تسجيل مصروف استقبال جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">الخزينة (مرتبطة بالاستقبال فقط)</label>
                    <select name="treasury_id" class="form-select bg-light" required>
                        @foreach($receptionTreasuries ?? [] as $tr)
                            <option value="{{ $tr->id }}">{{ $tr->name }} (الرصيد: {{ number_format($tr->current_balance, 2) }} ج.م)</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">بند المصروف <span class="text-danger">*</span></label>
                        <select name="expense_item" class="form-select" required>
                            <option value="ضيافة واستقبال">☕ ضيافة واستقبال</option>
                            <option value="مطبوعات وأدوات مكتبية">🖨️ مطبوعات وأدوات مكتبية</option>
                            <option value="انتقالات ومشاوير">🚕 انتقالات ومشاوير</option>
                            <option value="نظافة ومستلزمات">🧹 نظافة ومستلزمات</option>
                            <option value="صيانة سريعة">🔧 صيانة سريعة</option>
                            <option value="مصروفات نثرية أخرى">📋 مصروفات نثرية أخرى</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المبلغ (ج.م) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-monospace" required placeholder="0.00">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">تاريخ الصرف</label>
                    <input type="date" name="spent_at" class="form-control font-monospace" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">تفاصيل / ملاحظات المصروف</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="اكتب تفاصيل المصروف..."></textarea>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">صورة الفاتورة أو الإيصال (اختياري)</label>
                    <input type="file" name="attachment" class="form-control" accept="image/*,.pdf">
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger rounded-pill px-4 fw-bold">حفظ المصروف وخصمه من الخزينة</button>
            </div>
        </form>
    </div>
</div>

<!-- New Reception Custody / Donation Deposit Modal -->
<div class="modal fade" id="newReceptionDepositModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('reception.custody.deposit') }}" method="POST" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header bg-success text-white border-0">
                <h5 class="modal-title fw-bold text-white"><i class="bi bi-wallet2 me-1"></i> تغذية عهدة أو تسجيل تبرع بخزينة الاستقبال</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold">الخزينة</label>
                    <select name="treasury_id" class="form-select bg-light" required>
                        @foreach($receptionTreasuries ?? [] as $tr)
                            <option value="{{ $tr->id }}">{{ $tr->name }} (الرصيد: {{ number_format($tr->current_balance, 2) }} ج.م)</option>
                        @endforeach
                    </select>
                </div>
                <div class="row g-3 mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">نوع المبلغ الوارد <span class="text-danger">*</span></label>
                        <select name="deposit_type" class="form-select" required>
                            <option value="custody">💼 عهدة موظف / تغذية خزينة الاستقبال</option>
                            <option value="donation">❤️ تبرع نقدي مستلم بالاستقبال</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">المبلغ (ج.م) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" min="0.01" name="amount" class="form-control font-monospace" required placeholder="0.00">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">التاريخ</label>
                    <input type="date" name="transaction_date" class="form-control font-monospace" value="{{ date('Y-m-d') }}" required>
                </div>
                <div class="mb-2">
                    <label class="form-label fw-bold">البيان / اسم الموظف أو المتبرع</label>
                    <input type="text" name="description" class="form-control" placeholder="مثال: عهدة موظف الاستقبال أ/ ... أو تبرع نقدي من ...">
                </div>
            </div>
            <div class="modal-footer bg-light border-0">
                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success rounded-pill px-4 fw-bold">إيداع في خزينة الاستقبال</button>
            </div>
        </form>
    </div>
</div>

<!-- New Log Modal -->
<div class="modal fade" id="newLogModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تسجيل عملية جديدة</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reception.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">اسم الزائر / المتصل</label>
                        <input type="text" name="visitor_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">رقم الهاتف</label>
                        <input type="text" name="phone" class="form-control">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">نوع العملية</label>
                            <select name="visit_type" class="form-select" required>
                                <option value="personal">زيارة شخصية</option>
                                <option value="phone">مكالمة هاتفية</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">الغرض</label>
                            <select name="purpose" class="form-select" required>
                                <option value="help_request">طلب مساعدة</option>
                                <option value="complaint">شكوى</option>
                                <option value="donation">تبرع</option>
                                <option value="inquiry">استفسار عام</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تحويل إلى (اختياري)</label>
                        <input type="text" name="directed_to" class="form-control" placeholder="اسم الموظف أو القسم">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">حفظ</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

