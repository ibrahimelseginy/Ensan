
<div class="modal fade" id="createProjectModal" tabindex="-1" aria-hidden="true" style="z-index: 2050;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="{{ route('website.projects.store') }}" method="POST" enctype="multipart/form-data" class="modal-content modal-premium-dark border-0 text-white">
            @csrf
            <div class="modal-header border-bottom border-white border-opacity-10 bg-slate-800">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle-fill me-2 text-info"></i> إضافة مشروع جديد بنظام البطاقات المطور</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="row g-0">
                    {{-- Tabs Navigation --}}
                    <div class="col-md-3 border-end border-white border-opacity-10 bg-slate-800 p-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active mb-2 text-start" id="tab-basic-tab" data-bs-toggle="pill" data-bs-target="#tab-basic" type="button" role="tab"><i class="bi bi-info-circle me-2"></i> المعلومات الأساسية</button>
                            <button class="nav-link mb-2 text-start" id="tab-media-tab" data-bs-toggle="pill" data-bs-target="#tab-media" type="button" role="tab"><i class="bi bi-palette me-2"></i> الهوية والألوان</button>
                            <button class="nav-link mb-2 text-start" id="tab-dynamic-tab" data-bs-toggle="pill" data-bs-target="#tab-dynamic" type="button" role="tab"><i class="bi bi-grid-3x3-gap me-2"></i> المزايا والإحصائيات</button>
                            <button class="nav-link mb-2 text-start" id="tab-action-tab" data-bs-toggle="pill" data-bs-target="#tab-action" type="button" role="tab"><i class="bi bi-link-45deg me-2"></i> الروابط والإجراءات</button>
                        </div>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="col-md-9 p-4 ws-card-header" style="max-height: 70vh; overflow-y: auto; background-color: var(--ws-bg-card-header) !important;">
                        <div class="tab-content" id="v-pills-tabContent">
                            
                            {{-- Tab 1: Basic Info --}}
                            <div class="tab-pane fade show active" id="tab-basic" role="tabpanel">
                                <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">المعلومات الأساسية</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="p-3 rounded-4 bg-slate-800 border border-info border-opacity-30 mb-3" style="background-color: var(--ws-border) !important;">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="x-small fw-bold text-info mb-0"><i class="bi bi-star-fill me-1"></i> الشارة المميزة (Badge) - التي في الصورة</h6>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" name="show_badge" checked id="showBadgeCheck">
                                                    <label class="form-check-label fw-bold x-small" for="showBadgeCheck">تفعيل ظهور الشارة</label>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold ws-label">نص الشارة (مثلاً: مميز)</label>
                                                    <input type="text" name="badge_text" class="form-control form-control-sm ws-input shadow-none" placeholder="مثلاً: مميز">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold ws-label">أيقونة الشارة (اختر من المعرض)</label>
                                                    <input type="file" name="badge_icon_file" class="form-control form-control-sm ws-input shadow-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label x-small fw-bold ws-label">اسم المشروع (العنوان)</label>
                                        <input type="text" name="name" class="form-control ws-input" required placeholder="مثلاً: مشروع كفالة اليتيم">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold ws-label">التصنيف الرئيسي (Category)</label>
                                        <input type="text" name="category" class="form-control ws-input" placeholder="مثلاً: كفالات">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold ws-label">قيمة الكفالة الشهرية (بالأرقام)</label>
                                        <input type="text" name="sponsorship_details" class="form-control ws-input shadow-none" placeholder="مثلاً: 500">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label x-small fw-bold ws-label">وصف مختصر (حتى سطرين)</label>
                                        <textarea name="short_description" class="form-control ws-input" rows="2" placeholder="وصف قصير يظهر في البطاقة الرئيسية..."></textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label x-small fw-bold ws-label">الوصف الكامل (محتوى الصفحة)</label>
                                        <textarea name="website_content" class="form-control ws-input" rows="4" placeholder="التفاصيل الكاملة التي تظهر عند الضغط على البطاقة..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Identity & Colors --}}
                            <div class="tab-pane fade" id="tab-media" role="tabpanel">
                                <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">الهوية البصرية والألوان</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold ws-label">صورة الخلفية (Cover Image)</label>
                                        <input type="file" name="image" class="form-control ws-input" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold ws-label">أيقونة المشروع (Icon)</label>
                                        <input type="file" name="icon" class="form-control ws-input" accept="image/*">
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <h6 class="x-small fw-bold text-info mb-3"><i class="bi bi-palette me-1"></i> ثيم الألوان (Theme Colors)</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold ws-label">اللون الأساسي</label>
                                        <input type="color" name="theme_colors[primaryColor]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="#0d6efd">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold ws-label">لون شفاف (Tint)</label>
                                        <input type="color" name="theme_colors[lightTint]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="#e7f1ff">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold ws-label">لون الإطار</label>
                                        <input type="color" name="theme_colors[borderColor]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="#cfe2ff">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold ws-label">لون الأيقونة</label>
                                        <input type="color" name="theme_colors[iconColor]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="#0d6efd">
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 3: Dynamic Data --}}
                            <div class="tab-pane fade" id="tab-dynamic" role="tabpanel">
                                <div class="row g-4">
                                    {{-- Features Section --}}
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">قائمة المزايا / الخدمات (Pills)</h6>
                                        <div id="features-container">
                                            <div class="row g-2 mb-2 feature-row animate-slide-up align-items-center">
                                                <div class="col-md-4">
                                                    <input type="text" name="features[0][text]" class="form-control form-control-sm ws-input" placeholder="نص الميزة (مثلاً: دعم طبي)">
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="input-group input-group-sm">
                                                        <label class="input-group-text bg-slate-800 text-info border-secondary x-small py-0">أيقونة</label>
                                                        <input type="file" name="features[0][icon_file]" class="form-control ws-input">
                                                    </div>
                                                </div>
                                                <div class="col-md-2">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row w-100 py-1" onclick="this.closest('.feature-row').remove()"><i class="bi bi-trash"></i> حذف</button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="addFeatureRow()"><i class="bi bi-plus-lg"></i> إضافة ميزة</button>
                                    </div>

                                    {{-- Stats Section --}}
                                    <div class="col-12 mt-4">
                                        <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">قسم الإحصائيات</h6>
                                        <div id="stats-container">
                                            <div class="row g-2 mb-3 stat-row animate-slide-up align-items-center">
                                                <div class="col-md-3">
                                                    <input type="text" name="stats[0][value]" class="form-control form-control-sm ws-input" placeholder="القيمة">
                                                </div>
                                                <div class="col-md-3">
                                                    <input type="text" name="stats[0][label]" class="form-control form-control-sm ws-input" placeholder="الوصف">
                                                </div>
                                                <div class="col-md-5">
                                                    <div class="input-group input-group-sm">
                                                        <label class="input-group-text bg-slate-800 text-warning border-secondary x-small py-0">أيقونة</label>
                                                        <input type="file" name="stats[0][icon_file]" class="form-control ws-input">
                                                    </div>
                                                </div>
                                                <div class="col-md-1">
                                                    <button type="button" class="btn btn-sm btn-outline-danger remove-row w-100 py-1" onclick="this.closest('.stat-row').remove()"><i class="bi bi-trash"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-warning mt-2" onclick="addStatRow()"><i class="bi bi-plus-lg"></i> إضافة إحصائية</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 4: Actions & Badges --}}
                            <div class="tab-pane fade" id="tab-action" role="tabpanel">
                                <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">الإجراءات والروابط الذكية</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-20 text-info x-small mb-0 py-2">
                                            <i class="bi bi-info-circle me-2"></i> الزر ثابت بنص <strong>"تبرع الآن"</strong> ورابط <strong>"صفحة التبرع"</strong>.
                                            <input type="hidden" name="action_text" value="تبرع الان">
                                            <input type="hidden" name="action_url" value="http://127.0.0.1:4200/donate">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="p-3 rounded-4 bg-slate-800 border border-warning border-opacity-30 mb-3" style="background-color: var(--ws-border) !important;">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold ws-label">أيقونة الزر (اختر من المعرض)</label>
                                                    <input type="file" name="action_icon_file" class="form-control form-control-sm ws-input" accept="image/*"
                                                        onchange="document.getElementById('createActionIconPreview').src = window.URL.createObjectURL(this.files[0]); document.getElementById('createActionIconPreview').classList.remove('d-none');">
                                                </div>
                                                <div class="col-md-2 mt-4 text-center">
                                                    <img src="" id="createActionIconPreview" class="rounded bg-secondary bg-opacity-25 d-none" style="width: 35px; height: 35px; object-fit: contain;">
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch mb-0 mt-4 ms-3">
                                                        <input class="form-check-input" type="checkbox" name="is_visible" checked id="isVisibleCheck">
                                                        <label class="form-check-label fw-bold x-small" for="isVisibleCheck">المشروع مرئي للعامة</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>

            <div class="modal-footer border-top border-white border-opacity-10 bg-slate-800">
                <button type="button" class="btn btn-sm btn-outline-light rounded-pill px-4" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-sm btn-info text-white rounded-pill px-5 shadow-sm fw-bold">حفظ المشروع</button>
            </div>
        </form>
    </div>
</div>

<script>
    let featureIdx = 1;
    let statIdx = 1;

    function addFeatureRow() {
        const container = document.getElementById('features-container');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 feature-row animate-slide-up align-items-center';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="features[${featureIdx}][text]" class="form-control form-control-sm ws-input" placeholder="نص الميزة">
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-slate-800 text-info border-secondary x-small py-0">أيقونة</label>
                    <input type="file" name="features[${featureIdx}][icon_file]" class="form-control ws-input">
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row w-100 py-1" onclick="this.closest('.feature-row').remove()"><i class="bi bi-trash"></i> حذف</button>
            </div>
        `;
        container.appendChild(row);
        featureIdx++;
    }

    function addStatRow() {
        const container = document.getElementById('stats-container');
        const row = document.createElement('div');
        row.className = 'row g-2 mb-3 stat-row animate-slide-up align-items-center';
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="stats[${statIdx}][value]" class="form-control form-control-sm ws-input" placeholder="القيمة">
            </div>
            <div class="col-md-3">
                <input type="text" name="stats[${statIdx}][label]" class="form-control form-control-sm ws-input" placeholder="الوصف">
            </div>
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <label class="input-group-text bg-slate-800 text-warning border-secondary x-small py-0">أيقونة</label>
                    <input type="file" name="stats[${statIdx}][icon_file]" class="form-control ws-input">
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger remove-row w-100 py-1" onclick="this.closest('.stat-row').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        statIdx++;
    }
</script>





