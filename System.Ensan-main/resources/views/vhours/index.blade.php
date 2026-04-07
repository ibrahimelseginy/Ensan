@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #06b6d4 0%, #0891b2 50%, #0e7490 100%);">
    <div class="hero-content">
        <div class="hero-greeting">التطوع ⏱️</div>
        <h1 class="hero-title">ساعات التطوع</h1>
        <p class="hero-subtitle">تسجيل ومتابعة ساعات عمل المتطوعين</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('volunteer-hours.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> تسجيل ساعات
            </a>
        </div>
    </div>
    <i class="bi bi-clock-history hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr class="text-secondary small text-uppercase">
                            <th class="py-3 px-4">المتطوع</th>
                            <th class="py-3 px-4">التاريخ</th>
                            <th class="py-3 px-4">عدد الساعات</th>
                            <th class="py-3 px-4">المهمة / النشاط</th>
                            <th class="py-3 px-4 text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($hours as $h)
                            <tr>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials bg-success-subtle text-success rounded-circle me-2 d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px; font-weight: bold;">
                                            {{ strtoupper(substr($h->user?->name ?? 'V', 0, 1)) }}
                                        </div>
                                        <span class="fw-medium">{{ $h->user?->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 text-muted">{{ $h->date->format('Y-m-d') }}</td>
                                <td class="px-4">
                                    <span
                                        class="badge bg-primary-subtle text-primary rounded-pill px-3 fs-6">{{ $h->hours }}</span>
                                </td>
                                <td class="px-4 text-muted">{{ $h->task ?? '—' }}</td>
                                <td class="px-4 text-end">
                                    <div class="btn-group">
                                        <a class="btn btn-sm btn-outline-primary" href="{{ route('volunteer-hours.show', $h) }}"
                                            title="عرض"><i class="bi bi-eye"></i></a>
                                        <a class="btn btn-sm btn-outline-secondary"
                                            href="{{ route('volunteer-hours.edit', $h) }}" title="تعديل"><i
                                                class="bi bi-pencil"></i></a>
                                        <form class="d-inline" method="POST" action="{{ route('volunteer-hours.destroy', $h) }}"
                                            onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-sm btn-outline-danger" title="حذف"><i
                                                    class="bi bi-trash"></i></button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-clock-history display-4 mb-3 d-block opacity-50"></i>
                                    لا توجد سجلات ساعات تطوع حالياً
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($hours->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $hours->links() }}
            </div>
        @endif
    </div>

@endsection

