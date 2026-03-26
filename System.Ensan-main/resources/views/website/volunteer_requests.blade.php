@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="volunteer-requests-page">
    {{-- Dynamic Hero Section --}}
    <div class="premium-hero-sleek">
        <div class="hero-bg-visuals">
            <div class="glow-orb-1"></div>
            <div class="glow-orb-2"></div>
            <div class="noise-overlay"></div>
        </div>
        
        <div class="hero-content-wrapper container-fluid">
            <div class="row align-items-center">
                <div class="col-lg-8 animate-reveal-right text-end">
                    <nav aria-label="breadcrumb" class="mb-4 d-flex justify-content-end">
                        <ol class="breadcrumb mb-0">
                            <li class="breadcrumb-item"><a href="{{ route('dashboard.index') }}" class="text-white-50 decoration-none">لوحة التحكم</a></li>
                            <li class="breadcrumb-item active text-white" aria-current="page">تطوع معنا</li>
                        </ol>
                    </nav>
                    <div class="d-flex align-items-center justify-content-end gap-3 mb-3">
                        <div class="badge-glass-premium">
                            <i class="bi bi-person-heart me-2"></i> بناء مجتمع إنسان المعطاء
                        </div>
                    </div>
                    <h1 class="display-3 fw-800 text-white mb-3 text-end">تطوع معنا وكن جزءاً من التغيير</h1>
                    <p class="lead text-white-50 mb-0 max-w-600 ms-auto me-0 text-end">
                        {{ $settings['volunteer_description'] ?? 'نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي .' }}
                    </p>
                </div>
                <div class="col-lg-4 text-start mt-4 mt-lg-0 animate-reveal-left">
                    {{-- Button removed as form is now on page --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content Grid --}}
    <div class="container-fluid py-5 content-shift-up">
        <div class="row g-4">
            @forelse($requests as $request)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="request-card-premium animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="d-flex justify-content-between align-items-start w-100">
                        <!-- Status Badge (Left due to RTL alignment) -->
                        <div class="card-status-wrapper flex-shrink-0 mt-1">
                            @php
                                $statusMap = [
                                    'new' => ['label' => 'طلب جديد', 'class' => 'status-new'],
                                    'contacted' => ['label' => 'تم التواصل', 'class' => 'status-contacted'],
                                    'accepted' => ['label' => 'مقبول', 'class' => 'status-accepted'],
                                    'rejected' => ['label' => 'مرفوض', 'class' => 'status-rejected'],
                                ];
                                $currStatus = $statusMap[$request->status] ?? ['label' => $request->status, 'class' => 'status-new'];
                            @endphp
                            <span class="badge-status-lux {{ $currStatus['class'] }}">
                                <span class="status-dot"></span>
                                {{ $currStatus['label'] }}
                            </span>
                        </div>

                        <!-- User Info (Right) -->
                        <div class="card-top-vibe flex-grow-1 ps-2" style="min-width: 0;">
                            <div class="user-main-info text-end flex-grow-1" style="min-width: 0;">
                                <h5 class="fw-bold mb-1 text-white ls-tight text-truncate d-block w-100" style="max-width: 170px; margin-right: auto;">{{ $request->name }}</h5>
                                <p class="x-small text-slate-500 mb-0 d-flex align-items-center justify-content-end gap-2 text-nowrap">
                                    <span>{{ $request->created_at->translatedFormat('d M Y') }}</span>
                                    <i class="bi bi-calendar3 text-indigo-400"></i>
                                </p>
                            </div>
                            <div class="user-avatar-lux shadow-2xl flex-shrink-0 ms-3">
                                <span class="avatar-initials">{{ mb_substr($request->name, 0, 1) }}</span>
                                <div class="avatar-glow"></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-mid-vibe mt-4">
                        <div class="interest-pill-lux mb-4">
                            <i class="bi bi-lightning-charge-fill text-yellow-400"></i>
                            <span class="fw-800">{{ $request->area_of_interest ?? 'رغبة عامة' }}</span>
                        </div>
                        
                        <div class="quick-contact-stack">
                            <div class="contact-item-lux-v2">
                                <span class="contact-text">{{ $request->phone }}</span>
                                <div class="contact-icon-box bg-primary"><i class="bi bi-phone"></i></div>
                            </div>
                            <div class="contact-item-lux-v2">
                                <span class="contact-text text-break">{{ $request->email }}</span>
                                <div class="contact-icon-box bg-indigo-500"><i class="bi bi-envelope"></i></div>
                            </div>
                        </div>
                    </div>

                    <div class="card-bottom-vibe mt-4">
                        <div class="d-flex gap-2">
                             <button class="btn btn-primary-lux flex-grow-1 py-3" data-bs-toggle="modal" data-bs-target="#viewReq{{ $request->id }}">
                                <i class="bi bi-eye-fill me-2"></i> عرض الطلب
                            </button>
                            @if($request->cv_path)
                                @if($request->cvExists())
                                    <a href="{{ route('website.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn btn-glass-lux px-4">
                                        <i class="bi bi-file-earmark-pdf-fill text-danger me-1"></i> CV
                                    </a>
                                @else
                                    <span class="btn btn-glass-lux px-4" style="opacity: 0.5; cursor: not-allowed;" title="الملف غير موجود بالخادم">
                                        <i class="bi bi-file-earmark-x text-muted me-1"></i> مفقود
                                    </span>
                                @endif
                            @endif
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('website.volunteer-requests.destroy', $request) }}" method="POST" class="d-block w-100">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline-danger-lux w-100 py-2 fs-7" onclick="return confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟')">
                                    <i class="bi bi-trash3 me-2"></i> حذف الطلب نهائياً
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12">
                <div class="empty-state-card-lux animate-up mt-5">
                    <div class="empty-visual-wrapper">
                        <div class="glow-pulse"></div>
                        <i class="bi bi-mailbox2-flag empty-icon-vibe"></i>
                    </div>
                    <h3 class="fw-bold text-white mt-4">صندوق الطلبات فارغ</h3>
                    <p class="text-slate-400 max-w-400 mx-auto">لم تصلنا أي طلبات تطوع جديدة حالياً. سيتم إخطارك فور وصول أي طلب جديد عبر النظام.</p>
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Detail Modal --}}
@foreach($requests as $request)
<div class="modal fade" id="viewReq{{ $request->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content premium-modal-dark border-0" style="background-color: #0b0e14 !important; opacity: 1 !important;">
            <div class="modal-header border-0 p-4 bg-primary text-white" style="background-color: #0066ff !important; border-radius: 0;">
                <h5 class="modal-title fw-bold text-white">تفاصيل طلب التطوع</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-2" style="background-color: #0b0e14 !important;">
                {{-- Profile Strip (Enhanced) --}}
                <div class="profile-strip-lux mb-4 shadow-xl">
                    <div class="strip-avatar-glow shadow-indigo">
                        <i class="bi bi-person-bounding-box text-white fs-3"></i>
                    </div>
                    <div class="ms-3 text-end flex-grow-1">
                        <h3 class="fw-800 mb-1 text-white tracking-tight">{{ $request->name }}</h3>
                        <div class="d-flex align-items-center justify-content-end gap-3 x-small text-slate-400">
                            <span class="d-flex align-items-center gap-1"><i class="bi bi-calendar3 text-indigo-400"></i> {{ $request->created_at->format('d M Y') }}</span>
                            <span class="badge-status-dot {{ $request->status }}"></span>
                            <span class="text-indigo-300 fw-bold">{{ $currStatus['label'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Basic Info Section --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="detail-box-lux shadow-indigo-sm">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <label class="detail-label-sleek m-0"><i class="bi bi-card-text"></i> الرقم القومي</label>
                                @if($request->id_card_path)
                                <a href="{{ Storage::url($request->id_card_path) }}" target="_blank" class="badge bg-indigo-500 bg-opacity-10 text-indigo-300 decoration-none small px-2 py-1">
                                    <i class="bi bi-image me-1"></i> عرض البطاقة
                                </a>
                                @endif
                            </div>
                            <div class="detail-content-sleek fs-4 text-white font-outfit">{{ $request->national_id ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box-lux shadow-indigo-sm">
                            <label class="detail-label-sleek"><i class="bi bi-calendar-event"></i> تاريخ الميلاد</label>
                            <div class="detail-content-sleek fs-4 text-white font-outfit">{{ $request->birth_date ?? 'غير متوفر' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                            <label class="detail-label-sleek">النوع</label>
                            <div class="detail-content-sleek fw-bold text-white fs-5">
                                {{ $request->gender == 'male' ? 'ذكر' : ($request->gender == 'female' ? 'أنثى' : ($request->gender ?? '-')) }}
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                            <label class="detail-label-sleek">رقم التليفون (01xxxxxxxxx)</label>
                            <div class="detail-content-sleek fw-bold text-white fs-4 font-outfit text-indigo-300">{{ $request->phone ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-12">
                        <div class="detail-box-lux shadow-sm border-white border-opacity-5">
                            <div class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-envelope-at text-indigo-400"></i>
                                <label class="detail-label-sleek mb-0">البريد الإلكتروني</label>
                            </div>
                            <div class="detail-content-sleek text-break fw-bold text-white fs-6 font-outfit">{{ $request->email ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                            <label class="detail-label-sleek">العنوان الدائم</label>
                            <div class="detail-content-sleek fw-bold text-white lh-base">{{ $request->address ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                            <label class="detail-label-sleek">العنوان الحالي</label>
                            <div class="detail-content-sleek fw-bold text-white lh-base">{{ $request->current_address ?? '-' }}</div>
                        </div>
                    </div>
                </div>

                {{-- Academic Info Section --}}
                <div class="detail-section-lux mt-5">
                    <h6 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <span class="section-indicator-indigo"></span>
                        معلومات الدراسة والعمل
                    </h6>
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                                <label class="detail-label-sleek">المؤهل / المرحلة</label>
                                <div class="detail-content-sleek fw-bold text-white">
                                    {{ $request->education_level == 'student' ? 'طالب' : ($request->education_level == 'graduated' ? 'خريج' : ($request->education_level ?? '-')) }}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                                <label class="detail-label-sleek">الكلية</label>
                                <div class="detail-content-sleek fw-bold text-white">{{ $request->faculty ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                                <label class="detail-label-sleek">الجامعة</label>
                                <div class="detail-content-sleek fw-bold text-white">{{ $request->university ?? '-' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                                <label class="detail-label-sleek">الوظيفة الحالية</label>
                                <div class="detail-content-sleek fw-bold text-white">{{ $request->current_job ?? '-' }}</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="detail-box-lux h-100 shadow-sm border-white border-opacity-5">
                                <label class="detail-label-sleek">خبرة تطوعية سابقة</label>
                                <div class="detail-content-sleek fw-bold text-white">
                                    {{ ($request->previous_experience == '1' || strtolower($request->previous_experience) == 'yes') ? 'نعم' : (($request->previous_experience == '0' || strtolower($request->previous_experience) == 'no') ? 'لا' : ($request->previous_experience ?? '-')) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Volunteer Details Section --}}
                <div class="detail-section-lux mt-4">
                    <h6 class="fw-bold text-white mb-3 d-flex align-items-center gap-2">
                        <span class="section-indicator-indigo"></span>
                        بيانات التطوع
                    </h6>
                    <div class="col-md-12 mb-3">
                        <div class="detail-box-lux border-white border-opacity-5">
                            <label class="detail-label-sleek">المهارات والاهتمامات</label>
                            <div class="detail-content-sleek">
                                <span class="badge bg-indigo-500 bg-opacity-20 text-indigo-300 fs-7 mb-2 px-3 py-2 rounded-pill d-inline-block">
                                    مجال الاهتمام: {{ $request->area_of_interest ?? 'عام' }}
                                </span>
                                <p class="text-white fw-bold mb-0 lh-lg">{{ $request->skills ?? '-' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <div class="detail-box-lux border-white border-opacity-5">
                            <label class="detail-label-sleek">سبب الرغبة في التطوع</label>
                            <div class="detail-content-sleek text-white fw-bold lh-lg">{{ $request->goal ?? '-' }}</div>
                        </div>
                    </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="detail-box-lux h-100 border-white border-opacity-5">
                            <label class="detail-label-sleek">توقعاتك من التطوع</label>
                            <div class="detail-content-sleek text-white fw-bold lh-lg">{{ $request->expectations ?? '-' }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="detail-box-lux h-100 border-white border-opacity-5">
                            <label class="detail-label-sleek">ساعات التطوع المتاحة</label>
                            <div class="detail-content-sleek text-white fw-bold lh-lg">{{ $request->volunteer_hours ?? '-' }}</div>
                        </div>
                    </div>
                </div>
            </div>

                <div class="detail-box-lux mb-4 border-white border-opacity-5">
                    <label class="detail-label-sleek">الرسالة الدافعية / ملاحظات المتقدم</label>
                    <div class="detail-content-sleek text-white-50 italic">
                        "{{ $request->message ?? 'لا يوجد نص رسالة مرفق مع هذا الطلب.' }}"
                    </div>
                </div>

                <div class="d-grid mt-4">
                    <button type="button" class="btn btn-outline-secondary py-3 rounded-pill fw-bold text-white border-opacity-25" data-bs-dismiss="modal">إغلاق المعاينة</button>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 bg-slate-900 border-top border-white border-opacity-10">

                @if($request->cv_path)
                    @if($request->cvExists())
                        <a href="{{ route('website.volunteer-requests.cv', $request->id) }}" target="_blank" class="btn btn-indigo-solid px-5 py-2 rounded-pill ms-auto">
                            <i class="bi bi-file-earmark-pdf me-2"></i> عرض السيرة الذاتية (CV)
                        </a>
                    @else
                        <button disabled class="btn btn-outline-secondary px-5 py-2 rounded-pill ms-auto" style="opacity: 0.6; cursor: not-allowed;" title="ملف السيرة الذاتية غير موجود فعلياً على الخادم">
                            <i class="bi bi-file-earmark-x me-2"></i> ملف الـ CV مفقود من الخادم
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
@endforeach

    {{-- Management Section (On Page Level) --}}
    <div class="container-fluid pb-5 mt-5 border-top border-white border-opacity-10 pt-5">
        <form action="{{ route('website.volunteer-content.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            {{-- Page Level Header --}}
            <div class="d-flex justify-content-between align-items-center mb-5 animate-up">
                <div class="d-flex align-items-center gap-3">
                    <div class="header-icon-box-lux bg-indigo-500" style="width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white;">
                        <i class="bi bi-layout-text-window-reverse"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-0 text-white">تخصيص واجهة تطوع معنا</h4>
                        <p class="text-indigo-200 small mb-0 mt-1">تحديث المحتوى المرئي والإحصائيات لواجهة الموقع</p>
                    </div>
                </div>
                <button type="submit" class="btn btn-indigo-solid py-2 px-4 rounded-pill fw-bold shadow-indigo" style="background: var(--indigo-primary); color: white; border: none;">
                    <i class="bi bi-check2-all me-2"></i> حفظ التعديلات
                </button>
            </div>

            {{-- Form Grid --}}
            <div class="row g-5 animate-up">
                <div class="col-lg-7">
                    <h6 class="fw-bold text-white mb-4"><i class="bi bi-text-paragraph text-indigo-400 me-2"></i>النصوص والوصف</h6>
                    <div class="form-group-lux mb-4">
                        <label class="label-lux fw-bold text-white mb-2">عنوان الهيرو الرئيسي</label>
                        <input type="text" name="volunteer_title" class="field-lux form-control form-control-lg bg-slate-900 border-white border-opacity-10 text-white" value="{{ $settings['volunteer_title'] ?? 'تطوع معنا وكن جزءاً من التغيير' }}">
                    </div>
                    <div class="form-group-lux mb-4">
                        <label class="label-lux fw-bold text-white mb-2">نص الوصف</label>
                        <textarea name="volunteer_description" class="field-lux form-control bg-slate-900 border-white border-opacity-10 text-white scroll-thin" rows="4">{{ $settings['volunteer_description'] ?? 'نستقبل المتطوعين في مجالات مختلفة: تنظيم الحملات، التوزيع الميداني، التصميم، التسويق الرقمي .' }}</textarea>
                    </div>
                    
                    <h6 class="fw-bold text-white mb-4 mt-5"><i class="bi bi-bar-chart-fill text-indigo-400 me-2"></i>الإحصائيات والأرقام</h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="label-lux text-slate-300 mb-2">متطوع مسجل</label>
                            <input type="text" name="volunteer_stats_volunteers" class="form-control bg-slate-900 border-white border-opacity-10 text-white text-center fw-bold" value="{{ $settings['volunteer_stats_volunteers'] ?? '235112' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux text-slate-300 mb-2">ساعة تطوعية</label>
                            <input type="text" name="volunteer_stats_hours" class="form-control bg-slate-900 border-white border-opacity-10 text-white text-center fw-bold" value="{{ $settings['volunteer_stats_hours'] ?? '512525,00+' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux text-slate-300 mb-2">مشروع تطوعي</label>
                            <input type="text" name="volunteer_stats_projects" class="form-control bg-slate-900 border-white border-opacity-10 text-white text-center fw-bold" value="{{ $settings['volunteer_stats_projects'] ?? '+50525' }}">
                        </div>
                        <div class="col-md-6">
                            <label class="label-lux text-slate-300 mb-2">عدد الفروع</label>
                            <input type="text" name="volunteer_stats_branches" class="form-control bg-slate-900 border-white border-opacity-10 text-white text-center fw-bold" value="{{ $settings['volunteer_stats_branches'] ?? '45212' }}">
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <h6 class="fw-bold text-white mb-4"><i class="bi bi-image text-indigo-400 me-2"></i>سلايدر الهيرو الحالية (صورة)</h6>
                    <div class="upload-zone-lux position-relative" id="heroDropZone" style="background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.1); border-radius: 20px; padding: 20px; text-align: center;">
                        <input type="file" name="volunteer_hero_image" class="file-hidden position-absolute w-100 h-100 opacity-0" style="left: 0; top: 0; cursor: pointer; z-index: 10;" onchange="previewHero(this)">
                        
                        {{-- Delete Button --}}
                        @if(!empty($settings['volunteer_hero_image']) && \Illuminate\Support\Str::contains($settings['volunteer_hero_image'], '/'))
                        <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-lg" 
                                style="z-index: 20; width: 30px; height: 30px; padding: 0;"
                                onclick="event.stopPropagation(); document.getElementById('delete_volunteer_hero_image').value='1'; this.closest('.upload-zone-lux').querySelector('.preview-area-lux').innerHTML='<div class=\'placeholder-vibe py-5\'><i class=\'bi bi-cloud-arrow-up-fill fs-1 text-indigo-400\'></i><p class=\'small text-slate-400 mt-2\'>محذوف - حفظ للتنفيذ</p></div>'; this.remove();">
                            <i class="bi bi-trash"></i>
                        </button>
                        @endif
                        <input type="hidden" name="delete_volunteer_hero_image" id="delete_volunteer_hero_image" value="0">

                        <div class="preview-area-lux">
                            @if(!empty($settings['volunteer_hero_image']) && \Illuminate\Support\Str::contains($settings['volunteer_hero_image'], '/'))
                                <img src="{{ asset('storage/' . $settings['volunteer_hero_image']) }}" class="img-fluid rounded-4 shadow-lg w-100" style="max-height: 300px; object-fit: cover;" id="heroPrev2">
                            @else
                                <div class="placeholder-vibe py-5">
                                    <i class="bi bi-cloud-arrow-up-fill fs-1 text-indigo-400"></i>
                                    <p class="small text-slate-400 mt-2">انقر أو اسحب لاختيار صورة</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-5">
                    <h6 class="fw-bold text-white mb-4"><i class="bi bi-images text-indigo-400 me-2"></i>صور السلايدر (بديلة للهيرو)</h6>
                    <div class="row g-3">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="col-md-2 col-sm-4 col-6">
                            <div class="upload-zone-lux position-relative" style="height: 120px; background: rgba(255,255,255,0.02); border: 2px dashed rgba(255,255,255,0.1); border-radius: 16px; display: flex; align-items: center; justify-content: center; overflow: hidden;">
                                <input type="file" name="volunteer_slider_{{ $i }}" class="file-hidden position-absolute w-100 h-100 opacity-0" style="left: 0; top: 0; cursor: pointer; z-index: 10;" onchange="previewVolSlider(this, {{ $i }})">
                                
                                {{-- Delete Button --}}
                                @if(!empty($settings["volunteer_slider_$i"]) && \Illuminate\Support\Str::contains($settings["volunteer_slider_$i"], '/'))
                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-1 shadow-lg" 
                                        style="z-index: 20; width: 24px; height: 24px; padding: 0; font-size: 10px;"
                                        onclick="event.stopPropagation(); document.getElementById('delete_volunteer_slider_{{ $i }}').value='1'; this.closest('.upload-zone-lux').querySelector('.preview-area-lux').innerHTML='<div class=\'text-center\'><i class=\'bi bi-images fs-3 text-slate-600 mb-1\'></i><p class=\'x-small text-slate-500 mb-0\'>محذوف</p></div>'; this.remove();">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                                <input type="hidden" name="delete_volunteer_slider_{{ $i }}" id="delete_volunteer_slider_{{ $i }}" value="0">

                                <div class="preview-area-lux w-100 h-100 d-flex align-items-center justify-content-center" id="volSliderPrev{{ $i }}">
                                    @if(!empty($settings["volunteer_slider_$i"]) && \Illuminate\Support\Str::contains($settings["volunteer_slider_$i"], '/'))
                                        <img src="{{ asset('storage/' . $settings["volunteer_slider_$i"]) }}" class="img-fluid w-100 h-100 object-fit-cover">
                                    @else
                                        <div class="text-center">
                                            <i class="bi bi-images fs-3 text-slate-600 mb-1"></i>
                                            <p class="x-small text-slate-500 mb-0">صورة {{ $i }}</p>
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

<script>
function previewHero(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('heroPrev2');
            if (preview) {
                preview.src = e.target.result;
            } else {
                // If it was a placeholder, replace the placeholder content
                const container = document.querySelector('.preview-area-lux');
                if (container)
                    container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-4 shadow-lg" id="heroPrev2">`;
            }
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function previewVolSlider(input, index) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            const container = document.getElementById('volSliderPrev' + index);
            container.innerHTML = `<img src="${e.target.result}" class="img-fluid rounded-3 h-100 object-fit-cover">`;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<style>
    :root {
        --indigo-primary: #6366f1;
        --indigo-dark: #4f46e5;
        --dark-bg: #0b0e14;
        --card-dark: #1a1f2e;
        --slate-900: #0f172a;
        --slate-800: #1e293b;
        --slate-400: #94a3b8;
        --slate-500: #64748b;
        --gold-premium: #fbbf24;
    }

    body {
        background-color: var(--dark-bg) !important;
        font-family: 'Tajawal', sans-serif;
        color: #f8fafc;
    }

    .volunteer-requests-page {
        min-height: 100vh;
    }

    /* Premium Hero Sleek */
    .premium-hero-sleek {
        position: relative;
        padding: 100px 0 120px;
        background: linear-gradient(135deg, #1e1b4b 0%, #312e81 100%);
        border-radius: 0 0 60px 60px;
        overflow: hidden;
        z-index: 10;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .hero-bg-visuals div { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.4; pointer-events: none; }
    .glow-orb-1 { width: 400px; height: 400px; background: var(--indigo-primary); top: -100px; right: -50px; }
    .glow-orb-2 { width: 300px; height: 300px; background: #c084fc; bottom: -150px; left: -50px; }
    .noise-overlay { position: absolute; inset: 0; opacity: 0.05; background-image: url('data:image/svg+xml,...'); }

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

    .btn-action-glow {
        background: var(--indigo-primary);
        color: white;
        border: none;
        font-weight: 700;
        box-shadow: 0 0 20px rgba(99, 102, 241, 0.4);
        transition: 0.4s;
    }
    .btn-action-glow:hover {
        background: var(--indigo-dark);
        transform: translateY(-5px);
        box-shadow: 0 15px 30px rgba(99, 102, 241, 0.6);
        color: white;
    }

    /* Content Area */
    .content-shift-up { margin-top: -30px; position: relative; z-index: 20; padding: 0 5%; }

    /* Premium Request Card (Solid Dark) */
    .request-card-premium {
        background: #0f172a; /* Solid Slate 900 */
        border-radius: 40px;
        padding: 35px;
        border: 1px solid rgba(255,255,255,0.05);
        transition: 0.5s cubic-bezier(0.19, 1, 0.22, 1);
        height: 100%;
        display: flex;
        flex-direction: column;
        position: relative;
        overflow: hidden;
    }

    .request-card-premium::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 100px;
        background: linear-gradient(to bottom, rgba(99, 102, 241, 0.05), transparent);
        pointer-events: none;
    }

    .request-card-premium:hover {
        transform: translateY(-15px);
        border-color: rgba(99, 102, 241, 0.4);
        box-shadow: 0 40px 80px -20px rgba(0, 0, 0, 0.6), 0 0 50px rgba(99, 102, 241, 0.1);
    }

    .card-status-pill { position: relative; } /* Deprecated */
    
    .badge-status-lux {
        background: rgba(255, 255, 255, 0.03);
        backdrop-filter: blur(5px);
        padding: 6px 16px;
        border-radius: 100px;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1px solid rgba(255,255,255,0.05);
        white-space: nowrap;
    }

    .status-dot { width: 6px; height: 6px; border-radius: 50%; }
    .status-new { color: #3b82f6; } .status-new .status-dot { background: #3b82f6; box-shadow: 0 0 10px #3b82f6; }
    .status-contacted { color: #a855f7; } .status-contacted .status-dot { background: #a855f7; box-shadow: 0 0 10px #a855f7; }
    .status-accepted { color: #10b981; } .status-accepted .status-dot { background: #10b981; box-shadow: 0 0 10px #10b981; }
    .status-rejected { color: #ef4444; } .status-rejected .status-dot { background: #ef4444; box-shadow: 0 0 10px #ef4444; }

    .card-top-vibe { display: flex; align-items: flex-start; justify-content: flex-end; }
    
    .user-avatar-lux {
        width: 70px;
        height: 70px;
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        z-index: 2;
    }

    .avatar-glow {
        position: absolute;
        inset: -10px;
        background: var(--indigo-primary);
        filter: blur(25px);
        opacity: 0.2;
        border-radius: 30px;
        z-index: -1;
    }

    .avatar-initials { color: white; font-weight: 800; font-size: 1.6rem; font-family: 'Outfit'; }

    .interest-pill-lux {
        background: rgba(255, 255, 255, 0.02);
        padding: 14px 20px;
        border-radius: 20px;
        color: #f8fafc;
        font-size: 0.9rem;
        border: 1px solid rgba(255,255,255,0.04);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
        transition: 0.3s;
    }
    .request-card-premium:hover .interest-pill-lux { background: rgba(99, 102, 241, 0.05); border-color: rgba(99, 102, 241, 0.2); }

    .quick-contact-stack { display: flex; flex-direction: column; gap: 12px; }
    .contact-item-lux-v2 {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 15px;
        padding: 5px 0;
    }
    .contact-icon-box {
        width: 32px;
        height: 32px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
        background-opacity: 0.15;
    }
    .contact-text { font-size: 0.85rem; color: var(--slate-400); font-weight: 500; font-family: 'Outfit', sans-serif; }

    .btn-primary-lux {
        background: linear-gradient(to right, var(--indigo-primary), var(--indigo-dark));
        color: white;
        border: none;
        border-radius: 18px;
        font-weight: 700;
        transition: 0.4s;
        box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
    }
    .btn-primary-lux:hover {
        transform: scale(1.02);
        box-shadow: 0 15px 30px rgba(79, 70, 229, 0.4);
        color: white;
    }

    .btn-glass-lux {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255,255,255,0.08);
        border-radius: 18px;
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: 0.3s;
    }
    .btn-glass-lux:hover { background: rgba(255, 255, 255, 0.08); transform: translateY(-3px); color: white; }

    .btn-outline-danger-lux {
        background: transparent;
        border: 1px solid rgba(239, 68, 68, 0.2);
        color: #ef4444;
        border-radius: 15px;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn-outline-danger-lux:hover {
        background: rgba(239, 68, 68, 0.05);
        border-color: #ef4444;
        color: #ef4444;
    }

    /* Empty State LUX */
    .empty-state-card-lux { text-align: center; padding: 100px 30px; background: var(--card-dark); border-radius: 40px; border: 2px dashed rgba(255,255,255,0.05); }
    .empty-visual-wrapper { position: relative; width: 120px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
    .glow-pulse { position: absolute; inset: 0; background: var(--indigo-primary); opacity: 0.1; border-radius: 50%; animation: ping 3s infinite; }
    .empty-icon-vibe { font-size: 4rem; color: var(--indigo-primary); position: relative; z-index: 2; }
    .btn-glass-dark { background: rgba(255,255,255,0.1); border-radius: 14px; font-weight: 700; transition: 0.3s; }
    .btn-glass-dark:hover { background: rgba(255,255,255,0.15); color: white; transform: translateY(-3px); }

    .btn-primary-sleek { background: var(--indigo-primary); color: white; border: none; border-radius: 14px; font-weight: 700; transition: 0.3s; }
    .btn-primary-sleek:hover { background: var(--indigo-dark); box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3); color: white; transform: translateY(-3px); }

    .btn-link-danger { color: #f87171; opacity: 0.7; transition: 0.3s; border-radius: 8px; }
    .btn-link-danger:hover { opacity: 1; background: rgba(248, 113, 113, 0.05); }

    /* Empty State LUX */
    .empty-state-card-lux { text-align: center; padding: 100px 30px; background: var(--card-dark); border-radius: 40px; border: 2px dashed rgba(255,255,255,0.05); }
    .empty-visual-wrapper { position: relative; width: 120px; height: 120px; margin: 0 auto; display: flex; align-items: center; justify-content: center; }
    .glow-pulse { position: absolute; inset: 0; background: var(--indigo-primary); opacity: 0.1; border-radius: 50%; animation: ping 3s infinite; }
    .empty-icon-vibe { font-size: 4rem; color: var(--indigo-primary); position: relative; z-index: 2; }

    /* Modals Dark LUX (Solid Theme) */
    .premium-modal-dark { 
        background: #0b0e14 !important; 
        background-color: #0b0e14 !important; 
        border: 1px solid rgba(255,255,255,0.1) !important; 
        border-radius: 40px !important; 
        overflow: hidden; 
        box-shadow: 0 50px 100px -20px rgba(0, 0, 0, 0.7);
        opacity: 1 !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
    .profile-strip-lux { 
        background: linear-gradient(135deg, #1a2332 0%, #111827 100%); 
        padding: 30px; 
        border-radius: 35px; 
        display: flex; 
        align-items: center; 
        justify-content: flex-end; 
        border: 1px solid rgba(255,255,255,0.05); 
        position: relative;
        overflow: hidden;
    }
    .profile-strip-lux::after {
        content: '';
        position: absolute;
        top: 0; right: 0; bottom: 0; width: 150px;
        background: linear-gradient(to left, rgba(99, 102, 241, 0.05), transparent);
        pointer-events: none;
    }
    .strip-avatar-glow { 
        width: 70px; 
        height: 70px; 
        border-radius: 22px; 
        display: flex; 
        align-items: center; 
        justify-content: center; 
        position: relative; 
        z-index: 2;
        background: linear-gradient(135deg, #4f46e5 0%, #3730a3 100%);
    }
    
    .badge-status-dot { width: 12px; height: 12px; border-radius: 50%; display: inline-block; vertical-align: middle; }
    .badge-status-dot.new { background: #3b82f6; box-shadow: 0 0 15px #3b82f6; }
    .badge-status-dot.contacted { background: #a855f7; box-shadow: 0 0 15px #a855f7; }
    .badge-status-dot.accepted { background: #10b981; box-shadow: 0 0 15px #10b981; }
    .badge-status-dot.rejected { background: #ef4444; box-shadow: 0 0 15px #ef4444; }

    .section-indicator-indigo { width: 5px; height: 22px; background: var(--indigo-primary); border-radius: 10px; box-shadow: 0 0 10px rgba(99, 102, 241, 0.4); }
    
    .detail-box-lux { 
        background: #161d29 !important; 
        background-color: #161d29 !important; 
        padding: 24px; 
        border-radius: 30px; 
        border: 1px solid rgba(255,255,255,0.06);
        opacity: 1 !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
        transition: 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        justify-content: center;
        height: 100%;
    }
    .detail-box-lux:hover {
        background: #1c2636;
        border-color: rgba(99, 102, 241, 0.3);
        transform: translateY(-5px);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.5);
    }
    .detail-label-sleek { 
        font-size: 0.8rem; 
        font-weight: 800; 
        color: #818cf8; 
        margin-bottom: 14px; 
        display: flex; 
        align-items: center;
        gap: 8px;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        opacity: 0.9;
    }
    .detail-content-sleek { 
        font-size: 1.05rem; 
        color: #f1f5f9; 
        font-weight: 700;
        line-height: 1.5; 
    }
    .font-outfit { font-family: 'Outfit', sans-serif !important; }

    .btn-glass-indigo { background: rgba(99, 102, 241, 0.1); color: var(--indigo-primary); border: 1px solid rgba(99, 102, 241, 0.2); transition: 0.3s; }
    .btn-glass-indigo:hover { background: var(--indigo-primary); color: white; }

    /* Add Logic Styling */
    .header-icon-box-lux { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; color: white; font-size: 1.3rem; }
    .label-lux { color: #94a3b8; font-weight: 700; font-size: 0.85rem; margin-bottom: 10px; display: block; }
    .field-lux { width: 100%; background: #0b0e14; border: 2px solid #1e293b; border-radius: 16px; padding: 14px 20px; color: white; font-weight: 600; transition: 0.3s; }
    .field-lux:focus { border-color: var(--indigo-primary); outline: none; background: #000; box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1); }
    
    .upload-zone-lux { border: 2px dashed #1e293b; border-radius: 25px; height: 180px; position: relative; overflow: hidden; transition: 0.3s; cursor: pointer; }
    .upload-zone-lux:hover { border-color: var(--indigo-primary); background: rgba(99, 102, 241, 0.05); }
    .file-hidden { position: absolute; inset: 0; opacity: 0; cursor: pointer; z-index: 10; }
    .preview-area-lux { height: 100%; display: flex; align-items: center; justify-content: center; text-align: center; }

    .stats-row-lux { display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; }
    .btn-indigo-solid { background: var(--indigo-primary); color: white; border: none; transition: 0.3s; }
    .btn-indigo-solid:hover { background: var(--indigo-dark); transform: translateY(-3px); box-shadow: 0 15px 30px rgba(99, 102, 241, 0.3); }

    /* Animations */
    @keyframes ping { 75%, 100% { transform: scale(1.5); opacity: 0; } }
    .animate-reveal-right { animation: revealRight 1s both; }
    .animate-reveal-left { animation: revealLeft 1s both; }
    .animate-up { animation: fadeInUp 0.8s both; }

    @keyframes revealRight { from { opacity: 0; transform: translateX(50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes revealLeft { from { opacity: 0; transform: translateX(-50px); } to { opacity: 1; transform: translateX(0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    .scroll-thin::-webkit-scrollbar { width: 5px; }
    .scroll-thin::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

    @media (max-width: 991px) {
        .premium-hero-sleek { border-radius: 0 0 30px 30px; padding: 60px 0 80px; }
        .display-4 { font-size: 2.2rem; }
        .stats-row-lux { grid-template-columns: 1fr; }
    }
</style>
@endsection

