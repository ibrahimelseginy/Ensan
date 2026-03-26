@extends('layouts.app')
@section('content')
    {{-- Page Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="bi bi-chat-dots text-primary me-2"></i>الشكاوى
            </h4>
        </div>
        <div>
            <a href="{{ route('complaints.create') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-lg me-1"></i> إضافة شكوى
            </a>
        </div>
    </div>

    {{-- Complaints Table --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="table-responsive" style="min-height: 400px; overflow-x: auto; overflow-y: visible;">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>المصدر</th>
                        <th>العنوان</th>
                        <th>الحالة</th>
                        <th class="text-end">إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($complaints as $c)
                        <tr>
                            <td class="text-muted">{{ $c->id }}</td>
                            <td>
                                <span class="badge bg-secondary-subtle text-secondary">{{ $c->source_type }}
                                    #{{ $c->source_id }}</span>
                            </td>
                            <td class="fw-medium">{{ $c->subject }}</td>
                            <td>
                                @php
                                    $statusClass = match ($c->status) {
                                        'open' => 'bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10',
                                        'in_progress' => 'bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10',
                                        'resolved' => 'bg-success bg-opacity-10 text-success border border-success border-opacity-10',
                                        'pending' => 'bg-secondary bg-opacity-10 text-secondary border border-secondary border-opacity-10',
                                        'closed' => 'bg-dark bg-opacity-10 text-secondary border border-secondary border-opacity-10',
                                        default => 'bg-secondary bg-opacity-10 text-secondary'
                                    };
                                    $statusText = match ($c->status) {
                                        'open' => 'مفتوحة',
                                        'in_progress' => 'جارية',
                                        'resolved' => 'تم الحل',
                                        'pending' => 'قيد الانتظار',
                                        'closed' => 'مغلقة',
                                        default => $c->status
                                    };
                                @endphp
                                <span class="status-badge {{ $statusClass }}">{{ $statusText }}</span>
                            </td>
                            <td class="text-end">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-light rounded-circle border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="bi bi-three-dots-vertical"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 p-1">
                                        <li>
                                            <a class="dropdown-item rounded-2" href="{{ route('complaints.show', $c) }}">
                                                <i class="bi bi-eye me-2 opacity-50"></i> تفاصيل
                                            </a>
                                        </li>
                                        @if($c->pendingRequest)
                                            <li>
                                                <span class="dropdown-item rounded-2 text-warning bg-warning bg-opacity-10">
                                                    <i class="bi bi-hourglass-split me-2"></i> قيد المراجعة
                                                </span>
                                            </li>
                                        @else
                                            <li>
                                                <a class="dropdown-item rounded-2" href="{{ route('complaints.edit', $c) }}">
                                                    <i class="bi bi-pencil me-2 opacity-50"></i> تعديل
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider my-1"></li>
                                            <li>
                                                <form method="POST" action="{{ route('complaints.destroy', $c) }}" onsubmit="return confirm('حذف الشكوى؟');">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item rounded-2 text-danger">
                                                        <i class="bi bi-trash me-2"></i> حذف
                                                    </button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-5">
                                <div class="empty-state">
                                    <i class="bi bi-chat-left-dots"></i>
                                    <h5>لا توجد شكاوى مسجلة</h5>
                                    <p>اضغط على "إضافة شكوى" لتسجيل شكوى جديدة</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $complaints->links() }}</div>
@endsection