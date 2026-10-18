@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">

<div class="container-fluid py-4 min-vh-100 bg-theme-page">
    @if(session('success'))
      <div class="alert alert-success alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    @if($errors->any())
      <div class="alert alert-danger alert-dismissible fade show rounded-4 shadow-sm mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>يرجى مراجعة الملاحظات:</strong>
        <ul class="mb-0 mt-1 small">
          @foreach($errors->all() as $err) <li>{{ $err }}</li> @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    @endif

    <!-- Header -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 animate-reveal-down px-2 gap-3">
        <div>
            <div class="d-flex align-items-center gap-2 mb-1">
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-1.5 rounded-pill fw-bold fs-7">
                    مؤسسة إنسان الخيرية
                </span>
                <span class="badge bg-warning bg-opacity-25 text-warning-emphasis px-2.5 py-1 rounded-pill fw-bold fs-7">
                    المرحلة الأولى
                </span>
            </div>
            <h1 class="h2 fw-800 text-stats-main mb-1">منظومة البحث المبدئي للحالات</h1>
            <p class="text-muted-theme small mb-0">استقبال وفلترة طلبات الحالات والمساعدات (في انتظار البحث - تحت البحث - تم البحث - مرفوض)</p>
        </div>
        
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-primary rounded-pill px-4 py-2.5 fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                <i class="bi bi-plus-circle me-1"></i> تسجيل طلب بحث مبدئي جديد
            </button>

            <a href="{{ route('visits.create') }}" class="btn btn-outline-success rounded-pill px-3 py-2.5 fw-bold">
                <i class="bi bi-file-earmark-medical me-1"></i> استمارة البحث الاجتماعي
            </a>

            <!-- Bulk Action Bar -->
            <div id="bulk-action-bar" class="bulk-action-bar-elite shadow-premium d-none">
                <div class="d-flex align-items-center gap-4 bg-glass-onyx px-4 py-2 rounded-24 border border-primary animate-reveal-up">
                    <div class="selected-badge bg-primary px-3 py-1 rounded-pill">
                        <span class="text-stats-main small fw-bold"><span id="selected-count">0</span> محدد</span>
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

            <div class="glass-badge-theme px-3 py-2 border-0">
                <span class="fw-bold">إجمالي الحالات:</span> {{ $applications->total() }}
            </div>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="card border-0 shadow-xs rounded-4 p-3 mb-4 bg-stats-card-main">
        <div class="row g-3 align-items-center">
            <!-- Status Tabs -->
            <div class="col-xl-7 col-lg-12">
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('status', 'page'), [])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold {{ !request('status') ? 'btn-primary' : 'btn-outline-secondary' }}">
                        الكل ({{ \App\Models\MobileCaseApplication::count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'pending'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold {{ request('status') == 'pending' ? 'btn-warning text-dark' : 'btn-outline-warning text-warning-emphasis' }}">
                        <i class="bi bi-hourglass-split me-1"></i> في انتظار البحث ({{ \App\Models\MobileCaseApplication::where('status', 'pending')->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'under_research'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold {{ request('status') == 'under_research' ? 'btn-info text-white' : 'btn-outline-info' }}">
                        <i class="bi bi-search me-1"></i> تحت البحث ({{ \App\Models\MobileCaseApplication::whereIn('status', ['under_research', 'reviewed'])->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'approved'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold {{ request('status') == 'approved' ? 'btn-success text-white' : 'btn-outline-success' }}">
                        <i class="bi bi-check2-circle me-1"></i> تم البحث ({{ \App\Models\MobileCaseApplication::whereIn('status', ['approved', 'completed', 'accepted'])->count() }})
                    </a>
                    <a href="{{ route('mobile.case-applications.index', array_merge(request()->except('page'), ['status' => 'rejected'])) }}" 
                       class="btn btn-sm rounded-pill px-3 fw-bold {{ request('status') == 'rejected' ? 'btn-danger text-white' : 'btn-outline-danger' }}">
                        <i class="bi bi-x-circle me-1"></i> مرفوض ({{ \App\Models\MobileCaseApplication::where('status', 'rejected')->count() }})
                    </a>
                </div>
            </div>

            <!-- Search box -->
            <div class="col-xl-5 col-lg-12">
                <form method="GET" action="{{ route('mobile.case-applications.index') }}" class="d-flex gap-2">
                    @if(request('status')) <input type="hidden" name="status" value="{{ request('status') }}"> @endif
                    @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
                    <div class="input-group">
                        <input type="text" name="q" value="{{ request('q') }}" class="form-control rounded-start-pill" placeholder="ابحث بالاسم، الهاتف، الرقم القومي...">
                        <button class="btn btn-primary rounded-end-pill px-3" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                    @if(request('q') || request('type') || request('status'))
                        <a href="{{ route('mobile.case-applications.index') }}" class="btn btn-outline-secondary rounded-pill px-3 text-nowrap">إلغاء الفلتر</a>
                    @endif
                </form>
            </div>
        </div>
    </div>

    <!-- Hidden Bulk Form -->
    <form id="bulk-delete-form" action="{{ route('mobile.case-applications.bulk-destroy') }}" method="POST" class="d-none">
        @csrf
        <div id="bulk-ids-container"></div>
    </form>

    <div class="row g-4">
        @forelse($applications as $app)
        <div class="col-md-6 col-lg-4 col-xl-4 animate-up" style="animation-delay: {{ $loop->index * 0.05 }}s">
            <div class="premium-case-card bg-stats-card-main border-light-subtle position-relative d-flex flex-column h-100">
                <div class="card-inner-top flex-grow-1">
                    <div class="card-meta mb-3 d-flex justify-content-between align-items-center">
                        <!-- Status Badge -->
                        @if(in_array($app->status, ['pending', 'في انتظار البحث']))
                            <span class="badge bg-warning text-dark px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-clock-history me-1"></i> في انتظار البحث
                            </span>
                        @elseif(in_array($app->status, ['under_research', 'تحت البحث', 'reviewed']))
                            <span class="badge bg-info text-white px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-search me-1"></i> تحت البحث
                            </span>
                        @elseif(in_array($app->status, ['approved', 'completed', 'تم البحث', 'accepted']))
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-check-circle-fill me-1"></i> تم البحث
                            </span>
                        @else
                            <span class="badge bg-danger text-white px-3 py-1.5 rounded-pill fw-bold">
                                <i class="bi bi-x-circle-fill me-1"></i> مرفوض
                            </span>
                        @endif

                        <div class="d-flex align-items-center gap-2">
                            <span class="badge bg-light text-dark border x-small">
                                {{ $app->case_type_label }}
                            </span>
                            <input type="checkbox" class="case-checkbox form-check-input-elite m-0" value="{{ $app->id }}" onclick="updateBulkBar()">
                        </div>
                    </div>

                    <!-- Submitter badge if submitted on behalf of someone -->
                    @if($app->submitter_type === 'other' || !empty($app->submitter_name))
                        <div class="mb-3 px-3 py-2 rounded-3 bg-primary bg-opacity-10 border border-primary border-opacity-25 small">
                            <span class="fw-bold text-primary"><i class="bi bi-person-fill-up me-1"></i> مقدم من قِبل:</span>
                            <span class="text-dark fw-bold">{{ $app->submitter_name ?? 'فاعل خير' }}</span>
                            @if($app->submitter_relation) <span class="text-muted">({{ $app->submitter_relation }})</span> @endif
                            @if($app->submitter_phone) <div class="text-muted x-small font-monospace">{{ $app->submitter_phone }}</div> @endif
                        </div>
                    @endif
                    
                    <div class="card-user-info mb-3">
                        <h4 class="user-name text-truncate text-stats-main fw-bold mb-1" title="{{ $app->applicant_name }}">{{ $app->applicant_name }}</h4>
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="user-phone font-outfit text-primary fw-bold">{{ $app->applicant_phone }}</span>
                            @if($app->backup_phone)
                                <span class="text-muted x-small font-monospace">بديل: {{ $app->backup_phone }}</span>
                            @endif
                        </div>
                        @if($app->national_id)
                            <div class="x-small text-muted mb-2">
                                <i class="bi bi-credit-card-2-front me-1"></i> الرقم القومي: <span class="font-monospace fw-bold">{{ $app->national_id }}</span>
                            </div>
                        @endif
                        <div class="location-tag x-small text-muted-theme fw-bold">
                            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $app->governorate ?? 'غير محدد' }} - {{ $app->city ?? 'غير محدد' }}
                            @if($app->address) <div class="text-muted text-truncate mt-1">{{ $app->address }}</div> @endif
                        </div>
                    </div>

                    <div class="description-box bg-stats-inner-item border border-light-subtle rounded-3 p-3 text-muted-theme x-small italic mb-3">
                        <p class="mb-0">{{ Str::limit($app->description, 130) }}</p>
                    </div>

                    <!-- Rejection Reason Box -->
                    @if($app->status === 'rejected' && $app->rejection_reason)
                        <div class="alert alert-danger p-2.5 rounded-3 mb-3 x-small">
                            <strong class="d-block mb-1"><i class="bi bi-exclamation-octagon-fill me-1"></i> سبب الرفض:</strong>
                            {{ $app->rejection_reason }}
                        </div>
                    @endif

                    <!-- Direct Social Research Visit Link if Completed -->
                    @if(in_array($app->status, ['approved', 'completed', 'accepted']))
                        <div class="mb-3">
                            <a href="{{ route('visits.create', ['application_id' => $app->id]) }}" class="btn btn-success btn-sm w-100 fw-bold py-2 shadow-xs">
                                <i class="bi bi-file-earmark-check-fill me-1"></i> استمارة البحث الاجتماعي الميداني
                            </a>
                        </div>
                    @endif

                    <div class="mt-auto">
                        <button class="btn btn-details-glow w-100 fw-bold py-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#modal{{ $app->id }}">
                            <i class="bi bi-pencil-square me-2"></i> مراجعة وتحديث حالة البحث
                        </button>
                    </div>
                </div>

                <div class="card-inner-bottom bg-stats-inner-item border-top border-light-subtle p-2">
                    <div class="d-flex justify-content-between align-items-center x-small text-muted px-2">
                        <span><i class="bi bi-calendar3 me-1"></i> {{ $app->created_at->format('Y-m-d') }}</span>
                        <span><i class="bi bi-device-hdd me-1"></i> {{ $app->source ?? 'mobile' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Detail & Decision Modal -->
        <div class="modal fade" id="modal{{ $app->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg modal-glass-theme" style="border-radius: 24px; overflow: hidden;">
                    <div class="modal-header border-0 bg-stats-header px-4 py-3 border-bottom border-light-subtle">
                        <h5 class="modal-title fw-bold text-stats-title">
                            <i class="bi bi-clipboard2-check me-2 text-primary"></i> مراجعة وقرار البحث المبدئي
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body p-4 bg-stats-card-main">
                        <div class="row g-3 mb-4">
                            <div class="col-md-6 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">اسم صاحب الحالة (المستفيد)</label>
                                <div class="text-stats-main fw-bold fs-6">{{ $app->applicant_name }}</div>
                            </div>
                            <div class="col-md-6 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">رقم الهاتف</label>
                                <div class="font-outfit text-primary fw-bold fs-5">{{ $app->applicant_phone }}</div>
                            </div>

                            @if($app->submitter_name)
                                <div class="col-12 p-3 bg-light rounded-3 border">
                                    <div class="fw-bold text-primary mb-1"><i class="bi bi-person-heart me-1"></i> مُقدم الطلب نيابة عن الحالة:</div>
                                    <div>{{ $app->submitter_name }} ({{ $app->submitter_relation ?? 'غير محدد' }}) — هاتف: {{ $app->submitter_phone ?? '-' }}</div>
                                </div>
                            @endif

                            <div class="col-md-4 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">المشروع / نوع المساعدة</label>
                                <div class="fw-bold">{{ $app->case_type_label }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">الرقم القومي</label>
                                <div class="font-monospace fw-bold">{{ $app->national_id ?? '-' }}</div>
                            </div>
                            <div class="col-md-4 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">المحافظة والمركز</label>
                                <div class="fw-bold">{{ $app->governorate ?? '-' }} - {{ $app->city ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">العنوان التفصيلي</label>
                                <div class="text-stats-main">{{ $app->address ?? '-' }}</div>
                            </div>
                            <div class="col-12 info-group">
                                <label class="text-muted small fw-bold mb-1 d-block">وصف الحالة وظروف الأسرة</label>
                                <div class="p-3 bg-stats-inner-item border rounded-3 small">
                                    {{ $app->description }}
                                </div>
                            </div>
                        </div>

                        <!-- Decision Panel -->
                        <div class="admin-panel p-4 rounded-4 bg-stats-inner-item border">
                            <h6 class="mb-3 text-stats-main fw-bold border-start border-primary border-4 ps-3">
                                <i class="bi bi-shield-check me-2 text-primary"></i> قرار وتحديث نتيجة البحث المبدئي
                            </h6>

                            <form action="{{ route('mobile.case-applications.update', $app->id) }}" method="POST">
                                @csrf
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">حالة البحث المبدئي <span class="text-danger">*</span></label>
                                        <select name="status" id="statusSelect{{ $app->id }}" class="form-select p-2.5 fw-bold" onchange="toggleRejectionBox({{ $app->id }})">
                                            <option value="pending" {{ in_array($app->status, ['pending', 'في انتظار البحث']) ? 'selected' : '' }}>⏳ في انتظار البحث</option>
                                            <option value="under_research" {{ in_array($app->status, ['under_research', 'تحت البحث', 'reviewed']) ? 'selected' : '' }}>🔍 تحت البحث</option>
                                            <option value="approved" {{ in_array($app->status, ['approved', 'completed', 'تم البحث', 'accepted']) ? 'selected' : '' }}>✅ تم البحث (اعتماد للحث الميداني)</option>
                                            <option value="rejected" {{ in_array($app->status, ['rejected', 'مرفوض']) ? 'selected' : '' }}>❌ مرفوض</option>
                                        </select>
                                    </div>

                                    <div class="col-md-6 d-flex align-items-end">
                                        <div class="form-check pb-2">
                                            <input class="form-check-input" type="checkbox" name="start_visit" value="1" id="startVisit{{ $app->id }}">
                                            <label class="form-check-label fw-bold small text-success" for="startVisit{{ $app->id }}">
                                                التحويل فوراً إلى استمارة البحث الميداني بعد الحفظ
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Rejection Reason Input -->
                                    <div class="col-12 {{ in_array($app->status, ['rejected', 'مرفوض']) ? '' : 'd-none' }}" id="rejectionReasonBox{{ $app->id }}">
                                        <label class="form-label small fw-bold text-danger">سبب الرفض <span class="text-danger">* (إلزامي في حال الرفض)</span></label>
                                        <textarea name="rejection_reason" id="rejectionInput{{ $app->id }}" class="form-control border-danger" rows="2" placeholder="اكتب سبب رفض الطلب بالتفصيل لتسجيله رسمياً...">{{ $app->rejection_reason }}</textarea>
                                    </div>

                                    <div class="col-12">
                                        <label class="form-label small fw-bold">ملاحظات إدارية / توجيهات الباحث المبدئي</label>
                                        <textarea name="admin_notes" class="form-control" rows="2" placeholder="ملاحظات وتوجيهات تخص الحالة...">{{ $app->admin_notes }}</textarea>
                                    </div>

                                    <div class="col-12 mt-4 d-flex justify-content-between gap-3">
                                        <button type="submit" class="btn btn-primary flex-grow-1 rounded-pill fw-bold py-2.5 shadow-sm">
                                            <i class="bi bi-save me-1"></i> حفظ وتحديث نتيجة البحث
                                        </button>
                                        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold" onclick="if(confirm('هل أنت متأكد من حذف هذا الطلب نهائياً؟')) document.getElementById('del-form-{{ $app->id }}').submit()">
                                            <i class="bi bi-trash3 me-1"></i> حذف
                                        </button>
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
                    <i class="bi bi-search" style="font-size: 4.5rem; opacity: 0.25;"></i>
                </div>
                <h5 class="fw-800 text-stats-main">لا توجد طلبات بحث مبدئي مطابقة</h5>
                <p class="text-muted-theme px-4 px-md-5">لم يتم العثور على طلبات بحث مبدئي وفقاً للفلاتر المحددة.</p>
                <div class="mt-3">
                    <button type="button" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#createCaseModal">
                        <i class="bi bi-plus-circle me-1"></i> تسجيل طلب جديد الآن
                    </button>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $applications->links() }}
    </div>
</div>

<!-- Modal: تسجيل طلب بحث مبدئي جديد -->
<div class="modal fade" id="createCaseModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 24px; overflow: hidden;">
            <div class="modal-header bg-primary text-white p-4">
                <h5 class="modal-title fw-bold"><i class="bi bi-plus-circle me-2"></i> تسجيل طلب بحث مبدئي جديد</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form method="POST" action="{{ route('case-applications.store') }}">
                    @csrf

                    <!-- صفة مقدم الطلب -->
                    <div class="mb-3 p-3 rounded-3 bg-light border">
                        <label class="form-label fw-bold mb-2">جهة التقديم:</label>
                        <div class="d-flex gap-4">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="submitter_type" id="newSubSelf" value="self" checked onchange="toggleNewSubmitter()">
                                <label class="form-check-label fw-bold" for="newSubSelf">مقدم من الحالة نفسها</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="submitter_type" id="newSubOther" value="other" onchange="toggleNewSubmitter()">
                                <label class="form-check-label fw-bold" for="newSubOther">مقدم نيابة عن الحالة (فاعل خير / جار)</label>
                            </div>
                        </div>
                    </div>

                    <div id="newSubmitterFields" class="mb-3 p-3 rounded-3 border border-primary bg-primary bg-opacity-10 d-none">
                        <h6 class="fw-bold text-primary mb-2">بيانات مقدم الطلب نيابة عن الحالة</h6>
                        <div class="row g-2">
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">اسم مقدم الطلب</label>
                                <input type="text" name="submitter_name" class="form-control form-control-sm" placeholder="الاسم بالكامل">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">هاتف مقدم الطلب</label>
                                <input type="tel" name="submitter_phone" class="form-control form-control-sm" placeholder="01xxxxxxxxx">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label small fw-bold">صلته بالحالة</label>
                                <input type="text" name="submitter_relation" class="form-control form-control-sm" placeholder="جار / قريب...">
                            </div>
                        </div>
                    </div>

                    <!-- بيانات الحالة -->
                    <h6 class="fw-bold border-bottom pb-2 mb-3">بيانات المستفيد (صاحب الحالة)</h6>
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">اسم المستفيد بالكامل <span class="text-danger">*</span></label>
                            <input type="text" name="applicant_name" class="form-control" required placeholder="اسم الحالة أو ولي الأمر">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الرقم القومي (14 رقم)</label>
                            <input type="text" name="national_id" class="form-control" maxlength="14" placeholder="الرقم القومي إن وجد">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم الهاتف الأساسي <span class="text-danger">*</span></label>
                            <input type="tel" name="applicant_phone" class="form-control" required placeholder="01xxxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">رقم هاتف بديل</label>
                            <input type="tel" name="backup_phone" class="form-control" placeholder="رقم هاتف إضافي">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">نوع المساعدة</label>
                            <select name="case_type" class="form-select">
                                <option value="zad">كفالة أيتام (مشروع زاد)</option>
                                <option value="hope">مرضى أورام وعلاج (بعثاء الأمل)</option>
                                <option value="roof">سقف وترميم منازل</option>
                                <option value="financial">مساعدة مالية</option>
                                <option value="food">مساعدات عينية وغذائية</option>
                                <option value="medical">علاج وأدوية</option>
                                <option value="other">أخرى</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">المحافظة</label>
                            <input type="text" name="governorate" class="form-control" placeholder="القاهرة / الجيزة...">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label small fw-bold">المركز / المدينة</label>
                            <input type="text" name="city" class="form-control" placeholder="المدينة أو المركز">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">العنوان التفصيلي</label>
                            <input type="text" name="address" class="form-control" placeholder="القرية / الشارع / علامة مميزة">
                        </div>
                        <div class="col-12">
                            <label class="form-label small fw-bold">وصف الحالة والاحتياجات <span class="text-danger">*</span></label>
                            <textarea name="description" class="form-control" rows="3" required placeholder="شرح موجز لظروف الأسرة ومبررات الطلب..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-bold">الحالة الأولية للطلب</label>
                            <select name="status" class="form-select">
                                <option value="pending">⏳ في انتظار البحث</option>
                                <option value="under_research">🔍 تحت البحث</option>
                                <option value="approved">✅ تم البحث (تحويل للبحث الميداني)</option>
                                <option value="rejected">❌ مرفوض</option>
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check pb-2">
                                <input class="form-check-input" type="checkbox" name="start_visit" value="1" id="newStartVisit">
                                <label class="form-check-label fw-bold small text-success" for="newStartVisit">
                                    التحويل فوراً إلى استمارة البحث الميداني
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="text-end mt-4">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4 me-2" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold">
                            <i class="bi bi-check2-circle me-1"></i> حفظ وتسجيل الطلب
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function toggleRejectionBox(appId) {
    const select = document.getElementById('statusSelect' + appId);
    const box = document.getElementById('rejectionReasonBox' + appId);
    const input = document.getElementById('rejectionInput' + appId);
    if (select.value === 'rejected') {
        box.classList.remove('d-none');
        input.setAttribute('required', 'required');
    } else {
        box.classList.add('d-none');
        input.removeAttribute('required');
    }
}

function toggleNewSubmitter() {
    const isOther = document.getElementById('newSubOther').checked;
    const box = document.getElementById('newSubmitterFields');
    if (isOther) {
        box.classList.remove('d-none');
    } else {
        box.classList.add('d-none');
    }
}

function toggleAllCheckboxes(master) {
    document.querySelectorAll('.case-checkbox').forEach(cb => cb.checked = master.checked);
    updateBulkBar();
}

function updateBulkBar() {
    const checked = document.querySelectorAll('.case-checkbox:checked');
    const bar = document.getElementById('bulk-action-bar');
    const count = document.getElementById('selected-count');
    if (count) count.textContent = checked.length;
    if (bar) {
        if (checked.length > 0) bar.classList.remove('d-none');
        else bar.classList.add('d-none');
    }
}

function deselectAll() {
    document.querySelectorAll('.case-checkbox').forEach(cb => cb.checked = false);
    const master = document.getElementById('selectAllItems');
    if (master) master.checked = false;
    updateBulkBar();
}

function submitBulkDelete() {
    const checked = document.querySelectorAll('.case-checkbox:checked');
    if (!checked.length) return;
    if (!confirm('هل أنت متأكد من حذف الحالات المحددة (' + checked.length + ' حالة)؟')) return;
    const container = document.getElementById('bulk-ids-container');
    container.innerHTML = '';
    checked.forEach(cb => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'ids[]';
        input.value = cb.value;
        container.appendChild(input);
    });
    document.getElementById('bulk-delete-form').submit();
}
</script>
@endsection
