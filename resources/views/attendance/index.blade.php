@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        {{-- Premium Dashboard Hero --}}
        <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);">
            <div class="hero-content">
                <div class="hero-greeting">الموارد البشرية 📋</div>
                <h1 class="hero-title">سجل حضور المتطوعين</h1>
                <p class="hero-subtitle">إدارة ومتابعة سجلات الحضور والانصراف للمتطوعين</p>
                <div class="hero-actions d-flex gap-2">
                    <a href="{{ route('volunteer-attendance.create') }}" class="btn btn-sm rounded-pill px-4">
                        <i class="bi bi-plus-lg me-1"></i> تسجيل حضور
                    </a>
                </div>
            </div>
            <i class="bi bi-calendar-check-fill hero-icon d-none d-md-block"></i>
        </div>

        <div class="chart-container animate-slide-up animate-delay-1">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr class="text-secondary small text-uppercase">
                                <th class="py-3 px-4">المتطوع</th>
                                <th class="py-3 px-4">التاريخ</th>
                                <th class="py-3 px-4">وقت الدخول</th>
                                <th class="py-3 px-4">وقت الخروج</th>
                                <th class="py-3 px-4">ملاحظات</th>
                                <th class="py-3 px-4">التقييم</th>
                                <th class="py-3 px-4 text-end">إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $r)
                                <tr>
                                    <td class="px-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-initials bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                                style="width: 35px; height: 35px; font-weight: bold;">
                                                {{ strtoupper(substr($r->user?->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <span class="fw-medium">{{ $r->user?->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 text-secondary">{{ optional($r->date)->format('Y-m-d') }}</td>
                                    <td class="px-4">
                                        @if($r->check_in_at)
                                            <span class="badge bg-success-subtle text-success rounded-pill fw-normal px-3">
                                                {{ \Carbon\Carbon::parse($r->check_in_at)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4">
                                        @if($r->check_out_at)
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill fw-normal px-3">
                                                {{ \Carbon\Carbon::parse($r->check_out_at)->format('H:i') }}
                                            </span>
                                        @else
                                            <span class="text-muted small">—</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-secondary small">{{ Str::limit($r->notes ?? '—', 30) }}</td>
                                    <td class="px-4">
                                        @if($r->rating)
                                            <span class="text-warning" title="{{ $r->evaluation_notes }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="bi bi-star{{ $i <= $r->rating ? '-fill' : '' }}"></i>
                                                @endfor
                                            </span>
                                        @else
                                            <span class="text-muted small">غير مقيم</span>
                                        @endif
                                    </td>
                                    <td class="px-4 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        {{-- Delete Action --}}
                                        <form action="{{ route('volunteer-attendance.destroy', $r) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-outline-danger start-radius" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>

                                        {{-- Edit Action --}}
                                        <a href="{{ route('volunteer-attendance.edit', $r) }}" class="btn btn-outline-primary" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        {{-- View Action --}}
                                        <a href="{{ route('volunteer-attendance.show', $r) }}" class="btn btn-outline-secondary end-radius" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-calendar-x display-4 mb-3 d-block opacity-50"></i>
                                        لا توجد سجلات حضور حالياً
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            @if($records->hasPages())
                <div class="card-footer bg-transparent border-0 py-3">
                    {{ $records->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection

