@extends('layouts.app')

@section('content')
<div class="contact-messages-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek mb-4">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: var(--primary);"></div>
            <div class="glow-orb-2" style="background: var(--primary-dark);"></div>
        </div>
        <div class="container hero-content-wrapper text-center">
            <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-center">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-primary text-decoration-none">لوحة التحكم</a></li>
                    <li class="breadcrumb-item active" aria-current="page">تواصل معنا</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-mailbox2 me-2"></i> إدارة صندوق الوارد
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">تواصل معنا</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                متابعة استفسارات ومقترحات زوار الموقع الإلكتروني وإدارة قنوات التواصل المباشرة للجمهور.
            </p>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            {{-- Messages Inbox --}}
            <div class="col-12">
                <div class="card border-0 shadow-sm animate-slide-up overflow-hidden" style="border-radius: 24px;">
                    <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="header-icon-small bg-primary"><i class="bi bi-chat-left-dots-fill"></i></div>
                            <h5 class="fw-bold mb-0 text-dark">رسائل الوارد والطلبات</h5>
                        </div>
                        <div class="d-flex gap-2">
                             <span class="badge bg-primary rounded-pill px-3 py-2 fw-bold small">{{ $messages->where('read', 0)->count() }} جديدة</span>
                             <span class="badge bg-secondary rounded-pill px-3 py-2 fw-bold small opacity-75">{{ $messages->count() }} إجمالي</span>
                        </div>
                    </div>
                    
                    <div class="card-body p-0 bg-white">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr class="x-small text-uppercase fw-bold text-muted">
                                        <th class="ps-4 py-3 border-0">المرسل والبيانات</th>
                                        <th class="py-3 border-0">الموضوع والرسالة</th>
                                        <th class="py-3 border-0">التاريخ</th>
                                        <th class="py-3 border-0 text-center">الحالة</th>
                                        <th class="py-3 border-0 text-center pe-4">الإجراءات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $msg)
                                    <tr class="transition-all {{ !$msg->read ? 'bg-primary bg-opacity-05 fw-bold' : 'opacity-75' }}">
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="avatar-soft bg-primary-light text-primary rounded-circle d-flex align-items-center justify-content-center fw-bold" style="width: 44px; height: 44px;">
                                                    {{ mb_substr($msg->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="text-dark mb-0">{{ $msg->name }}</div>
                                                    <div class="x-small text-muted d-flex gap-2">
                                                        <span><i class="bi bi-envelope me-1"></i>{{ $msg->email }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <div class="small text-dark mb-1">
                                                @if(($msg->subject ?? '') == 'General')
                                                    <span class="badge bg-light text-dark border rounded-pill px-2">عام</span>
                                                @else
                                                    <span class="badge bg-primary-light text-primary border border-primary border-opacity-10 rounded-pill px-2">{{ $msg->subject ?? 'بلا عنوان' }}</span>
                                                @endif
                                            </div>
                                            <div class="x-small text-muted text-truncate" style="max-width: 250px;">{{ $msg->message }}</div>
                                        </td>
                                        <td class="py-3">
                                            <div class="x-small text-muted">
                                                <i class="bi bi-clock me-1"></i>
                                                {{ $msg->created_at->translatedFormat('d M Y - h:i a') }}
                                            </div>
                                        </td>
                                        <td class="py-3 text-center">
                                            @if($msg->read)
                                                <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold">مقروءة</span>
                                            @else
                                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-10 rounded-pill px-3 py-1 x-small fw-bold pulse-opacity">جديدة</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center pe-4">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-light text-primary border rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#viewMsg{{ $msg->id }}">
                                                    معاينة <i class="bi bi-eye ms-1"></i>
                                                </button>
                                                @if(!$msg->read)
                                                    <form action="{{ route('website.contact-messages.read', $msg) }}" method="POST" class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                                            تمت <i class="bi bi-check2-all ms-1"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('website.contact-messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-light text-danger border rounded-pill px-2">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5">
                                            <div class="py-5 opacity-25">
                                                <i class="bi bi-mailbox2 fs-1 d-block mb-3"></i>
                                                <h6 class="fw-bold">صندوق الوارد فارغ حالياً</h6>
                                                <p class="small mb-0">لم تصل أي رسائل جديدة من زوار الموقع</p>
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

            {{-- Contact Channel Settings --}}
            <div class="col-12 mt-5">
                <form action="{{ route('website.contact-settings.update') }}" method="POST">
                    @csrf
                    <div class="card border-0 shadow-sm animate-slide-up overflow-hidden" style="border-radius: 24px;">
                        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="header-icon-small bg-warning"><i class="bi bi-gear-fill text-dark"></i></div>
                                <h5 class="fw-bold mb-0 text-dark">إعدادات قنوات التواصل والبيانات</h5>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">حفظ كافة الإعدادات</button>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            {{-- Header Content --}}
                            <div class="row g-4 mb-5 border-bottom pb-5">
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">عنوان قسم التواصل (في الموقع)</label>
                                    <input type="text" name="contact_section_title" class="form-control" value="{{ $settings['contact_section_title'] ?? 'معلومات التواصل' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">العنوان الفرعي للقسم</label>
                                    <input type="text" name="contact_section_subtitle" class="form-control" value="{{ $settings['contact_section_subtitle'] ?? 'يسعدنا تواصلكم معنا بأي وسيلة تناسبكم' }}">
                                </div>
                            </div>

                            {{-- Channel Summary Cards --}}
                            <h6 class="fw-bold text-dark mb-4 border-start border-3 border-primary ps-3"><i class="bi bi-share me-2 text-primary"></i> روابط التواصل السريع (Quick Links)</h6>
                            <div class="row g-4 mb-5">
                                @php
                                    $channels = [
                                        ['key' => 'phone', 'label' => 'رقم الهاتف المباشر', 'icon' => 'bi-telephone', 'color' => 'success', 'placeholder' => '0123456789'],
                                        ['key' => 'email', 'label' => 'البريد الإلكتروني الرسمي', 'icon' => 'bi-envelope', 'color' => 'primary', 'placeholder' => 'info@org.com'],
                                        ['key' => 'whatsapp', 'label' => 'رابط / رقم واتساب', 'icon' => 'bi-whatsapp', 'color' => 'success', 'placeholder' => '+20...'],
                                    ];
                                @endphp
                                @foreach($channels as $channel)
                                <div class="col-xl-4 col-md-6">
                                    <div class="p-4 rounded-4 border bg-light h-100 transition-all hover-white-shadow">
                                        <div class="d-flex align-items-center gap-3 mb-3">
                                            <div class="stat-icon-vibe-small bg-{{ $channel['color'] }} bg-opacity-10 text-{{ $channel['color'] }}">
                                                <i class="bi {{ $channel['icon'] }}"></i>
                                            </div>
                                            <span class="fw-bold text-dark small">{{ $channel['label'] }}</span>
                                        </div>
                                        <input type="text" name="contact_{{ $channel['key'] }}_link" class="form-control" style="direction: ltr;" value="{{ $settings['contact_'.$channel['key'].'_link'] ?? '' }}" placeholder="{{ $channel['placeholder'] }}">
                                    </div>
                                </div>
                                @endforeach
                            </div>

                            {{-- Detailed Info --}}
                            <h6 class="fw-bold text-dark mb-4 border-start border-3 border-warning ps-3"><i class="bi bi-info-circle me-2 text-warning"></i> بيانات المقر وتفاصيل العمل</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted">عنوان المقر الرئيسي</label>
                                        <input type="text" name="contact_hq_address" class="form-control" value="{{ $settings['contact_hq_address'] ?? '' }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label small fw-bold text-muted">تفاصيل إضافية للعنوان (المبنى/علامة مميزة)</label>
                                        <input type="text" name="contact_hq_details" class="form-control" value="{{ $settings['contact_hq_details'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-muted">الخط الساخن</label>
                                            <input type="text" name="contact_hotline" class="form-control" style="direction: ltr;" value="{{ $settings['contact_hotline'] ?? '16... ' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-muted">بريد الدعم</label>
                                            <input type="text" name="contact_support_email" class="form-control" style="direction: ltr;" value="{{ $settings['contact_support_email'] ?? 'support@...' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-muted">أيام العمل</label>
                                            <input type="text" name="contact_working_days" class="form-control" value="{{ $settings['contact_working_days'] ?? 'يومياً عدا الجمعة' }}">
                                        </div>
                                        <div class="col-6">
                                            <label class="form-label small fw-bold text-muted">ساعات العمل</label>
                                            <input type="text" name="contact_working_hours" class="form-control" value="{{ $settings['contact_working_hours'] ?? '9 ص - 5 م' }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            {{-- Slider Gallery Management --}}
            <div class="col-12 mt-5">
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="card border-0 shadow-sm animate-slide-up overflow-hidden" style="border-radius: 24px;">
                        <div class="p-4 border-bottom bg-light d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="header-icon-small bg-success"><i class="bi bi-images"></i></div>
                                <h5 class="fw-bold mb-0 text-dark">معرض صور صفحة تواصل معنا (Slider)</h5>
                            </div>
                            <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm">حفظ المعرض</button>
                        </div>
                        <div class="card-body p-4 p-md-5">
                            <p class="x-small text-muted mb-4"><i class="bi bi-info-circle me-1"></i> تظهر هذه الصور كخلفية متحركة في صفحة تواصل معنا. الحجم المثالي (1920x600).</p>
                            <div class="row g-4">
                                @for($i = 1; $i <= 10; $i++)
                                <div class="col-md-4 col-lg-2-4 col-6">
                                    <div class="position-relative rounded-4 border overflow-hidden bg-light slider-upload-mini ratio ratio-21x9 group-hover-overlay" style="cursor: pointer;">
                                        @php $sliderPath = $settings["contact_slider_$i"] ?? null; @endphp
                                        @if($sliderPath)
                                            <img src="{{ asset('storage/' . $sliderPath) }}" class="w-100 h-100 object-fit-cover shadow-sm transition-all" id="contactSliderPrev{{ $i }}">
                                        @else
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="contactSliderPlace{{ $i }}">
                                                <i class="bi bi-cloud-arrow-up fs-3 mb-1"></i>
                                                <span class="x-small fw-bold">{{ $i }}</span>
                                            </div>
                                            <img src="" class="w-100 h-100 object-fit-cover d-none shadow-sm transition-all" id="contactSliderPrev{{ $i }}">
                                        @endif
                                        
                                        <input type="file" name="contact_slider_{{ $i }}" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewContactSlider(this, {{ $i }})">
                                        
                                        @if($sliderPath)
                                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center z-10" 
                                                style="width: 26px; height: 26px;"
                                                onclick="event.stopPropagation(); document.getElementById('delete_contact_slider_{{ $i }}').value='1'; this.closest('.slider-upload-mini').querySelector('.d-none').classList.remove('d-none'); document.getElementById('contactSliderPrev{{ $i }}').classList.add('d-none'); this.remove();">
                                            <i class="bi bi-trash fs-xs"></i>
                                        </button>
                                        @endif
                                        <input type="hidden" name="delete_contact_slider_{{ $i }}" id="delete_contact_slider_{{ $i }}" value="0">
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Message Detail Modals --}}
    @foreach($messages as $msg)
    <div class="modal fade" id="viewMsg{{ $msg->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                <div class="modal-header border-bottom bg-light px-4 py-3">
                    <h5 class="modal-title fw-bold text-dark"><i class="bi bi-info-circle-fill me-2 text-primary"></i> تفاصيل الرسالة الواردة</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="row g-0 text-start" dir="rtl">
                        <div class="col-md-4 bg-light border-start p-4 text-center">
                            <div class="avatar-large bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow-sm" style="width: 70px; height: 70px; font-size: 1.8rem; border: 4px solid white;">
                                {{ mb_substr($msg->name, 0, 1) }}
                            </div>
                            <h6 class="fw-bold mb-1 text-dark">{{ $msg->name }}</h6>
                            <div class="small text-muted mb-4">{{ $msg->email }}</div>
                            
                            <div class="vstack gap-2 mt-2">
                                <div class="p-3 bg-white rounded-3 border text-start">
                                    <div class="x-small text-uppercase text-primary fw-bold mb-2"><i class="bi bi-phone ms-1"></i> رقم الهاتف</div>
                                    <div class="fw-bold text-dark small" style="direction: ltr; text-align: left;">{{ $msg->phone }}</div>
                                </div>
                                <div class="p-3 bg-white rounded-3 border text-start">
                                    <div class="x-small text-uppercase text-success fw-bold mb-2"><i class="bi bi-calendar2-check ms-1"></i> تم الاستلام في</div>
                                    <div class="fw-bold text-dark small">{{ $msg->created_at->translatedFormat('d M Y (h:i a)') }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-8 p-4 bg-white">
                            <div class="mb-4">
                                <label class="x-small text-muted fw-bold d-block mb-1">موضوع الرسالة / القسم</label>
                                <div class="p-2 px-3 bg-light rounded-3 text-dark fw-bold border">
                                    @if(($msg->subject ?? '') == 'General') عام @else {{ $msg->subject ?? 'بلا عنوان' }} @endif
                                </div>
                            </div>

                            <div class="mb-5">
                                <label class="x-small text-muted fw-bold d-block mb-1">محتوى الرسالة</label>
                                <div class="p-3 bg-light rounded-4 border text-dark lh-lg" style="white-space: pre-wrap; font-size: 0.95rem;">{{ $msg->message }}</div>
                            </div>

                            @if($msg->image_path)
                            <div class="mb-2">
                                <label class="x-small text-muted fw-bold d-block mb-2">المرفقات (صورة)</label>
                                <a href="{{ $msg->image_url }}" target="_blank" class="d-block group-hover-zoom overflow-hidden rounded-4 border shadow-sm">
                                    <img src="{{ $msg->image_url }}" class="img-fluid w-100 transition-all" style="max-height: 300px; object-fit: contain; background: #f8fafc;">
                                </a>
                                <p class="x-small text-muted mt-1 text-center italic">اضغط على الصورة لفتحها بحجمها الكامل</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3 gap-2">
                    <form action="{{ route('website.contact-messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-4">حذف الرسالة <i class="bi bi-trash ms-1"></i></button>
                    </form>
                    <a href="mailto:{{ $msg->email }}" class="btn btn-sm btn-primary flex-grow-1 py-2 rounded-pill fw-bold">الرد عبر البريد <i class="bi bi-reply-fill ms-1"></i></a>
                    <button type="button" class="btn btn-sm btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<script>
    function previewContactSlider(input, index) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('contactSliderPrev' + index);
                const place = document.getElementById('contactSliderPlace' + index);
                if (img) {
                    img.src = e.target.result;
                    img.classList.remove('d-none');
                }
                if (place) {
                    place.classList.add('d-none');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

<style>
    .contact-messages-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .fs-xs { font-size: 0.75rem; }
    .bg-primary-light { background-color: rgba(34, 197, 94, 0.1); }
    .bg-primary-05 { background-color: rgba(34, 197, 94, 0.04); }
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

    .stat-icon-vibe-small {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .hover-white-shadow:hover {
        background-color: white !important;
        border-color: var(--primary-light) !important;
        box-shadow: 0 10px 25px rgba(0,0,0,0.05);
        transform: translateY(-3px);
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .slider-upload-mini {
        transition: all 0.3s ease;
        border: 2px dashed var(--border) !important;
    }
    .slider-upload-mini:hover {
        border-color: var(--primary) !important;
        background: var(--bg-soft) !important;
    }

    .group-hover-zoom:hover img { transform: scale(1.05); }
    .pulse-opacity { animation: pulseOp 2s infinite; }
    @keyframes pulseOp { 0% { opacity: 1; } 50% { opacity: 0.5; } 100% { opacity: 1; } }

    @media (min-width: 992px) {
        .col-lg-2-4 { flex: 0 0 20%; max-width: 20%; }
    }
</style>
@endsection
