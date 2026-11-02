@extends('layouts.app')

@section('title', 'دليل الشراكات والتعاونات')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    {{-- Header & Actions --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #0d9488, #14b8a6);">
                    <i class="bi bi-handshake-fill fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-slate-800">دليل الشراكات والتعاونات</h3>
                    <p class="text-muted small mb-0">شبكة التعاونات، المراكز الطبية، الصيدليات، والمؤسسات الشريكة وخصومات المتطوعين والحالات</p>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <button type="button" class="btn btn-primary d-inline-flex align-items-center gap-2 shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#createCollaborationModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>إضافة تعاون / جهة جديدة</span>
            </button>
            <a href="{{ route('collaborations.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1">
                <i class="bi bi-arrow-clockwise"></i>
                <span>تحديث</span>
            </a>
        </div>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(13, 148, 136, 0.08), rgba(20, 184, 166, 0.15)); border-right: 4px solid #0d9488 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">إجمالي التعاونات</div>
                        <div class="fs-3 fw-bold text-dark mt-1">{{ number_format($stats['total']) }}</div>
                    </div>
                    <div class="p-3 rounded-circle text-teal" style="background: rgba(13, 148, 136, 0.15); color: #0d9488;">
                        <i class="bi bi-collection-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(5, 150, 105, 0.15)); border-right: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">سارية ونشطة الآن</div>
                        <div class="fs-3 fw-bold text-success mt-1">{{ number_format($stats['active']) }}</div>
                    </div>
                    <div class="p-3 rounded-circle text-success" style="background: rgba(16, 185, 129, 0.15);">
                        <i class="bi bi-check-circle-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(2, 132, 199, 0.08), rgba(6, 182, 212, 0.15)); border-right: 4px solid #0284c7 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">القطاع الطبي والصحي</div>
                        <div class="fs-3 fw-bold text-info mt-1">{{ number_format($stats['medical']) }}</div>
                    </div>
                    <div class="p-3 rounded-circle text-info" style="background: rgba(2, 132, 199, 0.15);">
                        <i class="bi bi-heart-pulse-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(124, 58, 237, 0.08), rgba(139, 92, 246, 0.15)); border-right: 4px solid #7c3aed !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">التصنيفات والأنشطة</div>
                        <div class="fs-3 fw-bold text-purple mt-1" style="color: #7c3aed;">{{ number_format($stats['categories_count']) }}</div>
                    </div>
                    <div class="p-3 rounded-circle" style="background: rgba(124, 58, 237, 0.15); color: #7c3aed;">
                        <i class="bi bi-tags-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Search & Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3 p-md-4">
            <form action="{{ route('collaborations.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-bold text-muted">البحث الشامل</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-0"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control bg-light border-0" placeholder="اسم الجهة، التخصص، الشخص المسؤول، الهاتف..." value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-12 col-sm-6 col-md-3">
                    <label class="form-label small fw-bold text-muted">التصنيف</label>
                    <select name="category" class="form-select bg-light border-0">
                        <option value="">جميع التصنيفات</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat }}" {{ request('category') === $cat ? 'selected' : '' }}>{{ $cat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-muted">حالة التعاقد</label>
                    <select name="status" class="form-select bg-light border-0">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط وساري</option>
                        <option value="extended" {{ request('status') === 'extended' ? 'selected' : '' }}>ممدود</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد التفاوض</option>
                        <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>منتهي</option>
                    </select>
                </div>
                <div class="col-12 col-sm-6 col-md-2">
                    <label class="form-label small fw-bold text-muted">المدينة / المركز</label>
                    <input type="text" name="city" class="form-control bg-light border-0" placeholder="كفر الشيخ، دسوق..." value="{{ request('city') }}">
                </div>
                <div class="col-12 col-sm-6 col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100" title="تصفية"><i class="bi bi-funnel-fill"></i></button>
                    @if(request()->anyFilled(['search', 'category', 'status', 'city']))
                        <a href="{{ route('collaborations.index') }}" class="btn btn-light" title="إلغاء التصفية"><i class="bi bi-x-lg"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Cards Grid --}}
    @if($collaborations->count() > 0)
        <div class="row g-3 g-md-4">
            @foreach($collaborations as $collab)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden position-relative hover-shadow transition-all" style="border-top: 4px solid #0d9488 !important;">
                        <div class="card-body p-4 d-flex flex-column">
                            {{-- Header --}}
                            <div class="d-flex align-items-start justify-content-between gap-2 mb-3">
                                <div>
                                    <span class="badge bg-light text-teal border border-teal-subtle px-2.5 py-1.5 rounded-pill small mb-2 d-inline-flex align-items-center gap-1.5" style="color: #0d9488; background-color: rgba(13,148,136,0.1);">
                                        <i class="bi {{ $collab->category_icon }}"></i>
                                        <span>{{ $collab->category }}</span>
                                    </span>
                                    <h5 class="fw-bold text-dark mb-1">{{ $collab->name }}</h5>
                                    @if($collab->specialty)
                                        <div class="text-muted small fw-medium"><i class="bi bi-briefcase me-1"></i>{{ $collab->specialty }}</div>
                                    @endif
                                </div>
                                <div>
                                    <span class="badge bg-{{ $collab->status_color }}-subtle text-{{ $collab->status_color }} px-2 py-1 rounded-pill small">
                                        {{ $collab->status_label }}
                                    </span>
                                </div>
                            </div>

                            {{-- Discounts Info Box --}}
                            <div class="p-3 rounded-3 mb-3" style="background: rgba(248, 250, 252, 0.85); border: 1px dashed #cbd5e1;">
                                <div class="row g-2">
                                    <div class="col-6">
                                        <div class="small text-muted fw-semibold mb-1">
                                            <i class="bi bi-person-badge text-primary me-1"></i>خصم المتطوعين:
                                        </div>
                                        <div class="fw-bold text-primary small">
                                            {{ $collab->discount_volunteers ?: 'غير محدد' }}
                                        </div>
                                        @if($collab->activation_volunteers)
                                            <div class="text-muted extra-small" style="font-size: 0.75rem;">
                                                <i class="bi bi-info-circle me-0.5"></i>{{ $collab->activation_volunteers }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="col-6">
                                        <div class="small text-muted fw-semibold mb-1">
                                            <i class="bi bi-heart-fill text-danger me-1"></i>خصم المستفيدين:
                                        </div>
                                        <div class="fw-bold text-danger small">
                                            {{ $collab->discount_beneficiaries ?: 'غير محدد' }}
                                        </div>
                                        @if($collab->activation_beneficiaries)
                                            <div class="text-muted extra-small" style="font-size: 0.75rem;">
                                                <i class="bi bi-info-circle me-0.5"></i>{{ $collab->activation_beneficiaries }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Service provided & details --}}
                            @if($collab->service_provided)
                                <div class="small text-slate-700 mb-2">
                                    <strong class="text-muted">الخدمات: </strong>{{ Str::limit($collab->service_provided, 90) }}
                                </div>
                            @endif

                            {{-- Contact Person & Phone --}}
                            <div class="mt-auto pt-2 border-top">
                                @if($collab->contact_person || $collab->phone)
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <div class="small text-muted">
                                            <i class="bi bi-person-fill me-1"></i>
                                            <span class="fw-semibold text-dark">{{ $collab->contact_person ?: 'جهة الاتصال' }}</span>
                                            @if($collab->contact_person_role)
                                                <span class="text-muted extra-small">({{ $collab->contact_person_role }})</span>
                                            @endif
                                        </div>
                                        @if($collab->phone)
                                            <div class="d-flex align-items-center gap-1">
                                                <a href="tel:{{ $collab->phone }}" class="btn btn-sm btn-outline-primary py-0 px-2 rounded-pill" title="اتصال">
                                                    <i class="bi bi-telephone-fill"></i>
                                                </a>
                                                @php
                                                    $cleanPhone = preg_replace('/[^0-9]/', '', $collab->phone);
                                                    if(str_starts_with($cleanPhone, '01')) { $cleanPhone = '2' . $cleanPhone; }
                                                @endphp
                                                <a href="https://wa.me/{{ $cleanPhone }}" target="_blank" class="btn btn-sm btn-outline-success py-0 px-2 rounded-pill" title="واتساب">
                                                    <i class="bi bi-whatsapp"></i>
                                                </a>
                                            </div>
                                        @endif
                                    </div>
                                    @if($collab->phone)
                                        <div class="small text-muted d-flex align-items-center gap-2 mb-1">
                                            <i class="bi bi-telephone text-primary"></i>
                                            <span class="font-monospace fw-semibold">{{ $collab->phone }}</span>
                                            @if($collab->phone_alt)
                                                <span class="text-muted font-monospace">/ {{ $collab->phone_alt }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endif

                                @if($collab->address || $collab->city)
                                    <div class="small text-muted d-flex align-items-start gap-1.5 mb-2">
                                        <i class="bi bi-geo-alt-fill text-danger mt-0.5"></i>
                                        <span>{{ $collab->city ? $collab->city . ' - ' : '' }}{{ $collab->address }}</span>
                                    </div>
                                @endif

                                {{-- Links if any --}}
                                @if(!empty($collab->doc_links))
                                    <div class="d-flex flex-wrap gap-1 mb-2">
                                        @foreach($collab->doc_links as $link)
                                            <a href="{{ $link['url'] ?? '#' }}" target="_blank" class="btn btn-sm btn-light border py-0 px-2 rounded-pill extra-small text-primary">
                                                <i class="bi bi-box-arrow-up-right me-1"></i>{{ $link['title'] ?? 'رابط الشيت' }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Action buttons --}}
                                <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                    <span class="text-warning small" title="التقييم">{{ $collab->rating_stars }}</span>
                                    <div class="d-flex align-items-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-secondary py-1 px-2.5 rounded-3 btn-edit-collab" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editCollaborationModal"
                                                data-collab='@json($collab)'>
                                            <i class="bi bi-pencil-square"></i> تعديل
                                        </button>
                                        <form action="{{ route('collaborations.destroy', $collab) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا التعاون؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger py-1 px-2 rounded-3" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="d-flex justify-content-center mt-4">
            {{ $collaborations->links() }}
        </div>
    @else
        <div class="card border-0 shadow-sm rounded-4 p-5 text-center">
            <div class="mb-3 text-muted">
                <i class="bi bi-folder-x fs-1"></i>
            </div>
            <h5 class="fw-bold">لا توجد سجلات شراكات أو تعاونات مطابقة</h5>
            <p class="text-muted small">يمكنك إضافة شراكة جديدة أو تعديل شروط البحث والتصفية أعلاه.</p>
            <div>
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createCollaborationModal">
                    <i class="bi bi-plus-circle me-1"></i> إضافة أول تعاون الآن
                </button>
            </div>
        </div>
    @endif

</div>

{{-- Create Modal --}}
<div class="modal fade" id="createCollaborationModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form action="{{ route('collaborations.store') }}" method="POST">
                @csrf
                <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill text-primary"></i>
                        <span>إضافة تعاون / شراكة جديدة</span>
                    </h5>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">اسم الجهة / الدكتور / المركز <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required placeholder="مثال: معمل البرج، د/ فلان الفلاني">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">التصنيف <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">التخصص / المجال</label>
                            <input type="text" name="specialty" class="form-control" placeholder="مثال: باطنة وأورام، تحاليل طبية، صيدلة...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">حالة التعاقد</label>
                            <select name="status" class="form-select">
                                <option value="active" selected>نشط وساري الآن</option>
                                <option value="extended">ممدود وساري</option>
                                <option value="pending">قيد التفاوض والاتفاق</option>
                                <option value="expired">منتهي الصلاحية</option>
                            </select>
                        </div>

                        {{-- Discounts --}}
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-primary">نسبة خصم المتطوعين</label>
                            <input type="text" name="discount_volunteers" class="form-control" placeholder="مثال: خصم 25% على التحاليل">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-primary">آلية تفعيل خصم المتطوعين</label>
                            <input type="text" name="activation_volunteers" class="form-control" placeholder="مثال: إظهار كارنيه التطوع">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-danger">نسبة خصم المستفيدين والحالات</label>
                            <input type="text" name="discount_beneficiaries" class="form-control" placeholder="مثال: خصم 40% أو كشف مجاني">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-danger">آلية تفعيل خصم المستفيدين</label>
                            <input type="text" name="activation_beneficiaries" class="form-control" placeholder="مثال: خطاب تحويل مختوم من المؤسسة">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">تفاصيل الخدمات المقدمة</label>
                            <textarea name="service_provided" rows="2" class="form-control" placeholder="اكتب نبذة عن الخدمات المشمولة في التعاون..."></textarea>
                        </div>

                        {{-- Contacts --}}
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">الشخص المسؤول / جهة الاتصال</label>
                            <input type="text" name="contact_person" class="form-control" placeholder="الاسم ثلاثي">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">صفة المسؤول</label>
                            <input type="text" name="contact_person_role" class="form-control" placeholder="مثال: مدير الفرع، مسؤول العلاقات">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">رقم الهاتف الأساسي</label>
                            <input type="text" name="phone" class="form-control font-monospace" placeholder="01xxxxxxxxx">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">هاتف إضافي / أرضي</label>
                            <input type="text" name="phone_alt" class="form-control font-monospace" placeholder="هاتف بديل">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">المدينة / المركز</label>
                            <input type="text" name="city" class="form-control" placeholder="مثال: كفر الشيخ">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">التقييم (1 إلى 5)</label>
                            <select name="rating" class="form-select">
                                <option value="5" selected>★★★★★ (5 نجوم)</option>
                                <option value="4">★★★★☆ (4 نجوم)</option>
                                <option value="3">★★★☆☆ (3 نجوم)</option>
                                <option value="2">★★☆☆☆ (نجمتان)</option>
                                <option value="1">★☆☆☆☆ (نجمة واحدة)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">العنوان التفصيلي ومواعيد العمل</label>
                            <input type="text" name="address" class="form-control" placeholder="الشارع، المعلم المميز، مواعيد العمل...">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">رابط الشيت أو الوثيقة (اختياري)</label>
                            <input type="url" name="doc_link_url" class="form-control font-monospace" placeholder="https://docs.google.com/...">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">عنوان الرابط</label>
                            <input type="text" name="doc_link_title" class="form-control" placeholder="مثال: شيت الأسعار المخفضة">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">ملاحظات إضافية</label>
                            <textarea name="notes" rows="2" class="form-control" placeholder="أي شروط أو بنود إضافية..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ التعاون</button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- Edit Modal --}}
<div class="modal fade" id="editCollaborationModal" tabindex="-1" aria-hidden="true" dir="rtl">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <form id="editCollaborationForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-header border-bottom px-4 py-3 bg-light rounded-top-4">
                    <h5 class="modal-title fw-bold text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-primary"></i>
                        <span>تعديل بيانات التعاون</span>
                    </h5>
                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">اسم الجهة / المركز <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="edit_name" class="form-control" required>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">التصنيف <span class="text-danger">*</span></label>
                            <select name="category" id="edit_category" class="form-select" required>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">التخصص / المجال</label>
                            <input type="text" name="specialty" id="edit_specialty" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold">حالة التعاقد</label>
                            <select name="status" id="edit_status" class="form-select">
                                <option value="active">نشط وساري الآن</option>
                                <option value="extended">ممدود وساري</option>
                                <option value="pending">قيد التفاوض والاتفاق</option>
                                <option value="expired">منتهي الصلاحية</option>
                            </select>
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-primary">نسبة خصم المتطوعين</label>
                            <input type="text" name="discount_volunteers" id="edit_discount_volunteers" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-primary">آلية تفعيل خصم المتطوعين</label>
                            <input type="text" name="activation_volunteers" id="edit_activation_volunteers" class="form-control">
                        </div>

                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-danger">نسبة خصم المستفيدين والحالات</label>
                            <input type="text" name="discount_beneficiaries" id="edit_discount_beneficiaries" class="form-control">
                        </div>
                        <div class="col-12 col-md-6">
                            <label class="form-label small fw-bold text-danger">آلية تفعيل خصم المستفيدين</label>
                            <input type="text" name="activation_beneficiaries" id="edit_activation_beneficiaries" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">تفاصيل الخدمات المقدمة</label>
                            <textarea name="service_provided" id="edit_service_provided" rows="2" class="form-control"></textarea>
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">الشخص المسؤول</label>
                            <input type="text" name="contact_person" id="edit_contact_person" class="form-control">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">صفة المسؤول</label>
                            <input type="text" name="contact_person_role" id="edit_contact_person_role" class="form-control">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">رقم الهاتف</label>
                            <input type="text" name="phone" id="edit_phone" class="form-control font-monospace">
                        </div>

                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">هاتف إضافي</label>
                            <input type="text" name="phone_alt" id="edit_phone_alt" class="form-control font-monospace">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">المدينة / المركز</label>
                            <input type="text" name="city" id="edit_city" class="form-control">
                        </div>
                        <div class="col-12 col-md-4">
                            <label class="form-label small fw-bold">التقييم</label>
                            <select name="rating" id="edit_rating" class="form-select">
                                <option value="5">★★★★★ (5 نجوم)</option>
                                <option value="4">★★★★☆ (4 نجوم)</option>
                                <option value="3">★★★☆☆ (3 نجوم)</option>
                                <option value="2">★★☆☆☆ (نجمتان)</option>
                                <option value="1">★☆☆☆☆ (نجمة واحدة)</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">العنوان ومواعيد العمل</label>
                            <input type="text" name="address" id="edit_address" class="form-control">
                        </div>

                        <div class="col-12">
                            <label class="form-label small fw-bold">ملاحظات</label>
                            <textarea name="notes" id="edit_notes" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light px-4 py-3 rounded-bottom-4">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary px-4 fw-bold">حفظ التعديلات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const editBtns = document.querySelectorAll('.btn-edit-collab');
    const form = document.getElementById('editCollaborationForm');

    editBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const data = JSON.parse(this.dataset.collab);
            form.action = `/collaborations/${data.id || data.hashed_id}`;
            document.getElementById('edit_name').value = data.name || '';
            document.getElementById('edit_category').value = data.category || '';
            document.getElementById('edit_specialty').value = data.specialty || '';
            document.getElementById('edit_status').value = data.status || 'active';
            document.getElementById('edit_discount_volunteers').value = data.discount_volunteers || '';
            document.getElementById('edit_activation_volunteers').value = data.activation_volunteers || '';
            document.getElementById('edit_discount_beneficiaries').value = data.discount_beneficiaries || '';
            document.getElementById('edit_activation_beneficiaries').value = data.activation_beneficiaries || '';
            document.getElementById('edit_service_provided').value = data.service_provided || '';
            document.getElementById('edit_contact_person').value = data.contact_person || '';
            document.getElementById('edit_contact_person_role').value = data.contact_person_role || '';
            document.getElementById('edit_phone').value = data.phone || '';
            document.getElementById('edit_phone_alt').value = data.phone_alt || '';
            document.getElementById('edit_city').value = data.city || '';
            document.getElementById('edit_rating').value = data.rating || '5';
            document.getElementById('edit_address').value = data.address || '';
            document.getElementById('edit_notes').value = data.notes || '';
        });
    });
});
</script>
@endsection
