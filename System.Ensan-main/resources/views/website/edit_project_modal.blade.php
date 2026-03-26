<style>
    /* Robust Solid Dark Theme for Project Modals */
    .modal-premium-dark {
        background-color: #0f172a !important; /* Solid Slate 900 */
        background-image: none !important;
        opacity: 1 !important;
        border: 1px solid rgba(255, 255, 255, 0.15) !important;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.8) !important;
        backdrop-filter: none !important;
        -webkit-backdrop-filter: none !important;
    }
    .modal-premium-dark .modal-header, 
    .modal-premium-dark .modal-footer,
    .bg-slate-800 {
        background-color: #1e293b !important; /* Solid Slate 800 */
    }
    .modal-premium-dark .modal-body {
        background-color: #0f172a !important;
    }
    .modal-premium-dark .nav-pills .nav-link {
        color: #94a3b8;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    .modal-premium-dark .nav-pills .nav-link.active {
        background: #3b82f6 !important;
        color: white;
    }
    .modal-premium-dark .form-control,
    .modal-premium-dark .form-select,
    .modal-premium-dark textarea {
        background-color: #1e293b !important; /* Solid Slate 800 */
        border-color: #334155 !important;
        color: #f8fafc !important;
        opacity: 1 !important;
    }
    .modal-premium-dark .form-control:focus {
        border-color: #3b82f6 !important;
        box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.2) !important;
    }
</style>



<div class="modal fade" id="editProjectModal{{ $project->id }}" tabindex="-1" aria-hidden="true" style="z-index: 2050;">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <form action="{{ route('website.projects.update', $project->id) }}" method="POST" enctype="multipart/form-data" class="modal-content modal-premium-dark border-0 text-white">
            @csrf
            @method('PUT')
            <div class="modal-header border-bottom border-white border-opacity-10 bg-slate-800">
                <h5 class="modal-title fw-bold"><i class="bi bi-pencil-square me-2 text-warning"></i> تعديل المشروع: {{ $project->name }}</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body p-0">
                <div class="row g-0">
                    {{-- Tabs Navigation --}}
                    <div class="col-md-3 border-end border-white border-opacity-10 bg-slate-800 p-3">
                        <div class="nav flex-column nav-pills" id="v-pills-tab-edit-{{ $project->id }}" role="tablist" aria-orientation="vertical">
                            <button class="nav-link active mb-2 text-start" id="tab-basic-tab-{{ $project->id }}" data-bs-toggle="pill" data-bs-target="#tab-basic-{{ $project->id }}" type="button" role="tab"><i class="bi bi-info-circle me-2"></i> المعلومات الأساسية</button>
                            <button class="nav-link mb-2 text-start" id="tab-media-tab-{{ $project->id }}" data-bs-toggle="pill" data-bs-target="#tab-media-{{ $project->id }}" type="button" role="tab"><i class="bi bi-palette me-2"></i> الهوية والألوان</button>
                            <button class="nav-link mb-2 text-start" id="tab-dynamic-tab-{{ $project->id }}" data-bs-toggle="pill" data-bs-target="#tab-dynamic-{{ $project->id }}" type="button" role="tab"><i class="bi bi-grid-3x3-gap me-2"></i> المزايا والإحصائيات</button>
                            <button class="nav-link mb-2 text-start" id="tab-action-tab-{{ $project->id }}" data-bs-toggle="pill" data-bs-target="#tab-action-{{ $project->id }}" type="button" role="tab"><i class="bi bi-link-45deg me-2"></i> الروابط والإجراءات</button>
                        </div>
                    </div>

                    {{-- Tabs Content --}}
                    <div class="col-md-9 p-4 bg-slate-900" style="max-height: 70vh; overflow-y: auto; background-color: #0f172a !important;">
                        <div class="tab-content" id="v-pills-tabContent-{{ $project->id }}">
                            
                            {{-- Tab 1: Basic Info --}}
                            <div class="tab-pane fade show active" id="tab-basic-{{ $project->id }}" role="tabpanel">
                                <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">المعلومات الأساسية</h6>
                                <div class="row g-3">
                                    <div class="col-md-12">
                                        <div class="p-3 rounded-4 bg-slate-800 border border-info border-opacity-30 mb-3" style="background-color: #1e293b !important;">
                                            <div class="d-flex align-items-center justify-content-between mb-3">
                                                <h6 class="x-small fw-bold text-info mb-0"><i class="bi bi-star-fill me-1"></i> الشارة المميزة (Badge) - التي في الصورة</h6>
                                                <div class="form-check form-switch mb-0">
                                                    <input class="form-check-input" type="checkbox" name="show_badge" {{ $project->show_badge ? 'checked' : '' }} id="showBadgeCheck{{ $project->id }}">
                                                    <label class="form-check-label fw-bold x-small" for="showBadgeCheck{{ $project->id }}">تفعيل ظهور الشارة</label>
                                                </div>
                                            </div>
                                            <div class="row g-2">
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-slate-400">نص الشارة (مثلاً: مميز)</label>
                                                    <input type="text" name="badge_text" class="form-control form-control-sm bg-dark text-white border-secondary shadow-none" value="{{ $project->badge_text }}" placeholder="مثلاً: مميز">
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-slate-400">أيقونة الشارة (اختر من المعرض)</label>
                                                    <input type="file" name="badge_icon_file" class="form-control form-control-sm bg-dark text-white border-secondary shadow-none">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-12">
                                        <label class="form-label x-small fw-bold text-slate-400">اسم المشروع (العنوان)</label>
                                        <input type="text" name="name" class="form-control bg-dark text-white border-secondary" required value="{{ $project->name }}">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold text-slate-400">التصنيف الرئيسي (Category)</label>
                                        <input type="text" name="category" class="form-control bg-dark text-white border-secondary" value="{{ $project->category }}">
                                    </div>

                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold text-slate-400">تفاصيل الكفالة (مثلاً: 500 ريال شهرياً)</label>
                                        <input type="text" name="sponsorship_details" class="form-control bg-dark text-white border-secondary shadow-none" value="{{ $project->sponsorship_details }}">
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label x-small fw-bold text-slate-400">وصف مختصر (حتى سطرين)</label>
                                        <textarea name="short_description" class="form-control bg-dark text-white border-secondary" rows="2">{{ $project->short_description }}</textarea>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label x-small fw-bold text-slate-400">الوصف الكامل (محتوى الصفحة)</label>
                                        <textarea name="website_content" class="form-control bg-dark text-white border-secondary" rows="4">{{ $project->website_content }}</textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 2: Identity & Colors --}}
                            <div class="tab-pane fade" id="tab-media-{{ $project->id }}" role="tabpanel">
                                <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">الهوية البصرية والألوان</h6>
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold text-slate-400">صورة الخلفية (Cover Image)</label>
                                        @if($project->image_path)
                                            <div class="mb-2"><img src="{{ $project->image_url }}" class="rounded" style="height: 50px;"></div>
                                        @endif
                                        <input type="file" name="image" class="form-control bg-dark text-white border-secondary" accept="image/*">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label x-small fw-bold text-slate-400">أيقونة المشروع (Icon)</label>
                                        @if($project->icon_path)
                                            <div class="mb-2"><img src="{{ $project->image_url }}" class="rounded" style="height: 50px;"></div>
                                        @endif
                                        <input type="file" name="icon" class="form-control bg-dark text-white border-secondary" accept="image/*">
                                    </div>
                                    <div class="col-md-12 mt-4">
                                        <h6 class="x-small fw-bold text-info mb-3"><i class="bi bi-palette me-1"></i> ثيم الألوان (Theme Colors)</h6>
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-slate-400">اللون الأساسي</label>
                                        <input type="color" name="theme_colors[primaryColor]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="{{ $project->theme_colors['primaryColor'] ?? '#0d6efd' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-slate-400">لون شفاف (Tint)</label>
                                        <input type="color" name="theme_colors[lightTint]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="{{ $project->theme_colors['lightTint'] ?? '#e7f1ff' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-slate-400">لون الإطار</label>
                                        <input type="color" name="theme_colors[borderColor]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="{{ $project->theme_colors['borderColor'] ?? '#cfe2ff' }}">
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label x-small fw-bold text-slate-400">لون الأيقونة</label>
                                        <input type="color" name="theme_colors[iconColor]" class="form-control form-control-sm bg-dark border-secondary h-auto" value="{{ $project->theme_colors['iconColor'] ?? '#0d6efd' }}">
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 3: Dynamic Data --}}
                            <div class="tab-pane fade" id="tab-dynamic-{{ $project->id }}" role="tabpanel">
                                <div class="row g-4">
                                    {{-- Features Section --}}
                                    <div class="col-12">
                                        <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">قائمة المزايا / الخدمات (Pills)</h6>
                                        <div id="features-container-{{ $project->id }}" data-count="{{ count($project->features ?? []) }}">
                                            @php $fIdx = 0; @endphp
                                            @foreach(($project->features ?? []) as $feature)
                                                <div class="row g-2 mb-2 feature-row-edit align-items-center">
                                                    <div class="col-md-4">
                                                        <input type="text" name="features[{{ $fIdx }}][text]" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $feature['text'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="input-group input-group-sm">
                                                            @if(!empty($feature['icon']))
                                                                <span class="input-group-text bg-slate-800 border-secondary"><img src="{{ asset('storage/' . $feature['icon']) }}" style="width: 16px;"></span>
                                                            @endif
                                                            <input type="file" name="features[{{ $fIdx }}][icon_file]" class="form-control bg-dark text-white border-secondary">
                                                            <input type="hidden" name="features[{{ $fIdx }}][icon]" value="{{ $feature['icon'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1" onclick="this.closest('.feature-row-edit').remove()"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </div>
                                                @php $fIdx++; @endphp
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-info mt-2" onclick="addFeatureRowEdit({{ $project->id }})"><i class="bi bi-plus-lg"></i> إضافة ميزة</button>
                                    </div>

                                    {{-- Stats Section --}}
                                    <div class="col-12 mt-4">
                                        <h6 class="fw-bold mb-3 border-bottom border-white border-opacity-10 pb-2">قسم الإحصائيات</h6>
                                        <div id="stats-container-{{ $project->id }}" data-count="{{ count($project->stats ?? []) }}">
                                            @php $sIdx = 0; @endphp
                                            @foreach(($project->stats ?? []) as $stat)
                                                <div class="row g-2 mb-3 stat-row-edit align-items-center">
                                                    <div class="col-md-3">
                                                        <input type="text" name="stats[{{ $sIdx }}][value]" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $stat['value'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-3">
                                                        <input type="text" name="stats[{{ $sIdx }}][label]" class="form-control form-control-sm bg-dark text-white border-secondary" value="{{ $stat['label'] ?? '' }}">
                                                    </div>
                                                    <div class="col-md-5">
                                                        <div class="input-group input-group-sm">
                                                            @if(!empty($stat['icon']))
                                                                <span class="input-group-text bg-slate-800 border-secondary"><img src="{{ asset('storage/' . $stat['icon']) }}" style="width: 16px;"></span>
                                                            @endif
                                                            <input type="file" name="stats[{{ $sIdx }}][icon_file]" class="form-control bg-dark text-white border-secondary">
                                                            <input type="hidden" name="stats[{{ $sIdx }}][icon]" value="{{ $stat['icon'] ?? '' }}">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-1">
                                                        <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1" onclick="this.closest('.stat-row-edit').remove()"><i class="bi bi-trash"></i></button>
                                                    </div>
                                                </div>
                                                @php $sIdx++; @endphp
                                            @endforeach
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-warning mt-2" onclick="addStatRowEdit({{ $project->id }})"><i class="bi bi-plus-lg"></i> إضافة إحصائية</button>
                                    </div>
                                </div>
                            </div>

                            {{-- Tab 4: Actions & Badges --}}
                            <div class="tab-pane fade" id="tab-action-{{ $project->id }}" role="tabpanel">
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
                                        <div class="p-3 rounded-4 bg-slate-800 border border-warning border-opacity-30 mb-3" style="background-color: #1e293b !important;">
                                            <div class="row g-2 align-items-center">
                                                <div class="col-md-6">
                                                    <label class="form-label x-small fw-bold text-slate-400">أيقونة الزر (اختر من المعرض)</label>
                                                    <input type="file" name="action_icon_file" class="form-control form-control-sm bg-dark text-white border-secondary" accept="image/*"
                                                        onchange="document.getElementById('editActionIconPreview{{ $project->id }}').src = window.URL.createObjectURL(this.files[0]); document.getElementById('editActionIconPreview{{ $project->id }}').classList.remove('d-none');">
                                                </div>
                                                <div class="col-md-2 mt-4 text-center">
                                                    @if($project->action_icon)
                                                        <img src="{{ $project->getFileUrl('action_icon') }}" id="editActionIconPreview{{ $project->id }}" class="rounded bg-secondary bg-opacity-25" style="width: 35px; height: 35px; object-fit: contain;">
                                                    @else
                                                        <img src="" id="editActionIconPreview{{ $project->id }}" class="rounded bg-secondary bg-opacity-25 d-none" style="width: 35px; height: 35px; object-fit: contain;">
                                                    @endif
                                                </div>
                                                <div class="col-md-4">
                                                    <div class="form-check form-switch mb-0 mt-4 ms-3">
                                                        <input class="form-check-input" type="checkbox" name="is_visible" {{ $project->is_visible ? 'checked' : '' }} id="isVisibleCheck{{ $project->id }}">
                                                        <label class="form-check-label fw-bold x-small" for="isVisibleCheck{{ $project->id }}">المشروع مرئي للعامة</label>
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
                <button type="submit" class="btn btn-sm btn-warning text-dark rounded-pill px-5 shadow-sm fw-bold">تحديث المشروع</button>
            </div>
        </form>
    </div>
</div>


<script>
    window.editFeatureIdxs = window.editFeatureIdxs || {};
    window.editStatIdxs = window.editStatIdxs || {};

    function addFeatureRowEdit(projId) {
        const container = document.getElementById('features-container-' + projId);
        if (!window.editFeatureIdxs[projId]) {
            window.editFeatureIdxs[projId] = parseInt(container.getAttribute('data-count')) || 0;
        }
        const row = document.createElement('div');
        row.className = 'row g-2 mb-2 feature-row-edit align-items-center';
        row.innerHTML = `
            <div class="col-md-4">
                <input type="text" name="features[${window.editFeatureIdxs[projId]}][text]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="نص الميزة">
            </div>
            <div class="col-md-6">
                <div class="input-group input-group-sm">
                    <input type="file" name="features[${window.editFeatureIdxs[projId]}][icon_file]" class="form-control bg-dark text-white border-secondary">
                </div>
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1" onclick="this.closest('.feature-row-edit').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        window.editFeatureIdxs[projId]++;
    }

    function addStatRowEdit(projId) {
        const container = document.getElementById('stats-container-' + projId);
        if (!window.editStatIdxs[projId]) {
            window.editStatIdxs[projId] = parseInt(container.getAttribute('data-count')) || 0;
        }
        const row = document.createElement('div');
        row.className = 'row g-2 mb-3 stat-row-edit align-items-center';
        row.innerHTML = `
            <div class="col-md-3">
                <input type="text" name="stats[${window.editStatIdxs[projId]}][value]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="القيمة">
            </div>
            <div class="col-md-3">
                <input type="text" name="stats[${window.editStatIdxs[projId]}][label]" class="form-control form-control-sm bg-dark text-white border-secondary" placeholder="الوصف">
            </div>
            <div class="col-md-5">
                <div class="input-group input-group-sm">
                    <input type="file" name="stats[${window.editStatIdxs[projId]}][icon_file]" class="form-control bg-dark text-white border-secondary">
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger w-100 py-1" onclick="this.closest('.stat-row-edit').remove()"><i class="bi bi-trash"></i></button>
            </div>
        `;
        container.appendChild(row);
        window.editStatIdxs[projId]++;
    }
</script>


