@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 50%, #0369a1 100%);">
    <div class="hero-content">
        <div class="hero-greeting">الموارد البشرية ⏰</div>
        <h1 class="hero-title">سجل حضور الموظفين</h1>
        <p class="hero-subtitle">تسجيل ومتابعة حضور وانصراف الموظفين</p>
        <div class="hero-actions d-flex gap-2 flex-wrap">

            <a href="{{ route('employee-attendance.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> تسجيل يدوي
            </a>
        </div>
    </div>
    <i class="bi bi-calendar-check-fill hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3 px-4">
             <h5 class="mb-0 fw-bold text-primary">السجل ({{ $records->total() }})</h5>
             <div class="d-none animate-fade-in" id="bulk-actions">
                 <button class="btn btn-sm btn-outline-danger d-flex align-items-center gap-2" onclick="submitBulk('destroy')">
                     <i class="bi bi-trash"></i> حذف المحدد
                 </button>
             </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-secondary small text-uppercase">
                            <th class="py-3 px-4" style="width: 40px;">
                                <input type="checkbox" class="form-check-input" id="check-all" onchange="toggleAll(this)">
                            </th>
                            <th class="py-3 px-4">الموظف</th>
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
                                    <input type="checkbox" class="form-check-input bulk-item" value="{{ $r->id }}" onchange="updateBulkUI()">
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px; font-weight: bold;">
                                            {{ strtoupper(substr($r->user?->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="fw-medium">{{ $r->user?->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4 text-muted">{{ $r->date->format('Y-m-d') }}</td>
                                <td class="px-4"><span
                                        class="badge bg-success-subtle text-success rounded-pill px-3">{{ $r->check_in_at ?? '—' }}</span>
                                </td>
                                <td class="px-4"><span
                                        class="badge bg-danger-subtle text-danger rounded-pill px-3">{{ $r->check_out_at ?? '—' }}</span>
                                </td>
                                <td class="px-4 text-muted small">{{ Str::limit($r->notes, 30) ?? '—' }}</td>
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
                                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || $r->user_id == auth()->id()))
                                            <form action="{{ route('employee-attendance.destroy', ['employee_attendance' => $r->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من طلب حذف هذا السجل؟');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger start-radius" title="حذف">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @endif

                                        {{-- Edit Action --}}
                                        @if(auth()->check() && (auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager') || $r->user_id == auth()->id()))
                                            <a href="{{ route('employee-attendance.edit', ['employee_attendance' => $r->id]) }}" class="btn btn-outline-primary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @endif

                                        {{-- View Action --}}
                                        <a href="{{ route('employee-attendance.show', ['employee_attendance' => $r->id]) }}" class="btn btn-outline-secondary end-radius" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
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

    <script>
    function toggleAll(source) {
        document.querySelectorAll('.bulk-item').forEach(checkbox => {
            checkbox.checked = source.checked;
        });
        updateBulkUI();
    }

    function updateBulkUI() {
        const count = document.querySelectorAll('.bulk-item:checked').length;
        const toolbar = document.getElementById('bulk-actions');
        if (count > 0) {
            toolbar.classList.remove('d-none');
        } else {
            toolbar.classList.add('d-none');
        }
    }

    function submitBulk(action) {
        const ids = Array.from(document.querySelectorAll('.bulk-item:checked')).map(cb => cb.value);
        if (ids.length === 0) return;

        if (!confirm(`هل أنت متأكد من ${action === 'destroy' ? 'حذف' : 'معالجة'} ${ids.length} عنصر محدد؟`)) return;

        const form = document.getElementById('bulk-destroy-form');
        
        // Clear previous inputs
        form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());

        // Add new inputs
        ids.forEach(id => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'ids[]';
            input.value = id;
            form.appendChild(input);
        });

        form.submit();
    }
    </script>
    <form id="bulk-destroy-form" action="{{ route('employee-attendance.bulk-destroy') }}" method="POST" style="display:none">
        @csrf
    </form>
@endsection