@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
    <div class="hero-content">
        <div class="hero-greeting">تفاصيل القيد 📒</div>
        <h1 class="hero-title">قيد يومية رقم #{{ $journalEntry->id }}</h1>
        <p class="hero-subtitle">{{ optional($journalEntry->date)->format('Y-m-d') }} | {{ $journalEntry->entry_type }}</p>
        <div class="hero-actions d-flex gap-2 align-items-center">
            @if(isset($pendingRequest))
                <div class="badge bg-white bg-opacity-25 text-white p-2 px-3 rounded-pill d-flex align-items-center gap-2 shadow-sm border border-white border-opacity-10 animate-pulse">
                    <i class="bi bi-hourglass-split"></i>
                    <span>طلب قيد المراجعة</span>
                </div>
            @endif
            <a href="{{ route('journal-entries.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light shadow-sm border-0 hover-lift">
                <i class="bi bi-arrow-right me-1"></i> عودة
            </a>
        </div>
    </div>
    <div class="hero-stat-item">
        <span class="d-block small opacity-75">إجمالي القيد</span>
        <span class="fw-bold fs-4">{{ number_format($journalEntry->lines->sum('debit'), 2) }}</span>
    </div>
</div>

<div class="row g-4 animate-slide-up animate-delay-1">
    <div class="col-lg-8">
        {{-- Entry Lines --}}
        <div class="chart-container h-100">
            <h5 class="fw-bold mb-4 section-title"><i class="bi bi-list-columns-reverse me-2"></i>أطراف القيد</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                        <tr>
                            <th>الحساب</th>
                            <th class="text-end">مدين</th>
                            <th class="text-end">دائن</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($journalEntry->lines as $line)
                        <tr>
                            <td>
                                <div class="fw-bold text-dark">{{ $line->account->name }}</div>
                                <div class="small text-muted font-monospace">{{ $line->account->code }}</div>
                            </td>
                            <td class="text-end font-monospace text-success">{{ number_format($line->debit, 2) }}</td>
                            <td class="text-end font-monospace text-danger">{{ number_format($line->credit, 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-dark">
                        <tr>
                            <th class="fw-bold px-4">الإجمالي</th>
                            <th class="text-end font-monospace text-success fw-bold px-4">{{ number_format($journalEntry->lines->sum('debit'), 2) }}</th>
                            <th class="text-end font-monospace text-danger fw-bold px-4">{{ number_format($journalEntry->lines->sum('credit'), 2) }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="mt-4 pt-4 border-top">
                <h6 class="fw-bold text-muted small mb-2 text-uppercase letter-spacing-1">الشرح / الوصف</h6>
                <div class="p-4 rounded-4 bg-light bg-opacity-50 border border-dashed border-primary border-opacity-25">
                    <p class="mb-0 text-dark opacity-75">{{ $journalEntry->description ?: 'لا يوجد وصف لهذا القيد' }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        {{-- Quick Actions --}}
        <div class="glass-card mb-4 p-4 border-0 shadow-sm" style="background: rgba(255,255,255,0.05); backdrop-filter: blur(15px); border: 1px solid rgba(255,255,255,0.1) !important;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="fw-bold mb-0 section-title text-white opacity-75"><i class="bi bi-lightning me-2"></i>إجراءات سريعة</h6>
                @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                    <a href="{{ route('change-requests.index') }}" class="btn btn-sm btn-link text-white text-decoration-none opacity-50 hover-opacity-100 p-0">
                        <i class="bi bi-shield-check fs-5"></i>
                    </a>
                @endif
            </div>

            <div class="d-grid gap-3">
                @if(isset($pendingRequest))
                    <div class="alert alert-warning mb-0 p-3 border-0 bg-warning bg-opacity-10 text-warning rounded-4 shadow-sm animate-pulse">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="bi bi-hourglass-split fs-5"></i>
                            <span class="fw-bold small">طلب قيد المراجعة</span>
                        </div>
                        <p class="x-small mb-3 opacity-75">هذا السجل لديه طلب تعديل حالياً، يرجى الانتظار للموافقة أو إلغاء الطلب.</p>
                        
                        <form action="{{ route('change-requests.cancel', $pendingRequest) }}" method="POST" class="mt-2" onsubmit="return confirm('هل تريد بالتأكيد إلغاء هذا الطلب؟')">
                            @csrf
                            <button class="btn btn-outline-danger w-100 rounded-pill shadow-sm py-2">
                                <i class="bi bi-x-circle me-1"></i> إلغاء الطلب
                            </button>
                        </form>
                    </div>
                @else
                    @if(!$journalEntry->locked)
                        <a href="{{ route('journal-entries.edit', $journalEntry) }}" class="btn btn-light text-primary rounded-pill shadow-sm py-2 hover-lift fw-bold">
                            <i class="bi bi-pencil me-1"></i> تعديل القيد
                        </a>
                        <form action="{{ route('journal-entries.destroy', $journalEntry) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-outline-light text-danger rounded-pill shadow-sm py-2 hover-lift w-100 mt-2">
                                <i class="bi bi-trash me-1"></i> حذف القيد
                            </button>
                        </form>
                    @else
                        <div class="alert alert-info mb-0 text-center fw-bold rounded-pill bg-info bg-opacity-10 border-info border-opacity-25 text-info">
                            <i class="bi bi-lock me-1"></i> القيد مرحل ولا يمكن تعديله
                        </div>
                    @endif
                @endif
            </div>
        </div>

        {{-- Entry Details Summary --}}
        <div class="summary-panel p-4 rounded-4 shadow-sm">
            <h5 class="fw-bold mb-4 section-title"><i class="bi bi-info-circle me-2"></i>تفاصيل النظام</h5>
            <div class="list-group list-group-flush small">
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                    <span class="text-muted">رقم المرجع</span>
                    <span class="fw-bold text-dark">#{{ $journalEntry->id }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                    <span class="text-muted">التاريخ</span>
                    <span class="fw-bold text-dark">{{ optional($journalEntry->date)->format('Y-m-d') }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2">
                    <span class="text-muted">نوع القيد</span>
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">{{ $journalEntry->entry_type }}</span>
                </div>
                <div class="list-group-item d-flex justify-content-between align-items-center bg-transparent px-0 py-2 border-0">
                    <span class="text-muted">حالة السجل</span>
                    @if($journalEntry->locked)
                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">مرحل</span>
                    @else
                        <span class="badge bg-warning bg-opacity-10 text-dark rounded-pill px-3">مسودة</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


