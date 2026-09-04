@extends('layouts.app')

@section('title', 'عمال باليومية (طنطا)')

@section('styles')
<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #0061ff 0%, #60efff 100%);
        --card-bg-dark: rgba(30, 41, 59, 0.7);
        --card-border-dark: rgba(255, 255, 255, 0.1);
        --text-muted-dark: #94a3b8;
        --glass-bg: rgba(255, 255, 255, 0.03);
        --glass-border: rgba(255, 255, 255, 0.05);
    }

    /* Header Styling */
    .page-header-premium {
        background: var(--glass-bg);
        border: 1px solid var(--glass-border);
        border-radius: 24px;
        padding: 30px;
        backdrop-filter: blur(10px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        margin-bottom: 40px;
        position: relative;
        overflow: hidden;
    }

    .page-header-premium::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: var(--primary-gradient);
        filter: blur(100px);
        opacity: 0.15;
        border-radius: 50%;
        z-index: 0;
    }

    .header-content {
        position: relative;
        z-index: 1;
    }

    .title-gradient {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        font-weight: 900;
        letter-spacing: -0.5px;
    }

    /* Button Styling */
    .btn-premium-add {
        background: var(--primary-gradient);
        border: none;
        color: white;
        font-weight: 700;
        padding: 12px 28px;
        border-radius: 50px;
        box-shadow: 0 10px 20px rgba(0, 97, 255, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-premium-add:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 25px rgba(0, 97, 255, 0.4);
        color: white;
    }

    /* Card Styling */
    .service-card {
        background: var(--card-bg-dark);
        border: 1px solid var(--card-border-dark);
        border-radius: 20px;
        padding: 24px;
        backdrop-filter: blur(12px);
        transition: all 0.4s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .service-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 4px;
        background: var(--primary-gradient);
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .service-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
        border-color: rgba(255, 255, 255, 0.15);
    }

    .service-card:hover::after {
        opacity: 1;
    }

    .card-icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        background: linear-gradient(135deg, rgba(0, 97, 255, 0.1) 0%, rgba(96, 239, 255, 0.1) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        color: #60efff;
        margin-bottom: 20px;
        border: 1px solid rgba(96, 239, 255, 0.2);
        transition: transform 0.3s ease;
    }

    .service-card:hover .card-icon-wrapper {
        transform: scale(1.1) rotate(-5deg);
    }

    .service-name {
        font-size: 1.25rem;
        font-weight: 800;
        color: white;
        margin-bottom: 5px;
    }

    .type-badge {
        background: rgba(255, 255, 255, 0.1);
        color: #e2e8f0;
        padding: 6px 14px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 600;
        display: inline-block;
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    .contact-info {
        background: rgba(0, 0, 0, 0.2);
        border-radius: 12px;
        padding: 12px 16px;
        margin: 15px 0;
        display: flex;
        align-items: center;
        gap: 12px;
        border: 1px solid rgba(255, 255, 255, 0.02);
    }

    .contact-info i {
        color: #60efff;
        font-size: 1.1rem;
    }

    .contact-info span {
        color: #cbd5e1;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.5px;
    }

    .notes-box {
        background: rgba(255, 255, 255, 0.02);
        border-left: 3px solid #3b82f6;
        padding: 12px 15px;
        border-radius: 0 10px 10px 0;
        font-size: 0.9rem;
        color: var(--text-muted-dark);
        margin-bottom: 20px;
        line-height: 1.6;
        flex-grow: 1;
    }

    .card-actions {
        display: flex;
        gap: 10px;
        margin-top: auto;
        padding-top: 15px;
        border-top: 1px solid rgba(255, 255, 255, 0.05);
    }

    .action-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 10px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.2s ease;
        text-decoration: none;
        border: 1px solid transparent;
    }

    .edit-btn {
        background: rgba(59, 130, 246, 0.1);
        color: #60a5fa;
        border-color: rgba(59, 130, 246, 0.2);
    }

    .edit-btn:hover {
        background: rgba(59, 130, 246, 0.2);
        color: white;
    }

    .delete-btn {
        background: rgba(239, 68, 68, 0.1);
        color: #f87171;
        border-color: rgba(239, 68, 68, 0.2);
    }

    .delete-btn:hover {
        background: rgba(239, 68, 68, 0.2);
        color: white;
    }

    /* Modal Enhancements */
    .premium-modal .modal-content {
        background: #0f172a;
        border: 1px solid rgba(255,255,255,0.1);
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .premium-modal .modal-header {
        background: transparent;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        padding: 24px 30px;
        display: flex;
        align-items: center;
        flex-direction: row-reverse;
        justify-content: space-between;
    }

    .premium-modal .modal-title {
        font-weight: 800;
        color: white;
        font-size: 1.4rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .premium-modal .modal-body {
        padding: 30px;
    }

    .form-floating-custom {
        position: relative;
        margin-bottom: 20px;
    }

    .form-floating-custom label {
        display: block;
        margin-bottom: 8px;
        color: #94a3b8;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .form-floating-custom .form-control {
        background: rgba(0,0,0,0.2);
        border: 1px solid rgba(255,255,255,0.1);
        color: white;
        border-radius: 12px;
        padding: 14px 18px;
        transition: all 0.3s ease;
    }

    .form-floating-custom .form-control:focus {
        background: rgba(0,0,0,0.3);
        border-color: #3b82f6;
        box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    }

    .premium-modal .modal-footer {
        border-top: 1px solid rgba(255,255,255,0.05);
        padding: 20px 30px;
        background: rgba(0,0,0,0.1);
        border-radius: 0 0 24px 24px;
        display: flex;
        justify-content: flex-start;
        gap: 15px;
    }

    .btn-save {
        background: var(--primary-gradient);
        border: none;
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 700;
        color: white;
        transition: all 0.3s;
    }

    .btn-save:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 97, 255, 0.3);
    }

    .btn-cancel {
        background: rgba(255,255,255,0.05);
        border: 1px solid rgba(255,255,255,0.1);
        padding: 12px 25px;
        border-radius: 50px;
        color: #cbd5e1;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-cancel:hover {
        background: rgba(255,255,255,0.1);
        color: white;
    }

    /* Light Theme Adaptations */
    body:not(.theme-dark) .page-header-premium {
        background: white;
        border-color: #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,0.05);
    }

    body:not(.theme-dark) .text-slate-400,
    body:not(.theme-dark) .text-muted-dark {
        color: #64748b;
    }

    body:not(.theme-dark) .service-card {
        background: white;
        border-color: #e2e8f0;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    body:not(.theme-dark) .service-name {
        color: #0f172a;
    }

    body:not(.theme-dark) .type-badge {
        background: #f1f5f9;
        color: #475569;
        border-color: #e2e8f0;
    }

    body:not(.theme-dark) .contact-info {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    body:not(.theme-dark) .contact-info span {
        color: #334155;
    }

    body:not(.theme-dark) .notes-box {
        background: #f8fafc;
        color: #475569;
    }

    body:not(.theme-dark) .premium-modal .modal-content {
        background: white;
        border-color: #e2e8f0;
    }

    body:not(.theme-dark) .premium-modal .modal-title {
        color: #0f172a;
    }

    body:not(.theme-dark) .form-floating-custom label {
        color: #475569;
    }

    body:not(.theme-dark) .form-floating-custom .form-control {
        background: white;
        border-color: #cbd5e1;
        color: #0f172a;
    }

    body:not(.theme-dark) .premium-modal .modal-footer {
        background: #f8fafc;
        border-color: #e2e8f0;
    }

    body:not(.theme-dark) .btn-cancel {
        background: white;
        border-color: #cbd5e1;
        color: #475569;
    }

    body:not(.theme-dark) .btn-cancel:hover {
        background: #f1f5f9;
        color: #0f172a;
    }
</style>
@endsection

@section('content')
<div class="page-header-premium">
    <div class="header-content row align-items-center">
        <div class="col-md-7">
            <h1 class="title-gradient mb-2">عمال باليومية (طنطا)</h1>
            <p class="text-slate-400 mb-0 fs-5">إدارة قاعدة بيانات العمال المهنيين المتاحين للعمل باليومية بكفاءة عالية</p>
        </div>
        <div class="col-md-5 text-md-end mt-4 mt-md-0">
            <button type="button" class="btn-premium-add" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-circle-fill fs-5"></i>
                إضافة عامل جديد
            </button>
        </div>
    </div>
</div>

<div class="row g-4">
    @forelse($workers as $item)
        <div class="col-xl-3 col-lg-4 col-md-6">
            <div class="service-card">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="card-icon-wrapper">
                        <i class="bi bi-person-gear"></i>
                    </div>
                    <span class="type-badge">
                        <i class="bi bi-tags-fill me-1 opacity-50"></i>
                        {{ $item->profession ?? 'عامل' }}
                    </span>
                </div>

                <h3 class="service-name">{{ $item->name }}</h3>

                <div class="contact-info">
                    <i class="bi bi-telephone-outbound-fill"></i>
                    <span>{{ $item->phone ?? 'لا يوجد رقم مسجل' }}</span>
                </div>

                @if($item->notes)
                <div class="notes-box">
                    {{ Str::limit($item->notes, 80) }}
                </div>
                @else
                <div class="flex-grow-1"></div>
                @endif

                <div class="card-actions">
                    <button type="button" class="action-btn edit-btn" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                        <i class="bi bi-pen-fill"></i> تعديل
                    </button>

                    <form method="POST" action="{{ route('tanta-workers.destroy', $item) }}" class="d-inline flex-grow-1" onsubmit="return confirm('هل أنت متأكد من حذف هذا العامل نهائياً؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn delete-btn w-100">
                            <i class="bi bi-trash3-fill"></i> حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="empty-state-container text-center py-5">
                <img src="{{ asset('images/empty-state.svg') }}" alt="No Workers" style="width: 200px; opacity: 0.5; margin-bottom: 20px;">
                <h3 class="text-white fw-bold mb-3">لا توجد سجلات عمال حالياً</h3>
                <p class="text-slate-400 mb-4 fs-5">لم تقم بإضافة أي عمال باليومية لطنطا حتى الآن.</p>
                <button type="button" class="btn-premium-add" data-bs-toggle="modal" data-bs-target="#createModal">
                    <i class="bi bi-plus-circle-fill fs-5"></i>
                    ابدأ بإضافة أول عامل
                </button>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-5 d-flex justify-content-center">
    @if(method_exists($workers, 'hasPages') && $workers->hasPages())
        {{ $workers->links() }}
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade premium-modal" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('tanta-workers.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">
                        <div class="card-icon-wrapper m-0" style="width: 40px; height: 40px; font-size: 1.2rem;">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        إضافة عامل جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating-custom text-end">
                        <label>اسم العامل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="مثال: أحمد محمود">
                    </div>
                    <div class="form-floating-custom text-end">
                        <label>المهنة / التخصص</label>
                        <input type="text" name="profession" class="form-control" placeholder="مثال: نجار، كهربائي، حداد">
                    </div>
                    <div class="form-floating-custom text-end">
                        <label>رقم التليفون للتواصل</label>
                        <input type="text" name="phone" class="form-control" placeholder="01X XXXX XXXX">
                    </div>
                    <div class="form-floating-custom text-end mb-0">
                        <label>ملاحظات إضافية</label>
                        <textarea name="notes" class="form-control" rows="4" placeholder="أي تفاصيل أخرى تود إضافتها..."></textarea>
                    </div>
                </div>
                <div class="modal-footer flex-row-reverse">
                    <button type="submit" class="btn-save">
                        <i class="bi bi-check2-circle me-1"></i> حفظ العامل
                    </button>
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($workers as $item)
<div class="modal fade premium-modal" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('tanta-workers.update', $item) }}">
                @csrf
                @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title">
                        <div class="card-icon-wrapper m-0" style="width: 40px; height: 40px; font-size: 1.2rem; color: #f59e0b; background: rgba(245, 158, 11, 0.1); border-color: rgba(245, 158, 11, 0.2);">
                            <i class="bi bi-pencil-square"></i>
                        </div>
                        تعديل بيانات العامل
                    </h5>
                    <button type="button" class="btn-close btn-close-white opacity-75" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-floating-custom text-end">
                        <label>اسم العامل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ $item->name }}" required>
                    </div>
                    <div class="form-floating-custom text-end">
                        <label>المهنة / التخصص</label>
                        <input type="text" name="profession" class="form-control" value="{{ $item->profession }}">
                    </div>
                    <div class="form-floating-custom text-end">
                        <label>رقم التليفون للتواصل</label>
                        <input type="text" name="phone" class="form-control" value="{{ $item->phone }}">
                    </div>
                    <div class="form-floating-custom text-end mb-0">
                        <label>ملاحظات إضافية</label>
                        <textarea name="notes" class="form-control" rows="4">{{ $item->notes }}</textarea>
                    </div>
                </div>
                <div class="modal-footer flex-row-reverse">
                    <button type="submit" class="btn-save" style="background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);">
                        <i class="bi bi-save2-fill me-1"></i> تحديث البيانات
                    </button>
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach
@endsection
