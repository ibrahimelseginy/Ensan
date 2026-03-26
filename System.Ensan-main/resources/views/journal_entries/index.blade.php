@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 50%, #1d4ed8 100%);">
    <div class="hero-content">
        <div class="hero-greeting">المحاسبة 📒</div>
        <h1 class="hero-title">القيود اليومية</h1>
        <p class="hero-subtitle">إدارة ومراجعة قيود اليومية والعمليات المالية</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('journal-entries.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة قيد
            </a>
        </div>
    </div>
    <i class="bi bi-journal-bookmark hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1 mb-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-transparent">
                    <tr>
                        <th>#</th>
                        <th>التاريخ</th>
                        <th>الوصف</th>
                        <th>النوع</th>
                        <th>إجمالي</th>
                        <th>الحالة</th>
                        <th>إجراءات</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($entries as $entry)
                        <tr>
                            <td>{{ $entry->id }}</td>
<td>{{ optional($entry->date)->format('Y-m-d') }}</td>
                            <td>{{ Str::limit($entry->description, 50) }}</td>
                            <td>{{ $entry->entry_type }}</td>
                            <td>{{ $entry->lines->sum('debit') }}</td>
                            <td>
                                @if($entry->locked) <span class="badge bg-secondary">مغلق</span> @else <span
                                class="badge bg-success">مفتوح</span> @endif
                            </td>
                            <td class="text-end position-static">
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary rounded-pill px-3" type="button" data-bs-toggle="dropdown" data-bs-boundary="viewport">
                                        <i class="bi bi-three-dots"></i> خيارات
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                        <li><a class="dropdown-item py-2" href="{{ route('journal-entries.show', $entry) }}"><i class="bi bi-eye me-2 text-primary"></i> عرض التفاصيل</a></li>
                                        @if(!$entry->locked)
                                            @php $isAdminOrManager = auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'); @endphp
                                            <li><a class="dropdown-item py-2" href="{{ route('journal-entries.edit', $entry) }}"><i class="bi bi-pencil me-2 text-secondary"></i> {{ $isAdminOrManager ? 'تعديل' : 'طلب تعديل' }}</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('journal-entries.destroy', $entry) }}" onsubmit="return confirm('هل أنت متأكد من {{ $isAdminOrManager ? 'حذف' : 'طلب حذف' }} هذا القيد؟');">
                                                    @csrf @method('DELETE')
                                                    <button class="dropdown-item py-2 text-danger"><i class="bi bi-trash me-2"></i> {{ $isAdminOrManager ? 'حذف نهائي' : 'طلب حذف' }}</button>
                                                </form>
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            {{ $entries->links() }}
        </div>
@endsection