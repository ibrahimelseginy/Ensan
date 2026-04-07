@extends('layouts.app')
@section('content')

    {{-- Premium Dashboard Hero --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #475569 0%, #334155 50%, #1e293b 100%);">
        <div class="hero-content">
            <div class="hero-greeting">لوحة التحكم 🛡️</div>
            <h1 class="hero-title">الأدوار والصلاحيات</h1>
            <p class="hero-subtitle">إدارة أدوار المستخدمين ومنح الصلاحيات ({{ $roles->total() }} دور)</p>
            <div class="hero-actions d-flex gap-2">
                <form action="{{ route('roles.index') }}" method="GET" class="d-none d-md-flex">
                    <div class="input-group input-group-sm">
                        <input type="text" name="q" class="form-control rounded-pill-start border-0" placeholder="بحث عن دور..." value="{{ request('q') }}" style="min-width: 200px;">
                        <button class="btn btn-light rounded-pill-end border-0 text-primary" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <a href="{{ route('roles.create') }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-plus-lg me-1"></i> إضافة دور جديد
                </a>
            </div>
        </div>
        <i class="bi bi-shield-lock-fill hero-icon d-none d-md-block"></i>
    </div>

    <div class="chart-container animate-slide-up animate-delay-1">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-transparent">
                        <tr>
                            <th class="border-0 p-3 ps-4">الدور</th>
                            <th class="border-0 p-3">المعرف (Key)</th>
                            <th class="border-0 p-3">الوصف</th>
                            <th class="border-0 p-3 text-end pe-4">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $r)
                            <tr onclick="window.location='{{ route('roles.show', $r) }}'" style="cursor: pointer;">
                                <td class="p-3 ps-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-initial rounded bg-primary-subtle text-primary fw-bold me-3 d-flex align-items-center justify-content-center"
                                            style="width: 40px; height: 40px;">
                                            {{ strtoupper(substr($r->key, 0, 1)) }}
                                        </div>
                                        <span class="fw-bold text-body">{{ $r->name }}</span>
                                    </div>
                                </td>
                                <td class="p-3">
                                    <span class="badge bg-secondary-subtle text-body border font-monospace">{{ $r->key }}</span>
                                </td>
                                <td class="p-3 text-muted">
                                    {{ $r->description ?? '—' }}
                                </td>
                                <td class="p-3 text-end pe-4">
                                    <div class="btn-group" onclick="event.stopPropagation()">
                                        <a href="{{ route('roles.show', $r) }}" class="btn btn-sm btn-outline-secondary"
                                            title="عرض الصلاحيات">
                                            <i class="bi bi-shield-lock"></i>
                                        </a>
                                        <a href="{{ route('roles.edit', $r) }}" class="btn btn-sm btn-outline-primary"
                                            title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="if(confirm('هل أنت متأكد من حذف هذا الدور؟')) document.getElementById('del-{{ $r->id }}').submit()"
                                            title="حذف">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                    <form id="del-{{ $r->id }}" action="{{ route('roles.destroy', $r) }}" method="POST"
                                        class="d-none">
                                        @csrf @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-shield-x display-4 mb-3 d-block opacity-50"></i>
                                    لا توجد أدوار معرفة في النظام
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($roles->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $roles->links() }}
            </div>
        @endif
    </div>

@endsection

