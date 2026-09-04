@extends('layouts.app')

@section('title', 'العضويات')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<style>
    :root {
        --ws-primary-gradient: linear-gradient(135deg, #1a5c2e 0%, #2d8a4e 100%);
        --ws-card-shadow: 0 10px 30px rgba(0,0,0,0.05);
        --ws-card-hover-shadow: 0 20px 50px rgba(0,0,0,0.1);
    }

    .membership-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }

    .membership-card-elite {
        background: var(--ws-bg-card);
        border: 1px solid var(--ws-border);
        border-radius: 24px;
        overflow: hidden;
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: var(--ws-card-shadow);
        position: relative;
    }

    .membership-card-elite:hover {
        transform: translateY(-10px);
        border-color: var(--bs-success);
        box-shadow: var(--ws-card-hover-shadow);
    }

    .card-header-lux {
        padding: 24px;
        background: var(--ws-bg-stats-header);
        border-bottom: 1px solid var(--ws-border);
        position: relative;
    }

    .entity-badge {
        position: absolute;
        top: 20px;
        left: 20px;
        padding: 4px 12px;
        border-radius: 100px;
        font-size: 0.7rem;
        font-weight: 800;
        text-transform: uppercase;
        background: rgba(45, 138, 78, 0.1);
        color: #2d8a4e;
    }

    .card-body-lux {
        padding: 24px;
        flex-grow: 1;
    }

    .info-row-horizontal {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    .info-item-lux {
        background: var(--ws-bg-stats-inner-item);
        padding: 12px;
        border-radius: 16px;
        border: 1px solid var(--ws-border);
    }

    .info-label {
        font-size: 0.7rem;
        color: var(--ws-text-secondary);
        font-weight: 700;
        margin-bottom: 4px;
        display: block;
    }

    .info-value {
        font-size: 0.85rem;
        color: var(--ws-text-primary);
        font-weight: 700;
        display: block;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .card-footer-lux {
        padding: 16px 24px;
        background: var(--ws-bg-stats-header);
        border-top: 1px solid var(--ws-border);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .btn-action-lux {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
        border: 1px solid var(--ws-border);
        background: var(--ws-bg-card);
    }

    .btn-action-lux.edit:hover {
        background: var(--bs-primary);
        color: #fff;
        border-color: var(--bs-primary);
    }

    .btn-action-lux.delete:hover {
        background: var(--bs-danger);
        color: #fff;
        border-color: var(--bs-danger);
    }

    .discount-ribbon {
        background: var(--ws-primary-gradient);
        color: #fff;
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.9rem;
        box-shadow: 0 4px 15px rgba(45, 138, 78, 0.3);
    }

    /* Empty State */
    .empty-state-lux {
        background: var(--ws-bg-card);
        border: 2px dashed var(--ws-border);
        border-radius: 32px;
        padding: 80px 40px;
        text-align: center;
    }

    /* Modal adjustments */
    .premium-modal .modal-content {
        border-radius: 28px;
        border: none;
        box-shadow: 0 25px 80px rgba(0,0,0,0.2);
    }
    
    body.theme-dark .membership-card-elite { background: var(--bg-card); }
    body.theme-dark .info-item-lux { background: rgba(255,255,255,0.03); }
</style>

<div class="container-fluid py-4">
    <div class="row align-items-center mb-5 animate-reveal-down">
        <div class="col-md-6">
            <h1 class="h2 fw-800 text-stats-main mb-1">
                <i class="bi bi-person-badge text-success me-2"></i>العضويات والجهات المتعاونة
            </h1>
            <p class="text-muted-theme small mb-0 font-outfit">Memberships & Partner Entities Management</p>
        </div>
        <div class="col-md-6 text-end">
            <button type="button" class="btn btn-success rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
                <i class="bi bi-plus-lg me-2"></i> إضافة جهة جديدة
            </button>
        </div>
    </div>

    @if($memberships->count() > 0)
    <div class="membership-grid animate-up">
        @foreach($memberships as $item)
        <div class="membership-card-elite">
            <div class="card-header-lux">
                <span class="entity-badge">{{ $item->entity_type ?? 'جهة' }}</span>
                <h4 class="fw-800 text-stats-main mb-1 mt-3" title="{{ $item->entity_name }}">{{ Str::limit($item->entity_name, 30) }}</h4>
                <div class="d-flex align-items-center x-small text-muted-theme fw-bold">
                    <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ Str::limit($item->entity_address ?? 'العنوان غير محدد', 40) }}
                </div>
            </div>

            <div class="card-body-lux">
                <div class="info-row-horizontal">
                    <div class="info-item-lux">
                        <span class="info-label">الخدمة المقدمة</span>
                        <span class="info-value" title="{{ $item->service_provided }}">{{ $item->service_provided ?? '—' }}</span>
                    </div>
                    <div class="info-item-lux">
                        <span class="info-label">الفئة المستفيدة</span>
                        <span class="info-value" title="{{ $item->beneficiary_category }}">{{ $item->beneficiary_category ?? '—' }}</span>
                    </div>
                </div>

                <div class="info-row-horizontal">
                    <div class="info-item-lux">
                        <span class="info-label">رقم التواصل</span>
                        <span class="info-value font-outfit" dir="ltr">{{ $item->contact_number ?? '—' }}</span>
                    </div>
                    <div class="info-item-lux">
                        <span class="info-label">حالة التعاون</span>
                        <span class="info-value">
                            @if($item->cooperation_status)
                                <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i> {{ $item->cooperation_status }}</span>
                            @else
                                <span class="text-muted opacity-50">قيد الانتظار</span>
                            @endif
                        </span>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mt-3">
                    <div class="discount-ribbon">
                        <i class="bi bi-percent me-1"></i> {{ $item->discount_percentage ?? 'عرض خاص' }}
                    </div>
                    <div class="text-end">
                        <span class="d-block x-small text-muted-theme fw-800">صلاحية التعاون</span>
                        <span class="font-outfit small fw-bold text-stats-main">
                            {{ $item->cooperation_end_date ? $item->cooperation_end_date->format('Y/m/d') : 'دائم' }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-footer-lux">
                <div class="d-flex gap-2">
                    <button type="button" class="btn-action-lux edit" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}" title="تعديل">
                        <i class="bi bi-pencil-square"></i>
                    </button>
                    <form method="POST" action="{{ route('memberships.destroy', $item) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف {{ $item->entity_name }}؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-action-lux delete" title="حذف">
                            <i class="bi bi-trash"></i>
                        </button>
                    </form>
                </div>
                <button class="btn btn-sm btn-link text-success fw-bold text-decoration-none" data-bs-toggle="modal" data-bs-target="#editModal{{ $item->id }}">
                    عرض كافة التفاصيل <i class="bi bi-arrow-left ms-1"></i>
                </button>
            </div>
        </div>
        @endforeach
    </div>

    @if(method_exists($memberships, 'hasPages') && $memberships->hasPages())
        <div class="mt-5 d-flex justify-content-center">
            {{ $memberships->links() }}
        </div>
    @endif

    @else
    <div class="empty-state-lux animate-up">
        <div class="empty-icon-wrapper mb-4">
            <i class="bi bi-person-badge-fill text-muted opacity-25" style="font-size: 6rem;"></i>
        </div>
        <h3 class="fw-800 text-stats-main">لا توجد عضويات حالياً</h3>
        <p class="text-muted-theme">ابدأ بإضافة الجهات المتعاونة والخصومات المتاحة للأعضاء الآن.</p>
        <button class="btn btn-success rounded-pill px-5 py-2 fw-bold mt-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#createModal">
            <i class="bi bi-plus-lg me-2"></i> إضافة أول جهة
        </button>
    </div>
    @endif
</div>

{{-- Keep the existing Modals (Create & Edit) but with slight style improvements if needed --}}
<!-- Create Modal -->
<div class="modal fade premium-modal" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <form method="POST" action="{{ route('memberships.store') }}">
                @csrf
                <div class="modal-header bg-success text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2"></i>إضافة عضوية / جهة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-stats-card-main">
                    <div class="row g-4">
                        {{-- معلومات الجهة الأساسية --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-success mb-3 pb-2 border-bottom"><i class="bi bi-building me-2"></i> معلومات الجهة الأساسية</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">اسم الجهة / المكان <span class="text-danger">*</span></label>
                                    <input type="text" name="entity_name" class="form-control rounded-3" required placeholder="مثال: مستشفى الحياة">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">نوع الجهة</label>
                                    <input type="text" name="entity_type" class="form-control rounded-3" placeholder="مثال: طبية، تعليمية...">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">الخدمة المقدمة</label>
                                    <input type="text" name="service_provided" class="form-control rounded-3">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">عنوان الجهة بالكامل</label>
                                    <input type="text" name="entity_address" class="form-control rounded-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">رابط الموقع الجغرافي (Maps)</label>
                                    <input type="text" name="entity_location" class="form-control rounded-3">
                                </div>
                            </div>
                        </div>

                        {{-- معلومات الخصم والتعاون --}}
                        <div class="col-md-6">
                            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="bi bi-percent me-2"></i> تفاصيل الخصم</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">نسبة الخصم / العرض</label>
                                    <input type="text" name="discount_percentage" class="form-control rounded-3" placeholder="مثال: 20% خصم">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">الفئة المستفيدة</label>
                                    <input type="text" name="beneficiary_category" class="form-control rounded-3">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted-theme">شروط تفعيل الخصم</label>
                                    <textarea name="discount_conditions" class="form-control rounded-3" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-warning mb-3 pb-2 border-bottom"><i class="bi bi-telephone me-2"></i> قنوات التواصل</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">رقم التواصل الأساسي</label>
                                    <input type="text" name="contact_number" class="form-control rounded-3 font-outfit">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control rounded-3 font-outfit">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">اسم مسؤول التواصل</label>
                                    <input type="text" name="entity_contact_name" class="form-control rounded-3">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">رقم مسؤول التواصل</label>
                                    <input type="text" name="contact_person_number" class="form-control rounded-3 font-outfit">
                                </div>
                            </div>
                        </div>

                        <div class="col-12">
                            <h6 class="fw-bold text-secondary mb-3 pb-2 border-bottom"><i class="bi bi-handshake me-2"></i> تواريخ التعاون والتقييم</h6>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted-theme">بدء التعاون</label>
                                    <input type="date" name="cooperation_start_date" class="form-control rounded-3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted-theme">انتهاء التعاون</label>
                                    <input type="date" name="cooperation_end_date" class="form-control rounded-3">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted-theme">حالة التعاون</label>
                                    <input type="text" name="cooperation_status" class="form-control rounded-3" placeholder="نشط، منتهي...">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label small fw-bold text-muted-theme">رتبة الأولوية</label>
                                    <input type="text" name="priority_level" class="form-control rounded-3">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-stats-header p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-success rounded-pill px-5 fw-bold shadow-sm"><i class="bi bi-check-lg me-1"></i> حفظ البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modals -->
@foreach($memberships as $item)
<div class="modal fade premium-modal" id="editModal{{ $item->id }}" tabindex="-1">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content shadow-lg border-0">
            <form method="POST" action="{{ route('memberships.update', $item) }}">
                @csrf
                @method('PUT')
                <div class="modal-header bg-primary text-white border-0 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2"></i>تعديل بيانات: {{ $item->entity_name }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-stats-card-main">
                    <div class="row g-4">
                        {{-- Same structure as Create Modal but with values --}}
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-3 pb-2 border-bottom"><i class="bi bi-building me-2"></i> معلومات الجهة</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">اسم الجهة / المكان</label>
                                    <input type="text" name="entity_name" class="form-control rounded-3" value="{{ $item->entity_name }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">نوع الجهة</label>
                                    <input type="text" name="entity_type" class="form-control rounded-3" value="{{ $item->entity_type }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">الخدمة المقدمة</label>
                                    <input type="text" name="service_provided" class="form-control rounded-3" value="{{ $item->service_provided }}">
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-info mb-3 pb-2 border-bottom"><i class="bi bi-percent me-2"></i> الخصومات والعروض</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">نسبة الخصم</label>
                                    <input type="text" name="discount_percentage" class="form-control rounded-3" value="{{ $item->discount_percentage }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">الفئة المستفيدة</label>
                                    <input type="text" name="beneficiary_category" class="form-control rounded-3" value="{{ $item->beneficiary_category }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted-theme">ملاحظات وشروط الخصم</label>
                                    <textarea name="discount_conditions" class="form-control rounded-3" rows="2">{{ $item->discount_conditions }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <h6 class="fw-bold text-warning mb-3 pb-2 border-bottom"><i class="bi bi-telephone me-2"></i> بيانات التواصل</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">الرقم الأساسي</label>
                                    <input type="text" name="contact_number" class="form-control rounded-3 font-outfit" value="{{ $item->contact_number }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted-theme">البريد الإلكتروني</label>
                                    <input type="email" name="email" class="form-control rounded-3 font-outfit" value="{{ $item->email }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <h6 class="fw-bold text-secondary mb-3 pb-2 border-bottom"><i class="bi bi-clipboard-data me-2"></i> بيانات إضافية وملاحظات</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">رقم مسؤول التواصل</label>
                                    <input type="text" name="contact_person_number" class="form-control rounded-3" value="{{ $item->contact_person_number }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">اسم مصدر الجهة</label>
                                    <input type="text" name="entity_source_name" class="form-control rounded-3" value="{{ $item->entity_source_name }}">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted-theme">درجة الأولوية</label>
                                    <input type="text" name="priority_level" class="form-control rounded-3" value="{{ $item->priority_level }}">
                                </div>
                                <div class="col-12">
                                    <label class="form-label small fw-bold text-muted-theme">ملاحظات عامة</label>
                                    <textarea name="notes" class="form-control rounded-3" rows="3">{{ $item->notes }}</textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 bg-stats-header p-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm"><i class="bi bi-check-lg me-1"></i> تحديث البيانات</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endforeach

<script>
    // Smooth reveal animations
    document.addEventListener('DOMContentLoaded', () => {
        const cards = document.querySelectorAll('.membership-card-elite');
        cards.forEach((card, index) => {
            card.style.animationDelay = `${index * 0.05}s`;
        });
    });
</script>

@endsection
