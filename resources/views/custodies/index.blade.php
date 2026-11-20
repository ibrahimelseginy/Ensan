@extends('layouts.app')

@section('title', 'العهد المالية للموظفين')

@section('content')
<div class="container-fluid py-4 px-lg-4" style="direction: rtl; text-align: right;">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5 align-middle"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h2 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                <i class="bi bi-wallet2 text-success"></i>
                العهد المالية للموظفين
            </h2>
            <p class="text-muted mb-0 small">متابعة تسليم وتسوية العهد المالية والفواتير المقدمة والمتبقي للرد</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('treasuries.index') }}" class="btn btn-outline-secondary rounded-pill px-3 py-2">
                <i class="bi bi-arrow-right me-1"></i> العودة للخزائن
            </a>
            <a href="{{ route('custodies.create') }}" class="btn btn-success rounded-pill px-4 py-2 d-flex align-items-center gap-2 shadow-sm">
                <i class="bi bi-plus-lg"></i>
                <span>صرف عهدة جديدة لموظف</span>
            </a>
        </div>
    </div>

    {{-- Stats Row --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">إجمالي العهد المنصرفة</div>
                <div class="fs-4 fw-bold text-dark font-monospace">{{ number_format($totalIssued, 2) }} <small class="fs-6">ج.م</small></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">إجمالي الفواتير والمصروفات المسواة</div>
                <div class="fs-4 fw-bold text-primary font-monospace">{{ number_format($totalSettled, 2) }} <small class="fs-6">ج.م</small></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">المتبقي للرد في العهد المفتوحة</div>
                <div class="fs-4 fw-bold text-danger font-monospace">{{ number_format($totalOpenAmount, 2) }} <small class="fs-6">ج.م</small></div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm rounded-4 p-3 bg-white">
                <div class="text-muted small fw-bold mb-1">العهد المفتوحة حالياً</div>
                <div class="fs-4 fw-bold text-warning text-dark font-monospace">{{ $openCustodiesCount }} <small class="fs-6">عهدة</small></div>
            </div>
        </div>
    </div>

    {{-- Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4 bg-white p-3">
        <form method="GET" action="{{ route('custodies.index') }}" class="row g-2 align-items-end">
            <div class="col-md-4">
                <label class="form-label small text-muted">تصفية بالموظف المستلم</label>
                <select name="holder_id" class="form-select rounded-3">
                    <option value="">-- جميع الموظفين --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('holder_id') == $emp->id ? 'selected' : '' }}>{{ $emp->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">تصفية بالمشروع المستفيد</label>
                <select name="project_id" class="form-select rounded-3">
                    <option value="">-- جميع المشاريع --</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">حالة العهدة</label>
                <select name="status" class="form-select rounded-3">
                    <option value="">-- جميع الحالات --</option>
                    @foreach(\App\Models\Finance\Custody::STATUSES as $code => $st)
                        <option value="{{ $code }}" {{ request('status') == $code ? 'selected' : '' }}>{{ $st['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-primary rounded-3 w-100">بحث</button>
                <a href="{{ route('custodies.index') }}" class="btn btn-outline-secondary rounded-3">إلغاء</a>
            </div>
        </form>
    </div>

    {{-- Custodies Table --}}
    <div class="card border-0 shadow-sm rounded-4 bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="text-muted small">
                            <th>رقم العهدة</th>
                            <th>الموظف المستلم</th>
                            <th>تاريخ الصرف</th>
                            <th>المشروع / الغرض</th>
                            <th>قيمة العهدة</th>
                            <th>الفواتير المقدمة</th>
                            <th>المتبقي للرد</th>
                            <th>الحالة</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($custodies as $c)
                            <tr>
                                <td class="font-monospace fw-bold text-dark">
                                    <a href="{{ route('custodies.show', $c) }}" class="text-decoration-none">
                                        {{ $c->custody_number }}
                                    </a>
                                </td>
                                <td class="fw-semibold">
                                    <i class="bi bi-person-circle text-muted me-1"></i>
                                    {{ $c->holder->name ?? '—' }}
                                </td>
                                <td class="font-monospace small">{{ $c->issued_on ? $c->issued_on->format('Y-m-d') : '—' }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $c->project->name ?? 'مصروف عام للمؤسسة' }}</div>
                                    <small class="text-muted">{{ Str::limit($c->purpose, 35) }}</small>
                                </td>
                                <td class="fw-bold font-monospace text-dark">{{ number_format($c->issued_amount, 2) }} ج.م</td>
                                <td class="font-monospace text-primary fw-bold">{{ number_format($c->settled_amount, 2) }} ج.م</td>
                                <td class="font-monospace text-danger fw-bold">{{ number_format($c->remaining_to_return, 2) }} ج.م</td>
                                <td>
                                    @php $st = $c->status_meta; @endphp
                                    <span class="badge {{ $st['class'] }} px-2.5 py-1">{{ $st['label'] }}</span>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="{{ route('custodies.show', $c) }}" class="btn btn-sm btn-outline-primary px-2.5 py-1" title="كشف العهدة والتسوية">
                                            <i class="bi bi-card-checklist me-1"></i> التسوية
                                        </a>
                                        <a href="{{ route('custodies.printVoucher', $c) }}" target="_blank" class="btn btn-sm btn-outline-secondary px-2 py-1" title="طباعة سند الصرف">
                                            <i class="bi bi-printer"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 opacity-25 d-block mb-2"></i>
                                    لا توجد عهد مالية مطابقة للبحث
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($custodies->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $custodies->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
