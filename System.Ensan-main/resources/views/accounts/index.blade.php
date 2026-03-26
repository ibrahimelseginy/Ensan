@extends('layouts.app')
@section('content')

{{-- Premium Dashboard Hero --}}
<div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #0f766e 0%, #115e59 50%, #134e4a 100%);">
    <div class="hero-content">
        <div class="hero-greeting">المالية 💰</div>
        <h1 class="hero-title">دليل الحسابات</h1>
        <p class="hero-subtitle">إدارة شجرة الحسابات والهيكل المالي للمنظمة</p>
        <div class="hero-actions d-flex gap-2">
            <a href="{{ route('accounts.create') }}" class="btn btn-sm rounded-pill px-4">
                <i class="bi bi-plus-lg me-1"></i> إضافة حساب
            </a>
        </div>
    </div>
    <i class="bi bi-journal-text hero-icon d-none d-md-block"></i>
</div>

<div class="chart-container animate-slide-up animate-delay-1 mb-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
        <thead class="bg-transparent">
            <tr>
                <th>الكود</th>
                <th>الاسم</th>
                <th>النوع</th>
                <th>الحساب الرئيسي</th>
                <th>الوصف</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($allAccounts as $account)
            <tr>
                <td>{{ $account->code }}</td>
                <td>{{ $account->name }}</td>
                <td>
                    @switch($account->type)
                        @case('asset') أصول @break
                        @case('liability') خصوم @break
                        @case('equity') حقوق ملكية @break
                        @case('revenue') إيرادات @break
                        @case('expense') مصروفات @break
                        @default {{ $account->type }}
                    @endswitch
                </td>
                <td>{{ optional($account->parent)->name }}</td>
                <td>{{Str::limit($account->description, 50)}}</td>
                <td>
                    <a href="{{ route('accounts.show', $account) }}" class="btn btn-sm btn-outline-info">كشف حساب</a>
                    @if(auth()->check())
                        @if(auth()->user()->hasRole('admin') || auth()->user()->hasRole('manager'))
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-primary">تعديل</a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الحساب نهائياً؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">حذف</button>
                            </form>
                        @else
                            <a href="{{ route('accounts.edit', $account) }}" class="btn btn-sm btn-outline-warning">طلب تعديل</a>
                            <form action="{{ route('accounts.destroy', $account) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من طلب حذف هذا الحساب؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-warning">طلب حذف</button>
                            </form>
                        @endif
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    </div>
    {{ $allAccounts->links() }}
</div>
@endsection
