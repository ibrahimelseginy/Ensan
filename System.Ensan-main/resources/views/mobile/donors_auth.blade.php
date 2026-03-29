@extends('layouts.app')

@section('title', 'تسجيلات دخول الموبايل')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4 animate-slide-up">
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden" style="border-radius: 15px;">
                <div class="card-header bg-dark p-4 border-0">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h4 class="mb-1 fw-bold text-white"><i class="bi bi-person-badge-fill me-2 text-warning"></i> المتبرعين المسجلين من الموبايل</h4>
                            <p class="text-white-50 mb-0">عرض الأشخاص الذين قاموا بإنشاء حسابات عبر تطبيق الهاتف الذكي</p>
                        </div>
                        <div class="bg-warning text-dark px-3 py-2 rounded-pill fw-bold">
                            إجمالي المسجلين: {{ $donors->count() }}
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="px-4 py-3 text-dark fw-bold border-0">المتبرع</th>
                                    <th class="py-3 text-dark fw-bold border-0">رقم الهاتف / الإيميل</th>
                                    <th class="py-3 text-dark fw-bold border-0 text-center">تاريخ الانضمام</th>
                                    <th class="py-3 text-dark fw-bold border-0 text-center">الحالة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($donors as $donor)
                                <tr class="border-bottom border-light">
                                    <td class="px-4 py-3">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-soft-warning text-warning rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 40px; height: 40px; background-color: rgba(255, 193, 7, 0.1);">
                                                {{ substr($donor->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <h6 class="mb-0 fw-bold">{{ $donor->name }}</h6>
                                                <small class="text-muted">ID: #{{ $donor->id }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="py-3">
                                        <div class="mb-1"><i class="bi bi-phone me-2 text-muted"></i>{{ $donor->phone ?? 'N/A' }}</div>
                                        <div class="small text-muted"><i class="bi bi-envelope me-2"></i>{{ $donor->email }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="fw-bold">{{ $donor->created_at->format('Y-m-d') }}</div>
                                        <div class="small text-muted">{{ $donor->created_at->format('h:i A') }}</div>
                                    </td>
                                    <td class="py-3 text-center">
                                        @if($donor->active)
                                            <span class="badge bg-soft-success text-success rounded-pill px-3" style="background-color: rgba(25, 135, 84, 0.1);">نشط</span>
                                        @else
                                            <span class="badge bg-soft-danger text-danger rounded-pill px-3" style="background-color: rgba(220, 53, 69, 0.1);">غير نشط</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="py-5 text-center">
                                        <div class="text-muted">
                                            <i class="bi bi-people mb-3 d-block" style="font-size: 3rem;"></i>
                                            <p class="mb-0">لا يوجد متبرعين مسجلين من الموبايل حتى الآن</p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .animate-slide-up { animation: slideUp 0.5s ease-out; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
    .bg-soft-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-soft-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-soft-danger { background-color: rgba(220, 53, 69, 0.1); }
</style>
@endsection
