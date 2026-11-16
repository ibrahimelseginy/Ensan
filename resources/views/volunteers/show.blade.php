@extends('layouts.app')

@section('content')
    <div class="container-fluid p-0">
        {{-- Page Header --}}
        <div class="page-header mb-4">
            <h4 class="mb-0">
                <i class="bi bi-person-heart text-primary"></i>
                ملف المتطوع
            </h4>
            <div class="btn-group">
                <a href="{{ route('volunteers.edit', $volunteer) }}" class="btn btn-outline-primary">
                    <i class="bi bi-pencil me-1"></i> تعديل
                </a>
                <a href="{{ route('volunteers.index') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-right me-1"></i> رجوع
                </a>
            </div>
        </div>

        <div class="row">
            <!-- Sidebar / Profile Summary -->
            <div class="col-md-4 mb-4">
                <div class="card border-0 shadow-sm text-center h-100">
                    <div class="card-body p-4">
                        <div class="mb-4">
                            @if($volunteer->profile_photo_path)
                                <img src="{{ $volunteer->image_url }}"
                                    class="rounded-circle img-thumbnail shadow-sm"
                                    style="width: 150px; height: 150px; object-fit: cover;" alt="{{ $volunteer->name }}">
                            @else
                                <div class="bg-primary-subtle text-primary rounded-circle d-inline-flex align-items-center justify-content-center fw-bold display-4"
                                    style="width: 120px; height: 120px;">
                                    {{ strtoupper(substr($volunteer->name, 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h3 class="fw-bold mb-1">{{ $volunteer->name }}</h3>
                        <div class="mb-2">
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle font-monospace px-3 py-1 fs-6 rounded-pill fw-bold">
                                كود المتطوع: {{ $volunteer->volunteer_code }}
                            </span>
                        </div>
                        <p class="text-muted mb-3">{{ $volunteer->email }}</p>

                        <div class="mb-4">
                            @if($volunteer->active)
                                <span class="badge bg-success-subtle text-success fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-check-circle me-1"></i> نشط</span>
                            @else
                                <span class="badge bg-danger-subtle text-danger fs-6 px-3 py-2 rounded-pill"><i
                                        class="bi bi-x-circle me-1"></i> غير نشط</span>
                            @endif
                        </div>

                        <div class="d-grid gap-2">
                            <button type="button" class="btn btn-warning text-dark fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newVolunteerEvaluationModal">
                                <i class="bi bi-award-fill me-1 text-dark"></i> تقييم أداء المتطوع
                            </button>
                            <a href="{{ route('volunteers.edit', $volunteer) }}" class="btn btn-primary"><i
                                    class="bi bi-pencil-square me-2"></i> تعديل البيانات</a>
                            <a href="{{ route('volunteers.index') }}" class="btn btn-outline-secondary">رجوع للقائمة</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content / Details -->
            <div class="col-md-8">
                <!-- Personal Info -->
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-person-lines-fill me-2"></i> المعلومات الشخصية
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-4">
                                <label class="text-muted small mb-1">رقم الهاتف</label>
                                <div class="fw-medium fs-5">{{ $volunteer->phone ?? '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small mb-1">الرقم القومي</label>
                                <div class="fw-medium fs-5 font-monospace">{{ $volunteer->national_id ?: '—' }}</div>
                            </div>
                            <div class="col-md-4">
                                <label class="text-muted small mb-1">الكلية / التخصص</label>
                                <div class="fw-medium fs-5">{{ $volunteer->college ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">المحافظة</label>
                                <div class="fw-medium fs-5">{{ $volunteer->governorate ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">المدينة</label>
                                <div class="fw-medium fs-5">{{ $volunteer->city ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteering Info -->
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent py-3 border-bottom">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-briefcase me-2"></i> بيانات التطوع</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-12">
                                <label class="text-muted small mb-1">جهة التطوع الحالية</label>
                                <div class="fw-medium fs-5">
                                    @if($volunteer->project)
                                        <span class="text-dark"><i class="bi bi-kanban text-info me-2"></i> مشروع:
                                            {{ $volunteer->project->name }}</span>
                                    @elseif($volunteer->campaign)
                                        <span class="text-dark"><i class="bi bi-megaphone text-warning me-2"></i> حملة:
                                            {{ $volunteer->campaign->name }}</span>
                                    @elseif($volunteer->guestHouse)
                                        <span class="text-dark"><i class="bi bi-house text-primary me-2"></i> دار ضيافة:
                                            {{ $volunteer->guestHouse->name }}</span>
                                    @else
                                        <span class="text-muted">غير معين لجهة محددة</span>
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">الدور / المسمى التطوعي</label>
                                <div class="fw-medium fs-5">{{ $volunteer->project_role ?? '—' }}</div>
                            </div>
                            <div class="col-md-6">
                                <label class="text-muted small mb-1">ساعات التطوع الأسبوعية</label>
                                <div class="fw-medium fs-5">{{ $volunteer->volunteer_hours ?? 0 }} ساعة</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Volunteer Tasks -->
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-list-check me-2"></i> مهام المتطوعين</h5>
                        <div class="d-flex gap-2">
                            @if(request()->user()->hasPermission('volunteer_tasks.create'))
                                <a href="{{ route('volunteer-tasks.create', ['assigned_to' => $volunteer->id]) }}"
                                    class="btn btn-sm btn-primary rounded-pill"><i class="bi bi-plus-lg"></i> إضافة مهمة</a>
                            @endif
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="px-4 py-3">المهمة</th>
                                        <th class="px-4 py-3">تاريخ الاستحقاق</th>
                                        <th class="px-4 py-3">الحالة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($volunteer->assignedTasks()->latest()->take(5)->get() as $task)
                                        <tr>
                                            <td class="px-4 fw-medium">{{ $task->title }}</td>
                                            <td class="px-4">{{ $task->due_date ? $task->due_date->format('Y-m-d') : '—' }}</td>
                                            <td class="px-4">
                                                @if($task->status == 'completed' || $task->status == 'done')
                                                    <span
                                                        class="badge bg-success-subtle text-success rounded-pill px-2">منجزة</span>
                                                @elseif($task->status == 'in_progress')
                                                    <span class="badge bg-primary-subtle text-primary rounded-pill px-2">قيد
                                                        التنفيذ</span>
                                                @else
                                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2">قيد
                                                        الانتظار</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="text-center py-4 text-muted">
                                                <i class="bi bi-clipboard-x display-6 mb-2 d-block opacity-50"></i>
                                                لا توجد مهام مسندة حالياً
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Attendance History -->
                <div class="card border-0 shadow-sm mt-4">
                    <div
                        class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-primary"><i class="bi bi-calendar-check me-2"></i> سجل الحضور</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="bg-transparent">
                                    <tr>
                                        <th class="px-4 py-3">التاريخ</th>
                                        <th class="px-4 py-3">الدخول</th>
                                        <th class="px-4 py-3">الخروج</th>
                                        <th class="px-4 py-3">التقييم</th>
                                        <th class="px-4 py-3">ملاحظات</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($volunteer->volunteerAttendances()->latest()->take(5)->get() as $attendance)
                                        <tr>
                                            <td class="px-4">{{ $attendance->date->format('Y-m-d') }}</td>
                                            <td class="px-4">
                                                @if($attendance->check_in_at)
                                                    <span class="badge bg-success-subtle text-success rounded-pill fw-normal px-2">
                                                        {{ \Carbon\Carbon::parse($attendance->check_in_at)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($attendance->check_out_at)
                                                    <span
                                                        class="badge bg-secondary-subtle text-secondary rounded-pill fw-normal px-2">
                                                        {{ \Carbon\Carbon::parse($attendance->check_out_at)->format('H:i') }}
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4">
                                                @if($attendance->rating)
                                                    <span class="text-warning small" title="{{ $attendance->evaluation_notes }}">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="bi bi-star{{ $i <= $attendance->rating ? '-fill' : '' }}"></i>
                                                        @endfor
                                                    </span>
                                                @else
                                                    <span class="text-muted small">—</span>
                                                @endif
                                            </td>
                                            <td class="px-4 small text-muted">{{ Str::limit($attendance->notes ?? '—', 30) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4 text-muted">
                                                <i class="bi bi-calendar-x display-6 mb-2 d-block opacity-50"></i>
                                                لا يوجد سجلات حضور حديثة
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <!-- Volunteer Performance Evaluations (HR) -->
                <div class="card border-0 shadow-sm mt-4">
                    <div class="card-header bg-transparent py-3 border-bottom d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div class="d-flex align-items-center gap-2">
                            <div class="icon-circle bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center shadow-2xs" style="width: 36px; height: 36px;">
                                <i class="bi bi-award-fill text-warning fs-5"></i>
                            </div>
                            <div>
                                <h5 class="mb-0 fw-bold text-dark">تقييمات أداء المتطوع (HR)</h5>
                                <small class="text-muted">مصفوفة التقييم المعتمدة رسمياً (9 بنود موزونة بإجمالي 100 درجة)</small>
                            </div>
                        </div>
                        <button type="button" class="btn btn-sm btn-primary px-3 rounded-pill fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#newVolunteerEvaluationModal">
                            <i class="bi bi-plus-lg me-1"></i> تسجيل تقييم جديد
                        </button>
                    </div>
                    <div class="card-body p-0">
                        @php
                            $volunteerEvals = $volunteer->volunteerEvaluations ?? collect();
                        @endphp
                        @if($volunteerEvals->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="px-4 py-3">فترة التقييم</th>
                                            <th class="px-4 py-3">تاريخ التقييم</th>
                                            <th class="px-4 py-3 text-center">المجموع (من 100)</th>
                                            <th class="px-4 py-3 text-center">النسبة</th>
                                            <th class="px-4 py-3 text-center">التقدير</th>
                                            <th class="px-4 py-3">المُقيّم</th>
                                            <th class="px-4 py-3 text-center">إجراءات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($volunteerEvals as $ev)
                                            <tr>
                                                <td class="px-4 fw-bold text-dark">{{ $ev->evaluation_period ?: '—' }}</td>
                                                <td class="px-4 text-muted">{{ $ev->evaluation_date ? $ev->evaluation_date->format('Y-m-d') : '—' }}</td>
                                                <td class="px-4 text-center font-monospace fw-bold text-primary fs-6">{{ $ev->total_score }}</td>
                                                <td class="px-4 text-center font-monospace">{{ $ev->percentage }}%</td>
                                                <td class="px-4 text-center">
                                                    <span class="badge {{ $ev->getRatingBadgeClass() }} px-3 py-1.5 rounded-pill">{{ $ev->rating_label }}</span>
                                                </td>
                                                <td class="px-4 small text-muted">{{ $ev->evaluator?->name ?? '—' }}</td>
                                                <td class="px-4 text-center">
                                                    <form action="{{ route('volunteer-evaluations.destroy', $ev) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('هل تريد بالتأكيد حذف هذا التقييم؟');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-outline-danger btn-sm p-1" title="حذف التقييم">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="text-center py-5 text-muted">
                                <i class="bi bi-clipboard-x display-6 mb-2 d-block opacity-50 text-warning"></i>
                                <div class="fw-bold mb-1">لا توجد تقييمات أداء مسجلة لهذا المتطوع حتى الآن</div>
                                <small class="text-muted d-block mb-3">يمكنك تسجيل أول تقييم أداء الآن عبر الضغط على زر تسجيل تقييم جديد</small>
                                <button type="button" class="btn btn-sm btn-outline-primary px-3 rounded-pill fw-bold" data-bs-toggle="modal" data-bs-target="#newVolunteerEvaluationModal">
                                    <i class="bi bi-award-fill me-1"></i> تقييم المتطوع الآن
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- New Volunteer Evaluation Modal --}}
    <div class="modal fade" id="newVolunteerEvaluationModal" tabindex="-1" aria-labelledby="newVolunteerEvaluationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable my-3">
            <form action="{{ route('volunteer-evaluations.store') }}" method="POST" class="modal-content border-0 shadow-lg d-flex flex-column" style="max-height: 92vh;">
                @csrf
                <input type="hidden" name="user_id" value="{{ $volunteer->id }}">
                <div class="modal-header bg-warning text-dark py-3 flex-shrink-0">
                    <div class="d-flex align-items-center gap-2">
                        <div class="icon-circle bg-white text-dark rounded-circle d-flex align-items-center justify-content-center shadow-xs" style="width: 38px; height: 38px;">
                            <i class="bi bi-award-fill text-warning fs-5"></i>
                        </div>
                        <div>
                            <h5 class="modal-title fw-bold mb-0" id="newVolunteerEvaluationModalLabel">تسجيل تقييم أداء المتطوع: {{ $volunteer->name }}</h5>
                            <small class="text-dark opacity-75">مصفوفة تقييم المتطوعين الرسمية (9 بنود موزونة بإجمالي 100 درجة)</small>
                        </div>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3 p-md-4" style="overflow-y: auto !important; -webkit-overflow-scrolling: touch; flex: 1 1 auto;">
                    {{-- Live Score Sticky / Top Banner --}}
                    <div class="p-3 mb-3 rounded-4 border shadow-xs" style="background: linear-gradient(135deg, rgba(245, 158, 11, 0.08) 0%, rgba(16, 185, 129, 0.08) 100%); border-color: rgba(245, 158, 11, 0.25) !important;">
                        <div class="row align-items-center text-center g-2">
                            <div class="col-4 border-end">
                                <div class="x-small text-muted fw-bold">المجموع المحسوب</div>
                                <div class="h3 fw-bolder text-warning-emphasis mb-0 font-monospace" id="volLiveTotal">100.0</div>
                                <div class="x-small text-muted">من 100 درجة</div>
                            </div>
                            <div class="col-4 border-end">
                                <div class="x-small text-muted fw-bold">النسبة المئوية</div>
                                <div class="h3 fw-bold text-dark mb-0 font-monospace" id="volLivePct">100%</div>
                                <div class="x-small text-muted">النسبة الإجمالية</div>
                            </div>
                            <div class="col-4">
                                <div class="x-small text-muted fw-bold mb-1">التقدير المتوقع</div>
                                <span id="volLiveRating" class="badge bg-success fs-6 px-3 py-1.5 rounded-pill shadow-xs">متميز</span>
                            </div>
                        </div>
                        <div class="x-small text-muted mt-2 pt-2 border-top d-flex justify-content-between align-items-center flex-wrap gap-1">
                            <div>
                                <span class="badge bg-white text-dark border">الدرجة الكبرى: 5.0</span>
                                <span class="badge bg-white text-dark border ms-1">الحد الأدنى: 0.5</span>
                            </div>
                            <div class="fw-semibold">
                                90%+ متميز | 80%-89% جيد جداً | 65%-79% جيد | 50%-64% مقبول | أقل من 50% ضعيف
                            </div>
                        </div>
                    </div>

                    {{-- Metadata Row --}}
                    <div class="row g-2 mb-3 bg-light p-2 rounded-3 border">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">تاريخ التقييم <span class="text-danger">*</span></label>
                            <input type="date" name="evaluation_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1">فترة التقييم <span class="text-danger">*</span></label>
                            <input type="text" name="evaluation_period" class="form-control form-control-sm" value="{{ date('Y-m') }}" placeholder="مثال: 2026-11 أو نوفمبر 2026" required>
                        </div>
                    </div>

                    {{-- 9 Criteria Structured Table --}}
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold text-dark mb-0">
                            <i class="bi bi-ui-checks-grid me-1 text-warning"></i> بنود تقييم المتطوع التسعة كاملة (أدخل درجة من 0.5 إلى 5.0 لكل بند):
                        </h6>
                        <span class="badge bg-warning text-dark font-monospace">9 بنود = 100%</span>
                    </div>

                    <div class="table-responsive rounded-3 border mb-3">
                        <table class="table table-sm table-hover align-middle mb-0" style="font-size: 0.9rem;">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 35px;" class="text-center">#</th>
                                    <th>اسم البند والمعيار</th>
                                    <th style="width: 95px;" class="text-center">وزن البند</th>
                                    <th style="width: 170px;" class="text-center">الدرجة (0.5 - 5.0)</th>
                                    <th style="width: 100px;" class="text-center">الدرجة المحسوبة</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $critIndex = 1; @endphp
                                @foreach(\App\Models\VolunteerEvaluation::CRITERIA as $k => $c)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $critIndex++ }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $c['label'] }}</div>
                                            <div class="text-muted" style="font-size: 0.78rem;">{{ $c['desc'] }}</div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning-subtle text-dark border border-warning-subtle font-monospace px-2 py-1">
                                                {{ ($c['weight'] * 100) }}%
                                            </span>
                                        </td>
                                        <td>
                                            <div class="input-group input-group-sm">
                                                <input type="number" step="0.1" min="0.5" max="5.0" name="{{ $k }}"
                                                       class="form-control font-monospace fw-bold vol-eval-input text-center"
                                                       data-weight="{{ $c['weight'] }}" data-key="{{ $k }}" value="5.0" required>
                                                <span class="input-group-text bg-white text-muted small">/ 5.0</span>
                                            </div>
                                        </td>
                                        <td class="text-center font-monospace fw-bold text-primary">
                                            <span id="vol_score_{{ $k }}">{{ number_format($c['weight'] * 100, 1) }}</span>
                                            <span class="text-muted small fw-normal">/ {{ ($c['weight'] * 100) }}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Qualitative Inputs --}}
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1"><i class="bi bi-hand-thumbs-up text-success me-1"></i> نقاط القوة والتميز (اختياري)</label>
                            <textarea name="strengths" rows="2" class="form-control form-control-sm" placeholder="أبرز مبادرات وإيجابيات المتطوع في الميدان..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold small mb-1"><i class="bi bi-graph-up-arrow text-warning me-1"></i> مجالات التحسين والتطوير (اختياري)</label>
                            <textarea name="improvements" rows="2" class="form-control form-control-sm" placeholder="النقاط المقترحة لتطوير أداء المتطوع..."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-bold small mb-1"><i class="bi bi-chat-left-text text-secondary me-1"></i> ملاحظات المشرف وتوصيات الموارد البشرية</label>
                            <textarea name="notes" rows="2" class="form-control form-control-sm" placeholder="أي توصيات بشأن تسكين المتطوع في مشروعات أخرى أو تكريمه..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2.5 px-4 flex-shrink-0 d-flex justify-content-between align-items-center">
                    <div class="small text-muted">
                        <span class="fw-bold text-dark">المجموع الإجمالي:</span>
                        <span id="volFooterTotalScore" class="font-monospace fw-bold text-primary fs-6">100.0</span> / 100
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary px-3" data-bs-dismiss="modal">إلغاء</button>
                        <button type="submit" class="btn btn-sm btn-primary px-4 fw-bold shadow-sm">
                            <i class="bi bi-check-circle-fill me-1"></i> حفظ تقييم المتطوع واعتماده
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            function recalcVolEvaluation() {
                let weightedSum = 0;
                document.querySelectorAll('.vol-eval-input').forEach(function (input) {
                    let val = parseFloat(input.value) || 0;
                    if (val < 0) val = 0;
                    if (val > 5.0) val = 5.0;
                    let weight = parseFloat(input.dataset.weight) || 0;
                    let k = input.dataset.key;
                    let itemScore = Math.round((val * weight * 20) * 10) / 10;
                    const itemEl = document.getElementById('vol_score_' + k);
                    if (itemEl) itemEl.textContent = itemScore.toFixed(1);

                    weightedSum += (val * weight);
                });

                let totalScore = Math.round((weightedSum * 20) * 100) / 100;
                let pct = totalScore;

                let rating = 'ضعيف';
                let badgeClass = 'badge bg-danger fs-6 px-3 py-1.5 rounded-pill shadow-xs';

                if (pct >= 90) {
                    rating = 'متميز';
                    badgeClass = 'badge bg-success fs-6 px-3 py-1.5 rounded-pill shadow-xs';
                } else if (pct >= 80) {
                    rating = 'جيد جداً';
                    badgeClass = 'badge bg-primary fs-6 px-3 py-1.5 rounded-pill shadow-xs';
                } else if (pct >= 65) {
                    rating = 'جيد';
                    badgeClass = 'badge bg-info text-dark fs-6 px-3 py-1.5 rounded-pill shadow-xs';
                } else if (pct >= 50) {
                    rating = 'مقبول';
                    badgeClass = 'badge bg-warning text-dark fs-6 px-3 py-1.5 rounded-pill shadow-xs';
                }

                const totalEl = document.getElementById('volLiveTotal');
                const pctEl = document.getElementById('volLivePct');
                const ratingEl = document.getElementById('volLiveRating');
                const footerTotalEl = document.getElementById('volFooterTotalScore');

                if (totalEl) totalEl.textContent = totalScore.toFixed(1);
                if (pctEl) pctEl.textContent = pct.toFixed(1) + '%';
                if (footerTotalEl) footerTotalEl.textContent = totalScore.toFixed(1);
                if (ratingEl) {
                    ratingEl.textContent = rating;
                    ratingEl.className = badgeClass;
                }
            }

            document.querySelectorAll('.vol-eval-input').forEach(function (input) {
                input.addEventListener('input', recalcVolEvaluation);
            });
            recalcVolEvaluation();

            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('open_eval') === '1') {
                const evalModalEl = document.getElementById('newVolunteerEvaluationModal');
                if (evalModalEl && window.bootstrap && window.bootstrap.Modal) {
                    window.bootstrap.Modal.getOrCreateInstance(evalModalEl).show();
                }
            }
        });
    </script>
    @endpush
@endsection


