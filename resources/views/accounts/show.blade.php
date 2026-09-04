@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #134e4a 100%);">
    <div class="hero-content">
        <div class="hero-greeting">تفاصيل الحساب 📑</div>
        <h1 class="hero-title">{{ $account->name }}</h1>
        <p class="hero-subtitle">{{ $account->code }} | {{ $account->type_label ?? $account->type }}</p>
        <div class="hero-actions d-flex gap-2 align-items-center">
            @if(isset($pendingRequest))
                <div class="badge bg-warning text-dark p-2 px-3 rounded-pill d-flex align-items-center gap-2 shadow-sm border border-warning border-opacity-25 animate-pulse">
                    <i class="bi bi-hourglass-split"></i>
                    <span>يوجد طلب تعديل قيد المراجعة</span>
                </div>
                <form action="{{ route('change-requests.cancel', $pendingRequest) }}" method="POST" onsubmit="return confirm('هل تريد بالتأكيد إلغاء هذا الطلب؟')">
                    @csrf
                    <button class="btn btn-sm rounded-pill px-4 btn-danger shadow-sm border-0">
                        <i class="bi bi-x-circle me-1"></i> إلغاء الطلب
                    </button>
                </form>
            @else
                <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm rounded-pill px-4 btn-light text-primary shadow-sm border-0 hover-lift">
                    <i class="bi bi-pencil me-1"></i> تعديل
                </a>
            @endif
            
            <a href="{{ route('accounts.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light shadow-sm border-0 hover-lift">
                <i class="bi bi-arrow-right me-1"></i> عودة
            </a>
        </div>
    </div>
    <div class="hero-stat-item">
        <span class="d-block small opacity-75">الحساب الرئيسي</span>
        <span class="fw-bold">{{ optional($account->parent)->name ?? '—' }}</span>
    </div>
</div>

<div class="row g-4 animate-slide-up animate-delay-1">
    {{-- Account Details Card --}}
    <div class="col-md-4">
        <div class="summary-panel h-100">
            <h5 class="fw-bold mb-3 section-title"><i class="bi bi-info-circle me-2"></i>معلومات الحساب</h5>
            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                <span class="text-muted small">نوع الحساب</span>
                <span class="fw-bold">{{ $account->type }}</span>
            </div>
            <div class="d-flex justify-content-between mb-2 border-bottom pb-2">
                <span class="text-muted small">الكود</span>
                <span class="fw-bold font-monospace">{{ $account->code }}</span>
            </div>
            <div class="mb-2">
                <span class="text-muted small d-block mb-1">الوصف</span>
                <p class="small text-dark mb-0 bg-light p-2 rounded">{{ $account->description ?? 'لا يوجد وصف' }}</p>
            </div>
            
            @if($account->children->count() > 0)
                <h6 class="fw-bold mt-4 mb-3 section-title small"><i class="bi bi-diagram-3 me-2"></i>الحسابات الفرعية</h6>
                <div class="list-group list-group-flush small">
                    @foreach($account->children as $child)
                        <a href="{{ route('accounts.show', $child) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center px-0">
                            <span>{{ $child->name }}</span>
                            <span class="badge bg-light text-dark border">{{ $child->code }}</span>
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    {{-- Account Ledger --}}
    <div class="col-md-8">
        <div class="chart-container h-100">
            <h5 class="fw-bold mb-4 section-title"><i class="bi bi-journal-bookmark me-2"></i>حركة الحساب (Ledger)</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 small">
                    <thead class="bg-light">
                        <tr>
                            <th>التاريخ</th>
                            <th>القيد</th>
                            <th>البيان</th>
                            <th class="text-end">مدين</th>
                            <th class="text-end">دائن</th>
                            <th class="text-end">الرصيد</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $balance = 0; @endphp
                        @foreach($lines->reverse() as $line)
                        @php 
                            if(in_array($account->type, ['asset','expense'])) {
                                $balance += $line->debit - $line->credit;
                            } else {
                                $balance += $line->credit - $line->debit;
                            }
                        @endphp
                        <tr>
                            <td>{{ optional($line->journalEntry)->date->format('Y-m-d') }}</td>
                            <td><a href="{{ route('journal-entries.show', $line->journal_entry_id) }}" class="text-decoration-none font-monospace">#{{ $line->journal_entry_id }}</a></td>
                            <td class="text-truncate" style="max-width: 200px;">{{ optional($line->journalEntry)->description }}</td>
                            <td class="text-end text-success">{{ number_format($line->debit, 2) }}</td>
                            <td class="text-end text-danger">{{ number_format($line->credit, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($balance, 2) }}</td>
                        </tr>
                        @endforeach
                        @if($lines->isEmpty())
                        <tr><td colspan="6" class="text-center py-4 text-muted">لا توجد حركات مسجلة لهذا الحساب</td></tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <div class="mt-3">
                {{ $lines->links() }}
            </div>
        </div>
    </div>
</div>
@endsection


