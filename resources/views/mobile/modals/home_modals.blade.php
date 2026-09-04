{{-- Pillars Modals --}}
<div class="modal fade premium-modal-dark" id="addPillarModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('mobile.pillars.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title font-lux text-white">إضافة مبادرة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white-50">اسم المبادرة</label>
                            <input type="text" name="title" class="form-control field-lux" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white-50">الاسم الفريد (Slug)</label>
                            <input type="text" name="slug" class="form-control field-lux" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-white-50">الوصف</label>
                            <textarea name="description" class="form-control field-lux" rows="3"></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-white-50">الأيقونة</label>
                            <input type="file" name="icon" class="form-control field-lux" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-white border-opacity-10">
                    <button type="button" class="btn btn-glass-danger rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">حفظ المبادرة</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($pillars as $p)
<div class="modal fade premium-modal-dark" id="editPillarModal{{ $p->id }}" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('mobile.pillars.update', $p) }}" method="POST" enctype="multipart/form-data">
            @csrf @method('PUT')
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title font-lux text-white">تعديل المبادرة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white-50">اسم المبادرة</label>
                            <input type="text" name="title" value="{{ $p->title }}" class="form-control field-lux" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label text-white-50">الاسم الفريد (Slug)</label>
                            <input type="text" name="slug" value="{{ $p->slug }}" class="form-control field-lux" required>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label text-white-50">الوصف</label>
                            <textarea name="description" class="form-control field-lux" rows="3">{{ $p->description }}</textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label text-white-50">تغيير الأيقونة</label>
                            <input type="file" name="icon" class="form-control field-lux" accept="image/*">
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-white border-opacity-10">
                    <button type="button" class="btn btn-glass-danger rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

{{-- Gallery Modal --}}
<div class="modal fade premium-modal-dark" id="addGalleryModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="2">
            <div class="modal-content glass-card">
                <div class="modal-header">
                    <h5 class="modal-title text-white">إضافة صورة للمعرض</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="file" name="image" class="form-control field-lux" accept="image/*" required>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100">تحميل الصورة</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Share Modal --}}
<div class="modal fade premium-modal-dark" id="addShareModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="4">
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">إضافة عنصر جديد (شارك بما لا تحتاجه)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">العنوان</label>
                        <input type="text" name="title" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">سعر السهم (اختياري)</label>
                        <input type="number" name="share_price" class="form-control field-lux">
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الصورة</label>
                        <input type="file" name="image" class="form-control field-lux" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">إضافة</button>
                </div>
            </div>
        </form>
    </div>
</div>
@foreach($shareItems as $s)
<div class="modal fade premium-modal-dark" id="editShareModal{{ $s->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.update', $s) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">تعديل عنصر</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">العنوان</label>
                        <input type="text" name="title" value="{{ $s->title }}" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">سعر السهم</label>
                        <input type="number" name="share_price" value="{{ $s->share_price }}" class="form-control field-lux">
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control field-lux" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade premium-modal-dark" id="addCampaignModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="section" value="5">
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">إضافة حملة موسمية</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">اسم الحملة</label>
                        <input type="text" name="title" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">التفاصيل</label>
                        <input type="text" name="details" class="form-control field-lux">
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الصورة</label>
                        <input type="file" name="image" class="form-control field-lux" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">إضافة</button>
                </div>
            </div>
        </form>
    </div>
</div>

@foreach($campaigns as $c)
<div class="modal fade premium-modal-dark" id="editCampModal{{ $c->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.update', $c) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">تعديل الحملة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">العنوان</label>
                        <input type="text" name="title" value="{{ $c->title }}" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">التفاصيل</label>
                        <input type="text" name="details" value="{{ $c->details }}" class="form-control field-lux">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach

<div class="modal fade premium-modal-dark" id="editFinalModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ $finalSection ? route('mobile.home_content.update', $finalSection) : route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data">
            @csrf 
            @if(!$finalSection) <input type="hidden" name="section" value="6"> @endif
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">تعديل القسم الأخير</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">العنوان</label>
                        <input type="text" name="title" value="{{ $finalSection->title ?? '' }}" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الوصف</label>
                        <textarea name="description" class="form-control field-lux" rows="2">{{ $finalSection->description ?? '' }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الصورة</label>
                        <input type="file" name="image" class="form-control field-lux" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">حفظ</button>
                </div>
            </div>
        </form>
    </div>
</div>
{{-- About Us Modals --}}
<div class="modal fade premium-modal-dark" id="addAboutModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST">
            @csrf
            <input type="hidden" name="type" value="about_us">
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">إضافة معلومات عنا</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">العنوان</label>
                        <input type="text" name="title" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الوصف</label>
                        <textarea name="description" class="form-control field-lux" rows="4" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">إضافة</button>
                </div>
            </div>
        </form>
    </div>
</div>

@if(isset($aboutUs) && $aboutUs)
<div class="modal fade premium-modal-dark" id="editAboutModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.update', $aboutUs) }}" method="POST">
            @csrf
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">تعديل معلومات عنا</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">العنوان</label>
                        <input type="text" name="title" value="{{ $aboutUs->title }}" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الوصف</label>
                        <textarea name="description" class="form-control field-lux" rows="4" required>{{ $aboutUs->description }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">تحديث</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif

{{-- Services Modals --}}
<div class="modal fade premium-modal-dark" id="addServiceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" value="service">
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">إضافة خدمة جديدة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">اسم الخدمة</label>
                        <input type="text" name="title" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الوصف</label>
                        <input type="text" name="description" class="form-control field-lux">
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">سعر السهم</label>
                        <input type="number" name="share_price" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الصورة</label>
                        <input type="file" name="image" class="form-control field-lux" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">إضافة</button>
                </div>
            </div>
        </form>
    </div>
</div>
@foreach($services as $s)
<div class="modal fade premium-modal-dark" id="editItemModal{{ $s->id }}" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('mobile.home_content.update', $s) }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-content glass-card">
                <div class="modal-header border-white border-opacity-10">
                    <h5 class="modal-title text-white">تعديل الخدمة</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">اسم الخدمة</label>
                        <input type="text" name="title" value="{{ $s->title }}" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">الوصف</label>
                        <input type="text" name="description" value="{{ $s->description }}" class="form-control field-lux">
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">سعر السهم</label>
                        <input type="number" name="share_price" value="{{ $s->share_price }}" class="form-control field-lux" required>
                    </div>
                    <div class="mb-3">
                        <label class="text-white-50 mb-1">تغيير الصورة</label>
                        <input type="file" name="image" class="form-control field-lux" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary w-100 rounded-pill">تحديث</button>
                    <button type="button" class="btn btn-glass-danger w-100 rounded-pill mt-2" data-bs-dismiss="modal">إلغاء</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endforeach
