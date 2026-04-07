@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    {{-- Hero Section --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white shadow-lg" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h2 class="mb-2">
                                <i class="bi bi-safe me-2"></i>
                                لوحة تحكم الخزائن
                            </h2>
                            <p class="mb-0 opacity-8">إدارة شاملة لجميع الخزائن والحركات المالية</p>
                        </div>
                        <div class="col-md-4 text-end">
                            <a href="{{ route('treasuries.create') }}" class="btn btn-light btn-lg">
                                <i class="bi bi-plus-circle me-2"></i>
                                إضافة خزينة جديدة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-coin fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الرصيد</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_balance'], 2) }}</h3>
                            <small class="text-muted">ج.م</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-arrow-down-circle fa-2x text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">وارد اليوم</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_in_today'], 2) }}</h3>
                            <small class="text-muted">ج.م</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-arrow-up-circle fa-2x text-danger"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">صادر اليوم</h6>
                            <h3 class="mb-0">{{ number_format($stats['total_out_today'], 2) }}</h3>
                            <small class="text-muted">ج.م</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="bi bi-arrow-left-right fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">حركات اليوم</h6>
                            <h3 class="mb-0">{{ $stats['total_transactions_today'] }}</h3>
                            <small class="text-muted">حركة</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- AI Insights --}}
    @if(count($insights) > 0)
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-lightbulb me-2 text-warning"></i>
                        رؤى ذكية
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($insights as $insight)
                        <div class="col-md-6 mb-2">
                            <div class="alert alert-{{ $insight['type'] }} border-start border-5 border-{{ $insight['type'] }} mb-0">
                                <i class="bi bi-{{ $insight['icon'] }} me-2"></i>
                                {{ $insight['message'] }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Treasuries List --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="bi bi-safe me-2"></i>
                        الخزائن ({{ $stats['total_treasuries'] }})
                    </h5>
                    <a href="{{ route('treasuries.index') }}" class="btn btn-sm btn-outline-primary">
                        عرض الكل
                    </a>
                </div>
                <div class="card-body">
                    <div class="row">
                        @forelse($topTreasuries as $treasury)
                        <div class="col-md-4 mb-3">
                            <div class="card border">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="mb-0">{{ $treasury->name }}</h6>
                                            <small class="text-muted">{{ $treasury->code }}</small>
                                        </div>
                                        @if($treasury->is_active)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-secondary">غير نشط</span>
                                        @endif
                                    </div>
                                    <div class="text-center my-3">
                                        <h3 class="text-primary mb-0">{{ number_format($treasury->current_balance, 2) }}</h3>
                                        <small class="text-muted">{{ $treasury->currency }}</small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('treasuries.show', $treasury) }}" class="btn btn-sm btn-outline-primary flex-grow-1">
                                            <i class="bi bi-eye me-1"></i>
                                            عرض
                                        </a>
                                        @if(optional(auth()->user())->hasRole('admin') || optional(auth()->user())->hasRole('manager'))
                                        <a href="{{ route('treasuries.edit', $treasury) }}" class="btn btn-sm btn-outline-info">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <form action="{{ route('treasuries.destroy', $treasury) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخزينة؟ لا يمكن حذف الخزائن التي تحتوي على حركات مالية.')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <i class="bi bi-safe text-muted" style="font-size: 4rem;"></i>
                            <p class="text-muted mt-3">لا توجد خزائن بعد</p>
                            <a href="{{ route('treasuries.create') }}" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>
                                إضافة خزينة جديدة
                            </a>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Transactions --}}
    @if($recentTransactions->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white">
                    <h5 class="mb-0">
                        <i class="bi bi-clock-history me-2"></i>
                        آخر الحركات
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>التاريخ</th>
                                    <th>الخزينة</th>
                                    <th>النوع</th>
                                    <th>المبلغ</th>
                                    <th>الوصف</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->transaction_date->format('Y-m-d') }}</td>
                                    <td>{{ $transaction->treasury->name }}</td>
                                    <td>
                                        @if($transaction->type === 'in')
                                            <span class="badge bg-success">وارد</span>
                                        @else
                                            <span class="badge bg-danger">صادر</span>
                                        @endif
                                    </td>
                                    <td class="fw-bold">{{ number_format($transaction->amount, 2) }} {{ $transaction->currency }}</td>
                                    <td>{{ Str::limit($transaction->description, 50) }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection


