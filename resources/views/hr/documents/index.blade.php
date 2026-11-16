@extends('layouts.app')
@section('content')
<div class="container-fluid p-0">
    {{-- Hero Section --}}
    <div class="dashboard-hero animate-slide-up" style="background: linear-gradient(135deg, #6d28d9 0%, #7c3aed 50%, #4f46e5 100%);">
        <div class="hero-content">
            <div class="hero-greeting">الموارد البشرية HR 📁</div>
            <h1 class="hero-title">ملفات وتقارير الـ HR</h1>
            <p class="hero-subtitle">أرشيف تقارير السيشنز، تقارير الفعاليات، التقييمات واللوائح التنظيمية</p>
            <div class="hero-actions d-flex flex-wrap gap-2">
                <button type="button" class="btn btn-sm btn-light text-primary fw-bold rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadHrDocModal">
                    <i class="bi bi-cloud-arrow-up-fill me-1"></i> رفع ملف / تقرير جديد
                </button>
                <a href="{{ route('hr.evaluations') }}" class="btn btn-sm rounded-pill px-4">
                    <i class="bi bi-speedometer2 me-1"></i> التقييمات والمتابعة
                </a>
            </div>
        </div>
        <i class="bi bi-folder2-open hero-icon d-none d-md-block"></i>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm">
            <i class="bi bi-check-circle me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(isset($errors) && $errors->any())
        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm">
            <ul class="mb-0 small">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Category Filter Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-4 col-xl-2">
            <a href="{{ route('hr.documents.index', array_filter(['q' => $q, 'project_id' => $projectId])) }}"
               class="card h-100 border-0 shadow-sm text-decoration-none {{ empty($category) ? 'border border-2 border-primary bg-primary-subtle' : '' }}">
                <div class="card-body p-3 text-center">
                    <div class="rounded-circle bg-primary-subtle text-primary d-inline-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px;">
                        <i class="bi bi-collection-fill fs-5"></i>
                    </div>
                    <div class="fw-bold text-dark small">كل الملفات</div>
                    <div class="h4 fw-bolder mb-0 text-primary">{{ $counts['all'] }}</div>
                </div>
            </a>
        </div>
        @foreach($categories as $catKey => $meta)
            <div class="col-6 col-md-4 col-xl-2">
                <a href="{{ route('hr.documents.index', array_filter(['category' => $catKey, 'q' => $q, 'project_id' => $projectId])) }}"
                   class="card h-100 border-0 shadow-sm text-decoration-none {{ $category === $catKey ? 'border border-2 border-' . $meta['color'] . ' bg-' . $meta['color'] . '-subtle' : '' }}">
                    <div class="card-body p-3 text-center">
                        <div class="rounded-circle bg-{{ $meta['color'] }}-subtle text-{{ $meta['color'] }} d-inline-flex align-items-center justify-content-center mb-2" style="width: 42px; height: 42px;">
                            <i class="bi {{ $meta['icon'] }} fs-5"></i>
                        </div>
                        <div class="fw-bold text-dark small">{{ $meta['label'] }}</div>
                        <div class="h4 fw-bolder mb-0 text-{{ $meta['color'] }}">{{ $counts[$catKey] ?? 0 }}</div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>

    {{-- Search & Filters --}}
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-3">
            <form method="GET" action="{{ route('hr.documents.index') }}" class="row g-2 align-items-end">
                @if($category)
                    <input type="hidden" name="category" value="{{ $category }}">
                @endif
                <div class="col-md-4">
                    <label class="form-label small text-muted mb-1">بحث في العنوان أو الملاحظات</label>
                    <input type="text" name="q" value="{{ $q }}" class="form-control" placeholder="ابحث عن تقرير سيشن أو فعالية...">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">المشروع (اختياري)</label>
                    <select name="project_id" class="form-select">
                        <option value="">كل المشاريع</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" @selected((string)$projectId === (string)$p->id)>{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">من تاريخ</label>
                    <input type="date" name="date_from" value="{{ $dateFrom }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label small text-muted mb-1">إلى تاريخ</label>
                    <input type="date" name="date_to" value="{{ $dateTo }}" class="form-control">
                </div>
                <div class="col-md-1 d-flex gap-1">
                    <button type="submit" class="btn btn-primary w-100" title="تطبيق الفلتر"><i class="bi bi-search"></i></button>
                    <a href="{{ route('hr.documents.index') }}" class="btn btn-outline-secondary" title="إعادة ضبط"><i class="bi bi-arrow-clockwise"></i></a>
                </div>
            </form>
        </div>
    </div>

    {{-- Documents Table --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center py-3 px-4">
            <h5 class="mb-0 fw-bold text-dark">
                <i class="bi bi-files text-primary me-2"></i>
                {{ $category && isset($categories[$category]) ? $categories[$category]['label'] : 'جميع ملفات وتقارير الـ HR' }}
                <span class="badge bg-primary-subtle text-primary rounded-pill ms-1">{{ $documents->total() }}</span>
            </h5>
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#uploadHrDocModal" onclick="presetHrDocCategory('sessions')">
                    <i class="bi bi-plus-lg me-1"></i> إضافة تقرير سيشن
                </button>
                <button type="button" class="btn btn-sm btn-success rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#uploadHrDocModal" onclick="presetHrDocCategory('events')">
                    <i class="bi bi-plus-lg me-1"></i> إضافة تقرير فعالية
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr class="small text-secondary">
                            <th class="ps-4">الملف / التقرير</th>
                            <th>التصنيف</th>
                            <th>تاريخ السيشن / الفعالية</th>
                            <th>المشروع</th>
                            <th>الحجم</th>
                            <th>بواسطة</th>
                            <th class="text-end pe-4">إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($documents as $doc)
                            @php
                                $meta = $categories[$doc->category] ?? $categories['other'];
                            @endphp
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-start gap-2">
                                        <div class="rounded-3 bg-{{ $meta['color'] }}-subtle text-{{ $meta['color'] }} p-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                            <i class="bi {{ $meta['icon'] }} fs-5"></i>
                                        </div>
                                        <div>
                                            <div class="fw-bold text-dark">{{ $doc->title }}</div>
                                            <div class="small text-muted">{{ $doc->original_name }}</div>
                                            @if($doc->description)
                                                <div class="small text-secondary mt-1">{{ Str::limit($doc->description, 90) }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $meta['color'] }}-subtle text-{{ $meta['color'] }} border border-{{ $meta['color'] }}-subtle rounded-pill px-3">
                                        {{ $meta['label'] }}
                                    </span>
                                </td>
                                <td>{{ $doc->report_date ? $doc->report_date->format('Y-m-d') : '—' }}</td>
                                <td>{{ $doc->project?->name ?? '—' }}</td>
                                <td class="small text-muted font-monospace">{{ $doc->formatted_size }}</td>
                                <td class="small text-muted">{{ $doc->uploader?->name ?? '—' }}</td>
                                <td class="text-end pe-4">
                                    <div class="btn-group btn-group-sm">
                                        @if($doc->is_previewable)
                                            <a href="{{ route('hr.documents.show', $doc) }}" target="_blank" class="btn btn-outline-primary" title="معاينة">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('hr.documents.show', [$doc, 'download' => 1]) }}" class="btn btn-outline-success" title="تحميل">
                                            <i class="bi bi-download"></i>
                                        </a>
                                        <button type="button" class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#editHrDocModal{{ $doc->id }}" title="تعديل">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form action="{{ route('hr.documents.destroy', $doc) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا الملف؟');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            {{-- Edit Modal --}}
                            <div class="modal fade" id="editHrDocModal{{ $doc->id }}" tabindex="-1" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <form action="{{ route('hr.documents.update', $doc) }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">تعديل ملف: {{ $doc->title }}</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label">عنوان التقرير / الملف <span class="text-danger">*</span></label>
                                                <input type="text" name="title" class="form-control" value="{{ $doc->title }}" required>
                                            </div>
                                            <div class="row g-2 mb-3">
                                                <div class="col-md-6">
                                                    <label class="form-label">التصنيف <span class="text-danger">*</span></label>
                                                    <select name="category" class="form-select" required>
                                                        @foreach($categories as $catKey => $cMeta)
                                                            <option value="{{ $catKey }}" @selected($doc->category === $catKey)>{{ $cMeta['label'] }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-6">
                                                    <label class="form-label">تاريخ السيشن / الفعالية</label>
                                                    <input type="date" name="report_date" class="form-control" value="{{ $doc->report_date ? $doc->report_date->format('Y-m-d') : '' }}">
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">المشروع المرتبط (اختياري)</label>
                                                <select name="project_id" class="form-select">
                                                    <option value="">— غير مرتبط بمشروع محدد —</option>
                                                    @foreach($projects as $p)
                                                        <option value="{{ $p->id }}" @selected($doc->project_id == $p->id)>{{ $p->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">ملاحظات / ملخص</label>
                                                <textarea name="description" class="form-control" rows="2">{{ $doc->description }}</textarea>
                                            </div>
                                            <div class="mb-0">
                                                <label class="form-label">استبدال الملف (اختياري — بحد أقصى 50MB)</label>
                                                <input type="file" name="file" class="form-control" accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.zip,.txt,.csv">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                                            <button type="submit" class="btn btn-primary px-4">حفظ التعديلات</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-folder-x display-4 d-block mb-3 opacity-50"></i>
                                    <div class="fw-bold mb-1">لا توجد ملفات أو تقارير مرفوعة في هذا القسم بعد</div>
                                    <div class="small mb-3">يمكنك رفع تقارير السيشنز وتقارير الفعاليات وملفات الـ HR بحد أقصى 50 ميجابايت للملف.</div>
                                    <button type="button" class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#uploadHrDocModal">
                                        <i class="bi bi-cloud-arrow-up me-1"></i> رفع أول ملف
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($documents->hasPages())
            <div class="card-footer bg-transparent border-0 py-3">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</div>

{{-- Upload HR Document Modal --}}
<div class="modal fade" id="uploadHrDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('hr.documents.store') }}" method="POST" enctype="multipart/form-data" class="modal-content border-0 shadow-lg rounded-4">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-cloud-arrow-up-fill text-primary me-2"></i>رفع ملف / تقرير HR جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">عنوان التقرير / الملف <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="مثال: تقرير سيشن تدريب المتطوعين - نوفمبر" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-md-6">
                        <label class="form-label">التصنيف <span class="text-danger">*</span></label>
                        <select name="category" id="uploadHrDocCategory" class="form-select" required>
                            @foreach($categories as $catKey => $cMeta)
                                <option value="{{ $catKey }}" @selected(($category ?: 'sessions') === $catKey)>{{ $cMeta['label'] }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">تاريخ السيشن / الفعالية</label>
                        <input type="date" name="report_date" class="form-control" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">المشروع المرتبط (اختياري)</label>
                    <select name="project_id" class="form-select">
                        <option value="">— عام / غير مرتبط بمشروع —</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}">{{ $p->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">ملخص / ملاحظات (اختياري)</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="عدد الحضور، أهم النقاط، التوصيات..."></textarea>
                </div>
                <div class="mb-0">
                    <label class="form-label">الملف <span class="text-danger">*</span></label>
                    <input type="file" name="file" class="form-control" required accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.jpg,.jpeg,.png,.webp,.zip,.txt,.csv">
                    <div class="form-text">الحد الأقصى لحجم الملف 50 ميجابايت (PDF, Word, Excel, PowerPoint, صور, ZIP).</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-primary px-4"><i class="bi bi-check2-circle me-1"></i> رفع وحفظ</button>
            </div>
        </form>
    </div>
</div>

<script>
    function presetHrDocCategory(cat) {
        const sel = document.getElementById('uploadHrDocCategory');
        if (sel) sel.value = cat;
    }
</script>
@endsection
