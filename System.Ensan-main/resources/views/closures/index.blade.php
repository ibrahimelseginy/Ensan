@extends('layouts.app')
@section('content')
{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #4f46e5 0%, #4338ca 50%, #3730a3 100%);">
    <div class="hero-content">
        <div class="hero-greeting">المالية 🔐</div>
        <h1 class="hero-title">الإغلاقات المالية</h1>
        <p class="hero-subtitle">سجل الإغلاقات اليومية للفروع والمندوبين لضمان دقة الحسابات</p>
        <div class="hero-actions d-flex gap-2 align-items-center">
            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                <a href="{{ route('change-requests.index') }}" class="btn btn-sm rounded-pill px-4 btn-outline-light border-white border-opacity-25" title="طلبات المراجعة">
                    <i class="bi bi-shield-check me-1"></i> طلبات المراجعة
                </a>
            @endif
            <a href="{{ route('closures.create') }}" class="btn btn-sm rounded-pill px-4 bg-white text-indigo shadow-lg fw-bold border-0 hover-lift">
                <i class="bi bi-plus-lg me-1"></i> إغلاق جديد
            </a>
        </div>
    </div>
    <i class="bi bi-lock hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1 mb-4">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0 align-middle">
          <thead class="bg-light bg-opacity-50">
            <tr>
              <th class="px-4 py-3">التاريخ</th>
              <th class="px-4 py-3">الفرع</th>
              <th class="px-4 py-3">الإجمالي النقدي</th>
              <th class="px-4 py-3">الحالة</th>
              <th class="px-4 py-3 text-end">إجراءات</th>
            </tr>
          </thead>
          <tbody>
            @forelse($closures as $c)
            @php
                $pending = $pendingRequests->get($c->id)?->first();
            @endphp
            <tr>
                <td class="px-4">
                    <div class="fw-bold text-dark">{{ optional($c->date)->format('Y-m-d') }}</div>
                    <small class="text-muted x-small">ID: #{{ $c->id }}</small>
                </td>
                <td class="px-4 text-nowrap">
                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill fw-normal px-3 py-2">
                        <i class="bi bi-building me-1"></i> {{ $c->branch ?: 'الرئيسي' }}
                    </span>
                </td>
                <td class="px-4">
                    <div class="fw-bold text-success font-monospace">{{ number_format($c->total_stats['total'] ?? 0, 2) }}</div>
                    <div class="small text-muted opacity-75 x-small">
                        <span>مكتب: {{ number_format($c->total_stats['office'] ?? 0, 2) }}</span>
                        <span class="mx-1">|</span>
                        <span>مندوب: {{ number_format($c->total_stats['delegate'] ?? 0, 2) }}</span>
                    </div>
                </td>
                <td class="px-4">
                    @if(isset($pending))
                        <div class="badge bg-warning bg-opacity-10 text-warning rounded-pill fw-bold px-3 py-2 animate-pulse border border-warning border-opacity-25">
                            <i class="bi bi-hourglass-split me-1"></i> قيد المراجعة
                        </div>
                    @elseif($c->approved)
                        <div class="badge bg-success bg-opacity-10 text-success rounded-pill fw-bold px-3 py-2 border border-success border-opacity-25">
                            <i class="bi bi-check-circle-fill me-1"></i> معتمد
                        </div>
                    @else
                        <div class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill fw-bold px-3 py-2 border border-secondary border-opacity-25">
                            <i class="bi bi-clock me-1"></i> مسودة
                        </div>
                    @endif
                </td>
                <td class="px-4 text-end">
                    <div class="d-flex justify-content-end gap-2">
                        @if(isset($pending))
                            <form action="{{ route('change-requests.cancel', $pending) }}" method="POST" onsubmit="return confirm('هل تريد إلغاء طلب المراجعة الحالي؟')">
                                @csrf
                                <button class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-none">
                                    <i class="bi bi-x-circle me-1"></i> إلغاء طلب المراجعة
                                </button>
                            </form>
                        @else
                            <div class="btn-group shadow-sm rounded-pill overflow-hidden border">
                                <a href="{{ route('closures.show', $c) }}" class="btn btn-sm btn-white text-primary border-0 px-3" title="عرض التفاصيل">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('closures.edit', $c) }}" class="btn btn-sm btn-white text-secondary border-0 px-3 border-start" title="تعديل">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form method="POST" action="{{ route('closures.destroy', $c) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الإغلاق؟ سيتم إرسال طلب للحذف.')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-white text-danger border-0 px-3 border-start" title="حذف">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                            
                            @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager')))
                                <form method="POST" action="{{ route('closures.approve', $c) }}" class="d-inline">
                                    @csrf
                                    <button class="btn btn-sm {{ $c->approved ? 'btn-outline-warning' : 'btn-success' }} rounded-pill px-3 ms-1 shadow-sm" 
                                        title="{{ $c->approved ? 'إلغاء الاعتماد' : 'اعتماد' }}">
                                        <i class="bi {{ $c->approved ? 'bi-lock-fill' : 'bi-unlock-fill' }} me-1"></i>
                                        {{ $c->approved ? 'إلغاء الاعتماد' : 'اعتماد' }}
                                    </button>
                                </form>
                            @endif
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="text-center py-5 text-muted bg-light bg-opacity-25 rounded-4">
                    <div class="mb-3 text-primary opacity-25">
                        <i class="bi bi-safe2 display-1 d-block mb-3"></i>
                    </div>
                    <div class="h5 fw-bold text-dark">لا توجد إغلاقات مالية مسجلة</div>
                    <p class="small opacity-50 mb-0">ابدأ بإضافة إغلاق مالي جديد لترتيب حسابات اليوم</p>
                </td>
            </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
  </div>
  <div class="mt-4 d-flex justify-content-center">{{ $closures->links() }}</div>

@endsection

