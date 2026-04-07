@extends('layouts.app')

@section('content')
<div class="guest-house-mgmt-page">
    {{-- Dynamic Hero Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">إدارة دار الضيافة</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-hospital me-2"></i> إدارة محتوى دار الضيافة
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">إدارة دار الضيافة</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                {{ $settings['gh_hero_subtitle'] ?? 'ملاذ آمن للمرضى ومرافقيهم' }}
            </p>
            <div class="mt-4">
                <button type="button" onclick="document.querySelector('#combinedSubmitForm').submit()" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                    <i class="bi bi-cloud-check-fill me-2"></i> حفظ التغييرات النهائية
                </button>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Area --}}
    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            {{-- Unified Form --}}
            <div class="col-12">
                <form id="combinedSubmitForm" action="{{ route('website.guest-house.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        {{-- Basic Information Card --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm animate-slide-up h-100 overflow-hidden">
                                <div class="p-4 border-bottom bg-light d-flex align-items-center gap-3">
                                    <div class="header-icon-small bg-primary"><i class="bi bi-file-earmark-richtext"></i></div>
                                    <h5 class="fw-bold mb-0">المحتوى التعريفي والوصف</h5>
                                </div>
                                <div class="card-body p-4">
                                    <div class="row g-4">
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">عنوان الصفحة الرئيسي</label>
                                            <input type="text" name="title" class="form-control" value="{{ $page->title }}" required>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label small fw-bold text-muted">العنوان الفرعي (Hero Subtitle)</label>
                                            <input type="text" name="gh_hero_subtitle" class="form-control" value="{{ $settings['gh_hero_subtitle'] ?? 'ملاذ آمن للمرضى ومرافقيهم' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Stats & Gallery Unified Management --}}
                        <div class="col-12">
                            <div class="card border-0 shadow-sm animate-slide-up overflow-hidden">
                                <div class="p-4 border-bottom bg-light d-flex align-items-center gap-3">
                                    <div class="header-icon-small bg-success"><i class="bi bi-columns-gap"></i></div>
                                    <h5 class="fw-bold mb-0">لوحة الإحصائيات ومعرض الصور</h5>
                                </div>
                                <div class="card-body p-4">
                                    {{-- Statistics Section --}}
                                    <h6 class="fw-bold text-dark mb-4 border-start border-3 border-warning ps-2"><i class="bi bi-bar-chart-fill me-2 text-warning"></i> إحصائيات الدار العامة</h6>
                                    <div class="row g-4 mb-5">
                                        @php
                                            $gh_stats = [
                                                ['key' => '1', 'icon' => 'bi-hospital', 'color' => 'primary', 'default_val' => '+50', 'default_lab' => 'سرير'],
                                                ['key' => '2', 'icon' => 'bi-people-fill', 'color' => 'success', 'default_val' => '+3000', 'default_lab' => 'مريض سنوياً'],
                                                ['key' => '3', 'icon' => 'bi-geo-alt-fill', 'color' => 'warning', 'default_val' => '2', 'default_lab' => 'فرع'],
                                                ['key' => '4', 'icon' => 'bi-clock-history', 'color' => 'info', 'default_val' => '24/7', 'default_lab' => 'استقبال'],
                                            ];
                                        @endphp
                                        @foreach($gh_stats as $stat)
                                        <div class="col-md-3 col-6">
                                            <div class="stats-input-sleek p-3 rounded-4 border bg-light text-center h-100 transition-all">
                                                <div class="stat-icon-vibe-small bg-{{ $stat['color'] }} bg-opacity-10 text-{{ $stat['color'] }} mb-3 mx-auto">
                                                    <i class="bi {{ $stat['icon'] }}"></i>
                                                </div>
                                                <input type="text" name="gh_stat{{ $stat['key'] }}_label" class="form-control form-control-sm text-center x-small text-muted border-0 bg-transparent mb-1 p-0 fw-bold" value="{{ $settings['gh_stat'.$stat['key'].'_label'] ?? $stat['default_lab'] }}">
                                                <input type="text" name="gh_stat{{ $stat['key'] }}_value" class="form-control text-center fw-bold border-0 bg-transparent text-dark fs-4 p-0" value="{{ $settings['gh_stat'.$stat['key'].'_value'] ?? $stat['default_val'] }}">
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>

                                    <div class="border-top pt-5 mb-5"></div>
                                    
                                    {{-- Slider Section --}}
                                    <h6 class="fw-bold text-dark mb-4 border-start border-3 border-primary ps-2"><i class="bi bi-images me-2 text-primary"></i> سلايدر الصور (Header Slider)</h6>
                                    <div class="row g-4 mb-3">
                                        @for($i = 1; $i <= 10; $i++)
                                        <div class="col-md-4 col-lg-2-4 col-6">
                                            <div class="gallery-card-sleek ratio ratio-16x9 rounded-4 border bg-light overflow-hidden position-relative group-hover-overlay" style="cursor: pointer;">
                                                @php $sliderPath = $settings["gh_slider_$i"] ?? null; @endphp
                                                @if($sliderPath)
                                                    <img src="{{ asset('storage/' . $sliderPath) }}" class="w-100 h-100 object-fit-cover shadow-sm" id="ghSliderPreview{{ $i }}">
                                                @else
                                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="ghSliderPlaceholder{{ $i }}">
                                                        <i class="bi bi-image fs-3 mb-1"></i>
                                                        <span class="x-small fw-bold">شريحة {{ $i }}</span>
                                                    </div>
                                                    <img src="" class="w-100 h-100 object-fit-cover d-none" id="ghSliderPreview{{ $i }}">
                                                @endif
                                                
                                                <input type="file" name="gh_slider_{{ $i }}" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewGhSlider(this, {{ $i }})">
                                                
                                                @if($sliderPath)
                                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center z-10" 
                                                        style="width: 28px; height: 28px;"
                                                        onclick="event.stopPropagation(); document.getElementById('delete_gh_slider_{{ $i }}').value='1'; this.closest('.gallery-card-sleek').querySelector('.d-none').classList.remove('d-none'); document.getElementById('ghSliderPreview{{ $i }}').classList.add('d-none'); this.remove();">
                                                    <i class="bi bi-trash fs-xs"></i>
                                                </button>
                                                @endif
                                                <input type="hidden" name="delete_gh_slider_{{ $i }}" id="delete_gh_slider_{{ $i }}" value="0">
                                            </div>
                                        </div>
                                        @endfor
                                    </div>
                                    <p class="x-small text-muted mt-2"><i class="bi bi-info-circle me-1"></i> يمكنك إضافة حتى 10 صور للسلايدر المتحرك في صفحة دار الضيافة.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        {{-- Room Bookings Section --}}
        <div class="row mt-5">
            <div class="col-12">
                <div class="card border-0 shadow-sm animate-slide-up overflow-hidden" style="border-radius: 24px;">
                    <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon-small bg-indigo"><i class="bi bi-calendar-check-fill"></i></div>
                            <h5 class="fw-bold mb-0 text-dark">طلبات الحجز الواردة من الموقع</h5>
                        </div>
                        <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold small">{{ $bookings->count() }} طلب</span>
                    </div>
                    <div class="card-body p-4 bg-white">
                        <div class="row g-4">
                            @forelse($bookings as $booking)
                            <div class="col-md-6 col-xl-4 col-xxl-3">
                                <div class="card h-100 border shadow-sm booking-request-card rounded-4 transition-all">
                                    <div class="p-4">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            @php
                                                $statusInfo = [
                                                    'pending' => ['class' => 'bg-warning', 'text' => 'قيد الانتظار'],
                                                    'confirmed' => ['class' => 'bg-success', 'text' => 'مؤكد'],
                                                    'cancelled' => ['class' => 'bg-danger', 'text' => 'ملغي']
                                                ][$booking->status] ?? ['class' => 'bg-secondary', 'text' => $booking->status];
                                            @endphp
                                            <span class="badge {{ $statusInfo['class'] }} bg-opacity-10 text-{{ str_replace('bg-', '', $statusInfo['class']) }} border border-{{ str_replace('bg-', '', $statusInfo['class']) }} border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">{{ $statusInfo['text'] }}</span>
                                            <span class="text-muted x-small">#{{ $booking->id }}</span>
                                        </div>

                                        <div class="d-flex align-items-center gap-3 mb-4 pt-2">
                                            <div class="avatar-soft bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 48px; height: 48px; font-size: 1.2rem;">
                                                {{ mb_substr($booking->name, 0, 1) }}
                                            </div>
                                            <div class="overflow-hidden">
                                                <h6 class="fw-bold text-dark mb-0 text-truncate">{{ $booking->name }}</h6>
                                                <div class="x-small text-muted"><i class="bi bi-phone me-1"></i> {{ $booking->phone }}</div>
                                            </div>
                                        </div>

                                        <div class="bg-light rounded-4 p-3 mb-4">
                                            <div class="row g-2">
                                                <div class="col-6">
                                                    <label class="x-small text-muted d-block fw-bold">نوع الغرفة</label>
                                                    <span class="small text-dark fw-bold">{{ $booking->room_type }}</span>
                                                </div>
                                                <div class="col-6">
                                                    <label class="x-small text-muted d-block fw-bold">المدة</label>
                                                    <span class="small text-dark fw-bold">{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} ليالي</span>
                                                </div>
                                                <div class="col-12 mt-2 pt-2 border-top">
                                                    <label class="x-small text-muted d-block fw-bold">تاريخ الوصول</label>
                                                    <span class="small text-primary fw-bold">{{ $booking->check_in }}</span>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="d-flex gap-2">
                                            <button class="btn btn-outline-light text-primary border flex-grow-1 py-2 rounded-3 x-small fw-bold btn-view-hover" data-bs-toggle="modal" data-bs-target="#viewBooking{{ $booking->id }}">
                                                <i class="bi bi-eye me-1"></i> التفاصيل
                                            </button>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-light text-muted border py-2 px-3 rounded-3" data-bs-toggle="dropdown">
                                                    <i class="bi bi-three-dots"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg p-2 rounded-3">
                                                    <li>
                                                        <form action="{{ route('website.bookings.update', $booking) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="status" value="confirmed">
                                                            <button type="submit" class="dropdown-item text-success fw-bold x-small py-2 rounded-2"><i class="bi bi-check-circle me-2"></i> تأكيد الحجز</button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form action="{{ route('website.bookings.update', $booking) }}" method="POST">
                                                            @csrf @method('PATCH')
                                                            <input type="hidden" name="status" value="cancelled">
                                                            <button type="submit" class="dropdown-item text-danger fw-bold x-small py-2 rounded-2"><i class="bi bi-x-circle me-2"></i> إلغاء الحجز</button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="col-12">
                                <div class="text-center py-5 bg-light bg-opacity-50 border border-dashed rounded-4">
                                    <i class="bi bi-calendar-x fs-1 text-muted opacity-25 d-block mb-3"></i>
                                    <p class="text-muted fw-bold mb-0">لا توجد طلبات حجز مسجلة حالياً</p>
                                </div>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Booking Detail Modals --}}
    @foreach($bookings as $booking)
    <div class="modal fade" id="viewBooking{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-info-circle-fill me-2 text-primary"></i> تفاصيل حالة الحجز #{{ $booking->id }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0 text-start" dir="rtl">
                        <div class="col-md-4 bg-light border-start p-4 text-center">
                            <div class="avatar-large bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 80px; height: 80px; font-size: 2rem; border: 4px solid white;">
                                {{ mb_substr($booking->name, 0, 1) }}
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $booking->name }}</h6>
                            <div class="small text-muted mb-3"><i class="bi bi-telephone ms-1"></i> {{ $booking->phone }}</div>
                            @if($booking->national_id)
                                <div class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1 x-small mb-4 border border-primary border-opacity-10">
                                    {{ $booking->national_id }}
                                </div>
                            @endif

                            <div class="vstack gap-2 mt-2">
                                <div class="p-3 bg-white rounded-3 border text-start">
                                    <div class="x-small text-uppercase text-primary fw-bold mb-2"><i class="bi bi-people ms-1"></i> بيانات المرافق</div>
                                    <div class="fw-bold text-dark small">{{ $booking->companion_name ?? 'لا يوجد مرافق' }}</div>
                                    @if($booking->companion_phone)
                                        <div class="x-small text-muted mt-1"><i class="bi bi-phone ms-1"></i> {{ $booking->companion_phone }}</div>
                                    @endif
                                </div>
                                <div class="p-3 bg-white rounded-3 border text-start">
                                    <div class="x-small text-uppercase text-success fw-bold mb-2"><i class="bi bi-geo-alt ms-1"></i> العنوان</div>
                                    <div class="fw-bold text-dark small">{{ $booking->address ?? 'غير محدد' }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 p-4 bg-white">
                            <h6 class="fw-bold text-dark mb-4 border-bottom pb-2">تفاصيل الإقامة والعلاج</h6>
                            <div class="row g-3 mb-5">
                                <div class="col-6">
                                    <label class="x-small text-muted fw-bold d-block mb-1">نوع الغرفة</label>
                                    <div class="p-2 px-3 bg-light rounded-3 text-dark small border">{{ $booking->room_type }}</div>
                                </div>
                                <div class="col-6">
                                    <label class="x-small text-muted fw-bold d-block mb-1">المدة المتوقعة</label>
                                    <div class="p-2 px-3 bg-light rounded-3 text-dark small border">{{ $booking->expected_duration_arabic ?? '-' }}</div>
                                </div>
                                <div class="col-6">
                                    <label class="x-small text-muted fw-bold d-block mb-1">تاريخ الوصول</label>
                                    <div class="p-2 px-3 bg-light rounded-3 text-primary small border fw-bold">{{ $booking->arrival_date ?? $booking->check_in }}</div>
                                </div>
                                <div class="col-6">
                                    <label class="x-small text-muted fw-bold d-block mb-1">المركز الطبي</label>
                                    <div class="p-2 px-3 bg-light rounded-3 text-dark small border">{{ $booking->medical_center ?? '-' }}</div>
                                </div>
                            </div>

                            <h6 class="fw-bold text-dark mb-3">المستندات المرفقة</h6>
                            <div class="d-flex flex-wrap gap-2 mb-4">
                                @php
                                    $docs = [
                                        ['label' => 'بـطاقة المريض', 'path' => $booking->patient_id_path, 'icon' => 'bi-person-vcard'],
                                        ['label' => 'بـطاقة المرافق', 'path' => $booking->companion_id_path, 'icon' => 'bi-person-badge'],
                                        ['label' => 'تـحويل الـمستشفى', 'path' => $booking->medical_transfer_path, 'icon' => 'bi-hospital'],
                                        ['label' => 'كـارت المتابـعة', 'path' => $booking->followup_card_path, 'icon' => 'bi-card-list'],
                                        ['label' => 'تـقرير الإشعاع', 'path' => $booking->medical_report_path, 'icon' => 'bi-file-earmark-medical'],
                                    ];
                                @endphp

                                @foreach($docs as $doc)
                                    @if($doc['path'])
                                        <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" class="btn btn-outline-light text-primary border d-flex align-items-center gap-2 px-3 py-2 rounded-3 hover-bg-light transition-all">
                                            <i class="bi {{ $doc['icon'] }}"></i>
                                            <span class="x-small fw-bold">{{ $doc['label'] }}</span>
                                        </a>
                                    @endif
                                @endforeach

                                @if(collect($docs)->where('path', '!=', null)->isEmpty())
                                    <div class="p-4 w-100 bg-light rounded-3 text-center border border-dashed text-muted x-small">
                                        <i class="bi bi-folder-x fs-4 d-block mb-2"></i> لا توجد مستندات مرفقة لهذا الطلب
                                    </div>
                                @endif
                            </div>

                            @if($booking->notes)
                                <div class="mt-4 pt-3 border-top">
                                    <h6 class="fw-bold text-muted x-small text-uppercase mb-2">ملاحظات إضافية من المتقدم</h6>
                                    <div class="p-3 bg-light rounded-3 text-dark small border italic">
                                        "{{ $booking->notes }}"
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    @if($booking->status == 'pending')
                    <form action="{{ route('website.bookings.update', $booking) }}" method="POST" class="d-inline">
                        @csrf @method('PATCH')
                        <input type="hidden" name="status" value="confirmed">
                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-4 fw-bold">تأكيد الحجز الآن</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>
    function previewGhSlider(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const preview = document.getElementById('ghSliderPreview' + id);
                const placeholder = document.getElementById('ghSliderPlaceholder' + id);
                if (preview) {
                    preview.src = e.target.result;
                    preview.classList.remove('d-none');
                }
                if (placeholder) {
                    placeholder.classList.add('d-none');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .guest-house-mgmt-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .fs-xs { font-size: 0.8rem; }
    .btn-view-hover:hover { background: var(--primary-light) !important; color: var(--primary) !important; border-color: var(--primary) !important; }
    .bg-indigo { background-color: #6366f1; }
    .bg-primary-light { background-color: rgba(34, 197, 94, 0.1); }
    .transition-all { transition: all 0.3s ease; }

    /* Premium Hero */
    .premium-hero-sleek { 
        position: relative; 
        padding: 80px 0 100px; 
        background: white !important; 
        border-bottom: 1px solid var(--border); 
        overflow: hidden; 
        z-index: 10; 
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.05; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .hero-content-wrapper { position: relative; z-index: 5; }
    
    .badge-glass-premium { 
        background: var(--primary-light); 
        border: 1px solid rgba(34, 197, 94, 0.1); 
        padding: 8px 18px; 
        border-radius: 100px; 
        color: var(--primary); 
        font-weight: 700; 
        font-size: 0.85rem; 
        display: inline-block;
    }

    .header-icon-small {
        width: 38px;
        height: 38px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.1rem;
    }

    /* Stats Inputs */
    .stats-input-sleek:hover {
        border-color: var(--primary) !important;
        background-color: white !important;
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.05);
    }
    .stat-icon-vibe-small {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    /* Gallery Cards */
    .gallery-card-sleek {
        transition: all 0.3s ease;
    }
    .gallery-card-sleek:hover {
        border-color: var(--primary) !important;
        transform: translateY(-3px);
    }

    /* Booking Request Card */
    .booking-request-card {
        transition: all 0.3s ease;
    }
    .booking-request-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 30px rgba(0,0,0,0.08) !important;
        border-color: var(--primary-light) !important;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    /* Fix for 5 columns in grid */
    @media (min-width: 992px) {
        .col-lg-2-4 { flex: 0 0 20%; max-width: 20%; }
    }
</style>
@endsection
