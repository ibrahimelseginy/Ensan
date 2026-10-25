@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <div>
                <h4 class="mb-0">
                    <i class="bi bi-pencil-square text-primary"></i>
                    تعديل مهمة موظف
                </h4>
                <p class="text-muted small mt-1 mb-0">تحديث تفاصيل المهمة وحالتها.</p>
            </div>
            <a href="{{ route('employee-tasks.show', $task) }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-right me-1"></i> رجوع
            </a>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <form method="POST" action="{{ route('employee-tasks.update', $task) }}">
                            @csrf
                            @method('PUT')
                            <div class="row g-4">
                                <div class="col-md-8">
                                    <label class="form-label fw-medium text-secondary">عنوان المهمة <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="title" class="form-control form-control-lg fs-6"
                                        value="{{ $task->title }}" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-medium text-secondary">المرحلة / الحالة</label>
                                    <select name="status" class="form-select form-select-lg fs-6">
                                        <option value="pending" @selected($task->status === 'pending')>📋 قائمة المهام</option>
                                        <option value="in_progress" @selected($task->status === 'in_progress')>⏳ قيد التنفيذ</option>
                                        <option value="under_review" @selected($task->status === 'under_review')>🔍 قيد المراجعة</option>
                                        <option value="done" @selected($task->status === 'done')>✅ مكتملة ومراجعة</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">الأولوية</label>
                                    <select name="priority" class="form-select form-select-lg fs-6">
                                        <option value="medium" @selected(($task->priority ?? 'medium') === 'medium')>📌 عادية</option>
                                        <option value="high" @selected(($task->priority ?? '') === 'high')>⚡ أولوية قصوى</option>
                                        <option value="urgent" @selected(($task->priority ?? '') === 'urgent')>🔥 عاجل جداً</option>
                                        <option value="low" @selected(($task->priority ?? '') === 'low')>🌱 منخفضة</option>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">تاريخ الاستحقاق (Deadline)</label>
                                    <input type="date" name="due_date" class="form-control form-control-lg fs-6"
                                        value="{{ $task->due_date?->format('Y-m-d') }}">
                                </div>

                                <div class="col-12">
                                    <label class="form-label fw-medium text-secondary">وصف وتفاصيل المهمة</label>
                                    <textarea name="description" class="form-control form-control-lg fs-6"
                                        rows="4">{{ $task->description }}</textarea>
                                </div>

                                <div class="col-12">
                                    <hr class="text-muted opacity-25">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">الموظف (المكلّف الرئيسي)</label>
                                    <select name="assigned_to" class="form-select form-select-lg fs-6">
                                        <option value="">-- اختر الموظف --</option>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" @selected($task->assigned_to == $u->id)>{{ $u->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    @php $selectedShared = $task->assignees->pluck('id')->toArray(); @endphp
                                    <label class="form-label fw-medium text-secondary d-flex justify-content-between align-items-center mb-1">
                                        <span><i class="bi bi-people-fill text-success me-1"></i> فريق العمل المشترك (المهام المشتركة)</span>
                                        <span class="badge bg-light text-muted border px-2 py-1" id="editSharedBadge">تم اختيار {{ count($selectedShared) }}</span>
                                    </label>

                                    <!-- Selected Chips Display Area -->
                                    <div class="employee-chips-box p-2 rounded-3 border bg-light d-flex flex-wrap gap-2 align-items-center mb-2" id="editSharedChipsContainer" style="min-height: 48px;">
                                        <span class="text-muted small empty-chips-placeholder {{ count($selectedShared) > 0 ? 'd-none' : '' }}" id="editSharedPlaceholder">
                                            <i class="bi bi-person-plus me-1"></i> اختر من القائمة أدناه لإضافة أعضاء لفريق المهمة
                                        </span>
                                    </div>

                                    <!-- Hidden Native Multiple Select -->
                                    <select name="shared_with[]" id="editSharedSelect" multiple class="d-none">
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" @selected(in_array($u->id, $selectedShared))>{{ $u->name }}</option>
                                        @endforeach
                                    </select>

                                    <!-- Employee Picker Box with Live Search -->
                                    <div class="employee-picker-card rounded-3 border bg-white shadow-sm overflow-hidden">
                                        <div class="p-2 border-bottom bg-light d-flex align-items-center gap-2">
                                            <div class="input-group input-group-sm">
                                                <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
                                                <input type="text" class="form-control border-0 bg-white" placeholder="بحث باسم الموظف..." oninput="filterEmployeePicker('editShared', this.value)">
                                            </div>
                                            <button type="button" class="btn btn-xs btn-outline-secondary text-nowrap rounded-pill px-2 py-1" style="font-size: 0.72rem;" onclick="clearAllEmployeePicker('editShared')">
                                                إلغاء الكل
                                            </button>
                                        </div>
                                        <div class="employee-picker-list p-2" id="editSharedList" style="max-height: 180px; overflow-y: auto;">
                                            @foreach($users as $u)
                                            @php $isSelected = in_array($u->id, $selectedShared); @endphp
                                            <div class="employee-picker-item d-flex align-items-center justify-content-between p-2 rounded-2 mb-1 {{ $isSelected ? 'is-selected' : '' }}" 
                                                 data-id="{{ $u->id }}" 
                                                 data-name="{{ $u->name }}"
                                                 onclick="toggleEmployeePicker('editShared', '{{ $u->id }}', '{{ addslashes($u->name) }}')">
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="avatar-circle-sm bg-primary text-white">{{ mb_substr($u->name, 0, 1) }}</div>
                                                    <span class="emp-name small fw-semibold text-dark">{{ $u->name }}</span>
                                                </div>
                                                <div class="check-box-indicator">
                                                    <i class="bi bi-check-circle-fill text-success fs-5 check-icon {{ $isSelected ? '' : 'd-none' }}"></i>
                                                    <i class="bi bi-circle text-muted fs-5 uncheck-icon {{ $isSelected ? 'd-none' : '' }}"></i>
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-1" style="font-size: 0.72rem;">الموظفون المشتركون تظهر لهم المهمة في لوحة كانبان الخاصة بهم ويمكنك إضافتهم أو حذفهم بضغطة واحدة.</small>
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-medium text-secondary">رفع مرفقات أو إثباتات إضافية</label>
                                    <input type="file" name="attachments[]" multiple class="form-control form-control-lg fs-6">
                                </div>

                                <div class="col-12">
                                    <hr class="text-muted opacity-25">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-medium text-secondary">ارتباط المهمة بـ</label>
                                    @php $rel = $task->project_id ? 'project' : ($task->campaign_id ? 'campaign' : ($task->guest_house_id ? 'guest_house' : '')); @endphp
                                    <select id="relType" class="form-select form-select-lg fs-6">
                                        <option value="" @selected($rel === '')>-- لا يوجد ارتباط --</option>
                                        <option value="project" @selected($rel === 'project')>مشروع</option>
                                        <option value="campaign" @selected($rel === 'campaign')>حملة</option>
                                        <option value="guest_house" @selected($rel === 'guest_house')>دار ضيافة</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <div class="rel rel-project" style="display:none">
                                        <label class="form-label fw-medium text-secondary">المشروع</label>
                                        <select name="project_id" class="form-select form-select-lg fs-6" id="relProject"
                                            disabled>
                                            <option value="">اختر المشروع...</option>
                                            @foreach($projects as $p)
                                                <option value="{{ $p->id }}" @selected($task->project_id == $p->id)>{{ $p->name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="rel rel-campaign" style="display:none">
                                        <label class="form-label fw-medium text-secondary">الحملة</label>
                                        <select name="campaign_id" class="form-select form-select-lg fs-6" id="relCampaign"
                                            disabled>
                                            <option value="">اختر الحملة...</option>
                                            @foreach($campaigns as $c)
                                                <option value="{{ $c->id }}" @selected($task->campaign_id == $c->id)>
                                                    {{ $c->name }} {{ $c->season_year ? '(' . $c->season_year . ')' : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="rel rel-guest_house" style="display:none">
                                        <label class="form-label fw-medium text-secondary">دار الضيافة</label>
                                        <select name="guest_house_id" class="form-select form-select-lg fs-6"
                                            id="relGuestHouse" disabled>
                                            <option value="">اختر دار الضيافة...</option>
                                            @foreach($guestHouses as $gh)
                                                <option value="{{ $gh->id }}" @selected($task->guest_house_id == $gh->id)>
                                                    {{ $gh->name }} {{ $gh->location ? ' - ' . $gh->location : '' }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 mt-4">
                                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3">تقييم الأداء</h5>
                                    <div class="row g-3">
                                        <div class="col-md-12">
                                            <label class="form-label fw-medium text-secondary d-block">التقييم (من
                                                5)</label>
                                            <div class="btn-group" role="group" aria-label="Rating">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <input type="radio" class="btn-check" name="rating" id="rating{{$i}}"
                                                        value="{{$i}}" {{ $task->rating == $i ? 'checked' : '' }}
                                                        autocomplete="off">
                                                    <label class="btn btn-outline-warning" for="rating{{$i}}">{{$i}} <i
                                                            class="bi bi-star-fill"></i></label>
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <label class="form-label fw-medium text-secondary">ملاحظات التقييم</label>
                                            <textarea name="evaluation_notes" class="form-control" rows="2"
                                                placeholder="أضف ملاحظات حول أداء الموظف في هذه المهمة...">{{ $task->evaluation_notes }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex gap-2 justify-content-end mt-5">
                                <a href="{{ route('employee-tasks.show', $task) }}"
                                    class="btn btn-outline-secondary btn-lg px-4 rounded-pill">
                                    <i class="bi bi-x-lg me-1"></i> إلغاء
                                </a>
                                <button type="submit" class="btn btn-primary btn-lg px-5 rounded-pill shadow-sm">
                                    <i class="bi bi-check-lg me-1"></i> حفظ التعديلات
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var sel = document.getElementById('relType');
            function toggle() {
                var v = sel ? sel.value : '';
                ['project', 'campaign', 'guest_house'].forEach(function (k) {
                    var box = document.querySelector('.rel.rel-' + k);
                    var inp = document.getElementById('rel' + k.replace(/(?:^|\s)\S/g, function (a) { return a.toUpperCase(); }).replace('_', ''));

                    // Adjust ID construction if simple camelCase didn't match
                    if (k == 'project') inp = document.getElementById('relProject');
                    if (k == 'campaign') inp = document.getElementById('relCampaign');
                    if (k == 'guest_house') inp = document.getElementById('relGuestHouse');

                    if (box) {
                        if (k == v) {
                            box.style.display = 'block';
                            if (inp) inp.disabled = false;
                        } else {
                            box.style.display = 'none';
                            if (inp) inp.disabled = true;
                        }
                    }
                });
            }
            if (sel) {
                sel.addEventListener('change', toggle);
                toggle();
            }

            // Initialize Employee Multi-Picker for edit mode
            initEmployeePicker('editShared');
        });

        /* ============================================================== */
        /* EMPLOYEE MULTI-PICKER CONTROLLER JAVASCRIPT                    */
        /* ============================================================== */
        window.employeePickers = window.employeePickers || {};

        function initEmployeePicker(prefix) {
            const select = document.getElementById(prefix + 'Select');
            if (!select) return;

            window.employeePickers[prefix] = new Map();

            Array.from(select.options).forEach(opt => {
                if (opt.selected) {
                    window.employeePickers[prefix].set(String(opt.value), opt.text);
                }
            });

            renderEmployeePicker(prefix);
        }

        function renderEmployeePicker(prefix) {
            const map = window.employeePickers[prefix];
            const container = document.getElementById(prefix + 'ChipsContainer');
            const placeholder = document.getElementById(prefix + 'Placeholder');
            const badge = document.getElementById(prefix + 'Badge');
            const select = document.getElementById(prefix + 'Select');
            const list = document.getElementById(prefix + 'List');

            if (!container || !select) return;

            container.querySelectorAll('.employee-chip').forEach(el => el.remove());

            if (map.size === 0) {
                if (placeholder) placeholder.classList.remove('d-none');
                if (badge) badge.textContent = 'تم اختيار 0';
            } else {
                if (placeholder) placeholder.classList.add('d-none');
                if (badge) badge.textContent = 'تم اختيار ' + map.size;
            }

            const selectedIds = new Set(Array.from(map.keys()));
            Array.from(select.options).forEach(opt => {
                opt.selected = selectedIds.has(String(opt.value));
            });

            map.forEach((name, id) => {
                const chip = document.createElement('div');
                chip.className = 'employee-chip';
                chip.innerHTML = `
                    <div class="chip-avatar">${name.trim().charAt(0)}</div>
                    <span>${name}</span>
                    <button type="button" class="btn-remove-chip" title="حذف" onclick="event.stopPropagation(); removeEmployeePicker('${prefix}', '${id}')">
                        <i class="bi bi-x"></i>
                    </button>
                `;
                container.appendChild(chip);
            });

            if (list) {
                list.querySelectorAll('.employee-picker-item').forEach(row => {
                    const rowId = String(row.getAttribute('data-id'));
                    const isSelected = selectedIds.has(rowId);
                    const checkIcon = row.querySelector('.check-icon');
                    const uncheckIcon = row.querySelector('.uncheck-icon');

                    if (isSelected) {
                        row.classList.add('is-selected');
                        if (checkIcon) checkIcon.classList.remove('d-none');
                        if (uncheckIcon) uncheckIcon.classList.add('d-none');
                    } else {
                        row.classList.remove('is-selected');
                        if (checkIcon) checkIcon.classList.add('d-none');
                        if (uncheckIcon) uncheckIcon.classList.remove('d-none');
                    }
                });
            }
        }

        function toggleEmployeePicker(prefix, id, name) {
            const map = window.employeePickers[prefix];
            if (!map) return;

            const key = String(id);
            if (map.has(key)) {
                map.delete(key);
            } else {
                map.set(key, name);
            }
            renderEmployeePicker(prefix);
        }

        function removeEmployeePicker(prefix, id) {
            const map = window.employeePickers[prefix];
            if (!map) return;

            map.delete(String(id));
            renderEmployeePicker(prefix);
        }

        function clearAllEmployeePicker(prefix) {
            const map = window.employeePickers[prefix];
            if (!map) return;
            map.clear();
            renderEmployeePicker(prefix);
        }

        function filterEmployeePicker(prefix, query) {
            const list = document.getElementById(prefix + 'List');
            if (!list) return;

            const q = (query || '').toLowerCase().trim();
            list.querySelectorAll('.employee-picker-item').forEach(row => {
                const name = (row.getAttribute('data-name') || '').toLowerCase();
                if (!q || name.includes(q)) {
                    row.classList.remove('d-none');
                } else {
                    row.classList.add('d-none');
                }
            });
        }
    </script>

    <style>
    .employee-chips-box {
        background: #f8fafc;
        border: 1.5px dashed #cbd5e1;
        transition: all 0.2s ease;
    }
    .employee-chips-box:hover {
        border-color: #10b981;
    }
    .employee-chip {
        background: #ffffff;
        border: 1px solid #10b981;
        color: #065f46;
        border-radius: 50px;
        padding: 4px 10px 4px 6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 0.82rem;
        font-weight: 700;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.06);
        animation: fadeInChip 0.2s ease;
    }
    @keyframes fadeInChip {
        from { opacity: 0; transform: scale(0.9); }
        to { opacity: 1; transform: scale(1); }
    }
    .employee-chip .chip-avatar {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.68rem;
        font-weight: 800;
    }
    .employee-chip .btn-remove-chip {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border: none;
        border-radius: 50%;
        width: 18px;
        height: 18px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        cursor: pointer;
        transition: all 0.2s ease;
        padding: 0;
        line-height: 1;
    }
    .employee-chip .btn-remove-chip:hover {
        background: #dc2626;
        color: #ffffff;
        transform: scale(1.15);
    }
    .employee-picker-card {
        border: 1px solid #e2e8f0;
    }
    .employee-picker-item {
        cursor: pointer;
        transition: all 0.15s ease;
        border: 1px solid transparent;
    }
    .employee-picker-item:hover {
        background: #f1f5f9;
    }
    .employee-picker-item.is-selected {
        background: #ecfdf5 !important;
        border-color: #a7f3d0;
    }
    .employee-picker-item.is-selected .emp-name {
        color: #065f46 !important;
        font-weight: 700 !important;
    }
    .avatar-circle-sm {
        width: 26px;
        height: 26px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.72rem;
    }
    </style>
@endsection

