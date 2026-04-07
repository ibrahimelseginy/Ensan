@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="guest-house-mgmt-page">
    {{-- Dynamic Hero Section --}}
    <div class="premium-hero-sleek" style="background: @if($page->image_path) linear-gradient(rgba(15, 23, 42, 0.8), rgba(30, 58, 138, 0.8)), url('{{ $page->image_url }}') @else linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%) @endif; background-size: cover; background-position: center;">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #3b82f6;"></div>
            <div class="glow-orb-2" style="background: #0ea5e9;"></div>
            <div class="noise-overlay"></div>
        </div>
        
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-hospital me-2"></i> إدارة دار الضيافة
                        </div>
                    </div>
                    <h1 class="display-3 fw-800 text-white mb-3 text-end">إدارة دار الضيافة</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        محتوى الصفحة
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    <button type="button" onclick="document.querySelector('#combinedSubmitForm').submit()" class="btn btn-action-glow-blue btn-lg px-5 py-3 rounded-4 shadow-xl">
                        <i class="bi bi-cloud-check-fill me-2"></i>
                        <span>حفظ التغييرات النهائية</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Dashboard Area --}}
    <div class="container-fluid py-5">
        <div class="row g-4">
            {{-- Unified Form --}}
            <div class="col-12">
                <form id="combinedSubmitForm" action="{{ route('website.guest-house.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-4">
                        {{-- Basic Information Card --}}
                        <div class="col-12">
                            <div class="premium-card-dark animate-up h-100">
                                <div class="card-header-lux">
                                    <div class="header-icon bg-blue-500"><i class="bi bi-file-earmark-richtext"></i></div>
                                    <h5 class="fw-bold mb-0">المحتوى التعريفي والوصف</h5>
                                </div>
                                <div class="card-body-lux p-4 p-md-5">
                                    <div class="form-group-lux mb-4">
                                        <label class="label-lux">عنوان الصفحة الرئيسي</label>
                                        <div class="input-glow-wrapper">
                                            <input type="text" name="title" class="field-lux text-xl" value="{{ $page->title }}" required>
                                        </div>
                                    </div>
                                    <div class="form-group-lux mb-4">
                                        <label class="label-lux">العنوان الفرعي (Hero Subtitle)</label>
                                        <input type="text" name="gh_hero_subtitle" class="field-lux" value="{{ $settings['gh_hero_subtitle'] ?? 'ملاذ آمن للمرضى ومرافقيهم' }}">
                                    </div>

                                </div>
                            </div>
                        </div>





                        {{-- Stats & Gallery Unified Management --}}
                        <div class="col-12">
                            <div class="premium-card-dark animate-up">
                                <div class="card-header-lux d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="header-icon bg-emerald-500"><i class="bi bi-columns-gap"></i></div>
                                        <h5 class="fw-bold mb-0">لوحة الإحصائيات ومعرض الصور</h5>
                                    </div>
                                </div>
                                <div class="card-body-lux p-4 p-md-5">
                                    {{-- Statistics Section --}}
                                    <h6 class="fw-bold text-white mb-4"><i class="bi bi-bar-chart-fill me-2 text-amber-500"></i> إحصائيات الدار</h6>
                                    <div class="row g-4 mb-5">
                                        {{-- Stat 1: Beds --}}
                                        <div class="col-md-3 col-6">
                                            <div class="stat-input-box">
                                                <div class="stat-icon-vibe bg-blue-500 bg-opacity-10 text-blue-500">
                                                    <i class="bi bi-hospital"></i>
                                                </div>
                                                <div class="w-100">
                                                    <input type="text" name="gh_stat1_value" class="stat-value-field" value="{{ $settings['gh_stat1_value'] ?? '+50' }}">
                                                    <input type="text" name="gh_stat1_label" class="stat-label-field" value="{{ $settings['gh_stat1_label'] ?? 'سرير' }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Stat 2: Patients --}}
                                        <div class="col-md-3 col-6">
                                            <div class="stat-input-box">
                                                <div class="stat-icon-vibe bg-emerald-500 bg-opacity-10 text-emerald-500">
                                                    <i class="bi bi-people-fill"></i>
                                                </div>
                                                <div class="w-100">
                                                    <input type="text" name="gh_stat2_value" class="stat-value-field" value="{{ $settings['gh_stat2_value'] ?? '+3000' }}">
                                                    <input type="text" name="gh_stat2_label" class="stat-label-field" value="{{ $settings['gh_stat2_label'] ?? 'مريض سنوياً' }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Stat 3: Branches --}}
                                        <div class="col-md-3 col-6">
                                            <div class="stat-input-box">
                                                <div class="stat-icon-vibe bg-amber-500 bg-opacity-10 text-amber-500">
                                                    <i class="bi bi-geo-alt-fill"></i>
                                                </div>
                                                <div class="w-100">
                                                    <input type="text" name="gh_stat3_value" class="stat-value-field" value="{{ $settings['gh_stat3_value'] ?? '2' }}">
                                                    <input type="text" name="gh_stat3_label" class="stat-label-field" value="{{ $settings['gh_stat3_label'] ?? 'فرع' }}">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Stat 4: Reception --}}
                                        <div class="col-md-3 col-6">
                                            <div class="stat-input-box">
                                                <div class="stat-icon-vibe bg-indigo-500 bg-opacity-10 text-indigo-500">
                                                    <i class="bi bi-clock-history"></i>
                                                </div>
                                                <div class="w-100">
                                                    <input type="text" name="gh_stat4_value" class="stat-value-field" value="{{ $settings['gh_stat4_value'] ?? '24/7' }}">
                                                    <input type="text" name="gh_stat4_label" class="stat-label-field" value="{{ $settings['gh_stat4_label'] ?? 'استقبال' }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="divider-lux mb-5"></div>
                                    {{-- Slider Section --}}
                                    <h6 class="fw-bold text-white mb-4"><i class="bi bi-play-btn me-2 text-indigo-400"></i> سلايدر الصور المتحركة</h6>
                                    <div class="row g-4 mb-5">
                                        @for($i = 1; $i <= 10; $i++)
                                        <div class="col-md-4">
                                            <div class="gallery-card-lux position-relative" id="ghSliderZone{{ $i }}" style="height: 160px; border-color: #6366f1;">
                                                <input type="file" name="gh_slider_{{ $i }}" class="file-hidden" onchange="previewGhSlider(this, {{ $i }})">
                                                
                                                {{-- Delete Button --}}
                                                @if(isset($settings["gh_slider_$i"]))
                                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-lg" 
                                                        style="z-index: 20; width: 30px; height: 30px; padding: 0;"
                                                        onclick="event.stopPropagation(); document.getElementById('delete_gh_slider_{{ $i }}').value='1'; this.closest('.gallery-card-lux').querySelector('.gallery-preview-wrapper').innerHTML='<div class=\'gallery-placeholder\'><i class=\'bi bi-images fs-3 text-indigo-400\'></i><p class=\'x-small mb-0 mt-2\'>محذوف - حفظ لتنفيذ</p></div>'; this.remove();">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                                @endif
                                                <input type="hidden" name="delete_gh_slider_{{ $i }}" id="delete_gh_slider_{{ $i }}" value="0">

                                                <div class="gallery-preview-wrapper" id="ghSliderPreview{{ $i }}">
                                                    @if(isset($settings["gh_slider_$i"]))
                                                        <img src="{{ asset('storage/' . $settings["gh_slider_$i"]) }}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover">
                                                    @else
                                                        <div class="gallery-placeholder">
                                                            <i class="bi bi-images fs-3 text-indigo-400"></i>
                                                            <p class="x-small mb-0 mt-2">شريحة {{ $i }}</p>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endfor
                                    </div>

                                    <div class="divider-lux mb-5"></div>




                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Room Bookings Section (Added Request) --}}
    <div class="row mt-5">
        <div class="col-12">
            <div class="premium-card-dark animate-up">
                <div class="card-header-lux d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="header-icon bg-indigo-500"><i class="bi bi-calendar-check-fill"></i></div>
                        <h5 class="fw-bold mb-0">طلبات الحجز من الموقع</h5>
                    </div>
                </div>
                <div class="card-body-lux p-4">
                    {{-- Search Filter Section --}}


                    <div class="row g-4">
                        @forelse($bookings as $booking)
                        <div class="col-md-6 col-xl-4">
                            <div class="request-card-premium h-100">
                                <div class="card-status-pill">
                                    @php
                                        $statusClass = [
                                            'pending' => 'pending',
                                            'confirmed' => 'approved',
                                            'cancelled' => 'rejected'
                                        ][$booking->status] ?? 'pending';
                                        
                                        $statusText = [
                                            'pending' => 'قيد الانتظار',
                                            'confirmed' => 'مؤكد',
                                            'cancelled' => 'ملغي'
                                        ][$booking->status] ?? $booking->status;
                                    @endphp
                                    <span class="badge-status {{ $statusClass }}">{{ $statusText }}</span>
                                </div>

                                <div class="card-top-vibe mt-2">
                                    <div class="user-avatar-premium shadow-lg bg-indigo-600">
                                        <div class="avatar-ring"></div>
                                        <span class="avatar-initials"><i class="bi bi-person"></i></span>
                                    </div>
                                    <div class="user-main-info">
                                        <h6 class="fw-bold mb-1 text-white">{{ $booking->name }}</h6>
                                        <div class="x-small text-slate-400 d-flex gap-2">
                                            <span><i class="bi bi-phone me-1"></i>{{ $booking->phone }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-mid-vibe mt-4">
                                    <div class="detail-grid-compact">
                                        <div class="detail-item-compact">
                                            <label>الغرفة</label>
                                            <span>{{ $booking->room_type }}</span>
                                        </div>
                                        <div class="detail-item-compact">
                                            <label>المدة</label>
                                            <span>{{ \Carbon\Carbon::parse($booking->check_in)->diffInDays($booking->check_out) }} ليالي</span>
                                        </div>
                                        <div class="detail-item-compact w-100">
                                            <label>الوصول</label>
                                            <span class="text-emerald-400">{{ $booking->check_in }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="card-bottom-vibe mt-4 pt-3 border-top border-white border-opacity-10">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="text-slate-500 x-small fw-bold">#{{ $booking->id }}</span>
                                        <span class="text-slate-500 x-small">{{ $booking->created_at->format('d M Y') }}</span>
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-glass-indigo flex-grow-1 py-2 rounded-pill x-small fw-bold" data-bs-toggle="modal" data-bs-target="#viewBooking{{ $booking->id }}">
                                            <i class="bi bi-eye ms-1"></i> التفاصيل
                                        </button>
                                        <div class="dropdown">
                                            <button class="btn btn-glass-indigo py-2 px-3 rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                                                <i class="bi bi-three-dots"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-2xl border-0 premium-dropdown-dark">
                                                <li>
                                                    <form action="{{ route('website.bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="confirmed">
                                                        <button type="submit" class="dropdown-item text-success fw-bold x-small"><i class="bi bi-check-circle me-2"></i> تأكيد</button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <form action="{{ route('website.bookings.update', $booking) }}" method="POST">
                                                        @csrf @method('PATCH')
                                                        <input type="hidden" name="status" value="cancelled">
                                                        <button type="submit" class="dropdown-item text-danger fw-bold x-small"><i class="bi bi-x-circle me-2"></i> إلغاء</button>
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
                            <div class="empty-state-card-lux py-5 text-center">
                                <i class="bi bi-calendar-x fs-1 text-slate-600 mb-3 d-block"></i>
                                <p class="text-slate-400 mb-0">لا توجد طلبات حجز حالياً</p>
                            </div>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($bookings as $booking)
    <div class="modal fade" id="viewBooking{{ $booking->id }}" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content modal-premium-dark border-0 shadow-2xl rounded-4 overflow-hidden" style="background-color: #0f172a !important; color: white;">
                <div class="modal-header border-bottom border-white border-opacity-10 bg-slate-800 px-4 py-3">
                    <h5 class="modal-title fw-bold text-white"><i class="bi bi-info-circle-fill me-2 text-info"></i> تفاصيل طلب الحجز #{{ $booking->id }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0" style="background-color: #0f172a !important;">
                    <div class="row g-0 text-end" dir="rtl">
                        {{-- Side Info Strip --}}
                        <div class="col-md-4 bg-slate-800 bg-opacity-50 border-start border-white border-opacity-10 p-4">
                            <div class="text-center mb-4">
                                <div class="strip-avatar bg-indigo-600 shadow-lg rounded-20 d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; border-radius: 25px; position: relative; border: 2px solid rgba(255,255,255,0.1);">
                                    <i class="bi bi-person-fill fs-1 text-white opacity-75"></i>
                                    <div style="position: absolute; inset: -4px; border: 1px solid rgba(99, 102, 241, 0.3); border-radius: 28px;"></div>
                                </div>
                                <h6 class="fw-bold mb-1 text-white">{{ $booking->name }}</h6>
                                <div class="x-small text-slate-400 mb-1"><i class="bi bi-telephone me-1"></i> {{ $booking->phone }}</div>
                                @if($booking->national_id)
                                    <div class="badge bg-indigo-500 bg-opacity-20 text-indigo-300 rounded-pill px-3 py-1 x-small mt-2 border border-indigo-500 border-opacity-30">
                                        {{ $booking->national_id }}
                                    </div>
                                @endif
                            </div>

                            <div class="vstack gap-3 mt-4">
                                <div class="p-3 bg-slate-900 bg-opacity-50 rounded-4 border border-white border-opacity-5">
                                    <div class="x-small text-uppercase text-indigo-400 fw-800 mb-2"><i class="bi bi-people-fill ms-1"></i> بيانات المرافق</div>
                                    <div class="fw-bold text-white mb-1">{{ $booking->companion_name ?? 'لا يوجد مرافق' }}</div>
                                    @if($booking->companion_phone)
                                        <div class="x-small text-slate-400"><i class="bi bi-telephone ms-1"></i> {{ $booking->companion_phone }}</div>
                                    @endif
                                </div>

                                <div class="p-3 bg-slate-900 bg-opacity-50 rounded-4 border border-white border-opacity-5">
                                    <div class="x-small text-uppercase text-emerald-400 fw-800 mb-2"><i class="bi bi-geo-alt-fill ms-1"></i> مكان الإقامة</div>
                                    <div class="fw-bold text-white small opacity-90">{{ $booking->address ?? 'العنوان غير محدد' }}</div>
                                </div>
                            </div>
                        </div>

                        {{-- Main Details Area --}}
                        <div class="col-md-8 p-4">
                            <div class="mb-4">
                                <h6 class="fw-bold text-indigo-400 mb-3 d-flex align-items-center"><i class="bi bi-hospital-fill ms-2 fs-5"></i> تفاصيل الإقامة والعلاج</h6>
                                <div class="row g-3">
                                    <div class="col-6">
                                        <label class="x-small text-slate-500 fw-bold d-block mb-1">نوع الغرفة</label>
                                        <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-white small border border-white border-opacity-5">{{ $booking->room_type }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="x-small text-slate-500 fw-bold d-block mb-1">المدة المتوقعة</label>
                                        <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-white small border border-white border-opacity-5">{{ $booking->expected_duration_arabic ?? '-' }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="x-small text-slate-500 fw-bold d-block mb-1">تاريخ الوصول</label>
                                        <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-emerald-400 small border border-white border-opacity-5 fw-bold">{{ $booking->arrival_date ?? $booking->check_in }}</div>
                                    </div>
                                    <div class="col-6">
                                        <label class="x-small text-slate-500 fw-bold d-block mb-1">المركز الطبي</label>
                                        <div class="p-2 px-3 bg-slate-800 bg-opacity-40 rounded-3 text-white small border border-white border-opacity-5">{{ $booking->medical_center ?? '-' }}</div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h6 class="fw-bold text-warning mb-3 d-flex align-items-center"><i class="bi bi-paperclip ms-2 fs-5"></i> المستندات المرفقة</h6>
                                <div class="d-flex flex-wrap gap-2">
                                    @php
                                        $docs = [
                                            ['label' => 'بطاقة المريض', 'path' => $booking->patient_id_path, 'icon' => 'bi-person-vcard'],
                                            ['label' => 'بطاقة المرافق', 'path' => $booking->companion_id_path, 'icon' => 'bi-person-badge'],
                                            ['label' => 'تحويل المستشفى', 'path' => $booking->medical_transfer_path, 'icon' => 'bi-hospital'],
                                            ['label' => 'كارت المتابعة', 'path' => $booking->followup_card_path, 'icon' => 'bi-card-list'],
                                            ['label' => 'تقرير الإشعاع', 'path' => $booking->medical_report_path, 'icon' => 'bi-file-earmark-medical'],
                                        ];
                                    @endphp

                                    @foreach($docs as $doc)
                                        @if($doc['path'])
                                            <a href="{{ asset('storage/' . $doc['path']) }}" target="_blank" class="btn btn-sm btn-glass-premium-doc d-flex align-items-center gap-2 px-3 py-2 rounded-3">
                                                <i class="bi {{ $doc['icon'] }} text-info"></i>
                                                <span class="x-small fw-bold text-white">{{ $doc['label'] }}</span>
                                            </a>
                                        @endif
                                    @endforeach

                                    @if(collect($docs)->where('path', '!=', null)->isEmpty())
                                        <div class="p-3 w-100 bg-slate-800 bg-opacity-30 rounded-3 text-center border border-dashed border-white border-opacity-10 text-slate-500 x-small">
                                            <i class="bi bi-folder-x ms-1"></i> لا توجد مستندات مرفقة لهذه الحالة
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @if($booking->notes)
                                <div>
                                    <h6 class="fw-bold text-white-50 mb-2 x-small text-uppercase"><i class="bi bi-pencil-square ms-1"></i> ملاحظات إضافية</h6>
                                    <div class="p-3 bg-slate-800 bg-opacity-60 rounded-4 border border-white border-opacity-5 text-slate-300 small italic">
                                        "{{ $booking->notes }}"
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top border-white border-opacity-10 bg-slate-800 p-3">
                    <button type="button" class="btn btn-sm btn-glass-indigo px-5 rounded-pill fw-bold" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>
function previewMain(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('previewMainImg');
            if (preview) {
                preview.src = e.target.result;
            } else {
                const container = document.querySelector('.preview-area-lux-large');
                container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 shadow-2xl" id="previewMainImg">`;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewGallery(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('galleryPreviewContainer' + id);
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover shadow-lg">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewGhSlider(input, id) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('ghSliderPreview' + id);
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
    :root {
        --dark-bg: #0b0e14;
        --card-dark: #1a2332;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-400: #94a3b8;
        --blue-500: #3b82f6;
        --cyan-400: #22d3ee;
        --emerald-500: #10b981;
        --amber-500: #f59e0b;
        --indigo-500: #6366f1;
    }

    body {
        background-color: var(--dark-bg) !important;
        font-family: 'Tajawal', sans-serif;
        color: #f8fafc;
    }

    .guest-house-mgmt-page { min-height: 100vh; }

    /* Premium Hero Sleek */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 120px;
        border-radius: 0;
        margin: -30px -30px 0 -30px;
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }

    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.3; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.03; background-image: url('data:image/svg+xml,...'); }

    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { 
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px 18px;
        border-radius: 100px;
        color: #e0e7ff;
        font-weight: 700;
        font-size: 0.85rem;
    }

    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }

    .btn-action-glow-blue {
        background: var(--blue-500);
        color: white;
        border: none;
        font-weight: 800;
        box-shadow: 0 0 25px rgba(59, 130, 246, 0.4);
        transition: 0.4s;
    }
    .btn-action-glow-blue:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(59, 130, 246, 0.6);
        color: white;
    }

    /* Content Area */
    .content-shift-up { margin-top: -60px; position: relative; z-index: 20; padding: 0 5%; }

    /* Premium Card Dark */
    .premium-card-dark {
        background: var(--card-dark);
        border-radius: 35px;
        border: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }

    .card-header-lux {
        padding: 25px 30px;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        display: flex;
        align-items: center;
        gap: 15px;
        background: rgba(255,255,255,0.01);
    }
    .header-icon {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.2rem;
    }
    .bg-blue-500 { background: var(--blue-500); }
    .bg-cyan-500 { background: #06b6d4; }
    .bg-emerald-500 { background: var(--emerald-500); }

    .form-group-lux { position: relative; }
    .label-lux { color: var(--slate-400); font-weight: 700; font-size: 0.85rem; margin-bottom: 12px; display: block; }
    
    .field-lux {
        width: 100%;
        background: #0f172a;
        border: 2px solid #2d3748;
        border-radius: 18px;
        padding: 16px 22px;
        color: white;
        font-weight: 600;
        transition: 0.3s;
    }
    .field-lux:focus {
        border-color: var(--blue-500);
        outline: none;
        background: #000;
        box-shadow: 0 0 0 5px rgba(59, 130, 246, 0.1);
    }
    .text-xl { font-size: 1.4rem; color: var(--cyan-400); }

    /* Large Upload Zone */
    .upload-zone-lux-large {
        height: 380px;
        border: 2px dashed #2d3748;
        border-radius: 30px;
        position: relative;
        overflow: hidden;
        cursor: pointer;
        transition: 0.3s;
    }
    .upload-zone-lux-large:hover { border-color: #06b6d4; background: rgba(6, 182, 212, 0.03); }
    
    .preview-area-lux-large { height: 100%; display: flex; align-items: center; justify-content: center; }
    .upload-placeholder-lux { text-align: center; }
    
    .upload-hover-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.6);
        backdrop-filter: blur(4px);
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: 0.3s;
    }
    .upload-zone-lux-large:hover .upload-hover-overlay { opacity: 1; }
    .btn-cyan-solid { background: #06b6d4; color: white; border: none; font-weight: 700; }

    .info-alert-lux {
        background: rgba(6, 182, 212, 0.08);
        color: #67e8f9;
        padding: 15px 20px;
        border-radius: 18px;
        font-size: 0.8rem;
    }

    /* Stats Inputs */
    .stat-input-box {
        background: #0f172a;
        border: 1px solid #1e293b;
        border-radius: 24px;
        padding: 20px;
        display: flex;
        gap: 15px;
        align-items: center;
        transition: 0.3s;
    }
    .stat-input-box:hover { transform: translateY(-3px); border-color: var(--slate-400); }
    
    .stat-icon-vibe {
        width: 50px;
        height: 50px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .stat-label-field {
        background: transparent;
        border: none;
        color: var(--slate-500);
        font-size: 0.75rem;
        font-weight: 800;
        width: 100%;
        margin-bottom: 2px;
    }
    .stat-value-field {
        background: transparent;
        border: none;
        color: white;
        font-size: 1.25rem;
        font-weight: 800;
        width: 100%;
        font-family: 'Outfit';
    }

    /* Gallery Card */
    .gallery-card-lux {
        height: 200px;
        border-radius: 20px;
        position: relative;
        overflow: hidden;
        border: 2px dashed #1e293b;
        cursor: pointer;
        transition: 0.4s;
    }
    .gallery-card-lux:hover { border-color: var(--emerald-500); transform: scale(1.03); }
    
    .gallery-preview-wrapper { height: 100%; }
    .gallery-placeholder { height: 100%; display: flex; flex-direction: column; align-items: center; justify-content: center; color: var(--slate-800); }
    
    .gallery-overlay {
        position: absolute;
        inset: 0;
        background: rgba(16, 185, 129, 0.6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        opacity: 0;
        transition: 0.3s;
    }
    .gallery-card-lux:hover .gallery-overlay { opacity: 1; }

    .file-hidden {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }

    .divider-lux { height: 1px; background: rgba(255,255,255,0.05); }

    /* Animations */
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(35px); } to { opacity: 1; transform: translateY(0); } }

    .scroll-thin::-webkit-scrollbar { width: 6px; }
    .scroll-thin::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

    .bg-blue-500 { background: #3b82f6; }
    .bg-emerald-500 { background: #10b981; }
    .bg-amber-500 { background: #f59e0b; }
    .bg-indigo-500 { background: #6366f1; }

    @media (max-width: 991px) {
        .premium-hero-sleek { padding: 60px 0 80px; border-radius: 0 0 35px 35px; }
        .display-4 { font-size: 2.2rem; }
    }
    /* Premium Request Card (Copied & Adapted) */
    .request-card-premium {
        background: #1a2332;
        border-radius: 30px;
        padding: 30px;
        border: 1px solid rgba(255,255,255,0.05);
        transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
    }
    .request-card-premium:hover {
        transform: translateY(-8px) scale(1.02);
        border-color: var(--indigo-500);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
    }

    .card-status-pill { position: absolute; top: 25px; left: 25px; }
    
    .detail-item-compact label { display: block; font-size: 0.65rem; color: var(--slate-400); font-weight: 700; margin-bottom: 4px; }
    .detail-item-compact span { display: block; font-size: 0.85rem; color: white; font-weight: 600; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

    /* Premium Modal Dark Refinement */
    .modal-premium-dark { background-color: #0f172a !important; border: 1px solid rgba(255, 255, 255, 0.1) !important; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5) !important; }
    .rounded-20 { border-radius: 20px; }
    .fw-800 { font-weight: 800; }
    .text-indigo-400 { color: #818cf8 !important; }
    .text-emerald-400 { color: #34d399 !important; }
    
    .btn-glass-premium-doc {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.08);
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    .btn-glass-premium-doc:hover {
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
        transform: translateY(-2px);
    }
    .btn-glass-indigo {
        background: rgba(99, 102, 241, 0.1);
        border: 1px solid rgba(99, 102, 241, 0.2);
        color: #818cf8;
        backdrop-filter: blur(8px);
        transition: all 0.3s ease;
    }
    .btn-glass-indigo:hover {
        background: rgba(99, 102, 241, 0.2);
        border-color: rgba(99, 102, 241, 0.4);
        color: #a5b4fc;
        transform: translateY(-2px);
    }
      
      
            
      

      

      /* --- SYSTEM LIGHT MODE PATCH --- */
      body:not(.theme-dark) {
          background-color: var(--ws-bg-page) !important;
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .text-white, 
      body:not(.theme-dark) .text-white-50 {
          color: var(--ws-text-primary) !important;
      }
      body:not(.theme-dark) .premium-hero-sleek .text-white, 
      body:not(.theme-dark) .premium-hero-sleek .text-white-50,
      body:not(.theme-dark) .badge-glass-premium,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item,
      body:not(.theme-dark) .premium-hero-sleek .breadcrumb-item a {
          color: #fff !important;
      }
      body:not(.theme-dark) .glass-card, 
      body:not(.theme-dark) .premium-modal-dark,
      body:not(.theme-dark) .card,
      body:not(.theme-dark) .stats-card-dark,
      body:not(.theme-dark) .stats-inner-card,
      body:not(.theme-dark) .project-card-admin,
      body:not(.theme-dark) .campaign-card-lux,
      body:not(.theme-dark) .guest-card-lux,
      body:not(.theme-dark) .article-card-lux,
      body:not(.theme-dark) .message-card-lux,
      body:not(.theme-dark) .donation-card-lux,
      body:not(.theme-dark) .member-card-premium,
      body:not(.theme-dark) .partner-card-lux,
      body:not(.theme-dark) .leader-card-lux,
      body:not(.theme-dark) .empty-state-card-lux,
      body:not(.theme-dark) .bg-dark,
      body:not(.theme-dark) .bg-slate-800,
      body:not(.theme-dark) .bg-slate-900,
      body:not(.theme-dark) .modal-content,
      body:not(.theme-dark) .categories-sidebar,
      body:not(.theme-dark) .sector-header,
      body:not(.theme-dark) .item-card,
      body:not(.theme-dark) .dark-glass-card {
          background: var(--ws-bg-card) !important;
          border-color: var(--ws-border) !important;
          box-shadow: 0 10px 25px rgba(0,0,0,0.05) !important;
      }
      body:not(.theme-dark) .category-item {
          color: var(--ws-text-secondary);
          background: rgba(0,0,0,0.02);
      }
      body:not(.theme-dark) .category-item:hover { background: var(--ws-bg-page); color: var(--ws-text-primary); }
      body:not(.theme-dark) .category-item.active { background: var(--ws-bg-page); border-color: var(--ws-primary); color: var(--ws-text-primary); }
      body:not(.theme-dark) .field-lux, body:not(.theme-dark) .form-control, body:not(.theme-dark) .form-select, body:not(.theme-dark) .form-input-dark { 
          background: var(--ws-bg-input) !important; color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; 
      }
      body:not(.theme-dark) .label-lux, body:not(.theme-dark) .form-label, body:not(.theme-dark) .text-slate-400 { color: var(--ws-text-secondary) !important; }
      body:not(.theme-dark) .modal-header .text-white { color: var(--ws-text-primary) !important; }
      body:not(.theme-dark) .btn-close-white { filter: invert(1) grayscale(100%) brightness(200%); }
      body:not(.theme-dark) .table, body:not(.theme-dark) .table th, body:not(.theme-dark) .table td, body:not(.theme-dark) .table tr { color: var(--ws-text-primary) !important; border-color: var(--ws-border) !important; }
      </style>
@endsection








