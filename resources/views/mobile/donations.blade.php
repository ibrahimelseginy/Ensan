@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

@php
    $totalAmount = $donations->sum('donation_amount');
    $completedAmount = $donations->where('status', 'completed')->sum('donation_amount');
    $donationsCount = $donations->count();
    $pendingCount = $donations->where('status', 'pending')->count();
    $completedCount = $donations->where('status', 'completed')->count();
    $collectionRate = $totalAmount > 0 ? round(($completedAmount / $totalAmount) * 100) : 0;
    
    $targetsBreakdown = $donations->groupBy('donation_for')->map(function($items) {
        return [
            'count' => $items->count(),
            'amount' => $items->sum('donation_amount')
        ];
    })->sortByDesc('count')->take(3);
@endphp

<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4 animate-reveal-down px-2">
        <div>
            <h1 class="h2 fw-800 text-stats-main mb-1">سجلات التبرع <span class="text-primary">(الموبايل)</span></h1>
            <p class="text-muted-theme small mb-0">متابعة عمليات التبرع الواردة من تطبيق الموبايل والحالات الخاصة بها</p>
        </div>
        <div class="glass-badge-theme px-4 py-2 d-none d-md-flex align-items-center gap-2">
            <span class="pulse-indicator"></span>
            <span class="small fw-bold">نظام الرصد المباشر للنشاط</span>
        </div>
    </div>

    <!-- Modern Stats Dashboard -->
    <div class="row g-4 mb-5 animate-reveal-down" style="animation-delay: 0.1s">
        <!-- Highlights: Main Stats Card -->
        <div class="col-xl-4 col-lg-5">
            <div class="dashboard-main-card h-100 overflow-hidden position-relative shadow-lg">
                <div class="card-glow"></div>
                <div class="p-4 position-relative z-1 d-flex flex-column h-100">
                    <div class="d-flex justify-content-between align-items-start mb-4">
                        <div class="icon-box-premium">
                            <i class="bi bi-wallet2 fs-4 text-stats-main"></i>
                        </div>
                        <span class="badge bg-white bg-opacity-20 text-stats-main rounded-pill px-3 py-1 x-small">إجمالي المبالغ</span>
                    </div>
                    <div class="mb-4">
                        <h6 class="text-stats-main text-opacity-75 mb-1 small fw-bold">إجمالي المبلغ المحصل</h6>
                        <h2 class="text-stats-main fw-800 font-outfit mb-0" style="font-size: 2.8rem">{{ number_format($totalAmount, 0) }} <span class="fs-6 opacity-75">ج.م</span></h2>
                    </div>
                    <div class="mt-auto">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <span class="text-stats-main small fw-bold op-75">اكتمال التحصيل</span>
                            <div class="percentage-pill bg-white bg-opacity-20 px-3 py-1 rounded-pill blur-xs">
                                <span class="text-stats-main fw-800 font-outfit" style="font-size: 1.1rem">{{ $collectionRate }}%</span>
                            </div>
                        </div>
                        <div class="progress-container-elite shadow-sm">
                            <div class="progress bg-white bg-opacity-10" style="height: 12px; border-radius: 20px; padding: 2px;">
                                <div class="progress-bar emerald-glow-bar" role="progressbar" 
                                     style="width: {{ $collectionRate }}%; border-radius: 20px;" 
                                     aria-valuenow="{{ $collectionRate }}" aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Unified Analytics Card -->
        <div class="col-xl-8 col-lg-7">
            <div class="dashboard-metric-card h-100 overflow-hidden shadow-sm">
                <div class="row g-0 h-100">
                    <!-- Metrics Section -->
                    <div class="col-md-7 p-4 border-end-theme">
                        <h6 class="text-stats-main fw-800 mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-activity text-primary"></i> مؤشرات الأداء الحالية
                        </h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-stats-inner-item border border-light-subtle shadow-xs">
                                    <div class="metric-icon bg-primary bg-opacity-10 text-primary mb-2">
                                        <i class="bi bi-arrow-repeat"></i>
                                    </div>
                                    <span class="x-small text-muted-theme fw-bold d-block mb-1">إجمالي العمليات</span>
                                    <h4 class="fw-800 font-outfit mb-0 text-stats-main">{{ $donationsCount }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-stats-inner-item border border-light-subtle shadow-xs">
                                    <div class="metric-icon bg-warning bg-opacity-10 text-warning mb-2">
                                        <i class="bi bi-hourglass-split"></i>
                                    </div>
                                    <span class="x-small text-muted-theme fw-bold d-block mb-1">بانتظار التأكيد</span>
                                    <h4 class="fw-800 font-outfit mb-0 text-stats-main">{{ $pendingCount }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-stats-inner-item border border-light-subtle shadow-xs">
                                    <div class="metric-icon bg-success bg-opacity-10 text-success mb-2">
                                        <i class="bi bi-check2-circle"></i>
                                    </div>
                                    <span class="x-small text-muted-theme fw-bold d-block mb-1">تم التأكيد</span>
                                    <h4 class="fw-800 font-outfit mb-0 text-stats-main">{{ $completedCount }}</h4>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-4 bg-stats-inner-item border border-light-subtle shadow-xs">
                                    <div class="metric-icon bg-info bg-opacity-10 text-info mb-2">
                                        <i class="bi bi-lightning"></i>
                                    </div>
                                    <span class="x-small text-muted-theme fw-bold d-block mb-1">نسبة الإنجاز</span>
                                    <h4 class="fw-800 font-outfit mb-0 text-stats-main">{{ $donationsCount > 0 ? round(($completedCount / $donationsCount) * 100) : 0 }}%</h4>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Breakdown Section -->
                    <div class="col-md-5 p-4 bg-stats-inner-item-light">
                        <h6 class="text-stats-main fw-800 mb-4 d-flex align-items-center gap-2">
                            <i class="bi bi-pie-chart text-primary"></i> توزيع التبرعات حسب الجهة
                        </h6>
                        <div class="targets-list mt-3">
                            @forelse($targetsBreakdown as $target => $data)
                                <div class="target-item mb-4">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="small fw-bold text-stats-main text-truncate pe-2" style="max-width: 65%">{{ $target }}</span>
                                        <span class="x-small text-primary fw-bold">{{ number_format($data['amount'], 0) }} ج.م</span>
                                    </div>
                                    <div class="progress bg-white bg-opacity-50" style="height: 6px; border-radius: 4px;">
                                        @php $percent = $donationsCount > 0 ? ($data['count'] / $donationsCount) * 100 : 0; @endphp
                                        <div class="progress-bar bg-primary rounded-pill shadow-xs" style="width: {{ $percent }}%"></div>
                                    </div>
                                    <div class="mt-1">
                                        <span class="x-small text-muted-theme fw-bold">{{ $data['count'] }} عملية تبرع</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5">
                                    <i class="bi bi-inbox text-muted-theme opacity-30 fs-1 mb-3"></i>
                                    <p class="text-muted-theme small mb-0">لا توجد بيانات توزيع حاليا</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search & Filters Section -->
    <div class="row mb-5 animate-reveal-down" style="animation-delay: 0.2s">
        <div class="col-lg-12">
            <div class="search-container-premium p-1 pe-4 glass-card-elite d-flex align-items-center gap-3">
                <i class="bi bi-search text-primary fs-5"></i>
                <input type="text" id="donationSearch" class="form-control border-0 bg-transparent py-3 shadow-none text-stats-main" placeholder="البحث عن متبرع بالاسم أو رقم الهاتف...">
                <div class="d-none d-md-flex align-items-center gap-2">
                    <span class="badge-filter active" data-filter="all">
                        الكل <span class="badge bg-white bg-opacity-10 ms-2 font-outfit">{{ $donationsCount }}</span>
                    </span>
                    <span class="badge-filter" data-filter="pending">
                        بانتظار التأكيد <span class="badge bg-white bg-opacity-10 ms-2 font-outfit">{{ $pendingCount }}</span>
                    </span>
                    <span class="badge-filter" data-filter="completed">
                        تم التبرع <span class="badge bg-white bg-opacity-10 ms-2 font-outfit">{{ $completedCount }}</span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Donation Cards Grid -->
    <div class="row g-4" id="donationsGrid">
        @forelse($donations as $donation)
        <div class="col-xl-4 col-lg-6 col-md-6 donation-item animate-up" 
             data-name="{{ $donation->donor_name }}" 
             data-phone="{{ $donation->donor_phone }}" 
             data-status="{{ $donation->status }}"
             style="animation-delay: {{ $loop->index * 0.05 }}s">
            <div class="elite-donation-card">
                <div class="card-glow-overlay"></div>
                
                <!-- Card Header: Amount & Status -->
                <div class="elite-card-header d-flex justify-content-between align-items-start mb-4">
                    <div class="amount-glass-pill shadow-sm">
                        <span class="amount-value font-outfit">{{ number_format($donation->donation_amount, 0) }}</span>
                        <span class="amount-currency">ج.م</span>
                    </div>
                    <div class="status-marker {{ $donation->status == 'pending' ? 'bg-warning' : ($donation->status == 'completed' ? 'bg-success' : 'bg-danger') }} shadow-sm" title="{{ $donation->status }}"></div>
                </div>

                <!-- Card Body: User Info -->
                <div class="elite-card-body flex-grow-1">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="user-avatar-initial bg-primary bg-opacity-10 text-primary">
                            {{ mb_substr($donation->donor_name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h5 class="fw-800 text-stats-main mb-0 text-truncate" title="{{ $donation->donor_name }}">{{ $donation->donor_name }}</h5>
                            <p class="small text-muted-theme mb-0 font-outfit fw-bold op-75">{{ $donation->donor_phone }}</p>
                        </div>
                    </div>

                    <div class="info-row-elite mb-2">
                        <div class="info-item-elite">
                            <i class="bi bi-bullseye text-danger"></i>
                            <span class="text-truncate" title="{{ $donation->donation_for }}">{{ $donation->donation_for }}</span>
                        </div>
                    </div>
                    
                    <div class="info-row-elite">
                        <div class="info-item-elite">
                            @if(stripos($donation->payment_method, 'فودافون') !== false)
                                <i class="bi bi-phone text-danger"></i>
                            @elseif(stripos($donation->payment_method, 'انستا') !== false)
                                <i class="bi bi-lightning-charge text-info"></i>
                            @else
                                <i class="bi bi-credit-card text-primary"></i>
                            @endif
                            <span>{{ $donation->payment_method }}</span>
                        </div>
                        <div class="info-item-elite">
                            <i class="bi bi-clock-history text-muted-theme"></i>
                            <span class="small op-75">{{ $donation->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card Footer: Actions -->
                <div class="elite-card-footer mt-4 pt-3 border-top border-light-subtle d-flex gap-2">
                    <button class="btn btn-elite-primary flex-grow-1 d-flex align-items-center justify-content-center gap-2" data-bs-toggle="modal" data-bs-target="#modal{{ $donation->id }}">
                        <i class="bi bi-eye"></i> التفاصيل
                    </button>
                    @if($donation->receipt_path)
                    <a href="{{ $donation->image_url }}" target="_blank" class="btn btn-elite-outline-light px-3" title="تحميل الوصل">
                        <i class="bi bi-download"></i>
                    </a>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 animate-up">
            <div class="glass-card-elite text-center py-5 shadow-sm border-dashed border-2">
                <div class="d-inline-flex p-4 rounded-circle bg-primary bg-opacity-10 mb-4 animate-bounce">
                    <i class="bi bi-inbox display-4 text-primary"></i>
                </div>
                <h3 class="fw-800 text-stats-main mb-2">لا توجد سجلات تبرع</h3>
                <p class="text-muted-theme small mb-4">لم نتمكن من العثور على أي عمليات تبرع مطابقة في النظام حالياً.</p>
                <button onclick="window.location.reload()" class="btn btn-elite-primary px-5">إعادة تحميل الصفحة</button>
            </div>
        </div>
        @endforelse
    </div>
</div>

@foreach($donations as $donation)
<div class="modal fade" id="modal{{ $donation->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 32px; overflow: hidden;">
            <div class="modal-header border-0 bg-stats-header px-4 py-4 border-bottom border-light-subtle d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center gap-3">
                    <div class="icon-box-premium bg-primary text-stats-main shadow-sm">
                        <i class="bi bi-shield-check fs-4"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-800 text-stats-main mb-0">مراجعة عملية التبرع</h5>
                        <p class="small text-muted-theme mb-0 op-75">رقم المعاملة: #{{ $donation->id }} • {{ $donation->created_at->format('Y/m/d h:i A') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0 bg-stats-card-main">
                <div class="row g-0">
                    <!-- Right Section: Donor Details -->
                    <div class="col-md-7 p-4 p-lg-5">
                        <div class="section-elite mb-5">
                            <h6 class="text-primary fw-800 small text-uppercase letter-spacing-1 mb-4">بيانات المتبرع</h6>
                            <div class="row g-4">
                                <div class="col-12">
                                    <div class="info-group-premium p-3 rounded-4 bg-stats-inner-item border border-light-subtle">
                                        <label class="x-small text-muted-theme fw-bold mb-1 d-block">إسم المتبرع الكامل</label>
                                        <div class="text-stats-main fw-bold fs-5">{{ $donation->donor_name }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-group-premium p-3 rounded-4 bg-stats-inner-item border border-light-subtle">
                                        <label class="x-small text-muted-theme fw-bold mb-1 d-block">رقم الهاتف</label>
                                        <div class="font-outfit text-primary fw-bold fs-5">{{ $donation->donor_phone }}</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-group-premium p-3 rounded-4 bg-stats-inner-item border border-light-subtle">
                                        <label class="x-small text-muted-theme fw-bold mb-1 d-block">الجهة المتبرع لها</label>
                                        <div class="text-stats-main fw-bold">{{ $donation->donation_for }}</div>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="info-group-premium p-3 rounded-4 bg-stats-inner-item border border-light-subtle">
                                        <label class="x-small text-muted-theme fw-bold mb-1 d-block">ملاحظات المتبرع</label>
                                        <div class="text-stats-main small opacity-75">{{ $donation->notes ?? 'لا توجد ملاحظات.' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Timeline Process Section -->
                        <div class="section-elite mb-5">
                            <h6 class="text-primary fw-800 small text-uppercase letter-spacing-1 mb-4">مسار العملية (Timeline)</h6>
                            <div class="timeline-elite">
                                <div class="timeline-item-elite active">
                                    <div class="timeline-point-elite"></div>
                                    <div class="timeline-content-elite">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold text-stats-main small">إنشاء الطلب من الموبايل</span>
                                            <span class="x-small text-muted">{{ $donation->created_at->format('h:i A') }}</span>
                                        </div>
                                        <p class="x-small text-muted mb-0">تم استلام بيانات التبرع بنجاح.</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item-elite {{ $donation->status != 'pending' ? 'active' : '' }}">
                                    <div class="timeline-point-elite {{ $donation->status == 'pending' ? 'pulse-pending' : '' }}"></div>
                                    <div class="timeline-content-elite">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold text-stats-main small">مراجعة الإدارة</span>
                                            <span class="x-small text-muted">@if($donation->status != 'pending') {{ $donation->updated_at->format('h:i A') }} @else قيد الانتظار @endif</span>
                                        </div>
                                        <p class="x-small text-muted mb-0">
                                            @if($donation->status == 'pending')
                                                جاري مراجعة الوصل والبيانات من قبل المسؤول.
                                            @elseif($donation->status == 'completed')
                                                تمت مراجعة الوصل وتأكيده بنجاح.
                                            @else
                                                تم رفض الطلب أو إلغاؤه لمشكلة في البيانات.
                                            @endif
                                        </p>
                                    </div>
                                </div>

                                @if($donation->status == 'completed')
                                <div class="timeline-item-elite active success">
                                    <div class="timeline-point-elite bg-success"></div>
                                    <div class="timeline-content-elite">
                                        <div class="d-flex justify-content-between">
                                            <span class="fw-bold text-success small">اكتمال التحصيل</span>
                                            <span class="x-small text-success">{{ $donation->updated_at->format('Y/m/d') }}</span>
                                        </div>
                                        <p class="x-small text-success mb-0 opacity-75">تمت إضافة المبلغ لميزانية المشروع.</p>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <div class="section-elite">
                            <h6 class="text-primary fw-800 small text-uppercase letter-spacing-1 mb-4">تفاصيل الدفع</h6>
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="info-group-premium p-3 rounded-4 bg-stats-inner-item border border-light-subtle">
                                        <label class="x-small text-muted-theme fw-bold mb-1 d-block">المبلغ المحول</label>
                                        <div class="text-stats-main fw-800 fs-4 font-outfit">{{ number_format($donation->donation_amount, 0) }} ج.م</div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="info-group-premium p-3 rounded-4 bg-stats-inner-item border border-light-subtle h-100 d-flex flex-column justify-content-center">
                                        <label class="x-small text-muted-theme fw-bold mb-1 d-block">طريقة الدفع</label>
                                        <div class="text-stats-main fw-bold">{{ $donation->payment_method }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Left Section: Receipt & Actions -->
                    <div class="col-md-5 p-4 bg-stats-inner-item border-start border-light-subtle">
                        <div class="receipt-section h-100 d-flex flex-column">
                            <h6 class="text-stats-main fw-800 small text-uppercase mb-4">صورة إثبات التحويل</h6>
                            
                            @if($donation->receipt_path)
                                <div class="receipt-viewer-elite flex-grow-1 rounded-4 overflow-hidden position-relative bg-white shadow-sm mb-4">
                                    <img src="{{ $donation->image_url }}" class="img-fluid w-100 h-100" style="object-fit: cover; cursor: pointer" data-bs-toggle="tooltip" title="اضغط للتكبير" onclick="window.open(this.src)">
                                    <div class="receipt-zoom-hint position-absolute bottom-0 start-0 w-100 p-2 text-center text-stats-main bg-dark bg-opacity-50 small">
                                        <i class="bi bi-zoom-in me-1"></i> عرض الحجم الكامل
                                    </div>
                                </div>
                            @else
                                <div class="no-receipt-elite flex-grow-1 rounded-4 d-flex flex-column align-items-center justify-content-center border-dashed border-2 p-4 text-center mb-4">
                                    <i class="bi bi-camera-off fs-1 text-muted-theme opacity-30 mb-2"></i>
                                    <span class="small text-muted-theme fw-bold">لم يتم رفع وصل</span>
                                </div>
                            @endif

                            <div class="admin-action-card p-4 rounded-4 bg-white shadow-sm border border-light-subtle">
                                <form action="{{ route('mobile.donations.update', $donation->id) }}" method="POST">
                                    @csrf
                                    <label class="x-small text-muted-theme fw-bold mb-3 d-block text-center uppercase">اتخاذ إجراء بشأن العملية</label>
                                    <div class="status-buttons-elite d-flex flex-column gap-2">
                                        <button type="submit" name="status" value="completed" class="btn btn-success rounded-3 fw-bold py-2 {{ $donation->status == 'completed' ? 'opacity-50' : '' }}">
                                            <i class="bi bi-check2-all me-2"></i> تم التحصيل بنجاح
                                        </button>
                                        <div class="d-flex gap-2">
                                            <button type="submit" name="status" value="pending" class="btn btn-outline-warning rounded-3 fw-bold flex-grow-1 py-2 {{ $donation->status == 'pending' ? 'active' : '' }}">
                                                انتظار
                                            </button>
                                            <button type="submit" name="status" value="failed" class="btn btn-outline-danger rounded-3 fw-bold flex-grow-1 py-2 {{ $donation->status == 'failed' ? 'active' : '' }}">
                                                إلغاء
                                            </button>
                                        </div>
                                    </div>
                                </form>
                                <form id="del-form-{{ $donation->id }}" action="{{ route('mobile.donations.destroy', $donation->id) }}" method="POST" class="mt-3">
                                    @csrf @method('DELETE')
                                    <button type="button" class="btn btn-link text-danger w-100 small fw-bold text-decoration-none" onclick="if(confirm('هل أنت متأكد من حذف هذا السجل نهائياً؟')) this.parentElement.submit()">
                                        <i class="bi bi-trash3 me-1"></i> حذف السجل نهائياً
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endforeach

<style>
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-800 { font-weight: 800; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Dashboard Styling */
    .dashboard-main-card {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        border-radius: 28px;
        box-shadow: 0 15px 35px rgba(37, 99, 235, 0.2);
        color: white;
    }
    body.theme-dark .dashboard-main-card {
        background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
    }
    .card-glow {
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 70%);
        transform: rotate(30deg);
        pointer-events: none;
    }
    .icon-box-premium {
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        backdrop-filter: blur(10px);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    /* Progress Bar Improvements */
    .emerald-glow-bar {
        background: linear-gradient(90deg, #10b981 0%, #34d399 100%);
        box-shadow: 0 0 15px rgba(16, 185, 129, 0.5);
        position: relative;
        overflow: hidden;
    }
    .emerald-glow-bar::after {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: progress-shine 2s infinite;
    }
    @keyframes progress-shine {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    .blur-xs { backdrop-filter: blur(5px); }

    .search-container-premium {
        background: var(--ws-bg-card);
        border: 1px solid var(--ws-border);
        border-radius: 100px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 0 4px 20px rgba(0,0,0,0.03);
    }
    .search-container-premium:focus-within {
        border-color: var(--primary);
        box-shadow: 0 8px 30px rgba(5, 150, 105, 0.1);
        transform: translateY(-2px);
    }

    .badge-filter {
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid transparent;
    }
    .badge-filter:hover { background: var(--gray-100); }
    .badge-filter.active { background: var(--primary); color: white; }

    /* Elite Card Design */
    .elite-donation-card {
        background: var(--ws-bg-card);
        border: 1px solid var(--ws-border);
        border-radius: 32px;
        padding: 24px;
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        box-shadow: 0 10px 30px rgba(0,0,0,0.02);
    }
    .elite-donation-card:hover {
        transform: translateY(-12px) scale(1.02);
        box-shadow: 0 20px 60px rgba(0,0,0,0.08);
        border-color: var(--primary);
    }
    .card-glow-overlay {
        position: absolute;
        top: 0; left: 0; width: 100%; height: 100%;
        background: radial-gradient(circle at top right, rgba(5, 150, 105, 0.05), transparent 70%);
        pointer-events: none;
    }

    .amount-glass-pill {
        background: rgba(5, 150, 105, 0.08);
        padding: 10px 20px;
        border-radius: 20px;
        display: flex;
        align-items: baseline;
        gap: 6px;
        border: 1px solid rgba(5, 150, 105, 0.1);
    }
    .amount-value { font-size: 1.6rem; font-weight: 800; color: var(--primary); }
    .amount-currency { font-size: 0.75rem; font-weight: 700; color: var(--primary); opacity: 0.8; }

    .status-marker {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        border: 3px solid white;
    }
    body.theme-dark .status-marker { border-color: #1a202c; }

    .user-avatar-initial {
        width: 48px;
        height: 48px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.4rem;
        font-weight: 800;
    }

    .info-row-elite { display: flex; align-items: center; gap: 15px; }
    .info-item-elite {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.88rem;
        font-weight: 600;
        color: var(--text-stats-main);
        background: rgba(0,0,0,0.02);
        padding: 6px 14px;
        border-radius: 12px;
        border: 1px solid rgba(0,0,0,0.03);
    }
    body.theme-dark .info-item-elite { background: rgba(255,255,255,0.03); border-color: rgba(255,255,255,0.05); }

    .btn-elite-primary {
        background: var(--primary);
        color: white !important;
        border: none;
        border-radius: 18px;
        padding: 14px 20px;
        font-weight: 700;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(5, 150, 105, 0.2);
    }
    .btn-elite-primary:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(5, 150, 105, 0.3);
    }

    .btn-elite-outline-light {
        background: var(--gray-100);
        color: var(--text-muted) !important;
        border: none;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    body.theme-dark .btn-elite-outline-light { background: rgba(255,255,255,0.05); }
    .btn-elite-outline-light:hover {
        background: var(--primary);
        color: white !important;
    }

    .glass-card-elite {
        background: var(--ws-bg-card);
        border: 1px solid var(--ws-border);
        border-radius: 24px;
        transition: all 0.3s ease;
    }

    .badge-filter {
        padding: 8px 18px;
        border-radius: 50px;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--text-muted);
        cursor: pointer;
        transition: all 0.3s ease;
        border: 1px solid var(--ws-border);
        background: var(--ws-bg-card);
    }
    .badge-filter:hover { background: var(--gray-100); border-color: var(--primary); color: var(--primary); }
    .badge-filter.active { background: var(--primary); color: white; border-color: var(--primary); box-shadow: 0 4px 12px rgba(5, 150, 105, 0.2); }

    /* Elite Modal Styles */
    .modal-glass-theme { backdrop-filter: blur(20px); }
    
    .info-group-premium {
        transition: all 0.3s ease;
    }
    .info-group-premium:hover {
        border-color: var(--primary) !important;
        background: rgba(5, 150, 105, 0.02) !important;
    }

    .receipt-viewer-elite {
        box-shadow: 0 15px 35px rgba(0,0,0,0.1);
        border: 4px solid white;
        transition: transform 0.3s ease;
    }
    .receipt-viewer-elite:hover {
        transform: scale(1.02);
    }
    body.theme-dark .receipt-viewer-elite { border-color: #2d3748; }

    .status-buttons-elite .btn {
        transition: all 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    }
    .status-buttons-elite .btn:hover:not(:disabled) {
        transform: translateY(-2px);
    }
    .status-buttons-elite .btn.active {
        box-shadow: inset 0 2px 5px rgba(0,0,0,0.1);
    }

    .letter-spacing-1 { letter-spacing: 1px; }
    
    .animate-bounce {
        animation: bounce 2s infinite;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-10px); }
    }

    /* Timeline Styling */
    .timeline-elite {
        position: relative;
        padding-right: 32px;
        margin-right: 15px;
        border-right: 2px solid var(--ws-border);
    }
    .timeline-item-elite {
        position: relative;
        padding-bottom: 25px;
    }
    .timeline-item-elite:last-child { padding-bottom: 0; }
    
    .timeline-point-elite {
        position: absolute;
        right: -41px;
        top: 0;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--ws-border);
        border: 3px solid var(--ws-bg-page);
        z-index: 1;
        transition: all 0.3s ease;
    }
    .timeline-item-elite.active .timeline-point-elite {
        background: var(--primary);
        box-shadow: 0 0 0 4px rgba(5, 150, 105, 0.15);
    }
    .timeline-content-elite {
        background: var(--ws-bg-card);
        padding: 12px 16px;
        border-radius: 12px;
        border: 1px solid var(--ws-border);
    }
    
    .pulse-pending {
        animation: pulse-line 2s infinite;
        background: var(--warning) !important;
    }
    @keyframes pulse-line {
        0% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0.4); }
        70% { box-shadow: 0 0 0 10px rgba(245, 158, 11, 0); }
        100% { box-shadow: 0 0 0 0 rgba(245, 158, 11, 0); }
    }

    /* Search Result Handling */
    .donation-item.hidden-search {
        display: none !important;
    }
    
    /* Responsive Fixes */
    @media (max-width: 991px) {
        .modal-lg { max-width: 95%; margin: 10px auto; }
        .modal-content { border-radius: 20px !important; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('donationSearch');
    const donationItems = document.querySelectorAll('.donation-item');
    const filterBadges = document.querySelectorAll('.badge-filter');

    // Filter Badges Logic
    filterBadges.forEach(badge => {
        badge.addEventListener('click', function() {
            filterBadges.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            const filterValue = this.getAttribute('data-filter');
            const query = searchInput.value.toLowerCase().trim();
            
            donationItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                const phone = item.getAttribute('data-phone').toLowerCase();
                const status = item.getAttribute('data-status');
                
                const matchesSearch = name.includes(query) || phone.includes(query);
                const matchesFilter = filterValue === 'all' || status === filterValue;
                
                if (matchesSearch && matchesFilter) {
                    item.style.display = 'block';
                    item.classList.add('animate-up');
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });

    // Integrated search with filter support
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const activeBadge = document.querySelector('.badge-filter.active');
            const activeFilter = activeBadge ? activeBadge.getAttribute('data-filter') : 'all';
            const query = this.value.toLowerCase().trim();
            
            donationItems.forEach(item => {
                const name = item.getAttribute('data-name').toLowerCase();
                const phone = item.getAttribute('data-phone').toLowerCase();
                const status = item.getAttribute('data-status');
                
                const matchesSearch = name.includes(query) || phone.includes(query);
                const matchesFilter = activeFilter === 'all' || status === activeFilter;
                
                if (matchesSearch && matchesFilter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection



