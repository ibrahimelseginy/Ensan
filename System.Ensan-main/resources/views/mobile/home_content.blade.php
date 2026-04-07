@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-green: #22c55e;
        --primary-hover: #16a34a;
        --bg-light: #f9fafb;
        --text-main: #111111;
        --text-muted: #64748b;
        --border-color: #e5e7eb;
        --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    }

    /* Base Layout */
    body {
        background-color: var(--bg-light) !important;
        color: var(--text-main);
        font-family: 'Tajawal', sans-serif;
    }

    .mobile-content-mgmt {
        padding: 2rem 0;
    }

    /* Premium Header */
    .premium-page-header {
        background: white;
        padding: 2.5rem 2rem;
        border-radius: 24px;
        box-shadow: var(--card-shadow);
        margin-bottom: 2.5rem;
        border: 1px solid var(--border-color);
        position: relative;
        overflow: hidden;
    }

    .premium-page-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 6px;
        height: 100%;
        background: var(--primary-green);
    }

    .header-icon {
        width: 60px;
        height: 60px;
        background: rgba(34, 197, 94, 0.1);
        color: var(--primary-green);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1rem;
    }

    /* Section Cards */
    .section-card-p {
        background: white;
        border-radius: 24px;
        border: 1px solid var(--border-color);
        box-shadow: var(--card-shadow);
        margin-bottom: 2.5rem;
        overflow: hidden;
        transition: transform 0.3s ease;
    }

    .section-card-p:hover {
        transform: translateY(-4px);
    }

    .card-header-premium {
        background: #fff;
        padding: 1.5rem 2rem;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .card-header-premium h5 {
        margin: 0;
        font-weight: 800;
        color: var(--text-main);
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .card-header-premium h5 i {
        color: var(--primary-green);
    }

    .card-body-p {
        padding: 2rem;
    }

    /* Form Controls */
    .form-label-p {
        font-weight: 700;
        color: var(--text-main);
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control-p {
        border-radius: 12px;
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        background: #f8fafc;
        transition: all 0.2s ease;
    }

    .form-control-p:focus {
        border-color: var(--primary-green);
        background: white;
        box-shadow: 0 0 0 4px rgba(34, 197, 94, 0.1);
    }

    /* Multi-Card Grid for Pillars */
    .pillar-item-card {
        background: #f9fafb;
        border-radius: 18px;
        padding: 1.5rem;
        border: 1px solid var(--border-color);
        height: 100%;
    }

    .submit-btn-premium {
        background: var(--primary-green);
        color: white;
        border: none;
        padding: 0.8rem 2rem;
        border-radius: 12px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        text-decoration: none;
    }

    .submit-btn-premium:hover {
        background: var(--primary-hover);
        transform: translateY(-2px);
        box-shadow: 0 10px 15px -3px rgba(34, 197, 94, 0.3);
        color: white;
    }

    /* Image Upload Placeholder */
    .image-preview-wrapper {
        border: 2px dashed var(--border-color);
        border-radius: 16px;
        padding: 1rem;
        text-align: center;
        background: #f8fafc;
        margin-top: 0.5rem;
    }

    .image-preview-wrapper img {
        max-width: 100%;
        max-height: 120px;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    /* Breadcrumbs */
    .custom-breadcrumb {
        background: transparent;
        padding: 0;
        margin-bottom: 0.5rem;
    }

    .custom-breadcrumb .breadcrumb-item a {
        color: var(--text-muted);
        text-decoration: none;
        font-size: 0.85rem;
    }

    .custom-breadcrumb .breadcrumb-item.active {
        color: var(--primary-green);
        font-weight: 700;
        font-size: 0.85rem;
    }

    /* Animations */
    @keyframes slideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .animate-up {
        animation: slideUp 0.5s ease forwards;
    }

    /* Custom Checkbox/Switch */
    .form-check-input:checked {
        background-color: var(--primary-green);
        border-color: var(--primary-green);
    }
</style>

<div class="container-fluid mobile-content-mgmt">
    {{-- Header Section --}}
    <div class="row px-lg-4">
        <div class="col-12">
            <div class="premium-page-header animate-up">
                <nav aria-label="breadcrumb" class="custom-breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('mobile.dashboard') }}">لوحة التحكم</a></li>
                        <li class="breadcrumb-item active" aria-current="page">محتوى الصفحة الرئيسية</li>
                    </ol>
                </nav>
                <div class="header-icon">
                    <i class="bi bi-phone-vibrate"></i>
                </div>
                <h1 class="h2 fw-800 text-main mb-1">محتوى الصفحة الرئيسية <span style="color: var(--primary-green)">(الموبايل)</span></h1>
                <p class="text-muted mb-0">تخصيص وإدارة الأقسام التي تظهر في شاشة التطبيق الرئيسية</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4 mx-lg-4 rounded-4 shadow-sm border-0" role="alert" style="background: #ecfdf5; color: #065f46; border-right: 6px solid var(--primary-green) !important;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row px-lg-4 mt-2">
        <div class="col-12">
            
            {{-- Ensan Pillars Section --}}
            <div class="section-card-p animate-up" style="animation-delay: 0.1s">
                <div class="card-header-premium">
                    <h5><i class="bi bi-columns-gap"></i> أركان إنسان (Ensan Pillars)</h5>
                </div>
                <div class="card-body-p">
                    <div class="row g-4">
                        @foreach($pillars as $pillar)
                        <div class="col-lg-4">
                            <form action="{{ route('mobile.pillars.update', $pillar->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf @method('PUT')
                                <div class="pillar-item-card">
                                    <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-2">
                                        <span class="badge rounded-pill px-3 py-2 fw-bold" style="background: rgba(34, 197, 94, 0.1); color: var(--primary-green);">{{ $pillar->title }}</span>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-p">عنوان الركن</label>
                                        <input type="text" name="title" class="form-control form-control-p" 
                                               value="{{ $pillar->title }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-p">الرابط الفريد (Slug)</label>
                                        <input type="text" name="slug" class="form-control form-control-p" 
                                               value="{{ $pillar->slug }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-p">وصف مختصر</label>
                                        <textarea name="description" class="form-control form-control-p" rows="2" 
                                                  placeholder="وصف للخدمة...">{{ $pillar->description }}</textarea>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-p">أيقونة (Image)</label>
                                        <input type="file" name="icon" class="form-control form-control-p mb-2">
                                        @if($pillar->icon_path)
                                        <div class="image-preview-wrapper mt-2">
                                            <img src="{{ Storage::disk('public')->url($pillar->icon_path) }}" alt="preview">
                                        </div>
                                        @endif
                                    </div>
                                    <div class="mt-3 text-center">
                                        <button type="submit" class="submit-btn-premium btn-sm py-2">
                                            <i class="bi bi-save"></i> حفظ الركن
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Campaigns Section --}}
            <div class="section-card-p animate-up" style="animation-delay: 0.2s">
                <div class="card-header-premium">
                    <h5><i class="bi bi-megaphone"></i> حملات الصفحة الرئيسية (Campaigns Grid)</h5>
                </div>
                <div class="card-body-p">
                    <div class="row g-4">
                        @foreach($campaigns as $campaign)
                        <div class="col-md-6 col-lg-3">
                            <form action="{{ route('mobile.home_content.update', $campaign->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="pillar-item-card" style="background: #fff; border-style: dashed;">
                                    <div class="mb-3">
                                        <label class="form-label-p">عنوان الحملة</label>
                                        <input type="text" name="title" class="form-control form-control-p" 
                                               value="{{ $campaign->title }}">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label-p">الرابط / الفلتر</label>
                                        <input type="text" name="details" class="form-control form-control-p" 
                                               value="{{ $campaign->details }}" placeholder="campaign_{{ $loop->index + 1 }}">
                                    </div>
                                    <div class="text-center">
                                        <button type="submit" class="submit-btn-premium btn-sm py-2">
                                            <i class="bi bi-arrow-repeat"></i> تحديث
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Services Grid Section --}}
            <div class="section-card-p animate-up" style="animation-delay: 0.3s">
                <div class="card-header-premium">
                    <h5><i class="bi bi-grid-3x3-gap"></i> الخدمات الرئيسية (Services Grid)</h5>
                </div>
                <div class="card-body-p">
                    <div class="row g-3">
                        @foreach($serviceItems as $service)
                        <div class="col-md-4 col-lg-2">
                            <form action="{{ route('mobile.home_content.update', $service->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="pillar-item-card p-3 text-center">
                                    <label class="form-label-p d-block mb-2">اسم الخدمة</label>
                                    <input type="text" name="title" class="form-control form-control-p text-center mb-2" 
                                           value="{{ $service->title }}" placeholder="الاسم">
                                    <button type="submit" class="btn btn-sm btn-outline-success border-0 p-0" title="حفظ"><i class="bi bi-check-circle"></i></button>
                                </div>
                            </form>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Gallery & Share Section --}}
            <div class="row g-4 mb-4">
                {{-- Gallery Section --}}
                <div class="col-lg-6">
                    <div class="section-card-p h-100 mb-0 animate-up">
                        <div class="card-header-premium">
                            <h5><i class="bi bi-images"></i> معرض الصور الرئيسي</h5>
                        </div>
                        <div class="card-body-p">
                            <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="type" value="gallery">
                                <div class="mb-4">
                                    <label class="form-label-p">أضف صور جديدة للمعرض</label>
                                    <input type="file" name="image" class="form-control form-control-p mb-1">
                                    <small class="text-muted">ارفع صورة واحدة في كل مرة للإضافة</small>
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="submit-btn-premium w-100 justify-content-center">
                                        <i class="bi bi-cloud-upload"></i> إضافة صورة للمعرض
                                    </button>
                                </div>
                            </form>
                                
                            @if($gallery->count() > 0)
                            <div class="row g-2 mt-4 p-3 bg-light rounded-4">
                                <div class="col-12 mb-2"><label class="form-label-p">الصور الحالية</label></div>
                                @foreach($gallery as $img)
                                <div class="col-4">
                                    <div class="position-relative group">
                                        @if($img->image_path)
                                        <img src="{{ Storage::disk('public')->url($img->image_path) }}" class="img-fluid rounded-3 border" style="height: 80px; width: 100%; object-fit: cover;">
                                        <form action="{{ route('mobile.home_content.destroy', $img->id) }}" method="POST" class="position-absolute top-0 start-0 m-1">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm p-0 px-1 rounded-circle border-0" onclick="return confirm('حذف الصورة؟')">
                                                <i class="bi bi-x"></i>
                                            </button>
                                        </form>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Share Section --}}
                <div class="col-lg-6">
                    @php $share = $shareItems->first(); @endphp
                    @if($share)
                    <div class="section-card-p h-100 mb-0 animate-up">
                        <div class="card-header-premium">
                            <h5><i class="bi bi-share"></i> قسم "{{ $share->title }}"</h5>
                        </div>
                        <div class="card-body-p">
                            <form action="{{ route('mobile.home_content.update', $share->id) }}" method="POST">
                                @csrf @method('PUT')
                                <div class="mb-4">
                                    <label class="form-label-p">العنوان الرئيسي</label>
                                    <input type="text" name="title" class="form-control form-control-p" 
                                           value="{{ $share->title }}">
                                </div>
                                <div class="mb-4">
                                    <label class="form-label-p">النص الوصفي</label>
                                    <textarea name="description" class="form-control form-control-p" rows="4">{{ $share->description }}</textarea>
                                </div>
                                <div class="mb-0">
                                    <label class="form-label-p">نص زر المشاركة / الاتصال (تفاصيل)</label>
                                    <input type="text" name="details" class="form-control form-control-p" 
                                           value="{{ $share->details }}" placeholder="اتصل بنا">
                                </div>
                                <div class="mt-4">
                                    <button type="submit" class="submit-btn-premium w-100 justify-content-center">
                                        <i class="bi bi-check2-circle"></i> حفظ قسم المشاركة
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Final Footer Section --}}
            <div class="section-card-p animate-up" style="animation-delay: 0.4s">
                @if($finalSection)
                <div class="card-header-premium">
                    <h5><i class="bi bi-layout-text-window-reverse"></i> القسم الختامي (Final Section)</h5>
                </div>
                <div class="card-body-p">
                    <form action="{{ route('mobile.home_content.update', $finalSection->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row g-4">
                            <div class="col-md-6">
                                <label class="form-label-p">العنوان الختامي</label>
                                <input type="text" name="title" class="form-control form-control-p" 
                                       value="{{ $finalSection->title }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label-p">نص التذييل / الحقوق</label>
                                <input type="text" name="description" class="form-control form-control-p" 
                                       value="{{ $finalSection->description }}">
                            </div>
                        </div>
                        <div class="mt-4 text-end">
                            <button type="submit" class="submit-btn-premium">
                                <i class="bi bi-save2"></i> حفظ القسم الأخير
                            </button>
                        </div>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700;800&display=swap" rel="stylesheet">
@endsection
