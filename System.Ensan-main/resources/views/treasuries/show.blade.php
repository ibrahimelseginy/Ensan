@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistics --}}
    <div class="row mb-4 animate-slide-up">
        <div class="col-md-3">
            <div class="summary-panel text-center">
                <h6 class="text-muted mb-2 small">الرصيد الحالي</h6>
                <h3 class="text-primary mb-0 fw-bold">{{ number_format($treasury->current_balance, 2) }}</h3>
                <small class="text-muted">{{ $treasury->currency }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center">
                <h6 class="text-muted mb-2 small">إجمالي الوارد</h6>
                <h3 class="text-success mb-0 fw-bold">{{ number_format($stats['total_in'], 2) }}</h3>
                <small class="text-muted">{{ $treasury->currency }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center">
                <h6 class="text-muted mb-2 small">إجمالي الصادر</h6>
                <h3 class="text-danger mb-0 fw-bold">{{ number_format($stats['total_out'], 2) }}</h3>
                <small class="text-muted">{{ $treasury->currency }}</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-panel text-center">
                <h6 class="text-muted mb-2 small">عدد الحركات</h6>
                <h3 class="text-info mb-0 fw-bold">{{ $stats['total_transactions'] }}</h3>
                <small class="text-muted">حركة</small>
            </div>
        </div>
    </div>

    <div class="row g-4 animate-slide-up">
        <div class="col-lg-8">
            {{-- Recent Transactions --}}
            <div class="chart-container mb-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold mb-0 section-title"><i class="bi bi-clock-history me-2"></i>آخر الحركات</h5>
                    <button class="btn btn-sm btn-outline-primary rounded-pill px-3" onclick="window.print()">
                        <i class="bi bi-printer me-1"></i> طباعة كشف حساب
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 small">
                        <thead class="bg-light">
                            <tr>
                                <th>التاريخ</th>
                                <th>النوع</th>
                                <th>المبلغ</th>
                                <th>الوصف</th>
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
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted py-5">
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
@endsection
