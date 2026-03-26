@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="contact-messages-page">
    {{-- Premium Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1" style="background: #6366f1;"></div>
            <div class="glow-orb-2" style="background: #8b5cf6;"></div>
            <div class="noise-overlay"></div>
        </div>
        
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">تواصل معنا</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-mailbox2 ms-2"></i> صندوق الوارد
                        </div>
                    </div>
                    <h1 class="display-3 fw-800 text-white mb-3 text-end">تواصل معنا</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        متابعة استفسارات ومقترحات زوار الموقع الإلكتروني وإدارة قنوات التواصل المباشرة للجمهور.
                    </p>
                </div>

            </div>
        </div>
    </div>

    {{-- Main Content Section --}}
    <div class="container-fluid py-5">
        <div class="row g-4">
            {{-- Messages Table --}}
            <div class="col-12">
                <div class="premium-card-dark animate-up">
                    <div class="card-header-lux d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <h5 class="fw-bold mb-0">قائمة الرسائل الواردة</h5>
                            <div class="header-icon bg-indigo-500 ms-3"><i class="bi bi-chat-left-text-fill"></i></div>
                        </div>
                    </div>
                    
                    <div class="card-body-lux p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0 premium-table-dark">
                                <thead>
                                    <tr>
                                        <th class="ps-4">المرسل</th>
                                        <th>نوع الاستفسار</th>
                                        <th>تاريخ الإرسال</th>
                                        <th>الحالة</th>
                                        <th class="text-center">الإجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($messages as $msg)
                                    <tr class="{{ $msg->read ? 'opacity-75' : 'bg-unread' }}">
                                        <td class="ps-4">
                                            <div class="d-flex align-items-center gap-3">
                                                <div class="user-avatar-mini bg-indigo-900 text-indigo-400">
                                                    {{ mb_substr($msg->name, 0, 1) }}
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-white mb-1">{{ $msg->name }}</div>
                                                    <div class="x-small text-slate-500 d-flex gap-2">
                                                        <span><i class="bi bi-envelope ms-1"></i>{{ $msg->email }}</span>
                                                        <span><i class="bi bi-phone ms-1"></i>{{ $msg->phone }}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="small fw-bold text-white mb-1">
                                                @if(($msg->subject ?? '') == 'General')
                                                    عام
                                                @else
                                                    {{ $msg->subject ?? 'بلا عنوان' }}
                                                @endif
                                            </div>
                                            <div class="x-small text-slate-500 text-truncate" style="max-width: 300px;">{{ $msg->message }}</div>
                                        </td>
                                        <td>
                                            <div class="small text-slate-400">
                                                <i class="bi bi-calendar-event ms-1"></i>
                                                {{ $msg->created_at->translatedFormat('d M Y - h:i a') }}
                                            </div>
                                        </td>
                                        <td>
                                            @if($msg->read)
                                                <span class="badge-status approved">مقروءة</span>
                                            @else
                                                <span class="badge-status pending">جديدة</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-glass-indigo btn-sm py-2 px-3 rounded-pill" data-bs-toggle="modal" data-bs-target="#viewMsg{{ $msg->id }}">
                                                    معاينة <i class="bi bi-eye ms-1"></i>
                                                </button>
                                                @if(!$msg->read)
                                                    <form action="{{ route('website.contact-messages.read', $msg) }}" method="POST" class="d-inline">
                                                        @csrf @method('PATCH')
                                                        <button type="submit" class="btn btn-indigo-solid btn-sm py-2 px-3 rounded-pill">
                                                            تمت <i class="bi bi-check-lg ms-1"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                                <form action="{{ route('website.contact-messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                                                    @csrf @method('DELETE')
                                                    <button type="submit" class="btn btn-glass-danger btn-sm py-2 px-3 rounded-pill">
                                                        حذف <i class="bi bi-trash ms-1"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5">
                                            <div class="empty-state-card-lux py-5 mt-5">
                                                <div class="empty-visual-wrapper mx-auto">
                                                    <div class="glow-pulse"></div>
                                                    <i class="bi bi-mailbox2-flag empty-icon-vibe"></i>
                                                </div>
                                                <h5 class="fw-bold text-white mt-4">لا يوجد رسائل حالياً</h5>
                                                <p class="text-slate-400">صندوق الوارد الخاص بك فارغ، لم تتصل أي رسائل جديدة.</p>
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


    {{-- Contact Channels & Settings --}}
    <div class="container-fluid pb-5">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('website.contact-settings.update') }}" method="POST">
                    @csrf
                    <div class="premium-card-dark animate-up">
                        <div class="card-header-lux d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center gap-3">
                                <div class="header-icon bg-indigo-500"><i class="bi bi-gear-fill"></i></div>
                                <h5 class="fw-bold mb-0">إعدادات قنوات التواصل</h5>
                            </div>
                            <button type="submit" class="btn btn-indigo-solid py-2 px-4 rounded-pill fw-bold shadow-indigo">حفظ الإعدادات</button>
                        </div>
                        <div class="card-body-lux p-4 p-md-5">
                            {{-- Header Content --}}
                            <div class="row g-4 mb-5 border-bottom pb-4 border-opacity-10 border-white">
                                <div class="col-md-6">
                                    <label class="label-lux">عنوان القسم</label>
                                    <input type="text" name="contact_section_title" class="field-lux" value="{{ $settings['contact_section_title'] ?? 'معلومات التواصل' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="label-lux">العنوان الفرعي</label>
                                    <input type="text" name="contact_section_subtitle" class="field-lux" value="{{ $settings['contact_section_subtitle'] ?? 'يسعدنا تواصلكم معنا بأي وسيلة تناسبكم' }}">
                                </div>
                            </div>

                            {{-- Contact Cards (4 Cards) --}}
                            <h6 class="fw-bold text-white mb-4"><i class="bi bi-grid-3x3-gap me-2 text-indigo-400"></i> بطاقات التواصل السريع</h6>
                            <div class="row g-4 mb-5">
                                {{-- Phone Card --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="detail-box-lux h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="channel-icon-circle bg-primary bg-opacity-10 text-primary-emphasis"><i class="bi bi-telephone"></i></div>
                                            <span class="fw-bold text-white">بطاقة الهاتف</span>
                                        </div>
                                        <div>
                                            <label class="x-small text-slate-500 fw-bold">القيمة (الرقم)</label>
                                            <input type="text" name="contact_phone_link" class="field-lux" style="direction: ltr; text-align: left;" value="{{ $settings['contact_phone_link'] ?? '01006090616' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- Email Card --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="detail-box-lux h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="channel-icon-circle bg-indigo-500 bg-opacity-10 text-indigo-400"><i class="bi bi-envelope"></i></div>
                                            <span class="fw-bold text-white">بطاقة البريد</span>
                                        </div>
                                        <div>
                                            <label class="x-small text-slate-500 fw-bold">القيمة (الايميل)</label>
                                            <input type="text" name="contact_email_link" class="field-lux py-2" value="{{ $settings['contact_email_link'] ?? 'info@ensan.org' }}">
                                        </div>
                                    </div>
                                </div>

                                {{-- WhatsApp Card --}}
                                <div class="col-xl-4 col-md-6">
                                    <div class="detail-box-lux h-100">
                                        <div class="d-flex align-items-center gap-2 mb-3">
                                            <div class="channel-icon-circle bg-success bg-opacity-10 text-success-emphasis"><i class="bi bi-whatsapp"></i></div>
                                            <span class="fw-bold text-white">بطاقة واتساب</span>
                                        </div>
                                        <div>
                                            <label class="x-small text-slate-500 fw-bold">القيمة (الرقم)</label>
                                            <input type="text" name="contact_whatsapp_link" class="field-lux" style="direction: ltr; text-align: left;" value="{{ $settings['contact_whatsapp_link'] ?? '+20 100 609 0616' }}">
                                        </div>
                                    </div>
                                </div>

                                </div>
                            </div>

                            {{-- Detailed Info Section --}}
                            <h6 class="fw-bold text-white mb-4"><i class="bi bi-info-circle me-2 text-indigo-400"></i> تفاصيل بيانات التواصل (العنوان والمواعيد)</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <label class="label-lux">المقر الرئيسي (العنوان)</label>
                                    <input type="text" name="contact_hq_address" class="field-lux" value="{{ $settings['contact_hq_address'] ?? 'تقسيم 2 ش تونس المتفرع من ش مخزن الزيت' }}">
                                </div>
                                <div class="col-md-6">
                                    <label class="label-lux">تفاصيل العنوان (المبنى/بجوار...)</label>
                                    <input type="text" name="contact_hq_details" class="field-lux" value="{{ $settings['contact_hq_details'] ?? 'العماره اللي جنب كازيون' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="label-lux">الخط الساخن</label>
                                    <input type="text" name="contact_hotline" class="field-lux" style="direction: ltr; text-align: left;" value="{{ $settings['contact_hotline'] ?? '19000' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="label-lux">بريد الدعم الفني</label>
                                    <input type="text" name="contact_support_email" class="field-lux" value="{{ $settings['contact_support_email'] ?? 'support@ensan.org' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="label-lux">أيام العمل</label>
                                    <input type="text" name="contact_working_days" class="field-lux" value="{{ $settings['contact_working_days'] ?? 'السبت - الخميس' }}">
                                </div>
                                <div class="col-md-3">
                                    <label class="label-lux">ساعات العمل</label>
                                    <input type="text" name="contact_working_hours" class="field-lux" value="{{ $settings['contact_working_hours'] ?? '9:00 ص - 5:00 م' }}">
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>


    {{-- Statistics & Gallery Section --}}
    <div class="container-fluid pb-5">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="premium-card-dark animate-up">
                            <div class="card-header-lux d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="header-icon bg-emerald-500"><i class="bi bi-columns-gap"></i></div>
                                    <h5 class="fw-bold mb-0">معرض الصور (صفحة تواصل معنا)</h5>
                                </div>
                                <button type="submit" class="btn btn-indigo-solid py-2 px-4 rounded-pill fw-bold shadow-indigo">حفظ التعديلات</button>
                            </div>
                            <div class="card-body-lux p-4 p-md-5">
                                {{-- Slider Section --}}
                                <h6 class="fw-bold text-white mb-4"><i class="bi bi-images me-2 text-indigo-400"></i> معرض الصور (سلايدر)</h6>
                                <div class="row g-4 mb-5">
                                    @for($i = 1; $i <= 10; $i++)
                                    <div class="col-md-4">
                                        <div class="gallery-card-lux position-relative" id="contactSliderZone{{ $i }}" style="height: 160px; border-color: #6366f1;">
                                            <input type="file" name="contact_slider_{{ $i }}" class="file-hidden" onchange="previewContactSlider(this, {{ $i }})">
                                            
                                            {{-- Delete Button --}}
                                            @if(isset($settings["contact_slider_$i"]))
                                            <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-lg" 
                                                    style="z-index: 20; width: 30px; height: 30px; padding: 0;"
                                                    onclick="event.stopPropagation(); document.getElementById('delete_contact_slider_{{ $i }}').value='1'; this.closest('.gallery-card-lux').querySelector('.gallery-preview-wrapper').innerHTML='<div class=\'gallery-placeholder\'><i class=\'bi bi-image fs-3 text-indigo-400\'></i><p class=\'x-small mb-0 mt-2\'>محذوف - حفظ للتنفيذ</p></div>'; this.remove();">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                            @endif
                                            <input type="hidden" name="delete_contact_slider_{{ $i }}" id="delete_contact_slider_{{ $i }}" value="0">

                                            <div class="gallery-preview-wrapper" id="contactSliderPreview{{ $i }}">
                                                @if(isset($settings["contact_slider_$i"]))
                                                    <img src="{{ asset('storage/' . $settings["contact_slider_$i"]) }}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover">
                                                @else
                                                    <div class="gallery-placeholder">
                                                        <i class="bi bi-image fs-3 text-indigo-400"></i>
                                                        <p class="x-small mb-0 mt-2">شريحة {{ $i }}</p>
                                                    </div>
                                                @endif
                                            </div>
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
    
    <script>
    function previewContactSlider(input, id) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const container = document.getElementById('contactSliderPreview' + id);
                container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 h-100 w-100 object-fit-cover">`;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
    </script>

    @foreach($messages as $msg)
    <div class="modal fade" id="viewMsg{{ $msg->id }}" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content premium-modal-dark shadow-2xl">
                <div class="modal-header border-0 p-4 pb-0">
                    <h5 class="modal-title fw-bold">تفاصيل الرسالة الواردة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="info-strip-premium mb-4">
                        <div class="strip-avatar bg-indigo-600">
                            {{ mb_substr($msg->name, 0, 1) }}
                        </div>
                        <div class="text-end">
                            <h5 class="fw-bold text-white mb-1">{{ $msg->name }}</h5>
                            <p class="mb-0 x-small text-slate-400">{{ $msg->email }} | {{ $msg->phone }}</p>
                        </div>
                    </div>

                    <div class="detail-box-lux mb-4 text-end">
                        <label class="detail-label-sleek">نوع الاستفسار</label>
                        <div class="detail-content-sleek fs-5 fw-bold text-white">
                            @if(($msg->subject ?? '') == 'General')
                                عام
                            @else
                                {{ $msg->subject ?? 'بلا عنوان' }}
                            @endif
                        </div>
                    </div>

                    <div class="detail-box-lux mb-4 text-end">
                        <label class="detail-label-sleek">الرسالة</label>
                        <div class="detail-content-sleek py-3" style="white-space: pre-wrap; line-height: 1.8;">{{ $msg->message }}</div>
                    </div>

                    @if($msg->image_path)
                    <div class="detail-box-lux text-end">
                        <label class="detail-label-sleek">المرفقات</label>
                        <div class="mt-3">
                            <a href="{{ $msg->image_url }}" target="_blank" class="d-block">
                                <img src="{{ $msg->image_url }}" class="img-fluid rounded-4 shadow-lg border border-opacity-10 w-100" style="max-height: 400px; object-fit: contain; background: #000;">
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="modal-footer border-0 p-4 gap-2">
                    <form action="{{ route('website.contact-messages.destroy', $msg) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذه الرسالة؟')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-glass-danger py-3 px-4 rounded-pill fw-bold">حذف الرسالة <i class="bi bi-trash ms-1"></i></button>
                    </form>
                    <a href="mailto:{{ $msg->email }}" class="btn btn-indigo-solid flex-grow-1 py-3 rounded-pill fw-bold">الرد عبر البريد <i class="bi bi-reply-fill ms-1"></i></a>
                    <button type="button" class="btn btn-glass-indigo py-3 px-4 rounded-pill fw-bold" data-bs-dismiss="modal">إغلاق</button>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
    :root {
        --dark-bg: #0b0e14;
        --card-dark: #1a2332;
        --indigo-500: #6366f1;
        --indigo-600: #4f46e5;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --slate-600: #475569;
    }

    body { background-color: var(--dark-bg) !important; font-family: 'Tajawal', sans-serif; }

    /* Hero Styling */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 120px;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        border-radius: 0 0 60px 60px;
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.4);
    }
    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.3; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; background-image: url('data:image/svg+xml,...'); }

    .hero-content-wrapper { position: relative; z-index: 5; padding: 0 5%; }
    .badge-glass-premium { 
        background: rgba(255, 255, 255, 0.1); 
        backdrop-filter: blur(12px); 
        border: 1px solid rgba(255,255,255,0.1);
        padding: 8px 18px; border-radius: 100px; color: #e0e7ff; font-weight: 700; font-size: 0.85rem;
    }

    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }

    /* Action Buttons */
    .btn-glass-indigo {
        background: rgba(99, 102, 241, 0.1);
        color: #818cf8;
        border: 1px solid rgba(99, 102, 241, 0.2);
        backdrop-filter: blur(8px);
        font-weight: 700;
        transition: 0.4s;
    }
    .btn-glass-indigo:hover { background: rgba(99, 102, 241, 0.2); color: white; transform: translateY(-3px); }
    
    .btn-indigo-solid {
        background: var(--indigo-600);
        color: white; border: none; font-weight: 700;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        transition: 0.4s;
    }
    .btn-indigo-solid:hover { background: #4338ca; color: white; transform: translateY(-3px); box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4); }

    .btn-glass-danger {
        background: rgba(ef, 68, 68, 0.1);
        color: #f87171;
        border: 1px solid rgba(239, 68, 68, 0.2);
        backdrop-filter: blur(8px);
        font-weight: 700;
        transition: 0.4s;
    }
    .btn-glass-danger:hover { background: rgba(239, 68, 68, 0.2); color: white; transform: translateY(-3px); }

    /* Content Area */
    .content-shift-up { margin-top: -60px; position: relative; z-index: 20; padding: 0 5%; }

    /* Cards */
    .premium-card-dark {
        background: var(--card-dark);
        border-radius: 35px;
        border: 1px solid rgba(255,255,255,0.05);
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        overflow: hidden;
    }
    .card-header-lux { padding: 25px 30px; border-bottom: 1px solid rgba(255,255,255,0.05); background: rgba(255,255,255,0.01); }
    .header-icon { width: 44px; height: 44px; border-radius: 12px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem; }
    .bg-indigo-500 { background: var(--indigo-500); }

    /* Tables */
    .premium-table-dark { background: transparent; }
    .premium-table-dark thead th { 
        background: rgba(0,0,0,0.2); color: var(--slate-400); 
        text-transform: uppercase; font-size: 0.75rem; font-weight: 800; border: none; padding: 20px;
    }
    .premium-table-dark tbody td { border-bottom: 1px solid rgba(255,255,255,0.03); padding: 20px; color: #f8fafc; }
    .bg-unread { background: rgba(99, 102, 241, 0.05) !important; }
    .bg-unread td { border-bottom-color: rgba(99, 102, 241, 0.1) !important; }

    .user-avatar-mini {
        width: 45px; height: 45px; border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 1.2rem;
    }
    .bg-indigo-900 { background: #1e1b4b; }

    /* Status Badges */
    .badge-status { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; border: 1px solid transparent; }
    .badge-status.pending { background: rgba(245, 158, 11, 0.1); color: #f59e0b; border-color: rgba(245, 158, 11, 0.2); }
    .badge-status.approved { background: rgba(16, 185, 129, 0.1); color: #10b981; border-color: rgba(16, 185, 129, 0.2); }

    /* Empty State */
    .empty-state-card-lux { text-align: center; }
    .empty-visual-wrapper { position: relative; width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; }
    .empty-icon-vibe { font-size: 3rem; color: var(--slate-600); position: relative; z-index: 5; }
    .glow-pulse {
        position: absolute; inset: 0; background: var(--indigo-500); border-radius: 50%;
        filter: blur(20px); opacity: 0.2; animation: pulseGlow 3s infinite;
    }
    @keyframes pulseGlow { 0% { opacity: 0.1; transform: scale(0.8); } 50% { opacity: 0.3; transform: scale(1.2); } 100% { opacity: 0.1; transform: scale(0.8); } }

    /* Modal Styling */
    .premium-modal-dark { background: var(--card-dark); border: 1px solid rgba(255,255,255,0.08); border-radius: 40px; }
    .info-strip-premium { 
        background: rgba(255, 255, 255, 0.03); 
        padding: 20px; border-radius: 25px; display: flex; align-items: center; justify-content: space-between;
    }
    .strip-avatar { width: 55px; height: 55px; border-radius: 18px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.4rem; font-weight: 800; }

    .detail-box-lux { background: rgba(0,0,0,0.2); padding: 20px; border-radius: 25px; border: 1px solid rgba(255,255,255,0.03); }
    .detail-label-sleek { font-size: 0.75rem; color: var(--slate-500); font-weight: 800; margin-bottom: 10px; display: block; }
    .detail-content-sleek { color: var(--slate-300); }

    .channel-icon-circle { width: 45px; height: 45px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; }
    .field-lux {
        width: 100%; background: #0b0e14; border: 1px solid #2d3748;
        border-radius: 14px; padding: 12px 20px; color: white; font-weight: 600; transition: 0.3s;
    }
    .field-lux:focus { border-color: #6366f1; outline: none; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
    .label-lux { color: var(--slate-400); font-weight: 700; margin-bottom: 8px; display: block; }

    /* General Utilities */
    .x-small { font-size: 0.75rem; }
    .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5); }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(35px); } to { opacity: 1; transform: translateY(0); } }

    @media (max-width: 991px) {
        .premium-hero-sleek { padding: 60px 0 80px; border-radius: 0 0 35px 35px; }
        .display-3 { font-size: 2.2rem; }
        .text-end { text-align: center !important; }
        .text-start { text-align: center !important; }
        .justify-content-end { justify-content: center !important; }
        .align-items-start { align-items: center !important; }
        .ms-0.me-auto { margin-left: auto !important; margin-right: auto !important; }
    }

    /* Fixed Gallery Slider Styles */
    .gallery-card-lux {
        position: relative;
        background: rgba(0,0,0,0.2);
        border: 2px dashed #334155;
        border-radius: 20px;
        overflow: hidden;
        transition: 0.3s;
        cursor: pointer;
        height: 160px;
    }
    .gallery-card-lux:hover {
        border-color: var(--indigo-500);
        background: rgba(99, 102, 241, 0.05);
    }
    
    .file-hidden {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 10;
    }
    
    .gallery-preview-wrapper {
        width: 100%;
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        pointer-events: none;
    }
    
    .gallery-placeholder {
        text-align: center;
        color: var(--slate-500);
    }
</style>
@endsection

