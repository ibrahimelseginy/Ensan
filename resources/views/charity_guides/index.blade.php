@extends('layouts.app')

@section('title', 'أدلة العمل الخيري والمنسقين الميدانيين')

@section('content')
<div class="container-fluid px-3 px-md-4 py-4" dir="rtl">

    {{-- Page Header --}}
    <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <div class="p-2 rounded-3 text-white" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="bi bi-geo-alt-fill fs-4"></i>
                </div>
                <div>
                    <h3 class="fw-bold mb-0 text-slate-800">أدلة العمل الخيري والمنسقين الميدانيين</h3>
                    <p class="text-muted small mb-0">شبكة أدلة القرى والمراكز، أرقام التواصل المباشر، وتفاصيل وملاحظات تنظيم المعارض الميدانية</p>
                </div>
            </div>
        </div>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <a href="{{ route('collaborations.index') }}" class="btn btn-outline-secondary d-inline-flex align-items-center gap-1 shadow-sm px-3 py-2">
                <i class="bi bi-building me-1"></i>
                <span>الشركات والجهات الشريكة</span>
            </a>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2 shadow-sm px-3 py-2" data-bs-toggle="modal" data-bs-target="#createGuideModal">
                <i class="bi bi-plus-circle-fill"></i>
                <span>إضافة منسق / دليل جديد</span>
            </button>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.08), rgba(5, 150, 105, 0.15)); border-right: 4px solid #10b981 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">إجمالي المنسقين الميدانيين</div>
                        <div class="fs-3 fw-bold text-success mt-1">{{ number_format($totalGuides) }}</div>
                    </div>
                    <div class="p-3 rounded-circle text-success" style="background: rgba(16, 185, 129, 0.15);">
                        <i class="bi bi-people-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(2, 132, 199, 0.15)); border-right: 4px solid #0ea5e9 !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">المراكز والمدن المغطاة</div>
                        <div class="fs-3 fw-bold text-primary mt-1">{{ number_format($totalCenters) }}</div>
                    </div>
                    <div class="p-3 rounded-circle text-primary" style="background: rgba(14, 165, 233, 0.15);">
                        <i class="bi bi-geo-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-3 h-100" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08), rgba(217, 119, 6, 0.15)); border-right: 4px solid #f59e0b !important;">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-muted small fw-semibold">القرى والنجوع النشطة</div>
                        <div class="fs-3 fw-bold text-warning mt-1">{{ number_format($totalVillages) }}</div>
                    </div>
                    <div class="p-3 rounded-circle text-warning" style="background: rgba(245, 158, 11, 0.15);">
                        <i class="bi bi-houses-fill fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter & Search Form --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('charity-guides.index') }}" class="row g-2 align-items-center">
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control border-start-0" placeholder="بحث بالقرية، اسم المنسق، الهاتف، الملاحظات...">
                    </div>
                </div>
                <div class="col-md-3">
                    <select name="center" class="form-select">
                        <option value="">جميع المراكز والمدن</option>
                        @foreach($centers as $c)
                            <option value="{{ $c }}" @selected(request('center') === $c)>{{ $c }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary px-4 w-100"><i class="bi bi-funnel-fill me-1"></i> تصفية</button>
                    @if(request()->hasAny(['search', 'center']))
                        <a href="{{ route('charity-guides.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x-circle"></i></a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    {{-- Guides Cards & Grid --}}
    <div class="row g-3">
        @forelse($guides as $guide)
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 h-100 p-3" style="background:#ffffff; border-top: 3px solid #10b981 !important;">
                <div class="d-flex align-items-start justify-content-between gap-2 mb-2">
                    <div>
                        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 mb-1 font-monospace">
                            <i class="bi bi-geo-alt me-1"></i> {{ $guide->center }} — {{ $guide->village }}
                        </span>
                        <h5 class="fw-bold text-dark mb-0 mt-1">{{ $guide->coordinator_name }}</h5>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                            <li><button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editGuideModal{{ $guide->id }}"><i class="bi bi-pencil me-2 text-primary"></i> تعديل</button></li>
                            <li>
                                <form method="POST" action="{{ route('charity-guides.destroy', $guide) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا الدليل؟')">
                                    @csrf @method('DELETE')
                                    <button class="dropdown-item text-danger"><i class="bi bi-trash me-2"></i> حذف</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="small text-muted my-2">
                    @if($guide->phone)
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-telephone-fill text-success"></i>
                        <span class="font-monospace fw-semibold dir-ltr text-end">{{ $guide->phone }}</span>
                        @if($guide->clean_phone)
                        <div class="ms-auto d-flex gap-1">
                            <a href="tel:{{ $guide->phone }}" class="btn btn-sm btn-outline-success p-1 px-2" title="اتصال هاتفي"><i class="bi bi-telephone"></i></a>
                            <a href="https://wa.me/{{ $guide->clean_phone }}" target="_blank" class="btn btn-sm btn-success p-1 px-2" title="مراسلة واتساب"><i class="bi bi-whatsapp"></i></a>
                        </div>
                        @endif
                    </div>
                    @endif
                    @if($guide->secondary_phone)
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-telephone text-muted"></i>
                        <span class="font-monospace dir-ltr text-end">{{ $guide->secondary_phone }}</span>
                    </div>
                    @endif
                    @if($guide->exhibition_amount)
                    <div class="d-flex align-items-center gap-2 mt-2 pt-2 border-top">
                        <i class="bi bi-cash-stack text-warning"></i>
                        <span>مبلغ المعرض: <strong>{{ $guide->exhibition_amount }}</strong></span>
                    </div>
                    @endif
                </div>

                @if($guide->notes)
                <div class="mt-2 p-2 rounded-3 small bg-light text-secondary border">
                    <i class="bi bi-chat-left-text me-1 text-primary"></i> {{ $guide->notes }}
                </div>
                @endif
            </div>

            {{-- Edit Modal --}}
            <div class="modal fade" id="editGuideModal{{ $guide->id }}" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <form method="POST" action="{{ route('charity-guides.update', $guide) }}" class="modal-content">
                        @csrf @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title fw-bold">تعديل بيانات الدليل</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label required">المركز / المدينة</label>
                                <input type="text" name="center" value="{{ $guide->center }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">القرية</label>
                                <input type="text" name="village" value="{{ $guide->village }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label required">اسم الدليل / المنسق</label>
                                <input type="text" name="coordinator_name" value="{{ $guide->coordinator_name }}" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">رقم الهاتف</label>
                                <input type="text" name="phone" value="{{ $guide->phone }}" class="form-control font-monospace" placeholder="010XXXXXXXX">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">هاتف بديل</label>
                                <input type="text" name="secondary_phone" value="{{ $guide->secondary_phone }}" class="form-control font-monospace" placeholder="011XXXXXXXX">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">مبلغ المعرض + التاريخ</label>
                                <input type="text" name="exhibition_amount" value="{{ $guide->exhibition_amount }}" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ملاحظات المعرض والدعاية</label>
                                <textarea name="notes" class="form-control" rows="3">{{ $guide->notes }}</textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
                <i class="bi bi-inbox fs-1 mb-2"></i>
                <p class="mb-0">لا توجد بيانات مطابقة للبحث أو التصفية الحالية.</p>
            </div>
        </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-4 d-flex justify-content-center">
        {{ $guides->links() }}
    </div>

</div>

{{-- Create Modal --}}
<div class="modal fade" id="createGuideModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form method="POST" action="{{ route('charity-guides.store') }}" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">إضافة منسق / دليل جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label required">المركز / المدينة</label>
                    <input type="text" name="center" class="form-control" placeholder="مثال: الحامول، بيلا، قلين، الرياض..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label required">القرية</label>
                    <input type="text" name="village" class="form-control" placeholder="مثال: المجاز، قرية 10، الإصلاح..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label required">اسم الدليل / المنسق</label>
                    <input type="text" name="coordinator_name" class="form-control" placeholder="مثال: الشيخ أحمد، أ/ محمد..." required>
                </div>
                <div class="mb-3">
                    <label class="form-label">رقم الهاتف</label>
                    <input type="text" name="phone" class="form-control font-monospace" placeholder="010XXXXXXXX">
                </div>
                <div class="mb-3">
                    <label class="form-label">هاتف بديل</label>
                    <input type="text" name="secondary_phone" class="form-control font-monospace" placeholder="011XXXXXXXX">
                </div>
                <div class="mb-3">
                    <label class="form-label">مبلغ المعرض + التاريخ</label>
                    <input type="text" name="exhibition_amount" class="form-control" placeholder="مثال: 5000 ج.م - يناير 2026">
                </div>
                <div class="mb-3">
                    <label class="form-label">ملاحظات المعرض والدعاية</label>
                    <textarea name="notes" class="form-control" rows="3" placeholder="تأكيدات الدعاية، أفضل مكان للفرش، طبيعة الأهالي..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-success">إضافة الدليل</button>
            </div>
        </form>
    </div>
</div>
@endsection

