@extends('layouts.app')

@section('title', 'عمال باليومية (طنطا)')

@section('styles')
<style>
    :root {
        --premium-blue: #0066ff;
        --dark-bg: #0b0e14;
        --card-dark: #111827;
        --slate-400: #94a3b8;
        --input-bg: #0f172a;
        --input-border: #1e293b;
    }

    body {
        background-color: var(--dark-bg) !important;
    }

    .service-card {
        background: var(--card-dark);
        border: 1px solid rgba(255,255,255,0.05);
        border-radius: 16px;
        transition: all 0.3s ease;
        height: 100%;
        position: relative;
    }
    .service-card:hover {
        transform: translateY(-5px);
        border-color: rgba(255,255,255,0.1);
        box-shadow: 0 10px 30px rgba(0,0,0,0.5);
    }
    .card-icon-circle {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--slate-400);
        font-size: 1.25rem;
    }

    /* Solid Dark Modal Styling */
    .dark-modal-custom .modal-content {
        background: #0f172a !important; /* Solid Slate 900 */
        background-color: #0f172a !important;
        border-radius: 12px;
        border: 1px solid rgba(255,255,255,0.1) !important;
        overflow: hidden;
        opacity: 1 !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important;
    }
    .dark-modal-custom .modal-header {
        background-color: var(--premium-blue) !important;
        color: white;
        border: 0;
        padding: 15px 20px;
        display: flex;
        flex-direction: row-reverse; /* Title right, X left */
        justify-content: space-between;
        align-items: center;
    }
    .dark-modal-custom .modal-title {
        color: white !important;
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
    }
    .dark-modal-custom .btn-close {
        background-image: none;
        color: white;
        opacity: 0.8;
        filter: none;
        font-size: 1rem;
        padding: 5px;
        margin: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .dark-modal-custom .btn-close::after {
        content: '\2715';
        font-size: 1.2rem;
    }
    .dark-modal-custom .btn-close:hover {
        opacity: 1;
    }

    .dark-modal-custom .modal-body {
        background: #0f172a !important;
        background-color: #0f172a !important;
        padding: 25px 30px;
        opacity: 1 !important;
    }
    .dark-modal-custom .form-label {
        color: white;
        font-weight: 500;
        margin-bottom: 8px;
        display: block;
        text-align: right;
    }
    .dark-modal-custom .form-control {
        background-color: #121212 !important;
        border: 1px solid #333 !important;
        color: white !important;
        border-radius: 8px;
        padding: 12px 15px;
        text-align: right;
    }
    
    .dark-modal-custom .modal-footer {
        background-color: transparent !important;
        border-top: 1px solid rgba(255,255,255,0.05);
        padding: 15px 25px 25px;
        display: flex;
        justify-content: flex-start;
        gap: 12px;
    }
    
    /* Buttons specifically from the last screenshot */
    .btn-save-premium {
        background-color: #00d1b2 !important;
        border: none !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 10px 25px !important;
        border-radius: 8px !important;
        font-size: 1rem;
    }
    .btn-cancel-premium {
        background-color: #363636 !important;
        border: 1px solid rgba(255,255,255,0.1) !important;
        color: white !important;
        font-weight: 700 !important;
        padding: 10px 20px !important;
        border-radius: 8px !important;
        font-size: 1rem;
    }

    .empty-state-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 100px 20px;
        color: #cbd5e1;
    }
    .empty-icon {
        width: 80px;
        height: 80px;
        background: rgba(255,255,255,0.03);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 25px;
        font-size: 2.5rem;
        color: #475569;
    }
</style>
@endsection

@section('content')
<div class="row align-items-center mb-5 mt-2">
    <div class="col-md-6 text-white">
        <h2 class="fw-800 mb-1">عمال باليومية (طنطا)</h2>
        <p class="text-slate-400 mb-0">إدارة قاعدة بيانات العمال المهنيين المتاحين للعمل باليومية</p>
    </div>
    <div class="col-md-6 text-end">
        <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-1"></i> إضافة عامل جديد
        </button>
    </div>
</div>

<div class="row g-4">
    @forelse($workers as $item)
        <div class="col-md-4">
            <div class="service-card p-4">
                <div class="d-flex align-items-center gap-3 mb-4">
                    <div class="card-icon-circle">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <div>
                        <h5 class="text-white fw-bold mb-1">{{ $item->name }}</h5>
                        <span class="badge bg-secondary rounded-pill px-3 py-1 small opacity-75">{{ $item->profession ?? 'عامل' }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex align-items-center gap-2 text-slate-400 mb-2">
                        <i class="bi bi-telephone small"></i>
                        <span class="small fw-bold">{{ $item->phone ?? 'لا يوجد رقم' }}</span>
                    </div>
                    @if($item->notes)
                    <div class="rounded-3 p-3 mt-3" style="background: rgba(0,0,0,0.2);">
                        <p class="text-slate-400 small mb-0 lh-base">{{ $item->notes }}</p>
                    </div>
                    @endif
                </div>

                <div class="d-flex gap-2 pt-2 border-top border-white border-opacity-10">
                    <button type="button" class="btn btn-link text-white-50 p-0 text-decoration-none fw-bold small me-auto"
                        data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                        <i class="bi bi-pencil-square me-1"></i> تعديل
                    </button>
                    <form method="POST" action="{{ route('tanta-workers.destroy', $item) }}" 
                        onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-link text-danger p-0 text-decoration-none fw-bold small opacity-75">
                            <i class="bi bi-trash me-1"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state-container">
                <div class="empty-icon text-white-50">
                    <i class="bi bi-inbox"></i>
                </div>
                <h4 class="fw-bold mb-2">لا توجد سجلات حالياً</h4>
                <p class="text-slate-500">ابدأ بإضافة أول سجل من خلال زر الإضافة في الأعلى</p>
                <div class="mt-4 opacity-10">
                    <img src="{{ asset('logo.png') }}" alt="logo" height="60">
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-5">
    @if(method_exists($workers, 'hasPages') && $workers->hasPages())
        {{ $workers->links() }}
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade dark-modal-custom" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #0f172a !important; opacity: 1 !important;">
            <form method="POST" action="{{ route('tanta-workers.store') }}">
                @csrf
                <div class="modal-header bg-primary text-white border-0" style="background-color: #0066ff !important;">
                    <h5 class="modal-title fw-bold">إضافة عامل جديد</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #0f172a !important; opacity: 1 !important;">
                    <div class="mb-4 text-end">
                        <label class="form-label">اسم العامل *</label>
                        <input type="text" name="name" class="form-control" required placeholder="أدخل اسم العامل">
                    </div>
                    <div class="mb-4 text-end">
                        <label class="form-label">المهنة</label>
                        <input type="text" name="profession" class="form-control" placeholder="مثلاً: ميكانيكي، نجار، كهربائي">
                    </div>
                    <div class="mb-4 text-end">
                        <label class="form-label">رقم التليفون</label>
                        <input type="text" name="phone" class="form-control" placeholder="012XXXXXXXX">
                    </div>
                    <div class="mb-0 text-end">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3" placeholder="أدخل أي ملاحظات إضافية"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-save-premium px-4">حفظ</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
                <div class="text-center pb-4 opacity-10">
                    <small class="text-white">جميع الحقوق محفوظة مؤسسة إنسان 2026 ©</small>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($workers as $item)
<div class="modal fade dark-modal-custom" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background-color: #0f172a !important; opacity: 1 !important;">
            <form method="POST" action="{{ route('tanta-workers.update', $item) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white border-0" style="background-color: #0066ff !important;">
                    <h5 class="modal-title fw-bold">تعديل بيانات العامل</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="background-color: #0f172a !important; opacity: 1 !important;">
                    <div class="mb-4 text-end">
                        <label class="form-label">اسم العامل *</label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    </div>
                    <div class="mb-4 text-end">
                        <label class="form-label">المهنة</label>
                        <input type="text" name="profession" class="form-control" value="{{ $item->profession }}">
                    </div>
                    <div class="mb-4 text-end">
                        <label class="form-label">رقم التليفون</label>
                        <input type="text" name="phone" class="form-control" value="{{ $item->phone }}">
                    </div>
                    <div class="mb-0 text-end">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control" rows="3">{{ $item->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="submit" class="btn btn-save-premium px-4">تحديث</button>
                    <button type="button" class="btn btn-cancel-premium px-4" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
