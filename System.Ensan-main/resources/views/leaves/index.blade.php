@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #a855f7 0%, #9333ea 50%, #7e22ce 100%);">
    <div class="hero-content">
        <div class="hero-greeting">الموارد البشرية 🌴</div>
        <h1 class="hero-title">الإجازات</h1>
        <p class="hero-subtitle">طلبات إجازات الموظفين وإدارتها</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('leaves.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> طلب إجازة
            </a>
        </div>
    </div>
    <i class="bi bi-calendar2-week-fill hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1">

        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3 px-4">
             <h5 class="mb-0 fw-bold text-primary">السجل ({{ $leaves->total() }})</h5>
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
                            <th class="py-3 px-4">نوع الإجازة</th>
                            <th class="py-3 px-4">التاريخ</th>
                            <th class="py-3 px-4">المدة</th>
                            <th class="py-3 px-4">الحالة</th>
                            <th class="py-3 px-4">بواسطة</th>
                            <th class="py-3 px-4 text-end">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($leaves as $leave)
                            <tr>
                                <td class="px-4">
                                    <input type="checkbox" class="form-check-input bulk-item" value="{{ $leave->id }}" onchange="updateBulkUI()">
                                </td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initials bg-primary-subtle text-primary rounded-circle me-2 d-flex align-items-center justify-content-center"
                                            style="width: 35px; height: 35px; font-weight: bold;">
                                            {{ strtoupper(substr($leave->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <span class="fw-medium">{{ $leave->user->name }}</span>
                                    </div>
                                </td>
                                <td class="px-4">
                                    @php
                                        $types = [
                                            'annual' => 'سنوية',
                                            'sick' => 'مرضية',
                                            'unpaid' => 'بدون راتب',
                                            'emergency' => 'عارضة',
                                            'other' => 'أخرى'
                                        ];
                                    @endphp
                                    {{ $types[$leave->type] ?? $leave->type }}
                                </td>
                                <td class="px-4">
                                    <div>{{ $leave->start_date->format('Y-m-d') }}</div>
                                    <div class="small text-muted">إلى {{ $leave->end_date->format('Y-m-d') }}</div>
                                </td>
                                <td class="px-4">{{ $leave->days }} يوم</td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center gap-2">
                                        @if($leave->status == 'approved')
                                            <span class="badge bg-success-subtle text-success">مقبول</span>
                                        @elseif($leave->status == 'rejected')
                                            <span class="badge bg-danger-subtle text-danger" title="{{ $leave->rejection_reason }}" data-bs-toggle="tooltip">
                                                <i class="bi bi-x-circle me-1"></i> مرفوض
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning">معلق</span>
                                        @endif

                                        @if($leave->changeRequests->isNotEmpty())
                                            @php
                                                $req = $leave->changeRequests->first();
                                                $reqType = $req->action == 'delete' ? 'إلغاء' : 'تعديل';
                                                $reqColor = $req->action == 'delete' ? 'danger' : 'info';
                                            @endphp
                                            <span class="badge bg-{{ $reqColor }}-subtle text-{{ $reqColor }} border border-{{ $reqColor }}">
                                                <i class="bi bi-exclamation-circle me-1"></i> بانتظار {{ $reqType }}
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-4 text-muted small">
                                    {{ $leave->approver->name ?? '—' }}
                                </td>
                                <td class="px-4 text-end">
                                    <div class="btn-group btn-group-sm" role="group">
                                        @php
                                            $hasPendingRequest = $leave->changeRequests->isNotEmpty();
                                        @endphp

                                        {{-- Delete Action --}}
                                        @if((request()->user()->hasPermission('leaves.manage') || ($leave->user_id == request()->user()->id && $leave->status == 'pending')) && !$hasPendingRequest)
                                            <form action="{{ route('leaves.destroy', ['leave' => $leave->id]) }}" method="POST" class="d-inline" onsubmit="return confirm('{{ request()->user()->hasPermission('leaves.manage') ? 'هل أنت متأكد من حذف هذا الطلب؟' : 'هل أنت متأكد من طلب إلغاء هذا الطلب؟' }}');">
                                                @csrf @method('DELETE')
                                                <button class="btn btn-outline-danger start-radius" title="{{ request()->user()->hasPermission('leaves.manage') ? 'حذف' : 'طلب إلغاء' }}">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        @elseif($hasPendingRequest)
                                             <button class="btn btn-outline-secondary start-radius" disabled title="يوجد طلب معلق">
                                                <i class="bi bi-hourglass-split"></i>
                                            </button>
                                        @endif

                                        {{-- Edit Action --}}
                                        @if((request()->user()->hasPermission('leaves.manage') || ($leave->user_id == request()->user()->id && $leave->status == 'pending')) && !$hasPendingRequest)
                                            <a href="{{ route('leaves.edit', ['leave' => $leave->id]) }}" class="btn btn-outline-primary" title="تعديل">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                        @elseif($hasPendingRequest)
                                             <button class="btn btn-outline-secondary" disabled>
                                                <i class="bi bi-pencil-slash"></i>
                                            </button>
                                        @endif

                                        {{-- View Action --}}
                                        <a href="{{ route('leaves.show', ['leave' => $leave->id]) }}" class="btn btn-outline-secondary end-radius" title="عرض">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x display-4 mb-3 d-block opacity-50"></i>
                                    لا توجد إجازات مسجلة
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($leaves->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $leaves->links() }}
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

        function promptReject(url) {
            const reason = prompt('سبب الرفض:');
            if (reason !== null) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';
                form.innerHTML = `
                        @csrf @method('PUT')
                        <input type="hidden" name="status" value="rejected">
                        <input type="hidden" name="rejection_reason" value="${reason}">
                    `;
                document.body.appendChild(form);
                form.submit();
            }
        }
    </script>
    <form id="bulk-destroy-form" action="{{ route('leaves.bulk-destroy') }}" method="POST" style="display:none">
        @csrf
    </form>
@endsection

