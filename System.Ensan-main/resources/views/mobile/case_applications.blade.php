@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&family=Tajawal:wght@300;400;500;700;800&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    <div class="d-flex justify-content-between align-items-center mb-5 animate-reveal-down px-2">
        <div>
            @if(isset($type) && $type == 'zad')
                <h1 class="h2 fw-800 text-stats-main mb-1">طلبات مشروع زاد <span class="text-primary">(الموبايل)</span></h1>
                <p class="text-muted-theme small mb-0 font-outfit">Zad Orphans Project Applications Management</p>
            @elseif(isset($type) && $type == 'hope')
                <h1 class="h2 fw-800 text-stats-main mb-1">طلبات مشروع بعثاء الأمل <span class="text-primary">(الموبايل)</span></h1>
                <p class="text-muted-theme small mb-0 font-outfit">Hope Project - Mobile Applications</p>
            @elseif(isset($type))
                <h1 class="h2 fw-800 text-stats-main mb-1">طلبات: {{ ucfirst($type) }} <span class="text-primary">(الموبايل)</span></h1>
                <p class="text-muted-theme small mb-0">إدارة طلبات المساعدة الخاصة بنوع: {{ $type }}</p>
            @else
                <h1 class="h2 fw-800 text-stats-main mb-1">طلبات الحالات المستحقة <span class="text-primary">(الموبايل)</span></h1>
                <p class="text-muted-theme small mb-0">إدارة كافة طلبات المساعدة القادمة من تطبيق الهاتف</p>
            @endif
        </div>
        
        <div class="d-flex align-items-center gap-3">
            <!-- Bulk Action Bar - Moved to Bottom Fixed for Better UX -->
            <div id="bulk-action-bar" class="bulk-action-bar-elite shadow-premium d-none">
                <div class="d-flex align-items-center gap-4 bg-glass-onyx px-4 py-2 rounded-24 border border-primary animate-reveal-up">
                    <div class="selected-badge bg-primary px-3 py-1 rounded-pill">
                        <span class="text-white small fw-bold"><span id="selected-count">0</span> محدد</span>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-danger rounded-pill px-4 fw-800 shadow-sm btn-hover-scale" onclick="submitBulkDelete()">
                            <i class="bi bi-trash3-fill me-2"></i> حذف المحدد
                        </button>
                        <button type="button" class="btn btn-outline-light rounded-pill px-3 fw-bold" onclick="deselectAll()">إلغاء</button>
                    </div>
                </div>
            </div>
            
            <div class="form-check form-check-inline select-all-wrapper glass-badge-theme px-3 py-1 mb-0 border-0">
                <input class="form-check-input" type="checkbox" id="selectAllItems" onclick="toggleAllCheckboxes(this)">
                <label class="form-check-label fw-bold x-small ms-1" for="selectAllItems" style="cursor: pointer;">تحديد الكل</label>
            </div>

            <div class="glass-badge-theme px-4 py-2 border-0">
                <i class="bi bi-heart-fill me-2 text-primary"></i>
                <span class="fw-bold">إجمالي الحالات:</span> {{ $applications->count() }}
            </div>
        </div>
    </div>

    <!-- Filter Tabs Section -->
    <div class="filter-tabs-wrapper mb-5 animate-reveal-down" style="animation-delay: 0.1s">
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('mobile.case-applications.index') }}" class="tab-item {{ !isset($type) || !$type ? 'active' : '' }}">
                <i class="bi bi-grid-fill me-2"></i> الكل
            </a>
            <a href="{{ route('mobile.case-applications.index', ['type' => 'zad']) }}" class="tab-item {{ (isset($type) && $type == 'zad') ? 'active' : '' }}">
                <i class="bi bi-person-heart me-2"></i> مشروع زاد
            </a>
            <a href="{{ route('mobile.case-applications.index', ['type' => 'hope']) }}" class="tab-item {{ (isset($type) && $type == 'hope') ? 'active' : '' }}">
                <i class="bi bi-star-fill me-2"></i> بعثاء الأمل
            </a>
            <a href="{{ route('mobile.case-applications.index', ['type' => 'medical']) }}" class="tab-item {{ (isset($type) && $type == 'medical') ? 'active' : '' }}">
                <i class="bi bi-capsule me-2"></i> طبية
            </a>
            <a href="{{ route('mobile.case-applications.index', ['type' => 'financial']) }}" class="tab-item {{ (isset($type) && $type == 'financial') ? 'active' : '' }}">
                <i class="bi bi-cash-stack me-2"></i> مالية
            </a>
            <a href="{{ route('mobile.case-applications.index', ['type' => 'education']) }}" class="tab-item {{ (isset($type) && $type == 'education') ? 'active' : '' }}">
                <i class="bi bi-book-half me-2"></i> تعليمية
            </a>
        </div>
    </div>

    <!-- Hidden Bulk Form -->
    <form id="bulk-delete-form" action="{{ route('mobile.case-applications.bulk-destroy') }}" method="POST" class="d-none">
        @csrf
        <div id="bulk-ids-container"></div>
    </form>

    <div class="row g-4">
        @forelse($applications as $app)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.1 }}s">
            <div class="premium-case-card bg-stats-card-main border-light-subtle position-relative">
                <div class="card-inner-top">
                    <div class="card-meta mb-4">
                        <span class="badge-premium @if($app->status == 'pending') status-pending @elseif($app->status == 'reviewed') status-review @elseif($app->status == 'accepted') status-success @else status-danger @endif">
                            {{ $app->status == 'pending' ? 'بانتظار المراجعة' : ($app->status == 'reviewed' ? 'قيد الدراسة' : ($app->status == 'accepted' ? 'مقبول' : 'مرفوض')) }}
                        </span>
                        <div class="d-flex align-items-center gap-2">
                            <div class="case-type-badge x-small fw-bold">
                                @if($app->case_type == 'zad')
                                    <i class="bi bi-star-fill me-1 text-warning"></i> زاد الأيتام
                                @elseif($app->case_type == 'hope')
                                    <i class="bi bi-brightness-high-fill me-1 text-primary"></i> بعثاء الأمل
                                @else
                                    <i class="bi bi-folder-fill me-1 text-muted-theme"></i> {{ $app->case_type }}
                                @endif
                            </div>
                            <input type="checkbox" class="case-checkbox form-check-input-elite m-0" value="{{ $app->id }}" onclick="updateBulkBar()">
                        </div>
                    </div>
                    
                    <div class="card-user-info mb-3 px-4">
                        <h4 class="user-name text-truncate text-stats-main fw-bold" title="{{ $app->applicant_name }}">{{ $app->applicant_name }}</h4>
                        <p class="user-phone font-outfit text-primary fw-bold mb-2">{{ $app->applicant_phone }}</p>
                        <div class="location-tag x-small text-muted-theme fw-bold">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $app->governorate ?? 'غير محدد' }} - {{ $app->city ?? 'غير محدد' }}
                        </div>
                    </div>

                    <div class="description-box bg-stats-inner-item border border-light-subtle rounded-3 p-3 text-muted-theme x-small italic mb-4">
                        <p class="mb-0">{{ Str::limit($app->description, 120) }}</p>
                    </div>

                    <div class="mt-auto">
                        <button class="btn btn-details-glow w-100 fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}">
                            <i class="bi bi-file-earmark-medical me-2"></i> مراجعة الطلب بالكامل
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom bg-stats-inner-item border-top border-light-subtle">
                    <div class="row g-2">
                        <div class="col-6">
                            @if($app->id_image_path)
                            <a href="{{ Storage::disk('public')->url($app->id_image_path) }}" target="_blank" class="btn btn-action-card id-card-btn w-100">
                                <i class="bi bi-person-bounding-box"></i> الهوية
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100 x-small text-muted-theme">لا توجد صورة</button>
                            @endif
                        </div>
                        <div class="col-6">
                            @if($app->medical_report_path)
                            <a href="{{ Storage::disk('public')->url($app->medical_report_path) }}" target="_blank" class="btn btn-action-card report-btn w-100">
                                <i class="bi bi-file-earmark-medical-fill"></i> التقرير
                            </a>
                            @else
                            <button disabled class="btn btn-action-card disabled-btn w-100 x-small text-muted-theme">لا يوجد تقرير</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail Modal -->
        <div class="modal fade" id="modal{{ $app->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 28px; overflow: hidden;">
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-title">
                            <i class="bi bi-heart-pulse-fill me-2 text-primary"></i> دراسة حالة مستحقة (تطبيق)
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-stats-card-main">
                        <div class="row g-4 mb-4">
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">إسم مقدم الطلب</label>
                                <div class="text-stats-main fw-bold">{{ $app->applicant_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block text-end">رقم الهاتف</label>
                                <div class="font-outfit text-primary fw-bold text-end fs-5">{{ $app->applicant_phone }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">نوع المشروع</label>
                                <div class="text-stats-main fw-bold text-primary">{{ $app->case_type == 'zad' ? 'زاد الأيتام' : ($app->case_type == 'hope' ? 'بعثاء الأمل' : $app->case_type) }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block text-center">المحافظة</label>
                                <div class="text-stats-main fw-bold text-center">{{ $app->governorate ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group text-start">
                                <label class="text-muted-theme small fw-bold mb-2 d-block text-start">المدينة/المركز</label>
                                <div class="text-stats-main fw-bold text-start">{{ $app->city ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">العنوان بالتفصيل</label>
                                <div class="text-stats-main fw-bold">{{ $app->address ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted-theme small fw-bold mb-2 d-block">وصف الحالة والإحتياجات</label>
                                <div class="message-box bg-stats-inner-item border border-light-subtle rounded-4 p-4 text-muted-theme italic shadow-inner">
                                    "{{ $app->description }}"
                                </div>
                            </div>
                        </div>

                        <div class="admin-panel mt-5 p-4 rounded-4 bg-stats-inner-item border border-light-subtle">
                            <h6 class="mb-3 text-stats-main fw-bold border-start border-primary border-4 ps-3"><i class="bi bi-shield-lock me-2 text-primary"></i> قرار الإدارة</h6>
                            <form action="{{ route('mobile.case-applications.update', $app->id) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label x-small text-muted-theme fw-bold">تغيير حالة الطلب</label>
                                        <select name="status" class="form-select bg-stats-card-main border-light-subtle text-stats-main rounded-3 p-3">
                                            <option value="pending" {{ $app->status == 'pending' ? 'selected' : '' }}>بانتظار المراجعة</option>
                                            <option value="reviewed" {{ $app->status == 'reviewed' ? 'selected' : '' }}>قيد الدراسة</option>
                                            <option value="accepted" {{ $app->status == 'accepted' ? 'selected' : '' }}>مقبول (Accepted)</option>
                                            <option value="rejected" {{ $app->status == 'rejected' ? 'selected' : '' }}>مرفوض (Rejected)</option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label x-small text-muted-theme fw-bold">ملاحظات الباحث الاجتماعي / الإدارة</label>
                                        <textarea name="admin_notes" class="form-control bg-stats-card-main border-light-subtle text-stats-main rounded-3 p-3" rows="3">{{ $app->admin_notes }}</textarea>
                                    </div>
                                    <div class="col-12 mt-4 d-flex justify-content-between gap-3">
                                        <button type="submit" class="btn btn-success flex-grow-1 rounded-pill fw-bold py-3 shadow-sm">حفظ القرار والتعديلات</button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-5 fw-bold py-3" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب؟')) document.getElementById('del-form-{{ $app->id }}').submit()">حذف</button>
                                    </div>
                                </div>
                            </form>
                            <form id="del-form-{{ $app->id }}" action="{{ route('mobile.case-applications.destroy', $app->id) }}" method="POST" class="d-none">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 animate-up">
            <div class="empty-state-lux text-center py-5">
                <div class="empty-icon-wrapper mb-4 text-center d-flex justify-content-center">
                    <i class="bi bi-clipboard2-x" style="font-size: 5rem; opacity: 0.3;"></i>
                </div>
                <h5 class="fw-800 text-stats-main">لا توجد حالات حالياً</h5>
                <p class="text-muted-theme px-4 px-md-5">لم يقم أي مستخدم بتقديم طلبات مساعدة من هذا النوع عبر التطبيق بعد.</p>
                @if(isset($type))
                    <div class="mt-4">
                        <a href="{{ route('mobile.case-applications.index') }}" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">العودة للكل</a>
                    </div>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div>

<script>
    function toggleAllCheckboxes(master) {
        const checkboxes = document.querySelectorAll('.case-checkbox');
        checkboxes.forEach(cb => {
            cb.checked = master.checked;
        });
        updateBulkBar();
    }

    function updateBulkBar() {
        const checkboxes = document.querySelectorAll('.case-checkbox:checked');
        const bar = document.getElementById('bulk-action-bar');
        const countSpan = document.getElementById('selected-count');
        
        countSpan.textContent = checkboxes.length;
        
        if (checkboxes.length > 0) {
            bar.classList.remove('d-none');
            bar.classList.add('d-flex');
        } else {
            bar.classList.add('d-none');
            bar.classList.remove('d-flex');
            document.getElementById('selectAllItems').checked = false;
        }
    }

    function deselectAll() {
        const checkboxes = document.querySelectorAll('.case-checkbox');
        checkboxes.forEach(cb => cb.checked = false);
        document.getElementById('selectAllItems').checked = false;
        updateBulkBar();
    }

    function submitBulkDelete() {
        const checkboxes = document.querySelectorAll('.case-checkbox:checked');
        if (checkboxes.length === 0) return;

        const count = checkboxes.length;
        if (confirm(`هل أنت متأكد من حذف ${count} طلبات مختارة؟ سيتم مسح البيانات والملفات المرتبطة بها نهائياً.`)) {
            const container = document.getElementById('bulk-ids-container');
            container.innerHTML = '';
            checkboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                container.appendChild(input);
            });
            document.getElementById('bulk-delete-form').submit();
        }
    }
</script>

<style>
    body { background-color: var(--ws-bg-page) !important; color: var(--ws-text-primary) !important; font-family: 'Tajawal', 'Outfit', sans-serif; }
    .bg-theme-page { background-color: var(--ws-bg-page); }
    .fw-800 { font-weight: 800; }
    .font-outfit { font-family: 'Outfit', sans-serif; }

    /* Filter Tabs Styling */
    .filter-tabs-wrapper {
        background: var(--ws-bg-card);
        padding: 8px;
        border-radius: 100px;
        border: 1px solid var(--ws-border);
        box-shadow: 0 4px 15px rgba(0,0,0,0.02);
        display: inline-block;
    }
    .tab-item {
        padding: 10px 24px;
        border-radius: 100px;
        color: var(--ws-text-secondary);
        font-weight: 700;
        text-decoration: none;
        transition: 0.3s;
        display: flex;
        align-items: center;
        font-size: 0.85rem;
    }
    .tab-item:hover {
        background: rgba(var(--primary-rgb), 0.05);
        color: var(--primary);
    }
    .tab-item.active {
        background: var(--primary);
        color: #ffffff;
        box-shadow: 0 8px 20px rgba(var(--primary-rgb), 0.3);
    }

    /* Empty State Lux */
    .empty-state-lux {
        background: var(--ws-bg-card);
        border: 2px dashed var(--ws-border);
        border-radius: 32px;
        padding: 80px 40px;
    }
    .empty-icon-wrapper {
        font-size: 5rem;
        color: var(--ws-border);
        opacity: 0.5;
    }

    /* Premium Bulk Action Bar */
    .bulk-action-bar-elite {
        position: fixed;
        bottom: 40px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 2000;
        pointer-events: none;
    }
    .bulk-action-bar-elite > div {
        pointer-events: auto;
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.3), inset 0 0 0 1px rgba(255,255,255,0.1);
    }
    .bg-glass-onyx {
        background: rgba(15, 17, 20, 0.85);
    }
    .rounded-24 { border-radius: 24px; }
    .shadow-premium { box-shadow: 0 15px 35px rgba(0,0,0,0.2); }
    .btn-hover-scale:hover { transform: scale(1.05); }

    /* Theme-Aware Stats Styling */
    .bg-stats-card-main { background-color: #ffffff; }
    .bg-stats-inner-item { background-color: var(--gray-50); }
    .text-stats-main { color: var(--dark); }
    .text-muted-theme { color: var(--gray-500); }
    .bg-stats-header { background-color: var(--gray-50); }
    .text-stats-title { color: var(--dark); }

    body.theme-dark .bg-stats-card-main { background-color: var(--bg-card); }
    body.theme-dark .bg-stats-inner-item { background-color: rgba(255, 255, 255, 0.03); }
    body.theme-dark .text-stats-main { color: #ffffff; }
    body.theme-dark .text-muted-theme { color: var(--gray-400); }
    body.theme-dark .bg-stats-header { background-color: rgba(255, 255, 255, 0.05); }
    body.theme-dark .text-stats-title { color: #ffffff; }

    /* Custom Elements */
    .glass-badge-theme { background: var(--bg-stats-header); border: 1px solid var(--ws-border); border-radius: 100px; color: var(--ws-text-primary); }
    
    .premium-case-card { border-radius: 24px; overflow: hidden; height: 100%; display: flex; flex-direction: column; transition: 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); box-shadow: 0 10px 30px rgba(0,0,0,0.05); border: 1px solid var(--ws-border); background: var(--ws-bg-card); }
    .premium-case-card:hover { transform: translateY(-10px); border-color: var(--primary); box-shadow: 0 20px 50px rgba(0,0,0,0.1); }

    .card-inner-top { padding: 24px; flex-grow: 1; }
    .card-meta { display: flex; justify-content: space-between; align-items: center; }
    
    .badge-premium { padding: 6px 16px; border-radius: 100px; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border: 1px solid transparent; }
    .status-pending { background: rgba(59, 130, 246, 0.1); color: #3b82f6; border-color: rgba(59, 130, 246, 0.2); }
    .status-review { background: rgba(245, 158, 11, 0.1); color: #d97706; border-color: rgba(245, 158, 11, 0.2); }
    .status-success { background: rgba(16, 185, 129, 0.1); color: #059669; border-color: rgba(16, 185, 129, 0.2); }
    .status-danger { background: rgba(239, 68, 68, 0.1); color: #dc2626; border-color: rgba(239, 68, 68, 0.2); }

    .case-type-badge { color: var(--primary); font-size: 0.8rem; font-weight: 700; background: var(--ws-bg-stats-header); padding: 5px 12px; border-radius: 8px; }

    .description-box { line-height: 1.6; }
    .italic { font-style: italic; }

    .btn-details-glow { background: var(--gray-100); color: var(--dark); border: 1px solid var(--ws-border); border-radius: 12px; transition: 0.3s; }
    body.theme-dark .btn-details-glow { background: rgba(255,255,255,0.05); color: #ffffff; }
    .btn-details-glow:hover { background: var(--primary); border-color: var(--primary); color: #ffffff; box-shadow: 0 0 20px rgba(59, 130, 246, 0.4); }

    .card-inner-bottom { padding: 16px; }
    .btn-action-card { border-radius: 12px; padding: 10px; font-weight: 700; font-size: 0.8rem; border: none; display: flex; align-items: center; justify-content: center; gap: 8px; transition: 0.3s; color: #ffffff; }
    .id-card-btn { background: #3b82f6; }
    .id-card-btn:hover { background: #2563eb; transform: scale(1.03); color: #ffffff; }
    .report-btn { background: #991b1b; }
    .report-btn:hover { background: #b91c1c; transform: scale(1.03); color: #ffffff; }
    .disabled-btn { background: var(--gray-100); color: var(--gray-400); }

    /* Checkbox Alignment (Inline in Metadata) */
    .form-check-input-elite {
        width: 24px;
        height: 24px;
        border: 2px solid var(--primary);
        border-radius: 6px;
        cursor: pointer;
        transition: 0.2s;
        background: rgba(255,255,255,0.1);
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20'%3e%3cpath fill='none' stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='3' d='m6 10 3 3 6-6'/%3e%3c/svg%3e");
    }
    .form-check-input-elite:checked {
        background-color: var(--primary);
        background-position: center;
        background-repeat: no-repeat;
        background-size: 80% 80%;
        box-shadow: 0 0 15px rgba(59, 130, 246, 0.5);
    }

    /* Modal Styling */
    .modal-glass-theme { background-color: var(--ws-bg-card) !important; }
    .message-box { line-height: 1.8; position: relative; }
    .shadow-inner { box-shadow: inset 0 2px 4px 0 rgba(0, 0, 0, 0.06); }
    
    body.theme-dark .btn-close { filter: invert(1) grayscale(100%) brightness(200%); }

    /* Animations */
    .animate-reveal-down { animation: revealDown 1s both; }
    .animate-reveal-up { animation: revealUp 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275) both; }
    .animate-up { animation: fadeInUp 0.8s both; }
    @keyframes revealDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
    @keyframes revealUp { from { opacity: 0; transform: translate(0, 30px); } to { opacity: 1; transform: translate(0, 0); } }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(40px); } to { opacity: 1; transform: translateY(0); } }

    .x-small { font-size: 0.7rem; }
</style>
@endsection
