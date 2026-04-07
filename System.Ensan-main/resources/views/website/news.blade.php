@extends('layouts.app')

@section('content')
<div class="news-mgmt-page">
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
                    <li class="breadcrumb-item active" aria-current="page">الأخبار والفعاليات</li>
                </ol>
            </nav>
            <div class="badge-glass-premium mb-3">
                <i class="bi bi-newspaper me-2"></i> إدارة المركز الإعلامي
            </div>
            <h1 class="display-5 fw-800 text-dark mb-3">الأخبار والفعاليات</h1>
            <p class="lead text-muted mb-0 max-w-600 mx-auto">
                إدارة الأخبار والقصص والفعاليات التي يتم نشرها على واجهة الموقع الإلكتروني الرسمي
            </p>
            <div class="mt-4 d-flex justify-content-center gap-3 flex-wrap">
                <button class="btn btn-outline-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#manageSlider">
                    <i class="bi bi-images me-2"></i> إدارة صور السلايدر
                </button>
                <button class="btn btn-primary rounded-pill px-4 py-2 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#addNews">
                    <i class="bi bi-plus-lg me-2"></i> إضافة خبر جديد
                </button>
            </div>
        </div>
    </div>

    <div class="container-fluid py-4 px-lg-5">
        <div class="row g-4">
            @foreach($news as $item)
            <div class="col-md-6 col-lg-4 col-xxl-3">
                <div class="card h-100 border-0 shadow-sm news-card-sleek rounded-4 animate-slide-up overflow-hidden" style="animation-delay: {{ $loop->index * 0.05 }}s">
                    <div class="ratio ratio-16x9 position-relative overflow-hidden group-hover-zoom">
                        @if($item->image_path)
                            <img src="{{ $item->image_url }}" class="w-100 h-100 object-fit-cover transition-all">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-light border-bottom">
                                <i class="bi bi-camera fs-1 text-muted opacity-25"></i>
                            </div>
                        @endif
                        
                        <div class="news-card-actions-overlay position-absolute top-0 end-0 p-3 d-flex flex-column gap-2 transition-all opacity-0">
                            <button class="btn btn-white btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" data-bs-toggle="modal" data-bs-target="#editNews{{ $item->id }}" title="تعديل">
                                <i class="bi bi-pencil-square text-primary"></i>
                            </button>
                            <form action="{{ route('website.news.destroy', $item) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الخبر؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-white btn-sm rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;" title="حذف">
                                    <i class="bi bi-trash text-danger"></i>
                                </button>
                            </form>
                        </div>
                        
                        @if($item->category)
                        <div class="position-absolute top-0 start-0 p-3">
                            <span class="badge bg-primary rounded-pill px-3 py-1 x-small fw-bold shadow-sm">{{ $item->category }}</span>
                        </div>
                        @endif
                    </div>
                    
                    <div class="card-body p-4 d-flex flex-column h-100">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="text-muted x-small"><i class="bi bi-calendar3 me-1"></i> {{ $item->published_at ? $item->published_at->format('d M Y') : 'مسودة' }}</span>
                            <span class="text-muted x-small fw-bold px-2 py-0 bg-light rounded-pill border">#{{ $item->id }}</span>
                        </div>
                        
                        <h6 class="fw-bold text-dark mb-2 lh-base text-truncate-2 h-news-title" title="{{ $item->title }}">{{ $item->title }}</h6>
                        <p class="text-muted x-small mb-4 text-truncate-3 opacity-75">{{ Str::limit($item->content, 120) }}</p>
                        
                        @if($item->statistic_number)
                        <div class="stat-highlight-pill d-flex align-items-center gap-2 bg-light border rounded-pill px-3 py-2 mb-4">
                            <i class="bi bi-graph-up-arrow text-primary"></i>
                            <div class="fw-bold text-primary x-small lh-1">{{ $item->statistic_number }}</div>
                            <div class="text-muted x-small border-start ps-2 lh-1 opacity-75">{{ $item->statistic_description }}</div>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-auto pt-3 border-top">
                            <div class="d-flex align-items-center gap-3 text-muted x-small">
                                <span><i class="bi bi-eye me-1"></i> {{ $item->views_count ?? '0' }}</span>
                                <span><i class="bi bi-share me-1"></i> {{ $item->shares_count ?? '0' }}</span>
                            </div>
                            <button class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 x-small fw-bold" data-bs-toggle="modal" data-bs-target="#viewNews{{ $item->id }}">
                                تفاصيل أكثر <i class="bi bi-arrow-left ms-1"></i>
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Edit News Modal --}}
                <div class="modal fade" id="editNews{{ $item->id }}" tabindex="-1">
                    <div class="modal-dialog modal-lg modal-dialog-centered">
                        <form action="{{ route('website.news.update', $item) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                            @csrf @method('PUT')
                            <div class="modal-header border-bottom bg-light px-4 py-3">
                                <h5 class="modal-title fw-bold text-dark"><i class="bi bi-pencil-square me-2 text-primary"></i> تعديل الخبر: {{ Str::limit($item->title, 40) }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-4 bg-white">
                                <div class="row g-3">
                                    <div class="col-md-8 mb-2">
                                        <label class="form-label small fw-bold text-muted">عنوان الخبر</label>
                                        <input type="text" name="title" class="form-control" required value="{{ $item->title }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold text-muted">القسم / التصنيف</label>
                                        <select name="category" class="form-select">
                                            <option value="طبي" {{ $item->category == 'طبي' ? 'selected' : '' }}>طبي</option>
                                            <option value="تعليمي" {{ $item->category == 'تعليمي' ? 'selected' : '' }}>تعليمي</option>
                                            <option value="مساعدات" {{ $item->category == 'مساعدات' ? 'selected' : '' }}>مساعدات</option>
                                            <option value="فعاليات" {{ $item->category == 'فعاليات' ? 'selected' : '' }}>فعاليات</option>
                                            <option value="عام" {{ ($item->category == 'عام' || !$item->category) ? 'selected' : '' }}>عام</option>
                                        </select>
                                    </div>
                                    <div class="col-12 mb-3">
                                        <label class="form-label small fw-bold text-muted">شريط صورة الخبر</label>
                                        <div class="p-3 bg-light rounded-4 border">
                                            <div class="row align-items-center g-3">
                                                <div class="col-auto">
                                                    @if($item->image_path)
                                                        <img src="{{ $item->image_url }}" class="rounded shadow-sm border border-white" style="width: 100px; height: 60px; object-fit: cover;">
                                                    @else
                                                        <div class="rounded border p-3 text-muted text-center" style="width: 100px;"><i class="bi bi-image"></i></div>
                                                    @endif
                                                </div>
                                                <div class="col">
                                                    <input type="file" name="image" class="form-control form-control-sm">
                                                    <p class="x-small text-muted mt-1 mb-0">اتركه فارغاً للاحتفظ بالصورة الحالية. الحجم المثالي (800x450)</p>
                                                </div>
                                                @if($item->image_path)
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-3" onclick="document.getElementById('delete_image_{{ $item->id }}').value='1'; this.closest('.p-3').querySelector('img').classList.add('opacity-25'); this.remove();">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                    <input type="hidden" name="delete_image" id="delete_image_{{ $item->id }}" value="0">
                                                </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row g-3">
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold text-muted">تاريخ النشر</label>
                                        <input type="date" name="published_at" class="form-control" value="{{ $item->published_at ? $item->published_at->format('Y-m-d') : date('Y-m-d') }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold text-muted">عدد المشاهدات</label>
                                        <input type="text" name="views_count" class="form-control" value="{{ $item->views_count }}">
                                    </div>
                                    <div class="col-md-4 mb-2">
                                        <label class="form-label small fw-bold text-muted">عدد المشاركات</label>
                                        <input type="text" name="shares_count" class="form-control" value="{{ $item->shares_count }}">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small fw-bold text-muted">رقم إحصائي بارز (اختياري)</label>
                                        <input type="text" name="statistic_number" class="form-control" value="{{ $item->statistic_number }}" placeholder="مثلاً: 95% أو +150">
                                    </div>
                                    <div class="col-md-6 mb-2">
                                        <label class="form-label small fw-bold text-muted">وصف الإحصائية</label>
                                        <input type="text" name="statistic_description" class="form-control" value="{{ $item->statistic_description }}" placeholder="مثلاً: نسبة الإنجاز">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label small fw-bold text-muted">نص الخبر الكامل</label>
                                    <textarea name="content" class="form-control" rows="8" required>{{ $item->content }}</textarea>
                                </div>
                            </div>
                            <div class="modal-footer border-top bg-light p-3">
                                <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">تجاهل</button>
                                <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">حفظ التغييرات</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- View News Modal --}}
                <div class="modal fade" id="viewNews{{ $item->id }}" tabindex="-1" style="z-index: 2000;">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden bg-white">
                            <div class="modal-header border-bottom bg-light px-4 py-3">
                                <h5 class="modal-title fw-bold text-dark">{{ $item->title }}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body p-0">
                                @if($item->image_path)
                                    <img src="{{ $item->image_url }}" class="w-100 object-fit-cover shadow-inner" style="max-height: 400px;">
                                @endif
                                <div class="p-4 p-md-5">
                                    <div class="d-flex flex-wrap gap-4 text-muted small mb-4 pb-3 border-bottom">
                                        <span class="d-flex align-items-center gap-1"><i class="bi bi-calendar3 text-primary"></i> {{ $item->published_at ? $item->published_at->format('d M Y') : '' }}</span>
                                        <span class="d-flex align-items-center gap-1"><i class="bi bi-tag text-primary"></i> {{ $item->category ?? 'عام' }}</span>
                                        <span class="d-flex align-items-center gap-1"><i class="bi bi-eye text-primary"></i> {{ $item->views_count ?? 0 }} مشاهدة</span>
                                    </div>
                                    <div class="text-dark lh-lg" style="white-space: pre-wrap; font-size: 1.05rem; opacity: 0.9;">{{ $item->content }}</div>
                                    
                                    @if($item->contact_name)
                                    <div class="mt-5 p-4 bg-light rounded-4 border-start border-primary border-4 shadow-sm">
                                        <h6 class="fw-bold mb-2">للاستفسار / التواصل:</h6>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="fw-bold">{{ $item->contact_name }}</div>
                                            @if($item->contact_number)
                                            <div class="text-primary fw-bold"><i class="bi bi-phone me-1"></i> {{ $item->contact_number }}</div>
                                            @endif
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="modal-footer border-top bg-light p-3">
                                <button type="button" class="btn btn-primary rounded-pill px-5 fw-bold" data-bs-dismiss="modal">فهمت</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Add News Modal --}}
    <div class="modal fade" id="addNews" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('website.news.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                <div class="modal-header border-bottom bg-primary text-white px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> إضافة خبر جديد للموقع</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="row g-3">
                        <div class="col-md-8 mb-2">
                            <label class="form-label small fw-bold text-muted">عنوان الخبر الرئيسي</label>
                            <input type="text" name="title" class="form-control" required placeholder="أدخل عنواناً جذاباً...">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small fw-bold text-muted">تصنيف الخبر</label>
                            <select name="category" class="form-select">
                                <option value="طبي">طبي</option>
                                <option value="تعليمي">تعليمي</option>
                                <option value="مساعدات">مساعدات</option>
                                <option value="فعاليات">فعاليات</option>
                                <option value="عام" selected>عام</option>
                            </select>
                        </div>
                        <div class="col-12 mb-2">
                            <label class="form-label small fw-bold text-muted">صورة الخبر</label>
                            <input type="file" name="image" class="form-control">
                            <p class="x-small text-muted mt-1">يفضل أن تكون الصورة بعرض كبير (16:9)</p>
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4 mb-2">
                            <label class="form-label small fw-bold text-muted">تاريخ النشر</label>
                            <input type="date" name="published_at" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small fw-bold text-muted">مشاهدات وهمية (اختياري)</label>
                            <input type="text" name="views_count" class="form-control" placeholder="مثلاً: 1.5K">
                        </div>
                        <div class="col-md-4 mb-2">
                            <label class="form-label small fw-bold text-muted">مشاركات وهمية</label>
                            <input type="text" name="shares_count" class="form-control" placeholder="مثلاً: 250">
                        </div>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted">رقم إحصائي (مثل 90%)</label>
                            <input type="text" name="statistic_number" class="form-control" placeholder="يظهر بشكل بارز في الكارت">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label small fw-bold text-muted">وصف الإحصائي</label>
                            <input type="text" name="statistic_description" class="form-control" placeholder="مثلاً: نسبة المساعدات">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-bold text-muted">محتوى الخبر بالكامل</label>
                        <textarea name="content" class="form-control" rows="10" required placeholder="اكتب تفاصيل الخبر هنا..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm">نشر الخبر الآن</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Manage Slider Modal --}}
    <div class="modal fade" id="manageSlider" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <form action="{{ route('website.settings.update') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
                @csrf
                <div class="modal-header border-bottom bg-warning text-dark px-4 py-3">
                    <h5 class="modal-title fw-bold"><i class="bi bi-images me-2"></i> إدارة صور سلايدر الأخبار</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4 bg-white">
                    <div class="p-3 bg-light rounded-4 border mb-4">
                        <p class="x-small text-muted mb-0"><i class="bi bi-info-circle me-1"></i> الصور المضافة هنا تظهر في السلايدر العلوي لصفحة المركز الإعلامي. يمكنك إضافة حتى 10 صور.</p>
                    </div>
                    <div class="row g-3">
                        @for($i = 1; $i <= 10; $i++)
                        <div class="col-md-4 col-lg-3 col-6">
                            <div class="position-relative rounded-4 border overflow-hidden bg-light slider-upload-mini ratio ratio-4x3 group-hover-overlay" style="cursor: pointer;">
                                @php $sliderPath = $settings["news_slider_$i"] ?? null; @endphp
                                @if($sliderPath)
                                    <img src="{{ asset('storage/' . $sliderPath) }}" class="w-100 h-100 object-fit-cover shadow-sm transition-all" id="newsSliderPrev{{ $i }}">
                                @else
                                    <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted opacity-50" id="newsSliderPlace{{ $i }}">
                                        <i class="bi bi-cloud-arrow-up fs-2 mb-1"></i>
                                        <span class="x-small fw-bold">{{ $i }}</span>
                                    </div>
                                    <img src="" class="w-100 h-100 object-fit-cover d-none shadow-sm transition-all" id="newsSliderPrev{{ $i }}">
                                @endif
                                
                                <input type="file" name="news_slider_{{ $i }}" class="position-absolute inset-0 w-100 h-100 opacity-0 cursor-pointer z-5" onchange="previewNewsSlider(this, {{ $i }})">
                                
                                @if($sliderPath)
                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2 shadow-sm d-flex align-items-center justify-content-center z-10" 
                                        style="width: 26px; height: 26px;"
                                        onclick="event.stopPropagation(); document.getElementById('delete_news_slider_{{ $i }}').value='1'; this.closest('.slider-upload-mini').querySelector('.d-none').classList.remove('d-none'); document.getElementById('newsSliderPrev{{ $i }}').classList.add('d-none'); this.remove();">
                                    <i class="bi bi-trash fs-xs"></i>
                                </button>
                                @endif
                                <input type="hidden" name="delete_news_slider_{{ $i }}" id="delete_news_slider_{{ $i }}" value="0">
                            </div>
                        </div>
                        @endfor
                    </div>
                </div>
                <div class="modal-footer border-top bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">إغلاق</button>
                    <button type="submit" class="btn btn-warning rounded-pill px-5 fw-bold shadow-sm">حفظ السلايدر</button>
                </div>
            </form>
        </div>
    </div>

</div>

<script>
    function previewNewsSlider(input, index) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                const img = document.getElementById('newsSliderPrev' + index);
                const place = document.getElementById('newsSliderPlace' + index);
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
    .news-mgmt-page { min-height: 100vh; }
    .fw-800 { font-weight: 800; }
    .max-w-600 { max-width: 600px; }
    .x-small { font-size: 0.75rem; }
    .fs-xs { font-size: 0.75rem; }
    .text-truncate-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    .text-truncate-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
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

    /* News Card Sleek */
    .news-card-sleek {
        transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
        border: 1px solid var(--border) !important;
    }
    .news-card-sleek:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 35px rgba(0,0,0,0.08) !important;
        border-color: var(--primary-light) !important;
    }
    .news-card-sleek:hover .news-card-actions-overlay {
        opacity: 1;
    }
    .group-hover-zoom:hover img {
        transform: scale(1.1);
    }
    
    .h-news-title {
        min-height: 3rem;
    }

    .slider-upload-mini {
        transition: all 0.3s ease;
        border: 2px dashed var(--border) !important;
    }
    .slider-upload-mini:hover {
        border-color: var(--primary) !important;
        background: var(--bg-soft) !important;
    }

    .animate-slide-up { animation: slideUp 0.6s ease-out forwards; opacity: 0; }
    @keyframes slideUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }

    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.05) !important; }
    
    .modal-content .form-control:focus, .modal-content .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 0.25rem rgba(34, 197, 94, 0.1);
    }
    
    .btn-white {
        background: white;
        border: none;
    }
    .btn-white:hover {
        background: #f8fafc;
    }
</style>
@endsection
